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
                $acciones = '<button class="btn btn-primary" onclick="showSubtable(' . $row->id . ')">Ver</button>';
                $array[] = [
                    $index,
                    $row->nombre,
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
     * Obtener datos para la tabla de Permisos
     */
    public function getPermisoData()
    {
        try {
            $data = $this->RegulacionesModel->getPermisoData();

            $response = [
                'success' => true,
                'data' => $data,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data)
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

            $response = [
                'success' => true,
                'data' => $data,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data)
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

            $response = [
                'success' => true,
                'data' => $data,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data)
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
}
