
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="mb-3">Inicio</h1>
          <?php //array_debug($this->user); ?>
          <?php //array_debug($this->notificaciones); ?>
          <?php //array_debug($arrResponsePedidoXUsuario); ?>

          <?php if($arrResponsePedidoXUsuario['status']=='success'){ ?>
          <div class="card card-dark">
            <div class="card-header text-center border-0 pb-2 pt-2">
              <h4 class="mb-0">Pedidos Garantizados / O.C. Aprobadas</h4>
            </div>
            
            <div class="card-body">
              <div id="accordion">
                <?php foreach ($arrResponsePedidoXUsuario['result'] as $row) { ?>
                <?php
                  //array_debug($row);
                  $arrResponseVerificarProceso = $this->ConfiguracionModel->verificarEstadoProcesoAgenteCompra($row->ID_Pedido_Cabecera);
                  $iProgreso=0;
                  foreach ($arrResponseVerificarProceso as $row_progreso) {
                    $iProgreso+=($row_progreso->Nu_Estado_Proceso == 1 ? 1 : 0);
                  }
                  //array_debug($arrResponseVerificarProceso);
                  $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
                ?>
                  <div class="card card-light">
                    <div class="card-header">
                      <h4 class="card-title w-100">
                        <a class="d-block w-100" data-toggle="collapse" href="#collapse-<?php echo $row->ID_Pedido_Cabecera; ?>">
                          <div class="row">
                            <div class="col-2 col-sm-2">
                              <label><?php echo $sCorrelativoCotizacion; ?></label>
                            </div>
                            <div class="col-8 col-sm-8">
                              <!--<span>Cliente: <?php echo $row->No_Contacto; ?> / <label class="d-none d-sm-block">Empresa: <?php echo $row->No_Entidad; ?></label></span>-->
                              <div>Cliente: <?php echo $row->No_Contacto; ?> / Empresa: <?php echo $row->No_Entidad; ?></div>
                            </div>
                            <div class="col-2 col-sm-2 text-right">
                              <span class="badge bg-primary"><?php echo $iProgreso; ?> / 4</span>
                            </div>
                          </div>
                        </a>
                      </h4>
                    </div>
                    <div id="collapse-<?php echo $row->ID_Pedido_Cabecera; ?>" class="collapse" data-parent="#accordion" style="">
                      <?php
                      $arrResponseVerificarProcesoDetalle = $this->ConfiguracionModel->obtenerPedidosXUsuarioDetalle($row->ID_Pedido_Cabecera);
                      if ($arrResponseVerificarProcesoDetalle['status']=='success' && $this->empresa->Nu_Lae_Gestion==1) {
                        $iCantidadLineaCarga = 0;
                        foreach ($arrResponseVerificarProcesoDetalle['result'] as $row_menu) {
                          $iCantidadLineaCarga += ($row_menu->Nu_Estado_Proceso==1 ? 25 : 0);
                        }
                      ?>
                      <div class="card-body pt-0 px-1 pb-0">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <div class="container-tour bg-light" style="margin: 3% 0;border-radius: 12px;">
                            <div class="container-carga">
                              <div class="box-linea">
                                <div class="linea-carga-gestion" style="width: <?php echo $iCantidadLineaCarga; ?>%;"></div>
                              </div>
                              <div class="texto-porcentaje size-load" style="">
                                <span><?php echo $iCantidadLineaCarga; ?> %</span>
                              </div>
                            </div>
                            <div class="tour-item">
                              <?php foreach ($arrResponseVerificarProcesoDetalle['result'] as $row_menu) { ?>
                                <a href="<?php echo base_url() . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera; ?>">
                                  <i class="fa fa-check-circle check-gestion <?php echo ($row_menu->Nu_Estado_Proceso == 1 ? 'active' : ''); ?>"></i>
                                  <div>
                                    <label><?php echo $row_menu->No_Proceso; ?></label>
                                  </div>
                                </a>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>