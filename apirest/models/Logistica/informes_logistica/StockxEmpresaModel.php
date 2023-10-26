<?php
class StockxEmpresaModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
    public function getStockValorizado($ID_Empresa, $iTipoStock, $ID_Familia, $ID_Producto, $iIdSubFamilia){
        $cond_stock = $iTipoStock > 0 ? '' : 'AND STOCKPRO.Qt_Producto > 0';
        $cond_linea = $ID_Familia > 0 ? 'AND L.ID_Familia = ' . $ID_Familia : '';
        $cond_producto = $ID_Producto > 0 ? 'AND PRO.ID_Producto = ' . $ID_Producto : '';
        $cond_sub_familia = $iIdSubFamilia != "0" ? 'AND PRO.ID_Sub_Familia = ' . $iIdSubFamilia : "";
        
        $query = "SELECT
L.ID_Familia,
L.No_Familia,
STOCKPRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
UM.No_Unidad_Medida,
SUM(STOCKPRO.Qt_Producto) AS Qt_Producto,
PRO.Ss_Precio,
PRO.Ss_Costo,
STOCKPRO.Ss_Costo_Promedio
FROM
stock_producto AS STOCKPRO
JOIN producto AS PRO ON(STOCKPRO.ID_Producto = PRO.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN familia AS L ON(L.ID_Familia = PRO.ID_Familia)
WHERE
STOCKPRO.ID_Empresa = " . $ID_Empresa . "
" . $cond_stock . "
" . $cond_linea . "
" . $cond_producto ."
" . $cond_sub_familia . "
GROUP BY
L.ID_Familia,
L.No_Familia,
STOCKPRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
UM.No_Unidad_Medida,
PRO.Ss_Precio,
PRO.Ss_Costo
ORDER BY
L.ID_Familia,
PRO.No_Producto";
        return $this->db->query($query)->result();
    }
    
    public function getStockValorizadoxFecha($ID_Empresa, $Fe_Inicio, $Fe_Fin, $ID_Familia, $ID_Producto, $iIdSubFamilia){
        $cond_linea = $ID_Familia > 0 ? 'AND L.ID_Familia = ' . $ID_Familia : '';
        $cond_producto = $ID_Producto > 0 ? 'AND PRO.ID_Producto = ' . $ID_Producto : '';
        $cond_sub_familia = $iIdSubFamilia != "0" ? 'AND PRO.ID_Sub_Familia = ' . $iIdSubFamilia : "";
        
        $query = "(SELECT
L.ID_Familia,
L.No_Familia,
GD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
UM.No_Unidad_Medida,
PRO.Ss_Precio,
PRO.Ss_Costo,
STOCKPRO.Ss_Costo_Promedio
FROM
guia_cabecera AS GC
JOIN guia_detalle AS GD ON(GC.ID_Guia_Cabecera = GD.ID_Guia_Cabecera)
JOIN producto AS PRO ON(GD.ID_Producto = PRO.ID_Producto)
JOIN stock_producto AS STOCKPRO ON(STOCKPRO.ID_Producto = PRO.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN familia AS L ON(L.ID_Familia = PRO.ID_Familia)
WHERE
GC.ID_Empresa = " . $ID_Empresa . "
AND GC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_linea . "
" . $cond_producto . "
" . $cond_sub_familia . "
GROUP BY
GD.ID_Producto
) UNION (
SELECT
L.ID_Familia,
L.No_Familia,
CD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
UM.No_Unidad_Medida,
PRO.Ss_Precio,
PRO.Ss_Costo,
STOCKPRO.Ss_Costo_Promedio
FROM
documento_cabecera AS CC
JOIN documento_detalle AS CD ON(CC.ID_Documento_Cabecera = CD.ID_Documento_Cabecera)
JOIN producto AS PRO ON(CD.ID_Producto = PRO.ID_Producto)
JOIN stock_producto AS STOCKPRO ON(STOCKPRO.ID_Producto = PRO.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN familia AS L ON(L.ID_Familia = PRO.ID_Familia)
WHERE
CC.ID_Empresa = " . $ID_Empresa . "
AND CC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_linea . "
" . $cond_producto . "
" . $cond_sub_familia . "
GROUP BY
CD.ID_Producto)
ORDER BY
ID_Familia,
No_Producto";
        return $this->db->query($query)->result();
    }
    
    public function getStockValorizadoxProducto($ID_Empresa, $Fe_Inicio, $Fe_Fin, $ID_Producto){
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CVCAB.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $ID_Producto;
//array_debug($query);
		$row_cantidad_entrada = $this->db->query($query)->row();
        
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CVCAB.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $ID_Producto;
//array_debug($query);
		$row_cantidad_entrada_guia = $this->db->query($query)->row();
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CVCAB.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $ID_Producto;
//array_debug($query);
		$row_cantidad_salida = $this->db->query($query)->row();
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CVCAB.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $ID_Producto;
//array_debug($query);
		$row_cantidad_salida_guia = $this->db->query($query)->row();
		
        return (($row_cantidad_entrada->Qt_Producto + $row_cantidad_entrada_guia->Qt_Producto) - ($row_cantidad_salida->Qt_Producto + $row_cantidad_salida_guia->Qt_Producto));
    }
}
