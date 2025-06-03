<?php
class AdministracionModel extends CI_Model
{
    private $table_consolidado_pagos = "contenedor_consolidado_cotizacion_coordinacion_pagos";
    private $table_consolidado_pagos_concept = "cotizacion_coordinacion_pagos_concept";
    private $table_curso_pagos = "pedido_curso_pagos";
    private $table_cursos_pagos_conceptos = "pedido_curso_pagos_concept";
    private $table_consolidado = "carga_consolidada_contenedor";
    private $table_consolidado_cotizacion = "contenedor_consolidado_cotizacion";
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
            COUNT(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.id END) as total_pagos,
            ' . $this->table_consolidado . '.id as id_consolidado, 
            ' . $this->table_consolidado . '.carga as carga,
            SUM(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.monto ELSE 0 END) as total_pagos_monto'
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

        $this->db->group_by($this->table_consolidado_cotizacion . '.id');
        $this->db->having('total_pagos', 1);

        return $this->db->get()->result();
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
            "CC.*
		  ,CLI.Fe_Nacimiento,
		   CLI.Nu_Como_Entero_Empresa,
		    CLI.No_Otros_Como_Entero_Empresa,
			 No_Distrito, No_Provincia, 
			 No_Departamento, 
			 TDI.No_Tipo_Documento_Identidad_Breve,
			  P.No_Pais, CLI.Nu_Tipo_Sexo, 
			  CLI.No_Entidad, CLI.Nu_Documento_Identidad,
			   CLI.Nu_Celular_Entidad, CLI.Txt_Email_Entidad,
			    CLI.Nu_Edad, M.No_Signo, USR.ID_Usuario, USR.No_Usuario, USR.No_Password,
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
             JOIN pedido_curso_pagos_concept ccp ON cccp.id_concept= ccp.id                 
             WHERE cccp.id_pedido_curso = CC.ID_Pedido_Curso                 
             AND (ccp.name = 'ADELANTO')           
         ) AS total_pagos"
        )
            ->from($this->table_curso . ' AS CC')  // Add the CC alias here!
            ->join($this->table_pais . ' AS P', 'P.ID_Pais = CC.ID_Pais', 'join')  // Update references
            ->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = CC.ID_Entidad', 'join')  // Update references
            ->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
            ->join($this->table_moneda . ' AS M', 'M.ID_Moneda = CC.ID_Moneda', 'join')  // Update references
            ->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'join')
            ->join($this->table_distrito, $this->table_distrito . '.ID_Distrito = CLI.ID_Distrito', 'left')
            ->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = CLI.ID_Provincia', 'left')
            ->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = CLI.ID_Departamento', 'left')
            ->where('CC.ID_Empresa', $this->user->ID_Empresa);  // Update reference
        //get all course with less than 0 payments
        $this->db->where('CC.ID_Pedido_Curso  IN (SELECT id_pedido_curso FROM pedido_curso_pagos WHERE id_concept = ' . $this->CONCEPT_PAGO_ADELANTO_CURSO . ')');
        if (!empty($this->input->post('estado_pago'))) {
            $this->db->where("CC.Nu_Estado=", $this->input->post('estado_pago'));  // Update reference
        }
        //$this->db->where("CC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

        if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        $query = $this->db->get();
        return $query->result();
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
    public function handlePayment($idPago, $isConfirmed)
    {
        try {
            log_message("error", "handlePayment: idPago: $idPago, isConfirmed: $isConfirmed");
            $this->db->where('id', $idPago);
            $this->db->update($this->table_consolidado_pagos, ['is_confirmed' => $isConfirmed]);
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
    public function handlePaymentCurso($idPagoCurso, $isConfirmed)
    {
        try {
            log_message("error", "handlePaymentCurso: idPagoCurso: $idPagoCurso, isConfirmed: $isConfirmed");
            $this->db->where('id', $idPagoCurso);
            $this->db->update($this->table_pedido_curso_pagos, ['is_confirmed' => $isConfirmed]);
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
    public function getHeadersConsolidado()
    {
        $this->db->select(
            $this->table_consolidado_cotizacion . '.*, 
            COUNT(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.id END) as total_pagos,
            ' . $this->table_consolidado . '.id as id_consolidado, 
            ' . $this->table_consolidado . '.carga as carga,
            SUM(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.monto ELSE 0 END) as total_pagos_monto,
            IFNULL(SUM(CASE WHEN ' . $this->table_consolidado_cotizacion . '.impuestos_final + ' . $this->table_consolidado_cotizacion . '.monto_final = 0 THEN ' . $this->table_consolidado_pagos . '.monto ELSE ' . $this->table_consolidado_cotizacion . '.impuestos_final + ' . $this->table_consolidado_cotizacion . '.monto_final END), 0) as total_importe
            '
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

        $this->db->group_by($this->table_consolidado_cotizacion . '.id');
        $this->db->having('total_pagos', 1);
        $result = $this->db->get()->row_array();
        if ($result) {
            return [
                'total_importe' => $result['total_importe'] ?? 0,
            ];
        }
    }
    public function getHeadersCurso()
    {
        //get sum of Ss_Total from pedido_curso
        $this->db->select('SUM(Ss_Total) as total');
        $this->db->from($this->table_curso);
        $query = $this->db->get();
        $result = $query->row_array();
        return [
            'total_importe' => $result['total'] ?? 0,
        ];
    }
}
