<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/PHPExcel.php';

class PedidosGarantizados extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosGarantizadosModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar($sCorrelativoCotizacion = '', $ID_Pedido_Cabecera = ''){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2',  array("js_pedidos_garantizados" => true));
			$this->load->view('AgenteCompra/PedidosGarantizadosView', array(
				'sCorrelativoCotizacion' => $sCorrelativoCotizacion,
				'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera
			));
			$this->load->view('footer_v2', array("js_pedidos_garantizados" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosGarantizadosModel->get_datatables($this->user);
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
            $rows[] = $row->No_Pais;
            $rows[] = $row->cotizacionCode;
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;//quitar para el chino
            $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;//quitar para el chino
			
			//pago garantizado
			if($this->user->Nu_Tipo_Privilegio_Acceso!=2) {
				$btn_pago_garantizado_peru = '';
				if($row->Nu_Estado==2)
					$btn_pago_garantizado_peru = '<button class="btn btn-xs btn-link" alt="Subir pago" title="Subir pago" href="javascript:void(0)" onclick="documentoPagoGarantizado(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion. '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button><br>';

				if(!empty($row->file_url))
					$btn_pago_garantizado_peru .= '<button class="btn btn-xs btn-link" alt="Descargar pago" title="Descargar pago" href="javascript:void(0)" onclick="descargarDocumentoPagoGarantizado(\'' . $row->ID_Pedido_Cabecera . '\')">Descargar</button>';
				
				$rows[] = $btn_pago_garantizado_peru;
			}
			//jefe china 5, personal probusiness 1 personal china 2
			//estado peru
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Garantizado" title="Garantizado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Usuario_Interno_China . '\');">Esperando</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Usuario_Interno_China . '\');">Enviado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Usuario_Interno_China . '\');">Rechazado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Aprobado" title="Aprobado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Usuario_Interno_China . '\');">Aprobado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Observado" title="Observado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8, \'' . $row->ID_Usuario_Interno_China . '\');">Observado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Recibido" title="Recibido" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',10, \'' . $row->ID_Usuario_Interno_China . '\');">Recibido</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			
			if($this->user->Nu_Tipo_Privilegio_Acceso!=1){//no tiene acceso a cambiar status de Perú
				$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
            $rows[] = $dropdown_estado;

			//asignar personal de china desde perú
			if($this->user->Nu_Tipo_Privilegio_Acceso==5|| $this->user->Nu_Tipo_Privilegio_Acceso==1) {
				$btn_asignar_personal_china = '';
				if($this->user->Nu_Tipo_Privilegio_Acceso==5){//1=probusiness
					$btn_asignar_personal_china = '<button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="asignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><i class="far fa-user fa-2x text-danger" aria-hidden="true"></i></button>';
					//if(!empty($row->ID_Usuario_Interno_Empresa_China)){
				}
				if(!empty($row->ID_Usuario_Interno_China) ){
					if($this->user->Nu_Tipo_Privilegio_Acceso==5|| $this->user->Nu_Tipo_Privilegio_Acceso==1){
						$btn_asignar_personal_china = '<span class="badge bg-secondary">' . $row->No_Usuario . '</span>';
	
					}if($this->user->Nu_Tipo_Privilegio_Acceso==5){
						$btn_asignar_personal_china .= '<br><button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="removerAsignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->ID_Usuario_Interno_Empresa_China . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
					}
				}
				$rows[] = $btn_asignar_personal_china;
			}

			//estado de china
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
			$dropdown_estado_china = '<div class="dropdown">';
				$dropdown_estado_china .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado_china .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado_china .= '<span class="caret"></span></button>';
				$dropdown_estado_china .= '<ul class="dropdown-menu">';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $sCorrelativoCotizacion. '\');">Pendiente</a></li>';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="En proceso" title="En proceso" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $sCorrelativoCotizacion. '\');">En proceso</a></li>';
					$dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Cotizado" title="Cotizado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $sCorrelativoCotizacion. '\');">Cotizado</a></li>';
				$dropdown_estado_china .= '</ul>';
			$dropdown_estado_china .= '</div>';

			//comentado temporal
			if($this->user->Nu_Tipo_Privilegio_Acceso==1){//no tiene acceso a cambiar status de China
				$dropdown_estado_china = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			}
            $rows[] = $dropdown_estado_china;

			//confirmar cotización
			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';

			// //EXCEL cliente de pedido
			if($this->user->Nu_Tipo_Privilegio_Acceso!=2) {
				$excel_agente_compra = '<button class="btn" alt="Proforma Trading" title="Proforma Trading" href="javascript:void(0)" onclick="generarAgenteCompra(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2"> Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
				$excel_consolida_trading = '<button class="btn" alt="Proforma C. Trading" title="Proforma C. Trading" href="javascript:void(0)" onclick="generarConsolidaTrading(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2">C. Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
				$rows[] = $excel_agente_compra . '<br>' . $excel_consolida_trading;
				$rows[] = '<span class="badge bg-danger">7 días</span><br>T.C. $ ' . round($row->Ss_Tipo_Cambio, 2);
			}

			//estado peru
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoImportacionIntegral($row->Nu_Importacion_Integral);
			// $dropdown_estado = '<div class="dropdown">';
			// 	$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
			// 		$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
			// 	$dropdown_estado .= '<span class="caret"></span></button>';
			// 	$dropdown_estado .= '<ul class="dropdown-menu">';
			// 		$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Si" title="Si" href="javascript:void(0)" onclick="cambiarEstadoImpotacionIntegral(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $sCorrelativoCotizacion. '\');">Si</a></li>';
			// 		$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="No" title="No" href="javascript:void(0)" onclick="cambiarEstadoImpotacionIntegral(\'' . $row->ID_Pedido_Cabecera . '\',0, \'' . $sCorrelativoCotizacion. '\');">No</a></li>';
			// 	$dropdown_estado .= '</ul>';
			// $dropdown_estado .= '</div>';
			
			// if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
			// 	$dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			// }
            // $rows[] = $dropdown_estado;
			
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
		$arrReponse = $this->PedidosGarantizadosModel->get_by_id($this->security->xss_clean($ID));
		//get user Nu_Tipo_Privilegio_Acceso 
		
        echo json_encode($arrReponse);
        //echo json_encode($this->PedidosGarantizadosModel->get_by_id($this->security->xss_clean($ID)));
    }
    	
	public function getItemProveedor($ID){
		$data=$this->PedidosGarantizadosModel->getItemProveedor($this->security->xss_clean($ID));
        echo json_encode(array('data'=>$data,'privilegio'=>$this->user->Nu_Tipo_Privilegio_Acceso));
    }
    	
	public function getItemImagenProveedor($ID){
        echo json_encode($this->PedidosGarantizadosModel->getItemImagenProveedor($this->security->xss_clean($ID)));
    }
    	
	public function elegirItemProveedor($id_detalle, $ID, $status, $sCorrelativoCotizacion, $sNameItem='',$pedidoItem,$id_supplier){
        echo json_encode($this->PedidosGarantizadosModel->elegirItemProveedor($this->security->xss_clean($id_detalle), $this->security->xss_clean($ID), $this->security->xss_clean($status), $this->security->xss_clean($sCorrelativoCotizacion), $this->security->xss_clean($sNameItem),
			$this->security->xss_clean($pedidoItem), $this->security->xss_clean($id_supplier)));
    }

	public function actualizarElegirItemProductos(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
        echo json_encode($this->PedidosGarantizadosModel->actualizarElegirItemProductos($this->input->post(), $_FILES));
    }

	public function cambiarEstado($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($ID_Usuario_Interno_Empresa_China)));
	}

	public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativoCotizacion){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativoCotizacion)));
	}

	public function crudPedidoGrupal(){
		//array_debug($this->input->post());
		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('EID_Empresa'),
			'ID_Organizacion' => $this->input->post('EID_Organizacion'),
			'Ss_Tipo_Cambio' => $this->input->post('Ss_Tipo_Cambio'),
			'Txt_Observaciones_Garantizado' => $this->input->post('Txt_Observaciones_Garantizado'),
			'file_cotizacion' => $_FILES['file_cotizacion'],
		);
		
		echo json_encode($this->PedidosGarantizadosModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
				),
				$data,
				$this->input->post('addProducto'),
				$this->input->post('addProductoTable'),
				$this->input->post('ECorrelativo')
			)
		);
	}

	public function addPedidoItemProveedor(){
		//array_debug($this->input->post());
		//array_debug($_FILES);
		$formData = $this->input->post();
		$fileData = $_FILES;
		echo json_encode($this->PedidosGarantizadosModel->addPedidoItemProveedor($formData,$fileData));
		exit();
	}

	//generar cotización PDF para pedido de cliente	
	public function generarAgenteCompra($ID){
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
		$this->load->library('PHPExcel');
		// echo json_encode($data);
        $templatePath = 'assets/downloads/agente_compra/TRADING-PERU.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($templatePath);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cotizacion.xlsx"');
        header('Cache-Control: max-age=0');
		//set D10	 = $data[0]->No_Contacto
		$objPHPExcel->getActiveSheet()->setCellValue('E18', $data[0]->No_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('E19', $data[0]->Nu_Celular_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('E20', $data[0]->Txt_Email_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('E21', "TRADING");
		$objPHPExcel->getActiveSheet()->setCellValue('K21', date('d/m/Y'));
		$objPHPExcel->getActiveSheet()->setCellValue('S22', $data[0]->Ss_Tipo_Cambio);
		$objPHPExcel->getActiveSheet()->setCellValue('E35', "=K32");
		$initialRow=26;
		$lastProductrow=31;
		$tempUrl = array();
		foreach($data as $key => $val){
			//get $val->Txt_Url_Imagen_Producto and set image in cell
			if (!empty($val->Txt_Url_Imagen_Producto)) {
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				// $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
				// $row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
				$image = file_get_contents($val->Txt_Url_Imagen_Producto);
				if ($image !== false) {
					$path = 'assets/img/';
					$filename = $path . uniqid() . '.jpg';
					file_put_contents($filename, $image);
					$tempUrl[] = $filename;
					$objDrawing->setPath($filename);
					$objDrawing->setWidthAndHeight(148, 500);
					$objDrawing->setResizeProportional(true);
					$objDrawing->setCoordinates('D' . $initialRow);
					$objDrawing->setOffsetX(10); // Ajusta el desplazamiento X si es necesario
					$objDrawing->setOffsetY(10); // Ajusta el desplazamiento Y si es necesario
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				}
			}
			// $objDrawing->setPath($val->Txt_Url_Imagen_Producto);
	


			$objPHPExcel->getActiveSheet()->setCellValue("E". $initialRow, $val->Txt_Producto);
			$objPHPExcel->getActiveSheet()->setCellValue("F". $initialRow, $val->Txt_Descripcion);
			$objPHPExcel->getActiveSheet()->setCellValue("G". $initialRow, $val->Qt_Producto);
			$objPHPExcel->getActiveSheet()->setCellValue("H". $initialRow, $val->unidad_medida);
			$objPHPExcel->getActiveSheet()->setCellValue("I". $initialRow, $val->Ss_Precio);
			$objPHPExcel->getActiveSheet()->setCellValue("M". $initialRow, $val->Qt_Producto_Caja);
			$objPHPExcel->getActiveSheet()->setCellValue("O". $initialRow, "=P". $initialRow."/N". $initialRow);
			$objPHPExcel->getActiveSheet()->setCellValue("P". $initialRow, $val->Qt_Cbm);
			$objPHPExcel->getActiveSheet()->setCellValue("Q". $initialRow, $val->kg_box);
			$objPHPExcel->getActiveSheet()->setCellValue("S". $initialRow, $val->Ss_Costo_Delivery);
			$objPHPExcel->getActiveSheet()->setCellValue("T". $initialRow, $val->Nu_Dias_Delivery);
			$objPHPExcel->getActiveSheet()->setCellValue("U". $initialRow, $val->Txt_Nota);
			$initialRow++;
		}
		if($initialRow<$lastProductrow){
			$objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow-$initialRow+1);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('K'. ($initialRow),"=SUM(K26:K".($initialRow-1).")"); 
		$objPHPExcel->getActiveSheet()->setCellValue('L'. ($initialRow),"=SUM(L26:L".($initialRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('N'. ($initialRow),"=SUM(N26:N".($initialRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('P'. ($initialRow),"=SUM(P26:P".($initialRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('R'. ($initialRow),"=SUM(R26:R".($initialRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('S'. ($initialRow),"=SUM(S26:S".($initialRow-1).")"); 

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit(); //
		
	}
	

	//generar cotización PDF para pedido de cliente	
	public function generarConsolidaTrading($ID){
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
		//array_debug($data);
		$this->load->library('PHPExcel');
        $templatePath = 'assets/downloads/agente_compra/CTRADING-PERU.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($templatePath);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cotizacion.xlsx"');
        header('Cache-Control: max-age=0');
		//set D10	 = $data[0]->No_Contacto
		$objPHPExcel->getActiveSheet()->setCellValue('D10', $data[0]->No_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('D11', $data[0]->Nu_Celular_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('D12', $data[0]->Txt_Email_Contacto);
		$objPHPExcel->getActiveSheet()->setCellValue('J10', $data[0]->No_Entidad);
		$objPHPExcel->getActiveSheet()->setCellValue('T10', $data[0]->Ss_Tipo_Cambio);
		$tempUrl = array();
		//products
		// $initialColumn="C";
		$initialRow=17;
		foreach($data as $key => $val){
			if (!empty($val->Txt_Url_Imagen_Producto)) {
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				// $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
				// $row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
				$image = file_get_contents($val->Txt_Url_Imagen_Producto);
				if ($image !== false) {
					$path = 'assets/img/';
					$filename = $path . uniqid() . '.jpg';
					file_put_contents($filename, $image);
					$tempUrl[] = $filename;
					$objDrawing->setPath($filename);
					$objDrawing->setWidthAndHeight(148, 500);
					$objDrawing->setResizeProportional(true);
					$objDrawing->setCoordinates('C' . $initialRow);
					$objDrawing->setOffsetX(10); // Ajusta el desplazamiento X si es necesario
					$objDrawing->setOffsetY(10); // Ajusta el desplazamiento Y si es necesario
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue("D". $initialRow, $val->Txt_Producto);
			$objPHPExcel->getActiveSheet()->setCellValue("E". $initialRow, $val->Txt_Descripcion);
			$objPHPExcel->getActiveSheet()->setCellValue("F". $initialRow, $val->Qt_Producto);
			$objPHPExcel->getActiveSheet()->setCellValue("G". $initialRow, $val->unidad_medida);
			$objPHPExcel->getActiveSheet()->setCellValue("H". $initialRow, $val->Ss_Precio);
			$objPHPExcel->getActiveSheet()->setCellValue("L". $initialRow, $val->Qt_Producto_Caja);
			$objPHPExcel->getActiveSheet()->setCellValue("N". $initialRow, "=O". $initialRow . "/M". $initialRow);
			$objPHPExcel->getActiveSheet()->setCellValue("Q". $initialRow, "=P". $initialRow . "*M". $initialRow);

			$objPHPExcel->getActiveSheet()->setCellValue("O". $initialRow, $val->Qt_Cbm);
			$objPHPExcel->getActiveSheet()->setCellValue("P". $initialRow, $val->kg_box);
			$objPHPExcel->getActiveSheet()->setCellValue("R". $initialRow, $val->Ss_Costo_Delivery);
			$objPHPExcel->getActiveSheet()->setCellValue("S". $initialRow, $val->Nu_Dias_Delivery);
			$objPHPExcel->getActiveSheet()->setCellValue("T". $initialRow, $val->Txt_Nota);
			$initialRow++;
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
		foreach($tempUrl as $val){
			unlink($val);
		}
        exit(); 
		return;
		$hoja_activa = 0;
		$fila=1;
		$fileNameExcel = "Proforma_Consolida_Trading_sin_data.xls";
		
		$hoja_activa = 0;
			
		//Title
		$BStyle_top = array(
		'borders' => array(
			'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		)
		);
		
		$BStyle_left = array(
		'borders' => array(
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		)
		);
		
		$BStyle_right = array(
		'borders' => array(
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		)
		);
		
		$BStyle_bottom = array(
		'borders' => array(
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		)
		);
		
		$BStyle_background_title = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '000000')
			),
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FFFFFF'),
				'size'  => 18
			)
		);
		
		$BStyle_background_sub_tittle = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '000000')
			),
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FFFFFF'),
				'size'  => 13
			)
		);
		
		$BStyle_background_name_label = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'faddd0')
			),
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 12
			)
		);
		
		$style_align_center = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
		);
		
		$style_align_right = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		)
		);
		
		$style_align_left = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		)
		);
		
		$styleArrayAllborder = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			)
		);

		$BStyle_tittle_cursive = array(
			'font'  => array(
				'color' => array('rgb' => '000000'),
				'size'  => 11,
				'italic'  => true,
			)
		);

		//SET ALL BORDER NONE
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'FFFFFF')
				)
			)
		);
		$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("8");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("8");//NRO

		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("35");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("25");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("25");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");//NRO

		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");//NRO
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("10");//NRO

		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15");//NRO

		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("20");//NRO
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("20");//NRO

		//Title
		$fila=2;
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		
		$objDrawing->setPath('assets/img/logos/logo_probusiness.png');
		$objDrawing->setWidthAndHeight(340,500);
		$objDrawing->setResizeProportional(true);
		$objDrawing->setCoordinates('H' . $fila);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C' . $fila . ':R' . $fila);
		
		//border
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila . ':R' . $fila)->applyFromArray($BStyle_top);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

		$fila=3;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$fila)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'Ofic Perú');
		
		$objPHPExcel->getActiveSheet()->getStyle('P'.$fila)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('P'.$fila, 'Ofic China');
		$objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($style_align_right);
		
		$fila=4;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'Jr. Alberto Bartón 527');
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('N'.$fila, 'Shuangchuang Building, No. 1133');
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('N' . $fila . ':P' . $fila);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_tittle_cursive);
		$objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($BStyle_tittle_cursive);
		$objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->applyFromArray($style_align_right);
		
		$fila=5;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'Santa Catalina - La Victoria');
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('N'.$fila, 'Chouzhou North Road, Yiwu City');
		$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_tittle_cursive);
		$objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($BStyle_tittle_cursive);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('N' . $fila . ':P' . $fila);
		$objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->applyFromArray($style_align_right);

		$fila=6;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

		$fila=7;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
		
		$fila=8;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_title);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'COTIZACIÓN DE PRODUCTOS');
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':P' . $fila);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);

		$fila=9;
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
		$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
		// /. Title

		if( !empty($data) ){
			$sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0 , 3))  . str_pad($data[0]->Nu_Correlativo,3,"0",STR_PAD_LEFT);
	  			
			$objPHPExcel->getActiveSheet()->setTitle($sCorrelativoCotizacion);
			$fileNameExcel = "2.2_PROFORMA C.TRADING_" . $sCorrelativoCotizacion . ".xls";
			
			$fila=10;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'NAME: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, $data[0]->No_Contacto);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'N° PROFORMA: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M'.$fila, $sCorrelativoCotizacion);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
			
			$fila=11;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'WHATSAPP: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, $data[0]->Nu_Celular_Contacto);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$fila)->applyFromArray($style_align_left);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'SERVICIO: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M'.$fila, 'C. TRADING');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
			
			$fila=12;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'CORREO: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, $data[0]->Txt_Email_Contacto);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'FECHA: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M'.$fila, ToDateBD($data[0]->Fe_Emision_Cotizacion));
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
			
			$fila=13;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'RAZÓN SOCIAL: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, $data[0]->No_Entidad);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'VALIDEZ: ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M'.$fila, '7 DÍAS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
			
			$fila=14;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
			$fila=15;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
			$fila=16;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':R' . $fila)->applyFromArray($BStyle_top);
			
			$fila=17;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
			$fila=18;
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(50);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila. ':Q' . $fila)->applyFromArray($BStyle_background_sub_tittle);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'N');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E'.$fila, 'FOTO DEL PRODUCTO');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F'.$fila, 'NOMBRE COMERCIAL');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G'.$fila, 'CARACTERISTICAS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('H'.$fila, 'CANTIDAD TOTAL');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I'.$fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (RMB)');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('J'.$fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (USD)');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'COSTO TOTAL');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L'.$fila, 'PCS /' . "\n" . ' CAJA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M'.$fila, 'TOTAL CAJAS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('N'.$fila, 'CBM /' . "\n" . ' CAJA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O'.$fila, 'CBM ' . "\n" . ' TOTAL');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('P'.$fila, 'COSTO DE ENVÍO');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('Q'.$fila, 'TIEMPO ' . "\n" . ' PRODUCCION');

			
			$fila = 19;
			$iCounter=1;
			$fCostoTotalYuanesGeneral = 0;
			$fCostoTotalGeneral = 0;
			$fCbmTotal = 0;
			$fCbmTotalGeneral = 0;
			$fTotalCajasGeneral = 0;
			$fTotalCostoEnvioGeneral = 0;
            foreach($data as $row) {
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);
				
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('J' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('M' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('P' . $fila)->applyFromArray($styleArrayAllborder);
				$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($styleArrayAllborder);

				$html_data = array("&nbsp;");
				$row->Txt_Descripcion =str_replace($html_data," ",$row->Txt_Descripcion);
		
				$html_data = array("<br>", "<p>", "<br/>");
				$row->Txt_Descripcion =str_replace($html_data,"\n",$row->Txt_Descripcion);
		
				$row->Txt_Descripcion =strip_tags($row->Txt_Descripcion);

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('D' . $fila, $iCounter);

				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();

					//cloud
					$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
					$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
					if ( file_exists($row->Txt_Url_Imagen_Producto) ) {
						$objDrawing->setPath($row->Txt_Url_Imagen_Producto);
						$objDrawing->setWidthAndHeight(148,500);
						//$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('E' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('E' . $fila, '');
				}

				$fPrecioYuanes = $row->Ss_Precio;
				//$fPrecioDolares = ($row->Ss_Precio * $row->Ss_Tipo_Cambio);
				$fPrecioDolares = round(($row->Ss_Precio / $row->Ss_Tipo_Cambio), 2);
				//$fTotalCajas = ($row->Qt_Producto_Caja_Final / $row->Qt_Producto_Caja);//TOTAL CAJAS
				$fTotalCajas = ($row->Qt_Producto / $row->Qt_Producto_Caja);//TOTAL CAJAS
				//$fCostoTotal = ($fPrecioDolares * $row->Qt_Producto_Caja_Final);
				$fCostoTotal = ($fPrecioDolares * $row->Qt_Producto);
				$fCostoTotalYuanes = ($fPrecioYuanes * $row->Qt_Producto_Caja_Final);
				$fCbmTotal = ($fTotalCajas * $row->Qt_Cbm);

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('F' . $fila, $row->Txt_Producto)
				->setCellValue('G' . $fila, $row->Txt_Descripcion)
				//->setCellValue('H' . $fila, $row->Qt_Producto_Caja_Final)
				->setCellValue('H' . $fila, $row->Qt_Producto)
				->setCellValue('I' . $fila, $row->Ss_Precio)//precio yuanes
				->setCellValue('J' . $fila, $fPrecioDolares)
				->setCellValue('K' . $fila, $fCostoTotal)
				->setCellValue('L' . $fila, $row->Qt_Producto_Caja)
				->setCellValue('M' . $fila, $fTotalCajas)
				->setCellValue('N' . $fila, $row->Qt_Cbm)
				->setCellValue('O' . $fila, $fCbmTotal)
				->setCellValue('P' . $fila, $row->Ss_Costo_Delivery)
				->setCellValue('Q' . $fila, $row->Nu_Dias_Delivery . ' DIAS')
				;

				$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getAlignment()->setWrapText(true);

				$fCostoTotalGeneral += $fCostoTotal;//precio en dolares
				$fCostoTotalYuanesGeneral += $fCostoTotalYuanes;//precio en dolares
				$fCbmTotalGeneral += $fCbmTotal;
				$fTotalCajasGeneral += $fTotalCajas;
				$fTotalCostoEnvioGeneral += $row->Ss_Costo_Delivery;

				$iCounter++;
				$fila++;
			}
			
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, 'TOTAL')
            ->setCellValue('K' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($fTotalCajasGeneral, 2, '.', ','))
			->setCellValue('O' . $fila, numberFormat($fCbmTotalGeneral, 2, '.', ','))
			->setCellValue('P' . $fila, numberFormat($fTotalCostoEnvioGeneral, 2, '.', ','));

			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':J' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':R' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':R' . $fila)->getFont()->setBold(true);

			//SUMAR I y M
			$objPHPExcel->getActiveSheet()
			->getStyle('D' . $fila . ':' . 'K' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FF500B')
					),
					'font'  => array(
						'color' => array('rgb' => 'FFFFFF'),
					),
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('M' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FF500B')
					),
					'font'  => array(
						'color' => array('rgb' => 'FFFFFF'),
					),
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('O' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FF500B')
					),
					'font'  => array(
						'color' => array('rgb' => 'FFFFFF'),
					),
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('P' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FF500B')
					),
					'font'  => array(
						'color' => array('rgb' => 'FFFFFF'),
					),
				)
			);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('F' . $fila, 'USD')
			->setCellValue('G' . $fila, 'RMB')
			;
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_center);
			
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila. ':M' . $fila)->applyFromArray($BStyle_background_sub_tittle);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':M' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':M' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':M' . $fila);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K'.$fila, 'IMPORTES A ABONAR');
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fCostoTotalGeneralRMB = round($fCostoTotalGeneral * $data[0]->Ss_Tipo_Cambio, 2);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, 'TOTAL DE LA COMPRA');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('F' . $fila, $fCostoTotalGeneral + $fTotalCostoEnvioGeneral)
            ->setCellValue('G' . $fila, $fCostoTotalGeneralRMB);

			//FORUMAL DE COMISIOJN DE CONSOLIDA TRADING 5%
			$fComisionTotal = 250;
			$fTotalComisionGeneral = ($fCostoTotalGeneral * 0.05);
			if( $fTotalComisionGeneral > 250 ){//=SI((F26*0.05)>250,F26*0.05,250)
				$fComisionTotal = $fTotalComisionGeneral;
			}
			$fComisionTotalRMB = round($fComisionTotal * $data[0]->Ss_Tipo_Cambio, 2);

			//DERECHA
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':M' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('K' . $fila, '1er PAGO');
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':M' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('L' . $fila, $fCostoTotalGeneral + $fComisionTotal);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':M' . $fila)->applyFromArray($style_align_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('M' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':M' . $fila)->applyFromArray($style_align_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('N' . $fila, 'VALOR EXW+ BROKER');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('N' . $fila . ':O' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->applyFromArray($style_align_center);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, 'COMISION BROKER');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('F' . $fila, $fComisionTotal)
			->setCellValue('G' . $fila, $fComisionTotalRMB);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':G' . $fila)->applyFromArray($BStyle_bottom);

			//DERECHA
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':M' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('K' . $fila, '2do PAGO');
			$objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':M' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('L' . $fila, 'CONSOLIDADO');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':M' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('N' . $fila, 'SERV LOGISTICO+ IMPUESTOS');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('N' . $fila . ':O' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->applyFromArray($style_align_center);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($BStyle_background_name_label);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, 'TOTAL A PAGAR CHINA');
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('F' . $fila, $fCostoTotalGeneral + $fComisionTotal)
			->setCellValue('G' . $fila, $fCostoTotalGeneralRMB + $fComisionTotalRMB);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, 'CUENTAS BANCARIAS PERÚ');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':I' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':I' . $fila)->applyFromArray($styleArrayAllborder);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(25);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, ' - CUENTA: CORRIENTE EN DÓLARES');
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);
			
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, ' - TITULAR: GRUPO PROBUSINESS SAC');
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila. ':F' . $fila)->applyFromArray($BStyle_background_sub_tittle);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D'.$fila, 'BCP');
			
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila. ':I' . $fila)->applyFromArray($BStyle_background_sub_tittle);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G'.$fila, 'INTERBANK');
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('D' . $fila, ' - NRO.CUENTA EN DOLARES: 191-9840556-1-63');
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);
			
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('G' . $fila, ' - NRO.CUENTA EN DOLARES: 200-3001727696');
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':I' . $fila)->applyFromArray($BStyle_bottom);
			
			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);

			$fila++;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila . ':R' . $fila)->applyFromArray($BStyle_bottom);
			//FIN DE GENERAR EXCEL
		} else {
			$fila=3;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A' . $fila, 'No hay registro');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':R' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
		}
		
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function downloadImage($id){
		//echo "hola";
		$objPedido = $this->PedidosGarantizadosModel->getDownloadImage($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
		$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

		//$file="assets/img/arturo.jpeg";
		if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
			die('file not found');
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($objPedido->Txt_Url_Imagen_Producto);
			exit;
		}
	}

	public function addFileProveedor(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->addFileProveedor($this->input->post(), $_FILES));
	}
    	
	public function descargarDocumentoPagoGarantizado($id){
		//echo "hola";
		$objPedido = $this->PedidosGarantizadosModel->descargarDocumentoPagoGarantizado($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		$objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
		$objPedido->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

		//$file="assets/img/arturo.jpeg";
		if(!file_exists($objPedido->Txt_Url_Imagen_Producto)){
			die('file not found');
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($objPedido->Txt_Url_Imagen_Producto));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($objPedido->Txt_Url_Imagen_Producto);
			exit;
		}
    }
    	
	public function descargarDocumentoPagoGarantizadov2($id){
		//echo "hola";
		$objPedido = $this->PedidosGarantizadosModel->descargarDocumentoPagoGarantizado($this->security->xss_clean($id));
		//array_debug($objPedido);
		
		if(is_object($objPedido)){
			$response = array(
				'status' => 'success',
				'url_image' => $objPedido->Txt_Url_Imagen_Producto
			);
			echo json_encode($response);
		} else {
			$response = array(
				'status' => 'error',
				'message' => 'sin registro'
			);
			echo json_encode($response);
		}
    }

	public function sendMessage(){
		//array_debug($this->user->ID_Usuario);
		//array_debug($this->input->post());
		echo json_encode($this->PedidosGarantizadosModel->sendMessage($this->input->post()));
		exit();
	}

	public function viewChatItem($id){
		echo json_encode($this->PedidosGarantizadosModel->viewChatItem($id));
		exit();
	}

	public function asignarUsuarioPedidoChina(){
		//array_debug($this->input->post());
		echo json_encode($this->PedidosGarantizadosModel->asignarUsuarioPedidoChina($this->input->post()));
		exit();
	}

	public function removerAsignarPedido($ID, $id_usuario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->removerAsignarPedido($this->security->xss_clean($ID), $this->security->xss_clean($id_usuario)));
	}

	public function cambiarEstadoImpotacionIntegral($ID, $Nu_Estado, $sCorrelativoCotizacion){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGarantizadosModel->cambiarEstadoImpotacionIntegral($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativoCotizacion)));
	}
}
