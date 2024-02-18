<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProveedorController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Logistica/ReglasLogistica/ProveedorModel');
		$this->load->model('HelperModel');
	}
	
	public function importarExcelProveedor(){
		if (isset($_FILES['excel-archivo_proveedor']['name']) && isset($_FILES['excel-archivo_proveedor']['type']) && isset($_FILES['excel-archivo_proveedor']['tmp_name'])) {
		    $archivo	= $_FILES['excel-archivo_proveedor']['name'];
		    $tipo		= $_FILES['excel-archivo_proveedor']['type'];
		    $destino	= "bak_" . $archivo;
		    
		    if (copy($_FILES['excel-archivo_proveedor']['tmp_name'], $destino)) {
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
		            );
	                
	                $arrProveedor = array();
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
	                	$Txt_Email_Entidad	= filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['EMAIL'] . $i)->getCalculatedValue()));
                        
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
                        
                        if (
                        	(
                        		$ID_Tipo_Documento_Identidad == 1 ||
                        		$ID_Tipo_Documento_Identidad == 2 ||
                        		$ID_Tipo_Documento_Identidad == 3 ||
                        		$ID_Tipo_Documento_Identidad == 4 ||
                        		$ID_Tipo_Documento_Identidad == 5 ||
                        		$ID_Tipo_Documento_Identidad == 6
                        	) && !empty($Nu_Documento_Identidad) && !empty($No_Entidad)) {
		                	$arrProveedor[] = array(
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
		                	);
                        } else {
                        	$iCantidadNoProcesados++;
                        }
                	}
                	
                	$bResponse=false;
                	if ( count($arrProveedor) > 0 ) {
		                $this->ProveedorModel->setBatchImport($arrProveedor);
		                $bResponse = $this->ProveedorModel->importData();
                	} else {
	            		unlink($destino);
	                	unset($arrProveedor);
                	
                		$sStatus = 'error-sindatos';
						redirect('Logistica/ReglasLogistica/ProveedorController/listarProveedores/' . $sStatus);
                		exit();
                	}
                	
            		unlink($destino);
                	unset($arrProveedor);
                	
                	if ($bResponse){
                		$sStatus = 'success';
						redirect('Logistica/ReglasLogistica/ProveedorController/listarProveedores/' . $sStatus . '/' . $iCantidadNoProcesados);
                	} else {
                		$sStatus = 'error-bd';
						redirect('Logistica/ReglasLogistica/ProveedorController/listarProveedores/' . $sStatus);
                	}
		        } else {
        	        $sStatus = 'error-archivo_no_existe';
					redirect('Logistica/ReglasLogistica/ProveedorController/listarProveedores/' . $sStatus);
		        }
		    } else {
		        $sStatus = 'error-copiar_archivo';
				redirect('Logistica/ReglasLogistica/ProveedorController/listarProveedores/' . $sStatus);
		    }
		}
	}
	
	public function listarProveedores($sStatus='', $iCantidadNoProcesados=''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			//$this->load->view('header');
			//$this->load->view('Logistica/ReglasLogistica/ProveedorView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados));
			//$this->load->view('footer', array("js_proveedor" => true));
			
			$this->load->view('header_v2', array("js_proveedor" => true));
			$this->load->view('Logistica/ReglasLogistica/ProveedorView', array('sStatus' => $sStatus, 'iCantidadNoProcesados' => $iCantidadNoProcesados));
			$this->load->view('footer_v2', array("js_proveedor" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->ProveedorModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
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
			$rows[] = $row->Nu_Celular_Entidad;
			$rows[] = $row->Txt_Email_Entidad;
			$rows[] = $row->Nu_Dias_Credito;
			$rows[] = $row->Txt_Direccion_Entidad;
			$arrEstadoRegistro = $this->HelperModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verProveedor(\'' . $row->ID_Entidad . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarProveedor(\'' . $row->ID_Entidad . '\', \'' . $action . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->ProveedorModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudProveedor(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$Nu_Telefono_Entidad = '';
		if ( $this->input->post('Nu_Telefono_Entidad') && strlen($this->input->post('Nu_Telefono_Entidad')) == 8){
	        $Nu_Telefono_Entidad = explode(' ', $this->input->post('Nu_Telefono_Entidad'));
	        $Nu_Telefono_Entidad = $Nu_Telefono_Entidad[0].$Nu_Telefono_Entidad[1];
		}
		$Nu_Celular_Entidad = '';
		if ( $this->input->post('Nu_Celular_Entidad') && strlen($this->input->post('Nu_Celular_Entidad')) == 11){
	        $Nu_Celular_Entidad = explode(' ', $this->input->post('Nu_Celular_Entidad'));
	        $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
		}
		$data = array(
			'ID_Empresa'					=> $this->user->ID_Empresa,
			'ID_Organizacion'				=> $this->user->ID_Organizacion,//Organizacion
			'Nu_Tipo_Entidad'				=> 1,//Proveedor
			'ID_Tipo_Documento_Identidad'	=> $this->input->post('ID_Tipo_Documento_Identidad'),
			'Nu_Documento_Identidad'		=> strtoupper($this->input->post('Nu_Documento_Identidad')),
			'No_Entidad'					=> $this->input->post('No_Entidad'),
			'Txt_Direccion_Entidad'			=> $this->input->post('Txt_Direccion_Entidad'),
			'Nu_Dias_Credito' => $this->input->post('Nu_Dias_Credito'),
			'Nu_Telefono_Entidad'			=> $Nu_Telefono_Entidad,
			'Nu_Celular_Entidad'			=> $Nu_Celular_Entidad,
			'Txt_Email_Entidad'				=> $this->input->post('Txt_Email_Entidad'),
			'Nu_Estado'						=> $this->input->post('Nu_Estado'),
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
			$this->ProveedorModel->actualizarProveedor(array('ID_Empresa' => $this->input->post('EID_Empresa'), 'ID_Entidad' => $this->input->post('EID_Entidad')), $data, $this->input->post('ENu_Documento_Identidad'))
		:
			$this->ProveedorModel->agregarProveedor($data)
		);
	}
    
	public function eliminarProveedor($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->ProveedorModel->eliminarProveedor($this->security->xss_clean($ID)));
	}
}
