<?php
class POSRestauranteModel extends CI_Model{	
	public function __construct(){
		parent::__construct();
	}
    
    public function allEscenarioFirst(){
		$query = "SELECT ID_Escenario_Restaurante FROM escenario_restaurante WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . "
AND Nu_Estado = 1
ORDER BY
ID_Escenario_Restaurante ASC
LIMIT 1";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
    }
    
    public function allEscenario($iIdEscenarioRestaurante){
        $where_id_escenario_restaurante = $iIdEscenarioRestaurante != "0" ? ' AND ID_Escenario_Restaurante = ' . $iIdEscenarioRestaurante : '';
		$query = "SELECT *
FROM
escenario_restaurante
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . $where_id_escenario_restaurante . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . "
AND Nu_Estado = 1
ORDER BY
No_Escenario_Restaurante";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
    }
    
    public function addEscenario($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM escenario_restaurante WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Organizacion =".$data['ID_Organizacion']." AND ID_Almacen =".$data['ID_Almacen']." AND No_Escenario_Restaurante='" . $data['No_Escenario_Restaurante'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert('escenario_restaurante', $data) > 0 )
                return array('status' => 'success', 'message' => 'Registro guardado', 'iIdEscenarioRestaurante' => $this->db->insert_id());
		}
		return array('status' => 'error', 'message' => 'Error al insertar');
    }
    
    public function verEscenario($iIdMesaRestaurante){
		$query = "SELECT * FROM escenario_restaurante WHERE ID_Escenario_Restaurante = " . $iIdMesaRestaurante . " LIMIT 1";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
    }
    
    public function updEscenario($where, $data, $ENo_Escenario_Restaurante){
		if( $ENo_Escenario_Restaurante != $data['No_Escenario_Restaurante'] && $this->db->query("SELECT COUNT(*) AS existe FROM escenario_restaurante WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Organizacion =".$data['ID_Organizacion']." AND ID_Almacen =".$data['ID_Almacen']." AND No_Escenario_Restaurante='" . $data['No_Escenario_Restaurante'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update('escenario_restaurante', $data, $where) > 0 )
                return array('status' => 'success', 'message' => 'Registro actualizado', 'iIdEscenarioRestaurante' => $where['ID_Escenario_Restaurante']);
		}
		return array('status' => 'error', 'message' => 'Error al insertar');
    }
    
	public function eliminarEscenario($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM mesa_restaurante WHERE ID_Escenario_Restaurante=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El escenario tiene mesa(s) asignada(s)');
		}else{
			$this->db->where('ID_Escenario_Restaurante', $ID);
            $this->db->delete('escenario_restaurante');
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
    
    public function addEscenarioMesa($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM mesa_restaurante WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Organizacion =".$data['ID_Organizacion']." AND ID_Almacen =".$data['ID_Almacen']." AND ID_Escenario_Restaurante =".$data['ID_Escenario_Restaurante']." AND No_Mesa_Restaurante='" . $data['No_Mesa_Restaurante'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert('mesa_restaurante', $data) > 0 )
                return array('status' => 'success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'message' => 'Error al insertar');
    }
    
    public function verEscenarioMesa($iIdMesaRestaurante){
		$query = "SELECT * FROM mesa_restaurante WHERE ID_Mesa_Restaurante = " . $iIdMesaRestaurante . " LIMIT 1";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
    }
    
    public function updEscenarioMesa($where, $data, $ENo_Mesa_Restaurante){
		if( $ENo_Mesa_Restaurante != $data['No_Mesa_Restaurante'] && $this->db->query("SELECT COUNT(*) AS existe FROM mesa_restaurante WHERE ID_Empresa =".$data['ID_Empresa']." AND ID_Organizacion =".$data['ID_Organizacion']." AND ID_Almacen =".$data['ID_Almacen']." AND ID_Escenario_Restaurante =".$data['ID_Escenario_Restaurante']." AND No_Mesa_Restaurante='" . $data['No_Mesa_Restaurante'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'message' => 'El registro ya existe');
		}else{
		    if ( $this->db->update('mesa_restaurante', $data, $where) > 0 )
                return array('status' => 'success', 'message' => 'Registro actualizado');
		}
		return array('status' => 'error', 'message' => 'Error al insertar');
    }
    
	public function eliminarMesa($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM pedido_cabecera WHERE ID_Mesa=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La mesa tiene movimiento(s), no se puede eliminar pero pueden desactivarla');
		} else {
			$this->db->where('ID_Mesa_Restaurante', $ID);
            $this->db->delete('mesa_restaurante');
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
	}
    
    public function allEscenarioMesas($iIdEscenarioRestaurante){
//CONCAT(FLOOR(HOUR(TIMEDIFF(NOW(), PC.Fe_Emision_Hora))/24), ' día(s) ',  MOD(HOUR(TIMEDIFF(NOW(), PC.Fe_Emision_Hora)), 24), ' H ',  MINUTE(TIMEDIFF(NOW(), PC.Fe_Emision_Hora)), ' m') AS Fe_Transcurrida
//13 días 16 H 23 m
		$query = "SELECT
MR.*,
PC.ID_Pedido_Cabecera,
USR.No_Nombres_Apellidos AS No_Mesero,
CLI.No_Entidad AS No_Cliente,
PC.Nu_Cantidad_Personas_Restaurante,
PC.Ss_Total,
MONE.No_Signo,
FLOOR(HOUR(TIMEDIFF(NOW(), PC.Fe_Emision_Hora))/24) AS Fe_Transcurrida_Dia,
MOD(HOUR(TIMEDIFF(NOW(), PC.Fe_Emision_Hora)), 24) AS Fe_Transcurrida_Hora,
MINUTE(TIMEDIFF(NOW(), PC.Fe_Emision_Hora)) AS Fe_Transcurrida_Minuto
FROM
mesa_restaurante AS MR
LEFT JOIN pedido_cabecera AS PC ON(PC.ID_Mesa = MR.ID_Mesa_Restaurante)
LEFT JOIN entidad AS CLI ON(CLI.ID_Entidad = PC.ID_Entidad)
LEFT JOIN usuario AS USR ON(USR.ID_Usuario = PC.ID_Mesero)
LEFT JOIN moneda AS MONE ON(MONE.ID_Moneda = PC.ID_Moneda)
WHERE
MR.ID_Empresa=" . $this->empresa->ID_Empresa . "
AND MR.ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND MR.ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . "
AND MR.ID_Escenario_Restaurante =  " . $iIdEscenarioRestaurante . "
AND MR.Nu_Estado = 1
ORDER BY
MR.No_Mesa_Restaurante;";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
	}
	
    public function allMesasRestaurante(){
		$query = "SELECT * FROM mesa_restaurante WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Organizacion=" . $this->empresa->ID_Organizacion . " AND ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . " AND Nu_Estado = 1 ORDER BY No_Mesa_Restaurante;";
        if ( !$this->db->simple_query($query) ){
            $error = $this->db->error();
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query
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
            'message' => 'No se encontráron registro',
        );
    }
	
	public function liberarMesa($arrPost){
		$this->db->trans_begin();

		$this->db->where('ID_Pedido_Cabecera', $arrPost['ID_Pedido_Cabecera']);
		$this->db->delete('pedido_detalle');
		
		$this->db->where('ID_Pedido_Cabecera', $arrPost['ID_Pedido_Cabecera']);
		$this->db->delete('pedido_cabecera');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al liberar mesa');
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'sMessage' => 'Mesa liberada correctamente');
		}
	}

	public function imprimirPreCuentaYGuardar($arrPost){
		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . " LIMIT 1")->row();

		if ($objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);

		$this->db->trans_begin();

		if ( $arrPost['arrCabecera']['ID_Pedido_Cabecera'] > 0 ) {//Esto quiere decir que el pedido ya fue previamente guardado
			$this->db->where('ID_Pedido_Cabecera', $arrPost['arrCabecera']['ID_Pedido_Cabecera']);
			$this->db->delete('pedido_detalle');
			
			$this->db->where('ID_Pedido_Cabecera', $arrPost['arrCabecera']['ID_Pedido_Cabecera']);
			$this->db->delete('pedido_cabecera');
		}

		$Last_ID_Entidad = $arrPost['arrCabecera']['ID_Entidad'];
		// Cliente ya esta registrado en BD
		if ( !empty($Last_ID_Entidad) ){
			$arrClienteBD = $this->db->query("SELECT ID_Departamento, ID_Provincia, ID_Distrito, Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $Last_ID_Entidad . " LIMIT 1")->result();
			$Nu_Celular_Entidad = '';
			if ( strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
				$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
				$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
			}
			if (
				(!empty($arrPost['arrCabecera']['sDireccionDelivery']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrPost['arrCabecera']['sDireccionDelivery']) ||
				(!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) ||
				(!empty($arrPost['arrCliente']['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrPost['arrCliente']['Txt_Email_Entidad'])
			) {
				$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['arrCabecera']['sDireccionDelivery'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrPost['arrCliente']['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
				$this->db->query($sql);
			}// /. if cambiar celular o correo
		} // /. if cliente existe en BD

		//cambiamos por clientes varios cuando colocan en el campo nombre de 0 a 3 digitos, cuando sea mayor no hacemos nada
		$sNombreCliente = trim($arrPost['arrCliente']['No_Entidad']);
		$sNombreCliente = strip_tags($arrPost['arrCliente']['No_Entidad']);
		if($arrPost['arrCabecera']['ID_Tipo_Documento'] != '3' && strlen($sNombreCliente) < 4)
			$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;

		if (
			empty($arrPost['arrCabecera']['ID_Entidad'])
			&& (!empty($arrPost['arrCliente']['Nu_Documento_Identidad']) || !empty($arrPost['arrCliente']['No_Entidad']))
			&& (($arrPost['arrCabecera']['ID_Tipo_Documento'] == '3' && strlen($sNombreCliente) > 2) || ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '3' && strlen($sNombreCliente) > 3))
		) {//3=Cliente nuevo
			$iTipoDocumentoIdentidad = $arrPost['arrCliente']['ID_Tipo_Documento_Identidad'];
			$sNumeroDocumentoIdentidad = trim($arrPost['arrCliente']['Nu_Documento_Identidad']);
			if ( $iTipoDocumentoIdentidad == '2' && strlen($sNumeroDocumentoIdentidad) == '8' )
				$iTipoDocumentoIdentidad = '2';

			if ( ($iTipoDocumentoIdentidad == '1' || $iTipoDocumentoIdentidad == '2') && empty($sNumeroDocumentoIdentidad) ) {
				$iTipoDocumentoIdentidad='1';
				$sNumeroDocumentoIdentidad='0';
			}

			$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $iTipoDocumentoIdentidad . " AND Nu_Documento_Identidad = '" . $sNumeroDocumentoIdentidad . "' AND No_Entidad = '" . $arrPost['arrCliente']['No_Entidad'] . "' LIMIT 1";
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$arrData = $arrResponseSQL->result();
				$Last_ID_Entidad = $arrData[0]->ID_Entidad;
			} else {
				$Nu_Celular_Entidad = '';
				if ( strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}

				$arrCliente = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'Nu_Tipo_Entidad' => 0,//0=Cliente
					'ID_Tipo_Documento_Identidad' => $iTipoDocumentoIdentidad,
					'Nu_Documento_Identidad' => $sNumeroDocumentoIdentidad,
					'No_Entidad' => (!empty($arrPost['arrCliente']['No_Entidad']) ? $arrPost['arrCliente']['No_Entidad'] : $sNumeroDocumentoIdentidad),
					'Nu_Estado' => $arrPost['arrCliente']['Nu_Estado'],
					'Nu_Celular_Entidad' => $Nu_Celular_Entidad,
					'Txt_Email_Entidad'	=> $arrPost['arrCliente']['Txt_Email_Entidad'],
					'Txt_Direccion_Entidad' => $arrPost['arrCabecera']['sDireccionDelivery']
				);
				if ($this->db->insert('entidad', $arrCliente) > 0) {
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array('sStatus' => 'danger', 'sMessage' => 'No se puede registrar Cliente');
				}
			}
		}// ./ if cliente nuevo
		
		if( ($arrPost['arrCabecera']['ID_Tipo_Documento'] == 2 || $arrPost['arrCabecera']['ID_Tipo_Documento'] == 4) && empty($arrPost['arrCliente']['Nu_Documento_Identidad']) && empty($arrPost['arrCliente']['No_Entidad']) )//Obtener clientes varios
			$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;

		if(empty($Last_ID_Entidad)) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Sin entidad');
		}

		$fRetencion = 0.00;
		$fDetraccion = 0.00;
		$fPorcentajeDetraccion = $arrPost['arrCabecera']['Po_Detraccion'];

		$arrVentaCabecera = array(
			'ID_Empresa'				=> $this->empresa->ID_Empresa,
			'ID_Organizacion'			=> $this->empresa->ID_Organizacion,
			'ID_Almacen'			    => $this->session->userdata['almacen']->ID_Almacen,
			'ID_Entidad'				=> $Last_ID_Entidad,
			'Fe_Emision'				=> dateNow('fecha'),
			'Fe_Emision_Hora'			=> dateNow('fecha_hora'),
			'ID_Tipo_Documento'			=> $arrPost['arrCabecera']['ID_Tipo_Documento'],
			'ID_Mesero'					=> $this->session->usuario->ID_Usuario,
			'ID_Mesa'					=> $arrPost['arrCabecera']['ID_Mesa'],
			'ID_Moneda'					=> $arrPost['arrCabecera']['ID_Moneda'],//Soles
			'ID_Medio_Pago'				=> 0,
			'ID_Matricula_Empleado'	    => $arrPost['arrCabecera']['ID_Matricula_Empleado'],
			'Ss_Total' => $arrPost['arrCabecera']['Ss_Total'],
			'Nu_Estado' => 1,//Completado
			'Txt_Glosa' => $arrPost['arrCabecera']['sGlosa'],
			'No_Formato_PDF' => $arrPost['arrCabecera']['No_Formato_PDF'],
			'Po_Descuento' => ($arrPost['arrCabecera']['iTipoDescuento'] == 2 && $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] > 0.00 ? $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] : 0.00),
			'Ss_Descuento' => $arrPost['arrCabecera']['Ss_Descuento_Total'],
			'Ss_Descuento_Impuesto' => $arrPost['arrCabecera']['Ss_Descuento_Impuesto'],
			'ID_Distrito_Delivery' => 0,
			'Fe_Entrega' => ToDate($arrPost['arrCabecera']['Fe_Entrega']),
			'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
			'Nu_Estado_Despacho_Pos' => ($arrPost['arrCabecera']['Nu_Tipo_Recepcion'] == 5 ? 3 : 0),
			'ID_Transporte_Delivery' => $arrPost['arrCabecera']['ID_Transporte_Delivery'],
			'No_Entidad_Order_Address_Entry' => '',
			'Nu_Celular_Entidad_Order_Address_Entry' => '',
			'Txt_Direccion_Entidad_Order_Address_Entry' => '',
			'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' => '',
			'Nu_Documento_Identidad' => '',
			'No_Orden_Compra_FE' => $arrPost['arrCabecera']['No_Orden_Compra_FE'],
			'No_Placa_FE' => $arrPost['arrCabecera']['No_Placa_FE'],
			'Txt_Garantia' => strtoupper($arrPost['arrCabecera']['Txt_Garantia']),
			'ID_Canal_Venta_Tabla_Dato' => $arrPost['arrCabecera']['ID_Canal_Venta_Tabla_Dato'],
			'Nu_Retencion' => $arrPost['arrCabecera']['Nu_Retencion'],
			'Ss_Retencion' => $fRetencion,
			'Ss_Detraccion' => $fDetraccion,
			'Po_Detraccion' => $fPorcentajeDetraccion,
			'Nu_Cantidad_Personas_Restaurante' => $arrPost['arrCabecera']['Nu_Cantidad_Personas_Restaurante'],
		);

		if ( $arrPost['arrCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
			$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($arrPost['arrCabecera']['ID_Lista_Precio_Cabecera'])));

		$this->db->insert('pedido_cabecera', $arrVentaCabecera);
		$Last_ID_Pedido_Cabecera = $this->db->insert_id();
		
		$sTidoDocumento = 'PRECUENTA - ' . $Last_ID_Pedido_Cabecera;

		// URL para enviar correo y para consultar por fuera sin session
		// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
		//if($arrPost['arrCabecera']['ID_Tipo_Documento']==2) {//2=Nota de venta
			$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarPreCuentaPDF/' . $Last_ID_Pedido_Cabecera;
			$sql = "UPDATE pedido_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Pedido_Cabecera=" . $Last_ID_Pedido_Cabecera;
			$this->db->query($sql);
		//}

		foreach($arrPost['arrDetalle'] as $row) {
			//regalo o gratuita
			if ($row['iRegaloInafectoBonificacion']==1) {
				$row['ID_Impuesto_Cruce_Documento'] = $arrPost['arrCabecera']['id_impuesto_gratuita_inafecto_bonificacion'];
				$row['fImpuestoItem'] = 0;
				$row['fSubtotalItem'] = $row['Ss_Total_Producto'];
			}

			$pedido_detalle[] = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Pedido_Cabecera' => $Last_ID_Pedido_Cabecera,
				'ID_Producto' => $row['ID_Producto'],
				'Qt_Producto' => ($row['Qt_Producto'] > 0.00 ? $this->security->xss_clean($row['Qt_Producto']) : 1),
				'Ss_Precio' => $row['Ss_Precio'],
				'Ss_SubTotal' => $row['fSubtotalItem'],
				'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
				'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
				'Po_Descuento' => $row['fDescuentoPorcentajeItem'],
				'Txt_Nota' => $row['Txt_Nota'],
				'ID_Impuesto_Cruce_Documento' => $row['ID_Impuesto_Cruce_Documento'],
				'Ss_Impuesto' => $row['fImpuestoItem'],
				'Ss_Total' => $row['Ss_Total_Producto'],
				'Ss_Icbper' => $row['fIcbperItem'],
			);
		}
		$this->db->insert_batch('pedido_detalle', $pedido_detalle);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
		} else {
			$this->db->trans_commit();
			
			$arrResponseWhatsapp = array(
				'No_Empresa_Comercial' => $this->empresa->No_Empresa_Comercial,
				'No_Empresa' => $this->empresa->No_Empresa,
				'Documento' => $sTidoDocumento,
				'Fecha_Emision' => ToDateBD(dateNow('fecha')),
				'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
				'No_Tipo_Recepcion' => $arrPost['arrCabecera']['No_Tipo_Recepcion'],
				'sDireccionDelivery' => $arrPost['arrCabecera']['sDireccionDelivery'],
				'Total' => $arrPost['arrCabecera']['Ss_Total'],
			);

			$arrDetalle = array('arrDetalle' => $arrPost['arrDetalle']);
			$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrDetalle);
			//url de pdf nota de venta interna para whatsapp
			$arrResponseAdicionales = array('enlace_del_pdf' => $sUrlPDFNotaVentaInternoLae);
			$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrResponseAdicionales);

			return array('sStatus' => 'success', 'sMessage' => 'Venta guardada', 'iIdDocumentoCabecera' => $Last_ID_Pedido_Cabecera, 'arrResponseFE' => $arrResponseWhatsapp);
		}
	}

	public function agregarVentaPos($arrPost){
		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . " LIMIT 1")->row();

		if ($objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);
			
		$this->db->trans_begin();

		$query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=" . $this->session->userdata['almacen']->ID_Almacen . "
AND ID_Tipo_Documento=" . $arrPost['arrCabecera']['ID_Tipo_Documento'] . "
AND Nu_Estado=1
AND ID_POS=".$this->session->userdata['arrDataPersonal']['arrData'][0]->ID_POS." LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) )
			return array('sStatus' => 'danger', 'sMessage' => 'Configurar en Ventas > Series para ' . $sTidoDocumento . ' y Caja ' . $this->session->userdata['arrDataPersonal']['arrData'][0]->Nu_Caja . ', no existe');

		if ($this->db->query("SELECT COUNT(*) AS existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrPost['arrCabecera']['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe venta ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . '. Cambiar correlativo en ventas y clientes > series' );
		} else {
			if ( $arrPost['arrCabecera']['ID_Pedido_Cabecera'] > 0 ) {//Esto quiere decir que el pedido ya fue previamente guardado
				$this->db->where('ID_Pedido_Cabecera', $arrPost['arrCabecera']['ID_Pedido_Cabecera']);
				$this->db->delete('pedido_detalle');
				
				$this->db->where('ID_Pedido_Cabecera', $arrPost['arrCabecera']['ID_Pedido_Cabecera']);
				$this->db->delete('pedido_cabecera');
			}

			$Last_ID_Entidad = $arrPost['arrCabecera']['ID_Entidad'];
			// Cliente ya esta registrado en BD
			if ( !empty($Last_ID_Entidad) ){
				$arrClienteBD = $this->db->query("SELECT ID_Departamento, ID_Provincia, ID_Distrito, Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $Last_ID_Entidad . " LIMIT 1")->result();
				$Nu_Celular_Entidad = '';
				if ( strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if (
					(!empty($arrPost['arrCabecera']['sDireccionDelivery']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrPost['arrCabecera']['sDireccionDelivery']) ||
					(!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) ||
					(!empty($arrPost['arrCliente']['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrPost['arrCliente']['Txt_Email_Entidad'])
				) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['arrCabecera']['sDireccionDelivery'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrPost['arrCliente']['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
					$this->db->query($sql);
				}// /. if cambiar celular o correo
			} // /. if cliente existe en BD

			//cambiamos por clientes varios cuando colocan en el campo nombre de 0 a 3 digitos, cuando sea mayor no hacemos nada
			$sNombreCliente = trim($arrPost['arrCliente']['No_Entidad']);
			$sNombreCliente = strip_tags($arrPost['arrCliente']['No_Entidad']);
			if($arrPost['arrCabecera']['ID_Tipo_Documento'] != '3' && strlen($sNombreCliente) < 4)
				$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;

			if (
				empty($arrPost['arrCabecera']['ID_Entidad'])
				&& (!empty($arrPost['arrCliente']['Nu_Documento_Identidad']) || !empty($arrPost['arrCliente']['No_Entidad']))
				&& (($arrPost['arrCabecera']['ID_Tipo_Documento'] == '3' && strlen($sNombreCliente) > 2) || ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '3' && strlen($sNombreCliente) > 3))
			) {//3=Cliente nuevo
				$iTipoDocumentoIdentidad = $arrPost['arrCliente']['ID_Tipo_Documento_Identidad'];
				$sNumeroDocumentoIdentidad = trim($arrPost['arrCliente']['Nu_Documento_Identidad']);
				if ( $iTipoDocumentoIdentidad == '2' && strlen($sNumeroDocumentoIdentidad) == '8' )
					$iTipoDocumentoIdentidad = '2';

				if ( ($iTipoDocumentoIdentidad == '1' || $iTipoDocumentoIdentidad == '2') && empty($sNumeroDocumentoIdentidad) ) {
					$iTipoDocumentoIdentidad='1';
					$sNumeroDocumentoIdentidad='0';
				}

				$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $iTipoDocumentoIdentidad . " AND Nu_Documento_Identidad = '" . $sNumeroDocumentoIdentidad . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrPost['arrCliente']['No_Entidad']) . "' LIMIT 1";
				$arrResponseSQL = $this->db->query($query);
				if ( $arrResponseSQL->num_rows() > 0 ){
					$arrData = $arrResponseSQL->result();
					$Last_ID_Entidad = $arrData[0]->ID_Entidad;
				} else {
					$Nu_Celular_Entidad = '';
					if ( strlen($arrPost['arrCliente']['Nu_Celular_Entidad']) == 11){
						$Nu_Celular_Entidad = explode(' ', $arrPost['arrCliente']['Nu_Celular_Entidad']);
						$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
					}

					$arrCliente = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'Nu_Tipo_Entidad' => 0,//0=Cliente
						'ID_Tipo_Documento_Identidad' => $iTipoDocumentoIdentidad,
						'Nu_Documento_Identidad' => $sNumeroDocumentoIdentidad,
						'No_Entidad' => (!empty($arrPost['arrCliente']['No_Entidad']) ? $arrPost['arrCliente']['No_Entidad'] : $sNumeroDocumentoIdentidad),
						'Nu_Estado' => $arrPost['arrCliente']['Nu_Estado'],
						'Nu_Celular_Entidad' => $Nu_Celular_Entidad,
						'Txt_Email_Entidad'	=> $arrPost['arrCliente']['Txt_Email_Entidad'],
						'Txt_Direccion_Entidad' => $arrPost['arrCabecera']['sDireccionDelivery']
					);
					if ($this->db->insert('entidad', $arrCliente) > 0) {
						$Last_ID_Entidad = $this->db->insert_id();
					} else {
						$this->db->trans_rollback();
						return array('sStatus' => 'danger', 'sMessage' => 'No se puede registrar Cliente');
					}
				}
			}// ./ if cliente nuevo

			if( ($arrPost['arrCabecera']['ID_Tipo_Documento'] == 2 || $arrPost['arrCabecera']['ID_Tipo_Documento'] == 4) && empty($arrPost['arrCliente']['Nu_Documento_Identidad']) && empty($arrPost['arrCliente']['No_Entidad']) )//Obtener clientes varios
				$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;
	
			if(empty($Last_ID_Entidad)) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Sin entidad');
			}

			//Generar venta
			$Nu_Correlativo = 0;
			$Fe_Year = dateNow('año');
			$Fe_Month = dateNow('mes');
			
			if ( $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ) {
				// Obtener correlativo			
				if($this->db->query("SELECT COUNT(*) existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo=Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Tipo_Asiento=1
AND Fe_Year='" . $Fe_Year. "'
AND Fe_Month='" . $Fe_Month . "'";
				$this->db->query($sql_correlativo_libro_sunat);
			} else {
				$sql_correlativo_libro_sunat = "INSERT INTO correlativo_tipo_asiento (
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->empresa->ID_Empresa . ",
1,
'" . $Fe_Year . "',
'" . $Fe_Month . "',
1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
				// /. Obtener correlativo
			}// if validacion correlativo documento interno
			
			//FECHA DE VENCIMIENTO
			$dVencimiento = $arrPost['arrCabecera']['Fe_Vencimiento'];
			$arrFechaVencimiento = explode('/', $dVencimiento);		
			$dVencimiento = dateNow('fecha');
			if(count($arrFechaVencimiento) == 3 && checkdate($arrFechaVencimiento[1], $arrFechaVencimiento[0], $arrFechaVencimiento[2]))
				$dVencimiento = ToDate($arrPost['arrCabecera']['Fe_Vencimiento']);
			
			//set regalo o gratuita
			$fTotalGratuita = 0.00;
			foreach($arrPost['arrDetalle'] as $row) {
				if ($row['iTipoImpuestoSunat'] == 4 && $row['iRegaloInafectoBonificacion']==0)
					$fTotalGratuita += $row['Ss_Total_Producto'];
				if ($row['iRegaloInafectoBonificacion']==1)
					$fTotalGratuita += $row['Ss_Total_Producto'];
			}
			$arrPost['arrCabecera']['Ss_Total'] += $fTotalGratuita;//lo sumo porque luego hay un query en todos los reportes donde busco ese monto para luego restarlo

			$fTotalxMP = 0.00;
			$fVuelto = 0.00;
			$fRetencion = 0.00;
			$fDetraccion = 0.00;
			$fPorcentajeDetraccion = $arrPost['arrCabecera']['Po_Detraccion'];
			foreach ($arrPost['arrFormaPago'] as $row) {
				$fTotalxMP += $row['Ss_Total'];
				if ( $fTotalxMP > ($arrPost['arrCabecera']['Ss_Total'] - $fTotalGratuita) )
					$fVuelto = $fTotalxMP - ($arrPost['arrCabecera']['Ss_Total'] - $fTotalGratuita);
				if ( $row['iTipoVista'] == '1' ) {//Credito
					$fVuelto = $row['Ss_Total'];
					
					$dEmision = new DateTime(dateNow('fecha') . " 00:00:00");
					$dVencimiento_Comparar = new DateTime($dVencimiento . " 00:00:00");
					if($dVencimiento_Comparar<=$dEmision){
						$this->db->trans_rollback();
						return array('sStatus' => 'warning', 'sMessage' => 'La F. Vencimiento debe de ser mayor a la fecha de hoy > ' . ToDateBD(dateNow('fecha')));
					}

					if ($arrPost['arrCabecera']['Nu_Retencion'] == '1') {
						$fRetencion = (($arrPost['arrCabecera']['Ss_Total'] - $fTotalGratuita) * 0.03);
						$arrPost['arrCabecera']['Ss_Total_Saldo'] = ($arrPost['arrCabecera']['Ss_Total_Saldo'] - $fRetencion);
					}

					if ($arrPost['arrCabecera']['Nu_Detraccion'] == '1' && ($arrPost['arrCabecera']['Ss_Total'] - $fTotalGratuita) > 700) {
						$fDetraccion = (($arrPost['arrCabecera']['Ss_Total'] - $fTotalGratuita) * ($fPorcentajeDetraccion / 100));
						$fDetraccion = round($fDetraccion, 0, PHP_ROUND_HALF_UP);
						$arrPost['arrCabecera']['Ss_Total_Saldo'] = ($arrPost['arrCabecera']['Ss_Total_Saldo'] - $fDetraccion);
					}
				}
			}

			$arrVentaCabecera = array(
				'ID_Empresa'				=> $this->empresa->ID_Empresa,
				'ID_Organizacion'			=> $this->empresa->ID_Organizacion,
				'ID_Almacen'			    => $this->session->userdata['almacen']->ID_Almacen,
				'ID_Entidad'				=> $Last_ID_Entidad,
				'ID_Matricula_Empleado'	    => $arrPost['arrCabecera']['ID_Matricula_Empleado'],
				'ID_Tipo_Asiento'			=> 1,//Venta
				'ID_Tipo_Documento'			=> $arrPost['arrCabecera']['ID_Tipo_Documento'],
				'ID_Serie_Documento_PK'		=> $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento'		=> $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento'		=> $arrSerieDocumento->Nu_Numero_Documento,
				'Fe_Emision'				=> dateNow('fecha'),
				'Fe_Emision_Hora'			=> dateNow('fecha_hora'),
				'ID_Mesero'					=> $this->session->usuario->ID_Usuario,
				'ID_Mesa'					=> $arrPost['arrCabecera']['ID_Mesa'],
				'ID_Moneda'					=> $arrPost['arrCabecera']['ID_Moneda'],//Soles
				'ID_Medio_Pago'				=> $arrPost['arrFormaPago'][0]['ID_Medio_Pago'],
				'Fe_Vencimiento'			=> $dVencimiento,
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => $arrPost['arrCabecera']['Ss_Total'],
				'Ss_Total_Saldo' => $arrPost['arrCabecera']['Ss_Total_Saldo'],
				'Ss_Vuelto' => $fVuelto,
				'Nu_Correlativo' => $Nu_Correlativo,
				'Nu_Estado' => 6,//Completado
				'Nu_Transporte_Lavanderia_Hoy' => 0,
				'Nu_Estado_Lavado' => 0,
				'Fe_Entrega' => ToDate($arrPost['arrCabecera']['Fe_Entrega']),
				'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
				'Nu_Estado_Despacho_Pos' => ($arrPost['arrCabecera']['Nu_Tipo_Recepcion'] == 5 ? 3 : 0),
				'ID_Transporte_Delivery' => $arrPost['arrCabecera']['ID_Transporte_Delivery'],
				'Txt_Direccion_Delivery' => $arrPost['arrCabecera']['sDireccionDelivery'],
				'Txt_Glosa' => $arrPost['arrCabecera']['sGlosa'],
				'No_Formato_PDF' => $arrPost['arrCabecera']['No_Formato_PDF'],
				'Po_Descuento' => ($arrPost['arrCabecera']['iTipoDescuento'] == 2 && $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] > 0.00 ? $arrPost['arrCabecera']['Ss_Descuento_Total_Input'] : 0.00),
				'Ss_Descuento' => $arrPost['arrCabecera']['Ss_Descuento_Total'],
				'Ss_Descuento_Impuesto' => $arrPost['arrCabecera']['Ss_Descuento_Impuesto'],
				'No_Orden_Compra_FE' => $arrPost['arrCabecera']['No_Orden_Compra_FE'],
				'No_Placa_FE' => $arrPost['arrCabecera']['No_Placa_FE'],
				'Txt_Garantia' => strtoupper($arrPost['arrCabecera']['Txt_Garantia']),
				'Nu_Detraccion' => $arrPost['arrCabecera']['Nu_Detraccion'],
				'ID_Canal_Venta_Tabla_Dato' => $arrPost['arrCabecera']['ID_Canal_Venta_Tabla_Dato'],
				'ID_Sunat_Tipo_Transaction' => $arrPost['arrCabecera']['ID_Sunat_Tipo_Transaction'],
				'Nu_Retencion' => $arrPost['arrCabecera']['Nu_Retencion'],
				'Ss_Retencion' => $fRetencion,
				'Ss_Detraccion' => $fDetraccion,
				'Po_Detraccion' => $fPorcentajeDetraccion,
			);
			
			if ( $arrPost['arrCabecera']['ID_Lista_Precio_Cabecera'] != 0 )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Lista_Precio_Cabecera" => $this->security->xss_clean($arrPost['arrCabecera']['ID_Lista_Precio_Cabecera'])));

			$sTidoDocumento .= ($arrPost['arrCabecera']['ID_Tipo_Documento'] != 2 ? ' Electrónica' : '') .  ' - ' . $arrVentaCabecera['ID_Serie_Documento'] . ' - ' . $arrVentaCabecera['ID_Numero_Documento'];

			$this->db->insert('documento_cabecera', $arrVentaCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();

			// URL para enviar correo y para consultar por fuera sin session
			// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
			if($arrVentaCabecera['ID_Tipo_Documento']==2) {//2=Nota de venta
				$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/' . $Last_ID_Documento_Cabecera;
				$sql = "UPDATE documento_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}
			
			foreach($arrPost['arrDetalle'] as $row) {
				$Qt_Producto = ($row['Qt_Producto'] > 0.00 ? $this->security->xss_clean($row['Qt_Producto']) : 1);

				if ($this->empresa->Nu_Validar_Stock==1){//Activada la validación de stock
					$objItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto, No_Producto FROM producto WHERE ID_Producto =".$row['ID_Producto']." LIMIT 1")->row();

					if ( $objItem->Nu_Tipo_Producto == 1 ){
						if ( $objItem->Nu_Compuesto == 0 ){
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$row['ID_Producto']." AND ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $Qt_Producto ){
									$this->db->trans_rollback();
									return array('sStatus' => 'warning', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('sStatus' => 'warning', 'sMessage' => 'No tiene stock el producto > ' . $objItem->No_Producto);
							}
						} else {
							$query = "SELECT
							ENLAPRO.ID_Producto,
							ENLAPRO.Qt_Producto_Descargar
							FROM
							enlace_producto AS ENLAPRO
							JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
							WHERE
							ENLAPRO.ID_Producto_Enlace = " . $row['ID_Producto'];
							$arrItemsEnlazados = $this->db->query($query)->result();
							
							foreach($arrItemsEnlazados as $row_enlace) {
								$ID_Producto_Enlace = $row_enlace->ID_Producto;
								$fStockVenta = ($Qt_Producto * $row_enlace->Qt_Producto_Descargar);
								$objItem = $this->db->query("SELECT No_Producto FROM producto WHERE ID_Producto =".$ID_Producto_Enlace." LIMIT 1")->row();
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $this->session->userdata['almacen']->ID_Almacen . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
										$this->db->trans_rollback();
										return array('sStatus' => 'warning', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
									}
								} else {
									$this->db->trans_rollback();
									return array('sStatus' => 'warning', 'sMessage' => 'Producto enlazado no tiene stock > ' . $objItem->No_Producto);
								}
							}
						}
					}
				}

				//regalo o gratuita
				if ($row['iRegaloInafectoBonificacion']==1) {
					$row['ID_Impuesto_Cruce_Documento'] = $arrPost['arrCabecera']['id_impuesto_gratuita_inafecto_bonificacion'];
					$row['fImpuestoItem'] = 0;
					$row['fSubtotalItem'] = $row['Ss_Total_Producto'];
				}

				$fSubTotalItem = ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ? $row['fSubtotalItem'] : $row['Ss_Total_Producto']);
				$fImpuestoItem = ($arrPost['arrCabecera']['ID_Tipo_Documento'] != '2' ? $row['fImpuestoItem'] : 0.00);

				$fCalculoTotal = round($row['Ss_Total_Producto'] / $row['fImpuestoConfigurado'], 6);
				if( $fSubTotalItem < $fCalculoTotal ) {
					$fSubTotalItem = $fCalculoTotal;
					$fImpuestoItem = ($row['Ss_Total_Producto'] - $fCalculoTotal);
				}

				$documento_detalle[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $row['ID_Producto'],
					'Qt_Producto' => $Qt_Producto,
					'Ss_Precio' => $row['Ss_Precio'],
					'Ss_SubTotal' => $fSubTotalItem,
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['fDescuentoPorcentajeItem'],
					'Txt_Nota' => $row['Txt_Nota'],
					'ID_Impuesto_Cruce_Documento' => $row['ID_Impuesto_Cruce_Documento'],
					'Ss_Impuesto' => $fImpuestoItem,
					'Ss_Total' => $row['Ss_Total_Producto'],
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => $row['fIcbperItem'],
					'Fe_Emision' => dateNow('fecha')
				);
			}
			$this->db->insert_batch('documento_detalle', $documento_detalle);
			
			$fTotalMedioPago = 0.00;
			$documento_medio_pago_one = array();
			foreach($arrPost['arrFormaPago'] as $row) {
				//monto que realmente entra a caja
				$fTotalMedioPago = $this->security->xss_clean($row['Ss_Total']);
				if($row['iTipoVista'] == '1' && $fVuelto > 0.00) {
					$fTotalMedioPago = $arrPost['arrCabecera']['Ss_Total'];
					
					//Si es forma de pago crédito y además deja acuenta insertar
					$ID_Medio_Pago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND (No_Medio_Pago='Efectivo' OR No_Medio_Pago='EFECTIVO' OR No_Medio_Pago='CONTADO') LIMIT 1")->row()->ID_Medio_Pago;

					$documento_medio_pago_one = array(
						'ID_Empresa' => $this->empresa->ID_Empresa,
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Medio_Pago'	=> $ID_Medio_Pago,//query para obtener medio de pago en efectivo
						'Nu_Transaccion' => '',
						'Nu_Tarjeta' => '',
						'Ss_Total' => $fVuelto,
						'ID_Tipo_Medio_Pago' => '',
						'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
						'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
					);
					// fin
				}
				if($row['iTipoVista'] == '1' && $fVuelto == 0.00)
					$fTotalMedioPago = $arrPost['arrCabecera']['Ss_Total'];
				if($row['iTipoVista'] == '0' && $fVuelto > 0.00)
					$fTotalMedioPago = $row['Ss_Total'] - $arrPost['arrCabecera']['Ss_Vuelto'];
				$documento_medio_pago[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
					'ID_Medio_Pago'	=> $this->security->xss_clean($row['ID_Medio_Pago']),
					'Nu_Transaccion' => $this->security->xss_clean($row['Nu_Transaccion']),
					'Nu_Tarjeta' => $this->security->xss_clean($row['Nu_Tarjeta']),
					'Ss_Total' => $fTotalMedioPago,
					'ID_Tipo_Medio_Pago' => $this->security->xss_clean($row['ID_Tarjeta_Credito']),
					'Fe_Emision_Hora_Pago' => dateNow('fecha_hora'),
					'ID_Matricula_Empleado' => $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
				);
			}
			$this->db->insert_batch('documento_medio_pago', $documento_medio_pago);

			if(!empty($documento_medio_pago_one) && is_array($documento_medio_pago_one)){
				$this->db->insert('documento_medio_pago', $documento_medio_pago_one);
			}

			$this->MovimientoInventarioModel->crudMovimientoInventario($this->session->userdata['almacen']->ID_Almacen,$Last_ID_Documento_Cabecera,0,$documento_detalle,1,0,'',1,1);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
				$arrResponseWhatsapp = array(
					'No_Empresa_Comercial' => $this->empresa->No_Empresa_Comercial,
					'No_Empresa' => $this->empresa->No_Empresa,
					'Documento' => $sTidoDocumento,
					'Fecha_Emision' => ToDateBD(dateNow('fecha')),
					'Nu_Tipo_Recepcion' => $arrPost['arrCabecera']['Nu_Tipo_Recepcion'],
					'No_Tipo_Recepcion' => $arrPost['arrCabecera']['No_Tipo_Recepcion'],
					'sDireccionDelivery' => $arrPost['arrCabecera']['sDireccionDelivery'],
					'Total' => $arrPost['arrCabecera']['Ss_Total'],
				);
				$arrDetalle = array('arrDetalle' => $arrPost['arrDetalle']);
				$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrDetalle);

				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrVentaCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();
					$arrParams = array(
						'iCodigoProveedorDocumentoElectronico' => 1,
						'iEstadoVenta' => 6,//6=Completado
						'iIdDocumentoCabecera' =>  $Last_ID_Documento_Cabecera,
						'sEmailCliente' => ( !isset($arrPost['arrCliente']['Txt_Email_Entidad']) ? '' : $arrPost['arrCliente']['Txt_Email_Entidad'] ),
						'sTipoRespuesta' => 'php',
					);
					$arrResponseFE = array();
					if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 && $this->empresa->Nu_Estado_Pago_Sistema == 1 ) {//Nubefact
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronico( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 && $this->empresa->Nu_Estado_Pago_Sistema == 1 ) {//Facturador sunat
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoSunat( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					}

					$arrResponseFE = array_merge($arrResponseFE, $arrResponseWhatsapp);
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseFE,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['arrCabecera']['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrVentaCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();
					
					//Enviar correo
					if (!empty($arrPost['arrCliente']['Txt_Email_Entidad'])) {
						$sEmailCliente = $arrPost['arrCliente']['Txt_Email_Entidad'];
						$this->sendCorreoNotaVenta($Last_ID_Documento_Cabecera, $sEmailCliente);
					}
					
					//url de pdf nota de venta interna para whatsapp
					$arrResponseCorreo = array('enlace_del_pdf' => $sUrlPDFNotaVentaInternoLae);
					$arrResponseWhatsapp = array_merge($arrResponseWhatsapp, $arrResponseCorreo);

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['arrCabecera']['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrVentaCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['arrCabecera']['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();

					return array(
						'sStatus' => 'danger',
						'sMessage' => 'No se guardo venta por falta de pago',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseWhatsapp,
					);
				}
			}
		}// if - else validacion si existe comprobante
	}
	
	public function sendCorreoNotaVenta($id, $Txt_Email_Entidad){
		// Parametros de entrada
		$iIdDocumentoCabecera = $id;
		$arrData = $this->VentaModel->get_by_id($iIdDocumentoCabecera);
		if ( $arrData['sStatus'] == 'success' ) {
			$arrData = $arrData['arrData'];

			$this->load->library('email');

			$data = array();

			$sNombreTipoDocumentoVenta = strtoupper($arrData[0]->No_Tipo_Documento);

			$data["No_Documento"]	= $sNombreTipoDocumentoVenta . ' ' . $arrData[0]->ID_Serie_Documento . '-' . $arrData[0]->ID_Numero_Documento;
			$data["Fe_Emision"] 	= ToDateBD($arrData[0]->Fe_Emision);
			$data["No_Signo"]		= $arrData[0]->No_Signo;
			$data["Ss_Total"]		= $arrData[0]->Ss_Total;
			$data["Txt_Medio_Pago"]	= $arrData[0]->No_Medio_Pago;
			$data["Nu_Tipo"]		= $arrData[0]->Nu_Tipo;
			$data["Ss_Total_Saldo"]	= $arrData[0]->Ss_Total_Saldo;
			
			$data["No_Entidad"] = $arrData[0]->No_Entidad;
			
			$data["No_Empresa"] 					= $this->empresa->No_Empresa;
			$data["Nu_Documento_Identidad_Empresa"] = $this->empresa->Nu_Documento_Identidad;
			
			$data["url_comprobante"] = (!empty($arrData[0]->Txt_Url_PDF) ? $arrData[0]->Txt_Url_PDF : '');

			$asunto = $data["No_Documento"] . ' ' . $this->empresa->No_Empresa . ' | ' . $this->empresa->Nu_Documento_Identidad;
			
			$message = $this->load->view('correos/nota_venta', $data, true);

			$this->email->from('noreply@laesystems.com', $this->empresa->No_Empresa);//de
			
			$this->email->to($Txt_Email_Entidad);//para
				
			$this->email->subject($asunto);
			$this->email->message($message);
			if (!empty($arrData[0]->Txt_Url_PDF))
				$this->email->attach($arrData[0]->Txt_Url_PDF);
			$this->email->set_newline("\r\n");

			$isSend = $this->email->send();
			
			if($isSend) {
				$peticion = array(
					'status' => 'success',
					'style_modal' => 'modal-success',
					'message' => 'Correo enviado',
				);
			} else {
				$peticion = array(
					'status' => 'error',
					'style_modal' => 'modal-danger',
					'message' => 'No se pudo enviar el correo, inténtelo más tarde.',
					'sMessageErrorEmail' => $this->email->print_debugger(),
				);
			}// if - else envio email
		}
	}
}
