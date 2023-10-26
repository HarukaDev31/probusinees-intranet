<?php
class ValorImpuestoModel extends CI_Model{
	var $table                      = 'impuesto_cruce_documento';
	var $table_impuesto             = 'impuesto';
	var $table_tabla_dato           = 'tabla_dato';
	var $table_documento_detalle    = 'documento_detalle';
	var $table_empresa              = 'empresa';
	
    var $column_order = array('No_Empresa', 'No_Impuesto', null);
    var $column_search = array('No_Empresa', 'No_Impuesto');
    var $order = array('No_Empresa' => 'asc', 'No_Impuesto' => 'asc',);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('filtro_empresa') )
            $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
            
        if( $this->input->post('Filtros_Impuestos') == 'Impuesto' )
            $this->db->like('No_Impuesto', $this->input->post('Global_Filter'));
        
        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Impuesto_Cruce_Documento, No_Impuesto, Ss_Impuesto, Po_Impuesto, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
        ->from($this->table)
        ->join($this->table_impuesto, $this->table_impuesto . '.ID_Impuesto=' . $this->table . '.ID_Impuesto', 'join')
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table_impuesto . '.ID_Empresa', 'join')
        ->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor=' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join');

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
    	$this->db->select('VIMP.*, IMP.ID_Empresa');
        $this->db->from($this->table . ' AS VIMP');
        $this->db->join($this->table_impuesto . ' AS IMP', 'IMP.ID_Impuesto=VIMP.ID_Impuesto', 'join');
        $this->db->where('VIMP.ID_Impuesto_Cruce_Documento',$ID);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarValorImpuesto($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Impuesto = " . $data['ID_Impuesto'] . " AND Ss_Impuesto=" . $data['Ss_Impuesto'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Impuesto = " . $data['ID_Impuesto'] . " AND Nu_Estado=1")->row()->existe == 1 ){
		    return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No puede tener mas de un impuesto activo');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarValorImpuesto($where, $data, $EID_Impuesto, $ESs_Impuesto){
		if( $ESs_Impuesto != $data['Ss_Impuesto'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Impuesto=" . $data['ID_Impuesto'] . " AND Ss_Impuesto=" . $data['Ss_Impuesto'] . " LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarValorImpuesto($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_detalle . " WHERE ID_Impuesto_Cruce_Documento=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El monto del impuesto tiene movimiento(s)');
		} else {
            $this->db->where('ID_Impuesto_Cruce_Documento', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
