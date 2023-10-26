<?php
class Ventas_x_trabajador_model extends CI_Model{

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
      $ID_Almacen=$arrParams['ID_Almacen'];

      $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];
      $Nu_Tipo_Venta=$arrParams['Nu_Tipo_Venta'];

      $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
      $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
      $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
      $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
      $cond_empleado = ( $iIdEmpleado != '-' && $sNombreEmpleado != '-' ) ? 'AND EMPLE.ID_Entidad = ' . $iIdEmpleado : "";
      $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' ) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
      $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
      
      $where_gratuita = '';
/*
      if ( $Nu_Tipo_Impuesto == 1 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
      else if ( $Nu_Tipo_Impuesto == 2 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';
*/

      if($Nu_Tipo_Venta==1){//1=vender
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
EMPLE.ID_Entidad,
EMPLE.Nu_Documento_Identidad,
EMPLE.No_Entidad,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
MONE.Nu_Valor_FE AS Nu_Valor_Sunat_Moneda,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
VD.Qt_Producto,
VD.Ss_Precio,
VD.Ss_SubTotal,
VD.Ss_Impuesto,
VD.Ss_Total,
'0' AS Ss_Porcentaje_Impuesto,
VC.Nu_Estado,
VC.ID_Mesero,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN entidad AS EMPLE ON(VC.ID_Mesero = EMPLE.ID_Entidad)
LEFT JOIN producto AS PROD ON(VD.ID_Producto = PROD.ID_Producto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_empleado . "
" . $cond_item . "
" . $where_gratuita . "
ORDER BY
ID_Almacen,
ID_Entidad,
ID_Documento_Cabecera DESC;";
      }

      if($Nu_Tipo_Venta==2){//2=Punto de venta
        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
EMPLE.ID_Entidad,
EMPLE.Nu_Documento_Identidad,
EMPLE.No_Entidad,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
MONE.Nu_Valor_FE AS Nu_Valor_Sunat_Moneda,
'0.00' AS Ss_Tipo_Cambio,
'0.00' AS Ss_Tipo_Cambio_Modificar,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
VD.Qt_Producto,
VD.Ss_Precio,
VD.Ss_SubTotal,
VD.Ss_Impuesto,
VD.Ss_Total,
'0' AS Ss_Porcentaje_Impuesto,
VC.Nu_Estado,
VC.ID_Mesero,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN matricula_empleado AS MEMPLE ON(VC.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
LEFT JOIN entidad AS EMPLE ON(MEMPLE.ID_Entidad = EMPLE.ID_Entidad)
LEFT JOIN producto AS PROD ON(VD.ID_Producto = PROD.ID_Producto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Tipo_Asiento = 1
AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
" . $where_id_almacen . "
" . $cond_tipo . "
" . $cond_serie . "
" . $cond_numero . "
" . $cond_estado_documento . "
" . $cond_empleado . "
" . $cond_item . "
" . $where_gratuita . "
ORDER BY
ID_Almacen,
ID_Entidad,
ID_Documento_Cabecera DESC;";
      }

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
