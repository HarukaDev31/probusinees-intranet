<?php
class GuiaEntradaModel extends CI_Model{
	var $table          				= 'guia_cabecera';
	var $table_guia_detalle				= 'guia_detalle';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_tipo_movimiento			= 'tipo_movimiento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_moneda					= 'moneda';
	var $table_tabla_dato				= 'tabla_dato';
	
    var $column_order = array('VC.Fe_Emision', 'VC.ID_Tipo_Documento', 'VC.ID_Serie_Documento', 'VC.ID_Numero_Documento', 'TDOCUIDEN.No_Tipo_Documento_Identidad_Breve', 'PRO.No_Entidad', 'MONE.No_Signo', null, null);
    var $column_search = array('');
    var $order = array('VC.Fe_Emision' => 'DESC', 'VC.ID_Tipo_Documento' => 'DESC', 'VC.ID_Serie_Documento' => 'DESC', 'VC.ID_Numero_Documento' => 'DESC');
	
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
        if($this->input->post('Filtro_SerieDocumento')){
        	$this->db->where('VC.ID_Serie_Documento', $this->input->post('Filtro_SerieDocumento'));
        }
        
        if($this->input->post('Filtro_NumeroDocumento')){
        	$this->db->where('VC.ID_Numero_Documento', $this->input->post('Filtro_NumeroDocumento'));
        }
        
        if($this->input->post('Filtro_Estado') != ''){
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        }
        
        if($this->input->post('Filtro_Entidad')){
        	$this->db->where('PRO.No_Entidad', $this->input->post('Filtro_Entidad'));
        }
        
        $this->db->select('VC.ID_Guia_Cabecera, VE.ID_Documento_Cabecera, VC.Fe_Emision, TDOCU.No_Tipo_Documento_Breve, VC.ID_Serie_Documento, VC.ID_Numero_Documento, TDOCUIDEN.No_Tipo_Documento_Identidad_Breve, PRO.No_Entidad, MONE.No_Signo, ROUND(VC.Ss_Total, 2) AS Ss_Total, VC.Nu_Descargar_Inventario, VC.Nu_Estado, TDESTADO.No_Class AS No_Class_Estado, TDESTADO.No_Descripcion AS No_Descripcion_Estado')
		->from($this->table . ' AS VC')
		->join($this->table_guia_detalle . ' AS VD', 'VD.ID_Guia_Cabecera = VC.ID_Guia_Cabecera', 'left')
		->join('guia_enlace AS VE', 'VE.ID_Guia_Cabecera = VC.ID_Guia_Cabecera', 'left')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_tipo_movimiento . ' AS TMOVI', 'TMOVI.ID_Tipo_Movimiento = VC.ID_Tipo_Movimiento', 'join')
		->join($this->table_entidad . ' AS PRO', 'PRO.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_tipo_documento_identidad . ' AS TDOCUIDEN', 'TDOCUIDEN.ID_Tipo_Documento_Identidad = PRO.ID_Tipo_Documento_Identidad', 'join')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
    	->join($this->table_tabla_dato . ' AS TDESTADO', 'TDESTADO.Nu_Valor = VC.Nu_Estado AND TDESTADO.No_Relacion = "Tipos_EstadoDocumento"', 'join')
    	->where('VC.ID_Empresa', $this->user->ID_Empresa)
    	->where('VC.ID_Tipo_Asiento', 3)
    	->where('TMOVI.Nu_Tipo_Movimiento', 0)
    	->where('PRO.Nu_Tipo_Entidad', 0)
		->group_by('VC.ID_Guia_Cabecera, VC.Fe_Emision, VC.ID_Tipo_Documento, VC.ID_Serie_Documento, VC.ID_Numero_Documento');
        
        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID, $Nu_Tipo_Operacion){
        if ($Nu_Tipo_Operacion === '0') {//Factura y Guia
            $query = "
            SELECT
            	VC.ID_Empresa,
            	VC.ID_Organizacion,
            	VC.ID_Almacen,
            	VC.ID_Documento_Cabecera,
            	PROVE.ID_Entidad,
            	PROVE.No_Entidad,
            	PROVE.Nu_Documento_Identidad,
            	PROVE.Txt_Direccion_Entidad,
            	VC.ID_Tipo_Documento,
            	VC.ID_Serie_Documento,
            	VC.ID_Numero_Documento,
            	VE.ID_Tipo_Movimiento,
            	VC.Fe_Emision,
            	VC.ID_Moneda,
            	VC.Nu_Descargar_Inventario,
        		VC.ID_Lista_Precio_Cabecera,
            	VD.ID_Producto,
            	PRO.Nu_Codigo_Barra,
            	PRO.No_Producto,
            	ROUND(VD.Ss_Precio, 6) AS Ss_Precio,
            	VD.Qt_Producto,
        	    VD.ID_Impuesto_Cruce_Documento,
            	ROUND(VD.Ss_SubTotal, 2) AS Ss_SubTotal,
        	    ROUND(VD.Ss_Descuento, 2) AS Ss_Descuento_Producto,
        	    ICDOCU.Ss_Impuesto,
				VE.ID_Guia_Cabecera,
				VE.ID_Serie_Documento_Guia,
				VE.ID_Numero_Documento_Guia,
				VE.Nu_Descargar_Inventario_Guia,
                IMP.Nu_Tipo_Impuesto,
                VC.Txt_Glosa,
                ROUND(VC.Ss_Descuento, 2) AS Ss_Descuento,
                ROUND(VC.Ss_Total, 2) AS Ss_Total,
            	VC.Po_Descuento
            FROM
            	documento_cabecera AS VC
            	JOIN documento_detalle AS VD
            		ON (VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
            	JOIN " . $this->table_entidad . " AS PROVE
            		ON (PROVE.ID_Entidad = VC.ID_Entidad AND PROVE.Nu_Tipo_Entidad = 0)
            	JOIN producto AS PRO
	        		ON (PRO.ID_Producto = VD.ID_Producto)
	        	LEFT JOIN lista_precio_cabecera AS LPC
	        		ON (LPC.ID_Lista_Precio_Cabecera = VC.ID_Lista_Precio_Cabecera)
	        	LEFT JOIN lista_precio_detalle AS LPD
	        		ON (LPC.ID_Lista_Precio_Cabecera = LPD.ID_Lista_Precio_Cabecera AND LPD.ID_Producto = VD.ID_Producto)
	        	JOIN " . $this->table_tipo_documento . " AS TDOCU
            		ON (TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
            	JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU
            		ON (ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
    			JOIN impuesto AS IMP
    				ON (IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
            	LEFT JOIN (
        		SELECT
        			VE.ID_Documento_Cabecera,
        			GC.ID_Guia_Cabecera AS ID_Guia_Cabecera,
        			GC.ID_Serie_Documento AS ID_Serie_Documento_Guia,
        			GC.ID_Numero_Documento AS ID_Numero_Documento_Guia,
        			GC.ID_Tipo_Movimiento,
        			GC.Nu_Descargar_Inventario AS Nu_Descargar_Inventario_Guia
        		FROM
        			documento_cabecera AS VC
        			JOIN guia_enlace AS VE
        				ON (VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
        			JOIN guia_cabecera AS GC
        			    ON (GC.ID_Guia_Cabecera = VE.ID_Guia_Cabecera)
                    JOIN " . $this->table_tipo_documento . " AS TDOCU
                        ON (TDOCU.ID_Tipo_Documento = GC.ID_Tipo_Documento)
                WHERE
                	VC.ID_Tipo_Asiento = 2
            	) AS VE ON (VC.ID_Documento_Cabecera = VE.ID_Documento_Cabecera)
            WHERE
            	VC.ID_Tipo_Asiento = 2
            	AND VC.ID_Documento_Cabecera = " . $ID;
        } else {//Guia
            $query = "
            SELECT
            	VC.ID_Empresa,
            	VC.ID_Almacen,
            	VC.ID_Guia_Cabecera,
            	PROVE.ID_Entidad,
            	PROVE.No_Entidad,
            	PROVE.Nu_Documento_Identidad,
            	PROVE.Txt_Direccion_Entidad,
            	VC.ID_Tipo_Documento,
            	VC.ID_Serie_Documento AS ID_Serie_Documento_Guia,
            	VC.ID_Numero_Documento AS ID_Numero_Documento_Guia,
            	VC.ID_Tipo_Movimiento,
            	VC.Fe_Emision,
            	VC.ID_Moneda,
            	VC.Nu_Descargar_Inventario,
        		VC.ID_Lista_Precio_Cabecera,
            	VD.ID_Producto,
            	PRO.Nu_Codigo_Barra,
            	PRO.No_Producto,
            	ROUND(VD.Ss_Precio, 6) AS Ss_Precio,
            	VD.Qt_Producto,
        	    ICDOCU.ID_Impuesto_Cruce_Documento AS ID_Impuesto_Cruce_Documento,
            	ROUND(VD.Ss_SubTotal, 2) AS Ss_SubTotal,
        	    0.00 AS Ss_Descuento_Producto,
        	    ICDOCU.Ss_Impuesto,
    			'' AS ID_Tipo_Documento_Modificar,
    			'' AS ID_Serie_Documento_Modificar,
    			'' AS ID_Numero_Documento_Modificar,
    			VC.Nu_Descargar_Inventario AS Nu_Descargar_Inventario_Guia,
				ICDOCU.ID_Impuesto AS Nu_Tipo_Impuesto,
                VC.Txt_Glosa,
                0.00 AS Ss_Descuento,
                ROUND(VC.Ss_Total, 2) AS Ss_Total,
            	'' AS Po_Descuento
            FROM
            	guia_cabecera AS VC
            	JOIN guia_detalle AS VD
            		ON (VC.ID_Guia_Cabecera = VD.ID_Guia_Cabecera)
            	JOIN " . $this->table_entidad . " AS PROVE
            		ON (PROVE.ID_Entidad = VC.ID_Entidad AND PROVE.Nu_Tipo_Entidad = 0)
            	JOIN producto AS PRO
	        		ON (PRO.ID_Producto = VD.ID_Producto)
	        	LEFT JOIN lista_precio_cabecera AS LPC
	        		ON (LPC.ID_Lista_Precio_Cabecera = VC.ID_Lista_Precio_Cabecera)
	        	LEFT JOIN lista_precio_detalle AS LPD
	        		ON (LPC.ID_Lista_Precio_Cabecera = LPD.ID_Lista_Precio_Cabecera AND LPD.ID_Producto = VD.ID_Producto)
	        	JOIN " . $this->table_tipo_documento . " AS TDOCU
            		ON (TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
				JOIN impuesto_cruce_documento AS ICDOCU
					ON (ICDOCU.ID_Impuesto_Cruce_Documento = PRO.ID_Impuesto AND ICDOCU.Nu_Estado = 1)
            WHERE
            	VC.ID_Tipo_Asiento = 3
            	AND VC.ID_Guia_Cabecera = " . $ID;
        }
        return $this->db->query($query)->result();
    }
    
    public function agregarGuiaEntrada($arrGuiaEntradaCabecera, $arrGuiaEntradaDetalle){
    	$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
		if (
            $this->db->query("SELECT COUNT(*) existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Documento = " . $arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura'] . " AND ID_Serie_Documento = '" . $arrGuiaEntradaCabecera['ID_Serie_Documento_Factura'] . "' AND ID_Numero_Documento = '" . $arrGuiaEntradaCabecera['ID_Numero_Documento_Factura'] . "' LIMIT 1")->row()->existe > 0
            ||
            $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Documento = " . $arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia'] . " AND ID_Serie_Documento = '" . $arrGuiaEntradaCabecera['ID_Serie_Documento_Guia'] . "' AND ID_Numero_Documento = '" . $arrGuiaEntradaCabecera['ID_Numero_Documento_Guia'] . "' LIMIT 1")->row()->existe > 0
            ){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			$this->db->trans_begin();
			
			$Nu_Tipo_Operacion = $arrGuiaEntradaCabecera['ID_Tipo_Operacion'];			    
			unset($arrGuiaEntradaCabecera['ID_Tipo_Operacion']);
		
		    $ID_Tipo_Asiento_Factura = $arrGuiaEntradaCabecera['ID_Tipo_Asiento_Factura'];
		    $ID_Tipo_Documento_Factura = $arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura'];
		    $ID_Serie_Documento_Factura = $arrGuiaEntradaCabecera['ID_Serie_Documento_Factura'];
		    $ID_Numero_Documento_Factura = $arrGuiaEntradaCabecera['ID_Numero_Documento_Factura'];
		    $Ss_Descuento = $arrGuiaEntradaCabecera['Ss_Descuento'];
		    $Po_Descuento = $arrGuiaEntradaCabecera['Po_Descuento'];
		    
		    unset($arrGuiaEntradaCabecera['ID_Tipo_Asiento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Serie_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Numero_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['Ss_Descuento']);
		    unset($arrGuiaEntradaCabecera['Po_Descuento']);
			
			if ($Nu_Tipo_Operacion === '7' || $Nu_Tipo_Operacion === '0') {//Guia de Remision Compra
			    $arrGuiaEntradaCabecera['ID_Tipo_Asiento'] = $arrGuiaEntradaCabecera['ID_Tipo_Asiento_Guia'];;
			    $arrGuiaEntradaCabecera['ID_Tipo_Documento'] = $arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia'];
			    $arrGuiaEntradaCabecera['ID_Serie_Documento'] = $arrGuiaEntradaCabecera['ID_Serie_Documento_Guia'];
			    $arrGuiaEntradaCabecera['ID_Numero_Documento'] = $arrGuiaEntradaCabecera['ID_Numero_Documento_Guia'];
			    
			    unset($arrGuiaEntradaCabecera['ID_Tipo_Asiento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Serie_Documento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Numero_Documento_Guia']);
			    
    			$this->db->insert($this->table, $arrGuiaEntradaCabecera);
    			$Last_ID_Guia_Cabecera = $this->db->insert_id();
                
    			foreach ($arrGuiaEntradaDetalle as $row) {
    				$guia_detalle[] = array(
    					'ID_Empresa'		=> $this->user->ID_Empresa,
    					'ID_Guia_Cabecera'	=> $Last_ID_Guia_Cabecera,
    					'ID_Producto'		=> $this->security->xss_clean($row['ID_Producto']),
    					'Qt_Producto'		=> round($this->security->xss_clean($row['Qt_Producto']), 6),
    					'Ss_Precio'			=> round($this->security->xss_clean($row['Ss_Precio']), 6),
    					'Ss_SubTotal' 		=> round($this->security->xss_clean($row['Qt_Producto']) * $this->security->xss_clean($row['Ss_Precio']), 2)
    				);
    			}
    			$this->db->insert_batch($this->table_guia_detalle, $guia_detalle);
			}
			
			if ($Nu_Tipo_Operacion === '3' || $Nu_Tipo_Operacion === '0') {//Factura Compra
			    $arrCompraCabecera = $arrGuiaEntradaCabecera;
			    $arrCompraDetalle = $arrGuiaEntradaDetalle;
			    
			    $arrCompraCabecera['ID_Tipo_Asiento'] = $ID_Tipo_Asiento_Factura;
			    $arrCompraCabecera['ID_Organizacion'] = 1;
			    $arrCompraCabecera['ID_Tipo_Documento'] = $ID_Tipo_Documento_Factura;
			    $arrCompraCabecera['ID_Serie_Documento'] = $ID_Serie_Documento_Factura;
			    $arrCompraCabecera['ID_Numero_Documento'] = $ID_Numero_Documento_Factura;
			    $arrCompraCabecera['ID_Medio_Pago'] = 1;//Efectivo
			    $arrCompraCabecera['Fe_Vencimiento'] = $arrCompraCabecera['Fe_Emision'];
			    $arrCompraCabecera['Fe_Periodo'] = $arrCompraCabecera['Fe_Emision'];
			    $arrCompraCabecera['Nu_Detraccion'] = '';
			    $arrCompraCabecera['Fe_Detraccion'] = '';
			    $arrCompraCabecera['Ss_Descuento'] = $Ss_Descuento;
			    $arrCompraCabecera['Po_Descuento'] = $Po_Descuento;

			    unset($arrCompraCabecera['ID_Tipo_Operacion']);

			    unset($arrCompraCabecera['ID_Tipo_Asiento_Guia']);
			    unset($arrCompraCabecera['ID_Tipo_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Serie_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Numero_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Tipo_Movimiento']);
			    
			    $Nu_Correlativo = 0;
    			$Fe_Year = ToYear($arrCompraCabecera['Fe_Emision']);
    			$Fe_Month = ToMonth($arrCompraCabecera['Fe_Emision']);
    			$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();
    			
    			if ( count($arrCorrelativoPendiente) > 0 ){
    				$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
    				
    				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
    				$this->db->where('ID_Tipo_Asiento', 2);
    				$this->db->where('Fe_Year', $Fe_Year);
    				$this->db->where('Fe_Month', $Fe_Month);
    				$this->db->where('Nu_Correlativo', $Nu_Correlativo);
    		        $this->db->delete('correlativo_tipo_asiento_pendiente');
    			} else {
    				if($this->db->query("SELECT COUNT(*) existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
    					$sql_correlativo_libro_sunat = "
    					UPDATE
    						correlativo_tipo_asiento
    					SET
    						Nu_Correlativo = Nu_Correlativo + 1
    					WHERE
    						ID_Empresa			= " . $this->user->ID_Empresa . "
    						AND ID_Tipo_Asiento	= 2
    						AND Fe_Year 		= '" . $Fe_Year. "'
    						AND Fe_Month		= '" . $Fe_Month . "'
    					";
    					$this->db->query($sql_correlativo_libro_sunat);
    				} else {
    					$sql_correlativo_libro_sunat = "
    					INSERT INTO correlativo_tipo_asiento (
    						ID_Empresa,
    					    ID_Tipo_Asiento,
    					    Fe_Year,
    					    Fe_Month,
    					    Nu_Correlativo
    					) VALUES (
    						" . $this->user->ID_Empresa . ",
    						2,
    						'" . $Fe_Year . "',
    						'" . $Fe_Month . "',
    						1
    					);
    					";
    					$this->db->query($sql_correlativo_libro_sunat);
    				}
    				$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
    			}
    			
    			$_arrCompraCabecera = array("Nu_Correlativo" => $Nu_Correlativo);
    			$arrCompraCabecera_ = array_merge($arrCompraCabecera, $_arrCompraCabecera);
    			$this->db->insert('documento_cabecera', $arrCompraCabecera_);
    			$ID_Documento_Cabecera = $this->db->insert_id();
			    
    			foreach ($arrCompraDetalle as $row) {
    				$documento_detalle[] = array(
    					'ID_Empresa'					=> $this->user->ID_Empresa,
    					'ID_Documento_Cabecera'			=> $ID_Documento_Cabecera,
    					'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
    					'Qt_Producto'					=> round($this->security->xss_clean($row['Qt_Producto']), 6),
    					'Ss_Precio'						=> round($this->security->xss_clean($row['Ss_Precio']), 6),
    					'Ss_SubTotal' 					=> round($this->security->xss_clean($row['Ss_SubTotal']), 2),
    					'Ss_Descuento' 				    => round($this->security->xss_clean($row['Ss_Descuento']), 2),
    					'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento'])
    				);
    			}
    			$this->db->insert_batch('documento_detalle', $documento_detalle);
			}
			
			if ($Nu_Tipo_Operacion === '0') {
				$table_guia_enlace = array(
					'ID_Empresa'			=> $this->user->ID_Empresa,
					'ID_Guia_Cabecera'	    => $Last_ID_Guia_Cabecera,
					'ID_Documento_Cabecera'	=> $ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $table_guia_enlace);
				
				$Last_ID_Guia_Cabecera = $ID_Documento_Cabecera;
			}

        	$this->db->trans_complete();
	        if ($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
	        } else {
	            $this->db->trans_commit();
	            $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Guia_Cabecera' => "$Last_ID_Guia_Cabecera");
	        }
		}
		return $response;
    }
    
    public function actualizarGuiaEntrada($where, $whereFacturaCompra, $arrGuiaEntradaCabecera, $arrGuiaEntradaDetalle, $EID_Tipo_Documento_Guia, $EID_Serie_Documento_Guia, $EID_Numero_Documento_Guia, $EID_Tipo_Documento_Factura, $EID_Serie_Documento_Factura, $EID_Numero_Documento_Factura){
        $response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
		if ( ($EID_Tipo_Documento_Guia != $arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia'] || $EID_Serie_Documento_Guia != $arrGuiaEntradaCabecera['ID_Serie_Documento_Guia'] || $EID_Numero_Documento_Guia != $arrGuiaEntradaCabecera['ID_Numero_Documento_Guia']) && $arrGuiaEntradaCabecera['ID_Tipo_Operacion'] == '7' && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Documento = " . $arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia'] . " AND ID_Serie_Documento = '" . $arrGuiaEntradaCabecera['ID_Serie_Documento_Guia'] . "' AND ID_Numero_Documento = '" . $arrGuiaEntradaCabecera['ID_Numero_Documento_Guia'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if ( ($EID_Tipo_Documento_Factura != $arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura'] || $EID_Serie_Documento_Factura != $arrGuiaEntradaCabecera['ID_Serie_Documento_Factura'] || $EID_Numero_Documento_Factura != $arrGuiaEntradaCabecera['ID_Numero_Documento_Factura']) && $arrGuiaEntradaCabecera['ID_Tipo_Operacion'] == '0' && $this->db->query("SELECT COUNT(*) existe FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Documento = " . $arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura'] . " AND ID_Serie_Documento = '" . $arrGuiaEntradaCabecera['ID_Serie_Documento_Factura'] . "' AND ID_Numero_Documento = '" . $arrGuiaEntradaCabecera['ID_Numero_Documento_Factura'] . "' LIMIT 1")->row()->existe > 0){
			$response = array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else {
			$this->db->trans_begin();
			$Nu_Tipo_Operacion = $arrGuiaEntradaCabecera['ID_Tipo_Operacion'];
			unset($arrGuiaEntradaCabecera['ID_Tipo_Operacion']);
	            
		    $ID_Tipo_Asiento_Factura = $arrGuiaEntradaCabecera['ID_Tipo_Asiento_Factura'];
		    $ID_Tipo_Documento_Factura = $arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura'];
		    $ID_Serie_Documento_Factura = $arrGuiaEntradaCabecera['ID_Serie_Documento_Factura'];
		    $ID_Numero_Documento_Factura = $arrGuiaEntradaCabecera['ID_Numero_Documento_Factura'];
		    $Ss_Descuento = $arrGuiaEntradaCabecera['Ss_Descuento'];
		    $Po_Descuento = $arrGuiaEntradaCabecera['Po_Descuento'];
		    
		    unset($arrGuiaEntradaCabecera['ID_Tipo_Asiento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Tipo_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Serie_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['ID_Numero_Documento_Factura']);
		    unset($arrGuiaEntradaCabecera['Ss_Descuento']);
		    unset($arrGuiaEntradaCabecera['Po_Descuento']);
			
			if ($Nu_Tipo_Operacion === '7' || $Nu_Tipo_Operacion === '0') {//Guia de Remision ó Factura + Guia
				$ID_Guia_Cabecera = $this->db->query("SELECT ID_Guia_Cabecera FROM guia_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Guia_Cabecera = " . $where['ID_Guia_Cabecera'] . " LIMIT 1")->row()->ID_Guia_Cabecera;
				
		        $this->db->delete($this->table_guia_detalle, $where);
		        $this->db->delete($this->table, $where);
	        
	        	$arrGuiaEntradaCabecera['ID_Guia_Cabecera'] = $ID_Guia_Cabecera;
			    $arrGuiaEntradaCabecera['ID_Tipo_Asiento'] = $arrGuiaEntradaCabecera['ID_Tipo_Asiento_Guia'];;
			    $arrGuiaEntradaCabecera['ID_Tipo_Documento'] = $arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia'];
			    $arrGuiaEntradaCabecera['ID_Serie_Documento'] = $arrGuiaEntradaCabecera['ID_Serie_Documento_Guia'];
			    $arrGuiaEntradaCabecera['ID_Numero_Documento'] = $arrGuiaEntradaCabecera['ID_Numero_Documento_Guia'];
			    
			    unset($arrGuiaEntradaCabecera['ID_Tipo_Asiento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Tipo_Documento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Serie_Documento_Guia']);
			    unset($arrGuiaEntradaCabecera['ID_Numero_Documento_Guia']);
			    
				$this->db->insert($this->table, $arrGuiaEntradaCabecera);
				$Last_ID_Guia_Cabecera = $ID_Guia_Cabecera;
	            
				foreach ($arrGuiaEntradaDetalle as $row) {
					$guia_detalle[] = array(
						'ID_Empresa'		=> $this->user->ID_Empresa,
						'ID_Guia_Cabecera'	=> $Last_ID_Guia_Cabecera,
						'ID_Producto'		=> $this->security->xss_clean($row['ID_Producto']),
						'Qt_Producto'		=> round($this->security->xss_clean($row['Qt_Producto']), 6),
						'Ss_Precio'			=> round($this->security->xss_clean($row['Ss_Precio']), 6),
						'Ss_SubTotal' 		=> round($this->security->xss_clean($row['Qt_Producto']) * $this->security->xss_clean($row['Ss_Precio']), 2)
					);
				}
				$this->db->insert_batch($this->table_guia_detalle, $guia_detalle);
			}
			
			if ($Nu_Tipo_Operacion === '0') {//Factura Compra ó Factura + Guia
				if ($Nu_Tipo_Operacion === '0')
					$where = $whereFacturaCompra;
		        
			    $arrCompraCabecera = $arrGuiaEntradaCabecera;
			    $arrCompraDetalle = $arrGuiaEntradaDetalle;
			    
			    $arrCompraCabecera['ID_Tipo_Asiento'] = $ID_Tipo_Asiento_Factura;
			    $arrCompraCabecera['ID_Organizacion'] = 1;
			    $arrCompraCabecera['ID_Tipo_Documento'] = $ID_Tipo_Documento_Factura;
			    $arrCompraCabecera['ID_Serie_Documento'] = $ID_Serie_Documento_Factura;
			    $arrCompraCabecera['ID_Numero_Documento'] = $ID_Numero_Documento_Factura;
			    $arrCompraCabecera['ID_Medio_Pago'] = 1;//Efectivo
				$arrCompraCabecera['Fe_Vencimiento'] = $arrCompraCabecera['Fe_Emision'];
				$arrCompraCabecera['Fe_Periodo'] = $arrCompraCabecera['Fe_Emision'];
			    $arrCompraCabecera['Nu_Detraccion'] = '';
			    $arrCompraCabecera['Fe_Detraccion'] = '';
			    $arrCompraCabecera['Ss_Descuento'] = $Ss_Descuento;
			    $arrCompraCabecera['Po_Descuento'] = $Po_Descuento;
	
			    unset($arrCompraCabecera['ID_Tipo_Operacion']);
	
			    unset($arrCompraCabecera['ID_Guia_Cabecera']);
			    unset($arrCompraCabecera['ID_Tipo_Asiento_Guia']);
			    unset($arrCompraCabecera['ID_Tipo_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Serie_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Numero_Documento_Guia']);
			    unset($arrCompraCabecera['ID_Tipo_Movimiento']);
			    
			    $Fe_Year = ToYear($arrCompraCabecera['Fe_Emision']);
	    		$Fe_Month = ToMonth($arrCompraCabecera['Fe_Emision']);
			    if (!empty($where['ID_Documento_Cabecera'])) {
					if($this->db->query("SELECT COUNT(*) AS existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
						$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM documento_cabecera WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Documento_Cabecera = " . $where['ID_Documento_Cabecera'] . " LIMIT 1")->row()->Nu_Correlativo;
					} else {
						$sql_correlativo_libro_sunat = "
						INSERT INTO correlativo_tipo_asiento (
							ID_Empresa,
						    ID_Tipo_Asiento,
						    Fe_Year,
						    Fe_Month,
						    Nu_Correlativo
						) VALUES (
							" . $this->user->ID_Empresa . ",
							2,
							'" . $Fe_Year . "',
							'" . $Fe_Month . "',
							1
						);
						";
						$this->db->query($sql_correlativo_libro_sunat);
						$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
					}
			    } else {
			    	$arrCorrelativoPendiente = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento_pendiente WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' ORDER BY Nu_Correlativo DESC LIMIT 1")->result();
			    	if ( count($arrCorrelativoPendiente) > 0 ){
						$Nu_Correlativo = $arrCorrelativoPendiente[0]->Nu_Correlativo;
						
						$this->db->where('ID_Empresa', $this->user->ID_Empresa);
						$this->db->where('ID_Tipo_Asiento', 2);
						$this->db->where('Fe_Year', $Fe_Year);
						$this->db->where('Fe_Month', $Fe_Month);
						$this->db->where('Nu_Correlativo', $Nu_Correlativo);
				        $this->db->delete('correlativo_tipo_asiento_pendiente');
					} else {
				    	if($this->db->query("SELECT COUNT(*) existe FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->existe > 0){
							$sql_correlativo_libro_sunat = "
							UPDATE
								correlativo_tipo_asiento
							SET
								Nu_Correlativo = Nu_Correlativo + 1
							WHERE
								ID_Empresa			= " . $this->user->ID_Empresa . "
								AND ID_Tipo_Asiento	= 2
								AND Fe_Year 		= '" . $Fe_Year. "'
								AND Fe_Month		= '" . $Fe_Month . "'
							";
							$this->db->query($sql_correlativo_libro_sunat);
				    	} else {
							$sql_correlativo_libro_sunat = "
							INSERT INTO correlativo_tipo_asiento (
								ID_Empresa,
							    ID_Tipo_Asiento,
							    Fe_Year,
							    Fe_Month,
							    Nu_Correlativo
							) VALUES (
								" . $this->user->ID_Empresa . ",
								2,
								'" . $Fe_Year . "',
								'" . $Fe_Month . "',
								1
							);
							";
							$this->db->query($sql_correlativo_libro_sunat);
						}
						$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM correlativo_tipo_asiento WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND ID_Tipo_Asiento = 2 AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row()->Nu_Correlativo;
					}
				}
				
		        $this->db->delete('documento_detalle', $where);
		        $this->db->delete('documento_cabecera', $where);
		        
				$_arrCompraCabecera = array("Nu_Correlativo" => $Nu_Correlativo, "ID_Documento_Cabecera" => $where['ID_Documento_Cabecera']);
				$arrCompraCabecera_ = array_merge($arrCompraCabecera, $_arrCompraCabecera);
				$this->db->insert('documento_cabecera', $arrCompraCabecera_);
				$ID_Documento_Cabecera = $this->db->insert_id();
			    
				foreach ($arrCompraDetalle as $row) {
					$documento_detalle[] = array(
						'ID_Empresa'					=> $this->user->ID_Empresa,
						'ID_Documento_Cabecera'			=> $ID_Documento_Cabecera,
						'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
						'Qt_Producto'					=> round($this->security->xss_clean($row['Qt_Producto']), 6),
						'Ss_Precio'						=> round($this->security->xss_clean($row['Ss_Precio']), 6),
						'Ss_SubTotal' 					=> round($this->security->xss_clean($row['Ss_SubTotal']), 2),
						'Ss_Descuento' 				    => round($this->security->xss_clean($row['Ss_Descuento']), 2),
						'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento'])
					);
				}
				$this->db->insert_batch('documento_detalle', $documento_detalle);
			}
			
			if ($Nu_Tipo_Operacion === '0') {
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Documento_Cabecera', $where['ID_Documento_Cabecera']);
		        $this->db->delete('guia_enlace');
		        
				$table_guia_enlace = array(
					'ID_Empresa'			=> $this->user->ID_Empresa,
					'ID_Guia_Cabecera'	    => $Last_ID_Guia_Cabecera,
					'ID_Documento_Cabecera'	=> $ID_Documento_Cabecera,
				);
				$this->db->insert('guia_enlace', $table_guia_enlace);
				
				$Last_ID_Guia_Cabecera = $ID_Documento_Cabecera;
			}
		
	    	$this->db->trans_complete();
	        if ($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
	        } else {
	            $this->db->trans_commit();
		        $response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'Last_ID_Guia_Cabecera' => $Last_ID_Guia_Cabecera);
	        }
		}
        return $response;
    }
    
	public function anularGuiaEntrada($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al anular');
		$this->db->trans_begin();
        
        if ($Nu_Tipo_Operacion == 7){
        	if ($Nu_Descargar_Inventario == 1) {
		        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID;
		        $arrDetalle = $this->db->query($query)->result();
				foreach ($arrDetalle as $row) {
					if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
						$where = array('ID_Empresa' => $row->ID_Empresa, 'ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
						$Qt_Producto = $this->db->query("SELECT SUM(Qt_Producto) AS Qt_Producto FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
						$stock_producto = array(
							'ID_Empresa'		=> $row->ID_Empresa,
							'ID_Almacen'		=> $row->ID_Almacen,
							'ID_Producto'		=> $row->ID_Producto,
							'Qt_Producto'		=> ($Qt_Producto - round($row->Qt_Producto, 6)),
							'Ss_Costo_Promedio'	=> 0.00,
						);
						$this->db->update('stock_producto', $stock_producto, $where);
					}
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
			
			$this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete($this->table_guia_detalle);

	        $this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
			$data = array(
				'Nu_Estado' => 7,
				'Ss_Total' => 0.00,
			);
	        $this->db->update($this->table, $data);
        }
        
    	$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
        	$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro anulado');
        }
    	return $response;
	}
    
	public function eliminarGuiaEntrada($ID, $Nu_Tipo_Operacion, $Nu_Descargar_Inventario){
		$response = array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al eliminar');
		$this->db->trans_begin();
        
        if ($Nu_Tipo_Operacion == 7){
        	if ($Nu_Descargar_Inventario == 1){
		        $query = "SELECT * FROM movimiento_inventario WHERE ID_Guia_Cabecera = " . $ID;
		        $arrDetalle = $this->db->query($query)->result();
				foreach ($arrDetalle as $row) {
					if($this->db->query("SELECT COUNT(*) existe FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto . " LIMIT 1")->row()->existe > 0){
						$where = array('ID_Empresa' => $row->ID_Empresa, 'ID_Almacen' => $row->ID_Almacen, 'ID_Producto' => $row->ID_Producto);
						$Qt_Producto = $this->db->query("SELECT SUM(Qt_Producto) AS Qt_Producto FROM stock_producto WHERE ID_Empresa = " . $row->ID_Empresa . " AND ID_Almacen = " . $row->ID_Almacen . " AND ID_Producto = " . $row->ID_Producto)->row()->Qt_Producto;
						$stock_producto = array(
							'ID_Empresa'		=> $row->ID_Empresa,
							'ID_Almacen'		=> $row->ID_Almacen,
							'ID_Producto'		=> $row->ID_Producto,
							'Qt_Producto'		=> ($Qt_Producto - round($row->Qt_Producto, 6)),
							'Ss_Costo_Promedio'	=> 0.00,
						);
						$this->db->update('stock_producto', $stock_producto, $where);
					}
	        	}
				$this->db->where('ID_Empresa', $this->user->ID_Empresa);
				$this->db->where('ID_Guia_Cabecera', $ID);
		        $this->db->delete('movimiento_inventario');
        	}
	        
			$this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete($this->table_guia_detalle);

	        $this->db->where('ID_Empresa', $this->user->ID_Empresa);
			$this->db->where('ID_Guia_Cabecera', $ID);
	        $this->db->delete($this->table);
        }
        
    	$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
        	$response = array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
        }
    	return $response;
	}
}
