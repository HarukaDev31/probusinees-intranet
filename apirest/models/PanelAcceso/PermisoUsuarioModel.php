<?php
class PermisoUsuarioModel extends CI_Model{
	var $table_menu_acceso = 'menu_acceso';
	var $table_menu = 'menu';
	var $table_grupo_usuario = 'grupo_usuario';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getMenuAccesoxGrupo($arrGet){
		if ($this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Empresa = " . $arrGet['ID_Empresa'] . " AND ID_Organizacion = " . $arrGet['ID_Organizacion'] . " AND ID_Grupo = " . $arrGet['ID_Grupo'] . " LIMIT 1")->row()->existe == 0) {
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El grupo no tiene asignado usuario(s)', 'arrData' => 0);
		} else {
			$cond_tipo_sistema =  "AND MNU.Nu_Tipo_Sistema=0";

			$query = "SELECT DISTINCT
MNU.ID_Padre,
MNU.ID_Menu,
MNU.No_Menu,
MNU.Txt_Url_Video,
MNUACCESS.ID_Grupo,
MNUACCESS.Nu_Consultar,
MNUACCESS.Nu_Agregar,
MNUACCESS.Nu_Editar,
MNUACCESS.Nu_Eliminar
FROM
menu AS MNU
LEFT JOIN (
SELECT DISTINCT
MNUACCESS.ID_Menu,
GRPUSR.ID_Grupo,
MNUACCESS.Nu_Consultar,
MNUACCESS.Nu_Agregar,
MNUACCESS.Nu_Editar,
MNUACCESS.Nu_Eliminar
FROM
menu_acceso AS MNUACCESS
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNUACCESS.ID_Empresa = " . $arrGet['ID_Empresa'] . "
AND GRPUSR.ID_Grupo = " . $arrGet['ID_Grupo'] . "
) AS MNUACCESS ON (MNUACCESS.ID_Menu = MNU.ID_Menu)
LEFT JOIN (
SELECT
MNU.ID_Menu AS ID_Menu_Sub_Padre,
(SELECT COUNT(*) FROM menu WHERE Nu_Seguridad = 0 AND ID_Padre = MNU.ID_Menu) AS Nu_Cantidad_Menu_Hijos
FROM
menu AS MNU
INNER JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
WHERE
MNU.Nu_Seguridad = 0
AND MNU.Nu_Activo = 0
" . $cond_tipo_sistema . "
) AS MNUSUBPADRE ON(MNUSUBPADRE.ID_Menu_Sub_Padre = MNU.ID_Menu)
WHERE
MNU.ID_Padre > 0
AND (MNUSUBPADRE.Nu_Cantidad_Menu_Hijos = 0 OR MNUSUBPADRE.Nu_Cantidad_Menu_Hijos IS NULL)
AND MNU.Nu_Seguridad = 0
AND MNU.Nu_Activo = 0
" . $cond_tipo_sistema . "
ORDER BY
MNU.ID_Padre,
MNU.Nu_Orden;";
			$arrData = $this->db->query($query)->result();
			$i = 0;
			foreach ($arrData as $row) {
				$arrData[$i]->No_Menu_Padre = $this->getMenuPadreID($row->ID_Padre);
				++$i;
			}
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Datos encontrados', 'arrData' => $arrData);
		}
    }
    
	public function agregarPermisoUsuario($arrPost){
		$this->db->trans_begin();

		$arrData['ID_Grupo_Usuario'] = $this->db->query("SELECT ID_Grupo_Usuario FROM grupo_usuario WHERE ID_Empresa = " . $arrPost['ID_Empresa'] . " AND ID_Organizacion = " . $arrPost['ID_Organizacion'] . " AND ID_Grupo = " . $arrPost['ID_Grupo_'] . " LIMIT 1")->row()->ID_Grupo_Usuario;
		
		unset($arrData['ID_Grupo']);
		unset($arrData['ID_Grupo_']);
		
		$this->db->where('ID_Grupo_Usuario', $arrData['ID_Grupo_Usuario']);
		$this->db->delete('menu_acceso');
		
		// insertar menu seguridad
		$_arrData = array(
			'ID_Grupo_Usuario' => $arrData['ID_Grupo_Usuario'],
		);
		$arrPost = array_merge($arrPost, $_arrData);

		$this->addMenuAcceso($arrPost);

		$this->addMenuAccesoSeguridad($arrPost);
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al guardar permisos');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Registro guardado');
		}
	}

	private function addMenuAccesoSeguridad($arrPost){
		$arrGrupoUsuario = $this->db->query("SELECT No_Grupo FROM grupo_usuario AS GRPUSER JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSER.ID_Grupo) WHERE GRPUSER.ID_Grupo_Usuario = " . $arrPost['ID_Grupo_Usuario'] . " LIMIT 1")->result();
		$sNombreGrupo = strtoupper($arrGrupoUsuario[0]->No_Grupo);
		if ( 
			$sNombreGrupo == 'GERENCIA'
			|| $sNombreGrupo == 'GERENTE GENERAL'
			|| $sNombreGrupo == 'GERENTE'
			|| $sNombreGrupo == 'SISTEMAS'
			|| $sNombreGrupo == 'DUEÑO'
			|| $sNombreGrupo == 'SOCIOS'
			|| $sNombreGrupo == 'FUNDADOR'
			|| $sNombreGrupo == 'FUNDADORES'
		) {
			$query = "SELECT ID_Menu FROM menu WHERE ID_Menu IN(1,8,58,159,162,163);";
			$arrData = $this->db->query($query)->result();
			foreach ( $arrData as $row ) {
				$arrMenuSeguridad[] = array(
					'ID_Empresa'		=> $arrPost['ID_Empresa'],
					'ID_Menu'			=> $row->ID_Menu,
					'ID_Grupo_Usuario'	=> $arrPost['ID_Grupo_Usuario'],
					'Nu_Consultar'		=> 1,
					'Nu_Agregar'		=> 0,
					'Nu_Editar'			=> 1,
					'Nu_Eliminar'		=> 0,
				);
			}
			$this->db->insert_batch($this->table_menu_acceso, $arrMenuSeguridad);
			unset($arrMenuSeguridad);
		} else {
			$query = "SELECT ID_Menu FROM menu WHERE ID_Menu IN(1);";
			$arrData = $this->db->query($query)->result();
			foreach ( $arrData as $row ) {
				$arrMenuSeguridad[] = array(
					'ID_Empresa'		=> $arrPost['ID_Empresa'],
					'ID_Menu'			=> $row->ID_Menu,
					'ID_Grupo_Usuario'	=> $arrPost['ID_Grupo_Usuario'],
					'Nu_Consultar'		=> 1,
					'Nu_Agregar'		=> 0,
					'Nu_Editar'			=> 1,
					'Nu_Eliminar'		=> 0,
				);
			}
			$this->db->insert_batch($this->table_menu_acceso, $arrMenuSeguridad);
			unset($arrMenuSeguridad);
		}
	}

	private function addMenuAcceso($arrPost){
		$EID_Menu_Padre = '';
		$EID_Menu_Sub_Padre = '';
		foreach ($arrPost['ID_Menu_CRUD'] as $key => $value){
			$ID_Menu = $key;
			//Agregando menu padre
			if ( $this->db->query("SELECT COUNT(*) existe FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = " . $ID_Menu . " LIMIT 1) LIMIT 1) LIMIT 1")->row()->existe > 0) {
				$ID_Menu_Padre = $this->db->query("SELECT ID_Menu FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = " . $ID_Menu . " LIMIT 1) LIMIT 1) LIMIT 1")->row()->ID_Menu;
				if ($EID_Menu_Padre != $ID_Menu_Padre) {
					if ($this->db->query("SELECT COUNT(*) existe FROM menu_acceso WHERE ID_Grupo_Usuario = " . $arrPost['ID_Grupo_Usuario'] . " AND ID_Menu = " . $ID_Menu_Padre)->row()->existe == 0){
						$menu_acceso_padre = array(
							'ID_Empresa'		=> $arrPost['ID_Empresa'],
							'ID_Menu'			=> $ID_Menu_Padre,
							'ID_Grupo_Usuario'	=> $arrPost['ID_Grupo_Usuario'],
							'Nu_Consultar'		=> 1,
							'Nu_Agregar'		=> 1,
							'Nu_Editar'			=> 1,
							'Nu_Eliminar'		=> 1,
						);
						$this->db->insert('menu_acceso', $menu_acceso_padre);
					}
					$EID_Menu_Padre = $ID_Menu_Padre;
				}
			} // /. if menu padre

			//Agregando menu Sub padre
			if ($this->db->query("SELECT COUNT(*) existe FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = " . $ID_Menu . " LIMIT 1) LIMIT 1")->row()->existe > 0 ) {
				$ID_Menu_Sub_Padre = $this->db->query("SELECT ID_Menu FROM menu WHERE ID_Menu = (SELECT ID_Padre FROM menu WHERE ID_Menu = " . $ID_Menu . " LIMIT 1) LIMIT 1")->row()->ID_Menu;
				if ($EID_Menu_Sub_Padre != $ID_Menu_Sub_Padre) {
					if ($this->db->query("SELECT COUNT(*) existe FROM menu_acceso WHERE ID_Grupo_Usuario = " . $arrPost['ID_Grupo_Usuario'] . " AND ID_Menu = " . $ID_Menu_Sub_Padre)->row()->existe == 0){
						$menu_acceso_sub_padre = array(
							'ID_Empresa'		=> $arrPost['ID_Empresa'],
							'ID_Menu'			=> $ID_Menu_Sub_Padre,
							'ID_Grupo_Usuario'	=> $arrPost['ID_Grupo_Usuario'],
							'Nu_Consultar'		=> 1,
							'Nu_Agregar'		=> 1,
							'Nu_Editar'			=> 1,
							'Nu_Eliminar'		=> 1,
						);
						$this->db->insert('menu_acceso', $menu_acceso_sub_padre);
					}
					$EID_Menu_Sub_Padre = $ID_Menu_Sub_Padre;
				}
			}// /. if sub menu padre

			$menu_acceso_hijo = array(
				'ID_Empresa'		=> $arrPost['ID_Empresa'],
				'ID_Menu'			=> $ID_Menu,
				'ID_Grupo_Usuario'	=> $arrPost['ID_Grupo_Usuario'],
				'Nu_Consultar'		=> ( isset($value['Nu_Consultar']) ? 1 : 0),
				'Nu_Agregar'		=> ( isset($value['Nu_Agregar']) ? 1 : 0),
				'Nu_Editar'			=> ( isset($value['Nu_Editar']) ? 1 : 0),
				'Nu_Eliminar'		=> ( isset($value['Nu_Eliminar']) ? 1 : 0),
			);
			$this->db->insert('menu_acceso', $menu_acceso_hijo);
		}// /. foreach ID MENU CRUD
	}

    public function getMenuPadreID($ID){
        $query ="SELECT ID_Padre,No_Menu FROM menu WHERE ID_Menu = " . $ID . " LIMIT 1";
		$rowPadre = $this->db->query($query)->row();
		$No_Menu = $rowPadre->No_Menu;
		if($ID>0) {
			$query ="SELECT No_Menu FROM menu WHERE ID_Menu = " . $rowPadre->ID_Padre . " LIMIT 1";
			$rowPadreFirst = $this->db->query($query)->row();
			if ( is_object($rowPadreFirst) ) {
				$No_Menu = $rowPadreFirst->No_Menu . ' > ' . $No_Menu;
			}
		}
        return $No_Menu;
    }
}
