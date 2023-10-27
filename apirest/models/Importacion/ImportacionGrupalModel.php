<?php
class ImportacionGrupalModel extends CI_Model{
	var $table = 'importacion_grupal_cabecera';
	var $table_moneda = 'moneda';
	
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
        $this->db->from($this->table);
        $this->db->where('ID_Importacion_Grupal',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCliente($data){
		if ( $this->db->insert($this->table, $data) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarCliente($where, $data){
		if ( $this->db->update($this->table, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarCliente($ID){
		/*
		if ($this->db->query("SELECT COUNT(*) AS existe FROM pedido_cabecera WHERE ID_Entidad=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene movimiento(s)');
		} else {
		*/
			$this->db->where('ID_Entidad', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		//}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
