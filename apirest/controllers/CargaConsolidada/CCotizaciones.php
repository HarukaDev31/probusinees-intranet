<?php

use Mpdf\Http\Request;
require_once APPPATH . 'third_party/PHPExcel.php';

defined('BASEPATH') OR exit('No direct script access allowed');
class CCotizaciones extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('CargaConsolidada/CCotizacionesModel');
        if(!isset($this->session->userdata['usuario'])) redirect('');
    }
    public function listar(){
       if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
        $this->load->view('header_v2');
        $this->load->view('CargaConsolidada/CCotizacionesView');
        $this->load->view('footer_v2', array("js_ccotizaciones" => true));
    }
    public function ajax_list(){
        $sMethod=$this->input->post('sMethod');
        $arrData = $this->CCotizacionesModel->get_datatables();
        $data = array();

        foreach($arrData as $row){
            $select = '<select class="form-control" id="selectTipoCliente" name="selectTipoCliente" onchange="updateTipoCliente(this,'.$row->ID_Cotizacion.')">';
            //if row.ID_TipoCliente == $tipoCliente.value then selected = selected else selected = ''
            foreach(json_decode($row->Client_Types,true) as $tipoCliente){
                $option = '<option value="'.$tipoCliente['value'].'"';
                if($tipoCliente['value'] == $row->ID_Tipo_Cliente){
                    $option.=' selected';
                }
                $option.='>'.$tipoCliente['label'].'</option>';
                $select.=$option;
                
            }
            $select.='</select>';
            
            $rows = array();
            $rows[] = $row->CotizacionCode;
            $rows[] = ToDateBD($row->Fe_Creacion);
            $rows[] = $row->N_Cliente;
            $rows[] = $row->Empresa;
            $rows[] = 0;
            $rows[] = $select;
            $rows[] = '<button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarReporte(\'' . $row->ID_Cotizacion . '\')"><i class="fa fa-file-excel color_icon_excel fa-2x" aria-hidden="true"></i></button>';
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCotizacion(\'' . $row->ID_Cotizacion . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);  
    }public function getTypeClient(){
        return $this->CCotizacionesModel->getTypeClient();
    }
    
    public function ajax_edit_header($ID){
        echo json_encode($this->CCotizacionesModel->get_cotization_header($this->security->xss_clean($ID)));
    }
    public function ajax_edit_body($ID){
        echo json_encode($this->CCotizacionesModel->get_cotization_body($this->security->xss_clean($ID)));
    }
    public function ajax_edit_tributos($ID){
        echo json_encode($this->CCotizacionesModel->get_cotization_tributos($this->security->xss_clean($ID)));
    }
    public function guardarTributos(){
        $postData = file_get_contents('php://input');
        $tributos = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarTributos($tributos));
        
    }
    public function guardarCotizacion(){
        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarCotizacion($cotizacion));
    }
    public function descargarExcel(){
        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        $this->load->library('PHPExcel');
      

            // Create a new PHPExcel object
            $templatePath = 'assets/downloads/Boleta_Template.xlsx';
            $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
      
            $objPHPExcel= $this->CCotizacionesModel->fillExcelData($cotizacion,$objPHPExcel);
       

        // // Add some data to the sheet
       

        // Set the content type header to indicate that this is an Excel file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="example.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit(); // 
    }
    public function updateTipoCliente(){
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->updateTipoCliente($data));
    }
}
?>