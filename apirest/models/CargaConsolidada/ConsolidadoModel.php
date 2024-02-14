<?php
class ConsolidadoModel extends CI_Model{
	var $table = 'carga_consolidada';
	var $table_moneda = 'moneda';
	var $table_producto = 'producto';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*')
		->from($this->table)
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
        $this->db->select($this->table . '.*');
        $this->db->from($this->table);
        $this->db->where($this->table . '.ID_Carga_Consolidada',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCliente($data, $addProducto){
		if ( $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Carga_Consolidada='" . $data['No_Carga_Consolidada'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El nombre ya existe');
		} else {
			$this->db->trans_begin();
			if ( $this->db->insert($this->table, $data) > 0 ) {
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
    }
    
    public function actualizarCliente($where, $data, $ENo_Carga_Consolidada){
		if( $ENo_Carga_Consolidada != $data['No_Carga_Consolidada'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Carga_Consolidada='" . $data['No_Carga_Consolidada'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El nombre ' . $data['No_Carga_Consolidada'] . ' ya existe');
		}else{
			$this->db->trans_begin();
			if ( $this->db->update($this->table, $data, $where) > 0 ) {
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
    }
    
	public function eliminarCliente($ID){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM carga_consolidada_pedido_cabecera WHERE ID_Carga_Consolidada=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene movimiento(s)');
		} else {
			$this->db->where('ID_Carga_Consolidada', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
