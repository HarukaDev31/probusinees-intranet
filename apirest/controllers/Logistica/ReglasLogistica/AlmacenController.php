<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlmacenController extends CI_Controller {
	private $upload_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/AlmacenModel');
		$this->load->model('HelperModel');
	}

	public function listarAlmacenes(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Logistica/ReglasLogistica/AlmacenView');
			$this->load->view('footer', array("js_almacen" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->AlmacenModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			//$rows[] = '<span class="label label-' . ($row->Nu_Estado_Sistema == 1 ? 'success' : 'danger') . '">' . ($row->Nu_Estado_Sistema == 1 ? 'Producción' : 'Demostración') . '</span>';
			//$rows[] = '<span class="label label-' . $row->No_Class_Proveedor_FE . '">' . $row->No_Descripcion_Proveedor_FE . '</span>';

			//$rows[] = (!empty($row->No_Logo_Url_Almacen) ? '<img src="' . $row->No_Logo_Url_Almacen . '" style="height:50px;"></img>' : '<span class="label label-danger">Sin logo</span>');
			$rows[] = $row->No_Empresa;
			$rows[] = $row->No_Organizacion;
            $rows[] = $row->No_Almacen;
			$rows[] = $row->No_Departamento;
			$rows[] = $row->No_Provincia;
			$rows[] = $row->No_Distrito;
            $rows[] = $row->Txt_Direccion_Almacen;
			
			/*
			if ( $this->user->No_Usuario == 'root' ){
				//estado pago
				$dropdown = '<div class="dropdown">
					<button class="btn btn-' . ($row->Nu_Estado_Pago_Sistema == 0 ? 'danger' : 'success') . ' dropdown-toggle" type="button" data-toggle="dropdown">' . ($row->Nu_Estado_Pago_Sistema == 1 ? 'Cancelado' : 'Pendiente') . '
					<span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a alt="Cancelado" title="Cancelado" href="javascript:void(0)" onclick="cambiarEstadoPago(\'' . $row->ID_Almacen . '\',1);">Cancelado</a></li>
						<li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoPago(\'' . $row->ID_Almacen . '\',0);">Pendiente</a></li>
					</ul>
				</div>';
				$rows[] = $dropdown;
            	$rows[] = $row->Fe_Vencimiento_LaeGestion;


				if($row->Nu_Estado_Pago_Sistema_Laeshop == 0) {
					$sClassPagoLaeShop = 'danger';//pendiente de pago no cancelado por cliente
					$sNombrePagoLaeShop = 'Pendiente';//pendiente de pago no cancelado por cliente
				}else if($row->Nu_Estado_Pago_Sistema_Laeshop == 1){
					$sClassPagoLaeShop = 'success';//pago cancelado
					$sNombrePagoLaeShop = 'Cancelado';//pago cliente
			 	}
				*/
				/*else if($row->Nu_Estado_Pago_Sistema_Laeshop == 2){
					$sClassPagoLaeShop = 'warning';//pendiente de pago
					$sNombrePagoLaeShop = 'Pendiente - Por pagar';//pendiente pago de cliente
				}*/
				/*

				//estado pago
				$dropdown_laeshop = '<div class="dropdown">
					<button class="btn btn-' . $sClassPagoLaeShop . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $sNombrePagoLaeShop . '
					<span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a alt="Cancelado" title="Cancelado" href="javascript:void(0)" onclick="cambiarEstadoPagoLaeshop(\'' . $row->ID_Almacen . '\',1);">Cancelado</a></li>
						<li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoPagoLaeshop(\'' . $row->ID_Almacen . '\',0);">Pendiente</a></li>
					</ul>
				</div>';
				$rows[] = $dropdown_laeshop;
            	$rows[] = $row->Fe_Vencimiento_Laeshop;
			}// if - root
			*/

			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
			$rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			if ( $this->user->No_Usuario == 'root' ){
				if($row->Nu_Estado_Pago_Sistema_Laeshop == 0) {
					$sClassPagoLaeShop = 'danger';//pendiente de pago no cancelado por cliente
					$sNombrePagoLaeShop = 'Pendiente';//pendiente de pago no cancelado por cliente
				}else if($row->Nu_Estado_Pago_Sistema_Laeshop == 1){
					$sClassPagoLaeShop = 'success';//pago cancelado
					$sNombrePagoLaeShop = 'Cancelado';//pago cliente
			 	}

				//estado pago
				$dropdown_laeshop = '<div class="dropdown">
					<button class="btn btn-' . $sClassPagoLaeShop . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $sNombrePagoLaeShop . ' <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a alt="Cancelado" title="Cancelado" href="javascript:void(0)" onclick="cambiarEstadoPagoLaeshop(\'' . $row->ID_Almacen . '\',1);">Cancelado</a></li>
						<li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoPagoLaeshop(\'' . $row->ID_Almacen . '\',0);">Pendiente</a></li>
					</ul>
				</div>';
				$rows[] = $dropdown_laeshop;
				$rows[] = $row->Fe_Vencimiento_Laeshop;
			}

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verAlmacen(\'' . $row->ID_Almacen . '\', \'' . $row->No_Logo_Almacen . '\', \'' . $row->No_Logo_Url_Almacen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarAlmacen(\'' . $row->ID_Almacen . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->AlmacenModel->count_all(),
	        'recordsFiltered' => $this->AlmacenModel->count_filtered(),
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
				$config['allowed_types'] = 'png|jpg|jpeg';
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
						'sNombreImagenAlmacen' => $_FILES["file"]["name"],
						'sNombreImagenAlmacenUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
					'sNombreImagenAlmacen' => $_FILES["file"]["name"],
					'sNombreImagenAlmacenUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($_FILES['file']['name']),
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
        echo json_encode($this->AlmacenModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudAlmacen(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Organizacion' => $this->input->post('ID_Oganizacion'),
			'No_Almacen' => $this->input->post('No_Almacen'),
			'Nu_Codigo_Establecimiento_Sunat' => $this->input->post('Nu_Codigo_Establecimiento_Sunat'),
			'Txt_Direccion_Almacen'	=> $this->input->post('Txt_Direccion_Almacen'),
			'ID_Pais' => $this->input->post('ID_Pais'),
			'ID_Departamento' => $this->input->post('ID_Departamento'),
			'ID_Provincia' => $this->input->post('ID_Provincia'),
			'ID_Distrito' => $this->input->post('ID_Distrito'),
			'Nu_Estado_Pago_Sistema' => $this->input->post('Nu_Estado_Pago_Sistema'),
			'Nu_Estado_Pago_Sistema_Laeshop' => $this->input->post('Nu_Estado_Pago_Sistema_Laeshop'),
			'Nu_Estado'	=> $this->input->post('Nu_Estado'),
			'Txt_FE_Ruta' => $this->input->post('Txt_FE_Ruta'),
			'Txt_FE_Token' => $this->input->post('Txt_FE_Token'),
			'Nu_Latitud_Maps' => $this->input->post('Nu_Latitud_Maps'),
			'Nu_Longitud_Maps' => $this->input->post('Nu_Longitud_Maps'),
			'No_Logo_Almacen' => $this->input->post('No_Logo_Almacen'),
			'No_Logo_Url_Almacen' => $this->input->post('No_Logo_Url_Almacen'),
			'Txt_Ruta_Lae_Shop' => $this->input->post('Txt_Ruta_Lae_Shop'),
			'Txt_Token_Lae_Shop' => $this->input->post('Txt_Token_Lae_Shop'),
			'ID_Ubigeo_Inei_Partida' => $this->input->post('ID_Ubigeo_Inei_Partida')
		);

		$dVencimientoLaeGestion = $this->input->post('Fe_Vencimiento_LaeGestion');
		$arrFechaVencimientoLaeGestion = explode('/', $dVencimientoLaeGestion);		
		$dVencimientoLaeGestion = dateNow('fecha');
		if($this->user->No_Usuario == 'root' && count($arrFechaVencimientoLaeGestion) == 3 && checkdate($arrFechaVencimientoLaeGestion[1], $arrFechaVencimientoLaeGestion[0], $arrFechaVencimientoLaeGestion[2])) {
			$data = array_merge($data, array("Fe_Vencimiento_LaeGestion" => ToDate($this->input->post('Fe_Vencimiento_LaeGestion'))));
		}

		$dVencimiento = $this->input->post('Fe_Vencimiento_Laeshop');
		$arrFechaVencimiento = explode('/', $dVencimiento);		
		$dVencimiento = dateNow('fecha');
		if($this->user->No_Usuario == 'root' && count($arrFechaVencimiento) == 3 && checkdate($arrFechaVencimiento[1], $arrFechaVencimiento[0], $arrFechaVencimiento[2])) {
			$data = array_merge($data, array("Fe_Vencimiento_Laeshop" => ToDate($this->input->post('Fe_Vencimiento_Laeshop'))));
		}

		echo json_encode(
		($this->input->post('EID_Organizacion') != '' && $this->input->post('EID_Almacen') != '') ?
			$this->AlmacenModel->actualizarAlmacen(array('ID_Organizacion' => $this->input->post('EID_Organizacion'), 'ID_Almacen' => $this->input->post('EID_Almacen')), $data, $this->input->post('ENo_Almacen'))
		:
			$this->AlmacenModel->agregarAlmacen($data)
		);
	}
    
	public function eliminarAlmacen($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->AlmacenModel->eliminarAlmacen($this->security->xss_clean($ID)));
	}
	
	public function cambiarEstadoPago($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->AlmacenModel->cambiarEstadoPago($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
	
	public function cambiarEstadoPagoLaeshop($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->AlmacenModel->cambiarEstadoPagoLaeshop($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
}
