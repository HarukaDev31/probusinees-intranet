-- Agregar columna deleted_at a la tabla orders para soft delete
ALTER TABLE orders ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Crear índice para mejorar el rendimiento de las consultas
CREATE INDEX idx_orders_deleted_at ON orders(deleted_at);

-- Crear índice para mejorar el rendimiento de las consultas por fecha
CREATE INDEX idx_orders_order_date ON orders(order_date);

-- Crear índice para mejorar el rendimiento de las consultas por estado de pago
CREATE INDEX idx_orders_payment_status ON orders(payment_status);

-- Crear índice para mejorar el rendimiento de las consultas por estado
CREATE INDEX idx_orders_status ON orders(status); 

-- Agregar campo para la URL de la cotización en la tabla orders
ALTER TABLE orders ADD COLUMN cotizacion_url VARCHAR(255) NULL AFTER delivery_lead_times;
-- Recuerda ejecutar este script en tu base de datos 