<?php
class KardexTextilModel extends CI_Model{

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
        $sNombreItem = str_replace("-","/",$sNombreItem);

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

        $where_id_almacen = ($ID_Almacen > 0 ? ' AND K.ID_Almacen IN(' . $ID_Almacen . ') ': '');
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
        //array_debug($arrData);

        $sCadenaIdProductosSaldoInicial = '';
        $where_producto_sin_stock = '';
        $arrDataAlmacenSinMovimiento=array();
        $iIdAlmacen = 0;
        $iIdProducto = 0;
        $arrIdAlmacen = array();
        $arrIdAlmacenyIdProducto = array();
        foreach ($arrData as $key => $row) {
            if($iIdProducto != $row->ID_Producto || $iIdAlmacen != $row->ID_Almacen){
                if($iIdAlmacen != $row->ID_Almacen){
                    $arrIdAlmacen[] = $row->ID_Almacen;
                    $iIdAlmacen = $row->ID_Almacen;
                }
                //if($iIdProducto != $row->ID_Producto){
                    $arrIdAlmacenyIdProducto[$iIdAlmacen][] = $row->ID_Producto;

                    $iIdProducto = $row->ID_Producto;
                //}
            }
        }

        $where_id_almacen = ($ID_Almacen > 0 ? ' AND ALMA.ID_Almacen IN(' . $ID_Almacen . ') ': '');
        $where_like_nombre_item = (($iFiltroBusquedaNombre == 1 && !empty($sNombreItem)) ? " AND PRO.No_Producto LIKE '%" . $this->db->escape_like_str($sNombreItem) . "%' ESCAPE '!'" : "");

        $iIdAlmacen = 0;
        $query_stock_producto = '';
        
        /*
        echo "<pre>";
        var_dump($arrIdAlmacenyIdProducto);
        echo "</pre>";
        echo "<pre>";
        var_dump($arrIdAlmacen);
        echo "</pre>";
        */

        /*
        $sCadenaIdAlmacenFueronUsados = '';
        for ($i = 0; $i < count($arrIdAlmacenyIdProducto); $i++) {
            $sCadenaIdAlmacenFueronUsados .= $arrIdAlmacen[$i] . ",";
        }
        */

        foreach ($arrIdAlmacen as $key => $rowAlmacen) {
            if($iIdAlmacen != $rowAlmacen){
                $sCadenaIdProductosSaldoInicial = '';
                $iIdAlmacen = $rowAlmacen;
                //var_dump($rowAlmacen);
                if(isset($arrIdAlmacenyIdProducto[$rowAlmacen])) {
                    for ($i = 0; $i < count($arrIdAlmacenyIdProducto[$rowAlmacen]); $i++) {
                        //var_dump($arrIdAlmacenyIdProducto);
                        $sCadenaIdProductosSaldoInicial .= $arrIdAlmacenyIdProducto[$rowAlmacen][$i] . ",";
                    }
                }
                
                if(!empty($sCadenaIdProductosSaldoInicial)) {
                    $where_producto_sin_stock = "AND STOCK.ID_Producto NOT IN(" . substr($sCadenaIdProductosSaldoInicial, 0, -1) . ")";
                }

                for ($i = 0; $i < count($arrIdAlmacenyIdProducto); $i++) {
                    $query_stock_producto = "SELECT
                    ALMA.ID_Almacen,
                    ALMA.No_Almacen,
                    PRO.ID_Producto,
                    PRO.Nu_Codigo_Barra,
                    PRO.No_Codigo_Interno,
                    PRO.No_Producto,
                    STOCK.Qt_Producto,
                    VIDCAB.No_Variante AS No_Variante_1,
                    VID.No_Valor AS No_Valor_Variante_1,
                    VIDCAB2.No_Variante AS No_Variante_2,
                    VID2.No_Valor AS No_Valor_Variante_2,
                    VIDCAB3.No_Variante AS No_Variante_3,
                    VID3.No_Valor AS No_Valor_Variante_3
                    FROM
                    producto AS PRO
                    JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Producto = PRO.ID_Producto)
                    JOIN almacen AS ALMA ON(ALMA.ID_Almacen = STOCK.ID_Almacen)
                    LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
                    LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
                    LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
                    LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
                    LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
                    LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
                    WHERE STOCK.ID_Empresa = " . $this->empresa->ID_Empresa  . " 
AND ALMA.ID_Almacen = " . $arrIdAlmacen[$i]  .  "
" . $where_variante_item . "
" . $where_variante_item_detalle_1 . "
" . $where_variante_item2 . "
" . $where_variante_item_detalle_2 . "
" . $where_variante_item3 . "
" . $where_variante_item_detalle_3 . " " . $where_like_nombre_item . " " . $where_producto_sin_stock . " ORDER BY ALMA.ID_Almacen ASC, PRO.ID_Producto ASC";
array_debug($query_stock_producto);
                    $arrDataAlmacenSinMovimiento = $this->db->query($query_stock_producto)->result();
                }
            }
        }
        //var_dump($arrDataAlmacenSinMovimiento);
        if ( count($arrData) > 0 && count($arrDataAlmacenSinMovimiento) > 1){
            return array(
                'sStatus' => 'success',
                'arrData' => $arrData,
                'arrDataAlmacenSinMovimiento' => $arrDataAlmacenSinMovimiento
            );
        }
        
        $sCadenaIdAlmacenFueronUsados = '';
        for ($i = 0; $i < count($arrIdAlmacenyIdProducto); $i++) {
            $sCadenaIdAlmacenFueronUsados .= $arrIdAlmacen[$i] . ",";
        }

        $arrDataAlmacenSinMovimiento=array();
        $where_producto = ($iFiltroBusquedaNombre == 0 && $ID_Producto > 0 ? ' AND PRO.ID_Producto = ' . $ID_Producto : '');
        $where_id_almacen = ($ID_Almacen > 0 ? ' AND ALMA.ID_Almacen IN(' . $ID_Almacen . ') ': '');
        $where_like_nombre_item = (($iFiltroBusquedaNombre == 1 && !empty($sNombreItem)) ? " AND PRO.No_Producto LIKE '%" . $this->db->escape_like_str($sNombreItem) . "%' ESCAPE '!'" : "");

        $query_stock_producto = "SELECT
        ALMA.ID_Almacen,
        ALMA.No_Almacen,
        PRO.ID_Producto,
        PRO.Nu_Codigo_Barra,
        PRO.No_Codigo_Interno,
        PRO.No_Producto,
        STOCK.Qt_Producto,
        VIDCAB.No_Variante AS No_Variante_1,
        VID.No_Valor AS No_Valor_Variante_1,
        VIDCAB2.No_Variante AS No_Variante_2,
        VID2.No_Valor AS No_Valor_Variante_2,
        VIDCAB3.No_Variante AS No_Variante_3,
        VID3.No_Valor AS No_Valor_Variante_3
        FROM
        producto AS PRO
        JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Producto = PRO.ID_Producto)
        JOIN almacen AS ALMA ON(ALMA.ID_Almacen = STOCK.ID_Almacen)
        LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
        LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
        LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
        LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
        LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
        LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
        WHERE STOCK.ID_Empresa = " . $this->empresa->ID_Empresa  . " 
" . $where_variante_item . "
" . $where_variante_item_detalle_1 . "
" . $where_variante_item2 . "
" . $where_variante_item_detalle_2 . "
" . $where_variante_item3 . "
" . $where_variante_item_detalle_3 . " " . $where_like_nombre_item . " " . $where_producto . " AND ALMA.ID_Almacen NOT IN(" . substr($sCadenaIdAlmacenFueronUsados, 0, -1) . ") ORDER BY ALMA.ID_Almacen ASC, PRO.ID_Producto ASC";
//array_debug($query_stock_producto);
        $arrDataAlmacenSinMovimiento[] = $this->db->query($query_stock_producto)->result();

        return array(
            'sStatus' => 'success',
            'arrData' => $arrData,
            'arrDataAlmacenSinMovimiento' => $arrDataAlmacenSinMovimiento
        );
    }
}
