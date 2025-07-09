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
	public function listarCompletados()
	{
		if (!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if (isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('CatalogoView');
			$this->load->view('footer_v2', array("js_catalogo" => true));
		}
	}
	public function listarSeleccionados(){
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
	public function getCatalogoCompletados()
	{
		$data = $this->input->post();
		$response = $this->CatalogoModel->getCatalogoCompletados($data);
		echo json_encode($response);
	}
	public function getCatalogoTienda()
	{
		$data = $this->input->post();
		$response = $this->CatalogoModel->getCatalogoSeleccionados($data);
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
	public function deleteMultipleProducts()
	{
		$productIds = $this->input->post('productIds');
		$ids = json_decode($productIds, true);
		log_message('error', 'IDs: ' . json_encode($ids));
		$response = $this->CatalogoModel->deleteMultipleProducts($ids);
		echo json_encode($response);
	}
	public function sendCotizacion(){
		$productId = $this->input->post('productId');
		$response = $this->CatalogoModel->sendCotizacion($productId);
		echo json_encode($response);
	}
	public function pasarTienda(){
		$productIds = $this->input->post('productIds');
		$response = $this->CatalogoModel->pasarTienda($productIds);
		echo json_encode($response);
	}
	public function getCategorias(){
		$response = $this->CatalogoModel->getCategorias();
		echo json_encode($response['data']);
	}
	public function createCategoria(){
		$categoria = $this->input->post('categoria');	
		$response = $this->CatalogoModel->createCategoria($categoria);
		echo json_encode($response);
	}
	public function guardarCategoriaProductos(){
		$categoriaId= $this->input->post('categoriaId');
		$productIds= $this->input->post('productIds');
		$response = $this->CatalogoModel->guardarCategoriaProductos($categoriaId, $productIds);
		echo json_encode($response);
	}
}
