# API de Autenticación - ProBusiness Intranet

## Base URL
```
http://localhost/probusinees-intranet/apirest/
```

## Endpoints

### 1. Login
**POST** `/api/auth/login`

Autentica un usuario y devuelve información de sesión.

#### Request Body (JSON)
```json
{
  "No_Usuario": "admin",
  "No_Password": "password123"
}
```

#### Parámetros opcionales
- `ID_Empresa`: ID de la empresa (si el usuario pertenece a múltiples empresas)
- `ID_Organizacion`: ID de la organización (si el usuario pertenece a múltiples organizaciones)

#### Response (200 - Success)
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "ID_Usuario": 1,
      "No_Usuario": "admin",
      "No_Nombres_Apellidos": "Administrador",
      "Txt_Email": "admin@example.com",
      "ID_Grupo": 1,
      "No_Grupo": "ADMIN",
      "No_Grupo_Descripcion": "Administrador",
      "ID_Empresa": 1,
      "ID_Organizacion": 1
    },
    "almacen": {
      "ID_Almacen": 1,
      "No_Almacen": "Almacén Principal"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "empresa": {
      "ID_Empresa": 1,
      "No_Empresa": "Mi Empresa",
      "ID_Pais": 1,
      "No_Pais": "Perú"
    }
  }
}
```

#### Response (401 - Authentication Failed)
```json
{
  "success": false,
  "message": "Contraseña incorrecta",
  "error": "AUTHENTICATION_FAILED"
}
```

#### Response (400 - Bad Request)
```json
{
  "success": false,
  "message": "Datos incompletos. Se requiere No_Usuario y No_Password",
  "error": "MISSING_REQUIRED_FIELDS"
}
```

### 2. Logout
**POST** `/api/auth/logout`

Cierra la sesión del usuario actual.

#### Response (200 - Success)
```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

### 3. Get Current User
**GET** `/api/auth/me`

Obtiene información del usuario actualmente autenticado.

#### Response (200 - Success)
```json
{
  "success": true,
  "data": {
    "user": {
      "ID_Usuario": 1,
      "No_Usuario": "admin",
      "No_Nombres_Apellidos": "Administrador",
      "Txt_Email": "admin@example.com",
      "ID_Grupo": 1,
      "No_Grupo": "ADMIN",
      "No_Grupo_Descripcion": "Administrador",
      "ID_Empresa": 1,
      "ID_Organizacion": 1
    },
    "almacen": {
      "ID_Almacen": 1,
      "No_Almacen": "Almacén Principal"
    },
    "empresa": {
      "ID_Empresa": 1,
      "No_Empresa": "Mi Empresa",
      "ID_Pais": 1,
      "No_Pais": "Perú"
    }
  }
}
```

#### Response (401 - No Active Session)
```json
{
  "success": false,
  "message": "No hay sesión activa",
  "error": "NO_ACTIVE_SESSION"
}
```

## Códigos de Error

| Código | Descripción |
|--------|-------------|
| `MISSING_REQUIRED_FIELDS` | Faltan campos requeridos |
| `EMPTY_FIELDS` | Los campos están vacíos |
| `AUTHENTICATION_FAILED` | Fallo en la autenticación |
| `NO_ACTIVE_SESSION` | No hay sesión activa |
| `INTERNAL_SERVER_ERROR` | Error interno del servidor |

## Ejemplos de Uso

### JavaScript (Fetch API)
```javascript
// Login
const loginData = {
  "No_Usuario": "admin",
  "No_Password": "password123"
};

fetch('http://localhost/probusinees-intranet/apirest/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(loginData)
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Login exitoso:', data.data);
    // Guardar token si es necesario
    localStorage.setItem('token', data.data.token);
  } else {
    console.error('Error de login:', data.message);
  }
})
.catch(error => {
  console.error('Error:', error);
});

// Obtener usuario actual
fetch('http://localhost/probusinees-intranet/apirest/api/auth/me', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Usuario actual:', data.data.user);
  }
});

// Logout
fetch('http://localhost/probusinees-intranet/apirest/api/auth/logout', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    localStorage.removeItem('token');
    console.log('Logout exitoso');
  }
});
```

### cURL
```bash
# Login
curl -X POST http://localhost/probusinees-intranet/apirest/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"No_Usuario": "admin", "No_Password": "password123"}'

# Obtener usuario actual
curl -X GET http://localhost/probusinees-intranet/apirest/api/auth/me \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Logout
curl -X POST http://localhost/probusinees-intranet/apirest/api/auth/logout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Notas Importantes

1. **CORS**: La API está configurada para permitir peticiones desde cualquier origen (`Access-Control-Allow-Origin: *`).

2. **Sesiones**: La API utiliza sesiones de PHP para mantener el estado de autenticación.

3. **Tokens JWT**: Se genera un token JWT opcional que puede ser usado para autenticación adicional.

4. **Base de Datos**: La API utiliza la base de datos `LAE_SYSTEMS` configurada en el sistema.

5. **Validación**: Todos los datos de entrada son limpiados y validados antes de ser procesados.

6. **Logging**: Los errores son registrados en los logs del sistema para debugging. 