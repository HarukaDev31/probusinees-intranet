<?php
class ReporteMatriculaAlumnoModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $ID_Sede_Musica=$arrParams['ID_Sede_Musica'];
        $ID_Salon=$arrParams['ID_Salon'];

        $where_id_salon = ($arrParams['ID_Salon']!='0' ? ' AND MA.ID_Salon = " . $ID_Salon . "' : '');

        $query = "SELECT
MA.ID_Horario_Clase,
CONCAT(HC.Nu_Hora_Desde, ':', HC.Nu_Minuto_Desde, ' - ', HC.Nu_Hora_Hasta, ':', HC.Nu_Minuto_Hasta) AS Nombre_Hora,
MA.ID_Entidad_Alumno,
ALU.No_Contacto,
ALU.Fe_Nacimiento,
HC.ID_Dia_Semana,
C.No_Familia,
C.No_Html_Color,
TC.No_Descripcion AS No_Tipo_Clase,
S.No_Salon
FROM
matricula_alumno AS MA
JOIN familia AS C ON(MA.ID_Familia = C.ID_Familia)
JOIN horario_clase AS HC ON(MA.ID_Horario_Clase = HC.ID_Horario_Clase)
JOIN dia_semana AS DS ON(DS.ID_Dia_Semana = HC.ID_Dia_Semana)
JOIN entidad AS ALU ON(ALU.ID_Entidad = MA.ID_Entidad_Alumno)
JOIN tabla_dato AS TC ON(TC.ID_Tabla_Dato = MA.ID_Tipo_Clase)
JOIN salon AS S ON(S.ID_Salon = MA.ID_Salon)
WHERE
MA.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND MA.ID_Sede_Musica = " . $ID_Sede_Musica . "
" . $where_id_salon . "
ORDER BY
MA.ID_Horario_Clase,
MA.ID_Entidad_Alumno";
        
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
            return array(
                'sStatus' => 'success',
                'arrData' => $arrResponseSQL->result(),
            );
        }
        
        return array(
            'sStatus' => 'warning',
            'sMessage' => 'No se encontro registro',
        );
    }
}
