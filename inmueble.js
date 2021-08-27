var settings = new Properties();
settings.init("src/config/settings.xml");

var currentIndex = "";

$(document).on("ready", function(){
	init_sidebar();
	
	$('#sl-clasificacion').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true,
	});

	$("#txt-fecha-ultimo-avaluo").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es",
        clearBtn:false,
        //startDate:moment().format("DD-MM-YYYY")
    });

	$("#txt-valor-capitalizable").on("dblclick", function(){
		$trigger = $(this);
		if(confirm("¿Desea agregar el valor capitalizable? si lo agrega eliminara valor del terreno, valor de construcción y valor total del inmueble")){
			$("#txt-valor-capitalizable").removeAttr("readonly");
			/*$("#txt-valor-terreno").attr("disabled", true);
			$("#txt-valor-construccion").attr("disabled", true);
			$("#txt-valor-inmueble").attr("disabled", true);

			$("#txt-valor-terreno").val("0");
			$("#txt-valor-construccion").val("0");
			$("#txt-valor-inmueble").val("0");*/

		}else{
			$("#txt-valor-capitalizable").attr("readonly", true);
			$("#txt-valor-capitalizable").val("0");
			/*$("#txt-valor-terreno").removeAttr("disabled");
			$("#txt-valor-construccion").removeAttr("disabled");
			$("#txt-valor-inmueble").removeAttr("disabled");*/
		}
	});

    $("#txt-escritura").on("dblclick", function(){
		$trigger = $(this);
		if(!$trigger.is("disabled")){
			$trigger.removeAttr("readonly");
			if($trigger.val()=="NO ESCRITURADO"||$trigger.val()=="NO IDENTIFICADO"||$trigger.val()=="NO APLICA"||$trigger.val()=="NO LEGIBLE"||$trigger.val()=="NO IDENTIFICADO"||$trigger.val()=="GENERICO"){
				$trigger.val("");
				if($trigger.attr("id")=="txt-numero-registro"){
					$("#txt-numero-registro").removeAttr("readonly");
				}
			}
		}
	});

	$("#txt-escritura").each(function(){
    	var valores = [
    		"NO IDENTIFICADO",
    		"NO LEGIBLE",
    		"GENERICO",
    		"NO ESCRITURADO",
    		"NO APLICA"];

    	console.log(valores.length);
    	for(var i=0;i<valores.length;i++){
    		if($(this).val()==valores[i]){
    			$(this).attr("readonly",true);
    			
    		}
    	}
    });

    $(".opts-input").each(function(){
		$(this).on("click", function(event){
	    	event.preventDefault();
	    	var origen = $(this).attr("data-input"); 
	    	if($.trim($(this).attr("data-value"))==""){
	    		$("#"+origen).val("").removeAttr("readonly");
	    	}else{
	    		$("#"+origen).val($(this).attr("data-value")).attr("readonly",true);
	    	}    	
	    });    	
    });

    $("#txt-escritura").on("focusout", function(){
    	event.preventDefault();
    	var	$trigger = $(this);
    	if($.trim($trigger.val())=="NO ESCRITURADO"){
    		$trigger.attr("readonly", true);
    		$("#txt-numero-registro").attr("disabled", true);
    	}else{
    		$trigger.removeAttr("readonly");
    		$("#txt-numero-registro").removeAttr("disabled");
    	}	
    });

    $("#txt-fecha-ultimo-avaluo").on("focusout", function(){
    	$trigger = $(this);
    	console.log($trigger.val());
    	if($trigger.val()=="NO IDENTIFICADO"){
    		$trigger.attr("readonly", true);
    		console.log("ENTRA IF");
    	}else{
    		console.log("ENTRA ELSE");
    		$trigger.removeAttr("readonly");
    	}
    })

	$("#txt-file").on("change", function(){
		console.log("files: "+$(this)[0].files.length);
		for(var x=0; x<$(this)[0].files.length;x++){
			if($(this)[0].files[x]){
				var $dummy = $(".content-item-previews").find(".item-dummy");
				var $item = $dummy.clone(true);
				parseImgToCanvas(x, $(this)[0].files[x], $item, $(".content-item-previews"));
				$dummy.find("img").attr("data-index", (parseInt($dummy.find("img").attr("data-index"))+1) );
			}
		}
		
		$(this).val("");
	});

	$("#btn-listado").on("click", function(){
		window.location.href="inmuebles.php";
	});

	$("#txt-valor-construccion, #txt-valor-terreno").on("keyup", function(){
		var construccion = parseFloat($.trim($("#txt-valor-construccion").val()))
		var terreno = parseFloat($.trim($("#txt-valor-terreno").val()));
		var valor = (!isNaN(construccion)?construccion:0)+(!isNaN(terreno)?terreno:0);
		$("#txt-valor-inmueble").val(valor);
	});

	if($("#txt-escritura").val()=="NO ESCRITURADO"){
		$("#txt-numero-registro").attr("readonly",true);
	}

	if($("#txt-fecha-ultimo-avaluo").val()=="NO IDENTIFICADO"){
		$(this).attr("readonly", true);	
	}	

	if($("#sl-tipo-inmueble").val()=="1"){
		$("#txt-valor-construccion").val("").attr("readonly",true);
	}

	$("#sl-tipo-inmueble").on("change", function(){
		$trigger = $(this);
		if($.trim($trigger.val())!=""){
			$("#sl-clasificacion-inmueble").find("option").hide();
			$("#sl-clasificacion-inmueble").find("option[data-cuenta-contable*='"+$trigger.find("option:selected").attr("data-cuenta-contable")+"']").show();
		}else{
			$("#sl-clasificacion-inmueble").find("option:selected").removeAttr("selected");
			$("#sl-clasificacion-inmueble").val("");
		}
		if($trigger.val()=="1"){
			//TERRENOS
			$("#txt-valor-construccion").val("").attr("readonly",true);
		}else{
			$("#txt-valor-construccion").removeAttr("readonly");
		}
		if($trigger.val()!="0" && $("#sl-clasificacion-inmueble").val()!="0"){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"clasificacion":$("#sl-clasificacion-inmueble").val()
			};
			var folio = "";
			$.ajax({
				url:'src/services/getconsecutivoinmueble.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$trigger.attr("disabled",true);
				},
				success:function(json){
					$trigger.removeAttr("disabled");
					if(json.result=="SUCCESS"){
						folio = $("#sl-clasificacion-inmueble").find("option:selected").attr("data-consecutivo")+"-"+json.data;
					}else if(json.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	 
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	
					}
					$("#folio").val(folio);
					$("#txt-folio").val(folio);	
				},
				error:function(obj,quepaso,otro){
					$trigger.removeAttr("disabled");
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						delay: 2000,
						styling: 'bootstrap3',
					});	
				}
			});	
		}else{
			$("#folio").val("");
			$("#txt-folio").val("");
		}
	});

	$("#sl-clasificacion-inmueble").on("change", function(){
		$trigger = $(this);		
		if($trigger.val()!="0" && $("#sl-tipo-inmueble").val()!="0"){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"clasificacion":$("#sl-clasificacion-inmueble").val()
			};
			var folio = "";
			$.ajax({
				url:'src/services/getconsecutivoinmueble.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				beforeSend:function(){
					$trigger.attr("disabled",true);
				},
				success:function(json){
					$trigger.removeAttr("disabled");
					if(json.result=="SUCCESS"){
						consecutivo = json.data;
						folio = $("#sl-clasificacion-inmueble").find("option:selected").attr("data-consecutivo")+"-"+json.data.padStart(3,"0");
					}else if(json.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: json.desc,
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	 
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	
					}
					$("#consecutivo").val(consecutivo);
					$("#folio").val(folio);
					$("#txt-folio").val(folio);	
				},
				error:function(obj,quepaso,otro){
					$trigger.removeAttr("disabled");
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						delay: 2000,
						styling: 'bootstrap3',
					});	
				}
			});	
		}else{
			$("#folio").val("");
			$("#txt-folio").val("");
		}
	});
    
    $("#btn-guardar").on("click", function(){
    	$trigger = $(this);
    	if($.trim($("#txt-fecha-ultimo-avaluo").val())==""|| $.trim($("#txt-fecha-ultimo-avaluo").val())=="0000-00-00 00:00:00") return notificacion("Error", "error", "Debe especificar la última fecha de avaluo");
    	if($.trim($("#sl-tipo").val())!="0" && $.trim($("#sl-clasificacion-inmueble").val())!="0"){
	    	if($.trim($("#inmueble").val())!=""){
	    		var url_action = "src/services/updinmueble.php";	
	    	}else{
	    		var url_action = "src/services/altainmueble.php";
	    	}
	    	var ajax_data = new FormData();
	    	//var ajax_data = {
	    	ajax_data.append("empresa",$("#empresa").val());
	    	ajax_data.append("folio",$("#folio").val());
	    	ajax_data.append("consecutivo",$("#consecutivo").val());
	    	ajax_data.append("id",$("#inmueble").val());
	    	ajax_data.append("tipo",$("#sl-tipo-inmueble").val());
	    	ajax_data.append("clasificacion",$("#sl-clasificacion-inmueble").val());
	    	ajax_data.append("descripcion",$("#txt-descripcion").val());
	    	ajax_data.append("ubicacion",$("#txt-ubicacion").val());
	    	ajax_data.append("imagenes",$(".item-preview-img:not(.item-dummy)").length);    		
	    	ajax_data.append("medNorte",$("#txt-medida-norte").val());
	    	ajax_data.append("medSur",$("#txt-medida-sur").val());
	    	ajax_data.append("medEste",$("#txt-medida-este").val());
	    	ajax_data.append("medOeste",$("#txt-medida-oeste").val());
	    	ajax_data.append("colNorte",$("#txt-colindancia-norte").val());
	    	ajax_data.append("colSur",$("#txt-colindancia-sur").val());
	    	ajax_data.append("colEste",$("#txt-colindancia-este").val());
	    	ajax_data.append("colOeste",$("#txt-colindancia-oeste").val());
	    	ajax_data.append("superficieTerreno",$("#txt-superficie-terreno").val());
	    	ajax_data.append("superficieConstruccion",$("#txt-superficie-construccion").val());
	    	ajax_data.append("uso",$("#sl-uso").val());
	    	ajax_data.append("aprovechamiento",$("#sl-aprovechamiento").val());
	    	ajax_data.append("adquisicion",$("#sl-medio-adquisicion").val());
	    	ajax_data.append("servAgua",($("#ipt-serv-agua").is(":checked")?'1':'0'));
	    	ajax_data.append("servDrenaje",($("#ipt-serv-drenaje").is(":checked")?'1':'0'));
	    	ajax_data.append("servLuz",($("#ipt-serv-luz").is(":checked")?'1':'0'));
	    	ajax_data.append("servTelefonia",($("#ipt-serv-telefonia").is(":checked")?'1':'0'));
	    	ajax_data.append("servInternet",($("#ipt-serv-internet").is(":checked")?'1':'0'));
	    	ajax_data.append("servGas",($("#ipt-serv-gas").is(":checked")?'1':'0'));
	    	ajax_data.append("escritura",$("#txt-escritura").val());
	    	ajax_data.append("noRegistro",$("#txt-numero-registro").val());
	    	ajax_data.append("cuentaCatastral",$("#txt-cuenta-catastral").val());
	    	ajax_data.append("fechaUltimoAvaluo",($("#txt-fecha-ultimo-avaluo").val()!='NO IDENTIFICADO'?moment($("#txt-fecha-ultimo-avaluo").val(),"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"):"0000-00-00 00:00:00"));
	    	ajax_data.append("gravamen",$("#sl-gravamen").val());
	    	ajax_data.append("valorTerreno",$("#txt-valor-terreno").val());
	    	ajax_data.append("valorConstruccion",$("#txt-valor-construccion").val());
	    	ajax_data.append("valorInmueble",$("#txt-valor-inmueble").val());
	    	ajax_data.append("valorCapitalizable",$("#txt-valor-capitalizable").val());
	    	
	    	ajax_data.append("observaciones",$("#txt-observaciones").val());
	    	
	    	var total_images = $(".item-preview-img:not(.item-dummy)").length;
    		for(var x=1;x<=total_images;x++){
    			ajax_data.append("imagen"+x, dataURItoBlob( $(".item-preview-img:eq("+x+")").find("img").attr("src"), null));
    		}

	    	$.ajax({
    			url:url_action,
    			type:'POST',
    			data:ajax_data,
    			contentType:false,//'application/x-www-form-urlencoded; charset=UTF-8;',
    			processData:false,
    			cache:false,
    			dataType:'json', //json
    			beforeSend:function(){
    				$trigger.attr("disabled", true);
    				$trigger.find("i").addClass("fa-refresh fa-spin");
    				$trigger.find("i").removeClass("fa-check");
    				//$("#btn-guardar span").html(" Enviando ... (0%)");
    			},
    			xhr: function () {
					var xhr = new XMLHttpRequest();
					xhr.upload.onprogress = function(e){
						var percent = '0';
						var percentage = '0%';

						if (e.lengthComputable) {
							percent = Math.round((e.loaded / e.total) * 100);
							percentage = percent + '%';
							//$("#btn-guardar span").html(" Enviando ... ("+percentage+")");
							console.log(percentage);
						}
					};
					return xhr;
				},
    			success:function(response){
    				if(response.result=="SUCCESS"){
    					$("#inmueble").val(response.data.id);
    					new PNotify({
							title: 'Éxito',
							text: response.desc,
							type: 'success',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	
    				}else if(response.result=="FAIL"){
    					new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});	
    				}else{
    					new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});
    				}
    				$trigger.removeAttr("disabled");
    				$trigger.find("i").removeClass("fa-refresh fa-spin");
    				$trigger.find("i").addClass("fa-check");
    			},
    			error:function(obj,quepaso,otro){
    				$trigger.removeAttr("disabled");
    				$trigger.find("i").removeClass("fa-refresh fa-spin");
    				$trigger.find("i").addClass("fa-check");
    				new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: true,
						delay: 2000,
						styling: 'bootstrap3',
					});
    			}
    		});
	    }else{
	    	new PNotify({
				title: 'Error',
				text: 'Debe selccionar el tipo y clasificación del inmueble',
				type: 'error',
				hide: true,
				delay: 2000,
				styling: 'bootstrap3',
			});
	    }	
    });

	$("#div-content-sortable").sortable({
		placeholder: "ui-state-highlight",
		forcePlaceholderSize: true,
		forceHelperSize: true
	});

	$(".btn-rotate-left").on("click", function(){
		$('#image-edit').cropper("rotate",-90);
	});

	$(".btn-rotate-rigth").on("click", function(){
		$('#image-edit').cropper("rotate",90);
	});
	
	$(".btn-save-img").on("click",function(){
		$trigger = $(this);
		$trigger.attr("disabled",true);

		$(".item-preview-img").find("img[data-index="+currentIndex+"]").parent().find("a.waiting-panel").removeClass("hidden");
		//$trigger.html("Cortar <i class='fa fa-refresh fa-spin'></i>");
		var cropper = $('#image-edit').cropper("getCroppedCanvas");
		cropper.toBlob(function(blob){
			var reader = new FileReader();
			reader.onload = function(e){
				//$(".item-preview-img").find("img[data-index="+$('#image-edit').attr("data-index")+"]").attr("src", e.target.result);
				$(".item-preview-img").find("img[data-index="+currentIndex+"]").attr("src", e.target.result);				
				$(".item-preview-img").find("img[data-index="+currentIndex+"]").parent().find("a.waiting-panel").addClass("hidden");
			}
			reader.readAsDataURL(blob);
		});
		console.log("termino...");
		$trigger.removeAttr("disabled");
		//alert("Cerrando... ");
		$("#modal-editar-img").modal("hide");
		
		/*$.when(function(){

		}).done(function(){
			
		});*/					
		
	});

    $(".btn-editar-imagen").on("click", function(){
		var base64 = $(this).parent().find("img").attr("src");
		$("#image-edit").attr("src", base64);
		$("#image-edit").attr("data-index", $(this).parent().find("img").attr("data-index"));
		currentIndex = $(this).parent().find("img").attr("data-index");
		console.log("currentIndex: "+currentIndex);
		//alert($(this).parent().find("img").attr("data-index"));
		$('#modal-editar-img').modal();
	});

	$(".btn-cerrar-imagen").on("click", function(){
		if(confirm("¿Esta seguro de eliminar esta imagen?")){
			$(this).parent().remove();	
		}
	});

	$('#modal-editar-img').on('shown.bs.modal', function () {
		var $cropper = $('#image-edit').cropper({
			viewMode:2,
			responsive:true,
			/*responsive:false,
			width: 400,
			heigth: 300, */
			rotatable:true,
			scalable:true
		});

		$cropper.cropper("replace",$("#image-edit").attr("src"));
	}).on('hidden.bs.modal', function () {
		try{
			//$cropper.cropper("destroy");
			$("#image-edit").attr("src", "");
			$("#image-edit").attr("data-index", "");
			$cropper.destroy();
			$cropper = null;
		}catch(err){
			console.dir(err);
		}
	});

    $(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function notificacion($titulo, $type, $text){
	new PNotify({
		title: $titulo,
		text: $text,
		type: $type,
		hide: true,
		delay: 2000,
		styling: 'bootstrap3',
	});
	return false;
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
}

function parseImgToCanvas(i, file, $item, $contentMain){
	//console.log(file);
	var reader = new FileReader();
	
	const img = new Image();
	const elem = document.createElement('canvas');
	const ctx = elem.getContext('2d');
	var mime = "image/jpeg";
	var quality = 0.05;

	var width = 250;
	var height = 0;
	elem.width = width;
	
	$item.removeClass("item-dummy");
	$item.find("img").removeClass("img-dummy");
	//console.log($item);

	reader.onload = function(e) {
		//$original = contentDummy;
		//$original.find("img").attr("data-index", parseInt(contentDummy.find("img").attr("data-index"))+1); 
		//$item.attr("data-index", i);
		//$item.find("img").attr("data-index", i);
		img.src = e.target.result;
		img.onload = function(){
			if(img.width>width){
				var relacion = img.width/width;
				height = img.height/relacion;
				elem.height = height;
				console.log("(original) width: "+img.width+" - height: "+img.height);
				console.log("relacion: "+relacion);
				console.log("width: "+width+" - height: "+height);
				ctx.drawImage(img, 0, 0, width, height);
				console.dir(ctx);
				$item.find("img").attr("src", ctx.canvas.toDataURL(img, mime, quality));
			}else{
				$item.find("img").attr("src", e.target.result);
			}
			//console.dir($item.find("img"));
			//console.dir(e.target.result);
			console.log("img.onload: "+i);						
		};

		
		$item.find("img").attr("data-saved", "0");
		$contentMain.append($item);
		//console.dir($item);
		console.log("render.onload: "+i);
	
	}
	//se asigna el archivo original al reader como simple disparador del evento reader.onload, 
	//sin embargo este no es el que se muestra
	reader.readAsDataURL(file);		
}

function dataURItoBlob(dataURI, callback) {
    // convert base64 to raw binary data held in a string
    // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
    var byteString = atob(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    // write the ArrayBuffer to a blob, and you're done
    var bb = new Blob([ab]);
    return bb;
}