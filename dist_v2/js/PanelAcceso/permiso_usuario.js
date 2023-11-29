var url, sMenuPadre = '';

$(function () {
	$('.select2').select2();
	
	$( '#modal-loader' ).modal('show');
    
	url = base_url + 'HelperController/getEmpresasOpcionesMenu';
	$.post( url , function( response ){
		$( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
		for (var i = 0; i < response.length; i++)
			$( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '" data-nu_tipo_rubro_empresa="' + response[i].Nu_Tipo_Rubro_Empresa + '" data-proveedor_dropshipping="' + response[i].Nu_Proveedor_Dropshipping + '">' + response[i].No_Empresa + '</option>' );
		$( '#modal-loader' ).modal('hide');
	}, 'JSON');

	$( '#cbo-Empresas' ).change(function(){
		$( '#form-Permiso_Usuario' ).hide();

		$('#cbo-Grupos').html('<option value="0" selected="selected">- vacío -</option>');

		$( '#modal-loader' ).modal('show');
		url = base_url + 'HelperController/getOrganizaciones';
		var arrParams = {
		  iIdEmpresa : $( this ).val(),
		};
		$.post( url, arrParams, function( response ){
			$( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
			for (var i = 0; i < response.length; i++)
				$( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
			$( '#modal-loader' ).modal('hide');
		}, 'JSON');
	});

	url = base_url + 'HelperController/getOrganizaciones';
	var arrParams = {
	  iIdEmpresa : $( '#cbo-Empresas' ).val(),
	}
	$.post( url, arrParams, function( response ){
		$( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
		for (var i = 0; i < response.length; i++)
		$( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
		$( '#modal-loader' ).modal('hide');  
	}, 'JSON');

	$( '#cbo-organizacion' ).change(function(){
		$( '#form-Permiso_Usuario' ).hide();

		$( '#modal-loader' ).modal('show');
		url = base_url + 'HelperController/getGrupos';
		var arrParams = {
			iIdEmpresa : $( '#cbo-Empresas' ).val(),
		    iIdOrganizacion : $(this).val(),
		};
		$.post( url, arrParams, function( response ){
		  $( '#cbo-Grupos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
		  for (var i = 0; i < response.length; i++)
			$( '#cbo-Grupos' ).append( '<option value="' + response[i].ID_Grupo + '">' + response[i].No_Grupo + '</option>' );    
		  $( '#modal-loader' ).modal('hide');
		}, 'JSON');
	});
	
	if ( $( '#cbo-Empresas' ).val() > 0 || $( '#cbo-organizacion' ).val() > 0 ) {
		$( '#modal-loader' ).modal('show');
		url = base_url + 'HelperController/getGrupos';
		var arrParams = {
			iIdEmpresa : $( '#cbo-Empresas' ).val(),
			iIdOrganizacion : $( '#cbo-organizacion' ).val(),
		};
		$.post( url, arrParams, function( response ){
		  $( '#cbo-Grupos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
		  for (var i = 0; i < response.length; i++)
			$( '#cbo-Grupos' ).append( '<option value="' + response[i].ID_Grupo + '">' + response[i].No_Grupo + '</option>' );    
		  $( '#modal-loader' ).modal('hide');
		}, 'JSON');
	}

	var $eventSelect = $( '#cbo-Grupos' );
	$eventSelect.on("select2:select", function (e) {
		var data = e.params.data;
		var ID_Grupo = data.id;
		$( '#form-Permiso_Usuario' ).hide();

    	if ( ID_Grupo > 0 ) {
			$( '#form-Permiso_Usuario' ).show();
			$( '#modal-loader' ).modal('show');
			url = base_url + 'PanelAcceso/PermisoUsuarioController/getMenuAccesoxGrupo/' + $( '#cbo-Empresas' ).val() + '/' + $( '#cbo-organizacion' ).val() + '/' + ID_Grupo + '/' + $( '#cbo-Empresas' ).find(':selected').data('nu_tipo_rubro_empresa') + '/' + $( '#cbo-Empresas' ).find(':selected').data('proveedor_dropshipping');
			$.getJSON( url, function( response ) {
				if (response.status == 'success') {
					response = response.arrData;
					var content	= '';
					content	+= ''
					+ '<form id="form-Permiso_Usuario" enctype="multipart/form-data" method="post" role="form" autocomplete="off">'
						+ '<input type="hidden" name="ID_Grupo_" value="' + ID_Grupo + '" >'
						+ '<input type="hidden" name="iProveedorDropshipping" value="' + $( '#cbo-Empresas' ).find(':selected').data('proveedor_dropshipping') + '" >'
						+ '<table id="table-Permiso_Usuario" class="table table-striped table-bordered">'
							+ '<thead>'
								+ '<tr>'
								+ '<th class="text-center"><input type="checkbox" class="flat-red" onclick="checkAllMenuHeader();" id="check-AllMenuHeader"></th>'
									+ '<th class="text-center">Menu</th>'
									+ '<th class="text-center">Consultar</th>'
									+ '<th class="text-center">Agregar</th>'
									+ '<th class="text-center">Editar</th>'
									+ '<th class="text-center">Eliminar y Anular</th>'
									+ '<th class="text-center"><button type="button" class="btn btn-success btn-save" onclick="guardarMenuAcceso();"><span class="fa fa-save"></span> Guardar</button></th>'
								+ '</tr>'
							+ '</thead>'
							+ '<tbody>';
							var checkedC = [];
							var checkedR = [];
							var checkedU = [];
							var checkedD = [];
							var checkHeaderFooter = false;
							var $ID_Grupo = '';
							var ID_Padre = 0;
							var Txt_Url_Video = '';
							for (var i = 0; i < response.length; i++) {
								checkedC[i] = "";
								checkedR[i] = "";
								checkedU[i] = "";
								checkedD[i] = "";

								if (response[i]['ID_Grupo'] != null){
									$ID_Grupo = response[i]['ID_Grupo'];
									if (response[i]['Nu_Consultar'] == 1) {
										checkedC[i] = "checked";
										checkHeaderFooter = true;
									}
									if (response[i]['Nu_Agregar'] == 1) {
										checkedR[i] = "checked";
										checkHeaderFooter = true;
									}
									if (response[i]['Nu_Editar'] == 1) {
										checkedU[i] = "checked";
										checkHeaderFooter = true;
									}
									if (response[i]['Nu_Eliminar'] == 1) {
										checkedD[i] = "checked";
										checkHeaderFooter = true;
									}
								} // /. if grupo
        			  
								if (ID_Padre != response[i]['ID_Padre']) {
									content += ''
									+'<tr>'
										+ '<td class="text-left" align="left" colspan="6"><b style="font-size: 18px; font-weight: bold;">' + response[i]['No_Menu_Padre'] + ' </b>' + (response[i]['No_Menu_Padre'] != 'Configuración' ? '' : '(Las opciones que faltan el sistema lo creará automaticamente)') + '</td>'
									+'</tr>';
									ID_Padre = response[i]['ID_Padre'];
								}
								
								Txt_Url_Video = (response[i]['Txt_Url_Video'] != null && response[i]['Txt_Url_Video'] != '' ? ' &nbsp; <a href="' + response[i]['Txt_Url_Video'] + '" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Ver video ' + response[i]['No_Menu'] + '" alt="Ver video ' + response[i]['No_Menu'] + '"></i> <span style="color: #7b7b7b; font-size: 11px;">ver video<span></a>' : '');
								content	+= ''
								+'<tr>'
									+'<td class="text-right" align="right"><input type="hidden" name="ID_Menu[]" value="' + response[i]['ID_Menu'] + '">' +  (i + 1) + '. </td>'
									+'<td class="text-left" align="left">' + response[i]['No_Menu'] + Txt_Url_Video + '</td>'
									+'<td class="c text-center" align="center" onclick="checkboxActivarDesactivarIndividual(\'C\' , \'' + i + '\');"><input type="checkbox" onclick="checkboxActivarDesactivarIndividual(\'C\' , \'' + i + '\');" data-idc="C'+i+'" id="C'+i+'" class="flat-red check-Menu_Accion" name="ID_Menu_CRUD[' + response[i]['ID_Menu'] + '][Nu_Consultar]" ' + checkedC[i] + '></td>'
									+'<td class="r text-center" align="center" onclick="checkboxActivarDesactivarIndividual(\'R\' , \'' + i + '\');"><input type="checkbox" onclick="checkboxActivarDesactivarIndividual(\'R\' , \'' + i + '\');" data-idr="R'+i+'" id="R'+i+'" class="flat-red check-Menu_Accion" name="ID_Menu_CRUD[' + response[i]['ID_Menu'] + '][Nu_Agregar]" ' + checkedR[i] + '></td>'
									+'<td class="u text-center" align="center" onclick="checkboxActivarDesactivarIndividual(\'U\' , \'' + i + '\');"><input type="checkbox" onclick="checkboxActivarDesactivarIndividual(\'U\' , \'' + i + '\');" data-idu="U'+i+'" id="U'+i+'" class="flat-red check-Menu_Accion" name="ID_Menu_CRUD[' + response[i]['ID_Menu'] + '][Nu_Editar]" ' + checkedU[i] + '></td>'
									+'<td class="d text-center" align="center" onclick="checkboxActivarDesactivarIndividual(\'D\' , \'' + i + '\');"><input type="checkbox" onclick="checkboxActivarDesactivarIndividual(\'D\' , \'' + i + '\');" data-idd="D'+i+'" id="D'+i+'" class="flat-red check-Menu_Accion" name="ID_Menu_CRUD[' + response[i]['ID_Menu'] + '][Nu_Eliminar]" ' + checkedD[i] + '></td>'
								+'</tr>';
        					}// /. for 
							content += ''
							+'</tbody>'
							+ '<tfoot>'
								+ '<tr>'
									+ '<th class="text-center"><input type="checkbox" class="flat-red" onclick="checkAllMenuFooter();" id="check-AllMenuFooter"></th>'
									+ '<th class="text-center">Menu</th>'
									+ '<th class="text-center">Consultar</th>'
									+ '<th class="text-center">Agregar</th>'
									+ '<th class="text-center">Editar</th>'
									+ '<th class="text-center">Eliminar</th>'
									+ '<th class="text-center"><button type="button" class="btn btn-success btn-save" onclick="guardarMenuAcceso();"><span class="fa fa-save"></span> Guardar</button></th>'
								+ '</tr>'
							+ '</tfoot>'
						+'</table>'
						+ '<input type="hidden" name="ID_Empresa" value="' + $( '#cbo-Empresas' ).val() + '" />'
						+ '<input type="hidden" name="ID_Organizacion" value="' + $( '#cbo-organizacion' ).val() + '" />'
						+ '<input type="hidden" name="ID_Grupo" value="' + $ID_Grupo + '" />'
					+'</form>';
					$( '.div-table-Permiso_Usuario' ).html(content);
					
          			if ( checkHeaderFooter ){
						$( '#check-AllMenuHeader' ).prop('checked', true);
						$( '#check-AllMenuFooter' ).prop('checked', true);
          			}
				} else {
					$( '#modal-message' ).modal('show');
					$( '.modal-message' ).addClass(response.style_modal);
					$( '.modal-title-message' ).text(response.message);
					setTimeout(function() {$('#modal-message').modal('hide');}, 3000);
				}
				$( '#modal-loader' ).modal('hide');
			}, 'JSON')
			.fail(function(jqXHR, textStatus, errorThrown) {
				$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
				
				$( '#modal-message' ).modal('show');
				$( '.modal-message' ).addClass( 'modal-danger' );
				$( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
				
				$( '#modal-loader' ).modal('hide');
			
				//Message for developer
				console.log(jqXHR.responseText);
			});
		}// /. if
	});
})// /. Document Ready

var sIDCheckCRUD;
function checkboxActivarDesactivarIndividual(tipo, i){
    sIDCheckCRUD = "#" + tipo + i;
	if ( !$( sIDCheckCRUD ).prop('checked') ) {
    	$( sIDCheckCRUD ).prop('checked', true);
	}else {
    	$( sIDCheckCRUD ).prop('checked', false);
	}
}

function checkAllMenuHeader(){
	if ( $( '#check-AllMenuHeader' ).prop('checked') ){
		$( '.check-Menu_Accion' ).prop('checked', true);
		$( '#check-AllMenuFooter' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuHeader' ).prop('checked') ){
			$( '.check-Menu_Accion' ).prop('checked', false);
			$( '#check-AllMenuFooter' ).prop('checked', false);
		}
	}
}

function checkAllMenuFooter(){
	if ( $( '#check-AllMenuFooter' ).prop('checked') ){
		$( '.check-Menu_Accion' ).prop('checked', true);
		$( '#check-AllMenuHeader' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuFooter' ).prop('checked') ){
			$( '.check-Menu_Accion' ).prop('checked', false);
			$( '#check-AllMenuHeader' ).prop('checked', false);
		}
	}
}

function guardarMenuAcceso(){
	$( '.btn-save' ).text('');
	$( '.btn-save' ).attr('disabled', true);
	$( '.btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

	$( '#modal-loader' ).modal('show');
	url = base_url + 'PanelAcceso/PermisoUsuarioController/crudPermisoUsuario';
	$.ajax({
		type : 'POST',
		dataType : 'JSON',
		url : url,
		data : $('#form-Permiso_Usuario').serialize(),
		success : function( response ){
			$( '#modal-loader' ).modal('hide');
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$( '#modal-message' ).modal('show');
			
			if (response.sStatus == 'success'){
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

				window.location.href = base_url + $('#hidden-sDirectory').val() + $('#hidden-sClass').val() + '/' + $('#hidden-sMethod').val();
			} else {
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
			}

			$( '.btn-save' ).text('');
			$( '.btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
			$( '.btn-save' ).attr('disabled', false);
		}
  	})
	.fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
		setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
		
		$( '#modal-loader' ).modal('hide');
	
		//Message for developer
		console.log(jqXHR.responseText);

		$( '.btn-save' ).text('');
		$( '.btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
		$( '.btn-save' ).attr('disabled', false);
	});
}