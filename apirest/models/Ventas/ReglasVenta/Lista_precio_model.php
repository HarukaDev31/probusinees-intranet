<?php
class Lista_precio_model extends CI_Model{
	var $table                      = 'lista_precio_cabecera';
	var $table_organizacion         = 'organizacion';
	var $table_almacen              = 'almacen';
	var $table_entidad              = 'entidad';
	var $table_moneda               = 'moneda';
	var $table_tabla_dato           = 'tabla_dato';
	var $table_lista_precio_detalle = 'lista_precio_detalle';
	var $table_producto             = 'producto';
	var $table_documento_cabecera   = 'documento_cabecera';
	
    var $column_order = array('No_Almacen', 'No_Lista_Precio', 'No_Signo', 'Nu_Tipo_Lista_Precio', 'No_Entidad');
    var $column_search = array('No_Lista_Precio');
    var $order = array('LPC.Fe_Registro' => 'desc');
    
    private $ID_Lista_Precio_Cabecera;
	private $_batchImport;
	
	public function __construct(){
		parent::__construct();
	}
 
    public function setBatchImport($ID_Lista_Precio_Cabecera, $arrListaPrecio) {
        $this->ID_Lista_Precio_Cabecera = $ID_Lista_Precio_Cabecera;
        $this->_batchImport = $arrListaPrecio;
    }
    
    public function importData() {
	    $ID_Empresa = $this->user->ID_Empresa;
	    $ID_Usuario = $this->user->ID_Usuario;
	    $Fe_Creacion = dateNow('fecha_hora');
        $ID_Producto = 0;
        
        foreach ($this->_batchImport as $row) {
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM producto WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->existe > 0)
        		$ID_Producto = $this->db->query("SELECT ID_Producto FROM producto WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Codigo_Barra='" . $row['Nu_Codigo_Barra'] . "' LIMIT 1")->row()->ID_Producto;
        	
        	if ($this->db->query("SELECT COUNT(*) AS existe FROM lista_precio_detalle WHERE ID_Lista_Precio_Cabecera=" . $this->ID_Lista_Precio_Cabecera . " AND ID_Producto=" . $ID_Producto . " LIMIT 1")->row()->existe == 0){
            	$arrListaPrecio[] = array(
					'ID_Lista_Precio_Cabecera'	=> $this->ID_Lista_Precio_Cabecera,
					'ID_Producto'	            => $ID_Producto,
					'Ss_Precio_Interno'         => $row['Ss_Precio_Interno'],
					'Po_Descuento'		        => $row['Po_Descuento'],
					'Ss_Precio' 		        => $row['Ss_Precio'],
					'Nu_Estado'                 => 1,
            	);
        	} else {
        		$ID_Lista_Precio_Detalle = $this->db->query("SELECT ID_Lista_Precio_Detalle FROM lista_precio_detalle WHERE ID_Lista_Precio_Cabecera=" . $this->ID_Lista_Precio_Cabecera . " AND ID_Producto=" . $ID_Producto . " LIMIT 1")->row()->ID_Lista_Precio_Detalle;
        		$arrListaPrecioUPD[] = array(
					'ID_Lista_Precio_Detalle'   => $ID_Lista_Precio_Detalle,
					'ID_Lista_Precio_Cabecera'	=> $this->ID_Lista_Precio_Cabecera,
					'ID_Producto'	            => $ID_Producto,
					'Ss_Precio_Interno'         => $row['Ss_Precio_Interno'],
					'Po_Descuento'		        => $row['Po_Descuento'],
					'Ss_Precio' 		        => $row['Ss_Precio'],
					'Nu_Estado'                 => 1,
            	);
        	}
        }
        
        $bStatus=false;
        if (isset($arrListaPrecio) && count($arrListaPrecio) > 0)
    		$this->db->insert_batch($this->table_lista_precio_detalle, $arrListaPrecio);
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	if (isset($arrListaPrecioUPD) && count($arrListaPrecioUPD) > 0)
    		$this->db->update_batch($this->table_lista_precio_detalle, $arrListaPrecioUPD, 'ID_Lista_Precio_Detalle');
    		if ($this->db->affected_rows() > 0)
    			$bStatus = true;
    	
    	unset($arrListaPrecio);
    	unset($arrListaPrecioUPD);
    	
    	return $bStatus;
    }
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio' ){
            $this->db->like('No_Lista_Precio', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Cliente' ){
        	$this->db->like('No_Entidad', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'NumeroDocumentoIdentidad' ){
        	$this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Lista_Precio_Cabecera, ALMA.ID_Almacen, No_Almacen, No_Lista_Precio, No_Signo, Nu_Tipo_Lista_Precio, No_Entidad, LPC.Nu_Estado')
		->from($this->table . ' AS LPC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = LPC.ID_Almacen', 'left')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = LPC.ID_Entidad', 'left')
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = LPC.ID_Moneda', 'join')
		->where('LPC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('LPC.ID_Organizacion', $this->empresa->ID_Organizacion);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
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
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio' ){
            $this->db->like('No_Lista_Precio', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Cliente' ){
        	$this->db->like('No_Entidad', $this->input->post('Global_Filter'));
        } else if ( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'NumeroDocumentoIdentidad' ){
        	$this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Lista_Precio_Cabecera, No_Almacen, No_Lista_Precio, No_Signo, Nu_Tipo_Lista_Precio, No_Entidad, LPC.Nu_Estado')
		->from($this->table . ' AS LPC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = LPC.ID_Almacen', 'left')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = LPC.ID_Entidad', 'left')
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = LPC.ID_Moneda', 'join')
		->where('LPC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('LPC.ID_Organizacion', $this->empresa->ID_Organizacion);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Lista_Precio_Cabecera',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarLista_Precio($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Lista_Precio=" . $data['Nu_Tipo_Lista_Precio'] . " AND No_Lista_Precio='" . $data['No_Lista_Precio'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarLista_Precio($where, $data, $EID_Organizacion, $ENo_Lista_Precio){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( ($EID_Organizacion != $data['ID_Organizacion'] || $ENo_Lista_Precio != $data['No_Lista_Precio']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Lista_Precio=" . $data['Nu_Tipo_Lista_Precio'] . " AND No_Lista_Precio='" . $data['No_Lista_Precio'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarLista_Precio($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_lista_precio_detalle . " WHERE ID_Lista_Precio_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0) {
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La lista de precio tiene asignados precio(s)');
		} else if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Lista_Precio_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0) {
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La Lista de precio tiene movimiento(s)');
		} else {
			$this->db->where('ID_Lista_Precio_Cabecera', $ID);
            $this->db->delete($this->table);
    	    if ( $this->db->affected_rows() > 0 ) {
    	        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
    	    }
		}
        return $response;
	}
    
	//Lista de precios detalle
    var $column_order_precio = array('No_Producto', 'Ss_Precio_Interno', 'Po_Descuento', 'Ss_Precio');
    var $order_precio = array('No_Producto' => 'asc');
    
	public function _get_datatables_query_precio(){
        if( $this->input->post('Filtro_ID_Lista_Precio_Cabecera') != '' )
        	$this->db->where('LPD.ID_Lista_Precio_Cabecera', $this->input->post('Filtro_ID_Lista_Precio_Cabecera'));
            
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio_Producto' )
			$this->db->like('No_Producto', $this->input->post('Global_Filter'));
			
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio_UPC' )
            $this->db->like('ITEM.Nu_Codigo_Barra', $this->input->post('Global_Filter'));

        $this->db->select('LPD.ID_Lista_Precio_Cabecera, ID_Lista_Precio_Detalle, LPD.ID_Producto, ITEM.Nu_Codigo_Barra, ITEM.No_Producto, LPD.Ss_Precio_Interno, LPD.Po_Descuento, LPD.Ss_Precio, LPD.Nu_Estado')
		->from($this->table . ' AS LPC')
		->join($this->table_lista_precio_detalle . ' AS LPD', 'LPD.ID_Lista_Precio_Cabecera = LPC.ID_Lista_Precio_Cabecera', 'join')
		->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = LPD.ID_Producto', 'join')
    	->where('LPC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('LPC.ID_Organizacion', $this->empresa->ID_Organizacion);
         
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order_precio[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order_precio)) {
            $order = $this->order_precio;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables_precio(){
        $this->_get_datatables_query_precio();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_precio(){
        $this->_get_datatables_query_precio();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_precio(){
        if( $this->input->post('Filtro_ID_Lista_Precio_Cabecera') != '' )
        	$this->db->where('LPD.ID_Lista_Precio_Cabecera', $this->input->post('Filtro_ID_Lista_Precio_Cabecera'));
            
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio_Producto' )
			$this->db->like('No_Producto', $this->input->post('Global_Filter'));
			
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Tabla') == 'Lista_Precio_UPC' )
            $this->db->like('ITEM.Nu_Codigo_Barra', $this->input->post('Global_Filter'));

        $this->db->select('LPD.ID_Lista_Precio_Cabecera, ID_Lista_Precio_Detalle, LPD.ID_Producto, ITEM.Nu_Codigo_Barra, ITEM.No_Producto, LPD.Ss_Precio_Interno, LPD.Po_Descuento, LPD.Ss_Precio, LPD.Nu_Estado')
		->from($this->table . ' AS LPC')
		->join($this->table_lista_precio_detalle . ' AS LPD', 'LPD.ID_Lista_Precio_Cabecera = LPC.ID_Lista_Precio_Cabecera', 'join')
		->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = LPD.ID_Producto', 'join')
    	->where('LPC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('LPC.ID_Organizacion', $this->empresa->ID_Organizacion);
        return $this->db->count_all_results();
    }

    public function get_by_id_precio_producto($ID){
        $this->db->select('ID_Lista_Precio_Cabecera, ID_Lista_Precio_Detalle, LPD.ID_Producto, ITEM.No_Producto, ROUND(LPD.Ss_Precio_Interno, 2) AS Ss_Precio_Interno, LPD.Po_Descuento, ROUND(LPD.Ss_Precio, 2) AS Ss_Precio, LPD.Nu_Estado')
		->from($this->table_lista_precio_detalle . ' AS LPD')
		->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = LPD.ID_Producto', 'join')
        ->where('ID_Lista_Precio_Detalle', $ID);
        $query = $this->db->get();
        return $query->row();
    }

    public function agregarLista_Precio_Producto($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_lista_precio_detalle . " WHERE ID_Lista_Precio_Cabecera=" . $data['ID_Lista_Precio_Cabecera'] . " AND ID_Producto=" . $data['ID_Producto'] . " LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table_lista_precio_detalle, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarLista_Precio_Producto($where, $data, $EID_Producto){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( $EID_Producto != $data['ID_Producto'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_lista_precio_detalle . " WHERE ID_Lista_Precio_Cabecera=" . $data['ID_Lista_Precio_Cabecera'] . " AND ID_Producto=" . $data['ID_Producto'] . " LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table_lista_precio_detalle, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarLista_Precio_Producto($ID){
		$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Error al eliminar');
		$this->db->where('ID_Lista_Precio_Detalle', $ID);
        $this->db->delete($this->table_lista_precio_detalle);
	    if ( $this->db->affected_rows() > 0 ) {
	        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
	    }
        return $response;
	}
    
	public function getListaPrecioxId($ID_Almacen){
		$query = "SELECT
ID_Lista_Precio_Cabecera,
No_Lista_Precio,
Nu_Tipo_Lista_Precio
FROM
lista_precio_cabecera
WHERE ID_Almacen=" . $ID_Almacen . "
AND Nu_Estado=1
ORDER BY
No_Lista_Precio";
		return $this->db->query($query)->result();
	}

	public function replicarListaPrecio($arrPost){
		//validar que tenga lista de precios origen tenga items para replicar
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_lista_precio_detalle . " WHERE ID_Lista_Precio_Cabecera=" . $arrPost['ID_Lista_Precio_Cabecera_Replicacion_Precio'] . " LIMIT 1")->row()->existe <= 0)
			return array('sStatus' => 'warning', 'sMessage' => 'Lista precio no tiene productos con precios');


		$query = "SELECT * FROM lista_precio_detalle WHERE ID_Lista_Precio_Cabecera=" . $arrPost['ID_Lista_Precio_Cabecera_Replicacion_Precio'] . "";
		$arrResponse = $this->db->query($query)->result();

		foreach($arrResponse as $row){
			$objListaPrecioDetalle = $this->db->query("SELECT * FROM lista_precio_detalle WHERE ID_Lista_Precio_Cabecera = " . $arrPost['ID_Lista_Precio_Cabecera_Replicacion_Precio_Destino'] . " AND ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row();
			if (is_object($objListaPrecioDetalle)){
				$arrUPDATE[] = array(
					'ID_Lista_Precio_Detalle' => $objListaPrecioDetalle->ID_Lista_Precio_Detalle,
					'Ss_Precio_Interno' => $row->Ss_Precio_Interno,
					'Po_Descuento' => $row->Po_Descuento,
					'Ss_Precio' => $row->Ss_Precio,
					'Nu_Estado' => $row->Nu_Estado
				);
			} else {
				$arrINSERT[] = array(
					'ID_Lista_Precio_Cabecera' => $arrPost['ID_Lista_Precio_Cabecera_Replicacion_Precio_Destino'],
					'ID_Producto' => $row->ID_Producto,
					'Ss_Precio_Interno' => $row->Ss_Precio_Interno,
					'Po_Descuento' => $row->Po_Descuento,
					'Ss_Precio' => $row->Ss_Precio,
					'Nu_Estado' => $row->Nu_Estado
				);
			}
		}
		
		$this->db->trans_begin();
		//update masivo
		if(!empty($arrUPDATE))
			$this->db->update_batch('lista_precio_detalle', $arrUPDATE, 'ID_Lista_Precio_Detalle');

		//insert masivo
		if(!empty($arrINSERT))
    		$this->db->insert_batch('lista_precio_detalle', $arrINSERT);

	    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'error', 'sMessage' => 'Problemas al replicar');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success','sMessage' => 'Replicado satisfactoriamente');
		}
	}
}
