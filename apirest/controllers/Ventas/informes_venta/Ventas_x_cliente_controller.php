<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas_x_cliente_controller extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/Ventas_x_cliente_model');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/ventas_x_cliente_view');
			$this->load->view('footer', array("js_ventas_x_cliente" => true));
		}
	}
	
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->Ventas_x_cliente_model->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $fSubTotal = 0.00;
            $fIGV = 0.00;
            $fDescuento = 0.00;
            $fTotal = 0.00;
            
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['ID_Entidad'] = $row->ID_Entidad;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
                $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['ID_Moneda'] = $row->ID_Moneda;
                $rows['No_Signo'] = $row->No_Signo;
                
                //$rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
                //Obtener tipo de cambio solo si no es moneda nacional PEN = 1 Nu_Valor_FE_Moneda
                $rows['Ss_Tipo_Cambio'] = 0.00;
                if($row->Nu_Valor_FE_Moneda!=1) {
                    $arrParamsMoneda = array(
                        "ID_Empresa" => $row->ID_Empresa,
                        "ID_Moneda" => $row->ID_Moneda,
                        "Fe_Emision" => $row->Fe_Emision
                    );
                    $arrTipoCambio = $this->HelperModel->obtenerTipoCambio($arrParamsMoneda);
                    if(is_object($arrTipoCambio))
                        $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $arrTipoCambio->Ss_Venta_Oficial : $arrTipoCambio->Ss_Venta_Oficial);
                }

                $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
                $rows['No_Producto'] = $row->No_Producto;
                $rows['Qt_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
                $rows['Ss_Precio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Precio : -$row->Ss_Precio);

                //OBTENER CANTIDAD DE REGISTROS X DOCUMENTO DE VENTA DETALLE
                $arrCantidadItemDocumentoVentaDetalle = $this->HelperModel->getCantidadItemDocumentoVentaDetalle($row->ID_Documento_Cabecera);
                if($arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento > 0 && $row->Ss_Descuento > 0.00) {
                    $row->Ss_Descuento_Global = ($row->Ss_Descuento_Global / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
                    $row->Ss_Descuento = ($row->Ss_Descuento / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
                    $row->Ss_Descuento_Impuesto = ($row->Ss_Descuento_Impuesto / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);

                    $row->Ss_SubTotal = $row->Ss_SubTotal - $row->Ss_Descuento;
                    $row->Ss_Impuesto = $row->Ss_Impuesto - $row->Ss_Descuento_Impuesto;
                    $row->Ss_Total = $row->Ss_Total - $row->Ss_Descuento_Global;
                }

                // Calculando x tipo de moneda
                $fSubTotal = $row->Ss_SubTotal;
                $fImpuesto = $row->Ss_Impuesto;
                $fTotal = $row->Ss_Total;
                $fTotalExtranjero = 0.00;
                if ( $row->Nu_Codigo_Moneda != 1) {//Moneda extranjera
                    if ( $row->ID_Tipo_Documento != 5 ) {// 5 = N/C
                        $fSubTotal = $fSubTotal * $row->Ss_Tipo_Cambio;
                        $fImpuesto = $fImpuesto * $row->Ss_Tipo_Cambio;
                        $fTotal = $fTotal * $row->Ss_Tipo_Cambio;
                    } else {
                        $fSubTotal = $fSubTotal * $row->Ss_Tipo_Cambio_Modificar;
                        $fImpuesto = $fImpuesto * $row->Ss_Tipo_Cambio_Modificar;
                        $fTotal = $fTotal * $row->Ss_Tipo_Cambio_Modificar;
                    }
                    $fTotalExtranjero = $row->Ss_Total;
                }

                if ( $row->ID_Tipo_Documento != 2 )
                    $rows['Ss_SubTotal'] = ($row->ID_Tipo_Documento != 5 ? $fSubTotal : -$fSubTotal);
                else
                    $rows['Ss_SubTotal'] = $fTotal;
        
                if ( $row->ID_Tipo_Documento != 2 )
                    $rows['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? $fImpuesto : -$fImpuesto);
                else
                    $rows['Ss_IGV'] = 0;

                $rows['Ss_Descuento'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Descuento_Producto : -$row->Ss_Descuento_Producto);
                $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $fTotal : -$fTotal);
                $rows['Ss_Total_Extranjero'] = ($row->ID_Tipo_Documento != 5 ? $fTotalExtranjero : -$fTotalExtranjero);

                $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
                $data[] = (object)$rows;
            }
            return array(
              'sStatus' => 'success',
              'arrData' => $data,
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function sendReporte(){
        $arrParams = array(
            'Fe_Inicio' => $this->input->post('Fe_Inicio'),
            'Fe_Fin' => $this->input->post('Fe_Fin'),
            'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
            'ID_Serie_Documento' => $this->input->post('ID_Serie_Documento'),
            'ID_Numero_Documento' => $this->input->post('ID_Numero_Documento'),
            'Nu_Estado_Documento' => $this->input->post('Nu_Estado_Documento'),
            'iIdCliente' => $this->input->post('iIdCliente'),
            'sNombreCliente' => $this->input->post('sNombreCliente'),
            'iIdItem' => $this->input->post('iIdItem'),
            'sNombreItem' => $this->input->post('sNombreItem'),
            'ID_Almacen' => $this->input->post('ID_Almacen'),
            "iFiltroBusquedaNombre" => $this->input->post('iFiltroBusquedaNombre'),
            "ID_Familia" => $this->input->post('ID_Familia'),
            "ID_Sub_Familia" => $this->input->post('ID_Sub_Familia'),
            "ID_Marca" => $this->input->post('ID_Marca'),
            "ID_Variante_Item" => $this->input->post('ID_Variante_Item'),
            "ID_Variante_Item_Detalle_1" => $this->input->post('ID_Variante_Item_Detalle_1'),
            "ID_Variante_Item2" => $this->input->post('ID_Variante_Item2'),
            "ID_Variante_Item_Detalle_2" => $this->input->post('ID_Variante_Item_Detalle_2'),
            "ID_Variante_Item3" => $this->input->post('ID_Variante_Item3'),
            "ID_Variante_Item_Detalle_3" => $this->input->post('ID_Variante_Item_Detalle_3'),
            'Nu_Tipo_Impuesto' => $this->input->post('Nu_Tipo_Impuesto')
        );
        echo json_encode($this->getReporte($arrParams));
    }
    
	public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoReporte, $ID_Almacen,
    $iFiltroBusquedaNombre,
    $ID_Familia,
    $ID_Sub_Familia,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3, $Nu_Tipo_Impuesto
    ){
        $this->load->library('FormatoLibroSunatPDF');
		
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iTipoReporte = $this->security->xss_clean($iTipoReporte);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $ID_Familia   = $this->security->xss_clean($ID_Familia);
        $ID_Sub_Familia   = $this->security->xss_clean($ID_Sub_Familia);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$fileNamePDF = "Reporte_Ventas_x_Cliente_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $arrCabecera = array (
            "Fe_Inicio" => ToDateBD($Fe_Inicio),
            "Fe_Fin" => ToDateBD($Fe_Fin),
            "iTipoReporte" => $iTipoReporte,
        );
        
        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'ID_Tipo_Documento' => $ID_Tipo_Documento,
            'ID_Serie_Documento' => $ID_Serie_Documento,
            'ID_Numero_Documento' => $ID_Numero_Documento,
            'Nu_Estado_Documento' => $Nu_Estado_Documento,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iTipoReporte' => $iTipoReporte,
            'ID_Almacen' => $ID_Almacen,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "ID_Familia" => $ID_Familia,
            "ID_Sub_Familia" => $ID_Sub_Familia,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto
        );

		ob_start();
		$file = $this->load->view('Ventas/informes_venta/pdf/ventas_x_cliente_pdf', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getReporte($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
		$pdf->SetAuthor('Laesystems');
		$pdf->SetTitle('Laesystems - Reporte Ventas por Cliente');
	
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
	}

     public function ReporteVentaXClienteLista(){
        
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->Ventas_x_cliente_model->getReporte_()));
  }

   public function CancelarReporte(){
        echo $this->Ventas_x_cliente_model->CancelarReporte($this->input->post("ID_Reporte"));
    }

    public function BajarReporte($ID_Reporte){
        $row = $this->Ventas_x_cliente_model->getReporteRow($ID_Reporte);
        if($row){
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
        }

    }

  public function CrearReporte(){
      echo $this->Ventas_x_cliente_model->CrearReporte($this->input->post());
  }

  public function ReporteVentaXClienteBG(){
    $this->FileReporte = "";
    $this->FileName    = ""; 


    if(!is_cli()) // solo se ejecuta en terminal
        exit();

    // $this->user->ID_Empresa = 272;
    // $fYear = "2022";
    $row = $this->Ventas_x_cliente_model->getReporteBG();

    if(!$row)
        exit();

    $this->user->ID_Empresa = $row->ID_Empresa;
    
    $Data = unserialize($row->Txt_Parametro);
    //$this->user->ID_Organizacion = $Data["ID_Organizacion"];
    //$Data["fYear"]="2022";
    $arr = array(
        'Fe_Inicio' => date("Y-m-d G:i:s"),
        'ID_Estatus' => 1
     );
    //$this->empresa = $this->ConfiguracionModel->obtenerEmpresa_();
    $this->Ventas_x_cliente_model->UpdateReporteBG($arr,$row->ID_Reporte);

    if($row->Nu_Tipo_Formato==1){
            //echo "\n formato 1 Excel\n";
            $result = $this->sendReporteEXCEL(
                        $Data["Fe_Inicio"],
                        $Data["Fe_Fin"],
                        $Data["ID_Tipo_Documento"],
                        $Data["ID_Serie_Documento"],
                        $Data["ID_Numero_Documento"],
                        $Data["Nu_Estado_Documento"],
                        $Data["iIdCliente"],
                        $Data["sNombreCliente"],
                        $Data["iIdItem"],
                        $Data["sNombreItem"],
                        $Data["iTipoReporte"],
                        $Data["ID_Almacen"],
                        $Data["iFiltroBusquedaNombre"],
                        $Data["ID_Familia"],
                        $Data["ID_Sub_Familia"],
                        $Data["ID_Marca"],
                        $Data["ID_Variante_Item"],
                        $Data["ID_Variante_Item_Detalle_1"],
                        $Data["ID_Variante_Item2"],
                        $Data["ID_Variante_Item_Detalle_2"],
                        $Data["ID_Variante_Item3"],
                        $Data["ID_Variante_Item_Detalle_3"],
                        $Data["Nu_Tipo_Impuesto"]
                    );
        }

       if($result){
            //echo "\nfinal correcto";
             $arr = array(
                'Txt_Archivo' => $this->FileReporte,
                'Txt_Nombre_Archivo' => $this->FileName,
                'Fe_Finalizado' => date("Y-m-d G:i:s"),
                'ID_Estatus' => 2
             );
        }else{
          //echo "\nfinal incorrectcorrecto";
            $ID_Reporte = $row->ID_Reporte;
             $arr = array(
                'Fe_Finalizado' => date("Y-m-d G:i:s"),
                'ID_Estatus' => 4
             );
        }

        $this->Ventas_x_cliente_model->UpdateReporteBG($arr,$row->ID_Reporte);

  }
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoReporte, $ID_Almacen,
    $iFiltroBusquedaNombre,
    $ID_Familia,
    $ID_Sub_Familia,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3, $Nu_Tipo_Impuesto
    ){
        $this->load->library('Excel');
	    
        $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
        $Fe_Fin = $this->security->xss_clean($Fe_Fin);
        $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
        $ID_Serie_Documento = $this->security->xss_clean($ID_Serie_Documento);
        $ID_Numero_Documento = $this->security->xss_clean($ID_Numero_Documento);
        $Nu_Estado_Documento = $this->security->xss_clean($Nu_Estado_Documento);
        $iIdCliente = $this->security->xss_clean($iIdCliente);
        $sNombreCliente = $this->security->xss_clean($sNombreCliente);
        $iIdItem = $this->security->xss_clean($iIdItem);
        $sNombreItem = $this->security->xss_clean($sNombreItem);
        $iTipoReporte = $this->security->xss_clean($iTipoReporte);
        $ID_Almacen = $this->security->xss_clean($ID_Almacen);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $ID_Familia   = $this->security->xss_clean($ID_Familia);
        $ID_Sub_Familia   = $this->security->xss_clean($ID_Sub_Familia);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
        
		$this->FileName = $fileNameExcel = "Reporte_Ventas_x_Cliente_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Ventas por Cliente');
        
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
	    $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('B1', $this->empresa->No_Empresa)
        ->setCellValue('E2', 'Informe de Ventas por Cliente')
        ->setCellValue('E3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
        
        $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($style_align_center);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E2:K2');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('E3:K3');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("8");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("20");

        $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('B5:D5')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('G5:N5')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O6')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P6')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A5', 'Fecha');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('B5', 'Documento');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B5:D5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('E5', 'Moneda')
        ->setCellValue('F5', 'Tipo');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G5', 'Producto');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G5:N5');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A6', 'Emisión')
        ->setCellValue('B6', 'Tipo')
        ->setCellValue('C6', 'Serie')
        ->setCellValue('D6', 'Número')
        ->setCellValue('F6', 'Cambio')
        ->setCellValue('G6', 'Codigo')
        ->setCellValue('H6', 'Nombre')
        ->setCellValue('I6', 'Cantidad')
        ->setCellValue('J6', 'Precio')
        ->setCellValue('K6', 'SubTotal S/')
        ->setCellValue('L6', 'I.G.V S/')
        ->setCellValue('M6', 'Dscto. S/')
        ->setCellValue('N6', 'Total S/')
        ->setCellValue('O6', 'Total M. Ex.')
        ->setCellValue('P6', 'Estado')
        ;
        
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($style_align_center);
        
        $objPHPExcel->getActiveSheet()->freezePane('A7');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 7;
        
        $arrParams = array(
            'Fe_Inicio' => $Fe_Inicio,
            'Fe_Fin' => $Fe_Fin,
            'ID_Tipo_Documento' => $ID_Tipo_Documento,
            'ID_Serie_Documento' => $ID_Serie_Documento,
            'ID_Numero_Documento' => $ID_Numero_Documento,
            'Nu_Estado_Documento' => $Nu_Estado_Documento,
            'iIdCliente' => $iIdCliente,
            'sNombreCliente' => $sNombreCliente,
            'iIdItem' => $iIdItem,
            'sNombreItem' => $sNombreItem,
            'iTipoReporte' => $iTipoReporte,
            'ID_Almacen' => $ID_Almacen,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "ID_Familia" => $ID_Familia,
            "ID_Sub_Familia" => $ID_Sub_Familia,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto
        );
        $arrData = $this->getReporte($arrParams);
        
        if ( $arrData['sStatus'] == 'success' ) {
            $ID_Entidad = '';
            $counter = 0;
            
            $subtotal_s = 0.00;
            $igv_s = 0.00;
            $descuento_s = 0.00;
            $total_s = 0.00;
            
            $sum_cantidad = 0.000000;
            $sum_subtotal_s = 0.00;
            $sum_descuento_s = 0.00;
            $sum_igv_s = 0.00;
            $sum_total_s = 0.00;
            $sum_total_d = 0.00;
            
            $sum_general_cantidad = 0.000000;
            $sum_general_subtotal_s = 0.00;
            $sum_general_descuento_s = 0.00;
            $sum_general_igv_s = 0.00;
            $sum_general_total_s = 0.00;
            $sum_general_total_d = 0.00;
            
            $ID_Almacen = 0; $counter_almacen = 0; $sum_almacen_compras_cantidad = 0.000000; $sum_almacen_compras_subtotal_s = 0.00; $sum_almacen_compras_descuento_s = 0.00; $sum_almacen_compras_igv_s = 0.00; $sum_almacen_compras_total_s = 0.00; $sum_almacen_compras_total_d = 0.00;
            foreach ($arrData['arrData'] as $row) {
                if ($ID_Entidad != $row->ID_Entidad || $ID_Almacen != $row->ID_Almacen) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('H' . $fila, 'Total')
                        ->setCellValue('I' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
                        ->setCellValue('K' . $fila, numberFormat($sum_subtotal_s, 2, '.', ','))
                        ->setCellValue('L' . $fila, numberFormat($sum_igv_s, 2, '.', ','))
                        ->setCellValue('M' . $fila, numberFormat($sum_descuento_s, 2, '.', ','))
                        ->setCellValue('N' . $fila, numberFormat($sum_total_s, 2, '.', ','))
                        ->setCellValue('O' . $fila, numberFormat($sum_total_d, 2, '.', ','));
                        
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'O' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'E7E7E7')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                        $fila++;
                        
                        $sum_cantidad = 0.000000;
                        $sum_subtotal_s = 0.00;
                        $sum_igv_s = 0.00;
                        $sum_descuento_s = 0.00;
                        $sum_total_s = 0.00;
                        $sum_total_d = 0.00;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('H' . $fila, 'Total')
                            ->setCellValue('I' . $fila, numberFormat($sum_almacen_compras_cantidad, 3, '.', ','))
                            ->setCellValue('K' . $fila, numberFormat($sum_almacen_compras_subtotal_s, 2, '.', ','))
                            ->setCellValue('L' . $fila, numberFormat($sum_almacen_compras_igv_s, 2, '.', ','))
                            ->setCellValue('M' . $fila, numberFormat($sum_almacen_compras_descuento_s, 2, '.', ','))
                            ->setCellValue('N' . $fila, numberFormat($sum_almacen_compras_total_s, 2, '.', ','))
                            ->setCellValue('O' . $fila, numberFormat($sum_almacen_compras_total_d, 2, '.', ','));
                            
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                            
                            $objPHPExcel->getActiveSheet()
                            ->getStyle('A' . $fila . ':' . 'Q' . $fila)
                            ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E7E7E7')
                                    )
                                )
                            );
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                        
                            $fila++;
                            
                            $sum_almacen_compras_cantidad = 0.000000;
                            $sum_almacen_compras_subtotal_s = 0.00;
                            $sum_almacen_compras_igv_s = 0.00;
                            $sum_almacen_compras_descuento_s = 0.00;
                            $sum_almacen_compras_total_s = 0.00;
                            $sum_almacen_compras_total_d = 0.00;
                        }

                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'Almacén')
                        ->setCellValue('B' . $fila, $row->No_Almacen);

                        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':N'. $fila);
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                        
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $fila . ':' . 'Q' . $fila)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'F2F5F5')
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                        
                        $ID_Almacen = $row->ID_Almacen;
                        $fila++;
                    }
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'Cliente')
                    ->setCellValue('B' . $fila, $row->Nu_Documento_Identidad)
                    ->setCellValue('C' . $fila, $row->No_Entidad)
                    ;
                    
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
                    
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('A' . $fila . ':' . 'O' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F2F5F5')
                            )
                        )
                    );
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
                    
                    $ID_Entidad = $row->ID_Entidad;
                    $fila++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_left);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':' . 'O' . $fila)->applyFromArray($style_align_right);
                $objPHPExcel->getActiveSheet()->getStyle('P' . $fila)->applyFromArray($style_align_center);

                if ($iTipoReporte==0) {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, $row->Fe_Emision)
                    ->setCellValue('B' . $fila, $row->No_Tipo_Documento_Breve)
                    ->setCellValue('C' . $fila, $row->ID_Serie_Documento)
                    ->setCellValue('D' . $fila, $row->ID_Numero_Documento)
                    ->setCellValue('E' . $fila, $row->No_Signo)
                    ->setCellValue('F' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
                    ->setCellValue('G' . $fila, $row->Nu_Codigo_Barra)
                    ->setCellValue('H' . $fila, $row->No_Producto)
                    ->setCellValue('I' . $fila, numberFormat($row->Qt_Producto, 3, '.', ','))
                    ->setCellValue('J' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
                    ->setCellValue('K' . $fila, numberFormat($row->Ss_SubTotal, 2, '.', ','))
                    ->setCellValue('L' . $fila, numberFormat($row->Ss_IGV, 2, '.', ','))
                    ->setCellValue('M' . $fila, numberFormat($row->Ss_Descuento, 2, '.', ','))
                    ->setCellValue('N' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
                    ->setCellValue('O' . $fila, numberFormat($row->Ss_Total_Extranjero, 2, '.', ''))
                    ->setCellValue('P' . $fila, $row->No_Estado)
                    ;
                    $fila++;
                }

                $sum_cantidad += $row->Qt_Producto;
                $sum_subtotal_s += $row->Ss_SubTotal;
                $sum_igv_s += $row->Ss_IGV;
                $sum_descuento_s += $row->Ss_Descuento;
                $sum_total_s += $row->Ss_Total;
                $sum_total_d += $row->Ss_Total_Extranjero;
              
                $sum_almacen_compras_cantidad += $row->Qt_Producto;
                $sum_almacen_compras_subtotal_s += $row->Ss_SubTotal;
                $sum_almacen_compras_igv_s += $row->Ss_IGV;
                $sum_almacen_compras_descuento_s += $row->Ss_Descuento;
                $sum_almacen_compras_total_s += $row->Ss_Total;
                $sum_almacen_compras_total_d += $row->Ss_Total_Extranjero;
                
                $sum_general_cantidad += $row->Qt_Producto;
                $sum_general_subtotal_s += $row->Ss_SubTotal;
                $sum_general_igv_s += $row->Ss_IGV;
                $sum_general_descuento_s += $row->Ss_Descuento;
                $sum_general_total_s += $row->Ss_Total;
                $sum_general_total_d += $row->Ss_Total_Extranjero;
                
                $counter++;
                $counter_almacen++;
            }
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total')
            ->setCellValue('I' . $fila, numberFormat($sum_cantidad, 3, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_subtotal_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_igv_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_descuento_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_total_s, 2, '.', ','))
            ->setCellValue('O' . $fila, numberFormat($sum_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'O' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total Almacén')
            ->setCellValue('I' . $fila, numberFormat($sum_almacen_compras_cantidad, 3, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_almacen_compras_subtotal_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_almacen_compras_igv_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_almacen_compras_descuento_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_almacen_compras_total_s, 2, '.', ','))
            ->setCellValue('O' . $fila, numberFormat($sum_almacen_compras_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'Q' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
            
            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Total General')
            ->setCellValue('I' . $fila, numberFormat($sum_general_cantidad, 3, '.', ','))
            ->setCellValue('K' . $fila, numberFormat($sum_general_subtotal_s, 2, '.', ','))
            ->setCellValue('L' . $fila, numberFormat($sum_general_igv_s, 2, '.', ','))
            ->setCellValue('M' . $fila, numberFormat($sum_general_descuento_s, 2, '.', ','))
            ->setCellValue('N' . $fila, numberFormat($sum_general_total_s, 2, '.', ','))
            ->setCellValue('O' . $fila, numberFormat($sum_general_total_d, 2, '.', ','));
            
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                        
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'O' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'E7E7E7')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
        } else {

        }// /. if - else arrData
        
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        try {
            $this->FileReporte = md5(time().mt_rand(1,1000000));
            $objWriter->save(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte);
             return true;
        } catch (Exception $e) {
            return false;
        }
	}
}
