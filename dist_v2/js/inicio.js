$(function () {

  /*
  $("#example1").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
  */
 
	$(document).on('click', '#btn-save_cliente', function (e) {
    e.preventDefault();

    $( '#btn-save_cliente' ).text('');
    $( '#btn-save_cliente' ).attr('disabled', true);
    $( '#btn-save_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'InicioController/crudCliente';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-cliente').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            accion_cliente = '';
            $('.modal-cliente').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            setTimeout(function(){
              window.location.href = base_url + 'InicioController';
            }, 2100);
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_cliente' ).text('');
          $( '#btn-save_cliente' ).append( 'Guardar' );
          $( '#btn-save_cliente' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_cliente' ).text('');
          $( '#btn-save_cliente' ).append( 'Guardar' );
          $( '#btn-save_cliente' ).attr('disabled', false);
      }
    });
  });
});

function editarCliente(ID_Entidad){
  //alert(ID_Entidad);
  $( '#form-cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('.modal-cliente').modal('show');

  $( '[name="ID_Entidad"]' ).val('');
  $( '[name="ENo_Entidad"]' ).val('');
  
  url = base_url + 'Ventas/ReglasVenta/ClienteController/ajax_edit/' + ID_Entidad;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="ID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="ENo_Entidad"]' ).val(response.No_Entidad);

      //empresa
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);

      //cliente
      $( '[name="No_Contacto"]' ).val(response.No_Contacto);
      $( '[name="Nu_Documento_Identidad_Externo"]' ).val(response.Nu_Documento_Identidad_Externo);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
      console.log(jqXHR.responseText);
    }
  })
}