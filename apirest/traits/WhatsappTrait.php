    <?php
    trait WhatsappTrait
    {
    //     private $apiUrl = 'https://whatsapp.probusiness.pe/enviar-mensaje';
    //     // private $token = "EAAWycxktPLABO1mMGWamek2oZAKFcaD1fzmPa3CXjTmjZCQyBXsG6BnyZA3GGmvDAc4kTHHcgcRoZAPZBFeCoA6cFH1Yp6Pd2iMj7Wm5EHAxQqIWsteiZC65C3oAYZBEJzSvhm6jXATWZBVRxEIkAzxfjPwvCMDqTSbCHVSCZAANR5v2CcP62ya6YkTH3kXD4YgMAeFv7L2oiW4FvqQO1g5GuyXpMDvos";
    private $phoneNumberId = null;
    //     public function sendWelcome($carga)
    //     {
    //         try {
    //             $ch = curl_init($this->apiUrl);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    //             'mensaje' => '
    // Hola 🙋🏻‍♀, te escribe el área de coordinación de probusiness, 
    // yo me encargaré de ayudarte en tu importación del *consolidado #' . $carga . '*.

    // 📢 Preste atención al siguiente paso: 
    // *Rotulado* 👇🏼
    // Tienes que indicarle a tu proveedor que las cajas máster 📦 cuenten con un rotulado para identificar tus paquetes y diferenciarlas de los demás cuando llegue a nuestro almacén.

    // ☑ El documento está en idioma chino, solo debes enviarle a tu proveedor 📤

    // Nota: No cambiar ninguno de los datos, en caso tu proveedor tenga alguna consulta, se puede comunicarse:

    // 🙍🏻‍♂ Álmacen China: Mr. Younus 
    // 📞 Wechat: 13185122926',
    //                 'numero' => $this->phoneNumberId
    //             ]));
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    //             $response = curl_exec($ch);
    //             curl_close($ch);
    //             return $response;
    //         } catch (Exception $e) {
    //             return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //         }
    //     }
    //     public function sendDataItem($message, $filePath)
    //     {
    //         try {
    //             $postData = [
    //                 'numero' => $this->phoneNumberId, // Número de WhatsApp
    //                 'mensaje' => $message, // Mensaje opcional
    //                 'archivo' => new CURLFile($filePath, 'application/pdf', basename($filePath))
    //             ];

    //             // Configurar cURL
    //             $ch = curl_init();
    //             curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
    //             curl_setopt($ch, CURLOPT_POST, 1);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //             // Make sure to set the content type for multipart form data
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
    //             // Ejecutar la solicitud
    //             $response = curl_exec($ch);

    //             // Verificar errores
    //             if (curl_errno($ch)) {
    //                 log_message('error', curl_error($ch));
    //             } else {
    //                 log_message('info', $response);
    //             }

    //             // Cerrar cURL
    //             curl_close($ch);
    //         } catch (Exception $e) {
    //             return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //             log_message('error', $e->getMessage());
    //         }
    //         // Eliminar el archivo temporal después de enviarlo
    //         // unlink($tempFilePath);
    //     }
    //     public function sendMessage($message)
    //     {
    //         try {
    //             $ch = curl_init($this->apiUrl);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    //                 'mensaje' => $message,
    //                 'numero' => $this->phoneNumberId
    //             ]));
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    //             $response = curl_exec($ch);
    //             curl_close($ch);
    //             return $response;
    //         } catch (Exception $e) {
    //             return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //         }
    //     }
    //     public function sendMedia($filePath, $mimeType,$message=null)
    //     {
    //         try {
    //             // Check if file is a URL
    //             if (filter_var($filePath, FILTER_VALIDATE_URL)) {
    //                 // Download the file to a temporary location
    //                 $tempFile = tempnam(sys_get_temp_dir(), 'whatsapp_media_');
    //                 $fileContents = file_get_contents($filePath);
    //                 if ($fileContents === false) {
    //                     throw new Exception("Could not download file from: $filePath");
    //                 }
    //                 file_put_contents($tempFile, $fileContents);
    //                 $filePath = $tempFile;
    //                 $fileName = basename(parse_url($filePath, PHP_URL_PATH));

    //                 // Detect MIME type based on file extension if not provided
    //                 if (empty($mimeType)) {
    //                     $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    //                     switch (strtolower($ext)) {
    //                         case 'jpg':
    //                         case 'jpeg':
    //                             $mimeType = 'image/jpeg';
    //                             break;
    //                         case 'png':
    //                             $mimeType = 'image/png';
    //                             break;
    //                         case 'mp4':
    //                             $mimeType = 'video/mp4';
    //                             break;
    //                         case 'pdf':
    //                             $mimeType = 'application/pdf';
    //                             break;
    //                         case 'xlsx':
    //                             $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    //                             break;
    //                         default:
    //                             $mimeType = 'application/octet-stream';
    //                     }
    //                 }
    //             } else {
    //                 $fileName = basename($filePath);
    //                 // Use file's actual MIME type if available
    //                 if (empty($mimeType) && function_exists('mime_content_type')) {
    //                     $mimeType = mime_content_type($filePath);
    //                 }
    //             }

    //             // Create a proper multipart/form-data request
    //             $postData = [
    //                 'numero' => $this->phoneNumberId,
    //                 'mensaje' => $message,
    //                 'archivo' => new CURLFile($filePath, $mimeType, $fileName)
    //             ];

    //             $ch = curl_init($this->apiUrl);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    //             curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Increased timeout to 60 seconds

    //             $response = curl_exec($ch);

    //             if (curl_errno($ch)) {
    //                 throw new Exception(curl_error($ch));
    //             }

    //             curl_close($ch);

    //             // Clean up temporary file if created
    //             if (isset($tempFile) && file_exists($tempFile)) {
    //                 unlink($tempFile);
    //             }

    //             return $response;
    //         } catch (Exception $e) {
    //             return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //         }
    //     }
        private function _callApi($endpoint, $data) {
            $url = 'https://redis.probusiness.pe/api/whatsapp'. $endpoint;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            return [
                'status' => $httpCode >= 200 && $httpCode < 300,
                'response' => json_decode($response, true)
            ];
        }
        public function sendWelcome($carga, $phoneNumberId = null,$sleep=0): array {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;
            return $this->_callApi('/welcome', [
                'carga' => $carga,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
    
        public function sendDataItem($message, $filePath, $phoneNumberId = null, $sleep= 0): array {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;
            $fileContent = base64_encode(file_get_contents($filePath));
            
            return $this->_callApi('/data-item', [
                'message' => $message,
                'fileContent' => $fileContent,
                'fileName' => basename($filePath),
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
    
        public function sendMessage($message, $phoneNumberId = null,$sleep=0): array {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            return $this->_callApi('/message', [
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
        public function sendMessageVentas($message, $phoneNumberId = null,$sleep=0): array {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            return $this->_callApi('/message-ventas', [
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
        public function sendMedia($filePath, $mimeType = null, $message = null, $phoneNumberId = null,$sleep=0) {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            $fileContent = base64_encode(file_get_contents($filePath));
            
            return $this->_callApi('/media', [
                'fileContent' => $fileContent,
                'fileName' => basename($filePath),
                'mimeType' => $mimeType,
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
        public function sendMediaInspection($filePath, $mimeType = null, $message = null, $phoneNumberId = null,$sleep=0,$inspection_id=null) {
            $phoneNumberId= $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            $fileContent = base64_encode(file_get_contents($filePath));
            
            return $this->_callApi('/media-inspection', [
                'fileContent' => $fileContent,
                'fileName' => basename($filePath),
                'mimeType' => $mimeType,
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep,
                'inspectionId' => $inspection_id
            ]);
        }
    }