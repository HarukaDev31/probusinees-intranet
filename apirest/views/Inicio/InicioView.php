
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
                                    $a_href = 'alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="verificarDatosExportacion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="verificarDatosExportacion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-check" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==19){//paso 2 - trading
                                    $iIdTareaPedido = 19;
                                    $a_href = 'href="' . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera . '"';
                                    $btn_tarea = '<i class="fas fa-money-bill-alt" aria-hidden="true"></i>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==20){//paso 3 - trading
                                    $iIdTareaPedido = 20;
                                    $a_href = 'alt="Inspeccion" title="Inspeccion" href="javascript:void(0)"  onclick="bookingInspeccion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\', \'' . $row->ID_Usuario_Interno_China . '\', \'' . $sCorrelativoCotizacion . '\')"';
                                    $btn_tarea = '<button class="btn btn-xs btn-link" alt="Inspeccion" title="Inspeccion" href="javascript:void(0)"  onclick="bookingInspeccion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\', \'' . $row->ID_Usuario_Interno_China . '\', \'' . $sCorrelativoCotizacion . '\')"><i class="fas fa-search" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==21){//paso 4 - trading
                                    $iIdTareaPedido = 21;
                                    $a_href = 'href="' . $row_menu->Txt_Url_Menu . '/' . $sCorrelativoCotizacion . '/' . $row->ID_Pedido_Cabecera . '"';
                                    $btn_tarea = '<i class="fas fa-money-bill-alt" aria-hidden="true"></i>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==22){//paso 5 - trading
                                    $iIdTareaPedido = 22;
                                    $a_href = 'alt="Booking" title="Booking" href="javascript:void(0)"  onclick="bookingTrading(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Booking" title="Booking" href="javascript:void(0)"  onclick="bookingTrading(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-ship" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==23){//paso 6 - trading
                                    $iIdTareaPedido = 23;
                                    $a_href = 'alt="Costos Origen" title="Costos Origen" href="javascript:void(0)"  onclick="costosOrigenTradingChina(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Costos Origen" title="Costos Origen" href="javascript:void(0)"  onclick="costosOrigenTradingChina(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-money-bill-alt" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==24){//paso 7 - trading
                                    $iIdTareaPedido = 24;
                                    $a_href = 'alt="Docs Exportacion" title="Docs Exportacion" href="javascript:void(0)"  onclick="docsExportacion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Docs Exportacion" title="Docs Exportacion" href="javascript:void(0)"  onclick="docsExportacion(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-file" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==25){//paso 8 - trading
                                    $iIdTareaPedido = 25;
                                    $a_href = 'alt="Despacho al Shipper" title="Despacho al Shipper" href="javascript:void(0)"  onclick="despachoShipper(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Despacho al Shipper" title="Despacho al Shipper" href="javascript:void(0)"  onclick="despachoShipper(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-check fa-2x" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==26){//paso 9 - trading
                                    $iIdTareaPedido = 26;
                                    $a_href = 'alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="revisionBL(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Revision de BL" title="Revision de BL" href="javascript:void(0)"  onclick="revisionBL(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-warehouse fa-2x" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==27){//paso 10 - trading
                                    $iIdTareaPedido = 27;
                                    $a_href = 'alt="Entrega de Docs Cliente" title="Entrega de Docs Cliente" href="javascript:void(0)"  onclick="entregaDocsCliente(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Entrega de Docs Cliente" title="Entrega de Docs Cliente" href="javascript:void(0)"  onclick="entregaDocsCliente(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-user fa-2x" aria-hidden="true"></i></button>';
                                  }
                                  
                                  if ($row_menu->Nu_ID_Interno==28){//paso 10 - trading
                                    $iIdTareaPedido = 28;
                                    $a_href = 'alt="Pagos Logísticos" title="Pagos Logísticos" href="javascript:void(0)"  onclick="pagosLogisticos(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"';
                                    $btn_tarea = '<button type="button" class="btn btn-xs btn-link" alt="Pagos Logísticos" title="Pagos Logísticos" href="javascript:void(0)"  onclick="pagosLogisticos(\'' . $row->ID_Pedido_Cabecera . '\', \'' . $iIdTareaPedido . '\')"><i class="fas fa-truck fa-2x" aria-hidden="true"></i></button>';
                                  }
                                ?>
                                  <a <?php echo $a_href; ?>>
                                    <i class="fa fa-check-circle check-gestion <?php echo ($row_menu->Nu_Estado_Proceso == 1 ? 'active' : ''); ?>"></i>
                                    <div>
                                      <label>
                                        <?php echo $row_menu->No_Proceso . ' ' . $btn_tarea; ?>
                                      </label>
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
        <h4 class="text-center"><strong>Inspección</strong></h4>
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

<!-- Modal reserva_booking_trading -->
<div class="modal fade modal-reserva_booking_trading" id="modal-default">
  <?php $attributes = array('id' => 'form-reserva_booking_trading'); echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Reserva de Booking</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="reserva_booking_trading-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <label id="reserva_booking_trading-Qt_Cbm_Total_Booking"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-4">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <label id="reserva_booking_trading-Nu_Tipo_Transporte_Maritimo"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Shipper</label>
            <div class="form-group">
              <select id="cbo-shipper" name="reserva_booking_trading-ID_Shipper" class="form-control select2" style="width: 100%;">
                <option selected="selected" value="0"></option>
              </select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6 div-tipo_contenedor">
            <label>Tipo Contenedor</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Tipo_Contenedor" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <label>Naviera</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Naviera" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>C. Días de tránsito</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Dias_Transito" class="form-control input-number" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-4">
            <label>D. Días Libres</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Dias_Libres" class="form-control input-number" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_reserva_booking_trading" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal reserva_booking_trading -->

<!-- Modal costos_origen_china -->
<div class="modal fade modal-costos_origen_china" id="modal-default">
  <?php $attributes = array('id' => 'form-costos_origen_china'); echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Costos de Origen</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="costos_origen_china-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          
          <div class="col-6 col-lg-6">
            <label>Flete ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Flete $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Costos de Origen ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Costos de Origen $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Costos de FTA ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Costos de FTA $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label><input type="text" inputmode="text" id="costos_origen_china-No_Concepto_Pago_Cuadrilla" name="costos_origen_china-No_Concepto_Pago_Cuadrilla" class="form-control" value="Cuadrilla" maxlength="50" placeholder="" autocomplete="off"> ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>$</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Otros Costos ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-6">
            <label>Otros Costos $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_costos_origen_china" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal costos_origen_china -->

<!-- modal docs_exportacion -->
<div class="modal fade modal-docs_exportacion" id="modal-docs_exportacion">
  <?php $attributes = array('id' => 'form-docs_exportacion'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-docs_exportacion">
        <div class="row">
          <input type="hidden" id="docs_exportacion-ID_Pedido_Cabecera" name="docs_exportacion-ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" id="docs_exportacion-iIdTareaPedido" name="docs_exportacion-iIdTareaPedido" class="form-control">

          <div class="col-sm-12 div-docs_shipper">
            <label>Docs Shipper</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-12">
            <label>Commercial Invoice</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-12">
            <label>Packing List</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-12 div-bl">
            <label>BL</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-12">
            <label>FTA</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_docs_exportacion" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar docs_exportacion -->

<!-- modal despacho_shipper -->
<div class="modal fade modal-despacho_shipper" id="modal-despacho_shipper">
  <?php $attributes = array('id' => 'form-despacho_shipper'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Despacho al Shipper / Forwarder</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-despacho_shipper">
        <div class="row">
          <input type="hidden" id="despacho_shipper-ID_Pedido_Cabecera" name="despacho_shipper-ID_Pedido_Cabecera" class="form-control">

          <div class="col-12 col-lg-12">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
              <label class="form-check-label" for="inlineCheckbox1">Entrega de Carga</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
              <label class="form-check-label" for="inlineCheckbox2">Entrega de Documentos</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_despacho_shipper" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar despacho_shipper -->

<!-- Modal revision_bl -->
<div class="modal fade modal-revision_bl" id="modal-default">
  <?php $attributes = array('id' => 'form-revision_bl'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Revisión de BL</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="revision_bl-ID_Pedido_Cabecera" name="revision_bl-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-iIdTareaPedido" name="revision_bl-iIdTareaPedido" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-ID_Entidad" name="revision_bl-ID_Entidad" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-ENo_Entidad" name="revision_bl-ENo_Entidad" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Consignatario</label>
          </div>

          <div class="col-6 col-lg-4">
            <label>Empresa</label>
            <div class="form-group">
              <input type="text" name="revision_bl-No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label>RUC</label>
            <div class="form-group">
              <input type="text" name="revision_bl-Nu_Documento_Identidad" class="form-control input-Mayuscula input-number" placeholder="Ingresar" maxlength="11" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Dirección</label>
            <div class="form-group">
              <input type="text" name="revision_bl-Txt_Direccion_Entidad" class="form-control" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Exportador</label>
          </div>

          <div class="col-12 col-lg-4">
            <label>Razón Social</label>
            <div class="form-group">
              <span id="revision_bl-exportador"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Dirección</label>
            <div class="form-group">
              <span id="revision_bl-exportador_direccion"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-4">
            <label>Shipper</label>
            <div class="form-group">
              <span id="revision_bl-shipper"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Datos de Carga</label>
          </div>
          
          <div class="col-12 col-lg-3">
            <label>Cajas Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Caja_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>CBM Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Cbm_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-3">
            <label>Peso Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Peso_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-3">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <span id="revision_bl-Nu_Tipo_Transporte_Maritimo"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-12 col-lg-12">
            <label>Descripción BL</label>
            <div class="form-group">
              <textarea class="form-control" rows="5" placeholder="Obligatorio" name="revision_bl-Txt_Descripcion_BL_China"></textarea>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_revision_bl" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal revision_bl -->

<!-- modal entrega_docs_cliente -->
<div class="modal fade modal-entrega_docs_cliente" id="modal-entrega_docs_cliente">
  <?php $attributes = array('id' => 'form-entrega_docs_cliente'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Entrega de Docs - Cliente</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-entrega_docs_cliente">
        <div class="row">
          <input type="hidden" id="entrega_docs_cliente-ID_Pedido_Cabecera" name="entrega_docs_cliente-ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" id="entrega_docs_cliente-Nu_Tipo_Incoterms" name="entrega_docs_cliente-Nu_Tipo_Incoterms" class="form-control">

          <div class="col-12 col-lg-12">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox1" value="option1">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox1">Commercial Invoice</label>
            </div>

            <div class="form-check form-check-inline div-bl-entrega_docs"><!-- SOLO SI ES CIF O DDP-->
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox2" value="option2">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox2">BL</label>
            </div>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox3" value="option3">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox3">FTA Detalle</label>
            </div>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox4" value="option4">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox4">Packing List</label>
            </div>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox5" value="option5">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox5">FTA</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_entrega_docs_cliente" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar despacho_shipper -->

<!-- Modal pagos_logisticos -->
<div class="modal fade modal-pagos_logisticos" id="modal-default">
  <?php $attributes = array('id' => 'form-pagos_logisticos'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Pagos Logísticos</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="pagos_logisticos-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          
          <div class="col-12 col-lg-12">
            <span>Shipper: <label id="pagos_logisticos-shipper"></label></span>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Flete ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Flete $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3">
            <label>Costos de Origen ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3">
            <label>Costos de Origen $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
                    
          <div class="col-6 col-lg-3">
            <label>Costos de FTA ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3">
            <label>Costos de FTA $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong><h6>SubTotal ¥: <label id="pagos_logisticos-subtotal-yuan"></label></h6></strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong><h6>SubTotal $: <label id="pagos_logisticos-subtotal-dolar"></label></h6></strong>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Cuadrilla ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Cuadrilla $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Otros Costos ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Otros Costos $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong><h6>Total ¥: <label id="pagos_logisticos-total-yuan"></label></h6></strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong><h6>Total $: <label id="pagos_logisticos-total-dolar"></label></h6></strong>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_pagos_logisticos" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal pagos_logisticos -->