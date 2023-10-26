<?php
class VentasXDeliveryModel extends CI_Model{

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
      $iIdEmpleado=$arrParams['iIdEmpleado'];
      $sNombreEmpleado=$arrParams['sNombreEmpleado'];
      $iIdItem=$arrParams['iIdItem'];
      $sNombreItem=$arrParams['sNombreItem'];
      $ID_Transporte_Delivery=$arrParams['ID_Transporte_Delivery'];
      $Nu_Estado_Despacho_Pos=$arrParams['Nu_Estado_Despacho_Pos'];
      $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];

      $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
      $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
      $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
      $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
      //$cond_empleado = ( $iIdEmpleado != '-' && $sNombreEmpleado != '-' ) ? 'AND EMPLE.ID_Entidad = ' . $iIdEmpleado : "";
      $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
      $cond_delivery = $ID_Transporte_Delivery != "0" ? 'AND VC.ID_Transporte_Delivery = ' . $ID_Transporte_Delivery : "";
      $cond_estado_despacho_pos = $Nu_Estado_Despacho_Pos != "-" ? 'AND VC.Nu_Estado_Despacho_Pos = ' . $Nu_Estado_Despacho_Pos : "";
        
      $where_gratuita = '';
      if ( $Nu_Tipo_Impuesto == 1 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
      else if ( $Nu_Tipo_Impuesto == 2 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

      $query = "SELECT
DELI.ID_Entidad,
DELI.Nu_Documento_Identidad,
DELI.No_Entidad,
VC.ID_Documento_Cabecera,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
MONE.Nu_Valor_FE AS Nu_Codigo_Moneda,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
VD.Qt_Producto,
VD.Ss_Precio,
VD.Ss_SubTotal,
VD.Ss_Impuesto,
VD.Ss_Total,
IVDOCU.Ss_Impuesto AS Ss_Porcentaje_Impuesto,
VC.Nu_Estado,
VC.Nu_Estado_Despacho_Pos,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto
FROM
documento_cabecera AS VC
LEFT JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN impuesto_cruce_documento AS IVDOCU ON(IVDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
LEFT JOIN impuesto AS IMP ON(IMP.ID_Impuesto = IVDOCU.ID_Impuesto)
LEFT JOIN entidad AS DELI ON(DELI.ID_Entidad = VC.ID_Transporte_Delivery)
LEFT JOIN producto AS PROD ON(VD.ID_Producto = PROD.ID_Producto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_item . "
" . $cond_delivery . "
" . $cond_estado_despacho_pos . "
" . $where_gratuita . "
ORDER BY
DELI.ID_Entidad,
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
