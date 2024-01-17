<?php
class NotificacionModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	/*
  	`No_Usuario` varchar(100) COLLATE utf8_spanish_ci NULL, porque si guardo id puden modificar no podre trackear ademas tengo menos join
  	`No_Menu` varchar(100) COLLATE utf8_spanish_ci NULL, porque si guardo el menu y cambia nombre no podre trackear ademas tengo menos join
  	`No_Evento` varchar(100) COLLATE utf8_spanish_ci NULL COMMENT 'Guardar, Modificar, Anular, Eliminar', ejemplo: edito estado de pendiente a en proceso
  	`Txt_Evento` TEXT, guardo todo el evento
	*/
	public function procesarNotificacion($sUsuario, $sMenu, $sEvento, $sAllEvento){
		//array_debug($sUsuario);
		//array_debug($sMenu);
		//array_debug($sEvento);
		
		//aqui recorro la tabla grupo para guarda el evento de todos los usuario que tengan el campo Nu_Notificacion = 1
		//luego guardo insert masivo
		$query = "SELECT USR.ID_Usuario FROM usuario AS USR JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario) JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo) WHERE GRP.Nu_Notificacion = 1 AND GRP.ID_Empresa=" . $this->user->ID_Empresa;

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$notificacion = array();
			foreach ($arrResponseSQL->result() as $row) {
				$notificacion[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Organizacion' => $this->user->ID_Organizacion,
					'ID_Usuario' => $row->ID_Usuario,
					'No_Usuario_Evento' => $sUsuario,
					'No_Menu' => $sMenu,
					'No_Evento' => $sEvento,
					'Txt_Evento' => $sAllEvento,
				);
			}

			if(!empty($notificacion))
				$this->db->insert_batch('notificacion', $notificacion);
		
			return array(
				'status' => 'success',
				'message' => 'Se registro notificación',
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
	
	public function obtenerNotificacionUsuario($ID_Usuario){
		$query = "SELECT * FROM notificacion WHERE ID_Usuario = " . $ID_Usuario . " ORDER BY Fe_Registro DESC";

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result()
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
}
