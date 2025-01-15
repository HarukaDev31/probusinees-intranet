<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'traits/WebSocketTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';

class ContenedorConsolidadoModel extends CI_Model{
    use FileTrait,WebSocketTrait;
	var $table_cliente = 'entidad';
	private $table="carga_consolidada_contenedor";
    private $table_pais="pais";
    private $table_contenedor_steps="contenedor_consolidado_order_steps";
    private $table_contenedor_cotizacion="contenedor_consolidado_cotizacion";
    private $table_contenedor_tipo_cliente="contenedor_consolidado_tipo_cliente";
    private $table_contenedor_cotizacion_documentacion="contenedor_consolidado_cotizacion_documentacion";
    private $roleCotizador="Cotizador";
    private $roleCoordinacion="Coordinación";
    private $aNewContainer="new-container";
    private $aNewCotizacion="new-cotizacion";
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
        // if Fe_Inicio_Carga is not null and Fe_Fin_Carga is null
        // if($this->input->post('Fe_Inicio_Carga') != "" && $this->input->post('Fe_Fin_Carga') == ""){
        //     $this->db->where('Fe_Inicio_Carga >=', $this->input->post('Fe_Inicio_Carga'));
        // }
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
        $this->db->where('id_contenedor', $id);
        $this->db->delete($this->table_contenedor_cotizacion);
        //select all from table  $table_contenedor_steps
        $this->db->where('id_pedido', $id);
        $this->db->delete($this->table_contenedor_steps);
        $this->db->where('id', $id);
        $this->db->delete($this->table);
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
        ->where('id_contenedor', $idContenedor)
        ->where('estado', 'PENDIENTE');
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
            $volumen = $sheet->getCell('I11')->getValue(); 
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
            return [
                'nombre' => $nombre,
                'documento' => $documento,
                'correo' => $correo,
                'telefono' => $telefono,
                'volumen' => $volumen,
                'id_tipo_cliente' => $idTipoCliente,
                'fecha' => $fecha,
                'valor_cot'=>$valorCot
            ];
        }catch(Exception $e){
            return $e->getMessage();
        }

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
        
        $this->db->insert($this->table_contenedor_cotizacion, $dataToInsert);             
        if($this->db->affected_rows() > 0){
            $this->sendEvent([
                "project" => "0",
                "role" => $this->roleCotizador,
                "user" => "0",
                "action"=>$this->aNewCotizacion,
                "message" => "Nueva cotización",
            ]);
			return [
				'id' => $this->db->insert_id(),
				'status' => "success"
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
        return false;
    }
    public function uploadCotizacionFile($id,$file){
        $this->maxFileSize = 1000000;
        $this->setAllowedExtensionsImagesOfficeFiles();
        $this->db->select('cotizacion_file_url')
        ->from($this->table_contenedor_cotizacion)
        ->where('id', $id);
        $query = $this->db->get();
        $fileUrl=$query->row()->cotizacion_file_url;
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
        if($this->db->affected_rows() > 0){
            return "success";
        }
        return false;
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
            
        }
        $data['factura_comercial']=$fileUrl;
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
}