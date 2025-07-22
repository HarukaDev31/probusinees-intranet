<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductosModel extends CI_Model {

    private $table = 'productos';
    private $categorias_table = 'categorias_productos';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener productos con paginación y filtros
     */
    public function getProductos($start = 0, $length = 10, $filters = [], $order_column = 0, $order_dir = 'asc') {
        $columns = ['id', 'codigo', 'nombre', 'categoria', 'precio', 'stock', 'estado', 'fecha_creacion'];
        
        // Query base con JOIN a categorías
        $this->db->select('p.*, c.nombre as categoria');
        $this->db->from($this->table . ' p');
        $this->db->join($this->categorias_table . ' c', 'p.categoria_id = c.id', 'left');
        
        // Aplicar filtros
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('p.codigo', $filters['search']);
            $this->db->or_like('p.nombre', $filters['search']);
            $this->db->or_like('p.descripcion', $filters['search']);
            $this->db->or_like('c.nombre', $filters['search']);
            $this->db->group_end();
        }
        
        if (!empty($filters['categoria'])) {
            $this->db->where('p.categoria_id', $filters['categoria']);
        }
        
        if (!empty($filters['estado'])) {
            $this->db->where('p.estado', $filters['estado']);
        }
        
        // Contar total filtrado
        $filtered_query = clone $this->db;
        $total_filtered = $filtered_query->count_all_results('', false);
        
        // Aplicar ordenamiento
        if (isset($columns[$order_column])) {
            $order_column_name = $columns[$order_column] === 'categoria' ? 'c.nombre' : 'p.' . $columns[$order_column];
            $this->db->order_by($order_column_name, $order_dir);
        }
        
        // Aplicar paginación
        $this->db->limit($length, $start);
        
        $query = $this->db->get();
        $data = $query->result();
        
        // Contar total de registros
        $total_records = $this->db->count_all($this->table);
        
        return [
            'data' => $data,
            'total' => $total_records,
            'filtered' => $total_filtered
        ];
    }

    /**
     * Obtener producto por ID
     */
    public function getProductoById($id) {
        $this->db->select('p.*, c.nombre as categoria_nombre');
        $this->db->from($this->table . ' p');
        $this->db->join($this->categorias_table . ' c', 'p.categoria_id = c.id', 'left');
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
} 