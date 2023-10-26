<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header"></section>

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
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_TiposDocumento" name="Filtros_TiposDocumento" class="form-control">
    		  				  <option value="TipoDocumento">Tipo documento</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="50" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarTipo_Documento()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Tipo_Documento" class="table table-striped table-bordered">
              <thead>
              <tr>
                <th>Nombre</th>
                <!--<th>Nombre Breve</th>-->
                <th>Es SUNAT</th>
                <th>Codigo SUNAT</th>
                <th>Impuesto</th>
                <th>Cotización</th>
                <th>Venta</th>
                <th>Orden Compra</th>
                <th>Compra</th>
                <th class="no-sort">Estado</th>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                  <th class="no-sort"></th>
                <?php endif; ?>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                  <th class="no-sort"></th>
                <?php endif; ?>
              </tr>
              </thead>
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
  <!-- Modal -->
  <?php
  $attributes = array('id' => 'form-Tipo_Documento');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Tipo_Documento" role="dialog">
    <div class="modal-dialog modal-lg">
    	<div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center"></h4>
        </div>
        
      	<div class="modal-body">
      	  <input type="hidden" name="EID_Tipo_Documento" class="form-control required">
      	  <input type="hidden" name="ENo_Tipo_Documento" class="form-control required">
      	  
  			  <div class="row">
            <div class="col-md-6">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Tipo_Documento" name="No_Tipo_Documento" class="form-control required" placeholder="Ingresar nombre" autocomplete="off" maxlength="50">
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-4">
              <label>Nombre Breve <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="text" name="No_Tipo_Documento_Breve" class="form-control required" placeholder="Ingresar nombre breve" autocomplete="off" maxlength="20">
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-2">
              <div class="form-group">
                <label>Estado <span class="label-advertencia">*</span></label>
  		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
            
  			  <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Es SUNAT <span class="label-advertencia">*</span></label>
  		  				<select id="cbo-EsSunat" name="Nu_Es_Sunat" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-2">
              <label>Codigo SUNAT <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="tel" name="Nu_Sunat_Codigo" class="form-control required input-number" placeholder="Ingresar número" autocomplete="off" maxlength="2">
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-2">
              <div class="form-group">
                <label>Impuesto <span class="label-advertencia">*</span></label>
  		  				<select id="cbo-Impuesto" name="Nu_Impuesto" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
            
  			  <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Mostrar en Cotización?<span class="label-advertencia">*</span></label>
  		  				<select id="cbo-estado_cotizacion" name="Nu_Cotizacion_Venta" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Mostrar en Ventas?<span class="label-advertencia">*</span></label>
  		  				<select id="cbo-estado_venta" name="Nu_Venta" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>Mostrar en Orden Compra?<span class="label-advertencia">*</span></label>
  		  				<select id="cbo-estado_orden_compra" name="Nu_Orden_Compra" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>Mostrar en Compras?<span class="label-advertencia">*</span></label>
  		  				<select id="cbo-estado_compra" name="Nu_Compra" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
        </div>
        
      	<div class="modal-footer">
          <div class="col-md-6">
            <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir</button>
          </div>
          <div class="col-md-6">
            <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->