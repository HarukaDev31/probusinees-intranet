<?php
class VentasxFamiliaModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Moneda=$arrParams['iIdMoneda'];
        $iIdFamilia=$arrParams['iIdFamilia'];
        $iIdItem=$arrParams['iIdItem'];
        $sNombreItem=$arrParams['sNombreItem'];
        $iIdSubFamilia=$arrParams['iIdSubFamilia'];
        $ID_Almacen=$arrParams['ID_Almacen'];

        $iFiltroBusquedaNombre = $arrParams['iFiltroBusquedaNombre'];
        $ID_Marca = $arrParams['ID_Marca'];
        $ID_Variante_Item = $arrParams['ID_Variante_Item'];
        $ID_Variante_Item_Detalle_1 = $arrParams['ID_Variante_Item_Detalle_1'];
        $ID_Variante_Item2 = $arrParams['ID_Variante_Item2'];
        $ID_Variante_Item_Detalle_2 = $arrParams['ID_Variante_Item_Detalle_2'];
        $ID_Variante_Item3 = $arrParams['ID_Variante_Item3'];
        $ID_Variante_Item_Detalle_3 = $arrParams['ID_Variante_Item_Detalle_3'];

        $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];

        $cond_familia = $iIdFamilia != "0" ? 'AND ITEM.ID_Familia = ' . $iIdFamilia : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' && $iFiltroBusquedaNombre == 0) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
        $cond_sub_familia = $iIdSubFamilia != "0" ? 'AND ITEM.ID_Sub_Familia = ' . $iIdSubFamilia : "";
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
        
        $where_marca = $ID_Marca != "0" ? ' AND ITEM.ID_Marca = ' . $ID_Marca : "";

        $where_like_nombre_item = (($iFiltroBusquedaNombre == 1 && !empty($sNombreItem)) ? " AND ITEM.No_Producto LIKE '" . $this->db->escape_like_str($sNombreItem) . "%' ESCAPE '!'" : "");

        $where_variante_item = $ID_Variante_Item != "0" ? ' AND ITEM.ID_Variante_Item_1  = ' . $ID_Variante_Item : "";
        $where_variante_item_detalle_1 = $ID_Variante_Item_Detalle_1 != "0" ? ' AND ITEM.ID_Variante_Item_Detalle_1  = ' . $ID_Variante_Item_Detalle_1 : "";
        $where_variante_item2 = $ID_Variante_Item2 != "0" ? ' AND ITEM.ID_Variante_Item_2  = ' . $ID_Variante_Item2 : "";
        $where_variante_item_detalle_2 = $ID_Variante_Item_Detalle_2 != "0" ? ' AND ITEM.ID_Variante_Item_Detalle_2  = ' . $ID_Variante_Item_Detalle_2 : "";
        $where_variante_item3 = $ID_Variante_Item3 != "0" ? ' AND ITEM.ID_Variante_Item_3  = ' . $ID_Variante_Item3 : "";
        $where_variante_item_detalle_3 = $ID_Variante_Item_Detalle_3 != "0" ? ' AND ITEM.ID_Variante_Item_Detalle_3  = ' . $ID_Variante_Item_Detalle_3 : "";

        $where_gratuita = '';
        if ( $Nu_Tipo_Impuesto == 1 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
        else if ( $Nu_Tipo_Impuesto == 2 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

//AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
FAMI.ID_Familia,
FAMI.No_Familia,
TD.No_Tipo_Documento_Breve,
VC.ID_Documento_Cabecera,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision_Hora,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
CLI.No_Entidad,
'' AS Ss_Tipo_Cambio,
'' AS Ss_Tipo_Cambio_Modificar,
UM.No_Unidad_Medida,
ITEM.Nu_Codigo_Barra,
ITEM.No_Producto,
VD.Qt_Producto,
VD.Ss_Precio,
VD.Ss_Subtotal,
VD.Ss_Impuesto,
VD.Ss_Total,
VC.Nu_Estado,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto,
MONE.Nu_Valor_FE AS Nu_Valor_FE_Moneda,
VC.ID_Empresa,
VC.Fe_Emision,
VD.ID_Producto,
IMP.Nu_Tipo_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
LEFT JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
JOIN familia AS FAMI ON(ITEM.ID_Familia = FAMI.ID_Familia)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.ID_Moneda = " . $ID_Moneda . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_familia . "
" . $cond_item . "
" . $cond_sub_familia . "
" . $where_like_nombre_item . "
" . $where_marca . "
" . $where_variante_item . "
" . $where_variante_item_detalle_1 . "
" . $where_variante_item2 . "
" . $where_variante_item_detalle_2 . "
" . $where_variante_item3 . "
" . $where_variante_item_detalle_3 . "
" . $where_gratuita . "
ORDER BY
ALMA.ID_Almacen,
FAMI.ID_Familia,
VC.ID_Documento_Cabecera DESC;";
        
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
