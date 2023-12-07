<?php
class MenuModel extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	private $where_id_grupo;

	public function listarMenu(){
		$this->select_distinct = "";
		$this->where_id_grupo = "AND GRPUSR.ID_Grupo=" . $this->user->ID_Grupo;
		$this->order_by_nu_agregar = "";
		if ( $this->user->No_Usuario == 'root' ) {
			$this->select_distinct = " DISTINCT";
			$this->where_id_grupo = "";
			$this->order_by_nu_agregar = "ORDER BY Nu_Agregar DESC";
		}
		$sql = "SELECT DISTINCT
MNU.*,
(SELECT COUNT(*) FROM menu WHERE ID_Padre=MNU.ID_Menu AND Nu_Activo=0) AS Nu_Cantidad_Menu_Padre
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.ID_Padre=0
AND MNU.Nu_Activo=0
" . $this->where_id_grupo . "
ORDER BY
MNU.ID_Padre ASC,
MNU.Nu_Orden;";
		$arrMenuPadre = $this->db->query($sql)->result();
		
		foreach($arrMenuPadre as $rowPadre){
			$sql = "SELECT DISTINCT
MNU.*,
(SELECT COUNT(*) FROM menu WHERE ID_Padre=MNU.ID_Menu AND Nu_Activo=0) AS Nu_Cantidad_Menu_Hijos
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.ID_Padre=" . $rowPadre->ID_Menu . "
AND MNU.Nu_Activo=0
" . $this->where_id_grupo . "
ORDER BY
MNU.Nu_Orden;";
			$rowPadre->{'Hijos'} = $this->db->query($sql)->result();
		
			foreach($rowPadre->Hijos as $rowSubHijos){
				if ( $rowSubHijos->Nu_Cantidad_Menu_Hijos > 0 ) {
					$sql = "SELECT DISTINCT
MNU.*
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.ID_Padre=" . $rowSubHijos->ID_Menu . "
AND MNU.Nu_Activo=0
" . $this->where_id_grupo . "
ORDER BY
MNU.Nu_Orden;";
					$rowSubHijos->{'SubHijos'} = $this->db->query($sql)->result();
				}
			}
		}
		return $arrMenuPadre;
	}
	
	public function verificarAccesoMenu(){
		$sql = "SELECT
MNU.*,
MNUACCESS.Nu_Consultar, MNUACCESS.Nu_Agregar, MNUACCESS.Nu_Editar, MNUACCESS.Nu_Eliminar
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.No_Menu_Url='" . $this->router->directory . $this->router->class . "/" . $this->router->method . "'
AND MNU.Nu_Activo=0
AND GRPUSR.ID_Grupo=" . $this->user->ID_Grupo . " LIMIT 1";

		$bStatusMenu = is_object($this->db->query($sql)->row()) ? true : false;
		if ( $this->user->No_Usuario == 'root' )
			$bStatusMenu = true;
		return $bStatusMenu;
	}
	
	public function verificarAccesoMenuInterno($sMethod){
		$sql = "SELECT
MNU.*,
MNUACCESS.Nu_Consultar, MNUACCESS.Nu_Agregar, MNUACCESS.Nu_Editar, MNUACCESS.Nu_Eliminar
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.No_Menu_Url='" . $this->router->directory . $this->router->class . "/" . $sMethod . "'
AND MNU.Nu_Activo=0
AND GRPUSR.ID_Grupo=" . $this->user->ID_Grupo . " LIMIT 1";
		return $this->db->query($sql)->row();
	}
	
	public function verificarAccesoMenuCRUD(){
		$sql = "SELECT" . $this->select_distinct . "
MNU.No_Menu,
MNU.Txt_Css_Icons,
MNU.Txt_Url_Video,
MNUACCESS.Nu_Consultar, MNUACCESS.Nu_Agregar, MNUACCESS.Nu_Editar, MNUACCESS.Nu_Eliminar
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.No_Menu_Url='" . $this->router->directory . $this->router->class . "/" . $this->router->method . "'
AND MNU.Nu_Activo=0
" . $this->where_id_grupo . $this->order_by_nu_agregar;
		return $this->db->query($sql)->row();
	}
	
	public function verificarAccesoMenuXGrupo($arrPost){
		if($this->db->query("SELECT COUNT(*) AS existe FROM menu_acceso WHERE ID_Empresa=".$arrPost['ID_Empresa']." AND ID_Menu=".$arrPost['ID_Menu']." AND ID_Grupo_Usuario=" . $this->session->usuario->ID_Grupo_Usuario . " LIMIT 1")->row()->existe > 0)
			return array('status' => 'success', 'message' => 'Si tiene acceso');
		return array('status' => 'error', 'message' => 'No tienes acceso, agregar en la opción Usuarios > Opciones del menú, opción Ventas > Balance de Pagos Laesytems');
	}
	
	public function verificarAccesoMenuInternoEstatico($sRutaOpcion){
		$sql = "SELECT
MNU.*,
MNUACCESS.Nu_Consultar, MNUACCESS.Nu_Agregar, MNUACCESS.Nu_Editar, MNUACCESS.Nu_Eliminar
FROM
menu AS MNU
JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
WHERE
MNU.No_Menu_Url='" . $sRutaOpcion . "'
AND MNU.Nu_Activo=0
AND GRPUSR.ID_Grupo=" . $this->user->ID_Grupo . " LIMIT 1";
		return $this->db->query($sql)->row();
	}
}