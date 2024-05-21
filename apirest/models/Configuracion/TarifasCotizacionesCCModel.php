<?php
class TarifasCotizacionesCCModel extends CI_Model{
    public $table = 'carga_consolidada_cbm_tarifas';
    public $table_type_clientes="carga_consolidada_tipo_cliente";
    public function __construct(){
		parent::__construct();

		
	}public function getTarifas(){
        $this->db->select('*');
        $this->db->from($this->table.' as t');
        $this->db->join($this->table_type_clientes.' as tc','t.id_tipo_cliente=tc.ID_Tipo_Cliente','inner');

        $query = $this->db->get();
        return $query->result();
    }
    
}