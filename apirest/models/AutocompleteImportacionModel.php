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
	
	public function getDataAutocompleteItemxUnidad($arrPost){
	    $sql = "SELECT * FROM (
(SELECT
CONCAT(PRO.ID_Producto, PRO.ID_Unidad_Medida) AS ID,
PRO.ID_Producto AS id_item,
PRO.Nu_Codigo_Barra AS Codigo,
PRO.ID_Unidad_Medida,
'' AS ID_Unidad_Medida_Precio,
CONCAT(PRO.No_Producto, ' - ', UM.No_Unidad_Medida, ' - Unidades: ', PRO.Qt_Unidad_Medida, ' - C/U: ', PRO.Ss_Precio_Importacion) AS Nombre,
PRO.No_Producto AS nombre_item,
PRO.Qt_Unidad_Medida AS cantidad_configurada_item,
PRO.Ss_Precio_Importacion AS precio_importacion,
UM.No_Unidad_Medida AS nombre_unidad_medida
FROM
producto AS PRO
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Compuesto = 0
AND PRO.Nu_Activar_Item_Lae_Shop = 1
AND (PRO.No_Producto LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!')
LIMIT 15
)
UNION ALL
(
SELECT
CONCAT(PRO.ID_Producto, PRO.ID_Unidad_Medida_Precio) AS ID,
PRO.ID_Producto AS id_item,
PRO.Nu_Codigo_Barra AS Codigo,
'' AS ID_Unidad_Medida,
PRO.ID_Unidad_Medida_Precio,
CONCAT(PRO.No_Producto, ' - ', UM.No_Unidad_Medida, ' - Unidades: ', PRO.Qt_Unidad_Medida_2, ' - C/U: ', PRO.Ss_Precio_Importacion_2) AS Nombre,
PRO.No_Producto AS nombre_item,
PRO.Qt_Unidad_Medida_2 AS cantidad_configurada_item,
PRO.Ss_Precio_Importacion_2 AS precio_importacion,
UM.No_Unidad_Medida AS nombre_unidad_medida
FROM
producto AS PRO
JOIN unidad_medida AS UM ON(UM.ID_Unidad_Medida = PRO.ID_Unidad_Medida_Precio)
WHERE
PRO.ID_Empresa = " . $this->user->ID_Empresa . "
AND PRO.Nu_Compuesto = 0
AND PRO.Nu_Activar_Item_Lae_Shop = 1
AND (PRO.No_Producto LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!' OR PRO.Nu_Codigo_Barra LIKE '%" . $this->db->escape_like_str($arrPost['global_search']) . "%' ESCAPE '!')
LIMIT 15
)
) AS A
ORDER BY
Nombre DESC
LIMIT 15
";
		return $this->db->query($sql)->result();
    }
}
