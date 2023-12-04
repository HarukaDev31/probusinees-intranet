<?php
class SliderModel extends CI_Model{
	var $table = 'ecommerce_inicio';
	var $table_tabla_dato = 'tabla_dato';
	
    var $column_order = array('', 'Nu_Orden_Slider', '','');
    var $column_search = array('');
    var $order = array('Nu_Orden_Slider' => 'asc');
	
    //private $upload_path = '../assets/images/sliders/';
	private $upload_path = 'assets/images/sliders/';
    
	public function __construct(){
		parent::__construct();
	}
    
    /* slide */
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Inicios')=='Inicio' ){
            $this->db->like('No_Slider', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Ecommerce_Inicio, No_Slider, No_Imagen_Inicio_Slider, No_Imagen_Url_Inicio_Slider, Nu_Orden_Slider, Nu_Version_Imagen, ' . $this->table . '.Nu_Estado_Slider')
        ->from($this->table)
        ->where('ID_Empresa', $this->user->ID_Empresa)
        ->where('Nu_Tipo_Inicio', 1);

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
    
    /* slider mobile */
	public function _get_datatables_query_slider_mobile(){
        if( $this->input->post('Filtros_Inicios')=='Inicio' ){
            $this->db->like('No_Slider', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Ecommerce_Inicio, No_Slider, No_Imagen_Inicio_Slider, No_Imagen_Url_Inicio_Slider, Nu_Orden_Slider, Nu_Version_Imagen, ' . $this->table . '.Nu_Estado_Slider')
        ->from($this->table)
        ->where('ID_Empresa', $this->user->ID_Empresa)
        ->where('Nu_Tipo_Inicio', 3);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables_slider_mobile(){
        $this->_get_datatables_query_slider_mobile();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_slider_mobile(){
        $this->_get_datatables_query_slider_mobile();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_slider_mobile(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /* ofertas / promociones */
	public function _get_datatables_query_ofertas(){
        if( $this->input->post('Filtros_Inicios')=='Inicio' ){
            $this->db->like('No_Slider', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Ecommerce_Inicio, No_Slider, No_Imagen_Inicio_Slider, No_Imagen_Url_Inicio_Slider, Nu_Orden_Slider, Nu_Version_Imagen, ' . $this->table . '.Nu_Estado_Slider')
        ->from($this->table)
        ->where('ID_Empresa', $this->user->ID_Empresa)
        ->where('Nu_Tipo_Inicio', 2);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables_ofertas(){
        $this->_get_datatables_query_ofertas();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_ofertas(){
        $this->_get_datatables_query_ofertas();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_ofertas(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Ecommerce_Inicio',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarInicio($data){
        if ( $this->db->insert($this->table, $data) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarInicio($where, $data, $ENo_Slider){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada / eliminada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }
    
	public function eliminarInicio($ID){
        $sNombreImagenInicio = $this->db->query("SELECT No_Imagen_Inicio_Slider FROM ecommerce_inicio WHERE ID_Ecommerce_Inicio=" . $ID . " LIMIT 1")->row()->No_Imagen_Inicio_Slider;
        $this->db->where('ID_Ecommerce_Inicio', $ID);
        $this->db->delete($this->table);
        if ( $this->db->affected_rows() > 0 ) {
            if ( !empty($sNombreImagenInicio) ) {
                $path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $sNombreImagenInicio;
                if ( file_exists($path) )
                    unlink($path);
            }
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Problemas al eliminar');
	}
}
