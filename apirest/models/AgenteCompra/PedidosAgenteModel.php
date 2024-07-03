<?php
class PedidosAgenteModel extends CI_Model{
	var $table = 'agente_compra_pedido_cabecera';
	var $table_importacion_grupal_pedido_detalle = 'agente_compra_pedido_detalle';
	var $table_empresa = 'empresa';
	var $table_organizacion = 'organizacion';
	var $table_configuracion = 'configuracion';
	var $table_moneda = 'moneda';
	var $table_cliente = 'entidad';
	var $table_producto = 'producto';
	var $table_unidad_medida = 'unidad_medida';
	var $table_medio_pago = 'medio_pago';
	var $table_departamento = 'departamento';
	var $table_provincia = 'provincia';
	var $table_distrito = 'distrito';
	var $table_tipo_documento_identidad = 'tipo_documento_identidad';
	var $table_importacion_grupal_cabecera = 'importacion_grupal_cabecera';
	var $table_pais = 'pais';
	var $table_usuario_intero = 'usuario';
	var $table_grupo_usuario = 'grupo_usuario';
	
    var $order = array('Fe_Registro' => 'desc');
		
	public function __construct(){
		parent::__construct();
	}
	
	public function _get_datatables_query(){
        $this->db->select($this->table . '.*, P.No_Pais,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Entidad, CLI.ID_Entidad AS ID_Entidad_Cliente')//, USRINTERNO.No_Usuario
		->from($this->table)
    	->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
    	->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
    	//->join($this->table_usuario_intero . ' AS USRINTERNO', 'USRINTERNO.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Empresa', 'left')
    	->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
		->where($this->table . '.Nu_Estado=', 1);

		$this->db->where("Fe_Emision BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");

		if(isset($this->order)) {
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
    
    public function get_by_id($ID){
        $this->db->select('P.No_Pais AS No_Pais_Cliente, DEP.No_Departamento, PRO.No_Provincia, DIS.No_Distrito,
		EMP.No_Empresa, EMP.Txt_Direccion_Empresa, EMP.Nu_Documento_Identidad AS Nu_Documento_Identidad_Empresa,
		CONFI.No_Logo_Empresa, CONFI.No_Imagen_Logo_Empresa, CONFI.Nu_Height_Logo_Ticket,
		CONFI.Nu_Width_Logo_Ticket, ' . $this->table . '.*,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto,
		IGPD.ID_Pedido_Detalle, IGPD.Txt_Producto, IGPD.Txt_Descripcion, IGPD.Qt_Producto, IGPD.Txt_Url_Imagen_Producto, IGPD.Txt_Url_Link_Pagina_Producto,
		TDI.No_Tipo_Documento_Identidad_Breve, ' . $this->table . '.Nu_Estado AS Nu_Estado_Pedido, CONFI.Txt_Cuentas_Bancarias, CLI.ID_Entidad AS ID_Entidad_Cliente');
        $this->db->from($this->table);
		$this->db->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
		$this->db->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join');
		$this->db->join($this->table_departamento . ' AS DEP', 'DEP.ID_Departamento = EMP.ID_Departamento', 'join');
		$this->db->join($this->table_provincia . ' AS PRO', 'PRO.ID_Provincia = EMP.ID_Provincia', 'join');
		$this->db->join($this->table_distrito . ' AS DIS', 'DIS.ID_Distrito = EMP.ID_Distrito', 'join');
		$this->db->join($this->table_organizacion . ' AS ORG', 'ORG.ID_Organizacion = ' . $this->table . '.ID_Organizacion', 'join');
		$this->db->join($this->table_configuracion . ' AS CONFI', 'CONFI.ID_Empresa = EMP.ID_Empresa', 'join');
    	$this->db->join($this->table_importacion_grupal_pedido_detalle . ' AS IGPD', 'IGPD.ID_Pedido_Cabecera = ' . $this->table . '.ID_Pedido_Cabecera', 'join');
    	$this->db->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join');
		$this->db->join($this->table_tipo_documento_identidad . ' AS TDI', 'TDI.ID_Tipo_Documento_Identidad = CLI.ID_Tipo_Documento_Identidad', 'join');
        $this->db->where($this->table . '.ID_Pedido_Cabecera',$ID);
        $query = $this->db->get();
        return $query->result();
    }

	public function cambiarEstado($ID, $Nu_Estado, $id_correlativo, $ID_Entidad_Cliente){
        $where = array('ID_Pedido_Cabecera' => $ID);
        $data = array( 'Nu_Estado' => $Nu_Estado );
		if ($this->db->update($this->table, $data, $where) > 0) {
			if($Nu_Estado==2 && $id_correlativo==0){
				//asignar cotizacion a usuario de tipo cliente para que luego en su plataforma las pueda revisar
				$query = "SELECT No_Contacto, Txt_Email_Contacto, Nu_Celular_Contacto FROM entidad WHERE ID_Entidad = " . $ID_Entidad_Cliente . " LIMIT 1";
				$objEntidadCliente = $this->db->query($query)->row();
				if(is_object($objEntidadCliente)){
					//validacion de email
					$sEmail = trim($objEntidadCliente->Txt_Email_Contacto);
					$sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);// Remove all illegal characters from email
					$regex = '/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/';// regular expression for email check
					if ( $sEmail != 'root' && !filter_var($sEmail, FILTER_VALIDATE_EMAIL) && !preg_match($regex, $sEmail) ) {
						return array('status' => 'error', 'message' => 'Debes ingresar un email válido');
					}

					$data_cliente_usuario = array(
						'ID_Empresa' 			=> 1,
						'ID_Organizacion' 		=> 1,
						'ID_Grupo'				=> 1204,//1204 Grupo cliente localhost - 1205 grupo cliente
						'No_Usuario'			=> $sEmail,
						'No_Nombres_Apellidos'	=> $objEntidadCliente->No_Contacto,
						'No_Password'			=> $this->encryption->encrypt($sEmail),
						'Nu_Celular'			=> $objEntidadCliente->Nu_Celular_Contacto,
						'Txt_Email'				=> $sEmail,
						'No_IP'					=> $this->input->ip_address(),
						'Nu_Estado'				=> 1,
					);
					
					$arrUsuarioCliente = $this->crearUsuarioCliente($data_cliente_usuario);
					if($arrUsuarioCliente['status']=='success'){
						//si es Nu_Estado=2 Garantizado crear correlativo de mes y año si no existe y asignar al pedido
						$arrCorrelativo = $this->generarCorrelativo();
						if($arrCorrelativo['status']=='success'){
							$ID_Agente_Compra_Correlativo = $arrCorrelativo['result']['id_correlativo'];
							$Nu_Correlativo = $arrCorrelativo['result']['numero_correlativo'];

							//insertar estado proceso agente compra
							$arrDataTour = array(
								'ID_Pedido_Cabecera' => $ID
							);
							$arrTour = $this->generarEstadoProcesoAgenteCompra($arrDataTour);
							$cotizationCode=$this->generateCotizationCode();
							//actualizar tabla para agregar correlativo
							$data = array(
								'ID_Agente_Compra_Correlativo' => $ID_Agente_Compra_Correlativo,
								'Nu_Correlativo' => $Nu_Correlativo,
								'Fe_Emision_Cotizacion' => dateNow('fecha'),
								'Fe_Registro_Hora_Cotizacion' => dateNow('fecha_hora'),
								'ID_Usuario_Interno_Empresa' => $this->user->ID_Usuario,
								'ID_Usuario_Pedido' => $arrUsuarioCliente['ID_Usuario'],
								'cotizacionCode'=>$cotizationCode
								 //para cliente
							);

							if ($this->db->update($this->table, $data, $where) > 0) {
								return array('status' => 'success', 'message' => 'Correlativo generado');
							} else {
								return array('status' => 'error', 'message' => 'Error al asignar correlativo');
							}
						} else {
							return $arrCorrelativo;
						}
					} else {
						return $arrUsuarioCliente;
					}
				} else {
					return array('status' => 'error', 'message' => 'No existe cliente');
				}
			} else {
				return array('status' => 'success', 'message' => 'Actualizado');
			}
		}
		return array('status' => 'error', 'message' => 'Error al cambiar estado');
	}
    
    public function actualizarPedido($where, $data, $arrProducto, $arrProductoTable){
		//array_debug($where);
		//array_debug($data);
		//array_debug($arrProducto);
		//array_debug($_FILES);

		$this->db->trans_begin();
		
		//$this->db->where('ID_Pedido_Cabecera', $where['ID_Pedido_Cabecera']);
		//$this->db->delete('agente_compra_pedido_detalle');
		
		//localhost
		//$path = "assets/images/productos/";
		//cloud
		
		//agregar productos de tabla de cliente
		if (!empty($arrProducto)) {
			$path = "../../agentecompra.probusiness.pe/public_html/assets/images/productos/";
			$iCounter=0;
			$_FILES['tmp_voucher'] = $_FILES['voucher'];
			
			foreach($arrProducto as $row) {
				//SET IMAGEN
				$_FILES['voucher']['name'] = $_FILES['tmp_voucher']['name'][$iCounter];
				$_FILES['voucher']['type'] = $_FILES['tmp_voucher']['type'][$iCounter];
				$_FILES['voucher']['tmp_name'] = $_FILES['tmp_voucher']['tmp_name'][$iCounter];
				$_FILES['voucher']['error'] = $_FILES['tmp_voucher']['error'][$iCounter];
				$_FILES['voucher']['size'] = $_FILES['tmp_voucher']['size'][$iCounter];

				$config['upload_path'] = $path;
				$config['allowed_types'] = 'png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP';
				$config['max_size'] = 3096;//1024 KB = 1 MB
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = '255';
		
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('voucher')){
					$this->db->trans_rollback();
					return array(
						'status' => 'error',
						'message' => 'No se cargo imagen ' . $row['nombre_comercial'] . ' ' . strip_tags($this->upload->display_errors()),
					);
				} else {
					$arrUploadFile = $this->upload->data();
					$Txt_Url_Imagen_Producto = base_url($path . $arrUploadFile['file_name']);

					$Txt_Url_Imagen_Producto = str_replace("https://intranet.probusiness.pe/../../", "https://", $Txt_Url_Imagen_Producto);
					$Txt_Url_Imagen_Producto = str_replace("public_html/", "", $Txt_Url_Imagen_Producto);
				}

				$arrSaleOrderDetail[] = array(
					'ID_Empresa' => $data['ID_Empresa'],
					'ID_Organizacion' => $data['ID_Organizacion'],
					'ID_Pedido_Cabecera' => $where['ID_Pedido_Cabecera'],
					'Txt_Producto' => $row['nombre_comercial'],
					'Txt_Descripcion' => nl2br($row['caracteristicas']),
					'Qt_Producto' => $row['cantidad'],
					'Txt_Url_Imagen_Producto' => $Txt_Url_Imagen_Producto,
					'Txt_Url_Link_Pagina_Producto' => $row['link'],
				);
				++$iCounter;
			}
			$this->db->insert_batch('agente_compra_pedido_detalle', $arrSaleOrderDetail);
		}

		//actualizar productos de tabla de cliente
		if (!empty($arrProductoTable)) {
			foreach($arrProductoTable as $row) {
				//array_debug($row);
				$arrSaleOrderDetailUPD[] = array(
					'ID_Pedido_Detalle' => $row['id_item'],
					'Qt_Producto' => $row['cantidad'],
					'Txt_Descripcion' => nl2br($row['caracteristicas']),
				);
			}

    		$this->db->update_batch('agente_compra_pedido_detalle', $arrSaleOrderDetailUPD, 'ID_Pedido_Detalle');
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'error', 'message' => 'Error al modificar');
		} else {
			//$this->db->trans_rollback();
			$this->db->trans_commit();
			return array('status' => 'success', 'message' => 'Registro modificado');
		}
    }
	
	public function getDownloadImage($id){
		$query = "SELECT Txt_Url_Imagen_Producto FROM agente_compra_pedido_detalle WHERE ID_Pedido_Detalle = " . $id . " LIMIT 1";
		return $this->db->query($query)->row();
	}
	public function generateCotizationCode(){
		$Fe_Month = strtoupper(substr(date('F', strtotime(dateNow('fecha'))), 0, 3));
		$query = "SELECT COUNT(*) AS count FROM agente_compra_pedido_cabecera WHERE Nu_Estado>=2 AND MONTH(Fe_Registro_Hora_Cotizacion) = MONTH(NOW())";
		$Nu_Correlativo = $this->db->query($query)->row()->count + 1;
		$Nu_Correlativo = str_pad($Nu_Correlativo, 4, '0', STR_PAD_LEFT);
		return $Fe_Month . $Nu_Correlativo;
	}
	public function generarCorrelativo(){
		$Nu_Correlativo = 0;
		$Fe_Year = ToYear(dateNow('fecha'));
		//get current month first 3 letters
		$Fe_Month = dateNow('fecha');
		$Fe_Month2 = substr(date('F', strtotime($Fe_Month)), 0, 3);
		$objCorrelativo = $this->db->query("SELECT ID_Agente_Compra_Correlativo FROM agente_compra_correlativo WHERE ID_Empresa = " . $this->user->ID_Empresa . " AND Fe_Year = '" . $Fe_Year . "' AND Fe_Month = '" . $Fe_Month . "' LIMIT 1")->row();
		if(is_object($objCorrelativo)){
			$ID_Agente_Compra_Correlativo = $objCorrelativo->ID_Agente_Compra_Correlativo;
			$query = "UPDATE agente_compra_correlativo SET Nu_Correlativo=Nu_Correlativo + 1 WHERE ID_Agente_Compra_Correlativo=" . $ID_Agente_Compra_Correlativo;
			$this->db->query($query);
		} else {
			$query = "INSERT INTO agente_compra_correlativo(
				ID_Empresa,
				ID_Organizacion,
				Fe_Year,
				Fe_Month,
				Nu_Correlativo
				) VALUES (
				" . $this->user->ID_Empresa . ",
				" . $this->user->ID_Organizacion . ",
				" . $Fe_Year . ",
				" . $Fe_Month . ",
				1
				);";
			$this->db->query($query);
			$ID_Agente_Compra_Correlativo = $this->db->insert_id();
		}
		$Nu_Correlativo = $this->db->query("SELECT Nu_Correlativo FROM agente_compra_correlativo WHERE ID_Agente_Compra_Correlativo = " . $ID_Agente_Compra_Correlativo . " LIMIT 1")->row()->Nu_Correlativo;
		if(!empty($Nu_Correlativo)){
			return array(
				'status' => 'success',
				'result' => array(
					'id_correlativo' => $ID_Agente_Compra_Correlativo,
					'numero_correlativo' => $Nu_Correlativo
				)
			);
		}
		return array(
			'status' => 'error',
			'message' => 'Correlativo es: ' . $Nu_Correlativo
		);
	}

	public function asignarPedido($ID){
		$objPedido = $this->db->query("SELECT CLI.ID_Entidad, CLI.Txt_Email_Entidad FROM " . $this->table . " AS PC JOIN entidad AS CLI ON(PC.ID_Entidad=CLI.ID_Entidad) WHERE PC.ID_Pedido_Cabecera = " . $ID . " LIMIT 1")->row();
		$sCorreo = $objPedido->Txt_Email_Entidad;
		
		$objUser = $this->db->query("SELECT ID_Usuario FROM usuario WHERE No_Usuario = '" . $sCorreo . "' LIMIT 1")->row();

		if(!is_object($objUser)){
			return array(
				'status' => 'error',
				'message' => 'Usuario no existe. ' . $sCorreo
			);
		} else {
			$where = array('ID_Pedido_Cabecera' => $ID);
			$data = array( 'ID_Usuario_Pedido' => $objUser->ID_Usuario );
			if ($this->db->update($this->table, $data, $where) > 0) {
				$where = array('ID_Usuario' => $objUser->ID_Usuario);
				$data = array( 'ID_Entidad' => $objPedido->ID_Entidad );
				if ($this->db->update('usuario', $data, $where) > 0) {
					return array('status' => 'success', 'message' => 'Pedido asignado');
				} else {
					return array('status' => 'error', 'message' => 'Error al enlazar usuario');
				}
			}
			return array('status' => 'error', 'message' => 'Error al asignar pedido');
		}
	}

	public function removerAsignarPedido($ID, $id_usuario){
		$where = array('ID_Pedido_Cabecera' => $ID);
		$data = array( 'ID_Usuario_Pedido' => 0 );
		if ($this->db->update($this->table, $data, $where) > 0) {
			$where = array('ID_Usuario' => $id_usuario);
			$data = array( 'ID_Entidad' => 0 );
			if ($this->db->update('usuario', $data, $where) > 0) {
				return array('status' => 'success', 'message' => 'Elimino asignación');
			} else {
				return array('status' => 'error', 'message' => 'Error al eliminar asignación usuario');
			}
		}
		return array('status' => 'error', 'message' => 'Error al eliminar asignación pedido');
	}
	
	public function generarEstadoProcesoAgenteCompra($arrDataTour){
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '1. Cotizaciones Garantizadas',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosGarantizados/listar',
			'Nu_Orden' => '1',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '1',
			'ID_Usuario_Interno_Empresa' => $this->user->ID_Usuario,
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '2. Selección de Proveedores',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosGarantizados/listar',
			'Nu_Orden' => '2',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '2',
			'ID_Usuario_Interno_Empresa' => $this->user->ID_Usuario,
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '3. (A) Orden de Compra - Pedido',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '3',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '3',
			'ID_Usuario_Interno_Empresa' => $this->user->ID_Usuario,
		);
		
		$proceso_agente_compra_pedido[]=array(
			'ID_Empresa' => $this->user->ID_Empresa,
			'ID_Organizacion' => $this->user->ID_Organizacion,
			'ID_Pedido_Cabecera' => $arrDataTour['ID_Pedido_Cabecera'],
			'No_Proceso' => '3. (B) Orden de Compra - Pagos',
			'Txt_Url_Menu' => 'AgenteCompra/PedidosPagados/listar',
			'Nu_Orden' => '4',
			'Nu_Estado_Proceso' => '0',
			'Nu_Estado_Visualizacion' => '1',
			'Nu_ID_Interno' => '4',
			'ID_Usuario_Interno_Empresa' => $this->user->ID_Usuario,
		);
		
		if ($this->db->insert_batch('proceso_agente_compra_pedido', $proceso_agente_compra_pedido)>0)
			return array('status' => 'success', 'message' => 'Registro guardado');
		return array('status' => 'error', 'message' => 'Error al guardar');
	}

    public function crearUsuarioCliente($data){
		/*
		if($this->db->query("SELECT COUNT(*) AS existe FROM usuario WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Usuario='" . $data['No_Usuario'] . "' LIMIT 1")->row()->existe > 0){
			return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'El usuario ya existe');
		} else {
		*/
		$query = "SELECT ID_Usuario FROM usuario WHERE ID_Organizacion=" . $data['ID_Organizacion'] . " AND No_Usuario='" . $data['No_Usuario'] . "' LIMIT 1";
		$objUsuario = $this->db->query($query)->row();
		if(!is_object($objUsuario)){
			if($data['No_Usuario']=='root'){//1=root
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'No puedes crear un usuario con nombre > "root"');
			}

			if ( ($this->db->insert($this->table_usuario_intero, $data) > 0) ){
			    unset($data['No_Usuario']);
			    unset($data['No_Nombres_Apellidos']);
				unset($data['No_Password']);
				if( isset($data['Nu_Celular']) )
			    	unset($data['Nu_Celular']);
			    unset($data['Txt_Email']);
			    unset($data['Txt_Token_Activacion']);
			    unset($data['No_IP']);
			    unset($data['Nu_Estado']);
			    $data['ID_Usuario'] = $this->db->insert_id();
			    if ($this->db->insert($this->table_grupo_usuario, $data) > 0){
				    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'ID_Usuario' => $data['ID_Usuario']);
			    }
			} else {
				return array('status' => 'error', 'style_modal' => 'modal-warning', 'message' => 'No se creo usuario para cliente');
			}
		} else {
			return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro guardado', 'ID_Usuario' => $objUsuario->ID_Usuario);
		}
		return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al insertar');
    }
}
