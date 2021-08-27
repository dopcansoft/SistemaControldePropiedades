var settings = new Properties();
settings.init("src/config/settings.xml");

var currentIndex = "";
/********* WEBCAM ***********/
/*var canvas = $('.image-capture'),
    cxt = canvas[0].getContext('2d'),
    video = null;
navigator.getMedia = ( navigator.getUserMedia ||
                       navigator.webkitGetUserMedia ||
                       navigator.mozGetUserMedia);

navigator.getMedia(
   // Restricciones (contraints) *Requerido
   {
      video: true,
      audio: false
   },

   // Funcion de finalizacion (Succes-Callback) *Requerido
   function(localMediaStream) {
      video = document.querySelector('video');
      video.src = window.URL.createObjectURL(localMediaStream);
      video.play();
      video.onloadedmetadata = function(e) {
         // Haz algo con el video aquí.
      };
   },

   // errorCallback *Opcional
   function(err) {
		console.log("Ocurrió el siguiente error: " + err);
   }

);*/
/****** TERMINA WEBCAM ******/

$(document).on("ready", function(){
	init_sidebar();

	/**** RECONOCIMIENTO DE VOZ ****/
	var recognition;
	var recognizing = false;
	if (!('webkitSpeechRecognition' in window)) {
		alert("¡API no soportada!");
		$("#btn-descripcion-listen").remove();
	} else {
		recognition = new webkitSpeechRecognition();
		recognition.lang = "es-MX";
		recognition.continuous = true;
		recognition.interimResults = true;
		//recognition.element = $("#txt-descripcion");
		//recognition.button = $("#btn-descripcion-listen");
		recognition.onstart = function() {
			recognizing = true;
			$(recognition.button).addClass("btn-default").removeClass("btn-success").find("span").html("Detener");			
			console.log("empezando a eschucar");
		}
		recognition.onresult = function(event) {		
			var texto = "";
			for(var i = event.resultIndex; i < event.results.length; i++){
				if(event.results[i].isFinal){
					console.log("reconociendo...");
					texto += event.results[i][0].transcript;
					//$(recognition.element).val(texto);
				}
			}
			$(recognition.element).val(texto);
			//$("#txt-descripcion").val(texto);
		}
		recognition.onerror = function(event) {
		}
		recognition.onend = function() {
			recognizing = false;
			$(recognition.button).addClass("btn-success").removeClass("btn-default").find("span").html("Escuchar");
			//$("txt-descripcion").val()
			//document.getElementById("procesar").innerHTML = "Escuchar";
			console.log("termina eschuca");
			//recognition.start();
			//alert("termino");
		}

	}

	function procesar($buttontrigger, $fieldTarget) {

		if (recognizing == false) {
			recognition.element = $fieldTarget;
			recognition.button = $buttontrigger;
			recognition.start();
			recognizing = true;
		} else {
			recognition.stop();
			recognizing = false;
		}
	}
	
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
	
	$("#txt-file").on("change", function(){
		if($(this)[0].files[0]){
				console.log($(this)[0].files[0]);
				var reader = new FileReader();
				
				const img = new Image();
				const elem = document.createElement('canvas');
				const ctx = elem.getContext('2d');
				var mime = "image/jpeg";
				var quality = 0.3;
				
				width = 400;
				height = 0;
				elem.width = width;
				reader.onload = function(e) {
					$original = $(".item-preview-img:eq(0)");
					$original.find("img").attr("data-index", parseInt($original.find("img").attr("data-index"))+1); 
					$item = $original.clone(true);
					$item.removeClass("item-dummy");

					img.src = e.target.result;
					img.onload = function(){
						if(img.width>width){
							var relacion = img.width/400;
							height = img.height/relacion;
							elem.height = height;
							console.log("(original) width: "+img.width+" - height: "+img.height);
							console.log("relacion: "+relacion);
							console.log("width: "+width+" - height: "+height);
							ctx.drawImage(img, 0, 0, width, height);
							$item.find("img").attr("src", ctx.canvas.toDataURL(img, mime, quality));	
						}else{
							$item.find("img").attr("src", e.target.result);
						}						
					}					
					
					$item.find("img").attr("data-saved", "0");
					$(".content-item-previews").append($item);
					
				}
				reader.readAsDataURL($(this)[0].files[0]);
				//reader.readAsDataURL(data);
				$(this).val("");	
		}
    });

	$(".btn-editar-imagen").on("click", function(){
		var base64 = $(this).parent().find("img").attr("src");
		$("#image-edit").attr("src", base64);
		$("#image-edit").attr("data-index", $(this).parent().find("img").attr("data-index"));
		currentIndex = $(this).parent().find("img").attr("data-index");
		//alert($(this).parent().find("img").attr("data-index"));
		$('#modal-editar-img').modal();
	});

	$(".btn-cerrar-imagen").on("click", function(){
		if(confirm("¿Esta seguro de eliminar esta imagen?")){
			$(this).parent().remove();	
		}
	});

	$('#modal-editar-img').on('shown.bs.modal', function () {
		/*if($("#image-edit").attr("src")==""){
			$("#image-edit").attr("src", );
			data = {
	            Value: result.toDataURL(),
	        };
		}*/

			

		//console.dir($(".preview-img").find("img").attr("src"));
		/*var reader = new FileReader();
		reader.onload = function(e){
			$(".image-edit").find("img").attr("src", e.target.result);
		}
		reader.readAsDataURL($(".preview-img").find("img").files[0]);*/
		
		var $cropper = $('#image-edit').cropper({
			viewMode:2,
			responsive:false,
			width: 400,
			heigth: 300, 
			rotatable:true,
			scalable:true
		});

		$cropper.cropper("replace",$("#image-edit").attr("src"));

		//console.dir($cropper);	
	}).on('hidden.bs.modal', function () {
		try{
			//$cropper.cropper("destroy");
			$("#image-edit").attr("src", "");
			$("#image-edit").attr("data-index", "");
			$cropper.destroy();
			$cropper = null;
		}catch(err){
			//console.dir(err);
		}
	});

	//calcularAniosUso("01-01-2010");
	if($("#inventarioContable").val()=="0"){
		$(".datos-depreciacion").hide();
	}

	$("#btn-descripcion-listen").on("click",function(){
		procesar(this, $("#txt-descripcion"));			
	});

	$("#btn-notas-listen").on("click",function(){
		procesar(this, $("#txt-notas"));
	});

	$("#btn-marca-listen").on("click", function(){
		procesar(this, $("#txt-marca"));
		$("#txt-marca").removeAttr("readonly");
	});

	$("#btn-modelo-listen").on("click", function(){
		procesar(this, $("#txt-modelo"));
		$("#txt-modelo").removeAttr("readonly");
	});

	$("#btn-serie-listen").on("click", function(){
		procesar(this, $("#txt-serie"));
		$("#txt-serie").removeAttr("readonly");
	});

	$("#btn-factura-listen").on("click", function(){
		procesar(this, $("#txt-factura"));
		$("#txt-factura").removeAttr("readonly");
	});

	$("#btn-motor-listen").on("click", function(){
		procesar(this, $("#txt-motor"));
	});

	//console.log(moment("01-03-2010","DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"));

	$('#sl-clasificacion, #sl-departamento, #sl-depreciacion').selectpicker({
	    iconBase: 'fa',
	    tickIcon: 'fa-check'
	});

	$("#txt-fecha-adquisicion").datepicker({
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
    	$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-valuacion").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));   	
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada(anios, $("#txt-depreciacion-periodo").val(), $("#txt-valuacion").val()));	
		console.log(calcDepAcumulada(anios, $("#txt-depreciacion-periodo").val(), $("#txt-valuacion").val()));    	
    	$("#txt-anios-uso").val(anios);
    });

    $("#btn-guardar").click(function(event){
    	if(confirm("¿Está seguro de guardar la información?")){
    		event.preventDefault();
    		$trigger  = $(this);
    		
    		var tipoValuacion = $("#sl-tipo-valuacion").val(); 
    		var valorUma = $("#sl-uma option:selected").attr("data-factor")*$("#sl-uma option:selected").attr("data-valor-diario");
    		var inventarioContable = $("#txt-valuacion").val()>valorUma?"1":"0";
    		var ajax_data = new FormData();
    		ajax_data.append("bien",$("#bien").val());
    		ajax_data.append("empresa",$("#empresa").val());
    		ajax_data.append("periodo",$("#periodo").val());
    		ajax_data.append("departamento",$("#sl-departamento").val());
    		ajax_data.append("tipoClasificacion", $("#sl-clasificacion option:selected").attr("data-tipo"));
    		ajax_data.append("clasificacion", $("#sl-clasificacion").val());
    		ajax_data.append("cuentaContable", $("#txt-cuenta-contable").val());
    		ajax_data.append("cuentaDepreciacion", $("#txt-cuenta-depreciacion").val());
    		ajax_data.append("uma", $("#sl-uma").val());
    		ajax_data.append("valorUma", valorUma);
    		
    		var valuacion = $("#txt-valuacion").val().replace("$","");
    		valuacion = valuacion.replace(",","");
    		ajax_data.append("descripcion", $("#txt-descripcion").val());
    		ajax_data.append("notas", $("#txt-notas").val());
    		ajax_data.append("tipoValuacion",$("#sl-tipo-valuacion").val());
    		ajax_data.append("valuacion",valuacion);
    		ajax_data.append("marca", $("#txt-marca").val());
    		ajax_data.append("modelo", $("#txt-modelo").val());
    		ajax_data.append("serie", $("#txt-serie").val());
    		ajax_data.append("motor", $("#txt-motor").val());
    		ajax_data.append("factura", $("#txt-factura").val());
    		ajax_data.append("fechaAdquisicion", $("#txt-fecha-adquisicion").val()!='NO IDENTIFICADO'?moment($("#txt-fecha-adquisicion").val(),"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"):"0000-00-00 00:00:00");    		
    		ajax_data.append("estadoFisico", $("#sl-estado").val());
    		ajax_data.append("depreciacion", $("#sl-depreciacion").val());
    		ajax_data.append("aniosUso", $("#txt-anios-uso").val());
    		ajax_data.append("origen", $("#sl-origen").val());
    		ajax_data.append("inventarioContable", inventarioContable);
    		ajax_data.append("depreciacion", $("#sl-depreciacion").val());
    		ajax_data.append("depreciacionPeriodo", $("#txt-depreciacion-periodo").val());
    		ajax_data.append("depreciacionAcumulada", $("#txt-depreciacion-acumulada").val());    		
    		ajax_data.append("imagen", $("#txt-file")[0].files[0]);
    		if($.trim($("#txt-file-factura").val())!=""){
    			ajax_data.append("archivoFactura", $("#txt-file-factura")[0].files[0]);	
    		}
    		if($.trim($("#txt-file-poliza").val())!=""){
    			ajax_data.append("archivoPoliza", $("#txt-file-poliza")[0].files[0]);	
    		}
    		
    		ajax_data.append("imagen", $("#txt-file")[0].files[0]);

    		var url=$.trim($("#bien").val())!=""?'src/services/updarticulo.php':'src/services/altaarticulo.php';
    		console.log(ajax_data);
    		$.ajax({
				url:url,
				type:'POST',
                data:ajax_data,
                contentType: false,
                processData:false,
                cache:false,
				dataType:'json', //json
				beforeSend:function(){ 
					$trigger.attr("disabled",true);	
				},
				success:function(response){
					if(response.result=="SUCCESS"){
						$("#txt-file").val("");
						/*if(response.data.imagen!=""){
							console.log("actualiza imagen");
							d = new Date();
							$(".preview-img img").attr("src",settings.prop("system.url")+response.data.imagen+"?"+d.getTime());
							$("#image-edit").attr("src",settings.prop("system.url")+response.data.imagen+"?"+d.getTime());
						}*/

						//$("#bien").val(response.data.id);
						new PNotify({
							title: 'Éxito',
							text: response.desc,
							type: 'success',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});

						$(".preview-img img").attr("src","");
						$(".preview-img").addClass("hidden");
						$("#bien").val("");
			    		$("#sl-departamento").val("0");
			    		$('#sl-departamento').selectpicker('refresh');
			    		$("#sl-clasificacion").val("0");
			    		$('#sl-clasificacion').selectpicker('refresh');
			    		$("#txt-cuenta-contable").val("");
			    		$("#txt-cuenta-depreciacion").val("");
			    		
			    		$("#txt-descripcion").val("");
			    		$("#txt-notas").val("");
			    		$("#sl-tipo-valuacion").val("");
			    		$("#txt-valuacion").val("");
			    		
			    		$("#txt-marca").val("").removeAttr("disabled");
			    		$("#txt-modelo").val("").removeAttr("disabled");
			    		$("#txt-serie").val("").removeAttr("disabled");
			    		$("#txt-motor").val("").removeAttr("disabled");
			    		$("#txt-factura").val("").removeAttr("disabled");
			    		//$("#txt-marca, #txt-modelo, #txt-serie, #txt-motor, #txt-factura").removeAttr("disabled");

			    		$("#txt-fecha-adquisicion").val("");    		
			    		$("#sl-estado").val("0");
			    		$('#sl-estado').selectpicker('refresh');
			    		$("#sl-depreciacion").val("");
			    		$("#txt-anios-uso").val("");
			    		$("#sl-origen").val("");
			    		$("#sl-depreciacion").val("0");
			    		$('#sl-depreciacion').selectpicker('refresh');
			    		$("#txt-depreciacion-periodo").val("");
			    		$("#txt-depreciacion-acumulada").val("");

					}else if(response.result=="FAIL"){
						new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							delay: 2000,
							hide: false,
							styling: 'bootstrap3',
						});
					}else{
						new PNotify({
							title: 'Error',
							text: 'Ocurrio un error desconocido',
							type: 'error',
							delay: 2000,
							hide: false,
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
						hide: false,
						styling: 'bootstrap3',
					});
				}
			});
			$trigger.removeAttr("disabled");
    	}
    });

    $("#txt-valuacion").on("keyup", function(){
    	$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-valuacion").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));  		
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val(), $("#txt-valuacion").val()));
    	if(aplicaValorContable($("#txt-valuacion").val(), $("#sl-uma option:selected").attr("data-factor"), $("#sl-uma option:selected").attr("data-valor-diario") )){
    		$(".datos-depreciacion").show();
    	}else{
    		$(".datos-depreciacion").hide();
    	}
    });

    $("#sl-uma").on("change", function(){
    	if(aplicaValorContable($("#txt-valuacion").val(), $("#sl-uma option:selected").attr("data-factor"), $("#sl-uma option:selected").attr("data-valor-diario") )){
    		$(".datos-depreciacion").show();
    	}else{
    		$(".datos-depreciacion").hide();
    	}
    });

    $("#sl-clasificacion").on("change", function(){
    	console.log("tipo: "+$(this).find("option:selected").attr("data-tipo"));
    	$("#txt-cuenta-contable").val($(this).find("option:selected").attr("data-cc"));
    	$("#txt-cuenta-depreciacion").val($(this).find("option:selected").attr("data-cd"));
    	if($(this).find("option:selected").attr("data-tipo")=="2"){
    		$('#sl-depreciacion').find('[data-tipo=1]').hide();
			$('#sl-depreciacion').find('[data-tipo=2]').show();
    		
    		
    		$("#sl-depreciacion").val("0");	
    		$('#sl-depreciacion').selectpicker('refresh');
    		$("#txt-depreciacion-periodo").val("").attr("disabled",true);	
    		$("#txt-depreciacion-acumulada").val("").attr("disabled",true);	
    	}else{
    		$('#sl-depreciacion').find('[data-tipo=2]').hide();
			$('#sl-depreciacion').find('[data-tipo=1]').show();
			$('#sl-depreciacion').selectpicker('refresh');
			
    		//$("#sl-depreciacion").removeAttr("disabled");	
    		$("#txt-depreciacion-periodo").removeAttr("disabled");	
    		$("#txt-depreciacion-acumulada").removeAttr("disabled");	
    	}
    });

    $("#sl-depreciacion").on("change",function(){
 		$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-valuacion").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));   	
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val(), $("#txt-valuacion").val()));
    });

    $("#txt-anios-uso, #txt-depreciacion-periodo").on("keyup", function(){
		$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val() , $("#txt-valuacion").val()));    	
    });

    $("#txt-marca, #txt-modelo, #txt-serie, #txt-factura, #txt-motor, #txt-fecha-adquisicion").each(function(){
    	var valores = [
    		"NO IDENTIFICADO",
    		"NO LEGIBLE",
    		"GENERICO",
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

    $("#btn-regresar").on("click", function(){
    	window.location.href="inventario.php";
    });

    //$(".content-preloader").fadeOut();
    $(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function aplicaValorContable(valor, valorUma, factorUma){
	if(valor>(valorUma*factorUma)){
		return true;
	}else{
		return false;
	}
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
	if(depAcu>importe){
		depAcu = 1;
	}
	return depAcu;
}

function calcularAniosUso(fecha){
	console.log(fecha);
	var a = moment();
	var b = moment(fecha, "d-M-Y");
	console.log("years: "+a.diff(b, 'years'));
	var res = a.diff(b, 'years');
	console.dir(res);
	return res;	
}

function obtenerUltimo($empresa, $periodo, $clasificacion, $depto, $prefijo, $target){
	var datos = {
		"empresa":$empresa,
		"periodo":$periodo,
		"clasificacion":$clasificacion
	};
	
	$.ajax({
		url:'src/services/getConsecutivo.php',
		type:'POST',
		data:datos,
		contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
		dataType:'json', //json
		success:function(respuesta){
			//$target.val($prefijo+respuesta.data.);
		},
		error:function(obj,quepaso,otro){
			alert("mensaje: "+quepaso);
		}
	});
	return 0;
}