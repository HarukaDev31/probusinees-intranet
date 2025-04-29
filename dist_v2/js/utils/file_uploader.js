class FileUploader {
  static instances = {};
  constructor(config) {
    // Configuración predeterminada
    this.config = {
      containerId: '', // ID del contenedor principal (también será el prefijo)
      acceptedTypes: '*/*', // Tipos de archivos aceptados (todos por defecto)
      maxSize: 5 * 1024 * 1024, // Tamaño máximo en bytes (5MB por defecto)
      placeholderText: 'Haga clic para agregar archivo',
      placeholderIcon: 'i-lucide-file', // Clase de icono para placeholder
      ...config
    };

    // Validar que se proporcionó un ID de contenedor
    if (!this.config.containerId) {
      throw new Error('Se requiere un ID de contenedor');
    }

    // El prefijo será el mismo ID del contenedor
    this.prefix = this.config.containerId;

    // Inicializar variables
    this.container = document.getElementById(this.config.containerId);
    this.file = null;

    if (!this.container) {
      throw new Error(`No se encontró el contenedor con ID: ${this.config.containerId}`);
    }

    this.cleanupPreviousInstance();

    // Registrar esta instancia
    FileUploader.instances[this.config.containerId] = this;

    // Guardar referencias a los event listeners para poder eliminarlos después
    this.eventListeners = [];

    // Generar estructura HTML
    this.generateHTML();

    // Inicializar eventos

    // Inicializar eventos
    this.initEvents();
  }

  generateHTML() {
    // Crear IDs para los elementos internos usando el ID del contenedor como prefijo
    const previewId = `${this.prefix}-preview`;
    const placeholderId = `${this.prefix}-placeholder`;
    const inputId = `${this.prefix}-input`;
    const removeId = `${this.prefix}-remove`;

    // Guardar referencias para usar más tarde
    this.ids = { previewId, placeholderId, inputId, removeId };

    // Establecer clases para el contenedor principal si no las tiene
    if (!this.container.classList.contains('relative')) {
      this.container.classList.add('relative', 'bg-gray-50', 'border-2', 'border-dashed',
        'border-gray-300', 'rounded-lg', 'mb-4', 'transition-all',
        'hover:bg-gray-100', 'hover:border-blue-300');
    }

    // Generar HTML interno
    this.container.innerHTML = `
      <div class="flex flex-col items-center justify-center p-6 h-48">
        <div id="${previewId}" class="hidden w-full h-full flex flex-col items-center justify-center"></div>
        <div id="${placeholderId}" class="text-center">
          <span class="${this.config.placeholderIcon} text-gray-400 w-12 h-12 mb-2"></span>
          <p class="text-sm text-gray-500">${this.config.placeholderText}</p>
        </div>
        <input id="${inputId}" type="file" accept="${this.config.acceptedTypes}" class="hidden" />
      </div>
      <button id="${removeId}" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hidden hover:bg-red-600 transition-colors">
        <span class="i-lucide-x w-4 h-4"></span>
      </button>
    `;

    // Guardar referencias a los elementos DOM
    this.preview = document.getElementById(previewId);
    this.placeholder = document.getElementById(placeholderId);
    this.input = document.getElementById(inputId);
    this.removeButton = document.getElementById(removeId);
  }

  
  initEvents() {
    // Eventos del contenedor principal
    const handleContainerClick = () => this.input.click();
    this.addEventListenerWithTracking(this.container, 'click', handleContainerClick);
    
    const handleDragOver = (e) => {
      e.preventDefault();
      this.container.classList.add('border-blue-500', 'bg-blue-50');
    };
    this.addEventListenerWithTracking(this.container, 'dragover', handleDragOver);
    
    const handleDragLeave = () => {
      this.container.classList.remove('border-blue-500', 'bg-blue-50');
    };
    this.addEventListenerWithTracking(this.container, 'dragleave', handleDragLeave);
    
    const handleDrop = (e) => {
      e.preventDefault();
      this.container.classList.remove('border-blue-500', 'bg-blue-50');
      
      if (e.dataTransfer.files.length) {
        this.handleFile(e.dataTransfer.files[0]);
      }
    };
    this.addEventListenerWithTracking(this.container, 'drop', handleDrop);

    // Evento del input de archivo
    const handleInputChange = () => {
      if (this.input.files.length) {
        this.handleFile(this.input.files[0]);
      }
    };
    this.addEventListenerWithTracking(this.input, 'change', handleInputChange);

    // Evento del botón de eliminar
    const handleRemoveClick = (e) => {
      e.stopPropagation();
      this.clearFile();
    };
    this.addEventListenerWithTracking(this.removeButton, 'click', handleRemoveClick);
  }

  handleFile(file) {
    // Validar tipo de archivo
    const fileType = file.type;
    const isValidType = this.validateFileType(fileType);

    if (!isValidType) {
      alert(`Tipo de archivo no permitido. Por favor, use: ${this.config.acceptedTypes}`);
      return;
    }

    // Validar tamaño de archivo
    if (file.size > this.config.maxSize) {
      const maxSizeMB = this.config.maxSize / (1024 * 1024);
      alert(`El archivo es demasiado grande. Tamaño máximo: ${maxSizeMB}MB`);
      return;
    }

    // Guardar referencia al archivo
    this.file = file;

    // Mostrar vista previa
    this.showPreview();

    // Disparar evento personalizado
    this.dispatchChangeEvent();
  }

  validateFileType(fileType) {
    // Si se acepta cualquier tipo
    if (this.config.acceptedTypes === '*/*') return true;

    const acceptedTypes = this.config.acceptedTypes.split(',');

    for (const type of acceptedTypes) {
      // Manejar comodines (image/*)
      if (type.includes('*')) {
        const mainType = type.split('/')[0];
        if (fileType.startsWith(`${mainType}/`)) {
          return true;
        }
      } else if (fileType === type.trim()) {
        return true;
      }
    }

    return false;
  }

  showPreview() {
    // Limpiar cualquier vista previa anterior
    this.preview.innerHTML = '';

    // Mostrar vista previa según el tipo de archivo
    if (this.file.type.startsWith('image/')) {
      // Vista previa para imágenes
      this.showImagePreview();
    } else if (this.file.type.startsWith('video/')) {
      // Vista previa para videos
      this.showVideoPreview();
    } else if (this.file.type.startsWith('audio/')) {
      // Vista previa para audio
      this.showAudioPreview();
    } else if (this.file.type === 'application/pdf') {
      // Vista previa para PDF (icono)
      this.showDocumentPreview('i-lucide-file-text', 'PDF');
    } else if (this.file.type.includes('spreadsheet') || this.file.type.includes('excel')) {
      // Vista previa para hojas de cálculo
      this.showDocumentPreview('i-lucide-table', 'Excel');
    } else if (this.file.type.includes('document') || this.file.type.includes('word')) {
      // Vista previa para documentos
      this.showDocumentPreview('i-lucide-file-text', 'Word');
    } else if (this.file.type.includes('presentation') || this.file.type.includes('powerpoint')) {
      // Vista previa para presentaciones
      this.showDocumentPreview('i-lucide-monitor', 'PowerPoint');
    } else if (this.file.type.includes('zip') || this.file.type.includes('compressed') || this.file.type.includes('archive')) {
      // Vista previa para archivos comprimidos
      this.showDocumentPreview('i-lucide-archive', 'Archivo comprimido');
    } else {
      // Vista previa genérica para otros tipos de archivo
      this.showDocumentPreview('i-lucide-file', '');
    }

    // Mostrar el área de vista previa y ocultar el placeholder
    this.preview.classList.remove('hidden');
    this.placeholder.classList.add('hidden');
    this.removeButton.classList.remove('hidden');
  }

  showImagePreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.preview.innerHTML = `
        <div class="relative w-full h-full flex items-center justify-center">
          <img src="${e.target.result}" alt="${this.file.name}" class="max-w-full max-h-full object-contain" />
          <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
            ${this.file.name}
          </div>
        </div>
      `;
    };
    reader.readAsDataURL(this.file);
  }

  showVideoPreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.preview.innerHTML = `
        <div class="relative w-full h-full flex items-center justify-center">
          <video src="${e.target.result}" controls class="max-w-full max-h-full"></video>
          <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
            ${this.file.name}
          </div>
        </div>
      `;
    };
    reader.readAsDataURL(this.file);
  }

  showAudioPreview() {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center w-full h-full">
          <span class="i-lucide-music text-gray-500 w-16 h-16 mb-2"></span>
          <audio src="${e.target.result}" controls class="w-full max-w-xs"></audio>
          <p class="text-sm text-gray-700 mt-2 truncate max-w-xs">${this.file.name}</p>
        </div>
      `;
    };
    reader.readAsDataURL(this.file);
  }

  showDocumentPreview(iconClass, fileType) {
    // Formatear el tamaño del archivo
    const sizeInKB = this.file.size / 1024;
    let formattedSize;

    if (sizeInKB < 1024) {
      formattedSize = `${sizeInKB.toFixed(1)} KB`;
    } else {
      formattedSize = `${(sizeInKB / 1024).toFixed(1)} MB`;
    }

    // Mostrar vista previa con icono
    this.preview.innerHTML = `
      <div class="flex flex-col items-center justify-center h-full">
        <span class="${iconClass} text-gray-500 w-16 h-16 mb-2"></span>
        <p class="text-sm font-medium text-gray-800 truncate max-w-xs">${this.file.name}</p>
        <div class="flex items-center justify-center mt-1">
          ${fileType ? `<span class="text-xs text-gray-500 mr-2">${fileType}</span>` : ''}
          <span class="text-xs text-gray-500">${formattedSize}</span>
        </div>
      </div>
    `;
  }

  clearFile() {
    // Reiniciar el input
    this.input.value = '';

    // Limpiar vista previa
    this.preview.innerHTML = '';
    this.preview.classList.add('hidden');
    this.placeholder.classList.remove('hidden');
    this.removeButton.classList.add('hidden');

    // Eliminar referencia al archivo
    this.file = null;

    // Disparar evento personalizado
    this.dispatchChangeEvent();
  }
  async loadFromURL(url) {
    try {
      // Mostrar un estado de carga
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
          <span class="i-lucide-loader text-blue-500 w-12 h-12 animate-spin mb-2"></span>
          <p class="text-sm text-gray-500">Cargando imagen...</p>
        </div>
      `;
      this.preview.classList.remove('hidden');
      this.placeholder.classList.add('hidden');

      // Obtener la imagen de la URL
      const response = await fetch(url);

      if (!response.ok) {
        throw new Error(`Error al cargar la imagen: ${response.status} ${response.statusText}`);
      }

      // Obtener el blob de la respuesta
      const blob = await response.blob();

      // Extraer el nombre de archivo de la URL
      const urlParts = url.split('/');
      const fileName = urlParts[urlParts.length - 1].split('?')[0] || 'imagen';

      // Crear un objeto File a partir del blob
      const file = new File([blob], fileName, { type: blob.type });

      // Procesar el archivo como si se hubiera subido normalmente
      this.handleFile(file);

    } catch (error) {
      console.error('Error al cargar la imagen desde URL:', error);

      // Mostrar mensaje de error
      this.preview.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
          <span class="i-lucide-alert-circle text-red-500 w-12 h-12 mb-2"></span>
          <p class="text-sm text-red-500">Error al cargar la imagen</p>
          <p class="text-xs text-gray-500 mt-1">${error.message}</p>
        </div>
      `;
      this.preview.classList.remove('hidden');
      this.placeholder.classList.add('hidden');
      this.removeButton.classList.remove('hidden');
    }
  }
  cleanupPreviousInstance() {
    const previousInstance = FileUploader.instances[this.config.containerId];
    if (previousInstance) {
      console.log(`Limpiando instancia anterior de FileUploader con ID: ${this.config.containerId}`);
      previousInstance.destroy();
    }
  }

  destroy() {
    // Eliminar todos los event listeners registrados
    this.eventListeners.forEach(({ element, type, handler }) => {
      element.removeEventListener(type, handler);
    });
    
    // Limpiar la referencia en el almacenamiento estático
    delete FileUploader.instances[this.config.containerId];
    
    console.log(`FileUploader con ID: ${this.config.containerId} destruido correctamente`);
  }

  // Método auxiliar para agregar event listeners y rastrearlos
  addEventListenerWithTracking(element, type, handler) {
    element.addEventListener(type, handler);
    this.eventListeners.push({ element, type, handler });
  }
  dispatchChangeEvent() {
    // Crear evento personalizado
    const event = new CustomEvent(`${this.prefix}-change`, {
      detail: { file: this.file }
    });

    // Disparar evento en el contenedor principal
    this.container.dispatchEvent(event);
  }

  // Método público para obtener el archivo actual
  getFile() {
    return this.file;
  }

  // Método público para establecer un archivo programáticamente
  setFile(file) {
    if (file) {
      this.handleFile(file);
    } else {
      this.clearFile();
    }
  }

}