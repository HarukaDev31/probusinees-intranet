var url, table_Entidad;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();

$(function () {
  $("#table-Cliente").on('click', '.img-table_item', function () {
    $('.img-responsive').attr('src', '');

    $('.modal-ver_item').modal('show');
    $('.img-responsive').attr('src', $(this).data('url_img'));
    $("#a-download_image").attr("data-id_item", $(this).data('id_item'));
  })

  //Date picker invoice
  $( '.input-report' ).datepicker({
    autoclose : true,
    startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  //Global Autocomplete
  $( '.autocompletar' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term = term.toLowerCase();
        $.post( base_url + 'AutocompleteImportacionController/globalAutocomplete', { global_search : term }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-val="' + search + '">' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID' ).val(item.data('id'));
      $( '#txt-ACodigo' ).val(item.data('codigo'));
      $('#txt-ANombre').val(item.data('nombre'));

			arrItemVentaTemporal={
				id:item.data('id'),
				codigo:item.data('codigo'),
				nombre:item.data('nombre'),
      }
      
			agregarItemVentaTemporal(arrItemVentaTemporal);
    }
  });

  url = base_url + 'AgenteCompra/HistorialPagos/ajax_list';
  table_Entidad = $( '#table-Cliente' ).DataTable({
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
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $('#table-Cliente_filter input').removeClass('form-control-sm');
  $('#table-Cliente_filter input').addClass('form-control-md');
  $('#table-Cliente_filter input').addClass("width_full");
  
  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });

  $('.btn-generar_ventas_x_familia').click(function () {
    if ($('#cbo-filtro_monedas').val() == '0') {
      $('#cbo-filtro_monedas').closest('.form-group').find('.help-block').html('Seleccionar moneda');
      $('#cbo-filtro_monedas').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();
    
      var ID_Almacen, Fe_Inicio, Fe_Fin, iIdMoneda, iIdFamilia, iIdItem, sNombreItem, iIdSubFamilia, Nu_Agrupar_Empresa, ID_Marca, ID_Variante_Item, ID_Variante_Item_Detalle_1, ID_Variante_Item2, ID_Variante_Item_Detalle_2, ID_Variante_Item3, ID_Variante_Item_Detalle_3, Nu_Tipo_Impuesto;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdMoneda = $( '#cbo-filtro_monedas' ).val();
      iIdFamilia = $('#cbo-familia').val();
      iIdItem = ($('#txt-ID_Producto').val().length === 0 ? '-' : $('#txt-ID_Producto').val());
      sNombreItem = ($('#txt-No_Producto').val().length === 0 ? '-' : $('#txt-No_Producto').val());
      iIdSubFamilia = $('#cbo-sub_categoria').val();
      ID_Almacen = $('#cbo-Almacenes_VentasxFamilia').val();
      Nu_Agrupar_Empresa = $('[name="radio-agrupar_x_empresa"]:checked').attr('value');
      iFiltroBusquedaNombre = ($("#checkbox-busqueda_producto").prop("checked") == true ? 1 : 0);
      ID_Marca = $( '#cbo-filtro_marca' ).val();
      ID_Variante_Item = $( '#cbo-filtro_variante_1' ).val();
      ID_Variante_Item_Detalle_1 = $( '#cbo-filtro_valor_1' ).val();
      ID_Variante_Item2 = $( '#cbo-filtro_variante_2' ).val();
      ID_Variante_Item_Detalle_2 = $( '#cbo-filtro_valor_2' ).val();
      ID_Variante_Item3 = $( '#cbo-filtro_variante_3' ).val();
      ID_Variante_Item_Detalle_3 = $( '#cbo-filtro_valor_3' ).val();
      Nu_Tipo_Impuesto = $('#cbo-regalo').val();

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin: Fe_Fin,
        iIdMoneda: iIdMoneda,
        iIdFamilia: iIdFamilia,
        iIdItem: iIdItem,
        sNombreItem: sNombreItem,
        iIdSubFamilia: iIdSubFamilia,
        ID_Almacen: ID_Almacen,
        Nu_Agrupar_Empresa:Nu_Agrupar_Empresa,
        iFiltroBusquedaNombre: iFiltroBusquedaNombre,
        ID_Marca: ID_Marca,
        ID_Variante_Item: ID_Variante_Item,
        ID_Variante_Item_Detalle_1: ID_Variante_Item_Detalle_1,
        ID_Variante_Item2: ID_Variante_Item2,
        ID_Variante_Item_Detalle_2: ID_Variante_Item_Detalle_2,
        ID_Variante_Item3: ID_Variante_Item3,
        ID_Variante_Item_Detalle_3: ID_Variante_Item_Detalle_3,
        Nu_Tipo_Impuesto:Nu_Tipo_Impuesto
      };
        
      if ($(this).data('type') == 'excel') {
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-excel_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/informes_venta/VentasxFamiliaController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdMoneda + '/' + iIdFamilia + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iIdSubFamilia + '/' + ID_Almacen + '/' + Nu_Agrupar_Empresa + '/' + iFiltroBusquedaNombre + '/' + ID_Marca + '/' + ID_Variante_Item + '/' + ID_Variante_Item_Detalle_1 + '/' + ID_Variante_Item2 + '/' + ID_Variante_Item_Detalle_2 + '/' + ID_Variante_Item3 + '/' + ID_Variante_Item_Detalle_3  + '/' + Nu_Tipo_Impuesto;
        window.open(url,'_blank');
        
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', false);
      }// ./ if
    }
  })//./ btn
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}