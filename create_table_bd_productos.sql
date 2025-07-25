  -- Script para crear las tablas de productos y rubros
  -- Basado en el modelo ContenedorConsolidadoModel.php

  -- Primero crear la tabla de rubros
  CREATE TABLE `table_bd_rubros` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL COMMENT 'Nombre del rubro o categoría',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_nombre` (`nombre`),
    KEY `idx_created_at` (`created_at`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar los rubros o categorías de productos';

  -- Luego crear la tabla de productos con referencia a rubros
  CREATE TABLE `table_bd_productos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `idContenedor` int(11) NOT NULL COMMENT 'ID del contenedor al que pertenece el producto',
    `item` varchar(50) DEFAULT NULL COMMENT 'Número de item del producto',
    `nombre_comercial` varchar(255) DEFAULT NULL COMMENT 'Nombre comercial del producto',
    `foto` text DEFAULT NULL COMMENT 'URL o ruta de la foto del producto',
    `caracteristicas` text DEFAULT NULL COMMENT 'Características del producto',
    `id_rubro` int(11) DEFAULT NULL COMMENT 'ID del rubro (referencia a table_bd_rubros)',
    `tipo_producto` varchar(100) DEFAULT NULL COMMENT 'Tipo de producto',
    `precio_exw` decimal(10,2) DEFAULT NULL COMMENT 'Precio EXW del producto',
    `subpartida` varchar(50) DEFAULT NULL COMMENT 'Subpartida arancelaria',
    `link` text DEFAULT NULL COMMENT 'Link o URL del producto',
    `unidad_comercial` varchar(50) DEFAULT NULL COMMENT 'Unidad comercial del producto',
    `arancel_sunat` decimal(10,2) DEFAULT NULL COMMENT 'Arancel SUNAT',
    `arancel_tlc` decimal(10,2) DEFAULT NULL COMMENT 'Arancel TLC',
    `antidumping` decimal(10,2) DEFAULT NULL COMMENT 'Antidumping aplicable',
    `correlativo` varchar(50) DEFAULT NULL COMMENT 'Correlativo del producto',
    `etiquetado` varchar(100) DEFAULT NULL COMMENT 'Información de etiquetado',
    `doc_especial` varchar(100) DEFAULT NULL COMMENT 'Documentos especiales requeridos',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
    PRIMARY KEY (`id`),
    KEY `idx_idContenedor` (`idContenedor`),
    KEY `idx_item` (`item`),
    KEY `idx_id_rubro` (`id_rubro`),
    KEY `idx_tipo_producto` (`tipo_producto`),
    KEY `idx_subpartida` (`subpartida`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_productos_rubro` FOREIGN KEY (`id_rubro`) REFERENCES `table_bd_rubros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar información de productos importados desde Excel';

  -- Índices adicionales para optimizar consultas
  CREATE INDEX `idx_producto_contenedor_item` ON `table_bd_productos` (`idContenedor`, `item`);
  CREATE INDEX `idx_producto_rubro_tipo` ON `table_bd_productos` (`id_rubro`, `tipo_producto`);

  -- Insertar algunos rubros básicos
  INSERT INTO `table_bd_rubros` (`nombre`) VALUES 
  ('Calzados'),
  ('Textiles'),
  ('Electrónicos'),
  ('Juguetes'),
  ('Herramientas'),
  ('Muebles'),
  ('Alimentos'),
  ('Bebidas'),
  ('Cosméticos'),
  ('Automotriz'),
  ('Construcción'),
  ('Otros');

  -- Comentarios adicionales sobre la estructura
  -- Esta tabla almacena los productos importados desde archivos Excel
  -- Cada producto está asociado a un contenedor específico
  -- Los campos antidumping, etiquetado y doc_especial están relacionados con regulaciones
  -- El campo caracteristicas puede contener texto largo con múltiples características del producto
  -- La tabla table_bd_rubros permite categorizar los productos de manera organizada 