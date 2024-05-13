<?php
class CCotizacionesModel extends CI_Model
{
    public $table_carga_consolidada = 'carga_consolidada';
    public $table = 'carga_consolidada_cotizaciones_cabecera';
    public $table_proveedor = "carga_consolidada_cotizaciones_detalles_proovedor";
    public $table_producto = "carga_consolidada_cotizaciones_detalles_producto";
    public $table_tributo = "carga_consolidada_cotizaciones_detalles_tributo";
    public $table_tipo_tributo = "tipo_carga_consolidada_cotizaciones_tributo";
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
    public function get_datatables()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
    public function get_cotization_header($ID_Cotizacion)
    {
        $this->db->select('N_Cliente,Empresa,SUM(cccdp.CBM_Total) AS Total_CBM,
        SUM(cccdp.Peso_Total) AS Total_Peso');
        $this->db->from($this->table);
        $this->db->join($this->table_proveedor . ' as cccdp',
            'cccdp.ID_Cotizacion = ' . $this->table . '.ID_Cotizacion ', 'join');
        $this->db->where($this->table . '.ID_Cotizacion', $ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_cotization_body($ID_Cotizacion)
    {

        $this->db->select("
        cccdprov.ID_Proveedor,
        cccdprov.CBM_Total,
        cccdprov.Peso_Total,
        (
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'ID_Producto', cccdpro.ID_Producto,
                        'URL_Link', cccdpro.URL_Link,
                        'Nombre_Comercial', cccdpro.Nombre_Comercial,
                        'Uso', cccdpro.Uso,
                        'Cantidad', cccdpro.Cantidad,
                        'Valor_unitario', IFNULL(cccdpro.Valor_unitario, 0),
                        'Tributos_Pendientes', (
                            SELECT
                                COUNT(*)
                            FROM
                                carga_consolidada_cotizaciones_detalles_tributo cccdt
                            WHERE
                                cccdt.ID_Producto = cccdpro.ID_Producto
                                AND cccdt.Status = 'Pending'
                        )
                    )
                )
            FROM
                carga_consolidada_cotizaciones_detalles_producto cccdpro
            WHERE
                cccdpro.ID_Cotizacion = cccdprov.ID_Cotizacion
                AND cccdpro.ID_Proveedor = cccdprov.ID_Proveedor
        ) AS productos
    ");
        $this->db->from($this->table_proveedor . ' as cccdprov');
        // $this->db->join($this->table_producto.' as cccdp2',
        // 'cccdp2.ID_Cotizacion = cccdp.ID_Cotizacion ','join');
        $this->db->where('cccdprov.ID_Cotizacion', $ID_Cotizacion);
        $query = $this->db->get();
        return $query->result();
    }
    public function guardarTributos($tributos)
    {
        $ID_Producto = $tributos['ID_Producto'];

        // Define un array asociativo que mapea los nombres de tributos a los valores proporcionados
        $tributosArray = array(
            'ad-valorem' => $tributos['ad-valorem'],
            'igv' => $tributos['igv'],
            'ipm' => $tributos['ipm'],
            'percepcion' => $tributos['percepcion'],
            'valoracion' => $tributos['valoracion'],
            'antidumping' => $tributos['antidumping'],
        );

        // Itera sobre cada tipo de tributo y actualiza su valor en la base de datos
        foreach ($tributosArray as $tipoTributo => $valor) {
            $this->db->where(array(
                "ID_Producto" => $ID_Producto,
                "ID_Tipo_Tributo" => $this->getTributoId($tipoTributo),

            ));

            $data = array("value" => $valor, "Status" => "COMPLETED");
            $this->db->update($this->table_tributo, $data);
        }

        // Retorna algún mensaje de éxito o indicador de éxito
        return array("success" => true);
    }
    public function getTributoId($table_key)
    {
        $this->db->select('ID_Tipo_Tributo');
        $this->db->from($this->table_tipo_tributo);
        $this->db->where('table_key', $table_key);
        $query = $this->db->get();
        $result = $query->row();
        return $result->ID_Tipo_Tributo;

    }
    public function guardarCotizacion($cotizacion)
    {
        //[{"ID_Proveedor":"9","CBM_Total":"150.00","Peso_Total":"1500.00","productos":[{"ID_Producto":"6","URL_Link":"https:\/\/music.youtube.com\/watch?v=zul8B399nzA&list=RDAMVMxQEV9lYHlNY","Nombre_Comercial":"Zapatos","Uso":"para los pies","Cantidad":"10000","Valor_Unitario":"0"},{"ID_Producto":"7","URL_Link":"31313","Nombre_Comercial":"1131","Uso":"313","Cantidad":"131","Valor_Unitario":"0"}]}]
        try{
            foreach($cotizacion as $cot){
                $ID_Proveedor = $cot['ID_Proveedor'];
                $CBM_Total = $cot['CBM_Total'];
                $Peso_Total = $cot['Peso_Total'];
                $this->db->where('ID_Proveedor', $ID_Proveedor);
                $this->db->update($this->table_proveedor, array("CBM_Total" => $CBM_Total, "Peso_Total" => $Peso_Total));
                foreach($cot['productos'] as $producto){
                    $ID_Producto = $producto['ID_Producto'];
                    $URL_Link = $producto['URL_Link'];
                    $Nombre_Comercial = $producto['Nombre_Comercial'];
                    $Uso = $producto['Uso'];
                    $Cantidad = $producto['Cantidad'];
                    $Valor_Unitario = $producto['Valor_Unitario'];
                    $this->db->where('ID_Producto', $ID_Producto);
                    $this->db->update($this->table_producto, array("URL_Link" => $URL_Link, "Nombre_Comercial" => $Nombre_Comercial, "Uso" => $Uso, "Cantidad" => $Cantidad, "Valor_Unitario" => $Valor_Unitario));
                }
            }
            return array("success" => true);

        }catch(Exception $e){
            return array("success" => false, "message" => $e->getMessage());
        }
        
    }
}
