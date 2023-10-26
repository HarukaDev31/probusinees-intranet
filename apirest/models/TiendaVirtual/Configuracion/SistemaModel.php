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
        
        /*
        if ( $this->user->No_Usuario == 'root' ){
            if( $this->input->post('filtro_empresa') )
                $this->db->where('EMP.ID_Empresa', $this->input->post('filtro_empresa'));
        } else {
        */
            $this->db->where('EMP.ID_Empresa', $this->empresa->ID_Empresa);
        //}

        if(isset($_POST['order'])){
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_datatables(){
        $this->_get_datatables_query();
        if(isset($_POST['length']) && $_POST['length'] != -1)
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
        $this->db->select('C.*, STV.No_Subdominio_Tienda_Virtual,STV.ID_Subdominio_Tienda_Virtual,concat_ws(".",STV.No_Subdominio_Tienda_Virtual,STV.No_Dominio_Tienda_Virtual) AS DominioActual,STV.No_Dominio_Tienda_Virtual, EMP.No_Dominio_Externo, EMP.Txt_Llave_Externa');
        $this->db->from($this->table . ' AS C');
        $this->db->join('subdominio_tienda_virtual AS STV', 'STV.ID_Empresa = C.ID_Empresa', 'join');
        $this->db->join('empresa AS EMP', 'EMP.ID_Empresa = C.ID_Empresa', 'join');
        $this->db->where('C.ID_Configuracion',$ID);
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
        $this->db->where('DATEDIFF(NOW(),AL.Fe_Vencimiento_Laeshop) >=',1);
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
            if($this->empresa->Nu_Lae_Gestion==0 && $this->empresa->Nu_Lae_Shop==1){
                $this->db->query("UPDATE empresa SET No_Empresa='". limpiarCaracteresEspeciales($data['No_Tienda_Lae_Shop']) ."' WHERE ID_Empresa=" . $data['ID_Empresa']);
            }

            if ($data['Nu_Activar_Precio_Centralizado_Laeshop']==1) {//solo si activa cargamos masivamente precio producto del servicio de sistema
                $this->db->query("UPDATE producto SET Ss_Precio_Ecommerce_Online_Regular=Ss_Precio, Ss_Precio_Ecommerce_Online=0 WHERE ID_Empresa=" . $data['ID_Empresa']);
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

            //SE DESASIGNAN LOS ALMACENES PARA FACEBBOK SHOP DE LA ORGANIZACION;
            $this->db->set('almacen.Nu_Asignado_Facebook_Lae_Shop', 0);
            $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
            $this->db->update('almacen');
            $this->session->userdata['almacen']->Nu_Asignado_Facebook_Lae_Shop = 0;
            //SE VERIFICA SI ESTAN LOS DATOS COMPLESTOS DE FACEBOOK PIXEL PARA INTEGRAR FACEBOOK SHOP
            if(!empty($data['Txt_Facebook_Pixel_Lae_Shop']) && !empty($data['Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop'])) {
                //SE ASGINA EL ALMACEN QUE TENGA EL TOKEN DE LA TIENDA
                $this->db->set('almacen.Nu_Asignado_Facebook_Lae_Shop', 1);
                $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
                $this->db->where('almacen.Txt_Token_Lae_Shop IS NOT NULL');
                $this->db->update('almacen');
                //SE BUSCA EL ID ALMACEN ASIGNADO A FACEBOOK SHOP
                $this->db->select("ID_Almacen");
                $this->db->from('almacen');
                $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
                $this->db->where('almacen.Nu_Asignado_Facebook_Lae_Shop', 1);
                $query = $this->db->get();
                $arrAlmacen = $query->row();
                if($arrAlmacen->ID_Almacen == $this->session->userdata['almacen']->ID_Almacen) {
                    $this->session->userdata['almacen']->Nu_Asignado_Facebook_Lae_Shop = 1;
                }
                //SE CREA EL ARCHIVO DE FACEBOOK SHOP PARA LA SINCRONIZACION DE PRODUCTOS 
                $this->crearArchvioFacebookLaeShop($data['Txt_Facebook_Pixel_Lae_Shop']);
            }

            //SE DESASIGNAN LOS ALMACENES PARA GOOGLE SHOPPING DE LA ORGANIZACION;
            $this->db->set('almacen.Nu_Asignado_Google_Shopping_Lae_Shop', 0);
            $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
            $this->db->update('almacen');
            $this->session->userdata['almacen']->Nu_Asignado_Google_Shopping_Lae_Shop = 0;
            //SE VERIFICA SI ESTA LA VERIFICACION DE GOOGLE SHOPPING
            if(!empty($data['Txt_Google_Shopping_Dominio_Lae_Shop'])) {
                //SE ASGINA EL ALMACEN QUE TENGA EL TOKEN DE LA TIENDA
                $this->db->set('almacen.Nu_Asignado_Google_Shopping_Lae_Shop', 1);
                $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
                $this->db->where('almacen.Txt_Token_Lae_Shop IS NOT NULL');
                $this->db->update('almacen');
                //SE BUSCA EL ID ALMACEN ASIGNADO A GOOGLE SHOPPING
                $this->db->select("ID_Almacen");
                $this->db->from('almacen');
                $this->db->where('almacen.ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
                $this->db->where('almacen.Nu_Asignado_Google_Shopping_Lae_Shop', 1);
                $query = $this->db->get();
                $arrAlmacen = $query->row();
                if($arrAlmacen->ID_Almacen == $this->session->userdata['almacen']->ID_Almacen) {
                    $this->session->userdata['almacen']->Nu_Asignado_Google_Shopping_Lae_Shop = 1;
                }
                //SE CREA EL ARCHIVO DE GOOGLE SHOPPING SHOP PARA LA SINCRONIZACION DE PRODUCTOS 
                $this->crearArchvioGoogleLaeShop($data['Txt_Google_Shopping_Dominio_Lae_Shop']);
            }

            //Creacion de Archivo para la tienda en caso de cambio de subdominio
            /*$DominioNuevo   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual"))).".compramaz.com";        
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
            
            }*/

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Datos actualizado de tienda');
        }
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
    }
    
    public function actualizarEmpresaShopify($where, $data){
        if ( $this->db->update('empresa', $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'empresa actualizada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error empresa actualizada');
    }

    public function actualizarVersionImagen($where, $data){
        if ( $this->db->update($this->table, $data, $where) > 0 )
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Version de imagen modificada');
        return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error Version de imagen modificada');
    }

    //LOS CAMPOS QUE LLEGAN A ESTE METODO SON
	//ID_Empresa = id de la empresa
    //ID_Subdominio_Tienda_Virtual = id de la tabla subdominio_tienda_virtual
	//Nu_Tipo_Tienda = tipo de registro (1: subdominio, 3: dominio)
	//No_Subdominio_Tienda_Virtual = campo con el valor del subdominio (este campo existe solo si el valor de Nu_Tipo_Tienda es 1)
	//No_Dominio_Tienda_Virtual = campo con el valor del dominio (este campo existe solo si el valor de Nu_Tipo_Tienda es 3)
    public function actualizarSistemaDominio($where, $data){

        if($data["Nu_Tipo_Tienda"] == 1) { //SUBDOMINIO
            return $this->actualizarSubdominio();
        } elseif($data["Nu_Tipo_Tienda"] == 3) { //DOMINIO
            return $this->actualizarDominio();
        }        
    }

    public function actualizarSubdominio() {
        //AQUI SE ACTUALIZA EL SUBDOMINIO
        //print_r($this->input->post());
        //Creacion de Archivo para la tienda en caso de cambio de subdominio
        /*
            $DominioNuevo   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual"))).".compramaz.com";        
            $this->db->select("*,concat_ws('.', No_Subdominio_Tienda_Virtual, No_Dominio_Tienda_Virtual)  as Dominio");
            $this->db->from("subdominio_tienda_virtual");
            $this->db->where('ID_Empresa',$this->input->post("ID_Empresa"));
            $query  = $this->db->get();
            $row    = $query->row();
        */

        $sSubDominio=strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual")) . '.' . trim($this->input->post("No_Dominio_Tienda_Virtual")));
        if( $this->db->query("SELECT COUNT(*) existe FROM subdominio_tienda_virtual WHERE CONCAT(No_Subdominio_Tienda_Virtual, '.', No_Dominio_Tienda_Virtual)='" . $sSubDominio . "' LIMIT 1")->row()->existe > 0 ){
            return array('status' => 'warning', 'style_modal' => 'modal-warning', 'message' => 'Subdominio: ' . $sSubDominio . ', ya existe');
        } else{
            $this->db->select("SUB.*, concat_ws('.',SUB.No_Subdominio_Tienda_Virtual, SUB.No_Dominio_Tienda_Virtual ) AS Dominio, AL.Nu_Estado_Pago_Sistema_Laeshop, AL.Txt_Token_Lae_Shop");
            $this->db->from("subdominio_tienda_virtual SUB");
            $this->db->join('organizacion O', 'O.ID_Empresa = SUB.ID_Empresa', 'join');
            $this->db->join('almacen AL', 'AL.ID_Organizacion = O.ID_Organizacion', 'join');
            $this->db->where('SUB.ID_Empresa',$this->input->post("ID_Empresa"));
            $this->db->where('AL.Txt_Token_Lae_Shop != ',"");
            $query  = $this->db->get();
            $row    = $query->row();

            $DominioNuevo   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual"))).".compramaz.com";
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
            } catch (Exception $e) {
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
            }

            //Actualizar la tabla - sub dominio
            $No_Subdominio_Tienda_Virtual   = strtolower(trim($this->input->post("No_Subdominio_Tienda_Virtual")));
            $where_subdominio = array('ID_Empresa' => $this->input->post("ID_Empresa"));
            $data_subdominio = array('No_Subdominio_Tienda_Virtual' => $No_Subdominio_Tienda_Virtual,'No_Dominio_Tienda_Virtual'=>'compramaz.com','Nu_Tipo_Tienda'=>1);
            $this->db->update('subdominio_tienda_virtual', $data_subdominio, $where_subdominio);

            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Nuevo Subdominio creado correctamente');
        }
    }

    public function actualizarDominio() {
        //AQUI SE ACTUALIZA EL DOMINIO

        $data = array(
                'No_Dominio_Tienda_Virtual' => $this->input->post("No_Dominio_Tienda_Virtual"),
                'ID_Empresa' => $this->input->post("ID_Empresa"),
                'ID_Subdominio_Tienda_Virtual' => $this->input->post("ID_Subdominio_Tienda_Virtual")
        );

        if($this->db->insert('dominio_cron', $data)){

            // $where_subdominio = array('ID_Empresa' => $this->input->post("ID_Empresa"));
            // $data_subdominio = array('Nu_Tipo_Tienda'=>3 );
            // $this->db->update('subdominio_tienda_virtual', $data_subdominio, $where_subdominio);
            return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Dominio modificado, La tarea está en proceso y puede tardar hasta 24 horas en completarse');
        }
        else
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar Dominio');
    }

    function CreacionDominio(){
        $cmd            = "";
        $output         = [];
        $DataDominio    = $this->getDataDominio();
	    //print_r( $DataDominio);
        for($i=0;$i<count($DataDominio);$i++){

            $this->db->start_cache();
            $this->db->flush_cache();
            $this->db->select("SUB.*, concat_ws('.',SUB.No_Subdominio_Tienda_Virtual, SUB.No_Dominio_Tienda_Virtual ) AS Dominio, AL.Nu_Estado_Pago_Sistema_Laeshop, AL.Txt_Token_Lae_Shop");
            $this->db->from("subdominio_tienda_virtual SUB");
            $this->db->join('organizacion O', 'O.ID_Empresa = SUB.ID_Empresa', 'join');
            $this->db->join('almacen AL', 'AL.ID_Organizacion = O.ID_Organizacion', 'join');
            $this->db->where('SUB.ID_Empresa',$DataDominio[$i]->ID_Empresa);
            $this->db->where('AL.Txt_Token_Lae_Shop != ',"");      
            $query  = $this->db->get();
            $row    = $query->row();
            //echo $this->db->last_query();
            $this->db->flush_cache();
            if($row->No_Dominio_Tienda_Virtual!=$DataDominio[$i]->No_Dominio_Tienda_Virtual){

                $cmd = "/usr/sbin/virtualmin create-domain --domain ".$DataDominio[$i]->No_Dominio_Tienda_Virtual." --pass ".$this->generarPassword(20)." --unix --dir --webmin --web --ssl --letsencrypt --template \"Wildcard(compramaz) php7.4 git\" ";
                //Ejecutar el comando en el shell y capturar la salida y el código de salida
                $output = array();
                $return_var = 0;
                exec($cmd, $output, $return_var);
		        //echo implode("\n",$output);

                // Verificar si se produjo un error
                if ($return_var !== 1) {
                 
                     try {
                            $FileName    =   md5($DataDominio[$i]->No_Dominio_Tienda_Virtual);
                            $this->ConfiguracionTienda->setDebug(true);
                            $this->ConfiguracionTienda->Carga($FileName)
                            ->Constante('TIENDA_DOMINIO',$DataDominio[$i]->No_Dominio_Tienda_Virtual)
                            ->Constante("TIENDA_TOKEN",$row->Txt_Token_Lae_Shop)
                            ->Constante("TIENDA_IDTIENDAFILE",$FileName)
                            ->Constante("TIENDA_ESTATUS",$row->Nu_Estado_Pago_Sistema_Laeshop)
                            ->Escribir();
                             $this->ConfiguracionTienda->Borrado(md5($row->No_Dominio_Tienda_Virtual));
                        }
                        catch (Exception $e) {
                           return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Error al modificar');
                        }
                     $this->db->flush_cache();
                     //Actualizar la tabla - sub dominio
                    $No_Subdominio_Tienda_Virtual   = strtolower(trim($DataDominio[$i]->No_Dominio_Tienda_Virtual));
                    $where_subdominio = array('ID_Subdominio_Tienda_Virtual' => $DataDominio[$i]->ID_Subdominio_Tienda_Virtual);
                    $data_subdominio = array('No_Dominio_Tienda_Virtual' => $No_Subdominio_Tienda_Virtual,
                                              "No_Subdominio_Tienda_Virtual"=>"",'Nu_Tipo_Tienda'=>3);

                    $this->db->update('subdominio_tienda_virtual', $data_subdominio, $where_subdominio);
                    // print_r($this->db->last_query());
             
                    $this->db->flush_cache();
                    $this->db->set('Nu_Estado', 2, FALSE);
                    $this->db->set('Txt_Cmd', $cmd);
                    $this->db->set('Txt_Respuesta',implode("\n", $output));
                    $this->db->set('Fe_Registro_Fin', date("Y-m-d H:i:s"));
                    $this->db->where("ID_Dominio_Cron",$DataDominio[$i]->ID_Dominio_Cron);
                    $this->db->update('dominio_cron');
                 }else{
                    $this->db->flush_cache();
                    $this->db->set('Nu_Estado', 3, FALSE);
                    $this->db->set('Txt_Cmd', $cmd);
                    $this->db->set('Txt_Respuesta',implode("\n", $output));
                    $this->db->set('Fe_Registro_Fin', date("Y-m-d H:i:s"));
                    $this->db->where("ID_Dominio_Cron",$DataDominio[$i]->ID_Dominio_Cron);
                    $this->db->update('dominio_cron');
                 }

            } else {
                $this->db->flush_cache();
                $this->db->set('Nu_Estado', 3, FALSE);
                $this->db->set('Txt_Cmd', $cmd);
                $this->db->set('Txt_Respuesta',implode("\n", $output));
                $this->db->set('Fe_Registro_Fin', date("Y-m-d H:i:s"));
                $this->db->where("ID_Dominio_Cron",$DataDominio[$i]->ID_Dominio_Cron);
                $this->db->update('dominio_cron');
            }
        
        }

    }

    function generarPassword($longitud) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num_caracteres = strlen($caracteres);
        $password = '';
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[rand(0, $num_caracteres - 1)];
        }
        return $password;
    }

    function getDataDominio(){
        $this->db->select("*");
        $this->db->from("dominio_cron");
        $this->db->where('Nu_Estado',0);
        $query = $this->db->get();
        return $query->result();

    }

    function getDataCatalogoCron(){
        $this->db->select("*");
        $this->db->from("catalogo_producto_cron");
        $this->db->where('Nu_Estado',0);
        $query = $this->db->get();
        return $query->result();
    }

     function getProductoCatalogo($ID_Empresa){
        $this->db->select("ID_Producto,No_Producto,FORMAT(Ss_Precio,2) Ss_Precio,IF(No_Imagen_Item IS NULL or No_Imagen_Item = '', 'https://laesystems.com/principal/assets/img/imagen_nodisponible.png', No_Imagen_Item) No_Imagen_Item");
        $this->db->from("producto");
        $this->db->where('Nu_Estado',1);
        $this->db->where('ID_Empresa',$ID_Empresa);
        //$this->db->limit(50);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result();
    }

    function getConfiguracion($ID_Empresa){
         $this->db->select('conf.ID_Empresa,conf.ID_Configuracion,conf.Txt_Url_Logo_Lae_Shop,
                            conf.Nu_Height_Logo_Ticket,conf.Nu_Width_Logo_Ticket,
                            concat("https://",stv.No_Dominio_Tienda_Virtual,"/libro") No_Dominio_Tienda_Virtual,
                            concat("https://",stv.No_Subdominio_Tienda_Virtual,".", No_Dominio_Tienda_Virtual,"/libro") No_Subdominio_Tienda_Virtual,
                            stv.Nu_Tipo_Tienda,
                            conf.No_Estado_Catalogo_Lae_Shop ');
        $this->db->from("configuracion conf");
         $this->db->join('subdominio_tienda_virtual stv', 'stv.ID_Empresa =conf.ID_Empresa ', 'left');
        $this->db->where('conf.ID_Empresa',$ID_Empresa);
        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->row();
    }

    function CatalogoCron(){
        $CatalogoData = $this->getDataCatalogoCron();
       
        for($i=0;$i<count($CatalogoData);$i++){
            $this->GenerarCatalogos($CatalogoData[$i]);
        }

    }

    function GenerarCatalogos($CatalogoData){

        $productos       = $this->getProductoCatalogo($CatalogoData->ID_Empresa);
        $configuracion   = $this->getConfiguracion($CatalogoData->ID_Empresa);
        
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->set('Nu_Estado', 1, FALSE);
        $this->db->set('Fe_Registro_Inicio', date("Y-m-d G:i:s"));
        $this->db->where("ID_Catalogo_Producto_Cron",$CatalogoData->ID_Catalogo_Producto_Cron);
        $this->db->update('catalogo_producto_cron');

        $this->db->flush_cache();
        $this->db->set('No_Estado_Catalogo_Lae_Shop', 1, FALSE);
        $this->db->where("ID_Empresa",$configuracion->ID_Empresa);
        $this->db->update('configuracion');

        try {
            $mpdf = new \Mpdf\Mpdf(); 
            $header = '
             <table border="0" width="100%">
             <tr>
                <td style="width: 25%"> <img style="width:'.$configuracion->Nu_Width_Logo_Ticket.'px;height:'.$configuracion->Nu_Height_Logo_Ticket.'px" src="'.$configuracion->Txt_Url_Logo_Lae_Shop.'"> </td>
                <td style="width: 25%"></td>
                <td style="width: 25%"></td>
                <td style="width: 25%"><br><br>Click En Producto <br>para Comprar</td>
             </tr>
             </table><br><br>';
             $mpdf->WriteHTML($header);

            $mpdf->WriteHTML('<table border="0" width="100%"><tr>');
            
            $cuerpo = "";
            $contador = 1;
            for($i=0;$i<count($productos);$i++){

                if($configuracion->Nu_Tipo_Tienda==2 OR $configuracion->Nu_Tipo_Tienda==3)
                    $Dominio = $configuracion->No_Dominio_Tienda_Virtual."/";
                else
                    $Dominio = $configuracion->No_Subdominio_Tienda_Virtual."/";

                 $cuerpo ='<td>
                            <a href="'.$Dominio.$productos[$i]->ID_Producto.'">
                                <img style="width: 25%; height:140px; object-fit: cover;float: left;" src="'.$productos[$i]->No_Imagen_Item.'"/>
                            </a> <div style="font-size:10px;padding-top: 20px;"><br>'.$productos[$i]->No_Producto.'</div>
                                 <div style="font-size:8px;padding-bottom: 2px;"><strong>S/ '.$productos[$i]->Ss_Precio.'</strong></div>
                                 <br><br>
                            </td>'; 

                 $mpdf->WriteHTML($cuerpo);
            
             if($contador ==4){
                $mpdf->WriteHTML("</tr><tr>");
                $contador = 0;
             }

             $contador++;

            }
            $mpdf->WriteHTML('</table>');
            $mpdf->Output('/var/www/vhosts/laesystems/public_html/principal/catalogo/'.$configuracion->ID_Empresa.'.pdf', \Mpdf\Output\Destination::FILE);
            
            $this->db->flush_cache();
            $this->db->set('Nu_Estado', 2, FALSE);
            $this->db->set('Fe_Registro_Fin', date("Y-m-d G:i:s"));
            $this->db->where("ID_Catalogo_Producto_Cron",$CatalogoData->ID_Catalogo_Producto_Cron);
            $this->db->update('catalogo_producto_cron');

            $this->db->flush_cache();
            $this->db->set('No_Estado_Catalogo_Lae_Shop', 2, FALSE);
            $this->db->where("ID_Empresa",$configuracion->ID_Empresa);
            $this->db->update('configuracion');

        } catch (\Mpdf\MpdfException $e) {

            $this->db->flush_cache();
            $this->db->set('Nu_Estado', 3, FALSE);
            $this->db->set('Fe_Registro_Fin', date("Y-m-d G:i:s"));
            $this->db->set('Txt_Error', $e->getMessage());
            $this->db->where("ID_Catalogo_Producto_Cron",$CatalogoData->ID_Catalogo_Producto_Cron);
            $this->db->update('catalogo_producto_cron');

            $this->db->flush_cache();
            $this->db->set('No_Estado_Catalogo_Lae_Shop', 3, FALSE);
            $this->db->where("ID_Empresa",$configuracion->ID_Empresa);
            $this->db->update('configuracion');
       }
    }

	public function importarPaginas($ID, $sTipo){
        if($this->user->ID_Pais == 1) {//1=PERU
            if($sTipo=='terminos'){
                $Txt_Page_Landing_Terminos = $this->db->query("SELECT Txt_Page_Landing_Terminos FROM configuracion WHERE ID_Empresa = 1 LIMIT 1")->row()->Txt_Page_Landing_Terminos;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Terminos' => $Txt_Page_Landing_Terminos);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Terminos);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='privacidad'){
                $Txt_Page_Landing_Politica = $this->db->query("SELECT Txt_Page_Landing_Politica FROM configuracion WHERE ID_Empresa = 1 LIMIT 1")->row()->Txt_Page_Landing_Politica;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Politica' => $Txt_Page_Landing_Politica);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Politica);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='devoluciones'){
                $Txt_Page_Landing_Devolucion = $this->db->query("SELECT Txt_Page_Landing_Devolucion FROM configuracion WHERE ID_Empresa = 1 LIMIT 1")->row()->Txt_Page_Landing_Devolucion;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Devolucion' => $Txt_Page_Landing_Devolucion);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Devolucion);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='politica_envio'){
                $Txt_Page_Landing_Envio = $this->db->query("SELECT Txt_Page_Landing_Envio FROM configuracion WHERE ID_Empresa = 1 LIMIT 1")->row()->Txt_Page_Landing_Envio;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Envio' => $Txt_Page_Landing_Envio);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Envio);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else {
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay pagina');
            }
        } else if($this->user->ID_Pais == 2) {//2=MEXICO
            if($sTipo=='terminos'){
                $Txt_Page_Landing_Terminos = $this->db->query("SELECT Txt_Page_Landing_Terminos FROM configuracion WHERE ID_Empresa = 2827 LIMIT 1")->row()->Txt_Page_Landing_Terminos;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Terminos' => $Txt_Page_Landing_Terminos);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Terminos);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='privacidad'){
                $Txt_Page_Landing_Politica = $this->db->query("SELECT Txt_Page_Landing_Politica FROM configuracion WHERE ID_Empresa = 2827 LIMIT 1")->row()->Txt_Page_Landing_Politica;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Politica' => $Txt_Page_Landing_Politica);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Politica);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='devoluciones'){
                $Txt_Page_Landing_Devolucion = $this->db->query("SELECT Txt_Page_Landing_Devolucion FROM configuracion WHERE ID_Empresa = 2827 LIMIT 1")->row()->Txt_Page_Landing_Devolucion;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Devolucion' => $Txt_Page_Landing_Devolucion);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Devolucion);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else if($sTipo=='politica_envio'){
                $Txt_Page_Landing_Envio = $this->db->query("SELECT Txt_Page_Landing_Envio FROM configuracion WHERE ID_Empresa = 2827 LIMIT 1")->row()->Txt_Page_Landing_Envio;
                $where_update = array('ID_Configuracion' => $ID);
                $data_update = array( 'Txt_Page_Landing_Envio' => $Txt_Page_Landing_Envio);
                if ($this->db->update('configuracion', $data_update, $where_update) > 0)
                    return array('status' => 'success', 'style_modal' => 'modal-success', 'message' => 'Importado correctamente', 'data' => $Txt_Page_Landing_Envio);
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'Problemas al importar');
            } else {
                return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay pagina');
            }
        } else {
            return array('status' => 'error', 'style_modal' => 'modal-danger', 'message' => 'No hay país');
        }
	}

    function getAlmacenPrincipal() {
        $this->db->select('*');
        $this->db->from('almacen');
        $this->db->where('ID_Organizacion', $this->session->userdata['usuario']->ID_Organizacion);
        $this->db->where('Txt_Token_Lae_Shop IS NOT NULL');
        $query = $this->db->get();
        return $query->row();
    }

    function crearArchvioFacebookLaeShop($Txt_Facebook_Pixel_Lae_Shop) {
        $archivo = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/downloads/' . $Txt_Facebook_Pixel_Lae_Shop . '.csv';
        $fp = fopen($archivo, 'w');
        $columnas = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand','google_product_category', 'status', 'additional_image_link'];
        fputcsv($fp, $columnas);
        fclose($fp);
    }

    function getArchivosFacebookCron() {
        $this->db->select('AFC.*, CONF.Txt_Facebook_Pixel_Lae_Shop');
        $this->db->from('archivo_facebook_cron AS AFC');
        $this->db->join('configuracion AS CONF', 'CONF.ID_Empresa = AFC.ID_Empresa', 'join');
        $this->db->where('AFC.Nu_Estado < 2');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();
    }

    function actualizarEstadoArchivoFacebookCron($ID_Archivo_Facebook_Cron, $Nu_Estado) {
        switch ($Nu_Estado) {
            case 1:
                $this->db->set('Fe_Registro_Inicio', date("Y-m-d G:i:s"));
                break;
            case 2:
                $this->db->set('Fe_Registro_Fin', date("Y-m-d G:i:s"));
                break;
        }
        $this->db->set('Nu_Estado', $Nu_Estado);
        $this->db->where("ID_Archivo_Facebook_Cron",$ID_Archivo_Facebook_Cron);
        $this->db->update('archivo_facebook_cron');      
    }

    function getProductos($ID_Empresa, $Nu_Pagina) {
        $this->db->select('PRO.*, FAM.No_Familia');
        $this->db->from('producto AS PRO');
        $this->db->join('familia AS FAM','FAM.ID_Familia = PRO.ID_Familia', 'join');
        $this->db->where('PRO.ID_Empresa', $ID_Empresa);
        $this->db->order_by('PRO.Nu_Codigo_Barra', 'ASC');
        $this->db->limit(500, $Nu_Pagina);
        $query = $this->db->get();
        return $query->result();
    }

    function getSubdominioTiendaVirtualByIDEmpresa($ID_Empresa) {
        $this->db->select('No_Subdominio_Tienda_Virtual, No_Dominio_Tienda_Virtual');
        $this->db->from('subdominio_tienda_virtual');
        $this->db->where('ID_Empresa', $ID_Empresa);
        $this->db->where('Nu_Estado', 1);
        $query = $this->db->get();
        return $query->row();
    }

    function getEmpresaIdentidadById($ID_Empresa) {
        $this->db->select('Nu_Documento_Identidad');
        $this->db->from('empresa');
        $this->db->where('ID_Empresa', $ID_Empresa);
        $query = $this->db->get();
        return $query->row();
    }

    public function limpiarTexto($texto) {
		$texto = ucfirst(str_replace('&nbsp;', '', strtolower(trim(strip_tags(preg_replace('[\n|\r|\n\r]', '', $texto))))));
        $from = array(
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ',
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ',
            'ß', 'ç', 'Ç',
            'è', 'é', 'ê', 'ë',
            'È', 'É', 'Ê', 'Ë',
            'ì', 'í', 'î', 'ï',
            'Ì', 'Í', 'Î', 'Ï',
            'ñ', 'Ñ',
            'ò', 'ó', 'ô', 'õ', 'ö',
            'Ò', 'Ó', 'Ô', 'Õ', 'Ö',
            'š', 'Š',
            'ù', 'ú', 'û', 'ü',
            'Ù', 'Ú', 'Û', 'Ü',
            'ý', 'Ý', 'ž', 'Ž'
        );
        $to = array(
            'a', 'á', 'a', 'a', 'a', 'a', 'a', 
            'a', 'á', 'a', 'a', 'a', 'a', 'a',
            'B',  'c', 'C',
            'e', 'é', 'e', 'e',
            'e', 'é', 'e', 'e',
            'i', 'í', 'i', 'i',
            'i', 'í', 'i', 'i', 
            'ñ',  'ñ',
            'o', 'ó', 'o', 'o', 'o',
            'o', 'ó', 'o', 'o', 'o', 
            's',  'S', 
            'u', 'ú', 'u', 'u', 
            'u', 'ú', 'u', 'u', 
            'y',  'Y', 'z', 'Z'
        );
        return str_replace($from, $to, $texto);
	}

    function getProductoImagenes($ID_Producto) {
        $this->db->select('*');
        $this->db->from('producto_imagen');
        $this->db->where('ID_Producto', $ID_Producto);
        $this->db->where('ID_Estatus', 1);
        $this->db->order_by('ID_Predeterminado', 'DESC');
        $this->db->limit('21');
        $query = $this->db->get();
        return $query->result();
    }

    function galeriaProductoImagenes($arrProductoImagenes, $Txt_Ruta_Producto_Imagenes) {
        $Txt_Producto_Imanenes = '';        
        for ($i = 1; $i < count($arrProductoImagenes); $i++) {
            if(!empty($Txt_Producto_Imanenes)) {
                $Txt_Producto_Imanenes .= ",";
            }
            $Txt_Producto_Imanenes .=  $Txt_Ruta_Producto_Imagenes . $arrProductoImagenes[$i]->No_Producto_Imagen;
        }
        return $Txt_Producto_Imanenes;
    }

    function actualizarPaginaArchivoFacebookCron($ID_Archivo_Facebook_Cron) {
        $this->db->set('Nu_Pagina','Nu_Pagina + '. (int) 1, FALSE);
        $this->db->where("ID_Archivo_Facebook_Cron", $ID_Archivo_Facebook_Cron);
        $this->db->update('archivo_facebook_cron');      
    }

    function generarArchivoFacebookLaeShop($archivo) {
        if($archivo->Nu_Estado == 0) {
            $this->crearArchvioFacebookLaeShop($archivo->Txt_Facebook_Pixel_Lae_Shop);
            $this->actualizarEstadoArchivoFacebookCron($archivo->ID_Archivo_Facebook_Cron, 1);
        }
        $Nu_Pagina = $archivo->Nu_Pagina > 0 ? $archivo->Nu_Pagina * 500 : 0;
        $arrProductos = $this->getProductos($archivo->ID_Empresa, $Nu_Pagina);
        if(count($arrProductos) > 0) {
            $No_Dominio_Tienda_Virtual = 'compramaz.com';
            $arrSubdominio = $this->getSubdominioTiendaVirtualByIDEmpresa($archivo->ID_Empresa);
            if(count($arrSubdominio) > 0) {
                $No_Subdominio_Tienda_Virtual = $arrSubdominio->No_Subdominio_Tienda_Virtual;
                if(strlen($No_Subdominio_Tienda_Virtual) > 0) {
                    $No_Subdominio_Tienda_Virtual .= ".";
                }
                $No_Dominio_Tienda_Virtual = $No_Subdominio_Tienda_Virtual . $arrSubdominio->No_Dominio_Tienda_Virtual;
            }
            $Txt_Enlace = 'https://'.$No_Dominio_Tienda_Virtual.'/libro/';
            $Txt_Ruta_Producto_Imagenes = 'https://ecxpresslae.com/assets/images/productos/';
            $arrCarpetaImagenes = $this->getEmpresaIdentidadById($archivo->ID_Empresa);
            if(count($arrCarpetaImagenes) > 0 ) {
                $Txt_Ruta_Producto_Imagenes .= $arrCarpetaImagenes->Nu_Documento_Identidad . '/';
            }
            $FileName = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/downloads/' . $archivo->Txt_Facebook_Pixel_Lae_Shop . '.csv';
            $fp = fopen($FileName, 'a+');
            foreach ($arrProductos as $producto) {
                $Txt_Producto = substr($this->limpiarTexto($producto->Txt_Producto), 0, 9000);
                if(strlen($Txt_Producto) <= 0) {
                    $Txt_Producto = 'Sin descripción';
                }
                $Ss_Precio = $producto->Ss_Precio_Ecommerce_Online_Regular;	
                if($producto->Ss_Precio_Ecommerce_Online > 0.00) {
                    $Ss_Precio = $producto->Ss_Precio_Ecommerce_Online;
                }
                $arrProductoImagenes = $this->getProductoImagenes($producto->ID_Producto);
                if(count($arrProductoImagenes) > 0) {
                    $Txt_Producto_Imagen_Predeterminada = $Txt_Ruta_Producto_Imagenes . $arrProductoImagenes[0]->No_Producto_Imagen;
                    $Txt_Producto_Imagenes = '';
                    if(count($arrProductoImagenes) > 1) {
                        $Txt_Producto_Imagenes = $this->galeriaProductoImagenes($arrProductoImagenes, $Txt_Ruta_Producto_Imagenes);
                    }   
                } else {
                    $Txt_Producto_Imagen_Predeterminada = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/img/600.jpg';
                    $Txt_Producto_Imagenes = '';
                }
                $No_Familia = $this->limpiarTexto($producto->No_Familia);
                if(strlen($No_Familia) <= 0) {
                    $No_Familia = 'Genérico';
                }
                $row = [];
                $row[] = $producto->Nu_Codigo_Barra;
                $row[] = substr($this->limpiarTexto($producto->No_Producto), 0, 150);
                $row[] = $Txt_Producto;
                $row[] = 'available for order';
                $row[] = 'new';
                $row[] = $Ss_Precio;
                $row[] = $Txt_Enlace.$producto->ID_Producto;
                $row[] = $Txt_Producto_Imagen_Predeterminada;
                $row[] = 'Varios';
                $row[] = $No_Familia;
                $row[] = $producto->Nu_Activar_Item_Lae_Shop == 1 ? "active" : "archived";
                $row[] = $Txt_Producto_Imagenes;
                //echo "<pre>";  print_r($row); echo "</pre>";
                fputcsv($fp, $row);
            }
            fclose($fp);
            $this->actualizarPaginaArchivoFacebookCron($archivo->ID_Archivo_Facebook_Cron);
            if(count($arrProductos) < 500) {
                $this->actualizarEstadoArchivoFacebookCron($archivo->ID_Archivo_Facebook_Cron, 2);
            }
        } else {
            $this->actualizarEstadoArchivoFacebookCron($archivo->ID_Archivo_Facebook_Cron, 2);
        }
    }

    function ArchivoFacebookShopCron(){
        $arrArchivos = $this->getArchivosFacebookCron();
		if(count($arrArchivos) > 0) {
			foreach ($arrArchivos as $archivo) {
				$this->generarArchivoFacebookLaeShop($archivo);
			}
		}
    }

    function getArchivosGoogleCron() {
        $this->db->select('AGC.*, CONF.Txt_Google_Shopping_Dominio_Lae_Shop');
        $this->db->from('archivo_google_cron AS AGC');
        $this->db->join('configuracion AS CONF', 'CONF.ID_Empresa = AGC.ID_Empresa', 'join');
        $this->db->where('AGC.Nu_Estado < 2');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();
    }


    function crearArchvioGoogleLaeShop($Txt_Google_Shopping_Dominio_Lae_Shop) {
        $archivo = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/downloads/' . $Txt_Google_Shopping_Dominio_Lae_Shop . '.txt';
        $fp = fopen($archivo, 'w');
        $columnas = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand','google_product_category', 'additional_image_link'];
        fwrite($fp, implode("\t", $columnas)."\n");
        fclose($fp);
    }

    function actualizarEstadoArchivoGoogleCron($ID_Archivo_Google_Cron, $Nu_Estado) {
        switch ($Nu_Estado) {
            case 1:
                $this->db->set('Fe_Registro_Inicio', date("Y-m-d G:i:s"));
                break;
            case 2:
                $this->db->set('Fe_Registro_Fin', date("Y-m-d G:i:s"));
                break;
        }
        $this->db->set('Nu_Estado', $Nu_Estado);
        $this->db->where("ID_Archivo_Google_Cron",$ID_Archivo_Google_Cron);
        $this->db->update('archivo_google_cron');      
    }

    function actualizarPaginaArchivoGoogleCron($ID_Archivo_Google_Cron) {
        $this->db->set('Nu_Pagina','Nu_Pagina + '. (int) 1, FALSE);
        $this->db->where("ID_Archivo_Google_Cron", $ID_Archivo_Google_Cron);
        $this->db->update('archivo_google_cron');      
    }


    function generarArchivoGoogleLaeShop($archivo) {
        if($archivo->Nu_Estado == 0) {
            $this->crearArchvioGoogleLaeShop($archivo->Txt_Google_Shopping_Dominio_Lae_Shop);
            $this->actualizarEstadoArchivoGoogleCron($archivo->ID_Archivo_Google_Cron, 1);
        }
        $Nu_Pagina = $archivo->Nu_Pagina > 0 ? $archivo->Nu_Pagina * 500 : 0;
        $arrProductos = $this->getProductos($archivo->ID_Empresa, $Nu_Pagina);
        if(count($arrProductos) > 0) {
            $No_Dominio_Tienda_Virtual = 'compramaz.com';
            $arrSubdominio = $this->getSubdominioTiendaVirtualByIDEmpresa($archivo->ID_Empresa);
            if(count($arrSubdominio) > 0) {
                $No_Subdominio_Tienda_Virtual = $arrSubdominio->No_Subdominio_Tienda_Virtual;
                if(strlen($No_Subdominio_Tienda_Virtual) > 0) {
                    $No_Subdominio_Tienda_Virtual .= ".";
                }
                $No_Dominio_Tienda_Virtual = $No_Subdominio_Tienda_Virtual . $arrSubdominio->No_Dominio_Tienda_Virtual;
            }
            $Txt_Enlace = 'https://'.$No_Dominio_Tienda_Virtual.'/libro/';
            $Txt_Ruta_Producto_Imagenes = 'https://ecxpresslae.com/assets/images/productos/';
            $arrCarpetaImagenes = $this->getEmpresaIdentidadById($archivo->ID_Empresa);
            if(count($arrCarpetaImagenes) > 0 ) {
                $Txt_Ruta_Producto_Imagenes .= $arrCarpetaImagenes->Nu_Documento_Identidad . '/';
            }
            $FileName = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/downloads/' . $archivo->Txt_Google_Shopping_Dominio_Lae_Shop . '.txt';
            $fp = fopen($FileName, 'a+');
            foreach ($arrProductos as $producto) {
                $Txt_Producto = substr($this->limpiarTexto($producto->Txt_Producto), 0, 5000);
                if(strlen($Txt_Producto) <= 0) {
                    $Txt_Producto = 'Sin descripción';
                }
                $Ss_Precio = $producto->Ss_Precio_Ecommerce_Online_Regular;	
                if($producto->Ss_Precio_Ecommerce_Online > 0.00) {
                    $Ss_Precio = $producto->Ss_Precio_Ecommerce_Online;
                }
                $arrProductoImagenes = $this->getProductoImagenes($producto->ID_Producto);
                if(count($arrProductoImagenes) > 0) {
                    $Txt_Producto_Imagen_Predeterminada = $Txt_Ruta_Producto_Imagenes . $arrProductoImagenes[0]->No_Producto_Imagen;
                    $Txt_Producto_Imagenes = '';
                    if(count($arrProductoImagenes) > 1) {
                        $Txt_Producto_Imagenes = $this->galeriaProductoImagenes($arrProductoImagenes, $Txt_Ruta_Producto_Imagenes);
                    }   
                } else {
                    $Txt_Producto_Imagen_Predeterminada = '/var/www/vhosts/ecxpresslae/public_html/principal/assets/img/600.jpg';
                    $Txt_Producto_Imagenes = '';
                }
                $No_Familia = $this->limpiarTexto($producto->No_Familia);
                if(strlen($No_Familia) <= 0) {
                    $No_Familia = 'Genérico';
                }
                $row = [];
                $row[] = trim($producto->Nu_Codigo_Barra);
                $row[] = trim(substr($this->limpiarTexto($producto->No_Producto), 0, 150));
                $row[] = trim($Txt_Producto);
                $row[] = 'in_stock';
                $row[] = 'new';
                $row[] = $Ss_Precio.' PEN';
                $row[] = trim($Txt_Enlace.$producto->ID_Producto);
                $row[] = trim($Txt_Producto_Imagen_Predeterminada);
                $row[] = 'Varios';
                $row[] = trim($No_Familia);
                $row[] = trim($Txt_Producto_Imagenes);
                //echo "<pre>";  print_r($row); echo "</pre>";
                fwrite($fp, implode("\t", $row)."\n");
            }
            fclose($fp);
            $this->actualizarPaginaArchivoGoogleCron($archivo->ID_Archivo_Google_Cron);
            if(count($arrProductos) < 500) {
                $this->actualizarEstadoArchivoGoogleCron($archivo->ID_Archivo_Google_Cron, 2);
            }
        } else {
            $this->actualizarEstadoArchivoGoogleCron($archivo->ID_Archivo_Google_Cron, 2);
        }
    }

    function ArchivoGoogleLaeShopCron(){
        $arrArchivos = $this->getArchivosGoogleCron();
        if(count($arrArchivos) > 0) {
			foreach ($arrArchivos as $archivo) {
				$this->generarArchivoGoogleLaeShop($archivo);
			}
		}
    }

}