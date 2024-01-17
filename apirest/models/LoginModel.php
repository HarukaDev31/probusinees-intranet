<?php
class LoginModel extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function verificarAccesoLogin($data){
		$No_Password = trim($data['No_Password']);
		$No_Password = strip_tags($No_Password);
		
		$No_Usuario	= trim($data['No_Usuario']);
		$No_Usuario = strip_tags($No_Usuario);
		
		$No_Password = trim($data['No_Password']);
		$No_Password = strip_tags($No_Password);
						
		//$this->db->where('No_Usuario', $No_Usuario)->or_where('Nu_Celular', $No_Usuario)->or_where('Txt_Email', $No_Usuario);
		$this->db->where('No_Usuario', $No_Usuario);
		$this->db->order_by("Fe_Creacion", "ASC");
		$u = $this->db->get('usuario')->row();
		
		//var_dump($u);

		if(is_object($u)){
			if ($this->db->query("SELECT Nu_Estado FROM empresa WHERE ID_Empresa = " . $u->ID_Empresa . " LIMIT 1")->row()->Nu_Estado == 1) {
				if ($this->db->query("SELECT ORG.Nu_Estado FROM empresa AS EMP JOIN organizacion AS ORG ON(EMP.ID_Empresa = ORG.ID_Empresa) WHERE EMP.ID_Empresa = " . $u->ID_Empresa . " AND ORG.ID_Organizacion = " . $u->ID_Organizacion . " LIMIT 1")->row()->Nu_Estado == 1) {
					$UNo_Password = $this->encryption->decrypt($u->No_Password);//Verificar contraseña
					if( $UNo_Password == $No_Password ){
						if($u->Nu_Estado == 1){// 1 = Activo
							$ID_Empresa = trim($data['ID_Empresa']);
							$ID_Empresa = strip_tags($ID_Empresa);
							
							$ID_Organizacion = trim($data['ID_Organizacion']);
							$ID_Organizacion = strip_tags($ID_Organizacion);
							
							if ( $ID_Empresa == '' && $ID_Organizacion == '' ) {
								//$query = "SELECT COUNT(*) as cantidad FROM usuario WHERE ID_Grupo != '' AND (No_Usuario = '" . $No_Usuario . "' OR Nu_Celular = '" . $No_Usuario . "' OR Txt_Email = '" . $No_Usuario . "') AND Nu_Estado = 1";
								$query = "SELECT COUNT(*) as cantidad FROM usuario WHERE No_Usuario = '" . $No_Usuario . "' AND Nu_Estado = 1";
								$row = $this->db->query($query)->row();
								//$iCantidadAcessoUsuario = $row->cantidad;
								$iCantidadAcessoUsuario = 1;

								$sql = "SELECT
USR.*,
GRP.ID_Organizacion,
GRP.No_Grupo,
GRP.No_Grupo_Descripcion,
GRP.Nu_Tipo_Privilegio_Acceso,
GRP.Nu_Notificacion,
GRPUSR.ID_Grupo_Usuario,
T.No_Dominio_Tienda_Virtual,
T.No_Subdominio_Tienda_Virtual,
T.Nu_Estado as TiendaEstado,
P.ID_Pais,
P.No_Pais,
MONE.*
FROM
usuario AS USR
JOIN empresa AS EMP ON(EMP.ID_Empresa = USR.ID_Empresa)
JOIN pais AS P ON(P.ID_Pais = EMP.ID_Pais)
JOIN moneda AS MONE ON(EMP.ID_Empresa = MONE.ID_Empresa)
JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario)
JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo)
LEFT JOIN subdominio_tienda_virtual T ON T.ID_Empresa=USR.ID_Empresa
WHERE
USR.No_Usuario = '" . $No_Usuario . "' AND USR.Nu_Estado=1 ORDER BY Fe_Creacion ASC LIMIT 1;";
								$u = $this->db->query($sql)->row();
								
								// Protegemos la contraseña
								unset($No_Password);
								unset($UNo_Password);
								unset($u->No_Password);

								// Respondemos
								$this->session->set_userdata('usuario', $u);
								// print_r($u);
								// print_r($this->session->userdata['usuario']);
								// echo $this->db->last_query();
								// exit();
								$query = "SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Organizacion IN (SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa = " . $u->ID_Empresa . " AND ID_Organizacion = " . $u->ID_Organizacion . " AND Nu_Estado = 1) AND Nu_Estado = 1 ORDER BY ID_Almacen LIMIT 1";
								$objAlmacen = $this->db->query($query)->row();
								$this->session->set_userdata('almacen', $objAlmacen);

								return array(
									'sStatus' => 'success',
									'sMessage' => 'Iniciando sesión',
									'iCantidadAcessoUsuario' => $iCantidadAcessoUsuario,
									'iIdEmpresa' => $u->ID_Empresa
								);
							} else {
								$sql = "SELECT
USR.*,
GRP.ID_Organizacion,
GRP.No_Grupo,
GRP.No_Grupo_Descripcion,
GRP.Nu_Tipo_Privilegio_Acceso,
GRP.Nu_Notificacion,
GRPUSR.ID_Grupo_Usuario,
T.No_Dominio_Tienda_Virtual,
T.No_Subdominio_Tienda_Virtual,
T.Nu_Estado as TiendaEstado,
P.ID_Pais,
P.No_Pais,
MONE.*
FROM
usuario AS USR
JOIN empresa AS EMP ON(EMP.ID_Empresa = USR.ID_Empresa)
JOIN pais AS P ON(P.ID_Pais = EMP.ID_Pais)
JOIN moneda AS MONE ON(EMP.ID_Empresa = MONE.ID_Empresa)
JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario)
JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo)
JOIN organizacion AS ORG ON(ORG.ID_Organizacion = USR.ID_Organizacion)
LEFT JOIN subdominio_tienda_virtual T ON T.ID_Empresa=USR.ID_Empresa
WHERE
USR.No_Usuario = '" . $No_Usuario . "'
AND GRP.ID_Empresa = " . $ID_Empresa . "
AND GRP.ID_Organizacion = " . $ID_Organizacion . "
AND ORG.Nu_Estado = 1
AND USR.Nu_Estado=1 ORDER BY Fe_Creacion ASC
LIMIT 1;";
								$u = $this->db->query($sql)->row();
								if ( is_object($u) ) {
									// Protegemos la contraseña
									unset($No_Password);
									unset($UNo_Password);
									unset($u->No_Password);

									// Respondemos
									$this->session->set_userdata('usuario', $u);

									$query = "SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Organizacion=" . $ID_Organizacion . " AND Nu_Estado = 1 ORDER BY ID_Almacen LIMIT 1";
									$objAlmacen = $this->db->query($query)->row();
									$this->session->set_userdata('almacen', $objAlmacen);

									return array(
										'sStatus' => 'success',
										'sMessage' => 'Iniciando sesión',
									);
								} else {
									return array(
										'sStatus' => 'warning',
										'sMessage' => 'Comunicarse con soporte para activación de cuenta',
									);
								}
							}// /. if - else verificar empresa y organizacion
						} else {
							return array(
								'sStatus' => 'warning',
								'sMessage' => 'comunicarse con soporte.',
							);//Usuario Suspendido
						}// /. if - else usuario suspendido
					} else {
						return array(
							'sStatus' => 'warning',
							'sMessage' => 'Contraseña incorrecta',
						);//Contraseña incorrecta
					} // /. if - else contraseña incorrecta
				} else {
					return array(
						'sStatus' => 'danger',
						'sMessage' => 'Comunicarse con soporte.',
					);//Usuario no existe
				} // /. if - else usuario no existe
			} else {
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'comunicarse con soporte',
				);//Usuario no existe
			} // /. if - else usuario no existe
		} else {
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'No existe usuario',
			);//Usuario no existe
		} // /. if - else empresa desactivada
	}
	
	public function verificar_email($data){
		$Txt_Email = trim($data['Txt_Email']);
		$Txt_Email = strip_tags($Txt_Email);
		
		//$query = $this->db->get_where('usuario', array('Txt_Email' => $Txt_Email, 'Nu_Estado' => 1));
		$objUsuario = $this->db->query("SELECT Nu_Estado, ID_Usuario, Txt_Token_Activacion FROM usuario WHERE Txt_Email='" . $Txt_Email . "' LIMIT 1;")->row();
        if ( is_object($objUsuario) ) {//Existe usuario activo
			if ($objUsuario->Nu_Estado == 0) {
				return array('data' => NULL, 'status' => 'warning', 'type' => 'inactivo', 'message' => 'Usuario suspendido');
			}

			if($this->startRecoveryPassword($objUsuario->ID_Usuario, $data['Txt_Token_Activacion']))
        		return array('data' => $objUsuario, 'status' => 'success', 'type' => 'activo', 'message' => 'Usuario activo');
			return array('data' => NULL, 'status' => 'success_error_modificar', 'type' => 'activo', 'message' => 'Problemas al modificar usuario');
		}
		return array('data' => NULL, 'status' => 'error', 'type' => 'invalido', 'message' => 'No existe usuario');
	}
	
	private function startRecoveryPassword($ID_Usuario, $Txt_Token_Activacion){
		$Fe_Vencimiento_Token = date('Y-m-d H:i:s', strtotime("+15 min"));//15 minutos al usuario para recuperar su password
		$data = array("Fe_Vencimiento_Token" => $Fe_Vencimiento_Token, "Txt_Token_Activacion" => $Txt_Token_Activacion);
		$this->db->where("ID_Usuario", $ID_Usuario);
		$status = FALSE;
		if($this->db->update("usuario", $data))
			$status = TRUE;
		return $status;
	}
	
	public function cambiar_clave($data){
		$No_Password = $data['No_Password'];
		$Txt_Token_Activacion = $data['Txt_Token_Activacion'];
		//if ($this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE Nu_Estado = 1 AND Txt_Token_Activacion = '" . $Txt_Token_Activacion . "' AND Fe_Vencimiento_Token > '" . dateNow('fecha_hora') . "' LIMIT 1")->row()->existe > 0){
			
		$objUsuario = $this->db->query("SELECT No_Usuario, Nu_Estado, ID_Usuario, Txt_Token_Activacion FROM usuario WHERE Txt_Token_Activacion='" . $Txt_Token_Activacion . "' AND Fe_Vencimiento_Token > '" . dateNow('fecha_hora') . "' LIMIT 1;")->row();
        if ( is_object($objUsuario) ) {//Existe usuario activo
			if($this->changePassword($No_Password, $Txt_Token_Activacion, $objUsuario->No_Usuario))
				return array('status' => 'success', 'type' => 'token_no_expirado', 'message' => 'Contraseña cambiada satisfactoriamente');	
			return array('status' => 'warning', 'type' => 'token_no_expirado', 'message' => 'Problemas al cambiar contraseña');	
		}
		return array('status' => 'error', 'type' => 'invalido', 'message' => 'Token inválido');
	}
	
	private function changePassword($No_Password, $Txt_Token_Activacion, $No_Usuario){
		$data = array("No_Password" => $No_Password);
		//$this->db->where("Txt_Token_Activacion", $Txt_Token_Activacion);
		$this->db->where("No_Usuario", $No_Usuario);
		$status = FALSE;
		if($this->db->update("usuario", $data))
			$status = TRUE;
		return $status;
	}
	
	public function getValoresTablaDato($arrPost){
		$query = "SELECT * FROM tabla_dato WHERE No_Relacion='" . $arrPost['sTipoData'] . "' AND ID_Tabla_Dato!=2091 AND ID_Tabla_Dato!=2163 ORDER BY CONVERT(No_Class, SIGNED INTEGER) ASC";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos - ' . $arrPost['sTipoData'],
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro - ' . $arrPost['sTipoData'],
			'sClassModal' => 'modal-warning',
		);
	}
	
	public function getTiposDocumentoIdentidad(){
		$this->db->where('Nu_Estado', 1);
		$this->db->order_by('Nu_Orden');
		return $this->db->get('tipo_documento_identidad')->result();
	}

	public function creandoNuevaSessionUsuario($arrParams){
		$sql = "SELECT
		USR.*,
		GRP.ID_Organizacion,
		GRP.No_Grupo,
		GRP.No_Grupo_Descripcion,
		GRPUSR.ID_Grupo_Usuario,
		T.No_Dominio_Tienda_Virtual,
		T.No_Subdominio_Tienda_Virtual,
		T.Nu_Estado as TiendaEstado,
		P.ID_Pais,
		P.No_Pais,
		MONE.*
		FROM
		usuario AS USR
		JOIN empresa AS EMP ON(EMP.ID_Empresa = USR.ID_Empresa)
		JOIN pais AS P ON(P.ID_Pais = EMP.ID_Pais)
		JOIN moneda AS MONE ON(EMP.ID_Empresa = MONE.ID_Empresa)
		JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario)
		JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo)
		LEFT JOIN subdominio_tienda_virtual T ON T.ID_Empresa=USR.ID_Empresa
		WHERE
		USR.No_Usuario = '" . trim($arrParams['No_Usuario']) . "' AND P.Nu_Codigo_Sunat_ISO='" . trim($arrParams['Nu_Codigo_Sunat_ISO']) . "' LIMIT 1;";

		if ( !$this->db->simple_query($sql) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'No se encontro registro empresa: ' . $arrParams['Nu_Codigo_Sunat_ISO'] . ' - ' . $arrParams['No_Usuario'],
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($sql);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$u = $arrResponseSQL->row();
			//print_r($u);
			
			// Protegemos la contraseña
			unset($No_Password);
			unset($UNo_Password);
			unset($u->No_Password);

			// Respondemos
			$this->session->set_userdata('usuario', $u);
			//print_r($this->session->userdata['usuario']);
			// echo $this->db->last_query();
			// exit();
			$query = "SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Organizacion IN (SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa = " . $u->ID_Empresa . " AND ID_Organizacion = " . $u->ID_Organizacion . " AND Nu_Estado = 1) AND Nu_Estado = 1 ORDER BY ID_Almacen LIMIT 1";
			
			if ( !$this->db->simple_query($query) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'No se encontro registro empresa: ' . $arrParams['Nu_Codigo_Sunat_ISO'] . ' - ' . $arrParams['No_Usuario'],
					'sClassModal' => 'modal-danger',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}

			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$objAlmacen = $arrResponseSQL->row();
				$this->session->set_userdata('almacen', $objAlmacen);

				return array(
					'sStatus' => 'success',
					'sMessage' => 'Iniciando sesión',
					'iCantidadAcessoUsuario' => 1,
					'iIdEmpresa' => $u->ID_Empresa
				);
			} else {
				return array(
					'sStatus' => 'warning',
					'sMessage' => 'No se encontro registro almacén: ' . $arrParams['Nu_Codigo_Sunat_ISO'] . ' - ' . $arrParams['No_Usuario'],
					'sClassModal' => 'modal-warning',
				);
			}
		} else {
			return array(
				'sStatus' => 'warning',
				'sMessage' => 'No se encontro registro empresa: ' . $arrParams['Nu_Codigo_Sunat_ISO'] . ' - ' . $arrParams['No_Usuario'],
				'sClassModal' => 'modal-warning',
			);
		}
	}
}
