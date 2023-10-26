<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3><?php //array_debug($this->empresa); ?>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
          </h3>
        </div>
      </div>
      <!-- ./New box-header -->
    </div>
    <?php
    $i=0;
    if ( !empty($sStatus) ){
      $i=1;
      $sClassModal = 'success';
      $sMessage = 'Datos cargados satisfactoriamente';
      if ( (int)$iCantidadNoProcesados > 0 ){
        $sMessage .= '. Pero tiene ' . $iCantidadNoProcesados . ' registro(s) no procesados';
      }
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
              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Productos" name="Filtros_Productos" class="form-control">
    		  				  <option value="Producto">Nombre</option>
    		  				  <option value="CodigoBarra">Código</option>
    		  				  <option value="CodigoSKU">SKU</option>
                    <option value="Grupo">Grupo</option>
                    <option value="Categoria">Categoría</option>
                    <option value="SubCategoria">Sub Categoría</option>
                    <option value="UnidadMedida">Unidad Medida</option>
                    <option value="Marca">Marca</option>
                    <option value="Impuesto">Impuesto</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="250" placeholder="Buscar..." value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <select id="cbo-filtro-estado_producto" name="Nu_Estado" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">- Todos Estado -</option>
    		  				  <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarProducto()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para importación masiva de productos o servicios" class="btn btn-success btn-block" onclick="importarExcelProductos()"><i class="fa fa-file-excel-o color-white"></i> Importar Excel<span class="hidden-xs"> Productos</span></button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- ./box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Producto" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Grupo</th>
                  <th class="no-hidden">U.M.</th>
                  <th class="no-hidden">Categoría</th>
                  <th class="no-hidden">SubCategoría</th>
                  <th class="no-hidden">Marca</th>
                  <th>Código</th>
                  <th class="no-hidden">SKU</th>
                  <th>Nombre</th>
                  <th>Impuesto</th>
                  <th class="sort_right">Stock</th>
                  <th class="sort_right">Precio</th>
                  <th class="no-hidden sort_right">Costo</th>
                  <th class="no-hidden sort_right">Costo Promedio</th>
                  <th class="no-hidden sort_right">Stock Mínimo</th>
                  <th class="no-hidden sort_right">Stock Máximo</th>
                  <th class="no-sort">Estado</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort">Eliminar</th>
                  <?php endif; ?>
                  <th class="no-sort no-hidden">Estado x Almacen</th><!-- Imprimir codigo de barra -->
                  <th class="no-sort no-hidden">Imprimir Código Barra</th><!-- Imprimir codigo de barra -->
                  <th class="no-sort img_sort">Imagen</th><!-- img -->
                  <th class="no-sort img_sort">Add Variante</th>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Producto');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
          	  <input type="hidden" id="txt-EID_Producto" name="EID_Producto" class="form-control">
          	  <input type="hidden" id="txt-ENu_Codigo_Barra" name="ENu_Codigo_Barra" class="form-control">
          	  <input type="hidden" id="txt-ENo_Codigo_Interno" name="ENo_Codigo_Interno" class="form-control">
          	  <input type="hidden" id="hidden-nombre_imagen" name="No_Imagen_Item" class="form-control">
              <input type="hidden" id="hidden-id_imagen" name="ID_Imagen_Item" class="form-control">
              <input type="hidden" id="hidden-tamano_imagen" name="Tamano_Imagen_Item" class="form-control">
    	  
              <?php
                $sCssDisplayViewHideFarmacia='style="display:none"';
                $sCssDisplayViewHideTiendaGranel='style="display:none"';
                $sCssDisplayViewHideLavanderia='style="display:none"';
                $sCssDisplayViewHideGeneral='';
                if ( $this->empresa->Nu_Tipo_Rubro_Empresa == 1 ){//1 = Farmacia
                  $sCssDisplayViewHideFarmacia='';
                }
                if ( $this->empresa->Nu_Tipo_Rubro_Empresa == 2 ){//2 = Tienda a granel
                  $sCssDisplayViewHideTiendaGranel='';
                }
                if ( $this->empresa->Nu_Tipo_Rubro_Empresa == 3 ){//3 = Lavanderia
                  $sCssDisplayViewHideLavanderia='';
                }

                if ( $this->empresa->Nu_Tipo_Rubro_Empresa == 3 ){//3 = Lavanderia
                  $sCssDisplayViewHideGeneral='style="display:none"';
                }
                
                $sCssDisplayViewHideEcommerceMarketplace='style="display:none"';
                if ( $this->empresa->ID_Empresa_Marketplace > 0 ){// Es decir; si tiene una empresa asocioada
                  $sCssDisplayViewHideEcommerceMarketplace='';
                }
              ?>

              <div class="row">
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                      <div class="form-group">
                        <label>Grupo <span class="label-advertencia">*</span></label>
                        <select id="cbo-TiposItem" name="Nu_Tipo_Producto" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-4 col-md-3 hidden"><!-- div-Producto -->
                      <div class="form-group">
                        <label>Tipo Producto <span class="label-advertencia">*</span></label>
                        <select id="cbo-TiposExistenciaProducto" name="ID_Tipo_Producto" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-4 col-md-2 col-lg-2" style="display: none;">
                      <div class="form-group">
                        <label>Ubicación Inv.</label>
                        <select id="cbo-UbicacionesInventario" name="ID_Ubicacion_Inventario" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                      <div class="form-group">
                        <label>Código</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Acepta solo números, letras o ambos. (Cantidad de caracteres mínimo de 1 hasta 16 dígitos)">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <input type="text" id="txt-Nu_Codigo_Barra" name="Nu_Codigo_Barra" class="form-control input-codigo_barra input-Mayuscula" placeholder="Obligatorio" maxlength="20" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                      <div class="form-group">
                        <label>SKU</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Código interno de la empresa">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <input type="text" id="txt-No_Codigo_Interno" name="No_Codigo_Interno" class="form-control input-codigo_barra input-Mayuscula" placeholder="Opcional" maxlength="20" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                      <div class="form-group">
                        <label>Impuesto <span class="label-advertencia">*</span></label>
                        <select id="cbo-Impuestos" name="ID_Impuesto" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="display: none;">
                      <div class="form-group">
                        <label>Producto SUNAT <span class="label-advertencia">*</span></label>
                        <input type="hidden" id="hidden-ID_Tabla_Dato" name="ID_Tabla_Dato" class="form-control">
                        <input type="text" id="txt-No_Descripcion" name="No_Descripcion" class="form-control autocompletar_producto_sunat" row="1" placeholder="Ingresar nombre" value="" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
                      <div class="form-group">
                        <label>Nombre <span class="label-advertencia">*</span></label>
                        <textarea name="No_Producto" class="form-control required" rows="1" placeholder="Obligatorio" maxlength="250"></textarea>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
                      <div class="form-group">
                        <label>Precio</label>
                        <input type="text" name="Ss_Precio" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off" placeholder="Obligatorio">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
                      <div class="form-group">
                        <label>Costo</label>
                        <input type="text" name="Ss_Costo" inputmode="decimal" class="form-control input-decimal" maxlength="13" autocomplete="off" placeholder="Opcional">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div>
                    
                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                      <label>Categoría<span class="label-advertencia">*</span></label>
                      <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/CategoriaController/listarCategorias'; ?>">[Crear]</a>
                      <div class="form-group">                    
                        <select id="cbo-categoria" name="ID_Familia" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                      <label>Sub Cate.</label>
                      <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/LineaController/listarLineas'; ?>">[Crear]</a>
                      <div class="form-group">
                        <select id="cbo-sub_categoria" name="ID_Sub_Familia" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                      <label>U.M. <span class="label-advertencia">*</span></label>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Unidad de Medida">
                        <i class="fa fa-info-circle"></i>
                      </span><a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/UnidadMedidaController/listarUnidadesMedida'; ?>">[Crear]</a>
                      <div class="form-group">
                        <select id="cbo-UnidadesMedida" name="ID_Unidad_Medida" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                      <label>Marca</label>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Marca">
                        <i class="fa fa-info-circle"></i>
                      </span><a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/MarcaController/listarMarcas'; ?>">[Crear]</a>
                      <div class="form-group">
                        <select id="cbo-Marcas" name="ID_Marca" class="form-control select2" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div>
                </div><!-- col -->

                <div class="col-md-4">
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center divDropzone"></div><br>
                  </div>

                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-top: 4%;">
                      <div class="form-group">
                        <label>Favorito</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para la sección de Favoritos en el Punto de Venta > POS">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <select id="cbo-favorito" name="Nu_Estado" class="form-control required"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-top: 4%;">
                      <label>Estado</label>
                      <div class="form-group">
                        <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div>
                </div><!-- col -->
              </div><!-- row -->
      	  		
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <button type="button" id="btn-mostrar_campos_adicionales" class="btn btn-default btn-block" data-mostrar_campos_adicionales="0">Mostrar campos adicionales</button>
                  </div>
                </div>
              </div>

      			  <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" <?php echo $sCssDisplayViewHideFarmacia; ?>>
                  <label>Lote vencimiento</label>
                  <div class="form-group">
      		  				<select id="cbo-lote_vencimiento" name="Nu_Lote_Vencimiento" class="form-control"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
      			  <div class="row" <?php echo $sCssDisplayViewHideEcommerceMarketplace; ?>>
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label>Precio Online Regular</label>
                    <input type="text" name="Ss_Precio_Ecommerce_Online_Regular" class="form-control input-decimal" maxlength="10" autocomplete="off" placeholder="Precio">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label>Precio Online <span class="label-advertencia">*</span></label>
                    <input type="text" name="Ss_Precio_Ecommerce_Online" class="form-control required input-decimal" maxlength="10" autocomplete="off" placeholder="Precio">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                  <div class="form-group">
                    <label>Categoría Ecommerce <span class="label-advertencia">*</span></label>
      		  				<select id="cbo-categoria_marketplace" name="ID_Familia_Marketplace" class="form-control required select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                  <div class="form-group">
                    <label>Sub Categoría Ecommerce</label>
                    <select id="cbo-sub_categoria_marketplace" name="ID_Sub_Familia_Marketplace" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label>Marca Ecommerce</label>
                    <select id="cbo-marca_marketplace" name="ID_Marca_Marketplace" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2 div-campos_adicionales" <?php echo $sCssDisplayViewHideGeneral; ?>><!-- div-Producto -->
                  <div class="form-group">
                    <label>Stock Mínimo</label>
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear alertas cuando el stock está por vencer">
                      <i class="fa fa-info-circle"></i>
                    </span>
      		  				<input type="text" id="tel-Nu_Stock_Minimo" inputmode="numeric" class="form-control input-number" maxlength="6" value="" placeholder="opcional" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2 div-campos_adicionales" <?php echo $sCssDisplayViewHideGeneral; ?>><!-- div-Producto -->
                  <div class="form-group">
                    <label>Stock Máximo</label>
      		  				<input type="text" id="tel-Nu_Stock_Maximo" inputmode="numeric" class="form-control input-number" maxlength="6" value="" placeholder="opcional" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" <?php echo $sCssDisplayViewHideFarmacia; ?>>
                  <div class="form-group">
                    <label>Receta médica</label>
      		  				<select id="cbo-receta_medica" name="Nu_Receta_Medica" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2" <?php echo $sCssDisplayViewHideFarmacia; ?>>
                  <div class="form-group">
                    <label>Laboratorio</label>
                    <select id="cbo-laboratorio" name="ID_Laboratorio" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>  

                <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" <?php echo $sCssDisplayViewHideFarmacia; ?>>
                  <div class="form-group">
                    <label>Composición</label>
                    <select id="cbo-composicion" name="Txt_Composicion" class="form-control select2" multiple="multiple" style="width: 100%;" placeholder="- Seleccionar -"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2" <?php echo $sCssDisplayViewHideTiendaGranel; ?>>
                  <div class="form-group">
                    <label>CO2</label>
                    <input type="tel" id="tel-Qt_CO2_Producto" name="Qt_CO2_Producto" class="form-control input-decimal" placeholder="Ingresar cantidad" value="" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" <?php echo $sCssDisplayViewHideLavanderia; ?>>
                  <div class="form-group">
                    <label>Ubicación de Planta<span class="label-advertencia">*</span></label>
                    <select id="cbo-tipo_pedido_lavado" name="ID_Tipo_Pedido_Lavado" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-4 col-sm-3 col-md-2 col-lg-1 div-campos_adicionales"><!-- div-Producto -->
                  <div class="form-group">
                    <label>Icbper</label>
                    <select id="cbo-impuesto_icbper" name="ID_Impuesto_Icbper" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-3 col-md-5 col-lg-6 div-campos_adicionales"><!-- div-Producto -->
                  <div class="form-group">
                    <label>Ubicación <span class="hidden-sm">producto</span></label>
                    <input type="text" id="txt-Txt_Ubicacion_Producto_Tienda" class="form-control" value="" autocomplete="off" placeholder="Opcional">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
      			  <div class="row div-campos_adicionales">
      			    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="Txt_Producto" rows="1" class="form-control"  placeholder="opcional"></textarea>
                  </div>
                </div>
              </div>
              
      			  <div class="row div-campos_adicionales">
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Variante 1</label>
                  <a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-variante_1" name="ID_Variante_Item_1" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Valor 1</label>
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">
                    <i class="fa fa-info-circle"></i>
                  </span><a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-valor_1" name="ID_Variante_Item_Detalle_1" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Variante 2</label>
                  <a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-variante_2" name="ID_Variante_Item_2" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Valor 2</label>
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">
                    <i class="fa fa-info-circle"></i>
                  </span><a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-valor_2" name="ID_Variante_Item_Detalle_2" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Variante 3</label>
                  <a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-variante_3" name="ID_Variante_Item_3" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                  <label>Valor 3</label>
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">
                    <i class="fa fa-info-circle"></i>
                  </span><a class="btn btn-link" target="_blank" style="padding: 0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/VarianteController/listar'; ?>">[Crear]</a>
                  <div class="form-group">                    
                    <select id="cbo-valor_3" name="ID_Variante_Item_Detalle_3" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>

      			  <div class="row div-campos_adicionales">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div-Producto" <?php echo $sCssDisplayViewHideGeneral; ?>>
                  <div class="form-group">
                    <label>¿Enlazar ítems?</label>
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear combos, factor de conversión, crear insumos para platos, etc.">
                      <i class="fa fa-info-circle"></i>
                    </span>
      		  				<select id="cbo-Compuesto" name="Nu_Compuesto" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
      			  <div class="row div-Compuesto">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title"><i class="fa fa-tag"></i> Enlaces de Item</h3>
                    </div>
                    <div class="box-body">
                      <div class="col-xs-12 col-sm-9 col-md-6 col-lg-6">
                        <label>Producto</label>
                        <div class="form-group">
                          <input type="hidden" id="txt-AID" name="AID" class="form-control">
                          <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                          <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/globalAutocomplete" data-global-table="producto" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <label>Cantidad</label>
                        <div class="form-group">
                          <input type="tel" id="txt-Qt_Producto_Descargar" inputmode="decimal" name="Qt_Producto_Descargar" class="form-control input-decimal" placeholder="Ingresar cantidad" value="1" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                        <label class="hidden-xs">&nbsp;</label>
                        <div class="form-group">
                          <button type="button" id="btn-addProductosEnlaces" class="btn btn-success btn-block"> Agregar</button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="table-responsive div-Compuesto">
                      <table id="table-Producto_Enlace" class="table table-striped table-bordered">
                        <thead>
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-left">Código Barra</th>
                            <th class="text-left">Nombre</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-center"></th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div><!-- ./Compuesto -->
              
      			  <div class="row">
                <div class="col-md-12 col-lg-12">
                  <div class="form-group">
                    <label class="" style="cursor:pointer;">
                      <div class="icheckbox_flat-green">
                        <input type="checkbox" id="checkbox-precios_x_mayor" name="Nu_Activar_Precio_x_Mayor" class="flat-red">
                      </div>
                      Activar precio x mayor
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar esta opción puedes colocar precios de acuerdo a la cantidad">
                        <i class="fa fa-info-circle"></i>
                      </span>
                    </label>
                  </div>
                </div>
              </div>

      			  <div class="row div-precios_x_mayor">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title"><i class="fa fa-money"></i> Precios al por Mayor</h3>
                    </div>
                    <div class="box-body">
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <label>Cantidad</label>
                        <div class="form-group">
                          <input type="text" id="txt-Qt_Producto_x_Mayor" inputmode="decimal" name="Qt_Producto_x_Mayor" class="form-control input-decimal" placeholder="Ingresar cantidad" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <label>Precio</label>
                        <div class="form-group">
                          <input type="text" id="txt-Ss_Precio_x_Mayor" inputmode="decimal" name="Ss_Precio_x_Mayor" class="form-control input-decimal" placeholder="Ingresar precio" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                        <label class="hidden-xs">&nbsp;</label>
                        <div class="form-group">
                          <button type="button" id="btn-addProductoPrecioxMayor" class="btn btn-success btn-block">Agregar</button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="table-responsive">
                      <table id="table-precios_x_mayor" class="table table-striped table-bordered">
                        <thead>
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-right">Cantidad Desde</th>
                            <th class="text-right">Precio</th>
                            <th class="text-center">Eliminar</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div><!-- ./precios_x_mayor -->

    	        <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="table-responsive">
                    <table id="table-lista_variantes" class="table table-striped table-bordered">
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
      			    <br/>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
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
    
    <!-- modal ver imagen del item -->
    <div class="modal fade modal-ver_item" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body" id="modal-body-ver_item">
              <div class="col-xs-12 text-center">
							  <img class="img-responsive" style="
  display: block;
  margin-left: auto;
  margin-right: auto;" src="">
							</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal imagen del item -->

    
    <!-- modal informacion del item -->
    <div class="modal fade modal-info_item" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body" id="modal-body-info_item">
            <h4 class="text-center" id="modal-header-info_item-title"></h4>
            <div class="col-xs-12 text-center">
              <img class="img-responsive" style="
  display: block;
  margin-left: auto;
  margin-right: auto;" src="">
							</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal informacion del item -->

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal Impresion de Codigo de barra -->
<div class="modal fade modal-default" id="modal-print_codigo_barra">
  <div class="modal-dialog">
    <div class="modal-header" style="background-color: #fff;">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 id="modal-header-print_codigo_barra" class="text-center">Impresion de Codigo de Barra</h4>
    </div>
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label>Formato</label>
            <div class="form-group">
    				  <select id="cbo-modal-print_codigo_barra-formato" class="form-control">
    				    <!--<option value="0">- Seleccionar -</option>-->
    				    <option value="1">HTML</option>
    				    <!--<option value="2">PDF</option>-->
    				  </select>
    				</div>
          </div>

          <div class="col-xs-6 col-sm-4">
            <label>Imprimir Sku</label>
            <div class="form-group">
    				  <select id="txt-modal-print_codigo_barra-imprimir_sku" class="form-control">
    				    <option value="1">Si</option>
    				    <option value="2">No</option>
    				  </select>
    				</div>
          </div>

          <div class="col-xs-6 col-sm-4">
            <label>Columna</label>
            <div class="form-group">
              <input type="text" inputmode="number" id="txt-modal-print_codigo_barra-columna" class="form-control required input-number" maxlength="2" autocomplete="off" value="1">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-6">
          <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal"><span class="fa fa-close"></span> Cancelar</button>
        </div>
        <div class="col-xs-6">
          <button type="button" id="btn-modal-print_codigo_barra" class="btn btn-success btn-lg btn-block pull-center"><i class="fa fa-print"></i> Imprimir</button>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>