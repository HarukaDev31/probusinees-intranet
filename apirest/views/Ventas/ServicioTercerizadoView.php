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
                  <label>Estado Documento</label>
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
              
              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                  <label>Estado Lavado</label>
    		  				<select id="cbo-estado_lavado" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
                    <option value="11">Pendiente envío Servicio Tercerizado</option>
                    <option value="12">Enviar Servicio Tercerizado Externo</option>
                    <option value="13">Enviado Servicio Tercerizado Externo</option>
                    <option value="14">Recepción (falta) Servicio Tercerizado</option>
                    <option value="15">Recepción (todo) Servicio Tercerizado Externo</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label>Proveedor</label>
                  <select id="cbo-filtro_proveedor" class="form-control select2" style="width: 100%;"></select>
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
            <form id="form-estado_lavado" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
              <table id="table-proceso_planta_lavanderia" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center"><input type="checkbox" onclick="checkAllMenuHeader();" id="check-AllMenuHeader"></th>
                    <th class="text-center">F. Emisión</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Serie</th>
                    <th class="text-center">Número</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">M</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Accion</th>
                    <th class="text-center">Estado Lavado</th>
                    <th class="text-center">Proveedor</th>
                    <th class="text-center" colspan="2"><button type="button" class="btn btn-success btn-block btn-save" onclick="cambiarEstadoLavado();"><span class="fa fa-truck"></span> Enviar</button></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </form>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- Modal modificar pedido -->
    <div class="modal fade modal-modificar_pedido" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center"><label id="modal-header-label-title_modificado"></label></h4>
            <h5 class="text-left modal-header-label-subtitle_nota"><label id="modal-header-label-subtitle_nota"></label></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" id="hidden-iIdDocumentoCabeceraModificar" value="">
            <div class="row">
              <div class="col-xs-6">
                <label>Proveedor</label>
                <div class="form-group">
                  <select id="cbo-proveedor" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-3">
                <label>F. Entrega</label>
                <div class="input-group date">
                  <input type="text" id="txt-fe_entrega" class="form-control input-datepicker required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                </div>
              </div>

              <div class="col-xs-3">
                <label>Estado</label>
                <div class="form-group">
                  <select id="modal-cbo-estado_lavado" class="form-control">
                    <option value="0" selected>- Seleccionar -</option>
                    <option value="12" selected>Enviar</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
            <div class="row div-detalle_item_modificar_pedido">
              <div class="col-xs-12">
                <table id="table-detalle_item_modificar_pedido" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Cant.</th>
                      <th class="text-center">Descripción</th>
                      <!--<th class="text-center"></th>-->
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div><!-- /. row lista items de pedido -->
            <div class="row" id="div-detalle_item_pedido">
              <div class="col-xs-2">
                <label>Cantidad</label>
                <div class="form-group">
                  <input type="text" id="txt-cantidad" class="form-control input-number" value="1" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-7">
                <label>Item</label>
                <div class="form-group">
                  <input type="text" id="txt-item" class="form-control" value="" autocomplete="off" placeholder="Ingresar item">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-3">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-add_item_pedido" class="btn btn-success btn-md btn-block pull-center">agregar item</button>
                </div>
              </div>

              <div class="col-xs-12">
                <table id="table-detalle_item_pedido" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Cant.</th>
                      <th class="text-center">Descripción</th>
                      <th class="text-center"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div><!-- /. row registrar items de pedido -->
          </div>
          <div class="modal-footer">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <button type="button" id="btn-modificar_pedido" class="btn btn-primary btn-md btn-block pull-center">Guardar</button>
                </div>
              </div>
              <div class="col-xs-12" style="display:none">
                <div class="form-group">
                  <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /. Modal modificar pedido -->
    <!-- Modal verificar pedido -->
    <div class="modal fade modal-verificar_pedido" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Nro. Pedido <label id="modal-header-label-title_modificado"></label></h4>
          </div>
          <div class="modal-body">
            <div class="row div-detalle_item_verificar_pedido">
              <div class="col-xs-12">
                <form id="form-verificar_pedido" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
                  <input type="hidden" id="hidden-iIdDocumentoCabeceraVerificar" name="iIdDocumentoCabeceraVerificar" value="">
                  <input type="hidden" id="hidden-iTotalItemDetalle" name="iTotalItemDetalle" value="">
                  <table id="table-detalle_item_verificar_pedido" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th class="text-center">Cant.</th>
                        <th class="text-center">Descripción</th>
                        <th class="text-center"><input type="checkbox" onclick="checkAllMenuHeaderVerificarPedido();" id="check-AllMenuHeaderVerificarPedido"></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </form>
              </div>
            </div><!-- /. row registrar items de pedido -->
          </div>
          <div class="modal-footer">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <button type="button" id="btn-verificar_pedido" class="btn btn-primary btn-md btn-block pull-center">Procesar</button>
                </div>
              </div>
              <div class="col-xs-12" style="display:none">
                <div class="form-group">
                  <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /. Modal verificar_pedido -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->