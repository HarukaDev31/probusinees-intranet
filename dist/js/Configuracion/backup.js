$(function () {
	$( "#btn-generar_backup" ).click(function(){
		if(confirm('Este proceso puede tardar varios minutos, dependiendo la información que contenga.')){
			$.post(base_url + 'Configuracion/BackupController/generarBackup', function(r){
				if(r.response){
					alert("Se ha creado la copia con éxito, el proceso ha tardado " + r.message + 's.');
        		    window.location = base_url + 'Configuracion/BackupController/listarBackups';
				}else{
					alert(r.message);	
				}
			}, 'json')
		}
		return false;
	})  
})