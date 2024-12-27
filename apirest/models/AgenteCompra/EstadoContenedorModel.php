<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/SupplierTraits.php';
require_once APPPATH . 'traits/WebSocketTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';

class EstadoContenedorModel extends CI_Model
{
    use FileTrait, SupplierTraits, WebSocketTrait;
    public $table="agente_compra_pedido_booking_details";
    public $table_container="agente_compra_booking_container";
    public $table_naviera="agente_compra_booking_naviera";
    public $table_shipping="agente_compra_booking_shipper";
    public $table_agente="agente_compra_pedido_cabecera";
    public function __construct()
    {
        parent::__construct();
    }

    public function _get_datatables_query()
    {
        $this->db->select('*, agente_compra_booking_container.name as contenedor_tipo, agente_compra_booking_naviera.name as naviera, agente_compra_booking_shipper.name as shipper,agente_compra_pedido_booking_details.id as idBooking');
        $this->db->from($this->table);
        $this->db->join($this->table_container, 'agente_compra_pedido_booking_details.id_contenedor_tipo = agente_compra_booking_container.id', 'left');
        $this->db->join($this->table_naviera, 'agente_compra_pedido_booking_details.id_naviera = agente_compra_booking_naviera.id', 'left');
        $this->db->join($this->table_shipping, 'agente_compra_pedido_booking_details.id_shipper = agente_compra_booking_shipper.id', 'left');
        $this->db->join($this->table_agente, 'agente_compra_pedido_booking_details.id_pedido = agente_compra_pedido_cabecera.ID_Pedido_Cabecera', 'left');
        $filtroEstado = $this->input->post('Filtro_Estado');
        if($filtroEstado==1 ) {
            //if bl_telex or pagado not is 1    
            $this->db->where('pagado', 0);
            $this->db->or_where('bl_telex', 0);

        }else if($filtroEstado==2){

            //if bl_telex and pagado is 1
            $this->db->where('pagado', 1);
            $this->db->where('bl_telex', 1);
        }
        $this->db->where('(agente_compra_pedido_cabecera.booking_tipo != "CONSOLIDADO" OR agente_compra_pedido_cabecera.booking_tipo IS NULL)');


        return $this->db->get();
    }
    public function guardarTC($id, $tc)
    {
        $this->db->set('tc', $tc);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return $this->db->affected_rows();
    }
    public function guardarBlTelex($id, $bl_telex)
    {
        $this->db->set('bl_telex', $bl_telex);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return $this->db->affected_rows();
    }
    public function guardarPagado($id, $pagado)
    {
        $this->db->set('pagado', $pagado);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        return $this->db->affected_rows();
    }
    public function eliminar($idBooking){
        //get id_pedido and set booking_tipo to null in table_agente
        $this->db->select('id_pedido');
        $this->db->from($this->table);
        $this->db->where('id', $idBooking);
        $query = $this->db->get();
        $id_pedido = $query->row()->id_pedido;
        $this->db->set('booking_tipo', null);
        $this->db->where('ID_Pedido_Cabecera', $id_pedido);
        $this->db->update($this->table_agente);
        //delete booking
        $this->db->where('id', $idBooking);
        $this->db->delete($this->table);
        

        return $this->db->affected_rows();
    }
}