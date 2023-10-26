<?php
class CategoriaModel extends CI_Model{
	var $table              = 'familia';
	var $table_subfamilia   = 'subfamilia';
	var $table_producto     = 'producto';
	
    var $column_order = array('Nu_Orden', 'No_Familia', 'No_Html_Color', null);
    var $column_search = array('Nu_Orden', 'No_Familia');
    var $order = array('No_Familia' => 'asc',);
	
    private $upload_path = '../assets/images/categorias/';
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Categorias')=='Categoria' ){
            $this->db->like('No_Familia', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Familia, Nu_Orden, No_Familia, No_Imagen_Categoria, No_Html_Color, No_Imagen_Url_Categoria, ' . $this->table . '.Nu_Estado, Nu_Version_Imagen')
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
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Familia',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarCategoria($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Familia='" . $data['No_Familia'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe ' . $data['No_Familia']);
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarCategoria($where, $data, $ENo_Familia, $ENu_Orden){
		if( $ENo_Familia != $data['No_Familia'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Familia='" . $data['No_Familia'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ' . $data['No_Familia'] . ' ya existe');
		}else if( $ENu_Orden != $data['Nu_Orden'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND Nu_Orden='" . $data['Nu_Orden'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El Nro. Orden ' . $data['Nu_Orden'] . ' ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

	public function eliminarCategoria($ID){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_subfamilia . " WHERE ID_Familia = " . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La categoria esta enlazada con subcategoría(s)');
		} else if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Familia = " . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La categoria tiene producto(s)');
		} else {
			$sNombreImagenCategoria = $this->db->query("SELECT No_Imagen_Categoria FROM familia WHERE ID_Familia=" . $ID . " LIMIT 1")->row()->No_Imagen_Categoria;

			$this->db->where('ID_Familia', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
				if ( !empty($sNombreImagenCategoria) ) {
					$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $sNombreImagenCategoria;
					if ( file_exists($path) )
						unlink($path);
				}
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }
}
