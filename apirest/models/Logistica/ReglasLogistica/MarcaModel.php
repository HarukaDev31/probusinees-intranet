<?php
class MarcaModel extends CI_Model{
	var $table              = 'marca';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_producto     = 'producto';
	
    var $column_order = array('No_Marca', null);
    var $column_search = array('No_Marca',);
    var $order = array('No_Marca' => 'asc',);
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Marcas') == 'Marca' ){
            $this->db->like('No_Marca', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Marca, No_Marca, ' . $this->table . '.Nu_Estado')
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
        $this->db->where('ID_Marca',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMarca($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND No_Marca='" . $data['No_Marca'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarMarca($where, $data, $ENo_Marca){
		if( $ENo_Marca != $data['No_Marca'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND No_Marca='" . $data['No_Marca'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarMarca($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Marca=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La marca tiene producto(s)');
		}else{
			$this->db->where('ID_Marca', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
