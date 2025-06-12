<?php
class AdministracionModel extends CI_Model
{
    private $table_consolidado_pagos = "contenedor_consolidado_cotizacion_coordinacion_pagos";
    private $table_consolidado_pagos_concept = "cotizacion_coordinacion_pagos_concept";
    private $table_curso_pagos = "pedido_curso_pagos";
    private $table_cursos_pagos_conceptos = "pedido_curso_pagos_concept";
    private $table_consolidado = "carga_consolidada_contenedor";
    private $table_consolidado_cotizacion = "contenedor_consolidado_cotizacion";
    private $table_consolidado_cotizacion_providers = "contenedor_consolidado_cotizacion_proveedores";
    private $CONCEPT_PAGO_LOGISTICA = 1; // Assuming this is the ID for "LOGISTICA" concept
    private $CONCEPT_PAGO_IMPUESTOS = 2; // Assuming this is the ID for "IMPUESTOS" concept
    private $table_curso = 'pedido_curso';
    private $table_empresa = 'empresa';
    private $table_organizacion = 'organizacion';
    private $table_configuracion = 'configuracion';
    private $table_moneda = 'moneda';
    private $table_importe = 'importe';
    private $table_cliente = 'entidad';
    private $table_medio_pago = 'medio_pago';
    private $table_departamento = 'departamento';
    private $table_provincia = 'provincia';
    private $table_distrito = 'distrito';
    private $table_tipo_documento_identidad = 'tipo_documento_identidad';
    private $table_usuario = 'usuario';
    private $table_pais = 'pais';
    private $table_pedido_curso_pagos = 'pedido_curso_pagos';
    private $table_pedido_curso_pagos_conceptos = 'pedido_curso_pagos_concept';
    private $CONCEPT_PAGO_ADELANTO_CURSO = 1; // Define el concepto de pago de adelanto
    public function __construct()
    {
        parent::__construct();
    }
    public function getConsolidadoPagos()
    {
        $this->db->select(
            $this->table_consolidado_cotizacion . '.*, 
        ' . $this->table_consolidado . '.id as id_consolidado, 
        ' . $this->table_consolidado . '.carga as carga,
        (
            SELECT COUNT(*)
            FROM ' . $this->table_consolidado_pagos . ' as ccp
            JOIN ' . $this->table_consolidado_pagos_concept . ' as ccpc ON ccp.id_concept = ccpc.id
            WHERE ccp.id_cotizacion = ' . $this->table_consolidado_cotizacion . '.id
            AND (ccpc.name = "LOGISTICA" OR ccpc.name = "IMPUESTOS")
        ) AS total_pagos,
        (
            SELECT IFNULL(SUM(ccp.monto), 0)
            FROM ' . $this->table_consolidado_pagos . ' as ccp
            JOIN ' . $this->table_consolidado_pagos_concept . ' as ccpc ON ccp.id_concept = ccpc.id
            WHERE ccp.id_cotizacion = ' . $this->table_consolidado_cotizacion . '.id
            AND (ccpc.name = "LOGISTICA" OR ccpc.name = "IMPUESTOS")
        ) AS total_pagos_monto,
        (SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                "id_pago", ccp2.id,
                "monto", ccp2.monto,
                "concepto", ccpc2.name,
                "status", ccp2.status,
                "payment_date", ccp2.payment_date
            )   
        ) FROM ' . $this->table_consolidado_pagos . ' as ccp2
        LEFT JOIN ' . $this->table_consolidado_pagos_concept . ' as ccpc2 ON ccp2.id_concept = ccpc2.id
        WHERE ccp2.id_cotizacion = ' . $this->table_consolidado_cotizacion . '.id 
        AND (ccp2.id_concept = ' . intval($this->CONCEPT_PAGO_LOGISTICA) . ' 
        OR ccp2.id_concept = ' . intval($this->CONCEPT_PAGO_IMPUESTOS) . ')
        ) as pagos_details'
        );

        $this->db->from($this->table_consolidado_cotizacion);

        $this->db->join(
            $this->table_consolidado,
            $this->table_consolidado . '.id = ' . $this->table_consolidado_cotizacion . '.id_contenedor',
            'inner'
        );

        // Filtros de fecha
        if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
            $this->db->where($this->table_consolidado_cotizacion . '.fecha >=', $this->input->post('Filtro_Fe_Inicio'));
        }

        if (!empty($this->input->post('Filtro_Fe_Fin'))) {
            $this->db->where($this->table_consolidado_cotizacion . '.fecha <=', $this->input->post('Filtro_Fe_Fin'));
        }

        //start or where group
        $this->db->where($this->table_consolidado_cotizacion . '.id IN (
        SELECT id_cotizacion FROM ' . $this->table_consolidado_pagos . ' 
        WHERE id_concept = ' . intval($this->CONCEPT_PAGO_LOGISTICA) . ' 
        OR id_concept = ' . intval($this->CONCEPT_PAGO_IMPUESTOS) . '
        )');
        //or where estado_cliente != null
        $this->db->or_where($this->table_consolidado_cotizacion . '.estado_cliente IS NOT NULL');




        // Filtros opcionales adicionales si los necesitas
        if (!empty($this->input->post('estado'))) {
            $this->db->where($this->table_consolidado_cotizacion . '.estado', $this->input->post('estado'));
        }

        if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        $query = $this->db->get();

        // Verificar si la query fue exitosa
        if (!$query) {
            log_message('error', 'Error en query getConsolidadoPagos: ' . $this->db->error()['message']);
            return [];
        }

        return $query->result();
    }
    public function getHeadersConsolidado()
    {
        $this->db->select(
            'SUM(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.monto ELSE 0 END) as total_pagos_monto,
        IFNULL(
            SUM(CASE 
                WHEN (IFNULL((' . $this->table_consolidado_cotizacion . '.impuestos_final + ' . $this->table_consolidado_cotizacion . '.monto_final),0)) = 0 
                THEN ' . $this->table_consolidado_cotizacion . '.monto 
                ELSE ' . $this->table_consolidado_cotizacion . '.impuestos_final + ' . $this->table_consolidado_cotizacion . '.monto_final 
            END), 
            0
        ) as total_importe'
        );

        $this->db->from($this->table_consolidado_cotizacion);

        $this->db->join(
            $this->table_consolidado_pagos,
            $this->table_consolidado_pagos . '.id_cotizacion = ' . $this->table_consolidado_cotizacion . '.id',
            'left'
        );

        $this->db->join(
            $this->table_consolidado_pagos_concept,
            $this->table_consolidado_pagos_concept . '.id = ' . $this->table_consolidado_pagos . '.id_concept',
            'left'
        );

        $this->db->join(
            $this->table_consolidado,
            $this->table_consolidado . '.id = ' . $this->table_consolidado_cotizacion . '.id_contenedor',
            'inner'
        );


        if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
            $this->db->where($this->table_consolidado_cotizacion . '.fecha >=', $this->input->post('Filtro_Fe_Inicio'));
        }

        if (!empty($this->input->post('Filtro_Fe_Fin'))) {
            $this->db->where($this->table_consolidado_cotizacion . '.fecha <=', $this->input->post('Filtro_Fe_Fin'));
        }

        $this->db->where($this->table_consolidado_cotizacion . '.id IN (SELECT id_cotizacion FROM ' . $this->table_consolidado_pagos . ' WHERE id_concept = ' . $this->CONCEPT_PAGO_LOGISTICA . ' OR id_concept = ' . $this->CONCEPT_PAGO_IMPUESTOS . ')');
        $this->db->or_where($this->table_consolidado_cotizacion . '.estado_cliente IS NOT NULL');


        $result = $this->db->get()->row_array();

        if ($result) {
            return [
                'total_importe' => $result['total_importe'] ?? 0,
            ];
        }

        return ['total_importe' => 0];
    }
    public function getPagosCoordination($idCotizacion)
    {
        try {
            //get all data from table contenedor_consolidado_cotizacion_coordinacion_pagos where id_cotizacion=idCotizacion join with contenedor_consolidado_cotizacion_coordinacion_pagos_concept where id_concept=concept_pagos_logistica
            $this->db->select('contenedor_consolidado_cotizacion_coordinacion_pagos.*')
                ->from($this->table_consolidado_pagos)
                ->join($this->table_consolidado_pagos_concept, 'contenedor_consolidado_cotizacion_coordinacion_pagos.id_concept = cotizacion_coordinacion_pagos_concept.id')
                ->where('id_cotizacion', $idCotizacion)
                ->where('id_concept', $this->CONCEPT_PAGO_LOGISTICA)
                ->or_where('id_concept', $this->CONCEPT_PAGO_IMPUESTOS)
                ->order_by('payment_date', 'DESC');
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
    public function getCursosPagos()
    {
        $this->db->select(
            "CC.*, 
    CLI.Fe_Nacimiento, 
    CLI.Nu_Como_Entero_Empresa, 
    CLI.No_Otros_Como_Entero_Empresa, 
    distrito.No_Distrito, 
    provincia.No_Provincia,  
    departamento.No_Departamento,  
    TDI.No_Tipo_Documento_Identidad_Breve, 
    P.No_Pais, 
    CLI.Nu_Tipo_Sexo,  
    CLI.No_Entidad, 
    CLI.Nu_Documento_Identidad, 
    CLI.Nu_Celular_Entidad, 
    CLI.Txt_Email_Entidad, 
    CLI.Nu_Edad, 
    M.No_Signo, 
    USR.ID_Usuario, 
    USR.No_Usuario, 
    USR.No_Password,
    (
        SELECT COUNT(*)
        FROM pedido_curso_pagos as cccp
        JOIN pedido_curso_pagos_concept ccp ON cccp.id_concept = ccp.id
        WHERE cccp.id_pedido_curso = CC.ID_Pedido_Curso
        AND (ccp.name = 'ADELANTO')
    ) AS pagos_count,
    (
        SELECT IFNULL(SUM(cccp.monto), 0)
        FROM pedido_curso_pagos as cccp
        JOIN pedido_curso_pagos_concept ccp ON cccp.id_concept = ccp.id
        WHERE cccp.id_pedido_curso = CC.ID_Pedido_Curso
        AND (ccp.name = 'ADELANTO')
    ) AS total_pagos,
    (SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
            'id_pago', cccp2.id,
            'monto', cccp2.monto,
            'status', cccp2.status
         
        )   
    ) FROM pedido_curso_pagos as cccp2
    where cccp2.id_pedido_curso = CC.ID_Pedido_Curso 
    and cccp2.id_concept = " . $this->CONCEPT_PAGO_ADELANTO_CURSO . "   
    ) as pagos_details,
    "
        );

        $this->db->from($this->table_curso . ' AS CC');

        $this->db->join($this->table_pais . ' AS P', 'P.ID_Pais = CC.ID_Pais', 'inner');
        $this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = CC.ID_Entidad', 'inner');
        $this->db->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'inner');
        $this->db->join($this->table_moneda . ' AS M', 'M.ID_Moneda = CC.ID_Moneda', 'inner');
        $this->db->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'inner');
        $this->db->join($this->table_distrito . ' AS distrito', 'distrito.ID_Distrito = CLI.ID_Distrito', 'left');
        $this->db->join($this->table_provincia . ' AS provincia', 'provincia.ID_Provincia = CLI.ID_Provincia', 'left');
        $this->db->join($this->table_departamento . ' AS departamento', 'departamento.ID_Departamento = CLI.ID_Departamento', 'left');

        $this->db->where('CC.ID_Empresa', $this->user->ID_Empresa);

        $this->db->where('CC.ID_Pedido_Curso IN (SELECT id_pedido_curso FROM pedido_curso_pagos WHERE id_concept = ' . $this->CONCEPT_PAGO_ADELANTO_CURSO . ')');


        if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
            $this->db->where('CC.Fe_Emision >=', $this->input->post('Filtro_Fe_Inicio'));
        }

        if (!empty($this->input->post('Filtro_Fe_Fin'))) {
            $this->db->where('CC.Fe_Emision <=', $this->input->post('Filtro_Fe_Fin'));
        }

        // Agregar todas las columnas al GROUP BY para cumplir con sql_mode=only_full_group_by


        if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        $query = $this->db->get();
        return $query->result();
    }
    public function getHeadersCurso()
    {
        //get sum of Ss_Total from pedido_curso
        $this->db->select('SUM(Ss_Total) as total');
        $this->db->from($this->table_curso);
        // join with pagos and have at least one payment with concept ADELANTO
        $this->db->where($this->table_curso . '.ID_Pedido_Curso IN (SELECT id_pedido_curso FROM pedido_curso_pagos WHERE id_concept = ' . $this->CONCEPT_PAGO_ADELANTO_CURSO . ')');
        //filter by estado_pago if provided
        //filter by date range if provided
        if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
            $this->db->where($this->table_curso . '.Fe_Emision >=', $this->input->post('Filtro_Fe_Inicio'));
        }
        if (!empty($this->input->post('Filtro_Fe_Fin'))) {
            $this->db->where($this->table_curso . '.Fe_Emision <=', $this->input->post('Filtro_Fe_Fin'));
        }
        $this->db->where($this->table_curso . '.ID_Pedido_Curso IN (SELECT id_pedido_curso FROM pedido_curso_pagos WHERE id_concept = ' . $this->CONCEPT_PAGO_ADELANTO_CURSO . ')');

        $query = $this->db->get();
        $result = $query->row_array();
        return [
            'total_importe' => $result['total'] ?? 0,
        ];
    }

    public function getPagosCurso($idPedidoCurso)
    {
        try {
            $this->db->select('pedido_curso_pagos.*, pedido_curso_pagos_concept.name as concepto')
                ->from($this->table_pedido_curso_pagos)
                ->join($this->table_pedido_curso_pagos_conceptos, 'pedido_curso_pagos.id_concept = pedido_curso_pagos_concept.id')
                ->where('id_pedido_curso', $idPedidoCurso)
                ->order_by('payment_date', 'DESC');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Error en getPagosCurso: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los pagos del curso: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailsPagosCurso($idPedidoCurso)
    {
        try {
            $details = $this->getPagosCurso($idPedidoCurso);
            $nota = $this->db->select('note_administracion')
                ->from($this->table_curso)
                ->where('ID_Pedido_Curso', $idPedidoCurso)
                ->get();
            return
                [
                    "data" => $details,
                    "nota" => $nota->result()[0]->note_administracion ?? '',

                ];
        } catch (Exception $e) {
            log_message('error', 'Error en getDetailsPagosCurso: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los detalles de los pagos del curso: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailsPagosConsolidado($idCotizacion)
    {
        try {
            $details = $this->getPagosCoordination($idCotizacion);
            $nota = $this->db->select('note_administracion,cotizacion_file_url,cotizacion_final_url')
                ->from($this->table_consolidado_cotizacion)
                ->where('id', $idCotizacion)
                ->get();
            //get
            return
                [
                    "data" => $details,
                    "nota" => $nota->result()[0]->note_administracion ?? '',
                    "cotizacion_inicial_url" => $nota->result()[0]->cotizacion_file_url,
                    "cotizacion_final_url" => $nota->result()[0]->cotizacion_final_url
                ];
        } catch (Exception $e) {
            log_message('error', 'Error en getDetailsPagosConsolidado: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los detalles de los pagos consolidados: ' . $e->getMessage()
            ];
        }
    }
    public function getCampanasActivas()
    {
        $this->db->select('ID_Campana, Fe_Inicio');
        $this->db->from('campana_curso');
        $this->db->where('Fe_Borrado IS NULL');
        $this->db->order_by('Fe_Inicio', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();

        $meses_es = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        foreach ($result as &$row) {
            $mes = (int)date('m', strtotime($row['Fe_Inicio']));
            $row['nombre_campana'] = $meses_es[$mes] . ' ' . date('Y', strtotime($row['Fe_Inicio']));
        }
        return $result;
    }
    public function saveNoteCurso($idPedidoCurso, $note)
    {
        //save in table pedido_curso 
        try {
            log_message("error", "saveNoteCurso: $idPedidoCurso: $idPedidoCurso, note: $note");
            $this->db->where('ID_Pedido_Curso', $idPedidoCurso);
            $this->db->update($this->table_curso, ['note_administracion' => $note]);
            // if db error code != 0 then return error

            if ($this->db->error()['code'] != 0) {
                throw new Exception($this->db->error()['message']);
            }
            return [
                'status' => "success",
                'message' => 'Nota actualizada correctamente'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error en saveNoteCurso: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al actualizar la nota: ' . $e->getMessage()
            ];
        }
    }
    public function saveNote($idCotizacion, $note)
    {
        //save in table cotizacion 
        try {
            log_message("error", "saveNote: $idCotizacion: $idCotizacion, note: $note");
            $this->db->where('id', $idCotizacion);
            $this->db->update($this->table_consolidado_cotizacion, ['note_administracion' => $note]);
            // if db error code != 0 then return error

            if ($this->db->error()['code'] != 0) {
                throw new Exception($this->db->error()['message']);
            }
            return [
                'status' => "success",
                'message' => 'Nota actualizada correctamente'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error en saveNote: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al actualizar la nota: ' . $e->getMessage()
            ];
        }
    }
    public function handlePayment($idPago, $status)
    {
        try {
            log_message("error", "handlePayment: idPago: $idPago, status: $status");
            $this->db->where('id', $idPago);
            $this->db->update($this->table_consolidado_pagos, ['status' => $status]);
            return [
                'status' => "success",
                'message' => 'Pago actualizado correctamente'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error en handlePayment: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al actualizar el pago: ' . $e->getMessage()
            ];
        }
    }
    public function handlePaymentCurso($idPagoCurso, $status)
    {
        try {
            log_message("error", "handlePaymentCurso: idPagoCurso: $idPagoCurso, status: $status");
            $this->db->where('id', $idPagoCurso);
            $this->db->update($this->table_pedido_curso_pagos, ['status' => $status]);
            return [
                'status' => "success",
                'message' => 'Pago del curso actualizado correctamente'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error en handlePaymentCurso: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al actualizar el pago del curso: ' . $e->getMessage()
            ];
        }
    }
}
