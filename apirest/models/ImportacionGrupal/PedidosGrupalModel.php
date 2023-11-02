<?php
class PedidosGrupalModel extends CI_Model{
	var $table = 'importacion_grupal_pedido_cabecera';
	var $table_importacion_grupal_pedido_detalle = 'importacion_grupal_pedido_detalle';
	var $table_moneda = 'moneda';
	var $table_producto = 'producto';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, MONE.No_Moneda')
		->from($this->table)
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = ' . $this->table . '.ID_Moneda', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);
        
		if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
}
