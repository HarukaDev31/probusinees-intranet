<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <?php
  $sCssDisplay17='style="display:none"';
  if ( $this->empresa->Nu_Tipo_Rubro_Empresa == '17' ){//La cava del baco
    $sCssDisplay17='';
  }
  $sCampoNombreContacto='Nombre Contacto';
  if ( $this->empresa->Nu_Tipo_Rubro_Empresa == '18' ){//Escuela de musica
    $sCampoNombreContacto='Alumno';
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
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Ver video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Ver video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
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
        $sMessage = 'Llenar los campos obligatorios o el valor del tipo de documento identidad no se encuentra en la columna del excel';
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
              <div class="col-xs-12 col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Entidades" name="Filtros_Entidades" class="form-control">
    		  				  <option value="Cliente">Nombre Cliente</option>
    		  				  <option value="NumeroDocumentoIdentidad">Numero Documento Identidad</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-5">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarCliente()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-default btn-block" onclick="importarExcelCliente()"><i class="fa fa-file-excel-o color_icon_excel"></i> Importar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Cliente" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Tipo Doc.</th>
                  <th>Num Doc.</th>
                  <th>Nombre</th>
                  <th>Celular</th>
                  <th>Correo</th>
                  <th>Dirección</th>
                  <th class="no-hidden">Días Crédito</th>
                  <th class="no-hidden"><?php echo $sCampoNombreContacto; ?></th>
                  <th class="no-hidden">Descripción</th>
                  <th class="no-hidden">F. Registro</th>
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
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Cliente');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control required">
          	  <input type="hidden" id="txt-EID_Entidad" name="EID_Entidad" class="form-control required">
          	  <input type="hidden" id="txt-ENu_Documento_Identidad" name="ENu_Documento_Identidad" class="form-control required">
          	  <input type="hidden" id="txt-ENo_Entidad" name="ENo_Entidad" class="form-control required">
              
              <div class="row">
                <div class="col-xs-4 col-sm-5 col-md-2">
                  <div class="form-group">
                    <label>T.D.I.</label>
      		  				<select id="cbo-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-5 col-sm-5 col-md-3">
                  <div class="form-group">
                    <label id="label-Nombre_Documento_Identidad">DNI</label><span class="label-advertencia"> *</span>
                    <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar" maxlength="8" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-3 col-sm-2 col-md-1 text-center div-api">
                  <label>Api</label>
                  <div class="form-group">
                    <button type="button" id="btn-cloud-api" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
            
                <div class="col-xs-12 col-sm-12 col-md-6">
                  <label id="label-No_Entidad">Nombre(s) y Apellidos</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" name="No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <label class="" style="cursor:pointer;">
                    <div class="icheckbox_flat-green">
                      <input type="checkbox" id="checkbox-mas_filtros" name="filtro-mas_filtros" class="flat-red">
                    </div>
                    Más opciones
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Mostrar mas campos celular, correo, dirección, días de crédito y más">
                      <i class="fa fa-info-circle"></i>
                    </span>
                    <div><br></div>
                  </label>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-10 div-mas_opciones">
                  <label>Dirección</label>
                  <div class="form-group">
                    <input type="text" name="Txt_Direccion_Entidad" placeholder="Opcional" class="form-control" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                  <div class="form-group estado">
                    <label>Estado <span class="label-advertencia">*</span></label>
      		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                  <div class="form-group">
                    <label>Días Crédito</label>
                    <input type="text" inputmode="numeric" name="Nu_Dias_Credito" placeholder="Opcional" class="form-control input-number" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                  <div class="form-group">
                    <label>Telefono</label>
                    <input type="text" inputmode="tel" name="Nu_Telefono_Entidad" placeholder="Opcional" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                  <div class="form-group">
                    <label>Celular</label>
                    <input type="text" inputmode="tel" name="Nu_Celular_Entidad" placeholder="Opcional" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-8 col-md-4 div-mas_opciones">
                  <label>Correo</label>
                  <div class="form-group">
                    <input type="text" inputmode="email" name="Txt_Email_Entidad" placeholder="Opcional" class="form-control" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-4 col-md-2" <?php echo $sCssDisplay17; ?>>
                  <div class="form-group">
                    <label>Tipo</label>
      		  				<select id="cbo-tipo_cliente_1" name="ID_Tipo_Cliente_1" class="form-control" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-3 hidden">
                  <div class="form-group">
                    <label>País</label>
      		  				<select id="cbo-Paises" name="ID_Pais" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-4 div-mas_opciones">
                  <div class="form-group">
                    <label>Departamento</label>
      		  				<select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-4 col-md-4 div-mas_opciones">
                  <div class="form-group">
                    <label>Provincia</label>
      		  				<select id="cbo-Provincias" name="ID_Provincia" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 div-mas_opciones">
                  <div class="form-group">
                    <label>Distrito</label>
      		  				<select id="cbo-Distritos" name="ID_Distrito" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12 col-sm-5 col-md-4 div-mas_opciones">
                  <label><?php echo $sCampoNombreContacto; ?></label>
                  <div class="form-group">
                    <input type="text" name="No_Contacto" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-3 col-md-2 div-mas_opciones">
                  <label>F. Nacimiento</label>
                  <div class="form-group">                    
                    <div class="input-group date" style="width:100%">
                      <input type="text" name="Fe_Nacimiento" placeholder="Opcional" class="form-control date-picker-chid" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                    </div>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                  <label>Celular <span class="hidden-md">Contacto</span></label>
                  <div class="form-group">
                    <input type="text" inputmode="tel" name="Nu_Celular_Contacto" placeholder="Opcional" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-md-4 div-mas_opciones">
                  <label>Correo Contacto</label>
                  <div class="form-group">
                    <input type="text" inputmode="email" name="Txt_Email_Contacto" placeholder="Opcional" class="form-control" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
              <div class="row div-mas_opciones">
                <div class="col-xs-12 col-md-12">
                  <label>Descripción</label>
                  <div class="form-group">  
                    <textarea name="Txt_Descripcion" placeholder="Opcional" class="form-control"></textarea>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
    	        <div class="row hidden">
                <div class="col-xs-12 col-md-12">
                  <div class="form-group">
                    <div class="well well-sm">
                      <i class="fa fa-warning"></i> Indicaciones:
                      <br>- Imágen <b>opcional</b>
                      <br>- Nombre imágen <b>Número Documento Identidad</b>
                      <br>- Formatos: <b>PNG</b>
                      <br>- Tamaño <b>Ancho: 255px</b> y <b>Alto: 255px</b>
                      <br>- Peso Máximo <b>1 MB</b>
                    </div>
                  </div>
                </div>
              </div>
              
      			  <div class="row hidden">
                <div class="col-xs-12 col-md-12 text-center divDropzone"></div>
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