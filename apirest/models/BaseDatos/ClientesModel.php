<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClientesModel extends CI_Model {

    private $table = 'clientes';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener clientes con paginación y filtros
     */
    public function getClientes($start = 0, $length = 10, $filters = [], $order_column = 0, $order_dir = 'asc') {
        $columns = ['id', 'codigo_cliente', 'razon_social', 'nombre_comercial', 'tipo_cliente', 'numero_documento', 'email', 'telefono', 'pais', 'estado', 'fecha_registro'];
        
        // Query base
        $this->db->select('*');
        $this->db->from($this->table);
        
        // Aplicar filtros
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('codigo_cliente', $filters['search']);
            $this->db->or_like('razon_social', $filters['search']);
            $this->db->or_like('nombre_comercial', $filters['search']);
            $this->db->or_like('numero_documento', $filters['search']);
            $this->db->or_like('email', $filters['search']);
            $this->db->or_like('telefono', $filters['search']);
            $this->db->group_end();
        }
        
        if (!empty($filters['tipo_cliente'])) {
            $this->db->where('tipo_cliente', $filters['tipo_cliente']);
        }
        
        if (!empty($filters['estado'])) {
            $this->db->where('estado', $filters['estado']);
        }
        
        if (!empty($filters['pais'])) {
            $this->db->where('pais', $filters['pais']);
        }
        
        // Contar total filtrado
        $filtered_query = clone $this->db;
        $total_filtered = $filtered_query->count_all_results('', false);
        
        // Aplicar ordenamiento
        if (isset($columns[$order_column])) {
            $this->db->order_by($columns[$order_column], $order_dir);
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
     * Obtener cliente por ID
     */
    public function getClienteById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Crear nuevo cliente
     */
    public function createCliente($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Actualizar cliente
     */
    public function updateCliente($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Eliminar cliente
     */
    public function deleteCliente($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Verificar si el documento ya existe
     */
    public function existeDocumento($numero_documento, $id_excluir = null) {
        $this->db->where('numero_documento', $numero_documento);
        
        if ($id_excluir) {
            $this->db->where('id !=', $id_excluir);
        }
        
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Generar código de cliente automático
     */
    public function generarCodigoCliente() {
        $this->db->select('codigo_cliente');
        $this->db->from($this->table);
        $this->db->like('codigo_cliente', 'CLI', 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $ultimo_codigo = $query->row()->codigo_cliente;
            $numero = intval(substr($ultimo_codigo, 3)) + 1;
        } else {
            $numero = 1;
        }
        
        return 'CLI' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener todos los clientes (para exportar)
     */
    public function getAllClientes() {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('razon_social', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar clientes para autocomplete
     */
    public function buscarClientes($term, $limit = 10) {
        $this->db->select('id, codigo_cliente, razon_social, nombre_comercial, numero_documento');
        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('razon_social', $term);
        $this->db->or_like('nombre_comercial', $term);
        $this->db->or_like('codigo_cliente', $term);
        $this->db->or_like('numero_documento', $term);
        $this->db->group_end();
        $this->db->where('estado', 'activo');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        $result = [];
        
        foreach ($query->result() as $row) {
            $result[] = [
                'id' => $row->id,
                'value' => $row->razon_social,
                'label' => $row->razon_social . ' (' . $row->codigo_cliente . ')',
                'codigo' => $row->codigo_cliente,
                'documento' => $row->numero_documento
            ];
        }
        
        return $result;
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function getEstadisticas() {
        $stats = [];
        
        // Total de clientes
        $stats['total'] = $this->db->count_all($this->table);
        
        // Clientes activos
        $this->db->where('estado', 'activo');
        $stats['activos'] = $this->db->count_all_results($this->table);
        
        // Clientes prospectos
        $this->db->reset_query();
        $this->db->where('estado', 'prospecto');
        $stats['prospectos'] = $this->db->count_all_results($this->table);
        
        // Clientes favoritos
        $this->db->reset_query();
        $this->db->where('favorito', 1);
        $stats['favoritos'] = $this->db->count_all_results($this->table);
        
        // Clientes por tipo
        $this->db->reset_query();
        $this->db->select('tipo_cliente, COUNT(*) as cantidad');
        $this->db->from($this->table);
        $this->db->group_by('tipo_cliente');
        $query = $this->db->get();
        $stats['por_tipo'] = $query->result();
        
        // Clientes por país
        $this->db->reset_query();
        $this->db->select('pais, COUNT(*) as cantidad');
        $this->db->from($this->table);
        $this->db->where('pais IS NOT NULL');
        $this->db->where('pais !=', '');
        $this->db->group_by('pais');
        $this->db->limit(10); // Top 10 países
        $query = $this->db->get();
        $stats['por_pais'] = $query->result();
        
        // Clientes registrados en el último mes
        $this->db->reset_query();
        $this->db->where('fecha_registro >=', date('Y-m-d', strtotime('-30 days')));
        $stats['nuevos_mes'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    /**
     * Marcar/desmarcar cliente como favorito
     */
    public function toggleFavorito($id, $favorito) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['favorito' => $favorito]);
    }

    /**
     * Obtener historial de actividades del cliente
     */
    public function getHistorialActividades($cliente_id) {
        // Esta función podría consultar una tabla de actividades/historial
        // Por ahora retornamos datos de ejemplo
        return [
            [
                'fecha' => date('Y-m-d H:i:s'),
                'actividad' => 'Cliente registrado',
                'usuario' => 'Sistema',
                'detalles' => 'Cliente creado en el sistema'
            ]
        ];
    }

    /**
     * Obtener clientes favoritos
     */
    public function getClientesFavoritos() {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('favorito', 1);
        $this->db->where('estado', 'activo');
        $this->db->order_by('razon_social', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener clientes por estado
     */
    public function getClientesPorEstado($estado) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('estado', $estado);
        $this->db->order_by('fecha_registro', 'desc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Actualizar última actividad del cliente
     */
    public function actualizarUltimaActividad($cliente_id) {
        $this->db->where('id', $cliente_id);
        return $this->db->update($this->table, ['ultima_actividad' => date('Y-m-d H:i:s')]);
    }

    /**
     * Obtener clientes próximos a cumpleaños (si se maneja fecha de nacimiento)
     */
    public function getClientesCumpleanos($dias = 7) {
        // Implementar según la estructura de la tabla
        // Si existe campo fecha_nacimiento
        return [];
    }
} 