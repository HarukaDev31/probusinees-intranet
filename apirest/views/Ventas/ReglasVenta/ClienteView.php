<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
        
        <?php
        //array_debug($this->MenuModel->verificarAccesoMenuCRUD());
        ?>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row mb-3 div-Listar">
                <div class="col-12 col-sm-6">
                  <label>Servicio</label>
                  <div class="form-group">
                    <select id="cbo-filtro-tipo_servicio" name="" class="form-control required" style="width: 100%;">
                      <option value="0" selected="selected">Todos</option>
                      <option value="1">Otros</option>
                      <option value="2">Agente de Compra</option>
                      <option value="3">Carga Consolidada</option>
                      <option value="4">Importación Grupal</option>
                      <option value="5">Curso</option>
                      <option value="6">Viaje de Negocios</option>
                    </select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-6 col-sm-2">
                  <label class="d-none d-sm-block">&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>

                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <div class="col-6 col-sm-2">
                    <label class="d-none d-sm-block">&nbsp;</label>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarCliente()"><i class="fa fa-plus-circle"></i> Crear</button>
                  </div>
                
                  <div class="col-12 col-sm-2">
                    <label class="d-none d-sm-block">&nbsp;</label>
                    <button type="button" class="btn btn-default btn-block" onclick="importarExcelCliente()"><i class="fa fa-file-excel text-success"></i> Importar</button>
                  </div>
                <?php endif; ?>
              </div>

              <div class="table-responsive div-Listar">
                <table id="table-Cliente" class="table-nota table table-hover table-striped table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>Servicio</th>
                      <th>Tipo Doc.</th>
                      <th>Num Doc.</th>
                      <th>Nombre</th>
                      <th>Celular</th>
                      <th>Correo</th>
                      <!--<th>Dirección</th>-->
                      <th class="">Mensaje</th>
                      <th class="no-hidden">F. Registro</th>
                      <th>Estado</th>
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
                        <button type="button" id="btn-cloud-api" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud fa-lg"></i></button>
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
                        <input type="text" inputmode="tel" name="Nu_Telefono_Entidad" placeholder="Opcional" class="form-control" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                      <div class="form-group">
                        <label>Celular</label>
                        <input type="text" inputmode="tel" name="Nu_Celular_Entidad" placeholder="Opcional" class="form-control" autocomplete="off">
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
                  </div>
                  
                  <div class="row">
                    <div class="col-xs-6 col-sm-3 col-md-3">
                      <div class="form-group">
                        <label>País</label>
                        <select id="cbo-Paises" name="ID_Pais" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-3 div-mas_opciones">
                      <div class="form-group">
                        <label>Departamento</label>
                        <select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 div-mas_opciones">
                      <div class="form-group">
                        <label>Provincia</label>
                        <select id="cbo-Provincias" name="ID_Provincia" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-3 div-mas_opciones">
                      <div class="form-group">
                        <label>Distrito</label>
                        <select id="cbo-Distritos" name="ID_Distrito" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-xs-12 col-sm-5 col-md-4 div-mas_opciones">
                      <label>Contacto</label>
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
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Importar Clientes -->
<div class="modal fade modal_importar_cliente" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Ventas/ReglasVenta/ClienteController/importarExcelCliente" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Clientes</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="alert alert-light">
                <i class="fa fa-warning"></i> <label>Indicaciones:</label>
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                <br>&nbsp;
                <a id="a-download-client" href="<?php echo base_url(); ?>DownloadController/download/Probusiness_Plantilla_Clientes.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>

    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector_cliente"  style="width: 100%;">
                    <input type="file" id="my-file-selector_cliente" name="excel-archivo_cliente" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info_cliente').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info_cliente"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-client" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_cliente" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar Clientes -->

<?php
$i=0;
if ( !empty($sStatus) ){
  $i=1;
  $sClassModal = 'success';
  $sMessage = 'Datos cargados satisfactoriamente';
  if ( $sStatus == 'error-sindatos' ) {
    $sMessage = 'Llenar los campos obligatorios o los valores no son iguales a las columna del excel';
    $sClassModal = 'danger';  
  } else if ( $sStatus == 'error-bd' ) {
    $sMessage = quitarCaracteresEspeciales($sMessageErrorBD);
    $sClassModal = 'danger';  
  } else if ( $sStatus == 'error-archivo_no_existe' ) {
    $sMessage = 'El archivo no existe';
    $sClassModal = 'danger';  
  } else if ( $sStatus == 'error-copiar_archivo' ) {
    $sMessage = 'Error al copiar archivo al servidor';
    $sClassModal = 'danger';  
  }
?>
<script>
  alert('<?php echo $sMessage; ?>');
</script>
<?php } ?>
