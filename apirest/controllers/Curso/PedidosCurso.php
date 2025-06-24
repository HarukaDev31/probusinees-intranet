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
        if ($tipoTabla == "alumnos") {
            $arrData = $this->PedidosCursoModel->get_datatables();
        } else {
            $arrData = $this->PedidosCursoModel->getPagosCurso();
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
                //if input campana !="0"

                $select .= '</select>';
                if ($this->input->post('campana') != "0" && !empty($this->input->post('campana'))) {
                    if ($row->ID_Campana != $this->input->post('campana')) {
                        continue;
                    }
                }
                $rows[] = $select; //mes
                $select_usuario = '<select class="select-usuario-externo form-control" data-id-usuario="' . $row->ID_Usuario . '" data-id-pedido="' . $row->ID_Pedido_Curso . '">';
                $select_usuario .= '<option value="1"' . ($row->Nu_Estado_Usuario_Externo == 1 ? ' selected' : '') . '>Pendiente</option>';
                $select_usuario .= '<option class="bg-' . $arrEstadoRegistro['No_Class_Estado'] . '" value="2"' . ($row->Nu_Estado_Usuario_Externo == 2 ? ' selected' : '') . '>Creado</option>';
                $select_usuario .= '</select>';
                log_message('error', 'Nu_Estado_Usuario_Externo: ' . print_r($row->Nu_Estado_Usuario, true));
                $rows[] = $select_usuario; //usuario

                $rows[]            = '
                <div class="flex items-center">
                <span class="px-3 bg-gray-100">$</span>
                <input name="importe_pedido" class="w-[50%] form-control"
                id="importe_pedido_' . $row->ID_Pedido_Curso . '" type="text"
                value="' . number_format($row->Ss_Total, 2) . '" /> </div>'; //importe		
                $arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroPagosArray($row->Nu_Estado);                             //estado

                $fecha_hoy = date('Y-m-d');
                $fecha_fin = isset($row->Fe_Fin) ? $row->Fe_Fin : null;
                $tipo_curso = isset($row->tipo_curso) ? $row->tipo_curso : null; // 1 = En vivo

                $estado_pago = '<select disabled style="padding:0.8em 0.5em;border-radius:0.5em; width:100%;font-size:1.2em;text-align:center"';
                $estado = 'pendiente';
                if ($row->total_pagos == 0) {
                    $estado_pago .= 'class="bg-[#7E7E7E] select-estado-pago text-white">';
                    $estado_pago .= '<option class=" bg-[#7E7E7E]">Pendiente</option>';
                } elseif ($row->total_pagos < $row->Ss_Total && $row->total_pagos > 0) {
                    $estado = 'adelanto';
                    $estado_pago .= 'class="bg-[#FFDC3F] select-estado-pago text-black">';
                    $estado_pago .= '<option class="bg-[#FFDC3F]">Adelanto</option>';
                } elseif ($row->total_pagos == $row->Ss_Total) {
                    $estado = 'pagado';
                    $estado_pago .= 'class="bg-[#2BB348] select-estado-pago text-white">';
                    $estado_pago .=  '<option class="bg-[#2BB348]">Pagado</option>';
                } elseif ($row->total_pagos > $row->Ss_Total) {
                    $estado = 'sobrepagado';
                    $estado_pago .= 'class="bg-[#FF742B] select-estado-pago text-white">';
                    $estado_pago .= '<option class="bg-[#FF742B]">Sobrepagado</option>';
                }
                if (!empty($this->input->post('estado_pago')) && $this->input->post('estado_pago') != '0') {
                    $estadoFiltro = $this->input->post('estado_pago');
                    if ($estado !== $estadoFiltro) {
                        continue; // Usar continue en lugar de return
                    }
                }
                if (
                    $tipo_curso == 1 &&
                    $fecha_fin &&
                    strtotime($fecha_hoy) > strtotime($fecha_fin)
                ) {
                    $estado_pago .= 'class="bg-[#1E6CDB] select-estado-pago text-white">';
                    $estado_pago .= '<option class="bg-[#1E6CDB]">Constancia</option>';
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

                $divAcciones = '<div class="flex flex-row gap-2 align-items-center justify-content-center">'; //Acciones

                $divAcciones .= '<svg 
                class="cursor-pointer"
                width="30" height="23" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="viewCliente(\'' . $row->ID_Pedido_Curso . '\')">
                <path d="M1 6.45455C1 6.45455 3.72727 1 8.5 1C13.2727 1 16 6.45455 16 6.45455C16 6.45455 13.2727 11.9091 8.5 11.9091C3.72727 11.9091 1 6.45455 1 6.45455Z" stroke="#7E7E7E" stroke-width="1.09091" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.50004 8.50107C9.62972 8.50107 10.5455 7.58528 10.5455 6.45561C10.5455 5.32594 9.62972 4.41016 8.50004 4.41016C7.37037 4.41016 6.45459 5.32594 6.45459 6.45561C6.45459 7.58528 7.37037 8.50107 8.50004 8.50107Z" stroke="#7E7E7E" stroke-width="1.09091" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                ';
                $divAcciones .= '<svg 
                class="cursor-pointer"
                width="30" height="23" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="eliminarPedido(\'' . $row->ID_Pedido_Curso . '\')"">
                <path d="M1 3.32031H2.16H11.44" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.2802 3.32V11.44C10.2802 11.7477 10.1579 12.0427 9.9404 12.2602C9.72286 12.4778 9.42781 12.6 9.12016 12.6H3.32016C3.01251 12.6 2.71745 12.4778 2.49991 12.2602C2.28237 12.0427 2.16016 11.7477 2.16016 11.44V3.32M3.90016 3.32V2.16C3.90016 1.85235 4.02237 1.5573 4.23991 1.33976C4.45745 1.12221 4.75251 1 5.06016 1H7.38016C7.68781 1 7.98286 1.12221 8.2004 1.33976C8.41794 1.5573 8.54016 1.85235 8.54016 2.16V3.32" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5.06006 6.21875V9.69875" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.37988 6.21875V9.69875" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
                $divAcciones .= '<svg
                class="cursor-pointer"
                width="30" height="23" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="guardarCambiosPedido(\'' . $row->ID_Pedido_Curso . '\')">
                <path d="M12.7986 3.05333L9.67446 0.426667C9.35013 0.153333 8.93314 0 8.50952 0H2.3671C1.36101 0 0.546875 0.82 0.546875 1.83333V11.1667C0.546875 12.18 1.36101 13 2.3671 13H11.6337C12.6398 13 13.4539 12.18 13.4539 11.1667V4.46667C13.4539 3.92 13.2156 3.40667 12.7986 3.06V3.05333ZM7.82776 1.00667V3.17333C7.82776 3.63333 7.4571 4.00667 7.00039 4.00667H4.35279C3.89608 4.00667 3.52542 3.63333 3.52542 3.17333V1.00667H7.82776ZM3.52542 12.0067V9.17333C3.52542 8.71333 3.89608 8.34 4.35279 8.34H9.64799C10.1047 8.34 10.4754 8.71333 10.4754 9.17333V12.0067H3.52542ZM12.4611 11.1733C12.4611 11.6333 12.0904 12.0067 11.6337 12.0067H11.4682V9.17333C11.4682 8.16 10.6541 7.34 9.64799 7.34H4.35279C3.34671 7.34 2.53257 8.16 2.53257 9.17333V12.0067H2.3671C1.91039 12.0067 1.53972 11.6333 1.53972 11.1733V1.84C1.53972 1.38 1.91039 1.00667 2.3671 1.00667H2.53257V3.17333C2.53257 4.18667 3.34671 5.00667 4.35279 5.00667H7.00039C8.00648 5.00667 8.82061 4.18667 8.82061 3.17333V1.07333C8.90004 1.10667 8.97285 1.14667 9.03904 1.2L12.1632 3.82667C12.3552 3.98667 12.4611 4.22 12.4611 4.46667V11.1733Z" fill="green"/>
                </svg>';
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
                $subdata[] = $row->No_Signo . '<input value="' . round($row->Ss_Total, 2) . '" class="w-75" readonly/>';    //importe		
                $subdata[] = "S/" . round($row->total_pagos, 2);
                //if pagos_count is minor than 4 add button plus to add new payment
                $divAcciones = '<div class="nav gap-1">';
                $pagos_details = json_decode($row->pagos_details, true)?? [];
                foreach ($pagos_details as $pago) {
                    $divAcciones .= '<button
                    onclick="showPago('.$pago['monto'].', \'' . $pago['concepto'] . '\', \'' . $pago['payment_date'] . '\', \'' . $pago['voucher_url'] . '\', \'' . $pago['id_pago'] . '\')"
                    class="nav-link p-2 rounded-lg bg' . $this->getColorTabByStatus($pago['status']) . '">S/' . $pago['monto'] . '</button>';
                }
                $divAcciones .= '<div class="d-flex"  onclick="abrirModalPagoCurso(' . $row->ID_Pedido_Curso . ', \'' . addslashes(trim($row->No_Entidad)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';
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
    public function getColorTabByStatus($status)
    {
        switch ($status) {
            case 'PENDIENTE':
                return '-[#585858] text-white';
            case 'ADELANTO':
                return '-warning';
            case 'CONFIRMADO':
                return '-[#00D680]';
            case 'OBSERVADO':
                return '-[#D71009] text-white';
            default:
                return '-secondary';
        }
    }
    public function eliminarPedido($idPedido)
    {
        $response = $this->PedidosCursoModel->eliminarPedido($idPedido);
        echo json_encode($response);
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
                'id_usuario'                  => $data['id_usuario'] ?? null, // o el campo correcto de tu modelo
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
            log_message('error', 'result: ' . print_r($result, true));

            // Validar y limpiar datos antes de enviar a Moodle
            $original_username = trim($result->No_Nombres_Apellidos);
            $password = $this->encryption->decrypt($result->No_Password);
            if ($password === false) {
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-=+;:,.?';
                $length = 12;
                $password = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomIndex = ord(random_bytes(1)) % strlen($chars);
                    $password .= $chars[$randomIndex];
                }
            }
            $nombres = trim($result->No_Nombres_Apellidos);
            $email = trim($result->No_Usuario);
            log_message('error', 'Datos del usuario: ' . json_encode(
                [
                    'original_username' => $original_username,
                    'password' => $password,
                    'nombres' => $nombres,
                    'email' => $email,
                ]
            ));
            // Validaciones básicas
            if (empty($original_username) || empty($password) || empty($nombres)) {
                $response_error = [
                    'status' => 'error',
                    'message' => 'Datos de usuario incompletos'
                ];
                echo json_encode($response_error);
                exit();
            }

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response_error = [
                    'status' => 'error',
                    'message' => 'Formato de email inválido: ' . $email
                ];
                echo json_encode($response_error);
                exit();
            }

            // ============ DEBUG: Probar con datos mínimos y seguros ============
            // Crear username más seguro (solo letras y números)
            $username = $this->generateSafeUsername($original_username);

            // Crear contraseña más segura (solo letras y números)
            $cleaned_password = $this->generateSafePassword($password);

            // Separar nombres y apellidos con más validación
            $nombres_array = explode(' ', $nombres);
            $firstname = $this->cleanString(isset($nombres_array[0]) ? trim($nombres_array[0]) : 'Usuario');
            $lastname = $this->cleanString(isset($nombres_array[1]) ? trim(implode(' ', array_slice($nombres_array, 1))) : 'Apellido');

            // Si no hay apellido válido, usar algo simple
            if (empty($lastname) || strlen($lastname) < 2) {
                $lastname = 'Usuario';
            }

            // Asegurar longitudes mínimas y máximas
            $username = $this->validateLength($username, 3, 20);
            $firstname = $this->validateLength($firstname, 2, 50);
            $lastname = $this->validateLength($lastname, 2, 50);

            // ============ PROBAR CON DATOS ULTRA SIMPLES PRIMERO ============
            $arrPost = [
                'username'     => $username,
                'password'     => $cleaned_password,
                'firstname'    => $firstname,
                'lastname'     => $lastname,
                'email'        => $email,
                'auth'         => 'manual',
                'lang'         => 'es',
            ];

            // Log detallado para debug
            log_message('error', 'Datos limpiados para Moodle: ' . json_encode($arrPost));

            // Verificar cada campo individualmente
            $this->validateMoodleFields($arrPost);

            //crear usuario y cursos para moodle
            $MoodleRestPro = new MoodleRestPro();
            $response_usuario_moodle = $MoodleRestPro->createUser($arrPost);

            // Log de respuesta de Moodle
            log_message('error', 'Respuesta de Moodle: ' . json_encode($response_usuario_moodle));

            if ($response_usuario_moodle['status'] == 'success') {
                // Buscar el usuario creado usando el nuevo username
                $arrParams['criteria'][0]['key']   = 'username';
                $arrParams['criteria'][0]['value'] = $username;
                //set No_Usuario to  $username
                $this->PedidosCursoModel->setUsuarioModdle(
                    $username,
                    $this->encryption->encrypt($cleaned_password),
                    $id
                );
                $response_usuario = $MoodleRestPro->getUser($arrParams);

                if ($response_usuario['status'] == 'success') {
                    $result_usuario = $response_usuario['response'];
                    $id_usuario = $result_usuario->id;

                    $arrParamsCurso = [
                        'id_usuario' => $id_usuario,
                    ];

                    $response_curso = $MoodleRestPro->crearCursoUsuario($arrParamsCurso);

                    if ($response_curso['status'] != 'success') {
                        $where = ['ID_Pedido_Curso' => $id_pedido_curso];
                        $data_upd = ['Nu_Estado_Usuario_Externo' => '3'];
                        $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                        $response_error = [
                            'status' => 'error',
                            'message' => 'Usuario creado pero error al asignar curso: ' . ($response_curso['message'] ?? 'Error desconocido')
                        ];
                        echo json_encode($response_error);
                        exit();
                    } else {
                        $where = ['ID_Pedido_Curso' => $id_pedido_curso];
                        $data_upd = ['Nu_Estado_Usuario_Externo' => '2'];
                        $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                        $response_success = [
                            'status' => 'success',
                            'message' => 'Usuario y curso creados exitosamente',
                            'data' => [
                                'original_username' => $original_username,
                                'moodle_username' => $username,
                                'moodle_id' => $id_usuario,
                            ]
                        ];
                        echo json_encode($response_success);
                        exit();
                    }
                } else {
                    $where = ['ID_Pedido_Curso' => $id_pedido_curso];
                    $data_upd = ['Nu_Estado_Usuario_Externo' => '3'];
                    $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                    $response_error = [
                        'status' => 'error',
                        'message' => 'Usuario creado pero no se pudo recuperar: ' . ($response_usuario['message'] ?? 'Error desconocido')
                    ];
                    echo json_encode($response_error);
                    exit();
                }
            } else {
                $where = ['ID_Pedido_Curso' => $id_pedido_curso];
                $data_upd = ['Nu_Estado_Usuario_Externo' => '2'];
                $this->PedidosCursoModel->actualizarPedido($where, $data_upd);

                $error_message = 'Error al crear usuario en Moodle';
                if (isset($response_usuario_moodle['message'])) {
                    $error_message .= ': ' . $response_usuario_moodle['message'];
                }
                $arrPost = [];
                //get no_usuario and no_password from response_usuario_bd
                $arrPost['No_Usuario'] = $result->usuario_moodle == "" ? $result->No_Usuario : $result->usuario_moodle;
                $arrPost['No_Password'] = $this->encryption->decrypt($result->No_Password);

                $response_error = [
                    'status' => 'error',
                    'message' => "El usuario ya existe en Moodle o hubo un error al crearlo: $error_message",
                    'debug_data' => $arrPost,
                    'validation_results' => $this->getValidationResults($arrPost)
                ];
                echo json_encode($response_error);
                exit();
            }
        } else {
            $response_error = [
                'status' => 'error',
                'message' => 'No se pudieron obtener los datos del usuario: ' . ($response_usuario_bd['message'] ?? 'Error desconocido')
            ];
            echo json_encode($response_error);
            exit();
        }
    }

    /**
     * Genera un username ultra seguro para Moodle
     */
    private function generateSafeUsername($email)
    {
        $email_parts = explode('@', $email);
        $base = $email_parts[0];

        // Limpiar el nombre: solo letras y números
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', $base);

        // Si el nombre es muy corto o vacío, usar 'user' como base
        if (empty($clean) || strlen($clean) < 3) {
            $clean = 'user';
        } else {
            $clean = strtolower(substr($clean, 0, 10)); // Tomar máximo 10 caracteres del nombre
        }

        // Generar string aleatorio (4 caracteres alfanuméricos)
        $randomChars = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 4);

        // Construir el username final (max 20 chars)
        $username = $clean . $randomChars;

        // Asegurar que no exceda 20 caracteres
        return substr($username, 0, 20);
    }

    /**
     * Genera una contraseña ultra segura para Moodle
     */
    private function generateSafePassword($password)
    {
        // Primero intentar limpiar la contraseña original
        $clean = preg_replace('/[<>"\'\\\]/', '', $password);

        // Si es muy corta o tiene caracteres problemáticos, generar nueva
        if (strlen($clean) < 8 || preg_match('/[^\w\d!@#%&*]/', $clean)) {
            return 'TempPass' . rand(1000, 9999) . '!';
        }

        return $clean;
    }

    /**
     * Limpia strings para Moodle
     */
    private function cleanString($string)
    {
        // Remover caracteres especiales peligrosos
        $clean = preg_replace('/[<>"\'\\\&]/', '', $string);
        $clean = trim($clean);

        // Solo letras, números, espacios y algunos caracteres básicos
        $clean = preg_replace('/[^\w\d\s\-\.]/', '', $clean);

        return $clean;
    }

    /**
     * Valida longitud de strings
     */
    private function validateLength($string, $min, $max)
    {
        if (strlen($string) < $min) {
            return str_pad($string, $min, 'x');
        }
        if (strlen($string) > $max) {
            return substr($string, 0, $max);
        }
        return $string;
    }

    /**
     * Valida campos específicos de Moodle
     */
    private function validateMoodleFields($arrPost)
    {
        $errors = [];

        // Validar username
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $arrPost['username'])) {
            $errors[] = 'Username contiene caracteres inválidos';
        }

        // Validar email
        if (!filter_var($arrPost['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        // Validar nombres
        if (empty($arrPost['firstname']) || strlen($arrPost['firstname']) < 2) {
            $errors[] = 'Firstname muy corto';
        }

        if (empty($arrPost['lastname']) || strlen($arrPost['lastname']) < 2) {
            $errors[] = 'Lastname muy corto';
        }

        // Validar contraseña
        if (strlen($arrPost['password']) < 8) {
            $errors[] = 'Password muy corto';
        }

        if (!empty($errors)) {
            log_message('error', 'Errores de validación: ' . implode(', ', $errors));
        }

        return $errors;
    }

    /**
     * Obtiene resultados de validación para debug
     */
    private function getValidationResults($arrPost)
    {
        return [
            'username_length' => strlen($arrPost['username']),
            'username_chars' => preg_match('/^[a-zA-Z0-9._-]+$/', $arrPost['username']) ? 'valid' : 'invalid',
            'email_valid' => filter_var($arrPost['email'], FILTER_VALIDATE_EMAIL) ? 'valid' : 'invalid',
            'firstname_length' => strlen($arrPost['firstname']),
            'lastname_length' => strlen($arrPost['lastname']),
            'password_length' => strlen($arrPost['password']),
            'password_chars' => preg_match('/^[a-zA-Z0-9!@#$%&*._-]+$/', $arrPost['password']) ? 'valid' : 'invalid'
        ];
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
                $message .= "Usuario: " . (isset($result->usuario_moodle) && $result->usuario_moodle ? $result->usuario_moodle : $result->No_Usuario) . "\n";
                $message .= "Contraseña: {$this->encryption->decrypt($result->No_Password)}\n\n";
                $message .= "Puedes acceder a tu cuenta en el siguiente enlace: https://aulavirtualprobusiness.com/login/\n\n";
                $mensaje = "El día del inicio del curso, te agregaremos a un grupo de whatsapp por donde compartiremos los links de acceso al zoom, los materiales de trabajo y las grabaciones de las clases dictadas.\n\n";
                $message .= "Saludos,\nEl equipo de ProBusiness";
                $this->sendMessageVentas($message, $telefono);
                $this->sendMessageVentas($mensaje, $telefono, 2);
            } else {
                $response = [
                    'status'  => 'error',
                    'message' => 'No se encontró la entidad asociada al pedido.',
                ];
                echo json_encode($response);
                exit();
            }
            $this->load->library('email');

            $data_email["email"]    = $result->usuario_moodle ? $result->usuario_moodle : $result->No_Usuario;
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
            $rows[]      = date('d/m/Y', strtotime($row['Fe_Creacion']));
            $rows[]      = $row['No_Campana'];
            $rows[]      = date('d/m/Y', strtotime($row['Fe_Inicio']));
            $rows[]      = date('d/m/Y', strtotime($row['Fe_Fin']));
            $rows[]      = ($row['cantidad_personas'] ?? $row['Cantidad']) . ' personas';
            $divAcciones = '<div class="d-flex px-2 w-100 flex-row" style="gap:1em;">';
            $divAcciones .= '<svg 
                            class="cursor-pointer" onclick="editarCampana(\'' . $row['ID_Campana'] . '\')"
                            width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.83398 14H14.6676" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.2504 1.47176C11.5524 1.1697 11.9621 1 12.3893 1C12.6008 1 12.8103 1.04166 13.0057 1.12261C13.2011 1.20355 13.3787 1.32219 13.5282 1.47176C13.6778 1.62133 13.7964 1.79889 13.8774 1.99431C13.9583 2.18972 14 2.39917 14 2.61069C14 2.82221 13.9583 3.03166 13.8774 3.22708C13.7964 3.42249 13.6778 3.60006 13.5282 3.74962L4.03715 13.2407L1 14L1.75929 10.9629L11.2504 1.47176Z" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>';
            $divAcciones .= '<svg 
                            class="cursor-pointer" onclick="borrarCampana(\'' . $row['ID_Campana'] . '\')"
                            width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.666992 3.32031H1.82699H11.107" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.94715 3.32V11.44C9.94715 11.7477 9.82493 12.0427 9.60739 12.2602C9.38985 12.4778 9.0948 12.6 8.78715 12.6H2.98715C2.6795 12.6 2.38445 12.4778 2.1669 12.2602C1.94936 12.0427 1.82715 11.7477 1.82715 11.44V3.32M3.56715 3.32V2.16C3.56715 1.85235 3.68936 1.5573 3.9069 1.33976C4.12445 1.12221 4.4195 1 4.72715 1H7.04715C7.3548 1 7.64985 1.12221 7.86739 1.33976C8.08493 1.5573 8.20715 1.85235 8.20715 2.16V3.32" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.72656 6.21875V9.69875" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.04688 6.21875V9.69875" stroke="#FF0000" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>';
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
        $result = $this->PedidosCursoModel->crearCampana($this->input->post('Fe_Inicio'), $this->input->post('Fe_Fin'),
        $this->input->post('Dias_Seleccionados')
    );
        echo json_encode($result);
    }
    public function editarCampana()
    {
        $id        = $this->input->post('ID_Campana');
        $fe_inicio = $this->input->post('Fe_Inicio');
        $fe_fin    = $this->input->post('Fe_Fin');
        $dias      = $this->input->post('Dias_Seleccionados');
        $result    = $this->PedidosCursoModel->editarCampana($id, $fe_inicio, $fe_fin, $dias);
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
    public function getPagosCurso($idPedidoCurso)
    {
        try {
            $arrData = $this->PedidosCursoModel->getPagosCursoPedido($idPedidoCurso);
            $data    = [];
            $index   = 1;
            foreach ($arrData as $row) {
                $subdata = [];
                $subdata[] = $index;
                $subdata[] = allTypeDate($row->payment_date, '-', 0);
                $subdata[] = $row->banco;
                $subdata[] = "$" . round($row->monto, 2);
                $subdata[] = '<div data-url=' . $row->voucher_url . ' download
                onclick="showImageModal(\'' . $row->voucher_url . '\')"
                >
                    <i class="fas fa-file"></i>
                    </div>';
                $divAcciones = '<div class="d-flex justify-content-start">';
                $divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="eliminarPagoCurso(\'' . $row->id . '\')"></i>';
                $divAcciones .= '</div>';
                $subdata[] = $divAcciones;
                $data[] = $subdata;
                $index++;
            }
            $output = array(
                "data" => $data
            );
            echo json_encode($output);
        } catch (Exception $e) {
            log_message('error', 'ContenedorConsolidado : getPagosCurso() => ' . $e->getMessage());
        }
    }
    public function eliminarPagoCurso($idPagoCurso)
    {
        try {
            $result = $this->PedidosCursoModel->eliminarPagoCurso($idPagoCurso);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Pago eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el pago']);
            }
        } catch (Exception $e) {
            log_message('error', 'Error al eliminar pago: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el pago']);
        }
    }
    public function borrarPagoCurso()
    {
        $idPagoCurso = $this->input->post('idPagoCurso');
        if ($idPagoCurso) {
            $result = $this->PedidosCursoModel->borrarPagoCurso($idPagoCurso);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de pago no proporcionado']);
        }
    }
}
