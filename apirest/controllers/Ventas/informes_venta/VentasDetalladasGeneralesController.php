<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 400); //300 seconds = 5 minutes y mas
date_default_timezone_set('America/Lima');

class VentasDetalladasGeneralesController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Ventas/informes_venta/VentasDetalladasGeneralesModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Ventas/informes_venta/VentasDetalladasGeneralesView');
			$this->load->view('footer', array("js_ventas_detalladas_generales" => true));
		}
	}	
	
  private function getReporte($arrParams){
    $arrResponseModal = $this->VentasDetalladasGeneralesModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
      
      $fSubTotal = 0.00;
      $fIGV = 0.00;
      $fDescuento = 0.00;
      $fTotal = 0.00;
      $sAccionVer='ver';
      $sAccionImprimir='imprimir';
      $sVacio='';
      
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        
        $rows['Nu_Tipo_Impuesto'] = $row->Nu_Tipo_Impuesto;

        $rows['ID_Almacen'] = $row->ID_Almacen;
        $rows['No_Almacen'] = $row->No_Almacen;
        //$rows['Fe_Emision_Hora'] = ToDateBD(allTypeDate($row->Fe_Emision_Hora, ' ', 2));
        $rows['Fe_Emision_Hora'] = ToDateBD($row->Fe_Emision);
        $rows['Fe_Hora'] = allTypeDate($row->Fe_Emision_Hora, ' ', 3);
        $rows['No_Empleado'] = !empty($row->No_Empleado) ? $row->No_Empleado : '';
        $rows['ID_Tipo_Documento'] = $row->ID_Tipo_Documento;
        $rows['No_Tipo_Documento_Breve'] = $row->No_Tipo_Documento_Breve;
        $rows['ID_Serie_Documento'] = $row->ID_Serie_Documento;
        $rows['ID_Numero_Documento'] = $row->ID_Numero_Documento;
        
        $rows['No_Tipo_Documento_Identidad_Breve'] = $row->No_Tipo_Documento_Identidad_Breve;
        $rows['Nu_Documento_Identidad'] = $row->Nu_Documento_Identidad;
        $rows['No_Entidad'] = $row->No_Entidad;

        $rows['No_Signo'] = $row->No_Signo;
        $rows['Ss_Tipo_Cambio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Tipo_Cambio : $row->Ss_Tipo_Cambio_Modificar);
        
        $rows['No_Marca'] = (!empty($row->No_Marca) ? $row->No_Marca : '');
        $rows['No_Familia'] = (!empty($row->No_Familia) ? $row->No_Familia : '');
        $rows['No_Sub_Familia'] = (!empty($row->No_Sub_Familia) ? $row->No_Sub_Familia : '');
        $rows['No_Unidad_Medida'] = (!empty($row->No_Unidad_Medida) ? $row->No_Unidad_Medida : '');
        $rows['Nu_Codigo_Barra'] = (!empty($row->Nu_Codigo_Barra) ? $row->Nu_Codigo_Barra : '');
        $rows['No_Producto'] = (!empty($row->No_Producto) ? $row->No_Producto : '');
        $rows['Txt_Nota_Item'] = (!empty($row->Txt_Nota_Item) ? $row->Txt_Nota_Item : '') . ($row->Nu_Tipo_Impuesto==4 ? ' REGALO' : '');
        $rows['Qt_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Qt_Producto : -$row->Qt_Producto);
        $rows['Qt_CO2_Producto'] = (!empty($row->Qt_CO2_Producto) ? $row->Qt_CO2_Producto : '');
        $rows['Ss_Precio'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Precio : -$row->Ss_Precio);

        //OBTENER CANTIDAD DE REGISTROS X DOCUMENTO DE VENTA DETALLE
        if($row->Ss_Descuento > 0.00) {
        $arrCantidadItemDocumentoVentaDetalle = $this->HelperModel->getCantidadItemDocumentoVentaDetalle($row->ID_Documento_Cabecera);
          if($arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento > 0) {
            $row->Ss_Descuento_Global = ($row->Ss_Descuento_Global / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
            $row->Ss_Descuento = ($row->Ss_Descuento / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);
            $row->Ss_Descuento_Impuesto = ($row->Ss_Descuento_Impuesto / $arrCantidadItemDocumentoVentaDetalle->cantidad_item_x_documento);

            $row->Ss_Subtotal = $row->Ss_Subtotal - $row->Ss_Descuento;
            $row->Ss_Impuesto = $row->Ss_Impuesto - $row->Ss_Descuento_Impuesto;
            $row->Ss_Total = $row->Ss_Total - $row->Ss_Descuento_Global;
          }
        }
        
        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Subtotal'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Subtotal : -$row->Ss_Subtotal);
        else
          $rows['Ss_Subtotal'] = $row->Ss_Total;

        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Impuesto'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Impuesto : -$row->Ss_Impuesto);
        else
          $rows['Ss_Impuesto'] = 0;

        $rows['Ss_Total'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Total : -$row->Ss_Total);
        $rows['Txt_Nota'] = $row->Txt_Nota;

        $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($row->Nu_Estado);
        $rows['No_Estado'] = $arrEstadoDocumento['No_Estado'];
        $rows['No_Class_Estado'] = $arrEstadoDocumento['No_Class_Estado'];
        $rows['Nu_Estado'] = $row->Nu_Estado;

        $arrEstadoRecepcion = $this->HelperModel->obtenerEstadoRecepcionArray($row->Nu_Tipo_Recepcion);
        $rows['No_Tipo_Recepcion'] = $arrEstadoRecepcion['No_Estado'];

        $rows['No_Delivery'] = (!empty($row->No_Delivery) ? $row->No_Delivery : '');
        $rows['Fe_Entrega'] = (!empty($row->Fe_Entrega) ? ToDateBD($row->Fe_Entrega) : '');

        $arrEstadoDespacho = $this->HelperModel->obtenerEstadoDespachoArray($row->Nu_Estado_Despacho_Pos);
        $sNombreEstado = '';
        $sClaseEstado = '';
        if(!empty($arrEstadoDespacho)) {
          $sNombreEstado = $arrEstadoDespacho['No_Estado'];
          $sClaseEstado = $arrEstadoDespacho['No_Class_Estado'];
        }
        $rows['No_Estado_Delivery'] = $sNombreEstado;
        $rows['No_Class_Estado_Delivery'] = $sClaseEstado;

        $tipoGuiaxFacturaGarantia = '';
        $serieGuiaxFacturaGarantia = '';
        $numeroGuiaxFacturaGarantia = '';
				$cadena_de_texto = trim($row->Txt_Garantia);
        if ( substr($cadena_de_texto, -1) == ',' ) 
					$cadena_de_texto = substr($cadena_de_texto, 0, -1);
        
    		$cadena_buscada = '-';
        $posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
        if ( strlen($row->Txt_Garantia) > 5 && $posicion_coincidencia !== false) {
          $arrCadena = explode(',',$row->Txt_Garantia);
          foreach ($arrCadena as $row_guia) {
            $arrSerieNumero = explode('-', $row_guia);
            if ( strlen(trim($arrSerieNumero[0])) == 4 && isset($arrSerieNumero[1]) ) {
              $tipoGuiaxFacturaGarantia = 'G/Remisión';
              $serieGuiaxFacturaGarantia = trim($arrSerieNumero[0]);
              $numeroGuiaxFacturaGarantia = substr(trim($arrSerieNumero[1]), 0, 8);
            }
          }
        }
        
        $iExisteGuiaEnlace=0;
        $span_enlace_guias_tipo = '';
        $span_enlace_guias_serie = '';
        $span_enlace_guias_numero = '';
        $arrParams = array('ID_Documento_Cabecera' => $row->ID_Documento_Cabecera);
        $arrResponseDocument = $this->HelperModel->getGuianEnlace($arrParams);        
        if ($arrResponseDocument['sStatus'] == 'success') {
          $iExisteGuiaEnlace=1;
          $iCounterGuias = 0;
          $sSeparacion = '';
          foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
            $sSeparacion = ', ';
            $span_enlace_guias_tipo .= $rowEnlace->No_Tipo_Documento_Breve . $sSeparacion;
            $span_enlace_guias_serie .= $rowEnlace->_ID_Serie_Documento . $sSeparacion;
            $span_enlace_guias_numero .= $rowEnlace->ID_Numero_Documento . $sSeparacion;
            ++$iCounterGuias;
          }
          $span_enlace_guias_tipo = rtrim($span_enlace_guias_tipo, ', ');
          $span_enlace_guias_serie = rtrim($span_enlace_guias_serie, ', ');
          $span_enlace_guias_numero = rtrim($span_enlace_guias_numero, ', ');
        }

        $rows['No_Tipo_Documento_Breve_Guia'] = ($iExisteGuiaEnlace == 1 ? $span_enlace_guias_tipo : '' . $tipoGuiaxFacturaGarantia);
        $rows['ID_Serie_Documento_Guia'] = ($iExisteGuiaEnlace == 1 ? $span_enlace_guias_serie : $serieGuiaxFacturaGarantia);
        $rows['ID_Numero_Documento_Guia'] = ($iExisteGuiaEnlace == 1 ? $span_enlace_guias_numero : $numeroGuiaxFacturaGarantia);

        $rows['No_Lista_Precio'] = (!empty($row->No_Lista_Precio) ? $row->No_Lista_Precio : '');
        $arrCanalVenta = $this->HelperModel->obtenerCanalVentaArray($row->ID_Canal_Venta_Tabla_Dato);
        $rows['No_Canal_Venta'] = (!empty($arrCanalVenta['No_Canal_Venta']) ? $arrCanalVenta['No_Canal_Venta'] : '');

        $rows['No_Orden_Compra_FE'] = (!empty($row->No_Orden_Compra_FE) ? $row->No_Orden_Compra_FE : '');
        $rows['No_Placa_FE'] = (!empty($row->No_Placa_FE) ? $row->No_Placa_FE : '');

        $rows['Txt_Direccion_Entidad'] = (!empty($row->Txt_Direccion_Entidad) ? $row->Txt_Direccion_Entidad : '');
        $rows['No_Distrito'] = (!empty($row->No_Distrito) ? $row->No_Distrito : '');
        $rows['No_Provincia'] = (!empty($row->No_Provincia) ? $row->No_Provincia : '');
        $rows['No_Departamento'] = (!empty($row->No_Departamento) ? $row->No_Departamento : '');
        $rows['Nu_Celular_Entidad'] = (!empty($row->Nu_Celular_Entidad) ? $row->Nu_Celular_Entidad : '');
        $rows['Txt_Email_Entidad'] = (!empty($row->Txt_Email_Entidad) ? $row->Txt_Email_Entidad : '');

        //DESCUENTO TOTAL ITEM
        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Descuento_Producto'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Descuento_Producto : -$row->Ss_Descuento_Producto);
        else
          $rows['Ss_Descuento_Producto'] = $row->Ss_Descuento_Producto;
        
        //DESCUENTO TOTAL GLOBAL
        if ( $row->ID_Tipo_Documento != 2 )
          $rows['Ss_Descuento_Global'] = ($row->ID_Tipo_Documento != 5 ? $row->Ss_Descuento_Global : -$row->Ss_Descuento_Global);
        else
          $rows['Ss_Descuento_Global'] = $row->Ss_Descuento_Global;

        $rows['sAccionVer'] = '<button class="btn btn-xs btn-link" alt="Ver comprobante" title="Ver comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionVer . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-list-alt" aria-hidden="true"> Ver</i></button>';
        $rows['sAccionImprimir'] = '<button class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $row->ID_Documento_Cabecera . '\', \'' . $sVacio . '\')"><i class="fa fa-print" aria-hidden="true"> Imprimir</i></button>';
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

   public function ReporteVentasDetalladasLista(){
        
        echo json_encode(array("sStatus"=>"success","arrData"=>$this->VentasDetalladasGeneralesModel->getReporte_()));
  }

   public function CancelarReporte(){
        echo $this->VentasDetalladasGeneralesModel->CancelarReporte($this->input->post("ID_Reporte"));
    }


    public function BajarReporte($ID_Reporte){
      $row = $this->VentasDetalladasGeneralesModel->getReporteRow($ID_Reporte);
   
      if($row){
          header('Content-type: text/plain');
          header('Content-Disposition: attachment; filename="'.$row->Txt_Nombre_Archivo.'"');
          header('Cache-Control: no-cache, must-revalidate');
          header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
          readfile(FCPATH."17bfb21fcd36328bf87bf1636da09913/".$row->Txt_Archivo);
      }
    }
  
   public function CrearReporte(){
      echo $this->VentasDetalladasGeneralesModel->CrearReporte($this->input->post());

  }

  public function ReporteVentasDetalladasBG(){
    $this->FileReporte = "";
    $this->FileName    = ""; 

    if(!is_cli()) // solo se ejecuta en terminal
        exit();

    // $this->user->ID_Empresa = 272;
    // $fYear = "2022";
    $row = $this->VentasDetalladasGeneralesModel->getReporteBG();

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
    $this->VentasDetalladasGeneralesModel->UpdateReporteBG($arr,$row->ID_Reporte);

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
                        $Data["iTipoVenta"],
                        $Data["ID_Familia"],
                        $Data["ID_Sub_Familia"],
                        $Data["ID_Marca"],
                        $Data["Nu_Tipo_Recepcion"],
                        $Data["Nu_Estado_Despacho_Pos"],
                        $Data["ID_Transporte_Delivery"],
                        $Data["ID_Lista_Precio_Cabecera"],
                        $Data["ID_Canal_Venta_Tabla_Dato"],
                        $Data["ID_Almacen"],
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

      $this->VentasDetalladasGeneralesModel->UpdateReporteBG($arr,$row->ID_Reporte);

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
      'iTipoVenta' => $this->input->post('iTipoVenta'),
      'ID_Familia' => $this->input->post('ID_Familia'),
      'ID_Sub_Familia' => $this->input->post('ID_Sub_Familia'),
      'ID_Marca' => $this->input->post('ID_Marca'),
      'Nu_Tipo_Recepcion' => $this->input->post('Nu_Tipo_Recepcion'),
      'Nu_Estado_Despacho_Pos' => $this->input->post('Nu_Estado_Despacho_Pos'),
      'ID_Transporte_Delivery' => $this->input->post('ID_Transporte_Delivery'),
      'ID_Lista_Precio_Cabecera' => $this->input->post('ID_Lista_Precio_Cabecera'),
      'ID_Canal_Venta_Tabla_Dato' => $this->input->post('ID_Canal_Venta_Tabla_Dato'),
      'Nu_Tipo_Impuesto' => $this->input->post('Nu_Tipo_Impuesto'),
      'ID_Almacen' => $this->input->post('ID_Almacen'),
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoVenta, $ID_Familia, $ID_Sub_Familia, $ID_Marca, $Nu_Tipo_Recepcion, $Nu_Estado_Despacho_Pos, $ID_Transporte_Delivery, $ID_Lista_Precio_Cabecera, $ID_Canal_Venta_Tabla_Dato, $ID_Almacen, $Nu_Tipo_Impuesto){
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
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $ID_Familia = $this->security->xss_clean($ID_Familia);
    $ID_Sub_Familia = $this->security->xss_clean($ID_Sub_Familia);
    $ID_Marca = $this->security->xss_clean($ID_Marca);
    $Nu_Tipo_Recepcion = $this->security->xss_clean($Nu_Tipo_Recepcion);
    $Nu_Estado_Despacho_Pos = $this->security->xss_clean($Nu_Estado_Despacho_Pos);
    $ID_Transporte_Delivery = $this->security->xss_clean($ID_Transporte_Delivery);
    $ID_Lista_Precio_Cabecera = $this->security->xss_clean($ID_Lista_Precio_Cabecera);
    $ID_Canal_Venta_Tabla_Dato = $this->security->xss_clean($ID_Canal_Venta_Tabla_Dato);
    $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);

    $fileNamePDF = "ventas_detalladas_generales" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

    $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
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
      'iTipoVenta' => $iTipoVenta,
      'ID_Familia' => $ID_Familia,
      'ID_Sub_Familia' => $ID_Sub_Familia,
      'ID_Marca' => $ID_Marca,
      'Nu_Tipo_Recepcion' => $Nu_Tipo_Recepcion,
      'Nu_Estado_Despacho_Pos' => $Nu_Estado_Despacho_Pos,
      'ID_Transporte_Delivery' => $ID_Transporte_Delivery,
      'ID_Lista_Precio_Cabecera' => $ID_Lista_Precio_Cabecera,
      'ID_Canal_Venta_Tabla_Dato' => $ID_Canal_Venta_Tabla_Dato,
      'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto,
      'ID_Almacen' => $ID_Almacen,
    );

    ob_start();
    $file = $this->load->view('Ventas/informes_venta/pdf/VentasDetalladasGeneralesViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('laesystems');
    $pdf->SetTitle('laesystems - Ventas Detalladas Generales');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 5);
    
		$pdf->AddPage('L', ['format' => 'A4', 'Rotate' => 90]);
		$pdf->writeHTML($html, true, false, true, false, '');
		
    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Serie_Documento, $ID_Numero_Documento, $Nu_Estado_Documento, $iIdCliente, $sNombreCliente, $iIdItem, $sNombreItem, $iTipoVenta, $ID_Familia, $ID_Sub_Familia, $ID_Marca, $Nu_Tipo_Recepcion, $Nu_Estado_Despacho_Pos, $ID_Transporte_Delivery, $ID_Lista_Precio_Cabecera, $ID_Canal_Venta_Tabla_Dato, $ID_Almacen, $Nu_Tipo_Impuesto){
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
    $iTipoVenta = $this->security->xss_clean($iTipoVenta);
    $ID_Familia = $this->security->xss_clean($ID_Familia);
    $ID_Sub_Familia = $this->security->xss_clean($ID_Sub_Familia);
    $ID_Marca = $this->security->xss_clean($ID_Marca);
    $Nu_Tipo_Recepcion = $this->security->xss_clean($Nu_Tipo_Recepcion);
    $Nu_Estado_Despacho_Pos = $this->security->xss_clean($Nu_Estado_Despacho_Pos);
    $ID_Transporte_Delivery = $this->security->xss_clean($ID_Transporte_Delivery);
    $ID_Lista_Precio_Cabecera = $this->security->xss_clean($ID_Lista_Precio_Cabecera);
    $ID_Canal_Venta_Tabla_Dato = $this->security->xss_clean($ID_Canal_Venta_Tabla_Dato);
    $Nu_Tipo_Impuesto = $this->security->xss_clean($Nu_Tipo_Impuesto);
    $ID_Almacen = $this->security->xss_clean($ID_Almacen);
    
		$this->FileName = $fileNameExcel = "ventas_detalladas_generales_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Ventas Detalladas Generales');
      
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
    ->setCellValue('C2', 'Informe de Ventas Detalladas Generales')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:AF2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:AF3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");//FECHA
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("12");//HORA
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("30");//CAJERO / PERSONAL
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");//TIPO DOC
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("10");//SERIE
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("10");//NÚMERO
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");//CLIENTE NRO. DOC IDENTI
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("50");//CLIENTE RAZON S
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("12");
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("30");//CATEGORIA
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("30");//SUB CATEGORIA
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("20");//U.M.
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("20");//ITEM UPC / CODIGO DE BARRA
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("50");//ITEM NOMBRE
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("20");//ITEM NOTA
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth("30");
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth("30");//Total
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth("15");//Recepión Tipo
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth("40");//Recepión Transporte
    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth("15");//Recepión F. Entrega
    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth("15");//Recepión Estado Pedido
    $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth("15");//Guía Tipo
    $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth("10");//Guía Serie
    $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth("10");//Guía Número
    $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth("50");//NOTA GLOBAL
    $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth("30");//L. Precio
    $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth("30");//Canal de venta
    $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth("20");//O/C
    $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth("20");//Placa
    $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth("40");//Cliente Direccion
    $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth("20");//Cliente Distrito
    $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth("20");//Cliente Provincia
    $objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setWidth("20");//Cliente Departamento
    $objPHPExcel->getActiveSheet()->getColumnDimension('AO')->setWidth("12");//Cliente Celular
    $objPHPExcel->getActiveSheet()->getColumnDimension('AP')->setWidth("30");//Cliente Correo
    $objPHPExcel->getActiveSheet()->getColumnDimension('AQ')->setWidth("30");//Descuento ITEM
    $objPHPExcel->getActiveSheet()->getColumnDimension('AR')->setWidth("30");//Descuento GLOBAL
    $objPHPExcel->getActiveSheet()->getColumnDimension('AS')->setWidth("20");//ESTADO

    $objPHPExcel->getActiveSheet()->getStyle('A5:AS5')->applyFromArray($BStyle_top);
    
    $objPHPExcel->getActiveSheet()->getStyle('D5:F5')->applyFromArray($BStyle_bottom);
    $objPHPExcel->getActiveSheet()->getStyle('G5:X5')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('Y5:AB5')->applyFromArray($BStyle_bottom);//Recepción
    $objPHPExcel->getActiveSheet()->getStyle('AC5:AE5')->applyFromArray($BStyle_bottom);//Guía

    $objPHPExcel->getActiveSheet()->getStyle('AK5:AO5')->applyFromArray($BStyle_bottom);//Guía

    $objPHPExcel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($BStyle_bottom);
    
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('T5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('U5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('V5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('W5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('X5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Y5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Z5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AA5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AB5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AC5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AD5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AE5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AF5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AG5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AH5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AI5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AJ5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AK5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AL5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AM5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AN5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AO5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AP5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AQ5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AR5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AS5')->applyFromArray($BStyle_right);

    $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('G6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('K6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('L6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('M6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('N6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('O6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('P6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Q6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('R6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('S6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('T6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('U6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('V6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('W6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('X6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Y6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('Z6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AA6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AB6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AC6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AD6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AE6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AF6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AG6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AH6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AI6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AJ6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AK6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AL6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AM6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AN6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AO6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AP6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AQ6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AR6')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('AS6')->applyFromArray($BStyle_right); 
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:AS5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6:AS6')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Fecha')
    ->setCellValue('B5', 'Hora')
    ->setCellValue('C5', 'Personal / Cajero');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('D5', 'Documento');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D5:F5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('G5', 'Cliente');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G5:I5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('J5', 'Moneda');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('J5:K5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('L5', 'Producto');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L5:X5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('Y5', 'Recepción');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Y5:AB5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AC5', 'Guía');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('AC5:AE5');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AF5', 'Nota');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AG5', 'Lista');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AH5', 'Canal');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AI5', 'O/C');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AJ5', 'Placa');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AK5', 'Cliente');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('AK5:AP5');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AQ5', 'Descuento ITEM');
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AR5', 'Descuento TOTAL');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('AS5', 'Estado');

    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A6', 'Emisión')
    ->setCellValue('B6', 'Emisión')
    ->setCellValue('D6', 'Tipo')
    ->setCellValue('E6', 'Serie')
    ->setCellValue('F6', 'Número')
    ->setCellValue('G6', 'Tipo')
    ->setCellValue('H6', '# Documento')
    ->setCellValue('I6', 'Nombre')
    ->setCellValue('J6', 'Tipo')
    ->setCellValue('K6', 'T.C.')
    ->setCellValue('L6', 'Marca')
    ->setCellValue('M6', 'Categoría')
    ->setCellValue('N6', 'Sub Categoría')
    ->setCellValue('O6', 'Unidad Medida')
    ->setCellValue('P6', 'Código de Barra')
    ->setCellValue('Q6', 'Nombre')
    ->setCellValue('R6', 'Nota')
    ->setCellValue('S6', 'Cantidad')
    ->setCellValue('T6', 'CO2')
    ->setCellValue('U6', 'Precio')
    ->setCellValue('V6', 'SubTotal')
    ->setCellValue('W6', 'Impuesto')
    ->setCellValue('X6', 'Total')
    ->setCellValue('Y6', 'Tipo')
    ->setCellValue('Z6', 'Transporte')
    ->setCellValue('AA6', 'F. Entrega')
    ->setCellValue('AB6', 'Estado Pedido')
    ->setCellValue('AC6', 'Tipo')
    ->setCellValue('AD6', 'Serie')
    ->setCellValue('AE6', 'Número')
    ->setCellValue('AF6', 'Global')
    ->setCellValue('AG6', 'Precio')
    ->setCellValue('AH6', 'Venta')
    ->setCellValue('AI6', '')
    ->setCellValue('AJ6', '')
    ->setCellValue('AK6', 'Dirección')
    ->setCellValue('AL6', 'Distrito')
    ->setCellValue('AM6', 'Provincia')
    ->setCellValue('AN6', 'Departamento')
    ->setCellValue('AO6', 'Celular')
    ->setCellValue('AP6', 'Correo')
    ->setCellValue('AQ6', 'Documento')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:AS5')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('A6:AS6')->applyFromArray($style_align_center);
    
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
      'iTipoVenta' => $iTipoVenta,
      'ID_Familia' => $ID_Familia,
      'ID_Sub_Familia' => $ID_Sub_Familia,
      'ID_Marca' => $ID_Marca,
      'Nu_Tipo_Recepcion' => $Nu_Tipo_Recepcion,
      'Nu_Estado_Despacho_Pos' => $Nu_Estado_Despacho_Pos,
      'ID_Transporte_Delivery' => $ID_Transporte_Delivery,
      'ID_Lista_Precio_Cabecera' => $ID_Lista_Precio_Cabecera,
      'ID_Canal_Venta_Tabla_Dato' => $ID_Canal_Venta_Tabla_Dato,
      'Nu_Tipo_Impuesto' => $Nu_Tipo_Impuesto,
      'ID_Almacen' => $ID_Almacen,
    );
    $arrData = $this->getReporte($arrParams);
        
    if ( $arrData['sStatus'] == 'success' ) {
      $fCantidadItem = 0.00; $fPrecioItem = 0.00; $fSubtotalItem = 0.00; $fImpuestoItem = 0.00; $fTotalItem = 0.00; $fTotalDescuentoItem = 0.00; $fTotalDescuento = 0.00;
      $fCantidadTotalGeneral = 0.00; $fSubtotalGeneral = 0.00; $fImpuestoGeneral = 0.00; $fTotalGeneral = 0.00; $fTotalDescuentoItemGeneral = 0.00; $fTotalDescuentoGeneral = 0.00;
      $ID_Almacen = 0; $counter_almacen=0; $fCantidadTotalGeneralAlmacen = 0.00; $fSubtotalGeneralAlmacen = 0.00; $fImpuestoGeneralAlmacen = 0.00; $fTotalGeneralAlmacen = 0.00; $fTotalDescuentoItemAlmacen = 0.00; $fTotalDescuentoAlmacen = 0.00;
      foreach ($arrData['arrData'] as $row) {
        if ($ID_Almacen != $row->ID_Almacen) {
            if ($counter_almacen != 0) {
              $objPHPExcel->setActiveSheetIndex($hoja_activa)
              ->setCellValue('R' . $fila, 'Total Almacén')
              ->setCellValue('S' . $fila, numberFormat($fCantidadTotalGeneralAlmacen, 6, '.', ','))
              ->setCellValue('V' . $fila, numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','))
              ->setCellValue('W' . $fila, numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','))
              ->setCellValue('X' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','))
              ->setCellValue('AQ' . $fila, numberFormat($fTotalDescuentoItemAlmacen, 2, '.', ','))
              ->setCellValue('AR' . $fila, numberFormat($fTotalDescuentoAlmacen, 2, '.', ','));
              
              $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'AR' . $fila)->applyFromArray($style_align_right);
                          
              $objPHPExcel->getActiveSheet()
              ->getStyle('A' . $fila . ':' . 'AR' . $fila)
              ->applyFromArray(
                array(
                  'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E7E7E7')
                  )
                )
              );
              $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'AR' . $fila)->getFont()->setBold(true);
            
              $fila++;
              
              $fCantidadTotalGeneralAlmacen = 0.00;
              $fSubtotalGeneralAlmacen = 0.00;
              $fImpuestoGeneralAlmacen = 0.00;
              $fTotalGeneralAlmacen = 0.00;
              $fTotalDescuentoItemAlmacen = 0.00;
              $fTotalDescuentoAlmacen = 0.00;
            }

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('A' . $fila, 'Almacén')
            ->setCellValue('B' . $fila, $row->No_Almacen);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B'. $fila . ':X'. $fila);
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($style_align_left);
            
            $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $fila . ':' . 'AR' . $fila)
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'F2F5F5')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'AR' . $fila)->getFont()->setBold(true);
            
            $ID_Almacen = $row->ID_Almacen;
            $fila++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'B' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'E' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('P' . $fila . ':' . 'Q' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('S' . $fila . ':' . 'X' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('Y' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('Z' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('AA' . $fila . ':' . 'AJ' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('AK' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('AL' . $fila . ':' . 'AP' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('AQ' . $fila . ':' . 'AR' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('AS' . $fila)->applyFromArray($style_align_center);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->Fe_Emision_Hora)
        ->setCellValue('B' . $fila, $row->Fe_Hora)
        ->setCellValue('C' . $fila, $row->No_Empleado)
        ->setCellValue('D' . $fila, $row->No_Tipo_Documento_Breve)
        ->setCellValue('E' . $fila, $row->ID_Serie_Documento)
        ->setCellValue('F' . $fila, $row->ID_Numero_Documento)
        ->setCellValue('G' . $fila, $row->No_Tipo_Documento_Identidad_Breve)
        ->setCellValue('H' . $fila, $row->Nu_Documento_Identidad)
        ->setCellValue('I' . $fila, $row->No_Entidad)
        ->setCellValue('J' . $fila, $row->No_Signo)
        ->setCellValue('K' . $fila, numberFormat($row->Ss_Tipo_Cambio, 3, '.', ','))
        ->setCellValue('L' . $fila, $row->No_Marca)
        ->setCellValue('M' . $fila, $row->No_Familia)
        ->setCellValue('N' . $fila, $row->No_Sub_Familia)
        ->setCellValue('O' . $fila, $row->No_Unidad_Medida)
        ->setCellValue('P' . $fila, $row->Nu_Codigo_Barra)
        ->setCellValue('Q' . $fila, $row->No_Producto)
        ->setCellValue('R' . $fila, $row->Txt_Nota_Item)
        ->setCellValue('S' . $fila, numberFormat($row->Qt_Producto, 6, '.', ','))
        ->setCellValue('T' . $fila, $row->Qt_CO2_Producto)
        ->setCellValue('U' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
        ->setCellValue('V' . $fila, numberFormat($row->Ss_Subtotal, 2, '.', ','))
        ->setCellValue('W' . $fila, numberFormat($row->Ss_Impuesto, 2, '.', ','))
        ->setCellValue('X' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
        ->setCellValue('Y' . $fila, $row->No_Tipo_Recepcion)
        ->setCellValue('Z' . $fila, $row->No_Delivery)
        ->setCellValue('AA' . $fila, $row->Fe_Entrega)
        ->setCellValue('AB' . $fila, $row->No_Estado_Delivery)
        ->setCellValue('AC' . $fila, $row->No_Tipo_Documento_Breve_Guia)
        ->setCellValue('AD' . $fila, $row->ID_Serie_Documento_Guia)
        ->setCellValue('AE' . $fila, $row->ID_Numero_Documento_Guia)
        ->setCellValue('AF' . $fila, $row->Txt_Nota)
        ->setCellValue('AG' . $fila, $row->No_Lista_Precio)
        ->setCellValue('AH' . $fila, $row->No_Canal_Venta)
        ->setCellValue('AI' . $fila, $row->No_Orden_Compra_FE)
        ->setCellValue('AJ' . $fila, $row->No_Placa_FE)
        ->setCellValue('AK' . $fila, $row->Txt_Direccion_Entidad)
        ->setCellValue('AL' . $fila, $row->No_Distrito)
        ->setCellValue('AM' . $fila, $row->No_Provincia)
        ->setCellValue('AN' . $fila, $row->No_Departamento)
        ->setCellValue('AO' . $fila, $row->Nu_Celular_Entidad)
        ->setCellValue('AP' . $fila, $row->Txt_Email_Entidad)
        ->setCellValue('AQ' . $fila, numberFormat($row->Ss_Descuento_Producto, 2, '.', ','))
        ->setCellValue('AR' . $fila, numberFormat($row->Ss_Descuento_Global, 2, '.', ','))
        ->setCellValue('AS' . $fila, $row->No_Estado)
        ;
        $fila++;
        
        $fCantidadTotalGeneral += $row->Qt_Producto;
        $fSubtotalGeneral += $row->Ss_Subtotal;
        $fImpuestoGeneral += $row->Ss_Impuesto;
        $fTotalGeneral += $row->Ss_Total;
        $fTotalDescuentoItemGeneral += $row->Ss_Descuento_Producto;
        $fTotalDescuentoGeneral += $row->Ss_Descuento_Global;
        
        $fCantidadTotalGeneralAlmacen += $row->Qt_Producto;
        $fSubtotalGeneralAlmacen += $row->Ss_Subtotal;
        $fImpuestoGeneralAlmacen += $row->Ss_Impuesto;
        $fTotalGeneralAlmacen += $row->Ss_Total;
        $fTotalDescuentoItemAlmacen += $row->Ss_Descuento_Producto;
        $fTotalDescuentoAlmacen += $row->Ss_Descuento_Global;

        $counter_almacen++;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('R' . $fila, 'Total Almacén')
      ->setCellValue('S' . $fila, numberFormat($fCantidadTotalGeneralAlmacen, 6, '.', ','))
      ->setCellValue('V' . $fila, numberFormat($fSubtotalGeneralAlmacen, 2, '.', ','))
      ->setCellValue('W' . $fila, numberFormat($fImpuestoGeneralAlmacen, 2, '.', ','))
      ->setCellValue('X' . $fila, numberFormat($fTotalGeneralAlmacen, 2, '.', ','))
      ->setCellValue('AQ' . $fila, numberFormat($fTotalDescuentoItemAlmacen, 2, '.', ','))
      ->setCellValue('AR' . $fila, numberFormat($fTotalDescuentoAlmacen, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'AR' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'AR' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':' . 'AR' . $fila)->getFont()->setBold(true);

      $fila++;      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('R' . $fila, 'Total')
      ->setCellValue('S' . $fila, numberFormat($fCantidadTotalGeneral, 6, '.', ','))
      ->setCellValue('V' . $fila, numberFormat($fSubtotalGeneral, 2, '.', ','))
      ->setCellValue('W' . $fila, numberFormat($fImpuestoGeneral, 2, '.', ','))
      ->setCellValue('X' . $fila, numberFormat($fTotalGeneral, 2, '.', ','))
      ->setCellValue('AQ' . $fila, numberFormat($fTotalDescuentoItemGeneral, 2, '.', ','))
      ->setCellValue('AR' . $fila, numberFormat($fTotalDescuentoGeneral, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('R' . $fila . ':' . 'AR' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'AR' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('R' . $fila . ':' . 'AR' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':AS' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

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
