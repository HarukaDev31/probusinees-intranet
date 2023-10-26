<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <?php //array_debug($this->empresa); ?>
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
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
              <br>
              <div class="col-xs-12 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>Almacén</label>
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_crud txt-Filtro_Fe_Fin" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
                      
              <div class="col-xs-6 col-sm-6 col-md-3">
                <div class="form-group">
                  <label>Vendedor</label>
                  <select id="cbo-filtro-vendedor" name="ID_Mesero" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-Filtro_TiposDocumento" class="form-control">
        				    <option value="1" selected>Cotizacion</option>
    		  				  <!--
                      <option value="" selected>Todos</option>
        				      <option value="13">Orden de Pago</option>
                    -->
                  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2">
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
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>Contacto</label>
                  <input type="text" id="txt-Filtro_Contacto" class="form-control autocompletar_contacto" placeholder="Buscar por Nombre" maxlength="50" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-3">
                <div class="form-group">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-3">
                <div class="form-group">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarOrdenVenta()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-OrdenVenta" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort">Almacén</th>
                  <th class="no-sort">F. Emisión</th>
                  <!--<th class="no-sort">Tipo</th>-->
                  <th class="no-sort_left">Número</th>
                  <th class="no-sort_left">Cliente</th>
                  <!--<th class="no-sort_left">Contacto</th>-->
                  <th class="no-sort">M</th>
                  <th class="no-sort_right">Total</th>
                  <th class="no-sort">Estado</th>
                  <th class="no-sort_left">Vendedor</th>
                  <th class="no-sort">PDF</th><!--PDF o correo-->
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <th class="no-sort">Duplicar</th><!--Duplicar-->
                    <th class="no-sort">Vender</th>
                    <th class="no-sort">Guía</th>
                    <!--<th class="no-sort">Enlace</th> enlace de vender-->
                  <?php //endif; ?>
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php //endif; ?>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-OrdenVenta');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" name="EID_Empresa" class="form-control">
          	  <input type="hidden" name="EID_Documento_Cabecera" class="form-control">
          	  <input type="hidden" name="ENu_Estado" class="form-control">
          	  		  
      			  <div class="row">
                <div class="col-sm-12 col-md-6 cortar-padding">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Documento</b></div>
                    <div class="panel-body">
                      <div class="col-xs-6 col-sm-4 col-md-2 hidden">
                        <div class="form-group">
                          <label>Tipo <span class="label-advertencia">*</span></label>
                          <select id="cbo-TiposDocumento" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>F. Emisión <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Emision" name="Fe_Emision" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>F. Pago <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-MediosPago" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Moneda <span class="label-advertencia">*</span></label>
                          <select id="cbo-Monedas" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-4 div-MediosPago">
                        <div class="form-group">
                          <label>F. Vcto</label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Vencimiento" name="Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <input type="hidden" name="Nu_Tipo_Lista_Precio" value="1" class="form-control">
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <label>L. Precio</label>
                        <div class="form-group">
                          <select id="cbo-lista_precios" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <div class="css_tooltip">
                            <label>&nbsp;</label>
                            <button type="button" id="btn-adicionales_ov" class="btn btn-link btn-lg btn-block"  data-mostrar_campos_adicionales="0">Adicionales</button>
                            <span class="css_tooltiptext">Mostrar Vendedor, contacto, pdf y más</span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-xs-8 col-sm-12 col-md-5 col-lg-8 div-adicionales_ov">
                        <div class="form-group">
                          <label>Vendedor</label>
            		  				<select id="cbo-vendedor" name="ID_Mesero" class="form-control select2" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-4 col-sm-4 col-md-3 col-lg-4 div-adicionales_ov">
                        <div class="form-group">
                          <label>PDF</label>
                          <select id="cbo-formato_pdf" class="form-control" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>F. Entrega <span class="label-advertencia">*</span></label>
                          <div class="input-group date" style="width:100%">
                            <input type="text" id="txt-Fe_Entrega" name="Fe_Entrega" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                        <div class="form-group">
                          <label>¿Stock?</label>
                          <select id="cbo-descargar_stock" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                        <div class="form-group">
                          <label>Porcentaje</label>
            		  				<select id="cbo-porcentaje" name="Po_Comision" class="form-control" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Documento -->
                
                <div class="col-sm-12 col-md-6">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Cliente</b></div>
                    <div class="panel-body">
                      <div class="col-xs-6 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addCliente" id="radio-cliente_existente" class="flat-red" value="0"> Existente</label>
                      </div>
                      
                      <div class="col-xs-6 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addCliente" id="radio-cliente_nuevo" class="flat-red" value="1"> Nuevo</label>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 div-cliente_existente">
                        <div class="form-group">
                          <label>Cliente</label>
                          <input type="hidden" id="txt-AID" name="AID" class="form-control required">
                          <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-4 col-sm-4 col-md-4 hidden">
                        <div class="form-group">
                          <label># D.I.<span class="label-advertencia">*</span></label>
                          <input type="text" id="txt-ACodigo" name="ACodigo" class="form-control required" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-8 col-sm-8 col-md-8 hidden">
                        <div class="form-group">
                          <label>Dirección <span class="label-advertencia">*</span></label>
                          <input type="text" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <!-- Cliente Nuevo -->
                      <div class="col-xs-4 col-sm-3 col-md-3 div-cliente_nuevo">
                        <div class="form-group">
                          <label>T.D.I.</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de Documento Identidad">
                            <i class="fa fa-info-circle"></i>
                          </span>
            		  				<select id="cbo-TiposDocumentoIdentidadCliente" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-7 div-cliente_nuevo">
                        <div class="form-group">
                          <label id="label-Nombre_Documento_Identidad_Cliente">DNI</label></span>
                          <input type="text" id="txt-Nu_Documento_Identidad_Cliente" name="Nu_Documento_Identidad_Cliente" class="form-control input-Mayuscula input-codigo_barra" placeholder="Opcional" value="" maxlength="8" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-2 col-sm-3 col-md-2 text-center div-cliente_nuevo">
                        <label>Api</label>
                        <div class="form-group">
                          <button type="button" id="btn-cloud-api_orden_venta_cliente" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
              
                      <div class="col-xs-8 col-sm-10 col-md-9 col-lg-9 div-cliente_nuevo">
                        <div class="form-group">
                          <label id="label-No_Entidad_Cliente">Nombre y Apellidos</label><span class="label-advertencia"> *</span>
                          <input type="text" id="txt-No_Entidad_Cliente" name="No_Entidad_Cliente" class="form-control required" placeholder="Ingresar nombre" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-4 col-sm-2 col-md-3 col-lg-3 div-cliente_nuevo">
                        <div class="form-group estado">
                          <label>Estado</label>
            		  				<select id="cbo-Estado_Cliente" name="Nu_Estado_Cliente" class="form-control required">
            		  				  <option value="1">Activo</option>
            		  				  <option value="0">Inactivo</option>
            		  				</select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-7 col-sm-6 col-md-6 col-lg-4 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Email</label>
                          <input type="text" id="txt-Txt_Email_Entidad" name="Txt_Email_Entidad" class="form-control" placeholder="" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-5 col-sm-6 col-md-6 col-lg-4 div-cliente_nuevo">
                        <label>Celular</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Celular_Entidad_Cliente"  name="Nu_Celular_Entidad_Cliente" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad_Cliente" name="Txt_Direccion_Entidad_Cliente" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-5 col-sm-3 col-md-3 hidden">
                        <label>Telefono</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Nu_Telefono_Entidad_Cliente" name="Nu_Telefono_Entidad_Cliente" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Cliente -->

                <div class="col-sm-12 col-md-6 div-adicionales_ov">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Contacto</b> (opcional)</div>
                    <div class="panel-body">
                      <div class="row">
                        <input type="hidden" id="txt-ID_Tipo_Asiento" class="form-control" value="1">
                        
                        <div class="col-xs-6 text-center">
                          <label style="cursor: pointer;"><input type="radio" name="addContacto" id="radio-contacto_existente" class="flat-red" value="0"> Existente</label>
                        </div>
                        
                        <div class="col-xs-6 text-center">
                          <label style="cursor: pointer;"><input type="radio" name="addContacto" id="radio-contacto_nuevo" class="flat-red" value="1"> Nuevo</label>
                        </div>
                        
                        <div class="col-xs-4 col-md-6 div-contacto_existente hidden">
                          <div class="form-group id_tipo_documento_identidad">
                            <label>Tipo Doc. Identidad</label>
                            <select id="cbo-TiposDocumentoIdentidadContacto_existe" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-md-6 div-contacto_existente hidden">
                          <div class="form-group">
                            <label id="label-Nombre_Documento_Identidad">DNI</label>
                            <input type="text" id="txt-Nu_Documento_Identidad_existe" name="Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar número" maxlength="8" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-md-12 div-contacto_existente">
                          <div class="form-group">
                            <label id="label-No_Contacto">Nombre y Apellidos</label>
                            <input type="hidden" id="txt-AID_Contacto" name="AID_Contacto" class="form-control required">
                            <input type="text" id="txt-No_Contacto_existe" name="No_Contacto" class="form-control autocompletar_contacto" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
            
                        <div class="col-xs-12 col-sm-5 col-md-5 div-contacto_existente">
                          <label>Correo</label>
                          <div class="form-group">
                            <input type="text" id="txt-Txt_Email_Contacto_existe" name="Txt_Email_Contacto" placeholder="Ingresar correo" class="form-control" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                  
                        <div class="col-xs-7 col-sm-4 col-md-4 div-contacto_existente">
                          <label>Celular</label>
                          <div class="form-group">
                            <input type="tel" id="txt-Nu_Celular_Contacto_existe" name="Nu_Celular_Contacto" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-5 col-sm-3 col-md-3 hidden">
                          <label>Teléfono</label>
                          <div class="form-group">
                            <input type="tel" id="txt-Nu_Telefono_Contacto_existe" name="Nu_Telefono_Contacto" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <!-- Contacto Nuevo -->
                        <div class="col-xs-4 col-sm-3 col-md-3 div-contacto_nuevo">
                          <div class="form-group">
                            <label>T.D.I.<span class="label-advertencia">*</span></label>
                            <select id="cbo-TiposDocumentoIdentidadContacto" name="ID_Tipo_Documento_Identidad" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-6 col-md-7 div-contacto_nuevo">
                          <div class="form-group">
                            <label id="label-Nombre_Documento_Identidad">DNI</label></span>
                            <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar número" maxlength="8" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-2 col-sm-3 col-md-2 text-center div-contacto_nuevo">
                          <label>Api</label>
                          <div class="form-group">
                            <button type="button" id="btn-cloud-api_orden_venta_contacto" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                    
                        <div class="col-xs-8 col-sm-10 col-md-9 col-lg-9 div-contacto_nuevo">
                          <div class="form-group">
                            <label id="label-No_Contacto">Nombre y Apellidos</label><span class="label-advertencia"> *</span>
                            <input type="text" id="txt-No_Contacto" name="No_Contacto" class="form-control required" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-4 col-sm-2 col-md-3 col-lg-3 div-contacto_nuevo">
                          <div class="form-group estado">
                            <label>Estado</label>
                            <select id="cbo-Estado_Contacto" name="Nu_Estado_Contacto" class="form-control required">
                              <option value="1">Activo</option>
                              <option value="0">Inactivo</option>
                            </select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
            
                        <div class="col-xs-12 col-sm-5 col-md-5 div-contacto_nuevo">
                          <label>Correo</label>
                          <div class="form-group">
                            <input type="text" id="txt-Txt_Email_Contacto" name="Txt_Email_Contacto" placeholder="Ingresar correo" class="form-control" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                  
                        <div class="col-xs-7 col-sm-4 col-md-4 div-contacto_nuevo">
                          <label>Celular</label>
                          <div class="form-group">
                            <input type="tel" id="txt-Nu_Celular_Contacto" name="Nu_Celular_Contacto" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                  
                        <div class="col-xs-5 col-sm-3 col-md-3 hidden">
                          <label>Teléfono</label>
                          <div class="form-group">
                            <input type="tel" id="txt-Nu_Telefono_Contacto" name="Nu_Telefono_Contacto" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div><!-- ./row -->
                    </div>
                  </div>
                </div><!-- ./Contacto -->
              </div>
              
      			  <div class="row">
                <div class="col-md-12 cortar-padding">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b>Producto / Servicio</b></div>
                    <div class="panel-body" style="padding-top: 0px;">
                      <div class="sticky">
      			          <div class="row">                        
                        <div class="col-xs-12 col-md-9">
                          <label class="hidden">Producto / Servicio <span class="label-advertencia">*</span></label>
                          <div class="form-group">
                            <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                            <input type="hidden" id="txt-Nu_Tipo_Producto" class="form-control" value="2"><!-- No muestra los productos de tipo interno -->
                            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="">
                            <input type="hidden" id="txt-ID_Producto" class="form-control">
                            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
                            <input type="hidden" id="txt-Ss_Precio" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
                            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Qt_Producto" class="form-control">
                            <input type="hidden" id="txt-nu_tipo_item" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Icbper" class="form-control">
                            <input type="hidden" id="txt-Ss_Icbper" class="form-control">
                            <input type="hidden" id="txt-nu_activar_precio_x_mayor" class="form-control">
                            <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-md-3">
                          <div class="form-group">
                            <label clasS="hidden">&nbsp;</label>
                            <button type="button" id="btn-addProductoOrdenVenta" class="btn btn-success btn-md btn-block">Agregar Item</button>
                          </div>
                        </div>
                      </div>
                      </div>
                      
      			          <div class="row">
                        <div class="col-md-12 delete-position">
                          <div class="table-responsive">
                            <table id="table-DetalleProductosOrdenVenta" class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th style="display:none;" class="text-left"></th>
                                  <th class="text-center" style="width: 10%;">Cantidad</th>
                                  <th class="text-center" style="width: 30%;">Item</th>
                                  <th class="text-center" style="width: 5%;">Nota</th>
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
                    <div class="panel-heading"><i class="fa fa-comment-o"></i> <b>Garantía y Observaciones</b> <span class="hidden-xs">(opcional)</span> <button type="button" id="btn-adicionales_ov_garantia_glosa" class="btn btn-link"  data-ver_adicionales_ov_garantia_glosa="0">Agregar</button></div>
                    <div class="panel-body div-adicionales_ov_garantia_glosa">
                      <input type="text" name="Txt_Garantia" class="form-control" placeholder="Garantía" value="" autocomplete="off">
                      <br>
                      <label>Observaciones</label>
                      <textarea name="Txt_Glosa" class="form-control" placeholder="Glosa" value="" autocomplete="off"></textarea>
                    </div>
                  </div>
                </div>
              </div>
                
      			  <div class="row"><!-- Totales -->
                <div class="col-md-12">
      			    <div class="panel panel-default">
                  <div class="panel-heading text-right">
                    TOTAL <b class="hidden">CANTIDAD: </b> <span id="span-total_cantidad" class="hidden">0</span> <span class="span-signo" style="font-size: 20px;font-weight: bold;"></span> <span id="span-total_importe" style="font-size: 20px;font-weight: bold;">0</span><button type="button" id="btn-ver_total_todo" class="btn btn-link" data-ver_total_todo="0">VER / DESCUENTO</button>
                  </div>
                  <div class="panel-body panel_body_total_todo">
                    <div class="table-responsive">
                    <table class="table" id="table-OrdenVentaTotal">
                      <tr>
                        <td class="text-center"><label>% Descuento</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Inafectas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Exoneradas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gratuitas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gravadas</label></td>
                        <td class="text-right"><label>Dscto. Total (-)</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>I.G.V.</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>ICBPER</label></td>
                        <td class="text-right"><label>Total</label></td>
                      </tr>

                      <tr>                      
                        <td class="text-right">
                          <input type="text" class="form-control input-decimal input-size_otros" inputmode="decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
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
                          <input type="hidden" class="form-control" id="txt-descuento_igv" value="0.00"/>
                          <input type="hidden" class="form-control" id="txt-descuento" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-descuento">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                            <input type="hidden" class="form-control" id="txt-impuesto" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-impuesto">0.00</span>
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-total_icbper" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-total_icbper">0.00</span>
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
  
  <!-- Modal delivery -->
  <div class="modal fade modal-delivery" id="modal-default">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="text-center">Delivery</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <label>Transporte</label> (Opcional)
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para agregar Transporte o Delivery, ingresar a la opción Personal > Maestro Delivery">
                <i class="fa fa-info-circle"></i>
              </span>
              <div class="form-group">
                <select id="modal-cbo-transporte" name="ID_Transporte_Delivery" class="form-control" style="width: 100%;"></select>
              </div>
            </div>
            <div class="col-xs-12">
              <label>Dirección</label> (Opcional)
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ingresa la dirección, se mostrará impreso en el ticket. También se guardará y se mostrará para la próxima venta">
                <i class="fa fa-info-circle"></i>
              </span>
              <div class="form-group">
                <textarea name="Txt_Direccion_Delivery" class="form-control"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-xs-12">
            <div class="form-group">
              <button type="button" id="btn-salir_delivery" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
            </div>
          </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /. Modal delivery -->
</div>
<!-- /.content-wrapper -->

<?php
$attributes = array('id' => 'form-generar_guia');
echo form_open('', $attributes);
?>
<!-- modal generar_guia -->
<div class="modal fade modal-generar_guia" id="modal-default">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-body">
        <input type="hidden" name="Hidden_ID_Empresa" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Organizacion" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Almacen" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Moneda" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Documento_Cabecera" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Entidad" class="form-control" value="">
        <input type="hidden" name="Hidden_ID_Lista_Precio_Cabecera" class="form-control" value="">
        <input type="hidden" name="Hidden_Fe_Emision" class="form-control" value="">
        <input type="hidden" name="Hidden_Fe_Emision_Hora" class="form-control" value="">
        <input type="hidden" name="Hidden_Ss_Total" class="form-control" value="">
        
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12">
            <h4 class="text-center" id="modal-header-generar_guia-title"></h4>
            <h4 class="text-center" id="">Generar Guía / Salida de Inventario</h4>
          </div>

          <div class="col-xs-12 col-sm-12 col-md-12 hidden">
            <label id="generar_guia-modal-body-cliente"></label>
          </div>
        </div>

        <div class="row div-tipoguia">
          <br>
          <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-left hidden"><b>Documento:</b></div>

          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="14">
            <label style="cursor: pointer;" class="div-tipoguia"><input type="radio" name="radio-TipoDocumento" id="radio-guia_i" class="flat-red div-tipoguia" value="14"> Interna</label>
          </div>
        
          <div class="col-xs-3 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="7" style="padding-left: 0px;padding-right: 0px;">
            <label style="cursor: pointer;"><input type="radio" name="radio-TipoDocumento" id="radio-guia_f" class="flat-red" value="7"> Física</label>
          </div>
        
          <div class="col-xs-5 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="8" id="div-tipoguia_electronica">
            <label style="cursor: pointer;"><input type="radio" name="radio-TipoDocumento" id="radio-guia_e" class="flat-red" value="8"> Electrónica</label>
          </div>
        </div>
        
        <br>
        
        <div class="row hidden"><!-- Flete -->
          <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 text-left">
            <b>Flete</b>
          </div>

          <div class="col-xs-3 col-sm-4 col-md-2 col-lg-2 text-left div-flete" data-estado="1">
            <label style="cursor: pointer;"><input type="radio" name="radio-addFlete" id="radio-flete_si" class="flat-red" value="1"> Si</label>
          </div>
        
          <div class="col-xs-5 col-sm-4 col-md-2 col-lg-2 text-left div-flete" data-estado="0">
            <label style="cursor: pointer;"><input type="radio" name="radio-addFlete" id="radio-flete_no" class="flat-red" value="0"> No</label>
          </div>
        </div>

        <div class="row" id="div-addFlete"><!-- Flete -->
          <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-electronico">
            <label>Transporte</label>
            <div class="form-group">
              <label style="cursor: pointer;"><input type="radio" name="radio-TipoTransporte" id="radio-tipo_transporte_publico" class="flat-red" value="01" checked> Público &nbsp;&nbsp;</label>
              <label style="cursor: pointer;"><input type="radio" name="radio-TipoTransporte" id="radio-tipo_transporte_privado" class="flat-red" value="02"> Privado</label>
            </div>
          </div>

          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <label>Transportista</label><!--si transporte es privado es obligatorio-->
            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear la opción es Personal > Maestro Delivery.">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <select id="cbo-transporte" name="AID_Transportista" class="form-control select2" style="width: 100%;"></select>                        
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <div class="form-group">
              <label>F. Emisión</label>
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-Fe_Traslado" name="Fe_Traslado" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <label>Placa</label><!--si transporte es privado es obligatorio-->
            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Obligatorio si es GUÍA ELECTRÓNICA y Transporte Privado">
            <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <input type="text" id="txt-No_Placa" name="No_Placa" placeholder="Opcional" class="form-control required input-Mayuscula input-codigo_barra" maxlength="6" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-electronico">
            <label>Licencia</label><!--si transporte es privado es obligatorio-->
            <div class="form-group">
              <input type="text" id="txt-No_Licencia" inputmode="number" name="No_Licencia" placeholder="Opcional" class="form-control input-number" maxlength="10" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-electronico">
            <div class="form-group">
              <label>Ubigeo</label>
              <select id="cbo-ubigeo_inei-modal" name="ID_Ubigeo_Inei_Llegada" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Dirección</label>
            <div class="form-group">
              <input type="text" id="txt-Txt_Direccion_Entidad-modal" name="Txt_Direccion_Entidad-modal" placeholder="Obligatorio" class="form-control" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-datos_guia_electronica div-electronico">
            <label>Peso Bruto</label>
            <div class="form-group">
              <input type="text" id="txt-Ss_Peso_Bruto" inputmode="decimal" name="Ss_Peso_Bruto" placeholder="Obligatorio" class="form-control input-decimal" maxlength="20" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-datos_guia_electronica div-electronico">
            <label>Cantidad Bultos</label>
            <div class="form-group">
              <input type="text" id="txt-Nu_Bulto" inputmode="number" name="Nu_Bulto" placeholder="Opcional" class="form-control input-number" maxlength="12" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
              <label>¿Descargar Stock?</label>
              <select id="cbo-descargar_stock-modal" name="Nu_Descargar_Stock-modal" class="form-control required" style="width: 100%;">
                <option value="1">Si</option>
                <option value="0">No</option>
              </select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div><!-- ./ Flete -->
      </div>

      <div class="modal-footer">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <button type="button" id="btn-generar_guia" class="btn btn-primary btn-md btn-block pull-center">Generar Guía</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /. modal generar_guia -->
<?php echo form_close(); ?>

<?php
$attributes = array('id' => 'form-datos_adicionales_venta');
echo form_open('', $attributes);
?>
<!-- Modal Adicionales -->
<div class="modal fade modal-adicionales" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Adicionales</h4>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12 col-sm-4 col-md-3 col-lg-6">
            <div class="form-group">
              <label>Vendedor</label>
              <select id="cbo-vendedor-modal" name="ID_Mesero" class="form-control"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <label>Detracción</label>
            <div class="form-group" style="height: 0px;">
              <label style="cursor: pointer;">
                <input type="radio" style="cursor: pointer;" name="radio-addDetraccion" class="flat-red" id="radio-InactiveDetraccion" onclick="addDetraccion(this.value);" value="0" checked> No
              </label>
              <label style="cursor: pointer;">
                &nbsp;<input type="radio" style="cursor: pointer;" name="radio-addDetraccion" class="flat-red" id="radio-ActiveDetraccion" onclick="addDetraccion(this.value);" value="1"> Si
              </label>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 div-detraccion">
            <label>% Detraccion</label>
            <div class="form-group">
              <input type="text" class="form-control input-decimal" inputmode="decimal" id="txt-Po_Detraccion" name="Po_Detraccion" value="12" autocomplete="off" />
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <label>Retención</label>
            <div class="form-group">
              <label style="cursor: pointer;">
                <input type="radio" style="cursor: pointer;" name="radio-addRetencion" class="flat-red" id="radio-InactiveRetencion" onclick="addRetencion(this.value);" value="0" checked> No
              </label>
              <label style="cursor: pointer;">
                &nbsp;<input type="radio" style="cursor: pointer;" name="radio-addRetencion" class="flat-red" id="radio-ActiveRetencion" onclick="addRetencion(this.value);" value="1"> Si
              </label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>O/C / Servicio</label>
            <div class="form-group">
              <input type="text" name="No_Orden_Compra_FE" class="form-control" maxlength="20" placeholder="Opcional" value="" autocomplete="off">
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>Placa</label>
            <div class="form-group">
              <input type="text" name="No_Placa_FE" class="form-control" maxlength="255" placeholder="Opcional" value="" autocomplete="off">
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>Nro. Expediente</label>
            <div class="form-group">
              <input type="text" name="Nu_Expediente_FE" class="form-control" maxlength="50" placeholder="Opcional" value="" autocomplete="off">
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <label>Unidad Ejecutora</label>
            <div class="form-group">
              <input type="text" name="Nu_Codigo_Unidad_Ejecutora_FE" class="form-control" maxlength="50" placeholder="Opcional" value="" autocomplete="off">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-12 col-sm-12">
          <div class="form-group">
            <button type="button" id="btn-salir_adicionales" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal Adicionales -->
<?php echo form_close(); ?>