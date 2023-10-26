<?php
class VentasTiposDocumentoSunatModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
    public function getReporte($ID_Empresa, $Fe_Inicio, $Fe_Fin, $iDocumentStatus, $ID_Almacen, $Nu_Tipo_Impuesto){
        $cond_document_status = ($iDocumentStatus == 0 ? "" : "AND VC.Nu_Estado=".$iDocumentStatus);
        $where_id_almacen = ($ID_Almacen > 0 ? 'AND VC.ID_Almacen = ' . $ID_Almacen : '');
        
        $campo_total_y_gratuita = 'SUM(VC.Ss_Total)';
        if ( $Nu_Tipo_Impuesto == 1 )
            $campo_total_y_gratuita = '(SELECT SUM(CASE WHEN IMP.Nu_Tipo_Impuesto = 4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total FROM documento_detalle AS VD
            JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
            JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
            WHERE VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera
          )';
        else if ( $Nu_Tipo_Impuesto == 2 )
            $campo_total_y_gratuita = '(SELECT SUM(CASE WHEN IMP.Nu_Tipo_Impuesto != 4 THEN VD.Ss_Total ELSE 0 END) AS Ss_Total FROM documento_detalle AS VD
                JOIN impuesto_cruce_documento AS ICDOCU ON(ICDOCU.ID_Impuesto_Cruce_Documento = VD.ID_Impuesto_Cruce_Documento)
                JOIN impuesto AS IMP ON(IMP.ID_Impuesto = ICDOCU.ID_Impuesto)
                WHERE VD.ID_Documento_Cabecera = VC.ID_Documento_Cabecera
            )';

        $query = "SELECT
ALMA.ID_Almacen,
ALMA.No_Almacen,
 VC.Fe_Emision,
 BOL.Nu_Cantidad_Trans_BOL,
 BOL.Ss_Total_BOL,
 FACT.Nu_Cantidad_Trans_FACT,
 FACT.Ss_Total_FACT,
 NC.Nu_Cantidad_Trans_NC,
 NC.Ss_Total_NC,
 ND.Nu_Cantidad_Trans_ND,
 ND.Ss_Total_ND
FROM
 documento_cabecera AS VC
JOIN almacen AS ALMA ON(ALMA.ID_Almacen = VC.ID_Almacen)
 LEFT JOIN (
 SELECT
  VC.ID_Almacen,
  VC.Fe_Emision,
  COUNT(*) AS Nu_Cantidad_Trans_BOL,
  " . $campo_total_y_gratuita . " AS Ss_Total_BOL
 FROM
  documento_cabecera AS VC
 WHERE
  VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento = 4
  AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
  " . $cond_document_status . "
  " . $where_id_almacen . "
 GROUP BY
  VC.ID_Almacen,
  VC.Fe_Emision
 ) AS BOL ON (BOL.Fe_Emision = VC.Fe_Emision AND BOL.ID_Almacen = VC.ID_Almacen)
 LEFT JOIN (
 SELECT
  VC.ID_Almacen,
  VC.Fe_Emision,
  COUNT(*) AS Nu_Cantidad_Trans_FACT,
  " . $campo_total_y_gratuita . " AS Ss_Total_FACT
 FROM
  documento_cabecera AS VC
 WHERE
  VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento = 3
  AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
  " . $cond_document_status . "
  " . $where_id_almacen . "
 GROUP BY
  VC.ID_Almacen,
  VC.Fe_Emision
 ) AS FACT ON (FACT.Fe_Emision = VC.Fe_Emision AND FACT.ID_Almacen = VC.ID_Almacen)
 LEFT JOIN (
 SELECT
  VC.ID_Almacen,
  VC.Fe_Emision,
  COUNT(*) AS Nu_Cantidad_Trans_NC,
  " . $campo_total_y_gratuita . " AS Ss_Total_NC
 FROM
  documento_cabecera AS VC
 WHERE
  VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento = 5
  AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
  " . $cond_document_status . "
  " . $where_id_almacen . "
 GROUP BY
  VC.ID_Almacen,
  VC.Fe_Emision
 ) AS NC ON (NC.Fe_Emision = VC.Fe_Emision AND NC.ID_Almacen = VC.ID_Almacen)
 LEFT JOIN (
 SELECT
  VC.ID_Almacen,
  VC.Fe_Emision,
  COUNT(*) AS Nu_Cantidad_Trans_ND,
  " . $campo_total_y_gratuita . " AS Ss_Total_ND
 FROM
  documento_cabecera AS VC
 WHERE
  VC.ID_Empresa = " . $this->user->ID_Empresa . "
  AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
  AND VC.ID_Tipo_Asiento = 1
  AND VC.ID_Tipo_Documento = 6
  AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
  " . $cond_document_status . "
  " . $where_id_almacen . "
 GROUP BY
  VC.ID_Almacen,
  VC.Fe_Emision
 ) AS ND ON (ND.Fe_Emision = VC.Fe_Emision AND ND.ID_Almacen = VC.ID_Almacen)
WHERE
 VC.ID_Empresa = " . $this->user->ID_Empresa . "
 AND VC.ID_Organizacion = " . $this->user->ID_Organizacion . "
 AND VC.ID_Tipo_Asiento = 1
 AND VC.ID_Tipo_Documento IN(3,4,5,6)
 AND VC.Fe_Emision BETWEEN '" . $Fe_Inicio . "' AND '" . $Fe_Fin . "'
 " . $cond_document_status . "
  " . $where_id_almacen . "
GROUP BY
ALMA.ID_Almacen,
ALMA.No_Almacen,
VC.Fe_Emision
ORDER BY
ALMA.ID_Almacen DESC,
VC.Fe_Emision DESC;";
        return $this->db->query($query)->result();
    }
}
