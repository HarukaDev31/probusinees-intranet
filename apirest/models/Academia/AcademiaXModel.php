<?php
class AcademiaXModel extends CI_Model{
	var $table              = 'marca';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_producto     = 'producto';
	
    var $column_order = array('Nu_Orden','No_Marca', null);
    var $column_search = array('');
    var $order = array('Nu_Activar_Marca_Lae_Shop' => 'desc','No_Marca' => 'asc',);
    
	private $upload_path = '../assets/images/marcas/';

	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Marcas') == 'Marca' ){
            $this->db->like('No_Marca', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Marca, Nu_Orden, No_Marca, Txt_Url_Logo_Lae_Shop, Nu_Activar_Marca_Lae_Shop AS Nu_Estado, Nu_Version_Imagen')
		->from($this->table)
        ->where('ID_Empresa', $this->empresa->ID_Empresa);

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
			$objImage = $this->db->query("SELECT Txt_Url_Logo_Lae_Shop FROM marca WHERE ID_Marca=" . $ID . " LIMIT 1")->row();
			$sUrlImage = (is_object($objImage) ? $objImage->Txt_Url_Logo_Lae_Shop : '');

			$this->db->where('ID_Marca', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
                if ( !empty($sUrlImage) ) {
                    $arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImage);
                    $path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
                    if ( file_exists($path) )
                        unlink($path);
                }
                
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Marca imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error de Marca imagen modificada');
    }
}
