$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#articulos tr.dummy");
	
	$("#txt-fecha-inicio").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es"
    }).on("changeDate",function(e){
    	var startDate = $(this).val();
    	var final = moment($("#txt-fecha-fin").val(),"DD-MM-Y");
    	var inicio = moment($("#txt-fecha-inicio").val(),"DD-MM-Y");
    	if($.trim($("#txt-fecha-fin").val())!=""){
    		if(final.format("Y-MM-DD")<inicio.format("Y-MM-DD")){	
    			$("#txt-fecha-fin").datepicker("setDate",$(this).val());	
    			console.log("final es menor");
    		}else{
    			console.log("final es mayor");
    		}
    	}else{
    		$("#txt-fecha-fin").datepicker("setDate",$(this).val());
    	}
    });


	$("#txt-fecha-fin").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es"
    }).on("changeDate", function(e){
    	var final = moment($("#txt-fecha-fin").val(),"DD-MM-Y");
    	var inicio = moment($("#txt-fecha-inicio").val(),"DD-MM-Y");
    	if($.trim($("#txt-fecha-inicio").val())!=""){
    		if(final.format("Y-MM-DD")<inicio.format("Y-MM-DD")){	
    			$("#txt-fecha-inicio").datepicker("setDate",$(this).val());	
    			console.log("final es menor");
    		}else{
    			console.log("final es mayor");
    		}
    	}else{
    		$("#txt-fecha-inicio").datepicker("setDate",$(this).val());
    	}
    });

	var $tabla = $('#articulos').DataTable({
		'order': [[ 0, 'asc' ]],
		'columnDefs': [
			{ orderable: false, targets: [6] }
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

	$('#articulos tbody tr').on( 'click', 'a', function (event) {
		event.preventDefault();
        //var data = table.row( $(this).parents('tr') ).data();
        console.log("click");
        //alert( data[0] +"'s salary is: "+ data[ 5 ] );
    } );

	/*$tabla.column(0).data().each(function(value, index){
		console.log( 'Data in index: '+index+' is: '+value );
	});*/

	/*$("button.btn-editar").bind("click",function(event){
		redirection($(this), event);	
	});*/


	/*$('#articulos tbody tr').on( 'click', 'button', function(event){
	 	event.preventDefault();
	 	console.log("click");
        window.location.href="bien.php?id="+$(this).attr("data-id");
    });*/

	/*$("#articulos tbody tr button.btn-editar").each(function(){		
		console.log("append");
		$(this).on("click",function(event){
			event.preventDefault();
			window.location.href="bien.php?id="+$(this).attr("data-id");
		});
	});*/

	/*$("#btn-buscar").on("click", function(){
		$trigger = $(this);
		var fechaInicio = moment($("#txt-fecha-inicio").val(),"D-mm-Y");
		var fechaFin = moment($("#txt-fecha-fin").val(),"D-mm-Y");
		var ajax_data = {
			"empresa":$("#empresa").val(),
			"periodo":$("#periodo").val(),
			"fechaInicio": fechaInicio.format("Y-mm-D")+" 00:00:00",
            "fechaFin": fechaFin.format("Y-mm-D")+" 23:59:59",
			"clasificacionBM":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
			"clasificacionBI":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
			"departamento":$("#sl-departamento").val(),
			"estadoFisico":$("#sl-estado-fisico").val(),
			"tipo":$("#sl-tipo").val(),
		};

		$.ajax({
			url:'src/services/filtrado.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				limpiarTabla($tabla);
				$trigger.attr("disabled",true);
			},
			success:function(respuesta){
				if(respuesta.result=="SUCCESS"){
					var $datos = respuesta.data;
					if(respuesta.desc!=""){
						new PNotify({
							title: 'Completado',
							text: respuesta.desc,
							type: 'info',
							hide: true,
							styling: 'bootstrap3'
			            });
					}
					for(var i=0;i<respuesta.data.length;i++){
						console.log("iterando");
						var $row = dummy.clone(true);
						$row.removeClass("dummy");
						console.dir($datos[i]);
						$row.find("td:eq(0)").html($datos[i].descripcion);
						$row.find("td:eq(1)").html(($datos[i].clasificacion.descr.toLowerCase()).replace(/\b\w/g, l => l.toUpperCase()));
						$row.find("td:eq(2)").html(($datos[i].tipoClasificacion.descr.toLowerCase()).replace(/\b\w/g, l => l.toUpperCase()));
						$row.find("td:eq(3)").html(($datos[i].departamento.descr.toLowerCase()).replace(/\b\w/g, l => l.toUpperCase()));
						$row.find("td:eq(4)").html(($datos[i].estadoFisico.descr.toLowerCase()).replace(/\b\w/g, l => l.toUpperCase()));
						var fechaInsert = moment($datos[i].fechaInsert); 
						$row.find("td:eq(5)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
						$row.find("td:eq(6) a:eq(0)").attr("data-id",$datos[i].id);
						$row.find("td:eq(6) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
						$row.find("td:eq(6) a:eq(0)").attr("href","bien.php?id="+$datos[i].id);
						$row.find("td:eq(6) button:eq(1)").attr("data-id",$datos[i].id);
						$row.find("td:eq(6) button:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);						
						$tabla.row.add($row);	
					}
					$tabla.draw();
				}else if(respuesta.result=="FAIL"){
					new PNotify({
						title: 'Completado',
						text: respuesta.desc,
						type: 'info',
						hide: true,
						styling: 'bootstrap3'
		            });
				}
				$trigger.removeAttr("disabled");
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
                $trigger.removeAttr("disabled");
			}
		});
	});	*/

	/*$("#articulos tbody tr a.btn-eliminar").each(function(event){
		$(this).on("click",function(event){
			event.preventDefault();
			$trigger = $(this);
			if(confirm("¿Está seguro de eliminar el bien seleccionado?")){
				var ajax_data = {
	                "id":$trigger.attr("data-id")
	            };
	            $.ajax({
	                url:'src/services/eliminarbien.php',
	                type:'POST',
	                data:ajax_data,
	                contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
	                dataType:'json', //json
	                beforeSend:function(){
	                    $trigger.attr("disabled",true);
	                },
	                success:function(respuesta){
	                    console.dir(respuesta);
	                    $trigger.removeAttr("disabled");
	                    if(respuesta.result=='SUCCESS'){
	                        $trigger.parent().parent().remove();
	                        new PNotify({
	                            title: 'Eliminado',
	                            text: respuesta.desc,
	                            type: 'success',
	                            hide: true,
	                            delay: 2000,
	                            styling: 'bootstrap3'
	                        });                
	                    }else{
	                        new PNotify({
	                            title: 'Error',
	                            text: respuesta.desc,
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
                            delay: 2000,
                            hide: true,
                            styling: 'bootstrap3'
                        });
                        $trigger.removeAttr("disabled");
	                }
	            });	
			}			
		});
	});*/

	console.dir($("#articulos tbody tr.dummy").find("button:eq(0)"));
});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}

function redirection($element, event){
	event.preventDefault();
	window.location.href="bien.php?id="+$element.attr("data-id");
}