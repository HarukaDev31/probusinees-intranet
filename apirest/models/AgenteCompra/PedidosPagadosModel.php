<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/SupplierTraits.php';
class PedidosPagadosModel extends CI_Model
{
    use FileTrait,SupplierTraits;
    public $table = 'agente_compra_pedido_cabecera';
    public $table_agente_compra_pedido_detalle = 'agente_compra_pedido_detalle';
    public $table_agente_compra_pedido_detalle_producto_proveedor = 'agente_compra_pedido_detalle_producto_proveedor';
    public $table_agente_compra_pedido_detalle_producto_proveedor_imagen = 'agente_compra_pedido_detalle_producto_proveedor_imagen';
    public $table_empresa = 'empresa';
    public $table_organizacion = 'organizacion';
    public $table_configuracion = 'configuracion';
    public $table_moneda = 'moneda';
    public $table_cliente = 'entidad';
    public $table_producto = 'producto';
    public $table_unidad_medida = 'unidad_medida';
    public $table_medio_pago = 'medio_pago';
    public $table_departamento = 'departamento';
    public $table_provincia = 'provincia';
    public $table_distrito = 'distrito';
    public $table_suppliers = 'suppliers';
    public $table_tipo_documento_identidad = 'tipo_documento_identidad';
    public $table_importacion_grupal_cabecera = 'importacion_grupal_cabecera';
    public $table_pais = 'pais';
    public $table_agente_compra_correlativo = 'agente_compra_correlativo';
    public $table_agente_compra_pedido_detalle_producto_proveedor_inspeccion = 'agente_compra_pedido_detalle_producto_proveedor_inspeccion';
    public $table_usuario_intero = 'usuario';
    public $table_order_steps = "agente_compra_order_steps";
    public $order = array('Fe_Registro' => 'desc');
    public $get_productos="get_agente_compra_pedido_productos";
    public $table_payments="payments_agente_compra_pedido";
    public $sp_suppliers="get_suppliers_products";
    public function __construct()
    {
        parent::__construct();
    }

    public function _get_datatables_query()
    {
        $this->db->select(' CORRE.Fe_Month, Nu_Estado_China,' . $this->table . '.*, P.No_Pais,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
        (select count(*) from payments_agente_compra_pedido where id_pedido = '.$this->table.'.ID_Pedido_Cabecera and id_type_payment=2) as total_pagos,
        if((select count(*) from payments_agente_compra_pedido where id_pedido = '.$this->table.'.ID_Pedido_Cabecera and id_type_payment=3)>0,1,0) as is_closed ,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto, USRCHINA.No_Nombres_Apellidos AS No_Usuario, USRJEFECHINA.No_Nombres_Apellidos AS No_Usuario_Jefe')
            ->from($this->table)
            ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
            ->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
            ->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
            ->join($this->table_payments . ' AS PAY', 'PAY.id_pedido = ' . $this->table . '.ID_Pedido_Cabecera', 'left')
            //->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Empresa_China', 'left')
            ->join($this->table_usuario_intero . ' AS USRJEFECHINA', 'USRJEFECHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Jefe_China', 'left')
            ->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_China', 'left')
            ->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
            ->where_in($this->table . '.Nu_Estado', array(5, 6, 7, 9));

        //$this->db->where("Fe_Emision_Cotizacion BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        $this->db->where("Fe_Emision_OC_Aprobada BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

        if (!empty($this->input->post('ID_Pedido_Cabecera'))) {
            $this->db->where($this->table . '.ID_Pedido_Cabecera', $this->input->post('ID_Pedido_Cabecera'));
        }

        if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }

    public function get_by_id($ID)
    {
        $this->db->select($this->table . '.ID_Pedido_Cabecera,
		' . $this->table . '.ID_Entidad,
		' . $this->table . '.ID_Empresa,
		' . $this->table . '.ID_Organizacion,
		' . $this->table . '.Nu_Correlativo,
		' . $this->table . '.Fe_Emision_Cotizacion,
		' . $this->table . '.Ss_Tipo_Cambio,
		' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido,
		' . $this->table . '.Txt_Url_Pago_30_Cliente,
		' . $this->table . '.Txt_Url_Pago_100_Cliente,
		' . $this->table . '.Txt_Url_Pago_Servicio_Cliente,
		' . $this->table . '.Txt_Url_Pago_Otros_Flete,
		' . $this->table . '.Txt_Url_Pago_Otros_Costo_Origen,
		' . $this->table . '.Txt_Url_Pago_Otros_Costo_Fta,
		' . $this->table . '.Txt_Url_Pago_Otros_Cuadrilla,
		' . $this->table . '.Txt_Url_Pago_Otros_Costos,
		' . $this->table . '.Ss_Pago_30_Cliente,
		' . $this->table . '.Ss_Pago_100_Cliente,
		' . $this->table . '.Ss_Pago_Servicio_Cliente,
		' . $this->table . '.Ss_Pago_Otros_Flete,
		' . $this->table . '.Ss_Pago_Otros_Costo_Origen,
		' . $this->table . '.Ss_Pago_Otros_Costo_Fta,
		' . $this->table . '.Ss_Pago_Otros_Cuadrilla,
		' . $this->table . '.Ss_Pago_Otros_Costos,
		CORRE.Fe_Month,
		CLI.No_Contacto,
		CLI.Txt_Email_Contacto,
		CLI.Nu_Celular_Contacto,
		CLI.No_Entidad,
		CLI.Nu_Documento_Identidad,
		ACPDPP.ID_Pedido_Detalle_Producto_Proveedor,
		IGPD.ID_Pedido_Detalle,
		IGPD.Txt_Url_Imagen_Producto,
		IGPD.Txt_Producto,
		ACPDPP.ID_Entidad_Proveedor,
		ACPDPP.Qt_Producto_Caja_Final AS Qt_Producto,
		ACPDPP.Ss_Precio,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Txt_Url_Archivo_Pago_1_Proveedor,
		ACPDPP.Ss_Pago_1_Proveedor,
		ACPDPP.Txt_Url_Archivo_Pago_2_Proveedor,
		ACPDPP.Ss_Pago_2_Proveedor,
		ACPDPP.Nu_Agrego_Inspeccion,
		ACPDPP.Ss_Costo_Delivery,
		ACPDPP.No_Contacto_Proveedor,
		ACPDPP.Txt_Url_Imagen_Proveedor,
		ACPDPP.Fe_Entrega_Proveedor,
		ACPDPP.Nu_Visualizacion_Item,
		ACPDPP.Qt_Producto_Caja_Final_Verificada,
		ACPDPP.Nu_Estado_Recepcion_Carga_Proveedor_Item,
		ACPDPP.Txt_Nota_Recepcion_Carga_Proveedor,
		ACPDPP.Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor,
		ACPDPP.Fe_Pago_Importe_1,
		ACPDPP.Ss_Pago_Importe_1,
		ACPDPP.Fe_Pago_Importe_2,
		ACPDPP.Ss_Pago_Importe_2,
		ACPDPP.No_Cuenta_Bancaria,
		PROVE.No_Contacto AS No_Vendedor_Proveedor
		');
        $this->db->from($this->table);
        $this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
        $this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
        $this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP', 'ACPDPP.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera AND IGPD.ID_Pedido_Detalle=ACPDPP.ID_Pedido_Detalle', 'join');
        $this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
        $this->db->join($this->table_cliente . ' AS PROVE', 'PROVE.ID_Entidad = ACPDPP.ID_Entidad_Proveedor', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera', $ID);
        $this->db->where('ACPDPP.Nu_Selecciono_Proveedor', 1);
        $this->db->where('ACPDPP.Nu_Visualizacion_Item', 1);
        $this->db->order_by('ACPDPP.ID_Entidad_Proveedor ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function elminarItemProveedor($ID, $correlativo, $name_item)
    {
        $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $ID);
        $data = array('Nu_Visualizacion_Item' => 0);
        if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'O.C.',
                $correlativo . ' se eliminó producto ' . $name_item,
                ''
            );

            return array('status' => 'success', 'message' => 'Eliminar');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function get_by_id_inspeccion($ID)
    {
        $this->db->select('ID_Pedido_Detalle_Producto_Inspeccion, Txt_Url_Imagen_Producto');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion);
        $this->db->where($this->table_agente_compra_pedido_detalle_producto_proveedor_inspeccion . '.ID_Pedido_Detalle_Producto_Proveedor', $ID);
        $query = $this->db->get();
        return $query->result();
    }

    public function cambiarEstado($ID, $Nu_Estado, $sCorrelativo)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('Nu_Estado' => $Nu_Estado);
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraArray($Nu_Estado);
            //registrar evento de notificacion
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'O.C.',
                $sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
                ''
            );

            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function cambiarEstadoChina($ID, $Nu_Estado, $sCorrelativo)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('Nu_Estado_China' => $Nu_Estado);
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoAgenteCompraChinaArray($Nu_Estado);
            //registrar evento de notificacion
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'O.C.',
                $sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
                ''
            );
            return array('status' => 'success', 'message' => 'Actualizado', 'notificacion' => $notificacion);
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function addPagoProveedor($arrPost, $data_files)
    {
        if (!empty($arrPost)) {
            if (isset($data_files['voucher_proveedor']) && !empty($data_files['voucher_proveedor']) && !empty($data_files['voucher_proveedor']['name'])) {
                $this->db->trans_begin();

                $path = "assets/images/pagos_proveedores/";
                $config['upload_path'] = $path;
                $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
                $config['max_size'] = 3072; //1024 KB = 3 MB
                $config['encrypt_name'] = true;
                $config['max_filename'] = '255';

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('voucher_proveedor')) {
                    $this->db->trans_rollback();
                    return array(
                        'status' => 'error',
                        'message' => 'No se cargo archivo proveedor ' . strip_tags($this->upload->display_errors()),
                    );
                } else {
                    $arrUploadFile = $this->upload->data();
                    $Txt_Url_Imagen_Proveedor = base_url($path . $arrUploadFile['file_name']);

                    if ($arrPost['proveedor-tipo_pago'] == 1) {
                        //actualizar tabla
                        $data = array(
                            'Txt_Url_Archivo_Pago_1_Proveedor' => $Txt_Url_Imagen_Proveedor,
                            'Ss_Pago_1_Proveedor' => $arrPost['amount_proveedor'],
                        );
                    } else if ($arrPost['proveedor-tipo_pago'] == 2) {
                        //actualizar tabla
                        $data = array(
                            'Txt_Url_Archivo_Pago_2_Proveedor' => $Txt_Url_Imagen_Proveedor,
                            'Ss_Pago_2_Proveedor' => $arrPost['amount_proveedor'],
                        );
                    } else {
                        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe tipo de pago');
                    }

                    $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id']);
                    $this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where);
                }
            } else {
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay archivo');
            }
        } else {
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay datos');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
        } else {
            //$this->db->trans_rollback();
            //registrar evento de notificacion
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'O.C.',
                $arrPost['proveedor-correlativo'] . ' se agrego pago a proveedor',
                ''
            );

            $this->db->trans_commit();
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
        }
    }

    public function getDownloadImage($id)
    {
        $query = "SELECT Txt_Url_Archivo_Pago_1_Proveedor AS Txt_Url_Imagen_Producto FROM " . $this->table_agente_compra_pedido_detalle_producto_proveedor . " WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addInspeccionProveedor($arrPost, $data_files)
    {
        if (isset($data_files['image_inspeccion']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/productos_proveedores_inspeccion/";
            //capturando multiples imagenes por producto de proveedor
            for ($i = 0; $i < count($data_files['image_inspeccion']['name']); $i++) {
                $_FILES['img_proveedor']['name'] = $data_files['image_inspeccion']['name'][$i];
                $_FILES['img_proveedor']['type'] = $data_files['image_inspeccion']['type'][$i];
                $_FILES['img_proveedor']['tmp_name'] = $data_files['image_inspeccion']['tmp_name'][$i];
                $_FILES['img_proveedor']['error'] = $data_files['image_inspeccion']['error'][$i];
                $_FILES['img_proveedor']['size'] = $data_files['image_inspeccion']['size'][$i];

                $config['upload_path'] = $path;
                $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
                $config['max_size'] = 10240; //1024 KB = 10 MB
                $config['encrypt_name'] = true;
                $config['max_filename'] = '255';

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('img_proveedor')) {
                    $this->db->trans_rollback();
                    return array(
                        'status' => 'error',
                        'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                    );
                } else {
                    $arrUploadFile = $this->upload->data();
                    $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                    $arrDetalleImagen[] = array(
                        'ID_Empresa' => $arrPost['proveedor-id_empresa'],
                        'ID_Organizacion' => $arrPost['proveedor-id_organizacion'],
                        'ID_Pedido_Cabecera' => $arrPost['proveedor-id_cabecera'],
                        'ID_Pedido_Detalle' => $arrPost['proveedor-id_detalle'],
                        'ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id'],
                        'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,
                    );
                }
            }

            $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['proveedor-id']);
            $data = array('Nu_Agrego_Inspeccion' => 1); //1=SI
            $this->db->update('agente_compra_pedido_detalle_producto_proveedor', $data, $where);

            $this->db->insert_batch('agente_compra_pedido_detalle_producto_proveedor_inspeccion', $arrDetalleImagen);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();

                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'O.C.',
                    $arrPost['proveedor-correlativo'] . ' se subió fotos de productos de inspección',
                    ''
                );

                $this->db->trans_commit();
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
            }
        } else {
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe archivo(s)');
        }
    }

    public function addFileProveedor($arrPost, $data_files)
    {
        if (isset($data_files['image_documento']['name'])) {
            $this->db->trans_begin();

            $path = "assets/images/documento_entrega_cotizacion/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image_documento')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['documento-id_cabecera']);
                $data = array('Txt_Url_Archivo_Documento_Entrega' => $Txt_Url_Imagen_Producto); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $path = "assets/images/documento_entrega_cotizacion/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image_documento_detalle')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['documento-id_cabecera']);
                $data = array('Txt_Url_Archivo_Invoice_Detail' => $Txt_Url_Imagen_Producto); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                $where_progreso = array(
                    'ID_Pedido_Cabecera' => $arrPost['documento-id_cabecera'],
                    'Nu_ID_Interno' => 16,
                );
                $data_progreso = array('Nu_Estado_Proceso' => 1);
                $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

                //$this->db->trans_rollback();
                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'O.C.',
                    $arrPost['documento-correlativo'] . ' invoice se guardo documento',
                    ''
                );

                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarDocumentoEntregado($id)
    {
        $query = "SELECT Txt_Url_Archivo_Documento_Entrega AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function descargarDocumentoDetalle($id)
    {
        $query = "SELECT Txt_Url_Archivo_Invoice_Detail AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addPagoCliente30($arrPost, $data_files)
    {
        if (isset($data_files['pago_cliente_30']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pago_cliente_30')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_30-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_30_Cliente' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_30_Cliente' => $arrPost['ID_Pais_30_Cliente'],
                    'Fe_Pago_30_Cliente' => ToDate($arrPost['Fe_Pago_30_Cliente']),
                    'Ss_Pago_30_Cliente' => $arrPost['Ss_Pago_30_Cliente'],
                    'Nu_Operacion_Pago_30_Cliente' => $arrPost['Nu_Operacion_Pago_30_Cliente'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_30-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPago30($id)
    {
        $query = "SELECT Txt_Url_Pago_30_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addPagoCliente100($arrPost, $data_files)
    {
        if (isset($data_files['pago_cliente_100']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pago_cliente_100')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_100-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_100_Cliente' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_100_Cliente' => $arrPost['ID_Pais_100_Cliente'],
                    'Fe_Pago_100_Cliente' => ToDate($arrPost['Fe_Pago_100_Cliente']),
                    'Ss_Pago_100_Cliente' => $arrPost['Ss_Pago_100_Cliente'],
                    'Nu_Operacion_Pago_100_Cliente' => $arrPost['Nu_Operacion_Pago_100_Cliente'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_100-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPago100($id)
    {
        $query = "SELECT Txt_Url_Pago_100_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addPagoClienteServicio($arrPost, $data_files)
    {
        if (isset($data_files['pago_cliente_servicio']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pago_cliente_servicio')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_servicio-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_Servicio_Cliente' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Servicio_Cliente' => $arrPost['ID_Pais_Servicio_Cliente'],
                    'Fe_Pago_Servicio_Cliente' => ToDate($arrPost['Fe_Pago_Servicio_Cliente']),
                    'Ss_Pago_Servicio_Cliente' => $arrPost['Ss_Pago_Servicio_Cliente'],
                    'Nu_Operacion_Pago_Servicio_Cliente' => $arrPost['Nu_Operacion_Pago_Servicio_Cliente'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_cliente_servicio-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoServicio($id)
    {
        $query = "SELECT Txt_Url_Pago_Servicio_Cliente AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function actualizarPedido($where, $data, $arrProducto)
    {
        //actualizar productos de tabla de cliente
        if (!empty($arrProducto)) {
            foreach ($arrProducto as $row) {
                //array_debug($row);
                $arrSaleOrderDetailUPD[] = array(
                    'ID_Pedido_Detalle_Producto_Proveedor' => $row['id_item'],
                    'Fe_Entrega_Proveedor' => ToDate($row['fecha_entrega_proveedor']),
                );
            }

            $this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrSaleOrderDetailUPD, 'ID_Pedido_Detalle_Producto_Proveedor');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => 'error', 'message' => 'Error al modificar');
        } else {
            //$this->db->trans_rollback();
            $this->db->trans_commit();
            return array('status' => 'success', 'message' => 'Registro modificado');
        }
    }

    public function cambiarTipoServicio($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);

        $Nu_Tipo_Exportador = 0;
        if ($Nu_Estado == 2) //Consolidado tranding
        {
            $Nu_Tipo_Exportador = 1;
        } else if ($Nu_Estado == 1) //tranding
        {
            $Nu_Tipo_Exportador = 2;
        }

        $data = array(
            'Nu_Tipo_Servicio' => $Nu_Estado,
            'ID_Usuario_Interno_Jefe_China' => $ID_Usuario_Interno_Empresa_China,
            'Nu_Tipo_Exportador' => $Nu_Tipo_Exportador,
        );
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrParams = array('ID_Pedido_Cabecera' => $ID);
            $this->actualizarProgresoPedido($arrParams);

            //crear completar pasos de configuracion jefe
            if ($Nu_Estado == 2) { //Consolidado tranding
                $arrDataTour = array(
                    'ID_Pedido_Cabecera' => $ID,
                    'ID_Usuario_Interno_Empresa_China' => $ID_Usuario_Interno_Empresa_China,
                );
                $arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);
            } else if ($Nu_Estado == 1) { //trading
                $arrDataTour = array(
                    'ID_Pedido_Cabecera' => $ID,
                    'ID_Usuario_Interno_Empresa_China' => $ID_Usuario_Interno_Empresa_China,
                );
                $arrTour = $this->generarEstadoProcesoAgenteCompraTrading($arrDataTour);
            }

            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function agregarComisionTrading($where, $data)
    {
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrParams = array('ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera']);
            $this->actualizarProgresoPedido($arrParams);

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Se agrego comisión');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al agregar comisión');
    }

    public function cambiarIncoterms($ID, $Nu_Estado, $sCorrelativo)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('Nu_Tipo_Incoterms' => $Nu_Estado);
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrParams = array('ID_Pedido_Cabecera' => $ID);
            $this->actualizarProgresoPedido($arrParams);

            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function cambiarTransporte($ID, $Nu_Estado, $sCorrelativo)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('Nu_Tipo_Transporte_Maritimo' => $Nu_Estado);
        if ($this->db->update($this->table, $data, $where) > 0) {
            $arrParams = array('ID_Pedido_Cabecera' => $ID);
            $this->actualizarProgresoPedido($arrParams);

            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function actualizarProgresoPedido($arrParams)
    {
        $query = "SELECT Nu_Tipo_Servicio, Ss_Comision_Interna_Trading, Nu_Tipo_Incoterms, Nu_Tipo_Transporte_Maritimo FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'] . " LIMIT 1";
        $objPedidoTienda = $this->db->query($query)->row();
        if ($objPedidoTienda->Nu_Tipo_Servicio != 0 && $objPedidoTienda->Ss_Comision_Interna_Trading > 0.00 && $objPedidoTienda->Nu_Tipo_Incoterms != 0 && $objPedidoTienda->Nu_Tipo_Transporte_Maritimo != 0) {
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 3,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
                return array('status' => 'success', 'message' => 'Actualizado');
            }
            return array('status' => 'error', 'message' => 'Error al actualizar y agregar progreso compra');
        }
    }

    public function actualizarProgresoPedidoFinal($arrParams)
    {
        $query = "SELECT Ss_Pago_30_Cliente, Ss_Pago_100_Cliente, Ss_Pago_Servicio_Cliente, Ss_Pago_Otros_Flete, Ss_Pago_Otros_Costo_Origen, Ss_Pago_Otros_Costo_Fta, Ss_Pago_Otros_Cuadrilla, Ss_Pago_Otros_Costos FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'] . " LIMIT 1";
        $objPedidoTienda = $this->db->query($query)->row();
        if (
            $objPedidoTienda->Ss_Pago_30_Cliente > 0.00 &&
            $objPedidoTienda->Ss_Pago_100_Cliente > 0.00 &&
            $objPedidoTienda->Ss_Pago_Servicio_Cliente > 0.00 &&
            $objPedidoTienda->Ss_Pago_Otros_Flete > 0.00 &&
            $objPedidoTienda->Ss_Pago_Otros_Costo_Origen > 0.00 &&
            $objPedidoTienda->Ss_Pago_Otros_Costo_Fta > 0.00
        ) {
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 4,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
                $where = array('ID_Pedido_Cabecera' => $arrParams['ID_Pedido_Cabecera']);
                $data = array('Nu_Etapa_Pedido' => 1);
                if ($this->db->update($this->table, $data, $where) > 0) {
                    return array('status' => 'success', 'message' => 'Actualizado');
                } else {
                    return array('status' => 'error', 'message' => 'Error al finalizar etapa');
                }
            }
            return array('status' => 'error', 'message' => 'Error al actualizar y agregar finalizar etapa');
        }
    }

    public function addPagoFlete($arrPost, $data_files)
    {
        if (isset($data_files['pago_flete']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pago_flete')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['pago_flete-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_Otros_Flete' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Otros_Flete' => $arrPost['pago_flete-ID_Pais_Otros_Flete'],
                    'Fe_Pago_Otros_Flete' => ToDate($arrPost['pago_flete-Fe_Pago']),
                    'Ss_Pago_Otros_Flete' => $arrPost['pago_flete-Ss_Pago_Otros_Flete'],
                    'Nu_Operacion_Pago_Otros_Flete' => $arrPost['pago_flete-Nu_Operacion_Pago_Otros_Flete'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_flete-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoFlete($id)
    {
        $query = "SELECT Txt_Url_Pago_Otros_Flete AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addPagoCostosOrigen($arrPost, $data_files)
    {
        if (isset($data_files['costos_origen']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('costos_origen')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['costos_origen-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_Otros_Costo_Origen' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Otros_Costo_Origen' => $arrPost['costos_origen-ID_Pais_Otros_Costo_Origen'],
                    'Fe_Pago_Otros_Costo_Origen' => ToDate($arrPost['costos_origen-Fe_Pago_Otros_Costo_Origen']),
                    'Ss_Pago_Otros_Costo_Origen' => $arrPost['costos_origen-Ss_Pago_Otros_Costo_Origen'],
                    'Nu_Operacion_Pago_Otros_Costo_Origen' => $arrPost['costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['costos_origen-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoCostosOrigen($id)
    {
        $query = "SELECT Txt_Url_Pago_Otros_Costo_Origen AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addPagoFta($arrPost, $data_files)
    {
        if (isset($data_files['pago_fta']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pago_fta')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['pago_fta-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_Otros_Costo_Fta' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Otros_Costo_Fta' => $arrPost['pago_fta-ID_Pais_Otros_Costo_Fta'],
                    'Fe_Pago_Otros_Costo_Fta' => ToDate($arrPost['pago_fta-Fe_Pago_Otros_Costo_Fta']),
                    'Ss_Pago_Otros_Costo_Fta' => $arrPost['pago_fta-Ss_Pago_Otros_Costo_Fta'],
                    'Nu_Operacion_Pago_Otros_Costo_Fta' => $arrPost['pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['pago_fta-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoFTA($id)
    {
        $query = "SELECT Txt_Url_Pago_Otros_Costo_Fta AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addOtrosCuadrilla($arrPost, $data_files)
    {
        if (isset($data_files['otros_cuadrilla']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('otros_cuadrilla')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['otros_cuadrilla-id_cabecera']);
                $data = array(
                    'No_Concepto_Pago_Cuadrilla' => $arrPost['otros_cuadrilla-No_Concepto_Pago_Cuadrilla'],
                    'Txt_Url_Pago_Otros_Cuadrilla' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-ID_Pais_Otros_Cuadrilla'],
                    'Fe_Pago_Otros_Cuadrilla' => ToDate($arrPost['otros_cuadrilla-Fe_Pago_Otros_Cuadrilla']),
                    'Ss_Pago_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-Ss_Pago_Otros_Cuadrilla'],
                    'Nu_Operacion_Pago_Otros_Cuadrilla' => $arrPost['otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['otros_cuadrilla-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoCuadrilla($id)
    {
        $query = "SELECT Txt_Url_Pago_Otros_Cuadrilla AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function addOtrosCostos($arrPost, $data_files)
    {
        if (isset($data_files['otros_costos']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/pagos_clientes/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('otros_costos')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['otros_costos-id_cabecera']);
                $data = array(
                    'Txt_Url_Pago_Otros_Costos' => $Txt_Url_Imagen_Producto,
                    'ID_Pais_Otros_Costos' => $arrPost['otros_costos-ID_Pais_Otros_Costos'],
                    'Fe_Pago_Otros_Costos' => ToDate($arrPost['otros_costos-Fe_Pago_Otros_Costos']),
                    'Ss_Pago_Otros_Costos' => $arrPost['otros_costos-Ss_Pago_Otros_Costos'],
                    'Nu_Operacion_Pago_Otros_Costos' => $arrPost['otros_costos-Nu_Operacion_Pago_Otros_Costos'],
                ); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            $arrParams = array('ID_Pedido_Cabecera' => $arrPost['otros_costos-id_cabecera']);
            $this->actualizarProgresoPedidoFinal($arrParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarPagoOtrosCostos($id)
    {
        $query = "SELECT Txt_Url_Pago_Otros_Costos AS Txt_Url_Imagen_Producto FROM " . $this->table . " WHERE ID_Pedido_Cabecera = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function actualizarProveedor($where_entidad, $data, $where_detalle_item, $data_entidad)
    {
        if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where_detalle_item) > 0) {
            if ($this->db->update('entidad', $data_entidad, $where_entidad) > 0) {
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
            }

            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
        } else {
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al item proveedor');
        }
    }

    public function getPedidoProveedor($ID)
    {
        $query = "SELECT
ACPDPP.No_Wechat,
ACPDPP.No_Rubro,
ACPDPP.No_Cuenta_Bancaria,
ACPDPP.Ss_Pago_Importe_1,
PROVE.No_Contacto AS No_Vendedor_Proveedor,
PROVE.No_Titular_Cuenta_Bancaria,
PROVE.Txt_Url_Imagen_Proveedor_Pay_Qr,
PROVE.Nu_Tipo_Pay_Proveedor_China,
PROVE.No_Banco_China
FROM
agente_compra_pedido_detalle_producto_proveedor AS ACPDPP
JOIN entidad AS PROVE ON(PROVE.ID_Entidad = ACPDPP.ID_Entidad_Proveedor)
WHERE
ACPDPP.ID_Pedido_Detalle_Producto_Proveedor = " . $ID . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function reservaBookingConsolidado($where, $data)
    {
        if (isset($data['No_Numero_Consolidado']) && !empty($data['No_Numero_Consolidado'])) {
            //marcar progreso 1. Verificar datos de exportación
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 13,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);
        }

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function reservaBooking($where, $data)
    {
        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 6,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function getBooking($ID)
    {
        $query = "SELECT
ACPC.Qt_Caja_Total_Booking,
ACPC.Qt_Cbm_Total_Booking,
ACPC.Qt_Peso_Total_Booking,
ACPC.No_Numero_Consolidado,
ACPC.No_Observacion_Inspeccion,
ACPC.Nu_Tipo_Transporte_Maritimo,
ACPC.ID_Shipper,
ACPC.No_Tipo_Contenedor,
ACPC.No_Naviera,
ACPC.No_Dias_Transito,
ACPC.No_Dias_Libres,
ACPC.Ss_Pago_Otros_Flete_China_Yuan,
ACPC.Ss_Pago_Otros_Flete_China_Dolar,
ACPC.Ss_Pago_Otros_Costo_Origen_China_Yuan,
ACPC.Ss_Pago_Otros_Costo_Origen_China_Dolar,
ACPC.Ss_Pago_Otros_Costo_Fta_China_Yuan,
ACPC.Ss_Pago_Otros_Costo_Fta_China_Dolar,
ACPC.Ss_Pago_Otros_Cuadrilla_China_Yuan,
ACPC.Ss_Pago_Otros_Cuadrilla_China_Dolar,
ACPC.Ss_Pago_Otros_Costos_China_Yuan,
ACPC.Ss_Pago_Otros_Costos_China_Dolar,
ACPC.Txt_Url_Archivo_Exportacion_Docs_Shipper,
ACPC.Txt_Url_Archivo_Exportacion_Commercial_Invoice,
ACPC.Txt_Url_Archivo_Exportacion_Packing_List,
ACPC.Txt_Url_Archivo_Exportacion_Bl,
ACPC.Txt_Url_Archivo_Exportacion_Fta,
ACPC.Nu_Tipo_Incoterms,
ACPC.Txt_Url_Pago_Otros_Flete_China,
ACPC.Txt_Url_Pago_Otros_Costo_Origen_China,
ACPC.Txt_Url_Pago_Otros_Costo_Fta_China,
ACPC.Txt_Url_Pago_Otros_Cuadrilla_China,
ACPC.Txt_Url_Pago_Otros_Costos_China,
S.No_Shipper,
S.No_Coordinador,
ACPC.Nu_Tipo_Servicio,
ACPC.No_Concepto_Pago_Cuadrilla,
ACPC.Nu_Commercial_Invoice,
ACPC.Nu_Packing_List,
ACPC.Nu_BL,
ACPC.Nu_FTA,
ACPC.Nu_FTA_Detalle,
(SELECT Ss_Venta_Oficial FROM tasa_cambio WHERE ID_Empresa=1 AND Fe_Ingreso='" . dateNow('fecha') . "' LIMIT 1) AS yuan_venta
FROM
agente_compra_pedido_cabecera AS ACPC
LEFT JOIN shipper AS S ON(ACPC.ID_Shipper = S.ID_Shipper)
WHERE
ID_Pedido_Cabecera = " . $ID . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function actualizarRecepcionCargaItemProveedor($where, $data)
    {
        if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function actualizarRecepcionCargaProveedor($where, $data)
    {
        if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function subirInvoicePlProveedor($arrPost, $data_files)
    {
        if (isset($data_files['image_documento']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/invoice_pl_proveedor/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image_documento')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $arrPost['documento-id']);
                $data = array('Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor' => $Txt_Url_Imagen_Producto); //1=SI
                $this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarInvoicePlProveedor($id)
    {
        $query = "SELECT Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor AS Txt_Url_Imagen_Producto FROM " . $this->table_agente_compra_pedido_detalle_producto_proveedor . " WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function despacho($where, $data)
    {
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 10,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Completado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function asignarUsuarioPedidoChina($arrPost)
    {
        $where = array('ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera']);
        //$data = array( 'ID_Usuario_Interno_Empresa_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
        $data = array('ID_Usuario_Interno_Jefe_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
        if ($this->db->update($this->table, $data, $where) > 0) {
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 1,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
                return array('status' => 'success', 'message' => 'Se asigno cotización a Jefe');
            } else {
                return array('status' => 'error', 'message' => 'Error al actualizar y agregar progreso compra');
            }
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function removerAsignarPedido($ID, $id_usuario)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('ID_Usuario_Interno_Empresa_China' => 0, 'ID_Usuario_Interno_Jefe_China' => 0);
        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'message' => 'Se quitó asignación');
        }
        return array('status' => 'error', 'message' => 'Error al eliminar asignación pedido');
    }

    public function generarEstadoProcesoAgenteCompra($arrDataTour)
    {
        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '1. Verificar datos de exportación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '11',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '2. Pago a Proveedor (Inicial)',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '12',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '3. Reserva de Booking',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '13',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '4. Inspección',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '14',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '5. Pago a Proveedor (Final)',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '15',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '6. Docs Exportación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '16',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '7. Verificar llenado de contenedor',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '17',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido) > 0) {
            return array('status' => 'success', 'message' => 'Registro guardado');
        }

        return array('status' => 'error', 'message' => 'Error al guardar');
    }

    public function completarVerificacionOC($ID, $iIdTareaPedido)
    {
        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $ID,
            'Nu_ID_Interno' => $iIdTareaPedido,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
            return array('status' => 'success', 'message' => 'Paso 1 verificado');
        }
        return array('status' => 'error', 'message' => 'Error al completar tarea');
    }

    public function listadoTareaPorPedido($id)
    {
        $query = "SELECT ID_Proceso, No_Proceso FROM proceso_agente_compra_pedido WHERE ID_Pedido_Cabecera = " . $id . " AND Nu_Estado_Proceso=0 ORDER BY Nu_ID_Interno ASC LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function verificarTarea($id, $id_tarea)
    {
        $query = "SELECT Nu_Estado_Proceso FROM proceso_agente_compra_pedido WHERE ID_Pedido_Cabecera = " . $id . " AND ID_Proceso=" . $id_tarea . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function bookingInspeccion($where, $data, $data_notificacion)
    {
        //por mientras
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 19,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => $where['Nu_ID_Interno'],
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        unset($where['Nu_ID_Interno']);
        if ($this->db->update($this->table, $data, $where) > 0) {
            if (
                ($data['Qt_Caja_Total_Booking'] != $data_notificacion['Qt_Caja_Total_Booking'])
                || ($data['Qt_Cbm_Total_Booking'] != $data_notificacion['Qt_Cbm_Total_Booking'])
                || ($data['Qt_Peso_Total_Booking'] != $data_notificacion['Qt_Peso_Total_Booking'])
            ) {
                $sValoresCambiados = '';
                if ($data['Qt_Caja_Total_Booking'] != $data_notificacion['Qt_Caja_Total_Booking']) {
                    $sValoresCambiados .= 'Se cambio Cajas Total de ' . $data_notificacion['Qt_Caja_Total_Booking'] . ' por ' . $data['Qt_Caja_Total_Booking'];
                }

                if ($data['Qt_Cbm_Total_Booking'] != $data_notificacion['Qt_Cbm_Total_Booking']) {
                    $sValoresCambiados .= 'Se cambio Cajas Total de ' . $data_notificacion['Qt_Cbm_Total_Booking'] . ' por ' . $data['Qt_Cbm_Total_Booking'];
                }

                if ($data['Qt_Peso_Total_Booking'] != $data_notificacion['Qt_Peso_Total_Booking']) {
                    $sValoresCambiados .= 'Se cambio Cajas Total de ' . $data_notificacion['Qt_Peso_Total_Booking'] . ' por ' . $data['Qt_Peso_Total_Booking'];
                }

                if (!empty($sValoresCambiados)) {
                    $notificacion = $this->NotificacionModel->procesarNotificacion(
                        $this->user->No_Usuario,
                        'O.C.',
                        $data_notificacion['sCorrelativoCotizacion'] . ' se modifico Reserva de Booking. <br>' . $sValoresCambiados,
                        ''
                    );
                }
            }
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Completado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function supervisarContenedor($where, $data)
    {
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 17,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function generarEstadoProcesoAgenteCompraTrading($arrDataTour)
    {
        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '1. Verificar datos de exportación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '18',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '2. Pago a Proveedor (Inicial)',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '19',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '3. Inspección',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '20',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '4. Pago a Proveedor (Final)',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '21',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '5. Reserva de Booking',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '22',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '6. Costos de Origen',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '23',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '7. Docs Exportación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '24',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '8. Despacho al Shipper / Forwarder',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '25',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '9. Revisión de BL',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '26',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '10. Entrega de Docs - Cliente',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '27',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '11. Pagos Logísticos',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '28',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_Empresa_China'],
        );

        if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido) > 0) {
            return array('status' => 'success', 'message' => 'Registro guardado');
        }

        return array('status' => 'error', 'message' => 'Error al guardar');
    }

    public function reservaBookingTrading($where, $data)
    {
        //por mientras
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 21,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 22,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Completado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function costosOrigenTradingChina($where, $data)
    {
        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 23,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function docsExportacion($arrPost, $data_files)
    {
        $path = "assets/downloads/docs_exportacion/";

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
        $config['max_size'] = 3072; //1024 KB = 10 MB
        $config['encrypt_name'] = true;
        $config['max_filename'] = '255';

        if (isset($data_files['docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice']['name'])) {
            $this->db->trans_begin();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo archivo ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFileCI = $this->upload->data();
                $Txt_Url_Archivo_Exportacion_Commercial_Invoice = base_url($path . $arrUploadFileCI['file_name']);

                if (isset($data_files['docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List']['name'])) {
                    if (!$this->upload->do_upload('docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List')) {
                        $this->db->trans_rollback();
                        return array(
                            'status' => 'error',
                            'message' => 'No se cargo archivo PL' . strip_tags($this->upload->display_errors()),
                        );
                    } else {
                        $arrUploadFilePL = $this->upload->data();
                        $Txt_Url_Archivo_Exportacion_Packing_List = base_url($path . $arrUploadFilePL['file_name']);

                        if (isset($data_files['docs_exportacion-Txt_Url_Archivo_Exportacion_Fta']['name'])) {
                            if (!$this->upload->do_upload('docs_exportacion-Txt_Url_Archivo_Exportacion_Fta')) {
                                $this->db->trans_rollback();
                                return array(
                                    'status' => 'error',
                                    'message' => 'No se cargo archivo FTA ' . strip_tags($this->upload->display_errors()),
                                );
                            } else {
                                $arrUploadFileFTA = $this->upload->data();
                                $Txt_Url_Archivo_Exportacion_Fta = base_url($path . $arrUploadFileFTA['file_name']);

                                $Txt_Url_Archivo_Exportacion_Docs_Shipper = '';
                                if (isset($data_files['docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper']['name'])) {
                                    if (!$this->upload->do_upload('docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper')) {
                                        $this->db->trans_rollback();
                                        return array(
                                            'status' => 'error',
                                            'message' => 'No se cargo archivo Shipper ' . strip_tags($this->upload->display_errors()),
                                        );
                                    } else {
                                        $arrUploadFileShipper = $this->upload->data();
                                        $Txt_Url_Archivo_Exportacion_Docs_Shipper = base_url($path . $arrUploadFileShipper['file_name']);
                                    }
                                }

                                $Txt_Url_Archivo_Exportacion_Bl = '';
                                if (isset($data_files['docs_exportacion-Txt_Url_Archivo_Exportacion_Bl']['name'])) {
                                    if (!$this->upload->do_upload('docs_exportacion-Txt_Url_Archivo_Exportacion_Bl')) {
                                        $this->db->trans_rollback();
                                        return array(
                                            'status' => 'error',
                                            'message' => 'No se cargo archivo BL ' . strip_tags($this->upload->display_errors()),
                                        );
                                    } else {
                                        $arrUploadFileBL = $this->upload->data();
                                        $Txt_Url_Archivo_Exportacion_Bl = base_url($path . $arrUploadFileBL['file_name']);
                                    }
                                }

                                $where = array('ID_Pedido_Cabecera' => $arrPost['docs_exportacion-ID_Pedido_Cabecera']);
                                $data = array(
                                    'Txt_Url_Archivo_Exportacion_Docs_Shipper' => $Txt_Url_Archivo_Exportacion_Docs_Shipper,
                                    'Txt_Url_Archivo_Exportacion_Commercial_Invoice' => $Txt_Url_Archivo_Exportacion_Commercial_Invoice,
                                    'Txt_Url_Archivo_Exportacion_Packing_List' => $Txt_Url_Archivo_Exportacion_Packing_List,
                                    'Txt_Url_Archivo_Exportacion_Bl' => $Txt_Url_Archivo_Exportacion_Bl,
                                    'Txt_Url_Archivo_Exportacion_Fta' => $Txt_Url_Archivo_Exportacion_Fta,
                                ); //1=SI
                                $this->db->update($this->table, $data, $where);
                            }
                        } else {
                            return array('status' => 'error', 'message' => 'No existe archivo FTA');
                        }
                    }
                } else {
                    return array('status' => 'error', 'message' => 'No existe archivo PL');
                }
            }

            //marcar progreso 1. Verificar datos de exportación
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrPost['docs_exportacion-ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 24,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function despachoShipper($where, $data)
    {
        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 25,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function getBookingEntidad($ID)
    {
        $query = "SELECT
CLI.ID_Entidad,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
CLI.No_Contacto,
CLI.Nu_Documento_Identidad_Externo,
ACPC.Nu_Tipo_Exportador,
S.No_Shipper,
ACPC.Qt_Caja_Total_Booking,
ACPC.Qt_Cbm_Total_Booking,
ACPC.Qt_Peso_Total_Booking,
ACPC.Nu_Tipo_Transporte_Maritimo,
ACPC.Txt_Descripcion_BL_China,
ACPC.Nu_Tipo_Incoterms,
ACPC.Nu_Tipo_Servicio
FROM
agente_compra_pedido_cabecera AS ACPC
JOIN entidad AS CLI ON(ACPC.ID_Entidad = CLI.ID_Entidad)
LEFT JOIN shipper AS S ON(ACPC.ID_Shipper = S.ID_Shipper)
WHERE
ACPC.ID_Pedido_Cabecera = " . $ID . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function revisionBL($where, $data, $where_cliente, $data_cliente)
    {
        if ($this->db->update($this->table, $data, $where) > 0) {
            //marcar progreso 1. Verificar datos de exportación
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 26,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

            //modificar cliente
            $this->db->update('entidad', $data_cliente, $where_cliente);

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function entregaDocsCliente($where, $data)
    {
        //marcar progreso 1. Verificar datos de exportación
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 27,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function pagosLogisticos($arrPost, $data_files)
    {
        $path = "assets/downloads/pagos_logisticos/";

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
        $config['max_size'] = 3072; //1024 KB = 10 MB
        $config['encrypt_name'] = true;
        $config['max_filename'] = '255';

        if (isset($data_files['pagos_logisticos-Txt_Url_Pago_Otros_Flete_China']['name'])) {
            $this->db->trans_begin();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('pagos_logisticos-Txt_Url_Pago_Otros_Flete_China')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo archivo ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFileCI = $this->upload->data();
                $Txt_Url_Pago_Otros_Flete_China = base_url($path . $arrUploadFileCI['file_name']);

                if (isset($data_files['pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China']['name'])) {
                    if (!$this->upload->do_upload('pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China')) {
                        $this->db->trans_rollback();
                        return array(
                            'status' => 'error',
                            'message' => 'No se cargo archivo PL' . strip_tags($this->upload->display_errors()),
                        );
                    } else {
                        $arrUploadFilePL = $this->upload->data();
                        $Txt_Url_Pago_Otros_Costo_Origen_China = base_url($path . $arrUploadFilePL['file_name']);

                        if (isset($data_files['pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China']['name'])) {
                            if (!$this->upload->do_upload('pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China')) {
                                $this->db->trans_rollback();
                                return array(
                                    'status' => 'error',
                                    'message' => 'No se cargo archivo FTA ' . strip_tags($this->upload->display_errors()),
                                );
                            } else {
                                $arrUploadFileFTA = $this->upload->data();
                                $Txt_Url_Pago_Otros_Costo_Fta_China = base_url($path . $arrUploadFileFTA['file_name']);

                                $Txt_Url_Pago_Otros_Cuadrilla_China = '';
                                if (isset($data_files['pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China']['name'])) {
                                    if (!$this->upload->do_upload('pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China')) {
                                        $this->db->trans_rollback();
                                        return array(
                                            'status' => 'error',
                                            'message' => 'No se cargo archivo Shipper ' . strip_tags($this->upload->display_errors()),
                                        );
                                    } else {
                                        $arrUploadFileShipper = $this->upload->data();
                                        $Txt_Url_Pago_Otros_Cuadrilla_China = base_url($path . $arrUploadFileShipper['file_name']);
                                    }
                                }

                                $Txt_Url_Pago_Otros_Costos_China = '';
                                if (isset($data_files['pagos_logisticos-Txt_Url_Pago_Otros_Costos_China']['name'])) {
                                    if (!$this->upload->do_upload('pagos_logisticos-Txt_Url_Pago_Otros_Costos_China')) {
                                        $this->db->trans_rollback();
                                        return array(
                                            'status' => 'error',
                                            'message' => 'No se cargo archivo BL ' . strip_tags($this->upload->display_errors()),
                                        );
                                    } else {
                                        $arrUploadFileBL = $this->upload->data();
                                        $Txt_Url_Pago_Otros_Costos_China = base_url($path . $arrUploadFileBL['file_name']);
                                    }
                                }

                                $where = array('ID_Pedido_Cabecera' => $arrPost['pagos_logisticos-ID_Pedido_Cabecera']);
                                $data = array(
                                    'Txt_Url_Pago_Otros_Flete_China' => $Txt_Url_Pago_Otros_Flete_China,
                                    'Txt_Url_Pago_Otros_Costo_Origen_China' => $Txt_Url_Pago_Otros_Costo_Origen_China,
                                    'Txt_Url_Pago_Otros_Costo_Fta_China' => $Txt_Url_Pago_Otros_Costo_Fta_China,
                                    'Txt_Url_Pago_Otros_Cuadrilla_China' => $Txt_Url_Pago_Otros_Cuadrilla_China,
                                    'Txt_Url_Pago_Otros_Costos_China' => $Txt_Url_Pago_Otros_Costos_China,
                                ); //1=SI
                                $this->db->update($this->table, $data, $where);
                            }
                        } else {
                            return array('status' => 'error', 'message' => 'No existe archivo FTA');
                        }
                    }
                } else {
                    return array('status' => 'error', 'message' => 'No existe archivo PL');
                }
            }

            //marcar progreso 1. Verificar datos de exportación
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrPost['pagos_logisticos-ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 28,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function addFileProveedorDocumentoExportacion($arrPost, $data_files)
    {
        if (isset($data_files['documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion']['name'])) {
            $this->db->trans_begin();

            $path = "assets/images/documento_proveedor_exportacion/";

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|csv|xls|pdf|doc|docx';
            $config['max_size'] = 3072; //1024 KB = 10 MB
            $config['encrypt_name'] = true;
            $config['max_filename'] = '255';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion')) {
                $this->db->trans_rollback();
                return array(
                    'status' => 'error',
                    'message' => 'No se cargo imagen ' . strip_tags($this->upload->display_errors()),
                );
            } else {
                $arrUploadFile = $this->upload->data();
                $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                $where = array('ID_Pedido_Cabecera' => $arrPost['documento_proveedor_exportacion-id_cabecera']);
                $data = array('Txt_Url_Imagen_Proveedor_Doc_Exportacion' => $Txt_Url_Imagen_Producto); //1=SI
                $this->db->update($this->table, $data, $where);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                $where_progreso = array(
                    'ID_Pedido_Cabecera' => $arrPost['documento_proveedor_exportacion-id_cabecera'],
                    'Nu_ID_Interno' => 9,
                );
                $data_progreso = array('Nu_Estado_Proceso' => 1);
                $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

                //$this->db->trans_rollback();
                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'O.C.',
                    $arrPost['documento-correlativo'] . ' invoice se guardo documento',
                    ''
                );

                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Documento guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function get_by_id_excel($ID)
    {
        $this->db->select('
		' . $this->table . '.Nu_Correlativo,
		' . $this->table . '.Fe_Emision_Cotizacion,
		' . $this->table . '.Ss_Tipo_Cambio,
		CORRE.Fe_Month,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto, CLI.Nu_Documento_Identidad_Externo,
		IGPD.Txt_Url_Imagen_Producto,
		IGPD.Txt_Producto,
		IGPD.Txt_Descripcion,
		ACPDPP.Qt_Producto_Caja_Final,
		ACPDPP.Ss_Precio,
		ACPDPP.Qt_Producto_Caja,
		ACPDPP.Qt_Cbm,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Ss_Costo_Delivery');
        $this->db->from($this->table);
        $this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
        $this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
        $this->db->join($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP', 'ACPDPP.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera AND IGPD.ID_Pedido_Detalle=ACPDPP.ID_Pedido_Detalle', 'join');
        $this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera', $ID);
        $this->db->where('ACPDPP.Nu_Selecciono_Proveedor', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function reservaPedido($where, $data)
    {
        $where_progreso = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
            'Nu_ID_Interno' => 3,
        );
        $data_progreso = array('Nu_Estado_Proceso' => 1);
        $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Completado');
        }

        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    public function getOrderProgress($idPedido, $idPrivilegio): array
    {
        //select name status id, from table_order_steps
        try {
            $this->db->select('
		id,
		name,
		status,
        iconURL 
		');
            $this->db->from($this->table_order_steps);
            $this->db->where('id_pedido', $idPedido);
            $this->db->where('id_permision_role', $idPrivilegio);
            $this->db->order_by('id_order', 'asc');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }
    public function getPedidoProductos($idPedido){
        try{
            
            //call sp 
            $query = $this->db->query("CALL get_agente_compra_pedido_productos(".$idPedido.")");
            return $query->result();

        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }public function saveRotuladoProducto($data,$files){
        try{

            //get caja_master file in $files
            $idPedido = $data['idPedido'];
            $idProducto=$data['idProducto'];
            $cajaMasterURL=null;
            $empaqueURL=null;
            $vimmotorURL=null;
            $stepID=$data['stepID'];
            $notas=$data['notas_rotulado'];
            $existingcajaMasterURL=$data['caja_master_URL'];
            $existingempaqueURL=$data['empaque_URL'];
            $existingvimmotorURL=$data['vim_motor_URL'];

            $path = "assets/images/agentecompra/orden-compra/".$idPedido."/".$idProducto."/rotulado/";
            if($existingcajaMasterURL!="null"){
                $cajaMasterURL=$existingcajaMasterURL;
            }
            if(array_key_exists('caja_master',$files)){
                $cajaMasterURL=$this->uploadSingleFile($files['caja_master'],$path); 
            }
            if($existingempaqueURL!="null"){
                $empaqueURL=$existingempaqueURL;
            }

            if(array_key_exists('empaque',$files)){
                    $empaqueURL=$this->uploadSingleFile($files['empaque'],$path);
            }
            if($existingvimmotorURL!="null"){
                $vimmotorURL=$existingvimmotorURL;
            }
            if(array_key_exists('vim_motor',$files)){
                    $vimmotorURL=$this->uploadSingleFile($files['vim_motor'],$path);
                
            }
            $datatoInsert=array(
                'caja_master_URL'=>$cajaMasterURL,
                'empaque_URL'=>$empaqueURL,
                'vim_motor_URL'=>$vimmotorURL,
                'notas_rotulado'=>$notas
            );
            //update table agente_compra_pedido_detalle
            $this->db->where('ID_Pedido_Detalle',$idProducto);
            $this->db->update('agente_compra_pedido_detalle',$datatoInsert);
            //check if all agente_compra_pedido_detalle with ID_PEDIDO_CABECERA=$idPedido have null in caja_master_URL
            $waos=$this->checkIfPedidoHasAllProductsRotulated($idPedido);
            if($waos==0){
                //update table agente_compra_order_steps where id=stepID
                $this->db->where('id',$stepID);
                $this->db->update('agente_compra_order_steps',array('status'=>"COMPLETED"));

                
            }
            return array('status'=>'success','message'=>$datatoInsert);
        }
        catch (Exception $e){
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }
    function checkIfPedidoHasAllProductsRotulated($idPedido){
        try{
            $query="SELECT COUNT(*) AS total
            FROM agente_compra_pedido_detalle AS acpd
            JOIN agente_compra_pedido_detalle_producto_proveedor AS acpdpp 
            ON acpdpp.ID_Pedido_Detalle = acpd.ID_Pedido_Detalle
            WHERE acpd.caja_master_URL IS NULL
            AND acpd.ID_Pedido_Cabecera = 1
            and acpdpp.Nu_Selecciono_Proveedor =1;";
            $query = $this->db->query($query);
            return $query->row()->total;
        }catch (Exception $e){
            echo $e->getMessage();  
            throw new Exception($e->getMessage());
        }
    }
    public function getPedidoData($idPedido){
        //get all data from table agente_compra_pedido_cabecera where ID_Pedido_Cabecera=$idPedido
        try{
            $idPedido = intval($idPedido);
            $this->db->close();

            $this->db->select('*');
            $this->db->from('agente_compra_pedido_cabecera');
            
            $this->db->where('ID_Pedido_Cabecera',$idPedido);

            $query = $this->db->get();
            return $query->row();
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }public function saveOrdenCompra($data){
        try{
            $total_rmb = $data['total-rmb'];
            $Ss_Tipo_Cambio = $data['tc'];
            $ID_Pedido_Cabecera = intval($data['idPedido']);
            $stepID = $data['stepID'];
            //update pedido cabecera table with total_rmb and Ss_Tipo_Cambio
            $this->db->where('ID_Pedido_Cabecera',$ID_Pedido_Cabecera);
            $this->db->update('agente_compra_pedido_cabecera',array('total_rmb'=>$total_rmb,'Ss_Tipo_Cambio'=>$Ss_Tipo_Cambio));
            //update table agente_compra_order_steps where id=stepID
            $this->db->where('id',$stepID);
            $this->db->update('agente_compra_order_steps',array('status'=>"COMPLETED"));
            return $data;
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function getPedidoPagos($idPedido){
        try{
            $this->db->select('ifnull(round(sum(acpdpp.Ss_Precio*acpd.Qt_Producto),2),0) as orden_total');
            $this->db->from('agente_compra_pedido_detalle acpd');
            $this->db->join('agente_compra_pedido_detalle_producto_proveedor acpdpp','acpdpp.ID_Pedido_Detalle =acpd.ID_Pedido_Detalle','left');
            $this->db->where('acpd.ID_Pedido_Cabecera',$idPedido);
            $this->db->where('acpdpp.Nu_Selecciono_Proveedor',1);
            $orden_total = $this->db->get()->row();
            //select sum of all payments_agente_compra_pedido.value with id_pedido=$idPedido
            $this->db->select('ifnull(round(sum(pacp.value),2),0) as pago_cliente');    
            $this->db->from('payments_agente_compra_pedido pacp');
            $this->db->where('pacp.id_pedido',$idPedido);
            $pago_cliente = $this->db->get()->row();

            $queryData =array_merge((array)$orden_total,(array)$pago_cliente);
            $pagosData = $this->getPedidosPagosDetails($idPedido);
            return [
                "data"=>$queryData,
                "pagos"=>$pagosData
            ];
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function getPedidosPagosDetails($idPedido){
        try{
            $this->db->select('*,pacp.id as idPayment');
            $this->db->from('payments_agente_compra_pedido pacp');
            $this->db->join('payment_types ptacp','ptacp.id = pacp.id_type_payment','left');

            $this->db->where('pacp.id_pedido',$idPedido);
            $queryData = $this->db->get();
            return $queryData->result();
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function savePagos($data,$files){
        $pathGarantizado = "assets/images/agentecompra/garantizados/" . $data['idPedido'] . "/pagos/";
        $pathPagos = "assets/images/agentecompra/orden-compra/" . $data['idPedido'] . "/pagos/";
        $pagosURLS = [];
        $index = 1;
    
        // Process payment files
        // foreach ($files as $key => $file) {
        //     if ($key === "pago-garantia") {
        //         continue;
        //     } else if (array_key_exists("pago-" . $index, $files)) {
        //         $file = $files["pago-" . $index];
        //         $fileURL = $this->uploadSingleFile($file, $pathPagos);
        //         $pagosURLS[$key . '_URL'] = $fileURL??$data[$key."_URL"];
        //     } else if ($data[$key . '_URL'] != "null") {
        //         $pagosURLS[$key . '_URL'] = $data[$key . '_URL']??$data[$key."_URL"];

        //     }
        //     $index++;
        // }
        foreach($data as $key=>$value){
            if($key=="pago-garantia"){
                continue;
            }else if(strpos($key,"pago-")!==false && strpos($key,"_ID")===false && $value!="null" && 
            strpos($key,"_URL")===false && strpos($key,"_ID")===false){ 
                $file = $files['pago-'.substr($key,5,1)];
                $num=substr($key,5,1);
                $fileURL = $this->uploadSingleFile($file, $pathPagos);
                $pagosURLS["pago-".$num. '_URL'] = $fileURL??$data["pago-".$num."_URL"];
            
            }
        }
        // Process liquidation file
        if (array_key_exists('liquidacion', $files)) {
            $fileURL = $this->uploadSingleFile($files['liquidacion'], $pathPagos);
            $pagosURLS['liquidacion_URL'] = $fileURL;
        } else if ($data['liquidacion_URL']) {
            $pagosURLS['liquidacion_URL'] = $data['liquidacion_URL'];
        }
    
        // Process payment guarantee file
        if (array_key_exists('pago-garantia', $files)) {
            $fileURL = $this->uploadSingleFile($files['pago-garantia'], $pathGarantizado);
            $pagosURLS['pago-garantia_URL'] = $fileURL??$data['pago-garantia_URL'];
        } else if ($data['pago-garantia_URL']) {
            $pagosURLS['pago-garantia_URL'] = $data['pago-garantia_URL'];
        }
    
        // Insert or update records in the database
        $index = 1;
        return $pagosURLS;
        foreach ($pagosURLS as $key => $value) {
            $dataToInsert = [
                'file_url' => $value,
                'id_pedido' => $data['idPedido'],
            ];
    
            if ($key == 'pago-garantia_URL') {
                $dataToInsert['id_type_payment'] = 1;
                $dataToInsert['value'] = intval($data['pago-garantia-value']);
                $this->updateOrInsertPayment($data['idPedido'], $data['pago-garantia_ID'], $dataToInsert);
            } else if ($key == 'liquidacion_URL') {
                $dataToInsert['id_type_payment'] = 3;
                $dataToInsert['value'] = 0;
                $this->updateOrInsertPayment($data['idPedido'], $data['liquidacion_ID'], $dataToInsert);
            } else {
                $dataToInsert['id_type_payment'] = 2;
                $num = substr($key, 5, 1);
                
                $idToCheck=-1;
                if(array_key_exists('pago-'.$num.'_ID',$data)){
                    $idToCheck=$data['pago-'.$num.'_ID']; 
                }

                $dataToInsert['value'] = intval($data['pago-' . $num . '-value']);
                $this->updateOrInsertPayment($data['idPedido'], $idToCheck, $dataToInsert);
                $index++;
            }
        }
        $total=$this->getPedidoPagos($data['idPedido'])['data'];
        if($total->orden_total<=$total->pago_cliente){
            $this->updateStep($data['step'],"COMPLETED"); 
        }
        return $total;
        return ['status' => 'success', 'message' => "Pagos guardados"];
    }
    function updateStep($idStep,$status){
        $data=array('status'=>$status,'id_permision_role'=>$this->user->Nu_Tipo_Privilegio_Acceso);
        $this->db->where('id',$idStep);
        $this->db->update('agente_compra_order_steps',$data);
    }   
    function updateOrInsertPayment($idPedido, $idPayment, $dataToInsert) {
        if(!$dataToInsert['file_url']){
            //delete record if file_url is null where id=idPayment
            $this->db->where('id', $idPayment);
            $this->db->delete('payments_agente_compra_pedido');
            return;
        }
        $this->db->where('id_pedido', intval($idPedido));
        $this->db->where('id', intval($idPayment));
        $query = $this->db->get('payments_agente_compra_pedido');
        $result = $query->row();
        
        if ($result ){
            // Update existing record
            $this->db->where('id', $result->id);
            $this->db->update('payments_agente_compra_pedido', $dataToInsert);
        } else {
            // Insert new record if not found
            $this->db->insert('payments_agente_compra_pedido', $dataToInsert);
        }
    }
    public function getSupplierProducts($idPedido){
        try{
            $sp="CALL ".$this->sp_suppliers."(".$idPedido.")";
            $query = $this->db->query($sp);
            return $query->result();
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }public function getProductData($idProducto){
        try{
            $this->db->select('*');
            $this->db->from('agente_compra_pedido_detalle');
            $this->db->where('ID_Pedido_Detalle',$idProducto);
            $query = $this->db->get();
            return $query->row();
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function saveCoordination($data,$files){
        $idPedido=$data['id-pedido'];
        $currentStep=$data['current-step'];
        foreach($data['item'] as  $key=>$row){
            if($row=="" || $row==null || $row=="null")continue;

            $producto_detalle=[
                'ID_Pedido_Detalle'=>$key,
                'product_code'=>$row[0]
            ];
            $this->db->where('ID_Pedido_Detalle',$key);
            $this->db->update('agente_compra_pedido_detalle',$producto_detalle); 
        }
        foreach($data['coordination'] as $key=>$row){
            $path='assets/images/coordination/orden-compra/'.$key;
            $pago1Url = isset($files['coordination']['name'][$key]['pago_1_file']) ? 
            $this->uploadSingleFile([
                'name' => $files['coordination']['name'][$key]['pago_1_file'],
                'type' => $files['coordination']['type'][$key]['pago_1_file'],
                'tmp_name' => $files['coordination']['tmp_name'][$key]['pago_1_file'],
                'error' => $files['coordination']['error'][$key]['pago_1_file'],
                'size' => $files['coordination']['size'][$key]['pago_1_file'],
            ],$path) : null;

        $pago2Url = isset($files['coordination']['name'][$key]['pago_2_file']) ? 
            $this->uploadSingleFile([
                'name' => $files['coordination']['name'][$key]['pago_2_file'],
                'type' => $files['coordination']['type'][$key]['pago_2_file'],
                'tmp_name' => $files['coordination']['tmp_name'][$key]['pago_2_file'],
                'error' => $files['coordination']['error'][$key]['pago_2_file'],
                'size' => $files['coordination']['size'][$key]['pago_2_file'],
            ],$path) : null;
            $producto_detalle=[
                'id_coordination'=>$key,
                'pago_1_value'=>$row['pago_1_value'],
                'pago_2_value'=>$row['pago_2_value'],
                
                'estado'=>$row['estado'],
                
            ];
            //if pago_1_file is not null
            if($pago1Url){
                $producto_detalle['pago_1_URL']=$pago1Url;
            }
            //if pago_2_URL is not null
            if($pago2Url){
                $producto_detalle['pago_2_URL']=$pago2Url;
            }
           //update agente_compra_coordination_supplier
            $this->db->where('id_coordination',$key);
            $this->db->update('agente_compra_coordination_supplier',$producto_detalle);
            //check if all suppliers in agente_compra_coordination_supplier have estado enum='COMPLETED' where id_pedido=
            $this->db->select('count(*) as total');
            $this->db->from('agente_compra_coordination_supplier');
            $this->db->where('id_pedido',$idPedido);
            $this->db->where('estado','COMPLETED');
            $query = $this->db->get();
            $result = $query->row();
            if($result->total==count($data['coordination'])){
                $this->updateStep($currentStep,"COMPLETED");
            }
            return ['status'=>'success','message'=>'Coordination saved'];
        }
            
    }public function getSupplierItems($ID_pedido,$ID_supplier,$ID_coordination){
        $this->db->select('
		ACPDPP.ID_Pedido_Detalle,
		ACPDPP.ID_Pedido_Cabecera,
		ACPDPP.ID_Pedido_Detalle_Producto_Proveedor,
		ACPDPP.Ss_Precio,
		ACPDPP.Qt_Producto_Moq,
		ACPDPP.Qt_Producto_Caja,
		ACPDPP.Qt_Cbm,
		ACPDPP.Nu_Dias_Delivery,
		ACPDPP.Ss_Costo_Delivery,
		ACPDPP.Txt_Nota,
		ACPDPP.Nu_Selecciono_Proveedor,
		ACPDPP.Qt_Producto_Caja_Final,
		ACPDPP.Txt_Nota_Final,
        S.id_supplier,
		S.name as nombre_proveedor,
        S.phone as celular_proveedor,
		ACPDPP.main_photo,
		ACPDPP.secondary_photo,
		ACPDPP.terciary_photo,
		ACPDPP.primary_video,
		ACPDPP.secondary_video,
		');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP');
        $this->db->join($this->table_suppliers . ' AS S', 'S.id_supplier = ACPDPP.ID_Entidad_Proveedor', 'left');
        $this->db->join('agente_compra_coordination_supplier ACS','ACS.id_supplier=S.id_supplier','left');
        $this->db->where('ACPDPP.Nu_Selecciono_Proveedor', "1");

        $this->db->where('ACPDPP.ID_Pedido_Cabecera', $ID_pedido);
        $this->db->where('ACPDPP.ID_Entidad_Proveedor', $ID_supplier);
        $this->db->where('ACS.id_pedido', $ID_pedido);
        $this->db->where('ACS.updated_at is null');
        $query = $this->db->get();
        return $query->result();
    }
    public function saveSupplierItems($arrPost,$data_files){
        
        $this->db->trans_begin();

        try {
            $existsInitialSupplierCoordination=[];

            $results = [];

            $filesKey = [
                "main_photo",
                "secondary_photo",
                "terciary_photo",
                "primary_video",
                "secondary_video",
            ];
            $results = $this->processFiles($data_files, null, $filesKey, $arrPost);
            $existsindex=0;
            foreach ($arrPost['addProducto'] as $key => $row) {
                $Txt_Url_Imagen_Proveedor = '';

                //if $results not have key path return error
                if (!array_key_exists('paths', $results)) {
                    return array(
                        'status' => 'error',
                        'message' => 'No se cargaron los archivos multimedia ',
                    );
                }
                // if(isset($data_files['addProveedor']) && !empty($data_files['addProveedor']) && !empty($data_files['addProveedor']['name'][$key])) {
                //     $_FILES['img_proveedor']['name'] = $data_files['addProveedor']['name'][$key];
                //     $_FILES['img_proveedor']['type'] = $data_files['addProveedor']['type'][$key];
                //     $_FILES['img_proveedor']['tmp_name'] = $data_files['addProveedor']['tmp_name'][$key];
                //     $_FILES['img_proveedor']['error'] = $data_files['addProveedor']['error'][$key];
                //     $_FILES['img_proveedor']['size'] = $data_files['addProveedor']['size'][$key];

                //     $config['upload_path'] = $path;
                //     $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
                //     $config['max_size'] = 3072;//1024 KB = 3 MB
                //     $config['encrypt_name'] = TRUE;
                //     $config['max_filename'] = '255';

                //     $this->load->library('upload', $config);
                //     if (!$this->upload->do_upload('img_proveedor')){
                //         $this->db->trans_rollback();
                //         return array(
                //             'status' => 'error',
                //             'message' => 'No se cargo imagen proveedor ' . strip_tags($this->upload->display_errors()),
                //         );
                //     } else {
                //         $arrUploadFile = $this->upload->data();
                //         $Txt_Url_Imagen_Proveedor = base_url($path . $arrUploadFile['file_name']);
                //     }
                // }
                $existsInitialSupplierCoordination[$existsindex]=true;
                $cantidad = $row['cantidad_oculta'];
                if (isset($row['cantidad'])) {
                    $cantidad = $row['cantidad'];
                    if ($row['cantidad'] < $row['cantidad_oculta']) {
                        $cantidad = $row['cantidad_oculta'];
                    }

                }

                $nota = '';
                if (isset($row['nota'])) {
                    $nota = nl2br($row['nota']);
                }

                $precio = $row['precio'];
                if ($row['precio'] < $row['precio_oculta']) {
                    $precio = $row['precio_oculta'];
                }

                $moq = $row['moq'];
                if ($row['moq'] < $row['moq_oculta']) {
                    $moq = $row['moq_oculta'];
                }

                $caja = $row['qty_caja'];
                if ($row['qty_caja'] < $row['caja_oculta']) {
                    $caja = $row['caja_oculta'];
                }

                $cbm = $row['cbm'];
                if ($row['cbm'] < $row['cbm_oculta']) {
                    $cbm = $row['cbm_oculta'];
                }

                $delivery = $row['delivery'];
                if ($row['delivery'] < $row['delivery_oculta']) {
                    $delivery = $row['delivery_oculta'];
                }

                $costo_delivery = $row['shipping_cost'];
                if ($row['shipping_cost'] < $row['costo_delivery_oculta']) {
                    $costo_delivery = $row['costo_delivery_oculta'];
                }

                $nota_historica = $row['nota_historica'];
                if (empty($row['nota_historica'])) {
                    $nota_historica = $row['nota_historica_oculta'];
                }

                $contacto_proveedor = $row['contacto_proveedor'];
                if (empty($row['contacto_proveedor'])) {
                    $contacto_proveedor = $row['contacto_proveedor'];
                }
                $existsSupplier = $this->db->get_where($this->table_suppliers, array('phone' => $row['celular_proveedor']))->row();
                $idSupplier = 0;
                if (empty($existsSupplier)) {
                    /// generate code recursively until it does not exist in supplier table
                    $code = $this->generateSupplierCode($row['nombre_proveedor']);
                    while ($this->db->get_where($this->table_suppliers, array('code' => $code))->num_rows() > 0) {
                        $code = $this->generateSupplierCode($row['nombre_proveedor']);
                    }

                    $arrSupplier = array(
                        "name" => $row['nombre_proveedor'],
                        "phone" => $row['celular_proveedor'],
                        "code" => $code,
                    );

                    if ($this->db->insert('suppliers', $arrSupplier) > 0) {
                        $idSupplier = $this->db->insert_id();
                    } else {
                        $this->db->trans_rollback();
                        return array(
                            'status' => 'error',
                            'message' => 'No registro proveedor',
                        );
                    }
                } else {
                    $idSupplier = $existsSupplier->id_supplier;
                }

                $arrActualizar[] = array(
                    'ID_Pedido_Detalle_Producto_Proveedor' =>intval( $row['proovedor-id']),
                    'Qt_Producto_Caja_Final' => $cantidad,
                    'Txt_Nota_Final' => $nota,
                    'Ss_Precio' => $precio,
                    'Qt_Producto_Moq' => $moq,
                    'Qt_Producto_Caja' => $caja,
                    'Qt_Cbm' => $cbm,
                    'Nu_Dias_Delivery' => $delivery,
                    'Ss_Costo_Delivery' => $costo_delivery,
                    'Txt_Nota' => nl2br($nota_historica),
                    'No_Contacto_Proveedor' => $contacto_proveedor,
                    'main_photo' => $results['paths'][$key]['main_photo'],
                    'secondary_photo' => $results['paths'][$key]['secondary_photo'],
                    'terciary_photo' => $results['paths'][$key]['terciary_photo'],
                    'primary_video' => $results['paths'][$key]['primary_video'],
                    'secondary_video' => $results['paths'][$key]['secondary_video'],
                    'ID_Entidad_Proveedor' => $idSupplier,
                );
                $cordinationID=$arrPost['modal_coordination_id'];
                if($cordinationID!=0){
                    $existsSupplierCoordination = $this->db->get_where('agente_compra_coordination_supplier', array('id_coordination' => $cordinationID,'id_supplier'=>$idSupplier))->row();
                    //if not exists insert 
                    if(empty($existsSupplierCoordination)){
                        $toInsert=array('id_supplier'=>$idSupplier,'id_pedido'=>$row['pedido-cabecera'],
                        'estado'=>"PENDIENTE");
                        $this->db->insert('agente_compra_coordination_supplier',$toInsert);
                        $existsInitialSupplierCoordination[$existsindex]=false;
                    }
                }
                $existsindex++;
            }
            //check if all items in $existsInitialSupplierCoordination are true
            
            $allItemsExists=false;
            foreach($existsInitialSupplierCoordination as $item){
                if($item==true){
                    $allItemsExists=true;
                    break;
                }
            }
            if(!$allItemsExists){
                //remove agente_compra_coordination_supplier with cordinationID
                $this->db->where('id_coordination',$cordinationID);
                $this->db->delete('agente_compra_coordination_supplier');   

            }
            
            $this->db->query('SET FOREIGN KEY_CHECKS=1');
            $status=$this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrActualizar, 'ID_Pedido_Detalle_Producto_Proveedor');
          
            if ($status === false) {
                $this->db->trans_rollback();
                
                return array('status' => 'error', 'message' => 'error al actualizar datos');
            } else {
                //registrar evento de notificacion
                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'Pedidos Garantizados',
                    'Cotización ' . $arrPost['Item_ECorrelativo_Editar'] . ' edito proveedor de ' . $arrPost['Item_Ename_producto_Editar'],
                    ''
                );

                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Datos actualizados');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    } public function cambiarEstadoOrden($idPedido,$estado){
        try{
            $this->db->where('ID_Pedido_Cabecera',$idPedido);
            $this->db->update('agente_compra_pedido_cabecera',array('id_estado_orden_compra'=>$estado));
            return ['status'=>'success','message'=>'Estado actualizado'];
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }public function getOrderProgressLabel($privilegio,$idPedido){
        try{
             //select count(id),(count  STATUS ENUM ="COMPLETED")from agente_compra_order_steps where id_pedido = $id_pedido and id_privilegio = $privilegio
            $this->db->select('count(id) as total, SUM(status = "COMPLETED") as completed');
            $this->db->from('agente_compra_order_steps');
            $this->db->where('id_pedido',$idPedido);
            $this->db->where('id_permision_role',$privilegio);
            return $this->db->get()->row();
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}