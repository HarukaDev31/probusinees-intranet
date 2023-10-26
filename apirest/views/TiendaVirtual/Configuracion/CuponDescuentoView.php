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
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-12">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2"  style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>

              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_CuponDescuento" name="Filtros_CuponDescuento" class="form-control">
    		  				  <option value="CuponDescuento">Código Cupón</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar..." value="" autocomplete="off" maxlength="30">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block"  onclick="agregarCuponDescuento()">Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-CuponDescuento" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                <th>Empresa</th>
                <?php } ?>
                <th>Código</th>
                <th>Descripción</th>
                <th>Tipo Cupón</th>
                <th>Valor</th>
                <th>F. Inicio</th>
                <th>F. Vencimiento</th>
                <th>Total de Uso</th>
                <th class="no-sort">Estado</th>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                  <th class="no-sort">Editar</th>
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
  $attributes = array('id' => 'form-CuponDescuento');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-CuponDescuento" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Cupon_Descuento" class="form-control">
        <input type="hidden" name="ENo_Codigo_Cupon_Descuento" class="form-control">
    	  
        <?php
        if ( $this->user->No_Usuario == 'root' ){ ?>
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Empresa <span class="label-advertencia">*</span></label>
            <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
        <?php } else { ?>
          <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
        <?php } ?>
        
        <div class="row">
          <div class="col-xs-12 col-md-4">
            <label>Código <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="No_Codigo_Cupon_Descuento" name="No_Codigo_Cupon_Descuento" placeholder="Ingresar código" class="required form-control input-codigo_barra input-Mayuscula" autocomplete="off" value="" maxlength="20">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-8">
            <label>Descripción</label>
            <div class="form-group">
              <input type="text" id="Txt_Cupon_Descuento" name="Txt_Cupon_Descuento" placeholder="Opcional" class="form-control" autocomplete="off" value="">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12 col-md-6">
            <div class="form-group">
              <label>Tipo de Cupón <span class="label-advertencia">*</span></label>
              <select id="cbo-tipo_cupon_descuento" name="Nu_Tipo_Cupon_Descuento" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Importe Cupón <span class="label-advertencia">*</span></label>
              <input type="text" name="Ss_Valor_Cupon_Descuento" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off" value="" placeholder="Valor">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Gasto Mínimo</label>
              <input type="text" name="Ss_Gasto_Minimo_Compra" inputmode="decimal" class="form-control input-decimal" maxlength="13" autocomplete="off" value="" placeholder="Opcional">
              <span class="help-block" id="error"></span>
            </div>
          </div>  
        </div>

        <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <label>F. Inicio</label>
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-Fe_Inicio" name="Fe_Inicio" value="<?php echo ToDateBD(dateNow('fecha')); ?>" class="form-control required date-picker-inicio" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          

          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <label>F. Vencimiento</label>
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-Fe_Vencimiento" name="Fe_Vencimiento" value="<?php echo ToDateBD(dateNow('fecha')); ?>" class="form-control required date-picker-fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      
    	<div class="modal-footer">
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->