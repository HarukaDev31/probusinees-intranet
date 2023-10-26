<?php
class RedesSocialesModel extends CI_Model{
	var $table = 'configuracion';
	var $table_empresa = 'empresa';
	
    var $column_order = array('No_Empresa', 'No_Red_Social_Facebook', 'No_Red_Social_Instagram', 'No_Red_Social_Tiktok', 'No_Red_Social_Youtube', 'No_Red_Social_Linkedin', 'No_Red_Social_Twitter', 'No_Red_Social_Pinterest');
    var $column_search = array('');
    var $order = array('No_Empresa' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
    
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Configuracion, No_Red_Social_Facebook, No_Red_Social_Instagram, No_Red_Social_Tiktok, No_Red_Social_Youtube, No_Red_Social_Linkedin, No_Red_Social_Twitter, No_Red_Social_Pinterest')
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
        $this->db->where('ID_Configuracion', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function actualizarMedioPago($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
}
