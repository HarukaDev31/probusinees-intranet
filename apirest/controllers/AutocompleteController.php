<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AutocompleteController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AutocompleteModel');

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
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			$filter_id_codigo = $this->input->post('filter_id_codigo');
			echo json_encode($this->AutocompleteModel->getDataAutocompleteProduct($global_table, $global_search, $filter_id_codigo));
		}
	}
	
	public function globalAutocompleteReport(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			$filter_id_codigo = $this->input->post('filter_id_codigo');
			$filter_id_tipo_movimiento = $this->input->post('filter_id_tipo_movimiento');
			echo json_encode($this->AutocompleteModel->getDataAutocompleteProductReport($global_table, $global_search, $filter_id_codigo, $filter_id_tipo_movimiento));
		}
	}
	
	public function getAllClient(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllClient($global_table, $global_search));
		}
	}
	
	public function getAllClientCargaConsolidada(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllClientCargaConsolidada($global_table, $global_search));
		}
	}
	
	public function getAllProvider(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllProvider($global_table, $global_search));
		}
	}
	
	public function getAllEmployee(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllEmployee($global_table, $global_search));
		}
	}
	
	public function obtenerUsuarios(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->obtenerUsuarios($global_table, $global_search));
		}
	}
	
	public function getAllDelivery(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && !empty($this->input->post('global_search')) ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllDelivery($global_table, $global_search));
		} else {
			echo json_encode(array('message'=>'No hay registros'));
		}
	}
	
	public function getAllProduct(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && !empty($this->input->post('global_search')) ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			$filter_id_almacen = $this->input->post('filter_id_almacen');
			$filter_nu_compuesto = $this->input->post('filter_nu_compuesto');
			$filter_nu_tipo_producto = $this->input->post('filter_nu_tipo_producto');
			$filter_lista = $this->input->post('filter_lista');
			//if(isset($_POST['filter_inactive_item']) && $this->input->post('filter_inactive_item')==1 && $this->empresa->Nu_Validar_Stock==1) {
			if(isset($_POST['filter_inactive_item']) && $this->input->post('filter_inactive_item')==1){
				$arrData = $this->AutocompleteModel->getAllProduct($global_table, $global_search, $filter_id_almacen, $filter_nu_compuesto, $filter_nu_tipo_producto, $filter_lista);
				foreach ($arrData as $row) {
					$rows = array();
					if( $row->Nu_Tipo_Producto == 1 && $row->Nu_Estado == 0 && !empty($row->Qt_Producto))
						echo '';
					else
						$data[] = $row;
				}
				echo json_encode($data);
			} else {
				echo json_encode($this->AutocompleteModel->getAllProduct($global_table, $global_search, $filter_id_almacen, $filter_nu_compuesto, $filter_nu_tipo_producto, $filter_lista));
			}
		} else {
			echo "";
		}
	}
	
	public function globalAutocompleteKardex(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			$filter_id_codigo = $this->input->post('filter_id_codigo');
			$filter_id_tipo_movimiento = $this->input->post('filter_id_tipo_movimiento');
			echo json_encode($this->AutocompleteModel->getDataAutocompleteProductKardex($global_table, $global_search, $filter_id_codigo, $filter_id_tipo_movimiento));
		}
	}
	
	public function getItemAlternativos(){
		if ( $this->input->is_ajax_request() ){
			echo json_encode($this->AutocompleteModel->getItemAlternativos($this->input->post()));
		}
	}
	
	public function getItemsVariante(){
		if ( $this->input->is_ajax_request() ){
			echo json_encode($this->AutocompleteModel->getItemsVariante($this->input->post()));
		}
	}
	
	public function autocompleteItemAlternativos(){
		if ( $this->input->is_ajax_request() ){
			echo json_encode($this->AutocompleteModel->autocompleteItemAlternativos($this->input->post()));
		}
	}
	
	public function getAllProductClic(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			$filter_id_almacen = $this->input->post('filter_id_almacen');
			$filter_nu_compuesto = $this->input->post('filter_nu_compuesto');
			$filter_nu_tipo_producto = $this->input->post('filter_nu_tipo_producto');
			$filter_lista = $this->input->post('filter_lista');
			echo json_encode($this->AutocompleteModel->getAllProductClic($global_table, $global_search, $filter_id_almacen, $filter_nu_compuesto, $filter_nu_tipo_producto, $filter_lista));
		}
	}
	
	public function getAllContact(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_search') ){
			$global_search = $this->input->post('global_search');
			$iFilter_Tipo_Asiento = $this->input->post('filter_tipo_asiento');
			echo json_encode($this->AutocompleteModel->getAllContact($global_search, $iFilter_Tipo_Asiento));
		}
	}
	
	public function getAllOrden(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_search') ){
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllOrden($global_search));
		}
	}
	
	public function sendData(){
		echo json_encode($this->AutocompleteModel->getData($this->input->post('sTabla'), $this->input->post('iTipoSocio')));
	}
	
	public function getAllItemSunat(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_search') ){
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllItemSunat($global_search));
		}
	}
	
	public function getClienteEspecifico(){
		if ( $this->input->is_ajax_request() && $this->input->post('sNumeroDocumentoIdentidad') ){
			echo json_encode($this->AutocompleteModel->getClienteEspecifico($this->input->post()));
		}
	}
	
	public function getAllClientMarketSeller(){
		if ( $this->input->is_ajax_request() && $this->input->post('global_table') && $this->input->post('global_search') ){
			$global_table = $this->input->post('global_table');
			$global_search = $this->input->post('global_search');
			echo json_encode($this->AutocompleteModel->getAllClientMarketSeller($global_table, $global_search));
		}
	}
}
