<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LinkReferidoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PanelAcceso/LinkReferidoModel');
		$this->load->model('HelperModel');
	}
	
	public function EncriptarCodigo($Numero,$Semilla){
		$Numero = $Numero + $Semilla;
	 	return base_convert($Numero, 10, 36);
	}

	public function DesencriptarCodigo($Numero,$Semilla){
	 	$tmp = base_convert($Numero, 36, 10);
	 	return $tmp - $Semilla;
	}

	public function listarUsuarios($sUsuario=''){
		if(isset($this->session->userdata['usuario'])) {

			$iSemilla = '2023';
			$iIdEmpresaUsuario = $this->empresa->ID_Empresa;
			$iCodigoEncriptado = $this->EncriptarCodigo($iIdEmpresaUsuario, $iSemilla);

			$sLinkReferido = 'ecxpresslae.com/registro/?r=ECXLAE_' . $iCodigoEncriptado;

			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('PanelAcceso/LinkReferidoView', array('link_referido' => $sLinkReferido));
			$this->load->view('footer', array("js_link_referido" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->LinkReferidoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			
            $rows[] = $row->No_Empresa;
            $rows[] = $row->No_Empresa_Referida;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $data[] = $rows;
        }
        $output = array(
	        "draw" => $this->input->post('draw'),
	        "recordsTotal" => $this->LinkReferidoModel->count_all(),
	        "recordsFiltered" => $this->LinkReferidoModel->count_filtered(),
	        "data" => $data,
        );
        echo json_encode($output);
    }
}
