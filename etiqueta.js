$(document).on("ready", function(){
	init_sidebar();

	$(".content-preloader").fadeOut();

	$("#chk-all").on("change", function(){
		var $trigger = $(this);
		var valueAll = true; 
		if(!$trigger.prop("checked")){
			valueAll = false;
		}

		$(".item-label input[type='checkbox']").each(function(){
			$(this).prop("checked", valueAll);
		});
		
	});

	$(".btn-listado").on("click", function(){
		window.location.href="inventario.php";
	});

	$(".btn-imprimir").on("click", function(){
		//window.location.href="printlabelbien.php?id="+$("#id").val();
		var ventimp = window.open("printlabelbien.php?id="+$("#id").val());
		
	});
});