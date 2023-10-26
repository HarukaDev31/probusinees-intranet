<?php
class HelperTiendaVirtualModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function getReporteAlumnosMatriculadosxParams($arrParams){
		$query = "SELECT DISTINCT
ALU.No_Contacto
FROM
matricula_alumno AS MA
JOIN entidad AS ALU ON(MA.ID_Entidad_Alumno = ALU.ID_Entidad)
JOIN horario_clase AS HC ON(MA.ID_Horario_Clase = HC.ID_Horario_Clase)
JOIN dia_semana AS DS ON(DS.ID_Dia_Semana = HC.ID_Dia_Semana)
WHERE
MA.ID_Sede_Musica = " . $arrParams['ID_Sede_Musica'] . "
AND MA.ID_Salon = " . $arrParams['ID_Salon'] . "
AND DS.ID_Dia_Semana = " . $arrParams['ID_Dia_Semana'] . "
AND CONCAT(HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) = '" . $arrParams['Nombre_Hora'] . "'";

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro',
		);		
	}

	public function getCategorias($arrPost){
		$query = "SELECT ID_Familia AS ID, No_Familia AS Nombre FROM familia WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Activar_Familia_Lae_Shop=1 ORDER BY No_Familia";
	
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
			'sClassModal' => 'modal-warning',
		);
	}

	public function getSubCategorias($arrPost){
		$query = "SELECT ID_Sub_Familia AS ID, No_Sub_Familia AS Nombre FROM subfamilia WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND ID_Familia = " . $arrPost['sWhereIdCategoria'] . " AND Nu_Activar_Lae_Shop=1 ORDER BY No_Sub_Familia";
	
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
			'sClassModal' => 'modal-warning',
		);
	}
	
	public function getMarcas(){
		$this->db->where('Nu_Activar_Marca_Lae_Shop', 1);
		$this->db->where('ID_Empresa', $this->empresa->ID_Empresa);
		$this->db->order_by('No_Marca');
		return $this->db->get('marca')->result();
	}

	public function getBancos(){
		$query = "SELECT ID_Banco AS ID, No_Banco_Siglas AS Nombre FROM banco";
	
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
			'sClassModal' => 'modal-warning',
		);
	}

	public function getMedioPagoPagoTransferencia(){
		$query = "SELECT ID_Banco AS ID, No_Banco_Siglas AS Nombre FROM banco";
	
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener datos',
				'sClassModal' => 'modal-danger',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'sStatus' => 'success',
				'sMessage' => 'Registros encontrados',
				'sClassModal' => 'modal-success',
				'arrData' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontraron registros',
			'sClassModal' => 'modal-warning',
		);
	}
	
	public function getAlmacenxTokenTienda($arrPost){
		$query = "SELECT
ALMA.ID_Almacen,
ALMA.ID_Pais,
ALMA.ID_Departamento,
ALMA.ID_Provincia,
ALMA.ID_Distrito,
ALMA.Txt_Direccion_Almacen
FROM
empresa AS EMP
JOIN organizacion AS ORG ON(EMP.ID_Empresa = ORG.ID_Empresa)
JOIN almacen AS ALMA ON(ORG.ID_Organizacion = ALMA.ID_Organizacion)
WHERE
EMP.ID_Empresa=" . $this->user->ID_Empresa . "
AND ALMA.Txt_Token_Lae_Shop != '' LIMIT 1;";
		return $this->db->query($query)->row();
	}
}
