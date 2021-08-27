$(document).on("ready",function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	$(".btn-regresar").on("click", function(event){
		event.preventDefault();
		window.location.href="responsables.php";
	});

	$(".btn-guardar").on("click", function(){
		if($.trim($("#txt-nombre").val())!="" && $.trim($("#txt-apellidos").val())!=""){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val(),
				"responsable":$("#responsable").val(),
				"nombre":$("#txt-nombre").val(),
				"apellidos":$("#txt-apellidos").val(),
				"titulo":$("#txt-titulo").val()
			};
			$.ajax({
				url:'src/services/guardaresponsable.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						new PNotify({
			                title: 'Éxito',
			                text: respuesta.desc,
			                type: 'success',
			                hide: true,
			                delay: 2000,
			                styling: 'bootstrap3'
			            });
					}else if(respuesta.result=="FAIL"){
						new PNotify({
			                title: 'Error',
			                text: respuesta.desc,
			                type: 'error',
			                hide: true,
			                delay: 2000,
			                styling: 'bootstrap3'
			            });
					}else{
						new PNotify({
			                title: 'Error',
			                text: 'Ocurrio un error desconocido',
			                type: 'error',
			                hide: true,
			                delay: 2000,
			                styling: 'bootstrap3'
			            });
					}
				},
				error:function(obj,quepaso,otro){
					new PNotify({
		                title: 'Error',
		                text: quepaso,
		                type: 'error',
		                hide: true,
		                delay: 2000,
		                styling: 'bootstrap3'
		            });
				}
			});			
		}else{
			new PNotify({
                title: 'Error',
                text: 'Debe escribir los datos del responsable',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}
	});
});