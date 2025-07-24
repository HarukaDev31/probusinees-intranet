<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'traits/FileTrait.php';

class ProductosController extends CI_Controller {
    use FileTrait;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('BaseDatos/ProductosModel');
        $this->load->library('upload');
        $this->load->helper('url');
        
        // Configurar FileTrait para archivos de oficina
        $this->setAllowedExtensionsImagesOfficeFiles();
        
        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }
    }

    /**
     * Vista principal del listado de productos
     */
    public function index() {
        $data = [
            'title' => 'Gestión de Productos',
            'js_productos' => true
        ];
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/ProductosView', $data);
        $this->load->view('footer_v2', $data);
    }
 
    /**
     * Obtener listado de productos para DataTables
     */
    public function getProductos() {
        $idContenedor = $this->input->post('idContenedor');
        $tipoProducto = $this->input->post('tipo');
        $productos = $this->ProductosModel->getProductos($idContenedor, $tipoProducto);

        $data = [];
        $index = 1;
        foreach ($productos as $row) {
            $actions = $this->generateActionButtons($row);
            //id |idContenedor|item|nombre_comercial                       |foto|caracteristicas                                                                                                                                                                                                                                                |rubro                        |tipo_producto|precio_exw|subpartida   |link                                                                                                                                                                                                                                                           |unidad_comercial|arancel_sunat|arancel_tlc|antidumping|correlativo              |etiquetado|doc_especial|created_at         |updated_at         |deleted_at|
            $data[] = [
                $index,
                $row->nombre_comercial,
                $row->foto,
                $row->caracteristicas,
                $row->rubro,
                $row->tipo_producto,
                $row->unidad_comercial,
                $row->precio_exw,
                $row->subpartida,
                "#".$row->campana,
                $actions
            ];
            $index++;
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
     * Vista para crear/editar producto
     */
    public function form($id = null) {
        $data = [
            'title' => $id ? 'Editar Producto' : 'Nuevo Producto',
            'producto' => $id ? $this->ProductosModel->getProductoById($id) : null,
            'categorias' => $this->ProductosModel->getCategorias(),
            'js_productos_form' => true
        ];
        
        $this->load->view('header_v2', $data);
        $this->load->view('BaseDatos/ProductosFormView', $data);
        $this->load->view('footer_v2', $data);
    }

    /**
     * Guardar producto (crear o actualizar)
     */
    public function save() {
        $response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
        
        try {
            $id = $this->input->post('id');
            $data = [
                'codigo' => $this->input->post('codigo'),
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'categoria_id' => $this->input->post('categoria_id'),
                'precio' => $this->input->post('precio'),
                'stock' => $this->input->post('stock'),
                'stock_minimo' => $this->input->post('stock_minimo'),
                'estado' => $this->input->post('estado')
            ];
            
            // Validaciones básicas
            if (empty($data['codigo']) || empty($data['nombre'])) {
                throw new Exception('Código y nombre son obligatorios');
            }
            
            if ($id) {
                // Actualizar
                $result = $this->ProductosModel->updateProducto($id, $data);
                $response['message'] = 'Producto actualizado correctamente';
            } else {
                // Crear
                $data['fecha_creacion'] = date('Y-m-d H:i:s');
                $result = $this->ProductosModel->createProducto($data);
                $response['message'] = 'Producto creado correctamente';
            }
            
            if ($result) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error al guardar el producto';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Eliminar producto
     */
    public function delete() {
        $response = ['success' => false, 'message' => 'Error al eliminar'];
        
        try {
            $id = $this->input->post('id');
            
            if (!$id) {
                throw new Exception('ID de producto no válido');
            }
            
            $result = $this->ProductosModel->deleteProducto($id);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado correctamente';
            } else {
                $response['message'] = 'Error al eliminar el producto';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Obtener producto por ID (AJAX)
     */
    public function getProducto($id) {
        $producto = $this->ProductosModel->getProductoById($id);
        
        if ($producto) {
            echo json_encode(['success' => true, 'data' => $producto]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }
    }

    /**
     * Actualizar stock de producto
     */
    public function updateStock() {
        $response = ['success' => false, 'message' => 'Error al actualizar stock'];
        
        try {
            $id = $this->input->post('id');
            $stock = $this->input->post('stock');
            $tipo = $this->input->post('tipo'); // 'entrada' o 'salida'
            
            if (!$id || !is_numeric($stock)) {
                throw new Exception('Datos inválidos');
            }
            
            $result = $this->ProductosModel->updateStock($id, $stock, $tipo);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Stock actualizado correctamente';
            } else {
                $response['message'] = 'Error al actualizar el stock';
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    }

    /**
     * Exportar productos a Excel
     */
    public function exportar() {
        $productos = $this->ProductosModel->getAllProductos();
        
        // Aquí implementarías la lógica de exportación
        // Por ejemplo, usando PhpSpreadsheet o similar
        
        $response = ['success' => true, 'message' => 'Exportación completada'];
        echo json_encode($response);
    }

    /**
     * Obtener productos por campaña y tipo (usando tablas de consolidado)
     */
    public function getProductosByCampana() {
        $idContenedor = $this->input->post('idContenedor');
        $tipoProducto = $this->input->post('tipoProducto');
        $this->load->model('BaseDatos/ProductosModel');
        $productos = $this->ProductosModel->getProductosByCampana($idContenedor, $tipoProducto);
        echo json_encode($productos);
    }

    /**
     * Obtener campañas/cargas para el filtro (usando tabla carga_consolidada_contenedor)
     */
    public function getCampanas() {
        $this->load->database();
        $query = $this->db->select('id, carga')->from('carga_consolidada_contenedor')->order_by('carga', 'desc')->get();
        $result = $query->result();
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    /**
     * Generar botones de acción para cada fila
     */
    private function generateActionButtons($row) {
        $actions = '<div class="btn-group" role="group">';
        $actions .= '<button type="button" class="btn btn-sm btn-info" onclick="viewProducto(' . $row->id . ')" title="Ver">';
        $actions .= '<i class="fas fa-eye"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-warning" onclick="editProducto(' . $row->id . ')" title="Editar">';
        $actions .= '<i class="fas fa-edit"></i>';
        $actions .= '</button>';
        $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteProducto(' . $row->id . ')" title="Eliminar">';
        $actions .= '<i class="fas fa-trash"></i>';
        $actions .= '</button>';
        $actions .= '</div>';
        
        return $actions;
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
            case 'agotado':
                return '<span class="badge badge-danger">Agotado</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($estado) . '</span>';
        }
    }
} 