<?php
class ImpuestoModel extends CI_Model{
	var $table                          = 'impuesto';
	var $table_empresa                  = 'empresa';
	var $table_impuesto_cruce_documento = 'impuesto_cruce_documento';
	
    var $column_order = array('EMP.No_Empresa', 'No_Impuesto', 'No_Impuesto_Breve', 'Nu_Tipo_Impuesto');
    var $column_search = array('');
    var $order = array('EMP.No_Empresa' => 'asc', 'No_Impuesto' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('Filtros_Impuestos') == 'Impuesto' )
            $this->db->like('No_Impuesto', $this->input->post('Global_Filter'));

        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Impuesto, No_Impuesto, No_Impuesto_Breve, Nu_Tipo_Impuesto')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
         
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
        $this->db->where('ID_Impuesto', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarImpuesto($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND No_Impuesto='" . $data['No_Impuesto'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarImpuesto($where, $data, $ENo_Impuesto){
		if( $ENo_Impuesto != $data['No_Impuesto'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND No_Impuesto='" . $data['No_Impuesto'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarImpuesto($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_impuesto_cruce_documento . " WHERE ID_Impuesto = " . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El impuesto tiene asignado monto(s)');
		} else {
			$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            $this->db->where('ID_Impuesto', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
