<?php
class TipoMedioPagoModel extends CI_Model{
    var $table = 'tipo_medio_pago';
    var $table_medio_pago = 'medio_pago';
	var $table_tabla_dato = 'tabla_dato';
	var $table_empresa = 'empresa';
	var $table_documento_medio_pago = 'documento_medio_pago';
	var $table_caja_pos = 'caja_pos';
	
    var $column_order = array('No_Empresa', 'No_Medio_Pago', 'No_Tipo_Medio_Pago');
    var $column_search = array('No_Empresa', 'No_Medio_Pago', 'No_Tipo_Medio_Pago');
    var $order = array('No_Empresa' => 'asc', 'No_Medio_Pago' => 'asc', 'No_Tipo_Medio_Pago' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
        	$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        
        if( $this->input->post('Filtros_TipoMedioPago') == 'TipoMedioPago' )
            $this->db->like('No_Tipo_Medio_Pago', $this->input->post('Global_Filter'));

        if( $this->input->post('Filtros_TipoMedioPago') == 'MedioPago' )
            $this->db->like('No_Medio_Pago', $this->input->post('Global_Filter'));
    
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, MP.No_Medio_Pago, ID_Tipo_Medio_Pago, No_Tipo_Medio_Pago, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
        ->from($this->table)
        ->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = ' . $this->table . '.ID_Medio_Pago', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = MP.ID_Empresa', 'join')
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
        $this->db->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = ' . $this->table . '.ID_Medio_Pago', 'join');
        $this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = MP.ID_Empresa', 'join');
        $this->db->where('ID_Tipo_Medio_Pago', $ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTipoMedioPago($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Medio_Pago = ".$data['ID_Medio_Pago']." AND No_Tipo_Medio_Pago='" . $data['No_Tipo_Medio_Pago'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarTipoMedioPago($where, $data, $ENo_Tipo_Medio_Pago){
		if( $ENo_Tipo_Medio_Pago != $data['No_Tipo_Medio_Pago'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Medio_Pago = ".$data['ID_Medio_Pago']." AND No_Tipo_Medio_Pago='" . $data['No_Tipo_Medio_Pago'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarTipoMedioPago($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_medio_pago . " WHERE ID_Tipo_Medio_Pago=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El tipo medio de pago tiene movimiento(s)');
		} else {
			$this->db->where('ID_Tipo_Medio_Pago', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
