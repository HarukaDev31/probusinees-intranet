<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 400); //300 seconds = 5 minutes y mas
date_default_timezone_set('America/Lima');

class ReporteUtilidadBrutaController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/ReporteUtilidadBrutaModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/ReporteUtilidadBrutaView');
			$this->load->view('footer', array("js_reporte_utilidad_bruta" => true));
		}
	}
	
  private function getReporte($arrParams){
    $arrResponseModal = $this->ReporteUtilidadBrutaModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
      $fCostoVta = 0.00;
      $fCostoCompra = 0.00;
      $fGanancia = 0.00;
      $sVacio='';      
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        $rows['ID_Almacen'] = $row->ID_Almacen;
        $rows['No_Almacen'] = $row->No_Almacen;
        $rows['ID_Familia'] = $row->ID_Familia;
        $rows['No_Familia'] = $row->No_Familia;
        $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
        $rows['No_Producto'] = $row->No_Producto;
        $rows['No_Signo'] = $row->No_Signo;

        //obtener precio de venta promedio y cantidad total
        $arrParamsDetalle = array_merge(
          $arrParams,
          array(
            "ID_Empresa" => $row->ID_Empresa,
            "ID_Organizacion" => $row->ID_Organizacion,
            "ID_Almacen" => $row->ID_Almacen,
            "ID_Producto" => $row->ID_Producto
          )
        );
        $arrResponseDetalle = $this->ReporteUtilidadBrutaModel->obtenerPrecioCantidadVentaDetalle($arrParamsDetalle);
        if($arrResponseDetalle['status'] == "success"){
          $row->Ss_Precio=$arrResponseDetalle['precio_promedio_venta'];
          $row->Qt_Producto=$arrResponseDetalle['cantidad_venta'];
        }

        //$fCostoVta = round($row->Ss_Precio / $row->Ss_Impuesto, 2);
        //$fCostoCompra = round($row->Ss_Costo / $row->Ss_Impuesto, 2);
        $fCostoVta = round($row->Ss_Precio, 2);
        $fCostoCompra = round($row->Ss_Costo, 2);
        //si el costo de compra es 0 cambiarlo por 1
        //$fCostoCompra = ($fCostoCompra > 0.00 ? $fCostoCompra : 1);
        /*
        if ( $arrParams['Nu_Impuesto'] == 1) {
          $fCostoVta = round($row->Ss_Precio, 2);
          $fCostoCompra = round($row->Ss_Costo, 2);
        }
        */
        $fGanancia = (($fCostoVta - $fCostoCompra) / $fCostoVta);
        $fCalculoGanancia = round($fCostoVta - $fCostoCompra, 2);
        $rows['Ss_Costo'] = $fCostoCompra;
        $rows['Ss_Precio'] = $fCostoVta;
        $rows['Ss_Ganancia'] = round($fCalculoGanancia, 2);
        $rows['Po_Margen_Ganancia'] = round($fGanancia * 100, 4) . ' %';
        $rows['Qt_Producto'] = $row->Qt_Producto;
        
        //buscar descuento por cabecera y detalle
        //enviar id almacen, id producto, id moneda, rango de fecha
        $arrParamsDescuento = array(
          "ID_Empresa" => $row->ID_Empresa,
          "ID_Organizacion" => $row->ID_Organizacion,
          "ID_Almacen" => $row->ID_Almacen,
          "ID_Producto" => $row->ID_Producto,
          "ID_Moneda" => $row->ID_Moneda,
          "Fe_Inicio" => $arrParams['Fe_Inicio'],
          "Fe_Fin" => $arrParams['Fe_Fin']
        );
        $arrResponseDescuento = $this->ReporteUtilidadBrutaModel->getDescuentoDetalle($arrParamsDescuento);
        if(is_object($arrResponseDescuento)){
          $Ss_Descuento_Detalle = $arrResponseDescuento->Ss_Descuento;
          $Ss_Descuento_Impuesto_Detalle = $arrResponseDescuento->Ss_Descuento_Impuesto;
        }

        //buscar descuento por cabecera y detalle
        //enviar id almacen, id producto, id moneda, rango de fecha
        /*
        $arrParamsDescuento = array(
          "ID_Empresa" => $row->ID_Empresa,
          "ID_Organizacion" => $row->ID_Organizacion,
          "ID_Almacen" => $row->ID_Almacen,
          "ID_Producto" => $row->ID_Producto,
          "ID_Moneda" => $row->ID_Moneda,
          "Fe_Inicio" => $arrParams['Fe_Inicio'],
          "Fe_Fin" => $arrParams['Fe_Fin']
        );
        $arrResponseDescuento = $this->ReporteUtilidadBrutaModel->getDescuentoCabecera($arrParamsDescuento);
        if(is_object($arrResponseDescuento)){
          $Ss_Descuento_Cabecera = $arrResponseDescuento->Ss_Descuento;
          $Ss_Descuento_Impuesto_Cabecera = $arrResponseDescuento->Ss_Descuento_Impuesto;
        }
        */
        $Ss_Descuento_Cabecera = $row->Ss_Descuento_Cabecera;
        $Ss_Descuento_Impuesto_Cabecera = $row->Ss_Descuento_Impuesto_Cabecera;

        $Ss_Descuento = ($Ss_Descuento_Detalle + $Ss_Descuento_Impuesto_Detalle) + ($Ss_Descuento_Cabecera + $Ss_Descuento_Impuesto_Cabecera);

        $fUtilidad = round($fCalculoGanancia * $row->Qt_Producto, 2);
        $rows['Ss_Utilidad'] = $fUtilidad;
        $rows['Ss_Descuento'] = round($Ss_Descuento, 2);
        $rows['Ss_Utilidad_Neta'] = $fUtilidad - $Ss_Descuento;

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
      'ID_Moneda' => $this->input->post('ID_Moneda'),
      'iIdFamilia' => $this->input->post('iIdFamilia'),
      'Nu_Impuesto' => $this->input->post('Nu_Impuesto'),
      'iIdItem' => $this->input->post('iIdItem'),
      'sNombreItem' => $this->input->post('sNombreItem'),
      'iIdSubFamilia' => $this->input->post('iIdSubFamilia'),
      'ID_Almacen' => $this->input->post('ID_Almacen'),
    );
    echo json_encode($this->getReporte($arrParams));
    //print_r($this->input->post());
  }

  public function ReporteUtilidadLista(){
        
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->ReporteUtilidadBrutaModel->getReporte_()));
  }

   public function CancelarReporte(){
        echo $this->ReporteUtilidadBrutaModel->CancelarReporte($this->input->post("ID_Reporte"));
    }

    public function BajarReporte($ID_Reporte){
        $row = $this->ReporteUtilidadBrutaModel->getReporteRow($ID_Reporte);
        if($row){
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
        }

    }

  public function CrearReporte(){
      echo $this->ReporteUtilidadBrutaModel->CrearReporte($this->input->post());
  }

  public function ReporteUtilidadBG(){
    $this->FileReporte = "";
    $this->FileName    = ""; 


    if(!is_cli()) // solo se ejecuta en terminal
        exit();

    // $this->user->ID_Empresa = 272;
    // $fYear = "2022";
    $row = $this->ReporteUtilidadBrutaModel->getReporteBG();

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
    $this->ReporteUtilidadBrutaModel->UpdateReporteBG($arr,$row->ID_Reporte);

    if($row->Nu_Tipo_Formato==1){
            //echo "\n formato 1 Excel\n";
            $result = $this->sendReporteEXCEL(
                        $Data["Fe_Inicio"],
                        $Data["Fe_Fin"],
                        $Data["ID_Moneda"],
                        $Data["iIdFamilia"],
                        $Data["Nu_Impuesto"],
                        $Data["iIdItem"],
                        $Data["sNombreItem"],
                        $Data["iIdSubFamilia"],
                        $Data["ID_Almacen"]
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

        $this->ReporteUtilidadBrutaModel->UpdateReporteBG($arr,$row->ID_Reporte);

  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Moneda, $iIdFamilia, $Nu_Impuesto, $iIdItem, $sNombreItem, $iIdSubFamilia, $ID_Almacen){
    $this->load->library('FormatoLibroSunatPDF');

    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Moneda = $this->security->xss_clean($ID_Moneda);
    $iIdFamilia = $this->security->xss_clean($iIdFamilia);
    $Nu_Impuesto = $this->security->xss_clean($Nu_Impuesto);
    $iIdItem = $this->security->xss_clean($iIdItem);
    $sNombreItem = $this->security->xss_clean($sNombreItem);
    $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);

    $fileNamePDF = "reporte_utilidad_bruta_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

    $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
    );

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Moneda' => $ID_Moneda,
      'iIdFamilia' => $iIdFamilia,
      'Nu_Impuesto' => $Nu_Impuesto,
      'iIdItem' => $iIdItem,
      'sNombreItem' => $sNombreItem,
      'iIdSubFamilia' => $iIdSubFamilia,
      'ID_Almacen' => $ID_Almacen,
    );

    ob_start();
    $file = $this->load->view('Ventas/informes_venta/pdf/ReporteUtilidadBrutaViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('Laesystems');
    $pdf->SetTitle('Laesystems - Reporte Utilidad Bruta');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 7);
    
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Moneda, $iIdFamilia, $Nu_Impuesto, $iIdItem, $sNombreItem, $iIdSubFamilia, $ID_Almacen){
    $this->load->library('Excel');
	  
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Moneda = $this->security->xss_clean($ID_Moneda);
    $iIdFamilia = $this->security->xss_clean($iIdFamilia);
    $Nu_Impuesto = $this->security->xss_clean($Nu_Impuesto);
    $iIdItem = $this->security->xss_clean($iIdItem);
    $sNombreItem = $this->security->xss_clean($sNombreItem);
    $iIdSubFamilia = $this->security->xss_clean($iIdSubFamilia);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    
		$this->FileName = $fileNameExcel = "reporte_utilidad_bruta_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Reporte Utilidad Bruta');
      
    $hoja_activa = 0;
	    
    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Moneda' => $ID_Moneda,
      'iIdFamilia' => $iIdFamilia,
      'Nu_Impuesto' => $Nu_Impuesto,
      'iIdItem' => $iIdItem,
      'sNombreItem' => $sNombreItem,
      'iIdSubFamilia' => $iIdSubFamilia,
      'ID_Almacen' => $ID_Almacen,
    );
    $arrData = $this->getReporte($arrParams);
      
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
    ->setCellValue('A1', $this->empresa->No_Empresa)
    ->setCellValue('C2', 'Informe de Reporte Utilidad Bruta')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:G2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:G3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("40");
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("10");
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("30");

    $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($BStyle_top);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($BStyle_bottom);

    $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($style_align_center);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Código')
    ->setCellValue('B5', 'Item')
    ->setCellValue('C5', 'Moneda')
    ->setCellValue('D5', 'Último Costo Compra')
    ->setCellValue('E5', 'Costo Promedio Venta')
    ->setCellValue('F5', 'Ganancia')
    ->setCellValue('G5', 'Margen')
    ->setCellValue('H5', 'Cantidad')
    ->setCellValue('I5', 'Utilidad')
    ->setCellValue('J5', 'Descuento')
    ->setCellValue('K5', 'Utilidad - Descuento')
    ;
    
    $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE

    $fila = 6;

    if ( $arrData['sStatus'] == 'success' ) {
      $counter = 0; $ID_Familia = '';
      $sum_cantidad = 0.00; $sum_total = 0.00; $sum_descuento = 0.00; $sum_utilidad_neta = 0.00;
      $sum_general_cantidad = 0.00; $sum_general_total = 0.00; $sum_general_total_descuento = 0.00; $sum_general_total_utilidad_neta = 0.00;
      $ID_Almacen = 0; $sum_cantidad_almacen = 0.00; $sum_total_almacen = 0.00; $sum_total_descuento_almacen = 0.00; $sum_total_utilidad_neta_almacen = 0.00; $counter_almacen = 0;
      foreach($arrData['arrData'] as $row) {
        if ($ID_Familia != $row->ID_Familia || $ID_Almacen != $row->ID_Almacen) {
          if ($counter != 0) {
              $objPHPExcel->setActiveSheetIndex($hoja_activa)
              ->setCellValue('G' . $fila, 'Total')
              ->setCellValue('H' . $fila, numberFormat($sum_cantidad, 2, '.', ','))
              ->setCellValue('I' . $fila, numberFormat($sum_total, 2, '.', ','))
              ->setCellValue('J' . $fila, numberFormat($sum_descuento, 2, '.', ','))
              ->setCellValue('K' . $fila, numberFormat($sum_utilidad_neta, 2, '.', ','));
              
              $sum_cantidad = 0.000000;
              $sum_total = 0.00;
              $sum_descuento = 0.00;
              $sum_utilidad_neta = 0.00;
              
              $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
              
              $objPHPExcel->getActiveSheet()
              ->getStyle('A' . $fila . ':' . 'K' . $fila)
              ->applyFromArray(
                  array(
                      'fill' => array(
                          'type' => PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => 'E7E7E7')
                      )
                  )
              );
              $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
          
              $fila++;
          }

          if ($ID_Almacen != $row->ID_Almacen) {
              if ($counter_almacen != 0) {
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('G' . $fila, 'Total Almacén')
                ->setCellValue('H' . $fila, numberFormat($sum_cantidad_almacen, 2, '.', ','))
                ->setCellValue('I' . $fila, numberFormat($sum_total_almacen, 2, '.', ','))
                ->setCellValue('J' . $fila, numberFormat($sum_total_descuento_almacen, 2, '.', ','))
                ->setCellValue('K' . $fila, numberFormat($sum_total_utilidad_neta_almacen, 2, '.', ','));
                
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                            
                $objPHPExcel->getActiveSheet()
                ->getStyle('A' . $fila . ':' . 'K' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'E7E7E7')
                        )
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
            
                $fila++;
                
                $sum_cantidad_almacen = 0.000000;
                $sum_total_almacen = 0.00;
                $sum_total_descuento_almacen = 0.00;
                $sum_total_utilidad_neta_almacen = 0.00;
              }

              $objPHPExcel->setActiveSheetIndex($hoja_activa)
              ->setCellValue('A' . $fila, 'Almacén')
              ->setCellValue('B' . $fila, $row->No_Almacen);

              $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':K'. $fila);
              
              $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
              $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
              
              $objPHPExcel->getActiveSheet()
              ->getStyle('A' . $fila . ':' . 'K' . $fila)
              ->applyFromArray(
                  array(
                      'fill' => array(
                          'type' => PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => 'F2F5F5')
                      )
                  )
              );
              $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
              
              $ID_Almacen = $row->ID_Almacen;
              $fila++;
          }
          
          $objPHPExcel->setActiveSheetIndex($hoja_activa)
          ->setCellValue('A' . $fila, 'Familia')
          ->setCellValue('B' . $fila, $row->No_Familia)
          ;
          
          $ID_Familia = $row->ID_Familia;
          
          $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
          $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
          
          $objPHPExcel->getActiveSheet()
          ->getStyle('A' . $fila . ':' . 'I' . $fila)
          ->applyFromArray(
              array(
                  'fill' => array(
                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array('rgb' => 'F2F5F5')
                  )
              )
          );
          $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'I' . $fila)->getFont()->setBold(true);
          $fila++;
        }
        
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'I' . $fila)->applyFromArray($style_align_right);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->Nu_Codigo_Barra)
        ->setCellValue('B' . $fila, $row->No_Producto)
        ->setCellValue('C' . $fila, $row->No_Signo)
        ->setCellValue('D' . $fila, numberFormat($row->Ss_Costo, 2, '.', ','))
        ->setCellValue('E' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
        ->setCellValue('F' . $fila, numberFormat($row->Ss_Ganancia, 2, '.', ','))
        ->setCellValue('G' . $fila, numberFormat($row->Po_Margen_Ganancia, 2, '.', ','))
        ->setCellValue('H' . $fila, numberFormat($row->Qt_Producto, 2, '.', ','))
        ->setCellValue('I' . $fila, numberFormat($row->Ss_Utilidad, 2, '.', ','))
        ->setCellValue('J' . $fila, numberFormat($row->Ss_Descuento, 2, '.', ','))
        ->setCellValue('K' . $fila, numberFormat($row->Ss_Utilidad_Neta, 2, '.', ','))
        ;
        $fila++;

        $sum_cantidad += $row->Qt_Producto;
        $sum_total += $row->Ss_Utilidad;
        $sum_descuento += $row->Ss_Descuento;
        $sum_utilidad_neta += $row->Ss_Utilidad_Neta;
        
        $sum_cantidad_almacen += $row->Qt_Producto;
        $sum_total_almacen += $row->Ss_Utilidad;
        $sum_total_descuento_almacen += $row->Ss_Descuento;
        $sum_total_utilidad_neta_almacen += $row->Ss_Utilidad_Neta;
        
        $sum_general_cantidad += $row->Qt_Producto;
        $sum_general_total += $row->Ss_Utilidad;
        $sum_general_total_utilidad_neta += $row->Ss_Descuento;
        $sum_general_total_descuento += $row->Ss_Utilidad_Neta;
        
        $counter++;
        $counter_almacen++;
      }
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('G' . $fila, 'Total')
      ->setCellValue('H' . $fila, numberFormat($sum_cantidad, 2, '.', ','))
      ->setCellValue('I' . $fila, numberFormat($sum_total, 2, '.', ','))
      ->setCellValue('J' . $fila, numberFormat($sum_descuento, 2, '.', ','))
      ->setCellValue('K' . $fila, numberFormat($sum_utilidad_neta, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'K' . $fila)
      ->applyFromArray(
          array(
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'E7E7E7')
              )
          )
      );
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
      
      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('G' . $fila, 'Total Almacén')
      ->setCellValue('H' . $fila, numberFormat($sum_cantidad_almacen, 2, '.', ','))
      ->setCellValue('I' . $fila, numberFormat($sum_total_almacen, 2, '.', ','))
      ->setCellValue('J' . $fila, numberFormat($sum_total_descuento_almacen, 2, '.', ','))
      ->setCellValue('K' . $fila, numberFormat($sum_total_utilidad_neta_almacen, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'K' . $fila)
      ->applyFromArray(
          array(
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'E7E7E7')
              )
          )
      );
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);

      $fila++;
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('G' . $fila, 'Total General')
      ->setCellValue('H' . $fila, numberFormat($sum_general_cantidad, 2, '.', ','))
      ->setCellValue('I' . $fila, numberFormat($sum_general_total, 2, '.', ','))
      ->setCellValue('J' . $fila, numberFormat($sum_general_total_utilidad_neta, 2, '.', ','))
      ->setCellValue('K' . $fila, numberFormat($sum_general_total_descuento, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'K' . $fila)
      ->applyFromArray(
          array(
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'E7E7E7')
              )
          )
      );
      $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'K' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':K' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData

		// header('Content-type: application/vnd.ms-excel');
		// header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

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
