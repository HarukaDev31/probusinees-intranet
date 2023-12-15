<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosAgente extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('AgenteCompra/PedidosAgenteModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('AgenteCompra/PedidosAgenteView');
			$this->load->view('footer_v2', array("js_pedidos_agente" => true));
		}
	}

	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->PedidosAgenteModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = $row->ID_Pedido_Cabecera;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto . "<br>" . $row->Txt_Email_Contacto;
            $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;
			
			//EXCEL cliente de pedido
			$rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="generarExcelPedidoCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel text-green fa-2x"></i></button>';

			//PDF cliente de pedido
			//$rows[] = '<button class="btn btn-xs btn-link" alt="PDF" title="PDF" href="javascript:void(0)" onclick="generarPDFPedidoCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-pdf text-danger fa-2x"></i></button>';

            //$rows[] = round($row->Qt_Total, 2);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
            //$rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1,0);">Pendiente</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Agente_Compra_Correlativo . '\');">Garantizado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

			$btn_ver = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			if ( $this->MenuModel->verificarAccesoMenuInterno($sMethod)->Nu_Eliminar == 0)
				$btn_ver = '';
			$rows[] = $btn_ver;

			if($this->user->Nu_Tipo_Privilegio_Acceso==1){//1=probusiness
				$btn_asignar_usuario = '<button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="asignarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-user fa-2x" aria-hidden="true"></i></button>';
				if($row->ID_Usuario_Pedido){
					$btn_asignar_usuario = '<span class="badge bg-secondary">Asignado</span>';
					$btn_asignar_usuario .= '<br><button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="removerAsignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->ID_Usuario_Pedido . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
				}
			}

			$rows[] = $btn_asignar_usuario;
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAgenteModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($id_correlativo)));
	}

	public function crudPedidoGrupal(){
		//array_debug($this->input->post());
		
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('EID_Empresa'),
			'ID_Organizacion' => $this->input->post('EID_Organizacion'),
			'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
			'No_Entidad' => $this->input->post('No_Entidad'),
			'Nu_Celular_Entidad' => $this->input->post('Nu_Celular_Entidad'),
			'Txt_Email_Entidad' => $this->input->post('Txt_Email_Entidad'),
		);
		echo json_encode($this->PedidosAgenteModel->actualizarPedido(
				array(
					'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
					'ID_Entidad' => $this->input->post('EID_Entidad'),
				),
				$data,
				$this->input->post('addProducto')
			)
		);
	}

	//generar cotización PDF para pedido de cliente	
	public function generarPDFPedidoCliente($ID){
        $data = $this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			$this->load->library('Pdf');
			
			$this->load->library('EnLetras', 'el');
			$EnLetras = new EnLetras();

			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
			
			ob_start();
			$file = $this->load->view('AgenteCompra/pdf/PedidosAgentePDFView', array(
				'arrDataEmpresa' => $data,
				'arrData' => $data,
				'totalEnLetras'	=> $EnLetras->ValorEnLetras($data[0]->Ss_Total, $data[0]->No_Moneda),
			));
			$html = ob_get_contents();
			ob_end_clean();
			
			$pdf->SetAuthor('ProBusiness');
			$pdf->SetTitle('ProBusiness_Cotizacion_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Pedido_Cabecera);
		
			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-20, PDF_MARGIN_RIGHT-5);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
			$pdf->AddPage();
				
			$sNombreLogo=str_replace(' ', '_', $data[0]->No_Logo_Empresa);
			$sRutaArchivoLogoCliente = $this->logo_cliente_path . $sNombreLogo;
			$sUrlImagen = $data[0]->No_Imagen_Logo_Empresa;

			$sCssFontFamily='Arial';
			$format_header = '<table border="0" cellspacing="1" cellpadding="0">';
				$format_header .= '<tr>';
					$format_header .= '<td style="width: 50%; text-align: left;">';
						$format_header .= '<img style="height: ' . $data[0]->Nu_Height_Logo_Ticket . 'px; width: ' . $data[0]->Nu_Width_Logo_Ticket . 'px;" src="' . $sUrlImagen . '"><br>';
					$format_header .= '</td>';
					$format_header .= '<td style="width: 50%; text-align: right;">';
						if(!empty($data[0]->No_Empresa_Comercial))
							$format_header .= '<label style="font-size: 11px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa_Comercial . '</b></label><br>';
						else
							$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>' . $data[0]->No_Empresa . '</b></label><br>';
						$format_header .= '<label style="font-size: 10px; font-family: "' . $sCssFontFamily . '", Times, serif;"><b>RUC: ' . $data[0]->Nu_Documento_Identidad_Empresa . '</b></label><br>';
						if(!empty($data[0]->Txt_Direccion_Empresa))
							$format_header .= '<label style="font-size: 9px; font-family: "' . $sCssFontFamily . '", Times, serif;">' . $data[0]->Txt_Direccion_Empresa . ' - ' . $data[0]->No_Departamento . ' - ' . $data[0]->No_Provincia . ' - ' . $data[0]->No_Distrito . '</label><br>';
						if(!empty($data[0]->No_Dominio_Empresa))
							$format_header .= '<label style="color: #000000; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->No_Dominio_Empresa . '</label><br>';
						if(!empty($data[0]->Nu_Celular_Empresa))
							$format_header .= '<label style="color: #868686; font-size: 10px; font-family: "Times New Roman", Times, serif;">Celular: ' . $data[0]->Nu_Celular_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Email_Empresa))
							$format_header .= '<label style="color: #34bdad; font-size: 10px; font-family: "Times New Roman", Times, serif;">Correo: ' . $data[0]->Txt_Email_Empresa . '</label><br>';
						if(!empty($data[0]->Txt_Slogan_Empresa))
							$format_header .= '<label style="color: #979797; font-size: 10px; font-family: "Times New Roman", Times, serif;">' . $data[0]->Txt_Slogan_Empresa . '</label>';
					$format_header .= '</td>';
				$format_header .= '</tr>';
			$format_header .= '</table>';
			$pdf->writeHTML($format_header, true, 0, true, 0);

			$pdf->setFont('helvetica', '', 7);
			$pdf->writeHTML($html, true, false, true, false, '');
			
			$file_name = 'ProBusiness_Cotizacion_' . $data[0]->Nu_Documento_Identidad . '_' . $data[0]->ID_Pedido_Cabecera . ".pdf";
			$pdf->Output($file_name, 'I');
		} else {
			exit();
			//alert('no existe');
		}
	}

	//generar cotización PDF para pedido de cliente	
	public function generarExcelPedidoCliente($ID){
        $data = $this->PedidosAgenteModel->get_by_id($this->security->xss_clean($ID));
		//array_debug($data);

		if( !empty($data) ){
			//GENERAR EXCEL
			$this->load->library('Excel');
	  			
			$fileNameExcel = "AgenteCompra_Pedido_" . $ID . ".xls";
				
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getActiveSheet()->setTitle('Pedido Nro. ' . $ID);
			
			$hoja_activa = 0;
		
			$BStyle_top = array(
			'borders' => array(
				'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_left = array(
			'borders' => array(
				'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_right = array(
			'borders' => array(
				'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
			);
			
			$BStyle_bottom = array(
			'borders' => array(
				'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				)
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
			
			//Title
			$fila=1;
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B1', $this->empresa->No_Empresa)
			->setCellValue('C1', 'SOLICITUD DE COTIZACIÓN NRO. ' . $ID);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila . ':E' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);

			$fila=2;
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo ProBusiness');
			$objDrawing->setDescription('Comunidad de Importadores');
			
			//localhost
			//assets/img/logos/logo_horizontal_probusiness_claro.png?ver=4.0.0
			$objDrawing->setPath('assets/img/logos/logo_horizontal_probusiness_claro.png');
			$objDrawing->setHeight(210);
			$objDrawing->setWidth(200);
			$objDrawing->setCoordinates('B' . $fila);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($style_align_center);
			$objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C1:E1');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:E2');
			$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
			// /. Title

			$fila=2;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'Ofic Perú: Jr. Alberto Bartón 527 Santa Catalina -La Victoria');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':E'.$fila);

			$fila=3;
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->applyFromArray($style_align_center);
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('C'.$fila, 'Ofic China: Shuangchuang Building, No. 1133 Chouzhou North Road, Yiwu City.');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C'.$fila.':E'.$fila);

			$fila = 5;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'NOMBRE')
			->setCellValue('C' . $fila, $data[0]->No_Contacto);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
			
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('E' . $fila, 'RAZÓN SOCIAL')
			->setCellValue('F' . $fila, $data[0]->No_Entidad);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);

			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);

			$fila = 6;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'CORREO')
			->setCellValue('C' . $fila, $data[0]->Txt_Email_Contacto);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('E' . $fila, 'RUC')
			->setCellValue('F' . $fila, $data[0]->Nu_Documento_Identidad);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
			
			$fila = 7;
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('B' . $fila, 'WHATSAPP')
			->setCellValue('C' . $fila, $data[0]->Nu_Celular_Contacto);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':C' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('B' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);
			
			$objPHPExcel->getActiveSheet()
			->getStyle('E' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('E' . $fila, 'PAÍS')
			->setCellValue('F' . $fila, $data[0]->No_Pais_Cliente);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':F' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_left);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);


			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("10");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("40");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("30");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//NRO
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("30");//NRO

			$fila = 9;
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($BStyle_top);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($BStyle_bottom);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila. ':F' . $fila)->applyFromArray($style_align_center);

			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A' . $fila, 'NRO')
			->setCellValue('B' . $fila, 'FOTO DEL PRODUCTO')
			->setCellValue('C' . $fila, 'NOMBRE COMERCIAL')
			->setCellValue('D' . $fila, 'CARACTERÍSTICAS')
			->setCellValue('E' . $fila, 'CANTIDAD')
			->setCellValue('F' . $fila, 'LINK')
			;

			$objPHPExcel->getActiveSheet()
			->getStyle('A' . $fila . ':' . 'F' . $fila)
			->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'E7E7E7')
					)
				)
			);
			
			$fila = 10;
			//$objPHPExcel->getActiveSheet()->freezePane('A' . $fila);//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
				
			$iCounter = 1;
            foreach($data as $row) {				
				$html_data = array("&nbsp;");
				$row->Txt_Descripcion =str_replace($html_data," ",$row->Txt_Descripcion);
		
				$html_data = array("<br>", "<p>", "<br/>");
				$row->Txt_Descripcion =str_replace($html_data,"\n",$row->Txt_Descripcion);
		
				$row->Txt_Descripcion =strip_tags($row->Txt_Descripcion);

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('A' . $fila, $iCounter);

				if( !empty($row->Txt_Url_Imagen_Producto) ){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName($row->Txt_Producto);
					$objDrawing->setDescription($row->Txt_Descripcion);
					
					//pruebas localhost
					//$objDrawing->setPath('assets/img/arturo.jpeg');

					//cloud
					$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
					$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
					if ( file_exists($row->Txt_Url_Imagen_Producto) ) {
						$objDrawing->setPath($row->Txt_Url_Imagen_Producto);
						$objDrawing->setWidthAndHeight(148,500);
						$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(250);
						//$objDrawing->setHeight($objDrawing->getHeight() - ($objDrawing->getHeight() * .25));
						$objDrawing->setResizeProportional(true);

						$objDrawing->setCoordinates('B' . $fila);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					}
				} else {
					$objPHPExcel->setActiveSheetIndex($hoja_activa)
					->setCellValue('B' . $fila, '');
				}

				$objPHPExcel->setActiveSheetIndex($hoja_activa)
				->setCellValue('C' . $fila, $row->Txt_Producto)
				->setCellValue('D' . $fila, $row->Txt_Descripcion)
				->setCellValue('E' . $fila, $row->Qt_Producto)
				->setCellValue('F' . $fila, $row->Txt_Url_Link_Pagina_Producto)
				;
				
				//$objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(160);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getAlignment()->setWrapText(true);

				$objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

				if( !empty($row->Txt_Url_Link_Pagina_Producto) ){
					$objPHPExcel->getActiveSheet()->getCell('F' . $fila)
					->getHyperlink()
					->setUrl($row->Txt_Url_Link_Pagina_Producto)
					->setTooltip('Click para ir a link');

					$link_style_array = [
						'font'  => [
						'color' => ['rgb' => '0000FF'],
						'underline' => 'single'
						]
					];
					$objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($link_style_array);
				}

				$iCounter++;
				$fila++;
			} // /. foreach data

			header('Content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			//FIN DE GENERAR EXCEL
		} else {
			$objPHPExcel->setActiveSheetIndex($hoja_activa)
			->setCellValue('A' . $fila, 'No hay registro');
			$objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
		}
	}

	public function downloadImage($id){
		//echo "hola";
		$objPedido = $this->PedidosAgenteModel->getDownloadImage($this->security->xss_clean($id));
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

	public function asignarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAgenteModel->asignarPedido($this->security->xss_clean($ID)));
	}

	public function removerAsignarPedido($ID, $id_usuario){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosAgenteModel->removerAsignarPedido($this->security->xss_clean($ID), $this->security->xss_clean($id_usuario)));
	}
}
