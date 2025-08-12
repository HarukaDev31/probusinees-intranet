<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/WhatsappTrait.php';
require_once APPPATH . 'traits/NotificationTrait.php';
require_once APPPATH . 'traits/WebSocketTrait.php';
require_once APPPATH . 'traits/MailTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
class ContenedorConsolidadoModel extends CI_Model
{
    use FileTrait, WebSocketTrait, WhatsappTrait, NotificationTrait, MailTrait;
    private $table_cliente = 'entidad';
    private $table_usuario = 'usuario';
    private $table = "carga_consolidada_contenedor";
    private $defaultHoursContactado = 1 * 60 * 24;
    private $defaultHoursInteresado = 1 * 60 * 48;
    private $table_pais = "pais";
    private $table_contenedor_steps = "contenedor_consolidado_order_steps";
    private $table_contenedor_cotizacion = "contenedor_consolidado_cotizacion";
    private $table_contenedor_cotizacion_crons = "contenedor_consolidado_cotizacion_crons";
    private $table_contenedor_cotizacion_proveedores = "contenedor_consolidado_cotizacion_proveedores";
    private $table_contenedor_documentacion_files = "contenedor_consolidado_documentacion_files";
    private $table_contenedor_documentacion_folders = "contenedor_consolidado_documentacion_folders";
    private $table_contenedor_tipo_cliente = "contenedor_consolidado_tipo_cliente";
    private $table_contenedor_cotizacion_documentacion = "contenedor_consolidado_cotizacion_documentacion";
    private $table_contenedor_almacen_documentacion = "contenedor_consolidado_almacen_documentacion";
    private $table_contenedor_almacen_inspection = "contenedor_consolidado_almacen_inspection";
    private $table_conteneodr_proveedor_estados_tracking = "contenedor_proveedor_estados_tracking";
    private $roleCotizador = "Cotizador";
    private $roleCoordinacion = "Coordinación";
    private $roleContenedorAlmacen = "ContenedorAlmacen";
    private $roleCatalogoChina = "CatalogoChina";
    private $rolesChina = ["CatalogoChina", "ContenedorAlmacen"];
    private $roleDocumentacion = "Documentacion";
    private $aNewContainer = "new-container";
    private $aNewConfirmado = "new-confirmado";
    private $aNewCotizacion = "new-cotizacion";
    private $cambioEstadoProveedor = "cambio-estado-proveedor";
    private $table_contenedor_cotizacion_final = "contenedor_consolidado_cotizacion_final";
    private $CONCEPT_PAGO_LOGISTICA = 1;
    private $CONCEPT_PAGO_IMPUESTO = 2;

    private $providerOrderStatus = [
        "NC" => 0,
        "C" => 1,
        "R" => 2,
        "NS" => 3,
        "INSPECTION" => 4,
        'LOADED' => 5,
        'NO LOADED' => 6
    ];
    private $providerCoordinacionOrderStatus = [
        "ROTULADO" => 0,
        'DATOS PROVEEDOR' => 1,
        'COBRANDO' => 2,
        'INSPECCIONADO' => 3,
        'RESERVADO' => 4,
        'NO RESERVADO' => 5,
        'EMBARCADO' => 6,
        'NO EMBARCADO' => 7,
    ];
    private $STATUS_NOT_CONTACTED = "NC";
    private $STATUS_CONTACTED = "C";
    private $STATUS_RECIVED = "R";
    private $STATUS_NOT_SELECTED = "NS";
    private $STATUS_INSPECTION = "INSPECTION";
    private $STATUS_LOADED = "LOADED";
    private $STATUS_NO_LOADED = "NO LOADED";
    private $STATUS_ROTULADO = "ROTULADO";
    private $STATUS_DATOS_PROVEEDOR = "DATOS PROVEEDOR";
    private $STATUS_COBRANDO = "COBRANDO";
    private $STATUS_INSPECCIONADO = "INSPECCIONADO";
    private $STATUS_RESERVADO = "RESERVADO";
    private $STATUS_NO_RESERVADO = "NO RESERVADO";
    private $STATUS_EMBARCADO = "EMBARCADO";
    private $STATUS_NO_EMBARCADO = "NO EMBARCADO";
    private $table_contenedor_cotizacion_proveedores_documentacion = "contenedor_consolidado_proveedores_documentacion";
    private $order = array('carga_consolidada_pedido_cabecera.Fe_Registro' => 'desc');
    private $table_contenedor_aduana_files = "carga_consolidada_aduana_files";
    private $table_pagos_concept = "cotizacion_coordinacion_pagos_concept";
    private $table_contenedor_consolidado_cotizacion_coordinacion_pagos = "contenedor_consolidado_cotizacion_coordinacion_pagos";
    private $table_consolidado_cron = "contenedor_consolidado_cotizacion_crons";
    private $table_bd_productos = "productos_importados_excel";
    private $table_bd_productos_rubro = "bd_productos";
    private $table_bd_productos_regulaciones = "bd_productos_regulaciones";
    private $table_bd_productos_regulaciones_tipo = "bd_productos_regulaciones_tipo";

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function index()
    {
        try {

            $this->db->select("*")
                ->from($this->table)
                ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join');
            if ($this->input->post('Filtro_Estado') != "0") {
                $this->db->where('estado', $this->input->post('Filtro_Estado'));
            }
            //if no user = doc where estado_documentacion != "COMPLETADO"
            if ($this->user->No_Grupo == "Documentacion") {
                $this->db->where('estado_documentacion !=', "COMPLETADO");
            }
            //if no grupo in ["Cotizador", "Coordinación"] where estado_documentacion = "COMPLETADO"
            if (in_array($this->user->No_Grupo, [$this->roleCotizador, $this->roleCoordinacion, $this->roleContenedorAlmacen, $this->roleCatalogoChina])) {
                $this->db->where('estado_china !=', "COMPLETADO");
            }
            $this->db->order_by('carga', 'desc');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function indexCompletados()
    {
        try {

            $this->db->select("*,
            (select count(id) from carga_consolidada_aduana_files where id_contenedor = carga_consolidada_contenedor.id) as file_count,
            ")
                ->from($this->table)
                ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join');

            if (in_array($this->user->No_Grupo, [$this->roleCotizador, $this->roleCoordinacion, $this->roleContenedorAlmacen, $this->roleCatalogoChina])) {
                $this->db->where('estado_china =', "COMPLETADO");
            }
            if ($this->user->No_Grupo == "Documentacion") {
                $this->db->where('estado_documentacion =', "COMPLETADO");
            }

            $this->db->order_by('carga', 'desc');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function getPaises()
    {
        $this->db->select("*")
            ->from($this->table_pais);
        $query = $this->db->get();
        return $query->result();
    }
    public function addNote($note, $idProveedor)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update('contenedor_consolidado_cotizacion_proveedores', ['nota' => $note]);

        if ($this->db->error()['code'] == 0) {
            return "success";
        } else {
            return false;
        }
    }
    public function getNotes($idProveedor)
    {
        $this->db->select('nota')
            ->from('contenedor_consolidado_cotizacion_proveedores')
            ->where('id', $idProveedor);
        $query = $this->db->get();
        return $query->row();
    }
    public function store($data)
    {
        //set data in table
        //if field carga not exists in data add tipo carga field "G. IMPORTACION"
        if (!array_key_exists('carga', $data)) {
            $data['tipo_carga'] = "G. IMPORTACION";
        }

        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            //{"project": "0", "role": "Cotizador", "user": "0", "message": "Prueba de comunicación en tiempo real","action":"new-container"}
            // $socketResponse = $this->sendEvent([
            //     "project" => "0",
            //     "role" => $this->roleCotizador,
            //     "user" => "0",
            //     "action" => $this->aNewContainer,
            //     "message" => "asdas",
            // ]);
            return [
                'id' => $this->db->insert_id(),
                'status' => true,
                'socketResponse' => [''],
            ];
        }
        return false;
    }
    public function update($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function show($id)
    {
        $this->db->select("*")
            ->from($this->table)->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join')
            ->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function delete($id)
    {
        //select all from table  $table_contenedor_cotizacion
        //set foreign key check to 0 and delete all files in folder and delete folder
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $this->db->where('id_contenedor', $id);
        $this->db->delete($this->table_contenedor_cotizacion);
        //select all from table  $table_contenedor_steps
        $this->db->where('id_pedido', $id);
        $this->db->delete($this->table_contenedor_steps);
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');


        $errors = $this->db->error();
        //if not exists error return success
        if ($errors['code'] == 0) {
            return "success";
        }
        return false;
    }
    public function generateSteps($steps, $stepsDocumentacion)
    {
        $this->db->insert_batch($this->table_contenedor_steps, $steps);
        $this->db->insert_batch($this->table_contenedor_steps, $stepsDocumentacion);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function getOrderProgress($idContenedor): array
    {
        try {
            $this->db->select('
            id,
            name,
            status,
            iconURL,
            CONCAT("Consolidado #",(select carga from carga_consolidada_contenedor where id =' . $idContenedor . ')) as contenedor_name
            ');
            $this->db->from($this->table_contenedor_steps);
            $this->db->where('id_pedido', $idContenedor);
            $this->db->order_by('id_order', 'asc');
            if ($this->user->No_Grupo == "Cotizador") {
                $this->db->limit(1);
            }
            if ($this->user->ID_Usuario == 28791) {
                //limit to 2 steps
                $this->db->limit(2);
            }
            if ($this->user->No_Grupo == "Documentacion") {
                //limit to 3 last steps
                $this->db->where('tipo', 'DOCUMENTACION');
            } else {
                //limit to 3 last steps
                $this->db->where('tipo', 'COTIZADOR');
            }
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function getContenedorCotizacion($idContenedor)
    {
        $this->db->select("
            CC.*,
            CC.id AS id_cotizacion,
            TC.name AS name,
            U.No_Nombres_Apellidos,
            CONCAT(
                C.carga,
                DATE_FORMAT(CC.fecha, '%d%m%y'),
                UPPER(LEFT(TRIM(CC.nombre), 3))
            ) AS COD
        ")
            ->from($this->table_contenedor_cotizacion . " AS CC")
            ->join($this->table . ' AS C', 'C.id = CC.id_contenedor')
            ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = CC.id_tipo_cliente', 'join')
            ->join($this->table_usuario . ' AS U', 'U.ID_Usuario = CC.id_usuario', 'left')
            ->where('id_contenedor', $idContenedor)
            ->order_by('id_cotizacion', 'asc');
       // Si el usuario es "Cotizador", filtrar por el id del usuario actual
        if ($this->user->No_Grupo == "Cotizador" && $this->user->ID_Usuario != 28791) {
            $this->db->where('CC.id_usuario', $this->user->ID_Usuario);
        }
        if ($this->user->No_Grupo != "Cotizador") {
            $this->db->where('CC.estado_cotizador', 'CONFIRMADO');
            if ($this->input->post('Filtro_Estado') != "0") {
                $fieldToFilter = [
                    'Coordinación' => 'CC.estado',
                    'ContenedorAlmacen' => 'CC.estado_china',
                    'CatalogoChina' => 'CC.estado_china',
                    'Documentacion' => 'CC.estado',
                ];
                $this->db->where($fieldToFilter[$this->user->No_Grupo], $this->input->post('Filtro_Estado'));
            }
        } else {
            if ($this->input->post('Filtro_Estado') != "0") {
                $this->db->where('CC.estado_cotizador', $this->input->post('Filtro_Estado'));
            }
        }
        if ($this->user->No_Grupo == "Cotizador") {
            $this->db->order_by('CC.fecha_confirmacion', 'asc');
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function getContenedorCotizacionPagos($idContenedor)
    {
        $this->db->select(
            "*,CC.id AS id_cotizacion, " .
                "(
                SELECT IFNULL(SUM(cccp.monto), 0) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept= ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND ccp.name = 'LOGISTICA'
            ) AS total_pagos, " .
                "(
                SELECT COUNT(*) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept = ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND ccp.name = 'LOGISTICA'
            ) AS pagos_count"
        )
            ->from($this->table_contenedor_cotizacion . " AS CC")
            ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = CC.id_tipo_cliente', 'left')
            ->where('CC.id_contenedor', $idContenedor)
            ->order_by('CC.id', 'asc');
        // Si el usuario es "Cotizador", filtrar por el id del usuario actual
        if ($this->user->No_Grupo == "Cotizador" && $this->user->ID_Usuario != 28791) {
            $this->db->where($this->table_contenedor_cotizacion . '.id_usuario', $this->user->ID_Usuario);
            //order by fecha_confirmacion asc

        }
        if ($this->user->No_Grupo != "Cotizador") {
            $this->db->where('estado_cotizador', 'CONFIRMADO');

            if ($this->input->post('Filtro_Estado') != "0") {
                $fieldToFilter = [
                    'Coordinación' => 'estado',
                    'ContenedorAlmacen' => 'estado_china',
                    'CatalogoChina' => 'estado_china',
                    'Documentacion' => 'estado',
                ];
                $this->db->where($fieldToFilter[$this->user->No_Grupo], $this->input->post('Filtro_Estado'));
            }
        } else {
            if ($this->input->post('Filtro_Estado') != "0") {

                $this->db->where('estado_cotizador', $this->input->post('Filtro_Estado'));
            }
        }
        if ($this->user->No_Grupo == "Cotizador") {
            $this->db->order_by('fecha_confirmacion', 'asc');
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function getClientesDocumentacionPagos($idContenedor)
    {
        $this->db->select(
            "*,CC.id AS id_cotizacion, " .
                "(
                SELECT IFNULL(SUM(cccp.monto), 0) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept= ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND ccp.name = 'LOGISTICA'
            ) AS total_pagos, " .
                "(
                SELECT COUNT(*) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept = ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND ccp.name = 'LOGISTICA'
            ) AS pagos_count"
        )
            ->from($this->table_contenedor_cotizacion . " AS CC")
            ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = CC.id_tipo_cliente', 'left')
            ->where('CC.id_contenedor', $idContenedor)
            ->order_by('CC.id', 'asc');
        // Si el usuario es "Cotizador", filtrar por el id del usuario actual
        if ($this->user->No_Grupo == "Cotizador" && $this->user->ID_Usuario != 28791) {
            $this->db->where($this->table_contenedor_cotizacion . '.id_usuario', $this->user->ID_Usuario);
            //order by fecha_confirmacion asc

        }
        if ($this->user->No_Grupo != "Cotizador") {
            $this->db->where('estado_cotizador', 'CONFIRMADO');

            if ($this->input->post('Filtro_Estado') != "0") {
                $fieldToFilter = [
                    'Coordinación' => 'estado',
                    'ContenedorAlmacen' => 'estado_china',
                    'CatalogoChina' => 'estado_china',
                    'Documentacion' => 'estado',
                ];
                $this->db->where($fieldToFilter[$this->user->No_Grupo], $this->input->post('Filtro_Estado'));
            }
        } else {
            if ($this->input->post('Filtro_Estado') != "0") {

                $this->db->where('estado_cotizador', $this->input->post('Filtro_Estado'));
            }
        }
        if ($this->user->No_Grupo == "Cotizador") {
            $this->db->order_by('fecha_confirmacion', 'asc');
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function getContenedorCotizacionProveedores($idContenedor)
    {
        //select from table_contenedor_cotizacion join usuario.ID_USUARIO id_usuario,in array json select proveedores from table_contenedor_cotizacion_proveedores where id_cotizacion= firstable.id_cotizacion
        $this->db->select("main.*,
        U.No_Nombres_Apellidos,
        (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'id', proveedores.id,
                    'qty_box', proveedores.qty_box,
                    'peso', proveedores.peso,
                    'cbm_total', proveedores.cbm_total,
                    'supplier', proveedores.supplier,
                    'code_supplier', proveedores.code_supplier,
                    'estados_proveedor', proveedores.estados_proveedor,
                    'estados', proveedores.estados,
                    'supplier_phone', proveedores.supplier_phone,
                    'cbm_total_china', proveedores.cbm_total_china,
                    'qty_box_china', proveedores.qty_box_china,
                    'id_proveedor', proveedores.id,
                    'products',proveedores.products,
                    'estado_china',proveedores.estado_china,
                    'arrive_date_china',proveedores.arrive_date_china,
                    'send_rotulado_status',proveedores.send_rotulado_status
                )
            )
            FROM " . $this->table_contenedor_cotizacion_proveedores . " proveedores
            WHERE proveedores.id_cotizacion = main.id
        ) as proveedores")
            ->from($this->table_contenedor_cotizacion . " as main")
            ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = main.id_tipo_cliente', 'join')
            ->join($this->table_usuario . ' AS U', 'U.ID_Usuario = main.id_usuario', 'left')
            ->where('main.id_contenedor', $idContenedor)
            ->order_by('main.id', 'asc');
        // Aplicar filtros solo si no son "0" (valor por defecto)
        $filtroState = $this->input->post('Filtro_State') ?? "0";
        $filtroStatus = $this->input->post('Filtro_Status') ?? "0";
        if ($this->user->No_Grupo != "Cotizador") {
            $this->db->where('estado_cotizador', 'CONFIRMADO');

            if ($this->input->post('Filtro_Estado') != "0") {
                $fieldToFilter = [
                    'Coordinación' => 'estado',
                    'ContenedorAlmacen' => 'estado_china',
                    'CatalogoChina' => 'estado_china',
                    'Documentacion' => 'estado',
                ];
                $this->db->where("main" . $fieldToFilter[$this->user->No_Grupo], $this->input->post('Filtro_Estado'));
            }
            if ($filtroState != "0") {
                $state = $this->db->escape_str($filtroState);
                $this->db->join(
                    $this->table_contenedor_cotizacion_proveedores . ' AS ccp',
                    'ccp.id_cotizacion = main.id',
                    'inner'
                )->where('ccp.estados', $state);
            }

            // Filtro Status (1:N) - Tabla cotizacion_proveedores
            if ($filtroStatus != "0") {
                $status = $this->db->escape_str($filtroStatus);
                $this->db->where("EXISTS (
                    SELECT 1 FROM " . $this->table_contenedor_cotizacion_proveedores . " p 
                    WHERE p.id_cotizacion = main.id 
                    AND p.estados_proveedor = '$status'
                )", null, false);
            }

            // Condición combinada cuando ambos filtros están activos
            if ($filtroState != "0" && $filtroStatus != "0") {
                $this->db->group_start()
                    ->where('ccp.estados', $state)
                    ->where("EXISTS (
                        SELECT 1 FROM " . $this->table_contenedor_cotizacion_proveedores . " p 
                        WHERE p.id_cotizacion = main.id 
                        AND p.estados_proveedor = '$status'
                        AND p.estados = '$state'
                    )", null, false)
                    ->group_end();
            }
        } else if ($this->user->No_Grupo == "Cotizador" && $this->user->ID_Usuario != 28791) {
            $this->db->where('main.id_usuario', $this->user->ID_Usuario);
            $this->db->order_by('fecha_confirmacion', 'asc');
        } else {
            if ($this->input->post('Filtro_Estado') != "0") {

                $this->db->where('main.estado_cotizador', $this->input->post('Filtro_Estado'));
            }
        }
        if ($this->user->No_Grupo == "Cotizador") {
            $this->db->order_by('main.fecha_confirmacion', 'asc');
        }
        $query = $this->db->get();
        $data = $query->result();
        // Aplicar filtro al JSON de proveedores si hay filtro activo
        if ($filtroStatus != "0") {
            $statusFiltro = $this->input->post('Filtro_Status');
            foreach ($data as $item) {
                $proveedores = json_decode($item->proveedores, true);
                if ($proveedores) {
                    $item->proveedores = json_encode(array_filter($proveedores, function ($prov) use ($statusFiltro) {
                        return $prov['estados_proveedor'] == $statusFiltro;
                    }));
                }
            }
        }
        if ($filtroState != "0") {
            $stateFiltro = $this->input->post('Filtro_State');
            foreach ($data as $item) {
                $proveedores = json_decode($item->proveedores, true);
                if ($proveedores) {
                    $item->proveedores = json_encode(array_filter($proveedores, function ($prov) use ($stateFiltro) {
                        return $prov['estados'] == $stateFiltro;
                    }));
                }
            }
        }
        // Obtener valores de los filtros (con validación básica)
        $filtroStatus = $this->input->post('Filtro_Status') ?? "0";
        $filtroState = $this->input->post('Filtro_State') ?? "0";
        log_message('error', 'Filtro Status: ' . $filtroStatus);
        // Aplicar filtros combinados en una sola pasada
        foreach ($data as $item) {
            if (empty($item->proveedores)) {
                continue;
            }

            $proveedores = json_decode($item->proveedores, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($proveedores)) {
                continue;
            }

            // Filtrado combinado en una sola operación
            $proveedoresFiltrados = array_filter($proveedores, function ($prov) use ($filtroStatus, $filtroState) {
                $cumpleStatus = ($filtroStatus === "0" || $prov['estados_proveedor'] === $filtroStatus);
                $cumpleState = ($filtroState === "0" || $prov['estados'] === $filtroState);
                return $cumpleStatus && $cumpleState;
            });

            // Reconstruir el JSON manteniendo la estructura
            $item->proveedores = json_encode(array_values($proveedoresFiltrados), JSON_UNESCAPED_UNICODE);
        }

        return $data;
    }
    public function downloadContenedorCotizacionProveedoresExcel($idContenedor)
    {
        $data = $this->getContenedorCotizacionProveedores($idContenedor);

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();

        // Headers
        $headers = [
            'Status',
            'N',
            'Buyer',
            'Productos',
            'Qty Box',
            'CBM Total',
            'Weight',
            'Supplier',
            'Code Supplier',
            'Phone Number',
            'Qty Box China',
            'CBM Total China',
            'Arrive Date China'
        ];

        // Write headers
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
        }

        $row = 2;
        foreach ($data as $item) {
            // Parse providers JSON
            $providers = json_decode($item->proveedores, true);

            // If multiple providers, we'll need to merge cells
            $providerCount = count($providers);
            $startRow = $row;

            // Main row data (non-provider specific)
            $sheet->setCellValue('A' . $row, $item->estado);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->No_Usuario);

            // Process providers
            foreach ($providers as $providerIndex => $provider) {
                $currentRow = $row + $providerIndex;

                $sheet->setCellValue('D' . $currentRow, $provider['products']);
                $sheet->setCellValue('E' . $currentRow, $provider['qty_box']);
                $sheet->setCellValue('F' . $currentRow, $provider['cbm_total']);
                $sheet->setCellValue('G' . $currentRow, $provider['peso']);
                $sheet->setCellValue('H' . $currentRow, $provider['supplier']);
                $sheet->setCellValue('I' . $currentRow, $provider['code_supplier']);
                $sheet->setCellValue('J' . $currentRow, $provider['supplier_phone']);
                $sheet->setCellValue('K' . $currentRow, $provider['qty_box_china']);
                $sheet->setCellValue('L' . $currentRow, $provider['cbm_total_china']);
                $sheet->setCellValue('M' . $currentRow, $provider['arrive_date_china']);
            }

            // Merge cells for main columns if multiple providers
            if ($providerCount > 1) {
                $sheet->mergeCells('A' . $startRow . ':A' . ($startRow + $providerCount - 1));
                $sheet->mergeCells('B' . $startRow . ':B' . ($startRow + $providerCount - 1));
                $sheet->mergeCells('C' . $startRow . ':C' . ($startRow + $providerCount - 1));
            }

            $row += $providerCount;
        }

        // Auto-size columns
        foreach (range('A', 'M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Prepare download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="container_quotation_providers.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    public function getContenedorClientes($idContenedor)
    {
        $this->db->select("
            CC.*,
            CC.id AS id_cotizacion,
            TC.name AS name,
            U.No_Nombres_Apellidos,
            CONCAT(
                C.carga,
                DATE_FORMAT(CC.fecha, '%d%m%y'),
                UPPER(LEFT(TRIM(CC.nombre), 3))
            ) AS COD
        ")
        ->from($this->table_contenedor_cotizacion . " AS CC")
        ->join($this->table . ' AS C', 'C.id = CC.id_contenedor')
        ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = CC.id_tipo_cliente', 'join')
        ->join($this->table_usuario . ' AS U', 'U.ID_Usuario = CC.id_usuario', 'left')
        ->where('CC.id_contenedor', $idContenedor)
        ->where('CC.estado_cliente IS NOT NULL')
        ->where('CC.estado_cotizador', 'CONFIRMADO');
        $estado = $this->input->post('estado') ?? "0";
        if ($estado != "0") {
            $this->db->where('CC.estado_cliente', $estado);
        }
        $query = $this->db->get();
        return $query->result();
    }
    public  function convertDateFormat($date)
    {
        $dateObject = DateTime::createFromFormat('d/m/Y', $date);
        return $dateObject ? $dateObject->format('Y-m-d') : null; // Devuelve null si la fecha no es válida
    }
    public function getCotizacionDataFinal($cotizacion)
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            //find sheet 1 and get cell b8 as nombre,cell b9 as documento,cell b10 as correo,cell b11 as telefono,i11 as volumen,e9 as fecha
            $sheet = $objPHPExcel->getSheet(0);
            $nombre = $sheet->getCell('C8')->getValue();
            $documento = $sheet->getCell('C9')->getValue();
            $correo = $sheet->getCell('C10')->getValue();
            $telefono = $sheet->getCell('C11')->getValue();
            $volumen = $sheet->getCell('J11')->getCalculatedValue();
            $valorCot = $sheet->getCell('K14')->getCalculatedValue();
            //get calculated value from cell e9
            $fecha = $sheet->getCell('F9')->getValue();
            if ($fecha == "=+TODAY()") {
                $fecha = date("Y-m-d");
            } else {
                $fecha = $this->convertDateFormat($fecha);
            }

            //get tipo cliente for e11
            $tipoCliente = $sheet->getCell('F11')->getValue();
            //find if exists in table contenedor_consolidado_tipo_cliente with name = $tipoCliente else create new and get id
            $idTipoCliente = $this->db->select('id')
                ->from($this->table_contenedor_tipo_cliente)
                ->where('name', $tipoCliente)
                ->get();
            if ($idTipoCliente->num_rows() == 0) {
                $this->db->insert($this->table_contenedor_tipo_cliente, ['name' => $tipoCliente]);
                $idTipoCliente = $this->db->insert_id();
            } else {
                $idTipoCliente = $idTipoCliente->row()->id;
            }
            if (trim($sheet->getCell('B23')->getValue()) == "ANTIDUMPING") {
                $monto = $sheet->getCell('K31')->getCalculatedValue();
                $fob = $sheet->getCell('K30')->getCalculatedValue();
                $impuestos = $sheet->getCell('K32')->getCalculatedValue();
                $logistica = $sheet->getCell('K31')->getCalculatedValue();
            } else {
                $monto = $sheet->getCell('K30')->getCalculatedValue();
                $fob = $sheet->getCell('K29')->getCalculatedValue();
                $logistica = $sheet->getCell('K30')->getCalculatedValue();
                $impuestos = $sheet->getCell('K31')->getCalculatedValue();
            }
            $tarifa = $monto / ($volumen <= 0 ? 1 : $volumen);
            $peso = $sheet->getCell('K9')->getCalculatedValue();
            return [
                'nombre' => $nombre,
                'telefono' => $telefono,
                'volumen' => $volumen,
                'id_tipo_cliente' => $idTipoCliente,
                'fecha' => $fecha,
                'valor_cot' => $valorCot,
                'monto' => $monto,
                'fob_final' => $fob,
                'impuestos_final' => $impuestos,
                'logistica_final' => $logistica,
                'tarifa' => $tarifa,
                'peso' => $peso
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getCotizacionData($cotizacion)
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            //DISABLE AUTOLOAD CALCULATIONS

            //find sheet 1 and get cell b8 as nombre,cell b9 as documento,cell b10 as correo,cell b11 as telefono,i11 as volumen,e9 as fecha
            $sheet = $objPHPExcel->getSheet(0);
            $nombre = $sheet->getCell('B8')->getValue();
            $documento = $sheet->getCell('B9')->getValue();
            $correo = $sheet->getCell('B10')->getValue();
            $telefono = $sheet->getCell('B11')->getValue();
            $volumen = $sheet->getCell('I11')->getCalculatedValue();
            $valorCot = $sheet->getCell('J14')->getCalculatedValue();
            //get calculated value from cell e9
            $fecha = $sheet->getCell('E9')->getValue();
            if ($fecha == "=+TODAY()") {
                $fecha = date("Y-m-d");
            } else {
                $fecha = $this->convertDateFormat($fecha);
            }
            $tipoCliente = $sheet->getCell('E11')->getValue();
            //find if exists in table contenedor_consolidado_tipo_cliente with name = $tipoCliente else create new and get id
            $idTipoCliente = $this->db->select('id')
                ->from($this->table_contenedor_tipo_cliente)
                ->where('name', $tipoCliente)
                ->get();
            if ($idTipoCliente->num_rows() == 0) {
                $this->db->insert($this->table_contenedor_tipo_cliente, ['name' => $tipoCliente]);
                $idTipoCliente = $this->db->insert_id();
            } else {
                $idTipoCliente = $idTipoCliente->row()->id;
            }
            if (trim($sheet->getCell('A23')->getValue()) == "ANTIDUMPING") {
                $monto = $sheet->getCell('J31')->getOldCalculatedValue();
                $fob = $sheet->getCell('J30')->getOldCalculatedValue();
                $impuestos = $sheet->getCell('J32')->getOldCalculatedValue();
                //get j24 and j26
                log_message('error', '20: ' . $sheet->getCell('J20')->getOldCalculatedValue());
                log_message('error', '21: ' . $sheet->getCell('J21')->getOldCalculatedValue());
                log_message('error', '22: ' . $sheet->getCell('J22')->getOldCalculatedValue());
                log_message('error', '23: ' . $sheet->getCell('J23')->getOldCalculatedValue());

                log_message('error', '24: ' . $sheet->getCell('J24')->getOldCalculatedValue());
                log_message('error', '26: ' . $sheet->getCell('J26')->getOldCalculatedValue());
                log_message('error', 'impuestos: ' . $impuestos);
            } else {
                $monto = $sheet->getCell('J30')->getOldCalculatedValue();
                $fob = $sheet->getCell('J29')->getOldCalculatedValue();
                $impuestos = $sheet->getCell('J31')->getOldCalculatedValue();
            }
            $tarifa = $monto / (($volumen <= 0 ? 1 : $volumen) < 1.00 ? 1 : ($volumen <= 0 ? 1 : $volumen));
            $peso = $sheet->getCell('I9')->getOldCalculatedValue();
            $highestRow = $sheet->getHighestRow();
            $qtyItem = 0;
            for ($row = 36; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell('A' . $row)->getValue();
                if (is_numeric($cellValue) && $cellValue > 0) {
                    $qtyItem++;
                }
            }
            return [
                'nombre' => $nombre,
                'documento' => $documento,
                'correo' => $correo,
                'telefono' => $telefono,
                'volumen' => $volumen,
                'id_tipo_cliente' => $idTipoCliente,
                'fecha' => $fecha,
                'valor_cot' => $valorCot,
                'monto' => $monto,
                'tarifa' => $tarifa,
                'peso' => $peso,
                'fob' => $fob,
                'impuestos' => $impuestos,
                'qty_item' => $qtyItem
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function incrementColumn($column, $increment = 1)
    {
        $column = strtoupper($column); // Asegurarse de que todas las letras sean mayúsculas
        $length = strlen($column);
        $number = 0;

        // Convertir la columna a un número
        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        // Incrementar el número
        $number += $increment;

        // Convertir el número de vuelta a una columna
        $newColumn = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $newColumn = chr(ord('A') + $remainder) . $newColumn;
            $number = intval(($number - 1) / 26);
        }

        return $newColumn;
    }
    public function getEmbarqueDataModified($cotizacion, $data)
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            $rowProveedores = 4;
            $nameCliente = $objPHPExcel->getSheet(0)->getCell('B8')->getValue();
            $sheet2 = $objPHPExcel->getSheet(1);
            $columnStart = "C";
            $columnTotales = "";
            //if data if associative array convert to object
            if (is_array($data)) {
                $data = (object)$data;
            }
            $idContenedor = $data->id_contenedor;
            //get carga field from table with id=$idContenedor
            $this->db->select('carga')
                ->from($this->table)
                ->where('id', $idContenedor);
            $query = $this->db->get();
            $carga = $query->row()->carga;
            //complete to 0 to 2 digits if can converted to number else use last to chars
            $count = is_numeric($carga) ? str_pad($carga, 2, "0", STR_PAD_LEFT) : substr($carga, -2);
            $stop = false;

            while (!$stop) {
                $cell = $sheet2->getCell($columnStart . "3")->getValue();
                if (strtoupper(trim($cell)) == "TOTALES") {
                    $columnTotales = $columnStart;
                    $stop = true;
                } else {
                    $columnStart = $this->incrementColumn($columnStart);
                }
            }
            $rowCodeSupplier = 3;
            $rowCajasProveedor = 5;
            $rowPesoProveedor = 6;
            $rowVolProveedor = 8;
            //iterate from C TO $columnTotales and get values from row 5,6,8
            $columnStart = "C"; // Columna inicial
            $stop = false;
            $provider = 1;
            $currentRange = null;
            $processedRanges = []; // Almacena los rangos procesados
            $proveedores = []; // Lista de proveedores

            while (!$stop) {
                // Verifica si la columna actual es la última
                if ($columnStart == $columnTotales) {
                    $stop = true;
                } else {
                    // Obtiene el rango combinado de la celda actual
                    $cell = $sheet2->getCell($columnStart . $rowProveedores);
                    $currentRange = $cell->getMergeRange();

                    // Si el rango ya fue procesado, pasa a la siguiente columna
                    if ($currentRange && in_array($currentRange, $processedRanges)) {
                        $columnStart = $this->incrementColumn($columnStart);
                        continue;
                    }

                    // Agrega el rango actual a los rangos procesados
                    if ($currentRange) {
                        $processedRanges[] = $currentRange;
                    }

                    // Genera el código del proveedor
                    $codeSupplier = $sheet2->getCell($columnStart . $rowCodeSupplier)->getValue();
                    if (!$codeSupplier || $codeSupplier == '') {
                        $codeSupplier = $this->generateCodeSupplier($nameCliente, $count, $provider, $idContenedor);
                    }
                    // echo $sheet2->getCell($columnStart . $rowCajasProveedor)->getOldCalculatedValue();
                    $proveedores[] = [
                        'qty_box' => $this->getDataCell($sheet2, $columnStart . $rowCajasProveedor),
                        'peso' => $this->getDataCell($sheet2, $columnStart . $rowPesoProveedor),
                        'cbm_total' => $this->getDataCell($sheet2, $columnStart . $rowVolProveedor),
                        'id_cotizacion' => $data->id_cotizacion,
                        'id_contenedor' => $data->id_contenedor,
                        'code_supplier' => $codeSupplier,
                    ];

                    // Incrementa la columna y el contador del proveedor
                    $columnStart = $this->incrementColumn($columnStart);
                    $provider++;
                }
            }

            return $proveedores;
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
    public function getDataCell($sheet, $cell)
    {
        $value = "";
        $value = $sheet->getCell($cell)->getValue();
        if ($value == "") {
            $value = $sheet->getCell($cell)->getOldCalculatedValue();
        }
        if ($value == "") {
            $value = $sheet->getCell($cell)->getCalculatedValue();
        }
        return $value;
    }
    public function getEmbarqueData($cotizacion, $data)
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            $rowProveedores = 4;
            $nameCliente = $objPHPExcel->getSheet(0)->getCell('B8')->getValue();
            $sheet2 = $objPHPExcel->getSheet(1);
            $columnStart = "C";
            $columnTotales = "";
            //if data if associative array convert to object
            if (is_array($data)) {
                $data = (object)$data;
            }
            $idContenedor = $data->id_contenedor;
            //get carga field from table with id=$idContenedor
            $this->db->select('carga')
                ->from($this->table)
                ->where('id', $idContenedor);
            $query = $this->db->get();
            $carga = $query->row()->carga;
            //complete to 0 to 2 digits if can converted to number else use last to chars
            $count = is_numeric($carga) ? str_pad($carga, 2, "0", STR_PAD_LEFT) : substr($carga, -2);
            $stop = false;

            while (!$stop) {
                $cell = $sheet2->getCell($columnStart . "3")->getValue();
                if (strtoupper(trim($cell)) == "TOTALES") {
                    $columnTotales = $columnStart;
                    $stop = true;
                } else {
                    $columnStart = $this->incrementColumn($columnStart);
                }
            }
            $rowCajasProveedor = 5;
            $rowPesoProveedor = 6;
            $rowVolProveedor = 8;
            //iterate from C TO $columnTotales and get values from row 5,6,8
            $columnStart = "C"; // Columna inicial
            $stop = false;
            $provider = 1;
            $currentRange = null;
            $processedRanges = []; // Almacena los rangos procesados
            $proveedores = []; // Lista de proveedores

            while (!$stop) {
                // Verifica si la columna actual es la última
                if ($columnStart == $columnTotales) {
                    $stop = true;
                } else {
                    // Obtiene el rango combinado de la celda actual
                    $cell = $sheet2->getCell($columnStart . $rowProveedores);
                    $currentRange = $cell->getMergeRange();

                    // Si el rango ya fue procesado, pasa a la siguiente columna
                    if ($currentRange && in_array($currentRange, $processedRanges)) {
                        $columnStart = $this->incrementColumn($columnStart);
                        continue;
                    }

                    // Agrega el rango actual a los rangos procesados
                    if ($currentRange) {
                        $processedRanges[] = $currentRange;
                    }

                    // Genera el código del proveedor
                    $codeSupplier = $this->generateCodeSupplier($nameCliente, $count, $provider, $idContenedor);

                    // Agrega los datos del proveedor
                    $proveedores[] = [
                        'qty_box' => $sheet2->getCell($columnStart . $rowCajasProveedor)->getValue(),
                        'peso' => $sheet2->getCell($columnStart . $rowPesoProveedor)->getValue(),
                        'cbm_total' => $sheet2->getCell($columnStart . $rowVolProveedor)->getValue(),
                        'id_cotizacion' => $data->id_cotizacion,
                        'code_supplier' => $codeSupplier,
                        'id_contenedor' => $data->id_contenedor,
                    ];

                    // Incrementa la columna y el contador del proveedor
                    $columnStart = $this->incrementColumn($columnStart);
                    $provider++;
                }
            }

            return $proveedores;
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
    public function generateCodeSupplier($string, $idContenedor, $rowCount, $index)
    {
        $words = explode(" ", trim($string));
        $code = "";

        // Primeras 2 letras de las primeras 2 palabras (protegido)
        foreach ($words as $word) {
            if (strlen($code) >= 4) break; // Ya tenemos 4 caracteres (2 palabras)
            if (strlen($word) >= 2) { // Solo si la palabra tiene 2+ caracteres
                $code .= strtoupper(substr($word, 0, 2));
            }
        }

        // Completar con ceros y retornar
        $idContenedor = str_pad($idContenedor, 2, "0", STR_PAD_LEFT);
        return $code . $idContenedor . "-" . $rowCount;
    }
    public function storeCotizacion($data, $cotizacion)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $cotizacion['name'],
                    "type" => $cotizacion['type'],
                    "tmp_name" => $cotizacion['tmp_name'],
                    "error" => $cotizacion['error'],
                    "size" => $cotizacion['size']
                ],
                'assets/images/agentecompra/'
            );
            $dataToInsert = $this->getCotizacionData($cotizacion);
            $dataToInsert['cotizacion_file_url'] = $fileUrl;
            $dataToInsert['id_contenedor'] = $data['id_contenedor'];
            $dataToInsert['id_usuario'] = $this->user->ID_Usuario;
            $this->db->insert($this->table_contenedor_cotizacion, $dataToInsert);
            if ($this->db->affected_rows() > 0) {
                $idCotizacion = $this->db->insert_id();
                $dataToInsert['id_cotizacion'] = $idCotizacion;
                $dataEmbarque = $this->getEmbarqueData($cotizacion, $dataToInsert);
                log_message('error', 'Data embarque: ' . json_encode($dataEmbarque));
                $this->db->insert_batch($this->table_contenedor_cotizacion_proveedores, $dataEmbarque);
                //if db error return error
                if ($this->db->error()['code'] != 0) {
                    return [
                        'status' => "error",
                        'message' => $this->db->error()['message']
                    ];
                }
                if ($this->db->affected_rows() > 0) {
                    $nombre = $dataToInsert['nombre'];
                    //get f_cierre from table carga_consolidada_contenedor where id = $data['id_contenedor']
                    $this->db->select('f_cierre')
                        ->from($this->table)
                        ->where('id', $data['id_contenedor']);
                    $query = $this->db->get();
                    $f_cierre = $query->row()->f_cierre;

                    $message = 'Hola ' . $nombre . ' pudiste revisar la cotización enviada? 
Te comento que cerramos nuestro consolidado este ' . $f_cierre . ' Por favor si cuentas con alguna duda me avisas y puedo llamarte para aclarar tus dudas.';
                    $telefono = preg_replace('/\s+/', '', $dataToInsert['telefono']);
                    $telefono ? $telefono . '@c.us' : '';
                    $data_json = [
                        'message' => $message,
                        'phoneNumberId' => $telefono,
                    ];
                    $data = [
                        'id_contenedor' => $data['id_contenedor'],
                        'id_cotizacion' => $idCotizacion,
                        'created_at' => date('Y-m-d H:i:s'),
                        'data_json' => json_encode($data_json),
                        'execution_at' => date('Y-m-d H:i:s', strtotime('+' . $this->defaultHoursContactado . ' minutes')),
                        'time_between' => $this->defaultHoursContactado,
                        'status' => 'PENDING',
                    ];
                    $this->db->insert($this->table_contenedor_cotizacion_crons, $data);

                    //{"project": "0", "role": "Cotizador", "user": "0", "message": "Prueba de comunicación en tiempo real","action":"new-cotizacion"}
                    // $this->sendEvent([
                    //     "project" => "0",
                    //     "role" => $this->roleCotizador,
                    //     "user" => "0",
                    //     "action" => $this->aNewCotizacion,
                    //     "message" => "Nueva cotización",
                    // ]);
                    return [
                        'id' => $idCotizacion,
                        'status' => "success"
                    ];
                }
                return false;
            }
            if ($this->db->error()['code'] != 0) {
                return [
                    'status' => $this->db->error()['message'],
                    'message' => $this->db->error()['message']
                ];
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error en storeCotizacion: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => $e->getMessage()
            ];
        }
    }
    public function getTipoCliente()
    {
        $this->db->select("*")
            ->from($this->table_contenedor_tipo_cliente);
        $query = $this->db->get();
        return $query->result();
    }
    public function deleteCotizacionFile($id)
    {
        $this->db->select('cotizacion_file_url')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $id);
        $query = $this->db->get();
        $fileUrl = $query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion, ['cotizacion_file_url' => null]);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function deleteCotizacion($id)
    {
        //set foreign key check to 0 and delete all files in folder and delete folder
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->select('cotizacion_file_url')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $id);
        $query = $this->db->get();
        $fileUrl = $query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->delete($this->table_contenedor_cotizacion);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        //check if db error exists
        if ($this->db->error()['code'] != 0) {
            return [
                'status' => "error",
                'message' => $this->db->error()['message']
            ];
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        return false;
    }
    public function updateStatusCliente($id_cotizacion, $status)
    {
        if ($this->user->No_Grupo != 'Documentacion') return 'success';

        // Solo actualizar si el estado actual es "Pendiente"
        $this->db->select('status_cliente_doc');
        $this->db->from('contenedor_consolidado_cotizacion');
        $this->db->where('id', $id_cotizacion);
        $current = $this->db->get()->row();

        if (
            $current && ($current->status_cliente_doc === 'Pendiente' ||
                $current->status_cliente_doc === 'Incompleto')
        ) {
            $this->db->where('id', $id_cotizacion);
            $this->db->update('contenedor_consolidado_cotizacion', ['status_cliente_doc' => $status]);
            if ($this->db->affected_rows() > 0) {
                return 'success';
            } else {
                return [
                    'status' => "error",
                    'message' => $this->db->error()['message']
                ];
            }
        } else {
            return [
                'status' => "error",
                'message' => 'Solo se puede cambiar el estado si está en Pendiente.'
            ];
        }
    }
    public function refreshCotizacionFile($id)
    {
        log_message('error', 'Refresh cotizacion file with id: ' . $id);

        // Obtener información de la cotización
        $this->db->select('cotizacion_file_url,id_contenedor')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $id);
        $query = $this->db->get();

        if (!$query || $query->num_rows() == 0) {
            return [
                'status' => "error",
                'message' => 'No se encontró la cotización con el ID proporcionado.'
            ];
        }

        $row = $query->row();
        $fileUrl = $row->cotizacion_file_url;

        if (!$fileUrl) {
            return [
                'status' => "error",
                'message' => 'No se encontró la URL del archivo de cotización.'
            ];
        }

        log_message('info', 'Procesando archivo: ' . $fileUrl);

        // Intentar leer el archivo desde diferentes ubicaciones
        $fileContents = $this->readFileFromMultipleSources($fileUrl);

        if ($fileContents === false || $fileContents === null || strlen($fileContents) == 0) {
            log_message('error', 'No se pudo leer el archivo de cotización desde ninguna fuente: ' . $fileUrl);
            return [
                'status' => "error",
                'message' => 'El archivo de cotización no existe o no se puede leer.'
            ];
        }

        // Crear archivo temporal con extensión correcta
        $originalExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
        $tempFile = sys_get_temp_dir() . '/' . uniqid('cotizacion_', true) . '.' . $originalExtension;

        log_message('info', 'Creando archivo temporal: ' . $tempFile);

        $bytesWritten = file_put_contents($tempFile, $fileContents);
        if ($bytesWritten === false) {
            log_message('error', 'No se pudo crear el archivo temporal: ' . $tempFile);
            return [
                'status' => "error",
                'message' => 'Error al crear archivo temporal.'
            ];
        }

        log_message('info', 'Archivo temporal creado exitosamente. Tamaño: ' . $bytesWritten . ' bytes');

        // Verificar que el archivo temporal existe y es accesible
        if (!file_exists($tempFile) || !is_readable($tempFile)) {
            log_message('error', 'El archivo temporal no es accesible: ' . $tempFile);
            return [
                'status' => "error",
                'message' => 'El archivo temporal no es accesible.'
            ];
        }

        // Preparar datos para las funciones de procesamiento
        $cotizacionFile = [
            'tmp_name' => $tempFile,
            'name' => basename($fileUrl),
            'size' => $bytesWritten,
            'type' => $this->getMimeType($originalExtension)
        ];

        // Deshabilitar verificación de claves foráneas
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        try {
            // Procesar el contenido del archivo
            $dataToInsert = $this->getCotizacionData($cotizacionFile);

            if (!$dataToInsert) {
                throw new Exception('No se pudieron extraer datos del archivo de cotización');
            }

            // Eliminar el campo 'fecha' si existe
            if (isset($dataToInsert['fecha'])) {
                unset($dataToInsert['fecha']);
            }

            // Añadir la fecha de modificación
            $dataToInsert['updated_at'] = date('Y-m-d H:i:s');

            log_message('info', 'Datos extraídos del archivo: ' . json_encode($dataToInsert));

            // Actualizar la cotización principal
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion, $dataToInsert);

            if ($this->db->error()['code'] != 0) {
                throw new Exception('Error al actualizar cotización: ' . $this->db->error()['message']);
            }

            if (file_exists($tempFile)) {
                unlink($tempFile);
                log_message('info', 'Archivo temporal eliminado: ' . $tempFile);
            }

            // Rehabilitar verificación de claves foráneas
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            return [
                'status' => "success",
                'message' => 'Cotización actualizada exitosamente.'
            ];
        } catch (Exception $e) {
            // Limpiar archivo temporal en caso de error
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }

            // Rehabilitar verificación de claves foráneas en caso de error
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            log_message('error', 'Error en refreshCotizacionFile: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al procesar la cotización: ' . $e->getMessage()
            ];
        }
    }

    public function getCargasDisponibles()
    {
        $hoy = date('Y-m-d');
        $this->db->select('*')
            ->from($this->table)
            ->where('DATE(f_cierre) >=', $hoy)
            ->order_by('carga', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function moveCotizacionToConsolidado($idCotizacion, $idContenedorDestino)
    {
        // Actualiza la cotización principal
        $this->db->set('id_contenedor', $idContenedorDestino);
        $this->db->set('estado_cotizador', 'CONFIRMADO');
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $idCotizacion);
        $cotizacionUpdate = $this->db->update('contenedor_consolidado_cotizacion');

        // Actualiza los proveedores asociados
        $this->db->set('id_contenedor', $idContenedorDestino);
        $this->db->where('id_cotizacion', $idCotizacion);
        $proveedoresUpdate = $this->db->update('contenedor_consolidado_cotizacion_proveedores');

        return $cotizacionUpdate && $proveedoresUpdate;
    }

    /**
     * Intenta leer un archivo desde múltiples fuentes (local, servidor, URL)
     */
    private function readFileFromMultipleSources($fileUrl)
    {
        log_message('error', 'Intentando leer archivo: ' . $fileUrl);

        // 1. Intentar leer como ruta absoluta (servidor)
        if (file_exists($fileUrl)) {
            log_message('error', 'Leyendo archivo desde ruta absoluta: ' . $fileUrl);
            $content = file_get_contents($fileUrl);
            if ($content !== false) {
                log_message('error', 'Archivo leído exitosamente, tamaño: ' . strlen($content) . ' bytes');
                return $content;
            }
        }

        // 2. Intentar leer desde el directorio base de la aplicación (local)
        $localPath = FCPATH . ltrim($fileUrl, '/');
        if (file_exists($localPath)) {
            log_message('error', 'Leyendo archivo desde ruta local: ' . $localPath);
            $content = file_get_contents($localPath);
            if ($content !== false) {
                log_message('error', 'Archivo leído exitosamente, tamaño: ' . strlen($content) . ' bytes');
                return $content;
            }
        }

        // 3. Intentar leer desde el directorio de uploads común
        $uploadsPath = FCPATH . 'uploads/' . basename($fileUrl);
        if (file_exists($uploadsPath)) {
            log_message('error', 'Leyendo archivo desde uploads: ' . $uploadsPath);
            $content = file_get_contents($uploadsPath);
            if ($content !== false) {
                log_message('error', 'Archivo leído exitosamente, tamaño: ' . strlen($content) . ' bytes');
                return $content;
            }
        }

        // 4. Intentar leer desde el directorio de assets
        $assetsPath = FCPATH . 'assets/' . ltrim($fileUrl, '/');
        if (file_exists($assetsPath)) {
            log_message('error', 'Leyendo archivo desde assets: ' . $assetsPath);
            $content = file_get_contents($assetsPath);
            if ($content !== false) {
                log_message('error', 'Archivo leído exitosamente, tamaño: ' . strlen($content) . ' bytes');
                return $content;
            }
        }

        // 5. Si parece ser una URL, intentar leer remotamente
        if (filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            log_message('error', 'Intentando leer archivo remoto: ' . $fileUrl);

            // Método 1: file_get_contents con contexto
            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,*/*',
                        'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                        'Cache-Control: no-cache'
                    ],
                    'follow_location' => true,
                    'max_redirects' => 5
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);

            $content = @file_get_contents($fileUrl, false, $context);
            if ($content !== false && strlen($content) > 0) {
                log_message('error', 'Archivo remoto leído exitosamente con file_get_contents, tamaño: ' . strlen($content) . ' bytes');
                return $content;
            }

            // Método 2: cURL como fallback
            if (function_exists('curl_init')) {
                log_message('error', 'Intentando con cURL...');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fileUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,*/*',
                    'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                    'Cache-Control: no-cache'
                ]);

                $content = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($content !== false && $httpCode == 200 && strlen($content) > 0) {
                    log_message('error', 'Archivo remoto leído exitosamente con cURL, tamaño: ' . strlen($content) . ' bytes');
                    return $content;
                } else {
                    log_message('error', 'Error cURL: ' . $error . ', HTTP Code: ' . $httpCode);
                }
            }
        }

        // 6. Intentar con diferentes variaciones de ruta
        $possiblePaths = [
            APPPATH . '../' . $fileUrl,
            APPPATH . $fileUrl,
            dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $fileUrl,
            $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($fileUrl, '/')
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                log_message('error', 'Leyendo archivo desde ruta alternativa: ' . $path);
                $content = file_get_contents($path);
                if ($content !== false) {
                    log_message('error', 'Archivo leído exitosamente, tamaño: ' . strlen($content) . ' bytes');
                    return $content;
                }
            }
        }

        log_message('error', 'No se pudo encontrar el archivo en ninguna ubicación: ' . $fileUrl);
        return false;
    }

    /**
     * Obtiene el tipo MIME basado en la extensión del archivo
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv'
        ];

        return isset($mimeTypes[strtolower($extension)]) ? $mimeTypes[strtolower($extension)] : 'application/octet-stream';
    }

    public function uploadCotizacionFile($id, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $this->db->select('*')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $id);
            $query = $this->db->get();
            $data = $query->row();
            $fileUrl = $data->cotizacion_file_url;
            $idCotizacion = $data->id;
            unlink($fileUrl);
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/images/agentecompra/'
            );
            $dataToInsert = $this->getCotizacionData($file);
            $dataToInsert['cotizacion_file_url'] = $fileUrl;

            // Eliminar el campo 'fecha' si existe en la BD y actualizar el de 'updated_at'
            $dataToInsert['updated_at'] = date('Y-m-d H:i:s');
            $this->db->select('fecha')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $id);
            $queryCreated = $this->db->get();
            if ($queryCreated->num_rows() > 0 && $queryCreated->row()->fecha) {
                unset($dataToInsert['fecha']);
            }

            //disable foreign key check
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion, $dataToInsert);
            //truncate all data in table contenedor_consolidado_cotizacion_proveedores where id_cotizacion=$id

            if ($this->db->affected_rows() > 0) {
                // $this->db->where('id_cotizacion', $id);
                // $this->db->delete($this->table_contenedor_cotizacion_proveedores);
                $dataToInsert['id_cotizacion'] = $idCotizacion;
                $dataToInsert['id_contenedor'] = $data->id_contenedor;
                //get code_supplier from all rows in table_contenedor_cotizacion_proveedores where id_cotizacion=$id

                // Crear un array con los code_supplier de dataEmbarque
                // Obtener los code_supplier de la base de datos
                $this->db->select('code_supplier')
                    ->from($this->table_contenedor_cotizacion_proveedores)
                    ->where('id_cotizacion', $id);
                $query = $this->db->get();
                $codeSupplier = $query->result();

                // Convertir el resultado a un array de code_supplier
                $codeSupplier = array_map(function ($item) {
                    return $item->code_supplier;
                }, $codeSupplier);

                // Obtener los datos de embarque
                $dataEmbarque = $this->getEmbarqueDataModified($file, $dataToInsert);
                $codeSupplierEmbarque = array_column($dataEmbarque, 'code_supplier');
                // Recorrer los code_supplier de la base de datos
                foreach ($codeSupplier as $code) {
                    if (in_array($code, $codeSupplierEmbarque)) {
                        // Si existe en dataEmbarque, actualizar
                        $key = array_search($code, $codeSupplierEmbarque);
                        $dataToUpdate = $dataEmbarque[$key];
                        $this->db->where('code_supplier', $code)
                            ->where('id_cotizacion', $id)
                            ->update($this->table_contenedor_cotizacion_proveedores, $dataToUpdate);
                    } else {
                        // Si no existe en dataEmbarque, eliminar
                        $this->db->where('code_supplier', $code)
                            ->where('id_cotizacion', $id)
                            ->delete($this->table_contenedor_cotizacion_proveedores);
                    }
                }

                // Recorrer los code_supplier de dataEmbarque para insertar los nuevos
                foreach ($dataEmbarque as $data) {
                    if (!in_array($data['code_supplier'], $codeSupplier)) {
                        // Si no existe en la base de datos, insertar
                        $this->db->insert($this->table_contenedor_cotizacion_proveedores, $data);
                    }
                }

                if ($this->db->error()['code'] == 0) {
                    return "success";
                }
                log_message('error', 'Error en uploadCotizacionFile: ' . $this->db->error()['message']);
                return false;
            }
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error en uploadCotizacionFile: ' . $e->getMessage());
            return false;
        }
    }
    public function showCotizacion($id)
    {
        $this->db->select("*")
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function updateCotizacion($data, $cotizacion)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $this->db->select('cotizacion_file_url')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $data['id']);
        $query = $this->db->get();
        $fileUrl = $query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $cotizacion['name'],
                "type" => $cotizacion['type'],
                "tmp_name" => $cotizacion['tmp_name'],
                "error" => $cotizacion['error'],
                "size" => $cotizacion['size']
            ],
            'assets/images/agentecompra/'
        );
        $data['cotizacion_file_url'] = $fileUrl;
        $this->db->where('id', $data['id']);
        $this->db->update($this->table_contenedor_cotizacion, $data);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateEstadoCotizacion($id, $estado)
    {
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateEstado($id, $estado)
    {
        //if no_grupo is  roleContenedorAlmacen
        try {
            if (in_array($this->user->No_Grupo, $this->rolesChina)) {
                $this->db->set('estado_china', $estado);
                $this->db->where('id', $id);
                $this->db->update($this->table);
            } else {
                $this->db->set('estado', $estado);
                $this->db->where('id', $id);
                $this->db->update($this->table);
            }

            if ($this->db->error()['code'] == 0) {
                return "success";
            }
            $errors = $this->db->error();

            return false;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function updateEstadoDocumentacion($id, $estado)
    {
        $this->db->set('estado_documentacion', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function showClientesDocumentacion($id)
    {
        $this->db->select("
    main.*,
    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'id', docs.id,
                'file_url', docs.file_url,
                'folder_name', docs.name,
                'id_proveedor', docs.id_proveedor
            )
        )
        FROM " . $this->table_contenedor_cotizacion_documentacion . " docs
        WHERE docs.id_cotizacion = main.id
    ) as files,
    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'id', almacen_docs.id,
                'file_url', almacen_docs.file_path,
                'folder_name', almacen_docs.file_name,
                'file_name', almacen_docs.file_name,
                'id_proveedor', almacen_docs.id_proveedor,
                'file_ext',almacen_docs.file_ext
            )
        )
        FROM " . $this->table_contenedor_almacen_documentacion . " almacen_docs
        WHERE almacen_docs.id_cotizacion = main.id
    ) as files_almacen_documentacion,
    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'code_supplier', prov.code_supplier,
                'id', prov.id,
                'volumen_doc', prov.volumen_doc,
                'valor_doc', prov.valor_doc,
                'factura_comercial', prov.factura_comercial,
                'excel_confirmacion', prov.excel_confirmacion,
                'packing_list', prov.packing_list
            )
        )
        FROM " . $this->table_contenedor_cotizacion_proveedores . " prov
        WHERE prov.id_cotizacion = main.id
    ) as providers,
     (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                 'id', inspection_docs.id,
                'file_url', inspection_docs.file_path,
                'file_name', inspection_docs.file_name,
                'id_proveedor', inspection_docs.id_proveedor,
                'file_ext',inspection_docs.file_type
            )
        )
        FROM " . $this->table_contenedor_almacen_inspection . " inspection_docs
        WHERE inspection_docs.id_cotizacion = main.id
    ) as files_almacen_inspection
")
            ->from($this->table_contenedor_cotizacion . " as main")
            ->where('main.id', $id)
            ->where('main.estado is not null');

        $query = $this->db->get();
        $result = $query->row();

        return $result;
    }
    public function createClienteDocumentacion($id, $name, $file, $idProveedor)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            'assets/images/agentecompra/'
        );
        $this->db->insert($this->table_contenedor_cotizacion_documentacion, [
            'id_cotizacion' => $id,
            'name' => $name,
            'file_url' => $fileUrl,
            'id_proveedor' => $idProveedor
        ]);
        if ($this->db->affected_rows() > 0) {
            return ['status' => "success"];
        }
        return false;
    }
    public function deleteClienteDocumentacionFile($id)
    {
        try {

            $this->db->select('file_url')
                ->from($this->table_contenedor_cotizacion_documentacion)
                ->where('id', $id);
            $query = $this->db->get();
            $fileUrl = $query->row()->file_url;
            unlink($fileUrl);
            $this->db->delete($this->table_contenedor_cotizacion_documentacion, ['id' => $id]);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function updateClienteDocumentacion($data, $files)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = null;
            //if exists file_comercial in files update file_comercial
            if (isset($files['file_comercial'])) {
                $this->db->select('factura_comercial')
                    ->from($this->table_contenedor_cotizacion_proveedores)
                    ->where('id', $data['idProveedor']);
                $query = $this->db->get();
                $fileUrl = $query->row()->file_url;
                unlink($fileUrl);
                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['file_comercial']['name'],
                        "type" => $files['file_comercial']['type'],
                        "tmp_name" => $files['file_comercial']['tmp_name'],
                        "error" => $files['file_comercial']['error'],
                        "size" => $files['file_comercial']['size']
                    ],
                    'assets/images/agentecompra/'
                );
                $data['factura_comercial'] = $fileUrl;
            }
            if (isset($files['excel_confirmacion'])) {
                $this->db->select('excel_confirmacion')
                    ->from($this->table_contenedor_cotizacion_proveedores)
                    ->where('id', $data['idProveedor']);
                $query = $this->db->get();
                $fileUrl = $query->row()->file_url;
                unlink($fileUrl);

                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['excel_confirmacion']['name'],
                        "type" => $files['excel_confirmacion']['type'],
                        "tmp_name" => $files['excel_confirmacion']['tmp_name'],
                        "error" => $files['excel_confirmacion']['error'],
                        "size" => $files['excel_confirmacion']['size']
                    ],
                    'assets/images/agentecompra/'
                );
                $data['excel_confirmacion'] = $fileUrl;
            }
            if (isset($files['packing_list'])) {
                $this->db->select('packing_list')
                    ->from($this->table_contenedor_cotizacion_proveedores)
                    ->where('id', $data['idProveedor']);
                $query = $this->db->get();
                $fileUrl = $query->row()->file_url;
                unlink($fileUrl);
                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['packing_list']['name'],
                        "type" => $files['packing_list']['type'],
                        "tmp_name" => $files['packing_list']['tmp_name'],
                        "error" => $files['packing_list']['error'],
                        "size" => $files['packing_list']['size']
                    ],
                    'assets/images/agentecompra/'
                );
                $data['packing_list'] = $fileUrl;
            }
            //remove id from data array
            $idCotizacion = $data['id'];
            unset($data['id']);
            $idProveedor = $data['idProveedor'];
            unset($data['idProveedor']);
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, $data);
            //get sum of all valor_doc and all valor_doc and update table cotizacion
            $this->db->select('SUM(valor_doc) as total_valor_doc, SUM(volumen_doc) as total_volumen_doc')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id_cotizacion', $idCotizacion);
            $query = $this->db->get();
            $result = $query->row();
            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion, ['valor_doc' => $result->total_valor_doc, 'volumen_doc' => $result->total_volumen_doc]);

            if ($this->db->error()['code'] != 0) {
                return false;
            }
            return "success";
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function uploadListaEmbarque($idCotizacion, $idContenedor, $file)
    {
        //read excel and get data from row 5 to more get D and o values foreach row
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/images/agentecompra/'
            );

            $this->db->where('id', $idContenedor);
            $this->db->update($this->table, ['lista_embarque_url' => $fileUrl]);
            $this->verifyContainerIsCompleted($idContenedor);

            if ($this->db->affected_rows() > 0) {
                return [
                    'status' => "success",
                    'message' => "Lista de embarque actualizada"
                ];
            }
            if ($this->db->error()['code'] != 0) {
                return [
                    'status' => "error",
                    'message' => $this->db->error()['message']
                ];
            }

            return [
                'status' => "error",
                'message' => "No se pudo actualizar la lista de embarque"
            ];
        } catch (Exception $e) {
            return [
                'status' => "error",
                'message' => $e->getMessage()
            ];
        }
    }
    public function updateEstadoCliente($id, $estado)
    {
        $this->db->set('estado_cliente', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function verifyContainerIsCompleted($idcontenedor)
    {
        //IF lista_embarque_url  && bl_file_url is no null set estado COMPLETADO ELSE RECIBIENDO
        $this->db->select('lista_embarque_url')
            ->from($this->table)
            ->where('id', $idcontenedor);
        $query = $this->db->get();
        $listaEmbarque = $query->row()->lista_embarque_url;
        //FIND IF EXISTS PROVEEDOR WITH ESTADOS = DATOS PROVEEDOR
        $this->db->select('estado')
            ->from($this->table_contenedor_cotizacion)
            ->where('id_contenedor', $idcontenedor);
        $query = $this->db->get();
        $estadoProveedores = $query->result();
        $estado = null;
        foreach ($estadoProveedores as $estadoProveedor) {
            if ($estadoProveedor->estado == "DATOS PROVEEDOR") {
                $estado = "DATOS PROVEEDOR";
                break;
            }
        }

        // Preparar los datos para actualizar
        $updateData = array();

        if ($listaEmbarque != null) {
            $updateData['estado_china'] = 'COMPLETADO';
        } else if ($estado == "DATOS PROVEEDOR") {
            // No hacer nada
        } else {
            if ($this->user->No_Grupo == 'Coordinación') {
                $updateData['estado'] = 'RECIBIENDO';
            }
        }

        // Solo actualizar si hay datos para actualizar
        if (!empty($updateData)) {
            $this->db->where('id', $idcontenedor);
            $this->db->update($this->table, $updateData);
        }
    }
    public function validateListEmbarque($id)
    {
        $this->db->select('lista_embarque_url')
            ->from($this->table)
            ->where('id', $id);
        $query = $this->db->get();
        $fileUrl = $query->row()->lista_embarque_url;
        if ($fileUrl != null) {
            return true;
        }
        return false;
    }
    public function deleteFacturaComercial($id)
    {
        try {
            $this->db->select('factura_comercial')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id', $id);
            $query = $this->db->get();
            $fileUrl = $query->row()->factura_comercial;
            unlink($fileUrl);
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['factura_comercial' => null]);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function deletePackingList($id)
    {
        try {
            $this->db->select('packing_list')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id', $id);
            $query = $this->db->get();
            $fileUrl = $query->row()->packing_list;
            if ($fileUrl && file_exists($fileUrl)) {
                unlink($fileUrl);
            }
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['packing_list' => null]);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function deleteExcelConfirmacion($id)
    {
        try {
            $this->db->select('excel_confirmacion')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id', $id);
            $query = $this->db->get();
            $fileUrl = $query->row()->excel_confirmacion;
            unlink($fileUrl);
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['excel_confirmacion' => null]);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function deleteCliente($idCotizacion)
    {
        try {
            $this->db->select('cotizacion_file_url')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $idCotizacion);
            $query = $this->db->get();
            $fileUrl = $query->row()->cotizacion_file_url;
            unlink($fileUrl);
            $this->db->where('id', $idCotizacion);
            $this->db->delete($this->table_contenedor_cotizacion);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getDocumentationFolderFiles($id)
    {
        //select * from folders where id_cotizacion is null or $id and left join files where id_folder = id
        $this->db->select("main.*,files.id AS id_file,files.file_url,SUBSTRING_INDEX(SUBSTRING_INDEX(files.file_url, '.', -1), '/', 1) AS type,
        (select lista_embarque_url from carga_consolidada_contenedor cc where cc.id ='" . $id . "') as lista_embarque_url
        ")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id and files.id_contenedor = ' . $id, 'left');

        // First group: (main.id_contenedor = [id] OR main.id_contenedor IS NULL)
        $this->db->group_start()
            ->where('main.id_contenedor', $id)
            ->or_where('main.id_contenedor', null)
            ->group_end();

        // AND main.only_doc_profile = 0
        if ($this->user->No_Grupo != $this->roleDocumentacion) {
            $this->db->where('main.only_doc_profile', 0);
        }

        $query = $this->db->get();
        return $query->result();
    }
    public function uploadFileDocumentation($idFolder, $idContenedor, $file)
    {
        $this->maxFileSize = 10000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            'assets/images/agentecompra/'
        );

        //find if exists file in table contenedor_consolidado_documentacion_files where id_folder=$idFolder and id_contenedor=$idContenedor
        //if exists delete
        $this->db->delete($this->table_contenedor_documentacion_files, ['id_folder' => $idFolder, 'id_contenedor' => $idContenedor]);
        $this->db->insert($this->table_contenedor_documentacion_files, [
            'id_folder' => $idFolder,
            'file_url' => $fileUrl,
            'id_contenedor' => $idContenedor
        ]);
        if ($idFolder == 9 && isset($fileUrl)) {
            $objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);
            $this->importarProductosDesdeExcel($objPHPExcel, $idContenedor, $file['tmp_name']);
        }
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function deleteDocumentacionFolder($id)
    {
        try {
            //delete all files with same id_folder and unlink files and later delete folder
            $this->db->select('file_url')
                ->from($this->table_contenedor_documentacion_files)
                ->where('id_folder', $id);
            $query = $this->db->get();
            $files = $query->result();
            foreach ($files as $file) {
                unlink($file->file_url);
            }
            $files = $query->result();
            //delete files row
            $this->db->where('id_folder', $id);
            $this->db->delete($this->table_contenedor_documentacion_files);
            //delete folder row

            $this->db->where('id', $id);
            $this->db->delete($this->table_contenedor_documentacion_folders);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function deleteDocumentacionFile($id)
    {
        try {
            $this->db->select('file_url')
                ->from($this->table_contenedor_documentacion_files)
                ->where('id', $id);
            $query = $this->db->get();
            $fileUrl = $query->row()->file_url;
            unlink($fileUrl);
            $this->db->where('id', $id);
            $this->db->delete($this->table_contenedor_documentacion_files);
            if ($this->db->affected_rows() > 0) {
                return "success";
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function downloadDocumentacionZip($id)
    {
        try {
            $this->db->select("main.*,files.id AS id_file,files.file_url")
                ->from($this->table_contenedor_documentacion_folders . " as main")
                ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
                ->where('files.id_contenedor', $id);
            $query = $this->db->get();
            $folders = $query->result();

            // $zip = new ZipArchive;
            // $zipName = 'assets/images/agentecompra/contenedor_'.$id.'.zip';

            // if ($zip->open($zipName, ZipArchive::CREATE|ZipArchive::OVERWRITE) === TRUE) {
            $filePath = "C://xampp//htdocs//probusinees-intranet//assets//images//agentecompra//678a64a26787a.xlsx";
            $zipName = 'assets/images/agentecompra/contenedor_' . $id . '.zip';

            if (file_exists($zipName)) {
                unlink($zipName); // Eliminar el ZIP anterior si existe
            }

            $zip = new ZipArchive;

            // // Abre o crea el ZIP
            // if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            //     // Leer el contenido del archivo
            //     $content = file_get_contents($filePath);

            //     if ($content === false) {
            //         echo "Error al leer el archivo: $filePath\n";
            //     } else {
            //         // Agregar el contenido al ZIP
            //         $fileNameInZip = 'xd.xlsx'; // Nombre que tendrá el archivo dentro del ZIP
            //         if ($zip->addFromString($fileNameInZip, $content)) {
            //         } else {
            //         }
            //     }

            //     // Cierra el ZIP

            // } else {
            //     echo "Error al abrir el ZIP.\n";
            // }
            if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Agrega los archivos al ZIP
                foreach ($folders as $folder) {
                    // Paso 1: Decodificar la URL para obtener la ruta del archivo
                    $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $folder->file_url); // Extraer ruta relativa
                    $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
                    $fullPath = FCPATH . ltrim($decodedPath, '/'); // Construir la ruta completa

                    // Verificar si el archivo existe
                    if (file_exists($fullPath) && is_readable($fullPath)) {
                        $fileName = basename($decodedPath); // Obtener el nombre del archivo
                        // Normalizar el nombre si es necesario
                        if (function_exists('normalizer_normalize')) {
                            $fileName = normalizer_normalize($fileName, Normalizer::FORM_C);
                        }
                        // Asegurarse de que el nombre sea seguro
                        $fileName = preg_replace('/[\x00-\x1F\x7F<>:"\/\\|?*]/', '', $fileName);

                        // Agregar archivo al ZIP
                        if (!$zip->addFile($fullPath, $folder->folder_name . '/' . $fileName)) {
                            log_message('error', 'No se pudo agregar al ZIP: ' . $fullPath);
                        }
                    } else {
                        log_message('error', 'Archivo no encontrado o no legible: ' . $fullPath);
                    }
                }
                if ($zip->close() !== false) {
                } else {
                }
                return $zipName;
            }           // }
            return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            log_message('error', 'Error en ZIP: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
    public function downloadFacturaComercial($idContenedor)
    {
        //find in folder join files folser with name factura comercial,packing list  and lista partidas
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Factura Comercial');
        $query = $this->db->get();
        //find in query result folder_name factura comercial and get file_url
        $facturaComercial = $query->row();
        if (!$facturaComercial) {
            return ['status' => "error", 'message' => "No se encontró la factura comercial"];
        }

        $facturaComercial = $facturaComercial->file_url;
        //validate if factura_comercial is xls, xlsx,xlsm
        $path_parts = pathinfo($facturaComercial);
        $extension = $path_parts['extension'];
        if ($extension != "xls" && $extension != "xlsx" && $extension != "xlsm") {
            return ['status' => "error", 'message' => "La Factura Comercial no es un archivo de excel"];
        }
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Packing List');
        $query = $this->db->get();
        //find in query result folder_name packing list and get file_url
        $packingList = $query->row();
        if (!$packingList) {
            return ['status' => "error", 'message' => "No se encontró el packing list"];
        }
        $packingList = $packingList->file_url;
        //validate if packing list is xls, xlsx,xlsm
        $path_parts = pathinfo($packingList);
        $extension = $path_parts['extension'];
        if ($extension != "xls" && $extension != "xlsx" && $extension != "xlsm") {
            return ['status' => "error", 'message' => "El Packing List no es un archivo de excel"];
        }
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Lista de Partidas');
        $query = $this->db->get();
        //find in query result folder_name lista de partidas and get file_url
        $listaPartidas = $query->row();
        if (!$listaPartidas) {
            return ['status' => "error", 'message' => "No se encontró la lista de partidas"];
        }
        $listaPartidas = $listaPartidas->file_url;
        //validate if lista de partidas is xls, xlsx,xlsm
        $path_parts = pathinfo($listaPartidas);
        $extension = $path_parts['extension'];
        if ($extension != "xls" && $extension != "xlsx" && $extension != "xlsm") {
            return ['status' => "error", 'message' => "La Lista de Partidas no es un archivo de excel"];
        }
        //get object PHPExcel from factura
        //sanitize file url
        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $facturaComercial); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $facturaComercial = FCPATH . ltrim($decodedPath, '/');
        $objPHPExcel = PHPExcel_IOFactory::load($facturaComercial);


        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $packingList); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $packingList = FCPATH . ltrim($decodedPath, '/');
        $objPHPExcelPacking = PHPExcel_IOFactory::load($packingList);
        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $listaPartidas); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $listaPartidas = FCPATH . ltrim($decodedPath, '/');
        $objPHPExcelListaPartidas = PHPExcel_IOFactory::load($listaPartidas);

        $itemNColumn = "B";
        $tipoClienteColumn = "C";
        $clienteColumn = "D";
        $descriptionColumn = "D";
        $descriptionNColumn = "E";
        $quantityCountColumn = "L";
        $quantityCountNColumn = "M";
        $quantityMeasureColumn = "M";
        $quantityMeasureNColumn = "N";
        $unitPriceColumn = "N";
        $unitPriceNColumn = "O";
        $unitMeasureColumn = "O";
        $unitMeasureNColumn = "P";
        $fobPriceColumn = "P";
        $fobPriceNColumn = "Q";
        $startColumn = 26;
        $startPackingListColumn = 27;
        $startListaPartidasColumn = 6;
        $startIndex = $startColumn;
        $startPackingListIndex = $startPackingListColumn;
        $highestFirstSheetRow = 0;
        $skyBlueColor = "85c1e9";
        $pinkColor = "f5b7b1";
        $greenColor = "7dcea0";
        $grayColor = "dcdde1";
        $yellow2Color = "fad7a0";
        $dataSystem = $this->db->select('nombre,volumen,volumen_doc,valor_doc,valor_cot,volumen_china,name,vol_selected')
            ->from($this->table_contenedor_cotizacion)
            ->join($this->table_contenedor_tipo_cliente, 'contenedor_consolidado_cotizacion.id_tipo_cliente = contenedor_consolidado_tipo_cliente.id')
            ->where('id_contenedor', $idContenedor)
            ->where('estado_cliente!=', null)
            ->get()->result();
        try {
            // Create a mapping of item IDs to clients from the packing list
            $itemToClientMap = [];
            $sheetPackingList = $objPHPExcelPacking->getSheet(0);
            $highestPackingRow = $sheetPackingList->getHighestRow();
            for ($packRow = $startPackingListColumn; $packRow <= $highestPackingRow; $packRow++) {
                $itemId = $sheetPackingList->getCell('B' . $packRow)->getValue();
                $client = $sheetPackingList->getCell('C' . $packRow)->getValue();
                //if client or itemid contain "TOTAL" break
                if (stripos(trim($itemId), "TOTAL") !== false || stripos(trim($client), "TOTAL") !== false) {
                    break;
                }
                if (!empty($itemId) && !empty($client)) {
                    $itemToClientMap[trim($itemId)] = trim($client);
                }
            }
            $sheetCount = $objPHPExcel->getSheetCount();
            //SE SHEET COUNT FIND A ROW CONTAIN "TOTAL" AND GET THE ROW -1  
            $totalRow = 0;
            for ($i = 0; $i < $sheetCount; $i++) {
                $sheet = $objPHPExcel->getSheet($i);
                $highestRow = $sheet->getHighestRow();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $itemN = $sheet->getCell($itemNColumn . $row)->getValue();
                    if (stripos(trim($itemN), "TOTAL") !== false) {
                        $totalRow = $row - 1;
                        break;
                    }
                }
            }
            // Obtener el número real de hojas en el archivo
            $actualSheetCount = $objPHPExcel->getSheetCount();
            $sheetCount = $actualSheetCount;
            $sheet0 = $objPHPExcel->getSheet(0);
            $sheet0->insertNewColumnBefore('C', 2);
            $sheet0->setCellValue('D25', 'CLIENTE');
            $sheet0->setCellValue('C25', 'TIPO DE CLIENTE');
            // Verificar si la columna E existe antes de removerla
            try {
                $sheet0->removeColumn('E');
            } catch (Exception $e) {
                log_message('error', 'Error removing column E: ' . $e->getMessage());
            }
            $sheet0->setCellValue('R25', 'ADVALOREM');
            $sheet0->setCellValue('S25', 'ANTIDUMPING');
            $sheet0->setCellValue('T25', 'VOL. SISTEMA');
            // $sheet0->setCellValue('U25', 'VOL. CHINA');
            // $sheet0->setCellValue('V25', 'VOL. DOC.');
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
            );
            //set title font bold and center horizontal
            $sheet0->getStyle('A25:Z25')->getFont()->setBold(true);
            $sheet0->getStyle('A25:Z25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheetListaPartidas = $objPHPExcelListaPartidas->getSheet(0);
            //SET R TO V style
            $sheet0->getStyle('R25:V25')->applyFromArray($styleArray);

            // Variables para controlar el merge entre hojas
            $currentClient = "";
            $clientStartRow = 0;
            $clientEndRow = 0;
            $pendingMerge = array(); // Array para almacenar merges pendientes

            for ($i = 0; $i < $sheetCount; $i++) {
                // Verificar que la hoja existe antes de intentar acceder a ella
                if ($i >= $objPHPExcel->getSheetCount()) {
                    log_message('error', 'Sheet index ' . $i . ' is out of bounds. Total sheets: ' . $objPHPExcel->getSheetCount());
                    break;
                }

                $sheet = $objPHPExcel->getSheet($i);
                $sheet0 = $objPHPExcel->getSheet(0); // Siempre referenciar la primera hoja

                if ($i == 0) {
                    // PRIMERA HOJA
                    $highestRow = $sheet->getHighestRow();

                    for ($row = $startIndex; $row <= $highestRow; ++$row) {
                        $itemN = $sheet->getCell($itemNColumn . $row)->getValue();

                        // Obtener cliente
                        $client = isset($itemToClientMap[trim($itemN)]) ? $itemToClientMap[trim($itemN)] : null;
                        if ($client === null) {
                            $client = $sheetPackingList->getCell('C' . $startPackingListIndex)->getValue();
                            $startPackingListIndex++;
                        }

                        // Buscar información aduanera
                        $mergedCells = $sheetListaPartidas->getMergeCells();
                        foreach ($mergedCells as $range) {
                            [$startCell, $endCell] = explode(':', $range);
                            if (preg_match('/^B\d+$/', $startCell)) {
                                $value = $sheetListaPartidas->getCell($startCell)->getValue();
                                log_message('error', 'Checking value: ' . $value . ' against itemN: ' . $itemN);
                                if (trim($value) == $itemN) {
                                    preg_match('/\d+/', $startCell, $startMatches);
                                    preg_match('/\d+/', $endCell, $endMatches);
                                    $startRow = (int)$startMatches[0];
                                    $endRow = (int)$endMatches[0];
                                    log_message('error', 'Start Row: ' . $startRow . ' End Row: ' . $endRow);
                                    for ($r = $startRow; $r <= $endRow; $r++) {
                                        $adValorem = $sheetListaPartidas->getCell('G' . $r)->getValue();
                                        if (trim($adValorem) == "FTA") {
                                            $adValorem = $sheetListaPartidas->getCell('H' . $r)->getValue();
                                            log_message('error', 'Advalorem FTA: ' . $adValorem);
                                        }
                                        log_message('error', 'Advalorem: ' . $adValorem);
                                        $antiDumping = $sheetListaPartidas->getCell('I' . $r)->getValue();
                                        $sheet0->setCellValue('R' . $row, $adValorem);
                                        $sheet0->setCellValue('S' . $row, $antiDumping == 0 ? "-" : $antiDumping);
                                        break;
                                    }
                                    break;
                                }
                            }
                        }

                        // MANEJO DEL CLIENTE Y MERGE
                        if ($client !== $currentClient) {
                            // Si hay un cliente anterior, guardarlo para merge posterior
                            if ($currentClient !== "" && $currentClient !== null && $clientStartRow > 0) {
                                $pendingMerge[] = array(
                                    'client' => $currentClient,
                                    'start' => $clientStartRow,
                                    'end' => $clientEndRow
                                );
                            }

                            // Inicializar nuevo cliente
                            $currentClient = $client;
                            $clientStartRow = $row;
                        }

                        // Actualizar la fila final del cliente actual
                        $clientEndRow = $row;
                        // Validar que la fila sea válida antes de establecer el valor
                        if ($row > 0) {
                            $sheet0->setCellValue('D' . $row, $client);
                        }

                        // Buscar datos del sistema
                        $volumen_cotizacion = "-";
                        $volumen_selected = '';
                        $volumen_china = "-";
                        $volumen_doc = "-";
                        $valor_doc = "-";
                        $valor_cot = "-";
                        $tipoCliente = "No existe en contenedor";

                        foreach ($dataSystem as $item) {
                            if ($this->isNameMatch($client, $item->nombre)) {
                                $volumen_cotizacion = $item->volumen;
                                $volumen_china = $item->volumen_china;
                                $volumen_selected = $item->vol_selected ?? '';
                                $volumen_doc = $item->volumen_doc;
                                $valor_doc = $item->valor_doc;
                                $tipoCliente = $item->name;
                                break;
                            }
                        }

                        // Seleccionar volumen apropiado
                        if ($volumen_selected == 'volumen_doc') {
                            $volumen_cotizacion = $volumen_doc;
                        } elseif ($volumen_selected == 'volumen_china') {
                            $volumen_cotizacion = $volumen_china;
                        } elseif ($volumen_selected == 'volumen') {
                            $volumen_cotizacion = $volumen_cotizacion;
                        }

                        // Validar que la fila sea válida antes de establecer los valores
                        if ($row > 0) {
                            $sheet0->setCellValue('T' . $row, $volumen_cotizacion);
                            $sheet0->setCellValue('C' . $row, $tipoCliente);
                        }

                        if (stripos(trim($itemN), "TOTAL") !== false) {
                            // Validar que la fila sea válida antes de hacer merge/unmerge
                            if ($row > 0) {
                                try {
                                    $objPHPExcel->getActiveSheet()->unmergeCells('B' . $row . ':P' . $row);
                                } catch (Exception $e) {
                                    log_message('error', 'Error unmerging cells: ' . $e->getMessage());
                                }
                                try {
                                    $objPHPExcel->getActiveSheet()->mergeCells('E' . $row . ':L' . $row);
                                } catch (Exception $e) {
                                    log_message('error', 'Error merging cells: ' . $e->getMessage());
                                }
                            }
                            $highestRow = $row - 1;
                            $highestFirstSheetRow += $highestRow + 1;
                            break;
                        }

                        // Aplicar estilos solo si la fila es válida
                        if ($row > 0) {
                            $styleArray = array(
                                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    )
                                )
                            );
                            try {
                                $sheet0->getStyle('R' . $row . ':T' . $row)->applyFromArray($styleArray);
                                $sheet0->getStyle('R' . $row . ':T' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet0->getStyle('R' . $row . ':T' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                $sheet0->getStyle('R' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            } catch (Exception $e) {
                                log_message('error', 'Error applying styles: ' . $e->getMessage());
                            }
                        }
                    }
                } else {
                    // HOJAS ADICIONALES - Solo procesar si realmente hay más de una hoja
                    if ($objPHPExcel->getSheetCount() > 1) {
                        $startIndex = $startColumn;
                        $highestSheetRow = $sheet->getHighestRow();

                        for ($row = $startIndex; $row <= $highestSheetRow; ++$row) {
                            $itemN = $sheet->getCell($itemNColumn . $row)->getValue();

                            // Obtener cliente
                            $client = isset($itemToClientMap[trim($itemN)]) ? $itemToClientMap[trim($itemN)] : null;
                            if ($client === null) {
                                $client = $sheetPackingList->getCell('C' . $startPackingListIndex)->getValue();
                                $startPackingListIndex++;
                            }

                            if (stripos(trim($itemN), "TOTAL") !== false) {
                                $highestSheetRow = $row - 1;
                                break;
                            }

                            // MANEJO DEL CLIENTE - CONTINUIDAD ENTRE HOJAS
                            if ($client !== $currentClient) {
                                // Si hay un cliente anterior, guardarlo para merge
                                if ($currentClient !== "" && $currentClient !== null && $clientStartRow > 0) {
                                    $pendingMerge[] = array(
                                        'client' => $currentClient,
                                        'start' => $clientStartRow,
                                        'end' => $clientEndRow
                                    );
                                }

                                // Verificar si este cliente ya tiene filas previas
                                $clientResumed = false;
                                for ($j = count($pendingMerge) - 1; $j >= 0; $j--) {
                                    if ($pendingMerge[$j]['client'] === $client) {
                                        // Reanudar el cliente anterior
                                        $clientStartRow = $pendingMerge[$j]['start'];
                                        $clientResumed = true;
                                        // Remover el merge pendiente ya que lo continuaremos
                                        unset($pendingMerge[$j]);
                                        $pendingMerge = array_values($pendingMerge); // Reindexar
                                        break;
                                    }
                                }

                                if (!$clientResumed) {
                                    // Nuevo cliente
                                    $clientStartRow = $highestFirstSheetRow;
                                }

                                $currentClient = $client;
                            }

                            // Insertar nueva fila
                            $sheet0->insertNewRowBefore($highestFirstSheetRow, 1);
                            $clientEndRow = $highestFirstSheetRow;

                            // Buscar datos del sistema
                            $volumen_cotizacion = "-";
                            $volumen_china = "-";
                            $volumen_doc = "-";
                            $valor_doc = "-";
                            $valor_cot = "-";
                            $volumen_selected = '';
                            $tipoCliente = "No existe en contenedor";

                            foreach ($dataSystem as $item) {
                                if ($this->isNameMatch($client, $item->nombre)) {
                                    $volumen_cotizacion = $item->volumen;
                                    $volumen_china = $item->volumen_china;
                                    $volumen_selected = $item->vol_selected ?? '';
                                    $volumen_doc = $item->volumen_doc;
                                    $valor_doc = $item->valor_doc;
                                    $tipoCliente = $item->name;
                                    break;
                                }
                            }

                            // Seleccionar volumen apropiado
                            if ($volumen_selected == 'volumen_doc') {
                                $volumen_cotizacion = $volumen_doc;
                            } elseif ($volumen_selected == 'volumen_china') {
                                $volumen_cotizacion = $volumen_china;
                            } elseif ($volumen_selected == 'volumen') {
                                $volumen_cotizacion = $volumen_cotizacion;
                            }

                            // Validar que la fila sea válida antes de establecer los valores
                            if ($highestFirstSheetRow > 0) {
                                $sheet0->setCellValue('T' . $highestFirstSheetRow, $volumen_cotizacion);
                                $sheet0->setCellValue('C' . $highestFirstSheetRow, $tipoCliente);
                            }

                            // Buscar información aduanera
                            $mergedCells = $sheetListaPartidas->getMergeCells();
                            foreach ($mergedCells as $range) {
                                [$startCell, $endCell] = explode(':', $range);
                                if (preg_match('/^B\d+$/', $startCell)) {
                                    $value = $sheetListaPartidas->getCell($startCell)->getValue();
                                    if (trim($value) == $itemN) {
                                        preg_match('/\d+/', $startCell, $startMatches);
                                        preg_match('/\d+/', $endCell, $endMatches);
                                        $startRow = (int)$startMatches[0];
                                        $endRow = (int)$endMatches[0];

                                        for ($r = $startRow; $r <= $endRow; $r++) {
                                            $adValorem = $sheetListaPartidas->getCell('G' . $r)->getValue();
                                            if (trim($adValorem) == "FTA") {
                                                $adValorem = $sheetListaPartidas->getCell('H' . $r)->getValue();
                                            }
                                            log_message('error', 'Advalorem: ' . $adValorem);
                                            $antiDumping = $sheetListaPartidas->getCell('I' . $r)->getValue();
                                            $sheet0->setCellValue('R' . $highestFirstSheetRow, $adValorem);
                                            $sheet0->setCellValue('S' . $highestFirstSheetRow, $antiDumping == 0 ? "-" : $antiDumping);
                                            break;
                                        }
                                        break;
                                    }
                                }
                            }

                            $sheet0->setCellValue('D' . $highestFirstSheetRow, $client);

                            // Copiar datos del producto
                            $description = $sheet->getCell($descriptionColumn . $row)->getValue();
                            $quantityCount = $sheet->getCell($quantityCountColumn . $row)->getValue();
                            $quantityMeasure = $sheet->getCell($quantityMeasureColumn . $row)->getValue();
                            $unitPrice = $sheet->getCell($unitPriceColumn . $row)->getValue();
                            $unitMeasure = $sheet->getCell($unitMeasureColumn . $row)->getValue();
                            $fobPrice = $sheet->getCell($fobPriceColumn . $row)->getValue();

                            $sheet0->setCellValue($itemNColumn . $highestFirstSheetRow, $itemN);
                            $sheet0->setCellValue($descriptionNColumn . $highestFirstSheetRow, $description);
                            $sheet0->setCellValue($quantityCountNColumn . $highestFirstSheetRow, $quantityCount);
                            $sheet0->setCellValue($quantityMeasureNColumn . $highestFirstSheetRow, $quantityMeasure);
                            $sheet0->setCellValue($unitPriceNColumn . $highestFirstSheetRow, $unitPrice);
                            $sheet0->setCellValue($unitMeasureNColumn . $highestFirstSheetRow, $unitMeasure);
                            $sheet0->setCellValue($fobPriceNColumn . $highestFirstSheetRow, "=" . $quantityCountNColumn . $highestFirstSheetRow . "*" . $unitPriceNColumn . $highestFirstSheetRow);

                            // Aplicar formato
                            $styleArray = array(
                                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    )
                                )
                            );

                            $objPHPExcel->getActiveSheet()->mergeCells('E' . $highestFirstSheetRow . ':L' . $highestFirstSheetRow);
                            $sheet0->getStyle('R' . $highestFirstSheetRow . ':T' . $highestFirstSheetRow)->applyFromArray($styleArray);
                            $sheet0->getStyle('R' . $highestFirstSheetRow . ':T' . $highestFirstSheetRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet0->getStyle('R' . $highestFirstSheetRow . ':T' . $highestFirstSheetRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $sheet0->getStyle('O' . $highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                            $sheet0->getStyle('Q' . $highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                            $sheet0->getStyle('R' . $highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                            $highestFirstSheetRow++;
                        }
                    } // Cerrar el if ($objPHPExcel->getSheetCount() > 1)
                }
            }

            // EJECUTAR TODOS LOS MERGES PENDIENTES + EL CLIENTE ACTUAL
            if ($currentClient !== "" && $currentClient !== null && $clientStartRow > 0) {
                $pendingMerge[] = array(
                    'client' => $currentClient,
                    'start' => $clientStartRow,
                    'end' => $clientEndRow
                );
            }

            // Aplicar todos los merges
            $sheet0 = $objPHPExcel->getSheet(0);
            foreach ($pendingMerge as $merge) {
                log_message('error', 'Merging client: ' . $merge['client'] . ' from row ' . $merge['start'] . ' to ' . $merge['end']);
                if ($merge['start'] < $merge['end'] && $merge['start'] > 0 && $merge['end'] > 0) {
                    try {
                        $sheet0->mergeCells('C' . $merge['start'] . ':C' . $merge['end']);
                        $sheet0->mergeCells('D' . $merge['start'] . ':D' . $merge['end']);
                        $sheet0->mergeCells('T' . $merge['start'] . ':T' . $merge['end']);
                    } catch (Exception $e) {
                        log_message('error', 'Error merging cells: ' . $e->getMessage());
                    }
                }
            }


            //unmerge e to l - verificar si está mergeado antes de unmergear
            if ($highestFirstSheetRow > 0) {
                $mergeRange = 'E' . $highestFirstSheetRow . ':L' . $highestFirstSheetRow;
                $mergedCells = $objPHPExcel->getActiveSheet()->getMergeCells();
                if (in_array($mergeRange, $mergedCells)) {
                    try {
                        $objPHPExcel->getActiveSheet()->unmergeCells($mergeRange);
                    } catch (Exception $e) {
                        log_message('error', 'Error unmerging cells: ' . $e->getMessage());
                    }
                }
                try {
                    $sheet0->mergeCells('B' . $highestFirstSheetRow . ':P' . $highestFirstSheetRow);
                } catch (Exception $e) {
                    log_message('error', 'Error merging cells: ' . $e->getMessage());
                }
            }
            //set fill none in sheet 0 row=highestFirstSheetRow
            if ($highestFirstSheetRow > 0) {
                try {
                    $sheet0->getStyle('R' . $highestFirstSheetRow . ':T' . $highestFirstSheetRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
                    //set all borders
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
                        )
                    );
                    $sheet0->getStyle('R' . $highestFirstSheetRow . ':T' . $highestFirstSheetRow)->applyFromArray($styleArray);
                } catch (Exception $e) {
                    log_message('error', 'Error applying final styles: ' . $e->getMessage());
                }
            }

            //MERGE B TO 0
            //set p highestFirstSheetRow value to sum from p.startColumn to p.highestFirstSheetRow-1
            if ($highestFirstSheetRow > 0 && ($highestFirstSheetRow - 1) > 0) {
                try {
                    $sheet0->setCellValue('Q' . $highestFirstSheetRow, '=SUM(Q' . $startColumn . ':Q' . ($highestFirstSheetRow - 1) . ')');
                    $sheet0->setCellValue('T' . $highestFirstSheetRow, '=SUM(T' . $startColumn . ':T' . ($highestFirstSheetRow - 1) . ')');
                } catch (Exception $e) {
                    log_message('error', 'Error setting sum formulas: ' . $e->getMessage());
                }
            }

            //set  d column auto size
            $sheet0->getStyle('D')->getAlignment()->setWrapText(true);
            $sheet0->getColumnDimension('C')->setWidth(30);

            $sheet0->getColumnDimension('D')->setWidth(60);
            //SET WIDTH TO COLUMNS
            $sheet0->getColumnDimension('R')->setWidth(20);
            $sheet0->getColumnDimension('S')->setWidth(25);
            $sheet0->getColumnDimension('T')->setWidth(15);
            // $sheet0->getColumnDimension('U')->setWidth(15);
            // $sheet0->getColumnDimension('V')->setWidth(15);
            //from b starcolumn to b highestFirstSheetRow-1 set fill pinkColor
            if ($highestFirstSheetRow > 1) {
                try {
                    $sheet0->getStyle('C' . $startColumn . ':C' . ($highestFirstSheetRow - 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet0->getStyle('C' . $startColumn . ':C' . ($highestFirstSheetRow - 1))->getFill()->getStartColor()->setRGB($pinkColor);
                    //D TO GRAY, R AND S TO SKYBLUE, T TO PINK,U TO GREEN
                    $sheet0->getStyle('D' . $startColumn . ':D' . ($highestFirstSheetRow - 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet0->getStyle('D' . $startColumn . ':D' . ($highestFirstSheetRow - 1))->getFill()->getStartColor()->setRGB($grayColor);
                    $sheet0->getStyle('R' . $startColumn . ':S' . ($highestFirstSheetRow - 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet0->getStyle('R' . $startColumn . ':S' . ($highestFirstSheetRow - 1))->getFill()->getStartColor()->setRGB($skyBlueColor);
                    $sheet0->getStyle('T' . $startColumn . ':T' . ($highestFirstSheetRow - 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet0->getStyle('T' . $startColumn . ':T' . ($highestFirstSheetRow - 1))->getFill()->getStartColor()->setRGB($pinkColor);
                } catch (Exception $e) {
                    log_message('error', 'Error applying column styles: ' . $e->getMessage());
                }
            }
            return $objPHPExcel;
        } catch (Exception $e) {
            log_message('error', __METHOD__ . '' . $e->getMessage());
            // Crear un objeto PHPExcel vacío para evitar el error de tipo
            $emptyExcel = new PHPExcel();
            $emptyExcel->getActiveSheet()->setTitle('Error');
            $emptyExcel->getActiveSheet()->setCellValue('A1', 'Error: ' . $e->getMessage());
            return $emptyExcel;
        }
    }

    public function createDocumentacionFolder($name, $idContenedor, $file, $categoria = null, $icon = null)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/images/agentecompra/'
            );
            $isDocumentationProfile = $this->user->No_Grupo == $this->roleDocumentacion;
            if ($fileUrl) {
                $this->db->insert($this->table_contenedor_documentacion_folders, [
                    'id_contenedor' => $idContenedor,
                    'folder_name' => $name,
                    'categoria' => $categoria,
                    'b_icon' => $icon,
                    'only_doc_profile' => $isDocumentationProfile,
                ]);

                if ($this->db->affected_rows() > 0) {
                    $idFolder = $this->db->insert_id();
                    //insert file in table contenedor_consolidado_documentacion_files
                    $this->db->insert($this->table_contenedor_documentacion_files, [
                        'id_folder' => $idFolder,
                        'file_url' => $fileUrl,
                        'id_contenedor' => $idContenedor
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        return ['status' => "success", 'error' => false];
                    }
                    //if db error delete folder
                    return ['status' => "error", 'error' => true];
                }
            }

            if ($this->db->error()) {
                return ['status' => "error", 'error' => $this->db->error()];
            }
        } catch (Exception $e) {
            return false;
        }
    }
    public function updateEstadoCotizacionProveedor($idCotizacion, $idProveedor, $estado)
    {
        //get id_contenedor from id_cotizacion
        $idContenedor = $this->db->select('id_contenedor')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $idCotizacion)
            ->get()->row()->id_contenedor;
        if (in_array($estado, ["ROTULADO", "RESERVADO", 'COBRANDO'])) {
            $this->db->where('id_cotizacion', $idCotizacion);
            $this->db->group_start(); // Agrupar condiciones OR
            $this->db->where('estados IS NULL');
            $this->db->or_where('estados', 'RESERVADO');
            $this->db->or_where('estados', 'ROTULADO');
            $this->db->or_where('estados', 'COBRANDO');
            $this->db->or_where('estados', 'DATOS PROVEEDOR');
            $this->db->or_where('estados', 'INSPECCIONADO');
            $this->db->group_end();

            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => $estado]);

            // Segunda actualización para un proveedor específico
            $this->db->where('id_cotizacion', $idCotizacion);
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => $estado]);
            //INSERT INTO TRACKING TABLE

        }
        // Manejo del estado "LOADED"
        else if ($estado == "LOADED") {
            $this->db->select('estado')
                ->from($this->table_conteneodr_proveedor_estados_tracking)
                ->where('id_cotizacion', $idCotizacion)
                ->where('estado', 'RESERVADO');
            $query = $this->db->get();
            $estadoCliente = $query->row() ? "RESERVADO" : "NO RESERVADO";

            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion, ['estado_cliente' => $estadoCliente]);

            $this->db->where('id_cotizacion', $idCotizacion);
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados_proveedor' => "LOADED"]);
            $this->db->select('SUM(ifnull(cbm_total_china,0)) as volumen_china')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id_cotizacion', $idCotizacion)
                ->where('estados_proveedor', "LOADED");
            $query = $this->db->get();
            $volumenChina = $query->row()->volumen_china;

            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion, ['volumen_china' => $volumenChina]);
            //get status of all providers with id_cotizacion=idCotizacion
            $this->db->where('
            id_cotizacion', $idCotizacion);
            $this->db->select('estados_proveedor')
                ->from($this->table_contenedor_cotizacion_proveedores);
            $query = $this->db->get();
            //validate if all providers has status loaded   


        }
        // Manejo de los estados específicos en array
        else if (in_array($estado, ["NC", "C", "R", "NS", "NO LOADED", "INSPECTION"])) {
            $this->db->where('id_cotizacion', $idCotizacion);
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados_proveedor' => $estado]);
        }
        // Manejo de otros estados
        else {
            $this->db->where('id_cotizacion', $idCotizacion);
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => $estado]);
        }

        // Verificar si hubo una actualización exitosa


        // Manejo de errores en la base de datos
        $dbError = $this->db->error();
        if ($dbError && $dbError['code'] != 0) {
            return ['status' => "error", 'error' => $dbError];
        }

        // Actualizar timestamp en `tracking`
        $this->db->where('id_proveedor', $idProveedor);
        $this->db->update($this->table_conteneodr_proveedor_estados_tracking, ['updated_at' => date('Y-m-d H:i:s')]);

        // Insertar nuevo tracking
        $this->db->insert($this->table_conteneodr_proveedor_estados_tracking, [
            'id_cotizacion' => $idCotizacion,
            'id_proveedor' => $idProveedor,
            'estado' => $estado
        ]);
        $this->db->select('estado')
            ->from($this->table_conteneodr_proveedor_estados_tracking)
            ->where('id_cotizacion', $idCotizacion)
            ->where('estado', 'RESERVADO');
        $query = $this->db->get();
        $estadoCliente = $query->row() ? "RESERVADO" : "NO RESERVADO";

        $this->db->where('id', $idCotizacion);
        $this->db->update($this->table_contenedor_cotizacion, ['estado_cliente' => $estadoCliente]);
        // Manejo de errores en la inserción
        $dbError = $this->db->error();
        if ($dbError && $dbError['code'] != 0) {
            return ['status' => "error", 'error' => $dbError];
        }

        // Llamada al manejador de actualización de cotización
        $data = $this->handlerUpdateCotizacionProveedor($estado, $idProveedor, $idCotizacion);

        return $data ?: "success";
    }
    public function handlerUpdateCotizacionProveedor($estado, $idProveedor, $idCotizacion)
    {
        try {
            // Obtener información básica de la cotización
            $cotizacionInfo = $this->db->select('nombre, id_contenedor, telefono')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $idCotizacion)
                ->get()
                ->row();

            if (!$cotizacionInfo) {
                throw new Exception("No se encontró la cotización especificada");
            }

            $cliente = $cotizacionInfo->nombre;
            $idContenedor = $cotizacionInfo->id_contenedor;
            $telefono = preg_replace('/\s+/', '', $cotizacionInfo->telefono);
            $this->phoneNumberId = $telefono ? $telefono . '@c.us' : '';

            // Obtener proveedores asociados a la cotización
            $proveedores = $this->db->select('code_supplier, products, send_rotulado_status, id')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id_cotizacion', $idCotizacion)
                ->get()
                ->result_array();

            if (empty($proveedores)) {
                throw new Exception("No se encontraron proveedores para esta cotización");
            }

            // Obtener información del contenedor
            $contenedorInfo = $this->db->select('carga')
                ->from($this->table)
                ->where('id', $idContenedor)
                ->get()
                ->row();

            if (!$contenedorInfo) {
                throw new Exception("No se encontró información del contenedor");
            }

            $carga = $contenedorInfo->carga;

            if ($estado == "ROTULADO") {
                return $this->procesarEstadoRotulado($cliente, $carga, $proveedores, $idCotizacion);
            } elseif ($estado == "COBRANDO") {
                return $this->procesarEstadoCobrando($idProveedor, $idCotizacion, $carga);
            }

            return "success";
        } catch (Exception $e) {
            log_message("error", "Error en handlerUpdateCotizacionProveedor: " . $e->getMessage());
            return ['status' => "error", 'message' => $e->getMessage()];
        }
    }
    protected function procesarEstadoRotulado($cliente, $carga, $proveedores, $idCotizacion)
    {
        try {
            // Procesar plantilla de bienvenida
            $htmlWelcomePath = 'assets/downloads/Welcome_Consolidado_Template.html';
            if (!file_exists($htmlWelcomePath)) {
                throw new Exception("No se encontró la plantilla de bienvenida");
            }

            $htmlWelcomeContent = file_get_contents($htmlWelcomePath);
            $htmlWelcomeContent = mb_convert_encoding($htmlWelcomeContent, 'UTF-8', mb_detect_encoding($htmlWelcomeContent));
            $htmlWelcomeContent = str_replace('{{consolidadoNumber}}', $carga, $htmlWelcomeContent);

            // Filtrar proveedores
            $providersHasSended = array_filter($proveedores, function ($proveedor) {
                return $proveedor['send_rotulado_status'] == 'SENDED';
            });
            $providersHasNoSended = array_filter($proveedores, function ($proveedor) {
                return $proveedor['send_rotulado_status'] == 'PENDING';
            });
            if (empty($providersHasNoSended)) {
                throw new Exception("No hay proveedores pendientes de envío");
            }
            if (count($providersHasSended) == 0) {
                $this->sendWelcome($carga);
            } else if (
                count($providersHasSended) > 0
                && count($providersHasNoSended) > 0
            ) {
                $this->sendMessage("Hola 🙋🏻‍♀, te escribe el área de coordinación de Probusiness. 

📢 Añadiste un nuevo proveedor en el *Consolidado #${carga}*

*Rotulado: 👇🏼*  
Tienes que indicarle a tu proveedor que las cajas máster 📦 cuenten con un rotulado para 
identificar tus paquetes y diferenciarlas de los demás cuando llegue a nuestro almacén.");
            }



            // Configurar ZIP
            $zipFileName = 'assets/downloads/Rotulado.zip';
            $zipDirectory = dirname($zipFileName);

            // Asegurar que el directorio existe
            if (!file_exists($zipDirectory)) {
                if (!mkdir($zipDirectory, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio para el archivo ZIP");
                }
            }

            // Eliminar archivo ZIP existente si existe
            if (file_exists($zipFileName) && !unlink($zipFileName)) {
                throw new Exception("No se pudo eliminar el archivo ZIP existente");
            }

            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new Exception("No se pudo crear el archivo ZIP");
            }

            // Configuración de DomPDF
            $options = new Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $options->set('isRemoteEnabled', true);
            $sleepSendMedia = 7;
            // Procesar cada proveedor
            foreach ($providersHasNoSended as $proveedor) {
                log_message('error', 'Proveedor enviado: ' . json_encode($proveedor));
                $supplierCode = $proveedor['code_supplier'];
                $products = $proveedor['products'];
                $sleepSendMedia += 1;

                // Procesar plantilla de rotulado
                $htmlFilePath = 'assets/downloads/Rotulado_Template.html';
                if (!file_exists($htmlFilePath)) {
                    throw new Exception("No se encontró la plantilla de rotulado");
                }

                $htmlContent = file_get_contents($htmlFilePath);
                $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', mb_detect_encoding($htmlContent));
                $htmlContent = str_replace('{{cliente}}', $cliente, $htmlContent);
                $htmlContent = str_replace('{{supplier_code}}', $supplierCode, $htmlContent);
                $htmlContent = str_replace('{{carga}}', $carga, $htmlContent);
                $htmlContent = str_replace('{{base_url}}', base_url(), $htmlContent);

                // Generar PDF
                $dompdf = new Dompdf\Dompdf($options);
                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdfContent = $dompdf->output();

                // Guardar temporalmente
                $tempFilePath = sys_get_temp_dir() . "/temp_document_proveedor{$supplierCode}.pdf";
                if (file_exists($tempFilePath) && !unlink($tempFilePath)) {
                    throw new Exception("No se pudo eliminar el archivo temporal existente");
                }

                if (file_put_contents($tempFilePath, $pdfContent) === false) {
                    throw new Exception("No se pudo guardar el PDF temporal");
                }

                try {
                    if (!$zip->addFile($tempFilePath, "Rotulado_{$supplierCode}.pdf")) {
                        log_message('error', "No se pudo añadir $tempFilePath al ZIP");
                        continue;
                    }
                    // if (!$zip->addFile($tempFilePath, "Rotulado_{$supplierCode}.pdf")) {
                    //     throw new Exception("No se pudo añadir el archivo al ZIP");
                    // }
                    // Enviar documento al proveedor
                    $this->sendDataItem(
                        "Producto: {$products}\nCódigo de proveedor: {$supplierCode}",
                        $tempFilePath
                    );

                    // Actualizar estado del proveedor
                    $this->db->update(
                        $this->table_contenedor_cotizacion_proveedores,
                        ["send_rotulado_status" => "SENDED"],
                        ["id" => $proveedor['id']]
                    );

                    // Agregar al ZIP
                } catch (Exception $e) {
                    log_message('error', 'Error procesando proveedor ' . $supplierCode . ': ' . $e->getMessage());
                    continue; // Continuar con el siguiente proveedor si hay error
                } finally {
                    // Limpiar memoria

                    gc_collect_cycles();
                }
            }

            // Cerrar ZIP
            if (!$zip->close()) {
                throw new Exception("Error al cerrar el archivo ZIP");
            }
            $direccionUrl = base_url('assets/downloads/Direccion.jpg');
            $sleepSendMedia += 3;
            $this->sendMedia($direccionUrl, 'image/jpg', '🏽Dile a tu proveedor que envíe la carga a nuestro almacén en China', null, $sleepSendMedia);
            $sleepSendMedia += 3;
            $this->sendMessage("También necesito los datos de tu proveedor para comunicarnos y recibir tu carga.

➡ *Datos del proveedor: (Usted lo llena)*

☑ Nombre del producto:
☑ Nombre del vendedor:
☑ Celular del vendedor:

Te avisaré apenas tu carga llegue a nuestro almacén de China, cualquier duda me escribes. 🫡", null, $sleepSendMedia);

            // Enviar ZIP al cliente
            if (!file_exists($zipFileName)) {
                throw new Exception("El archivo ZIP no se generó correctamente");
            }

            $fileSize = filesize($zipFileName);
            if ($fileSize === false || $fileSize == 0) {
                throw new Exception("El archivo ZIP está vacío");
            }

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="Rotulado.zip"');
            header('Content-Length: ' . $fileSize);

            if (readfile($zipFileName) === false) {
                throw new Exception("No se pudo enviar el archivo ZIP");
            }

            // Limpieza final
            foreach ($proveedores as $proveedor) {
                $tempFilePath = sys_get_temp_dir() . "/temp_document_proveedor{$proveedor['code_supplier']}.pdf";
                if (file_exists($tempFilePath)) {
                    unlink($tempFilePath);
                }
            }

            exit;
        } catch (Exception $e) {
            log_message('error', 'Error en procesarEstadoRotulado: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function procesarEstadoCobrando($idProveedor, $idCotizacion, $carga)
    {
        try {
            // Obtener información del proveedor
            $proveedorInfo = $this->db->select('estados_proveedor, code_supplier, qty_box_china, qty_box, id_cotizacion')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id', $idProveedor)
                ->get()
                ->row();

            if (!$proveedorInfo) {
                throw new Exception("No se encontró información del proveedor");
            }

            $supplierCode = $proveedorInfo->code_supplier;
            $idCotizacion = $proveedorInfo->id_cotizacion;

            // Obtener información de la cotización
            $cotizacionInfo = $this->db->select('volumen, monto, id_contenedor')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $idCotizacion)
                ->get()
                ->row();

            if (!$cotizacionInfo) {
                throw new Exception("No se encontró información de la cotización");
            }

            $volumen = $cotizacionInfo->volumen;
            $valorCot = $cotizacionInfo->monto;
            $idContenedor = $cotizacionInfo->id_contenedor;

            // Obtener fecha de cierre
            $fechaCierre = $this->db->select('f_cierre')
                ->from($this->table)
                ->where('id', $idContenedor)
                ->get()
                ->row();

            if (!$fechaCierre) {
                throw new Exception("No se encontró la fecha de cierre");
            }

            $fCierre = date('d F', strtotime($fechaCierre->f_cierre));
            $meses = [
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            ];
            $fCierre = strtr($fCierre, $meses);

            // Obtener información del cliente
            $clienteInfo = $this->db->select('nombre, telefono')
                ->from($this->table_contenedor_cotizacion)
                ->where('id', $idCotizacion)
                ->get()
                ->row();

            if (!$clienteInfo) {
                throw new Exception("No se encontró información del cliente");
            }

            $telefono = preg_replace('/\s+/', '', $clienteInfo->telefono);
            $this->phoneNumberId = $telefono ? $telefono . '@c.us' : '';

            // Construir y enviar mensaje
            $message = "Reserva de espacio:\n" .
                "*Consolidado #" . $carga . "-2025*\n\n" .
                "Ahora tienes que hacer el pago del CBM preliminar para poder subir su carga en nuestro contenedor.\n\n" .
                "☑ CBM Preliminar: " . $volumen . " cbm\n" .
                "☑ Costo CBM: $" . $valorCot . "\n" .
                "☑ Fecha Limite de pago: " . $fCierre . "\n\n" .
                "⚠ Nota: Realizar el pago antes del llenado del contenedor.\n\n" .
                "📦 En caso hubiera variaciones en el cubicaje se cobrará la diferencia en la cotización final.\n\n" .
                "Apenas haga el pago, envíe por este medio para hacer la reserva.";

            $this->sendMessage($message);

            // Enviar imagen de pagos
            $pagosUrl = base_url('assets/downloads/pagos-full.jpg');
            $this->sendMedia($pagosUrl, 'image/jpg');

            return "success";
        } catch (Exception $e) {
            log_message('error', 'Error en procesarEstadoCobrando: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateTelefonoProveedor($idProveedor, $telefono)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update(
            $this->table_contenedor_cotizacion_proveedores,
            [
                'supplier_phone' => $telefono,
                "estados" => "DATOS PROVEEDOR"
            ]
        );
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateProveedor($idProveedor, $proveedor)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update(
            $this->table_contenedor_cotizacion_proveedores,
            ['supplier' => $proveedor]
        );
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateQtyChina($idProveedor, $qtyChina)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['qty_box_china' => $qtyChina]);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateCBMChina($idProveedor, $cbm)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['cbm_total_china' => $cbm]);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateArriveDateChina($idProveedor, $arriveDate)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['arrive_date_china' => $arriveDate]);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateProductos($idProveedor, $productos)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['products' => $productos]);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateEstadoProveedor($idCotizacion, $idProveedor, $estados_proveedor)
    {
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados_proveedor' => $estados_proveedor]);
        if ($this->db->affected_rows() > 0) {
            if ($estados_proveedor == "LOADED") {
                $this->db->where('id', $idProveedor);
                // $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => 'EMBARCADO']);
                //verify if in tracking exists RESERVADO ELSE TRUE SET estado_cliente in tablee cotizacion TO RESERVADO
                $this->db->select('estado')
                    ->from($this->table_conteneodr_proveedor_estados_tracking)
                    ->where('id_cotizacion', $idCotizacion)
                    ->where('estado', 'RESERVADO');
                $query = $this->db->get();
                if (!$query->row()) {
                    $this->db->where('id', $idCotizacion);
                    $this->db->update($this->table_contenedor_cotizacion, ['estado_cliente' => 'RESERVADO']);
                }
            }
            return "success";
        }
        return false;
    }
    public function uploadFileInspection($idProveedor, $files)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();

        $index = 0;
        $filesArray = [];
        foreach ($files['files']['name'] as $index => $fileName) {
            $fileToUp =  [
                "name" => $files['files']['name'][$index],
                "type" => $files['files']['type'][$index],
                "tmp_name" => $files['files']['tmp_name'][$index],
                "error" => $files['files']['error'][$index],
                "size" => $files['files']['size'][$index]
            ];
            $fileUrl = $this->uploadSingleFile(
                $fileToUp,
                'assets/images/agentecompra/'
            );
            if ($fileUrl) {
                $this->db->insert(
                    $this->table_contenedor_cotizacion_proveedores_documentacion,
                    [
                        'id_proveedor' => $idProveedor,
                        'file_url' => $fileUrl,
                        'file_name' => $file['name'][$index],
                        'file_ext' => $files['files']['type'][$index]
                    ]
                );
                if ($this->db->affected_rows() > 0) {
                    $fileToReturn = [
                        'id' => $this->db->insert_id(),
                        'file_url' => $fileUrl,
                        'file_name' => $files['files']['name'][$index],
                        'file_ext' => $files['files']['type'][$index]
                    ];
                    $filesArray[] = $fileToReturn;
                }
            }
            $index++;
        }

        //validate if exists db error

        return ['status' => "success", 'error' => false, "data" => $filesArray];
    }
    public function verCotizacionEmbarqueFiles($idProveedor)
    {
        $this->db->select('*')
            ->from($this->table_contenedor_cotizacion_proveedores_documentacion)
            ->where('id_proveedor', $idProveedor);
        $query = $this->db->get();
        return $query->result();
    }
    public function deleteFile($idFile)
    {
        $this->db->where('id', $idFile);
        $this->db->delete($this->table_contenedor_cotizacion_proveedores_documentacion);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function deleteFileAduana($idAduana)
    {
        $this->db->where("id", $idAduana);
        $this->db->delete($this->table_contenedor_aduana_files);
        if ($this->db->affected_rows() > 0) {
            return "success";
        }
        return false;
    }
    public function updateProveedorData($data, $idProveedor, $idCotizacion)
    {
        try {
            $this->db->select('estados,estados_proveedor,id_contenedor,code_supplier,id_cotizacion')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id', $idProveedor);
            //get estados list from tracking list

            $query = $this->db->get();
            $estado = $query->row()->estados;
            $estadoProveedor = $query->row()->estados_proveedor;
            $idContenedor = $query->row()->id_contenedor;
            $idCotizacion = $query->row()->id_cotizacion;
            $supplierCode = $query->row()->code_supplier;

            if ((isset($data['supplier_phone']) || isset($data['supplier']))) {
                $statusToUpdate = $this->providerCoordinacionOrderStatus[$this->STATUS_DATOS_PROVEEDOR] ?? 0;
                $estadoProveedorOrder = $this->providerCoordinacionOrderStatus[$estadoProveedor] ?? 0;
                if ($estadoProveedorOrder < $statusToUpdate) {
                    $this->db->where('id', $idProveedor);
                    $this->db->update($this->table_contenedor_cotizacion_proveedores, [
                        'estados' => $this->STATUS_DATOS_PROVEEDOR,
                        'supplier_phone' => $data['supplier_phone'] ?? null,
                        'supplier' => $data['supplier'] ?? null
                    ]);
                }
                //if estado ROTULADO NOT IN statesHistoryCotizacion update al provedor to rotulado where id_cotizacion
                // if (!in_array("ROTULADO", $statesHistoryCotizacion)) {
                //     $this->db->where('id_cotizacion', $idCotizacion);
                //     $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => 'ROTULADO']);
                //     //for each provider insert into table contenedor_proveedor_estados_tracking with estado ROTULADO and id_cotizacion and id_proveedor
                //     $providers = $this->db->select('id')->from($this->table_contenedor_cotizacion_proveedores)->where('id_cotizacion', $idCotizacion)->get()->result_array();
                //     foreach ($providers as $provider) {
                //         $this->db->insert($this->table_conteneodr_proveedor_estados_tracking, [
                //             'id_cotizacion' => $idCotizacion,
                //             'id_proveedor' => $provider['id'],
                //             'estado' => 'ROTULADO'
                //         ]);
                //     }
                // }


                $this->verifyContainerIsCompleted($idContenedor);
                $usuariosAlmacen = $this->getUsersByGrupo($this->roleContenedorAlmacen || $this->roleCatalogoChina);
                $ids = array_column($usuariosAlmacen, 'ID_Usuario');
                $message = "Se ha actualizado el proveedor con codigo de proveedor " . $supplierCode . " a estado DATOS PROVEEDOR";
                $notifications = $this->createNotification($ids, $message, "CARGA CONSOLIDADA", $this->user->ID_Usuario);
                foreach ($ids as $id) {
                    // $socketResponse = $this->sendEvent([
                    //     "project" => "intranet",
                    //     "role" => 0,
                    //     "user" => $id,
                    //     "action" => $this->cambioEstadoProveedor,
                    //     "message" => $message
                    // ]);
                }
            }

            if (
                isset($data['arrive_date_china']) &&
                (!isset($data['qty_box_china']) && !isset($data['cbm_total_china']))
            ) {
                $data['arrive_date_china'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['arrive_date_china'])));
                $estadoProveedorOrder = $this->providerOrderStatus[$estadoProveedor] ?? 0;
                $estadoProvedorToUpdate = $this->providerOrderStatus[$this->STATUS_CONTACTED] ?? 0;
                if ($estadoProveedorOrder < $estadoProvedorToUpdate) {
                    $this->db->where('id', $idProveedor);
                    $this->db->update($this->table_contenedor_cotizacion_proveedores, [
                        'estados_proveedor' => $this->STATUS_CONTACTED,
                        'arrive_date_china' => $data['arrive_date_china']
                    ]);
                    //INSERT INTO TABLE contenedor_proveedor_estados_tracking with estado CONTACTED and id_cotizacion and id_proveedor
                    $this->db->insert($this->table_conteneodr_proveedor_estados_tracking, [
                        'id_cotizacion' => $idCotizacion,
                        'id_proveedor' => $idProveedor,
                        'estado' => $this->STATUS_CONTACTED
                    ]);
                    //conver yyyy-mm-dd to dd de Mes
                    $date = DateTime::createFromFormat('Y-m-d', $data['arrive_date_china']);
                    $month = $date->format('F');
                    $day = $date->format('d');
                    $months = [
                        'January' => 'Enero',
                        'February' => 'Febrero',
                        'March' => 'Marzo',
                        'April' => 'Abril',
                        'May' => 'Mayo',
                        'June' => 'Junio',
                        'July' => 'Julio',
                        'August' => 'Agosto',
                        'September' => 'Septiembre',
                        'October' => 'Octubre',
                        'November' => 'Noviembre',
                        'December' => 'Diciembre'
                    ];
                    $month = strtr($month, $months);
                    $date = $day . ' de ' . $month;
                    $message = 'Hola, hemos contactado a tu proveedor con código ' .
                        $supplierCode . ' nos comunica que la carga será enviada el ' .
                        $date . '.';
                    $this->db->select('telefono')
                        ->from($this->table_contenedor_cotizacion)
                        ->where('id', $idCotizacion);
                    $query = $this->db->get();
                    $telefono = $query->row()->telefono;

                    $telefono = preg_replace('/\s+/', '', $telefono);
                    $this->phoneNumberId = $telefono ? $telefono . '@c.us' : '';
                    $this->sendMessage($message);
                }
                $this->verifyContainerIsCompleted($idContenedor);
            }
            if (isset($data['qty_box_china']) && isset($data['cbm_total_china'])) {
                $dateTime = DateTime::createFromFormat('d/m/Y', $data['arrive_date_china']);
                if ($dateTime) {
                    $data['arrive_date_china'] = $dateTime->format('Y-m-d');
                }
                $estadoProveedorOrder = $this->providerOrderStatus[$estadoProveedor] ?? 0;
                log_message('error', "llego");
                $estadoProvedorToUpdate = $this->providerOrderStatus[$this->STATUS_RECIVED] ?? 0;
                if ($estadoProveedorOrder < $estadoProvedorToUpdate) {
                    $this->db->where('id', $idProveedor);
                    $this->db->update($this->table_contenedor_cotizacion_proveedores, [
                        'estados_proveedor' =>
                        $this->STATUS_RECIVED,

                        'qty_box_china' => $data['qty_box_china'],
                        'cbm_total_china' => $data['cbm_total_china'],
                        'arrive_date_china' => $data['arrive_date_china']

                    ]);
                    //insert into table contenedor_proveedor_estados_tracking with estado STATE_RECIVED and id_cotizacion and id_proveedor
                    $this->db->insert($this->table_conteneodr_proveedor_estados_tracking, [
                        'id_cotizacion' => $idCotizacion,
                        'id_proveedor' => $idProveedor,
                        'estado' => $this->STATUS_RECIVED
                    ]);
                    //update estado column in table $this->table to estado RECIBIENDO where id = $idContenedor




                    $usuariosAlmacen = $this->getUsersByGrupo($this->roleCoordinacion);
                    $ids = array_column($usuariosAlmacen, 'ID_Usuario');
                    $message = "Se ha actualizado el proveedor con codigo de proveedor " . $supplierCode . " a estado RECIBIDO";
                    // $notifications = $this->createNotification($ids, $message, "CARGA CONSOLIDADA", $this->user->ID_Usuario);
                    // $socketResponse = $this->sendEvent([
                    //     "project" => "intranet",
                    //     "role" => $this->roleCoordinacion,
                    //     "user" => "0",
                    //     "action" => $this->cambioEstadoProveedor,
                    //     "message" => $message
                    // ]);
                    // $socketResponse = $this->sendEvent([
                    //     "project" => "intranet",
                    //     "role" => $this->roleCotizador,
                    //     "user" => "0",
                    //     "action" => $this->cambioEstadoProveedor,
                    //     "message" => $message
                    // ]);
                } else {
                    $message = "Se ha actualizado la cantidad de cajas y volumen total de china del proveedor con codigo de proveedor " . $supplierCode . " a " . $data['qty_box_china'] . " cajas y " . $data['cbm_total_china'] . " m3";
                    // $socketResponse = $this->sendEvent([
                    //     "project" => "intranet",
                    //     "role" => $this->roleCoordinacion,
                    //     "user" => 0,
                    //     "action" => $this->cambioEstadoProveedor,
                    //     "message" => $message
                    // ]);
                    // $socketResponse = $this->sendEvent([
                    //     "project" => "intranet",
                    //     "role" => $this->roleCotizador,
                    //     "user" => 0,
                    //     "action" => $this->cambioEstadoProveedor,
                    //     "message" => $message
                    // ]);
                }
                $contenedorEstado = $this->db->select('estado_china')->from($this->table)->where('id', $idContenedor)->get()->row()->estado_china;
                if ($contenedorEstado == "PENDIENTE") {
                    $this->db->where('id', $idContenedor);
                    $this->db->update($this->table, [
                        'estado_china' => "RECIBIENDO",
                        "estado" => "RECIBIENDO"
                    ]);
                }
            }
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores, $data);
            $this->db->close();
            $this->db->initialize();
            $this->db->select('SUM(ifnull(cbm_total_china,0)) as volumen_china')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id_cotizacion', $idCotizacion)
                ->where('estados_proveedor', "LOADED");
            $query = $this->db->get();
            $volumenChina = $query->row()->volumen_china;

            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion, ['volumen_china' => $volumenChina]);
            $this->verifyContainerIsCompleted($idContenedor);

            if ($this->db->error()->code != 0) {
                log_message('error', 'Error: ' . $this->db->error()['message']);
                return ['status' => "error", 'error' => $this->db->error()];
            } else {
                return "success";
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Error: ' . $e->getMessage());
            return ['status' => "error", 'error' => $e->getMessage()];
        }
    }
    public function uploadFileDocument($file, $idProveedor, $idCotizacion)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            'assets/images/agentecompra/'
        );
        $fileToInsert = [
            'file_path' => $fileUrl,
            'file_name' => $file['name'],
            'file_type' => $file['type'],
            'file_size' => $file['size'],
            'id_proveedor' => $idProveedor,
            'id_cotizacion' => $idCotizacion,
            'last_modified' => time(),
        ];
        if ($fileUrl) {
            $this->db->insert($this->table_contenedor_almacen_documentacion, $fileToInsert);
            if ($this->db->affected_rows() > 0) {
                $fileToReturn = [
                    'name' => $file['name'],
                    'path' => $fileUrl,
                    'thumbnaill' => $fileUrl,
                    'type' => $file['type'],
                    'lastModified' => time(),
                    'size' => $file['size'],
                    'id' => 1,
                ];
                return ['status' => "success", 'error' => false, "data" => $fileToReturn];
            }
        }



        // if ($fileUrl) {
        //     $this->db->insert($this->table_contenedor_documentacion_files, ['file_url' => $fileUrl]);
        //     if ($this->db->affected_rows() > 0) {
        //         return ['status' => "success", 'error' => false];
        //     }
        // }
        return ['status' => "error", 'error' => true];
    }
    public function uploadFileAlmacenInspection($file, $idProveedor, $idCotizacion)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFilesVideos();
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            'assets/images/agentecompra/'
        );
        $fileToInsert = [
            'file_path' => $fileUrl,
            'file_name' => $file['name'],
            'file_type' => $file['type'],
            'file_size' => $file['size'],
            'id_proveedor' => $idProveedor,
            'id_cotizacion' => $idCotizacion,
            'last_modified' => time(),
        ];
        log_message('error', "fileUrl: " . $fileUrl);
        log_message('error', "fileToInsert: " . json_encode($fileToInsert));

        if ($fileUrl) {

            $this->db->insert($this->table_contenedor_almacen_inspection, $fileToInsert);
            if ($this->db->error()['code'] != 0) {
                return ['status' => "error", 'error' => $this->db->error()];
            }
            if ($this->db->affected_rows() > 0) {
                $this->validateToSendInspectionMessage($idProveedor);
                $fileToReturn = [
                    'name' => $file['name'],
                    'path' => $fileUrl,
                    'thumbnaill' => $fileUrl,
                    'type' => $file['type'],
                    'lastModified' => time(),
                    'size' => $file['size'],
                    'id' => 1,
                ];
                return ['status' => "success", 'error' => false, "data" => $fileToReturn];
                // $sendMesagge=
                // if($sendMesagge){
                //     //get nombre from table cotizaciones, get qtyboxchina y suppliercode from table proveedor
                //     $this->db->select('nombre')
                //         ->from($this->table_contenedor_cotizacion)
                //         ->where('id', $idCotizacion);
                //     $query = $this->db->get();
                //     $cliente = $query->row()->nombre;
                //     $this->db->select('qty_box_china,code_supplier')
                //         ->from($this->table_contenedor_cotizacion_proveedores)
                //         ->where('id', $idProveedor);
                //     $query = $this->db->get();
                //     $qtyBoxChina = $query->row()->qty_box_china;
                //     $supplierCode = $query->row()->code_supplier;
                //     //get media_id from all files inspection for this proveedor
                //     $this->db->select('media_id,file_type')
                //         ->from($this->table_contenedor_almacen_inspection)
                //         ->where('id_proveedor', $idProveedor);
                //     $query = $this->db->get();
                //     $mediaData = $query->result();
                //     $response=$this->sendInspectionXSupllier($mediaData,$cliente,$qtyBoxChina,$supplierCode);

                // }

            }
        }
        return ['status' => "error", 'error' => true];
    }
    public function getFilesAlmacenDocument($idProveedor)
    {
        try {
            $this->db->select('id,file_name as file_name,file_path as file_url,file_type as type,file_size as size,last_modified as lastModified,file_ext')
                ->from($this->table_contenedor_almacen_documentacion)
                ->where('id_proveedor', $idProveedor);
            $query = $this->db->get();
            $result = $query->result();

            return ['status' => "success", 'error' => false, "data" => $result];
        } catch (Exception $e) {
            log_message('error', 'Error: ' . $e->getMessage());
            return ['status' => "error", 'error' => $e->getMessage()];
        }
    }
    public function deleteFileDocumentation($idFile)
    {
        //from table_contenedor_almacen_documentacion unlink and delete file row
        try {
            $this->db->select('file_path')
                ->from($this->table_contenedor_almacen_documentacion)
                ->where('id', $idFile);
            $query = $this->db->get();
            $result = $query->result();
            unlink($result[0]->file_path);
            $this->db->where('id', $idFile);
            $this->db->delete($this->table_contenedor_almacen_documentacion);
            if (($this->db->error()['code'] != 0)) {
                log_message('error', 'Error: ' . $this->db->error()['message']);
                return "false";
            }
            return  "success";
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
    public function getFilesAlmacenInspection($idProveedor)
    {
        $this->db->select('id,file_name as file_name,file_path as file_url,file_type as type,file_size as size,last_modified as lastModified,file_type as file_ext,send_status')
            ->from($this->table_contenedor_almacen_inspection)
            ->where('id_proveedor', $idProveedor);
        $query = $this->db->get();
        $result = $query->result();

        return ['status' => "success", 'error' => false, "data" => $result];
    }
    function validateToSendInspectionMessage($idProveedor)
    {
        log_message('error', "validateToSendInspectionMessage: " . $idProveedor);
        $this->db->select('id, file_path,file_type,send_status')
            ->from($this->table_contenedor_almacen_inspection)
            ->where('id_proveedor', $idProveedor)
            ->group_start()
            ->where('file_type', 'image/jpeg')
            ->or_where('file_type', 'image/png')
            ->or_where('file_type', 'image/jpg')
            ->group_end();
        $query = $this->db->get();
        $imagesUrls = $query->result();
        $images = $query->num_rows();
        $this->db->select('id,file_path,file_type,send_status')
            ->from($this->table_contenedor_almacen_inspection)
            ->where('id_proveedor', $idProveedor)
            ->where('file_type', 'video/mp4');
        $query = $this->db->get();
        $videosUrls = $query->result();
        $videos = $query->num_rows();
        $this->db->select('estados_proveedor,code_supplier,qty_box_china,qty_box,id_cotizacion')
            ->from($this->table_contenedor_cotizacion_proveedores)
            ->where('id', $idProveedor);
        $query = $this->db->get();
        $estadoChina = $query->row()->estados_proveedor;
        $supplierCode = $query->row()->code_supplier;
        $qtyBoxChina = $query->row()->qty_box_china;
        $qtyBox = $query->row()->qty_box;
        $idCotizacion = $query->row()->id_cotizacion;
        $this->db->select('volumen,monto,id_contenedor')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $idCotizacion);
        $query = $this->db->get();
        $volumen = $query->row()->volumen;
        $valorCot = $query->row()->monto;
        $idContenedor = $query->row()->id_contenedor;
        $this->db->select('f_cierre')
            ->from($this->table)
            ->where('id', $idContenedor);
        $query = $this->db->get();
        $fCierre = $query->row()->f_cierre;
        $fCierre = date('d F', strtotime($fCierre));
        $fCierre = str_replace('January', 'Enero', $fCierre);
        $fCierre = str_replace('February', 'Febrero', $fCierre);
        $fCierre = str_replace('March', 'Marzo', $fCierre);
        $fCierre = str_replace('April', 'Abril', $fCierre);
        $fCierre = str_replace('May', 'Mayo', $fCierre);
        $fCierre = str_replace('June', 'Junio', $fCierre);
        $fCierre = str_replace('July', 'Julio', $fCierre);
        $fCierre = str_replace('August', 'Agosto', $fCierre);
        $fCierre = str_replace('September', 'Septiembre', $fCierre);
        $fCierre = str_replace('October', 'Octubre', $fCierre);
        $fCierre = str_replace('November', 'Noviembre', $fCierre);
        $fCierre = str_replace('December', 'Diciembre', $fCierre);

        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, [
            'estados_proveedor' => 'INSPECTION',
            'estados' => 'INSPECCIONADO',
        ]);

        $message = "Se ha actualizado el proveedor con codigo de proveedor " . $supplierCode . " a estado INSPECCIONADO";
        $this->db->select('nombre,telefono')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $idCotizacion);
        $query = $this->db->get();
        $cliente = $query->row()->nombre;
        $telefono = $query->row()->telefono;
        $telefono = preg_replace('/\s+/', '', $telefono);
        $telefono .= $telefono ? '@c.us' : '';
        $this->phoneNumberId = $telefono;
        $sendStatus = true;
        foreach ($imagesUrls as $image) {
            if ($image->send_status == 'SENDED') {
                $sendStatus = false;
            }
        }
        foreach ($videosUrls as $video) {
            if ($video->send_status == 'SENDED') {
                $sendStatus = false;
            }
        }
        if ($sendStatus) {
            $message = $cliente . '----' . $supplierCode . '----' . ($qtyBoxChina ?? $qtyBox) . ' boxes. ' . "\n\n" .
                '📦 Tu carga llego a nuestro almacén de Yiwu, te comparto las fotos y videos. ' . "\n\n";

            $this->sendMessage('Hola buen día 🙋🏻‍♀' . "\n\n" . 'Inspección: ' . "\n" . $message);
        }
        //filter imagesUrls and videosUrls to get only the files that has send_status = 0
        $imagesUrls = array_filter($imagesUrls, function ($image) {
            return $image->send_status == "PENDING";
        });
        $videosUrls = array_filter($videosUrls, function ($video) {
            return $video->send_status == "PENDING";
        });
        //send media inspection

        foreach ($imagesUrls as $image) {
            $this->sendMediaInspection($image->file_path, $image->file_type, null, null, 1, $image->id);
        }
        foreach ($videosUrls as $video) {
            $this->sendMediaInspection($video->file_path, $video->file_type, null, null, 1, $video->id);
        }
        return true;
    }
    function validateToSendInspectionMessage2($idProveedor)
    {
        log_message('error', "validateToSendInspectionMessage: " . $idProveedor);
        $this->db->select('id, file_path,file_type,send_status')
            ->from($this->table_contenedor_almacen_inspection)
            ->where('id_proveedor', $idProveedor)
            ->group_start()
            ->where('file_type', 'image/jpeg')
            ->or_where('file_type', 'image/png')
            ->or_where('file_type', 'image/jpg')
            ->group_end();
        $query = $this->db->get();
        $imagesUrls = $query->result();
        $images = $query->num_rows();
        $this->db->select('id,file_path,file_type,send_status')
            ->from($this->table_contenedor_almacen_inspection)
            ->where('id_proveedor', $idProveedor)
            ->where('file_type', 'video/mp4');
        $query = $this->db->get();
        $videosUrls = $query->result();
        $videos = $query->num_rows();
        $this->db->select('estados_proveedor,code_supplier,qty_box_china,qty_box,id_cotizacion')
            ->from($this->table_contenedor_cotizacion_proveedores)
            ->where('id', $idProveedor);
        $query = $this->db->get();
        $estadoChina = $query->row()->estados_proveedor;
        $supplierCode = $query->row()->code_supplier;
        $qtyBoxChina = $query->row()->qty_box_china;
        $qtyBox = $query->row()->qty_box;
        $idCotizacion = $query->row()->id_cotizacion;
        $this->db->select('volumen,monto,id_contenedor')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $idCotizacion);
        $query = $this->db->get();
        $volumen = $query->row()->volumen;
        $valorCot = $query->row()->monto;
        $idContenedor = $query->row()->id_contenedor;
        $this->db->select('f_cierre')
            ->from($this->table)
            ->where('id', $idContenedor);
        $query = $this->db->get();
        $fCierre = $query->row()->f_cierre;
        $fCierre = date('d F', strtotime($fCierre));
        $fCierre = str_replace('January', 'Enero', $fCierre);
        $fCierre = str_replace('February', 'Febrero', $fCierre);
        $fCierre = str_replace('March', 'Marzo', $fCierre);
        $fCierre = str_replace('April', 'Abril', $fCierre);
        $fCierre = str_replace('May', 'Mayo', $fCierre);
        $fCierre = str_replace('June', 'Junio', $fCierre);
        $fCierre = str_replace('July', 'Julio', $fCierre);
        $fCierre = str_replace('August', 'Agosto', $fCierre);
        $fCierre = str_replace('September', 'Septiembre', $fCierre);
        $fCierre = str_replace('October', 'Octubre', $fCierre);
        $fCierre = str_replace('November', 'Noviembre', $fCierre);
        $fCierre = str_replace('December', 'Diciembre', $fCierre);

        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, [
            'estados_proveedor' => 'INSPECTION',
            'estados' => 'INSPECCIONADO',
        ]);

        $message = "Se ha actualizado el proveedor con codigo de proveedor " . $supplierCode . " a estado INSPECCIONADO";
        $this->db->select('nombre,telefono')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $idCotizacion);
        $query = $this->db->get();
        $cliente = $query->row()->nombre;
        $telefono = $query->row()->telefono;
        $telefono = preg_replace('/\s+/', '', $telefono);
        $telefono .= $telefono ? '@c.us' : '';
        $this->phoneNumberId = $telefono;
        $sendStatus = true;
        foreach ($imagesUrls as $image) {
            if ($image->send_status == 'SENDED') {
                $sendStatus = false;
            }
        }
        foreach ($videosUrls as $video) {
            if ($video->send_status == 'SENDED') {
                $sendStatus = false;
            }
        }
        if ($sendStatus) {
            $message = $cliente . '----' . $supplierCode . '----' . ($qtyBoxChina ?? $qtyBox) . ' boxes. ' . "\n\n" .
                '📦 Tu carga llego a nuestro almacén de Yiwu, te comparto las fotos y videos. ' . "\n\n";

            $this->sendMessage('Hola buen día 🙋🏻‍♀' . "\n\n" . 'Inspección: ' . "\n" . $message);
        }
        //filter imagesUrls and videosUrls to get only the files that has send_status = 0
        $imagesUrls = array_filter($imagesUrls, function ($image) {
            return $image->send_status == "PENDING";
        });
        $videosUrls = array_filter($videosUrls, function ($video) {
            return $video->send_status == "PENDING";
        });
        //send media inspection

        foreach ($imagesUrls as $image) {
            $this->sendMediaInspection($image->file_path, $image->file_type, null, null, 1, $image->id);
        }
        foreach ($videosUrls as $video) {
            $this->sendMediaInspection($video->file_path, $video->file_type, null, null, 1, $video->id);
        }
        return true;
    }
    public function getClientesHeader($idContenedor)
    {

        try {
            // Consulta para cbm_total_china usando DISTINCT para evitar duplicación
            $this->db->select('
            COALESCE(SUM( IF(cc.estado_cotizador = "CONFIRMADO", cccp.cbm_total_china, 0)), 0) as cbm_total_china
        ')
                ->from($this->table_contenedor_cotizacion_proveedores . ' cccp')
                ->join($this->table_contenedor_cotizacion . ' cc', 'cccp.id_cotizacion = cc.id')
                ->where('cccp.id_contenedor', $idContenedor)
                ->where('cccp.estados_proveedor', 'LOADED');

            // Subconsulta para cbm_total
            $this->db->select('(
            SELECT COALESCE(SUM(cbm_total), 0) 
            FROM ' . $this->table_contenedor_cotizacion_proveedores . ' 
            WHERE id_contenedor = ' . $idContenedor . '
            AND id_cotizacion IN (
                SELECT id 
                FROM ' . $this->table_contenedor_cotizacion . ' 
                WHERE estado_cotizador = "CONFIRMADO"
            )
        ) as cbm_total', false);



            // Subconsulta para total_logistica
            $this->db->select('(
            SELECT COALESCE(SUM(monto), 0) 
            FROM ' . $this->table_contenedor_cotizacion . ' 
            WHERE id IN (
                SELECT DISTINCT id_cotizacion 
                FROM ' . $this->table_contenedor_cotizacion_proveedores . ' 
                WHERE id_contenedor = ' . $idContenedor . '
            )
            AND estado_cotizador = "CONFIRMADO"
            AND id IN (
                SELECT id_cotizacion 
                FROM ' . $this->table_contenedor_cotizacion_proveedores . ' 
                WHERE id_contenedor = ' . $idContenedor . '
                AND estados_proveedor = "LOADED"
            )
        ) as total_logistica', false);
            // Subconsulta para total_qty_items
            $this->db->select('(
                SELECT COALESCE(SUM(qty_item), 0)
                FROM ' . $this->table_contenedor_cotizacion . '
                WHERE id IN (
                    SELECT DISTINCT id_cotizacion
                    FROM ' . $this->table_contenedor_cotizacion_proveedores . '
                    WHERE id_contenedor = ' . $idContenedor . '
                )
                AND estado_cotizador = "CONFIRMADO"
            ) as total_qty_items', false);
            $this->db->select('(SELECT COALESCE(SUM(monto), 0)
            FROM ' . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . ' 
            JOIN ' . $this->table_pagos_concept . ' ON ' . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . '.id_concept = ' . $this->table_pagos_concept . '.id
            WHERE id_contenedor = ' . $idContenedor . '
            AND ' . $this->table_pagos_concept . '.name = "LOGISTICA"
        ) as total_logistica_pagado', false);

            $query = $this->db->get();
            $result = $query->row();

            // Haz una consulta aparte para carga:
            $this->db->select('carga')
                ->from($this->table)
                ->where('id', $idContenedor);
            $cargaRow = $this->db->get()->row();

            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error: ' . $this->db->error()['message']);
            }

            // Obtener bl_file_url y lista_empaque_file_url del contenedor
            $this->db->select('bl_file_url, lista_embarque_url')
                ->from($this->table)
                ->where('id', $idContenedor);
            $query = $this->db->get();
            $result2 = $query->row();

            if ($result) {
                return [
                    'cbm_total_china' => $result->cbm_total_china,
                    'cbm_total' => $result->cbm_total,
                    'cbm_total_pendiente' => $result->cbm_total_pendiente,
                    'total_logistica' => $result->total_logistica,
                    'total_logistica_pagado' => round($result->total_logistica_pagado, 2),
                    'qty_items' => $result->total_qty_items,
                    'bl_file_url' => $result2->bl_file_url,
                    'carga' => $cargaRow ? $cargaRow->carga : '',
                    'lista_embarque_url' => $result2->lista_embarque_url
                ];
            } else {
                return [
                    'status' => "error",
                    'error' => false,
                    "data" => [
                        'cbm_total_china' => 0,
                        'cbm_total_pendiente' => 0,
                        'total_logistica' => 0,
                        'total_logistica_pagado' => 0,
                        'qty_items' => 0,
                        'cbm_total' => 0,
                        'bl_file_url' => '',
                        'carga' => '',
                        'lista_embarque_url' => ''
                    ]
                ];
            }
        } catch (Exception $e) {
            log_message('error', '' . $e->getMessage());
            return $e->getMessage();
        }

        if ($result) {
            return [
                'cbm_total_china' => $result->cbm_total_china,
                'monto' => $result2->monto
            ];
        } else {
            return ['status' => "error", 'error' => false, "data" => [
                'cbm_total_china' => 0,
                'monto' => 0
            ]];
        }
    }
    public function getCotizacionEmbarqueHeaders($idContenedor)
    {
        try {

            $userId = $this->user->ID_Usuario;


            // CBM Total China (todos los CONFIRMADO)
            $this->db->select('
                COALESCE(SUM(IF(cc.estado_cotizador = "CONFIRMADO", cccp.cbm_total_china, 0)), 0) as cbm_total_china
            ')
            ->from($this->table_contenedor_cotizacion_proveedores . ' cccp')
            ->join($this->table_contenedor_cotizacion . ' cc', 'cccp.id_cotizacion = cc.id')
            ->where('cccp.id_contenedor', $idContenedor);

            // CBM Total Perú (todos los CONFIRMADO)
            $this->db->select('(
                SELECT COALESCE(SUM(volumen), 0)
                FROM ' . $this->table_contenedor_cotizacion . '
                WHERE id IN (
                    SELECT DISTINCT id_cotizacion
                    FROM ' . $this->table_contenedor_cotizacion_proveedores . '
                    WHERE id_contenedor = ' . $idContenedor . '
                )
                AND estado_cotizador = "CONFIRMADO"
            ) as cbm_total_peru', false);

            // CBM Vendido (CONFIRMADO, solo del usuario)
            $this->db->select('(
                SELECT COALESCE(SUM(volumen), 0)
                FROM ' . $this->table_contenedor_cotizacion . '
                WHERE id_contenedor = ' . $idContenedor . '
                AND estado_cotizador = "CONFIRMADO"
                AND id_usuario = ' . $userId . '
            ) as cbm_vendido', false);

            // CBM Pendiente (NO CONFIRMADO, solo del usuario)
            $this->db->select('(
                SELECT COALESCE(SUM(volumen), 0)
                FROM ' . $this->table_contenedor_cotizacion . '
                WHERE id_contenedor = ' . $idContenedor . '
                AND estado_cotizador != "CONFIRMADO"
                AND id_usuario = ' . $userId . '
            ) as cbm_pendiente', false);

            // CBM Embarcado (LOADED, solo del usuario)
            $this->db->select('(
                SELECT COALESCE(SUM(cccp.cbm_total_china), 0)
                FROM ' . $this->table_contenedor_cotizacion_proveedores . ' cccp
                JOIN ' . $this->table_contenedor_cotizacion . ' cc ON cccp.id_cotizacion = cc.id
                WHERE cccp.id_contenedor = ' . $idContenedor . '
                AND cccp.estados_proveedor = "LOADED"
                AND cc.id_usuario = ' . $userId . '
            ) as cbm_embarcado', false);

            // Subconsulta para total_logistica
            $this->db->select('(
            SELECT COALESCE(SUM(monto), 0) 
            FROM ' . $this->table_contenedor_cotizacion . ' 
            WHERE id IN (
                SELECT DISTINCT id_cotizacion 
                FROM ' . $this->table_contenedor_cotizacion_proveedores . ' 
                WHERE id_contenedor = ' . $idContenedor . '
            )
            AND estado_cotizador = "CONFIRMADO"
        ) as total_logistica', false);
            // Subconsulta para total_qty_items
            $this->db->select('(
                SELECT COALESCE(SUM(qty_item), 0)
                FROM ' . $this->table_contenedor_cotizacion . '
                WHERE id IN (
                    SELECT DISTINCT id_cotizacion
                    FROM ' . $this->table_contenedor_cotizacion_proveedores . '
                    WHERE id_contenedor = ' . $idContenedor . '
                )
                AND estado_cotizador = "CONFIRMADO"
            ) as total_qty_items', false);
            //get coalesce sum from pagos where id_contenedor = $idContenedor and id_concept= 'LOGISTICA'
            $this->db->select('(SELECT COALESCE(SUM(monto), 0)
            FROM ' . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . ' 
            JOIN ' . $this->table_pagos_concept . ' ON ' . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . '.id_concept = ' . $this->table_pagos_concept . '.id
            WHERE id_contenedor = ' . $idContenedor . '
            AND ' . $this->table_pagos_concept . '.name = "LOGISTICA"
        ) as total_logistica_pagado', false);
            $query = $this->db->get();
            $result = $query->row();

            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error: ' . $this->db->error()['message']);
            }

            // Obtener bl_file_url y lista_empaque_file_url del contenedor
            $this->db->select('bl_file_url, lista_embarque_url')
                ->from($this->table)
                ->where('id', $idContenedor);
            $query = $this->db->get();
            $result2 = $query->row();

            // Si es el usuario 28791, obtener los CBM por usuario (vendido, pendiente, embarcado)
        // Si es el usuario 28791, obtener los CBM por usuario (vendido, pendiente, embarcado)
        if ($userId == 28791) {
            // CBM Vendido por usuario
            $cbmVendido = [];
            $cbmPendiente = [];
            $cbmEmbarcado = [];

            // Vendido
            $vendidoQuery = $this->db->query('
                SELECT u.No_Nombres_Apellidos, COALESCE(SUM(volumen), 0) as cbm_vendido
                FROM ' . $this->table_contenedor_cotizacion . ' c
                LEFT JOIN usuario u ON u.ID_Usuario = c.id_usuario
                WHERE id_contenedor = ? AND estado_cotizador = "CONFIRMADO"
                GROUP BY u.No_Nombres_Apellidos
            ', [$idContenedor]);
            foreach ($vendidoQuery->result() as $row) {
                $cbmVendido[$row->No_Nombres_Apellidos] = $row->cbm_vendido;
            }

            // Pendiente
            $pendienteQuery = $this->db->query('
                SELECT u.No_Nombres_Apellidos, COALESCE(SUM(volumen), 0) as cbm_pendiente
                FROM ' . $this->table_contenedor_cotizacion . ' c
                LEFT JOIN usuario u ON u.ID_Usuario = c.id_usuario
                WHERE id_contenedor = ? AND estado_cotizador != "CONFIRMADO"
                GROUP BY u.No_Nombres_Apellidos
            ', [$idContenedor]);
            foreach ($pendienteQuery->result() as $row) {
                $cbmPendiente[$row->No_Nombres_Apellidos] = $row->cbm_pendiente;
            }

            // Embarcado
            $embarcadoQuery = $this->db->query('
                SELECT u.No_Nombres_Apellidos, COALESCE(SUM(cccp.cbm_total_china), 0) as cbm_embarcado
                FROM ' . $this->table_contenedor_cotizacion_proveedores . ' cccp
                JOIN ' . $this->table_contenedor_cotizacion . ' cc ON cccp.id_cotizacion = cc.id
                LEFT JOIN usuario u ON u.ID_Usuario = cc.id_usuario
                WHERE cccp.id_contenedor = ? AND cccp.estados_proveedor = "LOADED"
                GROUP BY u.No_Nombres_Apellidos
            ', [$idContenedor]);
            foreach ($embarcadoQuery->result() as $row) {
                $cbmEmbarcado[$row->No_Nombres_Apellidos] = $row->cbm_embarcado;
            }

            return [
                'cbm_total_china'   => $result->cbm_total_china,
                'cbm_total_peru'    => $result->cbm_total_peru,
                'cbm_vendido'       => $cbmVendido,
                'cbm_pendiente'     => $cbmPendiente,
                'cbm_embarcado'     => $cbmEmbarcado,
                'total_logistica'   => $result->total_logistica,
                'total_logistica_pagado' => $result->total_logistica_pagado,
                'qty_items'         => $result->total_qty_items,
                'bl_file_url'       => $result2->bl_file_url,
                'lista_embarque_url'=> $result2->lista_embarque_url
            ];
        }

            if ($result) {
                return [
                    'cbm_total_china' => $result->cbm_total_china,
                    'cbm_total_peru' => $result->cbm_total_peru,
                    'cbm_vendido'       => $result->cbm_vendido,
                    'cbm_pendiente'     => $result->cbm_pendiente,
                    'cbm_embarcado'     => $result->cbm_embarcado,
                    'total_logistica' => $result->total_logistica,
                    'total_logistica_pagado' => round($result->total_logistica_pagado, 2),
                    'qty_items' => $result->total_qty_items,
                    'bl_file_url' => $result2->bl_file_url,
                    'lista_embarque_url' => $result2->lista_embarque_url
                ];
            } else {
                return [
                    'status' => "error",
                    'error' => false,
                    "data" => [
                        'cbm_total_china' => 0,
                        'cbm_pendiente' => 0,
                        'cbm_embarcado' => 0,
                        'cbm_vendido' => 0,
                        'total_logistica' => 0,
                        'total_logistica_pagado' => 0,
                        'qty_items' => 0,
                        'cbm_total_peru' => 0,
                        'bl_file_url' => '',
                        'lista_embarque_url' => ''
                    ]
                ];
            }
        } catch (Exception $e) {
            log_message('error', '' . $e->getMessage());
            return $e->getMessage();
        }
    }
    public function uploadBL($idContenedor, $file)
    {

        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();


        // Validar ruta de almacenamiento
        $uploadPath = './assets/images/agentecompra/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Subir archivo
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            $uploadPath
        );

        if (!$fileUrl) {
            return ['status' => "error", 'error' => "Error al subir el archivo."];
        }

        // Actualizar base de datos
        $this->db->where('id', $idContenedor);
        $this->db->update($this->table, ['bl_file_url' => $fileUrl]);

        if ($this->db->error()['code'] == 0) {
            $this->verifyContainerIsCompleted($idContenedor);
            return ['status' => "success", 'error' => false];
        }

        return ['status' => "error", 'error' => "No se pudo actualizar la base de datos."];
    }
    public function updateVolSelected($idCotizacion, $volSelected)
    {
        $this->db->where('id', $idCotizacion);
        $this->db->update($this->table_contenedor_cotizacion, ['vol_selected' => $volSelected]);
        if ($this->db->error()['code'] == 0) {
            return "success";
        }
        return false;
    }
    public function deleteBL($idContenedor)
    {
        // Verificar si el ID del contenedor es válido
        if (empty($idContenedor)) {
            return ['status' => 'error', 'message' => 'No se recibió el ID del contenedor.'];
        }

        // Obtener la URL del archivo BL para eliminarlo físicamente
        $this->db->select('bl_file_url')
            ->from($this->table)
            ->where('id', $idContenedor);
        $query = $this->db->get();
        $result = $query->row();

        if ($result && !empty($result->bl_file_url)) {
            // Eliminar el archivo físicamente
            $filePath = FCPATH . ltrim($result->bl_file_url, '/');
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Actualizar la base de datos para eliminar la referencia al archivo
        $this->db->where('id', $idContenedor);
        $this->db->update($this->table, ['bl_file_url' => null]);

        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'message' => 'Archivo eliminado correctamente.'];
        }

        return ['status' => 'error', 'message' => 'No se pudo eliminar el archivo.'];
    }
    public function getValidContainers()
    {
        //return array from 1 to 50 and remove this array with rows from table contenedor where id in array
        $this->db->select('carga')
            ->from($this->table);
        $query = $this->db->get();
        $containers = $query->result();
        $containersArray = [];
        foreach ($containers as $container) {
            $containersArray[] = $container->carga;
        }
        $containersArray = array_diff(range(1, 50), $containersArray);
        //convert to simple array
        $containersArray = array_values($containersArray);
        return $containersArray;
    }
    public function getUsersByGrupo($grupo)
    {
        /**
         * select u.ID_Usuario from usuario  u left  join grupo_usuario gu  on gu.ID_Usuario =u.ID_Usuario
         * join grupo g on g.ID_Grupo =gu.ID_Grupo
         * where g.No_Grupo ="Cliente" and u.Nu_Estado =1
         */
        try {
            $this->db->reset_query();

            $this->db->select('u.ID_Usuario')
                ->from('usuario u')
                ->join('grupo_usuario gu', 'gu.ID_Usuario = u.ID_Usuario')
                ->join('grupo g', 'g.ID_Grupo = gu.ID_Grupo')
                ->where('g.No_Grupo', $grupo)
                ->where('u.Nu_Estado', 1);
            $query = $this->db->get();
            $usuarios = $query->result();
            if ($this->db->error()['code'] != 0) {
                return ['status' => "error", 'error' => $this->db->error()];
            }
            return $usuarios;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function uploadGeneral($idContenedor, $file)
    {
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl = $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ],
            'assets/images/agentecompra/'
        );
        if ($fileUrl) {
            $this->db->where('id', $idContenedor);
            $this->db->update($this->table, ['factura_general_url' => $fileUrl]);

            if ($this->db->affected_rows() > 0) {
                return ['status' => "success", 'error' => false];
            }
        }
        return ['status' => "error", 'error' => true];
    }
    public function updateEstadoCotizador($ID, $estado)
    {
        try {
            //find if all proveedores have products not empty
            $this->db->select('id')
                ->from($this->table_contenedor_cotizacion_proveedores)
                ->where('id_cotizacion', $ID)
                //and where products is empty or null
                ->where('products is null or products=""');
            $query = $this->db->get();
            $result = $query->result();
            if (count($result) > 0 && $estado == "CONFIRMADO") {

                return ['status' => "error", 'error' => "No se puede cambiar el estado a " . $estado . " hasta que todos los proveedores tengan productos"];
            }
            $this->db->where('id', $ID);
            $this->db->update($this->table_contenedor_cotizacion, [
                'estado_cotizador' => $estado,
                'fecha_confirmacion' => date('Y-m-d H:i:s')
            ]);
            if ($this->db->error()['code'] != 0) {
                return ['status' => "error", 'error' => $this->db->error()];
            }
            if ($estado == "INTERESADO") {
                //get id_contenedor from table contenedor_consolidado_cotizacion
                $this->db->select('id_contenedor,nombre,telefono')
                    ->from($this->table_contenedor_cotizacion)
                    ->where('id', $ID);
                $query = $this->db->get();
                $result = $query->row();
                $idContenedor = $result->id_contenedor;
                $nombre = $result->nombre;
                $telefono = $result->telefono;
                //get f_cierre from table contenedor
                $this->db->select('f_cierre,carga')
                    ->from($this->table)
                    ->where('id', $idContenedor);
                $query = $this->db->get();
                $result = $query->row();
                $fCierre = $result->f_cierre;
                $carga = $result->carga;
                /*Hola @nombre del cliente@, sabemos que está interesad@ en el consolidado #@, y no queremos que te quedes sin espacio, deseas confirmar tu participación?
                */
                $message = "Hola " . $nombre . ", sabemos que está interesad@ en el consolidado #" . $carga . ", y no queremos que te quedes sin espacio, deseas confirmar tu participación? \n\n";

                $telefono = preg_replace('/\s+/', '', $telefono);
                $telefono = $telefono ? $telefono . '@c.us' : '';
                $data_json = [
                    'message' => $message,
                    'phoneNumberId' => $telefono,
                ];
                $data = [
                    'id_contenedor' => $idContenedor,
                    'id_cotizacion' => $ID,
                    'created_at' => date('Y-m-d H:i:s'),
                    'data_json' => json_encode($data_json),
                    'execution_at' => date('Y-m-d H:i:s', strtotime('+' . $this->defaultHoursInteresado . ' minutes')),
                    'time_between' => $this->defaultHoursInteresado,
                    'status' => 'PENDING',
                ];
                $this->db->insert($this->table_contenedor_cotizacion_crons, $data);
            }
            if ($estado == "CONFIRMADO") {
                $this->db->select('id_contenedor,nombre,telefono')
                    ->from($this->table_contenedor_cotizacion)
                    ->where('id', $ID);
                $query = $this->db->get();
                $result = $query->row();
                $idContenedor = $result->id_contenedor;
                $nombre = $result->nombre;
                $message = "El cliente " . $nombre . " ha pasado a confirmado, por favor contactar.";
                $usuariosCoordinacion = $this->getUsersByGrupo($this->roleCoordinacion);
                $ids = array_column($usuariosCoordinacion, 'ID_Usuario');
                $notifications = $this->createNotification($ids, $message, "CARGA CONSOLIDADA", $this->user->ID_Usuario);

                $socketResponse = $this->sendEvent([
                    "project" => "0",
                    "role" => $this->roleCoordinacion,
                    "user" => "0",
                    "action" => $this->aNewConfirmado,
                    "message" => $message,
                ]);
            }
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function downloadPlantillaGeneral($idContenedor)
    {
        try {
            $this->db->select('factura_general_url')
                ->from($this->table)
                ->where('id', $idContenedor);
            $query = $this->db->get();
            $result = $query->row();
            if ($result) {
                $facturaGeneralUrl = $result->factura_general_url;

                $startRow = 26;
                $startIndex = 26;
                $itemNColumn = "B";
                $tipoClienteColumn = "C";
                $clienteColumn = "D";
                $descriptionColumn = "E";
                $qtyColumn = "M";
                $qtyUnitColumn = "N";
                $unitPriceColumn = "O";
                $unitPriceUnitColumn = "P";
                $fobPriceColumn = "Q";
                $adValoremColumn = "R";
                $anticipoColumn = "S";
                $volSistemaColumn = "T";
                $dataSystem = $this->db->select('nombre,volumen,documento,volumen_doc,valor_doc,valor_cot,volumen_china,name,vol_selected,peso,telefono')
                    ->from($this->table_contenedor_cotizacion)
                    ->join($this->table_contenedor_tipo_cliente, 'contenedor_consolidado_cotizacion.id_tipo_cliente = contenedor_consolidado_tipo_cliente.id')
                    ->where('id_contenedor', $idContenedor)
                    ->where('estado_cliente!=', null)
                    ->get()->result();
                $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $facturaGeneralUrl);
                $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
                $facturaGeneralUrl = FCPATH . ltrim($decodedPath, '/');

                $objPHPExcel = PHPExcel_IOFactory::load($facturaGeneralUrl);
                $sheet = $objPHPExcel->getActiveSheet();
                $plantillaGeneralUrl = 'assets/downloads/PLANTILLA_GENERAL.xlsx';
                $newExcel = PHPExcel_IOFactory::load($plantillaGeneralUrl);
                $newSheet = $newExcel->getActiveSheet();

                $newSheet->setCellValue('A1', "CLIENTE");
                $newSheet->setCellValue('B1', "TIPO");
                $newSheet->setCellValue('C1', "DNI");
                $newSheet->setCellValue('D1', "TELEFONO");
                $newSheet->setCellValue('E1', "ITEM");
                $newSheet->setCellValue('F1', "PRODUCTO");
                $newSheet->setCellValue('N1', "CANTIDAD");
                $newSheet->setCellValue('O1', "PRECIO UNITARIO");
                $newSheet->setCellValue('P1', "ANTIDUMPING");
                $newSheet->setCellValue('Q1', "VALORACION");
                $newSheet->setCellValue('R1', "AD VALOREM");
                $newSheet->setCellValue('S1', "PERCEPCION");
                $newSheet->setCellValue('T1', "PESO");
                $newSheet->setCellValue('U1', "VOLUMEN SISTEMA");
                //get merge ranges from excel
                $highestRow = $sheet->getHighestRow();
                $nameActual = "";
                $mergedEndCell = 0;
                $mergedStartCell = $startIndex;
                $currentRow = $startRow;
                $newRow = 2;
                $continue = true;
                while ($continue) {
                    $mergeRanges = $sheet->getMergeCells();
                    $mergeStart = $currentRow;
                    $mergeEnd = $currentRow;
                    //if sheet b column trim =TOTAL FOB PRICE then break
                    if (
                        trim($sheet->getCell('B' . $currentRow)->getValue()) == "TOTAL FOB PRICE"
                        || $currentRow >= $highestRow
                    ) {
                        $continue = false;
                        break;
                    }
                    foreach ($mergeRanges as $mergeRange) {
                        if (strpos($mergeRange, 'D') !== false) {
                            list($startCell, $endCell) = explode(':', $mergeRange);
                            $startRow = (int)preg_replace('/[^0-9]/', '', $startCell);
                            $endRow = (int)preg_replace('/[^0-9]/', '', $endCell);

                            if ($currentRow >= $startRow && $currentRow <= $endRow) {
                                $mergeStart = $startRow;
                                $mergeEnd = $endRow;
                                break;
                            }
                        }
                    }

                    // Get client and type info from merged range
                    $clientName = $sheet->getCell('D' . $mergeStart)->getValue();
                    $clientType = $sheet->getCell('C' . $mergeStart)->getValue();
                    $newSheet->mergeCells('F' . $newRow . ':M' . $newRow);

                    $mergeStartRow = $newRow;

                    // Process all rows within the merge range
                    for ($i = $mergeStart; $i <= $mergeEnd; $i++) {
                        // Get product information
                        $itemNo = $sheet->getCell('B' . $i)->getValue();
                        $description = $sheet->getCell('E' . $i)->getValue();
                        $quantity = $sheet->getCell('M' . $i)->getValue();
                        $quantityUnit = $sheet->getCell('N' . $i)->getValue();
                        $unitPrice = $sheet->getCell('O' . $i)->getValue();
                        $unitPriceUnit = $sheet->getCell('P' . $i)->getValue();
                        $fobPrice = $sheet->getCell('Q' . $i)->getValue();
                        $adValorem = $sheet->getCell('R' . $i)->getValue();
                        $antiDumping = $sheet->getCell('S' . $i)->getValue();
                        $volSistema = $sheet->getCell('T' . $i)->getValue();
                        //insert before $newRow-1
                        $newSheet->insertNewRowBefore($newRow, 1);
                        $newSheet->mergeCells('F' . $newRow . ':M' . $newRow);
                        // Transfer to new Excel
                        $newSheet->setCellValue('A' . $newRow, $clientName);
                        $newSheet->setCellValue('B' . $newRow, $clientType);

                        $newSheet->setCellValue('E' . $newRow, $itemNo);
                        $newSheet->setCellValue('F' . $newRow, $description);
                        $newSheet->setCellValue('N' . $newRow, $quantity);
                        $newSheet->setCellValue('O' . $newRow, $unitPrice);
                        $newSheet->setCellValue('P' . $newRow, $antiDumping);
                        $newSheet->setCellValue('Q' . $newRow, 0);

                        $newSheet->setCellValue('R' . $newRow, $adValorem);
                        $newSheet->setCellValue('S' . $newRow, 0.035);
                        $newSheet->setCellValue('U' . $newRow, $volSistema);
                        //center horizontally all columns in this row
                        $newSheet->getStyle('A' . $newRow . ':U' . $newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        //apply dollar format to columns O, P, Q, R, S to percentage format
                        $newSheet->getStyle('O' . $newRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        $newSheet->getStyle('P' . $newRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        $newSheet->getStyle('Q' . $newRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        $newSheet->getStyle('R' . $newRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        $newSheet->getStyle('S' . $newRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        foreach ($dataSystem as $data) {
                            log_message('error', 'Comparing: ' . trim($data->nombre) . ' with ' . trim($clientName));
                            if ($this->isNameMatch($clientName, $data->nombre)) {
                                //$newSheet->setCellValue('C' . $newRow, $data->name);
                                $newSheet->setCellValue('C' . $newRow, $data->documento);
                                $newSheet->setCellValue('D' . $newRow, $data->telefono);
                                $newSheet->setCellValue('T' . $newRow, $data->peso);
                                break;
                            }
                        }
                        $newRow++;
                    }
                    if ($mergeStartRow < ($newRow - 1)) {
                        // Merge columns A, B, C, D, T, and U
                        $columnsToMerge = ['A', 'B', 'C', 'D', 'T', 'U'];
                        foreach ($columnsToMerge as $column) {
                            $newSheet->mergeCells($column . $mergeStartRow . ':' . $column . ($newRow - 1));
                        }

                        // Apply cell styling to merged cells
                        foreach ($columnsToMerge as $column) {
                            $newSheet->getStyle($column . $mergeStartRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                    }
                    $currentRow = $mergeEnd + 1;
                }
                //center horizontally all columns  sete o  p q r to dollar format and  s to percentage format

                $newSheet->getStyle('A1:U1')->getFont()->setBold(true);
                $newSheet->mergeCells('F1:M1');

                $newSheet->getStyle('A1:U1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $newSheet->getStyle('A1:U1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
                $newSheet->getStyle('A1:U1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $newSheet->getStyle('A1:U1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $newSheet->getStyle('A1:U1')->getAlignment()->setWrapText(true);
                $newSheet->getStyle('A1:U1')->getAlignment()->setShrinkToFit(true);
                //set all columns to center horizontally
                //set  r column to percentange format
                $newSheet->getStyle('R2:R' . ($newRow - 1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                //set p lastrow +1 =sum(p2:p lastrow)
                $newSheet->setCellValue('P' . $newRow, '=SUM(P2:P' . ($newRow - 1) . ')');

                return $newExcel;
            } else {
                return ['status' => "error", 'error' => false, "data" => ''];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function generateMassiveExcelPayrolls($objPHPExcel, $idContainer)
    {
        $originalMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');
        $this->load->library('zip');
        $templatePath = 'assets/downloads/Boleta_Template.xlsx';
        $data = $this->getMassiveExcelData($objPHPExcel);
        $result = $this->db->select('cc.id,cc.tarifa,cc.nombre,tc.id as id_tipo_cliente, tc.name as tipoCliente,cc.correo')
            ->from($this->table_contenedor_cotizacion . ' as cc')
            ->join($this->table_contenedor_tipo_cliente . ' as tc', 'cc.id_tipo_cliente = tc.id')
            ->where('id_contenedor', $idContainer)
            ->where('estado_cotizador', 'CONFIRMADO')
            ->where('estado_cliente IS NOT NULL');
        try {
            $result = $this->db->get()->result();

            foreach ($data as &$cliente) {
                $nombreCliente = $cliente['cliente']['nombre'];

                foreach ($result as $item) {
                    log_message('error', 'Comparing: ' . $nombreCliente . ' with ' . $item->nombre);
                    if ($this->isNameMatch($nombreCliente, $item->nombre)) {
                        $cliente['cliente']['tarifa'] = $item->tarifa;
                        $cliente['cliente']['correo'] = $item->correo;
                        $cliente['cliente']['tipo_cliente'] = $item->tipoCliente;
                        $cliente['cliente']['id_tipo_cliente'] = $item->id_tipo_cliente;
                        $cliente['id'] = $item->id;
                        break;
                    }
                }
            }
            unset($cliente);
        } catch (Exception $e) {
            echo $e->getMessage();
            return $e->getMessage();
        }
        try {
            //log number of clients
            foreach ($data as $key => $value) {
                $objPHPExcel = PHPExcel_IOFactory::load($templatePath);

                $result = $this->getFinalCotizacionExcelv2($objPHPExcel, $value, $idContainer);
                $excelFileName = $result['excel_file_name'];
                $excelFilePath = $result['excel_file_path'];
                $fileUrl = base_url($excelFilePath); // Asumiendo que usas CodeIgniter

                $this->zip->read_file($excelFilePath, $excelFileName); // Add the Excel file to the ZIP
                //upload and return the file path

                $result['cotizacion_final_url'] = $fileUrl;
                //remove excel_file_name and excel_file_path
                unset($result['excel_file_name']);
                unset($result['excel_file_path']);
                unset($result['whatsapp']);
                //update table cotizciones with result
                log_message('error', json_encode($result));
                $this->db->where('id', $result['id']);
                $this->db->update($this->table_contenedor_cotizacion, $result);
            }

            // Save the ZIP file
            $zipFileName = 'Boletas.zip';
            $zipFilePath = 'assets/downloads/' . $zipFileName;
            $this->zip->archive($zipFilePath);
            ini_set('memory_limit', $originalMemoryLimit);
            gc_collect_cycles();
            return $zipFilePath;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $e->getMessage();
        }
    }
    public function getFinalCotizacionExcelv2($objPHPExcel, $data, $idContenedor)
    {
        try {
            //GOD IMPLEMENTATION
            $newSheet = $objPHPExcel->createSheet();
            $newSheet->setTitle('3');
            /**Base Styles */
            $grayColor = 'F8F9F9';
            $blueColor = '1F618D';
            $yellowColor = 'FFFF33';
            $greenColor = "009999";
            $whiteColor = "FFFFFF";
            $borders = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            /**Apply Tributes Calc Zones Rows Title */
            $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B3:G3');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B3', 'Calculo de Tributos');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B3');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B3:G3')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B5', 'Nombres');
            $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B5')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            $objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B6', 'Peso');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B7', "Valor CBM");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B8', 'Valor Unitario');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B9', 'Valoracion');
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B10', 'Cantidad');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B11', 'Valor FOB');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B12', 'Valor FOB Valoracion');
            $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B13', 'Distribucion %');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B14', 'Flete');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B15', 'Valor CFR');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B16', 'CFR Valorizado');
            $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B17', 'Seguro');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B18', 'Valor CIF');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B19', 'CIF Valorizado');
            $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
            $InitialColumn = 'C';

            $totalRows = 0;
            $cbmTotal = 0;
            $pesoTotal = 0;
            $logistica = 0;
            $impuestos = 0;
            $fob = 0;
            $tarifa = $data['cliente']['tarifa'];
            $sheet1 = $objPHPExcel->getSheet(0);

            //first iterate for tributes zone, set values and apply styles to cells
            foreach ($data['cliente']['productos'] as $producto) {
                //validate if $InitialColumn is more than Z then set A$

                $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', $producto["nombre"]);
                //APLY BACKGROUND COLOR BLUE AND LETTERS WHITE
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', 0);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', 0);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '8', $producto["precio_unitario"]);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '9', $producto["valoracion"]);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', $producto["cantidad"]);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=" . $InitialColumn . "8*" . $InitialColumn . "10");
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=" . $InitialColumn . "10*" . $InitialColumn . "9");
                //set format currency with dollar symbol $InitialColumn.8,9,11,12
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                //set auto size for columns

                $InitialColumn = $this->incrementColumn($InitialColumn);

                $totalRows++;
                $cbmTotal += $producto['cbm'];
            }
            $pesoTotal = $data['cliente']['productos'][0]['peso'];

            $objPHPExcel->getActiveSheet()->getColumnDimension($InitialColumn)->setAutoSize(true);
            //         $cliente['cliente']['tipo_cliente'] = $item->tipoCliente;  $cliente['cliente']['id_tipo_cliente'] = $item->id_tipo_cliente;

            $tipoCliente = trim($data['cliente']["tipo_cliente"]);
            log_message('error', 'Tipo Cliente: ' . $tipoCliente);
            $tipoClienteCell = $this->incrementColumn($InitialColumn, 3) . '6';
            $tipoClienteCellValue = $this->incrementColumn($InitialColumn, 3) . '7';

            $tarifaCell = $this->incrementColumn($InitialColumn, 4) . '6';
            $tarifaCellValue = $this->incrementColumn($InitialColumn, 4) . '7';
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($tipoClienteCell, "Tipo Cliente");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($tarifaCell, "Tarifa");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($tipoClienteCellValue, $tipoCliente);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($tarifaCellValue, $tarifa);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCell)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCell)->getAlignment()->setShrinkToFit(true);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCell)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCell)->getAlignment()->setShrinkToFit(true);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCellValue)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCellValue)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCellValue)->getAlignment()->setShrinkToFit(true);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCellValue)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCellValue)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCellValue)->getAlignment()->setShrinkToFit(true);
            //apply borders to cells
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCell)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCell)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle($tipoClienteCellValue)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle($tarifaCellValue)->applyFromArray($borders);


            //create remaining zones and apply styles
            $InitialColumnLetter = $this->incrementColumn($InitialColumn, -1);
            $LastColumnLetter = $InitialColumn;
            $objPHPExcel->getActiveSheet()->getStyle('B5:' . $InitialColumn . '19')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B28:' . $InitialColumn . '32')->applyFromArray($borders);

            $objPHPExcel->getActiveSheet()->getStyle('B40:' . $InitialColumn . '40')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B43:' . $InitialColumn . '47')->applyFromArray($borders);

            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '5', "Total");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '6', $pesoTotal > 1000 ? round($pesoTotal / 1000, 2) : $pesoTotal);
            if ($pesoTotal > 1000) {
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" tn"');
            } else {
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getNumberFormat()->setFormatCode('0.00" Kg"');
            }
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $cbmTotal);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '7')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '10', "=SUM(C10:" . $InitialColumnLetter . "10)");

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '11', "=SUM(C11:" . $InitialColumnLetter . "11)");
            $VFOBCell = $InitialColumn . '11';
            $CBMTotal = $InitialColumn . "7";
            //log CBMTotal cell value
            $FleteCell = $InitialColumn . '14';
            $CobroCell = $InitialColumn . '40';
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', $data['cliente']['productos'][0]['cbm']);
            $cbmTotalProductos = $data['cliente']['productos'][0]['cbm'];

            $tarifaValue = $tarifa;
            $cbmTotalProductos = round($cbmTotalProductos, 2);
            if (trim(strtoupper($tipoCliente)) == "NUEVO") {
                switch ($cbmTotalProductos) {
                    case $cbmTotalProductos < 0.59 && $cbmTotalProductos > 0:
                        $tarifaValue = 280;
                        break;
                    case $cbmTotalProductos < 1.00 && $cbmTotalProductos > 0.59:
                        $tarifaValue = 375;
                        break;
                    case $cbmTotalProductos < 2.00 && $cbmTotalProductos > 1.00:
                        $tarifaValue = 375;
                        break;
                    case $cbmTotalProductos < 3.00 && $cbmTotalProductos > 2.00:
                        $tarifaValue = 350;
                        break;
                    case $cbmTotalProductos <= 4.10 && $cbmTotalProductos > 3.00:
                        $tarifaValue = 325;
                        break;
                    case $cbmTotalProductos > 4.10:
                        $tarifaValue = 300;
                }
            } else if (trim(strtoupper($tipoCliente)) == "ANTIGUO") {
                switch ($cbmTotalProductos) {
                    case $cbmTotalProductos < 0.59 && $cbmTotalProductos > 0:
                        $tarifaValue = 260;
                        break;
                    case $cbmTotalProductos < 1.00 && $cbmTotalProductos > 0.59:
                        $tarifaValue = 350;
                        break;
                    case $cbmTotalProductos <= 2.09 && $cbmTotalProductos > 1.00:
                        $tarifaValue = 350;
                        break;
                    case $cbmTotalProductos <= 3.09 && $cbmTotalProductos > 2.09:
                        $tarifaValue = 325;
                        break;
                    case $cbmTotalProductos <= 4.10 && $cbmTotalProductos > 3.09:
                        $tarifaValue = 300;
                        break;
                    case $cbmTotalProductos > 4.10:
                        $tarifaValue = 280;
                }
            } else if (trim(strtoupper($tipoCliente)) == "SOCIO") {
                switch ($cbmTotalProductos) {
                    case $cbmTotalProductos < 0.60:
                        $tarifaValue = 250;
                        break;
                    case $cbmTotalProductos < 1.00:
                        $tarifaValue = 250;
                        break;
                    case $cbmTotalProductos < 2.00:
                        $tarifaValue = 250;
                        break;
                    case $cbmTotalProductos < 3.00:
                        $tarifaValue = 250;
                        break;
                    case $cbmTotalProductos < 4.00:
                        $tarifaValue = 250;
                        break;
                    case $cbmTotalProductos >= 4.10:
                        $tarifaValue = 250;
                }
            }

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($tarifaCellValue, $tarifaValue);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '14',
                "=IF($CBMTotal<1, $tarifaCellValue*0.6, $tarifaCellValue*0.6*$CBMTotal)"
            );            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=IF($CBMTotal<1,ROUNDUP($tarifaCellValue*0.4),ROUNDUP($tarifaCellValue*0.4*$CBMTotal))");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                $InitialColumn . '40',
                "=IF($CBMTotal<1, $tarifaCellValue*0.4,$tarifaCellValue*0.4*$CBMTotal)"
            );
            $antidumpingSum = 0;
            $InitialColumn = 'C';
            //second iteration  for each product and set values and apply styles
            foreach ($data['cliente']['productos'] as $producto) {
                //$INITIALCOLUMN13 =ROUND($VFOBCell/$InitialColumn.'11') TO PERCENTAGE;

                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '13', "=" . $InitialColumn . '11/' . $VFOBCell);
                $distroCell = $InitialColumn . '13';
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '25', $tarifaValue);


                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                //$initialcolumn14=round($FleteCell*$InitialColumn.'13',2)
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=" . $FleteCell . '*' . $InitialColumn . '13');
                //$objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', '0');
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                //$initialcolumn15=roundup( $initialcolumn11+$initialcolumn14,2)

                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=" . $InitialColumn . '11+' . $InitialColumn . '14');
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $cfrCell = $InitialColumn . '15';
                //$initialcolumn15=roundup( $initialcolumn12+$initialcolumn14,2)
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=" . $InitialColumn . '12+' . $InitialColumn . '14');
                $cfrvCell = $InitialColumn . '16';
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $seguroCell = $InitialColumn . '17';
                //set currency format with dollar symbol
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                //IF COBROCELL IS GREATER THAN 5000 SET THE VALUE TO $initialcolumn17  TO roundup100/ distroCell ELSE SET roundup50/distroCell
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=IF(" . $LastColumnLetter . "11>5000,100*" . $distroCell . ",50*" . $distroCell . ")");
                //initial18 is roundup($cfrCell+$seguroCell,2)
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=" . $cfrCell . '+' . $seguroCell . "");
                //initial19 is roundup($cfrvCell+$seguroCell,2)
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=" . $cfrvCell . '+' . $seguroCell . "");

                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $quantityCell = $InitialColumn . '10';
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', $producto["antidumping"] * $producto["cantidad"] == "-" ? 0 : "=" . $InitialColumn . '10*' . $producto["antidumping"]);
                $antidumpingSum += $producto["antidumping"] * $producto["cantidad"];
                //set currency format with $ symbol
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', $producto["ad_valorem"]);
                //set porcentage format
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                //set text color red
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                $AdValoremCell = $InitialColumn . '28';
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                    $InitialColumn . '28',
                    "=MAX(" . $InitialColumn . "19," . $InitialColumn . "18)*" . $InitialColumn . "27"
                );
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "$" . $row["igv_value"]);
                //set initialcolumn29 = $row["igv"]*initialcolumn19
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=" . (16 / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "$" . $row["ipm_value"]);
                //set initialcolumn30 = $row["ipm"]*initialcolumn19
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=" . (2 / 100) . "*(" . "MAX(" . $InitialColumn . "19," . $InitialColumn . "18)+" . $AdValoremCell . ")");
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "$" . $row["percepcion_value"]);
                //set initialcolumn31 = $row["percepcion"]*initialcolumn19
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                    $InitialColumn . '31',
                    "=" . ($producto['percepcion']) . "*(MAX(" . $InitialColumn . '18,' . $InitialColumn . '19) +' . $InitialColumn . '28+' . $InitialColumn . '29+' . $InitialColumn . '30)'
                );
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sum = "=SUM(" . $InitialColumn . "28:" . $InitialColumn . "31)";
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', $sum);
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=" . $distroCell . "*" . $CobroCell);
                //Set currency format with dollar symbol
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $producto["nombre"]);
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "$" . $producto["Total_Cantidad"]);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $producto["cantidad"]);
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "$" . round($producto["Total_Cantidad"] / $producto["Cantidad"], 2));
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', 'S/.' . round(($producto["Total_Cantidad"] / $producto["Cantidad"]) * 3.7, 2));
                // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', $producto["Nombre_Comercial"]);
                //initial column 44=sum(initialcolumn16+initialcolumn40+initialcolumn32)
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue(
                    $InitialColumn . '44',
                    "=SUM(" . $InitialColumn . "15," . $InitialColumn . "40," . $InitialColumn . "32,(" . $InitialColumn . "26" . "))"
                );
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '45', $producto["cantidad"]);
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '46', "=SUM(" . $InitialColumn . "44/" . $InitialColumn . "45)");
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '46')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                //initial column 47=S/.initialcolumn46*3.7
                $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '47', "=" . $InitialColumn . "46*3.7");
                $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '47')->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
                $InitialColumn++;
            }

            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', "=" . $CobroCell);
            //$objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '7', "0");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '11')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '12', "=SUM(C12:" . $InitialColumnLetter . "12)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '12')->getFont()->setBold(true);
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '14', "=SUM(C14:" . $InitialColumnLetter . "14)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '14')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '15', "=SUM(C15:" . $InitialColumnLetter . "15)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '15')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '16', "=SUM(C16:" . $InitialColumnLetter . "16)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '16')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '17', "=SUM(C17:" . $InitialColumnLetter . "17)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '17')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '18', "=SUM(C18:" . $InitialColumnLetter . "18)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '18')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '19', "=SUM(C19:" . $InitialColumnLetter . "19)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '19')->getFont()->setBold(true);

            $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B23:E23');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B23', 'Tributos Aplicables');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B23');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B23:E23')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B26', 'ANTIDUMPING');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B28', 'AD VALOREM');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B29', 'IGV');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B30', 'IPM');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B31', 'PERCEPCION');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B32', 'TOTAL');
            //waos
            $objPHPExcel->getActiveSheet()->getStyle('B26:' . $InitialColumn . '26')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('C27:' . $InitialColumn . '27')->applyFromArray($borders);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '26', "=SUM(C26:" . $InitialColumnLetter . "26)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '26')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            // Verificar si el valor es numérico

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '27', "=SUM(C27:" . $InitialColumnLetter . "27)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '27')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '28', "=SUM(C28:" . $InitialColumnLetter . "28)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '28')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '29', "=SUM(C29:" . $InitialColumnLetter . "29)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '29')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '30', "=SUM(C30:" . $InitialColumnLetter . "30)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '30')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '31', "=SUM(C31:" . $InitialColumnLetter . "31)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '31')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '32', "=SUM($InitialColumn" . "28:" . $InitialColumn . "31)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '32')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            //Costos Destinos
            $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B37:E37');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B37', 'Costos Destinos');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B37');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B37:E37')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B40', 'ITEM');
            // $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '40', "=SUM(C40:" . $InitialColumnLetter . "40)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->setActiveSheetIndex(2)->mergeCells('B41:E41');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B43', 'ITEM');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B44', 'COSTO TOTAL');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B45', 'CANTIDAD');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B46', 'COSTO UNITARIO');
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B47', 'COSTO SOLES');

            //a
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44" . ":" . $InitialColumnLetter . "44)");
            $productsCount = count($data['cliente']['productos']);
            $ColumndIndex = PHPExcel_Cell::stringFromColumnIndex($productsCount + 1);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J20', "=MAX('3'!C27:" . $ColumndIndex . "27)");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '43', "Total");
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue($InitialColumn . '44', "=SUM(C44:" . $InitialColumnLetter . "44)");
            $objPHPExcel->getActiveSheet()->getStyle($InitialColumn . '44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $columnaIndex = PHPExcel_Cell::stringFromColumnIndex($productsCount + 2);

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('K14', "='3'!" . $columnaIndex . "11");

            // Construir la fórmula para sumar los valores de las celdas en las columnas 14 y 17
            $formula = "='3'!" . $columnaIndex . "14 + '3'!" . $columnaIndex . "17";

            // Establecer la fórmula en la celda K14
            $objPHPExcel->getActiveSheet()->setCellValue('K15', $formula);
            //k20 =columnaIndex 28
            $objPHPExcel->getActiveSheet()->setCellValue('K20', "='3'!" . $columnaIndex . "28");
            //k21 =columnaIndex 29
            $objPHPExcel->getActiveSheet()->setCellValue('K21', "='3'!" . $columnaIndex . "29");
            //k22 =columnaIndex 30
            $objPHPExcel->getActiveSheet()->setCellValue('K22', "='3'!" . $columnaIndex . "30");
            //k25 =columnaIndex 31
            $objPHPExcel->getActiveSheet()->setCellValue('K25', "='3'!" . $columnaIndex . "31");

            //k30 =$query[0]["Flete"]/$query[0]["Distribucion"]
            // $objPHPExcel->getActiveSheet()->setCellValue('K30', "='3'!" . $tarifaCellValue . "*J11");
            //if j11<1=sheet 3 tarifa cell value else j11* tarifa cell value
            $objPHPExcel->getActiveSheet()->setCellValue('K30', "=IF(J11<1, '3'!" . $tarifaCellValue . ", '3'!" . $tarifaCellValue . "*J11)");
            $LogisticaValue = $objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue();
            $CobroCellValue = $objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue();
            $ImpuestosCellValue = round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2);
            //convert $expirationDate dd//mm/yyyy to day de mes de año
            // $expirationDate = date('d/m/Y', strtotime($expirationDate));

            for ($row = 36; $row <= 39; $row++) {
                for ($col = 1; $col <= 12; $col++) {
                    $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $objPHPExcel->getActiveSheet()->setCellValue($cell, ''); // Establecer el valor de la celda como vacío
                    $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray(array()); // Eliminar cualquier estilo aplicado a la celda

                }
            }
            // //set column j y k autosize
            // $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            /*foreach query as row starts in row 36 set b as index +1, c as query["Nombre_Comercial"]
        f as query["Cantidad"] g as query["Valor_Unitario"] i as query["costo_total"]/ $query["Cantidad"]
        j as  query["costo_total"] k as query["Valor_Unitario"]*3.7
         */

            $lastRow = 0;
            $InitialColumn = 'C';
            //if count query is lower than 3 get sustract 3- count query and set the value to $substract and for each $substract remove border from row 36 to 39
            if ($productsCount < 3) {
                $substract = 3 - $productsCount;
                for ($i = 0; $i < $substract; $i++) {
                    $row = 36 + $i + $productsCount;
                    //set not borders from b$row to l$row
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray(array());
                }
                //remove borders from b36 to l39
            }
            for ($index = 0; $index < $productsCount; $index++) {
                $row = 36 + $index;
                if ($index >= 7 && $index != $productsCount) {
                    $sheet = $objPHPExcel->getActiveSheet();
                    $sheet->insertNewRowBefore($row, 1);
                }
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $index + 1);
                //SET FONT BOLD FALSE
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(false);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $data['cliente']['productos'][$index]["nombre"]);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, "='3'!" . $InitialColumn . 10);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(false);
                //center text
                $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, "='3'!" . $InitialColumn . 8);
                $objPHPExcel->getActiveSheet()->setCellValue('J11', "='3'!" . $CBMTotal);
                //set currency format with dollar symbol
                $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, "='3'!" . $InitialColumn . 46);
                //set currency format with dollar symbol
                $objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, "='3'!" . $InitialColumn . 44);
                $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(false);

                //set currency format with dollar symbol
                $objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $JCellVal = $objPHPExcel->getActiveSheet()->getCell('J' . $row)->getValue();
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "='3'!" . $InitialColumn . 47);
                //set currency format with pen symbol
                //combine cells from C$ROW to e$row
                $objPHPExcel->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
                $objPHPExcel->getActiveSheet()->mergeCells('G' . $row . ':H' . $row);
                //SET CURRRENCY FORMAT WITH DOLLAR SYMBOL

                $objPHPExcel->getActiveSheet()->mergeCells('K' . $row . ':L' . $row);
                //copy currency format from k$row-1 to k$row
                $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);
                $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setARGB($greenColor);
                //set letter color to white
                $style->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
                //center text
                //set normal weight
                $columnsToApply = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                //apply borders from b$row to l$row
                $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray($borders);
                //for each column in columnsToApply apply style center and auto size
                foreach ($columnsToApply as $column) {
                    //set font to calibri
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setName('Calibri');
                    //set font size to 11
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if ($column == 'K') {
                        $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
                    }
                }
                $InitialColumn = $this->incrementColumn($InitialColumn);
                $lastRow = $row;
            };
            $notUsedDefaultRows = 3 - $productsCount;
            if ($notUsedDefaultRows >= 0) {
                for ($i = 0; $i <= $notUsedDefaultRows; $i++) {
                    $row = 36 + $productsCount + $i;
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':L' . $row)->applyFromArray(array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_NONE,
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    ));
                    //set k background color to white
                    $style = $objPHPExcel->getActiveSheet()->getStyle('K' . $row);
                    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $style->getFill()->getStartColor()->setARGB($whiteColor);
                }
            }
            $lastRow++;
            //set b$latsrow values "total"
            if ($productsCount >= 7) {
                //unmerge b row
                $objPHPExcel->getActiveSheet()->unmergeCells('B' . $lastRow . ':L' . $lastRow);
                //merge b to e
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $lastRow . ':E' . $lastRow);
            }
            if ($notUsedDefaultRows >= 0) {
                //unmerge c to e
                $objPHPExcel->getActiveSheet()->mergeCells('C' . $lastRow . ':E' . $lastRow);

                $objPHPExcel->getActiveSheet()->unmergeCells('C' . $lastRow . ':E' . $lastRow);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $lastRow . ':E' . $lastRow);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $lastRow, "TOTAL");
            $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow . ':E' . $lastRow)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $lastRow, "=SUM(F36:F" . ($lastRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $lastRow, "=SUM(J36:J" . ($lastRow - 1) . ")");
            $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $objPHPExcel->getActiveSheet()->getStyle('J' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow . ':L' . $lastRow)->applyFromArray(array());
            //SET FONT SIZE TO 11
            $objPHPExcel->getActiveSheet()->getStyle('B' . $lastRow . ':L' . $lastRow + 1)->getFont()->setSize(11);
            $cellToCheck = 'I22';
            $rowToCheck = 23;
            $sheet = $objPHPExcel->getActiveSheet();

            // Obtener el valor de la celda

            // Verificar si se cumple la condición
            if ($antidumpingSum != 0) {
                // Insertar una nueva fila en la posición 22
                $sheet->insertNewRowBefore($rowToCheck, 1);

                // Opcional: Puedes rellenar la nueva fila con datos si es necesario
                $newRowIndex = $rowToCheck;
                $sheet->setCellValue('B' . $newRowIndex, "ANTIDUMPING");
                $sheet->setCellValue('K' . $newRowIndex, $antidumpingSum);
                //set currency format with dollar symbol

                //set b$NewRowIndex to l$NewRowIndex    background yellow
                $style = $sheet->getStyle('B' . $newRowIndex . ':L' . $newRowIndex);
                $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setARGB($yellowColor);
                // Ajusta según tus necesidades
                $objPHPExcel->getActiveSheet()->setCellValue('K24', "=SUM(K20:K23)");
            } else {
            }
            if ($objPHPExcel->getActiveSheet()->getCell('B23')->getValue() == "ANTIDUMPING") {
                $montoFinal = $objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue();
            }
            if ($sheet1->getCell('B23')->getValue() == "ANTIDUMPING") {
                $fob = $sheet1->getCell('K30')->getCalculatedValue();
                $logistica = $sheet1->getCell('K31')->getCalculatedValue();
                $impuestos = $sheet1->getCell('K32')->getCalculatedValue();
            } else {
                $fob = $sheet1->getCell('K29')->getCalculatedValue();
                $logistica = $sheet1->getCell('K30')->getCalculatedValue();
                $impuestos = $sheet1->getCell('K31')->getCalculatedValue();
            }

            //merge c8:c9
            $objPHPExcel->getActiveSheet()->mergeCells('C8:C9');
            //center vertically and horizontally
            $objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('C8', $data['cliente']['nombre']);
            $objPHPExcel->getActiveSheet()->setCellValue('C10', $data['cliente']['dni']);
            $objPHPExcel->getActiveSheet()->setCellValue('C11', $data['cliente']['telefono']);
            $objPHPExcel->getActiveSheet()->setCellValue('J9', $pesoTotal >= 1000 ? $pesoTotal / 1000 . " Tn" : $pesoTotal . " Kg");
            //mantain as numbrer custom format with m3 sufix
            $objPHPExcel->getActiveSheet()->getStyle('J11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER . ' "m3"');
            //FORMAT J11 2 DECIMALS
            $objPHPExcel->getActiveSheet()->getStyle('J11')->getNumberFormat()->setFormatCode('#,##0.00');

            //   $objPHPExcel->getActiveSheet()->setCellValue('I10', "QTY PROVEEDORES");
            $objPHPExcel->getActiveSheet()->setCellValue('I11', "CBM");

            //set number format
            $objPHPExcel->getActiveSheet()->getStyle('J9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            //SET COLUMN I AUTO SIZE
            $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

            //   $objPHPExcel->getActiveSheet()->setCellValue('K10', $query[0]["count_proveedores"]);
            $objPHPExcel->getActiveSheet()->setCellValue('J10', "");
            //APPPLY NUMBER FORMAT TO K10
            $objPHPExcel->getActiveSheet()->getStyle('K10')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $objPHPExcel->getActiveSheet()->setCellValue('L10', "");

            $objPHPExcel->getActiveSheet()->setCellValue('F11', $tipoCliente);
            if ($productsCount < 3) {
                //remove borders from b36 to l39
                $objPHPExcel->getActiveSheet()->getStyle('B39:L39')->applyFromArray(array());
            }
            $ClientName = $objPHPExcel->getActiveSheet()->getCell('C8')->getValue();
            //ajustar texto in c column
            $objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true);
            //select * from table_tarifas where id_tipo_cliente=$ID_Tipo_Cliente and updated_at is null
            $N20CellValue =
                "Hola " . $ClientName . " 😁 un gusto saludarte!
        A continuación te envío la cotización final de tu importación📋📦.
        🙋‍♂️ PAGO PENDIENTE :
        ☑️Costo CBM: $" . $CobroCellValue . "
        ☑️Impuestos: $" . $ImpuestosCellValue . "
        ☑️ Total: $" . ($ImpuestosCellValue + $CobroCellValue) . "
        Pronto le aviso nuevos avances, que tengan buen día🚢
        Último día de pago:";
            $objPHPExcel->getActiveSheet()->setCellValue('N20', $N20CellValue);
            //select
            //remove page 2
            $objPHPExcel->removeSheetByIndex(1);
            //set sheet 3 title to 2
            $objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->setTitle('2');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $excelFileName = 'Cotizacion' . $data['cliente']['nombre'] . '.xlsx';
            $excelFilePath = 'assets/downloads/' . $excelFileName;
            $montoFinal = $objPHPExcel->setActiveSheetIndex(0)->getCell('K30')->getCalculatedValue();
            //if b23 = antidumping then montofinal=k31;
            $objPHPExcel->setActiveSheetIndex(0);

            // Obtener valores después del recálculo
            $sheet1 = $objPHPExcel->getActiveSheet();
            if ($objPHPExcel->getActiveSheet()->getCell('B23')->getValue() == "ANTIDUMPING") {
                $montoFinal = $objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue();
            }
            if ($sheet1->getCell('B23')->getValue() == "ANTIDUMPING") {
                $fob = $sheet1->getCell('K30')->getCalculatedValue();
                $logistica = $sheet1->getCell('K31')->getCalculatedValue();
                $impuestos = $sheet1->getCell('K32')->getCalculatedValue();
            } else {
                $fob = $sheet1->getCell('K29')->getCalculatedValue();
                $logistica = $sheet1->getCell('K30')->getCalculatedValue();
                $impuestos = $sheet1->getCell('K31')->getCalculatedValue();
            }
            $objPHPExcel->setActiveSheetIndex(1);
            // $tarifaValue = $objPHPExcel->getActiveSheet()->getCell($tarifaCellValue)->getCalculatedValue();
            $logistica = $cbmTotalProductos < 1.00 ? $tarifaValue : $cbmTotalProductos * $tarifaValue;
            $objWriter->save($excelFilePath);
            return [
                //id_contenedor,id_tipo_cliente,nombre,documento,correo,whatsapp,volumen_final,monto_final,tarifa_final,estado=PENDIENTE
                'id' => $data['id'],
                'id_contenedor' => $idContenedor,
                'id_tipo_cliente' => $data['cliente']['id_tipo_cliente'],
                'nombre' => $data['cliente']['nombre'],
                'documento' => $data['cliente']['dni'],
                'correo' => $data['cliente']['correo'],
                'whatsapp' => $data['cliente']['telefono'],
                'volumen_final' => $data['cliente']['productos'][0]['cbm'],
                'monto_final' => $montoFinal,
                'tarifa_final' => $tarifaValue,
                'impuestos_final' => $impuestos,
                'logistica_final' => $logistica,
                'fob_final' => $fob,
                'estado' => 'PENDIENTE',
                "excel_file_name" => $excelFileName,
                "excel_file_path" => $excelFilePath
            ];
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            log_message('error', $e->getMessage());
            return $objPHPExcel;
            throw $e;
        }
    }
    public function getTipoByName($tipoCliente)
    {
        $tipoCliente = strtoupper($tipoCliente);

        switch ($tipoCliente) {
            case 'NUEVO':
                return 1;
            case 'ANTIGUO':
                return 2;
            case 'SOCIO':
                return 3;
            case "MANUAL":
                return 4; // Default to particular if not found
            default:
                return 1; // Default to particular if not found
        }
    }
    public function getFinalCotizacionExcel($objPHPExcel, $data)
    {
        try {
            //GOD IMPLEMENTATION

            /**Base Styles */
            $grayColor = 'F8F9F9';
            $blueColor = '1F618D';
            $yellowColor = 'FFFF33';
            $greenColor = "009999";
            $whiteColor = "FFFFFF";
            $borders = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            //REMOVE SHEET INDEX 1
            $objPHPExcel->removeSheetByIndex(1);
            /**Apply Tributes Calc Zones Rows Title */
            $newSheet = $objPHPExcel->createSheet();
            $newSheet->setTitle('2');
            /**Base Styles */
            $grayColor = 'F8F9F9';
            $blueColor = '1F618D';
            $yellowColor = 'FFFF33';
            $greenColor = "009999";
            $whiteColor = "FFFFFF";
            $borders = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            /**Apply Tributes Calc Zones Rows Title */
            $objPHPExcel->setActiveSheetIndex(1)->mergeCells('B1:Z1');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B1', 'Calculo de Tributos');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B1');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B1:Z1')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B4', 'N.Proveedor');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B5', 'N° Cajas');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B6', 'Peso(KG)');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B7', 'Medida');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B8', 'VOL. X PROVEEDOR');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B11', 'Nombres');
            $objPHPExcel->getActiveSheet()->getStyle('B11')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B11')->getFill()->getStartColor()->setARGB($blueColor);
            $objPHPExcel->getActiveSheet()->getStyle('B11')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            $objPHPExcel->getActiveSheet()->getStyle('B11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B12', 'Cantidad Cajones');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B13', 'Peso');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B14', "TOTAL CBM");

            // $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B7', "Valor CBM");
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B15', 'Valor Unitario');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B16', 'Valoracion');
            $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B16')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B17', 'Cantidad');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B18', 'Valor FOB');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B19', 'Distribucion %');
            // $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            // $objPHPExcel->getActiveSheet()->getStyle('B12')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B20', 'Flete');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B21', 'Valor CFR');
            $objPHPExcel->getActiveSheet()->getStyle('B18')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B18')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B22', 'Seguro');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B23', 'Valor CIF');
            $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('B19')->getFill()->getStartColor()->setARGB($yellowColor);
            $objPHPExcel->setActiveSheetIndex(1)->mergeCells('B28:Z28');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B28', 'Tributos Aplicables');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B28');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B28:Z28')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->setActiveSheetIndex(1)->mergeCells('B42:Z42');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B42', 'COSTOS DESTINOS');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B42');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B42:Z42')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B42')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(1)->mergeCells('B48:Z48');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B48', 'COSTO TOTAL DE IMPORTACIÓN');
            $style = $objPHPExcel->getActiveSheet()->getStyle('B48');
            $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($grayColor);
            $objPHPExcel->getActiveSheet()->getStyle('B48:Z48')->applyFromArray($borders);
            $objPHPExcel->getActiveSheet()->getStyle('B48')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B31', 'ANTIDUMPING');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B34', 'AD VALOREM');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B35', 'IGB 16%');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B36', 'IPM 2%');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B37', 'PERCEPCION');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B38', 'TOTAL');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B45', 'ITEM');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B50', 'ITEM');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B51', 'COSTO TOTAL');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B52', 'CANTIDAD');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B53', 'COSTO UNITARIO');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B54', 'COSTO SOLES');
            $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

            $InitialColumn = 'C';
            $LastColumn = 'C';
            $LastColumnTotal = 'C';
            $totalRows = 0;
            $cbmTotal = 0;
            $pesoTotal = 0;
            //first iterate for tributes zone, set values and apply styles to cells
            $index = 1;
            $tarifa = 0;
            $cajasTotales = 0;
            $pesoTotal = 0;
            $volumenTotal = 0;
            foreach ($data['cliente']['proveedores'] as $proveedor => $items) {
                //validate if $InitialColumn is more than Z then set A$
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '4', $proveedor);
                foreach ($items as $item) {
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '10', $item);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '11', $item["products"]);
                    $tarifa = $item['tarifa'];
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '12', $item["qty_box"]);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '13', $item["peso"]);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '14', $item['producto']['cbm']);
                    $volumenTotal += $item['producto']['cbm'];
                    $pesoTotal += $item["peso"];
                    $cajasTotales += $item["qty_box"];

                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '15', $item['producto']['precio_unitario']);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '17', $item['producto']['cantidad']);

                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '18', "=" . $LastColumn . "15*" . $LastColumn . "17");
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '31', "=" . $item['producto']['antidumping'] . "*" . $LastColumn . "17");
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '33', "=" . $item['producto']['ad_valorem']);
                    //set cell to percentage format
                    $objPHPExcel->getActiveSheet()->getStyle($LastColumn . '33')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '34', "=" . $item['producto']['ad_valorem'] . "*" . $LastColumn . "23");

                    $objPHPExcel->getActiveSheet()->mergeCells($InitialColumn . '14:' . $LastColumn . '14');
                    $objPHPExcel->getActiveSheet()->mergeCells($InitialColumn . '14:' . $LastColumn . '14');

                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($InitialColumn . '5', "=SUM(" . $InitialColumn . "12:" . $LastColumn . "12)");
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($InitialColumn . '6', "=SUM(" . $InitialColumn . "13:" . $LastColumn . "13)");
                    $LastColumn = $this->incrementColumn($LastColumn);
                    $index++;
                }
                $LastColumnRow = $this->incrementColumn($LastColumn, -1);
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '5', "=SUM(C12:" . $LastColumnRow . "12)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '6', "=SUM(C13:" . $LastColumnRow . "13)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '12', "=SUM(C12:" . $LastColumnRow . "12)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '13', "=SUM(C13:" . $LastColumnRow . "13)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '14', "=SUM(C14:" . $LastColumnRow . "14)");

                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '15', "=SUM(C15:" . $LastColumnRow . "15)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '17', "=SUM(C17:" . $LastColumnRow . "17)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '18', "=SUM(C18:" . $LastColumnRow . "18)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '20', "=" . $LastColumn . "14*0.6*" . $tarifa);
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '21', "=SUM(C21:" . $LastColumnRow . "21)");
                //if k last column is more than 5000 set 100 else set 50
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '22', "=IF(" . $LastColumn . "21>5000,100,50)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '31', "=SUM(C31:" . $LastColumnRow . "31)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '34', "=SUM(C34:" . $LastColumnRow . "34)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '35', "=SUM(C35:" . $LastColumnRow . "35)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '36', "=SUM(C36:" . $LastColumnRow . "36)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '37', "=SUM(C36:" . $LastColumnRow . "37)");
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '38', "=SUM(C38:" . $LastColumnRow . "38)");

                $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '45', "=" . $LastColumn . "14*0.4*" . $tarifa);

                $InitialColumn = $LastColumn;
                $LastColumnTotal = $LastColumn;
                $totalRows++;
            }
            $InitialColumn = 'C';
            //if count query is lower than 3 get sustract 3- count query and set the value to $substract and for each $substract remove border from row 36 to 39
            if ($index < 3) {
                $substract = 3 - $index;
                for ($i = 0; $i < $substract; $i++) {
                    $row = 36 + $i + $index;
                    //set not borders from b$row to l$row
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $row . ':L' . $row)->applyFromArray(array());
                }
                //remove borders from b36 to l39
            }
            $InitialColumn = 'C';
            $LastColumn = 'C';

            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('T39', 'TC');
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('T40', 3.7);
            $index = 1;
            $InitialColumn = 'C';
            for ($row = 36; $row <= 39; $row++) {
                for ($col = 1; $col <= 12; $col++) {
                    $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell, ''); // Establecer el valor de la celda como vacío
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($cell)->applyFromArray(array()); // Eliminar cualquier estilo aplicado a la celda

                }
            }
            foreach ($data['cliente']['proveedores'] as $proveedor => $items) {
                //validate if $InitialColumn is more than Z then set A$

                foreach ($items as $item) {
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '19', '=' . $LastColumn . '18/' . $LastColumnTotal . '18');
                    //set format percentage to $LastColumn.19
                    $objPHPExcel->getActiveSheet()->getStyle($LastColumn . '19')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '20', '=' . $LastColumnTotal . '20*' . $LastColumn . '19');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '21', '=' . $LastColumn . '18+' . $LastColumn . '20');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '22', '=' . $LastColumnTotal . '22*' . $LastColumn . '19');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '23', '=' . $LastColumn . '21+' . $LastColumn . '22');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '35', '=' . $LastColumn . '23*0.16');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '36', '=' . $LastColumn . '23*0.02');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '37', '=' . $LastColumn . '23*' . $item['producto']['percepcion']);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '38', '=SUM(' . $LastColumn . '34:' . $LastColumn . '37)');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '45', '=' . $LastColumnTotal . '45*' . $LastColumn . '19');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '50', $item["products"]);
                    //B51 = VALOR CFR +ANTIDUMPING + TOTAL TRIBUTOS +COSTO DESTINO
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '51', '=' . $LastColumn . '21+' . $LastColumn . '31+' . $LastColumn . '38+' . $LastColumn . '45');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '52', '=' . $LastColumn . '17');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '53', '=' . $LastColumn . '51/' . $LastColumn . '52');
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue($LastColumn . '54', '=' . $LastColumn . '53*T40');
                    $row = 36 + $index - 1;
                    if ($index >= 7) {
                        $sheet = $objPHPExcel->setActiveSheetIndex(0);
                        $sheet->insertNewRowBefore($row, 1);
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $row, $index);
                    //SET FONT BOLD FALSE
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $row)->getFont()->setBold(false);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $row, $item["products"]);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $row, "='2'!" . $InitialColumn . 17);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $row)->getFont()->setBold(false);
                    //center text
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $row, "='2'!" . $InitialColumn . 15);
                    //set currency format with dollar symbol
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $row, "='2'!" . $InitialColumn . 53);
                    //set currency format with dollar symbol
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $row, "='2'!" . $InitialColumn . 51);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('J' . $row)->getFont()->setBold(false);

                    //set currency format with dollar symbol
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    $JCellVal = $objPHPExcel->setActiveSheetIndex(0)->getCell('J' . $row)->getValue();
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $row, "='2'!" . $InitialColumn . 54);
                    //set currency format with pen symbol
                    //combine cells from C$ROW to e$row
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $row . ':E' . $row);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $row . ':H' . $row);
                    //SET CURRRENCY FORMAT WITH DOLLAR SYMBOL

                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K' . $row . ':L' . $row);
                    //copy currency format from k$row-1 to k$row
                    $style = $objPHPExcel->setActiveSheetIndex(0)->getStyle('K' . $row);
                    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $style->getFill()->getStartColor()->setARGB($greenColor);
                    //set letter color to white
                    $style->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
                    //center text
                    //set normal weight
                    $columnsToApply = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                    //apply borders from b$row to l$row
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $row . ':L' . $row)->applyFromArray($borders);
                    //for each column in columnsToApply apply style center and auto size
                    foreach ($columnsToApply as $column) {
                        //set font to calibri
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($column . $row)->getFont()->setName('Calibri');
                        //set font size to 11
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($column . $row)->getFont()->setSize(11);
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($column . $row)->getFont()->setBold(true);
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        if ($column == 'K') {
                            $objPHPExcel->setActiveSheetIndex(0)->getStyle($column . $row)->getNumberFormat()->setFormatCode('"S/." #,##0.00_-');
                        }
                    }
                    $InitialColumn = $this->incrementColumn($InitialColumn);
                    $lastRow = $row;

                    $LastColumn = $this->incrementColumn($LastColumn);
                    $index++;
                }
            }
            $notUsedDefaultRows = 3 - $index;
            if ($notUsedDefaultRows >= 0) {
                for ($i = 0; $i <= $notUsedDefaultRows; $i++) {
                    $row = 36 + $index + $i;
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $row . ':L' . $row)->applyFromArray(array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_NONE,
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    ));
                    //set k background color to white
                    $style = $objPHPExcel->setActiveSheetIndex(0)->getStyle('K' . $row);
                    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $style->getFill()->getStartColor()->setARGB($whiteColor);
                    //remove this row from the sheet

                }
            }


            //return $objPHPExcel;

            $ColumndIndex = PHPExcel_Cell::stringFromColumnIndex($index);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J20', "=MAX('2'!C33:" . $ColumndIndex . "33)");

            $columnaIndex = PHPExcel_Cell::stringFromColumnIndex($index + 1);
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('K14', "='2'!" . $ColumndIndex . "18");
            $formula = "='2'!" . $ColumndIndex . "20 + '2'!" . $ColumndIndex . "22";
            $objPHPExcel->getActiveSheet()->setCellValue('K15', $formula);
            $objPHPExcel->getActiveSheet()->setCellValue('K20', "='2'!" . $ColumndIndex . "34");
            $objPHPExcel->getActiveSheet()->setCellValue('K21', "='2'!" . $ColumndIndex . "35");
            $objPHPExcel->getActiveSheet()->setCellValue('K22', "='2'!" . $ColumndIndex . "36");
            $objPHPExcel->getActiveSheet()->setCellValue('K25', "='2'!" . $ColumndIndex . "37");
            $objPHPExcel->getActiveSheet()->setCellValue('K30', "='2'!" . $ColumndIndex . "18+" . "'2'!" . $ColumndIndex . "45");
            $CobroCellValue = $objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue();
            $ImpuestosCellValue = round($objPHPExcel->getActiveSheet()->getCell('K38')->getCalculatedValue(), 2);
            $objPHPExcel->getActiveSheet()->setCellValue('C9', "");
            $objPHPExcel->getActiveSheet()->setCellValue('C8', $item['nombre_cliente']);
            $objPHPExcel->getActiveSheet()->setCellValue('C10', $item['dni']);
            $objPHPExcel->getActiveSheet()->setCellValue('C11', $item['telefono']);
            $objPHPExcel->getActiveSheet()->setCellValue('F9', date('d/m/Y'));
            $objPHPExcel->getActiveSheet()->setCellValue('F11', $item['tipo_cliente']);
            $objPHPExcel->getActiveSheet()->setCellValue('J8', $cajasTotales);
            $objPHPExcel->getActiveSheet()->setCellValue('J9', $pesoTotal);
            //$objPHPExcel->getActiveSheet()->setCellValue('J11', $volumenTotal);

            $montoFinal = $objPHPExcel->setActiveSheetIndex(0)->getCell('K30')->getCalculatedValue();
            //if b23 =ANTIDUMPING THEN MONTOFINAL=K31
            if ($objPHPExcel->getActiveSheet()->getCell('B23')->getValue() == 'ANTIDUMPING') {
                $montoFinal = $objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue();
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $excelFileName = 'Cotizacion' . $item['nombre_cliente'] . '.xlsx';
            $excelFilePath = 'assets/downloads/' . $excelFileName;

            $objWriter->save($excelFilePath);
            return [
                //id_contenedor,id_tipo_cliente,nombre,documento,correo,whatsapp,volumen_final,monto_final,tarifa_final,estado=PENDIENTE
                'id_contenedor' => $item['id_contenedor'],
                'id_tipo_cliente' => $item['id_tipo_cliente'],
                'nombre' => $item['nombre_cliente'],
                'documento' => $item['dni'],
                'correo' => $item['correo'],
                'whatsapp' => $item['telefono'],
                'volumen_final' => $volumenTotal,
                'monto_final' => $montoFinal ?? 0,
                'tarifa_final' => $tarifa,
                'estado' => 'PENDIENTE',
                "excel_file_name" => $excelFileName,
                "excel_file_path" => $excelFilePath
            ];


            return $objPHPExcel;
        } catch (PHPExcel_Exception $e) {
            echo "Error en la fórmula de la celda " . $LastColumn . "18: " . $e->getMessage() . "\n";
            throw $e;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            return $objPHPExcel;
            throw $e;
        }
    }
    private function getCellValue($worksheet, $columnRanges, $startRow, $endRow, $defaultValue = null)
    {
        foreach ($columnRanges as $mergedRange) {
            preg_match('/^\w(\d+):\w(\d+)$/', $mergedRange, $matches);
            $startRowMatch = $matches[1];
            $endRowMatch = $matches[2];
            if ($startRowMatch == $startRow && $endRowMatch == $endRow) {
                $range = PHPExcel_Cell::extractAllCellReferencesInRange($mergedRange);
                $firstCell = $range[0];
                $value = $worksheet->getCell($firstCell)->getValue();
                return $value === null || $value === "-" ? $defaultValue : $value;
            }
        }
        return $defaultValue;
    }
    public function getMassiveExcelData($objPHPExcel)
    {
        $this->load->library('PHPExcel');

        $excel = $objPHPExcel;
        $worksheet = $excel->getActiveSheet();

        // Obtener el rango total de datos válidos
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Obtener todas las celdas combinadas
        $mergedCells = $worksheet->getMergeCells();

        // Función para obtener el valor real de una celda (considerando combinadas)
        $getCellValue = function ($col, $row) use ($worksheet, $mergedCells) {
            $cellAddress = $col . $row;
            $cellValue = trim($worksheet->getCell($cellAddress)->getValue());

            // Si la celda está vacía, buscar en celdas combinadas
            if (empty($cellValue)) {
                foreach ($mergedCells as $mergedRange) {
                    // Verificar si es un rango (contiene :)
                    if (strpos($mergedRange, ':') !== false) {
                        // Dividir el rango manualmente
                        list($startCell, $endCell) = explode(':', $mergedRange);

                        // Extraer coordenadas de inicio y fin
                        preg_match('/([A-Z]+)(\d+)/', $startCell, $startMatches);
                        preg_match('/([A-Z]+)(\d+)/', $endCell, $endMatches);

                        if (count($startMatches) >= 3 && count($endMatches) >= 3) {
                            $startCol = $startMatches[1];
                            $startRow = (int)$startMatches[2];
                            $endCol = $endMatches[1];
                            $endRow = (int)$endMatches[2];

                            // Verificar si la celda actual está dentro del rango
                            if ($col >= $startCol && $col <= $endCol && $row >= $startRow && $row <= $endRow) {
                                $cellValue = trim($worksheet->getCell($startCell)->getValue());
                                break;
                            }
                        }
                    }
                }
            }

            return $cellValue;
        };

        // Función para verificar si una fila pertenece a un cliente específico
        $getClientRowRange = function ($startRow) use ($worksheet, $getCellValue, $highestRow) {
            $endRow = $startRow;
            $clientName = $getCellValue('A', $startRow);

            // Buscar hasta dónde se extiende este cliente
            for ($row = $startRow + 1; $row <= $highestRow; $row++) {
                $nextClientName = $getCellValue('A', $row);
                if (!empty($nextClientName) && $nextClientName !== $clientName) {
                    break;
                }
                $endRow = $row;
            }

            return $endRow;
        };

        $clients = [];
        $processedRows = [];

        // Recorrer todas las filas buscando clientes
        for ($row = 1; $row <= $highestRow; $row++) {
            // Saltar filas ya procesadas
            if (in_array($row, $processedRows)) {
                continue;
            }

            $clientName = $getCellValue('A', $row);

            // Verificar si hay un nombre de cliente válido
            if (empty($clientName)) {
                continue;
            }

            // Determinar el rango de filas para este cliente
            $endRow = $getClientRowRange($row);

            // Marcar filas como procesadas
            for ($r = $row; $r <= $endRow; $r++) {
                $processedRows[] = $r;
            }

            // Obtener datos básicos del cliente
            $client = [
                'nombre' => $clientName,
                'tipo' => $getCellValue('B', $row),
                'dni' => $getCellValue('C', $row),
                'telefono' => $getCellValue('D', $row),
                'productos' => [],
            ];

            // Procesar productos dentro del rango del cliente
            for ($productRow = $row; $productRow <= $endRow; $productRow++) {
                $producto = $getCellValue('F', $productRow);

                if (empty($producto)) {
                    continue;
                }

                $cantidad = $getCellValue('N', $productRow);
                $precioUnitario = $getCellValue('O', $productRow);

                // Solo agregar productos con datos esenciales
                if (!empty($cantidad) && !empty($precioUnitario)) {
                    $productoData = [
                        'nombre' => $producto,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'antidumping' => $getCellValue('P', $productRow) ?: 0,
                        'valoracion' => $getCellValue('Q', $productRow) ?: 0,
                        'ad_valorem' => $getCellValue('R', $productRow) ?: 0,
                        'percepcion' => $getCellValue('S', $productRow) ?: 0.035,
                        'peso' => $getCellValue('T', $productRow) ?: 0,
                        'cbm' => $getCellValue('U', $productRow) ?: '',
                    ];

                    $client['productos'][] = $productoData;
                }
            }

            $clients[] = ['cliente' => $client];
        }

        return $clients;
    }
    public function getCotizacionFinalDocumentacionPagos($idContenedor)
    {
        $this->db->select(
            "*,CC.id AS id_cotizacion, " .
                "(
                SELECT IFNULL(SUM(cccp.monto), 0) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept= ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND (ccp.name = 'LOGISTICA' OR ccp.name = 'IMPUESTOS')
            ) AS total_pagos, " .
                "(
                SELECT COUNT(*) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept = ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND (ccp.name = 'LOGISTICA' OR ccp.name = 'IMPUESTOS')
            ) AS pagos_count"
        )
            ->from($this->table_contenedor_cotizacion . " AS CC")
            ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = CC.id_tipo_cliente', 'left')
            ->where('CC.id_contenedor', $idContenedor)
            ->where('CC.estado_cliente!=', null);

        $query = $this->db->get();
        return $query->result();
    }
    public function getContenedorCotizacionesFinales($idContenedor)
    {
        $this->db->select('*,contenedor_consolidado_cotizacion.id as id_cotizacion')
            ->from($this->table_contenedor_cotizacion)
            ->join($this->table_contenedor_tipo_cliente, 'contenedor_consolidado_cotizacion.id_tipo_cliente = contenedor_consolidado_tipo_cliente.id')
            ->where('id_contenedor', $idContenedor)
            ->where('estado_cliente IS NOT NULL')
            ->where('estado_cotizador', 'CONFIRMADO');
        //if $this-
        $query = $this->db->get();

        return $query->result();
    }
    public function updateEstadoCotizacionFinal($idCotizacionFinal, $estado)
    {
        try {
            $this->db->set('estado_cotizacion_final', $estado);
            $this->db->where('id', $idCotizacionFinal);
            $this->db->update($this->table_contenedor_cotizacion);
            if ($estado == 'COTIZADO') {
                //get phone from cotizacion table where id=idCotizacionFinal
                $this->db->select("CC.telefono,CC.id_contenedor,CC.impuestos_final,CC.volumen_final,CC.monto_final,CC.tarifa_final,nombre,logistica_final,
                (
                SELECT IFNULL(SUM(cccp.monto), 0) 
                FROM " . $this->table_contenedor_consolidado_cotizacion_coordinacion_pagos . " cccp
                JOIN " . $this->table_pagos_concept . " ccp ON cccp.id_concept= ccp.id
                WHERE cccp.id_cotizacion = CC.id
                AND (ccp.name = 'LOGISTICA'
                OR ccp.name = 'IMPUESTOS')
                ) AS total_pagos");
                $this->db->from($this->table_contenedor_cotizacion . " AS CC");
                $this->db->where('id', $idCotizacionFinal);

                $query = $this->db->get();
                $telefono = $query->row()->telefono;
                $telefono = preg_replace('/\s+/', '', $telefono);
                $this->phoneNumberId = $telefono ? $telefono . '@c.us' : '';
                $totalPagos = $query->row()->total_pagos;

                $volumen = $query->row()->volumen_final;
                $nombre = $query->row()->nombre;
                $logisticaFinal = $query->row()->logistica_final;
                $impuestosFinal = $query->row()->impuestos_final;
                $total = $logisticaFinal + $impuestosFinal;
                $totalAPagar = $total - $totalPagos;
                $idContenedor = $query->row()->id_contenedor;
                $this->db->select('fecha_arribo, carga');
                $this->db->from($this->table);
                $this->db->where('id', $idContenedor);
                $query = $this->db->get();
                $carga = $query->row()->carga;
                $fechaArribo = $query->row()->fecha_arribo;
                $message = "📦 *Consolidado #" . $carga . "*\n" .
                    "Hola " . $nombre . " 😁 un gusto saludarte! \n" .
                    "A continuación te envio la cotización final de tu importación📋📦.\n" .
                    "🙋‍♂️PAGO PENDIENTE: \n" .
                    "☑️Costo CBM: $" . number_format($logisticaFinal, 2) . "\n" .
                    "☑️Impuestos: $" . number_format($impuestosFinal, 2) . "\n" .
                    "☑️Total: $" . number_format($total, 2) . "\n" .
                    "Pronto le aviso nuevos avances, que tengan buen dia \n" .
                    "Último día de pago: " . date('d/m/Y', strtotime($fechaArribo)) . "\n";
                $this->sendMessage($message);
                $pathCotizacionFinalPDF = $this->getBoletaForSend($idCotizacionFinal);
                $this->sendMedia($pathCotizacionFinalPDF, null, null, null, 3);
                $message = "Resumen de Pago\n" .
                    "✅Cotización final: $" . number_format($total, 2) . "\n" .
                    "✅Adelanto: $" . number_format($totalPagos, 2) . "\n" .
                    "✅ Pendiente de pago: $" . number_format($totalAPagar, 2) . "\n";
                $this->sendMessage($message, null, 5);
                $pagosUrl = base_url('assets/downloads/pagos-full.jpg');
                $this->sendMedia($pagosUrl, 'image/jpg', null, null, 10);
            }
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en updateEstadoCotizacionFinal: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en updateEstadoCotizacionFinal: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteCotizacionFinalFile($idCotizacionFinal)
    {
        try {
            //get cotizacion_final_url from table where id=idCotizacion final and try to unlink and set null in database
            $this->db->select('cotizacion_final_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacionFinal);
            $query = $this->db->get();
            $cotizacionFinal = $query->row();
            if ($cotizacionFinal) {
                $cotizacionFinalUrl = $cotizacionFinal->cotizacion_final_url;
                if (file_exists($cotizacionFinalUrl)) {
                    unlink($cotizacionFinalUrl);
                }
            }
            $this->db->set('cotizacion_final_url', null);
            $this->db->where('id', $idCotizacionFinal);
            $this->db->update($this->table_contenedor_cotizacion);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en deleteCotizacionFinalFile: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en deleteCotizacionFinalFile: ' . $e->getMessage());
            return false;
        }
    }
    public function uploadCotizacionFinal($id, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/cargaconsolidada/cotizacionesFinales'
            );
            $dataToUpdate = $this->getCotizacionDataFinal($file);
            log_message('error', 'DataToUpdate: ' . json_encode($dataToUpdate));
            $dataToUpdate['cotizacion_final_url'] = $fileUrl;
                //change key telefono for whatsapp

            ;
            $dataToUpdate['volumen_final'] = $dataToUpdate['volumen'];
            unset($dataToUpdate['volumen']);
            $dataToUpdate['monto_final'] = $dataToUpdate['monto'];
            unset($dataToUpdate['monto']);
            $dataToUpdate['tarifa_final'] = $dataToUpdate['tarifa'];
            unset($dataToUpdate['tarifa']);
            unset($dataToUpdate['peso']);
            unset($dataToUpdate['fecha']);
            unset($dataToUpdate['valor_cot']);

            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion, $dataToUpdate);

            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en uploadCotizacionFinal: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en uploadCotizacionFinal: ' . $e->getMessage());
            return false;
        }
    }
    public function downloadBoleta($idCotizacionFinal)
    {
        //get cotizacion_final_url from table where id=idCotizacion final and get objPHPExcel and generate boleta and return it
        try {
            $this->db->select('cotizacion_final_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacionFinal);
            $query = $this->db->get();
            $cotizacionFinal = $query->row();
            if ($cotizacionFinal) {
                $cotizacionFinalUrl = $cotizacionFinal->cotizacion_final_url;
                $fileUrl = str_replace(' ', '%20', $cotizacionFinal->cotizacion_final_url);

                $fileContent = file_get_contents($fileUrl);
                if ($fileContent === false) {
                    throw new Exception("No se pudo leer el archivo Excel.");
                }
                $tempFile = tempnam(sys_get_temp_dir(), 'cotizacion_') . '.xlsx';
                file_put_contents($tempFile, $fileContent);

                // Cargar Excel

                $objPHPExcel = PHPExcel_IOFactory::load($tempFile);
                $this->generateBoleta($objPHPExcel);
            } else {
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Error en downloadBoleta: ' . $e->getMessage());
            return false;
        }
    }
    public function getBoletaForSend($idCotizacionFinal)
    {
        //get cotizacion_final_url from table where id=idCotizacion final and get objPHPExcel and generate boleta and return it
        try {
            $this->db->select('cotizacion_final_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacionFinal);
            $query = $this->db->get();
            $cotizacionFinal = $query->row();
            if ($cotizacionFinal) {
                $cotizacionFinalUrl = $cotizacionFinal->cotizacion_final_url;
                $fileUrl = str_replace(' ', '%20', $cotizacionFinal->cotizacion_final_url);

                $fileContent = file_get_contents($fileUrl);
                if ($fileContent === false) {
                    throw new Exception("No se pudo leer el archivo Excel.");
                }
                $tempFile = tempnam(sys_get_temp_dir(), 'cotizacion_') . '.xlsx';
                file_put_contents($tempFile, $fileContent);

                // Cargar Excel

                $objPHPExcel = PHPExcel_IOFactory::load($tempFile);
                return $this->generateBoletaForSend($objPHPExcel);
            } else {
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Error en downloadBoleta: ' . $e->getMessage());
            return false;
        }
    }
    private function generateBoleta($objPHPExcel)
    {
        try {
            $objPHPExcel->setActiveSheetIndex(0);
            $antidumping = $objPHPExcel->getActiveSheet()->getCell('B23')->getValue();
            $data = [
                "name" => $objPHPExcel->getActiveSheet()->getCell('C8')->getValue(),
                "lastname" => $objPHPExcel->getActiveSheet()->getCell('C9')->getValue(),
                "ID" => $objPHPExcel->getActiveSheet()->getCell('C10')->getValue(),
                "phone" => $objPHPExcel->getActiveSheet()->getCell('C11')->getValue(),
                "date" => date('d/m/Y'),
                "tipocliente" => $objPHPExcel->getActiveSheet()->getCell('F11')->getValue(),
                "peso" => $objPHPExcel->getActiveSheet()->getCell('J9')->getCalculatedValue(),
                "qtysuppliers" => $objPHPExcel->getActiveSheet()->getCell('J10')->getValue(),
                "cbm" => $objPHPExcel->getActiveSheet()->getCell('J11')->getCalculatedValue(),
                "valorcarga" => round($objPHPExcel->getActiveSheet()->getCell('K14')->getCalculatedValue(), 2),
                "fleteseguro" => round($objPHPExcel->getActiveSheet()->getCell('K15')->getCalculatedValue(), 2),
                "valorcif" => round($objPHPExcel->getActiveSheet()->getCell('K16')->getCalculatedValue(), 2),
                "advalorempercent" => intval($objPHPExcel->getActiveSheet()->getCell('J20')->getCalculatedValue() * 100),
                "advalorem" => round($objPHPExcel->getActiveSheet()->getCell('K20')->getCalculatedValue(), 2),
                "antidumping" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2) : "",

                "igv" => round($objPHPExcel->getActiveSheet()->getCell('K21')->getCalculatedValue(), 2),
                "ipm" => round($objPHPExcel->getActiveSheet()->getCell('K22')->getCalculatedValue(), 2),
                "subtotal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K24')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2),
                "percepcion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K25')->getCalculatedValue(), 2),
                "total" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K27')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2),
                "valorcargaproveedor" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K29')->getCalculatedValue(), 2),
                "servicioimportacion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2),
                "impuestos" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2),
                "montototal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K33')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2),
            ];
            $i = $antidumping == "ANTIDUMPING" ? 37 : 36;
            $items = [];
            while ($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue() != 'TOTAL') {
                //add item to items array
                $item = [
                    "index" => $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue(),
                    "name" => $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue(),
                    "qty" => $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue(),
                    "costounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "preciounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "total" => round($objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue(), 2),
                    "preciounitpen" => number_format(round($objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                ];
                $items[] = $item;
                $i++;
            }
            $itemsCount = count($items);
            $data["br"] = $itemsCount - 18 < 0 ? str_repeat("<br>", 18 - $itemsCount) : "";
            $data['items'] = $items;
            $logoContent = file_get_contents(base_url() . 'assets/downloads/logo.png');
            $logoData = base64_encode($logoContent);
            $data["logo"] = 'data:image/png;base64,' . $logoData;
            $htmlFilePath = 'assets/downloads/Boleta_Template.html';
            $htmlContent = file_get_contents($htmlFilePath);
            $pagosContent = file_get_contents(base_url() . 'assets/downloads/pagos.png');
            $pagosData = base64_encode($pagosContent);
            $data["pagos"] = 'data:image/png;base64,' . $pagosData;
            //replace {{name}} with data['name']
            foreach ($data as $key => $value) {
                //if value is a number parse to 2 decimals with comma as unit separator and dot as decimal separator
                if (is_numeric($value)) {
                    if ($value == 0) {
                        $value = '-';
                    }
                    if ($key != "ID" && $key != "phone" && $key != "qtysuppliers" && $key != "advalorempercent") {
                        $value = number_format($value, 2, '.', ',');
                    }
                }
                if ($key == "antidumping" && $antidumping == "ANTIDUMPING") {
                    $antidumpingHtml = '<tr style="background:#FFFF33">
                    <td style="border-top:none!important;border-bottom:none!important" colspan="3">ANTIDUMPING</td>
                    <td style="border-top:none!important;border-bottom:none!important" ></td>
                    <td style="border-top:none!important;border-bottom:none!important" >$' . number_format($data['antidumping'], 2, '.', ',') . '</td>
                    <td style="border-top:none!important;border-bottom:none!important" >USD</td>
                    </tr>';
                    $htmlContent = str_replace('{{antidumping}}', $antidumpingHtml, $htmlContent);
                    //search items with class ipm and set border none
                }
                if ($key == "items") {
                    $itemsHtml = "";
                    $total = 0;
                    $cantidad = 0;
                    foreach ($value as $item) {
                        $total += $item['total'];
                        $cantidad += $item['qty'];
                        $itemsHtml .= '<tr>
                        <td colspan="1">' . $item['index'] . '</td>
                        <td colspan="5">' . $item['name'] . '</td>
                        <td colspan="1">' . $item['qty'] . '</td>
                        <td colspan="2">$ ' . $item['costounit'] . '</td>
                        <td colspan="1">$ ' . $item['preciounit'] . '</td>
                        <td colspan="1">$ ' . number_format($item['total'], 2, '.', ',') . '</td>
                        <td colspan="1">S/. ' . $item['preciounitpen'] . '</td>
                    </tr>';
                    }
                    $itemsHtml .= '<tr>
                    <td colspan="6" >TOTAL</td>
                    <td >' . $cantidad . '</td>
                    <td colspan="2" style="border:none!important"></td>
                    <td style="border:none!important"></td>
                    <td >$ ' . number_format($total, 2, '.', ',') . '</td>
                    <td style="border:none!important"></td>

                </tr>';
                    $htmlContent = str_replace('{{' . $key . '}}', $itemsHtml, $htmlContent);
                } else {
                    $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                }
            }
            $options = new Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('Cotizacion.pdf', array("Attachment" => 0));
        } catch (PHPExcel_Exception $e) {
            echo "Error en la fórmula de la celda ";
            throw $e;
        } catch (Exception $e) {
            echo 'Excepción descargarBoleta: ',  $e->getMessage(), "\n";
            return $objPHPExcel;
            throw $e;
        }
    }
    private function generateBoletaForSend($objPHPExcel)
    {
        try {
            $objPHPExcel->setActiveSheetIndex(0);
            $antidumping = $objPHPExcel->getActiveSheet()->getCell('B23')->getValue();
            $data = [
                "name" => $objPHPExcel->getActiveSheet()->getCell('C8')->getValue(),
                "lastname" => $objPHPExcel->getActiveSheet()->getCell('C9')->getValue(),
                "ID" => $objPHPExcel->getActiveSheet()->getCell('C10')->getValue(),
                "phone" => $objPHPExcel->getActiveSheet()->getCell('C11')->getValue(),
                "date" => date('d/m/Y'),
                "tipocliente" => $objPHPExcel->getActiveSheet()->getCell('F11')->getValue(),
                "peso" => $objPHPExcel->getActiveSheet()->getCell('J9')->getCalculatedValue(),
                "qtysuppliers" => $objPHPExcel->getActiveSheet()->getCell('J10')->getValue(),
                "cbm" => $objPHPExcel->getActiveSheet()->getCell('J11')->getCalculatedValue(),
                "valorcarga" => round($objPHPExcel->getActiveSheet()->getCell('K14')->getCalculatedValue(), 2),
                "fleteseguro" => round($objPHPExcel->getActiveSheet()->getCell('K15')->getCalculatedValue(), 2),
                "valorcif" => round($objPHPExcel->getActiveSheet()->getCell('K16')->getCalculatedValue(), 2),
                "advalorempercent" => intval($objPHPExcel->getActiveSheet()->getCell('J20')->getCalculatedValue() * 100),
                "advalorem" => round($objPHPExcel->getActiveSheet()->getCell('K20')->getCalculatedValue(), 2),
                "antidumping" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2) : "",

                "igv" => round($objPHPExcel->getActiveSheet()->getCell('K21')->getCalculatedValue(), 2),
                "ipm" => round($objPHPExcel->getActiveSheet()->getCell('K22')->getCalculatedValue(), 2),
                "subtotal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K24')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2),
                "percepcion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K25')->getCalculatedValue(), 2),
                "total" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K27')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2),
                "valorcargaproveedor" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K29')->getCalculatedValue(), 2),
                "servicioimportacion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2),
                "impuestos" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2),
                "montototal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K33')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2),
            ];
            $i = $antidumping == "ANTIDUMPING" ? 37 : 36;
            $items = [];
            while ($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue() != 'TOTAL') {
                //add item to items array
                $item = [
                    "index" => $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue(),
                    "name" => $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue(),
                    "qty" => $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue(),
                    "costounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "preciounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "total" => round($objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue(), 2),
                    "preciounitpen" => number_format(round($objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                ];
                $items[] = $item;
                $i++;
            }
            $itemsCount = count($items);
            $data["br"] = $itemsCount - 18 < 0 ? str_repeat("<br>", 18 - $itemsCount) : "";
            $data['items'] = $items;
            $logoContent = file_get_contents(base_url() . 'assets/downloads/logo.png');
            $logoData = base64_encode($logoContent);
            $data["logo"] = 'data:image/png;base64,' . $logoData;
            $htmlFilePath = 'assets/downloads/Boleta_Template.html';
            $htmlContent = file_get_contents($htmlFilePath);
            $pagosContent = file_get_contents(base_url() . 'assets/downloads/pagos.png');
            $pagosData = base64_encode($pagosContent);
            $data["pagos"] = 'data:image/png;base64,' . $pagosData;
            //replace {{name}} with data['name']
            foreach ($data as $key => $value) {
                //if value is a number parse to 2 decimals with comma as unit separator and dot as decimal separator
                if (is_numeric($value)) {
                    if ($value == 0) {
                        $value = '-';
                    }
                    if ($key != "ID" && $key != "phone" && $key != "qtysuppliers" && $key != "advalorempercent") {
                        $value = number_format($value, 2, '.', ',');
                    }
                }
                if ($key == "antidumping" && $antidumping == "ANTIDUMPING") {
                    $antidumpingHtml = '<tr style="background:#FFFF33">
                    <td style="border-top:none!important;border-bottom:none!important" colspan="3">ANTIDUMPING</td>
                    <td style="border-top:none!important;border-bottom:none!important" ></td>
                    <td style="border-top:none!important;border-bottom:none!important" >$' . number_format($data['antidumping'], 2, '.', ',') . '</td>
                    <td style="border-top:none!important;border-bottom:none!important" >USD</td>
                    </tr>';
                    $htmlContent = str_replace('{{antidumping}}', $antidumpingHtml, $htmlContent);
                    //search items with class ipm and set border none
                }
                if ($key == "items") {
                    $itemsHtml = "";
                    $total = 0;
                    $cantidad = 0;
                    foreach ($value as $item) {
                        $total += $item['total'];
                        $cantidad += $item['qty'];
                        $itemsHtml .= '<tr>
                        <td colspan="1">' . $item['index'] . '</td>
                        <td colspan="5">' . $item['name'] . '</td>
                        <td colspan="1">' . $item['qty'] . '</td>
                        <td colspan="2">$ ' . $item['costounit'] . '</td>
                        <td colspan="1">$ ' . $item['preciounit'] . '</td>
                        <td colspan="1">$ ' . number_format($item['total'], 2, '.', ',') . '</td>
                        <td colspan="1">S/. ' . $item['preciounitpen'] . '</td>
                    </tr>';
                    }
                    $itemsHtml .= '<tr>
                    <td colspan="6" >TOTAL</td>
                    <td >' . $cantidad . '</td>
                    <td colspan="2" style="border:none!important"></td>
                    <td style="border:none!important"></td>
                    <td >$ ' . number_format($total, 2, '.', ',') . '</td>
                    <td style="border:none!important"></td>

                </tr>';
                    $htmlContent = str_replace('{{' . $key . '}}', $itemsHtml, $htmlContent);
                } else {
                    $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                }
            }
            $options = new Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $tempDir = sys_get_temp_dir() . '/pdfs/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Generar nombre único para el archivo
            $fileName = 'Cotizacion_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.pdf';
            $filePath = $tempDir . $fileName;

            // Guardar el PDF en el archivo
            $pdfContent = $dompdf->output();
            file_put_contents($filePath, $pdfContent);

            // Devolver la ruta del archivo guardado
            return $filePath;
        } catch (PHPExcel_Exception $e) {
            echo "Error en la fórmula de la celda ";
            throw $e;
        } catch (Exception $e) {
            echo 'Excepción descargarBoleta: ',  $e->getMessage(), "\n";
            return $objPHPExcel;
            throw $e;
        }
    }
    public function getContenedorFacturaGuia($idContenedor)
    {
        //from cotizacion table get aal with estado_cliente not null and join with tipo cliente
        $this->db->select('*,contenedor_consolidado_cotizacion.id as id_cotizacion')
            ->from($this->table_contenedor_cotizacion)
            ->join($this->table_contenedor_tipo_cliente, 'contenedor_consolidado_cotizacion.id_tipo_cliente = contenedor_consolidado_tipo_cliente.id')
            ->where('id_contenedor', $idContenedor)
            ->where('estado_cliente!=', null);
        $query = $this->db->get();
        return $query->result();
    }
    public function uploadFacturaGeneral($id, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/cargaconsolidada/facturasGenerales'
            );
            $dataToUpdate['factura_general_url'] = $fileUrl;
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion, $dataToUpdate);

            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en uploadFacturaGeneral: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en uploadFacturaGeneral: ' . $e->getMessage());
            return false;
        }
    }
    public function uploadGuiaRemision($id, $file)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFiles();
            $fileUrl = $this->uploadSingleFile(
                [
                    "name" => $file['name'],
                    "type" => $file['type'],
                    "tmp_name" => $file['tmp_name'],
                    "error" => $file['error'],
                    "size" => $file['size']
                ],
                'assets/cargaconsolidada/guiasRemision'
            );
            $dataToUpdate['guia_remision_url'] = $fileUrl;
            $this->db->where('id', $id);
            $this->db->update($this->table_contenedor_cotizacion, $dataToUpdate);

            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en uploadGuiaRemision: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en uploadGuiaRemision: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteFacturaGeneralFile($idCotizacion)
    {
        try {
            //get factura_general_url from table where id=idCotizacion and try to unlink and set null in database
            $this->db->select('factura_general_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacion);
            $query = $this->db->get();
            $cotizacion = $query->row();
            if ($cotizacion) {
                $facturaGeneralUrl = $cotizacion->factura_general_url;
                if (file_exists($facturaGeneralUrl)) {
                    unlink($facturaGeneralUrl);
                }
            }
            $this->db->set('factura_general_url', null);
            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en deleteFacturaGeneralFile: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en deleteFacturaGeneralFile: ' . $e->getMessage());
            return false;
        }
    }
    public function showClientesDocumentacionByDoc($idCotizacion)
    {
        //query from  contenedor_consolidado_cotizacion where id=idCotizacion use array agg to get documentacion peru key with fields , fields excel confirmacion f.comercial vol doc and valor doc from table contenedor_consolidado_cotizacion
        // and row from contenedor_consolidado_cotizacion_documentacion where id_cotizacion=idCotizacion
        //use alias
        try {
            $this->db->select([
                "JSON_ARRAYAGG(
                JSON_OBJECT(
                    'code_supplier', contenedor_consolidado_cotizacion_proveedores.code_supplier,
                    'id_cotizacion', contenedor_consolidado_cotizacion.id,
                    'documentacion_peru', JSON_OBJECT(
                        'factura_comercial', contenedor_consolidado_cotizacion.factura_comercial,
                        'excel_confirmacion', contenedor_consolidado_cotizacion.excel_confirmacion,
                        'volumen_doc', ifnull(contenedor_consolidado_cotizacion.volumen_doc,0),
                        'valor_doc', contenedor_consolidado_cotizacion.valor_doc
                    ),
                    'documentos_adicionales', (
                        SELECT JSON_ARRAYAGG(JSON_OBJECT(
                            'name', name,
                            'file_url', file_url
                        ))
                        FROM contenedor_consolidado_cotizacion_documentacion
                        WHERE id_cotizacion = contenedor_consolidado_cotizacion.id
                    ),
                    'documentacion_china', (
                        SELECT JSON_ARRAYAGG(JSON_OBJECT(
                            'name', file_name,
                            'file_url', file_path
                        ))
                        FROM contenedor_consolidado_almacen_documentacion
                        WHERE id_cotizacion = contenedor_consolidado_cotizacion.id
                        and id_proveedor=contenedor_consolidado_cotizacion_proveedores.id
                        AND code_supplier = contenedor_consolidado_cotizacion_proveedores.code_supplier
                    ),
                    'inspeccion', (
                        SELECT JSON_ARRAYAGG(JSON_OBJECT(
                            'name', file_name,
                            'file_url', file_path
                        ))
                        FROM contenedor_consolidado_almacen_inspection
                        WHERE id_cotizacion = contenedor_consolidado_cotizacion.id
                        and id_proveedor=contenedor_consolidado_cotizacion_proveedores.id

                        AND code_supplier = contenedor_consolidado_cotizacion_proveedores.code_supplier
                    )
                )
            ) as proveedores_documentacion"
            ])
                ->from($this->table_contenedor_cotizacion)
                ->join($this->table_contenedor_cotizacion_proveedores, 'contenedor_consolidado_cotizacion.id = contenedor_consolidado_cotizacion_proveedores.id_cotizacion')
                ->where('contenedor_consolidado_cotizacion.id', $idCotizacion)
                ->group_by('contenedor_consolidado_cotizacion.id');
            $query = $this->db->get();
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en showClientesDocumentacionByDoc: ' . $this->db->error()['message']);
                return false;
            }
            return $query->row();
        } catch (Exception $e) {
            log_message('error', 'Error en showClientesDocumentacionByDoc: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteGuiaRemisionFile($idCotizacion)
    {
        try {
            //get guia_remision_url from table where id=idCotizacion and try to unlink and set null in database
            $this->db->select('guia_remision_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacion);
            $query = $this->db->get();
            $cotizacion = $query->row();
            if ($cotizacion) {
                $guiaRemisionUrl = $cotizacion->guia_remision_url;
                if (file_exists($guiaRemisionUrl)) {
                    unlink($guiaRemisionUrl);
                }
            }
            $this->db->set('guia_remision_url', null);
            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_contenedor_cotizacion);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en deleteGuiaRemisionFile: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en deleteGuiaRemisionFile: ' . $e->getMessage());
            return false;
        }
    }
    public function viewFormularioAduana($idContenedor)
    {
        //get all data from carga_consolidada_contenedor
        $this->db->select('*,
        ifnull((select json_arrayagg(json_object(
            "id", id,
            "file_name", file_name,
            "file_url", file_path,
            "file_ext", file_type,
            "file_size", file_size,
            "tipo", tipo
        )) as files from carga_consolidada_aduana_files where id_contenedor =' . $idContenedor . '),"[]") as files')
            ->from($this->table)
            ->where('id', $idContenedor);
        $query = $this->db->get();
        return $query->result();
    }
    public function updateFormularioAduana($idContenedor, $data, $files)
    {
        try {
            //remove idContainer from data
            unset($data['idContainer']);
            $this->setAllowedExtensionsImagesOfficeFiles();
            $this->maxFileSize = 1000000;
            $this->db->where('id', $idContenedor);
            $this->db->update($this->table, $data);
            //foreach file 
            foreach ($files['files']['tmp_name'] as $key => $tmp_name) {
                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['files']['name'][$key],
                        "type" => $files['files']['type'][$key],
                        "tmp_name" => $files['files']['tmp_name'][$key],
                        "error" => $files['files']['error'][$key],
                        "size" => $files['files']['size'][$key]
                    ],
                    'assets/cargaconsolidada/formularioAduana'
                );
                $data = [
                    'id_contenedor' => $idContenedor,
                    'file_name' => $files['files']['name'][$key],
                    'file_path' => $fileUrl,
                    'file_type' => $files['files']['type'][$key],
                    'file_size' => $files['files']['size'][$key],
                ];
                $this->db->insert($this->table_contenedor_aduana_files, $data);
            }
            // Guardar archivos de Impuestos
            if (isset($files['impuestos_pagados'])) {
                foreach ($files['impuestos_pagados']['tmp_name'] as $key => $tmp_name) {
                    $fileUrl = $this->uploadSingleFile(
                        [
                            "name" => $files['impuestos_pagados']['name'][$key],
                            "type" => $files['impuestos_pagados']['type'][$key],
                            "tmp_name" => $files['impuestos_pagados']['tmp_name'][$key],
                            "error" => $files['impuestos_pagados']['error'][$key],
                            "size" => $files['impuestos_pagados']['size'][$key]
                        ],
                        'assets/cargaconsolidada/impuestosPagados'
                    );
                    $dataImpuestos = [
                        'id_contenedor' => $idContenedor,
                        'file_name' => $files['impuestos_pagados']['name'][$key],
                        'file_path' => $fileUrl,
                        'file_type' => $files['impuestos_pagados']['type'][$key],
                        'file_size' => $files['impuestos_pagados']['size'][$key],
                        'tipo' => 'impuestos'
                    ];
                    $this->db->insert($this->table_contenedor_aduana_files, $dataImpuestos);
                }
            }
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en updateFormularioAduana: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en updateFormularioAduana: ' . $e->getMessage());
            return false;
        }
    }
    public function saveInspectionSingle($idFile, $idProveedor)
    {
        try {
            // 1. Verificar si hay algún archivo SENDED para este proveedor (1 consulta)
            $hasSendedFiles = $this->db->select('id')
                ->from($this->table_contenedor_almacen_inspection)
                ->where('id_proveedor', $idProveedor)
                ->where('send_status', 'SENDED')
                ->limit(1)
                ->get()
                ->num_rows() > 0;

            // 2. Obtener datos del archivo específico (1 consulta)
            $fileData = $this->db->select('file_path, file_type, send_status')
                ->from($this->table_contenedor_almacen_inspection)
                ->where('id', $idFile)
                ->where('id_proveedor', $idProveedor)
                ->where_in('file_type', ['image/jpeg', 'image/png', 'image/jpg', 'video/mp4'])
                ->get()
                ->row();

            if (!$fileData) {
                throw new Exception("Archivo de inspección no encontrado");
            }

            // 3. Si el archivo ya está SENDED, no hacer nada
            if ($fileData->send_status == 'SENDED') {
                return ['status' => false, 'message' => 'El archivo ya fue enviado'];
            }

            // 4. Si NO hay archivos SENDED para este proveedor, obtener datos y enviar mensaje (1-2 consultas)

            // Obtener datos del proveedor y cliente (1 consulta)
            $proveedorInfo = $this->db->select('p.code_supplier, p.qty_box_china, p.qty_box, c.nombre as cliente, c.telefono')
                ->from($this->table_contenedor_cotizacion_proveedores . ' p')
                ->join($this->table_contenedor_cotizacion . ' c', 'c.id = p.id_cotizacion')
                ->where('p.id', $idProveedor)
                ->get()
                ->row();

            if (!$proveedorInfo) {
                throw new Exception("Datos del proveedor no encontrados");
            }

            // Actualizar estado (1 consulta)
            $this->db->where('id', $idProveedor)
                ->update($this->table_contenedor_cotizacion_proveedores, [
                    'estados_proveedor' => 'INSPECTION',
                    'estados' => 'INSPECCIONADO',
                ]);

            // Preparar y enviar mensaje
            $telefono = preg_replace('/\s+/', '', $proveedorInfo->telefono) . ($proveedorInfo->telefono ? '@c.us' : '');
            $this->phoneNumberId = $telefono;

            $qty = $proveedorInfo->qty_box_china ?? $proveedorInfo->qty_box;
            if (!$hasSendedFiles) {
                $message = $proveedorInfo->cliente . '----' . $proveedorInfo->code_supplier . '----' . $qty . ' boxes. ' . "\n\n" .
                    '📦 Tu carga llego a nuestro almacén de Yiwu, te comparto las fotos y videos. ' . "\n\n";

                $this->sendMessage('Hola buen día 🙋🏻‍♀' . "\n\n" . 'Inspección: ' . "\n" . $message);
            }
            log_message('error', 'FileData: ' . json_encode($fileData));
            // 5. Enviar el archivo (si no está SENDED)
            $this->sendMediaInspection($fileData->file_path, $fileData->file_type, null, null, 1, $idFile);

            return ['status' => true, 'message' => 'Proceso completado'];
        } catch (Exception $e) {
            log_message('error', 'Error en saveInspectionSingle: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    public function saveInspection($idProveedor, $idCotizacion, $files)
    {
        try {
            $this->setAllowedExtensionsImagesOfficeFilesVideos();
            $this->maxFileSize = 1000000;
            $index = 0;
            foreach ($files['files']['tmp_name'] as $key => $tmp_name) {
                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['files']['name'][$key],
                        "type" => $files['files']['type'][$key],
                        "tmp_name" => $files['files']['tmp_name'][$key],
                        "error" => $files['files']['error'][$key],
                        "size" => $files['files']['size'][$key]
                    ],
                    'assets/images/'
                );
                log_message('error', 'FileUrl: ' . $fileUrl);
                log_message('error', 'FileName: ' . $files['files']['name'][$key]);
                $fileName = mb_convert_encoding($files['files']['name'][$key], 'UTF-8', 'auto');
                $data = [
                    'id_cotizacion' => $idCotizacion,
                    'id_proveedor' => $idProveedor,
                    'file_name' => $fileName,
                    'file_path' => $fileUrl,
                    'file_type' => $files['files']['type'][$key],
                    'file_size' => $files['files']['size'][$key],
                    'last_modified' => date('Y-m-d H:i:s'),
                ];
                log_message('error', 'Data: ' . json_encode($data));

                $this->db->insert($this->table_contenedor_almacen_inspection, $data);
                if ($this->db->error()['code'] != 0) {
                    log_message('error', 'Error en saveInspection: ' . $this->db->error()['message']);
                    return false;
                }
                $index++;
            }
            $this->validateToSendInspectionMessage($idProveedor);
            return "success";
        } catch (Exception $e) {
            log_message('error', 'Error en saveInspection: ' . $e->getMessage());
            return false;
        }
    }
    public function saveDocumentation($idProveedor, $idCotizacion, $files)
    {
        try {
            $this->setAllowedExtensionsImagesOfficeFiles();
            $this->maxFileSize = 1000000;
            $index = 0;
            foreach ($files['files']['tmp_name'] as $key => $tmp_name) {
                $fileUrl = $this->uploadSingleFile(
                    [
                        "name" => $files['files']['name'][$key],
                        "type" => $files['files']['type'][$key],
                        "tmp_name" => $files['files']['tmp_name'][$key],
                        "error" => $files['files']['error'][$key],
                        "size" => $files['files']['size'][$key]
                    ],
                    'assets/cargaconsolidada/documentacion'
                );
                $data = [
                    'id_cotizacion' => $idCotizacion,
                    'id_proveedor' => $idProveedor,
                    'file_name' => $files['files']['name'][$key],
                    'file_path' => $fileUrl,
                    'file_ext' => $files['files']['type'][$key],
                ];
                $this->db->insert($this->table_contenedor_almacen_documentacion, $data);
                if ($this->db->error()['code'] != 0) {
                    log_message('error', 'Error en saveDocumentation: ' . $this->db->error()['message']);
                    return false;
                }
                $index++;
            }
            // $this->validateToSendDocumentationMessage($idProveedor);
            return "success";
        } catch (Exception $e) {
            log_message('error', 'Error en saveDocumentation: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteFileInspection($idFile)
    {
        try {
            //get file_path from table where id=idFile and try to unlink and delete row
            $this->db->select('file_path');
            $this->db->from($this->table_contenedor_almacen_inspection);
            $this->db->where('id', $idFile);
            $query = $this->db->get();
            $file = $query->row();
            if ($file) {
                $filePath = $file->file_path;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $this->db->where('id', $idFile);
            $this->db->delete($this->table_contenedor_almacen_inspection);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en deleteFileInspection: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en deleteFileInspection: ' . $e->getMessage());
            return false;
        }
    }
    public function getObservaciones($idContenedor)
    {
        try {
            $this->db->select('observaciones,
            (select json_arrayagg(json_object(
                "id", id,
                "file_name", file_name,
                "file_url", file_path,
                "file_ext", file_type,
                "file_size", file_size
            )) as files from carga_consolidada_aduana_files where id_contenedor =' . $idContenedor . ') as files
            ');
            $this->db->from($this->table);
            $this->db->where('id', $idContenedor);
            $query = $this->db->get();
            //return all data from table
            return ['status' => true, 'data' => $query->row()];
        } catch (Exception $e) {
            log_message('error', '' . $e->getMessage());
        }
    }
    public function updateRotulado($idCotizacion, $idProveedor)
    {
        try {

            //update send_rotulado_status to PENDING
            $this->db->set('send_rotulado_status', 'PENDING');
            $this->db->where('id', $idProveedor);
            $this->db->update($this->table_contenedor_cotizacion_proveedores);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en updateRotulado: ' . $this->db->error()['message']);
                return false;
            } else {
                return "success";
            }
        } catch (Exception $e) {
            log_message('error', 'Error en updateRotulado: ' . $e->getMessage());
            return false;
        }
    }
    public function getPagoCoordinationById($idPago)
    {
        $this->db->where('id', $idPago);
        $query = $this->db->get('contenedor_consolidado_cotizacion_coordinacion_pagos');
        return $query->row();
    }
    public function saveClientePagosCoordination($voucher, $idCotizacion, $idContenedor, $amount, $fecha, $banco)
    {
        try {

            // Permitir imágenes y archivos de oficina
            $this->setAllowedExtensionsImagesOfficeFiles();

            $voucherUrl = $this->uploadSingleFile(
                [
                    "name" => $voucher['name'],
                    "type" => $voucher['type'],
                    "tmp_name" => $voucher['tmp_name'],
                    "error" => $voucher['error'],
                    "size" => $voucher['size']
                ],
                'assets/cargaconsolidada/pagos'
            );
            //check if  cotizacion has cotizacion_final_url
            $this->db->select('cotizacion_final_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacion);
            $query = $this->db->get();
            $cotizacion = $query->row();
            $cotizacionFinalUrl = null;
            if ($cotizacion) {
                log_message('error', 'Cotizacion Final URL: ' . $cotizacion->cotizacion_final_url);
                $cotizacionFinalUrl = $cotizacion->cotizacion_final_url;
            }
            $data = [
                'voucher_url' => $voucherUrl,
                'id_cotizacion' => $idCotizacion,
                'id_contenedor' => $idContenedor,
                'id_concept' => !$cotizacionFinalUrl ? $this->CONCEPT_PAGO_LOGISTICA : $this->CONCEPT_PAGO_IMPUESTO,
                'monto' => $amount,
                'payment_date' => date('Y-m-d', strtotime($fecha)),
                'banco' => $banco
            ];
            $this->db->insert($this->table_contenedor_consolidado_cotizacion_coordinacion_pagos, $data);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en saveClientePagosCoordination: ' . $this->db->error()['message']);
                return [
                    'status' => "error",
                    'message' => 'Error al guardar el pago: ' . $this->db->error()['message']
                ];
            } else {
                return [
                    'status' => "success",
                    'message' => 'Pago guardado exitosamente',
                    'data' => $data
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Error en saveClientePagosCoordination: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al guardar el pago: ' . $e->getMessage()
            ];
        }
    }
    public function deletePagoCoordination($idPago)
    {
        $this->db->where('id', $idPago);
        return $this->db->delete('contenedor_consolidado_cotizacion_coordinacion_pagos');
    }
    public function getPagosCoordination($idCotizacion)
    {
        try {
            //check if cotizacion has cotizacion_final_url
            $this->db->select('cotizacion_final_url');
            $this->db->from($this->table_contenedor_cotizacion);
            $this->db->where('id', $idCotizacion);
            $query = $this->db->get();
            $cotizacion = $query->row();
            $cotizacionFinalUrl = null;
            if ($cotizacion) {
                log_message('error', 'Cotizacion Final URL: ' . $cotizacion->cotizacion_final_url);
                $cotizacionFinalUrl = $cotizacion->cotizacion_final_url;
            }
            $this->db->select('contenedor_consolidado_cotizacion_coordinacion_pagos.*')
                ->from($this->table_contenedor_consolidado_cotizacion_coordinacion_pagos)
                ->join($this->table_pagos_concept, 'contenedor_consolidado_cotizacion_coordinacion_pagos.id_concept = cotizacion_coordinacion_pagos_concept.id')
                ->where('id_cotizacion', $idCotizacion);

            // Agrupar las condiciones OR para id_concept
            $this->db->group_start();
            $this->db->where('id_concept', $this->CONCEPT_PAGO_LOGISTICA);
            if ($cotizacionFinalUrl) {
                $this->db->or_where('id_concept', $this->CONCEPT_PAGO_IMPUESTO);
            }
            $this->db->group_end();

            $this->db->order_by('payment_date', 'DESC');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Error en getPagosCoordination: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los pagos: ' . $e->getMessage()
            ];
        }
    }
    public function getConsolidadoCrons()
    {
        //get all data from table contenedor_consolidado_cron
        $this->db->select('cron.*,contenedor.carga,contenedor_consolidado_cotizacion.nombre');
        $this->db->from($this->table_consolidado_cron . ' as cron');
        $this->db->join($this->table . ' as contenedor', 'cron.id_contenedor = contenedor.id');
        $this->db->join($this->table_contenedor_cotizacion, 'cron.id_cotizacion = contenedor_consolidado_cotizacion.id');
        $query = $this->db->get();
        return $query->result();
    }
    public function getCron($idCron)
    {
        //get all data from table contenedor_consolidado_cron where id=$idCron
        $this->db->select('cron.*');
        $this->db->from($this->table_consolidado_cron . ' as cron');
        $this->db->where('cron.id', $idCron);
        $query = $this->db->get();
        return $query->row();
    }
    public function updateCron($idCron, $data, $timebetween, $createdBy)
    {
        try {
            $dataToInsert = [
                'data_json' => json_encode($data),
                'time_between' => $timebetween,
                'execution_at' =>
                //execution at is $createdBy add $timebetween in minutes
                date('Y-m-d H:i:s', strtotime('+' . intval($timebetween) . ' minutes', strtotime($createdBy)))
            ];
            $this->db->where('id', $idCron);
            $this->db->update($this->table_consolidado_cron, $dataToInsert);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en updateCron: ' . $this->db->error()['message']);
                return false;
            } else {
                return [
                    "status" => "success",
                    "message" => "Cron actualizado exitosamente",
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Error en updateCron: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteCron($idCron)
    {
        try {
            $this->db->where('id', $idCron);
            $this->db->delete($this->table_consolidado_cron);
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Error en deleteCron: ' . $this->db->error()['message']);
                return false;
            } else {
                return [
                    "status" => "success",
                    "message" => "Cron eliminado exitosamente",
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Error en deleteCron: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Compares a full name with a partial name to check for matches.
     *
     * @param string $fullName The full name to compare against.
     * @param string $partialName The partial name to check for matches.
     * @return bool True if there is a match, false otherwise.
     */
    private function isNameMatch($fullName, $partialName)
    {
        // Verificación inicial
        if (empty($fullName) || empty($partialName)) {
            return false;
        }

        $fullName = $this->normalizeString($fullName);
        $partialName = $this->normalizeString($partialName);

        // Verificar que normalizeString no devolvió cadenas vacías
        if (empty($fullName) || empty($partialName)) {
            return false;
        }

        // Comparación exacta primero
        if ($fullName === $partialName) {
            return true;
        }

        // Verificar si el nombre parcial está contenido en el completo
        // Verificar que $partialName no esté vacío antes de usar strpos
        if (!empty($partialName) && strpos($fullName, $partialName) !== false) {
            return true;
        }

        // Comparar palabra por palabra
        $fullWords = array_filter(explode(' ', $fullName)); // array_filter elimina elementos vacíos
        $partialWords = array_filter(explode(' ', $partialName)); // array_filter elimina elementos vacíos

        // Verificar que tenemos palabras para comparar
        if (empty($fullWords) || empty($partialWords)) {
            return false;
        }

        $matchCount = 0;

        foreach ($partialWords as $partialWord) {
            // Verificar que la palabra parcial no esté vacía
            if (empty($partialWord)) {
                continue;
            }

            foreach ($fullWords as $fullWord) {
                // Verificar que la palabra completa no esté vacía
                if (empty($fullWord)) {
                    continue;
                }

                if (strpos($fullWord, $partialWord) !== false) {
                    $matchCount++;
                    break;
                }
            }
        }

        // Si coinciden al menos 70% de las palabras del nombre parcial
        return $matchCount >= ceil(count($partialWords) * 0.7);
    }


    private function normalizeString($string)
    {
        $string = strtolower(trim($string));
        $accents = [
            'á' => 'a',
            'à' => 'a',
            'ä' => 'a',
            'â' => 'a',
            'ā' => 'a',
            'ã' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ë' => 'e',
            'ê' => 'e',
            'ē' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'ï' => 'i',
            'î' => 'i',
            'ī' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'ö' => 'o',
            'ô' => 'o',
            'ō' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'ü' => 'u',
            'û' => 'u',
            'ū' => 'u',
            'ñ' => 'n',
            'ç' => 'c',
            'Á' => 'a',
            'À' => 'a',
            'Ä' => 'a',
            'Â' => 'a',
            'Ā' => 'a',
            'Ã' => 'a',
            'É' => 'e',
            'È' => 'e',
            'Ë' => 'e',
            'Ê' => 'e',
            'Ē' => 'e',
            'Í' => 'i',
            'Ì' => 'i',
            'Ï' => 'i',
            'Î' => 'i',
            'Ī' => 'i',
            'Ó' => 'o',
            'Ò' => 'o',
            'Ö' => 'o',
            'Ô' => 'o',
            'Ō' => 'o',
            'Õ' => 'o',
            'Ú' => 'u',
            'Ù' => 'u',
            'Ü' => 'u',
            'Û' => 'u',
            'Ū' => 'u',
            'Ñ' => 'n',
            'Ç' => 'c'
        ];

        return strtr($string, $accents);
    }
    public function importarProductosDesdeExcel($objPHPExcel, $idContenedor, $tmpUrl = null)
    {
        try {
            $this->load->library('PHPExcel');

            // Extraer el archivo Excel como ZIP para acceder a las imágenes
            if ($tmpUrl) {
                log_message("error", "Iniciando extracción del archivo: " . $tmpUrl);
                $newFolder = 'assets/uploads/';
                if (!file_exists($newFolder)) {
                    mkdir($newFolder, 0777, true);
                    log_message("error", "Directorio creado: " . $newFolder);
                }

                // Renombrar el archivo temporal a .zip
                $zipPath = $newFolder . 'temp_' . uniqid() . '.zip';
                log_message("error", "Intentando renombrar a: " . $zipPath);
                if (rename($tmpUrl, $zipPath)) {
                    $zip = new ZipArchive;
                    $res = $zip->open($zipPath);
                    if ($res === TRUE) {
                        log_message("error", "Archivo ZIP abierto correctamente");
                        $zip->extractTo($newFolder);
                        $zip->close();
                        // Eliminar el archivo ZIP temporal
                        unlink($zipPath);
                        log_message("error", "Archivo Excel extraído exitosamente");

                        // Verificar si se extrajeron las imágenes
                        $mediaPath = $newFolder . 'xl/media/';
                        if (is_dir($mediaPath)) {
                            $files = scandir($mediaPath);
                            log_message("error", "Archivos encontrados en xl/media: " . (count($files) - 2)); // -2 para . y ..
                            foreach ($files as $file) {
                                if ($file != '.' && $file != '..') {
                                    log_message("error", "Imagen encontrada: " . $file);
                                }
                            }
                        } else {
                            log_message("error", "Directorio xl/media no encontrado");
                        }
                    } else {
                        log_message("error", "No se pudo abrir el archivo ZIP. Error: " . $res);
                    }
                } else {
                    log_message("error", "No se pudo renombrar el archivo temporal");
                }
            } else {
                log_message("error", "No se proporcionó tmpUrl para extracción");
            }

            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            // Obtener celdas combinadas (merge)
            $mergedCells = $sheet->getMergeCells();

            // Función mejorada para saber si una celda está mergeada
            $getMergeRange = function ($row) use ($mergedCells) {
                foreach ($mergedCells as $range) {
                    list($start, $end) = explode(':', $range);
                    $startRow = (int)preg_replace('/[A-Z]/', '', $start);
                    $endRow = (int)preg_replace('/[A-Z]/', '', $end);
                    if ($row >= $startRow && $row <= $endRow) {
                        return [$startRow, $endRow];
                    }
                }
                return [$row, $row];
            };

            $row = 3;
            $processedItems = []; // Para evitar duplicados

            // Obtener la colección de dibujos (imágenes) del Excel
            $drawings = $sheet->getDrawingCollection();

            while ($row <= $highestRow) {
                // Detectar rango mergeado en la columna 1 (A)
                list($startRow, $endRow) = $getMergeRange($row);
                log_message('error', 'Start Row: ' . $startRow . ' End Row: ' . $endRow);

                // Verificar si hay contenido en la celda A
                $cellValue = $sheet->getCell("A$startRow")->getValue();
                if ($cellValue == null || $cellValue == "" || $cellValue == "-") {
                    break;
                }

                $item = trim($cellValue);
                log_message('error', 'Processing Item: ' . $item . ' en rango ' . $startRow . '-' . $endRow);

                // Evitar procesar el mismo item múltiples veces
                if (in_array($item, $processedItems)) {
                    log_message('error', 'Item ya procesado, saltando: ' . $item);
                    $row = $endRow + 1;
                    continue;
                }

                $processedItems[] = $item;

                // Procesar solo una vez por rango mergeado
                $nombre_comercial = trim($sheet->getCell("B$startRow")->getValue());

                // Obtener imagen - buscar en todo el rango mergeado
                $foto = '';
                log_message("error", "Buscando imagen en rango C$startRow a C$endRow. Total de dibujos: " . count($drawings));

                $imageFound = false;
                for ($searchRow = $startRow; $searchRow <= $endRow && !$imageFound; $searchRow++) {
                    log_message("error", "Buscando imagen en celda C$searchRow");
                    foreach ($drawings as $drawing) {
                        $coordinates = $drawing->getCoordinates();
                        log_message("error", "Dibujo encontrado en coordenadas: " . $coordinates);

                        if ($coordinates == "C$searchRow") {
                            $drawingPath = $drawing->getPath();
                            log_message("error", "Ruta del dibujo: " . $drawingPath);

                            $hashPosition = strpos($drawingPath, '#');
                            if ($hashPosition !== false) {
                                $extractedPart = substr($drawingPath, $hashPosition + 1);
                                log_message("error", "Parte extraída: " . $extractedPart);

                                // Definir posibles ubicaciones de la imagen
                                $possiblePaths = [
                                    'assets/uploads/xl/media/' . $extractedPart,
                                    'assets/uploads/' . $extractedPart,
                                    'assets/uploads/media/' . $extractedPart
                                ];

                                foreach ($possiblePaths as $imagePath) {
                                    log_message("error", "Buscando imagen en: " . $imagePath);
                                    if (file_exists($imagePath)) {
                                        $imageData = file_get_contents($imagePath);
                                        if ($imageData !== false) {
                                            // Crear directorio si no existe
                                            $path = 'assets/img/productos/';
                                            if (!is_dir($path)) {
                                                mkdir($path, 0777, true);
                                            }

                                            // Obtener extensión original o usar jpg por defecto
                                            $extension = pathinfo($extractedPart, PATHINFO_EXTENSION);
                                            $extension = $extension ? $extension : 'jpg';

                                            $filename = $path . uniqid() . '.' . $extension;
                                            if (file_put_contents($filename, $imageData)) {
                                                $foto = base_url($filename);
                                                log_message("error", "Imagen guardada: " . $foto);
                                                $imageFound = true;
                                                break 2; // Salir de ambos foreach
                                            }
                                        }
                                    }
                                }
                            } else {
                                log_message("error", "La ruta no contiene fragmento: " . $drawingPath);
                            }
                        }
                    }
                }

                // Si no se encontró imagen, intentar obtener valor de texto en la primera fila del rango
                if (empty($foto)) {
                    $foto_valor = $sheet->getCell("C$startRow")->getValue();
                    $foto = !empty($foto_valor) ? base_url(trim($foto_valor)) : '';
                    log_message("error", "No se encontró imagen, usando valor de texto: " . $foto_valor);
                }

                // Obtener características combinando todas las celdas del rango mergeado
                $caracteristicas = "";
                for ($j = $startRow; $j <= $endRow; $j++) {
                    $cellContent = trim($sheet->getCell("D$j")->getValue());
                    if (!empty($cellContent)) {
                        $caracteristicas .= $cellContent . " ";
                    }
                }
                $caracteristicas = trim($caracteristicas);

                // Obtener el resto de datos (usar la primera fila del rango)
                $rubro = trim($sheet->getCell("E$startRow")->getValue());
                $tipo_producto = trim($sheet->getCell("F$startRow")->getValue());
                $precio_exw = $sheet->getCell("G$startRow")->getValue();
                $subpartida = $sheet->getCell("H$startRow")->getValue();
                $link = $sheet->getCell("I$startRow")->getValue();
                $unidad_comercial = $sheet->getCell("J$startRow")->getValue();
                $arancel_sunat = $sheet->getCell("K$startRow")->getValue();
                $arancel_tlc = $sheet->getCell("L$startRow")->getValue();
                $antidumping = $sheet->getCell("M$startRow")->getValue();
                $correlativo = $sheet->getCell("N$startRow")->getValue();
                $etiquetado = $sheet->getCell("O$startRow")->getValue();
                $doc_especial = $sheet->getCell("P$startRow")->getValue();

                // Preparar datos para insertar
                $data = [
                    'idContenedor' => $idContenedor,
                    'item' => $item,
                    'nombre_comercial' => $nombre_comercial,
                    'foto' => $foto,
                    'caracteristicas' => $caracteristicas,
                    'rubro' => $rubro,
                    'tipo_producto' => $tipo_producto,
                    'precio_exw' => $precio_exw,
                    'subpartida' => $subpartida,
                    'link' => $link,
                    'unidad_comercial' => $unidad_comercial,
                    'arancel_sunat' => $arancel_sunat,
                    'arancel_tlc' => $arancel_tlc,
                    'antidumping' => $antidumping,
                    'correlativo' => $correlativo,
                    'etiquetado' => $etiquetado,
                    'doc_especial' => $doc_especial
                ];

                // Guardar en la base de datos
                if ($this->db->insert($this->table_bd_productos, $data)) {
                    log_message('error', 'Producto insertado correctamente: ' . $item);
                } else {
                    log_message('error', 'Error al insertar producto: ' . $item);
                }

                // Avanzar al siguiente rango
                $row = $endRow + 1;
            }

            // Limpiar archivos temporales
            $tempDir = 'assets/uploads/xl/';
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
                log_message("error", "Directorio temporal eliminado: " . $tempDir);
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en importarProductosDesdeExcel: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    // Función auxiliar para eliminar directorios recursivamente
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }
}
