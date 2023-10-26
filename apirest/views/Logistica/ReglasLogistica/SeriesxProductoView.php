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
      <!-- ./New box-header -->
    </div>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-12 col-sm-4 col-md-4">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Marcas" name="Filtros_Marcas" class="form-control">
    		  				  <option value="Serie">Nombre de Serie</option>
                    <option value="Producto">Nombre de Producto</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-4">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-4">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarPos()">Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Pos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Serie</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                </tr>
              </thead>
            </table>
          </div>

          <!-- Modal -->
          <div class="box-body div-AgregarEditar">
          <?php
          $attributes = array('id' => 'form-Pos');
          echo form_open('', $attributes);
          ?>
            <input type="hidden" name="EID_Series_x_Producto" class="form-control">
            <input type="hidden" name="ENo_Serie_Producto" class="form-control">
            
            <div class="row">
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-sm-6 col-md-12">
                <div class="form-group">
                  <label>Empresa <span class="label-advertencia">*</span></label>
                  <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>
              
              <div class="col-xs-6 col-sm-6 col-md-6">
                <label>Item <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <input type="hidden" id="txt-AID" name="AID" class="form-control">
                  <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                  <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/globalAutocomplete" data-global-table="producto" placeholder="Buscar por nombre / código" value="" autocomplete="off">
                <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-4">
                <label>Serie</label>
                <div class="form-group">
                  <input type="text" id="txt-No_Serie_Producto" name="No_Serie_Producto" class="form-control input-codigo_barra input-Mayuscula" placeholder="Ingresar serie" maxlength="20" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
                      
              <div class="col-xs-12 col-sm-2 col-md-2 div-boton_agregar_serie">
                <label class="hidden-xs">&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-addProductosEnlaces" class="btn btn-success btn-block"> Agregar</button>
                </div>
              </div>
              
              <div class="col-xs-12">
                <div class="table-responsive div-series_x_producto_add">
                  <table id="table-series_x_producto_add" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th style='display:none;' class="text-left">ID</th>
                        <th class="text-left">Producto</th>
                        <th class="text-center">Serie</th>
                        <th class="text-center">Eliminar</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
            
            <div class="row">
              <br/>
              <div class="col-xs-6 col-md-6">
                <div class="form-group">
                  <button type="button" id="btn-cancelar" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                </div>
              </div>
              <div class="col-xs-6 col-md-6">
                <div class="form-group">
                  <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
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