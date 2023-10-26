<?php
class Transporte_delivery_model extends CI_Model{
	var $table                          = 'transporte_delivery';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_distrito                 = 'distrito';
	var $table_documento_cabecera       = 'documento_cabecera';
	
    var $column_order = array(null, 'Nu_Documento_Identidad', 'Nu_Documento_Identidad', 'No_Transportista');
    var $column_search = array('Nu_Documento_Identidad', 'No_Transportista');
    var $order = array('No_Transportista' => 'asc',);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_Transporte_Deliverys') == 'Transporte_Delivery' ){
            $this->db->like('No_Transportista', $this->input->post('Global_Filter'));
        } else if ( $this->input->post('Filtros_Transporte_Deliverys') == 'DNI' ){
        	$this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('ID_Transporte_Delivery, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Transportista, Txt_Direccion, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = ' . $this->table . '.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_Estados"', 'join')
    	->where('ID_Empresa', $this->user->ID_Empresa);
		
        $i = 0;
        foreach ($this->column_search as $item){
            if($_POST['search']['value']){
                if($i===0){
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                }else{
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
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
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Transporte_Delivery',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarTransporte_Delivery($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE Nu_Documento_Identidad = '" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarTransporte_Delivery($where, $data, $ENu_Documento_Identidad){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE Nu_Documento_Identidad = '" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarTransporte_Delivery($ID){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if ($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Transporte_Delivery = " . $ID . " LIMIT 1")->row()->existe > 0) {
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El empleado tiene movimiento(s)');
		} else {
			$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
			$this->db->where('ID_Transporte_Delivery', $ID);
            $this->db->delete($this->table);
		}
        return $response;
	}
}
