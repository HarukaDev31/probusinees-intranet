<?php

require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

defined('BASEPATH') or exit('No direct script access allowed');
class CCotizaciones extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('CargaConsolidada/CCotizacionesModel');
        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }

    }
    public function listar()
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        $this->load->view('header_v2');
        $this->load->view('CargaConsolidada/CCotizacionesView');
        $this->load->view('footer_v2', array("js_ccotizaciones" => true));
    }
    public function ajax_list()
    {
        $sMethod = $this->input->post('sMethod');
        $arrData = $this->CCotizacionesModel->get_datatables();
        $data = array();

        foreach ($arrData as $row) {
            $select = '<select class="form-control" id="selectTipoCliente" name="selectTipoCliente" onchange="updateTipoCliente(this,' . $row->ID_Cotizacion . ')">';
            //if row.ID_TipoCliente == $tipoCliente.value then selected = selected else selected = ''
            foreach (json_decode($row->Client_Types, true) as $tipoCliente) {
                $option = '<option value="' . $tipoCliente['value'] . '"';
                if ($tipoCliente['value'] == $row->ID_Tipo_Cliente) {
                    $option .= ' selected';
                }
                $option .= '>' . $tipoCliente['label'] . '</option>';
                $select .= $option;

            }
            $select .= '</select>';

            $rows = array();
            $rows[] = $row->CotizacionCode;
            $rows[] = ToDateBD($row->Fe_Creacion);
            $rows[] = $row->N_Cliente;
            $rows[] = $row->Telefono;
            $rows[] = $row->Empresa;
            $rows[] = $select;
            $rows[] = '<div>
            <button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarReporte(\'' . $row->ID_Cotizacion . '\')"><i class="fa fa-file-excel color_icon_excel fa-2x" aria-hidden="true"></i></button>
            </div>';
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCotizacion(\'' . $row->ID_Cotizacion . '\')"><i class="far fa-edit fa-2x" aria-hidden="true" id="ver-cotizacion(' . $row->ID_Cotizacion . ')"></i></button>';
            //select with options pendiente,cotizado,confirmado
            $rows[] = '<select class="form-control" id="selectEstado" name="selectEstado" onchange="updateEstadoCotizacion(this,' . $row->ID_Cotizacion . ')">
            <option value="1" ' . ($row->Cotizacion_Status_ID == 1 ? 'selected' : '') . '>Pendiente</option>
            <option value="2" ' . ($row->Cotizacion_Status_ID == 2 ? 'selected' : '') . '>Cotizado</option>
            <option value="3" ' . ($row->Cotizacion_Status_ID == 3 ? 'selected' : '') . '>Confirmado</option>
            </select>';
            $rows[]=$row->ID_Tipo_Cliente;
            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);
    }public function getTypeClient()
    {
        return $this->CCotizacionesModel->getTypeClient();
    }

    public function ajax_edit_header($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_header($this->security->xss_clean($ID)));
    }
    public function ajax_edit_body($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_body($this->security->xss_clean($ID)));
    }
    public function ajax_edit_tributos($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_tributos($this->security->xss_clean($ID)));
    }
    public function guardarTributos()
    {
        $postData = file_get_contents('php://input');
        $tributos = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarTributos($tributos));

    }
    public function guardarCotizacion()
    {
        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarCotizacion($cotizacion));
    }
    public function descargarExcel()
    {

        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        $this->load->library('PHPExcel');

        // Create a new PHPExcel object
        $templatePath = 'assets/downloads/Boleta_Template.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);

        $objPHPExcel = $this->CCotizacionesModel->fillExcelData($cotizacion, $objPHPExcel);

        // // Add some data to the sheet

        // Set the content type header to indicate that this is an Excel file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="example.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit(); //
    }public function descargarBoleta()
    {

        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);

// Cargar el archivo de plantilla Excel
        $templatePath = 'assets/downloads/Boleta_Template.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $objPHPExcel = $this->CCotizacionesModel->fillExcelData($cotizacion, $objPHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="example.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
   
        
    }

    public function getTipoCliente()
    {
        echo json_encode($this->CCotizacionesModel->getTipoCliente());
    }

    public function updateTipoCliente()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->updateTipoCliente($data));
    }
    public function updateEstadoCotizacion()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->updateEstadoCotizacion($data));
    }
}
