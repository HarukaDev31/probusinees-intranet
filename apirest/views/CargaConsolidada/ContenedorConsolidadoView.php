<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <span id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
          </h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
   <section class="content" id="main-container">
      <div class="container-fluid">
      <div class="row">
        <div class="col-6 col-sm-2">
                    <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                    <div class="form-group">
                      <input type="text" id="txt-Fe_Inicio_Carga" class="form-control  input-date input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                      <span class="help-block text-danger" id="error"></span>
                    </div>
                  </div>
                  <div class="col-6 col-sm-2">
                    <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                    <div class="form-group">
                      <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                      <span class="help-block text-danger" id="error"></span>
                    </div>
                  </div>
                  <div class="col-6 col-sm-3">
                    <label>Estado</label>
                    <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                      <option value="0" selected>Todos</option>
                      <option value="PENDIENTE">PENDIENTE</option>
                      <option value="RECIBIENDO">RECIBIENDO</option>
                      <option value="COMPLETADO">COMPLETADO</option>
                    </select>
                  </div>
                  <div class="col-6 col-sm-2">
                    <label>&nbsp;</label>
                    <button type="button" id="btn-crear" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear</button>
                  </div>
                  <div class="col-6 col-sm-2">
                    <label>&nbsp;</label>
                    <button type="button" id="btn-buscar-carga" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                  </div>
        </div>
        <div class="table-responsive div-Listar">
          <table id="table-contenedor" class="table table-bordered table-hover table-striped">
            <thead class="thead-light">
              <tr>
                <th>Mes  </th>
                <th>Pais</th>
                <th>Carga</th>
                <th>F. Puerto</th>
                <th>F. Entrega</th>
                <th>Empresa</th>
                <th>Ver</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
   </section>
    <section class="content" id="cotizacion-container">
      <div class="container-fluid ">
        <div class="row mb-2">
                <div class="col-6 col-sm-6">
                </div>
                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-crear-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear</button>
                </div>
                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-buscar-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-6 col-sm-1">
                 <label>&nbsp;</label>
                  <button type="button" id="btn-back-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i> Atras</button>
                </div>
        </div>
      <div class="table-responsive div-Listar">
        <table id="table-cotizacion" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Fecha</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Volumen</th>
              <th>Cotizacion</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      
      </div>
    </div>
    </section>
    <!--Steps-->
    <section id="steps" class="content">
      <div id="steps-container">
      </div>
      <div class="steps-buttons">
      </div>
    </section>
  <!--modal create cotizacion-->
  <div class="modal fade" id="modal-crear-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modal-cotizacion" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear Cotizacion</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-crear-cotizacion" class="form-horizontal" method="post">
            <div class="row">
              <div class="col-6 col-sm-6">
                <label>Fecha <span class="label-advertencia text-danger"> *</span></label>
                <div class="form-group">
                  <input type="text" id="txt-Fe_Cotizacion" name="fecha" class="form-control input-report required input-date" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group ">
                  <label>Nombre <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-Nombre" name="nombre" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group">
                  <label>DNI/RUC <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-Dni" name="documento" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                ">
                  <label>Correo <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-Correo" name="correo" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                ">
                  <label>Whatsapp <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-Whatsapp" name="telefono" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group" >
                  <label>Tipo Cliente <span class="label-advertencia text-danger"> *</span></label>
                  <select id="txt-ID_Tipo_Cliente" name="id_tipo_cliente" class="form-control input-report required">
                  </select>
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                ">
                  <label>Volumen <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-Volumen" name="volumen" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                ">
                  <label>Cotizacion <span class="label-advertencia text-danger"> *</span></label>
                  <input type="file" id="txt-Cotizacion" name="cotizacion" class="form-control input-report required">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>  
            </div>
          </form>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" id="btn-actualizar-cotizacion" class="btn btn-primary">Actualizar</button>
            <button type="button" id="btn-guardar-cotizacion" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-crear" tabindex="-1" role="dialog" aria-labelledby="modal-crear" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear Carga Consolidada</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-crear" class="form-horizontal" method="post">
            <div class="row">
              <div class="col-6 col-sm-6">
                <label>Mes <span class="label-advertencia text-danger"> *</span></label>
                <div class="form-group">
                  <select id="txt-Mes" name="mes" class="form-control input-report required">
                  </select>
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group">
                  <label>Pais <span class="label-advertencia text-danger"> *</span></label>
                  <select id="txt-ID_Pais" name="id_pais" class="form-control input-report required">
                    
                  </select>
                  <!-- <span class="help-block text-danger" id="error"></span>
                  <input type="hidden" id="txt-ID_Carga_Consolidada" name="id" value="0"> -->
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group">
                  <label>Carga <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" id="txt-No_Carga" name="carga" class="form-control input-report required">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                ">
                  <label>F. Puerto <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="f_puerto" id="txt-Fe_Puerto" class="form-control input-report required input-date" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group" id="div-Fe_Entrega">
                  <label>F. Entrega <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text"  name="f_entrega" id="txt-Fe_Entrega" class="form-control input-report required input-date" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                  <span class="help-block
                  text-danger" id="error"></span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group ">
                  <label>Empresa <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="empresa" id="txt-Empresa" class="form-control input-report required">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              </div>
            </div>
          </form>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" id="btn-actualizar" class="btn btn-primary">Actualizar</button>
            <button type="button" id="btn-guardar" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
 
<style scoped>
#steps{
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1em;
  margin-top: 1em;
  flex-direction: column;
}
#steps-container{
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1em;
  margin-top: 1em;
}
.step-container {
      display: flex;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 1em;
      align-items: center;
      margin-bottom: 10px;
      width: 200px;
      padding: 1em 2em;
      animation: fadeIn 1s forwards;
      min-width: 200px;
      /* Inicialmente ocultar los elementos */
      opacity: 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    /* Aplicar un retraso basado en el índice del hijo */
    .step-container:nth-child(1) {
      animation-delay: 0s;
    }

    .step-container:nth-child(2) {
      animation-delay: 0.2s;
    }

    .step-container:nth-child(3) {
      animation-delay: 0.4s;
    }

    .step-container:nth-child(4) {
      animation-delay: 0.6s;
    }

    .step-container:nth-child(4) {
      animation-delay: 0.8s;
    }

    .step-container:nth-child(5) {
      animation-delay: 1s;
    }

    .step-container:nth-child(6) {
      animation-delay: 1.2s;
    }
  .step-container-completed {
      display: flex;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 1em;
      align-items: center;
      margin-bottom: 10px;
      width: 200px;
      padding: 1em 2em;
      animation: fadeInBounce 1s forwards;
      min-width: 200px;
      /* Inicialmente ocultar los elementos */
      opacity: 0;
      background-color: #85C1E9;
      color: white;
    }.step-container-progress {
      display: flex;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 1em;
      align-items: center;
      margin-bottom: 10px;
      width: 200px;
      padding: 1em 2em;
      animation: fadeInBounce 1s forwards;
      min-width: 200px;
      /* Inicialmente ocultar los elementos */
      opacity: 0;
      background-color: #f4d03f;
      text-align: center;
      color: white;
    }

    @keyframes fadeInBounce {
      from {
        opacity: 0;
        transform: scale(0.5);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }



    .step-container-completed span {

      text-align: center;
    }

    .step-container span {
      text-align: center;
    }

    .step-container:hover {
      background-color: #f9f9f9;
    }



</style>