<?php
trait WhatsappTrait{
    public function sendRotulado($fileUrl,$cotizadoNumber){
        try{
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => "+51912705923",
                "type" => "template",
                "template" => [
                    "name" => "send_rotulado",
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
                                        "link" => $fileUrl,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            curl_setopt_array($curl, [
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
                    "Authorization: Bearer EAAWycxktPLABO2s3NppZCnRJz9S3lK7h1lhVpkKcKHbANLh8jwHj82oWl6XUc9Mn9d45pTZC0VBdG3odq7pbuRaZBD2CCSQww8srvE4TmdgobkdH1ZBOWs7LJaZBM3vqZCDeoYtKG5hi8FO5lz9gN005RIfyQSIUL81e0aWkzgpomtDBNOMgAexyGYV0QYjXXyKf6rxP1ladJyIppJ21tKZA2tuaSoZD",
                    "Content-Type: application/json"
                ],
                //add bearer token
              
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);
            
            if ($err) {
                return "cURL Error #:" . json_encode($err);
            } else {
                return json_encode($response);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}