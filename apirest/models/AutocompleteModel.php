<?php
class AutocompleteModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function getDataAutocompleteProduct($global_table, $global_search, $filter_id_codigo){
	    $sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Producto AS Nombre,
PRO.Nu_Estado
FROM
producto AS PRO
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Compuesto = 0
AND PRO.Nu_Estado = 1
AND (
PRO.No_Producto LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
OR PRO.Nu_Codigo_Barra LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
OR PRO.No_Codigo_Interno LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
)";
        if ($filter_id_codigo != '')
            $sql .= " AND PRO.ID_Producto != " . $filter_id_codigo;
		$sql .= "
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
	
	public function getDataAutocompleteProductReport($global_table, $global_search, $filter_id_codigo, $filter_id_tipo_movimiento){
		if ( $filter_id_tipo_movimiento == 1 )
			$where_tipo_producto = 'AND PRO.Nu_Tipo_Producto != 2';
		else if ( $filter_id_tipo_movimiento == 2 )
			$where_tipo_producto = 'AND PRO.Nu_Tipo_Producto != 2';
		else if ( $filter_id_tipo_movimiento == 3 )//Kardex only item
			$where_tipo_producto = 'AND PRO.Nu_Tipo_Producto != 0';
	    $sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
PRO.No_Producto AS Nombre,
PRO.Nu_Estado,
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
PRO.Nu_Activar_Precio_x_Mayor
FROM
producto AS PRO
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Compuesto = 0
" . $where_tipo_producto . "
AND (
PRO.No_Producto LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
OR PRO.Nu_Codigo_Barra LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
OR PRO.No_Codigo_Interno LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
)";
        if ($filter_id_codigo != '')
            $sql .= " AND PRO.ID_Producto != " . $filter_id_codigo;
		$sql .= "
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
	
	public function getDataAutocompleteProductKardex($global_table, $global_search, $filter_id_codigo, $filter_id_tipo_movimiento){
	    $sql = "SELECT
PRO.Nu_Tipo_Producto,
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
'0' AS Qt_Producto,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.Nu_Compuesto,
PRO.ID_Impuesto_Icbper,
PRO.Ss_Icbper,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
PRO.Nu_Activar_Precio_x_Mayor
FROM
producto AS PRO
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND (PRO.No_Producto LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR PRO.No_Codigo_Interno LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
AND PRO.Nu_Compuesto = 0
AND PRO.Nu_Tipo_Producto != 2
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function getAllClient($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Txt_Email_Entidad,
Nu_Estado,
Nu_Dias_Credito,
ID_Tipo_Documento_Identidad,
ID_Departamento,
ID_Provincia,
ID_Distrito
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = 0
AND Nu_Estado = 1
AND (Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 10";
		return $this->db->query($sql)->result();
    }
    
	public function getAllClientCargaConsolidada($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Txt_Email_Entidad,
Nu_Estado,
Nu_Dias_Credito,
ID_Tipo_Documento_Identidad,
ID_Departamento,
ID_Provincia,
ID_Distrito
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = 0
AND Nu_Estado = 1
AND Nu_Carga_Consolidada = 1
AND (Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 10";
		return $this->db->query($sql)->result();
    }
    
	public function getAllProvider($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Nu_Estado,
Nu_Dias_Credito,
ID_Tipo_Documento_Identidad
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = 1
AND Nu_Estado = 1
AND (No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function getAllEmployee($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Nu_Estado
FROM
entidad
WHERE
ID_Empresa = " . $this->empresa->ID_Empresa . "
AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND Nu_Tipo_Entidad = 4
AND Nu_Estado = 1
AND (No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function obtenerUsuarios($global_table, $global_search){
	    $sql = "SELECT
ID_Usuario AS ID,
No_Usuario AS Nombre
FROM
usuario
WHERE
ID_Empresa = " . $this->empresa->ID_Empresa . "
AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND No_Usuario LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
ORDER BY
No_Usuario DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function getAllDelivery($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Nu_Estado,
ID_Tipo_Documento_Identidad
FROM
entidad
WHERE
ID_Empresa = " . $this->empresa->ID_Empresa . "
AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND Nu_Tipo_Entidad = 6
AND Nu_Estado = 1
AND (No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
	
	public function getAllProduct($global_table, $global_search, $filter_id_almacen, $filter_nu_compuesto, $filter_nu_tipo_producto, $filter_lista){
	    $sql = "SELECT
PRO.Nu_Tipo_Producto,
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
ROUND(STOCK.Qt_Producto, 2) AS Qt_Producto,
LPD.Ss_Precio_Interno,
LPD.Po_Descuento,
LPD.Ss_Precio,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.Nu_Compuesto,
PRO.ID_Impuesto_Icbper,
PRO.Ss_Icbper,
UM.No_Unidad_Medida,
L.No_Laboratorio,
SF.No_Sub_Familia,
STOCK.Nu_Estado,
STOCK.ID_Almacen,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
PRO.Nu_Activar_Precio_x_Mayor,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
IMP.Nu_Valor_FE AS codigo_impuesto_sunat_pse
FROM
producto AS PRO
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN marca AS M ON(M.ID_Marca = PRO.ID_Marca)
LEFT JOIN laboratorio AS L ON(L.ID_Laboratorio = PRO.ID_Laboratorio)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = PRO.ID_Sub_Familia)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $filter_lista . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE PRO.ID_Empresa = " . $this->user->ID_Empresa . " AND PRO.Nu_Estado = 1 AND (PRO.No_Producto LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR PRO.No_Codigo_Interno LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
";
        if ($filter_nu_compuesto != '')
            $sql .= " AND PRO.Nu_Compuesto = " . $filter_nu_compuesto;
        if ($filter_nu_tipo_producto != '')
            $sql .= " AND PRO.Nu_Tipo_Producto != " . $filter_nu_tipo_producto;
		$sql .= "
LIMIT 15";
		return $this->db->query($sql)->result();
    }
	
	public function getAllProductClic($global_table, $global_search, $filter_id_almacen, $filter_nu_compuesto, $filter_nu_tipo_producto, $filter_lista){
		$sql = "SELECT
PRO.Nu_Tipo_Producto,
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
LPD.Ss_Precio_Interno,
LPD.Po_Descuento,
LPD.Ss_Precio,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.Nu_Compuesto,
PRO.ID_Impuesto_Icbper,
PRO.Ss_Icbper,
UM.No_Unidad_Medida,
L.No_Laboratorio,
PRO.Nu_Activar_Precio_x_Mayor,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
IMP.Nu_Valor_FE AS codigo_impuesto_sunat_pse
FROM
producto AS PRO
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN marca AS M ON(M.ID_Marca = PRO.ID_Marca)
LEFT JOIN laboratorio AS L ON(L.ID_Laboratorio = PRO.ID_Laboratorio)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $filter_lista . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1
AND (PRO.ID_Producto = '" . $this->db->escape_like_str($global_search) . "' OR PRO.Nu_Codigo_Barra = '" . $this->db->escape_like_str($global_search) . "' OR PRO.No_Codigo_Interno = '" . $this->db->escape_like_str($global_search) . "')
LIMIT 1";
		return $this->db->query($sql)->result();
    }
    
	public function getItemAlternativos($arrPost){
		$iIdListaPrecio = $arrPost['iIdListaPrecio'];
		$iIdItem = $arrPost['iIdItem'];
		$sComposicion = $arrPost['sComposicion'];

	    $query = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
LPD.Ss_Precio_Interno,
LPD.Po_Descuento,
LPD.Ss_Precio,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.ID_Impuesto_Icbper,
UM.No_Unidad_Medida,
PRO.Ss_Icbper,
PRO.Nu_Activar_Precio_x_Mayor,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
IMP.Nu_Valor_FE AS codigo_impuesto_sunat_pse
FROM
producto AS PRO
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN marca AS M ON(M.ID_Marca = PRO.ID_Marca)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $iIdListaPrecio . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1
AND PRO.Nu_Tipo_Producto = 1
AND PRO.Txt_Composicion = '" . $this->db->escape_like_str($sComposicion) . "'
AND PRO.ID_Producto != " . $iIdItem . "
ORDER BY
PRO.No_Producto";
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
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}
    
	public function getItemsVariante($arrPost){
		$iIdListaPrecio = $arrPost['iIdListaPrecio'];
		$sNombreItem = $arrPost['sNombreItem'];

	    $query = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
LPD.Ss_Precio_Interno,
LPD.Po_Descuento,
LPD.Ss_Precio,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.ID_Impuesto_Icbper,
PRO.Ss_Icbper,
UM.No_Unidad_Medida,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
PRO.Nu_Activar_Precio_x_Mayor,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
IMP.Nu_Valor_FE AS codigo_impuesto_sunat_pse
FROM
producto AS PRO
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN marca AS M ON(M.ID_Marca = PRO.ID_Marca)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $iIdListaPrecio . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1
AND PRO.Nu_Tipo_Producto = 1
AND PRO.No_Producto LIKE '" . $this->db->escape_like_str($sNombreItem) . "%'";
//array_debug($query);
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
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}
    
	public function autocompleteItemAlternativos($arrPost){
		$iIdListaPrecio = $arrPost['iIdListaPrecio'];
		$sNombreUpcSkuItem = $arrPost['sNombreUpcSkuItem'];
		$iValidarStockGlobal = $arrPost['iValidarStockGlobal'];		
		$where_validar_stock = ( $iValidarStockGlobal == 1 ? 'AND STOCK.Qt_Producto > 0' : '' );
	    $query = "SELECT
PRO.Txt_Composicion
FROM
producto AS PRO
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1
AND PRO.Nu_Tipo_Producto = 1
AND (PRO.No_Producto LIKE '" . $sNombreUpcSkuItem . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '" . $sNombreUpcSkuItem . "%' ESCAPE '!' OR PRO.No_Codigo_Interno LIKE '" . $sNombreUpcSkuItem . "%' ESCAPE '!')
" . $where_validar_stock;
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
			$arrData = $arrResponseSQL->result();
			$sConcatenarIdComposiciones = '';
			if ( !empty($arrData[0]->Txt_Composicion) ) {
				foreach ($arrData as $row)
					$sConcatenarIdComposiciones .= (!empty($row->Txt_Composicion) ? $row->Txt_Composicion . ',' : '');
				$sConcatenarIdComposiciones = substr($sConcatenarIdComposiciones, 0, -1);
			} else {
				return array(
					'sStatus' => 'warning',
					'sMessage' => 'No se encontraron registros con compisiciones iguales',
				);
			}

			$query = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Codigo_Interno,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
PRO.Txt_Producto AS Descripcion,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
LPD.Ss_Precio_Interno,
LPD.Po_Descuento,
LPD.Ss_Precio,
PRO.Txt_Composicion,
PRO.Ss_Precio as Ss_Precio_Item,
PRO.Ss_Costo as Ss_Costo_Item,
PRO.ID_Impuesto_Icbper,
PRO.Ss_Icbper,
UM.No_Unidad_Medida,
L.No_Laboratorio,
SF.No_Sub_Familia,
PRO.Nu_Activar_Precio_x_Mayor,
UM.Nu_Sunat_Codigo AS codigo_unidad_medida_sunat,
IMP.Nu_Sunat_Codigo AS codigo_impuesto_sunat,
IMP.Nu_Valor_FE AS codigo_impuesto_sunat_pse
FROM
producto AS PRO
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN marca AS M ON(M.ID_Marca = PRO.ID_Marca)
LEFT JOIN laboratorio AS L ON(L.ID_Laboratorio = PRO.ID_Laboratorio)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = PRO.ID_Sub_Familia)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $iIdListaPrecio . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1
AND PRO.Nu_Tipo_Producto = 1
AND PRO.Txt_Composicion IN(" . $this->db->escape_like_str($sConcatenarIdComposiciones) . ")";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos con compisiciones iguales',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros con compisiciones iguales',
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}
	
	public function getAllContact($global_search, $iFilter_Tipo_Asiento){
		$iTipoEntidad = 8;
		if ($iFilter_Tipo_Asiento == 2)
			$iTipoEntidad = 7;
	    $sql = "SELECT
ID_Entidad AS ID,
ID_Tipo_Documento_Identidad,
Nu_Documento_Identidad,
No_Entidad AS No_Contacto,
Nu_Telefono_Entidad AS Nu_Telefono_Contacto,
Nu_Celular_Entidad AS Nu_Celular_Contacto,
Txt_Email_Entidad AS Txt_Email_Contacto
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = " . $iTipoEntidad . "
AND Nu_Estado = 1
AND (No_Entidad LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR Nu_Documento_Identidad LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
No_Contacto DESC 
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function getAllOrden($global_search){
	    $sql = "SELECT
ID_Documento_Cabecera,
ID_Numero_Documento,
CONTACT.ID_Tipo_Documento_Identidad,
CONTACT.Nu_Documento_Identidad,
CONTACT.No_Entidad AS No_Contacto,
CONTACT.Nu_Telefono_Entidad AS Nu_Telefono_Contacto,
CONTACT.Nu_Celular_Entidad AS Nu_Celular_Contacto,
CONTACT.Txt_Email_Entidad AS Txt_Email_Contacto
FROM
documento_cabecera AS VC
JOIN entidad AS CONTACT ON(CONTACT.ID_Entidad = VC.ID_Contacto)
WHERE
VC.ID_Empresa = " . $this->user->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND VC.ID_Tipo_Documento = 1
AND VC.ID_Documento_Cabecera LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
ORDER BY
No_Contacto DESC 
LIMIT 15";
		return $this->db->query($sql)->result();
    }
    
	public function getData($sTabla, $iTipoSocio){
		if ($sTabla == 'entidad') {
			if ($iTipoSocio == '1') {//Cliente
			    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = 0
AND Nu_Estado = 1";
			} else {//Proveedor
			    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad
FROM
entidad
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Tipo_Entidad = 1
AND Nu_Estado = 1";
			}
		} else if ($sTabla == 'producto') {
			$sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
M.ID_Marca,
M.No_Marca AS No_Marca,
PRO.No_Producto AS Nombre,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.Nu_Tipo_Impuesto,
IMPDOC.Ss_Impuesto,
0 AS Qt_Producto,
PRO.Nu_Activar_Precio_x_Mayor
FROM
producto AS PRO
JOIN impuesto AS IMP ON(IMP.ID_Empresa = " . $this->empresa->ID_Empresa . " AND IMP.ID_Impuesto = PRO.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON(IMPDOC.ID_Impuesto = IMP.ID_Impuesto AND IMPDOC.Nu_Estado = 1)
LEFT JOIN marca AS M ON(M.ID_Empresa = " . $this->empresa->ID_Empresa . " AND  M.ID_Marca = PRO.ID_Marca)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Estado = 1";
		} else if ($sTabla == 'empleado') {
			$sql = "SELECT
ID_Empleado AS ID,
Nu_Documento_Identidad AS Codigo,
No_Empleado AS Nombre
FROM
empleado
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND Nu_Estado = 1";
		}
		return $this->db->query($sql)->result();
    }
	
	public function getAllItemSunat($global_search){
	    $sql = "SELECT
 ID_Tabla_Dato,
 No_Descripcion
FROM
 tabla_dato
WHERE
 No_Relacion='Catalogo_Producto_Sunat'
 AND No_Descripcion LIKE '" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!'
LIMIT 15";
		return $this->db->query($sql)->result();
	}

	public function getClienteEspecifico($arrPost){
		$sNumeroDocumentoIdentidad=$arrPost['sNumeroDocumentoIdentidad'];
		$query = "SELECT ID_Entidad AS ID, No_Entidad AS Nombre, Txt_Direccion_Entidad, Nu_Estado FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad=".$sNumeroDocumentoIdentidad." LIMIT 1";
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
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
		);
	}

	public function getAllClientMarketSeller($global_table, $global_search){
	    $sql = "SELECT
ID_Entidad AS ID,
Nu_Documento_Identidad AS Codigo,
No_Entidad AS Nombre,
Txt_Direccion_Entidad,
Nu_Telefono_Entidad,
Nu_Celular_Entidad,
Txt_Email_Entidad,
Nu_Estado,
Nu_Dias_Credito
FROM
entidad
WHERE
(ID_Empresa = " . $this->empresa->ID_Empresa . " OR ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . ")
AND Nu_Estado = 1
AND (Nu_Documento_Identidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!' OR No_Entidad LIKE '%" . $this->db->escape_like_str($global_search) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
}
