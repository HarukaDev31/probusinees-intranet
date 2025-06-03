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
    public function getConsolidadoPagos(){
        try {
            $arrData= $this->AdministracionModel->getConsolidadoPagos();
            $data    = [];
            $index  = 1;
            foreach ($arrData as $key => $value) {
                $subdata   = [];
                $subdata[]= $index ;
                $subdata[]= date('d-m-Y', strtotime($value->fecha));
                $subdata[]= $value->nombre;
                $subdata[]= $value->documento;
                $subdata[]= $value->telefono;
                $subdata[]= "Consolidado";
                $subdata[]= "#".$value->carga;
                $subdata[]= "Acciones";
                $estadoPagosCoordinacion = '<span class="badge badge-secondary">' . $value->estado_pagos_coordinacion . '</span>';
					if ($value->total_pagos==0) {
						$estadoPagosCoordinacion = '<span class="badge badge-secondary">PENDIENTE</span>';
					} else if ($value->total_pagos_monto<($value->monto_final+ $value->impuestos_final)) {
						$estadoPagosCoordinacion = '<span class="badge badge-warning">ADELANTO</span>';
					} else if ($value->total_pagos_monto==($value->monto_final+ $value->impuestos_final)) {
						$estadoPagosCoordinacion = '<span class="badge badge-success">PAGADO</span>';
					} else if ($value->total_pagos_monto>($value->monto_final+ $value->impuestos_final)) {
						$estadoPagosCoordinacion = '<span class="badge badge-danger">SOBREPAGO</span>';
					}
                $subdata[]= $estadoPagosCoordinacion;
                $subdata[]= $value->monto_final+ $value->impuestos_final;
                $subdata[]= $value->total_pagos_monto;
                $divAcciones='<div class="d-flex px-2 w-100" style="gap:1em;">';
					if($value->total_pagos < 4) {
						$divAcciones .='<div class="d-flex"  onclick="addPagosCoordination(' . $value->id . ', \'' . addslashes(trim($value->nombre)) . '\')" style="cursor:not-allowed;"><i class="fas fa-plus" style="cursor:pointer;"></i></div>';
					} 
					if($value->total_pagos > 0) {
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
        }catch (Exception $e) {
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
    public function getCursosPagos(){
        try {
            $arrData = $this->AdministracionModel->getCursosPagos();
            $data    = [];
            $index   = 1;
            foreach ($arrData as $row) {
					$subdata   = array();
					$subdata[] =  $row->ID_Pedido_Curso;
					$subdata[] = allTypeDate($row->Fe_Registro, '-', 0);
					$subdata[] = $row->No_Entidad ; 
					$subdata[] = $row->Nu_Celular_Entidad;
                    $subdata[] = "Curso";
                    $subdata[] = "CAMPAÑA";
                    $subdata[] = "ACCIONES";
					
                    $estadoCurso = '<span class="badge badge-secondary">' . $row->estado_pagos_coordinacion . '</span>';
					if ($row->total_pagos==0) {
						$estadoCurso = '<span class="badge badge-secondary">PENDIENTE</span>';
					} else if ($row->total_pagos<($row->Ss_Total)) {
						$estadoCurso = '<span class="badge badge-warning">ADELANTO</span>';
					} else if ($row->total_pagos==($row->Ss_Total)) {
						$estadoCurso = '<span class="badge badge-success">PAGADO</span>';
					} else if ($row->total_pagos>($row->Ss_Total)) {
						$estadoCurso = '<span class="badge badge-danger">SOBREPAGO</span>';
					}
                    $subdata[]= $estadoCurso;
                    $subdata[] = $row->No_Signo . '<input value="' . round($row->Ss_Total, 2) . '" readonly/>';	//importe		
					$subdata[] = "S/".round($row->total_pagos, 2);
                    $divAcciones='<div class="d-flex px-2 w-100" style="gap:1em;">';
					
					
					if($row->pagos_count > 0) {
						$divAcciones .= '<div class="d-flex"  onclick="viewClientePagosCurso(' . $row->ID_Pedido_Curso . ', \'' . addslashes(trim($row->No_Entidad)) . '\')">
												<i class="fas fa-eye" style="cursor:pointer;"></i>

                        </div>';
					}
					$divAcciones .=  '</div>';
                    $subdata[] = $divAcciones;
					$data[]= $subdata;

				}
            $output = array(
                "data" => $data
            );
            echo json_encode($output);
        }catch (Exception $e) {
            log_message('error', 'ContenedorConsolidado : getPagosCurso() => ' . $e->getMessage());
        }
    }
    public function getPagosCurso($idPedidoCurso){
        try {
            $arrData = $this->AdministracionModel->getPagosCurso($idPedidoCurso);
            $data    = [];
            $index   = 1;
            foreach ($arrData as $row) {
                $subdata = [];
                $subdata[] = $index;
                $subdata[] = allTypeDate($row->Fe_Registro, '-', 0);
                $subdata[] = $row->No_Entidad;
                $subdata[] = "$".round($row->Monto, 2);
                $subdata[] = '<a href='.$row->Voucher_Url.' download>
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
}