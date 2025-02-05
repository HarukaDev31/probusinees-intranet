<?php
trait MailTrait
{
    public function sendMail($to, $subject, $message,$attachmentsPath=[]) {
        try {
            $this->load->library('email');
    
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'smtp.gmail.com';
            $config['smtp_port'] = 587; // Cambia a 465 si usas SSL
            $config['smtp_user'] = 'harukakasugano31@gmail.com'; // Tu correo Gmail
            $config['smtp_pass'] = 'rhfhudoximvjulwh'; // NO tu clave normal, usa una contraseña de aplicación
            $config['smtp_crypto'] = 'tls'; // Usa 'ssl' si cambias a puerto 465
            $config['mailtype'] = 'html';
            $config['charset'] = 'utf-8';
            $config['smtp_timeout'] = '6';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; // Necesario para Gmail
            $config['crlf'] = "\r\n"; // Necesario para Gmail
            $config['validation'] = TRUE;
    
            $this->email->initialize($config);
            $this->email->from('harukakasugano31@gmail.com', 'PROBUSINESS'); // Remitente
            $this->email->to($to); // Destinatario
            $this->email->subject($subject);
            $this->email->message($message);
            //APPEND FILE TO EMAIL
            foreach ($attachmentsPath as $attachment) {
                if (file_exists($attachment)) {
                    $this->email->attach($attachment);
                }
            }
            if ($this->email->send()) {
                $this->email->clear(TRUE); // Limpiar configuración y adjuntos

                return true;
            } else {
                $this->email->clear(TRUE); // Limpiar configuración y adjuntos

                return $this->email->print_debugger(); // Muestra errores si falla
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
}