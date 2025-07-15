<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/NotificationTrait.php';
require_once APPPATH . 'traits/WebSocketTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
class CatalogoModel extends CI_Model
{
    use FileTrait, WebSocketTrait, NotificationTrait;
    private $table = "catalogo_producto";
    private $catalogoUrl = "assets/catalogo_productos";

    private $ROLE_PERU = 'CatalogoPeru';

    private $ROLE_CHINA = 'CatalogoChina';
    public function __construct()
    {
        try {
            parent::__construct();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function saveProduct($data, $files)
    {
        try {
            $this->maxFileSize = 1000000;
            $this->setAllowedExtensionsImagesOfficeFilesVideos();

            $contactCardUrl = null;
            $mainImageUrl = null;
            $aditionalImage1Url = null;
            $aditionalImage2Url = null;
            $aditionalVideo1Url = null;
            $randomUuid = uniqid();
            if ($this->user->No_Grupo == $this->ROLE_CHINA) {
                $dataToInsert = array(
                    'nombre' => $data['nombre'],
                    'precio' => $data['precio'],
                    'qty_box' => $data['qtyXbox'],
                    'cbm_box' => $data['cbmXbox'],
                    'moq' => $data['moq'],
                    'dias_entrega' => $data['diasEntrega'],
                    'delivery' => $data['delivery'],
                    'colores' => $data['colores'],
                    'notas' => $data['notas'],
                    'whechat_phone' => $data['wechatPhone'],
                    'contact_card_url' => $contactCardUrl,

                );
            } else if ($this->user->No_Grupo == $this->ROLE_PERU) {
                $dataToInsert = array(
                    'servicio_impo' => $data['servicioImpo'],
                    'arancel' => $data['arancel'],
                    'igv' => $data['igv'],
                    'antidumping' => $data['antidumping'],
                    'percepcion' => $data['percepcion'],
                    'precio_peru' => $data['precio_peru'],
                    'precio_usd' => $data['precio_usd'],

                );
            }
            $catalogProductUrl = $this->catalogoUrl . '/';
            if ($data['productId'] != null) {

                //try to unlink files
                $this->db->select('contact_card_url,main_image_url,aditional_image1_url,aditional_image2_url,aditional_video1_url');
                $this->db->from($this->table);
                $this->db->where('id', $data['productId']);
                $query = $this->db->get();
                if ($this->db->error()['code'] == 0) {
                    if ($this->user->No_Grupo == $this->ROLE_CHINA) {
                        $dataToDelete = $query->row();
                        $pattern = '/probusinees-intranet\/(.+)/';
                        if (preg_match($pattern, $dataToDelete->contact_card_url, $matches)) {
                            $contactCardUrlDelete = $matches[1];
                            unlink($contactCardUrlDelete);
                        }
                        if (preg_match($pattern, $dataToDelete->main_image_url, $matches)) {
                            $mainImageUrlDelete = $matches[1];
                            log_message('error', $mainImageUrlDelete);
                            unlink($mainImageUrlDelete);
                        }
                        if (preg_match($pattern, $dataToDelete->aditional_image1_url, $matches)) {
                            $aditionalImage1UrlDelete = $matches[1];
                            unlink($aditionalImage1UrlDelete);
                        }
                        if (preg_match($pattern, $dataToDelete->aditional_image2_url, $matches)) {
                            $aditionalImage2UrlDelete = $matches[1];
                            unlink($aditionalImage2UrlDelete);
                        }
                        if (preg_match($pattern, $dataToDelete->aditional_video1_url, $matches)) {
                            $aditionalVideo1UrlDelete = $matches[1];
                            unlink($aditionalVideo1UrlDelete);
                        }
                        if (isset($files['contactCard']) && $files['contactCard']['size'] > 0) {
                            $contactCardUrl = $this->uploadSingleFile($files['contactCard'], $catalogProductUrl);
                        }
                        if (isset($files['mainImage']) && $files['mainImage']['size'] > 0) {
                            $mainImageUrl = $this->uploadSingleFile($files['mainImage'], $catalogProductUrl);
                        }
                        if (isset($files['additionalImage1']) && $files['additionalImage1']['size'] > 0) {
                            $aditionalImage1Url = $this->uploadSingleFile($files['additionalImage1'], $catalogProductUrl);
                        }
                        if (isset($files['additionalImage2']) && $files['additionalImage2']['size'] > 0) {
                            $aditionalImage2Url = $this->uploadSingleFile($files['additionalImage2'], $catalogProductUrl);
                        }
                        if (isset($files['additionalVideo1']) && $files['additionalVideo1']['size'] > 0) {
                            $aditionalVideo1Url = $this->uploadSingleFile($files['additionalVideo1'], $catalogProductUrl);
                        }
                        $dataToInsert = array(
                            'nombre' => $data['nombre'],
                            'precio' => $data['precio'],
                            'qty_box' => $data['qtyXbox'],
                            'cbm_box' => $data['cbmXbox'],
                            'moq' => $data['moq'],
                            'dias_entrega' => $data['diasEntrega'],
                            'delivery' => $data['delivery'],
                            'colores' => $data['colores'],
                            'notas' => $data['notas'],
                            'whechat_phone' => $data['wechatPhone'],
                            'contact_card_url' => $contactCardUrl,
                            'main_image_url' => $mainImageUrl,
                            'aditional_image1_url' => $aditionalImage1Url,
                            'aditional_image2_url' => $aditionalImage2Url,
                            'aditional_video1_url' => $aditionalVideo1Url
                        );
                        if (isset($data['nombre']) && !empty($data['nombre'])) {
                            // Obtener todos los IDs ordenados
                            $this->db->select('id');
                            $this->db->from($this->table);
                            $this->db->order_by('id', 'ASC');
                            $ids = $this->db->get()->result_array();
                            // Buscar la posición del producto actual
                            $correlativo = 1;
                            foreach ($ids as $index => $row) {
                                if ($row['id'] == $data['productId']) {
                                    $correlativo = $index; // +1 porque el array es base 0
                                    break;
                                }
                            }
                            $correlativo = str_pad($correlativo, 4, '0', STR_PAD_LEFT);
                            $anio = date('y');
                            $nombre = trim($data['nombre']);
                            $primera_palabra = explode(' ', $nombre)[0];
                            $iniciales = strtoupper(substr($primera_palabra, 0, 2));
                            // Puedes usar el mismo ID como correlativo para mantener unicidad
                            $codigo = "COD-{$anio}{$iniciales}{$correlativo}";
                            $dataToInsert['cod_producto'] = $codigo;
                        }
                    }
                    $this->db->where('id', $data['productId']);
                    $this->db->update($this->table, $dataToInsert);
                } else {
                    log_message('error', 'Error al obtener el producto: ' . $this->db->error()['message']);
                }



                if ($this->db->error()['code'] == 0) {
                    return array('status' => true, 'message' => 'Producto actualizado correctamente');
                } else {
                    log_message('error', 'Error al actualizar el producto: ' . $this->db->error()['message']);
                    return array('status' => false, 'message' => 'Error al actualizar el producto');
                }
            } else {

                $catalogProductUrl = $this->catalogoUrl . '/';
                if ($this->user->No_Grupo == $this->ROLE_CHINA) {

                    if (isset($files['contactCard']) && $files['contactCard']['size'] > 0) {
                        $contactCardUrl = $this->uploadSingleFile($files['contactCard'], $catalogProductUrl);
                    }
                    if (isset($files['mainImage']) && $files['mainImage']['size'] > 0) {
                        $mainImageUrl = $this->uploadSingleFile($files['mainImage'], $catalogProductUrl);
                    }
                    if (isset($files['additionalImage1']) && $files['additionalImage1']['size'] > 0) {
                        $aditionalImage1Url = $this->uploadSingleFile($files['additionalImage1'], $catalogProductUrl);
                    }
                    if (isset($files['additionalImage2']) && $files['additionalImage2']['size'] > 0) {
                        $aditionalImage2Url = $this->uploadSingleFile($files['additionalImage2'], $catalogProductUrl);
                    }
                    if (isset($files['additionalVideo1']) && $files['additionalVideo1']['size'] > 0) {
                        $aditionalVideo1Url = $this->uploadSingleFile($files['additionalVideo1'], $catalogProductUrl);
                    }
                    $dataToInsert = array(
                        'nombre' => $data['nombre'],
                        'precio' => $data['precio'],
                        'qty_box' => $data['qtyXbox'],
                        'cbm_box' => $data['cbmXbox'],
                        'moq' => $data['moq'],
                        'dias_entrega' => $data['diasEntrega'],
                        'delivery' => $data['delivery'],
                        'colores' => $data['colores'],
                        'notas' => $data['notas'],
                        'whechat_phone' => $data['wechatPhone'],
                        'contact_card_url' => $contactCardUrl,
                        'main_image_url' => $mainImageUrl,
                        'aditional_image1_url' => $aditionalImage1Url,
                        'aditional_image2_url' => $aditionalImage2Url,
                        'aditional_video1_url' => $aditionalVideo1Url
                    );
                    // --- GENERAR CÓDIGO DE PRODUCTO ---
                    // Año en dos dígitos
                    $anio = date('y');
                    // Primeras dos letras de la primera palabra del nombre
                    $nombre = trim($data['nombre']);
                    $primera_palabra = explode(' ', $nombre)[0];
                    $iniciales = strtoupper(substr($primera_palabra, 0, 2));
                    // Correlativo (productos existentes + 1)
                    $this->db->select('COUNT(*) as total');
                    $this->db->from($this->table);
                    $query = $this->db->get();
                    $correlativo = str_pad($query->row()->total + 1, 4, '0', STR_PAD_LEFT);
                    // Código final
                    $codigo = "COD-{$anio}{$iniciales}{$correlativo}";
                    $dataToInsert['cod_producto'] = $codigo;
                }
                $this->db->insert($this->table, $dataToInsert);
                if ($this->db->error()['code'] == 0) {
                    return array('status' => true, 'message' => 'Producto guardado correctamente');
                } else {
                    log_message('error', 'Error al guardar el producto: ' . $this->db->error()['message']);
                    return array('status' => false, 'message' => 'Error al guardar el producto');
                }
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getCatalogo($filters = [])
    {
        try {
            $this->db->select('id,cod_producto,nombre,precio,moq,main_image_url,aditional_image1_url,aditional_image2_url,aditional_video1_url,precio_peru,precio_usd,prices_range,status,created_at,category_id,url_tienda,url_alibaba');
            $this->db->from($this->table);
            // Excluir productos donde prices_range es NULL
            $this->db->where('prices_range IS NOT NULL');

            $this->db->where('status', 'PENDIENTE');

            // Filtro por fecha inicio
            if (!empty($filters['fechaInicio'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaInicio']);
                if ($fecha) {
                    $this->db->where('created_at >=', $fecha->format('Y-m-d 00:00:00'));
                }
            }
            // Filtro por fecha fin
            if (!empty($filters['fechaFin'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaFin']);
                if ($fecha) {
                    $this->db->where('created_at <=', $fecha->format('Y-m-d 23:59:59'));
                }
            }
            // Filtro por categoría
            if (!empty($filters['categoria']) && $filters['categoria'] != '0') {
                $this->db->where('category_id', $filters['categoria']);
            }
            // Filtro por nombre, precio u orden (sort)
            if (!empty($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'nameAZ':
                        $this->db->order_by('nombre', 'ASC');
                        break;
                    case 'nameZA':
                        $this->db->order_by('nombre', 'DESC');
                        break;
                    case 'pricemin':
                    case 'pricemax':
                        break;
                    case 'recent':
                    default:
                        $this->db->order_by('created_at', 'DESC');
                        break;
                }
            } else {
                $this->db->order_by('created_at', 'DESC');
            }

            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                $result = $query->result();

                if (!empty($filters['sort']) && ($filters['sort'] === 'pricemin' || $filters['sort'] === 'pricemax')) {
                    usort($result, function($a, $b) use ($filters) {
                        // Obtener el menor precio de prices_range para cada producto
                        $aPrices = json_decode($a->prices_range, true);
                        $bPrices = json_decode($b->prices_range, true);

                        $aMin = 0;
                        $bMin = 0;
                        if (is_array($aPrices) && count($aPrices) > 0) {
                            $aMin = min(array_map(function($p) {
                                return floatval(str_replace(',', '', $p['price']));
                            }, $aPrices));
                        }
                        if (is_array($bPrices) && count($bPrices) > 0) {
                            $bMin = min(array_map(function($p) {
                                return floatval(str_replace(',', '', $p['price']));
                            }, $bPrices));
                        }

                        if ($filters['sort'] === 'pricemin') {
                            return $aMin <=> $bMin; // menor a mayor
                        } else {
                            return $bMin <=> $aMin; // mayor a menor
                        }
                    });
                }

                return array('status' => true, 'data' => $result,'total'=>count($result));
            } else {
                log_message('error', 'Error al obtener el catálogo: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el catálogo');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getCatalogoCompletados($filters = [])
    {
        try {
            $this->db->select('id,cod_producto,nombre,precio,moq,main_image_url,precio_peru,precio_usd,status,created_at,category_id');
            $this->db->from($this->table);
            $this->db->where('status', 'COTIZADO');

            // Filtro por fecha inicio
            if (!empty($filters['fechaInicio'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaInicio']);
                if ($fecha) {
                    $this->db->where('created_at >=', $fecha->format('Y-m-d 00:00:00'));
                }
            }
            // Filtro por fecha fin
            if (!empty($filters['fechaFin'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaFin']);
                if ($fecha) {
                    $this->db->where('created_at <=', $fecha->format('Y-m-d 23:59:59'));
                }
            }
            // Filtro por categoría
            if (!empty($filters['categoria']) && $filters['categoria'] != '0') {
                $this->db->where('category_id', $filters['categoria']);
            }

            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->result(),'total'=>count($query->result()));
            } else {
                log_message('error', 'Error al obtener el catálogo: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el catálogo');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getCatalogoSeleccionados($filters = []){
        try {
            // Verifica el perfil del usuario
            if (isset($this->user) && $this->user->No_Grupo == $this->ROLE_PERU) {
                $this->db->select('catalogo_producto.id,cod_producto,nombre,precio,moq,main_image_url,precio_peru,prices_range,status,
                    url_tienda, url_alibaba,
                    catalogo_producto_category.name as category_name
                ');
            } else {
                // Selección completa para otros perfiles
                $this->db->select('catalogo_producto.id,cod_producto,nombre,precio,moq,main_image_url,precio_peru,precio_usd,prices_range,status,
                    url_tienda, url_alibaba,
                    catalogo_producto_category.name as category_name
                ');
            }
            $this->db->from($this->table);
            $this->db->join('catalogo_producto_category', 'catalogo_producto_category.id = catalogo_producto.category_id', 'left');
            // Excluir productos donde prices_range es NULL
            $this->db->where('prices_range IS NOT NULL');
            
            $this->db->where('status', 'EN TIENDA');

            // Filtro por fecha inicio
            if (!empty($filters['fechaInicio'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaInicio']);
                if ($fecha) {
                    $this->db->where('created_at >=', $fecha->format('Y-m-d 00:00:00'));
                }
            }
            // Filtro por fecha fin
            if (!empty($filters['fechaFin'])) {
                $fecha = DateTime::createFromFormat('d/m/Y', $filters['fechaFin']);
                if ($fecha) {
                    $this->db->where('created_at <=', $fecha->format('Y-m-d 23:59:59'));
                }
            }
            // Filtro por categoría
            if (!empty($filters['categoria']) && $filters['categoria'] != '0') {
                $this->db->where('category_id', $filters['categoria']);
            }

            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->result(),'total'=>count($query->result()));
            } else {
                log_message('error', 'Error al obtener el catálogo: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el catálogo');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getProductDetails($id)
    {
        try {
            $this->db->select('*');
            $this->db->from($this->table);
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->row());
            } else {
                log_message('error', 'Error al obtener el producto: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el producto');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    private function reordenarCodigosCatalogo()
    {
        // Obtener todos los productos ordenados por ID ASC
        $this->db->select('id, nombre');
        $this->db->from($this->table);
        $this->db->order_by('id', 'ASC');
        $productos = $this->db->get()->result();

        $anio = date('y');
        foreach ($productos as $index => $producto) {
            $correlativo = str_pad($index, 4, '0', STR_PAD_LEFT);
            $nombre = trim($producto->nombre);
            $primera_palabra = explode(' ', $nombre)[0];
            $iniciales = strtoupper(substr($primera_palabra, 0, 2));
            $codigo = "COD-{$anio}{$iniciales}{$correlativo}";

            $this->db->where('id', $producto->id);
            $this->db->update($this->table, ['cod_producto' => $codigo]);
        }
    }
    public function deleteProduct($id)
    {
        try {
            // Eliminar registros relacionados en catalogo_producto_media
            $this->db->where('id_catalogo_producto', $id);
            $this->db->delete('catalogo_producto_media');


            //get files and unlink
            $this->db->select('contact_card_url,main_image_url,aditional_image1_url,aditional_image2_url,aditional_video1_url');
            $this->db->from($this->table);
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                $data = $query->row();
                $pattern = '/probusinees-intranet\/(.+)/';
                if (preg_match($pattern, $data->contact_card_url, $matches)) {
                    $contactCardUrl = $matches[1];
                    unlink($contactCardUrl);
                }
                if (preg_match($pattern, $data->main_image_url, $matches)) {
                    $mainImageUrl = $matches[1];
                    unlink($mainImageUrl);
                }
                if (preg_match($pattern, $data->aditional_image1_url, $matches)) {
                    $aditionalImage1Url = $matches[1];
                    unlink($aditionalImage1Url);
                }
                if (preg_match($pattern, $data->aditional_image2_url, $matches)) {
                    $aditionalImage2Url = $matches[1];
                    unlink($aditionalImage2Url);
                }
                if (preg_match($pattern, $data->aditional_video1_url, $matches)) {
                    $aditionalVideo1Url = $matches[1];
                    unlink($aditionalVideo1Url);
                }
                $this->db->where('id', $id);
                $this->db->delete($this->table);
            } else {
                log_message('error', 'Error al obtener el producto: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el producto');
            }
            if ($this->db->error()['code'] == 0) {
                // Reordenar los códigos después de borrar
                $this->reordenarCodigosCatalogo();
                return array('status' => true, 'message' => 'Producto eliminado correctamente');
            } else {
                log_message('error', 'Error al eliminar el producto: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al eliminar el producto');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function deleteMultipleProducts($ids)
    {
        try {
            $ids = array_map('intval', $ids);
            if (empty($ids)) {
                return array('status' => false, 'message' => 'No se recibieron IDs para eliminar');
            }

            // Eliminar registros relacionados en catalogo_producto_media
            $this->db->where_in('id_catalogo_producto', $ids);
            $this->db->delete('catalogo_producto_media');

            // Obtener productos para borrar archivos asociados
            $productos = $this->db->where_in('id', $ids)->get($this->table)->result();
            $pattern = '/probusinees-intranet\/(.+)/';
            foreach ($productos as $data) {
                // contact_card_url
                $matches = [];
                if (!empty($data->contact_card_url) && preg_match($pattern, $data->contact_card_url, $matches) && isset($matches[1])) {
                    $contactCardUrl = $matches[1];
                    if (file_exists($contactCardUrl)) @unlink($contactCardUrl);
                }
                // main_image_url
                $matches = [];
                if (!empty($data->main_image_url) && preg_match($pattern, $data->main_image_url, $matches) && isset($matches[1])) {
                    $mainImageUrl = $matches[1];
                    if (file_exists($mainImageUrl)) @unlink($mainImageUrl);
                }
                // aditional_image1_url
                $matches = [];
                if (!empty($data->aditional_image1_url) && preg_match($pattern, $data->aditional_image1_url, $matches) && isset($matches[1])) {
                    $aditionalImage1Url = $matches[1];
                    if (file_exists($aditionalImage1Url)) @unlink($aditionalImage1Url);
                }
                // aditional_image2_url
                $matches = [];
                if (!empty($data->aditional_image2_url) && preg_match($pattern, $data->aditional_image2_url, $matches) && isset($matches[1])) {
                    $aditionalImage2Url = $matches[1];
                    if (file_exists($aditionalImage2Url)) @unlink($aditionalImage2Url);
                }
                // aditional_video1_url
                $matches = [];
                if (!empty($data->aditional_video1_url) && preg_match($pattern, $data->aditional_video1_url, $matches) && isset($matches[1])) {
                    $aditionalVideo1Url = $matches[1];
                    if (file_exists($aditionalVideo1Url)) @unlink($aditionalVideo1Url);
                }
            }

            // Eliminar los productos de la base de datos
            $this->db->where_in('id', $ids);
            $this->db->delete($this->table);

            if ($this->db->affected_rows() > 0) {
                $this->reordenarCodigosCatalogo();
                return array('status' => true, 'message' => 'Productos eliminados correctamente');
            } else {
                return array('status' => false, 'message' => 'No se eliminaron productos');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return array('status' => false, 'message' => 'Error al eliminar los productos');
        }
    }
    public function sendCotizacion($productId)
    {
        try {
            ///update status to COTIZADO
            $this->db->set('status', 'COTIZADO');
            $this->db->where('id', $productId);
            $this->db->update($this->table);
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'message' => 'Cotización enviada correctamente');
            } else {
                log_message('error', 'Error al enviar la cotización: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al enviar la cotización');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function pasarTienda($productIds)
    {
        $productsIds = json_decode($productIds);
        foreach ($productsIds as $productId) {
            $this->db->set('status', 'EN TIENDA');
            $this->db->where('id', $productId);
            $this->db->update($this->table);
        }
        if ($this->db->error()['code'] == 0) {
            return array('status' => true, 'message' => 'Productos guardados correctamente');
        } else {
            log_message('error', 'Error al guardar los productos: ' . $this->db->error()['message']);
            return array('status' => false, 'message' => 'Error al guardar los productos');
        }
    }
    public function getCategorias()
    {
        //get id name from catalogo_producto_category
        $this->db->select('id,name');
        $this->db->from('catalogo_producto_category');
        $query = $this->db->get();
        if ($this->db->error()['code'] == 0) {
            return array('status' => true, 'data' => $query->result());
        } else {
            log_message('error', 'Error al obtener las categorias: ' . $this->db->error()['message']);
            return array('status' => false, 'message' => 'Error al obtener las categorias');
        }
    }
    public function createCategoria($categoria)
    {
        //insert into catalogo_producto_category

        //create slug equal trim name  to lower and parse and separate words with -
        $slug = strtolower(trim($categoria));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        $dataToInsert = array(
            'name' => $categoria,
            'slug' => $slug
        );
        $this->db->insert('catalogo_producto_category', $dataToInsert);
        if ($this->db->error()['code'] == 0) {
            return array('status' => true, 'message' => 'Categoria creada correctamente');
        } else {
            log_message('error', 'Error al crear la categoria: ' . $this->db->error()['message']);
            return array('status' => false, 'message' => 'Error al crear la categoria');
        }
    }
    public function guardarCategoriaProductos($categoriaId, $productIds)
    {
        $productsIds = json_decode($productIds);
        foreach ($productsIds as $productId) {
            $this->db->set('category_id', $categoriaId);
            $this->db->set('status', 'EN TIENDA');
            $this->db->where('id', $productId);
            $this->db->update($this->table);
        }
        if ($this->db->error()['code'] == 0) {
            return array('status' => true, 'message' => 'Productos guardados correctamente');
        } else {
            log_message('error', 'Error al guardar los productos: ' . $this->db->error()['message']);
            return array('status' => false, 'message' => 'Error al guardar los productos');
        }
    }
}
