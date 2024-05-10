<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * CI_Loader
	 *
	 * @var	CI_Loader
	 */
	public $load;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
		
		//$this->_ValidarPais();
		// Usuario
		$this->user = $this->session->userdata('usuario');
		
		if ($this->user != false) {
			// Configuracion Empresa
			$this->empresa = $this->ConfiguracionModel->obtenerEmpresa();
			
			// Configuracion Almacenes
			$arrParams = array(
				'iIdEmpresa' => $this->empresa->ID_Empresa,
				'iIdOrganizacion' => $this->empresa->ID_Organizacion,
			);
			$this->almacen = $this->ConfiguracionModel->obtenerAlmacenes($arrParams);
			
			// Datos de notificaciones y FE
			//$this->notificaciones = $this->ConfiguracionModel->inicio();
			//$this->notificaciones = array();
			
			$this->notificaciones = $this->NotificacionModel->obtenerNotificacionUsuario($this->user->ID_Usuario);

			//if($this->router->class == 'AccesoController' && $this->router->method != 'logout') redirect('InicioController');

			if(!$this->input->is_ajax_request()){
				// Cargamos el menu
				$this->menu = $this->MenuModel->listarMenu();
			}
		} else {
			if(!$this->input->is_ajax_request()){
				if(
					($this->router->class != 'LoginController')
					AND ($this->router->class != 'CronController')
					AND ($this->router->method != 'registroComprasBG')
					AND ($this->router->method != 'registroVentasIngresosBG')
					AND ($this->router->method != 'getOrdenVentaPDF')
					AND ($this->router->method != 'generarRepresentacionInternaPDF')
					AND ($this->router->method != 'generarPreCuentaPDF')
					AND ($this->router->method != 'ReporteUtilidadBG')
					AND ($this->router->method != 'ReporteVentasDetalladasBG')
					AND ($this->router->method != 'IzipayTransaccion')
					AND ($this->router->method != 'ReporteKardexBG')
					AND ($this->router->method != 'ReporteKardexValorizadoBG')
					AND ($this->router->method != 'ReporteVentaXClienteBG')
				) {
					redirect('');
				}
				
			} else {
				if($this->router->class != 'LoginController') exit(json_encode(array('response' => 'login')));
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

	public function _ValidarPais(){

		$config 		=& get_config();
		//$ip_remote      = "94.23.202.99"; //$this->input->ip_address();
		$ip_remote      = $this->input->ip_address();
		$ruta 		    = $this->router->class."/".$this->router->method;
		$gi  			= geoip_open("/usr/share/GeoIP/GeoIP.dat",GEOIP_STANDARD);
		$CountryCode 	= geoip_country_code_by_addr($gi, $ip_remote);
		$matchFound 	= false;
		$uri_string     = $this->uri->uri_string();
		geoip_close($gi);

		if(ENVIRONMENT=="development" OR is_cli())
			return false;

		foreach ($config["geoip_url_excepcion_regex"] as $pattern) {
			if (preg_match("/".$pattern."/i", $uri_string)) 
				$matchFound = true;
		}

		if(count($config["geoip_paises_permitidos"])>0){
			if(!(in_array($CountryCode, $config["geoip_paises_permitidos"]) OR $matchFound))	
				show_404();
		}

	}

}
