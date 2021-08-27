$(document).on("ready", function(){
	init_sidebar();
	var test = window.open('','test')
	if(test!=null){	
		test.close();
		tets = null;
	}else{
		test = null;
		new PNotify({
            title: 'Error',
            text: "Favor de habilitar las ventanas emergentes para poder imprimir",
            type: 'error',
            hide: false,
            styling: 'bootstrap3'
        });
	}

	$("#btn-imprimir").on("click", function(){
		var ventimp = window.open('auxiliar_especial_gasto_nocontable_print.php');
	});

	$(".content-preloader").fadeOut();
});