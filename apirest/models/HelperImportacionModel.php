<?php
class HelperImportacionModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	function obtenerEstadoRegistroArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Activo','No_Class_Estado' => 'success');
		return array('No_Estado' => 'Inactivo','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoNotificacionArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Recibir','No_Class_Estado' => 'success');
		return array('No_Estado' => 'Desactivar','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoRegistroPagosArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'secondary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Confirmado','No_Class_Estado' => 'primary');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Finalizado','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoProcesoUsuarioCursoArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'secondary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Creado','No_Class_Estado' => 'primary');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'No creado','No_Class_Estado' => 'danger');
		else
			return array('No_Estado' => 'Otros','No_Class_Estado' => 'secondary');
	}

	function obtenerTipoServicioArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Trading','No_Class_Estado' => 'success');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'C. Trading','No_Class_Estado' => 'primary');
		else
			return array('No_Estado' => 'Seleccionar','No_Class_Estado' => 'secondary');
	}

	function obtenerEstadoPedidoArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'secondary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Confirmado','No_Class_Estado' => 'primary');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
	}

	function obtenerEstadoPedidoAgenteCompraArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'secondary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Garantizado','No_Class_Estado' => 'primary');//separa con $50 o más para iniciar proceso de cotizado
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Enviado','No_Class_Estado' => 'warning');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Rechazado','No_Class_Estado' => 'danger');
		else if( $iEstado == 5 )
			return array('No_Estado' => 'Aprobado','No_Class_Estado' => 'success');
		else if( $iEstado == 6 )
			return array('No_Estado' => 'Pago 30%','No_Class_Estado' => 'primary');
		else if( $iEstado == 7 )
			return array('No_Estado' => 'Pago 70%','No_Class_Estado' => 'primary');
		else if( $iEstado == 8 )
			return array('No_Estado' => 'Observado','No_Class_Estado' => 'warning');
		else if( $iEstado == 9 )
			return array('No_Estado' => 'Pago servicio','No_Class_Estado' => 'primary');
	}

	//1=Pendiente, 2=Proceso, 3=Cotizado, 4=Producción, 5=Inspección y 6=Entregado
	function obtenerEstadoPedidoAgenteCompraChinaArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'secondary');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'En Proceso','No_Class_Estado' => 'primary');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'Cotizado','No_Class_Estado' => 'success');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'Producción','No_Class_Estado' => 'warning');
		else if( $iEstado == 5 )
			return array('No_Estado' => 'Inspección','No_Class_Estado' => 'secondary');
		else if( $iEstado == 6 )
			return array('No_Estado' => 'Entregado','No_Class_Estado' => 'success');
	}

	function obtenerTipoCanal($iEstado){
		if( $iEstado == 0 )
			return array('No_Estado' => 'Ninguno','No_Class_Estado' => 'secondary');
		else if( $iEstado == 1 )
			return array('No_Estado' => 'Verde','No_Class_Estado' => 'success');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'Naranja','No_Class_Estado' => 'warning');
		else
			return array('No_Estado' => 'Rojo','No_Class_Estado' => 'danger');
	}

	public function getCategorias(){
		$query = "SELECT ID_Familia AS ID, No_Familia AS Nombre FROM familia WHERE ID_Empresa = " . $this->user->ID_Empresa;
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
}
?>