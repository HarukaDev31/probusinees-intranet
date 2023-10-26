<?php
class DetalleGuiaModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($arrParams){
        $ID_Almacen=$arrParams['ID_Almacen'];
        $ID_Almacen_Externo=$arrParams['ID_Almacen_Externo'];
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento=$arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento=$arrParams['ID_Numero_Documento'];
        $Nu_Tipo_Movimiento=$arrParams['Nu_Tipo_Movimiento'];
        $Nu_Estado_Documento=$arrParams['Nu_Estado_Documento'];
        $ID_Proveedor=$arrParams['ID_Proveedor'];
        $ID_Producto=$arrParams['ID_Producto'];
        $cond_tipo_documento = $ID_Tipo_Documento !== '0' ? 'AND GC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : '';
        $cond_serie = $ID_Serie_Documento !== '-' ? 'AND GC.ID_Serie_Documento = ' . $ID_Serie_Documento : '';
        $cond_numero = $ID_Numero_Documento !== '-' ? 'AND GC.ID_Numero_Documento = ' . $ID_Numero_Documento : '';
        $cond_tipo_movimiento = $Nu_Tipo_Movimiento !== '-' ? 'AND TMOVI.Nu_Tipo_Movimiento = ' . $Nu_Tipo_Movimiento : '';
        $cond_estado_documento = $Nu_Estado_Documento !== '0' ? 'AND GC.Nu_Estado = ' . $Nu_Estado_Documento : '';
        $cond_proveedor = $ID_Proveedor > 0 ? 'AND PROV.ID_Entidad = ' . $ID_Proveedor : '';
        $cond_producto = $ID_Producto > 0 ? 'AND PROD.ID_Producto = ' . $ID_Producto : '';
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND GC.ID_Almacen = ' . $ID_Almacen : '');
        $where_id_almacen_externo = ($ID_Almacen_Externo > 0 ? 'AND GC.ID_Almacen_Transferencia = ' . $ID_Almacen_Externo : '');
        
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
GC.ID_Guia_Cabecera,
GC.ID_Tipo_Documento,
GC.ID_Serie_Documento,
GC.ID_Numero_Documento,
GC.Fe_Emision,
PROV.ID_Entidad,
PROV.Nu_Documento_Identidad,
PROV.No_Entidad,
DC.ID_Serie_Documento AS ID_Serie_Documento_Factura,
DC.ID_Numero_Documento AS ID_Numero_Documento_Factura,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS MONE_Nu_Sunat_Codigo,
TC.Ss_Compra_Oficial AS Ss_Tipo_Cambio,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
GD.Qt_Producto,
GD.Ss_Precio,
GD.Ss_SubTotal,
GD.Ss_Impuesto,
GD.Ss_Total,
TMOVI.Nu_Tipo_Movimiento,
TMOVI.No_Tipo_Movimiento,
GC.Txt_Glosa,
GC.Nu_Estado
FROM
guia_cabecera AS GC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = GC.ID_Almacen)
LEFT JOIN guia_detalle AS GD ON(GC.ID_Guia_Cabecera = GD.ID_Guia_Cabecera)
LEFT JOIN guia_enlace AS GE ON(GE.ID_Guia_Cabecera = GC.ID_Guia_Cabecera)
LEFT JOIN documento_cabecera AS DC ON(DC.ID_Documento_Cabecera = GE.ID_Documento_Cabecera)
JOIN entidad AS PROV ON(PROV.ID_Entidad = GC.ID_Entidad)
LEFT JOIN producto AS PROD ON(GD.ID_Producto = PROD.ID_Producto)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento)
JOIN moneda AS MONE ON(MONE.ID_Moneda = GC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(GC.ID_Empresa = TC.ID_Empresa AND TC.ID_Moneda = GC.ID_Moneda AND GC.Fe_Emision = TC.Fe_Ingreso)
WHERE
GC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND GC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND GC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $where_id_almacen_externo . "
" . $cond_tipo_documento . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_tipo_movimiento . "
" . $cond_estado_documento . "
" . $cond_proveedor . "
" . $cond_producto ."
ORDER BY
ALMA.ID_Almacen,
ALMA.No_Almacen,
GC.Fe_Emision DESC,
GC.ID_Tipo_Documento DESC,
GC.ID_Serie_Documento DESC,
GC.ID_Numero_Documento DESC";
        return $this->db->query($query)->result();
    }
}
