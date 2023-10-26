<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HelperController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('HelperModel');
		$this->load->model('MenuModel');

		if(!isset($this->session->userdata['usuario'])) {
			$sapi_type = php_sapi_name();
			if (substr($sapi_type, 0, 3) == 'cgi')
				header("Status: 404 Not Found");
			else
				header("HTTP/1.1 404 Not Found");
			exit();
		}
	}
	
	public function getTiposDocumentosModificar(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposDocumentosModificar($this->input->post('Nu_Tipo_Filtro')));
	}
	
	public function getSeriesDocumentoModificar(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumentoModificar($this->input->post()));
	}
	
	public function getSeriesDocumentoModificarxAlmacen(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumentoModificarxAlmacen($this->input->post()));
	}
	
	public function getMotivosReferenciaModificar(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMotivosReferenciaModificar($this->input->post('ID_Tipo_Documento')));
	}
	
	public function documentExistVerify(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->documentExistVerify($this->input->post('ID_Documento_Guardado'), $this->input->post('ID_Tipo_Documento_Modificar'), $this->input->post('ID_Serie_Documento_Modificar'), $this->input->post('ID_Numero_Documento_Modificar'), $this->input->post()));
	}
	
	public function getEmpresasTodo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEmpresasTodo());
	}
	
	public function getEmpresas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEmpresas());
	}
	
	public function getEmpresasLogin(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEmpresasLogin($this->input->post()));
	}
	
	public function getOrganizaciones(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getOrganizaciones($this->input->post()));
	}
	
	public function getAlmacenes(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getAlmacenes($this->input->post()));
	}
	
	public function getGrupos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getGrupos($this->input->post()));
	}
	
	public function getAlmacenesEmpresa(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getAlmacenesEmpresa());
	}
	
	public function getMonedas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMonedas());
	}
	
	public function getPaises(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getPaises());
	}
	
	public function getDepartamentos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDepartamentos($this->input->post('ID_Pais')));
	}
	
	public function getProvincias(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getProvincias($this->input->post('ID_Departamento')));
	}
	
	public function getDistritos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDistritos($this->input->post('ID_Provincia')));
	}
	
	public function getTiposSexo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        print(json_encode($this->HelperModel->getTiposSexo()));
	}
	
	public function getTiposProducto(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposProducto());
	}
	
	public function getTiposExistenciaProducto(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposExistenciaProducto());
	}
	
	public function getRubros(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getRubros());
	}
	
	public function getTiposDocumentoIdentidad(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposDocumentoIdentidad());
	}
	
	public function getTiposCliente(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposCliente());
	}
	
	public function getTiposFormaPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposFormaPago());
	}
	
	public function getImpuestos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        print(json_encode($this->HelperModel->getImpuestos($this->input->post())));
	}
	
	public function getLineas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        print(json_encode($this->HelperModel->getLineas()));
	}
	
	public function getMarcas(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMarcas());
	}
	
	public function getUnidadesMedida(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getUnidadesMedida());
	}
	
	public function getTipoMovimiento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTipoMovimiento($this->input->post('Nu_Tipo_Movimiento')));
	}
	
	public function getTiposDocumentos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposDocumentos($this->input->post('Nu_Tipo_Filtro')));
	}
	
	public function getSeriesDocumento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumento($this->input->post()));
	}
	public function getSeriesDocumentoxAlmacen(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumentoxAlmacen($this->input->post()));
	}
	
	public function getSeriesDocumentoPuntoVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumentoPuntoVenta($this->input->post()));
	}
	
	public function getSeriesDocumentoOficinaPuntoVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesDocumentoOficinaPuntoVenta($this->input->post()));
	}
	
	public function getPuntoVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getPuntoVenta($this->input->post()));
	}
	
	public function getNumeroDocumento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getNumeroDocumento($this->input->post()));
	}
	
	public function getNumeroDocumentoxAlmacen(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getNumeroDocumentoxAlmacen($this->input->post()));
	}
	
	public function getMediosPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMediosPago($this->input->post()));
	}
	
	public function getDescargarInventario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDescargarInventario());
	}
	
	public function getUbicacionesInventario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getUbicacionesInventario());
	}
	
	public function getTiposDocumentosOrden(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposDocumentosOrden());
	}
	
	public function getTiposOrdenSeguimiento(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposOrdenSeguimiento());
	}
	
	public function getTiposTarjetaCredito(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTiposTarjetaCredito($this->input->post('ID_Medio_Pago')));
	}
	
	public function getTipoTiempoRepetir(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTipoTiempoRepetir($this->input->post()));
	}
	
	public function getToken(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getToken());
	}
	
	public function getCodigoUnidadMedida(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getCodigoUnidadMedida());
	}
	
	public function getListaPrecio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getListaPrecio($this->input->post('Nu_Tipo_Lista_Precio'), $this->input->post('ID_Organizacion'), $this->input->post('ID_Almacen')));
	}
	
	public function getMotivosTraslado(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMotivosTraslado());
	}
	
	public function getTipoOperacionCaja(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTipoOperacionCaja($this->input->post('Nu_Tipo')));
	}
	
	public function getValidarStock(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getValidarStock());
	}
	
	public function getItems(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getItems($this->input->post('ID_Almacen'), $this->input->post('ID_Lista_Precio_Cabecera'), $this->input->post('ID_Linea')));
	}
	
	public function getUltimoCierre(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getUltimoCierre());
	}
	
	public function getDataGeneral(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDataGeneral($this->input->post()));
	}
	
	public function getPosConfiguracionxSerie(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getPosConfiguracionxSerie($this->input->post()));
	}

	public function getPos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getPos());
	}
	
	public function getPersonal(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getPersonal($this->input->post()));
	}
	
	public function validacionAlertaItem(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');

		if ( $this->input->post('sTipoAlerta') == 'Stock_Minimo' ) {
			echo json_encode($this->HelperModel->validacionStockMinimo($this->input->post()));
			exit();
		}
		
		if ( $this->input->post('sTipoAlerta') == 'Venta_Receta_Medica' ) {
			echo json_encode($this->HelperModel->validacionVentaRecetaMedica($this->input->post()));
			exit();
		}
		
		if ( $this->input->post('sTipoAlerta') == 'Lote_Vencimiento' ) {
			echo json_encode($this->HelperModel->validacionLoteVencimiento($this->input->post()));
			exit();
		}
	}
	
	public function validateStockNow(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->validateStockNow($this->input->post()));
	}
	
	public function getStockXEnlaceItem(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getStockXEnlaceItem($this->input->post()));
	}
	
	public function getValoresTablaDato(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getValoresTablaDato($this->input->post()));
	}
	
	public function connectToMysqlLocalhost(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->connectToMysqlLocalhost($this->input->post()));
	}
	
	public function validationKeySerieHDD(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->validationKeySerieHDD($this->input->post()));
	}
	
	public function getDocumentoDetalle(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDocumentoDetalle($this->input->post()));
	}
	
	public function getDocumentoDetalleEstadoLavado(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDocumentoDetalleEstadoLavado($this->input->post()));
	}
	
	public function getDocumentoDetalleEstadoLavadoxDocumentoDetalle(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getDocumentoDetalleEstadoLavadoxDocumentoDetalle($this->input->post()));
	}

	public function cobranzaClientePuntoVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HelperModel->cobranzaClientePuntoVenta($this->input->post()));
	}

	public function cobranzaProveedorPuntoVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HelperModel->cobranzaProveedorPuntoVenta($this->input->post()));
	}

	public function getCanalesVenta(){
        if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		echo json_encode($this->HelperModel->getCanalesVenta());
	}
	
	// Padre: Usuarios
	// Opcion: Opciones del menú
	public function getEmpresasOpcionesMenu(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEmpresasOpcionesMenu());
	}
	
	// Padre: Ventas
	// Opcion: Series
	public function getSeriesEmpresaOrgAlmacenDocumentoOficinaPuntoVenta(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSeriesEmpresaOrgAlmacenDocumentoOficinaPuntoVenta($this->input->post()));
	}
	
	// Padre: Configuración
	// Opcion: Empresa
	public function getEmpresasMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEmpresasMarketplace($this->input->post()));
	}

	// Padre: Logística
	// opcion: Entra o Salida de Inventario
	public function getOrganizacionesAlcenesEmpresaExternos(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getOrganizacionesAlcenesEmpresaExternos($this->input->post()));
	}

	// Padre: Todos
	// opcion: Punto de venta pero se puede ingresar por todo
	public function getEntidad(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getEntidad($this->input->post()));
	}

	//Padre: Punto de venta
	//opcion POS
	public function getTipoRecepcionNegocio(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getTipoRecepcionNegocio());
	}

	//Todo - Panel
	public function reloadUpdateUsuario(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->reloadUpdateUsuario($this->input->post()));
	}

	//Todo - Panel
	public function verificarAccesoMenuXGrupo(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->MenuModel->verificarAccesoMenuXGrupo($this->input->post()));
	}

	// Funciones para ECOMMERCE MARKETPLACE
	public function getCategoriasMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getCategoriasMarketplace());
	}

	public function getSubCategoriasMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getSubCategoriasMarketplace($this->input->post()));
	}

	public function getMarcasMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMarcasMarketplace($this->input->post()));
	}
	
	public function getMediosPagoMarketplace(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getMediosPagoMarketplace($this->input->post()));
	}
	// End Funciones para ECOMMERCE MARKETPLACE
	
	public function getAlmacenesSession(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getAlmacenesSession($this->input->post()));
	}
	
	public function getOrganizacionxUsuarioSessionEmpresa(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
		if(isset($this->session->userdata['usuario'])) {
        	echo json_encode($this->HelperModel->getOrganizacionxUsuarioSessionEmpresa());
		} else {
			echo json_encode(array('status'=>'error', 'message' => 'No existe sesión, cerrar y volver ingresar'));
		}
	}
	
	public function getDatosDocumentoVentaWhatsApp(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getDatosDocumentoVentaWhatsApp($this->input->post()));
	}
	
	public function getDatosCotizacionVentaWhatsApp(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getDatosCotizacionVentaWhatsApp($this->input->post()));
	}
	
	public function getPersonalVentas(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getPersonalVentas($this->input->post()));
	}
	
	public function getDeliveryVentas(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getDeliveryVentas($this->input->post()));
	}
	
	public function getMarcasV2(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getMarcasV2($this->input->post()));
	}
	
	public function getTipoVarianteTablaDato(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getTipoVarianteTablaDato($this->input->post()));
	}
	
	public function getVariante(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getVariante($this->input->post()));
	}
	
	public function getVariantexIDTablaDato(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getVariantexIDTablaDato($this->input->post()));
	}
	
	public function getVarianteDetalle(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getVarianteDetalle($this->input->post()));
	}
	
	public function getSunatTipoOperacion(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getSunatTipoOperacion($this->input->post()));
	}
	
	public function getSedexEmpresa(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getSedexEmpresa($this->input->post()));
	}
	
	public function getSalonxEmpresa(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getSalonxEmpresa($this->input->post()));
	}
	
	public function getHorarioClase(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getHorarioClase($this->input->post()));
	}
	
	public function getDiasSemana(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getDiasSemana($this->input->post()));
	}
	
	public function getAlumnoxEntidad(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getAlumnoxEntidad($this->input->post()));
	}
	
	public function getMatriculaAlumno(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getMatriculaAlumno($this->input->post()));
	}
	
	public function getReporteAlumnosMatriculadosxParams(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getReporteAlumnosMatriculadosxParams($this->input->post()));
	}
	
	public function getDatosPuntoVenta(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getDatosPuntoVenta($this->input->post()));
	}
	
	public function getListaPrecioxCliente(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getListaPrecioxCliente($this->input->post()));
	}
	
	public function getItem(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getItem($this->input->post()));
	}
	
	public function getStockXItem(){
		if (!$this->input->is_ajax_request()) echo json_encode(array('status'=>'error', 'message' => 'No se puede eliminar y acceder'));
        echo json_encode($this->HelperModel->getStockXItem($this->input->post()));
	}
	
	public function getListaPrecioxId(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->getListaPrecioxId($this->input->post('ID_Almacen')));
	}
	
	public function obtenerPreciosxMayor(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
        echo json_encode($this->HelperModel->obtenerPreciosxMayor($this->input->post()));
	}
}
