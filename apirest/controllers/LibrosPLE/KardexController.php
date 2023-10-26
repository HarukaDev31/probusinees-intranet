<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class KardexController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('LibrosPLE/KardexModel');
		$this->load->model('HelperModel');
	}

	public function reporteKardex(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('LibrosPLE/KardexView');
			$this->load->view('footer', array("js_kardex" => true));
		}
	}
	
	public function getTiposLibroSunat(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->KardexModel->getTiposLibroSunat($this->input->post('ID_Tipo_Asiento')));
	}
	
    private function getDataKardex($arrParams){
        $arrResponseModal = $this->KardexModel->kardex($arrParams);
        $sVarianteMultiple = '';
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();

                $sVarianteMultiple = '';
                if($this->empresa->Nu_Tipo_Rubro_Empresa=='6') {//6=ropa y moda
                    $arrVarianteProducto = $this->HelperModel->obtenerVarianteProductos($row->ID_Producto);
                    if (is_object($arrVarianteProducto)) {
                        $sVarianteMultiple .= (!empty($arrVarianteProducto->No_Variante_1) ? " " . $arrVarianteProducto->No_Variante_1 . ':' . $arrVarianteProducto->No_Valor_Variante_1 : '');
                        $sVarianteMultiple .= (!empty($arrVarianteProducto->No_Variante_2) ? " " . $arrVarianteProducto->No_Variante_2 . ':' . $arrVarianteProducto->No_Valor_Variante_2 : '');
                        $sVarianteMultiple .= (!empty($arrVarianteProducto->No_Variante_3) ? " " . $arrVarianteProducto->No_Variante_3 . ':' . $arrVarianteProducto->No_Valor_Variante_3 : '');
                    }
                }

                $rows['ID_Almacen'] = $row->ID_Almacen;
                $rows['No_Almacen'] = $row->No_Almacen;
                $rows['Txt_Direccion_Almacen'] = $row->Txt_Direccion_Almacen;

                $rows['ID_Producto'] = $row->ID_Producto;
                $rows['Nu_Codigo_Barra'] = $row->Nu_Codigo_Barra;
                $rows['No_Codigo_Interno'] = (!empty($row->No_Codigo_Interno) ? $row->No_Codigo_Interno : '-');
                $rows['No_Producto'] = $row->No_Producto . $sVarianteMultiple;
                $arrParams = array(
                    "ID_Almacen" => $row->ID_Almacen,
                    "dInicio" => $arrParams['dInicio'],
                    "ID_Producto" => $row->ID_Producto,
                );
                $arrDataPrevSaldo = $this->HelperModel->getStockProductoxFechaInicioyFin($arrParams);
                $rows['Qt_Producto_Inicial'] = $arrDataPrevSaldo['Qt_Producto_Prev_Rango_Fecha'];
                $rows['Nu_Tipo_Movimiento'] = $row->Nu_Tipo_Movimiento;
                $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                $rows['Tipo_Documento_Sunat_Codigo'] = $row->Tipo_Documento_Sunat_Codigo;
                $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
                $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                $rows['ID_Numero_Documento'] = autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT);
                $rows['Tipo_Operacion_Sunat_Codigo'] = $row->Tipo_Operacion_Sunat_Codigo;
                $rows['No_Tipo_Movimiento'] = $row->No_Tipo_Movimiento;
                $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                $rows['No_Entidad'] = $row->No_Entidad;
                $rows['Qt_Producto'] = $row->Qt_Producto;
                $rows['TP_Sunat_Codigo'] = $row->TP_Sunat_Codigo;
                $rows['TP_Sunat_Nombre'] = $row->TP_Sunat_Nombre;
                $rows['UM_Sunat_Codigo'] = $row->UM_Sunat_Codigo;
                
                $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];

                $rows['ID_Inventario'] = $row->ID_Inventario;
                $rows['Nu_Codigo_Establecimiento_Sunat'] = $row->Nu_Codigo_Establecimiento_Sunat;
                $data[] = (object)$rows;
            }
            return array(
                'sStatus' => 'success',
                'arrData' => $data,
                'arrDataAlmacenSinMovimiento' => $arrResponseModal['arrDataAlmacenSinMovimiento']
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function kardex(){
        $arrParams = array(
            "ID_Tipo_Asiento" => $this->input->post('ID_Tipo_Asiento'),
            "ID_Tipo_Asiento_Detalle" => $this->input->post('ID_Tipo_Asiento_Detalle'),
            "ID_Almacen" => $this->input->post('ID_Almacen'),
            "dInicio" => $this->input->post('dInicio'),
            "dFin" => $this->input->post('dFin'),
            "ID_Producto" => $this->input->post('ID_Producto'),
            "ID_Tipo_Movimiento" => $this->input->post('ID_Tipo_Movimiento'),
            "iFiltroBusquedaNombre" => $this->input->post('iFiltroBusquedaNombre'),
            "sNombreItem" => $this->input->post('sNombreItem'),
            "ID_Familia" => $this->input->post('ID_Familia'),
            "ID_Sub_Familia" => $this->input->post('ID_Sub_Familia'),
            "ID_Marca" => $this->input->post('ID_Marca'),
            "ID_Variante_Item" => $this->input->post('ID_Variante_Item'),
            "ID_Variante_Item_Detalle_1" => $this->input->post('ID_Variante_Item_Detalle_1'),
            "ID_Variante_Item2" => $this->input->post('ID_Variante_Item2'),
            "ID_Variante_Item_Detalle_2" => $this->input->post('ID_Variante_Item_Detalle_2'),
            "ID_Variante_Item3" => $this->input->post('ID_Variante_Item3'),
            "ID_Variante_Item_Detalle_3" => $this->input->post('ID_Variante_Item_Detalle_3'),
            "iFiltroItemMovimiento" => $this->input->post('iFiltroItemMovimiento')
        );
        echo json_encode($this->getDataKardex($arrParams));
    }
	
  public function ReporteKardexLista(){
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->KardexModel->getReporte_()));
  }

   public function CancelarReporte(){
        echo $this->KardexModel->CancelarReporte($this->input->post("ID_Reporte"));
    }

    public function BajarReporte($ID_Reporte){
        $row = $this->KardexModel->getReporteRow($ID_Reporte);
        if($row){
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
        }

    }

  public function CrearReporte(){
      echo $this->KardexModel->CrearReporte($this->input->post());
  }

  public function ReporteKardexBG(){
    $this->FileReporte = "";
    $this->FileName    = ""; 


    if(!is_cli()) // solo se ejecuta en terminal
        exit();

    // $this->user->ID_Empresa = 272;
    // $fYear = "2022";
    $row = $this->KardexModel->getReporteBG();

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
    $this->empresa = $this->ConfiguracionModel->obtenerEmpresa_();
    $this->KardexModel->UpdateReporteBG($arr,$row->ID_Reporte);
    $this->empresa->ID_Organizacion = $Data["ID_Organizacion"];

    if($row->Nu_Tipo_Formato==1){
            //echo "\n formato 1 Excel\n";
            $result = $this->kardexEXCEL(
                        $Data["ID_Tipo_Asiento"],
                        $Data["ID_Tipo_Asiento_Detalle"],
                        $Data["ID_Almacen"],
                        $Data["dInicio"],
                        $Data["dFin"],
                        $Data["ID_Producto"],
                        $Data["Txt_Direccion_Almacen"],
                        $Data["Nu_Codigo_Libro_Sunat"],
                        $Data["No_Tipo_Asiento_Apertura"],
                        $Data["ID_Tipo_Movimiento"],
                        $Data["iFiltroBusquedaNombre"],
                        $Data["sNombreItem"],
                        $Data["ID_Familia"],
                        $Data["ID_Sub_Familia"],
                        $Data["ID_Marca"],
                        $Data["ID_Variante_Item"],
                        $Data["ID_Variante_Item_Detalle_1"],
                        $Data["ID_Variante_Item2"],
                        $Data["ID_Variante_Item_Detalle_2"],
                        $Data["ID_Variante_Item3"],
                        $Data["ID_Variante_Item_Detalle_3"],
                        $Data["iFiltroItemMovimiento"]
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

        $this->KardexModel->UpdateReporteBG($arr,$row->ID_Reporte);

  }

	public function kardexPDF($ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Almacen, $dInicio, $dFin, $ID_Producto, $Txt_Direccion_Almacen, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $ID_Tipo_Movimiento,
    $iFiltroBusquedaNombre,
    $sNombreItem,
    $ID_Familia,
    $ID_Sub_Familia,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3,
    $iFiltroItemMovimiento
    ){
		$this->load->library('FormatoLibroSunatPDF');
		
        $ID_Tipo_Asiento            = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle    = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
        $ID_Almacen                 = $this->security->xss_clean($ID_Almacen);
        $dInicio                = $this->security->xss_clean($dInicio);
        $dFin                = $this->security->xss_clean($dFin);
        $ID_Producto                = $this->security->xss_clean($ID_Producto);
        $Txt_Direccion_Almacen      = $this->security->xss_clean($Txt_Direccion_Almacen);
        $Nu_Codigo_Libro_Sunat      = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura   = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $ID_Tipo_Movimiento   = $this->security->xss_clean($ID_Tipo_Movimiento);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $sNombreItem   = $this->security->xss_clean($sNombreItem);
        $ID_Familia   = $this->security->xss_clean($ID_Familia);
        $ID_Sub_Familia   = $this->security->xss_clean($ID_Sub_Familia);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $iFiltroItemMovimiento   = $this->security->xss_clean($iFiltroItemMovimiento);
        
		$fileNamePDF = "KardexFisico_" . $dInicio . "_" . $dFin . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $arrCabecera = array (
            "Txt_Direccion_Almacen" => $Txt_Direccion_Almacen,
            "dInicio" => $dInicio,
            "dFin" => $dFin,
        );
        
        $arrParams = array(
            "ID_Tipo_Asiento" => $ID_Tipo_Asiento,
            "ID_Tipo_Asiento_Detalle" => $ID_Tipo_Asiento_Detalle,
            "ID_Almacen" => $ID_Almacen,
            "dInicio" => $dInicio,
            "dFin" => $dFin,
            "ID_Producto" => $ID_Producto,
            "ID_Tipo_Movimiento" => $ID_Tipo_Movimiento,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "sNombreItem" => $sNombreItem,
            "ID_Familia" => $ID_Familia,
            "ID_Sub_Familia" => $ID_Sub_Familia,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            "iFiltroItemMovimiento" => $iFiltroItemMovimiento
        );
		ob_start();
		$file = $this->load->view('LibrosPLE/pdf/KardexPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getDataKardex($arrParams)
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 5);
        
		$pdf->AddPage('P', 'A4');
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->Output($fileNamePDF, 'I');
    }
    
	public function kardexEXCEL($ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Almacen, $dInicio, $dFin, $ID_Producto, $Txt_Direccion_Almacen, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $ID_Tipo_Movimiento,
    $iFiltroBusquedaNombre,
    $sNombreItem,
    $ID_Familia,
    $ID_Sub_Familia,
    $ID_Marca,
    $ID_Variante_Item,
    $ID_Variante_Item_Detalle_1,
    $ID_Variante_Item2,
    $ID_Variante_Item_Detalle_2,
    $ID_Variante_Item3,
    $ID_Variante_Item_Detalle_3,
    $iFiltroItemMovimiento
    ){
		$this->load->library('Excel');
	    
        $ID_Tipo_Asiento            = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle    = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
        $ID_Almacen                 = $this->security->xss_clean($ID_Almacen);
        $dInicio                = $this->security->xss_clean($dInicio);
        $dFin                = $this->security->xss_clean($dFin);
        $ID_Producto                = $this->security->xss_clean($ID_Producto);
        $Txt_Direccion_Almacen      = $this->security->xss_clean($Txt_Direccion_Almacen);
        $Nu_Codigo_Libro_Sunat      = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura   = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $ID_Tipo_Movimiento   = $this->security->xss_clean($ID_Tipo_Movimiento);
        $iFiltroBusquedaNombre   = $this->security->xss_clean($iFiltroBusquedaNombre);
        $sNombreItem   = $this->security->xss_clean($sNombreItem);
        $ID_Familia   = $this->security->xss_clean($ID_Familia);
        $ID_Sub_Familia   = $this->security->xss_clean($ID_Sub_Familia);
        $ID_Marca   = $this->security->xss_clean($ID_Marca);
        $ID_Variante_Item   = $this->security->xss_clean($ID_Variante_Item);
        $ID_Variante_Item_Detalle_1   = $this->security->xss_clean($ID_Variante_Item_Detalle_1);
        $ID_Variante_Item2   = $this->security->xss_clean($ID_Variante_Item2);
        $ID_Variante_Item_Detalle_2   = $this->security->xss_clean($ID_Variante_Item_Detalle_2);
        $ID_Variante_Item3   = $this->security->xss_clean($ID_Variante_Item3);
        $ID_Variante_Item_Detalle_3   = $this->security->xss_clean($ID_Variante_Item_Detalle_3);
        $iFiltroItemMovimiento   = $this->security->xss_clean($iFiltroItemMovimiento);
        
		$this->FileName = $fileNameExcel = "KardexFisico_" . $dInicio . "_" . $dFin . ".xls";
        $arrParams = array(
            "ID_Tipo_Asiento" => $ID_Tipo_Asiento,
            "ID_Tipo_Asiento_Detalle" => $ID_Tipo_Asiento_Detalle,
            "ID_Almacen" => $ID_Almacen,
            "dInicio" => $dInicio,
            "dFin" => $dFin,
            "ID_Producto" => $ID_Producto,
            "ID_Tipo_Movimiento" => $ID_Tipo_Movimiento,
            "iFiltroBusquedaNombre" => $iFiltroBusquedaNombre,
            "sNombreItem" => $sNombreItem,
            "ID_Familia" => $ID_Familia,
            "ID_Sub_Familia" => $ID_Sub_Familia,
            "ID_Marca" => $ID_Marca,
            "ID_Variante_Item" => $ID_Variante_Item,
            "ID_Variante_Item_Detalle_1" => $ID_Variante_Item_Detalle_1,
            "ID_Variante_Item2" => $ID_Variante_Item2,
            "ID_Variante_Item_Detalle_2" => $ID_Variante_Item_Detalle_2,
            "ID_Variante_Item3" => $ID_Variante_Item3,
            "ID_Variante_Item_Detalle_3" => $ID_Variante_Item_Detalle_3,
            "iFiltroItemMovimiento" => $iFiltroItemMovimiento
        );
        $arrData = $this->getDataKardex($arrParams);

	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('12 Reg. Inv. Perm. Und. Fisicas');
        
	    $hoja_activa = 0;
	    
	    $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A1', 'FORMATO 12.1: REGISTRO DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS- DETALLE DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS');
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
        
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
        
        $style_all_border_center = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        
        $style_all_border_left = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );
        
        $style_all_border_right = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        
        $fila = 2;
        
        $ID_Almacen = 0; $counter_almacen = 0; $sum_Almacen_Producto_Qt_Entrada = 0.00; $sum_Almacen_Producto_Qt_Salida = 0.00;
        $ID_Producto = 0;
        $counter = 0;
        $Qt_Producto_Saldo_Movimiento = 0.00;
        $sum_Producto_Qt_Entrada = 0.00;
        $sum_Producto_Qt_Salida = 0.00;
        $sum_General_Qt_Entrada = 0.00;
        $sum_General_Qt_Salida = 0.00;        
        if ( $arrData['sStatus'] == 'success' ) {
            $arrFechaInicio = explode('-', $dInicio);
            $fYear = $arrFechaInicio[0];
            $fMonth = $arrFechaInicio[1];
            //foreach ($arrData['arrData'] as $row) {
            for ($i=0; $i < count($arrData['arrData']); $i++) {
                $row = $arrData['arrData'][$i];
                if ($ID_Producto != $row->ID_Producto || $ID_Almacen != $row->ID_Almacen) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('H' . $fila, 'TOTAL PRODUCTO')
                        ->setCellValue('I' . $fila, $sum_Producto_Qt_Entrada)
                        ->setCellValue('J' . $fila, $sum_Producto_Qt_Salida);
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);
                        $fila++;
                    }
                    
                    if ($ID_Almacen != $row->ID_Almacen) {
                        if ($counter_almacen != 0) {
                            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                            ->setCellValue('H' . $fila, 'TOTAL ALMACÉN: ' . $arrData['arrData'][$i-1]->No_Almacen)
                            ->setCellValue('I' . $fila, $sum_Almacen_Producto_Qt_Entrada)
                            ->setCellValue('J' . $fila, $sum_Almacen_Producto_Qt_Salida);
                            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);
                            $fila++;
                        }
                        $sum_Almacen_Producto_Qt_Entrada = 0.00;
                        $sum_Almacen_Producto_Qt_Salida = 0.00;

                        $ID_Almacen = $row->ID_Almacen;
                    }

                    $ID_Producto = $row->ID_Producto;
                    $sum_Producto_Qt_Entrada = 0.00;
                    $sum_Producto_Qt_Salida = 0.00;
                    $Qt_Producto_Saldo_Movimiento = $row->Qt_Producto_Inicial;
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'PERÍODO:')
                    ->setCellValue('E' . $fila, $fMonth . ' ' . $fYear);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'RUC:')
                    ->setCellValue('E' . $fila, $this->empresa->Nu_Documento_Identidad);
                    
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL:')
                    ->setCellValue('E' . $fila, $this->empresa->No_Empresa);
                   
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'ALMACÉN:')
                    ->setCellValue('E' . $fila, $row->No_Almacen);

                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'ESTABLECIMIENTO (1):')
                    ->setCellValue('E' . $fila, $row->Txt_Direccion_Almacen);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'CÓDIGO DE LA EXISTENCIA:')
                    ->setCellValue('E' . $fila, $row->TP_Sunat_Codigo);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'TIPO (TABLA 5):')
                    ->setCellValue('E' . $fila, $row->TP_Sunat_Nombre);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'UPC:')
                    ->setCellValue('E' . $fila, $row->Nu_Codigo_Barra);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'SKU:')
                    ->setCellValue('E' . $fila, $row->No_Codigo_Interno);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'DESCRIPCIÓN:')
                    ->setCellValue('E' . $fila, $row->No_Producto);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'CÓDIGO DE LA UNIDAD DE MEDIDA (TABLA 6):')
                    ->setCellValue('E' . $fila, $row->UM_Sunat_Codigo);
                    
                    $fila++;
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR');
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':E' . $fila);
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('F' . $fila, 'TIPO DE OPERACIÓN')
                    ->setCellValue('G' . $fila, 'MOVIMIENTO')
                    ->setCellValue('H' . $fila, 'ENTIDAD')
                    ->setCellValue('I' . $fila, 'ENTRADAS')
                    ->setCellValue('J' . $fila, 'SALIDAS')
                    ->setCellValue('K' . $fila, 'SALDO FINAL');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':K' . $fila)->applyFromArray($BStyle_top);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($BStyle_bottom);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':K' . $fila)->getFont()->setBold(true);
                    
                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('A' . $fila, 'FECHA')
                    ->setCellValue('B' . $fila, 'TIPO (TABLA 10)')
                    ->setCellValue('C' . $fila, 'TIPO')
                    ->setCellValue('D' . $fila, 'SERIE')
                    ->setCellValue('E' . $fila, 'NÚMERO')
                    ->setCellValue('F' . $fila, '(TABLA 12)');
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':K' . $fila)->applyFromArray($BStyle_bottom);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila)->getFont()->setBold(true);

                    $fila++;
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('J' . $fila, 'SALDO INICIAL')
                    ->setCellValue('K' . $fila, numberFormat($Qt_Producto_Saldo_Movimiento, 2, '.', ''));
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':K' . $fila)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_left);
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':K' . $fila)->applyFromArray($BStyle_bottom);
                    
                    $fila++;
                }
                
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->Fe_Emision)
                ->setCellValue('B' . $fila, $row->Tipo_Documento_Sunat_Codigo)
                ->setCellValue('C' . $fila, $row->No_Tipo_Documento_Breve);
                
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $fila, $row->ID_Serie_Documento, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $fila, $row->ID_Numero_Documento, PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $row->Tipo_Operacion_Sunat_Codigo)
                ->setCellValue('G' . $fila, $row->No_Tipo_Movimiento)
                ->setCellValue('h' . $fila, $row->No_Entidad);
                
                if ($row->Nu_Tipo_Movimiento == 0){
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('I' . $fila, numberFormat($row->Qt_Producto, 2, '.', ''))
                    ->setCellValue('J' . $fila, 0);
                    
                    $Qt_Producto_Saldo_Movimiento += $row->Qt_Producto;
                    $sum_Producto_Qt_Entrada += $row->Qt_Producto;
                    $sum_Almacen_Producto_Qt_Entrada += $row->Qt_Producto;
                    $sum_General_Qt_Entrada += $row->Qt_Producto;
                } else {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('I' . $fila, 0)
                    ->setCellValue('J' . $fila, numberFormat($row->Qt_Producto, 2, '.', ''));
                    
                    $Qt_Producto_Saldo_Movimiento -= $row->Qt_Producto;
                    $sum_Producto_Qt_Salida += $row->Qt_Producto;
                    $sum_Almacen_Producto_Qt_Salida += $row->Qt_Producto;
                    $sum_General_Qt_Salida += $row->Qt_Producto;
                }
                
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('K' . $fila, numberFormat($Qt_Producto_Saldo_Movimiento, 2, '.', ''));
                
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_all_border_left);                
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':K' . $fila)->applyFromArray($BStyle_bottom);
                
                $counter++;
                $counter_almacen++;
                $fila++;
            } // /. foreach arrData
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);
            
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL PRODUCTO')
            ->setCellValue('I' . $fila, $sum_Producto_Qt_Entrada)
            ->setCellValue('J' . $fila, $sum_Producto_Qt_Salida);
            
            ++$fila;
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL ALMACEN: ' . $row->No_Almacen)
            ->setCellValue('I' . $fila, $sum_Almacen_Producto_Qt_Entrada)
            ->setCellValue('J' . $fila, $sum_Almacen_Producto_Qt_Salida);

            ++$fila;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL GENERAL')
            ->setCellValue('I' . $fila, $sum_General_Qt_Entrada)
            ->setCellValue('J' . $fila, $sum_General_Qt_Salida);
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);

            ++$fila;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'TOTAL CANTIDAD (ENTRADA - SALIDA)')
            ->setCellValue('I' . $fila, ($sum_General_Qt_Entrada - $sum_General_Qt_Salida));
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('I'. $fila . ':J'. $fila);
            
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($style_all_border_right);

            //PRODUCTOS SIN MOVIMIENTO
            $ID_Almacen = 0;
            for ($i = 0; $i < count($arrData['arrDataAlmacenSinMovimiento']); $i++) {
                $row_almacen = $arrData['arrDataAlmacenSinMovimiento'];
                if ($ID_Almacen != $row_almacen[$i][0]->ID_Almacen) {
                    for ($p = 0; $p < count($arrData['arrDataAlmacenSinMovimiento'][$i]); $p++) {
                      $row_producto = $row_almacen[$i];
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'PERÍODO:')
                        ->setCellValue('E' . $fila, $fMonth . ' ' . $fYear);
                        
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'RUC:')
                        ->setCellValue('E' . $fila, $this->empresa->Nu_Documento_Identidad);
                        
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL:')
                        ->setCellValue('E' . $fila, $this->empresa->No_Empresa);
                    
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'ALMACÉN:')
                        ->setCellValue('E' . $fila, $row_almacen[$i][0]->No_Almacen);

                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'ESTABLECIMIENTO (1):')
                        ->setCellValue('E' . $fila, $Txt_Direccion_Almacen);
                        
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'UPC:')
                        ->setCellValue('E' . $fila, $row_producto[$p]->Nu_Codigo_Barra);
                        
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'SKU:')
                        ->setCellValue('E' . $fila, $row_producto[$p]->No_Codigo_Interno);
                        
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'DESCRIPCIÓN:')
                        ->setCellValue('E' . $fila, $row_producto[$p]->No_Producto);
                        
                        $fila++;
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('A' . $fila, 'SALDO:')
                        ->setCellValue('E' . $fila, $row_producto[$p]->Qt_Producto);
                        
                        $fila++;
                    }
                    $ID_Almacen = $row_almacen[$i][0]->ID_Almacen;
                }
            } // /. foreach arrData
        } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('E' . $fila, $arrData['sMessage']);
        }
	    
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    try {
        $this->FileReporte = md5(time().mt_rand(1,1000000));
        $objWriter->save(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte);
         return true;
        } catch (Exception $e) {
            return false;
        }

	}
	
	public function kardexTXT($ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Almacen, $dInicio, $dFin, $ID_Producto, $Txt_Direccion_Almacen, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $ID_Tipo_Movimiento){
        $ID_Tipo_Asiento            = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle    = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
        $Nu_Codigo_Libro_Sunat      = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura   = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $dInicio                = $this->security->xss_clean($dInicio);
        $dFin                = $this->security->xss_clean($dFin);
        $ID_Tipo_Movimiento = $this->security->xss_clean($ID_Tipo_Movimiento);
        
        //Indicador de operaciones
        $O = 1;//Empresa o entidad operativa
        
        //Indicador del contenido del libro o registro
        $I = 0;//Sin información
        
        //Indicador de la moneda utilizada
        $M = 1;//Soles
        
        //Indicador de libro electrónico generado por el PLE
        $G = 1;//Generado por PLE (Fijo)
        
        ob_clean();

        $arrParams = array(
            "ID_Tipo_Asiento" => $ID_Tipo_Asiento,
            "ID_Tipo_Asiento_Detalle" => $ID_Tipo_Asiento_Detalle,
            "ID_Almacen" => $ID_Almacen,
            "dInicio" => $dInicio,
            "dFin" => $dFin,
            "ID_Producto" => $ID_Producto,
            "ID_Tipo_Movimiento" => $ID_Tipo_Movimiento,
        );
        $data = $this->getDataKardex($arrParams);
        if ( $data['sStatus'] == 'success' ) {
            $arrData = '';
            $arrFechaInicio = explode('-', $dInicio);
            $fYear = $arrFechaInicio[0];
            $fMonth = $arrFechaInicio[1];
            foreach ($data['arrData'] as $row) {
                $arrData .= '1|';//Periodo informa 1 y si no es diferente de 1
                $arrData .= $row->ID_Inventario . '|';//CUO
                $arrData .= 'M|';//A / M / C
                $arrData .= $row->Nu_Codigo_Establecimiento_Sunat . '|';//Codigo de establecimiento y si esta en un tercero 9999
                $arrData .= '9|';//tabla 13 9 = OTROS
                $arrData .= $row->TP_Sunat_Codigo . '|';//Tabla 05 - Tipo de existencia producto
                $arrData .= $row->Nu_Codigo_Establecimiento_Sunat . '|';//Codigo propio de existencia señalado en el campo 5
                $arrData .= '|';//No es obligatorio
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= $row->Tipo_Documento_Sunat_Codigo . '|';
                $arrData .= $row->ID_Serie_Documento . '|';
                $arrData .= $row->ID_Numero_Documento . '|';
                $arrData .= $row->Tipo_Operacion_Sunat_Codigo . '|';
                $arrData .= $row->No_Producto . '|';
                $arrData .= $row->UM_Sunat_Codigo . '|';
                if ($row->Nu_Tipo_Movimiento == 0){
                    $arrData .= numberFormat($row->Qt_Producto, 2, '.', '') . '|';//Entrada
                    $arrData .= '0.00|';
                } else {
                    $arrData .= '0.00|';
                    $arrData .= numberFormat($row->Qt_Producto, 2, '.', '') . '|';//Salida
                }
                $arrData .= '1|';//Estado de la operación 1 dentro del periodo
                $arrData .= "\n";
            }
            
            $I = 1;//Con información
            
            $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00080" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";
            
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="' . $fileNameTXT . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            $arrData = trim($arrData);
            die($arrData);
        } else {
            $arrData = '';
            $I = 0;//Sin información
            
            $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00080" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";
            
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="' . $fileNameTXT . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            $arrData = trim($arrData);
            die($arrData);
        }
	}	
}
