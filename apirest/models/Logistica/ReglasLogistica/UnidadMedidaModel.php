<?php
class UnidadMedidaModel extends CI_Model{
	var $table              = 'unidad_medida';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_producto     = 'producto';
	
    var $column_order = array('No_Unidad_Medida', 'Nu_Sunat_Codigo');
    var $column_search = array('No_Unidad_Medida');
    var $order = array('No_Unidad_Medida' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_UnidadesMedida') == 'UnidadMedida' )
            $this->db->like('No_Unidad_Medida', $this->input->post('Global_Filter'));
        
        $this->db->select('ID_Unidad_Medida, No_Unidad_Medida, Nu_Sunat_Codigo, ' . $this->table . '.Nu_Estado')
		->from($this->table)
    	->where('ID_Empresa', $this->user->ID_Empresa);
         
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
    	$this->db->where('ID_Empresa', $this->user->ID_Empresa);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Unidad_Medida', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarUnidadMedida($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Sunat_Codigo='" . $data['Nu_Sunat_Codigo'] . "' AND No_Unidad_Medida='" . $data['No_Unidad_Medida'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarUnidadMedida($where, $data, $ENo_Unidad_Medida, $ENu_Sunat_Codigo){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if ( ($ENu_Sunat_Codigo != $data['Nu_Sunat_Codigo'] || $ENo_Unidad_Medida != $data['No_Unidad_Medida']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Sunat_Codigo='" . $data['Nu_Sunat_Codigo'] . "' AND No_Unidad_Medida='" . $data['No_Unidad_Medida'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarUnidadMedida($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Unidad_Medida = " . $ID . " LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La unidad medida tiene producto(s)');
		}else{
			$this->db->where('ID_Unidad_Medida', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return $response;
	}
}
