<?php
class PedidosModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
		$where_id_empresa = " AND PC.ID_Empresa = " . $this->empresa->ID_Empresa;
		if(( $this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503 ) && $arrParams['ID_Filtro_Empresa']==0){
			$where_id_empresa = "";
		} else if(( $this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503 ) && $arrParams['ID_Filtro_Empresa']!=0){
			$where_id_empresa = " AND PC.ID_Empresa = " . $ID_Filtro_Empresa=$arrParams['ID_Filtro_Empresa'];
		}

        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Pedido_Cabecera=$arrParams['ID_Pedido_Cabecera'];
        $Nu_Estado_Pedido=$arrParams['Nu_Estado_Pedido'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $Nu_Estado_Pedido_Empresa=$arrParams['Nu_Estado_Pedido_Empresa'];
        $sNombreCiudad=$arrParams['sNombreCiudad'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? ' AND PC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : '';
        $cond_numero = $ID_Pedido_Cabecera != "-" ? " AND PC.ID_Pedido_Cabecera = '" . $ID_Pedido_Cabecera . "'" : "";
        $cond_estado_pedido = $Nu_Estado_Pedido != "0" ? 'AND PC.Nu_Estado = ' . $Nu_Estado_Pedido : '';
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? ' AND PC.ID_Entidad = ' . $iIdCliente : "";
        $cond_estado_pedido_empresa = $Nu_Estado_Pedido_Empresa != "999" ? 'AND PC.Nu_Estado_Pedido_Empresa = ' . $Nu_Estado_Pedido_Empresa : '';
        $where_nombre_ciudad = ( $sNombreCiudad != '-' ) ? " AND PC.No_Ciudad_Dropshipping = '" . $sNombreCiudad . "'" : "";

        $query = "SELECT
PC.Fe_Emision_Hora,
PC.ID_Pedido_Cabecera,
PC.No_Entidad_Order_Address_Entry AS No_Entidad,
PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular,
PC.Ss_Total,
PC.Nu_Estado,
PC.ID_Entidad,
Nu_Forma_Pago_Dropshipping,
Nu_Servicio_Transportadora_Dropshipping,
Nu_Tipo_Venta_Generada,
PC.Fe_Entrega,
PC.Nu_Estado_Pedido_Empresa,
PC.Ss_Precio_Delivery,
PC.No_Ciudad_Dropshipping,
EMP.ID_Pais,
PC.Txt_Response_TrackingId_Api,
PC.Nu_Tipo_Guia_Api,
PC.Txt_Response_Guia_Api,
PC.Ss_Total_Proveeedor,
MONE.No_Signo,
PC.Txt_Glosa
FROM
pedido_cabecera AS PC
JOIN empresa AS EMP ON(EMP.ID_Empresa = PC.ID_Empresa)
JOIN moneda AS MONE ON(MONE.ID_Moneda = PC.ID_Moneda)
WHERE
PC.Fe_Emision BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59'
" . $where_id_empresa . "
" . $cond_tipo . "
" . $cond_numero . "
" . $cond_estado_pedido . "
" . $cond_cliente . "
" . $cond_estado_pedido_empresa . "
" . $where_nombre_ciudad . "
ORDER BY
PC.ID_Pedido_Cabecera DESC;";

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
            'sMessage' => 'No hay registros',
        );
    }

    public function verPedido($iIdPedido){
        $query = "SELECT
PC.ID_Almacen,
ALMA.No_Almacen,
PC.Fe_Emision_Hora,
TD.No_Tipo_Documento_Breve,
PC.ID_Pedido_Cabecera,
PC.Nu_Documento_Identidad,
PC.No_Entidad_Order_Address_Entry AS No_Entidad,
PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Entidad,
CLI.Txt_Email_Entidad,
PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Referencia,
TDRECEP.No_Metodo_Entrega_Tienda_Virtual AS No_Estado_Recepcion,
PC.Nu_Tipo_Recepcion,
DISTRI.No_Distrito,
P.No_Provincia,
D.No_Departamento,
PC.Txt_Direccion_Entidad_Order_Address_Entry AS Txt_Direccion,
PC.Txt_Direccion_Referencia_Entidad_Order_Address_Entry AS Txt_Direccion_Referencia,
ITEM.No_Producto,
PD.Qt_Producto,
PD.Ss_Precio,
PD.Ss_SubTotal,
PC.Ss_Total,
PC.Ss_Precio_Delivery,
MPM.No_Medio_Pago_Tienda_Virtual,
PC.ID_Tipo_Documento,
PC.ID_Moneda,
PC.ID_Medio_Pago,
PC.Ss_Descuento,
PC.Txt_Glosa,
PC.ID_Entidad,
PC.ID_Distrito_Delivery,
CE.No_Codigo_Cupon_Descuento
FROM
pedido_cabecera AS PC
JOIN almacen AS ALMA ON(PC.ID_Almacen = ALMA.ID_Almacen)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = PC.ID_Tipo_Documento)
LEFT JOIN pedido_detalle AS PD ON(PD.ID_Pedido_Cabecera = PC.ID_Pedido_Cabecera)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
LEFT JOIN distrito_tienda_virtual AS DISTRI ON(DISTRI.ID_Distrito = PC.ID_Distrito_Delivery)
LEFT JOIN provincia AS P ON(P.ID_Provincia = DISTRI.ID_Provincia)
LEFT JOIN departamento AS D ON(D.ID_Departamento = P.ID_Departamento)
LEFT JOIN metodo_entrega_tienda_virtual AS TDRECEP ON(TDRECEP.ID_Metodo_Entrega_Tienda_Virtual = PC.ID_Tabla_Dato_Tipo_Recepcion)
LEFT JOIN entidad AS CLI ON(CLI.ID_Entidad = PC.ID_Entidad)
JOIN medio_pago AS MPM ON(MPM.ID_Medio_Pago = PC.ID_Medio_Pago)
LEFT JOIN cupon_descuento AS CE ON(CE.ID_Cupon_Descuento = PC.ID_Cupon_Descuento)
WHERE
PC.ID_Pedido_Cabecera = " . $iIdPedido;
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
            'sMessage' => 'No hay registros',
        );
    }

	public function cambiarEstado($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $arrData = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update('pedido_cabecera', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Estado cambiado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function eliminarPedido($ID){
		$this->db->trans_begin();
		$query = "SELECT Nu_Estado_Pedido_Empresa, Nu_Estado FROM pedido_cabecera WHERE ID_Pedido_Cabecera=" . $ID . " LIMIT 1";
		$objPedidoCabecera = $this->db->query($query)->row();
		if (is_object($objPedidoCabecera)) {
			if ($objPedidoCabecera->Nu_Estado_Pedido_Empresa==0 && $objPedidoCabecera->Nu_Estado==1) {
				$this->db->where('ID_Pedido_Cabecera', $ID);
				$this->db->delete('relacion_pedido_documento_cabecera');

				$this->db->where('ID_Pedido_Cabecera', $ID);
				$this->db->delete('pedido_detalle');

				$this->db->where('ID_Pedido_Cabecera', $ID);
				$this->db->delete('pedido_cabecera');

				/*
				$where_update = array('ID_Pedido_Cabecera' => $ID);//7=Eliminar
				$data_update = array( 'Nu_Estado' => 7, 'Txt_Glosa' => '{usuario: ' . $this->user->No_Usuario . ', Fe_Eliminado: ' . dateNow('fecha_hora') . '}' );//7=eliminado
				$this->db->update('pedido_cabecera', $data_update, $where_update);
				*/

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
				} else {
					$this->db->trans_commit();
					return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Pedido eliminado');
				}
			} else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Pedido COMPLETADO no se puede eliminar');
			}
		} else {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No existe registro');
		}
	}
    
	public function generarVenta($arrPost){
        //array_debug($arrPost);
        
		$query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $this->empresa->ID_Empresa . "
AND ID_Organizacion=" . $this->empresa->ID_Organizacion . "
AND ID_Almacen=" . $arrPost['ID_Almacen'] . "
AND ID_Tipo_Documento=" . $arrPost['ID_Tipo_Documento'] . "
AND Nu_Estado=1
AND ID_POS IS NULL LIMIT 1";

		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrPost['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrPost['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) )
			return array('sStatus' => 'danger', 'sMessage' => 'Falta configurar en opcion Ventas > Series para ' . $sTidoDocumento . ', elegir sin caja.');

		if ($this->db->query("SELECT COUNT(*) AS existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrPost['ID_Tipo_Documento'] . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe venta ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . ' modificar correlativo en la opción Ventas -> Series' );
		} else {
		    $this->db->trans_begin();

			$Nu_Correlativo = 0;
			$Fe_Year = dateNow('año');
			$Fe_Month = dateNow('mes');
			
			$iTipoDocumentoIdentidad = trim($arrPost['ID_Tipo_Documento_Identidad']);
			$sNombreEntidad = trim($arrPost['No_Entidad']);
			$sNombreEntidad = strip_tags($sNombreEntidad);
			$sNumeroDocumentoIdentidad = trim($arrPost['Nu_Documento_Identidad']);
			$sNumeroDocumentoIdentidad = strip_tags($sNumeroDocumentoIdentidad);
			
			$iTipoDocumentoIdentidad = '1';//1=OTROS
			if ( strlen($sNumeroDocumentoIdentidad) == 8 )
				$iTipoDocumentoIdentidad = '2';//2=DNI
			else if ( strlen($sNumeroDocumentoIdentidad) == 12 )
				$iTipoDocumentoIdentidad = '3';//3=CARNET EXTRANJERIA
			
			$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->empresa->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $iTipoDocumentoIdentidad . " AND Nu_Documento_Identidad = '" . $sNumeroDocumentoIdentidad . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($sNombreEntidad) . "' LIMIT 1";
			$arrResponseSQL = $this->db->query($query);
			if ( $arrResponseSQL->num_rows() > 0 ){
				$arrData = $arrResponseSQL->result();
				$Last_ID_Entidad = $arrData[0]->ID_Entidad;
			} else {
				$arrCliente = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'Nu_Tipo_Entidad' => 0,//0=Cliente
					'ID_Tipo_Documento_Identidad' => $iTipoDocumentoIdentidad,
					'Nu_Documento_Identidad' => $sNumeroDocumentoIdentidad,
					'No_Entidad' => (!empty($sNombreEntidad) ? $sNombreEntidad : $sNumeroDocumentoIdentidad),
					'Nu_Estado' => 1,
					'Nu_Celular_Entidad' => '',
					'Txt_Email_Entidad'	=> ''
				);

				/*
				$arrCliente = array_merge($arrCliente, array(
						"ID_Departamento" => $arrPostHeader['iIdDepartamentoDeliveryInvitado'],
						"ID_Provincia" => $arrPostHeader['iIdProvinciaDeliveryInvitado'],
						"ID_Distrito" => $arrPostHeader['iIdDistritoDelivery'],
						"Txt_Direccion_Entidad" => $arrPostHeader['sDireccionEntidad']
					)
				);
				*/

				if ($this->db->insert('entidad', $arrCliente) > 0) {
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se puede registrar cliente',
					);
				}
			}

			//Buscar que existe cliente en la tabla entidad si por algun motivo no funciona deberemos crear un modal para editar algunos datos del ticket
			$objCliente = $this->db->query("SELECT Txt_Direccion_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad=" . $Last_ID_Entidad . " LIMIT 1")->row();
			if ( !is_object($objCliente) ){
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'No se encontro cliente');
			}

			//Obtener datos de pedido cabecera
			$objPedidoCabecera = $this->db->query("SELECT Ss_Precio_Delivery, Ss_Descuento FROM pedido_cabecera WHERE ID_Pedido_Cabecera=" . $arrPost['ID_Pedido_Cabecera'] . " LIMIT 1")->row();
			
			$arrVentaCabecera = array(
				'ID_Empresa'				=> $this->empresa->ID_Empresa,
				'ID_Organizacion'			=> $this->empresa->ID_Organizacion,
				'ID_Almacen'			    => $arrPost['ID_Almacen'],
				'ID_Entidad'				=> $Last_ID_Entidad,
				'ID_Tipo_Asiento'			=> 1,//Venta
				'ID_Tipo_Documento'			=> $arrPost['ID_Tipo_Documento'],
				'ID_Serie_Documento_PK'		=> $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento'		=> $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento'		=> $arrSerieDocumento->Nu_Numero_Documento,
				'Fe_Emision'				=> dateNow('fecha'),
				'Fe_Emision_Hora'			=> dateNow('fecha_hora'),
				'ID_Moneda'					=> $arrPost['ID_Moneda'],
				'ID_Medio_Pago'				=> $arrPost['ID_Medio_Pago'],
				'Fe_Vencimiento'			=> dateNow('fecha'),
				'Fe_Periodo' => dateNow('fecha'),
				'Nu_Descargar_Inventario' => 1,
				'Ss_Total' => $arrPost['fTotalDocumento'],
				'Ss_Total_Saldo' => 0.00,
				'Ss_Vuelto' => 0,
				'Nu_Correlativo' => $Nu_Correlativo,
				'Nu_Estado' => 6,//Completado
				'Nu_Transporte_Lavanderia_Hoy' => 0,
				'Nu_Estado_Lavado' => 0,
				'Fe_Entrega' => dateNow('fecha'),
				'Nu_Tipo_Recepcion' => $arrPost['Nu_Tipo_Recepcion'],//6=Delivery y 7 = Recojo en tienda
				'Nu_Estado_Despacho_Pos' => 3,//3=entregado y 0=pendiente de envio
				'ID_Transporte_Delivery' => 0,
				'Txt_Direccion_Delivery' => ($arrPost['Nu_Tipo_Recepcion'] == 6 ? $arrPost['Txt_Direccion_Entidad_Order_Address_Entry'] . ' ' . $arrPost['Txt_Direccion_Referencia_Entidad_Order_Address_Entry'] : ''),
				'No_Formato_PDF' => 'TICKET',
				'Po_Descuento' => 0.00,
				'Ss_Descuento' => $objPedidoCabecera->Ss_Descuento,
				'ID_Canal_Venta_Tabla_Dato' => 2080,//Tienda virtual
				'ID_Sunat_Tipo_Transaction' => 1,//Venta interna
			);
			
			if ( !empty($arrPost['ID_Distrito_Delivery']) )
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Distrito_Delivery" => $this->security->xss_clean($arrPost['ID_Distrito_Delivery'])));

			$this->db->insert('documento_cabecera', $arrVentaCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			// URL para enviar correo y para consultar por fuera sin session
			// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
			if($arrPost['ID_Tipo_Documento']==2) {//2=Nota de venta
				$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/' . $Last_ID_Documento_Cabecera;
				$sql = "UPDATE documento_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}

            $query_detalle = " SELECT
ID_Producto,
Qt_Producto,
Txt_Nota,
Ss_Precio,
Ss_SubTotal,
ID_Impuesto_Cruce_Documento,
Ss_Impuesto,
Ss_Total
FROM
pedido_detalle
WHERE
ID_Pedido_Cabecera = " . $arrPost['ID_Pedido_Cabecera'];
            $arrDetalle = $this->db->query($query_detalle)->result();

			$fSubTotalItem=0.00;
			$fImpuestoItem=0.00;
		    foreach ($arrDetalle as $row) {
				$fSubTotalItem = $row->Ss_Total;
				$fImpuestoItem = 0.00;

				if ($arrPost['ID_Tipo_Documento'] != '2') {
					$arrImpuestoDetalle = $this->db->query("SELECT Ss_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento = " . $row->ID_Impuesto_Cruce_Documento . " LIMIT 1")->row();
					$fSubTotalItemCalculo = round($row->Ss_Total / $arrImpuestoDetalle->Ss_Impuesto, 2);

					$fSubTotalItem = $fSubTotalItemCalculo;
					$fImpuestoItem = ($row->Ss_Total - $fSubTotalItemCalculo);
				}

				$documento_detalle[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $row->ID_Producto,
					'Qt_Producto' => $row->Qt_Producto,
					'Ss_Precio' => $row->Ss_Precio,
					'Ss_SubTotal' => $fSubTotalItem,
					'Ss_Descuento' => 0.00,
					'Ss_Descuento_Impuesto' => 0.00,
					'Po_Descuento' => 0.00,
					'Txt_Nota' => $row->Txt_Nota,
					'ID_Impuesto_Cruce_Documento' => $row->ID_Impuesto_Cruce_Documento,
					'Ss_Impuesto' => $fImpuestoItem,
					'Ss_Total' => $row->Ss_Total,
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => 0.00,
				);
			}
			$this->db->insert_batch('documento_detalle', $documento_detalle);

            $documento_medio_pago = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
                'ID_Medio_Pago'	=> $arrPost['ID_Medio_Pago'],
                'Nu_Transaccion' => '',
                'Nu_Tarjeta' => '',
                'Ss_Total' => $arrPost['fTotalDocumento'],
                'ID_Tipo_Medio_Pago' => '',
            );
			$this->db->insert('documento_medio_pago', $documento_medio_pago);
			
			//realacion para saber que documento se facturo
            $relacion_pedido_documento_cabecera = array(
                'ID_Empresa' => $this->empresa->ID_Empresa,
                'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
                'ID_Pedido_Cabecera' => $arrPost['ID_Pedido_Cabecera'],
            );
			$this->db->insert('relacion_pedido_documento_cabecera', $relacion_pedido_documento_cabecera);
		
			$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Serie_Documento_PK=" . $arrSerieDocumento->ID_Serie_Documento_PK);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
				$this->db->trans_commit();

				return array(
					'sStatus' => 'success',
					'sMessage' => 'Venta completada',
					'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
					'arrResponseFE' => '',
				);
            }
        }
	}

	public function getRelacionPedidoVenta($arrParams){
		$query = "SELECT VC.ID_Entidad, VC.Nu_Estado, VC.ID_Documento_Cabecera, TD.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, VC.Txt_Url_PDF FROM
relacion_pedido_documento_cabecera AS VE
JOIN documento_cabecera AS VC ON(VE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
WHERE VE.ID_Pedido_Cabecera = " . $arrParams['ID_Pedido_Cabecera'];		
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

    public function actualizarPedido($where, $data, $arrPedidoDetalle, $EID_Empresa_Pedido){
		$this->db->trans_begin();

		//MODIFICANDO DATOS DE CABECERA
		$this->db->update('pedido_cabecera', $data, $where);

		$this->db->where('ID_Pedido_Cabecera', $where['ID_Pedido_Cabecera']);
		$this->db->delete('pedido_detalle');

		//insert detalle
		$fTotal = 0.00;
		$fTotalCabecera = 0.00;
		$fTotalCabeceraProveedor = 0.00;
		foreach($arrPedidoDetalle as $row) {
			$fTotal = ($row['cantidad'] * $row['precio']);
			$fTotalCabecera += $fTotal;
			$fTotalCabeceraProveedor += ($row['cantidad'] * $row['precio_empresa_proveedor']);
			$pedido_detalle[] = array(
				'ID_Empresa' => $EID_Empresa_Pedido,
				'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
				'ID_Producto' => $row['id_producto'],
				'Qt_Producto' => $row['cantidad'],
				'Ss_Precio' => $row['precio'],
				'Ss_SubTotal' => $fTotal,
				'Ss_Descuento' => 0,
				'Ss_Descuento_Impuesto' => 0,
				'Po_Descuento' => 0,
				'Txt_Nota' => '',
				'ID_Impuesto_Cruce_Documento' => $row['id_impuesto'],
				'Ss_Impuesto' => 0,
				'Ss_Total' => $fTotal,
				'Ss_Icbper' => 0,
				'ID_Empresa_Marketplace_Seller' => $row['id_empresa_proveedor'],//empresa de proveedor
				'ID_Almacen_Marketplace_Seller' => $row['id_almacen_proveedor'],//almacen de proveedor
				'Ss_Precio_Empresa_Proveedor' => $row['precio_empresa_proveedor']//precio de proveedor
			);
		}
		$this->db->insert_batch('pedido_detalle', $pedido_detalle);

		$this->db->query("UPDATE pedido_cabecera SET Ss_Total=" . $fTotalCabecera . ", Ss_Total_Proveeedor=" . $fTotalCabeceraProveedor . " WHERE ID_Pedido_Cabecera=" . $where['ID_Pedido_Cabecera']);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'style_modal' => 'modal-danger',
				'message' => '¡Oops! Algo salió mal. Inténtalo mas tarde detalle'
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'message' => 'Registro modificado'
			);
		}
    }

	public function agregarPedido($arrPost, $arrPedidoDetalle){
		$this->db->trans_begin();

		$iIdMoneda = $this->db->query("SELECT ID_Moneda FROM moneda WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Moneda;
		$iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Medio_Pago;
		$ID_Metodo_Entrega_Tienda_Virtual = $this->db->query("SELECT ID_Metodo_Entrega_Tienda_Virtual FROM metodo_entrega_tienda_virtual WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Metodo_Entrega_Tienda_Virtual;
		
		$arrSaleOrder = array(
			'ID_Empresa' => $this->empresa->ID_Empresa,
			'ID_Organizacion' => $this->empresa->ID_Organizacion,
			'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
			'ID_Tipo_Documento' => 2,
			'ID_Entidad' => 0,
			'Nu_Documento_Identidad' => '',
			'Fe_Emision' => dateNow('fecha'),
			'Fe_Emision_Hora' => dateNow('fecha_hora'),
			'ID_Moneda'	=> $iIdMoneda,
			'ID_Medio_Pago' => $iIdMedioPago,
			'ID_Tabla_Dato_Tipo_Recepcion' => $ID_Metodo_Entrega_Tienda_Virtual,
			'Nu_Tipo_Recepcion' => 6,//6=delivery
			'Nu_Descargar_Inventario' => 0,
			'Ss_Total' => 0,
			'Nu_Estado' => 1,//1=pendiente
			'Nu_Tipo_Pedido' => 1,// 1 = Marketplace - App, 2 = Marketplace - Web
			'ID_Distrito_Delivery' => 0,
			'Ss_Precio_Delivery' => 0,
			'No_Entidad_Order_Address_Entry' => $arrPost['No_Entidad_Order_Address_Entry'],
			'Nu_Celular_Entidad_Order_Address_Entry' => $arrPost['Nu_Celular_Entidad_Order_Address_Entry'],
			'Txt_Email_Dropshipping' => $arrPost['Txt_Email_Dropshipping'],
			'No_Ciudad_Dropshipping' => $arrPost['No_Ciudad_Dropshipping'],
			'Txt_Direccion_Entidad_Order_Address_Entry' => $arrPost['Txt_Direccion_Entidad_Order_Address_Entry'],
			'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' => $arrPost['Txt_Direccion_Referencia_Entidad_Order_Address_Entry'],
			'Ss_Efectivo_Contra_Entrega' => 0,
			'ID_Almacen_Retiro_Tienda' => 0,
			'Ss_Descuento' => 0,
			'ID_Cupon_Descuento' => 0,
			'Fe_Entrega' => ToDate($arrPost['Fe_Entrega']),
			'Nu_Forma_Pago_Dropshipping' => $arrPost['forma_pago'],
			'Nu_Servicio_Transportadora_Dropshipping' => $arrPost['servicio_transportadora'],
			'Txt_Glosa' => $arrPost['Txt_Glosa'],
			'Nu_Tipo_Venta_Generada' => 2,//=pedido manual
			'Nu_Estado_Pedido_Empresa' => 0//PEDIDO COMPLETADO Y SOLO PUEDE CORREGIR ECXPRESS
		);
		
		$this->db->insert('pedido_cabecera', $arrSaleOrder);
		$Last_ID_Pedido_Cabecera = $this->db->insert_id();

		$fTotal = 0.00;
		$fTotalCabecera = 0.00;
		$fTotalCabeceraProveedor = 0.00;
		foreach($arrPedidoDetalle as $row) {
			$fTotal = ($row['cantidad'] * $row['precio']);
			
			if($fTotal != $row['total']) {
				$this->db->trans_rollback();
				return array(
					'status' => 'error',
					'message' => '¡Oops! total no cuadra'
				);
			}
			
			$fTotal = $row['total'];
			$fTotalCabecera += $fTotal;
			$fTotalCabeceraProveedor += ($row['cantidad'] * $row['precio_empresa_proveedor']);

			$pedido_detalle[] = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Pedido_Cabecera' => $Last_ID_Pedido_Cabecera,
				'ID_Producto' => $row['id_producto'],
				'Qt_Producto' => $row['cantidad'],
				'Ss_Precio' => $row['precio'],
				'Ss_SubTotal' => $fTotal,
				'Ss_Descuento' => 0,
				'Ss_Descuento_Impuesto' => 0,
				'Po_Descuento' => 0,
				'Txt_Nota' => '',
				'ID_Impuesto_Cruce_Documento' => $row['id_impuesto'],
				'Ss_Impuesto' => 0,
				'Ss_Total' => $fTotal,
				'Ss_Icbper' => 0,
				'ID_Empresa_Marketplace_Seller' => $row['id_empresa_proveedor'],//empresa de proveedor
				'ID_Almacen_Marketplace_Seller' => $row['id_almacen_proveedor'],//almacen de proveedor
				'Ss_Precio_Empresa_Proveedor' => $row['precio_empresa_proveedor']//precio de proveedor
			);
		}
		$this->db->insert_batch('pedido_detalle', $pedido_detalle);
		
		$this->db->query("UPDATE pedido_cabecera SET Ss_Total=" . $fTotalCabecera . ", Ss_Total_Proveeedor=" . $fTotalCabeceraProveedor . " WHERE ID_Pedido_Cabecera=" . $Last_ID_Pedido_Cabecera);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'message' => '¡Oops! Algo salió mal. Inténtalo mas tarde detalle'
			);
		} else {
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Pedido guardado satisfactoriamente'
			);
		}
	}
    
    public function get_by_id($ID){
        $query = "SELECT
PC.*,
PD.*,
PC.ID_Pedido_Cabecera,
PC.ID_Empresa,
ITEM.No_Producto,
EMP.No_Empresa,
ALMA.No_Almacen,
EMP.ID_Empresa AS ID_Empresa_Proveedor,
ALMA.ID_Almacen AS ID_Almacen_Proveedor
FROM
pedido_cabecera AS PC
LEFT JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera = PD.ID_Pedido_Cabecera)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
LEFT JOIN empresa AS EMP ON(EMP.ID_Empresa = PD.ID_Empresa_Marketplace_Seller)
LEFT JOIN almacen AS ALMA ON(ALMA.ID_Almacen = PD.ID_Almacen_Marketplace_Seller)
WHERE
PC.ID_Pedido_Cabecera = " . $ID;
        return $this->db->query($query)->result();
    }
	
	public function guardarPedidosManualExcel($arrPost){
		$this->db->trans_begin();

		$iIdMoneda = $this->db->query("SELECT ID_Moneda FROM moneda WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Moneda;
		$iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Medio_Pago;
		$ID_Metodo_Entrega_Tienda_Virtual = $this->db->query("SELECT ID_Metodo_Entrega_Tienda_Virtual FROM metodo_entrega_tienda_virtual WHERE ID_Empresa = " . $this->user->ID_Empresa . " LIMIT 1")->row()->ID_Metodo_Entrega_Tienda_Virtual;

		$iIdPedido=0;
		$fTotalCabecera = 0.00;
		$fTotalCabeceraProveedor = 0.00;
		$Last_ID_Pedido_Cabecera = 0;
		foreach($arrPost['arrPedidoManualExcel'] as $row){
			//armo cabecera
			if($iIdPedido != $row['iIdPedido']){
				if($Last_ID_Pedido_Cabecera>0){
					//echo "total pedido > " . $Last_ID_Pedido_Cabecera . ' es ' . $fTotalCabecera;
					$this->db->query("UPDATE pedido_cabecera SET Ss_Total=" . $fTotalCabecera . ", Ss_Total_Proveeedor=" . $fTotalCabeceraProveedor . " WHERE ID_Pedido_Cabecera=" . $Last_ID_Pedido_Cabecera);
				}

				$fTotalCabecera=0;
				$fTotalCabeceraProveedor=0;
				$iIdPedido = $row['iIdPedido'];
				
				$pedido_cabecera = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'ID_Almacen' => $this->session->userdata['almacen']->ID_Almacen,
					'ID_Tipo_Documento' => 2,
					'ID_Entidad' => 0,
					'Nu_Documento_Identidad' => '',
					'Fe_Emision' => dateNow('fecha'),
					'Fe_Emision_Hora' => dateNow('fecha_hora'),
					'ID_Moneda'	=> $iIdMoneda,
					'ID_Medio_Pago' => $iIdMedioPago,
					'ID_Tabla_Dato_Tipo_Recepcion' => $ID_Metodo_Entrega_Tienda_Virtual,
					'Nu_Tipo_Recepcion' => 6,//6=delivery
					'Nu_Descargar_Inventario' => 0,
					'Ss_Total' => 0,
					'Nu_Estado' => 1,//1=pendiente
					'Nu_Tipo_Pedido' => 1,// 1 = Marketplace - App, 2 = Marketplace - Web
					'ID_Distrito_Delivery' => 0,
					'Ss_Precio_Delivery' => 0,
					'No_Entidad_Order_Address_Entry' => $row['sNombreCompleto'],
					'Nu_Celular_Entidad_Order_Address_Entry' => $row['iTelefono'],
					'No_Ciudad_Dropshipping' => $row['sCiudad'],
					'Txt_Direccion_Entidad_Order_Address_Entry' => $row['sDireccion'],
					'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' => $row['sDireccionReferencia'],
					'Ss_Efectivo_Contra_Entrega' => 0,
					'ID_Almacen_Retiro_Tienda' => 0,
					'Ss_Descuento' => 0,
					'ID_Cupon_Descuento' => 0,
					'Fe_Entrega' => $row['dFechaEntrega'],
					'Nu_Forma_Pago_Dropshipping' => $row['iFormaPago'],
					'Nu_Servicio_Transportadora_Dropshipping' => $row['iTransportadora'],
					'Txt_Glosa' => $row['sObservaciones'],
					'Nu_Tipo_Venta_Generada' => 2,//=pedido manual
					'Nu_Estado_Pedido_Empresa' => 0,//PEDIDO COMPLETADO Y SOLO PUEDE CORREGIR ECXPRESS
					'Txt_Email_Dropshipping' => $row['sEmail'],
					'Nu_Tipo_Guia_Api' => $row['iTipoTransportadora']
				);
				
				$this->db->insert('pedido_cabecera', $pedido_cabecera);
				$Last_ID_Pedido_Cabecera = $this->db->insert_id();
				//var_dump($pedido_cabecera);
			}

			$fTotalCabecera += $row['fTotal'];
			$fTotalCabeceraProveedor += ($row['fCantidad'] * $row['Ss_Precio_Proveedor_Dropshipping']);
			//armo detalle
			$pedido_detalle = array(
				'ID_Empresa' => $this->empresa->ID_Empresa,
				'ID_Pedido_Cabecera' => $Last_ID_Pedido_Cabecera,
				'ID_Producto' => $row['ID_Producto'],//falta sacar de BD por EXCEL
				'Qt_Producto' => $row['fCantidad'],
				'Ss_Precio' => $row['fPrecio'],
				'Ss_SubTotal' => $row['fTotal'],
				'Ss_Descuento' => 0,
				'Ss_Descuento_Impuesto' => 0,
				'Po_Descuento' => 0,
				'Txt_Nota' => '',
				'ID_Impuesto_Cruce_Documento' => $row['ID_Impuesto_Cruce_Documento'],
				'Ss_Impuesto' => 0,
				'Ss_Total' => $row['fTotal'],
				'Ss_Icbper' => 0,
				'ID_Empresa_Marketplace_Seller' => $row['ID_Empresa_Proveedor'],//empresa de proveedor
				'ID_Almacen_Marketplace_Seller' => $row['ID_Almacen_Proveeedor'],//almacen de proveedor
				'Ss_Precio_Empresa_Proveedor' => $row['Ss_Precio_Proveedor_Dropshipping']//precio de proveedor
			);
			$this->db->insert('pedido_detalle', $pedido_detalle);
			//var_dump($pedido_detalle);
		}

		if($Last_ID_Pedido_Cabecera>0){
			$this->db->query("UPDATE pedido_cabecera SET Ss_Total=" . $fTotalCabecera . ", Ss_Total_Proveeedor=" . $fTotalCabeceraProveedor . " WHERE ID_Pedido_Cabecera=" . $Last_ID_Pedido_Cabecera);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(
				'status' => 'error',
				'message' => '¡Oops! Algo salió mal. Inténtalo mas tarde detalle'
			);
		} else {
			//$this->db->trans_rollback();
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Pedido(s) guardado satisfactoriamente'
			);
		}
	}

	public function getStockItemxAlmacen($iIdStockProducto){
	    $query = "SELECT
STOCK.ID_Producto,
STOCK.ID_Empresa AS ID_Empresa_Proveedor,
STOCK.ID_Almacen AS ID_Almacen_Proveeedor,
ROUND(PRO.Ss_Precio_Proveedor_Dropshipping,2) AS Ss_Precio_Proveedor_Dropshipping,
ICD.ID_Impuesto_Cruce_Documento
FROM
stock_producto AS STOCK
JOIN producto AS PRO ON(STOCK.ID_Producto = PRO.ID_Producto)
JOIN impuesto_cruce_documento AS ICD ON(ICD.ID_Impuesto = PRO.ID_Impuesto AND ICD.Nu_Estado=1)
WHERE
STOCK.ID_Stock_Producto=".$iIdStockProducto."
LIMIT 1";
		return $this->db->query($query)->row();
	}
}
