<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PosController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/PosModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/PosView');
			$this->load->view('footer', array("js_pos" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->PosModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Empresa;
            $rows[] = $row->No_Organizacion;
            //$rows[] = $row->ID_Pos;
            $rows[] = $row->Nu_Pos;
            $rows[] = $row->No_Pos;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPos(\'' . $row->ID_Pos . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPos(\'' . $row->ID_Pos . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->PosModel->count_all(),
	        'recordsFiltered' => $this->PosModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID_Pos){
		$_data = $this->PosModel->get_by_id($this->security->xss_clean($ID_Pos));
        $data = array(
        	'ID_Empresa' => $_data->ID_Empresa,
			'ID_Organizacion' => $_data->ID_Organizacion,
			'ID_POS' => $_data->ID_POS,
        	'Nu_Pos' => $_data->Nu_Pos,
        	'No_Pos' => $_data->No_Pos,
        	'Txt_Autorizacion_Venta_Serie_Disco_Duro' => $_data->Txt_Autorizacion_Venta_Serie_Disco_Duro,
			'Nu_Estado' => $_data->Nu_Estado,
			'Key_Serie_Disco_Duro' => $_data->Txt_Autorizacion_Venta_Serie_Disco_Duro,
		);
        echo json_encode($data);
    }
    
	public function crudPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			'Nu_Pos' => $this->input->post('Nu_Pos'),
			'No_Pos' => $this->input->post('No_Pos'),
			'Txt_Autorizacion_Venta_Serie_Disco_Duro' => $this->input->post('Txt_Autorizacion_Venta_Serie_Disco_Duro'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Organizacion') != '' && $this->input->post('EID_Pos') != '') ?
			$this->PosModel->actualizarPos(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Organizacion' => $this->input->post('EID_Organizacion'), 'ID_Pos' => $this->input->post('EID_Pos')), $data, $this->input->post('EID_Organizacion'), $this->input->post('ENu_Pos'), $this->input->post('ENo_Pos'))
		:
			$this->PosModel->agregarPos($data)
		);
	}
    
	public function eliminarPos($ID_Pos){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PosModel->eliminarPos($this->security->xss_clean($ID_Pos)));
	}
}
