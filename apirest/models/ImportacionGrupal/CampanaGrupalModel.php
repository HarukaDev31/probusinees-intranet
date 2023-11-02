<?php
class CampanaGrupalModel extends CI_Model{
	var $table = 'importacion_grupal_cabecera';
	var $table_moneda = 'moneda';
	var $table_importacion_grupal_detalle = 'importacion_grupal_detalle';
	var $table_producto = 'producto';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, MONE.No_Moneda')
		->from($this->table)
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = ' . $this->table . '.ID_Moneda', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);
        
		if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($ID){
        $this->db->select($this->table . '.*, IGD.ID_Producto, ITEM.No_Producto');
        $this->db->from($this->table);
    	$this->db->join($this->table_importacion_grupal_detalle . ' AS IGD', 'IGD.ID_Importacion_Grupal = ' . $this->table . '.ID_Importacion_Grupal', 'join');
    	$this->db->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = IGD.ID_Producto', 'join');
        $this->db->where($this->table . '.ID_Importacion_Grupal',$ID);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function agregarCliente($data, $addProducto){
		$this->db->trans_begin();

		if ( $this->db->insert($this->table, $data) > 0 ) {
			$ID_Cabecera = $this->db->insert_id();
			foreach($addProducto as $row){
				$detalle[] = array(
					'ID_Empresa'				=> $this->user->ID_Empresa,
					'ID_Organizacion'			=> $this->user->ID_Organizacion,
					'ID_Importacion_Grupal' 	=> $ID_Cabecera,
					'ID_Producto'				=> $this->security->xss_clean($row['id_item']),
				);
			}
			$this->db->insert_batch($this->table_importacion_grupal_detalle, $detalle);
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al guradar');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al guradar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
    }
    
    public function actualizarCliente($where, $data, $arrProducto){
		$this->db->trans_begin();
		if ( $this->db->update($this->table, $data, $where) > 0 ) {
	    	$this->db->where('ID_Importacion_Grupal', $where['ID_Importacion_Grupal']);
        	$this->db->delete($this->table_importacion_grupal_detalle);
			
			foreach($arrProducto as $row){
				$detalle[] = array(
					'ID_Empresa'				=> $this->user->ID_Empresa,
					'ID_Organizacion'			=> $this->user->ID_Organizacion,
					'ID_Importacion_Grupal' 	=> $where['ID_Importacion_Grupal'],
					'ID_Producto'				=> $this->security->xss_clean($row['id_item']),
				);
			}
			$this->db->insert_batch($this->table_importacion_grupal_detalle, $detalle);
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
    }
    
	public function eliminarCliente($ID){
		/*
		if ($this->db->query("SELECT COUNT(*) AS existe FROM pedido_cabecera WHERE ID_Entidad=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene movimiento(s)');
		} else {
		*/
			$this->db->where('ID_Importacion_Grupal', $ID);
            $this->db->delete($this->table_importacion_grupal_detalle);

			$this->db->where('ID_Importacion_Grupal', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		//}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
