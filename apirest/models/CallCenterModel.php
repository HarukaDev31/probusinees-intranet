<?php
class CallCenterModel extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($arrParams){
		$where_id_empresa = " AND PC.ID_Empresa = " . $this->empresa->ID_Empresa;
		if(($this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503) && $arrParams['ID_Filtro_Empresa']==0){
			$where_id_empresa = "";
		} else if(($this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503) && $arrParams['ID_Filtro_Empresa']!=0){
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
        $ID_Pais=$arrParams['ID_Pais'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? ' AND PC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : '';
        $cond_numero = $ID_Pedido_Cabecera != "-" ? " AND PC.ID_Pedido_Cabecera = '" . $ID_Pedido_Cabecera . "'" : "";
        $cond_estado_pedido = $Nu_Estado_Pedido != "0" ? 'AND PC.Nu_Estado = ' . $Nu_Estado_Pedido : '';
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? ' AND PC.ID_Entidad = ' . $iIdCliente : "";
        $cond_estado_pedido_empresa = $Nu_Estado_Pedido_Empresa != "999" ? 'AND PC.Nu_Estado_Pedido_Empresa = ' . $Nu_Estado_Pedido_Empresa : '';
        $where_nombre_ciudad = ( $sNombreCiudad != '-' ) ? " AND PC.No_Ciudad_Dropshipping LIKE '%" . $sNombreCiudad . "%'" : "";

        $where_pais = $ID_Pais != "0" ? ' AND EMP.ID_Pais = ' . $ID_Pais : '';
		
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
EMP.No_Empresa,
PC.Ss_Precio_Delivery,
PC.Txt_Glosa,
PC.Ss_Precio_Delivery_Propio_Personal,
PC.ID_Usuario_Asignar_Delivery,
USRDELI.No_Nombres_Apellidos AS No_Delivery,
PC.No_Ciudad_Dropshipping,
PC.Ss_Saldo_A_Favor_Delivery_Interno,
PC.Txt_Direccion_Entidad_Order_Address_Entry,
EMP.ID_Pais,
PC.Txt_Response_TrackingId_Api,
PC.Nu_Tipo_Guia_Api,
PC.Txt_Response_Guia_Api,
MONE.No_Signo,
DTV.No_Dominio_Tienda_Virtual,
DTV.No_Subdominio_Tienda_Virtual,
DTV.Nu_Tipo_Tienda
FROM
pedido_cabecera AS PC
JOIN empresa AS EMP ON(EMP.ID_Empresa = PC.ID_Empresa)
JOIN moneda AS MONE ON(MONE.ID_Moneda = PC.ID_Moneda)
JOIN subdominio_tienda_virtual AS DTV ON(DTV.ID_Empresa = PC.ID_Empresa)
LEFT JOIN usuario AS USRDELI ON(USRDELI.ID_Usuario = PC.ID_Usuario_Asignar_Delivery)
WHERE
Nu_Servicio_Transportadora_Dropshipping = 1
AND PC.Fe_Emision BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59'
" . $where_id_empresa . "
" . $cond_tipo . "
" . $cond_numero . "
" . $cond_estado_pedido . "
" . $cond_cliente . "
" . $cond_estado_pedido_empresa . "
" . $where_nombre_ciudad . "
" . $where_pais . "
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
			//array_debug($arrResponseSQL->result());
            return array(
                'sStatus' => 'success',
                'arrData' => $arrResponseSQL->result()
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No hay registros',
        );
    }
	
    public function getReporteExcel($arrParams){
		$where_id_empresa = " AND PC.ID_Empresa = " . $this->empresa->ID_Empresa;
		if(($this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503) && $arrParams['ID_Filtro_Empresa']==0){
			$where_id_empresa = "";
		} else if(($this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503) && $arrParams['ID_Filtro_Empresa']!=0){
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
        $ID_Pais=$arrParams['ID_Pais'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? ' AND PC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : '';
        $cond_numero = $ID_Pedido_Cabecera != "-" ? " AND PC.ID_Pedido_Cabecera = '" . $ID_Pedido_Cabecera . "'" : "";
        $cond_estado_pedido = $Nu_Estado_Pedido != "0" ? 'AND PC.Nu_Estado = ' . $Nu_Estado_Pedido : '';
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? ' AND PC.ID_Entidad = ' . $iIdCliente : "";
        $cond_estado_pedido_empresa = $Nu_Estado_Pedido_Empresa != "999" ? 'AND PC.Nu_Estado_Pedido_Empresa = ' . $Nu_Estado_Pedido_Empresa : '';
        $where_nombre_ciudad = ( $sNombreCiudad != '-' ) ? " AND PC.No_Ciudad_Dropshipping LIKE '%" . $sNombreCiudad . "%'" : "";

        $where_pais = $ID_Pais != "0" ? ' AND EMP.ID_Pais = ' . $ID_Pais : '';

        $query = "SELECT
PC.Nu_Estado_Pedido_Empresa,
PC.Fe_Entrega,
PC.No_Entidad_Order_Address_Entry AS No_Entidad,
PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular,
ITEM.No_Producto,
PD.Qt_Producto,
PD.Ss_Precio,
PC.No_Ciudad_Dropshipping,
EMP.No_Empresa,
PC.Ss_Precio_Delivery,
PC.Txt_Direccion_Entidad_Order_Address_Entry,
EMP.ID_Pais,
PC.Txt_Response_TrackingId_Api,
PC.Ss_Total,
PC.Nu_Tipo_Guia_Api,
PC.Ss_Total_Proveeedor,
Nu_Servicio_Transportadora_Dropshipping,
Nu_Forma_Pago_Dropshipping,
PC.Nu_Estado
FROM
pedido_cabecera AS PC
JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera = PD.ID_Pedido_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
JOIN empresa AS EMP ON(EMP.ID_Empresa = PC.ID_Empresa)
WHERE
Nu_Servicio_Transportadora_Dropshipping = 1
AND PC.Fe_Emision BETWEEN '" . $Fe_Inicio . " 00:00:00' AND '" . $Fe_Fin . " 23:59:59'
" . $where_id_empresa . "
" . $cond_tipo . "
" . $cond_numero . "
" . $cond_estado_pedido . "
" . $cond_cliente . "
" . $cond_estado_pedido_empresa . "
" . $where_nombre_ciudad . "
" . $where_pais . "
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

	public function cambiarEstado($ID, $Nu_Estado, $ID_Pais){
        $where = array('ID_Pedido_Cabecera' => $ID);
		//nuevo cambio el 02/09/2023
		$arrData = array( 'Nu_Estado' => $Nu_Estado);

		//PROCESO PARA STOCK
		if($Nu_Estado==2){//2=confirmado
        	$arrData = array( 'Nu_Estado' => $Nu_Estado, 'Nu_Estado_Pedido_Empresa' => 1 );

			//RESTA
			//actualizar stock por proveedor
			$arrParams=array(
				'ID_Pedido_Cabecera' => $ID,
				'Nu_Estado' => $Nu_Estado
			);
			$this->HelperDropshippingModel->actualizarStockGeneral($arrParams);
		}
		
		if($Nu_Estado==14 || $Nu_Estado==6){//14=devuelto y 6=rechazado
			//SUMA
			//actualizar stock por proveedor
			$arrParams=array(
				'ID_Pedido_Cabecera' => $ID,
				'Nu_Estado' => $Nu_Estado
			);
			$this->HelperDropshippingModel->actualizarStockGeneral($arrParams);
		}

		//PROCESO PARA BILLETERA
		//5=entregado //13=Devolución Pendiente //14=Devuelto // 6=Rechazado
		if($Nu_Estado==5 || $Nu_Estado==13 || $Nu_Estado==14 || $Nu_Estado==6){
			$arrParams=array(
				'ID_Pedido_Cabecera' => $ID,
				'Nu_Estado' => $Nu_Estado,
				'ID_Pais' => $ID_Pais
			);
			$this->HelperDropshippingModel->acumularSaldoBilleteraTienda($arrParams);
		}

		if ($this->db->update('pedido_cabecera', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Estado cambiado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function eliminarPedido($ID){
		$this->db->trans_begin();

		//$this->db->where('ID_Pedido_Cabecera', $ID);
		//$this->db->delete('relacion_pedido_documento_cabecera');

        //verificando si descargamos stock o no - hacemos esto porque delibros descarga stock en ese momento y los demás clientes de laesystems cuando se procesa el documento recién además en el api hay un campo para controlar si se descarga o no
		if($this->db->query("SELECT Nu_Descargar_Inventario FROM pedido_cabecera WHERE ID_Pedido_Cabecera=" . $ID . " LIMIT 1")->row()->Nu_Descargar_Inventario == 1){//1=si
            $query = "SELECT * FROM pedido_detalle WHERE ID_Pedido_Cabecera=".$ID;
            $arrDetalle = $this->db->query($query)->result();
            foreach ($arrDetalle as $row) {
                if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){                    
                    $Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto=" . $row->ID_Producto)->row()->Qt_Producto;                    
                    $data_stock_producto = array('Qt_Producto' => ($Qt_Producto + $row->Qt_Producto));
                    $where_stock_producto = array('ID_Producto' => $row->ID_Producto);
                    $this->db->update('stock_producto', $data_stock_producto, $where_stock_producto);
                }
            }
        }

		//$this->db->where('ID_Pedido_Cabecera', $ID);
		//$this->db->delete('pedido_detalle');

		//$this->db->where('ID_Pedido_Cabecera', $ID);
		//$this->db->delete('pedido_cabecera');

        $where_update = array('ID_Pedido_Cabecera' => $ID);//7=Eliminar
        $data_update = array( 'Nu_Estado' => 7, 'Txt_Glosa' => '{usuario: ' . $this->user->No_Usuario . ', Fe_Eliminado: ' . dateNow('fecha_hora') . '}' );//7=eliminado
		$this->db->update('pedido_cabecera', $data_update, $where_update);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Pedido eliminado');
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
			
			if ( $arrPost['ID_Tipo_Documento'] != '2' ) {
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
			
			$iTipoDocumentoIdentidad = trim($arrPost['ID_Tipo_Documento_Identidad']);
			$sNombreEntidad = trim($arrPost['No_Entidad']);
			$sNombreEntidad = strip_tags($sNombreEntidad);
			$sNumeroDocumentoIdentidad = trim($arrPost['Nu_Documento_Identidad']);
			$sNumeroDocumentoIdentidad = strip_tags($sNumeroDocumentoIdentidad);
			if(strlen($sNombreEntidad) < 4 && (empty($sNumeroDocumentoIdentidad) || strlen($sNumeroDocumentoIdentidad)!=8 || strlen($sNumeroDocumentoIdentidad)!=11) ){
				$Last_ID_Entidad = $this->empresa->ID_Entidad_Clientes_Varios_Venta_Predeterminado;//clientes varios
			} else {
				$Last_ID_Entidad = $arrPost['ID_Entidad'];//cliente registrado con una cuenta
				if ($arrPost['ID_Tipo_Documento']!=4 && $arrPost['Nu_Tipo_Recepcion']==6) {//4=boleta y como esta registrado busco, solo si es delivery realizo update
					// Cliente ya esta registrado en BD
					$objCliente = $this->db->query("SELECT Txt_Direccion_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad=" . $Last_ID_Entidad . " LIMIT 1")->row();
					if ( is_object($objCliente) ){
						$Nu_Celular_Entidad = $arrPost['Nu_Celular_Entidad_Order_Address_Entry'];
						if ( (!empty($arrPost['Txt_Direccion_Entidad_Order_Address_Entry']) && $objCliente->Txt_Direccion_Entidad != $arrPost['Txt_Direccion_Entidad_Order_Address_Entry']) || (!empty($Nu_Celular_Entidad) && $objCliente->Nu_Celular_Entidad != $Nu_Celular_Entidad) ) {
							$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['Txt_Direccion_Entidad_Order_Address_Entry'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
							$this->db->query($sql);
						}// /. if cambiar celular o correo
					} else {
						$this->db->trans_rollback();
						return array('sStatus' => 'danger', 'sMessage' => 'No encontro cliente');
					}
				} else if ($arrPost['ID_Tipo_Documento']==3) {//si es 3=factura busco en BD porque ya se creo con el api de tienda
					// Cliente ya esta registrado en BD
					$objCliente = $this->db->query("SELECT ID_Entidad FROM entidad WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND Nu_Tipo_Entidad=0 AND Nu_Documento_Identidad='" . $sNumeroDocumentoIdentidad . "' LIMIT 1")->row();
					if ( is_object($objCliente) ){
						$Last_ID_Entidad = $objCliente->ID_Entidad;

						$Nu_Celular_Entidad = $arrPost['Nu_Celular_Entidad_Order_Address_Entry'];
						if ( (!empty($Nu_Celular_Entidad) && $objCliente->Nu_Celular_Entidad != $Nu_Celular_Entidad) ) {
							$sql = "UPDATE entidad SET Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "' WHERE ID_Entidad = " . $Last_ID_Entidad;
							$this->db->query($sql);
						}// /. if cambiar celular o correo
					} else {
						$this->db->trans_rollback();
						return array('sStatus' => 'danger', 'sMessage' => 'No encontro cliente empresa');
					}
					// fin de cliente
				} else {//el ID entidad existe en la tabla pedido pero por alguna razon no se registro en BD asi que buscaremos nuevamente en entidad por los datos
					$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $iTipoDocumentoIdentidad . " AND Nu_Documento_Identidad = '" . $sNumeroDocumentoIdentidad . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($sNombreEntidad) . "' LIMIT 1";
					$arrResponseSQL = $this->db->query($query);
					if ( $arrResponseSQL->num_rows() > 0 ){
						$arrData = $arrResponseSQL->result();
						$Last_ID_Entidad = $arrData[0]->ID_Entidad;
					} else {
						$sTipoDocumentoIdentidad = 'RUC';
						if($iTipoDocumentoIdentidad==2)
							$sTipoDocumentoIdentidad = 'DNI';
						else if($iTipoDocumentoIdentidad==1)
							$sTipoDocumentoIdentidad = 'OTROS';
						$this->db->trans_rollback();
						return array('sStatus' => 'warning', 'sMessage' => 'No encontro cliente T.D.I: ' . $sTipoDocumentoIdentidad . ' Nro.: ' . $sNumeroDocumentoIdentidad . ' Nombre: ' . limpiarCaracteresEspeciales($sNombreEntidad));
					}
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

            // Crear ítem de delivery = 6
            // verificar si existe ítem con código de servicio, si no hay lo creo
            if ($arrPost['Nu_Tipo_Recepcion'] == 6){
            	$objProducto = $this->db->query("SELECT ID_Producto FROM producto WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND No_Producto LIKE '%DELIVERY%' LIMIT 1; ")->row();
            	//Buscar impuesto DETALLE IGV
	            $ID_Impuesto_Cruce_Documento = $this->db->query("SELECT ID_Impuesto_Cruce_Documento FROM impuesto_cruce_documento WHERE Nu_Estado = 1 AND ID_Impuesto = (SELECT ID_Impuesto FROM impuesto WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Tipo_Impuesto=1 AND No_Impuesto_Breve='IGV' LIMIT 1) LIMIT 1;")->row()->ID_Impuesto_Cruce_Documento;
            	if (!is_object($objProducto) ) {
	            	//Buscar impuesto IGV
	            	$ID_Impuesto = $this->db->query("SELECT ID_Impuesto FROM impuesto WHERE ID_Empresa=".$this->empresa->ID_Empresa." AND Nu_Tipo_Impuesto=1 AND No_Impuesto_Breve='IGV' LIMIT 1;")->row()->ID_Impuesto;

	            	//Buscar categoría
	            	$objFamilia = $this->db->query("SELECT ID_Familia FROM familia WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND No_Familia LIKE '%GENERAL%' LIMIT 1;")->row();
	            	if ( !is_object($objFamilia) ) {
	            		$ID_Familia = $this->db->query("SELECT ID_Familia FROM familia WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " LIMIT 1;")->row()->ID_Familia;
	            	} else {
	            		$ID_Familia=$objFamilia->ID_Familia;	            		
	            	}
	            	//unidad de medida
					$objUnidadMedida = $this->db->query("SELECT ID_Unidad_Medida FROM unidad_medida WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND Nu_Sunat_Codigo='ZZ' LIMIT 1; ")->row();
	            	if ( !is_object($objUnidadMedida) ) {
	            		//crear unidad de medida
						$sql = "INSERT INTO unidad_medida(ID_Empresa, No_Unidad_Medida, Nu_Sunat_Codigo, Nu_Estado) VALUES (" . $this->empresa->ID_Empresa . ", 'UNIDAD (SERVICIOS)', 'ZZ', 1)";
						$this->lae_systems->query($sql);
						$ID_Unidad_Medida = $this->db->insert_id();
	            	} else {
	            		$ID_Unidad_Medida=$objUnidadMedida->ID_Unidad_Medida;
	            	}
	            	$sql = "INSERT INTO producto(ID_Empresa,Nu_Tipo_Producto,Nu_Codigo_Barra,ID_Impuesto,No_Producto,Ss_Precio,ID_Familia,ID_Unidad_Medida,ID_tipo_Producto,ID_Ubicacion_Inventario,ID_Producto_Sunat,ID_Impuesto_Icbper,Qt_CO2_Producto,Nu_Estado) VALUES (" . $this->empresa->ID_Empresa . ",0,'DELIVERY',".$ID_Impuesto.",'DELIVERY',1,".$ID_Familia.",".$ID_Unidad_Medida.",4,1,0,0,0.00,1);";
	                $this->db->query($sql);
					$ID_Producto = $this->db->insert_id();
				} else {
	            	$ID_Producto=$objProducto->ID_Producto;
				}

				$fSubTotalItem = $objPedidoCabecera->Ss_Precio_Delivery;
				$fImpuestoItem = 0.00;
				if ($arrPost['ID_Tipo_Documento'] != '2') {
					$arrImpuestoDetalle = $this->db->query("SELECT Ss_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento = " . $ID_Impuesto_Cruce_Documento . " LIMIT 1")->row();
					$fSubTotalItemCalculo = round($objPedidoCabecera->Ss_Precio_Delivery / $arrImpuestoDetalle->Ss_Impuesto, 2);

					$fSubTotalItem = $fSubTotalItemCalculo;
					$fImpuestoItem = ($objPedidoCabecera->Ss_Precio_Delivery - $fSubTotalItemCalculo);
				}
				//INSERT en el detalle de la VENTA
				$documento_detalle_delivery = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $ID_Producto,
					'Qt_Producto' => 1,
					'Ss_Precio' => $objPedidoCabecera->Ss_Precio_Delivery,
					'Ss_SubTotal' => $fSubTotalItem,
					'Ss_Descuento' => 0.00,
					'Ss_Descuento_Impuesto' => 0.00,
					'Po_Descuento' => 0.00,
					'Txt_Nota' => '',
					'ID_Impuesto_Cruce_Documento' => $ID_Impuesto_Cruce_Documento,
					'Ss_Impuesto' => $fImpuestoItem,
					'Ss_Total' => $objPedidoCabecera->Ss_Precio_Delivery,
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => 0.00,
				);
				$this->db->insert('documento_detalle', $documento_detalle_delivery);
            }//fin de producto delivery

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

			$this->MovimientoInventarioModel->crudMovimientoInventario($arrPost['ID_Almacen'],$Last_ID_Documento_Cabecera,0,$documento_detalle,1,0,'',1,1);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar venta');
			} else {
                //$this->db->trans_rollback();
                $this->db->trans_commit();
                
				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['ID_Tipo_Documento'] != '2') {// cancelado y 2 = Documento interno
					$this->db->trans_commit();

					$arrParams = array(
						'iCodigoProveedorDocumentoElectronico' => 1,
						'iEstadoVenta' => 6,//6=Completado
						'iIdDocumentoCabecera' =>  $Last_ID_Documento_Cabecera,
						'sEmailCliente' => '',
						'sTipoRespuesta' => 'php',
					);
					$arrResponseFE = array();
					
					if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronico( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
						$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoSunat( $arrParams );
						$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFE( $arrResponseFE, $arrParams );
						if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
							return $arrResponseFEMensaje;
						}
						if ( $arrResponseFE['sStatus'] != 'success' ) {
							return $arrResponseFE;
						}
					}
						
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => $arrResponseFE,
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['ID_Tipo_Documento'] == '2') {// cancelado y 2 = Documento interno
					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => '',
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['ID_Tipo_Documento'] != '2') {// pago pendiente y diferente 2 = Documento interno
					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Venta completada pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => '',
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['ID_Tipo_Documento'] == '2') {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();
					
					return array(
						'sStatus' => 'danger',
						'sMessage' => 'No se guardo venta por falta de pago',
						'iIdDocumentoCabecera' => $Last_ID_Documento_Cabecera,
						'arrResponseFE' => '',
					);
				}
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

    public function actualizarPedido($where, $data){
		if ( $this->db->update('pedido_cabecera', $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

	public function agregarPedido($arrPost, $arrPedidoDetalle){
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
			'Nu_Celular_Entidad_Order_Address_Entry' => $arrPost['No_Entidad_Order_Address_Entry'],
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
			'Nu_Estado_Pedido_Empresa' => 1//PEDIDO COMPLETADO Y SOLO PUEDE CORREGIR ECXPRESS
		);
		
		$this->db->insert('pedido_cabecera', $arrSaleOrder);
		$Last_ID_Pedido_Cabecera = $this->db->insert_id();

		$fTotal = 0.00;
		foreach($arrPedidoDetalle as $row) {
			$fTotal = ($row['cantidad'] * $row['precio']);
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
				'Ss_Icbper' => 0
			);
		}
		$this->db->insert_batch('pedido_detalle', $pedido_detalle);

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
EMP.No_Empresa AS No_Empresa_Vendedor,
EMPPROV.No_Empresa,
ALMAPROV.No_Almacen,
EMPPROV.ID_Empresa AS ID_Empresa_Proveedor,
ALMAPROV.ID_Almacen AS ID_Almacen_Proveedor,
CONFIVEND.Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop,
CONFIVEND.Nu_Celular_Whatsapp_Lae_Shop,
CONFIPROV.Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop AS Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop_Proveedor,
CONFIPROV.Nu_Celular_Whatsapp_Lae_Shop AS Nu_Celular_Whatsapp_Lae_Shop_Proveedor
FROM
pedido_cabecera AS PC
LEFT JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera = PD.ID_Pedido_Cabecera)
LEFT JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
LEFT JOIN empresa AS EMP ON(EMP.ID_Empresa = PC.ID_Empresa)
LEFT JOIN empresa AS EMPPROV ON(EMPPROV.ID_Empresa = PD.ID_Empresa_Marketplace_Seller)
LEFT JOIN almacen AS ALMAPROV ON(ALMAPROV.ID_Almacen = PD.ID_Almacen_Marketplace_Seller)
LEFT JOIN configuracion AS CONFIPROV ON(CONFIPROV.ID_Empresa = PD.ID_Empresa_Marketplace_Seller)
LEFT JOIN configuracion AS CONFIVEND ON(CONFIVEND.ID_Empresa = PC.ID_Empresa)
WHERE
PC.ID_Pedido_Cabecera = " . $ID;
        return $this->db->query($query)->result();
    }

	public function cambiarEstadoPedidoEmpresa($ID, $Nu_Estado){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $arrData = array( 'Nu_Estado_Pedido_Empresa' => $Nu_Estado);
		if ($this->db->update('pedido_cabecera', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Actualizado satisfactoriamente');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

    public function actualizarPrecioDelivery($where, $data){
		if ( $this->db->update('pedido_cabecera', $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function actualizarNotaPedido($where, $data){
		$salto_linea="<br>";
		if ( $this->db->query("UPDATE pedido_cabecera SET Txt_Glosa=CONCAT(Txt_Glosa, '" . $salto_linea . " RECHAZADO: " . $data['Txt_Glosa'] .  "') WHERE ID_Pedido_Cabecera=" . $where['ID_Pedido_Cabecera']) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function asignarUsuarioDelivery($where, $data){
		if ($this->db->update('pedido_cabecera', $data, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Delivery asignado satisfactoriamente');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al asignar Delivery');
    }

    public function actualizarPrecioDeliverySaldoAFavor($where, $data){
		if ( $this->db->update('pedido_cabecera', $data, $where) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function generarGuia99($ID_Pedido_Cabecera){
		//generar token para 99 minutos
		$curl = curl_init();
	
		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://delivery.99minutos.com/api/v3/oauth/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode([
			'client_id' => '7a581c2d-c641-490f-9730-2a2e7eeb2d0c',
			'client_secret' => 'RsFGHMclFEMzJ9qvzUVySkq0X5'
		  ]),
		  CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"content-type: application/json"
		  ],
		]);
	
		$response = curl_exec($curl);
		$err = curl_error($curl);
	
		curl_close($curl);
	
		if ($err) {
		  	return array(
				'status' => 'warning',
				'message' => 'error de generar token ' . $err,
			);
		} else {
			$leer_respuesta = json_decode($response, true);
			$access_token = $leer_respuesta['access_token'];

			$query = "SELECT
			STV.Nu_Tipo_Tienda,
			STV.No_Subdominio_Tienda_Virtual,
			STV.No_Dominio_Tienda_Virtual,
			USRPROVEEDOR.Nu_Codigo_Pais,
			USRPROVEEDOR.Nu_Celular,
			EMP.ID_Pais AS ID_Pais_Vendedor,
			PC.No_Entidad_Order_Address_Entry AS No_Entidad_Recipient,
			PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Entidad_Recipient,
			ALMAPROVEEDOR.Txt_Direccion_Almacen,
			DTRPROVEEDOR.No_Distrito,
			PC.Txt_Direccion_Entidad_Order_Address_Entry AS direccion_destination,
			PC.Txt_Direccion_Referencia_Entidad_Order_Address_Entry AS direccion_ref_destination,
			PC.No_Ciudad_Dropshipping AS ciudad_destination,
			MONE.Nu_Sunat_Codigo,
			PC.Ss_Total,
			ITEM.No_Producto,
			PD.Qt_Producto,
			PC.Txt_Email_Dropshipping
			FROM
			pedido_cabecera AS PC
			JOIN subdominio_tienda_virtual AS STV ON(PC.ID_Empresa = STV.ID_Empresa)
			JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera=PD.ID_Pedido_Cabecera)
			JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
			JOIN usuario AS USRPROVEEDOR ON(PD.ID_Empresa_Marketplace_Seller=USRPROVEEDOR.ID_Empresa)
			JOIN almacen AS ALMAPROVEEDOR ON(PD.ID_Almacen_Marketplace_Seller=ALMAPROVEEDOR.ID_Almacen)
			JOIN distrito AS DTRPROVEEDOR ON(ALMAPROVEEDOR.ID_Distrito=DTRPROVEEDOR.ID_Distrito)
			JOIN moneda AS MONE ON(PC.ID_Empresa=MONE.ID_Empresa)
			JOIN empresa AS EMP ON(PC.ID_Empresa=EMP.ID_Empresa)
			WHERE PC.ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
			
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
				$arrDataBD = $arrResponseSQL->result();

				$sNombreTiendaVendedor = $arrDataBD[0]->No_Subdominio_Tienda_Virtual;
				if ($arrDataBD[0]->Nu_Tipo_Tienda==3){
					$sNombreTiendaVendedor = $arrDataBD[0]->No_Dominio_Tienda_Virtual;
				}
				
				$sCodigoPaisVendedor = '51';
				if ($arrDataBD[0]->ID_Pais_Vendedor==2){//2=mexico
					$sCodigoPaisVendedor = '52';
				}

				//obtener codigo postal almacen
				//$arrDataBD[0]->Txt_Direccion_Almacen = $arrDataBD[0]->Txt_Direccion_Almacen . '15900';
				$geeks = $arrDataBD[0]->Txt_Direccion_Almacen;
	
				// Use preg_match_all() function to check match
				preg_match_all('!\d+!', $geeks, $matches);
				
				// print output of function
				if(!empty($matches) && is_array($matches)) {
					for ($x = 0; $x <= count($matches[0]); $x++) {
						if(isset($matches[0][$x])) {
							$sCodigoPostalAlmacen = $matches[0][$x];
							if(strlen($sCodigoPostalAlmacen)>4){
								$sCodigoPostalAlmacen;
								break;
							}
						} else {
							return array(
								'status' => 'warning',
								'message' => 'Falta codigo postal almacen'
							);
						}
					}
				} else {
					return array(
						'status' => 'warning',
						'message' => 'Falta codigo postal almacen'
					);
				}
			
				//obtener codigo postal comprador
				$direccion_destination = $arrDataBD[0]->direccion_destination;
	
				// Use preg_match_all() function to check match
				preg_match_all('!\d+!', $direccion_destination, $arrCodigoPostalComprador);
				
				// print output of function
				if(!empty($arrCodigoPostalComprador) && is_array($arrCodigoPostalComprador)) {
					for ($x = 0; $x <= count($arrCodigoPostalComprador[0]); $x++) {
						if(isset($arrCodigoPostalComprador[0][$x])) {
							$sCodigoPostalComprador = $arrCodigoPostalComprador[0][$x];
							if(strlen($sCodigoPostalComprador)>4){
								$sCodigoPostalComprador;
								break;
							}
						} else {
							return array(
								'status' => 'warning',
								'message' => 'Falta codigo postal comprador'
							);
						}
					}
				} else {
					return array(
						'status' => 'warning',
						'message' => 'Falta codigo postal comprador'
					);
				}
				
				$data = array(
					"shipments" => [
						[
							"sender" => array(
								'firstName' => $sNombreTiendaVendedor,//vendedor
								'lastName' => '.',//vendedor
								'phone' => '+' . $arrDataBD[0]->Nu_Codigo_Pais . $arrDataBD[0]->Nu_Celular,//proveedor
								//'email' => $arrDataBD[0]->correo_vendedor
								'email' => 'soporte.ecxpressmexico@gmail.com'//plataforma
							),
							"recipient" => array(
								'firstName' => $arrDataBD[0]->No_Entidad_Recipient,
								'lastName' => '.',
								'phone' => '+' . $sCodigoPaisVendedor . $arrDataBD[0]->Nu_Celular_Entidad_Recipient,
								'email' => $arrDataBD[0]->Txt_Email_Dropshipping
								//'email' => 'soporte.ecxpressmexico@gmail.com'
							),
							"origin" => array(
								'address' => $arrDataBD[0]->Txt_Direccion_Almacen,
								'country' => 'MEX',
								'reference' => '',
								'zipcode' => $sCodigoPostalAlmacen,
								'city' => $arrDataBD[0]->No_Distrito
							),
							"destination" => array(
								'address' => $arrDataBD[0]->direccion_destination,
								'country' => 'MEX',
								'reference' => $arrDataBD[0]->direccion_ref_destination,
								'zipcode' => $sCodigoPostalComprador,//falta cambiar
								'city' => $arrDataBD[0]->ciudad_destination
							),
							"payments" => array(
								"cashOnDelivery" => array(
									'currency' => $arrDataBD[0]->Nu_Sunat_Codigo,
									'amount' => $arrDataBD[0]->Ss_Total
								),
							),
							"deliveryType" => 'NXD'
						]
					]
				);
				
				$i=0;
				$sNoteProducto = '';
				foreach ($arrDataBD as $row) {
					$data["shipments"][0]["items"][$i]["size"] = 'xs';
					$data["shipments"][0]["items"][$i]["description"] = $row->No_Producto;
					$data["shipments"][0]["items"][$i]["weight"] = '10';
					$sComa='';
					if($i>0){
						$sComa = ', ';
					}
					$sNoteProducto .= $sComa . $row->No_Producto . ' Cant: ' . round($row->Qt_Producto, 0);
					$i++;
				}
				
				$data["shipments"][0]["options"]["notes"] = $sNoteProducto;
				
				$data_json = json_encode($data);

				$ch = curl_init();
				$ruta="https://delivery.99minutos.com/api/v3/orders";
				curl_setopt($ch, CURLOPT_URL, $ruta);
				curl_setopt(
					$ch, CURLOPT_HTTPHEADER, array(
						"accept: application/json",
						"authorization: Bearer " . $access_token,
						"Content-Type: application/json",
					)
				);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$response = curl_exec($ch);
				$err = curl_error($ch);
				
				curl_close($ch);

				if ($err) {
					return array(
						'status' => 'warning',
						'message' => 'No se encontro registro . ' . $err,
					);
				} else {
					$leer_respuesta = json_decode($response, true);

					//GENERACION DE PRECIO DE DELIVERY 99 MINUTOS MEXICO
					$data = array(
						'destination_country' => 'MEX',
						'destination_zipcode' => '',
						'origin_country' => 'MEX',
						'origin_zipcode' => '',
						'cash_on_delivery' => $arrDataBD[0]->Ss_Total,
						'delivery_type' => 'NXD',
						'insurance' => '',
						'size' => 'xs'
 					);

					$data_json = json_encode($data);

					$sCodigoPais="MEX";
					$url = 'https://delivery.99minutos.com/api/v3/shipping/rates/zipcodes/' . $sCodigoPais .'/' . $sCodigoPostalAlmacen . '/' . $sCodigoPais . '/' . $sCodigoPostalComprador . '?cash_on_delivery=' . $arrDataBD[0]->Ss_Total . '&delivery_type=NXD&size=xs';

					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt(
						$curl, CURLOPT_HTTPHEADER, array(
							"accept: application/json",
							"authorization: Bearer " . $access_token,
							"Content-Type: application/json",
						)
					);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					$response_precio = curl_exec($curl);
					curl_close($curl);
					
					$leer_respuesta_precio = json_decode($response_precio, true);

					$fPrecioDelivery = $leer_respuesta_precio['data'][0]['prices']['total'];
					if($fPrecioDelivery <= 0 && empty($fPrecioDelivery))
						$fPrecioDelivery = 170;
					// FIN PRECIO

					$fPrecioDelivery += 10;

					if(!empty($leer_respuesta['data']) && is_array($leer_respuesta['data'])){
						//guardar trackingId para luego consultar el PDF para generar
						if( isset($leer_respuesta['data']['shipments'][0]['trackingId']) && !empty($leer_respuesta['data']['shipments'][0]['trackingId']) ){
							$update_pedido = "UPDATE pedido_cabecera SET Ss_Precio_Delivery=" . $fPrecioDelivery . ", Nu_Tipo_Guia_Api=1, Nu_Estado_Pedido_Empresa = 1, Nu_Estado=8, Txt_Response_TrackingId_Api='" . $leer_respuesta['data']['shipments'][0]['trackingId'] . "' WHERE ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
							$this->db->query($update_pedido);
						}

						return array(
							'status' => 'success',
							'message' => 'Orden creada satisfactoriamente',
							'result' => $leer_respuesta
							//'result2' => $response_precio
						);
					} else {
						return array(
							'status' => 'warning',
							'message' => $leer_respuesta['message'],
							'errors' => $leer_respuesta
							//'errors2' => $response_precio
						);
					}
				}
			}//if de BD
			
			return array(
				'status' => 'warning',
				'message' => 'No se encontro registro',
			);
		}
	}
	
    public function generarGuiaQuiken($ID_Pedido_Cabecera){
		//var_dump($ID_Pedido_Cabecera);

		$query = "SELECT
STV.Nu_Tipo_Tienda,
STV.No_Subdominio_Tienda_Virtual,
STV.No_Dominio_Tienda_Virtual,
USRPROVEEDOR.Nu_Codigo_Pais,
USRPROVEEDOR.Nu_Celular,
EMP.ID_Pais AS ID_Pais_Vendedor,
PC.No_Entidad_Order_Address_Entry AS No_Entidad_Recipient,
PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Entidad_Recipient,
ALMAPROVEEDOR.Txt_Direccion_Almacen,
DTRPROVEEDOR.No_Distrito,
PC.Txt_Direccion_Entidad_Order_Address_Entry AS direccion_destination,
PC.Txt_Direccion_Referencia_Entidad_Order_Address_Entry AS direccion_ref_destination,
PC.No_Ciudad_Dropshipping AS ciudad_destination,
MONE.Nu_Sunat_Codigo,
PC.Ss_Total,
ITEM.No_Producto,
PD.Qt_Producto,
PD.Ss_Precio_Empresa_Proveedor,
CONFI.No_Tienda_Lae_Shop,
PC.Txt_Email_Dropshipping
FROM
pedido_cabecera AS PC
JOIN configuracion AS CONFI ON(PC.ID_Empresa = CONFI.ID_Empresa)
JOIN subdominio_tienda_virtual AS STV ON(PC.ID_Empresa = STV.ID_Empresa)
JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera=PD.ID_Pedido_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
JOIN usuario AS USRPROVEEDOR ON(PD.ID_Empresa_Marketplace_Seller=USRPROVEEDOR.ID_Empresa)
JOIN almacen AS ALMAPROVEEDOR ON(PD.ID_Almacen_Marketplace_Seller=ALMAPROVEEDOR.ID_Almacen)
JOIN distrito AS DTRPROVEEDOR ON(ALMAPROVEEDOR.ID_Distrito=DTRPROVEEDOR.ID_Distrito)
JOIN moneda AS MONE ON(PC.ID_Empresa=MONE.ID_Empresa)
JOIN empresa AS EMP ON(PC.ID_Empresa=EMP.ID_Empresa)
WHERE PC.ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
		
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
			$arrDataBD = $arrResponseSQL->result();

			$sNombreTiendaVendedor = $arrDataBD[0]->No_Subdominio_Tienda_Virtual;
			if ($arrDataBD[0]->Nu_Tipo_Tienda==3){
				$sNombreTiendaVendedor = $arrDataBD[0]->No_Dominio_Tienda_Virtual;
			}
			
			$sCodigoPaisVendedor = '51';
			if ($arrDataBD[0]->ID_Pais_Vendedor==2){//2=mexico
				$sCodigoPaisVendedor = '52';
			}

			//obtener codigo postal almacen
			//$arrDataBD[0]->Txt_Direccion_Almacen = $arrDataBD[0]->Txt_Direccion_Almacen . '15900';
			$geeks = $arrDataBD[0]->Txt_Direccion_Almacen;

			// Use preg_match_all() function to check match
			preg_match_all('!\d+!', $geeks, $matches);
			
			// print output of function
			if(!empty($matches) && is_array($matches)) {
				for ($x = 0; $x <= count($matches[0]); $x++) {
					if(isset($matches[0][$x])) {
						$sCodigoPostalAlmacen = $matches[0][$x];
						if(strlen($sCodigoPostalAlmacen)>4){
							$sCodigoPostalAlmacen;
							break;
						}
					} else {
						return array(
							'status' => 'warning',
							'message' => 'Falta codigo postal almacen'
						);
					}
				}
			} else {
				return array(
					'status' => 'warning',
					'message' => 'Falta codigo postal almacen'
				);
			}
		
			//obtener codigo postal comprador
			$direccion_destination = $arrDataBD[0]->direccion_destination;

			// Use preg_match_all() function to check match
			preg_match_all('!\d+!', $direccion_destination, $arrCodigoPostalComprador);
			
			// print output of function
			if(!empty($arrCodigoPostalComprador) && is_array($arrCodigoPostalComprador)) {
				for ($x = 0; $x <= count($arrCodigoPostalComprador[0]); $x++) {
					if(isset($arrCodigoPostalComprador[0][$x])) {
						$sCodigoPostalComprador = $arrCodigoPostalComprador[0][$x];
						if(strlen($sCodigoPostalComprador)>4){
							$sCodigoPostalComprador;
							break;
						}
					} else {
						return array(
							'status' => 'warning',
							'message' => 'Falta codigo postal comprador'
						);
					}
				}
			} else {
				return array(
					'status' => 'warning',
					'message' => 'Falta codigo postal comprador'
				);
			}
			
			$i=0;
			$sNoteProducto = '';
			foreach ($arrDataBD as $row) {
				$sComa='';
				if($i>0){
					$sComa = ', ';
				}
				$sNoteProducto .= $sComa . $row->No_Producto . ' Cant: ' . round($row->Qt_Producto, 0);
				$i++;
			}

			$data = array(
				"origin" => array(
					"name" => $arrDataBD[0]->No_Tienda_Lae_Shop,//vendedor
					"company" => $sNombreTiendaVendedor,//vendedor
					//"email" => $arrDataBD[0]->correo_vendedor,//vendedor
					"email" => "soporte.ecxpressmexico@gmail.com",//ecxpress
					"phone" => $arrDataBD[0]->Nu_Celular,//proveedor
					"street" => $arrDataBD[0]->Txt_Direccion_Almacen,
					"number" => "",
					"district" => "-",
					"city" => $arrDataBD[0]->No_Distrito,
					"state" => "-",
					"country" => "MX",
					"postalCode" => $sCodigoPostalAlmacen,
					"reference" => ""
				),
				"destination" => array(
					"name" => $arrDataBD[0]->No_Entidad_Recipient,
					"company" => "",
					"email" => $arrDataBD[0]->Txt_Email_Dropshipping,
					"phone" => $arrDataBD[0]->Nu_Celular_Entidad_Recipient,
					"street" => $arrDataBD[0]->direccion_destination,
					"number" => "",
					"district" => "",
					"city" => $arrDataBD[0]->ciudad_destination,
					"state" => "-",
					"country" => "MX",
					"postalCode" => $sCodigoPostalComprador,
					"reference" => $arrDataBD[0]->direccion_ref_destination . " - " . $sNoteProducto
				),
				"shipment" => array(
					"carrier" => "quiken",
					"service" => "national_ground",
					"type" => 15
				)
			);
			
			$i=0;
			$sNoteProducto = '';
			foreach ($arrDataBD as $row) {
				$data["packages"][$i]["content"] = $row->No_Producto;
				$data["packages"][$i]["amount"] = round($row->Qt_Producto);
				$data["packages"][$i]["type"] = 'box';
				$data["packages"][$i]["dimensions"]["length"] = 20;//largo
				$data["packages"][$i]["dimensions"]["width"] = 20;//ancho
				$data["packages"][$i]["dimensions"]["height"] = 20;//alto
				$data["packages"][$i]["weight"] = 1;
				$data["packages"][$i]["insurance"] = 0;
				$data["packages"][$i]["declaredValue"] = ($row->Ss_Precio_Empresa_Proveedor * $row->Qt_Producto);//precio de proveedor x cantidad
				$data["packages"][$i]["weightUnit"] = 'KG';
				$data["packages"][$i]["lengthUnit"] = 'CM';

				$sComa='';
				if($i>0){
					$sComa = ', ';
				}
				$sNoteProducto .= $sComa . $row->No_Producto . ' Cant: ' . round($row->Qt_Producto, 0);
				$i++;
			}

			$data["settings"]["currency"] = "MXN";
			$data["settings"]["labelFormat"] = "pdf";
			$data["settings"]["printFormat"] = "PDF";
			$data["settings"]["cashOnDelivery"] = $arrDataBD[0]->Ss_Total;
			$data["settings"]["printSize"] = "STOCK_4X6";
			$data["settings"]["comments"] = $sNoteProducto;

			$data_json = json_encode($data);

			$access_token = '0a29d5e7507a8a6bf2087fb93aeef4de03a012eb73b7aef0b5580b3d7d2d074a';

			$ch = curl_init();
			$ruta="https://api.envia.com/ship/generate";
			curl_setopt($ch, CURLOPT_URL, $ruta);
			curl_setopt(
				$ch, CURLOPT_HTTPHEADER, array(
					"accept: application/json",
					"authorization: Bearer " . $access_token,
					"Content-Type: application/json",
				)
			);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($ch);
			$err = curl_error($ch);
			
			curl_close($ch);

			$leer_respuesta = json_decode($response, true);

			if ($err) {
				return array(
					'status' => 'warning',
					'message' => 'No se encontro registro . ' . $err,
				);
			} else {
				$leer_respuesta = json_decode($response, true);

				/*
				echo "<pre>";
				var_dump($leer_respuesta);
				echo "</pre>";
				*/

				if(!empty($leer_respuesta['data']) && is_array($leer_respuesta['data'])){
					//guardar trackingId para luego consultar el PDF para generar
					//if( isset($leer_respuesta['data']['shipments'][0]['trackingId']) && !empty($leer_respuesta['data']['shipments'][0]['trackingId']) ){
						$fPrecioDelivery = $leer_respuesta['data'][0]['totalPrice'];
						$fPrecioDelivery += (($arrDataBD[0]->Ss_Total * 5) / 100);

						$update_pedido = "UPDATE pedido_cabecera SET Ss_Precio_Delivery=" . $fPrecioDelivery . ", Nu_Tipo_Guia_Api=2, Nu_Estado_Pedido_Empresa = 1, Nu_Estado=8, Txt_Response_Guia_Api='" . $leer_respuesta['data'][0]['label'] . "', Txt_Response_TrackingId_Api='" . $leer_respuesta['data'][0]['trackingNumber'] . "' WHERE ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
						$this->db->query($update_pedido);
					//}

					return array(
						'status' => 'success',
						'message' => 'Orden creada satisfactoriamente',
					);
				} else {
					/*
					echo "<pre>";
					var_dump($leer_respuesta);
					echo "</pre>";
*/
					return array(
						'status' => 'warning',
						'message' => $leer_respuesta['error']['message'],
						'errors' => $leer_respuesta
					);
				}
			}
		} //if de BD
	
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro',
		);
	}	
	
    public function generarGuiaEcxlae($ID_Pedido_Cabecera){
		//generar token para 99 minutos
		$curl = curl_init();

		//$url_global = "https://api-dev.tiui.app/api/";//DEMOSTRACION
		//$tiuiAmigoId = 111;
		$url_global = "https://api.tiui.app/api/";//PRODUCCION
		$tiuiAmigoId = 127;

		$url = $url_global . "AccountTiuiAmigo/login";
		$user_guia = "soporte.ecxpressmexico@gmail.com";
		$password_guia = "100ECXLAE44";

		curl_setopt_array($curl, [
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode([
			'userName' => $user_guia,
			'password' => $password_guia
		  ]),
		  CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"content-type: application/json"
		  ],
		]);
	
		$response = curl_exec($curl);
		$err = curl_error($curl);
	
		curl_close($curl);
	
		if ($err) {
		  	return array(
				'status' => 'warning',
				'message' => 'error de generar token ' . $err,
			);
		} else {
			$leer_respuesta = json_decode($response, true);
			$access_token = $leer_respuesta['accessToken'];

			$query = "SELECT
			USRPROVEEDOR.Nu_Codigo_Pais,
			USRPROVEEDOR.Nu_Celular,
			EMP.ID_Pais AS ID_Pais_Vendedor,
			PC.No_Entidad_Order_Address_Entry AS No_Entidad_Recipient,
			PC.Nu_Celular_Entidad_Order_Address_Entry AS Nu_Celular_Entidad_Recipient,
			ALMAPROVEEDOR.Txt_Direccion_Almacen,
			DTRPROVEEDOR.No_Distrito,
			PROPROVEEDOR.No_Provincia,
			PC.Txt_Direccion_Entidad_Order_Address_Entry AS direccion_destination,
			PC.Txt_Direccion_Referencia_Entidad_Order_Address_Entry AS direccion_ref_destination,
			PC.No_Ciudad_Dropshipping AS ciudad_destination,
			MONE.Nu_Sunat_Codigo,
			PC.Ss_Total,
			ITEM.No_Producto,
			PD.Qt_Producto,
			PD.Ss_Precio_Empresa_Proveedor,
			CONFI.No_Tienda_Lae_Shop,
			PC.Txt_Email_Dropshipping,
			EMPPROVEEDOR.No_Empresa AS No_Empresa_Proveedor,
			USRVENTA.No_Usuario AS correo_vendedor
			FROM
			pedido_cabecera AS PC
			JOIN configuracion AS CONFI ON(PC.ID_Empresa = CONFI.ID_Empresa)
			JOIN pedido_detalle AS PD ON(PC.ID_Pedido_Cabecera=PD.ID_Pedido_Cabecera)
			JOIN producto AS ITEM ON(ITEM.ID_Producto = PD.ID_Producto)
			JOIN usuario AS USRPROVEEDOR ON(PD.ID_Empresa_Marketplace_Seller=USRPROVEEDOR.ID_Empresa)
			JOIN empresa AS EMPPROVEEDOR ON(PD.ID_Empresa_Marketplace_Seller=EMPPROVEEDOR.ID_Empresa)
			JOIN almacen AS ALMAPROVEEDOR ON(PD.ID_Almacen_Marketplace_Seller=ALMAPROVEEDOR.ID_Almacen)
			JOIN distrito AS DTRPROVEEDOR ON(ALMAPROVEEDOR.ID_Distrito=DTRPROVEEDOR.ID_Distrito)
			JOIN provincia AS PROPROVEEDOR ON(ALMAPROVEEDOR.ID_Provincia=PROPROVEEDOR.ID_Provincia)
			JOIN moneda AS MONE ON(PC.ID_Empresa=MONE.ID_Empresa)
			JOIN usuario AS USRVENTA ON(PC.ID_Empresa=USRVENTA.ID_Empresa)
			JOIN empresa AS EMP ON(PC.ID_Empresa=EMP.ID_Empresa)
			WHERE PC.ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
			
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
				$arrDataBD = $arrResponseSQL->result();

				$sNombreTiendaVendedor = $arrDataBD[0]->No_Subdominio_Tienda_Virtual;
				if ($arrDataBD[0]->Nu_Tipo_Tienda==3){
					$sNombreTiendaVendedor = $arrDataBD[0]->No_Dominio_Tienda_Virtual;
				}
				
				$sCodigoPaisVendedor = '51';
				if ($arrDataBD[0]->ID_Pais_Vendedor==2){//2=mexico
					$sCodigoPaisVendedor = '52';
				}

				//obtener codigo postal almacen
				//$arrDataBD[0]->Txt_Direccion_Almacen = $arrDataBD[0]->Txt_Direccion_Almacen . '15900';
				$geeks = $arrDataBD[0]->Txt_Direccion_Almacen;

				// Use preg_match_all() function to check match
				preg_match_all('!\d+!', $geeks, $matches);
				
				// print output of function
				if(!empty($matches) && is_array($matches)) {
					for ($x = 0; $x <= count($matches[0]); $x++) {
						if(isset($matches[0][$x])) {
							$sCodigoPostalAlmacen = $matches[0][$x];
							if(strlen($sCodigoPostalAlmacen)>4){
								$sCodigoPostalAlmacen;
								break;
							}
						} else {
							return array(
								'status' => 'warning',
								'message' => 'Falta codigo postal almacen'
							);
						}
					}
				} else {
					return array(
						'status' => 'warning',
						'message' => 'Falta codigo postal almacen'
					);
				}
			
				//obtener codigo postal comprador
				$direccion_destination = $arrDataBD[0]->direccion_destination;

				// Use preg_match_all() function to check match
				preg_match_all('!\d+!', $direccion_destination, $arrCodigoPostalComprador);
				
				// print output of function
				if(!empty($arrCodigoPostalComprador) && is_array($arrCodigoPostalComprador)) {
					for ($x = 0; $x <= count($arrCodigoPostalComprador[0]); $x++) {
						if(isset($arrCodigoPostalComprador[0][$x])) {
							$sCodigoPostalComprador = $arrCodigoPostalComprador[0][$x];
							if(strlen($sCodigoPostalComprador)>4){
								$sCodigoPostalComprador;
								break;
							}
						} else {
							return array(
								'status' => 'warning',
								'message' => 'Falta codigo postal comprador'
							);
						}
					}
				} else {
					return array(
						'status' => 'warning',
						'message' => 'Falta codigo postal comprador'
					);
				}
				
				$i=0;
				$iCantidadPaquetes=0;
				$sNoteProducto = '';
				foreach ($arrDataBD as $row) {
					$sComa='';
					if($i>0){
						$sComa = ', ';
					}
					$sNoteProducto .= $sComa . $row->No_Producto . ' Cant: ' . round($row->Qt_Producto, 0);
					$i++;
					++$iCantidadPaquetes;
				}
				
				$arrCiudadEstadoDestinatario = (explode(",",$arrDataBD[0]->ciudad_destination));
				//$arrCiudadDestination[0];//Ciudad
				//$arrCiudadDestination[0];//Estado

				$data = array(
					"guiaId" => 0,
					"folio" => "",
					"esPagoContraEntrega" => true,
					"importeContraEntrega" => $arrDataBD[0]->Ss_Total,
					"tieneSeguroMercancia" => false, 
					"importeSeguroMercancia" => 0,
					"importeCalculoSeguro" => 0,//valor mercanceia
					"importePaqueteria" => 0,
					"costoOperativo" => 0,
					"subTotal" => 0,//comision de tiui
					"iva" => 0,//comision de tiui
					"total" => 0,//comision de tiui
					"remitente" => array(
						"nombre" => $arrDataBD[0]->No_Tienda_Lae_Shop,
						"empresa" => $arrDataBD[0]->No_Empresa_Proveedor,
						"telefono" => $arrDataBD[0]->Nu_Celular,
						//"correoElectronico" => "soporte.ecxpressmexico@gmail.com",
						"correoElectronico" => "soporte.ecxpressmexico@gmail.com",
						"calle" => $arrDataBD[0]->Txt_Direccion_Almacen,
						"cruzamiento" => "",
						"numero" => "",
						"colonia" => "",
						"codigoPostal" => $sCodigoPostalAlmacen,
						"ciudad" => $arrDataBD[0]->No_Distrito,
						"estado" => $arrDataBD[0]->No_Provincia,
						"pais" => "México",
						"referencias" => "",
						"municipioId"=> 1,
						"tiuiAmigoId"=> $tiuiAmigoId
					),
					"destinatario" => array(
						"nombre" => $arrDataBD[0]->No_Entidad_Recipient,
						"empresa" => "Ecxlae",
						"telefono" => $arrDataBD[0]->Nu_Celular_Entidad_Recipient,
						"correoElectronico" => $arrDataBD[0]->Txt_Email_Dropshipping,
						"calle" => $arrDataBD[0]->direccion_destination,
						"cruzamiento" => "-",
						"numero" => "-",
						"colonia" => "-",
						"codigoPostal" => $sCodigoPostalComprador,
						"ciudad" => $arrCiudadEstadoDestinatario[0],
						"estado" => $arrCiudadEstadoDestinatario[1],
						"pais" => "México",
						"referencias" => $arrDataBD[0]->direccion_ref_destination,
						"municipioId" => 1,
						"tiuiAmigoId" => $tiuiAmigoId
					),
					"paquete" => array(
						"paqueteId" => 0,
						"largo" => 20,
						"alto" => 20,
						"ancho" => 20,
						"pesoFisico" => 1,
						"pesoCotizado" => 1
					),
					"tiuiAmigoId" => $tiuiAmigoId,
					"paqueteriaId" => 1,//24 a 48 horas
					"registrarRemitente" => false,
					"registrarDestinatario" => false,
					"registrarConfiguracionPaquete" => false,
					"cobroContraEntrega" => 0,
					"cantidadPaquetes" => $iCantidadPaquetes,
					"nombreProducto" => $sNoteProducto
				);

				$data_json = json_encode($data);

				$ch = curl_init();
						
				$url = $url_global . "guia";

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt(
					$ch, CURLOPT_HTTPHEADER, array(
						"accept: application/json",
						"Authorization: Bearer " . $access_token,
						"Content-Type: application/json; charset=utf-8",
					)
				);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$response = curl_exec($ch);
				$err = curl_error($ch);

				/*
				echo "<pre>";
				var_dump($response);
				echo "</pre>";
				*/

				curl_close($ch);

				$leer_respuesta = json_decode($response, true);

				if ($leer_respuesta['success']==false) {
					return array(
						'status' => 'warning',
						'message' => 'No se encontro registro . ' . $err,
						'errors' => $leer_respuesta,
						'json' => $data_json
					);
				} else {
					$leer_respuesta = json_decode($response, true);
					
					if($leer_respuesta['success'] == true){
						//$fPrecioDelivery = $leer_respuesta['data'][0]['totalPrice'];
						//$fPrecioDelivery += (($arrDataBD[0]->Ss_Total * 5) / 100);

						$update_pedido = "UPDATE pedido_cabecera SET Nu_Tipo_Guia_Api=3, Nu_Estado_Pedido_Empresa=1, Nu_Estado=8, Txt_Response_Guia_Api='" . $leer_respuesta['entity']['folio'] . "' WHERE ID_Pedido_Cabecera=" . $ID_Pedido_Cabecera;
						$this->db->query($update_pedido);

						return array(
							'status' => 'success',
							'message' => 'Guía creada satisfactoriamente',
						);
					} else {
						return array(
							'status' => 'warning',
							'message' => 'problemas al generar guia',
							'errors' => $leer_respuesta
						);
					}
				}
			} //if de BD
		
			return array(
				'status' => 'warning',
				'message' => 'No se encontro registro',
			);
		}
	}
}
