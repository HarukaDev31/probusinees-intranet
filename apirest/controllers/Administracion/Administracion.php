<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administracion extends CI_Controller
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
			$this->load->model('Administracion/AdministracionModel');
			$this->load->model('HelperImportacionModel');
			if (!isset($this->session->userdata['usuario'])) {
				redirect('');
			}
		} catch (Exception $e) {
			log_message('error', 'AdministracionModel : __construct() => ' . $e->getMessage());
		}
	}

	public function listar($ID_Carga_Consolidada = 0)
	{
		try {
			if (! $this->MenuModel->verificarAccesoMenu()) {
				redirect('Inicio/InicioView');
			}

			if (isset($this->session->userdata['usuario'])) {
				$this->load->view('header_v2', ["js_administracion" => true]);
				$this->load->view('Administracion/AdministracionView', [
					'arrResponseConsolidado' => [],
					'ID_Carga_Consolidada'   => $ID_Carga_Consolidada,
				]);
				$this->load->view('footer_v2', ["js_administracion" => true]);
			}
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : listar() => ' . $e->getMessage());
		}
	}
	public function getConsolidadoPagos()
	{
		try {
			$arrData = $this->AdministracionModel->getConsolidadoPagos();
			$data    = [];
			$index  = 1;
			foreach ($arrData as $key => $value) {
				$subdata   = [];
				$subdata[] = $index;
				$subdata[] = date('d-m-Y', strtotime($value->fecha));
				$subdata[] = $value->nombre;
				$subdata[] = $value->documento;
				$subdata[] = $value->telefono;
				$subdata[] = "Consolidado";
				$subdata[] =  "#" . $value->carga ;
				//if input post campana is not 0 
				if (!empty($this->input->post('campana')) && $this->input->post('campana') != '0') {
					$campanaFiltro = $this->input->post('campana');
					if ($value->carga != $campanaFiltro) {
						continue; // Usar continue en lugar de return
					}
				}
				$aPagar = ($value->logistica_final + $value->impuestos_final) == 0 ? $value->monto : ($value->logistica_final + $value->impuestos_final);
				$estadoPago = '';
				if ($value->total_pagos == 0) {
					$estadoPago = 'PENDIENTE';
				} else if ($value->total_pagos_monto < $aPagar) {
					$estadoPago = 'ADELANTO';
				} else if ($value->total_pagos_monto == $aPagar) {
					$estadoPago = 'PAGADO';
				} else if ($value->total_pagos_monto > $aPagar) {
					$estadoPago = 'SOBREPAGO';
				}

				// Define la clase según el estado
				$estadoClass = '';
				switch ($estadoPago) {
					case 'PENDIENTE': $estadoClass = 'bg-secondary text-white'; break;
					case 'ADELANTO': $estadoClass = 'bg-warning text-dark'; break;
					case 'PAGADO': $estadoClass = 'bg-success text-white'; break;
					case 'SOBREPAGO': $estadoClass = 'bg-danger text-white'; break;
					default: $estadoClass = 'bg-secondary text-white'; break;
				}
				$estadoPagosCoordinacion = '<select class="form-control form-control-sm '.$estadoClass.'" disabled>
					<option value="PENDIENTE" '.($estadoPago == "PENDIENTE" ? "selected" : "").'>Pendiente</option>
					<option value="ADELANTO" '.($estadoPago == "ADELANTO" ? "selected" : "").'>Adelanto</option>
					<option value="PAGADO" '.($estadoPago == "PAGADO" ? "selected" : "").'>Pagado</option>
					<option value="SOBREPAGO" '.($estadoPago == "SOBREPAGO" ? "selected" : "").'>Sobrepago</option>
				</select>';

				if (!empty($this->input->post('estado_pago')) && $this->input->post('estado_pago') != '0') {
					$estadoFiltro = $this->input->post('estado_pago');
					if ($estadoPago !== $estadoFiltro) {
						continue; // Usar continue en lugar de return
					}
				}

				$subdata[] = $estadoPagosCoordinacion;
				$subdata[] = "$ " . (($aPagar) == 0 ? $value->monto : number_format($aPagar, 2, '.', ''));
				$subdata[] = "$ " . number_format($value->total_pagos_monto, 2, '.', '');
				$divAcciones = '<div class="nav gap-1">';
				$pagos_details = json_decode($value->pagos_details, true);
				foreach ($pagos_details as $pago) {
					$divAcciones .= '<button class="nav-link p-2 rounded-lg bg' . $this->getColortabByStatus($pago['status']) . '">' . '$' . number_format($pago['monto'], 2) . '</button>';
				}
				$divAcciones .= '</div>';
				$subdata[] = $divAcciones;
				$divAcciones1 = '<div class="d-flex gap-1">';
				$divAcciones1 .= '<div class="d-flex"  onclick="viewDetailsPagosConsolidado(' . $value->id . ',' . $value->total_pagos_monto . ',' . $aPagar . ',' . '\'' . addslashes(trim($value->nombre)) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
					</div>';
				if ($value->note_administracion) {
					$divAcciones1 .= '<div class="d-flex"  onclick="viewNote(\'' . addslashes(trim($value->note_administracion)) . '\')">
						<i class="fas fa-sticky-note" style="cursor:pointer;"></i>
					</div>';
				}
				$subdata[] = $divAcciones1;
				$data[] = $subdata;

				$index++;
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getConsolidado() => ' . $e->getMessage());
		}
	}
	public function getPagosCoordination($idCotizacion)
	{
		$arrData = $this->AdministracionModel->getPagosCoordination($idCotizacion);
		$data    = [];
		$index   = 1;
		foreach ($arrData as $row) {
			$subdata = [];
			$subdata[] = $index;
			$subdata[] = $row->payment_date;
			$subdata[] = $row->banco;
			$subdata[] = "$" . round($row->monto, 2);
			$subdata[] = '<div data-url=' . $row->voucher_url . ' download
                onclick="showImageModal(\'' . $row->voucher_url . '\')"
                >
                    <i class="fas fa-file"></i>
                    </div>';
			$data[] = $subdata;
			$index++;
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
	}
	public function getCampanasActivas()
	{
		try {
			$arrResponse = $this->AdministracionModel->getCampanasActivas();
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getCampanasActivas() => ' . $e->getMessage());
		}
	}
	public function getCursosPagos()
	{
		try {
			$arrData = $this->AdministracionModel->getCursosPagos();
			$data    = [];
			$index   = 1;
			foreach ($arrData as $row) {
				$subdata   = array();
				$subdata[] =  $row->ID_Pedido_Curso;
				$subdata[] = date('d-m-Y', strtotime($row->Fe_Registro));
				$subdata[] = $row->No_Entidad;
				$subdata[] = $row->Nu_Celular_Entidad;
				$subdata[] = "Curso";
				$campanas = $this->AdministracionModel->getCampanasActivas();
				// Armar el select
				$select = '';
				if (!empty($row->ID_Campana)) {
					foreach ($campanas as $campana) {
						if ($row->ID_Campana == $campana['ID_Campana']) {
							$select .= $campana['nombre_campana'];
						}
					}
				}
				if (!empty($this->input->post('campana')) && $this->input->post('campana') != '0') {
					$campanaFiltro = $this->input->post('campana');
					if ($row->ID_Campana != $campanaFiltro) {
						continue; // Usar continue en lugar de return
					}
				}
				$subdata[] = $select; //mes

				// --- Lógica de estado de pago igual que consolidado ---
				$aPagar = ($row->logistica_final + $row->impuestos_final) == 0 ? $row->Ss_Total : ($row->logistica_final + $row->impuestos_final);
				$estadoPago = '';
				if ($row->total_pagos == 0) {
					$estadoPago = 'PENDIENTE';
				} else if ($row->total_pagos < $aPagar) {
					$estadoPago = 'ADELANTO';
				} else if ($row->total_pagos == $aPagar) {
					$estadoPago = 'PAGADO';
				} else if ($row->total_pagos > $aPagar) {
					$estadoPago = 'SOBREPAGO';
				}
				$estadoClass = '';
				switch ($estadoPago) {
					case 'PENDIENTE': $estadoClass = 'bg-secondary text-white'; break;
					case 'ADELANTO': $estadoClass = 'bg-warning text-dark'; break;
					case 'PAGADO': $estadoClass = 'bg-success text-white'; break;
					case 'SOBREPAGO': $estadoClass = 'bg-danger text-white'; break;
					default: $estadoClass = 'bg-secondary text-white'; break;
				}
				$estadoCurso = '<select class="form-control form-control-sm '.$estadoClass.'" disabled>';
				$estadoCurso .= '<option value="PENDIENTE" '.($estadoPago == "PENDIENTE" ? "selected" : "").'>Pendiente</option>';
				$estadoCurso .= '<option value="ADELANTO" '.($estadoPago == "ADELANTO" ? "selected" : "").'>Adelanto</option>';
				$estadoCurso .= '<option value="PAGADO" '.($estadoPago == "PAGADO" ? "selected" : "").'>Pagado</option>';
				$estadoCurso .= '<option value="SOBREPAGO" '.($estadoPago == "SOBREPAGO" ? "selected" : "").'>Sobrepago</option>';
				$estadoCurso .= '</select>';
				//get from input post estado_pago if !=0 filter by estado_curso if not in continue
				if ($this->input->post('estado_pago') != 0) {
					if (strpos($estadoCurso, $this->input->post('estado_pago')) === false) {
						continue;
					}
				}
				$subdata[] = $estadoCurso;
				$subdata[] = $row->No_Signo . round($row->Ss_Total, 2);
				$subdata[] = "S/" . round($row->total_pagos, 2);
				$divAcciones = '<div class="nav gap-1">';
				$pagos_details = json_decode($row->pagos_details, true);
				foreach ($pagos_details as $pago) {
					$divAcciones .= '<button class="nav-link p-2 rounded-lg bg' . $this->getColorTabByStatus($pago['status']) . '">S/' . $pago['monto'] . '</button>';
				}
				$divAcciones .= '</div>';
				$subdata[] = $divAcciones;
				$divAcciones1 = '<div class="d-flex gap-1">';
				$divAcciones1 .= '<div class="d-flex"  onclick="viewDetailsPagosCurso(' . $row->ID_Pedido_Curso . ',' . $row->Ss_Total . ',' . $row->total_pagos . ',\'' . addslashes(trim($row->No_Entidad)) . '\')">
					<i class="fas fa-eye" style="cursor:pointer;"></i>
					</div>';
				if ($row->note_administracion) {
					$divAcciones1 .= '<div class="d-flex"  onclick="viewNote(\'' . addslashes(trim($row->note_administracion)) . '\')">
						<i class="fas fa-sticky-note" style="cursor:pointer;"></i>
					</div>';
				}
				$subdata[] = $divAcciones1;
				$data[] = $subdata;
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getPagosCurso() => ' . $e->getMessage());
		}
	}
	public function getColorByStatus($status)
	{
		switch ($status) {
			case 'PENDIENTE':
				return 'badge-secondary';
			case 'ADELANTO':
				return 'badge-warning';
			case 'CONFIRMADO':
				return 'badge-success';
			case 'OBSERVADO':
				return 'badge-danger';
			default:
				return 'badge-secondary';
		}
	}
	public function getColorTabByStatus($status)
	{
		switch ($status) {
			case 'PENDIENTE':
				return '-[#585858] text-white';
			case 'ADELANTO':
				return '-warning';
			case 'CONFIRMADO':
				return '-[#00D680]';
			case 'OBSERVADO':
				return '-[#D71009] text-white';
			default:
				return '-secondary';
		}
	}
	public function getPagosCurso($idPedidoCurso)
	{
		try {
			$arrData = $this->AdministracionModel->getPagosCurso($idPedidoCurso);
			$data    = [];
			$index   = 1;
			foreach ($arrData as $row) {
				$subdata = [];
				$subdata[] = $index;
				$subdata[] = allTypeDate($row->payment_date, '-', 0);
				$subdata[] = $row->banco;
				$subdata[] = "$" . round($row->monto, 2);
				$subdata[] = '<a href=' . $row->voucher_url . ' download>
                    <i class="fas fa-file-excel text-success"></i>
                    </a>';
				$data[] = $subdata;
				$index++;
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getPagosCurso() => ' . $e->getMessage());
		}
	}
	public function getCampanasSelect()
	{
		$data = $this->AdministracionModel->getCampanasActivas();
		echo json_encode(['status' => 'success', 'data' => $data]);
	}
	public function getDetailsPagosConsolidado($idCotizacion)
	{
		try {
			$arrResponse = $this->AdministracionModel->getDetailsPagosConsolidado($idCotizacion);
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getDetailsPagosConsolidado() => ' . $e->getMessage());
		}
	}
	public function handlePayment()
	{
		$idPago = $this->input->post('idPago');
		$status = $this->input->post('status');
		$result = $this->AdministracionModel->handlePayment($idPago, $status);
		echo json_encode($result);
	}
	public function handlePaymentCurso()
	{
		$idPagoCurso = $this->input->post('idPago');
		$status = $this->input->post('status');
		$result = $this->AdministracionModel->handlePaymentCurso($idPagoCurso, $status);
		echo json_encode($result);
	}
	public function saveNote()
	{
		$idCotizacion = $this->input->post('idCotizacion');
		$note = $this->input->post('note');
		$result = $this->AdministracionModel->saveNote($idCotizacion, $note);
		echo json_encode($result);
	}
	public function saveNoteCurso()
	{
		$idPedidoCurso = $this->input->post('idCotizacion');
		$note = $this->input->post('note');
		$result = $this->AdministracionModel->saveNoteCurso($idPedidoCurso, $note);
		echo json_encode($result);
	}
	public function getDetailsPagosCurso($idPedidoCurso)
	{
		try {
			$arrResponse = $this->AdministracionModel->getDetailsPagosCurso($idPedidoCurso);
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getDetailsPagosCurso() => ' . $e->getMessage());
		}
	}
	public function getHeadersConsolidado()
	{
		try {
			$arrResponse = $this->AdministracionModel->getHeadersConsolidado();
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getHeadersConsolidado() => ' . $e->getMessage());
		}
	}
	public function getHeadersCurso()
	{
		try {
			$arrResponse = $this->AdministracionModel->getHeadersCurso();
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getHeadersCurso() => ' . $e->getMessage());
		}
		//get containers avalable for consolidation

	}
	public function getContainersAvailable()
	{
		try {
			$arrResponse = $this->AdministracionModel->getContainersAvailable();
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getContainersAvailable() => ' . $e->getMessage());
		}
	}
}
