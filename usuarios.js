$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#usuarios tr.dummy");

	var $tabla = $('#usuarios').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [7] }
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

	$("#usuarios tbody").on("click", "button.btn-acceso", function(event){
		event.preventDefault();
		$trigger = $(this);
		window.location.href="listuseremp.php?usuario="+$trigger.attr("data-id-usuario");
	});

	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}