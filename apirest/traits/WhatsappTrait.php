<?php
trait WhatsappTrait
{
    private $apiUrl = 'https://whatsapp.probusiness.pe/enviar-mensaje';
    // private $token = "EAAWycxktPLABO1mMGWamek2oZAKFcaD1fzmPa3CXjTmjZCQyBXsG6BnyZA3GGmvDAc4kTHHcgcRoZAPZBFeCoA6cFH1Yp6Pd2iMj7Wm5EHAxQqIWsteiZC65C3oAYZBEJzSvhm6jXATWZBVRxEIkAzxfjPwvCMDqTSbCHVSCZAANR5v2CcP62ya6YkTH3kXD4YgMAeFv7L2oiW4FvqQO1g5GuyXpMDvos";
    private $phoneNumberId = "51912705923@c.us";
    public function sendWelcome($carga)
    {
        try {
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'mensaje' => '
Hola 🙋🏻‍♀, te escribe el área de coordinación de probusiness, 
yo me encargaré de ayudarte en tu importación del *consolidado #' . $carga . '*.

📢 Preste atención al siguiente paso: Rotulado 👇🏼
Tienes que indicarle a tu proveedor que las cajas máster 📦 cuenten con un rotulado para identificar tus paquetes y diferenciarlas de los demás cuando llegue a nuestro almacén.

☑ El documento está en idioma chino, solo debes enviarle a tu proveedor 📤

Nota: No cambiar ninguno de los datos, en caso tu proveedor tenga alguna consulta, se puede comunicarse:

🙍🏻‍♂ Álmacen China: Mr. Younus 
📞 Wechat: 13185122926',
                'numero' => $this->phoneNumberId
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
        }
    }
    public function sendDataItem($message, $filePath)
    {
        try {
            $postData = [
                'numero' => $this->phoneNumberId, // Número de WhatsApp
                'mensaje' => $message, // Mensaje opcional
                'archivo' => new CURLFile($filePath, 'application/pdf', basename($filePath))
            ];

            // Configurar cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Make sure to set the content type for multipart form data
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
            // Ejecutar la solicitud
            $response = curl_exec($ch);

            // Verificar errores
            if (curl_errno($ch)) {
                log_message('error', curl_error($ch));
            } else {
                log_message('info', $response);
            }

            // Cerrar cURL
            curl_close($ch);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
            log_message('error', $e->getMessage());
        }
        // Eliminar el archivo temporal después de enviarlo
        // unlink($tempFilePath);
    }
    public function sendMessage($message)
    {
        try {
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'mensaje' => $message,
                'numero' => $this->phoneNumberId
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
        }
    }
    public function sendMedia($filePath, $mimeType)
    {
        try {
            // Check if file is a URL
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                // Download the file to a temporary location
                $tempFile = tempnam(sys_get_temp_dir(), 'whatsapp_media_');
                $fileContents = file_get_contents($filePath);
                if ($fileContents === false) {
                    throw new Exception("Could not download file from: $filePath");
                }
                file_put_contents($tempFile, $fileContents);
                $filePath = $tempFile;
                $fileName = basename(parse_url($filePath, PHP_URL_PATH));

                // Detect MIME type based on file extension if not provided
                if (empty($mimeType)) {
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    switch (strtolower($ext)) {
                        case 'jpg':
                        case 'jpeg':
                            $mimeType = 'image/jpeg';
                            break;
                        case 'png':
                            $mimeType = 'image/png';
                            break;
                        case 'mp4':
                            $mimeType = 'video/mp4';
                            break;
                        case 'pdf':
                            $mimeType = 'application/pdf';
                            break;
                        case 'xlsx':
                            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                            break;
                        default:
                            $mimeType = 'application/octet-stream';
                    }
                }
            } else {
                $fileName = basename($filePath);
                // Use file's actual MIME type if available
                if (empty($mimeType) && function_exists('mime_content_type')) {
                    $mimeType = mime_content_type($filePath);
                }
            }

            // Create a proper multipart/form-data request
            $postData = [
                'numero' => $this->phoneNumberId,
                'mensaje' => '', // Optional message
                'archivo' => new CURLFile($filePath, $mimeType, $fileName)
            ];

            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Increased timeout to 60 seconds

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            // Clean up temporary file if created
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return $response;
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
        }
    }
    // private $phoneNumberDestiny = "+51912705923";
    // public function sendWelcome()
    // {
    //     try {
    //         $ch = curl_init(); // Inicializa cURL

    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "recipient_type" => "individual",
    //             "to" => $this->phoneNumberDestiny,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => "welcome_message",
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],
    //                 // "components" => [
    //                 //     [
    //                 //         "type" => "header",
    //                 //         "parameters" => [
    //                 //             [
    //                 //                 "type" => "document",
    //                 //                 "document" => [
    //                 //                     "id" => $mediaId
    //                 //                 ]
    //                 //             ]
    //                 //         ]
    //                 //     ]
    //                 // ]
    //             ]
    //         ];
    //         curl_setopt_array($ch, [
    //             CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 "Authorization: Bearer " . $this->token,
    //                 "Content-Type: application/json"
    //             ],
    //         ]);

    //         $response = curl_exec($ch);
    //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         $httpError = curl_error($ch);

    //         curl_close($ch);

    //         if ($response === false) {
    //             throw new Exception("cURL error: $httpError");
    //         }

    //         if ($httpStatus !== 200) {
    //             // Maneja el error HTTP
    //             $errorResponse = [
    //                 'http_status' => $httpStatus,
    //                 'curl_error' => $httpError,
    //                 'response_body' => $response // Guarda la respuesta para depuración
    //             ];
    //             return json_encode($errorResponse, JSON_PRETTY_PRINT);
    //         }

    //         // Si todo es correcto, procesa la respuesta
    //         return json_encode($response);
    //     } catch (Exception $e) {
    //         // Captura y retorna excepciones
    //         return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //     }
    // }
    // public function sendInspectionXSupllier($mediaData,$cliente,$codeSupplier,$qtyBoxChina){
    //     try {
    //         $ch = curl_init(); // Inicializa cURL

    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "recipient_type" => "individual",
    //             "to" => $this->phoneNumberDestiny,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => "inspection_x_supplier",
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],
    //                 "components" => [

    //                     [
    //                         "type" => "body",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  $cliente 
    //                             ],
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  $codeSupplier 
    //                             ],
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  $qtyBoxChina 
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ];
    //         curl_setopt_array($ch, [
    //             CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 "Authorization: Bearer " . $this->token,
    //                 "Content-Type: application/json"
    //             ],
    //         ]);

    //         $response = curl_exec($ch);
    //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         $httpError = curl_error($ch);

    //         curl_close($ch);

    //         if ($response === false) {
    //             throw new Exception("cURL error: $httpError");
    //         }

    //         if ($httpStatus !== 200) {
    //             // Maneja el error HTTP
    //             $errorResponse = [
    //                 'http_status' => $httpStatus,
    //                 'curl_error' => $httpError,
    //                 'response_body' => $response // Guarda la respuesta para depuración
    //             ];
    //             return json_encode($errorResponse, JSON_PRETTY_PRINT);
    //         }else{
    //             //for each media id send the media
    //             foreach ($mediaData as $media) {
    //                 try{
    //                     $typesimage=['image/png','image/jpeg','image/jpg'];
    //                 $typesdoc=['application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf'];
    //                 $typesvideo=['video/mp4','video/avi','video/mov','video/flv','video/wmv','video/3gp','video/mkv','video/webm'];
    //                 if(in_array($media->file_type,$typesimage)){
    //                     $response=$this->sendFileProveedor($media->media_id,"image");
    //                 }else if(in_array($media->file_type,$typesdoc)){

    //                     $response=$this->sendFileProveedor($media->media_id,"document");
    //                 }else if(in_array($media->file_type,$typesvideo)){
    //                     $response=$this->sendFileProveedor($media->media_id,"video");
    //                 }
    //                 echo $response;
    //                 }catch(Exception $e){
    //                     return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //                 }
    //             }
    //             return json_encode($response);

    //         }

    //     } catch (Exception $e) {
    //         // Captura y retorna excepciones
    //         return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //     }
    // }
    // public function sendDatosProveedor($mediaId, $producto,$supplier_code)
    // {
    //     try {
    //         $ch = curl_init(); // Inicializa cURL

    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "recipient_type" => "individual",
    //             "to" => $this->phoneNumberDestiny,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => "auto_pay_reminder_2",
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],
    //                 "components" => [
    //                     [
    //                         "type" => "header",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "document",
    //                                 "document" => [
    //                                     "id" => $mediaId
    //                                 ]
    //                             ]
    //                         ]
    //                     ],
    //                     [
    //                         "type" => "body",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  $producto 
    //                             ],
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  $supplier_code 
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ];
    //         curl_setopt_array($ch, [
    //             CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 "Authorization: Bearer " . $this->token,
    //                 "Content-Type: application/json"
    //             ],
    //         ]);

    //         $response = curl_exec($ch);
    //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         $httpError = curl_error($ch);

    //         curl_close($ch);

    //         if ($response === false) {
    //             throw new Exception("cURL error: $httpError");
    //         }

    //         if ($httpStatus !== 200) {
    //             // Maneja el error HTTP
    //             $errorResponse = [
    //                 'http_status' => $httpStatus,
    //                 'curl_error' => $httpError,
    //                 'response_body' => $response // Guarda la respuesta para depuración
    //             ];
    //             return json_encode($errorResponse, JSON_PRETTY_PRINT);
    //         }

    //         // Si todo es correcto, procesa la respuesta
    //         return json_encode($response);
    //     } catch (Exception $e) {
    //         // Captura y retorna excepciones
    //         return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //     }
    // }
    // public function sendDataRotulado()
    // {
    //     try {
    //         $ch = curl_init(); // Inicializa cURL

    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "recipient_type" => "individual",
    //             "to" => $this->phoneNumberDestiny,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => "datos_rotulado",
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],

    //             ]
    //         ];
    //         curl_setopt_array($ch, [
    //             CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 "Authorization: Bearer " . $this->token,
    //                 "Content-Type: application/json"
    //             ],
    //         ]);

    //         $response = curl_exec($ch);
    //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         $httpError = curl_error($ch);

    //         curl_close($ch);

    //         if ($response === false) {
    //             throw new Exception("cURL error: $httpError");
    //         }

    //         if ($httpStatus !== 200) {
    //             // Maneja el error HTTP
    //             $errorResponse = [
    //                 'http_status' => $httpStatus,
    //                 'curl_error' => $httpError,
    //                 'response_body' => $response // Guarda la respuesta para depuración
    //             ];
    //             return json_encode($errorResponse, JSON_PRETTY_PRINT);
    //         }

    //         // Si todo es correcto, procesa la respuesta
    //         return json_encode($response);
    //     } catch (Exception $e) {
    //         // Captura y retorna excepciones
    //         return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //     }
    // }
    // public function uploadDocument($filePath, $mimeType)
    // {
    //     $url = 'https://graph.facebook.com/v21.0/' . $this->phoneNumberId . '/media';
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->token,
    //         'Content-Type: multipart/form-data'
    //     ));
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, array(
    //         'messaging_product' => 'whatsapp',
    //         'file' => new CURLFile($filePath, $mimeType)
    //     ));
    //     $response = curl_exec($ch);
    //     $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $httpError = curl_error($ch);
    //     curl_close($ch);

    //     // Mostrar la respuesta completa para depuración
    //     if ($httpStatus !== 200) {
    //         throw new Exception('Error uploading document.' . $httpError . ' ' . $response);
    //     }
    //     $data = json_decode($response, true);
    //     return $data['id'];
    // }
    // public function sendFileProveedor($mediaId,$type)
    // {

    //     try {
    //         $ch = curl_init(); // Inicializa cURL
    //         $template="";
    //         if($type=="image"){
    //             $template="send_media_img";
    //         }else if($type=="document"){
    //             $template="send_media_doc";
    //         }else if($type=="video"){
    //             $template="send_media_vid";
    //         }
    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "recipient_type" => "individual",
    //             "to" => $this->phoneNumberDestiny,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => $template,
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],
    //                 "components" => [
    //                     [
    //                         "type" => "header",
    //                         "parameters" => [
    //                             [
    //                                 "type" => $type,
    //                                 $type => [
    //                                     "id" => $mediaId
    //                                 ]
    //                             ]
    //                         ]
    //                     ],

    //                 ]
    //             ]
    //         ];
    //         echo json_encode($data);
    //         curl_setopt_array($ch, [
    //             CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 "Authorization: Bearer " . $this->token,
    //                 "Content-Type: application/json"
    //             ],
    //         ]);
    //         $response = curl_exec($ch);
    //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         $httpError = curl_error($ch);
    //         curl_close($ch);

    //         if ($response === false) {
    //             throw new Exception("cURL error: $httpError");
    //         }

    //         if ($httpStatus !== 200) {
    //             // Maneja el error HTTP
    //             $errorResponse = [
    //                 'http_status' => $httpStatus,
    //                 'curl_error' => $httpError,
    //                 'response_body' => $response // Guarda la respuesta para depuración
    //             ];
    //             return json_encode($errorResponse, JSON_PRETTY_PRINT);
    //         }

    //         // Si todo es correcto, procesa la respuesta
    //         return json_encode($response);
    //     } catch (Exception $e) {
    //         // Captura y retorna excepciones
    //         return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    //     }
    // }
}
