$(document).on("ready",function(){
	init_sidebar();

	$('#empresas').dataTable({
		'order': [[ 0, 'asc' ]],
		'columnDefs': [
			{ orderable: false, targets: [3] }
		],
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

	$("#Planes tbody tr a.btn-editar").each(function(){
		$trigger = $(this);
		$trigger.on("click",function(){
			window.location.href="plan.php?id="+$trigger.attr("data-id");
		});
	});	

	$("#Planes tbody tr a.btn-eliminar").each(function(){
		$(this).on("click",function(){
			$trigger = $(this);
			if(confirm("¿Está seguro de eliminar el plan seleccionado?")){
				var ajax_data = {
	                "id":$trigger.attr("data-id")
	            };
	            $.ajax({
	                url:'src/services/eliminarplan.php',
	                type:'POST',
	                data:ajax_data,
	                contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
	                dataType:'json', //json
	                beforeSend:function(){
	                    $trigger.attr("disabled",true);
	                },
	                success:function(respuesta){
	                    $trigger.removeAttr("disabled");
	                    if(respuesta.result=='SUCCESS'){
	                        new PNotify({
	                            title: 'Eliminado',
	                            text: respuesta.desc,
	                            type: 'success',
	                            hide: false,
	                            styling: 'bootstrap3'
	                        });                
	                    }else{
	                        new PNotify({
	                            title: 'Error',
	                            text: respuesta.desc,
	                            type: 'error',
	                            hide: false,
	                            styling: 'bootstrap3'
	                        });
	                    }
	                    $trigger.parent().parent().remove();
	                },
	                error:function(obj,quepaso,otro){
	                    new PNotify({
                            title: 'Error',
                            text: quepaso,
                            type: 'error',
                            hide: false,
                            styling: 'bootstrap3'
                        });
	                }
	            });	
			}			
		});
	});
});