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
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Almacén</label>
                <div class="form-group">
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Contacto</label>
                  <input type="text" id="txt-Filtro_Contacto" class="form-control autocompletar_contacto" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-3 col-sm-3 col-md-1">
                <div class="form-group">
                  <label>Serie</label>
                  <input type="text" id="txt-Filtro_SerieDocumento" class="form-control required input-Mayuscula input-codigo_barra" placeholder="Buscar" maxlength="20" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-3 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-3 col-sm-2 col-md-2">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-Filtro_Estado" class="form-control">
    		  				  <option value="" selected>Todos</option>
        				    <option value="5">Registrado</option>
        				    <option value="0">Entregado</option>
        				    <option value="1">Revisado</option>
        				    <option value="2">Aceptado</option>
        				    <option value="3">Rechazado</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-xs-9 col-sm-12 col-md-6">
                <div class="form-group">
                  <label>Proveedor</label>
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Ingresar Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                  <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <div class="form-group">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <div class="form-group">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarOrdenCompra()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-OrdenCompra" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort_left">Almacén</th>
                  <th class="no-sort">F. Emisión</th>
                  <th class="no-sort_left">Serie</th>
                  <th class="no-sort_left">Núm.</th>
                  <th class="no-sort_left">Proveedor</th>
                  <th class="no-sort_left">Contacto</th>
                  <th class="no-sort">M</th>
                  <th class="no-sort_right">Total</th>
                  <th class="no-sort">Estado</th>
                  <th class="no-sort">Enlace</th>
                  <th class="no-sort">PDF</th>
                  <th class="no-sort">Duplicar</th>
                  <th class="no-sort">Comprar</th>
                  <th class="no-sort">Editar</th><!-- editar -->
                  <th class="no-sort">Eliminar</th><!-- eliminar -->
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-OrdenCompra');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" name="EID_Empresa" class="form-control">
          	  <input type="hidden" name="EID_Documento_Cabecera" class="form-control">
          	  <input type="hidden" name="ENu_Estado" class="form-control">
          	  		  
      			  <div class="row">
                <div class="col-sm-12 col-md-12 cortar-padding">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Documento</b></div>
                    <div class="panel-body">                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>Series <span class="label-advertencia">*</span></label>
            		  				<input type="text" id="txt-ID_Serie_Documento" name="ID_Serie_Documento" class="form-control required input-Mayuscula input-codigo_barra" maxlength="20" autocomplete="off" placeholder="Ingresar serie">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>Número <span class="label-advertencia">*</span></label>
                          <input type="tel" id="txt-ID_Numero_Documento" name="ID_Numero_Documento" class="form-control required input-number" maxlength="20" autocomplete="off" placeholder="Ingresar número">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>F. Emisión <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Emision" name="Fe_Emision" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>F. Vcto. <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Vencimiento" name="Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>F. Entrega <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Entrega" name="Fe_Entrega" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>Forma Pago <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-MediosPago" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="form-group">
                          <label>Moneda <span class="label-advertencia">*</span></label>
                          <select id="cbo-Monedas" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-2 hidden">
                        <div class="form-group">
                          <label>¿Stock?</label>
                          <select id="cbo-descargar_stock" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-8">
                        <label>Lista de Precio</label>
                        <div class="form-group">
                          <select id="cbo-lista_precios" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Documento -->
              </div>
              
      			  <div class="row">
                <div class="col-sm-12 col-md-6">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Proveedor</b></div>
                    <div class="panel-body">
                      <div class="col-xs-6 text-center">
                        <label><input type="radio" name="addProveedor" id="radio-cliente_existente" class="flat-red" value="0"> Existente</label>
                      </div>
                      
                      <div class="col-xs-6 text-center">
                        <label><input type="radio" name="addProveedor" id="radio-cliente_nuevo" class="flat-red" value="1"> Nuevo</label>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 div-cliente_existente">
                        <div class="form-group">
                          <label>Proveedor <span class="label-advertencia">*</span></label>
                          <input type="hidden" id="txt-AID" name="AID" class="form-control required">
                          <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Ingresar Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-5 col-sm-4 col-md-4 div-cliente_existente">
                        <div class="form-group">
                          <label># D.I. <span class="label-advertencia">*</span></label>
                          <input type="text" id="txt-ACodigo" name="ACodigo" class="form-control required" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-7 col-sm-8 col-md-8 div-cliente_existente">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <!-- Proveedor Nuevo -->
                      <div class="col-xs-4 col-sm-3 col-md-3 div-cliente_nuevo">
                        <div class="form-group">
                          <label>T.D.I. <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-TiposDocumentoIdentidadProveedor" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-6 div-cliente_nuevo">
                        <div class="form-group">
                          <label id="label-Nombre_Documento_Identidad_Proveedor">DNI</label><span class="label-advertencia"> *</span>
                          <input type="text" id="txt-Nu_Documento_Identidad_Proveedor" name="Nu_Documento_Identidad_Proveedor" class="form-control required input-Mayuscula input-codigo_barra" placeholder="Ingresar número" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-2 col-sm-3 col-md-3 text-center div-cliente_nuevo">
                        <label>Api</label>
                        <div class="form-group">
                          <button type="button" id="btn-cloud-api_orden_compra_cliente" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
              
                      <div class="col-xs-9 col-sm-9 col-md-9 div-cliente_nuevo">
                        <div class="form-group">
                          <label id="label-No_Entidad_Proveedor">Nombre(s) y Apellidos</label>
                          <input type="text" id="txt-No_Entidad_Proveedor" name="No_Entidad_Proveedor" class="form-control" placeholder="Ingresar nombre" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-3 col-sm-3 col-md-3 div-cliente_nuevo">
                        <div class="form-group estado">
                          <label>Estado</label>
            		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required">
            		  				  <option value="1">Activo</option>
            		  				  <option value="0">Inactivo</option>
            		  				</select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-12 col-md-5 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad_Proveedor" name="Txt_Direccion_Entidad_Proveedor" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-4 div-cliente_nuevo">
                        <label>Celular</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Celular_Entidad_Proveedor"  name="Nu_Celular_Entidad_Proveedor" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-3 div-cliente_nuevo">
                        <label>Telefono</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Telefono_Entidad_Proveedor" name="Nu_Telefono_Entidad_Proveedor" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Proveedor -->
                
                <div class="col-sm-12 col-md-6">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Contacto</b> (opcional)</div>
                    <div class="panel-body">
                      <input type="hidden" id="txt-ID_Tipo_Asiento" class="form-control" value="2">
                      
                      <div class="col-xs-6 text-center">
                        <label><input type="radio" name="addContacto" id="radio-contacto_existente" class="flat-red" value="0"> Existente</label>
                      </div>
                      
                      <div class="col-xs-6 text-center">
                        <label><input type="radio" name="addContacto" id="radio-contacto_nuevo" class="flat-red" value="1"> Nuevo</label>
                      </div>
                      
                      <div class="col-xs-12 col-md-6 div-contacto_existente hidden">
                        <div class="form-group id_tipo_documento_identidad">
                          <label>Tipo Doc. Identidad <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-TiposDocumentoIdentidadContacto_existe" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-md-6 div-contacto_existente hidden">
                        <div class="form-group">
                          <label id="label-Nombre_Documento_Identidad">DNI</label>
                          <input type="text" id="txt-Nu_Documento_Identidad_existe" name="Nu_Documento_Identidad" class="form-control required input-Mayuscula input-codigo_barra" placeholder="Ingresar número" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-md-12 div-contacto_existente">
                        <div class="form-group">
                          <label id="label-No_Contacto">Nombre(s) y Apellidos</label>
                          <input type="hidden" id="txt-AID_Contacto" name="AID_Contacto" class="form-control required">
                          <input type="text" id="txt-No_Contacto_existe" name="No_Contacto" class="form-control autocompletar_contacto" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
          
                      <div class="col-xs-12 col-sm-6 col-md-5 div-contacto_existente">
                        <label>Correo</label>
                        <div class="form-group">
                          <input type="text" id="txt-Txt_Email_Contacto_existe" name="Txt_Email_Contacto" placeholder="Ingresar correo" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-xs-6 col-sm-3 col-md-4 div-contacto_existente">
                        <label>Celular</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Celular_Contacto_existe" name="Nu_Celular_Contacto" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-3 div-contacto_existente">
                        <label>Teléfono</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Telefono_Contacto_existe" name="Nu_Telefono_Contacto" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <!-- Contacto Nuevo -->
                      <div class="col-xs-4 col-sm-3 col-md-3 div-contacto_nuevo">
                        <div class="form-group">
                          <label>T.D.I. <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-TiposDocumentoIdentidadContacto" name="ID_Tipo_Documento_Identidad" class="form-control" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-6 div-contacto_nuevo">
                        <div class="form-group">
                          <label id="label-Nombre_Documento_Identidad">DNI</label>
                          <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar número" autocomplete="off" maxlength="8">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-2 col-sm-3 col-md-3 text-center div-contacto_nuevo">
                        <label>Api</label>
                        <div class="form-group">
                          <button type="button" id="btn-cloud-api_orden_compra_contacto" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                  
                      <div class="col-xs-9 col-sm-9 col-md-9 div-contacto_nuevo">
                        <div class="form-group">
                          <label id="label-No_Contacto">Nombre(s) y Apellidos</label>
                          <input type="text" id="txt-No_Contacto" name="No_Contacto" class="form-control" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-3 col-sm-3 col-md-3 div-contacto_nuevo">
                        <div class="form-group estado">
                          <label>Estado</label>
            		  				<select id="cbo-Estado_Contacto" name="Nu_Estado_Contacto" class="form-control required">
            		  				  <option value="1">Activo</option>
            		  				  <option value="0">Inactivo</option>
            		  				</select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
          
                      <div class="col-xs-12 col-sm-6 col-md-5 div-contacto_nuevo">
                        <label>Correo</label>
                        <div class="form-group">
                          <input type="text" id="txt-Txt_Email_Contacto" name="Txt_Email_Contacto" placeholder="Ingresar correo" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-xs-6 col-sm-3 col-md-4 div-contacto_nuevo">
                        <label>Celular</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Celular_Contacto" name="Nu_Celular_Contacto" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-xs-6 col-sm-3 col-md-3 div-contacto_nuevo">
                        <label>Teléfono</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Telefono_Contacto" name="Nu_Telefono_Contacto" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Contacto -->
              </div>
              
      			  <div class="row">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenCompra" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b>Detalle</b></div>
                    <div class="panel-body">
      			          <div class="row">
          	            <input type="hidden" name="Nu_Tipo_Lista_Precio" value="2" class="form-control">                        
                        <div class="col-xs-12 col-md-9">
                          <label>Producto / Servicio <span class="label-advertencia">*</span></label>
                          <div class="form-group">
                            <input type="hidden" id="txt-Activar_Almacen" class="form-control" value="1">
                            <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="0"><!-- Compra -->
                            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="0">
                            <input type="hidden" id="txt-ID_Producto" class="form-control">
                            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
                            <input type="hidden" id="txt-Ss_Precio" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
                            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
                            <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" placeholder="Ingresar nombre / código de barra / código sku" value="" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-md-3">
                          <div class="form-group">
                            <label class="hidden-xs hidden-sm">&nbsp;</label>
                            <button type="button" id="btn-addProductoOrden" class="btn btn-success btn-md btn-block"><i class="fa fa-plus-circle"></i> Agregar Item Detalle</button>
                          </div>
                        </div>
                      </div>
                      
      			          <div class="row">
                        <div class="col-md-12">
                          <div class="table-responsive">
                            <table id="table-DetalleProductosOrdenCompra" class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th style="display:none;" class="text-left"></th>
                                  <th class="text-center" style="width: 10%;">Cantidad</th>
                                  <th class="text-center" style="width: 35%;">Item</th>
                                  <th class="text-center" style="width: 10%;">Precio</th>
                                  <th class="text-center" style="width: 15%;">Impuesto Tributario</th>
                                  <th class="text-center" style="display:none;">Sub Total</th>
                                  <th class="text-center" style="width: 10%;">% Dscto</th>
                                  <th class="text-center">Total</th>
                                  <th class="text-center"></th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Detalle -->
              </div>
              
      			  <div class="row">
      			    <div class="col-md-12">
      			      <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-comment-o"></i> <b>Garantía y Glosa</b></div>
                    <div class="panel-body">
                      <input type="text" name="Txt_Garantia" class="form-control" placeholder="Garantía" value="" autocomplete="off">
                      <br>
                      <textarea name="Txt_Glosa" class="form-control" placeholder="Glosa" value="" autocomplete="off"></textarea>
                    </div>
                  </div>
                </div>
              </div>
                
      			  <div class="row"><!-- Totales -->
                <div class="col-md-12">
      			    <div class="panel panel-default">
                  <div class="panel-heading"><i class="fa fa-money"></i> <b>Totales</b></div>
                  <div class="panel-body">
                    <div class="table-responsive">
                    <table class="table" id="table-OrdenCompraTotal">
                      <tr>
                        <td class="text-center"><label>% Descuento</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Inafectas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Exoneradas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gratuitas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gravadas</label></td>
                        <td class="text-right"><label>Dscto. Total (-)</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>I.G.V.</label></td>
                        <td class="text-right"><label>Total</label></td>
                      </tr>

                      <tr>
                        <td class="text-right">
    	  					        <input type="tel" class="form-control input-decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-inafecto" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-inafecto">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-exonerada" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-exonerada">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-gratuita" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-gratuita">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
    	  					        <input type="hidden" class="form-control" id="txt-subTotal" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-subTotal">0.00</span>
                        </td>
                        
                        <td class="text-right">
                          <input type="hidden" class="form-control" id="txt-descuento" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-descuento">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                            <input type="hidden" class="form-control" id="txt-impuesto" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-impuesto">0.00</span>
                        </td>
                        
                        <td class="text-right">
                          <input type="hidden" class="form-control" id="txt-total" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-total">0.00</span>
                        </td>
                      </tr>
                    </table><!-- ./Totales -->
                    </div>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
                  </div>
                </div>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
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