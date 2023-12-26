var url, table_Entidad;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate(), Fe_Inicio='', Fe_Fin='';

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

  $('#btn-excel_reporte').click(function () {
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
      
    Fe_Inicio = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
    Fe_Fin = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');

    $( '#btn-excel_reporte' ).text('');
    $( '#btn-excel_reporte' ).attr('disabled', true);
    $( '#btn-excel_reporte' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
    url = base_url + 'AgenteCompra/HistorialPagos/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin;
    window.open(url,'_blank');
    
    $( '#btn-excel_reporte' ).text('');
    $( '#btn-excel_reporte' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
    $( '#btn-excel_reporte' ).attr('disabled', false);
  })//./ btn
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}