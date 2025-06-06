<?php
require_once APPPATH . 'traits/FileTrait.php';

class PedidosCursoModel extends CI_Model
{
	use FileTrait;
	var $table = 'pedido_curso';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_configuracion = 'configuracion';
	var $table_moneda = 'moneda';
	var $table_importe = 'importe';
	var $table_cliente = 'entidad';
	var $table_medio_pago = 'medio_pago';
	var $table_departamento = 'departamento';
	var $table_provincia = 'provincia';
	var $table_distrito = 'distrito';
	var $table_tipo_curso = 'tipo_curso';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_usuario = 'usuario';
	var $table_pais = 'pais';
	var $table_pedido_curso_pagos = 'pedido_curso_pagos';
	var $table_pedido_curso_pagos_conceptos = 'pedido_curso_pagos_concept';
	var $CONCEPT_PAGO_ADELANTO = 1; // Define el concepto de pago de adelanto
	var $order = array('Fe_Registro' => 'desc');

	public function __construct()
	{
		parent::__construct();
	}

	public function _get_datatables_query()
	{
		try {
			$this->db->select("PC.*, 
			CLI.Fe_Nacimiento, CLI.Nu_Como_Entero_Empresa, CLI.No_Otros_Como_Entero_Empresa,
			 No_Distrito, No_Provincia, No_Departamento, TDI.No_Tipo_Documento_Identidad_Breve,
			  P.No_Pais, CLI.Nu_Tipo_Sexo, CLI.No_Entidad, CLI.Nu_Documento_Identidad,
			   CLI.Nu_Celular_Entidad, CLI.Txt_Email_Entidad, CLI.Nu_Edad, M.No_Signo,
			    USR.ID_Usuario, USR.No_Usuario, USR.No_Password,
				
			         (                 
             SELECT COUNT(*)                  
             FROM pedido_curso_pagos as cccp                 
             JOIN pedido_curso_pagos_concept ccp ON cccp.id_concept = ccp.id                 
             WHERE cccp.id_pedido_curso = PC.ID_Pedido_Curso                 
             AND (ccp.name = 'ADELANTO')             
         ) AS pagos_count,
         (                 
             SELECT IFNULL(SUM(cccp.monto), 0)                  
             FROM pedido_curso_pagos as cccp                 
             JOIN pedido_curso_pagos_concept ccp ON cccp.id_concept= ccp.id                 
             WHERE cccp.id_pedido_curso = PC.ID_Pedido_Curso                 
             AND (ccp.name = 'ADELANTO')           
         ) AS total_pagos")
				->from($this->table. ' AS PC')
				->join($this->table_pais . ' AS P', 'P.ID_Pais = PC.ID_Pais', 'join')
				->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = PC.ID_Entidad', 'join')
				->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
				->join($this->table_moneda . ' AS M', 'M.ID_Moneda = PC.ID_Moneda', 'join')
				->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'join')
				->join($this->table_distrito, $this->table_distrito . '.ID_Distrito = CLI.ID_Distrito', 'left')
				->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = CLI.ID_Provincia', 'left')
				->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = CLI.ID_Departamento', 'left')
				->where('PC.ID_Empresa', $this->user->ID_Empresa);

			//if !empty isset Filtro_Fe_Inicio
			if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
				$this->db->where('DATE(PC.Fe_Registro) >=', $this->input->post('Filtro_Fe_Inicio'));
			}
			//if !empty isset Filtro_Fe_Fin
			if (!empty($this->input->post('Filtro_Fe_Fin'))) {
				$this->db->where('DATE(PC.Fe_Registro) <=', $this->input->post('Filtro_Fe_Fin'));
			}

			if (isset($this->order)) {
				$order = $this->order;
				$this->db->order_by(key($order), $order[key($order)]);
			}
		} catch (Exception $e) {
			log_message('error', 'Error en _get_datatables_query: ' . $e->getMessage());
		}
	}

	public function getDatosClientePorPedido($id_pedido)
	{
		$this->db->select("
			CLI.ID_Entidad as id_entidad,
			CLI.No_Entidad as nombres,
			CLI.Nu_Tipo_Sexo as sexo,
			CLI.Nu_Documento_Identidad as dni,
			CLI.Nu_Celular_Entidad as whatsapp,
			CLI.Txt_Email_Entidad as correo,
			CLI.Fe_Nacimiento as nacimiento,
			CLI.Nu_Como_Entero_Empresa as red_social,
			P.ID_Pais as id_pais,
			D.ID_Departamento as id_departamento,
			PR.ID_Provincia as id_provincia,
			DI.ID_Distrito as id_distrito,
			P.No_Pais as pais,
			D.No_Departamento as departamento,
			PR.No_Provincia as provincia,
			DI.No_Distrito as distrito,
			USR.ID_Usuario as id_usuario, 
			USR.usuario_moodle as usuario_moodle,
			USR.No_Password as password_moodle,
			PC.ID_Campana,
			PC.tipo_curso as tipo_curso,
			PC.Nu_Estado as Nu_Estado, 
			PC.Nu_Estado_Usuario_Externo as Nu_Estado_Usuario_Externo,
			PC.ID_Pedido_Curso as id_pedido_curso,
			MONTH(CC.Fe_Inicio) as mes_numero"
		)
			->from($this->table . ' AS PC')
			->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = PC.ID_Entidad', 'join')
			->join($this->table_pais . ' AS P', 'P.ID_Pais = PC.ID_Pais', 'join')
			->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'left')
			->join($this->table_distrito . ' AS DI', 'DI.ID_Distrito = CLI.ID_Distrito', 'left')
			->join($this->table_provincia . ' AS PR', 'PR.ID_Provincia = CLI.ID_Provincia', 'left')
			->join($this->table_departamento . ' AS D', 'D.ID_Departamento = CLI.ID_Departamento', 'left')
			->join('campana_curso AS CC', 'CC.ID_Campana = PC.ID_Campana', 'left')
			->where('PC.ID_Pedido_Curso', $id_pedido);

		$query = $this->db->get();
		$data = $query->row_array();

		// Traduce el mes a español
		if ($data && isset($data['mes_numero'])) {
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
			$data['mes_nombre'] = $meses_es[(int)$data['mes_numero']];
		} else {
			$data['mes_nombre'] = '';
		}

		return $data;
	}

	function get_datatables()
	{
		try {
			$this->_get_datatables_query();
			if ($_POST['length'] != -1)
				$this->db->limit($_POST['length'], $_POST['start']);
			$query = $this->db->get();
			if ($this->db->error()['code'] != 0) {
				log_message('error', 'Error en get_datatables: ' . $this->db->error()['message']);
			}
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Error en get_datatables: ' . $e->getMessage());
		}
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	public function setUsuarioModdle($user,$password,$id_usuario){
		$data = array(
			'usuario_moodle' => $user,
			'No_Password' => $password
		);
		$this->db->where('ID_Usuario', $id_usuario);
		if ($this->db->update($this->table_usuario, $data)) {
			return array('status' => 'success', 'message' => 'Usuario actualizado correctamente');
		} else {
			return array('status' => 'error', 'message' => 'Error al actualizar el usuario');
		}

	}
	public function actualizarPedido($where, $data)
	{
		if ($this->db->update('pedido_curso', $data, $where) > 0)
			return array('status' => 'success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'message' => 'Error al modificar');
	}

	public function getUsuario($id)
	{
		$query = "SELECT No_Usuario, No_Password,usuario_moodle, No_Nombres_Apellidos FROM usuario WHERE ID_Usuario = " . $id . " LIMIT 1";

		if (!$this->db->simple_query($query)) {
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
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
	public function actualizarDatosCliente($id_entidad, $data)
	{
		$this->db->where('ID_Entidad', $id_entidad);
		$this->db->update('entidad', $data);
		if ($this->db->affected_rows() > 0) {
			return array('status' => 'success', 'message' => 'Datos del cliente actualizados');
		}
		return array('status' => 'warning', 'message' => 'No se modificó ningún dato');
	}
	public function getCampanas()
	{
		$this->db->select('
			c.ID_Campana,
			c.Fe_Creacion,
			c.Fe_Inicio,
			c.Fe_Fin,
			MONTH(c.Fe_Inicio) as Mes_Numero,
			(SELECT COUNT(*) FROM pedido_curso p WHERE p.ID_Campana = c.ID_Campana) as cantidad_personas
		');
		$this->db->from('campana_curso c');
		$this->db->where('c.Fe_Borrado IS NULL');
		$query = $this->db->get();
		$result = $query->result_array();

		// Traduce el mes a español
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
			$row['No_Campana'] = $meses_es[(int)$row['Mes_Numero']];
		}
		return $result;
	}

	public function crearCampana($fe_inicio, $fe_fin)
	{
		// Insertar campaña
		$data = [
			'Fe_Inicio'   => $fe_inicio,
			'Fe_Fin'      => $fe_fin,
			'Fe_Creacion' => date('Y-m-d H:i:s')
		];
		$this->db->insert('campana_curso', $data);

		if ($this->db->affected_rows() > 0) {
			// Obtener la campaña recién creada
			$id = $this->db->insert_id();
			$this->db->select('
				c.ID_Campana,
				c.Fe_Creacion,
				c.Fe_Inicio,
				c.Fe_Fin,
				MONTH(c.Fe_Inicio) as Mes_Numero,
				(SELECT COUNT(*) FROM pedido_curso p WHERE p.ID_Campana = c.ID_Campana) as cantidad_personas
			');
			$this->db->from('campana_curso c');
			$this->db->where('c.ID_Campana', $id);
			$row = $this->db->get()->row_array();

			// Traduce el mes a español
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
			$no_campana = $meses_es[(int)$row['Mes_Numero']];

			// Armar array por posición (igual que en getCampanas)
			$data_row = [
				$row['ID_Campana'],
				$row['Fe_Creacion'],
				$no_campana,
				$row['Fe_Inicio'],
				$row['Fe_Fin'],
				$row['cantidad_personas'],
				'<div>
					<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;"></i>
					<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="borrarCampana(\'' . $row['ID_Campana'] . '\')"></i>
				</div>'
			];

			return [
				'status' => 'success',
				'message' => 'Campaña registrada correctamente',
				'row' => $data_row // <-- array por posición
			];
		} else {
			return ['status' => 'error', 'message' => 'No se pudo registrar la campaña'];
		}
	}
	public function editarCampana($id, $fe_inicio, $fe_fin)
	{
		$data = [
			'Fe_Inicio' => $fe_inicio,
			'Fe_Fin'    => $fe_fin
		];
		$this->db->where('ID_Campana', $id);
		$this->db->update('campana_curso', $data);
		if ($this->db->affected_rows() > 0) {
			return ['status' => 'success', 'message' => 'Campaña actualizada correctamente'];
		} else {
			return ['status' => 'warning', 'message' => 'No se modificó ningún dato'];
		}
	}
	public function getCampanaById($id)
	{
		$this->db->where('ID_Campana', $id);
		$query = $this->db->get('campana_curso');
		return $query->row_array();
	}

	public function borrarCampana($id)
	{
		$this->db->where('ID_Campana', $id);
		$this->db->update('campana_curso', ['Fe_Borrado' => date('Y-m-d H:i:s')]);
		if ($this->db->affected_rows() > 0) {
			return ['status' => 'success', 'message' => 'Campaña eliminada correctamente'];
		} else {
			return ['status' => 'error', 'message' => 'No se pudo eliminar la campaña'];
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

	public function asignarCampanaPedido($id_pedido, $id_campana)
	{
		$this->db->where('ID_Pedido_Curso', $id_pedido);
		$this->db->update('pedido_curso', ['ID_Campana' => $id_campana]);
		if ($this->db->affected_rows() > 0) {
			return ['status' => 'success', 'message' => 'Campaña asignada correctamente'];
		} else {
			return ['status' => 'warning', 'message' => 'No se modificó ningún dato'];
		}
	}
	public function actualizarImportePedido($id_pedido, $importe)
	{
		$this->db->where('ID_Pedido_Curso', $id_pedido);
		$this->db->update('pedido_curso', ['Ss_Total' => $importe]);
		if ($this->db->affected_rows() > 0) {
			return ['status' => 'success', 'message' => 'Importe actualizado correctamente'];
		} else {
			return ['status' => 'warning', 'message' => 'No se modificó ningún dato'];
		}
	}
	public function getPrefijoPais($id_pais)
	{
		$this->db->select("prefijo_pais")
			->from($this->table_pais)
			->where("ID_Pais", $id_pais);
		$query = $this->db->get();
		return $query->result();
	}
	public function getEntidadByIdPedido($id_pedido)
	{
		$this->db->select('CLI.ID_Entidad,CLI.ID_Pais,CLI.Nu_Celular_Entidad')
			->from($this->table)
			->join($this->table_cliente . ' as CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
			->where('ID_Pedido_Curso', $id_pedido);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		return null;
	}
	public function getPagosCurso()
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
			->from($this->table . ' AS CC')  // Add the CC alias here!
			->join($this->table_pais . ' AS P', 'P.ID_Pais = CC.ID_Pais', 'join')  // Update references
			->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = CC.ID_Entidad', 'join')  // Update references
			->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join')
			->join($this->table_moneda . ' AS M', 'M.ID_Moneda = CC.ID_Moneda', 'join')  // Update references
			->join($this->table_usuario . ' AS USR', 'USR.ID_Entidad = CLI.ID_Entidad', 'join')
			->join($this->table_distrito, $this->table_distrito . '.ID_Distrito = CLI.ID_Distrito', 'left')
			->join($this->table_provincia, $this->table_provincia . '.ID_Provincia = CLI.ID_Provincia', 'left')
			->join($this->table_departamento, $this->table_departamento . '.ID_Departamento = CLI.ID_Departamento', 'left')
			->where('CC.ID_Empresa', $this->user->ID_Empresa);  // Update reference

		if (!empty($this->input->post('estado_pago'))) {
			$this->db->where("CC.Nu_Estado=", $this->input->post('estado_pago'));  // Update reference
		}
		if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
			$this->db->where('CC.Fe_Registro >=', $this->input->post('Filtro_Fe_Inicio'));
		}
		//if !empty isset Filtro_Fe_Fin
		if (!empty($this->input->post('Filtro_Fe_Fin'))) {
			$this->db->where('CC.Fe_Registro <=', $this->input->post('Filtro_Fe_Fin'));
		}
		if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		$query = $this->db->get();
		return $query->result();
	}
	 public function getPagosCursoPedido($idPedidoCurso)
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
	public function saveClientePagosCurso($voucher, $idPedido, $amount, $fecha, $banco)
	{
		try {
			$voucherUrl = $this->uploadSingleFile(
				[
					"name" => $voucher['name'],
					"type" => $voucher['type'],
					"tmp_name" => $voucher['tmp_name'],
					"error" => $voucher['error'],
					"size" => $voucher['size']
				],
				'assets/curso/pagos'
			);
			$data = [
				'voucher_url' => $voucherUrl,
				'id_pedido_curso' => $idPedido,
				'id_concept' => $this->CONCEPT_PAGO_ADELANTO,
				'monto' => $amount,
				'payment_date' => date('Y-m-d', strtotime($fecha)),
				'banco' => $banco
			];
			$this->db->insert($this->table_pedido_curso_pagos, $data);
			if ($this->db->error()['code'] != 0) {
				log_message('error', 'Error en saveClientePagoCurso: ' . $this->db->error()['message']);
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
	public function getCursosHeader()
	{
		//get sum of importe from pedido_curso_pagos 
		$this->db->select('SUM(Ss_Total) as total_importe');
		$this->db->from($this->table);
		
        if (!empty($this->input->post('Filtro_Fe_Inicio'))) {
            $this->db->where($this->table.'.Fe_Emision >=', $this->input->post('Filtro_Fe_Inicio'));
        }

        if (!empty($this->input->post('Filtro_Fe_Fin'))) {
            $this->db->where($this->table.'.Fe_Emision <=', $this->input->post('Filtro_Fe_Fin'));
        }
		$query = $this->db->get();
		return $query->row();
	}
}
