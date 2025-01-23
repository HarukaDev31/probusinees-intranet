<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/WhatsappTrait.php';

require_once APPPATH . 'traits/WebSocketTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';
require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
class ContenedorConsolidadoModel extends CI_Model{
    use FileTrait,WebSocketTrait,WhatsappTrait;
	var $table_cliente = 'entidad';
    private $table_usuario = 'usuario';
	private $table="carga_consolidada_contenedor";
    private $table_pais="pais";
    private $table_contenedor_steps="contenedor_consolidado_order_steps";
    private $table_contenedor_cotizacion="contenedor_consolidado_cotizacion";
    private $table_contenedor_cotizacion_proveedores="contenedor_consolidado_cotizacion_proveedores";
    private $table_contenedor_documentacion_files="contenedor_consolidado_documentacion_files";
    private $table_contenedor_documentacion_folders="contenedor_consolidado_documentacion_folders";
    private $table_contenedor_tipo_cliente="contenedor_consolidado_tipo_cliente";
    private $table_contenedor_cotizacion_documentacion="contenedor_consolidado_cotizacion_documentacion";
    private $roleCotizador="Cotizador";
    private $roleCoordinacion="Coordinación";
    private $aNewContainer="new-container";
    private $aNewCotizacion="new-cotizacion";
    private $table_contenedor_cotizacion_proveedores_documentacion="contenedor_consolidado_proveedores_documentacion";
    var $order = array('carga_consolidada_pedido_cabecera.Fe_Registro' => 'desc');
	public function __construct(){
		parent::__construct();
	}
    public function index(){
        $this->db->select("*")
        ->from($this->table)
        ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join');
        if($this->input->post('Filtro_Estado')!="0"){
            $this->db->where('estado', $this->input->post('Filtro_Estado'));
        }

        $query = $this->db->get();
        return $query->result();

    }
    public function getPaises(){
        $this->db->select("*")
        ->from($this->table_pais);
        $query = $this->db->get();
        return $query->result();
    }
    public function store($data){
        //set data in table 
        $this->db->insert($this->table, $data);
        if($this->db->affected_rows() > 0){
            //{"project": "0", "role": "Cotizador", "user": "0", "message": "Prueba de comunicación en tiempo real","action":"new-container"}
            $socketResponse=$this->sendEvent([
                "project" => "0",
                "role" => $this->roleCotizador,
                "user" => "0",
                "action"=>$this->aNewContainer,
                "message" => "asdas",
            ]);
            return [
                'id' => $this->db->insert_id(),
                'status' => true,
                'socketResponse'=>$socketResponse
            ];
        }
        return false;
    }
    public function update($data){
        $this->db->where('id', $data['id']);
        $this->db->update($this->table, $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function show($id){
        $this->db->select("*")
        ->from($this->table)->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.id_pais', 'join')
        ->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function delete($id){
        //select all from table  $table_contenedor_cotizacion
        //set foreign key check to 0 and delete all files in folder and delete folder
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $this->db->where('id_contenedor', $id);
        $this->db->delete($this->table_contenedor_cotizacion);
        //select all from table  $table_contenedor_steps
        $this->db->where('id_pedido', $id);
        $this->db->delete($this->table_contenedor_steps);
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        if($this->db->affected_rows() > 0){
            return true;
        }
        $errors = $this->db->error();

        return $errors;
        return false;
    }
    public function generateSteps($steps){
        $this->db->insert_batch($this->table_contenedor_steps, $steps);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function getOrderProgress($idContenedor): array
    {
        try {
            $this->db->select('
            id,
            name,
            status,
            iconURL
            ');
            $this->db->from($this->table_contenedor_steps);
            $this->db->where('id_pedido', $idContenedor);
            $this->db->order_by('id_order', 'asc');
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getContenedorCotizacion($idContenedor){
        $this->db->select("*," . $this->table_contenedor_cotizacion . ".id AS id_cotizacion")
        ->from($this->table_contenedor_cotizacion)
        ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = ' . $this->table_contenedor_cotizacion . '.id_tipo_cliente', 'join')
        ->where('id_contenedor', $idContenedor);
        $query = $this->db->get();
        return $query->result();
    }
    public function getContenedorCotizacionProveedores($idContenedor){
       //select from table_contenedor_cotizacion join usuario.ID_USUARIO id_usuario,in array json select proveedores from table_contenedor_cotizacion_proveedores where id_cotizacion= firstable.id_cotizacion
        $this->db->select("main.*,
        U.No_Usuario,
        (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'id', proveedores.id,
                    'qty_box', proveedores.qty_box,
                    'peso', proveedores.peso,
                    'cbm_total', proveedores.cbm_total,
                    'supplier', proveedores.supplier,
                    'code_supplier', proveedores.code_supplier,
                    'estados_proveedor', proveedores.estados_proveedor,
                    'estados', proveedores.estados,
                    'supplier_phone', proveedores.supplier_phone,
                    'id_proveedor', proveedores.id
                )
            )
            FROM " . $this->table_contenedor_cotizacion_proveedores . " proveedores 
            WHERE proveedores.id_cotizacion = main.id
        ) as proveedores")
        ->from($this->table_contenedor_cotizacion . " as main")
        ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = main.id_tipo_cliente', 'join')
        ->join($this->table_usuario . ' AS U', 'U.ID_Usuario = main.id_usuario', 'left')
        ->where('main.id_contenedor', $idContenedor);
        $query = $this->db->get();
        return $query->result();
       
    }
    public function getContenedorClientes($idContenedor){
        $this->db->select("*," . $this->table_contenedor_cotizacion . ".id AS id_cotizacion")
        ->from($this->table_contenedor_cotizacion)
        ->join($this->table_contenedor_tipo_cliente . ' AS TC', 'TC.id = ' . $this->table_contenedor_cotizacion . '.id_tipo_cliente', 'join')
        ->where('id_contenedor', $idContenedor)
        ->where('estado', 'CONFIRMADO');
        if($this->input->post('estado')!="0"){
            $this->db->where('estado_cliente', $this->input->post('estado'));
        }
        $query = $this->db->get();
        return $query->result();
    }
    public  function convertDateFormat($date) {
		$dateObject = DateTime::createFromFormat('d/m/Y', $date);
		return $dateObject ? $dateObject->format('Y-m-d') : null; // Devuelve null si la fecha no es válida
	}
    public function getCotizacionData($cotizacion){
        try{
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            //find sheet 1 and get cell b8 as nombre,cell b9 as documento,cell b10 as correo,cell b11 as telefono,i11 as volumen,e9 as fecha
            $sheet = $objPHPExcel->getSheet(0);
            $nombre = $sheet->getCell('B8')->getValue();
            $documento = $sheet->getCell('B9')->getValue();
            $correo = $sheet->getCell('B10')->getValue();
            $telefono = $sheet->getCell('B11')->getValue();
            $volumen = $sheet->getCell('I11')->getCalculatedValue(); 
            $valorCot=$sheet->getCell('J14')->getCalculatedValue();
            //get calculated value from cell e9
            $fecha = $sheet->getCell('E9')->getValue(); 
            if($fecha=="=+TODAY()"){
                $fecha = date("Y-m-d");
            }else{
                $fecha = $this->convertDateFormat($fecha);
            }
        
            //get tipo cliente for e11 
            $tipoCliente = $sheet->getCell('E11')->getValue();
            //find if exists in table contenedor_consolidado_tipo_cliente with name = $tipoCliente else create new and get id
            $idTipoCliente = $this->db->select('id')
            ->from($this->table_contenedor_tipo_cliente)
            ->where('name', $tipoCliente)
            ->get();
            if($idTipoCliente->num_rows() == 0){
                $this->db->insert($this->table_contenedor_tipo_cliente, ['name' => $tipoCliente]);
                $idTipoCliente = $this->db->insert_id();
            }else{
                $idTipoCliente = $idTipoCliente->row()->id;
            }
            if(trim($sheet->getCell('A23')->getValue())=="ANTIDUMPING"){
                $monto=$sheet->getCell('J31')->getCalculatedValue();
            }else{
                $monto=$sheet->getCell('J30')->getCalculatedValue();
            }
            $tarifa=$monto/($volumen<=0?1:$volumen);
            return [
                'nombre' => $nombre,
                'documento' => $documento,
                'correo' => $correo,
                'telefono' => $telefono,
                'volumen' => $volumen,
                'id_tipo_cliente' => $idTipoCliente,
                'fecha' => $fecha,
                'valor_cot'=>$valorCot,
                'monto'=>$monto,
                'tarifa'=>$tarifa
            ];
        }catch(Exception $e){
            return $e->getMessage();
        }

    }
    public function incrementColumn($column, $increment = 1)
    {
        $column = strtoupper($column); // Asegurarse de que todas las letras sean mayúsculas
        $length = strlen($column);
        $number = 0;

        // Convertir la columna a un número
        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        // Incrementar el número
        $number += $increment;

        // Convertir el número de vuelta a una columna
        $newColumn = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $newColumn = chr(ord('A') + $remainder) . $newColumn;
            $number = intval(($number - 1) / 26);
        }

        return $newColumn;
    }
    public function getEmbarqueData($cotizacion,$data){
        try{
            $objPHPExcel = PHPExcel_IOFactory::load($cotizacion['tmp_name']);
            $rowProveedores=4;
            $nameCliente=$objPHPExcel->getSheet(0)->getCell('B8')->getValue();
            $sheet2=$objPHPExcel->getSheet(1);
            $columnStart="C";
            $columnTotales="";
            //if data if associative array convert to object
            if(is_array($data)){
                $data=(object)$data;
            }
            $idContenedor=$data->id_contenedor;
            //get count of row with name in table cotizacion and this id_contenedor
            $query = $this->db->select('COUNT(*) as count')
            ->from($this->table_contenedor_cotizacion)
            ->where('id_contenedor', $idContenedor)
            ->where('UPPER(nombre)', strtoupper($nameCliente))
            ->get();
            $count=$query->row()->count==0?1:$query->row()->count+1;//if count is 0 set 1 else increment by 1
            $stop=false;

            while(!$stop){
                $cell=$sheet2->getCell($columnStart."3")->getValue();
                if(strtoupper(trim($cell))=="TOTALES"){
                    $columnTotales=$columnStart;
                    $stop=true;
                }else{
                    $columnStart=$this->incrementColumn($columnStart);
                }
            }
            $rowCajasProveedor=5;
            $rowPesoProveedor=6;
            $rowVolProveedor=8;
            //iterate from C TO $columnTotales and get values from row 5,6,8
            $columnStart = "C"; // Columna inicial
            $stop = false;
            $provider = 1;
            $currentRange = null;
            $processedRanges = []; // Almacena los rangos procesados
            $proveedores = []; // Lista de proveedores

            while (!$stop) {
                // Verifica si la columna actual es la última
                    if ($columnStart == $columnTotales) {
                        $stop = true;
                    } else {
                        // Obtiene el rango combinado de la celda actual
                        $cell = $sheet2->getCell($columnStart . $rowProveedores);
                        $currentRange = $cell->getMergeRange();

                        // Si el rango ya fue procesado, pasa a la siguiente columna
                        if ($currentRange && in_array($currentRange, $processedRanges)) {
                            $columnStart = $this->incrementColumn($columnStart);
                            continue;
                        }

                        // Agrega el rango actual a los rangos procesados
                        if ($currentRange) {
                            $processedRanges[] = $currentRange;
                        }

                        // Genera el código del proveedor
                        $codeSupplier = $this->generateCodeSupplier($nameCliente, $count, $provider);

                        // Agrega los datos del proveedor
                        $proveedores[] = [
                            'qty_box' => $sheet2->getCell($columnStart . $rowCajasProveedor)->getValue(),
                            'peso' => $sheet2->getCell($columnStart . $rowPesoProveedor)->getValue(),
                            'cbm_total' => $sheet2->getCell($columnStart . $rowVolProveedor)->getValue(),
                            'id_cotizacion' => $data->id_cotizacion,
                            'code_supplier' => $codeSupplier,
                        ];

                        // Incrementa la columna y el contador del proveedor
                        $columnStart = $this->incrementColumn($columnStart);
                        $provider++;
                    }
                }

                return $proveedores;

        }catch(Exception $e){
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
    public function generateCodeSupplier($string,$rowCount,$index){
        //from string get first letter each word in uppercase and concatenate with rowCount and index
        $words=explode(" ",$string);
        $code="";
        foreach($words as $word){
            $code.=strtoupper(substr($word,0,1));
        }
        return $code.$rowCount."-".$index;
    }
    public function storeCotizacion($data,$cotizacion){
       try{
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
		$fileUrl= $this->uploadSingleFile(
            [
                "name" => $cotizacion['name'],
                "type" => $cotizacion['type'],
                "tmp_name" => $cotizacion['tmp_name'],
                "error" => $cotizacion['error'],
                "size" => $cotizacion['size']
            ]
            , 'assets/images/agentecompra/');
        $dataToInsert=$this->getCotizacionData($cotizacion);

        $dataToInsert['cotizacion_file_url']=$fileUrl;
        $dataToInsert['id_contenedor']=$data['id_contenedor'];
        $dataToInsert['id_usuario']=$this->user->ID_Usuario;
        $this->db->insert($this->table_contenedor_cotizacion, $dataToInsert);  

        if($this->db->affected_rows() > 0){
           
            //get inserted id
            $idCotizacion=$this->db->insert_id();
            $dataToInsert['id_cotizacion']=$idCotizacion;
            $dataEmbarque=$this->getEmbarqueData($cotizacion,$dataToInsert);
            //insert in tabla proveedores 
            $this->db->insert_batch($this->table_contenedor_cotizacion_proveedores, $dataEmbarque);
            //if db error return error
            if($this->db->error()['code']!=0){
                return [
                    'status' => "error",
                    'message' => $this->db->error()['message']
                ];
            }
            if($this->db->affected_rows() > 0){
                //{"project": "0", "role": "Cotizador", "user": "0", "message": "Prueba de comunicación en tiempo real","action":"new-cotizacion"}
                $this->sendEvent([
                    "project" => "0",
                    "role" => $this->roleCotizador,
                    "user" => "0",
                    "action"=>$this->aNewCotizacion,
                    "message" => "Nueva cotización",
                ]);
                return [
                    'id' => $idCotizacion,
                    'status' => "success"
                ];
            }
            return false;
		}
        if($this->db->error()['code']!=0){
            return [
                'status' => $this->db->error()['message'],
                'message' => $this->db->error()['message']
            ];
        }
		return false;
       }catch(Exception $e){
        return[
            'status' => "error",
            'message' => $e->getMessage()
        ];
       }
	}
    public function getTipoCliente(){
        $this->db->select("*")
        ->from($this->table_contenedor_tipo_cliente);
        $query = $this->db->get();
        return $query->result();
    }
    public function deleteCotizacionFile($id){
        $this->db->select('cotizacion_file_url')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion, ['cotizacion_file_url' => null]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function deleteCotizacion($id){
        //set foreign key check to 0 and delete all files in folder and delete folder
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->select('cotizacion_file_url')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->delete($this->table_contenedor_cotizacion);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        //check if db error exists
        if($this->db->error()['code']!=0){
            return [
                'status' => "error",
                'message' => $this->db->error()['message']
            ];
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        return false;
    }
    public function uploadCotizacionFile($id,$file){
        try{
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $this->db->select('*')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        $data=$query->row();
        $fileUrl=$data->cotizacion_file_url;
        unlink($fileUrl);
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ]
            , 'assets/images/agentecompra/');
        $dataToInsert=$this->getCotizacionData($file);
        $dataToInsert['cotizacion_file_url']=$fileUrl;
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion, $dataToInsert);
        //truncate all data in table contenedor_consolidado_cotizacion_proveedores where id_cotizacion=$id
        
        if($this->db->affected_rows() > 0){
            $this->db->where('id_cotizacion', $id);
            $this->db->delete($this->table_contenedor_cotizacion_proveedores);
            $dataEmbarque=$this->getEmbarqueData($file,$data);
            //insert in tabla proveedores
            $this->db->insert_batch($this->table_contenedor_cotizacion_proveedores, $dataEmbarque);
            if($this->db->affected_rows() > 0){
                return "success";
            }
            return false;
        }
        return false;
        }catch(Exception $e){
            return false;
        }
    }
    public function showCotizacion($id){
        $this->db->select("*")
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function updateCotizacion($data,$cotizacion){
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $this->db->select('cotizacion_file_url')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $data['id']);
        $query = $this->db->get();
        $fileUrl=$query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $cotizacion['name'],
                "type" => $cotizacion['type'],
                "tmp_name" => $cotizacion['tmp_name'],
                "error" => $cotizacion['error'],
                "size" => $cotizacion['size']
            ]
            , 'assets/images/agentecompra/');
        $data['cotizacion_file_url']=$fileUrl;
        $this->db->where('id', $data['id']);
        $this->db->update($this->table_contenedor_cotizacion, $data);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateEstadoCotizacion($id,$estado){
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateEstado($id,$estado){
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        $errors = $this->db->error();
       
        return false;
    }
    public function showClientesDocumentacion($id){
        $this->db->select("main.*, (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', docs.id,
                        'file_url', docs.file_url,
                        'folder_name', docs.name
                    )
                )
                FROM " . $this->table_contenedor_cotizacion_documentacion . " docs 
                WHERE docs.id_cotizacion = main.id
            ) as files")
            ->from($this->table_contenedor_cotizacion . " as main")
            ->where('main.id', $id)
            ->where('main.estado', 'CONFIRMADO');
        $query = $this->db->get();
        return $query->result();
    }
    public function createClienteDocumentacion($id,$name,$file){
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ]
            , 'assets/images/agentecompra/');
        $this->db->insert($this->table_contenedor_cotizacion_documentacion, ['id_cotizacion' => $id, 'name' => $name, 'file_url' => $fileUrl]);
        if($this->db->affected_rows() > 0){
            return ['status' => "success"];
        }
        return false;
    }
    public function deleteClienteDocumentacionFile($id){
        try{

        $this->db->select('file_url')
        ->from($this->table_contenedor_cotizacion_documentacion)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->file_url;
            unlink($fileUrl);
            $this->db->delete($this->table_contenedor_cotizacion_documentacion, ['id' => $id]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
        }catch(Exception $e){
            return false;
        }
        
    }
    public function updateClienteDocumentacion($data,$files){
        try{
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl=null;
        //if exists file_comercial in files update file_comercial
        if(isset($files['file_comercial'])){
            $this->db->select('factura_comercial')
            ->from($this->table_contenedor_cotizacion)
            ->where('id', $data['id']);
            $query = $this->db->get();
            $fileUrl=$query->row()->file_url;
            unlink($fileUrl);
            $fileUrl= $this->uploadSingleFile(
                [
                    "name" => $files['file_comercial']['name'],
                    "type" => $files['file_comercial']['type'],
                    "tmp_name" => $files['file_comercial']['tmp_name'],
                    "error" => $files['file_comercial']['error'],
                    "size" => $files['file_comercial']['size']
                ]
                , 'assets/images/agentecompra/');
                $data['factura_comercial']=$fileUrl;

        }
        $this->db->where('id', $data['id']);
        $this->db->update($this->table_contenedor_cotizacion, $data);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }catch(Exception $e){
        return false;
    }
    }
    public function uploadListaEmbarque($idCotizacion,$idContenedor,$file){
        //read excel and get data from row 5 to more get D and o values foreach row
        try{
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ]
            , 'assets/images/agentecompra/');
        $objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $data=[];
        $initialRow=5;
        for ($row = $initialRow; $row <= $highestRow; ++$row) {
            $data[]=[
                'name' => $sheet->getCell('D'.$row)->getValue(),
                'volumen_china' => $sheet->getCell('O'.$row)->getValue()
            ];
        }
        //compare nombre in table contenedor_consolidado_cotizacion where id_contenedor=$idContenedor and compare with data excel array if exists update volumen_china
        $this->db->select('id,nombre')
        ->from($this->table_contenedor_cotizacion)
        ->where('id_contenedor', $idContenedor);
        $query = $this->db->get();
        $cotizaciones=$query->result();
        foreach($cotizaciones as $cotizacion){
            foreach($data as $item){
                if(trim($cotizacion->nombre)==trim($item['name'])){
                    $this->db->where('id', $cotizacion->id);
                    $this->db->update($this->table_contenedor_cotizacion, ['volumen_china' => $item['volumen_china']]);
                }
            }
        }
        //update lista_embarque_url in table contenedor_consolidado_cotizacion where id=$idCotizacion
        $this->db->where('id', $idContenedor);
        $this->db->update($this->table, ['lista_embarque_url' => $fileUrl]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
        }catch(Exception $e){
            return false;
        }

    }
    public function updateEstadoCliente($id,$estado){
        $this->db->set('estado_cliente', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function validateListEmbarque($id){
        $this->db->select('lista_embarque_url')
        ->from($this->table)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->lista_embarque_url;
        if($fileUrl!=null){
            return true;
        }
        return false;
    }
    public function deleteFacturaComercial($id){
        try{
            $this->db->select('factura_comercial')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->factura_comercial;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->update($this->table_contenedor_cotizacion, ['factura_comercial' => null]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
        }catch(Exception $e){
            return false;
        }
    }
    public function deleteCliente($idCotizacion){
        try{
            $this->db->select('cotizacion_file_url')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $idCotizacion);
        $query = $this->db->get();
        $fileUrl=$query->row()->cotizacion_file_url;
        unlink($fileUrl);
        $this->db->where('id', $idCotizacion);
        $this->db->delete($this->table_contenedor_cotizacion);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
        }catch(Exception $e){
            return false;
        }

    }
     public function getDocumentationFolderFiles($id){
        //select * from folders where id_cotizacion is null or $id and left join files where id_folder = id
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id 
            and files.id_contenedor = '.$id, 'left')
            ->where('main.id_contenedor', $id)
            ->or_where('main.id_contenedor', null);
        $query = $this->db->get();
        return $query->result();
    }
    public function uploadFileDocumentation($idFolder,$idContenedor,$file){
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ]
            , 'assets/images/agentecompra/');
        //find if exists file in table contenedor_consolidado_documentacion_files where id_folder=$idFolder and id_contenedor=$idContenedor
        //if exists delete
        $this->db->delete($this->table_contenedor_documentacion_files, ['id_folder' => $idFolder, 'id_contenedor' => $idContenedor]);
        $this->db->insert($this->table_contenedor_documentacion_files, ['id_folder' => $idFolder, 'file_url' => $fileUrl,
        'id_contenedor' => $idContenedor]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function deleteDocumentacionFolder($id){
        try{
            //delete all files with same id_folder and unlink files and later delete folder
            $this->db->select('file_url')
            ->from($this->table_contenedor_documentacion_files)
            ->where('id_folder', $id);
            $query = $this->db->get();
            $files=$query->result();
            foreach($files as $file){
                unlink($file->file_url);
                
                }
            $files=$query->result();
            //delete files row
            $this->db->where('id_folder', $id);
            $this->db->delete($this->table_contenedor_documentacion_files);
            //delete folder row
            
            $this->db->where('id', $id);
            $this->db->delete($this->table_contenedor_documentacion_folders);
            if($this->db->affected_rows() > 0){
                return "success";
            }
            return false;
        }catch(Exception $e){
            return false;
        }
    }
    public function deleteDocumentacionFile($id){
        try{
            $this->db->select('file_url')
        ->from($this->table_contenedor_documentacion_files)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->file_url;
        unlink($fileUrl);
        $this->db->where('id', $id);
        $this->db->delete($this->table_contenedor_documentacion_files);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
        }catch(Exception $e){
            return false;
        }
    }
    public function downloadDocumentacionZip($id){
        try {
            $this->db->select("main.*,files.id AS id_file,files.file_url")
                ->from($this->table_contenedor_documentacion_folders . " as main")
                ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
                ->where('files.id_contenedor', $id);
            $query = $this->db->get();
            $folders = $query->result();
            
            // $zip = new ZipArchive;
            // $zipName = 'assets/images/agentecompra/contenedor_'.$id.'.zip';
           
            // if ($zip->open($zipName, ZipArchive::CREATE|ZipArchive::OVERWRITE) === TRUE) {
                $filePath = "C://xampp//htdocs//probusinees-intranet//assets//images//agentecompra//678a64a26787a.xlsx";
                $zipName = 'assets/images/agentecompra/contenedor_' . $id . '.zip';
                
                if (file_exists($zipName)) {
                    unlink($zipName); // Eliminar el ZIP anterior si existe
                }
                
                $zip = new ZipArchive;
                
                // // Abre o crea el ZIP
                // if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                //     // Leer el contenido del archivo
                //     $content = file_get_contents($filePath);
                
                //     if ($content === false) {
                //         echo "Error al leer el archivo: $filePath\n";
                //     } else {
                //         // Agregar el contenido al ZIP
                //         $fileNameInZip = 'xd.xlsx'; // Nombre que tendrá el archivo dentro del ZIP
                //         if ($zip->addFromString($fileNameInZip, $content)) {
                //         } else {
                //         }
                //     }
                
                //     // Cierra el ZIP
                   
                // } else {
                //     echo "Error al abrir el ZIP.\n";
                // }
                if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                    // Agrega los archivos al ZIP
                    foreach ($folders as $folder) {
                        // Paso 1: Decodificar la URL para obtener la ruta del archivo
                        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $folder->file_url); // Extraer ruta relativa
                        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
                        $fullPath = FCPATH . ltrim($decodedPath, '/'); // Construir la ruta completa
                    
                        // Verificar si el archivo existe
                        if (file_exists($fullPath) && is_readable($fullPath)) {
                            $fileName = basename($decodedPath); // Obtener el nombre del archivo
                            // Normalizar el nombre si es necesario
                            if (function_exists('normalizer_normalize')) {
                                $fileName = normalizer_normalize($fileName, Normalizer::FORM_C);
                            }
                            // Asegurarse de que el nombre sea seguro
                            $fileName = preg_replace('/[\x00-\x1F\x7F<>:"\/\\|?*]/', '', $fileName);
                    
                            // Agregar archivo al ZIP
                            if (!$zip->addFile($fullPath, $folder->folder_name . '/' . $fileName)) {
                                log_message('error', 'No se pudo agregar al ZIP: ' . $fullPath);
                            }
                        } else {
                            log_message('error', 'Archivo no encontrado o no legible: ' . $fullPath);
                        }
                    }
                    if($zip->close()!==false) {
                    } else {
                    } 
                    return $zipName;
                }           // }
            return false;
        } catch(Exception $e) {
            echo $e->getMessage();
            log_message('error', 'Error en ZIP: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
    public function downloadFacturaComercial($idContenedor){
        //find in folder join files folser with name factura comercial,packing list  and lista partidas
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Factura Comercial');
        $query = $this->db->get();
        //find in query result folder_name factura comercial and get file_url
        $facturaComercial=$query->row();
        if(!$facturaComercial){
            return ['status' => "error",'message'=>"No se encontró la factura comercial"];
        }
        $facturaComercial=$facturaComercial->file_url;
        //validate if factura_comercial is xls, xlsx,xlsm
        $path_parts = pathinfo($facturaComercial);
        $extension = $path_parts['extension'];
        if($extension!="xls" && $extension!="xlsx" && $extension!="xlsm"){
            return ['status' => "error",'message'=>"La Factura Comercial no es un archivo de excel"];
        }
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Packing List');
        $query = $this->db->get();
        //find in query result folder_name packing list and get file_url
        $packingList=$query->row();
        if(!$packingList){
            return ['status' => "error",'message'=>"No se encontró el packing list"];
        }
        $packingList=$packingList->file_url;
        //validate if packing list is xls, xlsx,xlsm
        $path_parts = pathinfo($packingList);
        $extension = $path_parts['extension'];
        if($extension!="xls" && $extension!="xlsx" && $extension!="xlsm"){
            return ['status' => "error",'message'=>"El Packing List no es un archivo de excel"];
        }
        $this->db->select("main.*,files.id AS id_file,files.file_url")
            ->from($this->table_contenedor_documentacion_folders . " as main")
            ->join($this->table_contenedor_documentacion_files . ' AS files', 'files.id_folder = main.id', 'left')
            ->where('files.id_contenedor', $idContenedor)
            ->where('main.folder_name', 'Lista de Partidas');
        $query = $this->db->get();
        //find in query result folder_name lista de partidas and get file_url
        $listaPartidas=$query->row();
        if(!$listaPartidas){
            return ['status' => "error",'message'=>"No se encontró la lista de partidas"];
        }
        $listaPartidas=$listaPartidas->file_url;
        //validate if lista de partidas is xls, xlsx,xlsm
        $path_parts = pathinfo($listaPartidas);
        $extension = $path_parts['extension'];
        if($extension!="xls" && $extension!="xlsx" && $extension!="xlsm"){
            return ['status' => "error",'message'=>"La Lista de Partidas no es un archivo de excel"];
        }
        //get object PHPExcel from factura 
        //sanitize file url
        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $facturaComercial); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $facturaComercial = FCPATH . ltrim($decodedPath, '/'); 
        $objPHPExcel = PHPExcel_IOFactory::load($facturaComercial);
        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $packingList); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $packingList = FCPATH . ltrim($decodedPath, '/'); 
        $objPHPExcelPacking = PHPExcel_IOFactory::load($packingList);
        $filePath = preg_replace('/.*(\/assets\/.*)/', '$1', $listaPartidas); // Extraer ruta relativa
        $decodedPath = rawurldecode($filePath); // Decodificar la ruta codificada
        $listaPartidas = FCPATH . ltrim($decodedPath, '/');
        $objPHPExcelListaPartidas = PHPExcel_IOFactory::load($listaPartidas);
        //get b merged rows range
     
        $itemNColumn="B";
        $tipoClienteColumn="C";
        $clienteColumn="D";
        $descriptionColumn="D";
        $descriptionNColumn="E";
        $quantityCountColumn="L";
        $quantityCountNColumn="M";
        $quantityMeasureColumn="M";
        $quantityMeasureNColumn="N";
        $unitPriceColumn="N";
        $unitPriceNColumn="O";
        $unitMeasureColumn="O";
        $unitMeasureNColumn="P";
        $fobPriceColumn="P";
        $fobPriceNColumn="Q";
        $startColumn=26;
        $startPackingListColumn=27;
        $startListaPartidasColumn=6;
        $startIndex=$startColumn;
        $startPackingListIndex=$startPackingListColumn;
        $highestFirstSheetRow=0;
        $skyBlueColor="85c1e9";
        $pinkColor="f5b7b1";
        $greenColor="7dcea0";
        $grayColor="dcdde1";
        $yellow2Color="fad7a0";
        $dataSystem=$this->db->select('nombre,volumen,volumen_doc,valor_doc,valor_cot,volumen_china,name')
        ->from($this->table_contenedor_cotizacion)
        ->join($this->table_contenedor_tipo_cliente, 'contenedor_consolidado_cotizacion.id_tipo_cliente = contenedor_consolidado_tipo_cliente.id')
        ->where('id_contenedor', $idContenedor)
        ->where('estado',"CONFIRMADO")
        ->get()->result();  
        //return $dataSystem;
        try{    
            $sheetCount = $objPHPExcel->getSheetCount();
            $sheet0=$objPHPExcel->getSheet(0);
            $sheet0->insertNewColumnBefore('C', 2);
            $sheet0->setCellValue('D25','CLIENTE');
            $sheet0->setCellValue('C25','TIPO DE CLIENTE');
            //remove e column 
            $sheet0->removeColumn('E');
            $sheet0->setCellValue('R25','ADVALOREM');
            $sheet0->setCellValue('S25','ANTIDUMPING');
            $sheet0->setCellValue('T25','VOL. COT.');
            $sheet0->setCellValue('U25','VOL. CHINA');
            $sheet0->setCellValue('V25','VOL. DOC.');
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
            );
            //set title font bold and center horizontal
            $sheet0->getStyle('A25:Z25')->getFont()->setBold(true);
            $sheet0->getStyle('A25:Z25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheetPackingList=$objPHPExcelPacking->getSheet(0);
            $sheetListaPartidas=$objPHPExcelListaPartidas->getSheet(2);
            //SET R TO V style
            $sheet0->getStyle('R25:V25')->applyFromArray($styleArray);
            for($i=0;$i<$sheetCount;$i++){
                $sheet = $objPHPExcel->getSheet($i);
                //get from start column to bcolumn= where trim value=TOTAL FOB PRICE remove this row and go to next sheet and add columns to this range for first sheet and this for each sheet
                if($i==0){
                    $highestRow = $sheet->getHighestRow();
                    $nameActual="";
                    $mergedEndCell=0;
                    $mergedStartCell=$startIndex;
                    for ($row = $startIndex; $row <= $highestRow; ++$row) {
                        $itemN = $sheet->getCell($itemNColumn.$row)->getValue();
                        //get g merged column value where b merged column value in listapartidas =itemN using =vlookup
                        $mergedCells = $sheetListaPartidas->getMergeCells();
                        foreach ($mergedCells as $range) {
                            // Extraer las celdas inicial y final del rango
                            [$startCell, $endCell] = explode(':', $range);
                        
                            // Verificar si el rango está en la columna B
                            if (preg_match('/^B\d+$/', $startCell)) {
                                // Obtener el valor de la celda fusionada
                                $value = $sheetListaPartidas->getCell($startCell)->getValue();
                        
                                // Comparar el valor con el itemNumber
                                if (trim($value) == $itemN) {
                                    // Obtener el rango de filas del rango fusionado
                                    preg_match('/\d+/', $startCell, $startMatches);
                                    preg_match('/\d+/', $endCell, $endMatches);
                                    $startRow = (int)$startMatches[0];
                                    $endRow = (int)$endMatches[0];
                        
                                    // Obtener el valor de la columna G para el rango fusionado
                                    for ($r = $startRow; $r <= $endRow; $r++) {
                                        $adValorem = $sheetListaPartidas->getCell('G'.$r)->getValue();
                                        $antiDumping = $sheetListaPartidas->getCell('H'.$r)->getValue();

                                        $sheet->setCellValue('R'.$row,$adValorem);
                                        $sheet->setCellValue('S'.$row,$antiDumping==0?"-":$antiDumping);
                                        break;
                                    }
                        
                                    // Salir del bucle si ya encontramos el rango que buscamos
                                    break;
                                }
                            }
                        }
                        //set client col value= packinglist c column  startPackingListIndex
                        $client = $sheetPackingList->getCell('C'.$startPackingListIndex)->getValue();
                        if ($client !== $nameActual) {
                            if ($nameActual !== "") {
                                // Si cambia el cliente, fusionar las celdas desde el inicio hasta la última fila del bloque actual
                                $sheet->mergeCells('C' . $mergedStartCell . ':C' . $mergedEndCell);
                                $sheet->mergeCells('D' . $mergedStartCell . ':D' . $mergedEndCell);
                                // $sheet->mergeCells('R' . $mergedStartCell . ':R' . $mergedEndCell);
                                // $sheet->mergeCells('S' . $mergedStartCell . ':S' . $mergedEndCell);
                                $sheet0->mergeCells('T' . $mergedStartCell . ':T' . $mergedEndCell);
                                $sheet0->mergeCells('U' . $mergedStartCell . ':U' . $mergedEndCell);
                                $sheet0->mergeCells('V' . $mergedStartCell . ':V' . $mergedEndCell);
                            }
                    
                            // Actualizar el valor actual y establecer nuevas celdas iniciales
                            $nameActual = $client;
                            $mergedStartCell = $row;
                        }
                        $mergedEndCell = $row;
                        $sheet->setCellValue('D'.$row,$client);
                        //find if exists row in datasystem array where trim(nombre)=trim(client) if exists set volumen_cotizacion, volumen_china, volumen_doc, valor_doc, valor_cot else set -
                        $volumen_cotizacion="-";
                        $volumen_china="-";
                        $volumen_doc="-";
                        $valor_doc="-";
                        $valor_cot="-";
                        $tipoCliente="No existe en contenedor";
                        //find in array
                        foreach($dataSystem as $item){
                            if(trim($item->nombre)==trim($client)){
                                $volumen_cotizacion=$item->volumen;
                                $volumen_china=$item->volumen_china;
                                $volumen_doc=$item->volumen_doc;
                                $valor_doc=$item->valor_doc;
                                $tipoCliente=$item->name;
                                break;
                            }
                        }
                        //set vol_cot to t column
                        $sheet->setCellValue('T'.$row,$volumen_cotizacion);
                        $sheet->setCellValue('U'.$row,$volumen_china);
                        $sheet->setCellValue('V'.$row,$volumen_doc);
                        $sheet->setCellValue('C'.$row,$tipoCliente);
                        if(trim($itemN)=="TOTAL FOB PRICE"){
                            //unmerge cell 

                            $objPHPExcel->getActiveSheet()->unmergeCells('B'.$row.':P'.$row);
                            // //MERGE FROM D TO K
                            $objPHPExcel->getActiveSheet()->mergeCells('E'.$row.':L'.$row);
                            $highestRow=$row-1;
                            $highestFirstSheetRow+=$highestRow+1;
                            
                            break;
                        }
                        $startPackingListIndex++;   
                        
                        
                        $sheet0->getStyle('R'.$row.':V'.$row)->applyFromArray($styleArray);
                        //set horizontal alignment to center
                        $sheet0->getStyle('R'.$row.':V'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        //set vertical alignment to center
                        $sheet0->getStyle('R'.$row.':V'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        
                        $sheet0->getStyle('R'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                    }
                }else{
                    $startIndex=$startColumn;
                    $highestSheetRow = $sheet->getHighestRow();
                    for ($row = $startIndex; $row <= $highestSheetRow; ++$row) {
                        $client = $sheetPackingList->getCell('C'.$startPackingListIndex)->getValue();
                        if ($client !== $nameActual) {
                            if ($nameActual !== "") {
                                $sheet0->mergeCells('C' . $mergedStartCell . ':C' . $mergedEndCell);
                                $sheet0->mergeCells('D' . $mergedStartCell . ':D' . $mergedEndCell);
                                // $sheet0->mergeCells('R' . $mergedStartCell . ':R' . $mergedEndCell);
                                // $sheet0->mergeCells('S' . $mergedStartCell . ':S' . $mergedEndCell);
                                $sheet0->mergeCells('T' . $mergedStartCell . ':T' . $mergedEndCell);
                                $sheet0->mergeCells('U' . $mergedStartCell . ':U' . $mergedEndCell);
                                $sheet0->mergeCells('V' . $mergedStartCell . ':V' . $mergedEndCell);
                            }
                    
                            // Actualizar el valor actual y establecer nuevas celdas iniciales
                            $nameActual = $client;
                            $mergedStartCell = $highestFirstSheetRow;
                        }
                        $mergedEndCell = $highestFirstSheetRow;
                       
                        $sheet0 = $objPHPExcel->getSheet(0);
                        $itemN = $sheet->getCell($itemNColumn.$row)->getValue();
                        
                        if(trim($itemN)=="TOTAL FOB PRICE"){
                            $highestSheetRow=$row-1;
                            break;
                        }
                        $sheet0->insertNewRowBefore($highestFirstSheetRow, 1);

                        $volumen_cotizacion="-";
                        $volumen_china="-";
                        $volumen_doc="-";
                        $valor_doc="-";
                        $valor_cot="-";
                        $tipoCliente="No existe en contenedor";
                        //find in array
                        foreach($dataSystem as $item){
                            if(trim($item->nombre)==trim($client)){
                                $volumen_cotizacion=$item->volumen;
                                $volumen_china=$item->volumen_china;
                                $volumen_doc=$item->volumen_doc;
                                $valor_doc=$item->valor_doc;
                                $tipoCliente=$item->name;
                                break;
                            }
                        }
                        
                        //set vol_cot to t column
                        $sheet0->setCellValue('T'.$highestFirstSheetRow,$volumen_cotizacion);
                        $sheet0->setCellValue('U'.$highestFirstSheetRow,$volumen_china);
                        $sheet0->setCellValue('V'.$highestFirstSheetRow,$volumen_doc);
                        $sheet0->setCellValue('C'.$highestFirstSheetRow,$tipoCliente);
                        $mergedCells = $sheetListaPartidas->getMergeCells();
                        foreach ($mergedCells as $range) {
                            // Extraer las celdas inicial y final del rango
                            [$startCell, $endCell] = explode(':', $range);
                        
                            // Verificar si el rango está en la columna B
                            if (preg_match('/^B\d+$/', $startCell)) {
                                // Obtener el valor de la celda fusionada
                                $value = $sheetListaPartidas->getCell($startCell)->getValue();
                        
                                // Comparar el valor con el itemNumber
                                if (trim($value) == $itemN) {
                                    // Obtener el rango de filas del rango fusionado
                                    preg_match('/\d+/', $startCell, $startMatches);
                                    preg_match('/\d+/', $endCell, $endMatches);
                                    $startRow = (int)$startMatches[0];
                                    $endRow = (int)$endMatches[0];
                        
                                    // Obtener el valor de la columna G para el rango fusionado
                                    for ($r = $startRow; $r <= $endRow; $r++) {
                                        $adValorem = $sheetListaPartidas->getCell('G'.$r)->getValue();
                                        $antiDumping = $sheetListaPartidas->getCell('H'.$r)->getValue();

                                        $sheet0->setCellValue('R'.$highestFirstSheetRow,$adValorem);
                                        $sheet0->setCellValue('S'.$highestFirstSheetRow,$antiDumping==0?"-":$antiDumping);
                                        break;
                                    }
                        
                                    // Salir del bucle si ya encontramos el rango que buscamos
                                    break;
                                }
                            }
                        }
                        $sheet0->setCellValue('D'.$highestFirstSheetRow,$client);
                        
                        //get this sheet values and insert in first sheet
                      
                        // $brand = $sheet->getCell($brandColumn.$row)->getValue();
                        $description = $sheet->getCell($descriptionColumn.$row)->getValue();
                        $quantityCount = $sheet->getCell($quantityCountColumn.$row)->getValue();
                        $quantityMeasure = $sheet->getCell($quantityMeasureColumn.$row)->getValue();
                        $unitPrice = $sheet->getCell($unitPriceColumn.$row)->getValue();
                        $unitMeasure = $sheet->getCell($unitMeasureColumn.$row)->getValue();
                        $fobPrice = $sheet->getCell($fobPriceColumn.$row)->getValue();
                        $sheet0->setCellValue($itemNColumn.$highestFirstSheetRow,$itemN);

                        // // $sheet0->setCellValue($brandColumn.$highestFirstSheetRow,$brand);
                        $sheet0->setCellValue($descriptionNColumn.$highestFirstSheetRow,$description);
                        $sheet0->setCellValue($quantityCountNColumn.$highestFirstSheetRow,$quantityCount);
                        $sheet0->setCellValue($quantityMeasureNColumn.$highestFirstSheetRow,$quantityMeasure);
                        $sheet0->setCellValue($unitPriceNColumn.$highestFirstSheetRow,$unitPrice);
                        $sheet0->setCellValue($unitMeasureNColumn.$highestFirstSheetRow,$unitMeasure);
                        $sheet0->setCellValue($fobPriceNColumn.$highestFirstSheetRow,"=".$quantityCountNColumn.$highestFirstSheetRow."*".$unitPriceNColumn.$highestFirstSheetRow);
                        // // $sheet0->mergeCells('D'.$highestFirstSheetRow.':K'.$highestFirstSheetRow);
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                )
                            )
                        );
                        $objPHPExcel->getActiveSheet()->mergeCells('E'.$highestFirstSheetRow.':L'.$highestFirstSheetRow);


                        $sheet0->getStyle('R'.$highestFirstSheetRow.':V'.$highestFirstSheetRow)->applyFromArray($styleArray);
                        //set horizontal alignment to center
                        $sheet0->getStyle('R'.$highestFirstSheetRow.':V'.$highestFirstSheetRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        //set vertical alignment to center
                        $sheet0->getStyle('R'.$highestFirstSheetRow.':V'.$highestFirstSheetRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $sheet0->getStyle('O'.$highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        $sheet0->getStyle('Q'.$highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                        //set r column porcentage format
                        $sheet0->getStyle('R'.$highestFirstSheetRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                        $highestFirstSheetRow++;

                        $startPackingListIndex++;


                    }
                }

            }
            //unmerge e to l
            $objPHPExcel->getActiveSheet()->unmergeCells('E'.$highestFirstSheetRow.':L'.$highestFirstSheetRow);
            $sheet0->mergeCells('B'.$highestFirstSheetRow.':P'.$highestFirstSheetRow);
            //set fill none in sheet 0 row=highestFirstSheetRow
            $sheet0->getStyle('R'.$highestFirstSheetRow.':V'.$highestFirstSheetRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
            //set all borders
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
            );
            $sheet0->getStyle('R'.$highestFirstSheetRow.':V'.$highestFirstSheetRow)->applyFromArray($styleArray);
            
              //MERGE B TO 0
              //set p highestFirstSheetRow value to sum from p.startColumn to p.highestFirstSheetRow-1
            $sheet0->setCellValue('Q'.$highestFirstSheetRow,'=SUM(Q'.$startColumn.':Q'.($highestFirstSheetRow-1).')');
              //set  d column auto size                
            $sheet0->getStyle('D')->getAlignment()->setWrapText(true);
            $sheet0->getColumnDimension('C')->setWidth(30);

            $sheet0->getColumnDimension('D')->setWidth(60);
            //SET WIDTH TO COLUMNS 
            $sheet0->getColumnDimension('R')->setWidth(20);
            $sheet0->getColumnDimension('S')->setWidth(25);
            $sheet0->getColumnDimension('T')->setWidth(15);
            $sheet0->getColumnDimension('U')->setWidth(15);
            $sheet0->getColumnDimension('V')->setWidth(15);
            //from b starcolumn to b highestFirstSheetRow-1 set fill pinkColor
            $sheet0->getStyle('C'.($startColumn-1).':C'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('C'.($startColumn-1).':C'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($pinkColor);
            //D TO GRAY, R AND S TO SKYBLUE, T TO PINK,U TO GREEN
            $sheet0->getStyle('D'.($startColumn-1).':D'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('D'.($startColumn-1).':D'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($grayColor);
            $sheet0->getStyle('R'.($startColumn-1).':S'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('R'.($startColumn-1).':S'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($skyBlueColor);
            $sheet0->getStyle('T'.($startColumn-1).':T'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('T'.($startColumn-1).':T'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($pinkColor);
            $sheet0->getStyle('U'.($startColumn-1).':U'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('U'.($startColumn-1).':U'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($greenColor);
            $sheet0->getStyle('V'.($startColumn-1).':V'.($highestFirstSheetRow-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet0->getStyle('V'.($startColumn-1).':V'.($highestFirstSheetRow-1))->getFill()->getStartColor()->setRGB($yellow2Color);
                //set wrap text to true
            return $objPHPExcel;
        }catch(Exception $e){
            return ['status' => "error",'message'=>$e->getMessage()];
        }

    }

    public function createDocumentacionFolder($name,$idContenedor,$file){
        try{
            $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $fileUrl= $this->uploadSingleFile(
            [
                "name" => $file['name'],
                "type" => $file['type'],
                "tmp_name" => $file['tmp_name'],
                "error" => $file['error'],
                "size" => $file['size']
            ]
            , 'assets/images/agentecompra/');
        if($fileUrl){
            $this->db->insert($this->table_contenedor_documentacion_folders, ['id_contenedor' => $idContenedor, 'folder_name' => $name]);
            if($this->db->affected_rows() > 0){
                $idFolder=$this->db->insert_id();
                //insert file in table contenedor_consolidado_documentacion_files
                $this->db->insert($this->table_contenedor_documentacion_files, ['id_folder' => $idFolder, 'file_url' => $fileUrl]);
                if($this->db->affected_rows() > 0){
                    return ['status' => "success",'error'=>false];
                }
                //if db error delete folder
               return ['status' => "error",'error'=>true];
             }
        }
       
        if($this->db->error()){
            return ['status' => "error",'error'=>$this->db->error()];
        } 
        }catch(Exception $e){
            return false;
        }
    }
    public function updateEstadoCotizacionProveedor($idCotizacion,$idProveedor,$estado){
        $this->db->where('id_cotizacion', $idCotizacion);
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados' => $estado]);
        //find query error
       
        if($this->db->affected_rows() > 0){
            $data=$this->handlerUpdateCotizacionProveedor($estado,$idProveedor,$idCotizacion);
            return "success";
        }
        return false;
    }
    public function handlerUpdateCotizacionProveedor($estado,$idProveedor,$idCotizacion){
        if($estado=="ROTULADO"){

           try{
             //select nombre from contenedor_consolidado_cotizacion where id=idCotizacion
             $this->db->select('nombre,id_contenedor')
             ->from($this->table_contenedor_cotizacion)
             ->where('id', $idCotizacion);
             $query = $this->db->get();
             $cliente=$query->row()->nombre;
             $idContenedor=$query->row()->id_contenedor;
 
             //get supplier_code from contenedor_consolidado_cotizacion_proveedores where id=idProveedor
             $this->db->select('code_supplier')
             ->from($this->table_contenedor_cotizacion_proveedores)
             ->where('id', $idProveedor);
             $query = $this->db->get();
             $supplierCode=$query->row()->code_supplier;
             //select carga from contenedor_consolidado where id=idContenedor
             $this->db->select('carga')
             ->from($this->table)
             ->where('id', $idContenedor);
             $query = $this->db->get();
             $carga=$query->row()->carga;
 
             $htmlFilePath = 'assets/downloads/Rotulado_Template.html';
             $htmlContent = file_get_contents($htmlFilePath);
             $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', mb_detect_encoding($htmlContent));
         
         
             $htmlContent = str_replace('{{cliente}}', $cliente, $htmlContent);
             $htmlContent = str_replace('{{supplier_code}}', $supplierCode, $htmlContent);
             $htmlContent = str_replace('{{carga}}', $carga, $htmlContent);
         
             $options = new Dompdf\Options();
             $options->set('isHtml5ParserEnabled', true);
             $options->set('isFontSubsettingEnabled', true);
             $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);

            //  $fontDir = 'assets/downloads/';
            //  $options->set('fontDir', $fontDir);
         
         
            
             
          
            //  // Add CSS for font
            //  $htmlContent = '
            // <style>
            // @font-face {
            //     font-family: "NotoSansTC";
            //     src: url("assets/downloads/NotoSansTC-Regular.ttf") format("truetype");
            // }
            // body { 
            //     font-family: "NotoSansTC", "Noto Sans TC", Arial, sans-serif; 
            // }
            // </style>' . $htmlContent;
            // $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', mb_detect_encoding($htmlContent));
        //     $dompdf->getFontMetrics()->registerFont(
        //         ['family' => 'Muli', 'style' => 'normal', 'weight' => 'normal'],
        //         $fontDir . '/muli-v20-latin-regular.ttf'
        // );

             $dompdf->loadHtml($htmlContent);
             $dompdf->setPaper('A4', 'portrait');
             $dompdf->render();
             $pdfContent = $dompdf->output();
             $tempFilePath = sys_get_temp_dir() . '/temp_document.pdf';
                file_put_contents($tempFilePath, $pdfContent);
                echo $tempFilePath;
                return ;
                try {
                    $mediaId = $this->uploadDocument($tempFilePath, 'application/pdf');
                    $sendRotulado = $this->sendRotulado($mediaId, $supplierCode);
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                } finally {
                    // Eliminar el archivo temporal
                    if (file_exists($tempFilePath)) {
                        unlink($tempFilePath);
                    }
}
            // $ruta = 'assets/downloads/Rotulado.pdf';
            // $fileUrl= file_put_contents($ruta, $output);
            // $fileUrl=base_url($ruta);
            
           
            //     $response=$this->sendRotulado($fileUrl,"waos");
                
            
            $dompdf->stream('Cotizacion.pdf', ["Attachment" => 0]);
           }catch(Exception $e){
               echo $e->getMessage();
           }
        }
        return true;
    }
    public function updateTelefonoProveedor($idProveedor,$telefono){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores,
         ['supplier_phone' => $telefono,
    "estados" => "DATOS PROVEEDOR"]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateProveedor($idProveedor,$proveedor){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, 
        ['supplier' => $proveedor]
    );
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateQtyChina($idProveedor,$qtyChina){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['qty_box_china' => $qtyChina]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateCBMChina($idProveedor,$cbm){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['cbm_total_china' => $cbm]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateArriveDateChina($idProveedor,$arriveDate){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['arrive_date_china' => $arriveDate]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateProductos($idProveedor,$productos){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['productos' => $productos]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function updateEstadoProveedor($idProveedor,$estados_proveedor){
        $this->db->where('id', $idProveedor);
        $this->db->update($this->table_contenedor_cotizacion_proveedores, ['estados_proveedor' => $estados_proveedor]);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    public function uploadFileInspection($idProveedor,$files){
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
     
        $index=0;
        $filesArray=[];
        foreach ($files['files']['name'] as $index => $fileName) {
            $fileToUp=  [
                "name" => $files['files']['name'][$index],
                "type" => $files['files']['type'][$index],
                "tmp_name" => $files['files']['tmp_name'][$index],
                "error" => $files['files']['error'][$index],
                "size" => $files['files']['size'][$index]
            ];
            $fileUrl= $this->uploadSingleFile(
                $fileToUp
                , 'assets/images/agentecompra/');
            if($fileUrl){
                $this->db->insert($this->table_contenedor_cotizacion_proveedores_documentacion, 
                ['id_proveedor' => $idProveedor, 'file_url' => $fileUrl,'file_name'=>$file['name'][$index],
                'file_ext'=>$files['files']['type'][$index]]);
                if($this->db->affected_rows() > 0){
                    $fileToReturn=[
                        'id'=>$this->db->insert_id(),
                        'file_url'=>$fileUrl,
                        'file_name'=>$files['files']['name'][$index],
                        'file_ext'=>$files['files']['type'][$index]
                    ];
                    $filesArray[]=$fileToReturn;
                }
            }
            $index++;
        }
       
        //validate if exists db error
       
        return ['status' => "success",'error'=>false,"data"=>$filesArray];
    }
    public function verCotizacionEmbarqueFiles($idProveedor){
        $this->db->select('*')
        ->from($this->table_contenedor_cotizacion_proveedores_documentacion)
        ->where('id_proveedor', $idProveedor);
        $query = $this->db->get();
        return $query->result();
    }
    public function deleteFile($idFile){
        $this->db->where('id', $idFile);
        $this->db->delete($this->table_contenedor_cotizacion_proveedores_documentacion);
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
    }
    
      
}