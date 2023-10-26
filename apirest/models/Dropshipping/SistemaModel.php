<?php
class SistemaModel extends CI_Model{
    var $table = 'configuracion';
    var $table_empresa = 'empresa';
    
    var $column_order = array('No_Empresa', '', 'No_Tienda_Lae_Shop', 'Nu_Celular_Lae_Shop', 'Nu_Celular_Whatsapp_Lae_Shop', 'Txt_Email_Lae_Shop', 'Txt_Descripcion_Lae_Shop');
    var $column_search = array('');
    var $order = array('No_Empresa' => 'asc');
    
    private $upload_path = '../assets/images/logos';
    
    public function __construct(){
        parent::__construct();
    }
    
    public function _get_datatables_query(){
        if ( $this->user->No_Usuario != 'root' ){
            $this->column_order = array('', 'No_Tienda_Lae_Shop', 'Nu_Celular_Lae_Shop', 'Nu_Celular_Whatsapp_Lae_Shop', 'Txt_Email_Lae_Shop', 'Txt_Descripcion_Lae_Shop');
        }

        $this->db->select('EMP.ID_Empresa, EMP.No_Empresa, ID_Configuracion, Txt_Url_Logo_Lae_Shop, No_Tienda_Lae_Shop, Nu_Celular_Lae_Shop, Nu_Celular_Whatsapp_Lae_Shop, Txt_Email_Lae_Shop, Txt_Descripcion_Lae_Shop, Nu_Version_Imagen')
        ->from($this->table)
        ->join($this->table_empresa . ' AS EMP', 'EMP.ID_Empresa = ' . $this->table . '.ID_Empresa', 'join');
        
        if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        }

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_datatables(){
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    
    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_by_id($ID){
        $this->db->select('C.*, STV.No_Subdominio_Tienda_Virtual,STV.ID_Subdominio_Tienda_Virtual,concat_ws(".",STV.No_Subdominio_Tienda_Virtual,STV.No_Dominio_Tienda_Virtual) AS DominioActual');
        $this->db->from($this->table . ' AS C');
        $this->db->join('subdominio_tienda_virtual AS STV', 'STV.ID_Empresa = C.ID_Empresa', 'join');
        $this->db->where('ID_Configuracion',$ID);
        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->row();
    }

    public function ValidarDominioTienda(){

        $DominioNuevo   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual"))).".compramaz.com";
        $this->db->select("*,concat_ws('.', No_Subdominio_Tienda_Virtual, No_Dominio_Tienda_Virtual)  as Dominio");
        $this->db->from("subdominio_tienda_virtual");
        $this->db->where('ID_Subdominio_Tienda_Virtual',$this->input->post("t"));
        $query  = $this->db->get();
        $row    = $query->row();
        
        // echo "\nnuevo:".$DominioNuevo;
        //  echo "\nviejo:".$row->Dominio;
        //echo $this->db->last_query();
         if($DominioNuevo==$row->Dominio){
         //echo "1XXXX";
            return "true";
        }else{
           // echo "2XXXX";
        }

        $this->db->select("*,concat_ws('.', No_Subdominio_Tienda_Virtual, No_Dominio_Tienda_Virtual)  as Dominio");
        $this->db->from("subdominio_tienda_virtual");
 
        $this->db->where('ID_Subdominio_Tienda_Virtual !=',$this->input->post("t"));
        $this->db->like("CONCAT_WS('.',`No_Subdominio_Tienda_Virtual`,No_Dominio_Tienda_Virtual)", $DominioNuevo, 'none'); 
        $query  = $this->db->get();
        $row    = $query->row();

        //echo $this->db->last_query();
         if($row)
            return "false";
        else 
            return "true";
        

    }

    public function ValidarVencimientoPago(){
        
        $usuario = $this->getUsuarioDeudor();

        for($i=0;$i<count($usuario);$i++){
        
            $data = array( 'Nu_Estado_Pago_Sistema_Laeshop' => 0);
            $this->db->where('ID_Almacen', $usuario[$i]->ID_Almacen);
            $this->db->update('almacen', $data);
            $this->ActualizarTienda(array(
                                    "Dominio"=>$usuario[$i]->Dominio,
                                    "Token"=>$usuario[$i]->Txt_Token_Lae_Shop,
                                    "Estatus"=>0
                                    ));
        }
    }

    public function ReporteBorrado(){

        try {
             $this->BorradoArchivo->setCarpeta("17bfb21fcd36328bf87bf1636da09913");
        } catch (Exception $e) {
            print_r($e->getMessage());
            exit();
        }

        $this->db->select('*');
        $this->db->from("reporte");
        $this->db->where("Fe_Creacion <=","date_add(NOW(), INTERVAL -7 DAY)",FALSE);
        $this->db->where_not_in('ID_Estatus', array(3));
        $query = $this->db->get();
        $rows = $query->result();
        
        for($i=0;$i<count($rows);$i++){

            try {
                
                 $this->BorradoArchivo->BorrarArchivo($rows[$i]->Txt_Archivo);

            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }

        if(count($rows)>0){
            $this->db->set('ID_Estatus', '3', FALSE);
            $this->db->where("Fe_Creacion <=","date_add(NOW(), INTERVAL -7 DAY)",FALSE);
            $this->db->where_not_in('ID_Estatus', array(3));
            $this->db->update('reporte');
        }
    } 

    public function ListadoReporteBorrado(){

        try {
             $this->BorradoArchivo->setCarpeta("17bfb21fcd36328bf87bf1636da09913");
        } catch (Exception $e) {
            print_r($e->getMessage());
            exit();
        }

        $this->db->select('*');
        $this->db->from("reporte");
        $this->db->where("Fe_Creacion <=","date_add(NOW(), INTERVAL -7 DAY)",FALSE);
        $this->db->where_not_in('ID_Estatus', array(3));
        $query = $this->db->get();
        $rows = $query->result();
        
        for($i=0;$i<count($rows);$i++){

            try {
                
                 echo "f: ".$this->BorradoArchivo->getFileLocation($rows[$i]->Txt_Archivo)."\n";

            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }

        echo "\nCantidad Total: ".count($rows)."\n";
    } 

    public function getUsuarioDeudor(){

        $this->db->select('AL.*,O.ID_Empresa,CONCAT(SUTI.No_Subdominio_Tienda_Virtual, ".", SUTI.No_Dominio_Tienda_Virtual) Dominio');
        $this->db->from("almacen AL");
        $this->db->join('organizacion O', 'O.ID_Organizacion=AL.ID_Organizacion', 'join');
        $this->db->join('subdominio_tienda_virtual SUTI', 'SUTI.ID_Empresa=O.ID_Empresa', 'join');
        $this->db->where('AL.Txt_Token_Lae_Shop != ',"");
        $this->db->where('DATEDIFF(NOW(),AL.Fe_Vencimiento_Laeshop) >=',7);
        $this->db->where('AL.Nu_Estado_Pago_Sistema_Laeshop',1);
        $query = $this->db->get();
        // print_r($this->db->last_query());
        $rows = $query->result();
        //print_r($rows);
        return $rows;
    }


    public function ActualizarTienda($config){

        $FileName    =   md5($config["Dominio"]);
        $this->ConfiguracionTienda->Borrado($FileName);
        $this->ConfiguracionTienda->setDebug(true);
        $this->ConfiguracionTienda->Carga($FileName)
        ->Constante('TIENDA_DOMINIO',$config["Dominio"])
        ->Constante("TIENDA_TOKEN",$config["Token"])
        ->Constante("TIENDA_IDTIENDAFILE",$FileName)
        ->Constante("TIENDA_ESTATUS",$config["Estatus"])
        ->Escribir();

    }
    
    public function actualizarSistema($where, $data){

        if ( $this->db->update($this->table, $data, $where) > 0 ) {
            if ($data['Nu_Activar_Precio_Centralizado_Laeshop']==1) {//solo si activa cargamos masivamente preci producto
                $this->db->query("UPDATE producto SET Ss_Precio_Ecommerce_Online_Regular=Ss_Precio WHERE ID_Empresa=" . $data['ID_Empresa']);
            }

            /* TOUR TIENDA VIRTUAL */           
            $where_tour = array('ID_Empresa' => $data['ID_Empresa'], 'Nu_ID_Interno' => 1);
            //validamos que si complete los siguientes datos
            if (!empty($data['No_Tienda_Lae_Shop']) && !empty($data['Txt_Email_Lae_Shop']) && !empty($data['Nu_Celular_Whatsapp_Lae_Shop']) ) {
                //Cambiar estado a completado para el tour
                $data_tour = array('Nu_Estado_Proceso' => 1);
            } else {
                //Cambiar estado a completado para el tour
                $data_tour = array('Nu_Estado_Proceso' => 0);
            }
            $this->db->update('tour_tienda_virtual', $data_tour, $where_tour);
            /* END TOUR TIENDA VIRTUAL */

            //Creacion de Archivo para la tienda en caso de cambio de subdominio
            $DominioNuevo   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual"))).".compramaz.com";        
            $this->db->select("*,concat_ws('.', No_Subdominio_Tienda_Virtual, No_Dominio_Tienda_Virtual)  as Dominio");
            $this->db->from("subdominio_tienda_virtual");
            $this->db->where('ID_Empresa',$this->input->post("ID_Empresa"));
            $query  = $this->db->get();
            $row    = $query->row();

            if($DominioNuevo!=$row->Dominio){
                $this->db->select("SUB.*, concat_ws('.',SUB.No_Subdominio_Tienda_Virtual, SUB.No_Dominio_Tienda_Virtual ) AS Dominio, AL.Nu_Estado_Pago_Sistema_Laeshop, AL.Txt_Token_Lae_Shop");
                $this->db->from("subdominio_tienda_virtual SUB");
                $this->db->join('organizacion O', 'O.ID_Empresa = SUB.ID_Empresa', 'join');
                $this->db->join('almacen AL', 'AL.ID_Organizacion = O.ID_Organizacion', 'join');
                $this->db->where('SUB.ID_Empresa',$this->input->post("ID_Empresa"));
                $this->db->where('AL.Txt_Token_Lae_Shop != ',"");
                $query  = $this->db->get();
                $row    = $query->row();
                 try {
                        $FileName    =   md5($DominioNuevo);
                        $this->ConfiguracionTienda->setDebug(false);
                        $this->ConfiguracionTienda->Carga($FileName)
                        ->Constante('TIENDA_DOMINIO',$DominioNuevo)
                        ->Constante("TIENDA_TOKEN",$row->Txt_Token_Lae_Shop)
                        ->Constante("TIENDA_IDTIENDAFILE",$FileName)
                        ->Constante("TIENDA_ESTATUS",$row->Nu_Estado_Pago_Sistema_Laeshop)
                        ->Escribir();
                        $this->ConfiguracionTienda->Borrado(md5($row->Dominio));
                    }
                    catch (Exception $e) {
                       return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
                    }

                 //Actualizar la tabla - sub dominio
                $No_Subdominio_Tienda_Virtual   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual")));
                $where_subdominio = array('ID_Empresa' => $data['ID_Empresa']);
                $data_subdominio = array('No_Subdominio_Tienda_Virtual' => $No_Subdominio_Tienda_Virtual);
                $this->db->update('subdominio_tienda_virtual', $data_subdominio, $where_subdominio);
            
            }

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Registro modificado');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }
}
