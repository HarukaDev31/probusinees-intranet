<?php
class PerfilUsuarioModel extends CI_Model{
	var $table = 'grupo';
	var $table_empresa  = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_tabla_dato = 'tabla_dato';
	
    var $column_order = array('No_Empresa', 'No_Organizacion', 'No_Grupo', 'No_Grupo_Descripcion',null);
    var $column_search = array('No_Empresa', 'No_Organizacion', 'No_Grupo', 'No_Grupo_Descripcion');
    var $order = array('No_Empresa' => 'desc', 'No_Organizacion' => 'desc', 'No_Grupo' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') )
            $this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));

        if( $this->input->post('Perfil_Usuario') == 'Perfil_Usuario' )
            $this->db->like('No_Grupo', $this->input->post('Global_Filter'));
                
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ID_Grupo, No_Grupo, No_Grupo_Descripcion, ' . $this->table . '.Nu_Estado')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
        ->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join');
         
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
    
    public function get_by_id($ID_Grupo){
        $this->db->from($this->table);
        $this->db->where('ID_Grupo',$ID_Grupo);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarPerfilUsuario($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM grupo WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Grupo='" . $data['No_Grupo'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarPerfilUsuario($where, $data, $ENo_Grupo){
		if( ($where['ID_Organizacion'] != $data['ID_Organizacion'] || $ENo_Grupo != $data['No_Grupo']) &&  $this->db->query("SELECT COUNT(*) AS existe FROM grupo WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Grupo='" . $data['No_Grupo'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPerfilUsuario($ID_Grupo){
		if($this->db->query("SELECT COUNT(*) existe FROM grupo_usuario WHERE ID_Grupo=" . $ID_Grupo . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El grupo tiene asignado usuario(s)');
		}else{
            if($ID_Grupo==1){
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No se puede eliminar grupo ROOT porque es el principal');
            }

			$this->db->where('ID_Grupo', $ID_Grupo);
            $this->db->delete($this->table);
            
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
