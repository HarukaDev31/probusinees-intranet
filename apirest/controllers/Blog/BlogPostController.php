<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class BlogPostController extends CI_Controller {
	
	private $upload_path = '../assets/images/blog_posts/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Blog/BlogPostModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Blog/BlogPostView');
			$this->load->view('footer', array("js_blog_post" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->BlogPostModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
        	settype($row->Qt_Producto, "double");
            $no++;
            $rows = array();
            $rows[] = $row->No_Tag_Blog;
            $rows[] = $row->No_Titulo_Blog;
            $rows[] = '<span class="label label-' . $row->No_Class_Estado . '">' . $row->No_Descripcion_Estado . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verBlogPost(\'' . $row->ID_Post_Blog . '\')"><i class="fa fa-pencil" aria-hidden="true"> Modificar</i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPostBlog(\'' . $row->ID_Post_Blog . '\', \'' . $action . '\')"><i class="fa fa-trash-o" aria-hidden="true"> Eliminar</i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->BlogPostModel->count_all(),
	        'recordsFiltered' => $this->BlogPostModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
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
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp';
				$config['max_size'] = 400;//400 KB
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')){
					$arrResponse = array(
						'sStatus' => 'error',
						'sMessage' => strip_tags($this->upload->display_errors()) . ' No se guardó imagén' . $path,
						'sClassModal' => 'modal-danger',
					);
				} else {
					$arrUrlImagePath = explode('..', $path);
					$arrUrlImage = explode('/principal',base_url());
					$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];

					$ID_Ruta_Enlace_Gallery = 1;//1 = Blog post, 2 = Logistica producto
					$sFileName = cambiarCaracteresEspecialesImagen($_FILES["file"]["name"]);
					$No_Imagen_Gallery = $sFileName;
					$No_Url_Imagen_Gallery = $url_image . '/' . $sFileName;

					$data = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Ruta_Enlace_Gallery' => $ID_Ruta_Enlace_Gallery,
						'No_Imagen_Gallery' => $No_Imagen_Gallery,
						'No_Url_Imagen_Gallery' => $No_Url_Imagen_Gallery,
						'Nu_Version_Imagen_Gallery' => 1,
						'Nu_Estado' => 1,
					);
					echo json_encode($this->BlogPostModel->agregarImagenGallery($data));
					exit();
				}
			} else {
				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];		
				$sFileName = cambiarCaracteresEspecialesImagen($_FILES["file"]["name"]);		
				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'La imagen ya fue guardada',
					'sClassModal' => 'modal-success',
					'iLastIdGallery' => 0,
					'sNombreImagenGallery' => $sFileName,
					'sNombreImagenGalleryUrl' => $url_image . '/' . $sFileName,
				);
			}
    	}
    	echo json_encode($arrResponse);
    }
    
    public function removeAddFileImage(){
		$sRutaFileName = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . cambiarCaracteresEspecialesImagen($this->input->post('file_name'));//No se debe de usar URL para eliminar file
		if ( file_exists($sRutaFileName) )
    		unlink($sRutaFileName);
    }
    
    public function removeFileImage(){
		$arrResponse = $this->BlogPostModel->deleteImagenGallery( array('ID_Gallery' => $this->input->post('id_image')) );
		if ( $arrResponse['sStatus'] == 'success' ) {
			if ( file_exists($this->input->post('file_name')) )
				unlink($this->input->post('file_name'));
		}
    }
	
	public function get_image(){
		$arrResponse = $this->BlogPostModel->getGalleryImage($this->input->post('iIdRelacionGallery'));
		if ( $arrResponse['sStatus'] == 'success' ) {
			foreach ($arrResponse['arrData'] as $row){
				$path = $this->upload_path . $this->empresa->Nu_Documento_Identidad . '/' . $row->No_Imagen_Gallery;//Se usuará para eliminar la imagen
				
				if ( file_exists($path) ){
					$arrfilesImages[] = array(
						'name' => $row->No_Url_Imagen_Gallery,//Se necesita enviar la ruta completa del archivo es decir; ruta + nombre de imagen
						'size' => filesize($path),
						'file_name' => $path,
						'id_image' => $row->ID_Gallery,
					);
				}
			}
			echo json_encode(array('sStatus' => 'success', 'arrfilesImages' => $arrfilesImages));
		} else {
			echo json_encode($arrResponse);
		}
	}
	
	public function ajax_edit($ID){
        echo json_encode($this->BlogPostModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPostBlog(){
		if (!$this->input->is_ajax_request()) exit('No se puede Agregar/Editar y acceder');
		
		$data = $this->input->post();
		$data['ID_Empresa'] = $this->empresa->ID_Empresa;
		echo json_encode(( $data['EID_Post_Blog'] != '' ) ? $this->BlogPostModel->actualizarBlogPost(array('ID_Post_Blog' => $data['EID_Post_Blog']), $data) : $this->BlogPostModel->agregarBlogPost($data) );
	}
    
	public function eliminarPostBlog($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->BlogPostModel->eliminarPostBlog($this->security->xss_clean($ID)));
	}
}
