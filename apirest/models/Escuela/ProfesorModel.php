<?php
class ProfesorModel extends CI_Model{
	var $table                          = 'entidad';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_distrito                 = 'distrito';
	var $table_matricula_empleado       = 'matricula_empleado';
	
    var $column_order = array(null, 'Nu_Documento_Identidad', 'Nu_Documento_Identidad', 'No_Entidad');
    var $column_search = array('Nu_Documento_Identidad', 'No_Entidad');
    var $order = array('No_Entidad' => 'asc',);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Empleados') == 'Empleado' ){
            $this->db->like('No_Entidad', $this->input->post('Global_Filter'));
        } else if ( $this->input->post('Filtros_Empleados') == 'DNI' ){
        	$this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Entidad, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Entidad, DISTRI.No_Distrito, Txt_Direccion_Entidad, ' . $this->table . '.Nu_Estado')
		->from($this->table)
    	->join($this->table_distrito . ' AS DISTRI', 'DISTRI.ID_Distrito = ' . $this->table . '.ID_Distrito', 'left')
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
		->where('ID_Empresa', $this->user->ID_Empresa)
		->where('ID_Organizacion', $this->user->ID_Organizacion)
    	->where('Nu_Tipo_Entidad', 10);
         
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
        $this->db->where('ID_Entidad',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarEmpleado($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Entidad=10 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if( !empty($data['Nu_Pin_Caja']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Entidad=10 AND Nu_Pin_Caja='" . $data['Nu_Pin_Caja'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El PIN ya existe');
		} else {
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarEmpleado($where, $data, $ENu_Documento_Identidad, $ENu_Pin_Caja){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al modificar');
		if( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Entidad=10 AND Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if( !empty($data['Nu_Pin_Caja']) && $ENu_Pin_Caja != $data['Nu_Pin_Caja'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Tipo_Entidad=10 AND Nu_Pin_Caja='" . $data['Nu_Pin_Caja'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarEmpleado($ID){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM matricula_alumno WHERE ID_Entidad_Profesor=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El profesor esta matriculado');
		} else {
			$this->db->where('ID_Entidad', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}
}
