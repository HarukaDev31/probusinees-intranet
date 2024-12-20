<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'traits/CommonTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'traits/FileTrait.php';

class EstadoContenedor extends CI_Controller
{
    use CommonTrait, FileTrait;
    private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio = 1;
    private $upload_path = '../assets/images/clientes/';
    private $file_path = '../assets/images/logos/';
    private $logo_cliente_path = '../assets/images/logos/';
    private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('AgenteCompra/EstadoContenedorModel');
        $this->load->model('HelperImportacionModel');
        $this->load->model('NotificacionModel');
        $this->load->model('MenuModel');

        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }
    }
    public function listar($sCorrelativoCotizacion = '', $ID_Pedido_Cabecera = '')
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        if (isset($this->session->userdata['usuario'])) {
            $this->load->view('header_v2', array("js_estado_contenedor" => true));
            $this->load->view('AgenteCompra/EstadoContenedorView', array(
                'sCorrelativoCotizacion' => $sCorrelativoCotizacion,
                'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
            ));
            $this->load->view('footer_v2', array("js_estado_contenedor" => true));
        }
    }
    public function index(){
        $arrData = $this->EstadoContenedorModel->_get_datatables_query();
        $data = array();
        foreach ($arrData->result() as $key => $value) {
            $inputTC='
            <div class="input-group">
                <input type="number" class="form-control tc-input" id="txtTC-'.$value->id.'"
                 name="txtTC"
                 data-id="'.$value->id.'"
                value="'.$value->tc.'"
                >
                <div class="input-group-append">
                    <button 
                    onclick="guardarTC('.$value->id.')"
                    class="btn btn-outline-secondary " type="button" id="btnTC">
                    <i class="fas fa-save"></i>
                    </button>
                </div>
            </div>';
            $inlandContainer='
            <span id="inlandContainer-'.$value->id.'">
                '.$value->inland.'
            </span>
            ';
            $fleteContainer='
            <span id="fleteContainer-'.$value->id.'">
                '.$value->flete.'
            </span>';
            $totalRMBContainer='
            <span id="totalRMBContainer-'.$value->id.'">
                '.($value->flete+$value->inland).'
            </span>';
            $totalUSDContainer='
            <span id="totalUSDContainer-'.$value->id.'">
                '.($value->flete+$value->inland)*$value->tc.'
            </span>';
            $blTelexSelect='
            <select class="form-control" id="blTelex-'.$value->id.'"
            onchange="guardarBlTelex('.$value->id.')"
            >
                <option value="1"' .($value->bl_telex==1?"selected":"").'>SI</option>
                <option value="0"' .($value->bl_telex==0?"selected":"").'>NO</option>
            </select>';
            $pagadoSelect='
            <select class="form-control" id="pagado-'.$value->id.'"
            onchange="guardarPagado('.$value->id.')"
            >
                <option value="1" '.($value->pagado==1?"selected":"").'>SI</option>
                <option value="0" '.($value->pagado==0?"selected":"").'>NO</option>
            </select>';
            //span estado= completado when bl_telex,pagado is 1 else pendiente
            $estado = '<span class="badge badge-'.($value->bl_telex==1 && $value->pagado==1?"success":"warning").'">'.($value->bl_telex==1 && $value->pagado==1?"Completado":"Pendiente").'</span>';
            $data[] = array(
                $value->servicio,
                $value->nu_order,
                $value->cod_bl,
                $value->client,
                $value->shipper,
                $inlandContainer,
                $inputTC,
                $fleteContainer,
                $totalRMBContainer,
                $totalUSDContainer,
                $value->naviera,
                $value->box_fee,
                $blTelexSelect,
                $pagadoSelect,
                $value->contenedor_tipo,
                $estado
            );
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);
    }
    public function guardarTC(){
        $id = $this->input->post('idBooking');
        $tc = $this->input->post('tc');
        $this->EstadoContenedorModel->guardarTC($id,$tc);
    }
    public function guardarBlTelex(){
        $id = $this->input->post('idBooking');
        $blTelex = $this->input->post('blTelex');
        $this->EstadoContenedorModel->guardarBlTelex($id,$blTelex);
    }
    public function guardarPagado(){
        $id = $this->input->post('idBooking');
        $pagado = $this->input->post('pagado');
        $this->EstadoContenedorModel->guardarPagado($id,$pagado);
    }
}