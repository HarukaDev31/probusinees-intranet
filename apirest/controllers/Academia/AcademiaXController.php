<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AcademiaXController extends CI_Controller {

	private $upload_path = '../assets/images/marcas/';
	private $upload_path_table = '../assets/images/marcas';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Academia/AcademiaXModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('Academia/AcademiaXView');
			$this->load->view('footer', array("js_academiax" => true));
		}
	}
	
    public function uploadMultiple(){
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
				$config['max_size'] = 400;//400 KB
				$config['file_name'] = cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén' . $path,
						'sClassModal' => 'modal-danger',
					);
				} else {
					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Marca' => $this->input->post('iIdProducto') );
					$this->AcademiaXModel->actualizarVersionImagen($where, $data);

					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenItem' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
					'sNombreImagenItem' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
				$where = array('ID_Marca' => $this->input->post('iIdProducto') );
				echo json_encode($this->AcademiaXModel->actualizarVersionImagen($where, $data));
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
				$data = array('Txt_Url_Logo_Lae_Shop' => '');
				$where = array('ID_Marca' => $this->input->post('iIdProducto') );
				echo json_encode($this->AcademiaXModel->actualizarVersionImagen($where, $data));
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

	public function ajax_list(){
		$arrData = $this->AcademiaXModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
			$arrImgProducto = explode('marcas',$row->Txt_Url_Logo_Lae_Shop);
			$sPathImgProducto='';
			if(isset($arrImgProducto[1]))
				$sPathImgProducto = $this->upload_path_table . $arrImgProducto[1];

			$image='';
			if(!empty($sPathImgProducto) && !empty($row->Txt_Url_Logo_Lae_Shop)){
				if ( file_exists($sPathImgProducto) ) {
					$img_binary = fread(fopen($sPathImgProducto, "r"), filesize($sPathImgProducto));
					$base64 = 'data:image/png;base64, ' . base64_encode($img_binary);
					$image = '<img class="img-fluid" data-url_img="' . $row->Txt_Url_Logo_Lae_Shop . '" src="' . $base64 . '" title="' . $row->No_Marca . '" alt="' . $row->No_Marca . '" style="cursor:pointer; max-height:40px;" />';
				}
			}

            $no++;
            $rows = array();
            $rows[] = $row->Nu_Orden;
            $rows[] = $row->No_Marca;
			$rows[] = !empty($image) ? $image : 'Sin imagen';
			
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMarca(\'' . $row->ID_Marca . '\', \'' . $row->Txt_Url_Logo_Lae_Shop . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarMarca(\'' . $row->ID_Marca . '\', \'' . $action . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->AcademiaXModel->count_all(),
	        'recordsFiltered' => $this->AcademiaXModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->AcademiaXModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMarca(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		
		$sUrlProductoImagen = '';
		if ( !empty($this->input->post('Txt_Url_Logo_Lae_Shop')) ) {
			$sUrlProductoImagen = $this->input->post('Txt_Url_Logo_Lae_Shop');
		}

		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'No_Marca'	=> $this->input->post('No_Marca'),
			'Nu_Orden' => $this->input->post('Nu_Orden'),
			'Nu_Activar_Marca_Lae_Shop'	=> $this->input->post('Nu_Estado'),
			'Txt_Url_Logo_Lae_Shop' => $sUrlProductoImagen,
		);
		echo json_encode(
		($this->input->post('EID_Marca') != '') ?
			$this->AcademiaXModel->actualizarMarca(array('ID_Marca' => $this->input->post('EID_Marca')), $data, $this->input->post('ENo_Marca'))
		:
			$this->AcademiaXModel->agregarMarca($data)
		);
	}
    
	public function eliminarMarca($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->AcademiaXModel->eliminarMarca($this->security->xss_clean($ID)));
	}
}
