<?php
class UsuarioModel extends CI_Model{
	var $table = 'usuario';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_grupo = 'grupo';
	var $table_tabla_dato = 'tabla_dato';
	var $table_grupo_usuario = 'grupo_usuario';
	
    var $column_order = array('No_Grupo', 'No_Usuario', 'No_Nombres_Apellidos');
    var $column_search = array('No_Grupo', 'No_Usuario', 'No_Nombres_Apellidos');
    var $order = array('Fe_Creacion' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if ( $this->user->ID_Usuario == 1 ){
            $this->column_order = array('No_Empresa', 'No_Organizacion', 'No_Grupo', 'No_Usuario', 'No_Nombres_Apellidos');
        }

        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('filtro_organizacion') )
			$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
			
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter'))>0 && $this->input->post('Usuario') == 'Usuario' )
            $this->db->like('No_Usuario', $this->input->post('Global_Filter'));
	    
		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, GRPUSR.No_Grupo, ID_Usuario, No_Usuario, No_Nombres_Apellidos, ' . $this->table . '.Nu_Estado')
		->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
        ->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
		->join($this->table_grupo . ' AS GRPUSR', 'GRPUSR.ID_Grupo = ' . $this->table . '.ID_Grupo', 'left');
         
		if ($this->user->ID_Usuario != 1){
        	$this->db->where('No_Usuario != ', '"root"');
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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID_Usuario){
        $this->db->from($this->table);
        $this->db->where('ID_Usuario', $ID_Usuario);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarUsuario($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Usuario='" . $data['No_Usuario'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El usuario ya existe');
		} else if( isset($data['Nu_Celular']) && strlen($data['Nu_Celular']) == 11 && $this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Celular='" . $data['Nu_Celular'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El número ya existe');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Txt_Email='" . $data['Txt_Email'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El correo ya existe');
		} else {
			if($data['No_Usuario']=='root'){//1=root
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'No puedes crear un usuario con nombre > "root"');
			}

			if ( ($this->db->insert($this->table, $data) > 0) ){
			    unset($data['No_Usuario']);
			    unset($data['No_Nombres_Apellidos']);
				unset($data['No_Password']);
				if( isset($data['Nu_Celular']) )
			    	unset($data['Nu_Celular']);
			    unset($data['Txt_Email']);
			    unset($data['Txt_Token_Activacion']);
			    unset($data['No_IP']);
			    unset($data['Nu_Estado']);
			    $data['ID_Usuario'] = $this->db->insert_id();
			    if ($this->db->insert($this->table_grupo_usuario, $data) > 0){
				    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			    }
			}
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarUsuario($where, $data, $EID_Grupo, $ENo_Usuario, $ENu_Celular, $ETxt_Email, $ENu_Estado){
		if( $ENu_Estado == $data['Nu_Estado'] && ($where['ID_Organizacion'] != $data['ID_Organizacion']) && $EID_Grupo == $data['ID_Grupo'] && $this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Usuario='" . $data['No_Usuario'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El usuario ya existe');
		} else if( $ENu_Estado == $data['Nu_Estado'] && isset($data['Nu_Celular']) && strlen($data['Nu_Celular']) == 11 && ($where['ID_Organizacion'] != $data['ID_Organizacion'] || $ENu_Celular != $data['Nu_Celular']) && $EID_Grupo == $data['ID_Grupo'] && $this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Nu_Celular='" . $data['Nu_Celular'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El número ya existe');
		} else if( $ENu_Estado == $data['Nu_Estado'] && ($where['ID_Organizacion'] != $data['ID_Organizacion'] || $ETxt_Email != $data['Txt_Email']) && $EID_Grupo == $data['ID_Grupo'] && $this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND Txt_Email='" . $data['Txt_Email'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El correo ya existe');
		} else if( $ENu_Estado == $data['Nu_Estado'] && ($where['ID_Organizacion'] != $data['ID_Organizacion'] || $EID_Grupo != $data['ID_Grupo'] || $ENo_Usuario != $data['No_Usuario']) && $this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Organizacion=" . $data['ID_Organizacion'] . " AND ID_Grupo=" . $data['ID_Grupo'] . " AND No_Usuario='" . $data['No_Usuario'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El usuario ya existe.');
		} else {
			if($where['ID_Usuario']==1 && $data['No_Usuario']!='root'){//1=root
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'No se puede cambiar el nombre "root"');
			}

			$where = array('ID_Usuario' => $where['ID_Usuario']);
		    if ( $this->db->update($this->table, $data, $where) > 0 ){
			    unset($data['No_Usuario']);
			    unset($data['No_Nombres_Apellidos']);
			    unset($data['No_Password']);
				if( isset($data['Nu_Celular']) )
			    	unset($data['Nu_Celular']);
			    unset($data['Txt_Email']);
			    unset($data['Txt_Token_Activacion']);
			    unset($data['No_IP']);
			    unset($data['Nu_Estado']);
                $this->db->update($this->table_grupo_usuario, $data, $where);
		        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
			}
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarUsuario($ID_Usuario){
		//VALIDAR QUE NO PUEDAN CAMBIAR NI ELIMINAR ROOT
		if($ID_Usuario==1){
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No se puede eliminar usuario ROOT porque es el principal');
		}

        $this->db->trans_begin();

		$objGrupoUsuario = $this->db->query("SELECT ID_Grupo_Usuario FROM grupo_usuario WHERE ID_Usuario=" . $ID_Usuario . " LIMIT 1")->row();

		if(is_object($objGrupoUsuario)){
        	$ID_Grupo_Usuario = $objGrupoUsuario->ID_Grupo_Usuario;
		}

        $this->db->where('ID_Grupo_Usuario', $ID_Grupo_Usuario);
        $this->db->delete('menu_acceso');

        $this->db->where('ID_Usuario', $ID_Usuario);
        $this->db->delete($this->table_grupo_usuario);
        
		$this->db->where('ID_Usuario', $ID_Usuario);
        $this->db->delete($this->table);
	
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
            $this->db->trans_commit();
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
	}
}
