<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class ReporteMatriculaAlumnoController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('Escuela/ReporteMatriculaAlumnoModel');
		$this->load->model('HelperModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header');
			$this->load->view('Escuela/ReporteMatriculaAlumnoView');
			$this->load->view('footer', array("js_escuela_reporte_matricula_alumno" => true));
		}
	}
	
    private function getReporte($arrParams){
        $arrResponseModal = $this->ReporteMatriculaAlumnoModel->getReporte($arrParams);
        if ( $arrResponseModal['sStatus']=='success' ) {
            $data = array();
            
            $arrParams = array_merge($arrParams, array('iIdEmpresa' => $this->empresa->ID_Empresa));
            $arrResponseHorarioClase = $this->HelperModel->getHorarioClaseReporte($arrParams);

            $No_Class_Color_Tipo_Clase='';
            foreach ($arrResponseModal['arrData'] as $row) {
                $rows = array();
                $rows['ID_Horario_Clase'] = $row->ID_Horario_Clase;
                $rows['ID_Entidad_Alumno'] = $row->ID_Entidad_Alumno;
                $rows['No_Tipo_Clase'] = $row->No_Tipo_Clase;

                $No_Class_Color_Tipo_Clase='69a1f7';
                if ($row->No_Tipo_Clase=='Presencial')
                    $No_Class_Color_Tipo_Clase='6de6af';
                else if ($row->No_Tipo_Clase=='Ambos')
                    $No_Class_Color_Tipo_Clase='ff6c7a';

                $rows['No_Class_Color_Tipo_Clase'] = $No_Class_Color_Tipo_Clase;
                $rows['No_Familia'] = $row->No_Familia;
                $rows['No_Html_Color'] = $row->No_Html_Color;
                $rows['No_Contacto'] = $row->No_Contacto;
                $rows['No_Salon'] = $row->No_Salon;
                $rows['Nu_Edad'] = diferenciaFechasMultipleFormato(dateNow('fecha'), $row->Fe_Nacimiento, 'year');
                $rows['ID_Dia_Semana'] = $row->ID_Dia_Semana;
                $rows['Nombre_Hora'] = $row->Nombre_Hora;
                $data[] = (object)$rows;
            }
            return array(
                'sStatus' => 'success',
                'arrData' => $data,
                'arrDataHorarioClase' => $arrResponseHorarioClase['arrData'],
            );
        } else {
            return $arrResponseModal;
        }
    }
    
	public function sendReporte(){
        $arrParams = array(
            'ID_Sede_Musica' => $this->input->post('ID_Sede_Musica'),
            'ID_Salon' => $this->input->post('ID_Salon'),
        );
        echo json_encode($this->getReporte($arrParams));
    }
}
