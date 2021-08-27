var settings = new Properties();
settings.init("src/config/settings.xml");

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

		recognition.onstart = function() {
			recognizing = true;
			$("#btn-descripcion-listen").addClass("btn-default").removeClass("btn-success").find("span").html("Detener");			
			console.log("empezando a eschucar");
		}
		recognition.onresult = function(event) {		
			var texto = "";
			for(var i = event.resultIndex; i < event.results.length; i++){
				if(event.results[i].isFinal){
					console.log("reconociendo...");
					texto += event.results[i][0].transcript;
				}
			}	
			$("#txt-descripcion").val(texto);
		}
		recognition.onerror = function(event) {
		}
		recognition.onend = function() {
			recognizing = false;
			$("#btn-descripcion-listen").addClass("btn-success").removeClass("btn-default").find("span").html("Escuchar");
			//$("txt-descripcion").val()
			//document.getElementById("procesar").innerHTML = "Escuchar";
			console.log("termina eschuca");
			recognition.start();
			alert("termino");
		}

	}

	function procesar() {

		if (recognizing == false) {
			recognition.start();
			recognizing = true;
			//document.getElementById("procesar").innerHTML = "Detener";
		} else {
			recognition.stop();
			recognizing = false;
			//document.getElementById("procesar").innerHTML = "Escuchar";
		}
	}

	$('.btn-foto').on("click", function(){
		console.log("tomando imagen");
		
		var canvas = $(".image-capture")[0];
		console.log("canvas width: "+canvas.width);
		console.log("canvas heigth: "+canvas.height);
		cxt.drawImage(video, 0, 0, video.videoWidth, video.videoHeight, 0, 0, canvas.width, canvas.height);

		canvas.toBlob(function(blob){
			var url = URL.createObjectURL(blob);
			console.dir(url);
			var fd = new FormData();
			fd.append("file", new Blob([blob], { "type" : "image/jpeg" }))
			$.ajax({
				url:'src/services/capturaimagen.php',
				type:'POST',
				data:fd,
				contentType: false,
                processData:false,
                cache:false,
				dataType:'json', //json
				success:function(respuesta){
					console.dir(respuesta);	
				},
				error:function(obj,quepaso,otro){
					alert("mensaje: "+quepaso);
				}
			});
		}, "image/jpeg", 0.75);
	});

	$("#txt-file").on("change", function(){
    	var ctx = $(".image-capture")[0].getContext('2d');
		var img = new Image;
		img.onload = function(){
			if(img.height>img.width){
				console.log("alta");
				var por = (($(".image-capture").height()*100)/img.height)/100;
				console.log("image, width: "+img.width+", height: "+img.height);
				console.log("canvas, width: "+(img.width*por)+", height: "+$(".image-capture").height());
				ctx.drawImage(img, 0,0, img.width, img.height, 0,0, (img.width*por), $(".image-capture").height());				
			}else{	
				console.log("ancha");
				var por = (($(".image-capture").width()*100)/img.width)/100;
				if((img.height*por)> $(".image-capture").height()){
					
				}
				console.log("image, width: "+img.width+", height: "+img.height);
				console.log("canvas original, width: "+$(".image-capture").width()+", height: "+$(".image-capture").height());
				console.log("canvas, width: "+$(".image-capture").width()+", height: "+img.height*por);
				ctx.drawImage(img, 0,0, img.width, img.height, 0,0, $(".image-capture").width(), img.height*por);
			}
		}
		img.src = URL.createObjectURL($("#txt-file")[0].files[0]);
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
		//console.dir($cropper.getCroppedCanvas());
		var cropper = $('#image-edit').cropper("getCroppedCanvas");
		console.dir(cropper);		
		cropper.toBlob(function (blob) {
			var formData = new FormData();

			var fuente = $(".preview-img img").attr("src").split("/");
			var nombre = fuente[fuente.length-1];

			formData.append('bien', $("#bien").val());
			formData.append('imagen', blob);
			formData.append('nombre', nombre);

			// Use `jQuery.ajax` method
			$.ajax({
				url:'src/services/updimg.php',
				type:'POST',
                data:formData,
                contentType: false,
                processData:false,
                cache:false,
				beforeSend:function(){ 
						
				},
				success:function(response){
					$trigger.removeAttr("disabled");	
					alert(response.desc);
					if(response.result=="SUCCESS"){
						$("#modal-editar-img").modal("hide");
						console.log(response.data.imagen);
						d = new Date();
						$(".preview-img img").attr("src", response.data.imagen+"?"+d.getTime());
						console.log('Upload success');
					}					
				},
				error:function(obj,quepaso,otro){
					alert(quepaso);
					$trigger.removeAttr("disabled");
					console.log('Upload error');
				}
			});
		});
		//console.dir($cropper.cropper("getCroppedCanvas"));
	});
	
	
	$("#btn-editar-imagen").on("click", function(){
		$('#modal-editar-img').modal();
	});


	$('#modal-editar-img').on('shown.bs.modal', function () {
		var $cropper = $('#image-edit').cropper({
			viewMode:2,
			responsive:true,
			rotatable:true,
			scalable:false
		});

		console.dir($cropper);	
	});

	//calcularAniosUso("01-01-2010");
	if($("#inventarioContable").val()=="0"){
		$(".datos-depreciacion").hide();
	}

	$("#btn-descripcion-listen").on("click",function(){
		if (recognizing == false) {
			recognition.start();
			recognizing = true;
			//document.getElementById("procesar").innerHTML = "Detener";
		} else {
			recognition.stop();
			recognizing = false;
			//document.getElementById("procesar").innerHTML = "Escuchar";
		}				
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
    		
    		ajax_data.append("descripcion", $("#txt-descripcion").val());
    		ajax_data.append("tipoValuacion",$("#sl-tipo-valuacion").val());
    		ajax_data.append("valuacion",$("#txt-valuacion").val());
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
						if(response.data.imagen!=""){
							$(".articulo-imagen").attr("src",settings.prop("system.url")+response.data.imagen);
						}
						$("#bien").val(response.data.id);
						new PNotify({
							title: 'Éxito',
							text: response.desc,
							type: 'success',
							hide: true,
							delay: 2000,
							styling: 'bootstrap3',
						});

						$("#bien").val("");
			    		$("#sl-departamento").val("0");
			    		$('#sl-departamento').selectpicker('refresh');
			    		$("#sl-clasificacion").val("0");
			    		$('#sl-clasificacion').selectpicker('refresh');
			    		$("#txt-cuenta-contable").val("");
			    		$("#txt-cuenta-depreciacion").val("");
			    		
			    		$("#txt-descripcion").val("");
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

   /* $("#txt-fecha-adquisicion").each(function(){
    	var valores = [
    		"0000-00-00 00:00:00"];

    	console.log(valores.length);
    	for(var i=0;i<valores.length;i++){
    		if($(this).val()==valores[i]){
    			$(this).attr("readonly",true);
    		}
    	}
    });*/

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