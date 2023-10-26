<?php
class BlogHistorialUsuarioModel extends CI_Model{
	var $table = 'blog_historial';
	var $table_blog_post = 'blog_post';
	var $table_usuario = 'usuario';
	var $table_grupo = 'grupo';
	
    var $column_order = array('No_Grupo', 'No_USuario','Fe_Emision', 'No_Titulo_Blog');
    var $column_search = array();
    var $order = array('HB.Fe_Emision' => 'desc');
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
		$this->db->where("HB.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
		
        if( !empty($this->input->post('Global_Filter')) )
            $this->db->like('USR.No_USuario', $this->input->post('Global_Filter'));
        
		$this->db->select('GRPUSR.No_Grupo, USR.No_USuario, HB.Fe_Emision, BP.No_Titulo_Blog')
		->from($this->table . ' AS HB')
    	->join($this->table_blog_post . ' AS BP', 'BP.ID_Post_Blog = HB.ID_Post_Blog', 'join')
    	->join($this->table_usuario . ' AS USR', 'USR.ID_Usuario = HB.ID_Usuario', 'join')
		->join($this->table_grupo . ' AS GRPUSR', 'GRPUSR.ID_Grupo = USR.ID_Grupo', 'left')
		->where('HB.ID_Empresa', $this->empresa->ID_Empresa);
         
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
		$this->db->select('GRPUSR.No_Grupo, USR.No_USuario, HB.Fe_Emision, BP.No_Titulo_Blog')
		->from($this->table . ' AS HB')
    	->join($this->table_blog_post . ' AS BP', 'BP.ID_Post_Blog = HB.ID_Post_Blog', 'join')
    	->join($this->table_usuario . ' AS USR', 'USR.ID_Usuario = HB.ID_Usuario', 'join')
		->join($this->table_grupo . ' AS GRPUSR', 'GRPUSR.ID_Grupo = USR.ID_Grupo', 'left')
		->where('HB.ID_Empresa', $this->empresa->ID_Empresa);
		
		$this->db->where("HB.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
		
        if( !empty($this->input->post('Global_Filter')) )
            $this->db->like('USR.No_USuario', $this->input->post('Global_Filter'));
        return $this->db->count_all_results();
    }
}