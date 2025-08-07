<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RegulacionesController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('BaseDatos/RegulacionesModel');
        $this->load->helper('url');
        $this->load->library('session');
        
        // Verificar sesión de usuario (opcional)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }
    public function index()
    {
        $data = [
            'title' => 'Gestión de Regulaciones',
            'js_regulaciones' => true
        ];
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/RegulacionesView', $data);
        $this->load->view('footer_v2', $data);
    }
    /**
     * Vista principal del listado de regulaciones
     */
    public function getAntidumpingData()
    {
        try {
            $data = $this->RegulacionesModel->getAntidumpingData();
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                //button view with function to show subtable
                $acciones = '<button class="btn btn-primary" onclick="showSubtable(' . $row['id'] . ')">Ver</button>';
                $array[] = [
                    $index,
                    $row['nombre'],
                    $acciones
                ];
                $index++;
            }
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
            echo json_encode($response);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de antidumping: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }

    }

    /**
     * Obtener datos para la tabla de Permisos (Entidades)
     */
    public function getPermisoData()
    {
        try {
            $data = $this->RegulacionesModel->getEntidadesConPermisos();
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                $acciones = '<div class="flex space-x-2">';
                $acciones .= '<button class="btn-show-subtable bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs" data-entidad-id="' . $row['id'] . '">Ver Detalles</button>';
                $acciones .= '</div>';
                
                $array[] = [
                    $index,
                    $row['nombre'] ?? 'N/A',
                    $row['total_permisos'] ?? '0',
                    date('d/m/Y', strtotime($row['ultima_actualizacion'] ?? 'now')),
                    $acciones
                ];
                $index++;
            }
            
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de permisos: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Obtener detalles de permisos por entidad
     */
    public function getAllPermisoData()
    {
        try {
            $entidad_id = $this->input->post('entidad_id');
            if (!$entidad_id) {
                throw new Exception('ID de entidad no proporcionado');
            }
            
            $data = $this->RegulacionesModel->getPermisosByEntidad($entidad_id);
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                $acciones = '<div class="flex space-x-2">';
                $acciones .= '<button class="btn-edit bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Editar</button>';
                $acciones .= '<button class="btn-delete bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Eliminar</button>';
                $acciones .= '<button class="btn-view bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '" data-observaciones="' . htmlspecialchars($row['observaciones'] ?? '') . '" data-documentos="' . htmlspecialchars(json_encode($this->getPermisoDocuments($row['id']))) . '">Ver</button>';
                $acciones .= '</div>';
                
                $array[] = [
                    $index,
                    $row['nombre'] ?? 'N/A',
                    $row['c_permiso'] ?? 'N/A',
                    $row['c_tramitador'] ?? 'N/A',
                    $row['rubro_nombre'] ?? 'N/A',
                    $row['observaciones'] ?? 'N/A',
                    date('d/m/Y', strtotime($row['created_at'])),
                    $acciones
                ];
                $index++;
            }
            
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de permisos: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Obtener datos para la tabla de Etiquetado
     */
    public function getEtiquetadoData()
    {
        try {
            $data = $this->RegulacionesModel->getEtiquetadoData();
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                $acciones = '<div class="flex space-x-2">';
                $acciones .= '<button class="btn-edit bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Editar</button>';
                $acciones .= '<button class="btn-delete bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Eliminar</button>';
                $acciones .= '</div>';
                
                $array[] = [
                    $index,
                    $row['rubro_nombre'] ?? 'N/A',
                    $row['observaciones'] ?? 'N/A',
                    date('d/m/Y', strtotime($row['created_at'])),
                    $acciones
                ];
                $index++;
            }
            
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de etiquetado: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Obtener datos para la tabla de Documentos Especiales
     */
    public function getDocumentosData()
    {
        try {
            $data = $this->RegulacionesModel->getDocumentosData();
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                $acciones = '<div class="flex space-x-2">';
                $acciones .= '<button class="btn-edit bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Editar</button>';
                $acciones .= '<button class="btn-delete bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Eliminar</button>';
                $acciones .= '</div>';
                
                $array[] = [
                    $index,
                    $row['rubro_nombre'] ?? 'N/A',
                    $row['observaciones'] ?? 'N/A',
                    date('d/m/Y', strtotime($row['created_at'])),
                    $acciones
                ];
                $index++;
            }
            
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de documentos especiales: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Guardar nueva regulación
     */
    public function saveRegulacion()
    {
        try {
            // Obtener datos POST
            $postData = $this->input->post();
            $files = $_FILES;
            
            // Validar que hay datos para guardar
            if (empty($postData)) {
                throw new Exception('No se recibieron datos para guardar');
            }
            
            // Procesar y guardar las regulaciones
            $result = $this->RegulacionesModel->saveRegulacion($postData, $files);
            
            // Verificar si se guardó correctamente
            if ($result && !empty($result)) {
                $response = [
                    'success' => true,
                    'message' => 'Regulaciones guardadas exitosamente',
                    'data' => $result
                ];
            } else {
                throw new Exception('Error al guardar las regulaciones');
            }
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al guardar regulación: ' . $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Eliminar regulación
     */
    public function deleteRegulacion()
    {
        try {
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');

            $result = $this->RegulacionesModel->deleteRegulacion($id, $tipo);

            $response = [
                'success' => true,
                'message' => 'Regulación eliminada exitosamente'
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al eliminar regulación: ' . $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Obtener todas las regulaciones antidumping para la subtabla
     */
    public function getAllAntidumpingData() {
        try {
            $id_rubro = $this->input->post('id_rubro');
            $data = $this->RegulacionesModel->getAllAntidumpingData($id_rubro);
            $array = [];
            $index = 1;
            foreach ($data as $row) {
                $acciones = '<div class="flex space-x-2">';
                $acciones .= '<button class="btn-view bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '" data-observaciones="' . htmlspecialchars($row['observaciones'] ?? '', ENT_QUOTES) . '" data-imagenes="' . htmlspecialchars(json_encode($this->getAntidumpingImages($row['id'])), ENT_QUOTES) . '"><i class="fas fa-eye"></i></button>';
                $acciones .= '<button class="btn-edit bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Editar</button>';
                $acciones .= '<button class="btn-delete bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" data-id="' . $row['id'] . '">Eliminar</button>';
                $acciones .= '</div>';
                
                $array[] = [
                    $index,
                    $row['descripcion_producto'],
                    $row['partida'],
                    $row['antidumping'] ?? 'N/A',
                    $row['antidumping'] ?? 'N/A',
                    $row['rubro_nombre'] ?? 'N/A',
                    $row['observaciones'] ?? 'N/A',
                    date('d/m/Y', strtotime($row['created_at'])),
                    $acciones
                ];
                $index++;
            }
            
            $response = [
                'success' => true,
                'data' => $array,
                'recordsTotal' => count($array),
                'recordsFiltered' => count($array)
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al obtener datos de antidumping: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
            
            header('Content-Type: application/json');
        echo json_encode($response);
    }
    }

    /**
     * Obtener rubros por producto
     */
    public function getRubrosByProduct() {
        try {
            $this->load->model('BaseDatos/RegulacionesModel');
            
            // Obtener todos los rubros de la tabla
            $rubros = $this->RegulacionesModel->getRubros();
            
            if ($rubros) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'data' => $rubros
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'No se encontraron rubros'
                    ]));
            }
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error al obtener rubros: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Obtener entidades reguladoras
     */
    public function getEntidadesReguladoras() {
        try {
            $this->load->model('BaseDatos/RegulacionesModel');
            
            // Obtener todas las entidades de la tabla
            $entidades = $this->RegulacionesModel->getEntidadesReguladoras();
            
            if ($entidades) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'data' => $entidades
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'No se encontraron entidades'
                    ]));
            }
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error al obtener entidades: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Crear nueva entidad reguladora
     */
    public function createEntidadReguladora() {
        try {
            $nombre = $this->input->post('nombre');
            $descripcion = $this->input->post('descripcion');
            
            if (empty($nombre)) {
                throw new Exception('El nombre de la entidad es obligatorio');
            }
            
            $this->load->model('BaseDatos/RegulacionesModel');
            $result = $this->RegulacionesModel->createEntidadReguladora($nombre, $descripcion);
            
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'Entidad creada exitosamente',
                        'data' => ['id' => $result, 'nombre' => $nombre]
                    ]));
            } else {
                throw new Exception('Error al crear la entidad');
            }
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error al crear entidad: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Obtener imágenes de una regulación antidumping
     */
    private function getAntidumpingImages($id_regulacion) {
        $this->db->select('*');
        $this->db->from('bd_productos_regulaciones_antidumping_media');
        $this->db->where('id_regulacion', $id_regulacion);
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getPermisoDocuments($id_regulacion) {
        try {
            $this->db->select('*');
            $this->db->from('bd_productos_regulaciones_permiso_media');
            $this->db->where('id_regulacion', $id_regulacion);
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            return [];
        }
    }
}
