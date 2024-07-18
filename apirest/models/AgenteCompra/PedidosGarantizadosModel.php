<?php
require_once APPPATH . 'traits/SupplierTraits.php';
require_once APPPATH . 'traits/FileTrait.php';
class PedidosGarantizadosModel extends CI_Model
{
    use SupplierTraits, FileTrait;
    public $table = 'agente_compra_pedido_cabecera';
    public $table_suppliers = 'suppliers';
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
    public $table_tipo_documento_identidad = 'tipo_documento_identidad';
    public $table_importacion_grupal_cabecera = 'importacion_grupal_cabecera';
    public $table_pais = 'pais';
    public $table_agente_compra_correlativo = 'agente_compra_correlativo';
    public $table_coordination = "agente_compra_coordination_supplier";
    public $table_usuario_intero = 'usuario';
    private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio = 1;
    private $table_payments = "payments_agente_compra_pedido";
    public $order = array('Fe_Registro' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    public function _get_datatables_query($user)
    {
        $this->db->select($this->table . '.*, P.No_Pais,
        PAY.file_url,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		CORRE.Fe_Month, USRCHINA.No_Nombres_Apellidos AS No_Usuario')
            ->from($this->table)
            ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
            ->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
            ->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join')
            ->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_China', 'left')
            ->join($this->table_payments . ' AS PAY', 'PAY.id_pedido = ' . $this->table . '.ID_Pedido_Cabecera', 'left')
            ->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
            ->where_in($this->table . '.Nu_Estado', array(2, 3, 4, 8));
        if ($user->Nu_Tipo_Privilegio_Acceso == $this->personalChinaPrivilegio) {
            $this->db->where($this->table . '.ID_Usuario_Interno_China', $user->ID_Usuario);
        }
        $this->db->where("Fe_Emision_Cotizacion BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

        if (!empty($this->input->post('ID_Pedido_Cabecera'))) {
            $this->db->where($this->table . '.ID_Pedido_Cabecera', $this->input->post('ID_Pedido_Cabecera'));
        }

        if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($user)
    {
        $this->_get_datatables_query($user);

        $query = $this->db->get();
        return $query->result();
    }
    /**
     * This function loads the products for the pedidos garantizados table
     * @param $ID
     */
    public function get_by_id($ID)
    {
        $acceso = $this->user->Nu_Tipo_Privilegio_Acceso;

        $this->db->select('CORRE.Fe_Month, Nu_Estado_China,
		(SELECT Ss_Venta_Oficial FROM tasa_cambio WHERE ID_Empresa=1 AND Fe_Ingreso="' . dateNow('fecha') . '" LIMIT 1) AS yuan_venta,
		' . $this->table . '.*,
        (select count(*) from agente_compra_pedido_detalle_producto_proveedor where ID_Pedido_Cabecera=' . $this->table . '.ID_Pedido_Cabecera) as count_proveedor,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
        USR.No_Usuario,
        USR.Txt_Email,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
        IGPD.Txt_Producto_Ingles, IGPD.Txt_Description_Ingles,
		IGPD.ID_Pedido_Detalle, IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
		IGPD.Nu_Envio_Mensaje_Chat_Producto, TDI.No_Tipo_Documento_Identidad_Breve, ' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido');
        $this->db->from($this->table);
        $this->db->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'join');
        $this->db->join($this->table_agente_compra_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
        $this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
        $this->db->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join');
        $this->db->join('usuario AS USR', 'USR.ID_Usuario = ' . $this->table . '.ID_Usuario_Interno_China', 'left');
        $this->db->where($this->table . '.ID_Pedido_Cabecera', $ID);
        $query = $this->db->get();
        $query = $query->result();
        $sCorrelativoCotizacion = '';
        foreach ($query as $row) {
            $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0, 3)) . str_pad($row->Nu_Correlativo, 3, "0", STR_PAD_LEFT);
            $row->sCorrelativoCotizacion = $sCorrelativoCotizacion;
            $row->Nu_Tipo_Privilegio_Acceso = $this->user->Nu_Tipo_Privilegio_Acceso;
            $row->currentUser = $this->user->Txt_Email;
            $row->pending_messages = $this->getPendingMessagesCount($row->ID_Pedido_Detalle,$this->user);

        }
        $sCorrelativoCotizacion = $query[0]->sCorrelativoCotizacion;
        if ($acceso == $this->personalChinaPrivilegio || $acceso == $this->jefeChinaPrivilegio) {
            //get current estadochina from this pedido
            $query2 = $this->db->get_where($this->table, ['ID_Pedido_Cabecera' => $ID]);
            $estadoChina = $query2->row()->Nu_Estado_China;
            if ($estadoChina == 1) {
                $query2 = $this->cambiarEstadoChina($ID, 2, $sCorrelativoCotizacion);
            }
        }
        return $query;

    }

    public function get_by_id_excel($ID)
    {
        $this->db->select('
        A.Nu_Correlativo,
        A.Fe_Emision_Cotizacion,
        A.Ss_Tipo_Cambio,
        A.cotizacionCode,
        CORRE.Fe_Month,
        CLI.No_Entidad,
        CLI.Nu_Documento_Identidad,
        CLI.No_Contacto,
        CLI.Nu_Celular_Contacto,
        CLI.Txt_Email_Contacto,
        IGPD.Txt_Url_Imagen_Producto,
        IGPD.Txt_Producto,
        IGPD.Txt_Descripcion,
        IGPD.Qt_Producto,
        IGPD.ID_Pedido_Detalle,
        P.No_Pais,
        ACPDPP.Ss_Precio,
        ACPDPP.Qt_Producto_Moq,
        ACPDPP.Qt_Producto_Caja,
        ACPDPP.Qt_Cbm,
        ACPDPP.Nu_Dias_Delivery,
        ACPDPP.Ss_Costo_Delivery,
        ACPDPP.Txt_Nota,
        ACPDPP.unidad_medida,
        ACPDPP.kg_box,
    ');
        $this->db->from('agente_compra_pedido_cabecera A');
        $this->db->join('agente_compra_correlativo CORRE', 'CORRE.ID_Agente_Compra_Correlativo = A.ID_Agente_Compra_Correlativo');
        $this->db->join('agente_compra_pedido_detalle IGPD', 'IGPD.ID_Pedido_Cabecera = A.ID_Pedido_Cabecera');
        $this->db->join('entidad CLI', 'CLI.ID_Entidad = A.ID_Entidad');
        $this->db->join('pais P', 'P.ID_Pais = A.ID_Pais');
        $this->db->join('(
        SELECT
            ACPDPP1.*
        FROM agente_compra_pedido_detalle_producto_proveedor ACPDPP1
        LEFT JOIN (
            SELECT
                ID_Pedido_Detalle,
                COUNT(*) AS selected_count
            FROM agente_compra_pedido_detalle_producto_proveedor
            WHERE Nu_Selecciono_Proveedor = 1
            GROUP BY ID_Pedido_Detalle
        ) AS subquery
        ON ACPDPP1.ID_Pedido_Detalle = subquery.ID_Pedido_Detalle
        WHERE (subquery.selected_count IS NULL OR ACPDPP1.Nu_Selecciono_Proveedor = 1)
    ) ACPDPP', 'IGPD.ID_Pedido_Detalle = ACPDPP.ID_Pedido_Detalle', 'left');
        $this->db->where('A.ID_Pedido_Cabecera', $ID);
        $this->db->order_by('A.Nu_Correlativo', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }
    /**
     * This function loads the products in the pedidos garantizados table
     */
    public function getItemProveedor($ID)
    {
        $this->db->select('
		ACPC.Ss_Tipo_Cambio,
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
        ACPDPP.unidad_medida,
        ACPDPP.kg_box
		');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor . ' AS ACPDPP');
        $this->db->join($this->table . ' AS ACPC', 'ACPC.ID_Pedido_Cabecera = ACPDPP.ID_Pedido_Cabecera', 'join');
        $this->db->join($this->table_suppliers . ' AS S', 'S.id_supplier = ACPDPP.ID_Entidad_Proveedor', 'join');
        $this->db->where('ACPDPP.ID_Pedido_Detalle', $ID);
        $query = $this->db->get();
        return $query->result();
    }

    public function getItemImagenProveedor($ID)
    {
        $this->db->select('Txt_Url_Imagen_Producto');
        $this->db->from($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen);
        $this->db->where($this->table_agente_compra_pedido_detalle_producto_proveedor_imagen . '.ID_Pedido_Detalle_Producto_Proveedor', $ID);
        $query = $this->db->get();
        return $query->result();
    }

    public function actualizarElegirItemProductos($arrPost, $data_files)
    {
        $this->db->trans_begin();

        try {
            //array_debug($data_files['addProveedor']);
            $results = [];

            $filesKey = [
                "main_photo",
                "secondary_photo",
                "terciary_photo",
                "primary_video",
                "secondary_video",
            ];
            $results = $this->processFiles($data_files, null, $filesKey, $arrPost);
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

                $nota_historica = $row['notas'];

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
                    'ID_Pedido_Detalle_Producto_Proveedor' => $row['id_detalle'],
                    'Qt_Producto_Caja_Final' => $cantidad,
                    'Txt_Nota_Final' => $nota,
                    'Ss_Precio' => $precio,
                    'Qt_Producto_Moq' => $moq,
                    'Qt_Producto_Caja' => $caja,
                    'Qt_Cbm' => $cbm,
                    'Nu_Dias_Delivery' => $delivery,
                    'Ss_Costo_Delivery' => $costo_delivery,
                    'Txt_Nota' => urldecode($nota_historica),
                    'No_Contacto_Proveedor' => $contacto_proveedor,
                    'main_photo' => $results['paths'][$key]['main_photo'],
                    'secondary_photo' => $results['paths'][$key]['secondary_photo'],
                    'terciary_photo' => $results['paths'][$key]['terciary_photo'],
                    'primary_video' => $results['paths'][$key]['primary_video'],
                    'secondary_video' => $results['paths'][$key]['secondary_video'],
                    'unidad_medida' => $row['unidad_medida'],
                    'kg_box' => $row['kgbox'],
                    'ID_Entidad_Proveedor' => $idSupplier,
                );

            }

            $this->db->update_batch($this->table_agente_compra_pedido_detalle_producto_proveedor, $arrActualizar, 'ID_Pedido_Detalle_Producto_Proveedor');

            if ($this->db->trans_status() === false) {
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
    }

    public function elegirItemProveedor($id_detalle, $ID, $status, $sCorrelativoCotizacion, $sNameItem, $id_pedido, $id_supplier)
    {
        $query = "SELECT Nu_Selecciono_Proveedor FROM agente_compra_pedido_detalle_producto_proveedor WHERE ID_Pedido_Detalle = " . $id_detalle . " AND Nu_Selecciono_Proveedor=1";
        $objProveedor = $this->db->query($query)->row();
        $where = array('ID_Pedido_Detalle_Producto_Proveedor' => $ID);
        $data = array('Nu_Selecciono_Proveedor' => $status); //1=proveedor seleccionado
        if ($this->db->update($this->table_agente_compra_pedido_detalle_producto_proveedor, $data, $where) > 0) {
            $sElegirProveedor = ($status == 1 ? 'marco proveedor' : 'desmarco proveedor');
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'Pedidos Garantizados',
                'Cotización ' . $sCorrelativoCotizacion . ' ' . $sElegirProveedor . ' de ' . $sNameItem,
                ''
            );
            //get all
            $coordinationSupplier = array(
                'id_pedido' => $id_pedido,
                'id_supplier' => $id_supplier,
                'estado' => "PENDIENTE",
            );
            //check in $table_coordination if exists row with this id_pedido and id_supplier
            $query = $this->db->get_where($this->table_coordination, [
                'id_pedido' => $id_pedido,
                'id_supplier' => $id_supplier,
            ]);
            if ($query->num_rows() == 0 && $status == 1) {
                $this->db->insert($this->table_coordination, $coordinationSupplier);
            } else if ($query->num_rows() > 0 && $status == 0) {
                //set update_at to current date ROW with id_pedido and id_supplier

                $this->db->update($this->table_coordination, ['updated_at' => date('Y-m-d H:i:s', time())], ['id_pedido' => $id_pedido, 'id_supplier' => intval($id_supplier)]);
            } else if ($query->num_rows() > 0 && $status == 1) {
                //set updated_at as null
                $this->db->update($this->table_coordination, ['updated_at' => null], ['id_pedido' => $id_pedido, 'id_supplier' => $id_supplier]);
            }
            return array('status' => 'success', 'message' => 'Proveedor seleccionado');
        }

        return array('status' => 'error', 'message' => 'Error al seleccionar proveedor');
    }

    public function cambiarEstado($ID, $Nu_Estado, $ID_Usuario_Interno_Empresa_China)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array(
            'Nu_Estado' => $Nu_Estado,
            'ID_Usuario_Interno_China' => $ID_Usuario_Interno_Empresa_China,
        );

        if ($Nu_Estado == 5 || $Nu_Estado == 3) {

            /**
             * select ID_Pedido_Detalle,count(ID_Pedido_Detalle) as count from agente_compra_pedido_detalle_producto_proveedor acpdpp where ID_Pedido_Cabecera =231 group by ID_Pedido_Detalle ;
             */
            $query = "SELECT ID_Pedido_Detalle,count(ID_Pedido_Detalle) as suppliers_count from agente_compra_pedido_detalle_producto_proveedor acpdpp where ID_Pedido_Cabecera =" . $ID . " and acpdpp.Nu_Selecciono_Proveedor =1 group by ID_Pedido_Detalle ";
            $result = $this->db->query($query)->result();
            //check if exists count of ID_Pedido_Detalle is greater than 1
            $isValidToContinue = true;
            foreach ($result as $row) {
                if ($row->suppliers_count > 1 || $row->suppliers_count==0) {
                    $isValidToContinue = false;
                    break;
                }
            }
            if (!$isValidToContinue) {
                return array('status' => 'error', 'message' => 'Error al aprobar la cotización hay items con mas de un proveedor o sin proveedor elegido');
            }
            $arrDataTour = array(
                'ID_Pedido_Cabecera' => $ID,
                'ID_Usuario_Interno_China' => $ID_Usuario_Interno_Empresa_China,
                //'ID_Usuario_Interno_Empresa_China' => $ID_Usuario_Interno_Empresa_China
            );
            // $arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);

            $data = array_merge($data, array(
                'Fe_Emision_OC_Aprobada' => dateNow('fecha'),
            ));

            //marcar progreso de pedido completado 2/3
            $where_progreso = array(
                'ID_Pedido_Cabecera' => $ID,
                'Nu_ID_Interno' => 2,
            );
            $permissionRoles = [
                "agente" => $this->personalPeruPrivilegio,
                "agente_china" => $this->personalChinaPrivilegio,
                "jefe_china" => $this->jefeChinaPrivilegio,
            ];
            $stepsArray = $this->generatePurchaseOrderSteps($permissionRoles, $ID);
            $response = $this->db->insert_batch('agente_compra_order_steps', $stepsArray);
            // $data_progreso = array('Nu_Estado_Proceso' => 1);
            // $this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso);
        }

        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function generatePurchaseOrderSteps($roles, $idPedido): array
    {
        $steps = $this->HelperImportacionModel->generateOrderSteps($roles, $idPedido);
        return $steps;
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
                'Pedidos Garantizados',
                'Cotización ' . $sCorrelativo . ' cambio estado a ' . $arrEstadoRegistro['No_Estado'],
                ''
            );

            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function actualizarPedido($where, $data, $arrProducto, $arrProductoTable, $sCorrelativo)
    {
        $this->db->trans_begin();
        //upload $data['file-cotizacion'] in agente_compra_pedido_cabecera

        $fileCotizacion = $data['file_cotizacion'];
        $this->allowedExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp');
        $this->allowedContentTypes = array('application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/gif', 'image/webp');
        $pedido = $where['ID_Pedido_Cabecera'];

        $path = "assets/images/agentecompra/cotizaciones/" . $pedido . "/";
        $fileCotizacionURL = $this->uploadSingleFile($fileCotizacion, $path);
        $data['file_cotizacion'] = $fileCotizacionURL;
        $this->db->update('agente_compra_pedido_cabecera',
            ['file_cotizacion' => $fileCotizacionURL],
            ['ID_Pedido_Cabecera' => $pedido]);
        if (!empty($arrProducto)) {
            //localhost
            $path = "assets/images/productos/";
            //$path = "../../agentecompra.probusiness.pe/public_html/assets/images/productos/";
            $iCounter = 0;
            $_FILES['tmp_voucher'] = $_FILES['voucher'];
            foreach ($arrProducto as $row) {
                //SET IMAGEN
                $_FILES['voucher']['name'] = $_FILES['tmp_voucher']['name'][$iCounter];
                $_FILES['voucher']['type'] = $_FILES['tmp_voucher']['type'][$iCounter];
                $_FILES['voucher']['tmp_name'] = $_FILES['tmp_voucher']['tmp_name'][$iCounter];
                $_FILES['voucher']['error'] = $_FILES['tmp_voucher']['error'][$iCounter];
                $_FILES['voucher']['size'] = $_FILES['tmp_voucher']['size'][$iCounter];

                $config['upload_path'] = $path;
                $config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
                $config['max_size'] = 3096; //1024 KB = 1 MB
                $config['encrypt_name'] = true;
                $config['max_filename'] = '255';

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('voucher')) {
                    $this->db->trans_rollback();
                    return array(
                        'status' => 'error',
                        'message' => 'No se cargo imagen ' . $row['nombre_comercial'] . ' ' . strip_tags($this->upload->display_errors()),
                    );
                } else {
                    $arrUploadFile = $this->upload->data();
                    $Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

                    $Txt_Url_Imagen_Producto = str_replace("https://intranet.probusiness.pe/../../", "https://", $Txt_Url_Imagen_Producto);
                    $Txt_Url_Imagen_Producto = str_replace("public_html/", "", $Txt_Url_Imagen_Producto);
                }

                $arrSaleOrderDetail[$iCounter] = array(
                    'ID_Empresa' => $data['ID_Empresa'],
                    'ID_Organizacion' => $data['ID_Organizacion'],
                    'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
                    'Txt_Producto' => $row['nombre_comercial'],
                    'Txt_Descripcion' => nl2br($row['caracteristicas']),

                    'Qt_Producto' => $row['cantidad'],
                    'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,

                    'Txt_Url_Link_Pagina_Producto' => $row['link'],
                );
                if (array_key_exists('caracteristicas_ingles', $row)) {
                    $arrSaleOrderDetail[$iCounter]['Txt_Description_Ingles'] = nl2br($row['caracteristicas_ingles']);

                }if (
                    array_key_exists('txtproductoIngles', $row)) {
                    $arrSaleOrderDetail[$iCounter]['Txt_Producto_Ingles'] = $row['txtproductoIngles'];

                }

                ++$iCounter;
            }
            $this->db->insert_batch('agente_compra_pedido_detalle', $arrSaleOrderDetail);
        }

        //actualizar productos de tabla de cliente
        if (!empty($arrProductoTable)) {
            $arrayIndex = 0;
            foreach ($arrProductoTable as $row) {
                
                //array_debug($row);
                $arrSaleOrderDetailUPD[$arrayIndex] = array(
                    'ID_Pedido_Detalle' => $row['id_item'],
                    'Qt_Producto' => $row['cantidad'], //agergar input de cantidad
                    'Txt_Descripcion' => nl2br(urldecode($row['caracteristicas'])),

                );
                if (array_key_exists('caracteristicas_ingles', $row)) {
                    $arrSaleOrderDetailUPD[$arrayIndex]['Txt_Description_Ingles'] = nl2br(urldecode($row['caracteristicas_ingles']));

                }if (
                    array_key_exists('txtproductoIngles', $row)) {
                    $arrSaleOrderDetailUPD[$arrayIndex]['Txt_Producto_Ingles'] = $row['txtproductoIngles'];

                }
                $arrayIndex++;
            }
            $this->db->update_batch('agente_compra_pedido_detalle', $arrSaleOrderDetailUPD, 'ID_Pedido_Detalle');
        }

        $where_cabecera = array(
            'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
        );
        $this->db->update($this->table, $data, $where_cabecera);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
        } else {
            $notificacion = $this->NotificacionModel->procesarNotificacion(
                $this->user->No_Usuario,
                'Pedidos Garantizados',
                'Cotización ' . $sCorrelativo . ' se actualizo',
                ''
            );

            $this->db->trans_commit();
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
    }
    /**
     * This function adds a new supplier to the product of guaranteed orders table
     *
     */
    public function addPedidoItemProveedor($data, $data_files)
    {
        $this->db->trans_begin();

        //actualizar cabecera
        $results = [];
        $pedidoID = $data['EID_Pedido_Cabecera_item'];
        $correlativo = $data['Item_ECorrelativo'];
        $path = "assets/images/agentecompra/garantizados/" . $pedidoID . "/" . $data['EID_Pedido_Detalle_item'];
        $filesKey = [
            "main_photo",
            "secondary_photo",
            "terciary_photo",
            "primary_video",
            "secondary_video",
        ];
        $arrDetalle = [];
        $results = $this->processFiles($data_files, $path, $filesKey, null);
        foreach ($data['addProducto'] as $key => $row) {

            $Txt_Url_Imagen_Proveedor = '';

            //if $results not have key path return error
            if (!array_key_exists('paths', $results)) {
                return array(
                    'status' => 'error',
                    'message' => 'No se cargaron los archivos multimedia ',
                );
            }
            $existsSupplier = $this->db->get_where($this->table_suppliers, array('phone' => $row['celular_proveedor']))->row();
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
            $arrDetalle[] = array(
                'ID_Empresa' => $data['EID_Empresa_item'],
                'ID_Organizacion' => $data['EID_Organizacion_item'],
                'ID_Pedido_Cabecera' => $data['EID_Pedido_Cabecera_item'],
                'ID_Pedido_Detalle' => $data['EID_Pedido_Detalle_item'],
                'Ss_Precio' => $row['precio'],
                'Qt_Producto_Moq' => $row['moq'],
                'Qt_Producto_Caja' => $row['qty_caja'],
                'Qt_Cbm' => $row['cbm'],
                'Nu_Dias_Delivery' => $row['delivery'],
                'Ss_Costo_Delivery' => $row['shipping_cost'],
                'Txt_Nota' => urldecode($row['notas']),
                'No_Contacto_Proveedor' => $row['contacto_proveedor'],
                'Txt_Url_Imagen_Proveedor' => $Txt_Url_Imagen_Proveedor,
                "main_photo" => $results['paths'][$key]['main_photo'],
                "secondary_photo" => $results['paths'][$key]['secondary_photo'],
                "terciary_photo" => $results['paths'][$key]['terciary_photo'],
                "primary_video" => $results['paths'][$key]['primary_video'],
                "secondary_video" => $results['paths'][$key]['secondary_video'],
                'ID_Entidad_Proveedor' => $idSupplier,
                'unidad_medida' => $row['unidad_medida'],
                'kg_box' => $row['kgbox'],
            );
            $this->db->insert_batch('agente_compra_pedido_detalle_producto_proveedor', $arrDetalle);
            $arrDetalle = [];
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
            } else {
                $this->db->trans_commit();

                // registrar evento de notificacion
                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'Pedidos Garantizados',
                    'Cotización ' . $data['Item_ECorrelativo'] . ' nuevo proveedor de ITEM ' . $data['Item_Ename_producto'],
                    ''
                );
            }
        }
        $this->checkAllProductsWithSupplier($pedidoID, $correlativo);
        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');

    }
    public function checkAllProductsWithSupplier($idPedido, $correlativo)
    {
        /*
        SELECT
        acpd.ID_Pedido_Detalle,
        (SELECT COUNT(*)
        FROM agente_compra_pedido_detalle_producto_proveedor acpdp2
        WHERE acpdp2.ID_Pedido_Detalle = acpd.ID_Pedido_Detalle) AS product_count
        FROM
        agente_compra_pedido_detalle acpd
        LEFT JOIN
        agente_compra_pedido_detalle_producto_proveedor acpdp
        ON acpd.ID_Pedido_Detalle = acpdp.ID_Pedido_Detalle
        WHERE
        acpd.ID_Pedido_Cabecera = 222
        group by 1

         */
        $this->db->select('
            acpd.ID_Pedido_Detalle,
            (SELECT COUNT(*)
            FROM agente_compra_pedido_detalle_producto_proveedor acpdp2
            WHERE acpdp2.ID_Pedido_Detalle = acpd.ID_Pedido_Detalle) AS product_count
        ');
        $this->db->from('agente_compra_pedido_detalle acpd');
        $this->db->join('agente_compra_pedido_detalle_producto_proveedor acpdp', 'acpd.ID_Pedido_Detalle = acpdp.ID_Pedido_Detalle', 'left');
        $this->db->where('acpd.ID_Pedido_Cabecera', $idPedido);
        $this->db->group_by('1');
        $query = $this->db->get();
        $data = $query->result();
        if (count($data) > 0) {
            $allWithSupplier = true;
            foreach ($data as $row) {
                if ($row->product_count == 0) {
                    $allWithSupplier = false;
                    break;
                } else {

                }
            }
            if ($allWithSupplier) {
                $this->cambiarEstadoChina($idPedido, 3, $correlativo);
            }
        }
    }
    public function getDownloadImage($id)
    {
        $query = "SELECT Txt_Url_Imagen_Producto FROM agente_compra_pedido_detalle WHERE ID_Pedido_Detalle = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function generarCorrelativo()
    {
        $Nu_Correlativo = 0;
        $Fe_Year = ToYear(dateNow('fecha'));
        $Fe_Month = ToMonth(dateNow('fecha'));

        $objCorrelativo = $this->db->query("SELECT ID_Agente_Compra_Correlativo FROM agente_compra_correlativo WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row();
        if (is_object($objCorrelativo)) {
            $ID_Agente_Compra_Correlativo = $objCorrelativo->ID_Agente_Compra_Correlativo;
            $query = "UPDATE agente_compra_correlativo SET Nu_Correlativo=Nu_Correlativo + 1 WHERE ID_Agente_Compra_Correlativo=" . $ID_Agente_Compra_Correlativo;
            $this->db->query($query);
        } else {
            $query = "INSERT INTO agente_compra_correlativo(
ID_Empresa,
ID_Organizacion,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
" . $this->user->ID_Organizacion . ",
" . $Fe_Year . ",
" . $Fe_Month . ",
1
);";
            $this->db->query($query);
            $ID_Agente_Compra_Correlativo = $this->db->insert_id();
        }
        $Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM agente_compra_correlativo WHERE ID_Agente_Compra_Correlativo = " . $ID_Agente_Compra_Correlativo . " LIMIT 1")->row()->Nu_Correlativo;
        if ($Nu_Correlativo > 0) {
            return array(
                'status' => 'success',
                'result' => array(
                    'id_correlativo' => $ID_Agente_Compra_Correlativo,
                    'numero_correlativo' => $Nu_Correlativo,
                ),
            );
        }
        return array(
            'status' => 'error',
            'message' => 'Correlativo es: ' . $Nu_Correlativo,
        );
    }

    public function addFileProveedor($arrPost, $data_files)
    {
        if (isset($data_files['image_documento']['name'])) {
            $this->db->trans_begin();
            $path = "assets/images/garantizados/" . $arrPost['documento_pago_garantizado-id_cabecera'] . "/pagos/";
            $this->allowedContentTypes = array('image', 'application', 'text', 'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/pdf');
            $this->allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar', '7z');
            $this->maxFileSize = 20240;
            $Txt_Url_Imagen_Producto = $this->uploadSingleFile($data_files['image_documento'], $path);

            // $where = array('id_pedido' => $arrPost['documento_pago_garantizado-id_cabecera']);
            // $data = array('Txt_Url_Pago_Garantizado' => $Txt_Url_Imagen_Producto); //1=SI
            $data = array(
                "id_pedido" => $arrPost['documento_pago_garantizado-id_cabecera'],
                "file_url" => $Txt_Url_Imagen_Producto,
            );
            $status = $this->db->insert($this->table_payments, $data);

            if ($status === false) {
                $this->db->trans_rollback();
                return array('status' => 'error', 'message' => 'Error al insertar');
            } else {
                //$this->db->trans_rollback();
                //registrar evento de notificacion
                $notificacion = $this->NotificacionModel->procesarNotificacion(
                    $this->user->No_Usuario,
                    'Pedidos Garantizados',
                    'Cotización ' . $arrPost['documento_pago_garantizado-correlativo'] . ' se realizo pago garantía',
                    ''
                );

                $this->db->trans_commit();
                return array('status' => 'success', 'message' => 'Voucher guardado');
            }
        } else {
            return array('status' => 'error', 'message' => 'No existe archivo');
        }
    }

    public function descargarDocumentoPagoGarantizado($id)
    {
        $query = "SELECT file_url AS Txt_Url_Imagen_Producto FROM " . $this->table_payments . " WHERE id_pedido = " . $id . " LIMIT 1";
        return $this->db->query($query)->row();
    }

    public function sendMessage($data)
    {
        $this->db->trans_begin();

        $arrMessage = array(
            'ID_Empresa' => $data['chat_producto-ID_Empresa_item'],
            'ID_Organizacion' => $data['chat_producto-ID_Organizacion_item'],
            'ID_Pedido_Cabecera' => $data['chat_producto-ID_Pedido_Cabecera_item'],
            'ID_Pedido_Detalle' => $data['chat_producto-ID_Pedido_Detalle_item'],
        );

        if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { //1peru
            $arrMessageUser = array(
                'ID_Usuario_Remitente' => $this->user->ID_Usuario,
                'Txt_Usuario_Remitente' => nl2br($data['message_chat']),
            );
            //update nu estado in agente_compra_pedido_cabecera to 8
            $where = array('ID_Pedido_Cabecera' => $data['chat_producto-ID_Pedido_Cabecera_item']);
            $toUpdate = array('Nu_Estado' => 8); //1=SI
            $this->db->update('agente_compra_pedido_cabecera', $toUpdate, $where);
            //foreach message with   this id_pedido_detalle where ID_Remitente not this user AND ID_USUARIO_DESTINO = 0 UPDATE TO THIS USER
            $query="
            UPDATE agente_compra_pedido_detalle_chat_producto
            SET ID_Usuario_Remitente = ".$this->user->ID_Usuario."
            WHERE ID_Pedido_Detalle = ".$data['chat_producto-ID_Pedido_Detalle_item']." AND ID_Usuario_Remitente = 0 AND ID_Usuario_Destino != ".$this->user->ID_Usuario;
            $this->db->query($query);

        }

        if ($this->user->Nu_Tipo_Privilegio_Acceso == 2 || $this->user->Nu_Tipo_Privilegio_Acceso == 5) { //china
            $arrMessageUser = array(
                'ID_Usuario_Destino' => $this->user->ID_Usuario,
                'Txt_Usuario_Destino' => nl2br($data['message_chat']),
            );
            $query="
            UPDATE agente_compra_pedido_detalle_chat_producto
            SET ID_Usuario_Destino= ".$this->user->ID_Usuario."
            WHERE ID_Pedido_Detalle = ".$data['chat_producto-ID_Pedido_Detalle_item']." AND ID_Usuario_Destino = 0 AND ID_Usuario_Remitente != ".$this->user->ID_Usuario;
            $this->db->query($query);
        }

        $arrMessage = array_merge($arrMessage, $arrMessageUser);

        $this->db->insert('agente_compra_pedido_detalle_chat_producto', $arrMessage);

        $sql = "UPDATE agente_compra_pedido_detalle SET Nu_Envio_Mensaje_Chat_Producto=Nu_Envio_Mensaje_Chat_Producto+1 WHERE ID_Pedido_Detalle=" . $data['chat_producto-ID_Pedido_Detalle_item'];
        $this->db->query($sql);

        //$where = array('ID_Pedido_Detalle' => $data['chat_producto-ID_Pedido_Detalle_item']);
        //$data = array( 'Nu_Envio_Mensaje_Chat_Producto' => 'Nu_Envio_Mensaje_Chat_Producto+1');//1=SI
        //$this->db->update('agente_compra_pedido_detalle', $data, $where);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => 'error', 'message' => 'Error al enviar');
        } else {
            //$this->db->trans_rollback();
            $this->db->trans_commit();
            return array('status' => 'success', 'message' => 'Mensaje enviado');
        }
    }

    public function viewChatItem($id)
    {
        $query = "SELECT CHAT.*, USRR.No_Nombres_Apellidos AS No_Nombres_Apellidos_Remitente, USRD.No_Nombres_Apellidos AS No_Nombres_Apellidos_Destinatario FROM
agente_compra_pedido_detalle_chat_producto AS CHAT
LEFT JOIN usuario AS USRR ON(USRR.ID_Usuario = CHAT.ID_Usuario_Remitente)
LEFT JOIN usuario AS USRD ON(USRD.ID_Usuario = CHAT.ID_Usuario_Destino)
WHERE ID_Pedido_Detalle = " . $id . " ORDER BY CHAT.Fe_Registro ASC";
        if (!$this->db->simple_query($query)) {
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
            );
        }
        $arrResponseSQL = $this->db->query($query);
        if ($arrResponseSQL->num_rows() > 0) {
            return array(
                'status' => 'success',
                'result' => $arrResponseSQL->result(),
            );
        }

        return array(
            'status' => 'warning',
            'message' => 'No se encontro registro',
        );
    }

    public function asignarUsuarioPedidoChina($arrPost)
    {
        $where = array('ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera']);
        $data = array('ID_Usuario_Interno_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
        //$data = array( 'ID_Usuario_Interno_Empresa_China' => $arrPost['cbo-guardar_personal_china-ID_Usuario']);
        if ($this->db->update($this->table, $data, $where) > 0) {

            $where_progreso = array(
                'ID_Pedido_Cabecera' => $arrPost['guardar_personal_china-ID_Pedido_Cabecera'],
                'Nu_ID_Interno' => 1,
            );
            $data_progreso = array('Nu_Estado_Proceso' => 1);
            if ($this->db->update('proceso_agente_compra_pedido', $data_progreso, $where_progreso) > 0) {
                return array('status' => 'success', 'message' => 'Completado');
            } else {
                return array('status' => 'error', 'message' => 'Error al actualizar y agregar progreso compra');
            }
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function removerAsignarPedido($ID, $id_usuario)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('ID_Usuario_Interno_Empresa_China' => 0, 'ID_Usuario_Interno_China' => 0);
        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'message' => 'Se quitó asignación');
        }
        return array('status' => 'error', 'message' => 'Error al eliminar asignación pedido');
    }

    public function cambiarEstadoImpotacionIntegral($ID, $Nu_Estado, $sCorrelativo)
    {
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array('Nu_Importacion_Integral' => $Nu_Estado);
        if ($this->db->update($this->table, $data, $where) > 0) {
            return array('status' => 'success', 'message' => 'Actualizado');
        }
        return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }

    public function generarEstadoProcesoAgenteCompra($arrDataTour)
    {
        //var_dump($arrDataTour);
        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '1. Coordinación con Proveedores <br> A. Negociación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '5',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '2. Reserva de Booking',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '6',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '3. Recepción de carga',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '7',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
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
            'Nu_ID_Interno' => '8',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '5. Docs Exportación',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '9',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
        );

        $proceso_agente_compra_pedido[] = array(
            'ID_Empresa' => $this->user->ID_Empresa,
            'ID_Organizacion' => $this->user->ID_Organizacion,
            'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
            'No_Proceso' => '6. Despacho al Shipper / Forwarder',
            'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
            'Nu_Orden' => '1',
            'Nu_Estado_Proceso' => '0',
            'Nu_Estado_Visualizacion' => '1',
            'Nu_ID_Interno' => '10',
            'ID_Usuario_Interno_Empresa' => $arrDataTour['ID_Usuario_Interno_China'],
        );

        if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido) > 0) {
            return array('status' => 'success', 'message' => 'Registro guardado');
        }

        return array('status' => 'error', 'message' => 'Error al guardar');
    }
    public function getSuppliersByName($data)
    {
        $query = "SELECT id_supplier,name,phone FROM suppliers s
        join agente_compra_pedido_detalle_producto_proveedor acpdpp on acpdpp.ID_Entidad_Proveedor =s.id_supplier

         WHERE 
         acpdpp.ID_Pedido_Cabecera=" . $data['idPedido'] . "
         group by 1";
        return $this->db->query($query)->result();
    }
    public function removeSupplier($data)
    {
        try {
            $query = "DELETE FROM agente_compra_pedido_detalle_producto_proveedor WHERE ID_Pedido_Detalle_Producto_Proveedor = " . $data['idProveedor'] ."";
            return $this->db->query($query);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getPendingMessagesCount($id_detalle,$user)
    {
        $privilegio = $user->Nu_Tipo_Privilegio_Acceso;
        $idUsuario = $user->ID_Usuario;
        $query="";
        if($privilegio==1){
            $query = "SELECT count(*) count FROM agente_compra_pedido_detalle_chat_producto WHERE ID_Pedido_Detalle = " . $id_detalle . " AND ID_Usuario_Destino != " . $idUsuario . " AND ID_Usuario_Remitente = 0";

        }else{
            $query = "SELECT count(*) count FROM agente_compra_pedido_detalle_chat_producto WHERE ID_Pedido_Detalle = " . $id_detalle . " AND ID_Usuario_Remitente != " . $idUsuario . " AND ID_Usuario_Destino = 0";
        }
        return intval($this->db->query($query)->row()->count);
    }
}
