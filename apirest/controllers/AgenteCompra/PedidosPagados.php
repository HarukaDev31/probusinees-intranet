<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'traits/CommonTrait.php';

class PedidosPagados extends CI_Controller
{
    use CommonTrait;
    private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio = 1;
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
    public function getStatusOrdenCompraLabel($id, $privilegio, $idpedido)
    {
        $HTML = "";
        if ($privilegio == $this->jefeChinaPrivilegio || $privilegio == $this->personalChinaPrivilegio) {
            $HTML = '<select class="form-control" id="status_' . $id . '" onchange="changeStatusOrden(this.value,' . $idpedido . ')">
                <option value="1" ' . ($id == 1 ? 'selected' : '') . '>Pendiente</option>
                <option value="2" ' . ($id == 2 ? 'selected' : '') . '>En Produccion</option>
                <option value="3" ' . ($id == 3 ? 'selected' : '') . '>Recepcionado</option>
                <option value="4" ' . ($id == 4 ? 'selected' : '') . '>Inspeccionado</option>
                <option value="5" ' . ($id == 5 ? 'selected' : '') . '>Entregado</option>

            </select>';
        } else {
            switch ($id) {
                case 1:
                    $HTML = '<span class="badge bg-secondary">Pendiente</span>';
                    break;
                case 2:
                    $HTML = '<span class="badge bg-success">En Produccion</span>';
                    break;
                case 3:
                    $HTML = '<span class="badge bg-danger">Recepcionado</span>';
                    break;
                case 4:
                    $HTML = '<span class="badge bg-warning">Inspeccionado</span>';
                    break;
                case 5:
                    $HTML = '<span class="badge bg-info">Entregado</span>';
                    break;
                default:
                    $HTML = '<span class="badge bg-secondary">Pendiente</span>';
                    break;
            }
        }
        return $HTML;
    }
    public function ajax_list()
    {
        $arrData = $this->PedidosPagadosModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0, 3)) . str_pad($row->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            $divPagosEstado = '';
            if ($row->is_closed == 0) {
                if ($row->total_pagos > 0) {
                    $divPagosEstado = '<span class="badge bg-primary">Pago ' . $row->total_pagos . '</span>';
                } else {
                    $divPagosEstado = '<span class="badge bg-secondary">Pendiente</span>';
                }
            } else {
                $divPagosEstado = '<span class="badge bg-success">Pagado</span>';
            }
            $estadoChina = $this->getStatusOrdenCompraLabel($row->id_estado_orden_compra, $this->user->Nu_Tipo_Privilegio_Acceso, $row->ID_Pedido_Cabecera);
            $avance = $this->getOrderProgressLabel($this->user->Nu_Tipo_Privilegio_Acceso, $row->ID_Pedido_Cabecera);
            $rows[] = $row->No_Pais;
            if($this->user->Nu_Tipo_Privilegio_Acceso == 6){
                $rows[] = ToDateBD($row->Fe_Emision_OC_Aprobada);
                $rows[] = $row->No_Contacto;
                $rows[] = $row->No_Entidad;
                $rows[] = $row->cotizacionCode;
                $rows[] = "<button class='btn btn-xs btn-link' onclick='getAlmacenData(" . $row->ID_Pedido_Cabecera . ")' alt='Editar' title='Editar' href='javascript:void(0)'><i class='fas fa-edit fa-2x' aria-hidden='true'></i></button>";
                //select with 3 options Pendiente,Recibiendo y Completado
                $rows[] = '<select class="form-control" id="status_' . $row->estado_almacen . '" onchange="changeStatusAlmacen(this.value,' . $row->ID_Pedido_Cabecera . ')">
                    <option value="PENDIENTE" ' . ($row->estado_almacen == "PENDIENTE" ? 'selected' : '') . '>PENDIENTE</option>
                    <option value="RECIBIENDO" ' . ($row->estado_almacen == "RECIBIENDO" ? 'selected' : '') . '>RECIBIENDO</option>
                    <option value="COMPLETADO" ' . ($row->estado_almacen == "COMPLETADO" ? 'selected' : '') . '>COMPLETADO</option>
                </select>';
                $data[] = $rows;
                continue;
            }
            $rows[] = $row->cotizacionCode;
            $rows[] = ToDateBD($row->Fe_Emision_OC_Aprobada);

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

            // $btn_comision_trading = '';
            // if ($this->user->Nu_Tipo_Privilegio_Acceso != 2) {
            //     $btn_comision_trading = '<button class="btn btn-link" alt="Agregar comisión Trading" title="Agregar comisión Trading" href="javascript:void(0)" onclick="agregarComisionTrading(\'' . $row->ID_Pedido_Cabecera . '\')">Comisión</button>';
            //     if ($row->Ss_Comision_Interna_Trading > 0) {
            //         $btn_comision_trading = "<br>" . '$ ' . $row->Ss_Comision_Interna_Trading;
            //     }
            // }

            $rows[] = $dropdown_estado;

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
            if($this->user->Nu_Tipo_Privilegio_Acceso != 2){
                $rows[] = $divPagosEstado;
            }
            $rows[] = $estadoChina;
            $rows[] = "<button class='btn btn-xs btn-link' alt='Editar' title='Editar' href='javascript:void(0)'
			onclick='getOrderProgress(" . $row->ID_Pedido_Cabecera . ",".$row->Nu_Tipo_Servicio.")'><i class='fas fa-edit fa-2x' aria-hidden='true'></i></button>";
            // if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) {
                //no tiene acceso a cambiar status de Perú
                $excel_orden_compra = '<button class="btn" alt="Orden Compra Trading" title="Orden Compra Trading" href="javascript:void(0)" onclick="generarAgenteCompra(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2"> Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                if ($row->Nu_Tipo_Servicio == 2) {
                    $excel_orden_compra = '<button class="btn" alt="Orden Compra C. Trading" title="Orden Compra C. Trading" href="javascript:void(0)" onclick="generarConsolidaTrading(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2">C. Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                }
                $rows[] = $excel_orden_compra;
            //}
            $rows[] = $avance;

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

        $data = array();
        echo json_encode(
            $this->PedidosPagadosModel->actualizarPedido(
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
                ->setCellValue('M' . $fila, 'PHONE NUMBER');

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
                    ->setCellValue('K' . $fila, $row->Nu_Dias_Delivery);

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
            echo json_encode(
                $this->PedidosPagadosModel->actualizarProveedor(
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
            echo json_encode(
                $this->PedidosPagadosModel->revisionBL(
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
            $data = array_merge(
                $data,
                array(
                    'Nu_Commercial_Invoice' => 1,
                )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_Packing_List']) && $this->input->post('entrega_docs_cliente-Nu_Packing_List') == 'option2') {
            $data = array_merge(
                $data,
                array(
                    'Nu_Packing_List' => 1,
                )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_BL']) && $this->input->post('entrega_docs_cliente-Nu_BL') == 'option3') {
            $data = array_merge(
                $data,
                array(
                    'Nu_BL' => 1,
                )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_FTA']) && $this->input->post('entrega_docs_cliente-Nu_FTA') == 'option4') {
            $data = array_merge(
                $data,
                array(
                    'Nu_FTA' => 1,
                )
            );
        }
        if (isset($_POST['entrega_docs_cliente-Nu_FTA_Detalle']) && $this->input->post('entrega_docs_cliente-Nu_FTA_Detalle') == 'option5') {
            $data = array_merge(
                $data,
                array(
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
        $this->load->library('PHPExcel');
        $templatePath = 'assets/downloads/agente_compra/TRADING-ORDEN-PERU.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=TRADING_ORDEN_PERU_' . $data[0]->cotizacionCode . '.xlsx');
        header('Cache-Control: max-age=0');

        $objPHPExcel->getActiveSheet()->setCellValue('F17', $data[0]->No_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('F19', $data[0]->Nu_Celular_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('F20', $data[0]->Txt_Email_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('R20', "TRADING");
        $objPHPExcel->getActiveSheet()->setCellValue('R9', date('d/m/Y'));
        $objPHPExcel->getActiveSheet()->setCellValue('T22', $data[0]->Ss_Tipo_Cambio);
        $objPHPExcel->getActiveSheet()->setCellValue('N9', $data[0]->cotizacionCode);
        $objPHPExcel->getActiveSheet()->setCellValue('R17', $data[0]->No_Entidad);
        $objPHPExcel->getActiveSheet()->setCellValue('R18', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('R19', $data[0]->No_Pais);
        $objPHPExcel->getActiveSheet()->setCellValue('T22', $data[0]->Ss_Tipo_Cambio);

        // $objPHPExcel->getActiveSheet()->setCellValue('E35', "=K32");
        $initialRow = 28;
        $lastProductrow = 29;
        $tempUrl = array();

        foreach ($data as $key => $val) {
            if($initialRow>$lastProductrow){
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($initialRow, 1);
            }
            if (!empty($val->Txt_Url_Imagen_Producto)) {
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                // $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                // $row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
                $image = file_get_contents($val->Txt_Url_Imagen_Producto);
                if ($image !== false) {
                    $path = 'assets/img/';
                    $filename = $path . uniqid() . '.jpg';
                    file_put_contents($filename, $image);
                    $tempUrl[] = $filename;
                    $objDrawing->setPath($filename);
                    $objDrawing->setWidthAndHeight(148, 500);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setCoordinates('E' . $initialRow);
                    $objDrawing->setOffsetX(10); // Ajusta el desplazamiento X si es necesario
                    $objDrawing->setOffsetY(10); // Ajusta el desplazamiento Y si es necesario
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
            }
            // $objDrawing->setPath($val->Txt_Url_Imagen_Producto);

            $objPHPExcel->getActiveSheet()->setCellValue("F" . $initialRow, $val->Txt_Producto);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $initialRow, $this->htmlToRichText($this->htmlToTextAndLineBreaks($val->Txt_Descripcion)));
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $initialRow, $val->Qt_Producto);
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $initialRow,$this->getUnitName($val->unidad_medida));
            $objPHPExcel->getActiveSheet()->setCellValue("J" . $initialRow, $val->Ss_Precio);
            $objPHPExcel->getActiveSheet()->setCellValue("L" . $initialRow, "=J" . $initialRow . "*H" . $initialRow);

            $objPHPExcel->getActiveSheet()->setCellValue("M" . $initialRow, "=K" . $initialRow . "*H" . $initialRow);

            $objPHPExcel->getActiveSheet()->setCellValue("N" . $initialRow, $val->Qt_Producto_Caja);
            $objPHPExcel->getActiveSheet()->setCellValue("P" . $initialRow, $val->Qt_Cbm);

            $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialRow, "=O" . $initialRow . "*P" . $initialRow);
            $objPHPExcel->getActiveSheet()->setCellValue("R" . $initialRow, $val->kg_box);
            $objPHPExcel->getActiveSheet()->setCellValue("T" . $initialRow, $val->Ss_Costo_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("U" . $initialRow, $val->Nu_Dias_Delivery);
            $objPHPExcel->getActiveSheet()->getStyle("R" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
            $objPHPExcel->getActiveSheet()->getStyle("S" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
            //apply all borders to column n and p   
            $objPHPExcel->getActiveSheet()->getStyle('G' . $initialRow  )->getAlignment()->setWrapText(true);
            //SET CENTER HORIZONTAL
            $objPHPExcel->getActiveSheet()->getStyle('G' . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('N' . $initialRow . ':P' . $initialRow)->applyFromArray(
                array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
            
            $initialRow++;
        }
        if ($initialRow <= $lastProductrow) {
            $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
        }
       
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($initialRow), "=SUM(L26:L" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($initialRow), "=SUM(M26:M" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('O' . ($initialRow), "=SUM(O26:O" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . ($initialRow), "=SUM(Q26:Q" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('S' . ($initialRow), "=SUM(S26:S" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('T' . ($initialRow), "=SUM(T26:T" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }

    //generar cotización PDF para pedido de cliente
    public function generarConsolidaTrading($ID)
    {
        $data = $this->PedidosPagadosModel->get_by_id_excel($this->security->xss_clean($ID));
        $this->load->library('PHPExcel');
        $templatePath = 'assets/downloads/agente_compra/CTRADING-ORDEN-PERU.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CTRADING_ORDEN_PERU_' . $data[0]->cotizacionCode . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objPHPExcel->getActiveSheet()->setCellValue('F17', $data[0]->No_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('F19', $data[0]->Nu_Celular_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('F20', $data[0]->Txt_Email_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('R20', "C.TRADING");
        $objPHPExcel->getActiveSheet()->setCellValue('R9', date('d/m/Y'));
        $objPHPExcel->getActiveSheet()->setCellValue('T22', $data[0]->Ss_Tipo_Cambio);
        $objPHPExcel->getActiveSheet()->setCellValue('N9', $data[0]->cotizacionCode);
        $objPHPExcel->getActiveSheet()->setCellValue('R17', $data[0]->No_Entidad);
        $objPHPExcel->getActiveSheet()->setCellValue('R18', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('R19', $data[0]->No_Pais);

        $initialRow = 28;
        $lastProductrow = 29;
        $tempUrl = array();

        foreach ($data as $key => $val) {
            if($initialRow>$lastProductrow){
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($initialRow, 1);
			}
            if (!empty($val->Txt_Url_Imagen_Producto)) {
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                // $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                // $row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
                $image = file_get_contents($val->Txt_Url_Imagen_Producto);
                if ($image !== false) {
                    $path = 'assets/img/';
                    $filename = $path . uniqid() . '.jpg';
                    file_put_contents($filename, $image);
                    $tempUrl[] = $filename;
                    $objDrawing->setPath($filename);
                    $objDrawing->setWidthAndHeight(148, 500);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setCoordinates('E' . $initialRow);
                    $objDrawing->setOffsetX(10); // Ajusta el desplazamiento X si es necesario
                    $objDrawing->setOffsetY(10); // Ajusta el desplazamiento Y si es necesario
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
            }
            // $objDrawing->setPath($val->Txt_Url_Imagen_Producto);

            $objPHPExcel->getActiveSheet()->setCellValue("F" . $initialRow, $val->Txt_Producto);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $initialRow, $this->htmlToRichText($this->htmlToTextAndLineBreaks($val->Txt_Descripcion)));
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $initialRow, $val->Qt_Producto);
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $initialRow, $this->getUnitName($val->unidad_medida));
            $objPHPExcel->getActiveSheet()->setCellValue("J" . $initialRow, $val->Ss_Precio);
            $objPHPExcel->getActiveSheet()->setCellValue("N" . $initialRow, $val->Qt_Producto_Caja);
            $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialRow, "=P" . $initialRow . "*O" . $initialRow);
            $objPHPExcel->getActiveSheet()->setCellValue("P" . $initialRow, $val->Qt_Cbm);
            $objPHPExcel->getActiveSheet()->setCellValue("R" . $initialRow, $val->kg_box);
            $objPHPExcel->getActiveSheet()->setCellValue("T" . $initialRow, $val->Ss_Costo_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("U" . $initialRow, $val->Nu_Dias_Delivery);
            $objPHPExcel->getActiveSheet()->getStyle("R" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
            $objPHPExcel->getActiveSheet()->getStyle("S" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
            //set g text wrap and center horizontal
            $objPHPExcel->getActiveSheet()->getStyle('G' . $initialRow)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $initialRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('N' . $initialRow . ':P' . $initialRow)->applyFromArray(
                array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
            $initialRow++;
        }
        if ($initialRow <= $lastProductrow) {
            $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
        }
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($initialRow), "=SUM(L26:L" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($initialRow), "=SUM(M26:M" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('O' . ($initialRow), "=SUM(O26:O" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . ($initialRow), "=SUM(Q26:Q" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('S' . ($initialRow), "=SUM(S26:S" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('T' . ($initialRow), "=SUM(T26:T" . ($initialRow - 1) . ")");
        //SET COLUMN E AUTOSIZE AND AUTOAJUST TEXT
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        foreach ($tempUrl as $key => $val) {
            unlink($val);
        }
        exit();
    }
    public function getOrderProgress()
    {
        //get post data
        try {
            $idPedido = $this->input->post()['idPedido'];
            $idPrivilegio = $this->user->Nu_Tipo_Privilegio_Acceso;

            $dbResponse = $this->PedidosPagadosModel->getOrderProgress($idPedido, $idPrivilegio);
            $consolidadoCode = $this->PedidosPagadosModel->getConsolidadoCode($idPedido);
            //RETURN JSON RESPONSE
            echo json_encode(array(
                "status" => "success",
                "data" => $dbResponse,
                "consolidadoCode" => $consolidadoCode,
            ));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function getAlmacenData()
    {
        try {
            $idPedido = $this->input->post('idPedido');
            $data = $this->PedidosPagadosModel->getAlmacenData($idPedido);
            echo json_encode(array('status' => 'success', 'data' => $data,"privilegio"=>$this->user->Nu_Tipo_Privilegio_Acceso));
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
                    echo json_encode(array('status' => 'success', 'data' => $data, 'priviligie' => $priviligie),JSON_UNESCAPED_UNICODE );
                    return;
                }
                //if china personal
                if ($priviligie == $this->personalChinaPrivilegio || $priviligie == $this->jefeChinaPrivilegio) {
                    $data = $this->PedidosPagadosModel->getPedidoProductos($idPedido);
                    $pedidoData = $this->PedidosPagadosModel->getPedidoData($idPedido);
                    echo json_encode(array('status' => 'success', 'data' => $data, 'pedidoData' => $pedidoData, 'priviligie' => $priviligie));
                    return;
                } else {
                    echo json_encode(array('status' => 'error', 'data' => [], 'priviligie' => $priviligie));
                    return;
                }
            }
            if ($step == 2) {
                //if peru personal
                if ($priviligie == $this->personalPeruPrivilegio) {
                    $data = $this->PedidosPagadosModel->getPedidoPagos($idPedido);
                    
                    echo json_encode(array(
                        'status' => 'success', 'data' => $data['data'],
                        'pagosData' => $data['pagos'], 'priviligie' => $priviligie,
                    ));
                    return;
                } else if ($priviligie == $this->personalChinaPrivilegio || $priviligie == $this->jefeChinaPrivilegio) {
                    $data = $this->PedidosPagadosModel->getSupplierProducts($idPedido, null);
                    echo json_encode(array(
                        'status' => 'success',
                        'data' => $data,
                        'priviligie' => $priviligie,
                    ));
                    return;
                } else {
                    echo json_encode(array('status' => 'error', 'data' => [], 'priviligie' => $priviligie));
                    return;
                }
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
            $response = $this->PedidosPagadosModel->saveRotuladoProducto($data, $files);
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
    public function savePagos()
    {
        try {
            $data = $this->input->post();
            $files = $_FILES;
            $response = $this->PedidosPagadosModel->savePagos($data, $files);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function getProductData()
    {
        try {
            $idProducto = $this->input->post('idProducto');
            $data = $this->PedidosPagadosModel->getProductData($idProducto);
            echo json_encode(array('status' => 'success', 'data' => $data));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function saveCoordination()
    {
        try {
            $data = $this->input->post();
            $files = $_FILES;
            $response = $this->PedidosPagadosModel->saveCoordination($data, $files);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function getSupplierItems()
    {
        try {
            $idPedido = $this->input->post('idPedido');
            $idSupplier = $this->input->post('idSupplier');
            $idCoordination = $this->input->post('idCoordination');
            $data = $this->PedidosPagadosModel->getSupplierItems($idPedido, $idSupplier,$idCoordination);    
            echo json_encode(array('status' => 'success', 'data' => $data));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function saveSupplierItems()
    {
        try {
            $data = $this->input->post();
            $files = $_FILES;
            $response = $this->PedidosPagadosModel->saveSupplierItems($data, $files);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function cambiarEstadoOrden()
    {
        try {
            $data = $this->input->post();
            $id_pedido = $data['id_pedido'];
            $estado = $data['estado'];
            $response = $this->PedidosPagadosModel->cambiarEstadoOrden($id_pedido, $estado);
            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
    public function getOrderProgressLabel($privilegio, $id_pedido)
    {
        $response = $this->PedidosPagadosModel->getOrderProgressLabel($privilegio, $id_pedido);
        $HTML = "";
        if ($response->completed == 0) {
            //return a bar with the progress 0%
            $HTML = '<div class="progress" style="height:auto">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%;height: 30px" aria-label="30px high" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                      </div>';
        } else {
            //return a bar with the progress with the percentage division from total and completed
            $percentage = ($response->completed / $response->total) * 100;
            $HTML = '<div class="progress" style="height:auto">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: ' . $percentage . '%;height: 30px" aria-label="30px high" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">' . $percentage . '%</div>
                      </div>';
        }
        return $HTML;
    }
    public function downloadSupplierExcel()
    {
        $postData = $this->input->post();
        $idPedido = $postData['idPedido'];
        $idSupplier = $postData['idSupplier'];
        $idCoordination = $postData['idCoordination'];
     
        $data = $this->PedidosPagadosModel->getSupplierProductsInvoice($idPedido, $idSupplier,$idCoordination);

        $templatePath = 'assets/downloads/agente_compra/INVOICE_PROVEEDOR.xls';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cotizacion.xlsx"');
        header('Cache-Control: max-age=0');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', $data[0]->cotizacionCode);
        $initialDetailrow = 16;
        $lastDetailrow = 18;
        $tempUrl = array();
        $envio=0;
        $pago1=$data[0]->pago_1_value;
        $pago2=0;
        $total=0;
        foreach ($data  as  $detalle) {
          
            if($initialDetailrow>$lastDetailrow){
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($initialDetailrow, 1);
            }
            $detalle=(array)$detalle;
            
            $envio+=$detalle['shipping_cost'];
            
            $total+=$detalle['total_producto'];

            if (!empty($detalle['imagenURL'])) {
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                // $row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
                // $row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);
                $image = file_get_contents($detalle['imagenURL']);
                if ($image !== false) {
                    $path = 'assets/img/';
                    $filename = $path . uniqid() . '.jpg';
                    file_put_contents($filename, $image);
                    $tempUrl[] = $filename;
                    $objDrawing->setPath($filename);
                    $objDrawing->setWidthAndHeight(148, 500);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setCoordinates('B' . $initialDetailrow);
                    $objDrawing->setOffsetX(10); // Ajusta el desplazamiento X si es necesario
                    $objDrawing->setOffsetY(10); // Ajusta el desplazamiento Y si es necesario
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue("C" . $initialDetailrow, $detalle['Txt_Producto']);
            //set adjust text
            $objPHPExcel->getActiveSheet()->getStyle('C' . $initialDetailrow)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue("E" . $initialDetailrow, $this->htmlToRichText($this->htmlToTextAndLineBreaks($detalle['descripcion'])));
            $objPHPExcel->getActiveSheet()->setCellValue("K" . $initialDetailrow, $detalle['qty_product']);
            $objPHPExcel->getActiveSheet()->setCellValue("L" . $initialDetailrow, $this->getUnitName($detalle['unidad_medida']));
            $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialDetailrow, $detalle['price_product']);
            $objPHPExcel->getActiveSheet()->setCellValue("S" . $initialDetailrow, "要贴麦头");
            $initialDetailrow++;
        }

        if ($initialDetailrow <= $lastDetailrow) {
            $objPHPExcel->getActiveSheet()->removeRow($initialDetailrow, $lastDetailrow - $initialDetailrow + 1);
        }
        $pago2=$total-$pago1;
        $objPHPExcel->getActiveSheet()->setCellValue('J' . ($initialDetailrow), "=SUM(J16:J" . ($initialDetailrow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('N' . ($initialDetailrow), "=SUM(N16:N" . ($initialDetailrow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('R' . ($initialDetailrow + 2), "=SUM(R16:R" . ($initialDetailrow - 1) . ")");
        $initialDetailrow += 3;
        $objPHPExcel->getActiveSheet()->setCellValue('R' . ($initialDetailrow), $envio);
        $initialDetailrow += 1;
        $objPHPExcel->getActiveSheet()->setCellValue('R' . ($initialDetailrow), $pago1);
        $initialDetailrow += 1;
        $objPHPExcel->getActiveSheet()->setCellValue('R' . ($initialDetailrow), $pago2);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        foreach ($tempUrl as $key => $val) {
            unlink($val);
        }
        exit();
    }
    function updateOrdenPedido(){
        $data = $this->input->post();

        $response = $this->PedidosPagadosModel->updateOrdenPedido($data);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    function openRotuladoView(){
        $data = $this->input->post();
        $ID_Detalle = $data['ID_Detalle'];
        $response = $this->PedidosPagadosModel->openRotuladoView($ID_Detalle);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    public function deletePago()
    {
        $data = $this->input->post();
        $idPayment = $data['idPayment'];
        $response = $this->PedidosPagadosModel->deletePago($idPayment);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    public function getUnitName($name){
        switch ($name) {
            case 'un':
                return 'UNIDADES';
                break;
            case 'CAJA':
                return 'CAJA';
                break;
            case 'kg':
                return 'KILOGRAMOS';
                break;
            case 'mt':
                return 'METROS';
                break;
            case 'mt2':
                return 'METRO CUADRADO';
                break;
            case 'lt':
                return 'LITRO';
                break;
            case 'pa':
                return 'PARES';
                break;
            case 'pc':
                return 'PIEZAS';
                break;
            case 'MILLAR':
                return 'MILLAR';
                break;
            case 'BOLSA':
                return 'BOLSA';
                break;
            case 'PAQUETE':
                return 'PAQUETE';
                break;
            case 'OTRO':
                return 'OTRO';
                break;
            default:
                return 'OTRO';
                break;
        }
    }
    public function saveAlmacenData()
    {
        $data = $this->input->post();
        $response = $this->PedidosPagadosModel->saveAlmacenData($data);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    public function cambiarEstadoAlmacen()
    {
        $data = $this->input->post();
        $id_pedido = $data['id_pedido'];
        $estado = $data['estado'];
        $response = $this->PedidosPagadosModel->cambiarEstadoAlmacen($id_pedido, $estado);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    public function saveSupplierPhotos()
    {
        $data = $this->input->post();
        $idSupplier = $data['idSupplier'];
        $idPedido = $data['idPedido'];
        $files = $_FILES;
        $response = $this->PedidosPagadosModel->saveSupplierPhotos($idSupplier,$idPedido, $files);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
    public function getSupplierPhotos()
    {
        $data = $this->input->post();
        $idSupplier = $data['idSupplier'];
        $response = $this->PedidosPagadosModel->getSupplierPhotos($idSupplier);
        echo json_encode(array('status' => 'success', 'data' => $response));
    }
}
