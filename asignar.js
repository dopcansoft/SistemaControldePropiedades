$(document).on("ready",function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	$(".btn-regresar").on("click", function(event){
		event.preventDefault();
		window.location.href="departamentos.php";
	});

	$('#sl-responsable').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true
	});

	$(".btn-guardar").on("click", function(){
		if($.trim($("#txt-descr").val())!=""){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val(),
				"departamento":$("#departamento").val(),
				"responsable":$("#sl-responsable").val(),
				"folio":$("#txt-folio").val()
			};
			$.ajax({
				url:'src/services/updasignacion.php',
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
                text: 'Debe escribir el nombre del departamento',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}
	});
});