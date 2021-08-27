$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#articulos tr.dummy");
	
	var $tabla = $('#articulos').DataTable({
		'order': [[ 1, 'asc' ]],
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

	$("#btn-imprimir").on("click", function(){
		var ventimp = window.open('deptosprint.php');
	});
	
	$("#articulos tbody").on("click", "button.btn-editar",function(event){
		event.preventDefault();
		var $trigger = $(this);
		window.location.href="departamento.php?id="+$trigger.attr("data-id");
	});

	$("#articulos tbody").on("click", "button.btn-editar-asignacion",function(event){
		event.preventDefault();
		var $trigger = $(this);
		window.location.href="asignar.php?id="+$trigger.attr("data-id");
	});

	$(".content-preloader").fadeOut();
});