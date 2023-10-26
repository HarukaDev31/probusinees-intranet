<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoMedioPagoMarketplaceController extends CI_Controller {
	private $upload_path = '../assets/images/medios_pago/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/TipoMedioPagoMarketplaceModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/TipoMedioPagoMarketplaceView');
			$this->load->view('footer', array("js_tipo_medio_pago_marketplace" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->TipoMedioPagoMarketplaceModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}
            $rows[] = $row->No_Medio_Pago_Marketplace;
            $rows[] = $row->No_Tipo_Medio_Pago_Marketplace;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verTipoMedioPagoMarketplace(\'' . $row->ID_Tipo_Medio_Pago_Marketplace . '\', \'' . $row->No_Imagen_Tipo_Medio_Pago_Marketplace . '\', \'' . $row->No_Imagen_Url_Tipo_Medio_Pago_Marketplace . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarTipoMedioPagoMarketplace(\'' . $row->ID_Tipo_Medio_Pago_Marketplace . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->TipoMedioPagoMarketplaceModel->count_all(),
	        'recordsFiltered' => $this->TipoMedioPagoMarketplaceModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
    public function uploadOnly(){
    	$arrResponse = array(
			'sStatus' => 'error',
			'sMessage' => 'problemas con imagén',
			'sClassModal' => 'modal-danger',
		);
		
    	if (!empty($_FILES)){
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad;
			if(!is_dir($path)){
			    mkdir($path,0755,TRUE);
			}
			
			if ( !file_exists($path . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name'])) ){
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png';
				$config['max_size'] = 1024;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén',
						'sClassModal' => 'modal-danger',
					);
				} else {
					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagen' => $_FILES["file"]["name"],
						'sNombreImagenUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
					);
				}
			} else {				
				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'La imagen ya fue guardada',
					'sClassModal' => 'modal-success',
					'sNombreImagen' => $_FILES["file"]["name"],
					'sNombreImagenUrl' => $_FILES['file']['name'],
				);
			}
    	}
    	echo json_encode($arrResponse);
	}

    public function removeFileImage(){
    	$nameFileImage = $this->input->post('nameFileImage');
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/';
		if ( $nameFileImage && file_exists($path . $nameFileImage) ){
    		unlink($path . $nameFileImage);
    	}
    }

	public function get_image(){
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $this->input->post('sNombreImage');
    	$arrfilesImages = array();
		if ( file_exists($path) ){
			$arrfilesImages[] = array(
				'name' => $path,
				'size' => filesize($path),
			);
		}
		echo json_encode($arrfilesImages);
	}

	public function ajax_edit($ID){
        echo json_encode($this->TipoMedioPagoMarketplaceModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudTipoMedioPagoMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Medio_Pago_Marketplace' => $this->input->post('ID_Medio_Pago_Marketplace'),
			'No_Tipo_Medio_Pago_Marketplace' => $this->input->post('No_Tipo_Medio_Pago_Marketplace'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
			'No_Imagen_Tipo_Medio_Pago_Marketplace' => $this->input->post('No_Imagen_Tipo_Medio_Pago_Marketplace'),
			'No_Imagen_Url_Tipo_Medio_Pago_Marketplace' => $this->input->post('No_Imagen_Url_Tipo_Medio_Pago_Marketplace'),
		);
		echo json_encode(
		($this->input->post('EID_Tipo_Medio_Pago_Marketplace') != '') ?
			$this->TipoMedioPagoMarketplaceModel->actualizarTipoMedioPagoMarketplace(array('ID_Tipo_Medio_Pago_Marketplace' => $this->input->post('EID_Tipo_Medio_Pago_Marketplace')), $data, $this->input->post('ENo_Tipo_Medio_Pago_Marketplace'))
		:
			$this->TipoMedioPagoMarketplaceModel->agregarTipoMedioPagoMarketplace($data)
		);
	}
    
	public function eliminarTipoMedioPagoMarketplace($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->TipoMedioPagoMarketplaceModel->eliminarTipoMedioPagoMarketplace($this->security->xss_clean($ID)));
	}
}
