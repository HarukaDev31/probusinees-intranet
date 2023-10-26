<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImpuestoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/ImpuestoModel');
	}

	public function listarImpuestos(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/ImpuestoView');
			$this->load->view('footer', array("js_impuesto" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ImpuestoModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Impuesto;
            $rows[] = $row->No_Impuesto_Breve;
			$sNombreGrupoImpuesto = 'IGV';
			if($row->Nu_Tipo_Impuesto==2)
				$sNombreGrupoImpuesto = 'Inafecto';
			else if ($row->Nu_Tipo_Impuesto==3)
				$sNombreGrupoImpuesto = 'Exonerado';
			else if ($row->Nu_Tipo_Impuesto==4)
				$sNombreGrupoImpuesto = 'Gratuita';
            $rows[] = $sNombreGrupoImpuesto;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verImpuesto(\'' . $row->ID_Impuesto . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarImpuesto(\'' . $row->ID_Impuesto . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->ImpuestoModel->count_all(),
	        'recordsFiltered' => $this->ImpuestoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ImpuestoModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudImpuesto(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Impuesto' => $this->input->post('No_Impuesto'),
			'No_Impuesto_Breve'	=> $this->input->post('No_Impuesto_Breve'),
			'Nu_Sunat_Codigo' => $this->input->post('Nu_Sunat_Codigo'),
			'Nu_Tipo_Impuesto' => $this->input->post('Nu_Tipo_Impuesto'),
			'Nu_Valor_FE' => $this->input->post('Nu_Valor_FE'),
		);
		echo json_encode(
		($this->input->post('EID_Impuesto') != '') ?
			$this->ImpuestoModel->actualizarImpuesto(array('ID_Impuesto' => $this->input->post('EID_Impuesto')), $data, $this->input->post('ENo_Impuesto'))
		:
			$this->ImpuestoModel->agregarImpuesto($data)
		);
	}
    
	public function eliminarImpuesto($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ImpuestoModel->eliminarImpuesto($this->security->xss_clean($ID)));
	}
}
