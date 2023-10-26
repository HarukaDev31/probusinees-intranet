<?php
class TipoMovimientoInventarioModel extends CI_Model{
	var $table              = 'tipo_movimiento';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_movimiento_inventario = 'movimiento_inventario';
	
    var $column_order = array('No_Tipo_Movimiento', 'Nu_Sunat_Codigo', 'Nu_Tipo_Movimiento', 'Nu_Costear',);
    var $column_search = array('No_Tipo_Movimiento');
    var $order = array('No_Tipo_Movimiento' => 'asc',);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_TiposMovimiento') == 'Tipo_Movimiento' ){
            $this->db->like('No_Tipo_Movimiento', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Tipo_Movimiento, No_Tipo_Movimiento, Nu_Sunat_Codigo, Nu_Tipo_Movimiento, Nu_Costear, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
         
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Tipo_Movimiento',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTipoMovimientoInventario($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE No_Tipo_Movimiento='" . $data['No_Tipo_Movimiento'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarTipoMovimientoInventario($where, $data, $ENo_Tipo_Movimiento){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( $ENo_Tipo_Movimiento != $data['No_Tipo_Movimiento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE No_Tipo_Movimiento='" . $data['No_Tipo_Movimiento'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarTipoMovimientoInventario($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_movimiento_inventario . " WHERE ID_Tipo_Movimiento=" . $ID . " LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El tipo operacion tiene movimiento(s)');
		}else{
			$this->db->where('ID_Tipo_Movimiento', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return $response;
	}
}
