<?php
trait WhatsappTrait{
    private $token="EAAWycxktPLABO2ksqmUaZAVfcq6kryAgXGZCWnGjW4FBuSP4qFm50Qx8GBJHpt7jduA37nAvg1FPKXwCbunZBMyPXsRi0P2XL7VkYBWmJprX6xPGVuzROIYKWASRwZABxq0ihSu5IzWfrZAVZCL5cWoh2kc9v712WmEJWCpbQ4Kc7BZBXulZCVBNoO3u6NayQbXTJBwnY285UXMVrGF5mYpAn1Nt5eMZC";
    private $phoneNumberId="530883513442650";
    public function sendRotulado($mediaId, $cotizadoNumber)
{
    try {
        $ch = curl_init(); // Inicializa cURL
        
        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => "+51912705923",
            "type" => "template",
            "template" => [
                "name" => "send_rot",
                "language" => [
                    "code" => "en_US"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "document",
                                "document" => [
                                    "id" => $mediaId
                                ]
                            ]
                        ]
                    ]
                ]
            ]
            ];
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://graph.facebook.com/v21.0/530883513442650/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->token,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new Exception("cURL error: $httpError");
        }

        if ($httpStatus !== 200) {
            // Maneja el error HTTP
            $errorResponse = [
                'http_status' => $httpStatus,
                'curl_error' => $httpError,
                'response_body' => $response // Guarda la respuesta para depuración
            ];
            return json_encode($errorResponse, JSON_PRETTY_PRINT);
        }

        // Si todo es correcto, procesa la respuesta
        return json_encode($response);
    } catch (Exception $e) {
        // Captura y retorna excepciones
        return json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    }
}

    public function uploadDocument($filePath, $mimeType) {
        $url = 'https://graph.facebook.com/v21.0/' . $this->phoneNumberId . '/media';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->token,
            'Content-Type: multipart/form-data'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'messaging_product' => 'whatsapp',
            'file' => new CURLFile($filePath, $mimeType)
        ));
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpError = curl_error($ch);
        curl_close($ch);
    
        // Mostrar la respuesta completa para depuración
        if ($httpStatus !== 200) {
            throw new Exception('Error uploading document.'.$httpError.' '.$response);
        }
        $data = json_decode($response, true);
        return $data['id'];
        }
}