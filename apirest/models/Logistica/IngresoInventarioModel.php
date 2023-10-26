<?php
class IngresoInventarioModel extends CI_Model{
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

        $this->db->select('TMOVI.Nu_Tipo_Movimiento, ALMA.No_Almacen, VC.ID_Almacen_Transferencia, VC.ID_Guia_Cabecera, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Tipo_Documento, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Nu_Proceso_Transferencia_Almacen')
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
		->where('TMOVI.Nu_Tipo_Movimiento', 0);
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
		
        if($this->input->post('filtro_almacen') == 0)
			$this->db->or_where("(VC.ID_Empresa = " . $this->empresa->ID_Empresa . " AND VC.ID_Almacen_Transferencia = " . $this->session->userdata['almacen']->ID_Almacen . " AND VC.Nu_Proceso_Transferencia_Almacen=0 AND VC.ID_Tipo_Movimiento=15)");
		
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

        $this->db->select('TMOVI.Nu_Tipo_Movimiento, ALMA.No_Almacen, VC.ID_Almacen_Transferencia, VC.ID_Guia_Cabecera, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Tipo_Documento, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PROVE.No_Entidad, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, VC.Nu_Descargar_Inventario, VC.Nu_Proceso_Transferencia_Almacen')
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
		->where('TMOVI.Nu_Tipo_Movimiento', 0);
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
		
        if($this->input->post('filtro_almacen') == 0)
			$this->db->or_where("(VC.ID_Empresa = " . $this->empresa->ID_Empresa . " AND VC.ID_Almacen_Transferencia = " . $this->session->userdata['almacen']->ID_Almacen . " AND VC.Nu_Proceso_Transferencia_Almacen=0 AND VC.ID_Tipo_Movimiento=15)");
		
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
PROVE.Txt_Direccion_Entidad AS Txt_Direccion_Destino,
TDOCUIDENCLI.No_Tipo_Documento_Identidad_Breve,
PROVE.No_Entidad,
PROVE.Nu_Documento_Identidad,
VC.ID_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
TDOCU.No_Tipo_Documento,
VC.ID_Serie_Documento,
VC.ID_Numero_Documento,
VC.Fe_Emision,
VC.ID_Moneda,
VC.Fe_Periodo,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
VD.ID_Producto,
PRO.Nu_Codigo_Barra,
PRO.No_Codigo_Interno,
PRO.No_Producto,
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
IMP.Nu_Tipo_Impuesto,
IMP.No_Impuesto_Breve,
ICDOCU.Po_Impuesto,
UM.No_Unidad_Medida,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3,
VC.No_Formato_PDF,
VC.Ss_Peso_Bruto,
VC.Nu_Bulto,
VC.No_Tipo_Transporte
FROM
" . $this->table . " AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN " . $this->table_guia_detalle . " AS VD ON(VC.ID_Guia_Cabecera = VD.ID_Guia_Cabecera)
JOIN " . $this->table_entidad . " AS PROVE ON(PROVE.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDENCLI ON(PROVE.ID_Tipo_Documento_Identidad = TDOCUIDENCLI.ID_Tipo_Documento_Identidad)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
LEFT JOIN flete AS F ON(F.ID_Guia_Cabecera = VC.ID_Guia_Cabecera)
LEFT JOIN " . $this->table_entidad . " AS TRANS ON(TRANS.ID_Entidad = F.ID_Entidad)
LEFT JOIN tipo_documento_identidad AS TDOCUIDEN ON(TRANS.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = PRO.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
WHERE
VC.ID_Guia_Cabecera = " . $ID;
        return $this->db->query($query)->result();
    }
    
    public function agregarCompra($arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Guia_Cabecera, $arrProveedorNuevo){
    	$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];

		$sTidoDocumento = 'Guía Interna';
		if ( $arrCompraCabecera['ID_Tipo_Documento'] == '7' )
			$sTidoDocumento = 'G/Remisión';

		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $this->user->ID_Empresa . " AND TMOVI.Nu_Tipo_Movimiento = 0 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $ID_Entidad . " AND GC.ID_Tipo_Documento = " . $arrCompraCabecera['ID_Tipo_Documento'] . " AND GC.ID_Serie_Documento = '" . $arrCompraCabecera['ID_Serie_Documento'] . "' AND GC.ID_Numero_Documento = " . $arrCompraCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe la guía de entrada ' . $sTidoDocumento . ' - ' . $arrCompraCabecera['ID_Serie_Documento'] . ' - ' . $arrCompraCabecera['ID_Numero_Documento']);
		}else{
			$this->db->trans_begin();

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
			
			if (is_array($arrProveedorNuevo)){
			    unset($arrCompraCabecera['ID_Entidad']);
			    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
					$arrProveedor = array(
		                'ID_Empresa'					=> $this->user->ID_Empresa,
	                	'ID_Organizacion'				=> $arrCompraCabecera['ID_Organizacion'],
		                'Nu_Tipo_Entidad'				=> 1,//Proveedor
		                'ID_Tipo_Documento_Identidad'	=> $arrProveedorNuevo['ID_Tipo_Documento_Identidad'],
		                'Nu_Documento_Identidad'		=> $arrProveedorNuevo['Nu_Documento_Identidad'],
		                'No_Entidad'					=> $arrProveedorNuevo['No_Entidad'],
		                'Txt_Direccion_Entidad' 		=> $arrProveedorNuevo['Txt_Direccion_Entidad'],
		                'Nu_Telefono_Entidad'			=> $arrProveedorNuevo['Nu_Telefono_Entidad'],
		                'Nu_Celular_Entidad'			=> $arrProveedorNuevo['Nu_Celular_Entidad'],
		                'Nu_Estado' 					=> 1,
		            );
		    		$this->db->insert('entidad', $arrProveedor);
		    		$Last_ID_Entidad = $this->db->insert_id();
			    } else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El proveedor ya se encuentra creado, seleccionar Existente');
				}
	    		$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			//Flete
			$arrFlete = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'iFlete' => $arrCompraCabecera['iFlete'],
				'ID_Entidad' => $arrCompraCabecera['ID_Entidad_Transportista'],
				'No_Placa' => $arrCompraCabecera['No_Placa'],
				'Fe_Traslado' => $arrCompraCabecera['Fe_Traslado'],
				'ID_Motivo_Traslado' => $arrCompraCabecera['ID_Motivo_Traslado'],
				'No_Licencia' => $arrCompraCabecera['No_Licencia'],
				'No_Certificado_Inscripcion' => $arrCompraCabecera['No_Certificado_Inscripcion'],
			);
			unset( $arrCompraCabecera['iFlete'] );
			unset( $arrCompraCabecera['ID_Entidad_Transportista'] );
			unset( $arrCompraCabecera['No_Placa'] );
			unset( $arrCompraCabecera['Fe_Traslado'] );
			unset( $arrCompraCabecera['ID_Motivo_Traslado'] );
			unset( $arrCompraCabecera['No_Licencia'] );
			unset( $arrCompraCabecera['No_Certificado_Inscripcion'] );

			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Guia_Cabecera = $this->db->insert_id();
			
			if ( $arrFlete['iFlete'] == '1' ) {
				unset( $arrFlete['iFlete'] );
				$arrFlete = array_merge( $arrFlete, array( 'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera) );
				$this->db->insert($this->table_flete, $arrFlete);
			}

			foreach ($arrCompraDetalle as $row) {
				$guia_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
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
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
	        } else {
	            $this->db->trans_commit();
	            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera);
	        }
		}
    }

    public function actualizarCompra($where, $arrCompraCabecera, $arrCompraDetalle, $esEnlace, $ID_Guia_Cabecera, $arrProveedorNuevo){
		$this->db->trans_begin();

		$arrDataModificar = $this->db->query("SELECT ID_Organizacion, ID_Almacen, ID_Guia_Cabecera, ID_Tipo_Documento, Nu_Correlativo, Nu_Descargar_Inventario FROM guia_cabecera WHERE ID_Guia_Cabecera = " . $where['ID_Guia_Cabecera'] . " LIMIT 1")->result();
		
		$ID_Guia_Cabecera = $arrDataModificar[0]->ID_Guia_Cabecera;
		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;
		
		$objAlmacen = $this->db->query("SELECT ID_Almacen, No_Almacen FROM almacen WHERE ID_Almacen=" . $ID_Almacen . " LIMIT 1")->row();
		if ($ID_Almacen != $this->session->userdata['almacen']->ID_Almacen ) {
			$this->db->trans_rollback();
			return array('status' => 'danger', 'style_modal' => 'modal-warning', 'message' => 'Para modificar debes seleccionar ' . $objAlmacen->No_Almacen);
		}

		$ID_Tipo_Documento = $arrDataModificar[0]->ID_Tipo_Documento;
		$Nu_Correlativo = $arrDataModificar[0]->Nu_Correlativo;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;
		
		$this->db->delete($this->table_guia_detalle, $where);

		$this->db->where('ID_Guia_Cabecera', $ID_Guia_Cabecera);
		$this->db->delete('flete');

		if ($Nu_Descargar_Inventario == 1) {
			$query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID_Guia_Cabecera;
			$arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where_stock_producto = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where_stock_producto);

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
			}
			$this->db->where('ID_Guia_Cabecera', $ID_Guia_Cabecera);
			$this->db->delete('movimiento_inventario');
		}
		
		$sTidoDocumento = 'Guía Interna';
		if ( $arrCompraCabecera['ID_Tipo_Documento'] == '7' )
			$sTidoDocumento = 'G/Remisión';

		$this->db->delete($this->table, $where);
		$ID_Entidad=0;
    	if (!empty($arrCompraCabecera['ID_Entidad']))
    		$ID_Entidad=$arrCompraCabecera['ID_Entidad'];
		if( $ID_Tipo_Documento != $arrCompraCabecera['ID_Tipo_Documento'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " AS GC JOIN tipo_movimiento AS TMOVI ON(TMOVI.ID_Tipo_Movimiento = GC.ID_Tipo_Movimiento) WHERE GC.ID_Empresa = " . $this->user->ID_Empresa . " AND TMOVI.Nu_Tipo_Movimiento = 0 AND GC.ID_Tipo_Asiento = 3 AND GC.ID_Entidad = " . $ID_Entidad . " AND GC.ID_Tipo_Documento = " . $arrCompraCabecera['ID_Tipo_Documento'] . " AND GC.ID_Serie_Documento = '" . $arrCompraCabecera['ID_Serie_Documento'] . "' AND GC.ID_Numero_Documento = " . $arrCompraCabecera['ID_Numero_Documento'] . " LIMIT 1")->row()->existe > 0){
			$this->db->trans_rollback();
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Ya existe la guía de entrada ' . $sTidoDocumento . ' - ' . $arrCompraCabecera['ID_Serie_Documento'] . ' - ' . $arrCompraCabecera['ID_Numero_Documento']);
		}else{
			if (is_array($arrProveedorNuevo)){
				unset($arrCompraCabecera['ID_Entidad']);
				if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 1 AND ID_Tipo_Documento_Identidad = " . $arrProveedorNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrProveedorNuevo['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){
					$arrProveedor = array(
						'ID_Empresa'					=> $this->user->ID_Empresa,
						'ID_Organizacion'				=> $arrCompraCabecera['ID_Organizacion'],
						'Nu_Tipo_Entidad'				=> 1,//Proveedor
						'ID_Tipo_Documento_Identidad'	=> $arrProveedorNuevo['ID_Tipo_Documento_Identidad'],
						'Nu_Documento_Identidad'		=> $arrProveedorNuevo['Nu_Documento_Identidad'],
						'No_Entidad'					=> $arrProveedorNuevo['No_Entidad'],
						'Txt_Direccion_Entidad' 		=> $arrProveedorNuevo['Txt_Direccion_Entidad'],
						'Nu_Telefono_Entidad'			=> $arrProveedorNuevo['Nu_Telefono_Entidad'],
						'Nu_Celular_Entidad'			=> $arrProveedorNuevo['Nu_Celular_Entidad'],
						'Nu_Estado' 					=> 1,
					);
					$this->db->insert('entidad', $arrProveedor);
					$Last_ID_Entidad = $this->db->insert_id();
				} else {
					$this->db->trans_rollback();
					return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El proveedor ya se encuentra creado, seleccionar Existente');
				}
				$arrCompraCabecera = array_merge($arrCompraCabecera, array("ID_Entidad" => $Last_ID_Entidad));
			}
			
			$arrCompraCabecera['ID_Almacen'] = $ID_Almacen;
			$arrCompraCabecera['ID_Guia_Cabecera'] = $ID_Guia_Cabecera;

			//Flete
			$arrFlete = array(
				'ID_Empresa' => $this->user->ID_Empresa,
				'iFlete' => $arrCompraCabecera['iFlete'],
				'ID_Entidad' => $arrCompraCabecera['ID_Entidad_Transportista'],
				'No_Placa' => $arrCompraCabecera['No_Placa'],
				'Fe_Traslado' => $arrCompraCabecera['Fe_Traslado'],
				'ID_Motivo_Traslado' => $arrCompraCabecera['ID_Motivo_Traslado'],
				'No_Licencia' => $arrCompraCabecera['No_Licencia'],
				'No_Certificado_Inscripcion' => $arrCompraCabecera['No_Certificado_Inscripcion'],
			);
			unset( $arrCompraCabecera['iFlete'] );
			unset( $arrCompraCabecera['ID_Entidad_Transportista'] );
			unset( $arrCompraCabecera['No_Placa'] );
			unset( $arrCompraCabecera['Fe_Traslado'] );
			unset( $arrCompraCabecera['ID_Motivo_Traslado'] );
			unset( $arrCompraCabecera['No_Licencia'] );
			unset( $arrCompraCabecera['No_Certificado_Inscripcion'] );

			$arrCompraCabecera = array_merge($arrCompraCabecera, array("Nu_Correlativo" => $Nu_Correlativo));
			$this->db->insert($this->table, $arrCompraCabecera);
			$Last_ID_Guia_Cabecera = $this->db->insert_id();
			
			if ( $arrFlete['iFlete'] == '1' ) {
				unset( $arrFlete['iFlete'] );
				$arrFlete = array_merge( $arrFlete, array( 'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera) );
				$this->db->insert($this->table_flete, $arrFlete);
			}

			foreach ($arrCompraDetalle as $row) {
				$guia_detalle[] = array(
					'ID_Empresa' => $this->user->ID_Empresa,
					'ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera,
					'ID_Producto' => $this->security->xss_clean($row['ID_Producto']),
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
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al actualizar');
			} else {
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro actualizado', 'Last_ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera);
			}
		}
    }
    
	public function anularCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
				
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table_guia_detalle);

		$this->db->where('ID_Guia_Cabecera', $ID);
		$this->db->delete('flete');

    	if ($Nu_Descargar_Inventario == 1) {    		
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = ".$ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where);

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
        	}
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
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}

		$this->db->where('ID_Guia_Cabecera', $ID);
		$data = array(
			'Nu_Estado' => 7,
			'Ss_Descuento' => 0.00,
			'Ss_Total' => 0.00,
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
    
	public function eliminarCompra($ID, $Nu_Enlace, $Nu_Descargar_Inventario){
		$this->db->trans_begin();
				
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table_guia_detalle);

		$this->db->where('ID_Guia_Cabecera', $ID);
		$this->db->delete('flete');
                
    	if ($Nu_Descargar_Inventario == 1) {
	        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID;
	        $arrDetalle = $this->db->query($query)->result();
			foreach ($arrDetalle as $row) {
				if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
					$where = array('ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
					$Qt_Producto = $this->db->query("SELECT Qt_Producto FROM stock_producto WHERE ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
					
					$stock_producto = array('Qt_Producto' => ($Qt_Producto - round($row->Qt_Producto, 6)));
					$this->db->update('stock_producto', $stock_producto, $where);

					//actualizar costo promedio
					$arrParamsCostoPromedioStock = array(
						'ID_Almacen' => $row->ID_Almacen,
						'ID_Producto' => $row->ID_Producto
					);
					$this->HelperModel->updCostoPromedioProductoxAlmacen($arrParamsCostoPromedioStock);
				}
        	}
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
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete('guia_enlace');
		}
        
		$this->db->where('ID_Guia_Cabecera', $ID);
        $this->db->delete($this->table);
        
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
	}
    
	public function procesarStockTransferencia($arrPost){
		$query = "SELECT * FROM guia_cabecera WHERE ID_Guia_Cabecera = " . $arrPost['ID_Guia_Cabecera_Salida'] . " LIMIT 1";
		$arrCabecera = $this->db->query($query)->result();

		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 3 AND ID_Tipo_Movimiento=13 AND ID_Entidad = " . $arrCabecera[0]->ID_Entidad . " AND ID_Tipo_Documento = " . $arrPost['ID_Tipo_Documento_Modal'] . " AND ID_Serie_Documento = '" . $arrPost['ID_Serie_Documento_Modal'] . "' AND ID_Numero_Documento = " . $arrPost['ID_Numero_Documento_Modal'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			$sDescargarStock = 0;
			$ID_Entidad = 0;
			foreach ($arrCabecera as $row) {
				$ID_Entidad = $row->ID_Entidad;
				//verificar si existe proveedor
				$query = "SELECT COUNT(*) AS existe FROM entidad WHERE ID_Entidad = " . $ID_Entidad . " AND Nu_Tipo_Entidad=1 LIMIT 1";
				if($this->db->query($query)->row()->existe == 0){//si no existe creamos proveedor
					$query = "SELECT * FROM entidad WHERE ID_Entidad = " . $ID_Entidad . " AND Nu_Tipo_Entidad=0 LIMIT 1";
					$row_entidad = $this->db->query($query)->row();

					$arrProveedor = array(
						'ID_Empresa'					=> $row_entidad->ID_Empresa,
						'ID_Organizacion'				=> $row_entidad->ID_Organizacion,
						'Nu_Tipo_Entidad'				=> 1,//Proveedor
						'ID_Tipo_Documento_Identidad'	=> $row_entidad->ID_Tipo_Documento_Identidad,
						'Nu_Documento_Identidad'		=> $row_entidad->Nu_Documento_Identidad,
						'No_Entidad'					=> $row_entidad->No_Entidad,
						'Txt_Direccion_Entidad' 		=> $row_entidad->Txt_Direccion_Entidad,
						'Nu_Telefono_Entidad'			=> $row_entidad->Nu_Telefono_Entidad,
						'Nu_Celular_Entidad'			=> $row_entidad->Nu_Celular_Entidad,
						'Nu_Estado' 					=> 1,
					);
					if ($this->db->insert('entidad', $arrProveedor) > 0)
						$ID_Entidad = $this->db->insert_id();
					else {
						$this->db->trans_rollback();
						return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Problema al crear proveedor');
					}
				}

				$guia_cabecera = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Organizacion' => $this->empresa->ID_Organizacion,
					'ID_Almacen' => $arrPost['ID_Almacen_Modal'],
					'ID_Entidad' => $ID_Entidad,
					'ID_Tipo_Asiento' => 3,//Guías
					'ID_Tipo_Movimiento' => 13,
					'ID_Tipo_Documento' => $arrPost['ID_Tipo_Documento_Modal'],
					'ID_Serie_Documento' => $arrPost['ID_Serie_Documento_Modal'],
					'ID_Numero_Documento' => $arrPost['ID_Numero_Documento_Modal'],
					'Fe_Emision' => $row->Fe_Emision,
					'ID_Moneda' => $row->ID_Moneda,
					'Nu_Descargar_Inventario' => $row->Nu_Descargar_Inventario,
					'Txt_Glosa' => $row->Txt_Glosa,
					'Ss_Total' => $row->Ss_Total,
					'Nu_Estado' => $row->Nu_Estado,
					'ID_Lista_Precio_Cabecera' => $row->ID_Lista_Precio_Cabecera,
					'Fe_Periodo' => $row->Fe_Periodo,
					'Nu_Correlativo' => $row->Nu_Correlativo,
					'Ss_Descuento' => $row->Ss_Descuento,
					'Po_Descuento' => $row->Po_Descuento,
					'ID_Almacen_Transferencia' => $row->ID_Almacen,
					'Nu_Proceso_Transferencia_Almacen' => 1
				);
				$sDescargarStock = $row->Nu_Descargar_Inventario;
			}

			$this->db->trans_begin();

			$this->db->query("UPDATE guia_cabecera SET Nu_Proceso_Transferencia_Almacen = 1 WHERE ID_Guia_Cabecera = " . $arrPost['ID_Guia_Cabecera_Salida']);

			$this->db->insert($this->table, $guia_cabecera);
			$ID_Guia_Cabecera = $this->db->insert_id();
			
			$query_detalle = "SELECT * FROM guia_detalle WHERE ID_Guia_Cabecera = " . $arrPost['ID_Guia_Cabecera_Salida'];
			$arrDetalle = $this->db->query($query_detalle)->result();
			foreach ($arrDetalle as $row) {
				$guia_detalle[] = array(
					'ID_Empresa' => $this->empresa->ID_Empresa,
					'ID_Guia_Cabecera' => $ID_Guia_Cabecera,
					'ID_Producto' => $row->ID_Producto,
					'Qt_Producto' => $row->Qt_Producto,
					'Txt_Nota' => $row->Txt_Nota,
					'Ss_Precio' => $row->Ss_Precio,
					'Ss_Descuento' => $row->Ss_Descuento,
					'Po_Descuento' => $row->Po_Descuento,
					'Ss_SubTotal' => $row->Ss_SubTotal,
					'Ss_Impuesto' => $row->Ss_Impuesto,
					'Ss_Total' => $row->Ss_Total,
					'ID_Impuesto_Cruce_Documento' => $row->ID_Impuesto_Cruce_Documento,
					'Ss_Descuento_Impuesto' => $row->Ss_Descuento_Impuesto,
				);
			}
			$this->db->insert_batch($this->table_guia_detalle, $guia_detalle);

			if ($this->db->query("SELECT COUNT(*) AS existe FROM flete WHERE ID_Guia_Cabecera = " . $arrPost['ID_Guia_Cabecera_Salida'] . " LIMIT 1")->row()->existe > 0) {
				$query = "SELECT * FROM flete WHERE ID_Guia_Cabecera = " . $arrPost['ID_Guia_Cabecera_Salida'] . " LIMIT 1";
				$arrFlete = $this->db->query($query)->result();
				foreach ($arrFlete as $row) {
					$arrFlete = array(
						'ID_Empresa' => $row->ID_Empresa,
						'ID_Guia_Cabecera' => $ID_Guia_Cabecera,
						'ID_Entidad' => $row->ID_Entidad,
						'No_Placa' => $row->No_Placa,
						'Fe_Traslado' => $row->Fe_Traslado,
						'ID_Motivo_Traslado' => $row->ID_Motivo_Traslado,
						'No_Licencia' => $row->No_Licencia,
						'No_Certificado_Inscripcion' => $row->No_Certificado_Inscripcion,
					);
				}
				$this->db->insert($this->table_flete, $arrFlete);
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al guardar registro');
			} else {
				$this->db->trans_commit();
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro agregado', 'Last_ID_Guia_Cabecera' => $ID_Guia_Cabecera, 'sDescargarStock' => $sDescargarStock, 'arrDetalle' => $guia_detalle);
			}
		}
	}

    public function getSalidaInventarioTransferencia($ID){
		return $this->db->query("SELECT
GD.Qt_Producto,
ITEM.No_Producto,
ITEM.Nu_Codigo_Barra AS Codigo,
VIDCAB.No_Variante AS No_Variante_1,
VID.No_Valor AS No_Valor_Variante_1,
VIDCAB2.No_Variante AS No_Variante_2,
VID2.No_Valor AS No_Valor_Variante_2,
VIDCAB3.No_Variante AS No_Variante_3,
VID3.No_Valor AS No_Valor_Variante_3
FROM
guia_cabecera AS GC
JOIN guia_detalle AS GD ON(GC.ID_Guia_Cabecera = GD.ID_Guia_Cabecera)
JOIN producto AS ITEM ON(ITEM.ID_Producto = GD.ID_Producto)
LEFT JOIN variante_item_detalle AS VID ON(VID.ID_Variante_Item_Detalle = ITEM.ID_Variante_Item_Detalle_1)
LEFT JOIN variante_item AS VIDCAB ON(VIDCAB.ID_Variante_Item = VID.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID2 ON(VID2.ID_Variante_Item_Detalle = ITEM.ID_Variante_Item_Detalle_2)
LEFT JOIN variante_item AS VIDCAB2 ON(VIDCAB2.ID_Variante_Item = VID2.ID_Variante_Item)
LEFT JOIN variante_item_detalle AS VID3 ON(VID3.ID_Variante_Item_Detalle = ITEM.ID_Variante_Item_Detalle_3)
LEFT JOIN variante_item AS VIDCAB3 ON(VIDCAB3.ID_Variante_Item = VID3.ID_Variante_Item)
WHERE
GC.ID_Guia_Cabecera=" . $ID)->result();
	}
}
