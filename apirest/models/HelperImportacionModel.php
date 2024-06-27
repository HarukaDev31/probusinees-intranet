<?php
class HelperImportacionModel extends CI_Model{
		
	private $defaultAgenteSteps=[];

	private $defautlAgenteChinaSteps=[];
	private $defaultJefeChina=[];

	public function __construct(){
		parent::__construct();
		$this->defaultAgenteSteps=array(
			["name"=>"ORDEN DE COMPRA","iconURL"=>base_url()."assets/icons/orden.png"],
			["name"=>"PAGOS","iconURL"=>base_url()."assets/icons/pagos.png"],
			["name"=>"RECEPCION DE CARGA","iconURL"=>base_url()."assets/icons/recepcion.png"],
			["name"=>"INSPECCIÓN","iconURL"=>base_url()."assets/icons/inspeccion.png"],
			["name"=>"DOCUMENTACIÓN","iconURL"=>base_url()."assets/icons/documentacion.png"]);
		$this->defautlAgenteChinaSteps=array(
			["name"=>"ORDEN DE COMPRA","iconURL"=>base_url()."assets/icons/orden.png"],
			["name"=>"Coordinación","iconURL"=>base_url()."assets/icons/coordinacion.png"],
			["name"=>"RECEPCION DE CARGA","iconURL"=>base_url()."assets/icons/recepcion.png"],
			["name"=>"INSPECCIÓN","iconURL"=>base_url()."assets/icons/inspeccion.png"],
			["name"=>"DOCUMENTACIÓN","iconURL"=>base_url()."assets/icons/documentacion.png"]);
		$this->defaultJefeChina=array(
			["name"=>"ORDEN DE COMPRA","iconURL"=>base_url()."assets/icons/orden.png"],
			["name"=>"Coordinación","iconURL"=>base_url()."assets/icons/coordinacion.png"],
			["name"=>"RECEPCION DE CARGA","iconURL"=>base_url()."assets/icons/recepcion.png"],
			["name"=>"INSPECCIÓN","iconURL"=>base_url()."assets/icons/inspeccion.png"],
			["name"=>"DOCUMENTACIÓN","iconURL"=>base_url()."assets/icons/documentacion.png"]);
	}

	function obtenerEstadoImportacionIntegral($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Si','No_Class_Estado' => 'success');
		return array('No_Estado' => 'No','No_Class_Estado' => 'secondary');
	}

	function obtenerEstadoConsolidadoArray($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Completada','No_Class_Estado' => 'success');
		return array('No_Estado' => 'En proceso','No_Class_Estado' => 'warning');
	}

	function obtenerEstadoTareaChecklist($iEstado){
		if( $iEstado == 1 )
			return array('No_Estado' => 'Completada','No_Class_Estado' => 'success');
		return array('No_Estado' => 'Pendiente','No_Class_Estado' => 'warning');
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
			return array('No_Estado' => 'Esperando','No_Class_Estado' => 'danger');//separa con $50 o más para iniciar proceso de cotizado
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
		else if ($iEstado==10){
			return array('No_Estado' => 'Recibido','No_Class_Estado' => 'primary');

		}
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

	public function getUsuarioChina(){
		$query = "SELECT
USR.ID_Usuario AS ID,
USR.No_Usuario AS Nombre
FROM
usuario AS USR
JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario)
JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo)
WHERE USR.ID_Empresa = " . $this->user->ID_Empresa . " AND GRP.Nu_Tipo_Privilegio_Acceso=2 AND USR.Nu_Estado=1";//2=china
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
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}

	public function getUsuarioJefeChina(){
		$query = "SELECT
USR.ID_Usuario AS ID,
USR.No_Usuario AS Nombre
FROM
usuario AS USR
JOIN grupo_usuario AS GRPUSR ON(USR.ID_Usuario = GRPUSR.ID_Usuario)
JOIN grupo AS GRP ON(GRP.ID_Grupo = GRPUSR.ID_Grupo)
WHERE USR.ID_Empresa = " . $this->user->ID_Empresa . " AND GRP.Nu_Tipo_Privilegio_Acceso=5 AND USR.Nu_Estado=1";//5=Jefes de china
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
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
	
	//Nu_Tipo_Incoterms
	function obtenerIncoterms($iEstado){
		if( $iEstado == 0 )
			return array('No_Estado' => 'Ninguno','No_Class_Estado' => 'secondary');
		else if( $iEstado == 1 )
			return array('No_Estado' => 'EXW','No_Class_Estado' => 'success');
		else if( $iEstado == 2 )
			return array('No_Estado' => 'FOB','No_Class_Estado' => 'success');
		else if( $iEstado == 3 )
			return array('No_Estado' => 'CIF','No_Class_Estado' => 'success');
		else if( $iEstado == 4 )
			return array('No_Estado' => 'DAP','No_Class_Estado' => 'success');
		else if($iEstado==5)
			return array('No_Estado' => 'FCA','No_Class_Estado' => 'success');
		else if($iEstado==6)
			return array('No_Estado' => 'CFR','No_Class_Estado' => 'success');
		
	}
	
	//Nu_Tipo_Transporte_Maritimo
	function obtenerTransporteMaritimo($iEstado){
		if( $iEstado == 0 )
			return array('No_Estado' => 'Ninguno','No_Class_Estado' => 'secondary');
		else if( $iEstado == 1 )
			return array('No_Estado' => 'FCL','No_Class_Estado' => 'success');
		else
			return array('No_Estado' => 'LCL','No_Class_Estado' => 'success');
	}

	public function getShipper(){
		$query = "SELECT ID_Shipper AS ID, No_Shipper AS Nombre FROM shipper WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Estado=1";//2=china
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
				'result' => $arrResponseSQL->result(),
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}
	public function generateOrderSteps($privilegiesArray,$idPedido){
		$stepAgente=[];
		$stepAgenteChina=[];
		$stepJefeChina=[];
		$idPedido=intval($idPedido);
		foreach($privilegiesArray as $key=> $privilegie){
			$agenteIndex=1;
			foreach($this->defaultAgenteSteps as $step){
				if($key=="agente"){
					$stepAgente[]=[
						"id_pedido"=>$idPedido,
						'id_permision_role'=>$privilegiesArray[$key],
						'id_order'=>$agenteIndex,
						'name'=>$step['name'],
						'iconURL'=>$step['iconURL'],
						'status'=>'PENDING'
					];
					$agenteIndex++;
				}
			}
			$agenteChinaIndex=1;
			foreach($this->defautlAgenteChinaSteps as $step){
				if($key=="agente_china"){
					$stepAgenteChina[]=[
						"id_pedido"=>$idPedido,
						'id_permision_role'=>$privilegiesArray[$key],
						'id_order'=>$agenteChinaIndex,
						'name'=>$step['name'],
						'iconURL'=>$step['iconURL'],
						'status'=>'PENDING'
					];
					$agenteChinaIndex++;
				}
			}
			$jefeChinaIndex=1;
			foreach($this->defaultJefeChina as $step){
				if($key=="jefe_china"){
					$stepJefeChina[]=[
						"id_pedido"=>$idPedido,
						'id_permision_role'=>$privilegiesArray[$key],
						'id_order'=>$jefeChinaIndex,
						'name'=>$step['name'],
						'iconURL'=>$step['iconURL'],
						'status'=>'PENDING'
					];
					$jefeChinaIndex++;
				}
			}
		}
		return array_merge($stepAgente,$stepAgenteChina,$stepJefeChina);
	}
}
?>