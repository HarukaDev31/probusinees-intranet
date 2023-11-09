<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PedidosGrupal extends CI_Controller {
	
	private $upload_path = '../assets/images/clientes/';
	
	function __construct(){
    	parent::__construct();	
		$this->load->library('session');
		$this->load->database('LAE_SYSTEMS');
		$this->load->model('ImportacionGrupal/PedidosGrupalModel');
		$this->load->model('HelperImportacionModel');
	}
	
	public function listar(){
		if(!$this->MenuModel->verificarAccesoMenu()) redirect('Inicio/InicioView');
		if(isset($this->session->userdata['usuario'])) {
			$this->load->view('header_v2');
			$this->load->view('ImportacionGrupal/PedidosGrupalView');
			$this->load->view('footer_v2', array("js_pedidos_grupal" => true));
		}
	}

	public function ajax_list(){
		$arrData = $this->PedidosGrupalModel->get_datatables();
        $data = array();
        foreach ($arrData as $row) {
			$rows = array();

            $rows[] = $row->No_Importacion_Grupal;
            $rows[] = $row->ID_Pedido_Cabecera;
            $rows[] = allTypeDate($row->Fe_Registro, '-', 0);
            $rows[] = $row->No_Entidad;
            /*
			$rows[] = $row->No_Moneda;
            $rows[] = $row->No_Medio_Pago_Tienda_Virtual;
			*/
            
			
			$image='';
			$voucher='';
			if(!empty($row->Txt_Url_Imagen_Deposito)){
				//$image = '<img class="img-fluid" src="' . $row->Txt_Url_Imagen_Deposito . '" style="cursor:pointer; max-height:40px;" />';
				$voucher = '<a class="btn btn-link" href="' . $row->Txt_Url_Imagen_Deposito . '"  target="_blank" rel="noopener noreferrer"><i class="fas fa-file-alt fa-2x"></i></a>';
			} else {
				//https://impogrupal.probusiness.pe/Payment/thank/8
				$url_voucher = 'https://impogrupal.probusiness.pe/Payment/thank/';
				$url_voucher = $url_voucher . $row->ID_Pedido_Cabecera;
				//$image = '<a class="btn btn-link" href="' . $url_voucher . '" target="_blank" rel="noopener noreferrer" role="button"><i class="fas fa-link" aria-hidden="true"></i> link</a>';
				
				$sCodigoPaisCelular='51';
				$sMensajeWhatsAppVoucher = "Hola " . $row->No_Entidad . ", espero se encuentre bien. 👋🏻\n\n";
				$sMensajeWhatsAppVoucher .= "Le comento que no hemos recibio su depósito 😢\n\n";
				$sMensajeWhatsAppVoucher .= "Le envío link donde adjuntará su voucher.\n";
				$sMensajeWhatsAppVoucher .= $url_voucher;
				$sMensajeWhatsAppVoucher = urlencode($sMensajeWhatsAppVoucher);
				$sMensajeWhatsAppVoucher = '<a class="btn btn-link" href="https://api.whatsapp.com/send?phone=' . $sCodigoPaisCelular . $row->Nu_Celular_Entidad . '&text=' . $sMensajeWhatsAppVoucher . '" target="_blank"><i class="fab fa-whatsapp fa-2x" style="color: #25d366;"></i></a>';

				$voucher = $sMensajeWhatsAppVoucher;
			}
			$rows[] = $voucher;

            $rows[] = round($row->Ss_Total / 2, 2);
            $rows[] = round($row->Ss_Total, 2);
            //$rows[] = round($row->Qt_Total, 2);
			$arrEstadoRegistro = $this->HelperImportacionModel->obtenerEstadoPedidoArray($row->Nu_Estado);
            //$rows[] = '<span class="badge bg-' . $arrEstadoRegistro['No_Class_Estado'] . '">' . $arrEstadoRegistro['No_Estado'] . '</span>';

			$dropdown_estado = '<div class="dropdown">';
				$dropdown_estado .= '<button class="btn btn-' . $arrEstadoRegistro['No_Class_Estado'] . ' dropdown-toggle" type="button" data-toggle="dropdown">';
					$dropdown_estado .= $arrEstadoRegistro['No_Estado'];
				$dropdown_estado .= '<span class="caret"></span></button>';
				$dropdown_estado .= '<ul class="dropdown-menu">';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Pendiente" title="Pendiente" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',1);">Pendiente</a></li>';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Confirmado" title="Confirmado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',2);">Confirmado</a></li>';
					$dropdown_estado .= '<li class="dropdown-item"><a alt="Entregado" title="Entregado" href="javascript:void(0)" onclick="cambiarEstado(\'' . $row->ID_Pedido_Cabecera . '\',3);">Entregado</a></li>';
				$dropdown_estado .= '</ul>';
			$dropdown_estado .= '</div>';
            $rows[] = $dropdown_estado;

			$rows[] = '<button class="btn btn-xs btn-link" alt="Ver pedido" title="Ver pedido" href="javascript:void(0)"  onclick="verPedido(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
			//$rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCliente(\'' . $row->ID_Pedido_Cabecera . '\')"><i class="fas fa-trash-alt fa-2x" aria-hidden="true"></i></button>';
            $data[] = $rows;
        }
        $output = array(
	        'data' => $data,
        );
        echo json_encode($output);
    }
    	
	public function ajax_edit($ID){
        echo json_encode($this->PedidosGrupalModel->get_by_id($this->security->xss_clean($ID)));
    }

	public function cambiarEstado($ID, $Nu_Estado){
		if (!$this->input->is_ajax_request()) exit('No se puede eliminar y acceder');
    	echo json_encode($this->PedidosGrupalModel->cambiarEstado($this->security->xss_clean($ID), $this->security->xss_clean($Nu_Estado)));
	}
}
