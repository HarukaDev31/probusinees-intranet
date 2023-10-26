<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
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

              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label>Nombre Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_proceso_planta_lavanderia" class="btn btn-primary btn-block btn-proceso_planta_lavanderia" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_proceso_planta_lavanderia" class="btn btn-danger btn-block btn-proceso_planta_lavanderia" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_proceso_planta_lavanderia" class="btn btn-success btn-block btn-proceso_planta_lavanderia" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-proceso_planta_lavanderia" class="table-responsive">
            <table id="table-proceso_planta_lavanderia" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">F. Emisión</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Cliente</th>
                  <th class="text-center">M</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Saldo</th>
                  <th class="text-center">Nota</th>
                  <th class="text-center">Accion</th>
                  <th class="text-center">Estado Lavado</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    
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
            <input type="hidden" name="iIdDocumentoMedioPago" class="form-control" value="0">
            <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="3">
            <input type="hidden" id="hidden-entregar_pedido-fsaldo" name="fSaldoCliente" class="form-control" value="0">

            <div class="row">
              <div class="col-sm-12">
                <h4 class="text-center"><label id="modal-header-label-title_modificado"></label></h4>
                <h5 class="text-left modal-header-label-subtitle_nota"><label id="modal-header-label-subtitle_nota"></label></h5>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label id="cobrar_cliente-modal-body-cliente"></label>
              </div>
              <div class="col-sm-12">
                <label id="cobrar_cliente-modal-body-saldo_cliente"></label>
              </div>
            </div>

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
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->