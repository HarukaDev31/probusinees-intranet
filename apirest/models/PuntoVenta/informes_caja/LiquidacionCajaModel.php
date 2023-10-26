<?php
class LiquidacionCajaModel extends CI_Model{
	  public function __construct(){
		  parent::__construct();
	  }
	
    public function getReporte($arrParams){
        $ID_Almacen=$arrParams['ID_Almacen'];
        $dInicio=$arrParams['Fe_Inicio'];
        $dFin=$arrParams['Fe_Fin'];
        $iIdEmpleado=$arrParams['iIdEmpleado'];
        $sNombreEmpleado=$arrParams['sNombreEmpleado'];
        $ID_Organizacion=$arrParams['ID_Organizacion'];
        $cond_empleado = ( $iIdEmpleado != '-' && $sNombreEmpleado != '-' ) ? 'AND TRAB.ID_Entidad = ' . $iIdEmpleado : "";
        $cond_almacen = ( $ID_Almacen != '0' ) ? 'AND AC.ID_Almacen = ' . $ID_Almacen : "";
        $cond_organizacion = ( $ID_Organizacion != '0' ) ? 'AND AC.ID_Organizacion = ' . $ID_Organizacion : "";
        
        //AND AC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "

		$query = "SELECT
ORG.No_Organizacion,
ALMA.No_Almacen,
ME.ID_Matricula_Empleado,
AC.ID_Caja_Pos AS ID_Caja_Pos_Apertura,
CC.ID_Caja_Pos AS ID_Caja_Pos_Cierre,
TRAB.No_Entidad,
AC.Fe_Movimiento AS Fe_Apertura,
CC.Fe_Movimiento AS Fe_Cierre,
MONE.No_Signo,
CC.Ss_Expectativa,
CC.Ss_Total,
AC.Txt_Nota
FROM
caja_pos AS AC
JOIN caja_pos AS CC ON(AC.ID_Caja_Pos = CC.ID_Enlace_Apertura_Caja_Pos)
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = AC.ID_Almacen)
JOIN organizacion AS ORG ON(ORG.ID_Organizacion = AC.ID_Organizacion)
JOIN moneda AS MONE ON(MONE.ID_Moneda = AC.ID_Moneda)
JOIN matricula_empleado AS ME ON(ME.ID_Matricula_Empleado = AC.ID_Matricula_Empleado)
JOIN entidad AS TRAB ON(TRAB.ID_Entidad = ME.ID_Entidad)
WHERE
AC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND AC.Fe_Movimiento BETWEEN '" . $dInicio . " 00:00:00' AND '" . $dFin . " 23:59:59'
" . $cond_almacen . "
" . $cond_empleado . "
" . $cond_organizacion . "
ORDER BY
AC.Fe_Movimiento DESC,
TRAB.No_Entidad ASC,
AC.ID_Moneda";
        
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
