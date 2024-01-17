<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerfilUsuarioController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('PanelAcceso/PerfilUsuarioModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listarPerfilUsuarios(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('inicio');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('PanelAcceso/PerfilUsuarioView');
			$this->load->view('footer_v2', array("js_perfil_usuario" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->PerfilUsuarioModel->get_datatables();
        $action = 'delete';
        foreach ($arrData as $row) {
            $rows = array();
			if ( $this->user->No_Usuario == 'root' ){
				$rows[] = $row->No_Empresa;
				$rows[] = $row->No_Organizacion;
			}

			$sNombrePrivilegioAcceso = 'Personal Probusiness';
			if($row->Nu_Tipo_Privilegio_Acceso==2){
				$sNombrePrivilegioAcceso = 'Personal China';
			} else if($row->Nu_Tipo_Privilegio_Acceso==3){
				$sNombrePrivilegioAcceso = 'Proveedor externo';
			} else if($row->Nu_Tipo_Privilegio_Acceso==4){
				$sNombrePrivilegioAcceso = 'Cliente';
			}

            $rows[] = '<span class="badge bg-secondary">' . $sNombrePrivilegioAcceso . '</span>';
            $rows[] = $row->No_Grupo;
            $rows[] = $row->No_Grupo_Descripcion;
			
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoNotificacionArray($row->Nu_Notificacion);
			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Recibir" title="Recibir" href="javascript:void(0)" onclick="cambiarNotificacion(\'' . $row->ID_Grupo . '\',1);">Recibir</a></li>';
					$dropdown_estado .= '<li class="dropdown-item p-0"><a class="px-3 py-1 btn-block" alt="Inactivo" title="Inactivo" href="javascript:void(0)" onclick="cambiarNotificacion(\'' . $row->ID_Grupo . '\',0);">Desactivar</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';
			
            $rows[] = '<button class="btn btn-xs btn-link btn-upd" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verPerfilUsuario(\'' . $row->ID_Grupo . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
            $rows[] = '<button class="btn btn-xs btn-link btn-upd" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarPerfilUsuario(\'' . $row->ID_Grupo . '\', \'' . $action . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        "data" => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->PerfilUsuarioModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudPerfilUsuario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'ID_Empresa' => $this->input->post('ID_Empresa'),
			'ID_Organizacion' => $this->input->post('ID_Organizacion'),
			'No_Grupo' => $this->input->post('No_Grupo'),
			'No_Grupo_Descripcion' => $this->input->post('No_Grupo_Descripcion'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
			'Nu_Tipo_Privilegio_Acceso' => $this->input->post('Nu_Tipo_Privilegio_Acceso'),
		);
		echo json_encode(
		($_POST['EID_Organizacion'] != '' || $_POST['EID_Grupo'] != '') ?
			$this->PerfilUsuarioModel->actualizarPerfilUsuario(array('ID_Organizacion' => $this->input->post('EID_Organizacion'), 'ID_Grupo' => $this->input->post('EID_Grupo')), $data, $this->input->post('ENo_Grupo'))
		:
			$this->PerfilUsuarioModel->agregarPerfilUsuario($data)
		);
	}
    
	public function eliminarPerfilUsuario($ID){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->PerfilUsuarioModel->eliminarPerfilUsuario($this->security->xss_clean($ID)));
	}

	public function cambiarNotificacion($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PerfilUsuarioModel->cambiarNotificacion($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
}
