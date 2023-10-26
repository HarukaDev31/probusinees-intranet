<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class BlogHistorialUsuarioController extends CI_Controller {	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Blog/BlogHistorialUsuarioModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Blog/BlogHistorialUsuarioView');
			$this->load->view('footer', array("js_blog_historial_usuario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->BlogHistorialUsuarioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Grupo;
            $rows[] = $row->No_USuario;
            $rows[] = ToDateBD($row->Fe_Emision);
            $rows[] = $row->No_Titulo_Blog;
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BlogHistorialUsuarioModel->count_all(),
	        'recordsFiltered' => $this->BlogHistorialUsuarioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
}
