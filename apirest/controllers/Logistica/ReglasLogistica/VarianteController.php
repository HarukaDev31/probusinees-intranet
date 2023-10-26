<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VarianteController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/VarianteModel');
		$this->load->model('HelperModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/VarianteView');
			$this->load->view('footer', array("js_variante" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->VarianteModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();

			$arrVarianteItem = $this->HelperModel->obtenerVarianteItemArray($row->ID_Tabla_Dato);
			$rows[] = $arrVarianteItem['No_Descripcion'];
            $rows[] = $row->No_Variante;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMarca(\'' . $row->ID_Variante_Item . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMarca(\'' . $row->ID_Variante_Item . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->VarianteModel->count_all(),
	        'recordsFiltered' => $this->VarianteModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->VarianteModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMarca(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Tabla_Dato' => $this->input->post('ID_Tabla_Dato'),
			'No_Variante' => $this->input->post('No_Variante'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Variante_Item') != '') ?
			$this->VarianteModel->actualizarMarca(array('ID_Variante_Item' => $this->input->post('EID_Variante_Item')), $data, $this->input->post('ENo_Variante'), $this->input->post('EID_Tabla_Dato'))
		:
			$this->VarianteModel->agregarMarca($data)
		);
	}
    
	public function eliminarMarca($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VarianteModel->eliminarMarca($this->security->xss_clean($ID)));
	}

	//Detalle
	public function ajax_list_detalle(){
		$arrData = $this->VarianteModel->get_datatables_detalle();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();

			$arrVarianteItem = $this->HelperModel->obtenerVarianteItemArray($row->ID_Tabla_Dato);
			$rows[] = $arrVarianteItem['No_Descripcion'];
			$rows[] = $row->No_Variante;
            $rows[] = $row->No_Valor;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMarcaDetalle(\'' . $row->ID_Variante_Item_Detalle . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMarcaDetalle(\'' . $row->ID_Variante_Item_Detalle . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->VarianteModel->count_all_detalle(),
	        'recordsFiltered' => $this->VarianteModel->count_filtered_detalle(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit_detalle($ID){
        echo json_encode($this->VarianteModel->get_by_id_detalle($this->security->xss_clean($ID)));
    }
    
	public function crudMarcaDetalle(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa_Detalle'),
			'ID_Variante_Item' => $this->input->post('ID_Variante_Item'),
			'No_Valor' => $this->input->post('No_Valor'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado_Detalle'),
		);
		echo json_encode(
		($this->input->post('EID_Variante_Item_Detalle') != '') ?
			$this->VarianteModel->actualizarMarcaDetalle(array('ID_Variante_Item_Detalle' => $this->input->post('EID_Variante_Item_Detalle')), $data, $this->input->post('ENo_Valor'), $this->input->post('EID_Variante_Item'))
		:
			$this->VarianteModel->agregarMarcaDetalle($data)
		);
	}
    
	public function eliminarMarcaDetalle($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->VarianteModel->eliminarMarcaDetalle($this->security->xss_clean($ID)));
	}
}
