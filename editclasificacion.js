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

    console.log("dato: "+$tabla.$("tr").length);

    $("#btn-guardar").on("click", function(){
		$trigger = $(this);
		var datos = new Array();
		$tabla.$("tr").each(function(){
			if($(this).find("td:eq(2) div").length>0){
				//console.log("id: "+$(this).find("td:eq(2) div").attr("data-id")+", estatus:"+$(this).find("input:checked").val());	
				datos.push({
					id:$(this).find("td:eq(2) div").attr("data-id"),
					enabled:$(this).find("input:checked").val()
				});
			}
		});
		console.dir(datos);
		var ajax_data = {
			idEmpresa:$("#empresa").val(),
			idPeriodo:$("#periodo").val(),
			ids:datos
		}
		$.ajax({
			url:'src/services/updestatuscla.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				$trigger.attr("disabled",true);
				$trigger.html("<i class='fa fa-refresh fa-spin'></i>&nbsp;Espere ..."); 
			},
			success:function(respuesta){
				if(respuesta.result=="SUCCESS"){
					new PNotify({
						title: 'Completado',
						text: respuesta.desc,
						type: 'success',
						hide: false,
						styling: 'bootstrap3',
					});
				}else if(respuesta.result=="FAIL"){
					new PNotify({
						title: 'Error',
						text: respuesta.desc,
						type: 'error',
						hide: false,
						styling: 'bootstrap3',
					});
				}else{
					new PNotify({
						title: 'Error',
						text: (typeof respuesta.desc!='undefined'?respuesta.desc:'Ocurrio un error inesperado'),
						type: 'info',
						hide: false,
						styling: 'bootstrap3',
					});
				}
				$trigger.html("<i class='fa fa-save'></i> Guardar");
				$trigger.removeAttr("disabled",true);
			},
			error:function(obj,quepaso,otro){
				$trigger.html("<i class='fa fa-save'></i> Guardar");
				$trigger.removeAttr("disabled",true);
				new PNotify({
					title: 'Error',
					text: quepaso,
					type: 'error',
					hide: false,
					styling: 'bootstrap3',
				});
			}
		});
    });

	//console.dir($("#articulos tbody tr.dummy").find("button:eq(0)"));
	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}

function redirection($element, event){
	event.preventDefault();
	window.location.href="bien.php?id="+$element.attr("data-id");
}
