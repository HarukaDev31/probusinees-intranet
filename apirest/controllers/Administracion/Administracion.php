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
				$subdata[] = "#" . $value->carga;

				$estadoPagosCoordinacion = '<span class="badge badge-secondary">' . $value->estado_pagos_coordinacion . '</span>';
				$aPagar = ($value->monto_final + $value->impuestos_final) == 0 ? $value->monto : ($value->monto_final + $value->impuestos_final);
				if ($value->total_pagos == 0) {
					$estadoPagosCoordinacion = '<span class="badge badge-secondary">PENDIENTE</span>';
				} else if ($value->total_pagos_monto < ($aPagar)) {
					$estadoPagosCoordinacion = '<span class="badge badge-warning">ADELANTO</span>';
				} else if ($value->total_pagos_monto == ($aPagar)) {
					$estadoPagosCoordinacion = '<span class="badge badge-success">PAGADO</span>';
				} else if ($value->total_pagos_monto > ($aPagar)) {
					$estadoPagosCoordinacion = '<span class="badge badge-danger">SOBREPAGO</span>';
				}
				$divAcciones1='<div class="d-flex gap-1">';
				$divAcciones1.='<div class="d-flex"  onclick="viewDetailsPagosConsolidado(' . $value->id . ',' . $value->total_pagos_monto . ',' . $aPagar . ')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
					</div>';
				if ($value->note_administracion) {
					$divAcciones1 .= '<div class="d-flex"  onclick="viewNote(\'' . addslashes(trim($value->note_administracion)) . '\')">
						<i class="fas fa-sticky-note" style="cursor:pointer;"></i>
					</div>';
				}
				$subdata[] = $divAcciones1;
				$subdata[] = $estadoPagosCoordinacion;
				$subdata[] = "$ " . (($aPagar) == 0 ? $value->monto : number_format($aPagar, 2, '.', ''));
				$subdata[] = "$ " . number_format($value->total_pagos_monto, 2, '.', '');
				$divAcciones = '<div class="d-flex px-2 w-100" style="gap:1em;">';

				if ($value->total_pagos > 0) {
					$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCoordination(' . $value->id . ', \'' . addslashes(trim($value->nombre)) . '\')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
						</div>';
				}
				$divAcciones .=  '</div>';
				$subdata[] = $divAcciones;
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
	public function getCursosPagos()
	{
		try {
			$arrData = $this->AdministracionModel->getCursosPagos();
			$data    = [];
			$index   = 1;
			foreach ($arrData as $row) {
				$subdata   = array();
				$subdata[] =  $row->ID_Pedido_Curso;
				$subdata[] = allTypeDate($row->Fe_Registro, '-', 0);
				$subdata[] = $row->No_Entidad;
				$subdata[] = $row->Nu_Celular_Entidad;
				$subdata[] = "Curso";
				$campanas = $this->AdministracionModel->getCampanasActivas();
				// Armar el select
				$select = '<select name="ID_Campana" class="form-control" disabled>';
				if (empty($row->ID_Campana)) {
					$select .= '<option value="">Seleccionar</option>';
				}
				foreach ($campanas as $campana) {
					$selected = ($row->ID_Campana == $campana['ID_Campana']) ? 'selected' : '';
					$select .= '<option value="' . $campana['ID_Campana'] . '" ' . $selected . '>' . $campana['nombre_campana'] . '</option>';
				}
				$select .= '</select>';
				$subdata[] = $select; //mes
				$divAcciones1 = '<div class="d-flex gap-1">';
				$divAcciones1 .= '<div class="d-flex"  onclick="viewDetailsPagosCurso(' . $row->ID_Pedido_Curso . ',' . $row->Ss_Total . ',' . $row->total_pagos . ')">
						<i class="fas fa-eye" style="cursor:pointer;"></i>
					</div>';
				if ($row->note_administracion) {
					$divAcciones1 .= '<div class="d-flex"  onclick="viewNote(\'' . addslashes(trim($row->note_administracion)) . '\')">
						<i class="fas fa-sticky-note" style="cursor:pointer;"></i>
					</div>';
				}
				$subdata[] = $divAcciones1;

				$estadoCurso = '<span class="badge badge-secondary">' . $row->estado_pagos_coordinacion . '</span>';
				if ($row->total_pagos == 0) {
					$estadoCurso = '<span class="badge badge-secondary">PENDIENTE</span>';
				} else if ($row->total_pagos < ($row->Ss_Total)) {
					$estadoCurso = '<span class="badge badge-warning">ADELANTO</span>';
				} else if ($row->total_pagos == ($row->Ss_Total)) {
					$estadoCurso = '<span class="badge badge-success">PAGADO</span>';
				} else if ($row->total_pagos > ($row->Ss_Total)) {
					$estadoCurso = '<span class="badge badge-danger">SOBREPAGO</span>';
				}
				$subdata[] = $estadoCurso;
				$subdata[] = $row->No_Signo . '<input value="' . round($row->Ss_Total, 2) . '" readonly/>';	//importe		
				$subdata[] = "S/" . round($row->total_pagos, 2);
				$divAcciones = '<div class="d-flex px-2 w-100" style="gap:1em;">';


				if ($row->pagos_count > 0) {
					$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCurso(' . $row->ID_Pedido_Curso . ', \'' . addslashes(trim($row->No_Entidad)) . '\')">
												<i class="fas fa-eye" style="cursor:pointer;"></i>

                        </div>';
				}
				$divAcciones .=  '</div>';
				$subdata[] = $divAcciones;
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
	public function handlePayment() {
		$idPago = $this->input->post('idPago');
		$isConfirmed = $this->input->post('isConfirmed');
		$result = $this->AdministracionModel->handlePayment($idPago, $isConfirmed);
		echo json_encode($result);
	}
	public function handlePaymentCurso(){
		$idPagoCurso = $this->input->post('idPago');
		$isConfirmed = $this->input->post('isConfirmed');
		$result = $this->AdministracionModel->handlePaymentCurso($idPagoCurso, $isConfirmed);
		echo json_encode($result);
	}
	public function saveNote(){
		$idCotizacion = $this->input->post('idCotizacion');
		$note = $this->input->post('note');
		$result = $this->AdministracionModel->saveNote($idCotizacion, $note);
		echo json_encode($result);
	}
	public function saveNoteCurso(){
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
	public function getHeadersConsolidado(){
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
	public function getHeadersCurso(){
		try {
			$arrResponse = $this->AdministracionModel->getHeadersCurso();
			echo json_encode([
				'status' => 'success',
				'data'   => $arrResponse
			]);
		} catch (Exception $e) {
			log_message('error', 'ContenedorConsolidado : getHeadersCurso() => ' . $e->getMessage());
		}
	}
}
