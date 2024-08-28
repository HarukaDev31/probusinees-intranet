<?php
trait WebSocketTrait {
    private $url='http://localhost:8081/send';
    public function sendEvent($message) {
        $data = array(' ' => $message);

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}