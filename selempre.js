$(document).ready(function(){
	console.log("test");

	$("#sl-periodo").attr("disabled",true);

	$("#btn-agregar").on("click", function(event){
		event.preventDefault();
		$("#myModal").modal("show");
	});

	/*$("#txt-fecha-cierre").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es",
        clearBtn:true
    });*/

	$("#sl-empresa").on("change", function(){
		$trigger = $(this);
		if($trigger.val()>0){
			var ajax_data = {
				"empresa":$trigger.val()
			};
			$.ajax({
				url:'src/services/getperiodos.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){ 
					$trigger.attr("disabled",true);
					$("#sl-periodo > option").not("[value='0']").remove();
					$("#sl-periodo").attr("disabled",true);
				},
				success:function(respuesta){
					$("#sl-empresa").removeAttr("disabled");
					if(respuesta.result=="SUCCESS"){
						var dummy = $("#sl-periodo").find("option");
						console.log(dummy);
						if(respuesta.data.length>0){
							$("#sl-periodo").removeAttr("disabled");
							for(var i=0;i<respuesta.data.length;i++){
								var $option = dummy.clone(true);
								$option.val(respuesta.data[i].id);
								$option.html(respuesta.data[i].descr);
								console.log($option);
								$("#sl-periodo").append($option);
							}	
						}						
					}else if(respuesta.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: respuesta.desc,
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});			
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
					}	
				},
				error:function(obj,quepaso,otro){
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: false,
						styling: 'bootstrap3',
					});
				}
			});
		}else{

			$("#sl-periodo").val("0").attr("disabled",true);
		}
	});

	$("#btn-agregar-periodo").on("click", function(event){
		event.preventDefault();
		$("#modalPeriodo").modal("show");
	});

	$("#btn-seleccionar").on("click", function(event){
		event.preventDefault();
		$trigger = $(this);
		if($("#sl-empresa").val()>0 && $("#sl-periodo").val()>0 ){
			var ajax_data = {
				"empresa":$("#sl-empresa").val(),
				"periodo":$("#sl-periodo").val()
			};
			$.ajax({
				url:'src/services/selempresa.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$trigger.attr("disabled",true);
					$trigger.html("<i class='fa fa-refresh fa-spin'></i>&nbsp;Espere ..."); 
				},
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						window.location.href=respuesta.link;
					}else if(respuesta.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: respuesta.desc,
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});		
						$trigger.removeAttr("disabled");
						$trigger.html("Seleccionar"); 
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
						$trigger.removeAttr("disabled");
						$trigger.html("Seleccionar");
					}	
				},
				error:function(obj,quepaso,otro){
					$trigger.removeAttr("disabled");
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: false,
						styling: 'bootstrap3',
					});		
				}
			});
			//window.location.href="inventario.php";
		}else{
			new PNotify({
				title: 'Error',
				text: 'Debe seleccionar una empresa y un periodo valido',
				type: 'warning',
				hide: false,
				styling: 'bootstrap3',
			});
		}
	});

	$("#btn-salir").on("click", function(event){
		event.preventDefault();
		if(confirm("¿Está seguro de cerrar su sesión?")){
			window.location.href="src/services/logout.php";
		}
	});

	$(".btn-guardar").on("click",function(){
		if($("#txt-nombre").val()){
			var ajax_data = {
				"nombre":$("#txt-nombre").val(),
				"descripcion":$("#txt-descripcion").val(),
				"idUsuario":$("#id-usuario").val()
			};
			$.ajax({
				url:'src/services/altaempresa.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				success:function(response){
					if($.trim(response.result)=="SUCCESS"){
						$("#myModal").modal("hide");
						$("#sl-empresa").append('<option value="'+response.data.id+'">'+response.data.nombre+'</option>');
						new PNotify({
							title: 'Completado',
							text: response.desc,
							type: 'success',
							hide: false,
							styling: 'bootstrap3',
						});
					}else if($.trim(response.result)=="FAIL"){
						new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
					}
				},
				error:function(obj,quepaso,otro){
					new PNotify({
							title: 'Error',
							text: quepaso,
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
				}
			});
		}else{
			alert("Debe escribir al menos el nombre de la empresa");
		}
	});
});