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
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i></a>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    <?php
    if ( !empty($sStatus) ){
      $sClassModal = 'success';
      $sMessage = 'Datos cargados satisfactoriamente';
      if ( (int)$iCantidadNoProcesados > 0 ){
        $sMessage .= '. Pero tiene ' . $iCantidadNoProcesados . ' registro(s) no procesados';
      }
      if ( $sStatus == 'error-sindatos' ) {
        $sMessage = 'Llenar los campos obligatorios (código de barra / UPC) y precio';
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-bd' ) {
        $sMessage = 'Problemas al generar excel';
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-archivo_no_existe' ) {
        $sMessage = 'El archivo no existe';
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-copiar_archivo' ) {
        $sMessage = 'Error al copiar archivo al servidor';
        $sClassModal = 'danger';  
      }
    ?>
      <div class="modal fade in modal-<?php echo $sClassModal; ?>" id="modal-message_excel" role="dialog" style="display: block;">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><?php echo $sMessage; ?></h4>
            </div>
            <div class="modal-footer">
              <button type="button" id="btn-cerrar_modal_excel" class="btn btn-outline pull-right" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Tabla" class="form-control">
    		  				  <option value="Lista_Precio">Lista</option>
    		  				  <option value="Cliente">Cliente / Proveedor</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar por.." value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarLista_Precio()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Lista_Precio" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Almacén</th>
                  <th>Lista</th>
                  <th>M</th>
                  <th>Tipo</th>
                  <th>Cliente / Proveedor</th>
                  <th class="no-sort">Estado</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <th class="no-sort">Precios</th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <th class="no-sort">Replicar Precios</th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort">Eliminar</th>
                  <?php endif; ?>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Lista_Precio');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" name="EID_Empresa" class="form-control">
          	  <input type="hidden" name="EID_Organizacion" class="form-control">
          	  <input type="hidden" name="EID_Lista_Precio_Cabecera" class="form-control">
          	  <input type="hidden" name="ENo_Lista_Precio" class="form-control">
              
              <div class="row">                
                <div class="col-xs-12 col-sm-6 col-md-4">
                  <div class="form-group">
                    <label>Almacén</label>
        	  				<select id="cbo-Almacenes" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
            
                <div class="col-xs-12 col-sm-6 col-md-8">
                  <label>Nombre Lista</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" name="No_Lista_Precio" class="form-control required" placeholder="Obligatorio" maxlength="100" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-3">
                  <div class="form-group">
                    <label>Tipo Lista <span class="label-advertencia">*</span></label>
                    <select id="cbo-tipos_lista_precio" name="Nu_Tipo_Lista_Precio" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-3">
                  <div class="form-group">
                    <label>Moneda <span class="label-advertencia">*</span></label>
                    <select id="cbo-Monedas" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-4">
                  <div class="form-group">
                    <label id="label-tipo_socio">Cliente / Proveedor</label> (opcional)
        	  				<select id="cbo-Socios" name="ID_Entidad" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-2">
                  <div class="form-group">
                    <label>Estado <span class="label-advertencia">*</span></label>
      		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
      			    <br/>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
                  </div>
                </div>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block">Guardar</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
          <!-- /.box-body -->
          <!-- box-body Precio por Producto -->
          <div class="box-body div-AgregarEditarPrecio">
            <?php
            $attributes = array('id' => 'form-Lista_Precio_Producto');
            echo form_open('', $attributes);
            ?>
              <input type="hidden" name="ID_Lista_Precio_Cabecera" class="form-control">

              <div class="row">
                <h2 class="text-center" id="title-lista_precio_detalle" style="margin-top: 0px;margin-bottom: 2.5rem;"></h2>

                <div class="col-xs-12 col-sm-9 col-md-6">
                  <div class="form-group">
                    <label>Producto / Servicio</label>
        	  				<!--<select id="cbo-Productos" name="ID_Producto" class="form-control select2" style="width: 100%;"></select>-->
                    
                    <input type="hidden" id="txt-AID" name="ID_Producto" class="form-control">
                    <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                    <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/globalAutocomplete" data-global-table="producto" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">

                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-7 col-sm-4 col-md-2 hidden">
                  <div class="form-group">
                    <label>Precio Interno</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></span>
                      <input type="text" name="Ss_Precio_Interno" class="form-control input-decimal" maxlength="13" autocomplete="off">
                    </div>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-5 col-sm-3 col-md-2 hidden">
                  <div class="form-group">
                    <label>Dscto %</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></span>
                      <input type="text" name="Po_Descuento" class="form-control input-decimal" maxlength="13" autocomplete="off">
                    </div>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-3 col-md-2">
                  <label>Precio <span class="label-advertencia">*</span></label>
                  <div class="form-group">
                    <input type="text" name="Ss_Precio" class="form-control required input-decimal" placeholder="Obligatorio" maxlength="13" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-5 col-sm-12 col-md-2 hidden">
                  <div class="form-group">
                    <label>Estado <span class="label-advertencia">*</span></label>
      		  				<select id="cbo-Estado_Precio" name="Nu_Estado" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <button type="submit" id="btn-save_precio" class="btn btn-success btn-md btn-block">Guardar</button>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <button type="button" id="btn-cancelar_precio" class="btn btn-danger btn-md btn-block">Cancelar</button>
                  </div>
                </div>
              </div><!-- ./Row -->
          
      			  <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group">
                    <button type="button" class="btn btn-default btn-md btn-block" onclick="importarExcelListaPrecios()"><i class="fa fa-file-excel-o color_icon_excel"></i> Importar Precios Excel</button>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
                <div class="col-xs-12"><br>
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b>Listado</b></div>
                    <div class="panel-body">
          	          <input type="hidden" id="txt-ID_Lista_Precio_Cabecera" class="form-control">
                      <div class="box-header box-header-new">
                        <div class="row div-Filtros">
                          <br>
                          <div class="col-md-3">
                            <div class="form-group">
                		  				<select id="cbo-Filtros_Tabla_Precio" class="form-control">
                		  				  <option value="Lista_Precio_Producto">Nombre producto</option>
                		  				  <option value="Lista_Precio_UPC">Código producto</option>
                		  				</select>
                            </div>
                          </div>
                          
                          <div class="col-md-9">
                            <div class="form-group">
                              <input type="text" id="txt-Global_Filter_Producto" class="form-control" placeholder="Buscar" value="" autocomplete="off">
                            </div>
                          </div>
                        </div>
                      </div>
              
              			  <div class="row">
                        <div class="col-xs-12">
                          <div class="table-responsive">
                            <table id="table-Lista_Precio_Producto" class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>Código</th>
                                  <th>Nombre</th>
                                  <!--
                                  <th>P. Interno</th>
                                  <th>Dscto %</th>
                                  -->
                                  <th>P. Venta</th>
                                  <th class="no-sort">Estado</th>
                                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                                    <th class="no-sort">Editar</th>
                                  <?php endif; ?>
                                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                                    <th class="no-sort">Eliminar</th>
                                  <?php endif; ?>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
          <!-- /.box-body Precio por Producto -->
          <form id="form-Lista_Precio_Producto_Editar" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
          <!-- Modal -->
          <div class="modal fade" id="modal-Lista_Precio_Producto_Editar" role="dialog">
          <div class="modal-dialog">
          	<div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"></h4>
              </div>
              
            	<div class="modal-body">
                <input type="hidden" name="ID_Lista_Precio_Cabecera" class="form-control">
            	  <input type="hidden" name="ID_Lista_Precio_Detalle" class="form-control">
            	  <input type="hidden" name="EID_Producto" class="form-control">
              	  
                <div class="row">
                  <div class="col-xs-8 col-sm-9 col-md-9">
                    <label>Producto</label>
                    <div class="form-group">
          	  				<!--<select id="cbo-Productos_Editar" name="ID_Producto_Editar" class="form-control select2" style="width: 100%;"></select>-->
                      <!--<span id="item-lista_precio-editar-nombre"></label>-->
                      <input type="text" id="item-lista_precio-editar-nombre" name="ID_Producto_Editar" class="form-control" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-4 col-sm-3 col-md-3">
                    <label>Precio <span class="label-advertencia">*</span></label>
                    <div class="form-group">
                      <input type="text" name="Ss_Precio_Editar" class="form-control required input-decimal" placeholder="Obligatorio" maxlength="13" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                </div>
                  
                <div class="row">
                  <div class="col-xs-7 col-sm-3 col-md-3 hidden">
                    <div class="form-group">
                      <label>Precio Interno</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        <input type="text" name="Ss_Precio_Interno_Editar" class="form-control input-decimal" maxlength="13" autocomplete="off">
                      </div>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-5 col-sm-3 col-md-3 hidden">
                    <div class="form-group">
                      <label>Dscto %</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        <input type="text" name="Po_Descuento_Editar" class="form-control input-decimal" maxlength="13" autocomplete="off">
                      </div>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-5 col-sm-3 col-md-3 hidden">
                    <div class="form-group">
                      <label>Estado <span class="label-advertencia">*</span></label>
        		  				<select id="cbo-Estado_Precio_Editar" name="Nu_Estado" class="form-control required"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                </div><!-- ./Row -->
              </div>
              
            	<div class="modal-footer">
        			  <div class="row">
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-danger btn-lg btn-block" data-dismiss="modal">Cancelar</button>
                  </div>
                  <div class="col-xs-6">
                    <button type="submit" id="btn-save_precio_editar" class="btn btn-success btn-lg btn-block">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
          </form>
          <!-- /.Modal -->
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

<!-- Modal delivery -->
<?php
$attributes = array('id' => 'form-replicacion_precio');
echo form_open('', $attributes);
?>
<div class="modal fade modal-replicacion_precio" id="modal-default">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="text-center">Replicación Lista Precios</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-md-6">
          <div class="col-xs-12">
            <label>Almacén Origen</label>
            <div class="form-group">
              <select id="modal_replicacion-cbo-almacen" name="ID_Almacen_Replicacion_Precio" class="form-control" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12">
            <label>Lista Precio Origen</label>
            <div class="form-group">
              <select id="modal-replicacion-cbo-lista_precios" name="ID_Lista_Precio_Cabecera_Replicacion_Precio" class="form-control" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          </div>

          <div class="col-xs-12 col-md-6">
          <div class="col-xs-12">
            <label>Almacén Destino</label>
            <div class="form-group">
              <select id="modal_replicacion-cbo-almacen_destino" name="ID_Almacen_Replicacion_Precio_Destino" class="form-control" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12">
            <label>Lista Precio Destino</label>
            <div class="form-group">
              <select id="modal-replicacion-cbo-lista_precios_destino" name="ID_Lista_Precio_Cabecera_Replicacion_Precio_Destino" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <button type="button" id="btn-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <button type="button" id="btn-replicar_lista_precio" class="btn btn-primary btn-lg btn-block pull-center">Replicar</button>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal delivery -->
<?php echo form_close(); ?>