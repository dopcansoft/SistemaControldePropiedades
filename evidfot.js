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

	$(".btn-imprimir").on("click", function(){
		var ventimp = window.open('evidfotprint.php?departamento='+$("#departamento").val());
	});

	$(".btn-listado").on("click", function(){
		window.location.href="resguardos.php";
	});

	$(".content-preloader").fadeOut();
});