<?php
class DespachoPOSModel extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($arrParams){
        $iTipoConsultaFecha=$arrParams['iTipoConsultaFecha'];
        $Fe_Inicio=$arrParams['Fe_Inicio'];
        $Fe_Fin=$arrParams['Fe_Fin'];
        $ID_Tipo_Documento=$arrParams['ID_Tipo_Documento'];
        $ID_Serie_Documento=$arrParams['ID_Serie_Documento'];
        $ID_Numero_Documento=$arrParams['ID_Numero_Documento'];
        $Nu_Estado_Documento=$arrParams['Nu_Estado_Documento'];
        $iIdCliente=$arrParams['iIdCliente'];
        $sNombreCliente=$arrParams['sNombreCliente'];
        $iTipoRecepcionCliente=$arrParams['iTipoRecepcionCliente'];
        $iEstadoPago=$arrParams['iEstadoPago'];
		$iTipoRecepcionClienteEstado=$arrParams['iTipoRecepcionClienteEstado'];

        $cond_tipo = $ID_Tipo_Documento != "0" ? 'AND VC.ID_Tipo_Documento = ' . $ID_Tipo_Documento : 'AND VC.ID_Tipo_Documento IN(2,3,4,5,6)';
        $cond_serie = $ID_Serie_Documento != "0" ? "AND VC.ID_Serie_Documento = '" . $ID_Serie_Documento . "'" : "";
        $cond_numero = $ID_Numero_Documento != "-" ? "AND VC.ID_Numero_Documento = '" . $ID_Numero_Documento . "'" : "";
        $cond_estado_documento = $Nu_Estado_Documento != "0" ? 'AND VC.Nu_Estado = ' . $Nu_Estado_Documento : "";
        $cond_cliente = ( $iIdCliente != '-' && $sNombreCliente != '-' ) ? 'AND VC.ID_Entidad = ' . $iIdCliente : "";
        $cond_tipo_recepcion_cliente = $iTipoRecepcionCliente != "0" ? 'AND VC.Nu_Tipo_Recepcion = ' . $iTipoRecepcionCliente : "AND VC.Nu_Tipo_Recepcion IN(6,7)";
		$cond_tipo_recepcion_cliente_despacho = $iTipoRecepcionClienteEstado != "-" ? 'AND VC.Nu_Estado_Despacho_Pos = ' . $iTipoRecepcionClienteEstado : "";

        if ( $iTipoConsultaFecha=='actual' ) {//0=Actual
            $cond_fecha_matricula_empleado = "
AND VC.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . "
AND VC.Fe_Emision_Hora>='" . $this->session->userdata['arrDataPersonal']['arrData'][0]->Fe_Matricula . "'";
        } else if ( $iTipoConsultaFecha=='hoy' ) {
			$cond_fecha_matricula_empleado = "AND VC.Fe_Emision='" . dateNow('fecha') . "'";
		} else if ( $iTipoConsultaFecha=='semana' ) {
			$cond_fecha_matricula_empleado = "AND VC.Fe_Emision>=date_add('" . dateNow('fecha') . "', INTERVAL -1 WEEK)";
		} else if ( $iTipoConsultaFecha=='mes' ) {
			$cond_fecha_matricula_empleado = "AND VC.Fe_Emision>=date_add('" . dateNow('fecha') . "', INTERVAL -1 MONTH)";
		}

        $cond_estado_pago = '';
        if ( $iEstadoPago == "1" )// Pendiente
            $cond_estado_pago = 'AND VC.Ss_Total_Saldo > 0.00';
        else if ( $iEstadoPago == "2" )// Cancelado
			$cond_estado_pago = 'AND VC.Ss_Total_Saldo = 0.00';
	
        $query = "SELECT
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Moneda,
VC.ID_Lista_Precio_Cabecera,
VC.ID_Transporte_Delivery,
VC.Fe_Emision,
VC.Fe_Entrega,
VC.ID_Documento_Cabecera,
VC.ID_Tipo_Documento,
VC.Nu_Estado,
'' AS No_Tipo_Recepcion,
DELI.No_Entidad AS No_Delivery,
VC.Fe_Emision_Hora,
TD.No_Tipo_Documento_Breve,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
CLI.ID_Entidad,
CLI.No_Entidad,
CLI.Txt_Direccion_Entidad,
CLI.Nu_Celular_Entidad,
'' AS No_Estado,
'' AS No_Class_Estado,
'' AS No_Estado_Delivery,
'' AS No_Class_Estado_Delivery,
MONE.No_Signo,
VC.Ss_Total,
VC.Nu_Estado_Despacho_Pos,
TDG.No_Tipo_Documento_Breve AS No_Tipo_Documento_Breve_Guia,
GC.ID_Serie_Documento AS ID_Serie_Documento_Guia,
GC.ID_Numero_Documento AS ID_Numero_Documento_Guia,
VC.Nu_Tipo_Recepcion,
VC.Txt_Url_CDR,
VC.Txt_Url_PDF
FROM
documento_cabecera AS VC
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN tipo_documento AS TD ON(TD.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN entidad AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN entidad AS DELI ON(DELI.ID_Entidad = VC.ID_Transporte_Delivery)
LEFT JOIN guia_enlace AS GE ON(GE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
LEFT JOIN guia_cabecera AS GC ON(GC.ID_Guia_Cabecera = GE.ID_Guia_Cabecera)
LEFT JOIN tipo_documento AS TDG ON(TDG.ID_Tipo_Documento = GC.ID_Tipo_Documento)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
" . $cond_tipo_recepcion_cliente . "
" . $cond_fecha_matricula_empleado . "
" . $cond_tipo_recepcion_cliente_despacho . "
ORDER BY
VC.Fe_Entrega DESC;";
 //array_debug($query);
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
			$query_total = "SELECT
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 6
AND VC.Nu_Estado_Despacho_Pos = 0
" . $cond_fecha_matricula_empleado . "
) AS Total_Delivery_Pendiente,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 6
AND VC.Nu_Estado_Despacho_Pos = 1
" . $cond_fecha_matricula_empleado . "
) AS Total_Delivery_Preparando,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 6
AND VC.Nu_Estado_Despacho_Pos = 2
" . $cond_fecha_matricula_empleado . "
) AS Total_Delivery_Enviado,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 6
AND VC.Nu_Estado_Despacho_Pos = 3
" . $cond_fecha_matricula_empleado . "
) AS Total_Delivery_Entregado,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 6
AND VC.Nu_Estado_Despacho_Pos = 4
" . $cond_fecha_matricula_empleado . "
) AS Total_Delivery_Rechazado,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 7
AND VC.Nu_Estado_Despacho_Pos = 0
" . $cond_fecha_matricula_empleado . "
) AS Total_Recojo_Pendiente,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 7
AND VC.Nu_Estado_Despacho_Pos = 1
" . $cond_fecha_matricula_empleado . "
) AS Total_Recojo_Preparando,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 7
AND VC.Nu_Estado_Despacho_Pos = 2
" . $cond_fecha_matricula_empleado . "
) AS Total_Recojo_Enviado,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 7
AND VC.Nu_Estado_Despacho_Pos = 3
" . $cond_fecha_matricula_empleado . "
) AS Total_Recojo_Entregado,
(SELECT COUNT(*) FROM documento_cabecera AS VC JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK) WHERE 
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion = 7
AND VC.Nu_Estado_Despacho_Pos = 4
" . $cond_fecha_matricula_empleado . "
) AS Total_Recojo_Rechazado
FROM
documento_cabecera AS VC
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
WHERE
VC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND VC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND VC.ID_Tipo_Asiento = 1
AND SD.ID_POS > 0
AND VC.Nu_Tipo_Recepcion IN(6,7) LIMIT 1";
			if ( !$this->db->simple_query($query_total) ){
				$error = $this->db->error();
				return array(
					'sStatus' => 'danger',
					'sMessage' => 'Problemas al obtener datos',
					'sCodeSQL' => $error['code'],
					'sMessageSQL' => $error['message'],
				);
			}
        	$arrResponseSQLTotal = $this->db->query($query_total);

            return array(
                'sStatus' => 'success',
                'arrData' => $arrResponseSQL->result(),
				'arrDataTotal' => $arrResponseSQLTotal->result(),
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontráron registro',
        );
    }

	public function estadoPedido($ID, $Nu_Estado){
        $where = array('ID_Documento_Cabecera' => $ID);
        $arrData = array( 'Nu_Estado_Despacho_Pos' => $Nu_Estado );
		if ($this->db->update('documento_cabecera', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function generarGuia($arrPost){
		$this->db->trans_begin();

		$iTipoDocumento = $arrPost['radio-TipoDocumento'];
		if($iTipoDocumento == 8)
			$iTipoDocumento = 7;

		$where_serie = '';
		if ($arrPost['radio-TipoDocumento'] == 7)
			$where_serie = 'AND ID_Serie_Documento LIKE "0%"';
		if ($arrPost['radio-TipoDocumento'] == 8)
			$where_serie = 'AND ID_Serie_Documento LIKE "T%"';
		
		$query = "SELECT
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Empresa=" . $arrPost['Hidden_ID_Empresa'] . "
AND ID_Organizacion=" . $arrPost['Hidden_ID_Organizacion'] . "
AND ID_Almacen=" . $arrPost['Hidden_ID_Almacen'] . "
AND ID_Tipo_Documento=" . $iTipoDocumento . "
AND Nu_Estado=1
" . $where_serie . "
AND ID_POS=".$this->session->userdata['arrDataPersonal']['arrData'][0]->ID_POS." LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Guía Interna';
		if ( $arrPost['radio-TipoDocumento'] == '7' )
			$sTidoDocumento = 'Guía Física';
		else if ( $arrPost['radio-TipoDocumento'] == '8' )
			$sTidoDocumento = 'Guía Electrónica';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'sMessage' => 'Deben configurar serie para ' . $sTidoDocumento . ', no existe');
		}
		
		if($this->db->query("SELECT COUNT(*) AS existe FROM guia_cabecera AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $arrPost['Hidden_ID_Empresa'] . " AND TMOVI.Nu_Tipo_Movimiento = 1 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $arrPost['Hidden_ID_Entidad'] . " AND GC.ID_Tipo_Documento = " . $iTipoDocumento . " AND GC.ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND GC.ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe guía ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . ' modificar correlativo en la opción Ventas -> Series' );
		}else{
			if (!empty($arrPost['Txt_Direccion_Entidad-modal'])){
				$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['Txt_Direccion_Entidad-modal'] . "' WHERE ID_Entidad = " . $arrPost['Hidden_ID_Entidad'];
				$this->db->query($sql);
			}
			
			if (empty($this->db->query("SELECT Txt_Direccion_Entidad FROM entidad WHERE ID_Entidad=" . $arrPost['Hidden_ID_Entidad'] . " LIMIT 1")->row()->Txt_Direccion_Entidad)) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'No tiene dirección, registrar en Punto de venta > Historial de Venta');
			}

			$Nu_Correlativo = 0;
			$Fe_Year = ToYear($arrPost['Hidden_Fe_Emision']);
			$Fe_Month = ToMonth($arrPost['Hidden_Fe_Emision']);
			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();

			if ( count($arrCorrelativoPendiente) > 0 ){
				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
				
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Tipo_Asiento', 3);
				$this->db->where('Fe_Year', $Fe_Year);
				$this->db->where('Fe_Month', $Fe_Month);
				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
		        $this->db->delete('correlativo_tipo_asiento_pendiente');
			} else {
				if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo = Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
AND ID_Tipo_Asiento=3
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
 " . $this->user->ID_Empresa . ",
 3,
 '" . $Fe_Year . "',
 '" . $Fe_Month . "',
 1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			}

			$Fe_Guia = (!empty($arrPost['Fe_Traslado']) ? ToDate($arrPost['Fe_Traslado']) : dateNow('fecha'));
			
			$iDias = diferenciaFechasMultipleFormato($Fe_Guia, dateNow('fecha') , 'dias' );
			if ( $iDias > 1 && $arrPost['radio-TipoDocumento'] == '8'){// Sobre paso los días límite
				$this->db->trans_rollback();
				return array('sStatus' => 'warning', 'sMessage' => 'La fecha debe de ser máximo 1 día antes');
			}

			$arrHeader = array(
				'ID_Empresa' => $arrPost['Hidden_ID_Empresa'],
				'ID_Organizacion' => $arrPost['Hidden_ID_Organizacion'],
				'ID_Almacen' => $arrPost['Hidden_ID_Almacen'],
				'ID_Tipo_Asiento' => 3,
				'ID_Tipo_Documento' => $iTipoDocumento,
				'ID_Serie_Documento_PK' => $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento' => $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento' => $arrSerieDocumento->Nu_Numero_Documento,
				'Fe_Emision' => $Fe_Guia,
				'Fe_Periodo' => $Fe_Guia,
				'ID_Moneda' => $arrPost['Hidden_ID_Moneda'],
				'Nu_Descargar_Inventario' => 0,
				'ID_Tipo_Movimiento' => 1,
				'ID_Entidad' => $arrPost['Hidden_ID_Entidad'],
				'Ss_Total' => $arrPost['Hidden_Ss_Total'],
				'Nu_Estado' => 6,
				'ID_Almacen_Transferencia' => 0,
				'Ss_Peso_Bruto' => $arrPost['Ss_Peso_Bruto'],
				'Nu_Bulto' => $arrPost['Nu_Bulto'],
				'No_Tipo_Transporte' => $arrPost['radio-TipoTransporte']
			);

			if ( $arrPost['Hidden_ID_Lista_Precio_Cabecera'] != 0 )
				$arrHeader = array_merge($arrHeader, array("ID_Lista_Precio_Cabecera" => $arrPost['Hidden_ID_Lista_Precio_Cabecera']));

			$arrHeader = array_merge($arrHeader, array("Nu_Correlativo" => $Nu_Correlativo));

			$this->db->insert('guia_cabecera', $arrHeader);
			$Last_ID_Guia_Cabecera = $this->db->insert_id();
			
			if ( $arrPost['radio-addFlete'] == '1' ) {
				$arrFlete = array(
					'ID_Empresa' => $arrPost['Hidden_ID_Empresa'],
					'ID_Entidad' => $arrPost['AID_Transportista'],
					'ID_Ubigeo_Inei_Llegada' => $arrPost['ID_Ubigeo_Inei_Llegada'],
					'No_Placa' => strtoupper($arrPost['No_Placa']),
					//'Fe_Traslado' => dateNow('fecha'),
					'Fe_Traslado' => $Fe_Guia,
					'No_Licencia' => $arrPost['No_Licencia'],
					'ID_Motivo_Traslado' => 76,//76=Venta
				);

				$arrFlete = array_merge( $arrFlete, array( 'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera) );
				$this->db->insert('flete', $arrFlete);
			}

			$table_guia_enlace = array(
				'ID_Empresa' => $arrPost['Hidden_ID_Empresa'],
				'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
				'ID_Documento_Cabecera'	=> $arrPost['Hidden_ID_Documento_Cabecera'],
			);
			$this->db->insert('guia_enlace', $table_guia_enlace);

        	$query_detalle = "SELECT
ID_Producto,
Qt_Producto,
Ss_Precio,
Ss_SubTotal,
ID_Impuesto_Cruce_Documento,
Ss_Impuesto,
Ss_Total
FROM
documento_detalle
WHERE
ID_Documento_Cabecera = " . $arrPost['Hidden_ID_Documento_Cabecera'];
			$arrDetalle = $this->db->query($query_detalle)->result();
		
			foreach ($arrDetalle as $row) {
				$arrDetail[] = array(
					'ID_Empresa' => $arrPost['Hidden_ID_Empresa'],
					'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
					'ID_Producto' => $row->ID_Producto,
					'Qt_Producto' => $row->Qt_Producto,
					'Ss_Precio' => $row->Ss_Precio,
					'Ss_SubTotal' => $row->Ss_SubTotal,
					'Ss_Descuento' => '0.00',
					'Ss_Descuento_Impuesto' => '0.00',
					'Po_Descuento' => '0.00',
					'ID_Impuesto_Cruce_Documento' => $row->ID_Impuesto_Cruce_Documento,
					'Ss_Impuesto' => $row->Ss_Impuesto,
					'Ss_Total' => $row->Ss_Total
				);
			}
			$this->db->insert_batch('guia_detalle', $arrDetail);
			
//			$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento = Nu_Numero_Documento + 1 WHERE ID_Serie_Documento_PK=" . $arrSerieDocumento->ID_Serie_Documento_PK);

			$this->db->query("UPDATE documento_cabecera SET ID_Transporte_Delivery = " . $arrPost['AID_Transportista'] . " WHERE ID_Documento_Cabecera=" . $arrPost['Hidden_ID_Documento_Cabecera']);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al generar Guía');
			} else {
				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['radio-TipoDocumento'] != '14') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Documento=" . $iTipoDocumento . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();

					if (substr($arrSerieDocumento->ID_Serie_Documento, 0, 1) == 'T') {
						if ($this->empresa->Nu_Activar_Guia_Electronica==1) {
							$arrParams = array(
								'iCodigoProveedorDocumentoElectronico' => 1,
								'iEstadoVenta' => 6,
								'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
								'sEmailCliente' => '',
								'sTipoRespuesta' => 'php',
							);
							$arrResponseFE = array();
							
							if ( $this->empresa->Nu_Tipo_Proveedor_FE == 1 ) {//Nubefact
								$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuia( $arrParams );
								$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
								if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
									return $arrResponseFEMensaje;
								}
								if ( $arrResponseFE['sStatus'] != 'success' ) {
									return $arrResponseFE;
								}
							} else if ( $this->empresa->Nu_Tipo_Proveedor_FE == 2 ) {//Facturador sunat
								$arrResponseFE = $this->DocumentoElectronicoModel->generarFormatoDocumentoElectronicoGuiaSunat( $arrParams );
								$arrResponseFEMensaje = $this->DocumentoElectronicoModel->agregarMensajeRespuestaProveedorFEGuia( $arrResponseFE, $arrParams );
								if ( $arrResponseFEMensaje['sStatus'] != 'success' ) {
									return $arrResponseFEMensaje;
								}
								if ( $arrResponseFE['sStatus'] != 'success' ) {
									return $arrResponseFE;
								}
							}

							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro enviado',
								'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
								'arrResponseFE' => $arrResponseFE,
							);
						} else {
							return array(
								'sStatus' => 'success',
								'sMessage' => 'Registro guardado pero no tiene activado Guía de Remision Electronica',
								'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
								'arrResponseFE' => '',
							);
						}
					} else {
						return array(
							'sStatus' => 'success',
							'sMessage' => 'Guía generada',
							'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
							'arrResponseFE' => '',
						);
					}
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 1 && $arrPost['radio-TipoDocumento'] == '14') {// cancelado y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Documento=" . $iTipoDocumento . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
	
					$sql = "UPDATE guia_cabecera SET ID_Numero_Documento='" . $arrSerieDocumento->Nu_Numero_Documento . "' WHERE ID_Guia_Cabecera=" . $Last_ID_Guia_Cabecera;
					$this->db->query($sql);
					// fin correlativo

					$this->db->trans_commit();
					
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Guía generada',
						'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
						'arrResponseFE' => '',
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['radio-TipoDocumento'] != '2') {// pago pendiente y diferente 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $arrPost['Hidden_ID_Empresa'] . " AND ID_Tipo_Documento=" . $iTipoDocumento . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					// fin correlativo

					$this->db->trans_commit();

					return array(
						'sStatus' => 'success',
						'sMessage' => 'Guía generada pero no fue enviada a SUNAT por falta de pago, tienen hasta 6 días calendarios para poder regularizarlo de lo contrario se perderán',
						'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
						'arrResponseFE' => '',
					);
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['radio-TipoDocumento'] == '2') {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();
					
					return array(
						'sStatus' => 'danger',
						'sMessage' => 'No se guardo guía por falta de pago',
						'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
						'arrResponseFE' => '',
					);
				}
			}
		}
	}
}
