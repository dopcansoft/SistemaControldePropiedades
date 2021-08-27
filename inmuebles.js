$(document).on("ready",function(){
	init_sidebar();

	if($.trim($("#result").val())!="" && $.trim($("#result").val())=="SUCCESS"){
		new PNotify({
			title: 'Éxito',
			text: $("#message").val(),
			type: 'success',
			hide: true,
			delay: 2000,
			styling: 'bootstrap3',
		});
	}

	var $dummy = $("#articulos tr.dummy");
	
	/*$("a[data-toggle='preview']").on("click", function(){
		var src = $(this).find("img").attr("src");
		$("#modal-img-preview").find("img.img-main").attr("src", src); 
		$("#modal-img-preview").modal("show");
	});*/

	$("#btn-agregar").on("click", function(){
    	window.location.href="inmueble.php";
    });

	$('#sl-clasificacion').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true,
	});

	
	var $tabla = $('#articulos').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [8] }
		],
		'scrollX': true,
		'columns': [
			{'width':'70px'},
			{'width':'240px'},
			{'width':'90px'},
			{'width':'220px'},
			{'width':'100px'},
			{'width':'120px'},
			{'width':'120px'},
			{'width':'70px'},
			{'width':'140px'}
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

	
	/*$("#btn-imprimir").on("click", function(){
		var ventimp = window.open('inventario.imp.php?departamento='+$("#sl-departamento").val()+"&estadoFisico="+$("#sl-estado-fisico").val()+"&clasificacionBm="+$("#sl-clasificacion").val());
	});*/

	$("#sl-tipo").on("change", function(){
		$trigger = $(this);
		buscar($dummy, $trigger, $tabla);	
	});

	$("#sl-clasificacion").on("change", function(){
		$trigger = $(this);
		buscar($dummy, $trigger, $tabla);	
	});


	$("#articulos tbody").on("click","a.btn-editar", function(event){
		event.preventDefault();
		$trigger = $(this);
		if($trigger.attr("data-id")!=""){
			window.location.href="inmueble.php?id="+$trigger.attr("data-id");
		}else{
			new PNotify({
                title: 'Error',
                text: 'No se encuentra el id del inmueble',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}		
    });

    $("#articulos tbody").on("click","a.btn-cedula", function(event){
		event.preventDefault();
		$trigger = $(this);
		if($trigger.attr("data-id")!=""){
			window.location.href="fichainmueble.php?id="+$trigger.attr("data-id");
		}else{
			new PNotify({
                title: 'Error',
                text: 'No se encuentra el id del inmueble',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}		
    });

	$("#articulos tbody").on("click","a.btn-eliminar", function(event){
		event.preventDefault();
		$trigger = $(this);
		if(confirm("¿Está seguro de eliminar el bien seleccionado?")){
			var ajax_data = {
                "inmueble":$trigger.attr("data-id"),
                "empresa":$("#empresa").val()
                //,"tipo":$("#sl-tipo-inmueble").val(),
                //"clasificacion":$("#sl-clasificacion-inmueble").val()
            };
            $.ajax({
                url:'src/services/eliminarinmueble.php',
                type:'POST',
                data:ajax_data,
                contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
                dataType:'json', //json
                beforeSend:function(){
                    $trigger.attr("disabled",true);
                    $trigger.find("i").removeClass("fa-trash");
                    $trigger.find("i").addClass("fa-spin fa-refresh");
                },
                success:function(respuesta){
                    console.dir(respuesta);
                    $trigger.removeAttr("disabled");
                    $trigger.find("i").removeClass("fa-spin fa-refresh");
                    $trigger.find("i").addClass("fa-trash");
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
                    $trigger.find("i").removeClass("fa-spin fa-refresh");
                    $trigger.find("i").addClass("fa-trash");
                }
            });	
		}
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

function buscar($dummy, $trigger, $tabla){
	var ajax_data = {
		"empresa":$("#empresa").val(),
		"tipo":$("#sl-tipo").val(),
		"clasificacion":$("#sl-clasificacion").val()
	};
	$.ajax({
		url:'src/services/filtradoinmuebles.php',
		type:'POST',
		data:ajax_data,
		contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
		dataType:'json', //json
		beforeSend:function(){
			$trigger.attr("disabled", true);
			limpiarTabla($tabla);
		},
		success:function(json){
			if(json.result=="SUCCESS"){
				for(var i=0;i<json.data.length;i++){
					//console.dir(json.data[i]);
					var $tr = $dummy.clone(true);
					$tr.removeClass("dummy");
					$tr.find("td:eq(0)").html(json.data[i].folio);
					$tr.find("td:eq(1)").html(json.data[i].descr);
					$tr.find("td:eq(2)").html(json.data[i].tipo.descr);
					$tr.find("td:eq(3)").html(json.data[i].clasificacion.descr);
					$tr.find("td:eq(4)").html(json.data[i].superficieTerreno);
					$tr.find("td:eq(5)").html(json.data[i].superficieConstruccion);
					$tr.find("td:eq(6)").html(json.data[i].fechaUltimoAvaluo);
					$tr.find("td:eq(7)").html(json.data[i].valor);
					$tr.find("td:eq(8)").find("a:eq(0)").attr("data-id", json.data[i].id);
					$tr.find("td:eq(8)").find("a:eq(1)").attr("data-id", json.data[i].id);
					$tr.find("td:eq(8)").find("a:eq(2)").attr("data-id", json.data[i].id);
					//console.log($tr.html());
					$tabla.row.add($tr);
					//$tabla.find("tbody").append($tr);
				}
				$tabla.draw();
			}else{
				new PNotify({
	                title: 'Error',
	                text: 'Ocurrio un error desconocido',
	                type: 'error',
	                delay: 2000,
	                hide: true,
	                styling: 'bootstrap3'
	            });	
			}
			$trigger.removeAttr("disabled");	
		},
		error:function(obj,quepaso,otro){
			$trigger.removeAttr("disabled");
			new PNotify({
                title: 'Error',
                text: quepaso,
                type: 'error',
                delay: 2000,
                hide: true,
                styling: 'bootstrap3'
            });
		}
	});
}