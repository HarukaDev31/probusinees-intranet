<?php
class Compras_x_proveedor_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($arrParams){
        $Fe_Inicio = $arrParams['Fe_Inicio'];
        $Fe_Fin = $arrParams['Fe_Fin'];
        $ID_Tipo_Documento = $arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento = $arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento = $arrParams['ID_Numero_Documento'];
        $Nu_Estado_Documento = $arrParams['Nu_Estado_Documento'];
        $iIdProveedor=$arrParams['iIdProveedor'];
        $sNombreProveedor=$arrParams['sNombreProveedor'];
        $iIdItem=$arrParams['iIdItem'];
        $sNombreItem=$arrParams['sNombreItem'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND CC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND TD.ID_Tipo_Documento!=12';
        $cond_serie = $ID_Serie_Documento != "-" ? "AND CC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND CC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND CC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_proveedor = ( $iIdProveedor != '-' && $sNombreProveedor != '-' ) ? 'AND PROV.ID_Entidad = ' . $iIdProveedor : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND CD.ID_Producto = ' . $iIdItem : "";
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND CC.ID_Almacen = ' . $ID_Almacen : '');
        
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
PROV.ID_Entidad,
TD.No_Tipo_Documento_Breve,
CC.ID_Tipo_Documento,
CC.ID_Serie_Documento,
CC.ID_Numero_Documento,
CC.Fe_Emision,
PROV.Nu_Documento_Identidad,
PROV.No_Entidad,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
TC.Ss_Compra_Oficial AS Ss_Tipo_Cambio,
VE.Ss_Tipo_Cambio_Modificar,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
CD.Qt_Producto,
CD.Ss_Precio,
CD.Ss_SubTotal,
CD.Ss_Descuento,
CD.Ss_Impuesto,
CD.Ss_Total,
CC.Ss_Descuento AS Ss_Descuento_Header,
CC.Po_Descuento AS Po_Descuento_Header,
CD.Ss_Descuento AS Ss_Descuento,
ICDOCU.Ss_Impuesto,
CDL.Nu_Lote_Vencimiento,
CDL.Fe_Lote_Vencimiento,
CC.Nu_Estado,
CC.Ss_Percepcion
FROM
documento_cabecera AS CC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = CC.ID_Almacen)
LEFT JOIN documento_detalle AS CD ON(CC.ID_Documento_Cabecera = CD.ID_Documento_Cabecera)
LEFT JOIN documento_detalle_lote AS CDL ON(CDL.ID_Documento_Cabecera = CD.ID_Documento_Cabecera AND CDL.ID_Documento_Detalle = CD.ID_Documento_Detalle)
LEFT JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = CD.ID_Impuesto_Cruce_Documento)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = CC.ID_Tipo_Documento)
JOIN entidad AS PROV ON(PROV.ID_Entidad = CC.ID_Entidad)
LEFT JOIN producto AS PROD ON(CD.ID_Producto = PROD.ID_Producto)
LEFT JOIN moneda AS MONE ON(MONE.ID_Moneda = CC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(CC.ID_Empresa = TC.ID_Empresa AND TC.ID_Moneda = CC.ID_Moneda AND CC.Fe_Emision = TC.Fe_Ingreso)
LEFT JOIN (
SELECT
VE.ID_Documento_Cabecera,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio_Modificar
FROM
documento_cabecera AS CC
JOIN documento_enlace AS VE ON(CC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
JOIN tasa_cambio AS TC ON(TC.ID_Empresa = CC.ID_Empresa AND TC.ID_Moneda = CC.ID_Moneda AND TC.Fe_Ingreso = CC.Fe_Emision)
) AS VE ON (CC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
WHERE
CC.ID_Empresa = " . $this->user->ID_Empresa . "
AND CC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND CC.ID_Tipo_Asiento = 2
AND CC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_proveedor . "
" . $cond_item . "
ORDER BY
ALMA.ID_Almacen,
ALMA.No_Almacen,
PROV.ID_Entidad,
CC.Fe_Emision,
CC.ID_Tipo_Documento,
CC.ID_Serie_Documento,
CONVERT(CC.ID_Numero_Documento, SIGNED INTEGER) DESC;"; 
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ( $arrResponseSQL->num_rows() > 0 ){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrResponseSQL->result(),
            );
        }
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
        );
    }
}
