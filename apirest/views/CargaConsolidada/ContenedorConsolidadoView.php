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
                      <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required" >
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
                  <?php if($this->user->No_Grupo=="Coordinación"){  ?>
                  <div class="col-6 col-sm-2">
                    <label>&nbsp;</label>
                    <button type="button" id="btn-crear" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear</button>
                  </div>
                  <?php } ?>
                  
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
                <th>F. Cierre</th>
                <th>F. Arribo</th>
                <th>F. Entrega</th>
                <th>Empresa</th>
                <th>Ver</th>
                <th>Estado</th>
                <?php if($this->user->No_Grupo=="Coordinación"){  ?>
                <th>Acciones                 
                </th>
              <?php } ?>
              </tr>
            </thead>
          </table>
        </div>
      </div>
   </section>
    <section class="content" id="cotizacion-container">
      <div class="container-fluid ">
        <div class="row mb-2">

                <div class="col-12 col-md-4">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-crear-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear Prospecto</button>
                </div>
                <div class="col-12 col-md-4">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-buscar-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-12 col-md-3">
                </div>
                <div class="col-12 col-md-1">
                 <label>&nbsp;</label>
                  <button type="button" class="btn btn-outline-primary btn-block btn-reporte btn-back-cotizacion" data-type="html"><i class="fa fa-arrow-left"></i> </button>
                </div>
        </div>
        <div class="table-responsive">
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
                <?php if($this->user->No_Grupo=="Coordinación"){  ?>

                <th>Acciones
                </th>
                <?php } ?>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </section>
    <section class="content" id="clientes-container">
        <div class="row mb-2">
                <!--select estado  with TODOS PENDIENTE,COTIZADO,PAGADO Y ENTREGADO OPTIOONS-->
                <div class="col-12 col-md-4">
                  <label>Estado</label>
                  <select id="txt-ID_Estado_Cliente" name="ID_Estado" class="form-control input-estado">
                    <option value="0" selected>Todos</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="COTIZADO">COTIZADO</option>
                    <option value="PAGADO">PAGADO</option>
                    <option value="ENTREGADO">ENTREGADO</option>
                  </select>
                </div>
                
                <div class="col-12 col-md-4">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-buscar-clientes-general" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-12 col-md-3">
                </div>
                <div class="col-12 col-md-1">
                 <label>&nbsp;</label>
                  <button type="button"  class="btn-back-cotizacion btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i> </button>
                </div>
        </div>
      <div class="table-responsive" class="table table-bordered table-hover table-striped">
        <table id="table-clientes-general" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Volumen</th>
              <th>Ver</th>
              <th>Estados</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>               
        <table id="table-clientes-variacion" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>T. Cliente</th>
              <th>Vol. Cot</th>
              <th>Vol. China</th>
              <th>Vol. Doc</th>
              <th>Valor Cot</th>
              <th>Valor Doc</th>
            </tr>
          </thead>
        </table>             
      </div>
    </section>
    <section class="content card px-4 py-3" id="clientes-documentation-container">

      <div class="col col-12" id="clientes-documentacion">
        <h3
        class="d-flex w-100 row documentation-title"
        >
        <span
        
        data-toggle="collapse"
        href="#collapse-documentacion"
        role="button"
        aria-expanded="false"
        aria-controls="collapse-documentacion"
        class="col-9 "
        >
          Documentación
        </span>
        <div
        class="col-3 d-flex justify-content-end"
        >
        <button type="button" id="btn-crear-documentacion" class="btn btn-outline-primary" data-type="html"><i class="fa fa-upload"></i>Nuevo documento</button>
        <button type="button" id="btn-back-cliente-documentacion" class="btn btn-outline-primary" data-type="html"><i class="fa fa-arrow-left"></i></button>        
        </div>              
        </h3>
        <div class="collapse show row row-cols-1" id="collapse-documentacion">
          <form id="form-documentacion" class="form-horizontal  mb-2 row" >
            <div class="col-6">
              <label>Vol. Doc</label>
              <input type="text" id="txt-Vol_Doc"
              name="volumen_doc"
              class="form-control input-report required">
              <span class="invalid-feedback" id="error-vol-doc">El volumen es requerido</span>
            </div>
            <div class="col-6">
            <label class="form-label">Valor Doc</label>  
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">$</span>
                </div>
                <input type="number" 
                name="valor_doc"
                id="txt-Valor_Doc" class="form-control input-report required">
                <span class="invalid-feedback" id="error-valor-doc">El valor es requerido</span>
              </div>
            </div>
            <div class="col-3">
              <label>F. Comercial</label>
              <div id="factura-comercial">
              </div>
            </div>
            <div class="col-12 col-guardar-documentacion m-2 d-flex justify-content-center align-items-center">
              <div id="btn-guardar-documentacion" 
              
              class="btn btn-primary btn-block btn-reporte col-6" data-type="html"><i class="fa fa-save"></i> Guardar</div>
            </div>
          </form>
        
        </div>
      </div>
      <div class="col col-12" id="clientes-cotizacion">
        <h3
        data-toggle="collapse"
        href="#collapse-cotizacion"
        role="button"
        aria-expanded="false"
        aria-controls="collapse-cotizacion"
        class="documentation-title"

        >Cotizaciones</h3>
        <div class="collapse show row row-cols-4 " id="collapse-cotizacion">
          <div class="col">
            <a id="btn-descargar-cotizacion-inicial" 
            class="btn btn-outline-success btn-block " target="_blank" href="#"><i class="fa fa-download"></i> Descargar Cotización Inicial</a>
          </div>
          <div class="col">
            <button type="button" id="btn-descargar-cotizacion-final" class="btn btn-outline-success btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i> Descargar Cotización Final</button>
          </div>
        </div>
      </div>          
    </section>

    <section id="steps" class="content">
      <div id="steps-container">
      </div>
      <div class="steps-buttons">
      </div>
    </section>
    <section class="content card px-4 py-3" id="documentation-container">
      <div class="row">
            <div class="col-12 col-md-2 ">
              <button type="button" id="btn-documentacion-factura" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i>Factura General</button>
            </div>
            <div class="col-12 col-md-2">
              <button type="button" id="btn-documentacion-zip" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i>Zip</button>
            </div>
            <div class="col-12 col-md-2">
               <button type="button" id="btn-documentacion-new" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-upload"></i> Nuevo documento</button>
            </div>
            <div class="col-12 col-md-4">
            </div>
            <div class="col-12 col-md-2">
                  <button type="button"  class="btn btn-outline-primary btn-block btn-reporte btn-back-documentacion" data-type="html"><i class="fa fa-arrow-left"></i> </button>
            </div>
      </div>
      <div class="row mb-2 row-cols-3 documentation-files-container mt-2">
      </div>
    </section>
    <div class="modal fade" id="modal-crear-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modal-cotizacion" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Subir Prospecto</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="form-crear-cotizacion" class="form-horizontal" method="post">
              <div class="row">
              
                <div class="col-12 col-sm-12">
                  <div class="form-group
                  ">
                    <label>Cotizacion <span class="label-advertencia text-danger"> *</span></label>
                    <input type="file" id="txt-Cotizacion" name="cotizacion" required class="form-control input-report required">
                    <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>

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
                    <select id="txt-Mes" required name="mes" class="form-control input-report required">
                    </select>
                    <span class="invalid-feedback" id="error-mes">El mes es requerido</span>
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group">
                    <label>Pais <span class="label-advertencia text-danger"> *</span></label>
                    <select id="txt-ID_Pais" required name="id_pais" class="form-control input-report required">
                      
                    </select>
                    <!-- <span class="help-block text-danger" id="error"></span>
                    <input type="hidden" id="txt-ID_Carga_Consolidada" name="id" value="0"> -->
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group">
                    <label>Carga <span class="label-advertencia text-danger"> *</span></label>
                    <input type="text" id="txt-No_Carga" required name="carga" class="form-control input-report required">
                    <span class="invalid-feedback" id="error-carga">La carga es requerida</span>
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group
                  ">
                    <label>F. Arribo <span class="label-advertencia text-danger"> *</span></label>
                    <input type="text" name="f_puerto" required id="txt-Fe_Puerto" class="form-control input-report required input-date" >
                    <span class="invalid-feedback" id="error-f-puerto">La fecha Arribo es requerida</span>
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group
                  ">
                    <label>F. Cierre <span class="label-advertencia text-danger"> *</span></label>
                    <input type="text" name="f_cierre" required id="txt-Fe_Cierre" class="form-control input-report required input-date" >
                    <span class="invalid-feedback" id="error-f-cierre">La fecha de Cierre es requerida</span>
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group" id="div-Fe_Entrega">
                    <label>F. Entrega <span class="label-advertencia text-danger"> *</span></label>
                    <input type="text"  required  name="f_entrega" id="txt-Fe_Entrega" class="form-control input-report required input-date" >
                    <span class="invalid-feedback" id="error-f-entrega">La fecha entrega es requerida</span>
                  </div>
                </div>
                <div class="col-6 col-sm-6">
                  <div class="form-group ">
                    <label>Empresa <span class="label-advertencia text-danger"> *</span></label>
                    <input type="text"  required name="empresa" id="txt-Empresa" class="form-control input-report required">
                    <span class="invalid-feedback" id="error-empresa">La empresa es requerida</span>
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
    .collapse.show {
    
    visibility: visible;
  }
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}.documentation-title{
  cursor: pointer;
  padding: 0 1em;
  height: 3em;
  background-color: #f9f9f9;
  border: 1px solid #ccc;
  border-radius: 0.2em;
  margin-bottom: 1em;
  display: flex;
  justify-content:center;
  align-items:center;
  text-align:center;
}.documentation-title:hover{
  background-color: #d5dbdb;
}.delete-folder-button:hover{
  cursor: pointer;
}

</style>