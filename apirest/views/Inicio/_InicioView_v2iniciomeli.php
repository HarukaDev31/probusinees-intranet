
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
          <div class="card">
            <div class="card-header text-center">
              <h2 class="text-center card-title fw-bold">Pedidos Garantizados / O.C. Aprobadas</h2>
            </div>

            <!--
            <div class="card-body">
              <div class="table-responsive">
                <table id="table-proceso_pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="">
                    <tr>
                      <th style="width: 20px">Pedido</th>
                      <th>Cliente</th>
                      <th>Servicio</th>
                      <th>Progreso</th>
                      <th style="width: 40px">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($arrResponsePedidoXUsuario['result'] as $row) { ?>
                    <?php
                      //array_debug($row);
                      $sCorrelativoCotizacion = strtoupper(substr(getNameMonth($row->Fe_Month), 0 , 3)) . str_pad($row->Nu_Correlativo,3,"0",STR_PAD_LEFT);
                    ?>
                      <tr>
                        <td><?php echo $sCorrelativoCotizacion; ?></td>
                        <td>
                          <?php echo $row->No_Contacto; ?> y DNI: <?php echo ''; ?><br>
                          Empresa: <?php echo $row->No_Entidad; ?> y RUC: <?php echo $row->Nu_Documento_Identidad; ?>
                        </td>
                        <td><?php echo ''; ?></td>
                        <td>
                          <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                          </div>
                        </td>
                        <td><span class="badge bg-danger">55%</span></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            -->
            
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
                  <div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title w-100">
                        <a class="d-block w-100" data-toggle="collapse" href="#collapse-<?php echo $row->ID_Pedido_Cabecera; ?>">
                          <div class="row">
                            <div class="col-2 col-sm-3">
                              <?php echo $sCorrelativoCotizacion; ?>
                            </div>
                            <div class="col-8 col-sm-7">
                              Cliente: <?php echo $row->No_Contacto; ?> / Empresa: <?php echo $row->No_Entidad; ?>
                            </div>
                            <div class="col-2 col-sm-2 text-right">
                              <span class="badge bg-secondary"><?php echo $iProgreso; ?> / 3</span>
                            </div>
                          </div>
                        </a>
                      </h4>
                    </div>
                    <div id="collapse-<?php echo $row->ID_Pedido_Cabecera; ?>" class="collapse" data-parent="#accordion" style="">
                      <div class="card-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid.
                      </div>
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