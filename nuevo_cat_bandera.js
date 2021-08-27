$(document).on("ready",function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	$(".btn-regresar").on("click", function(event){
		event.preventDefault();
		window.location.href="cat_bandera.php";
	});

	$(".btn-guardar").on("click", function(){
		if($.trim($("#txt-descr").val())!="" ){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val(),
				"status":$("#txt-status").val(),
				"descr":$("#txt-descr").val()
			};
			$.ajax({
				url:'src/services/insert_cat_bandera.php',
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
			                delay: 3000,
			                styling: 'bootstrap3'
			            }
			            );
			            
                   setTimeout(function () {
                       window.location.href = "cat_bandera.php"; 
                       }, 2000);   

					}else if(respuesta.result=="FAIL"){
						new PNotify({
			                title: 'Error1 1',
			                text: respuesta.desc,
			                type: 'error',
			                hide: true,
			                delay: 3000,
			                styling: 'bootstrap3'
			            });
					}else{
						new PNotify({
			                title: 'Error',
			                text: 'Ocurrio un error desconocido',
			                type: 'error',
			                hide: true,
			                delay: 3000,
			                styling: 'bootstrap3'
			            });
					}
				},
				error:function(obj,quepaso,otro){
					new PNotify({
		                title: 'Error fhg',
		                text: quepaso,
		                type: 'error',
		                hide: true,
		                delay: 3000,
		                styling: 'bootstrap3'
		            });
				}
			});			
		}else{
			new PNotify({
                title: 'Error',
                text: 'Debe escribir los datos de la bandera',
                type: 'error',
                hide: true,
                delay: 3000,
                styling: 'bootstrap3'
            });
		}
	});
});