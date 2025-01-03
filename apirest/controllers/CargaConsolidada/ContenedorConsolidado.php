<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContenedorConsolidado extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	private $file_path = '../assets/images/logos/';
	private $logo_cliente_path = '../assets/images/logos/';
	private $logo_cliente_logos_empresa_almacen_path = '../assets/images/logos_empresa_almacen/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		//$this->load->database('LAE_SYSTEMS');
		$this->load->model('CargaConsolidada/ContenedorConsolidadoModel');
		$this->load->model('HelperImportacionModel');
		if(!isset($this->session->userdata['usuario'])) {
			redirect('');
		}
	}
	
	public function listar($ID_Carga_Consolidada=0){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2', array("js_contenedor_consolidadado" => true));
			$this->load->view('CargaConsolidada/ContenedorConsolidadoView', array(
				'arrResponseConsolidado' => $arrResponseConsolidado,
				'ID_Carga_Consolidada' => $ID_Carga_Consolidada,
			));
			$this->load->view('footer_v2', array("js_contenedor_consolidadado" => true));
		}
	}
    public function index(){
        $arrData = $this->ContenedorConsolidadoModel->index();
        $data= array();
        foreach ($arrData as $row) {
            $subdata = array();
			$subdata[] = $row->mes;
			$subdata[] = $row->No_Pais;
			$subdata[] = $row->carga;
			$subdata[] = date("d/m/Y", strtotime($row->f_cierre));
			$subdata[] = date("d/m/Y", strtotime($row->f_puerto));
			$subdata[] = date("d/m/Y", strtotime($row->f_entrega));
			
			$subdata[] = $row->empresa;
			$btnView='<div>
			<i class="fas fa-eye" style="cursor:pointer;" onclick="viewSteps('.$row->id.')"></i>
			</div>';
			$subdata[] = $btnView;
			//if estado= pendiente use badge warning else success
			$divEstadoSelect="";
			// if($row->estado=="PENDIENTE"){
			// 	$divEstado='<div class="badge badge-warning">'.$row->estado.'</div>';
			// }else{
			// 	$divEstado='<div class="badge badge-success">'.$row->estado.'</div>';
			// }
			if($this->user->No_Grupo=="Coordinación"){
			$divEstado='<select class="form-control" id="estado-'.$row->id.'" name="estado" onchange="updateEstado('.$row->id.')">
				<option value="PENDIENTE" '.($row->estado=="PENDIENTE" ? "selected" : "").'>PENDIENTE</option>
				<option value="COMPLETADO" '.($row->estado=="COMPLETADO" ? "selected" : "").'>COMPLETADO</option>
			</select>';	

			}else{
				if($row->estado=="PENDIENTE"){
					$divEstado='<div class="badge badge-warning">'.$row->estado.'</div>';
				}else{
					$divEstado='<div class="badge badge-success">'.$row->estado.'</div>';
				}
			}
			$subdata[] = $divEstado;
			if($this->user->No_Grupo=="Coordinación"){
				$divAcciones='<div>
				<i class="fas fa-edit text-warning" style="cursor:pointer;" onclick="view('.$row->id.')"></i>
				<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCarga('.$row->id.')"></i>
				</div>';
			}
		
			$subdata[] = $divAcciones;
			$data[] = $subdata;
        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
	public function getPaises(){
		$arrResponse = $this->ContenedorConsolidadoModel->getPaises();
		echo json_encode($arrResponse);
	}
	public function store(){
		$data=$this->input->post();
		//parse all f_entrega and f_puerto from dd/mm/yyyy to yyyy-mm-dd
		$data['f_puerto']=$this->convertDateFormat($data['f_puerto']);
		$data['f_entrega']=$this->convertDateFormat($data['f_entrega']);
		$data['f_cierre']=$this->convertDateFormat($data['f_cierre']);
		// $data['f_puerto']=date("Y-m-d", strtotime($data['f_puerto']));
		// $data['f_entrega']=date("Y-m-d", strtotime($data['f_entrega']));
		$response = $this->ContenedorConsolidadoModel->store($data);
		

		$id=$response['id'];
		$socketResponse=$response['socketResponse'];
		$this->generateSteps($id);	
		echo json_encode([
			"status" => $response['status'],
			'id' => $id,
			"socketResponse"=>$socketResponse
		]);
	}
	public function update(){
		$data=$this->input->post();
		$data['f_puerto']=$this->convertDateFormat($data['f_puerto']);
		$data['f_entrega']=$this->convertDateFormat($data['f_entrega']);
		$data['f_cierre']=$this->convertDateFormat($data['f_cierre']);

		$arrResponse = $this->ContenedorConsolidadoModel->update($data);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function show($id){
	
		$arrResponse = $this->ContenedorConsolidadoModel->show($id);
		//parse all f_entrega and f_puerto to Y/m/d
		$arrResponse->f_entrega=date("Y/m/d", strtotime($arrResponse->f_entrega));
		$arrResponse->f_puerto=date("Y/m/d", strtotime($arrResponse->f_puerto));
	
		echo json_encode($arrResponse);
	}
	public function delete(){
		$id=$this->input->post('id');
		$arrResponse = $this->ContenedorConsolidadoModel->delete($id);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function generateSteps($idContenedor){
		$steps=$this->HelperImportacionModel->getCotizacionSteps($idContenedor);
		$result=$this->ContenedorConsolidadoModel->generateSteps($steps);
	}
	public function steps($idContenedor){
		$arrResponse = $this->ContenedorConsolidadoModel->getOrderProgress($idContenedor);
		echo json_encode(['data' => $arrResponse,'status' => "success"]);
	}
	public function step(){
		$stepIndex=$this->input->post('stepIndex');
		$idContenedor=$this->input->post('idContenedor');
		if($stepIndex==1){
			$arrResponse = $this->ContenedorConsolidadoModel->getContenedorCotizacion($idContenedor);
			$data= array();
			$index=1;
			foreach ($arrResponse as $row) {
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
				$divFile .= '</div>';
				$subdata[] = $divFile;
				if($this->user->No_Grupo=="Coordinación"){
				$selectEstado='<select class="form-control" id="estado-cotizacion-'.$row->id_cotizacion.'" name="estado" onchange="updateEstadoCotizacion('.$row->id_cotizacion.')">
					<option value="PENDIENTE" '.($row->estado=="PENDIENTE" ? "selected" : "").'>PENDIENTE</option>
					<option value="CONFIRMADO" '.($row->estado=="CONFIRMADO" ? "selected" : "").'>CONFIRMADO</option>
					<option value="DECLINADO" '.($row->estado=="DECLINADO" ? "selected" : "").'>DECLINADO</option>
				</select>';
				}else{
					//if estado= pendiente if confirmado use badge success else danger
					if($row->estado=="PENDIENTE"){
						$selectEstado='<div class="badge badge-warning">'.$row->estado.'</div>';
					}else if($row->estado=="CONFIRMADO"){
						$selectEstado='<div class="badge badge-success">'.$row->estado.'</div>';
					}else{
						$selectEstado='<div class="badge badge-danger">'.$row->estado.'</div>';
					}
				}
				$subdata[] = $selectEstado;
				$divAcciones='<div>
				<i class="fas fa-edit text-warning" style="cursor:pointer;" onclick="viewCotizacion('.$row->id_cotizacion.')"></i>
				<i class="fas fa-trash text-danger" style="cursor:pointer;" onclick="deleteCotizacion('.$row->id_cotizacion.')"></i>
				</div>';
				$subdata[] = $divAcciones;
				$data[] = $subdata;
				$index++;
			}
			$output = array(
				"data" => $data
			);
			echo json_encode($output);
			// echo json_encode(['data' => $arrResponse,'status' => "success"]);
		}
	}
	///function to step 1 cotizacion
	public function storeCotizacion(){
		try{
			$data=$this->input->post();
		$cotizacion = $_FILES['cotizacion'];
		
		$response = $this->ContenedorConsolidadoModel->storeCotizacion($data,$cotizacion);
		echo json_encode([
			"status" => $response['status'],
		]);
		}catch(Exception $e){
			echo json_encode([
				"status" => false,
				"message" => $e->getMessage()
			]);
		}
	}
	public function getTipoCliente(){
		$arrResponse = $this->ContenedorConsolidadoModel->getTipoCliente();
		echo json_encode($arrResponse);
	}
	public function deleteCotizacionFile($idCotizacion){
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacionFile($idCotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function deleteCotizacion($idCotizacion){
		$arrResponse = $this->ContenedorConsolidadoModel->deleteCotizacion($idCotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function uploadCotizacionFile(){
		$idCotizacion=$this->input->post('id');
		$file = $_FILES['file'];
		$arrResponse = $this->ContenedorConsolidadoModel->uploadCotizacionFile($idCotizacion,$file);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function showCotizacion($id){
		$arrResponse = $this->ContenedorConsolidadoModel->showCotizacion($id);
		echo json_encode($arrResponse);
	}
	public function updateCotizacion(){
		$data=$this->input->post();
		$cotizacion = $_FILES['cotizacion'];

		$arrResponse = $this->ContenedorConsolidadoModel->updateCotizacion($data,$cotizacion);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateEstadoCotizacion(){
		$id=$this->input->post('id');
		$estado=$this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstadoCotizacion($id,$estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	public function updateEstado(){
		$id=$this->input->post('id');
		$estado=$this->input->post('estado');
		$arrResponse = $this->ContenedorConsolidadoModel->updateEstado($id,$estado);
		echo json_encode([
			"status" => $arrResponse
		]);
	}
	function convertDateFormat($date) {
		$dateObject = DateTime::createFromFormat('d/m/Y', $date);
		return $dateObject ? $dateObject->format('Y-m-d') : null; // Devuelve null si la fecha no es válida
	}
}