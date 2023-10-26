<?php
class VarianteModel extends CI_Model{
	var $table              = 'variante_item';
	var $table_producto     = 'producto';

    var $column_order = array('ID_Tabla_Dato', 'No_Variante', 'Nu_Estado');
    var $order = array('ID_Tabla_Dato' => 'asc','No_Variante' => 'asc',);
    

	var $table_detalle = 'variante_item_detalle';
    var $column_order_detalle = array('ID_Tabla_Dato', 'No_Variante', 'No_Valor', 'Nu_Estado');
    var $order_detalle = array('ID_Tabla_Dato' => 'asc','No_Variante' => 'asc','No_Valor' => 'asc',);

	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Marcas') == 'Marca' )
            $this->db->like('No_Variante', $this->input->post('Global_Filter'));
        
        $this->db->select('ID_Variante_Item, ID_Tabla_Dato, No_Variante, Nu_Estado')
		->from($this->table)
        ->where('ID_Empresa', $this->user->ID_Empresa);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
        $this->db->where('ID_Variante_Item',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMarca($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Tabla_Dato=" . $data['ID_Tabla_Dato'] . " AND No_Variante='" . $data['No_Variante'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Tabla_Dato=" . $data['ID_Tabla_Dato'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya tiene variante, elegir la siguiente');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarMarca($where, $data, $ENo_Variante, $EID_Tabla_Dato){
		if( ($ENo_Variante != $data['No_Variante'] || $EID_Tabla_Dato != $data['ID_Tabla_Dato']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Tabla_Dato=" . $data['ID_Tabla_Dato'] . " AND No_Variante='" . $data['No_Variante'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else if( ($EID_Tabla_Dato != $data['ID_Tabla_Dato']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Tabla_Dato=" . $data['ID_Tabla_Dato'] . " LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya tiene variante, elegir la siguiente');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarMarca($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_1=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 1 tiene producto(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_2=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 2 tiene producto(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_3=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 3 tiene producto(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_detalle . " WHERE ID_Variante_Item=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante tiene detalle');
		} else {
			$this->db->where('ID_Variante_Item', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}

    //DETALLE
	public function _get_datatables_query_detalle(){
        if( !empty($this->input->post('Global_Filter_Detalle')) && $this->input->post('Filtros_Marcas_Detalle') == 'Marca' )
            $this->db->like('No_Valor', $this->input->post('Global_Filter_Detalle'));
        
        $this->db->select('VI.ID_Variante_Item, VI.ID_Tabla_Dato, ID_Variante_Item_Detalle, No_Variante, No_Valor, ' . $this->table_detalle . '.Nu_Estado')
		->from($this->table_detalle)
    	->join($this->table . ' AS VI', 'VI.ID_Variante_Item = ' . $this->table_detalle . '.ID_Variante_Item', 'join')
        ->where('VI.ID_Empresa', $this->user->ID_Empresa);

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order_detalle[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_detalle)) {
            $order = $this->order_detalle;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	function get_datatables_detalle(){
        $this->_get_datatables_query_detalle();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered_detalle(){
        $this->_get_datatables_query_detalle();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_detalle(){
        $this->db->from($this->table_detalle);
        return $this->db->count_all_results();
    }
    
    public function get_by_id_detalle($ID){
        $this->db->from($this->table_detalle);
        $this->db->where('ID_Variante_Item_Detalle',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarMarcaDetalle($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_detalle . " WHERE ID_Variante_Item=" . $data['ID_Variante_Item'] . " AND No_Valor='" . $data['No_Valor'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table_detalle, $data) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarMarcaDetalle($where, $data, $ENo_Valor, $EID_Variante_Item){
		if( ($ENo_Valor != $data['No_Valor'] || $EID_Variante_Item != $data['ID_Variante_Item']) && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_detalle . " WHERE ID_Variante_Item=" . $data['ID_Variante_Item'] . " AND No_Valor='" . $data['No_Valor'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table_detalle, $data, $where) > 0 )
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarMarcaDetalle($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_Detalle_1=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 1 tiene producto(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_Detalle_2=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 2 tiene producto(s)');
		} else if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_Variante_Item_Detalle_3=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La variante 3 tiene producto(s)');
		} else {
			$this->db->where('ID_Variante_Item_Detalle', $ID);
            $this->db->delete($this->table_detalle);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
