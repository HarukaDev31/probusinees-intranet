<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ContenedorConsolidado extends CI_Controller
{

	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	private $roleGerencia = "GERENCIA";
	function __construct()
	{
		try {
			parent::__construct();
			$this->load->library('session');
			//$this->load->database('LAE_SYSTEMS');
			$this->load->model('CargaConsolidada/ContenedorConsolidadoModel');
			$this->load->model('HelperImportacionModel');
			if (!isset($this->session->userdata['usuario'])) {
				redirect('');
			}
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : __construct() => ' . $e->getMessage());
		}
	}

	public function listar($ID_Carga_Consolidada = 0)
	{
		try {
			if (! $this->MenuModel->verificarAccesoMenu()) {
				redirect('Inicio/InicioView');
			}

			if (isset($this->session->userdata['usuario'])) {
				$this->load->view('header_v2', ["js_contenedor_consolidadado" => true]);
				$this->load->view('CargaConsolidada/ContenedorConsolidadoView', [
					'arrResponseConsolidado' => [],
					'ID_Carga_Consolidada'   => $ID_Carga_Consolidada,
				]);
				$this->load->view('footer_v2', ["js_contenedor_consolidadado" => true]);
			}
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : listar() => ' . $e->getMessage());
		}
	}
	public function listarCompletados($ID_Carga_Consolidada = 0)
	{
		try {
			if (! $this->MenuModel->verificarAccesoMenu()) {
				redirect('Inicio/InicioView');
			}

			if (isset($this->session->userdata['usuario'])) {
				$this->load->view('header_v2', ["js_contenedor_consolidadado" => true]);
				$this->load->view('CargaConsolidada/ContenedorConsolidadoView', [
					'arrResponseConsolidado' => [],
					'ID_Carga_Consolidada'   => $ID_Carga_Consolidada,
				]);
				$this->load->view('footer_v2', ["js_contenedor_consolidadado" => true]);
			}
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : listar() => ' . $e->getMessage());
		}
	}
	public function listarCrons($ID_Carga_Consolidada = 0)
	{
		try {
			if (! $this->MenuModel->verificarAccesoMenu()) {
				redirect('Inicio/InicioView');
			}

			if (isset($this->session->userdata['usuario'])) {
				$this->load->view('header_v2', ["js_contenedor_consolidado_crons" => true]);
				$this->load->view('CargaConsolidada/ContenedorConsolidadoCronsView', [
					'arrResponseConsolidado' => [],
					'ID_Carga_Consolidada'   => $ID_Carga_Consolidada,
				]);
				$this->load->view('footer_v2', ["js_contenedor_consolidado_crons" => true]);
			}
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : listar() => ' . $e->getMessage());
		}
	}
	public function index()
	{
		$arrData = $this->ContenedorConsolidadoModel->index();
		$data    = [];
		usort($arrData, function ($a, $b) {
			$numA = (int)$a->carga;
			$numB = (int)$b->carga;
			return $numB - $numA;
		});
		foreach ($arrData as $row) {
			$subdata   = [];
			$subdata[] = $row->tipo_carga == 'G. IMPORTACION' ? $row->tipo_carga : $row->tipo_carga . " #" . $row->carga;
			$subdata[] = $row->mes;
			$subdata[] = $row->No_Pais;
			$subdata[] = date("d/m/Y", strtotime($row->f_cierre));
			if (
				$this->user->No_Grupo == "Coordinación"
				|| $this->user->No_Grupo == "Cotizador"
			) {
				$subdata[] = date("d/m/Y", strtotime($row->f_puerto));
				$subdata[] = date("d/m/Y", strtotime($row->f_entrega));
			}
			$subdata[] = $row->empresa;
			if ($this->user->No_Grupo == "ContenedorAlmacen") {
				$divEstado = '<select disabled
		onchange="updateEstado(' . $row->id . ')"
				 id="estado-' . $row->id . '"
			class="form-control
					' . ($row->estado_china == "PENDIENTE" ||  !$row->estado_china  ? "bg-warning" : "") .
					($row->estado_china == "RECIBIENDO" ? "bg-primary" : "") .
					($row->estado_china == "COMPLETADO" ? "bg-success" : "") . '">
					<option value="PENDIENTE" ' . ($row->estado_china == "PENDIENTE" ? "selected" : "") . '>WAITING</option>
					<option value="RECIBIENDO" ' . ($row->estado_china == "RECIBIENDO" ? "selected" : "") . '>RECEIVING</option>
					<option value="COMPLETADO" ' . ($row->estado_china == "COMPLETADO" ? "selected" : "") . '>FINISH</option>
				</select>';
			} else if ($this->user->No_Grupo == "Documentacion") {
				$divEstado = '<select 
				class="form-control
				' . ($row->estado_documentacion == "PENDIENTE" ||  !$row->estado_documentacion ? "bg-warning" : "") .
					($row->estado_documentacion == "DOCUMENTACION" ? "bg-primary" : "") .
					($row->estado_documentacion == "COMPLETADO" ? "bg-success" : "") . '
				
				" id="estado-documentacion-' . $row->id . '" name="estado" onchange="updateEstadoDocumentacion(' . $row->id . ')">
					<option 
					value="PENDIENTE" ' . ($row->estado_documentacion == "PENDIENTE" ? "selected" : "") . '>Pendiente</option>
					<option value="DOCUMENTACION" ' . ($row->estado_documentacion == "DOCUMENTACION" ? "selected" : "") . '>Documentacion</option>
	
					<option value="COMPLETADO" ' . ($row->estado_documentacion == "COMPLETADO" ? "selected" : "") . '>Completado</option>
				</select>';
			} else {
				$divEstado = '<select disabled
				class="form-control
				' . ($row->estado == "PENDIENTE" ||  !$row->estado ? "bg-warning" : "") .
					($row->estado == "RECIBIENDO" ? "bg-primary" : "") .
					($row->estado == "COMPLETADO" ? "bg-success" : "") . '
				
				" id="estado-' . $row->id . '" name="estado" onchange="updateEstado(' . $row->id . ')">
					<option 
					value="PENDIENTE" ' . ($row->estado == "PENDIENTE" ? "selected" : "") . '>Pendiente</option>
					<option value="RECIBIENDO" ' . ($row->estado == "RECIBIENDO" ? "selected" : "") . '>Recibiendo</option>

				
					<option value="COMPLETADO" ' . ($row->estado == "COMPLETADO" ? "selected" : "") . '>Completado</option>
				</select>';
			}
			$subdata[] = $divEstado;

			$divAcciones = '<div>';

			$divAcciones .= '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewSteps(' . $row->id . ',
			' . $row->carga . ')"></i>';
			//if user is coordinacion show
			if ($this->user->No_Grupo == "Coordinación") {
				$divAcciones .= '<i class="fas fa-edit text-warning" style="cursor:pointer; padding:10px;" onclick="view(' . $row->id . ')"></i>';
				$divAcciones .= '<i class="fas fa-trash text-danger" style="cursor:pointer; padding:10px;" onclick="deleteCarga(' . $row->id . ')"></i>';
			}

			$divAcciones .= '</div>';

			$subdata[] = $divAcciones;
			$subdata[] = $row->id;  
			$subdata[] = $row->carga;
			$data[] = $subdata;
		}

		$output = array(
			"data" => $data
		);
		echo json_encode($output);
	}
	public function indexCompletados()
	{
		$arrData = $this->ContenedorConsolidadoModel->indexCompletados();
		$data    = [];
		usort($arrData, function ($a, $b) {
			$numA = (int)$a->carga;
			$numB = (int)$b->carga;
			return $numB - $numA;
		});

		foreach ($arrData as $row) {
			$subdata   = [];
			if ($this->user->No_Grupo == "Documentacion") {
				$subdata[] = $row->mes;
				$subdata[] = $row->No_Pais;
				$subdata[] = $row->empresa;
				$subdata[] = $row->tipo_contenedor;
				$subdata[] = $row->canal_control;
				$subdata[] = $row->fecha_levante;
				$subdata[] = $row->ajuste_valor;
				$subdata[] = $row->multa;
				$subdata[] = $row->valor_fob;
				$subdata[] = $row->valor_flete;
				$subdata[] = $row->costo_destino;
				//icon mail
				$divObservacion = "
			<div class='d-flex justify-center items-center' style='position: relative; display: flex; justify-content: center; align-items: center;'>
			<div class='relative'>	
			<i class='fas fa-envelope text-lg' style='cursor:pointer;' onclick='showObservaciones(" . $row->id . ")'></i>";

				if ($row->file_count > 0) {
					// Círculo rojo con el número de archivos, posicionado encima del icono
					$divObservacion .= "<span class='absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs' style='position: absolute; z-index: 10;'>" . $row->file_count . "</span>";
				}
				$divObservacion .= "
			</div>
			</div>";
				$subdata[] = $divObservacion;
				//icon eye
				$divAcciones = '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewSteps(' . $row->id . ',
			' . $row->carga . ',true)"></i>';
				$subdata[] = $divAcciones;
			} else {
				$subdata[] = $row->tipo_carga == 'G. IMPORTACION' ? $row->tipo_carga : $row->tipo_carga . " #" . $row->carga;
				$subdata[] = $row->mes;
				$subdata[] = $row->No_Pais;
				$subdata[] = date("d/m/Y", strtotime($row->f_cierre));
				if ($this->user->No_Grupo == "Coordinación"
				|| $this->user->No_Grupo == "Cotizador"
				) {
					$subdata[] = date("d/m/Y", strtotime($row->f_puerto));
					$subdata[] = date("d/m/Y", strtotime($row->f_entrega));
				}

				$subdata[] = $row->empresa;
				if ($this->user->No_Grupo == "ContenedorAlmacen") {
					$divEstado = '<select
					onchange="updateEstado(' . $row->id . ')"
							id="estado-' . $row->id . '"
						class="form-control
					' . ($row->estado_china == "PENDIENTE" ||  !$row->estado_china  ? "bg-warning" : "") .
						($row->estado_china == "RECIBIENDO" ? "bg-primary" : "") .
						($row->estado_china == "COMPLETADO" ? "bg-success" : "") . '">
					<option value="PENDIENTE" ' . ($row->estado_china == "PENDIENTE" ? "selected" : "") . '>WAITING</option>
					<option value="RECIBIENDO" ' . ($row->estado_china == "RECIBIENDO" ? "selected" : "") . '>RECEIVING</option>
					<option value="COMPLETADO" ' . ($row->estado_china == "COMPLETADO" ? "selected" : "") . '>FINISH</option>
				</select>';
				} else if ($this->user->No_Grupo == "Documentacion") {
					$divEstado = '<select 
					class="form-control
					' . ($row->estado_documentacion == "PENDIENTE" ||  !$row->estado_documentacion ? "bg-warning" : "") .
							($row->estado_documentacion == "DOCUMENTACION" ? "bg-primary" : "") .
							($row->estado_documentacion == "COMPLETADO" ? "bg-success" : "") . '
					
					" id="estado-documentacion-' . $row->id . '" name="estado" onchange="updateEstadoDocumentacion(' . $row->id . ')">
					<option 
					value="PENDIENTE" ' . ($row->estado_documentacion == "PENDIENTE" ? "selected" : "") . '>Pendiente</option>
					<option value="DOCUMENTACION" ' . ($row->estado_documentacion == "DOCUMENTACION" ? "selected" : "") . '>Documentacion</option>
	
					<option value="COMPLETADO" ' . ($row->estado_documentacion == "COMPLETADO" ? "selected" : "") . '>Completado</option>
				</select>';
				} else {
					$divEstado = '<select disabled
				class="form-control
				' . ($row->estado == "PENDIENTE" ||  !$row->estado ? "bg-warning" : "") .
						($row->estado == "RECIBIENDO" ? "bg-primary" : "") .
						($row->estado == "COMPLETADO" ? "bg-success" : "") . '
				
				" id="estado-' . $row->id . '" name="estado" onchange="updateEstado(' . $row->id . ')">
					<option 
					value="PENDIENTE" ' . ($row->estado == "PENDIENTE" ? "selected" : "") . '>Pendiente</option>
					<option value="RECIBIENDO" ' . ($row->estado == "RECIBIENDO" ? "selected" : "") . '>Recibiendo</option>

				
					<option value="COMPLETADO" ' . ($row->estado == "COMPLETADO" ? "selected" : "") . '>Completado</option>
				</select>';
				}
				$subdata[] = $divEstado;

				$divAcciones = '<div>';

				$divAcciones .= '<i class="fas fa-eye text-primary view-eye" style="cursor:pointer; padding:10px;" onclick="viewSteps(' . $row->id . ',
			' . $row->carga . ')"></i>';
				//if user is coordinacion show
				if ($this->user->No_Grupo == "Coordinación") {
					$divAcciones .= '<i class="fas fa-edit text-warning" style="cursor:pointer; padding:10px;" onclick="view(' . $row->id . ')"></i>';
				}

				$divAcciones .= '</div>';

				$subdata[] = $divAcciones;
				$subdata[] = $row->id;
				$subdata[] = $row->carga;
			}

			$data[] = $subdata;
		}

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
		$data['f_puerto']  = $this->convertDateFormat($data['f_puerto']);
		$data['f_entrega'] = $this->convertDateFormat($data['f_entrega']);
		$data['f_cierre']  = $this->convertDateFormat($data['f_cierre']);
		// $data['f_puerto']=date("Y-m-d", strtotime($data['f_puerto']));
		// $data['f_entrega']=date("Y-m-d", strtotime($data['f_entrega']));
		$response = $this->ContenedorConsolidadoModel->store($data);

		$id             = $response['id'];
		$socketResponse = $response['socketResponse'];
		$this->generateSteps($id);
		echo json_encode([
			"status"         => $response['status'],
			'id'             => $id,
			"socketResponse" => $socketResponse,
		]);
	}
	public function update()
	{
		$data              = $this->input->post();
		$data['f_puerto']  = $this->convertDateFormat($data['f_puerto']);
		$data['f_entrega'] = $this->convertDateFormat($data['f_entrega']);
		$data['f_cierre']  = $this->convertDateFormat($data['f_cierre']);

		$arrResponse = $this->ContenedorConsolidadoModel->update($data);
		echo json_encode([
			"status" => $arrResponse,
		]);
	}
	public function show($id)
	{

		$arrResponse = $this->ContenedorConsolidadoModel->show($id);
		//parse all f_entrega and f_puerto to Y/m/d
		$arrResponse->f_entrega = date("Y/m/d", strtotime($arrResponse->f_entrega));
		$arrResponse->f_puerto  = date("Y/m/d", strtotime($arrResponse->f_puerto));
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
		$stepsDocumentacion = $this->HelperImportacionModel->getDocumentacionSteps($idContenedor);
		$result = $this->ContenedorConsolidadoModel->generateSteps($steps, $stepsDocumentacion);
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
	public function updateProveedorData()
	{
		$data = $this->input->post('data');
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$arrResponse = $this->ContenedorConsolidadoModel->updateProveedorData($data, $idProveedor, $idCotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function step()
	{
		$stepIndex = $this->input->post('stepIndex');
		$idContenedor = $this->input->post('idContenedor');
		$tipoTabla = $this->input->post('tipoTabla');
		if ($stepIndex == 1 && $this->user->No_Grupo != "Documentacion") {
			$arrResponse = [];
			if ($tipoTabla == "prospectos") {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacion($idContenedor);
			}else if($tipoTabla=="embarque"){
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacionProveedores($idContenedor);

			}
			else {
				$arrResponse = $this->ContenedorConsolidadoModel->getClientesDocumentacionPagos($idContenedor);

			}
			$data  = [];
			$index = 1;
			foreach ($arrResponse as $row) {
				if ($tipoTabla == "prospectos") {

					$subdata   = [];
					$subdata[] = $index;
					$subdata[] = date("d/m/Y", strtotime($row->fecha));
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					$subdata[] = $row->volumen;
					//div with a tag to download file and button to delete file
					$divFile = '<div style="display: flex;gap: 10px; justify-content:center;">';
					if (! empty($row->cotizacion_file_url)) {
						$divFile .= '
						<a href="' . $row->cotizacion_file_url . '" download>
							<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="32" viewBox="0 0 48 48">
						<rect width="16" height="9" x="28" y="15" fill="#21a366"></rect><path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path><rect width="16" height="9" x="28" y="24" fill="#107c42"></rect><rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect><path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path><path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path><path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path><path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path><path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path><linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#18884f"></stop><stop offset="1" stop-color="#0b6731"></stop></linearGradient><path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
						</svg>
						</a>
						<i class="fas fa-trash" style="cursor:pointer;" onclick="deleteCotizacionFile(' . $row->id_cotizacion . ')"></i>';
					} else {
						$divFile .= '
						<i class="fas fa-upload" style="cursor:pointer;" onclick="uploadCotizacionFile(' . $row->id_cotizacion . ')"></i>';
					}
					$subdata[] = $row->fob;
					$subdata[] = $row->monto;
					$subdata[] = $row->impuestos;
					$subdata[] = $row->tarifa;
					$divFile .= '</div>';
					$subdata[] = $divFile;
					if ($this->user->No_Grupo == "Coordinación") {
						$divAcciones = '<div style="display: flex;gap: 10px; justify-content:center;">
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
							<option value="CONTACTADO" ' . ($row->estado_cotizador == "CONTACTADO" ? "selected" : "") . '>CONTACTADO</option>
							<option value="INTERESADO" ' . ($row->estado_cotizador == "INTERESADO" ? "selected" : "") . '>INTERESADO</option>

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
				} else if ($tipoTabla == "embarque"){
					$subdata = [];
					if ($this->user->No_Grupo != "ContenedorAlmacen") {
						$subdata[] = $row->No_Nombres_Apellidos;
					}
					$proveedores = $row->proveedores;
					//check if is posible to decode json
					if ($proveedores == null) {
						$proveedores = [];
					} else {
						$proveedores = json_decode($proveedores);
					}

					//foreach proveedores add to select
					$proveedoresSelect            = "";
					$estadoSelect                 = "";
					$qtyBoxDiv                    = "";
					$cbmTotalDiv                  = "";
					$pesoTotalDiv                 = "";
					$divInputsSupplier            = "";
					$divInputsCodeSupplier        = "";
					$divInputsPhoneNumberSupplier = "";
					$divInputQtyChina             = "";
					$divInputCBMChina             = "";
					$divInputArriveDateChina      = "";
					$divProductos                 = "";
					$divAcciones                  = "";
					$divEstadoChina               = "";
					$cbmTotalChina                = 0;
					$cbmTotalPeru                 = 0;
					foreach ($proveedores as $proveedor) {
						//validate is number
						if (is_numeric($proveedor->cbm_total_china)) {
							$cbmTotalChina += $proveedor->cbm_total_china;
						}
						if (is_numeric($proveedor->cbm_total)) {
							$cbmTotalPeru += $proveedor->cbm_total;
						}

						if ($this->user->No_Grupo == "ContenedorAlmacen" || $this->user->No_Grupo == "GERENCIA") {
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
								<option value="C" disabled
								class="bg-warning"
								' . ($proveedor->estados_proveedor == "C" ? "selected" : "") . '>C</option>
								<option value="R" disabled
								class="bg-success"
								' . ($proveedor->estados_proveedor == "R" ? "selected" : "") . '>R</option>
								<option value="NS"
								class="bg-danger"
								' . ($proveedor->estados_proveedor == "NS" ? "selected" : "") . '>NS</option>
								<option value="INSPECTION"  disabled ' . ($proveedor->estados_proveedor == "INSPECTION" ? "selected" : "") . '>INSPECTION</option>
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

						$estadoSelect .= '<select class="form-control mb-1" style="font-weight: bold;background-color:#DFDFDF"' . ($this->user->No_Grupo == "Cotizador" ? "disabled" : "") . '
						id="estado-' . $row->id . '-' . $proveedor->id_proveedor . '"
						name="estado" onchange="updateEstadoCotizacionProveedor(' . $row->id . ',' . $proveedor->id . ',' . $row->estados . ')">
							<option value="DEFAULT" ' . ($proveedor->estados == "" ? "selected disabled"  : "") . '>Seleccionar</option>
							<option value="ROTULADO" ' . ($proveedor->estados == "ROTULADO" ? "selected" : "") . '>ROTULADO</option>
							<option value="DATOS PROVEEDOR" ' . ($proveedor->estados == "DATOS PROVEEDOR" ? "selected disabled" : "disabled") . '>DATOS PROVEEDOR</option>
							<option value="INSPECCIONADO" ' . ($proveedor->estados == "INSPECCIONADO" ? "selected" : "") . '' . ($this->user->No_Grupo !== "ContenedorAlmacen" ? " disabled" : "") . '		>INSPECCIONADO</option>
							<option value="COBRANDO" ' . ($proveedor->estados == "COBRANDO" ? "selected" : "") . '' . ($this->user->No_Grupo !== "Coordinación" ? " disabled" : "") . '		>COBRANDO</option>
							<option value="RESERVADO" ' . ($proveedor->estados == "RESERVADO" ? "selected" : "") . '>RESERVADO</option>
							
						</select>';

						$qtyBoxDiv .= '<div><input type="number" class="form-control" disabled value="' . ($proveedor->qty_box ?? 0) . '"></input></div>';
						$cbmTotalDiv .= '<div><input type="number" class="form-control" disabled value="' . ($proveedor->cbm_total ?? 0) . '"></input></div>';
						$pesoTotalDiv .= '<div><input type="number" class="form-control" disabled value="' . ($proveedor->peso ?? 0) . '"></input></div>';
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
						$divAcciones .=
							'<div class="d-flex flex-row gap-1">';

						$divAcciones .= '<div class="mb-1" onclick="verCotizacionEmbarque(' . $proveedor->id_proveedor . ',' . $row->id . ',\'' . addslashes($proveedor->code_supplier) . '\',\'' . addslashes(trim($row->nombre)) . '\')">
								<i class="far fa-eye" style="cursor:pointer;padding:10px;" "></i>
								</div>';

						$divAcciones .= '<div class="mb-1"  onclick="updateProveedorData(' . $row->id . ',' . $proveedor->id_proveedor . ')">
								<i class="far fa-save" style="cursor:pointer;padding:10px;"></i>
								</div>';
						if ($this->user->No_Grupo == "Coordinación") {
							$divAcciones .= '<div class="mb-1" onclick="deleteCotizacion(' . $row->id . ',' . $proveedor->id_proveedor . ')">
									<i class="far fa-trash-alt text-danger" style="cursor:pointer;padding:10px;"></i>
								</div>';
						}
						//if no grupo is $roleGerencia add button with text change rotulado to penidng 
						if ($this->user->No_Grupo == $this->roleGerencia) {
							$divAcciones .= '<div class="mb-1"
							data-toggle="tooltip" data-placement="right" title="Cambiar Rotulado a Pendiente"
							onclick="updateRotulado(' . $row->id . ',' . $proveedor->id_proveedor . ')">
								<i class="fas fa-sync-alt
								' . ($proveedor->send_rotulado_status == "SENDED" ? "text-success" : "") . '
								" style="cursor:pointer;padding:10px;"></i>
							
								</div>';
						}

						$divAcciones .= '</div>';
					}
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
					$subdata[] = $divAcciones;
					$data[]    = $subdata;
				}else{
					$subdata   = [];
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					//badge for estado_pagos_coordinacion gray is PENDIENTE,yellow if ADELANTO,green if PAGADO,red if SOBREPAGO
					$estadoPagosCoordinacion = '<span class="badge badge-secondary">' . $row->estado_pagos_coordinacion . '</span>';
					if ($row->pagos_count==0) {
						$estadoPagosCoordinacion = '<span class="badge badge-secondary">PENDIENTE</span>';
					} else if ($row->total_pagos<$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-warning">ADELANTO</span>';
					} else if ($row->total_pagos==$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-success">PAGADO</span>';
					} else if ($row->total_pagos>$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-danger">SOBREPAGO</span>';
					}
					$subdata[] = $estadoPagosCoordinacion;
					$subdata[] = "Logistica";
					$subdata[] = $row->monto;
					$subdata[] = $row->total_pagos==0 ? "0" : number_format($row->total_pagos, 2);
					//if pagos_count is minor than 4 add button plus to add new payment
					$divAcciones='<div class="d-flex px-2 w-100" style="gap:1em;">';
					if($row->pagos_count < 4) {
						$divAcciones .='<div class="d-flex"  onclick="addPagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';
					} 
					if($row->pagos_count > 0) {
						$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
						</div>';
					}
					$divAcciones .=  '</div>';
					$subdata[] = $divAcciones;

					$data[]    = $subdata;
				}
				$index++;

				$output = [
					"data" => $data,
				];
			}
			echo json_encode($output);

			// echo json_encode(['data' => $arrResponse,'status' => "success"]);
		} else if ($stepIndex == 2 || ($stepIndex == 1 && $this->user->No_Grupo == "Documentacion")) {
			$arrResponse = [];
			if ($tipoTabla == "general") {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorClientes($idContenedor);
			} else if ($tipoTabla == "variacion") {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorClientes($idContenedor);
			} else {
				$arrResponse = $this->ContenedorConsolidadoModel->getClientesDocumentacionPagos($idContenedor);
			}
			$data        = [];
			$index       = 1;
			$tipoTabla   = $this->input->post('tipoTabla');
			foreach ($arrResponse as $row) {
				if ($tipoTabla == "general") {
					$subdata   = [];
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->correo;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					if ($this->user->No_Grupo != "Documentacion") {
						$subdata[] = $row->volumen;
						$subdata[] = $row->fob;
						$subdata[] = $row->monto;
						$subdata[] = $row->impuestos;
						$subdata[] = $row->tarifa;
					}

					if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Documentacion") {
						$selectEstadoCliente = "";
						$selectEstadoCliente = '<select class="form-control
						' . ($row->estado_cliente == "RESERVADO" ? "bg-warning" : "") .
							($row->estado_cliente == "NO RESERVADO" ? "bg-secondary" : "") .
							($row->estado_cliente == "DOCUMENTACION" ? "bg-primary" : "") .
							($row->estado_cliente == "C FINAL" ? "bg-blue-600" : "") .
							($row->estado_cliente == "FACTURADO" ? "bg-success" : "") . ' 
                        " id="estado-cliente-' . $row->id_cotizacion . '" name="estado" onchange="updateEstadoCliente(' . $row->id_cotizacion . ')">
						<option value="RESERVADO" ' . ($row->estado_cliente == "RESERVADO" ? "selected"  : "") . ' >RESERVADO</option>
						<option value="NO RESERVADO" ' . ($row->estado_cliente == "NO RESERVADO" ? "selected" : "") . ' >NO RESERVADO</option>
						<option value="DOCUMENTACION" ' . ($row->estado_cliente == "DOCUMENTACION" ? "selected" : "") . ' >DOCUMENTACION</option>
						<option value="C FINAL" ' . ($row->estado_cliente == "C FINAL" ? "selected" : "") . '>C FINAL</option>
						<option value="FACTURADO" ' . ($row->estado_cliente == "FACTURADO" ? "selected" : "") . '>FACTURADO</option>
					</select>';
						$subdata[] = $selectEstadoCliente;
					}
					if ($this->user->No_Grupo == "Coordinación") {
						$divAcciones = '<div class="d-flex px-2" style="gap:20px;"><div class="d-flex"  onclick="viewClientesDocumentacion(' . $row->id_cotizacion . ', \'' . addslashes($row->nombre) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
						</div>' .
							'<div class="d-flex" onclick="deleteCliente(' . $row->id_cotizacion . ')">
							<i class="fas fa-trash text-danger" style="cursor:pointer;" ></i>
							</div></div>';
						$subdata[] = $divAcciones;
					} else {
						$btnView = '<div onclick="viewClientesDocumentacion(' . $row->id_cotizacion . ', \'' . addslashes($row->nombre) . '\')">

					<i class="fas fa-eye" style="cursor:pointer;"></i>
					</div>';
						$subdata[] = $btnView;
					}

					$data[]    = $subdata;
					$index++;
				} else if ($tipoTabla == "variacion") {
					$subdata       = [];
					$volSelected   = $row->vol_selected;
					$divValidacion = "";
					//if volumen , volumen china and volumen doc are not equal or valor_cot and valor_doct badge danger SI ELSE NO
					if ($row->volumen != $row->volumen_china || $row->volumen != $row->volumen_doc || $row->volumen_china != $row->volumen_doc || $row->valor_cot != $row->valor_doc) {
						$divValidacion = '<span class="px-3 py-2 bg-danger rounded-sm">SI</span>';
					} else {
						$divValidacion = '<span class="px-3 py-2 bg-success rounded-sm">NO</span>';
					}
					//for each type of  volumen  a div with background color diferent, and if vol_selected text equeal to type not change the span for a button to select else yes


					$divVol = '<div class="d-flex flex-row gap-2">
                    <div class="d-flex flex-row gap-2">
                        ' . (($volSelected != "volumen")
						? '<button class="px-3 py-2 bg-light rounded-sm border-0" onclick="updateVolSelected(' . $row->id_cotizacion . ', \'volumen\')">' . ($row->volumen ?? 0) . '</button>'
						: '<span class="px-3 py-2 bg-orange  rounded-sm">' . ($row->volumen ?? 0) . '</span>'
					) . '
                    </div>
					</div>';


						$divVolChina = '<div class="d-flex flex-row gap-2">
						<div class="d-flex flex-row gap-2">
						' . (($volSelected != "volumen_china")
							? '<button class="px-3 py-2 bg-light rounded-sm border-0" onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen_china\')">' . ($row->volumen_china ?? 0) . '</button>'
							: '<span class="px-3 py-2 bg-orange rounded-sm">' . ($row->volumen_china ?? 0) . '</span>') . '
						</div>
					</div>';

					//     $divVolDoc = '<div class="d-flex flex-row gap-2">
					// 	<div class="d-flex flex-row gap-2">
					// 	<span class="px-3 py-2 bg-light rounded-sm">' . ($row->volumen_doc ?? 0) . '</span>
					// 	' . ($volSelected != "volumen_doc" ? '<i class="fas fa-check text-success" onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen_doc\')"></i>' : '') . '
					// 	</div>
					// </div>';

					$divVolDoc = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					' . (($volSelected != "volumen_doc")
						? '<button class="px-3 py-2 bg-light rounded-sm border-0" onclick="updateVolSelected(' . $row->id_cotizacion . ',\'volumen_doc\')">' . ($row->volumen_doc ?? 0) . '</button>'
						: '<span class="px-3 py-2 bg-orange rounded-sm">' . ($row->volumen_doc ?? 0) . '</span>') . '
					</div>
					</div>';
					$divValorCot = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span>' . ($row->valor_cot ?? 0) . '</span></div>';
					$divValorDoc = '<div class="d-flex flex-row gap-2">
					<div class="d-flex flex-row gap-2">
					<span>' . ($row->valor_doc ?? 0) . '</span></div>';

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
				}else{
					$subdata   = [];
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;
					//badge for estado_pagos_coordinacion gray is PENDIENTE,yellow if ADELANTO,green if PAGADO,red if SOBREPAGO
					$estadoPagosCoordinacion = '<span class="badge badge-secondary">' . $row->estado_pagos_coordinacion . '</span>';
					if ($row->pagos_count==0) {
						$estadoPagosCoordinacion = '<span class="badge badge-secondary">PENDIENTE</span>';
					} else if ($row->total_pagos<$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-warning">ADELANTO</span>';
					} else if ($row->total_pagos==$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-success">PAGADO</span>';
					} else if ($row->total_pagos>$row->monto) {
						$estadoPagosCoordinacion = '<span class="badge badge-danger">SOBREPAGO</span>';
					}
					$subdata[] = $estadoPagosCoordinacion;
					$subdata[] = "Logistica";
					$subdata[] = "$".round($row->monto+$row->impuestos,2);
					$subdata[] = $row->total_pagos==0 ? "0" : number_format($row->total_pagos, 2);
					//if pagos_count is minor than 4 add button plus to add new payment
					$divAcciones='<div class="d-flex px-2 w-100" style="gap:1em;">';
					if($row->pagos_count < 4) {
						$divAcciones .='<div class="d-flex"  onclick="addPagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';
					} 
					if($row->pagos_count > 0) {
						$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
						</div>';
					}
					$divAcciones .=  '</div>';
					$subdata[] = $divAcciones;

					$data[]    = $subdata;
				}
			}
			$output = [
				"data" => $data,
			];
			echo json_encode($output);
		} else if ($stepIndex == 3) {
			$arrResponse = $this->ContenedorConsolidadoModel->getDocumentationFolderFiles($idContenedor);
			echo json_encode($arrResponse);
		} else if ($stepIndex == 4) {
			$arrResponse = [];
			if ($tipoTabla == "general") {
				$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacionesFinales($idContenedor);
			}  else {
				$arrResponse = $this->ContenedorConsolidadoModel->getCotizacionFinalDocumentacionPagos($idContenedor);
			}
			$data        = [];
			$index       = 1;
			foreach ($arrResponse as $row) {
				$subdata       = [];
				if($tipoTabla == "general") {
					$subdata[]     = $index;
					$subdata[]     = $row->nombre;
					$subdata[]     = $row->documento;
					$subdata[]     = $row->correo;
					$subdata[]     = $row->telefono;
					$subdata[]     = $row->name;
					$subdata[]     = $row->volumen_final;
					$subdata[]     = $row->monto_final;
					$subdata[]     = $row->fob_final;
					$subdata[]     = $row->impuestos_final;
					$subdata[]     = $row->tarifa_final;
					//select for options C.FINAL,AJUSTADO,COTIZADO,PAGADO,SOBREPAGO
					$selectEstados = '<select class="form-control" id="estado-cotizacion-final' . $row->id_cotizacion . '" name="estado" onchange="updateEstadoCotizacionFinal(' . $row->id_cotizacion . ')">
						<option value="PENDIENTE" ' . ($row->estado_cotizacion_final == "PENDIENTE" ? "selected" : "") . '>PENDIENTE</option>
						<option value="C.FINAL" ' . ($row->estado_cotizacion_final == "C.FINAL" ? "selected" : "") . '>C.FINAL</option>
						<option value="AJUSTADO" ' . ($row->estado_cotizacion_final == "AJUSTADO" ? "selected" : "") . '>AJUSTADO</option>
						<option value="COTIZADO" ' . ($row->estado_cotizacion_final == "COTIZADO" ? "selected" : "") . '>COTIZADO</option>
						<option value="PAGADO" ' . ($row->estado_cotizacion_final == "PAGADO" ? "selected" : "") . '>PAGADO</option>
						<option value="SOBREPAGO" ' . ($row->estado_cotizacion_final == "SOBREPAGO" ? "selected" : "") . '>SOBREPAGO</option>
						</select>';
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
				}else {
					$subdata   = [];
					$subdata[] = $index;
					$subdata[] = $row->nombre;
					$subdata[] = $row->documento;
					$subdata[] = $row->telefono;
					$subdata[] = $row->name;	
					$subdata[] = "$".($row->monto_final+$row->impuestos_final);
					$subdata[] =$row->total_pagos==0 ? "$0" : "$".  number_format($row->total_pagos, 2);
					//if pagos_count is minor than 4 add button plus to add new payment
					$divAcciones='<div class="d-flex px-2 w-100" style="gap:1em;">';
					
					$divAcciones .='<div class="d-flex"  onclick="addPagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';
					
					if($row->pagos_count > 0) {
						$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCoordination(' . $row->id_cotizacion . ', \'' . addslashes(trim($row->nombre)) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
						</div>';
					}
					$divAcciones .=  '</div>';
					$subdata[] = $divAcciones;
					$index++;
					$data[]    = $subdata;
				}
			}

			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} else if ($stepIndex == 5) {
			$arrResponse = $this->ContenedorConsolidadoModel->getContenedorFacturaGuia($idContenedor);
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
				$subdata[] = '<div class="badge badge-' . ($row->estado_cotizacion_final == "AJUSTADO" ? "danger" : "success") . '">' .
					($row->estado_cotizacion_final == "AJUSTADO" ? "SI" : "NO") . '</div>';

				//if cotizacion_final_url exists add icon download
				$divFile = '<div>';
				if (!empty($row->cotizacion_final_url)) {
					$divFile .= '<div class="d-flex flex-row gap-2">
						<a href="' . $row->cotizacion_final_url . '" download>
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
	public function storeCotizacion()
	{
		try {
			$data       = $this->input->post();
			$cotizacion = $_FILES['cotizacion'];
			$response = $this->ContenedorConsolidadoModel->storeCotizacion($data, $cotizacion);
			echo json_encode([
				"status" => $response['status'],
			]);
		} catch (Exception $e) {
			echo json_encode([
				"status"  => false,
				"message" => $e->getMessage(),
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
			"status" => $arrResponse,
		]);
	}
	public function deleteCotizacion($idCotizacion)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacion($idCotizacion);
		echo json_encode([
			"status" => $arrResponse,
		]);
	}
	public function uploadCotizacionFile()
	{
		$idCotizacion = $this->input->post('id');
		$file         = $_FILES['file'];
		$arrResponse  = $this->ContenedorConsolidadoModel->uploadCotizacionFile($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse,
		]);
	}
	public function showCotizacion($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->showCotizacion($id);
		echo json_encode($arrResponse);
	}
	public function updateCotizacion()
	{
		$data       = $this->input->post();
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
	public function updateEstadoDocumentacion()
	{
		$id = $this->input->post('id');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoDocumentacion($id, $estado);
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
		$id_cotizacion = $this->input->post('id_cotizacion');
		$id_proveedor = $this->input->post('id_proveedor');
		$name = $this->input->post('name');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->createClienteDocumentacion($id_cotizacion, $name, $file, $id_proveedor);
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
	public function deleteFileDocumentation($idFile)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFileDocumentation($idFile);
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
		$categoria = $this->input->post('categoria');
		$icon = $this->input->post('icon');
		$arrResponse = $this->ContenedorConsolidadoModel->createDocumentacionFolder(
			$name,
			$idContenedor,
			$file,
			$categoria,
			$icon
		);
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
			ob_clean();
			ob_start();

			$objExcel = $this->ContenedorConsolidadoModel->downloadFacturaComercial($idContenedor);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Factura_Comercial.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
			$objWriter->save('php://output');
			ob_end_flush();
		} catch (Exception $e) {
			log_message('error', 'Error al descargar la factura comercial: ' . $e->getMessage());
			echo json_encode([
				"status" => false,
				"message" => $e->getMessage()
			]);
		}
	}
	public function updateEstadoCotizacionProveedor()
	{
		ob_end_clean();
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

	public function uploadFileDocument()
	{
		$file         = $_FILES['file'];
		$idProveedor  = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$arrResponse  = $this->ContenedorConsolidadoModel->uploadFileDocument($file, $idProveedor, $idCotizacion);
		echo json_encode($arrResponse);
	}
	public function uploadFileAlmacenInspection()
	{
		$file         = $_FILES['file'];
		$idProveedor  = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$arrResponse  = $this->ContenedorConsolidadoModel->uploadFileAlmacenInspection($file, $idProveedor, $idCotizacion);
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
		if (empty($_FILES['file']['name'])) {
			log_message('error', 'uploadBL: No se recibió ningún archivo.');
			echo json_encode([
				"status" => "error",
				"message" => "No se recibió ningún archivo.",
			]);
			return;
		}

		$file = $_FILES['file'];
		$idContenedor = $this->input->post('idContenedor');
		if (empty($idContenedor)) {
			log_message('error', 'uploadBL: No se recibió el ID del contenedor.');
			echo json_encode([
				"status" => "error",
				"message" => "No se recibió el ID del contenedor.",
			]);
			return;
		}

		// Llama al modelo para manejar la subida
		$arrResponse = $this->ContenedorConsolidadoModel->uploadBL($idContenedor, $file);
		log_message('error', 'uploadBL: Respuesta del modelo: ' . json_encode($arrResponse));
		echo json_encode([
			"status" => $arrResponse['status'],
			"error" => $arrResponse['message']
		]);
	}
	public function updateVolSelected()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$volSelected  = $this->input->post('type');
		$arrResponse  = $this->ContenedorConsolidadoModel->updateVolSelected($idCotizacion, $volSelected);
		echo json_encode([
			"status" => $arrResponse,
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
			"status" => $arrResponse['status'],
			"message" => $arrResponse['message']
		]);
	}
	public function deleteListaEmbarque($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteListaEmbarque($idContenedor);
		echo json_encode([
			"status" => $arrResponse,
		]);
	}
	public function addNote()
	{
		$note        = $this->input->post('note');
		$idProveedor = $this->input->post('idProveedor');
		$arrResponse = $this->ContenedorConsolidadoModel->addNote($note, $idProveedor);
		echo json_encode([
			"status" => $arrResponse,
		]);
	}
	public function getNotes($idProveedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getNotes($idProveedor);
		echo json_encode($arrResponse);
	}
	public function updateEstadoCotizador()
	{
		$estado      = $this->input->post('estado');
		$id          = $this->input->post('id');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizador($id, $estado);
		echo json_encode([
			"status"  => $arrResponse,
			"message" => "No todos los proveedores tienen productos",

		]);
	}
	public function getClientesHeader($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getClientesHeader($idContenedor);
		echo json_encode($arrResponse);
	}
	public function uploadGeneral()
	{
		$file         = $_FILES['file'];
		$idContenedor = $this->input->post('idContenedor');
		$arrResponse  = $this->ContenedorConsolidadoModel->uploadGeneral($idContenedor, $file);
		echo json_encode([
			"status" => $arrResponse['status'],
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
		$idContenedor = $this->input->post('idContenedor');
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
	public function updateEstadoCotizacionFinal()
	{
		$idCotizacionFinal = $this->input->post('idCotizacionFinal');
		$estado = $this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizacionFinal($idCotizacionFinal, $estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteCotizacionFinalFile($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacionFinalFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadCotizacionFinal()
	{
		$idCotizacionFinal = $this->input->post('idCotizacionFinal');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadCotizacionFinal($idCotizacionFinal, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function downloadBoleta($idCotizacionFinal)
	{
		ob_end_clean();
		$this->ContenedorConsolidadoModel->downloadBoleta($idCotizacionFinal);
	}
	public function uploadFacturaGeneral()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadFacturaGeneral($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadGuiaRemision()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadGuiaRemision($idCotizacion, $file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteFacturaGeneralFile($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFacturaGeneralFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteGuiaRemisionFile($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteGuiaRemisionFile($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function showClientesDocumentacionByDoc($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->showClientesDocumentacionByDoc($id);
		echo json_encode($arrResponse);
	}
	public function getDocumentationFolderFiles($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getDocumentationFolderFiles($idContenedor);
		echo json_encode($arrResponse);
	}
	public function viewFormularioAduana($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->viewFormularioAduana($idContenedor);
		echo json_encode($arrResponse);
	}
	public function updateFormularioAduana()
	{
		$idContenedor = $this->input->post('idContainer');
		$data = $this->input->post();
		$files = $_FILES;
		$arrResponse = $this->ContenedorConsolidadoModel->updateFormularioAduana($idContenedor, $data, $files);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function getCotizacionEmbarqueHeaders($idContenedor)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getCotizacionEmbarqueHeaders($idContenedor);
		echo json_encode($arrResponse);
	}
	public function saveInspectionSingle(){
		$idFile= $this->input->post('idFile');
		$idProveedor= $this->input->post('idProveedor');
		$arrResponse = $this->ContenedorConsolidadoModel->saveInspectionSingle($idFile,$idProveedor);
		echo json_encode($arrResponse);

		
	}
	public function saveInspection()
	{
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$files = $_FILES;
		$arrResponse = $this->ContenedorConsolidadoModel->saveInspection($idProveedor, $idCotizacion, $files);
		echo json_encode([
			'status' => $arrResponse,
		]);
	}
	public function saveDocumentation()
	{
		$idProveedor = $this->input->post('idProveedor');
		$idCotizacion = $this->input->post('idCotizacion');
		$files = $_FILES;
		$arrResponse = $this->ContenedorConsolidadoModel->saveDocumentation($idProveedor, $idCotizacion, $files);
		echo json_encode([
			'status' => $arrResponse,
		]);
	}
	public function deleteFileInspection($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFileInspection($id);
		echo json_encode([
			'status' => $arrResponse,
		]);
	}
	public function deleteFileAduana($id)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->deleteFileAduana($id);
		echo json_encode([
			'status' => $arrResponse,
		]);
	}
	public function getObservaciones($idContainer)
	{
		$arrResponse = $this->ContenedorConsolidadoModel->getObservaciones($idContainer);
		echo json_encode([
			'status' => $arrResponse['status'],
			'data' => $arrResponse['data'],
		]);
	}
	function convertDateFormat($date)
	{
		$dateObject = DateTime::createFromFormat('d/m/Y', $date);
		return $dateObject ? $dateObject->format('Y-m-d') : null; // Devuelve null si la fecha no es válida
	}

	public function getChinaDocuments()
	{
		$idCotizacion = $this->input->get('id_cotizacion'); // Obtén el ID de la cotización desde la solicitud AJAX
		$this->load->model('CargaConsolidada/ContenedorConsolidadoModel'); // Carga el modelo

		$data = $this->ContenedorConsolidadoModel->showClientesDocumentacion($idCotizacion); // Llama al método del modelo

		if ($data) {
			echo json_encode(['status' => 'success', 'data' => $data]); // Devuelve los datos en formato JSON
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No se encontraron documentos para la cotización especificada.']);
		}
	}
	public function updateRotulado()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$idProveeedor = $this->input->post('idProveedor');
		Log_message('error', 'updateRotulado: idCotizacion: ' . $idCotizacion . ', idProveedor: ' . $idProveeedor);
		$arrResponse = $this->ContenedorConsolidadoModel->updateRotulado($idCotizacion, $idProveeedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function forceSendRotulado($idProveedor){
		$arrResponse = $this->ContenedorConsolidadoModel->validateToSendInspectionMessage2($idProveedor);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function saveClientePagosCoordination(){
		$voucher = $_FILES['voucher'];
		$idCotizacion = $this->input->post('idCotizacion');
		$idContenedor = $this->input->post('idContenedor');
		$amount = $this->input->post('monto');
		$fecha= $this->input->post('fecha');
		$banco= $this->input->post('banco');
		$arrResponse = $this->ContenedorConsolidadoModel->saveClientePagosCoordination($voucher, $idCotizacion, $idContenedor,$amount, $fecha, $banco);
		echo json_encode([
			"status" => $arrResponse['status'],
			"message" => $arrResponse['message']
		]);
	}
	public function getPagosCoordination($idCotizacion)
	{
		$arrData = $this->ContenedorConsolidadoModel->getPagosCoordination($idCotizacion);
		$data    = [];
		$index   = 1;
		foreach ($arrData as $row) {
			$subdata = [];
			$subdata[] = $index;
			$subdata[] = $row->payment_date;
			$subdata[] = $row->banco;
			$subdata[] = "$".round($row->monto, 2);
			$subdata[] = '<a href='.$row->voucher_url.' download>
				<i class="fas fa-file-excel text-success"></i>
				</a>';
			$data[] = $subdata;
			$index++;
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
	}
	public function getPagosClientes($idCotizacion)
	{
		$arrData = $this->ContenedorConsolidadoModel->getPagosCoordination($idCotizacion);
		$data    = [];
		$index   = 1;
		foreach ($arrData as $row) {
			$subdata = [];
			$subdata[] = $index;
			$subdata[] = $row->payment_date;
			$subdata[] = $row->banco;
			$subdata[] = $row->monto;
			$subdata[] = '<a href='.$row->voucher_url.' download>
				<i class="fas fa-file-excel text-success"></i>
				</a>';
			$data[] = $subdata;
			$index++;
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
	}
	public function saveClientePagosClientes(){
		$voucher = $_FILES['voucher'];
		$idCotizacion = $this->input->post('idCotizacion');
		$idContenedor = $this->input->post('idContenedor');
		$amount = $this->input->post('monto');
		$fecha= $this->input->post('fecha');
		$banco= $this->input->post('banco');
		$arrResponse = $this->ContenedorConsolidadoModel->saveClientePagosClientes($voucher, $idCotizacion, $idContenedor,$amount, $fecha, $banco);
		echo json_encode([
			"status" => $arrResponse['status'],
			"message" => $arrResponse['message']
		]);
	}
	public function getConsolidadoCrons(){
		$arrData = $this->ContenedorConsolidadoModel->getConsolidadoCrons();
		$data    = [];
		foreach ($arrData as $row) {
			$subdata = [];
			$subdata[] = $row->id;
			$subdata[] = "Contenedor #".$row->carga;
			$subdata[] = $row->nombre;
			$subdata[] = $row->created_at;
			$subdata[] = $row->execution_at;
			$dataJson = json_decode($row->data_json, true);
			$divDataJson = '<div class="text-truncate" style="max-width: 200px;">';
			$divDataJson .= '<textarea class="form-control" rows="5" readonly >'. htmlspecialchars(json_encode($dataJson['message'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) .'</textarea>';
			$divDataJson .= '<span class="badge badge-info" style="margin-left: 5px;">' . $dataJson['phoneNumberId'] . '</span>';
			$divDataJson .= '</div>';
			$subdata[] = $divDataJson;
			$divEstado = '<div>
			<span class="badge badge-success">' . $row->status . '</span>';
			$divEstado .= '<span class="badge badge-info" style="margin-left: 5px;">' . $row->executed_at . '</span>';
			$divEstado .= '</div>';
			$subdata[] = $divEstado;
			$divAcciones = '<div class="btn-group">';
			//edit button 
			$divAcciones .= '<button type="button" class="btn btn-primary btn-sm" onclick="editCron(' . $row->id . ')"><i class="fas fa-edit"></i></button>';

			//delete button
			$divAcciones .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteCron(' . $row->id . ')"><i class="fas fa-trash"></i></button>';
			$divAcciones .= '</div>';
			$subdata[] = $divAcciones;
			$data[] = $subdata;
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);

	}
	public function getCron($idCron){
		$arrResponse = $this->ContenedorConsolidadoModel->getCron($idCron);
		if ($arrResponse) {
			echo json_encode([
				"status" => "success",
				"data" => $arrResponse
			]);
		} else {
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró el cron con el ID especificado."
			]);
		}
	
	}
	public function updateCron(){
		$idCron = $this->input->post('id');
		$dataJson = $this->input->post('data_json');
		$arrData = json_decode($dataJson, true);
		$time_between = $this->input->post('time_between');
		$created_at = $this->input->post('created_at');
		$arrResponse = $this->ContenedorConsolidadoModel->updateCron($idCron, $arrData,$time_between,$created_at
	);
		echo json_encode($arrResponse);
	}
	public function deleteCron($idCron){
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCron($idCron);
		echo json_encode([
			"status" => $arrResponse['status'],
			"message" => $arrResponse['message']
		]);
	}
}