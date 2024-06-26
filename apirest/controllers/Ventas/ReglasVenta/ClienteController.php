<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClienteController extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/ReglasVenta/ClienteModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function importarExcelCliente(){
		if (isset($_FILES['excel-archivo_cliente']['name']) && isset($_FILES['excel-archivo_cliente']['type']) && isset($_FILES['excel-archivo_cliente']['tmp_name'])) {
		    $archivo	= $_FILES['excel-archivo_cliente']['name'];
		    $tipo		= $_FILES['excel-archivo_cliente']['type'];
		    $destino	= "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo_cliente']['tmp_name'], $destino)) {
		        if (file_exists($destino)) {
					$this->load->library('Excel');
		    		$objReader = new PHPExcel_Reader_Excel2007();
		    		$objPHPExcel = $objReader->load($destino);
		            $objPHPExcel->setActiveSheetIndex(0);
		            
		            $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		            
		            $column = array(
		                'TIPO_DOCUMENTO_IDENTIDAD'		=> 'A',
		                'NUMERO_DOCUMENTO_IDENTIDAD'	=> 'B',
		                'NOMBRE'						=> 'C',
		                'DIRECCION'						=> 'D',
		                'TELEFONO'						=> 'E',
		                'CELULAR'						=> 'F',
		                'EMAIL'							=> 'G',
		                'NOMBRE_CONTACTO'				=> 'H',
		                'CELULAR_TELEFONO_CONTACTO'		=> 'I',
		                'EMAIL_CONTACTO'				=> 'J',
		                'PAIS'							=> 'K',
		                'DEPARTAMENTO'					=> 'L',
		                'PROVINCIA'						=> 'M',
		                'DISTRTIO'						=> 'N',
		                'DESCRIPCION'					=> 'O',
		                'DIAS_CREDITO'					=> 'P',
		                'AGENTE_COMPRA'					=> 'Q',
		                'CARGA_CONSOLIDADA'				=> 'R',
		                'IMPORTACION_GRUPAL'			=> 'S',
		                'CURSO'							=> 'T',
		                'VIAJE_CHINA'					=> 'U',
		            );
	                
	                $arrCliente = array();
	                $iCantidadNoProcesados = 0;
                	for ($i = 2; $i <= $iCantidadRegistros; $i++) {
	                	$ID_Tipo_Documento_Identidad = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TIPO_DOCUMENTO_IDENTIDAD'] . $i)->getCalculatedValue()));
                        
	                	$Nu_Documento_Identidad = $objPHPExcel->getActiveSheet()->getCell($column['NUMERO_DOCUMENTO_IDENTIDAD'] . $i)->getCalculatedValue();
	                	$Nu_Documento_Identidad = strtoupper(filter_var(trim($Nu_Documento_Identidad)));
	                	
	                	$No_Entidad = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['NOMBRE'] . $i)->getCalculatedValue()));
                        $No_Entidad = str_replace("'", "\'", $No_Entidad);
                        
	                	$Txt_Direccion_Entidad = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['DIRECCION'] . $i)->getCalculatedValue()));
                        $Txt_Direccion_Entidad = str_replace("'", "\'", $Txt_Direccion_Entidad);
                        
	                	$Nu_Telefono_Entidad	= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TELEFONO'] . $i)->getCalculatedValue()));
	                	$Nu_Celular_Entidad 	= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CELULAR'] . $i)->getCalculatedValue()));
	                	$Txt_Email_Entidad		= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['EMAIL'] . $i)->getCalculatedValue()));
                        
	                	$No_Contacto			= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['NOMBRE_CONTACTO'] . $i)->getCalculatedValue()));
                        $No_Contacto			= str_replace("'", "\'", $No_Contacto);
                        $Nu_Celular_Contacto	= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CELULAR_TELEFONO_CONTACTO'] . $i)->getCalculatedValue()));
	                	$Txt_Email_Contacto		= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['EMAIL_CONTACTO'] . $i)->getCalculatedValue()));
                        
	                	$No_Pais			= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PAIS'] . $i)->getCalculatedValue()));
	                	$No_Departamento	= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['DEPARTAMENTO'] . $i)->getCalculatedValue()));
	                	$No_Provincia		= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['PROVINCIA'] . $i)->getCalculatedValue()));
	                	$No_Distrito		= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['DISTRTIO'] . $i)->getCalculatedValue()));
	                	
	                	$Txt_Descripcion = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['DESCRIPCION'] . $i)->getCalculatedValue()));
                        $Txt_Descripcion = str_replace("'", "\'", $Txt_Descripcion);
                        
	                	$sDiasCredito = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['DIAS_CREDITO'] . $i)->getCalculatedValue()));
                        $sDiasCredito = str_replace("'", "\'", $sDiasCredito);
                        
	                	$iEstadoAgenteCompra = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['AGENTE_COMPRA'] . $i)->getCalculatedValue()));
						$iEstadoAgenteCompra = quitarCaracteresEspeciales($iEstadoAgenteCompra);
						$Nu_Agente_Compra = ($iEstadoAgenteCompra==1 ? 1 : 0);
                        
	                	$iEstadoCargaConsolidada = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CARGA_CONSOLIDADA'] . $i)->getCalculatedValue()));
						$iEstadoCargaConsolidada = quitarCaracteresEspeciales($iEstadoCargaConsolidada);
						$Nu_Carga_Consolidada = ($iEstadoCargaConsolidada==1 ? 1 : 0);
                        
	                	$iEstadoImportacionGrupal = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['IMPORTACION_GRUPAL'] . $i)->getCalculatedValue()));
						$iEstadoImportacionGrupal = quitarCaracteresEspeciales($iEstadoImportacionGrupal);
						$Nu_Importacion_Grupal = ($iEstadoImportacionGrupal==1 ? 1 : 0);
                        
	                	$iEstadoCurso = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CURSO'] . $i)->getCalculatedValue()));
						$iEstadoCurso = quitarCaracteresEspeciales($iEstadoCurso);
						$Nu_Curso = ($iEstadoCurso==1 ? 1 : 0);
                        
	                	$iEstadoViajeChina = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['VIAJE_CHINA'] . $i)->getCalculatedValue()));
						$iEstadoViajeChina = quitarCaracteresEspeciales($iEstadoViajeChina);
						$Nu_Viaje_Negocios = ($iEstadoViajeChina==1 ? 1 : 0);

                        if (
                        	(
                        		$ID_Tipo_Documento_Identidad == 1 ||
                        		$ID_Tipo_Documento_Identidad == 2 ||
                        		$ID_Tipo_Documento_Identidad == 3 ||
                        		$ID_Tipo_Documento_Identidad == 4 ||
                        		$ID_Tipo_Documento_Identidad == 5 ||
                        		$ID_Tipo_Documento_Identidad == 6
                        	) && !empty($Nu_Documento_Identidad) && !empty($No_Entidad)) {
		                	$arrCliente[] = array(
								'ID_Tipo_Documento_Identidad'	=> $ID_Tipo_Documento_Identidad,
								'Nu_Documento_Identidad'		=> $Nu_Documento_Identidad,
								'No_Entidad'					=> $No_Entidad,
								'Txt_Direccion_Entidad'			=> $Txt_Direccion_Entidad,
								'Nu_Telefono_Entidad'			=> $Nu_Telefono_Entidad,
								'Nu_Celular_Entidad'			=> $Nu_Celular_Entidad,
								'Txt_Email_Entidad'				=> $Txt_Email_Entidad,
								'No_Contacto'					=> $No_Contacto,
								'Nu_Celular_Contacto'			=> $Nu_Celular_Contacto,
								'Txt_Email_Contacto'			=> $Txt_Email_Contacto,
								'No_Pais'						=> $No_Pais,
								'No_Departamento'				=> $No_Departamento,
								'No_Provincia'					=> $No_Provincia,
								'No_Distrito'					=> $No_Distrito,
								'Txt_Descripcion'				=> $Txt_Descripcion,
								'Nu_Dias_Credito'				=> $sDiasCredito,
								'Nu_Agente_Compra'				=> $Nu_Agente_Compra,
								'Nu_Carga_Consolidada'			=> $Nu_Carga_Consolidada,
								'Nu_Importacion_Grupal'			=> $Nu_Importacion_Grupal,
								'Nu_Curso'						=> $Nu_Curso,
								'Nu_Viaje_Negocios'				=> $Nu_Viaje_Negocios,
		                	);
                        } else {
                        	$iCantidadNoProcesados++;
                        }
                	}

                	$bResponse=false;
                	if ( count($arrCliente) > 0 ) {
		                $this->ClienteModel->setBatchImport($arrCliente);
		                $bResponse = $this->ClienteModel->importData();
                	} else {
	            		unlink($destino);
	                	unset($arrCliente);
                	
                		$sStatus = 'error-sindatos';
						redirect('Ventas/ReglasVenta/ClienteController/listarClientes/' . $sStatus);
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrCliente);
                	
                	if ($bResponse){
                		$sStatus = 'success';
						redirect('Ventas/ReglasVenta/ClienteController/listarClientes/' . $sStatus . '/' . $iCantidadNoProcesados);
                	} else {
                		$sStatus = 'error-bd';
						redirect('Ventas/ReglasVenta/ClienteController/listarClientes/' . $sStatus);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Ventas/ReglasVenta/ClienteController/listarClientes/' . $sStatus);
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Ventas/ReglasVenta/ClienteController/listarClientes/' . $sStatus);
		    }
		}
	}

	public function listarClientes($sStatus='', $iCantidadNoProcesados=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_cliente" => true));
			$this->load->view('Ventas/ReglasVenta/ClienteView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados));
			$this->load->view('footer_v2', array("js_cliente" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->ClienteModel->get_datatables();
        $data = array();
        $action = 'delete';
        foreach ($arrData as $row) {
			$rows = array();
			
			$sTipoServicio = '';
			$sTipoServicioWhatsApp = '';
			$sTipoServicio .= '<span class="badge bg-secondary">Otros</span>';
			$sTipoServicioWhatsApp = 'Otros';
			if($row->Nu_Agente_Compra==1){
				$sTipoServicio .= '<br><span class="badge bg-primary">Agente de Compra</span>';
				$sTipoServicioWhatsApp .= 'Agente de Compra';
			}
			if($row->Nu_Carga_Consolidada==1){
				$sTipoServicio .= '<br><span class="badge bg-warning">Carga Consolidada</span>';
				$sTipoServicioWhatsApp .= 'Carga Consolidada';
			}
			if($row->Nu_Importacion_Grupal==1){
				$sTipoServicio .= '<br><span class="badge bg-dark">Importación Grupal</span>';
				$sTipoServicioWhatsApp .= 'Importación Grupal';
			}
			if($row->Nu_Curso==1){
				$sTipoServicio .= '<br><span class="badge bg-info">Curso</span>';
				$sTipoServicioWhatsApp .= 'Curso';
			}
			if($row->Nu_Viaje_Negocios==1){
				$sTipoServicio .= '<br><span class="badge bg-success">Viaje de Negocios</span>';
				$sTipoServicioWhatsApp .= 'Viaje de Negocios';
			}

			$rows[] = $sTipoServicio;

            $rows[] = $row->No_Tipo_Documento_Identidad_Breve;
            $rows[] = $row->Nu_Documento_Identidad;
			$rows[] = $row->No_Entidad;

			//whatsapp
			//$whatsapp = 'https://api.whatsapp.com/send?phone=51953314683&text=Te+saluda+ProBusiness+%F0%9F%91%8B%F0%9F%8F%BB%2C%0ATe+registraste+en+nuestra+plataforma+para+el+servicio+de+carga+consolidada.';

			$sCodigoPaisCelular = '51';
			$sMensaje = "Te saluda ProBusiness 👋🏻\n";
            $sMensaje .= "Te registraste en nuestra plataforma para el servicio de " . $sTipoServicioWhatsApp . ". \n\n";
            $sMensaje = urlencode($sMensaje);
            $sWhatsAppCliente = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular_Entidad . '&text=' . $sMensaje . '" target="_blank"><i class="fab fa-whatsapp" style="color: #25d366;"></i></a>';

			$rows[] = $row->Nu_Celular_Entidad . $sWhatsAppCliente;

			$rows[] = $row->Txt_Email_Entidad;
			$rows[] = $row->Txt_Descripcion;
			$rows[] = allTypeDate($row->Fe_Registro, '-', 0);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCliente(\'' . $row->ID_Entidad . '\', \'' . $row->Nu_Documento_Identidad . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Empresa . '\', \'' . $row->ID_Entidad . '\', \'' . ( $row->Nu_Documento_Identidad != '' ? $row->Nu_Documento_Identidad : '-') . '\', \'' . $action . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    
    public function uploadOnly(){
    	if (!empty($_FILES)){
    		$config['upload_path']	 = $this->upload_path;
    		$config['allowed_types'] = 'png';
            $config['max_size']      = 1024;//1024 KB = 1 MB
    		$this->load->library('upload', $config);
    		if (!$this->upload->do_upload('file')){
    			echo 'Error al subir imagen';
    		}
    	}
    }
    
    public function removeFileImage(){
    	$nameFileImage = $this->input->post('nameFileImage');
    	if ( $nameFileImage && file_exists($this->upload_path . $this->security->xss_clean($nameFileImage)) )
    		unlink($this->upload_path . $this->security->xss_clean($nameFileImage));
    	else
    		unlink($nameFileImage);
    }
    
    public function get_image($Nu_Documento_Identidad){
    	if ( file_exists($this->upload_path . $this->security->xss_clean($Nu_Documento_Identidad) . '.png') ){
	    	$arrfilesImages = array(
	    		'name' => $this->upload_path . $this->security->xss_clean($Nu_Documento_Identidad) . '.png',
	    		'size' => filesize($this->upload_path . $this->security->xss_clean($Nu_Documento_Identidad) . '.png')
	    	);
    	}
    	echo json_encode($arrfilesImages);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ClienteModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Telefono_Entidad = $this->input->post('Nu_Telefono_Entidad');
		if ( $this->input->post('Nu_Telefono_Entidad') && strlen($this->input->post('Nu_Telefono_Entidad')) == 8){
	        $Nu_Telefono_Entidad = explode(' ', $this->input->post('Nu_Telefono_Entidad'));
	        $Nu_Telefono_Entidad = $Nu_Telefono_Entidad[0].$Nu_Telefono_Entidad[1];
		}
		$Nu_Celular_Entidad = $this->input->post('Nu_Celular_Entidad');
		if ( $this->input->post('Nu_Celular_Entidad') && strlen($this->input->post('Nu_Celular_Entidad')) == 11){
	        $Nu_Celular_Entidad = explode(' ', $this->input->post('Nu_Celular_Entidad'));
	        $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
		}
		$Nu_Celular_Contacto = $this->input->post('Nu_Celular_Contacto');
		if ( $this->input->post('Nu_Celular_Contacto') && strlen($this->input->post('Nu_Celular_Contacto')) == 11){
	        $Nu_Celular_Contacto = explode(' ', $this->input->post('Nu_Celular_Contacto'));
	        $Nu_Celular_Contacto = $Nu_Celular_Contacto[0].$Nu_Celular_Contacto[1].$Nu_Celular_Contacto[2];
		}
		$data = array(
			'ID_Empresa'					=> $this->user->ID_Empresa,
			'ID_Organizacion'				=> $this->user->ID_Organizacion,//Organizacion
			'Nu_Tipo_Entidad'				=> 0,//Cliente
			'ID_Tipo_Documento_Identidad'	=> $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad'		=> strtoupper($this->input->post('Nu_Documento_Identidad')),
			'No_Entidad'					=> $this->input->post('No_Entidad'),
			'Txt_Direccion_Entidad'			=> $this->input->post('Txt_Direccion_Entidad'),
			'Nu_Dias_Credito'			=> $this->input->post('Nu_Dias_Credito'),
			'Nu_Telefono_Entidad'			=> $Nu_Telefono_Entidad,
			'Nu_Celular_Entidad'			=> $Nu_Celular_Entidad,
			'Txt_Email_Entidad'				=> $this->input->post('Txt_Email_Entidad'),
			'Txt_Descripcion'				=> $this->input->post('Txt_Descripcion'),
			'No_Contacto'					=> $this->input->post('No_Contacto'),
			'Txt_Email_Contacto'			=> $this->input->post('Txt_Email_Contacto'),
			'Nu_Celular_Contacto'			=> $Nu_Celular_Contacto,
			'Nu_Estado'						=> $this->input->post('Nu_Estado'),
			'ID_Tipo_Cliente_1'			=> $this->input->post('ID_Tipo_Cliente_1'),
			'Fe_Nacimiento'			=> !empty($this->input->post('Fe_Nacimiento')) ? ToDate($this->input->post('Fe_Nacimiento')) : '0000-00-00',
		);
		if ( !empty($this->input->post('ID_Pais')) ){
			$data = array_merge($data, array("ID_Pais" => $this->input->post('ID_Pais')));
		}
		if ( !empty($this->input->post('ID_Departamento')) ){
			$data = array_merge($data, array("ID_Departamento" => $this->input->post('ID_Departamento')));
		}
		if ( !empty($this->input->post('ID_Provincia')) ){
			$data = array_merge($data, array("ID_Provincia" => $this->input->post('ID_Provincia')));
		}
		if ( !empty($this->input->post('ID_Distrito')) ){
			$data = array_merge($data, array("ID_Distrito" => $this->input->post('ID_Distrito')));
		}
		echo json_encode(
		($this->input->post('EID_Empresa') != '' && $this->input->post('EID_Entidad') != '') ?
			$this->ClienteModel->actualizarCliente(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Entidad' => $this->input->post('EID_Entidad')), $data, $this->input->post('ENu_Documento_Identidad'), $this->input->post('ENo_Entidad'))
		:
			$this->ClienteModel->agregarCliente($data)
		);
	}
    
	public function eliminarCliente($ID_Empresa, $ID, $Nu_Documento_Identidad){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ClienteModel->eliminarCliente($this->security->xss_clean($ID_Empresa), $this->security->xss_clean($ID), $this->security->xss_clean($Nu_Documento_Identidad)));
	}
}
