<?php
class VentasxMarcaModel extends CI_Model{
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
        $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];

        $cond_familia = $iIdFamilia != "0" ? 'AND ITEM.ID_Marca = ' . $iIdFamilia : "";
        $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');

        $where_gratuita = '';
        if ( $Nu_Tipo_Impuesto == 1 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
        else if ( $Nu_Tipo_Impuesto == 2 )
            $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
M.ID_Marca,
M.No_Marca,
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
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
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
VC.Ss_Descuento_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN marca AS M ON(ITEM.ID_Marca = M.ID_Marca)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento IN(2,3,4,5,6)
AND VC.ID_Moneda = " . $ID_Moneda . "
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_familia . "
" . $cond_item . "
" . $where_gratuita . "
ORDER BY
ALMA.ID_Almacen,
M.ID_Marca,
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
