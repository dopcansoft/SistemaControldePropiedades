$(document).on("ready",function(){
	init_sidebar();

	if($.trim($("#result").val())!="" && $.trim($("#result").val())=="SUCCESS"){
		new PNotify({
			title: 'Éxito',
			text: $("#message").val(),
			type: 'success',
			hide: true,
			delay: 2000,
			styling: 'bootstrap3',
		});
	}

	var dummy = $("#articulos tr.dummy");
	
	/*$('a[data-toggle="tooltip"]').tooltip({
	    placement: 'bottom',
	    html: true
	});*/

	$("a[data-toggle='preview']").on("click", function(){
		var src = $(this).find("img").attr("src");
		$("#modal-img-preview").find("img.img-main").attr("src", src); 
		$("#modal-img-preview").modal("show");
	});

	$("#btn-agregar").on("click", function(){
    	window.location.href="bien.php";
    });

	$('#sl-departamento').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true,
	});

	$('#sl-clasificacion').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true,
	});

	
	var $tabla = $('#articulos').DataTable({
		'order': [[ 0, 'asc' ]],
		'stateSave': true,
		'columnDefs': [
			{ orderable: false, targets: [10] }
		],
		'scrollX': true,
		'columns': [
			{'width':'70px'},
			{'width':'250px'},
			{'width':'200px'},
			{'width':'90px'},
			{'width':'190px'},
			{'width':'100px'},
			{'width':'120px'},
			{'width':'120px'},
			{'width':'50px'},
			{'width':'140px'},
			{'width':'100px'}
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
		var ventimp = window.open('inventario.imp.php?departamento='+$("#sl-departamento").val()+"&estadoFisico="+$("#sl-estado-fisico").val()+"&clasificacionBm="+$("#sl-clasificacion").val());
	});


	//$("#btn-buscar").on("click", function(){
	$("#sl-departamento, #sl-clasificacion, #sl-estado-fisico").on("change", function(){
		$trigger = $(this);
		//var fechaInicio = moment($("#txt-fecha-inicio").val(),"D-mm-Y");
		//var fechaFin = moment($("#txt-fecha-fin").val(),"D-mm-Y");
		var ajax_data = {
			"empresa":$("#empresa").val(),
			"periodo":$("#periodo").val(),
			"fechaInicio": "",
			"fechaFin": "",
			"clasificacionBm":$("#sl-clasificacion option:selected").attr("data-tipo")==1?$("#sl-clasificacion").val():"",
			"clasificacionBi":$("#sl-clasificacion option:selected").attr("data-tipo")==2?$("#sl-clasificacion").val():"",
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
				//$trigger.find("i").removeClass("fa-search");
				//$trigger.find("i").addClass("fa-refresh fa-spin");
				$trigger.attr("disabled",true);
			},
			success:function(respuesta){
				//$trigger.find("i").removeClass("fa-refresh fa-spin");
				//$trigger.find("i").addClass("fa-search");				
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
						//console.log("iterando");
						var $row = dummy.clone(true);
						$row.removeClass("dummy");
						//console.dir($datos[i]);
						$row.attr("data-id", $datos[i].id);
						$row.find("td:eq(0)").html($datos[i].folio);
						$row.find("td:eq(1)").html($datos[i].descripcion);
						$row.find("td:eq(2)").html($datos[i].clasificacion.descr);
						$row.find("td:eq(3)").html($datos[i].tipoClasificacion.descr);
						$row.find("td:eq(4)").html($datos[i].departamento.descr);
						$row.find("td:eq(5)").html($datos[i].estadoFisico.descr);
						$row.find("td:eq(6)").html($datos[i].tipoValuacion.descr);
						$row.find("td:eq(7)").html($datos[i].origen.descr);
						$row.find("td:eq(8)").html($datos[i].valor);
						if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
							$row.find("td:eq(9)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
						}						

						$row.find("td:eq(10)").html($datos[i].clasificacion.cuentaContable);
						$row.find("td:eq(11)").html($datos[i].clasificacion.cuentaDepreciacion);
						var fechaInsert = moment($datos[i].fechaInsert); 
						$row.find("td:eq(12)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
						

						$row.find("td:eq(13) a:eq(0)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(0)").attr("data-id-empresa",$datos[i].empresa.id);
						$row.find("td:eq(13) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
						
						$row.find("td:eq(13) a:eq(1)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
						$row.find("td:eq(13) a:eq(1)").attr("href","bien.php?id="+$datos[i].id);

						$row.find("td:eq(13) a:eq(2)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);
						
						$row.find("td:eq(13) a:eq(3)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(3)").attr("data-id-periodo",$datos[i].periodo.id);

						$row.find("td:eq(13) a:eq(4)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(4)").attr("data-id-empresa",$datos[i].empresa.id);
						$row.find("td:eq(13) a:eq(4)").attr("data-id-periodo",$datos[i].periodo.id);

						$row.find("td:eq(13) a:eq(5)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(5)").attr("data-id-empresa",$datos[i].empresa.id);
						$row.find("td:eq(13) a:eq(5)").attr("data-id-periodo",$datos[i].periodo.id);

						$row.find("td:eq(13) a:eq(6)").attr("data-id",$datos[i].id);
						$row.find("td:eq(13) a:eq(6)").attr("data-id-empresa",$datos[i].empresa.id);
						$row.find("td:eq(13) a:eq(6)").attr("data-id-periodo",$datos[i].periodo.id);
						
						if($("#perfil").val()==1||$("#perfil").val()==3){

						}else{
							$row.find("td:eq(13) a:eq(3)").remove();
							$row.find("td:eq(13) a:eq(2)").remove();
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
                //$trigger.find("i").removeClass("fa-refresh fa-spin");
				//$trigger.find("i").addClass("fa-search");
                $trigger.removeAttr("disabled");
			}
		});
	});	


	$("#articulos tbody").on("click","a.btn-eliminar", function(event){
		event.preventDefault();
		$trigger = $(this);
		if(confirm("¿Está seguro de eliminar el bien seleccionado?")){
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
		if(confirm("¿Está seguro de DUPLICAR el bien seleccionado?")){
			$(".btn-action-duplicar").attr("data-id-bien", id);
			$(".modal-duplicar").modal("show");
		}
	});

	$("#articulos tbody").on("click","a.btn-fotos", function(event){
		event.preventDefault();
		$trigger = $(this);
		var id = $(this).attr("data-id");
		window.location.href="evidfotbien.php?id="+id;
	});

	$("#articulos tbody").on("click","a.btn-resguardo", function(event){
		event.preventDefault();
		$trigger = $(this);
		var id = $(this).attr("data-id");
		window.location.href="fichabien.php?id="+id;
	});

	$("#articulos tbody").on("click","a.btn-etiqueta", function(event){
		event.preventDefault();
		$trigger = $(this);
		var id = $(this).attr("data-id");
		window.location.href="etiqueta.php?id="+id;
	});

	$(".btn-action-duplicar").on("click", function(){
		var veces = $("#txt-copias").val();
		var idbien = $(this).attr("data-id-bien");
		if(typeof veces!='undefined' && veces>0){
			var ajax_data = {
				"id":idbien,
				"periodo":$("#periodo").val(),
				"empresa":$("#empresa").val(),
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
                            title: 'Éxito',
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
										$row.find("td:eq(0)").html($datos[i].folio);
										$row.find("td:eq(1)").html($datos[i].descripcion);
										$row.find("td:eq(2)").html($datos[i].clasificacion.descr);
										$row.find("td:eq(3)").html($datos[i].tipoClasificacion.descr);
										$row.find("td:eq(4)").html($datos[i].departamento.descr);
										$row.find("td:eq(5)").html($datos[i].estadoFisico.descr);
										$row.find("td:eq(6)").html($datos[i].tipoValuacion.descr);
										$row.find("td:eq(7)").html($datos[i].origen.descr);
										$row.find("td:eq(8)").html($datos[i].valor);
										if(typeof $datos[i].imagen!='undefined' && $datos[i].imagen!=""){
											$row.find("td:eq(9)").html("<img src=\""+$datos[i].imagen+"\" style=\"width: 50px;\">");
										}						
										
										$row.find("td:eq(10)").html($datos[i].clasificacion.cuentaContable);
										$row.find("td:eq(11)").html($datos[i].clasificacion.cuentaDepreciacion);
										var fechaInsert = moment($datos[i].fechaInsert); 
										$row.find("td:eq(12)").html(fechaInsert.format("DD/MM/YYYY H:m:s"));
										
										/*$row.find("td:eq(9) a:eq(0)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
										$row.find("td:eq(9) a:eq(0)").attr("href","bien.php?id="+$datos[i].id);
										
										$row.find("td:eq(9) a:eq(1)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(9) a:eq(2)").attr("data-id",$datos[i].id);
										$row.find("td:eq(9) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);*/
										$row.find("td:eq(13) a:eq(0)").attr("data-id",$datos[i].id);
										$row.find("td:eq(13) a:eq(0)").attr("data-id-empresa",$datos[i].empresa.id);
										$row.find("td:eq(13) a:eq(0)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(13) a:eq(1)").attr("data-id",$datos[i].id);
										$row.find("td:eq(13) a:eq(1)").attr("data-id-periodo",$datos[i].periodo.id);
										$row.find("td:eq(13) a:eq(1)").attr("href","bien.php?id="+$datos[i].id);

										$row.find("td:eq(13) a:eq(2)").attr("data-id",$datos[i].id);
										$row.find("td:eq(13) a:eq(2)").attr("data-id-periodo",$datos[i].periodo.id);
										
										$row.find("td:eq(13) a:eq(3)").attr("data-id",$datos[i].id);
										$row.find("td:eq(13) a:eq(3)").attr("data-id-periodo",$datos[i].periodo.id);
										
										if($("#perfil").val()==1||$("#perfil").val()==3){

										}else{
											$row.find("td:eq(13) a:eq(3)").remove();
											$row.find("td:eq(13) a:eq(2)").remove();
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

	var fechaModal = $("#txt-fecha-adquisicion-modal").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es",
        clearBtn:true
        //startDate:moment().format("DD-MM-YYYY")
    }).on("changeDate",function(e){
    	var anios = calcularAniosUso($(this).val());
    	console.log("anios: "+anios);
    	$("#txt-depreciacion-periodo-modal").val(calcDepreciacion($("#txt-valuacion-modal").val(), $("#sl-depreciacion-modal option:selected").attr("data-depreciacion-anual")));   	
    	$("#txt-depreciacion-acumulada-modal").val(calcDepAcumulada(anios, $("#txt-depreciacion-periodo-modal").val(), $("#txt-valuacion-modal").val()));	
		console.log(calcDepAcumulada(anios, $("#txt-depreciacion-periodo-modal").val(), $("#txt-valuacion-modal").val()));    	
    	//NO SE MUESTRA EN EDICION RAPIDA $("#txt-anios-uso").val(anios);
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
					limpiarModal();
					$("#txt-id-modal").val(respuesta.data.folio);
					$("#txt-id-modal").attr("data-counter",respuesta.data.consecutivo);
					$("#txt-id-modal").attr("data-id-empresa", $trigger.attr("data-id-empresa"));
					$("#txt-id-modal").attr("data-id-periodo", $trigger.attr("data-id-periodo"));
					$("#txt-id-modal").attr("data-id-bien", $trigger.attr("data-id"));
					$("#txt-descripcion-modal").val(respuesta.data.descripcion);
					$("#sl-clasificacion-modal").val(respuesta.data.clasificacion.id);
					$("#sl-edo-fisico-modal").val(respuesta.data.estadoFisico.id);
					$("#sl-departamento-modal").val(respuesta.data.departamento.id);
					$("#sl-tipo-valuacion-modal").val(respuesta.data.tipoValuacion.id);
					$("#txt-valor-modal").val(respuesta.data.valor);
					$("#sl-uma-modal").val(respuesta.data.uma.id);
					$("#txt-fecha-adquisicion-modal").val(moment(respuesta.data.fechaAdquisicion).format("DD-MM-YYYY"));
					if(respuesta.data.fechaAdquisicion!="0000-00-00 00:00:00" && respuesta.data.fechaAdquisicion!=""){
						fechaModal.datepicker("setDate",moment(respuesta.data.fechaAdquisicion).format("DD-MM-YYYY"));	
					}else{
						fechaModal.datepicker("setDate",moment().format("DD-MM-YYYY"));
					}
					if(respuesta.data.origen.id!=""){
						$("#sl-origen-modal").val(respuesta.data.origen.id);
					}else{
						$("#sl-origen-modal").val(0);
					}

					
					
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

	$("#sl-departamento-modal").on("change", function(){
		$trigger = $(this);
		var clasificacion = "000";
		if($trigger.val()!="0"){
			console.log("clasificacion: "+$("#sl-clasificacion-modal").val());
			if($("#sl-clasificacion-modal").val()!="0"){
				clasificacion = $("#sl-clasificacion-modal option:selected").attr("data-grupo")+$("#sl-clasificacion-modal option:selected").attr("data-subgrupo")+$("#sl-clasificacion-modal option:selected").attr("data-clase");
			}else{
				clasificacion = "000";
			}
			obtenerUltimo($("#txt-id-modal").attr("data-id-empresa"), $("#txt-id-modal").attr("data-id-periodo"), $("#sl-clasificacion-modal").val(), $trigger.val(), clasificacion+"-"+$("#sl-departamento-modal option:selected").attr("data-clave")+"-", $("#txt-id-modal"));
									
		}else{
			$("#txt-id-modal").val("");
			$("#txt-id-modal").attr("data-counter","0")
		}
	});

	$("#sl-clasificacion-modal").on("change", function(){
		$trigger = $(this);
		if($trigger.val()!="0"){
			obtenerUltimo($("#txt-id-modal").attr("data-id-empresa"), $("#txt-id-modal").attr("data-id-periodo"), $trigger.val(), $("#sl-departamento-modal").val(), $("#sl-clasificacion-modal option:selected").attr("data-grupo")+$("#sl-clasificacion-modal option:selected").attr("data-subgrupo")+$("#sl-clasificacion-modal option:selected").attr("data-clase")+"-"+$("#sl-departamento-modal option:selected").attr("data-clave")+"-", $("#txt-id-modal"));				
		}else{
			$("#txt-id-modal").val("");
			$("#txt-id-modal").attr("data-counter","0");
		}
	});


	$(".btn-guardar-modal").on("click", function(){
		var $trigger = $(this);
		var valorUma = $("#sl-uma-modal option:selected").attr("data-factor")*$("#sl-uma-modal option:selected").attr("data-valor-diario");
		var inventarioContable = $("#txt-valor-modal").val()>valorUma?"1":"0";
		var ajax_data = {
			"empresa":$("#empresa-modal").val(),
			"periodo":$("#periodo-modal").val(),
			"bien":$("#bien-modal").val(),
			"folio":$("#txt-id-modal").val(),
			"consecutivo":$("#txt-id-modal").attr("data-counter"),
			"tipoClasificacion":$("#sl-clasificacion-modal option:selected").attr("data-tipo"),
			"clasificacion":$("#sl-clasificacion-modal").val(),
			"descripcion":$("#txt-descripcion-modal").val(),
			"estadoFisico":$("#sl-edo-fisico-modal").val(),
			"tipoValuacion":$("#sl-tipo-valuacion-modal").val(),
			"valuacion":$("#txt-valor-modal").val(),
			"uma":$("#sl-uma-modal").val(),
			"origen":$("#sl-origen-modal").val(),
			"fechaAdquisicion":moment($("#txt-fecha-adquisicion-modal").val(), "DD-MM-YYYY").format("YYYY-MM-DD")+" 00:00:00",
			"valorUma":valorUma,
			"inventarioContable":inventarioContable,
			"departamento":$("#sl-departamento-modal").val(),
			"depreciacion":$("#sl-depreciacion-modal").val(),
			"depreciacionPeriodo":$("#txt-depreciacion-periodo-modal").val(),
			"depreciacionAcumulada":$("#txt-depreciacion-acumulada-modal").val()
		};

		console.log("fechaAdquisicion valor: "+$("#txt-fecha-adquisicion-modal").val());
		console.log("formated: "+moment($("#txt-fecha-adquisicion-modal").val(), "DD-MM-YYYY").format("YYYY-MM-DD"));

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
					limpiarModal();
					var $rows = $tabla.rows().nodes();
					for(var i=0;i<$rows.length;i++){
						if($($rows[i]).attr("data-id") == respuesta.data.id){
							$($rows[i]).find("td:eq(0)").html(respuesta.data.folio);
							$($rows[i]).find("td:eq(1)").html(respuesta.data.descripcion);
							$($rows[i]).find("td:eq(2)").html(respuesta.data.clasificacion.descr);
							$($rows[i]).find("td:eq(3)").html(respuesta.data.tipoClasificacion.descr);
							$($rows[i]).find("td:eq(4)").html(respuesta.data.departamento.descr);
							$($rows[i]).find("td:eq(5)").html(respuesta.data.estadoFisico.descr);
							$($rows[i]).find("td:eq(6)").html(respuesta.data.tipoValuacion.descr);
							$($rows[i]).find("td:eq(8)").html(respuesta.data.valor);
						}
					}
				
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
						text: 'Ocurrio un error desconocido, intenta más tarde',
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

function calcularAniosUso(fecha){
	console.log(fecha);
	var a = moment();
	var b = moment(fecha, "d-M-Y");
	console.log("years: "+a.diff(b, 'years'));
	var res = a.diff(b, 'years');
	console.dir(res);
	return res;	
}

function calcDepreciacion(importe, porDep){
	var depreciacion = 0;
	if(importe>0 && porDep>0){
		var porDep = porDep/100;  
		depreciacion = importe*porDep;		
	}
	depreciacion = Math.round(depreciacion * 100) / 100;
	return depreciacion;
}

function calcDepAcumulada(aniosUso, depPeriodo, importe){
	var depAcu = aniosUso*depPeriodo;
	var depAcu = Math.round(depAcu * 100) / 100;
	$("#txt-valor-actual").val(importe-depAcu);
	/*if(depAcu>importe){
		depAcu = 1;
	}*/
	return depAcu;
}

function limpiarTabla($tabla){
	$tabla.rows().clear().draw();
}

function redirection($element, event){
	event.preventDefault();
	window.location.href="bien.php?id="+$element.attr("data-id");
}

function limpiarModal(){
	$("#txt-id-modal").val("");
	$("#txt-id-modal").attr("data-counter","");
	$("#txt-id-modal").attr("data-id-empresa", "");
	$("#txt-id-modal").attr("data-id-periodo", "");
	$("#txt-id-modal").attr("data-id-bien", "");
	$("#txt-descripcion-modal").val("");
	$("#sl-clasificacion-modal").val("");
	$("#sl-edo-fisico-modal").val("");
	$("#sl-departamento-modal").val("");
	$("#sl-tipo-valuacion-modal").val("");
	$("#txt-valor-modal").val("");
	$("#sl-uma-modal").val("");
	$("#sl-depreciacion-modal").val("");
	$("#txt-depreciacion-periodo-modal").val("");
	$("#txt-depreciacion-acumulada-modal").val("");
}

function obtenerUltimo($empresa, $periodo, $clasificacion, $depto, $prefijo, $target){
	var datos = {
		"empresa":$empresa,
		"periodo":$periodo,
		"clasificacion":$clasificacion,
		"departamento": $depto
	};
	
	$.ajax({
		url:'src/services/getConsecutivo.php',
		type:'POST',
		data:datos,
		contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
		dataType:'json', //json
		success:function(respuesta){
			$target.val($prefijo+respuesta.data.counter);
			$target.attr("data-counter", respuesta.data.counter);
		},
		error:function(obj,quepaso,otro){
			alert("mensaje: "+quepaso);
		}
	});
	return true;
}