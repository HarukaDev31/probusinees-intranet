<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="color-scheme" content="light dark">
    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico?ver=4.0">
    <link rel="apple-touch-icon-precomposed" sizes="192x192" href="assets/ico/android-chrome-512x512.png?ver=4.0">
    <link rel="apple-touch-icon-precomposed" sizes="192x192" href="assets/ico/android-chrome-192x192.png?ver=4.0">
    <link rel="apple-touch-icon-precomposed" sizes="32x32" href="assets/ico/favicon-32x32.png?ver=4.0">
    <link rel="apple-touch-icon-precomposed" sizes="16x16" href="assets/ico/favicon-16x16.png?ver=4.0">
    <link rel="apple-touch-icon-precomposed" sizes="16x16" href="assets/ico/apple-touch-icon.png?ver=4.0">
    <link rel="manifest" href="/site.webmanifest">
    <title>Ecxlae | Recuperar contraseña</title>
    <meta name="author" content="Ecxlae">
    <meta name="Subject" content="Creamos soluciones innovadoras">
    <meta name="Copyright" content="Copyright © Ecxlae. Todos los derechos reservados.">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/bootstrap/dist/css/bootstrap.min.css"); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/font-awesome/css/font-awesome.min.css"); ?>">
    <!-- Selected -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/select2/dist/css/select2.min.css"); ?>">
    <!-- Ecxlae -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/login.css?ver=4.0"); ?>">
    
    <meta name="theme-color" content="#000000">
    <meta name="msapplication-navbutton-color" content="#000000"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="theme-color" content="#000000" />  
    <meta name="msapplication-navbutton-color" content="#000000" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1Z3VT88W7C"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1Z3VT88W7C');
</script>
</head>
<body>
    <div class="fondo_pantalla">
        <div class="top-content">
        <div class="inner-bg">
        <div class="container">
            <div class="row">
            <div class="col-xs-11 col-sm-6 col-sm-offset-2 col-md-5 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="panel panel-default">
                	<div class="panel-heading">
                        <div class="row">
                			<div class="col-xs-offset-3 col-xs-6 col-sm-6 col-sm-offset-3 col-md-6">
                                <img class="img-logo" src="<?php echo base_url("assets/img/logos/logo_ecxlae.png?ver=4.0.0") ?>" alt="EcXlae" title="EcXlae">
                            </div>
                        </div>
                	</div>
                	<div class="panel-body">
                        <?php
                        $attributes = array('id' => 'form-cambiar_clave');
                        echo form_open('', $attributes, '');
                        ?>
                            <div id="div-login" class="row">
                    			<div class="col-sm-12">
                                    <input type="hidden" name="Txt_Token_Activacion" value="<?php echo $token; ?>">
                    			    <div class="form-group">
                    					<div class="input-group">
                    						<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                    						<input type="password" id="No_Password" name="No_Password" class="form-control pwd_1" autocomplete="off" placeholder="Ingresar contraseña">
                    						<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-recuperar_password_1"></span>
                    					</div>
                    					<span class="help-block" id="error"></span>
                    				</div>
                    			</div>
                    			
                    			<div class="col-sm-12">
                    			    <div class="form-group">
                    					<div class="input-group">
                    						<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                    						<input type="password" name="RNo_Password" class="form-control pwd_2" autocomplete="off" placeholder="Ingresar contraseña">
                    						<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-recuperar_password_2"></span>
                    					</div>
                    					<span class="help-block" id="error"></span>
                    				</div>
                    			</div>
                    			
                    			<div class="col-sm-12">
                    			    <div class="form-group">
                                        <div class="div-msg"></div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                    			    <div class="form-group">
                    					<button type="submit" id="btn-cambiar_clave" class="btn btn-primary btn-md btn-block">Cambiar contraseña</button>
                                        <a href="<?php echo base_url(); ?>" class="btn btn-success btn-md btn-block" role="button">Iniciar sesión</a>
                    				</div>
                    			</div>
                    		</div>
                        <?php echo form_close(); ?>
                	</div>
                </div>
            </div>
            </div><!--FIN row-->
        </div><!--FIN Container-->
        </div>
        </div>
        <footer>
            <div id="footer">
                <div id="footerLinks" class="footerNode text-secondary">
                    <span id="ftrCopy"><a href="https://www.ecxpresslae.com" target="_blank" alt="Ecxlae" title="Ecxlae" style="text-decoration: none;"><span style="color: #29c7d8">Ecxlae</span> - <?php echo date("Y"); ?></a></span>
                </div>
            </div>
        </footer>
    </div>
    <!-- Ecxlae -->
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery-3.2.1.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bower_components/bootstrap/dist/js/bootstrap.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bower_components/select2/dist/js/select2.full.min.js"); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/recuperar_cuenta.js?ver=2.3"); ?>"></script>
	<script> var base_url = '<?php echo base_url(); ?>'; </script>
</body>