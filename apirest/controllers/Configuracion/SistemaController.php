<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SistemaController extends CI_Controller {
	//private $upload_path = '../assets/images/logos';
	private $upload_path = 'assets/images/logos';
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Configuracion/SistemaModel');
		$this->load->model('HelperModel');
	}
	
	public function listarConfiguraciones(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Configuracion/SistemaView');
			$this->load->view('footer', array("js_sistema_formato_ordenes" => true));
		}
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
				$rows[] = $row->Ss_Total_Pago_Cliente_Servicio;
				$rows[] = ToDateBD($row->Fe_Inicio_Sistema);
			}
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verSistema(\'' . $row->ID_Configuracion . '\', \'' . $row->No_Logo_Empresa . '\', \'' . $row->No_Imagen_Logo_Empresa . '\', \'' . $row->Nu_Version_Imagen . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
			$rows[] = (!empty($row->No_Imagen_Logo_Empresa) ? '<img src="' . $row->No_Imagen_Logo_Empresa . '" style="height:50px;"></img>' : '<span class="label label-danger">Sin logo</span>');
            $rows[] = $row->No_Dominio_Empresa;
            $rows[] = $row->Nu_Celular_Empresa;
			$rows[] = $row->Txt_Email_Empresa;
            $rows[] = '<span class="label label-' . ($row->Nu_Validar_Stock == 1 ? 'success' : 'danger') . '" data-toggle="tooltip" data-placement="bottom" title="Al elegir (SI), solo se podrá vender si su stock es > 0, si eligen (NO) entonces podrán vender con stock en negativo <= 0">' . ($row->Nu_Validar_Stock == 1 ? 'Vender con Stock' : 'Vender sin Stock') . '</span>';
            
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarSistema(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Configuracion . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>';
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
			$path = $this->upload_path;
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
				$config['max_size'] = 1024;
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
					//$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
	$server_addr = $_SERVER['HTTP_HOST'];
					$base_url = (is_https() ? 'https' : 'http').'://'.$server_addr.'/';
					$url_image = $base_url . $path;
					//$url_image = $path;
					$arrResponse = array(
						'sStatus' => 'success',
						'sMessage' => 'imagén guardada',
						'sClassModal' => 'modal-success',
						'sNombreImagenLogoEmpresa' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
						'sNombreImagenLogoEmpresaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					);
				}
			} else {
				$arrUrlImagePath = explode('..', $path);
				$arrUrlImage = explode('/principal',base_url());
				//$url_image = $arrUrlImage[0] . $arrUrlImagePath[1];
				$url_image = $path;

				$arrResponse = array(
					'sStatus' => 'success',
					'sMessage' => 'La imagen ya fue guardada',
					'sClassModal' => 'modal-success',
					'sNombreImagenLogoEmpresa' => cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
					'sNombreImagenLogoEmpresaUrl' => $url_image . '/' . cambiarCaracteresEspecialesImagen($sNombreImagen) . '.' . $sExtensionNombreImagen,
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
		$path = $this->upload_path . '/' . $this->input->post('sNombreImage');
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

		$No_Foto_Boleta='';
		if ($this->input->post('ENo_Foto_Boleta') != '')
			$No_Foto_Boleta = $this->input->post('ENo_Foto_Boleta');
		else
			$No_Foto_Boleta = ($_FILES['No_Foto_Boleta']['name'] != '' ? $_FILES['No_Foto_Boleta']['name'] : '');

		$No_Foto_Factura='';
		if ($this->input->post('ENo_Foto_Factura') != '')
			$No_Foto_Factura = $this->input->post('ENo_Foto_Factura');
		else
			$No_Foto_Factura = ($_FILES['No_Foto_Factura']['name'] != '' ? $_FILES['No_Foto_Factura']['name'] : '');

		$No_Foto_NCredito='';
		if ($this->input->post('ENo_Foto_NCredito') != '')
			$No_Foto_NCredito = $this->input->post('ENNo_Foto_NCredito');
		else
			$No_Foto_NCredito = ($_FILES['No_Foto_NCredito']['name'] != '' ? $_FILES['No_Foto_NCredito']['name'] : '');

		$No_Foto_Guia='';
		if ($this->input->post('ENo_Foto_Guia') != '')
			$No_Foto_Guia = $this->input->post('ENo_Foto_Guia');
		else
			$No_Foto_Guia = ($_FILES['No_Foto_Guia']['name'] != '' ? $_FILES['No_Foto_Guia']['name'] : '');

		$data = array(
			'ID_Empresa'				=> $this->input->post('ID_Empresa'),
			'Fe_Inicio_Sistema'			=> ToDate($this->input->post('Fe_Inicio_Sistema')),
			'Nu_Enviar_Sunat_Automatic' => $this->input->post('Nu_Enviar_Sunat_Automatic'),
			'Nu_Dia_Limite_Fecha_Vencimiento' => $this->input->post('Nu_Dia_Limite_Fecha_Vencimiento'),
			'Nu_Logo_Empresa_Ticket' => $this->input->post('Nu_Logo_Empresa_Ticket'),
			'Nu_Height_Logo_Ticket'	=> $this->input->post('Nu_Height_Logo_Ticket'),
			'Nu_Width_Logo_Ticket'	=> $this->input->post('Nu_Width_Logo_Ticket'),
			'Nu_Imprimir_Liquidacion_Caja'	=> $this->input->post('Nu_Imprimir_Liquidacion_Caja'),
			'Nu_Precio_Punto_Venta'	=> $this->input->post('Nu_Precio_Punto_Venta'),
			'Nu_Tipo_Rubro_Empresa'	=> $this->input->post('Nu_Tipo_Rubro_Empresa'),
			'Nu_Verificar_Autorizacion_Venta' => $this->input->post('Nu_Verificar_Autorizacion_Venta'),
			'Nu_Activar_Descuento_Punto_Venta' => $this->input->post('Nu_Activar_Descuento_Punto_Venta'),
			'Nu_Validar_Stock'			=> $this->input->post('Nu_Validar_Stock'),
			'Nu_Estado'					=> $this->input->post('Nu_Estado'),
			'Txt_Token'					=> $this->input->post('Txt_Token'),
			'No_Dominio_Empresa'		=> $this->input->post('No_Dominio_Empresa'),
			'Txt_Email_Empresa'			=> $this->input->post('Txt_Email_Empresa'),
			'Nu_Celular_Empresa'		=> $this->input->post('Nu_Celular_Empresa'),
			'Nu_Telefono_Empresa'		=> $this->input->post('Nu_Telefono_Empresa'),
			'Txt_Slogan_Empresa'		=> $this->input->post('Txt_Slogan_Empresa'),
			'Txt_Terminos_Condiciones_Ticket' => nl2br($this->input->post('Txt_Terminos_Condiciones_Ticket')),
			'Txt_Terminos_Condiciones' => nl2br($this->input->post('Txt_Terminos_Condiciones')),
			'Txt_Cuentas_Bancarias'	=> nl2br($this->input->post('Txt_Cuentas_Bancarias')),
			'Txt_Nota'					=> nl2br($this->input->post('Txt_Nota')),
			'No_Foto_Boleta'			=> $No_Foto_Boleta,
			'No_Foto_Factura'			=> $No_Foto_Factura,
			'No_Foto_NCredito'			=> $No_Foto_NCredito,
			'No_Foto_Guia'				=> $No_Foto_Guia,
			'No_Logo_Empresa'			=> $this->input->post('hidden-nombre_logo'),
			'No_Imagen_Logo_Empresa' => $this->input->post('No_Imagen_Logo_Empresa'),
			'Ss_Total_Pago_Cliente_Servicio' => $this->input->post('Ss_Total_Pago_Cliente_Servicio'),
			'Nu_Activar_Detalle_Una_Linea_Ticket' => $this->input->post('Nu_Activar_Detalle_Una_Linea_Ticket'),
			'Nu_ID_Tipo_Documento_Venta_Predeterminado' => $this->input->post('Nu_ID_Tipo_Documento_Venta_Predeterminado'),
			'Nu_Cliente_Varios_Venta_Predeterminado' => $this->input->post('Nu_Cliente_Varios_Venta_Predeterminado'),
			'Nu_Tipo_Lenguaje_Impresion_Pos' => $this->input->post('Nu_Tipo_Lenguaje_Impresion_Pos'),
			'No_Predeterminado_Formato_PDF_POS' => $this->input->post('No_Predeterminado_Formato_PDF_POS'),
			'Nu_Activar_Redondeo' => $this->input->post('Nu_Activar_Redondeo'),
			'Txt_Cuenta_Banco_Detraccion'	=> nl2br($this->input->post('Txt_Cuenta_Banco_Detraccion')),
			'Nu_Imprimir_Columna_Ticket_Detalle' => $this->input->post('Nu_Imprimir_Columna_Ticket_Detalle'),
			'Nu_Tipo_Vender_Usuario_POS' => $this->input->post('Nu_Tipo_Vender_Usuario_POS')
		);
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Configuracion') != '' ) ?
			$this->SistemaModel->actualizarSistema(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Configuracion' => $this->input->post('EID_Configuracion')), $data, $this->input->post('ENo_Dominio_Empresa'), $_FILES)
		:
			$this->SistemaModel->agregarSistema($data, $_FILES)
		);
	}
    
	public function eliminarSistema($ID_Empresa, $ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->SistemaModel->eliminarSistema($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID)));
	}
}
