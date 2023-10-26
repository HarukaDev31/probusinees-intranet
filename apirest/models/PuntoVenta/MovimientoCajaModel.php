<?php
class MovimientoCajaModel extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
    
    public function addMovimientoCaja($arrPost){
		$objAlmacen = $this->db->query("SELECT ALMA.ID_Almacen, ALMA.No_Almacen FROM matricula_empleado AS ME JOIN almacen AS ALMA ON(ALMA.ID_Almacen = ME.ID_Almacen) WHERE ME.ID_Matricula_Empleado=" . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . " LIMIT 1")->row();

		if ($objAlmacen->ID_Almacen != $this->session->userdata['almacen']->ID_Almacen )
			return array('sStatus' => 'danger', 'sMessage' => 'Debes seleccionar el Almacén: ' . $objAlmacen->No_Almacen);
            
        //Iniciamos la transacción:
        $this->db->trans_begin();
        
        $this->db->insert('caja_pos', $arrPost);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('sStatus' => 'danger', 'sMessage' => 'Problemas al registrar');
        } else {
            $this->db->trans_commit();
            return array('sStatus' => 'success', 'sMessage' => 'Registro guardado');
        }
	}
	
    public function getReporte(){
		$query = "SELECT
AC.ID_Caja_Pos,
AC.Fe_Movimiento,
TOC.No_Tipo_Operacion_Caja,
MONE.No_Signo,
AC.Ss_Total,
AC.Txt_Nota,
TOC.Nu_Tipo
FROM
caja_pos AS AC
JOIN tipo_operacion_caja AS TOC ON(TOC.ID_Tipo_Operacion_Caja = AC.ID_Tipo_Operacion_Caja)
JOIN moneda AS MONE ON(MONE.ID_Moneda = AC.ID_Moneda)
WHERE
AC.ID_Empresa = " . $this->empresa->ID_Empresa . "
AND AC.ID_Organizacion = " . $this->empresa->ID_Organizacion . "
AND AC.ID_Matricula_Empleado = " . $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado . "
AND AC.Fe_Movimiento >= '" . $this->session->userdata['arrDataPersonal']['arrData'][0]->Fe_Matricula . "'
AND TOC.Nu_Tipo IN(5,6)
ORDER BY
AC.ID_Caja_Pos DESC";
        
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
	
    public function getMovimientoCaja($ID){
		$query = "SELECT
EMP.No_Empresa,
EMP.No_Empresa_Comercial,
AC.ID_Caja_Pos,
AC.Fe_Movimiento,
TOC.No_Tipo_Operacion_Caja,
MONE.No_Signo,
AC.Ss_Total,
AC.Txt_Nota,
EMPLE.No_Entidad AS No_Empleado
FROM
caja_pos AS AC
JOIN empresa AS EMP ON(EMP.ID_Empresa = AC.ID_Empresa)
JOIN tipo_operacion_caja AS TOC ON(TOC.ID_Tipo_Operacion_Caja = AC.ID_Tipo_Operacion_Caja)
JOIN matricula_empleado AS ME ON(AC.ID_Matricula_Empleado = ME.ID_Matricula_Empleado)
JOIN entidad AS EMPLE ON(EMPLE.ID_Entidad = ME.ID_Entidad)
JOIN moneda AS MONE ON(MONE.ID_Moneda = AC.ID_Moneda)
WHERE
AC.ID_Caja_Pos = " . $ID . " LIMIT 1";        
		return $this->db->query($query)->result();
    }
}
