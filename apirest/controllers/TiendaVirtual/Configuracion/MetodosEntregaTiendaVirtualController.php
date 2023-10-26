<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MetodosEntregaTiendaVirtualController extends CI_Controller {
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('TiendaVirtual/Configuracion/MetodoEntregaModel');
		$this->load->model('HelperDropshippingModel');
		$this->load->model('ConfiguracionModel');
	}

	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
		  	$arrUrlTiendaVirtual = $this->ConfiguracionModel->obtenerUrlTiendaVirtual();
			$arrPaisesUsuario = $this->ConfiguracionModel->obtenerPaisesUsuario();
			$this->load->view('header', array(
					"arrUrlTiendaVirtual" => $arrUrlTiendaVirtual,
					"arrPaisesUsuario" => $arrPaisesUsuario
				)
			);
			$this->load->view('TiendaVirtual/Configuracion/MetodoEntregaView');
			$this->load->view('footer', array("js_metodo_entrega_tienda_virtual" => true));
		}
	}
	
	public function ajax_list(){
		$arrData = $this->MetodoEntregaModel->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
            $rows[] = $row->No_Metodo_Entrega_Tienda_Virtual;
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            $rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';			
			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verMetodoEntrega(\'' . $row->ID_Metodo_Entrega_Tienda_Virtual . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MetodoEntregaModel->count_all(),
	        'recordsFiltered' => $this->MetodoEntregaModel->count_filtered(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit($ID){
        echo json_encode($this->MetodoEntregaModel->get_by_id($this->security->xss_clean($ID)));
    }
    
	public function crudMedioPago(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'No_Metodo_Entrega_Tienda_Virtual' => $this->input->post('No_Metodo_Entrega_Tienda_Virtual'),
			'Nu_Estado' => $this->input->post('Nu_Estado'),
		);

		$data_recojo_tienda = array(
			'ID_Almacen' => $this->input->post('EID_Almacen'),
			'ID_Pais' => $this->input->post('ID_Pais-recojo_tienda'),
			'ID_Departamento' => $this->input->post('ID_Departamento-recojo_tienda'),
			'ID_Provincia' => $this->input->post('ID_Provincia-recojo_tienda'),
			'ID_Distrito' => $this->input->post('ID_Distrito-recojo_tienda'),
			'Txt_Direccion_Almacen' => $this->input->post('Txt_Direccion_Almacen'),
		);
		echo json_encode($this->MetodoEntregaModel->actualizarMetodoEntrega(array('ID_Metodo_Entrega_Tienda_Virtual' => $this->input->post('EID_Metodo_Entrega_Tienda_Virtual')), $data, $this->input->post('ENo_Metodo_Entrega_Tienda_Virtual'), $this->input->post('ENu_Tipo_Metodo_Entrega_Tienda_Virtual'), $data_recojo_tienda));
	}

	//DISTRITO	
	public function ajax_list_distrito(){
		$arrData = $this->MetodoEntregaModel->get_datatables_distrito();
        $data = array();
        $no = $this->input->post('start');
        $action = 'delete';
        foreach ($arrData as $row) {
            $no++;
            $rows = array();
			
            $rows[] = $row->No_Departamento;
            $rows[] = $row->No_Provincia;
            $rows[] = $row->No_Distrito;
            //$rows[] = $row->Ss_Delivery;
			
			$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado);
			$dropdown_estado_tienda = '<div class="dropdown">
			<button style="width: 100%;" class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">' . ($row->Nu_Estado == 1 ? 'Visible' : 'Oculto') . ' <span class="caret"></span></button>
			<ul class="dropdown-menu" style="width: 100%; position: sticky;">
				<li><a alt="Mostrar item en tienda" title="Mostrar lugar en tienda" href="javascript:void(0)" onclick="cambiarEstadoTienda(\'' . $row->ID_Distrito . '\',1);">Visible</a></li>
				<li><a alt="Ocultar item en tienda" title="Ocultar lugar en tienda" href="javascript:void(0)" onclick="cambiarEstadoTienda(\'' . $row->ID_Distrito . '\',0);">Oculto</a></li>
			</ul>
			</div>';
			$rows[] = $dropdown_estado_tienda;
			//$arrEstadoRegistro = $this->HelperDropshippingModel->obtenerEstadoRegistroArray($row->Nu_Estado);
            //$rows[] = '<span class="label label-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verDistrito(\'' . $row->ID_Distrito . '\')"><i class="fa fa-2x fa-pencil" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'draw' => $this->input->post('draw'),
	        'recordsTotal' => $this->MetodoEntregaModel->count_all_distrito(),
	        'recordsFiltered' => $this->MetodoEntregaModel->count_filtered_distrito(),
	        'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function ajax_edit_distrito($ID){
        echo json_encode($this->MetodoEntregaModel->get_by_id_distrito($this->security->xss_clean($ID)));
    }
    
	public function crudDistrito(){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data = array(
			'Ss_Delivery' => $this->input->post('Ss_Delivery'),
			'Nu_Habilitar_Ecommerce' => $this->input->post('Nu_Habilitar_Ecommerce'),
		);
		echo json_encode($this->MetodoEntregaModel->actualizarDistrito(array('ID_Distrito' => $this->input->post('EID_Distrito')), $data));
	}
	//FIN DISTRITO
	
	public function updPrecioEstandarDelivery(){
        echo json_encode($this->MetodoEntregaModel->updPrecioEstandarDelivery($this->input->post()));
    }

	//CRUD PROMO DELIVERY
	function crudPromoDelivery() {
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
		$data['ID_Estatus_Promo'] = 0;
		if(count($this->input->post()) != 0) {
			$data['ID_Estatus_Promo'] = 1;
			$data['Nu_Monto_Compra'] = $this->input->post('Nu_Monto_Compra');
			$data['Nu_Costo_Envio'] = $this->input->post('Nu_Costo_Envio');
			$data['Txt_Terminos'] = $this->input->post('Txt_Terminos');
		}
		echo json_encode($this->MetodoEntregaModel->updatePromoDelivery($data));
	}
	//FIN CRUD PROMO DELIVERY

	//AJAX GET PROMO DELIVERY
	public function ajax_promoDelivery(){
		echo json_encode($this->MetodoEntregaModel->get_promoDelivery());
    }
	//FIN AJAX GET PROMO DELIVERY
	
	public function cambiarEstadoTienda($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->MetodoEntregaModel->cambiarEstadoTienda($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
}
