<?php //defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="color-scheme" content="light dark">
  <title>ProBusiness | Admin</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap/dist/css/bootstrap.min.css'; ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/font-awesome/css/font-awesome.min.css'; ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/Ionicons/css/ionicons.min.css'; ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'; ?>">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/select2/dist/css/select2.min.css'; ?>">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/morris.js/morris.css'; ?>">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/jvectormap/jquery-jvectormap.css'; ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-daterangepicker/daterangepicker.css'; ?>">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo base_url() . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo base_url() . 'plugins/iCheck/all.css'; ?>">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url() . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'; ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url() . 'dist/css/AdminLTE.min.css'; ?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url() . 'dist/css/skins/_all-skins.min.css'; ?>">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- laesystems -->
  <meta name="Author" content="laesystems">
  <meta name="Subject" content="Desarrollamos soluciones innovadoras">
  <meta name="Copyright" content="Copyright © laesystems. Todos los derechos reservados.">

  <link rel="stylesheet" href="<?php echo base_url() . 'assets/dropzone/css/dropzone.min.css'; ?>">
  <link rel="stylesheet" href="<?php echo base_url() . 'assets/css/style.css?ver=8.35.7'; ?>">

  <!--====== Favicon Icon ======-->
  <link rel="shortcut icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0'; ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0" type="image/x-icon'; ?>">

  
  <link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/css/datatables/buttons.bootstrap.min.css'; ?>">
  
  <meta name="theme-color" content="#150f0f">
  <meta name="msapplication-navbutton-color" content="#150f0f"/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
  <meta name="theme-color" content="#150f0f" />  
  <meta name="msapplication-navbutton-color" content="#150f0f" />
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

<body class="hold-transition sidebar-mini skin-purple <?php echo (($this->session->usuario->Nu_Setting_Panel_Menu_Izquierdo == 0 && (!isset($iOcultarMenuIzquierdo) || $iOcultarMenuIzquierdo==0)) ? '' : 'sidebar-collapse'); ?>">
<!-- MENSAJE DE ALERTA DE VENCIMIENTO DE LAESHOP-->
<?php
$arrFechaActualHeader = explode('-', dateNow('fecha'));
$dFechaActualHeader = $arrFechaActualHeader[2] . '-' . $arrFechaActualHeader[1] . '-' . $arrFechaActualHeader[0];
$dFechaActualHeader = date("d-m-Y",strtotime($dFechaActualHeader."+ 5 days"));
$arrFechaActualHeader = explode('-', $dFechaActualHeader);
$dFechaActualHeader = $arrFechaActualHeader[2] . '-' . $arrFechaActualHeader[1] . '-' . $arrFechaActualHeader[0];
if ($this->user->ID_Pais == 1 && ($this->empresa->Nu_Vendedor_Dropshipping == 1 || $this->empresa->Nu_Tienda_Virtual_Propia == 1) && $this->empresa->Nu_Lae_Shop == 1 && !empty($this->empresa->Fe_Vencimiento_Laeshop) && $dFechaActualHeader >= $this->empresa->Fe_Vencimiento_Laeshop) {
?>
<div class="callout callout-danger" style="margin-bottom: 0px;border-radius: 0px;padding: 7px 5px;">
  <h5 style="margin-bottom: 0px;margin-top: 0px;">
    <span class="label label-dark" style="font-size: 1.3rem !important; font-weight: 400 !important; background: #7707e8;">Tienda</span>
    <span style="font-size: 13px;">Válido hasta el <?php
      $dVencimientoLaeshop = date_create($this->empresa->Fe_Vencimiento_Laeshop);
      date_add($dVencimientoLaeshop,date_interval_create_from_date_string("1 days"));
      $dVencimientoLaeshop = date_format($dVencimientoLaeshop,"Y-m-d");
      echo DateFormat(ToDateBD($dVencimientoLaeshop), 6);
      ?>
    </span>
    &nbsp;&nbsp;<button type="button" class="btn btn-success" style="padding: 5px 25px; font-size: 16px;" data-toggle="modal" data-target="#modal-pago_cuenta_bancarias_laesystems">Pagar aquí</button>
  </h5>
</div>
<!-- FIN MENSAJE DE ALERTA DE VENCIMIENTO DE LAESHOP-->
<?php
}
?>

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
		        <div class="well well-sm alert-warning text-left">
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

<div class="modal fade in" id="modal-soporte" style="padding-right: 17px;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        <h3 class="modal-title text-center">Soporte</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="well well-sm text-left"><strong>Horario de Atención:</strong> <br class="hidden-sm hidden-md hidden-lg"> Lunes a Sábado: 9 am - 6 pm</div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="well well-sm text-left">
              <a href="<?php echo base_url() . 'TiendaVirtual/PedidosTiendaVirtualController/preguntasFrecuentes'; ?>" alt="Preguntas Frecuentes" title="Preguntas Frecuentes" target="_blank" rel="noopener noreferrer">
                <strong>¿Preguntas Frecuentes (FAQ's)?</strong>
              </a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12 col-sm-4 text-center hidden">
            <div class="well well-sm text-center">
              <strong class="text-center">WhatsApp</strong><br>
              <a href="https://api.whatsapp.com/send?phone=51960986108&text=Necesito%20mas%20informacion" alt="EcxpressLae" title="EcxpressLae" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>960 986 108</span>
              </a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="well well-sm text-center">
              <strong class="text-center">Instagram</strong><br>
              <a href="https://www.instagram.com/ecxlae.oficial" alt="ProBusiness" title="ProBusiness" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-instagram fa-2x" style="color: #eb00ff;"></i>&nbsp;&nbsp;&nbsp; <span>@ecxlae.oficial</span>
              </a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="well well-sm text-center">
              <strong class="text-center">TikTok</strong><br>
              <a href="https://www.tiktok.com/@ecxlae.oficial" alt="ProBusiness" title="ProBusiness" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-music" style="color: #2e62ff;font-size: 2.7rem;color: #111111;filter: drop-shadow(2px 0px 0px #FD3E3E) drop-shadow(-2px -2px 0px #4DE8F4);"></i>&nbsp;&nbsp;&nbsp; <span>@ecxlae.oficial</span>
              </a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="well well-sm text-center">
              <strong class="text-center">YouTube</strong><br>
              <a href="https://www.youtube.com/channel/UCbSUijZj83pHJWxg5j_xsfQ" alt="ProBusiness" title="ProBusiness" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-youtube-play fa-2x" style="color: #c4302b;"></i>&nbsp;&nbsp;&nbsp; <span>@ecxlae.oficial</span>
              </a>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <strong class="text-center">Soporte Tienda 1</strong><br>
              <a href="https://api.whatsapp.com/send?phone=51904179541&text=Hola%20Leyna%20Necesito%20ayuda%20con%20Soporte" alt="EcxpressLae" title="EcxpressLae" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;<span>(+51) 904 179 541</span>
              </a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <strong class="text-center">Soporte Tienda 2</strong><br>
              <a href="https://api.whatsapp.com/send?phone=51948733094&text=Hola%20Josspeh%20Necesito%20ayuda%20con%20Soporte" alt="EcxpressLae" title="EcxpressLae" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>(+51) 948 733 094</span>
              </a>
            </div>
          </div>

          <?php if($this->user->ID_Pais == 1) { ?>
          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <span>Soporte </span><strong class="text-center">Coordinado</strong><br>
              <a href="https://api.whatsapp.com/send?phone=51986224023&text=Hola%20Cecilia%20Necesito%20ayuda%20con%20Pedido%20Coordinado" alt="EcxpressLae Coordinado" title="EcxpressLae Coordinado" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>(+51) 986 224 023</span>
              </a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <span>Soporte </span><strong class="text-center">CallCenter</strong><br>
              <a href="https://api.whatsapp.com/send?phone=51986224023&text=Hola%20Belen%20Necesito%20ayuda%20con%20Pedido%20CallCenter" alt="EcxpressLae CallCenter" title="EcxpressLae CallCenter" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>(+51) 986 224 023</span>
              </a>
            </div>
          </div>
          <?php } ?>

          <?php if($this->user->ID_Pais == 2) { ?>
          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <span>Soporte </span><strong class="text-center">Coordinado</strong><br>
              <a href="https://api.whatsapp.com/send?phone=5215561805920&text=Hola%20Cecilia%20Necesito%20ayuda%20con%20Pedido%20Coordinado" alt="EcxpressLae Coordinado" title="EcxpressLae Coordinado" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>(+52) 1 55 6180 5920</span>
              </a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <span>Soporte </span><strong class="text-center">CallCenter</strong><br>
              <a href="https://api.whatsapp.com/send?phone=525611763134&text=Hola%20Belen%20Necesito%20ayuda%20con%20Pedido%20CallCenter" alt="EcxpressLae CallCenter" title="EcxpressLae CallCenter" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;&nbsp;&nbsp; <span>(+52) 56 1176 3134</span>
              </a>
            </div>
          </div>
          <?php } ?>

          <div class="col-xs-12 col-sm-6 text-center">
            <div class="well well-sm text-center">
              <strong class="text-center">Soporte Campañas</strong><br>
              <a href="https://api.whatsapp.com/send?phone=524613431132&text=Hola%20Carlos%20Necesito%20ayuda%20con%20Campaña" alt="EcxpressLae" title="EcxpressLae" target="_blank">
                <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i>&nbsp;<span>(+52) 461 343 1132</span>
              </a>
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

<div class="wrapper">
  <input type="hidden" id="hidden-sDirectory" name="sDirectory" class="form-control" value="<?php echo $this->router->directory; ?>">
  <input type="hidden" id="hidden-sClass" name="sClass" class="form-control" value="<?php echo $this->router->class; ?>">
  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
  <input type="hidden" id="hidden-Nu_Documento_Identidad-empresa" name="Nu_Documento_Identidad_Empresa" class="form-control" value="<?php echo $this->empresa->Nu_Documento_Identidad; ?>">
  <input type="hidden" id="hidden-iTipoRubroEmpresa" name="iTipoRubroEmpresa" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Rubro_Empresa; ?>">
  <input type="hidden" id="hidden-Nu_Tipo_Lenguaje_Impresion_Pos" name="Nu_Tipo_Lenguaje_Impresion_Pos" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Lenguaje_Impresion_Pos; ?>">
  <input type="hidden" id="hidden-Nu_Activar_Descuento_Punto_Venta" name="Nu_Activar_Descuento_Punto_Venta" class="form-control" value="<?php echo $this->empresa->Nu_Activar_Descuento_Punto_Venta; ?>">
  <input type="hidden" id="hidden-Nu_Agregar_Almacen_Virtual" name="Nu_Agregar_Almacen_Virtual" class="form-control" value="<?php echo $this->empresa->Nu_Agregar_Almacen_Virtual; ?>">
  <input type="hidden" id="hidden-Nu_Tipo_Proveedor_FE" name="Nu_Tipo_Proveedor_FE" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Proveedor_FE; ?>">
  <input type="hidden" id="hidden-No_Predeterminado_Formato_PDF_POS" name="No_Predeterminado_Formato_PDF_POS" class="form-control" value="<?php echo $this->empresa->No_Predeterminado_Formato_PDF_POS; ?>">
  <input type="hidden" id="hidden-Nu_Activar_Redondeo" name="Nu_Activar_Redondeo" class="form-control" value="<?php echo $this->empresa->Nu_Activar_Redondeo; ?>">

  <!-- PARA SABER TIPO DE SERVICIO PERO TMB VALIDAMOS POR BACKEND -->
  <input type="hidden" id="hidden-Nu_Lae_Gestion" name="Nu_Lae_Gestion" class="form-control" value="<?php echo $this->empresa->Nu_Lae_Gestion; ?>">
  <input type="hidden" id="hidden-Nu_Tipo_Plan_Lae_Gestion" name="Nu_Tipo_Plan_Lae_Gestion" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Plan_Lae_Gestion; ?>">
  <input type="hidden" id="hidden-Nu_Lae_Shop" name="Nu_Lae_Shop" class="form-control" value="<?php echo $this->empresa->Nu_Lae_Shop; ?>">
  <input type="hidden" id="hidden-Nu_Tipo_Plan_Lae_Shop" name="Nu_Tipo_Plan_Lae_Shop" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Plan_Lae_Shop; ?>">

  <input type="hidden" id="hidden-ID_Pais_Usuario" name="ID_Pais_Usuario" class="form-control" value="<?php echo $this->user->ID_Pais; ?>">
  <input type="hidden" id="hidden-No_Signo_Global" name="No_Signo_Global" class="form-control" value="<?php echo $this->user->No_Signo; ?>">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo base_url() . 'InicioController'; ?>" class="logo hidden-xs">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>Pro</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>ProBusiness</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button a-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>&nbsp;&nbsp;&nbsp;
        <span class="logo-lg hidden-sm hidden-md hidden-lg" style="font-size: 14px;font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight: 400;line-height: 1.42857143;"><b>EcX</b>lae</span>
      </a>
      
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Empresa y organizacion -->
          <li class="dropdown" id="header-a-id_empresa" value="<?php echo $this->empresa->ID_Empresa; ?>"></li>
          <li class="dropdown" id="header-a-id_organizacion" value="<?php echo $this->empresa->ID_Organizacion; ?>"></li>
          <li class="dropdown" id="header-a-id_almacen" value="<?php echo $this->session->userdata['almacen']->ID_Almacen; ?>"></li>
          <li class="dropdown" id="header-a-id_tipo_documento_venta_predeterminado" value="<?php echo $this->empresa->Nu_ID_Tipo_Documento_Venta_Predeterminado; ?>"></li>
          
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" >
              <img src="<?php echo base_url() . 'assets/img/usuarios/foto_usuario.png'; ?>" class="user-image" alt="User Image" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="<?php echo "(" . $this->session->userdata['usuario']->No_Grupo . ") " . $this->session->userdata['usuario']->No_Usuario; ?>">
              <span class="hidden-xs hidden-sm" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="<?php echo "(" . $this->session->userdata['usuario']->No_Grupo . ") " . $this->session->userdata['usuario']->No_Usuario; ?>"><?php echo $this->session->userdata['usuario']->No_Nombres_Apellidos; ?>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-12 text-left">
                    <div class="form-group">
                      <a href="<?php echo base_url() . 'PanelAcceso/UsuarioController/listarUsuarios/' . $this->user->No_Usuario; ?>" style="color: #ffffff !important;background-color: #00a65a !important;border-color: #008d4c !important;" class="btn btn-success btn-block text-white"><span class="fa fa-user"></span> Mi Perfil</a>
                    </div>
                  </div>
                </div>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="col-xs-12 text-center">
                  <a href="<?php echo base_url() . 'LoginController/logout' ?>" class="btn btn-danger btn-block btn-mobile_cerrar_sesion"><span class="fa fa-sign-out"></span> Cerrar sesión</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar main-sidebar-mobile">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel"><!--pasar por hidden su id cuando sea mas 1 organization or warehouse add select combobox-->
        <div class="col-xs-12 text-left hidden">
          <div class="form-group">
          <?php
          if ($this->empresa->Nu_Tipo_Proveedor_FE==1)
            $sTipoProveedor = '<span class="label label-primary">PSE N</span>';
          else if ($this->empresa->Nu_Tipo_Proveedor_FE==2)
            $sTipoProveedor = '<span class="label label-danger">SUNAT</span>';
          else
            $sTipoProveedor = '<span class="label label-dark">INTERNO</span>';
          ?>
            <a href="#" alt="Empresa: <?php echo $this->empresa->No_Empresa; ?>" title="Empresa: <?php echo $this->empresa->No_Empresa; ?>"><?php echo $sTipoProveedor . ' ' . $this->empresa->No_Empresa; ?></a>
          </div>
        </div>
        
        <div class="col-xs-12 text-left hidden">
          <div class="form-group">
            <a href="#" alt="Organización: <?php echo $this->empresa->No_Organizacion; ?>" title="Organización: <?php echo $this->empresa->No_Organizacion; ?>"><?php echo $this->empresa->No_Organizacion; ?></a>
          </div>
        </div>
        
        <div class="col-xs-12 text-left" style="margin-bottom: -14px !important;">
          <div class="form-group">
            <a href="#">Almacén</a>
            <select id="cbo-almacen" class="form-control required" style="width: 100%;">
              <?php
                $arrResponseAlmacen = $this->almacen;
                if ($arrResponseAlmacen['sStatus'] == 'success') {
                  foreach($arrResponseAlmacen['arrData'] as $row){
                    $selected= '';
                    if ( $this->session->userdata['almacen']->ID_Almacen == $row->ID_Almacen )
                      $selected= 'selected';
                  ?>
				            <option value="<?php echo $row->ID_Almacen; ?>" title="<?php echo $row->No_Almacen; ?>" <?php echo $selected; ?>><?php echo $row->No_Almacen; ?></option>
                  <?php
                  }
                }
              ?>
            </select>
          </div>
        </div>
      </div>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">Menú</li>
	      <?php
        foreach($this->menu as $arrMenuPadre):
          $menu_padre = explode('/', $this->router->directory);
          $menu_padre = $menu_padre[0];
          $No_Class_Li_Padre = "";
          if ($menu_padre != $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre > 0)
            $No_Class_Li_Padre = "treeview";
          else if ($menu_padre == $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre > 0)
            $No_Class_Li_Padre = "active treeview";
          else if ($this->router->class == $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre == 0)
            $No_Class_Li_Padre = "active";
	      ?>
	      <li class="<?php echo $No_Class_Li_Padre; ?>">
      	  <?php if ($arrMenuPadre->ID_Padre == 0){ ?>
      	    <a title="<?php echo $arrMenuPadre->No_Menu; ?>" href="<?php echo base_url() . $arrMenuPadre->No_Menu_Url; ?>">
              <i class="<?php echo $arrMenuPadre->Txt_Css_Icons; ?>"></i>
		      		<span><?php echo $arrMenuPadre->No_Menu; ?></span>
		      		<?php if($arrMenuPadre->Nu_Cantidad_Menu_Padre > 0): ?>
				      <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				      <?php endif; ?>
            </a>
		      	<?php if($arrMenuPadre->Nu_Cantidad_Menu_Padre > 0): ?>
            <ul class="treeview-menu">
				      <?php
				        foreach($arrMenuPadre->Hijos as $arrHijos):
				          $No_Class_Li = "";
				          if ($this->router->directory != $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos > 0)
				            $No_Class_Li = "treeview";
				          else if ($this->router->directory == $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos > 0)
				            $No_Class_Li = "treeview active";
				          else if ($this->router->class == $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos == 0)
				            $No_Class_Li = "active";?>
				          <li class="<?php echo $No_Class_Li; ?>">
                    <a title="<?php echo $arrHijos->No_Menu; ?>" href="<?php echo base_url() . $arrHijos->No_Menu_Url; ?>">
                      <i class="<?php echo $arrHijos->Txt_Css_Icons; ?>"></i> <?php echo $arrHijos->No_Menu; ?>
        		      		<?php if($arrHijos->Nu_Cantidad_Menu_Hijos > 0): ?>
        				      <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        				      <?php endif; ?>
                    </a>
                    <?php if($arrHijos->Nu_Cantidad_Menu_Hijos > 0): ?>
                    <ul class="treeview-menu">
				              <?php foreach($arrHijos->SubHijos as $arrSubHijos): ?>
                      <li class="<?php echo ($this->router->class == $arrSubHijos->No_Class_Controller ? 'active' : ''); ?>">
                        <a title="<?php echo $arrSubHijos->No_Menu; ?>" href="<?php echo base_url() . $arrSubHijos->No_Menu_Url; ?>">
                          <i class="<?php echo $arrSubHijos->Txt_Css_Icons; ?>"></i> <?php echo $arrSubHijos->No_Menu; ?>
                        </a>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                  </li>
              <?php
                endforeach; ?>
            </ul>
            <?php endif; ?>
			    <?php } ?>
			    </li>
	      <?php endforeach; ?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>