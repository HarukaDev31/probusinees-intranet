<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=gb18030">
        <title>LAE Systems</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>            
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
        </style>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
            <tr>
                <td>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFF" style="order-bottom-left-radius: 3px; order-bottom-right-radius: 3px; border-color: transparent; border-image: none; border-style: none transparent solid; border-width: 0 1px 1px; max-width: 600px; min-width: 332px;">
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr height="10">
                                        <td width="32px">
                                            Estimados,<br>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">
                                            <br>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">
                                            Se desea actualizar nuestra versión de sistema <b><?php echo $sVersionCliente; ?> a <?php echo $sVersionNueva; ?></b>:
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">
                                            <ul>
                                                <li>Empresa: <b><?php echo $sRazonSocialEmpresa; ?></b></li>
                                                <li>Organizacion: <b><?php echo $sNombreOrganizacion; ?></b></li>
                                                <li>Fecha de solicitud: <b><?php echo $dSolicitud; ?></b></li>
                                                <li>Usuario: <b><?php echo $sUsuario; ?></b></li>
                                                <li>Version Cliente: <b><?php echo $sVersionCliente; ?></b></li>
                                                <li>Version Nueva: <b><?php echo $sVersionNueva; ?></b></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr height="10">
                                        <td width="32px">
                                            Atentamente,
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>