$(document).on("ready",function(){
	init_sidebar();

	var dummy = $("#articulos tr.dummy");
	
	$("#txt-fecha-inicio").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es"
    }).on("changeDate",function(e){
    	var startDate = $(this).val();
    	var final = moment($("#txt-fecha-fin").val(),"DD-MM-Y");
    	var inicio = moment($("#txt-fecha-inicio").val(),"DD-MM-Y");
    	if($.trim($("#txt-fecha-fin").val())!=""){
    		if(final.format("Y-MM-DD")<inicio.format("Y-MM-DD")){	
    			$("#txt-fecha-fin").datepicker("setDate",$(this).val());	
    			console.log("final es menor");
    		}else{
    			console.log("final es mayor");
    		}
    	}else{
    		$("#txt-fecha-fin").datepicker("setDate",$(this).val());
    	}
    });


	$("#txt-fecha-fin").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es"
    }).on("changeDate", function(e){
    	var final = moment($("#txt-fecha-fin").val(),"DD-MM-Y");
    	var inicio = moment($("#txt-fecha-inicio").val(),"DD-MM-Y");
    	if($.trim($("#txt-fecha-inicio").val())!=""){
    		if(final.format("Y-MM-DD")<inicio.format("Y-MM-DD")){	
    			$("#txt-fecha-inicio").datepicker("setDate",$(this).val());	
    			console.log("final es menor");
    		}else{
    			console.log("final es mayor");
    		}
    	}else{
    		$("#txt-fecha-inicio").datepicker("setDate",$(this).val());
    	}
    });

	var $tabla = $('#articulos').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [11] }
		],
		'scrollX': true,
		'columns': [
			{'width':'50px'},
			{'width':'100px'},
			{'width':'200px'},
			{'width':'200px'},
			{'width':'120px'},
			{'width':'240px'},
			{'width':'150px'},
			{'width':'120px'},
			{'width':'120px'},
			{'width':'90px'},
			{'width':'60px'},
			{'width':'140px'},
			{'width':'140px'},
			{'width':'180px'}
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
		if($.trim($("#txt-fecha-inicio").val())!="" && $.trim($("#txt-fecha-fin").val())!=""){
			var ventimp = window.open('repfechasimp.php?fechaInicio='+moment($("#txt-fecha-inicio").val(),"D-mm-Y").format("Y-mm-DD")+'&fechaFin='+moment($("#txt-fecha-fin").val(),"D-mm-Y").format("Y-mm-DD")+"&tipoFecha="+$("#sl-tipo-fecha").val());
		}else{
			new PNotify({
				title: 'Error',
				text: 'Debe especificar fecha de inicio y fin',
				type: 'error',
				hide: true,
				styling: 'bootstrap3'
            });
		}		
	});

	$("#btn-buscar").on("click", function(){
		$trigger = $(this);
		var fechaInicio = moment($("#txt-fecha-inicio").val(),"D-mm-Y").format("Y-mm-DD");
		var fechaFin = moment($("#txt-fecha-fin").val(),"D-mm-Y").format("Y-mm-DD");
		var tipoFecha = $("#sl-tipo-fecha").val();
		console.log(fechaInicio);
		console.log(fechaFin);
		var ajax_data = {
			"empresa":$("#empresa").val(),
			"periodo":$("#periodo").val(),
			"fechaInicio": fechaInicio,
			"fechaFin": fechaFin,
			"tipoFecha":tipoFecha,
			"clasificacionBm":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
			"clasificacionBi":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
			"departamento":$("#sl-departamento").val(),
			"estadoFisico":$("#sl-estado-fisico").val(),
			"tipo":""
		};

		$.ajax({
			url:'src/services/filtrado.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				limpiarTabla($tabla);
				$trigger.find("i").removeClass("fa-search");
				$trigger.find("i").addClass("fa-refresh fa-spin");
				$trigger.attr("disabled",true);
			},
			success:function(respuesta){
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-search");				
				if(respuesta.result=="SUCCESS"){
					var $datos = respuesta.data;
					if(respuesta.desc!=""){
						new PNotify({
							title: 'Completado',
							text: respuesta.desc,
							type: 'info',
							hide: true,
							styling: 'bootstrap3'
			            });
					}
					for(var i=0;i<respuesta.data.length;i++){
						var $row = dummy.clone(true);
						$row.removeClass("dummy");
						$row.attr("data-id", $datos[i].id);
						$row.find("td:eq(0)").html($datos[i].id);
						$row.find("td:eq(1)").html($datos[i].folio);
						$row.find("td:eq(2)").html($datos[i].descripcion);
						$row.find("td:eq(3)").html($datos[i].clasificacion.descr);
						$row.find("td:eq(4)").html($datos[i].tipoClasificacion.descr);
						$row.find("td:eq(5)").html($datos[i].departamento.descr);
						$row.find("td:eq(6)").html($datos[i].origen.descr);
						$row.find("td:eq(7)").html($datos[i].estadoFisico.descr);
						$row.find("td:eq(8)").html($datos[i].tipoValuacion.descr);
						$row.find("td:eq(9)").html($datos[i].valor);
						if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
							$row.find("td:eq(10)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
						}						

						$row.find("td:eq(11)").html($datos[i].clasificacion.cuentaContable);
						$row.find("td:eq(12)").html($datos[i].clasificacion.cuentaDepreciacion);
						var fechaInsert = moment($datos[i].fechaInsert); 
						$row.find("td:eq(13)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
						
						$tabla.row.add($row);	
					}
					$tabla.draw();
				}else if(respuesta.result=="FAIL"){
					new PNotify({
						title: 'Completado',
						text: respuesta.desc,
						type: 'info',
						hide: true,
						styling: 'bootstrap3'
		            });
				}
				$trigger.removeAttr("disabled");
			},
			error:function(obj,quepaso,otro){
				new PNotify({
                    title: 'Error',
                    text: quepaso,
                    type: 'error',
                    hide: true,
                    delay: 2000,
                    styling: 'bootstrap3'
                });
                $trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-search");
                $trigger.removeAttr("disabled");
			}
		});
	});	


	$("#articulos tbody").on("click","a.btn-eliminar", function(event){
		event.preventDefault();
		$trigger = $(this);
		if(confirm("??Est?? seguro de eliminar el bien seleccionado?")){
			var ajax_data = {
                "id":$trigger.attr("data-id")
            };
            $.ajax({
                url:'src/services/eliminarbien.php',
                type:'POST',
                data:ajax_data,
                contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
                dataType:'json', //json
                beforeSend:function(){
                    $trigger.attr("disabled",true);
                },
                success:function(respuesta){
                    console.dir(respuesta);
                    $trigger.removeAttr("disabled");
                    if(respuesta.result=='SUCCESS'){
                        $trigger.parent().parent().remove();
                        new PNotify({
                            title: 'Eliminado',
                            text: respuesta.desc,
                            type: 'success',
                            hide: true,
                            delay: 2000,
                            styling: 'bootstrap3'
                        });                
                    }else{
                        new PNotify({
                            title: 'Error',
                            text: respuesta.desc,
                            type: 'error',
                            hide: true,
                            delay: 2000,
                            styling: 'bootstrap3'
                        });
                    }    
               	},
                error:function(obj,quepaso,otro){
                    new PNotify({
                        title: 'Error',
                        text: quepaso,
                        type: 'error',
                        delay: 2000,
                        hide: true,
                        styling: 'bootstrap3'
                    });
                    $trigger.removeAttr("disabled");
                }
            });	
		}
	});

	$("#articulos tbody").on("click","a.btn-duplicar", function(event){
		event.preventDefault();
		$trigger = $(this);
		var id = $(this).attr("data-id");
		if(confirm("??Est?? seguro de DUPLICAR el bien seleccionado?")){
			$(".btn-action-duplicar").attr("data-id-bien", id);
			$(".modal-duplicar").modal("show");
		}
	});

	$(".btn-action-duplicar").on("click", function(){
		var veces = $("#txt-copias").val();
		var idbien = $(this).attr("data-id-bien");
		if(typeof veces!='undefined' && veces>0){
			var ajax_data = {
				"id":idbien,
				"periodo":$("#periodo").val(),
				"repeticiones":veces
			};
			$.ajax({
				url:'src/services/duplicarbien.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				success:function(respuesta){
					console.dir(respuesta);
					if(respuesta.result=="SUCCESS"){
						new PNotify({
                            title: '??xito',
                            text: respuesta.desc,
                            type: 'success',
                            hide: true,
                            delay: 2000,
                            styling: 'bootstrap3'
                        });
						var ajax_data = {
							"empresa":$("#empresa").val(),
							"periodo":$("#periodo").val(),
							"fechaInicio": "",
				            "fechaFin": "",
							"clasificacionBM":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
							"clasificacionBI":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
							"departamento":$("#sl-departamento").val(),
							"estadoFisico":$("#sl-estado-fisico").val(),
							"tipo":"",
						};

						$.ajax({
							url:'src/services/filtrado.php',
							type:'POST',
							data:ajax_data,
							contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
							dataType:'json', //json
							beforeSend:function(){
								limpiarTabla($tabla);
								//$trigger.attr("disabled",true);
							},
							success:function(respuesta){
								if(respuesta.result=="SUCCESS"){
									var $datos = respuesta.data;
									for(var i=0;i<respuesta.data.length;i++){
										var $row = dummy.clone(true);
										$row.removeClass("dummy");
										$row.find("td:eq(0)").html($datos[i].descripcion);
										$row.find("td:eq(1)").html($datos[i].clasificacion.descr);
										$row.find("td:eq(2)").html($datos[i].tipoClasificacion.descr);
										$row.find("td:eq(3)").html($datos[i].departamento.descr);
										$row.find("td:eq(4)").html($datos[i].estadoFisico.descr);
										$row.find("td:eq(5)").html($datos[i].tipoValuacion.descr);
										$row.find("td:eq(6)").html($datos[i].valor);
										if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
											$row.find("td:eq(7)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
										}						
										var fechaInsert = moment($datos[i].fechaInsert); 
										$row.find("td:eq(8)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
										
										/*$row.find("td:eq(9) a:eq(0)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
										$row.find("td:eq(9) a:eq(0)").attr("href","bien.php?id="+$datos[i].id);
										
										$row.find("td:eq(9) a:eq(1)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(9) a:eq(2)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);*/
										$row.find("td:eq(9) a:eq(0)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(0)").attr("data-id-empresa",$datos[i].empresa.id);
										$row.find("td:eq(9) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(9) a:eq(1)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
										$row.find("td:eq(9) a:eq(1)").attr("href","bien.php?id="+$datos[i].id);

										$row.find("td:eq(9) a:eq(2)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(9) a:eq(3)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(3)").attr("data-id-periodo",$datos[i].periodo.id);
										
										if($("#perfil").val()==1||$("#perfil").val()==3){

										}else{
											$row.find("td:eq(9) a:eq(3)").remove();
											$row.find("td:eq(9) a:eq(2)").remove();
										}
										$tabla.row.add($row);	
									}
									$tabla.draw();
								}else if(respuesta.result=="FAIL"){
									new PNotify({
										title: 'Completado',
										text: respuesta.desc,
										type: 'info',
										hide: true,
										styling: 'bootstrap3'
						            });
								}
								$trigger.removeAttr("disabled");
							},
							error:function(obj,quepaso,otro){
								new PNotify({
				                    title: 'Error',
				                    text: quepaso,
				                    type: 'error',
				                    hide: true,
				                    delay: 2000,
				                    styling: 'bootstrap3'
				                });
				                $trigger.removeAttr("disabled");
							}
						});

					}else if(respuesta.result=="FAIL"){
						new PNotify({
                            title: 'Error',
                            text: respuesta.desc,
                            type: 'error',
                            hide: true,
                            delay: 2000,
                            styling: 'bootstrap3'
                        });
					}else{
						new PNotify({
                            title: 'Error',
                            text: 'Ocurrio un error desconocido',
                            type: 'error',
                            hide: true,
                            delay: 2000,
                            styling: 'bootstrap3'
                        });
					}
					$(".modal-duplicar").modal("hide");
				},
				error:function(obj,quepaso,otro){
					new PNotify({
                        title: 'Error',
                        text: quepaso,
                        type: 'error',
                        hide: true,
                        delay: 2000,
                        styling: 'bootstrap3'
                    });
				}
			});
		}else{
			new PNotify({
				title: 'Error',
				text: "Debe especificar un numero para dupliar el bien seleccionado",
				delay: 2000,
				type: 'error',
				hide: false,
				styling: 'bootstrap3',
			});
		}
	});

	$("#articulos tbody").on("click", "a.btn-edicion-rapida",function(event){
		event.preventDefault();
		var $trigger = $(this);
		var ajax_data = {
			"empresa":$trigger.attr("data-id-empresa"),
			"periodo":$trigger.attr("data-id-periodo"),
			"id":$trigger.attr("data-id")
		};
		$("#empresa-modal").val($trigger.attr("data-id-empresa"));
		$("#periodo-modal").val($trigger.attr("data-id-periodo"));
		$("#bien-modal").val($trigger.attr("data-id"));
		$.ajax({
			url:'src/services/getArticulo.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				$trigger.find("i").removeClass("fa-pencil");
				$trigger.find("i").addClass("fa-refresh fa-spin");
				$trigger.attr("disabled", true);
			},
			success:function(respuesta){
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-pencil");				
				$trigger.removeAttr("disabled");
				if(respuesta.result=="SUCCESS"){
					$("#txt-descripcion-modal").val(respuesta.data.descripcion);
					$("#sl-clasificacion-modal").val(respuesta.data.clasificacion.id);
					$("#sl-edo-fisico-modal").val(respuesta.data.estadoFisico.id);
					$("#sl-departamento-modal").val(respuesta.data.departamento.id);
					$("#sl-tipo-valuacion-modal").val(respuesta.data.tipoValuacion.id);
					$("#txt-valor-modal").val(respuesta.data.valor);
					$("#sl-uma-modal").val(respuesta.data.uma.id);
					
					console.log("analizando");
					console.log("valor:"+respuesta.data.valor);
					console.log("valorUma:"+($("#sl-uma-modal option:selected").attr("data-factor")*$("#sl-uma-modal option:selected").attr("data-valor-diario")));
					
					if(parseFloat(respuesta.data.valor)<$("#sl-uma-modal option:selected").attr("data-factor")*$("#sl-uma-modal option:selected").attr("data-valor-diario")){
						console.log("es menor");
						$(".div-depreciacion, .div-depreciacion-periodo, .div-depreciacion-acumulada").hide();
					}else{
						$(".div-depreciacion, .div-depreciacion-periodo, .div-depreciacion-acumulada").show();
						console.log(parseFloat(respuesta.data.valor)+" < "+parseFloat(respuesta.data.valorUma));
						console.log("no es menor");
					}
					
					$("#sl-depreciacion-modal").val(respuesta.data.depreciacion.id);
					$("#txt-depreciacion-periodo-modal").val(respuesta.data.depreciacionPeriodo);
					$("#txt-depreciacion-acumulada-modal").val(respuesta.data.depreciacionAcumulada);
					$("#modal-edicion").modal("show");
				}else if(respuesta.result=="FAIL"){
					new PNotify({
						title: 'Error',
						text: respuesta.desc,
						delay: 2000,
						type: 'error',
						hide: true,
						styling: 'bootstrap3',
					});
				}else{
					new PNotify({
						title: 'Error',
						text: "Ocurrio un error desconocido",
						delay: 2000,
						type: 'error',
						hide: true,
						styling: 'bootstrap3',
					});
				}
			},
			error:function(obj,quepaso,otro){
				new PNotify({
					title: 'Error',
					text: quepaso,
					delay: 2000,
					type: 'error',
					hide: true,
					styling: 'bootstrap3',
				});
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-pencil");				
				$trigger.removeAttr("disabled");
			}
		});	
	});

	$(".btn-cancelar").on("click", function(){
		$("#modal-edicion").modal("hide");
	});

	$(".btn-guardar-modal").on("click", function(){
		var $trigger = $(this);
		var valorUma = $("#sl-uma-modal option:selected").attr("data-factor")*$("#sl-uma-modal option:selected").attr("data-valor-diario");
		var inventarioContable = $("#txt-valor-modal").val()>valorUma?"1":"0";
		var ajax_data = {
			"empresa":$("#empresa-modal").val(),
			"periodo":$("#periodo-modal").val(),
			"bien":$("#bien-modal").val(),
			"tipoClasificacion":$("#sl-clasificacion-modal option:selected").attr("data-tipo"),
			"clasificacion":$("#sl-clasificacion-modal").val(),
			"descripcion":$("#txt-descripcion-modal").val(),
			"estadoFisico":$("#sl-edo-fisico-modal").val(),
			"tipoValuacion":$("#sl-tipo-valuacion-modal").val(),
			"valuacion":$("#txt-valor-modal").val(),
			"uma":$("#sl-uma-modal").val(),
			"valorUma":valorUma,
			"inventarioContable":inventarioContable,
			"departamento":$("#sl-departamento-modal").val(),
			"depreciacion":$("#sl-depreciacion-modal").val(),
			"depreciacionPeriodo":$("#txt-depreciacion-periodo-modal").val(),
			"depreciacionAcumulada":$("#txt-depreciacion-acumulada-modal").val()
		};

		$.ajax({
			url:'src/services/updarticulomin.php',
			type:'POST',
			data:ajax_data,
			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
			dataType:'json', //json
			beforeSend:function(){
				$trigger.find("i").removeClass("fa-check");
				$trigger.find("i").addClass("fa-refresh fa-spin");
				$trigger.attr("disabled", true);
			},
			success:function(respuesta){
				if(respuesta.result=="SUCCESS"){
					new PNotify({
						title: 'Completado',
						text: respuesta.desc,
						delay: 2000,
						type: 'success',
						hide: true,
						styling: 'bootstrap3',
					});
					$("#modal-edicion").modal("hide");
					var $rows = $tabla.rows().nodes();
					for(var i=0;i<$rows.length;i++){
						if($($rows[i]).attr("data-id") == respuesta.data.id){
							$($rows[i]).find("td:eq(0)").html(respuesta.data.descripcion);
							$($rows[i]).find("td:eq(1)").html(respuesta.data.clasificacion.descr);
							$($rows[i]).find("td:eq(2)").html(respuesta.data.tipoClasificacion.descr);
							$($rows[i]).find("td:eq(3)").html(respuesta.data.departamento.descr);
							$($rows[i]).find("td:eq(4)").html(respuesta.data.estadoFisico.descr);
							$($rows[i]).find("td:eq(5)").html(respuesta.data.tipoValuacion.descr);
							$($rows[i]).find("td:eq(6)").html(respuesta.data.valor);
						}
					}
					/***** ACTUALIZAR *****/
					/*var ajax_data = {
						"empresa":$("#empresa").val(),
						"periodo":$("#periodo").val(),
						"fechaInicio": "",
			            "fechaFin": "",
						"clasificacionBM":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
						"clasificacionBI":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
						"departamento":$("#sl-departamento").val(),
						"estadoFisico":$("#sl-estado-fisico").val(),
						"tipo":"",
					};

					$.ajax({
						url:'src/services/filtrado.php',
						type:'POST',
						data:ajax_data,
						contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
						dataType:'json', //json
						beforeSend:function(){
							limpiarTabla($tabla);
						},
						success:function(respuesta){
							if(respuesta.result=="SUCCESS"){
								var $datos = respuesta.data;
								for(var i=0;i<respuesta.data.length;i++){
									var $row = dummy.clone(true);
									$row.removeClass("dummy");
									$row.find("td:eq(0)").html($datos[i].descripcion);
									$row.find("td:eq(1)").html($datos[i].clasificacion.descr);
									$row.find("td:eq(2)").html($datos[i].tipoClasificacion.descr);
									$row.find("td:eq(3)").html($datos[i].departamento.descr);
									$row.find("td:eq(4)").html($datos[i].estadoFisico.descr);
									$row.find("td:eq(5)").html($datos[i].tipoValuacion.descr);
									$row.find("td:eq(6)").html($datos[i].valor);
									if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
										$row.find("td:eq(7)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
									}						
									var fechaInsert = moment($datos[i].fechaInsert); 
									$row.find("td:eq(8)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
									
									$row.find("td:eq(9) a:eq(0)").attr("data-id",$datos[i].id);
									$row.find("td:eq(9) a:eq(0)").attr("data-id-empresa",$datos[i].empresa.id);
									$row.find("td:eq(9) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
									
									$row.find("td:eq(9) a:eq(1)").attr("data-id",$datos[i].id);
									$row.find("td:eq(9) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
									$row.find("td:eq(9) a:eq(1)").attr("href","bien.php?id="+$datos[i].id);

									$row.find("td:eq(9) a:eq(2)").attr("data-id",$datos[i].id);
									$row.find("td:eq(9) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);
									
									$row.find("td:eq(9) a:eq(3)").attr("data-id",$datos[i].id);
									$row.find("td:eq(9) a:eq(3)").attr("data-id-periodo",$datos[i].periodo.id);
									
									if($("#perfil").val()==1||$("#perfil").val()==3){

									}else{
										$row.find("td:eq(9) a:eq(3)").remove();
										$row.find("td:eq(9) a:eq(2)").remove();
									}
									$tabla.row.add($row);	
								}
								$tabla.draw();
							}else if(respuesta.result=="FAIL"){
								new PNotify({
									title: 'Completado',
									text: respuesta.desc,
									type: 'info',
									hide: true,
									styling: 'bootstrap3'
					            });
							}
							$trigger.removeAttr("disabled");
						},
						error:function(obj,quepaso,otro){
							new PNotify({
			                    title: 'Error',
			                    text: quepaso,
			                    type: 'error',
			                    hide: true,
			                    delay: 2000,
			                    styling: 'bootstrap3'
			                });
			                $trigger.removeAttr("disabled");
						}
					});*/
					/***** TERMNA ACTUALIZAR ****/

				}else if(respuesta.result=="FAIL"){
					new PNotify({
						title: 'Error',
						text: respuesta.desc,
						delay: 2000,
						type: 'error',
						hide: true,
						styling: 'bootstrap3',
					});
				}else{
					new PNotify({
						title: 'Error',
						text: 'Ocurrio un error desconocido, intenta m??s tarde',
						delay: 2000,
						type: 'error',
						hide: true,
						styling: 'bootstrap3',
					});
				}
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-check");				
				$trigger.removeAttr("disabled");
			},
			error:function(obj,quepaso,otro){
				new PNotify({
					title: 'Error',
					text: quepaso,
					delay: 2000,
					type: 'error',
					hide: true,
					styling: 'bootstrap3',
				});
				$trigger.find("i").removeClass("fa-refresh fa-spin");
				$trigger.find("i").addClass("fa-check");				
				$trigger.removeAttr("disabled");
			}
		});
	});

	//console.dir($("#articulos tbody tr.dummy").find("button:eq(0)"));
	$(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}

function redirection($element, event){
	event.preventDefault();
	window.location.href="bien.php?id="+$element.attr("data-id");
}