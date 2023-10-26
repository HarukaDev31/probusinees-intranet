<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ValorImpuestoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/ValorImpuestoModel');
	}

	public function listarValoresImpuestos(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/ValorImpuestoView');
			$this->load->view('footer', array("js_valor_impuesto" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ValorImpuestoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Impuesto;
            $rows[] = $row->Ss_Impuesto;
            $rows[] = $row->Po_Impuesto . ' %';
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verValorImpuesto(\'' . $row->ID_Impuesto_Cruce_Documento . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarValorImpuesto(\'' . $row->ID_Impuesto_Cruce_Documento . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ValorImpuestoModel->count_all(),
	        'recordsFiltered' => $this->ValorImpuestoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ValorImpuestoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudValorImpuesto(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Impuesto'	=> $this->input->post('ID_Impuesto'),
			'Ss_Impuesto'	=> $this->input->post('Ss_Impuesto'),
			'Po_Impuesto'	=> $this->input->post('Po_Impuesto'),
			'Nu_Estado'		=> $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Impuesto') != '' && $this->input->post('EID_Impuesto_Cruce_Documento') != '') ?
			$this->ValorImpuestoModel->actualizarValorImpuesto(array('ID_Impuesto_Cruce_Documento' => $this->input->post('EID_Impuesto_Cruce_Documento')), $data, $this->input->post('EID_Impuesto'), $this->input->post('ESs_Impuesto'))
		:
			$this->ValorImpuestoModel->agregarValorImpuesto($data)
		);
	}
    
	public function eliminarValorImpuesto($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ValorImpuestoModel->eliminarValorImpuesto($this->security->xss_clean($ID)));
	}
	
	public function getTiposImpuestos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->ValorImpuestoModel->getTiposImpuestos());
	}
}
