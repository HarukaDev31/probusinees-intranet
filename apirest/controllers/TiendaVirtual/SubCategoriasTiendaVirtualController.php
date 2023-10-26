<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SubCategoriasTiendaVirtualController extends CI_Controller {	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/SubCategoriaModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			$this->load->view('TiendaVirtual/SubCategoriaView');
			$this->load->view('footer', array("js_subcategoria_tienda_virtual" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SubCategoriaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Familia;
            $rows[] = $row->No_Sub_Familia;		
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Activar_Lae_Shop);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCategoria(\'' . $row->ID_Sub_Familia . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCategoria(\'' . $row->ID_Sub_Familia . '\', \''.$action.'\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SubCategoriaModel->count_all(),
	        'recordsFiltered' => $this->SubCategoriaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->SubCategoriaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCategoria(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Familia' => $this->input->post('ID_Familia'),
			'No_Sub_Familia' => $this->input->post('No_Sub_Familia'),
			'Nu_Activar_Lae_Shop' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Sub_Familia') != '') ?
			$this->SubCategoriaModel->actualizarCategoria(array('ID_Sub_Familia' => $this->input->post('EID_Sub_Familia')), $data, $this->input->post('ENo_Sub_Familia'))
		:
			$this->SubCategoriaModel->agregarCategoria($data)
		);
	}
    
	public function eliminarCategoria($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SubCategoriaModel->eliminarCategoria($this->security->xss_clean($ID)));
	}
}
