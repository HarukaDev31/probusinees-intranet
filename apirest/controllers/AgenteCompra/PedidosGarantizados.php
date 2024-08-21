<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'traits/CommonTrait.php';


class PedidosGarantizados extends CI_Controller
{
    use CommonTrait;
    private $upload_path = '../assets/images/clientes/';
    private $file_path = '../assets/images/logos/';
    private $logo_cliente_path = '../assets/images/logos/';
    private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('AgenteCompra/PedidosGarantizadosModel');
        $this->load->model('HelperImportacionModel');
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
            $this->load->view('header_v2', array("js_pedidos_garantizados" => true));
            $this->load->view('AgenteCompra/PedidosGarantizadosView', array(
                'sCorrelativoCotizacion' => $sCorrelativoCotizacion,
                'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
            ));
            $this->load->view('footer_v2', array("js_pedidos_garantizados" => true));
        }
    }

    public function ajax_list()
    {
        $arrData = $this->PedidosGarantizadosModel->get_datatables($this->user);
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();

            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0, 3)) . str_pad($row->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            $rows[] = $row->No_Pais;
            $rows[] = //input with value $row->cotizacionCode; disabled and with a button to edit 
                '<div class="row" id="container-cotizacionCode_' . $row->ID_Pedido_Cabecera . '">
                <input 
                id="cotizacionCode_' . $row->ID_Pedido_Cabecera . '"
                type="text" class="form-control w-100" value="' . $row->cotizacionCode . '" disabled>
                '.(($this->user->Nu_Tipo_Privilegio_Acceso==5||$this->user->Nu_Tipo_Privilegio_Acceso==1)?
                '
                <div class="btn btn-xs btn-link"
                id="btn_edit_cotizacionCode_' . $row->ID_Pedido_Cabecera . '"
                alt="Editar" title="Editar" href="javascript:void(0)" onclick="editarCotizacionCode(\'' . $row->ID_Pedido_Cabecera . '\')">
                <i class="fas fa-edit fa-2x" aria-hidden="true"></i></div>
                </div>':'</div>');
                
            $rows[] = ToDateBD($row->Fe_Emision_Cotizacion);
            $rows[] = $row->No_Contacto . "<br>" . $row->Nu_Celular_Contacto; //quitar para el chino
            $rows[] = $row->No_Entidad . "<br>" . $row->Nu_Documento_Identidad; //quitar para el chino

            //pago garantizado
            if ($this->user->Nu_Tipo_Privilegio_Acceso != 2) {
                $btn_pago_garantizado_peru = '';
                if ($row->Nu_Estado == 2) {
                    $btn_pago_garantizado_peru = '<button class="btn btn-xs btn-link" alt="Subir pago" title="Subir pago" href="javascript:void(0)" onclick="documentoPagoGarantizado(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-money-bill-alt fa-2x" aria-hidden="true"></i></button><br>';
                }

                if (!empty($row->file_url)) {
                    $btn_pago_garantizado_peru .= '<button class="btn btn-xs btn-link" alt="Descargar pago" title="Descargar pago" href="javascript:void(0)" onclick="descargarDocumentoPagoGarantizado(\'' . $row->file_url . '\')">Descargar</button>';
                }

                $rows[] = $btn_pago_garantizado_peru;
            }
            //jefe china 5, personal probusiness 1 personal china 2
            //estado peru
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($row->Nu_Estado);
            $dropdown_estado = '<div class="dropdown">';
            $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado .= '<span class="caret"></span></button>';
            $dropdown_estado .= '<ul class="dropdown-menu">';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Garantizado" title="Garantizado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $row->ID_Usuario_Interno_China . '\');">Esperando</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $row->ID_Usuario_Interno_China . '\');">Enviado</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',4, \'' . $row->ID_Usuario_Interno_China . '\');">Rechazado</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Aprobado" title="Aprobado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',5, \'' . $row->ID_Usuario_Interno_China . '\');">Aprobado</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Observado" title="Observado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',8, \'' . $row->ID_Usuario_Interno_China . '\');">Observado</a></li>';
            $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Recibido" title="Recibido" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',10, \'' . $row->ID_Usuario_Interno_China . '\');">Recibido</a></li>';
            $dropdown_estado .= '</ul>';
            $dropdown_estado .= '</div>';

            if ($this->user->Nu_Tipo_Privilegio_Acceso != 1) { //no tiene acceso a cambiar status de Perú
                $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }
            $rows[] = $dropdown_estado;

            //asignar personal de china desde perú
            if ($this->user->Nu_Tipo_Privilegio_Acceso == 5 || $this->user->Nu_Tipo_Privilegio_Acceso == 1) {
                $btn_asignar_personal_china = '';
                if ($this->user->Nu_Tipo_Privilegio_Acceso == 5) { //1=probusiness
                    $btn_asignar_personal_china = '<button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="asignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->Nu_Estado . '\')"><i class="far fa-user fa-2x text-danger" aria-hidden="true"></i></button>';
                    //if(!empty($row->ID_Usuario_Interno_Empresa_China)){
                }
                if (!empty($row->ID_Usuario_Interno_China)) {
                    if ($this->user->Nu_Tipo_Privilegio_Acceso == 5 || $this->user->Nu_Tipo_Privilegio_Acceso == 1) {
                        $btn_asignar_personal_china = '<span class="badge bg-secondary">' . $row->No_Usuario . '</span>';

                    }if ($this->user->Nu_Tipo_Privilegio_Acceso == 5) {
                        $btn_asignar_personal_china .= '<br><button class="btn btn-xs btn-link" alt="Asginar pedido" title="Asginar pedido" href="javascript:void(0)"  onclick="removerAsignarPedido(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $row->ID_Usuario_Interno_Empresa_China . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
                    }
                }
                $rows[] = $btn_asignar_personal_china;
            }

            //estado de china
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($row->Nu_Estado_China);
            $dropdown_estado_china = '<div class="dropdown">';
            $dropdown_estado_china .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            $dropdown_estado_china .= $arrEstadoRegistro['No_Estado'];
            $dropdown_estado_china .= '<span class="caret"></span></button>';
            $dropdown_estado_china .= '<ul class="dropdown-menu">';
            $dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $sCorrelativoCotizacion . '\');">Pendiente</a></li>';
            $dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="En proceso" title="En proceso" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',2, \'' . $sCorrelativoCotizacion . '\');">En proceso</a></li>';
            $dropdown_estado_china .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Cotizado" title="Cotizado" href="javascript:void(0)" onclick="cambiarEstadoChina(\'' . $row->ID_Pedido_Cabecera . '\',3, \'' . $sCorrelativoCotizacion . '\');">Cotizado</a></li>';
            $dropdown_estado_china .= '</ul>';
            $dropdown_estado_china .= '</div>';

            //comentado temporal
            if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { //no tiene acceso a cambiar status de China
                $dropdown_estado_china = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            }
            $rows[] = $dropdown_estado_china;

            //confirmar cotización
            $rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';

            // //EXCEL cliente de pedido
            if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) {
                $excel_agente_compra = '<button class="btn" alt="Proforma Trading" title="Proforma Trading" href="javascript:void(0)" onclick="generarAgenteCompra(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2"> Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                $excel_consolida_trading = '<button class="btn" alt="Proforma C. Trading" title="Proforma C. Trading" href="javascript:void(0)" onclick="generarConsolidaTrading(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2">C. Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                $rows[] = $excel_agente_compra . '<br>' . $excel_consolida_trading;
                $rows[] = '<span class="badge bg-danger">7 días</span><br>T.C. $ ' . round($row->Ss_Tipo_Cambio, 2);
            } else {
                $excel_consolida_trading = '<button class="btn" alt="Proforma C. Trading" title="Proforma C. Trading" href="javascript:void(0)" onclick="generarCotizacionChina(\'' . $row->ID_Pedido_Cabecera . '\')"><span class="badge bg-success p-2">C. Trading/Trading &nbsp;<i class="fa fa-file-excel text-white"></i></span></button>';
                $rows[] = $excel_consolida_trading;
            }

            //estado peru
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoImportacionIntegral($row->Nu_Importacion_Integral);
            // $dropdown_estado = '<div class="dropdown">';
            //     $dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
            //         $dropdown_estado .= $arrEstadoRegistro['No_Estado'];
            //     $dropdown_estado .= '<span class="caret"></span></button>';
            //     $dropdown_estado .= '<ul class="dropdown-menu">';
            //         $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Si" title="Si" href="javascript:void(0)" onclick="cambiarEstadoImpotacionIntegral(\'' . $row->ID_Pedido_Cabecera . '\',1, \'' . $sCorrelativoCotizacion. '\');">Si</a></li>';
            //         $dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="No" title="No" href="javascript:void(0)" onclick="cambiarEstadoImpotacionIntegral(\'' . $row->ID_Pedido_Cabecera . '\',0, \'' . $sCorrelativoCotizacion. '\');">No</a></li>';
            //     $dropdown_estado .= '</ul>';
            // $dropdown_estado .= '</div>';

            // if($this->user->Nu_Tipo_Privilegio_Acceso==2){//no tiene acceso a cambiar status de Perú
            //     $dropdown_estado = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
            // }
            // $rows[] = $dropdown_estado;

            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function ajax_edit($ID)
    {

        $arrReponse = $this->PedidosGarantizadosModel->get_by_id($this->security->xss_clean($ID));
        //get user Nu_Tipo_Privilegio_Acceso
        
        echo json_encode($arrReponse);
        //echo json_encode($this->PedidosGarantizadosModel->get_by_id($this->security->xss_clean($ID)));
    }

    public function getItemProveedor($ID)
    {
        $data = $this->PedidosGarantizadosModel->getItemProveedor($this->security->xss_clean($ID));
        echo json_encode(array('data' => $data, 'privilegio' => $this->user->Nu_Tipo_Privilegio_Acceso));
    }

    public function getItemImagenProveedor($ID)
    {
        echo json_encode($this->PedidosGarantizadosModel->getItemImagenProveedor($this->security->xss_clean($ID)));
    }

    public function elegirItemProveedor($id_detalle, $ID, $status, $sCorrelativoCotizacion, $sNameItem = '', $pedidoItem, $id_supplier)
    {
        echo json_encode($this->PedidosGarantizadosModel->elegirItemProveedor($this->security->xss_clean($id_detalle), $this->security->xss_clean($ID), $this->security->xss_clean($status), $this->security->xss_clean($sCorrelativoCotizacion), $this->security->xss_clean($sNameItem),
            $this->security->xss_clean($pedidoItem), $this->security->xss_clean($id_supplier)));
    }

    public function actualizarElegirItemProductos()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        echo json_encode($this->PedidosGarantizadosModel->actualizarElegirItemProductos($this->input->post(), $_FILES));
    }

    public function cambiarEstado($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosGarantizadosModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($ID_Usuario_Interno_Empresa_China)));
    }

    public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativoCotizacion)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosGarantizadosModel->cambiarEstadoChina($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativoCotizacion)));
    }

    public function crudPedidoGrupal()
    {
        //array_debug($this->input->post());
        
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        $data = array(
            'ID_Empresa' => $this->input->post('EID_Empresa'),
            'ID_Organizacion' => $this->input->post('EID_Organizacion'),
            
            'Txt_Observaciones_Garantizado' => $this->input->post('Txt_Observaciones_Garantizado'),
            'file_cotizacion' => $_FILES['file_cotizacion'],
        );
        //IF $THIS->USER->NU_TIPO_PRIVILEGIO_ACCESO==5 || $THIS->USER->NU_TIPO_PRIVILEGIO_ACCESO==2
        if($this->user->Nu_Tipo_Privilegio_Acceso==5 || $this->user->Nu_Tipo_Privilegio_Acceso==2){
            $data['Ss_Tipo_Cambio'] = $this->input->post('Ss_Tipo_Cambio');
        }
        echo json_encode($this->PedidosGarantizadosModel->actualizarPedido(
            array(
                'ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera'),
            ),
            $data,
            $this->input->post('addProducto'),
            $this->input->post('addProductoTable'),
            $this->input->post('ECorrelativo')
        )
        );
    }

    public function addPedidoItemProveedor()
    {
        //array_debug($this->input->post());
        //array_debug($_FILES);
        $formData = $this->input->post();
        $fileData = $_FILES;
        echo json_encode($this->PedidosGarantizadosModel->addPedidoItemProveedor($formData, $fileData));
        exit();
    }
    public function generarCotizacionChina($ID)
    {   
        
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
        $this->load->library('PHPExcel');
        // echo json_encode($data);
        $templatePath = 'assets/downloads/agente_compra/COTIZACION-CHINA.xls';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=TRADING_CHINA_' . $data[0]->cotizacionCode . '_Garantizado.xlsx');
        header('Cache-Control: max-age=0');
        //set D10     = $data[0]->No_Contacto
        $objPHPExcel->getActiveSheet()->setCellValue('B8', "COTIZACION:    " . $data[0]->cotizacionCode);

        $objPHPExcel->getActiveSheet()->setCellValue('D10', $data[0]->No_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('D11', $data[0]->Nu_Celular_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('D12', $data[0]->Txt_Email_Contacto);
        // $objPHPExcel->getActiveSheet()->setCellValue('E21', "TRADING");
        $objPHPExcel->getActiveSheet()->setCellValue('M10', $data[0]->No_Entidad);
        $objPHPExcel->getActiveSheet()->setCellValue('M11', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('M12', $data[0]->No_Pais);
        $objPHPExcel->getActiveSheet()->setCellValue('V10', $data[0]->Ss_Tipo_Cambio);
        // $objPHPExcel->getActiveSheet()->setCellValue('E35', "=K32");
        $initialRow = 17;
        $lastProductrow = 18;
        $tempUrl = array();
        $currentIDDetalle = null;
        $startRow = $initialRow;
        $i = 1;
        $imagesInfo = [];

        foreach ($data as $key => $val) {
            if ($initialRow > $lastProductrow) {
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($initialRow, 1);
            }

            if ($currentIDDetalle != $val->ID_Pedido_Detalle) {
                
                if ($currentIDDetalle !== null) {
                    $i++;
                    // Merge previous rows for the same ID_Pedido_Detalle
                    $objPHPExcel->getActiveSheet()->mergeCells("B{$startRow}:B" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));

                }
                // Update startRow for new ID_Pedido_Detalle
                $startRow = $initialRow;
                $currentIDDetalle = $val->ID_Pedido_Detalle;

                // Set merged cells values
                $objPHPExcel->getActiveSheet()->setCellValue("B{$initialRow}", $i);
                // $objPHPExcel->getActiveSheet()->setCellValue("C{$initialRow}", $val->Txt_Producto);
                $objPHPExcel->getActiveSheet()->setCellValue("D{$initialRow}", $val->Txt_Producto);
                //ajuastar texto  in column D}

                // $objPHPExcel->getActiveSheet()->setCellValue("E{$initialRow}",$this->htmlToTextAndLineBreaks ($val->Txt_Descripcion));}
                $objPHPExcel->getActiveSheet()->setCellValue("E{$initialRow}",$this->htmlToRichText($this->htmlToTextAndLineBreaks($val->Txt_Descripcion)));
                if (!empty($val->Txt_Url_Imagen_Producto)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $image = file_get_contents($val->Txt_Url_Imagen_Producto);
                    if ($image !== false) {
                        $path = 'assets/img/';
                        $filename = $path . uniqid() . '.jpg';
                        file_put_contents($filename, $image);
                        $tempUrl[] = $filename;

                        // Obtener las dimensiones de la imagen
                        $newWidth = 148; // O el valor que prefieras

                        $objDrawing->setPath($filename);
                        $objDrawing->setWidth($newWidth);
                        $objDrawing->setHeight(148);
                        $objDrawing->setCoordinates('C' . $startRow);
                        //SET OFFSET y
                        $objDrawing->setOffsetX(20);
                        $objDrawing->setOffsetY(20);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        if ($currentIDDetalle !== null) {
                            $imagesInfo[$val->ID_Pedido_Detalle] = [
                                "objDrawing" => $objDrawing,
                                "startRow" => $startRow,
                            ];
                        }
                    }
                }
            } else {
                

            }
            $objPHPExcel->getActiveSheet()->setCellValue("F{$initialRow}", $val->Qt_Producto);
            $objPHPExcel->getActiveSheet()->setCellValue("H{$initialRow}", $val->Qt_Producto_Moq);
            $objPHPExcel->getActiveSheet()->setCellValue("I{$initialRow}", $this->getUnitName($val->unidad_medida));
            $objPHPExcel->getActiveSheet()->setCellValue("J{$initialRow}", $val->Ss_Precio);
            $objPHPExcel->getActiveSheet()->setCellValue("K{$initialRow}", "=J{$initialRow}/V10");
            $objPHPExcel->getActiveSheet()->setCellValue("L{$initialRow}", "=MAX(H{$initialRow},F{$startRow})*J{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("M{$initialRow}", "=MAX(H{$initialRow},F{$startRow})*K{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("N{$initialRow}", $val->Qt_Producto_Caja);
            $objPHPExcel->getActiveSheet()->setCellValue("O{$initialRow}", "=H{$initialRow}/N{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("P{$initialRow}", $val->Qt_Cbm);

            $objPHPExcel->getActiveSheet()->setCellValue("Q{$initialRow}", "=O{$initialRow}*P{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("R{$initialRow}", $val->kg_box);
            $objPHPExcel->getActiveSheet()->setCellValue("S{$initialRow}", "=O{$initialRow}*R{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("T{$initialRow}", $val->Ss_Costo_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("U{$initialRow}", $val->Nu_Dias_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("V{$initialRow}", $this->htmlToRichText($this->htmlToTextAndLineBreaks(($val->Txt_Nota))));
            $initialRow++;

        }
        //SET COL Q AND P BORDER BOTTOM

        // Merge last group of cells
        if ($currentIDDetalle !== null) {
            $objPHPExcel->getActiveSheet()->mergeCells("B{$startRow}:B" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));
        }
        if ($initialRow <= $lastProductrow) {
            $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
        }
        foreach ($imagesInfo as $imageInfo) {
            //get CimageInfo['startRow'] height
            $height = $objPHPExcel->getActiveSheet()->getRowDimension($imageInfo['startRow'])->getRowHeight();
            $imageInfo['objDrawing']->setOffsetY($height < $imageInfo['objDrawing']->getHeight() * 1.4 ? 50 : $height * 1.2);

        }
        //APPLY ALL BORDER IN COLUMN Q AND P
        $objPHPExcel->getActiveSheet()->getStyle('P17:P' . ($initialRow - 1))->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('R17:R' . ($initialRow - 1))->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            )
        );
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . ($initialRow), "=SUM(K17:K" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($initialRow), "=SUM(L17:L" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($initialRow), "=SUM(M17:M" . ($initialRow - 1) . ")");

        // $objPHPExcel->getActiveSheet()->setCellValue('N'. ($initialRow),"=SUM(N17:N".($initialRow-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . ($initialRow), "=SUM(Q17:Q" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('T' . ($initialRow), "=SUM(T17:T" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('S' . ($initialRow), "=SUM(S17:S" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle('V17:V' . $initialRow)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('D17:D' . $initialRow)->getAlignment()->setWrapText(true);

        //set auto size column v 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit(); //

    }
    //generar cotización PDF para pedido de cliente
    public function generarAgenteCompra($ID)
    {
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
        $this->load->library('PHPExcel');
        $templatePath = 'assets/downloads/agente_compra/TRADING-PERU.xls';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=TRADING_PERU_" . $data[0]->cotizacionCode . "_Garantizado.xlsx");
        header('Cache-Control: max-age=0');
        //set D10     = $data[0]->No_Contacto
        $objPHPExcel->getActiveSheet()->setCellValue('E18', $data[0]->No_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('E19', $data[0]->Nu_Celular_Contacto);
        //set horizontal alignment left
        $objPHPExcel->getActiveSheet()->getStyle('E19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('E20', $data[0]->Txt_Email_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('E21', "TRADING");
        $objPHPExcel->getActiveSheet()->setCellValue('E22', $data[0]->No_Pais);
        $objPHPExcel->getActiveSheet()->setCellValue('M18', $data[0]->No_Entidad);
        $objPHPExcel->getActiveSheet()->setCellValue('M19', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('M20', $data[0]->cotizacionCode);

        $objPHPExcel->getActiveSheet()->setCellValue('M21', date('d/m/Y'));
        $objPHPExcel->getActiveSheet()->setCellValue('U22', $data[0]->Ss_Tipo_Cambio);
        // $objPHPExcel->getActiveSheet()->setCellValue('E35', "=K32");
        $initialRow = 26;
        $lastProductrow = 31;
        $tempUrl = array();
        $currentIDDetalle = null;
        $startRow = $initialRow;
        $i = 1;
        $imagesInfo = [];
        foreach ($data as $key => $val) {
            if ($initialRow > $lastProductrow) {
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($initialRow, 1);
            }
            if ($currentIDDetalle !== $val->ID_Pedido_Detalle) {
                if ($currentIDDetalle !== null) {
                    $i++;

                    $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("G{$startRow}:G" . ($initialRow - 1));

                }
                $startRow = $initialRow;
                $currentIDDetalle = $val->ID_Pedido_Detalle;
                $objPHPExcel->getActiveSheet()->setCellValue("C{$initialRow}", $i);     
                $objPHPExcel->getActiveSheet()->setCellValue("E{$initialRow}", $val->Txt_Producto);

                $objPHPExcel->getActiveSheet()->setCellValue("F{$initialRow}", $this->htmlToRichText($this->htmlToTextAndLineBreaks(($val->Txt_Descripcion))));
                $objPHPExcel->getActiveSheet()->setCellValue("G{$initialRow}", $val->Qt_Producto);
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
 // Obtener las dimensiones de la imagen

                        // Obtener las dimensiones de la imagen
                        $newWidth = 148; // O el valor que prefieras

                        $objDrawing->setPath($filename);
                        $objDrawing->setWidth($newWidth);
                        $objDrawing->setHeight(148);
                        $objDrawing->setCoordinates('C' . $startRow);
                        //SET OFFSET y
                        $objDrawing->setOffsetX(200);
                        $objDrawing->setOffsetY(10);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                        // Guardar la información de la imagen para usarla después
                        $imagesInfo[] = [
                            "objDrawing" => $objDrawing,
                            "startRow" => $startRow,
                        ];
                    }

                }
            }
            else {
                    
                }
                // $objDrawing->setPath($val->Txt_Url_Imagen_Producto);

                $objPHPExcel->getActiveSheet()->setCellValue("E" . $initialRow, $val->Txt_Producto);
                $objPHPExcel->getActiveSheet()->setCellValue("F" . $initialRow, $this->htmlToRichText($this->htmlToTextAndLineBreaks($val->Txt_Descripcion)));
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $initialRow, $val->Qt_Producto);
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $initialRow, $val->Qt_Producto_Moq);
                $objPHPExcel->getActiveSheet()->setCellValue("J" . $initialRow, $this->getUnitName($val->unidad_medida));
                $objPHPExcel->getActiveSheet()->setCellValue("K" . $initialRow, $val->Ss_Precio);
                $objPHPExcel->getActiveSheet()->setCellValue("L{$initialRow}", "=K{$initialRow}/U22");

                // $objPHPExcel->getActiveSheet()->setCellValue("K" . $initialRow,"=MAX(G{$initialRow},I{$startRow})*J{$initialRow}");

                $objPHPExcel->getActiveSheet()->setCellValue("M" . $initialRow,"=MAX(G{$initialRow},I{$startRow})*K{$initialRow}");
                $objPHPExcel->getActiveSheet()->setCellValue("N" . $initialRow,"=MAX(G{$initialRow},I{$startRow})*L{$initialRow}");

                $objPHPExcel->getActiveSheet()->setCellValue("O" . $initialRow, $val->Qt_Producto_Caja);
                $objPHPExcel->getActiveSheet()->setCellValue("P" . $initialRow, "=I{$initialRow}/O{$initialRow}");

                $objPHPExcel->getActiveSheet()->setCellValue("T" . $initialRow, "=P" . $initialRow . "*S" . $initialRow);
                $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialRow, $val->Qt_Cbm);
                $objPHPExcel->getActiveSheet()->setCellValue("S" . $initialRow, $val->kg_box);
              
                $objPHPExcel->getActiveSheet()->setCellValue("U" . $initialRow, $val->Ss_Costo_Delivery);
                $objPHPExcel->getActiveSheet()->setCellValue("V" . $initialRow, $val->Nu_Dias_Delivery);
                $objPHPExcel->getActiveSheet()->setCellValue("W" . $initialRow, $this->htmlToRichText($this->htmlToTextAndLineBreaks(($val->Txt_Nota))));    


                $objPHPExcel->getActiveSheet()->getStyle("Q" . $initialRow)->getNumberFormat()->setFormatCode('0.00');
                $objPHPExcel->getActiveSheet()->getStyle("S" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
                $objPHPExcel->getActiveSheet()->getStyle("T" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
               

                $initialRow++;
            }
            if ($currentIDDetalle !== null) {
                $objPHPExcel->getActiveSheet()->mergeCells("G{$startRow}:G" . ($initialRow - 1));
                $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
                $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
                $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
                $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));
            }
            if ($initialRow <= $lastProductrow) {
                $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
            }
            
            // if ($initialRow < $lastProductrow) {
            //     $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
            // }
            //ajustar texto in d and 3 column 
            $objPHPExcel->getActiveSheet()->getStyle('D26:D' . ($initialRow - 1))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('E26:E' . ($initialRow - 1))->getAlignment()->setWrapText(true);
            // $objPHPExcel->getActiveSheet()->setCellValue('L' . ($initialRow), "=SUM(L26:L" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('M' . ($initialRow), "=SUM(M26:M" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('N' . ($initialRow), "=SUM(N26:N" . ($initialRow - 1) . ")");

            $objPHPExcel->getActiveSheet()->setCellValue('P' . ($initialRow), "=SUM(P26:P" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('R' . ($initialRow), "=SUM(R26:R" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('S' . ($initialRow), "=SUM(S26:S" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('T' . ($initialRow), "=SUM(T26:T" . ($initialRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->setCellValue('U' . ($initialRow), "=SUM(U26:U" . ($initialRow - 1) . ")");

            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($initialRow+3), "=M" . ($initialRow));
            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($initialRow+3), "=N" . ($initialRow));
            //set auto size column w and center horizontal column f
            
            $objPHPExcel->getActiveSheet()->getStyle("F26:F" . ($initialRow - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('W26:W' . ($initialRow - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('V26:V' . ($initialRow - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('W26:W' . ($initialRow - 1))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('F26:F' . ($initialRow - 1))->getAlignment()->setWrapText(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            $sheet=$objPHPExcel->getActiveSheet();
            //set all row auto size 
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            //SET ROW AUTO SIZE 

            foreach ($imagesInfo as $imageInfo) {
                //get CimageInfo['startRow'] height
                $height = $objPHPExcel->getActiveSheet()->getRowDimension($imageInfo['startRow'])->getRowHeight();

                $imageInfo['objDrawing']->setOffsetY($height < $imageInfo['objDrawing']->getHeight() * 1.2 ? 50 : $height * 1.2);
    
            }   
            //set min width column F
            foreach ($tempUrl as $val) {
                unlink($val);
            }
            exit(); //
        
    }

    //generar cotización PDF para pedido de cliente
    public function generarConsolidaTrading($ID)
    {
        $data = $this->PedidosGarantizadosModel->get_by_id_excel($this->security->xss_clean($ID));
        //array_debug($data);
        $this->load->library('PHPExcel');
        $templatePath = 'assets/downloads/agente_compra/CTRADING-PERU.xls';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=CTRADING_PERU_" . $data[0]->cotizacionCode . "_Garantizado.xlsx");
        header('Cache-Control: max-age=0');
        //set D10     = $data[0]->No_Contacto
        $objPHPExcel->getActiveSheet()->setCellValue('B8', $data[0]->cotizacionCode);
        $objPHPExcel->getActiveSheet()->setCellValue('K11', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('K12', $data[0]->No_Pais);
        $objPHPExcel->getActiveSheet()->setCellValue('D10', $data[0]->No_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('D11', $data[0]->Nu_Celular_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('D12', $data[0]->Txt_Email_Contacto);
        $objPHPExcel->getActiveSheet()->setCellValue('L10', $data[0]->No_Entidad);
        $objPHPExcel->getActiveSheet()->setCellValue('L11', $data[0]->Nu_Documento_Identidad);
        $objPHPExcel->getActiveSheet()->setCellValue('L12', $data[0]->No_Pais);
        $objPHPExcel->getActiveSheet()->setCellValue('V10', $data[0]->Ss_Tipo_Cambio);
        $tempUrl = array();
        $initialRow = 17;
        $lastProductrow = 18;
        $currentIDDetalle = null;
        $startRow = $initialRow;
        $i = 1;
        $imagesInfo = [];
        foreach ($data as $key => $val) {
            if ($initialRow > $lastProductrow) {
                //insert a new row
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($initialRow, 1);
            }
            
            if ($currentIDDetalle !== $val->ID_Pedido_Detalle) {
                if ($currentIDDetalle !== null) {
                    $i++;
                    $objPHPExcel->getActiveSheet()->mergeCells("B{$startRow}:B" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
                    $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));

                }
                $startRow = $initialRow;
                $currentIDDetalle = $val->ID_Pedido_Detalle;
                $objPHPExcel->getActiveSheet()->setCellValue("B{$initialRow}", $i);
                $objPHPExcel->getActiveSheet()->setCellValue("D{$initialRow}", $val->Txt_Producto);

                $objPHPExcel->getActiveSheet()->setCellValue("E{$initialRow}", $this->htmlToRichText($this->htmlToTextAndLineBreaks(($val->Txt_Descripcion))));
                $objPHPExcel->getActiveSheet()->setCellValue("F{$initialRow}", $val->Qt_Producto);
                if (!empty($val->Txt_Url_Imagen_Producto)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $image = file_get_contents($val->Txt_Url_Imagen_Producto);
                    if ($image !== false) {
                        $path = 'assets/img/';
                        $filename = $path . uniqid() . '.jpg';
                        file_put_contents($filename, $image);
                        $tempUrl[] = $filename;

                        // Obtener las dimensiones de la imagen
                        $newWidth = 148; // O el valor que prefieras

                        $objDrawing->setPath($filename);
                        $objDrawing->setWidth($newWidth);
                        $objDrawing->setHeight(148);
                        $objDrawing->setCoordinates('C' . $startRow);
                        //SET OFFSET y
                        $objDrawing->setOffsetX(20);
                        $objDrawing->setOffsetY(50);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                        // Guardar la información de la imagen para usarla después
                        $imagesInfo[] = [
                            "objDrawing" => $objDrawing,
                            "startRow" => $startRow,
                        ];
                    }
                }
            } else {
               
            }
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $initialRow, $val->Qt_Producto_Moq);
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $initialRow, $this->getUnitName($val->unidad_medida));
            $objPHPExcel->getActiveSheet()->setCellValue("J" . $initialRow, $val->Ss_Precio);

            $objPHPExcel->getActiveSheet()->setCellValue("K" . $initialRow, "=J" . $initialRow . "/V10");
            $objPHPExcel->getActiveSheet()->setCellValue("L{$initialRow}", "=MAX(H{$initialRow},F{$startRow})*J{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("M" . $initialRow, "=MAX(H{$initialRow},F{$startRow})*K{$initialRow}");
            $objPHPExcel->getActiveSheet()->setCellValue("N" . $initialRow, $val->Qt_Producto_Caja);
            $objPHPExcel->getActiveSheet()->setCellValue("O" . $initialRow, "=H" . $initialRow . "/N" . $initialRow);
            $objPHPExcel->getActiveSheet()->setCellValue("P" . $initialRow, $val->Qt_Cbm);
            // $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialRow, "=P" . $initialRow . "*O" . $initialRow);

            $objPHPExcel->getActiveSheet()->setCellValue("Q" . $initialRow, "=P" . $initialRow . "*O" . $initialRow);
            $objPHPExcel->getActiveSheet()->setCellValue("T" . $initialRow, $val->Ss_Costo_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("R" . $initialRow, $val->kg_box);
            $objPHPExcel->getActiveSheet()->setCellValue("S" . $initialRow,"=O" . $initialRow . "*R" . $initialRow);

            $objPHPExcel->getActiveSheet()->setCellValue("U" . $initialRow, $val->Nu_Dias_Delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("V" . $initialRow, $this->htmlToRichText($this->htmlToTextAndLineBreaks(($val->Txt_Nota))));
            //set auto row height
            $initialRow++;
        }
        // $objPHPExcel->getActiveSheet()->setCellValue('T'. ($initialRow),"=SUM(T26:T".($initialRow-1).")");
        if ($currentIDDetalle !== null) {
            $objPHPExcel->getActiveSheet()->mergeCells("B{$startRow}:B" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("C{$startRow}:C" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("D{$startRow}:D" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("E{$startRow}:E" . ($initialRow - 1));
            $objPHPExcel->getActiveSheet()->mergeCells("F{$startRow}:F" . ($initialRow - 1));
        }
        if ($initialRow <= $lastProductrow) {
            $objPHPExcel->getActiveSheet()->removeRow($initialRow, $lastProductrow - $initialRow + 1);
        }
        foreach ($imagesInfo as $imageInfo) {
            //get CimageInfo['startRow'] height
            $height = $objPHPExcel->getActiveSheet()->getRowDimension($imageInfo['startRow'])->getRowHeight();
            $imageInfo['objDrawing']->setOffsetY($height < $imageInfo['objDrawing']->getHeight() * 1.4 ? 50 : $height * 1.2);

        }
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . ($initialRow), "=SUM(K17:K" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($initialRow), "=SUM(L17:L" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($initialRow), "=SUM(M17:M" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . ($initialRow), "=SUM(Q17:Q" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('S' . ($initialRow), "=SUM(S17:S" . ($initialRow - 1) . ")");
        $objPHPExcel->getActiveSheet()->setCellValue('T' . ($initialRow), "=SUM(T17:T" . ($initialRow - 1) . ")");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //SET V COLUMN AUTO SIZE
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle('V17:V' . $initialRow)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('D17:D' . $initialRow  )->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("Q" . $initialRow)->getNumberFormat()->setFormatCode('0.00');
        //APPLY ALL BORDERS TO COLUMN P and R
        $objPHPExcel->getActiveSheet()->getStyle('P17:P' . ($initialRow - 1))->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('R17:R' . ($initialRow - 1))->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("R" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');
        $objPHPExcel->getActiveSheet()->getStyle("S" . $initialRow)->getNumberFormat()->setFormatCode('0.00" KG"');

        $objWriter->save('php://output');
        foreach ($tempUrl as $val) {
            unlink($val);
        }
        exit();

    }

    public function sendMessage()
    {
        //array_debug($this->user->ID_Usuario);
        //array_debug($this->input->post());
        echo json_encode($this->PedidosGarantizadosModel->sendMessage($this->input->post()));
        exit();
    }

    public function viewChatItem($id)
    {
        echo json_encode($this->PedidosGarantizadosModel->viewChatItem($id));
        exit();
    }

    public function asignarUsuarioPedidoChina()
    {
        //array_debug($this->input->post());
        echo json_encode($this->PedidosGarantizadosModel->asignarUsuarioPedidoChina($this->input->post()));
        exit();
    }

    public function removerAsignarPedido($ID, $id_usuario)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosGarantizadosModel->removerAsignarPedido($this->security->xss_clean($ID), $this->security->xss_clean($id_usuario)));
    }

    public function cambiarEstadoImpotacionIntegral($ID, $Nu_Estado, $sCorrelativoCotizacion)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No se puede eliminar y acceder');
        }

        echo json_encode($this->PedidosGarantizadosModel->cambiarEstadoImpotacionIntegral($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado), $this->security->xss_clean($sCorrelativoCotizacion)));
    }
    public function getSuppliersByName()
    {
        echo json_encode($this->PedidosGarantizadosModel->getSuppliersByName($this->input->post()));

    }
    public function removeSupplier()
    {
        $inputJSON = file_get_contents('php://input');
        // Decodifica el JSON a un array asociativo
        $data = json_decode($inputJSON, true);
        $dbResponse = $this->PedidosGarantizadosModel->removeSupplier($this->security->xss_clean($data));
        if
        ($dbResponse) {
            echo json_encode(array('statusCode' => 200, 'message' => 'Proveedor eliminado correctamente',
                'data' => $dbResponse));
        } else {
            echo json_encode(array('statusCode' => 500, 'message' => 'Error al eliminar proveedor',
                'data' => $dbResponse));
        }
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
    public function addFileProveedor(){
        $file = $_FILES['image_documento'];
        $idCabecera = $this->input->post('documento_pago_garantizado-id_cabecera');
        $response = $this->PedidosGarantizadosModel->addFileProveedor($file, $idCabecera);
        if ($response) {
            echo json_encode(array('statusCode' => 200, 'message' => 'Archivo subido correctamente','status' => 'success',
                'data' => $response));
        } else {
            echo json_encode(array('statusCode' => 500, 'message' => 'Error al subir archivo',
                'status' => 'error','data' => $response));
        }

    }public function deleteItem(){
        $id_item = $this->input->post('id_item');
        $response = $this->PedidosGarantizadosModel->deleteItem($id_item);  
        echo json_encode($response);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
    }
    public function editCotizacionCode(){
        $id_cabecera = $this->input->post('idPedido');
        $cotizacion_code = $this->input->post('value');
        $response = $this->PedidosGarantizadosModel->editCotizacionCode($id_cabecera,$cotizacion_code);  
        echo json_encode($response);
    }
    public function saveCotizacion(){
        $data=$this->input->post();
        $files = $_FILES; // Obtener los archivos del formulario

        $response = $this->PedidosGarantizadosModel->saveCotizacion($data,$files);
        echo json_encode($response);
    }
}
