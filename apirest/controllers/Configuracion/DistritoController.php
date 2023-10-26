<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DistritoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/DistritoModel');
	}

	public function listarDistritos(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/DistritoView');
			$this->load->view('footer', array("js_distrito" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->DistritoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Pais;
            $rows[] = $row->No_Departamento;
            $rows[] = $row->No_Provincia;
            $rows[] = $row->No_Distrito;
            //$rows[] = $row->Ss_Delivery;
            //$rows[] = (($row->Nu_Habilitar_Ecommerce==1) ? 'Si' : 'No');
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verDistrito(\'' . $row->ID_Distrito . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarDistrito(\'' . $row->ID_Distrito . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->DistritoModel->count_all(),
	        'recordsFiltered' => $this->DistritoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->DistritoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudDistrito(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Provincia'		=> $this->input->post('ID_Provincia'),
			'No_Distrito'		=> $this->input->post('No_Distrito'),
			'No_Distrito_Breve'	=> $this->input->post('No_Distrito_Breve'),
			'Ss_Delivery'	=> $this->input->post('Ss_Delivery'),
			'Nu_Habilitar_Ecommerce' => $this->input->post('Nu_Habilitar_Ecommerce'),
			'Nu_Estado'			=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Provincia') != '') && ($this->input->post('EID_Distrito') != '') ?
			$this->DistritoModel->actualizarDistrito(array('ID_Provincia' => $this->input->post('EID_Provincia'), 'ID_Distrito' => $this->input->post('EID_Distrito')), $data, $this->input->post('EID_Provincia'), $this->input->post('ENo_Distrito'))
		:
			$this->DistritoModel->agregarDistrito($data)
		);
	}
    
	public function eliminarDistrito($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->DistritoModel->eliminarDistrito($this->security->xss_clean($ID)));
	}
}
