<?php

require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
defined('BASEPATH') or exit('No direct script access allowed');
class CCotizaciones extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('CargaConsolidada/CCotizacionesModel');

        if (isset($this->session->userdata['usuario'])) {
            redirect('CCotizaciones/index');
        }

    }
    public function index(){
        $this->load->view('footer_v2', array("sockets" => true));

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
            <button class="btn btn-xs btn-link" alt="Descargar" title="Descargar" href="javascript:void(0)" onclick="descargarReporte(' . $row->ID_Cotizacion . ', \'' . $row->CotizacionCode . '\')"><i class="fas fa-file-excel fa-2x text-success" aria-hidden="true" id="descargar-reporte-' . $row->ID_Cotizacion . '"></i></button>
            <button class="btn btn-xs btn-link" alt="Descargar" title="Descargar PDF" href="javascript:void(0)" onclick="descargarBoletaPDF(' . $row->ID_Cotizacion . ', \'' . $row->CotizacionCode . '\', \'' . $row->N_Cliente . '\')"><i class="fas fa-file-pdf fa-2x text-danger" aria-hidden="true" id="descargar-pdf-' . $row->ID_Cotizacion . '"></i></button>
          </div>';
            $rows[] = '<button class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="verCotizacion(' . $row->ID_Cotizacion . ',' . $row->CotizacionCode . ')"><i class="far fa-edit fa-2x" aria-hidden="true" id="ver-cotizacion(' . $row->ID_Cotizacion . ')"></i></button>';
            //row with button to delete cotizacion 
            $rows[] = '<button class="btn btn-xs btn-link" alt="Eliminar" title="Eliminar" href="javascript:void(0)" onclick="eliminarCotizacion(' . $row->ID_Cotizacion . ')"><i class="far fa-trash-alt fa-2x text-danger" aria-hidden="true" id="eliminar-cotizacion(' . $row->ID_Cotizacion . ')"></i></button>';   
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
        $expirationDate = $_POST['expiration_date'];
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
                $zipFilePath = $this->CCotizacionesModel->generateMassiveExcelPayrolls($objPHPExcel, $tarifas, $expirationDate);
            //    echo json_encode($zipFilePath);
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
    }
    public function descargarBoleta()
    {
        try {
            // Obtener datos del POST (si es necesario)
            $postData = file_get_contents('php://input');
            $cotizacion = json_decode($postData, true);
            $C_Cotizacion = $cotizacion['C_Cotizacion'];

            // Ruta del archivo de plantilla Excel
            $templatePath = 'assets/downloads/Boleta_Template.xlsx';

            // Cargar el archivo de plantilla Excel
            $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
            $objPHPExcel = $this->CCotizacionesModel->fillExcelData($cotizacion, $objPHPExcel);
            $objPHPExcel->setActiveSheetIndex(0);
            $antidumping = $objPHPExcel->getActiveSheet()->getCell('B23')->getValue();
            $data = [
                "name" => $objPHPExcel->getActiveSheet()->getCell('C8')->getValue(),
                "lastname" => $objPHPExcel->getActiveSheet()->getCell('C9')->getValue(),
                "ID" => $objPHPExcel->getActiveSheet()->getCell('C10')->getValue(),
                "phone" => $objPHPExcel->getActiveSheet()->getCell('C11')->getValue(),
                "date" => date('d/m/Y'),
                "tipocliente" => $objPHPExcel->getActiveSheet()->getCell('F11')->getValue(),
                "peso" => $objPHPExcel->getActiveSheet()->getCell('J9')->getCalculatedValue(),
                "qtysuppliers" => $objPHPExcel->getActiveSheet()->getCell('J10')->getValue(),
                "cbm" => $objPHPExcel->getActiveSheet()->getCell('J11')->getCalculatedValue(),
                "valorcarga" => round($objPHPExcel->getActiveSheet()->getCell('K14')->getCalculatedValue(), 2),
                "fleteseguro" => round($objPHPExcel->getActiveSheet()->getCell('K15')->getCalculatedValue(), 2),
                "valorcif" => round($objPHPExcel->getActiveSheet()->getCell('K16')->getCalculatedValue(), 2),
                "advalorempercent" => intval($objPHPExcel->getActiveSheet()->getCell('J20')->getCalculatedValue()*100) ,
                "advalorem" => round($objPHPExcel->getActiveSheet()->getCell('K20')->getCalculatedValue(), 2),
                "antidumping" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2) : "",

                "igv" => round($objPHPExcel->getActiveSheet()->getCell('K21')->getCalculatedValue(), 2),
                "ipm" => round($objPHPExcel->getActiveSheet()->getCell('K22')->getCalculatedValue(), 2),
                "subtotal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K24')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K23')->getCalculatedValue(), 2),
                "percepcion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K25')->getCalculatedValue(), 2),
                "total" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K27')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K26')->getCalculatedValue(), 2),
                "valorcargaproveedor" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K29')->getCalculatedValue(), 2),
                "servicioimportacion" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K30')->getCalculatedValue(), 2),
                "impuestos" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K31')->getCalculatedValue(), 2),
                "montototal" => $antidumping == "ANTIDUMPING" ? round($objPHPExcel->getActiveSheet()->getCell('K33')->getCalculatedValue(), 2) : round($objPHPExcel->getActiveSheet()->getCell('K32')->getCalculatedValue(), 2),
            ];
            //iterate until you find the total word from c36 to more
            $i = 36;
            $items = [];
            while ($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue() != 'TOTAL') {
                //add item to items array
                $item = [
                    "index" => $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue(),
                    "name" => $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue(),
                    "qty" => $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue(),
                    "costounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "preciounit" => number_format(round($objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                    "total" => round($objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue(), 2),
                    "preciounitpen" => number_format(round($objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue(), 2), 2, '.', ','),
                ];
                $items[] = $item;
                $i++;
            }
            $itemsCount = count($items);
            $data["br"] = $itemsCount - 18 < 0 ? str_repeat("<br>", 18 - $itemsCount) : "";
            $data['items'] = $items;
            $logoContent = file_get_contents(base_url() . 'assets/downloads/logo.png');
            $logoData = base64_encode($logoContent);
            $data["logo"] = 'data:image/png;base64,' . $logoData;
            $htmlFilePath = 'assets/downloads/Boleta_Template.html';
            $htmlContent = file_get_contents($htmlFilePath);
            $pagosContent = file_get_contents(base_url() . 'assets/downloads/pagos.png');
            $pagosData = base64_encode($pagosContent);
            $data["pagos"] = 'data:image/png;base64,' . $pagosData;
            //replace {{name}} with data['name']
            foreach ($data as $key => $value) {
                //if value is a number parse to 2 decimals with comma as unit separator and dot as decimal separator
                if (is_numeric($value)) {
                    if ($value == 0) {
                        $value = '-';
                    }
                    if ($key != "ID" && $key != "phone" && $key != "qtysuppliers" && $key != "advalorempercent") {
                        $value = number_format($value, 2, '.', ',');
                    }
                }
                if ($key == "antidumping" && $antidumping == "ANTIDUMPING") {
                    $antidumpingHtml = '<tr style="background:#FFFF33">
                    <td style="border-top:none!important;border-bottom:none!important" colspan="3">ANTIDUMPING</td>
                    <td style="border-top:none!important;border-bottom:none!important" ></td>
                    <td style="border-top:none!important;border-bottom:none!important" >$' . number_format($data['antidumping'], 2, '.', ',') . '</td>
                    <td style="border-top:none!important;border-bottom:none!important" >USD</td>
                    </tr>';
                    $htmlContent = str_replace('{{antidumping}}', $antidumpingHtml, $htmlContent);
                    //search items with class ipm and set border none
                    }
                if ($key == "items") {
                    $itemsHtml = "";
                    $total = 0;
                    $cantidad = 0;
                    foreach ($value as $item) {
                        $total += $item['total'];
                        $cantidad += $item['qty'];
                        $itemsHtml .= '<tr>
                        <td colspan="1">' . $item['index'] . '</td>
                        <td colspan="5">' . $item['name'] . '</td>
                        <td colspan="1">' . $item['qty'] . '</td>
                        <td colspan="2">$ ' . $item['costounit'] . '</td>
                        <td colspan="1">$ ' . $item['preciounit'] . '</td>
                        <td colspan="1">$ ' . number_format($item['total'], 2, '.', ',') . '</td>
                        <td colspan="1">S/. ' . $item['preciounitpen'] . '</td>
                    </tr>';
                    }
                    $itemsHtml .= '<tr>
                    <td colspan="6" >TOTAL</td>
                    <td >' . $cantidad. '</td>
                    <td colspan="2" style="border:none!important"></td>
                    <td style="border:none!important"></td>
                    <td >$ ' . number_format($total, 2, '.', ','). '</td>
                    <td style="border:none!important"></td>

                </tr>';
                    $htmlContent = str_replace('{{' . $key . '}}', $itemsHtml, $htmlContent);
                } else {
                    $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                }

            }
            $options = new Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);

            // $dompdf->loadHtml('<img src="data:image/png;base64,' . $imgData . '">');
            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('Cotizacion' . $C_Cotizacion . '.pdf', array("Attachment" => 0));
            // Eliminar el archivo temporal

        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
    public function getBase64Image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    public function replaceClassesWithStyle($htmlContent, $classesToReplace, $style)
    {
        $pattern = '/(?<=\sclass=["\'])((?:\s*\w+\s*)+?)(?=(?:' . implode('|', array_map('preg_quote', $classesToReplace, array_fill(0, count($classesToReplace), '/'))) . '))/is';
        $replacement = function ($match) use ($classesToReplace, $style) {
            $classes = explode(' ', trim($match[0]));
            $newClasses = array_filter($classes, function ($class) use ($classesToReplace) {
                return !in_array($class, $classesToReplace);
            });
            return 'style="' . $style . '"';
        };

        return preg_replace_callback($pattern, $replacement, $htmlContent);
    }
    public function eliminarElementoPorClase($htmlContent, $tagName, $className)
    {
        // Construir el patrón de búsqueda para encontrar el elemento con la clase específica
        $pattern = '/<' . $tagName . '[^>]*\sclass=[\'"]' . $className . '[\'"][^>]*>.*?<\/' . $tagName . '>/is';

        // Reemplazar el elemento encontrado con una cadena vacía para eliminarlo
        $htmlContent = preg_replace($pattern, '', $htmlContent);

        return $htmlContent;
    }
    public function ajustarRutasImagenes($htmlContent, $objPHPExcel)
    {
        // Recorrer las imágenes dentro del archivo Excel y ajustar sus rutas en el HTML
        foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                // Obtener la etiqueta <img> generada por PhpSpreadsheet
                $htmlImageTag = '<img src="' . $drawing->getIndexedFilename() . '" alt="' . $drawing->getDescription() . '">';

                // Ajustar la ruta de la imagen en el HTML
                $htmlContent = str_replace($drawing->getIndexedFilename(), $htmlImageTag, $htmlContent);
            }
        }

        return $htmlContent;
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
    public function deleteCotization(){
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $id = $data['ID_Cotizacion'];
        echo json_encode($this->CCotizacionesModel->deleteCotization($id));
    }
}
