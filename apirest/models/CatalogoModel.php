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
            $catalogProductUrl = $this->catalogoUrl . '/' . $data['nombre'] . '_' . $randomUuid . '/';
            if ($data['productId']!= null  ) {
          
                //try to unlink files
                $this->db->select('contact_card_url,main_image_url,aditional_image1_url,aditional_image2_url,aditional_video1_url');
                $this->db->from($this->table);
                $this->db->where('id', $data['productId']);
                $query = $this->db->get();
                if ($this->db->error()['code'] == 0) {
                    $dataToDelete = $query->row();
                    $pattern = '/probusinees-intranet\/(.+)/';
                    if (preg_match($pattern, $dataToDelete->contact_card_url, $matches)) {
                        $contactCardUrlDelete = $matches[1];
                        unlink($contactCardUrlDelete);
                    }
                    if (preg_match($pattern, $dataToDelete->main_image_url, $matches)) {
                        $mainImageUrlDelete = $matches[1];
                        log_message('error',$mainImageUrlDelete);
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
                
                $catalogProductUrl = $this->catalogoUrl . '/' . $data['nombre'] . '_' . $randomUuid . '/';
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
            $this->db->select('id,nombre,precio,moq,main_image_url');
            $this->db->from($this->table);
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
                return array('status' => true, 'message' => 'Producto eliminado correctamente');
            } else {
                log_message('error', 'Error al eliminar el producto: ' . $this->db->error()['message']);
                return array('status' => false, 'message' => 'Error al eliminar el producto');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}
