<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SeriesxProductoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/SeriesxProductoModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/SeriesxProductoView');
			$this->load->view('footer', array("js_series_x_producto" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SeriesxProductoModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Producto;
            $rows[] = $row->No_Serie_Producto;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verSeriexProducto(\'' . $row->ID_Series_x_Producto . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SeriesxProductoModel->count_all(),
	        'recordsFiltered' => $this->SeriesxProductoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->SeriesxProductoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode(
		($_POST['arrHeader']['EID_Series_x_Producto'] != '') ?
			$this->SeriesxProductoModel->actualizarSeriesxProducto(array('ID_Series_x_Producto' => $_POST['arrHeader']['EID_Series_x_Producto']), $_POST, $this->input->post('ENo_Serie_Producto'))
		:
			$this->SeriesxProductoModel->agregarSeriesxProducto($_POST)
		);
	}
}
