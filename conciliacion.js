$(document).on("ready", function(){
	init_sidebar();

	$(".panel-scan").hide();

	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");

	$("#sl-departamento").on("change", function(){
		if($.trim($("#sl-departamento").val())!=""){
			var ajax_data = {
				"depto":$("#sl-departamento").val(),
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val()
			}
			$.ajax({
				url:'src/services/getdepto.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$("#bienes tbody").find("tr.no-departamento td p").html("<i class='fa fa-spin fa-refresh'></i>");
					$("#btn-iniciar").attr("disabled", true);
				},
				success:function(response){
					$("#bienes tbody").find("tr.no-departamento td p").find("i").remove();
					$("#bienes tbody").find("tr.no-departamento td p").html("No se ha seleccionado el departamento");
					$("#bienes tbody").find("tr.no-departamento").hide();
					$("#btn-iniciar").removeAttr("disabled");
					if(response.result=="SUCCESS"){
						var dummy = $("#bienes tbody tr.dummy");
						var tabla = $("#bienes tbody");
						var datos = response.data;
						for(var i=0;i<datos.length;i++){
							var item = datos[i];
							var tr = dummy.clone(true);
							tr.removeClass("dummy");
							tr.attr("data-id",item.id);
							tr.find("td:eq(1)").html(item.folio);
							tr.find("td:eq(2)").html(item.descripcion);
							tabla.append(tr);
							//tr.find("td(3)").html(item.descr);
						}
					}else if(response.result == "FAIL"){
						new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });	
					}else{
						new PNotify({
							title: 'Error',
							text: "No se pudieron obtener los bienes del departamento seleccionado",
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });
					}
				},
				error:function(obj,quepaso,otro){
					$("#bienes tbody").find("tr.no-departamento td p").find("i").remove();
					$("#bienes tbody").find("tr.no-departamento td p").html("No se ha seleccionado el departamento");
					$("#bienes tbody").find("tr.no-departamento").hide();
					$("#btn-iniciar").removeAttr("disabled");
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						styling: 'bootstrap3'
		            });		
				}
			});	
		}else{
			//$(".panel-scan").hide();
			$("#bienes tbody").find("tr.no-departamento").show();
			$("#bienes tbody").find("tr").not(".no-departamento").not(".dummy").remove();
		}
	});

	$("#btn-iniciar").on("click", function(){
		$trigger = $(this);
		if($.trim($("#sl-departamento").val())!=""){
			var ajax_data = {
				"usuario":$("#usuario").val(),
				"departamento":$("#sl-departamento").val(),
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val()
			};
			$.ajax({
				url:'src/services/registrarconciliacion.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$trigger.attr("disabled", true);
					$trigger.find("i").removeClass("fa-barcode");
					$trigger.find("i").addClass("fa-spin fa-refresh");
					$("#txt-scan").attr("disabled", true);
				},
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						$("#conciliacion").val(respuesta.data.id);
						$("#txt-scan").removeAttr("disabled");
						$("#txt-scan").focus();	
					}else if(respuesta.result=="FAIL"){
						$trigger.removeAttr("disabled");
						new PNotify({
							title: 'Error',
							text: respuesta.desc,
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });
					}else{
						$trigger.removeAttr("disabled");
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error inesperado',
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });
					}
					$trigger.find("i").addClass("fa-barcode");
					$trigger.find("i").removeClass("fa-spin fa-refresh");
						
				},
				error:function(obj,quepaso,otro){
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						styling: 'bootstrap3'
		            });
		            $trigger.removeAttr("disabled");
					$trigger.find("i").addClass("fa-barcode");
					$trigger.find("i").removeClass("fa-spin fa-refresh");
				}
			});
			$(".panel-scan").show();
			$("#sl-departamento").attr("disabled", true);
			$("#txt-scan").focus();
			
		}else{
			$(".panel-scan").hide();
			$("#sl-departamento").removeAttr("disabled");
			new PNotify({
				title: 'Error',
				text: "Debe seleccionar un departamento",
				type: 'error',
				hide: true,
				styling: 'bootstrap3'
            });	
		}
	});

	$("#txt-scan").on("change", function(){
		if($.trim($("#conciliacion").val())!=""){
			$trigger = $(this);
			var ajax_data = {
				"scan":$trigger.val(),
				"conciliacion":$("#conciliacion").val(),
				"periodo":$("#periodo").val()
			};

			$.ajax({
				url:'src/services/finditem.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$trigger.attr("disabled", true);
				},
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						console.log("id: "+respuesta.data.bien.id);
						var target = $("#bienes tbody").find("tr[data-id='"+respuesta.data.bien.id+"']");
						console.dir(target);
						target.find("td:eq(0)").find("i").removeClass("fa-search");
						target.find("td:eq(0)").find("i").addClass("fa-check");
						target.find("td:eq(0)").find("i").attr("style","color:#5cb85c");
						target.find("td:eq(3)").html(moment().format("DD-MM-YYYY HH:mm"));
						new PNotify({
							title: 'Encontrado',
							text: respuesta.desc,
							type: 'success',
							hide: true,
							styling: 'bootstrap3'
			            });
					}else if(respuesta.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: respuesta.desc,
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });	
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error inesperado',
							type: 'error',
							hide: true,
							styling: 'bootstrap3'
			            });
					}
					$trigger.removeAttr("disabled");
					$trigger.val("");
					$trigger.focus();
				},
				error:function(obj,quepaso,otro){
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						styling: 'bootstrap3'
		            });		
					$trigger.removeAttr("disabled");
				}
			});
		}else{
			new PNotify({
				title: 'Error',
				text: "No se ha detectado el id de conciliación",
				type: 'error',
				hide: true,
				styling: 'bootstrap3'
            });		
		}
	});
});