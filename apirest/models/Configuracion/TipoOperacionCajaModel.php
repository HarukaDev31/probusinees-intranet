<?php
class TipoOperacionCajaModel extends CI_Model{
	var $table = 'tipo_operacion_caja';
	var $table_tabla_dato = 'tabla_dato';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_almacen = 'almacen';
	var $table_caja_pos = 'caja_pos';
	
    var $column_order = array('No_Empresa', 'No_Organizacion', 'No_Almacen', 'No_Tipo_Operacion_Caja');
    var $column_search = array('No_Empresa', 'No_Organizacion', 'No_Almacen', 'No_Tipo_Operacion_Caja');
    var $order = array('No_Empresa' => 'asc', 'No_Organizacion' => 'asc', 'No_Almacen' => 'asc', 'No_Tipo_Operacion_Caja' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
            $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
            
        if( $this->input->post('filtro_organizacion') )
            $this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
            
        if( $this->input->post('filtro_almacen') && $this->input->post('filtro_almacen') > 0 )
            $this->db->where('ALMA.ID_Almacen', $this->input->post('filtro_almacen'));
        
        if( $this->input->post('Filtros_TipoOperacionCaja') == 'TipoOperacionCaja' )
            $this->db->like('No_Tipo_Operacion_Caja', $this->input->post('Global_Filter'));
    
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ORG.ID_Organizacion, ORG.No_Organizacion, ALMA.ID_Almacen, ALMA.No_Almacen, ID_Tipo_Operacion_Caja, No_Tipo_Operacion_Caja, TOCAJAPV.No_Class AS No_Class_Grupo_Operacion_Caja, TOCAJAPV.No_Descripcion AS No_Descripcion_Grupo_Operacion_Caja, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join')
        ->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Empresa = ' . $this->table . '.ID_Empresa AND ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join')
        ->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Organizacion = ' . $this->table . '.ID_Organizacion AND ALMA.ID_Almacen = ' . $this->table . '.ID_Almacen', 'join')
        ->join($this->table_tabla_dato . ' AS TOCAJAPV', 'TOCAJAPV.Nu_Valor = ' . $this->table . '.Nu_Tipo AND TOCAJAPV.No_Relacion = "Tipos_Operaciones_Caja_PV"', 'join')
        ->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');
         
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
        $this->db->where('ID_Tipo_Operacion_Caja', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTipoOperacionCaja($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND ID_Organizacion = ".$data['ID_Organizacion']." AND ID_Almacen = ".$data['ID_Almacen']." AND No_Tipo_Operacion_Caja='" . $data['No_Tipo_Operacion_Caja'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarTipoOperacionCaja($where, $data, $ENo_Tipo_Operacion_Caja){
		if( $ENo_Tipo_Operacion_Caja != $data['No_Tipo_Operacion_Caja'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = ".$data['ID_Empresa']." AND ID_Organizacion = ".$data['ID_Organizacion']." AND ID_Almacen = ".$data['ID_Almacen']." AND No_Tipo_Operacion_Caja='" . $data['No_Tipo_Operacion_Caja'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_caja_pos . " WHERE ID_Tipo_Operacion_Caja=" . $where['ID_Tipo_Operacion_Caja'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El tipo de operación de caja tiene movimiento(s)');
        } else {
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarTipoOperacionCaja($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_caja_pos . " WHERE ID_Tipo_Operacion_Caja=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El tipo de operación de caja tiene movimiento(s)');
		} else {
			$this->db->where('ID_Tipo_Operacion_Caja', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
