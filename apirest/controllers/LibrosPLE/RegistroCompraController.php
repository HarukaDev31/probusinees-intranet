<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
date_default_timezone_set('America/Lima');

class RegistroCompraController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('LibrosPLE/RegistroCompraModel');
		$this->load->model('HelperModel');
	}

	public function reporteRC(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('LibrosPLE/RegistroCompraView');
			$this->load->view('footer', array("js_registro_compras" => true));
		}
	}
	
    public function modificarCorrelativo(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->RegistroCompraModel->modificarCorrelativo($this->input->post()));
    }

	public function getTiposLibroSunat(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->RegistroCompraModel->getTiposLibroSunat($this->input->post('ID_Tipo_Asiento')));
	}
	
    private function getDataRegistroCompra($arrParams){
        $arrResponseModal = $this->RegistroCompraModel->registroCompra($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();

            $ID_Tipo_Documento = '';
            $ID_Serie_Documento = '';

            $iDetener = 0;

            $Ss_SubTotal_Gravadas = 0.00;
            $Ss_IGV = 0.00;
            $Ss_Inafecta = 0.00;
            $Ss_Exonerada = 0.00;
            $Ss_Gratuita = 0.00;
            $Ss_Exportacion = 0.00;
            $Ss_Icbper = 0.00;
            $Ss_Total = 0.00;
            
            $rows = array();
            
            $ID_Tipo_Vista = $arrParams['ID_Tipo_Vista'];
            $Nu_Codigo_Libro_Sunat = $arrParams['Nu_Codigo_Libro_Sunat'];
            $No_Tipo_Asiento_Apertura = $arrParams['No_Tipo_Asiento_Apertura'];
            $fYear = $arrParams['fYear'];
            $fMonth = $arrParams['fMonth'];
            $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
            
            foreach ($arrResponseModal['arrData'] as $row) {
                settype($row->CUO, "int");
                settype($row->Ss_SubTotal_Gravadas, "double");
                settype($row->Ss_Inafecta, "double");
                settype($row->Ss_Exonerada, "double");
                settype($row->Ss_Gratuita, "double");
                settype($row->Ss_Exportacion, "double");
                
                $fGravada = $row->Ss_SubTotal_Gravadas;
                $fIGV = $row->Ss_IGV_Gravadas;
                $fInafecta = $row->Ss_Inafecta;
                $fExonerada = $row->Ss_Exonerada;
                $fGratuita = $row->Ss_Gratuita;
                $fExportacion = $row->Ss_Exportacion;

                if($row->Ss_Descuento>0.00) {
                    $fGravada = ($fGravada - $row->Ss_Descuento);
                    $fIGV = ($fGravada * $fImpuestoConfiguracionIGV) - $fGravada;
                }
                
                if (
                    $iDetener == 0 &&
                    ($row->ID_Tipo_Documento != 4 && $row->ID_Tipo_Documento != 8 || (($row->ID_Tipo_Documento == 4 || $row->ID_Tipo_Documento == 8) && ($row->Ss_Exonerada >= 350.00 || $row->Ss_Inafecta >= 350.00 || $row->Ss_Gratuita >= 350.00)) || (($row->ID_Tipo_Documento == 4 || $row->ID_Tipo_Documento == 8) && $row->Ss_SubTotal_Gravadas >= 700.00) || $ID_Tipo_Vista == 1)
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
                    $rows_['ID_Numero_Documento_Inicial'] = $row->ID_Numero_Documento;
                    $rows_['ID_Numero_Documento_Final'] = '';
                    $rows_['IDE_Nu_Sunat_Codigo'] = $row->IDE_Nu_Sunat_Codigo;
                    $rows_['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                    $rows_['No_Entidad'] = $row->No_Entidad;
                    $rows_['Ss_SubTotal_Gravadas'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fGravada, 2, '.', '');
                    $rows_['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fIGV, 2, '.', '');
                    $rows_['Ss_Inafecta'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fInafecta, 2, '.', '');
                    $rows_['Ss_Exonerada'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fExonerada, 2, '.', '');
                    $rows_['Ss_Gratuita'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fGratuita, 2, '.', '');
                    $rows_['Ss_Exportacion'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($fExportacion, 2, '.', '');
                    $rows_['Ss_Icbper'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($row->Ss_Icbper, 2, '.', '');
                    $rows_['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($row->Ss_Total, 2, '.', '');
                    $rows_['MONE_Nu_Sunat_Codigo'] = $row->MONE_Nu_Sunat_Codigo;
                    $rows_['Ss_Tipo_Cambio'] = numberFormat( (($row->ID_Tipo_Documento != 5 && $row->ID_Tipo_Documento != 6) ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar), 3, '.', '');
                    $rows_['Fe_Emision_Modificar'] = $row->Fe_Emision_Modificar == '' ? '01/01/0001' : ToDateBD($row->Fe_Emision_Modificar);
                    $rows_['ID_Tipo_Documento_Modificar'] = $row->ID_Tipo_Documento_Modificar;
                    $rows_['ID_Serie_Documento_Modificar'] = $row->ID_Serie_Documento_Modificar;
                    $rows_['ID_Numero_Documento_Modificar'] = $row->ID_Numero_Documento_Modificar;
                    $rows_['No_Codigo_Sunat_PLE'] = 1;//Medio de pago
                    $rows_['No_Tipo_Documento'] = $row->No_Tipo_Documento;
                    $rows_['Ss_Percepcion'] = numberFormat($row->Ss_Percepcion, 2, '.', '');
                    $rows_['Fe_Detraccion'] = ($row->Nu_Detraccion == '' ? '01/01/0001' : ToDateBD($row->Fe_Detraccion));
                    $rows_['Nu_Detraccion'] = $row->Nu_Detraccion;
                    $rows_['Fe_Vencimiento'] = ToDateBD($row->Fe_Vencimiento);
                    $rows_['Nu_Estado'] = $row->Nu_Estado;
                    $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                    $rows_['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                    $rows_['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
                    if ( count($rows_) > 0 )
                        $data[] = (object)$rows_;
                    $ID_Tipo_Documento = '';
                }
                
                if ( $row->ID_Tipo_Documento == 4 || $row->ID_Tipo_Documento == 8 ){
                    if ( $ID_Tipo_Vista == 0 && $ID_Serie_Documento != $row->ID_Serie_Documento && ($row->Ss_SubTotal_Gravadas < 700.00 || $row->Ss_Exonerada < 350.00 || $row->Ss_Inafecta < 350.00 || $row->Ss_Gratuita < 350.00) ) {//inicial
                        $rows['Correlativo'] = $row->CUO;
                        $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
                        $rows['Fe_Periodo'] = $fYear . $fMonth . '00';
                        $rows['CUO'] = $Nu_Codigo_Libro_Sunat . $row->CUO;
                        $rows['No_Tipo_Asiento_Apertura'] = $No_Tipo_Asiento_Apertura;
                        $rows['Fe_Emision'] = ToDateBD($row->Fe_Emision);
                        $rows['DOCU_Nu_Sunat_Codigo_'] = $row->DOCU_Nu_Sunat_Codigo;
                        $rows['ID_Serie_Documento_'] = $row->ID_Serie_Documento;
                        $rows['ID_Numero_Documento_Inicial_'] = $row->ID_Numero_Documento;
                        $rows['IDE_Nu_Sunat_Codigo'] = $row->IDE_Nu_Sunat_Codigo;
                        $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
                        $rows['No_Entidad'] = $row->No_Entidad;
                        $rows['MONE_Nu_Sunat_Codigo'] = $row->MONE_Nu_Sunat_Codigo;
                        $rows['Ss_Tipo_Cambio'] = numberFormat( (($row->ID_Tipo_Documento != 5 && $row->ID_Tipo_Documento != 6) ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar), 3, '.', '');
                        $rows['Fe_Emision_Modificar'] = $row->Fe_Emision_Modificar == '' ? '01/01/0001' : ToDateBD($row->Fe_Emision_Modificar);
                        $rows['ID_Tipo_Documento_Modificar'] = $row->ID_Tipo_Documento_Modificar;
                        $rows['ID_Serie_Documento_Modificar'] = $row->ID_Serie_Documento_Modificar;
                        $rows['ID_Numero_Documento_Modificar'] = $row->ID_Numero_Documento_Modificar;
                        $rows['No_Codigo_Sunat_PLE'] = 1;//Medio de pago
                        $rows['No_Tipo_Documento'] = $row->No_Tipo_Documento;
                        $rows['Ss_Percepcion'] = numberFormat($row->Ss_Percepcion, 2, '.', '');
                        $rows['Fe_Detraccion'] = ($row->Nu_Detraccion == '' ? '01/01/0001' : ToDateBD($row->Fe_Detraccion));
                        $rows['Nu_Detraccion'] = $row->Nu_Detraccion;
                        $rows['Fe_Vencimiento'] = ToDateBD($row->Fe_Vencimiento);
                        $rows['Nu_Estado'] = $row->Nu_Estado;
                        $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
                        $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
                        $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
                        if ( isset($rows['ID_Numero_Documento_Final']) ) {//Para no tomar el primer arreglo, ver otra forma
                            $data[] = (object)$rows;
                            $Ss_SubTotal_Gravadas = 0.00;
                            $Ss_IGV = 0.00;
                            $Ss_Inafecta = 0.00;
                            $Ss_Exonerada = 0.00;
                            $Ss_Gratuita = 0.00;
                            $Ss_Exportacion = 0.00;
                            $Ss_Total = 0.00;
                        }
                    }
                    $ID_Tipo_Documento = $row->ID_Tipo_Documento;
                    $ID_Serie_Documento = $row->ID_Serie_Documento;
                }
                
                if ( $ID_Tipo_Vista == 0 && ($ID_Tipo_Documento == 4 || $ID_Tipo_Documento == 8) && $ID_Serie_Documento == $row->ID_Serie_Documento && ($row->Ss_SubTotal_Gravadas < 700.00 || $row->Ss_Exonerada < 350.00 || $row->Ss_Inafecta < 350.00 || $row->Ss_Gratuita < 350.00) ) {//final
                    $Ss_SubTotal_Gravadas += $fGravada;
                    $Ss_IGV += $fIGV;
                    $Ss_Inafecta += $fInafecta;
                    $Ss_Exonerada = $fExonerada;
                    $Ss_Gratuita = $fGratuita;
                    $Ss_Exportacion += $fExportacion;
                    $Ss_Icbper += $row->Ss_Icbper;
                    $Ss_Total += $row->Ss_Total;
                    
                    $rows['DOCU_Nu_Sunat_Codigo'] = $rows['DOCU_Nu_Sunat_Codigo_'];
                    $rows['ID_Numero_Documento_Inicial'] = $rows['ID_Numero_Documento_Inicial_'];
                    $rows['ID_Serie_Documento'] = $rows['ID_Serie_Documento_'];
                    $rows['ID_Numero_Documento_Final'] = '-' . $row->ID_Numero_Documento;
                    $rows['Ss_SubTotal_Gravadas'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_SubTotal_Gravadas, 2, '.', '');
                    $rows['Ss_IGV'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_IGV, 2, '.', '');
                    $rows['Ss_Inafecta'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Inafecta, 2, '.', '');
                    $rows['Ss_Exonerada'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Exonerada, 2, '.', '');
                    $rows['Ss_Gratuita'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Gratuita, 2, '.', '');
                    $rows['Ss_Exportacion'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Exportacion, 2, '.', '');
                    $rows['Ss_Icbper'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Icbper, 2, '.', '');
                    $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? '' : '-') . numberFormat($Ss_Total, 2, '.', '');
                } else {
                    $Ss_SubTotal_Gravadas = 0.00;
                    $Ss_IGV = 0.00;
                    $Ss_Inafecta = 0.00;
                    $Ss_Exonerada = 0.00;
                    $Ss_Gratuita = 0.00;
                    $Ss_Exportacion = 0.00;
                    $Ss_Icbper = 0.00;
                    $Ss_Total = 0.00;
                    
                    $ID_Serie_Documento = $row->ID_Serie_Documento;
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
    
	public function registroCompras(){
        $arrParams = array(
            'ID_Organizacion' => $this->input->post('ID_Organizacion'),
            'ID_Tipo_Asiento' => $this->input->post('ID_Tipo_Asiento'),
            'ID_Tipo_Vista' => $this->input->post('ID_Tipo_Vista'),
            'Nu_Codigo_Libro_Sunat' => $this->input->post('Nu_Codigo_Libro_Sunat'),
            'No_Tipo_Asiento_Apertura' => $this->input->post('No_Tipo_Asiento_Apertura'),
            'fYear' => $this->input->post('fYear'),
            'fMonth' => $this->input->post('fMonth'),
            'fMonthText' => '',
            'sNombreLibroSunat' => '',
            'ID_Tipo_Asiento_Detalle' => $this->input->post('ID_Tipo_Asiento_Detalle'),
        );
        echo json_encode($this->getDataRegistroCompra($arrParams));
    }
	
	public function registroCompraPDF($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText, $sNombreLibroSunat,$Background=0){
		$this->load->library('FormatoLibroSunatPDF');
		
		$ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = $this->security->xss_clean($sNombreLibroSunat);
        
        $this->FileName = $fileNamePDF = "RegistroCompra_" . $fMonthText . "_" . $fYear . ".pdf";
        
		$pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $arrCabecera = array (
            "sNombreLibroSunat" => $sNombreLibroSunat,
            "fYear" => $fYear,
            "fMonthText" => $fMonthText,
        );

        $arrParams = array(
            'ID_Organizacion' => $ID_Organizacion,
            'ID_Tipo_Asiento' => $ID_Tipo_Asiento,
            'ID_Tipo_Asiento_Detalle' => $ID_Tipo_Asiento_Detalle,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        
		ob_start();
		$file = $this->load->view('LibrosPLE/pdf/RegistroCompraPDF', array(
			'arrCabecera' => $arrCabecera,
			'arrDetalle' => $this->getDataRegistroCompra($arrParams),
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

    public function ReporteComprasLista(){
        
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->RegistroCompraModel->getReporte()));
    }

    public function CancelarReporte(){
        echo $this->RegistroCompraModel->CancelarReporte($this->input->post("ID_Reporte"));
    }

    public function BajarReporte($ID_Reporte){
        $row = $this->RegistroCompraModel->getReporteRow($ID_Reporte);
        if($row){
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
        }

    }

    public function CrearReporteCompras(){
        echo $this->RegistroCompraModel->CrearReporteCompras($this->input->post());
    }

    public function registroComprasBG(){
        $this->FileReporte = "";
        $this->FileName    = ""; 

        if(!is_cli()) // solo se ejecuta en terminal
            exit();

        $row = $this->RegistroCompraModel->getReporteBG();
        
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
        $this->RegistroCompraModel->UpdateReporteBG($arr,$row->ID_Reporte);
        //1=excel,2=pdf,3=txt
        if($row->Nu_Tipo_Formato==1){
            echo "\n formato Excel\n";
            $result = $this->registroCompraEXCEL(
                        $Data["ID_Organizacion"],
                        $Data["ID_Tipo_Asiento"],
                        $Data["ID_Tipo_Asiento_Detalle"],
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
            echo "\n formato PDF\n";
            $result = $this->registroCompraPDF(
                        $Data["ID_Organizacion"],
                        $Data["ID_Tipo_Asiento"],
                        $Data["ID_Tipo_Asiento_Detalle"],
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
            echo "\n formato TXT\n";
            echo "\n Salida detalle: ".$Data["ID_Tipo_Asiento_Detalle"]."\n";
            if($Data["ID_Tipo_Asiento_Detalle"]==1){
                echo "\nregistroVentasIngresosTXT\n";
                $result = $this->registroCompraTXT(
                            $Data["ID_Organizacion"],
                            $Data["ID_Tipo_Asiento"],
                            $Data["ID_Tipo_Asiento_Detalle"],
                            $Data["ID_Tipo_Vista"],
                            $Data["Nu_Codigo_Libro_Sunat"],
                            $Data["No_Tipo_Asiento_Apertura"],
                            $Data["fYear"],
                            $Data["fMonth"],
                            $Data["fMonthText"],
                            1
                        );
            }else if($Data["ID_Tipo_Asiento_Detalle"]==2){
                echo "\registroCompraNODomiciliadoTXT\n";
                $result = $this->registroCompraNODomiciliadoTXT(
                            $Data["ID_Organizacion"],
                            $Data["ID_Tipo_Asiento"],
                            $Data["ID_Tipo_Asiento_Detalle"],
                            $Data["ID_Tipo_Vista"],
                            $Data["Nu_Codigo_Libro_Sunat"],
                            $Data["No_Tipo_Asiento_Apertura"],
                            $Data["fYear"],
                            $Data["fMonth"],
                            $Data["fMonthText"],
                            1
                        );
            }
            else if($Data["ID_Tipo_Asiento_Detalle"]==3){
                echo "\nregistroVentasIngresosSimplificadoTXT\n";
                $result = $this->registroCompraSimplificadoTXT(
                            $Data["ID_Organizacion"],
                            $Data["ID_Tipo_Asiento"],
                            $Data["ID_Tipo_Asiento_Detalle"],
                            $Data["ID_Tipo_Vista"],
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
        $this->RegistroCompraModel->UpdateReporteBG($arr,$row->ID_Reporte);
    }

    // public function test(){
    //     $this->user->ID_Empresa = 272;
    // }
    
	public function registroCompraEXCEL($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText, $sNombreLibroSunat,$Background=0){
		$this->load->library('Excel');

		$ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
        $ID_Tipo_Vista = $this->security->xss_clean($ID_Tipo_Vista);
        $Nu_Codigo_Libro_Sunat = $this->security->xss_clean($Nu_Codigo_Libro_Sunat);
        $No_Tipo_Asiento_Apertura = $this->security->xss_clean($No_Tipo_Asiento_Apertura);
        $fYear = $this->security->xss_clean($fYear);
        $fMonth = $this->security->xss_clean($fMonth);
        $fMonthText = $this->security->xss_clean($fMonthText);
        $sNombreLibroSunat = $this->security->xss_clean($sNombreLibroSunat);
        
		$this->FileName = $fileNameExcel = "RegistroCompra_" . $fMonthText . "_" . $fYear . ".xls";
        
	    $objPHPExcel = new PHPExcel();
	    
	    $objPHPExcel->getActiveSheet()->setTitle('8.1 Reg. de Compras');
        
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("13");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("40");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("17");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("28");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("20");
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("13");
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth("25");
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth("12");
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth("20");
        
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
        
        $objPHPExcel->getActiveSheet()->getStyle('A7:AC7')->applyFromArray($BStyle_top);
        
        $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('A13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('B13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('C13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('D8:F8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('D9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('D13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('E13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('F13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('G13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('H8:J8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('H9:I9')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('H9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('H13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('I13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('J13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('K8:L8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('K9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('K13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('L13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('M8:N8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('M7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('M13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('N7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('N13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('O8:P8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('O7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('O13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('P7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('P13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('Q7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Q13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('R7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('R13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('S7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('S13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('T7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('T13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('U7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('U13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('W8:X8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('V7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('V13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('W7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('W13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('X7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('X13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('W8:AC8')->applyFromArray($BStyle_bottom);
        $objPHPExcel->getActiveSheet()->getStyle('Y9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Z9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Z10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Z11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Z12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('Z13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AA9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AA10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AA11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AA12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AA13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AB13')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC7')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC8')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC9')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC10')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC11')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC12')->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getStyle('AC13')->applyFromArray($BStyle_right);
        
        $objPHPExcel->getActiveSheet()->getStyle('A7:CB7')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A8:AC8')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A9:AC9')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A10:AC10')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A11:AC11')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A12:AC12')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A13:AC13')->getFont()->setBold(true);
        
        
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
        ->setCellValue('E9', 'SERIE O')
        ->setCellValue('E10', 'CÓDIGO DE LA')
        ->setCellValue('E11', 'DEPENDENCIA')
        ->setCellValue('E12', 'ADUANERA')
        ->setCellValue('E13', '(TABLA 11)')
        ->setCellValue('F7', 'AÑO DE')
        ->setCellValue('F8', 'EMISIÓN DE')
        ->setCellValue('F9', 'LA DUA')
        ->setCellValue('F10', 'O DSI');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('G7', 'N° DEL COMPROBANTE DE PAGO,')
        ->setCellValue('G8', 'DOCUMENTO, N° DE ORDEN DEL')
        ->setCellValue('G9', 'FORMULARIO FÍSICO O VIRTUAL,')
        ->setCellValue('G10', 'N° DE DUA, DSI O LIQUIDACIÓN DE')
        ->setCellValue('G11', 'COBRANZA U OTROS DOCUMENTOS')
        ->setCellValue('G12', 'EMITIDOS POR SUNAT PARA ACREDITAR')
        ->setCellValue('G13', 'EL CRÉDITO FISCAL EN LA IMPORTACIÓN');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('H7', 'INFORMACIÓN DEL')
        ->setCellValue('H8', 'PROVEEDOR');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H7:J7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H8:J8');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('H9', 'DOCUMENTO DE IDENTIDAD');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('H9:I9');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('H10', 'TIPO')
        ->setCellValue('H11', '(TABLA 2)')
        ->setCellValue('I10', 'NÚMERO')
        ->setCellValue('J9', 'APELLIDOS')
        ->setCellValue('J10', 'Y NOMBRES,')
        ->setCellValue('J11', 'DENOMINACIÓN')
        ->setCellValue('J12', 'O RAZÓN')
        ->setCellValue('J13', 'SOCIAL');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('K7', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES')
        ->setCellValue('K8', 'GRAVADAS Y/O DE EXPORTACIÓN');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K7:L7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K8:L8');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('K10', 'BASE')
        ->setCellValue('K11', 'IMPONIBLE')
        ->setCellValue('L11', 'IGV');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('M7', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES')
        ->setCellValue('N8', 'GRAVADAS Y/O DE EXPORTACIÓN Y A OPERACIONES NO GRAVADAS');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M7:N7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M8:N8');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('M10', 'BASE')
        ->setCellValue('M11', 'IMPONIBLE')
        ->setCellValue('N11', 'IGV');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('O7', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O7:P7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('P8', 'NO GRAVADAS');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O8:P8');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('O10', 'BASE')
        ->setCellValue('O11', 'IMPONIBLE')
        ->setCellValue('P11', 'IGV');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('Q8', 'VALOR')
        ->setCellValue('Q9', 'DE LAS')
        ->setCellValue('Q10', 'ADQUISICIONES')
        ->setCellValue('Q11', 'NO')
        ->setCellValue('Q12', 'GRAVADAS');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('R10', 'ISC');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('S10', 'ICBPER');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('T9', 'OTROS')
        ->setCellValue('T10', 'TRIBUTOS Y')
        ->setCellValue('T11', 'CARGOS');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('U9', 'IMPORTE')
        ->setCellValue('U10', 'TOTAL');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('V7', 'N° DE')
        ->setCellValue('V8', 'COMPROBANTE')
        ->setCellValue('V9', 'DE PAGO')
        ->setCellValue('V10', 'EMITIDO POR')
        ->setCellValue('V11', 'SUJETO NO')
        ->setCellValue('V12', 'DOMICILIADO (2)');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('W7', 'CONSTANCIA DE DEPÓSITO');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('W7:X7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('W8', 'DE DETRACCIÓN (3)');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('W8:X8');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('W11', 'NÚMERO')
        ->setCellValue('X10', 'FECHA')
        ->setCellValue('X11', 'DE')
        ->setCellValue('X12', 'EMISIÓN');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('Y9', 'TIPO')
        ->setCellValue('Y10', 'DE')
        ->setCellValue('Y11', 'CAMBIO');
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('Z7', 'REFERENCIA DEL COMPROBANTE DE PAGO')
        ->setCellValue('Z8', 'O DOCUMENTO ORIGINAL QUE SE MODIFICA');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Z7:AC7');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Z8:AC8');
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('Z10', 'FECHA')
        ->setCellValue('AA10', 'TIPO')
        ->setCellValue('AA11', '(TABLA 10)')
        ->setCellValue('AB10', 'SERIE')
        ->setCellValue('AC9', 'N° DEL')
        ->setCellValue('AC10', 'COMPROBANTE')
        ->setCellValue('AC11', 'DE PAGO O')
        ->setCellValue('AC12', 'DOCUMENTO');
        
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
        
        $objPHPExcel->getActiveSheet()->freezePane('A14');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
        
        $fila = 14;
        
        $sum_Ss_SubTotal_Gravadas = 0.00;
        $sum_Ss_SubTotal_Gravadas_Boletas = 0.00;
        $sum_Ss_IGV = 0.00;
        $sum_Ss_Gratuita = 0.00;
        $sum_Ss_Inafecta = 0.00;
        $sum_Ss_Exonerada = 0.00;
        $sum_Ss_Percepcion = 0.00;
        $sum_Ss_Icbper = 0.00;
        $sum_Ss_Total = 0.00;
        
        $sumGeneral_Ss_SubTotal_Gravadas = 0.00;
        $sumGeneral_Ss_IGV = 0.00;
        $sumGeneral_Ss_Gratuita = 0.00;
        $sumGeneral_Ss_Inafecta = 0.00;
        $sumGeneral_Ss_Exonerada = 0.00;
        $sumGeneral_Ss_Percepcion = 0.00;
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
            'ID_Tipo_Asiento_Detalle' => $ID_Tipo_Asiento_Detalle,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrData = $this->getDataRegistroCompra($arrParams);        
        if( $arrData['sStatus'] == 'success' ) {
            foreach ($arrData['arrData'] as $row) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($style_all_border_center);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':J' . $fila)->applyFromArray($style_all_border_left);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
                $objPHPExcel->getActiveSheet()->getStyle('V' . $fila . ':X' . $fila)->applyFromArray($style_all_border_left);
                $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_all_border_right);
                $objPHPExcel->getActiveSheet()->getStyle('Z' . $fila . ':AC' . $fila)->applyFromArray($style_all_border_left);
                
                 if ($DOCU_Nu_Sunat_Codigo != $row->DOCU_Nu_Sunat_Codigo) {
                    if ($counter != 0) {
                        $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('J' . $fila, 'Total ' . $No_Tipo_Documento)
                        ->setCellValue('K' . $fila, $sum_Ss_SubTotal_Gravadas)
                        ->setCellValue('L' . $fila, $sum_Ss_IGV)
                        ->setCellValue('M' . $fila, $sum_Ss_Gratuita)
                        ->setCellValue('Q' . $fila, $sum_Ss_Inafecta + $sum_Ss_Exonerada)
                        ->setCellValue('S' . $fila, $sum_Ss_Icbper)
                        ->setCellValue('T' . $fila, $sum_Ss_Percepcion)
                        ->setCellValue('U' . $fila, $sum_Ss_Percepcion + $sum_Ss_Total)
                        ;
                        $fila_total = $fila++;
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila_total . ':U' . $fila_total)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila_total . ':U' . $fila_total)->getFont()->setBold(true);
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($style_all_border_center);
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':J' . $fila)->applyFromArray($style_all_border_left);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('V' . $fila . ':X' . $fila)->applyFromArray($style_all_border_left);
                        $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_all_border_right);
                        $objPHPExcel->getActiveSheet()->getStyle('Z' . $fila . ':AC' . $fila)->applyFromArray($style_all_border_left);
                    }
                    $sum_Ss_SubTotal_Gravadas = 0.00;
                    $sum_Ss_IGV = 0.00;
                    $sum_Ss_Gratuita = 0.00;
                    $sum_Ss_Inafecta = 0.00;
                    $sum_Ss_Exonerada = 0.00;
                    $sum_Ss_Percepcion = 0.00;
                    $sum_Ss_Icbper = 0.00;
                    $sum_Ss_Total = 0.00;
                    $DOCU_Nu_Sunat_Codigo = $row->DOCU_Nu_Sunat_Codigo;
                }

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, $row->CUO)
                ->setCellValue('B' . $fila, $row->Fe_Emision)
                ->setCellValue('D' . $fila, $row->DOCU_Nu_Sunat_Codigo)
                ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
                ->setCellValue('G' . $fila, $row->ID_Numero_Documento_Inicial . $row->ID_Numero_Documento_Final )
                ->setCellValue('H' . $fila, $row->IDE_Nu_Sunat_Codigo)
                ->setCellValue('I' . $fila, $row->Nu_Documento_Identidad)
                ->setCellValue('J' . $fila, $row->No_Entidad)
                ->setCellValue('K' . $fila, $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion)
                ->setCellValue('L' . $fila, $row->Ss_IGV)
                ->setCellValue('M' . $fila, $row->Ss_Gratuita)
                ->setCellValue('Q' . $fila, $row->Ss_Inafecta + $row->Ss_Exonerada)
                ->setCellValue('S' . $fila, $row->Ss_Icbper)
                ->setCellValue('T' . $fila, $row->Ss_Percepcion)
                ->setCellValue('U' . $fila, $row->Ss_Total + $row->Ss_Percepcion)
                ->setCellValue('W' . $fila, $row->Nu_Detraccion)
                ->setCellValue('X' . $fila, ($row->Fe_Detraccion == '01/01/0001' ? '' : $row->Fe_Detraccion))
                ->setCellValue('Y' . $fila, $row->Ss_Tipo_Cambio)
                ->setCellValue('Z' . $fila, ($row->Fe_Emision_Modificar == '01/01/0001' ? '' : $row->Fe_Emision_Modificar))
                ->setCellValue('AA' . $fila, $row->ID_Tipo_Documento_Modificar)
                ->setCellValue('AB' . $fila, $row->ID_Serie_Documento_Modificar)
                ->setCellValue('AC' . $fila, $row->ID_Numero_Documento_Modificar)
                ;
                $fila++;
                $counter++;
                $sum_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion;
                $sum_Ss_IGV += $row->Ss_IGV;
                $sum_Ss_Gratuita += $row->Ss_Gratuita;
                $sum_Ss_Inafecta += $row->Ss_Inafecta;
                $sum_Ss_Exonerada += $row->Ss_Exonerada;
                $sum_Ss_Percepcion += $row->Ss_Percepcion;
                $sum_Ss_Icbper += $row->Ss_Icbper;
                $sum_Ss_Total += $row->Ss_Total;
                
                $sumGeneral_Ss_SubTotal_Gravadas += $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion;
                $sumGeneral_Ss_IGV += $row->Ss_IGV;
                $sumGeneral_Ss_Gratuita += $row->Ss_Gratuita;
                $sumGeneral_Ss_Inafecta += $row->Ss_Inafecta;
                $sumGeneral_Ss_Exonerada += $row->Ss_Exonerada;
                $sumGeneral_Ss_Percepcion += $row->Ss_Percepcion;
                $sumGeneral_Ss_Icbper += $row->Ss_Icbper;
                $sumGeneral_Ss_Total += $row->Ss_Total;
                
                if ($ID_Tipo_Documento != $row->ID_Tipo_Documento) {
                    $ID_Tipo_Documento = $row->ID_Tipo_Documento;
                    $No_Tipo_Documento = $row->No_Tipo_Documento;
                }
            }
            //Totales
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':U' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('J' . $fila, 'Total ' . $No_Tipo_Documento)
            ->setCellValue('K' . $fila, $sum_Ss_SubTotal_Gravadas)
            ->setCellValue('L' . $fila, $sum_Ss_IGV)
            ->setCellValue('M' . $fila, $sum_Ss_Gratuita)
            ->setCellValue('Q' . $fila, $sum_Ss_Inafecta + $sum_Ss_Exonerada)
            ->setCellValue('S' . $fila, $sum_Ss_Icbper)
            ->setCellValue('T' . $fila, $sum_Ss_Percepcion)
            ->setCellValue('U' . $fila, $sum_Ss_Percepcion + $sum_Ss_Total);
    
            $fila++;
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':U' . $fila)->applyFromArray($style_all_border_right);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila . ':U' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('J' . $fila, 'Total General')
            ->setCellValue('K' . $fila, $sumGeneral_Ss_SubTotal_Gravadas)
            ->setCellValue('L' . $fila, $sumGeneral_Ss_IGV)
            ->setCellValue('M' . $fila, $sumGeneral_Ss_Gratuita)
            ->setCellValue('Q' . $fila, $sumGeneral_Ss_Inafecta + $sumGeneral_Ss_Exonerada)
            ->setCellValue('S' . $fila, $sumGeneral_Ss_Icbper)
            ->setCellValue('T' . $fila, $sumGeneral_Ss_Percepcion)
            ->setCellValue('U' . $fila, $sumGeneral_Ss_Percepcion + $sumGeneral_Ss_Total);
	    } else {
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('I' . $fila, 'Sin Operaciones')
            ->setCellValue('K' . $fila, 0.00)
            ->setCellValue('L' . $fila, 0.00)
            ->setCellValue('Q' . $fila, 0.00)
            ->setCellValue('S' . $fila, 0.00)
            ->setCellValue('T' . $fila, 0.00)
            ->setCellValue('U' . $fila, 0.00)
            ;
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
    
    public function registroCompraTXT($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText,$Background=0){
        
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
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
            'ID_Tipo_Asiento_Detalle' => $ID_Tipo_Asiento_Detalle,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrDataModel = $this->getDataRegistroCompra($arrParams);        
        if( $arrDataModel['sStatus'] == 'success' ) {
            $arrData = '';
            foreach ($arrDataModel['arrData'] as $row) {
                $arrData .= $row->Fe_Periodo . '|';
                $arrData .= $row->CUO . '|';
                $arrData .= $row->No_Tipo_Asiento_Apertura . $row->Correlativo . '|';
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= ($row->ID_Tipo_Documento == 10 ? $row->Fe_Vencimiento . '|' : '01/01/0001|');//Obligatorio si tipo de documento = '14'
                $arrData .= $row->DOCU_Nu_Sunat_Codigo . '|';
                $arrData .= autocompletarConCeros('', $row->ID_Serie_Documento, 4, '0', STR_PAD_LEFT) . '|';
                $arrData .= '0|';//Año de emisión DUA
                $arrData .= $row->ID_Numero_Documento_Inicial . '|';
                $arrData .= ($row->ID_Numero_Documento_Final == '' ? '' : str_replace("-","",$row->ID_Numero_Documento_Final)) . '|';
                $arrData .= $row->IDE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Nu_Documento_Identidad . '|';
                $arrData .= $row->No_Entidad . '|';
                $arrData .= $row->Ss_SubTotal_Gravadas + $row->Ss_Exportacion . '|';
                $arrData .= $row->Ss_IGV . '|';//15 Monto del Impuesto General a las Ventas y/o Impuesto de Promoción Municipal
                $arrData .= $row->Ss_Gratuita . '|';//16 B.I. Y OTROS
                $arrData .= '0.00|';//17 Monto del Impuesto General a las Ventas y/o Impuesto de Promoción Municipal
                $arrData .= '0.00|';//18 B.I. Y OTROS
                $arrData .= '0.00|';//19 Monto del Impuesto General a las Ventas y/o Impuesto de Promoción Municipal
                $arrData .= ($row->Ss_Exonerada + $row->Ss_Inafecta) . '|';//20 Valor de las adquisiciones no gravadas
                $arrData .= '0.00|';//21 ISC
                $arrData .= $row->Ss_Icbper . '|';//22 Impuesto al Consumo de las Bolsas de Plástico.
                $arrData .= ($row->Ss_Percepcion) . '|';//23 Otros conceptos, tributos y cargos que no formen parte de la base imponible.
                $arrData .= ($row->Ss_Total + $row->Ss_Percepcion) . '|';
                $arrData .= $row->MONE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Ss_Tipo_Cambio . '|';
                $arrData .= ($row->Fe_Emision_Modificar == '01/01/0001' ? '' : $row->Fe_Emision_Modificar) . '|';
                $arrData .= $row->ID_Tipo_Documento_Modificar . '|';
                $arrData .= $row->ID_Serie_Documento_Modificar . '|';
                $arrData .= '|';//DUA 29
                $arrData .= $row->ID_Numero_Documento_Modificar . '|';
                $arrData .= $row->Fe_Detraccion . '|';//F. Detraccion
                $arrData .= $row->Nu_Detraccion . '|';//Num. Detraccion
                $arrData .= '|';//Retencion
                $arrData .= '|';//1500 UIT
                $arrData .= '|';//Proyecto
                $arrData .= '|';//36
                $arrData .= '|';//37
                $arrData .= '|';//38
                $arrData .= '|';//39
                $arrData .= $row->No_Codigo_Sunat_PLE . '|';//40 Medio pago
                
                //Datos de BD
                $iNewYear = ToYearDMY($row->Fe_Emision) + 1;
                $iMonthBD = ToMonthDMY($row->Fe_Emision);
                settype($iMonthBD, "int");
                
                //Datos de BD - PERIODO
                $iNewYearPeriodo = ToYearDMY($row->Fe_Periodo);
                $iMonthBDPeriodo = ToMonthDMY($row->Fe_Periodo);
                settype($iMonthBD, "int");
                
                //Año y mes a declarar
                settype($fYear, "int");
                settype($fMonth, "int");
                
                if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '0|';//31 (0 -> sin IGV dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '7|';//31 (0 -> sin IGV declarado en un periodo posterior)
                else
                    $arrData .= '9|';//31 (9 rectificación o ajuste)
                $arrData .= "\n";
            } // /. for each
            
            $I = 1;//Con información
        }// /. if

        $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
        $this->FileName = $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00080" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";
        
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
    
    public function registroCompraNODomiciliadoTXT($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText,$Background=0){
        
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
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
            'ID_Tipo_Asiento_Detalle' => $ID_Tipo_Asiento_Detalle,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrDataModel = $this->getDataRegistroCompra($arrParams);
        if( $arrDataModel['sStatus'] == 'success' ) {
            $arrData = '';
            foreach ($arrDataModel['arrData'] as $row) {
                $arrData .= $row->Fe_Periodo . '|';
                $arrData .= $row->CUO . '|';
                $arrData .= $row->No_Tipo_Asiento_Apertura . $row->Correlativo . '|';
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= $row->DOCU_Nu_Sunat_Codigo . '|';
                $arrData .= autocompletarConCeros('', $row->ID_Serie_Documento, 4, '0', STR_PAD_LEFT) . '|';
                $arrData .= $row->ID_Numero_Documento_Inicial . '|';
                $arrData .= '0.00|';//8 Valor de las adquisiciones
                $arrData .= '0.00|';//9 Otros conceptos adicionales
                $arrData .= '0.00|';//10 Importe total de las adquisiciones
                $arrData .= '|';//11 Tipo docu Pago
                $arrData .= '|';//12 Serie docu o DUA
                $arrData .= '|';//13 Año DUA
                $arrData .= '|';//14 Num docu
                $arrData .= '0.00|';//15 Monto Retencion IGV
                $arrData .= $row->MONE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Ss_Tipo_Cambio . '|';
                $arrData .= '|';//18 Pais
                $arrData .= $row->No_Entidad . '|';// 19 Nombre no domiciliado
                $arrData .= '|';//20 Direccion
                $arrData .= $row->Nu_Documento_Identidad . '|';
                $arrData .= '|';//22 Num efectivo pago
                $arrData .= $row->No_Entidad . '|';//23 Nombre del beneficiario efectivo de los pagos.
                $arrData .= '|';//24 Pais
                $arrData .= '|';//25 Vínculo entre el contribuyente y el residente en el extranjero
                $arrData .= '0.00|';//26 Renta Bruta
                $arrData .= '0.00|';//27 Deducción / Costo de Enajenación de bienes de capital
                $arrData .= '0.00|';//28 Renta Neta
                $arrData .= '0.00|';//29 Tasa de retencion
                $arrData .= '0.00|';//30 Impuesto retenido
                $arrData .= '0.00|';//31 Convenios para evitar la doble imposición
                $arrData .= '0.00|';//32 Exoneración aplicada
                $arrData .= '0.00|';//33 Tipo renta
                $arrData .= '0.00|';//34 Modalidad del servicio prestado por el no domiciliado 
                $arrData .= '0.00|';//35 Aplicación del penultimo parrafo del Art. 76° de la Ley del Impuesto a la Renta

                //Datos de BD
                $iNewYear = ToYearDMY($row->Fe_Emision) + 1;
                $iMonthBD = ToMonthDMY($row->Fe_Emision);
                settype($iMonthBD, "int");
                
                //Datos de BD - PERIODO
                $iNewYearPeriodo = ToYearDMY($row->Fe_Periodo);
                $iMonthBDPeriodo = ToMonthDMY($row->Fe_Periodo);
                settype($iMonthBD, "int");
                
                //Año y mes a declarar
                settype($fYear, "int");
                settype($fMonth, "int");
                
                if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '0|';//31 (0 -> sin IGV dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '7|';//31 (0 -> sin IGV declarado en un periodo posterior)
                else
                    $arrData .= '9|';//31 (9 rectificación o ajuste)
                $arrData .= "\n";
            }// /. for each
            
            $I = 1;//Con información
        } // /. if
        $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
        $this->FileName = $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00080" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";

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
    
    public function registroCompraSimplificadoTXT($ID_Organizacion, $ID_Tipo_Asiento, $ID_Tipo_Asiento_Detalle, $ID_Tipo_Vista, $Nu_Codigo_Libro_Sunat, $No_Tipo_Asiento_Apertura, $fYear, $fMonth, $fMonthText,$Background=0){
        
        $ID_Organizacion = $this->security->xss_clean($ID_Organizacion);
        $ID_Tipo_Asiento = $this->security->xss_clean($ID_Tipo_Asiento);
        $ID_Tipo_Asiento_Detalle = $this->security->xss_clean($ID_Tipo_Asiento_Detalle);
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
            'ID_Tipo_Asiento_Detalle' => $ID_Tipo_Asiento_Detalle,
            'ID_Tipo_Vista' => $ID_Tipo_Vista,
            'Nu_Codigo_Libro_Sunat' => $Nu_Codigo_Libro_Sunat,
            'No_Tipo_Asiento_Apertura' => $No_Tipo_Asiento_Apertura,
            'fYear' => $fYear,
            'fMonth' => $fMonth,
            'fMonthText' => $fMonthText,
            'sNombreLibroSunat' => $sNombreLibroSunat,
        );
        $arrDataModel = $this->getDataRegistroCompra($arrParams);        
        if( $arrDataModel['sStatus'] == 'success' ) {
            $arrData = '';
            foreach ($arrDataModel['arrData'] as $row) {
                $arrData .= $row->Fe_Periodo . '|';
                $arrData .= $row->CUO . '|';
                $arrData .= $row->No_Tipo_Asiento_Apertura . $row->Correlativo . '|';
                $arrData .= $row->Fe_Emision . '|';
                $arrData .= ($row->ID_Tipo_Documento == 10 ? $row->Fe_Vencimiento .'|' : '01/01/0001|');//Obligatorio si tipo de documento = '14'
                $arrData .= $row->DOCU_Nu_Sunat_Codigo . '|';
                $arrData .= autocompletarConCeros('', $row->ID_Serie_Documento, 4, '0', STR_PAD_LEFT) . '|';
                $arrData .= '0|';//Año de emisión DUA
                $arrData .= $row->ID_Numero_Documento_Inicial . '|';
                $arrData .= ($row->ID_Numero_Documento_Final == '' ? '' : str_replace("-","",$row->ID_Numero_Documento_Final)) . '|';
                $arrData .= $row->IDE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Nu_Documento_Identidad . '|';
                $arrData .= $row->No_Entidad . '|';
                $arrData .= $row->Ss_SubTotal_Gravadas . '|';
                $arrData .= $row->Ss_IGV . '|';// 14 Monto del Impuesto General a las Ventas y/o Impuesto de Promoción Municipal
                $arrData .= $row->Ss_Icbper . '|';// 15 Impuesto al Consumo de las Bolsas de Plástico.
                $arrData .= $row->Ss_Exonerada + $row->Ss_Inafecta + $row->Ss_Percepcion . '|';//Otros Cargos
                $arrData .= ($row->Ss_Total + $row->Ss_Exonerada + $row->Ss_Inafecta + $row->Ss_Percepcion) . '|';
                $arrData .= $row->MONE_Nu_Sunat_Codigo . '|';
                $arrData .= $row->Ss_Tipo_Cambio . '|';
                $arrData .= $row->Fe_Emision_Modificar . '|';
                $arrData .= $row->ID_Tipo_Documento_Modificar . '|';
                $arrData .= $row->ID_Serie_Documento_Modificar . '|';
                $arrData .= $row->ID_Numero_Documento_Modificar . '|';
                $arrData .= $row->Fe_Detraccion . '|';//F. Detraccion
                $arrData .= $row->Nu_Detraccion . '|';//Num. Detraccion
                $arrData .= '|';//Retencion
                $arrData .= '|';//1500 UIT
                $arrData .= '|';//27
                $arrData .= '|';//28
                $arrData .= '|';//29
                $arrData .= $row->No_Codigo_Sunat_PLE . '|';//30 Medio pago
                
                //Datos de BD
                $iNewYear = ToYearDMY($row->Fe_Emision) + 1;
                $iMonthBD = ToMonthDMY($row->Fe_Emision);
                settype($iMonthBD, "int");
                
                //Datos de BD - PERIODO
                $iNewYearPeriodo = ToYearDMY($row->Fe_Periodo);
                $iMonthBDPeriodo = ToMonthDMY($row->Fe_Periodo);
                settype($iMonthBD, "int");
                
                //Año y mes a declarar
                settype($fYear, "int");
                settype($fMonth, "int");
                
                if ($row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '0|';//31 (0 -> sin IGV dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ToYearDMY($row->Fe_Emision) == $fYear && $iMonthBD == $fMonth)
                    $arrData .= '1|';//31 (1 -> con IGV y dentro del mismo periodo)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas > 0 || $row->Ss_Inafecta > 0 || $row->Ss_Exonerada > 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && ($row->Ss_SubTotal_Gravadas < 0 || $row->Ss_Inafecta < 0 || $row->Ss_Exonerada < 0) && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '6|';//31 (1 -> con IGV declarado en un periodo posterior)
                else if ( $row->Nu_Estado != 10 && $row->Nu_Estado != 7 && $row->Ss_Gratuita > 0 && ($iNewYearPeriodo >= $fYear && $iMonthBDPeriodo > $iMonthBD))
                    $arrData .= '7|';//31 (0 -> sin IGV declarado en un periodo posterior)
                else
                    $arrData .= '9|';//31 (9 rectificación o ajuste)
                $arrData .= "\n";
            }// /. for each
            
            $I = 1;//Con información
        } // /. if

        $fMonth = (strlen($fMonth) > 1 ? $fMonth : '0' . $fMonth);
        $this->FileName = $fileNameTXT = "LE" . $this->empresa->Nu_Documento_Identidad . "" . $fYear . "" . $fMonth . "00080" . $ID_Tipo_Asiento_Detalle . "0000" . $O . $I . $M . $G . ".txt";

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
}
