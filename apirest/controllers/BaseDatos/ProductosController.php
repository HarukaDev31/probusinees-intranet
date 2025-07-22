<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductosController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('BaseDatos/ProductosModel');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('input');
        
        // Verificar sesión de usuario (opcional)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
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
        $start = $this->input->post('start') ?: 0;
        $length = $this->input->post('length') ?: 10;
        $search = $this->input->post('search')['value'] ?? '';
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'asc';
        
        // Filtros adicionales
        $categoria = $this->input->post('categoria') ?? '';
        $estado = $this->input->post('estado') ?? '';
        
        $filters = [
            'categoria' => $categoria,
            'estado' => $estado,
            'search' => $search
        ];
        
        $result = $this->ProductosModel->getProductos($start, $length, $filters, $order_column, $order_dir);
        
        $data = [];
        foreach ($result['data'] as $row) {
            $actions = $this->generateActionButtons($row);
            
            $data[] = [
                'id' => $row->id,
                'codigo' => $row->codigo,
                'nombre' => $row->nombre,
                'categoria' => $row->categoria,
                'precio' => number_format($row->precio, 2),
                'stock' => $row->stock,
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