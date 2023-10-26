<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class RegistroVentaIngresoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('LibrosPLE/RegistroVentaIngresoModel');
		$this->load->model('HelperModel');
	}

	public function reporteRVI(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('LibrosPLE/RegistroVentaIngresoView');
			$this->load->view('footer', array("js_registro_venta_ingreso" => true));
		}
	}
	
	public function getTiposLibroSunat(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->RegistroVentaIngresoModel->getTiposLibroSunat($this->input->post('ID_Tipo_Asiento')));
    }
    
    public function modificarCorrelativo(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->RegistroVentaIngresoModel->modificarCorrelativo($this->input->post()));
    }

    private function getDataRegistroVentasIngresos($arrParams){
        $arrResponseModal = $this->RegistroVentaIngresoModel->registroVentasIngresos($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $ID_Tipo_Documento = '';
            $ID_Serie_Documento = '';
            $sTipoDocumentoCodigoSunatBoleta = '';
            $sSerieDocumentoBoleta = '';
            $iNumeroDocumentoInicialBoleta = '';
            $iDetener = 0;
            $Ss_SubTotal_Gravadas = 0.00;
            $Ss_Descuento = 0.00;
            $Ss_IGV = 0.00;
            $Ss_Descuento_IGV = 0.00;
            $Ss_SubTotal_Inafecta = 0.00;
            $Ss_SubTotal_Exonerada = 0.00;
            $Ss_SubTotal_Gratuita = 0.00;
            $Ss_Exportacion = 0.00;
            $Ss_Icbper = 0.00;
            $Ss_Total = 0.00;
            $ID_Tipo_Vista = $arrParams['ID_Tipo_Vista'];
            $Nu_Codigo_Libro_Sunat = $arrParams['Nu_Codigo_Libro_Sunat'];
            $No_Tipo_Asiento_Apertura = $arrParams['No_Tipo_Asiento_Apertura'];
            $fYear = $arrParams['fYear'];
            $fMonth = $arrParams['fMonth'];
            $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
            $data = array();
            $rows = array();
            foreach ($arrResponseModal['arrData'] as $row) {
                settype($row->CUO, "int");
                settype($row->Ss_SubTotal_Gravadas, "double");
                settype($row->Ss_Inafecta, "double");
                settype($row->Ss_Exonerada, "double");
                settype($row->Ss_Gratuita, "double");
                settype($row->Ss_Exportacion, "double");
                
                $iNumImpuestoDescuento = 0;
                $iNumImpuestoDescuentoIGV = 0;
                $iNumImpuestoDescuentoEXO = 0;
                $fImpuestoConfiguracionIGV = $row->Ss_Impuesto;

                $fGravada = $row->Ss_SubTotal_Gravadas;
                $fIGV = $row->Ss_IGV_Gravadas;
                $fInafecta = $row->Ss_Inafecta;
                $fExonerada = $row->Ss_Exonerada;
                $fGratuita = $row->Ss_Gratuita;
                $fExportacion = $row->Ss_Exportacion;

                if($row->Ss_Descuento>0.00 && $row->Po_Descuento==0) {
                    //$fGravada = ($fGravada - $row->Ss_Descuento);
                    //$fIGV = ($fGravada * $fImpuestoConfiguracionIGV) - $fGravada;
                    $fGravada = ($fGravada - $row->Ss_Descuento);
                    $fIGV = ($fIGV - $row->Ss_Descuento_Impuesto);
                }

                if ($iDetener == 0 && (
                    $row->ID_Tipo_Documento != 4 ||
                    ($row->ID_Tipo_Documento == 4 && ($row->Ss_SubTotal_Gravadas >= 700.00 || $row->Ss_Inafecta >= 350.00 || $row->Ss_Exonerada >= 350.00 || $row->Ss_Gratuita >= 350.00)) ||
                    $ID_Tipo_Vista == 1)
                ){
                    $rows_ = array();
                    $rows_['Correlativo'] = $row->CUO;
                    $rows_['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                    $rows_['Fe_Periodo'] = $fYear . $fMonth. '00';
                    $rows_['CUO'] = $Nu_Codigo_Libro_Sunat . $row->CUO;
                    $rows_['No_Tipo_Asiento_Apertura'] = $No_Tipo_Asiento_Apertura;
                    $rows_['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                    $rows_['DOCU_Nu_Sunat_Codigo'] = $row->DOCU_Nu_Sunat_Codigo;
                    $rows_['ID_Serie_Documento'] = $row->ID_Serie_Documento;
                    $rows_['ID_Numero_Documento_Inicial'] = autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT);
                    $rows_['ID_Numero_Documento_Final'] = '';
                    $rows_['IDE_Nu_Sunat_Codigo'] = $row->IDE_Nu_Sunat_Codigo;
                    $rows_['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                    $rows_['No_Entidad'] = $row->No_Entidad;
                    $rows_['Ss_SubTotal_Gravadas'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fGravada, 2, '.', '');
                    $rows_['Ss_Descuento'] = 0.00;//aqui va el descuento que aplica la NC a un documento de periodo cerrado, es decir del mes anterior o más
                    $rows_['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fIGV, 2, '.', '');
                    $rows_['Ss_Descuento_IGV'] = 0.00;
                    $rows_['Ss_SubTotal_Inafecta'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fInafecta, 2, '.', '');
                    $rows_['Ss_SubTotal_Exonerada'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fExonerada, 2, '.', '');
                    $rows_['Ss_SubTotal_Gratuita'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fGratuita, 2, '.', '');
                    $rows_['Ss_Exportacion'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fExportacion, 2, '.', '');
                    $rows_['Ss_Icbper'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($row->Ss_Icbper, 2, '.', '');
                    $rows_['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($row->Ss_Total, 2, '.', '');
                    $rows_['MONE_Nu_Sunat_Codigo'] = $row->MONE_Nu_Sunat_Codigo;
                    $rows_['Ss_Tipo_Cambio'] = numberFormat( (($row->ID_Tipo_Documento != 5 && $row->ID_Tipo_Documento != 6) ? $row->Ss_Tipo_Cambio : ($row->Ss_Tipo_Cambio_Modificar>0.000 ? $row->Ss_Tipo_Cambio_Modificar : $row->Ss_Tipo_Cambio)), 3, '.', '');
                    $rows_['Fe_Emision_Modificar'] = $row->Fe_Emision_Modificar == '' ? '01/01/0001' : ToDateBD($row->Fe_Emision_Modificar);
                    $rows_['ID_Tipo_Documento_Modificar'] = $row->ID_Tipo_Documento_Modificar;
                    $rows_['ID_Serie_Documento_Modificar'] = $row->ID_Serie_Documento_Modificar;
                    $rows_['ID_Numero_Documento_Modificar'] = $row->ID_Numero_Documento_Modificar;
                    $rows_['No_Codigo_Sunat_PLE'] = 1;//Medio de pago
                    $rows_['Nu_Cantidad_Caracteres'] = $row->Nu_Cantidad_Caracteres;
                    $rows_['No_Tipo_Documento'] = $row->No_Tipo_Documento;
                    $rows_['Nu_Estado'] = $row->Nu_Estado;
                    $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                    $rows_['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                    $rows_['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];

                    if ( count($rows_) > 0 )
                        $data[] = (object)$rows_;
                    $ID_Tipo_Documento = '';
                    $ID_Serie_Documento = '';
                }

                if ( $row->ID_Tipo_Documento == 4 && $ID_Tipo_Vista == 0 && $ID_Serie_Documento != $row->ID_Serie_Documento && ($row->Ss_SubTotal_Gravadas < 700.00 && $row->Ss_Inafecta < 350.00 && $row->Ss_Exonerada < 350.00 && $row->Ss_Gratuita < 350.00) ) {//inicial
                    $rows['Correlativo'] = $row->CUO;
                    $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                    $rows['Fe_Periodo'] = $fYear . $fMonth . '00';
                    $rows['CUO'] = $Nu_Codigo_Libro_Sunat . $row->CUO;
                    $rows['No_Tipo_Asiento_Apertura'] = $No_Tipo_Asiento_Apertura;
                    $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                    $sTipoDocumentoCodigoSunatBoleta = $row->DOCU_Nu_Sunat_Codigo;
                    $sSerieDocumentoBoleta = $row->ID_Serie_Documento;
                    $iNumeroDocumentoInicialBoleta = autocompletarConCeros('', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT);
                    $rows['IDE_Nu_Sunat_Codigo'] = $row->IDE_Nu_Sunat_Codigo;
                    $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                    $rows['No_Entidad'] = $row->No_Entidad;
                    $rows['MONE_Nu_Sunat_Codigo'] = $row->MONE_Nu_Sunat_Codigo;
                    $rows['Ss_Tipo_Cambio'] = numberFormat( (($row->ID_Tipo_Documento != 5 && $row->ID_Tipo_Documento != 6) ? $row->Ss_Tipo_Cambio : ($row->Ss_Tipo_Cambio_Modificar>0.000 ? $row->Ss_Tipo_Cambio_Modificar : $row->Ss_Tipo_Cambio)), 3, '.', '');
                    $rows['Fe_Emision_Modificar'] = $row->Fe_Emision_Modificar == '' ? '01/01/0001' : ToDateBD($row->Fe_Emision_Modificar);
                    $rows['ID_Tipo_Documento_Modificar'] = $row->ID_Tipo_Documento_Modificar;
                    $rows['ID_Serie_Documento_Modificar'] = $row->ID_Serie_Documento_Modificar;
                    $rows['ID_Numero_Documento_Modificar'] = $row->ID_Numero_Documento_Modificar;
                    $rows['No_Codigo_Sunat_PLE'] = 1;//Medio de pago
                    $rows['Nu_Cantidad_Caracteres'] = $row->Nu_Cantidad_Caracteres;
                    $rows['No_Tipo_Documento'] = $row->No_Tipo_Documento;
                    $rows['Nu_Estado'] = $row->Nu_Estado;
                    $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                    $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                    $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
                    if ( isset($rows['ID_Numero_Documento_Final']) ) {//Para no tomar el primer arreglo, ver otra forma
                        $data[] = (object)$rows;
                        $Ss_SubTotal_Gravadas = 0.00;
                        $Ss_Descuento = 0.00;
                        $Ss_IGV = 0.00;
                        $Ss_Descuento_IGV = 0.00;
                        $Ss_SubTotal_Inafecta = 0.00;
                        $Ss_SubTotal_Exonerada = 0.00;
                        $Ss_SubTotal_Gratuita = 0.00;
                        $Ss_Exportacion = 0.00;
                        $Ss_Total = 0.00;
                    }
                    $ID_Tipo_Documento = $row->ID_Tipo_Documento;
                    $ID_Serie_Documento = $row->ID_Serie_Documento;
                }
                
                if ( $ID_Tipo_Vista == 0 && $row->ID_Tipo_Documento == 4 && $ID_Serie_Documento == $row->ID_Serie_Documento && ($row->Ss_SubTotal_Gravadas < 700.00 && $row->Ss_Inafecta < 350.00 && $row->Ss_Exonerada < 350.00 && $row->Ss_Gratuita < 350.00) ) {//final
                    $Ss_SubTotal_Gravadas += $fGravada;
                    $Ss_IGV += $fIGV;
                    $Ss_SubTotal_Inafecta += $fInafecta;
                    $Ss_SubTotal_Exonerada += $fExonerada;
                    $Ss_SubTotal_Gratuita += $fGratuita;
                    $Ss_Exportacion += $fExportacion;
                    $Ss_Icbper += $row->Ss_Icbper;
                    $Ss_Total += $row->Ss_Total;

                    $rows['DOCU_Nu_Sunat_Codigo'] = $sTipoDocumentoCodigoSunatBoleta;
                    $rows['ID_Numero_Documento_Inicial'] = $iNumeroDocumentoInicialBoleta;
                    $rows['ID_Serie_Documento'] = $sSerieDocumentoBoleta;
                    $rows['ID_Numero_Documento_Final'] = autocompletarConCeros('-', $row->ID_Numero_Documento, $row->Nu_Cantidad_Caracteres, '0', STR_PAD_LEFT);
                    $rows['Ss_SubTotal_Gravadas'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_SubTotal_Gravadas, 2, '.', '');
                    $rows['Ss_Descuento'] = 0.00;
                    $rows['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_IGV, 2, '.', '');
                    $rows['Ss_Descuento_IGV'] = 0.00;
                    $rows['Ss_SubTotal_Inafecta'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_SubTotal_Inafecta, 2, '.', '');
                    $rows['Ss_SubTotal_Exonerada'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_SubTotal_Exonerada, 2, '.', '');
                    $rows['Ss_SubTotal_Gratuita'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_SubTotal_Gratuita, 2, '.', '');
                    $rows['Ss_Exportacion'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Exportacion, 2, '.', '');
                    $rows['Ss_Icbper'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Icbper, 2, '.', '');
                    $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Total, 2, '.', '');
                } else {
                    $Ss_SubTotal_Gravadas = 0.00;
                    $Ss_Descuento = 0.00;
                    $Ss_IGV = 0.00;
                    $Ss_Descuento_IGV = 0.00;
                    $Ss_SubTotal_Inafecta = 0.00;
                    $Ss_SubTotal_Exonerada = 0.00;
                    $Ss_SubTotal_Gratuita = 0.00;
                    $Ss_Exportacion = 0.00;
                    $Ss_Icbper = 0.00;
                    $Ss_Total = 0.00;
                    
                    $iDetener = 1;
                }
                
                if ( $ID_Tipo_Vista == 0 && $iDetener == 1 ){
                    if ( count($rows) > 0 )
                        $data[] = (object)$rows;
                    $rows = array();
                }
                
                $iDetener = 0;
            }// /. for each
            if ( count($rows) > 0 )
                $data[] = (object)$rows;
                $orderNo_Tipo_Docuento = array();

            $orderNo_Tipo_Docuento = array();
            $orderCorrelativo = array();            
            foreach ($data as $key => $row) {
                $orderNo_Tipo_Docuento[$key] = $row->ID_Tipo_Documento;
                $orderCorrelativo[$key] = $row->Correlativo;
            }
            array_multisort($orderNo_Tipo_Docuento, SORT_ASC, $orderCorrelativo, SORT_ASC, $data);
            
            return array(
                'sStatus' => 'success',
                'arrData' => $data,
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function registroVentasIngresos(){
        $arrParams = array(
            'ID_Organizacion' => $this->input->post('ID_Organizacion'),
            'ID_Tipo_Asiento' => $this->input->post('ID_Tipo_Asiento'),
            'ID_Tipo_Vista' => $this->input->post('ID_Tipo_Vista'),
            'Nu_Codigo_Libro_Sunat' => $this->input->post('Nu_Codigo_Libro_Sunat'),
            'No_Tipo_Asiento_Apertura' => $this->input->post('No_Tipo_Asiento_Apertura'),
            'fYear' => $this->input->post('fYear'),
            'fMonth' => $this->input->post('fMonth'),
            'fMonthText' => '',
            'sNombreLibroSunat' => ''
        );
        echo json_encode($this->getDataRegistroVentasIngresos($arrParams));
    }
	
	public function registroVentasIngresosPDF($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText, $sNombreLibroSunat,$Background=0){
        $this->load->library('FormatoLibroSunatPDF');
		
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = $this->security->xss_clean($sNombreLibroSunat);
        
		$this->FileName = $fileNamePDF = "RegistroVentasIngresos_" . $fMonthText . "_" . $fYear . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $arrCabecera = array (
            "sNombreLibroSunat" => $sNombreLibroSunat,
            "fYear" => $fYear,
            "fMonthText" => $fMonthText,
        );

        $arrParams = array(
            'ID_Organizacion' => $ID_Organizacion,
            'ID_Tipo_Asiento' => $ID_Tipo_Asiento,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        
		ob_start();
		$file = $this->load->view('LibrosPLE/pdf/RegistroVentasIngresoPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getDataRegistroVentasIngresos($arrParams),
		));
		$html = ob_get_contents();
		ob_end_clean();
        		
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $pdf->setFont('helvetica', '', 4);
        
		$pdf->AddPage('L', ['format' => 'A4', 'Rotate' => 90]);
		$pdf->writeHTML($html, true, false, true, false, '');
		if($Background){
            $this->FileReporte = md5(time().mt_rand(1,1000000));
            $pdf->Output(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte, 'F');
            return true;
        }
        else
           $pdf->Output($fileNamePDF, 'I');

    }

    public function ReporteVentasLista(){
        
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->RegistroVentaIngresoModel->getReporte()));
    }

    public function CancelarReporte(){
        echo $this->RegistroVentaIngresoModel->CancelarReporte($this->input->post("ID_Reporte"));
    }

    public function BajarReporte($ID_Reporte){
        $row = $this->RegistroVentaIngresoModel->getReporteRow($ID_Reporte);
        if($row){
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
        }

    }

    public function CrearReporteVentas(){
        echo $this->RegistroVentaIngresoModel->CrearReporteVentas($this->input->post());
    }

    public function registroVentasIngresosBG(){
        $this->FileReporte = "";
        $this->FileName    = ""; 


        if(!is_cli()) // solo se ejecuta en terminal
            exit();

        // $this->user->ID_Empresa = 272;
        // $fYear = "2022";
        $row = $this->RegistroVentaIngresoModel->getReporteBG();
        
        if(!$row)
            exit();

        $this->user->ID_Empresa = $row->ID_Empresa;
        
        $Data = unserialize($row->Txt_Parametro);
        $this->user->ID_Organizacion = $Data["ID_Organizacion"];
        //$Data["fYear"]="2022";
        $arr = array(
            'Fe_Inicio' => date("Y-m-d G:i:s"),
            'ID_Estatus' => 1
         );
        $this->empresa = $this->ConfiguracionModel->obtenerEmpresa_();
        $this->RegistroVentaIngresoModel->UpdateReporteBG($arr,$row->ID_Reporte);
        //1=excel,2=pdf,3=txt
        if($row->Nu_Tipo_Formato==1){
            echo "\n formato 1\n";
            $result = $this->registroVentasIngresosEXCEL(
                        $Data["ID_Organizacion"],
                        $Data["ID_Tipo_Asiento"],
                        $Data["ID_Tipo_Vista"],
                        $Data["Nu_Codigo_Libro_Sunat"],
                        $Data["No_Tipo_Asiento_Apertura"],
                        $Data["fYear"],
                        $Data["fMonth"],
                        $Data["fMonthText"],
                        $Data["sNombreLibroSunat"],
                        1
                    );
        }else if($row->Nu_Tipo_Formato==2){
            echo "\n formato 2\n";
            $result = $this->registroVentasIngresosPDF(
                        $Data["ID_Organizacion"],
                        $Data["ID_Tipo_Asiento"],
                        $Data["ID_Tipo_Vista"],
                        $Data["Nu_Codigo_Libro_Sunat"],
                        $Data["No_Tipo_Asiento_Apertura"],
                        $Data["fYear"],
                        $Data["fMonth"],
                        $Data["fMonthText"],
                        $Data["sNombreLibroSunat"],
                        1
                    );
        }

        else if($row->Nu_Tipo_Formato==3){
            echo "\n formato 3\n";

            if($Data["ID_Tipo_Asiento_Detalle"]==1){
                echo "\nregistroVentasIngresosTXT\n";
                $result = $this->registroVentasIngresosTXT(
                            $Data["ID_Organizacion"],
                            $Data["ID_Tipo_Asiento"],
                            $Data["ID_Tipo_Vista"],
                            $Data["ID_Tipo_Asiento_Detalle"],
                            $Data["Nu_Codigo_Libro_Sunat"],
                            $Data["No_Tipo_Asiento_Apertura"],
                            $Data["fYear"],
                            $Data["fMonth"],
                            $Data["fMonthText"],
                            1
                        );
            }else if($Data["ID_Tipo_Asiento_Detalle"]==2){
                echo "\nregistroVentasIngresosSimplificadoTXT\n";
                $result = $this->registroVentasIngresosSimplificadoTXT(
                            $Data["ID_Organizacion"],
                            $Data["ID_Tipo_Asiento"],
                            $Data["ID_Tipo_Vista"],
                            $Data["ID_Tipo_Asiento_Detalle"],
                            $Data["Nu_Codigo_Libro_Sunat"],
                            $Data["No_Tipo_Asiento_Apertura"],
                            $Data["fYear"],
                            $Data["fMonth"],
                            $Data["fMonthText"],
                            1
                        );
            }
        }

        if($result){
            
             $arr = array(
                'Txt_Archivo' => $this->FileReporte,
                'Txt_Nombre_Archivo' => $this->FileName,
                'Fe_Finalizado' => date("Y-m-d G:i:s"),
                'ID_Estatus' => 2
             );
        }else{
            $ID_Reporte = $row->ID_Reporte;
             $arr = array(
                'Fe_Finalizado' => date("Y-m-d G:i:s"),
                'ID_Estatus' => 4
             );
        }
        $this->RegistroVentaIngresoModel->UpdateReporteBG($arr,$row->ID_Reporte);
    }

    // public function test(){
    //     $this->user->ID_Empresa = 272;
    // }
    
	public function registroVentasIngresosEXCEL($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText, $sNombreLibroSunat,$Background=0){
        $this->load->library('Excel');
	    
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = $this->security->xss_clean($sNombreLibroSunat);
        
		$this->FileName = $fileNameExcel = "RegistroVentasIngresos_" . $fMonthText . "_" . $fYear . ".xls";
        
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('14.1 Reg. de Ventas');
        
	    $hoja_activa = 0;
	    
	    $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A1', 'FORMATO ' . $sNombreLibroSunat);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A3', 'PERIODO: ')
        ->setCellValue('A4', 'RUC: ')
        ->setCellValue('A5', 'APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL: ');
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("17");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("70");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("14");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("14");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("16");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("16");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("13");
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("22");
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth("10");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth("25");
        
        $style_align_left = array(
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($style_align_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('B3', $fMonthText . ' ' . $fYear)
        ->setCellValue('B4', $this->empresa->Nu_Documento_Identidad)
        ->setCellValue('B5', $this->empresa->No_Empresa);
        
        $BStyle_top = array(
          'borders' => array(
            'top' => array(
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A7:Y7')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('D8:F8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('D9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('e9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('G8:I8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('G9:H9')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('G10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('K7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('L7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('M9:N9')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('M7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('N7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('O7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('P7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('Q7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('R7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('T8:U8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V8:Y8')->applyFromArray($BStyle_bottom);
        
        $objPHPExcel->getActiveSheet()->getStyle('S7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y11')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A7:Y7')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A8:Y8')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A9:Y9')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A10:Y10')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A11:Y11')->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A7', 'NÚMERO')
        ->setCellValue('A8', 'CORRELATIVO')
        ->setCellValue('A9', 'DEL REGISTRO O')
        ->setCellValue('A10', 'CÓDIGO UNICO')
        ->setCellValue('A11', 'DE LA OPERACIÓN');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('B7', 'FECHA DE')
        ->setCellValue('B8', 'EMISIÓN DEL')
        ->setCellValue('B9', 'COMPROBANTE')
        ->setCellValue('B10', 'DE PAGO')
        ->setCellValue('B11', 'O DOCUMENTO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('C7', 'FECHA')
        ->setCellValue('C8', 'DE')
        ->setCellValue('C9', 'VENCIMIENTO')
        ->setCellValue('C10', 'Y/O PAGO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('D7', 'COMPROBANTE DE PAGO');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D7:F7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('D8', 'O DOCUMENTO');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D8:F8');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('D10', 'TIPO')
        ->setCellValue('D11', '(TABLA 10)')
        ->setCellValue('E9', 'N° SERIE O')
        ->setCellValue('E10', 'N° DE SERIE DE LA')
        ->setCellValue('E11', 'MAQUINA REGISTRADORA')
        ->setCellValue('F10', 'NÚMERO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G7', 'INFORMACIÓN DEL CLIENTE');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G7:I7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G9', 'DOCUMENTO DE IDENTIDAD');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G9:H9');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G10', 'TIPO')
        ->setCellValue('G11', '(TABLA 2)')
        ->setCellValue('H10', 'NÚMERO')
        ->setCellValue('I9', 'APELLIDOS Y NOMBRES')
        ->setCellValue('I10', 'DENOMINACIÓN')
        ->setCellValue('I11', 'O RAZÓN SOCIAL');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('J7', 'VALOR')
        ->setCellValue('J8', 'FACTURADO')
        ->setCellValue('J9', 'DE LA')
        ->setCellValue('J10', 'EXPORTACIÓN');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('K7', 'BASE')
        ->setCellValue('K8', 'IMPONIBLE')
        ->setCellValue('K9', 'DE LA')
        ->setCellValue('K10', 'OPERACIÓN')
        ->setCellValue('K11', 'GRAVADA');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('L7', 'DESCUENTO BASE')
        ->setCellValue('L8', 'IMPONIBLE')
        ->setCellValue('L9', 'DE LA')
        ->setCellValue('L10', 'OPERACIÓN')
        ->setCellValue('L11', 'GRAVADA');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('M7', 'IMPORTE TOTAL DE LA OPERACIÓN')
        ->setCellValue('M8', 'EXONERADA O INAFECTA');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M7:N7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('M10', 'EXONERADA')
        ->setCellValue('N10', 'INAFECTA');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('O9', 'ISC');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('P9', 'IGV Y/0 IPM');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('Q9', 'DESCUENTO IGV Y/0 IPM');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('R9', 'ICBPER');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('S7', 'OTROS TRIBUTOS')
        ->setCellValue('S8', 'Y CARGOS QUE')
        ->setCellValue('S9', 'NO FORMAN PARTE')
        ->setCellValue('S10', 'DE LA')
        ->setCellValue('S11', 'BASE IMPONIBLE');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('T7', 'IMPORTE')
        ->setCellValue('T8', 'TOTAL')
        ->setCellValue('T9', 'DEL')
        ->setCellValue('T10', 'COMPROBANTE')
        ->setCellValue('T11', 'DE PAGO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('U8', 'TIPO')
        ->setCellValue('U9', 'DE')
        ->setCellValue('U10', 'CAMBIO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('V7', 'REFERENCIA DEL COMPROBANTE DE PAGO')
        ->setCellValue('V8', ' O DOCUMENTO ORIGINAL QUE SE MODIFICA');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('V7:Y7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('V8:Y8');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('V10', 'FECHA')
        ->setCellValue('W10', 'TIPO')
        ->setCellValue('W11', '(TABLA 10)')
        ->setCellValue('X10', 'SERIE')
        ->setCellValue('Y9', 'N° DEL')
        ->setCellValue('Y10', 'COMPROBANTE')
        ->setCellValue('Y11', 'DE PAGO O DOCUMENTO');
        
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
        
        $objPHPExcel->getActiveSheet()->freezePane('A12');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 12;
        
        $sum_Ss_Exportacion = 0.00;
        $sum_Ss_SubTotal_Gravadas = 0.00;
        $sum_Ss_Descuento = 0.00;
        $sum_Ss_IGV = 0.00;
        $sum_Ss_Descuento_IGV = 0.00;
        $sum_Ss_SubTotal_Inafecta = 0.00;
        $sum_Ss_SubTotal_Exonerada = 0.00;
        $sum_Ss_Icbper = 0.00;
        $sum_Ss_Total = 0.00;
        
        $sumGeneral_Ss_Exportacion = 0.00;
        $sumGeneral_Ss_SubTotal_Gravadas = 0.00;
        $sumGeneral_Ss_Descuento = 0.00;
        $sumGeneral_Ss_IGV = 0.00;
        $sumGeneral_Ss_Descuento_IGV = 0.00;
        $sumGeneral_Ss_SubTotal_Inafecta = 0.00;
        $sumGeneral_Ss_SubTotal_Exonerada = 0.00;
        $sumGeneral_Ss_Icbper = 0.00;
        $sumGeneral_Ss_Total = 0.00;

        $DOCU_Nu_Sunat_Codigo = '';
        $ID_Tipo_Documento = 0;
        $No_Tipo_Documento = '';
        $counter = 0;
        $fila_total = '';
        
        $arrParams = array(
            'ID_Organizacion' => $ID_Organizacion,
            'ID_Tipo_Asiento' => $ID_Tipo_Asiento,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrData = $this->getDataRegistroVentasIngresos($arrParams);
        if( $arrData['sStatus'] == 'success' ) {
            foreach ($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($style_all_border_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':I' . $fila)->applyFromArray($style_all_border_left);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':S' . $fila)->applyFromArray($style_all_border_right);
                $objPHPExcel->getActiveSheet()->getStyle('T' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
                $objPHPExcel->getActiveSheet()->getStyle('V' . $fila . ':X' . $fila)->applyFromArray($style_all_border_center);
                $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_all_border_left);
                
                 if ($DOCU_Nu_Sunat_Codigo != $row->DOCU_Nu_Sunat_Codigo) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('I' . $fila, 'Total ' . $No_Tipo_Documento)
                        ->setCellValue('J' . $fila, $sum_Ss_Exportacion)
                        ->setCellValue('K' . $fila, $sum_Ss_SubTotal_Gravadas)
                        ->setCellValue('L' . $fila, $sum_Ss_Descuento)
                        ->setCellValue('M' . $fila, $sum_Ss_SubTotal_Exonerada)
                        ->setCellValue('N' . $fila, $sum_Ss_SubTotal_Inafecta)
                        ->setCellValue('P' . $fila, $sum_Ss_IGV)
                        ->setCellValue('Q' . $fila, $sum_Ss_Descuento_IGV)
                        ->setCellValue('R' . $fila, $sum_Ss_Icbper)
                        ->setCellValue('T' . $fila, $sum_Ss_Total);
                        $fila_total = $fila++;
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila_total . ':T' . $fila_total)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila_total . ':T' . $fila_total)->getFont()->setBold(true);
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($style_all_border_center);
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':I' . $fila)->applyFromArray($style_all_border_left);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':S' . $fila)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('T' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('V' . $fila . ':X' . $fila)->applyFromArray($style_all_border_center);
                        $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_all_border_left);                
                    }
                    $sum_Ss_Exportacion = 0.00;
                    $sum_Ss_SubTotal_Gravadas = 0.00;
                    $sum_Ss_Descuento = 0.00;
                    $sum_Ss_IGV = 0.00;
                    $sum_Ss_Descuento_IGV = 0.00;
                    $sum_Ss_SubTotal_Inafecta = 0.00;
                    $sum_Ss_SubTotal_Exonerada = 0.00;
                    $sum_Ss_Icbper = 0.00;
                    $sum_Ss_Total = 0.00;
                    $DOCU_Nu_Sunat_Codigo = $row->DOCU_Nu_Sunat_Codigo;
                }

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->CUO)
                ->setCellValue('B' . $fila, $row->Fe_Emision)
                ->setCellValue('D' . $fila, $row->DOCU_Nu_Sunat_Codigo)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('F' . $fila, $row->ID_Numero_Documento_Inicial . ($row->ID_Numero_Documento_Final != '' ? $row->ID_Numero_Documento_Final : ''))
                ->setCellValue('G' . $fila, $row->IDE_Nu_Sunat_Codigo)
                ->setCellValue('H' . $fila, $row->Nu_Documento_Identidad)
                ->setCellValue('I' . $fila, $row->No_Entidad)
                ->setCellValue('J' . $fila, $row->Ss_Exportacion)
                ->setCellValue('K' . $fila, $row->Ss_SubTotal_Gravadas + $row->Ss_SubTotal_Gratuita)
                ->setCellValue('L' . $fila, $row->Ss_Descuento)
                ->setCellValue('M' . $fila, $row->Ss_SubTotal_Exonerada)
                ->setCellValue('N' . $fila, $row->Ss_SubTotal_Inafecta)
                ->setCellValue('P' . $fila, $row->Ss_IGV)
                ->setCellValue('Q' . $fila, $row->Ss_Descuento_IGV)
                ->setCellValue('R' . $fila, $row->Ss_Icbper)
                ->setCellValue('T' . $fila, $row->Ss_Total)
                ->setCellValue('U' . $fila, $row->Ss_Tipo_Cambio)
                ->setCellValue('V' . $fila, ($row->Fe_Emision_Modificar == '01/01/0001' ? '' : $row->Fe_Emision_Modificar))
                ->setCellValue('W' . $fila, $row->ID_Tipo_Documento_Modificar)
                ->setCellValue('X' . $fila, $row->ID_Serie_Documento_Modificar)
                ->setCellValue('Y' . $fila, $row->ID_Numero_Documento_Modificar);
                $fila++;
                $counter++;
                $sum_Ss_Exportacion += $row->Ss_Exportacion;
                $sum_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_SubTotal_Gratuita;
                $sum_Ss_Descuento += $row->Ss_Descuento;
                $sum_Ss_IGV += $row->Ss_IGV;
                $sum_Ss_Descuento_IGV += $row->Ss_Descuento_IGV;
                $sum_Ss_SubTotal_Inafecta += $row->Ss_SubTotal_Inafecta;
                $sum_Ss_SubTotal_Exonerada += $row->Ss_SubTotal_Exonerada;
                $sum_Ss_Icbper += $row->Ss_Icbper;
                $sum_Ss_Total += $row->Ss_Total;
                
                $sumGeneral_Ss_Exportacion += $row->Ss_Exportacion;
                $sumGeneral_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_SubTotal_Gratuita;
                $sumGeneral_Ss_Descuento += $row->Ss_Descuento;
                $sumGeneral_Ss_IGV += $row->Ss_IGV;
                $sumGeneral_Ss_Descuento_IGV += $row->Ss_Descuento_IGV;
                $sumGeneral_Ss_SubTotal_Inafecta += $row->Ss_SubTotal_Inafecta;
                $sumGeneral_Ss_SubTotal_Exonerada += $row->Ss_SubTotal_Exonerada;
                $sumGeneral_Ss_Icbper += $row->Ss_Icbper;
                $sumGeneral_Ss_Total += $row->Ss_Total;
                
                if ($ID_Tipo_Documento != $row->ID_Tipo_Documento) {
                    $ID_Tipo_Documento = $row->ID_Tipo_Documento;
                    $No_Tipo_Documento = $row->No_Tipo_Documento;
                }
            }
            //Totales
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':T' . $fila)->applyFromArray($style_all_border_right);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':T' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total ' . $No_Tipo_Documento)
            ->setCellValue('J' . $fila, $sum_Ss_Exportacion)
            ->setCellValue('K' . $fila, $sum_Ss_SubTotal_Gravadas)
            ->setCellValue('L' . $fila, $sum_Ss_Descuento)
            ->setCellValue('M' . $fila, $sum_Ss_SubTotal_Exonerada)
            ->setCellValue('N' . $fila, $sum_Ss_SubTotal_Inafecta)
            ->setCellValue('P' . $fila, $sum_Ss_IGV)
            ->setCellValue('Q' . $fila, $sum_Ss_Descuento_IGV)
            ->setCellValue('R' . $fila, $sum_Ss_Icbper)
            ->setCellValue('T' . $fila, $sum_Ss_Total);
    
            $fila++;
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':T' . $fila)->applyFromArray($style_all_border_right);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila . ':T' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Total General')
            ->setCellValue('J' . $fila, $sumGeneral_Ss_Exportacion)
            ->setCellValue('K' . $fila, $sumGeneral_Ss_SubTotal_Gravadas)
            ->setCellValue('L' . $fila, $sumGeneral_Ss_Descuento)
            ->setCellValue('M' . $fila, $sumGeneral_Ss_SubTotal_Exonerada)
            ->setCellValue('N' . $fila, $sumGeneral_Ss_SubTotal_Inafecta)
            ->setCellValue('P' . $fila, $sumGeneral_Ss_IGV)
            ->setCellValue('Q' . $fila, $sumGeneral_Ss_Descuento_IGV)
            ->setCellValue('R' . $fila, $sumGeneral_Ss_Icbper)
            ->setCellValue('T' . $fila, $sumGeneral_Ss_Total);
        } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('H' . $fila, 'Sin Operaciones')
            ->setCellValue('K' . $fila, 0.00)
            ->setCellValue('L' . $fila, 0.00)
            ->setCellValue('M' . $fila, 0.00)
            ->setCellValue('P' . $fila, 0.00)
            ->setCellValue('Q' . $fila, 0.00)
            ->setCellValue('R' . $fila, 0.00)
            ->setCellValue('S' . $fila, 0.00)
            ->setCellValue('T' . $fila, 0.00);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if($Background){
                try {
                    $this->FileReporte = md5(time().mt_rand(1,1000000));
                    $objWriter->save(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte);
                     return true;
                } catch (Exception $e) {
                    return false;
                }
		}
        else{
             header('Content-type: application/vnd.ms-excel');
             header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');
             $objWriter->save('php://output');
	    }
    }
	
	public function registroVentasIngresosTXT($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Vista, $ID_Tipo_Asiento_Detalle, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText,$Background=0){
        
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = '';
        
        //Indicador de operaciones
        $O = 1;//Empresa o entidad operativa
        
        //Indicador del contenido del libro o registro
        $I = 0;//Sin información
        
        //Indicador de la moneda utilizada
        $M = 1;//Soles
        
        //Indicador de libro electrónico generado por el PLE
        $G = 1;//Generado por PLE (Fijo)
        
        //ob_clean();
        
        $arrParams = array(
            'ID_Organizacion' => $ID_Organizacion,
            'ID_Tipo_Asiento' => $ID_Tipo_Asiento,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrDataModel = $this->getDataRegistroVentasIngresos($arrParams);
        if( $arrDataModel['sStatus'] == 'success' ) {
            $arrData = '';
            foreach ($arrDataModel['arrData'] as $row) {
                $arrData .= $row->Fe_Periodo . '|';
                $arrData .= $row->CUO . '|';
                $arrData .= $row->No_Tipo_Asiento_Apertura . $row->Correlativo . '|';
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= '|';
                $arrData .= $row->DOCU_Nu_Sunat_Codigo . '|';
                $arrData .= autocompletarConCeros('', $row->ID_Serie_Documento, 4, '0', STR_PAD_LEFT) . '|';
                $arrData .= $row->ID_Numero_Documento_Inicial . '|';
                $arrData .= ($row->ID_Numero_Documento_Final == '' ? '' : str_replace("-","",$row->ID_Numero_Documento_Final)) . '|';
                $arrData .= $row->IDE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Nu_Documento_Identidad . '|';
                $arrData .= $row->No_Entidad . '|';
                $arrData .= $row->Ss_Exportacion . '|';//13 = Valor facturado de la exportación
                $arrData .= $row->Ss_SubTotal_Gravadas + $row->Ss_SubTotal_Gratuita . '|';
                $arrData .= $row->Ss_Descuento . '|';
                $arrData .= $row->Ss_IGV . '|';
                $arrData .= $row->Ss_Descuento_IGV . '|';
                $arrData .= $row->Ss_SubTotal_Exonerada . '|';//18
                $arrData .= $row->Ss_SubTotal_Inafecta . '|';//19
                $arrData .= '|';//20 Impuesto Selectivo al Consumo, de ser el caso.
                $arrData .= '|';//21 B.I. Arroz Pilado
                $arrData .= '|';//22 IGV Arroz Pilado
                $arrData .= numberFormat(abs($row->Ss_Icbper), 2, '.', '') . '|';//23 Impuesto al Consumo de las Bolsas de Plástico.
                $arrData .= '|';//24 Otros conceptos, tributos y cargos que no forman parte de la base imponible
                $arrData .= $row->Ss_Total . '|';//25 Importe total del comprobante de pago
                $arrData .= $row->MONE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Ss_Tipo_Cambio . '|';
                $arrData .= $row->Fe_Emision_Modificar . '|';
                $arrData .= $row->ID_Tipo_Documento_Modificar . '|';
                $arrData .= $row->ID_Serie_Documento_Modificar . '|';
                $arrData .= $row->ID_Numero_Documento_Modificar . '|';
                $arrData .= '|';//31 Proyectos
                $arrData .= '|';//32 Error tipo
                $arrData .= $row->No_Codigo_Sunat_PLE . '|';
                
                //Datos de BD
                $iNewYear = ToYearDMY($row->Fe_Emision) + 1;
                $iMonthBD = ToMonthDMY($row->Fe_Emision);
                settype($iMonthBD, "int");
                
                //Año y mes a declarar
                settype($fYear, "int");
                settype($fMonth, "int");
                
                if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_SubTotal_Gratuita > 0 && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '0|';//31 (0 -> sin IGV dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_SubTotal_Inafecta > 0 || $row->Ss_SubTotal_Exonerada > 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_SubTotal_Inafecta < 0 || $row->Ss_SubTotal_Exonerada < 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado == 10 || $row->Nu_Estado == 7 )
                    $arrData .= '2|';//31 (2 Anulados)
                //else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_SubTotal_Inafecta > 0 || $row->Ss_SubTotal_Exonerada > 0) && ($iNewYear >= $fYear && $iMonthBD > $fMonth))
                    //$arrData .= '8|';//31 (declarado en un periodo posterior)
                //else
                    //$arrData .= '9|';//31 (9 rectificación o ajuste)
                $arrData .= "\n";
            }// /. foreach
            
            $I = 1;//Con información
        }

        $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
		$this->FileName = $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00140" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";
		
        if($Background){
            $this->FileReporte = md5(time().mt_rand(1,1000000));
            ob_start();
            $arrData = trim($arrData);
            echo $arrData;
            $content = ob_get_contents();
            ob_end_clean();
            $f = fopen(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte, "w");
            fwrite($f, $content);
            fclose($f);
            return true;
        }
        else{
    		header('Content-type: text/plain');
    		header('Content-Disposition: attachment; filename="' . $fileNameTXT . '"');
    		header('Cache-Control: no-cache, must-revalidate');
    		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    		
    		$arrData = trim($arrData);
    		die($arrData);
        }
	}
	
	public function registroVentasIngresosSimplificadoTXT($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Vista, $ID_Tipo_Asiento_Detalle, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText,$Background=0){
	    /*$tmp = array(
            "ID_Organizacion"=>$ID_Organizacion,
            "ID_Tipo_Asiento"=>$ID_Tipo_Asiento,
            "ID_Tipo_Vista"=>$ID_Tipo_Vista,
            "ID_Tipo_Asiento_Detalle"=>$ID_Tipo_Asiento_Detalle,
            "Nu_Codigo_Libro_Sunat"=>$Nu_Codigo_Libro_Sunat,
            "No_Tipo_Asiento_Apertura"=>$No_Tipo_Asiento_Apertura,
            "fYear"=>$fYear,
            "fMonth"=>$fMonth,
            "fMonthText"=>$fMonthText

        );
        echo "<pre>";
        print_r($tmp);
        echo "</pre>";

        echo "XXXXXX";
        exit();*/
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = '';
        
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
            'ID_Organizacion' => $ID_Organizacion,
            'ID_Tipo_Asiento' => $ID_Tipo_Asiento,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrDataModel = $this->getDataRegistroVentasIngresos($arrParams);
        if( $arrDataModel['sStatus'] == 'success' ) {
            $arrData = '';
            foreach ($arrDataModel['arrData'] as $row) {
                $arrData .= $row->Fe_Periodo . '|';
                $arrData .= $row->CUO . '|';
                $arrData .= $row->No_Tipo_Asiento_Apertura . $row->Correlativo . '|';
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= '|';
                $arrData .= $row->DOCU_Nu_Sunat_Codigo . '|';
                $arrData .= autocompletarConCeros('', $row->ID_Serie_Documento, 4, '0', STR_PAD_LEFT) . '|';
                $arrData .= $row->ID_Numero_Documento_Inicial . '|';
                $arrData .= ($row->ID_Numero_Documento_Final == '' ? '' : str_replace("-","",$row->ID_Numero_Documento_Final)) . '|';
                $arrData .= $row->IDE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Nu_Documento_Identidad . '|';
                $arrData .= $row->No_Entidad . '|';
                $arrData .= $row->Ss_SubTotal_Gravadas . '|';
                $arrData .= $row->Ss_IGV . '|';//Impuesto General a las Ventas y/o Impuesto de Promoción Municipal
                $arrData .= numberFormat(abs($row->Ss_Icbper), 2, '.', '') . '|';//23 Impuesto al Consumo de las Bolsas de Plástico.
                $arrData .= '|';//16 Otros conceptos, tributos y cargos que no forman parte de la base imponible
                $arrData .= $row->Ss_Total . '|';
                $arrData .= $row->MONE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Ss_Tipo_Cambio . '|';
                $arrData .= $row->Fe_Emision_Modificar . '|';
                $arrData .= $row->ID_Tipo_Documento_Modificar . '|';
                $arrData .= $row->ID_Serie_Documento_Modificar . '|';
                $arrData .= $row->ID_Numero_Documento_Modificar . '|';
                $arrData .= '|';//23 Error tipo
                $arrData .= $row->No_Codigo_Sunat_PLE . '|';
                //Datos de BD
                $iNewYear = ToYearDMY($row->Fe_Emision) + 1;
                $iMonthBD = ToMonthDMY($row->Fe_Emision);
                settype($iMonthBD, "int");
                
                //Año y mes a declarar
                settype($fYear, "int");
                settype($fMonth, "int");
                
                if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_SubTotal_Inafecta > 0 || $row->Ss_SubTotal_Exonerada > 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_SubTotal_Inafecta < 0 || $row->Ss_SubTotal_Exonerada < 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado == 10 || $row->Nu_Estado == 7)
                    $arrData .= '2|';//31 (2 Anulados)
                //else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_SubTotal_Inafecta > 0 || $row->Ss_SubTotal_Exonerada > 0) && ($iNewYear >= $fYear && $iMonthBD <= $fMonth))
                    //$arrData .= '8|';//31 (declarado en un periodo posterior)
                //else
                    //$arrData .= '9|';//31 (9 rectificación o ajuste)
                $arrData .= "\n";
            } // /. for each
            $I = 1;//Con información
        }

        $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
		$this->FileName = $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00140" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";

         if($Background){
            $this->FileReporte = md5(time().mt_rand(1,1000000));
            ob_start();
            $arrData = trim($arrData);
            echo $arrData;
            $content = ob_get_contents();
            ob_end_clean();
            $f = fopen(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$this->FileReporte.".txt", "w");
            fwrite($f, $content);
            fclose($f);
            return true;
        }
        else{
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="' . $fileNameTXT . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            $arrData = trim($arrData);
            die($arrData);

        }

	}
}