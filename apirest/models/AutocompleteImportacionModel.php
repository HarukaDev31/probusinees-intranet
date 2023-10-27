<?php
class AutocompleteImportacionModel extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function getDataAutocompleteProduct($arrPost){
	    $sql = "SELECT
PRO.ID_Producto AS ID,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.No_Producto AS Nombre
FROM
producto AS PRO
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Compuesto = 0
AND PRO.Nu_Activar_Item_Lae_Shop = 1
AND (PRO.No_Producto LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!')
ORDER BY
Nombre DESC
LIMIT 15";
		return $this->db->query($sql)->result();
    }
}
