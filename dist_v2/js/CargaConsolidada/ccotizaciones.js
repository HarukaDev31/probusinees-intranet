var url;
$(function(){
    url=base_url+'CargaConsolidada/CCotizaciones/ajax_list';
    table_Entidad=$('#table-CCotizaciones').DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
        extend    : 'excel',
        text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr : 'Excel',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend    : 'pdf',
        text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
        titleAttr : 'PDF',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend    : 'colvis',
        text      : '<i class="fa fa-ellipsis-v"></i> Columnas',
        titleAttr : 'Columnas',
        exportOptions: {
            columns: ':visible'
        }
    }],
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
        'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
        'sLengthMenu'        : '_MENU_',
        'sSearch'            : 'Buscar por: ',
        'sSearchPlaceholder' : '',
        'sZeroRecords'       : 'No se encontraron registros',
        'sInfoEmpty'         : 'No hay registros',
        'sLoadingRecords'    : 'Cargando...',
        'sProcessing'        : 'Procesando...',
        'oPaginate'          : {
            'sFirst'    : '<<',
            'sLast'     : '>>',
            'sPrevious' : '<',
            'sNext'     : '>',
        },
    },
    'order': [],
    'ajax': {
        'url'       : url,
        'type'      : 'POST',
        'dataType'  : 'json',
        'data'      : function ( data ) {}

    },
    'columnDefs': [
        {
            'targets'   : 'no-hidden',
            'orderable' : false,
        },
        {
            'className' : 'text-center',
            'targets'   : 'no-sort',
            'orderable' : false,
        }
    ],
    'lengthMenu': [[10,100,1000,-1],[10,100,1000,"Todos"]],
});
})
function verCotizacion(ID){
    $( '.div-Listar' ).hide();
    $( '.div-AgregarEditar' ).show();
    urlCabecera = base_url + 'CargaConsolidada/CCotizaciones/ajax_edit_header/' + ID;
    $.ajax({
        url : urlCabecera,
        type: "GET",
        dataType: "JSON",
        success: function(response){
            //response have names,cbm_total, peso_total y empresa
            $('#Nombre').val(response[0].Nombre);
            $('#CBM_Total').val(response[0].CBM_Total);
            $('#Peso_Total').val(response[0].Peso_Total);
            $('#Empresa').val(response[0].Empresa);
            $('#ID_Cotizacion').val(ID);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
        }
    })

    urlBody = base_url + 'CargaConsolidada/CCotizaciones/ajax_edit_body/' + ID;
    $.ajax({
      url : urlBody,
      type: "GET",
      dataType: "JSON",
      success: function(response){
        for (var i = 0; i < response.length; i++) {
            $('#div-CotizacionBody').append(getProvTemplate(i,response[i].ID_Proveedor));
            $('#CBM_Total-'+i).val(response[i].CBM_Total);
            $('#Peso_Total-'+i).val(response[i].Peso_Total);
          productosJSON = JSON.parse(response[i].productos);
          product=0;
          
            for(const key in productosJSON){
                var productoID = productosJSON[key].ID_Producto;
                $('#div-CotizacionBody').append(getProductoTemplate(i,product,productoID));
                $(`#URL_Link-${i}-${product}`).val(productosJSON[key].URL_Link);
                $(`#Nombre_Comercial-${i}-${product}`).val(productosJSON[key].Nombre_Comercial);
                $(`#Uso-${i}-${product}`).val(productosJSON[key].Uso);
                $(`#Cantidad-${i}-${product}`).val(productosJSON[key].Cantidad);
                $(`#Valor_Unitario-${i}-${product}`).val(productosJSON[key].Valor_unitario);
                
                //add attributes data to button
                const button=$(`#button-tributo-${i}-${product}`);
                button.attr('data-nombre',productosJSON[key].Nombre_Comercial);
                button.attr('data-proveedorIndex',i);
                product++;


            }   
          
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
  }
function getProvTemplate(index,ID_Proveedor=null){
    template=
    `<div class="col-12"> 
        <div class="row">
            <div class="col-12 col-sm-3 col-md-4 col-lg-4 d-flex flex-column justify-content-center">
                <div class="row">
                    <div class="col-12 col-md-5 d-flex flex-column justify-content-center">
                    <h4>Proveedor ${index+1}</h4>
                    </div>
                    <div class="col-12 col-md-7">
                    <button type="button" class="btn btn-danger w-100" >Eliminar</button>
                    <button type="button" class="btn btn-danger w-100 mt-1" >Agregar Producto</button>

                    </div>
                        
                </div>
            </div>
            <div class="col-12 col-sm-9 col-md-8 col-lg-8">
                <div class="row d-flex proveedor flex-row">
                    <input class="proveedorID" value="${ID_Proveedor?ID_Proveedor:-1}" type="hidden" >
                    <div class="col-12 col-md-6">
                        <div class="form-group ">
                        <label>CBM Total</label>
                        <input id="CBM_Total-${index}"  type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                    </div>
                </div>
            
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Peso Total</label>
                        <input  id="Peso_Total-${index}" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                    </div>   
                </div>
            </div>
        </div>
        </div>
        <div class="row rounded-5 mb-2 border border-secondary">
            <div class="col-12 col-md-6 d-flex justify-content-center">
                <Label>Producto</Label>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-center">
                <Label>Informacion del Producto</Label>
            </div>
            <div class="col-12 col-md-2 d-flex justify-content-center">
                <Label>Tributos</Label>
            </div>
        </div>
    </div>`
    return template;
}
function getProductoTemplate(proveedor,index,productoID){
    
    template=`<div class="col-12">
        <div class="row producto-${proveedor} ">
        <input class="productID" value="${productoID}" type="hidden" >

            <div class="col-12 col-md-6 ">
            <Label>Img</Label>
            <div class="form-group">
                    <input id="URL_Link-${proveedor}-${index}"  class="form-control required URL_Link" placeholder="Ingresar" maxlength="100" autocomplete="off">
                    <span class="help-block text-danger" id="error"></span>
            </div>
            <button type="button" class="btn btn-primary w-100" style="height:50px;">Quitar</button>
        </div>
        <div class="col-12 col-md-4">
        <div class="form-group">
                <label>Nombre Comercial</label>
                <input id="Nombre_Comercial-${proveedor}-${index}"  type="text"  class="form-control required Nombre_Comercial" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Uso</label>
                <input id="Uso-${proveedor}-${index}"  type="text"  class="form-control required Uso" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Cantidad</label>
                <input id="Cantidad-${proveedor}-${index}"  type="text"  class="form-control required Cantidad" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Valor Unitario</label>
                <input id="Valor_Unitario-${proveedor}-${index}" type="text"  class="form-control required Valor_Unitario" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        </div>
        <div class="col-12 col-md-2 d-flex justify-content-center align-items-center">
        <button id="button-tributo-${proveedor}-${index}" type="button" class="btn btn-primary w-100" style="height:50px;" data-toggle="modal" data-target="#exampleModal" data-productid="${productoID}">Ver</button>
        </div>
    </div>
    </div>`
  return template;

}
var productoID=null;
var newProveedores=[

]
const guardarTributos=()=>{
    
    const tributos ={
        "ID_Producto":productoID,
        'ad-valorem':$('#ad-valorem').val(),
        'igv':$('#igv').val(),
        'ipm':$('#ipm').val(),
        'percepcion':$('#percepcion').val(),
        'valoracion':$('#valoracion').val(),
        'antidumping':$('#antidumping').val(),
    }
    url = base_url + 'CargaConsolidada/CCotizaciones/guardarTributos';
    $.ajax({
      url : url,
      type: "post",
      dataType: "JSON",
      contentType: "application/json; charset=utf-8",
      traditional: true,
      data: JSON.stringify(tributos),
      success: function(response){
        $('#exampleModal').modal('hide');
        
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
}
$('#exampleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
     // Button that triggered the modal
    productoID= button.data('productid')
    nombre=button.data('nombre')
    proveedorIndex=button.data('proveedorindex')+1
    $('#exampleModalLabel').text('Tributos del producto '+nombre+' del Proveedor '+proveedorIndex);

    if(productoID==null){
        $('#ad-valorem').val('0');
        $('#igv').val('16');
        $('#ipm').val('2');
        $('#percepcion').val('3.50');
        $('#valoracion').val('0');
        $('#antidumping').val('0');
        return;
    }
    url = base_url + 'CargaConsolidada/CCotizaciones/ajax_edit_tributos/'+productoID;
    
    $.ajax({
        url : url,
        type: "GET",
        dataType: "JSON",
        data: {ID_Producto:productoID},
        success: function(response){
            $('#ad-valorem').val(response["ad-valorem"]);
            $('#igv').val(response.igv);
            $('#ipm').val(response.ipm);
            $('#percepcion').val(response.percepcion);
            $('#valoracion').val(response.valoracion);
            $('#antidumping').val(response.antidumping);
        },
        error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
        }
        })
})
const guardarCotizacion=()=>{
    const cotizacion=[
    ]
    const proveedores = $('.proveedor');
    for (let i = 0; i < proveedores.length; i++) {
        var CBM_Total = $(proveedores[i]).find('#CBM_Total-'+i).val();
        var Peso_Total = $(proveedores[i]).find('#Peso_Total-'+i).val();
        cotizacion.push({
            ID_Proveedor:$(proveedores[i]).find('.proveedorID').val(),
            CBM_Total:CBM_Total,
            Peso_Total:Peso_Total,
            productos:[]
        });
        const productosHTML = $(`.producto-${i}`);
        for (let j = 0; j < productosHTML.length; j++) {
            var URL_Link = $(productosHTML[j]).find(`#URL_Link-${i}-${j}`).val();
            var Nombre_Comercial = $(productosHTML[j]).find(`#Nombre_Comercial-${i}-${j}`).val();
            var Uso = $(productosHTML[j]).find(`#Uso-${i}-${j}`).val();
            var Cantidad = $(productosHTML[j]).find(`#Cantidad-${i}-${j}`).val();
            var Valor_Unitario = $(productosHTML[j]).find(`#Valor_Unitario-${i}-${j}`).val();
            var ID_Producto = $(productosHTML[j]).find('.productID').val();
            cotizacion[i].productos.push({
                ID_Producto:ID_Producto,
                URL_Link:URL_Link,
                Nombre_Comercial:Nombre_Comercial,
                Uso:Uso,
                Cantidad:Cantidad,
                Valor_Unitario:Valor_Unitario,
                
            });
        }
    }
    //add preloader


    url = base_url + 'CargaConsolidada/CCotizaciones/guardarCotizacion';
    $.ajax({
      url : url,
      type: "post",
      dataType: "JSON",
      contentType: "application/json; charset=utf-8",
      traditional: true,
      data: JSON.stringify(cotizacion),
      success: function(response){
        //remove preloader
        $('.preloader').remove();
        $( '.div-Listar' ).show();
        $( '.div-CotizacionBody' ).html('');
        $( '.div-AgregarEditar' ).hide();
        table_Entidad.ajax.reload();
      },
      error: function (jqXHR, textStatus, errorThrown) {
            $('.preloader').remove();
      }
    })
}
const descargarReporte=(ID_Cotizacion)=> {
    $.ajax({
        url:base_url + 'CargaConsolidada/CCotizaciones/descargarExcel',
        type: 'POST',
        xhrFields: {
            responseType: 'blob'
        },
        data: JSON.stringify({ID_Cotizacion:ID_Cotizacion}),
        success: function(response) {
            var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'example.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            },
        error: function(errorThrown) {
            console.error('Error al descargar el archivo Excel: ' + errorThrown);
        }
    });
}
const agregarProveedor=()=>{
    const index = $('.proveedor').length;
    $('#div-CotizacionBody').append(getProvTemplate(index));
    //add initial product
    $('#div-CotizacionBody').append(getProductoTemplate(index,0));
    const button=$(`#button-tributo-${index}-${0}`);
    button.attr('data-nombre',"nuevo");
    button.attr('data-proveedorIndex',index);
    button.attr('data-productid',null);
    newProveedores.push({
        
    });
    //set disabled

}