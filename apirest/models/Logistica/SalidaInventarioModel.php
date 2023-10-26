<?php
class SalidaInventarioModel extends CI_Model{
	var $table          				= 'guia_cabecera';
	var $table_guia_detalle				= 'guia_detalle';
	var $table_guia_enlace 				= 'guia_enlace';
	var $table_flete		 			= 'flete';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_tipo_movimiento			= 'tipo_movimiento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda					= 'moneda';
	var $table_organizacion				= 'organizacion';
	var $table_tabla_dato				= 'tabla_dato';
	var $table_almacen				= 'almacen';
	var $table_estado_documento = 'estado_documento';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
        
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
        
        if($this->input->post('Filtro_Estado') != '')
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));

        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

        $this->db->select('VC.ID_Guia_Cabecera, ALMA.No_Almacen, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Tipo_Documento, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.ID_Entidad AS ID_Proveedor, PROVE.No_Entidad, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR, VC.Txt_Respuesta_Sunat_FE, VC.Txt_Hash')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_tipo_movimiento . ' AS TMOVI', 'TMOVI.ID_Tipo_Movimiento = VC.ID_Tipo_Movimiento', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
		->where('VC.ID_Tipo_Asiento', 3)
		->where('TMOVI.Nu_Tipo_Movimiento', 1);
		
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

        if(isset($_POST['order']))
        	$this->db->order_by( 'VC.ID_Guia_Cabecera DESC' );
        else if(isset($this->order))
        	$this->db->order_by( 'VC.ID_Guia_Cabecera DESC' );
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
        
        if(!empty($this->input->post('Filtro_SerieDocumento')))
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
        
        if($this->input->post('Filtro_Estado') != '')
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if(!empty($this->input->post('Filtro_ID_Entidad')) && !empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('PROVE.ID_Entidad', $this->input->post('Filtro_ID_Entidad'));

        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

        $this->db->select('VC.ID_Guia_Cabecera, ALMA.No_Almacen, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Tipo_Documento, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.ID_Entidad AS ID_Proveedor, PROVE.No_Entidad, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Txt_Url_PDF, VC.Txt_Url_XML, VC.Txt_Url_CDR, VC.Txt_Respuesta_Sunat_FE, VC.Txt_Hash')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS PROVE', 'PROVE.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PROVE.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->join($this->table_tipo_movimiento . ' AS TMOVI', 'TMOVI.ID_Tipo_Movimiento = VC.ID_Tipo_Movimiento', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
		->where('VC.ID_Tipo_Asiento', 3)
		->where('TMOVI.Nu_Tipo_Movimiento', 1);
		
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $query = "SELECT
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Guia_Cabecera,
EMP.Txt_Direccion_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
ALMA.Txt_Direccion_Almacen AS Txt_Direccion_Origen,
PROVE.ID_Entidad,
TDOCUIDENCLI.No_Tipo_Documento_Identidad_Breve,
PROVE.No_Entidad,
PROVE.Nu_Documento_Identidad,
PROVE.Txt_Direccion_Entidad AS Txt_Direccion_Destino,
PROVE.Nu_Celular_Entidad,
PROVE.Txt_Email_Entidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
TDOCU.No_Tipo_Documento,
VC.ID_Serie_Documento,
SD.Nu_Cantidad_Caracteres,
VC.ID_Numero_Documento,
VC.Fe_Emision,
VC.ID_Moneda,
VC.Fe_Periodo,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
VD.ID_Producto,
PRO.Nu_Tipo_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
PRO.ID_Impuesto_Icbper,
VD.Ss_Precio AS Ss_Precio,
VD.Qt_Producto,
VD.ID_Impuesto_Cruce_Documento,
VD.Ss_SubTotal AS Ss_SubTotal_Producto,
VD.Ss_Impuesto AS Ss_Impuesto_Producto,
VD.Ss_Descuento AS Ss_Descuento_Producto,
VD.Ss_Descuento_Impuesto AS Ss_Descuento_Impuesto_Producto,
VD.Po_Descuento AS Po_Descuento_Impuesto_Producto,
VD.Ss_Total AS Ss_Total_Producto,
ICDOCU.Ss_Impuesto,
IMP.Nu_Tipo_Impuesto,
VC.Txt_Glosa,
TDMOTIVOTRAS.Nu_Valor AS Nu_Codigo_Motivo_Traslado_Sunat,
TDMOTIVOTRAS.No_Descripcion AS No_Motivo_Traslado_Sunat,
VC.Ss_Descuento AS Ss_Descuento,
VC.Ss_Total AS Ss_Total,
VC.Po_Descuento,
VC.ID_Tipo_Movimiento,
F.ID_Entidad AS ID_Entidad_Transportista,
TDOCUIDEN.No_Tipo_Documento_Identidad_Breve AS No_Tipo_Documento_Identidad_Breve_Transporte,
TRANS.ID_Tipo_Documento_Identidad AS ID_Tipo_Documento_Identidad_Transportista,
TRANS.No_Entidad AS No_Entidad_Transportista,
TRANS.Nu_Documento_Identidad AS Nu_Documento_Identidad_Transportista,
F.ID_Ubigeo_Inei_Llegada AS ID_Ubigeo_Inei_Llegada,
F.No_Placa,
F.Fe_Traslado,
F.ID_Motivo_Traslado,
F.No_Licencia,
F.No_Certificado_Inscripcion,
VC.ID_Almacen_Transferencia,
IMP.Nu_Tipo_Impuesto,
IMP.No_Impuesto_Breve,
ICDOCU.Po_Impuesto,
UM.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_UM,
UM.No_Unidad_Medida, 
VC.No_Formato_PDF,
VC.Ss_Peso_Bruto,
VC.Nu_Bulto,
VC.No_Tipo_Transporte,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
CONFI.Nu_Tipo_Rubro_Empresa,
VD.Txt_Nota AS Txt_Nota_Item,
UO.No_Descripcion AS Valor_Ubigeo_Inei_Partida,
UD.No_Descripcion AS Valor_Ubigeo_Inei_Llegada
FROM
" . $this->table . " AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN " . $this->table_guia_detalle . " AS VD ON(VC.ID_Guia_Cabecera = VD.ID_Guia_Cabecera)
JOIN " . $this->table_entidad . " AS PROVE ON(PROVE.ID_Entidad = VC.ID_Entidad)
JOIN serie_documento AS SD ON(SD.ID_Serie_Documento_PK=VC.ID_Serie_Documento_PK)
JOIN tipo_documento_identidad AS TDOCUIDENCLI ON(PROVE.ID_Tipo_Documento_Identidad = TDOCUIDENCLI.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
LEFT JOIN flete AS F ON(F.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
LEFT JOIN " . $this->table_entidad . " AS TRANS ON(TRANS.ID_Entidad = F.ID_Entidad)
LEFT JOIN tipo_documento_identidad AS TDOCUIDEN ON(TRANS.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
LEFT JOIN tabla_dato AS TDMOTIVOTRAS ON(TDMOTIVOTRAS.ID_Tabla_Dato = F.ID_Motivo_Traslado)
LEFT JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
LEFT JOIN tabla_dato AS UO ON(UO.ID_Tabla_Dato = ALMA.ID_Ubigeo_Inei_Partida)
LEFT JOIN tabla_dato AS UD ON(UD.ID_Tabla_Dato = F.ID_Ubigeo_Inei_Llegada)
WHERE VC.ID_Guia_Cabecera = " . $ID;

		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'style_modal' => 'modal-danger',
				'sStatus' => 'danger',
				'sMessage' => 'Problemas al generar Guía',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message'],
				'sql' => $query,
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			$arrData = $arrResponseSQL->result();
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
    
    public function agregarCompra($arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Guia_Cabecera, $arrClienteNuevo){
		if ($arrCompraCabecera['ID_Tipo_Movimiento'] == 15 && $arrCompraCabecera['ID_Almacen_Transferencia'] == 0) {//15 = Salida de transferencia entre almacenes
			return array('sStatus' => 'warning', 'style_modal' => 'modal-warning', 'sMessage' => 'Deben de seleccionar Almacén Destino');
		}
		
    	$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];

		$this->db->trans_begin();

		$query = "SELECT
ID_Tipo_Documento,
ID_Serie_Documento_PK,
ID_Serie_Documento,
Nu_Numero_Documento
FROM
serie_documento
WHERE
ID_Serie_Documento_PK=" . $arrCompraCabecera['ID_Serie_Documento_PK'] . " LIMIT 1";
		$arrSerieDocumento = $this->db->query($query)->row();
		
		$sTidoDocumento = 'Guía Interna';
		if ( $arrCompraCabecera['ID_Tipo_Documento'] == '7' )
			$sTidoDocumento = 'G/Remisión';
		
		if ( $arrSerieDocumento == '' || empty($arrSerieDocumento) ) {
			$this->db->trans_rollback();
			return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'Falta configurar serie para ' . $sTidoDocumento . ', no existe');
		}

		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $this->user->ID_Empresa . " AND TMOVI.Nu_Tipo_Movimiento = 1 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $ID_Entidad . " AND GC.ID_Tipo_Documento = " . $arrSerieDocumento->ID_Tipo_Documento . " AND ID_Serie_Documento = '" . $arrSerieDocumento->ID_Serie_Documento . "' AND ID_Numero_Documento = '" . $arrSerieDocumento->Nu_Numero_Documento . "' LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('sStatus' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'sMessage' => 'Ya existe guia de salida ' . $sTidoDocumento . ' - ' . $arrSerieDocumento->ID_Serie_Documento . ' - ' . $arrSerieDocumento->Nu_Numero_Documento . '. Cambiar correlativo en ventas y clientes > series');
		}else{
			$iTipoCliente = $arrCompraCabecera['iTipoCliente'];
			unset($arrCompraCabecera['iTipoCliente']);

			$Nu_Correlativo = 0;
			$Fe_Year = ToYear($arrCompraCabecera['Fe_Periodo']);
			$Fe_Month = ToMonth($arrCompraCabecera['Fe_Periodo']);
			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();

			if ( count($arrCorrelativoPendiente) > 0 ){
				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
				
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Tipo_Asiento', 3);
				$this->db->where('Fe_Year', $Fe_Year);
				$this->db->where('Fe_Month', $Fe_Month);
				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
		        $this->db->delete('correlativo_tipo_asiento_pendiente');
			} else {
				if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
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
				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 3 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
			}
			
			if ($iTipoCliente == 0){//0=cliente existente
				$arrClienteBD = $this->db->query("SELECT Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $arrCompraCabecera['ID_Entidad'] . " LIMIT 1")->result();
				$Nu_Celular_Entidad = '';
				if ( strlen($arrCompraCabecera['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrCompraCabecera['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if ( (!empty($arrCompraCabecera['Txt_Direccion_Entidad']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrCompraCabecera['Txt_Direccion_Entidad']) || (!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) || (!empty($arrCompraCabecera['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrCompraCabecera['Txt_Email_Entidad']) ) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrCompraCabecera['Txt_Direccion_Entidad'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrCompraCabecera['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $arrCompraCabecera['ID_Entidad'];
					$this->db->query($sql);
				}// /. if cambiar celular o correo

			    unset($arrCompraCabecera['Txt_Email_Entidad']);
			    unset($arrCompraCabecera['Nu_Celular_Entidad']);
			    unset($arrCompraCabecera['Txt_Direccion_Entidad']);
			}

			if (!empty($arrCompraCabecera['ID_Entidad']) && substr($arrSerieDocumento->ID_Serie_Documento,0,1) == 'T') {
				if (empty($this->db->query("SELECT Txt_Direccion_Entidad FROM entidad WHERE ID_Entidad=" . $arrCompraCabecera['ID_Entidad'] . " LIMIT 1")->row()->Txt_Direccion_Entidad)) {
					$this->db->trans_rollback();
					return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'No tiene dirección, registrar en opción > Cliente');
				}
			}

			if (is_array($arrClienteNuevo)){
			    unset($arrCompraCabecera['ID_Entidad']);

				if(substr($arrSerieDocumento->ID_Serie_Documento,0,1) == 'T' && empty($arrClienteNuevo['Txt_Direccion_Entidad'])){
					$this->db->trans_rollback();
					return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'No tiene dirección');
				}

			    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . "' LIMIT 1")->row()->existe == 0){
					$arrCliente = array(
		                'ID_Empresa'					=> $this->user->ID_Empresa,
	                	'ID_Organizacion'				=> $arrCompraCabecera['ID_Organizacion'],
		                'Nu_Tipo_Entidad'				=> 0,//Cliente
		                'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
		                'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
		                'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
		                'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
		                'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
		                'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
						'Txt_Email_Entidad' => $arrClienteNuevo['Txt_Email_Entidad'],
		                'Nu_Estado' => 1,
		            );
		    		$this->db->insert('entidad', $arrCliente);
		    		$Last_ID_Entidad = $this->db->insert_id();
			    } else {
					$this->db->trans_rollback();
					return array('sStatus' => 'warning', 'style_modal' => 'modal-warning', 'sMessage' => 'El cliente ya se encuentra creado, seleccionar Existente');
				}
			    unset($arrCompraCabecera['Txt_Email_Entidad']);
			    unset($arrCompraCabecera['Nu_Celular_Entidad']);
			    unset($arrCompraCabecera['Txt_Direccion_Entidad']);
	    		$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			//Flete
			$arrFlete = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'iFlete' => $arrCompraCabecera['iFlete'],
				'ID_Entidad' => $arrCompraCabecera['ID_Entidad_Transportista'],
				'No_Placa' => $arrCompraCabecera['No_Placa'],
				'Fe_Traslado' => (!empty($arrCompraCabecera['Fe_Traslado']) ? $arrCompraCabecera['Fe_Traslado'] : dateNow('fecha')),
				'ID_Motivo_Traslado' => $arrCompraCabecera['ID_Motivo_Traslado'],
				'No_Licencia' => $arrCompraCabecera['No_Licencia'],
				'No_Certificado_Inscripcion' => $arrCompraCabecera['No_Certificado_Inscripcion'],
				'ID_Ubigeo_Inei_Llegada' => $arrCompraCabecera['ID_Ubigeo_Inei_Llegada']//UBIGEO
			);
			unset( $arrCompraCabecera['iFlete'] );
			unset( $arrCompraCabecera['ID_Entidad_Transportista'] );
			unset( $arrCompraCabecera['No_Placa'] );
			unset( $arrCompraCabecera['Fe_Traslado'] );
			unset( $arrCompraCabecera['ID_Motivo_Traslado'] );
			unset( $arrCompraCabecera['No_Licencia'] );
			unset( $arrCompraCabecera['No_Certificado_Inscripcion'] );
			unset( $arrCompraCabecera['ID_Ubigeo_Inei_Llegada'] );//UBIGEO

			$arrCompraCabecera['ID_Numero_Documento'] = $arrSerieDocumento->Nu_Numero_Documento;

			$Fe_Guia = $arrCompraCabecera['Fe_Emision'];
			$iDias = diferenciaFechasMultipleFormato($Fe_Guia, dateNow('fecha') , 'dias' );
			if ( $iDias > 1 && substr($arrSerieDocumento->ID_Serie_Documento,0,1) == 'T'){// Sobre paso los días límite
				$this->db->trans_rollback();
				return array('sStatus' => 'warning2', 'sMessage' => 'La fecha debe de ser máximo 1 día antes');
			}

			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Guia_Cabecera = $this->db->insert_id();
			
			if ( $arrFlete['iFlete'] == '1' || !empty($arrFlete['ID_Ubigeo_Inei_Llegada']) ) {
				unset( $arrFlete['iFlete'] );
				$arrFlete = array_merge( $arrFlete, array( 'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera) );
				$this->db->insert($this->table_flete, $arrFlete);
			}

			foreach ($arrCompraDetalle as $row) {
				$ID_Producto = $this->security->xss_clean($row['ID_Producto']);
				if ($this->empresa->Nu_Validar_Stock==1 && $arrCompraCabecera['Nu_Descargar_Inventario']==1){//Activada la validación de stock, debo de verificar items con stock
					$objItem = $this->db->query("SELECT Nu_Compuesto, Nu_Tipo_Producto, No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();

					if ( $objItem->Nu_Tipo_Producto == 1 ){
						if ( $objItem->Nu_Compuesto == 0 ){
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrCompraCabecera['ID_Almacen'] . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $row['Qt_Producto'] ){
									$this->db->trans_rollback();
									return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'No tiene stock el producto > ' . $objItem->No_Producto);
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
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $arrCompraCabecera['ID_Almacen'] . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
										$this->db->trans_rollback();
										return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
									}
								} else {
									$this->db->trans_rollback();
									return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Producto enlazado no tiene stock > ' . $objItem->No_Producto);
								}
							}
						}
					}
				}

				$guia_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
					'ID_Producto' => $ID_Producto,
					'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => $this->security->xss_clean($row['Ss_SubTotal']),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => $this->security->xss_clean($row['Ss_Impuesto']),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
				);
			}
			$this->db->insert_batch($this->table_guia_detalle, $guia_detalle);

	        if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'Error al insertar');
	        } else {
				$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento=Nu_Numero_Documento+1 WHERE ID_Empresa=" . $this->empresa->ID_Empresa . " AND ID_Tipo_Documento=" . $arrCompraCabecera['ID_Tipo_Documento'] . " AND ID_Serie_Documento='" . $arrSerieDocumento->ID_Serie_Documento . "'");

	            $this->db->trans_commit();
	            return array('sStatus' => 'success', 'style_modal' => 'modal-success', 'sMessage' => 'Registro guardado', 'Last_ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera);
	        }
		}
    }

    public function actualizarCompra($where, $arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Guia_Cabecera, $arrClienteNuevo){
		$this->db->trans_begin();

		$iTipoCliente = $arrCompraCabecera['iTipoCliente'];
		unset($arrCompraCabecera['iTipoCliente']);

		$arrDataModificar = $this->db->query("SELECT Nu_Proceso_Transferencia_Almacen, ID_Organizacion, ID_Almacen, ID_Guia_Cabecera, ID_Serie_Documento, ID_Tipo_Documento, Nu_Correlativo, Nu_Descargar_Inventario, Nu_Estado, Txt_Respuesta_Sunat_FE, Txt_Url_Comprobante, Txt_Url_PDF, Txt_Url_XML, Txt_Url_CDR, Txt_QR, Txt_Hash, No_Formato_PDF, Ss_Peso_Bruto, Nu_Bulto FROM guia_cabecera WHERE ID_Guia_Cabecera = " . $where['ID_Guia_Cabecera'] . " LIMIT 1")->result();
		
		$Nu_Proceso_Transferencia_Almacen = $arrDataModificar[0]->Nu_Proceso_Transferencia_Almacen;
		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;

		$objAlmacen = $this->db->query("SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Almacen=" . $ID_Almacen . " LIMIT 1")->row();
		if ($ID_Almacen != $this->session->userdata['almacen']->ID_Almacen ) {
			$this->db->trans_rollback();
			return array('sStatus' => 'warning', 'style_modal' => 'modal-warning', 'sMessage' => 'Para modificar debes seleccionar ' . $objAlmacen->No_Almacen);
		}

		if ($arrCompraCabecera['ID_Tipo_Movimiento'] == 15 && $arrCompraCabecera['ID_Almacen_Transferencia'] == 0) {//15 = Salida de transferencia entre almacenes
			$this->db->trans_rollback();
			return array('sStatus' => 'warning', 'style_modal' => 'modal-warning', 'sMessage' => 'Deben de seleccionar Almacén Destino');
		}

		$ID_Guia_Cabecera = $arrDataModificar[0]->ID_Guia_Cabecera;
		$ID_Tipo_Documento = $arrDataModificar[0]->ID_Tipo_Documento;
		$Nu_Correlativo = $arrDataModificar[0]->Nu_Correlativo;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;
		$Nu_Estado = $arrDataModificar[0]->Nu_Estado;
		$Txt_Respuesta_Sunat_FE = $arrDataModificar[0]->Txt_Respuesta_Sunat_FE;
		$Txt_Url_Comprobante = $arrDataModificar[0]->Txt_Url_Comprobante;
		$Txt_Url_PDF = $arrDataModificar[0]->Txt_Url_PDF;
		$Txt_Url_XML = $arrDataModificar[0]->Txt_Url_XML;
		$Txt_Url_CDR = $arrDataModificar[0]->Txt_Url_CDR;
		$Txt_QR = $arrDataModificar[0]->Txt_QR;
		$Txt_Hash = $arrDataModificar[0]->Txt_Hash;
		$No_Formato_PDF = $arrDataModificar[0]->No_Formato_PDF;

		if( substr($arrDataModificar[0]->ID_Serie_Documento,0,1)=='T' && $Nu_Estado>6 ){
			$Ss_Peso_Bruto = $arrDataModificar[0]->Ss_Peso_Bruto;
			$Nu_Bulto = $arrDataModificar[0]->Nu_Bulto;
		}
		
		if ($ID_Tipo_Documento != $arrCompraCabecera['ID_Tipo_Documento'])
			$this->db->query("UPDATE serie_documento SET Nu_Numero_Documento = Nu_Numero_Documento + 1 WHERE ID_Serie_Documento_PK=" . $arrCompraCabecera['ID_Serie_Documento_PK']);

		$this->db->delete($this->table_guia_detalle, $where);

		$this->db->where('ID_Guia_Cabecera', $ID_Guia_Cabecera);
		$this->db->delete('flete');

		if ($Nu_Descargar_Inventario == 1) {
			$query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID_Guia_Cabecera;
			$arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				//if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);

					//actualizar costo promedio
					/*
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
					*/
				//}
			}
			$this->db->where('ID_Guia_Cabecera', $ID_Guia_Cabecera);
			$this->db->delete('movimiento_inventario');
		}
    	
		$iTieneEnlaceGuiaConVenta = 0;
		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Guia_Cabecera = " . $ID_Guia_Cabecera . " LIMIT 1")->row()->existe > 0){
			$arrGuiaEnlace = $this->db->query("SELECT * FROM guia_enlace WHERE ID_Guia_Cabecera = " . $ID_Guia_Cabecera . " LIMIT 1")->row();

			$arrDataGuiaEnlace_Insert = array(
				'ID_Empresa' => $arrGuiaEnlace->ID_Empresa,
				'ID_Guia_Cabecera' => $arrGuiaEnlace->ID_Guia_Cabecera,
				'ID_Documento_Cabecera' => $arrGuiaEnlace->ID_Documento_Cabecera,
			);
			$iTieneEnlaceGuiaConVenta = 1;

			$this->db->where('ID_Guia_Cabecera', $ID_Guia_Cabecera);
	        $this->db->delete('guia_enlace');
		}

		$sTidoDocumento = 'Guía Interna';
		if ( $arrCompraCabecera['ID_Tipo_Documento'] == '7' )
			$sTidoDocumento = 'G/Remisión';
		
		$this->db->delete($this->table, $where);
		$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];
		if($ID_Tipo_Documento != $arrCompraCabecera['ID_Tipo_Documento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $this->user->ID_Empresa . " AND TMOVI.Nu_Tipo_Movimiento = 1 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $ID_Entidad . " AND GC.ID_Tipo_Documento = " . $arrCompraCabecera['ID_Tipo_Documento'] . " AND GC.ID_Serie_Documento = '" . $arrCompraCabecera['ID_Serie_Documento'] . "' AND GC.ID_Numero_Documento = " . $arrCompraCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('sStatus' => 'warning', 'message_nubefact' => '', 'style_modal' => 'modal-warning', 'sMessage' => 'Ya existe guia de salida ' . $sTidoDocumento . ' - ' . $arrCompraCabecera['ID_Serie_Documento'] . ' - ' . $arrCompraCabecera['ID_Numero_Documento'] . '. Cambiar correlativo en ventas y clientes > series');
		}else{
			if ($iTipoCliente == 0){//0=cliente existente
				$arrClienteBD = $this->db->query("SELECT Txt_Direccion_Entidad, Txt_Email_Entidad, Nu_Celular_Entidad FROM entidad WHERE ID_Entidad = " . $arrCompraCabecera['ID_Entidad'] . " LIMIT 1")->result();
				$Nu_Celular_Entidad = '';
				if ( strlen($arrCompraCabecera['Nu_Celular_Entidad']) == 11){
					$Nu_Celular_Entidad = explode(' ', $arrCompraCabecera['Nu_Celular_Entidad']);
					$Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
				}
				if ( (!empty($arrCompraCabecera['Txt_Direccion_Entidad']) && $arrClienteBD[0]->Txt_Direccion_Entidad != $arrCompraCabecera['Txt_Direccion_Entidad']) || (!empty($Nu_Celular_Entidad) && $arrClienteBD[0]->Nu_Celular_Entidad != $Nu_Celular_Entidad) || (!empty($arrCompraCabecera['Txt_Email_Entidad']) && $arrClienteBD[0]->Txt_Email_Entidad != $arrCompraCabecera['Txt_Email_Entidad']) ) {
					$sql = "UPDATE entidad SET Txt_Direccion_Entidad = '" . $arrCompraCabecera['Txt_Direccion_Entidad'] . "', Nu_Celular_Entidad = '" . $Nu_Celular_Entidad . "', Txt_Email_Entidad = '" . $arrCompraCabecera['Txt_Email_Entidad'] . "' WHERE ID_Entidad = " . $arrCompraCabecera['ID_Entidad'];
					$this->db->query($sql);
				}// /. if cambiar celular o correo

			    unset($arrCompraCabecera['Txt_Email_Entidad']);
			    unset($arrCompraCabecera['Nu_Celular_Entidad']);
			    unset($arrCompraCabecera['Txt_Direccion_Entidad']);
			}

			if (is_array($arrClienteNuevo)){
				unset($arrCompraCabecera['ID_Entidad']);
				if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . "' LIMIT 1")->row()->existe == 0){
					$arrCliente = array(
						'ID_Empresa'					=> $this->user->ID_Empresa,
						'ID_Organizacion'				=> $arrCompraCabecera['ID_Organizacion'],
						'Nu_Tipo_Entidad'				=> 0,//Cliente
						'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
						'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
						'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
						'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
						'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
						'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
						'Txt_Email_Entidad' => $arrClienteNuevo['Txt_Email_Entidad'],
						'Nu_Estado' => 1,
					);
					$this->db->insert('entidad', $arrCliente);
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array('sStatus' => 'warning', 'style_modal' => 'modal-warning', 'sMessage' => 'El cliente ya se encuentra creado, seleccionar Existente');
				}
			    unset($arrCompraCabecera['Txt_Email_Entidad']);
			    unset($arrCompraCabecera['Nu_Celular_Entidad']);
			    unset($arrCompraCabecera['Txt_Direccion_Entidad']);
				$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			$arrCompraCabecera['Nu_Proceso_Transferencia_Almacen'] = $Nu_Proceso_Transferencia_Almacen;
			$arrCompraCabecera['ID_Almacen'] = $ID_Almacen;
			$arrCompraCabecera['ID_Guia_Cabecera'] = $ID_Guia_Cabecera;
			$arrCompraCabecera['Nu_Estado'] = $Nu_Estado;
			$arrCompraCabecera['Txt_Respuesta_Sunat_FE'] = $Txt_Respuesta_Sunat_FE;
			$arrCompraCabecera['Txt_Url_Comprobante'] = $Txt_Url_Comprobante;
			$arrCompraCabecera['Txt_Url_PDF'] = $Txt_Url_PDF;
			$arrCompraCabecera['Txt_Url_XML'] = $Txt_Url_XML;
			$arrCompraCabecera['Txt_Url_CDR'] = $Txt_Url_CDR;
			$arrCompraCabecera['Txt_QR'] = $Txt_QR;
			$arrCompraCabecera['Txt_Hash'] = $Txt_Hash;
			$arrCompraCabecera['No_Formato_PDF'] = $No_Formato_PDF;

			if( substr($arrDataModificar[0]->ID_Serie_Documento,0,1)=='T' && $Nu_Estado>6 ){
				$Ss_Peso_Bruto['Nu_Bulto'] = $Ss_Peso_Bruto;
				$arrCompraCabecera['Nu_Bulto'] = $Nu_Bulto;
			}
			
			//Flete
			$arrFlete = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'iFlete' => $arrCompraCabecera['iFlete'],
				'ID_Entidad' => $arrCompraCabecera['ID_Entidad_Transportista'],
				'No_Placa' => $arrCompraCabecera['No_Placa'],
				'Fe_Traslado' => (!empty($arrCompraCabecera['Fe_Traslado']) ? $arrCompraCabecera['Fe_Traslado'] : dateNow('fecha')),
				'ID_Motivo_Traslado' => $arrCompraCabecera['ID_Motivo_Traslado'],
				'No_Licencia' => $arrCompraCabecera['No_Licencia'],
				'No_Certificado_Inscripcion' => $arrCompraCabecera['No_Certificado_Inscripcion'],
				'ID_Ubigeo_Inei_Llegada' => $arrCompraCabecera['ID_Ubigeo_Inei_Llegada']//UBIGEO
			);
			unset( $arrCompraCabecera['iFlete'] );
			unset( $arrCompraCabecera['ID_Entidad_Transportista'] );
			unset( $arrCompraCabecera['No_Placa'] );
			unset( $arrCompraCabecera['Fe_Traslado'] );
			unset( $arrCompraCabecera['ID_Motivo_Traslado'] );
			unset( $arrCompraCabecera['No_Licencia'] );
			unset( $arrCompraCabecera['No_Certificado_Inscripcion'] );
			unset( $arrCompraCabecera['ID_Ubigeo_Inei_Llegada'] );//UBIGEO

			$Fe_Guia = $arrCompraCabecera['Fe_Emision'];
			$iDias = diferenciaFechasMultipleFormato($Fe_Guia, dateNow('fecha') , 'dias' );
			if ( $iDias > 1 && substr($arrCompraCabecera['ID_Serie_Documento'],0,1) == 'T'){// Sobre paso los días límite
				$this->db->trans_rollback();
				return array('sStatus' => 'warning2', 'sMessage' => 'La fecha debe de ser máximo 1 día antes');
			}

			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Guia_Cabecera = $this->db->insert_id();
			
			if ($iTieneEnlaceGuiaConVenta==1){//si
				if ( $this->db->insert('guia_enlace', $arrDataGuiaEnlace_Insert) <= 0 ) {
					$this->db->trans_rollback();
					return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'No se puede generar enlace guia');
				}
			}

			if ( $arrFlete['iFlete'] == '1' || !empty($arrFlete['ID_Ubigeo_Inei_Llegada']) ) {
				unset( $arrFlete['iFlete'] );
				$arrFlete = array_merge( $arrFlete, array( 'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera) );
				$this->db->insert($this->table_flete, $arrFlete);
			}

			foreach ($arrCompraDetalle as $row) {
				$ID_Producto = $this->security->xss_clean($row['ID_Producto']);
				if ($this->empresa->Nu_Validar_Stock==1 && $arrCompraCabecera['Nu_Descargar_Inventario']==1){//Activada la validación de stock, debo de verificar items con stock
					$objItem = $this->db->query("SELECT Nu_Compuesto, Nu_Tipo_Producto, No_Producto FROM producto WHERE ID_Producto =".$ID_Producto." LIMIT 1")->row();

					if ( $objItem->Nu_Tipo_Producto == 1 ){
						if ( $objItem->Nu_Compuesto == 0 ){
							$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto." AND ID_Almacen = " . $arrCompraCabecera['ID_Almacen'] . " LIMIT 1")->row();
							if(is_object($objStockItemAlmacen)){
								if ( $objStockItemAlmacen->Qt_Producto < $row['Qt_Producto'] ){
									$this->db->trans_rollback();
									return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
								}
							} else {
								$this->db->trans_rollback();
								return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'No tiene stock el producto > ' . $objItem->No_Producto);
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
								$objStockItemAlmacen = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Producto =".$ID_Producto_Enlace." AND ID_Almacen = " . $arrCompraCabecera['ID_Almacen'] . " LIMIT 1")->row();
								if(is_object($objStockItemAlmacen)){
									if ( $objStockItemAlmacen->Qt_Producto < $fStockVenta ){
										$this->db->trans_rollback();
										return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Stock actual: ' . round($objStockItemAlmacen->Qt_Producto, 2) . ' del item > ' . $objItem->No_Producto);
									}
								} else {
									$this->db->trans_rollback();
									return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'message_nubefact' => '', 'sMessage' => 'Producto enlazado no tiene stock > ' . $objItem->No_Producto);
								}
							}
						}
					}
				}

				$guia_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
					'ID_Producto' => $ID_Producto,
					'Qt_Producto' => $this->security->xss_clean($row['Qt_Producto']),
					'Ss_Precio' => $this->security->xss_clean($row['Ss_Precio']),
					'Ss_SubTotal' => $this->security->xss_clean($row['Ss_SubTotal']),
					'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
					'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
					'Po_Descuento' => $row['Ss_Descuento'],
					'ID_Impuesto_Cruce_Documento' => $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
					'Ss_Impuesto' => $this->security->xss_clean($row['Ss_Impuesto']),
					'Ss_Total' => round($this->security->xss_clean($row['Ss_Total']), 2),
				);
			}
			$this->db->insert_batch($this->table_guia_detalle, $guia_detalle);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'style_modal' => 'modal-danger', 'sMessage' => 'Error al actualizar');
			} else {
				$this->db->trans_commit();
				return array('sStatus' => 'success', 'style_modal' => 'modal-success', 'sMessage' => 'Registro actualizado', 'Last_ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera);
			}
		}
    }
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario, $iEstado){
		$this->db->trans_begin();
		
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table_guia_detalle);

		$this->db->where('ID_Guia_Cabecera', $ID);
		$this->db->delete('flete');

    	if ($Nu_Descargar_Inventario == 1) {    		
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = ".$ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				//if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where);

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				//}
        	}
	        $this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
			$data = array(
				'Qt_Producto' => 0,
				'Ss_Precio' => 0,
				'Ss_SubTotal' => 0,
				'Ss_Costo_Promedio' => 0,
			);
	        $this->db->update('movimiento_inventario', $data);
    	}
    	
		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Guia_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}

        $this->db->where('ID_Empresa', $this->user->ID_Empresa);
		$this->db->where('ID_Guia_Cabecera', $ID);
		$data = array(
			'Nu_Estado' => $iEstado,
			'Ss_Descuento' => 0.00,
			'Ss_Total' => 0.00,
		);
        $this->db->update($this->table, $data);
        
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'error', 'style_modal' => 'modal-danger', 'sMessage' => 'Error al anular');
        } else {
			$this->db->trans_commit();
        	return array('sStatus' => 'success', 'style_modal' => 'modal-success', 'sMessage' => 'Registro anulado');
        }
	}
    
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
		
		$this->db->where('ID_Empresa', $this->user->ID_Empresa);
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table_guia_detalle);
                
		$this->db->where('ID_Guia_Cabecera', $ID);
		$this->db->delete('flete');

    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				//if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto + round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where);

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				//}
        	}
			$this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete('movimiento_inventario');
    	}
        
        $arrCorrelativoPendiente = $this->db->query("SELECT Fe_Periodo, Nu_Correlativo FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Guia_Cabecera = " . $ID . " LIMIT 1")->result();
        
        $sql_correlativo_pendiente_libro_sunat = "
INSERT INTO correlativo_tipo_asiento_pendiente (
 ID_Empresa,
 ID_Tipo_Asiento,
 Fe_Year,
 Fe_Month,
 Nu_Correlativo
) VALUES (
 " . $this->user->ID_Empresa . ",
 3,
 '" . ToYear($arrCorrelativoPendiente[0]->Fe_Periodo) . "',
 '" . ToMonth($arrCorrelativoPendiente[0]->Fe_Periodo) . "',
 " . $arrCorrelativoPendiente[0]->Nu_Correlativo . "
);
		";
		$this->db->query($sql_correlativo_pendiente_libro_sunat);

		if ($this->db->query("SELECT count(*) existe FROM guia_enlace WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Guia_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0){
			$this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}
        
        $this->db->where('ID_Empresa', $this->user->ID_Empresa);
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table);
        
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('sStatus' => 'error', 'style_modal' => 'modal-danger', 'sMessage' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('sStatus' => 'success', 'style_modal' => 'modal-success', 'sMessage' => 'Registro eliminado');
        }
	}
}
