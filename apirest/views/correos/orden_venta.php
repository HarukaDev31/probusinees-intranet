<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=gb18030">
        <title>laesystems</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap');

            *{
                font-family: 'Roboto', sans-serif;
            }

            #btn-ver_documento, #btn-ver_documento:link, #btn-ver_documento:visited {
                background-color: #00b8d4;
                color: white;
                padding: 14px 25px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                border-radius: 8px;
            }
            
            #btn-ver_documento:hover, #btn-ver_documento:active {
                background-color: #08c7e5;
            }
            
            .list-unstyled {
                padding-left: 0;
                list-style: none;
            }
            
            .list-inline li {
                display: inline-block;
                padding-right: 5px;
                padding-left: 5px;
                margin-bottom: 5px;
            }
            
            /*---- Genral classes end -------*/
            
            /*Change icons size here*/
            .social-icons .fa {
                font-size: 1.8em;
            }
            /*Change icons circle size and color here*/
            .social-icons .fa {
                border-radius: 8px;
                width: 50px;
                height: 50px;
                line-height: 50px;
                text-align: center;
                color: #FFF;
                color: rgba(255, 255, 255, 0.8);
                -webkit-transition: all 0.3s ease-in-out;
                -moz-transition: all 0.3s ease-in-out;
                -ms-transition: all 0.3s ease-in-out;
                -o-transition: all 0.3s ease-in-out;
                transition: all 0.3s ease-in-out;
            }
            
            .social-icons.icon-circle .fa{ 
                border-radius: 50%;
            }
            .social-icons.icon-rounded .fa{
                border-radius:5px;
            }
            .social-icons.icon-flat .fa{
                border-radius: 0;
            }
            
            .social-icons .fa:hover, .social-icons .fa:active {
                color: #FFF;
                -webkit-box-shadow: 1px 1px 3px #333;
                -moz-box-shadow: 1px 1px 3px #333;
                box-shadow: 1px 1px 3px #333; 
            }
            .social-icons.icon-zoom .fa:hover, .social-icons.icon-zoom .fa:active { 
                -webkit-transform: scale(1.1);
                -moz-transform: scale(1.1);
                -ms-transform: scale(1.1);
                -o-transform: scale(1.1);
                transform: scale(1.1); 
            }
            .social-icons.icon-rotate .fa:hover, .social-icons.icon-rotate .fa:active { 
                -webkit-transform: scale(1.1) rotate(360deg);
                -moz-transform: scale(1.1) rotate(360deg);
                -ms-transform: scale(1.1) rotate(360deg);
                -o-transform: scale(1.1) rotate(360deg);
                transform: scale(1.1) rotate(360deg);
            }
             
            .social-icons .fa-facebook,.social-icons .fa-facebook-square{background-color:#3C599F;} 
            .social-icons .fa-instagram{background-color:#A1755C;}
        </style>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFF" style="order-bottom-left-radius: 3px; order-bottom-right-radius: 3px; border-color: transparent; border-image: none; border-style: none transparent solid; border-width: 0 1px 1px; max-width: 600px; min-width: 332px;">
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr height="10">
                                        <td width="32px">Estimado(a) <?php echo $No_Entidad; ?>,<br></td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px"><br></td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">Detallamos en este mensaje una <?php echo $No_Documento; ?>.</td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">
                                            <ul>
                                                <li>Documento: <b><?php echo $No_Documento; ?></b></li>
                                                <li>Fecha Emisión: <b><?php echo $Fe_Emision; ?></b></li>
                                                <li>Fecha Vencimiento: <b><?php echo $Fe_Vencimiento; ?></b></li>
                                                <li>Total: <b><?php echo $No_Signo . ' ' . $Ss_Total; ?></b></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">Se adjunta en este mensaje la <b><?php echo $No_Documento; ?></b> en formato PDF.</td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px"><br></td>
                                    </tr>
                                    <?php if (!empty($this->empresa->Txt_Cuentas_Bancarias)) { ?>
                                    <tr>
                                        <td align="center" bgcolor="#f1f1f1">
                                            <table border="0" cellpadding="5" cellspacing="5" width="100%">
                                                <tr height="10">
                                                    <td width="32px" align="center">Depósito, transferencia o agente</td>
                                                </tr>
                                                <tr height="10">
                                                    <td width="32px">
                                                        <?php echo $this->empresa->Txt_Cuentas_Bancarias; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px"><br></td>
                                    </tr>
                                    <?php } ?>
                                    <tr height="10">
                                        <td width="32px">
                                            Atentamente,<br>
                                            <b><?php echo $No_Empresa; ?></b><br>
                                            <b>RUC <?php echo $Nu_Documento_Identidad_Empresa; ?></b><br>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px"><br></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" bgcolor="#fafafa">
                                <div class="wrapper">
                                    <ul class="social-icons icon-flat list-unstyled list-inline"> 
                                        <li>
                                            <a style="display:inline-block;" href="https://www.facebook.com/laesystemsperu" alt="laesystems" title="laesystems" target="_blank">
                                                <img src="<?php echo base_url() ?>assets/img/facebook.png" style="border-radius: 4px" />
                                            </a>
                                        </li>
                                        <li>
                                            <a style="display:inline-block;" href="https://instagram.com/laesystems" alt="laesystems" title="laesystems" target="_blank">
                                                <img src="<?php echo base_url() ?>assets/img/instagram.png" style="border-radius: 4px" />
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr height="10">
                            <td style="font-weight: bold; color: #fff; padding: 8px 0 4px 0; border-radius: 8px;" align="center" bgcolor="#00b8d4">
                                <h4>visítanos también en nuestra página web <a href="https://www.laesystems.com" alt="laesystems" title="laesystems" target="_blank" style="color: #FFF; text-decoration: none;"> www.laesystems.com</a></h4>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>