<?php
class Matricular_empleado_model extends CI_Model{
	var $table                      = 'matricula_empleado';
	var $table_empleado             = 'entidad';
	var $table_documento_cabecera   = 'documento_cabecera';
	
    var $column_order = array('Fe_Matricula', 'Nu_Turno', 'EMP.No_Entidad');
    var $column_search = array('');
    var $order = array('Fe_Matricula' => 'asc',);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if($this->input->post('Filtro_Empleado'))
        	$this->db->where('EMP.ID_Entidad', $this->input->post('Filtro_Empleado'));
    
        $this->db->where("Fe_Matricula BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");
        
        $this->db->select('ID_Matricula_Empleado, Fe_Matricula, EMP.No_Entidad')
		->from($this->table)
    	->join($this->table_empleado . ' AS EMP', 'EMP.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->where('EMP.ID_Empresa', $this->user->ID_Empresa);

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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Matricula_Empleado', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMatricula_Empleado($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al insertar');
		if($this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Entidad='" . $data['ID_Entidad'] . "' AND Fe_Matricula='" . $data['Fe_Matricula'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarMatricula_Empleado($where, $data){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al modificar');
		if( $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Entidad='" . $data['ID_Entidad'] . "' AND Fe_Matricula='" . $data['Fe_Matricula'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarMatricula_Empleado($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Matricula_Empleado=" . $ID . " LIMIT 1")->row()->existe > 0) {
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El empleado tiene movimiento(s)');
		} else {
			$this->db->where('ID_Matricula_Empleado', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return $response;
	}
}
