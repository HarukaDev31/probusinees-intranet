<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrdenSeguimientoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/OrdenSeguimientoModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/OrdenSeguimientoView');
			$this->load->view('footer', array("js_orden_seguimiento" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->OrdenSeguimientoModel->get_datatables();
        $data = array();
		$draw = intval($this->input->get("draw"));
		$no = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Almacen;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $rows[] = $row->No_Descripcion;
            $rows[] = $row->ID_Documento_Cabecera;
            $rows[] = $row->No_Entidad;
            $rows[] = $row->No_Contacto;
            $rows[] = $row->Txt_Observacion;
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verOrdenSeguimiento(\'' . $row->ID_Orden_Seguimiento . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarOrdenSeguimiento(\'' . $row->ID_Orden_Seguimiento . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->OrdenSeguimientoModel->count_all(),
	        'recordsFiltered' => $this->OrdenSeguimientoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID_Orden_Seguimiento){
		echo json_encode($this->OrdenSeguimientoModel->get_by_id($this->security->xss_clean($ID_Orden_Seguimiento)));
    }
    
	public function crudOrdenSeguimiento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		if ($this->input->post('Nu_Tipo_Contacto') == 0) {//0=Existe
			$data = array(
				'ID_Tipo_Orden_Seguimiento' => $this->input->post('ID_Tipo_Orden_Seguimiento'),
				'ID_Documento_Cabecera'	=> $this->input->post('ID_Documento_Cabecera'),
				'Fe_Registro' => ToDate($this->input->post('Fe_Registro')) . ' ' . $this->input->post('ID_Hora') . ':' . $this->input->post('ID_Minuto') . ':00',
				'Nu_Tipo_Contacto' => $this->input->post('Nu_Tipo_Contacto'),
				'Txt_Observacion' => nl2br($this->input->post('Txt_Observacion')),
			);
		} else {
			$Nu_Celular_Contacto = '';
			if ( $this->input->post('Nu_Celular_Contacto') && strlen($this->input->post('Nu_Celular_Contacto')) === 11){
		        $Nu_Celular_Contacto = explode(' ', $this->input->post('Nu_Celular_Contacto'));
		        $Nu_Celular_Contacto = $Nu_Celular_Contacto[0].$Nu_Celular_Contacto[1].$Nu_Celular_Contacto[2];
			}
			
			$Nu_Telefono_Contacto = '';
			if ( $this->input->post('Nu_Telefono_Contacto') && strlen($this->input->post('Nu_Telefono_Contacto')) === 8){
		        $Nu_Telefono_Contacto = explode(' ', $this->input->post('Nu_Telefono_Contacto'));
		        $Nu_Telefono_Contacto = $Nu_Telefono_Contacto[0].$Nu_Telefono_Contacto[1];
			}
			$data = array(
				'ID_Tipo_Orden_Seguimiento' => $this->input->post('ID_Tipo_Orden_Seguimiento'),
				'ID_Documento_Cabecera' => $this->input->post('ID_Documento_Cabecera'),
				'Fe_Registro' => ToDate($this->input->post('Fe_Registro')) . ' ' . $this->input->post('ID_Hora') . ':' . $this->input->post('ID_Minuto') . ':00',
				'Nu_Tipo_Contacto' => $this->input->post('Nu_Tipo_Contacto'),
				'Txt_Observacion' => $this->input->post('Txt_Observacion'),
				'ID_Tipo_Documento_Identidad' => $this->input->post('ID_Tipo_Documento_Identidad'),
				'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
				'No_Contacto' => $this->input->post('No_Contacto'),
				'Txt_Email_Contacto' => $this->input->post('Txt_Email_Contacto'),
				'Nu_Celular_Contacto' => $Nu_Celular_Contacto,
				'Nu_Telefono_Contacto' => $Nu_Telefono_Contacto,
			);
		}
		
		echo json_encode(
		($this->input->post('EID_Orden_Seguimiento') != '') ?
			$this->OrdenSeguimientoModel->actualizarOrdenSeguimiento(array('ID_Orden_Seguimiento' => $this->input->post('EID_Orden_Seguimiento')), $data)
		:
			$this->OrdenSeguimientoModel->agregarOrdenSeguimiento($data)
		);
	}
    
	public function eliminarOrdenSeguimiento($ID_Orden_Seguimiento){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->OrdenSeguimientoModel->eliminarOrdenSeguimiento($this->security->xss_clean($ID_Orden_Seguimiento)));
	}
}
