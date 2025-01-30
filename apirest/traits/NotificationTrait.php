<?php
trait NotificationTrait
{
    /**
     * This function is used to create a notification for a group of users
     * @param $arrayIdUsers
     * @param $message
     * @param $nombreMenu
     * @param $IdUserCreador
     */
    public function createNotification($arrayIdUsers,$message,$nombreMenu,$IdUserCreador){
        try{
            $notificacion = array();
        foreach ($arrayIdUsers as $row) {
            $notificacion[] = array(
                'ID_Empresa' => 1,
                'ID_Organizacion' => 1,
                'ID_Usuario' => $row,
                'No_Usuario_Evento' => $IdUserCreador,
                'No_Menu' => $nombreMenu,
                'No_Evento' => $message,
            );
        }
        if(!empty($notificacion)){
            $this->db->insert_batch('notificacion', $notificacion);
        }
        if($this->db->error()['code'] != 0){
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $this->db->error()['code'],
                'sMessageSQL' => $this->db->error()['message'],
            );
        }
            
        return array(
            'status' => 'success',
            'message' => 'Se registro notificación',
            'notification' => $notificacion
        );
        }catch(Exception $e){
            return array(
                'status' => 'danger',
                'message' => 'Problemas al obtener datos',
                'sCodeSQL' => $e->getCode(),
                'sMessageSQL' => $e->getMessage(),
            );
        }
    }

}