<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class InicioDropshippingController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ConfiguracionModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
        $dToday = dateNow('fecha');
		$dLastDay = date('t',strtotime($dToday));
		
		$arrMoneda = $this->HelperModel->getMonedas();
		
		$arrGrafico = array(
			"dInicial" => $dToday,
			"dFinal" => $dToday,
			"iIDMoneda" => $arrMoneda[0]->ID_Moneda,
			"iImpuesto" => 0,
		);
		$arrRowGrafico = $this->ConfiguracionModel->reporteGraficoTiendaVirtual($arrGrafico);
		$arrTourTiendaVirtual = $this->ConfiguracionModel->obtenerTourDropshippingTiendaVirtual();
		$arrUrlDropshipping = $this->ConfiguracionModel->obtenerUrlDropshippingTiendaVirtual();

		$this->load->view('header', array("arrUrlDropshipping" => $arrUrlDropshipping));
		$this->load->view('Dropshipping/InicioView',
			array(
				"sToday" => dateNow('dia') . ' ' . getNameMonth(date('m')) . ', ' . date('Y'),
				"reporte" => $arrRowGrafico,
				"dInicial" => $dToday,
				"dFinal" => $dToday,
				"iImpuesto" => 0,
				"arrTourTiendaVirtual" => $arrTourTiendaVirtual,
				"arrUrlDropshipping" => $arrUrlDropshipping
			)
		);
		$this->load->view('footer', array("js_dropshipping_inicio" => true));
		$this->load->view('Dropshipping/FooterInicioView');
	}
    
	public function Ajax($action){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		switch($action){
			case "Reporte":
				$arrReporteGraficoPOST = $this->input->post('arrReporteGrafico');
				$arrRowGrafico = $this->ConfiguracionModel->reporteGraficoTiendaVirtual($arrReporteGraficoPOST);
				
				$titulo = 'Reporte Mensual';
				echo $this->load->view('Dropshipping/_InicioView', array(
					"reporte"   => $arrRowGrafico,
					"dInicial"  => $arrReporteGraficoPOST['dInicial'],
					"dFinal"    => $arrReporteGraficoPOST['dFinal'],
					"iIDMoneda" => $arrReporteGraficoPOST['iIDMoneda'],
					"iImpuesto" => $arrReporteGraficoPOST['iImpuesto'],
				), true);
				$this->load->view('Dropshipping/FooterInicioView');
				break;
		}
	}
}
