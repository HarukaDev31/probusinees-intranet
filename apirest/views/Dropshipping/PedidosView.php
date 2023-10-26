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
              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2 hidden">
                <div class="form-group">
                  <label>Documento</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;">
                    <option value="0">Todos</option>
                    <option value="4">Boleta</option>
                    <option value="3">Factura</option>
                  </select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Pedido</label>
                  <input type="text" inputmode="numeric" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-3">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-estado_pedido" class="form-control" style="width: 100%;">
    		  				  <option value="1" selected="selected">Pendiente</option>
    		  				  <option value="2">Confirmado</option>
    		  				  <option value="3">Preparando</option>
                    <option value="4">En Camino</option>
                    <option value="5">Entregado</option>
                    <option value="6">Rechazado</option>
                    <option value="7">Eliminado</option>
    		  				  <option value="0">Todos</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Recepción</label>
    		  				<select id="cbo-estado_recepcion" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Delivery</option>
    		  				  <option value="7">Recojo en tienda</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-8 col-md-12">
                <label>Cliente</label>
                <div class="form-group">
                  <input type="hidden" id="txt-AID" class="form-control">
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                    <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                  <span style="font-size: 1.2rem;">(*) Para ver clientes la opción es <strong>Ventas > Reglas de Ventas > Clientes</strong></span><br>
                  <span style="font-size: 1.2rem;">(*) Para ver ventas la opción es <strong>Ventas > Factura de Venta (Boleta, Factura y Nota de Venta)</strong></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-12 col-md-12">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-primary btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4 hidden">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4 hidden">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_detalladas_generales" class="table-responsive">
            <table id="table-ventas_detalladas_generales" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Fecha</th>
                  <th class="text-center">Pedido</th>
                  <th class="text-left">Cliente</th>
                  <th class="text-right">Total</th>
                  <th class="text-center">Recepción</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center">Ver</th>
                  <th class="text-center">Vender</th>
                  <th class="text-center">Eliminar</th>
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

  <?php
  $attributes = array('id' => 'form-generar_venta');
  echo form_open('', $attributes);
  ?>
  <div class="modal modal-default fade modal-generar_venta" id="modal-generar_venta">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-header-title-generar_venta text-center"></h4>
        </div>

        <div class="modal-body">
          <input type="hidden" name="ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" name="ID_Almacen" class="form-control">
          <input type="hidden" name="fTotalDocumento" class="form-control">
          <input type="hidden" name="ID_Tipo_Documento_Identidad" class="form-control">
          <input type="hidden" name="No_Entidad" class="form-control" placeholder="nombre">
          <input type="hidden" name="Nu_Documento_Identidad" class="form-control" placeholder="nro. doc. iden">
          <input type="hidden" name="Nu_Celular_Entidad_Order_Address_Entry" class="form-control" placeholder="celular">
          <input type="hidden" name="Txt_Direccion_Entidad_Order_Address_Entry" class="form-control" placeholder="direccion">
          <input type="hidden" name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry" class="form-control" placeholder="direccion">
          <input type="hidden" name="Nu_Tipo_Recepcion" class="form-control" placeholder="tipo recepcion">
          <input type="hidden" name="ID_Moneda" class="form-control" placeholder="moneda">
          <input type="hidden" name="ID_Medio_Pago" class="form-control" placeholder="medio pago">
          <input type="hidden" name="ID_Entidad" class="form-control" placeholder="id entidad">
          <input type="hidden" name="ID_Distrito_Delivery" class="form-control" placeholder="id distrito delivery">

          <div class="row">
            <div class="col-xs-12 div-tipo_documento">
              <label>Tipo Documento</label>
              <div class="form-group">
                <select id="cbo-tipo_documento" name="ID_Tipo_Documento" class="form-control" style="width: 100%;">
                  <?php if($this->empresa->Nu_Tipo_Proveedor_FE != 3) { ?>
                  <option value="4">B/Venta</option>
                  <option value="3">Factura</option>
                  <?php } ?>
                  <option value="2">Nota de Venta</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12">
              <p id="info-generar_venta"></p>
              (*) Para ver las ventas la opción es <strong>Ventas > Factura de Venta</strong>
              <br>(*) Al procesar venta se descargará stock.
            </div>
          </div>
        </div><!-- div modal body -->

        <div class="modal-footer">
          <div class="col-xs-6">
            <button type="button" id="btn-modal-generar_venta-cancel" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Cancelar</button>
          </div>
          <div class="col-xs-6">
            <button type="button" id="btn-modal-generar_venta-send" class="btn btn-success btn-md btn-block">Generar Venta</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->
  <?php echo form_close(); ?>

</div>
<!-- /.content-wrapper -->