$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#usuarios tr.dummy");

	var $tabla = $('#permisos').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [3] }
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

	$("#permisos tbody").on("click", "button.btn-eliminar", function(event){
		event.preventDefault();
	});

	$("#btn-agregar").on("click", function(){
		$("#modal-agregar").modal("show");	
	});

	$("#btn-listado").on("click", function(){
		window.location.href="usuarios.php";	
	});

	$("#modal-agregar").on('shown.bs.modal', function(){
	    var ajax_data = {
	    	"usuario":$("#usr").val()
	    }
	    $.ajax({
	    	url:'src/services/listempresas.php',
	    	type:'POST',
	    	data:ajax_data,
	    	contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
	    	dataType:'json', //json
	    	beforeSend:function(){
	    		$("#select-mpio-modal").find("option").not("[value='']").remove();
	    		$("#select-mpio-modal").attr("disabled", true);
	    	},
	    	success:function(json){
	    		var datos = json.data;
	    		for(var i=0;i<datos.length;i++){
	    			var $option = $("<option data-id='' value=''></option>");
	    			$option.val(datos[i].id);
	    			$option.attr("data-id");
	    			$option.html(datos[i].nombre);
	    			$("#select-mpio-modal").append($option);			
	    		}
	    		$("#select-mpio-modal").removeAttr("disabled");		
	    	},
	    	error:function(obj,quepaso,otro){
	    		alert("mensaje: "+quepaso);
	    	}
	    });
	});

	$("#btn-action-guardar").on("click", function(){
		if($.trim($("#select-mpio-modal").val())!="" && $.trim($("#select-rol-modal").val())!=""){
			var ajax_data = {
				"usuario":$("#usr").val(),
				"empresa":$("#select-mpio-modal").val(),
				"rol":$("#select-rol-modal").val()
			};

			$.ajax({
				url:'src/services/altauserxemp.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						alert(respuesta.desc);
					}else if(respuesta.result=="FAIL"){
						alert(respuesta.desc);
					}else{
						alert("Ocurrio un error inesperado");
					}
				},
				error:function(obj,quepaso,otro){
					alert(quepaso);
				}
			});
		}else{
			alert("Debe seleccionar un municipio y un rol");
		}
	});

	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");

});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}