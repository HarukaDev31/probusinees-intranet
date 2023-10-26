<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DelibroController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('TransaccionModel');
	}
	
	public function IzipayTransaccion(){
		
		$this->TransaccionModel->ProcesarPagoIzipay();

	}
	
	
}
