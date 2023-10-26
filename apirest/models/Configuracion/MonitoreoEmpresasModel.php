<?php
class MonitoreoEmpresasModel extends CI_Model{
	var $table                          = 'empresa';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_distrito                 = 'distrito';
	var $table_documento_cabecera       = 'documento_cabecera';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
    var $table_subdominio_tienda_virtual = 'subdominio_tienda_virtual';
	var $table_configuracion = 'configuracion';
    var $table_pais = 'pais';
	
    var $column_order = array('Fe_Registro', 'No_Pais', 'No_Empresa', null, null);
    var $column_search = array();
    var $order = array('empresa.Fe_Registro' => 'desc');
	
    //private $path_cdt_prueba_sunat = '../librerias.laesystems.com/apirest/libraries/sunat_facturador/certificado_digital/';//localhost
    //ruta_localhost para logistica > reglas de logistica > almacen cambiar por http://localhost/librerias.laesystems.com/SunatFacturador/cpe
    private $path_cdt_prueba_sunat = '../librerias/apirest/libraries/sunat_facturador/certificado_digital/';//cloud

	public function __construct(){
		parent::__construct();
        $this->load->database();
	}

    public function getDocumentosVentaXEstado($arrParams){
        $where_nu_estado = '';
        if ($arrParams['sTiposEstado'] == 'pendiente')
            $where_nu_estado = "AND Nu_Estado IN(6,7)";
        else if ($arrParams['sTiposEstado'] == 'error')
            $where_nu_estado = "AND Nu_Estado IN(9,11)";
        $query ="SELECT COUNT(*) AS cantidad FROM documento_cabecera WHERE ID_Empresa = " . $arrParams['ID_Empresa'] . " AND ID_Tipo_Asiento=1 AND ID_Tipo_Documento IN(3,4,5,6) " . $where_nu_estado;
        return $this->db->query($query)->row()->cantidad;
    }

    public function getVerificarMedioPagoTiendaVirtual($arrParams){
        $query ="SELECT COUNT(*) AS existe FROM medio_pago WHERE ID_Empresa = " . $arrParams['ID_Empresa'] . " AND No_Medio_Pago = 'YAPE' LIMIT 1";
        return $this->db->query($query)->row()->existe;
    }

    public function getProgresoTienda($arrParams){
        $query ="SELECT COUNT(*) AS existe FROM tour_tienda_virtual WHERE ID_Empresa = " . $arrParams['ID_Empresa'] . " AND Nu_Estado_Proceso=1";
        return $this->db->query($query)->row()->existe;
    }

    public function getVerificarUnPedidoTiendaVirtual($arrParams){
        $query ="SELECT 1 AS existe FROM pedido_cabecera WHERE ID_Empresa = " . $arrParams['ID_Empresa'] . " LIMIT 1";
        $objPedidoTienda = $this->db->query($query)->row();
        $iCantidadPedido = 0;
        if(is_object($objPedidoTienda)){
            $iCantidadPedido = $objPedidoTienda->existe;
        }
        return $iCantidadPedido;
    }

    public function getVerificarActivacionTiendaVirtual($arrParams){
        $query ="SELECT COUNT(*) AS existe FROM subdominio_tienda_virtual WHERE ID_Empresa = " . $arrParams['ID_Empresa'] . " LIMIT 1";
        return $this->db->query($query)->row()->existe;
    }

    public function getCDREmpresa($arrParams){
		//Lo que pasa que nubefact solo devuelve los cdr de factura, y nc y nb que sean F
		$where_serie_documento = ($arrParams['Nu_Tipo_Proveedor_FE'] == 1 ? "AND SUBSTR(VC.ID_Serie_Documento, 1, 1) = 'F'" : "");
        $in_tipo_documento = ($arrParams['Nu_Tipo_Proveedor_FE'] == 1 ? '(3,5,6)' : '(3,4,5,6)');

        $query ="SELECT COUNT(*) AS cantidad_cdr
FROM
documento_cabecera AS VC
JOIN empresa AS EMP ON(VC.ID_Empresa = EMP.ID_Empresa)
WHERE
EMP.Nu_Tipo_Proveedor_FE = " . $arrParams['Nu_Tipo_Proveedor_FE'] . "
AND VC.ID_Empresa = " . $arrParams['ID_Empresa'] . "
AND ID_Tipo_Asiento=1
AND ID_Tipo_Documento IN". $in_tipo_documento ."
AND VC.Nu_Estado=8
AND (Txt_Url_CDR = '' OR Txt_Url_CDR = NULL OR Txt_Url_CDR IS NULL)
" . $where_serie_documento;
        return $this->db->query($query)->row()->cantidad_cdr;
    }

	public function _get_datatables_query(){
        if($this->input->post('Filtro_Tipo_Sistema') != '0')
            $this->db->where('empresa.Nu_Tipo_Proveedor_FE', $this->input->post('Filtro_Tipo_Sistema'));

        if( $this->input->post('Filtro_Estado') != '' )
            $this->db->where('empresa.Nu_Estado', $this->input->post('Filtro_Estado'));
            
        if( $this->input->post('estado_proveedor') != '' )
            $this->db->where('empresa.Nu_Proveedor_Dropshipping', $this->input->post('estado_proveedor'));
            
        if( $this->input->post('Filtro_Pais') != '0' )
            $this->db->where('PA.ID_Pais', $this->input->post('Filtro_Pais'));

        $this->db->select('PA.No_Pais, DTV.No_Dominio_Tienda_Virtual, DTV.No_Subdominio_Tienda_Virtual, DTV.Nu_Tipo_Tienda, empresa.ID_Empresa, empresa.Fe_Registro, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Empresa, Nu_Activar_Guia_Electronica, Txt_Direccion_Empresa, Nu_MultiAlmacen, empresa.Nu_Estado, Nu_Tipo_Proveedor_FE, empresa.Nu_Lae_Gestion, empresa.Nu_Tipo_Plan_Lae_Gestion, Nu_Vendedor_Dropshipping, Nu_Proveedor_Dropshipping, Nu_Tienda_Virtual_Propia, Ss_Saldo_Acumulado_Billetera, Ss_Deposito_Pago_Billetera')
        ->from($this->table)
        ->join($this->table_pais . ' AS PA', 'PA.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
        ->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
        ->join($this->table_subdominio_tienda_virtual . ' AS DTV', 'DTV.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        //->where('empresa.ID_Empresa != ', 1);
            
        if( !empty($this->input->post('Global_Filter')) && strlen($this->input->post('Global_Filter')) > 0) {
            if ($this->input->post('Filtros_Empresas') == 'Empresa' ){
                $this->db->like('No_Empresa', $this->input->post('Global_Filter'));
            }
        
            if( $this->input->post('Filtros_Empresas') == 'RUC' ){
                $this->db->like('Nu_Documento_Identidad', $this->input->post('Global_Filter'));
            }
        }

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
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
        return 0;
    }
    
    public function get_by_id($ID){
        $this->db->from($this->table);
        $this->db->where('ID_Empresa',$ID);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function agregarEmpresa($data){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table . " WHERE Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		}else{
			if ( $this->db->insert($this->table, $data) > 0 )
				return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado');
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
    
    public function actualizarEmpresa($where, $data, $ENu_Documento_Identidad){
		if( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if ( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Empresa=" . $where['ID_Empresa'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La empresa tiene movimiento(s)');
        } else {
		    if ( $this->db->update($this->table, $data, $where) > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarEmpresa($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Empresa=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La empresa tiene movimiento(s)');
		} else {
			$this->db->where('ID_Empresa', $ID);
            $this->db->delete('producto');

			$this->db->where('ID_Empresa', $ID);
            $this->db->delete('familia');

			$this->db->where('ID_Empresa', $ID);
            $this->db->delete('unidad_medida');

			$this->db->where('ID_Empresa', $ID);
            $this->db->delete('configuracion');

			$this->db->where('ID_Empresa', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}
    
	public function configuracionAutomaticaOpciones($arrPost){
        /*
        iTipoProveedorFE:
        1 = Nubefact
        2 = Sunat
        3 = Sin facturacion electronica
        */
        if ( $arrPost['iEstadoEmpresa'] == 0 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'La empresa debe de estar ACTIVA.');
        } else {
            $this->db->trans_begin();
            
            // TABLE - Organizacion
            $sql = "INSERT INTO organizacion(ID_Empresa, No_Organizacion, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", 'Principal', 1)";
            $this->db->query($sql);
			$ID_Organizacion = $this->db->insert_id();

            if ($arrPost['iTipoProveedorFE']==2) {
                //Crear acceso a API de Sunat Facturador automaticamente
                $arrParamsApi = array('sNombreServicio' => 'SunatFacturador');
                $arrResponseApiSunat = $this->crearAccesoAutomaticoApi($arrParamsApi);
                if ($arrResponseApiSunat['success'] == false){
                    $this->db->trans_rollback();
                    return array('sStatus' => 'danger', 'sMessage' => 'Problemas al crear api automaticamente ' . $arrResponseApiSunat['msg']);
                }
                $arrPost['sTokenFE'] = $arrResponseApiSunat["data"]["token"];
            }

            // TABLE - almacén
            $sql = "INSERT INTO almacen
(ID_Organizacion, No_Almacen, Nu_Codigo_Establecimiento_Sunat, Txt_Direccion_Almacen, ID_Pais, ID_Departamento, ID_Provincia, ID_Distrito, Nu_Estado_Pago_Sistema, Nu_Estado, Txt_Fe_Ruta, Txt_Fe_Token)
VALUES
(" . $ID_Organizacion . ", 'Principal', '0000', '" . $arrPost['sDireccionEmpresa'] . "', 1, 1, 1, 1, 1, 1, '" . $arrPost['sUrlFE'] . "', '" . $arrPost['sTokenFE'] . "')";
            $this->db->query($sql);
			$ID_Almacen = $this->db->insert_id();

            $Nu_ID_Tipo_Documento_Venta_Predeterminado = ($arrPost['iTipoProveedorFE'] != '3' ? '4' : '2'); //4=Boleta o 2 = Nota de venta

            // TABLE - configuracion verificado
            $sql = "INSERT INTO configuracion(
ID_Empresa,
Fe_Inicio_Sistema,
Nu_Enviar_Sunat_Automatic,
Nu_Dia_Limite_Fecha_Vencimiento,
Nu_Width_Logo_Ticket,
Nu_Height_Logo_Ticket,
Nu_Imprimir_Liquidacion_Caja,
Nu_Tipo_Rubro_Empresa,
Nu_Verificar_Autorizacion_Venta,
Nu_Activar_Descuento_Punto_Venta,
Nu_Validar_Stock,
Nu_Estado,
Txt_Token,
Txt_Email_Empresa,
No_Dominio_Empresa,
Ss_Total_Pago_Cliente_Servicio,
Nu_ID_Tipo_Documento_Venta_Predeterminado,
Nu_Tipo_Lenguaje_Impresion_Pos
) VALUES (
" . $arrPost['iIdEmpresa'] . ",
'" . dateNow('fecha') . "',
1,
0,
260,
70,
0,
" . $arrPost['iTipoRubroEmpresa'] . ",
0,
0,
0,
1,
'emanQmQl8mPqelSfSPA6jSqo5Gl5c5Fsfdcoeh1m',
'" . $arrPost['sEmailUsuario'] . "',
'',
" . $arrPost['fPagoClienteServicio'] . ",
" . $Nu_ID_Tipo_Documento_Venta_Predeterminado . ",
2
)";
            $this->db->query($sql);
			$ID_Configuracion = $this->db->insert_id();

            // TABLE - grupo de impuesto - IGV
            $sSunatCodigoXProveedor = ($arrPost['iTipoProveedorFE'] == '1' ? '1' : '10');// LA = Sunat
            $sql = "INSERT INTO impuesto(ID_Empresa, No_Impuesto, No_Impuesto_Breve, Nu_Sunat_Codigo, Nu_Tipo_Impuesto, Nu_Valor_FE) VALUES (" . $arrPost['iIdEmpresa'] . ", 'Gravado - Operación Onerosa', 'IGV', '10', '1', '" . $sSunatCodigoXProveedor . "')";
            $this->db->query($sql);
			$ID_Impuesto = $this->db->insert_id();

            // TABLE - valor de impuesto
            $sql = "INSERT INTO impuesto_cruce_documento(ID_Impuesto, Ss_Impuesto, Po_Impuesto, Nu_Estado) VALUES (" . $ID_Impuesto . ", 1.18, '18', 1)";
            $this->db->query($sql);

            // TABLE - moneda
            $sql = "INSERT INTO moneda(ID_Empresa, No_Moneda, No_Signo, Nu_Sunat_Codigo, Nu_Valor_FE, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", 'Soles', 'S/', 'PEN', 1, 1)";
            $this->db->query($sql);

            // TABLE - medio pago
            // 0 = SI y 1 = NO Dineo Caja PV
            $sql = "INSERT INTO medio_pago(ID_Empresa, No_Medio_Pago, Txt_Medio_Pago, No_Codigo_Sunat_PLE, No_Codigo_Sunat_FE, Nu_Tipo, Nu_Tipo_Caja, Nu_Orden, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", 'Efectivo', 'CONTADO', '008', '10', 0, 0, 1, 1),
(" . $arrPost['iIdEmpresa'] . ", 'T/C Crédito', 'CONTADO', '006', '48', 2, 1, 2, 1),
(" . $arrPost['iIdEmpresa'] . ", 'T/C Débito', 'CONTADO', '005', '48', 2, 1, 3, 1),
(" . $arrPost['iIdEmpresa'] . ", 'Depósito Bancario', 'CONTADO', '001', '20', 2, 1, 4, 1),
(" . $arrPost['iIdEmpresa'] . ", 'Crédito', 'CREDITO', '0', '2', 1, 1, 5, 1)";
            $this->db->query($sql);

            // TABLE - tipo de medio pago
            $iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND No_Codigo_Sunat_PLE = '006'")->row()->ID_Medio_Pago;//Crédito
            $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado) VALUES (" . $iIdMedioPago . ", 'Visa', 1)";
            $this->db->query($sql);
            
            $iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND No_Codigo_Sunat_PLE = '005'")->row()->ID_Medio_Pago;//Débito
            $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado) VALUES (" . $iIdMedioPago . ", 'Visa', 1)";
            $this->db->query($sql);
            
            $iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND No_Codigo_Sunat_PLE = '001'")->row()->ID_Medio_Pago;//Depósito
            $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado)
VALUES
(" . $iIdMedioPago . ", 'BCP', 1),
(" . $iIdMedioPago . ", 'BBVA', 1),
(" . $iIdMedioPago . ", 'INTERBANK', 1),
(" . $iIdMedioPago . ", 'SCOTIABANK', 1)";
            $this->db->query($sql);

            $sql = "INSERT INTO unidad_medida(ID_Empresa, No_Unidad_Medida, Nu_Sunat_Codigo, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", 'UNIDAD (BIENES)', 'NIU', 1),
(" . $arrPost['iIdEmpresa'] . ", 'UNIDAD (SERVICIOS)', 'ZZ', 1)";
            $this->db->query($sql);
                        
            // TABLE - familia
            $sql = "INSERT INTO familia(ID_Empresa, No_Familia, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", 'GENERAL', 1)";
            $this->db->query($sql);
			$ID_Familia = $this->db->insert_id();

            $arrRowEmpresa = $this->db->query("SELECT ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Empresa FROM empresa WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " LIMIT 1")->row();
            $No_Empresa_NuevoCliente = limpiarCaracteresEspeciales($arrRowEmpresa->No_Empresa);

            // TABLE - entidad - clientes varios
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 0, 1, '0', 'clientes varios', 1)";
            $this->db->query($sql);
			$ID_Cliente_Varios = $this->db->insert_id();
            
            $data_confi = array( 'ID_Entidad_Clientes_Varios_Venta_Predeterminado' => $ID_Cliente_Varios );
            $where_confi = array( 'ID_Configuracion' => $ID_Configuracion);
            $this->db->update( 'configuracion' , $data_confi, $where_confi);

            // TABLE - entidad - cliente y proveedor misma empresa
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 1, 4, '" . $arrRowEmpresa->Nu_Documento_Identidad . "', '" . $No_Empresa_NuevoCliente . "', 1)";
            $this->db->query($sql);
            
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 0, 4, '" . $arrRowEmpresa->Nu_Documento_Identidad . "', '" . $No_Empresa_NuevoCliente . "', 1)";
            $this->db->query($sql);

            //CREAR DATOS PARA LAESYSTEMS CLIENTE - Y MÁS
            $Nu_Celular_Entidad = '';
            if ( strlen($arrPost['sNumeroCelular']) == 11){
                $Nu_Celular_Entidad = explode(' ', $arrPost['sNumeroCelular']);
                $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
            }
            $arrParamsLaesystems = array(
                'Nu_Tipo_Entidad' => 0,//0=Cliente
                'ID_Tipo_Documento_Identidad' => $arrRowEmpresa->ID_Tipo_Documento_Identidad,
                'Nu_Documento_Identidad' => $arrRowEmpresa->Nu_Documento_Identidad,
                'No_Entidad' => $No_Empresa_NuevoCliente,
                'Txt_Email_Entidad' => $arrPost['sEmailUsuario'],
                'Nu_Celular_Entidad' => $Nu_Celular_Entidad,
            );
            $this->agregarDatosNuevaEmpresaALaesystems($arrParamsLaesystems);

            // CREAR CERTIFICADO DIGITAL DE PRUEBA
            if ($arrPost['iTipoProveedorFE']==2) {
                //Crear directorio
                if(file_exists($this->path_cdt_prueba_sunat)){
                    if (copy($this->path_cdt_prueba_sunat . 'FIRMA/12345678901.pfx', $this->path_cdt_prueba_sunat . 'FIRMA/'.$arrRowEmpresa->Nu_Documento_Identidad.'.pfx')) {
                        if (file_exists($this->path_cdt_prueba_sunat . 'FIRMA/'.$arrRowEmpresa->Nu_Documento_Identidad.'.pfx')) {
                        }
                    }

                    $path = $this->path_cdt_prueba_sunat . "BETA/".$arrRowEmpresa->Nu_Documento_Identidad;
                    if(!is_dir($path)){
                        mkdir($path,0755,TRUE);
                    }
                    $path = $this->path_cdt_prueba_sunat . "PRODUCCION/".$arrRowEmpresa->Nu_Documento_Identidad;
                    if(!is_dir($path)){
                        mkdir($path,0755,TRUE);
                    }
                }
            }

            // TABLE - tipo de operacion de caja
            $sql = "INSERT INTO tipo_operacion_caja(ID_Empresa, ID_Organizacion, ID_Almacen, No_Tipo_Operacion_Caja, Nu_Tipo, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Caja Aperturada', 3, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Ingreso de dinero', 5, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Salida de dinero', 6, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Caja Cerrada', 4, 1)
";
            $this->db->query($sql);

            // TABLE - pos
            $sql = "INSERT INTO pos(ID_Empresa, ID_Organizacion, Nu_Pos, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 1, 1)";
            $this->db->query($sql);
			$ID_Pos = $this->db->insert_id();

            // TABLE - serie_documento
            /*
            - 2 D/interno
            - 3 Factura
            - 4 Boleta
            - 5 Nota de Débito
            - 6 Nota de Crédito
            */
            if ( $arrPost['iTipoProveedorFE'] != 3 ) {
                $sSiglasSerie = ($arrPost['iTipoProveedorFE'] == '1' ? 'PP' : 'LA');// PP= Nubefact Reseller y LA = Sunat

                $sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 3, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 4, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 3, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 4, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'F" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'B" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1)";
            } else {
                $sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 14, '0002', 1, 6, " . $ID_Pos . ", 1)";
            }
            $this->db->query($sql);

            if($arrPost['Nu_Activar_Guia_Electronica']==1){//Guías electronicas
                $sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
                VALUES
                (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, 'T" . $sSiglasSerie . "1', 1, 6, NULL, 1),
                (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 7, 'T" . $sSiglasSerie . "2', 1, 6, " . $ID_Pos . ", 1)";

                $this->db->query($sql);
            }

            // TABLE - grupo
            $sql = "INSERT INTO grupo(ID_Empresa, ID_Organizacion, No_Grupo, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 'Gerencia', 1)";
            $this->db->query($sql);
			$ID_Grupo = $this->db->insert_id();

            // TABLE - usuario
            $Nu_Celular = '';
            if ( $arrPost['sNumeroCelular'] && strlen($arrPost['sNumeroCelular']) == 11){
                $Nu_Celular = explode(' ', $arrPost['sNumeroCelular']);
                $Nu_Celular = $Nu_Celular[0].$Nu_Celular[1].$Nu_Celular[2];
            }
            $sql = "INSERT INTO usuario(ID_Empresa, ID_Organizacion, ID_Grupo, No_Usuario, No_Nombres_Apellidos, Nu_Celular, No_Password, Txt_Email, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Grupo . ", '" . $arrPost['sEmailUsuario'] . "', '" . $arrPost['sNombresApellidos'] . "', '" . $Nu_Celular . "', '" . $this->encryption->encrypt($arrPost['sPasswordUsuario']) . "', '" . $arrPost['sEmailUsuario'] . "', 1)";
            $this->db->query($sql);
			$ID_Usuario = $this->db->insert_id();
            
            // TABLE - grupo_usuario
            $sql = "INSERT INTO grupo_usuario(ID_Empresa, ID_Organizacion, ID_Grupo, ID_Usuario) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Grupo . ", " . $ID_Usuario . ")";
            $this->db->query($sql);
			$ID_Grupo_Usuario = $this->db->insert_id();
    
            // TABLE - menu_acceso
            // INSERT INTO menu_acceso ID_Empresa, ID_Menu_Grupo_Usuario, ID_Menu, ID_Grupo_Usuario, Nu_Consultar, Nu_Agregar, Nu_Editar, Nu_Eliminar
            // Se crear una function y esta tendra varios if para distintos tipos de perfil, luego nos traera un array para luego realizar un foreach agrupo en una variable [] y luego con un insert_batch masivamente 
            $arrParams = array(
                'sTipoPerfil' => 'Gerencia',
                'iTipoRubroEmpresa' => $arrPost['iTipoRubroEmpresa']
            );
            $arrResponseMenuAcceso = $this->addMenuAcceso($arrParams);
            if ( $arrResponseMenuAcceso['status'] == 'success' ) {
                $menu_acceso = array();
                foreach( $arrResponseMenuAcceso['result'] as $row){
                    if ( $row->Nu_Seguridad == 1 ) {//24 = Tipos de Movimiento
                        if ( $row->ID_Menu == 45 ) {//45 = Series
                            $menu_acceso[] = array(
                                'ID_Empresa' => $arrPost['iIdEmpresa'],
                                'ID_Menu' => $row->ID_Menu,
                                'ID_Grupo_Usuario' => $ID_Grupo_Usuario,
                                'Nu_Consultar' => 1,
                                'Nu_Agregar' => 1,
                                'Nu_Editar' => 0,
                                'Nu_Eliminar' => 0,
                            );
                        }
                        if ( $row->ID_Menu == 1 || $row->ID_Menu == 9 || $row->ID_Menu == 10 || $row->ID_Menu == 11 || $row->ID_Menu == 25 ) {//IN(1,9,10,11,25);";//Escritorio, Empresa, Org, Sistema y formato, Almacén
                            $menu_acceso[] = array(
                                'ID_Empresa'		=> $arrPost['iIdEmpresa'],
                                'ID_Menu'			=> $row->ID_Menu,
                                'ID_Grupo_Usuario'	=> $ID_Grupo_Usuario,
                                'Nu_Consultar'		=> 1,
                                'Nu_Agregar'		=> 0,
                                'Nu_Editar'			=> 1,
                                'Nu_Eliminar'		=> 0,
                            );
                        }
                        if ( $row->ID_Menu == 12 || $row->ID_Menu == 13 || $row->ID_Menu == 14 || $row->ID_Menu == 15 || $row->ID_Menu == 16 ||
                             $row->ID_Menu == 17 || $row->ID_Menu == 18 || $row->ID_Menu == 85 || $row->ID_Menu == 86 || $row->ID_Menu == 87 ) {//IN(12,13,14,15,16,17,18,85,86,87);";//Moneda, Pais, Departamento, Provincia, Distrito, Grupo Impuesto, Monto Impuesto, Medio Pago, Tipo Medio Pago, Tipo Operacion Caja
                            $menu_acceso[] = array(
                                'ID_Empresa'		=> $arrPost['iIdEmpresa'],
                                'ID_Menu'			=> $row->ID_Menu,
                                'ID_Grupo_Usuario'	=> $ID_Grupo_Usuario,
                                'Nu_Consultar'		=> 1,
                                'Nu_Agregar'		=> 0,
                                'Nu_Editar'			=> 0,
                                'Nu_Eliminar'		=> 0,
                            );
                        }
                        
                        if ( $row->ID_Menu == 57 || $row->ID_Menu == 58 || $row->ID_Menu == 59 ) {//57 = Cargo / Grupo, 58 = Usuario 59 = Opciones del menú
                            $menu_acceso[] = array(
                                'ID_Empresa'		=> $arrPost['iIdEmpresa'],
                                'ID_Menu'			=> $row->ID_Menu,
                                'ID_Grupo_Usuario'	=> $ID_Grupo_Usuario,
                                'Nu_Consultar'		=> 1,
                                'Nu_Agregar'		=> 1,
                                'Nu_Editar'			=> 1,
                                'Nu_Eliminar'		=> 0,
                            );
                        }
                    } else {
                        $menu_acceso[] = array(
                            'ID_Empresa' => $arrPost['iIdEmpresa'],
                            'ID_Menu' => $row->ID_Menu,
                            'ID_Grupo_Usuario' => $ID_Grupo_Usuario,
                            'Nu_Consultar' => 1,
                            'Nu_Agregar' => 1,
                            'Nu_Editar' => 1,
                            'Nu_Eliminar' => 1,
                        );
                    }
                }
			    $this->db->insert_batch('menu_acceso', $menu_acceso);
            } else {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al crear menu acceso');
            }

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al crear datos automaticamente');
			} else {
				$this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Se crea datos automaticamente');
            }
        }// if - else
    }// function automatic
    
    public function addMenuAcceso($arrParams){
        if ( $arrParams['sTipoPerfil'] == 'Gerencia' ) {
            $where_id_menu = "ID_Menu NOT IN(6,26,34,35,51,63,64,66,48,102,103,104,105,106,107,108,112,113,44,100)";
        }
        //44 = Seguimiento de Cotización, 88 = Maestro Delivery, 100 = Estado Cuenta Corriente de Cliente - Otros módulos
        //48 = Pedidos (estos son para pedidos de web)
        //102 = Medio de Pago Marketplace, 103 = Tipo Medio de Pago Marketplace, 104 = Pedidos Marketplace, 105 = Marketplace, 106 = Inicio Marketplace - Marketplace
        //107 = Blog, 108 = Blog Inicio, 112 = Blog post, 113 = Blog historial - Blog
        $where_tipo_sistema =  "AND Nu_Tipo_Sistema =0";//General
        if ( $arrParams['iTipoRubroEmpresa'] == 1) {//1 = Farmacia
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,1)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 2) {// 2 = Tienda a granel
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,2)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 3) {// 3 = Lavandería Personalizada
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,3)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 11) {// 11 = Restaurante
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,11)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 18) {// 18 = escuela de musica
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,18)";
        }
        $query = "SELECT ID_Menu, Nu_Seguridad FROM menu WHERE " . $where_id_menu . " " . $where_tipo_sistema;
        
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
            'message' => 'No hay registros',
        );
    }

	private function agregarDatosNuevaEmpresaALaesystems($arrParams){
        if($this->db->query("SELECT COUNT(*) AS existe FROM entidad WHERE ID_Empresa = 1 AND Nu_Tipo_Entidad = 0 AND ID_Tipo_Documento_Identidad = " . $arrParams['ID_Tipo_Documento_Identidad'] . " AND Nu_Documento_Identidad = '" . $arrParams['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe == 0){//no existe
            // TABLE - entidad - proveedor misma empresa
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado, Txt_Email_Entidad, Nu_Celular_Entidad) VALUES (1, 1, 0, " . $arrParams['ID_Tipo_Documento_Identidad'] . ", '" . $arrParams['Nu_Documento_Identidad'] . "', '" . $arrParams['No_Entidad'] . "', 1, '" . $arrParams['Txt_Email_Entidad'] . "', '" . $arrParams['Nu_Celular_Entidad'] . "')";
            $this->db->query($sql);
        }
    }
    
	public function getPrimerUsuarioLaeGestionxEmpresa($arrPost){
		$query = "SELECT EMP.Nu_Documento_Identidad, EMP.No_Empresa, USR.No_Usuario, USR.No_Password, USR.Nu_Codigo_Pais, USR.Nu_Celular FROM usuario AS USR JOIN empresa AS EMP ON(USR.ID_Empresa=EMP.ID_Empresa) WHERE USR.ID_Empresa=" . $arrPost['ID_Empresa'] . " ORDER BY USR.ID_Usuario ASC LIMIT 1";
		
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
            $arrData = $arrResponseSQL->row();
            $arrData = array(
                'Nu_Documento_Identidad' => $arrData->Nu_Documento_Identidad,
                'No_Empresa' => $arrData->No_Empresa,
                'No_Usuario' => $arrData->No_Usuario,
                'No_Password' => $this->encryption->decrypt($arrData->No_Password),
                'Nu_Codigo_Pais' => $arrData->Nu_Codigo_Pais,
                'Nu_Celular' => $arrData->Nu_Celular
            );
			return array(
				'sStatus' => 'success',
				'arrData' => $arrData,
			);
		}
		
		return array(
			'sStatus' => 'warning',
			'sMessage' => 'No se encontro registro',
		);		
	}
    
	public function cambiarEstadoLaeGestion($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Lae_Gestion' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function cambiarPlanLaeGestion($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Tipo_Plan_Lae_Gestion' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function cambiarEstadoLaeShop($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Lae_Shop' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}

	public function cambiarPlanLaeShop($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Tipo_Plan_Lae_Shop' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function configurarTiendaVirtual($arrPost){
        /*
        iTipoProveedorFE:
        1 = Nubefact
        2 = Sunat
        3 = Sin facturacion electronica
        */
    
        if ( $arrPost['iEstadoEmpresa'] == 0 ) {
            return array('sStatus' => 'danger', 'sMessage' => 'La empresa debe de estar ACTIVA.');
        } else {
            // TABLE - empresa - ACTIVAR TIENDA VIRTUAL
            $sql = "UPDATE empresa SET Nu_Lae_Shop=1 WHERE ID_Empresa=".$arrPost['iIdEmpresa'];
            $this->db->query($sql);

            //Obtener medio pago
            $ID_Medio_Pago_Where=0;
            $arrMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa=".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%Depósito%' LIMIT 1")->row();
            if (is_object($arrMedioPago)) {
                $ID_Medio_Pago_Where = $arrMedioPago->ID_Medio_Pago;
            }

            if ($ID_Medio_Pago_Where==0) {//si no existe en el primero entra aquí
                $arrMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa=".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%Transferencia%' LIMIT 1")->row();
                if (is_object($arrMedioPago)) {
                    $ID_Medio_Pago_Where = $arrMedioPago->ID_Medio_Pago;
                }
            }

            // TABLE - medio_pago - YAPE
            // INACTIVAR en la tabla TIPO MEDIO PAGO
            //$sql = "UPDATE tipo_medio_pago SET Nu_Estado=0 WHERE ID_Medio_Pago=".$ID_Medio_Pago_Where." AND No_Tipo_Medio_Pago = 'YAPE'";
            //$this->db->query($sql);
            
            // TABLE - medio_pago - YAPE
            // INACTIVAR en la tabla TIPO MEDIO PAGO
            //$sql = "UPDATE tipo_medio_pago SET Nu_Estado=0 WHERE ID_Medio_Pago=".$ID_Medio_Pago_Where." AND No_Tipo_Medio_Pago = 'PLIN'";
            //$this->db->query($sql);

            //AGREGAR BANCOS
            /*
            $iIdMedioPago = $this->db->query("SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " AND No_Medio_Pago='Depósito Bancario' AND No_Codigo_Sunat_PLE = '001'")->row()->ID_Medio_Pago;//Depósito
            $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado)
VALUES
(" . $iIdMedioPago . ", 'BCP', 1),
(" . $iIdMedioPago . ", 'BBVA', 1),
(" . $iIdMedioPago . ", 'INTERBANK', 1),
(" . $iIdMedioPago . ", 'SCOTIABANK', 1)";
			$this->db->query($sql);
            */

            // VERIFICAR SI EXISTE Y LUEGO INSERTAR
            if ($this->db->query("SELECT COUNT(*) AS existe FROM medio_pago WHERE ID_Empresa =".$arrPost['iIdEmpresa']." AND No_Medio_Pago = 'YAPE' LIMIT 1;")->row()->existe == 0) {
                $sql = "INSERT INTO medio_pago(ID_Empresa, No_Medio_Pago, Txt_Medio_Pago, No_Codigo_Sunat_PLE, No_Codigo_Sunat_FE, Nu_Tipo, Nu_Tipo_Caja, Nu_Orden, Nu_Estado, No_Medio_Pago_Tienda_Virtual, Nu_Activar_Medio_Pago_Lae_Shop, Nu_Tipo_Forma_Pago_Lae_Shop, Txt_Url_Imagen, Nu_Cierre_Venta_Pago_Lae_Shop)
VALUES
(".$arrPost['iIdEmpresa'].", 'YAPE', 'CONTADO', '001', '20', 2, 1, 6, 1, 'YAPE', 1, 4, 'https://laesystems.com/assets/images/yape.png', 1);";
                $this->db->query($sql);
                $ID_Medio_Pago = $this->db->insert_id();

                // TIPO MEDIO DE PAGO
                $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado) VALUES (" . $ID_Medio_Pago . ", 'YAPE', 1)";
                $this->db->query($sql);
            } else {
                $sql = "UPDATE medio_pago SET No_Medio_Pago_Tienda_Virtual='YAPE', Nu_Activar_Medio_Pago_Lae_Shop = 1, Nu_Tipo_Forma_Pago_Lae_Shop=4, Nu_Cierre_Venta_Pago_Lae_Shop=1, Txt_Url_Imagen='https://laesystems.com/assets/images/yape.png' WHERE ID_Empresa = ".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%YAPE%';";
                $this->db->query($sql);
            }

            // VERIFICAR SI EXISTE Y LUEGO INSERTAR
            if ($this->db->query("SELECT COUNT(*) AS existe FROM medio_pago WHERE ID_Empresa =".$arrPost['iIdEmpresa']." AND No_Medio_Pago = 'PLIN' LIMIT 1;")->row()->existe == 0) {
                $sql = "INSERT INTO medio_pago(ID_Empresa, No_Medio_Pago, Txt_Medio_Pago, No_Codigo_Sunat_PLE, No_Codigo_Sunat_FE, Nu_Tipo, Nu_Tipo_Caja, Nu_Orden, Nu_Estado, No_Medio_Pago_Tienda_Virtual, Nu_Activar_Medio_Pago_Lae_Shop, Nu_Tipo_Forma_Pago_Lae_Shop, Txt_Url_Imagen, Nu_Cierre_Venta_Pago_Lae_Shop)
    VALUES
    (".$arrPost['iIdEmpresa'].", 'PLIN', 'CONTADO', '001', '20', 2, 1, 7, 1, 'PLIN', 1, 4, 'https://laesystems.com/assets/images/plin.png', 1);";
                $this->db->query($sql);
                $ID_Medio_Pago = $this->db->insert_id();

                // TIPO MEDIO DE PAGO
                $sql = "INSERT INTO tipo_medio_pago(ID_Medio_Pago, No_Tipo_Medio_Pago, Nu_Estado) VALUES (" . $ID_Medio_Pago . ", 'PLIN', 1)";
                $this->db->query($sql);
            } else {
                $sql = "UPDATE medio_pago SET No_Medio_Pago_Tienda_Virtual='PLIN', Nu_Activar_Medio_Pago_Lae_Shop = 1, Nu_Tipo_Forma_Pago_Lae_Shop=4, Nu_Cierre_Venta_Pago_Lae_Shop=1, Txt_Url_Imagen='https://laesystems.com/assets/images/plin.png' WHERE ID_Empresa = ".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%PLIN%';";
                $this->db->query($sql);
            }

            // ACTIVAR MEDIO DE PAGO PARA TIENDA
            $sql = "UPDATE medio_pago SET No_Medio_Pago_Tienda_Virtual='Pagar con Efectivo', Nu_Activar_Medio_Pago_Lae_Shop = 1, Nu_Tipo_Forma_Pago_Lae_Shop=3, Nu_Cierre_Venta_Pago_Lae_Shop=1, Txt_Url_Imagen='https://laesystems.com/assets/images/efectivo.png' WHERE ID_Empresa = ".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%Efectivo%';";
            $this->db->query($sql);
            $sql = "UPDATE medio_pago SET No_Medio_Pago_Tienda_Virtual='Pagar con Transferencia/Depósito Bancario', Nu_Activar_Medio_Pago_Lae_Shop = 1, Nu_Tipo_Forma_Pago_Lae_Shop=4, Nu_Cierre_Venta_Pago_Lae_Shop=1, Txt_Url_Imagen='https://laesystems.com/assets/images/transferencia.png' WHERE ID_Empresa = ".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%Depósito%';";
            $this->db->query($sql);
            $sql = "UPDATE medio_pago SET No_Medio_Pago_Tienda_Virtual='Pagar con Transferencia/Depósito Bancario', Nu_Activar_Medio_Pago_Lae_Shop = 1, Nu_Tipo_Forma_Pago_Lae_Shop=4, Nu_Cierre_Venta_Pago_Lae_Shop=1, Txt_Url_Imagen='https://laesystems.com/assets/images/transferencia.png' WHERE ID_Empresa = ".$arrPost['iIdEmpresa']." AND No_Medio_Pago LIKE '%Transferencia%';";
            $this->db->query($sql);

            //Crear acceso a API de Ecommercev2
            $arrParamsApi = array('sNombreServicio' => 'Ecommercev2');
            $arrResponseApiSunat = $this->crearAccesoAutomaticoApi($arrParamsApi);
            if ($arrResponseApiSunat['success'] == false){
                $this->db->trans_rollback();
                return array('sStatus' => 'danger', 'sMessage' => 'Problemas al crear api automaticamente ' . $arrResponseApiSunat['msg']);
            }
            $arrPost['sToken'] = $arrResponseApiSunat["data"]["token"];

            //Obtener primer almacen
            $ID_Almacen = $this->db->query("SELECT ID_Almacen FROM almacen WHERE ID_Organizacion = (SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa=".$arrPost['iIdEmpresa']." ORDER BY ID_Organizacion ASC LIMIT 1) LIMIT 1")->row()->ID_Almacen;

            // URL para tienda virtual
            //$url = "http://localhost/librerias.laesystems.com/Ecommercev2";//localhost
            $url = "http://ecxpresslae.com/librerias/Ecommercev2";//cloud
            //UPDATE almacen - Token y URL de la tienda
            $dVencimientoLaeshop = dateNow('fecha');
            $dVencimientoLaeshop = explode('-', $dVencimientoLaeshop);
            $dVencimientoLaeshop = $dVencimientoLaeshop[2] . '-' . $dVencimientoLaeshop[1] . '-' . $dVencimientoLaeshop[0];
            $dVencimientoLaeshop = date("d-m-Y",strtotime($dVencimientoLaeshop."+ 8 days"));
            $dVencimientoLaeshop = explode('-', $dVencimientoLaeshop);
            $dVencimientoLaeshop = $dVencimientoLaeshop[2] . '-' . $dVencimientoLaeshop[1] . '-' . $dVencimientoLaeshop[0];
            $sql = "UPDATE almacen SET Txt_Ruta_Lae_Shop = '" . $url . "', Txt_Token_Lae_Shop='" . $arrPost['sToken'] . "', Fe_Vencimiento_Laeshop='" . $dVencimientoLaeshop . "' WHERE ID_Almacen = ".$ID_Almacen;
            $this->db->query($sql);

            //INSERT - tour_tienda_virtual
            $sql = "INSERT INTO tour_tienda_virtual(ID_Empresa,No_Titulo,No_Subtitulo,Txt_Url_Menu,Nu_Orden,Nu_Estado_Proceso, Nu_Estado_Visualizacion, Nu_ID_Interno)
VALUES
(".$arrPost['iIdEmpresa'].", 'Información de tienda', 'Tienda Virtual > Configuración', 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/listar', 1, 0, 1, 1),
(".$arrPost['iIdEmpresa'].", 'Crear Categorías', 'Tienda Virtual', 'TiendaVirtual/CategoriasTiendaVirtualController/listar', 2, 0, 1, 2),
(".$arrPost['iIdEmpresa'].", 'Agregar Productos', 'Tienda Virtual', 'TiendaVirtual/ItemsTiendaVirtualController/listar', 3, 0, 1, 3),
(".$arrPost['iIdEmpresa'].", 'Métodos de Entrega', 'Tienda Virtual > Confguración', 'TiendaVirtual/Configuracion/MetodosEntregaTiendaVirtualController/listar', 4, 0, 1, 4),
(".$arrPost['iIdEmpresa'].", 'Métodos de Pago', 'Tienda Virtual > Configuración', 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/listar', 5, 0, 1, 5);";
            $this->db->query($sql);
            $UniqueID = "tmp".$this->ConfiguracionTienda->uniqID();
            //INSERT - subdominio_tienda_virtual
            $sql = "INSERT INTO subdominio_tienda_virtual(ID_Empresa,No_Subdominio_Tienda_Virtual,No_Dominio_Tienda_Virtual,Nu_Tipo_Tienda,Nu_Estado) VALUES (".$arrPost['iIdEmpresa'].", '".$UniqueID."', 'compramaz.com', 1, 1);";
            $this->db->query($sql);

            $Dominio    = $UniqueID.".compramaz.com";
            $FileName   =   md5($Dominio);
            $this->ConfiguracionTienda->setDebug(false);
            $this->ConfiguracionTienda->Carga($FileName)
            ->Constante('TIENDA_DOMINIO',$Dominio)
            ->Constante("TIENDA_TOKEN",$arrPost['sToken'])
            ->Constante("TIENDA_IDTIENDA",$FileName)
            ->Constante("TIENDA_ESTATUS","1")
            ->Escribir();

            //Obtener usuario gerencia
		    $ID_Grupo_Usuario = $this->db->query("SELECT ID_Grupo_Usuario FROM grupo_usuario WHERE ID_Empresa=" . $arrPost['iIdEmpresa'] . " ORDER BY ID_Usuario ASC LIMIT 1")->row()->ID_Grupo_Usuario;

            //INSERT - menu -> activar menú de tienda virtual
            $sql = "INSERT INTO menu_acceso(ID_Empresa,ID_Menu,ID_Grupo_Usuario,Nu_Consultar,Nu_Agregar,Nu_Editar,Nu_Eliminar)
VALUES
(".$arrPost['iIdEmpresa'].",136,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",137,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",138,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",139,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",140,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",141,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",142,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",143,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",144,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",145,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",146,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",147,".$ID_Grupo_Usuario.",1,1,1,1),
(".$arrPost['iIdEmpresa'].",148,".$ID_Grupo_Usuario.",1,1,1,1);";
            $this->db->query($sql);

            //INSERT - metodo_entrega_tienda_virtual -> para saber que tipo de entrega usará en su tienda
            $sql = "INSERT INTO metodo_entrega_tienda_virtual(ID_Empresa,No_Metodo_Entrega_Tienda_Virtual,Nu_Tipo_Metodo_Entrega_Tienda_Virtual,Nu_Estado)
VALUES
(".$arrPost['iIdEmpresa'].", 'Delivery', 6, 1),
(".$arrPost['iIdEmpresa'].", 'Recojo en Tienda', 7, 1);";
            $this->db->query($sql);

            //INSERT CON SELECT - distrito_tienda_virtual -> se indica cual usará en su tienda para sus delivery
            $sql = "INSERT INTO distrito_tienda_virtual(ID_Empresa, ID_Provincia, No_Distrito, Ss_Delivery, Nu_Habilitar_Ecommerce, Nu_Estado) SELECT '".$arrPost['iIdEmpresa']."', ID_Provincia, No_Distrito, '0.00', '0', Nu_Estado FROM distrito;";
            $this->db->query($sql);

            if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return array('sStatus' => 'danger', 'sMessage' => 'Problemas al crear datos automaticamente');
			} else {
				$this->db->trans_commit();
                return array('sStatus' => 'success', 'sMessage' => 'Se crea datos automaticamente');
            }
        }
    }


    private function crearAccesoAutomaticoApi($arrParams){
        //$url = "http://localhost/librerias.laesystems.com/ApiController/crear_api_servicio_automatico";//localhost
        $url = "https://ecxpresslae.com/librerias/ApiController/crear_api_servicio_automatico";//cloud

        $token = "U6Gclz6Mai5iJy7uIsPNDeoFsL6qN9i8Bg1JuiRw";

        $postData = array(
            "tipo_servicio" => $arrParams['sNombreServicio']//"Ecommercev2" //SunatFacturador
        );

        $data_json = json_encode($postData);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="'.$token.'"',
                'Content-Type: application/json; charset=utf-8',
                'X-Api-Key: ' . $token,
                'Host: laesystems.com'
            )
        );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE );
        if ($errno = curl_errno($curl)) {
            $error_msg = curl_error($curl);
            if ( !empty($error_msg) ) {
                $error_message = curl_strerror($errno);                
                return array("success" => false, "msg" => "cURL error ({$errno}):\n {$error_message}");
            }
        }
        curl_close($curl);

        $accepted_response = array( 200, 301, 302 );
        if( in_array( $httpcode, $accepted_response ) ) {
            $response = json_decode($response, true);
            if (is_array($response)) {
                return $response;
            } else {
                return array("success" => false, "msg" => "Error con respuesta API");
            }
        } else {
            return array("success" => false, "msg" => "Error http code " . $httpcode);
        }
    }
    
	public function cambiarEstadoEmpresa($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function cambiarEstadoVendedorDrop($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Vendedor_Dropshipping' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function cambiarEstadoProveedorDrop($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Proveedor_Dropshipping' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function cambiarEstadoTiendaPropia($ID, $Nu_Estado){
        $where = array('ID_Empresa' => $ID);
        $arrData = array( 'Nu_Tienda_Virtual_Propia' => $Nu_Estado );
		if ($this->db->update('empresa', $arrData, $where) > 0)
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al cambiar estado');
	}
    
	public function verProgresoTienda($arrPost){
        $query ="SELECT * FROM tour_tienda_virtual WHERE ID_Empresa = " . $arrPost['ID_Empresa'] . " ORDER BY Nu_Orden ASC;";
		
		if ( !$this->db->simple_query($query) ){
			$error = $this->db->error();
			return array(
				'status' => 'danger',
				'message' => 'Problemas al obtener datos',
				'sCodeSQL' => $error['code'],
				'sMessageSQL' => $error['message']
			);
		}
		$arrResponseSQL = $this->db->query($query);
		if ( $arrResponseSQL->num_rows() > 0 ){
			return array(
				'status' => 'success',
				'result' => $arrResponseSQL->result()
			);
		}
		
		return array(
			'status' => 'warning',
			'message' => 'No se encontro registro'
        );
	}

    public function actualizarDepositoBilletera($where, $data){
		if ( $this->db->query("UPDATE empresa SET Ss_Deposito_Pago_Billetera=Ss_Deposito_Pago_Billetera+" . $data['Ss_Deposito_Pago_Billetera'] . " WHERE ID_Empresa=".$where['ID_Empresa']) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }

    public function actualizarSaldoAcumuladoBilletera($where, $data){
		if ( $this->db->query("UPDATE empresa SET Ss_Saldo_Acumulado_Billetera=" . $data['Ss_Saldo_Acumulado_Billetera'] . " WHERE ID_Empresa=".$where['ID_Empresa']) > 0 )
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
}
