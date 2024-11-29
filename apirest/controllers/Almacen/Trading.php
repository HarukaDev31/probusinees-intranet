<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Trading extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('Almacen/TradingModel');
        $this->load->model('HelperImportacionModel');
        $this->load->model('NotificacionModel');
        $this->load->model('MenuModel');

        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }
    }
    public function listar()
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        if (isset($this->session->userdata['usuario'])) {
            $this->load->view('header_v2', array("js_almacen" => true));
            $this->load->view('Almacen/TradingView');
            $this->load->view('footer_v2', array("js_almacen" => true));
        }
    }
    public function getAlmacen(){
        $arrData = $this->TradingModel->getAlmacen();
        $data= array();
        foreach ($arrData as $row) {
            $data[] = array(
                $row->No_Pais,
                $row->Fe_Emision_OC_Aprobada,
                $rows[] = "<div class='d-flex flex-column'><span class='mr-2'>" . $row->No_Entidad . "</span><span>" . $row->Nu_Documento_Identidad . "</span></div>",
                $rows[] = "<div class='d-flex flex-column'><span class='mr-2'>" . $row->No_Contacto . "</span><span>" . $row->Nu_Celular_Contacto . "</span></div>",
                $row->cotizacionCode,
                $rows[] = "<button class='btn btn-xs btn-link' onclick='getAlmacenData(" . $row->ID_Pedido_Cabecera . ")' alt='Editar' title='Editar' href='javascript:void(0)'><i class='fas fa-edit fa-2x' aria-hidden='true'></i></button>",
                $rows[] = '<select class="form-control" id="status_' . $row->estado_almacen . '" onchange="changeStatusAlmacen(this.value,' . $row->ID_Pedido_Cabecera . ')">
                <option value="PENDIENTE" ' . ($row->estado_almacen == "PENDIENTE" ? 'selected' : '') . '>PENDIENTE</option>
                <option value="RECIBIENDO" ' . ($row->estado_almacen == "RECIBIENDO" ? 'selected' : '') . '>RECIBIENDO</option>
                <option value="COMPLETADO" ' . ($row->estado_almacen == "COMPLETADO" ? 'selected' : '') . '>COMPLETADO</option>
            </select>',
            );
        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    public function getInspeccion(){
        $idOrder = $this->input->post("idOrder");
        $arrData = $this->TradingModel->getInspeccion($idOrder);
        $data = array();
        foreach ($arrData as $row) {
            $data[] = array(
                "<img src='" . $row->image_url . "' class='img-fluid' alt='imagen' style='width: 100px; height: 100px;'>",
                $row->name,
                $row->quantity,
                $row->total_boxes,
                $row->total_cbm,
                $row->kg_box,
                "<button class='btn btn-xs btn-link' onclick='getFotos(" . $row->order_excel_id . ")' alt='Fotos' title='Fotos' href='javascript:void(0)'><i class='fas fa-images fa-2x' aria-hidden='true'></i></button>",
               $rows[]='<select class="form-control" id="status_' . $row->estado_almacen . '" onchange="changeStatusAlmacen(this.value,' . $row->ID_Pedido_Cabecera . ')">
                <option value="PENDIENTE" ' . ($row->estado_almacen == "PENDIENTE" ? 'selected' : '') . '>PENDIENTE</option>
                <option value="RECIBIENDO" ' . ($row->estado_almacen == "RECIBIENDO" ? 'selected' : '') . '>RECIBIENDO</option>
                <option value="COMPLETADO" ' . ($row->estado_almacen == "COMPLETADO" ? 'selected' : '') . '>COMPLETADO</option>
                    </select>',
                $row->notas,
            );
        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    public function getInspeccionFiles(){
        $idExcel = $this->input->post("idExcel");
        $arrData = $this->TradingModel->getInspeccionFiles($idExcel);
        $data = array();
        foreach ($arrData as $row) {
            $data[] = array(
                $row->name,
                $row->size,
                $row->created_at,
                "<a href='" . base_url() . "uploads/" . $row->path . "' target='_blank' class='btn btn-xs btn-link' alt='Descargar' title='Descargar'><i class='fas fa-download fa-2x' aria-hidden='true'></i></a>",
            );
        }
        $output = array(
            "data" => $arrData
        );
        echo json_encode($output);
    }
    public function uploadInspeccionFiles(){
   
        $file = $_FILES['file'];
        $idExcel = $this->input->post("idExcel");
        $data=$this->TradingModel->uploadInspeccionFiles($idExcel,$file);
        echo json_encode($data);
    }
}