$(document).on("ready", function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	$(".btn-res-general").on("click", function(){
		var $trigger = $(this);
		window.location.href="resdep.php?departamento="+$trigger.attr("data-id-dep")+"&tipo="+$trigger.attr("data-tipo");
	});

	$(".btn-res-general-cont").on("click", function(){
		var $trigger = $(this);
		window.location.href="resdep.php?departamento="+$trigger.attr("data-id-dep")+"&tipo="+$trigger.attr("data-tipo");
	});

	$(".btn-res-general-inst").on("click", function(){
		var $trigger = $(this);
		window.location.href="resdep.php?departamento="+$trigger.attr("data-id-dep")+"&tipo="+$trigger.attr("data-tipo");
	});

	$(".btn-resguardatario").on("click", function(){
		var $trigger = $(this);
		window.location.href="resdepcomp.php?departamento="+$trigger.attr("data-id-dep")+"&tipo="+$trigger.attr("data-tipo");
	});

	$(".btn-labels").on("click", function(){
		var $trigger = $(this);
		window.location.href="etiquetas.php?departamento="+$trigger.attr("data-id-dep");
	});

	$(".btn-fotos").on("click", function(){
		var $trigger = $(this);
		window.location.href="evidfot.php?departamento="+$trigger.attr("data-id-dep");
	});

	var $tabla = $('.tabla-datos').DataTable({
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
});