<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/MoodleRestPro.php';
require_once APPPATH . 'traits/WhatsappTrait.php';

class PedidosCurso extends CI_Controller
{
	use WhatsappTrait;

    private $upload_path                             = '../assets/images/clientes/';
    private $file_path                               = '../assets/images/logos/';
    private $logo_cliente_path                       = '../assets/images/logos/';
    private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('encryption');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('Curso/PedidosCursoModel');
        $this->load->model('HelperImportacionModel');
        if (! isset($this->session->userdata['usuario'])) {
            redirect('');
        }
    }

    public function listar()
    {
        if (! $this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        if (isset($this->session->userdata['usuario'])) {
            $this->load->view('header_v2');
            $this->load->view('Curso/PedidosCursoView');
            $this->load->view('footer_v2', ["js_pedidos_curso" => true]);
        }
    }

    public function ajax_list()
    {
        $sMethod = $this->input->post('sMethod');
        $arrData = $this->PedidosCursoModel->get_datatables();
        $data    = [];
        foreach ($arrData as $row) {
            $rows = [];

            $rows[] = $row->ID_Pedido_Curso;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);

            $sWhatsAppCliente = '';
            if ($row->Nu_Estado != 2) { //2=confirmado
                $sCodigoPaisCelular = '51';
                $sMensaje           = "Te saluda ProBusiness 👋🏻\n";
                $sMensaje .= "No pudiste completar la compra del curso. ¿Cómo te puedo ayudar?. \n\n";
                $sMensaje         = urlencode($sMensaje);
                $sWhatsAppCliente = ' <a href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular_Entidad . '&text=' . $sMensaje . '" target="_blank"><i class="fab fa-whatsapp" style="color: #25d366;"></i></a>';
            }

            $rows[]   = $row->No_Entidad . "<br>" . $row->No_Tipo_Documento_Identidad_Breve . ": " . $row->Nu_Documento_Identidad . "<br>" . $row->Nu_Celular_Entidad . $sWhatsAppCliente . "<br>" . $row->Txt_Email_Entidad; //cliente
            $campanas = $this->PedidosCursoModel->getCampanasActivas();
            // Armar el select
            $select = '<select name="ID_Campana" class="form-control">';
            if (empty($row->ID_Campana)) {
                $select .= '<option value="">Seleccionar</option>';
            }
            foreach ($campanas as $campana) {
                $selected = ($row->ID_Campana == $campana['ID_Campana']) ? 'selected' : '';
                $select .= '<option value="' . $campana['ID_Campana'] . '" ' . $selected . '>' . $campana['nombre_campana'] . '</option>';
            }
            $select .= '</select>';
            $rows[]         = $select;
            $select_usuario = '<select class="select-usuario-externo form-control" data-id-usuario="' . $row->ID_Usuario . '" data-id-pedido="' . $row->ID_Pedido_Curso . '">';
            $select_usuario .= '<option value="1"' . ($row->Nu_Estado_Usuario_Externo == 1 ? ' selected' : '') . '>Pendiente</option>';
            $select_usuario .= '<option class="bg-' . $arrEstadoRegistro['No_Class_Estado'] . '" value="2"' . ($row->Nu_Estado_Usuario_Externo == 2 ? ' selected' : '') . '>Creado</option>';
            $select_usuario .= '</select>';
            $rows[] = $select_usuario;

            $btn_usuario_moodle = '';
            if ($row->Nu_Estado == 2 && $row->Nu_Estado_Usuario_Externo != "2") {
                $btn_usuario_moodle = '<button class="btn btn-primary" alt="Crear usuario" title="Crear usuario" href="javascript:void(0)"  onclick="crearUsuarioCursosMoodle(\'' . $row->ID_Usuario . '\', \'' . $row->ID_Pedido_Curso . '\')">Crear</button>';
            }
                                                                                                                                //usuario
            $rows[] = $row->No_Usuario . "<br>" . $this->encryption->decrypt($row->No_Password) . "<br>" . $btn_usuario_moodle; //moodle

            $rows[]            = $row->ID_Referencia_Pago_Online;                                                                             //rf pago
            $rows[]            = $row->No_Signo . '<input name="importe_pedido" class="w-[50%]" value="' . round($row->Ss_Total, 2) . '" />'; //importe		
            $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroPagosArray($row->Nu_Estado);                             //estado
            $rows[]            = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

            $divAcciones = '<div>'; //Acciones
            $divAcciones .= '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewCliente(\'' . $row->ID_Pedido_Curso . '\')"></i>';
            $divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="eliminarPedido(\'' . $row->ID_Pedido_Curso . '\')"></i>';

            $divAcciones .= '</div>';
            $rows[] = $divAcciones;

            $data[] = $rows;
        }
        $output = [
            'draw'            => $this->input->post('draw'),
            'recordsTotal'    => $this->PedidosCursoModel->count_all(),
            'recordsFiltered' => $this->PedidosCursoModel->count_filtered(),
            'data'            => $data,
        ];
        echo json_encode($output);
    }

    public function ViewCliente($id_pedido)
    {
        $data = $this->PedidosCursoModel->getDatosClientePorPedido($id_pedido);
        if ($data) {
            // Desencripta la contraseña si existe
            if (isset($data['password_moodle']) && ! empty($data['password_moodle'])) {
                $data['password_moodle'] = $this->encryption->decrypt($data['password_moodle']);
            }

            // Sexo: mostrar número y texto
            $sexo_num  = $data['sexo'];
            $sexo_text = 'Hombre';
            if ($sexo_num == 2) {
                $sexo_text = 'Mujer';
            } else if ($sexo_num == 3) {
                $sexo_text = 'Otros';
            }

            // Red social: mostrar texto descriptivo
            $red_social_num  = $data['red_social'];
            $red_social_text = 'Tiktok';
            if ($red_social_num == 2) {
                $red_social_text = 'Facebook';
            } else if ($red_social_num == 3) {
                $red_social_text = 'Instagram';
            } else if ($red_social_num == 4) {
                $red_social_text = 'Youtube';
            } else if ($red_social_num == 5) {
                $red_social_text = 'Familiares/Amigos';
            } else if ($red_social_num == 6) {
                $red_social_text = 'LinkedIn';
            } else if ($red_social_num == 7) {
                $red_social_text = 'Google';
            } else if ($red_social_num == 8) {
                $red_social_text = 'Otros: ' . ($data['No_Otros_Como_Entero_Empresa'] ?? '');
            }

            // Prepara la respuesta
            $response = [
                'id_entidad'      => $data['id_entidad'],
                'nombres'         => $data['nombres'],
                'sexo'            => $sexo_text,
                'dni'             => $data['dni'],
                'red_social'      => $red_social_text,
                'correo'          => $data['correo'],
                'pais'            => $data['pais'],
                'whatsapp'        => $data['whatsapp'],
                'departamento'    => $data['departamento'],
                'provincia'       => $data['provincia'],
                'distrito'        => $data['distrito'],
                'nacimiento'      => (! empty($data['nacimiento']) ? date('Y-m-d', strtotime($data['nacimiento'])) : ''),
                'usuario_moodle'  => $data['usuario_moodle'],
                'password_moodle' => $data['password_moodle'],
                'id_pais'         => $data['id_pais'],
                'id_departamento' => $data['id_departamento'],
                'id_provincia'    => $data['id_provincia'],
                'id_distrito'     => $data['id_distrito'],
            ];

            echo json_encode(['status' => 'success', 'data' => $response]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró el pedido']);
        }
    }
    public function crearUsuarioCursosMoodle($id, $ID_Pedido_Curso)
    {
        $id_pedido_curso = $ID_Pedido_Curso;
        //buscar usuario
        $response_usuario_bd = $this->PedidosCursoModel->getUsuario($id);
        if ($response_usuario_bd['status'] == 'success') {
            $result = $response_usuario_bd['result'][0];

            //crear usuario y cursos para moodle
            $MoodleRestPro = new MoodleRestPro();
            $arrPost       = [
                'username'  => $result->No_Usuario,
                'password'  => $this->encryption->decrypt($result->No_Password),
                'firstname' => $result->No_Nombres_Apellidos,
                "lastname"  => "lastname",
                'email'     => $result->No_Usuario,
            ];

            $response_usuario_moodle = $MoodleRestPro->createUser($arrPost);
            if ($response_usuario_moodle['status'] == 'success') {
                // Property added to the object
                $arrParams['criteria'][0]['key']   = 'username';
                $arrParams['criteria'][0]['value'] = $result->No_Usuario;
                $response_usuario                  = $MoodleRestPro->getUser($arrParams);

                if ($response_usuario['status'] == 'success') {
                    $result_usuario = $response_usuario['response'];

                    $id_usuario     = $result_usuario->id;
                    $arrParamsCurso = [
                        'id_usuario' => $id_usuario, //id_usuario
                    ];
                    $response_curso = $MoodleRestPro->crearCursoUsuario($arrParamsCurso);
                    if ($response_curso['status'] != 'success') {
                        $where    = ['ID_Pedido_Curso' => $id_pedido_curso];
                        $data_upd = ['Nu_Estado_Usuario_Externo' => '3']; //usuario no creado en moodle
                        $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                        echo json_encode($response_curso);
                        exit();
                    } else {
                        $where    = ['ID_Pedido_Curso' => $id_pedido_curso];
                        $data_upd = ['Nu_Estado_Usuario_Externo' => '2']; //usuario creado
                        $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                        echo json_encode($response_curso);
                        exit();
                    }
                } else {
                    $where    = ['ID_Pedido_Curso' => $id_pedido_curso];
                    $data_upd = ['Nu_Estado_Usuario_Externo' => '3']; //usuario no creado en moodle
                    $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                    echo json_encode($response_usuario);
                    exit();
                }
            } else {
                $where    = ['ID_Pedido_Curso' => $id_pedido_curso];
                $data_upd = ['Nu_Estado_Usuario_Externo' => '3']; //usuario no creado en moodle
                $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                echo json_encode($response_usuario_moodle);
                exit();
            }
        } else {
            echo json_encode($response_usuario_bd);
            exit();
        }
    }

    public function enviarEmailUsuarioMoodle($id, $ID_Pedido_Curso)
    {
        $id_pedido_curso = $ID_Pedido_Curso;
        //buscar usuario
        $response_usuario_bd = $this->PedidosCursoModel->getUsuario($id);
        if ($response_usuario_bd['status'] == 'success') {
            $result = $response_usuario_bd['result'][0];
            // enviar correo con las credenciales
            $entidad = $this->PedidosCursoModel->getEntidadByIdPedido($id_pedido_curso);
            if ($entidad) {
                $idEntidad = $entidad->ID_Entidad;
                $idPais    = $entidad->ID_Pais;
                $telefono  = $entidad->Nu_Celular_Entidad;
                $telefono  = preg_replace('/\s+/', '', $telefono);
                $prefijoPais = $this->PedidosCursoModel->getPrefijoPais($idPais);
                if ($prefijoPais) {

                    $telefono = $prefijoPais->Nu_Prefijo . $telefono . "@c.us";
                } else {
                    $telefono = '51' . $telefono; // Default to Peru if no prefix found
                }
				$message= "Hola, {$result->No_Nombres_Apellidos},\n\n";
				$message .= "Tu cuenta en ProBusiness ha sido creada exitosamente.\n\n";
				$message .= "Usuario: {$result->No_Usuario}\n";
				$message .= "Contraseña: {$this->encryption->decrypt($result->No_Password)}\n\n";
				$message .= "Puedes acceder a tu cuenta en el siguiente enlace: https://probusiness.com.pe/cursos\n\n";
				$message .= "Saludos,\nEl equipo de ProBusiness";
				$this->sendMessage($message, $telefono);
			} else {
				$response = [
					'status'  => 'error',
					'message' => 'No se encontró la entidad asociada al pedido.',
				];
				echo json_encode($response);
				exit();
            }
            $this->load->library('email');

            $data_email["email"]    = $result->No_Usuario;
            $data_email["password"] = $this->encryption->decrypt($result->No_Password);
            $data_email["name"]     = $result->No_Nombres_Apellidos;
            $message_email          = $this->load->view('correos/cuenta_moodle', $data_email, true);

            $this->email->from('noreply@lae.one', 'ProBusiness'); //de
            $this->email->to($result->No_Usuario);                //para
            $this->email->subject('🎉 Bienvenido al curso');
            $this->email->message($message_email);
            $this->email->set_newline("\r\n");

            $isSend = $this->email->send();
            if ($isSend) {
                $response = [
                    'status'  => 'success',
                    'message' => 'Se envío email',
                ];
                echo json_encode($response);
                exit();
            } else {
                $response = [
                    'status'             => 'error',
                    'message'            => 'No se pudo enviar email, inténtelo más tarde.',
                    'error_message_mail' => $this->email->print_debugger(),
                ];
                echo json_encode($response);
                exit();
            }
        } else {
            echo json_encode($response_usuario_bd);
            exit();
        }
    }
    public function actualizarDatosCliente()
    {
        $id_entidad = $this->input->post('ID_Entidad');
        $data       = [
            'No_Entidad'             => $this->input->post('No_Entidad'),
            'Nu_Documento_Identidad' => $this->input->post('Nu_Documento_Identidad'),
            'Nu_Tipo_Sexo'           => $this->input->post('Nu_Tipo_Sexo'),
            'Nu_Como_Entero_Empresa' => $this->input->post('Nu_Como_Entero_Empresa'),
            'Txt_Email_Entidad'      => $this->input->post('Txt_Email_Entidad'),
            'ID_Pais'                => $this->input->post('ID_Pais'),
            'Nu_Celular_Entidad'     => $this->input->post('Nu_Celular_Entidad'),
            'ID_Departamento'        => $this->input->post('ID_Departamento'),
            'Fe_Nacimiento'          => $this->input->post('Fe_Nacimiento'),
            'ID_Provincia'           => $this->input->post('ID_Provincia'),
            'ID_Distrito'            => $this->input->post('ID_Distrito'),
        ];
        log_message('error', 'ID_Entidad: ' . print_r($id_entidad, true));
        log_message('error', 'DATA: ' . print_r($data, true));
        $result = $this->PedidosCursoModel->actualizarDatosCliente($id_entidad, $data);
        echo json_encode($result);
    }
    public function getCampanasTabla()
    {
        $sMethod = $this->input->post('sMethod');
        $arrData = $this->PedidosCursoModel->getCampanas();
        $data    = [];
        foreach ($arrData as $row) {
            $rows        = [];
            $rows[]      = $row['ID_Campana'];
            $rows[]      = $row['Fe_Creacion'];
            $rows[]      = $row['No_Campana'];
            $rows[]      = allTypeDate($row['Fe_Inicio'], '-', 0);
            $rows[]      = allTypeDate($row['Fe_Fin'], '-', 0);
            $rows[]      = $row['cantidad_personas'] ?? $row['Cantidad'];
            $divAcciones = '<div>';
            $divAcciones .= '<i class="fas fa-edit text-warning view-eye" style="cursor:pointer; padding:10px;" onclick="editarCampana(\'' . $row['ID_Campana'] . '\')"></i>';
            $divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="borrarCampana(\'' . $row['ID_Campana'] . '\')"></i>';
            $divAcciones .= '</div>';
            $rows[] = $divAcciones;
            $data[] = $rows;
        }
        $output = [
            'draw' => $this->input->post('draw'),

            'data' => $data,
        ];
        echo json_encode($output);
    }

    public function crearCampana()
    {
        $result = $this->PedidosCursoModel->crearCampana($this->input->post('Fe_Inicio'), $this->input->post('Fe_Fin'));
        echo json_encode($result);
    }
    public function editarCampana()
    {
        $id        = $this->input->post('ID_Campana');
        $fe_inicio = $this->input->post('Fe_Inicio');
        $fe_fin    = $this->input->post('Fe_Fin');
        $result    = $this->PedidosCursoModel->editarCampana($id, $fe_inicio, $fe_fin);
        echo json_encode($result);
    }
    public function getCampanaById()
    {
        $id  = $this->input->post('ID_Campana');
        $row = $this->PedidosCursoModel->getCampanaById($id);
        if ($row) {
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No encontrada']);
        }
    }

    public function borrarCampana()
    {
        $id     = $this->input->post('ID_Campana');
        $result = $this->PedidosCursoModel->borrarCampana($id);
        echo json_encode($result);
    }
    public function getCampanasSelect()
    {
        $data = $this->PedidosCursoModel->getCampanasActivas();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    public function asignarCampanaPedido()
    {
        $id_pedido  = $this->input->post('ID_Pedido_Curso');
        $id_campana = $this->input->post('ID_Campana');
        $result     = $this->PedidosCursoModel->asignarCampanaPedido($id_pedido, $id_campana);
        echo json_encode($result);
    }
    public function actualizarImportePedido()
    {
        $id_pedido = $this->input->post('ID_Pedido_Curso');
        $importe   = $this->input->post('importe');
        // Validar en backend también
        if (! preg_match('/^\d+(\.\d{1,2})?$/', $importe)) {
            echo json_encode(['status' => 'error', 'message' => 'Importe inválido']);
            return;
        }
        $result = $this->PedidosCursoModel->actualizarImportePedido($id_pedido, $importe);
        echo json_encode($result);
    }

}
