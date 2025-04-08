<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
 
  <!-- /.navbar -->

<!-- Content Header (Page header) -->
<section class="content-header px-4" style="
    height: 93vh;">
  <!-- Hero Section -->
  <div class="hero-section position-relative" style="background-image: url('<?php echo base_url().'assets/img/backgrounds/inicioview.png'?>');">
    <div class="container position-relative d-flex align-items-center mx-4" style="height: 100%;">
      <div class="text-white display-4 py-5" style="font-weight: 400;z-index: 3">¡Hola, bienvenido!</div>
    </div>
  </div>

  <!-- Stats Section -->
  <div class="container">
    <div class="row g-4">
      <!-- Dollar Stats -->
      <div class="col-md-6 col-lg-3 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div class="icon-container">
              <i class="bi bi-currency-dollar stat-icon"></i>
            </div>
            <div class="text-container">
              <div class="display-2 fw-bold" id="Dolars import">0M</div>
              <p class="text-muted"><small>De dólares en importaciones</small></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Stats -->
      <div class="col-md-6 col-lg-3 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div class="icon-container">
              <i class="bi bi-people stat-icon"></i>
            </div>
            <div class="text-container">
              <div class="display-2 fw-bold" id="Clients satisfied">0K</div>
              <p class="text-muted"><small>Clientes satisfechos</small></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Package Stats -->
      <div class="col-md-6 col-lg-3 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div class="icon-container">
              <i class="bi bi-box-seam stat-icon"></i>
            </div>
            <div class="text-container">
              <div class="display-2 fw-bold" id="CBM sells">0</div>
              <p class="text-muted"><small>CBM vendidos</small></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Container Stats -->
      <div class="col-md-6 col-lg-3 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div class="icon-container">
              <i class="fas fa-ship stat-icon"></i>
            </div>
            <div class="text-container">
              <div class="display-2 fw-bold" id="Containers imported">0K</div>
              <p class="text-muted"><small>Contenedores importados</small></p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<!-- /.content -->
</div>

<!-- Modal cliente -->
<div class="modal fade modal-cliente" id="modal-default">
  <?php $attributes = array('id' => 'form-cliente');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-cliente_modal_paso1');
  echo form_open('', $attributes); ?>
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

          <div class="col-12 col-lg-6 div-cliente_modal_paso1-trading">
            <label>Razón Social</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-No_Entidad"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6 div-cliente_modal_paso1-trading">
            <label>RUC</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-Nu_Documento_Identidad"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6 div-cliente_modal_paso1-consolidatrading">
            <label>Cliente</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-No_Contacto"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6 div-cliente_modal_paso1-consolidatrading">
            <label>DNI</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-Nu_Documento_Identidad_Externo"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <label>Exportador</label>
            <div class="form-group">
              <span id="cliente_modal_paso1-exportador"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>Incoterms</label>
            <div class="form-group">
              <span class="badge bg-success" id="cliente_modal_paso1-Nu_Tipo_Incoterms"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <span class="badge bg-success" id="cliente_modal_paso1-Nu_Tipo_Transporte_Maritimo"></span>
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
  <?php $attributes = array('id' => 'form-booking_inspeccion');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-reserva_booking_trading');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Reserva de Booking</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="reserva_booking_trading-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-3">
            <label>CBM Total</label>
            <div class="form-group">
              <label id="reserva_booking_trading-Qt_Cbm_Total_Booking"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-2">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <label class="badge bg-success" id="reserva_booking_trading-Nu_Tipo_Transporte_Maritimo"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-2">
            <label>Inconterms</label>
            <div class="form-group">
              <label class="badge bg-success" id="reserva_booking_trading-Nu_Tipo_Incoterms"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-5">
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
  <?php $attributes = array('id' => 'form-costos_origen_china');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">
          <strong>Costos de Origen</strong>
          &nbsp;&nbsp;<span style="font-size: 1rem;">T.C.: <span class="badge bg-success" id="costos_origen_china-tipo_cambio"></span></span>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="costos_origen_china-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" name="costos_origen_china-Ss_Tipo_Cambio" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-6">
            <label>Flete ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan" class="form-control input-decimal conversion-yuan_dolar" data-conversion_dolar='costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar' placeholder="Ingresar" maxlength="20" autocomplete="off">
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
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan" class="form-control input-decimal conversion-yuan_dolar" data-conversion_dolar='costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar' placeholder="Ingresar" maxlength="20" autocomplete="off">
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
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan" class="form-control input-decimal conversion-yuan_dolar" data-conversion_dolar='costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar' placeholder="Ingresar" maxlength="20" autocomplete="off">
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
            <label><input type="text" inputmode="text" id="costos_origen_china-No_Concepto_Pago_Cuadrilla" name="costos_origen_china-No_Concepto_Pago_Cuadrilla" class="form-control" value="Cuadrilla" maxlength="50" placeholder="" autocomplete="off"></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan" class="form-control input-decimal conversion-yuan_dolar" data-conversion_dolar='costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar' placeholder="Ingresar" maxlength="20" autocomplete="off">
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
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan" class="form-control input-decimal conversion-yuan_dolar" data-conversion_dolar='costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar' placeholder="Ingresar" maxlength="20" autocomplete="off">
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
  <?php $attributes = array('id' => 'form-docs_exportacion');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-despacho_shipper');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Despacho al Shipper / Forwarder</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-despacho_shipper">
        <div class="row">
          <input type="hidden" id="despacho_shipper-ID_Pedido_Cabecera" name="despacho_shipper-ID_Pedido_Cabecera" class="form-control">

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Datos de Shipper</label>
          </div>

          <div class="col-12 col-lg-12">
            <div class="row">
              <div class="col-12 col-lg-4">
                <label>Empresa: &nbsp;</label><span id="despacho_shipper-span-empresa"></span>
              </div>
              <div class="col-12 col-lg-4">
                <label>Coordinador(a): &nbsp;</label><span id="despacho_shipper-span-coordinador"></span>
              </div>
              <div class="col-12 col-lg-4">
                <label>Wechat: &nbsp;</label><span id="despacho_shipper-span-wechat"></span>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Verificar</label>
          </div>
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
  <?php $attributes = array('id' => 'form-revision_bl');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-entrega_docs_cliente');
  echo form_open('', $attributes); ?>
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
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox1" name="entrega_docs_cliente-Nu_Commercial_Invoice" value="option1">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox1">Commercial Invoice</label>
            </div>

            <div class="form-check form-check-inline div-bl-entrega_docs"><!-- SOLO SI ES CIF O DDP-->
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox2" name="entrega_docs_cliente-Nu_Packing_List" value="option2">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox2">BL</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox3" name="entrega_docs_cliente-Nu_BL" value="option3">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox3">FTA Detalle</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox4" name="entrega_docs_cliente-Nu_FTA" value="option4">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox4">Packing List</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox5" name="entrega_docs_cliente-Nu_FTA_Detalle" value="option5">
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
  <?php $attributes = array('id' => 'form-pagos_logisticos');
  echo form_open('', $attributes); ?>
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
              <strong>
                <h6>SubTotal ¥: <label id="pagos_logisticos-subtotal-yuan"></label></h6>
              </strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>SubTotal $: <label id="pagos_logisticos-subtotal-dolar"></label></h6>
              </strong>
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
              <strong>
                <h6>Total ¥: <label id="pagos_logisticos-total-yuan"></label></h6>
              </strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>Total $: <label id="pagos_logisticos-total-dolar"></label></h6>
              </strong>
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


<!-- modal documento -->
<div class="modal fade modal-documento_proveedor_exportacion" id="modal-documento_proveedor_exportacion">
  <?php $attributes = array('id' => 'form-documento_proveedor_exportacion');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Docs Exportación</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-documento_proveedor_exportacion">
        <div class="row">
          <input type="hidden" id="documento_proveedor_exportacion-id_cabecera" name="documento_proveedor_exportacion-id_cabecera" class="form-control">
          <input type="hidden" id="documento_proveedor_exportacion-correlativo" name="documento_proveedor_exportacion-correlativo" class="form-control">
          <div class="col-sm-12">
            <label>Invoice and PL</label>
            <div class="form-group">
              <input class="form-control" id="documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion" name="documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_documento_proveedor_exportacion" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar documento_proveedor_exportacion -->

<!-- Modal oc_reservar_pedido -->
<div class="modal fade modal-oc_reservar_pedido" id="modal-default">
  <?php $attributes = array('id' => 'form-oc_reservar_pedido');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Selección de Servicio</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="oc_reservar_pedido-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-4 col-lg-4">
            <label>Servicio</label>
            <div class="form-group">
              <select id="oc_reservar_pedido-Nu_Tipo_Servicio" name="oc_reservar_pedido-Nu_Tipo_Servicio" class="form-control" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-4 col-lg-4">
            <label>Inconterms</label>
            <div class="form-group">
              <select id="oc_reservar_pedido-Nu_Tipo_Incoterms" name="oc_reservar_pedido-Nu_Tipo_Incoterms" class="form-control" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-4 col-lg-4">
            <label>Envío</label>
            <div class="form-group">
              <select id="oc_reservar_pedido-Nu_Tipo_Transporte_Maritimo" name="oc_reservar_pedido-Nu_Tipo_Transporte_Maritimo" class="form-control" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_oc_reservar_pedido" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal oc_reservar_pedido -->

<!-- Modal oc_reservar_pedido -->
<div class="modal fade modal-pago_cliente_oc" id="modal-default">
  <?php $attributes = array('id' => 'form-pago_cliente_oc');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Pagos de Cliente y Otros</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="pago_cliente_oc-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-6 col-sm-12 col-md-6 text-left">
            <label>Pagos</label>
            <div class="form-group">
              <button type="button" class="btn btn-primary" alt="Subir pago 30%" title="Subir pago 30%" onclick="subirPago30()">Pagar 30%</button>
              <button type="button" id="btn-descargar_pago_30" class="btn btn-primary d-none" alt="Descargar pago 30%" title="Descargar pago 30%" onclick="descargarPago30()"><span id="span-pago_30"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir pago 100%" title="Subir pago 100%" onclick="subirPago100()">Pagar 70%</button>
              <button type="button" id="btn-descargar_pago_100" class="btn btn-primary d-none" alt="Descargar pago 100%" title="Descargar pago 100%" onclick="descargarPago100()"><span id="span-pago_100"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir pago servicio" title="Subir pago servicio" onclick="subirPagoServicio()">Pagar servicio</button>
              <button type="button" id="btn-descargar_pago_servicio" class="btn btn-primary d-none" alt="Descargar pago servicio" title="Descargar pago servicio" onclick="descargarPagoServicio()"><span id="span-pago_servicio"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
            </div>
          </div>

          <div class="col-6 col-sm-12 col-md-6 text-left">
            <label>Otros Pagos</label>
            <div class="form-group">
              <button type="button" class="btn btn-primary" alt="Subir Flete" title="Subir Flete" onclick="subirPagoFlete()">Pagar Flete</button>
              <button type="button" id="btn-descargar_flete" class="btn btn-primary d-none" alt="Descargar Flete" title="Descargar Flete" onclick="descargarPagoFlete()"><span id="span-flete"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir Costo Origen" title="Subir Costo Origen" onclick="subirPagoCostoOrigen()">Costo Origen</button>
              <button type="button" id="btn-descargar_costo_origen" class="btn btn-primary d-none" alt="Descargar Costo Origen" title="Descargar Costo Origen" onclick="descargarPagoCostosOrigen()"><span id="span-costo_origen"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir Costo FTA" title="Subir Costo FTA" onclick="subirPagoFTA()">Costo FTA</button>
              <button type="button" id="btn-descargar_fta" class="btn btn-primary d-none" alt="Descargar Costo FTA" title="Descargar Costo FTA" onclick="descargarPagoFTA()"><span id="span-fta"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir Costo Gastos" title="Subir Costo Gastos" onclick="subirPagoCuadrilla()">Gastos</button>
              <button type="button" id="btn-descargar_pago_cuadrilla" class="btn btn-primary d-none" alt="Descargar Costo Gastos" title="Descargar Costo Gastos" onclick="descargarPagoCuadrilla()"><span id="span-cuadrilla"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

              <button type="button" class="btn btn-primary" alt="Subir Otros Costos" title="Subir Otros Costo" onclick="subirPagoOtrosCostos()">Otros Costo</button>
              <button type="button" id="btn-descargar_otros_costos" class="btn btn-primary d-none" alt="Descargar Otros Costo" title="Descargar Otros Costo" onclick="descargarPagoOtrosCostos()"><span id="span-otros_costo"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_pago_cliente_oc" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal pago_cliente_oc -->


<!-- modal pago 30% cliente -->
<div class="modal fade modal-pago_cliente_30" id="modal-pago_cliente_30">
  <?php $attributes = array('id' => 'form-pago_cliente_30');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_30">
        <div class="row">
          <input type="hidden" id="pago_cliente_30-id_cabecera" name="pago_cliente_30-id_cabecera" class="form-control">

          <div class="col-12 col-sm-12">
            <label>Voucher pago 30%</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_30" name="pago_cliente_30" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">Empresa <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_30_Cliente" id="cbo-ID_Pais_30_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_30_Cliente" name="Fe_Pago_30_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_30_Cliente" name="Ss_Pago_30_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_30_Cliente" name="Nu_Operacion_Pago_30_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_pago_cliente_30" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago 30% cliente -->

<!-- modal pago 100% cliente -->
<div class="modal fade modal-pago_cliente_100" id="modal-pago_cliente_100">
  <?php $attributes = array('id' => 'form-pago_cliente_100');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_100">
        <div class="row">
          <input type="hidden" id="pago_cliente_100-id_cabecera" name="pago_cliente_100-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher pago 70%</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_100" name="pago_cliente_100" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_100_Cliente" id="cbo-ID_Pais_100_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_100_Cliente" name="Fe_Pago_100_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_100_Cliente" name="Ss_Pago_100_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_100_Cliente" name="Nu_Operacion_Pago_100_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_cliente_100" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago 100% cliente -->

<!-- modal pago servicio cliente -->
<div class="modal fade modal-pago_cliente_servicio" id="modal-pago_cliente_servicio">
  <?php $attributes = array('id' => 'form-pago_cliente_servicio');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_servicio">
        <div class="row">
          <input type="hidden" id="pago_cliente_servicio-id_cabecera" name="pago_cliente_servicio-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher pago servicio</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_servicio" name="pago_cliente_servicio" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_Servicio_Cliente" id="cbo-ID_Pais_Servicio_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_Servicio_Cliente" name="Fe_Pago_Servicio_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_Servicio_Cliente" name="Ss_Pago_Servicio_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_Servicio_Cliente" name="Nu_Operacion_Pago_Servicio_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_cliente_servicio" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago servicio cliente -->


<!-- modal pago flete -->
<div class="modal fade modal-pago_flete" id="modal-pago_flete">
  <?php $attributes = array('id' => 'form-pago_flete');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_flete">
        <div class="row">
          <input type="hidden" id="pago_flete-id_cabecera" name="pago_flete-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pago_flete" name="pago_flete" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="pago_flete-ID_Pais_Otros_Flete" id="pago_flete-ID_Pais_Otros_Flete" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="pago_flete-Fe_Pago" name="pago_flete-Fe_Pago" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="pago_flete-Ss_Pago_Otros_Flete" name="pago_flete-Ss_Pago_Otros_Flete" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="pago_flete-Nu_Operacion_Pago_Otros_Flete" name="pago_flete-Nu_Operacion_Pago_Otros_Flete" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_flete" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago flete -->

<!-- modal pago costos_origen -->
<div class="modal fade modal-costos_origen" id="modal-costos_origen">
  <?php $attributes = array('id' => 'form-costos_origen');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-costos_origen">
        <div class="row">
          <input type="hidden" id="costos_origen-id_cabecera" name="costos_origen-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="costos_origen" name="costos_origen" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="costos_origen-ID_Pais_Otros_Costo_Origen" id="costos_origen-ID_Pais_Otros_Costo_Origen" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="costos_origen-Fe_Pago_Otros_Costo_Origen" name="costos_origen-Fe_Pago_Otros_Costo_Origen" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="costos_origen-Ss_Pago_Otros_Costo_Origen" name="costos_origen-Ss_Pago_Otros_Costo_Origen" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen" name="costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-costos_origen" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago costos_origen -->

<!-- modal pago pago_fta -->
<div class="modal fade modal-pago_fta" id="modal-pago_fta">
  <?php $attributes = array('id' => 'form-pago_fta');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_fta">
        <div class="row">
          <input type="hidden" id="pago_fta-id_cabecera" name="pago_fta-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pago_fta" name="pago_fta" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="pago_fta-ID_Pais_Otros_Costo_Fta" id="pago_fta-ID_Pais_Otros_Costo_Fta" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="pago_fta-Fe_Pago_Otros_Costo_Fta" name="pago_fta-Fe_Pago_Otros_Costo_Fta" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="pago_fta-Ss_Pago_Otros_Costo_Fta" name="pago_fta-Ss_Pago_Otros_Costo_Fta" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta" name="pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_fta" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago pago_fta -->

<!-- modal pago otros_cuadrilla -->
<div class="modal fade modal-otros_cuadrilla" id="modal-otros_cuadrilla">
  <?php $attributes = array('id' => 'form-otros_cuadrilla');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-otros_cuadrilla">
        <div class="row">
          <input type="hidden" id="otros_cuadrilla-id_cabecera" name="otros_cuadrilla-id_cabecera" class="form-control">

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nombre Gasto <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="text" id="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" name="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" class="form-control" value="" maxlength="50" placeholder="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="otros_cuadrilla" name="otros_cuadrilla" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="otros_cuadrilla-ID_Pais_Otros_Cuadrilla" id="otros_cuadrilla-ID_Pais_Otros_Cuadrilla" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="otros_cuadrilla-Fe_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Fe_Pago_Otros_Cuadrilla" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="otros_cuadrilla-Ss_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Ss_Pago_Otros_Cuadrilla" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-otros_cuadrilla" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago otros_cuadrilla -->

<!-- modal pago pago_fta -->
<div class="modal fade modal-otros_costos" id="modal-otros_costos">
  <?php $attributes = array('id' => 'form-otros_costos');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-otros_costos">
        <div class="row">
          <input type="hidden" id="otros_costos-id_cabecera" name="otros_costos-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="otros_costos" name="otros_costos" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="otros_costos-ID_Pais_Otros_Costos" id="otros_costos-ID_Pais_Otros_Costos" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="otros_costos-Fe_Pago_Otros_Costos" name="otros_costos-Fe_Pago_Otros_Costos" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="otros_costos-Ss_Pago_Otros_Costos" name="otros_costos-Ss_Pago_Otros_Costos" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="otros_costos-Nu_Operacion_Pago_Otros_Costos" name="otros_costos-Nu_Operacion_Pago_Otros_Costos" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-otros_costos" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago otros_costos -->

<!-- modal fecha_entrega_shipper -->
<div class="modal fade modal-fecha_entrega_shipper" id="modal-fecha_entrega_shipper">
  <?php $attributes = array('id' => 'form-fecha_entrega_shipper');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-fecha_entrega_shipper">
        <div class="row">
          <input type="hidden" id="despacho-id_cabecera" name="despacho-id_cabecera" class="form-control">
          <input type="hidden" id="despacho-correlativo" name="despacho-correlativo" class="form-control">
          <div class="col-sm-12">
            <label>F. Entrega</label>
            <div class="form-group">
              <input type="text" name="despacho-Fe_Entrega_Shipper_Forwarder" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_fecha_entrega_shipper" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar fecha_entrega_shipper -->

<!-- Modal booking -->
<div class="modal fade modal-booking" id="modal-default">
  <?php $attributes = array('id' => 'form-booking');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Reserva de Booking</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="booking-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>Cajas Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Caja_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Cbm_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Peso Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Peso_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_booking" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal booking -->

<style>
  /* Hero Section Styles */
  .hero-section {
    margin-top: 4rem;
    background-size: cover;
    background-position: center;
    height: 450px;
    border-bottom-left-radius: 2rem;
    border-top-right-radius: 2rem;
    border-top-left-radius: 2rem;
    border-bottom-right-radius: 2rem;
    position: relative;
    z-index: 2;
  }

  .overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin-top: 100px;
    background-color: rgba(13, 110, 253, 0.3);
    border-bottom-left-radius: 2rem;
    border-top-right-radius: 2rem;
    border-top-left-radius: 2rem;
    border-bottom-right-radius: 2rem;
    z-index: 1;
  }

  /* Stats Card Styles */
  .stat-card {
    background: white;
    border-radius: 1rem;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: transform 0.2s;
    margin-top: 3rem;
  }

  .stat-card:hover {
    transform: translateY(-5px);
  }

  .stat-icon {
    font-size: 5rem;
    color: #FF7F50;
    margin-bottom: 1rem;
  }

  .display-2.fw-bold {
    font-family: 'Sora', sans-serif;
  }

  .icon-container {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .hero-section {
      height: 250px;
    }

    .stat-card {
      margin-top: 1.5rem;
    }
  }
  @media (max-width: 576px) {
    .stat-card {
      padding: 1rem;
    }

    .display-6 {
      font-size: 1.2rem;
    }
    .card-body{
      flex-direction: column;
    }
    .text-container{
      text-align: center;
    }
  }
</style>
<script>
  function animateNumber(containerId, targetNumber, duration) {
    const element = document.getElementById(containerId);
    const content = element.textContent;

    // Extrae el número y el sufijo usando una expresión regular
    const match = content.match(/^(\d+)(\D*)$/);
    if (!match) {
      console.error("El contenido no contiene un número válido.");
      return;
    }

    const startNumber = parseInt(match[1], 10); // Número inicial
    const suffix = match[2]; // Sufijo (letras o símbolos)
    const increment = (targetNumber - startNumber) / (duration / 16); // Incremento por frame
    let currentNumber = startNumber;

    const interval = setInterval(() => {
      currentNumber += increment;
      if (currentNumber >= targetNumber) {
        clearInterval(interval);
        currentNumber = targetNumber; // Asegura que llegue al número exacto
      }
      element.textContent = Math.round(currentNumber) + suffix; // Actualiza el contenido
    }, 16); // 16ms por frame
  }

  animateNumber("Dolars import", 15, 2000);
  animateNumber("Clients satisfied", 5, 2000);
  animateNumber("CBM sells", 1100, 2000);
  animateNumber("Containers imported", 10, 2000);
</script>