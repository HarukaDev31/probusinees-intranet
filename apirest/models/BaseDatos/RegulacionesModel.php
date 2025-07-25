<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'traits/FileTrait.php';

class RegulacionesModel extends CI_Model {
    use FileTrait;
    private $table_rubros = 'bd_productos_rubro';
    private $table_entidades = 'bd_entidades_reguladoras';
    private $table_regulaciones_antidumping = 'bd_productos_regulaciones_antidumping';
    private $table_regulaciones_antidumping_media = 'bd_productos_regulaciones_antidumping_media';
    private $table_regulaciones_permiso = 'bd_productos_regulaciones_permiso';
    private $table_regulaciones_permiso_media = 'bd_productos_regulaciones_permiso_media';
    private $table_regulaciones_etiquetado = 'bd_productos_regulaciones_etiquetado';
    private $table_regulaciones_etiquetado_media = 'bd_productos_regulaciones_etiquetado_media';
    private $table_regulaciones_documentos_especiales = 'bd_productos_regulaciones_documentos_especiales';
    private $table_regulaciones_documentos_especiales_media = 'bd_productos_regulaciones_documentos_especiales_media';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener datos de regulaciones antidumping
     */
    public function getAntidumpingData() {
        $this->db->select('ra.id, ra.descripcion_producto, ra.partida, ra.antidumping, ra.observaciones, r.nombre as rubro_nombre, ra.created_at');
        $this->db->from($this->table_regulaciones_antidumping . ' ra');
        $this->db->join($this->table_rubros . ' r', 'r.id = ra.id_rubro', 'left');
        $this->db->order_by('ra.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtener datos de regulaciones de permisos
     */
    public function getPermisoData() {
        $this->db->select('rp.id, rp.nombre, rp.c_permiso, rp.c_tramitador, rp.observaciones, er.nombre as entidad_nombre, r.nombre as rubro_nombre, rp.created_at');
        $this->db->from($this->table_regulaciones_permiso . ' rp');
        $this->db->join($this->table_entidades . ' er', 'er.id = rp.id_entidad_reguladora', 'left');
        $this->db->join($this->table_rubros . ' r', 'r.id = rp.id_rubro', 'left');
        $this->db->order_by('rp.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtener datos de regulaciones de etiquetado
     */
    public function getEtiquetadoData() {
        $this->db->select('re.id, re.observaciones, r.nombre as rubro_nombre, re.created_at');
        $this->db->from($this->table_regulaciones_etiquetado . ' re');
        $this->db->join($this->table_rubros . ' r', 'r.id = re.id_rubro', 'left');
        $this->db->order_by('re.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtener datos de documentos especiales
     */
    public function getDocumentosData() {
        $this->db->select('rd.id, rd.observaciones, r.nombre as rubro_nombre, rd.created_at');
        $this->db->from($this->table_regulaciones_documentos_especiales . ' rd');
        $this->db->join($this->table_rubros . ' r', 'r.id = rd.id_rubro', 'left');
        $this->db->order_by('rd.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtener entidades reguladoras
     */
    public function getEntidadesReguladoras() {
        $this->db->select('id, nombre');
        $this->db->from($this->table_entidades);
        $this->db->order_by('nombre', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtener rubros
     */
    public function getRubros() {
        $this->db->select('id, nombre');
        $this->db->from($this->table_rubros);
        $this->db->order_by('nombre', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Guardar regulación antidumping
     */
    private function saveAntidumpingRegulacion($data, $files = []) {
        $this->db->trans_start();
        
        // Mapear los nombres de campos del FormData a los nombres de la BD
        $regulacionData = [
            'id_rubro' => isset($data['id_rubro']) ? $data['id_rubro'] : 1,
            'descripcion_producto' => isset($data['antidumping-description']) ? $data['antidumping-description'] : (isset($data['descripcion_producto']) ? $data['descripcion_producto'] : ''),
            'partida' => isset($data['antidumping-partida']) ? $data['antidumping-partida'] : (isset($data['partida']) ? $data['partida'] : ''),
            'antidumping' => isset($data['antidumping-antidumping']) ? $data['antidumping-antidumping'] : (isset($data['antidumping']) ? $data['antidumping'] : 0),
            'observaciones' => isset($data['antidumping-observaciones']) ? $data['antidumping-observaciones'] : (isset($data['observaciones']) ? $data['observaciones'] : '')
        ];
        
        // Debug: log los datos recibidos
        log_message('debug', 'Datos antidumping recibidos: ' . json_encode($data));
        log_message('debug', 'Datos mapeados: ' . json_encode($regulacionData));
        
        $this->db->insert($this->table_regulaciones_antidumping, $regulacionData);
        $regulacionId = $this->db->insert_id();
        
        // Guardar archivos si existen
        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadPath = 'uploads/regulaciones/antidumping/';
                $fileName = time() . '_' . $file['name'];
                $fullPath = $uploadPath . $fileName;
                
                // Crear directorio si no existe
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Mover archivo
                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $mediaData = [
                        'id_regulacion' => $regulacionId,
                        'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                        'peso' => $file['size'],
                        'nombre_original' => $file['name'],
                        'ruta' => $fullPath
                    ];
                    $this->db->insert($this->table_regulaciones_antidumping_media, $mediaData);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $regulacionId : false;
    }

    /**
     * Guardar regulación de permiso
     */
    private function savePermisoRegulacion($data, $files = []) {
        $this->db->trans_start();
        
        // Mapear los nombres de campos del FormData a los nombres de la BD
        $regulacionData = [
            'id_rubro' => isset($data['id_rubro']) ? $data['id_rubro'] : 1,
            'id_entidad_reguladora' => isset($data['permiso-entidad']) ? $data['permiso-entidad'] : (isset($data['id_entidad_reguladora']) ? $data['id_entidad_reguladora'] : 1),
            'nombre' => isset($data['permiso-nombre']) ? $data['permiso-nombre'] : (isset($data['nombre']) ? $data['nombre'] : ''),
            'c_permiso' => isset($data['permiso-costo']) ? $data['permiso-costo'] : (isset($data['c_permiso']) ? $data['c_permiso'] : 0),
            'c_tramitador' => isset($data['permiso-tramitador']) ? $data['permiso-tramitador'] : (isset($data['c_tramitador']) ? $data['c_tramitador'] : 0),
            'observaciones' => isset($data['permiso-observaciones']) ? $data['permiso-observaciones'] : (isset($data['observaciones']) ? $data['observaciones'] : '')
        ];
        
        // Debug: log los datos recibidos
        log_message('debug', 'Datos permiso recibidos: ' . json_encode($data));
        log_message('debug', 'Datos mapeados: ' . json_encode($regulacionData));
        
        $this->db->insert($this->table_regulaciones_permiso, $regulacionData);
        $regulacionId = $this->db->insert_id();
        
        // Guardar archivos si existen
        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadPath = 'uploads/regulaciones/permiso/';
                $fileName = time() . '_' . $file['name'];
                $fullPath = $uploadPath . $fileName;
                
                // Crear directorio si no existe
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Mover archivo
                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $mediaData = [
                        'id_regulacion' => $regulacionId,
                        'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                        'peso' => $file['size'],
                        'nombre_original' => $file['name'],
                        'ruta' => $fullPath
                    ];
                    $this->db->insert($this->table_regulaciones_permiso_media, $mediaData);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $regulacionId : false;
    }

    /**
     * Guardar regulación de etiquetado
     */
    private function saveEtiquetadoRegulacion($data, $files = []) {
        $this->db->trans_start();
        
        // Mapear los nombres de campos del FormData a los nombres de la BD
        $regulacionData = [
            'id_rubro' => isset($data['id_rubro']) ? $data['id_rubro'] : 1,
            'observaciones' => isset($data['etiquetado-observaciones']) ? $data['etiquetado-observaciones'] : (isset($data['observaciones']) ? $data['observaciones'] : '')
        ];
        
        // Debug: log los datos recibidos
        log_message('debug', 'Datos etiquetado recibidos: ' . json_encode($data));
        log_message('debug', 'Datos mapeados: ' . json_encode($regulacionData));
        
        $this->db->insert($this->table_regulaciones_etiquetado, $regulacionData);
        $regulacionId = $this->db->insert_id();
        
        // Guardar archivos si existen
        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadPath = 'uploads/regulaciones/etiquetado/';
                $fileName = time() . '_' . $file['name'];
                $fullPath = $uploadPath . $fileName;
                
                // Crear directorio si no existe
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Mover archivo
                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $mediaData = [
                        'id_regulacion' => $regulacionId,
                        'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                        'peso' => $file['size'],
                        'nombre_original' => $file['name'],
                        'ruta' => $fullPath
                    ];
                    $this->db->insert($this->table_regulaciones_etiquetado_media, $mediaData);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $regulacionId : false;
    }

    /**
     * Guardar regulación de documentos especiales
     */
    private function saveDocumentosEspecialesRegulacion($data, $files = []) {
        $this->db->trans_start();
        
        // Mapear los nombres de campos del FormData a los nombres de la BD
        $regulacionData = [
            'id_rubro' => isset($data['id_rubro']) ? $data['id_rubro'] : 1,
            'observaciones' => isset($data['documentos-observaciones']) ? $data['documentos-observaciones'] : (isset($data['observaciones']) ? $data['observaciones'] : '')
        ];
        
        // Debug: log los datos recibidos
        log_message('debug', 'Datos documentos especiales recibidos: ' . json_encode($data));
        log_message('debug', 'Datos mapeados: ' . json_encode($regulacionData));
        
        $this->db->insert($this->table_regulaciones_documentos_especiales, $regulacionData);
        $regulacionId = $this->db->insert_id();
        
        // Guardar archivos si existen
        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadPath = 'uploads/regulaciones/documentos_especiales/';
                $fileName = time() . '_' . $file['name'];
                $fullPath = $uploadPath . $fileName;
                
                // Crear directorio si no existe
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Mover archivo
                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $mediaData = [
                        'id_regulacion' => $regulacionId,
                        'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                        'peso' => $file['size'],
                        'nombre_original' => $file['name'],
                        'ruta' => $fullPath
                    ];
                    $this->db->insert($this->table_regulaciones_documentos_especiales_media, $mediaData);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $regulacionId : false;
    }

    /**
     * Guardar nueva regulación (método principal)
     */
    public function saveRegulacion($postData, $files) {
        $results = [];
        
        // Procesar cada tipo de regulación
        foreach ($postData as $tipo => $data) {
            if ($tipo === 'producto' || $tipo === 'id_rubro' || $tipo === 'created_at') {
                continue; // Saltar campos generales
            }
            
            $tipoFiles = [];
            if (isset($files[$tipo]['archivos'])) {
                $tipoFiles = $files[$tipo]['archivos'];
            }
            
            switch ($tipo) {
                case 'antidumping':
                    $results['antidumping'] = $this->saveAntidumpingRegulacion($data, $tipoFiles);
                    break;
                case 'permiso':
                    $results['permiso'] = $this->savePermisoRegulacion($data, $tipoFiles);
                    break;
                case 'etiquetado':
                    $results['etiquetado'] = $this->saveEtiquetadoRegulacion($data, $tipoFiles);
                    break;
                case 'documentos':
                    $results['documentos'] = $this->saveDocumentosEspecialesRegulacion($data, $tipoFiles);
                    break;
            }
        }
        
        return $results;
    }

    /**
     * Eliminar regulación
     */
    public function deleteRegulacion($id, $tipo) {
        $this->db->trans_start();
        
        switch ($tipo) {
            case 'antidumping':
                $this->db->where('id_regulacion', $id);
                $this->db->delete($this->table_regulaciones_antidumping_media);
                $this->db->where('id', $id);
                $this->db->delete($this->table_regulaciones_antidumping);
                break;
            case 'permiso':
                $this->db->where('id_regulacion', $id);
                $this->db->delete($this->table_regulaciones_permiso_media);
                $this->db->where('id', $id);
                $this->db->delete($this->table_regulaciones_permiso);
                break;
            case 'etiquetado':
                $this->db->where('id_regulacion', $id);
                $this->db->delete($this->table_regulaciones_etiquetado_media);
                $this->db->where('id', $id);
                $this->db->delete($this->table_regulaciones_etiquetado);
                break;
            case 'documentos':
                $this->db->where('id_regulacion', $id);
                $this->db->delete($this->table_regulaciones_documentos_especiales_media);
                $this->db->where('id', $id);
                $this->db->delete($this->table_regulaciones_documentos_especiales);
                break;
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Obtener regulación por ID
     */
    public function getRegulacionById($id, $tipo) {
        switch ($tipo) {
            case 'antidumping':
                $this->db->select('ra.*, r.nombre as rubro_nombre');
                $this->db->from($this->table_regulaciones_antidumping . ' ra');
                $this->db->join($this->table_rubros . ' r', 'r.id = ra.id_rubro', 'left');
                $this->db->where('ra.id', $id);
                break;
            case 'permiso':
                $this->db->select('rp.*, er.nombre as entidad_nombre, r.nombre as rubro_nombre');
                $this->db->from($this->table_regulaciones_permiso . ' rp');
                $this->db->join($this->table_entidades . ' er', 'er.id = rp.id_entidad_reguladora', 'left');
                $this->db->join($this->table_rubros . ' r', 'r.id = rp.id_rubro', 'left');
                $this->db->where('rp.id', $id);
                break;
            case 'etiquetado':
                $this->db->select('re.*, r.nombre as rubro_nombre');
                $this->db->from($this->table_regulaciones_etiquetado . ' re');
                $this->db->join($this->table_rubros . ' r', 'r.id = re.id_rubro', 'left');
                $this->db->where('re.id', $id);
                break;
            case 'documentos':
                $this->db->select('rd.*, r.nombre as rubro_nombre');
                $this->db->from($this->table_regulaciones_documentos_especiales . ' rd');
                $this->db->join($this->table_rubros . ' r', 'r.id = rd.id_rubro', 'left');
                $this->db->where('rd.id', $id);
                break;
            default:
                return null;
        }
        
        $query = $this->db->get();
        return $query->row_array();
    }
}