<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <?php //array_debug($this->empresa);
  
  $sCssDisplayViewHideLavanderia='style="display:none"';
  if ( $this->empresa->Nu_Tipo_Rubro_Empresa == 3 ){//3 = Lavanderia
    $sCssDisplayViewHideLavanderia='';
  }
  ?>

  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i></a>
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
          	<input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Fecha</label>
    		  				<select id="cbo-tipo_consulta_fecha" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Actual</option>
    		  				  <option value="1">Histórico</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 div-fecha_historica">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 div-fecha_historica">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<!---<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>-->
                  <select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;">
                    <option value="0" selected="selected">Todos</option>
                    <option value="4">B/Venta</option>
                    <option value="3">Factura</option>
                    <option value="2">Nota de Venta</option>
                  </select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="text" inputmode="numeric" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Estado <span class="hidden-xs">Doc.</span></label>
    		  				<select id="cbo-estado_documento" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="9">Completado Error</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
    		  				  <option value="11">Anulado Error</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label><span class="hidden-xs">Estado </span>Pago</label>
    		  				<select id="cbo-estado_pago" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Pendiente</option>
                    <option value="2">Cancelado</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Recepción</label>
    		  				<select id="cbo-tipo_recepcion_cliente" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
                    <option value="5">Tienda</option>
                    <option value="6">Delivery</option>
                    <option value="7">Recojo en Tienda</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-6">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar Nombre / Nro. Documento de identidad" value="" autocomplete="off">
                    <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            
              <div class="col-xs-12 col-sm-12 col-md-4">
                <label>Observaciones</label>
                <div class="form-group">
                  <input type="text" id="txt-Filtro_Glosa" name="Filtro_Glosa" class="form-control" maxlength="255" placeholder="Opcional" value="" autocomplete="off">
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta" class="btn btn-primary btn-block btn-generar_venta_punto_venta" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-pdf_venta_punto_venta" class="btn btn-danger btn-block btn-generar_venta_punto_venta" data-type="pdf"><i class="fa fa-file-pdf-o color-white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-excel_venta_punto_venta" class="btn btn-success btn-block btn-generar_venta_punto_venta" data-type="excel"><i class="fa fa-file-excel-o color-white"></i> Excel</button>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" class="btn btn-default btn-block btn-save" alt="Cobrar varias ventas del mismo cliente al credito masivamente" title="Cobrar varias ventas del mismo cliente al credito masivamente" onclick="cobroMasivoVenta();"><span class="fa fa-money"></span> Cobro Masivo</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-venta_punto_venta" class="table-responsive">
            <form id="form-cobro_masivo_venta" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
              <table id="table-venta_punto_venta" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center"></th>
                    <!--<th class="text-center">Cajero</th>-->
                    <th class="text-center">Recepción</th>
                    <th class="text-center">F. Emisión</th>
                    <th class="text-center">Documento</th>
                    <th class="text-center">Cliente</th>
                    <!--<th class="text-center">T.C.</th>-->
                    <th class="text-center">Total</th><!-- S/ -->
                    <!--<th class="text-center">Total M. Ex.</th>-->
                    <th class="text-center">Saldo</th>
                    <!--<th class="text-center">Pago</th>-->
                    <th class="text-center">Estado</th>
                    <th class="text-center">Editar</th><!-- Modificar -->
                    <th class="text-center">Anular</th><!-- Eliminar -->
                    <th class="text-center">Imprimir</th><!-- imprimir -->
                    <th class="text-center">Cobrar</th><!-- cobrar -->
                    <th class="text-center">Facturar</th><!-- Facturar -->
                    <th class="text-center"></th>
                    <th class="text-center">Glosa</th><!-- glosa -->
                    <th class="text-center">Guia(s)</th><!-- Guia -->
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <!-- Modal cobro_masivo_venta -->
              <div class="modal fade modal-cobro_masivo_venta" id="modal-default">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-body">
                      <input type="hidden" id="hidden-cobro_masivo_venta-fsaldo" name="fSaldoCliente" class="form-control" value="0">
                      <div class="row">
                        <div class="col-sm-12">
                          <label id="cobrar_cliente-modal-body-cliente_masivo"></label>
                        </div>
                        <div class="col-sm-12">
                          <label id="cobrar_cliente-modal-body-saldo_cliente_masivo"></label>
                        </div>
                      </div>

                      <div class="row div-forma_pago_masivo">
                        <div class="col-xs-6 col-sm-3">
                          <label>Forma Pago</label>
                          <div class="form-group">
                            <select id="cbo-modal_forma_pago_masivo" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-3 div-modal_datos_tarjeta_credito_masivo">
                          <label>Tarjeta</label>
                          <div class="form-group">
                            <select id="cbo-cobrar_cliente-modal_tarjeta_credito_masivo" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito_masivo">
                          <label>Nro. Operación</label>
                          <div class="form-group">
                            <input type="text" inputmode="numeric" id="tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="Opcional" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito_masivo">
                          <label>4 dígitos</label>
                          <div class="form-group">
                            <input type="text" inputmode="numeric" id="tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="Opcional" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-3">
                          <label>Pago cliente</label>
                          <div class="form-group">
                            <input type="text" inputmode="decimal" class="form-control input-decimal" id="tel-cobro_masivo_venta-fPagoCliente" name="fPagoCliente" value="" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div><!-- ./ row importes -->
                    </div>
                    <div class="modal-footer">
                      <div class="col-xs-6">
                        <div class="form-group">
                          <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                        </div>
                      </div>
                      <div class="col-xs-6">
                        <div class="form-group">
                          <button type="button" id="btn-cobro_masivo_venta" class="btn btn-success btn-md btn-block pull-center">Cobrar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /. Modal cobro_masivo_venta -->
            </form>
          </div><!-- /. div-venta_punto_venta -->
          
          <?php
          $attributes = array('id' => 'form-entregar_pedido');
          echo form_open('', $attributes);
          ?>
          <!-- modal entregar pedido -->
          <div class="modal fade modal-entregar_pedido" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-entregar_pedido-title"></h4>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
                  <input type="hidden" name="iIdDocumentoMedioPago" class="form-control" value="0">
                  <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="3">
                  <input type="hidden" id="hidden-entregar_pedido-fsaldo" name="fSaldoCliente" class="form-control" value="0">
  
                  <div class="row">
                    <div class="col-sm-12">
                      <label id="entregar_pedido-modal-body-cliente"></label>
                    </div>
                    <div class="col-sm-12">
                      <label id="entregar_pedido-modal-body-saldo_cliente"></label>
                    </div>
                  </div>

                  <div class="row div-forma_pago">
                    <div class="col-xs-6 col-sm-3">
                      <label>Forma Pago</label>
                      <div class="form-group">
                        <select id="cbo-modal_forma_pago_entrega_pedido" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Tarjeta</label>
                      <div class="form-group">
                        <select id="cbo-entregar_pedido-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Nro. Operación</label>
                      <div class="form-group">
                        <input type="text" inputmode="numeric" id="tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="Opcional" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>4 dígitos</label>
                      <div class="form-group">
                        <input type="text" inputmode="numeric" id="tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="Opcional" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3">
                      <label>Pago cliente</label>
                      <div class="form-group">
                        <input type="text" inputmode="decimal" class="form-control input-decimal" id="tel-entregar_pedido-fPagoCliente" name="fPagoCliente" value="" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div><!-- ./ row importes -->
                  
                  <div class="row">
                    <div class="col-xs-3 col-sm-4">
                      <label>¿Recibe la misma persona?</label>
                      <div class="form-group">
                        <select id="cbo-modal_quien_recibe" name="iCrearEntidad" class="form-control" style="width: 100%;">
                          <option value="1" selected>Si</option>
                          <option value="0">No</option>
                        </select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-9 col-sm-8 div-recibe_otra_persona">
                      <label>Nombre(s) y apellidos</label>
                      <div class="form-group">
                        <input type="text" name="sNombreRecepcion" class="form-control" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div><!-- ./ row entrega al cliente -->
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

          <?php
          $attributes = array('id' => 'form-cobrar_cliente');
          echo form_open('', $attributes);
          ?>
          <!-- modal cobrar cliente -->
          <div class="modal fade modal-cobrar_cliente" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-cobrar_cliente-title"></h4>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
                  <input type="hidden" name="iIdDocumentoMedioPagoCobrarCliente" class="form-control" value="0">
                  <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="2">
                  <input type="hidden" id="hidden-cobrar_cliente-fsaldo" name="fSaldoCliente" class="form-control" value="0">
                  <input type="hidden" id="hidden-cobrar_cliente-detraccion"name="iCobrarModalDetraccion" class="form-control" value="0">
                  
                  <div class="row">
                    <div class="col-sm-12">
                      <label id="cobrar_cliente-modal-body-cliente"></label>
                    </div>
                    <div class="col-sm-12">
                      <label id="cobrar_cliente-modal-body-saldo_cliente"></label>
                    </div>
                  </div>

                  <div class="row div-forma_pago">
                    <div class="col-xs-6 col-sm-3">
                      <label>Forma Pago</label>
                      <div class="form-group">
                        <select id="cbo-modal_forma_pago" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Tarjeta</label>
                      <div class="form-group">
                        <select id="cbo-cobrar_cliente-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Nro. Operación</label>
                      <div class="form-group">
                        <input type="text" inputmode="numeric" id="tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="Opcional" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>4 dígitos</label>
                      <div class="form-group">
                        <input type="text" inputmode="numeric" id="tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="Opcional" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-4 col-sm-3">
                      <label>Pago cliente</label>
                      <div class="form-group">
                        <input type="text" inputmode="decimal" class="form-control input-decimal" id="tel-cobrar_cliente-fPagoCliente" name="fPagoCliente" value="" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div><!-- ./ row importes -->
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
          $attributes = array('id' => 'form-facturar_orden_lavanderia');
          echo form_open('', $attributes);
          ?>
          <!-- modal facturar orden -->
          <div class="modal fade modal-facturar_orden_lavanderia" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-facturar_orden_lavanderia-title"></h4>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
                  <input type="hidden" name="iIdDocumentoMedioPago" class="form-control" value="0">
                  <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="2">
                  <input type="hidden" id="hidden-facturar_oden_lavanderia-fsaldo" class="form-control" value="0">
                  <input type="hidden" id="hidden-facturar_oden_lavanderia-iEstadoLavadoRecepcionCliente" name="Nu_Estado_Lavado_Recepcion_Cliente" class="form-control" value="0">
                  <input type="hidden" name="fTotalDocumento" class="form-control" value="0">
                  
                  <div class="row">
                    <div class="col-sm-12">
                      <label id="facturar_orden_lavanderia-modal-body-cliente"></label><br><br>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-fecha_convertir">
                      <label id="label_correo">F. Emisión</label>
                      <div class="form-group">
                        <div class="input-group date" style="width:100%">
                          <input type="text" id="txt-fe_emision_convertir" name="Fe_Emision_Convertir" class="form-control date-picker-invoice" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-fecha_convertir">
                      <label id="label_correo">F. Vencimiento</label>
                      <div class="form-group">
                        <div class="input-group date" style="width:100%">
                          <input type="text" id="txt-fe_vencimiento_convertir" name="Fe_Vencimiento_Convertir" class="form-control input-datepicker-today-to-more" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                        </div>
                      </div>
                    </div>

                    <div class="col-xs-6 col-sm-2 col-md-4 col-lg-2" style="padding-right: 0px;">
                      <label>Documento</label>
                      <div class="form-group">
                        <select id="modal-cbo-tipo_documento" name="ID_Tipo_Documento" class="form-control" style="width: 100%;padding: 3px;">
                          <option value="4" data-nu_cantidad_caracteres="8" title="Puedes registrar boleta ingresando solo Nombres del Cliente">Boleta</option>
                          <option value="3" data-nu_cantidad_caracteres="11">Factura</option>
                        </select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-2 col-md-3 col-lg-2">
                      <div class="form-group">
                        <label>T.D.I</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de Documento de Identidad">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <select id="cbo-modal-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-5 col-sm-3 col-md-5 col-lg-3">
                      <label id="label-tipo_documento_identidad" title="Si existe cliente, buscar por Nombre / Num. Doc. Ident.">DNI</label>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si el cliente ya existe, puedes buscar por Nombre o Número de Documento de Identidad, si no existe el sistema lo guardará y en la siguiente venta lo podrán buscar">
                        <i class="fa fa-info-circle"></i>
                      </span>
                      <div class="form-group">
                        <input type="hidden" id="txt-ID_Tipo_Documento_Identidad_Existente" class="form-control">
                        <input type="hidden" id="txt-AID" name="AID" class="form-control">
                        <input type="hidden" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control">
                        <input type="text" id="txt-ACodigo" name="Nu_Documento_Identidad" class="form-control autocompletar input-Mayuscula input-codigo_barra" onkeyup="api_sunat_reniec(this.value);" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar / opcional" title="Si existe cliente ingresar Nombre / # Doc. Ident." maxlength="8" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-7 col-sm-5 col-md-6 col-lg-5">
                      <label>Nombre <span id="span-no_nombres_cargando"></span></label>
                      <div class="form-group">
                        <input type="hidden" id="hidden-nu_numero_documento_identidad" class="form-control" value="">
                        <input type="hidden" id="hidden-estado_entidad" name="Nu_Estado_Entidad" class="form-control" value="0">
                        <input type="text" id="txt-ANombre" name="No_Entidad" class="form-control" autocomplete="off">
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-7 col-md-8 col-lg-6">
                      <div class="form-group">
                        <label id="label_correo">Correo</label>
                        <input type="email" id="txt-Txt_Email_Entidad_Cliente" name="Txt_Email_Entidad" placeholder="opcional" class="form-control" autocomplete="off">
                        <span class="hide" id="span-email" style="color: #dd4b39;">Ingresa un email válido</span>
                      </div>
                    </div>
              
                    <div class="col-xs-6 col-sm-3 col-md-4 col-lg-3">
                      <label>Celular</label>
                      <div class="form-group">
                        <input type="tel" id="txt-Nu_Celular_Entidad_Cliente" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-2 col-md-12 col-lg-3">
                      <label>Estado</label>
                      <div class="form-group estado">
                        <select id="cbo-Estado-modal" name="Nu_Estado" class="form-control required"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                      <label id="label-txt_estado_cliente" style="font-weight: normal"></label>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" id="btn-facturar_orden_lavanderia" class="btn btn-primary btn-md btn-block pull-center">Facturar</button>
                    </div>
                  </div>
                </div>

              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
          </div>
          <!-- /. modal facturar orden -->
          <?php echo form_close(); ?>     
          
          <?php
          $attributes = array('id' => 'form-modificar_venta');
          echo form_open('', $attributes);
          ?>
          <!-- modal modificar_venta -->
          <div class="modal fade modal-modificar_venta" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-modificar_venta-title"></h4>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="ID_Documento_Cabecera-Modificar" class="form-control" value="0">
                  <input type="hidden" name="ID_Entidad-Modificar" class="form-control" value="0">
                  <input type="hidden" name="ID_Tipo_Documento-Modificar" class="form-control" value="0">
                  
                  <div class="col-xs-6 col-sm-3 col-md-3 div-interno">
                    <label id="label_correo">F. Emisión</label>
                    <div class="form-group">
                      <div class="input-group date" style="width:100%">
                        <input type="text" id="txt-fe_emision_interno" name="Fe_Emision_Interno" class="form-control date-picker-invoice" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                      </div>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-3 col-md-3">
                    <label id="label_correo">Recepción</label>
                    <div class="form-group">
                      <select id="modal-cbo-tipo_recepcion-modificar" name="Nu_Tipo_Recepcion" class="form-control"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-4 col-md-4" <?php echo $sCssDisplayViewHideLavanderia; ?>>
                    <label id="label_correo">Envío</label>
                    <div class="form-group">
                      <select id="modal-cbo-tipo_envio_lavado-modificar" name="Nu_Transporte_Lavanderia_Hoy" class="form-control"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-6 col-sm-3 col-md-3 div-credito">
                    <label id="label_correo">F. Vencimiento</label>
                    <div class="form-group">
                      <div class="input-group date" style="width:100%">
                        <input type="text" id="txt-fe_vencimiento" name="Fe_Vencimiento" class="form-control input-datepicker-today-to-more" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 div-delivery">
                    <label>Transporte</label>
                    <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="Para agregar Transporte o Delivery, ingresar a la opción Personal > Maestro Delivery">
                      <i class="fa fa-info-circle"></i>
                    </span>
                    <div class="form-group">
                      <select id="cbo-transporte" name="ID_Transporte_Delivery" class="form-control select2" style="width: 100%;"></select>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 div-delivery div-recojo_tienda">
                    <label>F. Entrega</label>
                    <div class="form-group">
                      <div class="input-group date" style="width:100%">
                        <input type="text" id="txt-fe_entrega" name="Fe_Entrega" class="form-control input-datepicker-today-to-more required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12 div-delivery">
                    <label>Dirección</label>
                    <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="Si ingresa la dirección, se mostrará impreso en el ticket. También se guardará y se mostrará para la próxima venta">
                      <i class="fa fa-info-circle"></i>
                    </span>
                    <div class="form-group">
                      <textarea name="Txt_Direccion_Delivery" class="form-control" rows="1"></textarea>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <label>Glosa</label>
                    <div class="form-group">
                      <textarea name="Txt_Glosa" class="form-control"></textarea>
                    </div>
                  </div>
                </div><!--modal-body-->
                <div class="modal-footer">
                  <div class="col-xs-6">
                    <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" id="btn-modificar_venta" class="btn btn-primary btn-md btn-block pull-center">Modificar</button>
                  </div>
                </div>
              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
          </div>
          <!-- /. modal modificar_venta -->
          <?php echo form_close(); ?>
     
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->