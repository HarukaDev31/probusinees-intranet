<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductosModel extends CI_Model {

    private $table = 'bd_productos';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener todos los productos de todos los proveedores (sin paginación, sin agrupamiento)
     * Si se pasa $carga_id, filtra por esa campaña/carga
     */
    public function getProductos($idContenedor = 0, $tipoProducto = 0) {
        $this->db->select('p.*,c.carga as campana, r.nombre as rubro');
        $this->db->from($this->table.' p');
        $this->db->join('carga_consolidada_contenedor c', 'c.id = p.idContenedor','left');
        $this->db->join('bd_productos_rubro r', 'r.id = p.id_rubro','left');
        if ($idContenedor != 0) {
            $this->db->where('p.idContenedor', $idContenedor);
        }
        if ($tipoProducto != 0) {
            $this->db->where('p.tipo', $tipoProducto);
        }
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener producto por ID
     */
    public function getProductoById($id) {
        $this->db->select('p.*');
        $this->db->from($this->table . ' p');
        $this->db->join('carga_consolidada_contenedor cc', 'cc.id = p.idContenedor', 'left');
        $this->db->where('p.id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Crear nuevo producto
     */
    public function createProducto($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Actualizar producto
     */
    public function updateProducto($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Eliminar producto
     */
    public function deleteProducto($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Obtener todas las categorías
     */
    public function getCategorias() {
        $this->db->select('*');
        $this->db->from($this->categorias_table);
        $this->db->where('estado', 'activo');
        $this->db->order_by('nombre', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener todos los productos (para exportar)
     */
    public function getAllProductos() {
        $this->db->select('p.*, c.nombre as categoria');
        $this->db->from($this->table . ' p');
        $this->db->join($this->categorias_table . ' c', 'p.categoria_id = c.id', 'left');
        $this->db->order_by('p.nombre', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Actualizar stock de producto
     */
    public function updateStock($id, $cantidad, $tipo = 'entrada') {
        $producto = $this->getProductoById($id);
        
        if (!$producto) {
            return false;
        }
        
        $nuevo_stock = $tipo === 'entrada' 
            ? $producto->stock + $cantidad 
            : $producto->stock - $cantidad;
        
        // Evitar stock negativo
        if ($nuevo_stock < 0) {
            $nuevo_stock = 0;
        }
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['stock' => $nuevo_stock]);
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajo() {
        $this->db->select('p.*, c.nombre as categoria');
        $this->db->from($this->table . ' p');
        $this->db->join($this->categorias_table . ' c', 'p.categoria_id = c.id', 'left');
        $this->db->where('p.stock <=', 'p.stock_minimo', false);
        $this->db->where('p.estado', 'activo');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Verificar si el código de producto ya existe
     */
    public function existeCodigo($codigo, $id_excluir = null) {
        $this->db->where('codigo', $codigo);
        
        if ($id_excluir) {
            $this->db->where('id !=', $id_excluir);
        }
        
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Obtener estadísticas de productos
     */
    public function getEstadisticas() {
        $stats = [];
        
        // Total de productos
        $stats['total'] = $this->db->count_all($this->table);
        
        // Productos activos
        $this->db->where('estado', 'activo');
        $stats['activos'] = $this->db->count_all_results($this->table);
        
        // Productos con stock bajo
        $this->db->reset_query();
        $this->db->where('stock <=', 'stock_minimo', false);
        $this->db->where('estado', 'activo');
        $stats['stock_bajo'] = $this->db->count_all_results($this->table);
        
        // Productos agotados
        $this->db->reset_query();
        $this->db->where('stock', 0);
        $this->db->where('estado', 'activo');
        $stats['agotados'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    /**
     * Obtener productos por campaña y tipo (usando tablas de consolidado)
     */
    public function getProductosByCampana($idContenedor = 0, $tipoProducto = 0) {
        $this->db->select('p.id, p.nombre, p.foto, p.caracteristicas, p.rubro, p.tipo, p.unidad, p.precio, p.subpartida, c.carga as campana');
        $this->db->from('contenedor_consolidado_cotizacion_proveedores p');
        $this->db->join('carga_consolidada_contenedor c', 'c.id = p.id_contenedor');
        if ($idContenedor != 0) {
            $this->db->where('c.idContenedor', $idContenedor);
        }
        if ($tipoProducto != 0) {
            $this->db->where('p.tipo', $tipoProducto);
        }
        $query = $this->db->get();
        return $query->result();
    }
} 