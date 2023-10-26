<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpresaController extends CI_Controller {
	private $upload_path = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/EmpresaModel');
	}
	
	public function listarEmpresas(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/EmpresaView');
			$this->load->view('footer', array("js_empresa" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->EmpresaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
		$sTipoProveedor = '';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ($row->Nu_Tipo_Proveedor_FE==1)
				$sTipoProveedor = '<span class="label label-primary">PSE N</span>';
			else if ($row->Nu_Tipo_Proveedor_FE==2)
				$sTipoProveedor = '<span class="label label-danger">SUNAT</span>';
			else
				$sTipoProveedor = '<span class="label label-dark">INTERNO</span>';
			
			$sStatusEmpresa='<span class="label label-success">Activo</span>';
			if ($row->Nu_Estado==0)
				$sStatusEmpresa='<span class="label label-danger">Inactivo</span>';
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verEmpresa(\'' . $row->ID_Empresa . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';

			$rows[] = $sTipoProveedor;
            $rows[] = $row->No_Tipo_Documento_Identidad_Breve;
            $rows[] = $row->Nu_Documento_Identidad;
            $rows[] = $row->No_Empresa;
			$rows[] = $row->No_Empresa_Comercial;
            $rows[] = $row->Txt_Direccion_Empresa;
			//$rows[] = ($row->Nu_MultiAlmacen == 0 ? 'No' : 'Si');//logo por almacen o sucursal luego de activar se agregar por logistica > reglas de logistica > almacen
			$rows[] = $sStatusEmpresa;
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarEmpresa(\'' . $row->ID_Empresa . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->EmpresaModel->count_all(),
	        'recordsFiltered' => $this->EmpresaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
		$arrData = $this->EmpresaModel->get_by_id($this->security->xss_clean($ID));
		if (file_exists($this->upload_path . 'FIRMA/' . $arrData->Nu_Documento_Identidad . '.pfx')) {
			$arrData = array_merge((array)$arrData, array('sNombreArchivoCertificadoDigital' => $arrData->Nu_Documento_Identidad . '.pfx'));
		}
        echo json_encode($arrData);
    }
    
	public function crudEmpresa(){		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'Nu_Tipo_Proveedor_FE' => $this->input->post('Nu_Tipo_Proveedor_FE'),
			'Nu_Activar_Guia_Electronica' => $this->input->post('Nu_Activar_Guia_Electronica'),
			'ID_Tipo_Documento_Identidad' => $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
			'No_Empresa' => $this->input->post('No_Empresa'),
			'No_Empresa_Comercial' => $this->input->post('No_Empresa_Comercial'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
			'Txt_Direccion_Empresa'	=> $this->input->post('Txt_Direccion_Empresa'),
			'Nu_MultiAlmacen'	=> $this->input->post('Nu_MultiAlmacen'),
			'ID_Ubigeo_Inei' => $this->input->post('ID_Ubigeo_Inei'),
			'ID_Pais' => $this->input->post('ID_Pais'),
			'ID_Departamento' => $this->input->post('ID_Departamento'),
			'ID_Provincia' => $this->input->post('ID_Provincia'),
			'ID_Distrito' => $this->input->post('ID_Distrito'),
			'Txt_Usuario_Sunat_Sol'	=> strtoupper($this->input->post('Txt_Usuario_Sunat_Sol')),
			'Txt_Password_Sunat_Sol' => $this->input->post('Txt_Password_Sunat_Sol'),
			'Txt_Password_Firma_Digital' => $this->input->post('Txt_Password_Firma_Digital'),
			'Nu_Tipo_Ecommerce_Empresa' => $this->input->post('Nu_Tipo_Ecommerce_Empresa'),
			'ID_Empresa_Marketplace' => $this->input->post('ID_Empresa_Marketplace'),
		);
		
		if ( $this->input->post('Nu_Tipo_Proveedor_FE') == 2 ) {// 2 = Facturador de Sunat
			if ( isset($_FILES['certificado_digital']['tmp_name']) && !empty($_FILES['certificado_digital']['tmp_name']) ) {
				$destino = $_FILES['certificado_digital']['name'];
				if (copy($_FILES['certificado_digital']['tmp_name'], $this->upload_path . 'FIRMA/' . $destino)) {
					if (file_exists($this->upload_path . 'FIRMA/' . $destino)) {
					}
				}
			}
			$path = $this->upload_path . "BETA/" . $this->input->post('Nu_Documento_Identidad');
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
			$path = $this->upload_path . "PRODUCCION/" . $this->input->post('Nu_Documento_Identidad');
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
		}

		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('ENu_Documento_Identidad') != '') ?
			$this->EmpresaModel->actualizarEmpresa(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'Nu_Documento_Identidad' => $this->input->post('ENu_Documento_Identidad')), $data, $this->input->post('ENu_Documento_Identidad'), $this->input->post('ETxt_Direccion_Empresa'))
		:
			$this->EmpresaModel->agregarEmpresa($data)
		);
	}
    
	public function eliminarEmpresa($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->EmpresaModel->eliminarEmpresa($this->security->xss_clean($ID)));
	}
	
	public function getDistritos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->EmpresaModel->getDistritos());
	}
	
	public function getEmpresas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->EmpresaModel->getEmpresas());
	}
    
	public function configuracionAutomaticaOpciones(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode( $this->EmpresaModel->configuracionAutomaticaOpciones($this->input->post()));
	}
}
