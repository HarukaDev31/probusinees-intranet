<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class PedidoProveedoDropshippingController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Proveedores/PedidoProveedoDropshippingModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  $arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			$this->load->view('Proveedores/PedidoProveedoDropshippingView');
			$this->load->view('footer', array("js_pedido_proveedor" => true));
		}
	}
	
	public function verPedido($iIdPedido){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PedidoProveedoDropshippingModel->verPedido($this->security->xss_clean($iIdPedido)));
  }
  
  private function getReporte($arrParams){
    $arrResponseModal = $this->PedidoProveedoDropshippingModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
            
      $sRelacionData='';
      $sAccionImprimir='imprimir';
      $sVacio='mostrar-img-logo_punto_venta';
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        
        $sTipoVenta = '';
        if($row->Nu_Tipo_Venta_Generada== 1){
          $sTipoVenta = 'Tienda Virtual';
        } else if($row->Nu_Tipo_Venta_Generada==2){
          $sTipoVenta = 'Manual';
        }
        
        $rows['No_Empresa'] = $row->No_Empresa;
        $rows['sTipoVenta'] = $sTipoVenta;
        $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
        $rows['ID_Pedido_Cabecera'] = $row->ID_Pedido_Cabecera;
        $rows['No_Entidad'] = ($row->Nu_Estado!=6 ? $row->No_Entidad : '-');
        $rows['Ss_Total'] = ($row->Nu_Estado!=5 ? '' : $row->Ss_Total);

        //$rows['No_Estado_Recepcion'] = $row->No_Estado_Recepcion;
        //$btn_delivery_nota = ($row->Nu_Estado != 5 && $row->Nu_Estado != 6 ? '<button class="btn btn-xs btn-link" alt="Precio de delivery" title="Precio de delivery" href="javascript:void(0)" onclick="agregarPrecioDeliveryPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-money"></i> Precio Delivery</button>' : 'S/ ' . $row->Ss_Precio_Delivery);
        $btn_delivery_nota = ($row->Ss_Precio_Delivery > 0.00 ? 'S/ ' . $row->Ss_Precio_Delivery : '<button class="btn btn-xs btn-link" alt="Precio de delivery" title="Precio de delivery" href="javascript:void(0)" onclick="agregarPrecioDeliveryPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-money"></i> Precio Delivery</button>');
        $btn_delivery_nota .= ($row->Nu_Estado == 6 ? '<br><button class="btn btn-xs btn-link" alt="Agregar Nota" title="Agregar Nota" href="javascript:void(0)" onclick="agregarNotaPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-comment"></i> Agregar Nota</button>' : '');
        //SI YA ASIGNO DELIVERY NO MOSTRAR
        $btn_delivery_nota .= '<br>' . (!empty($row->ID_Usuario_Asignar_Delivery) ? $row->No_Delivery . ' <button class="btn btn-xs btn-link" alt="Cambiar Delivery" title="Cambiar Delivery" href="javascript:void(0)" onclick="asignarDelivery(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-motorcycle"></i> Cambiar Delivery</button>' : '<button class="btn btn-xs btn-link" alt="Asignar Delivery" title="Asignar Delivery" href="javascript:void(0)" onclick="asignarDelivery(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-motorcycle"></i> Asignar Delivery</button>');
        $rows['Ss_Precio_Delivery'] = $btn_delivery_nota;
        $rows['Ss_Precio_Delivery_Excel'] = $row->Ss_Precio_Delivery;
        $rows['Txt_Glosa'] = $row->Txt_Glosa;
        $rows['Ss_Precio_Delivery_Propio_Personal'] = $row->Ss_Precio_Delivery_Propio_Personal;
        
        $sFormaPagoDropshipping = 'Sin definir';
        if($row->Nu_Forma_Pago_Dropshipping == 1){
          $sFormaPagoDropshipping = 'Contra Entrega';
        } else if($row->Nu_Forma_Pago_Dropshipping==2){
          $sFormaPagoDropshipping = 'Dropshipping';
        }
  /*      
        $sServicioTransportadora = 'Sin definir';
        if($row->Nu_Servicio_Transportadora_Dropshipping == 1){
          $sServicioTransportadora = 'Call Center';
        } else if($row->Nu_Servicio_Transportadora_Dropshipping==2){
          $sServicioTransportadora = 'Coordinado';
        }
*/
        $rows['Fe_Entrega'] = ($row->Nu_Forma_Pago_Dropshipping == 0 ? '' : ToDateBD($row->Fe_Entrega));
        $rows['Nu_Forma_Pago_Dropshipping'] = $sFormaPagoDropshipping;
        $rows['Estado_Nu_Forma_Pago_Dropshipping'] = $row->Nu_Forma_Pago_Dropshipping;
        //$rows['Nu_Servicio_Transportadora_Dropshipping'] = $sServicioTransportadora;

        /*
        $sEstadoPedidoEmpresa = '';
        $sClassEstadoPedidoEmpresa = '';
        if($row->Nu_Estado_Pedido_Empresa==0){
          $sEstadoPedidoEmpresa = 'Pendiente';
          $sClassEstadoPedidoEmpresa = 'warning';
        } else if($row->Nu_Estado_Pedido_Empresa==1){
          $sEstadoPedidoEmpresa = 'Completado';
          $sClassEstadoPedidoEmpresa = 'success';
        } else if($row->Nu_Estado_Pedido_Empresa==2){
          $sEstadoPedidoEmpresa = 'Pago Realizado';
          $sClassEstadoPedidoEmpresa = 'primary';
        } else if($row->Nu_Estado_Pedido_Empresa==3){
          $sEstadoPedidoEmpresa = 'Falsa Parada';
          $sClassEstadoPedidoEmpresa = 'info';
        }
        $rows['sEstadoPedidoEmpresa'] = $sEstadoPedidoEmpresa;
        */

        //$rows['sEstadoPedidoEmpresaExcel'] = $sEstadoPedidoEmpresa;
        
			  $arrEstadoPedidoTienda = $this->HelperModel->obtenerEstadoOrdenPedidoTienda($row->Nu_Estado);
        
        if($row->ID_Pais==1){//1=PERU
          //5=entregado y 6 = rechazado
          //cuando es rechazado no se muestra cliente
          if($row->Nu_Estado==5 || $row->Nu_Estado==6){
            $dropdown = '<span class="label label-' . $arrEstadoPedidoTienda['No_Class_Estado'] . '">' . $arrEstadoPedidoTienda['No_Estado'] . '</span>';
          } else {
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Guía Generada" title="Guía Generada" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8);">Guía Generada</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Preparando</a></li>
              </ul>
            </div>';
          }
        } else if($row->ID_Pais==2){//2=MEXICO
          if($row->Nu_Estado==2 || $row->Nu_Estado==8 || $row->Nu_Estado==3){
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Guía Generada" title="Guía Generada" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8);">Guía Generada</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Preparando</a></li>
              </ul>
            </div>';
          } else {
            $dropdown = '<span class="label label-' . $arrEstadoPedidoTienda['No_Class_Estado'] . '">' . $arrEstadoPedidoTienda['No_Estado'] . '</span>';
          }
        }
        $rows['No_Estado_Pedido'] = $dropdown;
        $rows['No_Estado_Pedido_Cliente_Excel'] = $arrEstadoPedidoTienda['No_Estado'];

        $rows['sAccionVer'] = ($row->Nu_Tipo_Venta_Generada == 1 ? '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-list-alt" aria-hidden="true"></i></button>' : '');
        $rows['sAccionEditar'] = '<button class="btn btn-xs btn-link" alt="Completar pedido" title="Completar pedido" href="javascript:void(0)" onclick="editarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-edit" aria-hidden="true"></i></button>';
                
        $sAccionFacturar = '<button class="btn btn-xs btn-link" alt="Generar Venta" title="Generar Venta" href="javascript:void(0)" onclick="generarVenta(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-shopping-cart" aria-hidden="true"></i></button>';
				$arrParams = array('ID_Pedido_Cabecera' => $row->ID_Pedido_Cabecera);
				$arrResponseDocument = $this->PedidoProveedoDropshippingModel->getRelacionPedidoVenta($arrParams);
				$sRelacionData = '';
				if ($arrResponseDocument['sStatus'] == 'success') {
          $sAccionFacturar = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
            $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($rowEnlace->Nu_Estado);
					  $sRelacionData .= '<span class="label label-' . $arrEstadoDocumento['No_Class_Estado'] . '">' . $rowEnlace->No_Tipo_Documento_Breve . ' - ' . $rowEnlace->ID_Serie_Documento . ' - '. $rowEnlace->ID_Numero_Documento . " (" . $arrEstadoDocumento['No_Estado'] . ")</span>";
            $sRelacionData .= '<br><button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $rowEnlace->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';
            
            if ($rowEnlace->Nu_Estado==6 || $rowEnlace->Nu_Estado==8) {
              $sRelacionData .= '<br><button type="button" id="whatsapp-' . $rowEnlace->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $rowEnlace->ID_Entidad . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
              $sRelacionData .= '<br><button type="button" class="btn btn-xs btn-link" alt="Correo" title="Correo" href="javascript:void(0)" onclick="sendCorreoFacturaVentaSUNAT(\'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $rowEnlace->ID_Entidad . '\')"><i class="fa fa-fw fa-envelope fa-2x" style="color: #2d2d2d;"></i></button>';
            }
          }
				}
        $rows['sAccionFacturar'] = $sAccionFacturar . $sRelacionData;
            
        $fPrecioTotalPedidoProveedor = $this->PedidoProveedoDropshippingModel->getPrecioPedidoDetalleProveedor($row->ID_Pedido_Cabecera);
			  $rows['fPrecioTotalPedidoProveedor'] = $fPrecioTotalPedidoProveedor;

        $fPrecioTotalPedioEcxlae = ($row->Nu_Estado!=5 ? '' : $this->PedidoProveedoDropshippingModel->getPrecioPedidoDetalleEcxlae($row->ID_Pedido_Cabecera, $row->ID_Pais));
			  $rows['fPrecioTotalPedioEcxlae'] = $fPrecioTotalPedioEcxlae;

        $fTotalGanancia = ($row->Nu_Estado!=5 ? '' : ($fPrecioTotalPedidoProveedor - $fPrecioTotalPedioEcxlae));
			  $rows['fTotalGanancia'] = $fTotalGanancia;

        //$rows['sAccionEliminar'] = ($row->Nu_Estado!=7 ? '<button class="btn btn-xs btn-link" alt="Eliminar pedido" title="Eliminar pedido" href="javascript:void(0)" onclick="eliminarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>' : '');

        $btn_generar_guia_99 = 'Pendiente...';

        //if ( !empty($row->Txt_Response_TrackingId_Api) ) {  
          if ($row->Nu_Tipo_Guia_Api==1) {//1=99 minutos
            $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia 99" title="PDF Guia 99" href="javascript:void(0)" onclick="pdfGuia99(\'' . $row->Txt_Response_TrackingId_Api . '\',\'' . $row->ID_Pedido_Cabecera . '\',\'' . $row->Nu_Imprimir_Guia_Mexico . '\',\'' . $row->Nu_Estado . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';
            $btn_generar_guia_99 = '99 Minutos: ' . $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
          } else if ($row->Nu_Tipo_Guia_Api==2) {//2=Quiken
            $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia Quiken" title="PDF Guia Quiken" href="javascript:void(0)" onclick="pdfQuiken(\'' . $row->Txt_Response_Guia_Api . '\',\'' . $row->ID_Pedido_Cabecera . '\',\'' . $row->Nu_Imprimir_Guia_Mexico . '\',\'' . $row->Nu_Estado . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';
            //$btn_generar_guia_99 = '<a class="btn btn-xs btn-link" alt="PDF Guia Quiken" title="PDF Guia Quiken" target="_blank" rel="noopener noreferrer" href="' . $row->Txt_Response_Guia_Api . '"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</a>';
            $btn_generar_guia_99 = 'Quiken: ' . $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
          } else if ($row->Nu_Tipo_Guia_Api==3) {//3=Ecxlae
            $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia Ecxlae" title="PDF Guia Ecxlae" href="javascript:void(0)" onclick="pdfGuiaEcxlae(\'' . $row->Txt_Response_Guia_Api . '\',\'' . $row->ID_Pedido_Cabecera . '\',\'' . $row->Nu_Imprimir_Guia_Mexico . '\',\'' . $row->Nu_Estado . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';  
            $btn_generar_guia_99 = 'Ecxlae: ' . $row->Txt_Response_Guia_Api . ' ' . $btn_generar_guia_99;
          }
        //}

        $rows['btn_generar_guia_99'] = $btn_generar_guia_99;
        
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
  
  private function getReporteExcel($arrParams){
    $arrResponseModal = $this->PedidoProveedoDropshippingModel->getReporteExcel($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
            
      $sRelacionData='';
      $sAccionImprimir='imprimir';
      $sVacio='mostrar-img-logo_punto_venta';
      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        
        $sEstadoPedidoEmpresa = '';
        if($row->Nu_Estado_Pedido_Empresa==0){
          $sEstadoPedidoEmpresa = 'Pendiente';
        } else if($row->Nu_Estado_Pedido_Empresa==1){
          $sEstadoPedidoEmpresa = 'Completado';
        } else if($row->Nu_Estado_Pedido_Empresa==2){
          $sEstadoPedidoEmpresa = 'Pago Realizado';
        }

        $rows['No_Empresa'] = $row->No_Empresa;
        $rows['Nu_Estado_Pedido_Empresa'] = $sEstadoPedidoEmpresa;
        $rows['Fe_Entrega'] = ToDateBD($row->Fe_Entrega);
        $rows['No_Entidad'] = $row->No_Entidad;
        $rows['Nu_Celular'] = $row->Nu_Celular;
        $rows['No_Producto'] = $row->No_Producto;
        $rows['Qt_Producto'] = $row->Qt_Producto;
        $rows['Ss_Precio'] = $row->Ss_Precio;
        $rows['No_Ciudad_Dropshipping'] = $row->No_Ciudad_Dropshipping;
        $rows['Ss_Precio_Delivery'] = $row->Ss_Precio_Delivery;
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
  
	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidoProveedoDropshippingModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function eliminarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidoProveedoDropshippingModel->eliminarPedido($this->security->xss_clean($ID)));
	}
  
	public function generarVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidoProveedoDropshippingModel->generarVenta($this->input->post()));
	}

	public function sendReporte(){
    $arrParams = array(
      'Fe_Inicio' => $this->input->post('Fe_Inicio'),
      'Fe_Fin' => $this->input->post('Fe_Fin'),
      'ID_Tipo_Documento' => $this->input->post('ID_Tipo_Documento'),
      'ID_Pedido_Cabecera' => $this->input->post('ID_Pedido_Cabecera'),
      'Nu_Estado_Pedido' => $this->input->post('Nu_Estado_Pedido'),
      'iIdCliente' => $this->input->post('iIdCliente'),
      'sNombreCliente' => $this->input->post('sNombreCliente'),
      'ID_Filtro_Empresa' => $this->input->post('ID_Filtro_Empresa'),
      'Nu_Estado_Pedido_Empresa' => $this->input->post('Nu_Estado_Pedido_Empresa')
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $ID_Filtro_Empresa, $Nu_Estado_Pedido_Empresa){
    $this->load->library('FormatoLibroSunatPDF');

    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Pedido_Cabecera = $this->security->xss_clean($ID_Pedido_Cabecera);
    $Nu_Estado_Pedido = $this->security->xss_clean($Nu_Estado_Pedido);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $ID_Filtro_Empresa = $this->security->xss_clean($ID_Filtro_Empresa);
    $Nu_Estado_Pedido_Empresa = $this->security->xss_clean($Nu_Estado_Pedido_Empresa);

    $fileNamePDF = "Reporte_CallCenter_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

    $pdf = new FormatoLibroSunatPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arrCabecera = array (
      "Fe_Inicio" => ToDateBD($Fe_Inicio),
      "Fe_Fin" => ToDateBD($Fe_Fin),
    );

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
      'Nu_Estado_Pedido' => $Nu_Estado_Pedido,
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'ID_Filtro_Empresa' => $ID_Filtro_Empresa,
      'Nu_Estado_Pedido_Empresa' => $Nu_Estado_Pedido_Empresa
    );

    ob_start();
    $file = $this->load->view('pdf/PedidosMarketplaceViewPDF', array(
      'arrCabecera' => $arrCabecera,
      'arrDetalle' => $this->getReporte($arrParams),
    ));
    $html = ob_get_contents();
    ob_end_clean();

    $pdf->SetAuthor('EcxpressLae');
    $pdf->SetTitle('TiendaVirtual Detalladas Generales');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->setFont('helvetica', '', 7);
    
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($fileNamePDF, 'I');
	}
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $ID_Filtro_Empresa, $Nu_Estado_Pedido_Empresa){
    $this->load->library('Excel');
	  
    $Fe_Inicio = $this->security->xss_clean($Fe_Inicio);
    $Fe_Fin = $this->security->xss_clean($Fe_Fin);
    $ID_Tipo_Documento = $this->security->xss_clean($ID_Tipo_Documento);
    $ID_Pedido_Cabecera = $this->security->xss_clean($ID_Pedido_Cabecera);
    $Nu_Estado_Pedido = $this->security->xss_clean($Nu_Estado_Pedido);
    $iIdCliente = $this->security->xss_clean($iIdCliente);
    $sNombreCliente = $this->security->xss_clean($sNombreCliente);
    $ID_Filtro_Empresa = $this->security->xss_clean($ID_Filtro_Empresa);
    $Nu_Estado_Pedido_Empresa = $this->security->xss_clean($Nu_Estado_Pedido_Empresa);
    
		$fileNameExcel = "Reporte_CallCenter_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Pedidos Call Center');
      
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
    ->setCellValue('C2', 'Informe de Pedidos de Call Center')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:H2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:H3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");//Nombre de Empresa
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");//Estado de Empresa
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("20");//F. Pedido
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");//Cliente
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//Telefono
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("40");//Producto
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");//Cantidad
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");//Precio
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("30");//Ciudad
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");//Delivery

    $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($BStyle_top);
    $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($BStyle_bottom);
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Empresa')
    ->setCellValue('B5', 'Estado')
    ->setCellValue('C5', 'Fecha')
    ->setCellValue('D5', 'Cliente')
    ->setCellValue('E5', 'Telefono')
    ->setCellValue('F5', 'Producto')
    ->setCellValue('G5', 'Cantidad')
    ->setCellValue('H5', 'Precio')
    ->setCellValue('I5', 'Ciudad')
    ->setCellValue('J5', 'Delivery')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($style_align_center);
    
    $objPHPExcel->getActiveSheet()->freezePane('A6');//LINEA HORIZONTAL PARA SEPARAR CABECERA Y DETALLE
    
    $fila = 6;

    $arrParams = array(
      'Fe_Inicio' => $Fe_Inicio,
      'Fe_Fin' => $Fe_Fin,
      'ID_Tipo_Documento' => $ID_Tipo_Documento,
      'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
      'Nu_Estado_Pedido' => $Nu_Estado_Pedido,
      'iIdCliente' => $iIdCliente,
      'sNombreCliente' => $sNombreCliente,
      'ID_Filtro_Empresa' => $ID_Filtro_Empresa,
      'Nu_Estado_Pedido_Empresa' => $Nu_Estado_Pedido_Empresa
    );
    $arrData = $this->getReporteExcel($arrParams);
        
    if ( $arrData['sStatus'] == 'success' ) {
      $iCounter = 0; $fTotalCantidad = 0.00; $fTotalDelivery = 0.00;
      foreach ($arrData['arrData'] as $row) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':' . 'H' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($style_align_right);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->No_Empresa)
        ->setCellValue('B' . $fila, $row->Nu_Estado_Pedido_Empresa)
        ->setCellValue('C' . $fila, $row->Fe_Entrega)
        ->setCellValue('D' . $fila, $row->No_Entidad)
        ->setCellValue('E' . $fila, $row->Nu_Celular)
        ->setCellValue('F' . $fila, $row->No_Producto)
        ->setCellValue('G' . $fila, numberFormat($row->Qt_Producto, 0, '.', ','))
        ->setCellValue('H' . $fila, numberFormat($row->Ss_Precio, 2, '.', ','))
        ->setCellValue('I' . $fila, $row->No_Ciudad_Dropshipping)
        ->setCellValue('J' . $fila, $row->Ss_Precio_Delivery)
        ;

        $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

        $fila++;
        
        $fTotalCantidad += $row->Qt_Producto;
        $fTotalDelivery += $row->Ss_Precio_Delivery;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('F' . $fila, 'Total')
      ->setCellValue('G' . $fila, numberFormat($fTotalCantidad, 2, '.', ','))
      ->setCellValue('J' . $fila, numberFormat($fTotalDelivery, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':' . 'J' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'J' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('E' . $fila . ':' . 'J' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':J' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
	}
    
	public function crudPedido(){
    //CABECERA
    $arrCabeceraPedido = $_POST['arrCabeceraPedido'];
    //DETALLE
    $arrPedidoDetalle = $_POST['arrDetallePedido'];

		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    if($arrCabeceraPedido['EID_Pedido_Cabecera'] != ''){//editar
      $data = array(
        'No_Entidad_Order_Address_Entry' => $arrCabeceraPedido['No_Entidad_Order_Address_Entry'],
        'Nu_Celular_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Nu_Celular_Entidad_Order_Address_Entry'],
        'No_Ciudad_Dropshipping' => $arrCabeceraPedido['No_Ciudad_Dropshipping'],
        'Txt_Direccion_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Txt_Direccion_Entidad_Order_Address_Entry'],
        'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Txt_Direccion_Referencia_Entidad_Order_Address_Entry'],
        'Fe_Entrega' => ToDate($arrCabeceraPedido['Fe_Entrega']),
        'Nu_Forma_Pago_Dropshipping' => $arrCabeceraPedido['forma_pago'],
        'Nu_Servicio_Transportadora_Dropshipping' => $arrCabeceraPedido['servicio_transportadora'],
        'Txt_Glosa' => $arrCabeceraPedido['Txt_Glosa'],
        //'Nu_Estado_Pedido_Empresa' => 1,//PEDIDO COMPLETADO Y SOLO PUEDE CORREGIR ECXPRESS
      );
      $response = $this->PedidoProveedoDropshippingModel->actualizarPedido(array('ID_Pedido_Cabecera' => $arrCabeceraPedido['EID_Pedido_Cabecera']), $data);
      echo json_encode($response);
      exit();
    } else {
      $response = $this->PedidoProveedoDropshippingModel->agregarPedido($arrCabeceraPedido, $arrPedidoDetalle);
      echo json_encode($response);
      exit();
    }
	}
	
  public function ajax_edit($ID){
    echo json_encode($this->PedidoProveedoDropshippingModel->get_by_id($this->security->xss_clean($ID)));
  }
	
	public function cambiarEstadoPedidoEmpresa($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidoProveedoDropshippingModel->cambiarEstadoPedidoEmpresa($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
    
	public function actualizarPrecioDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataDelivery = $_POST['arrDataDelivery'];
    $data = array(
      'Ss_Precio_Delivery' => $arrDataDelivery['precio_delivery']
    );
    $response = $this->PedidoProveedoDropshippingModel->actualizarPrecioDelivery(array('ID_Pedido_Cabecera' => $arrDataDelivery['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
    
	public function actualizarNotaPedido(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataNotaPedido = $_POST['arrDataNotaPedido'];
    $data = array(
      'Txt_Glosa' => $arrDataNotaPedido['nota']
    );
    $response = $this->PedidoProveedoDropshippingModel->actualizarNotaPedido(array('ID_Pedido_Cabecera' => $arrDataNotaPedido['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
    
	public function asignarUsuarioDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataNotaPedido = $_POST['arrDataNotaPedido'];
    $data = array(
      'ID_Usuario_Asignar_Delivery' => $arrDataNotaPedido['id_usuario_delivery']
    );
    $response = $this->PedidoProveedoDropshippingModel->asignarUsuarioDelivery(array('ID_Pedido_Cabecera' => $arrDataNotaPedido['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
	
	public function cambiarEstadoImpresionGuiaMexico(){
		$response = $this->PedidoProveedoDropshippingModel->cambiarEstadoImpresionGuiaMexico($this->input->post('ID'), $this->input->post('imprimir_guia_mexico'), $this->input->post('Nu_Estado'));
    echo json_encode($response);
    exit();
	}
}
