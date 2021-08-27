$(document).on("ready",function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	/*$(".btn-regresar").on("click", function(event){
		event.preventDefault();
		window.location.href="responsables.php";
	});*/

	$("#sl-presidente, #sl-tesorero, #sl-regidor, #sl-sindico, #sl-dir-juridico, #sl-ogano-control-interno, #sl-jefe-adquisiciones").selectpicker({
		liveSearch:true
	});

	$(".btn-editar").on("click", function(){
		var $sl = $(this).parent().parent().find("select.form-control");
		if($.trim($sl.val())!="" && $.trim($sl.val())!=""){
			var ajax_data = {
				"id":$sl.val(),
				"cargo":$(this).attr("data-id-cargo"),
				"empresa":$("#empresa").val()
			};
			$.ajax({
				url:'src/services/updatecargo.php',
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