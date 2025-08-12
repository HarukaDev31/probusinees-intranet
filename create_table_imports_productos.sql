-- Script para crear la tabla imports_productos
-- Esta tabla almacena información sobre las importaciones de productos desde Excel

CREATE TABLE `imports_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL COMMENT 'Nombre del archivo importado',
  `ruta_archivo` varchar(500) DEFAULT NULL COMMENT 'Ruta donde se almacena el archivo',
  `cantidad_rows` int(11) DEFAULT 0 COMMENT 'Cantidad total de filas procesadas',
  `estadisticas` text DEFAULT NULL COMMENT 'Estadísticas de la importación en formato JSON',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_nombre_archivo` (`nombre_archivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar información de importaciones de productos desde Excel';

-- Agregar columna id_import_producto a la tabla productos_importados_excel si no existe
ALTER TABLE `productos_importados_excel` 
ADD COLUMN `id_import_producto` int(11) DEFAULT NULL COMMENT 'ID de referencia a imports_productos' AFTER `idContenedor`,
ADD KEY `idx_id_import_producto` (`id_import_producto`),
ADD CONSTRAINT `fk_productos_import_producto` FOREIGN KEY (`id_import_producto`) REFERENCES `imports_productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE; 