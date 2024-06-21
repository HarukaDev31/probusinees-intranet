<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PedidosPagados extends CI_Controller
{
    private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio=1;
    private $upload_path = '../assets/images/clientes/';
    private $file_path = '../assets/images/logos/';
    private $logo_cliente_path = '../assets/images/logos/';
    private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('AgenteCompra/PedidosPagadosModel');
        $this->load->model('HelperImportacionModel');
        $this->load->model('NotificacionModel');
        $this->load->model('MenuModel');

        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }
    }

    public function listar($sCorrelativoCotizacion = '', $ID_Pedido_Cabecera = '')
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        if (isset($this->session->userdata['usuario'])) {
            $this->load->view('header_v2', array("js_pedidos_pagados" => true));
            $this->load->view('AgenteCompra/PedidosPagadosView', array(
                'sCorrelativoCotizacion' => $sCorrelativoCotizacion,
                'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
            ));
            $this->load->view('footer_v2', array("js_pedidos_pagados" => true));
        }
    }

    public function ajax_list()
    {
        $arrData = $this->PedidosPagadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();

            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0, 3)) . str_pad($row->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            //$rows[] = $row->No_Pais;
            $rows[] = $sCorrelativoCotizacion;
            $rows[] = ToDateBD($row->Fe_Emision_OC_Aprobada);

            // if($this->user->Nu_Tipo_Privilegio_Acceso!=2) {
            //     $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto;
            // }

            // //asignar personal de china desde perú
            // $btn_asignar_personal_china = '';
            // if($this->user->Nu_Tipo_Privilegio_Acceso==1){//1=probusiness
            //     //$btn_asignar_personal_china = '<button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="asignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><i class="far fa-user fa-2x" aria-hidden="true"></i></button>';
            //     if(!empty($row->ID_Usuario_Interno_Jefe_China)){
            //         $btn_asignar_personal_china = '<span class="badge bg-secondary">' . $row->No_Usuario_Jefe . '</span>';
            //         //$btn_asignar_personal_china .= '<br><button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="removerAsignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->ID_Usuario_Interno_Jefe_China . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            //     }
            // }

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 5 && $this->user->Nu_Tipo_Privilegio_Acceso != 2) {
                // $rows[] = '<span class="badge bg-secondary">' . $row->No_Usuario . '</span>';
                //$rows[] = $btn_asignar_personal_china;
            } else if ($this->user->Nu_Tipo_Privilegio_Acceso == 5) {
                // $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad;

                $sNombreExportador = '';
                if ($row->Nu_Tipo_Exportador == 1) {
                    $sNombreExportador = 'INTERNATIONAL PRO TRADING CO., LIMITED';
                } else if ($row->Nu_Tipo_Exportador == 2) {
                    $sNombreExportador = 'CHRIS FACTORY LIMITED';
                }

                $iIdTareaPedido = 0; //ninguno
                if ($row->Nu_Tipo_Servicio == 1) { //trading
                    $iIdTareaPedido = 18;
                } else if ($row->Nu_Tipo_Servicio == 2) { //consolida trading
                    $iIdTareaPedido = 11;
                }

                //verificar si completo o no
                $btn_completar_verificacion_oc = '';
                //$arrResponsePaso1 = $this->PedidosPagadosModel->verificarTarea($iIdTareaPedido, $row->ID_Pedido_Cabecera);
                //if(is_object($arrResponsePaso1) && $arrResponsePaso1->Nu_Estado_Proceso==0)
                //$btn_completar_verificacion_oc = '<br><button class="btn btn-primary" alt="Completado" title="Completado" href="javascript:void(0)"  onclick="completarVerificacionOC(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')">Verificar</button>';

                // $rows[] = $sNombreExportador . $btn_completar_verificacion_oc;
            }

            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoServicioArray($row->Nu_Tipo_Servicio);
            $dropdown_estado = '<div class="dropdown">';
            $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado .= '<span class="caret"></span></button>';
            $dropdown_estado .= '<ul class="dropdown-menu">';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Trading" title="Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Usuario_Interno_Jefe_China . '\');">Trading</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="C. Trading" title="C. Trading" href="javascript:void(0)" onclick="cambiarTipoServicio(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Usuario_Interno_Jefe_China . '\');">C. Trading</a></li>';
            $dropdown_estado .= '</ul>';
            $dropdown_estado .= '</div>';

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 1) { //no tiene acceso a cambiar status de Perú
                $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }

            $btn_comision_trading = '';
            if ($this->user->Nu_Tipo_Privilegio_Acceso != 2) {
                $btn_comision_trading = '<button class="btn btn-link" alt="Agregar comisión Trading" title="Agregar comisión Trading" href="javascript:void(0)" onclick="agregarComisionTrading(\'' . $row->ID_Pedido_Cabecera . '\')">Comisión</button>';
                if ($row->Ss_Comision_Interna_Trading > 0) {
                    $btn_comision_trading = "<br>" . '$ ' . $row->Ss_Comision_Interna_Trading;
                }

            }

            $rows[] = $dropdown_estado . $btn_comision_trading;

            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerIncoterms($row->Nu_Tipo_Incoterms);
            $dropdown_estado = '<div class="dropdown">';
            $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado .= '<span class="caret"></span></button>';
            $dropdown_estado .= '<ul class="dropdown-menu">';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="EXW" title="EXW" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">EXW</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="FOB" title="FOB" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">FCA</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="FOB" title="FOB" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">FOB</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="CIF" title="CIF" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">CFR</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="CIF" title="CIF" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">CIF</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="DDP" title="DDP" href="javascript:void(0)" onclick="cambiarIncoterms(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">DAP</a></li>';
            $dropdown_estado .= '</ul>';
            $dropdown_estado .= '</div>';

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 1) { //no tiene acceso a cambiar status de Perú
                $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }

            $rows[] = $dropdown_estado; //incoterms

            if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { //no tiene acceso a cambiar status de Perú
                $excel_orden_compra = '<button class="btn" alt="Orden Compra Trading" title="Orden Compra Trading" href="javascript:void(0)" onclick="generarAgenteCompra(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2"> Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                if ($row->Nu_Tipo_Servicio == 2) {
                    $excel_orden_compra = '<button class="btn" alt="Orden Compra C. Trading" title="Orden Compra C. Trading" href="javascript:void(0)" onclick="generarConsolidaTrading(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2">C. Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                }
                $rows[] = $excel_orden_compra;
            }

            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
            $dropdown_estado = '<div class="dropdown">';
            $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado .= '<span class="caret"></span></button>';
            $dropdown_estado .= '<ul class="dropdown-menu">';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Volver a Garantizado</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 30%" title="Pago 30%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago 30%</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago 70%" title="Pago 70%" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',7, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago 70%</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pago Servicio" title="Pago Servicio" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',9, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Pago Servicio</a></li>';
            $dropdown_estado .= '</ul>';
            $dropdown_estado .= '</div>';

            if ($this->user->Nu_Tipo_Privilegio_Acceso == 2) { //no tiene acceso a cambiar status de Perú
                $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 5) {
                $rows[] = $dropdown_estado;
            }

            // if($this->user->Nu_Tipo_Privilegio_Acceso==1){
            //     $rows[] = '<button class="btn btn-xs btn-link" alt="Recepcion de carga" title="Recepcion de carga" href="javascript:void(0)"  onclick="recepcionCarga(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            // }

            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
            $dropdown_estado = '<div class="dropdown">';
            $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado .= '<span class="caret"></span></button>';
            $dropdown_estado .= '<ul class="dropdown-menu">';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Producción" title="Producción" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Producción</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Inspección" title="Inspección" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Inspección</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',6, \'' . $row->ID_Agente_Compra_Correlativo . '\', \'' . $sCorrelativoCotizacion . '\');">Entregado</a></li>';
            $dropdown_estado .= '</ul>';
            $dropdown_estado .= '</div>';

            if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { //no tiene acceso a cambiar status de China
                $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 5 && $this->user->Nu_Tipo_Privilegio_Acceso != 1) {
                $rows[] = $dropdown_estado;
            }
//china

            // if($this->user->Nu_Tipo_Privilegio_Acceso!=5 && $this->user->Nu_Tipo_Privilegio_Acceso!=1) {
            //     //Negociar con proveedores
            //     $rows[] = '<button class="btn btn-xs btn-link" alt="Negociar con proveedor" title="Negociar con proveedor" href="javascript:void(0)"  onclick="coordinarPagosProveedor(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-handshake fa-2x" aria-hidden="true"></i></button>';

            //     //Booking
            //     $rows[] = '<button class="btn btn-xs btn-link" alt="Booking" title="Booking" href="javascript:void(0)"  onclick="booking(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-box fa-2x" aria-hidden="true"></i></button>';

            //     //Recepcion de carga
            //     $rows[] = '<button class="btn btn-xs btn-link" alt="Recepcion de carga" title="Recepcion de carga" href="javascript:void(0)"  onclick="recepcionCarga(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            // }

            if ($this->user->Nu_Tipo_Privilegio_Acceso == 5 && $this->user->Nu_Tipo_Privilegio_Acceso != 1) {
                //Pagos
                $rows[] = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="pagarProveedores(\'' . $row->ID_Pedido_Cabecera . '\', 1)"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';
                //$rows[] = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';

                //Reserva de Booking
                /*
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 20;
                } else if ($row->Nu_Tipo_Servicio==2) {//consolida trading
                $iIdTareaPedido = 14;
                }

                $btn_reserva_booking = '<button class="btn btn-xs btn-link" alt="Booking" title="Booking" href="javascript:void(0)"  onclick="bookingConsolidado(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-box fa-2x" aria-hidden="true"></i></button>';
                if($row->Nu_Tipo_Servicio==1)
                $btn_reserva_booking = '';
                $rows[] = $btn_reserva_booking;

                //Inspección
                $btn_inpseccion = '<button class="btn btn-xs btn-link" alt="Inspeccion" title="Inspeccion" href="javascript:void(0)"  onclick="bookingInspeccion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\', \'' . $row->ID_Usuario_Interno_China . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-search fa-2x" aria-hidden="true"></i></button>';
                 */

                /*
                $btn_reserva_booking = '';
                $btn_costos_origen = '';
                $btn_docs_exportacion = '';
                $btn_despacho_shipper = '';
                $btn_revision_bl = '';
                $btn_entrega_docs_cliente = '';
                $btn_pagos_logisticos = '';
                if($row->Nu_Tipo_Servicio==1) {
                $btn_reserva_booking = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Booking" title="Booking" href="javascript:void(0)"  onclick="bookingTrading(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-ship fa-2x" aria-hidden="true"></i></button>';

                //Costos de Origen
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 23;
                }
                $btn_costos_origen = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Costos Origen" title="Costos Origen" href="javascript:void(0)"  onclick="costosOrigenTradingChina(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';

                //Docs Exportacion
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 24;
                }
                $btn_docs_exportacion = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Docs Exportacion" title="Docs Exportacion" href="javascript:void(0)"  onclick="docsExportacion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-file fa-2x" aria-hidden="true"></i></button>';

                //Despacho al Shipper
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 25;
                }
                $btn_despacho_shipper = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Despacho al Shipper" title="Despacho al Shipper" href="javascript:void(0)"  onclick="despachoShipper(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-check fa-2x" aria-hidden="true"></i></button>';

                //Despacho al Shipper
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 26;
                }
                $btn_revision_bl = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="revisionBL(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-warehouse fa-2x" aria-hidden="true"></i></button>';

                //Despacho al Shipper
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 27;
                }
                $btn_entrega_docs_cliente = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Entrega de Docs Cliente" title="Entrega de Docs Cliente" href="javascript:void(0)"  onclick="entregaDocsCliente(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-user fa-2x" aria-hidden="true"></i></button>';

                //Despacho al Shipper
                $iIdTareaPedido = 0;//ninguno
                if ($row->Nu_Tipo_Servicio==1){//trading
                $iIdTareaPedido = 28;
                }
                $btn_pagos_logisticos = '<br>' . '<button type="button" class="btn btn-xs btn-link" alt="Pagos Logísticos" title="Pagos Logísticos" href="javascript:void(0)"  onclick="pagosLogisticos(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-truck fa-2x" aria-hidden="true"></i></button>';
                }

                $rows[] = $btn_inpseccion . $btn_reserva_booking . $btn_costos_origen . $btn_docs_exportacion . $btn_despacho_shipper . $btn_revision_bl . $btn_entrega_docs_cliente . $btn_pagos_logisticos;
                 */

                //Pagos 2
                $rows[] = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="pagarProveedores(\'' . $row->ID_Pedido_Cabecera . '\', 2)"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button>';
            }

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 5 && $this->user->Nu_Tipo_Privilegio_Acceso != 1) {
                //inspeccion
                $btn_inspeccion = '';
                if ($row->Nu_Estado_China == 5 || $row->Nu_Estado_China == 6) {
                    $btn_inspeccion = '<button class="btn btn-xs btn-link" alt="Subir inspección" title="Subir inspección" href="javascript:void(0)"  onclick="subirInspeccion(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-search fa-2x" aria-hidden="true"></i></button>';
                }

                $rows[] = $btn_inspeccion;

                //Invoice proveedor
                $rows[] = '<button class="btn btn-xs btn-link" alt="Invoice proveedor" title="Invoice proveedor" href="javascript:void(0)"  onclick="invoiceProveedor(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fa fa-file-excel fa-2x" aria-hidden="true"></i></button>';

                //Despacho
                $btn_despacho = '<button class="btn btn-xs btn-link" alt="Despacho" title="Despacho" href="javascript:void(0)"  onclick="despacho(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-truck fa-2x" aria-hidden="true"></i></button>';

                $rows[] = $btn_despacho . '<br>' . (!empty($row->Fe_Entrega_Shipper_Forwarder) ? ToDateBD($row->Fe_Entrega_Shipper_Forwarder) : '');
            }
            $rows[] = "<button class='btn btn-xs btn-link' alt='Editar' title='Editar' href='javascript:void(0)'
			onclick='getOrderProgress(" . $row->ID_Pedido_Cabecera . ")'><i class='fas fa-edit fa-2x' aria-hidden='true'></i></button>";

            // if($this->user->Nu_Tipo_Privilegio_Acceso==5 && $this->user->Nu_Tipo_Privilegio_Acceso!=1) {
            //     //entregado
            //     /*
            //     $btn_entregado = '';
            //     if($row->Nu_Estado_China==6)
            //         $btn_entregado = '<button class="btn btn-xs btn-link" alt="Subir documento" title="Subir documento" href="javascript:void(0)" onclick="documentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-folder fa-2x" aria-hidden="true"></i></button>';

            //     if(!empty($row->Txt_Url_Archivo_Documento_Entrega)) {
            //         $btn_entregado .= '<br><button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarDocumentoEntregado(\'' . $row->ID_Pedido_Cabecera . '\')">Descargar</button>';
            //     }

            //     if(!empty($row->Txt_Url_Archivo_Documento_Entrega)) {
            //         $btn_entregado .= '<br><button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarDocumentoDetalle(\'' . $row->ID_Pedido_Cabecera . '\')">Descargar</button>';
            //     }

            //     $rows[] = $btn_entregado;

            //     //Supervisar llenado de contenedor
            //     $btn_supervisar = '<button class="btn btn-xs btn-link" alt="Supervisar" title="Supervisar" href="javascript:void(0)"  onclick="supervisarContenedor(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-truck fa-2x" aria-hidden="true"></i></button>';

            //     $rows[] = $btn_supervisar;
            //     */
            // }

            //jalar en que tarea se quedo
            // $span_estado_proceso = '<span class="badge bg-secondary">No ejecuto tarea</span>';
            // $arrResponseTarea = $this->PedidosPagadosModel->listadoTareaPorPedido($row->ID_Pedido_Cabecera);
            // if(is_object($arrResponseTarea)){
            //     //$arrResponseStatusTarea = $this->PedidosPagadosModel->verificarTarea($row->ID_Pedido_Cabecera,$arrResponseTarea->ID_Proceso);
            //     //$span_status_tarea = ($arrResponseStatusTarea->Nu_Estado_Proceso == 1 ? 'success' : 'danger');
            //     $span_status_tarea = 'danger';
            //     $span_estado_proceso = '<span class="badge bg-' . $span_status_tarea . '">' . $arrResponseTarea->No_Proceso . '</span>';
            // }
            // $rows[] = $span_estado_proceso;//status

            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function ajax_edit($ID)
    {
        $arrReponse = $this->PedidosPagadosModel->get_by_id($this->security->xss_clean($ID));
        $sCorrelativoCotizacion = '';
        foreach ($arrReponse as $row) {
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0, 3)) . str_pad($row->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            $row->sCorrelativoCotizacion = $sCorrelativoCotizacion;
        }
        echo json_encode($arrReponse);
    }

    public function cambiarEstado($ID, $Nu_Estado, $sCorrelativo)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
    }

    public function crudPedidoGrupal()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        $data = array(
        );
        echo json_encode($this->PedidosPagadosModel->actualizarPedido(
            array(
                'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
            ),
            $data,
            $this->input->post('addProducto')
        )
        );
    }

    public function addPagoProveedor()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoProveedor($this->input->post(), $_FILES));
    }

    public function downloadImage($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->getDownloadImage($this->security->xss_clean($id));
        //array_debug($objPedido);

        $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
        $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

        //$file="assets/img/arturo.jpeg";
        if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
            die('file not found');
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
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

    //generar cotización PDF para pedido de cliente
    public function generarExcelOrderTracking($ID)
    {
        $data = $this->PedidosPagadosModel->get_by_id($this->security->xss_clean($ID));
        //array_debug($data);

        if (!empty($data)) {
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0, 3)) . str_pad($data[0]->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            //GENERAR EXCEL
            $this->load->library('Excel');

            $fileNameExcel = "Orden_Tracking_" . $sCorrelativoCotizacion . ".xls";

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->getActiveSheet()->setTitle('Orden Tracking Cot. ' . $sCorrelativoCotizacion);

            $hoja_activa = 0;

            $BStyle_top_general = array(
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $BStyle_left_general = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $BStyle_right_general = array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $BStyle_bottom_general = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $BStyle_top = array(
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );

            $BStyle_left = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );

            $BStyle_right = array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );

            $BStyle_bottom = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );

            $style_align_center = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $style_align_right = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
            );

            $style_align_left = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
            );

            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'FFFFFF'),
                    ),
                ),
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

            //Title
            $fila = 3;
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_bottom);
            $objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_left);

            $fila = 2;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('B' . ($fila + 1) . ':M' . ($fila + 2));

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('B3', 'ORDEN TRACKING');
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(16);

            $objPHPExcel->getActiveSheet()
                ->getStyle('B3')
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FFFF00'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()->getStyle('B3:M3')->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($BStyle_left_general);
            $objPHPExcel->getActiveSheet()->getStyle('M3')->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($BStyle_left_general);
            $objPHPExcel->getActiveSheet()->getStyle('M4')->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('B4:M4')->applyFromArray($BStyle_bottom_general);

            $fila = 6;
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':M' . $fila)->applyFromArray($BStyle_bottom);
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
            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':M' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($style_align_center);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("30"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("30"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("20"); //NRO
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("20"); //NRO

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('B' . $fila, 'PRODUCT PHOTO')
                ->setCellValue('C' . $fila, 'PRODUCT NAME')
                ->setCellValue('D' . $fila, 'ITEM No')
                ->setCellValue('E' . $fila, 'QTY')
                ->setCellValue('F' . $fila, 'PRICE')
                ->setCellValue('G' . $fila, 'AMOUNT')
                ->setCellValue('H' . $fila, 'DEPOSIT #1')
                ->setCellValue('I' . $fila, 'BALANCE')
                ->setCellValue('J' . $fila, 'DEPOSIT #2')
                ->setCellValue('K' . $fila, 'DELIVERY DATE')
                ->setCellValue('L' . $fila, 'SUPPLIER')
                ->setCellValue('M' . $fila, 'PHONE NUMBER')
            ;

            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_top_general);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->getActiveSheet()
                ->getStyle('B' . $fila . ':' . 'M' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'E7E7E7'),
                        ),
                    )
                );

            $fila = 7;

            $fSumGeneralAmount = 0;
            $fSumGeneralPago1 = 0;
            $fSumGeneralBalance = 0;
            $fSumGeneralPago2 = 0;
            $iCounter = 1;
            foreach ($data as $row) {
                if (!empty($row->Txt_Url_Imagen_Producto)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();

                    $objDrawing->setName($row->Txt_Producto);

                    //pruebas localhost
                    //$objDrawing->setPath('assets/img/unicpn.png');

                    //cloud
                    $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                    $row->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $row->Txt_Url_Imagen_Producto);
                    if (file_exists($row->Txt_Url_Imagen_Producto)) {
                        $objDrawing->setPath($row->Txt_Url_Imagen_Producto);
                        $objDrawing->setWidthAndHeight(148, 500);
                        $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(130);
                        $objDrawing->setResizeProportional(true);

                        $objDrawing->setCoordinates('B' . $fila);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                } else {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('B' . $fila, '');
                }

                $fAmount = ($row->Qt_Producto * $row->Ss_Precio);
                $fBalance = ($fAmount - $row->Ss_Pago_1_Proveedor);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('C' . $fila, $row->Txt_Producto)
                    ->setCellValue('D' . $fila, '1')
                    ->setCellValue('E' . $fila, $row->Qt_Producto)
                    ->setCellValue('F' . $fila, $row->Ss_Precio)
                    ->setCellValue('G' . $fila, $fAmount)
                    ->setCellValue('H' . $fila, $row->Ss_Pago_1_Proveedor)
                    ->setCellValue('I' . $fila, $fBalance)
                    ->setCellValue('J' . $fila, $row->Ss_Pago_2_Proveedor)
                    ->setCellValue('K' . $fila, $row->Nu_Dias_Delivery)
                ;

                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('K' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_top_general);
                $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_left_general);
                $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_right_general);
                $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($BStyle_bottom_general);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('H' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'D6E3BC'),
                            ),
                        )
                    );

                $objPHPExcel->getActiveSheet()
                    ->getStyle('I' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00'),
                            ),
                        )
                    );

                $objPHPExcel->getActiveSheet()
                    ->getStyle('J' . $fila)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'F79646'),
                            ),
                        )
                    );

                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getNumberFormat()->setFormatCode('#,##0.00');

                $fSumGeneralAmount += $fAmount;
                $fSumGeneralPago1 += $row->Ss_Pago_1_Proveedor;
                $fSumGeneralBalance += $fBalance;
                $fSumGeneralPago2 += $row->Ss_Pago_2_Proveedor;

                $iCounter++;
                $fila++;
            } // /. foreach data

            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila . ':J' . $fila)->applyFromArray($style_align_right);

            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila)->applyFromArray($BStyle_left_general);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('H' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->applyFromArray($BStyle_right_general);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':J' . $fila)->applyFromArray($BStyle_bottom_general);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, 'TOTAL')
                ->setCellValue('G' . $fila, numberFormat($fSumGeneralAmount, 2, '.', ','))
                ->setCellValue('H' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','))
                ->setCellValue('I' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','))
                ->setCellValue('J' . $fila, numberFormat($fSumGeneralPago1, 2, '.', ','));

            //SUMAR I y M
            $objPHPExcel->getActiveSheet()
                ->getStyle('B' . $fila . ':' . 'J' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'F2F2F2'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()->getStyle('B' . $fila . ':J' . $fila)->getFont()->setBold(true);

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

    public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativo)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
    }

    public function addInspeccionProveedor()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addInspeccionProveedor($this->input->post(), $_FILES));
    }

    public function ajax_edit_inspeccion($ID)
    {
        echo json_encode($this->PedidosPagadosModel->get_by_id_inspeccion($this->security->xss_clean($ID)));
    }

    public function addFileProveedor()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addFileProveedor($this->input->post(), $_FILES));
    }

    public function descargarDocumentoEntregado($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarDocumentoEntregado($this->security->xss_clean($id));
        //array_debug($objPedido);

        $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
        $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

        //$file="assets/img/arturo.jpeg";
        if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
            die('file not found');
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
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

    public function descargarDocumentoDetalle($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarDocumentoDetalle($this->security->xss_clean($id));
        //array_debug($objPedido);

        $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
        $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

        //$file="assets/img/arturo.jpeg";
        if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
            die('file not found');
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
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

    public function addPagoCliente30()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoCliente30($this->input->post(), $_FILES));
    }

    public function descargarPago30($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPago30($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 30%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function addPagoCliente100()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoCliente100($this->input->post(), $_FILES));
    }

    public function descargarPago100($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPago100($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function cambiarTipoServicio($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->cambiarTipoServicio($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($ID_Usuario_Interno_Empresa_China)));
    }

    public function addPagoClienteServicio()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoClienteServicio($this->input->post(), $_FILES));
    }

    public function descargarPagoServicio($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoServicio($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function elminarItemProveedor($id, $correlativo, $name_item)
    {
        //echo "hola";
        echo json_encode($this->PedidosPagadosModel->elminarItemProveedor($this->security->xss_clean($id), $this->security->xss_clean($correlativo), $this->security->xss_clean($name_item)));
    }

    public function cambiarIncoterms($ID, $Nu_Estado, $sCorrelativo)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->cambiarIncoterms($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
    }

    public function cambiarTransporte($ID, $Nu_Estado, $sCorrelativo)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->cambiarTransporte($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativo)));
    }

    public function agregarComisionTrading()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        $arrData = $_POST['arrData'];
        $data = array(
            'Ss_Comision_Interna_Trading' => $arrData['precio_comision_trading'],
        );
        $response = $this->PedidosPagadosModel->agregarComisionTrading(array('ID_Pedido_Cabecera' => $arrData['id_pedido_cabecera']), $data);
        echo json_encode($response);
        exit();
    }

    public function addPagoFlete()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoFlete($this->input->post(), $_FILES));
    }

    public function descargarPagoFlete($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoFlete($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function addPagoCostosOrigen()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoCostosOrigen($this->input->post(), $_FILES));
    }

    public function descargarPagoCostosOrigen($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoCostosOrigen($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function addPagoFta()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addPagoFta($this->input->post(), $_FILES));
    }

    public function descargarPagoFTA($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoFTA($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function addOtrosCuadrilla()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addOtrosCuadrilla($this->input->post(), $_FILES));
    }

    public function descargarPagoCuadrilla($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoCuadrilla($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function addOtrosCostos()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addOtrosCostos($this->input->post(), $_FILES));
    }

    public function descargarPagoOtrosCostos($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarPagoOtrosCostos($this->security->xss_clean($id));
        if (is_object($objPedido)) {
            if (!empty($objPedido->Txt_Url_Imagen_Producto)) {
                //array_debug($objPedido);

                $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
                $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

                //$file="assets/img/arturo.jpeg";
                if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
                    die('file not found');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    ob_clean();
                    flush();
                    readfile($objPedido->Txt_Url_Imagen_Producto);
                    exit;
                }
            } else {
                die('Primero subir voucher de pago del 100%');
            }
        } else {
            die('No existe registro');
        }
    }

    public function crudProveedor()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            /*
            $data = array(
            'No_Contacto' => $this->input->post('proveedor-No_Contacto'),
            'No_Titular_Cuenta_Bancaria' => $this->input->post('proveedor-No_Titular_Cuenta_Bancaria'),
            //'No_Wechat' => $this->input->post('proveedor-No_Wechat'),
            'No_Rubro' => $this->input->post('proveedor-No_Rubro'),
            //'No_Cuenta_Bancaria' => $this->input->post('proveedor-No_Cuenta_Bancaria'),
            'Ss_Pago_Importe_1' => $this->input->post('proveedor-Ss_Pago_Importe_1'),
            );
             */
            $data = array(
                'No_Rubro' => $this->input->post('proveedor-No_Rubro'),
                'Ss_Pago_Importe_1' => $this->input->post('proveedor-Ss_Pago_Importe_1'),
                'No_Cuenta_Bancaria' => $this->input->post('proveedor-No_Cuenta_Bancaria'),
            );
            $data_entidad = array(
                'No_Contacto' => $this->input->post('proveedor-No_Contacto'),
                'No_Titular_Cuenta_Bancaria' => $this->input->post('proveedor-No_Titular_Cuenta_Bancaria'),
                'Nu_Tipo_Pay_Proveedor_China' => $this->input->post('proveedor-Nu_Tipo_Pay_Proveedor_China'),
                'No_Banco_China' => $this->input->post('proveedor-No_Banco_China'),
            );
            echo json_encode($this->PedidosPagadosModel->actualizarProveedor(
                array('ID_Entidad' => $this->input->post('proveedor-ID_Entidad')),
                $data,
                array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('proveedor-ID_Pedido_Detalle_Producto_Proveedor')),
                $data_entidad
            )
            );
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function getPedidoProveedor($ID)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->getPedidoProveedor($this->security->xss_clean($ID)));
    }

    public function reservaBooking()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Qt_Caja_Total_Booking' => $this->input->post('booking-Qt_Caja_Total_Booking'),
                'Qt_Cbm_Total_Booking' => $this->input->post('booking-Qt_Cbm_Total_Booking'),
                'Qt_Peso_Total_Booking' => $this->input->post('booking-Qt_Peso_Total_Booking'),
            );
            echo json_encode($this->PedidosPagadosModel->reservaBooking(array('ID_Pedido_Cabecera' => $this->input->post('booking-ID_Pedido_Cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function getBooking($ID)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->getBooking($this->security->xss_clean($ID)));
    }

    public function actualizarRecepcionCargaItemProveedor()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Qt_Producto_Caja_Final_Verificada' => $this->input->post('cantidad'),
                'Nu_Estado_Recepcion_Carga_Proveedor_Item' => $this->input->post('estado'),
            );
            echo json_encode($this->PedidosPagadosModel->actualizarRecepcionCargaItemProveedor(array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('id')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function actualizarRecepcionCargaProveedor()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Txt_Nota_Recepcion_Carga_Proveedor' => $this->input->post('nota'),
            );
            echo json_encode($this->PedidosPagadosModel->actualizarRecepcionCargaProveedor(array('ID_Pedido_Detalle_Producto_Proveedor' => $this->input->post('id')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function subirInvoicePlProveedor()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            echo json_encode($this->PedidosPagadosModel->subirInvoicePlProveedor($this->input->post(), $_FILES));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function descargarInvoicePlProveedor($id)
    {
        //echo "hola";
        $objPedido = $this->PedidosPagadosModel->descargarInvoicePlProveedor($this->security->xss_clean($id));
        //array_debug($objPedido);

        $objPedido->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $objPedido->Txt_Url_Imagen_Producto);
        $objPedido->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $objPedido->Txt_Url_Imagen_Producto);

        //$file="assets/img/arturo.jpeg";
        if (!file_exists($objPedido->Txt_Url_Imagen_Producto)) {
            die('file not found');
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($objPedido->Txt_Url_Imagen_Producto));
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

    public function despacho()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Fe_Entrega_Shipper_Forwarder' => ToDate($this->input->post('despacho-Fe_Entrega_Shipper_Forwarder')),
            );
            echo json_encode($this->PedidosPagadosModel->despacho(array('ID_Pedido_Cabecera' => $this->input->post('despacho-id_cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function asignarUsuarioPedidoChina()
    {
        //array_debug($this->input->post());
        echo json_encode($this->PedidosPagadosModel->asignarUsuarioPedidoChina($this->input->post()));
        exit();
    }

    public function removerAsignarPedido($ID, $id_usuario)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->removerAsignarPedido($this->security->xss_clean($ID), $this->security->xss_clean($id_usuario)));
    }

    public function completarVerificacionOC($ID, $iIdTareaPedido)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->completarVerificacionOC($this->security->xss_clean($ID), $this->security->xss_clean($iIdTareaPedido)));
    }

    public function reservaBookingConsolidado()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'No_Numero_Consolidado' => $this->input->post('booking_consolidado-No_Numero_Consolidado'),
            );
            echo json_encode($this->PedidosPagadosModel->reservaBookingConsolidado(array('ID_Pedido_Cabecera' => $this->input->post('booking_consolidado-ID_Pedido_Cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function bookingInspeccion()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Qt_Caja_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Caja_Total_Booking'),
                'Qt_Cbm_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Cbm_Total_Booking'),
                'Qt_Peso_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Peso_Total_Booking'),
                'No_Observacion_Inspeccion' => $this->input->post('booking_inspeccion-No_Observacion_Inspeccion'),
            );
            $data_notificacion = array(
                'ID_Usuario_Interno_China' => $this->input->post('booking_inspeccion-ID_Usuario_Interno_China-Actual'),
                'Qt_Caja_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Caja_Total_Booking-Actual'),
                'Qt_Cbm_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Cbm_Total_Booking-Actual'),
                'Qt_Peso_Total_Booking' => $this->input->post('booking_inspeccion-Qt_Peso_Total_Booking-Actual'),
                'sCorrelativoCotizacion' => $this->input->post('booking_inspeccion-sCorrelativoCotizacion'),
            );
            echo json_encode($this->PedidosPagadosModel->bookingInspeccion(array(
                'ID_Pedido_Cabecera' => $this->input->post('booking_inspeccion-ID_Pedido_Cabecera'),
                'Nu_ID_Interno' => $this->input->post('booking_inspeccion-Nu_ID_Interno'),
            ), $data, $data_notificacion));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function supervisarContenedor()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Fe_Llenado_Contenedor' => ToDate($this->input->post('supervisar_llenado_contenedor-Fe_Llenado_Contenedor')),
                'Txt_Llenado_Contenedor' => $this->input->post('supervisar_llenado_contenedor-Txt_Llenado_Contenedor'),
            );
            echo json_encode($this->PedidosPagadosModel->supervisarContenedor(array('ID_Pedido_Cabecera' => $this->input->post('supervisar_llenado_contenedor-id_cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function reservaBookingTrading()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'ID_Shipper' => $this->input->post('reserva_booking_trading-ID_Shipper'),
                'No_Tipo_Contenedor' => $this->input->post('reserva_booking_trading-No_Tipo_Contenedor'),
                'No_Naviera' => $this->input->post('reserva_booking_trading-No_Naviera'),
                'No_Dias_Transito' => $this->input->post('reserva_booking_trading-No_Dias_Transito'),
                'No_Dias_Libres' => $this->input->post('reserva_booking_trading-No_Dias_Libres'),
            );
            echo json_encode($this->PedidosPagadosModel->reservaBookingTrading(array('ID_Pedido_Cabecera' => $this->input->post('reserva_booking_trading-ID_Pedido_Cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function costosOrigenTradingChina()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Ss_Pago_Otros_Flete_China_Yuan' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan'),
                'Ss_Pago_Otros_Flete_China_Dolar' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar'),
                'Ss_Pago_Otros_Costo_Origen_China_Yuan' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan'),
                'Ss_Pago_Otros_Costo_Origen_China_Dolar' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar'),
                'Ss_Pago_Otros_Costo_Fta_China_Yuan' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan'),
                'Ss_Pago_Otros_Costo_Fta_China_Dolar' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar'),
                'Ss_Pago_Otros_Cuadrilla_China_Yuan' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan'),
                'Ss_Pago_Otros_Cuadrilla_China_Dolar' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar'),
                'Ss_Pago_Otros_Costos_China_Yuan' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan'),
                'Ss_Pago_Otros_Costos_China_Dolar' => $this->input->post('costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar'),
                'No_Concepto_Pago_Cuadrilla' => $this->input->post('costos_origen_china-No_Concepto_Pago_Cuadrilla'),
            );
            echo json_encode($this->PedidosPagadosModel->costosOrigenTradingChina(array('ID_Pedido_Cabecera' => $this->input->post('costos_origen_china-ID_Pedido_Cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function docsExportacion()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->docsExportacion($this->input->post(), $_FILES));
    }

    public function despachoShipper()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        $data = array(
            'Nu_Verificar_Despacho_Shipper_Forwarder' => 1,
        );
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->despachoShipper(array('ID_Pedido_Cabecera' => $this->input->post('despacho_shipper-ID_Pedido_Cabecera')), $data));
    }

    public function getBookingEntidad($ID)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->getBookingEntidad($this->security->xss_clean($ID)));
    }

    public function revisionBL()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Txt_Descripcion_BL_China' => $this->input->post('revision_bl-Txt_Descripcion_BL_China'),
            );

            $data_cliente = array(
                'No_Entidad' => $this->input->post('revision_bl-No_Entidad'),
                'Nu_Documento_Identidad' => $this->input->post('revision_bl-Nu_Documento_Identidad'),
                'Txt_Direccion_Entidad' => $this->input->post('revision_bl-Txt_Direccion_Entidad'),
            );
            echo json_encode($this->PedidosPagadosModel->revisionBL(
                array('ID_Pedido_Cabecera' => $this->input->post('revision_bl-ID_Pedido_Cabecera')),
                $data,
                array('ID_Entidad' => $this->input->post('revision_bl-ID_Entidad')),
                $data_cliente
            )
            );
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    public function entregaDocsCliente()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        $data = array(
            'Nu_Verificar_Entrega_Docs_Cliente' => 1,
        );
        if (isset($_POST['entrega_docs_cliente-Nu_Commercial_Invoice']) && $this->input->post('entrega_docs_cliente-Nu_Commercial_Invoice') == 'option1') {
            $data = array_merge($data, array(
                'Nu_Commercial_Invoice' => 1,
            )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_Packing_List']) && $this->input->post('entrega_docs_cliente-Nu_Packing_List') == 'option2') {
            $data = array_merge($data, array(
                'Nu_Packing_List' => 1,
            )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_BL']) && $this->input->post('entrega_docs_cliente-Nu_BL') == 'option3') {
            $data = array_merge($data, array(
                'Nu_BL' => 1,
            )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_FTA']) && $this->input->post('entrega_docs_cliente-Nu_FTA') == 'option4') {
            $data = array_merge($data, array(
                'Nu_FTA' => 1,
            )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_FTA_Detalle']) && $this->input->post('entrega_docs_cliente-Nu_FTA_Detalle') == 'option5') {
            $data = array_merge($data, array(
                'Nu_FTA_Detalle' => 1,
            )
            );
        }
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->entregaDocsCliente(array('ID_Pedido_Cabecera' => $this->input->post('entrega_docs_cliente-ID_Pedido_Cabecera')), $data));
    }

    public function pagosLogisticos()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->pagosLogisticos($this->input->post(), $_FILES));
    }

    public function addFileProveedorDocumentoExportacion()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosPagadosModel->addFileProveedorDocumentoExportacion($this->input->post(), $_FILES));
    }

    public function reservaPedido()
    {
        if (isset($this->session->userdata['usuario'])) {
            if (!$this->input->is_ajax_request()) {
                exit('No se puede eliminar y acceder');
            }

            $data = array(
                'Nu_Tipo_Servicio' => $this->input->post('oc_reservar_pedido-Nu_Tipo_Servicio'),
                'Nu_Tipo_Incoterms' => $this->input->post('oc_reservar_pedido-Nu_Tipo_Incoterms'),
                'Nu_Tipo_Transporte_Maritimo' => $this->input->post('oc_reservar_pedido-Nu_Tipo_Transporte_Maritimo'),
            );
            echo json_encode($this->PedidosPagadosModel->reservaPedido(array('ID_Pedido_Cabecera' => $this->input->post('oc_reservar_pedido-ID_Pedido_Cabecera')), $data));
        } else {
            echo json_encode(array('sStatus' => 'danger', 'sMessage' => 'Sesión terminar. Ingresar nuevamente'));
        }
    }

    //generar cotización PDF para pedido de cliente
    public function generarAgenteCompra($ID)
    {
        $data = $this->PedidosPagadosModel->get_by_id_excel($this->security->xss_clean($ID));
        //array_debug($data);

        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $hoja_activa = 0;
        $fila = 1;
        $fileNameExcel = "OC_Trading_sin_data.xls";

        $hoja_activa = 0;

        //Title
        $BStyle_top = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_left = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_right = array(
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_bottom = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_background_title = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 18,
            ),
        );

        $BStyle_background_sub_tittle = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 13,
            ),
        );

        $BStyle_background_name_label = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'faddd0'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 12,
            ),
        );

        $style_align_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_align_right = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );

        $style_align_left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleArrayAllborder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_tittle_cursive = array(
            'font' => array(
                'color' => array('rgb' => '000000'),
                'size' => 11,
                'italic' => true,
            ),
        );

        //SET ALL BORDER NONE
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'FFFFFF'),
                ),
            ),
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("8"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("8"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("35"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("25"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("25"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("10"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("5"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("8"); //NRO

        //Title
        $fila = 1;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 2;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 3;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 4;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 5;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 6;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(60);

        $objDrawing = new PHPExcel_Worksheet_Drawing();

        $objDrawing->setPath('assets/img/logos/logo_probusiness.png');
        $objDrawing->setWidthAndHeight(700, 800);
        $objDrawing->setResizeProportional(true);
        $objDrawing->setCoordinates('D2');
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        //DERECHA
        $objPHPExcel->getActiveSheet()
            ->getStyle('L' . $fila)
            ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '000000'),
                        'size' => 54,
                    ),
                )
            );

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('L' . $fila, 'ORDEN DE COMPRA');

        $fila = 7;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 8;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Ofic China');
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Ofic China');
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($BStyle_background_sub_tittle);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':N' . $fila);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, 'N° ORDEN');

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($BStyle_background_sub_tittle);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O' . $fila . ':Q' . $fila);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, 'FECHA');

        $fila = 9;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Jr. Alberto Bartón 527');
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Shuangchuang Building, No. 1133');
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

        // /. Title

        if (!empty($data)) {
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0, 3)) . str_pad($data[0]->Nu_Correlativo, 3, "0", STR_PAD_LEFT);

            $objPHPExcel->getActiveSheet()->setTitle($sCorrelativoCotizacion);
            $fileNameExcel = "3.1_O.C TRADING APROBADA_" . $sCorrelativoCotizacion . ".xls";

            $fila = 9;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila . ':Q' . $fila)->applyFromArray($BStyle_bottom);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, $sCorrelativoCotizacion);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':N' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, ToDateBD($data[0]->Fe_Emision_Cotizacion));
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O' . $fila . ':Q' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);

            $fila = 10;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Santa Catalina - La Victoria');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Chouzhou North Road, Yiwu City');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':E' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'CLIENTE');
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'NAME: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->No_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':H' . $fila)->applyFromArray($BStyle_top);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila . ':Q' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'RAZON SOCIAL: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $data[0]->No_Entidad);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'DNI: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Nu_Documento_Identidad_Externo);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'RUC: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $data[0]->Nu_Documento_Identidad);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'WHATSAPP: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Nu_Celular_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'N° PROFORMA: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $sCorrelativoCotizacion);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'CORREO: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Txt_Email_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_bottom);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':H' . $fila)->applyFromArray($BStyle_bottom);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'SERVICIO: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, 'TRADING');
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila . ':Q' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':R' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(50);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'N');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E' . $fila, 'FOTO DEL PRODUCTO');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, 'NOMBRE COMERCIAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'CARACTERISTICAS');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('H' . $fila, 'CANTIDAD TOTAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (RMB)');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('J' . $fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (USD)');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'COSTO TOTAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, 'PCS /' . "\n" . ' CAJA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, 'TOTAL CAJAS');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('N' . $fila, 'CBM /' . "\n" . ' CAJA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, 'CBM ' . "\n" . ' TOTAL');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('Q' . $fila, 'TIEMPO PRODUCCIÓN');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . $fila . ':R' . $fila);

            $fila++;
            $iCounter = 1;
            $fCostoTotalYuanesGeneral = 0;
            $fCostoTotalGeneral = 0;
            $fCbmTotal = 0;
            $fCbmTotalGeneral = 0;
            $fTotalCajasGeneral = 0;
            foreach ($data as $row) {
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('J' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('M' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($styleArrayAllborder);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . $fila . ':R' . $fila);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($styleArrayAllborder);

                $html_data = array("&nbsp;");
                $row->Txt_Descripcion = str_replace($html_data, " ", $row->Txt_Descripcion);

                $html_data = array("<br>", "<p>", "<br/>");
                $row->Txt_Descripcion = str_replace($html_data, "\n", $row->Txt_Descripcion);

                $row->Txt_Descripcion = strip_tags($row->Txt_Descripcion);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('D' . $fila, $iCounter);

                if (!empty($row->Txt_Url_Imagen_Producto)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();

                    //cloud
                    $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                    $row->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $row->Txt_Url_Imagen_Producto);
                    if (file_exists($row->Txt_Url_Imagen_Producto)) {
                        $objDrawing->setPath($row->Txt_Url_Imagen_Producto);
                        $objDrawing->setWidthAndHeight(148, 500);
                        $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
                        $objDrawing->setResizeProportional(true);

                        $objDrawing->setCoordinates('E' . $fila);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                } else {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('E' . $fila, '');
                }

                $fPrecioYuanes = $row->Ss_Precio;
                $fPrecioDolares = ($row->Ss_Precio * $row->Ss_Tipo_Cambio);
                $fTotalCajas = ($row->Qt_Producto_Caja_Final / $row->Qt_Producto_Caja); //TOTAL CAJAS
                $fCostoTotal = ($fPrecioDolares * $row->Qt_Producto_Caja_Final);
                $fCostoTotalYuanes = ($fPrecioYuanes * $row->Qt_Producto_Caja_Final);
                $fCbmTotal = ($fTotalCajas * $row->Qt_Cbm);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('F' . $fila, $row->Txt_Producto)
                    ->setCellValue('G' . $fila, $row->Txt_Descripcion)
                    ->setCellValue('H' . $fila, $row->Qt_Producto_Caja_Final)
                    ->setCellValue('I' . $fila, $row->Ss_Precio) //precio yuanes
                    ->setCellValue('J' . $fila, $fPrecioDolares)
                    ->setCellValue('K' . $fila, $fCostoTotal)
                    ->setCellValue('L' . $fila, $row->Qt_Producto_Caja)
                    ->setCellValue('M' . $fila, $fTotalCajas)
                    ->setCellValue('N' . $fila, $row->Qt_Cbm)
                    ->setCellValue('O' . $fila, $fCbmTotal)
                    ->setCellValue('Q' . $fila, $row->Nu_Dias_Delivery . ' DIAS')
                ;

                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getAlignment()->setWrapText(true);

                $fCostoTotalGeneral += $fCostoTotal; //precio en dolares
                $fCostoTotalYuanesGeneral += $fCostoTotalYuanes; //precio en dolares
                $fCbmTotalGeneral += $fCbmTotal;
                $fTotalCajasGeneral += $fTotalCajas;

                $iCounter++;
                $fila++;
            }

            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'TOTAL')
                ->setCellValue('K' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
                ->setCellValue('M' . $fila, numberFormat($fTotalCajasGeneral, 2, '.', ','))
                ->setCellValue('O' . $fila, numberFormat($fCbmTotalGeneral, 2, '.', ','));

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':J' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->getFont()->setBold(true);

            //SUMAR I y M
            $objPHPExcel->getActiveSheet()
                ->getStyle('D' . $fila . ':' . 'K' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()
                ->getStyle('M' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()
                ->getStyle('O' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, 'USD');
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);

            //DIBUJAR SELLO
            $objDrawing = new PHPExcel_Worksheet_Drawing();

            $objDrawing->setName('Sello ProBusiness China');
            $sSelloEmpresa = 'assets/img/sello_probusiness_china.png';
            if (file_exists($sSelloEmpresa)) {
                $objDrawing->setPath($sSelloEmpresa);
                $objDrawing->setWidthAndHeight(170, 600);
                $objDrawing->setResizeProportional(true);

                $sello_fila = $fila;
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . ($sello_fila + 1) . ':R' . ($sello_fila + 8));
                $objDrawing->setCoordinates('Q' . ($sello_fila + 1));
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fCostoTotalGeneralRMB = round($fCostoTotalGeneral * $data[0]->Ss_Tipo_Cambio, 2);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'TOTAL DE LA COMPRA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fCostoTotalGeneral)
                ->setCellValue('G' . $fila, $fCostoTotalGeneralRMB);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            //FORUMAL DE COMISIOJN DE CONSOLIDA TRADING 5%
            $fComisionTotal = 500;
            $fTotalComisionGeneral = ($fCostoTotalGeneral * 0.05);
            if ($fTotalComisionGeneral > 500) { //=SI((F26*0.05)>250,F26*0.05,250)
                $fComisionTotal = $fTotalComisionGeneral;
            }
            $fComisionTotalRMB = round($fComisionTotal * $data[0]->Ss_Tipo_Cambio, 2);

            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'COMISION BROKER');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fComisionTotal)
                ->setCellValue('G' . $fila, $fComisionTotalRMB);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':G' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'SUB TOTAL');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fCostoTotalGeneral)
                ->setCellValue('G' . $fila, $fCostoTotalGeneralRMB + $fComisionTotalRMB);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'CUENTA BANCARIA CHINA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':O' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, '** Beneficiary Bank: ZHEJIANG CHOUZHOU ');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('I' . $fila, '** Beneficiary Name:   CHRIS FACTORY LIMITED ');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'COMMERCIAL BANK');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('I' . $fila, '- Beneficiary Account: NRA15602002010590009448');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - SWIFT BIC: CZCBCN2X');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('I' . $fila, ' - Company Address: Room 2107 21/F CC Wu Building');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - City: YIWU');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('I' . $fila, ' 302-308  Henessy Road, Wanchai, Hong Kong');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - Province: ZHEJIANG');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('I' . $fila, ' Henessy Road, Wanchai, Hong Kong');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, '  - Country: CHINA');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - Bank Address: No. 1401 North Chouzhou Road ');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' Yiwu Zhejiang China');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila . ':S' . $fila)->applyFromArray($BStyle_bottom);
            //FIN DE GENERAR EXCEL
        } else {
            $fila = 3;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, 'No hay registro');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
        }

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //generar cotización PDF para pedido de cliente
    public function generarConsolidaTrading($ID)
    {
        $data = $this->PedidosPagadosModel->get_by_id_excel($this->security->xss_clean($ID));
        //array_debug($data);

        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $hoja_activa = 0;
        $fila = 1;
        $fileNameExcel = "OC_C.Trading_sin_data.xls";

        $hoja_activa = 0;

        //Title
        $BStyle_top = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_left = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_right = array(
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_bottom = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_background_title = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 18,
            ),
        );

        $BStyle_background_sub_tittle = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 13,
            ),
        );

        $BStyle_background_name_label = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'faddd0'),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 12,
            ),
        );

        $style_align_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_align_right = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );

        $style_align_left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleArrayAllborder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $BStyle_tittle_cursive = array(
            'font' => array(
                'color' => array('rgb' => '000000'),
                'size' => 11,
                'italic' => true,
            ),
        );

        //SET ALL BORDER NONE
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'FFFFFF'),
                ),
            ),
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("8"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("8"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("35"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("25"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("25"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("20"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("10"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth("15"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth("5"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth("20"); //NRO
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth("15"); //NRO

        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth("8"); //NRO

        //Title
        $fila = 1;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 2;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 3;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 4;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 5;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 6;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(60);

        $objDrawing = new PHPExcel_Worksheet_Drawing();

        $objDrawing->setPath('assets/img/logos/logo_probusiness.png');
        $objDrawing->setWidthAndHeight(700, 800);
        $objDrawing->setResizeProportional(true);
        //$objDrawing->setCoordinates('D' . $fila);
        $objDrawing->setCoordinates('D2');
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        //DERECHA
        $objPHPExcel->getActiveSheet()
            ->getStyle('L' . $fila)
            ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '000000'),
                        'size' => 54,
                    ),
                )
            );

        $objPHPExcel->setActiveSheetIndex($hoja_activa)
            ->setCellValue('L' . $fila, 'ORDEN DE COMPRA');

        $fila = 7;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $fila = 8;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Ofic China');
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Ofic China');
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($BStyle_background_sub_tittle);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':N' . $fila);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, 'N° ORDEN');

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($BStyle_background_sub_tittle);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O' . $fila . ':Q' . $fila);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, 'FECHA');

        $fila = 9;
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
        $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Jr. Alberto Bartón 527');
        $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

        $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Shuangchuang Building, No. 1133');
        $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

        // /. Title

        if (!empty($data)) {
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($data[0]->Fe_Month), 0, 3)) . str_pad($data[0]->Nu_Correlativo, 3, "0", STR_PAD_LEFT);

            $objPHPExcel->getActiveSheet()->setTitle($sCorrelativoCotizacion);
            //$fileNameExcel = "3.1_O.C TRADING APROBADA_" . $sCorrelativoCotizacion . ".xls";
            $fileNameExcel = "3.2_O.C C.TRADING APROBADA_" . $sCorrelativoCotizacion . ".xls";

            $fila = 9;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila . ':Q' . $fila)->applyFromArray($BStyle_bottom);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, $sCorrelativoCotizacion);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('L' . $fila . ':N' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('L' . $fila . ':N' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, ToDateBD($data[0]->Fe_Emision_Cotizacion));
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('O' . $fila . ':Q' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('O' . $fila . ':Q' . $fila)->applyFromArray($style_align_center);

            $fila = 10;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'Santa Catalina - La Victoria');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_left);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'Chouzhou North Road, Yiwu City');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':E' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'CLIENTE');
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'NAME: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->No_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':H' . $fila)->applyFromArray($BStyle_top);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila . ':Q' . $fila)->applyFromArray($BStyle_top);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'RAZON SOCIAL: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $data[0]->No_Entidad);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'DNI: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Nu_Documento_Identidad_Externo);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'RUC: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $data[0]->Nu_Documento_Identidad);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'WHATSAPP: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Nu_Celular_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'N° PROFORMA: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, $sCorrelativoCotizacion);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'CORREO: ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, $data[0]->Txt_Email_Contacto);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('F' . $fila . ':H' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':E' . $fila)->applyFromArray($BStyle_bottom);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':H' . $fila)->applyFromArray($BStyle_bottom);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('K' . $fila . ':L' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'SERVICIO: ');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('M' . $fila . ':Q' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, 'C. TRADING');
            $objPHPExcel->getActiveSheet()->getStyle('M' . $fila)->applyFromArray($style_align_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila . ':Q' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':R' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(50);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'N');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('E' . $fila, 'FOTO DEL PRODUCTO');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('F' . $fila, 'NOMBRE COMERCIAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'CARACTERISTICAS');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('H' . $fila, 'CANTIDAD TOTAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('I' . $fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (RMB)');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('J' . $fila, 'PRECIO UNITARIO ' . "\n" . ' EXW (USD)');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('K' . $fila, 'COSTO TOTAL');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('L' . $fila, 'PCS /' . "\n" . ' CAJA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('M' . $fila, 'TOTAL CAJAS');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('N' . $fila, 'CBM /' . "\n" . ' CAJA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('O' . $fila, 'CBM ' . "\n" . ' TOTAL');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('Q' . $fila, 'TIEMPO PRODUCCIÓN');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . $fila . ':R' . $fila);

            $fila++;
            $iCounter = 1;
            $fCostoTotalYuanesGeneral = 0;
            $fCostoTotalGeneral = 0;
            $fCbmTotal = 0;
            $fCbmTotalGeneral = 0;
            $fTotalCajasGeneral = 0;
            foreach ($data as $row) {
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
                $objPHPExcel->getActiveSheet()->getStyle('Q' . $fila . ':R' . $fila)->applyFromArray($style_align_center);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('H' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('J' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('K' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('L' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('M' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('N' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('O' . $fila)->applyFromArray($styleArrayAllborder);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . $fila . ':R' . $fila);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('Q' . $fila)->applyFromArray($styleArrayAllborder);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('R' . $fila)->applyFromArray($styleArrayAllborder);

                $html_data = array("&nbsp;");
                $row->Txt_Descripcion = str_replace($html_data, " ", $row->Txt_Descripcion);

                $html_data = array("<br>", "<p>", "<br/>");
                $row->Txt_Descripcion = str_replace($html_data, "\n", $row->Txt_Descripcion);

                $row->Txt_Descripcion = strip_tags($row->Txt_Descripcion);

                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('D' . $fila, $iCounter);

                if (!empty($row->Txt_Url_Imagen_Producto)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();

                    //cloud
                    $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                    $row->Txt_Url_Imagen_Producto = str_replace("assets", "public_html/assets", $row->Txt_Url_Imagen_Producto);
                    if (file_exists($row->Txt_Url_Imagen_Producto)) {
                        $objDrawing->setPath($row->Txt_Url_Imagen_Producto);
                        $objDrawing->setWidthAndHeight(148, 500);
                        $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(120);
                        $objDrawing->setResizeProportional(true);

                        $objDrawing->setCoordinates('E' . $fila);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                } else {
                    $objPHPExcel->setActiveSheetIndex($hoja_activa)
                        ->setCellValue('E' . $fila, '');
                }

                $fPrecioYuanes = $row->Ss_Precio;
                $fPrecioDolares = ($row->Ss_Precio * $row->Ss_Tipo_Cambio);
                $fTotalCajas = ($row->Qt_Producto_Caja_Final / $row->Qt_Producto_Caja); //TOTAL CAJAS
                $fCostoTotal = ($fPrecioDolares * $row->Qt_Producto_Caja_Final);
                $fCostoTotalYuanes = ($fPrecioYuanes * $row->Qt_Producto_Caja_Final);
                $fCbmTotal = ($fTotalCajas * $row->Qt_Cbm);
                $objPHPExcel->setActiveSheetIndex($hoja_activa)
                    ->setCellValue('F' . $fila, $row->Txt_Producto)
                    ->setCellValue('G' . $fila, $row->Txt_Descripcion)
                    ->setCellValue('H' . $fila, $row->Qt_Producto_Caja_Final)
                    ->setCellValue('I' . $fila, $row->Ss_Precio) //precio yuanes
                    ->setCellValue('J' . $fila, $fPrecioDolares)
                    ->setCellValue('K' . $fila, $fCostoTotal)
                    ->setCellValue('L' . $fila, $row->Qt_Producto_Caja)
                    ->setCellValue('M' . $fila, $fTotalCajas)
                    ->setCellValue('N' . $fila, $row->Qt_Cbm)
                    ->setCellValue('O' . $fila, $fCbmTotal)
                    ->setCellValue('Q' . $fila, $row->Nu_Dias_Delivery . ' DIAS')
                ;

                $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getAlignment()->setWrapText(true);

                $fCostoTotalGeneral += $fCostoTotal; //precio en dolares
                $fCostoTotalYuanesGeneral += $fCostoTotalYuanes; //precio en dolares
                $fCbmTotalGeneral += $fCbmTotal;
                $fTotalCajasGeneral += $fTotalCajas;

                $iCounter++;
                $fila++;
            }

            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(30);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'TOTAL')
                ->setCellValue('K' . $fila, numberFormat($fCostoTotalGeneral, 2, '.', ','))
                ->setCellValue('M' . $fila, numberFormat($fTotalCajasGeneral, 2, '.', ','))
                ->setCellValue('O' . $fila, numberFormat($fCbmTotalGeneral, 2, '.', ','));

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':J' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':O' . $fila)->getFont()->setBold(true);

            //SUMAR I y M
            $objPHPExcel->getActiveSheet()
                ->getStyle('D' . $fila . ':' . 'K' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()
                ->getStyle('M' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $objPHPExcel->getActiveSheet()
                ->getStyle('O' . $fila)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF500B'),
                        ),
                        'font' => array(
                            'color' => array('rgb' => 'FFFFFF'),
                        ),
                    )
                );

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, 'USD')
                ->setCellValue('G' . $fila, 'RMB');
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($style_align_center);

            //DIBUJAR SELLO
            $objDrawing = new PHPExcel_Worksheet_Drawing();

            $objDrawing->setName('Sello ProBusiness China');
            $sSelloEmpresa = 'assets/img/sello_probusiness_china.png';
            if (file_exists($sSelloEmpresa)) {
                $objDrawing->setPath($sSelloEmpresa);
                $objDrawing->setWidthAndHeight(170, 600);
                $objDrawing->setResizeProportional(true);

                $sello_fila = $fila;
                $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('Q' . ($sello_fila + 1) . ':R' . ($sello_fila + 8));
                $objDrawing->setCoordinates('Q' . ($sello_fila + 1));
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fCostoTotalGeneralRMB = round($fCostoTotalGeneral * $data[0]->Ss_Tipo_Cambio, 2);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'TOTAL DE LA COMPRA');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fCostoTotalGeneral)
                ->setCellValue('G' . $fila, $fCostoTotalGeneralRMB);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            //FORUMAL DE COMISIOJN DE CONSOLIDA TRADING 5%
            $fComisionTotal = 250;
            $fTotalComisionGeneral = ($fCostoTotalGeneral * 0.05);
            if ($fTotalComisionGeneral > 250) { //=SI((F26*0.05)>250,F26*0.05,250)
                $fComisionTotal = $fTotalComisionGeneral;
            }
            $fComisionTotalRMB = round($fComisionTotal * $data[0]->Ss_Tipo_Cambio, 2);

            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'COMISION BROKER');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fComisionTotal)
                ->setCellValue('G' . $fila, $fComisionTotalRMB);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila . ':G' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('E' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('F' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila)->applyFromArray($BStyle_background_name_label);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'SUB TOTAL');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':E' . $fila);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('F' . $fila, $fCostoTotalGeneral)
                ->setCellValue('G' . $fila, $fCostoTotalGeneralRMB + $fComisionTotalRMB);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, 'CUENTAS BANCARIAS PERÚ');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':I' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':I' . $fila)->applyFromArray($styleArrayAllborder);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(25);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - CUENTA: CORRIENTE EN DÓLARES');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('G' . $fila, ' - TITULAR: GRUPO PROBUSINESS SAC');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('D' . $fila, 'BCP');

            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($BStyle_background_sub_tittle);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->setCellValue('G' . $fila, 'INTERBANK');

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('D' . $fila, ' - NRO.CUENTA EN DOLARES: 191-9840556-1-63');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('D' . $fila . ':F' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $fila . ':F' . $fila)->applyFromArray($style_align_center);

            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('G' . $fila, ' - NRO.CUENTA EN DOLARES: 200-3001727696');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('G' . $fila . ':I' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $fila . ':I' . $fila)->applyFromArray($style_align_center);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('I' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('D' . $fila . ':I' . $fila)->applyFromArray($BStyle_bottom);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);

            $fila++;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila)->applyFromArray($BStyle_left);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('S' . $fila)->applyFromArray($BStyle_right);
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->getStyle('C' . $fila . ':Q' . $fila)->applyFromArray($BStyle_bottom);
            //FIN DE GENERAR EXCEL
        } else {
            $fila = 3;
            $objPHPExcel->setActiveSheetIndex($hoja_activa)
                ->setCellValue('A' . $fila, 'No hay registro');
            $objPHPExcel->setActiveSheetIndex($hoja_activa)->mergeCells('A' . $fila . ':O' . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($style_align_center);
        }

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileNameExcel . '"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    public function getOrderProgress()
    {
        //get post data
        try {
            $idPedido = $this->input->post()['idPedido'];
            $idPrivilegio = $this->user->Nu_Tipo_Privilegio_Acceso;

            $dbResponse = $this->PedidosPagadosModel->getOrderProgress($idPedido, $idPrivilegio);
            //RETURN JSON RESPONSE
            echo json_encode(array(
                "status" => "success",
                "data" => $dbResponse,
            ));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }

    }
    public function getStepByRole()
    {
        try {
            $step = $this->input->post('step');
            $priviligie = $this->user->Nu_Tipo_Privilegio_Acceso;
            $idPedido = $this->input->post('idPedido');
            if ($step == 1) {
                //if peru personal
                if ($priviligie == $this->personalPeruPrivilegio) {
                    $data = $this->PedidosPagadosModel->getPedidoProductos($idPedido);
                    echo json_encode(array('status' => 'success', 'data' => $data,'priviligie'=>$priviligie));
                    return;
                }
                //if china personal
                if ($priviligie == $this->personalChinaPrivilegio||$priviligie == $this->jefeChinaPrivilegio) {
                    $data = $this->PedidosPagadosModel->getPedidoProductos($idPedido);
                    $pedidoData=$this->PedidosPagadosModel->getPedidoData($idPedido);
                    echo json_encode(array('status' => 'success', 'data' => $data,'pedidoData'=>$pedidoData,'priviligie'=>$priviligie));
                    return;

                }else{
                    echo json_encode(array('status' => 'error', 'data' => [],'priviligie'=>$priviligie));
                    return;

                }   
            }
            if($step == 2){
                //if peru personal
                if ($priviligie == $this->personalPeruPrivilegio) {
                    $data = $this->PedidosPagadosModel->getPedidoPagos($idPedido);
                    echo json_encode(array('status' => 'success', 'data' => $data['data'],
                    'pagosData'=>$data['pagos'],'priviligie'=>$priviligie));
                    return;
                }
                //if china personal
                // if ($priviligie == $this->personalChinaPrivilegio||$priviligie == $this->jefeChinaPrivilegio) {
                //     $data = $this->PedidosPagadosModel->getPedidoProductos($idPedido);
                //     $pedidoData=$this->PedidosPagadosModel->getPedidoData($idPedido);
                //     echo json_encode(array('status' => 'success', 'data' => $data,'pedidoData'=>$pedidoData,'priviligie'=>$priviligie));
                //     return;

                // }else{
                //     echo json_encode(array('status' => 'error', 'data' => [],'priviligie'=>$priviligie));
                //     return;

                // }   
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function saveRotuladoProducto()
    {
        try {
            $data = $this->input->post();
            $files = $_FILES;
            $response = $this->PedidosPagadosModel->saveRotuladoProducto($data,$files);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function saveOrdenCompra()
    {
        try {
            $data = $this->input->post();

            $response = $this->PedidosPagadosModel->saveOrdenCompra($data);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
}
