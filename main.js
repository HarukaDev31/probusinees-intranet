const express = require('express');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
const winston = require('winston');
const multer = require('multer');
const mime = require('mime-types');
const { exec } = require('child_process');
const { v4: uuidv4 } = require('uuid');
const os = require('os');
const cors = require('cors');
const EventEmitter = require('events');

// ===== CONFIGURACIÓN PARA EVITAR MEMORY LEAKS =====
// Aumentar límite de listeners para EventEmitters
EventEmitter.defaultMaxListeners = 20;
process.setMaxListeners(20);

// Configurar Node.js para mejor manejo de memoria
process.env.NODE_OPTIONS = '--max-old-space-size=2048';

// 1. Configuración inicial
const logger = winston.createLogger({
    level: 'debug',
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.printf(({ timestamp, level, message }) => {
            return `[${timestamp}] ${level.toUpperCase()}: ${message}`;
        })
    ),
    transports: [
        new winston.transports.Console(),
        new winston.transports.File({
            filename: 'logs/app.log',
            maxsize: 5 * 1024 * 1024 // 5MB
        })
    ]
});

const app = express();

// Permitir CORS para TODOS los dominios
app.use(cors({
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization'],
}));

const port = process.env.PORT || 8083;

// Archivo para almacenar información de sesiones
const SESSION_INFO_FILE = path.join(__dirname, 'session-info.json');

// 2. Configuración de middleware
app.use(express.json());
app.use(express.static('public'));
const upload = multer({
    dest: 'uploads/',
    limits: { fileSize: 30 * 1024 * 1024 } // 30MB
});

// 3. Almacenamiento de sesiones con WeakMap para evitar memory leaks
const sessions = new Map();
const sessionTimers = new Map(); // Para gestionar timeouts
const sessionCleanupTasks = new Map(); // Para tareas de limpieza

// Cola de mensajes para procesamiento asíncrono
const messageQueue = new Map();

// ===== FUNCIONES DE LIMPIEZA DE MEMORIA =====

// Limpiar todos los listeners de un cliente
const cleanupClientListeners = (client, sessionId) => {
    try {
        if (client && client.removeAllListeners) {
            const events = ['qr', 'authenticated', 'auth_failure', 'ready', 'disconnected', 'loading_screen', 'message'];
            events.forEach(event => {
                client.removeAllListeners(event);
            });
            logger.debug(`[${sessionId}] Listeners limpiados`);
        }
    } catch (err) {
        logger.warn(`[${sessionId}] Error limpiando listeners: ${err.message}`);
    }
};

// Limpiar timeouts y intervalos de una sesión
const cleanupSessionTimers = (sessionId) => {
    try {
        const timers = sessionTimers.get(sessionId) || {};
        Object.values(timers).forEach(timer => {
            if (timer) clearTimeout(timer);
        });
        sessionTimers.delete(sessionId);
        
        const cleanupTasks = sessionCleanupTasks.get(sessionId) || [];
        cleanupTasks.forEach(task => {
            if (typeof task === 'function') task();
        });
        sessionCleanupTasks.delete(sessionId);
        
        logger.debug(`[${sessionId}] Timers y tareas de limpieza eliminados`);
    } catch (err) {
        logger.warn(`[${sessionId}] Error limpiando timers: ${err.message}`);
    }
};

// Función para procesar la cola de mensajes
const processMessageQueue = async (sessionId) => {
    const queue = messageQueue.get(sessionId);
    if (!queue || queue.length === 0) return;

    const message = queue.shift();
    const session = sessions.get(sessionId);
    
    if (!session || session.status !== 'authenticated') {
        logger.warn(`[${sessionId}] No se puede procesar mensaje, sesión no lista`);
        return;
    }

    try {
        const { numero, mensaje, archivo, callback } = message;
        const client = session.client;
        
        const numberId = await client.getNumberId(numero);
        if (!numberId) {
            logger.error(`[${sessionId}] Número no registrado: ${numero}`);
            if (callback) callback({ error: 'Número no registrado en WhatsApp' });
            return;
        }

        const chatId = numberId._serialized;

        if (archivo) {
            const fileMimeType = archivo.mimetype || mime.lookup(archivo.path) || 'application/octet-stream';
            const fileName = archivo.originalname || `file_${Date.now()}${path.extname(archivo.originalname) || '.dat'}`;
            const fileData = fs.readFileSync(archivo.path, { encoding: 'base64' });
            
            const media = new MessageMedia(fileMimeType, fileData, fileName);
            let options = { caption: mensaje || '' };

            if (fileMimeType.startsWith('video/') || fileMimeType.startsWith('audio/')) {
                options.sendMediaAsDocument = false;
            } else if (fileMimeType === 'application/pdf' || !fileMimeType.startsWith('image/')) {
                options.sendMediaAsDocument = true;
            }

            await client.sendMessage(chatId, media, options);
            
            // Limpiar archivo temporal
            try {
                fs.unlinkSync(archivo.path);
            } catch (err) {
                logger.warn(`[${sessionId}] Error eliminando archivo temporal: ${err.message}`);
            }
            
            logger.info(`[${sessionId}] Archivo enviado exitosamente a ${numero}`);
            if (callback) callback({ success: true, type: 'media' });
        } else if (mensaje) {
            await client.sendMessage(chatId, mensaje);
            logger.info(`[${sessionId}] Mensaje enviado exitosamente a ${numero}`);
            if (callback) callback({ success: true, type: 'text' });
        }
    } catch (err) {
        logger.error(`[${sessionId}] Error procesando mensaje: ${err.message}`);
        if (message.callback) message.callback({ error: err.message });
    }

    // Procesar siguiente mensaje en la cola
    if (queue.length > 0) {
        const timer = setTimeout(() => processMessageQueue(sessionId), 1000);
        // Guardar timer para limpieza posterior
        const timers = sessionTimers.get(sessionId) || {};
        timers.queueProcessor = timer;
        sessionTimers.set(sessionId, timers);
    }
};

// Función mejorada para guardar información de sesiones
const saveSessionInfo = () => {
    try {
        const sessionInfo = Array.from(sessions.entries()).map(([id, session]) => {
            let phoneNumber = null;
            let infoData = null;

            try {
                phoneNumber = session.client?.info?.wid?.user || session.phoneNumber || null;
                
                if (session.client && session.status === 'authenticated') {
                    infoData = {
                        wid: session.client.info?.wid,
                        pushname: session.client.info?.pushname,
                        platform: session.client.info?.platform
                    };
                }
            } catch (err) {
                logger.warn(`[${id}] Error obteniendo información: ${err.message}`);
            }

            return {
                sessionId: id,
                status: session.status,
                lastActivity: session.lastActivity,
                phoneNumber: phoneNumber,
                authenticated: session.status === 'authenticated',
                reconnecting: session.status === 'reconnecting',
                infoData: infoData || session.infoData,
                timestamp: Date.now()
            };
        });

        // Crear backup del archivo anterior
        if (fs.existsSync(SESSION_INFO_FILE)) {
            try {
                fs.copyFileSync(SESSION_INFO_FILE, `${SESSION_INFO_FILE}.bak`);
            } catch (err) {
                logger.warn(`Error creando backup de session-info: ${err.message}`);
            }
        }

        fs.writeFileSync(SESSION_INFO_FILE, JSON.stringify(sessionInfo, null, 2));
        logger.debug(`Información de sesiones guardada (${sessionInfo.length} sesiones)`);
    } catch (error) {
        logger.error(`Error guardando información de sesiones: ${error.message}`);
    }
};

// 4. Limpieza nuclear mejorada con gestión de memoria
const nuclearCleanup = async (sessionId) => {
    try {
        logger.debug(`[${sessionId}] Iniciando limpieza nuclear`);

        // Limpiar todos los listeners primero
        const session = sessions.get(sessionId);
        if (session && session.client) {
            cleanupClientListeners(session.client, sessionId);
            
            try {
                await session.client.destroy();
            } catch (err) {
                logger.warn(`[${sessionId}] Error al destruir cliente: ${err.message}`);
            }
        }

        // Limpiar timers y tareas
        cleanupSessionTimers(sessionId);

        // Esperar un poco para que se liberen recursos
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Eliminar directorios específicos de la sesión
        const dirsToRemove = [
            path.join(__dirname, `.wwebjs_auth/session-${sessionId}`),
            path.join(__dirname, `whatsapp-session-${sessionId}`),
            `/tmp/chrome-profile-${sessionId}`
        ];

        for (const dir of dirsToRemove) {
            if (fs.existsSync(dir)) {
                try {
                    fs.rmSync(dir, { recursive: true, force: true });
                    logger.debug(`[${sessionId}] Directorio eliminado: ${dir}`);
                } catch (err) {
                    logger.error(`[${sessionId}] Error eliminando ${dir}: ${err.message}`);
                }
            }
        }

        // Matar procesos específicos
        exec(`pkill -f "chromium.*${sessionId}"`, (err) => {
            if (!err) logger.debug(`[${sessionId}] Procesos Chromium terminados`);
        });

        // Forzar garbage collection si está disponible
        if (global.gc) {
            global.gc();
        }

    } catch (error) {
        logger.error(`[${sessionId}] Error en nuclearCleanup: ${error.message}`);
    }
};

// 5. Creación de cliente optimizada con mejor gestión de listeners
const createIsolatedClient = (sessionId, isRestore = false) => {
    const tempDir = `/tmp/chrome-profile-${sessionId}`;
    
    // Crear directorios necesarios
    [
        path.join(__dirname, `.wwebjs_auth/session-${sessionId}`),
        path.join(__dirname, `whatsapp-session-${sessionId}`),
        tempDir
    ].forEach(dir => {
        try {
            fs.mkdirSync(dir, { recursive: true });
        } catch (err) {
            logger.warn(`[${sessionId}] Error creando directorio ${dir}: ${err.message}`);
        }
    });

    // Inicializar cola de mensajes para esta sesión
    if (!messageQueue.has(sessionId)) {
        messageQueue.set(sessionId, []);
    }

    const clientOptions = {
        authStrategy: new LocalAuth({
            clientId: sessionId,
            dataPath: path.resolve(__dirname, `.wwebjs_auth`),
            restartOnAuthFail: true,
            clearAuthDataOnLogout: false
        }),
        puppeteer: {
            executablePath: '/usr/bin/chromium-browser',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--disable-web-security',
                '--disable-features=IsolateOrigins,site-per-process',
                '--disable-notifications',
                '--disable-background-timer-throttling',
                '--disable-backgrounding-occluded-windows',
                '--disable-renderer-backgrounding',
                '--disable-features=TranslateUI',
                '--disable-ipc-flooding-protection',
                '--no-first-run',
                '--no-zygote',
                '--single-process',
                '--user-data-dir=' + tempDir,
                '--memory-pressure-off', // Nuevo: deshabilitar presión de memoria
                '--max_old_space_size=512', // Nuevo: limitar memoria por proceso
                `--user-agent=Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36`
            ],
            headless: true,
            timeout: isRestore ? 120000 : 90000,
            defaultViewport: null
        },
        webVersionCache: {
            type: 'remote',
            remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html',
        },
        takeoverOnConflict: true,
        qrMaxRetries: isRestore ? 0 : 3,
        authTimeoutMs: isRestore ? 90000 : 60000
    };

    const client = new Client(clientOptions);

    // Configurar límite de listeners para este cliente específico
    client.setMaxListeners(15);

    // Timeout para detectar si se cuelga
    let timers = {};

    // ===== LISTENERS CON LIMPIEZA AUTOMÁTICA =====
    
    const qrHandler = async (qr) => {
        if (isRestore) {
            logger.warn(`[${sessionId}] QR generado durante restauración - posible fallo de autenticación`);
            sessions.set(sessionId, {
                ...sessions.get(sessionId),
                status: 'auth_failed',
                lastActivity: Date.now()
            });
            saveSessionInfo();
        } else {
            logger.info(`[${sessionId}] Nuevo QR generado`);
            try {
                const qrImage = await qrcode.toDataURL(qr, { width: 300, margin: 2 });
                sessions.set(sessionId, {
                    ...sessions.get(sessionId),
                    qrData: { qr, qrImage },
                    status: 'waiting_qr',
                    lastActivity: Date.now()
                });
                saveSessionInfo();
            } catch (err) {
                logger.error(`[${sessionId}] Error generando QR: ${err.message}`);
            }
        }

        // Timeout para QR expirado
        if (timers.qrTimeout) clearTimeout(timers.qrTimeout);
        timers.qrTimeout = setTimeout(() => {
            const session = sessions.get(sessionId);
            if (session && session.status === 'waiting_qr') {
                logger.warn(`[${sessionId}] QR expirado`);
                sessions.set(sessionId, {
                    ...session,
                    qrData: null,
                    status: 'qr_expired'
                });
                saveSessionInfo();
            }
        }, 3 * 60 * 1000); // 3 minutos
    };

    const loadingHandler = (percent, message) => {
        logger.debug(`[${sessionId}] Cargando: ${percent}% - ${message}`);
        if (timers.initTimeout) clearTimeout(timers.initTimeout);
    };

    const authenticatedHandler = async () => {
        logger.info(`[${sessionId}] Autenticado correctamente`);
        
        if (timers.qrTimeout) clearTimeout(timers.qrTimeout);
        if (timers.initTimeout) clearTimeout(timers.initTimeout);

        sessions.set(sessionId, {
            ...sessions.get(sessionId),
            status: 'authenticated',
            qrData: null,
            lastActivity: Date.now()
        });

        // Obtener información del cliente después de un breve delay
        timers.infoTimeout = setTimeout(async () => {
            try {
                const session = sessions.get(sessionId);
                if (session && session.client && session.client.info) {
                    const phoneNumber = session.client.info.wid?.user;
                    const infoData = {
                        wid: session.client.info.wid,
                        pushname: session.client.info.pushname,
                        platform: session.client.info.platform
                    };

                    sessions.set(sessionId, {
                        ...session,
                        phoneNumber,
                        infoData
                    });

                    logger.info(`[${sessionId}] Información actualizada - Teléfono: ${phoneNumber}`);
                    saveSessionInfo();
                }
            } catch (err) {
                logger.error(`[${sessionId}] Error obteniendo información: ${err.message}`);
            }
        }, 2000);

        saveSessionInfo();
    };

    const authFailureHandler = (msg) => {
        logger.error(`[${sessionId}] Error de autenticación: ${msg}`);
        
        if (timers.qrTimeout) clearTimeout(timers.qrTimeout);
        if (timers.initTimeout) clearTimeout(timers.initTimeout);

        sessions.set(sessionId, {
            ...sessions.get(sessionId),
            status: 'auth_failed',
            lastActivity: Date.now()
        });
        saveSessionInfo();
    };

    const readyHandler = () => {
        logger.info(`[${sessionId}] Sesión lista y conectada`);
        
        const session = sessions.get(sessionId);
        if (session) {
            sessions.set(sessionId, {
                ...session,
                status: 'authenticated',
                ready: true,
                qrData: null,
                lastActivity: Date.now()
            });
            saveSessionInfo();
        }
    };

    const disconnectedHandler = (reason) => {
        logger.warn(`[${sessionId}] Desconectado: ${reason}`);

        const currentSession = sessions.get(sessionId);
        if (!currentSession) return;

        if (reason === 'NAVIGATION' || reason.includes('CONFLICT') || reason === 'LOGOUT') {
            logger.warn(`[${sessionId}] Desconexión crítica: ${reason}`);
            sessions.set(sessionId, {
                ...currentSession,
                status: 'disconnected',
                lastActivity: Date.now()
            });
            saveSessionInfo();
        } else {
            // Intentar reconexión automática
            logger.info(`[${sessionId}] Intentando reconexión automática...`);
            sessions.set(sessionId, {
                ...currentSession,
                status: 'reconnecting',
                lastActivity: Date.now()
            });

            timers.reconnectTimeout = setTimeout(() => {
                const updatedSession = sessions.get(sessionId);
                if (updatedSession && updatedSession.status === 'reconnecting') {
                    try {
                        client.initialize().catch(err => {
                            logger.error(`[${sessionId}] Error en reconexión: ${err.message}`);
                            sessions.set(sessionId, {
                                ...sessions.get(sessionId),
                                status: 'failed',
                                lastActivity: Date.now()
                            });
                            saveSessionInfo();
                        });
                    } catch (err) {
                        logger.error(`[${sessionId}] Error en reconexión: ${err.message}`);
                    }
                }
            }, 5000);
        }
    };

    // Registrar listeners
    client.on('qr', qrHandler);
    client.on('loading_screen', loadingHandler);
    client.on('authenticated', authenticatedHandler);
    client.on('auth_failure', authFailureHandler);
    client.on('ready', readyHandler);
    client.on('disconnected', disconnectedHandler);

    // Timeout para detectar inicialización colgada
    timers.initTimeout = setTimeout(() => {
        const session = sessions.get(sessionId);
        if (session && session.status === 'initializing' && !session.qrData) {
            logger.error(`[${sessionId}] Timeout de inicialización`);
            sessions.set(sessionId, {
                ...session,
                status: 'timeout'
            });
            saveSessionInfo();
            
            // Intentar destruir el cliente
            try {
                client.destroy().catch(() => {});
            } catch (err) {
                // Ignorar errores
            }
        }
    }, 60000); // 1 minuto

    // Guardar timers para limpieza posterior
    sessionTimers.set(sessionId, timers);

    // Agregar tarea de limpieza para los listeners
    const cleanupTasks = sessionCleanupTasks.get(sessionId) || [];
    cleanupTasks.push(() => {
        client.removeListener('qr', qrHandler);
        client.removeListener('loading_screen', loadingHandler);
        client.removeListener('authenticated', authenticatedHandler);
        client.removeListener('auth_failure', authFailureHandler);
        client.removeListener('ready', readyHandler);
        client.removeListener('disconnected', disconnectedHandler);
    });
    sessionCleanupTasks.set(sessionId, cleanupTasks);

    return client;
};

// Monitor de salud optimizado con mejor gestión de memoria
const monitorSessionHealth = () => {
    let consecutiveFailures = 0;
    const maxFailuresBeforeRestart = 2;

    const healthCheckInterval = setInterval(async () => {
        let problemCount = 0;
        const checkPromises = [];

        for (const [id, session] of sessions.entries()) {
            if (session.status === 'authenticated' || session.status === 'reconnecting') {
                checkPromises.push(
                    session.client.getState()
                        .then(state => {
                            if (state !== 'CONNECTED' && session.status === 'authenticated') {
                                problemCount++;
                                logger.warn(`[${id}] Sesión desconectada detectada: ${state}`);
                                
                                // Intentar recuperación inmediata
                                sessions.set(id, {
                                    ...session,
                                    status: 'reconnecting'
                                });
                                
                                session.client.initialize().catch(err => {
                                    logger.error(`[${id}] Error en recuperación automática: ${err.message}`);
                                });
                            }
                        })
                        .catch(err => {
                            if (err.message.includes('Protocol error') ||
                                err.message.includes('Session closed') ||
                                err.message.includes('Failed to launch')) {
                                problemCount++;
                                logger.error(`[${id}] Error crítico detectado: ${err.message}`);
                            }
                        })
                );
            }
        }

        await Promise.allSettled(checkPromises);

        if (problemCount > 0) {
            consecutiveFailures++;
            logger.warn(`Detectados ${problemCount} problemas. Fallos consecutivos: ${consecutiveFailures}/${maxFailuresBeforeRestart}`);

            if (consecutiveFailures >= maxFailuresBeforeRestart && process.env.pm_id) {
                logger.error(`¡Demasiados fallos! Reiniciando proceso PM2...`);
                
                // Guardar estado antes de reiniciar
                saveSessionInfo();

                // Marcar todas las sesiones para restauración
                for (const [id, session] of sessions.entries()) {
                    sessions.set(id, {
                        ...session,
                        status: 'awaiting_restart'
                    });
                }
                saveSessionInfo();

                // Limpiar interval antes de reiniciar
                clearInterval(healthCheckInterval);

                // Reiniciar PM2
                setTimeout(() => {
                    exec('pm2 restart ' + process.env.pm_id, (err) => {
                        if (err) {
                            logger.error(`Error reiniciando PM2: ${err.message}`);
                            process.exit(1);
                        }
                    });
                }, 2000);
            }
        } else {
            if (consecutiveFailures > 0) {
                logger.info(`Sistema estable. Reseteando contador de fallos.`);
                consecutiveFailures = 0;
            }
        }
    }, 120000); // Cada 2 minutos

    // Guardar referencia para limpieza
    process.healthCheckInterval = healthCheckInterval;
}; 