<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!--====== Favicon Icon ======-->
  <link rel="shortcut icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0'; ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo base_url() . 'assets/ico/favicon.ico?ver=1.0" type="image/x-icon'; ?>">

  <title>ProBusiness | Admin</title>
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/fontawesome-free/css/all.min.css"); ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/datatables-bs4/css/dataTables.bootstrap4.min.css"); ?>">
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/datatables-responsive/css/responsive.bootstrap4.min.css"); ?>">
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/datatables-buttons/css/buttons.bootstrap4.min.css"); ?>">
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/daterangepicker/daterangepicker.css"); ?>">
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"); ?>">
  
  <?php if (isset($js_permiso_usuario) && $js_permiso_usuario==true) : ?>
  <link rel="stylesheet" href="<?php echo base_url("plugins_v2/select2/css/select2.min.css"); ?>">
  <?php endif; ?>

  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url("dist_v2/css/adminlte.min.css"); ?>">
  <link rel="stylesheet" href="<?php echo base_url() . 'assets/css/style_v2.css?ver=6.2.0'; ?>">

  <meta name="theme-color" content="#FF6700">
  <meta name="msapplication-navbutton-color" content="#FF6700"/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="msapplication-navbutton-color" content="#FF6700" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <?php if (isset($js_producto_importacion) && $js_producto_importacion==true) : ?>  
  <link rel="stylesheet" href="<?php echo base_url() . 'plugins_v2/summernote/summernote-bs4.min.css'; ?>">
  <?php endif; ?>

</head>
<body class="hold-transition sidebar-mini">
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

  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?php echo base_url() . 'LoginController/logout'; ?>" role="button">
            <i class="text-danger fas fa-power-off"></i>
            Salir
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="<?php echo base_url() . 'InicioController'; ?>" class="brand-link">
        <img src="<?php echo base_url() . 'dist_v2/img/logos/isotipo_probusiness.png'; ?>" alt="ProBusiness" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><strong>ProBusiness</strong></span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="<?php echo base_url() . 'dist_v2/img/user_all.png?ver=1.0.0'; ?>" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block"><?php echo $this->user->No_Nombres_Apellidos; ?></a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">Menú</li>
            <?php
            foreach($this->menu as $arrMenuPadre):
              $menu_padre = explode('/', $this->router->directory);
              $menu_padre = $menu_padre[0];
              $No_Class_Li_Padre = "nav-item";
              $No_Class_A_Padre_Active = "";
              if ($menu_padre != $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre > 0) {
                $No_Class_Li_Padre = "nav-item";
                $No_Class_A_Padre_Active = "";
              } else if ($menu_padre == $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre > 0) {
                $No_Class_Li_Padre = "nav-item active menu-open";
                $No_Class_A_Padre_Active = "active";
              } else if ($this->router->class == $arrMenuPadre->No_Class_Controller && $arrMenuPadre->Nu_Cantidad_Menu_Padre == 0) {
                $No_Class_Li_Padre = "nav-item active menu-open";
                $No_Class_A_Padre_Active = "active";
              }
            ?>
            <li class="<?php echo $No_Class_Li_Padre; ?>">
              <?php if ($arrMenuPadre->ID_Padre == 0){ ?>
                <a class="nav-link <?php echo $No_Class_A_Padre_Active; ?>" title="<?php echo $arrMenuPadre->No_Menu; ?>" href="<?php echo base_url() . $arrMenuPadre->No_Menu_Url; ?>">
                  <i class="nav-icon <?php echo $arrMenuPadre->Txt_Css_Icons; ?>"></i>
                  <p>&nbsp;<?php echo $arrMenuPadre->No_Menu; ?></p>
                  <?php if($arrMenuPadre->Nu_Cantidad_Menu_Padre > 0): ?>
                    <i class="right fas fa-angle-left"></i>
                  <?php endif; ?>
                </a>
                <?php if($arrMenuPadre->Nu_Cantidad_Menu_Padre > 0): ?>
                <ul class="nav nav-treeview">
                  <?php
                  foreach($arrMenuPadre->Hijos as $arrHijos):
                    $No_Class_Li = "nav-item";
                    if ($this->router->directory != $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos > 0)
                      $No_Class_Li = "nav-item";
                    else if ($this->router->directory == $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos > 0)
                      $No_Class_Li = "nav-item active  menu-open";
                    else if ($this->router->class == $arrHijos->No_Class_Controller && $arrHijos->Nu_Cantidad_Menu_Hijos == 0)
                      $No_Class_Li = "nav-item active  menu-open"; ?>
                    <li class="<?php echo $No_Class_Li; ?>">
                      <a class="nav-link 1 <?php echo ($this->router->class == $arrHijos->No_Class_Controller ? 'nav-item active' : 'nav-item'); ?> <?php echo $No_Class_Li; ?>" title="<?php echo $arrHijos->No_Menu; ?>" href="<?php echo base_url() . $arrHijos->No_Menu_Url; ?>">
                        <i class="<?php echo $arrHijos->Txt_Css_Icons; ?>"></i>
                        <p>&nbsp;<?php echo $arrHijos->No_Menu; ?></p>
                        <?php if($arrHijos->Nu_Cantidad_Menu_Hijos > 0): ?>
                        <i class="right fas fa-angle-left"></i>
                        <?php endif; ?>
                      </a>
                      <?php if($arrHijos->Nu_Cantidad_Menu_Hijos > 0): ?>
                      <ul class="nav nav-treeview">
                        <?php foreach($arrHijos->SubHijos as $arrSubHijos): ?>
                        <li class="<?php echo ($this->router->class == $arrSubHijos->No_Class_Controller ? 'nav-item active' : 'nav-item'); ?>">
                          <a class="nav-link 2 <?php echo ($this->router->class == $arrSubHijos->No_Class_Controller ? 'nav-item active' : 'nav-item'); ?>" title="<?php echo $arrSubHijos->No_Menu; ?>" href="<?php echo base_url() . $arrSubHijos->No_Menu_Url; ?>">
                            <i class="<?php echo $arrSubHijos->Txt_Css_Icons; ?>"></i>
                            <p>&nbsp;<?php echo $arrSubHijos->No_Menu; ?></p>
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
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>