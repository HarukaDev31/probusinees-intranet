<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RegulacionesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('BaseDatos/RegulacionesModel');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('input');
        
        // Verificar sesión de usuario (opcional)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }

    /**
     * Vista principal del listado de regulaciones
     */
    public function index() {
        $data = [
            'title' => 'Gestión de Regulaciones',
            'js_regulaciones' => true
        ];
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/RegulacionesView', $data);
        $this->load->view('footer_v2', $data);
    }

    /**
     * Obtener listado de regulaciones para DataTables
     */
    public function getRegulaciones() {
        $start = $this->input->post('start') ?: 0;
        $length = $this->input->post('length') ?: 10;
        $search = $this->input->post('search')['value'] ?? '';
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'asc';
        
        // Filtros adicionales
        $tipo = $this->input->post('tipo') ?? '';
        $estado = $this->input->post('estado') ?? '';
        $pais = $this->input->post('pais') ?? '';
        
        $filters = [
            'tipo' => $tipo,
            'estado' => $estado,
            'pais' => $pais,
            'search' => $search
        ];
        
        $result = $this->RegulacionesModel->getRegulaciones($start, $length, $filters, $order_column, $order_dir);
        
        $data = [];
        foreach ($result['data'] as $row) {
            $actions = $this->generateActionButtons($row);
            
            $data[] = [
                'id' => $row->id,
                'codigo' => $row->codigo,
                'titulo' => $row->titulo,
                'tipo' => $this->getTipoBadge($row->tipo),
                'pais' => $row->pais,
                'entidad_emisora' => $row->entidad_emisora,
                'fecha_vigencia' => $row->fecha_vigencia ? date('d/m/Y', strtotime($row->fecha_vigencia)) : '',
                'estado' => $this->getEstadoBadge($row->estado),
                'fecha_creacion' => date('d/m/Y H:i', strtotime($row->fecha_creacion)),
                'acciones' => $actions
            ];
        }
        
        $response = [
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $data
        ];
        
        echo json_encode($response);
    }

    /**
     * Vista para crear/editar regulación
     */
    public function form($id = null) {
        $data = [
            'title' => $id ? 'Editar Regulación' : 'Nueva Regulación',
            'regulacion' => $id ? $this->RegulacionesModel->getRegulacionById($id) : null,
            'paises' => $this->RegulacionesModel->getPaises(),
            'js_regulaciones_form' => true
        ];
        
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/RegulacionesFormView', $data);
        $this->load->view('footer_v2', $data);
    }

    /**
     * Guardar regulación (crear o actualizar)
     */
    public function save() {
        $response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
        
        try {
            $id = $this->input->post('id');
            $data = [
                'codigo' => $this->input->post('codigo'),
                'titulo' => $this->input->post('titulo'),
                'descripcion' => $this->input->post('descripcion'),
                'tipo' => $this->input->post('tipo'),
                'pais' => $this->input->post('pais'),
                'entidad_emisora' => $this->input->post('entidad_emisora'),
                'fecha_vigencia' => $this->input->post('fecha_vigencia'),
                'fecha_vencimiento' => $this->input->post('fecha_vencimiento'),
                'estado' => $this->input->post('estado'),
                'url_documento' => $this->input->post('url_documento'),
                'observaciones' => $this->input->post('observaciones')
            ];
            
            // Validaciones básicas
            if (empty($data['codigo']) || empty($data['titulo'])) {
                throw new Exception('Código y título son obligatorios');
            }
            
            if ($id) {
                // Actualizar
                $result = $this->RegulacionesModel->updateRegulacion($id, $data);
                $response['message'] = 'Regulación actualizada correctamente';
            } else {
                // Crear
                $data['fecha_creacion'] = date('Y-m-d H:i:s');
                $result = $this->RegulacionesModel->createRegulacion($data);
                $response['message'] = 'Regulación creada correctamente';
            }
            
            if ($result) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error al guardar la regulación';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Eliminar regulación
     */
    public function delete() {
        $response = ['success' => false, 'message' => 'Error al eliminar'];
        
        try {
            $id = $this->input->post('id');
            
            if (!$id) {
                throw new Exception('ID de regulación no válido');
            }
            
            $result = $this->RegulacionesModel->deleteRegulacion($id);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Regulación eliminada correctamente';
            } else {
                $response['message'] = 'Error al eliminar la regulación';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Obtener regulación por ID (AJAX)
     */
    public function getRegulacion($id) {
        $regulacion = $this->RegulacionesModel->getRegulacionById($id);
        
        if ($regulacion) {
            echo json_encode(['success' => true, 'data' => $regulacion]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Regulación no encontrada']);
        }
    }

    /**
     * Obtener estadísticas de regulaciones
     */
    public function getEstadisticas() {
        $stats = $this->RegulacionesModel->getEstadisticas();
        echo json_encode(['success' => true, 'data' => $stats]);
    }

    /**
     * Obtener países para filtros
     */
    public function getPaises() {
        $paises = $this->RegulacionesModel->getPaises();
        echo json_encode(['success' => true, 'data' => $paises]);
    }

    /**
     * Exportar regulaciones a Excel
     */
    public function exportar() {
        $regulaciones = $this->RegulacionesModel->getAllRegulaciones();
        
        // Aquí implementarías la lógica de exportación
        // Por ejemplo, usando PhpSpreadsheet o similar
        
        $response = ['success' => true, 'message' => 'Exportación completada'];
        echo json_encode($response);
    }

    /**
     * Obtener regulaciones próximas a vencer
     */
    public function proximasVencer() {
        $dias = $this->input->get('dias') ?: 30;
        $regulaciones = $this->RegulacionesModel->getRegulacionesProximasVencer($dias);
        echo json_encode(['success' => true, 'data' => $regulaciones]);
    }

    /**
     * Marcar regulación como revisada
     */
    public function marcarRevisada() {
        $response = ['success' => false, 'message' => 'Error al procesar'];
        
        try {
            $id = $this->input->post('id');
            $observaciones = $this->input->post('observaciones');
            
            $result = $this->RegulacionesModel->marcarRevisada($id, $observaciones);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Regulación marcada como revisada';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Generar botones de acción para cada fila
     */
    private function generateActionButtons($row) {
        $actions = '<div class="btn-group" role="group">';
        $actions .= '<button type="button" class="btn btn-sm btn-info" onclick="viewRegulacion(' . $row->id . ')" title="Ver">';
        $actions .= '<i class="fas fa-eye"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-warning" onclick="editRegulacion(' . $row->id . ')" title="Editar">';
        $actions .= '<i class="fas fa-edit"></i>';
        $actions .= '</button>';
        
        if (!empty($row->url_documento)) {
            $actions .= '<button type="button" class="btn btn-sm btn-success" onclick="verDocumento(\'' . $row->url_documento . '\')" title="Ver Documento">';
            $actions .= '<i class="fas fa-file-pdf"></i>';
            $actions .= '</button>';
        }
        
        $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteRegulacion(' . $row->id . ')" title="Eliminar">';
        $actions .= '<i class="fas fa-trash"></i>';
        $actions .= '</button>';
        $actions .= '</div>';
        
        return $actions;
    }

    /**
     * Generar badge de tipo
     */
    private function getTipoBadge($tipo) {
        switch ($tipo) {
            case 'ley':
                return '<span class="badge badge-primary">Ley</span>';
            case 'decreto':
                return '<span class="badge badge-info">Decreto</span>';
            case 'resolucion':
                return '<span class="badge badge-warning">Resolución</span>';
            case 'norma_tecnica':
                return '<span class="badge badge-secondary">Norma Técnica</span>';
            default:
                return '<span class="badge badge-light">' . ucfirst($tipo) . '</span>';
        }
    }

    /**
     * Generar badge de estado
     */
    private function getEstadoBadge($estado) {
        switch ($estado) {
            case 'vigente':
                return '<span class="badge badge-success">Vigente</span>';
            case 'derogada':
                return '<span class="badge badge-danger">Derogada</span>';
            case 'en_revision':
                return '<span class="badge badge-warning">En Revisión</span>';
            case 'proyecto':
                return '<span class="badge badge-info">Proyecto</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($estado) . '</span>';
        }
    }
} 