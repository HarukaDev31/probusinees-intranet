
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-lg-12 col-12">
          <h1 class="mb-3">Inicio</h1>
        </div>

        <?php
        //echo $this->user->ID_Usuario . "<br>";
        //echo $this->user->Nu_Tipo_Privilegio_Acceso;
        //array_debug($arrResponsePedidoXUsuario);

        if($arrResponsePedidoXUsuario['status']=='success'){ ?>
          <?php
          $iCantidadGaranizado = 0;
          $iCantidadPagado = 0;
          //->where_in($this->table . '.Nu_Estado', array(2,3,4,8));//garantizados
          //->where_in($this->table . '.Nu_Estado', array(5,6,7,9));//pagados / oc
          foreach ($arrResponsePedidoXUsuario['result'] as $row) {
            $iCantidadGaranizado += (($row->Nu_Estado_Pedido == 2 || $row->Nu_Estado_Pedido == 3 || $row->Nu_Estado_Pedido == 4 || $row->Nu_Estado_Pedido == 8) ? 1 : 0);
            $iCantidadPagado += (($row->Nu_Estado_Pedido == 5 || $row->Nu_Estado_Pedido == 6 || $row->Nu_Estado_Pedido == 7 || $row->Nu_Estado_Pedido == 9) ? 1 : 0);
          ?>
          <?php } ?>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $iCantidadGaranizado; ?></h3>
                <p>Pedidos Garantizados</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $iCantidadPagado; ?></h3>
                <p>O/C Aprobadas</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <?php //array_debug($this->user); ?>
            <?php //array_debug($this->notificaciones); ?>
            <?php //array_debug($arrResponsePedidoXUsuario); ?>

            <div class="card card-dark">
              <div class="card-header text-center border-0 pb-2 pt-2">
                <h4 class="mb-0">Cotizaciones Garantizadas / O.C. Aprobadas</h4>
              </div>
              
              <div class="card-body">
                <div id="accordion">
                  <?php foreach ($arrResponsePedidoXUsuario['result'] as $row) { ?>
                  <?php
                    $arrResponseVerificarProceso = $this->ConfiguracionModel->verificarEstadoProcesoAgenteCompra($row->ID_Pedido_Cabecera);
                    //echo count($arrResponseVerificarProceso);
                    //array_debug($arrResponseVerificarProceso);
                    $iCantidadPasos = count($arrResponseVerificarProceso);
                    $iProgreso=0;
                    //$btn_editar_cliente = '';
                    $btn_editar_cliente = '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="editarCliente(' . $row->ID_Entidad . ')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
                    foreach ($arrResponseVerificarProceso as $row_progreso) {
                      $iProgreso+=($row_progreso->Nu_Estado_Proceso == 1 ? 1 : 0);
                      //$btn_editar_cliente = '';
                      //if($row_progreso->Nu_ID_Interno==1 && $row_progreso->Nu_Estado_Proceso == 0) {//1=pedido pagado
                        //$btn_editar_cliente = '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="editarCliente(' . $row->ID_Entidad . ')"><i class="far fa-edit fa-2x" aria-hidden="true"></i></button>';
                      //}
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
                                <div>Cliente: <?php echo $row->No_Contacto; ?> / Empresa: <?php echo $row->No_Entidad; ?> <?php echo $btn_editar_cliente; ?></div>
                              </div>
                              <div class="col-2 col-sm-2 text-right">
                                <span class="badge bg-primary"><?php echo $iProgreso; ?> / <?php echo $iCantidadPasos; ?></span>
                              </div>
                            </div>
                          </a>
                        </h4>
                      </div>
                      <div id="collapse-<?php echo $row->ID_Pedido_Cabecera; ?>" class="collapse" data-parent="#accordion" style="">
                        <?php
                        $arrResponseVerificarProcesoDetalle = $this->ConfiguracionModel->obtenerPedidosXUsuarioDetalle($row->ID_Pedido_Cabecera);
                        if ($arrResponseVerificarProcesoDetalle['status']=='success') {
                          $iCantidadLineaCarga = 0;
                          foreach ($arrResponseVerificarProcesoDetalle['result'] as $row_menu) {
                            $iCantidadLineaCarga += ($row_menu->Nu_Estado_Proceso==1 ? round((100 / $iCantidadPasos), 0) : 0);
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
                                <?php
                                foreach ($arrResponseVerificarProcesoDetalle['result'] as $row_menu) {
                                  $a_href = '';
                                  $btn_tarea = '';
                                  if ($row_menu->Nu_ID_Interno==18){//paso 1 - trading
                                    $iIdTareaPedido = 18;
                                    $a_href = 'alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="revisionBL(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="revisionBL(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-check" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==19){//paso 2 - trading
                                    $iIdTareaPedido = 19;
                                    $a_href = 'href="' . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera . '"';
                                    $btn_tarea = '<button class="btn btn-xs btn-link" alt="Pagar proveedor" title="Pagar proveedor" href="javascript:void(0)"  onclick="pagarProveedores(\'' . $row->ID_Pedido_Cabecera . '\', 1)"><i class="fas fa-money-bill-alt" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==20){//paso 3 - trading
                                    $iIdTareaPedido = 20;
                                    $a_href = 'href="' . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera . '"';
                                    $btn_tarea = '<button class="btn btn-xs btn-link" alt="Inspeccion" title="Inspeccion" href="javascript:void(0)"  onclick="bookingInspeccion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\', \'' . $row->ID_Usuario_Interno_China . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-search fa-2x" aria-hidden="true"></i></button>';
                                  }
                                ?>
                                  <!--<a href="<?php echo base_url() . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera; ?>">-->
                                  <a <?php echo $a_href; ?>>
                                    <i class="fa fa-check-circle check-gestion <?php echo ($row_menu->Nu_Estado_Proceso == 1 ? 'active' : ''); ?>"></i>
                                    <div>
                                      <label>
                                        <?php echo $row_menu->No_Proceso . ' ' . $btn_tarea; ?>
                                      </label>
                                    </div>
                                  </a>
                                  <!--</a>-->
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
          </div><!-- col-12-->
        <?php } ?>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

<!-- Modal cliente -->
<div class="modal fade modal-cliente" id="modal-default">
  <?php $attributes = array('id' => 'form-cliente'); echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">Cliente</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="hidden-cliente_modal-ID_Entidad" name="ID_Entidad" class="form-control" autocomplete="off">
          <input type="hidden" id="hidden-cliente_modal-ENo_Entidad" name="ENo_Entidad" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-8">
            <label>Empresa</label>
            <div class="form-group">
              <input type="text" name="No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>RUC</label>
            <div class="form-group">
              <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar" maxlength="11" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-8">
            <label>Cliente</label>
            <div class="form-group">
              <input type="text" name="No_Contacto" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>DNI</label>
            <div class="form-group">
              <input type="text" id="txt-Nu_Documento_Identidad_Externo" name="Nu_Documento_Identidad_Externo" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar" maxlength="8" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_cliente" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal cliente -->

<!-- Modal cliente -->
<div class="modal fade modal-cliente_modal_paso1" id="modal-default">
  <?php $attributes = array('id' => 'form-cliente_modal_paso1'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Verificar datos de Exportación</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="cliente_modal_paso1-ID_Pedido_Cabecera" name="cliente_modal_paso1-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" id="cliente_modal_paso1-iIdTareaPedido" name="cliente_modal_paso1-iIdTareaPedido" class="form-control" autocomplete="off">
          <input type="hidden" id="cliente_modal_paso1-ID_Entidad" name="cliente_modal_paso1-ID_Entidad" class="form-control" autocomplete="off">
          <input type="hidden" id="cliente_modal_paso1-ENo_Entidad" name="cliente_modal_paso1-ENo_Entidad" class="form-control" autocomplete="off">
          
          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Exportador</label>
          </div>

          <div class="col-12 col-lg-4">
            <label>Razón Social</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-exportador"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-3">
            <label>Incoterms</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-Nu_Tipo_Incoterms"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-3">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-Nu_Tipo_Transporte_Maritimo"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_cliente_modal_paso1" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal cliente_modal_paso1 -->

<!-- Modal inspeccion -->
<div class="modal fade modal-booking_inspeccion" id="modal-default">
  <?php $attributes = array('id' => 'form-booking_inspeccion'); echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">Inspección</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="booking_inspeccion-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Nu_ID_Interno" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-ID_Usuario_Interno_China" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-sCorrelativoCotizacion" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Caja_Total_Booking-Actual" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Cbm_Total_Booking-Actual" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Peso_Total_Booking-Actual" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>Cajas Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Caja_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Caja_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Cbm_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Cbm_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-4">
            <label>Peso Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Peso_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Peso_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-12">
            <label>Observación</label>
            <div class="form-group">
              <input type="text" name="booking_inspeccion-No_Observacion_Inspeccion" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_booking_inspeccion" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal booking_inspeccion -->
