<?php
class DepartamentoModel extends CI_Model{
	var $table              = 'departamento';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_pais         = 'pais';
	var $table_entidad      = 'entidad';
	
    var $column_order = array('No_Pais', 'No_Departamento', 'No_Departamento_Breve');
    var $column_search = array('No_Pais', 'No_Departamento');
    var $order = array('No_Pais' => 'asc', 'No_Departamento' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Departamentos')=='Pais' ){
            $this->db->like('No_Pais', $this->input->post('Global_Filter'));
        }
        
        if( $this->input->post('Filtros_Departamentos')=='Departamento' ){
            $this->db->like('No_Departamento', $this->input->post('Global_Filter'));
        }
        
		$this->db->select('ID_Departamento, No_Pais, No_Departamento, No_Departamento_Breve, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
    	->join($this->table_pais, $this->table_pais . '.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
         
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
        $this->db->where('ID_Departamento', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarDepartamento($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Pais=" . $data['ID_Pais'] . " AND No_Departamento='" . $data['No_Departamento'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarDepartamento($where, $data, $EID_Pais, $ENo_Departamento){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( ($EID_Pais != $data['ID_Pais'] || $ENo_Departamento != $data['No_Departamento']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Pais=" . $data['ID_Pais'] . " AND No_Departamento='" . $data['No_Departamento'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarDepartamento($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_entidad . " WHERE ID_Departamento=" . $ID . " LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El departamento tiene movimiento(s)');
		}else{
			$this->db->where('ID_Departamento', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return $response;
	}
}
