<?php
class KardexValorizadoModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
    
	public function getTiposLibroSunat($ID_Tipo_Asiento){
		$query = "SELECT * FROM asiento_libro_sunat_detalle WHERE ID_Tipo_Asiento = " . $ID_Tipo_Asiento;
		return $this->db->query($query)->result();
	}
	
    public function kardex($arrParams){
        $ID_Producto = $arrParams['ID_Producto'];
        $ID_Tipo_Movimiento = $arrParams['ID_Tipo_Movimiento'];
        $ID_Almacen = $arrParams['ID_Almacen'];
        $where_id_almacen = ($ID_Almacen > 0 ? ' AND K.ID_Almacen = ' . $ID_Almacen : '');
        $where_producto = ($ID_Producto > 0 ? 'AND K.ID_Producto = ' . $ID_Producto : '');
        $where_tipo_movimiento = ($ID_Tipo_Movimiento > 0 ? 'AND K.ID_Tipo_Movimiento = ' . $ID_Tipo_Movimiento : '');
        
        $query = "SELECT * FROM (
SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
ALMA.Txt_Direccion_Almacen,
K.ID_Inventario,
PRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
TMOVI.Nu_Tipo_Movimiento,
CVCAB.Fe_Emision,
TDOCU.Nu_Sunat_Codigo AS Tipo_Documento_Sunat_Codigo,
TDOCU.No_Tipo_Documento_Breve,
CVCAB.ID_Tipo_Documento,
CVCAB.ID_Serie_Documento,
CVCAB.ID_Numero_Documento,
TMOVI.Nu_Sunat_Codigo AS Tipo_Operacion_Sunat_Codigo,
TMOVI.No_Tipo_Movimiento,
CLIPROV.Nu_Documento_Identidad,
CLIPROV.No_Entidad,
K.Qt_Producto,
K.Ss_Precio,
K.Ss_SubTotal,
K.Ss_Costo_Promedio,
TP.Sunat_Codigo_PLE AS TP_Sunat_Codigo,
TP.No_Tipo_Producto AS TP_Sunat_Nombre,
UM.Nu_Sunat_Codigo AS UM_Sunat_Codigo,
ALMA.Nu_Codigo_Establecimiento_Sunat,
CVCAB.Nu_Estado,
SD.Nu_Cantidad_Caracteres
FROM
movimiento_inventario AS K
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = K.ID_Almacen)
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = CVCAB.ID_Tipo_Documento)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
JOIN entidad AS CLIPROV ON(CLIPROV.ID_Entidad = CVCAB.ID_Entidad)
JOIN producto AS PRO ON(PRO.ID_Producto = K.ID_Producto)
JOIN tipo_producto AS TP ON(TP.ID_Tipo_Producto = PRO.ID_Tipo_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=CVCAB.ID_Serie_Documento_PK)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND CVCAB.Fe_Emision BETWEEN '" . $arrParams['dInicio'] . "' AND '" . $arrParams['dFin'] . "'
" . $where_id_almacen . "
" . $where_producto . "
" . $where_tipo_movimiento . "
UNION ALL
SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
ALMA.Txt_Direccion_Almacen,
K.ID_Inventario,
PRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
TMOVI.Nu_Tipo_Movimiento,
GESCAB.Fe_Emision,
TDOCU.Nu_Sunat_Codigo AS Tipo_Documento_Sunat_Codigo,
TDOCU.No_Tipo_Documento_Breve,
GESCAB.ID_Tipo_Documento,
GESCAB.ID_Serie_Documento,
GESCAB.ID_Numero_Documento,
TMOVI.Nu_Sunat_Codigo AS Tipo_Operacion_Sunat_Codigo,
TMOVI.No_Tipo_Movimiento,
CLIPROV.Nu_Documento_Identidad,
CLIPROV.No_Entidad,
K.Qt_Producto,
K.Ss_Precio,
K.Ss_SubTotal,
K.Ss_Costo_Promedio,
TP.Sunat_Codigo_PLE AS TP_Sunat_Codigo,
TP.No_Tipo_Producto AS TP_Sunat_Nombre,
UM.Nu_Sunat_Codigo AS UM_Sunat_Codigo,
ALMA.Nu_Codigo_Establecimiento_Sunat,
GESCAB.Nu_Estado,
SD.Nu_Cantidad_Caracteres
FROM
movimiento_inventario AS K
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = K.ID_Almacen)
JOIN guia_cabecera AS GESCAB ON(K.ID_Guia_Cabecera = GESCAB.ID_Guia_Cabecera)
JOIN tipo_documento AS TDOCU ON(TDOCU.ID_Tipo_Documento = GESCAB.ID_Tipo_Documento)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
JOIN entidad AS CLIPROV ON(CLIPROV.ID_Entidad = GESCAB.ID_Entidad)
JOIN producto AS PRO ON(PRO.ID_Producto = K.ID_Producto)
JOIN tipo_producto AS TP ON(TP.ID_Tipo_Producto = PRO.ID_Tipo_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=GESCAB.ID_Serie_Documento_PK)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND GESCAB.Fe_Emision BETWEEN '" . $arrParams['dInicio'] . "' AND '" . $arrParams['dFin'] . "'
" . $where_id_almacen . "
" . $where_producto . "
" . $where_tipo_movimiento . "
) AS A
ORDER BY ID_Almacen ASC, ID_Producto ASC, Fe_Emision ASC";

        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'sStatus' => 'danger',
                'sMessage' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
                'sMessageSQL' => $error['message'],
            );
        }

        $arrData = $this->db->query($query)->result();

        /*
        $orderID_Producto = array();
        $orderFe_Emision = array();
        foreach ($arrData as $key => $row) {
            $orderID_Producto[$key] = $row->ID_Producto;
            $orderFe_Emision[$key] = $row->Fe_Emision;
        }
        //array_multisort($orderID_Producto, SORT_ASC, $arrData);//13/02/2021
        array_multisort($orderID_Producto, SORT_ASC, $orderFe_Emision, SORT_ASC, $arrData);
        */
/*
        $orderID_Producto = array();
        $orderFe_Emision = array();
        foreach ($arrData as $key => $row) {
            $orderID_Producto[$key] = $row->ID_Producto;
            $orderFe_Emision[$key] = $row->Fe_Emision;
        }
        //array_multisort($orderID_Producto, SORT_ASC, $arrData);//13/02/2021
        array_multisort($orderID_Producto, SORT_ASC, $orderFe_Emision, SORT_ASC, $arrData);
*/
        /*
        $orderID_Almacen = array();
        $orderID_Producto = array();
        foreach ($arrData as $key => $row) {
            $orderID_Almacen[$key] = $row->ID_Almacen;
            $orderID_Producto[$key] = $row->ID_Producto;
        }
        array_multisort($orderID_Almacen, SORT_ASC, $orderID_Producto, SORT_ASC, $arrData);
*/
        if ( count($arrData) > 0 ){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrData,
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
        );
    }

    public function CrearReporte($valores){
        $valores["ID_Organizacion"] = $this->user->ID_Organizacion;
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 6,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=6 ORDER BY Fe_Creacion ASC LIMIT 1";
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
                    AND Nu_Tipo_Reporte=6
                    AND ID_Estatus IN(0,1,2)
                    ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=6 AND ID_Estatus=2";
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
