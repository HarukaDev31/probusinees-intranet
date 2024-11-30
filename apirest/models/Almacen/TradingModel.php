<?php
require_once APPPATH . 'traits/FileTrait.php';

class TradingModel extends CI_Model{
	use FileTrait;
	public $table = 'agente_compra_pedido_cabecera';
    public $table_cliente = 'entidad';
	public $table_agente_compra_correlativo = 'agente_compra_correlativo';
	public $table_pais = 'pais';
	public $table_payments = 'payments_agente_compra_pedido';
	public $table_usuario_intero = 'usuario_interno_empresa_china';
	private $table_agente_compra_excel="agente_compra_order_excel";
	private $table_agente_compra_excel_detalle="agente_compra_order_excel_detail";
	private $jefeChinaPrivilegio = 5;
    private $personalChinaPrivilegio = 2;
    private $personalPeruPrivilegio = 1;
    private $almacenPrivilegio = 6;
	private $table_agente_compra_excel_files="agente_compra_order_excel_files";
		public $order = array('ID_Pedido_Cabecera' => 'desc');
	public function __construct(){
		parent::__construct();
	}
	public function getAlmacen(){

		try{
			$filtroEstado = $this->input->post('Filtro_Estado');
			$this->db->select('CORRE.Fe_Month, Nu_Estado_China,' . $this->table . '.*, P.No_Pais,
		CLI.No_Entidad, CLI.Nu_Documento_Identidad,CLI.No_Contacto,
        (select count(*) from payments_agente_compra_pedido where id_pedido = ' . $this->table . '.ID_Pedido_Cabecera and id_type_payment=2) as total_pagos,
        if((select count(*) from payments_agente_compra_pedido where id_pedido = ' . $this->table . '.ID_Pedido_Cabecera and id_type_payment=3)>0,1,0) as is_closed ,
		CLI.No_Contacto, CLI.Nu_Celular_Contacto, CLI.Txt_Email_Contacto')
            ->from($this->table)
            ->join($this->table_pais . ' AS P', 'P.ID_Pais = ' . $this->table . '.ID_Pais', 'join')
            ->join($this->table_cliente . ' AS CLI', 'CLI.ID_Entidad = ' . $this->table . '.ID_Entidad', 'join')
            ->join($this->table_agente_compra_correlativo . ' AS CORRE', 'CORRE.ID_Agente_Compra_Correlativo = ' . $this->table . '.ID_Agente_Compra_Correlativo', 'left')
        // ->join($this->table_payments . ' AS PAY', 'PAY.id_pedido = ' . $this->table . '.ID_Pedido_Cabecera', 'left')
        //->join($this->table_usuario_intero . ' AS USRCHINA', 'USRCHINA.ID_Usuario  = ' . $this->table . '.ID_Usuario_Interno_Empresa_China', 'left')
            ->where($this->table . '.ID_Empresa', $this->user->ID_Empresa)
            ->where_in($this->table . '.Nu_Estado_General', array(4));
	
			if(!empty($this->input->post('Filtro_Fe_Inicio')) && !empty($this->input->post('Filtro_Fe_Fin'))){
				$this->db->where("Fe_Emision_OC_Aprobada BETWEEN '" . $this->input->post('Filtro_Fe_Inicio') . "' AND '" . $this->input->post('Filtro_Fe_Fin') . "'");
					}	
		if(!empty($filtroEstado)){
				if($filtroEstado!="0"){
					$this->db->where("estado_almacen",$filtroEstado);
				}
			}
	
		return $this->db->get()->result();
		}
		catch(Exception $e){
			echo $e->getMessage();
			return $e->getMessage();
		}
		
	}
	public function getInspeccion($idOrder){
		$query = $this->db->select("*")
		->from($this->table_agente_compra_excel)
		->join($this->table_agente_compra_excel_detalle, " agente_compra_order_excel.id = agente_compra_order_excel_detail.order_excel_id ", "join")
		->join ($this->table," agente_compra_order_excel.order_id = agente_compra_pedido_cabecera.ID_Pedido_Cabecera","join")
		->where("order_id", $idOrder)
		->order_by("agente_compra_order_excel.id", "desc")
		->limit(1);

		// Para depuración, imprime la consulta SQL generada

		return $query->get()->result();
	}
	public function getInspeccionFiles($idExcel){
		$query = $this->db->select("id,id_order_excel,file_name as name, file_path as path,file_type as type,file_size as size,created_at,file_path thumbnail")
		->from($this->table_agente_compra_excel_files)
		->where("id_order_excel", $idExcel)
		->order_by("id", "desc");
		return $query->get()->result();
	}
	public function uploadInspeccionFiles($idExcel,$idDetalle,$idOrder,$file){
		$this->setAllowedExtensionsImagesOfficeFiles();
        $this->maxFileSize = 200240;
		$fileUrl=$this->uploadSingleFile([
			"name"=>$file["name"],
			"type"=>$file["type"],
			"tmp_name"=>$file["tmp_name"],
			"error"=>$file["error"],
			"size"=>$file["size"],
		],"assets/images/");
		$data=[
			"id_order_excel"=>$idExcel,
			"file_name"=>$file["name"],
			"file_path"=>$fileUrl,
			"file_type"=>$file["type"],
			"file_size"=>$file["size"],
			"created_at"=>date("Y-m-d H:i:s"),
			"updated_at"=>date("Y-m-d H:i:s"),
		];
		$this->db->insert($this->table_agente_compra_excel_files,$data);
		$id=$this->db->insert_id();
		$this->db->where("id",$idDetalle)->update($this->table_agente_compra_excel_detalle,["almacen_estado"=>"COMPLETADO"]);
		$this->db->where("ID_Pedido_Cabecera",$idOrder)->update($this->table,["estado_almacen"=>"COMPLETADO"]);
		$dataToReturn=[
			"id"=>$id,
			"path"=>$fileUrl,
			"name"=>$file["name"],
			"type"=>$file["type"],
			"size"=>$file["size"],
			"thumbnail"=>$fileUrl,
			"lastModified"=>$data["created_at"],
		];
		return $dataToReturn;
	}
	public function deleteInspeccionFiles($id){
		$file=$this->db->select("file_path")->from($this->table_agente_compra_excel_files)->where("id",$id)->get()->row();
		if($file){
			$this->deleteFile($file->file_path);
			$this->db->where("id",$id)->delete($this->table_agente_compra_excel_files);
			return true;
		}
		return false;
	}
	public function deleteFile($path){
		if(file_exists($path)){
			unlink($path);
			return true;
		}
		return false;
	}
    public function saveInspection($data,$idOrder){
		$isChange=false;
		foreach($data as $row){
			if($row[0]['value']!=0|| $row[1]['value']!=0|| $row[2]['value']!=0){
				$isChange=true;
			
			}
			$dataToUpdate=[
				$row[0]['key']=>$row[0]['value'],
				$row[1]['key']=>$row[1]['value'],
				$row[2]['key']=>$row[2]['value'],
				$row[3]['key']=>$row[3]['value'],
			];
			$this->db->where("id",$row[0]['id'])->update($this->table_agente_compra_excel_detalle,$dataToUpdate);
			if($isChange){
				//get current almacen estado
				$almacenEstado=$this->db->select("almacen_estado")->from($this->table_agente_compra_excel_detalle)->where("id",$row[0]['id'])->get()->row();
				if($almacenEstado->almacen_estado=="PENDIENTE"){
					$this->db->where("id",$row[0]['id'])->update($this->table_agente_compra_excel_detalle,["almacen_estado"=>"RECIBIDO"]);
				}
			}
		}
		//get current estado almacen
		$estadoAlmacen=$this->db->select("estado_almacen")->from($this->table)->where("ID_Pedido_Cabecera",$idOrder)->get()->row();
		if($estadoAlmacen->estado_almacen=="PENDIENTE"){
			$this->db->where("ID_Pedido_Cabecera",$idOrder)->update($this->table,["estado_almacen"=>"RECIBIENDO"]);
		}		
		return ["message"=>"Datos actualizados correctamente"];
	}
}