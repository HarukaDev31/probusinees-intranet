<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BannersGrupal extends CI_Controller {
	//private $upload_path = '../assets/images/sliders/';
	private $upload_path = 'assets/images/sliders/';
	private $upload_path_table = '../assets/images/sliders';
	private $upload_path_table_v2 = '../../../assets/images/sliders';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImportacionGrupal/SliderModel');
		$this->load->model('ConfiguracionModel');
		$this->load->model('HelperDropshippingModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('ImportacionGrupal/SliderView');
			$this->load->view('footer_v2', array("js_slider_importacion" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->SliderModel->get_datatables();
        $data = array();
        $action='delete';
        foreach ($arrData as $row) {
            $rows = array();
			
			$arrImgProducto = explode('sliders',$row->No_Imagen_Url_Inicio_Slider);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table_v2 . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->No_Imagen_Url_Inicio_Slider)){
				//if ( file_exists($sPathImgProducto) ) {
					//$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					//$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $sPathImgProducto . '" src="' . $sPathImgProducto . '" title="' . $row->No_Imagen_Inicio_Slider . '" alt="' . $row->No_Imagen_Inicio_Slider . '" style="cursor:pointer; max-height:40px;" />';
				//}
			}
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \'' . $row->No_Imagen_Inicio_Slider . '\', \'' . $row->No_Imagen_Url_Inicio_Slider . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $rows[] = $row->Nu_Orden_Slider;
			$rows[] = !empty($image) ? $image : 'Sin imagen';

            $rows[] = $row->No_Slider;
			
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado_Slider);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \''.$action.'\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_list_slider_mobile(){
		$arrData = $this->SliderModel->get_datatables_slider_mobile();
        $data = array();
        $action='delete';
        foreach ($arrData as $row) {
            $rows = array();

			$arrImgProducto = explode('sliders',$row->No_Imagen_Url_Inicio_Slider);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table_v2 . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->No_Imagen_Url_Inicio_Slider)){
				//if ( file_exists($sPathImgProducto) ) {
					//$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					//$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $sPathImgProducto . '" src="' . $sPathImgProducto . '" title="' . $row->No_Imagen_Inicio_Slider . '" alt="' . $row->No_Imagen_Inicio_Slider . '" style="cursor:pointer; max-height:40px;" />';
				//}
			}
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \'' . $row->No_Imagen_Inicio_Slider . '\', \'' . $row->No_Imagen_Url_Inicio_Slider . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $rows[] = $row->Nu_Orden_Slider;
			$rows[] = $image;
            $rows[] = $row->No_Slider;

			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado_Slider);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \''.$action.'\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_list_ofertas(){
		$arrData = $this->SliderModel->get_datatables_ofertas();
        $data = array();
        $action='delete';
        foreach ($arrData as $row) {
            $rows = array();
            
			$arrImgProducto = explode('sliders',$row->No_Imagen_Url_Inicio_Slider);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->No_Imagen_Url_Inicio_Slider)){
				//if ( file_exists($sPathImgProducto) ) {
					//$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					//$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $sPathImgProducto . '" src="' . $sPathImgProducto . '" title="' . $row->No_Imagen_Inicio_Slider . '" alt="' . $row->No_Imagen_Inicio_Slider . '" style="cursor:pointer; max-height:40px;" />';
				//}
			}
			$rows[] = $image;

			//$rows[] = (file_exists($this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $row->No_Imagen_Inicio_Slider) ? '<img src="' . $row->No_Imagen_Url_Inicio_Slider . '" style="height:50px;"></img>' : 'Sin imagen');

            $rows[] = $row->No_Slider;
            $rows[] = $row->Nu_Orden_Slider;
			
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado_Slider);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \'' . $row->No_Imagen_Inicio_Slider . '\', \'' . $row->No_Imagen_Url_Inicio_Slider . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \''.$action.'\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
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
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 1024;
				$config['encrypt_name'] = TRUE;
				//$config['file_name'] = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén',
						'sClassModal' => 'modal-danger',
					);
				} else {
					$UploadData = $this->upload->data();

					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Ecommerce_Inicio' => $this->input->post('iIdEcommerceInicio') );
					$this->SliderModel->actualizarVersionImagen($where, $data);

					//$arrUrlImagePath = explode('..', $path);
					//$arrUrlImage = explode('/principal',base_url());
								
					$server_addr = $_SERVER['HTTP_HOST'];
					$base_url = (is_https() ? 'https' : 'http').'://'.$server_addr.'/';
					$url_image = $base_url . $path;

					//$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
					//$url_image = $path;
					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenInicio' => $UploadData["file_name"],
						'sNombreImagenInicioUrl' => $url_image . '/' . $UploadData["file_name"],
						//'sNombreImagenInicio' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
						//'sNombreImagenInicioUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
					'sNombreImagenInicio' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					'sNombreImagenInicioUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
				);
			}
    	}
    	echo json_encode($arrResponse);
	}

    public function removeFileImage(){//CAMBIO AL ELIMINAR IMAGEN AGREGAR SOLO SUBO IMAGEN Y LUEGO BORRABA PERO EN LA CARPETA NO ELIMINA PORQUE TENIA OTRO NOMNBRE
		//$nameFileImage = cambiarCaracteresEspecialesImagen($this->input->post('nameFileImage'));
		$nameFileImage = $this->input->post('nameFileImage');
		if ( strpos($nameFileImage,"/") > 0 ) {
			if ( file_exists($nameFileImage) ){
				unlink($nameFileImage);
				$data = array('No_Imagen_Inicio_Slider' => '', 'No_Imagen_Url_Inicio_Slider' => '');
				$where = array('ID_Ecommerce_Inicio' => $this->input->post('iIdProducto') );
				echo json_encode($this->SliderModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 1'));
			}
		} else {
			$sNombreImagenMasExtension = $this->input->post('nameFileImage');
			$sExtensionNombreImagen = pathinfo($sNombreImagenMasExtension, PATHINFO_EXTENSION);
			
			$str = $sNombreImagenMasExtension;
			$arrNombre=(explode("jpeg",$str));
			$sNombreImagen = substr($arrNombre[0], 0, -1);
	
			$nameFileImage = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/';
			if ( $nameFileImage && file_exists($path . $nameFileImage) ){
				unlink($path . $nameFileImage);
				$data = array('No_Imagen_Inicio_Slider' => '', 'No_Imagen_Url_Inicio_Slider' => '');
				$where = array('ID_Ecommerce_Inicio' => $this->input->post('iIdProducto') );
				echo json_encode($this->SliderModel->actualizarVersionImagen($where, $data));
			} else {
				echo json_encode(array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Imagen no encontrada al eliminar 2'));
			}
		}
    }

	public function get_image(){
		/*
		$sUrlImage = $this->input->post('sUrlImage');
		$arrUrlImage = explode($this->empresa->Nu_Documento_Identidad, $sUrlImage);
		$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . $arrUrlImage[1];
		*/
		$path         = $this->upload_path . $this->empresa->Nu_Documento_Identidad;		
		$server_addr = $_SERVER['HTTP_HOST'];
		$base_url = (is_https() ? 'https' : 'http').'://'.$server_addr.'/';
		$url_image = $base_url . $path;
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
        echo json_encode($this->SliderModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function crudInicio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'Nu_Tipo_Inicio' => $this->input->post('Nu_Tipo_Inicio'),
			'No_Slider' => $this->input->post('No_Slider'),
			'No_Imagen_Inicio_Slider' => $this->input->post('No_Imagen_Inicio_Slider'),
			'No_Imagen_Url_Inicio_Slider' => $this->input->post('No_Imagen_Url_Inicio_Slider'),
			'Nu_Orden_Slider' => $this->input->post('Nu_Orden_Slider'),
			'No_Url_Accion' => $this->input->post('No_Url_Accion'),
			'Nu_Estado_Slider' => $this->input->post('Nu_Estado_Slider'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Ecommerce_Inicio') != '') ?
			$this->SliderModel->actualizarInicio(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Ecommerce_Inicio' => $this->input->post('EID_Ecommerce_Inicio')), $data, $this->input->post('ENo_Slider'))
		:
			$this->SliderModel->agregarInicio($data)
		);
	}
    
	public function eliminarInicio($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SliderModel->eliminarInicio($this->security->xss_clean($ID)));
	}
}
