<?php
class EmpresaModel extends CI_Model{
	var $table                          = 'empresa';
	var $table_tabla_dato               = 'tabla_dato';
	var $table_distrito                 = 'distrito';
	var $table_documento_cabecera       = 'documento_cabecera';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_configuracion = 'configuracion';
	
    var $column_order = array('Nu_Tipo_Proveedor_FE', 'No_Tipo_Documento_Identidad_Breve', 'Nu_Documento_Identidad', 'No_Empresa', null);
    var $column_search = array('Nu_Tipo_Proveedor_FE', 'No_Tipo_Documento_Identidad_Breve', 'Nu_Documento_Identidad', 'No_Empresa', null);
    var $order = array('Nu_Documento_Identidad' => 'asc');
	
	public function __construct(){
		parent::__construct();
        $this->load->database();
	}

	public function _get_datatables_query(){
        $this->db->select('empresa.ID_Empresa, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Empresa, No_Empresa_Comercial, Txt_Direccion_Empresa, Nu_MultiAlmacen, empresa.Nu_Estado, Nu_Tipo_Proveedor_FE')
        ->from($this->table)
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
        ->where($this->table . '.ID_Empresa', $this->empresa->ID_Empresa);

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
        $this->db->select('empresa.ID_Empresa, TDI.No_Tipo_Documento_Identidad_Breve, Nu_Documento_Identidad, No_Empresa, Txt_Direccion_Empresa, Nu_MultiAlmacen, empresa.Nu_Estado, Nu_Tipo_Proveedor_FE, CONFI.ID_Configuracion')
        ->from($this->table)
        ->join($this->table_configuracion . ' AS CONFI', 'CONFI.ID_Empresa = ' . $this->table . '.ID_Empresa', 'left')
    	->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = ' . $this->table . '.ID_Tipo_Documento_Identidad', 'join')
        ->where($this->table . '.ID_Empresa', $this->empresa->ID_Empresa);
        
        return $this->db->count_all_results();
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
    
    public function actualizarEmpresa($where, $data, $ENu_Documento_Identidad, $ETxt_Direccion_Empresa){
		if( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) existe FROM " . $this->table . " WHERE Nu_Documento_Identidad='" . $data['Nu_Documento_Identidad'] . "' LIMIT 1")->row()->existe > 0 ){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El registro ya existe');
		} else if ( $ENu_Documento_Identidad != $data['Nu_Documento_Identidad'] && $this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Empresa=" . $where['ID_Empresa'] . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La empresa tiene movimiento(s)');
        } else {
            //if ( $ETxt_Direccion_Empresa != $data['Txt_Direccion_Empresa'] ) {
                $arrResponse = $this->modificarDireccionEmpresaAlmacen($data, $where['ID_Empresa']);
                if ($arrResponse['status'] == 'error')
                    return $arrResponse;
            //}

            $arrResponse = $this->modificarClienteProveedorMismaEmpresa($data, $where['ID_Empresa']);
            if ($arrResponse['status'] == 'error')
                return $arrResponse;

		    if ( $this->db->update($this->table, $data, $where) > 0 ) {
                /* TOUR GESTION */
                $where_tour = array('ID_Empresa' => $where['ID_Empresa'], 'Nu_ID_Interno' => 1);
                //validamos que si complete los siguientes datos
                if($this->db->query("SELECT COUNT(*) AS cantidad FROM empresa WHERE ID_Empresa=" . $where['ID_Empresa'] . " AND Nu_Documento_Identidad!=No_Empresa LIMIT 1")->row()->cantidad > 0){
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 1);
                } else {
                    //Cambiar estado a completado para el tour
                    $data_tour = array('Nu_Estado_Proceso' => 0);
                }
                $this->db->update('tour_gestion', $data_tour, $where_tour);
                /* END TOUR GESTION */

                //TOUR GESTION - Solo si es SUNAT
                if($this->empresa->Nu_Tipo_Proveedor_FE==2) {//2=SUNAT
                    $where_tour = array('ID_Empresa' => $where['ID_Empresa'], 'Nu_ID_Interno' => 2);
                    //validamos que si complete los siguientes datos
                    if($this->db->query("SELECT COUNT(*) AS cantidad FROM empresa WHERE ID_Empresa=" . $where['ID_Empresa'] . " AND (Txt_Usuario_Sunat_Sol='MODDATOS' OR Txt_Password_Sunat_Sol='moddatos' OR Txt_Password_Firma_Digital='123456') LIMIT 1")->row()->cantidad == 0){
                        //Cambiar estado a completado para el tour
                        $data_tour = array('Nu_Estado_Proceso' => 1);
                    } else {
                        //Cambiar estado a completado para el tour
                        $data_tour = array('Nu_Estado_Proceso' => 0);
                    }
                    $this->db->update('tour_gestion', $data_tour, $where_tour);
                }

		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
            }
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
	public function eliminarEmpresa($ID){
		if($this->db->query("SELECT COUNT(*) AS existe FROM " . $this->table_documento_cabecera . " WHERE ID_Empresa=" . $ID . " LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'La empresa tiene movimiento(s)');
		} else {
			$this->db->where('ID_Empresa', $ID);
            $this->db->delete($this->table);
		    if ( $this->db->affected_rows() > 0 )
		        return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro eliminado');
		}
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al eliminar');
	}

    public function modificarDireccionEmpresaAlmacen($arrData, $ID_Empresa){
        if ($this->db->query("SELECT COUNT(*) AS cantidad FROM organizacion WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Estado=1")->row()->cantidad == 1){
            $ID_Organizacion = $this->db->query("SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa=" . $ID_Empresa . " AND Nu_Estado=1 ORDER BY ID_Organizacion ASC LIMIT 1")->row()->ID_Organizacion;
            if ($this->db->query("SELECT COUNT(*) AS cantidad FROM almacen WHERE ID_Organizacion=" . $ID_Organizacion . " AND Nu_Estado=1")->row()->cantidad == 1){
                //cambiar direccion de la tabla almacen solo si tiene 1 registro
                $data['ID_Departamento'] = $arrData['ID_Departamento'];
                $data['ID_Provincia'] = $arrData['ID_Provincia'];
                $data['ID_Distrito'] = $arrData['ID_Distrito'];
                $data['Txt_Direccion_Almacen'] = $arrData['Txt_Direccion_Empresa'];
                $data['ID_Ubigeo_Inei_Partida'] = $arrData['ID_Ubigeo_Inei'];
                unset($arrData);
                //ID_Almacen
                $ID_Almacen = $this->db->query("SELECT ID_Almacen FROM almacen WHERE ID_Organizacion=" . $ID_Organizacion . " LIMIT 1")->row()->ID_Almacen;
                $where['ID_Almacen'] = $ID_Almacen;
                if ( $this->db->update('almacen', $data, $where) > 0 )
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al modificar almacén');
            } else {
                return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'No se modificará porque tiene mas de 2 almacenes');
            }
        } else {
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'No se modificará porque tiene mas de 2 organizaciones');
        }
    }

    public function modificarClienteProveedorMismaEmpresa($arrData, $ID_Empresa){
        $data['No_Entidad'] = $arrData['No_Empresa'];
        $where = array(
            'ID_Empresa' => $ID_Empresa,
            'Nu_Documento_Identidad' => $arrData['Nu_Documento_Identidad'],
        );
        if ( $this->db->update('entidad', $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al modificar entidad');
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
140,
80,
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
(" . $iIdMedioPago . ", 'YAPE', 1),
(" . $iIdMedioPago . ", 'BBVA', 1),
(" . $iIdMedioPago . ", 'INTERBANK', 1),
(" . $iIdMedioPago . ", 'SCOTIABANK', 1),
(" . $iIdMedioPago . ", 'PLIN', 1)";
            $this->db->query($sql);

            $sql = "INSERT INTO unidad_medida(ID_Empresa, No_Unidad_Medida, Nu_Sunat_Codigo, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", 'UNIDAD (BIENES)', 'NIU', 1),
(" . $arrPost['iIdEmpresa'] . ", 'UNIDAD (SERVICIOS)', 'ZZ', 1)";
            $this->db->query($sql);
            
            $sql = "INSERT INTO unidad_medida(ID_Empresa, No_Unidad_Medida, Nu_Sunat_Codigo, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", 'BOLSA', 'BG', 1)";
            $this->db->query($sql);
			$ID_Unidad_Medida_Bolsa = $this->db->insert_id();
            
            // TABLE - familia
            $sql = "INSERT INTO familia(ID_Empresa, No_Familia, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", 'GENERAL', 1)";
            $this->db->query($sql);
			$ID_Familia = $this->db->insert_id();

            // TABLE - producto
            $sql = "INSERT INTO producto(ID_Empresa, ID_Ubicacion_Inventario, ID_Producto_Sunat, Nu_Tipo_Producto, ID_Tipo_Producto, Nu_Codigo_Barra, ID_Impuesto, No_Producto, Ss_Precio, ID_Familia, ID_Unidad_Medida, ID_Impuesto_Icbper, Nu_Estado, ID_Tabla_Dato_Icbper, Ss_Icbper)
VALUES (" . $arrPost['iIdEmpresa'] . ", 1, 0, 1, 2, 'BCICBPER', " . $ID_Impuesto . ", 'BOLSA CON ICBPER', 0.30, " . $ID_Familia . ", " . $ID_Unidad_Medida_Bolsa . ", 1, 1, 2070, 0.30)";
            $this->db->query($sql);

            $arrRowEmpresa = $this->db->query("SELECT Nu_Documento_Identidad, No_Empresa FROM empresa WHERE ID_Empresa = " . $arrPost['iIdEmpresa'] . " LIMIT 1")->row();
            $No_Empresa_NuevoCliente = limpiarCaracteresEspeciales($arrRowEmpresa->No_Empresa);

            // TABLE - entidad - clientes varios
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 0, 1, '0', 'clientes varios', 1)";
            $this->db->query($sql);
			$ID_Cliente_Varios = $this->db->insert_id();
            
            $data_confi = array( 'ID_Entidad_Clientes_Varios_Venta_Predeterminado' => $ID_Cliente_Varios );
            $where_confi = array( 'ID_Configuracion' => $ID_Configuracion);
            $this->db->update( 'configuracion' , $data_confi, $where_confi);

            // TABLE - entidad - proveedor misma empresa
            $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado) VALUES (" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", 1, 4, '" . $arrRowEmpresa->Nu_Documento_Identidad . "', '" . $No_Empresa_NuevoCliente . "', 1)";
            $this->db->query($sql);

            //CREAR DATOS PARA LAESYSTEMS CLIENTE - Y MÁS
            $Nu_Celular_Entidad = '';
            if ( strlen($arrPost['sNumeroCelular']) == 11){
                $Nu_Celular_Entidad = explode(' ', $arrPost['sNumeroCelular']);
                $Nu_Celular_Entidad = $Nu_Celular_Entidad[0].$Nu_Celular_Entidad[1].$Nu_Celular_Entidad[2];
            }
            $arrParamsLaesystems = array(
                'Nu_Tipo_Entidad' => 0,//0=Cliente
                'ID_Tipo_Documento_Identidad' => 4,
                'Nu_Documento_Identidad' => $arrRowEmpresa->Nu_Documento_Identidad,
                'No_Entidad' => $No_Empresa_NuevoCliente,
                'Txt_Email_Entidad' => $arrPost['sEmailUsuario'],
                'Nu_Celular_Entidad' => $Nu_Celular_Entidad,
            );
            $this->agregarDatosNuevaEmpresaALaesystems($arrParamsLaesystems);

            // TABLE - tipo de operacion de caja
            $sql = "INSERT INTO tipo_operacion_caja(ID_Empresa, ID_Organizacion, ID_Almacen, No_Tipo_Operacion_Caja, Nu_Tipo, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Apertura de Caja', 3, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Ingreso de dinero', 5, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Salida de dinero', 6, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 'Cierre de Caja', 4, 1)
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
                $sSiglasSerie = ($arrPost['iTipoProveedorFE'] == '1' ? 'PP' : 'LA');// LA = Sunat

                $sql = "INSERT INTO serie_documento(ID_Empresa, ID_Organizacion, ID_Almacen, ID_Tipo_Documento, ID_Serie_Documento, Nu_Numero_Documento, Nu_Cantidad_Caracteres, ID_POS, Nu_Estado)
VALUES
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0001', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 3, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 4, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 5, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'F" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 6, 'B" . $sSiglasSerie . "1', 1, 6, NULL, 1),
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1),
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
(" . $arrPost['iIdEmpresa'] . ", " . $ID_Organizacion . ", " . $ID_Almacen . ", 2, '0002', 1, 6, " . $ID_Pos . ", 1)";
            }
            $this->db->query($sql);

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

/* LIMPIAR DATOS DE PRUEBA DE CONFIGURACION AUTOMATICA

DELETE FROM menu_acceso WHERE ID_Empresa = 54;
DELETE FROM grupo_usuario WHERE ID_Empresa = 54;
DELETE FROM usuario WHERE ID_Empresa = 54;
DELETE FROM grupo WHERE ID_Empresa = 54;
DELETE FROM serie_documento WHERE ID_Empresa = 54;
DELETE FROM pos WHERE ID_Empresa = 54;
DELETE FROM tipo_operacion_caja WHERE ID_Empresa = 54;
DELETE FROM entidad WHERE ID_Empresa = 54;
DELETE FROM familia WHERE ID_Empresa = 54;
DELETE FROM unidad_medida WHERE ID_Empresa = 54;
DELETE FROM producto WHERE ID_Empresa = 54;
DELETE FROM tipo_medio_pago WHERE ID_Medio_Pago IN (SELECT ID_Medio_Pago FROM medio_pago WHERE ID_Empresa = 54);
DELETE FROM medio_pago WHERE ID_Empresa = 54;
DELETE FROM moneda WHERE ID_Empresa = 54;
DELETE FROM impuesto_cruce_documento WHERE ID_Impuesto IN (SELECT ID_Impuesto FROM impuesto WHERE ID_Empresa = 54);
DELETE FROM impuesto WHERE ID_Empresa = 54;
DELETE FROM configuracion WHERE ID_Empresa = 54;
DELETE FROM almacen WHERE ID_Organizacion IN (SELECT ID_Organizacion FROM organizacion WHERE ID_Empresa = 54);
DELETE FROM organizacion WHERE ID_Empresa = 54;

*/

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
        $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0)";//General
        if ( $arrParams['iTipoRubroEmpresa'] == 1) {//1 = Farmacia
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,1)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 2) {// 2 = Tienda a granel
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,2)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 3) {// 3 = Lavandería Personalizada
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,3)";
        } else if ( $arrParams['iTipoRubroEmpresa'] == 11) {// 11 = Restaurante
            $where_tipo_sistema =  "AND Nu_Tipo_Sistema IN(0,11)";
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
        // TABLE - entidad - proveedor misma empresa
        $sql = "INSERT INTO entidad(ID_Empresa, ID_Organizacion, Nu_Tipo_Entidad, ID_Tipo_Documento_Identidad, Nu_Documento_Identidad, No_Entidad, Nu_Estado, Txt_Email_Entidad, Nu_Celular_Entidad) VALUES (1, 1, 0, 4, '" . $arrParams['Nu_Documento_Identidad'] . "', '" . $arrParams['No_Entidad'] . "', 1, '" . $arrParams['Txt_Email_Entidad'] . "', '" . $arrParams['Nu_Celular_Entidad'] . "')";
        $this->db->query($sql);
    }
}
