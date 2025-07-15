<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'traits/FileTrait.php';

class OrdersModel extends CI_Model
{
    use FileTrait;
    public function __construct()
    {
        parent::__construct();
        $this->setAllowedExtensionsImagesOfficeFiles();
    }

    public function getOrders()
    {
        try {
            $this->db->select('*');
            $this->db->from('orders');
            $this->db->where('deleted_at IS NULL');
            $this->db->order_by('order_date', 'DESC');

            // Aplicar filtros si existen
            if ($this->input->post('fecha_inicio') && $this->input->post('fecha_inicio') != '') {
                $this->db->where('order_date >=', $this->input->post('fecha_inicio'));
            }

            if ($this->input->post('fecha_fin') && $this->input->post('fecha_fin') != '') {
                $this->db->where('order_date <=', $this->input->post('fecha_fin'));
            }

          
           

            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : getOrders() => ' . $e->getMessage());
            return [];
        }
    }

    public function getOrderById($orderId)
    {
        try {
            $this->db->select('*');
            $this->db->from('orders');
            $this->db->where('id', $orderId);
            $this->db->where('deleted_at IS NULL');

            $query = $this->db->get();
            return $query->row();
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : getOrderById() => ' . $e->getMessage());
            return null;
        }
    }

    public function getOrderItems($orderId)
    {
        try {
            $this->db->select('order_items.*, catalogo_producto.main_image_url as product_image, catalogo_producto.nombre as product_name, catalogo_producto.url_tienda as store_link, catalogo_producto.url_alibaba as alibaba_link, catalogo_producto.delivery_lead_times');
            $this->db->from('order_items');
            $this->db->join('catalogo_producto', 'catalogo_producto.id = order_items.product_id', 'left');
            $this->db->where('order_items.order_id', $orderId);
            $this->db->order_by('order_items.created_at', 'ASC');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : getOrderItems() => ' . $e->getMessage());
            return [];
        }
    }

    public function softDeleteOrder($orderId)
    {
        try {
            $this->db->where('id', $orderId);
            $this->db->update('orders', ['deleted_at' => date('Y-m-d H:i:s')]);

            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : softDeleteOrder() => ' . $e->getMessage());
            return false;
        }
    }

    public function getPaymentStatuses()
    {
        try {
            $this->db->select('DISTINCT(payment_status) as status');
            $this->db->from('orders');
            $this->db->where('deleted_at IS NULL');
            $this->db->where('payment_status IS NOT NULL');
            $this->db->where('payment_status !=', '');

            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : getPaymentStatuses() => ' . $e->getMessage());
            return [];
        }
    }

    public function getOrderStatuses()
    {
        try {
            $this->db->select('DISTINCT(status) as status');
            $this->db->from('orders');
            $this->db->where('deleted_at IS NULL');
            $this->db->where('status IS NOT NULL');
            $this->db->where('status !=', '');

            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : getOrderStatuses() => ' . $e->getMessage());
            return [];
        }
    }

    public function getCatalogoProducto($productId)
    {
        $this->db->select('*');
        $this->db->from('catalogo_producto');
        $this->db->where('id', $productId);
        $query = $this->db->get();
        return $query->row();
    }

    public function subirCotizacion($orderId, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFilesVideos();
            $uploadPath = 'assets/cotizaciones/';
            $fileUrl = $this->uploadSingleFile($file, $uploadPath);
            log_message('error', 'OrdersModel : subirCotizacion() => ' . $fileUrl);
            if ($fileUrl != null) {
                $this->db->where('id', $orderId);
                $this->db->update('orders', ['cotizacion_url' => $fileUrl]);
                log_message('error', 'OrdersModel : subirCotizacion() => ' . $fileUrl);
                return $fileUrl;
            } else {
                return null; // string con error
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : subirCotizacion() => ' . $e->getMessage());
            return null;
        }
    }

    public function subirOrden($orderId, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFilesVideos();
            $uploadPath = 'assets/ordenes/';
            $fileUrl = $this->uploadSingleFile($file, $uploadPath);
            log_message('error', 'OrdersModel : subirOrden() => ' . $fileUrl);
            if ($fileUrl != null) {
                $this->db->where('id', $orderId);
                $this->db->update('orders', ['orden_url' => $fileUrl]);
                log_message('error', 'OrdersModel : subirOrden() => ' . $fileUrl);
                return $fileUrl;
            } else {
                return null;
            }
        } catch (Exception $e) {
            log_message('error', 'OrdersModel : subirOrden() => ' . $e->getMessage());
            return null;
        }
    }
}
