<?php
class AdministracionModel extends CI_Model{
    private $table_consolidado_pagos = "contenedor_consolidado_cotizacion_coordinacion_pagos";
    private $table_consolidado_pagos_concept = "cotizacion_coordinacion_pagos_concept";
    private $table_curso_pagos= "pedido_curso_pagos";
    private $table_cursos_pagos_conceptos = "pedido_curso_pagos_concept";
    private $table_consolidado="carga_consolidada_contenedor";
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
	private $table_pedido_curso_pagos= 'pedido_curso_pagos';
	private $table_pedido_curso_pagos_conceptos = 'pedido_curso_pagos_concept';
	private $CONCEPT_PAGO_ADELANTO_CURSO = 1; // Define el concepto de pago de adelanto
	public function __construct(){
		parent::__construct();
	}
     public function getConsolidadoPagos(){
        $this->db->select(
            $this->table_consolidado_cotizacion . '.*, 
            COUNT(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.id END) as total_pagos,
            ' . $this->table_consolidado . '.id as id_consolidado, 
            ' . $this->table_consolidado . '.carga as carga,
            SUM(CASE WHEN ' . $this->table_consolidado_pagos_concept . '.name = "LOGISTICA" OR ' . $this->table_consolidado_pagos_concept . '.name = "IMPUESTOS" THEN ' . $this->table_consolidado_pagos . '.monto ELSE 0 END) as total_pagos_monto'
        );
        
        $this->db->from($this->table_consolidado_cotizacion);
        
        $this->db->join($this->table_consolidado_pagos, 
                       $this->table_consolidado_pagos . '.id_cotizacion = ' . $this->table_consolidado_cotizacion . '.id', 
                       'left');
        
        $this->db->join($this->table_consolidado_pagos_concept, 
                       $this->table_consolidado_pagos_concept . '.id = ' . $this->table_consolidado_pagos . '.id_concept', 
                       'left');
        
        $this->db->join($this->table_consolidado, 
                       $this->table_consolidado . '.id = ' . $this->table_consolidado_cotizacion . '.id_contenedor', 
                       'inner');
        
        $this->db->group_by($this->table_consolidado_cotizacion . '.id');
        $this->db->having('total_pagos', 1);
        
        return $this->db->get()->result();
    }
    public function getPagosCoordination($idCotizacion){
        try{
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
        }catch(Exception $e){
            log_message('error', 'Error en getPagosCoordination: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los pagos: ' . $e->getMessage()
            ];
        }
    }
    public function getCursosPagos(){
		 $this->db->select("CC.*
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

		if(!empty($this->input->post('estado_pago'))) 
			$this->db->where("CC.Nu_Estado=", $this->input->post('estado_pago'));  // Update reference

		$this->db->where("CC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		if(isset($this->order)) { 
			$order = $this->order; 
			$this->db->order_by(key($order), $order[key($order)]); 
		}
			$query = $this->db->get();
			return $query->result();
	}
    public function getPagosCurso($idPedidoCurso){
        try{
            $this->db->select('pedido_curso_pagos.*, pedido_curso_pagos_concept.name as concepto')
                ->from($this->table_pedido_curso_pagos)
                ->join($this->table_pedido_curso_pagos_conceptos, 'pedido_curso_pagos.id_concept = pedido_curso_pagos_conceptos.id')
                ->where('id_pedido_curso', $idPedidoCurso)
                ->order_by('payment_date', 'DESC');
            $query = $this->db->get();
            return $query->result();
        }catch(Exception $e){
            log_message('error', 'Error en getPagosCurso: ' . $e->getMessage());
            return [
                'status' => "error",
                'message' => 'Error al obtener los pagos del curso: ' . $e->getMessage()
            ];
        }
    }
}