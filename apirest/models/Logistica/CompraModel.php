<?php
class CompraModel extends CI_Model{
	var $table          				= 'documento_cabecera';
	var $table_documento_detalle		= 'documento_detalle';
	var $table_documento_detalle_lote	= 'documento_detalle_lote';
	var $table_documento_enlace			= 'documento_enlace';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda					= 'moneda';
	var $table_medio_pago				= 'medio_pago';
	var $table_organizacion				= 'organizacion';
	var $table_tabla_dato				= 'tabla_dato';
	var $table_almacen				= 'almacen';
	var $table_estado_documento = 'estado_documento';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
        
        if(!empty($this->input->post('Filtro_Estado')))
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if($this->input->post('Filtro_Estado_Pago') == '1')
			$this->db->where('VC.Ss_Total_Saldo > ', '0.00');
			
        if($this->input->post('Filtro_Estado_Pago') == '2')
			$this->db->where('VC.Ss_Total_Saldo = ', '0.00');
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
        $this->db->select('VC.ID_Documento_Cabecera, ALMA.No_Almacen, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, MP.No_Medio_Pago, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario, MP.Nu_Tipo AS Nu_Tipo_Medio_Pago, VC.Ss_Total_Saldo, VC.Ss_Percepcion, PROVE.Nu_Documento_Identidad AS Nu_Documento_Identidad_Proveedor, VC.Txt_Glosa, VC.Fe_Detraccion, VC.Nu_Detraccion')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = VC.ID_Medio_Pago', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 2)
		->where('VC.Nu_Correlativo !=', 0)
    	->where('VC.ID_Tipo_Documento != ', 12);
		
        if(isset($_POST['order']))
        	$this->db->order_by( 'VC.ID_Documento_Cabecera DESC' );
        else if(isset($this->order))
        	$this->db->order_by( 'VC.ID_Documento_Cabecera DESC' );
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all(){
        if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
        
        if(!empty($this->input->post('Filtro_Estado')))
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if($this->input->post('Filtro_Estado_Pago') == '1')
			$this->db->where('VC.Ss_Total_Saldo > ', '0.00');
			
        if($this->input->post('Filtro_Estado_Pago') == '2')
			$this->db->where('VC.Ss_Total_Saldo = ', '0.00');
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

        $this->db->select('VC.ID_Documento_Cabecera, ALMA.No_Almacen, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, MP.No_Medio_Pago, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario, MP.Nu_Tipo AS Nu_Tipo_Medio_Pago, VC.Ss_Total_Saldo, VC.Ss_Percepcion, PROVE.Nu_Documento_Identidad AS Nu_Documento_Identidad_Proveedor, VC.Txt_Glosa, VC.Fe_Detraccion, VC.Nu_Detraccion')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = VC.ID_Medio_Pago', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 2)
		->where('VC.Nu_Correlativo !=', 0)
    	->where('VC.ID_Tipo_Documento != ', 12);
		
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $query = "SELECT
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Documento_Cabecera,
PROVE.ID_Entidad,
PROVE.No_Entidad,
PROVE.Nu_Documento_Identidad,
PROVE.Txt_Direccion_Entidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
VC.ID_Moneda,
VC.ID_Medio_Pago,
VC.Fe_Vencimiento,
VC.Fe_Periodo,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
VD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
VD.Ss_Precio AS Ss_Precio,
VD.Qt_Producto,
VD.ID_Impuesto_Cruce_Documento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
VD.Ss_Descuento AS Ss_Descuento_Producto,
VD.Ss_Descuento_Impuesto AS Ss_Descuento_Impuesto_Producto,
VD.Po_Descuento AS Po_Descuento_Impuesto_Producto,
VD.Ss_Total AS Ss_Total_Producto,
ICDOCU.Ss_Impuesto,
VE.ID_Documento_Cabecera_Enlace,
VE.ID_Tipo_Documento_Modificar,
VE.ID_Serie_Documento_Modificar,
VE.ID_Numero_Documento_Modificar,
MP.Nu_Tipo,
IMP.Nu_Tipo_Impuesto,
VC.Txt_Glosa,
VC.Ss_Descuento AS Ss_Descuento,
VC.Ss_Total AS Ss_Total,
VC.Ss_Total_Saldo AS Ss_Total_Saldo,
VC.Ss_Percepcion AS Ss_Percepcion,
VC.Fe_Detraccion,
VC.Nu_Detraccion,
VC.ID_Rubro,
VC.Po_Descuento,
VDL.Nu_Lote_Vencimiento,
VDL.Fe_Lote_Vencimiento,
UM.No_Unidad_Medida,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3
FROM
" . $this->table . " AS VC
JOIN " . $this->table_documento_detalle . " AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN " . $this->table_documento_detalle_lote . " AS VDL ON(VC.ID_Documento_Cabecera = VDL.ID_Documento_Cabecera AND VD.ID_Documento_Detalle = VDL.ID_Documento_Detalle)
JOIN " . $this->table_entidad . " AS PROVE ON(PROVE.ID_Entidad = VC.ID_Entidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN (
SELECT
VE.ID_Documento_Cabecera,
VE.ID_Documento_Cabecera_Enlace,
VC.ID_Tipo_Documento AS ID_Tipo_Documento_Modificar,
VC.ID_Serie_Documento AS ID_Serie_Documento_Modificar,
VC.ID_Numero_Documento AS ID_Numero_Documento_Modificar
FROM
" . $this->table . " AS VC
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_documento_enlace . " AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
) AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
WHERE VC.ID_Documento_Cabecera = " . $ID;
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'style_modal' => 'modal-danger',
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar formato documento electrónico',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrData = $arrResponseSQL->result();
			foreach ($arrData as $row) {
				$arrParams['iIdDocumentoCabecera'] = $row->ID_Documento_Cabecera;
				$arrMedioPago = $this->obtenerComprobanteMedioPago($arrParams);
				if($arrMedioPago['sStatus']=='success') {
					$row->ID_Tipo_Medio_Pago = $arrMedioPago['arrData'][0]->ID_Tipo_Medio_Pago;
					$row->Nu_Tarjeta = (!empty($arrMedioPago['arrData'][0]->Nu_Tarjeta) ? $arrMedioPago['arrData'][0]->Nu_Tarjeta : '-');
					$row->Nu_Transaccion = (!empty($arrMedioPago['arrData'][0]->Nu_Transaccion) ? $arrMedioPago['arrData'][0]->Nu_Transaccion : '-');
				}
				$rows[] = $row;
				$arrData = $rows;
			}
			return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'sStatus' => 'success',
				'arrData' => $arrData,
			);
		}

		return array(
			'status' => 'warning',
			'style_modal' => 'modal-warning',
			'sStatus' => 'warning',
			'sMessage' => 'No hay registro',
		);
    }
    
	public function obtenerComprobanteMedioPago($arrParams){
		$query = "SELECT
VMP.ID_Tipo_Medio_Pago,
VMP.Nu_Tarjeta,
VMP.Nu_Transaccion
FROM
documento_cabecera AS VC
JOIN documento_medio_pago AS VMP ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
WHERE
VC.ID_Documento_Cabecera = " . $arrParams['iIdDocumentoCabecera'] . " ORDER BY VMP.ID_Documento_Medio_Pago ASC LIMIT 1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener medio(s) de pago',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
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
			'sMessage' => 'No hay registro F.C.',
		);
	}
    
    public function agregarCompra($arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrProveedorNuevo){
    	$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND ID_Entidad = " . $ID_Entidad . " AND ID_Tipo_Documento = " . $arrCompraCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrCompraCabecera['ID_Serie_Documento'] . "' AND ID_Numero_Documento = " . $arrCompraCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe la compra');
		}else{
			$this->db->trans_begin();

			$iTipoCliente = $arrCompraCabecera['iTipoCliente'];
			unset($arrCompraCabecera['iTipoCliente']);

			$Nu_Correlativo = 0;
			$Fe_Year = ToYear($arrCompraCabecera['Fe_Periodo']);
			$Fe_Month = ToMonth($arrCompraCabecera['Fe_Periodo']);
			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();

			if ( count($arrCorrelativoPendiente) > 0 ){
				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
				
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Tipo_Asiento', 2);
				$this->db->where('Fe_Year', $Fe_Year);
				$this->db->where('Fe_Month', $Fe_Month);
				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
		        $this->db->delete('correlativo_tipo_asiento_pendiente');
			} else {
				if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo = Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
AND ID_Tipo_Asiento=2
AND Fe_Year='" . $Fe_Year. "'
AND Fe_Month='" . $Fe_Month . "'";
					$this->db->query($sql_correlativo_libro_sunat);
				} else {
					$sql_correlativo_libro_sunat = "INSERT INTO correlativo_tipo_asiento (
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
2,
'" . $Fe_Year . "',
'" . $Fe_Month . "',
1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			}
			
			if (is_array($arrProveedorNuevo)){
			    unset($arrCompraCabecera['ID_Entidad']);
			    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
					$arrProveedor = array(
		                'ID_Empresa'					=> $this->user->ID_Empresa,
	                	'ID_Organizacion'				=> $arrCompraCabecera['ID_Organizacion'],
		                'Nu_Tipo_Entidad'				=> 1,//Proveedor
		                'ID_Tipo_Documento_Identidad'	=> $arrProveedorNuevo['ID_Tipo_Documento_Identidad'],
		                'Nu_Documento_Identidad'		=> $arrProveedorNuevo['Nu_Documento_Identidad'],
		                'No_Entidad'					=> $arrProveedorNuevo['No_Entidad'],
		                'Txt_Direccion_Entidad' 		=> $arrProveedorNuevo['Txt_Direccion_Entidad'],
		                'Nu_Telefono_Entidad'			=> $arrProveedorNuevo['Nu_Telefono_Entidad'],
		                'Nu_Celular_Entidad'			=> $arrProveedorNuevo['Nu_Celular_Entidad'],
		                'Nu_Estado' 					=> 1,
		            );
		    		$this->db->insert('entidad', $arrProveedor);
		    		$Last_ID_Entidad = $this->db->insert_id();
			    } else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El proveedor ya se encuentra creado, seleccionar Existente');
				}
	    		$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			$ID_Tipo_Medio_Pago = $arrCompraCabecera['ID_Tipo_Medio_Pago'];
			$Nu_Transaccion = $arrCompraCabecera['Nu_Transaccion'];
			$Nu_Tarjeta = $arrCompraCabecera['Nu_Tarjeta'];

			unset($arrCompraCabecera['ID_Tipo_Medio_Pago']);
			unset($arrCompraCabecera['Nu_Transaccion']);
			unset($arrCompraCabecera['Nu_Tarjeta']);

			$ID_Guia_Cabecera_Enlace = '';
			if ( $esEnlace == 2 ){//es Guia
				$ID_Guia_Cabecera_Enlace = $arrCompraCabecera['ID_Guia_Cabecera'];
				unset($arrCompraCabecera['ID_Guia_Cabecera']);
			}

			$ID_Documento_Cabecera_Enlace = (!empty($ID_Documento_Cabecera_Enlace) ? $ID_Documento_Cabecera_Enlace : $arrCompraCabecera['ID_Documento_Cabecera_Enlace']);
			unset($arrCompraCabecera['ID_Documento_Cabecera_Enlace']);
			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			if ( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 ){
				$table_documento_enlace = array(
					'ID_Empresa'					=> $this->user->ID_Empresa,
					'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
					'ID_Documento_Cabecera_Enlace'	=> $ID_Documento_Cabecera_Enlace,
				);
				$this->db->insert($this->table_documento_enlace, $table_documento_enlace);
				
				$query = "SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$arrDocumentoReferencia = $this->db->query($query)->row();

				if ( $arrDocumentoReferencia->Ss_Total_Saldo >= $arrCompraCabecera['Ss_Total'] ) {
					if ($arrCompraCabecera['ID_Tipo_Documento']==5) {//NC
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrCompraCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);
					}
					
					if ($arrCompraCabecera['ID_Tipo_Documento']==6) {//ND
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrCompraCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);					
					}
				}
			}
			
			if ( $esEnlace == 2 ){//es Guia
				$guia_enlace = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $ID_Guia_Cabecera_Enlace,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $guia_enlace);
			}
			
			foreach ($arrCompraDetalle as $row) {
				$documento_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
					'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => $this->security->xss_clean($row['Ss_SubTotal']),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => $this->security->xss_clean($row['Ss_Impuesto']),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
					'Fe_Emision' => $arrCompraCabecera['Fe_Emision']
				);
				$arrUpdData = array( 'Ss_Costo' => $this->security->xss_clean($row['Ss_Precio']) );
				$arrUpdWhere = array( 'ID_Empresa' => $this->user->ID_Empresa, 'ID_Producto' => $this->security->xss_clean($row['ID_Producto']) );
				if ($this->updCostoItemCompra($arrUpdData, $arrUpdWhere) == false ){
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'Problemas al agregar costo en item');
				}
			}
			$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();

			foreach ($arrCompraDetalle as $row) {
				if (!empty($row['Nu_Lote_Vencimiento']) && !empty($row['Fe_Lote_Vencimiento'])) {
					$documento_detalle_lote[] = array(
						'ID_Empresa' => $this->user->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
						'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Documento_Detalle'	=> $iIdDocumentoDetalleFirst,
						'Nu_Lote_Vencimiento' => $this->security->xss_clean($row['Nu_Lote_Vencimiento']),
						'Fe_Lote_Vencimiento' => ToDate($this->security->xss_clean($row['Fe_Lote_Vencimiento'])),
					);
					++$iIdDocumentoDetalleFirst;
				}
			}
			if ( isset($documento_detalle_lote) )
				$this->db->insert_batch($this->table_documento_detalle_lote, $documento_detalle_lote);

			$documento_medio_pago = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
				'ID_Medio_Pago'	=> $this->security->xss_clean($arrCompraCabecera['ID_Medio_Pago']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($ID_Tipo_Medio_Pago),
				'Nu_Transaccion' => $this->security->xss_clean($Nu_Transaccion),
				'Nu_Tarjeta' => $this->security->xss_clean($Nu_Tarjeta),
				'Ss_Total' => $this->security->xss_clean($arrCompraCabecera['Ss_Total']),
			);
			$this->db->insert('documento_medio_pago', $documento_medio_pago);

	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
	        }
		}
    }

    public function actualizarCompra($where, $arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrProveedorNuevo){
		$this->db->trans_begin();
		
		$iTipoCliente = $arrCompraCabecera['iTipoCliente'];
		unset($arrCompraCabecera['iTipoCliente']);

		$arrDataModificar = $this->db->query("SELECT ID_Organizacion, ID_Almacen, ID_Documento_Cabecera, ID_Tipo_Documento, Nu_Correlativo, Nu_Descargar_Inventario FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $where['ID_Documento_Cabecera'] . " LIMIT 1")->result();

		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;
		
		$objAlmacen = $this->db->query("SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Almacen=" . $ID_Almacen . " LIMIT 1")->row();
		if ($ID_Almacen != $this->session->userdata['almacen']->ID_Almacen ) {
			$this->db->trans_rollback();
			return array('status' => 'danger', 'style_modal' => 'modal-warning', 'message' => 'Para modificar debes seleccionar ' . $objAlmacen->No_Almacen);
		}

		$ID_Documento_Cabecera = $arrDataModificar[0]->ID_Documento_Cabecera;
		$ID_Tipo_Documento = $arrDataModificar[0]->ID_Tipo_Documento;
		$Nu_Correlativo = $arrDataModificar[0]->Nu_Correlativo;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;
		
        $this->db->delete($this->table_documento_detalle_lote, $where);
		$this->db->delete($this->table_documento_detalle, $where);
		$this->db->delete('documento_medio_pago', $where);

		$ID_Documento_Cabecera_Enlace = (!empty($ID_Documento_Cabecera_Enlace) ? $ID_Documento_Cabecera_Enlace : $arrCompraCabecera['ID_Documento_Cabecera_Enlace']);
		unset($arrCompraCabecera['ID_Documento_Cabecera_Enlace']);
		if ( (!empty($ID_Documento_Cabecera_Enlace)) && $esEnlace == 1 ){
			$this->db->delete($this->table_documento_enlace, $where);
		}
		
		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1")->row()->existe > 0){
			$objGuiaEnlace = $this->db->query("SELECT * FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1")->row();
			$esEnlace = 2;
			$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
	        $this->db->delete('guia_enlace');
		}

    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
	        		if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
	        		} else {
						$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
	        		}

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
	        $this->db->delete('movimiento_inventario');
		}
		
        $this->db->delete($this->table, $where);

    	$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];
		if($ID_Tipo_Documento != $arrCompraCabecera['ID_Tipo_Documento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND ID_Entidad = " . $ID_Entidad . " AND ID_Tipo_Documento = " . $arrCompraCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrCompraCabecera['ID_Serie_Documento'] . "' AND ID_Numero_Documento = " . $arrCompraCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe la compra');
		}else{
			if (is_array($arrProveedorNuevo)){
				unset($arrCompraCabecera['ID_Entidad']);
				if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
					$arrProveedor = array(
						'ID_Empresa'					=> $this->user->ID_Empresa,
						'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
						'Nu_Tipo_Entidad'				=> 1,//Proveedor
						'ID_Tipo_Documento_Identidad'	=> $arrProveedorNuevo['ID_Tipo_Documento_Identidad'],
						'Nu_Documento_Identidad'		=> $arrProveedorNuevo['Nu_Documento_Identidad'],
						'No_Entidad'					=> $arrProveedorNuevo['No_Entidad'],
						'Txt_Direccion_Entidad' 		=> $arrProveedorNuevo['Txt_Direccion_Entidad'],
						'Nu_Telefono_Entidad'			=> $arrProveedorNuevo['Nu_Telefono_Entidad'],
						'Nu_Celular_Entidad'			=> $arrProveedorNuevo['Nu_Celular_Entidad'],
						'Nu_Estado' 					=> 1,
					);
					$this->db->insert('entidad', $arrProveedor);
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El proveedor ya se encuentra creado, seleccionar Existente');
				}
				$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}

			$arrCompraCabecera['ID_Documento_Cabecera'] = $ID_Documento_Cabecera;
			$arrCompraCabecera['ID_Almacen'] = $ID_Almacen;
			$ID_Tipo_Medio_Pago = $arrCompraCabecera['ID_Tipo_Medio_Pago'];
			$Nu_Transaccion = $arrCompraCabecera['Nu_Transaccion'];
			$Nu_Tarjeta = $arrCompraCabecera['Nu_Tarjeta'];

			unset($arrCompraCabecera['ID_Tipo_Medio_Pago']);
			unset($arrCompraCabecera['Nu_Transaccion']);
			unset($arrCompraCabecera['Nu_Tarjeta']);

			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			if ( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 ){				
				$table_documento_enlace = array(
					'ID_Empresa'					=> $this->user->ID_Empresa,
					'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
					'ID_Documento_Cabecera_Enlace'	=> $ID_Documento_Cabecera_Enlace,
				);
				$this->db->insert($this->table_documento_enlace, $table_documento_enlace);
				
				$query = "SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$arrDocumentoReferencia = $this->db->query($query)->row();

				if ( $arrDocumentoReferencia->Ss_Total_Saldo >= $arrCompraCabecera['Ss_Total'] ) {
					if ($arrCompraCabecera['ID_Tipo_Documento']==5) {//NC
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrCompraCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);
					}
					
					if ($arrCompraCabecera['ID_Tipo_Documento']==6) {//ND
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrCompraCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);					
					}
				}
			}

			if ( $esEnlace == 2 ){//es Guia
				$guia_enlace = array(
					'ID_Empresa' => $objGuiaEnlace->ID_Empresa,
					'ID_Guia_Cabecera' => $objGuiaEnlace->ID_Guia_Cabecera,
					'ID_Documento_Cabecera' => $ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $guia_enlace);
			}

			foreach ($arrCompraDetalle as $row) {
				$documento_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
					'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => $this->security->xss_clean($row['Ss_SubTotal']),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => $this->security->xss_clean($row['Ss_Impuesto']),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
					'Fe_Emision' => $arrCompraCabecera['Fe_Emision']
				);
				$arrUpdData = array( 'Ss_Costo' => $this->security->xss_clean($row['Ss_Precio']) );
				$arrUpdWhere = array( 'ID_Empresa' => $this->user->ID_Empresa, 'ID_Producto' => $this->security->xss_clean($row['ID_Producto']) );
				if ($this->updCostoItemCompra($arrUpdData, $arrUpdWhere) == false ){
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'Problemas al actualizar costo en item');
				}
			}
			$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();

			foreach ($arrCompraDetalle as $row) {
				if (!empty($row['Nu_Lote_Vencimiento']) && !empty($row['Fe_Lote_Vencimiento'])) {
					$documento_detalle_lote[] = array(
						'ID_Empresa' => $this->user->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
						'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Documento_Detalle'	=> $iIdDocumentoDetalleFirst,
						'Nu_Lote_Vencimiento' => $this->security->xss_clean($row['Nu_Lote_Vencimiento']),
						'Fe_Lote_Vencimiento' => ToDate($this->security->xss_clean($row['Fe_Lote_Vencimiento'])),
					);
					++$iIdDocumentoDetalleFirst;
				}
			}
			if ( isset($documento_detalle_lote) )
				$this->db->insert_batch($this->table_documento_detalle_lote, $documento_detalle_lote);

			$documento_medio_pago = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
				'ID_Medio_Pago'	=> $this->security->xss_clean($arrCompraCabecera['ID_Medio_Pago']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($ID_Tipo_Medio_Pago),
				'Nu_Transaccion' => $this->security->xss_clean($Nu_Transaccion),
				'Nu_Tarjeta' => $this->security->xss_clean($Nu_Tarjeta),
				'Ss_Total' => $this->security->xss_clean($arrCompraCabecera['Ss_Total']),
			);
			$this->db->insert('documento_medio_pago', $documento_medio_pago);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
			} else {
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
			}
		}
    }
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle_lote);
		
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete($this->table_documento_detalle);

		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete('documento_medio_pago');
        
    	$ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;
    		
		$query = "SELECT ID_Documento_Cabecera_Enlace FROM documento_enlace WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
		$arrMedioPagoReferencia = $this->db->query($query)->row();
		if ( is_object($arrMedioPagoReferencia) ) {
			$query = "SELECT Ss_Total FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
			$arrDocumentoReferencia = $this->db->query($query)->row();
			
			if ($ID_Tipo_Documento==5){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}

			if ($ID_Tipo_Documento==6){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}
		}
        
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_enlace);

    	if ($Nu_Descargar_Inventario == 1) {
    		$ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;
    		
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = ".$ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
	        		if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		} else {
						$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		}

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID);
			$data = array(
				'Qt_Producto' => 0,
				'Ss_Precio' => 0,
				'Ss_SubTotal' => 0,
				'Ss_Costo_Promedio' => 0,
			);
	        $this->db->update('movimiento_inventario', $data);
    	}
    	
		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}

		$this->db->where('ID_Documento_Cabecera', $ID);
		$data = array(
			'Nu_Estado' => 7,
			'Ss_Descuento' => 0.00,
			'Ss_Total' => 0.00,
			'Ss_Percepcion' => 0.00,
			'Ss_Total_Saldo' => 0.00,
		);
        $this->db->update($this->table, $data);
        
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al anular');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro anulado');
        }
	}
    
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle_lote);
		
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete($this->table_documento_detalle);
        
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete('documento_medio_pago');

        $ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;

		$query = "SELECT ID_Documento_Cabecera_Enlace FROM documento_enlace WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
		$arrMedioPagoReferencia = $this->db->query($query)->row();
		if ( is_object($arrMedioPagoReferencia) ) {			
			$query = "SELECT Ss_Total FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
			$arrDocumentoReferencia = $this->db->query($query)->row();

			if ($ID_Tipo_Documento==5){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}

			if ($ID_Tipo_Documento==6){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}
		}
        
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_enlace);

    	if ($Nu_Descargar_Inventario == 1) {
    		$ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;
    		
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
	        		if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		} else {
						$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		}

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('movimiento_inventario');
    	}
        
        $arrCorrelativoPendiente = $this->db->query("SELECT Fe_Periodo, Nu_Correlativo FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Documento_Cabecera = " . $ID . " LIMIT 1")->result();
        
        $sql_correlativo_pendiente_libro_sunat = "INSERT INTO correlativo_tipo_asiento_pendiente (
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
2,
'" . ToYear($arrCorrelativoPendiente[0]->Fe_Periodo) . "',
'" . ToMonth($arrCorrelativoPendiente[0]->Fe_Periodo) . "',
" . $arrCorrelativoPendiente[0]->Nu_Correlativo . "
);";
		$this->db->query($sql_correlativo_pendiente_libro_sunat);

		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}
        
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete($this->table);
        
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
	}
	
	public function updCostoItemCompra($data, $where){
		if ( $this->db->update('producto', $data, $where) > 0 )
			return true;
		return false;
	}

	public function obtenerImporteDetalleDocumento($ID_Documento_Cabecera){
		$query = "SELECT SUM(Ss_SubTotal) AS Ss_SubTotal, SUM(Ss_Impuesto) AS Ss_Impuesto FROM documento_detalle WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		return $this->db->query($query)->row();
	}

	//Obtener proveedor creado con los datos de la misma empresa
	public function getEntidadProveedorInterno($arrParams){
	    $ID_Empresa = $this->user->ID_Empresa;
		$objProveedor = $this->db->query("SELECT ID_Entidad, Nu_Documento_Identidad, No_Entidad FROM entidad WHERE ID_Empresa = " . $ID_Empresa . " AND Nu_Tipo_Entidad = " . $arrParams['Nu_Tipo_Entidad'] . " AND ID_Tipo_Documento_Identidad = " . $arrParams['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrParams['Nu_Documento_Identidad'] . "' AND Nu_Estado=1 LIMIT 1")->row();
		if(is_object($objProveedor)){
			return array("status" => "success", "data" => $objProveedor);
		}

		$sTipoDocumentoIdentidad = 'RUC';
		if($arrParams['ID_Tipo_Documento_Identidad']==2)
			$sTipoDocumentoIdentidad = 'DNI';
		else if($arrParams['ID_Tipo_Documento_Identidad']==1)
			$sTipoDocumentoIdentidad = 'OTROS';
		
		return array('status' => 'error', 'message' => 'Elegir proveedor nuevo con ' . $sTipoDocumentoIdentidad . ' - ' . $this->empresa->Nu_Documento_Identidad . ' ya que no está registrado para la compra rápida');
	}
}
