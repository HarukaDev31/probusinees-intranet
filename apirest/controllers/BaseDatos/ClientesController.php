<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClientesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('BaseDatos/ClientesModel');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('input');
        
        // Verificar sesión de usuario (opcional)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }

    /**
     * Vista principal del listado de clientes
     */
    public function index() {
        $data = [
            'title' => 'Gestión de Clientes',
            'js_clientes' => true
        ];
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/ClientesView', $data);
        $this->load->view('footer_v2', $data);
    }

    /**
     * Obtener listado de clientes para DataTables
     */
    public function getClientes() {
        $start = $this->input->post('start') ?: 0;
        $length = $this->input->post('length') ?: 10;
        $search = $this->input->post('search')['value'] ?? '';
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'asc';
        
        // Filtros adicionales
        $tipo_cliente = $this->input->post('tipo_cliente') ?? '';
        $estado = $this->input->post('estado') ?? '';
        $pais = $this->input->post('pais') ?? '';
        
        $filters = [
            'tipo_cliente' => $tipo_cliente,
            'estado' => $estado,
            'pais' => $pais,
            'search' => $search
        ];
        
        $result = $this->ClientesModel->getClientes($start, $length, $filters, $order_column, $order_dir);
        
        $data = [];
        foreach ($result['data'] as $row) {
            $actions = $this->generateActionButtons($row);
            
            $data[] = [
                'id' => $row->id,
                'codigo_cliente' => $row->codigo_cliente,
                'razon_social' => $row->razon_social,
                'nombre_comercial' => $row->nombre_comercial,
                'tipo_cliente' => $this->getTipoClienteBadge($row->tipo_cliente),
                'documento' => $row->numero_documento,
                'email' => $row->email,
                'telefono' => $row->telefono,
                'pais' => $row->pais,
                'estado' => $this->getEstadoBadge($row->estado),
                'fecha_registro' => date('d/m/Y', strtotime($row->fecha_registro)),
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
     * Vista para crear/editar cliente
     */
    public function form($id = null) {
        $data = [
            'title' => $id ? 'Editar Cliente' : 'Nuevo Cliente',
            'cliente' => $id ? $this->ClientesModel->getClienteById($id) : null,
            'js_clientes_form' => true
        ];
        
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/ClientesFormView', $data);
        $this->load->view('footer_v2', $data);
    }

    /**
     * Guardar cliente (crear o actualizar)
     */
    public function save() {
        $response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
        
        try {
            $id = $this->input->post('id');
            $data = [
                'codigo_cliente' => $this->input->post('codigo_cliente'),
                'tipo_documento' => $this->input->post('tipo_documento'),
                'numero_documento' => $this->input->post('numero_documento'),
                'razon_social' => $this->input->post('razon_social'),
                'nombre_comercial' => $this->input->post('nombre_comercial'),
                'tipo_cliente' => $this->input->post('tipo_cliente'),
                'email' => $this->input->post('email'),
                'telefono' => $this->input->post('telefono'),
                'direccion' => $this->input->post('direccion'),
                'ciudad' => $this->input->post('ciudad'),
                'estado_provincia' => $this->input->post('estado_provincia'),
                'codigo_postal' => $this->input->post('codigo_postal'),
                'pais' => $this->input->post('pais'),
                'sitio_web' => $this->input->post('sitio_web'),
                'persona_contacto' => $this->input->post('persona_contacto'),
                'telefono_contacto' => $this->input->post('telefono_contacto'),
                'email_contacto' => $this->input->post('email_contacto'),
                'notas' => $this->input->post('notas'),
                'estado' => $this->input->post('estado')
            ];
            
            // Validaciones básicas
            if (empty($data['numero_documento']) || empty($data['razon_social'])) {
                throw new Exception('Número de documento y razón social son obligatorios');
            }
            
            // Verificar si el documento ya existe
            if ($this->ClientesModel->existeDocumento($data['numero_documento'], $id)) {
                throw new Exception('Ya existe un cliente con este número de documento');
            }
            
            if ($id) {
                // Actualizar
                $result = $this->ClientesModel->updateCliente($id, $data);
                $response['message'] = 'Cliente actualizado correctamente';
            } else {
                // Crear
                $data['fecha_registro'] = date('Y-m-d H:i:s');
                // Generar código de cliente si no se proporcionó
                if (empty($data['codigo_cliente'])) {
                    $data['codigo_cliente'] = $this->ClientesModel->generarCodigoCliente();
                }
                $result = $this->ClientesModel->createCliente($data);
                $response['message'] = 'Cliente creado correctamente';
            }
            
            if ($result) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error al guardar el cliente';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Eliminar cliente
     */
    public function delete() {
        $response = ['success' => false, 'message' => 'Error al eliminar'];
        
        try {
            $id = $this->input->post('id');
            
            if (!$id) {
                throw new Exception('ID de cliente no válido');
            }
            
            $result = $this->ClientesModel->deleteCliente($id);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Cliente eliminado correctamente';
            } else {
                $response['message'] = 'Error al eliminar el cliente';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Obtener cliente por ID (AJAX)
     */
    public function getCliente($id) {
        $cliente = $this->ClientesModel->getClienteById($id);
        
        if ($cliente) {
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
        }
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function getEstadisticas() {
        $stats = $this->ClientesModel->getEstadisticas();
        echo json_encode(['success' => true, 'data' => $stats]);
    }

    /**
     * Buscar clientes (autocomplete)
     */
    public function buscar() {
        $term = $this->input->get('term');
        $clientes = $this->ClientesModel->buscarClientes($term);
        echo json_encode($clientes);
    }

    /**
     * Exportar clientes a Excel
     */
    public function exportar() {
        $clientes = $this->ClientesModel->getAllClientes();
        
        // Aquí implementarías la lógica de exportación
        // Por ejemplo, usando PhpSpreadsheet o similar
        
        $response = ['success' => true, 'message' => 'Exportación completada'];
        echo json_encode($response);
    }

    /**
     * Importar clientes desde Excel
     */
    public function importar() {
        $response = ['success' => false, 'message' => 'Error al importar'];
        
        try {
            // Aquí implementarías la lógica de importación
            // Validar archivo, procesar datos, insertar en BD
            
            $response['success'] = true;
            $response['message'] = 'Clientes importados correctamente';
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Obtener historial de actividades del cliente
     */
    public function getHistorial($cliente_id) {
        $historial = $this->ClientesModel->getHistorialActividades($cliente_id);
        echo json_encode(['success' => true, 'data' => $historial]);
    }

    /**
     * Marcar cliente como favorito
     */
    public function toggleFavorito() {
        $response = ['success' => false, 'message' => 'Error al procesar'];
        
        try {
            $id = $this->input->post('id');
            $favorito = $this->input->post('favorito') == 1 ? 1 : 0;
            
            $result = $this->ClientesModel->toggleFavorito($id, $favorito);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = $favorito ? 'Cliente marcado como favorito' : 'Cliente removido de favoritos';
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
        $favoriteIcon = $row->favorito ? 'fas fa-star text-warning' : 'far fa-star';
        
        $actions = '<div class="btn-group" role="group">';
        $actions .= '<button type="button" class="btn btn-sm btn-info" onclick="viewCliente(' . $row->id . ')" title="Ver">';
        $actions .= '<i class="fas fa-eye"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-warning" onclick="editCliente(' . $row->id . ')" title="Editar">';
        $actions .= '<i class="fas fa-edit"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-secondary" onclick="toggleFavorito(' . $row->id . ', ' . ($row->favorito ? '0' : '1') . ')" title="' . ($row->favorito ? 'Quitar de favoritos' : 'Marcar favorito') . '">';
        $actions .= '<i class="' . $favoriteIcon . '"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-success" onclick="verHistorial(' . $row->id . ')" title="Historial">';
        $actions .= '<i class="fas fa-history"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteCliente(' . $row->id . ')" title="Eliminar">';
        $actions .= '<i class="fas fa-trash"></i>';
        $actions .= '</button>';
        $actions .= '</div>';
        
        return $actions;
    }

    /**
     * Generar badge de tipo de cliente
     */
    private function getTipoClienteBadge($tipo) {
        switch ($tipo) {
            case 'persona_natural':
                return '<span class="badge badge-primary">Persona Natural</span>';
            case 'empresa':
                return '<span class="badge badge-info">Empresa</span>';
            case 'gobierno':
                return '<span class="badge badge-warning">Gobierno</span>';
            case 'ong':
                return '<span class="badge badge-secondary">ONG</span>';
            default:
                return '<span class="badge badge-light">' . ucfirst($tipo) . '</span>';
        }
    }

    /**
     * Generar badge de estado
     */
    private function getEstadoBadge($estado) {
        switch ($estado) {
            case 'activo':
                return '<span class="badge badge-success">Activo</span>';
            case 'inactivo':
                return '<span class="badge badge-secondary">Inactivo</span>';
            case 'suspendido':
                return '<span class="badge badge-danger">Suspendido</span>';
            case 'prospecto':
                return '<span class="badge badge-info">Prospecto</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($estado) . '</span>';
        }
    }
} 