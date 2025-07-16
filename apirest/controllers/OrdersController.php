<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'traits/FileTrait.php';

class OrdersController extends CI_Controller
{
    use FileTrait;

    function __construct()
    {
        try {
            parent::__construct();
            $this->load->library('session');
            $this->load->model('Administracion/OrdersModel');
            $this->load->library('upload');
            $this->load->helper('url');
            
            // Configurar FileTrait para archivos de oficina
            $this->setAllowedExtensionsImagesOfficeFiles();
            
            if (!isset($this->session->userdata['usuario'])) {
                redirect('');
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : __construct() => ' . $e->getMessage());
        }
    }

    public function listar()
    {
        try {
            if (isset($this->session->userdata['usuario'])) {
                $this->load->view('header_v2', ["js_orders" => true]);
                $this->load->view('OrdersView');
                $this->load->view('footer_v2', ["js_orders" => true]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : listar() => ' . $e->getMessage());
        }
    }

    public function listarConfirmados()
    {
        try {
            if (isset($this->session->userdata['usuario'])) {
                $this->load->view('header_v2', ["js_orders" => true]);
                $this->load->view('OrdersView');
                $this->load->view('footer_v2', ["js_orders" => true]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : listarConfirmados() => ' . $e->getMessage());
        }
    }

    public function getOrders()
    {
        try {
            $arrData = $this->OrdersModel->getOrders();
            $data = [];
            $index = 1;
            
            foreach ($arrData as $value) {
                // Si se solicita solo confirmados, filtrar
                $confirmados = $this->input->post('confirmados');
                log_message('error', 'OrdersController : getOrders() => ' . $value->status_cotizacion);

                if (!empty($confirmados)) {
                    if ($value->status_cotizacion !== 'Confirmado') continue;
                    
                    // Filtrar por estado_confirmado si se especifica
                    $estadoConfirmado = $this->input->post('estado_confirmado');
                    if ($estadoConfirmado && $estadoConfirmado !== '0' && $value->estado_confirmado !== $estadoConfirmado) {
                        continue;
                    }
                } else {
                    log_message('error', 'OrdersController : getOrders() => ' . $value->status_cotizacion);
                    // Si NO es la vista de confirmados, ocultar los confirmados
                    if ($value->status_cotizacion === 'Confirmado') continue;
                    
                    // Filtrar por estado si se especifica
                    $estado = trim($this->input->post('estado'));
                    if ($estado && $estado !== '0' && $value->status_cotizacion !== $estado) {
                        continue;
                    }
                }
                
                
                
                $subdata = [];
                $subdata[] = $index;
                $subdata[] = $value->order_number;
                $subdata[] = date('d/m/Y', strtotime($value->order_date));
                $subdata[] = $value->customer_full_name;
                $subdata[] = $value->customer_dni;
                $subdata[] = $value->customer_phone;
                $subdata[] = $value->customer_email;
                
             
                
                // Campo de estado (select)
                if ($this->input->post('confirmados')) {
                    $estadoOptions = ['Pendiente', 'Negociacion', 'Produccion', 'Embarcado', 'Entregado'];
                    $select = '<select class="form-control form-control-sm" onchange="updateEstadoConfirmadoJS(' . $value->id . ', this.value)">';
                    foreach ($estadoOptions as $opt) {
                        $selected = ($value->estado_confirmado == $opt) ? 'selected' : '';
                        $select .= '<option value="' . $opt . '" ' . $selected . '>' . $opt . '</option>';
                    }
                    $select .= '</select>';
                    $subdata[] = $select;
                } else {
                    // Campo status_cotizacion (select)
                    $statusOptions = ['Pendiente', 'Cotizado', 'Observado', 'Confirmado', 'Rechazado'];
                    $select = '<select class="form-control form-control-sm" onchange="updateStatusCotizacionJS(' . $value->id . ', this.value)">';
                    foreach ($statusOptions as $opt) {
                        $selected = ($value->status_cotizacion == $opt) ? 'selected' : '';
                        $select .= '<option value="' . $opt . '" ' . $selected . '>' . $opt . '</option>';
                    }
                    $select .= '</select>';
                    $subdata[] = $select;
                }
                // Botón de cotización (solo si existe archivo)
                $cotizacionBtn = '';
                if (!empty($value->cotizacion_url)) {
                    $cotizacionBtn = '<button class="btn btn-success btn-sm" onclick="window.open(\'' . $value->cotizacion_url . '\', \'_blank\')"><i class="fas fa-download"></i> Descargar</button>';
                    //if input confirmados is false, show delete button
                    if (!$this->input->post('confirmados')) {
                        $cotizacionBtn .= ' <button class="btn btn-danger btn-sm" onclick="deleteCotizacion(' . $value->id . ')"><i class="fas fa-trash"></i> Eliminar</button>';
                    }
                } else {
                }
                $subdata[] = $cotizacionBtn;
                
                // Acciones
                $acciones = '<div class="d-flex gap-1">';
                if ($this->input->post('confirmados')) {
                    $acciones .= '<a href="' . base_url('OrdersController/detalleConfirmado/' . $value->id) . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>';
                } else {
                    $acciones .= '<a href="' . base_url('OrdersController/detalle/' . $value->id) . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>';
                }
                $acciones .= '<button class="btn btn-danger btn-sm" onclick="deleteOrder(' . $value->id . ')">
                    <i class="fas fa-trash"></i>
                </button>';
                $acciones .= '</div>';
                $subdata[] = $acciones;
                
                $data[] = $subdata;
                $index++;
            }
            
            $output = array("data" => $data);
            echo json_encode($output);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : getOrders() => ' . $e->getMessage());
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            $orderData = $this->OrdersModel->getOrderById($orderId);
            $orderItems = $this->OrdersModel->getOrderItems($orderId);
            
            echo json_encode([
                'status' => 'success',
                'order' => $orderData,
                'items' => $orderItems
            ]);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : getOrderDetails() => ' . $e->getMessage());
        }
    }

    public function uploadCotizacion()
    {
        try {
            $orderId = $this->input->post('orderId');
            if (!isset($_FILES['cotizacion_file']) || $_FILES['cotizacion_file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se ha seleccionado ningún archivo'
                ]);
                return;
            }
            $file = $_FILES['cotizacion_file'];
            $fileUrl = $this->OrdersModel->subirCotizacion($orderId, $file);
            if ($fileUrl) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cotización subida correctamente',
                    'file_url' => $fileUrl
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al subir el archivo: ' . $fileUrl
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : uploadCotizacion() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    public function uploadOrden()
    {
        try {
            $orderId = $this->input->post('orderId');
            if (!isset($_FILES['orden_file']) || $_FILES['orden_file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se ha seleccionado ningún archivo'
                ]);
                return;
            }
            $file = $_FILES['orden_file'];
            $fileUrl = $this->OrdersModel->subirOrden($orderId, $file);
            if ($fileUrl) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Orden subida correctamente',
                    'file_url' => $fileUrl
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al subir el archivo: ' . $fileUrl
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : uploadOrden() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    public function detalle($orderId)
    {
        try {
            if (isset($this->session->userdata['usuario'])) {
                $orderData = $this->OrdersModel->getOrderById($orderId);
                
                if (!$orderData) {
                    redirect('orders/listar');
                }
                
                $data = [
                    'order' => $orderData
                ];
                
                $this->load->view('header_v2', ["js_order_detail" => true]);
                $this->load->view('OrderDetailView', $data);
                $this->load->view('footer_v2', ["js_order_detail" => true]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : detalle() => ' . $e->getMessage());
        }
    }

    public function detalleConfirmado($orderId)
    {
        try {
            if (isset($this->session->userdata['usuario'])) {
                $orderData = $this->OrdersModel->getOrderById($orderId);
                if (!$orderData) {
                    redirect('orders/listarConfirmados');
                }
                $data = [
                    'order' => $orderData
                ];
                $this->load->view('header_v2', ["js_order_confirmado_detail" => true]);
                $this->load->view('OrderConfirmadoDetailView', $data);
                $this->load->view('footer_v2', ["js_order_confirmado_detail" => true]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : detalleConfirmado() => ' . $e->getMessage());
        }
    }

    public function getOrderInfo($orderId)
    {
        try {
            $orderData = $this->OrdersModel->getOrderById($orderId);
            
            echo json_encode([
                'status' => 'success',
                'order' => $orderData
            ]);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : getOrderInfo() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al obtener información de la orden'
            ]);
        }
    }

    public function getOrderProducts($orderId)
    {
        try {
            $orderItems = $this->OrdersModel->getOrderItems($orderId);
            
            // Completar datos faltantes desde catalogo_producto
            foreach ($orderItems as $item) {
                if (empty($item->product_image) || empty($item->product_name)) {
                    $catalogoProducto = $this->OrdersModel->getCatalogoProducto($item->product_id);
                    if ($catalogoProducto) {
                        if (empty($item->product_image)) {
                            $item->product_image = $catalogoProducto->imagen;
                        }
                        if (empty($item->product_name)) {
                            $item->product_name = $catalogoProducto->nombre;
                        }
                        if (empty($item->delivery_lead_times)) {
                            $item->delivery_lead_times = $catalogoProducto->delivery_lead_times;
                        }
                    }
                }
            }
            
            $data = [];
            foreach ($orderItems as $item) {
                $data[] = [
                    'product_id' => $item->product_id,
                    'product_image' => $item->product_image,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'store_link' => $item->store_link,
                    'alibaba_link' => $item->alibaba_link,
                    'delivery_lead_times' => $item->delivery_lead_times
                ];
            }
            
            echo json_encode([
                'data' => $data
            ]);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : getOrderProducts() => ' . $e->getMessage());
            echo json_encode([
                'data' => []
            ]);
        }
    }

    public function cotizacion($orderId)
    {
        try {
            if (isset($this->session->userdata['usuario'])) {
                $orderData = $this->OrdersModel->getOrderById($orderId);
                $orderItems = $this->OrdersModel->getOrderItems($orderId);
                
                // Completar datos faltantes desde catalogo_producto
                foreach ($orderItems as $item) {
                    if (empty($item->product_image) || empty($item->product_name)) {
                        $catalogoProducto = $this->OrdersModel->getCatalogoProducto($item->product_id);
                        if ($catalogoProducto) {
                            if (empty($item->product_image)) {
                                $item->product_image = $catalogoProducto->imagen;
                            }
                            if (empty($item->product_name)) {
                                $item->product_name = $catalogoProducto->nombre;
                            }
                            if (empty($item->delivery_lead_times)) {
                                $item->delivery_lead_times = $catalogoProducto->delivery_lead_times;
                            }
                        }
                    }
                }
                
                $data = [
                    'order' => $orderData,
                    'items' => $orderItems
                ];
                
                $this->load->view('header_simple', ["title" => "Cotización - " . $orderData->order_number]);
                $this->load->view('Administracion/CotizacionView', $data);
                $this->load->view('footer_simple');
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : cotizacion() => ' . $e->getMessage());
        }
    }

    public function downloadOrderExcel($orderId)
    {
        try {
            $orderData = $this->OrdersModel->getOrderById($orderId);
            $orderItems = $this->OrdersModel->getOrderItems($orderId);
            
            // Crear archivo Excel
            $this->load->library('excel');
            
            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('Cotización');
            
            // Encabezados
            $this->excel->getActiveSheet()->setCellValue('A1', 'Imagen');
            $this->excel->getActiveSheet()->setCellValue('B1', 'Nombre');
            $this->excel->getActiveSheet()->setCellValue('C1', 'Cantidad');
            $this->excel->getActiveSheet()->setCellValue('D1', 'Precio Unitario');
            $this->excel->getActiveSheet()->setCellValue('E1', 'Precio Total');
            
            $row = 2;
            foreach ($orderItems as $item) {
                $this->excel->getActiveSheet()->setCellValue('A' . $row, $item->product_image);
                $this->excel->getActiveSheet()->setCellValue('B' . $row, $item->product_name);
                $this->excel->getActiveSheet()->setCellValue('C' . $row, $item->quantity);
                $this->excel->getActiveSheet()->setCellValue('D' . $row, $item->unit_price);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, $item->total_price);
                $row++;
            }
            
            $filename = 'Cotizacion_' . $orderData->order_number . '_' . date('Y-m-d') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            $objWriter->save('php://output');
            
        } catch (Exception $e) {
            log_message('error', 'OrdersController : downloadOrderExcel() => ' . $e->getMessage());
        }
    }

    public function deleteOrder()
    {
        try {
            $orderId = $this->input->post('orderId');
            $result = $this->OrdersModel->softDeleteOrder($orderId);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Orden eliminada correctamente'
            ]);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : deleteOrder() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al eliminar la orden'
            ]);
        }
    }

    public function deleteCotizacion()
    {
        try {
            $orderId = $this->input->post('orderId');
            $order = $this->OrdersModel->getOrderById($orderId);
            if ($order && !empty($order->cotizacion_url)) {
                $filePath = str_replace(base_url(), '', $order->cotizacion_url);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $this->db->where('id', $orderId);
                $this->db->update('orders', ['cotizacion_url' => null]);
                echo json_encode(['status' => 'success', 'message' => 'Cotización eliminada correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No existe cotización para eliminar']);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : deleteCotizacion() => ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error interno al eliminar cotización']);
        }
    }

    public function getOrderCustomerName($orderId)
    {
        try {
            $orderData = $this->OrdersModel->getOrderById($orderId);
            $customerName = $orderData ? $orderData->customer_full_name : '';
            echo json_encode([
                'status' => 'success',
                'customer_name' => $customerName
            ]);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : getOrderCustomerName() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'customer_name' => ''
            ]);
        }
    }

    public function updateStatusCotizacion()
    {
        try {
            $orderId = $this->input->post('orderId');
            $status = $this->input->post('status');
            $allowed = ['Pendiente', 'Cotizado', 'Observado', 'Confirmado', 'Rechazado'];
            if (!in_array($status, $allowed)) {
                echo json_encode(['status' => 'error', 'message' => 'Estado no permitido']);
                return;
            }
            $this->db->where('id', $orderId);
            $this->db->update('orders', ['status_cotizacion' => $status]);
            echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : updateStatusCotizacion() => ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error interno al actualizar estado']);
        }
    }

    public function updateEstadoConfirmado()
    {
        try {
            $orderId = $this->input->post('orderId');
            $estado = $this->input->post('estado');
            $allowed = ['Pendiente', 'Negociacion', 'Produccion', 'Embarcado', 'Entregado'];
            if (!in_array($estado, $allowed)) {
                echo json_encode(['status' => 'error', 'message' => 'Estado no permitido']);
                return;
            }
            $this->db->where('id', $orderId);
            $this->db->update('orders', ['estado_confirmado' => $estado]);
            echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
        } catch (Exception $e) {
            log_message('error', 'OrdersController : updateEstadoConfirmado() => ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error interno al actualizar estado']);
        }
    }

    public function deleteOrden()
    {
        try {
            $orderId = $this->input->post('orderId');
            $order = $this->OrdersModel->getOrderById($orderId);
            
            if ($order && !empty($order->orden_url)) {
                // Eliminar archivo físico
                $filePath = str_replace(base_url(), '', $order->orden_url);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                
                // Limpiar URL en base de datos
                $this->db->where('id', $orderId);
                $this->db->update('orders', ['orden_url' => null]);
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Orden eliminada correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'No existe orden para eliminar'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersController : deleteOrden() => ' . $e->getMessage());
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error interno al eliminar orden'
            ]);
        }
    }

    private function getEstadoClass($status)
    {
        switch ($status) {
            case 'PENDIENTE':
                return 'bg-secondary';
            case 'PAGADO':
                return 'bg-success';
            case 'ADELANTO':
                return 'bg-warning';
            case 'OBSERVADO':
                return 'bg-danger';
            case 'CONFIRMADO':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }
} 