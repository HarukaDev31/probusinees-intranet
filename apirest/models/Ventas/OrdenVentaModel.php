<?php
class OrdenVentaModel extends CI_Model{
	var $table          				= 'documento_cabecera';
	var $table_documento_detalle		= 'documento_detalle';
	var $table_documento_enlace			= 'documento_enlace';
	var $table_tipo_documento			= 'tipo_documento';
	var $table_impuesto_cruce_documento	= 'impuesto_cruce_documento';
	var $table_entidad					= 'entidad';
	var $table_tipo_documento_identidad	= 'tipo_documento_identidad';
	var $table_moneda					= 'moneda';
	var $table_organizacion				= 'organizacion';
	var $table_tabla_dato				= 'tabla_dato';
	var $table_almacen				= 'almacen';
	
    var $column_order = array('');
    var $column_search = array('');
    var $order = array('');
    
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        if(!empty($this->input->post('Filtro_TiposDocumento')))
        	$this->db->where('VC.ID_Tipo_Documento', $this->input->post('Filtro_TiposDocumento'));
		
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Documento_Cabecera', $this->input->post('Filtro_NumeroDocumento'));
        
        if($this->input->post('Filtro_Estado') != '')
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if(!empty($this->input->post('Filtro_Contacto')))
        	$this->db->where('CONTAC.No_Entidad', $this->input->post('Filtro_Contacto'));

        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.No_Entidad', $this->input->post('Filtro_Entidad'));
			
        if( $this->input->post('filtro_vendedor') != 0 )
        	$this->db->where('VC.ID_Mesero', $this->input->post('filtro_vendedor'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
        $this->db->select('VC.ID_Documento_Cabecera, VC.ID_Empresa, VC.ID_Organizacion, ALMA.ID_Almacen, ALMA.No_Almacen, VC.ID_Tipo_Documento, TDOCU.No_Tipo_Documento, VC.Fe_Emision, CLI.ID_Entidad, CLI.No_Entidad, CONTAC.No_Entidad AS No_Contacto, VEND.No_Entidad AS No_Personal, CONTAC.Txt_Email_Entidad AS Txt_Email_Contacto, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario, VC.ID_Moneda')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_entidad . ' AS CONTAC', 'CONTAC.ID_Entidad = VC.ID_Contacto', 'left')
		->join($this->table_entidad . ' AS VEND', 'VEND.ID_Entidad = VC.ID_Mesero', 'left')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 1)
    	->where_in('VC.ID_Tipo_Documento', array(1,13));
					
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
		
        if(!empty($this->input->post('Filtro_NumeroDocumento')))
        	$this->db->where('VC.ID_Documento_Cabecera', $this->input->post('Filtro_NumeroDocumento'));
        
        if($this->input->post('Filtro_Estado') != '')
        	$this->db->where('VC.Nu_Estado', $this->input->post('Filtro_Estado'));
        
        if(!empty($this->input->post('Filtro_Contacto')))
        	$this->db->where('CONTAC.No_Entidad', $this->input->post('Filtro_Contacto'));

        if(!empty($this->input->post('Filtro_Entidad')))
        	$this->db->where('CLI.No_Entidad', $this->input->post('Filtro_Entidad'));
			
        if( $this->input->post('filtro_vendedor') != 0 )
        	$this->db->where('VC.ID_Mesero', $this->input->post('filtro_vendedor'));
        
        if($this->input->post('filtro_almacen') != '0')
			$this->db->where('VC.ID_Almacen', $this->input->post('filtro_almacen'));

    	$this->db->where("VC.Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
        
        $this->db->select('VC.ID_Documento_Cabecera, VC.ID_Empresa, VC.ID_Organizacion, ALMA.ID_Almacen, ALMA.No_Almacen, VC.ID_Tipo_Documento, TDOCU.No_Tipo_Documento, VC.Fe_Emision, CLI.ID_Entidad, CLI.No_Entidad, CONTAC.No_Entidad AS No_Contacto, VEND.No_Entidad AS No_Personal, CONTAC.Txt_Email_Entidad AS Txt_Email_Contacto, MONE.No_Signo, VC.Ss_Total, VC.Nu_Estado, TDOCU.Nu_Enlace, VC.Nu_Descargar_Inventario, VC.ID_Moneda')
		->from($this->table . ' AS VC')
		->join($this->table_almacen . ' AS ALMA', 'ALMA.ID_Almacen = VC.ID_Almacen', 'join')
		->join($this->table_tipo_documento . ' AS TDOCU', 'TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento', 'join')
		->join($this->table_entidad . ' AS CLI', 'CLI.ID_Entidad = VC.ID_Entidad', 'join')
		->join($this->table_entidad . ' AS CONTAC', 'CONTAC.ID_Entidad = VC.ID_Contacto', 'left')
		->join($this->table_entidad . ' AS VEND', 'VEND.ID_Entidad = VC.ID_Mesero', 'left')
		->join($this->table_moneda . ' AS MONE', 'MONE.ID_Moneda = VC.ID_Moneda', 'join')
		->where('VC.ID_Empresa', $this->empresa->ID_Empresa)
		->where('VC.ID_Organizacion', $this->empresa->ID_Organizacion)
    	->where('VC.ID_Tipo_Asiento', 1)
    	->where_in('VC.ID_Tipo_Documento', array(1,13));
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $query = "SELECT
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
ALMA.No_Logo_Almacen,
ALMA.Txt_FE_Ruta,
ALMA.Txt_FE_Token,
CONFI.Nu_Celular_Empresa,
CONFI.Nu_Telefono_Empresa,
CONFI.Txt_Email_Empresa,
CONFI.Nu_Logo_Empresa_Ticket,
EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
EMP.Txt_Direccion_Empresa,
ALMA.Txt_Direccion_Almacen,
VC.ID_Empresa,
VC.ID_Organizacion,
VC.ID_Almacen,
VC.ID_Documento_Cabecera,
VC.Nu_Estado,
VC.ID_Numero_Documento,
CLI.ID_Tipo_Documento_Identidad,
CLI.ID_Entidad,
CLI.No_Entidad,
CLI.Nu_Documento_Identidad,
CLI.Txt_Direccion_Entidad,
CLI.Nu_Telefono_Entidad,
CLI.Nu_Celular_Entidad,
CLI.Txt_Email_Entidad,
CLI.Nu_Dias_Credito,
VC.ID_Tipo_Documento,
TDOCU.No_Tipo_Documento,
TDOCU.Nu_Impuesto,
TDOCU.Nu_Enlace,
VC.Fe_Emision,
VC.Fe_Vencimiento,
VC.Fe_Periodo,
VC.ID_Moneda,
VC.ID_Medio_Pago,
VC.Nu_Descargar_Inventario,
VC.ID_Lista_Precio_Cabecera,
CONTAC.ID_Entidad AS ID_Contacto,
CONTAC.ID_Tipo_Documento_Identidad AS ID_Tipo_Documento_Identidad_Contacto,
CONTAC.Nu_Documento_Identidad AS Nu_Documento_Identidad_Contacto,
CONTAC.No_Entidad AS No_Contacto,
CONTAC.Txt_Email_Entidad AS Txt_Email_Contacto,
CONTAC.Nu_Celular_Entidad AS Nu_Celular_Contacto,
CONTAC.Nu_Telefono_Entidad AS Nu_Telefono_Contacto,
VD.ID_Producto,
PRO.Nu_Codigo_Barra,
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
UM.Nu_Sunat_Codigo AS Nu_Sunat_Codigo_UM,
UM.No_Unidad_Medida, 
ICDOCU.Ss_Impuesto,
MP.Nu_Tipo AS Nu_Tipo_Forma_Pago,
IMP.Nu_Tipo_Impuesto,
IMP.No_Impuesto_Breve,
ICDOCU.Po_Impuesto,
VC.Txt_Garantia,
VC.Txt_Glosa,
VC.Ss_Descuento AS Ss_Descuento,
VC.Ss_Total AS Ss_Total,
MONE.No_Moneda,
MONE.No_Signo,
VC.Po_Descuento,
VC.ID_Mesero,
VC.ID_Comision,
MP.No_Medio_Pago,
VC.No_Formato_PDF,
TDOCUIDEN.No_Tipo_Documento_Identidad_Breve,
PRO.Ss_Icbper AS Ss_Icbper_Item,
VD.Ss_Icbper,
EMPLE.No_Entidad AS No_Vendedor,
EMPLE.Nu_Celular_Entidad AS Nu_Celular_Vendedor,
EMPLE.Txt_Email_Entidad AS Txt_Email_Vendedor,
DIS.No_Distrito,
PR.No_Provincia,
DP.No_Departamento,
VD.Txt_Nota AS Txt_Nota_Item,
LAB.No_Laboratorio_Breve,
UM.Nu_Sunat_Codigo AS nu_codigo_unidad_medida_sunat,
TC.Ss_Venta_Oficial AS Ss_Tipo_Cambio,
VC.Ss_Descuento_Impuesto,
PRO.Nu_Activar_Precio_x_Mayor
FROM
" . $this->table . " AS VC
JOIN empresa AS EMP ON(EMP.ID_Empresa = VC.ID_Empresa)
JOIN configuracion AS CONFI ON(CONFI.ID_Empresa = EMP.ID_Empresa)
JOIN organizacion AS ORG ON(VC.ID_Organizacion = ORG.ID_Organizacion)
JOIN almacen AS ALMA ON(VC.ID_Almacen = ALMA.ID_Almacen)
LEFT JOIN distrito AS DIS ON(DIS.ID_Distrito = EMP.ID_Distrito)
LEFT JOIN provincia AS PR ON(PR.ID_Provincia = EMP.ID_Provincia)
LEFT JOIN departamento AS DP ON(DP.ID_Departamento = EMP.ID_Departamento)
JOIN " . $this->table_documento_detalle . " AS VD ON(VC.ID_Documento_Cabecera = VD.ID_Documento_Cabecera)
JOIN " . $this->table_entidad . " AS CLI ON(CLI.ID_Entidad = VC.ID_Entidad)
JOIN tipo_documento_identidad AS TDOCUIDEN ON(CLI.ID_Tipo_Documento_Identidad = TDOCUIDEN.ID_Tipo_Documento_Identidad)
LEFT JOIN " . $this->table_entidad . " AS CONTAC ON(CONTAC.ID_Entidad = VC.ID_Contacto)
LEFT JOIN " . $this->table_entidad . " AS EMPLE ON(EMPLE.ID_Entidad = VC.ID_Mesero)
JOIN producto AS PRO ON(PRO.ID_Producto = VD.ID_Producto)
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
JOIN " . $this->table_tipo_documento . " AS TDOCU ON(TDOCU.ID_Tipo_Documento = VC.ID_Tipo_Documento)
JOIN " . $this->table_impuesto_cruce_documento . " AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
JOIN medio_pago AS MP ON(MP.ID_Medio_Pago = VC.ID_Medio_Pago)
JOIN moneda AS MONE ON(MONE.ID_Moneda = VC.ID_Moneda)
LEFT JOIN tasa_cambio AS TC ON(TC.ID_Empresa = VC.ID_Empresa AND TC.ID_Moneda = VC.ID_Moneda AND TC.Fe_Ingreso = VC.Fe_Emision)
LEFT JOIN laboratorio AS LAB ON (LAB.ID_Laboratorio = PRO.ID_Laboratorio)
WHERE VC.ID_Documento_Cabecera = " . $ID;
        return $this->db->query($query)->result();
    }
    
    public function agregarVenta($arrOrdenCabecera, $arrOrdenDetalle, $arrClienteNuevo, $arrContactoNuevo){		
		$this->db->trans_begin();
		
		$iAddCliente = $arrOrdenCabecera['addCliente'];//0=existe y 1 = nuevo
		$iAddContacto = $arrOrdenCabecera['addContacto'];//0=existe y 1 = nuevo
		unset($arrOrdenCabecera['addCliente']);
		unset($arrOrdenCabecera['addContacto']);

		if ($iAddCliente == 1 && is_array($arrClienteNuevo)){
		    unset($arrOrdenCabecera['ID_Entidad']);
		    //Si no existe el cliente, lo crearemos
		    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad = '" . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . "' LIMIT 1")->row()->existe == 0){
				$arrCliente = array(
	                'ID_Empresa'					=> $this->empresa->ID_Empresa,
	                'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 0,
	                'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrClienteNuevo['Txt_Email_Entidad'],
	                'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
	                'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
	                'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
	                'Nu_Estado' 					=> 1,
	            );
	    		$this->db->insert('entidad', $arrCliente);
	    		$Last_ID_Entidad = $this->db->insert_id();
		    } else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El cliente ' . $arrClienteNuevo['Nu_Documento_Identidad'] . ': ' . limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad']) . ' ya se encuentra creado, seleccionar Existente');
			}
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Entidad" => $Last_ID_Entidad));
		}
		
		if ($iAddContacto == 1 && is_array($arrContactoNuevo)){
		    unset($arrOrdenCabecera['ID_Contacto']);
		    //Si no existe el contacto, lo crearemos
		    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 8 AND ID_Tipo_Documento_Identidad = " . $arrContactoNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrContactoNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad='".limpiarCaracteresEspeciales($arrContactoNuevo['No_Entidad'])."' LIMIT 1")->row()->existe == 0){
				$arrContacto = array(
	                'ID_Empresa'					=> $this->empresa->ID_Empresa,
	                'ID_Organizacion'				=> $this->empresa->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 8,//Contacto
	                'ID_Tipo_Documento_Identidad'	=> $arrContactoNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrContactoNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrContactoNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrContactoNuevo['Txt_Email_Entidad'],
	                'Nu_Telefono_Entidad'			=> $arrContactoNuevo['Nu_Telefono_Entidad'],
	                'Nu_Celular_Entidad'			=> $arrContactoNuevo['Nu_Celular_Entidad'],
	                'Nu_Estado' 					=> 1,
	            );
	    		$this->db->insert('entidad', $arrContacto);
	    		$Last_ID_Contacto = $this->db->insert_id();
		    } else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El contacto ya se encuentra creado, seleccionar Existente');
			}
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Contacto" => $Last_ID_Contacto));
		}
		
		$this->db->insert($this->table, $arrOrdenCabecera);
		$Last_ID_Documento_Cabecera = $this->db->insert_id();
		
		foreach ($arrOrdenDetalle as $row) {				
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
				'ID_Empresa'					=> $this->user->ID_Empresa,
				'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
				'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
				'Qt_Producto'					=> $this->security->xss_clean($row['Qt_Producto']),
				'Ss_Precio'						=> $this->security->xss_clean($row['Ss_Precio']),
				'Ss_SubTotal' 					=> $this->security->xss_clean($row['Ss_SubTotal']),
				'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
				'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
				'Po_Descuento' => $row['Ss_Descuento'],
				'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
				'Ss_Impuesto' 					=> $this->security->xss_clean($row['Ss_Impuesto']),
				'Ss_Total' 						=> round($this->security->xss_clean($row['Ss_Total']), 2),
				'Ss_Icbper' => $row['fIcbperItem'],
				'Txt_Nota' => $row['Txt_Nota'],
				'Fe_Emision' => $arrOrdenCabecera['Fe_Emision']
			);
		}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
			
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
        } else {
            $this->db->trans_commit();
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'Last_ID_Documento_Cabecera' => $Last_ID_Documento_Cabecera);
        }
    }
    
    public function actualizarVenta($where, $arrOrdenCabecera, $arrOrdenDetalle, $arrClienteNuevo, $arrContactoNuevo){
		$this->db->trans_begin();
		
		$this->db->query("SET FOREIGN_KEY_CHECKS=OFF;");

		$arrDataModificar = $this->db->query("SELECT ID_Organizacion, ID_Almacen, ID_Documento_Cabecera, Nu_Descargar_Inventario FROM documento_cabecera WHERE ID_Documento_Cabecera=" . $where['ID_Documento_Cabecera'] . " LIMIT 1")->result();
	
		$ID_Documento_Cabecera = $arrDataModificar[0]->ID_Documento_Cabecera;
		$ID_Almacen = $arrDataModificar[0]->ID_Almacen;
		$Nu_Descargar_Inventario = $arrDataModificar[0]->Nu_Descargar_Inventario;

		$this->db->delete($this->table_documento_detalle, $where);
		
        $this->db->delete($this->table, $where);
		
		$iAddCliente = $arrOrdenCabecera['addCliente'];//0=existe y 1 = nuevo
		$iAddContacto = $arrOrdenCabecera['addContacto'];//0=existe y 1 = nuevo
		unset($arrOrdenCabecera['addCliente']);
		unset($arrOrdenCabecera['addContacto']);

		if ($iAddCliente == 1 && is_array($arrClienteNuevo)){
		    unset($arrOrdenCabecera['ID_Entidad']);
		    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrClienteNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrClienteNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad='".limpiarCaracteresEspeciales($arrClienteNuevo['No_Entidad'])."' LIMIT 1")->row()->existe == 0){
				$arrCliente = array(
	                'ID_Empresa'					=> $this->user->ID_Empresa,
	                'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 0,
	                'ID_Tipo_Documento_Identidad'	=> $arrClienteNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrClienteNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrClienteNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrClienteNuevo['Txt_Email_Entidad'],
	                'Txt_Direccion_Entidad' 		=> $arrClienteNuevo['Txt_Direccion_Entidad'],
	                'Nu_Telefono_Entidad'			=> $arrClienteNuevo['Nu_Telefono_Entidad'],
	                'Nu_Celular_Entidad'			=> $arrClienteNuevo['Nu_Celular_Entidad'],
	                'Nu_Estado' 					=> 1,
	            );
	    		$this->db->insert('entidad', $arrCliente);
	    		$Last_ID_Entidad = $this->db->insert_id();
		    } else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El cliente ya se encuentra creado, seleccionar Existente');
			}
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Entidad" => $Last_ID_Entidad));
		}
		
		if ($iAddContacto == 1 && is_array($arrContactoNuevo)){
			echo "asdd";
		    unset($arrOrdenCabecera['ID_Contacto']);
		    //Si no existe el cliente, lo crearemos
		    if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Nu_Tipo_Entidad = 8 AND ID_Tipo_Documento_Identidad = " . $arrContactoNuevo['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrContactoNuevo['Nu_Documento_Identidad'] . "' AND No_Entidad='".limpiarCaracteresEspeciales($arrContactoNuevo['No_Entidad'])."' LIMIT 1")->row()->existe == 0){
				$arrContacto = array(
	                'ID_Empresa'					=> $this->user->ID_Empresa,
	                'ID_Organizacion'				=> $arrDataModificar[0]->ID_Organizacion,
	                'Nu_Tipo_Entidad'				=> 8,//Contacto
	                'ID_Tipo_Documento_Identidad'	=> $arrContactoNuevo['ID_Tipo_Documento_Identidad'],
	                'Nu_Documento_Identidad'		=> $arrContactoNuevo['Nu_Documento_Identidad'],
	                'No_Entidad'					=> $arrContactoNuevo['No_Entidad'],
	                'Txt_Email_Entidad' 			=> $arrContactoNuevo['Txt_Email_Entidad'],
	                'Nu_Telefono_Entidad'			=> $arrContactoNuevo['Nu_Telefono_Entidad'],
	                'Nu_Celular_Entidad'			=> $arrContactoNuevo['Nu_Celular_Entidad'],
	                'Nu_Estado' 					=> 1,
	            );
	    		$this->db->insert('entidad', $arrContacto);
	    		$Last_ID_Contacto = $this->db->insert_id();
		    } else {
				$this->db->trans_rollback();
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'El contacto ya se encuentra creado, seleccionar Existente');
			}
    		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Contacto" => $Last_ID_Contacto));
		}
		
		$arrOrdenCabecera['ID_Almacen'] = $ID_Almacen;
		$arrOrdenCabecera = array_merge($arrOrdenCabecera, array("ID_Documento_Cabecera" => $arrDataModificar[0]->ID_Documento_Cabecera));
		$this->db->insert($this->table, $arrOrdenCabecera);
		$Last_ID_Documento_Cabecera = $this->db->insert_id();
		
		foreach ($arrOrdenDetalle as $row) {
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
				'ID_Empresa'					=> $this->user->ID_Empresa,
				'ID_Documento_Cabecera'			=> $Last_ID_Documento_Cabecera,
				'ID_Producto'					=> $this->security->xss_clean($row['ID_Producto']),
				'Qt_Producto'					=> $this->security->xss_clean($row['Qt_Producto']),
				'Ss_Precio'						=> $this->security->xss_clean($row['Ss_Precio']),
				'Ss_SubTotal' 					=> $this->security->xss_clean($row['Ss_SubTotal']),
				'Ss_Descuento' => $row['fDescuentoSinImpuestosItem'],
				'Ss_Descuento_Impuesto' => $row['fDescuentoImpuestosItem'],
				'Po_Descuento' => $row['Ss_Descuento'],
				'ID_Impuesto_Cruce_Documento'	=> $this->security->xss_clean($row['ID_Impuesto_Cruce_Documento']),
				'Ss_Impuesto' 					=> $this->security->xss_clean($row['Ss_Impuesto']),
				'Ss_Total' 						=> round($this->security->xss_clean($row['Ss_Total']), 2),
				'Ss_Icbper' => $row['fIcbperItem'],
				'Txt_Nota' => $row['Txt_Nota'],
				'Fe_Emision' => $arrOrdenCabecera['Fe_Emision']
			);
		}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
		
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
        } else {
			$this->db->query("SET FOREIGN_KEY_CHECKS=ON;");
            $this->db->trans_commit();
	        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado', 'Last_ID_Documento_Cabecera' => "$Last_ID_Documento_Cabecera");
        }
    }
    
	public function eliminarOrdenVenta($ID, $Nu_Descargar_Inventario){
		if($this->db->query("SELECT COUNT(*) AS existe FROM orden_seguimiento WHERE ID_Documento_Cabecera = " . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La orden de venta tiene seguimientos');
		}else{
			$this->db->trans_begin();
			
			$this->db->where('ID_Documento_Cabecera', $ID);
			$this->db->delete($this->table_documento_detalle);
						
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
	}
    
	public function estadoOrdenVenta($ID, $Nu_Descargar_Inventario, $Nu_Estado){
		$this->db->trans_begin();
        
        $where_orden_venta = array('ID_Documento_Cabecera' => $ID);
        $arrData = array( 'Nu_Estado' => $Nu_Estado );
		$this->db->update('documento_cabecera', $arrData, $where_orden_venta);
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
	}
    
	public function duplicarOrdenVenta($ID){
		$this->db->trans_begin();
        
        $query_cabecera = " SELECT
ID_Empresa,
ID_Organizacion,
ID_Almacen,
ID_Entidad,
ID_Contacto,
ID_Tipo_Asiento,
ID_Tipo_Documento,
ID_Matricula_Empleado,
Fe_Emision,
ID_Medio_Pago,
ID_Rubro,
ID_Moneda,
Fe_Vencimiento,
Fe_Periodo,
Nu_Correlativo,
Nu_Descargar_Inventario,
ID_Lista_Precio_Cabecera,
Txt_Glosa,
Ss_Descuento,
Ss_Total,
Ss_Total_Saldo,
Ss_Percepcion,
Fe_Detraccion,
Nu_Detraccion,
Nu_Estado,
Txt_Garantia,
ID_Mesero,
ID_Comision,
No_Formato_PDF
FROM
documento_cabecera
WHERE
ID_Documento_Cabecera = " . $ID . " LIMIT 1";
		$arrCabecera = $this->db->query($query_cabecera)->result();
		
		foreach ($arrCabecera as $row) {
			$dEmision = $row->Fe_Emision;
			$documento_cabecera = array(
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Organizacion' => $row->ID_Organizacion,
				'ID_Almacen' => $row->ID_Almacen,
				'ID_Entidad' => $row->ID_Entidad,
				'ID_Contacto' => $row->ID_Contacto,
				'ID_Tipo_Asiento' => $row->ID_Tipo_Asiento,
				'ID_Tipo_Documento' => $row->ID_Tipo_Documento,
				'ID_Matricula_Empleado' => $row->ID_Matricula_Empleado,
				'Fe_Emision' => $row->Fe_Emision,
				'ID_Medio_Pago' => $row->ID_Medio_Pago,
				'ID_Rubro' => $row->ID_Rubro,
				'ID_Moneda' => $row->ID_Moneda,
				'Fe_Vencimiento' => $row->Fe_Vencimiento,
				'Fe_Periodo' => $row->Fe_Periodo,
				'Nu_Correlativo' => $row->Nu_Correlativo,
				'Nu_Descargar_Inventario' => $row->Nu_Descargar_Inventario,
				'ID_Lista_Precio_Cabecera' => $row->ID_Lista_Precio_Cabecera,
				'Txt_Glosa' => $row->Txt_Glosa,
				'Ss_Descuento' => $row->Ss_Descuento,
				'Ss_Total' => $row->Ss_Total,
				'Ss_Total_Saldo' => $row->Ss_Total_Saldo,
				'Ss_Percepcion' => $row->Ss_Percepcion,
				'Fe_Detraccion' => $row->Fe_Detraccion,
				'Nu_Detraccion' => $row->Nu_Detraccion,
				'Nu_Estado' => 5,
				'Txt_Garantia' => $row->Txt_Garantia,
				'ID_Mesero' => $row->ID_Mesero,
				'ID_Comision' => $row->ID_Comision,
				'No_Formato_PDF' => $row->No_Formato_PDF,
			);
    	}
    	
		$this->db->insert($this->table, $documento_cabecera);
		$ID_Documento_Cabecera = $this->db->insert_id();
        
        $query_detalle = " SELECT
ID_Empresa,
ID_Producto,
Qt_Producto,
Ss_Precio,
Ss_Descuento,
Ss_Descuento_Impuesto,
Po_Descuento,
Ss_SubTotal,
ID_Impuesto_Cruce_Documento,
Ss_Impuesto,
Ss_Total,
Txt_Nota
FROM
documento_detalle
WHERE
ID_Documento_Cabecera = " . $ID;
		$arrDetalle = $this->db->query($query_detalle)->result();
		
		foreach ($arrDetalle as $row) {
			$documento_detalle[] = array(
				'ID_Empresa' => $row->ID_Empresa,
				'ID_Documento_Cabecera' => $ID_Documento_Cabecera,
				'ID_Producto' => $row->ID_Producto,
				'Qt_Producto' => $row->Qt_Producto,
				'Ss_Precio' => $row->Ss_Precio,
				'Ss_Descuento' => $row->Ss_Descuento,
				'Ss_Descuento_Impuesto'	=> $row->Ss_Descuento_Impuesto,
				'Po_Descuento' => $row->Po_Descuento,
				'Ss_SubTotal' => $row->Ss_SubTotal,
				'ID_Impuesto_Cruce_Documento' => $row->ID_Impuesto_Cruce_Documento,
				'Ss_Impuesto' => $row->Ss_Impuesto,
				'Ss_Total' => $row->Ss_Total,
				'Txt_Nota' => $row->Txt_Nota,
				'Fe_Emision' => $dEmision
			);
    	}
		$this->db->insert_batch($this->table_documento_detalle, $documento_detalle);
 
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al duplicar orden de venta');
        } else {
			$this->db->trans_commit();
        	return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro agregado');
        }
	}
}
