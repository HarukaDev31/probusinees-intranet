<link rel="stylesheet" href="https://pqina.nl/filepond/static/assets/filepond.css?1660222202" type="text/css" />
<style>
  .predeterminado{
      border: solid 3px green;
  }

  h1 {
      color:Green;
  }
  div.scroll {
      margin:4px, 4px;
      padding:4px;
      //background-color: green;
      width: 100%;
      height: 150px;
      overflow-x: hidden;
      overflow-y: auto;
      text-align:justify;
  }
</style>
<script type="text/template" id="template-container">
  <div class="dz-preview dz-processing dz-image-preview dz-success dz-complete qqqqqqqqqq"/>
  <div class="dz-image">
  <img data-dz-thumbnail />    
  </div>
  <div class="dz-details">
    <div class="dz-size">
      <span data-dz-size="">
        <strong>23.7</strong> KB </span>
    </div>
    <div class="dz-filename">
  <span data-dz-name></span>
    </div>
  </div>
  <div class="dz-progress">
    <span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;"></span>
  </div>
  <div class="dz-error-message">
    <span data-dz-errormessage=""></span>
  </div>
  <div class="dz-success-mark">
    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <title>Check</title>
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF"></path>
      </g>
    </svg>
  </div>
  <div class="dz-error-mark">
    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <title>Error</title>
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">
          <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"></path>
        </g>
      </g>
    </svg>
  </div>
<!--
    <button class="filepond--file-action-button filepond--action-remove-item remove" type="button" data-align="left bottom" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1); opacity: 1;position: absolute;top: 87px;left: 6px;cursor: pointer;">
    <i class="fa fa-fw fa-remove"></i>
  </button>
-->
<!--
  <button class="filepond--file-action-button filepond--action-remove-item default" type="button" data-align="left bottom" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1); opacity: 1;position: absolute;top: 87px;left: 90px;cursor: pointer;">
    <i class="fa fa-fw fa-check-square-o"></i>
  </button>
-->
</div>
</script>
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
            <!--
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
-->
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

              <?php
              if ($this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503){ ?>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div-configurar_delivery_estandar hidden">
                <div class="form-group">
                  <label>Empresa <span class="label-advertencia">*</span></label>
                  <select id="cbo-Empresas" name="ID_Empresa_Item" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                <input type="text" id="cbo-Empresas" name="ID_Empresa_Item" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              </div>
              <?php } ?>

              <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
                <label>Proveedores</label>
                <div class="form-group">
                  <select id="cbo-filtro-proveedores" name="ID_Filtro_Proveedor" class="form-control required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
                <label>Almecenes</label>
                <div class="form-group">
                  <select id="cbo-filtro-almacenes" name="ID_Filtro_Almacen" class="form-control required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-2 col-lg-2 hidden">
                <label>Producto</label>
                <div class="form-group">
    		  				<select id="cbo-Filtros_Productos" name="Filtros_Productos" class="form-control">
    		  				  <option value="Producto">Nombre</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
                <label>Buscar producto</label>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="250" placeholder="" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 hidden">
                <div class="form-group">
                  <select id="cbo-filtro-estado_producto" name="Nu_Estado" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">- Estados Todos -</option>
    		  				  <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

            </div>
          </div>
          <!-- ./box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Producto" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort">Importar</th>
                  <!--<th class="no-sort">Proveedor</th>
                  <th class="no-sort">Almacén</th>-->
                  <!--<th class="no-sort">Dirección</th>-->
                  <th class="no-sort img_sort">Imagen</th>
                  <th class="no-sort">Codigo</th>
                  <th class="no_sort_left">Producto</th>
                  <th class="no_sort_right">Stock</th>
                  <th class="no_sort_right" title="Precio Proveedor">Precio Proveedor</th>
                  <th class="no_sort_right" title="Precio Venta Sugerido">Precio Sugerido</th>
                  <th class="no-sort">Link</th>
                  <th class="no-sort">Ver</th>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar" style="padding:0px">
            <?php
            $attributes = array('id' => 'form-Producto');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
          	  <input type="hidden" id="txt-EID_Producto" name="EID_Producto" class="form-control">
          	  <input type="hidden" id="txt-ENu_Codigo_Barra" name="ENu_Codigo_Barra" class="form-control">
          	  <input type="hidden" id="txt-ENo_Codigo_Interno" name="ENo_Codigo_Interno" class="form-control">
          	  <input type="hidden" id="hidden-nombre_imagen" name="No_Imagen_Item" class="form-control">
    	  
              <div class="row">
                <div class="col-md-12 col-lg-12">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left dropzone" style="border: 0px !important;">
                    <label for="">Galería de Imágenes</label>
                    <div class="scroll" id="dropzone-previews"></div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center hidden">
                    <div id="id-divDropzone" style="border: 2px dashed #bfbfbf !important; padding: 1rem 1rem; margin-top: 1rem; cursor: pointer; margin-bottom: 1.5rem;">
                      <div class="dz-message">Subir imágene(s)</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 col-lg-12">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                      <label>Url Video</label>
                      <style> .tooltip-inner { min-width: 150px; /* the minimum width */ } </style>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-html="true" data-placement="bottom" title="Puede colocar una url de video de Youtube o de TikTok, por ejemplo:<br>https://youtu.be/xxxxxxxxxxx<br>https://www.youtube.com/watch?v=xxxxxxxxxx<br>https://www.tiktok.com/embed/xxxxxxxxxxxxxxxxxxx">
                        <i class="fa fa-info-circle"></i>
                      </span>
                      <input name="Txt_Url_Video_Lae_Shop" class="form-control"  placeholder="Opcional" />
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 hidden">
                    <div class="form-group">
                      <label>Grupo <span class="label-advertencia">*</span></label>
                      <select id="cbo-TiposItem" name="Nu_Tipo_Producto" class="form-control required" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 hidden"><!-- div-Producto -->
                    <div class="form-group">
                      <label>Tipo Producto <span class="label-advertencia">*</span></label>
                      <select id="cbo-TiposExistenciaProducto" name="ID_Tipo_Producto" class="form-control" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                                    
                  <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 hidden">
                    <div class="form-group">
                      <label>Código<span class="label-advertencia">*</span></label>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si no tienen un código de barra, crear un código interno (Pueden ingresar números, letras o ambos. Cantidad de 1 hasta 16 dígitos)">
                        <i class="fa fa-info-circle"></i>
                      </span>
                      <input type="text" id="txt-Nu_Codigo_Barra" name="Nu_Codigo_Barra" class="form-control input-codigo_barra input-Mayuscula" placeholder="Ingresar código" maxlength="20" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 hidden">
                    <div class="form-group">
                      <label>Impuesto<span class="label-advertencia">*</span></label>
                      <select id="cbo-Impuestos" name="ID_Impuesto" class="form-control required" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                      <label>Nombre <span class="label-advertencia">*</span></label>
                      <textarea name="No_Producto" class="form-control required" rows="1" placeholder="Ingresar nombre" maxlength="250" style="height: 34px;"></textarea>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label>Categoría <span class="label-advertencia">*</span></label>
                    <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . '/TiendaVirtual/CategoriasTiendaVirtualController/listar'; ?>">[Crear]</a>
                    <div class="form-group">                    
                      <select id="cbo-categoria" name="ID_Familia" class="form-control select2" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                      <label>Precio Proveedor</label>
                      <input type="text" name="Ss_Precio_Proveedor_Dropshipping" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off" value="" placeholder="Obligatorio" disabled>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                      <label>Precio Sugerido</label>
                      <input type="text" name="Ss_Precio_Vendedor_Dropshipping" inputmode="decimal" class="form-control input-decimal" maxlength="13" autocomplete="off" value="" placeholder="Opcional" disabled>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                      <label>Precio de tu Tienda</label>
                      <input type="text" name="Ss_Precio_Ecommerce_Online_Regular" inputmode="decimal" class="form-control input-decimal" maxlength="13" autocomplete="off" value="" placeholder="Opcional">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label>Descripción</label>
                    <div class="form-group">
                      <!--<textarea name="Txt_Producto" rows="5" class="form-control"  placeholder="opcional"></textarea>-->
                      <div id="textarea-descripcion_item"></div>
                    </div>
                  </div>
                
                  <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 hidden">
                    <label>SubCate.</label>
                    <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'TiendaVirtual/SubCategoriasTiendaVirtualController/listar'; ?>">[Crear]</a>
                    <div class="form-group">                    
                      <select id="cbo-sub_categoria" name="ID_Sub_Familia" class="form-control select2" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 hidden">
                    <label>Unidad <span class="label-advertencia">*</span></label>
                    <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/UnidadMedidaController/listarUnidadesMedida'; ?>">[Crear]</a>
                    <div class="form-group">
                      <select id="cbo-UnidadesMedida" name="ID_Unidad_Medida" class="form-control select2" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 hidden">
                    <label>Marca</label>
                    <a class="btn btn-link" target="_blank" style="padding:0px; color: #7b7b7b" href="<?php echo base_url() . 'TiendaVirtual/MarcasTiendaVirtualController/listar'; ?>">[Crear]</a>
                    <div class="form-group">
                      <select id="cbo-Marcas" name="ID_Marca" class="form-control select2" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                 <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12 hidden">
                    <div class="form-group">
                      <label>Estado de Tienda</label>
                      <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                </div>
                
                <!--
                <div class="col-md-3 col-lg-3" style="padding: 0px;">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                    <div class="well well-sm">
                      <i class="fa fa-warning"></i> Indicaciones:
                      <br>- Imagen: <b>opcional</b>
                      <br>- Formato: <b>.png | .jpg | .jpeg</b>
                      <br>- Fondo de imágen(es): <b>SIN FONDO / BLANCO / TRANSPARENTE</b>
                      <br>- Tamaños: <br>
                        &nbsp;&nbsp;* Pequeño: <b>Ancho: 199px</b> y <b>Alto: 199px</b><br>
                        &nbsp;&nbsp;* Mediano: <b>Ancho: 340px</b> y <b>Alto: 340px</b>
                      <br>- Peso Máximo: <b>400 KB</b>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center dropzone" style="border: 0px !important;">
                    <div class="scroll" id="dropzone-previews" style="text-align: center;"></div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                      <label>Url Video</label>
                      <style> .tooltip-inner { min-width: 150px; /* the minimum width */ } </style>
                      <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-html="true" data-placement="bottom" title="Puede colocar una url de video de Youtube o de TikTok, por ejemplo:<br>https://youtu.be/xxxxxxxxxxx<br>https://www.youtube.com/watch?v=xxxxxxxxxx<br>https://www.tiktok.com/embed/xxxxxxxxxxxxxxxxxxx">
                        <i class="fa fa-info-circle"></i>
                      </span>
                      <input name="Txt_Url_Video_Lae_Shop" class="form-control"  placeholder="Opcional" />
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center hidden">
                    <div id="id-divDropzone" style="border: 2px dashed #bfbfbf !important; padding: 1rem 1rem; margin-top: 1rem; cursor: pointer; margin-bottom: 1.5rem;">
                      <div class="dz-message">Subir imágene(s)</div>
                    </div>
                  </div>
                </div>

                    -->
              </div>
              
      			  <div class="row hidden">
              </div>

      			  <div class="row hidden">
                <div class="col-md-12 col-lg-12">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
              </div>
              
      			  <div class="row div-precios_x_mayor hidden">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title"><i class="fa fa-money"></i> Precios al por Mayor</h3>
                    </div>
                    <div class="box-body">
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <label>Cantidad</label>
                        <div class="form-group">
                          <input type="text" id="txt-Qt_Producto_x_Mayor" inputmode="decimal" name="Qt_Producto_x_Mayor" class="form-control input-decimal" placeholder="Ingresar cantidad" value="2" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <label>Precio</label>
                        <div class="form-group">
                          <input type="text" id="txt-Ss_Precio_x_Mayor" inputmode="decimal" name="Ss_Precio_x_Mayor" class="form-control input-decimal" placeholder="Ingresar precio" value="0" autocomplete="off">
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
      			    <br/>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Salir</button>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save_producto_proveedor" class="btn btn-success btn-lg btn-block">Importar Producto</button>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 hidden">
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
    
    <!-- modal informacion del item -->
    <div class="modal fade modal-info_item" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center" id="modal-header-info_item-title"></h4>
          </div>
          <div class="modal-body" id="modal-body-info_item">
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

<!-- Importar Productos Laehop -->
<div class="modal fade modal_importar_producto-laeshop" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Dropshipping/ProductosProveedoresDropshippingController/importarExcelProductos" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Productos</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                <br>&nbsp;
                <a id="a-download-product" href="<?php echo base_url(); ?>DownloadController/download/LaeDropshipping_Plantilla_Productos.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector">
                    <input type="file" id="my-file-selector" name="excel-archivo_producto" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">Buscar...
                  </label>
                  <span class='label label-info' id="upload-file-info"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancel-product" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_producto" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar productos -->

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->