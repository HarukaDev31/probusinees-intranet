<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ContenedorConsolidado extends CI_Controller
{

	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		//$this->load->database('LAE_SYSTEMS');
		$this->load->model('CargaConsolidada/ContenedorConsolidadoModel');
		$this->load->model('HelperImportacionModel');
		if (!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
		log_message('error', 'ContenedorConsolidado : __construct()');

	}

	public function listar($ID_Carga_Consolidada = 0)
	{
		if (!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if (isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_contenedor_consolidadado" => true));
			$this->load->view('CargaConsolidada/ContenedorConsolidadoView', array(
				'arrResponseConsolidado' => $arrResponseConsolidado,
				'ID_Carga_Consolidada' => $ID_Carga_Consolidada,
			));
			$this->load->view('footer_v2', array("js_contenedor_consolidadado" => true));
		}
	}
	public function index()
	{
		$arrData = $this->ContenedorConsolidadoModel->index();
		$data = array();
		
		foreach ($arrData as $row) {
			$subdata = array();
			$subdata[] = "Consolidado #" . $row->carga;
			$subdata[] = $row->mes;
			$subdata[] = $row->No_Pais;
			$subdata[] = date("d/m/Y", strtotime($row->f_cierre));
			if ($this->user->No_Grupo == "Coordinación") {
				$subdata[] = date("d/m/Y", strtotime($row->f_puerto));
				$subdata[] = date("d/m/Y", strtotime($row->f_entrega));
			}

			$subdata[] = $row->empresa;



			$divEstado = '<select 
			class="form-control
			' . ($row->estado == "PENDIENTE" ? "bg-warning" : "") .
				($row->estado == "RECIBIENDO" ? "bg-primary" : "") .
				($row->estado == "COMPLETADO" ? "bg-success" : "") . '
			
			" id="estado-' . $row->id . '" name="estado" onchange="updateEstado(' . $row->id . ')">
				<option 
				value="PENDIENTE" ' . ($row->estado == "PENDIENTE" ? "selected" : "") . '>Pendiente</option>
				<option value="RECIBIENDO" ' . ($row->estado == "RECIBIENDO" ? "selected" : "") . '>Recibiendo</option>

				<option value="COMPLETADO" ' . ($row->estado == "COMPLETADO" ? "selected" : "") . '>Completado</option>
			</select>';


			$subdata[] = $divEstado;

			if ($this->user->No_Grupo == "Coordinación") {
				$divAcciones = '<div>
				<i class="fas fa-eye text-primary" style="cursor:pointer; padding:10px;" onclick="viewSteps(' . $row->id . ')"></i>
				<i class="fas fa-edit text-warning" style="cursor:pointer; padding:10px;" onclick="view(' . $row->id . ')"></i>
				<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="deleteCarga(' . $row->id . ')"></i>
				</div>';
			}

			$subdata[] = $divAcciones;
			$data[] = $subdata;
		}
		usort($data, function($a, $b) {
			$numA = (int) substr($a['carga'], 1); // Elimina el '#' y convierte a número
			$numB = (int) substr($b['carga'], 1); // Elimina el '#' y convierte a número
			return $numB - $numA; // Orden descendente
		});
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
	}
	public function getPaises()
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getPaises();
		echo json_encode($arrResponse);
	}
	public function store()
	{
		$data = $this->input->post();
		//parse all f_entrega and f_puerto from dd/mm/yyyy to yyyy-mm-dd
		$data['f_puerto'] = $this->convertDateFormat($data['f_puerto']);
		$data['f_entrega'] = $this->convertDateFormat($data['f_entrega']);
		$data['f_cierre'] = $this->convertDateFormat($data['f_cierre']);
		// $data['f_puerto']=date("Y-m-d", strtotime($data['f_puerto']));
		// $data['f_entrega']=date("Y-m-d", strtotime($data['f_entrega']));
		$response = $this->ContenedorConsolidadoModel->store($data);


		$id = $response['id'];
		$socketResponse = $response['socketResponse'];
		$this->generateSteps($id);
		echo json_encode([
			"status" => $response['status'],
			'id' => $id,
			"socketResponse" => $socketResponse
		]);
	}
	public function update()
	{
		$data = $this->input->post();
		$data['f_puerto'] = $this->convertDateFormat($data['f_puerto']);
		$data['f_entrega'] = $this->convertDateFormat($data['f_entrega']);
		$data['f_cierre'] = $this->convertDateFormat($data['f_cierre']);

		$arrResponse = $this->ContenedorConsolidadoModel->update($data);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function show($id)
	{

		$arrResponse = $this->ContenedorConsolidadoModel->show($id);
		//parse all f_entrega and f_puerto to Y/m/d
		$arrResponse->f_entrega = date("Y/m/d", strtotime($arrResponse->f_entrega));
		$arrResponse->f_puerto = date("Y/m/d", strtotime($arrResponse->f_puerto));

		echo json_encode($arrResponse);
	}
	public function delete()
	{
		$id = $this->input->post('id');
		$arrResponse = $this->ContenedorConsolidadoModel->delete($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function generateSteps($idContenedor)
	{
		$steps = $this->HelperImportacionModel->getCotizacionSteps($idContenedor);
		$result = $this->ContenedorConsolidadoModel->generateSteps($steps);
	}
	public function steps($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getOrderProgress($idContenedor);
		echo json_encode([
			'data' => $arrResponse,
			'status' => "success",
			"currentPrivilege" => $this->user->No_Grupo
		]);
	}
	public function step()
	{
		$stepIndex = $this->input->post('stepIndex');
		$idContenedor = $this->input->post('idContenedor');
		$tipoTabla = $this->input->post('tipoTabla');
		if ($stepIndex == 1) {
			$arrResponse = [];
			if ($tipoTabla == "prospectos") {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacion($idContenedor);
			} else {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacionProveedores($idContenedor);
			}

			$data = array();
			$index = 1;
			foreach ($arrResponse as $row) {
				if ($tipoTabla == "prospectos") {

					$subdata = array();
					$subdata[] = $index;
					$subdata[] = date("d/m/Y", strtotime($row->fecha));
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					$subdata[] = $row->volumen;
					//div with a tag to download file and button to delete file
					$divFile = '<div>';
					if (!empty($row->cotizacion_file_url)) {
						$divFile .= '
						<a href="' . $row->cotizacion_file_url . '" download>
							<i class="fas fa-file-download text-success"></i>
						</a>
						<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacionFile(' . $row->id_cotizacion . ')"></i>';
					} else {
						$divFile .= '
						<i class="fas fa-upload" style="cursor:pointer;" onclick="uploadCotizacionFile(' . $row->id_cotizacion . ')"></i>';
					}
					$subdata[] = $row->monto;
					$subdata[] = $row->tarifa;
					$divFile .= '</div>';
					$subdata[] = $divFile;
					if ($this->user->No_Grupo == "Coordinación") {
						$divAcciones = '<div>
					<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacion(' . $row->id_cotizacion . ')"></i>
					</div>';
						$subdata[] = $divAcciones;
					}
					if ($this->user->No_Grupo == "Cotizador") {
						$divEstadoCotizador = '<select 
						class="form-control
						' . ($row->estado_cotizador == "PENDIENTE" ? "bg-light" : "") .

							($row->estado_cotizador == "CONFIRMADO" ? "bg-success" : "") . '
						
						" id="estado-cotizador-' . $row->id_cotizacion . '" name="estado" onchange="updateEstadoCotizador(' . $row->id_cotizacion . ')">
							<option 
							value="PENDIENTE" ' . ($row->estado_cotizador == "PENDIENTE" ? "selected" : "") . '>PENDIENTE</option>
	
							<option value="CONFIRMADO" ' . ($row->estado_cotizador == "CONFIRMADO" ? "selected" : "") . '>CONFIRMADO</option>
						</select>';
						$subdata[] = $divEstadoCotizador;
						if ($row->estado_cotizador == "PENDIENTE") {
							$divAcciones = '<div>
							<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacion(' . $row->id_cotizacion . ')"></i>
							</div>';
						}
						$subdata[] = $divAcciones;
					}
					$data[] = $subdata;
				} else {
					$subdata = array();
					if ($this->user->No_Grupo != "ContenedorAlmacen") {
						$subdata[] = $row->No_Usuario;
					}
					$proveedores = $row->proveedores;
					//check if is posible to decode json
					if ($proveedores == null) {
						$proveedores = [];
					} else {
						$proveedores = json_decode($proveedores);
					}

					//foreach proveedores add to select
					$proveedoresSelect = "";
					$estadoSelect = "";
					$qtyBoxDiv = "";
					$cbmTotalDiv = "";
					$pesoTotalDiv = "";
					$divInputsSupplier = "";
					$divInputsCodeSupplier = "";
					$divInputsPhoneNumberSupplier = "";
					$divInputQtyChina = "";
					$divInputCBMChina = "";
					$divInputArriveDateChina = "";
					$divProductos = "";
					$divViewBtn = "";
					$divAcciones = "";
					$divEstadoChina = "";

					foreach ($proveedores as $proveedor) {
						//validate is number
						if (is_numeric($proveedor->cbm_total_china)) {
							$cbmTotalChina += $proveedor->cbm_total_china;
						}
						if (is_numeric($proveedor->cbm_total)) {
							$cbmTotalPeru += $proveedor->cbm_total;
						}

						if ($this->user->No_Grupo == "ContenedorAlmacen") {
							$proveedoresSelect .= '<select class="form-control
						' . ($proveedor->estados_proveedor == "NC" ? "bg-info" : "") .
								($proveedor->estados_proveedor == "C" ? "bg-warning" : "") .
								($proveedor->estados_proveedor == "R" ? "bg-success" : "") .
								($proveedor->estados_proveedor == "NS" ? "bg-danger" : "") .
								($proveedor->estados_proveedor == "INSPECTION" ? "bg-primary" : "") .
								($proveedor->estados_proveedor == "LOADED" ? "bg-success" : "") .
								($proveedor->estados_proveedor == "NO LOADED" ? "bg-danger" : "") . '	
						" id="estado-' . $proveedor->id_proveedor . '" name="estado" onchange="updateEstadoProveedor(' . $row->id . ',' . $proveedor->id_proveedor . ')">
							<option 
							class="bg-info"
							value="NC"' . ($proveedor->estados_proveedor == "NC" ? "selected" : "") . '>NC</option>
							<option value="C" 
							class="bg-warning"
							' . ($proveedor->estados_proveedor == "C" ? "selected" : "") . '>C</option>
							<option value="R"
							class="bg-success"
							' . ($proveedor->estados_proveedor == "R" ? "selected" : "") . '>R</option>
							<option value="NS"
							class="bg-danger"
							' . ($proveedor->estados_proveedor == "NS" ? "selected" : "") . '>NS</option>
							<option value="INSPECTION" ' . ($proveedor->estados_proveedor == "INSPECTION" ? "selected" : "") . '>INSPECTION</option>
							<option value="LOADED" ' . ($proveedor->estados_proveedor == "LOADED" ? "selected" : "") . '>LOADED</option>
							<option value="NO LOADED" ' . ($proveedor->estados_proveedor == "NO LOADED" ? "selected" : "") . '>NO LOADED</option>
							
						</select>';
							$divInputQtyChina .= '<div class="d-flex flex-row gap-2">
						<input type="text" class="form-control mb-1" id="qty-china-' . $proveedor->id_proveedor . '" name="qty" value="' . $proveedor->qty_box_china . '">
					
						</div>';
							$divInputCBMChina .= '<div class="d-flex flex-row gap-2">
						<input type="text" class="form-control mb-1" id="cbm-china-' . $proveedor->id_proveedor . '" name="cbm" value="' . $proveedor->cbm_total_china . '">
					
						</div>';
							$divInputArriveDateChina .= '<div class="d-flex flex-row gap-2">
						<input type="text" class="form-control input-date mb-1"  id="arrive-date-china-' . $proveedor->id_proveedor . '" name="arrive-date" value="' .
								($proveedor->arrive_date_china ? date("d-m-Y", strtotime($proveedor->arrive_date_china)) : $proveedor->arrive_date_china) . '">
						</div>';
						} else {
							$proveedoresSelect .= '<select class="form-control mb-1
						' . ($proveedor->estados_proveedor == "NC" ? "bg-info" : "") .
								($proveedor->estados_proveedor == "C" ? "bg-warning" : "") .
								($proveedor->estados_proveedor == "R" ? "bg-success" : "") .
								($proveedor->estados_proveedor == "NS" ? "bg-danger" : "") .
								($proveedor->estados_proveedor == "INSPECTION" ? "bg-primary" : "") .
								($proveedor->estados_proveedor == "LOADED" ? "bg-success" : "") .
								($proveedor->estados_proveedor == "NO LOADED" ? "bg-danger" : "") . '	
						" id="estado-' . $proveedor->id_proveedor . '" name="estado" disabled onchange="updateEstadoProveedor(' . $row->id . ',' . $proveedor->id_proveedor . ')">
							<option 
							class="bg-info"
							value="NC"' . ($proveedor->estados_proveedor == "NC" ? "selected" : "") . '>NC</option>
							<option value="C" 
							class="bg-warning"
							' . ($proveedor->estados_proveedor == "C" ? "selected" : "") . '>C</option>
							<option value="R"
							class="bg-success"
							' . ($proveedor->estados_proveedor == "R" ? "selected" : "") . '>R</option>
							<option value="NS"
							class="bg-danger"
							' . ($proveedor->estados_proveedor == "NS" ? "selected" : "") . '>NS</option>
							<option value="INSPECTION" ' . ($proveedor->estados_proveedor == "INSPECTION" ? "selected" : "") . '>INSPECTION</option>
							<option value="LOADED" ' . ($proveedor->estados_proveedor == "LOADED" ? "selected" : "") . '>LOADED</option>
							<option value="NO LOADED" ' . ($proveedor->estados_proveedor == "NO LOADED" ? "selected" : "") . '>NO LOADED</option>
							
						</select>';

							$divInputQtyChina .= '<div>
						<input disabled  class="form-control mb-1" 
							value="' . ($proveedor->qty_box_china ?? 0) . '"
						></input>
						</div>';
							$divInputCBMChina .= '<div>
						<input disabled  class="form-control mb-1"
							value="' . ($proveedor->cbm_total_china ?? 0) . '"
						></input>
						</div>';
							$divInputArriveDateChina .= '<div>
						<input disabled  class="form-control mb-1"
							value="' . ($proveedor->arrive_date_china ? date("d-m-Y", strtotime($proveedor->arrive_date_china)) : $proveedor->arrive_date_china) . '"
						</div>';
						}
						//add select with status enum("ROTULADO","DATOS PROVEEDOR","INSPECCIONADO","RESERVADO","EMBARCADO","NO EMBARCADO"),

						$estadoSelect .= '<select class="form-control mb-1"' . ($this->user->No_Grupo == "Cotizador" ? "disabled" : "") . '
					id="estado-' . $row->id . '-' . $proveedor->id_proveedor . '"
					name="estado" onchange="updateEstadoCotizacionProveedor(' . $row->id . ',' . $proveedor->id . ',' . $row->estados . ')">
						<option value="DEFAULT" ' . ($proveedor->estados == "" ? "selected disabled" : "") . '>--Seleccionar--</option>
						<option value="ROTULADO" ' . ($proveedor->estados == "ROTULADO" ? "selected" : "") . '>ROTULADO</option>
						<option value="DATOS PROVEEDOR" ' . ($proveedor->estados == "DATOS PROVEEDOR" ? "selected disabled" : "disabled") . '>DATOS PROVEEDOR</option>
						<option value="INSPECCIONADO" ' . ($proveedor->estados == "INSPECCIONADO" ? "selected" : "") . '' . ($this->user->No_Grupo !== "ContenedorAlmacen" ? " disabled" : "") . '		>INSPECCIONADO</option>
						<option value="RESERVADO" ' . ($proveedor->estados == "RESERVADO" ? "selected" : "") . '>RESERVADO</option>
						<option value="EMBARCADO" ' . ($proveedor->estados == "EMBARCADO" ? "selected" : "") . '' . ($this->user->No_Grupo !== "ContenedorAlmacen" ? "disabled" : "") . '
						>EMBARCADO</option>
						<option value="NO EMBARCADO" ' . ($proveedor->estados == "NO EMBARCADO" ? "selected" : "") . '' . ($this->user->No_Grupo !== "ContenedorAlmacen" ? "disabled" : "") . '>NO EMBARCADO</option>
					</select>';

						$qtyBoxDiv .= '<div>
					<input disabled  class="form-control mb-1" 
					value="' . ($proveedor->qty_box ?? 0) . '"
					></input>
					</div>';
						$cbmTotalDiv .= '<div>
					<input disabled  class="form-control mb-1" value="' . ($proveedor->cbm_total ?? 0) . '"></input>
					</div>';
						$pesoTotalDiv .= '<div>
					<input disabled  class="form-control mb-1" value="' . ($proveedor->peso ?? 0) . '"></input>
					</div>';
						//add inputs to supplier
						$divInputsSupplier .= '<div class="d-flex flex-row mb-1">
					<input type="text" class="form-control"' . ($this->user->No_Grupo == "Cotizador" ? "disabled" : "") . '
					
					id="proveedor-' . $proveedor->id_proveedor . '" name="proveedor" value="' . $proveedor->supplier . '"
					' . ($this->user->No_Grupo == "ContenedorAlmacen" ? "disabled" : "") . '>
				
					</div>';
						$divInputsCodeSupplier .= '<div class="d-flex flex-row mb-1">
					<input type="text" class="form-control"' . ($this->user->No_Grupo == "Cotizador" ? "disabled" : "") . ' id="codigo-' . $proveedor->id_proveedor . '" name="codigo" value="' . $proveedor->code_supplier . '"
					' . ($this->user->No_Grupo == "ContenedorAlmacen" ? "disabled" : "") . '>
				
					</div>';
						$divInputsPhoneNumberSupplier .= '<div class="d-flex flex-row mb-1">
					<input type="text" class="form-control"'
							. ($this->user->No_Grupo == "Cotizador" ? "disabled" : "") . ' 
					id="telefono-' . $proveedor->id_proveedor . '" name="telefono" value="' . $proveedor->supplier_phone . '"
					' . ($this->user->No_Grupo == "ContenedorAlmacen" ? "disabled" : "") . '>
				
					</div>';
						$divProductos .= '<div class="d-flex flex-row gap-2 mb-1">
					<input type="text" class="form-control cotizacion-products-' . $row->id . '"
					
					id="productos-' . $proveedor->id_proveedor . '" name="productos" value="' . $proveedor->products . '"
					' . ($this->user->No_Grupo == "ContenedorAlmacen" ? "disabled" : "") . '
					>
					</input>
			
					</div>';
						$divViewBtn .= '<div  class="btn btn-outline-primary mb-1"
					onclick="verCotizacionEmbarque(
						' . $proveedor->id_proveedor . ',
						' . $row->id . ',
						\'' . addslashes($proveedor->code_supplier) . '\',
						\'' . addslashes($row->nombre) . '\'
					)"
					>
					<i class="fas fa-eye" style="cursor:pointer;" 
					></i>
					</div>';
						$divAcciones .=
							'<div class="d-flex flex-row gap-1">';
						if ($this->user->No_Grupo == "Coordinación") {
							$divAcciones .= '<div class="btn btn-outline-danger mb-1" onclick="deleteCotizacion(' . $row->id . ',' . $proveedor->id_proveedor . ')"> 
						<i class="fas fa-trash text-danger" style="cursor:pointer;" "></i>
						</div>';
						}
						$divAcciones .= '<div class="btn btn-outline-success mb-1"  onclick="updateProveedorData(' . $row->id . ',' . $proveedor->id_proveedor . ')">
					<i class="fas fa-save text-success" style="cursor:pointer;"></i>
					</div>
					</div>';

						// '<div class="btn btn-outline-danger mb-1">
						// '.($this->user->No_Grupo!="ContenedorAlmacen" ? '<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacion('.$row->id.','.$proveedor->id_proveedor.')"></i>' : '').'
						// </div>'.
						// '<div class="btn btn-outline-success mb-1"> 					<i class="fas fa-save text-success" style="cursor:pointer;" onclick="updateProveedorData('.$row->id. ','.$proveedor->id_proveedor.')"></i>
						// </div>'.
						// '</div>';
					}
					//input productos with with button to save text in input and call function to save

					//div view with fa eye icon call function verCotizacionEmbarque

					$subdata[] = $proveedoresSelect;
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					if ($this->user->No_Grupo != "ContenedorAlmacen") {
						$subdata[] = $row->telefono;
						$subdata[] = $estadoSelect;
					}
					$subdata[] = $divProductos;
					$subdata[] = $qtyBoxDiv;
					$subdata[] = $cbmTotalDiv;
					$subdata[] = $pesoTotalDiv;
					$subdata[] = $divInputsSupplier;
					$subdata[] = $divInputsCodeSupplier;
					$subdata[] = $divInputsPhoneNumberSupplier;
					$subdata[] = $divInputQtyChina;
					$subdata[] = $divInputCBMChina;
					$subdata[] = $divInputArriveDateChina;
					$subdata[] = $divViewBtn;
					$subdata[] = $divAcciones;
					$data[] = $subdata;
				}
				$index++;
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
			// echo json_encode(['data' => $arrResponse,'status' => "success"]);
		} else if ($stepIndex == 2) {
			$arrResponse = $this->ContenedorConsolidadoModel->getContenedorClientes($idContenedor);
			$data = array();
			$index = 1;
			$tipoTabla = $this->input->post('tipoTabla');
			foreach ($arrResponse as $row) {
				if ($tipoTabla == "general") {
					$subdata = array();
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					$subdata[] = $row->volumen;
					$subdata[] = $row->monto;
					$subdata[] = $row->tarifa;

					$btnView = '<div  onclick="viewClientesDocumentacion(' . $row->id_cotizacion . ')">
				<i class="fas fa-eye" style="cursor:pointer;"></i>
				</div>';
					$subdata[] = $btnView;
					$selectEstadoCliente = "";
					if ($this->user->No_Grupo == "Coordinación") {
						$selectEstadoCliente = '<select class="form-control" id="estado-cliente-' . $row->id_cotizacion . '" name="estado" onchange="updateEstadoCliente(' . $row->id_cotizacion . ')">
						<option value="RESERVADO" ' . ($row->estado_cliente == "RESERVADO" ? "selected" : "") . '>RESERVADO</option>
						<option value="NO RESERVADO" ' . ($row->estado_cliente == "NO RESERVADO" ? "selected" : "") . '>NO RESERVADO</option>
						<option value="DOCUMENTACION" ' . ($row->estado_cliente == "DOCUMENTACION" ? "selected" : "") . '>DOCUMENTACION</option>
						<option value="C FINAL" ' . ($row->estado_cliente == "C FINAL" ? "selected" : "") . '>C FINAL</option>
						<option value="FACTURADO" ' . ($row->estado_cliente == "FACTURADO" ? "selected" : "") . '>FACTURADO</option>
					</select>';
					}
					$subdata[] = $selectEstadoCliente;
					if ($this->user->No_Grupo == "Coordinación") {
						$divAcciones = '<div onclick="deleteCliente(' . $row->id_cotizacion . ')">
					<i class="fas fa-trash text-danger" style="cursor:pointer;" ></i>
					</div>';
					}
					$subdata[] = $divAcciones;
					$data[] = $subdata;
					$index++;
				} else {
					$subdata = array();
					$volSelected = $row->vol_selected;
					$divValidacion = "";
					//if volumen , volumen china and volumen doc are not equal or valor_cot and valor_doct badge danger SI ELSE NO
					if ($row->volumen != $row->volumen_china || $row->volumen != $row->volumen_doc || $row->volumen_china != $row->volumen_doc || $row->valor_cot != $row->valor_doc) {
						$divValidacion = '<div class="badge badge-danger">SI</div>';
					} else {
						$divValidacion = '<div class="badge badge-success">NO</div>';
					}
					//for each type of  volumen  a div with background color diferent, and if vol_selected text equeal to type not add check icon to select else yes
					$divVol = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span class="badge badge-primary">' . ($row->volumen ?? 0) . '</span>
					' . ($volSelected != "volumen" ? '<i class="fas fa-check text-success "
					onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen\')"></i>' : '') . '
					</div>
				</div>';
					$divVolChina = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span class="badge badge-primary">' . ($row->volumen_china ?? 0) . '</span>
					' . ($volSelected != "volumen_china" ? '<i class="fas fa-check text-success" onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen_china\')"></i>' : '') . '
					</div>
				</div>';
					$divVolDoc = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span class="badge badge-primary">' . ($row->volumen_doc ?? 0) . '</span>
					' . ($volSelected != "volumen_doc" ? '<i class="fas fa-check text-success" onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen_doc\')"></i>' : '') . '
					</div>
				</div>';
					$divValorCot = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span class="badge badge-success">' . ($row->valor_cot ?? 0) . '</span></div>';
					$divValorDoc = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span class="badge badge-success">' . ($row->valor_doc ?? 0) . '</span></div>';

					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->name;
					$subdata[] = $row->tarifa;
					$subdata[] = $divVol;
					$subdata[] = $divVolChina;
					$subdata[] = $divVolDoc;
					$subdata[] = $divValorCot;
					$subdata[] = $divValorDoc;
					$subdata[] = $divValidacion;

					$data[] = $subdata;
					$index++;
				}
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} else if ($stepIndex == 3) {
			$arrResponse = $this->ContenedorConsolidadoModel->getDocumentationFolderFiles($idContenedor);
			echo json_encode($arrResponse);
		} else if($stepIndex==4){
			$arrResponse= $this->ContenedorConsolidadoModel->getContenedorCotizacionesFinales($idContenedor);
			$data = array();
			$index = 1;
			foreach ($arrResponse as $row) {
					$subdata = array();
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					$subdata[] = $row->volumen_final;
					$subdata[] = $row->monto_final;
					$subdata[] = $row->tarifa_final;
					$selectEstados= '<select class="form-control" id="estado-cotizacion-final' . $row->id_cotizacion . '" name="estado" onchange="updateEstadoCotizacionFinal(' . $row->id_cotizacion. ')">
					<option value="PENDIENTE" ' . ($row->estado_cotizacion_final == "PENDIENTE" ? "selected" : "") . '>PENDIENTE</option>
					<option value="COTIZADO" ' . ($row->estado_cotizacion_final == "COTIZADO" ? "selected" : "") . '>COTIZADO</option>
					<option value="AJUSTADO" ' . ($row->estado_cotizacion_final == "AJUSTADO" ? "selected" : "") . '>AJUSTADO</option>';
					$subdata[] = $selectEstados;
					//if cotizacion_final_url not null div with excel icon to download file else div with upload icon to upload file
					$divFile = '<div>';
					if (!empty($row->cotizacion_final_url)) {
						$divFile .= '<div class="d-flex flex-row gap-2">
						<a href="' . $row->cotizacion_final_url . '" download>
							<i class="fas fa-file-excel text-success"></i>

						</a>
						<i class="fas fa-file-pdf text-danger"
						onclick="descargarBoletaPDF(' . $row->id_cotizacion . ')" ></i>
			
						<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacionFinalFile(' . $row->id_cotizacion . ')"></i>
						
						</div>
						';
					} else {
						$divFile .= '
						<i class="fas fa-upload" style="cursor:pointer;" onclick="uploadCotizacionFinal(' . $row->id_cotizacion . ')"></i>';
					}
					$divFile .= '</div>';
					$subdata[] = $divFile;
					$data[] = $subdata;
					$index++;

				}
			
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} else if($stepIndex==5){
			$arrResponse= $this->ContenedorConsolidadoModel->getContenedorFacturaGuia($idContenedor);
			$data = array();
			$index = 1;
			foreach ($arrResponse as $row) {
					$subdata = array();
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					$subdata[] ='<div class="badge badge-'.($row->estado_cotizacion_final=="AJUSTADO" ? "danger" : "success").'">'.
					($row->estado_cotizacion_final=="AJUSTADO" ? "SI" : "NO").'</div>';

					//if cotizacion_final_url exists add icon download
					$divFile = '<div>';
					if(!empty($row->cotizacion_final_url)){
						$divFile .= '<div class="d-flex flex-row gap-2">
						<a href="'.$row->cotizacion_final_url.'" download>
							<i class="fas fa-file-excel text-success"></i>
						</a>						
						</div>
						';
					}
					$divFile .= '</div>';
					$subdata[] = $divFile;
					//if factura_general_url is not null add download and delete button else add upload button
					$divFile = '<div>';
					if (!empty($row->factura_general_url)) {
						$divFile .= '<div class="d-flex flex-row gap-2">
						<a href="' . $row->factura_general_url . '" download>
							<i class="fas fa-file-excel text-success"></i>

						</a>
						<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteFacturaGeneralFile(' . $row->id_cotizacion . ')"></i>
						
						</div>
						';
					} else {
						$divFile .= '
						<i class="fas fa-upload" style="cursor:pointer;" onclick="uploadFacturaGeneral(' . $row->id_cotizacion . ')"></i>';
					}
					$divFile .= '</div>';
					$subdata[] = $divFile;
					//if guia_remision_url is not null add download and delete button else add upload button
					$divFile = '<div>';
					if (!empty($row->guia_remision_url)) {
						$divFile .= '<div class="d-flex flex-row gap-2">
						<a href="' . $row->guia_remision_url . '" download>
							<i class="fas fa-file-excel text-success"></i>

						</a>
						<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteGuiaRemisionFile(' . $row->id_cotizacion . ')"></i>
						
						</div>
						';
					} else {
						$divFile .= '
						<i class="fas fa-upload" style="cursor:pointer;" onclick="uploadGuiaRemision(' . $row->id_cotizacion . ')"></i>';
					}
					$divFile .= '</div>';
					$subdata[] = $divFile;
					$data[] = $subdata;
					$index++;

				}
			
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		}
	}
	public function getCotizacionEmbarqueHeaders($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getCotizacionEmbarqueHeaders($idContenedor);
		echo json_encode($arrResponse);
	}
	public function storeCotizacion()
	{
		try {
			$data = $this->input->post();
			$cotizacion = $_FILES['cotizacion'];

			$response = $this->ContenedorConsolidadoModel->storeCotizacion($data, $cotizacion);
			echo json_encode([
				"status" => $response['status'],
			]);
		} catch (Exception $e) {
			echo json_encode([
				"status" => false,
				"message" => $e->getMessage()
			]);
		}
	}
	public function getTipoCliente()
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getTipoCliente();
		echo json_encode($arrResponse);
	}
	public function deleteCotizacionFile($idCotizacion)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacionFile($idCotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteCotizacion($idCotizacion)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacion($idCotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadCotizacionFile()
	{
		$idCotizacion = $this->input->post('id');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadCotizacionFile($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function showCotizacion($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->showCotizacion($id);
		echo json_encode($arrResponse);
	}
	public function updateCotizacion()
	{
		$data = $this->input->post();
		$cotizacion = $_FILES['cotizacion'];

		$arrResponse = $this->ContenedorConsolidadoModel->updateCotizacion($data, $cotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateEstadoCotizacion()
	{
		$id = $this->input->post('id');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizacion($id, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateEstado()
	{
		$id = $this->input->post('id');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstado($id, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function showClientesDocumentacion($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->showClientesDocumentacion($id);
		echo json_encode($arrResponse);
	}
	public function createClienteDocumentacion()
	{
		$id_cotizacion = $this->input->post('id');
		$name = $this->input->post('name');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->createClienteDocumentacion($id_cotizacion, $name, $file);
		echo json_encode($arrResponse);
	}
	public function deleteClienteDocumentacionFile($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteClienteDocumentacionFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateClienteDocumentacion()
	{
		$data = $this->input->post();
		$arrResponse = $this->ContenedorConsolidadoModel->updateClienteDocumentacion($data, $_FILES);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function validateListEmbarque($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->validateListEmbarque($idContenedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadListaEmbarque()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$idContenedor = $this->input->post('idContenedor');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadListaEmbarque($idCotizacion, $idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse['status'],
			"error" => $arrResponse['message']
		]);
	}
	function updateEstadoCliente()
	{
		$id = $this->input->post('id');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCliente($id, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteFacturaComercial($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFacturaComercial($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteExcelConfirmacion($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteExcelConfirmacion($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteCliente($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCliente($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadFileDocumentation()
	{
		$idFolder = $this->input->post('idFolder');
		$idContenedor = $this->input->post('idContenedor');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadFileDocumentation($idFolder, $idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteDocumentacionFile($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteDocumentacionFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteDocumentacionFolder($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteDocumentacionFolder($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function createDocumentacionFolder()
	{
		$name = $this->input->post('name');
		$idContenedor = $this->input->post('idContenedor');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->createDocumentacionFolder($name, $idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse['status'],
			"error" => $arrResponse['error']
		]);
	}
	public function downloadDocumentacionZip($idContenedor)
	{
		$zipFilePath = $this->ContenedorConsolidadoModel->downloadDocumentacionZip($idContenedor);
		if (file_exists($zipFilePath)) {
			ob_end_clean();

			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
			header('Content-Length: ' . filesize($zipFilePath));
			readfile($zipFilePath);
			unlink($zipFilePath);
			exit();
		} else {
			// Handle error if file generation failed
			echo "Error: Unable to generate the ZIP file.";
		}
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function downloadFacturaComercial($idContenedor)
	{
		try {
			ob_end_clean();
			$objExcel = $this->ContenedorConsolidadoModel->downloadFacturaComercial($idContenedor);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Factura_Comercial.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
			$objWriter->save('php://output');
		} catch (Exception $e) {
			echo json_encode([
				"status" => false,
				"message" => $e->getMessage()
			]);
		}
	}
	public function updateEstadoCotizacionProveedor()
	{	ob_end_clean();
		$idCotizacion = $this->input->post('idCotizacion');
		$idProveedor = $this->input->post('idProveedor');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizacionProveedor($idCotizacion, $idProveedor, $estado);
		if ($estado = !"ROTULADO") {
			echo json_encode([
				"status" => $arrResponse
			]);
			return;
		} else {
			echo json_encode([
				"status" => $arrResponse
			]);
		}
	}
	public function updateTelefonoProveedor()
	{
		$idProveedor = $this->input->post('idProveedor');
		$telefono = $this->input->post('telefono');
		$arrResponse = $this->ContenedorConsolidadoModel->updateTelefonoProveedor($idProveedor, $telefono);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateProveedor()
	{
		$idProveedor = $this->input->post('idProveedor');
		$proveedor = $this->input->post('supplier');
		$arrResponse = $this->ContenedorConsolidadoModel->updateProveedor($idProveedor, $proveedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateQtyChina()
	{
		$idProveedor = $this->input->post('idProveedor');
		$qty = $this->input->post('qtyChina');
		$arrResponse = $this->ContenedorConsolidadoModel->updateQtyChina($idProveedor, $qty);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateCBMChina()
	{
		$idProveedor = $this->input->post('idProveedor');
		$cbm = $this->input->post('cbmChina');
		$arrResponse = $this->ContenedorConsolidadoModel->updateCBMChina($idProveedor, $cbm);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateArriveDateChina()
	{
		$idProveedor = $this->input->post('idProveedor');
		$arriveDate = $this->input->post('arriveDateChina');
		$arrResponse = $this->ContenedorConsolidadoModel->updateArriveDateChina($idProveedor, $arriveDate);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateProductos()
	{
		$idProveedor = $this->input->post('idProveedor');
		$productos = $this->input->post('productos');
		$arrResponse = $this->ContenedorConsolidadoModel->updateProductos($idProveedor, $productos);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateEstadoProveedor()
	{
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizacionProveedor($idCotizacion, $idProveedor, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function downloadContenedorCotizacionProveedoresExcel($idContenedor)
	{
		$objExcel = $this->ContenedorConsolidadoModel->downloadContenedorCotizacionProveedoresExcel($idContenedor);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Cotizacion_Proveedores.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	public function uploadFileInspection()
	{
		$idProveedor = $this->input->post('idProveedor');
		//files file[] array
		$files = $_FILES;
		$arrResponse = $this->ContenedorConsolidadoModel->uploadFileInspection($idProveedor, $files);
		if ($arrResponse) {
			echo json_encode([
				"status" => $arrResponse['status'],
				"data" => $arrResponse['data'],
				'error' => $arrResponse['error']
			]);
			return;
		}
		echo json_encode([
			"status" => $arrResponse['status'],
			"error" => $arrResponse['error']
		]);
	}
	public function deleteFile($fileId)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFile($fileId);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function verCotizacionEmbarqueFiles($idProveedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->verCotizacionEmbarqueFiles($idProveedor);
		echo json_encode($arrResponse);
	}
	public function updateProveedorData()
	{
		$data = $this->input->post('data');
		$idProveedor = $this->input->post('idProveedor');
		$arrResponse = $this->ContenedorConsolidadoModel->updateProveedorData($data, $idProveedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadFileDocument()
	{
		$file = $_FILES['file'];
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$arrResponse = $this->ContenedorConsolidadoModel->uploadFileDocument($file, $idProveedor, $idCotizacion);
		echo json_encode($arrResponse);
	}
	public function uploadFileAlmacenInspection()
	{
		$file = $_FILES['file'];
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$arrResponse = $this->ContenedorConsolidadoModel->uploadFileAlmacenInspection($file, $idProveedor, $idCotizacion);
		echo json_encode($arrResponse);
	}
	public function getFilesAlmacenDocument($idProveedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getFilesAlmacenDocument($idProveedor);
		echo json_encode($arrResponse);
	}
	public function getFilesAlmacenInspection($idProveedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getFilesAlmacenInspection($idProveedor);
		echo json_encode($arrResponse);
	}
	public function uploadBL()
	{
		$file = $_FILES['file'];
		$idContenedor = $this->input->post('id');
		$arrResponse = $this->ContenedorConsolidadoModel->uploadBL($idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateVolSelected()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$volSelected = $this->input->post('type');
		$arrResponse = $this->ContenedorConsolidadoModel->updateVolSelected($idCotizacion, $volSelected);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function getValidContainers()
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getValidContainers();
		echo json_encode($arrResponse);
	}
	public function deleteBL($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteBL($idContenedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteListaEmbarque($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteListaEmbarque($idContenedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function addNote()
	{
		$note = $this->input->post('note');
		$idProveedor = $this->input->post('idProveedor');
		$arrResponse = $this->ContenedorConsolidadoModel->addNote($note, $idProveedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function getNotes($idProveedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getNotes($idProveedor);
		echo json_encode($arrResponse);
	}
	public function updateEstadoCotizador()
	{
		$estado = $this->input->post('estado');
		$id = $this->input->post('id');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizador($id, $estado);
		echo json_encode([
			"status" => $arrResponse,
			"message" => "No todos los proveedores tienen productos"

		]);
	}
	public function getClientesHeader($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getClientesHeader($idContenedor);
		echo json_encode($arrResponse);
	}
	public function uploadGeneral()
	{
		$file = $_FILES['file'];
		$idContenedor = $this->input->post('idContenedor');
		$arrResponse = $this->ContenedorConsolidadoModel->uploadGeneral($idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse['status']
		]);
	}
	public function downloadPlantillaGeneral($idContenedor)
	{
		ob_end_clean();
		$objExcel = $this->ContenedorConsolidadoModel->downloadPlantillaGeneral($idContenedor);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Plantilla_General.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	public function generateMassiveExcelPayrolls()
	{
		ob_end_clean();
		$idContenedor= $this->input->post('idContenedor');
		$fileTmpPath = $_FILES['file']['tmp_name'];
		$fileName = $_FILES['file']['name'];
		$fileSize = $_FILES['file']['size'];
		$fileType = $_FILES['file']['type'];
		$fileNameCmps = explode(".", $fileName);
		$fileExtension = strtolower(end($fileNameCmps));
		$this->load->library('PHPExcel');
		$objPHPExcel = PHPExcel_IOFactory::load($fileTmpPath);
		$zipFilePath = $this->ContenedorConsolidadoModel->generateMassiveExcelPayrolls($objPHPExcel, $idContenedor);
		
		if (file_exists($zipFilePath)) {
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
			header('Content-Length: ' . filesize($zipFilePath));
			readfile($zipFilePath);
			unlink($zipFilePath);
			exit();
		} else {
			// Handle error if file generation failed
			echo "Error: Unable to generate the ZIP file.";
		}
		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename="Plantilla_General.xlsx"');
		// header('Cache-Control: max-age=0');
		// $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
		// $objWriter->save('php://output');
	}
	public function updateEstadoCotizacionFinal(){
		$idCotizacionFinal= $this->input->post('idCotizacionFinal');
		$estado= $this->input->post('estado');
		$arrResponse= $this->ContenedorConsolidadoModel->updateEstadoCotizacionFinal($idCotizacionFinal, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
		
	}
	public function deleteCotizacionFinalFile($id){
		$arrResponse= $this->ContenedorConsolidadoModel->deleteCotizacionFinalFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadCotizacionFinal(){
		$idCotizacionFinal= $this->input->post('idCotizacionFinal');
		$file= $_FILES['file'];
		$arrResponse= $this->ContenedorConsolidadoModel->uploadCotizacionFinal($idCotizacionFinal, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function downloadBoleta($idCotizacionFinal){
		ob_end_clean();
		$this->ContenedorConsolidadoModel->downloadBoleta($idCotizacionFinal);
		
	}
	public function uploadFacturaGeneral(){
		$idCotizacion= $this->input->post('idCotizacion');
		$file= $_FILES['file'];
		$arrResponse= $this->ContenedorConsolidadoModel->uploadFacturaGeneral($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadGuiaRemision(){
		$idCotizacion= $this->input->post('idCotizacion');
		$file= $_FILES['file'];
		$arrResponse= $this->ContenedorConsolidadoModel->uploadGuiaRemision($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteFacturaGeneralFile($id){
		$arrResponse= $this->ContenedorConsolidadoModel->deleteFacturaGeneralFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteGuiaRemisionFile($id){
		$arrResponse= $this->ContenedorConsolidadoModel->deleteGuiaRemisionFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function receiveMessage(){
		$inputPost= $this->input->post();
		log_message('error', $inputPost);
	}
	function convertDateFormat($date)
	{
		$dateObject = DateTime::createFromFormat('d/m/Y', $date);
		return $dateObject ? $dateObject->format('Y-m-d') : null; // Devuelve null si la fecha no es válida
	}

}
