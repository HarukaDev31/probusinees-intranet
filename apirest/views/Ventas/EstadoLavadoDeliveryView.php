<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header"></section>
  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br><div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-estado_documento" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="9">Completado Error</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
    		  				  <option value="11">Anulado Error</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-6">
                <div class="form-group">
                  <label>Nombre Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2" style="display:none">
                <div class="form-group">
                  <label>Recepción</label>
    		  				<select id="cbo-tipo_recepcion_cliente" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Empresa</option>
    		  				  <option value="2">Delivery</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                  <label>Estado Lavado</label>
    		  				<select id="cbo-estado_lavado" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Pendiente envío (Sede)</option>
                    <option value="16">Pedido enviado (Sede)</option>
    		  				  <option value="2">Planta</option>
                    <option value="3">Lavado al agua (iniciado)</option>
                    <option value="4">Lavado al agua (finalizado)</option>
                    <option value="5">Lavado al seco (iniciado)</option>
                    <option value="6">Lavado al seco (finalizado)</option>
                    <option value="7">Planchado</option>
                    <option value="8">Doblado</option>
                    <option value="9">Embolsado</option>
                    <option value="11">Pendiente envío Servicio Tercerizado Externo</option>
                    <option value="12">Enviar Servicio Tercerizado Externo</option>
                    <option value="13">Enviado Servicio Tercerizado Externo</option>
                    <option value="14">Recepción (falta) Servicio Tercerizado Externo</option>
                    <option value="15">Recepción (todo) Servicio Tercerizado Externo</option>
                    <option value="17">Pendiente envío (Planta)</option>
                    <option value="18">Pedido enviado (Planta)</option>
        				  </select>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_estado_lavado" class="btn btn-default btn-block btn-estado_lavado" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_estado_lavado" class="btn btn-default btn-block btn-estado_lavado" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_estado_lavado" class="btn btn-default btn-block btn-estado_lavado" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-estado_lavado" class="table-responsive">
            <form id="form-estado_lavado" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
              <table id="table-estado_lavado" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center"><input type="checkbox" onclick="checkAllMenuHeader();" id="check-AllMenuHeader"></th>
                    <th class="text-center">Cajero</th>
                    <th class="text-center">F. Emisión</th>
                    <th class="text-center">F. Entrega</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Serie</th>
                    <th class="text-center">Número</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">M</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Tipo Envío</th>
                    <th class="text-center">Estado Lavado</th>
                    <th class="text-center">Personal / Proveedor</th>
                    <th class="text-center">Nota</th>
                    <th class="text-center">Estado Recepción</th>
                    <th class="text-center">Recibió</th>
                    <th class="text-center" colspan="3"><button type="button" class="btn btn-success btn-block btn-save" onclick="cambiarEstadoLavado();"><span class="fa fa-truck"></span> Enviar</button></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <!-- Modal delivery -->
              <div class="modal fade modal-delivery" id="modal-default">
                <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <label>Transporte</label>
                          <div class="form-group">
                            <select id="cbo-transporte" name="ID_Transporte_Sede_Planta" class="form-control"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-xs-6">
                        <div class="form-group">
                          <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                        </div>
                      </div>
                      <div class="col-xs-6">
                        <div class="form-group">
                          <button type="button" id="btn-enviar_planta_transporte" class="btn btn-success btn-md btn-block pull-center">Enviar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /. Modal delivery -->
            </form>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- modal notas de planta -->    
    <div class="modal fade modal-notas_planta" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Notas</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12" id="div-final_prelavado"></div>
              <div class="col-sm-12" id="div-final_lavado_seco"></div>
              <div class="col-sm-12" id="div-planchado"></div>
              <div class="col-sm-12" id="div-doblado"></div>
              <div class="col-sm-12" id="div-embolsado"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal notas de planta -->
    <?php
    $attributes = array('id' => 'form-cobrar_cliente');
    echo form_open('', $attributes);
    ?>
    <!-- modal cobrar cliente -->
    <div class="modal fade modal-cobrar_cliente" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
            <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="2">
            <div class="row div-forma_pago">
              <div class="col-sm-3">
                <label>Forma Pago</label>
                <div class="form-group">
                  <select id="cbo-modal_forma_pago" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 div-modal_datos_tarjeta_credito">
                <label>Tarjeta</label>
                <div class="form-group">
                  <select id="cbo-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3">
                <label>Pago cliente</label>
                <div class="form-group">
                  <input type="tel" class="form-control input-decimal" name="fPagoCliente" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row importes -->
            
            <div class="row div-modal_datos_tarjeta_credito">
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="tel" id="tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="tel" id="tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row -->
          </div>
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-cobrar_cliente" class="btn btn-primary btn-md btn-block pull-center">Cobrar</button>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /. modal cobrar cliente -->
    <?php echo form_close(); ?>
    <?php
    $attributes = array('id' => 'form-entregar_pedido');
    echo form_open('', $attributes);
    ?>
    <!-- modal entregar pedido -->
    <div class="modal fade modal-entregar_pedido" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
            <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="3">
            <div class="row div-forma_pago">
              <div class="col-sm-3">
                <label>Forma Pago</label>
                <div class="form-group">
                  <select id="cbo-modal_forma_pago_entrega_pedido" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 div-modal_datos_tarjeta_credito">
                <label>Tarjeta</label>
                <div class="form-group">
                  <select id="cbo-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3">
                <label>Pago cliente</label>
                <div class="form-group">
                  <input type="tel" class="form-control input-decimal" name="fPagoCliente" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row importes -->
            
            <div class="row div-modal_datos_tarjeta_credito">
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="tel" id="tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="tel" id="tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row tarjeta -->
            
            <div class="row">
              <div class="col-sm-4">
                <label>¿Recibe la misma persona?</label>
                <div class="form-group">
                  <select id="cbo-modal_quien_recibe" name="iCrearEntidad" class="form-control" style="width: 100%;">
                    <option value="1" selected>Si</option>
                    <option value="0">No</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-sm-8 div-recibe_otra_persona">
                <label>Nombre(s) y apellidos</label>
                <div class="form-group">
                  <input type="text" name="sNombreRecepcion" class="form-control" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row entrega al cliente -->
            
            <div class="row">
              <div class="col-xs-12">
                <label>Transporte</label>
                <div class="form-group">
                  <select id="cbo-transporte_delivery_cliente" name="iTransporteDeliveryCliente" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!-- ./ row transporte de delivery -->
          </div>
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-entregar_pedido" class="btn btn-primary btn-md btn-block pull-center">Entregar</button>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /. modal entregar pedido -->
    <?php echo form_close(); ?>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->