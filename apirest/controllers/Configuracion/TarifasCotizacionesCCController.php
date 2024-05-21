<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TarifasCotizacionesCCController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('LAE_SYSTEMS');
        $this->load->model('Configuracion/TarifasCotizacionesCCModel');

    }
    public function listar()
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }
        $this->load->view('header_v2');
        $this->load->view('Configuracion/TarifasCotizacionesCCView');
        $this->load->view('footer_v2', array("js_tarifas_cotizaciones" => true));
    }
    public function getTarifas()
    {
        $sMethod = $this->input->post('sMethod');
        $arrData = $this->TarifasCotizacionesCCModel->getTarifas();
        $data = array();
        foreach ($arrData as $row) {
            $rows = array();

            $rows[] = $row->id_tipo_tarifa == 1 ? 'Estandar' : 'No Estandar';
            $rows[] = $row->Nombre;
            $rows[] = $row->limite_inf;
            $rows[] = $row->limite_sup;
            $rows[] = $row->currency;
            $rows[] = $row->tarifa;
            $rows[] = $row->updated_at == null ? 'Activo' : "No Activo";
            $rows[] = $row->created_at;
            //create button modidy tarifa with fucntion modificartarifa with parameter id_tarifa and tarifa
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="modificarTarifa(' . $row->id_tarifa . ',' . $row->tarifa . ')"><i class="far fa-edit fa-2x" aria-hidden="true" id="modificar-tarifa(' . $row->id_tarifa . ')"></i></button>';
            $data[] = $rows;
        }
        $outpus = array(
            'data' => $data,
        );
        echo json_encode($outpus);
    }
    public function modificarTarifa()
    {
        $id_tarifa = $this->input->post('id_tarifa');
        $tarifa = $this->input->post('tarifa');

// Paso 1: Actualizar el registro existente con updated_at
        $data_update = array(
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id_tarifa', $id_tarifa);
        $this->db->update('carga_consolidada_cbm_tarifas', $data_update);

// Paso 2: Obtener los datos del registro existente excepto id_tarifa y updated_at
        $this->db->select('*');
        $this->db->from('carga_consolidada_cbm_tarifas');
        $this->db->where('id_tarifa', $id_tarifa);
        $query = $this->db->get();
        $existing_data = $query->row_array();

        if ($existing_data) {
            // Eliminar los campos id_tarifa y updated_at del array de datos
            unset($existing_data['id_tarifa']);
            unset($existing_data['updated_at']);

            // Agregar/Modificar los campos tarifa y created_at
            $existing_data['tarifa'] = $tarifa;
            $existing_data['created_at'] = date('Y-m-d H:i:s');

            // Paso 3: Insertar el nuevo registro
            $this->db->insert('carga_consolidada_cbm_tarifas', $existing_data);
        }

        echo json_encode(array('status' => 200));
    }
}
