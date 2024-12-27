<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <!--button to create a new record-->
    
    <div class="container-fluid">
    <div class="row mb-3 d-flex justify-content-end" >
                
                <div class="col-6 col-sm-3 d-flex justify-content-end  align-items-end">
                  <button type="button" class="btn btn-outline-secondary" id="btnNuevo"  class="h-50"
                  data-toggle="modal" onclick="openModalNuevo()" data-target="#modalNuevo"
                  >+</button>
                </div>
                <div class="col-6 col-sm-3">
                  <label>Estado</label>
                  <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                    <option value="0" selected>Todos</option>
                    <option value="1">Pendiente</option>
                    <option value="2">Completado</option>
                  </select>
                </div>
                <div class="col-6 col-sm-3">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
</div>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive div-Listar">
                <table id="table-estadoContenedor" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>Servicio</th>
                      <th>N.Orden</th>
                      <th>COD. BL</th>
                      <th>CLIENTE</th>
                      <th>COD.SHIPPER</th>
                      <th>INLAND</th>
                      <th style="min-width:7em;max-width:8em">T.C</th>
                      <th>FLETE</th>
                      <th>TOTAL RMB</th>
                      <th>TOTAL USD</th>
                      <th>NAVIERA </th>
                      <th>FREE BOX</th>
                      <th>BL TELEX </th>
                      <th>PAGADO</th>
                      <th>CONTENEDOR</th>
                      <th>ESTADO</th>
                      <th>ACCIONES</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
       </div>
    </div>
    </section>
  <!--modal para confirmar el guardado del tipo de cambio-->
  <div class="modal fade" id="modalConfirmar" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalConfirmarLabel">Confirmar Guardado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ¿Está seguro de guardar el tipo de cambio?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" 
          
          data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarTC">Guardar</button>
        </div>
      </div>
    </div>
  

</div>
<div class="modal fade " id="modalNuevo" tabindex="-1" role="dialog" aria-labelledby="modalNuevoLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalNuevoLabel">Nuevo Estado Contenedor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="fclDetails" class="space-y-8">
                  <div class=" grid gap-4">
                      <div class="mb-3">
                      <label for="client" class="form-label">Cliente</label>
                      <input  type="text" id="client" name="client"  class="form-control">
                      <span class="error" id="error-client"></span>
                      </div>
                  </div>

                  

                  <div class="form-section grid gap-4 md:grid-cols-3">
                      <div class="grid gap-2"> 
                          <div>
                            <label for="inland" class="form-label">Inland </label>
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                <span class="input-group-text" >¥</span>
                              </div>
                            <input  type="number"  class="form-control"id="inland" name="inland" step="0.01" placeholder="0.00">
                            <span class="error" id="error-inland"></span>
                            </div>
                          </div>
                      </div>
                      <div class="grid gap-2"> 
                          <div>
                            <label for="flete" class="form-label">Flete ($)</label>
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                <span class="input-group-text" >$</span>
                              </div>
                            <input  type="number"  class="form-control" id="flete" name="flete" step="0.01" placeholder="0.00">
                            <span class="error" id="error-flete"></span>
                            </div>
                          </div>
                      </div>
                      <div class="grid gap-2"> 
                          <div class="mb-3">
                          <label for="naviera" class="w-100">Naviera</label>
                          <select id="naviera" name="naviera" class="form-control">
                              <option value="">Seleccionar naviera</option>
                           
                          </select>
                          <span class="error" id="error-naviera"></span>
                          </div>
                      </div>
                      <div class="grid gap-2">
                          <div class="mb-3">
                          <label for="contenedor" class="w-100">Contenedor</label>
                          <select id="contenedor" name="contenedor" class="form-control">
                              <option value="">Seleccionar contenedor</option>
                        
                          </select>
                          <span class="error" id="error-contenedor"></span>
                          </div>
                      </div>
                      <div class="grid gap-2">
                         <div class="mb-3">
                          <label for="diasTransito">Días Tránsito</label>
                          <input  type="text" id="diasTransito" name="diasTransito" min="0" class="form-control">
                          <span class="error" id="error-diasTransito"></span>
                        </div>
                      </div>
                      
                      <div class="grid gap-2">
                      <div class="mb-3">
                          <label for="boxFree">Box Free</label>
                          <input  type="text" id="boxFree" name="boxFree" min="0" class="form-control">
                          <span class="error" id="error-boxFree"></span>
                      </div>
                          </div>
                      <div class="grid gap-2">
                          <div class="mb-3">
                            <label for="codShipper" class="w-100" >Cod. Shipper
                            </label>
                            <div class="input-group mb-3">
                              <select id="codShipper" name="codShipper" class="form-control">
                                  <option value="">Seleccionar código</option class="form-control"> 
                              </select>
                              <div class="input-group-prepend">
                                <span class="input-group-text" data-toggle="modal" data-target="#newShipperDialog" id="basic-addon1">+</span>
                                <span class="input-group-text"  onclick="deleteShipper()" id="deleteShipperButton">-</span>
                                </div>
                             <span class="error" id="error-codShipper"></span>

                            </div>
                          </div>
                        </div>
                      <div class="grid gap-2">
                          <div class="mb-3">
                          <label for="norden">N. Orden</label>
                          <input  type="text" id="norden" name="norden" class="form-control">
                          <span class="error" id="error-norden"></span>
                          </div>
                      </div>
                      <div class="grid gap-2">
                        <div class="mb-3">
                          <label for="codbl">Cod. BL</label>
                          <input  type="text" id="codbl" name="codbl" class="form-control">
                          <span class="error" id="error-codbl"></span>
                        </div>
                      </div>
                      <div class="grid gap-2">
                        <div class="mb-3">
                          <label for="codbl">Servicio</label>
                          <input  type="text" id="servicio" name="servicio" class="form-control">
                          <span class="error" id="error-servicio"></span>
                        </div>
                      </div>
                  </div>

                  <div class="form-section">
                      <button  type="submit" id="btn-save-fcl" class="btn btn-outline-secondary">Guardar Booking</button>
                  </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal fade" tabindex="-1" id="newShipperDialog">
    <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Nuevo Shipper</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newShipperForm">
                            <div class="form-group">
                                <label for="shipperName">Nombre</label>
                                <input type="text" class="form-control" id="shipperName" name="shipperName" required>
                              </div>
                              <button id="btn-save-shipper" class="btn btn-primary">Crear</button>
                        </form>  
                    </div>   
                </div>
    </div>
  </div>
    </div>
  <style scoped>
    .error { 
  color: red;
  font-size: 0.9em;
  display: block;
  margin-top: 0.5em;
  width: 100%;
 }
  input.error { 
  border: 1px solid red;
  color: black; 
  }
  select.error {
    border: 1px solid red;
    color: black;
  }
  /** remove up and down button in number  input  */
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  </style>