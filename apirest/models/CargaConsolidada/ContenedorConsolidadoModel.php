<?php
class ContenedorConsolidadoModel extends CI_Model{
	var $table_cliente = 'entidad';
	private $table="carga_consolidada_contenedor";
    private $table_pais="pais";

    var $order = array('carga_consolidada_pedido_cabecera.Fe_Registro' => 'desc');
	public function __construct(){
		parent::__construct();
	}
    public function index(){
        $this->db->select("*")
        ->from($this->table)
        ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join');
        $query = $this->db->get();
        return $query->result();

    }
}