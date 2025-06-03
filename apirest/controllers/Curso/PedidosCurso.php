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
        $arrData = [];
        $index = 1;
        $tipoTabla = $this->input->post('tipoTabla');
        $filtro_estado_pago = $this->input->post('estado_pago');
        if ($tipoTabla == "alumnos") {
            $arrData = $this->PedidosCursoModel->get_datatables();
            // FILTRAR POR ESTADO DE PAGO EN PHP
            if ($filtro_estado_pago && $filtro_estado_pago != "0") {
                $arrData = array_filter($arrData, function ($row) use ($filtro_estado_pago) {
                    // Calcula el estado de pago igual que en tu foreach
                    $fecha_hoy = date('Y-m-d');
                    $fecha_inicio = isset($row->Fe_Inicio) ? $row->Fe_Inicio : null;
                    $fecha_fin = isset($row->Fe_Fin) ? $row->Fe_Fin : null;
                    $tipo_curso = isset($row->tipo_curso) ? $row->tipo_curso : null;
                    $estado_pago = 'pendiente';
                    if ($row->total_pagos == 0) {
                        $estado_pago = '<span class="badge bg-secondary">Pendiente</span>';
                    } elseif ($row->total_pagos < $row->Ss_Total) {
                        if (
                            $tipo_curso == 1 &&
                            $fecha_inicio &&
                            (strtotime($fecha_inicio) - strtotime($fecha_hoy)) <= 2 * 86400 &&
                            (strtotime($fecha_inicio) - strtotime($fecha_hoy)) >= 0
                        ) {
                            $estado_pago = '<span class="badge bg-primary">Cobrando</span>';
                        } else {
                            $estado_pago = '<span class="badge bg-warning">Adelanto</span>';
                        }
                    } elseif ($row->total_pagos == $row->Ss_Total) {
                        $estado_pago =  '<span class="badge bg-success">Pagado</span>';
                    } elseif ($row->total_pagos > $row->Ss_Total) {
                        $estado_pago = '<span class="badge bg-danger">Sobrepagado</span>';
                    }
                    if (
                        $tipo_curso == 1 &&
                        $fecha_fin &&
                        strtotime($fecha_hoy) > strtotime($fecha_fin)
                    ) {
                        $estado_pago = '<span class="badge bg-info">Constancia</span>';
                    }
                    return $estado_pago == $filtro_estado_pago;
                });
            }
        } else {
            $arrData = $this->PedidosCursoModel->getPagosCurso();
            log_message('error', 'arrData: ' . print_r($arrData, true));
        }
        $data = array();
        if ($tipoTabla == "alumnos") {
            foreach ($arrData as $row) {
                $rows = array();
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
                $tipo_curso = '<select class="form-control select-tipo-curso" data-id="' . $row->ID_Pedido_Curso . '">';
                $tipo_curso .= '<option value="">Seleccionar</option>';
                $tipo_curso .= '<option value="0" ' . ($row->tipo_curso === "0" || $row->tipo_curso === 0 ? 'selected' : '') . '>Virtual</option>';
                $tipo_curso .= '<option value="1" ' . ($row->tipo_curso === "1" || $row->tipo_curso === 1 ? 'selected' : '') . '>En vivo</option>';
                $tipo_curso .= '</select>';
                $rows[] = $tipo_curso; //tipo curso

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
                $rows[] = $select; //mes
                $select_usuario = '<select class="select-usuario-externo form-control" data-id-usuario="' . $row->ID_Usuario . '" data-id-pedido="' . $row->ID_Pedido_Curso . '">';
                $select_usuario .= '<option value="1"' . ($row->Nu_Estado_Usuario_Externo == 1 ? ' selected' : '') . '>Pendiente</option>';
                $select_usuario .= '<option class="bg-' . $arrEstadoRegistro['No_Class_Estado'] . '" value="2"' . ($row->Nu_Estado_Usuario_Externo == 2 ? ' selected' : '') . '>Creado</option>';
                $select_usuario .= '</select>';
                log_message('error', 'Nu_Estado_Usuario_Externo: ' . print_r($row->Nu_Estado_Usuario, true));
                $rows[] = $select_usuario; //usuario

                $rows[]            = $row->No_Signo . '<input name="importe_pedido" class="w-[50%]" value="' . round($row->Ss_Total, 2) . '" />'; //importe		
                $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroPagosArray($row->Nu_Estado);                             //estado

                $fecha_hoy = date('Y-m-d');
                $fecha_inicio = isset($row->Fe_Inicio) ? $row->Fe_Inicio : null;
                $fecha_fin = isset($row->Fe_Fin) ? $row->Fe_Fin : null;
                $tipo_curso = isset($row->tipo_curso) ? $row->tipo_curso : null; // 1 = En vivo

                    $estado_pago = 'pendiente';
                    if ($row->total_pagos == 0) {
                        $estado_pago = '<span class="badge bg-secondary">Pendiente</span>';
                    } elseif ($row->total_pagos < $row->Ss_Total) {
                        if (
                            $tipo_curso == 1 &&
                            $fecha_inicio &&
                            (strtotime($fecha_inicio) - strtotime($fecha_hoy)) <= 2 * 86400 &&
                            (strtotime($fecha_inicio) - strtotime($fecha_hoy)) >= 0
                        ) {
                            $estado_pago = '<span class="badge bg-primary">Cobrando</span>';
                        } else {
                            $estado_pago = '<span class="badge bg-warning">Adelanto</span>';
                        }
                    } elseif ($row->total_pagos == $row->Ss_Total) {
                        $estado_pago =  '<span class="badge bg-success">Pagado</span>';
                    } elseif ($row->total_pagos > $row->Ss_Total) {
                        $estado_pago = '<span class="badge bg-danger">Sobrepagado</span>';
                    }
                    if (
                        $tipo_curso == 1 &&
                        $fecha_fin &&
                        strtotime($fecha_hoy) > strtotime($fecha_fin)
                    ) {
                        $estado_pago = '<span class="badge bg-info">Constancia</span>';
                    }

                // Luego úsalo para el select:
                $select_estado = '<select class="form-control 
                ' . ($estado_pago == 'pendiente' ? 'bg-secondary' : '') .
                    ($estado_pago == 'adelanto' ? 'bg-warning' : '') . '
                ' . ($estado_pago == 'cobrando' ? 'bg-primary' : '') .
                    ($estado_pago == 'pagado' ? 'bg-success' : '') .
                    ($estado_pago == 'sobrepagado' ? 'bg-danger' : '') .
                    ($estado_pago == 'constancia' ? 'bg-secondary' : '') . '
            select-estado-pago" data-id="' . $row->ID_Pedido_Curso . '">';

                $select_estado .= '<option value="pendiente" class="bg-secondary" ' . ($estado_pago == 'pendiente' ? 'selected' : '') . '>Pendiente</option>';
                $select_estado .= '<option value="adelanto" class="bg-warning"' . ($estado_pago == 'adelanto' ? 'selected' : '') . '>Adelanto</option>';

                // Solo mostrar "Cobrando" y "Constancia" si es EN VIVO
                if ($tipo_curso == 1) {
                    $select_estado .= '<option value="cobrando" class="bg-primary"' . ($estado_pago == 'cobrando' ? 'selected' : '') . '>Cobrando</option>';
                }

                $select_estado .= '<option value="pagado" class="bg-success"' . ($estado_pago == 'pagado' ? 'selected' : '') . '>Pagado</option>';
                $select_estado .= '<option value="sobrepagado" class="bg-danger"' . ($estado_pago == 'sobrepagado' ? 'selected' : '') . '>Sobrepagado</option>';

                if ($tipo_curso == 1) {
                    $select_estado .= '<option value="constancia" class="bg-secondary"' . ($estado_pago == 'constancia' ? 'selected' : '') . '>Constancia</option>';
                }

                $select_estado .= '</select>';
                $rows[] = $estado_pago;

                $divAcciones = '<div>'; //Acciones
                $divAcciones .= '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewCliente(\'' . $row->ID_Pedido_Curso . '\')"></i>';
                $divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="eliminarPedido(\'' . $row->ID_Pedido_Curso . '\')"></i>';

                $divAcciones .= '</div>';
                $rows[] = $divAcciones;

                $data[] = $rows;
            }
        } else {
            foreach ($arrData as $row) {
                $subdata   = array();
                $subdata[] =  $row->ID_Pedido_Curso;
                $subdata[] = allTypeDate($row->Fe_Registro, '-', 0);
                $subdata[] = $row->No_Entidad;
                $subdata[] = $row->No_Tipo_Documento_Identidad_Breve . ": " . $row->Nu_Documento_Identidad;
                $subdata[] = $row->Nu_Celular_Entidad;
                $subdata[] = $row->No_Signo . '<input value="' . round($row->Ss_Total, 2) . '" readonly/>';    //importe		
                $subdata[] = "S/" . round($row->total_pagos, 2);
                //if pagos_count is minor than 4 add button plus to add new payment
                $divAcciones = '<div class="d-flex px-2 w-100" style="gap:1em;">';

                $divAcciones .= '<div class="d-flex"  onclick="addPagosCurso(' . $row->ID_Pedido_Curso . ', \'' . addslashes(trim($row->No_Entidad)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';

                if ($row->pagos_count > 0) {
                    $divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCurso(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')">
						</div>';
                }
                $divAcciones .=  '</div>';
                $subdata[] = $divAcciones;
                $data[] = $subdata;
            }
        }
        $output = array(
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->PedidosCursoModel->count_all(),
            'recordsFiltered' => $this->PedidosCursoModel->count_filtered(),
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function ViewCliente($id_pedido)
    {
        $data = $this->PedidosCursoModel->getDatosClientePorPedido($id_pedido);
        if ($data) {
            // Desencripta la contraseña si existe
            if (isset($data['password_moodle']) && ! empty($data['password_moodle'])) {
                $data['password_moodle'] = $this->encryption->decrypt($data['password_moodle']);
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

                'nu_estado'                   => $data['Nu_Estado'] ?? null,
                'nu_estado_usuario_externo'   => $data['Nu_Estado_Usuario_Externo'] ?? null,
                'id_usuario'                  => $data['usuario_moodle'] ?? null, // o el campo correcto de tu modelo
                'id_pedido_curso'             => $id_pedido,
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
                $message = "Hola, {$result->No_Nombres_Apellidos},\n\n";
                $message .= "Tu cuenta en ProBusiness ha sido creada exitosamente.\n\n";
                $message .= "Usuario: {$result->No_Usuario}\n";
                $message .= "Contraseña: {$this->encryption->decrypt($result->No_Password)}\n\n";
                $message .= "Puedes acceder a tu cuenta en el siguiente enlace: https://probusiness.com.pe/cursos\n\n";
                $message .= "Saludos,\nEl equipo de ProBusiness";
                $this->sendMessageVentas($message, $telefono);
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
    public function asignarTipoCurso()
    {
        $id_pedido = $this->input->post('id_pedido');
        $id_tipo_curso = $this->input->post('id_tipo_curso');
        if ($id_pedido !== null && $id_tipo_curso !== null) {
            $this->db->where('ID_Pedido_Curso', $id_pedido)
                ->update('pedido_curso', ['tipo_curso' => $id_tipo_curso]);
            echo json_encode(['status' => 'success', 'message' => 'Tipo de curso actualizado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        }
    }
    public function asignarEstadoPago()
    {
        $id_pedido = $this->input->post('id_pedido');
        $estado_pago = $this->input->post('estado_pago');
        if ($id_pedido && $estado_pago) {
            $this->db->where('ID_Pedido_Curso', $id_pedido)
                ->update('pedido_curso', ['estado_pago' => $estado_pago]);
            echo json_encode(['status' => 'success', 'message' => 'Estado de pago actualizado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        }
    }
    public function calcularEstadoPago($id_pedido)
    {
        $pedido = $this->getPedido($id_pedido);
        $adelantos = $this->getTotalAdelantos($id_pedido); // suma de pagos
        $importe = $pedido['importe'];

        if ($adelantos == 0) return 'pendiente';
        if ($adelantos < $importe) return 'adelanto';
        if ($adelantos == $importe) return 'pagado';
        if ($adelantos > $importe) return 'sobrepagado';
        // lógica para "cobrando" y "constancia" según fechas y tipo_curso
    }

    public function saveClientePagosCurso()
    {
        $voucher = $_FILES['voucher'];
        $idPedido = $this->input->post('idPedido');
        $amount = $this->input->post('monto');
        $fecha = $this->input->post('fecha');
        $banco = $this->input->post('banco');
        $arrResponse = $this->PedidosCursoModel->saveClientePagosCurso($voucher, $idPedido, $amount, $fecha, $banco);
        echo json_encode([
            "status" => $arrResponse['status'],
            "message" => $arrResponse['message']
        ]);
    }
    public function getCursosHeader()
    {
        try {
            $data = $this->PedidosCursoModel->getCursosHeader();
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            log_message('error', 'Error al obtener cursos: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener cursos']);
        }
    }
}
