<?php
require_once APPPATH . 'traits/FileTrait.php';
require_once APPPATH . 'third_party/PHPExcel.php';

class ContenedorConsolidadoModel extends CI_Model{
    use FileTrait;
	var $table_cliente = 'entidad';
	private $table="carga_consolidada_contenedor";
    private $table_pais="pais";
    private $table_contenedor_steps="contenedor_consolidado_order_steps";
    private $table_contenedor_cotizacion="contenedor_consolidado_cotizacion";
    private $table_contenedor_tipo_cliente="contenedor_consolidado_tipo_cliente";
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
            return [
                'id' => $this->db->insert_id(),
                'status' => true
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
        ->where('id_contenedor', $idContenedor);
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
                'fecha' => $fecha
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
}