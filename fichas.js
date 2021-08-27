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

	$(".btn-opt-color").on("click", function(){
		$this = $(this);
		if(!$this.hasClass("btn-checked")){
			$(".btn-opt-color").removeClass("btn-checked");
			$this.addClass("btn-checked");
			console.log("data-index: "+$this.attr("data-index"));
			switch($this.attr("data-index")){
				case '1':
					console.log("index 1");
					$("table.cedula td.header").removeClass("header-azul header-amarillo header-gris");
					$("table.cedula td.header").addClass("header-naranja");
					break;
				case '2':
					console.log("index 2");
					$("table.cedula td.header").removeClass("header-naranja header-amarillo header-gris");
					$("table.cedula td.header").addClass("header-azul");
					break;
				case '3':
					console.log("index 3");
					$("table.cedula td.header").removeClass("header-naranja header-azul header-gris");
					$("table.cedula td.header").addClass("header-amarillo");
					break;
				case '4':
					console.log("index 4");
					$("table.cedula td.header").removeClass("header-naranja header-azul header-amarillo");
					$("table.cedula td.header").addClass("header-gris");
					break;
			}
		}
	});

	$(".btn-imprimir").on("click", function(){
		var ventimp = window.open('cedulasprint.php?c='+$("button.btn-checked").attr("data-index")+'&departamento='+$("#departamento").val()+"&tipo="+$("#tipo").val());
	});

	$(".btn-listado").on("click", function(){
		window.location.href="cedulas.php";
	});

	$(".content-preloader").fadeOut();
});