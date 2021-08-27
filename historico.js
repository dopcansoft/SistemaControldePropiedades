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
			{ orderable: false, targets: [4] }
		],
		'scrollX': true,
		'columns': [
			{'width':'207px'},
			{'width':'207px'},
			{'width':'207px'},
			{'width':'207px'},
			{'width':'207px'}
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

	
	$("#articulos tbody").on("click","a.btn-detalle", function(event){
		event.preventDefault();
		$trigger = $(this);
		if($trigger.attr("data-id")!=""){
			window.location.href="conciliacion.php?id="+$trigger.attr("data-id");
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