<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RegulacionesModel extends CI_Model {

    private $table = 'regulaciones';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener regulaciones con paginación y filtros
     */
    public function getRegulaciones($start = 0, $length = 10, $filters = [], $order_column = 0, $order_dir = 'asc') {
        $columns = ['id', 'codigo', 'titulo', 'tipo', 'pais', 'entidad_emisora', 'fecha_vigencia', 'estado', 'fecha_creacion'];
        
        // Query base
        $this->db->select('*');
        $this->db->from($this->table);
        
        // Aplicar filtros
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('codigo', $filters['search']);
            $this->db->or_like('titulo', $filters['search']);
            $this->db->or_like('descripcion', $filters['search']);
            $this->db->or_like('entidad_emisora', $filters['search']);
            $this->db->group_end();
        }
        
        if (!empty($filters['tipo'])) {
            $this->db->where('tipo', $filters['tipo']);
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
     * Obtener regulación por ID
     */
    public function getRegulacionById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Crear nueva regulación
     */
    public function createRegulacion($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Actualizar regulación
     */
    public function updateRegulacion($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Eliminar regulación
     */
    public function deleteRegulacion($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Obtener países únicos
     */
    public function getPaises() {
        $this->db->distinct();
        $this->db->select('pais');
        $this->db->from($this->table);
        $this->db->where('pais IS NOT NULL');
        $this->db->where('pais !=', '');
        $this->db->order_by('pais', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener todas las regulaciones (para exportar)
     */
    public function getAllRegulaciones() {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('titulo', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener regulaciones próximas a vencer
     */
    public function getRegulacionesProximasVencer($dias = 30) {
        $fecha_limite = date('Y-m-d', strtotime('+' . $dias . ' days'));
        
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('fecha_vencimiento <=', $fecha_limite);
        $this->db->where('fecha_vencimiento >=', date('Y-m-d'));
        $this->db->where('estado', 'vigente');
        $this->db->order_by('fecha_vencimiento', 'asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Marcar regulación como revisada
     */
    public function marcarRevisada($id, $observaciones = '') {
        $data = [
            'ultima_revision' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Obtener estadísticas de regulaciones
     */
    public function getEstadisticas() {
        $stats = [];
        
        // Total de regulaciones
        $stats['total'] = $this->db->count_all($this->table);
        
        // Regulaciones vigentes
        $this->db->where('estado', 'vigente');
        $stats['vigentes'] = $this->db->count_all_results($this->table);
        
        // Regulaciones en revisión
        $this->db->reset_query();
        $this->db->where('estado', 'en_revision');
        $stats['en_revision'] = $this->db->count_all_results($this->table);
        
        // Regulaciones próximas a vencer (30 días)
        $this->db->reset_query();
        $fecha_limite = date('Y-m-d', strtotime('+30 days'));
        $this->db->where('fecha_vencimiento <=', $fecha_limite);
        $this->db->where('fecha_vencimiento >=', date('Y-m-d'));
        $this->db->where('estado', 'vigente');
        $stats['proximas_vencer'] = $this->db->count_all_results($this->table);
        
        // Regulaciones por tipo
        $this->db->reset_query();
        $this->db->select('tipo, COUNT(*) as cantidad');
        $this->db->from($this->table);
        $this->db->group_by('tipo');
        $query = $this->db->get();
        $stats['por_tipo'] = $query->result();
        
        // Regulaciones por país
        $this->db->reset_query();
        $this->db->select('pais, COUNT(*) as cantidad');
        $this->db->from($this->table);
        $this->db->where('pais IS NOT NULL');
        $this->db->where('pais !=', '');
        $this->db->group_by('pais');
        $this->db->limit(10); // Top 10 países
        $query = $this->db->get();
        $stats['por_pais'] = $query->result();
        
        return $stats;
    }

    /**
     * Verificar si el código de regulación ya existe
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
     * Buscar regulaciones por palabra clave
     */
    public function buscarPorPalabraClave($keyword) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('titulo', $keyword);
        $this->db->or_like('descripcion', $keyword);
        $this->db->or_like('observaciones', $keyword);
        $this->db->group_end();
        $this->db->where('estado', 'vigente');
        $this->db->order_by('fecha_vigencia', 'desc');
        
        $query = $this->db->get();
        return $query->result();
    }
} 