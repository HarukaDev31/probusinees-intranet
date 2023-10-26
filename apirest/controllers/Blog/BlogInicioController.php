<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BlogInicioController extends CI_Controller {
	private $upload_path = '../assets/images/sliders/';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Blog/BlogInicioModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Blog/BlogInicioView');
			$this->load->view('footer', array("js_blog_inicio" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->BlogInicioModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Slider;
            $rows[] = (file_exists($this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $row->No_Imagen_Inicio_Slider) ? '<img src="' . $row->No_Imagen_Url_Inicio_Slider . '" style="height:50px;"></img>' : 'Sin imagen');
            $rows[] = $row->Nu_Orden_Slider;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \'' . $row->No_Imagen_Inicio_Slider . '\', \'' . $row->No_Imagen_Url_Inicio_Slider . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \''.$action.'\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BlogInicioModel->count_all(),
	        'recordsFiltered' => $this->BlogInicioModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_list_slider_mobile(){
		$arrData = $this->BlogInicioModel->get_datatables_slider_mobile();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Slider;
            $rows[] = (file_exists($this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $row->No_Imagen_Inicio_Slider) ? '<img src="' . $row->No_Imagen_Url_Inicio_Slider . '" style="height:50px;"></img>' : 'Sin imagen');
            $rows[] = $row->Nu_Orden_Slider;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \'' . $row->No_Imagen_Inicio_Slider . '\', \'' . $row->No_Imagen_Url_Inicio_Slider . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarInicio(\'' . $row->ID_Ecommerce_Inicio . '\', \''.$action.'\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BlogInicioModel->count_all_slider_mobile(),
	        'recordsFiltered' => $this->BlogInicioModel->count_filtered_slider_mobile(),
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
				$config['allowed_types'] = 'png|jpg|jpeg|webp';
				$config['max_size'] = 1024;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén',
						'sClassModal' => 'modal-danger',
					);
				} else {
					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Ecommerce_Inicio' => $this->input->post('iIdEcommerceInicio') );
					$this->BlogInicioModel->actualizarVersionImagen($where, $data);

					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenInicio' => $_FILES["file"]["name"],
						'sNombreImagenInicioUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
					'sNombreImagenInicio' => $_FILES["file"]["name"],
					'sNombreImagenInicioUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
        echo json_encode($this->BlogInicioModel->get_by_id($this->security->xss_clean($ID)));
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
			$this->BlogInicioModel->actualizarInicio(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Ecommerce_Inicio' => $this->input->post('EID_Ecommerce_Inicio')), $data, $this->input->post('ENo_Slider'))
		:
			$this->BlogInicioModel->agregarInicio($data)
		);
	}
    
	public function eliminarInicio($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->BlogInicioModel->eliminarInicio($this->security->xss_clean($ID)));
	}
}
