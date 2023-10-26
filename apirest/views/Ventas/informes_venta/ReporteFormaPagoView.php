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
              <br>
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
              <div class="col-sm-2">
                <div class="form-group">
                  <label>Almacén</label>
    		  				<select id="cbo-Almacenes_ReporteFormaPago" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
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

              <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Forma pago</label>
                  <select id="cbo-forma_pago" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 div-tipos_tarjetas">
                <div class="form-group">
                  <label>Tipo tarjeta</label>
                  <select id="cbo-tipo_tarjeta" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Regalo</label>
                  <select id="cbo-regalo" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Si</option>
                    <option value="2">No</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-mas_filtros" name="filtro-mas_filtros" class="flat-red">
                  </div>
                  Más filtros
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar tendrán mas opciones para filtrar">
                    <i class="fa fa-info-circle"></i>
                  </span>
                </label>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 hidden">
                <div class="form-group">
                  <label>Estado Documento</label>
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

              <div class="col-xs-12 col-sm-2 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo Venta</label>
    		  				<select id="cbo-tipo_venta" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Factura de Venta (Oficina)</option>
    		  				  <option value="2">Punto de Venta (Caja)</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-5 col-md-4 div-mas_opciones">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-5 col-md-4 div-mas_opciones">
                <div class="form-group">
                  <label>Personal</label>
                  <input type="hidden" id="txt-AID_Personal" class="form-control">
                  <input type="text" id="txt-Filtro_Personal" class="form-control autocompletar_personal" data-global-class_method="AutocompleteController/getAllEmployee" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-primary btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_detalladas_generales" class="btn btn-danger btn-block btn-generar_ventas_detalladas_generales" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_detalladas_generales" class="btn btn-success btn-block btn-generar_ventas_detalladas_generales" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_detalladas_generales" class="table-responsive">
            <table id="table-ventas_detalladas_generales" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center" rowspan="2">Fecha Emisión</th>
                  <th class="text-center" rowspan="2">Fecha Pago</th>
                  <th class="text-center" rowspan="2">Personal / Cajero</th>
                  <th class="text-center" colspan="3">Documento</th>
                  <th class="text-center" colspan="3">Cliente</th>
                  <th class="text-center" colspan="2">Moneda</th>
                  <th class="text-center" colspan="5">Forma Pago</th>
                  <th class="text-center" rowspan="2">Estado</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="text-center" rowspan="2">Editar</th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="text-center" rowspan="2">Eliminar</th>
                  <?php endif; ?>
                </tr>
                <tr>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center"># Documento</th>
                  <th class="text-center">Nombre</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">T.C.</th>
                  <th class="text-center">Medio Pago</th>
                  <th class="text-center">Tipo Tarjeta</th>
                  <th class="text-center">Nro. Tarjeta</th>
                  <th class="text-center">Nro. Voucher</th>
                  <th class="text-center">Total</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
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

<?php
$attributes = array('id' => 'form-medio_pago');
echo form_open('', $attributes);
?>
<div class="modal fade modal-medio_pago" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center" id="modal-header-medio_pago-title"></h4>
      </div>

      <div class="modal-body">
        <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
        <input type="hidden" name="iIdDocumentoMedioPago" class="form-control">
        <input type="hidden" name="iTipoMedioPagoOperacionActual" class="form-control">
        <input type="hidden" name="iTipoMedioPagoOperacion" class="form-control">
        <input type="hidden" name="iIdMedioPagoActual" class="form-control">
        <input type="hidden" name="iIdTipoMedioPagoActual" class="form-control">

        <input type="hidden" name="sTotalDocumento" class="form-control">
        <input type="hidden" name="sTotalSaldo" class="form-control">

        <div class="row div-forma_pago">
          <div class="col-xs-6 col-sm-3">
            <label>Importe</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" name="fTotalMedioPago" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

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
              <select id="cbo-medio_pago-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
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
          
          <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 div-credito">
            <div class="form-group">
              <label>F. Vencimiento</label>
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-Fe_Vencimiento" name="Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
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
          <button type="button" id="btn-medio_pago" class="btn btn-primary btn-md btn-block pull-center">Actualizar</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php echo form_close(); ?>