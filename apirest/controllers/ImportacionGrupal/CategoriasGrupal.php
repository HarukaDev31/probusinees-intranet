<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriasGrupal extends CI_Controller {
	private $upload_path = 'assets/images/categorias/';//aqui cambie y agregue carpeta
	private $upload_path_table = '../assets/images/categorias';
	private $upload_path_table_v2 = '../../../assets/images/categorias';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImportacionGrupal/CategoriaModel');
		$this->load->model('HelperDropshippingModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('ImportacionGrupal/CategoriaView');
			$this->load->view('footer_v2', array("js_categoria_importacion_grupal" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->CategoriaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
			$arrImgProducto = explode('categorias',$row->No_Imagen_Url_Categoria);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table_v2 . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->No_Imagen_Url_Categoria)){
				//if ( file_exists($sPathImgProducto) ) {
					//$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					//$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $sPathImgProducto . '" src="' . $sPathImgProducto . '" title="' . $row->No_Familia . '" alt="' . $row->No_Familia . '" style="cursor:pointer; max-height:40px;" />';
				//}
			}

            $no++;
            $rows = array();

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCategoria(\'' . $row->ID_Familia . '\', \'' . $row->No_Imagen_Categoria . '\', \'' . $row->No_Imagen_Url_Categoria . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $rows[] = $row->Nu_Orden;
            $rows[] = $row->No_Familia;
			$rows[] = !empty($image) ? $image : 'Sin imagen';
			
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Activar_Familia_Lae_Shop);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCategoria(\'' . $row->ID_Familia . '\', \''.$action.'\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
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
				$config['max_size'] = 1024;//400 KB
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
					$where = array('ID_Familia' => $this->input->post('iIdFamilia') );
					$this->CategoriaModel->actualizarVersionImagen($where, $data);

					/*
					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
					*/

					$server_addr = $_SERVER['HTTP_HOST'];
					$base_url = (is_https() ? 'https' : 'http').'://'.$server_addr.'/';
					$url_image = $base_url . $path;

					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenCategoria' => $UploadData["file_name"],
						'sNombreImagenCategoriaUrl' => $url_image . '/' . $UploadData["file_name"],
						//'sNombreImagenCategoria' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
						//'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
				$data = array('No_Imagen_Url_Categoria' => '');
				$where = array('ID_Familia' => $this->input->post('iIdProducto') );
				echo json_encode($this->CategoriaModel->actualizarVersionImagen($where, $data));
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
				$data = array('No_Imagen_Url_Categoria' => '');
				$where = array('ID_Familia' => $this->input->post('iIdProducto') );
				echo json_encode($this->CategoriaModel->actualizarVersionImagen($where, $data));
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
        echo json_encode($this->CategoriaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCategoria(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'Nu_Orden' => $this->input->post('Nu_Orden'),
			'No_Familia' => $this->input->post('No_Familia'),
			'No_Imagen_Categoria' => $this->input->post('No_Imagen_Categoria'),
			'No_Imagen_Url_Categoria' => $this->input->post('No_Imagen_Url_Categoria'),
			'Nu_Activar_Familia_Lae_Shop' => $this->input->post('Nu_Estado'),
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Familia') != '') ?
			$this->CategoriaModel->actualizarCategoria(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Familia' => $this->input->post('EID_Familia')), $data, $this->input->post('ENo_Familia'), $this->input->post('ENu_Orden'))
		:
			$this->CategoriaModel->agregarCategoria($data)
		);
	}
    
	public function eliminarCategoria($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->CategoriaModel->eliminarCategoria($this->security->xss_clean($ID)));
	}
}
