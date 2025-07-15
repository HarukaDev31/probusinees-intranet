# Sistema de Cotizaciones para Órdenes

## Cambios Implementados

### 1. Base de Datos
- **Campo agregado**: `cotizacion_url` en la tabla `orders`
- **Tipo**: VARCHAR(255) NULL
- **Ubicación**: Después del campo `delivery_lead_times`

### 2. Backend (OrdersController.php)

#### Nuevos Métodos:
- **`uploadCotizacion()`**: Sube archivos de cotización usando FileTrait
- **`cotizacion($orderId)`**: Muestra la vista de cotización por ruta

#### Modificaciones:
- **FileTrait**: Agregado para manejo de archivos
- **`getOrders()`**: Modificado para mostrar botón de cotización solo si existe archivo
- **Validación**: El botón "Cotización" solo aparece si `cotizacion_url` no está vacío
- **Completado de datos**: Si faltan datos en la orden, se obtienen de `catalogo_producto`

### 3. Frontend

#### OrdersView.php:
- **Modal de subida**: Agregado modal para subir archivos de cotización
- **Botones dinámicos**: 
  - Si no hay cotización: "Subir Cotización" (amarillo)
  - Si hay cotización: "Cotización" (verde) + botón de vista
- **Botón "Ver"**: Ahora es un enlace a `/orders/detalle/{id}`

#### OrderDetailView.php (NUEVO):
- **Vista de detalle**: Accesible por ruta `/orders/detalle/{id}`
- **Información completa**: Cliente, orden y todos los productos
- **Botones de cotización**: Solo aparecen si existe archivo subido
- **Funcionalidades**:
  - Botón "Regresar"
  - Subir cotización (si no existe)
  - Descargar/Ver cotización (si existe)

#### CotizacionView.php (NUEVO):
- **Vista dedicada**: Accesible por ruta `/orders/cotizacion/{id}`
- **Campos mostrados** (según imagen):
  - Imagen
  - Nombre
  - Cantidad
  - Precio
  - Link tienda
  - Link Alibaba
  - Delivery Lead Times
- **Funcionalidades**:
  - Botón "Regresar"
  - Subir nueva cotización
  - Descargar Excel

#### js_orders.js:
- **`showUploadModal()`**: Muestra modal de subida
- **`uploadCotizacion()`**: Maneja la subida de archivos
- **`viewCotizacion()`**: Abre la vista de cotización en nueva pestaña

### 4. Estructura de Archivos

```
assets/
├── cotizaciones/          # Carpeta para archivos subidos
└── js/
    └── js_orders.js       # JavaScript actualizado

apirest/
├── controllers/
│   └── OrdersController.php    # Controlador actualizado
├── models/
│   └── Administracion/
│       └── OrdersModel.php     # Modelo con getCatalogoProducto()
└── views/
    └── Administracion/
        ├── OrdersView.php      # Vista principal actualizada
        └── CotizacionView.php  # Nueva vista de cotización
```

### 5. Flujo de Trabajo

1. **Usuario ve lista de órdenes**
2. **Botón "Ver"**: Lleva a `/orders/detalle/{id}` (vista completa)
3. **Si no hay cotización**: Ve botón "Subir Cotización" (amarillo)
4. **Al hacer clic**: Se abre modal para subir archivo
5. **Archivo se sube**: Se guarda en `assets/cotizaciones/` y URL en BD
6. **Tabla se actualiza**: Ahora muestra botón "Cotización" (verde) y botón de vista
7. **En vista de detalle**: Si hay cotización, muestra botones de descarga y vista
8. **Vista de cotización**: `/orders/cotizacion/{id}` muestra solo campos específicos

### 6. Formatos de Archivo Permitidos
- Excel (.xlsx, .xls)
- PDF (.pdf)
- Word (.doc, .docx)

### 7. Rutas
- **Lista de órdenes**: `/orders/listar`
- **Detalle de orden**: `/orders/detalle/{id}`
- **Subir cotización**: `/orders/uploadCotizacion` (POST)
- **Ver cotización**: `/orders/cotizacion/{id}`
- **Descargar Excel**: `/orders/downloadOrderExcel/{id}`

### 8. Notas Importantes
- Los archivos se guardan con timestamp para evitar conflictos
- Si faltan datos en la orden, se completan desde `catalogo_producto`
- El botón de cotización solo aparece si existe el archivo
- La vista de cotización es accesible por ruta, no por modal 