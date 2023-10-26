<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class PedidosTiendaVirtualController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/PedidosModel');
		$this->load->model('HelperModel');
		$this->load->model('ConfiguracionModel');
		//$this->load->model('Logistica/MovimientoInventarioModel');
		//$this->load->model('DocumentoElectronicoModel');
	}

	public function importarExcelPedidos(){
		if (isset($_FILES['excel-archivo-pedidos']['name']) && isset($_FILES['excel-archivo-pedidos']['type']) && isset($_FILES['excel-archivo-pedidos']['tmp_name'])) {
      $archivo	= $_FILES['excel-archivo-pedidos']['name'];
      $tipo		= $_FILES['excel-archivo-pedidos']['type'];
      $destino	= "bak_" . $archivo;
		    
		  if (copy($_FILES['excel-archivo-pedidos']['tmp_name'], $destino)) {
        if (file_exists($destino)) {
          $this->load->library('Excel');
          $objReader = new PHPExcel_Reader_Excel2007();
          $objPHPExcel = $objReader->load($destino);
          $objPHPExcel->setActiveSheetIndex(0);

          $iCantidadRegistros = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

          $column = array(
            'ID_PEDIDO' => 'A',
            'NOMBRE_COMPLETO' => 'B',
            'TELEFONO' => 'C',
            'CIUDAD' => 'D',
            'DIRECCION' => 'E',
            'DIRECCION_REFERENCIA' => 'F',
            'FECHA_ENTREGA' => 'G',
            'FORMA_PAGO' => 'H',
            'TRANSPORTADORA' => 'I',
            'OBSERVACIONES' => 'J',
            'CODIGO' => 'K',
            'PRODUCTO' => 'L',
            'CANTIDAD' => 'M',
            'TOTAL' => 'N',
            'EMAIL' => 'O',
            'TRANSPORTADORA_MEXICO' => 'P'
          );

          $arrAjusteInventarioItem = array();
          for ($i = 2; $i <= $iCantidadRegistros; $i++) {
            $ID_PEDIDO = $objPHPExcel->getActiveSheet()->getCell($column['ID_PEDIDO'] . $i)->getCalculatedValue();
            $ID_PEDIDO = quitarCaracteresEspeciales(filter_var(trim($ID_PEDIDO)));

            $NOMBRE_COMPLETO = $objPHPExcel->getActiveSheet()->getCell($column['NOMBRE_COMPLETO'] . $i)->getCalculatedValue();
            $NOMBRE_COMPLETO = quitarCaracteresEspeciales(strtoupper(filter_var(trim($NOMBRE_COMPLETO))));

            $TELEFONO = $objPHPExcel->getActiveSheet()->getCell($column['TELEFONO'] . $i)->getCalculatedValue();
            $TELEFONO = quitarCaracteresEspeciales(filter_var(trim($TELEFONO)));

            $CIUDAD = $objPHPExcel->getActiveSheet()->getCell($column['CIUDAD'] . $i)->getCalculatedValue();
            $CIUDAD = quitarCaracteresEspeciales(strtoupper(filter_var(trim($CIUDAD))));

            $DIRECCION = $objPHPExcel->getActiveSheet()->getCell($column['DIRECCION'] . $i)->getCalculatedValue();
            $DIRECCION = quitarCaracteresEspeciales(strtoupper(filter_var(trim($DIRECCION))));

            $DIRECCION_REFERENCIA = $objPHPExcel->getActiveSheet()->getCell($column['DIRECCION_REFERENCIA'] . $i)->getCalculatedValue();
            $DIRECCION_REFERENCIA = quitarCaracteresEspeciales(strtoupper(filter_var(trim($DIRECCION_REFERENCIA))));

            $FECHA_ENTREGA = $objPHPExcel->getActiveSheet()->getCell($column['FECHA_ENTREGA'] . $i)->getCalculatedValue();
            $FECHA_ENTREGA = quitarCaracteresEspeciales(filter_var(trim($FECHA_ENTREGA)));
						if ( !empty($FECHA_ENTREGA) ) {
							$_FECHA_ENTREGA = PHPExcel_Shared_Date::ExcelToPHPObject($FECHA_ENTREGA);
							$FECHA_ENTREGA = $_FECHA_ENTREGA->format('Y-m-d');
						}

            $FORMA_PAGO = $objPHPExcel->getActiveSheet()->getCell($column['FORMA_PAGO'] . $i)->getCalculatedValue();
            $FORMA_PAGO = quitarCaracteresEspeciales(filter_var(trim($FORMA_PAGO)));

            $TRANSPORTADORA = $objPHPExcel->getActiveSheet()->getCell($column['TRANSPORTADORA'] . $i)->getCalculatedValue();
            $TRANSPORTADORA = quitarCaracteresEspeciales(filter_var(trim($TRANSPORTADORA)));

            $OBSERVACIONES = $objPHPExcel->getActiveSheet()->getCell($column['OBSERVACIONES'] . $i)->getCalculatedValue();
            $OBSERVACIONES = quitarCaracteresEspeciales(filter_var(trim($OBSERVACIONES)));

            $CODIGO = $objPHPExcel->getActiveSheet()->getCell($column['CODIGO'] . $i)->getCalculatedValue();
            $CODIGO = quitarCaracteresEspeciales(filter_var(trim($CODIGO)));

            $PRODUCTO = $objPHPExcel->getActiveSheet()->getCell($column['PRODUCTO'] . $i)->getCalculatedValue();
            $PRODUCTO = quitarCaracteresEspeciales(filter_var(trim($PRODUCTO)));

            $CANTIDAD = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['CANTIDAD'] . $i)->getCalculatedValue()));
            $CANTIDAD = quitarCaracteresEspeciales(filter_var(trim($CANTIDAD)));
						settype($CANTIDAD, "double");

            $TOTAL = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TOTAL'] . $i)->getCalculatedValue()));
            $TOTAL = quitarCaracteresEspeciales(filter_var(trim($TOTAL)));
						settype($TOTAL, "double");

            $EMAIL = $objPHPExcel->getActiveSheet()->getCell($column['EMAIL'] . $i)->getCalculatedValue();
            $EMAIL = quitarCaracteresEspeciales(filter_var(trim($EMAIL)));

            $TRANSPORTADORA_MEXICO = filter_var(trim($objPHPExcel->getActiveSheet()->getCell($column['TRANSPORTADORA_MEXICO'] . $i)->getCalculatedValue()));
            $TRANSPORTADORA_MEXICO = quitarCaracteresEspeciales(filter_var(trim($TRANSPORTADORA_MEXICO)));

            //SET
            if($TRANSPORTADORA_MEXICO==1){
              $TRANSPORTADORA_MEXICO=3;//BD 3=ECXLAE
            } else if($TRANSPORTADORA_MEXICO==2){
              $TRANSPORTADORA_MEXICO=1;//BD 1=99 MINUTOS
            } else if($TRANSPORTADORA_MEXICO==3){
              $TRANSPORTADORA_MEXICO=2;//BD 2=quikwen
            } else {
              $TRANSPORTADORA_MEXICO=3;//BD 3=ECXLAE
            }

            if(
              !empty($ID_PEDIDO) &&
              !empty($CODIGO) &&
              !empty($PRODUCTO) &&
              !empty($CANTIDAD) &&
              !empty($TOTAL)
            ){
              $arrPedidoManualExcel[] = array(
                'iIdPedido'           => $ID_PEDIDO,
                'sNombreCompleto'     => $NOMBRE_COMPLETO,
                'iTelefono'           => $TELEFONO,
                'sCiudad'             => $CIUDAD,
                'sDireccion'          => $DIRECCION,
                'sDireccionReferencia'=> $DIRECCION_REFERENCIA,
                'dFechaEntrega'       => $FECHA_ENTREGA,
                'iFormaPago'          => $FORMA_PAGO,
                'iTransportadora'     => $TRANSPORTADORA,
                'sObservaciones'      => $OBSERVACIONES,
                'iIdStockProducto'    => $CODIGO,
                'sNombreProducto'     => $PRODUCTO,
                'fCantidad'           => $CANTIDAD,
                'fTotal'              => $TOTAL,
                'sEmail'              => $EMAIL,
                'iTipoTransportadora' => $TRANSPORTADORA_MEXICO
              );
            }
          }// ./ for arr excel

          if(isset($this->session->userdata['usuario'])) {
            $this->load->view('header');
            $this->load->view('TiendaVirtual/PedidosViewExcel', array('sStatusExcel' => 0, 'arrPedidoManualExcel' => $arrPedidoManualExcel));
            $this->load->view('footer', array("js_pedidos_tienda_virtual" => true));
          }
        } else {
          unlink($destino);

          $sStatusExcel = 'error-archivo_no_existe';
          redirect('TiendaVirtual/PedidosTiendaVirtualController/listar/' . $sStatusExcel);
        }
      } else {
        unlink($destino);

        $sStatusExcel = 'error-copiar_archivo';
        redirect('TiendaVirtual/PedidosTiendaVirtualController/listar/' . $sStatusExcel);
      }
		}
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  $arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/PedidosView');
			$this->load->view('footer', array("js_pedidos_tienda_virtual" => true));
		}
	}	
	
	public function verPedido($iIdPedido){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PedidosModel->verPedido($this->security->xss_clean($iIdPedido)));
  }
  
  private function getReporte($arrParams){
    $arrResponseModal = $this->PedidosModel->getReporte($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
            
      $sRelacionData='';
      $sAccionImprimir='imprimir';
      $sVacio='mostrar-img-logo_punto_venta';
      
      $sMensajeVendedorPedido = "
*¡Felicidades por tu compra!* 🎉\n
Ayúdanos a confirmar tú envío:\n
1️⃣Si, deseo envío inmediato.\n
2️⃣No, deseo hablar con un agente para coordinar la entrega.\n
3️⃣Me equivoque, deseo cancelar mi pedido.";
$sMensajeVendedorPedido = urlencode($sMensajeVendedorPedido);

      $sNumeroCelularSoporteCoordinado = '51986224023';
      $sNumeroCelularSoporteCallCenter = '51986224023';//
      if($this->user->ID_Pais == 2) {//2=MEXICO
        $sNumeroCelularSoporteCoordinado = '5215561805920';
        $sNumeroCelularSoporteCallCenter = '525611763134';
      }

      foreach ($arrResponseModal['arrData'] as $row) {
        $rows = array();
        
        $sTipoVenta = '';
        if($row->Nu_Tipo_Venta_Generada== 1){
          $sTipoVenta = 'Tienda Virtual';
        } else if($row->Nu_Tipo_Venta_Generada==2){
          $sTipoVenta = 'Manual';
        }
        
        $sCodigoPaisCelular ='51';
        $sImgPais = '<img src="' . base_url() . 'assets/img/peru.png" class="rounded-circle" alt="País Peru" width="16">';
        if($row->ID_Pais == 2) {//2=MEXICO
          $sCodigoPaisCelular ='52';
          $sImgPais = '<img src="' . base_url() . 'assets/img/mexico.png" class="rounded-circle" alt="País México" width="16">';
        }

        $rows['sTipoVenta'] = $sTipoVenta;
        $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
        $rows['ID_Pedido_Cabecera'] = $row->ID_Pedido_Cabecera;
        
        $sWhatsAppCliente = '';
        if(!empty($row->Nu_Celular)){
          $sWhatsAppCliente = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular  . '&text=' . $sMensajeVendedorPedido . '" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
        }
        $rows['No_Entidad'] = $row->No_Entidad . $sWhatsAppCliente;

        $rows['No_Ciudad_Dropshipping'] = $row->No_Ciudad_Dropshipping;
        $rows['Ss_Total'] = $row->Ss_Total;
        //$rows['No_Estado_Recepcion'] = $row->No_Estado_Recepcion;
        
        $sFormaPagoDropshipping = 'Sin definir';
        if($row->Nu_Forma_Pago_Dropshipping == 1){
          $sFormaPagoDropshipping = 'Contra Entrega';
        } else if($row->Nu_Forma_Pago_Dropshipping==2){
          $sFormaPagoDropshipping = 'Dropshipping';
        }
        
        $sServicioTransportadora = 'Sin definir';
        if($row->Nu_Servicio_Transportadora_Dropshipping == 1){
          $sServicioTransportadora = 'Call Center <a href="https://api.whatsapp.com/send?phone=' . $sNumeroCelularSoporteCallCenter . '&text=Necesito%20ayuda%20con%20CallCenter" alt="EcxpressLae Call Center" title="EcxpressLae Call Center" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
        } else if($row->Nu_Servicio_Transportadora_Dropshipping==2){
          $sServicioTransportadora = 'Coordinado <a href="https://api.whatsapp.com/send?phone=' . $sNumeroCelularSoporteCoordinado . '&text=Necesito%20ayuda%20con%20Coordinado" alt="EcxpressLae Coordinado" title="EcxpressLae Coordinado" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
        }

        $rows['Fe_Entrega'] = ($row->Nu_Forma_Pago_Dropshipping == 0 ? '' : ToDateBD($row->Fe_Entrega));
        $rows['Nu_Forma_Pago_Dropshipping'] = $sFormaPagoDropshipping;
        $rows['Nu_Servicio_Transportadora_Dropshipping'] = $sServicioTransportadora;
        
        $sServicioTransportadoraExcel = 'Sin definir';
        if($row->Nu_Servicio_Transportadora_Dropshipping == 1){
          $sServicioTransportadoraExcel = 'Call Center';
        } else if($row->Nu_Servicio_Transportadora_Dropshipping==2){
          $sServicioTransportadoraExcel = 'Coordinado';
        }
        $rows['No_Servicio_Transportadora_Dropshipping_Excel'] = $sServicioTransportadoraExcel;

        $sEstadoPedidoEmpresa = '';
        if($row->Nu_Estado_Pedido_Empresa==0){
          $sEstadoPedidoEmpresa = 'Pendiente';
        } else if($row->Nu_Estado_Pedido_Empresa==1){
          $sEstadoPedidoEmpresa = 'Completado';
        } else if($row->Nu_Estado_Pedido_Empresa==2){
          $sEstadoPedidoEmpresa = 'Pago realizado';
        } else if($row->Nu_Estado_Pedido_Empresa==3){
          $sEstadoPedidoEmpresa = 'Falsa Parada';
        }
        $rows['sEstadoPedidoEmpresa'] = $sEstadoPedidoEmpresa;
        $rows['sEstadoPedidoEmpresaExcel'] = $sEstadoPedidoEmpresa;

        //$rows['No_Class_Estado_Recepcion'] = ($row->Nu_Tipo_Metodo_Entrega_Tienda_Virtual == 6 ? 'danger' : 'success');
        
			  $arrEstadoPedidoTienda = $this->HelperModel->obtenerEstadoOrdenPedidoTienda($row->Nu_Estado);
        
        //if($this->user->ID_Usuario!=1) {//1=root
          $rows['No_Estado_Pedido'] = '<span class="label label-' . $arrEstadoPedidoTienda['No_Class_Estado'] . '">' . $arrEstadoPedidoTienda['No_Estado'] . '</span>';
          $rows['No_Estado_Pedido_Cliente_Excel'] = $arrEstadoPedidoTienda['No_Estado'];
        /*
        } else {
          if($row->ID_Pais == 1) {//1=PERU
            //SHOP
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Confirmado</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Preparando</a></li>
                <li><a alt="En Camino" title="En Camino" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4);">En Camino</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5);">Entregado</a></li>
                <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6);">Rechazado</a></li>
              </ul>
            </div>';
          } else if($row->ID_Pais == 2) {//2=MEXICO
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1);">Pendiente</a></li>
                <li><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Confirmado</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8);">Guia Generada</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Preparando</a></li>
                <li><a alt="Recolectado" title="Recolectado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',9);">Recolectado</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',10);">Transito</a></li>
                <li><a alt="En Camino" title="En Camino" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4);">En Camino</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',11);">1 Intento</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',12);">2 Intento</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5);">Entregado</a></li>
                <li><a alt="Devolución Pendiente" title="Devolución Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',13);">Devolución Pendiente</a></li>
                <li><a alt="Devuelto" title="Devuelto" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',14);">Devuelto</a></li>
                <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6);">Rechazado</a></li>
              </ul>
            </div>';
          }
          $rows['No_Estado_Pedido'] = $dropdown;
          $rows['No_Estado_Pedido_Cliente_Excel'] = $arrEstadoPedidoTienda['No_Estado'];
        }
        */

        $precio_callcenter = 5;//peru
        if($row->ID_Pais == 2){
          $precio_callcenter = 30;//mexico
        }
        
        //$ss_delivery_precio = ($row->Nu_Estado != 5 && $row->Nu_Estado != 6 ? '' : $this->user->No_Signo . ' ' . $row->Ss_Precio_Delivery);
        //$rows['Ss_Precio_Delivery'] = $ss_delivery_precio;
        //$rows['Ss_Precio_Delivery'] = '';
        //$rows['Ss_Precio_Delivery_Excel'] = '';

        $rows['Ss_Precio_CallCenter'] = (($row->Nu_Estado == 5 && $row->Nu_Servicio_Transportadora_Dropshipping == 1) ? $precio_callcenter : '');
        $rows['Ss_Precio_Dropshipping'] = (($row->Nu_Estado == 5 && $row->Nu_Forma_Pago_Dropshipping == 2) ? 8 : '');

        $rows['sAccionVer'] = ($row->Nu_Tipo_Venta_Generada == 1 ? '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-list-alt" aria-hidden="true"></i></button>' : '');
        $rows['sAccionEditar'] = '<button class="btn btn-xs btn-link" alt="Completar pedido" title="Completar pedido" href="javascript:void(0)" onclick="editarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-edit" aria-hidden="true"></i></button>';

      
        $rows['sAccionEliminar'] = (($row->Nu_Estado_Pedido_Empresa==0 && $row->Nu_Estado==1) ? '<button class="btn btn-xs btn-link" alt="Eliminar pedido" title="Eliminar pedido" href="javascript:void(0)" onclick="eliminarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></button>' : '');
        
        $sRelacionData = '<button class="btn btn-xs btn-link" alt="Generar Venta" title="Generar Venta" href="javascript:void(0)" onclick="generarVenta(\'' . $row->ID_Pedido_Cabecera . '\')">Generar</button>';
        $arrParams = array('ID_Pedido_Cabecera' => $row->ID_Pedido_Cabecera);
				$arrResponseDocument = $this->PedidosModel->getRelacionPedidoVenta($arrParams);
				if ($arrResponseDocument['sStatus'] == 'success') {
          $sRelacionData = '';
          $sAccionFacturar = '';
					foreach ($arrResponseDocument['arrData'] as $rowEnlace) {
            $arrEstadoDocumento = $this->HelperModel->obtenerEstadoDocumentoArray($rowEnlace->Nu_Estado);
            $sRelacionData .= '<button type="button" class="btn btn-xs btn-link" alt="Imprimir comprobante" title="Imprimir comprobante" href="javascript:void(0)" onclick="formatoImpresionTicket(\'' . $sAccionImprimir . '\', \'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $sVacio . '\', \'' . $rowEnlace->Txt_Url_PDF . '\')"><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>';
            $sRelacionData .= '<br><button type="button" id="whatsapp-' . $rowEnlace->ID_Documento_Cabecera . '" class="btn btn-xs btn-link" alt="WhatsApp" title="WhatsApp" href="javascript:void(0)" onclick="sendWhatsapp(\'' . $rowEnlace->ID_Documento_Cabecera . '\', \'' . $rowEnlace->ID_Entidad . '\')"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></button>';
          }
				}

        $rows['sGenerarTicket'] = $sRelacionData;

        $rows['sVerNovedades'] = '<button class="btn btn-xs btn-link" alt="Ver Novedades" title="Ver Novedades" href="javascript:void(0)" onclick="verNovedades(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-bell"></i></button>';

        //$btn_generar_guia_99 = 'Pendiente...';

        $btn_generar_guia_99='';
        $sNombrePaqueteria = '';
        if($row->Nu_Forma_Pago_Dropshipping==1){//Contra entrega
          if($row->ID_Pais == 2) {//2=MEXICO
            if ($row->Nu_Tipo_Guia_Api==1) {//1=99 minutos
              $sNombrePaqueteria = '<strong>99 Minutos</strong><br>';
              if( !empty($row->Txt_Response_TrackingId_Api) ) {
                $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia 99" title="PDF Guia 99" href="javascript:void(0)" onclick="pdfGuia99(\'' . $row->Txt_Response_TrackingId_Api . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';
                $btn_generar_guia_99 = $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
              }
            } else if ($row->Nu_Tipo_Guia_Api==2) {//2=Quiken
              $sNombrePaqueteria = '<strong>Quiken</strong><br>';
              
              if( !empty($row->Txt_Response_TrackingId_Api) ) {
                $btn_generar_guia_99 = '<a class="btn btn-xs btn-link" alt="PDF Guia Quiken" title="PDF Guia Quiken" target="_blank" rel="noopener noreferrer" href="' . $row->Txt_Response_Guia_Api . '"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</a>';
                $btn_generar_guia_99 = $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
              }
            } else if ($row->Nu_Tipo_Guia_Api==3) {//3=Ecxlae
              $sNombrePaqueteria = '<strong>Ecxlae</strong><br>';

              if( !empty($row->Txt_Response_Guia_Api) ) {
                $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia Ecxlae" title="PDF Guia Ecxlae" href="javascript:void(0)" onclick="pdfGuiaEcxlae(\'' . $row->Txt_Response_Guia_Api . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';
                $btn_generar_guia_99 = $row->Txt_Response_Guia_Api . ' ' . $btn_generar_guia_99;
              }
            }
          }
        }

        $rows['btn_generar_guia_99'] = $sNombrePaqueteria . $btn_generar_guia_99;

        //Ganancia por pedido
        //total pedido - total proveedor - costo de envio - 4% de comision de total del pedido (solo si es callcenter restar 30 pesos para mexico)
        $ganancia=0;
        
        if($row->ID_Pais == 2) {//2=MEXICO
          if(($row->Nu_Estado == 5 || $row->Nu_Estado == 13 || $row->Nu_Estado == 14) && $row->Nu_Estado_Pedido_Empresa == 1) {
            if($row->Nu_Estado == 5){//5=entregado
              $total_comision_mexico = (($row->Ss_Total * 4) / 100);
              if($row->Ss_Precio_Delivery>0 && $row->Nu_Tipo_Guia_Api==1){//1=99 minutos
                //$total_comision_mexico += (($row->Ss_Total * 4) / 100);
                $row->Ss_Precio_Delivery += (($row->Ss_Total * 4) / 100);
              }
              
              $ganancia = ($row->Ss_Total - $row->Ss_Total_Proveeedor - $row->Ss_Precio_Delivery - $total_comision_mexico);
              if($row->Nu_Servicio_Transportadora_Dropshipping==1)//1=callcenter mexico $30 pesos
                $ganancia -= $precio_callcenter;
            }

            //cuando es el estado Devolución Pendiente solo se resta el delivery será negativo para su ganancia
            if($row->Nu_Estado == 13){//13=Devolución Pendiente
              $ganancia = -($row->Ss_Total_Proveeedor + $row->Ss_Precio_Delivery);
            }

            //cuando es el estado devuelto solo se resta el delivery será negativo para su ganancia
            if($row->Nu_Estado == 14){//14=devuelto
              $ganancia = -$row->Ss_Precio_Delivery;
            }
          }
        } else if($row->ID_Pais == 1) {//1=peru
          if($row->Nu_Estado == 5) {//5=entregado
            $fPrecioCallcenter = 0;
            if($row->Nu_Servicio_Transportadora_Dropshipping == 1){//callcenter
              $fPrecioCallcenter = 5;
            }

            $fPrecioDropshipping = 0;
            if($row->Nu_Forma_Pago_Dropshipping==2){//2=dropshipping
              $fPrecioDropshipping = 8;
            }
            
            //total cliente - total proveedor - delivery - call center - dropshipping (OLVA, etc)
            $ganancia = ($row->Ss_Total - $row->Ss_Total_Proveeedor - $row->Ss_Precio_Delivery - $fPrecioCallcenter - $fPrecioDropshipping);
          }

          if($row->Nu_Estado == 6) {//6=Rechazado
            $ganancia = -$row->Ss_Precio_Delivery;
          }
        }

        $rows['Ss_Precio_Delivery'] = $row->Ss_Precio_Delivery;
        $rows['Ss_Precio_Delivery_Excel'] = $row->Ss_Precio_Delivery;
        $rows['Txt_Glosa'] = $row->Txt_Glosa;

        $rows['ganancia'] = numberFormat($ganancia, 2, '.', ',');

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
    echo json_encode($this->PedidosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
  
	public function eliminarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidosModel->eliminarPedido($this->security->xss_clean($ID)));
	}
  
	public function generarVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->PedidosModel->generarVenta($this->input->post()));
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
      'Nu_Estado_Pedido_Empresa' => $this->input->post('Nu_Estado_Pedido_Empresa'),
      'sNombreCiudad' => $this->input->post('sNombreCiudad')
    );
    echo json_encode($this->getReporte($arrParams));
  }
    
  public function sendReportePDF($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $ID_Filtro_Empresa, $Nu_Estado_Pedido_Empresa, $sNombreCiudad){
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
    $sNombreCiudad = $this->security->xss_clean($sNombreCiudad);

    $fileNamePDF = "reporte_pedidos_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

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
      'Nu_Estado_Pedido_Empresa' => $Nu_Estado_Pedido_Empresa,
      'sNombreCiudad' => $sNombreCiudad
    );

    ob_start();
    $file = $this->load->view('TiendaVirtual/pdf/PedidosMarketplaceViewPDF', array(
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
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $ID_Filtro_Empresa, $Nu_Estado_Pedido_Empresa, $sNombreCiudad){
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
    $sNombreCiudad = $this->security->xss_clean($sNombreCiudad);
    
		$fileNameExcel = "reporte_pedidos_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Mis pedidos');
      
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
    ->setCellValue('C2', 'Informe de Pedidos')
    ->setCellValue('C3', 'Desde: ' . ToDateBD($Fe_Inicio) . ' Hasta: ' . ToDateBD($Fe_Fin));
    
    $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style_align_center);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($style_align_center);
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C2:L2');
    $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('C3:L3');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    // /. Title
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");//Canal
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");//F. Pedido
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");//ID Pedido
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");//Cliente
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");//Total
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");//F. Entrega
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");//Forma Pago
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");//Transportadora
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20");//Estado Empresa
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");//Recepción
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");//Estado Cliente
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");//Delivery
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");//Call Center
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("20");//Dropshipping
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("100");//Nota
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("50");//Ciudad

    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($BStyle_top);
    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($BStyle_bottom);
    $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('G5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('J5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('L5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('N5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('O5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($BStyle_right);
    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->getFont()->setBold(true);
    
    $objPHPExcel->setActiveSheetIndex($hoja_activa)
    ->setCellValue('A5', 'Canal')
    ->setCellValue('B5', 'F. Pedido')
    ->setCellValue('C5', 'ID Pedido')
    ->setCellValue('D5', 'Cliente')
    ->setCellValue('E5', 'Total')
    ->setCellValue('F5', 'F. Entrega')
    ->setCellValue('G5', 'Forma Pago')
    ->setCellValue('H5', 'Transportadora')
    ->setCellValue('I5', 'Estado Empresa')
    ->setCellValue('J5', 'Recepción')
    ->setCellValue('K5', 'Estado Cliente')
    ->setCellValue('L5', 'Precio Delivery')
    ->setCellValue('M5', 'Precio Call Center')
    ->setCellValue('N5', 'Precio Dropshipping')
    ->setCellValue('O5', 'Nota')
    ->setCellValue('P5', 'Ciudad')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($style_align_center);
    
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
      'Nu_Estado_Pedido_Empresa' => $Nu_Estado_Pedido_Empresa,
      'sNombreCiudad' => $sNombreCiudad
    );
    $arrData = $this->getReporte($arrParams);
        
    if ( $arrData['sStatus'] == 'success' ) {
      $iCounter = 0; $fTotal = 0.00; $fTotalGeneral = 0.00; $fTotalDelivery = 0.00; $fTotalCallcenter = 0.00; $fTotalDropshipping = 0.00;
      foreach ($arrData['arrData'] as $row) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':' . 'C' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':' . 'K' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila)->applyFromArray($style_align_left);
        
        $objPHPExcel->setActiveSheetIndex($hoja_activa)
        ->setCellValue('A' . $fila, $row->sTipoVenta)
        ->setCellValue('B' . $fila, $row->Fe_Emision_Hora)
        ->setCellValue('C' . $fila, $row->ID_Pedido_Cabecera)
        ->setCellValue('D' . $fila, $row->No_Entidad)
        ->setCellValue('E' . $fila, numberFormat($row->Ss_Total, 2, '.', ','))
        ->setCellValue('F' . $fila, $row->Fe_Entrega)
        ->setCellValue('G' . $fila, $row->Nu_Forma_Pago_Dropshipping)
        ->setCellValue('H' . $fila, $row->No_Servicio_Transportadora_Dropshipping_Excel)
        ->setCellValue('I' . $fila, $row->sEstadoPedidoEmpresaExcel)
        ->setCellValue('J' . $fila, 'Delivery')
        ->setCellValue('K' . $fila, $row->No_Estado_Pedido_Cliente_Excel)
        ->setCellValue('L' . $fila, $row->Ss_Precio_Delivery_Excel)
        ->setCellValue('M' . $fila, $row->Ss_Precio_CallCenter)
        ->setCellValue('N' . $fila, $row->Ss_Precio_Dropshipping)
        ->setCellValue('O' . $fila, $row->Txt_Glosa)
        ->setCellValue('P' . $fila, $row->No_Ciudad_Dropshipping)
        ;

        $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle('N' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

        $fila++;
        
        $fTotalGeneral += $row->Ss_Total;
        $fTotalDelivery += $row->Ss_Precio_Delivery_Excel;
        $fTotalCallcenter += $row->Ss_Precio_CallCenter;
        $fTotalDropshipping += $row->Ss_Precio_Dropshipping;
      } // /. foreach arrData
      
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('D' . $fila, 'Total')
      ->setCellValue('E' . $fila, numberFormat($fTotalGeneral, 2, '.', ','))
      ->setCellValue('L' . $fila, numberFormat($fTotalDelivery, 2, '.', ','))
      ->setCellValue('M' . $fila, numberFormat($fTotalCallcenter, 2, '.', ','))
      ->setCellValue('N' . $fila, numberFormat($fTotalDropshipping, 2, '.', ','));
      
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'N' . $fila)->applyFromArray($style_align_right);
                  
      $objPHPExcel->getActiveSheet()
      ->getStyle('A' . $fila . ':' . 'N' . $fila)
      ->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E7E7E7')
          )
        )
      );
      $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':' . 'N' . $fila)->getFont()->setBold(true);
    } else {
      $objPHPExcel->setActiveSheetIndex($hoja_activa)
      ->setCellValue('A' . $fila, $arrData['sMessage']);
      $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
      $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
    }// /. if - else arrData
        
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
	}
    
	public function crudPedido(){
    if($_POST['arrCabeceraPedido']['ENu_Estado_Pedido_Empresa'] == 0) {//0=pedido pendiente
      //CABECERA
      $arrCabeceraPedido = $_POST['arrCabeceraPedido'];
      //DETALLE
      $arrPedidoDetalle = $_POST['arrDetallePedido'];

      if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

      if($arrCabeceraPedido['EID_Pedido_Cabecera'] != ''){//editar
        $data = array(
          'No_Entidad_Order_Address_Entry' => $arrCabeceraPedido['No_Entidad_Order_Address_Entry'],
          'Nu_Celular_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Nu_Celular_Entidad_Order_Address_Entry'],
          'Txt_Email_Dropshipping' => $arrCabeceraPedido['Txt_Email_Dropshipping'],
          'No_Ciudad_Dropshipping' => $arrCabeceraPedido['No_Ciudad_Dropshipping'],
          'Txt_Direccion_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Txt_Direccion_Entidad_Order_Address_Entry'],
          'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' => $arrCabeceraPedido['Txt_Direccion_Referencia_Entidad_Order_Address_Entry'],
          'Fe_Entrega' => ToDate($arrCabeceraPedido['Fe_Entrega']),
          'Nu_Forma_Pago_Dropshipping' => $arrCabeceraPedido['forma_pago'],
          'Nu_Tipo_Guia_Api' => $arrCabeceraPedido['paqueteria'],
          'Nu_Servicio_Transportadora_Dropshipping' => $arrCabeceraPedido['servicio_transportadora'],
          'Txt_Glosa' => $arrCabeceraPedido['Txt_Glosa'],
          'Nu_Estado_Pedido_Empresa' => 0,//PEDIDO COMPLETADO Y SOLO PUEDE CORREGIR ECXPRESS
        );
        $response = $this->PedidosModel->actualizarPedido(array('ID_Pedido_Cabecera' => $arrCabeceraPedido['EID_Pedido_Cabecera']), $data, $arrPedidoDetalle, $arrCabeceraPedido['EID_Empresa_Pedido']);
        echo json_encode($response);
        exit();
      } else {
        $response = $this->PedidosModel->agregarPedido($arrCabeceraPedido, $arrPedidoDetalle);
        echo json_encode($response);
        exit();
      }
    } else {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Sin acceso a modificar porque ya se completo pedido'
			));
      exit();
    }
	}
	
  public function ajax_edit($ID){
    echo json_encode($this->PedidosModel->get_by_id($this->security->xss_clean($ID)));
  }
	
	public function guardarPedidosManualExcel(){
		echo json_encode($this->PedidosModel->guardarPedidosManualExcel($this->input->post()));
	}

	public function preguntasFrecuentes(){
		if(isset($this->session->userdata['usuario'])) {
		  $arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/PreguntasFrecuentesView');
			$this->load->view('footer', array("js_pedidos_tienda_virtual" => true));
		}
	}
}
