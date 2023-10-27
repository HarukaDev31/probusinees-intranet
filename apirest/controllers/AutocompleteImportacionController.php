<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AutocompleteImportacionController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AutocompleteImportacionModel');

		if(!isset($this->session->userdata['usuario'])) {
			$sapi_type = php_sapi_name();
			if (substr($sapi_type, 0, 3) == 'cgi')
				header("Status: 404 Not Found");
			else
				header("HTTP/1.1 404 Not Found");
			exit();
		}
	}
	
	public function globalAutocomplete(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_search') ){
			$arrPost = array(
				"global_search" => $this->input->post('global_search')
			);
			echo json_encode($this->AutocompleteImportacionModel->getDataAutocompleteProduct($arrPost));
		}
	}
}
