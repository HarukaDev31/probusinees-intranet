<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriaController extends CI_Controller {
	private $upload_path = '../assets/images/categorias/';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/CategoriaModel');
		$this->load->model('HelperModel');
	}
	
	public function listarCategorias(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/CategoriaView');
			$this->load->view('footer', array("js_categoria" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->CategoriaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action='delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Familia;
            $rows[] = $row->Nu_Orden;
			$rows[] = '<div style="padding: 2%; background-color: #' . $row->No_Html_Color . ';"></div>';
            
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			//$rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCategoria(\'' . $row->ID_Familia . '\', \'' . $row->No_Imagen_Categoria . '\', \'' . $row->No_Imagen_Url_Categoria . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link delete" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCategoria(\'' . $row->ID_Familia . '\', \''.$action.'\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->CategoriaModel->count_all(),
	        'recordsFiltered' => $this->CategoriaModel->count_filtered(),
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
					$data = array('Nu_Version_Imagen' => $this->input->post('iVersionImage'));
					$where = array('ID_Familia' => $this->input->post('iIdFamilia') );
					$this->CategoriaModel->actualizarVersionImagen($where, $data);

					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenCategoria' => $_FILES["file"]["name"],
						'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
					'sNombreImagenCategoria' => $_FILES["file"]["name"],
					'sNombreImagenCategoriaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
			}
		} else {
			$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/';
			if ( $nameFileImage && file_exists($path . $nameFileImage) ){
				unlink($path . $nameFileImage);
			}
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
        echo json_encode($this->CategoriaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCategoria(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'Nu_Orden' => $this->input->post('Nu_Orden'),
			'No_Familia' => $this->input->post('No_Familia'),
			'No_Html_Color' => $this->input->post('No_Html_Color'),
			'No_Familia_Breve' => $this->input->post('No_Familia_Breve'),
			'No_Imagen_Categoria' => cambiarCaracteresEspecialesImagen($this->input->post('No_Imagen_Categoria')),
			'No_Imagen_Url_Categoria' => cambiarCaracteresEspecialesImagen($this->input->post('No_Imagen_Url_Categoria')),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
			'Nu_Imprimir_Comanda_Restaurante' => $this->input->post('Nu_Imprimir_Comanda_Restaurante'),
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
