<?php
class UbicacionInventarioModel extends CI_Model{
	var $table              = 'ubicacion_inventario';
	var $table_almacen      = 'almacen';
	var $table_organizacion = 'organizacion';
	var $table_tabla_dato   = 'tabla_dato';
	var $table_producto     = 'producto';
	
    var $column_order = array('No_Almacen', 'No_Ubicacion_Inventario');
    var $column_search = array('No_Almacen', 'No_Ubicacion_Inventario');
    var $order = array('No_Almacen' => 'asc', 'No_Ubicacion_Inventario' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( $this->input->post('Filtros_UnidadesMedida') == 'UbicacionInventario' ){
            $this->db->like('No_Ubicacion_Inventario', $this->input->post('Global_Filter'));
        }
        
        if( $this->input->post('Filtros_UnidadesMedida') == 'Almacen' ){
            $this->db->like('No_Almacen', $this->input->post('Global_Filter'));
        }
        
        $this->db->select('No_Almacen, ALMA.ID_Almacen, ID_Ubicacion_Inventario, No_Ubicacion_Inventario, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table)
    	->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = ' . $this->table . '.ID_Almacen', 'join')
    	->join($this->table_organizacion, $this->table_organizacion . '.ID_Organizacion = ALMA.ID_Organizacion', 'join')
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
        $this->db->where('ID_Ubicacion_Inventario',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarUbicacionInventario($data){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if($this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Almacen = " . $data['ID_Almacen'] . " AND No_Ubicacion_Inventario = '" . $data['No_Ubicacion_Inventario'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return $response;
    }
    
    public function actualizarUbicacionInventario($where, $data, $ENo_Ubicacion_Inventario){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if( $ENo_Ubicacion_Inventario != $data['No_Ubicacion_Inventario'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Almacen = " . $data['ID_Almacen'] . " AND No_Ubicacion_Inventario = '" . $data['No_Ubicacion_Inventario'] . "' LIMIT 1")->row()->existe > 0 ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return $response;
    }
    
	public function eliminarUbicacionInventario($ID, $ID_Almacen){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_producto . " WHERE ID_empresa IN (SELECT ID_Empresa FROM organizacion WHERE ID_Organizacion = (SELECT ID_Organizacion FROM almacen WHERE ID_Almacen = " . $ID_Almacen . ")) AND ID_Ubicacion_Inventario = " . $ID . " LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La unidad medida tiene producto(s)');
		}else{
			$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
			$this->db->where('ID_Ubicacion_Inventario', $ID);
            $this->db->delete($this->table);
		}
        return $response;
	}
}
