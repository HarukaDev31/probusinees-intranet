<?php
trait WhatsappTrait{
    private $token="EAAWycxktPLABO7Bo0bxeznJ4PFZAp6aPLo7W9j0rxRFndvG5PgqRFouSZBMPQL99mcRe3zPeotmhuZBfcVPrD9y5oUCZCeuapcESA2ZCZALsHHhN4krT3GrIHzqtaRoyAqoMtf7NIXXMLEkfvjeqb9a7IoTZCi7tuAl552v5aqiWrwYtAgGStpbfJflAHfTdHZAOmDUSPhKcD0Sj6hXZByRj2YqUwYrEZD";
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
            throw new Exception('Error uploading document.');
        }
        $data = json_decode($response, true);
        return $data['id'];
        }
}