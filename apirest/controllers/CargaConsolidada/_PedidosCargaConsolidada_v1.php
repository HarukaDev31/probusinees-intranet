<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosCargaConsolidada extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		//$this->load->database('LAE_SYSTEMS');
		$this->load->model('CargaConsolidada/PedidosCargaConsolidadaModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('CargaConsolidada/PedidosCargaConsolidadaView');
			$this->load->view('footer_v2', array("js_pedidos_cargaconsolidada" => true));
		}
	}

	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->PedidosCargaConsolidadaModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();
			
            $rows[] = $row->No_Carga_Consolidada;
            $rows[] = ToDateBD($row->Fe_Inicio);
            $rows[] = ToDateBD($row->Fe_Termino);
            $rows[] = ToDateBD($row->Fe_Carga);
            $rows[] = ToDateBD($row->Fe_Zarpe);
            $rows[] = ToDateBD($row->Fe_Llegada);

			$dLiberacion = date_create($row->Fe_Llegada);
			date_add($dLiberacion,date_interval_create_from_date_string("7 days"));
			$dLiberacion = date_format($dLiberacion,"Y-m-d");

            $rows[] = ToDateBD($dLiberacion);

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerTipoCanal($row->Nu_Tipo_Canal);

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Garantizado" title="Garantizado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1);">Verde</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Enviado" title="Enviado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Naranja</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Rechazado" title="Rechazado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Rojo</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

			$dEntrega = '';
			if($row->Nu_Tipo_Canal!=0){
				//=SI(I1="VERDE",H4+1,SI(I1="ROJO",H4+10,SI(I1="NARANJA",H4+10,"")))
				
				$dEntrega = date_create($dLiberacion);
				if($row->Nu_Tipo_Canal==1){//verde
					date_add($dEntrega,date_interval_create_from_date_string("1 days"));
				} else {
					date_add($dEntrega,date_interval_create_from_date_string("10 days"));
				}
				$dEntrega = date_format($dEntrega,"Y-m-d");

				$dEntrega = ToDateBD($dEntrega);
			}

			$rows[] = $dEntrega;

			$sCategoria = 'Ninguna';//COMMENT '0=Ninguna, 1=Informativa, 2=Documentaria, 3=Declaración y 4=Final'
			if($row->Nu_Tipo_Categoria==1){
				$sCategoria = 'Informativa';
			} else if($row->Nu_Tipo_Categoria==2){
				$sCategoria = 'Documentaria';
			} else if($row->Nu_Tipo_Categoria==3){
				$sCategoria = 'Declaración';
			} else if($row->Nu_Tipo_Categoria==4){
				$sCategoria = 'Final';
			}

			//$rows[] = $sCategoria;
			
			$span_categoria = '<span class="badge bg-success">' . $sCategoria . '</span>';
			$rows[] = $span_categoria . '<br>' . $row->No_Checklist;
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoTareaChecklist($row->Nu_Tarea);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstadoTarea(\'' . $row->ID_Pedido_Cabecera_Checklist . '\',0);">Pendiente</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Completada" title="Completada" href="javascript:void(0)" onclick="cambiarEstadoTarea(\'' . $row->ID_Pedido_Cabecera_Checklist . '\',1);">Completada</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
			$rows[] = $dropdown_estado;

			/*
			$btn_enviar_mensaje = '<button class="btn btn-xs btn-link p-0" alt="Seguimiento" title="Seguimiento" href="javascript:void(0)" onclick="enviarSeguimiento(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-comments fa-2x" aria-hidden="true"></i>';
			
			$iCantidadMensaje = $this->PedidosCargaConsolidadaModel->obtenerCantidadMensaje($row->ID_Pedido_Cabecera);
			$btn_enviar_mensaje .= ($iCantidadMensaje > 0 ? '<span class="badge badge-danger">' . $iCantidadMensaje . '</span>' : '');
			
			$btn_enviar_mensaje .= '</button>';

			$btn_ver_mensaje = '<button class="btn btn-xs btn-link p-0" alt="Ver seguimiento" title="Ver seguimiento" href="javascript:void(0)" onclick="verSeguimiento(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-eye fa-2x" aria-hidden="true"></i>';
			
			$iCantidadMensaje = $this->PedidosCargaConsolidadaModel->obtenerCantidadMensaje($row->ID_Pedido_Cabecera);
			$btn_ver_mensaje .= ($iCantidadMensaje > 0 ? '<span class="badge badge-danger">' . $iCantidadMensaje . '</span>' : '');
			
			$btn_ver_mensaje .= '</button>';

			$rows[] = $btn_enviar_mensaje . '&nbsp;&nbsp;' . $btn_ver_mensaje;
			*/

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
			$data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosCargaConsolidadaModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function crudPedidoGrupal(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa'			=> $this->user->ID_Empresa,
			'ID_Organizacion'		=> $this->user->ID_Organizacion,//Organizacion
			'No_Carga_Consolidada'	=> $this->input->post('No_Carga_Consolidada'),
			'Fe_Inicio'				=> ToDate($this->input->post('Fe_Inicio')),
			'Fe_Termino'			=> ToDate($this->input->post('Fe_Termino')),
			'Fe_Carga'				=> ToDate($this->input->post('Fe_Carga')),
			'Fe_Zarpe'				=> ToDate($this->input->post('Fe_Zarpe')),
			'Fe_Llegada'			=> ToDate($this->input->post('Fe_Llegada')),
		);
		echo json_encode(
		$this->input->post('EID_Pedido_Cabecera') != '' ?
			$this->PedidosCargaConsolidadaModel->actualizarPedido(array('ID_Pedido_Cabecera' => $this->input->post('EID_Pedido_Cabecera')), $data, $this->input->post('arrEntidad'))
		:
			$this->PedidosCargaConsolidadaModel->agregarPedido($data, $this->input->post('arrEntidad'))
		);
	}
    
	public function eliminarPedido($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PedidosCargaConsolidadaModel->eliminarPedido($this->security->xss_clean($ID)));
	}

	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosCargaConsolidadaModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}

	public function sendMessage(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$arrResponse = $this->PedidosCargaConsolidadaModel->sendMessage($this->input->post());
		if($arrResponse['status'] == 'success'){
			//aquí voy hacer un foreach de todos los correos que debo de enviar
			$arrResponseEntidad = $this->PedidosCargaConsolidadaModel->obtenerEntidad($this->input->post());
			if($arrResponseEntidad['status'] == 'success'){
				$iCantidadEmail = 0;
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
							$arrData = array(
								'email' => $row->correo,
								'name' => $row->nombre,
								'message' => $this->input->post('enviar_mensaje-No_Seguimiento')
							);
							$arrResponseEmail = $this->enviarCorreoSeguimiento($arrData);
							++$iCantidadEmail;
						}

						sleep(1);
					}
					//también debo de capturar a los que no envío por error
				}

				$response = array(
					'status' => 'success',
					'message' => 'Se envío email ' . $iCantidadEmail
				);
				echo json_encode($response);
				exit();
			} else {
				echo json_encode($arrResponseEntidad);
				exit();
			}
		} else {
			echo json_encode($arrResponse);
			exit();
		}
    	//echo json_encode($this->PedidosCargaConsolidadaModel->sendMessage($this->input->post()));
	}
	
	public function enviarCorreoSeguimiento($arrData){
		// enviar correo con las credenciales
		$this->load->library('email');

		$data_email["email"] = $arrData['email'];
		$data_email["name"] = $arrData['name'];
		$data_email["message"] = $arrData['message'];
		$message_email = $this->load->view('correos/seguimiento_carga_consolidada', $data_email, true);
		
		$this->email->from('noreply@lae.one', 'ProBusiness');//de
		$this->email->to($arrData['email']);//para
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

	public function cambiarEstadoTarea($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosCargaConsolidadaModel->cambiarEstadoTarea($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
}
