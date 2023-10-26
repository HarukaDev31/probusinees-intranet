<?php
class KardexModel extends CI_Model{

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

        $iFiltroBusquedaNombre = $arrParams['iFiltroBusquedaNombre'];
        $sNombreItem = $arrParams['sNombreItem'];
        $ID_Familia = $arrParams['ID_Familia'];
        $ID_Sub_Familia = $arrParams['ID_Sub_Familia'];
        $ID_Marca = $arrParams['ID_Marca'];
        $ID_Variante_Item = $arrParams['ID_Variante_Item'];
        $ID_Variante_Item_Detalle_1 = $arrParams['ID_Variante_Item_Detalle_1'];
        $ID_Variante_Item2 = $arrParams['ID_Variante_Item2'];
        $ID_Variante_Item_Detalle_2 = $arrParams['ID_Variante_Item_Detalle_2'];
        $ID_Variante_Item3 = $arrParams['ID_Variante_Item3'];
        $ID_Variante_Item_Detalle_3 = $arrParams['ID_Variante_Item_Detalle_3'];
        $iFiltroItemMovimiento = $arrParams['iFiltroItemMovimiento'];

        $where_id_almacen = ($ID_Almacen > 0 ? ' AND K.ID_Almacen = ' . $ID_Almacen : '');
        $where_producto = ($iFiltroBusquedaNombre == 0 && $ID_Producto > 0 ? ' AND K.ID_Producto = ' . $ID_Producto : '');
        $where_tipo_movimiento = ($ID_Tipo_Movimiento > 0 ? ' AND K.ID_Tipo_Movimiento = ' . $ID_Tipo_Movimiento : '');
        
        $where_like_nombre_item = (($iFiltroBusquedaNombre == 1 && !empty($sNombreItem)) ? " AND PRO.No_Producto LIKE '%" . $this->db->escape_like_str($sNombreItem) . "%' ESCAPE '!'" : "");

        $where_familia = $ID_Familia != "0" ? ' AND PRO.ID_Familia = ' . $ID_Familia : "";
        $where_sub_familia = $ID_Sub_Familia != "0" ? ' AND PRO.ID_Sub_Familia = ' . $ID_Sub_Familia : "";
        $where_marca = $ID_Marca != "0" ? ' AND PRO.ID_Marca = ' . $ID_Marca : "";

        $where_variante_item = $ID_Variante_Item != "0" ? ' AND PRO.ID_Variante_Item_1  = ' . $ID_Variante_Item : "";
        $where_variante_item_detalle_1 = $ID_Variante_Item_Detalle_1 != "0" ? ' AND PRO.ID_Variante_Item_Detalle_1  = ' . $ID_Variante_Item_Detalle_1 : "";
        $where_variante_item2 = $ID_Variante_Item2 != "0" ? ' AND PRO.ID_Variante_Item_2  = ' . $ID_Variante_Item2 : "";
        $where_variante_item_detalle_2 = $ID_Variante_Item_Detalle_2 != "0" ? ' AND PRO.ID_Variante_Item_Detalle_2  = ' . $ID_Variante_Item_Detalle_2 : "";
        $where_variante_item3 = $ID_Variante_Item3 != "0" ? ' AND PRO.ID_Variante_Item_3  = ' . $ID_Variante_Item3 : "";
        $where_variante_item_detalle_3 = $ID_Variante_Item_Detalle_3 != "0" ? ' AND PRO.ID_Variante_Item_Detalle_3  = ' . $ID_Variante_Item_Detalle_3 : "";

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
        
        $arrDataAlmacenSinMovimiento=array();
        if($iFiltroItemMovimiento==1) {//2=con movimientos y 1 = ambos con y sin movmientos
            $iIdAlmacen = 0;
            $iIdProducto = 0;
            $arrIdAlmacen = array();
            $arrIdAlmacenyIdProducto = array();
            foreach ($arrData as $key => $row) {
                if($iIdAlmacen != $row->ID_Almacen){
                    $arrIdAlmacen[] = $row->ID_Almacen;
                    $iIdAlmacen = $row->ID_Almacen;
                }
                if($iIdProducto != $row->ID_Producto){
                    $arrIdAlmacenyIdProducto[$iIdAlmacen][] = $row->ID_Producto;

                    $iIdProducto = $row->ID_Producto;
                }
            }

            $iIdAlmacen = 0;
            $sCadenaIdProductos = '';
            foreach ($arrIdAlmacen as $key => $rowAlmacen) {
                if($iIdAlmacen != $rowAlmacen){
                    $sCadenaIdProductos = '';
                    $iIdAlmacen = $rowAlmacen;
                    
                    for ($i = 0; $i < count($arrIdAlmacenyIdProducto[$rowAlmacen]); $i++) {
                        $sCadenaIdProductos .= $arrIdAlmacenyIdProducto[$rowAlmacen][$i] . ",";
                    }

                    $query_stock_producto = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
PRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
STOCK.Qt_Producto
FROM
producto AS PRO
JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $iIdAlmacen ." AND STOCK.ID_Producto = PRO.ID_Producto)
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = STOCK.ID_Almacen)
WHERE
STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND STOCK.ID_Almacen = " . $iIdAlmacen . "
AND STOCK.ID_Producto NOT IN(" . substr($sCadenaIdProductos, 0, -2) . ")";
                    
                    $arrDataAlmacenSinMovimiento[] = $this->db->query($query_stock_producto)->result();
                    
                }
            }
        }

        if ( count($arrData) > 0 ){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrData,
                'arrDataAlmacenSinMovimiento' => $arrDataAlmacenSinMovimiento
            );
        }

        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
            'arrDataAlmacenSinMovimiento' => ''
        );
    }

      public function CrearReporte($valores){
        $valores["ID_Organizacion"] = $this->user->ID_Organizacion;
        $data = array(
            "ID_Empresa"=>$this->user->ID_Empresa,
            'Txt_Parametro' => serialize($valores),
            'Nu_Tipo_Reporte' => 5,
            'ID_Estatus' => 0,
            "Nu_Tipo_Formato"=>$valores["Nu_Tipo_Formato"]
        );

        $this->db->insert('reporte', $data);
        return json_encode(array("sStatus"=>"success"));
    } 

    public function getReporteBG(){
        $query = "SELECT * FROM reporte  WHERE ID_Estatus IN (0) AND Nu_Tipo_Reporte=5 ORDER BY Fe_Creacion ASC LIMIT 1";
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
                    AND Nu_Tipo_Reporte=5
                    AND ID_Estatus IN(0,1,2)
                    ORDER BY ID_Reporte DESC";
        $row = $this->db->query($query)->result();
        return $row;
    }

    public function getReporteRow($ID_Reporte){
        $query = "SELECT * FROM `reporte` WHERE ID_Reporte=".$ID_Reporte." AND ID_Empresa = ".$this->user->ID_Empresa." AND Nu_Tipo_Reporte=5 AND ID_Estatus=2";
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
