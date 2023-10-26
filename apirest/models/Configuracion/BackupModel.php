<?php
class BackupModel extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->dbutil();
		$this->load->helper('file');
	}

	public function listarBackups(){
		$path = FCPATH . 'backups';

		if(!is_dir($path)){
			$response = array("response" => "danger", "message" => "No existe la carpeta backups en la ráiz del proyecto, agregue una carpeta backups en " . FCPATH);
		} else {
			$copias = array();
			foreach(scandir($path) as $k => $p){
				if($p != '..' && $p != '.'){
					$fecha = explode('-', $p);

					$copias[] = (object) array(
						'Archivo' => $p,
						'Fecha'   => DateFormat(str_replace('.sql', '', $fecha[1]), 5)
					);
				}
			}
			$response = array("response" => "success", "result" => (object)$copias);
		}
		return $response;
	}

	public function generarBackup(){
		$this->benchmark->mark('code_start');
		$backup = $this->dbutil->backup(array(
			'format' => 'txt'
		));

		write_file('backups/backup-' . date('YmdHis') . '.sql', $backup);
		$this->benchmark->mark('code_end');
		
		$response = array("response" => "success", "message" => $this->benchmark->elapsed_time('code_start', 'code_end'));
		return $response;
	}
}
