<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DownloadController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
	    $this->load->helper('download');

		if(!isset($this->session->userdata['usuario'])) {
			exit();	
		}
	}
	
	function download($filename = NULL) {
		$filename_path = './assets/downloads/' . $filename;
		if (file_exists($filename_path)){
		    $data = file_get_contents($filename_path);
		    force_download($filename, $data);
		}
	}
}