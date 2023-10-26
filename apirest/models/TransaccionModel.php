<?php
class TransaccionModel extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}

	public function ProcesarPagoIzipay(){
		
		Lyra\Client::setDefaultUsername("55463244");
		Lyra\Client::setDefaultPassword("prodpassword_TTfD51elD8MjRpmambEyHMA9IYREPql4HANVPFx1GroJF");
		Lyra\Client::setDefaultEndpoint("https://api.micuentaweb.pe");
		/* publicKey and used by the javascript client */
		Lyra\Client::setDefaultPublicKey("55463244:publickey_AAoti3fxO4KnEFKqznl2QavEO6okN5KDOndi8wDHiJYHP");
		/* Javascript content delivery server */
		Lyra\Client::setDefaultClientEndpoint("https://api.micuentaweb.pe");
		/* SHA256 key */
		Lyra\Client::setDefaultSHA256Key("zP68bMP2XWaZ4IeCaI5J8EgHHWh0mEB8eWgswek7mXuw6");

		$client = new Lyra\Client();

		if (empty($_POST)) {
		    throw new Exception("no post data received!");
		}

		/* Use client SDK helper to retrieve POST parameters */
		$data_web = json_encode($_POST);
		$formAnswer = $client->getParsedFormAnswer();
		// echo "<br><br><br>";
		// echo "<pre>";
		// print_r($formAnswer["kr-answer"]);
		$Estatus = $formAnswer["kr-answer"]["orderStatus"];
		$IdOrden = $formAnswer["kr-answer"]["orderDetails"]["orderId"];
		
		// echo "<br> Estatus:".$Estatus;
		// echo "<br> IdOrden:".$IdOrden;

		// echo "<pre>";

		if($Estatus=="PAID"){
			$data  = array( 'Nu_Estado' => "2",'Nu_Estado_Pago' => 1, "Txt_Respuesta_Pago_Web"=>$data_web);
			$where = array( 'ID_Pedido_Cabecera' => $IdOrden);
		}
		else{
			$data  = array( 'Nu_Estado' => "6", "Txt_Respuesta_Pago_Web"=>$data_web);
			$where = array( 'ID_Pedido_Cabecera' => $IdOrden);
		}

		$this->db->update( 'pedido_cabecera' , $data, $where);
		//echo $this->db->last_query() ;


		// $ar = json_decode($formAnswer);
		// $fp = fopen('data.txt', 'w');
		// fwrite($fp, json_encode($formAnswer));
		// fclose($fp);
	
	}

}
