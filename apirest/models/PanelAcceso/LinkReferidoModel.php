<?php
class LinkReferidoModel extends CI_Model{
	var $table = 'usuario_referido';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_grupo = 'grupo';
	var $table_tabla_dato = 'tabla_dato';
	var $table_grupo_usuario = 'grupo_usuario';
	
    var $column_order = array('EMPLINKORIGEN.No_Empresa', 'No_Empresa_Referida', 'No_Usuario', 'No_Nombres_Apellidos');
    var $column_search = array('');
    var $order = array('Fe_Registro' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Usuario') == 'Usuario' )
            $this->db->like('EMPLINKDESTINO.No_Empresa', $this->input->post('Global_Filter'));

		$this->db->select('EMPLINKORIGEN.No_Empresa, EMPLINKDESTINO.No_Empresa AS No_Empresa_Referida, ' . $this->table . '.Fe_Registro')
		->from($this->table)
        ->join($this->table_empresa . ' AS EMPLINKORIGEN', 'EMPLINKORIGEN.ID_Empresa = ' . $this->table . '.ID_Usuario_Empresa', 'join')
        ->join($this->table_empresa . ' AS EMPLINKDESTINO', 'EMPLINKDESTINO.ID_Empresa = ' . $this->table . '.ID_Usuario_Empresa_Referido', 'join');
         
		if ($this->user->ID_Usuario != 1){
			$this->db->where('EMPLINKORIGEN.ID_Empresa', $this->empresa->ID_Empresa);
		}

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
        return 0;
        //$this->db->from($this->table);
        //return $this->db->count_all_results();
    }
}
