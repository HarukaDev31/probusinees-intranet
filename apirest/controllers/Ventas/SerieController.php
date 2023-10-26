<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SerieController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/SerieModel');
	}

	public function listarSeries(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/SerieView');
			$this->load->view('footer', array("js_serie" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SerieModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
			$iIdAlmacen = ($row->ID_Almacen > 0 ? $row->ID_Almacen : 0);
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
				$rows[] = $row->No_Organizacion;
			}
			$rows[] = $row->No_Almacen;
            $rows[] = $row->No_Tipo_Documento_Breve;
            $rows[] = $row->ID_Serie_Documento;
            $rows[] = $row->Nu_Numero_Documento;
            $rows[] = $row->Nu_Pos;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verSerie(\'' . $row->ID_Serie_Documento_PK . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarSerie(\'' . $row->ID_Serie_Documento_PK . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SerieModel->count_all(),
	        'recordsFiltered' => $this->SerieModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID_Serie_Documento_PK){
		echo json_encode($this->SerieModel->get_by_id($this->security->xss_clean($ID_Serie_Documento_PK)));
    }
    
	public function crudSerie(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		if($this->empresa->Nu_Tipo_Proveedor_FE == '3' && ($this->input->post('ID_Tipo_Documento')!='14' && $this->input->post('ID_Tipo_Documento')!='2') ){
			echo json_encode(array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El tipo EMPRESA es INTERNO, elegir solo NOTA DE VENTA o GUIA INTERNA'));
			exit();
		}

		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			'ID_Almacen' => $this->input->post('ID_Almacen'),
			'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
			'ID_Serie_Documento' => strtoupper($this->input->post('ID_Serie_Documento')),
			'Nu_Numero_Documento' => $this->input->post('Nu_Numero_Documento'),
			'Nu_Cantidad_Caracteres' => $this->input->post('Nu_Cantidad_Caracteres'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
			'ID_POS' => (!empty($this->input->post('ID_POS')) ? $this->input->post('ID_POS') : NULL),
		);
		echo json_encode(
		($this->input->post('EID_Serie_Documento_PK') != '') ?
			$this->SerieModel->actualizarSerie(array('ID_Serie_Documento_PK' => $this->input->post('EID_Serie_Documento_PK')), $data, $this->input->post('EID_Organizacion'), $this->input->post('EID_Almacen'), $this->input->post('EID_Tipo_Documento'), $this->input->post('EID_Serie_Documento'), $this->input->post('radio-addSerieIgual'))
		:
			$this->SerieModel->agregarSerie($data, $this->input->post('radio-addSerieIgual'))
		);
	}
    
	public function eliminarSerie($ID_Serie_Documento_PK){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SerieModel->eliminarSerie($this->security->xss_clean($ID_Serie_Documento_PK)));
	}
}
