CREATE PROCEDURE probussiness_v3.get_suppliers_products(IN p_id_pedido INT)
BEGIN
    SELECT 
        s.name,
        s.phone,
        s.id_supplier,
        acc.*,
        CONCAT('[', 
            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        '{"ID_Pedido_Detalle":', a2.ID_Pedido_Detalle,
                        ',"nombre_producto":"', IFNULL(a2.Txt_Producto, ''),
                        '","product_code":"', IFNULL(a2.product_code, ''),
                        '","qty_product":"', IFNULL(a2.Qt_Producto, ''),
                        '","price_product":"', IFNULL(b.Ss_Precio, ''),
                        '","total_producto":"', IFNULL(b.Ss_Precio * a2.Qt_Producto, ''),
                        '","delivery":"', IFNULL(b.Ss_Costo_Delivery, ''),
                        '","tentrega":"', IFNULL( DATE_ADD(NOW(), INTERVAL Nu_Dias_Delivery DAY),now()),
                        '","pago1":"', IFNULL(b.Ss_Precio, ''),
                        '","pago1URL":"', IFNULL(NULL, ''),
                        '","pago2":"', IFNULL(b.Ss_Precio, ''),
                        '","pago2URL":"', IFNULL(NULL, ''),
                        '","estado":"', IFNULL(b.Ss_Precio, ''),
                        '"}'
                    ) SEPARATOR ','
                )
                FROM agente_compra_pedido_detalle a2
                JOIN agente_compra_pedido_detalle_producto_proveedor b 
                    ON b.ID_Pedido_Detalle = a2.ID_Pedido_Detalle
                 
                WHERE a2.ID_Pedido_Cabecera = p_id_pedido and b.Nu_Selecciono_Proveedor=1
                AND b.ID_Entidad_Proveedor = s.id_supplier
            )
        ,']') AS detalles
    FROM suppliers s
    join agente_compra_coordination_supplier acc on acc.id_supplier=s.id_supplier and acc.id_pedido=p_id_pedido
    HAVING detalles IS NOT NULL;
END