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
                    					<button type="button" id="btn-recuperar_cuenta" class="btn btn-link btn-md btn-block">Recuperar contraseña</button>
-->
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
                        <?php echo form_close();
                        $attributes = array('id' => 'form-crear_cuenta');
                        echo form_open('', $attributes, '');?>
                            <div id="div-crear_cuenta_cuentas_bancarias" class="row">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <div class="div-msg"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                      <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="well well-sm text-left"><strong>IMPORTANTE:</strong> Colocar su <strong>RUC</strong> en NOTA / DESCRIPCION.</div>
                                      </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="panel panel-success cuentas_bancarias">
                                        <div class="panel-heading interbank panel-head">
                                          <h3 class="panel-title">Interbank - SOLES</h3>
                                        </div>
                                        <div class="panel-body panel-body_v2">
                                            <div class="col-xs-12"><b>CUENTA CORRIENTE</b></div>

                                            <div class="col-xs-12"><b>TITULAR</b></div>
                                            <div class="col-xs-12">LAE SYSTEM EIRL</div>

                                            <div class="col-xs-12"><b>NRO. CUENTA</b></div>
                                            <div class="col-xs-12">200-3001580696</div>

                                            <div class="col-xs-12"><b>CCI</b></div>
                                            <div class="col-xs-12">003-200-003001580696-38</div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="panel panel-primary cuentas_bancarias">
                                        <div class="panel-heading bcp panel-head">
                                          <h3 class="panel-title">BCP - SOLES</h3>
                                        </div>
                                        <div class="panel-body panel-body_v2">
                                            <div class="col-xs-12"><b>CUENTA DE AHORROS</b></div>

                                            <div class="col-xs-12"><b>TITULAR</b></div>
                                            <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                                            <div class="col-xs-12"><b>NRO. CUENTA</b></div>
                                            <div class="col-xs-12">191-92655231-0-40</div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="panel panel-success cuentas_bancarias">
                                        <div class="panel-heading plin panel-head">
                                          <h3 class="panel-title">Plin</h3>
                                        </div>
                                        <div class="panel-body panel-body_v2">
                                            <div class="col-xs-12"><b>TITULAR</b></div>
                                            <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                                            <div class="col-xs-12"><b>CELULAR</b></div>
                                            <div class="col-xs-12">941 400 239</div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="panel panel-primary cuentas_bancarias yape_border">
                                        <div class="panel-heading yape yape_border panel-head">
                                          <h3 class="panel-title">Yape</h3>
                                        </div>
                                        <div class="panel-body panel-body_v2">
                                            <div class="col-xs-12"><b>TITULAR</b></div>
                                            <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                                            <div class="col-xs-12"><b>CELULAR</b></div>
                                            <div class="col-xs-12">941 400 239</div>
                                        </div>
                                      </div>
                                    </div>
                                
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-link btn-md btn-block btn-login_return">Regresar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="div-crear_cuenta" class="row">
                              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                <label>Tipo Conexión</label>
                                
                                <div class="form-group">
                                  <label style="cursor: pointer; font-weight:normal;">
                                    <input type="radio" name="Nu_Tipo_Proveedor_FE" class="" id="radio-sunat" value="2" style="cursor: pointer; height: 25px;width: 25px;vertical-align: middle;"> SUNAT
                                  </label>
                                  <label style="cursor: pointer; font-weight:normal;">
                                    &nbsp;<input type="radio" name="Nu_Tipo_Proveedor_FE" class="" id="radio-interno" value="3" style="cursor: pointer; height: 25px;width: 25px;vertical-align: middle;"> INTERNO
                                  </label>
                                  <span class="help-block" id="error"></span>
                                </div>
                                <!--
                                <div class="form-group">
                                  <select id="cbo-tipo_proveedor_fe" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;">
                                    <option value="" selected="selected">- Seleccionar -</option>
                                    <option value="3">INTERNO</option>
                                    <option value="2">SUNAT</option>
                                    <option value="1">PSE</option>
                                  </select>
                                  <span class="help-block" id="error"></span>
                                </div>
                                -->
                              </div>

                              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <label>T. Doc. Ident.</label>
                                <div class="form-group">
                                  <select id="cbo-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                                  <span class="help-block" id="error"></span>
                                </div>
                              </div>

                              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <label class="label-valortdi">RUC</label>
                                <div class="form-group">
                                  <div class="input-group" style="width: 100%;">
                                    <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" inputmode="numeric" class="form-control input-numeros_letras input-Mayuscula" placeholder="Ingresar" autocomplete="on">
                                    <span class="help-block" id="error"></span>
                                  </div>
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-7 col-md-12 col-lg-7">
                                  <div class="form-group">
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></span>
                                    <input type="text" id="txt-Txt_Email" name="Txt_Email" inputmode="email" class="form-control inputBgOpaque input-Minuscula input-username" placeholder="Email" autocomplete="on" autocorrect="off" autocapitalize="none">
                                  </div>
                                  <span class="help-block" id="error"></span>
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-5 col-md-12 col-lg-5">
                                  <div class="form-group">
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></span>
                                    <input type="text" id="txt-Nu_Celular" name="Nu_Celular" inputmode="tel" data-inputmask="'mask': ['999 999 999']" data-mask  class="form-control" placeholder="Celular" autocomplete="on">
                                  </div>
                                  <span class="help-block" id="error"></span>
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                  <div class="form-group">
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user fa-lg" aria-hidden="true"></i></span>
                                    <input type="text" id="txt-No_Nombres_Apellidos" name="No_Nombres_Apellidos" inputmode="text" class="form-control" placeholder="Nombres y Apellidos" autocomplete="on">
                                  </div>
                                  <span class="help-block" id="error"></span>
                                </div>
                              </div>
                                    
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                  <div class="form-group">
                                    <label>Rubro</label>
                                    <select id="cbo-rubro" name="ID_Tipo_Rubro_Empresa" class="form-control required" style="width: 100%;"></select>
                                    <span class="help-block" id="error"></span>
                                  </div>
                                </div>
                                    
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                  <div class="form-group">
                                    <label>Plan</label>
                                    <select id="cbo-plan_laegestion" name="Nu_Tipo_Plan_Lae_Gestion" class="form-control required" style="width: 100%;">
                                      <option value="" selected="selected">- Seleccionar -</option>
                                      <option value="1">Mensual</option>
                                      <!--<option value="2">Trimestral</option>-->
                                      <option value="3">Anual</option>
                                    </select>
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
                                  <button type="submit" id="btn-guardar_cuenta" class="btn btn-success btn-md btn-block">Crear cuenta</button>
                                  <button type="button" class="btn btn-link btn-md btn-block btn-login_return">Regresar al login</button>
                                </div>
                              </div>
                    		    </div><!-- crear cuenta-->
                        <?php echo form_close(); ?>
                    </div>
                  </div>
              </div>
            </div><!--FIN row-->
        </div><!--FIN Container-->
    </div>
	
  	<!-- Modal -->
	<div class="modal fade" id="modal-tipo_gestion" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center modal-header-title"></h4>
				</div>
				<div class="modal-body">
					<p class="modal-p-body-interno">
					Solo podrá emitir <strong>NOTA DE VENTA INTERNA.</strong><br>
					Acceso a todos los módulos.
					</p>
					<p class="modal-p-body-sunat_o_pse">
					Podrá emitir <strong>FACTURACIÓN ELECTRÓNICA Y VENTA INTERNA.</strong><br>
					Acceso a todos los módulos.
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Salir</button>
				</div>
			</div>			
		</div>
	</div>
  
<div class="modal fade in" id="modal-pago_cuenta_bancarias_laesystems" style="padding-right: 17px;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        <h3 class="text-center">Cuentas Bancarias</h3>
        <!--<h3 class="text-center"><strong>Pagar: S/ 79.90 o $ 19.90</strong></h3>-->
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="well well-sm alert-warning text-left" style="background: #f9ba00 !important;">
              <strong>IMPORTANTE:</strong> El precio no incluye IGV o comisiones por pasarela / billeteras virtuales.
              <div class="form-group">
                <label style="cursor: pointer;">Enviar voucher por WhatsApp
                  <a href="https://api.whatsapp.com/send?phone=51904179541&text=Envio%20pago%20de%20mi%20tienda%20<?php echo $this->empresa->No_Tienda_Lae_Shop; ?>%20y%20mi%20correo%20es%20<?php echo $this->user->No_Usuario; ?>" alt="EcxpressLae Pago" title="EcxpressLae Pago" target="_blank"  style="text-decoration: none !important;">
                    <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i><span style="color: #ffffff;font-size: 2rem;">904 179 541</span>
                  </a>
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-primary cuentas_bancarias">
              <div class="panel-heading bcp panel-head">
                <h3 class="panel-title">BCP - SOLES CUENTA AHORROS</h3>
              </div>
              <div class="panel-body panel-body_v2">
                  <div class="col-xs-12"><b>TITULAR</b></div>
                  <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                  <div class="col-xs-12"><b>NRO. CUENTA</b></div>
                  <div class="col-xs-12">192-71314190-0-33</div>
              </div>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-primary cuentas_bancarias">
              <div class="panel-heading bcp panel-head">
                <h3 class="panel-title">BCP - DÓLARES CUENTA AHORROS</h3>
              </div>
              <div class="panel-body panel-body_v2">
                  <div class="col-xs-12"><b>TITULAR</b></div>
                  <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                  <div class="col-xs-12"><b>NRO. CUENTA</b></div>
                  <div class="col-xs-12">191-95821664-1-57</div>
              </div>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-success cuentas_bancarias">
              <div class="panel-heading plin panel-head">
                <h3 class="panel-title">Plin</h3>
              </div>
              <div class="panel-body panel-body_v2">
                  <div class="col-xs-12"><b>TITULAR</b></div>
                  <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                  <div class="col-xs-12"><b>CELULAR</b></div>
                  <div class="col-xs-12">941 400 239</div>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-primary cuentas_bancarias yape_border">
              <div class="panel-heading yape yape_border panel-head">
                <h3 class="panel-title">Yape</h3>
              </div>
              <div class="panel-body panel-body_v2">
                  <div class="col-xs-12"><b>TITULAR</b></div>
                  <div class="col-xs-12">Antony Geancarlos Collazos Chumbile</div>

                  <div class="col-xs-12"><b>CELULAR</b></div>
                  <div class="col-xs-12">915 914 064</div>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-primary cuentas_bancarias">
              <div class="panel-heading bcp panel-head">
                <h3 class="panel-title">Paypal</h3>
              </div>
              <div class="panel-body panel-body_v2">
                <div class="col-xs-12"><b>TITULAR</b></div>
                <div class="col-xs-12">velasquezruiz.manuel24@gmail.com</div>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="panel panel-primary cuentas_bancarias" style="border: 1px solid #dbc261 !important;">
              <div class="panel-heading panel-head" style="background-color: #111 !important;border: 1px solid #dbc261 !important;">
                <h3 class="panel-title">Binance</h3>
              </div>
              <div class="panel-body panel-body_v2">
                <div class="col-xs-12"><b>TITULAR</b></div>
                <div class="col-xs-12">jhero33tlc@gmail.com</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Salir</button>
      </div>
    </div>
  </div>
</div>

    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery-3.2.1.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bower_components/bootstrap/dist/js/bootstrap.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
	<!--<script type="text/javascript" src="<?php echo base_url("bower_components/select2/dist/js/select2.full.min.js"); ?>"></script>-->
    <script type="text/javascript" src="<?php echo base_url() . 'plugins/input-mask/jquery.inputmask.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("assets/js/inicio.js?ver=2.4.20"); ?>"></script>
	<script> var base_url = '<?php echo base_url(); ?>'; </script>
</body>