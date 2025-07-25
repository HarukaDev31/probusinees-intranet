    <?php
    trait WhatsappTrait
    {
        private $phoneNumberId = null;

        private function _callApi($endpoint, $data)
        {
            $url = 'https://redis.probusiness.pe/api/whatsapp' . $endpoint;
            //check if on prod or on local using base_url
            $envUrl = base_url();
            //if $envUrl contains 'localhost' 
            if (strpos($envUrl, 'localhost') !== false) {
                $data['phoneNumberId'] = '51931629529@c.us';
            }
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
        public function sendWelcome($carga, $phoneNumberId = null, $sleep = 0): array
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;
            return $this->_callApi('/welcome', [
                'carga' => $carga,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }

        public function sendDataItem($message, $filePath, $phoneNumberId = null, $sleep = 0): array
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;
            $fileContent = base64_encode(file_get_contents($filePath));

            return $this->_callApi('/data-item', [
                'message' => $message,
                'fileContent' => $fileContent,
                'fileName' => basename($filePath),
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }

        public function sendMessage($message, $phoneNumberId = null, $sleep = 0): array
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            return $this->_callApi('/message', [
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
        public function sendMessageVentas($message, $phoneNumberId = null, $sleep = 0): array
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

            return $this->_callApi('/message-ventas', [
                'message' => $message,
                'phoneNumberId' => $phoneNumberId,
                'sleep' => $sleep
            ]);
        }
        public function sendMedia($filePath, $mimeType = null, $message = null, $phoneNumberId = null, $sleep = 0)
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;

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
        public function sendMediaInspection($filePath, $mimeType = null, $message = null, $phoneNumberId = null, $sleep = 0, $inspection_id = null)
        {
            $phoneNumberId = $phoneNumberId ? $phoneNumberId : $this->phoneNumberId;
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
