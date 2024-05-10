<?php
class CCotizacionesModel extends CI_Model{
    var $table_carga_consolidada = 'carga_consolidada';
    var $table='carga_consolidada_cotizaciones_cabecera';

    public function __construct()
    {
        parent::__construct();        
    }
    public function _get_datatables_query()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        // Aquí puedes agregar cualquier condición o filtro que necesites
        // Ejemplo: $this->db->where('estado', 'activo');
    }
    public function get_datatables(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
}

?>