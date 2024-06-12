<?php

require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

defined('BASEPATH') or exit('No direct script access allowed');
class CCotizaciones extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('CargaConsolidada/CCotizacionesModel');
        if (!isset($this->session->userdata['usuario'])) {
            redirect('');
        }

    }
    public function listar()
    {
        if (!$this->MenuModel->verificarAccesoMenu()) {
            redirect('Inicio/InicioView');
        }

        $this->load->view('header_v2');
        $this->load->view('CargaConsolidada/CCotizacionesView');
        $this->load->view('footer_v2', array("js_ccotizaciones" => true));
    }
    public function ajax_list()
    {
        $sMethod = $this->input->post('sMethod');
        $arrData = $this->CCotizacionesModel->get_datatables();
        $data = array();

        foreach ($arrData as $row) {
            $select = '<select class="form-control" id="selectTipoCliente" name="selectTipoCliente" onchange="updateTipoCliente(this,' . $row->ID_Cotizacion . ')">';
            //if row.ID_TipoCliente == $tipoCliente.value then selected = selected else selected = ''
            foreach (json_decode($row->Client_Types, true) as $tipoCliente) {
                $option = '<option value="' . $tipoCliente['value'] . '"';
                if ($tipoCliente['value'] == $row->ID_Tipo_Cliente) {
                    $option .= ' selected';
                }
                $option .= '>' . $tipoCliente['label'] . '</option>';
                $select .= $option;

            }
            $select .= '</select>';

            $rows = array();
            $rows[] = $row->CotizacionCode;
            //fe_Creation to unix timestamp
            $rows[] = strtotime($row->Fe_Creacion);
            $rows[] = ToDateBD($row->Fe_Creacion);
            $rows[] = $row->N_Cliente;
            $rows[] = $row->Telefono;
            $rows[] = $row->Empresa;
            $rows[] = $select;
            $rows[] = '<div>
            <button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarReporte(' . $row->ID_Cotizacion . ',' . $row->CotizacionCode . ')"><i class="fas fa-file-excel fa-2x text-success" aria-hidden="true" id="descargar-reporte(' . $row->ID_Cotizacion . ')"></i></button>
                        <button class="btn btn-xs btn-link" alt="Descargar" title="Descargar PDF" href="javascript:void(0)" onclick="descargarBoletaPDF(' . $row->ID_Cotizacion . ',' . $row->CotizacionCode . ')"><i class="fas fa-file-pdf fa-2x text-danger" aria-hidden="true" id="descargar-pdf(' . $row->ID_Cotizacion . ')"></i></button>

            </div>';
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCotizacion(' . $row->ID_Cotizacion . ',' . $row->CotizacionCode . ')"><i class="far fa-edit fa-2x" aria-hidden="true" id="ver-cotizacion(' . $row->ID_Cotizacion . ')"></i></button>';
            //select with options pendiente,cotizado,confirmado
            $rows[] = '<select class="form-control" id="selectEstado" name="selectEstado" onchange="updateEstadoCotizacion(this,' . $row->ID_Cotizacion . ')">
            <option value="1" ' . ($row->Cotizacion_Status_ID == 1 ? 'selected' : '') . '>Pendiente</option>
            <option value="2" ' . ($row->Cotizacion_Status_ID == 2 ? 'selected' : '') . '>Cotizado</option>
            <option value="3" ' . ($row->Cotizacion_Status_ID == 3 ? 'selected' : '') . '>Confirmado</option>
            </select>';
            $rows[] = $row->Cotizacion_Status_ID;
            $data[] = $rows;
        }
        $output = array(
            'data' => $data,
        );
        echo json_encode($output);
    }public function getTypeClient()
    {
        return $this->CCotizacionesModel->getTypeClient();
    }

    public function ajax_edit_header($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_header($this->security->xss_clean($ID)));
    }
    public function ajax_edit_body($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_body($this->security->xss_clean($ID)));
    }
    public function ajax_edit_tributos($ID)
    {
        echo json_encode($this->CCotizacionesModel->get_cotization_tributos($this->security->xss_clean($ID)));
    }
    public function guardarTributos()
    {
        $postData = file_get_contents('php://input');
        $tributos = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarTributos($tributos));

    }
    public function guardarCotizacion()
    {
        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->guardarCotizacion($cotizacion));
    }
    public function getExcelData()
    {
        $data = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Get the uploaded file
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validate file type if necessary
            $allowedfileExtensions = array('xls', 'xlsx');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                //convert this excel to phpoject
                $this->load->library('PHPExcel');
                $objPHPExcel = PHPExcel_IOFactory::load($fileTmpPath);
                $data = $this->CCotizacionesModel->getMassiveExcelData($objPHPExcel);
            }
        }

        echo json_encode($data);
    }
    public function uploadExcelMassive()
    {
        //get tarifas from post
        $tarifas = $_POST['tarifas'];
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Get the uploaded file
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validate file type if necessary
            $allowedfileExtensions = array('xls', 'xlsx');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                //convert this excel to phpoject
                $this->load->library('PHPExcel');
                $objPHPExcel = PHPExcel_IOFactory::load($fileTmpPath);
                $zipFilePath = $this->CCotizacionesModel->generateMassiveExcelPayrolls($objPHPExcel, $tarifas);
                // Assuming $zipFilePath is the path to the generated ZIP file
                if (file_exists($zipFilePath)) {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
                    header('Content-Length: ' . filesize($zipFilePath));
                    readfile($zipFilePath);
                    unlink($zipFilePath);
                    exit();
                } else {
                    // Handle error if file generation failed
                    echo "Error: Unable to generate the ZIP file.";
                }
            } else {
                echo "Error: Invalid file extension.";
            }
        } else {
            echo "Error: " . $_FILES['file']['error'];
        }

    }
    public function descargarExcel()
    {
        ob_start();

        $postData = file_get_contents('php://input');
        $cotizacion = json_decode($postData, true);
        $C_Cotizacion = $cotizacion['C_Cotizacion'];

        $this->load->library('PHPExcel');

        // Create a new PHPExcel object
        $templatePath = 'assets/downloads/Boleta_Template.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $objPHPExcel = $this->CCotizacionesModel->fillExcelData($cotizacion, $objPHPExcel);
        // // Add some data to the sheet

        // Set the content type header to indicate that this is an Excel file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cotizacion' . $C_Cotizacion . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit(); //
    }public function descargarBoleta()
    {
        try {
            $postData = file_get_contents('php://input');
            $cotizacion = json_decode($postData, true);
            $C_Cotizacion = $cotizacion['C_Cotizacion'];
            // Cargar el archivo de plantilla Excel
            $templatePath = 'assets/downloads/Boleta_Template.xlsx';
            $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
            $objPHPExcel = $this->CCotizacionesModel->fillExcelData($cotizacion, $objPHPExcel);
            //get only first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
            $htmlFilePath = 'assets/downloads/temp.html';
            $objWriter->save($htmlFilePath);    
            $htmlContent = file_get_contents($htmlFilePath);
            
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Configurar márgenes
            $pdf->SetMargins(0, 0, 0); // Márgenes izquierdo, superior, derecho
            $pdf->SetHeaderMargin(0); // Margen superior del encabezado
            $pdf->SetFooterMargin(0);
            $pdf->SetAutoPageBreak(TRUE, 10);


            // Añadir una página
            $pdf->AddPage();

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            //remove    html file
            unlink($htmlFilePath);

    
            //devolver el pdf
            $pdf->Output('Cotizacion' . $C_Cotizacion . '.pdf', 'I');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getTipoCliente()
    {
        echo json_encode($this->CCotizacionesModel->getTipoCliente());
    }

    public function updateTipoCliente()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->updateTipoCliente($data));
    }
    public function updateEstadoCotizacion()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        echo json_encode($this->CCotizacionesModel->updateEstadoCotizacion($data));
    }
    public function getTarifas()
    {
        echo json_encode($this->CCotizacionesModel->getTarifas());
    }
}
