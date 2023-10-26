<?php
class VentaModel extends CI_Model{
	var $table          				= 'documento_cabecera';
	var $table_empresa              = 'empresa';
	var $table_documento_detalle		= 'documento_detalle';
	var $table_documento_enlace			= 'documento_enlace';
	var $table_documento_detalle_lote	= 'documento_detalle_lote';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda					= 'moneda';
	var $table_medio_pago				= 'medio_pago';
	var $table_organizacion				= 'organizacion';
	var $table_tabla_dato				= 'tabla_dato';
	var $table_estado_documento = 'estado_documento';
	var $table_serie_documento = 'serie_documento';
	var $table_almacen = 'almacen';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
		if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if(!empty($this->input->post('Filtro_SeriesDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SeriesDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));

        if(!empty($this->input->post('Filtro_Estado')))
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if($this->input->post('Filtro_Estado_Pago') == '1')
			$this->db->where('VC.Ss_Total_Saldo > ', '0.00');
			
        if($this->input->post('Filtro_Estado_Pago') == '2')
			$this->db->where('VC.Ss_Total_Saldo = ', '0.00');
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));
        
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
		$this->db->select('VC.ID_Empresa, VC.ID_Organizacion, VC.ID_Almacen, VC.ID_Moneda, VC.ID_Lista_Precio_Cabecera, VC.ID_Documento_Cabecera, EMP.No_Empresa, EMP.Nu_Documento_Identidad, EMP.Nu_Tipo_Proveedor_FE, ORG.Nu_Estado_Sistema, ALMA.No_Almacen, VC.ID_Tipo_Documento, VC.Fe_Emision, TDOCU.Nu_Sunat_Codigo, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, CLI.ID_Entidad AS id_cliente, CLI.No_Entidad, MP.No_Medio_Pago, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR, VC.Txt_Respuesta_Sunat_FE, MP.Nu_Tipo AS Nu_Tipo_Medio_Pago, VC.Ss_Total_Saldo, SD.ID_POS, MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE, SD.Nu_Cantidad_Caracteres, TRC.ID_Referencia, TRC.Nu_Dia, TRC.ID_Tipo_Tiempo_Repetir, TRC.Nu_Month, VC.Ss_Detraccion, CLI.Nu_Documento_Identidad AS Nu_Documento_Identidad_Cliente, VC.Txt_Glosa, VC.Txt_Garantia, VC.No_Orden_Compra_FE, VC.No_Placa_FE, VC.ID_Serie_Documento_PK')
		->from($this->table . ' AS VC')
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = VC.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = VC.ID_Organizacion', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_serie_documento . ' AS SD', 'SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK', 'left')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = VC.ID_Medio_Pago', 'join')
		->join('tarea_repetir_cron AS TRC', 'TRC.ID_Referencia = VC.ID_Documento_Cabecera', 'left')
    	->where('VC.ID_Tipo_Asiento', 1)
    	->where_in('VC.ID_Tipo_Documento', array('2','3','4','5','6'));
		
		if ( $this->user->No_Usuario == 'root' ){
        	if($this->input->post('filtro_empresa') != '0')
				$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
			
        	if($this->input->post('filtro_organizacion') != '0')
				$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
		} else {
            $this->db->where('VC.ID_Empresa', $this->empresa->ID_Empresa);
            $this->db->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion);
        }

        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));
				
        if($this->input->post('filtro_estado_sistema') != '-')
			$this->db->where('ORG.Nu_Estado_Sistema', $this->input->post('filtro_estado_sistema'));
				
        if(!empty($this->input->post('filtro_tipo_sistema')))
			$this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('filtro_tipo_sistema'));
			
        if(isset($_POST['order']))
        	$this->db->order_by( 'VC.ID_Documento_Cabecera DESC' );
        else if(isset($this->order))
        	$this->db->order_by( 'VC.ID_Documento_Cabecera DESC' );
    }
	
	function get_datatables(){
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all(){
		if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if(!empty($this->input->post('Filtro_SeriesDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SeriesDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));

        if(!empty($this->input->post('Filtro_Estado')))
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if($this->input->post('Filtro_Estado_Pago') == '1')
			$this->db->where('VC.Ss_Total_Saldo > ', '0.00');
			
        if($this->input->post('Filtro_Estado_Pago') == '2')
			$this->db->where('VC.Ss_Total_Saldo = ', '0.00');
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));
        
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

		$this->db->select('VC.ID_Empresa, VC.ID_Organizacion, VC.ID_Almacen, VC.ID_Moneda, VC.ID_Lista_Precio_Cabecera, VC.ID_Documento_Cabecera, EMP.No_Empresa, EMP.Nu_Documento_Identidad, EMP.Nu_Tipo_Proveedor_FE, ORG.Nu_Estado_Sistema, ALMA.No_Almacen, VC.ID_Tipo_Documento, VC.Fe_Emision, TDOCU.Nu_Sunat_Codigo, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, CLI.ID_Entidad AS id_cliente, CLI.No_Entidad, MP.No_Medio_Pago, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR, VC.Txt_Respuesta_Sunat_FE, MP.Nu_Tipo AS Nu_Tipo_Medio_Pago, VC.Ss_Total_Saldo, SD.ID_POS, MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE, SD.Nu_Cantidad_Caracteres, TRC.ID_Referencia, TRC.Nu_Dia, TRC.ID_Tipo_Tiempo_Repetir, TRC.Nu_Month, VC.Ss_Detraccion, CLI.Nu_Documento_Identidad AS Nu_Documento_Identidad_Cliente, VC.Txt_Glosa, VC.Txt_Garantia, VC.No_Orden_Compra_FE, VC.No_Placa_FE, VC.ID_Serie_Documento_PK')
		->from($this->table . ' AS VC')
		->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = VC.ID_Empresa', 'join')
		->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = VC.ID_Organizacion', 'join')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_serie_documento . ' AS SD', 'SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK', 'left')		
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_medio_pago . ' AS MP', 'MP.ID_Medio_Pago = VC.ID_Medio_Pago', 'join')
		->join('tarea_repetir_cron AS TRC', 'TRC.ID_Referencia = VC.ID_Documento_Cabecera', 'left')
    	->where('VC.ID_Tipo_Asiento', 1)
		->where_in('VC.ID_Tipo_Documento', array('2','3','4','5','6'));
		
		if ( $this->user->No_Usuario == 'root' ){
        	if($this->input->post('filtro_empresa') != '0')
				$this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
			
        	if($this->input->post('filtro_organizacion') != '0')
				$this->db->where('ORG.ID_Organizacion', $this->input->post('filtro_organizacion'));
		} else {
            $this->db->where('VC.ID_Empresa', $this->empresa->ID_Empresa);
            $this->db->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion);
        }

        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));
				
        if($this->input->post('filtro_estado_sistema') != '-')
			$this->db->where('ORG.Nu_Estado_Sistema', $this->input->post('filtro_estado_sistema'));

        if(!empty($this->input->post('filtro_tipo_sistema')))
			$this->db->where('EMP.Nu_Tipo_Proveedor_FE', $this->input->post('filtro_tipo_sistema'));

        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $query = "SELECT
CONFI.Nu_Logo_Empresa_Ticket,
CONFI.No_Logo_Empresa,
CONFI.Nu_Height_Logo_Ticket,
CONFI.Nu_Width_Logo_Ticket,
CONFI.No_Dominio_Empresa,
CONFI.Nu_Tipo_Rubro_Empresa,
CONFI.Txt_Cuentas_Bancarias,
CONFI.Txt_Nota,
CONFI.Txt_Terminos_Condiciones,
CONFI.No_Imagen_Logo_Empresa,
ALMA.No_Logo_Url_Almacen,
EMP.Nu_MultiAlmacen,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Nu_Tipo_Proveedor_FE,
EMP.Txt_Direccion_Empresa,
ALMA.Txt_Direccion_Almacen,
ALMA.No_Logo_Almacen,
CONFI.Nu_Celular_Empresa,
CONFI.Nu_Telefono_Empresa,
CONFI.Txt_Email_Empresa,
CONFI.Nu_Logo_Empresa_Ticket,
CONFI.Txt_Slogan_Empresa,
CONFI.Txt_Terminos_Condiciones_Ticket,
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Documento_Cabecera,
CLI.ID_Entidad,
TDOCUIDEN.No_Tipo_Documento_Identidad_Breve,
TDOCUIDEN.ID_Tipo_Documento_Identidad,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
CLI.Nu_Celular_Entidad,
CLI.Txt_Email_Entidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
VC.ID_Serie_Documento,
VC.ID_Serie_Documento_PK,
VC.ID_Numero_Documento,
VC.Fe_Emision,
VC.ID_Moneda,
VC.ID_Medio_Pago,
VC.Fe_Vencimiento,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
VD.ID_Producto,
PRO.Nu_Tipo_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
VD.Ss_Precio,
VD.Qt_Producto,
VD.ID_Impuesto_Cruce_Documento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
VD.Ss_Descuento AS Ss_Descuento_Producto,
VD.Ss_Descuento_Impuesto AS Ss_Descuento_Impuesto_Producto,
VD.Po_Descuento AS Po_Descuento_Impuesto_Producto,
VD.Ss_Total AS Ss_Total_Producto,
PRO.ID_Impuesto_Icbper,
ICDOCU.Ss_Impuesto,
VE.ID_Documento_Cabecera_Enlace,
VE.No_Tipo_Documento_Modificar,
VE.ID_Tipo_Documento_Modificar,
VE.ID_Serie_Documento_Modificar,
VE.ID_Serie_Documento_Modificar_PK,
VE.ID_Numero_Documento_Modificar,
TDMOTIVO.No_Descripcion AS No_Descripcion_Motivo_Referencia,
MP.Nu_Tipo,
IMP.Nu_Tipo_Impuesto,
IMP.ID_Impuesto,
VC.Txt_Glosa,
VC.Ss_Descuento,
MONE.No_Signo,
VC.Ss_Total,
VC.Ss_Total_Saldo,
MONE.No_Moneda,
VC.Po_Descuento,
TDOCUIDEN.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_TDI,
UM.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_UM,
UM.No_Unidad_Medida, 
VC.Txt_Url_PDF,
VC.Txt_Url_XML,
VC.Txt_Url_CDR,
VC.Txt_Url_Comprobante,
TDOCU.No_Tipo_Documento,
VC.Nu_Codigo_Motivo_Referencia,
VC.Nu_Detraccion,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio,
ITEMSUNAT.Nu_Valor AS Nu_Codigo_Producto_Sunat,
VC.No_Formato_PDF,
MP.No_Medio_Pago,
MP.Txt_Medio_Pago,
MP.No_Codigo_Sunat_PLE AS No_Codigo_Medio_Pago_Sunat_PLE,
VC.Txt_Garantia,
VC.ID_Mesero,
VC.ID_Comision,
CLI.Nu_Dias_Credito,
SD.Nu_Cantidad_Caracteres,
CLI.Txt_Email_Entidad,
MONE.Nu_Valor_Fe AS Nu_Valor_Fe_Moneda,
IMP.Nu_Valor_Fe AS Nu_Valor_Fe_Impuesto,
VDL.Nu_Lote_Vencimiento,
VDL.Fe_Lote_Vencimiento,
VC.No_Orden_Compra_FE,
VC.No_Placa_FE,
PRO.Ss_Icbper AS Ss_Icbper_Item,
VD.Ss_Icbper,
VC.ID_Canal_Venta_Tabla_Dato,
VC.ID_Sunat_Tipo_Transaction,
STT.Nu_Codigo_Sunat AS Nu_Codigo_Sunat_Tipo_Transaccion,
STT.Nu_Codigo_Pse AS Nu_Codigo_Pse_Tipo_Transaccion,
VC.Nu_Tipo_Recepcion,
VC.Fe_Entrega,
VC.ID_Transporte_Delivery,
VC.Txt_Direccion_Delivery,
VD.Txt_Nota AS Txt_Nota_Item,
VC.Nu_Retencion,
VC.Ss_Retencion,
VC.Ss_Detraccion,
VC.Po_Detraccion,
VC.Ss_Descuento_Impuesto,
PRO.Nu_Activar_Precio_x_Mayor,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
VC.Nu_Expediente_FE,
VC.Nu_Codigo_Unidad_Ejecutora_FE
FROM
" . $this->table . " AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN sunat_tipo_transaction AS STT ON(VC.ID_Sunat_Tipo_Transaction = STT.ID_Sunat_Tipo_Transaction)
JOIN " . $this->table_documento_detalle . " AS VD ON (VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
LEFT JOIN " . $this->table_documento_detalle_lote . " AS VDL ON(VC.ID_Documento_Cabecera = VDL.ID_Documento_Cabecera AND VD.ID_Documento_Detalle = VDL.ID_Documento_Detalle)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN " . $this->table_entidad . " AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
LEFT JOIN " . $this->table_tabla_dato . " AS ITEMSUNAT ON(ITEMSUNAT.ID_Tabla_Dato = PRO.ID_Producto_Sunat)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
LEFT JOIN tabla_dato AS TDMOTIVO ON(TDMOTIVO.Nu_Valor = VC.Nu_Codigo_Motivo_Referencia AND TDMOTIVO.No_Class = VC.ID_Tipo_Documento)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN (
SELECT
VE.ID_Documento_Cabecera,
VE.ID_Documento_Cabecera_Enlace,
TDOCU.No_Tipo_Documento AS No_Tipo_Documento_Modificar,
VC.ID_Tipo_Documento AS ID_Tipo_Documento_Modificar,
VC.ID_Serie_Documento AS ID_Serie_Documento_Modificar,
VC.ID_Serie_Documento_PK AS ID_Serie_Documento_Modificar_PK,
VC.ID_Numero_Documento AS ID_Numero_Documento_Modificar
FROM
" . $this->table . " AS VC
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_documento_enlace . " AS VE ON(VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera_Enlace)
) AS VE ON(VE.ID_Documento_Cabecera = VC.ID_Documento_Cabecera)
WHERE VC.ID_Documento_Cabecera = " . $ID;
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'style_modal' => 'modal-danger',
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar formato documento electrónico',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrData = $arrResponseSQL->result();
			foreach ($arrData as $row) {
				$arrParams['iIdDocumentoCabecera'] = $row->ID_Documento_Cabecera;
				$arrMedioPago = $this->obtenerComprobanteMedioPago($arrParams);
				$row->ID_Tipo_Medio_Pago = $arrMedioPago['arrData'][0]->ID_Tipo_Medio_Pago;
				$row->Nu_Tarjeta = (!empty($arrMedioPago['arrData'][0]->Nu_Tarjeta) ? $arrMedioPago['arrData'][0]->Nu_Tarjeta : '-');
				$row->Nu_Transaccion = (!empty($arrMedioPago['arrData'][0]->Nu_Transaccion) ? $arrMedioPago['arrData'][0]->Nu_Transaccion : '-');
				$rows[] = $row;
				$arrData = $rows;
			}
			return array(
				'status' => 'success',
				'style_modal' => 'modal-success',
				'sStatus' => 'success',
				'arrData' => $arrData,
			);
		}

		return array(
			'status' => 'warning',
			'style_modal' => 'modal-warning',
			'sStatus' => 'warning',
			'sMessage' => 'No hay registro',
		);
    }
    
	public function obtenerComprobanteMedioPago($arrParams){
		$query = "SELECT
VMP.ID_Tipo_Medio_Pago,
VMP.Nu_Tarjeta,
VMP.Nu_Transaccion
FROM
documento_cabecera AS VC
JOIN documento_medio_pago AS VMP ON(VC.ID_Documento_Cabecera = VMP.ID_Documento_Cabecera)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VMP.ID_Medio_Pago)
WHERE
VC.ID_Documento_Cabecera = " . $arrParams['iIdDocumentoCabecera'] . " ORDER BY VMP.ID_Documento_Medio_Pago ASC LIMIT 1";
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al obtener medio(s) de pago',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
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
			'sMessage' => 'No hay registro',
		);
	}

    public function agregarVenta($arrVentaCabecera, $arrVentaDetalle, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrClienteNuevo){
		if ($this->empresa->Nu_Tipo_Proveedor_FE == 3 && $arrVentaCabecera['ID_Tipo_Documento']!='2') {
			return array('status' => 'danger', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Solo puedes emitir NOTA DE VENTA - Plan CONTROL INTERNO');
		}
		
		$this->db->trans_begin();
		
		$query = "SELECT
ID_Tipo_Documento,
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Serie_Documento_PK=" . $arrVentaCabecera['ID_Serie_Documento_PK'] . " LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrVentaCabecera['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrVentaCabecera['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			$this->db->trans_rollback();
			return array('status' => 'danger', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Falta configurar serie para ' . $sTidoDocumento . ', no existe');
		}

		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrSerieDocumento->ID_Tipo_Documento . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'Ya existe venta ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . '. Cambiar correlativo en ventas y clientes > series');
		}else{
			//Tipo de medio de pago
			$iTipoFormaPago = $arrVentaCabecera['iTipoFormaPago'];
			unset($arrVentaCabecera['iTipoFormaPago']);

			//verificacion de fecha al crédito
			$dEmision = new DateTime($arrVentaCabecera['Fe_Emision'] . " 00:00:00");
			$dVencimiento_Comparar = new DateTime($arrVentaCabecera['Fe_Vencimiento'] . " 00:00:00");
			if ($iTipoFormaPago==1 && $dVencimiento_Comparar<=$dEmision) {
				$this->db->trans_rollback();
				return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'La F. Vencimiento debe de ser mayor a la fecha de hoy > ' . ToDateBD($arrVentaCabecera['Fe_Emision']));
			}

			$arrVentaCabecera['ID_Serie_Documento_PK'] = $arrSerieDocumento->ID_Serie_Documento_PK;
			$arrVentaCabecera['ID_Serie_Documento'] = $arrSerieDocumento->ID_Serie_Documento;
			$arrVentaCabecera['ID_Numero_Documento'] = $arrSerieDocumento->Nu_Numero_Documento;

			$iTipoCliente = $arrVentaCabecera['iTipoCliente'];
			unset($arrVentaCabecera['iTipoCliente']);

			$Nu_Correlativo = 0;
			$Fe_Year = ToYear($arrVentaCabecera['Fe_Emision']);
			$Fe_Month = ToMonth($arrVentaCabecera['Fe_Emision']);
			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();
			
			if ( count($arrCorrelativoPendiente) > 0 ){
				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
				
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Tipo_Asiento', 1);
				$this->db->where('Fe_Year', $Fe_Year);
				$this->db->where('Fe_Month', $Fe_Month);
				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
		        $this->db->delete('correlativo_tipo_asiento_pendiente');
			} else {
				if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
					$sql_correlativo_libro_sunat = "UPDATE
correlativo_tipo_asiento
SET
Nu_Correlativo=Nu_Correlativo + 1
WHERE
ID_Empresa=" . $this->user->ID_Empresa . "
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
" . $this->user->ID_Empresa . ",
1,
'" . $Fe_Year . "',
'" . $Fe_Month . "',
1
);";
					$this->db->query($sql_correlativo_libro_sunat);
				}
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			}
			
			if ($iTipoCliente == 0){//0=cliente existente
				$arrClienteBD = $this->db->query("SELECT Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad FROM entidad WHERE ID_Entidad = " . $arrVentaCabecera['ID_Entidad'] . " LIMIT 1")->result();
				
				if ( $arrVentaCabecera['ID_Sunat_Tipo_Transaction'] == 1 && $arrVentaCabecera['ID_Tipo_Documento'] == '3' && $arrClienteBD[0]->ID_Tipo_Documento_Identidad == 1 ) {//1=OTROS
					$this->db->trans_rollback();
					return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'Para ventas con Tipo operación VENTA INTERNA no puedes facturar a T.D.I. OTROS');
				}

				if ( $arrVentaCabecera['ID_Sunat_Tipo_Transaction'] == 1 && $arrVentaCabecera['ID_Tipo_Documento'] == '3' && $arrClienteBD[0]->ID_Tipo_Documento_Identidad == 2 ) {//2=DNI
					$this->db->trans_rollback();
					return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'No se puede facturar a un cliente con DNI');
				}

				if ( $arrVentaCabecera['ID_Sunat_Tipo_Transaction'] == 2 && $arrVentaCabecera['ID_Tipo_Documento'] == '3' && $arrClienteBD[0]->ID_Tipo_Documento_Identidad != 1 ) {
					$this->db->trans_rollback();
					return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'Para ventas con Tipo operación EXPORTACIÓN debe de tener T.D.I. OTROS');
				}

				if ( $arrVentaCabecera['ID_Sunat_Tipo_Transaction'] == 1 && $arrVentaCabecera['ID_Tipo_Documento'] == '3' && strlen($arrClienteBD[0]->Nu_Documento_Identidad) < 10 ) {
					$this->db->trans_rollback();
					return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'Numero de Documento de Identidad debe de tener mínimo 11 caracteres');
				}

				$Nu_Celular_Entidad = '';
				if ( strlen($arrVentaCabecera['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrVentaCabecera['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if ( (!empty($arrVentaCabecera['Txt_Direccion_Entidad']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrVentaCabecera['Txt_Direccion_Entidad']) || (!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) || (!empty($arrVentaCabecera['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrVentaCabecera['Txt_Email_Entidad']) ) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrVentaCabecera['Txt_Direccion_Entidad'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrVentaCabecera['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $arrVentaCabecera['ID_Entidad'];
					$this->db->query($sql);
				}// /. if cambiar celular o correo

			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
			}

			if (is_array($arrClienteNuevo)){
			    unset($arrVentaCabecera['ID_Entidad']);
			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
			    //Si no existe el cliente, lo crearemos
			    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . "' LIMIT 1")->row()->existe == 0){
					$arrCliente = array(
		                'ID_Empresa'					=> $this->user->ID_Empresa,
		                'ID_Organizacion'				=> $arrVentaCabecera['ID_Organizacion'],
		                'Nu_Tipo_Entidad'				=> 0,
		                'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
		                'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
		                'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
		                'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
		                'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
						'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
						'Txt_Email_Entidad' => $arrClienteNuevo['Txt_Email_Entidad'],
		                'Nu_Estado' => 1,
						'ID_Tipo_Cliente_1' => $arrClienteNuevo['ID_Tipo_Cliente_1']
		            );
		    		$this->db->insert('entidad', $arrCliente);
		    		$Last_ID_Entidad = $this->db->insert_id();
			    } else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'El cliente ya se encuentra creado, seleccionar Existente');
				}
	    		$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			if ( $iTipoCliente == 3 ) {//Cliente rápido
			    unset($arrVentaCabecera['ID_Entidad']);
			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
				$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND No_Entidad LIKE '%clientes varios%' LIMIT 1"; //1 = ID_Entidad -> Cliente varios
				if ( !$this->db->simple_query($query) ){
					$this->db->trans_rollback();
					$error = $this->db->error();
					return array(
						'status' => 'danger', 'style_modal' => 'modal-danger', 'message' => 'Problemas al obtener datos de clientes varios',
						'sStatus' => 'danger',
						'sMessage' => 'Problemas al obtener datos de clientes varios',
						'sClassModal' => 'modal-danger',
						'sCodeSQL' => $error['code'],
						'sMessageSQL' => $error['message'],
						'message_nubefact' => '',
					);
				}
				$arrResponseSQL = $this->db->query($query);
				if ( $arrResponseSQL->num_rows() > 0 ){
					$arrData = $arrResponseSQL->result();
					$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Entidad" => $arrData[0]->ID_Entidad));
				} else {
					$this->db->trans_rollback();
					return array(
						'status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No se encontro clientes varios',
						'sStatus' => 'warning',
						'sMessage' => 'No se encontro clientes varios',
						'sClassModal' => 'modal-warning',
						'message_nubefact' => '',
					);
				}
			}
			
			$ID_Tipo_Medio_Pago = $arrVentaCabecera['ID_Tipo_Medio_Pago'];
			$Nu_Transaccion = $arrVentaCabecera['Nu_Transaccion'];
			$Nu_Tarjeta = $arrVentaCabecera['Nu_Tarjeta'];

			unset($arrVentaCabecera['ID_Tipo_Medio_Pago']);
			unset($arrVentaCabecera['Nu_Transaccion']);
			unset($arrVentaCabecera['Nu_Tarjeta']);

			$ID_Guia_Cabecera_Enlace = '';
			if ( $esEnlace == 2 ){//es Guia
				$ID_Guia_Cabecera_Enlace = $arrVentaCabecera['ID_Guia_Cabecera'];
				unset($arrVentaCabecera['ID_Guia_Cabecera']);
			}
			
			$ID_Documento_Cabecera_Enlace = (!empty($ID_Documento_Cabecera_Enlace) ? $ID_Documento_Cabecera_Enlace : $arrVentaCabecera['ID_Documento_Cabecera_Enlace']);
			unset($arrVentaCabecera['ID_Documento_Cabecera_Enlace']);
			$arrVentaCabecera = array_merge($arrVentaCabecera, array("Nu_Correlativo" => $Nu_Correlativo));

			//Verificar total por item y borramos total por cabecera
			$fDescuentoItem = 0;
			foreach ($arrVentaDetalle as $row)
				$fDescuentoItem += (float)$row['fDescuentoSinImpuestosItem'];

			if ($fDescuentoItem > 0)
				$arrVentaCabecera['Ss_Descuento'] = 0.00;

			//solo se va a considerar para NC y ND por el momento
			if( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 && isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado)){
				$query="SELECT SD.ID_POS FROM
				documento_cabecera AS VC
				JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
				WHERE
				ID_Documento_Cabecera = " . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$objRowVentaOrigen = $this->db->query($query)->row();

				if (!empty($objRowVentaOrigen->ID_POS)) {
					$arrVentaCabecera = array_merge($arrVentaCabecera, array(
							"ID_Matricula_Empleado" => $_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
							"Nu_Transporte_Lavanderia_Hoy" => 0
						)
					);
				}
			}

			$this->db->insert($this->table, $arrVentaCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			if ( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 ){
				$table_documento_enlace = array(
					'ID_Empresa'					=> $this->user->ID_Empresa,
					'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
					'ID_Documento_Cabecera_Enlace'	=> $ID_Documento_Cabecera_Enlace,
				);
				$this->db->insert($this->table_documento_enlace, $table_documento_enlace);
				
				$query = "SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$arrDocumentoReferencia = $this->db->query($query)->row();

				if ( $arrDocumentoReferencia->Ss_Total_Saldo >= $arrVentaCabecera['Ss_Total'] ) {
					if ($arrVentaCabecera['ID_Tipo_Documento']==5) {//NC
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);
					}
					
					if ($arrVentaCabecera['ID_Tipo_Documento']==6) {//ND
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);					
					}
				}
			}
			
			if ( $esEnlace == 2 ){//es Guia
				$guia_enlace = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $ID_Guia_Cabecera_Enlace,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $guia_enlace);
			}

			$fGratuitaDetalle=0.00;
			foreach ($arrVentaDetalle as $row) {
				$ID_Producto = $row['ID_Producto'];
				
				if(empty($row['Qt_Producto']) || $row['Qt_Producto'] <= 0.000) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con CANTIDAD en CERO');
				}

				if(empty($row['Ss_Precio']) || $row['Ss_Precio'] <= 0.000) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con PRECIO en CERO');
				}

				if(empty($row['Ss_Total']) || $row['Ss_Total'] <= 0.00) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con TOTAL en CERO');
				}

				if ($this->empresa->Nu_Validar_Stock==1 && $arrVentaCabecera['Nu_Descargar_Inventario']==1 && ($arrVentaCabecera['ID_Tipo_Documento']!=5 && $arrVentaCabecera['ID_Tipo_Documento']!=6)){//Activada la validación de stock, debo de verificar items con stock
					$objItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto, No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();

					if ( $objItem->Nu_Tipo_Producto == 1 ){
						if ( $objItem->Nu_Compuesto == 0 ){
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $row['Qt_Producto'] ){
									$this->db->trans_rollback();
									return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'No tiene stock el producto > ' . $objItem->No_Producto);
							}
						} else {
							$query = "SELECT
							ENLAPRO.ID_Producto,
							ENLAPRO.Qt_Producto_Descargar
							FROM
							enlace_producto AS ENLAPRO
							JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
							WHERE
							ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
							$arrItemsEnlazados = $this->db->query($query)->result();
							
							foreach($arrItemsEnlazados as $row_enlace) {
								$ID_Producto_Enlace = $row_enlace->ID_Producto;
								$fStockVenta = ($row['Qt_Producto'] * $row_enlace->Qt_Producto_Descargar);
								$objItem = $this->db->query("SELECT No_Producto FROM producto WHERE ID_Producto =".$ID_Producto_Enlace." LIMIT 1")->row();
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
										$this->db->trans_rollback();
										return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
									}
								} else {
									$this->db->trans_rollback();
									return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Producto enlazado no tiene stock > ' . $objItem->No_Producto);
								}
							}
						}
					}
				}

				//Obtener gratuita
				$objItemImpuesto = $this->db->query("SELECT ID_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento=".$row['ID_Impuesto_Cruce_Documento']." LIMIT 1")->row();
				$objImpuesto = $this->db->query("SELECT Nu_Tipo_Impuesto FROM impuesto WHERE ID_Impuesto=".$objItemImpuesto->ID_Impuesto." LIMIT 1")->row();
				if($objImpuesto->Nu_Tipo_Impuesto==4) {//4=gratuita
					$fGratuitaDetalle += $row['Ss_Total'];
				}

				$documento_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $ID_Producto,
					'Qt_Producto' => $row['Qt_Producto'],
					'Txt_Nota' => '',
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_SubTotal']) : round($this->security->xss_clean($row['Ss_Total']), 2)),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_Impuesto']) : 0.00),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => $row['fIcbperItem'],
					'Txt_Nota' => $row['Txt_Nota'],
					'Fe_Emision' => $arrVentaCabecera['Fe_Emision']
				);
			}
			$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();

			foreach ($arrVentaDetalle as $row) {
				if (!empty($row['Nu_Lote_Vencimiento']) && !empty($row['Fe_Lote_Vencimiento'])) {
					$documento_detalle_lote[] = array(
						'ID_Empresa' => $this->user->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => (isset($arrVentaCabecera['ID_Almacen']) ? $arrVentaCabecera['ID_Almacen'] : ''),
						'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Documento_Detalle'	=> $iIdDocumentoDetalleFirst,
						'Nu_Lote_Vencimiento' => $this->security->xss_clean($row['Nu_Lote_Vencimiento']),
						'Fe_Lote_Vencimiento' => ToDate($this->security->xss_clean($row['Fe_Lote_Vencimiento'])),
					);
					++$iIdDocumentoDetalleFirst;
				}
			}
			if ( isset($documento_detalle_lote) )
				$this->db->insert_batch($this->table_documento_detalle_lote, $documento_detalle_lote);

			$documento_medio_pago = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
				'ID_Medio_Pago'	=> $this->security->xss_clean($arrVentaCabecera['ID_Medio_Pago']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($ID_Tipo_Medio_Pago),
				'Nu_Transaccion' => $this->security->xss_clean($Nu_Transaccion),
				'Nu_Tarjeta' => $this->security->xss_clean($Nu_Tarjeta),
				'Ss_Total' =>($arrVentaCabecera['Ss_Total'] - $fGratuitaDetalle),
				'Fe_Emision_Hora_Pago' => $arrVentaCabecera['Fe_Emision'] . ' ' . dateNow('hora'),
			);
			$this->db->insert('documento_medio_pago', $documento_medio_pago);
			
			if ($iTipoFormaPago==1) {//1=credito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo='" . ($arrVentaCabecera['Ss_Total_Saldo'] - $fGratuitaDetalle) . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}

			// URL para enviar correo y para consultar por fuera sin session
			// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
			if($arrVentaCabecera['ID_Tipo_Documento']==2) {//2=Nota de venta
				$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/' . $Last_ID_Documento_Cabecera;
				$sql = "UPDATE documento_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}

	        if ($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
				return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
				if ( $this->empresa->Nu_Estado_Pago_Sistema == 1) {// pago pendiente y 2 = Documento interno
					//correlativo
					$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrVentaCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
					//fin correlativo

					$this->db->trans_commit();

					/* TOUR GESTION */
					$where_tour = array('ID_Empresa' => $this->empresa->ID_Empresa, 'Nu_ID_Interno' => 6);
					//Cambiar estado a completado para el tour
					$data_tour = array('Nu_Estado_Proceso' => 1);
					$this->db->update('tour_gestion', $data_tour, $where_tour);
					/* END TOUR GESTION */

					return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera, 'sEnviarSunatAutomatic' => 'No');
				} else if ( $this->empresa->Nu_Estado_Pago_Sistema == 0) {// pago pendiente y 2 = Documento interno
					$this->db->trans_rollback();
					
					return array(
						'status' => 'error',
						'style_modal' => 'modal-danger',
						'message' => 'No se guardo venta por falta de pago',
					);
				}
	        }
		}
    }
    
    public function actualizarVenta($where, $arrVentaCabecera, $arrVentaDetalle, $esEnlace, $ID_Documento_Cabecera_Enlace, $arrClienteNuevo){
		$this->db->trans_begin();

		//Tipo de medio de pago
		$iTipoFormaPago = $arrVentaCabecera['iTipoFormaPago'];
		unset($arrVentaCabecera['iTipoFormaPago']);

		//verificacion de fecha al crédito
		$dEmision = new DateTime($arrVentaCabecera['Fe_Emision'] . " 00:00:00");
		$dVencimiento_Comparar = new DateTime($arrVentaCabecera['Fe_Vencimiento'] . " 00:00:00");
		if ($iTipoFormaPago==1 && $dVencimiento_Comparar<=$dEmision) {
			$this->db->trans_rollback();
			return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'La F. Vencimiento debe de ser mayor a la fecha de hoy > ' . ToDateBD($arrVentaCabecera['Fe_Emision']));
		}

		$iTipoCliente = $arrVentaCabecera['iTipoCliente'];
		unset($arrVentaCabecera['iTipoCliente']);

		$arrDataModificar = $this->db->query("SELECT ID_Empresa, ID_Organizacion, ID_Almacen, ID_Documento_Cabecera, ID_Tipo_Documento, ID_Serie_Documento_PK, ID_Numero_Documento, Nu_Correlativo, Nu_Descargar_Inventario FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $where['ID_Documento_Cabecera'] . " LIMIT 1")->result();
		
		$ID_Documento_Cabecera = $arrDataModificar[0]->ID_Documento_Cabecera;
		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;

		//Verificar si la venta viene por factura de venta o punto de venta, eso lo sabemos por la tabla serie_documento el campo ID_POS si tiene valor es de punto de venta y si no factura de venta
		if ($this->db->query("SELECT ID_POS FROM serie_documento WHERE ID_Serie_Documento_PK = " . $arrDataModificar[0]->ID_Serie_Documento_PK . " LIMIT 1")->row()->ID_POS > 0) {
			//validar que no cambie de serie
			if($arrVentaCabecera['ID_Serie_Documento_PK'] != $arrDataModificar[0]->ID_Serie_Documento_PK) {
				$this->db->trans_rollback();
				return array('status' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'message' => 'No puedes cambiar de serie');
			} else {
				//Ejecutamos otro proceso por que para punto de venta guarda otros valores
				$this->VentaModel->actualizarDetalleVentaPOS($where, $arrVentaCabecera, $arrVentaDetalle, $ID_Documento_Cabecera);
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado 2', 'Last_ID_Documento_Cabecera' => $ID_Documento_Cabecera, 'sEnviarSunatAutomatic' => 'No');
			}
		}
		
		$objAlmacen = $this->db->query("SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Almacen=" . $ID_Almacen . " LIMIT 1")->row();
		if ($ID_Almacen != $this->session->userdata['almacen']->ID_Almacen ) {
			$this->db->trans_rollback();
			return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message' => 'Para modificar debes seleccionar Almacén: ' . $objAlmacen->No_Almacen);
		}

		$ID_Tipo_Documento = $arrDataModificar[0]->ID_Tipo_Documento;
		$ID_Numero_Documento = $arrDataModificar[0]->ID_Numero_Documento;
		$Nu_Correlativo = $arrDataModificar[0]->Nu_Correlativo;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;
		
		$query = "SELECT
ID_Tipo_Documento,
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Serie_Documento_PK=" . $arrVentaCabecera['ID_Serie_Documento_PK'];
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Nota de Venta';
		if ( $arrVentaCabecera['ID_Tipo_Documento'] == '4' )
			$sTidoDocumento = 'Boleta';
		else if ( $arrVentaCabecera['ID_Tipo_Documento'] == '3' )
			$sTidoDocumento = 'Factura';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			$this->db->trans_rollback();
			return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message' => 'Deben configurar serie para ' . $sTidoDocumento . ', no existe');
		}

		$arrVentaCabecera['ID_Numero_Documento'] = $ID_Numero_Documento;
		if ($ID_Tipo_Documento != $arrVentaCabecera['ID_Tipo_Documento']) {
			$arrVentaCabecera['ID_Numero_Documento'] = $arrSerieDocumento->Nu_Numero_Documento;
			//$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento = Nu_Numero_Documento + 1 WHERE ID_Serie_Documento_PK=" . $arrVentaCabecera['ID_Serie_Documento_PK']);
			$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $arrDataModificar[0]->ID_Empresa . " AND ID_Tipo_Documento=" . $arrVentaCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");
		}
		
		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1")->row()->existe > 0){
			$objGuiaEnlace = $this->db->query("SELECT * FROM guia_enlace WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1")->row();
			$esEnlace = 2;
			$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
	        $this->db->delete('guia_enlace');
		}

		$this->db->delete($this->table_documento_detalle, $where);
        $this->db->delete($this->table_documento_detalle_lote, $where);
		$this->db->delete('documento_medio_pago', $where);
		
		$ID_Documento_Cabecera_Enlace = (!empty($ID_Documento_Cabecera_Enlace) ? $ID_Documento_Cabecera_Enlace : $arrVentaCabecera['ID_Documento_Cabecera_Enlace']);
		unset($arrVentaCabecera['ID_Documento_Cabecera_Enlace']);
		if ( (!empty($ID_Documento_Cabecera_Enlace)) && $esEnlace == 1 ){
			$this->db->delete($this->table_documento_enlace, $where);
		}
		
    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
				$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
				
				if ($ID_Tipo_Documento != 5){//Nota de Crédito
					$stock_producto = array( 'Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
				} else {
					$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
				}
        	}
			$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
	        $this->db->delete('movimiento_inventario');
		}

        $this->db->delete($this->table, $where);
	       

		if( $ID_Tipo_Documento != $arrVentaCabecera['ID_Tipo_Documento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 1 AND ID_Tipo_Documento = " . $arrSerieDocumento->ID_Tipo_Documento . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrVentaCabecera['ID_Numero_Documento'] . "' LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			$arrVentaCabecera['ID_Serie_Documento_PK'] = $arrSerieDocumento->ID_Serie_Documento_PK;
			$arrVentaCabecera['ID_Serie_Documento'] = $arrSerieDocumento->ID_Serie_Documento;

			if ($iTipoCliente == 0){//0=cliente existente
				$arrClienteBD = $this->db->query("SELECT Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $arrVentaCabecera['ID_Entidad'] . " LIMIT 1")->result();
				$Nu_Celular_Entidad = '';
				if ( strlen($arrVentaCabecera['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrVentaCabecera['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if ( (!empty($arrVentaCabecera['Txt_Direccion_Entidad']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrVentaCabecera['Txt_Direccion_Entidad']) || (!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) || (!empty($arrVentaCabecera['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrVentaCabecera['Txt_Email_Entidad']) ) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrVentaCabecera['Txt_Direccion_Entidad'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrVentaCabecera['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $arrVentaCabecera['ID_Entidad'];
					$this->db->query($sql);
				}// /. if cambiar celular o correo

			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
			}

			if (is_array($arrClienteNuevo)){
				unset($arrVentaCabecera['ID_Entidad']);
			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
				//Si no existe el cliente, lo crearemos
				if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . "' LIMIT 1")->row()->existe == 0){
					$arrCliente = array(
						'ID_Empresa'					=> $this->user->ID_Empresa,
						'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
						'Nu_Tipo_Entidad'				=> 0,
						'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
						'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
						'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
						'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
						'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
						'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
						'Txt_Email_Entidad' => $arrClienteNuevo['Txt_Email_Entidad'],
						'Nu_Estado' => 1,
						'ID_Tipo_Cliente_1' => $arrClienteNuevo['ID_Tipo_Cliente_1']
					);
					$this->db->insert('entidad', $arrCliente);
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El cliente ya se encuentra creado, seleccionar Existente');
				}
				$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
				
			if ( $iTipoCliente == 3 ) {
				unset($arrVentaCabecera['ID_Entidad']);
			    unset($arrVentaCabecera['Txt_Email_Entidad']);
			    unset($arrVentaCabecera['Nu_Celular_Entidad']);
			    unset($arrVentaCabecera['Txt_Direccion_Entidad']);
				$query = "SELECT ID_Entidad FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND No_Entidad LIKE '%clientes varios%' LIMIT 1"; //1 = ID_Entidad -> Cliente varios
				if ( !$this->db->simple_query($query) ){
					$this->db->trans_rollback();
					$error = $this->db->error();
					return array(
						'status' => 'danger', 'style_modal' => 'modal-danger', 'message' => 'Problemas al obtener datos de clientes varios',
						'sStatus' => 'danger',
						'sMessage' => 'Problemas al obtener datos de clientes varios',
						'sClassModal' => 'modal-danger',
						'sCodeSQL' => $error['code'],
						'sMessageSQL' => $error['message'],
					);
				}
				$arrResponseSQL = $this->db->query($query);
				if ( $arrResponseSQL->num_rows() > 0 ){
					$arrData = $arrResponseSQL->result();
					$arrVentaCabecera = array_merge($arrVentaCabecera, array("ID_Entidad" => $arrData[0]->ID_Entidad));
				} else {
					$this->db->trans_rollback();
					return array(
						'status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'No se encontro clientes varios',
						'sStatus' => 'warning',
						'sMessage' => 'No se encontro clientes varios',
						'sClassModal' => 'modal-warning',
					);
				}
			}

			$arrVentaCabecera['ID_Almacen'] = $ID_Almacen;
			$arrVentaCabecera['ID_Documento_Cabecera'] = $ID_Documento_Cabecera;
			$ID_Tipo_Medio_Pago = $arrVentaCabecera['ID_Tipo_Medio_Pago'];
			$Nu_Transaccion = $arrVentaCabecera['Nu_Transaccion'];
			$Nu_Tarjeta = $arrVentaCabecera['Nu_Tarjeta'];

			unset($arrVentaCabecera['ID_Tipo_Medio_Pago']);
			unset($arrVentaCabecera['Nu_Transaccion']);
			unset($arrVentaCabecera['Nu_Tarjeta']);

			//solo se va a considerar para NC y ND por el momento
			if( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 && isset($_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado)){
				$query="SELECT SD.ID_POS FROM
				documento_cabecera AS VC
				JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
				WHERE
				ID_Documento_Cabecera = " . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$objRowVentaOrigen = $this->db->query($query)->row();

				if (!empty($objRowVentaOrigen->ID_POS)) {
					$arrVentaCabecera = array_merge($arrVentaCabecera, array(
							"ID_Matricula_Empleado" => $_SESSION['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado,
							"Nu_Transporte_Lavanderia_Hoy" => 0
						)
					);
				}
			}

			$arrVentaCabecera = array_merge($arrVentaCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrVentaCabecera);
			$Last_ID_Documento_Cabecera = $this->db->insert_id();
			
			if ( !empty($ID_Documento_Cabecera_Enlace) && $esEnlace == 1 ){
				$table_documento_enlace = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Documento_Cabecera_Enlace' => $ID_Documento_Cabecera_Enlace,
				);
				$this->db->insert($this->table_documento_enlace, $table_documento_enlace);
				
				$query = "SELECT Ss_Total_Saldo FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace . " LIMIT 1";
				$arrDocumentoReferencia = $this->db->query($query)->row();

				if ( $arrDocumentoReferencia->Ss_Total_Saldo >= $arrVentaCabecera['Ss_Total'] ) {
					if ($arrVentaCabecera['ID_Tipo_Documento']==5) {//NC
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);
					}
					
					if ($arrVentaCabecera['ID_Tipo_Documento']==6) {//ND
						$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera_Enlace;
						$this->db->query($sql);					
					}
				}
			}
			
			if ( $esEnlace == 2 ){//es Guia
				$guia_enlace = array(
					'ID_Empresa' => $objGuiaEnlace->ID_Empresa,
					'ID_Guia_Cabecera' => $objGuiaEnlace->ID_Guia_Cabecera,
					'ID_Documento_Cabecera' => $ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $guia_enlace);
			}

			$fGratuitaDetalle=0.00;
			foreach ($arrVentaDetalle as $row) {
				$ID_Producto = $row['ID_Producto'];
				if ($this->empresa->Nu_Validar_Stock==1 && $arrVentaCabecera['Nu_Descargar_Inventario']==1 && ($arrVentaCabecera['ID_Tipo_Documento']!=5 && $arrVentaCabecera['ID_Tipo_Documento']!=6)){//Activada la validación de stock, debo de verificar items con stock
					$objItem = $this->db->query("SELECT Nu_Compuesto, Nu_Tipo_Producto, No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();

					if ( $objItem->Nu_Tipo_Producto == 1 ){
						if ( $objItem->Nu_Compuesto == 0 ){
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $row['Qt_Producto'] ){
									$this->db->trans_rollback();
									return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'No tiene stock el producto > ' . $objItem->No_Producto);
							}
						} else {
							$query = "SELECT
							ENLAPRO.ID_Producto,
							ENLAPRO.Qt_Producto_Descargar
							FROM
							enlace_producto AS ENLAPRO
							JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
							WHERE
							ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
							$arrItemsEnlazados = $this->db->query($query)->result();

							foreach($arrItemsEnlazados as $row_enlace) {
								$ID_Producto_Enlace = $row_enlace->ID_Producto;
								$fStockVenta = ($row['Qt_Producto'] * $row_enlace->Qt_Producto_Descargar);
								$objItem = $this->db->query("SELECT No_Producto FROM producto WHERE ID_Producto =".$ID_Producto_Enlace." LIMIT 1")->row();
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
										$this->db->trans_rollback();
										return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
									}
								} else {
									$this->db->trans_rollback();
									return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Producto enlazado no tiene > ' . $objItem->No_Producto);
								}
							}
						}
					}
				}
				
				if(empty($row['Qt_Producto']) || $row['Qt_Producto'] <= 0.000) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con CANTIDAD en CERO');
				}

				if(empty($row['Ss_Precio']) || $row['Ss_Precio'] <= 0.000) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con PRECIO en CERO');
				}

				if(empty($row['Ss_Total']) || $row['Ss_Total'] <= 0.00) {
					$this->db->trans_rollback();
					return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con TOTAL en CERO');
				}

				//Obtener gratuita
				$objItemImpuesto = $this->db->query("SELECT ID_Impuesto FROM impuesto_cruce_documento WHERE ID_Impuesto_Cruce_Documento=".$row['ID_Impuesto_Cruce_Documento']." LIMIT 1")->row();
				$objImpuesto = $this->db->query("SELECT Nu_Tipo_Impuesto FROM impuesto WHERE ID_Impuesto=".$objItemImpuesto->ID_Impuesto." LIMIT 1")->row();
				if($objImpuesto->Nu_Tipo_Impuesto==4) {//4=gratuita
					$fGratuitaDetalle += $row['Ss_Total'];
				}

				$documento_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera,
					'ID_Producto' => $ID_Producto,
					'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),					
					'Txt_Nota' => '',
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_SubTotal']) : round($this->security->xss_clean($row['Ss_Total']), 2)),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_Impuesto']) : 0.00),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
					'Nu_Estado_Lavado' => 0,
					'Ss_Icbper' => $row['fIcbperItem'],
					'Txt_Nota' => $row['Txt_Nota'],
					'Fe_Emision' => $arrVentaCabecera['Fe_Emision']
				);
			}
			$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			$iIdDocumentoDetalleFirst = $this->db->insert_id();

			foreach ($arrVentaDetalle as $row) {
				if (!empty($row['Nu_Lote_Vencimiento']) && !empty($row['Fe_Lote_Vencimiento'])) {
					$documento_detalle_lote[] = array(
						'ID_Empresa' => $this->user->ID_Empresa,
						'ID_Organizacion' => $this->empresa->ID_Organizacion,
						'ID_Almacen' => $arrVentaCabecera['ID_Almacen'],
						'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
						'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
						'ID_Documento_Detalle'	=> $iIdDocumentoDetalleFirst,
						'Nu_Lote_Vencimiento' => $this->security->xss_clean($row['Nu_Lote_Vencimiento']),
						'Fe_Lote_Vencimiento' => ToDate($this->security->xss_clean($row['Fe_Lote_Vencimiento'])),
					);
					++$iIdDocumentoDetalleFirst;
				}
			}
			if ( isset($documento_detalle_lote) )
				$this->db->insert_batch($this->table_documento_detalle_lote, $documento_detalle_lote);
			
			$documento_medio_pago = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'ID_Documento_Cabecera'	=> $Last_ID_Documento_Cabecera,
				'ID_Medio_Pago'	=> $this->security->xss_clean($arrVentaCabecera['ID_Medio_Pago']),
				'ID_Tipo_Medio_Pago' => $this->security->xss_clean($ID_Tipo_Medio_Pago),
				'Nu_Transaccion' => $this->security->xss_clean($Nu_Transaccion),
				'Nu_Tarjeta' => $this->security->xss_clean($Nu_Tarjeta),
				'Ss_Total' =>($arrVentaCabecera['Ss_Total'] - $fGratuitaDetalle),
				'Fe_Emision_Hora_Pago' => $arrVentaCabecera['Fe_Emision'] . ' ' . dateNow('hora'),
			);
			$this->db->insert('documento_medio_pago', $documento_medio_pago);

			if ($iTipoFormaPago==1) {//1=credito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo='" . ($arrVentaCabecera['Ss_Total_Saldo'] - $fGratuitaDetalle) . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}
			
			// URL para enviar correo y para consultar por fuera sin session
			// base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/'
			if($arrVentaCabecera['ID_Tipo_Documento']==2) {//2=Nota de venta
				$sUrlPDFNotaVentaInternoLae = base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/' . $Last_ID_Documento_Cabecera;
				$sql = "UPDATE documento_cabecera SET Txt_Url_PDF='" . $sUrlPDFNotaVentaInternoLae . "' WHERE ID_Documento_Cabecera=" . $Last_ID_Documento_Cabecera;
				$this->db->query($sql);
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
			} else {
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera, 'sEnviarSunatAutomatic' => 'No');
			}
		}
    }
    
	//modificar punto de venta detalle
	public function actualizarDetalleVentaPOS($where, $arrVentaCabecera, $arrVentaDetalle, $ID_Documento_Cabecera){
		//eliminar tabla detalle
		$this->db->delete($this->table_documento_detalle, $where);

		//eliminar tabla movimiento de inventario
		$query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera;
		$arrDetalle = $this->db->query($query)->result();
		foreach ($arrDetalle as $row) {
			$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
			$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
			
			if ($ID_Tipo_Documento != 5){//Nota de Crédito
				$stock_producto = array( 'Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
				$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
			} else {
				$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
				$this->db->update('stock_producto', $stock_producto, $where_stock_producto);
			}
		}
		$this->db->where('ID_Documento_Cabecera', $ID_Documento_Cabecera);
		$this->db->delete('movimiento_inventario');

		//obtener fecha de emision
		$objDocumentoCabecera = $this->db->query("SELECT Fe_Emision FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $ID_Documento_Cabecera . " LIMIT 1")->row();

		//generar insert a la tabla detalle
		foreach ($arrVentaDetalle as $row) {
			$ID_Producto = $row['ID_Producto'];
			if ($this->empresa->Nu_Validar_Stock==1 && $arrVentaCabecera['Nu_Descargar_Inventario']==1 && ($arrVentaCabecera['ID_Tipo_Documento']!=5 && $arrVentaCabecera['ID_Tipo_Documento']!=6)){//Activada la validación de stock, debo de verificar items con stock
				$objItem = $this->db->query("SELECT Nu_Compuesto, Nu_Tipo_Producto, No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();

				if ( $objItem->Nu_Tipo_Producto == 1 ){
					if ( $objItem->Nu_Compuesto == 0 ){
						$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
						if(is_object($objStockItemAlmacen)){
							if ( $objStockItemAlmacen->Qt_Producto < $row['Qt_Producto'] ){
								$this->db->trans_rollback();
								return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
							}
						} else {
							$this->db->trans_rollback();
							return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'No tiene stock el producto > ' . $objItem->No_Producto);
						}
					} else {
						$query = "SELECT
						ENLAPRO.ID_Producto,
						ENLAPRO.Qt_Producto_Descargar
						FROM
						enlace_producto AS ENLAPRO
						JOIN producto AS PROD ON(PROD.ID_Producto = ENLAPRO.ID_Producto)
						WHERE
						ENLAPRO.ID_Producto_Enlace = " . $ID_Producto;
						$arrItemsEnlazados = $this->db->query($query)->result();
						
						foreach ($arrItemsEnlazados as $row_enlace) {
							$ID_Producto = $row_enlace->ID_Producto;
							$fStockVenta = ($row['Qt_Producto'] * $row_enlace->Qt_Producto_Descargar);
							$objItem = $this->db->query("SELECT No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrVentaCabecera['ID_Almacen'] . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
									$this->db->trans_rollback();
									return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('status' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'message' => 'Producto enlazado no tiene > ' . $objItem->No_Producto);
							}
						}
					}
				}
			}
			
			if(empty($row['Qt_Producto']) || $row['Qt_Producto'] <= 0.000) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con CANTIDAD en CERO');
			}

			if(empty($row['Ss_Precio']) || $row['Ss_Precio'] <= 0.000) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con PRECIO en CERO');
			}

			if(empty($row['Ss_Total']) || $row['Ss_Total'] <= 0.00) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'message_nubefact' => '', 'style_modal' => 'modal-danger', 'message' => 'Hay item con TOTAL en CERO');
			}

			$documento_detalle[] = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'ID_Documento_Cabecera' => $ID_Documento_Cabecera,
				'ID_Producto' => $ID_Producto,
				'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),					
				'Txt_Nota' => '',
				'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
				'Ss_SubTotal' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_SubTotal']) : round($this->security->xss_clean($row['Ss_Total']), 2)),
				'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
				'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
				'Po_Descuento' => $row['Ss_Descuento'],
				'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
				'Ss_Impuesto' => ($arrVentaCabecera['ID_Tipo_Documento'] != 2 ? $this->security->xss_clean($row['Ss_Impuesto']) : 0.00),
				'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
				'Nu_Estado_Lavado' => 0,
				'Ss_Icbper' => $row['fIcbperItem'],
				'Txt_Nota' => $row['Txt_Nota'],
				'Fe_Emision' => $objDocumentoCabecera->Fe_Emision
			);
		}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
		
		//actualizar tabla cabecera y medio de pago
		$sql = "UPDATE documento_cabecera SET Ss_Total=" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera;
		$this->db->query($sql);
		
		$sql = "UPDATE documento_medio_pago SET Ss_Total=" . $arrVentaCabecera['Ss_Total'] . " WHERE ID_Documento_Cabecera=" . $ID_Documento_Cabecera;
		$this->db->query($sql);		
	}

	public function anularVenta($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle_lote);
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle);
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete('documento_medio_pago');
        
    	$ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;
    		
		$query = "SELECT ID_Documento_Cabecera_Enlace FROM documento_enlace WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
		$arrMedioPagoReferencia = $this->db->query($query)->row();
		if ( is_object($arrMedioPagoReferencia) ) {		
			$query = "SELECT Ss_Total FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
			$arrDocumentoReferencia = $this->db->query($query)->row();
			
			if ($ID_Tipo_Documento==5){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}

			if ($ID_Tipo_Documento==6){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}
		}

		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_enlace);

    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera=".$ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				//if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto)->row()->Qt_Producto;
					
	        		if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		} else {
						$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		}

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				//}			
        	}
			$this->db->where('ID_Documento_Cabecera', $ID);
			$data = array(
				'Qt_Producto' => 0,
				'Ss_Precio' => 0,
				'Ss_SubTotal' => 0,
				'Ss_Costo_Promedio' => 0,
			);
	        $this->db->update('movimiento_inventario', $data);
    	}

		if ($this->db->query("SELECT count(*) AS existe FROM guia_enlace WHERE ID_Empresa=" . $this->user->ID_Empresa . " AND ID_Documento_Cabecera=" . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}

		$this->db->where('ID_Documento_Cabecera', $ID);
		$data = array(
			'Nu_Estado' => 7,
			'Ss_Descuento' => 0.00,
			'Ss_Descuento_Impuesto' => 0.00,
			'Ss_Total' => 0.00,
			'Ss_Total_Saldo' => 0.00,
		);
        $this->db->update($this->table, $data);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al anular');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro anulado');
        }
	}
    
	public function eliminarVenta($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle_lote);

		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_detalle);
		
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete('documento_medio_pago');

        $ID_Tipo_Documento = $this->db->query("SELECT ID_Tipo_Documento FROM documento_cabecera WHERE ID_Empresa=".$this->user->ID_Empresa." AND ID_Documento_Cabecera=".$ID." LIMIT 1")->row()->ID_Tipo_Documento;

		$query = "SELECT ID_Documento_Cabecera_Enlace FROM documento_enlace WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
		$arrMedioPagoReferencia = $this->db->query($query)->row();
		if ( is_object($arrMedioPagoReferencia) ) {			
			$query = "SELECT Ss_Total FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $ID . " LIMIT 1";
			$arrDocumentoReferencia = $this->db->query($query)->row();

			if ($ID_Tipo_Documento==5){//Nota de Crédito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo+" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}

			if ($ID_Tipo_Documento==6){//Nota de Débito
				$sql = "UPDATE documento_cabecera SET Ss_Total_Saldo=Ss_Total_Saldo-" . $arrDocumentoReferencia->Ss_Total . " WHERE ID_Documento_Cabecera=" . $arrMedioPagoReferencia->ID_Documento_Cabecera_Enlace;
				$this->db->query($sql);
			}
		}

		$this->db->where('ID_Documento_Cabecera', $ID);
		$this->db->delete($this->table_documento_enlace);

    	if ($Nu_Descargar_Inventario == 1) {    		
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Documento_Cabecera=".$ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				//if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen=" . $row->ID_Almacen . " AND ID_Producto=" . $row->ID_Producto)->row()->Qt_Producto;
					
	        		if ($ID_Tipo_Documento != 5){//Nota de Crédito
						$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		} else {
						$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
						$this->db->update('stock_producto', $stock_producto, $where);
	        		}
				//}

				//actualizar costo promedio
				$arrParamsCostoPromedioStock = array(
					'ID_Almacen' => $row->ID_Almacen,
					'ID_Producto' => $row->ID_Producto
				);
				$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
        	}
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('movimiento_inventario');
    	}
        
        $arrCorrelativoPendiente = $this->db->query("SELECT ID_Organizacion, Fe_Emision, Nu_Correlativo FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Documento_Cabecera = " . $ID . " LIMIT 1")->result();
		
		$sql_limipiar_correlativo = "DELETE FROM correlativo_tipo_asiento_pendiente
WHERE
ID_Empresa = " . $this->user->ID_Empresa . "
AND ID_Tipo_Asiento = 1
AND Fe_Year = '" . ToYear($arrCorrelativoPendiente[0]->Fe_Emision) . "'
AND Fe_Month = '" . ToMonth($arrCorrelativoPendiente[0]->Fe_Emision) . "'
AND Nu_Correlativo = " . $arrCorrelativoPendiente[0]->Nu_Correlativo;
		$this->db->query($sql_limipiar_correlativo);

        $sql_correlativo_pendiente_libro_sunat = "INSERT INTO correlativo_tipo_asiento_pendiente (
ID_Empresa,
ID_Tipo_Asiento,
Fe_Year,
Fe_Month,
Nu_Correlativo
) VALUES (
" . $this->user->ID_Empresa . ",
1,
'" . ToYear($arrCorrelativoPendiente[0]->Fe_Emision) . "',
'" . ToMonth($arrCorrelativoPendiente[0]->Fe_Emision) . "',
" . $arrCorrelativoPendiente[0]->Nu_Correlativo . "
);";
		$this->db->query($sql_correlativo_pendiente_libro_sunat);

		if ($this->db->query("SELECT count(*) AS existe FROM guia_enlace WHERE ID_Empresa=" . $this->user->ID_Empresa . " AND ID_Documento_Cabecera=" . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Documento_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}
  
		$this->db->where('ID_Documento_Cabecera', $ID);
        $this->db->delete($this->table);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
	}
	
	public function get_by_id_anulado($ID){
		$arrData = $this->db->query("SELECT
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
VC.ID_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision
FROM
documento_cabecera AS VC
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
WHERE
VC.ID_Documento_Cabecera = " . $ID . " LIMIT 1")->result();
		return $arrData;
	}
    
	public function changeStatusSunat($ID, $iTipoStatus, $arrParametrosNubefact){
		$this->db->trans_begin();
		if ( $iTipoStatus == 8 || $iTipoStatus == 10 ) {
			if ( count($arrParametrosNubefact) > 0 ) {
				$data = array(
					'Nu_Estado' => $iTipoStatus,
					'Txt_Url_PDF' => $arrParametrosNubefact['url_pdf'],
					'Txt_Url_XML' => $arrParametrosNubefact['url_xml'],
					'Txt_Url_CDR' => $arrParametrosNubefact['url_cdr'],
					'Txt_Url_Comprobante' => $arrParametrosNubefact['url_nubefact'],
					'Txt_QR' => $arrParametrosNubefact['Txt_QR'],
					'Txt_Hash' => $arrParametrosNubefact['Txt_Hash'],
				);
			} else {
				$data = array(
					'Nu_Estado' => ($iTipoStatus == 8 ? 9 : 11),
				);
			}
		} else {
			$data = array(
				'Nu_Estado' => $iTipoStatus
			);
		}

		$this->db->update($this->table, $data, array('ID_Documento_Cabecera' => $ID));
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No se envió a SUNAT', 'arrMessagePSE' => 'No se envió a SUNAT',);
		} else {
			$this->db->trans_commit();
			return array('sStatus' => 'success', 'status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
	}
	
	public function generarTareaRepetirMensual($arrPost){
		$this->db->trans_begin();
		
		$arrRegistro = $this->db->query("SELECT ID_Empresa, ID_Organizacion, ID_Almacen FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $arrPost['ID'] . " LIMIT 1")->result();		
		$ID_Empresa = $arrRegistro[0]->ID_Empresa;
		$ID_Organizacion = $arrRegistro[0]->ID_Organizacion;
		$ID_Almacen = $arrRegistro[0]->ID_Almacen;
		
		$tarea_repetir_cron = array(
			'ID_Empresa' => $ID_Empresa,
			'ID_Organizacion' => $ID_Organizacion,
			'ID_Almacen' => $ID_Almacen,
			'ID_Referencia' => $arrPost['ID'],
			'ID_Tipo_Tiempo_Repetir' => $arrPost['ID_Tipo_Tiempo_Repetir'],
			'Nu_Month' => $arrPost['Nu_Month'],
			'Nu_Dia' => $arrPost['Nu_Dia'],
			'Nu_Estado' => 1,
		);
		$this->db->insert('tarea_repetir_cron', $tarea_repetir_cron);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al generar repetición');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Repetición generada');
        }
	}
	
	public function eliminarTareaRepetirMensual($ID){
		$this->db->trans_begin();
		
		$arrRegistro = $this->db->query("SELECT ID_Empresa, ID_Organizacion, ID_Almacen FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $ID . " LIMIT 1")->result();
		
		$this->db->where('ID_Referencia', $ID);
        $this->db->delete('tarea_repetir_cron');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al generar repetecion');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Repeticion generada');
        }
	}

	public function verificarSunatCDR($arrParams){
		return $this->db->query("SELECT Txt_Url_CDR FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $arrParams['ID_Documento_Cabecera'] . " LIMIT 1")->row()->Txt_Url_CDR;
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
AND Nu_Estado=1 AND ID_POS IS NULL
" . $where_serie . " LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();

		$sTidoDocumento = 'Guía Interna';
		if ( $arrPost['radio-TipoDocumento'] == '7' )
			$sTidoDocumento = 'Guía Física';
		else if ( $arrPost['radio-TipoDocumento'] == '8' )
			$sTidoDocumento = 'Guía Electrónica';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			return array('sStatus' => 'danger', 'sMessage' => 'Deben configurar serie para ' . $sTidoDocumento . ', no existe');
		}
		
		if($this->db->query("SELECT COUNT(*) AS existe FROM guia_cabecera AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $arrPost['Hidden_ID_Empresa'] . " AND TMOVI.Nu_Tipo_Movimiento = 1 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $arrPost['Hidden_ID_Entidad'] . " AND GC.ID_Tipo_Documento = " . $iTipoDocumento . " AND GC.ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND GC.ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			return array('sStatus' => 'warning', 'sMessage' => 'Ya existe guía ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . ' modificar correlativo en la opción Ventas -> Series' );
		}else{
			if (!empty($arrPost['Txt_Direccion_Entidad-modal'])){
				$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrPost['Txt_Direccion_Entidad-modal'] . "' WHERE ID_Entidad = " . $arrPost['Hidden_ID_Entidad'];
				$this->db->query($sql);
			}

			if (empty($this->db->query("SELECT Txt_Direccion_Entidad FROM entidad WHERE ID_Entidad=" . $arrPost['Hidden_ID_Entidad'] . " LIMIT 1")->row()->Txt_Direccion_Entidad)) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'No tiene dirección, registrar > Historial de Venta o Cliente');
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
			
			$iDescargarStock = ((isset($arrPost['Nu_Descargar_Stock-modal']) && $arrPost['Nu_Descargar_Stock-modal'] == 1) ? 1 : 0);

			//obtener mas campos de la cotizacion
			$objDocumentoCabeceraCotizacion = $this->db->query("SELECT No_Formato_PDF FROM documento_cabecera WHERE ID_Documento_Cabecera = " . $arrPost['Hidden_ID_Documento_Cabecera'] . " LIMIT 1")->row();

			$arrHeader = array(
				'ID_Empresa' => $arrPost['Hidden_ID_Empresa'],
				'ID_Organizacion' => $arrPost['Hidden_ID_Organizacion'],
				'ID_Almacen' => $arrPost['Hidden_ID_Almacen'],
				'ID_Tipo_Asiento' => 3,
				'ID_Tipo_Documento' => $iTipoDocumento,
				'ID_Serie_Documento_PK' => $arrSerieDocumento->ID_Serie_Documento_PK,
				'ID_Serie_Documento' => $arrSerieDocumento->ID_Serie_Documento,
				'ID_Numero_Documento' => $arrSerieDocumento->Nu_Numero_Documento,
				//'Fe_Emision' => $arrPost['Hidden_Fe_Emision'],
				//'Fe_Periodo' => $arrPost['Hidden_Fe_Emision'],
				'Fe_Emision' => $Fe_Guia,
				'Fe_Periodo' => $Fe_Guia,
				'ID_Moneda' => $arrPost['Hidden_ID_Moneda'],
				'Nu_Descargar_Inventario' => $iDescargarStock,
				'ID_Tipo_Movimiento' => 1,
				'ID_Entidad' => $arrPost['Hidden_ID_Entidad'],
				'Ss_Total' => $arrPost['Hidden_Ss_Total'],
				'Nu_Estado' => 6,
				'ID_Almacen_Transferencia' => 0,
				'Ss_Peso_Bruto' => $arrPost['Ss_Peso_Bruto'],
				'Nu_Bulto' => $arrPost['Nu_Bulto'],
				'No_Tipo_Transporte' => $arrPost['radio-TipoTransporte'],
				'No_Formato_PDF' => $objDocumentoCabeceraCotizacion->No_Formato_PDF
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
Ss_Total,
Txt_Nota
FROM
documento_detalle
WHERE
ID_Documento_Cabecera = " . $arrPost['Hidden_ID_Documento_Cabecera'];
			$arrDetalle = $this->db->query($query_detalle)->result();
		
			foreach ($arrDetalle as $row) {
				//si existe variable y si es igual a 1
				if(isset($arrPost['Nu_Descargar_Stock-modal']) && $arrPost['Nu_Descargar_Stock-modal'] == 1){
					if ($this->empresa->Nu_Validar_Stock==1){//Activada la validación de stock
						$objItem = $this->db->query("SELECT Nu_Tipo_Producto, Nu_Compuesto, No_Producto FROM producto WHERE ID_Producto =".$row->ID_Producto." LIMIT 1")->row();

						if ( $objItem->Nu_Tipo_Producto == 1 ){
							if ( $objItem->Nu_Compuesto == 0 ){
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$row->ID_Producto." AND ID_Almacen = " . $arrPost['Hidden_ID_Almacen'] . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $row->Qt_Producto ){
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
								ENLAPRO.ID_Producto_Enlace = " . $row->ID_Producto;
								$arrItemsEnlazados = $this->db->query($query)->result();
								
								foreach($arrItemsEnlazados as $row_enlace) {
									$ID_Producto_Enlace = $row_enlace->ID_Producto;
									$fStockVenta = ($row->Qt_Producto * $row_enlace->Qt_Producto_Descargar);
									$objItem = $this->db->query("SELECT No_Producto FROM producto WHERE ID_Producto =".$ID_Producto_Enlace." LIMIT 1")->row();
									$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $arrPost['Hidden_ID_Almacen'] . " LIMIT 1")->row();
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
				}

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
					'Ss_Total' => $row->Ss_Total,
					'Txt_Nota' => $row->Txt_Nota
				);
			}
			$this->db->insert_batch('guia_detalle', $arrDetail);
			
			//si existe variable y si es igual a 1
			if(isset($arrPost['Nu_Descargar_Stock-modal']) && $arrPost['Nu_Descargar_Stock-modal'] == 1) {
				$this->MovimientoInventarioModel->crudMovimientoInventario($arrPost['Hidden_ID_Almacen'], 0, $Last_ID_Guia_Cabecera, $arrDetail, 1, 0, '', 1, 1);
			}

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
					// fin correlativo

					$this->db->trans_commit();
					
					return array(
						'sStatus' => 'success',
						'sMessage' => 'Guía generada',
						'iIdDocumentoCabecera' => $Last_ID_Guia_Cabecera,
						'arrResponseFE' => '',
					);
				} else if ($this->empresa->Nu_Estado_Pago_Sistema == 0 && $arrPost['radio-TipoDocumento'] != '14') {// pago pendiente y diferente 2 = Documento interno
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
