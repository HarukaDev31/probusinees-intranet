<?php
class AperturaCajaModel extends CI_Model{	
	public function __construct(){
		parent::__construct();
	}
	
	/*
	 Funcion matricular personal y aperturar caja
	 MP = Matricula Personal
	 AC = Apertura de Caja
	*/
    public function agregarMPyAC($arrData){
		$iExisteMatriculaPersonal = 0;
		
		$query="SELECT
Fe_Matricula
FROM
matricula_empleado
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=".$this->session->userdata['almacen']->ID_Almacen."
AND ID_POS=" . $arrData['ID_POS'] . "
ORDER BY
Fe_Matricula DESC
LIMIT 1";
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$dMatricula=$this->db->query($query)->row()->Fe_Matricula;
		
			$query="SELECT
COUNT(*) AS existe
FROM
caja_pos AS CP
JOIN tipo_operacion_caja AS TOC ON(CP.ID_Tipo_Operacion_Caja=TOC.ID_Tipo_Operacion_Caja)
WHERE
CP.ID_Empresa=" . $this->user->ID_Empresa . "
AND CP.ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND CP.ID_Almacen=".$this->session->userdata['almacen']->ID_Almacen."
AND CP.ID_POS=" . $arrData['ID_POS'] . "
AND CP.Nu_Estado=0
AND Fe_Movimiento>='" . $dMatricula . "'
AND TOC.Nu_Tipo=3 LIMIT 1";//Nu_Tipo = 3 Caja aperturada
			$iExisteMatriculaPersonal= $this->db->query($query)->row()->existe;
		}// /. if validacion de matricula personal
		
		if( $iExisteMatriculaPersonal > 0 ){
			return array('sStatus' => 'warning', 'sMessage' => 'La caja se encuentra aperturada' );
		}else{
			if ( empty($arrData['ID_Tipo_Operacion_Caja']) || $this->db->query("SELECT COUNT(*) AS existe FROM tipo_operacion_caja WHERE ID_Tipo_Operacion_Caja = " . $arrData['ID_Tipo_Operacion_Caja'] . " LIMIT 1")->row()->existe == 0){
				return array('sStatus' => 'warning', 'sMessage' => 'No existe el tipo de operación Apertura de caja' );
			} else {
				//Iniciamos la transacción:
				$this->db->trans_begin();
				
				$dFechaHoraActual=dateNow('fecha_hora');//Fecha matricula y movimiento de apertura de caja

				$arrDataMP = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
					'ID_Entidad' => $arrData['ID_Entidad'],
					'ID_POS' => $arrData['ID_POS'],
					'Fe_Matricula' => $dFechaHoraActual,
				);
				$this->db->insert('matricula_empleado', $arrDataMP);
				
				$ID_Matricula_Empleado = $this->db->insert_id();

				$arrDataAC = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
					'ID_Matricula_Empleado' => $ID_Matricula_Empleado,
					'ID_POS' => $arrData['ID_POS'],
					'Fe_Movimiento' => $dFechaHoraActual,
					'ID_Tipo_Operacion_Caja' => $arrData['ID_Tipo_Operacion_Caja'],
					'ID_Moneda' => $arrData['ID_Moneda'],
					'Ss_Total' => $arrData['Ss_Total'],
					'Txt_Nota' => $arrData['Txt_Nota'],
					'Nu_Estado' => 0,//Abierto
					'ID_Enlace_Apertura_Caja_Pos' => 0,
				);
				$this->db->insert('caja_pos', $arrDataAC);

				//Todo lo que se haga a partir de aquí puede guardarse o cancelarse por completo.
				//Después de una serie de procesos, verificamos si hubo algún error:
				if ($this->db->trans_status() === FALSE) {
					//Si la transacción falló por algún motivo, volvemos todo atrás con rollback:
					$this->db->trans_rollback();
					return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar');
				} else {
					$this->db->trans_commit();
					/* TOUR GESTION */
					$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 6);
					//validamos que si complete los siguientes datos
					if($this->db->query("SELECT COUNT(*) AS cantidad FROM caja_pos WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " LIMIT 1")->row()->cantidad > 0){
						//Cambiar estado a completado para el tour
						$data_tour = array('Nu_Estado_Proceso' => 1);
					} else {
						//Cambiar estado a completado para el tour
						$data_tour = array('Nu_Estado_Proceso' => 0);
					}
					$this->db->update('tour_gestion', $data_tour, $where_tour);
					/* END TOUR GESTION */
					return array('sStatus' => 'success', 'sMessage' => 'Registro guardado', 'ID_Matricula_Empleado' => $ID_Matricula_Empleado);
				}
			}// if - else validacion de existe tipo de operacion de caja
		}
	}
}
