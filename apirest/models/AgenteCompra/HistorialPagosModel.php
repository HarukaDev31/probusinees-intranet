<?php
class HistorialPagosModel extends CI_Model{
	var $table = 'agente_compra_pedido_cabecera';
	var $table_agente_compra_pedido_detalle = 'agente_compra_pedido_detalle';
	var $table_pais = 'pais';
	var $table_agente_compra_correlativo = 'agente_compra_correlativo';
	var $table_producto = 'producto';
	var $table_cliente = 'entidad';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select('CORRE.Fe_Month, Nu_Estado_China,' . $this->table . '.*, P.No_Pais,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto, P2.No_Pais AS No_Pais_2, P3.No_Pais AS No_Pais_3, P4.No_Pais AS No_Pais_4')
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_pais . ' AS P2', 'P2.ID_Pais = ' . $this->table . '.ID_Pais_30_Cliente', 'left')
    	->join($this->table_pais . ' AS P3', 'P3.ID_Pais = ' . $this->table . '.ID_Pais_100_Cliente', 'left')
    	->join($this->table_pais . ' AS P4', 'P4.ID_Pais = ' . $this->table . '.ID_Pais_Servicio_Cliente', 'left')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where_in($this->table . '.Nu_Estado', array(5,6,7,9));

		$this->db->where("Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

		if(isset($this->order)) {
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
        $this->db->select($this->table . '.*, IGD.ID_Producto, ITEM.No_Producto');
        $this->db->from($this->table);
    	$this->db->join($this->table_importacion_grupal_detalle . ' AS IGD', 'IGD.ID_Importacion_Grupal = ' . $this->table . '.ID_Importacion_Grupal', 'join');
    	$this->db->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = IGD.ID_Producto', 'join');
        $this->db->where($this->table . '.ID_Importacion_Grupal',$ID);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function agregarCliente($data, $addProducto){
		$this->db->trans_begin();

		if ( $this->db->insert($this->table, $data) > 0 ) {
			$ID_Cabecera = $this->db->insert_id();
			foreach($addProducto as $row){
				$detalle[] = array(
					'ID_Empresa'				=> $this->user->ID_Empresa,
					'ID_Organizacion'			=> $this->user->ID_Organizacion,
					'ID_Importacion_Grupal' 	=> $ID_Cabecera,
					'ID_Producto'				=> $this->security->xss_clean($row['id_item']),
				);
			}
			$this->db->insert_batch($this->table_importacion_grupal_detalle, $detalle);
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al guradar');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al guradar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
    }
    
    public function actualizarCliente($where, $data, $arrProducto){
		$this->db->trans_begin();
		if ( $this->db->update($this->table, $data, $where) > 0 ) {
	    	$this->db->where('ID_Importacion_Grupal', $where['ID_Importacion_Grupal']);
        	$this->db->delete($this->table_importacion_grupal_detalle);
			
			foreach($arrProducto as $row){
				$detalle[] = array(
					'ID_Empresa'				=> $this->user->ID_Empresa,
					'ID_Organizacion'			=> $this->user->ID_Organizacion,
					'ID_Importacion_Grupal' 	=> $where['ID_Importacion_Grupal'],
					'ID_Producto'				=> $this->security->xss_clean($row['id_item']),
				);
			}
			$this->db->insert_batch($this->table_importacion_grupal_detalle, $detalle);
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		} else {
			$this->db->trans_commit();
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
    }
    
	public function eliminarCliente($ID){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM importacion_grupal_pedido_cabecera WHERE ID_Importacion_Grupal=" . $ID . " LIMIT 1")->row()->existe > 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene movimiento(s)');
		} else {
			$this->db->where('ID_Importacion_Grupal', $ID);
            $this->db->delete($this->table_importacion_grupal_detalle);

			$this->db->where('ID_Importacion_Grupal', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 ) {
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		    }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
}
