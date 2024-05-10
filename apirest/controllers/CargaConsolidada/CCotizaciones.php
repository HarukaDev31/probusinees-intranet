<?php
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
            $rows = array();
            $rows[] = $row->ID_Cotizacion;
            $rows[] = ToDateBD($row->Fe_Creacion);
            $rows[] = $row->N_Cliente;
            $rows[] = $row->Empresa;
            $rows[] = $row->Cotizacion;
            $rows[] = $row->ID_Tipo_Cliente;
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCotizacion(\'' . $row->ID_Cotizacion . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);  
    }
}
?>