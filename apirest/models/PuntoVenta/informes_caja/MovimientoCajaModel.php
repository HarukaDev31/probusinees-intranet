<?php
class MovimientoCajaModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $ID_Almacen=$arrParams['ID_Almacen'];
        $dInicio=$arrParams['Fe_Inicio'];
        $dFin=$arrParams['Fe_Fin'];
        $ID_Tipo_Operacion_Caja=$arrParams['ID_Tipo_Operacion_Caja'];
        $iIdEmpleado=$arrParams['iIdEmpleado'];
        $sNombreEmpleado=$arrParams['sNombreEmpleado'];
        
        $cond_tipo_operacion_caja = $ID_Tipo_Operacion_Caja > 0 ? 'AND TOC.ID_Tipo_Operacion_Caja = ' . $ID_Tipo_Operacion_Caja : '';
        $cond_empleado = ( $iIdEmpleado != '-' && $sNombreEmpleado != '-' ) ? 'AND EMPLE.ID_Entidad = ' . $iIdEmpleado : "";
        $cond_almacen = ( $ID_Almacen != '0' ) ? 'AND CP.ID_Almacen = ' . $ID_Almacen : "";
        
		$query = "SELECT
ALMA.No_Almacen,
CP.ID_Caja_Pos,
EMPLE.ID_Entidad AS ID_Empleado,
EMPLE.No_Entidad AS No_Empleado,
TOC.No_Tipo_Operacion_Caja,
CP.Fe_Movimiento,
MONE.No_Signo,
CP.Ss_Total,
CP.Txt_Nota,
TOC.Nu_Tipo
FROM
caja_pos AS CP
JOIN moneda AS MONE ON(MONE.ID_Moneda = CP.ID_Moneda)
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = CP.ID_Almacen)
JOIN tipo_operacion_caja AS TOC ON(TOC.ID_Tipo_Operacion_Caja = CP.ID_Tipo_Operacion_Caja)
JOIN matricula_empleado AS ME ON(CP.ID_Matricula_Empleado = ME.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(EMPLE.ID_Entidad = ME.ID_Entidad)
WHERE
CP.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND CP.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND CP.Fe_Movimiento BETWEEN '" . $dInicio . " 00:00:00' AND '" . $dFin . " 23:59:59'
AND TOC.Nu_Tipo IN(3,4,5,6)
" . $cond_tipo_operacion_caja . "
" . $cond_almacen . "
" . $cond_empleado . "
ORDER BY
CP.Fe_Movimiento DESC;";

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
