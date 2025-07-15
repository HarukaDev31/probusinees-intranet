# Sección de Órdenes - Sistema de Administración

## Descripción
Nueva sección para gestionar órdenes de clientes con funcionalidades de listado, filtrado, visualización de detalles, descarga de Excel y eliminación suave (soft delete).

## Archivos Creados

### 1. Controlador
- **Archivo:** `apirest/controllers/Administracion/OrdersController.php`
- **Funcionalidades:**
  - Listado de órdenes con filtros
  - Visualización de detalles de orden
  - Descarga de Excel con productos de la orden
  - Eliminación suave de órdenes

### 2. Modelo
- **Archivo:** `apirest/models/Administracion/OrdersModel.php`
- **Funcionalidades:**
  - Consultas a la base de datos
  - Filtrado por fechas, estados y estado de pago
  - Soft delete implementado

### 3. Vista
- **Archivo:** `apirest/views/Administracion/OrdersView.php`
- **Características:**
  - Tabla responsive con DataTables
  - Filtros avanzados
  - Modal para detalles de orden
  - Búsqueda en tiempo real

### 4. JavaScript
- **Archivo:** `assets/js/js_orders.js`
- **Funcionalidades:**
  - Inicialización de DataTable
  - Manejo de filtros
  - Funciones para ver detalles, descargar Excel y eliminar
  - Confirmaciones con SweetAlert2

## Instalación

### 1. Base de Datos
Ejecutar el archivo `database_migration.sql` para agregar la columna `deleted_at` a la tabla `orders`:

```sql
ALTER TABLE orders ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
```

### 2. Configuración de Rutas
Agregar la ruta en tu archivo de configuración de rutas:

```php
// En tu archivo de rutas
$route['orders'] = 'OrdersController/listar';
$route['orders/getOrders'] = 'OrdersController/getOrders';
$route['orders/getOrderDetails/(:num)'] = 'OrdersController/getOrderDetails/$1';
$route['orders/downloadOrderExcel/(:num)'] = 'OrdersController/downloadOrderExcel/$1';
$route['orders/deleteOrder'] = 'OrdersController/deleteOrder';
```

### 3. Configuración de Menús
Ejecutar el archivo `menu_orders.sql` para crear los menús en la base de datos:

```sql
-- Ejecutar el script completo para crear la estructura de menús
-- El script creará:
-- - Menú padre "Órdenes"
-- - Submenús: Cotizaciones, Confirmados, Pendientes, Observados, Rechazados
```

### 4. Dependencias
Asegúrate de tener las siguientes librerías incluidas:
- DataTables
- SweetAlert2
- Font Awesome (para iconos)
- Bootstrap (para modales y estilos)

### 5. Configuración del Footer
El archivo `footer_v2.php` ya incluye la carga automática del archivo `js_orders.js` cuando se pasa la variable `js_orders = true` en la vista.

## Uso

### Acceso a la Vista
Navegar a: `tu-dominio.com/orders`

### Funcionalidades Disponibles

#### 1. Listado de Órdenes
- Tabla con paginación
- Ordenamiento por columnas
- Búsqueda en tiempo real

#### 2. Filtros
- **Fecha de inicio y fin:** Filtrar por rango de fechas
- **Estado de pago:** Pendiente, Pagado, Adelanto, Observado, Confirmado
- **Estado:** Activo, Inactivo, Cancelado

#### 3. Acciones por Orden
- **Ver detalles:** Modal con información completa del cliente y productos
- **Descargar Excel:** Archivo Excel con productos de la orden
- **Eliminar:** Soft delete (marca como eliminado sin borrar de BD)

### Estructura de Datos

#### Tabla `orders`
```sql
id | user_id | order_number | uuid | customer_full_name | customer_dni | 
customer_email | customer_phone | customer_departamento_id | customer_provincia_id | 
customer_distrito_id | customer_province | customer_city | customer_district | 
total_amount | payment_method | payment_status | payment_amount | payment_date | 
transaction_id | payment_notes | status | order_date | source | user_agent | 
timestamp | created_at | updated_at | deleted_at
```

#### Tabla `order_items`
```sql
id | order_id | product_id | product_name | unit_price | quantity | 
total_price | product_image | created_at | updated_at
```

## Personalización

### Cambiar Estados de Pago
Editar el método `getEstadoClass()` en `OrdersController.php`:

```php
private function getEstadoClass($status)
{
    switch ($status) {
        case 'PENDIENTE': return 'bg-secondary';
        case 'PAGADO': return 'bg-success';
        // Agregar más estados según necesites
    }
}
```

### Modificar Columnas de la Tabla
Editar el método `getOrders()` en `OrdersController.php` para agregar o quitar columnas.

### Cambiar Estilos
Modificar `OrdersView.php` para personalizar la apariencia de la tabla y modales.

## Notas Importantes

1. **Soft Delete:** Las órdenes eliminadas se marcan con `deleted_at` pero no se borran físicamente de la base de datos.

2. **Excel:** La descarga de Excel incluye todos los productos de la orden con sus precios y cantidades.

3. **Filtros:** Los filtros se aplican en tiempo real y se mantienen durante la sesión.

4. **Responsive:** La tabla es completamente responsive y funciona en dispositivos móviles.

## Soporte
Para problemas o mejoras, revisar los logs de error en `apirest/logs/` o contactar al equipo de desarrollo. 