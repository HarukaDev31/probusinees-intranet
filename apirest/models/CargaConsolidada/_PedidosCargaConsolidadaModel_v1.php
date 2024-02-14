<?php
class PedidosCargaConsolidadaModel extends CI_Model{
	var $table = 'carga_consolidada_pedido_cabecera';
	var $table_carga_consolidada_pedido_detalle = 'carga_consolidada_pedido_detalle';
	var $table_carga_consolidada_seguimiento = 'carga_consolidada_seguimiento';
	var $table_carga_consolidada_cabecera_checklist = 'carga_consolidada_cabecera_checklist';
	var $table_cliente = 'entidad';

    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select(
			$this->table . '.*, CCCC.ID_Pedido_Cabecera_Checklist, CCCC.Nu_Tipo_Categoria, CCCC.No_Checklist, CCCC.Nu_Tarea'
		)
		->from($this->table)
		->join($this->table_carga_consolidada_cabecera_checklist . ' AS CCCC', 'CCCC.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa);

		$this->db->where("Fe_Registro BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . " 00:00:00' AND '" . $this->input->post('Filtro_Fe_Fin') . " 23:59:59'");

		if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($ID){
        $this->db->select($this->table . '.*, CLI.No_Entidad, CLI.ID_Entidad');
        $this->db->from($this->table);
    	$this->db->join($this->table_carga_consolidada_pedido_detalle . ' AS PD', 'PD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = PD.ID_Entidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function agregarPedido($data, $arrEntidad){
		if ( $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa=" . $data['ID_Empresa'] . " AND No_Carga_Consolidada='" . $data['No_Carga_Consolidada'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			if ( $this->db->insert($this->table, $data) > 0 ) {
				$ID_Pedido_Cabecera = $this->db->insert_id();

				//copiar lista de tarea para carga consolidada
				$arrResponseChechlist = $this->obtenerChecklist();
				if($arrResponseChechlist['status']=='success') {
					foreach ($arrResponseChechlist['result'] as $row) {
						$data_checklist[] = array(
							'ID_Empresa'			=> $this->user->ID_Empresa,
							'ID_Organizacion'		=> $this->user->ID_Organizacion,
							'ID_Pedido_Cabecera' 	=> $ID_Pedido_Cabecera,
							'Nu_Tipo_Categoria' 	=> $row->Nu_Tipo_Categoria,
							'No_Checklist' 			=> $row->No_Checklist,
							'Nu_Tarea' => 0//pendiente
						);
					}
					$this->db->insert_batch($this->table_carga_consolidada_cabecera_checklist, $data_checklist);
				}
				foreach ($arrEntidad as $row) {
					$detalle[] = array(
						'ID_Empresa'			=> $this->user->ID_Empresa,
						'ID_Organizacion'		=> $this->user->ID_Organizacion,
						'ID_Pedido_Cabecera' 	=> $ID_Pedido_Cabecera,
						'ID_Entidad'			=> $this->security->xss_clean($row['ID_Entidad'])
					);
				}
				$this->db->insert_batch($this->table_carga_consolidada_pedido_detalle, $detalle);

				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
			}
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarPedido($where, $data, $arrEntidad){
		if ( $this->db->update($this->table, $data, $where) > 0 ) {

	    	$this->db->where('ID_Pedido_Cabecera', $where['ID_Pedido_Cabecera']);
        	$this->db->delete($this->table_carga_consolidada_pedido_detalle);
			foreach ($arrEntidad as $row) {
				//array_debug($row['ID_Entidad']);
				$detalle[] = array(
					'ID_Empresa'			=> $this->user->ID_Empresa,
					'ID_Organizacion'		=> $this->user->ID_Organizacion,//Organizacion
					'ID_Pedido_Cabecera' 	=> $where['ID_Pedido_Cabecera'],
					'ID_Entidad'			=> $this->security->xss_clean($row['ID_Entidad'])
				);
			}
			$this->db->insert_batch($this->table_carga_consolidada_pedido_detalle, $detalle);

			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarPedido($ID){
        $query ="SELECT 1 AS existe FROM carga_consolidada_seguimiento WHERE ID_Pedido_Cabecera = " . $ID . " LIMIT 1";
        $objRegistro = $this->db->query($query)->row();
		if(is_object($objRegistro)){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Tiene seguimiento(s)');
		} else {
			$this->db->where('ID_Pedido_Cabecera', $ID);
            $this->db->delete($this->table);
            
		    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
	
	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Tipo_Canal' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
    }
	
	public function sendMessage($arrPost){
        $data = array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrPost['enviar_mensaje-id_pedido_cabecera'],
			'No_Seguimiento' => $arrPost['enviar_mensaje-No_Seguimiento']
		);
		if ($this->db->insert($this->table_carga_consolidada_seguimiento, $data) > 0) {
			return array('status' => 'success', 'message' => 'Se envío seguimiento');
		}
		return array('status' => 'error', 'message' => 'Error al enviar seguimiento');
    }
	
	public function obtenerCantidadMensaje($ID_Pedido_Cabecera){
		$query = "SELECT 1 FROM " . $this->table_carga_consolidada_seguimiento . " WHERE ID_Pedido_Cabecera = " . $ID_Pedido_Cabecera;
		$arrData = $this->db->query($query)->result();
		$iCantidadMensaje = 0;
		foreach ($arrData as $row) {
			++$iCantidadMensaje;
		}
		return $iCantidadMensaje;
    }
	
	public function obtenerEntidad($arrParams){
		$query = "SELECT
DET.ID_Entidad AS id,
CLI.No_Entidad AS nombre,
CLI.Txt_Email_Entidad AS correo,
CCCC.No_Checklist
FROM
" . $this->table_carga_consolidada_pedido_detalle . " AS DET
JOIN entidad AS CLI ON(CLI.ID_Entidad = DET.ID_Entidad)
JOIN " . $this->table_carga_consolidada_cabecera_checklist . " AS CCCC ON(CCCC.ID_Pedido_Cabecera = DET.ID_Pedido_Cabecera)
WHERE
DET.ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'] . " AND CCCC.ID_Pedido_Cabecera_Checklist=" . $arrParams['ID_Pedido_Cabecera_Checklist'];
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos entidad',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
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
	
	public function obtenerChecklist(){
		$query = "SELECT Nu_Tipo_Categoria, No_Checklist FROM carga_consolidada_checklist WHERE Nu_Estado=1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos entidad',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
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

	public function cambiarEstadoTarea($ID, $Nu_Estado, $ID_Pedido_Cabecera){
        $where = array('ID_Pedido_Cabecera_Checklist' => $ID);
        $data = array('Nu_Tarea' => $Nu_Estado);
		if ($this->db->update($this->table_carga_consolidada_cabecera_checklist, $data, $where) > 0) {
			if($Nu_Estado==1){//1=completada tarea enviar mensaje
				$arrParams = array(
					'ID_Pedido_Cabecera' => $ID_Pedido_Cabecera,
					'ID_Pedido_Cabecera_Checklist' => $ID,
				);
				$this->sendMessageTodos($arrParams);
			}

			return array('status' => 'success', 'message' => 'Actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}

	public function sendMessageTodos($arrParams){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		//aquí voy hacer un foreach de todos los correos que debo de enviar
		$arrResponseEntidad = $this->PedidosCargaConsolidadaModel->obtenerEntidad($arrParams);
		if($arrResponseEntidad['status'] == 'success'){
			$iCantidadEmail = 0;
			$arrEmail = array();
			$message = '';
			foreach ($arrResponseEntidad['result'] as $row) {
				$bStatusEnviarCorreo = true;
				if(!empty($row->correo)){
					if (!filter_var($row->correo, FILTER_VALIDATE_EMAIL)) {
						$bStatusEnviarCorreo = false;
					}
			
					if (!is_valid_email($row->correo)) {
						$bStatusEnviarCorreo = false;
					}
			
					if (!is_valid_email_expresion_regular($row->correo)) {
						$bStatusEnviarCorreo = false;
					}

					if($bStatusEnviarCorreo==true){//enviar correo
						array_push($arrEmail, $row->correo);
					}
				}
				$message = $row->No_Checklist;
				//también debo de capturar a los que no envío por error
			}
			
			$arrData = array(
				'iTipoEmail' => 1,//1=todos y 2=cliente espicifico
				'arrEmail' => $arrEmail,
				'message' => $message
			);
			$arrResponseEmail = $this->enviarCorreoSeguimiento($arrData);
			if($arrResponseEmail['status']!='success'){
				echo json_encode($arrResponseEmail);
				exit();
			}

			$response = array(
				'status' => 'success',
				'message' => 'Se envío email'
			);
			echo json_encode($response);
			exit();
		} else {
			echo json_encode($arrResponseEntidad);
			exit();
		}
	}
	
	public function enviarCorreoSeguimiento($arrData){
		// enviar correo con las credenciales
		$this->load->library('email');

		//$data_email["email"] = $arrData['email'];
		//$data_email["name"] = $arrData['name'];
		$data_email["name"] = 'Cliente';
		$data_email["message"] = $arrData['message'];
		$message_email = $this->load->view('correos/seguimiento_carga_consolidada', $data_email, true);
		
		$this->email->from('noreply@lae.one', 'ProBusiness');//de
		$this->email->to($arrData['arrEmail']);
		//$this->email->to($arrData['email']);//para
		//$this->email->to('one@example.com, two@example.com, three@example.com');
		/*
		$this->email->to(
			array('one@example.com', 'two@example.com', 'three@example.com')
		);
		*/
		$this->email->subject('✅ Seguimiento tu carga');
		$this->email->message($message_email);
		$this->email->set_newline("\r\n");

		$isSend = $this->email->send();
		if($isSend) {
			$response = array(
				'status' => 'success',
				'message' => 'Se envío email'
			);
			return $response;
		} else {
			$response = array(
				'status' => 'error',
				'message' => 'No se pudo enviar email, inténtelo más tarde.',
				'error_message_mail' => $this->email->print_debugger()
			);
			return $response;
		}
	}
}
