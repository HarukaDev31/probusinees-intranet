<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CatalogoController extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('CatalogoModel');
		$this->load->model('HelperImportacionModel');
	}

	public function listar()
	{
		if (!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if (isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('CatalogoView');
			$this->load->view('footer_v2', array("js_catalogo" => true));
		}
	}
	public function saveProduct()
	{
		$data = $this->input->post();
		$files = $_FILES;
		$response = $this->CatalogoModel->saveProduct($data, $files);
		echo json_encode($response);
	}
	public function getCatalogo()
	{
		$data = $this->input->post();
		$response = $this->CatalogoModel->getCatalogo($data);
		echo json_encode($response);
	}
	public function getProductDetails($id)
	{
		$response = $this->CatalogoModel->getProductDetails($id);
		echo json_encode($response);
	}
	public function deleteProduct()
	{
		$productId = $this->input->post('productId');
		log_message('error', 'ID: ' . $productId);	
		$response = $this->CatalogoModel->deleteProduct($productId);
		echo json_encode($response);
	}
}
