CREATE TABLE agente_compra_order_steps (
    id INT NOT NULL AUTO_INCREMENT,
    id_pedido INT  unsigned NOT NULL,
    id_permision_role int not null,
    id_order int not null,
    name VARCHAR(100) NOT NULL,
    status ENUM('PENDING', 'PROGRESS', 'COMPLETED'),
    created_at datetime  default now(),
    updated_at datetime,
    PRIMARY KEY (id),
    FOREIGN KEY (id_pedido) REFERENCES agente_compra_pedido_cabecera(ID_Pedido_Cabecera)
);
CREATE PROCEDURE probussiness_v3.get_agente_compra_pedido_productos(
in p_id_producto int)
begin
	select * from agente_compra_pedido_detalle acpd where acpd.ID_Pedido_Cabecera=p_id_producto
END