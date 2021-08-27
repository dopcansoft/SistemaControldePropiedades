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
		window.location.href="resguardos.php";
	});

	$(".btn-imprimir").on("click", function(){
		var departamento = $("#departamento").val();
		//var ventimp = window.open("printlabel2.php?departamento="+departamento);
		var data = new Array();
		$(".item-label input[type='checkbox']").each(function(){
			if($(this).prop("checked")){
				data.push({
					"id":$(this).attr("data-id"),
					"folio":$(this).attr("data-folio")
				});
			}
				
		});

		if(data.length>0){
			var allData = {
				"departamento":departamento,
				"data":data
			};

			var $form = $("<form>");
			var $datos = $("<input name='datos' id='datos' type='hidden' />");
			$datos.val(JSON.stringify(allData));
			$form.append($datos);
			$("body").append($form);
			//console.dir($form);
			$form.attr("method","POST");
			$form.attr("action", "printlabel2.php");
			$form.attr("target", "_blank");
			$form.submit();
		}else{
			new PNotify({
                title: 'Error',
                text: 'No selecciono ninguna etiqueta para imprimir',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}
		//window.location.href="printlabel2.php?departamento="+departamento;
	});
});