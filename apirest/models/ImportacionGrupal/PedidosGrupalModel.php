<?php
class PedidosGrupalModel extends CI_Model{
	var $table = 'importacion_grupal_pedido_cabecera';
	var $table_importacion_grupal_pedido_detalle = 'importacion_grupal_pedido_detalle';
	var $table_moneda = 'moneda';
	var $table_cliente = 'entidad';
	var $table_producto = 'producto';
	var $table_unidad_medida = 'unidad_medida';
	var $table_medio_pago = 'medio_pago';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, MONE.No_Moneda, CLI.No_Entidad, CLI.Nu_Celular_Entidad, MP.No_Medio_Pago_Tienda_Virtual')
		->from($this->table)
    	->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = ' . $this->table . '.ID_Moneda', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = ' . $this->table . '.ID_Medio_Pago', 'join')
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
    
    public function get_by_id($ID){
        $this->db->select($this->table . '.*, UM.No_Unidad_Medida, UM2.No_Unidad_Medida AS No_Unidad_Medida_2, CLI.No_Entidad, CLI.Nu_Documento_Identidad, CLI.Nu_Celular_Entidad, CLI.Txt_Email_Entidad, IGPD.ID_Producto, ITEM.No_Producto, IGPD.Qt_Producto, IGPD.Ss_Precio, IGPD.Ss_Total');
        $this->db->from($this->table);
    	$this->db->join($this->table_importacion_grupal_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
    	$this->db->join($this->table_producto . ' AS ITEM', 'ITEM.ID_Producto = IGPD.ID_Producto', 'join');
		$this->db->join($this->table_unidad_medida . ' AS UM', 'UM.ID_Unidad_Medida = IGPD.ID_Unidad_Medida', 'left');
		$this->db->join($this->table_unidad_medida . ' AS UM2', 'UM2.ID_Unidad_Medida = IGPD.ID_Unidad_Medida_Precio', 'left');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }

	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0)
			return array('status' => 'success', 'message' => 'Actualizado');
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
}
