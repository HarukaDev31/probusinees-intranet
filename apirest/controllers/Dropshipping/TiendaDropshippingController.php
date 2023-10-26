<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TiendaDropshippingController extends CI_Controller {
	private $upload_path = '../assets/images/logos_tienda/';
	private $upload_path_table = '../assets/images/logos_tienda';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->library('ConfiguracionTienda',NULL,"ConfiguracionTienda");
		$this->load->model('Dropshipping/SistemaModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			$this->load->view('Dropshipping/SistemaView');
			$this->load->view('footer', array("js_sistema_dropshipping" => true));
		}
	}

	public function ValidarDominioTienda(){
		
		echo $this->SistemaModel->ValidarDominioTienda();

	}
	
	public function ajax_list(){
		$arrData = $this->SistemaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
			$rows = array();
		
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
			}

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verSistema(\'' . $row->ID_Configuracion . '\', \'' . $row->Txt_Url_Logo_Lae_Shop . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';

			$arrImgProducto = explode('logos_tienda',$row->Txt_Url_Logo_Lae_Shop);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->Txt_Url_Logo_Lae_Shop)){
				if ( file_exists($sPathImgProducto) ) {
					$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $row->Txt_Url_Logo_Lae_Shop . '" src="' . $base64 . '" title="' . $row->No_Tienda_Lae_Shop . '" alt="' . $row->No_Tienda_Lae_Shop . '" style="cursor:pointer; max-height:40px;" />';
				}
			}
			
			$rows[] = $image;
            $rows[] = $row->No_Tienda_Lae_Shop;
            $rows[] = $row->Nu_Celular_Lae_Shop;
			$rows[] = $row->Nu_Celular_Whatsapp_Lae_Shop;
			$rows[] = $row->Txt_Email_Lae_Shop;
			$rows[] = $row->Txt_Descripcion_Lae_Shop;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->SistemaModel->count_all(),
	        'recordsFiltered' => $this->SistemaModel->count_filtered(),
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
				$sNombreImagenMasExtension = $_FILES['file']['name'];
				$sExtensionNombreImagen = pathinfo($sNombreImagenMasExtension, PATHINFO_EXTENSION);

				$str = $sNombreImagenMasExtension;
				$arrNombre=(explode("jpeg",$str));
				$sNombreImagen = substr($arrNombre[0], 0, -1);

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg';
				$config['max_size'] = 400;//400 KB
				$config['file_name'] = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén',
						'sClassModal' => 'modal-danger',
					);
				} else {
					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Configuracion' => $this->input->post('iIdConfiguracion') );
					$this->SistemaModel->actualizarVersionImagen($where, $data);

					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenCategoria' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
						'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					);
				}
			} else {
				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'La imagen ya fue guardada',
					'sClassModal' => 'modal-success',
					'sNombreImagenCategoria' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
				);
			}
    	}
    	echo json_encode($arrResponse);
	}

    public function removeFileImage(){
    	$nameFileImage = $this->input->post('nameFileImage');
		if ( strpos($nameFileImage,"/") > 0 ) {
			if ( file_exists($nameFileImage) ){
				unlink($nameFileImage);
				$data = array('Txt_Url_Logo_Lae_Shop' => '');
				$where = array('ID_Configuracion' => $this->input->post('iIdConfiguracion') );
				echo json_encode($this->SistemaModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 1'));
			}
		} else {
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/';
			if ( $nameFileImage && file_exists($path . $nameFileImage) ){
				unlink($path . $nameFileImage);
				$data = array('Txt_Url_Logo_Lae_Shop' => '');
				$where = array('ID_Configuracion' => $this->input->post('iIdProducto') );
				echo json_encode($this->SistemaModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 2'));
			}
		}
    }

	public function get_image(){
		$sUrlImage = $this->input->post('sUrlImage');
		$arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImage);
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
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
        echo json_encode($this->SistemaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudSistema(){
	
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		$Nu_Celular_Lae_Shop = '';
		if ( $this->input->post('Nu_Celular_Lae_Shop') && strlen($this->input->post('Nu_Celular_Lae_Shop')) == 11){
	        $Nu_Celular_Lae_Shop = explode(' ', $this->input->post('Nu_Celular_Lae_Shop'));
	        $Nu_Celular_Lae_Shop = $Nu_Celular_Lae_Shop[0].$Nu_Celular_Lae_Shop[1].$Nu_Celular_Lae_Shop[2];
		}
		
		$Nu_Celular_Whatsapp_Lae_Shop = '';
		if ( $this->input->post('Nu_Celular_Whatsapp_Lae_Shop') && strlen($this->input->post('Nu_Celular_Whatsapp_Lae_Shop')) == 11){
	        $Nu_Celular_Whatsapp_Lae_Shop = explode(' ', $this->input->post('Nu_Celular_Whatsapp_Lae_Shop'));
	        $Nu_Celular_Whatsapp_Lae_Shop = $Nu_Celular_Whatsapp_Lae_Shop[0].$Nu_Celular_Whatsapp_Lae_Shop[1].$Nu_Celular_Whatsapp_Lae_Shop[2];
		}

		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'Txt_Url_Logo_Lae_Shop' => $this->input->post('Txt_Url_Logo_Lae_Shop'),
			'No_Tienda_Lae_Shop' => $this->input->post('No_Tienda_Lae_Shop'),
			'Nu_Celular_Lae_Shop' => $Nu_Celular_Lae_Shop,
			'Nu_Celular_Whatsapp_Lae_Shop' => $Nu_Celular_Whatsapp_Lae_Shop,
			'Txt_Email_Lae_Shop' => $this->input->post('Txt_Email_Lae_Shop'),
			'Txt_Descripcion_Lae_Shop' => $this->input->post('Txt_Descripcion_Lae_Shop'),
			'Nu_Validar_Stock_Laeshop' => $this->input->post('Nu_Validar_Stock_Laeshop'),
			'No_Html_Color_Lae_Shop' => $this->input->post('No_Html_Color_Lae_Shop'),
			'Nu_Activar_Precio_Centralizado_Laeshop' => $this->input->post('Nu_Activar_Precio_Centralizado_Laeshop'),
			'Nu_Activar_Emitir_Factura_Laeshop' => $this->input->post('Nu_Activar_Emitir_Factura_Laeshop')
		);
		echo json_encode($this->SistemaModel->actualizarSistema(array('ID_Configuracion' => $this->input->post('EID_Configuracion')), $data));
	}
}
