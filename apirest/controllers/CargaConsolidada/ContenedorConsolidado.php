<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContenedorConsolidado extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		//$this->load->database('LAE_SYSTEMS');
		$this->load->model('CargaConsolidada/ContenedorConsolidadoModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar($ID_Carga_Consolidada=0){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_contenedor_consolidadado" => true));
			$this->load->view('CargaConsolidada/ContenedorConsolidadoView', array(
				'arrResponseConsolidado' => $arrResponseConsolidado,
				'ID_Carga_Consolidada' => $ID_Carga_Consolidada,
			));
			$this->load->view('footer_v2', array("js_contenedor_consolidadado" => true));
		}
	}
    public function index(){
        $arrData = $this->ContenedorConsolidadoModel->index();
        $data= array();
        foreach ($arrData as $row) {
            
        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
}