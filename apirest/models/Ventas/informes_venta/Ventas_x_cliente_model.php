<?php
class Ventas_x_cliente_model extends CI_Model{

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
      $iIdCliente=$arrParams['iIdCliente'];
      $sNombreCliente=$arrParams['sNombreCliente'];
      $iIdItem=$arrParams['iIdItem'];
      $sNombreItem=$arrParams['sNombreItem'];
      $ID_Almacen=$arrParams['ID_Almacen'];
      
      $iFiltroBusquedaNombre = $arrParams['iFiltroBusquedaNombre'];
      $ID_Familia = $arrParams['ID_Familia'];
      $ID_Sub_Familia = $arrParams['ID_Sub_Familia'];
      $ID_Marca = $arrParams['ID_Marca'];
      $ID_Variante_Item = $arrParams['ID_Variante_Item'];
      $ID_Variante_Item_Detalle_1 = $arrParams['ID_Variante_Item_Detalle_1'];
      $ID_Variante_Item2 = $arrParams['ID_Variante_Item2'];
      $ID_Variante_Item_Detalle_2 = $arrParams['ID_Variante_Item_Detalle_2'];
      $ID_Variante_Item3 = $arrParams['ID_Variante_Item3'];
      $ID_Variante_Item_Detalle_3 = $arrParams['ID_Variante_Item_Detalle_3'];

      $Nu_Tipo_Impuesto=$arrParams['Nu_Tipo_Impuesto'];

      $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
      $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
      $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
      $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
      $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND CLI.ID_Entidad = ' . $iIdCliente : "";
      $cond_item = ( $iIdItem != '-' && $sNombreItem != '-' && $iFiltroBusquedaNombre == 0) ? 'AND VD.ID_Producto = ' . $iIdItem : "";
      $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
        
      $where_like_nombre_item = (($iFiltroBusquedaNombre == 1 && !empty($sNombreItem)) ? " AND PROD.No_Producto LIKE '" . $this->db->escape_like_str($sNombreItem) . "%' ESCAPE '!'" : "");

      $where_familia = $ID_Familia != "0" ? ' AND PROD.ID_Familia = ' . $ID_Familia : "";
      $where_sub_familia = $ID_Sub_Familia != "0" ? ' AND PROD.ID_Sub_Familia = ' . $ID_Sub_Familia : "";
      $where_marca = $ID_Marca != "0" ? ' AND PROD.ID_Marca = ' . $ID_Marca : "";

      $where_variante_item = $ID_Variante_Item != "0" ? ' AND PROD.ID_Variante_Item_1  = ' . $ID_Variante_Item : "";
      $where_variante_item_detalle_1 = $ID_Variante_Item_Detalle_1 != "0" ? ' AND PROD.ID_Variante_Item_Detalle_1  = ' . $ID_Variante_Item_Detalle_1 : "";
      $where_variante_item2 = $ID_Variante_Item2 != "0" ? ' AND PROD.ID_Variante_Item_2  = ' . $ID_Variante_Item2 : "";
      $where_variante_item_detalle_2 = $ID_Variante_Item_Detalle_2 != "0" ? ' AND PROD.ID_Variante_Item_Detalle_2  = ' . $ID_Variante_Item_Detalle_2 : "";
      $where_variante_item3 = $ID_Variante_Item3 != "0" ? ' AND PROD.ID_Variante_Item_3  = ' . $ID_Variante_Item3 : "";
      $where_variante_item_detalle_3 = $ID_Variante_Item_Detalle_3 != "0" ? ' AND PROD.ID_Variante_Item_Detalle_3  = ' . $ID_Variante_Item_Detalle_3 : "";

      $where_gratuita = '';
      if ( $Nu_Tipo_Impuesto == 1 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto=4';
      else if ( $Nu_Tipo_Impuesto == 2 )
        $where_gratuita = 'AND IMP.Nu_Tipo_Impuesto!=4';

      $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.ID_Documento_Cabecera,
CLI.ID_Entidad,
CLI.Nu_Documento_Identidad,
CLI.No_Entidad,
TD.No_Tipo_Documento_Breve,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
MONE.Nu_Valor_FE AS Nu_Codigo_Moneda,
MONE.ID_Moneda,
MONE.No_Signo,
MONE.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_Moneda,
'' AS Ss_Tipo_Cambio,
'' AS Ss_Tipo_Cambio_Modificar,
PROD.Nu_Codigo_Barra,
PROD.No_Producto,
VD.Qt_Producto,
VD.Ss_Precio,
VD.Ss_SubTotal,
VD.Ss_Impuesto,
VD.Ss_Total,
IVDOCU.Ss_Impuesto AS Ss_Porcentaje_Impuesto,
VC.Nu_Estado,
(VD.Ss_Descuento + VD.Ss_Descuento_Impuesto) AS Ss_Descuento_Producto,
(VC.Ss_Descuento + VC.Ss_Descuento_Impuesto) AS Ss_Descuento_Global,
VC.Ss_Descuento,
VC.Ss_Descuento_Impuesto,
MONE.Nu_Valor_FE AS Nu_Valor_FE_Moneda,
VC.ID_Empresa,
IMP.Nu_Tipo_Impuesto
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN impuesto_cruce_documento AS IVDOCU ON(IVDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
LEFT JOIN impuesto AS IMP ON(IMP.ID_Impuesto = IVDOCU.ID_Impuesto)
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
" . $cond_cliente . "
" . $cond_item . "
" . $where_like_nombre_item . "
" . $where_familia . "
" . $where_sub_familia . "
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
CLI.ID_Entidad,
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

       public function CrearReporte($valores){
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 7,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=7 ORDER BY Fe_Creacion ASC LIMIT 1";
        $row = $this->db->query($query)->row();

       // if($row)
            return $row;
        // else
        //     exit();
    }

    public function getReporte_(){
        $query = "SELECT
                    ID_Reporte,
                    DATE_FORMAT(Fe_Creacion, \"%d/%m/%Y %T\") Fe_Creacion,
                    IF(Txt_Nombre_Archivo IS NULL or Txt_Nombre_Archivo = '', 'Esperando...', Txt_Nombre_Archivo) Txt_Nombre_Archivo,
                    ID_Estatus,Nu_Tipo_Formato
                    FROM
                    `reporte`
                    WHERE
                    ID_Empresa = ".$this->user->ID_Empresa."
                    AND Nu_Tipo_Reporte=7
                    AND ID_Estatus IN(0,1,2)
                    ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=7 AND ID_Estatus=2";
        $row = $this->db->query($query)->row();
        return $row;
    }

    public function CancelarReporte($ID_Reporte){
        $this->UpdateReporteBG(array("ID_Estatus"=>3),$ID_Reporte);
        return json_encode(array("sStatus"=>"success"));
    }

    public function UpdateReporteBG($data,$ID_Reporte){

        $this->db->where('ID_Reporte', $ID_Reporte);
        $this->db->update('reporte', $data);
        //print_r($this->db->last_query());
    }
}
