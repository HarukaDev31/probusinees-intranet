<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/libraries/MoodleRestPro.php');

class PedidosCurso extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->library('encryption');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Curso/PedidosCursoModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('Curso/PedidosCursoView');
			$this->load->view('footer_v2', array("js_pedidos_curso" => true));
		}
	}

	public function ajax_list(){
		$sMethod = $this->input->post('sMethod');
		$arrData = $this->PedidosCursoModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Pais;
            $rows[] = $row->ID_Pedido_Curso;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
			
			$sWhatsAppCliente = '';
			if($row->Nu_Estado!=2) {//2=confirmado
				$sCodigoPaisCelular = '51';
				$sMensaje = "Te saluda ProBusiness 👋🏻\n";
				$sMensaje .= "No pudiste completar la compra del curso. ¿Cómo te puedo ayudar?. \n\n";
				$sMensaje = urlencode($sMensaje);
				$sWhatsAppCliente = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular_Entidad . '&text=' . $sMensaje . '" target="_blank"><i class="fab fa-whatsapp" style="color: #25d366;"></i></a>';
			}

            $rows[] = $row->No_Entidad . "<br>" . $row->No_Tipo_Documento_Identidad_Breve . ": " . $row->Nu_Documento_Identidad . "<br>" . $row->Nu_Celular_Entidad . $sWhatsAppCliente . "<br>" . $row->Txt_Email_Entidad;
		
			$rows[] = $row->No_Signo . ' ' . round($row->Ss_Total, 2);
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroPagosArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoProcesoUsuarioCursoArray($row->Nu_Estado_Usuario_Externo);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$btn_usuario_moodle ='';
			if($row->Nu_Estado==2 && $row->Nu_Estado_Usuario_Externo!="2")
				$btn_usuario_moodle = '<button class="btn btn-primary" alt="Crear usuario" title="Crear usuario" href="javascript:void(0)"  onclick="crearUsuarioCursosMoodle(\'' . $row->ID_Usuario . '\', \'' . $row->ID_Pedido_Curso . '\')">Crear</button>';
            $rows[] = $row->No_Usuario . "<br>" . $this->encryption->decrypt($row->No_Password) . "<br>" . $btn_usuario_moodle;

			$rows[] = $row->ID_Referencia_Pago_Online;

			$btn_compartir = '<button class="btn btn-xs btn-link" alt="Enviar email" title="Enviar email" href="javascript:void(0)"  onclick="enviarEmailUsuarioMoodle(\'' . $row->ID_Usuario . '\', \'' . $row->ID_Pedido_Curso . '\')"><i class="far fa-envelope fa-2x" aria-hidden="true"></i></button>';
			if($row->Nu_Estado==4)
				$btn_compartir ='';
			$rows[] = $btn_compartir;

            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->PedidosCursoModel->count_all(),
	        'recordsFiltered' => $this->PedidosCursoModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function crearUsuarioCursosMoodle($id, $ID_Pedido_Curso){
		$id_pedido_curso = $ID_Pedido_Curso;
		//buscar usuario
		$response_usuario_bd = $this->PedidosCursoModel->getUsuario($id);
		if($response_usuario_bd['status']=='success') {
			$result = $response_usuario_bd['result'][0];
			
			//crear usuario y cursos para moodle
			$MoodleRestPro = new MoodleRestPro();
			$arrPost = array(
				'username' => $result->No_Usuario,
				'password' => $this->encryption->decrypt($result->No_Password),
				'firstname' => $result->No_Nombres_Apellidos,
				'email' => $result->No_Usuario,
			);
			$response_usuario_moodle = $MoodleRestPro->createUser($arrPost);

			if($response_usuario_moodle['status']=='success'){
				// Property added to the object
				$arrParams['criteria'][0]['key']='username';
				$arrParams['criteria'][0]['value']=$result->No_Usuario;
				$response_usuario = $MoodleRestPro->getUser($arrParams);
			
				if($response_usuario['status']=='success'){
					$result_usuario = $response_usuario['response'];
				
					$id_usuario = $result_usuario->id;
					$arrParamsCurso = array(
						'id_usuario' => $id_usuario//id_usuario
					);
					$response_curso = $MoodleRestPro->crearCursoUsuario($arrParamsCurso);
					if($response_curso['status']!='success'){
						$where = array('ID_Pedido_Curso' => $id_pedido_curso);
						$data_upd = array('Nu_Estado_Usuario_Externo' => '3');//usuario no creado en moodle
						$this->PedidosCursoModel->actualizarPedido($where, $data_upd);
						
						echo json_encode($response_curso);
						exit();
					} else {
						$where = array('ID_Pedido_Curso' => $id_pedido_curso);
						$data_upd = array('Nu_Estado_Usuario_Externo' => '2');//usuario creado
						$this->PedidosCursoModel->actualizarPedido($where, $data_upd);

						echo json_encode($response_curso);
						exit();
					}
				} else {
					$where = array('ID_Pedido_Curso' => $id_pedido_curso);
					$data_upd = array('Nu_Estado_Usuario_Externo' => '3');//usuario no creado en moodle
					$this->PedidosCursoModel->actualizarPedido($where, $data_upd);

					echo json_encode($response_usuario);
					exit();
				}
			} else {
				$where = array('ID_Pedido_Curso' => $id_pedido_curso);
				$data_upd = array('Nu_Estado_Usuario_Externo' => '3');//usuario no creado en moodle
				$this->PedidosCursoModel->actualizarPedido($where, $data_upd);
				
				echo json_encode($response_usuario_moodle);
				exit();
			}
		} else {
			echo json_encode($response_usuario_bd);
			exit();
		}
	}
	
	public function enviarEmailUsuarioMoodle($id, $ID_Pedido_Curso){
		$id_pedido_curso = $ID_Pedido_Curso;
		//buscar usuario
		$response_usuario_bd = $this->PedidosCursoModel->getUsuario($id);
		if($response_usuario_bd['status']=='success') {
			$result = $response_usuario_bd['result'][0];

			// enviar correo con las credenciales
			$this->load->library('email');

			$data_email["email"] = $result->No_Usuario;
			$data_email["password"] = $this->encryption->decrypt($result->No_Password);
			$data_email["name"] = $result->No_Nombres_Apellidos;
			$message_email = $this->load->view('correos/cuenta_moodle', $data_email, true);
			
			$this->email->from('noreply@lae.one', 'ProBusiness');//de
			$this->email->to($result->No_Usuario);//para
			$this->email->subject('🎉 Bienvenido al curso');
			$this->email->message($message_email);
			$this->email->set_newline("\r\n");

			$isSend = $this->email->send();
			if($isSend) {
				$response = array(
					'status' => 'success',
					'message' => 'Se envío email'
				);
				echo json_encode($response);
				exit();
			} else {
				$response = array(
					'status' => 'error',
					'message' => 'No se pudo enviar email, inténtelo más tarde.',
					'error_message_mail' => $this->email->print_debugger()
				);
				echo json_encode($response);
				exit();
			}
		} else {
			echo json_encode($response_usuario_bd);
			exit();
		}
	}
}
