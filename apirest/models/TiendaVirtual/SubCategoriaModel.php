<?php
class SubCategoriaModel extends CI_Model{
	var $table              = 'subfamilia';
    var $table_familia = 'familia';
	var $table_producto     = 'producto';
	
    var $column_order = array('No_Sub_Familia', null);
    var $column_search = array('');
    var $order = array('Nu_Activar_Lae_Shop' => 'desc');
	    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if(!empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Categorias')=='Categoria')
            $this->db->like('No_Familia', $this->input->post('Global_Filter'));
        if(!empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Categorias')=='Sub_Categoria')
            $this->db->like('No_Sub_Familia', $this->input->post('Global_Filter'));
        
        $this->db->select('ID_Sub_Familia, F.No_Familia, No_Sub_Familia, Nu_Activar_Lae_Shop')
		->from($this->table)
		->join($this->table_familia . ' AS F', 'F.ID_Familia = ' . $this->table .  '.ID_Familia', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);

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
        $this->db->where('ID_Sub_Familia',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCategoria($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Sub_Familia='" . $data['No_Sub_Familia'] . "' AND Nu_Activar_Lae_Shop=1 LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ' . $data['No_Sub_Familia'] . ' ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarCategoria($where, $data, $ENo_Sub_Familia){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( $ENo_Sub_Familia != $data['No_Sub_Familia'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Sub_Familia='" . $data['No_Sub_Familia'] . "' AND Nu_Activar_Lae_Shop=1 LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ' . $data['No_Sub_Familia'] . ' ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarCategoria($ID){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Sub_Familia = " . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La categoria tiene producto(s)');
		} else {
			$this->db->where('ID_Sub_Familia', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }
}
