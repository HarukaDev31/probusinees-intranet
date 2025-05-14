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
    public function getCatalogo()
    {
        try {
            $this->db->select('id,cod_producto,nombre,precio,moq,main_image_url,status');
            $this->db->from($this->table);
            $this->db->where('status', 'PENDIENTE');
            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->result());
            } else {
                log_message('error', 'Error al obtener el catálogo: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el catálogo');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getCatalogoCompletados()
    {
        try {
            $this->db->select('id,cod_producto,nombre,precio,moq,main_image_url,precio_peru,precio_usd,status');
            $this->db->from($this->table);
            $this->db->where('status', 'COTIZADO');

            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->result());
            } else {
                log_message('error', 'Error al obtener el catálogo: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al obtener el catálogo');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    public function getCatalogoSeleccionados(){
        try {
            $this->db->select('catalogo_producto.id,cod_producto,nombre,precio,moq,main_image_url,status,
            catalogo_producto_category.name as category_name,
            ');
            $this->db->from($this->table);
            $this->db->join('catalogo_producto_category', 'catalogo_producto_category.id = catalogo_producto.category_id', 'left');
            $this->db->where('status', 'EN TIENDA');
            $query = $this->db->get();
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'data' => $query->result());
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
    public function pasarTienda($productId)
    {
        try {
            ///update status to EN TIENDA
            $this->db->set('status', 'EN TIENDA');
            $this->db->where('id', $productId);
            $this->db->update($this->table);
            if ($this->db->error()['code'] == 0) {
                return array('status' => true, 'message' => 'Producto enviado a tienda correctamente');
            } else {
                log_message('error', 'Error al enviar el producto a tienda: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al enviar el producto a tienda');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
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
