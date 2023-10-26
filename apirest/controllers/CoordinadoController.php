<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class CoordinadoController extends CI_Controller {
	
	function __construct(){
    parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('CoordinadoModel');
		$this->load->model('HelperModel');
    $this->load->model('HelperDropshippingModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  $arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$this->load->view('header', array("arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,));
			$this->load->view('CoordinadoView');
			$this->load->view('footer', array("js_coordinado" => true));
		}
	}	
	
	public function verPedido($iIdPedido){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->CoordinadoModel->verPedido($this->security->xss_clean($iIdPedido)));
  }
  
  private function getReporte($arrParams){
    $arrResponseModal = $this->CoordinadoModel->getReporte($arrParams);
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
        
        $sCodigoPaisCelular ='51';
        $sImgPais = '<img src="' . base_url() . 'assets/img/peru.png" class="rounded-circle" alt="País Peru" width="16">';
        if($row->ID_Pais == 2) {//2=MEXICO
          $sCodigoPaisCelular ='52';
          $sImgPais = '<img src="' . base_url() . 'assets/img/mexico.png" class="rounded-circle" alt="País México" width="16">';
        }
        
        $rows['No_Empresa'] = $row->No_Empresa . ' ' . $sImgPais;
        $rows['sTipoVenta'] = $sTipoVenta;
        $rows['Fe_Emision_Hora'] = allTypeDate($row->Fe_Emision_Hora, '-', 0);
        $rows['ID_Pedido_Cabecera'] = $row->ID_Pedido_Cabecera;
        
        //luego cambiar por pais
        $sWhatsAppCliente = '';
        $sWhatsAppClienteInterno2 = '';
        $sWhatsAppClienteInterno3 = '';
        if(!empty($row->Nu_Celular)){
          //PERU Y MEXICO
          $sMensajeVendedorPedido = "🎉 *¡Felicidades por tu compra " . $row->No_Entidad . "!* 🎉 \n\n";
          $sMensajeVendedorPedido .= "Recibimos el siguiente pedido: \n";
          
          $arrParamsPedidoDetalle = array('ID_Pedido_Cabecera' => $row->ID_Pedido_Cabecera);
          $arrResponseDetallePedido = $this->HelperDropshippingModel->getPedidoDetalle($arrParamsPedidoDetalle);
          foreach($arrResponseDetallePedido['result'] as $row_pedido_detalle){
            $sMensajeVendedorPedido .= round($row_pedido_detalle->Qt_Producto, 0) . " - " . $row_pedido_detalle->No_Producto . ' ' . $row_pedido_detalle->Txt_Nota_Item . " \n";
          }

          $sMensajeVendedorPedido .= "\npor un total de *" . $row->No_Signo . ' ' . $row->Ss_Total . "* con envío totalmente *GRATIS* 🥳 " . $row->ID_Pedido_Cabecera;
          $sMensajeVendedorPedido = urlencode($sMensajeVendedorPedido);
        
          $sWhatsAppCliente = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular . '&text=' . $sMensajeVendedorPedido . '" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
          
          //MEXICO - WhatsApp interno para coordinado y callcenter
          $btn_whatsapp_interno_mexico = '';
          if($row->ID_Pais == 2) {//2=MEXICO
            $sMensajeVendedorPedidoInterno2 = "Hola " . $row->No_Entidad . ", espero se encuentre bien. 👋🏻\n\n";
            $sMensajeVendedorPedidoInterno2 .= "Le comento que no hemos enviado su pedido debido a que no hemos recibido confirmación de su parte. 😢\n\n";
            $sMensajeVendedorPedidoInterno2 .= "¿Desea confirmar el pedido para proceder al envío? 🤔💭";
            $sMensajeVendedorPedidoInterno2 = urlencode($sMensajeVendedorPedidoInterno2);
            $sWhatsAppClienteInterno2 = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular . '&text=' . $sMensajeVendedorPedidoInterno2 . '" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
            
            //link de tienda virtual
            $sTipoDominio = $row->Nu_Tipo_Tienda;
            $sDominio='';
            if($sTipoDominio=='3'){//dominio
              $sDominio = $row->No_Dominio_Tienda_Virtual;
            } else if($sTipoDominio=='1'){//subdominio
              $sDominio = $row->No_Subdominio_Tienda_Virtual . '.' . $row->No_Dominio_Tienda_Virtual;
            }
            $sMensajeVendedorPedidoInterno3 = "Hola " . $row->No_Entidad . " espero se encuentre bien. 👋🏻\n\n";
            $sMensajeVendedorPedidoInterno3 .= "Le informo que debido a que no hemos recibido su confirmación, su pedido ha sido cancelado ❌😢\n\n";
            $sMensajeVendedorPedidoInterno3 .= "🙏🏻 Gracias por visitar nuestra tienda " . $sDominio;
            $sMensajeVendedorPedidoInterno3 = urlencode($sMensajeVendedorPedidoInterno3);
            $sWhatsAppClienteInterno3 = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular . '&text=' . $sMensajeVendedorPedidoInterno3 . '" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';

            $btn_whatsapp_interno_mexico = ' ' . $sWhatsAppClienteInterno2 . ' ' . $sWhatsAppClienteInterno3;
          }
        }

        $rows['No_Entidad'] = $row->No_Entidad . '<br>' . $sCodigoPaisCelular . $row->Nu_Celular . $sWhatsAppCliente . $btn_whatsapp_interno_mexico;

        $rows['No_Ciudad_Dropshipping'] = $row->Txt_Direccion_Entidad_Order_Address_Entry . '<br>' . $row->No_Ciudad_Dropshipping;
        $rows['Ss_Total'] = $row->Ss_Total;
        //$rows['No_Estado_Recepcion'] = $row->No_Estado_Recepcion;
        
        $btn_delivery_precio = ' <button class="btn btn-xs btn-link" alt="Precio de delivery" title="Precio de delivery" href="javascript:void(0)" onclick="agregarPrecioDeliveryPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-money"></i> Precio Delivery</button>';
        if($row->Nu_Estado==5 || $row->Nu_Estado==6){
          $btn_delivery_nota = ( !empty($row->Ss_Precio_Delivery) ? $row->No_Signo . ' ' . $row->Ss_Precio_Delivery : '');
        } else {
          $btn_delivery_nota = ( !empty($row->Ss_Precio_Delivery) ? $row->No_Signo . ' ' . $row->Ss_Precio_Delivery . $btn_delivery_precio : $btn_delivery_precio);
        }

        $btn_delivery_nota .= ($row->Nu_Estado == 6 ? '<br><button class="btn btn-xs btn-link" alt="Agregar Nota" title="Agregar Nota" href="javascript:void(0)" onclick="agregarNotaPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-comment"></i> Agregar Nota</button>' : '');

        //SI YA ASIGNO DELIVERY NO MOSTRAR
        $btn_delivery_nota .= '<br>' . (!empty($row->ID_Usuario_Asignar_Delivery) ? $row->No_Delivery . ' <button class="btn btn-xs btn-link" alt="Cambiar Delivery" title="Cambiar Delivery" href="javascript:void(0)" onclick="asignarDelivery(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-motorcycle"></i> Cambiar Delivery</button>' : '<button class="btn btn-xs btn-link" alt="Asignar Delivery" title="Asignar Delivery" href="javascript:void(0)" onclick="asignarDelivery(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-motorcycle"></i> Asignar Delivery</button>');
        
        //SI EL CLIENTE PAGA ADELANTADO UNA PARTE A ECXLAE DE 30 SOLES LUEGO
        if($row->Nu_Forma_Pago_Dropshipping==2){
          if($row->Nu_Estado==5 || $row->Nu_Estado==6){
            $btn_delivery_nota .= '';
          } else {
            $btn_delivery_nota .= '<button class="btn btn-xs btn-link" alt="Saldo a Favor" title="Saldo a Favor" href="javascript:void(0)" onclick="agregarSaldoFavor(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-retweet"></i> Saldo a Favor</button>';
          }
        }

        $rows['Ss_Precio_Delivery'] = $btn_delivery_nota;
        $rows['Ss_Precio_Delivery_Excel'] = $row->Ss_Precio_Delivery;
        $rows['Ss_Saldo_A_Favor_Delivery_Interno'] = $row->Ss_Saldo_A_Favor_Delivery_Interno;
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
        $dropdown_estado_pedido = '<div class="dropdown">
          <button style="width: 100%;" class="btn btn-' . $sClassEstadoPedidoEmpresa . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $sEstadoPedidoEmpresa . ' <span class="caret"></span></button>
          <ul class="dropdown-menu" style="width: 100%; position: sticky;">
            <li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoPedidoEmpresa(\'' . $row->ID_Pedido_Cabecera . '\',0);">Pendiente</a></li>
            <li><a alt="Completado" title="Completado" href="javascript:void(0)" onclick="cambiarEstadoPedidoEmpresa(\'' . $row->ID_Pedido_Cabecera . '\',1);">Completado</a></li>
          </ul>
        </div>';
        /*
        <li><a alt="Pago realizado" title="Pago realizado" href="javascript:void(0)" onclick="cambiarEstadoPedidoEmpresa(\'' . $row->ID_Pedido_Cabecera . '\',2);">Pago Realizado</a></li>
        <li><a alt="Falsa Parada" title="Falsa Parada" href="javascript:void(0)" onclick="cambiarEstadoPedidoEmpresa(\'' . $row->ID_Pedido_Cabecera . '\',3);">Falsa Parada</a></li>
        */
        $rows['sEstadoPedidoEmpresa'] = $dropdown_estado_pedido;
        $rows['sEstadoPedidoEmpresaExcel'] = $sEstadoPedidoEmpresa;

        //$rows['No_Class_Estado_Recepcion'] = ($row->Nu_Tipo_Metodo_Entrega_Tienda_Virtual == 6 ? 'danger' : 'success');
        
			  $arrEstadoPedidoTienda = $this->HelperModel->obtenerEstadoOrdenPedidoTienda($row->Nu_Estado);
        
        if($row->ID_Pais == 1) {//1=PERU
          //if($row->Nu_Estado==1 || $row->Nu_Estado==2 || $row->Nu_Estado==3 || $row->Nu_Estado==4){
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" id="btn-dropdown-pedido_estado-' . $row->ID_Pedido_Cabecera . '" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(2)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(2)['No_Estado'] . '\');">Confirmado</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(3)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(3)['No_Estado'] . '\');">Preparando</a></li>
                <li><a alt="En Camino" title="En Camino" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(4)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(4)['No_Estado'] . '\');">En Camino</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(5)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(5)['No_Estado'] . '\');">Entregado</a></li>
                <li><a alt="Devuelto" title="Devuelto" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',14, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(14)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(14)['No_Estado'] . '\');">Devuelto</a></li>
                <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(6)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(6)['No_Estado'] . '\');">Rechazado</a></li>
              </ul>
            </div>';
          /*
          } else {
            $dropdown = '<span class="label label-' . $arrEstadoPedidoTienda['No_Class_Estado'] . '">' . $arrEstadoPedidoTienda['No_Estado'] . '</span>';
          }
          */
        } else if($row->ID_Pais == 2) {//2=MEXICO
          //if($row->Nu_Estado==1 || $row->Nu_Estado==2 || $row->Nu_Estado==8 || $row->Nu_Estado==3 || $row->Nu_Estado==9 || $row->Nu_Estado==10 || $row->Nu_Estado==4 || $row->Nu_Estado==11 || $row->Nu_Estado==12){
            $dropdown = '<div class="dropdown">
              <button style="width: 100%;" id="btn-dropdown-pedido_estado-' . $row->ID_Pedido_Cabecera . '" class="btn btn-' . $arrEstadoPedidoTienda['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . $arrEstadoPedidoTienda['No_Estado'] . ' <span class="caret"></span></button>
              <ul class="dropdown-menu" style="width: 100%; position: sticky;">
                <li><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(1)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(1)['No_Estado'] . '\');">Pendiente</a></li>
                <li><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(2)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(2)['No_Estado'] . '\');">Confirmado</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(8)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(8)['No_Estado'] . '\');">Guia Generada</a></li>
                <li><a alt="Preparando" title="Preparando" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(3)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(3)['No_Estado'] . '\');">Preparando</a></li>
                <li><a alt="Recolectado" title="Recolectado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',9, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(9)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(9)['No_Estado'] . '\');">Recolectado</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',10, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(10)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(10)['No_Estado'] . '\');">Transito</a></li>
                <li><a alt="En Camino" title="En Camino" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(4)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(4)['No_Estado'] . '\');">En Camino</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',11, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(11)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(11)['No_Estado'] . '\');">1 Intento</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',12, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(12)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(12)['No_Estado'] . '\');">2 Intento</a></li>
                <li><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(5)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(5)['No_Estado'] . '\');">Entregado</a></li>
                <li><a alt="Devolución Pendiente" title="Devolución Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',13, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(13)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(13)['No_Estado'] . '\');">Devolución Pendiente</a></li>
                <li><a alt="Devuelto" title="Devuelto" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',14, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(14)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(14)['No_Estado'] . '\');">Devuelto</a></li>
                <li><a alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Pais . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(6)['No_Class_Estado'] . '\', \'' . $this->HelperModel->obtenerEstadoOrdenPedidoTienda(6)['No_Estado'] . '\');">Rechazado</a></li>
              </ul>
            </div>';
          /*
          } else {
            $dropdown = '<span class="label label-' . $arrEstadoPedidoTienda['No_Class_Estado'] . '">' . $arrEstadoPedidoTienda['No_Estado'] . '</span>';
          }
          */
        }
        $rows['No_Estado_Pedido'] = $dropdown;
        $rows['No_Estado_Pedido_Cliente_Excel'] = $arrEstadoPedidoTienda['No_Estado'];

        $rows['sAccionVer'] = ($row->Nu_Tipo_Venta_Generada == 1 ? '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-list-alt" aria-hidden="true"></i></button>' : '');
        $rows['sAccionEditar'] = '<button class="btn btn-xs btn-link" alt="Completar pedido" title="Completar pedido" href="javascript:void(0)" onclick="editarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-2x fa-edit" aria-hidden="true"></i></button>';

        $btn_generar_guia_99 = '';
        $sNombrePaqueteria = '';
        $sMensajeGuiaCliente = '';

        if($row->Nu_Forma_Pago_Dropshipping==1){//Contra entrega
          if($row->ID_Pais == 2) {//2=MEXICO
            $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="Generar Guia 99" title="Generar Guia 99" href="javascript:void(0)" onclick="generarGuia99(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-retweet"></i> Generar 99 Minutos</button>';
            $btn_generar_guia_99 .= '<br><button class="btn btn-xs btn-link" alt="Generar Guia Quiken" title="Generar Guia Quiken" href="javascript:void(0)" onclick="generarGuiaQuiken(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-retweet"></i> Generar Quiken</button>';
            $btn_generar_guia_99 .= '<br><button class="btn btn-xs btn-link" alt="Generar Guia Ecxlae" title="Generar Guia Ecxlae" href="javascript:void(0)" onclick="generarGuiaEcxlae(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-retweet"></i> Generar Ecxlae</button>';

            if ($row->Nu_Tipo_Guia_Api==1) {//1=99 minutos
              $sNombrePaqueteria = '<strong>Droshipper: 99 Minutos</strong><br>';
              
              if( !empty($row->Txt_Response_TrackingId_Api) ) {
                $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia 99" title="PDF Guia 99" href="javascript:void(0)" onclick="pdfGuia99(\'' . $row->Txt_Response_TrackingId_Api . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';  
                $btn_generar_guia_99 = '99 Minutos: ' . $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
              }
              
              //WhatsApp
              $sMensajeGuiaCliente = "Hola " . $row->No_Entidad . " espero se encuentre bien. 👋🏻\n";
              $sMensajeGuiaCliente .= "✅ Su pedido *#" . $row->ID_Pedido_Cabecera . "* ha sido confirmado y enviado con éxito.\n";
              $sMensajeGuiaCliente .= "🏍️ Este es su número de guía " . $row->Txt_Response_TrackingId_Api . "\n";
              $sMensajeGuiaCliente .= "📲 y su link de rastreo https://tracking.99minutos.com/search \n";
              $sMensajeGuiaCliente = urlencode($sMensajeGuiaCliente);
              $sMensajeGuiaCliente = '<br><a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular . '&text=' . $sMensajeGuiaCliente . '" target="_blank"><i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';
            } else if ($row->Nu_Tipo_Guia_Api==2) {//2=Quiken
              $sNombrePaqueteria = '<strong>Droshipper: Quiken</strong><br>';

              if( !empty($row->Txt_Response_TrackingId_Api) ) {
                $btn_generar_guia_99 = '<a class="btn btn-xs btn-link" alt="PDF Guia Quiken" title="PDF Guia Quiken" target="_blank" rel="noopener noreferrer" href="' . $row->Txt_Response_Guia_Api . '"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</a>';
                $btn_generar_guia_99 = 'Quiken: ' . $row->Txt_Response_TrackingId_Api . ' ' . $btn_generar_guia_99;
              }
            } else if ($row->Nu_Tipo_Guia_Api==3) {//3=Ecxlae
              $sNombrePaqueteria = '<strong>Droshipper: Ecxlae</strong><br>';

              if( !empty($row->Txt_Response_Guia_Api) ) {
                $btn_generar_guia_99 = '<button class="btn btn-xs btn-link" alt="PDF Guia Ecxlae" title="PDF Guia Ecxlae" href="javascript:void(0)" onclick="pdfGuiaEcxlae(\'' . $row->Txt_Response_Guia_Api . '\')"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>';  
                $btn_generar_guia_99 = 'Ecxlae: ' . $row->Txt_Response_Guia_Api . ' ' . $btn_generar_guia_99;
              }
            }
          }
        }

        $rows['btn_generar_guia_99'] = $sNombrePaqueteria . $btn_generar_guia_99 . $sMensajeGuiaCliente;

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
    $arrResponseModal = $this->CoordinadoModel->getReporteExcel($arrParams);
    if ( $arrResponseModal['sStatus']=='success' ) {
      $data = array();
      
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
        $arrEstadoPedidoTienda = $this->HelperModel->obtenerEstadoOrdenPedidoTienda($row->Nu_Estado);
        $rows['No_Estado'] = $arrEstadoPedidoTienda['No_Estado'];

        $precio_callcenter = 5;//peru
        if($row->ID_Pais == 2){
          $precio_callcenter = 30;//mexico
        }
        
        /* GANANCIA */
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
        $rows['ganancia'] = numberFormat($ganancia, 2, '.', ',');
        
        $rows['precio_4'] = numberFormat(($row->Ss_Precio * 0.04), 2, '.', ',');
        /* FIN DE GANANCIA */

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
  
	public function cambiarEstado($ID, $Nu_Estado, $ID_Pais){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->CallCenterModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($ID_Pais)));
	}
  
	public function eliminarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->CoordinadoModel->eliminarPedido($this->security->xss_clean($ID)));
	}
  
	public function generarVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->CoordinadoModel->generarVenta($this->input->post()));
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
      'sNombreCiudad' => $this->input->post('sNombreCiudad'),
      'ID_Pais' => $this->input->post('ID_Pais')
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

    $fileNamePDF = "Reporte_Coordinado_" . $Fe_Inicio . "_" . $Fe_Fin . ".pdf";

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
    
	public function sendReporteEXCEL($Fe_Inicio, $Fe_Fin, $ID_Tipo_Documento, $ID_Pedido_Cabecera, $Nu_Estado_Pedido, $iIdCliente, $sNombreCliente, $ID_Filtro_Empresa, $Nu_Estado_Pedido_Empresa, $sNombreCiudad, $ID_Pais){
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
    $ID_Pais = $this->security->xss_clean($ID_Pais);
    
		$fileNameExcel = "Reporte_Coordinado_" . $Fe_Inicio . "_" . $Fe_Fin . ".xls";
		
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->getActiveSheet()->setTitle('Pedidos Coordinado');
      
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
    ->setCellValue('C2', 'Informe de Pedidos de Coordinado')
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("20");//Estado
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");//Ganancia
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20");//Precio 4%

    $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($BStyle_top);
    $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($BStyle_bottom);
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
    $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->getFont()->setBold(true);
    
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
    ->setCellValue('K5', 'Estado')
    ->setCellValue('L5', 'Ganancia')
    ->setCellValue('M5', 'Precio 4')
    ;
    
    $objPHPExcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($style_align_center);
    
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
      'sNombreCiudad' => $sNombreCiudad,
      'ID_Pais' => $ID_Pais
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
        ->setCellValue('K' . $fila, $row->No_Estado)
        ->setCellValue('L' . $fila, $row->ganancia)
        ->setCellValue('M' . $fila, $row->precio_4)
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
      $response = $this->CoordinadoModel->actualizarPedido(array('ID_Pedido_Cabecera' => $arrCabeceraPedido['EID_Pedido_Cabecera']), $data);
      echo json_encode($response);
      exit();
    } else {
      $response = $this->CoordinadoModel->agregarPedido($arrCabeceraPedido, $arrPedidoDetalle);
      echo json_encode($response);
      exit();
    }
	}
	
  public function ajax_edit($ID){
    echo json_encode($this->CoordinadoModel->get_by_id($this->security->xss_clean($ID)));
  }
	
	public function cambiarEstadoPedidoEmpresa($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->CoordinadoModel->cambiarEstadoPedidoEmpresa($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
    
	public function actualizarPrecioDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataDelivery = $_POST['arrDataDelivery'];
    $data = array(
      'Ss_Precio_Delivery' => $arrDataDelivery['precio_delivery']
    );
    $response = $this->CoordinadoModel->actualizarPrecioDelivery(array('ID_Pedido_Cabecera' => $arrDataDelivery['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
    
	public function actualizarNotaPedido(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataNotaPedido = $_POST['arrDataNotaPedido'];
    $data = array(
      'Txt_Glosa' => $arrDataNotaPedido['nota']
    );
    $response = $this->CoordinadoModel->actualizarNotaPedido(array('ID_Pedido_Cabecera' => $arrDataNotaPedido['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
    
	public function asignarUsuarioDelivery(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataNotaPedido = $_POST['arrDataNotaPedido'];
    $data = array(
      'ID_Usuario_Asignar_Delivery' => $arrDataNotaPedido['id_usuario_delivery']
    );
    $response = $this->CoordinadoModel->asignarUsuarioDelivery(array('ID_Pedido_Cabecera' => $arrDataNotaPedido['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
    
	public function actualizarPrecioDeliverySaldoAFavor(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

    $arrDataDelivery = $_POST['arrDataDelivery'];
    $data = array(
      'Ss_Saldo_A_Favor_Delivery_Interno' => $arrDataDelivery['precio_delivery']
    );
    $response = $this->CoordinadoModel->actualizarPrecioDeliverySaldoAFavor(array('ID_Pedido_Cabecera' => $arrDataDelivery['id_pedido_cabecera']), $data);
    echo json_encode($response);
    exit();
	}
	
	public function generarGuia99($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    echo json_encode($this->CoordinadoModel->generarGuia99($this->security->xss_clean($ID)));
	}

	public function sendPDF($ID){
    //generar token para 99 minutos
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://delivery.99minutos.com/api/v3/oauth/token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'client_id' => '7a581c2d-c641-490f-9730-2a2e7eeb2d0c',
        'client_secret' => 'RsFGHMclFEMzJ9qvzUVySkq0X5'
      ]),
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "content-type: application/json"
      ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      //echo "cURL Error #:" . $err;
      $response = array(
        'status' => 'warning',
        'message' => 'error de generar token ' . $err,
      );
      
      echo json_encode($response);
      exit();
    } else {
      $leer_respuesta = json_decode($response, true);
      $access_token = $leer_respuesta['access_token'];

      $curl = curl_init();

      curl_setopt_array($curl, [
        CURLOPT_URL => "https://delivery.99minutos.com/api/v3/documents/guides",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
          'guides' => [
            [
              'size' => 'zebra',
              'identifier' => $ID
            ]
          ]
        ]),
        CURLOPT_HTTPHEADER => [
          "accept: application/json",
          "authorization: Bearer " . $access_token,
          "content-type: application/json"
        ],
      ]);

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        //echo "cURL Error #:" . $err;
        $response = array(
          'status' => 'warning',
          'message' => $response,
        );
        echo json_encode($response);
        exit();
      } else {
        $leer_respuesta = json_decode($response, true);
        /*
        echo "<pre>";
        var_dump($leer_respuesta['data'][0]['pdf']);
        echo "</pre>";
        */
        if(isset($leer_respuesta['data'][0]['pdf']) && !empty($leer_respuesta['data'][0]['pdf'])){
          $response = array(
            'status' => 'success'
          );
          
          $b64 = $leer_respuesta['data'][0]['pdf'];
          # Decode the Base64 string, making sure that it contains only valid characters
          $bin = base64_decode($b64, true);

          # Perform a basic validation to make sure that the result is a valid PDF file
          # Be aware! The magic number (file signature) is not 100% reliable solution to validate PDF files
          # Moreover, if you get Base64 from an untrusted source, you must sanitize the PDF contents
          if (strpos($bin, '%PDF') !== 0) {
            $response = array(
              'status' => 'warning',
              'message' => 'error al generar PDF',
            );
            echo json_encode($response);
            exit();
          }

          # Write the PDF contents to a local file
          file_put_contents('file.pdf', $bin);
          
          $pdfname='file.pdf';

          header('Content-Type: application/octet-stream');
          header('Content-type: application/pdf');
          header("Content-disposition: attachment; filename=".$pdfname);
          header('Content-Transfer-Encoding: binary');
          header('Accept-Ranges: bytes');
          @readfile('file.pdf');
          exit;
        } else {
          $response = array(
            'status' => 'warning',
            'message' => $response,
          );
        }
        echo json_encode($response);
        exit();
      }
    }
	}
  
	public function sendPDFEcxlae($ID){
    //generar token para 99 minutos
    $curl = curl_init();
    
		//$url_global = "https://api-dev.tiui.app/api/";
		$url_global = "https://api.tiui.app/api/";

		$url = $url_global . "AccountTiuiAmigo/login";
		$user_guia = "soporte.ecxpressmexico@gmail.com";
		$password_guia = "100ECXLAE44";

    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'userName' => $user_guia,
        'password' => $password_guia
      ]),
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "content-type: application/json"
      ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if (!empty($response['messageError'])) {
      $response = array(
        'status' => 'warning',
        'message' => 'error de generar token ' . $response['messageError'],
      );
      
      echo json_encode($response);
      exit();
    } else {
      $leer_respuesta = json_decode($response, true);
      $access_token = $leer_respuesta['accessToken'];

      $curl = curl_init();

      $url = $url_global . "guia/guiaReport/". $ID;

      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      ]);

      $response = curl_exec($curl);
/*
      echo "<pre>";
      var_dump($response);
      echo "</pre>";
      */

      curl_close($curl);
      
      $bin = $response;
      if (strpos($bin, '%PDF') !== 0) {
        $response = array(
          'status' => 'warning',
          'message' => 'error al generar PDF',
        );
        echo json_encode($response);
        exit();
      }

      # Write the PDF contents to a local file
      file_put_contents('file.pdf', $bin);
      
      $pdfname='file.pdf';

      header('Content-Type: application/octet-stream');
      header('Content-type: application/pdf');
      header("Content-disposition: attachment; filename=".$pdfname);
      header('Content-Transfer-Encoding: binary');
      header('Accept-Ranges: bytes');
      @readfile('file.pdf');
      exit;
    }
	}
}
