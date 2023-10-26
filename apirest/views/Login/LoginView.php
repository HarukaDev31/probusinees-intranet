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
    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0'; ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0" type="image/x-icon'; ?>">
    
    <title>ProBusiness | Login</title>
    <meta name="author" content="Ecxlae">
    <meta name="Subject" content="Creamos soluciones innovadoras">
    <meta name="Copyright" content="Copyright © Ecxlae. Todos los derechos reservados.">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/bootstrap/dist/css/bootstrap.min.css"); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url("bower_components/font-awesome/css/font-awesome.min.css"); ?>">
    <!-- Selected -->
    <!-- <link rel="stylesheet" href="<?php echo base_url("bower_components/select2/dist/css/select2.min.css"); ?>"> -->
    <!-- Ecxlae -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/login.css?ver=3.30.0"); ?>">
    
    <meta name="theme-color" content="#FF6700">
		<meta name="msapplication-navbutton-color" content="#FF6700"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<meta name="msapplication-navbutton-color" content="#FF6700" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HCDF3CNLJC"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-HCDF3CNLJC');
</script>
</head>
<body>
  <div class="fondo_pantalla">
    <div class="container-fluid">
        <div class="row">
        <div class="d-none d-md-flex col-sm-12 col-md-6 col-lg-6 bg-image" alt="ProBusiness" title="ProBusiness"></div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <div class="row row-login-logo">
                  <div class="col-md-12 col-lg-12 text-center">
                    <img class="img-logo" src="<?php echo base_url("assets/img/logos/logo_horizontal_probusiness_claro.png?ver=4.0.0") ?>" alt="ProBusiness" title="ProBusiness">
                  </div>
                </div>
              </div>
              <div class="panel-body">
                    <?php
                    $attributes = array('id' => 'form-login');
                    echo form_open('', $attributes, '');
                    ?>
                        <div id="div-login" class="row">
                      <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
                          <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user fa-lg" aria-hidden="true"></i></span>
                            <input type="text" id="txt-usuario" name="No_Usuario" inputmode="email" class="form-control inputBgOpaque input-Minuscula input-username" autocomplete="on" autocorrect="off" autocapitalize="none" placeholder="Ingresar correo">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
                          <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                            <input type="password" id="txt-password" name="No_Password" class="form-control pwd inputBgOpaque" autocomplete="on" placeholder="Ingresar contraseña">
                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                                    <div class="div-msg"></div>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                          <button type="submit" id="btn-login" class="btn btn-success btn-md btn-block">Iniciar sesión</button>
                          <button type="button" id="btn-recuperar_cuenta" class="btn btn-link btn-md btn-block">Recuperar contraseña</button>
                          <!--
                          <button type="button" id="btn-crear_cuenta" class="btn btn-default btn-md btn-block">Crear cuenta</button>
                          <button type="button" id="btn-recuperar_cuenta" class="btn btn-link btn-md btn-block">Recuperar contraseña</button>-->
                        </div>
                      </div>
                    </div>
                    <?php
                    echo form_close();
                    $attributes = array('id' => 'form-login_empresa');
                    echo form_open('', $attributes, ''); ?>
                        <div id="div-empresa" class="row">
                            <input type="hidden" id="txt-usuario_empresa" name="No_Usuario" class="form-control">
                            <input type="hidden" id="txt-password_empresa" name="No_Password" class="form-control">
                      
                            <div class="col-sm-12">
                          <div class="form-group">
                                    <label>Empresa</label>
                          <select id="cbo-Empresas" name="ID_Empresa" class="form-control required" style="width: 100%;"></select>
                                    <span class="help-block" id="error"></span>
                        </div>
                      </div>

                            <div class="col-sm-12">
                          <div class="form-group">
                                    <label>Organización</label>
                          <select id="cbo-organizacion" name="ID_Organizacion" class="form-control required" style="width: 100%;"></select>
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
                          <button type="submit" id="btn-login_empresa" class="btn btn-success btn-md btn-block">Entrar</button>
                          <button type="button" class="btn btn-link btn-md btn-block btn-login_return">Regresar al login</button>
                        </div>
                      </div>
                    </div>
                    <?php
                    echo form_close();
                    $attributes = array('id' => 'form-recuperar_cuenta');
                    echo form_open('', $attributes, '');?>
                        <div id="div-recuperar_cuenta" class="row">
                      <div class="col-sm-12">
                          <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></span>
                            <input type="text" id="txt-email" name="Txt_Email_Recovery" inputmode="email" class="form-control" autocorrect="off" autocapitalize="none" placeholder="Ingresar correo">
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
                          <button type="submit" id="btn-send_correo" class="btn btn-success btn-md btn-block">Recuperar cuenta</button>
                          <button type="button" class="btn btn-link btn-md btn-block btn-login_return">Regresar al login</button>
                        </div>
                      </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
              </div>
          </div>
        </div>
    </div>
  </div>
  <script type="text/javascript" src="<?php echo base_url("assets/js/jquery-3.2.1.min.js"); ?>"></script>
  <script type="text/javascript" src="<?php echo base_url("plugins_v2/bootstrap/js/bootstrap.bundle.min.js"); ?>"></script>
  <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
  <script type="text/javascript" src="<?php echo base_url("plugins/input-mask/jquery.inputmask.js"); ?>"></script>
  <script type="text/javascript" src="<?php echo base_url("assets/js/inicio.js?ver=2.5.20"); ?>"></script>
  <script> var base_url = '<?php echo base_url(); ?>'; </script>
</body>