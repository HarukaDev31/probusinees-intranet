<?php
class OrdenCompraModel extends CI_Model{
	var $table          				= 'documento_cabecera';
	var $table_documento_detalle		= 'documento_detalle';
	var $table_documento_enlace			= 'documento_enlace';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda					= 'moneda';
	var $table_organizacion				= 'organizacion';
	var $table_tabla_dato				= 'tabla_dato';
	var $table_almacen				= 'almacen';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
    	
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
			$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
			
        if($this->input->post('Filtro_Estado') != '')
			$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
			
        if(!empty($this->input->post('Filtro_Contacto')))
			$this->db->where('CONTAC.No_Entidad', $this->input->post('Filtro_Contacto'));
			
        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.No_Entidad', $this->input->post('Filtro_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

        $this->db->select('VC.ID_Documento_Cabecera, ALMA.No_Almacen, VC.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Fe_Emision, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, CONTAC.No_Entidad AS No_Contacto, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_entidad . ' AS CONTAC', 'CONTAC.ID_Entidad = VC.ID_Contacto', 'left')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_EstadoDocumento"', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 2)
    	->where('VC.ID_Tipo_Documento', 12);
		
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
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
    	
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
			$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
			
        if($this->input->post('Filtro_Estado') != '')
			$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
			
        if(!empty($this->input->post('Filtro_Contacto')))
			$this->db->where('CONTAC.No_Entidad', $this->input->post('Filtro_Contacto'));
			
        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.No_Entidad', $this->input->post('Filtro_Entidad'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));
	
        $this->db->select('VC.ID_Documento_Cabecera, ALMA.No_Almacen, VC.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Fe_Emision, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, CONTAC.No_Entidad AS No_Contacto, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_entidad . ' AS CONTAC', 'CONTAC.ID_Entidad = VC.ID_Contacto', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_EstadoDocumento"', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 2)
    	->where('VC.ID_Tipo_Documento', 12);
		
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $query = "SELECT
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Documento_Cabecera,
VC.Nu_Estado,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
PROVE.ID_Tipo_Documento_Identidad,
PROVE.ID_Entidad,
PROVE.No_Entidad,
PROVE.Nu_Documento_Identidad,
PROVE.Txt_Direccion_Entidad,
PROVE.Nu_Telefono_Entidad,
PROVE.Nu_Celular_Entidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
VC.Fe_Emision,
VC.Fe_Vencimiento,
VC.Fe_Periodo,
VC.ID_Moneda,
VC.ID_Medio_Pago,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
CONTAC.ID_Entidad AS ID_Contacto,
CONTAC.ID_Tipo_Documento_Identidad AS ID_Tipo_Documento_Identidad_Contacto,
CONTAC.Nu_Documento_Identidad AS Nu_Documento_Identidad_Contacto,
CONTAC.No_Entidad AS No_Contacto,
CONTAC.Txt_Email_Entidad AS Txt_Email_Contacto,
CONTAC.Nu_Celular_Entidad AS Nu_Celular_Contacto,
CONTAC.Nu_Telefono_Entidad AS Nu_Telefono_Contacto,
VD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Producto,
ROUND(VD.Ss_Precio, 2) AS Ss_Precio,
VD.Qt_Producto,
VD.ID_Impuesto_Cruce_Documento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
ROUND(VD.Ss_Descuento, 2) AS Ss_Descuento_Producto,
ROUND(VD.Ss_Descuento_Impuesto, 2) AS Ss_Descuento_Impuesto_Producto,
ROUND(VD.Po_Descuento, 2) AS Po_Descuento_Impuesto_Producto,
ROUND(VD.Ss_Total, 2) AS Ss_Total_Producto,
ICDOCU.Ss_Impuesto,
MP.Nu_Tipo,
IMP.Nu_Tipo_Impuesto,
IMP.No_Impuesto_Breve,
ICDOCU.Po_Impuesto,
VC.Txt_Garantia,
VC.Txt_Glosa,
ROUND(VC.Ss_Descuento, 2) AS Ss_Descuento,
ROUND(VC.Ss_Total, 2) AS Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
VC.Po_Descuento,
MP.No_Medio_Pago,
VC.No_Formato_PDF,
TDOCUIDEN.No_Tipo_Documento_Identidad_Breve,
UM.No_Unidad_Medida
FROM
" . $this->table . " AS VC
JOIN " . $this->table_documento_detalle . " AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN " . $this->table_entidad . " AS PROVE ON(PROVE.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(PROVE.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
LEFT JOIN " . $this->table_entidad . " AS CONTAC ON(CONTAC.ID_Entidad = VC.ID_Contacto)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
WHERE VC.ID_Documento_Cabecera=" . $ID;
        return $this->db->query($query)->result();
    }
    
    public function agregarCompra($arrOrdenCabecera, $arrOrdenDetalle, $arrProveedorNuevo, $arrContactoNuevo){
    	$ID_Entidad=0;
    	if (!empty($arrOrdenCabecera['ID_Entidad']))
    		$ID_Entidad=$arrOrdenCabecera['ID_Entidad'];

		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Organizacion = " . $this->empresa->ID_Organizacion . " AND ID_Tipo_Asiento = 2 AND ID_Entidad = " . $ID_Entidad . " AND ID_Tipo_Documento = " . $arrOrdenCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrOrdenCabecera['ID_Serie_Documento'] . "' AND ID_Numero_Documento = " . $arrOrdenCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			$this->db->trans_begin();
			
			if (is_array($arrProveedorNuevo)){
			    unset($arrOrdenCabecera['ID_Entidad']);
		    	if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
					$arrProveedor = array(
		                'ID_Empresa'					=> $this->empresa->ID_Empresa,
		                'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
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
	    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			if (is_array($arrContactoNuevo)){
			    unset($arrOrdenCabecera['ID_Contacto']);
		    	if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 7 AND ID_Tipo_Documento_Identidad = " . $arrContactoNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrContactoNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad='".$arrContactoNuevo['No_Entidad']."' LIMIT 1")->row()->existe == 0){
					$arrContacto = array(
			            'ID_Empresa'					=> $this->empresa->ID_Empresa,
			            'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
			            'Nu_Tipo_Entidad'				=> 7,//Contacto
			            'ID_Tipo_Documento_Identidad'	=> $arrContactoNuevo['ID_Tipo_Documento_Identidad'],
			            'Nu_Documento_Identidad'		=> $arrContactoNuevo['Nu_Documento_Identidad'],
			            'No_Entidad'					=> $arrContactoNuevo['No_Entidad'],
			            'Txt_Email_Entidad' 			=> $arrContactoNuevo['Txt_Email_Entidad'],
			            'Nu_Telefono_Entidad'			=> $arrContactoNuevo['Nu_Telefono_Entidad'],
			            'Nu_Celular_Entidad'			=> $arrContactoNuevo['Nu_Celular_Entidad'],
			            'Nu_Estado' 					=> 1,
			        );
					$this->db->insert('entidad', $arrContacto);
					$Last_ID_Contacto = $this->db->insert_id();
		    	} else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El contacto ya se encuentra creado, seleccionar Existente');
				}
	    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Contacto" => $Last_ID_Contacto));
			}
			
			$this->db->insert($this->table, $arrOrdenCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			foreach ($arrOrdenDetalle as $row) {
				$documento_detalle[] = array(
					'ID_Empresa'					=> $this->user->ID_Empresa,
					'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
					'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
					'Qt_Producto'					=> $this->security->xss_clean($row['Qt_Producto']),
					'Ss_Precio'						=> $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' 					=> $this->security->xss_clean($row['Ss_SubTotal']),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' 					=> $this->security->xss_clean($row['Ss_Impuesto']),
					'Ss_Total' 						=> round($this->security->xss_clean($row['Ss_Total']), 2),
					'Fe_Emision' => $arrOrdenCabecera['Fe_Emision']
				);
			}
			
			$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			
	        if ($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
	        }
		}
    }
    
    public function actualizarCompra($where, $arrOrdenCabecera, $arrOrdenDetalle, $arrProveedorNuevo, $arrContactoNuevo){
		$this->db->trans_begin();
		
		$this->db->query("SET FOREIGN_KEY_CHECKS=OFF;");
		
		$arrDataModificar = $this->db->query("SELECT ID_Organizacion, ID_Almacen, ID_Documento_Cabecera, Nu_Descargar_Inventario FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $where['ID_Documento_Cabecera'] . " LIMIT 1")->result();
	
		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;
		$ID_Documento_Cabecera = $arrDataModificar[0]->ID_Documento_Cabecera;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;

		$this->db->delete($this->table_documento_detalle, $where);
		
    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Organizacion = " . $row->ID_Organizacion . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where_stock_producto = array('ID_Empresa' => $row->ID_Empresa, 'ID_Organizacion' => $row->ID_Organizacion, 'ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT SUM(Qt_Producto) AS Qt_Producto FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Organizacion = " . $row->ID_Organizacion . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array(
						'ID_Producto'		=> $row->ID_Producto,
						'Qt_Producto'		=> ($Qt_Producto - round($row->Qt_Producto, 6)),
						'Ss_Costo_Promedio'	=> 0.00,
					);
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
	        $this->db->delete('movimiento_inventario');
		}
		
        $this->db->delete($this->table, $where);

		if (is_array($arrProveedorNuevo)){
		    unset($arrOrdenCabecera['ID_Entidad']);
		    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
				$arrProveedor = array(
	                'ID_Empresa'					=> $this->user->ID_Empresa,
	                'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 1,//Proveedor
	                'ID_Tipo_Documento_Identidad'	=> $arrProveedorNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrProveedorNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrProveedorNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrContactoNuevo['Txt_Email_Entidad'],
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
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Entidad" => $Last_ID_Entidad));
		}
		
		if (is_array($arrContactoNuevo)){
		    unset($arrOrdenCabecera['ID_Contacto']);
	    	if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 7 AND ID_Tipo_Documento_Identidad = " . $arrContactoNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrContactoNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad='".$arrContactoNuevo['No_Entidad']."' LIMIT 1")->row()->existe == 0){
				$arrContacto = array(
	                'ID_Empresa'					=> $this->user->ID_Empresa,
	                'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 7,//Contacto
	                'ID_Tipo_Documento_Identidad'	=> $arrContactoNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrContactoNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrContactoNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrContactoNuevo['Txt_Email_Entidad'],
	                'Nu_Telefono_Entidad'			=> $arrContactoNuevo['Nu_Telefono_Entidad'],
	                'Nu_Celular_Entidad'			=> $arrContactoNuevo['Nu_Celular_Entidad'],
	                'Nu_Estado' 					=> 1,
	            );
	    		$this->db->insert('entidad', $arrContacto);
	    		$Last_ID_Contacto = $this->db->insert_id();
	    	} else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El contacto ya se encuentra creado, seleccionar Existente');
			}
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Contacto" => $Last_ID_Contacto));
		}
		
		$arrOrdenCabecera['ID_Almacen'] = $ID_Almacen;
		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Documento_Cabecera" => $arrDataModificar[0]->ID_Documento_Cabecera));
		$this->db->insert($this->table, $arrOrdenCabecera);
		$Last_ID_Documento_Cabecera = $this->db->insert_id();
		
		foreach ($arrOrdenDetalle as $row) {
			$documento_detalle[] = array(
				'ID_Empresa'					=> $this->user->ID_Empresa,
				'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
				'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
				'Qt_Producto'					=> $this->security->xss_clean($row['Qt_Producto']),
				'Ss_Precio'						=> $this->security->xss_clean($row['Ss_Precio']),
				'Ss_SubTotal' 					=> $this->security->xss_clean($row['Ss_SubTotal']),
				'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
				'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
				'Po_Descuento' => $row['Ss_Descuento'],
				'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
				'Ss_Impuesto' 					=> $this->security->xss_clean($row['Ss_Impuesto']),
				'Ss_Total' 						=> round($this->security->xss_clean($row['Ss_Total']), 2),
				'Fe_Emision' => $arrOrdenCabecera['Fe_Emision']
			);
		}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
		
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
        } else {
			$this->db->query("SET FOREIGN_KEY_CHECKS=ON;");
            $this->db->trans_commit();
	        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
        }
    }
    
	public function eliminarOrdenCompra($ID, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete($this->table_documento_detalle);
        
    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Organizacion = " . $row->ID_Organizacion . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Empresa' => $row->ID_Empresa, 'ID_Organizacion' => $row->ID_Organizacion, 'ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT SUM(Qt_Producto) AS Qt_Producto FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Organizacion = " . $row->ID_Organizacion . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					$stock_producto = array(
						'ID_Empresa'		=> $row->ID_Empresa,
						'ID_Almacen'		=> $row->ID_Almacen,
						'ID_Producto'		=> $row->ID_Producto,
						'Qt_Producto'		=> ($Qt_Producto - round($row->Qt_Producto, 6)),
						'Ss_Costo_Promedio'	=> 0.00,
					);
					$this->db->update('stock_producto', $stock_producto, $where);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('movimiento_inventario');
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
    
	public function estadoOrdenCompra($ID, $Nu_Descargar_Inventario, $Nu_Estado){
		$this->db->trans_begin();
        
        $where_orden_compra = array('ID_Documento_Cabecera' => $ID);
        $arrData = array(
			'Nu_Estado' => $Nu_Estado,
		);
		$this->db->update('documento_cabecera', $arrData, $where_orden_compra);
                
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
        } else {
            $this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
	}
    
	public function duplicarOrdenCompra($ID){
		$this->db->trans_begin();
        
        $query_cabecera = " SELECT
ID_Empresa,
ID_Organizacion,
ID_Almacen,
ID_Entidad,
ID_Contacto,
ID_Tipo_Asiento,
ID_Tipo_Documento,
ID_Serie_Documento,
ID_Numero_Documento,
ID_Matricula_Empleado,
Fe_Emision,
ID_Medio_Pago,
ID_Rubro,
ID_Moneda,
Fe_Vencimiento,
Fe_Periodo,
Nu_Correlativo,
Nu_Descargar_Inventario,
ID_Lista_Precio_Cabecera,
Txt_Glosa,
Ss_Descuento,
Ss_Total,
Ss_Total_Saldo,
Ss_Percepcion,
Fe_Detraccion,
Nu_Detraccion,
Nu_Estado,
Txt_Garantia
FROM
documento_cabecera
WHERE
ID_Documento_Cabecera = " . $ID . " LIMIT 1";
		$arrCabecera = $this->db->query($query_cabecera)->result();
		
		foreach ($arrCabecera as $row) {
			//$ID_Numero_Documento = $this->db->query("SELECT ID_Numero_Documento FROM documento_cabecera WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND ID_Tipo_Documento = 12 AND ID_Serie_Documento = '" . $row->ID_Serie_Documento . "' ORDER BY CONVERT(ID_Numero_Documento, SIGNED INTEGER) DESC LIMIT 1;")->row()->ID_Numero_Documento;
			//settype($ID_Numero_Documento, "int");
			$dEmision = $row->Fe_Emision;
			$documento_cabecera = array(
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Organizacion' => $row->ID_Organizacion,
				'ID_Almacen' => $row->ID_Almacen,
				'ID_Entidad' => $row->ID_Entidad,
				'ID_Contacto' => $row->ID_Contacto,
				'ID_Tipo_Asiento' => $row->ID_Tipo_Asiento,
				'ID_Tipo_Documento' => $row->ID_Tipo_Documento,
				'ID_Serie_Documento' => $row->ID_Serie_Documento,
				//'ID_Numero_Documento' => $ID_Numero_Documento + 1,
				'ID_Numero_Documento' => dateNow('numero_ymdhms'),
				'ID_Matricula_Empleado' => $row->ID_Matricula_Empleado,
				'Fe_Emision' => $row->Fe_Emision,
				'ID_Medio_Pago' => $row->ID_Medio_Pago,
				'ID_Rubro' => $row->ID_Rubro,
				'ID_Moneda' => $row->ID_Moneda,
				'Fe_Vencimiento' => $row->Fe_Vencimiento,
				'Fe_Periodo' => $row->Fe_Periodo,
				'Nu_Correlativo' => $row->Nu_Correlativo,
				'Nu_Descargar_Inventario' => $row->Nu_Descargar_Inventario,
				'ID_Lista_Precio_Cabecera' => $row->ID_Lista_Precio_Cabecera,
				'Txt_Glosa' => $row->Txt_Glosa,
				'Ss_Descuento' => $row->Ss_Descuento,
				'Ss_Total' => $row->Ss_Total,
				'Ss_Total_Saldo' => $row->Ss_Total_Saldo,
				'Ss_Percepcion' => $row->Ss_Percepcion,
				'Fe_Detraccion' => $row->Fe_Detraccion,
				'Nu_Detraccion' => $row->Nu_Detraccion,
				'Nu_Estado' => 5,
			);
    	}
		$this->db->insert($this->table, $documento_cabecera);
		$ID_Documento_Cabecera = $this->db->insert_id();
        
        $query_detalle = " SELECT
ID_Empresa,
ID_Producto,
Qt_Producto,
Ss_Precio,
Ss_Descuento,
Ss_Descuento_Impuesto,
Po_Descuento,
Ss_SubTotal, 
ID_Impuesto_Cruce_Documento,
Ss_Impuesto,
Ss_Total
FROM
documento_detalle
WHERE
ID_Documento_Cabecera = " . $ID;
		$arrDetalle = $this->db->query($query_detalle)->result();
		
		foreach ($arrDetalle as $row) {
			$documento_detalle[] = array(
				'ID_Empresa'					=> $row->ID_Empresa,
				'ID_Documento_Cabecera'			=> $ID_Documento_Cabecera,
				'ID_Producto'					=> $row->ID_Producto,
				'Qt_Producto'					=> $row->Qt_Producto,
				'Ss_Precio'						=> $row->Ss_Precio,
				'Ss_Descuento'					=> $row->Ss_Descuento,
				'Ss_Descuento_Impuesto'			=> $row->Ss_Descuento_Impuesto,
				'Po_Descuento'					=> $row->Po_Descuento,
				'Ss_SubTotal'					=> $row->Ss_SubTotal,
				'ID_Impuesto_Cruce_Documento'	=> $row->ID_Impuesto_Cruce_Documento,
				'Ss_Impuesto' => $row->Ss_Impuesto,
				'Ss_Total' => $row->Ss_Total,
				'Fe_Emision' => $dEmision
			);
    	}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
 
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al duplicar orden de compra');	
        } else {
            $this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro agregado');
        }
	}
}
