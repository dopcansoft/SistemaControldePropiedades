$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#articulos tr.dummy");
	
	var $tabla = $('#articulos').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [2] }
		],
		"bProcessing": true,
		language: {
			processing:     "Buscando ...",
			search:         "Buscar:",
			lengthMenu:    "Mostrar _MENU_ registros",
			info:           "Registro _START_ hasta _END_ de _TOTAL_ registro(s)",
			infoEmpty:      "Sin coincidencias",
			infoFiltered:   "(filtrado de _MAX_ registros en total)",
			infoPostFix:    "",
			loadingRecords: "Cargando informacion",
			zeroRecords:    "Sin concidencias",
			emptyTable:     "Sin registros",
			paginate: {
				first:      "Primera",
				previous:   "Previa",
				next:       "Siguiente",
				last:       "Ultima"
			}
		}
	});
	
	$("#articulos tbody").on("click", "button.btn-editar",function(event){
		event.preventDefault();
		var $trigger = $(this);
		window.location.href="responsable.php?id="+$trigger.attr("data-id");
	});

	$("#articulos tbody").on("click", "button.btn-eliminar",function(event){
		event.preventDefault();
		var $trigger = $(this);
		if(confirm("¿Esta seguro de eliminar a este responsable?")){
			var ajax_data = {
				"id":$trigger.attr("data-id"),
				"empresa":$("#empresa").val()
			}
			$.ajax({
				url:'src/services/eliminaresponsable.php',
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
			            $("#articulos tbody").find("tr[data-id='"+$trigger.attr("data-id")+"']").remove();

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
		}
	});

	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});