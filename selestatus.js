$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#articulos tr.dummy");
	
	var $tabla = $('#articulos').DataTable({
		'order': [[ 1, 'asc' ]],
		'stateSave': true,
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


	$("#btn-buscar").on("click", function(){
		$trigger = $(this);
		var fechaInicio = moment($("#txt-fecha-inicio").val(),"D-mm-Y");
		var fechaFin = moment($("#txt-fecha-fin").val(),"D-mm-Y");
		var ajax_data = {
			"empresa":$("#empresa").val(),
			"periodo":$("#periodo").val(),
			"fechaInicio": "",
			"fechaFin": "",
			"clasificacionBm":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
			"clasificacionBi":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
			"departamento":$("#sl-departamento").val(),
			"estadoFisico":$("#sl-estado-fisico").val(),
			"tipo":"",
		};

		$.ajax({
			url:'src/services/filtradoall.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				limpiarTabla($tabla);
				$trigger.find("i").removeClass("fa-search");
				$trigger.find("i").addClass("fa-refresh fa-spin");
				$trigger.attr("disabled",true);
			},
			success:function(respuesta){
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-search");				
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
						var $row = dummy.clone(true);
						$row.removeClass("dummy");
						//$row.find("td:eq(0) input.chk-estatus").attr("data-id", $datos[i].id);
						$row.find("input:eq(0)").attr("data-id", $datos[i].id);
						$row.find("input:eq(1)").attr("data-id", $datos[i].id);
						$row.find("input:eq(2)").attr("data-id", $datos[i].id);

						$row.find("input:eq(0)").attr("name", "radio-"+$datos[i].id);
						$row.find("input:eq(1)").attr("name", "radio-"+$datos[i].id);
						$row.find("input:eq(2)").attr("name", "radio-"+$datos[i].id);						

						if($datos[i].estatusInventario.id=="1"){
							$row.find("input:eq(0)").attr("checked", true);
							$row.find("input:eq(1)").attr("checked", false);
							$row.find("input:eq(2)").attr("checked", false);
						}else if($datos[i].estatusInventario.id=="2"){
							$row.find("input:eq(1)").attr("checked", true);
							$row.find("input:eq(0)").attr("checked", false);
							$row.find("input:eq(2)").attr("checked", false);
						}else if($datos[i].estatusInventario.id=="3"){
							$row.find("input:eq(0)").attr("checked", false);
							$row.find("input:eq(1)").attr("checked", false);
							$row.find("input:eq(2)").attr("checked", true);
						}
						
						$row.find("td:eq(3)").html($datos[i].folio);
						$row.find("td:eq(4)").html($datos[i].descripcion);
						$row.find("td:eq(5)").html($datos[i].clasificacion.descr);
						$row.find("td:eq(6)").html($datos[i].tipoClasificacion.descr);
						$row.find("td:eq(7)").html($datos[i].departamento.descr);
						$row.find("td:eq(8)").html($datos[i].estadoFisico.descr);
						$row.find("td:eq(9)").html($datos[i].tipoValuacion.descr);
						$row.find("td:eq(10)").html($datos[i].valor);
						if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
							$row.find("td:eq(11)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
						}						
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
                $trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-search");
                $trigger.removeAttr("disabled");
			}
		});
	});
	
	$(".chk-todos").on("click", function(){
		$trigger = $(this);
		var $rows = $tabla.rows().nodes();
		var estatus = $("#sl-tipo-seleccion").val()
		//console.log($rows.length);
		if($trigger.is(":checked")){
			
			for(var i=0;i<$rows.length;i++){
				//$($rows[i]).find("input").filter("[value='"+estatus+"']").prop("checked", true);
				if(estatus==1){
					$($rows[i]).find("input:eq(1)").prop("checked", false);
					$($rows[i]).find("input:eq(2)").prop("checked", false);
					$($rows[i]).find("input:eq(0)").prop("checked", true);
					//console.dir($($rows[i]).find("input:eq(0)").html());	
				}else if(estatus==2){
					$($rows[i]).find("input:eq(0)").prop("checked", false);
					$($rows[i]).find("input:eq(2)").prop("checked", false);
					$($rows[i]).find("input:eq(1)").prop("checked", true);
					//console.dir($($rows[i]).find("input:eq(1)").html());	
				}else if(estatus==3){
					$($rows[i]).find("input:eq(0)").prop("checked", false);
					$($rows[i]).find("input:eq(1)").prop("checked", false);
					$($rows[i]).find("input:eq(2)").prop("checked", true);
					//console.dir($($rows[i]).find("input:eq(2)").html());
				}else{
					console.log("estatus desconocido");
				}
			}
		}				
	});

	$("#sl-tipo-seleccion").on("change", function(){
		$(".chk-todos").prop("checked", false);
	});

	$(".btn-modificar").on("click", function(){
		var $trigger = $(this);
		var $rows = $tabla.rows().nodes();
		var ids = new Array();
		for(var i=1;i<$rows.length;i++){
			var estatus = $($rows[i]).find("input").filter(":checked").val();
			var id = $($rows[i]).find("input").filter(":checked").attr("data-id");
			if(typeof estatus!="undefined"&&estatus!=""){
				ids.push(id+","+estatus);
			}
			/*if($($rows[i]).find("input.chk-estatus").is(":checked")){
				ids.push($($rows[i]).find("input.chk-estatus").attr("data-id"));
			}*/
		}
		if(ids.length>0){
			var ajax_data = {
				"ids":ids.join("|"),
				"periodo":$("#periodo").val()
			};
			$.ajax({
				url:'src/services/updestinv.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){					
					$trigger.attr("disabled", true);
					$trigger.html("<i class='fa fa-refresh fa-spin'></i>&nbsp;Espere ..."); 
				},
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
			                text: 'Ocurrio un error inesperado',
			                type: 'error',
			                hide: true,
			                delay: 2000,
			                styling: 'bootstrap3'
			            });
					}
					$trigger.removeAttr("disabled");
					$trigger.html("GUARDAR CAMBIOS"); 	
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
					$trigger.html("GUARDAR CAMBIOS"); 
				}
			});	
		}else{
			new PNotify({
                title: 'Error',
                text: 'No se ha seleccionado ningun bien',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}		
		
	});

	$(".content-preloader").fadeOut();

});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}

function redirection($element, event){
	event.preventDefault();
	window.location.href="bien.php?id="+$element.attr("data-id");
}