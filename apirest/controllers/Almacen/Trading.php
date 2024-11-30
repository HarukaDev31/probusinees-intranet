<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Trading extends CI_Controller
{
    private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio = 1;
    private $almacenPrivilegio = 6;
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

        $privilegio = $this->user->Nu_Tipo_Privilegio_Acceso;
        $disabled = ($privilegio != $this->almacenPrivilegio) ? 'disabled' : '';

        foreach ($arrData as $row) {
            $span_estado = "";
            if ($row->almacen_estado == "PENDIENTE") {
                $span_estado = "<span class='badge badge-warning'>" . $row->almacen_estado . "</span>";
            } else if ($row->almacen_estado == "RECIBIDO") {
                $span_estado = "<span class='badge badge-info'>" . $row->almacen_estado . "</span>";
            } else if ($row->almacen_estado == "COMPLETADO") {
                $span_estado = "<span class='badge badge-success'>" . $row->almacen_estado . "</span>";
            }
            $data[] = array(
                "<img src='" . $row->image_url . "' class='img-fluid' alt='imagen' style='width: 100px; height: 100px;'>",
                $row->name,
                $row->quantity,
                "<input type='number'
                data-id='" . $row->id . "'
                class='form-control box_value'
                value='" . $row->total_box_almacen . "'
                id='total_cbm_" . $row->id . "'
                $disabled>",
                "<input type='number'
                data-id='" . $row->id . "'
                class='form-control cbm_value'
                value='" . $row->total_cbm_almacen . "'
                id='total_cbm_" . $row->id . "'
                $disabled>",
              "
                <input type='number'
                    data-id='" . $row->id . "'
                    class='form-control kg_value'
                    value='" . $row->total_kg_almacen . "'
                    id='total_kg_" . $row->id . "'
                    $disabled>",
                "<button class='btn btn-xs btn-link' onclick='getFotos(" . $row->order_excel_id ." ,".$row->id.
                 ")'.
                alt='Fotos' title='Fotos' href='javascript:void(0)'><i class='fas fa-images fa-2x' aria-hidden='true'></i></button>",
                $span_estado,
                "<textarea 
                data-id='".$row->id ."'
                class='form-control almacen_notas' id='notas_" . $row->order_excel_id . "'>" . $row->nota_almacen . "</textarea>",
            );
        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    public function getInspeccionFiles(){
        $idExcel = $this->input->post("idExcel");
        $idDetalle = $this->input->post("idDetalle");
        $arrData = $this->TradingModel->getInspeccionFiles($idDetalle);

        $output = array(
            "data" => $arrData
        );
        echo json_encode($output);
    }
    public function uploadInspeccionFiles(){
   
        $file = $_FILES['file'];
        $idExcel = $this->input->post("idExcel");
        $idDetalle=$this->input->post("idDetalle");
        $idOrder=$this->input->post("idOrder");
        $data=$this->TradingModel->uploadInspeccionFiles($idExcel,$idDetalle,$idOrder,$file);
        echo json_encode($data);
    }
    public function deleteInspeccionFiles(){
        $idFile = $this->input->post("idFile");
        $idDetalle=$this->input->post("idDetalle");
        $data=$this->TradingModel->deleteInspeccionFiles($idFile,$idDetalle);
        echo json_encode($data);
    }
    public function saveInspection(){
        $data = $this->input->post('data');
        $idOrder=$this->input->post('idOrder');
        $response = $this->TradingModel->saveInspection($data,$idOrder);
        echo json_encode($response);
    }
}