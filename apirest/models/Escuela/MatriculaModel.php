<?php
class MatriculaModel extends CI_Model{
	var $table = 'matricula_alumno';
	var $table_empresa = 'empresa';
	var $table_sede_musica = 'sede_musica';
	var $table_salon = 'salon';
	var $table_horario_clase = 'horario_clase';
	var $table_dia_semana = 'dia_semana';
	var $table_profesor = 'entidad';
	var $table_alumno = 'entidad';
	var $table_famiia = 'familia';
	var $table_grupo_clase = 'tabla_dato';
	var $table_tipo_clase = 'tabla_dato';
	
    var $column_order = array('EMP.No_Empresa', 'Fe_Matricula', 'No_Sede_Musica', 'No_Salon', 'No_Dia', 'Nu_Hora_Desde', 'P.No_Entidad', 'A.No_Contacto', 'F.No_Familia', ' GC.No_Descripcion', 'TC.No_Descripcion');
    var $column_search = array('');
    var $order = array('EMP.No_Empresa' => 'asc', 'Fe_Matricula' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Matricula_Alumno, Fe_Matricula, SM.No_Sede_Musica, S.No_Salon, DS.No_Dia, HC.Nu_Hora_Desde, HC.Nu_Minuto_Desde, HC.Nu_Hora_Hasta, HC.Nu_Minuto_Hasta, P.No_Entidad AS No_Profesor, A.No_Contacto AS No_Alumno, F.No_Familia, GC.No_Descripcion AS No_Grupo_Clase, TC.No_Descripcion AS No_Tipo_Clase, ' . $this->table . '.Nu_Estado')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->join($this->table_sede_musica . ' AS SM', 'SM.ID_Sede_Musica = ' . $this->table . '.ID_Sede_Musica', 'join')
		->join($this->table_salon . ' AS S', 'S.ID_Salon = ' . $this->table . '.ID_Salon', 'join')
		->join($this->table_horario_clase . ' AS HC', 'HC.ID_Horario_Clase = ' . $this->table . '.ID_Horario_Clase', 'join')
		->join($this->table_dia_semana . ' AS DS', 'DS.ID_Dia_Semana = HC.ID_Dia_Semana', 'join')
		->join($this->table_profesor . ' AS P', 'P.ID_Entidad = ' . $this->table . '.ID_Entidad_Profesor', 'join')
		->join($this->table_alumno . ' AS A', 'A.ID_Entidad = ' . $this->table . '.ID_Entidad_Alumno', 'join')
		->join($this->table_famiia . ' AS F', 'F.ID_Familia = ' . $this->table . '.ID_Familia', 'join')
		->join($this->table_grupo_clase . ' AS GC', 'GC.ID_Tabla_Dato = ' . $this->table . '.ID_Grupo_Clase', 'join')
		->join($this->table_tipo_clase . ' AS TC', 'TC.ID_Tabla_Dato = ' . $this->table . '.ID_Tipo_Clase', 'join')
		->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
		
    	$this->db->where("Fe_Matricula BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

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
    	$this->db->where("Fe_Matricula BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

		$this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Matricula_Alumno, Fe_Matricula, ' . $this->table . '.Nu_Estado')
		->from($this->table)
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
		->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
		$this->db->where('ID_Matricula_Alumno', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarPos($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Horario_Clase='" . $data['ID_Horario_Clase'] . "' AND ID_Entidad_Alumno='" . $data['ID_Entidad_Alumno'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }

    public function actualizarPos($where, $data, $arrDataActual){
		if(($arrDataActual['ID_Horario_Clase'] != $data['ID_Horario_Clase'] || $arrDataActual['ID_Entidad_Alumno'] != $data['ID_Entidad_Alumno']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND ID_Horario_Clase='" . $data['ID_Horario_Clase'] . "' AND ID_Entidad_Alumno='" . $data['ID_Entidad_Alumno'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPos($ID){
		$this->db->where('ID_Matricula_Alumno', $ID);
		$this->db->delete($this->table);
		if ( $this->db->affected_rows() > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
    
    public function agregarPosMultiple($arrHeader, $arrAlumnoHorario){
		$this->db->trans_begin();
			
		$matricula_alumno=array();
		foreach ($arrAlumnoHorario as $row) {
			$matricula_alumno[] = array(
				'ID_Empresa' => $this->security->xss_clean($arrHeader['ID_Empresa']),
				'ID_Sede_Musica' => $this->security->xss_clean($arrHeader['ID_Sede_Musica']),
				'ID_Salon' => $this->security->xss_clean($arrHeader['ID_Salon']),
				'ID_Entidad_Profesor' => $this->security->xss_clean($arrHeader['ID_Entidad_Profesor']),
				'ID_Familia' => $this->security->xss_clean($arrHeader['ID_Familia']),
				'ID_Grupo_Clase' => $this->security->xss_clean($arrHeader['ID_Grupo_Clase']),
				'ID_Tipo_Clase' => $this->security->xss_clean($arrHeader['ID_Tipo_Clase']),
				'Nu_Estado' => $this->security->xss_clean($arrHeader['Nu_Estado']),
				'Fe_Matricula' => $arrHeader['Fe_Matricula'],
				'Txt_Glosa' => $this->security->xss_clean($arrHeader['Txt_Glosa']),				
				'ID_Horario_Clase' => $this->security->xss_clean($row['ID_Horario_Clase2']),
				'ID_Entidad_Alumno' => $this->security->xss_clean($row['ID_Entidad_Alumno2']),
			);
		}

		//INSERT MASIVO
		$this->db->insert_batch($this->table, $matricula_alumno);
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
	}
}
