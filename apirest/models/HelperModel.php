<?php
class HelperModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getTiposDocumentosModificar($Nu_Tipo_Filtro){
		$this->db->where('Nu_Estado', 1);
		$this->db->where('Nu_Enlace_Modificar', 1);
		if ($Nu_Tipo_Filtro == '1')//Venta
			$this->db->where('Nu_Venta', 1);
		if ($Nu_Tipo_Filtro == '2')//Compra
			$this->db->where('Nu_Compra', 1);
		$this->db->order_by('No_Tipo_Documento');
		return $this->db->get('tipo_documento')->result();
	}
	
	public function getSeriesDocumentoModificar($arrPost){
		$iIDOrganizacion = $arrPost['ID_Organizacion'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$query = "SELECT * FROM serie_documento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Organizacion = " . $iIDOrganizacion . " AND ID_Tipo_Documento = " . $iIDTipoDocumento . " AND Nu_Estado = 1";
		return $this->db->query($query)->result();
	}
	
	public function getSeriesDocumentoModificarxAlmacen($arrPost){
		$iIDOrganizacion = $arrPost['ID_Organizacion'];
		$iIdAlmacen = $arrPost['ID_Almacen'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$query = "SELECT * FROM serie_documento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Organizacion = " . $iIDOrganizacion . " AND ID_Almacen = " . $iIdAlmacen . " AND ID_Tipo_Documento = " . $iIDTipoDocumento . " AND Nu_Estado = 1";
		return $this->db->query($query)->result();
	}
	
	public function getMotivosReferenciaModificar($ID_Tipo_Documento){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Motivos_Referencia');
		$this->db->where('No_Class', $ID_Tipo_Documento);
		return $this->db->get('tabla_dato')->result();
	}
	
	public function documentExistVerify($ID_Documento_Guardado, $ID_Tipo_Documento_Modificar, $ID_Serie_Documento_Modificar, $ID_Numero_Documento_Modificar, $arrPost){		
		$iIdEntidad = $arrPost['iIdEntidad'];
		if ($arrPost['iTipoCliente']=='3') {// Cliente existente < 700 IGV y es Boleta
			$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND No_Entidad LIKE '%clientes varios%' LIMIT 1"; //1 = ID_Entidad -> Cliente varios
			if ( !$this->db->simple_query($query) ){
				$this->db->trans_rollback();
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos de clientes varios',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$arrData = $arrResponseSQL->result();
				$iIdEntidad = $arrData[0]->ID_Entidad;
			} else {
				$this->db->trans_rollback();
				return array(
					'sStatus' => 'warning',
					'sMessage' => 'No se encontro clientes varios',
					'sClassModal' => 'modal-warning',
				);
			}
		}// /. Cliente existente < 700 IGV y es Boleta

		if ( empty($ID_Serie_Documento_Modificar) && empty($ID_Numero_Documento_Modificar) && !empty($ID_Documento_Guardado) )
			$arrData = $this->db->query("SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $ID_Documento_Guardado . " LIMIT 1")->result();
		else
			$arrData = $this->db->query("SELECT ID_Documento_Cabecera FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Documento = " . $ID_Tipo_Documento_Modificar . " AND ID_Serie_Documento = '" . $ID_Serie_Documento_Modificar . "' AND ID_Numero_Documento = " . $ID_Numero_Documento_Modificar . " AND ID_Entidad = " . $iIdEntidad . " LIMIT 1")->result();

		if( count($arrData) > 0 ){
			$ID_Documento_Cabecera_Enlace = $arrData[0]->ID_Documento_Cabecera;
			if ( $ID_Documento_Guardado == 0 && $this->db->query("SELECT COUNT(*) existe FROM documento_enlace WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Documento_Cabecera_Enlace = " . $ID_Documento_Cabecera_Enlace . " LIMIT 1")->row()->existe == 1){
				return array('esEnlace' => 1, 'status' => 'warning', 'style_panel' => 'panel-warning', 'style_p' => 'text-warning', 'style_modal' => 'modal-warning', 'message' => 'El documento ya se encuentra enlazado', 'ID_Documento_Cabecera_Enlace' => $ID_Documento_Cabecera_Enlace);
			} else {
				return array(
					'esEnlace' => 1,
					'status' => 'success',
					'style_panel' => 'panel-success',
					'style_p' => 'text-success',
					'style_modal' => 'modal-succes',
					'message' => 'Documento encontrado',
					'ID_Documento_Cabecera_Enlace' => $ID_Documento_Cabecera_Enlace
				);
			}
		}
		return array('status' => 'error', 'style_panel' => 'panel-danger', 'style_p' => 'text-danger', 'style_modal' => 'modal-danger', 'message' => 'No existe documento de referencia');
	}
	
	public function getEmpresasTodo(){
		$query = "SELECT
ID_Empresa,
No_Empresa,
Nu_Tipo_Proveedor_FE,
'' AS No_Descripcion_Proveedor_FE,
'' AS Nu_Tipo_Ecommerce
FROM empresa ORDER BY No_Empresa;";

/*
		$query = "SELECT
ID_Empresa,
No_Empresa,
Nu_Tipo_Proveedor_FE,
TIPOPROVEEDORFE.No_Descripcion AS No_Descripcion_Proveedor_FE,
TIPOECOMMERCE.Nu_Valor AS Nu_Tipo_Ecommerce
FROM
empresa AS EMP
LEFT JOIN tabla_dato AS TIPOPROVEEDORFE ON(TIPOPROVEEDORFE.Nu_Valor=EMP.Nu_Tipo_Proveedor_FE AND TIPOPROVEEDORFE.No_Relacion = 'Tipos_Proveedor_FE')
LEFT JOIN tabla_dato AS TIPOECOMMERCE ON(TIPOECOMMERCE.ID_Tabla_Dato=EMP.Nu_Tipo_Ecommerce_Empresa AND TIPOECOMMERCE.No_Relacion = 'Tipos_Ecommerce_Empresa')
WHERE
Nu_Estado = 1
ORDER BY
No_Empresa;";
*/
		return $this->db->query($query)->result();
	}
	
	public function getEmpresas(){
		$query = "SELECT
ID_Empresa,
No_Empresa,
Nu_Tipo_Proveedor_FE,
'' AS No_Descripcion_Proveedor_FE,
'' AS Nu_Tipo_Ecommerce
FROM
empresa AS EMP
WHERE
Nu_Estado = 1
ORDER BY
No_Empresa;";

/*
		$query = "SELECT
ID_Empresa,
No_Empresa,
Nu_Tipo_Proveedor_FE,
TIPOPROVEEDORFE.No_Descripcion AS No_Descripcion_Proveedor_FE,
TIPOECOMMERCE.Nu_Valor AS Nu_Tipo_Ecommerce
FROM
empresa AS EMP
LEFT JOIN tabla_dato AS TIPOPROVEEDORFE ON(TIPOPROVEEDORFE.Nu_Valor=EMP.Nu_Tipo_Proveedor_FE AND TIPOPROVEEDORFE.No_Relacion = 'Tipos_Proveedor_FE')
LEFT JOIN tabla_dato AS TIPOECOMMERCE ON(TIPOECOMMERCE.ID_Tabla_Dato=EMP.Nu_Tipo_Ecommerce_Empresa AND TIPOECOMMERCE.No_Relacion = 'Tipos_Ecommerce_Empresa')
WHERE
Nu_Estado = 1
ORDER BY
No_Empresa;";
*/
		return $this->db->query($query)->result();
	}
	
	public function getEmpresasLogin($arrPost){
		$where_empresa = '';
		if ( isset($arrPost['iIdEmpresa']) ) {
			$where_empresa = ' AND EMP.ID_Empresa = ' . $arrPost['iIdEmpresa'];
		}
		
		$query = "SELECT
ID_Empresa,
No_Empresa,
Nu_Tipo_Proveedor_FE,
'' AS No_Descripcion_Proveedor_FE,
'' AS Nu_Tipo_Ecommerce
FROM
empresa AS EMP
WHERE
Nu_Estado = 1
" . $where_empresa . "
ORDER BY
No_Empresa;";

/*
		$query = "SELECT
 ID_Empresa,
 No_Empresa,
 Nu_Tipo_Proveedor_FE,
 TIPOPROVEEDORFE.No_Descripcion AS No_Descripcion_Proveedor_FE,
 TIPOECOMMERCE.Nu_Valor AS Nu_Tipo_Ecommerce
FROM
 empresa AS EMP
 LEFT JOIN tabla_dato AS TIPOPROVEEDORFE ON(TIPOPROVEEDORFE.Nu_Valor=EMP.Nu_Tipo_Proveedor_FE AND TIPOPROVEEDORFE.No_Relacion = 'Tipos_Proveedor_FE')
 LEFT JOIN tabla_dato AS TIPOECOMMERCE ON(TIPOECOMMERCE.ID_Tabla_Dato=EMP.Nu_Tipo_Ecommerce_Empresa AND TIPOECOMMERCE.No_Relacion = 'Tipos_Ecommerce_Empresa')
WHERE
 Nu_Estado = 1
 " . $where_empresa . "
ORDER BY
 No_Empresa;";
 */
		return $this->db->query($query)->result();
	}
	
	public function getOrganizaciones($arrPost){
		$where_empresa = '';
		if ( isset($arrPost['iIdEmpresa']) ) {
			$where_empresa = ' AND ID_Empresa = ' . $arrPost['iIdEmpresa'];
		}
		$query = "SELECT ID_Organizacion, No_Organizacion FROM organizacion WHERE Nu_Estado = 1 " . $where_empresa . " ORDER BY No_Organizacion";
		return $this->db->query($query)->result();
	}
	
	public function getAlmacenes($arrPost){
		$where_id_organizacion = 'AND ID_Organizacion=' . $this->empresa->ID_Organizacion;
		if ( isset( $arrPost['iIdOrganizacion'] ) ) {
			$where_id_organizacion = 'AND ID_Organizacion=' . $arrPost['iIdOrganizacion'];
		}
		$query = "SELECT
ID_Almacen,
No_Almacen,
Txt_Direccion_Almacen
FROM
almacen
WHERE
Nu_Estado = 1
" . $where_id_organizacion . "
ORDER BY
No_Almacen;";
		return $this->db->query($query)->result();
	}
	
	public function getAlmacenesEmpresa(){
		$query = "SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Organizacion IN (SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Estado = 1) AND Nu_Estado = 1 ORDER BY No_Almacen";
		return $this->db->query($query)->result();
	}
	
	public function getGrupos($arrPost){
		$where_empresa = '';
		if ( isset($arrPost['iIdEmpresa']) ) {
			$where_empresa = ' AND ID_Empresa=' . $arrPost['iIdEmpresa'];
		}
		$where_organizacion = '';
		if ( isset($arrPost['iIdOrganizacion']) ) {
			$where_organizacion = ' AND ID_Organizacion=' . $arrPost['iIdOrganizacion'];
		}
		$query = "SELECT ID_Grupo, No_Grupo FROM grupo WHERE ID_Grupo>2 " . $where_empresa . $where_organizacion . " AND Nu_Estado=1 ORDER BY No_Grupo";
		return $this->db->query($query)->result();
	}
	
	public function getMonedas(){
		$query = "SELECT * FROM moneda WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado = 1 ORDER BY ID_Moneda";
		return $this->db->query($query)->result();
	}

	public function getPaises(){
		//$this->db->where('ID_Pais', $this->user->ID_Pais);//agregado el 11/08 para pais
		$this->db->where('Nu_Estado', 1);
		$this->db->order_by('No_Pais');
		return $this->db->get('pais')->result();
	}

	public function getDepartamentos($ID_Pais){
		$this->db->where('Nu_Estado', 1);
	    $this->db->where('ID_Pais', $ID_Pais);
		$this->db->order_by('No_Departamento');
		return $this->db->get('departamento')->result();
	}
	
	public function getProvincias($ID_Departamento){
		$this->db->where('Nu_Estado', 1);
	    $this->db->where('ID_Departamento', $ID_Departamento);
		$this->db->order_by('No_Provincia');
		return $this->db->get('provincia')->result();
	}

	public function getDistritos($ID_Provincia){
		$this->db->where('Nu_Estado', 1);
		if (!empty($ID_Provincia))
	    	$this->db->where('ID_Provincia', $ID_Provincia);
		$this->db->order_by('No_Distrito');
		return $this->db->get('distrito')->result();
	}

	public function getTiposSexo(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Tipos_Sexo');
		return $this->db->get('tabla_dato')->result();
	}

	public function getSeriesDocumento($arrPost){
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$query = "
SELECT
 *
FROM
serie_documento
WHERE
 ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND Nu_Estado = 1
 AND ID_POS IS NULL
ORDER BY
 ID_Serie_Documento";
		return $this->db->query($query)->result();
	}

	public function getSeriesDocumentoxAlmacen($arrPost){
		$iIdAlmacen = $arrPost['ID_Almacen'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$query = "
SELECT
 *
FROM
serie_documento
WHERE
 ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND ID_Almacen = " . $iIdAlmacen . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND Nu_Estado = 1
 AND ID_POS IS NULL
ORDER BY
 ID_Serie_Documento";
		return $this->db->query($query)->result();
	}

	public function getSeriesDocumentoPuntoVenta($arrPost){	
		$query = "
SELECT
 ID_POS,
 ID_Serie_Documento
FROM
 serie_documento
WHERE
 ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND ID_Tipo_Documento = " . $arrPost['ID_Tipo_Documento'] . "
 AND Nu_Estado = 1
 AND ID_POS > 0
ORDER BY
 ID_Serie_Documento";
		return $this->db->query($query)->result();
	}

	public function getSeriesEmpresaOrgAlmacenDocumentoOficinaPuntoVenta($arrPost){
		$iIdEmpresa = $arrPost['iIdEmpresa'];
		$iIdOrganizacion = $arrPost['iIdOrganizacion'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$where_almacen = (isset($arrPost['iIdAlmacen']) && $arrPost['iIdAlmacen'] > 0 ? ' AND ID_Almacen=' . $arrPost['iIdAlmacen'] : '');

		$query = "
SELECT
 *
FROM
serie_documento
WHERE
 ID_Empresa = " . $iIdEmpresa . "
 AND ID_Organizacion = " . $iIdOrganizacion . "
 " . $where_almacen . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND Nu_Estado = 1
ORDER BY
 ID_Serie_Documento";
		return $this->db->query($query)->result();
	}

	public function getSeriesDocumentoOficinaPuntoVenta($arrPost){
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		
		$iIdOrganizacion = (isset($arrPost['ID_Organizacion']) && $arrPost['ID_Organizacion'] > 0 ? $arrPost['ID_Organizacion'] : $this->empresa->ID_Organizacion);

		$query = "
SELECT
 *
FROM
serie_documento
WHERE
 ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND ID_Organizacion = " . $iIdOrganizacion . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND Nu_Estado = 1
ORDER BY
 ID_Serie_Documento";
		return $this->db->query($query)->result();
	}

	public function getPuntoVenta($arrPost){
		$query = "
SELECT
 ID_POS
FROM
 serie_documento
WHERE
 ID_Empresa = " . $this->empresa->ID_Empresa . "
 AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
 AND Nu_Estado = 1
 AND ID_POS > 0
GROUP BY
 ID_POS
ORDER BY
 ID_POS";
		return $this->db->query($query)->result();
	}
	
	public function getNumeroDocumento($arrPost){
		$iIDOrganizacion = $arrPost['ID_Organizacion'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		$iIDSerieDocumento = $arrPost['ID_Serie_Documento'];
		
		$query = "
SELECT
 Nu_Numero_Documento AS ID_Numero_Documento
FROM
 serie_documento
WHERE
 ID_Empresa = " . $this->user->ID_Empresa . "
 AND ID_Organizacion = " . $iIDOrganizacion . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND ID_Serie_Documento = '" . $iIDSerieDocumento . "'
LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getNumeroDocumentoxAlmacen($arrPost){
		$iIDOrganizacion = $arrPost['ID_Organizacion'];
		$iIdAlmacen = $arrPost['ID_Almacen'];
		$iIDTipoDocumento = $arrPost['ID_Tipo_Documento'];
		$iIDSerieDocumento = $arrPost['ID_Serie_Documento'];
		
		$query = "
SELECT
 Nu_Numero_Documento AS ID_Numero_Documento
FROM
 serie_documento
WHERE
 ID_Empresa = " . $this->user->ID_Empresa . "
 AND ID_Organizacion = " . $iIDOrganizacion . "
 AND ID_Almacen = " . $iIdAlmacen . "
 AND ID_Tipo_Documento = " . $iIDTipoDocumento . "
 AND ID_Serie_Documento = '" . $iIDSerieDocumento . "'
LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getStockProducto($arrParams){
		$Fe_Buscar = $arrParams['fYear'] . '-' . $arrParams['fMonth'];

		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CONCAT(YEAR(CVCAB.Fe_Emision), '-', MONTH(CVCAB.Fe_Emision)) < '" . $Fe_Buscar . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_entrada = $this->db->query($query)->row();
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CONCAT(YEAR(CVCAB.Fe_Emision), '-', MONTH(CVCAB.Fe_Emision)) < '" . $Fe_Buscar . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_salida = $this->db->query($query)->row();
		
		return $row_cantidad_entrada->Qt_Producto - $row_cantidad_salida->Qt_Producto;
	}

	public function getStockProductoxFechaInicioyFin($arrParams){
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision < '" . $arrParams['dInicio'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_entrada = $this->db->query($query)->row();
		$row_cantidad_entrada = $row_entrada->Qt_Producto;
		$row_subtotal_entrada = $row_entrada->Ss_SubTotal;

		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision < '" . $arrParams['dInicio'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_entrada_guia = $this->db->query($query)->row();
		$row_cantidad_entrada_guia = $row_entrada_guia->Qt_Producto;
		$row_subtotal_entrada_guia = $row_entrada_guia->Ss_SubTotal;
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision < '" . $arrParams['dInicio'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_salida = $this->db->query($query)->row();
		$row_cantidad_salida = $row_salida->Qt_Producto;
		$row_subtotal_salida = $row_salida->Ss_SubTotal;
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND K.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision < '" . $arrParams['dInicio'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_salida_guia = $this->db->query($query)->row();
		$row_cantidad_salida_guia = $row_salida_guia->Qt_Producto;
		$row_subtotal_salida_guia = $row_salida_guia->Ss_SubTotal;
		
		$Qt_Producto_Prev_Rango_Fecha = (($row_cantidad_entrada + $row_cantidad_entrada_guia) - ($row_cantidad_salida + $row_cantidad_salida_guia));
		$Ss_Importe_Prev_Rango_Fecha = (($row_subtotal_entrada + $row_subtotal_entrada_guia) - ($row_subtotal_salida + $row_subtotal_salida_guia));
		$arrData = array(
			"Qt_Producto_Prev_Rango_Fecha" => $Qt_Producto_Prev_Rango_Fecha,
			"Ss_Costo_Prev_Rango_Fecha" => ($Qt_Producto_Prev_Rango_Fecha > 0 ? ($Ss_Importe_Prev_Rango_Fecha / $Qt_Producto_Prev_Rango_Fecha) : 0.00),
			"Ss_Importe_Prev_Rango_Fecha" => $Ss_Importe_Prev_Rango_Fecha,
		);
		return $arrData;
	}
	

	public function getStockProductoTotal($arrParams){
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision <= '" . $arrParams['Fe_Emision'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_entrada = $this->db->query($query)->row();

		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision <= '" . $arrParams['Fe_Emision'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 0
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_entrada_guia = $this->db->query($query)->row();
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN documento_cabecera AS CVCAB ON(K.ID_Documento_Cabecera = CVCAB.ID_Documento_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision <= '" . $arrParams['Fe_Emision'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_salida = $this->db->query($query)->row();
		
		$query = "SELECT
SUM(K.Qt_Producto) AS Qt_Producto,
SUM(K.Ss_SubTotal) AS Ss_SubTotal
FROM
movimiento_inventario AS K
JOIN guia_cabecera AS CVCAB ON(K.ID_Guia_Cabecera = CVCAB.ID_Guia_Cabecera)
JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
WHERE
K.ID_Almacen = " . $arrParams['ID_Almacen'] . "
AND CVCAB.Fe_Emision <= '" . $arrParams['Fe_Emision'] . "'
AND TMOVI.Nu_Tipo_Movimiento = 1
AND K.ID_Producto = " . $arrParams['ID_Producto'];
		$row_cantidad_salida_guia = $this->db->query($query)->row();
		
		$arrData = array(
			"Qt_Producto_Total_Entrada" => ($row_cantidad_entrada->Qt_Producto + $row_cantidad_entrada_guia->Qt_Producto),
			"Ss_Importe_Total_Entrada" => ($row_cantidad_entrada->Ss_SubTotal + $row_cantidad_entrada_guia->Ss_SubTotal),
			"Qt_Producto_Total_Salida" => ($row_cantidad_salida->Qt_Producto + $row_cantidad_salida_guia->Qt_Producto),
			"Ss_Importe_Total_Salida" => ($row_cantidad_salida->Ss_SubTotal + $row_cantidad_salida_guia->Ss_SubTotal),
		);
		return $arrData;
	}
	
	public function getTiposProducto(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Tipos_Item');
		return $this->db->get('tabla_dato')->result();
	}
	
	public function getTiposExistenciaProducto(){
		$query = "SELECT ID_Tipo_Producto, No_Tipo_Producto FROM tipo_producto ORDER BY Nu_Orden";
		return $this->db->query($query)->result();
	}
	
	public function getRubros(){
		$query = "SELECT
R.ID_Rubro,
R.No_Rubro,
IMPDOC.Ss_Impuesto,
IMP.Nu_Tipo_Impuesto
FROM
rubro AS R
JOIN impuesto AS IMP ON (IMP.ID_Impuesto = R.ID_Impuesto)
JOIN impuesto_cruce_documento AS IMPDOC ON (IMPDOC.ID_Impuesto = IMP.ID_Impuesto)
WHERE
IMPDOC.Nu_Estado = 1
ORDER BY
R.No_Rubro;";
		return $this->db->query($query)->result();
	}
	
	public function getTiposDocumentoIdentidad(){
		$this->db->where('Nu_Estado', 1);
		$this->db->order_by('Nu_Orden');
		return $this->db->get('tipo_documento_identidad')->result();
	}
	
	public function getTiposCliente(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Tipos_Entidad');
		return $this->db->get('tabla_dato')->result();
	}
	
	public function getTiposFormaPago(){
		$this->db->order_by('No_Tipo_Forma_Pago');
		return $this->db->get('tipo_forma_pago')->result();
	}

	public function getImpuestos($arrPost){
		$where_id_empresa = 'IMPDOC.Nu_Estado=1 AND ID_Empresa=' . $this->empresa->ID_Empresa;
		if ( isset( $arrPost['iIdEmpresa'] ) )
			$where_id_empresa = 'ID_Empresa=' . $arrPost['iIdEmpresa'];
		$sql = "SELECT
IMP.ID_Impuesto,
IMPDOC.ID_Impuesto_Cruce_Documento,
IMP.No_Impuesto AS No_Impuesto_,
CONCAT(substring_index(IMP.No_Impuesto, ' ' , 1), ' ', IMPDOC.Po_Impuesto, '% ', (CASE
WHEN IMP.Nu_Tipo_Impuesto=1 THEN 'IGV'
WHEN IMP.Nu_Tipo_Impuesto=3 THEN ''
WHEN IMP.Nu_Tipo_Impuesto=2 THEN ''
ELSE 'Gratuita' END)) AS No_Impuesto,
IMPDOC.Ss_Impuesto,
IMP.Nu_Tipo_Impuesto
FROM
impuesto AS IMP
LEFT JOIN impuesto_cruce_documento AS IMPDOC ON (IMPDOC.ID_Impuesto = IMP.ID_Impuesto)
WHERE
" . $where_id_empresa . "
ORDER BY
IMPDOC.ID_Impuesto_Cruce_Documento ASC;";
		return $this->db->query($sql)->result();
	}
	
	public function getLineas(){
		$this->db->where('Nu_Estado', 1);
		$this->db->order_by('No_Linea');
		return $this->db->get('linea')->result();
	}
	
	public function getMarcas(){
		$this->db->where('Nu_Estado', 1);
		$this->db->where('ID_Empresa', $this->empresa->ID_Empresa);
		$this->db->order_by('No_Marca');
		return $this->db->get('marca')->result();
	}
	
	public function getUnidadesMedida(){
		$query = "
SELECT
 ID_Unidad_Medida,
 No_Unidad_Medida
FROM
 unidad_medida
WHERE
 ID_Empresa=" . $this->user->ID_Empresa . "
 AND Nu_Estado=1
ORDER BY
 Nu_Orden,
 No_Unidad_Medida;";
		return $this->db->query($query)->result();
	}
	
	public function getTipoMovimiento($Nu_Tipo_Movimiento){
		$this->db->order_by('No_Tipo_Movimiento');
		if ($Nu_Tipo_Movimiento!=3)
			$this->db->where('Nu_Tipo_Movimiento' , $Nu_Tipo_Movimiento);
		return $this->db->get('tipo_movimiento')->result();
	}

	public function getTiposDocumentos($Nu_Tipo_Filtro){
		$this->db->order_by('No_Tipo_Documento');
		if ($Nu_Tipo_Filtro=='1' || $Nu_Tipo_Filtro=='3')//Venta
			$this->db->where('Nu_Venta', 1);
		if ($Nu_Tipo_Filtro=='2' || $Nu_Tipo_Filtro=='4')//Compra
			$this->db->where('Nu_Compra', 1);
		if ($Nu_Tipo_Filtro=='1' || $Nu_Tipo_Filtro=='2')//Sunat
			$this->db->where('Nu_Es_Sunat', 1);
		if ($Nu_Tipo_Filtro==5){//Cotización Venta
			//$this->db->where('Nu_Cotizacion_Venta', 1);
			$this->db->where('ID_Tipo_Documento', 1);
		}
		if ($Nu_Tipo_Filtro==6)//Orden Compra
			$this->db->where('Nu_Orden_Compra', 1);
		if ($Nu_Tipo_Filtro==7)//Guia de remisión y documento intero
			$this->db->where_in('ID_Tipo_Documento', array('7','14'));
		if ($Nu_Tipo_Filtro==8)//Solo para la opción series
			$this->db->where_in('ID_Tipo_Documento', array('2','3','4','5','6','7','14'));
		//if ($Nu_Tipo_Filtro==9)//Solo para la opción Compras Detalladas Generales
			//$this->db->where_in('ID_Tipo_Documento', array('2','3','4','5','6','7','8','9','10','11','14'));
		if ($Nu_Tipo_Filtro==9)//Solo para la opción Compras Detalladas Generales
			$this->db->where_in('ID_Tipo_Documento', array('2','3','4','5','6','8','9','10','11'));
		return $this->db->get('tipo_documento')->result();
	}
	
	public function getMediosPago($arrPost){
		$where_id_empresa = 'AND ID_Empresa=' . $this->empresa->ID_Empresa;
		if ( isset( $arrPost['iIdEmpresa'] ) ) {
			$where_id_empresa = 'AND ID_Empresa=' . $arrPost['iIdEmpresa'];
		}
		$query = "SELECT ID_Medio_Pago, No_Medio_Pago, Nu_Tipo, Nu_Tipo_Caja, No_Codigo_Sunat_PLE, Txt_Medio_Pago FROM medio_pago WHERE Nu_Estado = 1 " . $where_id_empresa . " ORDER BY Nu_Orden";
		return $this->db->query($query)->result();
	}
	
	public function getUbicacionesInventario(){
		$this->db->where('Nu_Estado', 1);
		$this->db->order_by('No_Ubicacion_Inventario');
		return $this->db->get('ubicacion_inventario')->result();
	}
	
	public function getDescargarInventario(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Descargar_Inventario');
		return $this->db->get('tabla_dato')->result();
	}

	public function getTiposDocumentosOrden(){
		$this->db->order_by('No_Tipo_Documento');
		$this->db->where('Nu_Orden', 1);
		return $this->db->get('tipo_documento')->result();
	}
	
	public function getTiposOrdenSeguimiento(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Tipos_Orden_Seguimiento');
		return $this->db->get('tabla_dato')->result();
	}
	
	public function getTiposTarjetaCredito($ID_Medio_Pago){
		$query = "SELECT ID_Tipo_Medio_Pago, No_Tipo_Medio_Pago FROM tipo_medio_pago WHERE ID_Medio_Pago=" . $ID_Medio_Pago . " AND Nu_Estado=1 ORDER BY No_Tipo_Medio_Pago";
		return $this->db->query($query)->result();
	}
	
	public function getTipoTiempoRepetir($ID_Medio_Pago){
		$query = "SELECT * FROM tipo_tiempo_repetir ORDER BY ID_Tipo_Tiempo_Repetir";
		return $this->db->query($query)->result();
	}
	
	public function getToken(){
		$query = "SELECT Nu_Tipo_Lenguaje_Impresion_Pos, Nu_ID_Tipo_Documento_Venta_Predeterminado, Nu_Cliente_Varios_Venta_Predeterminado, ID_Entidad_Clientes_Varios_Venta_Predeterminado, Nu_Activar_Detalle_Una_Linea_Ticket, Nu_Precio_Punto_Venta, Nu_Activar_Descuento_Punto_Venta, Txt_Terminos_Condiciones_Ticket, Nu_Verificar_Autorizacion_Venta, Nu_Height_Logo_Ticket, Nu_Width_Logo_Ticket, Nu_Imprimir_Liquidacion_Caja, Nu_Dia_Limite_Fecha_Vencimiento, Nu_Validar_Stock, Nu_Logo_Empresa_Ticket, Nu_Tipo_Rubro_Empresa, Fe_Inicio_Sistema, Txt_Token FROM configuracion WHERE ID_Empresa=" . $this->user->ID_Empresa . " LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getCodigoUnidadMedida(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Unidades_Medida');
		return $this->db->get('tabla_dato')->result();
	}
	
	public function getListaPrecio($iTipoLista, $ID_Organizacion, $ID_Almacen){
		$cond_id_almacen = $ID_Almacen > 0 ? '=' . $ID_Almacen : 'IS NULL';		
		$cond_lista_precio = "";
		if (!empty($iTipoLista))
			$cond_lista_precio = "AND Nu_Tipo_Lista_Precio=" . $iTipoLista;
		$query = "SELECT
ID_Lista_Precio_Cabecera,
No_Lista_Precio
FROM
lista_precio_cabecera
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND ID_Almacen " . $cond_id_almacen . "
AND (No_Lista_Precio != '%web%' OR No_Lista_Precio != '%app%')
" . $cond_lista_precio . "
AND Nu_Estado=1
ORDER BY
No_Lista_Precio";
		return $this->db->query($query)->result();
	}
	
	public function getMotivosTraslado(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Motivo_Traslado');
		return $this->db->get('tabla_dato')->result();
	}

	public function getTipoOperacionCaja($Nu_Tipo){
		$this->db->order_by('ID_Tipo_Operacion_Caja');
		$this->db->where('Nu_Estado', 1);
		$this->db->where('Nu_Tipo', $Nu_Tipo);
		$this->db->where('ID_Empresa', $this->user->ID_Empresa);
		$this->db->where('ID_Organizacion', $this->user->ID_Organizacion);
		return $this->db->get('tipo_operacion_caja')->result();
	}
	
	public function getValidarStock(){
		$query = "SELECT Nu_Validar_Stock FROM configuracion WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getItems($ID_Almacen, $ID_Lista_Precio_Cabecera, $ID_Linea){		
		$sql = "SELECT ID_Familia, No_Familia, No_Html_Color FROM familia WHERE ID_Empresa=" . $this->user->ID_Empresa . " AND Nu_Estado=1 ORDER BY Nu_Orden, No_Familia DESC";
		$arrData['arrAllCategorie'] = $this->db->query($sql)->result();

		if ( $ID_Linea != 'top_sale' && $ID_Linea != 'favorito' ) {
			$cond_Linea = (!empty($ID_Linea) ? "AND PRO.ID_Familia = " . $ID_Linea : "" );
			$sql = "SELECT
PRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
PRO.Txt_Producto,
PRO.Ss_Precio,
STOCK.Qt_Producto,
LPD.Ss_Precio AS Ss_Precio_Lista,
PRO.No_Imagen_Item,
PRO.Nu_Tipo_Producto,
FAMI.No_Html_Color
FROM
producto AS PRO
JOIN familia AS FAMI ON(PRO.ID_Familia = FAMI.ID_Familia)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $ID_Lista_Precio_Cabecera . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE PRO.ID_Empresa=" . $this->user->ID_Empresa . "
" . $cond_Linea . "
AND PRO.Nu_Estado=1
AND PRO.Nu_Tipo_Producto!=2
ORDER BY
No_Producto ASC";
			$arrData['arrAllItemsCategorie'] = $this->db->query($sql)->result();
			$arrData['message'] = 'No hay ítems. Para agregar dar clic en el botón azul <b>Crear</b> o ir a <b>Compras y Productos > Reglas de Productos >  <a target="_blank" title="Producto" href="' . base_url() . 'Logistica/ReglasLogistica/ProductoController/listarProductos">Producto</a></b>';
		} else if ( $ID_Linea == 'favorito' ) {
			$sql = "SELECT
PRO.ID_Producto,
PRO.Nu_Tipo_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
PRO.Txt_Producto,
PRO.Ss_Precio,
STOCK.Qt_Producto,
LPD.Ss_Precio AS Ss_Precio_Lista,
PRO.No_Imagen_Item,
FAMI.No_Html_Color
FROM
producto AS PRO
JOIN familia AS FAMI ON(PRO.ID_Familia = FAMI.ID_Familia)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = PRO.ID_Producto)
LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $ID_Lista_Precio_Cabecera . " AND LPD.ID_Producto = PRO.ID_Producto)
WHERE PRO.ID_Empresa=" . $this->user->ID_Empresa . "
AND PRO.Nu_Estado=1
AND PRO.Nu_Favorito=1
AND PRO.Nu_Tipo_Producto!=2
ORDER BY
No_Producto ASC";
			$arrData['arrAllItemsCategorie'] = $this->db->query($sql)->result();
			$arrData['message'] = 'Para agregar <b>Favorito</b> ir a <b>Compras y Productos > Reglas de Productos > <a target="_blank" title="Producto" href="' . base_url() . 'Logistica/ReglasLogistica/ProductoController/listarProductos">Producto</a></b>, modifcar campo <b>Favorito = Si.</b>';
		}
		  
		return $arrData;
	}
	
	public function getUltimoCierre(){
		$query = "SELECT
CPOS.ID_Matricula_Empleado,
CPOS.ID_Caja_Pos,
CPOS.Fe_Movimiento,
MEMPLE.ID_Empleado
FROM
caja_pos AS CPOS
JOIN matricula_empleado AS MEMPLE ON(CPOS.ID_Matricula_Empleado = MEMPLE.ID_Matricula_Empleado)
WHERE
CPOS.Nu_Estado = 0
LIMIT 1;";
		return $this->db->query($query)->row();
	}
	
	public function getDataGeneral($arrPost){
		if ( $arrPost['sTipoData'] == 'item' ) {
			$column_lista_precio = "";
			$left_join_stock_producto = "LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen." AND STOCK.ID_Producto = ITEM.ID_Producto)";
			$left_join_lista_precio_detalle = '';
			if ( isset($arrPost['iIdListaPrecioCabecera']) && $arrPost['iIdListaPrecioCabecera']!='0' ) {
				$column_lista_precio = "LPD.Ss_Precio_Interno, LPD.Po_Descuento, LPD.Ss_Precio,";
				//$left_join_stock_producto = "LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Empresa = " . $this->empresa->ID_Empresa . " AND STOCK.ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND STOCK.ID_Producto = ITEM.ID_Producto)";
				$left_join_lista_precio_detalle = "LEFT JOIN lista_precio_detalle AS LPD ON(LPD.ID_Lista_Precio_Cabecera = " . $arrPost['iIdListaPrecioCabecera'] . " AND LPD.ID_Producto = ITEM.ID_Producto)";
			}
			$query = "SELECT
ITEM.*,
ITEM.ID_Producto AS ID,
ITEM.No_Producto AS Nombre,
" . $column_lista_precio . "
ROUND(STOCK.Qt_Producto, 0) AS Qt_Producto,
ITEM.Ss_Precio as Ss_Precio_Item,
ITEM.Ss_Costo as Ss_Costo_Item,
F.No_Familia,
UM.No_Unidad_Medida,
M.No_Marca,
IMP.No_Impuesto_Breve,
SF.No_Sub_Familia
FROM
producto AS ITEM
JOIN familia AS F ON(F.ID_Familia = ITEM.ID_Familia)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = ITEM.ID_Unidad_Medida)
LEFT JOIN subfamilia AS SF ON(SF.ID_Sub_Familia = ITEM.ID_Sub_Familia)
LEFT JOIN marca AS M ON(M.ID_Marca = ITEM.ID_Marca)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ITEM.ID_Impuesto)
" . $left_join_stock_producto . "
" . $left_join_lista_precio_detalle . "
WHERE
ITEM.ID_Producto=" . $arrPost['iIdItem'] . " LIMIT 1";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - item',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - item',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'categoria' ) {
			$where_id_empresa='';
			if ( isset($arrPost['iIdEmpresa']) ) {
				$where_id_empresa='AND ID_Empresa=' . $arrPost['iIdEmpresa'];
			}
			$query = "SELECT ID_Familia AS ID, No_Familia AS Nombre FROM familia WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 ORDER BY No_Familia";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - Categorías',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - Categorías',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'subcategoria' ) {
			$cond_id_categoria='';
			if ( isset($arrPost['sWhereIdCategoria']) && !empty($arrPost['sWhereIdCategoria']) ) {
				$cond_id_categoria='AND ID_Familia=' . $arrPost['sWhereIdCategoria'];
			}
			$query = "SELECT ID_Sub_Familia AS ID, No_Sub_Familia AS Nombre FROM subfamilia WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 " . $cond_id_categoria . " ORDER BY No_Sub_Familia";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - Sub categorías',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
					'sql' => $query,
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - Sub categorías',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'laboratorio' ) {
			$query = "SELECT ID_Laboratorio AS ID, No_Laboratorio AS Nombre FROM laboratorio WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 ORDER BY No_Laboratorio";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - laboratorio',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - laboratorio',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'composicion' ) {
			$cond_id_composicion='';
			if ( isset($arrPost['sWhereIdComposicion']) && !empty($arrPost['sWhereIdComposicion']) ) {
				$cond_id_composicion='AND ID_Composicion IN(' . $arrPost['sWhereIdComposicion'] . ')';
			}
			$query = "SELECT ID_Composicion AS ID, No_Composicion AS Nombre FROM composicion WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 " . $cond_id_composicion . " ORDER BY No_Composicion";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - composiciones',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - composiciones',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'entidad' ) {
			$query = "SELECT ID_Entidad AS ID, No_Entidad AS Nombre FROM entidad WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 AND Nu_Tipo_Entidad = " . $arrPost['iTipoEntidad'] . " ORDER BY No_Entidad";
			
			$sNombreEntidad = 'Clientes';
			if ( $arrPost['iTipoEntidad'] == 1 ) {
				$sNombreEntidad = 'Proveedores';
			} else if ( $arrPost['iTipoEntidad'] == 4 ) {
				$sNombreEntidad = 'Empleados';
			} else if ( $arrPost['iTipoEntidad'] == 6 ) {
				$sNombreEntidad = 'Delivery';
			}

			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - ' . $sNombreEntidad,
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - ' . $sNombreEntidad,
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'get_entidad' ) {
			$where_id_entidad = 'AND ID_Entidad = ' . $arrPost['iIDEntidad'];
			$where_nombre_entidad = '';
			if ( !empty($arrPost['sNombreEntidad']) ) {
				$where_id_entidad = '';
				$where_nombre_entidad = "AND No_Entidad LIKE '%" . $arrPost['sNombreEntidad'] . "%'";			
			}
			$query = "SELECT Nu_Estado, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Tipo_Entidad = " . $arrPost['iTipoEntidad'] . " " . $where_id_entidad . $where_nombre_entidad . " AND Nu_Estado=1 LIMIT 1";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrado',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontro registro',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'Porcentaje_Comision_Vendedores' ) {
			$query = "SELECT * FROM tabla_dato WHERE No_Relacion='" . $arrPost['sTipoData'] . "'";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrado',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontro registro',
				'sClassModal' => 'modal-warning',
			);
		} else if ( $arrPost['sTipoData'] == 'movimiento_caja_pv' ) {
			$query = "SELECT ID_Tipo_Operacion_Caja AS ID, No_Tipo_Operacion_Caja AS Nombre FROM tipo_operacion_caja WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Tipo IN(3,4,5,6) AND ID_Organizacion = ".$this->empresa->ID_Organizacion." AND Nu_Estado=1 ORDER BY No_Tipo_Operacion_Caja";
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos - reporte movimiento de caja',
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				return array(
					'sStatus' => 'success',
					'sMessage' => 'Registros encontrados',
					'sClassModal' => 'modal-success',
					'arrData' => $arrResponseSQL->result(),
				);
			}
			
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontraron registros - reporte movimiento de caja',
				'sClassModal' => 'modal-warning',
			);
		}
	}
	
	public function getPosConfiguracionxSerie($arrPost){
		$where_id_empresa = 'AND ID_Empresa=' . $this->empresa->ID_Empresa;
		$where_id_organizacion = 'AND ID_Organizacion=' . $this->empresa->ID_Organizacion;
		if ( isset( $arrPost['iIdEmpresa'] ) ) {
			$where_id_empresa = 'AND ID_Empresa=' . $arrPost['iIdEmpresa'];
		}
		if ( isset( $arrPost['iIdOrganizacion'] ) ) {
			$where_id_organizacion = 'AND ID_Organizacion=' . $arrPost['iIdOrganizacion'];
		}
		$query = "SELECT ID_POS, Nu_Pos FROM pos WHERE Nu_Estado>0 " . $where_id_empresa . " " . $where_id_organizacion . " ORDER BY ID_POS";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - POS',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}		
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros - POS',
		);
	}

	public function getPos(){
		$query = "SELECT ID_POS, Nu_Pos, No_Pos, Txt_Autorizacion_Venta_Serie_Disco_Duro FROM pos WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND Nu_Estado>0 ORDER BY ID_POS";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - POS',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataPos=$arrResponseSQL->result();
			$arrDataMatriculaPersonal=array();
			$arrDataSaldoPos=array();
			foreach( $arrDataPos as $row ){
				$arrDataMatriculaPersonal[$row->ID_POS][]=$this->getUltimaMatriculaPersonalxPos($row->ID_POS);
			}
			foreach( $arrDataPos as $row ){
				$arrDataSaldoPos[$row->ID_POS][]=$this->getUltimoCierrexPos($row->ID_POS);
			}
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrDataPos,
				'arrDataMatriculaPersonal' => $arrDataMatriculaPersonal,
				'arrDataSaldoPos' => $arrDataSaldoPos,
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros - POS',
		);
	}

	public function getUltimaMatriculaPersonalxPos($iIDPos){
		$query = "SELECT
ME.ID_Matricula_Empleado,
ME.ID_Entidad,
TRA.No_Entidad,
ME.Fe_Matricula
FROM
matricula_empleado AS ME
LEFT JOIN entidad AS TRA ON(TRA.ID_Entidad=ME.ID_Entidad)
WHERE
ME.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND ME.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND ME.ID_Pos=".$iIDPos."
ORDER BY
ME.Fe_Matricula DESC
LIMIT 1";

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - Matricula de personal x pos',
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
			'sMessage' => 'No se encontraron registros - Matricula de personal x pos',
		);
	}

	public function getUltimoCierrexPos($iIDPos){
		$query = "SELECT
CP.ID_Matricula_Empleado,
MONE.No_Signo,
CP.Ss_Total,
CP.Fe_Movimiento,
TOC.No_Tipo_Operacion_Caja,
CASE WHEN TOC.Nu_Tipo=3 THEN 'success' ELSE 'danger' END AS No_Class_Estado,
CP.ID_Moneda AS ID_Moneda_Caja_Pos
FROM
caja_pos AS CP
JOIN tipo_operacion_caja AS TOC ON(TOC.ID_Tipo_Operacion_Caja=CP.ID_Tipo_Operacion_Caja)
JOIN moneda AS MONE ON(MONE.ID_Moneda=CP.ID_Moneda)
WHERE
CP.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CP.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND CP.ID_POS=".$iIDPos."
AND TOC.Nu_Tipo IN(3, 4)
ORDER BY
CP.Fe_Movimiento DESC
LIMIT 1";
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - Ultimo cierre x pos',
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
			'sMessage' => 'No se encontraron registros - Ultimo cierre x pos',
		);
	}

	public function getPersonal($arrPost){
		$cond_where_nu_documento_identidad="";
		$cond_where_nu_pin="";
		if ( isset($arrPost['iNumeroDocumentoIdentidad']) ) {
			$cond_where_nu_documento_identidad="AND Nu_Documento_Identidad=".$arrPost['iNumeroDocumentoIdentidad'];
			$sMessage = 'No se encontraron registros - Personal';
		}
		if ( isset($arrPost['iPin']) ) {
			$cond_where_nu_pin="AND Nu_Pin_Caja=".$arrPost['iPin'];
			$sMessage = 'No se encontro PIN. Verificar en Personal > Maestro personal si fue creado.';
		}
		$query = "SELECT * FROM entidad WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND Nu_Tipo_Entidad=4 " . $cond_where_nu_documento_identidad . " " . $cond_where_nu_pin . " LIMIT 1";
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - Personal',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
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
			'sMessage' => $sMessage,
		);
	}

	public function getMatriculaPersonal($ID_Matricula_Empleado, $iIdMoneda){
		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $ID_Matricula_Empleado . " LIMIT 1")->row();

		if ($objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);
		
		$query = "SELECT
ME.ID_Almacen,
ALMA.No_Almacen,
ME.ID_Matricula_Empleado,
ME.ID_Entidad,
TRA.No_Entidad,
ME.Fe_Matricula,
ME.ID_POS,
POS.Nu_Pos AS Nu_Caja,
TRA.Nu_Pin_Caja
FROM
matricula_empleado AS ME
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen)
JOIN entidad AS TRA ON(TRA.ID_Entidad=ME.ID_Entidad)
JOIN pos AS POS ON(POS.ID_POS = ME.ID_POS)
WHERE
ME.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND ME.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND ME.ID_Matricula_Empleado=".$ID_Matricula_Empleado."
ORDER BY
ME.Fe_Matricula DESC
LIMIT 1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - Matricula de personal x ID',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrMoneda = $this->db->query("SELECT ID_Moneda, No_Signo, Nu_Sunat_Codigo AS Nu_Codigo_Moneda_Sunat, No_Moneda FROM moneda WHERE ID_Moneda = " . $iIdMoneda . " LIMIT 1")->result();
			$arrMoneda = array('ID_Moneda' => $arrMoneda[0]->ID_Moneda, 'No_Signo' => $arrMoneda[0]->No_Signo, 'Nu_Codigo_Moneda_Sunat' => $arrMoneda[0]->Nu_Codigo_Moneda_Sunat, 'No_Moneda' => $arrMoneda[0]->No_Moneda);
			$arrData = array_merge( $arrResponseSQL->result(), $arrMoneda );
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'arrData' => $arrData,
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros - Matricula de personal x ID',
		);
	}

	public function validacionStockMinimo($arrPost){
		$iIdItem = $arrPost['iIdItem'];
		$fCantidadItem = $arrPost['fCantidadItem'];
		$query = "SELECT Nu_Stock_Minimo FROM producto WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Producto = " . $iIdItem . " AND Nu_Stock_Minimo >= " . $fCantidadItem . " LIMIT 1";
		
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
				'sMessage' => 'Stock Mínimo',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'Item no supera el stock minimo',
		);
	}

	public function validacionVentaRecetaMedica($arrPost){
		$iIdItem = $arrPost['iIdItem'];
		$query = "SELECT Nu_Receta_Medica FROM producto WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Producto = " . $iIdItem . " AND Nu_Receta_Medica=1 LIMIT 1";
		
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
				'sMessage' => 'Venta solo con <b>Receta Médica</b>',
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'Sin receta medica',
		);
	}

	public function validacionLoteVencimiento($arrPost){
		$iIdAlmacen = $arrPost['iIdAlmacen'];
		$iIdItem = $arrPost['iIdItem'];
		$query = "SELECT Nu_Lote_Vencimiento, Fe_Lote_Vencimiento FROM documento_detalle_lote WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND ID_Almacen = " . $iIdAlmacen . " AND ID_Producto = " . $iIdItem . " ORDER BY Fe_Lote_Vencimiento DESC LIMIT 1";
		
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
			$dLoteVencimiento = strtotime( '-' . $this->empresa->Nu_Dia_Limite_Fecha_Vencimiento . ' day', strtotime( $arrData[0]->Fe_Lote_Vencimiento ) );
			$dLoteVencimiento = date( 'Y-m-j', $dLoteVencimiento );
			$arrFechaLoteVencimiento = explode('-', $dLoteVencimiento);//Y-M-D

			$iDay = (strlen($arrFechaLoteVencimiento[2]) > 1 ? $arrFechaLoteVencimiento[2] : '0' . $arrFechaLoteVencimiento[2]);

			$dLoteVencimiento = $arrFechaLoteVencimiento[0] . '-' . $arrFechaLoteVencimiento[1] . '-' . $iDay;

			return array(
				'sStatus' => 'success',
				'sMessage' => 'Lote Vencimiento',
				'Nu_Lote_Vencimiento' => $arrData[0]->Nu_Lote_Vencimiento,
				'dToday' => dateNow('fecha'),
				'dLoteVencimientoOperacion' => $dLoteVencimiento,
				'dLoteVencimiento' => $arrData[0]->Fe_Lote_Vencimiento,
				'iDiasLoteVencimiento' => $this->empresa->Nu_Dia_Limite_Fecha_Vencimiento,
				'arrFechaLoteVencimiento' => $arrFechaLoteVencimiento,
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No hay lote de vencimiento'
		);
	}
	
	public function validateStockNow($arrPost){
		$iIdAlmacen = $arrPost['iIdAlmacen'];
		$iIdItem = $arrPost['iIdItem'];
		$query = "SELECT Qt_Producto FROM stock_producto WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Organizacion=" . $this->empresa->ID_Organizacion . " AND ID_Almacen = " . $iIdAlmacen . " AND ID_Producto=" . $iIdItem . " LIMIT 1";
		return $this->db->query($query)->row();
	}

    public function getStockXEnlaceItem($arrPost){
		$iIdAlmacen = $arrPost['iIdAlmacen'];
		$iIdItem = $arrPost['iIdItem'];
        $query = "SELECT
ENLAPRO.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
STOCK.Qt_Producto
FROM
enlace_producto AS ENLAPRO
JOIN producto AS PRO ON(PRO.ID_Producto = ENLAPRO.ID_Producto)
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Almacen = " . $iIdAlmacen." AND STOCK.ID_Producto = ENLAPRO.ID_Producto)
WHERE
ENLAPRO.ID_Producto_Enlace = " . $iIdItem;
        return $this->db->query($query)->result();
    }

    public function getStockXItem($arrPost){
		$iIdAlmacen = $arrPost['ID_Almacen'];
		$iIdItem = $arrPost['ID_Producto'];
        $query = "SELECT
PRO.No_Producto,
STOCK.Qt_Producto
FROM
producto AS PRO
LEFT JOIN stock_producto AS STOCK ON(STOCK.ID_Almacen = " . $iIdAlmacen." AND STOCK.ID_Producto = PRO.ID_Producto)
WHERE
PRO.ID_Producto = " . $iIdItem;
        return $this->db->query($query)->row();
    }

	public function getValoresTablaDato($arrPost){
		$query = "SELECT * FROM tabla_dato WHERE No_Relacion='" . $arrPost['sTipoData'] . "'";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - ' . $arrPost['sTipoData'],
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro - ' . $arrPost['sTipoData'],
			'sClassModal' => 'modal-warning',
		);
	}

	public function connectToMysqlLocalhost($arrPost){
		$query = "SELECT Txt_Autorizacion_Venta_Localhost_Database, Txt_Autorizacion_Venta_Localhost_Hostname, Txt_Autorizacion_Venta_Localhost_User, Txt_Autorizacion_Venta_Localhost_Password FROM organizacion WHERE ID_Empresa=" . $this->user->ID_Empresa . " AND ID_Organizacion=" . $this->user->ID_Organizacion;
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener credenciales localhost',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrDataCredencialesMysqlLocalhost = $arrResponseSQL->result();
			
			$dbs = 'mysql:dbname=' . $arrDataCredencialesMysqlLocalhost[0]->Txt_Autorizacion_Venta_Localhost_Database . ';host=' . $arrDataCredencialesMysqlLocalhost[0]->Txt_Autorizacion_Venta_Localhost_Hostname;
			$user = $arrDataCredencialesMysqlLocalhost[0]->Txt_Autorizacion_Venta_Localhost_User;
			$password = $arrDataCredencialesMysqlLocalhost[0]->Txt_Autorizacion_Venta_Localhost_Password;

			try {
				$conexion = new PDO($dbs, $user, $password);      
				$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$arrData = $conexion->query("SELECT ID_POS, Txt_Key_Serie_HDD FROM punto_autorizado LIMIT 1");
				$sKeySerieHDD = '';
				foreach($arrData as $row)
					$sKeySerieHDD = $row['Txt_Key_Serie_HDD'];
				
				if ( !empty($sKeySerieHDD) ) {
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Conexión realizada satisfactoriamente',
						'sKeySerieHDD' => $sKeySerieHDD,
					);
				} else {
					return array(
						'sStatus' => 'warning',
						'sMessage' => 'No se encontro key serie HDD',
					);
				}
			} catch(PDOException $e) {
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'No hay conexión',
					'sMessageSQL' => 'La conexión ha fallado: ' . $e->getMessage(),
				);
			}
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro de credenciales',
		);
	}
	
	public function validationKeySerieHDD($arrPost){
		$where_key_serie_hdd = ( isset($arrPost['sKeySerieHDD']) ? "AND Txt_Autorizacion_Venta_Serie_Disco_Duro = '" . $arrPost['sKeySerieHDD'] . "' LIMIT 1" : '' );
		$query = "SELECT Txt_Autorizacion_Venta_Serie_Disco_Duro FROM pos WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND Nu_Estado>0 " . $where_key_serie_hdd;
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - POS',
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
			'sMessage' => 'No se encontraron registros - POS',
		);
	}
	
	public function getDocumentoDetalle($arrPost){
		$query = "SELECT VD.ID_Producto, VD.Qt_Producto, ITEM.No_Producto, VD.Txt_Nota AS Txt_Nota_Item FROM documento_detalle AS VD JOIN producto AS ITEM ON(ITEM.ID_Producto = VD.ID_Producto) WHERE ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'] . " AND ID_Documento_Detalle = " . $arrPost['iIdDocumentoDetalle'];
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
	
	public function getDocumentoDetalleEstadoLavado($arrPost){
		/*DDEL.*,*/
		$query = "SELECT DISTINCT
DDEL.Txt_Final_Prelavado,
DDEL.Txt_Final_Lavado_Seco,
DDEL.Txt_Planchado,
DDEL.Txt_Doblado,
DDEL.Txt_Embolsado,
VD.Qt_Producto,
ITEMPELAVA.No_Descripcion AS No_Tipo_Pedido_Lavado,
ITEM.No_Producto,
TDESTADOLAVADETALLE.No_Descripcion AS No_Estado_Lavado_Detalle,
TDESTADOLAVADETALLE.No_Class AS No_Class_Estado_Lavado_Detalle,
LI.No_Entidad AS No_Entidad_Lavado_Iniciado,
LF.No_Entidad AS No_Entidad_Lavado_Finalizado,
LP.No_Entidad AS No_Entidad_Lavado_Planchado,
LD.No_Entidad AS No_Entidad_Lavado_Doblado,
LE.No_Entidad AS No_Entidad_Lavado_Embolsado,
TRANSPT.No_Entidad AS No_Entidad_Transporte,
TIPOENVIO.No_Descripcion AS No_Tipo_Envio_Transporte_Detalle,
TIPOENVIO.No_Class AS No_Class_Tipo_Envio_Transporte_Detalle
FROM
documento_detalle AS VD
JOIN documento_cabecera AS VC ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN tabla_dato AS TIPOENVIO ON(TIPOENVIO.Nu_Valor = VC.Nu_Transporte_Lavanderia_Hoy AND TIPOENVIO.No_Relacion = 'Tipos_EstadoEnvioPedidoLavado')
LEFT JOIN entidad AS TRANSPT ON(TRANSPT.ID_Entidad = VC.ID_Transporte_Sede_Planta)
LEFT JOIN documento_detalle_estado_lavado AS DDEL ON(DDEL.ID_Documento_Cabecera = VD.ID_Documento_Cabecera AND DDEL.ID_Documento_Detalle = VD.ID_Documento_Detalle)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
JOIN tabla_dato AS ITEMPELAVA ON(ITEMPELAVA.Nu_Valor = ITEM.ID_Tipo_Pedido_Lavado AND ITEMPELAVA.No_Relacion = 'Tipos_PedidoLavado')
LEFT JOIN tabla_dato AS TDESTADOLAVADETALLE ON(TDESTADOLAVADETALLE.Nu_Valor = DDEL.Nu_Estado_Lavado AND TDESTADOLAVADETALLE.No_Relacion = 'Tipos_EstadoLavado')
LEFT JOIN entidad AS LI ON(LI.ID_Entidad = DDEL.ID_Entidad_Lavado_Iniciado)
LEFT JOIN entidad AS LF ON(LF.ID_Entidad = DDEL.ID_Entidad_Lavado_Finalizado)
LEFT JOIN entidad AS LP ON(LP.ID_Entidad = DDEL.ID_Entidad_Lavado_Planchado)
LEFT JOIN entidad AS LD ON(LD.ID_Entidad = DDEL.ID_Entidad_Lavado_Doblado)
LEFT JOIN entidad AS LE ON(LE.ID_Entidad = DDEL.ID_Entidad_Lavado_Embolsado)
WHERE
VD.ID_Documento_Cabecera = " . $arrPost['iIdDocumentoCabecera'];
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

	public function getDocumentoDetalleEstadoLavadoxDocumentoDetalle($arrPost){
		$query = "SELECT * FROM documento_detalle_estado_lavado WHERE ID_Documento_Detalle = " . $arrPost['iIdDocumentoDetalle'];
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

	public function cobranzaClientePuntoVenta($arrPost){
        $this->db->trans_begin();

        $data = array( 'Nu_Estado_Lavado_Recepcion_Cliente' => $arrPost['iEstadoLavadoRecepcionCliente'] );
        if ( isset($arrPost['iCrearEntidad']) && $arrPost['iCrearEntidad'] == 0 ) {// 0 Crear entidad recepcion lavado cliente = 9
            $arrEntidadLavado = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Organizacion' => $this->empresa->ID_Organizacion,
                'Nu_Tipo_Entidad' => 9,
                'ID_Tipo_Documento_Identidad' => 1,
                'No_Entidad' => $arrPost['sNombreRecepcion'],
            );
            $this->db->insert('entidad', $arrEntidadLavado);
            $iIdEntidad = $this->db->insert_id();
            $data = array( 'Nu_Estado_Lavado_Recepcion_Cliente' => $arrPost['iEstadoLavadoRecepcionCliente'], 'ID_Mesero' => $iIdEntidad );
        }

        if ( !empty($arrPost['fPagoCliente']) ) {
            $documento_medio_pago = array(
                'ID_Empresa'			=> $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera'	=> $arrPost['iIdDocumentoCabecera'],
                'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
                'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
                'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
                'Ss_Total'		        => $arrPost['fPagoCliente'],
                'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
				'ID_Documento_Medio_Pago_Enlace' => 1,
				'Fe_Emision_Hora_Pago' => (isset($arrPost['Fe_Emision_Hora_Pago']) ? ToDate($arrPost['Fe_Emision_Hora_Pago']) . ' ' . dateNow('hora') : dateNow('fecha_hora')),
				'ID_Matricula_Empleado' => (isset($this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado) ? $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado : 0),
            );
            $this->db->insert('documento_medio_pago', $documento_medio_pago);
			
			//PAGAR DETRACCION
			if(isset($arrPost['iCobrarModalDetraccion']) && $arrPost['iCobrarModalDetraccion']==0) {
            	$data = array_merge( $data, array( 'Ss_Total_Saldo' => $arrPost['fSaldoCliente'] - $arrPost['fPagoCliente'] ) );
			}
        }
        
        $where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
        $this->db->update('documento_cabecera', $data, $where);

		//PAGAR DETRACCION
		if(isset($arrPost['iCobrarModalDetraccion']) && $arrPost['iCobrarModalDetraccion']==1) {
			$where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
			$data = array('Ss_Detraccion' => 0.00, 'Fe_Detraccion' => (isset($arrPost['Fe_Emision_Hora_Pago']) ? ToDate($arrPost['Fe_Emision_Hora_Pago']) : dateNow('fecha')));
			$this->db->update('documento_cabecera', $data, $where);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al procesar pago del cliente');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Se agrego pago de cliente');
        }
	}

	public function cobranzaProveedorPuntoVenta($arrPost){
        $this->db->trans_begin();

        if ( !empty($arrPost['fPagoProveedor']) ) {
            $documento_medio_pago = array(
                'ID_Empresa'			=> $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera'	=> $arrPost['iIdDocumentoCabecera'],
                'ID_Medio_Pago'		    => $arrPost['iFormaPago'],
                'Nu_Transaccion'		=> $arrPost['iNumeroTransaccion'],
                'Nu_Tarjeta'		    => $arrPost['iNumeroTarjeta'],
                'Ss_Total'		        => $arrPost['fPagoProveedor'],
                'ID_Tipo_Medio_Pago' => isset($arrPost['iTipoMedioPago']) ? $arrPost['iTipoMedioPago'] : 0,
				'Fe_Emision_Hora_Pago' => ( isset($arrPost['Fe_Emision_Hora_Pago']) ? ToDate($arrPost['Fe_Emision_Hora_Pago']) : dateNow('fecha_hora')),
				'ID_Matricula_Empleado' => 0,
				'ID_Documento_Medio_Pago_Enlace' => 1//se utiliza para cierre de caja de punto de venta
            );
            $this->db->insert('documento_medio_pago', $documento_medio_pago);
            $data = array( 'Ss_Total_Saldo' => ($arrPost['fSaldoProveedor'] - $arrPost['fPagoProveedor']) );
        }
        
        $where = array( 'ID_Documento_Cabecera' => $arrPost['iIdDocumentoCabecera'] );
        $this->db->update('documento_cabecera', $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al procesar pago del proveedor');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Se agrego pago de proveedor');
        }
	}
	
	public function getEmpresasOpcionesMenu(){
		$query = "SELECT EMP.ID_Empresa, EMP.No_Empresa, EMP.Nu_Proveedor_Dropshipping, CONF.ID_Configuracion, CONF.Nu_Tipo_Rubro_Empresa FROM empresa AS EMP JOIN configuracion AS CONF ON(EMP.ID_Empresa = CONF.ID_Empresa) WHERE EMP.Nu_Estado = 1 ORDER BY EMP.No_Empresa;";
		return $this->db->query($query)->result();
	}

	public function getEmpresasMarketplace($arrPost){
		$query = "SELECT * FROM empresa AS EMP JOIN tabla_dato AS TIPOECOMMERCE ON(TIPOECOMMERCE.ID_Tabla_Dato=EMP.Nu_Tipo_Ecommerce_Empresa AND TIPOECOMMERCE.No_Relacion = 'Tipos_Ecommerce_Empresa') WHERE TIPOECOMMERCE.Nu_Valor = 1 ORDER BY No_Empresa;";
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

	public function getCategoriasMarketplace(){
		$query = "SELECT * FROM familia WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace;
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

	public function getSubCategoriasMarketplace($arrPost){
		$query = "SELECT * FROM subfamilia WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace . " AND ID_Familia = " . $arrPost['iIdFamilia'];
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

	public function getMarcasMarketplace(){
		$query = "SELECT * FROM marca WHERE ID_Empresa = " . $this->empresa->ID_Empresa_Marketplace;
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
	
	public function getMediosPagoMarketplace($arrPost){
		$where_id_empresa = 'AND ID_Empresa=' . $this->empresa->ID_Empresa;
		if ( isset( $arrPost['iIdEmpresa'] ) ) {
			$where_id_empresa = 'AND ID_Empresa=' . $arrPost['iIdEmpresa'];
		}
		$query = "SELECT * FROM medio_pago_marketplace WHERE Nu_Estado = 1 " . $where_id_empresa . " ORDER BY Nu_Orden";
		return $this->db->query($query)->result();
	}
	
	public function getOrganizacionesAlcenesEmpresaExternos($arrPost){
		$query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
ALMA.Txt_Direccion_Almacen
FROM
almacen AS ALMA
JOIN organizacion AS ORG ON(ALMA.ID_Organizacion = ORG.ID_Organizacion)
JOIN empresa AS EMP ON(ORG.ID_Empresa = EMP.ID_Empresa)
WHERE
EMP.ID_Empresa=".$this->empresa->ID_Empresa."
AND ALMA.ID_Almacen != " . $arrPost['iIdAlmacen'] . "
AND ALMA.Nu_Estado = 1
ORDER BY
ALMA.No_Almacen";
		
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

	public function getDocumentoEnlace($arrParams){
		$query = "SELECT TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento AS _ID_Serie_Documento, SD.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Txt_Url_PDF FROM
documento_enlace AS VE
JOIN documento_cabecera AS VC ON(VE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE VE.ID_Documento_Cabecera_Enlace = " . $arrParams['ID_Documento_Cabecera'];
		
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

	public function getDocumentoEnlaceOrigen($arrParams){
		$query = "SELECT TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento AS _ID_Serie_Documento, SD.ID_Serie_Documento, COALESCE(VC.ID_Numero_Documento,VC.ID_Documento_Cabecera) AS ID_Numero_Documento FROM
documento_enlace AS VE
JOIN documento_cabecera AS VC ON(VE.ID_Documento_Cabecera_Enlace = VC.ID_Documento_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE VE.ID_Documento_Cabecera = " . $arrParams['ID_Documento_Cabecera'];
		
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

	public function getGuianEnlace($arrParams){
		$query = "SELECT TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento AS _ID_Serie_Documento, SD.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Txt_Url_PDF FROM
guia_enlace AS VE
JOIN guia_cabecera AS VC ON(VE.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE VE.ID_Documento_Cabecera = " . $arrParams['ID_Documento_Cabecera'];
		
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

	public function getGuianEnlaceOrigen($arrParams){
		$query = "SELECT VC.ID_Tipo_Documento, VC.ID_Documento_Cabecera, TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento AS _ID_Serie_Documento, SD.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Txt_Url_PDF FROM
guia_enlace AS VE
JOIN documento_cabecera AS VC ON(VE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
LEFT JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE VE.ID_Guia_Cabecera = " . $arrParams['ID_Guia_Cabecera'];
		
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

	public function getEntidad($arrParams){
		$query = "SELECT ID_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Txt_Direccion_Entidad, ID_Departamento, ID_Provincia, ID_Distrito FROM entidad WHERE ID_Entidad = " . $arrParams['ID_Entidad'] . " LIMIT 1";
		
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
	
	public function getTipoRecepcionNegocio(){
		$this->db->order_by('Nu_Valor');
		$this->db->where('No_Relacion', 'Tipos_Recepcion');
		$this->db->where_in('Nu_Valor', array(5,6,7));
		return $this->db->get('tabla_dato')->result();
	}

    public function reloadUpdateUsuario($arrPost){
		$Nu_Setting_Panel_Menu_Izquierdo = ($arrPost['Nu_Setting_Panel_Menu_Izquierdo'] == 0 ? 1 : 0);
		$data = array('Nu_Setting_Panel_Menu_Izquierdo' => $Nu_Setting_Panel_Menu_Izquierdo);
		$where = array('ID_Usuario' => $this->session->usuario->ID_Usuario);
		if ( $this->db->update('usuario', $data, $where) > 0 ) {
			$this->session->usuario->Nu_Setting_Panel_Menu_Izquierdo = $Nu_Setting_Panel_Menu_Izquierdo;
			return array('status' => 'success', 'message' => 'Panel modificado', 'router' => $this->router);
		}
        return array('status' => 'error', 'message' => 'Error al modificar panel');
    }
	
	public function getAlmacenesSession($arrPost){
		// Eliminamos session
		$this->session->unset_userdata('almacen');

		// Volvemos a crear session
		$objAlmacen = new stdClass();
		$objAlmacen->ID_Almacen = $arrPost['iIdAlmacen'];
		$this->session->set_userdata('almacen', $objAlmacen);
		
		return array('status'=>'success', 'message' => 'Cambiando almacen');
	}
	
	public function getOrganizacionxUsuarioSessionEmpresa(){
		$query = "SELECT * FROM organizacion WHERE ID_Organizacion IN(SELECT ID_Organizacion FROM usuario WHERE No_Usuario='" . $this->user->No_Usuario .  "' AND Nu_Estado=1)";
		
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
	
	public function getCanalesVenta(){
		$query = "SELECT ID_Tabla_Dato AS ID, No_Descripcion AS Nombre FROM tabla_dato WHERE No_Relacion='Canal_Venta'";
		
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
	
	public function getDatosDocumentoVentaWhatsApp($arrPost){
		$query = "SELECT
EMP.No_Empresa_Comercial,
EMP.No_Empresa,
TD.ID_Tipo_Documento,
TD.No_Tipo_Documento,
SD.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision AS Fecha_Emision,
VC.Fe_Vencimiento AS Fecha_Vencimiento,
VC.Nu_Tipo_Recepcion,
(CASE
    WHEN VC.Nu_Tipo_Recepcion = 5 THEN 'Tienda'
    WHEN VC.Nu_Tipo_Recepcion = 6 THEN 'Delivery'
    WHEN VC.Nu_Tipo_Recepcion = 7 THEN 'Recojo en Tienda'
    ELSE ''
END) AS No_Tipo_Recepcion,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
TDI.No_Tipo_Documento_Identidad_Breve,
VC.Ss_Total AS Total,
VC.Ss_Total_Saldo AS Total_Saldo,
VC.Txt_Url_PDF AS enlace_del_pdf,
VD.Qt_Producto,
VD.Ss_Precio,
ITEM.No_Producto AS sNombreItem,
CONFI.Txt_Terminos_Condiciones_Ticket AS sTerminosCondicionesTicket,
MONE.No_Signo
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
JOIN configuracion AS CONFI ON(EMP.ID_Empresa = CONFI.ID_Empresa)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN tipo_documento AS TD ON(VC.ID_Tipo_Documento = TD.ID_Tipo_Documento)
JOIN serie_documento AS SD ON(VC.ID_Serie_Documento_PK = SD.ID_Serie_Documento_PK)
JOIN entidad AS CLI ON(VC.ID_Entidad = CLI.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
WHERE VC.ID_Documento_Cabecera = " . $arrPost['ID'];
		
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
	
	public function getDatosCotizacionVentaWhatsApp($arrPost){
		$query = "SELECT
EMP.No_Empresa_Comercial,
EMP.No_Empresa,
TD.ID_Tipo_Documento,
TD.No_Tipo_Documento,
VC.ID_Documento_Cabecera AS ID_Numero_Documento,
VC.Fe_Emision AS Fecha_Emision,
VC.Fe_Vencimiento AS Fecha_Vencimiento,
VC.Nu_Tipo_Recepcion,
(CASE
    WHEN VC.Nu_Tipo_Recepcion = 5 THEN 'Tienda'
    WHEN VC.Nu_Tipo_Recepcion = 6 THEN 'Delivery'
    WHEN VC.Nu_Tipo_Recepcion = 7 THEN 'Recojo en Tienda'
    ELSE ''
END) AS No_Tipo_Recepcion,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
TDI.No_Tipo_Documento_Identidad_Breve,
VC.Ss_Total AS Total,
VC.Ss_Total_Saldo AS Total_Saldo,
VD.Qt_Producto,
VD.Ss_Precio,
ITEM.No_Producto AS sNombreItem,
CONFI.Txt_Terminos_Condiciones_Ticket AS sTerminosCondicionesTicket,
MONE.No_Signo,
EMPLE.No_Entidad AS No_Vendedor,
EMPLE.Nu_Celular_Entidad AS Nu_Celular_Vendedor,
EMPLE.Txt_Email_Entidad AS Txt_Email_Vendedor
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
JOIN configuracion AS CONFI ON(EMP.ID_Empresa = CONFI.ID_Empresa)
JOIN moneda AS MONE ON(VC.ID_Moneda = MONE.ID_Moneda)
JOIN tipo_documento AS TD ON(VC.ID_Tipo_Documento = TD.ID_Tipo_Documento)
JOIN entidad AS CLI ON(VC.ID_Entidad = CLI.ID_Entidad)
JOIN tipo_documento_identidad AS TDI ON(TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad)
JOIN documento_detalle AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN producto AS ITEM ON(VD.ID_Producto = ITEM.ID_Producto)
LEFT JOIN entidad AS EMPLE ON(EMPLE.ID_Entidad = VC.ID_Mesero)
WHERE VC.ID_Documento_Cabecera = " . $arrPost['ID'];
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
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro',
		);		
	}
	
	public function getPersonalVentas($arrPost){
		$query = "SELECT ID_Entidad AS ID, No_Entidad AS Nombre FROM entidad WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND Nu_Estado=1 AND Nu_Tipo_Entidad = 4 ORDER BY No_Entidad";
		
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
	
	public function getDeliveryVentas($arrPost){
		$query = "SELECT ID_Entidad AS ID, No_Entidad AS Nombre, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad FROM entidad WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND Nu_Estado=1 AND Nu_Tipo_Entidad=6 ORDER BY No_Entidad";
		
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
	
	public function getMarcasV2($arrPost){
		$query = "SELECT ID_Marca AS ID, No_Marca AS Nombre FROM marca WHERE ID_Empresa=".$this->empresa->ID_Empresa." ORDER BY No_Marca";
		
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

	function obtenerEstadoDocumento($iEstado){
		if( $iEstado == 6 )
			return '<span class="label label-success">Completado</span>';
		else if( $iEstado == 7 )
			return '<span class="label label-warning">Anulado</span>';
		else if( $iEstado == 8 )
			return '<span class="label label-success">Enviado</span>';
		else if( $iEstado == 9 )
			return '<span class="label label-danger">Completado E.</span>';
		else if( $iEstado == 10 )
			return '<span class="label label-warning">Anulado E.</span>';
		else if( $iEstado == 11 )
			return '<span class="label label-danger">Anulado Error</span>';
	}

	function obtenerEstadoDocumentoArray($iEstado){
		if( $iEstado == 6 )
			return array('No_Estado' => 'Completado','No_Class_Estado' => 'success');
		else if( $iEstado == 7 )
			return array('No_Estado' => 'Anulado','No_Class_Estado' => 'warning');
		else if( $iEstado == 8 )
			return array('No_Estado' => 'Enviado','No_Class_Estado' => 'success');
		else if( $iEstado == 9 )
			return array('No_Estado' => 'Completado E.','No_Class_Estado' => 'danger');
		else if( $iEstado == 10 )
			return array('No_Estado' => 'Anulado E.','No_Class_Estado' => 'warning');
		else if( $iEstado == 11 )
			return array('No_Estado' => 'Anulado Error','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoRegistroArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Activo','No_Class_Estado' => 'success');
		return array('No_Estado' => 'Inactivo','No_Class_Estado' => 'danger');
	}

	function obtenerTiposItemArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Tipo_Item' => 'Producto','No_Class_Estado' => 'success');
		else if( $iEstado == 0 )
			return array('No_Tipo_Item' => 'Servicio','No_Class_Estado' => 'success');
		return array('No_Tipo_Item' => 'Interno (Compras)','No_Class_Estado' => 'success');
	}

	function obtenerEstadoRecepcionArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Marketplace - Empresa','No_Class_Estado' => 'success');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Marketplace - Delivery','No_Class_Estado' => 'primary');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Tienda online - Empresa','No_Class_Estado' => 'primary');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Tienda online - Delivery','No_Class_Estado' => 'primary');
		else if( $iEstado == 5 )
			return array('No_Estado' => 'Empresa','No_Class_Estado' => 'primary');
		else if( $iEstado == 6 )
			return array('No_Estado' => 'Delivery','No_Class_Estado' => 'primary');
		else
			return array('No_Estado' => 'Recojo en Tienda','No_Class_Estado' => 'primary');
	}

	function obtenerEstadoCotizacionArray($iEstado){
		if( $iEstado == 5 )
			return array('No_Estado' => 'Registrado','No_Class_Estado' => 'info');
		else if( $iEstado == 0 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'primary');
		else if( $iEstado == 1 )
			return array('No_Estado' => 'Revisado','No_Class_Estado' => 'primary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Aceptado','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoDespachoArray($iEstado){
		if( $iEstado == 0 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'warning');
		else if( $iEstado == 1 )
			return array('No_Estado' => 'Preparando','No_Class_Estado' => 'default');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Enviado','No_Class_Estado' => 'success');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'primary');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
	}

	function obtenerCanalVentaArray($ID_Tabla_Dato){
		if( $ID_Tabla_Dato == 0 )
			return array('Nu_Valor' => '0', 'No_Canal_Venta' => '','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2071 )
			return array('Nu_Valor' => '1', 'No_Canal_Venta' => 'Facebook Messenger','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2072 )
			return array('Nu_Valor' => '2', 'No_Canal_Venta' => 'Instagram','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2073 )
			return array('Nu_Valor' => '3', 'No_Canal_Venta' => 'WhatsApp','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2074 )
			return array('Nu_Valor' => '4', 'No_Canal_Venta' => 'Rappi','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2075 )
			return array('Nu_Valor' => '5', 'No_Canal_Venta' => 'Uber Eats','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2076 )
			return array('Nu_Valor' => '6', 'No_Canal_Venta' => 'PedidosYa','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2077 )
			return array('Nu_Valor' => '7', 'No_Canal_Venta' => 'Mercado Libre','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2078 )
			return array('Nu_Valor' => '8', 'No_Canal_Venta' => 'Cornershop','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2079 )
			return array('Nu_Valor' => '9', 'No_Canal_Venta' => 'Linio','No_Class' => '1');
		else if( $ID_Tabla_Dato == 2080 )
			return array('Nu_Valor' => '10', 'No_Canal_Venta' => 'Tienda Virtual','No_Class' => '1');
		return array('Nu_Valor' => '11', 'No_Canal_Venta' => 'Facebook Marketplace','No_Class' => '1');
	}

	function obtenerVarianteItemArray($ID_Tabla_Dato){
		if( $ID_Tabla_Dato == 0 )
			return array('Nu_Valor' => '-', 'No_Descripcion' => '','No_Class' => 'success');
		else if( $ID_Tabla_Dato == 2084 )
			return array('Nu_Valor' => 'Variante 1', 'No_Descripcion' => 'Variante 1','No_Class' => 'success');
		else if( $ID_Tabla_Dato == 2085 )
			return array('Nu_Valor' => 'Variante 2', 'No_Descripcion' => 'Variante 2','No_Class' => 'success');
		return array('Nu_Valor' => 'Variante 3', 'No_Descripcion' => 'Variante 3','No_Class' => 'success');
	}
	
	public function getTipoVarianteTablaDato(){
		$query = "SELECT ID_Tabla_Dato AS ID, No_Descripcion AS Nombre FROM tabla_dato WHERE ID_Tabla_Dato IN(2084,2085,2086) ORDER BY No_Descripcion";
		
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
	
	public function getVariante(){
		$query = "SELECT ID_Variante_Item AS ID, No_Variante AS Nombre FROM variante_item WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " ORDER BY No_Variante";
		
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
	
	public function getVariantexIDTablaDato($arrPost){
		$query = "SELECT ID_Variante_Item AS ID, No_Variante AS Nombre FROM variante_item WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tabla_Dato = " . $arrPost['ID_Tabla_Dato'];
		
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
	
	public function getVarianteDetalle($arrPost){
		$query = "SELECT ID_Variante_Item_Detalle AS ID, No_Valor AS Nombre FROM variante_item_detalle WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Variante_Item = " . $arrPost['ID_Variante_Item'];
		
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
	
	public function getSunatTipoOperacion(){
		$query = "SELECT ID_Sunat_Tipo_Transaction AS ID, No_Sunat_Tipo_Transaction AS Nombre FROM sunat_tipo_transaction ORDER BY ID_Sunat_Tipo_Transaction ASC";
		
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
	
	public function getSedexEmpresa($arrParams){
		$query = "SELECT ID_Sede_Musica AS ID, No_Sede_Musica AS Nombre FROM sede_musica WHERE ID_Empresa = " . $arrParams['iIdEmpresa'] . " ORDER BY No_Sede_Musica ASC";
		
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
	
	public function getSalonxEmpresa($arrParams){
		$query = "SELECT ID_Salon AS ID, No_Salon AS Nombre FROM salon WHERE ID_Empresa = " . $arrParams['iIdEmpresa'] . " AND ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . " ORDER BY No_Salon ASC";
		
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
	
	public function getHorarioClase($arrParams){
		$query = "SELECT
ID_Horario_Clase AS ID,
CONCAT(DS.No_Dia, ' ', HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) AS Nombre,
CONCAT(HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) AS Nombre_Hora
FROM
horario_clase AS HC
JOIN dia_semana AS DS ON(DS.ID_Dia_Semana = HC.ID_Dia_Semana) 
WHERE
ID_Empresa = " . $arrParams['iIdEmpresa'] . "
AND ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . "
ORDER BY Nombre ASC";

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

	public function getDiasSemana($arrParams){
		$query = "SELECT ID_Dia_Semana AS ID, No_Dia AS Nombre FROM dia_semana ORDER BY ID_Dia_Semana ASC";
		
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

	public function getAlumnoxEntidad($arrPost){
		$query = "SELECT ID_Entidad AS ID, No_Contacto AS Nombre FROM entidad WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Estado=1 AND No_Contacto != '' AND Nu_Tipo_Entidad = " . $arrPost['iTipoEntidad'] . " ORDER BY No_Entidad";
	
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
			'sClassModal' => 'modal-warning',
		);
	}
	
	public function getMatriculaAlumno($arrParams){
		$query = "SELECT DISTINCT
ALU.ID_Entidad,
ALU.No_Contacto
FROM
matricula_alumno AS MA
JOIN entidad AS ALU ON(MA.ID_Entidad_Alumno = ALU.ID_Entidad)
WHERE
MA.ID_Empresa = " . $arrParams['iIdEmpresa'] . "
AND MA.ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . "
AND MA.ID_Salon = " . $arrParams['ID_Salon'] . "
AND MA.ID_Entidad_Profesor = " . $arrParams['ID_Entidad_Profesor'] . "
ORDER BY ALU.No_Contacto ASC";

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

	function obtenerEstadoAsistenciaArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Si','No_Class_Estado' => 'success');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Recuperar','No_Class_Estado' => 'warning');
		return array('No_Estado' => 'No','No_Class_Estado' => 'danger');
	}
	
	public function getHorarioClaseReporte($arrParams){
		$query = "SELECT DISTINCT
CONCAT(HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) AS Nombre_Hora
FROM
horario_clase AS HC
JOIN dia_semana AS DS ON(DS.ID_Dia_Semana = HC.ID_Dia_Semana) 
WHERE
ID_Empresa = " . $arrParams['iIdEmpresa'] . "
AND ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . "
ORDER BY Nombre_Hora ASC";

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
	
	public function getReporteAlumnosMatriculadosxParams($arrParams){
		$query = "SELECT DISTINCT
ALU.No_Contacto
FROM
matricula_alumno AS MA
JOIN entidad AS ALU ON(MA.ID_Entidad_Alumno = ALU.ID_Entidad)
JOIN horario_clase AS HC ON(MA.ID_Horario_Clase = HC.ID_Horario_Clase)
JOIN dia_semana AS DS ON(DS.ID_Dia_Semana = HC.ID_Dia_Semana)
WHERE
MA.ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . "
AND MA.ID_Salon = " . $arrParams['ID_Salon'] . "
AND DS.ID_Dia_Semana = " . $arrParams['ID_Dia_Semana'] . "
AND CONCAT(HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) = '" . $arrParams['Nombre_Hora'] . "'";

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
	
	public function getDatosPuntoVenta($arrPost){
		$ID_Documento_Cabecera = $arrPost['ID_Documento_Cabecera'];
		$query = "SELECT No_Formato_PDF, ID_Tipo_Documento FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getListaPrecioxCliente($arrPost){
		$Nu_Tipo_Lista_Precio = $arrPost['Nu_Tipo_Lista_Precio'];
		$ID_Entidad = $arrPost['ID_Entidad'];
		$query = "SELECT ID_Lista_Precio_Cabecera FROM lista_precio_cabecera WHERE Nu_Tipo_Lista_Precio = " . $Nu_Tipo_Lista_Precio . " AND ID_Entidad = " . $ID_Entidad . " LIMIT 1";
		
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

	function obtenerEstadoLavadoRecepcionArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Trabajando','No_Class_Estado' => 'warning');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Por entregar','No_Class_Estado' => 'danger');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'primary');
	}

	function obtenerEstadoLavadoInternoArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente envío (Sede)','No_Class_Estado' => 'danger');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Planta','No_Class_Estado' => 'warning');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Lavado al agua iniciado','No_Class_Estado' => 'warning');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Lavado al agua finalizado','No_Class_Estado' => 'warning');
		else if( $iEstado == 5 )
			return array('No_Estado' => 'Lavado al seco iniciado','No_Class_Estado' => 'warning');
		else if( $iEstado == 6 )
			return array('No_Estado' => 'Lavado al seco finalizado','No_Class_Estado' => 'warning');
		else if( $iEstado == 7 )
			return array('No_Estado' => 'Planchado','No_Class_Estado' => 'warning');
		else if( $iEstado == 8 )
			return array('No_Estado' => 'Doblado','No_Class_Estado' => 'warning');
		else if( $iEstado == 9 )
			return array('No_Estado' => 'Embolsado','No_Class_Estado' => 'info');
		else if( $iEstado == 10 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'success');
		else if( $iEstado == 11 )
			return array('No_Estado' => 'Pendiente envo Servicio Tercerizado','No_Class_Estado' => 'primary');
		else if( $iEstado == 12 )
			return array('No_Estado' => 'Enviar Servicio Tercerizado','No_Class_Estado' => 'danger');
		else if( $iEstado == 13 )
			return array('No_Estado' => 'Enviado Servicio Tercerizado','No_Class_Estado' => 'success');
		else if( $iEstado == 14 )
			return array('No_Estado' => 'Recepción (falta) Servicio Tercerizado','No_Class_Estado' => 'warning');
		else if( $iEstado == 15 )
			return array('No_Estado' => 'Recepción (todo) Servicio Tercerizado','No_Class_Estado' => 'success');
		else if( $iEstado == 16 )
			return array('No_Estado' => 'Pedido enviado (Sede)','No_Class_Estado' => 'success');
		else if( $iEstado == 17 )
			return array('No_Estado' => 'Pendiente envío (Planta)','No_Class_Estado' => 'danger');
		else
			return array('No_Estado' => 'Pedido enviado (Planta)','No_Class_Estado' => 'success');
	}

	function obtenerEstadoLavadoItemInternoArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Lavado al agua','No_Class_Estado' => 'primary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Lavado al seco','No_Class_Estado' => 'success');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Planchado','No_Class_Estado' => 'info');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Otros','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'Secado','No_Class_Estado' => 'success');
	}
	
	public function getImpuestoRegaloSUNAT(){
		//Inafecto – Retiro por Bonificación
		$query="SELECT ICD.ID_Impuesto_Cruce_Documento, ICD.Ss_Impuesto
FROM
impuesto AS IMP
JOIN impuesto_cruce_documento AS ICD ON(IMP.ID_Impuesto = ICD.ID_Impuesto)
WHERE
ICD.Nu_Estado = 1
AND IMP.Nu_Sunat_Codigo = 31
AND IMP.ID_Empresa=" . $this->empresa->ID_Empresa;
	    return $this->db->query($query)->row();
	}
	
	public function getCantidadItemDocumentoVentaDetalle($ID_Documento_Cabecera){
		$query = "SELECT COUNT(*) AS cantidad_item_x_documento FROM documento_detalle WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		return $this->db->query($query)->row();
	}
	
	public function getItem($arrPost){
		$ID_Producto = $arrPost['ID_Producto'];
		$query = "SELECT Nu_Compuesto FROM producto WHERE ID_Producto = " . $ID_Producto . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock){
		$ID_Almacen = $arrParamsCostoPromedioStock['ID_Almacen'];
		$ID_Producto = $arrParamsCostoPromedioStock['ID_Producto'];

		$arrDataItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto FROM producto WHERE ID_Producto = " . $ID_Producto . " LIMIT 1")->result();
		$Nu_Tipo_Producto = $arrDataItem[0]->Nu_Tipo_Producto;
		$Nu_Compuesto = $arrDataItem[0]->Nu_Compuesto;
		
		if ( $Nu_Tipo_Producto != '0' ) {
			if ($Nu_Compuesto == 0){
				if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where_stock_producto = array('ID_Almacen' => $ID_Almacen, 'ID_Producto' => $ID_Producto);
				
					$arrRowSalida = $this->db->query("SELECT
					SUM(K.Qt_Producto) AS Qt_Producto,
					SUM(K.Ss_SubTotal) AS Ss_SubTotal
					FROM
					movimiento_inventario AS K
					JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
					WHERE
					K.ID_Almacen = " . $ID_Almacen . "
					AND K.ID_Producto = " . $ID_Producto . "
					AND TMOVI.Nu_Tipo_Movimiento = 1")->row();
					$Qt_Producto_Salida = $arrRowSalida->Qt_Producto;
					$Ss_SubTotal_Salida = $arrRowSalida->Ss_SubTotal;
					settype($Qt_Producto_Salida, "double");
					settype($Ss_SubTotal_Salida, "double");
				
					$arrRowEntrada = $this->db->query("SELECT
					SUM(K.Qt_Producto) AS Qt_Producto,
					SUM(K.Ss_SubTotal) AS Ss_SubTotal
					FROM
					movimiento_inventario AS K
					JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
					WHERE
					K.ID_Almacen = " . $ID_Almacen . "
					AND K.ID_Producto = " . $ID_Producto . "
					AND TMOVI.Nu_Tipo_Movimiento = 0")->row();
					$Qt_Producto_Entrada = $arrRowEntrada->Qt_Producto;
					$Ss_SubTotal_Entrada = $arrRowEntrada->Ss_SubTotal;
					settype($Qt_Producto_Entrada, "double");
					settype($Ss_SubTotal_Entrada, "double");
				
					$Qt_Producto_Actual = round(($Qt_Producto_Entrada - $Qt_Producto_Salida), 6);
					$Ss_SubTotal_Actual = round(($Ss_SubTotal_Entrada - $Ss_SubTotal_Salida), 6);
					$Ss_Costo_Promedio = ($Ss_SubTotal_Actual / ($Qt_Producto_Actual != 0 ? $Qt_Producto_Actual : 1));
					$stock_producto = array(
						'Ss_Costo_Promedio'	=> $Ss_Costo_Promedio,
					);
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
				}
			} else {
				$query = "SELECT
				ENLAPRO.ID_Producto,
				ENLAPRO.Qt_Producto_Descargar
				FROM
				enlace_producto AS ENLAPRO
				JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
				WHERE
				ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
				$arrItems = $this->db->query($query)->result();
				
				foreach ($arrItems as $row_enlace) {
					$ID_Producto = $row_enlace->ID_Producto;
					$Qt_Producto_Descargar = $row_enlace->Qt_Producto_Descargar;
					if ($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $ID_Almacen . " AND ID_Producto = " . $ID_Producto . " LIMIT 1")->row()->existe > 0){
						$where_stock_producto = array('ID_Almacen' => $ID_Almacen, 'ID_Producto' => $ID_Producto);
						
						$arrRowSalida = $this->db->query("SELECT
						SUM(K.Qt_Producto) AS Qt_Producto,
						SUM(K.Ss_SubTotal) AS Ss_SubTotal
						FROM
						movimiento_inventario AS K
						JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
						WHERE
						K.ID_Almacen = " . $ID_Almacen . "
						AND K.ID_Producto = " . $ID_Producto . "
						AND TMOVI.Nu_Tipo_Movimiento = 1")->row();
						$Qt_Producto_Salida = $arrRowSalida->Qt_Producto;
						$Ss_SubTotal_Salida = $arrRowSalida->Ss_SubTotal;
						settype($Qt_Producto_Salida, "double");
						settype($Ss_SubTotal_Salida, "double");
						
						$arrRowEntrada = $this->db->query("SELECT
						SUM(K.Qt_Producto) AS Qt_Producto,
						SUM(K.Ss_SubTotal) AS Ss_SubTotal
						FROM
						movimiento_inventario AS K
						JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = K.ID_Tipo_Movimiento)
						WHERE
						K.ID_Almacen = " . $ID_Almacen . "
						AND K.ID_Producto = " . $ID_Producto . "
						AND TMOVI.Nu_Tipo_Movimiento = 0")->row();
						$Qt_Producto_Entrada = $arrRowEntrada->Qt_Producto;
						$Ss_SubTotal_Entrada = $arrRowEntrada->Ss_SubTotal;
						settype($Qt_Producto_Entrada, "double");
						settype($Ss_SubTotal_Entrada, "double");
						
						$Qt_Producto_Actual = round(($Qt_Producto_Entrada - $Qt_Producto_Salida), 6);
						$Ss_SubTotal_Actual = round(($Ss_SubTotal_Entrada - $Ss_SubTotal_Salida), 6);
						$Ss_Costo_Promedio = ($Ss_SubTotal_Actual / ($Qt_Producto_Actual != 0 ? $Qt_Producto_Actual : 1));
						$stock_producto = array(
							'Ss_Costo_Promedio'	=> $Ss_Costo_Promedio,
						);
						$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
					}
				} // /. foreach calcular stock por enlaces de item
			} // /. validacion item si es compuesto
		} // /. validando tipo de item 0 = Servicio
	}

	//actualizar stock por anulacion o eliminacion de SALIDA (Salida o Guia Inventario / factura de venta / Historial de venta)
	public function actualizarStockMovimientoSalidaxAnularEliminar($arrParamsStockSalida){
		$ID_Almacen = $arrParamsStockSalida['ID_Almacen'];
		$ID_Producto = $arrParamsStockSalida['ID_Producto'];
		$ID_Tipo_Documento = $arrParamsStockSalida['ID_Tipo_Documento'];
		$Qt_Producto_Item_Documento = $arrParamsStockSalida['Qt_Producto_Item_Documento'];

		$arrDataItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto FROM producto WHERE ID_Producto = " . $ID_Producto . " LIMIT 1")->result();
		$Nu_Tipo_Producto = $arrDataItem[0]->Nu_Tipo_Producto;
		$Nu_Compuesto = $arrDataItem[0]->Nu_Compuesto;

		if ( $Nu_Tipo_Producto != 0 ) {//0=Servicio (No genera stock)
			if ($Nu_Compuesto == 0){
				$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $ID_Almacen . " LIMIT 1")->row();
				$where = array('ID_Almacen' => $ID_Almacen, 'ID_Producto' => $ID_Producto);

				if ($ID_Tipo_Documento != 5){//Nota de Crédito
					$stock_producto = array('Qt_Producto' => ($objStockItemAlmacen->Qt_Producto + $Qt_Producto_Item_Documento));
					$this->db->update('stock_producto', $stock_producto, $where);
				} else {
					$stock_producto = array('Qt_Producto' => ($objStockItemAlmacen->Qt_Producto - $Qt_Producto_Item_Documento));
					$this->db->update('stock_producto', $stock_producto, $where);
				}
			} else {
				$query = "SELECT
				ENLAPRO.ID_Producto
				FROM
				enlace_producto AS ENLAPRO
				JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
				WHERE
				ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
				$arrItems = $this->db->query($query)->result();
				foreach ($arrItems as $row_enlace) {
					$ID_Producto_Enlace = $row_enlace->ID_Producto;
					$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $ID_Almacen . " LIMIT 1")->row();
					$where = array('ID_Almacen' => $ID_Almacen, 'ID_Producto' => $ID_Producto_Enlace);
	
					if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($objStockItemAlmacen->Qt_Producto + $Qt_Producto_Item_Documento));
						$this->db->update('stock_producto', $stock_producto, $where);
					} else {
						$stock_producto = array('Qt_Producto' => ($objStockItemAlmacen->Qt_Producto - $Qt_Producto_Item_Documento));
						$this->db->update('stock_producto', $stock_producto, $where);
					}
				}// ./ foreach enlace de items
			}// if - else verificar si es compuesto o no
		}//if si no es un SERVICIO
	}

	public function obtenerImporteDetalleDocumentoGratuita($ID_Documento_Cabecera){
		$query = "SELECT SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total FROM
documento_detalle AS VD
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		return $this->db->query($query)->row();
	}

	public function obtenerVarianteProductos($ID_Producto){
		$query = "SELECT
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3
FROM
producto AS PRO
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
WHERE PRO.ID_Producto = " . $ID_Producto . " LIMIT 1";
		return $this->db->query($query)->row();
	}

	public function obtenerTipoCambio($arrParams){
		$query = "SELECT Ss_Venta_Oficial, Ss_Compra_Oficial FROM tasa_cambio WHERE ID_Empresa=" . $arrParams['ID_Empresa'] . " AND ID_Moneda=" . $arrParams['ID_Moneda'] . " AND Fe_Ingreso='" . $arrParams['Fe_Emision'] . "' LIMIT 1";
		return $this->db->query($query)->row();
	}
	
	public function getListaPrecioxId($ID_Almacen){
		$query = "SELECT
ID_Lista_Precio_Cabecera,
No_Lista_Precio
FROM
lista_precio_cabecera
WHERE
ID_Almacen=".$ID_Almacen."
ORDER BY
No_Lista_Precio";
		return $this->db->query($query)->result();
	}
	
	public function obtenerPreciosxMayor($arrPost){
		$query = "SELECT * FROM producto_precio_x_mayor WHERE ID_Empresa=".$arrPost['ID_Empresa']." AND ID_Producto=".$arrPost['ID_Producto'] . " ORDER BY Qt_Producto_x_Mayor " . $arrPost['ordenar'];
		return $this->db->query($query)->result();
	}

	public function obtenerImporteDetalleDocumentoGratuitaxIdItem($arrParams){
		$query = "SELECT VD.Qt_Producto, VD.Ss_Subtotal, VD.Ss_Impuesto, VD.Ss_Total FROM
documento_detalle AS VD
JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
WHERE
VD.ID_Documento_Cabecera = " . $arrParams['ID_Documento_Cabecera'] . "
AND VD.ID_Producto = " . $arrParams['ID_Producto'] . "
AND IMP.Nu_Tipo_Impuesto = 4 LIMIT 1";
		return $this->db->query($query)->row();
	}

	function obtenerEstadoOrdenPedidoTienda($iEstado){
		//if($this->user->ID_Pais==1) {//1 = PERU
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'default');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Confirmado','No_Class_Estado' => 'warning');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Preparando','No_Class_Estado' => 'info');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'En Camino','No_Class_Estado' => 'primary');
		else if( $iEstado == 5 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'success');
		else if( $iEstado == 6 )
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
		else if( $iEstado == 8 )
			return array('No_Estado' => 'Guia Generada','No_Class_Estado' => 'info');
		else if( $iEstado == 9 )
			return array('No_Estado' => 'Recolectado','No_Class_Estado' => 'success');
		else if( $iEstado == 10 )
			return array('No_Estado' => 'Transito','No_Class_Estado' => 'primary');
		else if( $iEstado == 11 )
			return array('No_Estado' => '1 intento','No_Class_Estado' => 'info');
		else if( $iEstado == 12 )
			return array('No_Estado' => '2 intento','No_Class_Estado' => 'info');
		else if( $iEstado == 13 )
			return array('No_Estado' => 'Devolución Pendiente','No_Class_Estado' => 'warning');
		else if( $iEstado == 14 )
			return array('No_Estado' => 'Devuelto','No_Class_Estado' => 'success');
	}
}
