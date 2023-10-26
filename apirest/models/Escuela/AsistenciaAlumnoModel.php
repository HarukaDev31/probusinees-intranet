<?php
class AsistenciaAlumnoModel extends CI_Model{
	var $table = 'control_asistencia_alumno';
	var $table_empresa = 'empresa';
	var $table_sede_musica = 'sede_musica';
	var $table_salon = 'salon';
	var $table_profesor = 'entidad';
	var $table_alumno = 'entidad';
	
    var $column_order = array('EMP.No_Empresa', 'Fe_Asistencia');
    var $column_search = array('');
    var $order = array('Fe_Asistencia' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
    	$this->db->where("Fe_Asistencia BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

        if(!empty($this->input->post('ID_Entidad_Alumno')))
        	$this->db->where($this->table . '.ID_Entidad_Alumno', $this->input->post('ID_Entidad_Alumno'));

		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Control_Asistencia_Alumno, SM.No_Sede_Musica, S.No_Salon, P.No_Entidad AS No_Profesor, Fe_Asistencia, A.No_Contacto AS No_Alumno, ' . $this->table . '.Nu_Asistio')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_sede_musica . ' AS SM', 'SM.ID_Sede_Musica = ' . $this->table . '.ID_Sede_Musica', 'join')
		->join($this->table_salon . ' AS S', 'S.ID_Salon = ' . $this->table . '.ID_Salon', 'join')
		->join($this->table_alumno . ' AS A', 'A.ID_Entidad = ' . $this->table . '.ID_Entidad_Alumno', 'join')
		->join($this->table_profesor . ' AS P', 'P.ID_Entidad = ' . $this->table . '.ID_Entidad_Profesor', 'join')
		->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
		
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
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros') == 'Nombre' )
            $this->db->like('Fe_Asistencia', $this->input->post('Global_Filter'));

		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Control_Asistencia_Alumno, Fe_Asistencia, ' . $this->table . '.Nu_Asistio')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
		$this->db->where('ID_Control_Asistencia_Alumno', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarPos($data){
		$control_asistencia_alumno=array();
		foreach ($data['arrAsistencia'] as $row) {
			if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['arrHeader']['ID_Empresa'] . " AND ID_Sede_Musica=" . $data['arrHeader']['ID_Sede_Musica'] . " AND ID_Salon=" . $data['arrHeader']['ID_Salon'] . " AND ID_Entidad_Profesor=" . $data['arrHeader']['ID_Entidad_Profesor'] . " AND Fe_Asistencia='" . ToDate($data['arrHeader']['Fe_Asistencia']) . "' AND ID_Entidad_Alumno = '" . trim($row['ID_Entidad']) . "' LIMIT 1")->row()->existe > 0){
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
			} else {
				$control_asistencia_alumno[] = array(
					'ID_Empresa' => $this->security->xss_clean($data['arrHeader']['ID_Empresa']),
					'ID_Sede_Musica' => $this->security->xss_clean($data['arrHeader']['ID_Sede_Musica']),
					'ID_Salon' => $this->security->xss_clean($data['arrHeader']['ID_Salon']),
					'ID_Entidad_Profesor' => $this->security->xss_clean($data['arrHeader']['ID_Entidad_Profesor']),
					'Fe_Asistencia' => ToDate($data['arrHeader']['Fe_Asistencia']),
					'ID_Entidad_Alumno' => $this->security->xss_clean($row['ID_Entidad']),
					'Nu_Asistio' => $this->security->xss_clean($row['Nu_Asistio']),
					'Txt_Glosa' => $this->security->xss_clean($row['Txt_Glosa']),
				);
			}
		}
		
		if ( $this->db->insert_batch($this->table, $control_asistencia_alumno) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarPos($where, $data){
		if ( $this->db->update($this->table, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPos($ID){
		$this->db->where('ID_Control_Asistencia_Alumno', $ID);
		$this->db->delete($this->table);
		if ( $this->db->affected_rows() > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

    public function estadoAsistencia($ID, $Nu_Asistencia){
		$Nu_Asistencia = ($Nu_Asistencia == 1 ? 0 : 1);
		$data = array("Nu_Asistio" => $Nu_Asistencia);
		$where = array("ID_Control_Asistencia_Alumno" => $ID);			
		
		if ( $this->db->update($this->table, $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado');
        return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Problemas al cambiar estado');
    }
}
