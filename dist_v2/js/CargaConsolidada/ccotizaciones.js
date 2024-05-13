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

    url = base_url + 'CargaConsolidada/CCotizaciones/ajax_edit_body/' + ID;
    $.ajax({
      url : url,
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
                product++;

            }   
          
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
  }

function getProvTemplate(index,ID_Proveedor){
    template=`<div class="col-12"> 
    <div class="row"><div class="col-12 col-sm-3 col-md-6 col-lg-8"><label>Proveedor ${index+1}</label></div>
    <div class="col-12 col-sm-9 col-md-6 col-lg-4">
      <div class="row d-flex proveedor">
        <input class="proveedorID" value="${ID_Proveedor}" type="hidden" >
        <div class="form-group ">
            <input id="CBM_Total-${index}"disabled="true" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
            <span class="help-block text-danger" id="error"></span>
            </div>
            <div class="form-group">
                <input disabled="true"
                id="Peso_Total-${index}"
                type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
            </div>
        </div>
    </div>
    </div>
    </div>`
    return template;
}
function getProductoTemplate(proveedor,index,productoID){
    
    template=`<div class="col-12">
    <div class="row producto-${proveedor}">
        <input class="productID" value="${productoID}" type="hidden" >

            <div class="col-12 col-md-6">
            <label>Img</label>
            <div class="form-group">
                    <input id="URL_Link-${proveedor}-${index}" class="URL_Link" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                    <span class="help-block text-danger" id="error"></span>
            </div>
        </div>
        <div class="col-12 col-md-3">
        <div class="form-group">
                <input id="Nombre_Comercial-${proveedor}-${index}" class="Nombre_Comercial" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <input id="Uso-${proveedor}-${index}" class="Uso" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <input id="Cantidad-${proveedor}-${index}" class="Cantidad" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <input id="Valor_Unitario-${proveedor}-${index}" class="Valor_Unitario" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        </div>
        <div class="col-12 col-md-3">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-productid="${productoID}">Ver Tributo</button>
        </div>
    </div>
    </div>`
  return template;

}
var productoID=null;
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
        
        
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
}
$('#exampleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    productoID= button.data('productid') // Extract info from data-* attributes   
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
    url = base_url + 'CargaConsolidada/CCotizaciones/guardarCotizacion';
    $.ajax({
      url : url,
      type: "post",
      dataType: "JSON",
      contentType: "application/json; charset=utf-8",
      traditional: true,
      data: JSON.stringify(cotizacion),
      success: function(response){
        
        
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
}