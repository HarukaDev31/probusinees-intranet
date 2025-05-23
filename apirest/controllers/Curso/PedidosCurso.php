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

            $rows[] = $row->No_Entidad . "<br>" . $row->No_Tipo_Documento_Identidad_Breve . ": " . $row->Nu_Documento_Identidad . "<br>" . $row->Nu_Celular_Entidad . $sWhatsAppCliente . "<br>" . $row->Txt_Email_Entidad; //cliente
			$btn_compartir = '<button class="btn btn-xs btn-link" alt="Enviar email" title="Enviar email" href="javascript:void(0)"  onclick="enviarEmailUsuarioMoodle(\'' . $row->ID_Usuario . '\', \'' . $row->ID_Pedido_Curso . '\')"><i class="far fa-envelope fa-2x" aria-hidden="true"></i></button>';
			if($row->Nu_Estado==4)
				$btn_compartir ='';
			$rows[] = $btn_compartir; //compartir
			
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoProcesoUsuarioCursoArray($row->Nu_Estado_Usuario_Externo); //estado usuario
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$btn_usuario_moodle ='';
			if($row->Nu_Estado==2 && $row->Nu_Estado_Usuario_Externo!="2")
			$btn_usuario_moodle = '<button class="btn btn-primary" alt="Crear usuario" title="Crear usuario" href="javascript:void(0)"  onclick="crearUsuarioCursosMoodle(\'' . $row->ID_Usuario . '\', \'' . $row->ID_Pedido_Curso . '\')">Crear</button>';
            $rows[] = $row->No_Usuario . "<br>" . $this->encryption->decrypt($row->No_Password) . "<br>" . $btn_usuario_moodle; //moodle

			$rows[] = $row->ID_Referencia_Pago_Online; //rf pago
			$rows[] = $row->No_Signo . '<input value="' . round($row->Ss_Total, 2) . '" readonly/>';	//importe		
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroPagosArray($row->Nu_Estado); //estado
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			
			
			$divAcciones = '<div>';//Acciones
			$divAcciones .= '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewCliente(\'' . $row->ID_Pedido_Curso . '\')"></i>';
			$divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="eliminarPedido(\'' . $row->ID_Pedido_Curso . '\')"></i>';

			$divAcciones .= '</div>';
			$rows[] = $divAcciones;

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
	
	public function ViewCliente($id_pedido) {
		$data = $this->PedidosCursoModel->getDatosClientePorPedido($id_pedido);
		if ($data) {
			// Desencripta la contraseña si existe
			if (isset($data['password_moodle']) && !empty($data['password_moodle'])) {
				$data['password_moodle'] = $this->encryption->decrypt($data['password_moodle']);
			}

			// Sexo: mostrar número y texto
			$sexo_num = $data['sexo'];
			$sexo_text = 'Hombre';
			if ($sexo_num == 2) $sexo_text = 'Mujer';
			else if ($sexo_num == 3) $sexo_text = 'Otros';

			// Red social: mostrar texto descriptivo
			$red_social_num = $data['red_social'];
			$red_social_text = 'Tiktok';
			if ($red_social_num == 2) $red_social_text = 'Facebook';
			else if ($red_social_num == 3) $red_social_text = 'Instagram';
			else if ($red_social_num == 4) $red_social_text = 'Youtube';
			else if ($red_social_num == 5) $red_social_text = 'Familiares/Amigos';
			else if ($red_social_num == 6) $red_social_text = 'LinkedIn';
			else if ($red_social_num == 7) $red_social_text = 'Google';
			else if ($red_social_num == 8) $red_social_text = 'Otros: ' . ($data['No_Otros_Como_Entero_Empresa'] ?? '');

			// Prepara la respuesta
			$response = [
				'nombres' => $data['nombres'],
				'sexo' => $sexo_text,
				'dni' => $data['dni'],
				'red_social' => $red_social_text,
				'correo' => $data['correo'],
				'pais' => $data['pais'],
				'whatsapp' => $data['whatsapp'],
				'departamento' => $data['departamento'],
				'provincia' => $data['provincia'],
				'distrito' => $data['distrito'],
				'nacimiento' => (!empty($data['nacimiento']) ? date('d/m/Y', strtotime($data['nacimiento'])) : ''),
				'usuario_moodle' => $data['usuario_moodle'],
				'password_moodle' => $data['password_moodle'],
			];

			echo json_encode(['status' => 'success', 'data' => $response]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No se encontró el pedido']);
		}
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
				"lastname" => "lastname",
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
