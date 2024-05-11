<?php
class CCotizacionesModel extends CI_Model{
    var $table_carga_consolidada = 'carga_consolidada';
    var $table='carga_consolidada_cotizaciones_cabecera';
    var $table_proveedor="carga_consolidada_cotizaciones_detalles_proovedor";
    var $table_producto="carga_consolidada_cotizaciones_detalles_producto";
    var $table_tributo="carga_consolidada_cotizaciones_detalles_tributo";
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
    public function get_cotization_header($ID_Cotizacion){
        $this->db->select('N_Cliente,Empresa,SUM(cccdp.CBM_Total) AS Total_CBM,
        SUM(cccdp.Peso_Total) AS Total_Peso');
        $this->db->from($this->table);
        $this->db->join($this->table_proveedor.' as cccdp',
        'cccdp.ID_Cotizacion = '.$this->table.'.ID_Cotizacion ','join');
        $this->db->where($this->table.'.ID_Cotizacion',$ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();    
    }
    public function get_cotization_body($ID_Cotizacion){
        "select CBM_Total,Peso_Total,
        (select json_array(
            json_object(
            'ID_Proveedor',cccdp2.ID_Proveedor,
            'ID_Producto',cccdp2.ID_Producto,
            'URL_Link',cccdp2.URL_Link,
            'Nombre_Comercial',cccdp2.Nombre_Comercial,
            'Uso',cccdp2.Uso,
            'Cantidad',cccdp2.Cantidad,
            'Valor_unitario',if(Valor_unitario is null,0,Valor_unitario)
            ) 
        ) from carga_consolidada_cotizaciones_detalles_producto cccdp2 where cccdp2.ID_Cotizacion=cccdp.ID_Cotizacion)as productos,
        (select count(*) from carga_consolidada_cotizaciones_detalles_tributo cccdt where cccdt.Status='Pending') as pending
        ";
        $this->db->select('CBM_Total,Peso_Total,
        (select json_array(
            json_object(
            "ID_Proveedor",cccdp2.ID_Proveedor,
            "ID_Producto",cccdp2.ID_Producto,
            "URL_Link",cccdp2.URL_Link,
            "Nombre_Comercial",cccdp2.Nombre_Comercial,
            "Uso",cccdp2.Uso,
            "Cantidad",cccdp2.Cantidad,
            "Valor_unitario",if(Valor_unitario is null,0,Valor_unitario)
            ) 
        ) from carga_consolidada_cotizaciones_detalles_producto cccdp2 where cccdp2.ID_Cotizacion=cccdp.ID_Cotizacion)as productos');
        $this->db->from($this->table_proveedor.' as cccdp');
        $this->db->join($this->table_producto.' as cccdp2',
        'cccdp2.ID_Cotizacion = cccdp.ID_Cotizacion ','join');
        $this->db->where('cccdp.ID_Cotizacion',$ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();
    }
}

?>