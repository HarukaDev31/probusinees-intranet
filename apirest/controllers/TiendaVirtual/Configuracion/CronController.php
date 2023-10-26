<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CronController extends CI_Controller {
	
	
	function __construct(){

		if(!is_cli()) // solo se ejecuta en terminal
			exit();

    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->library('ConfiguracionTienda',NULL,"ConfiguracionTienda");
		$this->load->library('BorradoArchivo',NULL,"BorradoArchivo");
		$this->load->model('TiendaVirtual/Configuracion/SistemaModel');
	}

	public function ValidarPagoUsuario(){
	 
		$this->SistemaModel->ValidarVencimientoPago();

	}

	public function BorradoReportes(){
		
		$this->SistemaModel->ReporteBorrado();
	}

	public function ListadoBorradoReportes(){
		
		$this->SistemaModel->ListadoReporteBorrado();
	}
	
	public function CreacionDominio(){
		$this->SistemaModel->CreacionDominio();
	}

	public function GenerarCatalogos(){

		$this->SistemaModel->CatalogoCron();
		
	}

	public function GenerarArchivoFacebookLaeShop() {
		
		$this->SistemaModel->ArchivoFacebookShopCron();
	}

	public function GenerarArchivoGoogleLaeShop() {
		
		$this->SistemaModel->ArchivoGoogleLaeShopCron();
	}
}
