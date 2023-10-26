<?php
class SeriesxProductoModel extends CI_Model{
	var $table = 'series_x_producto';
	var $table_producto = 'producto';
	
    var $column_order = array('No_Producto','No_Serie_Producto',);
    var $column_search = array('');
    var $order = array('No_Producto' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Marcas') == 'Serie' )
            $this->db->like('No_Serie_Producto', $this->input->post('Global_Filter'));

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Marcas') == 'Producto' )
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));

		$this->db->select('ID_Series_x_Producto, No_Producto, No_Serie_Producto')
		->from($this->table)
    	->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = ' . $this->table . '.ID_Producto', 'left')
		->where($this->table . '.ID_Empresa', $this->empresa->ID_Empresa);
		
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
        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Marcas') == 'Serie' )
            $this->db->like('No_Serie_Producto', $this->input->post('Global_Filter'));

        if( !empty($this->input->post('Global_Filter')) && $this->input->post('Filtros_Marcas') == 'Producto' )
            $this->db->like('No_Producto', $this->input->post('Global_Filter'));

		$this->db->select('ID_Series_x_Producto, No_Producto, No_Serie_Producto')
		->from($this->table)
    	->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = ' . $this->table . '.ID_Producto', 'left')
		->where($this->table . '.ID_Empresa', $this->empresa->ID_Empresa);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
    	$this->db->select('SERIES.*, ITEM.No_Producto, ITEM.Nu_Codigo_Barra');
        $this->db->from($this->table . ' AS SERIES');
        $this->db->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = SERIES.ID_Producto', 'left');
        $this->db->where('SERIES.ID_Series_x_Producto',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarSeriesxProducto($data){
		$series_x_producto=array();
		foreach ($data['arrSeriesxProducto'] as $row) {
			if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['arrHeader']['ID_Empresa'] . " AND ID_Producto=" . $row['ID_Producto'] . " AND No_Serie_Producto='" . $row['No_Serie_Producto'] . "' LIMIT 1")->row()->existe > 0){
				return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie ya existe ' . $row['No_Serie_Producto']);
			} else {
				$series_x_producto[] = array(
					'ID_Empresa' => $this->security->xss_clean($data['arrHeader']['ID_Empresa']),
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
					'No_Serie_Producto' => strtoupper($this->security->xss_clean($row['No_Serie_Producto'])),
					'Nu_Estado' => 1,
				);

				$producto_upd[] = array(
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
					'Nu_Activar_Series_x_Producto' => 1,
				);
			}
		}
		
		if ( $this->db->insert_batch($this->table, $series_x_producto) > 0 ) {
    		$this->db->update_batch('producto', $producto_upd, 'ID_Producto');
			
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarSeriesxProducto($where, $data, $ENo_Serie_Producto){
		if( $ENo_Serie_Producto != $data['arrHeader']['No_Serie_Producto'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa =".$data['arrHeader']['ID_Empresa']." AND ID_Producto='" . $data['arrHeader']['ID_Producto'] . "' AND No_Serie_Producto='" . $data['arrHeader']['No_Serie_Producto'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La serie ya existe ' . $data['arrHeader']['No_Serie_Producto']);
		}else{
			$data = array(
				'ID_Empresa' => $this->security->xss_clean($data['arrHeader']['ID_Empresa']),
				'ID_Producto' => $this->security->xss_clean($data['arrHeader']['ID_Producto']),
				'No_Serie_Producto' => strtoupper($this->security->xss_clean($data['arrHeader']['No_Serie_Producto'])),
				'Nu_Estado' => 1,
			);
			if ( $this->db->update($this->table, $data, $where) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		}
    }
}
