var settings = new Properties();
settings.init("src/config/settings.xml");

var currentIndex = "";
var valuaciones = new Array();

$(document).on("ready", function(){
	init_sidebar();
	
	$('#sl-clasificacion, #sl-departamento, #sl-depreciacion').selectpicker({
	    showIcon: true,
	    iconBase: 'fa',
	    tickIcon: 'fa-check',
	    liveSearch:true,
	});


	/**** RECONOCIMIENTO DE VOZ ****/
	var recognition;
	var recognizing = false;
	if (!('webkitSpeechRecognition' in window)) {
		console.log("reconocimiento de voz NO activado");
		alert("¡API no soportada!");
		$("#btn-descripcion-listen").remove();
	} else {
		console.log("reconocimiento de voz activado");
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
		if(recognizing == false) {
			recognition.element = $fieldTarget;
			recognition.button = $buttontrigger;
			recognition.start();
			recognizing = true;
		} else {
			recognition.stop();
			recognizing = false;
		}
	}	

	$("#txt-marca, #txt-modelo, #txt-serie, #txt-factura, #txt-motor, #txt-matricula").on("dblclick", function(){
		$trigger = $(this);
		console.log("entra");
		console.log($trigger.is("disabled"));
		if(!$trigger.is("disabled")){
			$trigger.removeAttr("readonly");
			if($trigger.val()=="NO IDENTIFICADO"||$trigger.val()=="NO APLICA"||$trigger.val()=="NO LEGIBLE"||$trigger.val()=="NO IDENTIFICADO"||$trigger.val()=="GENERICO"){
				$trigger.val("");
			}
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
	
	$("#txt-file").on("change", function(){
		console.log("files: "+$(this)[0].files.length);
		for(var x=0; x<$(this)[0].files.length;x++){
			if($(this)[0].files[x]){
				var $dummy = $(".content-item-previews").find(".item-dummy");
				var $item = $dummy.clone(true);
				//$item.removeClass("item-dummy");
				parseImgToCanvas(x, $(this)[0].files[x], $item, $(".content-item-previews"));
				$dummy.find("img").attr("data-index", (parseInt($dummy.find("img").attr("data-index"))+1) );
			}
		}
		
		$(this).val("");
		/*if($(this)[0].files[0]){
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
			//se asigna el archivo original al reader como simple disparador del evento reader.onload, 
			//sin embargo este no es el que se muestra
			reader.readAsDataURL($(this)[0].files[0]);
			$(this).val("");	
		}*/
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

	//calcularAniosUso("01-01-2010");
	if($("#inventarioContable").val()=="0"){
		$(".datos-depreciacion").hide();
	}

	$("#btn-descripcion-listen").on("click",function(){
		procesar($(this), $("#txt-descripcion"));			
	});

	$("#btn-notas-listen").on("click",function(){
		procesar($(this), $("#txt-notas"));
	});

	$("#btn-marca-listen").on("click", function(){
		procesar($(this), $("#txt-marca"));
		$("#txt-marca").removeAttr("readonly");
	});

	$("#btn-modelo-listen").on("click", function(){
		procesar($(this), $("#txt-modelo"));
		$("#txt-modelo").removeAttr("readonly");
	});

	$("#btn-serie-listen").on("click", function(){
		procesar($(this), $("#txt-serie"));
		$("#txt-serie").removeAttr("readonly");
	});

	$("#btn-factura-listen").on("click", function(){
		procesar($(this), $("#txt-factura"));
		$("#txt-factura").removeAttr("readonly");
	});

	$("#btn-motor-listen").on("click", function(){
		procesar($(this), $("#txt-motor"));
	});

	$("#btn-matricula-listen").on("click", function(){
		procesar($(this), $("#txt-matricula"));
	});

	//console.log(moment("01-03-2010","DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"));

	$("#sl-departamento").on("change", function(){
		$trigger = $(this);
		var clasificacion = "000";
		if($trigger.val()!="0"){
			console.log("clasificacion: "+$("#sl-clasificacion").val());
			if($("#sl-clasificacion").val()!="0"){
				clasificacion = $("#sl-clasificacion option:selected").attr("data-grupo")+$("#sl-clasificacion option:selected").attr("data-subgrupo")+$("#sl-clasificacion option:selected").attr("data-clase")+$("#sl-clasificacion option:selected").attr("data-subclase");
			}else{
				clasificacion = "0000";
			}
			obtenerUltimo($("#empresa").val(), $("#periodo").val(), $("#sl-clasificacion").val(), $trigger.val(), clasificacion+"-"+$("#sl-departamento option:selected").attr("data-clave")+"-", $("#txt-id"));
									
		}else{
			$("#txt-id").val("");
			$("#txt-id").attr("data-counter","0");
		}
	});


	var options =  {
		placeholder: '__-__-____',
	  	onKeyPress: function(cep){
			console.log("onKeyPress...");
	  		console.log("length: "+cep.length);
	  		if($.trim(cep.length)==10){
	  			var tempDate = moment(cep,'DD-MM-YYYY');
		  		var tempDateIni = moment("1900-01-01","YYYY-MM-DD");
		  		if(tempDate.isValid() && tempDate.diff(tempDateIni,"years") > 0){
		  			console.log("TRUE");
		  			$("#txt-fecha-adquisicion-modal").removeClass("input-danger");
		    	}else{
		    		console.log("FALSE");
		    		$("#txt-fecha-adquisicion-modal").addClass("input-danger");
		    	}	
	  		}else{
	  			$("#txt-fecha-adquisicion-modal").addClass("input-danger");
	  		}	  		
		}
	}; 	

	$('#txt-fecha-adquisicion-modal').mask('00-00-0000', options);

	$('#myFechaAdquisicion').datetimepicker({
        format: 'DD-MM-YYYY',
        ignoreReadonly: true,
    });

    $("#myFechaAdquisicion").on("dp.change", function(e){
    	console.log("dp.change ...")
    	var cep = $("#txt-fecha-adquisicion-modal").val();
    	if($.trim(cep.length)==10){
  			var tempDate = moment(cep,'DD-MM-YYYY');
	  		var tempDateIni = moment("1900-01-01","YYYY-MM-DD");
	  		if(tempDate.isValid() && tempDate.diff(tempDateIni,"years") > 0){
	  			console.log("TRUE");
	  			$("#txt-fecha-adquisicion-modal").removeClass("input-danger");
	    	}else{
	    		console.log("FALSE");
	    		$("#txt-fecha-adquisicion-modal").addClass("input-danger");
	    	}	
  		}else{
  			$("#txt-fecha-adquisicion-modal").addClass("input-danger");
  		}		
    });

	/*var modalAdquisicion = $("#txt-fecha-adquisicion-modal").datepicker({
        format: 'dd-mm-yyyy',
        multidate:false,
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es",
        endDate:moment($("#cierre").val(),'YYYY-MM-DD').format("DD/MM/YYYY"),
        clearBtn:true        
    }).on("changeDate",function(e){
    	var $uma = getItemUma($("#sl-uma-modal"));
    	var $dep = getItemDepreciacion($("#sl-tipo-depreciacion-modal"));
    	var $valor = $("#txt-valor-modal");
    	var anios = calcularAniosUso($(this).val(), $("#cierre").val());
    	var meses = getMeses(moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), moment($(this).val(),'DD-MM-YYYY').format("YYYY-MM-DD"));
    	var depreciacionPeriodo = calDepreciacionPeriodo($valor.val(), $dep.vidaUtil,moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), $(this).val());
    	var depreciacionAcumulada = calcDepreciacionAcumulada($valor.val(), $dep.vidaUtil, $dep.depreciacionAnual, meses);
    	var valorLibros = getValorLibros($valor, depreciacionAcumulada);
    	$("#txt-contable-modal").val(esContable($uma, $valor)?'CONTABLE':'INSTRUMENTAL');
    	$("#txt-contable-modal").attr("data-inventario-contable",esContable($uma, $valor)?'1':'0');
    	$("#txt-depreciacion-periodo-modal").val(depreciacionPeriodo);
    	$("#txt-depreciacion-acumulada-modal").val(depreciacionAcumulada);
    	$("#txt-valor-actual-modal").val($("#txt-valor-modal").val());
    	$("#txt-valor-libros-modal").val(valorLibros.importe);
    	if(valorLibros.result=="FAIL"){
    		$(".alert").addClass("alert-danger");
    		$(".alert").html(valorLibros.message);
    		$(".alert").show();
    	}else{
    		$(".alert").removeClass("alert-danger");
    		$(".alert").html("");
    		$(".alert").hide();
    	}
    });*/


    $("#txt-fecha-cierre-modal").datepicker({
        format: 'dd-mm-yyyy',
        multidate:false,
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        language: "es",
        endDate:moment($("#cierre").val(),'YYYY-MM-DD').format("DD/MM/YYYY"),
        clearBtn:true        
    }).on("changeDate",function(e){
    	var ini = moment($("#txt-fecha-adquisicion-modal").val(),'DD-MM-YYYY');
		var fin = moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY');	
    	console.log(ini);
    	console.log(fin)
    	console.log("diferencia: "+fin.diff(ini,'days'));
    	if(typeof ini!="undefined" && typeof fin!="undefined" && fin.diff(ini,'days')<0){
    		console.log("entra");
    		console.log("Cierre: "+$("#txt-fecha-cierre-modal").val());
    		$("#txt-fecha-adquisicion-modal").datepicker('update','startDate',moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("DD/MM/YYYY"));
    		$("#txt-fecha-adquisicion-modal").datepicker('update','endDate',moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("DD/MM/YYYY"));
    	}
    	var $uma = getItemUma($("#sl-uma-modal"));
    	var $dep = getItemDepreciacion($("#sl-tipo-depreciacion-modal"));
    	var $valor = $("#txt-valor-modal");
    	var anios = calcularAniosUso($('#txt-fecha-adquisicion-modal').val(), $("#cierre").val());
    	var meses = getMeses(moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), moment($('#txt-fecha-adquisicion-modal').val(),'DD-MM-YYYY').format("YYYY-MM-DD"));
		console.log("Depreciacion: ");
    	console.dir($dep);
    	var depreciacionPeriodo = calDepreciacionPeriodo($valor.val(), $dep.vidaUtil,moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), $("#txt-fecha-adquisicion-modal").val());
		var depreciacionAcumulada = calcDepreciacionAcumulada($valor.val(), $dep.vidaUtil, $dep.depreciacionAnual, meses);
    	var valorLibros = getValorLibros($valor, depreciacionAcumulada);
    	$("#txt-contable-modal").val(esContable($uma, $valor)?'CONTABLE':'INSTRUMENTAL');
    	$("#txt-contable-modal").attr("data-inventario-contable",esContable($uma, $valor)?'1':'0');
    	$("#txt-depreciacion-periodo-modal").val(depreciacionPeriodo);
    	$("#txt-depreciacion-acumulada-modal").val(depreciacionAcumulada);
    	$("#txt-valor-actual-modal").val($("#txt-valor-modal").val());
    	$("#txt-valor-libros-modal").val(valorLibros.importe);
    	if(valorLibros.result=="FAIL"){
    		$(".alert").addClass("alert-danger");
    		$(".alert").html(valorLibros.message);
    		$(".alert").show();
    	}else{
    		$(".alert").removeClass("alert-danger");
    		$(".alert").html("");
    		$(".alert").hide();
    	}
    });

    $("#txt-valor-modal").on("keyup", function(){
    	var size = $("#txt-valor-modal").val().length;
    	var importe = $("#txt-valor-modal").val();
    	console.log("keyup");
    	setTimeout(function(){
    		console.log("setTimeout");
    		if(size==$("#txt-valor-modal").val().length&&importe==$("#txt-valor-modal").val()){
    			console.log("entra disparador if");
    			var $uma = getItemUma($("#sl-uma-modal"));
		    	var $dep = getItemDepreciacion($("#sl-tipo-depreciacion-modal"));
    			var $valor = $("#txt-valor-modal");
		    	var anios = calcularAniosUso($('#txt-fecha-adquisicion-modal').val(), $("#cierre").val());
		    	var meses = getMeses(moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), moment($('#txt-fecha-adquisicion-modal').val(),'DD-MM-YYYY').format("YYYY-MM-DD"));
		    	var depreciacionPeriodo = calDepreciacionPeriodo($valor.val(), $dep.vidaUtil,moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), $("#txt-fecha-adquisicion-modal").val());
		    	var depreciacionAcumulada = calcDepreciacionAcumulada($valor.val(), $dep.vidaUtil, $dep.depreciacionAnual, meses);
		    	var valorLibros = getValorLibros($valor, depreciacionAcumulada);
		    	$("#txt-contable-modal").val(esContable($uma, $valor)?'CONTABLE':'INSTRUMENTAL');
		    	$("#txt-contable-modal").attr("data-inventario-contable",esContable($uma, $valor)?'1':'0');
    	    	$("#txt-depreciacion-periodo-modal").val(depreciacionPeriodo);
		    	$("#txt-depreciacion-acumulada-modal").val(depreciacionAcumulada);
		    	$("#txt-valor-actual-modal").val($("#txt-valor-modal").val());
		    	$("#txt-valor-libros-modal").val(valorLibros.importe);
		    	if(valorLibros.result=="FAIL"){
		    		$(".alert").addClass("alert-danger");
		    		$(".alert").html(valorLibros.message);
		    		$(".alert").show();
		    	}else{
		    		$(".alert").removeClass("alert-danger");
		    		$(".alert").html("");
		    		$(".alert").hide();
		    	}		
    		}	
    	},1500);	
    });

    $("#sl-tipo-depreciacion-modal, #sl-uma-modal").on("change", function(){
    	var $uma = getItemUma($("#sl-uma-modal"));
    	var $dep = getItemDepreciacion($("#sl-tipo-depreciacion-modal"));
    	var $valor = $("#txt-valor-modal");
    	var anios = calcularAniosUso($('#txt-fecha-adquisicion-modal').val(), $("#cierre").val());
    	var meses = getMeses(moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), moment($('#txt-fecha-adquisicion-modal').val(),'DD-MM-YYYY').format("YYYY-MM-DD"));
		console.log("Depreciacion: ");
    	console.dir($dep);
    	var depreciacionPeriodo = calDepreciacionPeriodo($valor.val(), $dep.vidaUtil,moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("YYYY-MM-DD"), $("#txt-fecha-adquisicion-modal").val());
		var depreciacionAcumulada = calcDepreciacionAcumulada($valor.val(), $dep.vidaUtil, $dep.depreciacionAnual, meses);
    	var valorLibros = getValorLibros($valor, depreciacionAcumulada);
    	$("#txt-contable-modal").val(esContable($uma, $valor)?'CONTABLE':'INSTRUMENTAL');
    	$("#txt-contable-modal").attr("data-inventario-contable",esContable($uma, $valor)?'1':'0');
    	$("#txt-depreciacion-periodo-modal").val(depreciacionPeriodo);
    	$("#txt-depreciacion-acumulada-modal").val(depreciacionAcumulada);
    	$("#txt-valor-actual-modal").val($("#txt-valor-modal").val());
    	$("#txt-valor-libros-modal").val(valorLibros.importe);
    	if(valorLibros.result=="FAIL"){
    		$(".alert").addClass("alert-danger");
    		$(".alert").html(valorLibros.message);
    		$(".alert").show();
    	}else{
    		$(".alert").removeClass("alert-danger");
    		$(".alert").html("");
    		$(".alert").hide();
    	}		
    });

    $(".alert").hide();

	$("#btn-guardar-test").on("click", function(event){
    	event.preventDefault();
    	console.log("test");
    	var $form = $("<form>");
		var $result = $("<input type='hidden' id='result' name='result'>");
		var $message = $("<input type='hidden' id='message' name='message'>");
		$result.val("SUCCESS");
		$message.val("Se ");
		$form.append($result);
		$form.append($message);
		$form.attr("method", "POST")
		$form.attr("action", "inventario.php");
		$(document.body).append($form);
		console.dir($form);	
		//$form.submit();
    });

    $(".btn-cancelar-color").on("click", function(){
    	$("#txt-color-modal").val("");
    	$("#modal-agregar-color").modal("hide");
    });

    $(".btn-cancelar-responsable").on("click", function(){
    	$("#txt-titulo-modal").val("");
    	$("#txt-nombre-modal").val("");
    	$("#txt-apellidos-modal").val("");
    	$("#modal-agregar-responsable").modal("hide");
    });

    $("#btn-guardar").click(function(event){
    	event.preventDefault();
    	if(confirm("¿Está seguro de guardar la información?")){
    		$trigger  = $(this);
    		
    		var tipoValuacion = $("#sl-tipo-valuacion").val(); 
    		var valorUma = $("#sl-uma option:selected").attr("data-factor")*$("#sl-uma option:selected").attr("data-valor-diario");
    		var inventarioContable = $("#txt-valuacion").val()>valorUma?"1":"0";
    		var ajax_data = new FormData();
    		ajax_data.append("bien",$("#bien").val());
    		ajax_data.append("empresa",$("#empresa").val());
    		ajax_data.append("periodo",$("#periodo").val());
    		//console.log("imagenes detectadas: "+$(".item-preview-img:not(.item-dummy)").length);
    		ajax_data.append("imagenes",$(".item-preview-img:not(.item-dummy)").length);
    		ajax_data.append("folio",$("#txt-id").val());
    		ajax_data.append("folioAnterior",$("#txt-folio-anterior").val());
    		ajax_data.append("consecutivo",$("#txt-id").attr("data-counter"));
    		ajax_data.append("departamento",$("#sl-departamento").val());
    		ajax_data.append("tipoClasificacion", $("#sl-clasificacion option:selected").attr("data-tipo"));
    		ajax_data.append("clasificacion", $("#sl-clasificacion").val());
    		ajax_data.append("cuentaContable", $("#txt-cuenta-contable").val());
    		ajax_data.append("cuentaDepreciacion", $("#txt-cuenta-depreciacion").val());
    		//ajax_data.append("uma", $("#sl-uma").val());
    		ajax_data.append("valorUma", valorUma);
    		var total_images = $(".item-preview-img:not(.item-dummy)").length;
    		
    		for(var x=1;x<=total_images;x++){
    			ajax_data.append("imagen"+x, dataURItoBlob( $(".item-preview-img:eq("+x+")").find("img").attr("src"), null));
    		}

    		var valuacion = ""; //$("#txt-valuacion").val().replace("$","");
    		valuacion = valuacion.replace(",","");
    		ajax_data.append("descripcion", $("#txt-descripcion").val());
    		ajax_data.append("notas", $("#txt-notas").val());
    		ajax_data.append("marca", $("#txt-marca").val());
    		ajax_data.append("modelo", $("#txt-modelo").val());
    		ajax_data.append("serie", $("#txt-serie").val());
    		ajax_data.append("motor", $("#txt-motor").val());
    		ajax_data.append("factura", $("#txt-factura").val());
    		//ajax_data.append("fechaAdquisicion", $("#txt-fecha-adquisicion").val()!='NO IDENTIFICADO'?moment($("#txt-fecha-adquisicion").val(),"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"):"0000-00-00 00:00:00");    		
    		
    		ajax_data.append("aniosUso", $("#txt-anios-uso").val());
    		ajax_data.append("origen", $("#sl-origen").val());
    		ajax_data.append("inventarioContable", inventarioContable);
    		
    		if(valuaciones.length>0){
    			var first = valuaciones[0];
    			var last = valuaciones[valuaciones.length-1];
    			ajax_data.append("tipoValuacion",last.tipoImporte.id);
	    		ajax_data.append("valuacion",last.valor);
	    		ajax_data.append("depreciacion", last.tipoDepreciacion.id);
	    		ajax_data.append("depreciacionPeriodo", last.depPeriodo);
	    		ajax_data.append("depreciacionAcumulada", last.depAcumulada); 
	    		ajax_data.append("valorAnterior", first.valor);
    			ajax_data.append("depreciacion", last.tipoDepreciacion.id);
    			ajax_data.append("estadoFisico", last.edoFisico.id);
    			ajax_data.append("fechaAdquisicion", moment(first.fechaAdquisicion,"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"));
    			ajax_data.append("uma", last.uma.id);
    		}else{
    			ajax_data.append("tipoValuacion","0");
	    		ajax_data.append("valuacion","0");
	    		ajax_data.append("depreciacion", "0");
	    		ajax_data.append("depreciacionPeriodo", "0");
	    		ajax_data.append("depreciacionAcumulada", "0"); 
	    		ajax_data.append("valorAnterior", "0");
    			ajax_data.append("depreciacion", "0");
    			ajax_data.append("estadoFisico", "0");
    			ajax_data.append("fechaAdquisicion", "0000-00-00 00:00:00");
    			ajax_data.append("uma", "0");
    		}
    		ajax_data.append("responsable", $("#sl-responsable").val());
    		ajax_data.append("bandera", $("#sl-bandera").val());
    		ajax_data.append("color", $("#sl-color").val());
    		ajax_data.append("matricula", $("#txt-matricula").val()); 
    		ajax_data.append("tipoEtiqueta", $("#tipo_etiqueta").val());
    		//ajax_data.append("numero", $("#txt-unico").val());
    		//ajax_data.append("folioUnico", $("#txt-unico").val());
    		
    		if($.trim($("#txt-file-factura").val())!=""){
    			ajax_data.append("archivoFactura", $("#txt-file-factura")[0].files[0]);
    		}
    		if($.trim($("#txt-file-poliza").val())!=""){
    			ajax_data.append("archivoPoliza", $("#txt-file-poliza")[0].files[0]);	
    		}
    		
    		//ajax_data.append("imagen", $("#txt-file")[0].files[0]);

    		for(var x=0; x<valuaciones.length;x++){
    			ajax_data = addValuacionFormData(x, ajax_data, valuaciones[x]);	   
    		}

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
					$("#btn-guardar i").removeClass("fa-check").addClass("fa-refresh fa-spin");
					$("#btn-guardar span").html(" Enviando ... (0%)");	
				},
				xhr: function () {
					var xhr = new XMLHttpRequest();
					xhr.upload.onprogress = function(e){
						var percent = '0';
						var percentage = '0%';

						if (e.lengthComputable) {
							percent = Math.round((e.loaded / e.total) * 100);
							percentage = percent + '%';
							$("#btn-guardar span").html(" Enviando ... ("+percentage+")");
							console.log(percentage);
							//$progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
						}
					};
					return xhr;
				},
				success:function(response){
					if(response.result=="SUCCESS"){
						var $form = $("<form>");
						var $result = $("<input type='hidden' id='result' name='result'>");
						var $message = $("<input type='hidden' id='message' name='message'>");
						$result.val(response.result);
						$message.val(response.desc);
						$form.append($result);
						$form.append($message);
						$form.attr("method", "POST");
						if($.trim($("#origen").val())==""){
				    		$form.attr("action", "inventario.php");
				    		//window.location.href="inventario.php";	
				    	}else if($.trim($("#origen").val())=="BAJAS"){
				    		//window.location.href="inventariobajas.php";
				    		$form.attr("action", "inventariobajas.php");
				    	}

						$(document.body).append($form);
						$form.submit();

						$("#txt-id").val("");
						$("#txt-id").attr("data-counter","");
						$("#txt-file").val("");

						$("#bien").val("");
			    		$("#sl-departamento").val("0");
			    		$('#sl-departamento').selectpicker('refresh');
			    		$("#sl-clasificacion").val("0");
			    		$('#sl-clasificacion').selectpicker('refresh');
			    		$("#txt-cuenta-contable").val("");
			    		$("#txt-cuenta-depreciacion").val("");
			    		
			    		$("#txt-descripcion").val("");
			    		$("#txt-notas").val("");
			    		$("#sl-tipo-valuacion").val("1");
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
			    		$("#txt-file-factura").val("");
			    		$("#txt-file-poliza").val("");
			    		
			    		$(".item-preview-img:not(.item-dummy)").remove();

					}else if(response.result=="FAIL"){
						$("#btn-guardar i").removeClass("fa-refresh fa-spin").addClass("fa-check");
						$("#btn-guardar span").html(" Guardar");
						$trigger.removeAttr("disabled");
						new PNotify({
							title: 'Error',
							text: response.desc,
							type: 'error',
							delay: 2000,
							hide: false,
							styling: 'bootstrap3',
						});
					}else{
						$("#btn-guardar i").removeClass("fa-refresh fa-spin").addClass("fa-check");
						$("#btn-guardar span").html(" Guardar");
						$trigger.removeAttr("disabled");
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
					$("#btn-guardar i").removeClass("fa-refresh fa-spin").addClass("fa-check");
					$("#btn-guardar span").html(" Guardar");
					$trigger.removeAttr("disabled");
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

    /*$(".btn-agregar-valor").on("click", function(){

    });*/

    function SelectText(element) {
        var doc = document;
        if (doc.body.createTextRange) {
            var range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection) {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    function b64toBlob(b64Data, contentType, sliceSize) {
		contentType = contentType || '';
		sliceSize = sliceSize || 512;

		var byteCharacters = atob(b64Data);
		var byteArrays = [];

		for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
		var slice = byteCharacters.slice(offset, offset + sliceSize);

		var byteNumbers = new Array(slice.length);
		for (var i = 0; i < slice.length; i++) {
		  byteNumbers[i] = slice.charCodeAt(i);
		}

		var byteArray = new Uint8Array(byteNumbers);

		byteArrays.push(byteArray);
		}

		var blob = new Blob(byteArrays, {type: contentType});
		return blob;
	}

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

    	$trigger = $(this);
		if($trigger.val()!="0"){
			obtenerUltimo($("#empresa").val(), $("#periodo").val(), $trigger.val(), $("#sl-departamento").val(), $("#sl-clasificacion option:selected").attr("data-grupo")+$("#sl-clasificacion option:selected").attr("data-subgrupo")+$("#sl-clasificacion option:selected").attr("data-clase")+$("#sl-clasificacion option:selected").attr("data-subclase")+"-"+$("#sl-departamento option:selected").attr("data-clave")+"-", $("#txt-id"));				
		}else{
			$("#txt-id").val("");
			$("#txt-id").attr("data-counter","0");
		}
    });

    $("#sl-depreciacion").on("change",function(){
 		$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-valuacion").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));   	
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val(), $("#txt-valuacion").val()));
    });

    $("#txt-anios-uso, #txt-depreciacion-periodo").on("keyup", function(){
		$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val() , $("#txt-valuacion").val()));    	
    });

    $("#txt-marca, #txt-modelo, #txt-serie, #txt-factura, #txt-motor, #txt-fecha-adquisicion-modal, #txt-matricula").each(function(){
    	var valores = [
    		"NO IDENTIFICADO",
    		"NO LEGIBLE",
    		"GENERICO",
    		"NO APLICA"];
    	//console.log(valores.length);
    	for(var i=0;i<valores.length;i++){
    		if($(this).val()==valores[i]){
    			$(this).attr("readonly",true);
    		}
    	}
    });

    $(".dm-color .opts-item").each(function(){
    	$(this).on("click", function(event){
    		event.preventDefault();
    		if($.trim($(this).attr("data-value"))=="AGREGAR"){
    			$('#modal-agregar-color').modal();
    		}
    	});
    });

    $(".dm-responsable .opts-item").each(function(){
    	$(this).on("click", function(event){
    		event.preventDefault();
    		if($.trim($(this).attr("data-value"))=="AGREGAR"){
    			$('#modal-agregar-responsable').modal();
    		}
    	});
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

    $(".dm-fecha-cierre .opts-item").each(function(){
		$(this).on("click", function(event){
	    	event.preventDefault();
	    	$trigger = $(this);	    
	    	console.log($trigger.attr("data-instruction"));
	    	console.log($("#"+$trigger.attr("data-input")).is(":disabled"));
	    		
	    	if($.trim($trigger.attr("data-instruction"))=="EDITAR" && $("#"+$trigger.attr("data-input")).is(":disabled") ){
	    		$("#"+$trigger.attr("data-input")).removeAttr("disabled");
	    		console.log($("#"+$trigger.attr("data-input")).is(":disabled"));
	    	}else{
	    		$("#"+$trigger.attr("data-input")).attr("disabled", true);
	    	}    	
	    });    	
    });


    $(".btn-agregar-color").on("click", function(){
    	var $trigger = $(this);
    	var ajax_data = {
    		"descr":$("#txt-color-modal").val(),
    		"empresa":$("#empresa").val()
    	};
    	if($.trim($("#txt-color-modal").val())!=""){
    		$.ajax({
    			url:'src/services/altacolor.php',
    			type:'POST',
    			data:ajax_data,
    			contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
    			dataType:'json', //json
    			success:function(respuesta){
    				if(respuesta.result=="SUCCESS"){
    					new PNotify({
							title: 'Exito',
							text: respuesta.desc,
							delay: 2000,
							type: 'success',
							hide: false,
							styling: 'bootstrap3',
						});
    					var $dummy = $("#sl-color").find("option[value='']");
    					var $option = $dummy.clone(true);
    					$option.val(respuesta.data.id);
    					$option.html(respuesta.data.descr);
    					$option.attr("selected",true);
    					$("#sl-color").append($option);
    					$("#txt-color-modal").val("");
    					$("#modal-agregar-color").modal("hide");  					
    				}else{
    					new PNotify({
							title: 'Error',
							text: respuesta.desc,
							delay: 2000,
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
						delay: 2000,
						type: 'error',
						hide: false,
						styling: 'bootstrap3',
					});
    			}
    		});
    	}
    });


    $(".btn-agregar-responsable").on("click", function(){
    	var $trigger = $(this);
    	if($.trim($("#txt-nombre-modal").val())!="" && $.trim($("#txt-apellidos-modal").val())!=""){
			var ajax_data = {
				"empresa":$("#empresa").val(),
				"periodo":$("#periodo").val(),
				"responsable":"",
				"nombre":$("#txt-nombre-modal").val(),
				"apellidos":$("#txt-apellidos-modal").val(),
				"titulo":$("#txt-titulo-modal").val()
			};
			$.ajax({
				url:'src/services/guardaresponsable.php',
				type:'POST',
				data:ajax_data,
				contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
				dataType:'json', //json
				success:function(respuesta){
					if(respuesta.result=="SUCCESS"){
						respuesta.data;
						$("#sl-responsable").append("<option value='"+respuesta.data.id+"'>"+respuesta.data.titulo+" "+respuesta.data.nombre+" "+respuesta.data.apellido+"</option>");
						$("#sl-responsable").find("option[value='"+respuesta.data.id+"']").attr("selected",true);
						$("#txt-titulo-modal").val("");
				    	$("#txt-nombre-modal").val("");
				    	$("#txt-apellidos-modal").val("");
				    	$("#modal-agregar-responsable").modal("hide");
						new PNotify({
			                title: 'Éxito',
			                text: respuesta.desc,
			                type: 'success',
			                hide: true,
			                delay: 2000,
			                styling: 'bootstrap3'
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
                text: 'Debe escribir los datos del responsable',
                type: 'error',
                hide: true,
                delay: 2000,
                styling: 'bootstrap3'
            });
		}
    });

    $("#btn-regresar").on("click", function(){
    	if($.trim($("#origen").val())==""){
    		window.location.href="inventario.php";	
    	}else if($.trim($("#origen").val())=="BAJAS"){
    		window.location.href="inventariobajas.php";
    	}    	
    });


    $(".btn-agregar-valor").on("click", function(){
		$("#modal-agregar-valor").modal("show");
		$("#current-index").val("");
		$("#sl-tipo-valuacion-modal").find("option").attr("disabled",true);
    	$("#sl-tipo-valuacion-modal").find("option").removeAttr("selected");

		if(valuaciones.length>0){
			$("#sl-tipo-valuacion-modal").find("option:not([value='1'])").removeAttr("disabled",true);
    		$("#sl-tipo-valuacion-modal").find("option:not([value='1'])").removeAttr("selected");
    	}else{
			$("#sl-tipo-valuacion-modal").find("option[value='1']").removeAttr("disabled",true);
    		$("#sl-tipo-valuacion-modal").find("option[value='1']").removeAttr("selected");
			$("#sl-tipo-valuacion-modal").find("option[value='2']").removeAttr("disabled",true);
    		$("#sl-tipo-valuacion-modal").find("option[value='2']").removeAttr("selected");
			/*$("#sl-tipo-valuacion-modal option").each(function(index, $item){
    			if($.trim($($item).val())!="1"||$.trim($($item).val())!="2"){
    				$($item).attr("disabled",true);
    				$($item).removeAttr("selected");
    			}else{
    				$($item).removeAttr("disabled",true);
    			}
    		});*/
		}
		/*if(valuaciones.length<1){
    		$("#sl-tipo-valuacion-modal option").each(function(index, $item){
    			if($($item).val()<"1"){
    				$($item).attr("disabled",true);
    				$($item).removeAttr("selected");
    			}else{
    				$($item).removeAttr("disabled",true);
    			}
    		});
    	}else{
    		$("#sl-tipo-valuacion-modal").find("option[value='1']").attr("disabled",true);
    		$("#sl-tipo-valuacion-modal").find("option[value='1']").removeAttr("selected");
    	}*/	
    	$("#sl-uma-modal option").each(function(i, item){
			if($(item).val()==$("#uma-periodo").val()){
				$(item).attr("selected", true);
			}
		});
	});


    $("#modal-agregar-valor").on('show.bs.modal', function(){    	
    			
    });


   $(".btn-cancelar-valor-cancelar").on("click", function(){
   	$("#modal-agregar-valor").modal("hide");
    	clearModalValuacion();
   });

   $(".btn-agregar-valor-confirmacion").on("click",function(){
    	var datos = new FormData();
    	if($.trim($("#current-index").val())!=""){
    		console.log("editando: "+$("#current-index").val());
    		var item = creaItemValuacion();
    		item = setCurrentValuacion(item, $("#current-index").val(), $("#sl-tipo-valuacion-modal"), $("#txt-valor-modal"), $("#txt-fecha-adquisicion-modal"), $("#sl-edo-fisico-modal"), $("#txt-fecha-cierre-modal"), $("#sl-uma-modal"), $("#sl-tipo-depreciacion-modal"), $("#txt-depreciacion-periodo-modal"), $("#txt-depreciacion-acumulada-modal"), $("#txt-valor-libros-modal"), $("#txt-valor-actual-modal"), $("#txt-contable-modal"));
	    	console.dir(item);
	    	valuaciones[$("#current-index").val()] = item;
	   }else{
    		console.log("nuevo");
    		console.log("valuaciones: "+valuaciones.length);
    		var nuevo = creaItemValuacion();
    		nuevo = setCurrentValuacion(nuevo, valuaciones.length, $("#sl-tipo-valuacion-modal"), $("#txt-valor-modal"), $("#txt-fecha-adquisicion-modal"), $("#sl-edo-fisico-modal"), $("#txt-fecha-cierre-modal"), $("#sl-uma-modal"), $("#sl-tipo-depreciacion-modal"), $("#txt-depreciacion-periodo-modal"), $("#txt-depreciacion-acumulada-modal"), $("#txt-valor-libros-modal"), $("#txt-valor-actual-modal"), $("#txt-contable-modal"));
	    	valuaciones.push(nuevo);
    		console.dir(nuevo);
    	}
	    $("#modal-agregar-valor").modal("hide");
    	clearModalValuacion();
    	clearViewValuaciones($("table.valores"));
    	for(var i=0;i<valuaciones.length;i++){
    		console.log("mostrando en tabla: "+i);
    		console.dir(valuaciones[i]);
    		addItemValuaciones(valuaciones[i], $("table.valores tbody"), i);
    	}
    	
    	$("#current-index").val("");
   });

   $("table.valores tbody").on("click","button.btn-edit-modal", function(event){
    	event.preventDefault();
    	$trigger = $(this);
    	var index = $trigger.attr("data-index");
    	var item = valuaciones[index];
    	console.log("index: "+index);
    	$("#current-index").val(index);
    	console.dir(item);
    	clearModalValuacion();
    	bindItemValuacion(item);		
    	$("#modal-agregar-valor").modal("show");
    	if(index == 0){
    		$("#sl-tipo-valuacion-modal option").each(function(index, $item){
    			if($($item).val()!="1"){
    				$($item).attr("disabled",true);
    				$($item).removeAttr("selected");
    			}
    		});
    	}else{
    		$("#sl-tipo-valuacion-modal").find("option[value='1']").attr("disabled",true);
    		$("#sl-tipo-valuacion-modal").find("option[value='1']").removeAttr("selected");
    	}
    });

   	$("table.valores tbody").on("click","button.btn-remove-modal", function(event){
   		if(confirm("¿Está seguro de eliminar el registro?")){
	   		var index = $(this).attr("data-index");
	   		size = valuaciones.length; 
	   		valuaciones = eliminarValor(valuaciones, index);
	   		console.log("size: "+size+", valuaciones: "+valuaciones.length);
	   		if(size>valuaciones.length){
	   			new PNotify({
	                title: 'Registro eliminado',
	                text: 'Se elimino el registro seleccionado',
	                type: 'info',
	                hide: true,
	                delay: 2000,
	                styling: 'bootstrap3'
	            });
	   		}else{
	   			new PNotify({
	                title: 'Error',
	                text: 'No se ha podido eliminar el registro',
	                type: 'error',
	                hide: true,
	                delay: 2000,
	                styling: 'bootstrap3'
	            });
	   		}
	   		clearViewValuaciones($("table.valores"));
	   		for(var i=0;i<valuaciones.length;i++){
	    		//console.log("mostrando en tabla: "+i);
	    		//console.dir(valuaciones[i]);
	    		addItemValuaciones(valuaciones[i], $("table.valores tbody"), i);
	    	}
	    }	
   	});
    

   	$("input.item-valuacion").each(function(){
   		var nuevo = creaItemValuacion();
   		var $actual = $(this); 
		nuevo = setCurrentValuacionValues(nuevo, valuaciones.length, 
			{
				"id":$actual.attr("data-tipo"),
				"descr":$actual.attr("data-tipo-descr")
			}, 
			$actual.attr("data-valor"), 
			$actual.attr("data-fecha"), 
			{
				"id":$actual.attr("data-estado-fisico"),
				"descr":$actual.attr("data-estado-fisico-descr")
			}, 
			$actual.attr("data-fecha-cierre"), 
			{
				"id":$actual.attr("data-uma"),
				"factor":$actual.attr("data-uma-factor"),
				"valorDiario":$actual.attr("data-uma-valor-diario"),
				"valorMensual":$actual.attr("data-uma-valor-mensual"),
				"valorAnual":$actual.attr("data-uma-valor-anual"),
				"anio":$actual.attr("data-uma-anio")
			},
			{
				"id":$actual.attr("data-depreciacion"),
				"descr":$actual.attr("data-depreciacion-descr"),
				"cuenta":$actual.attr("data-depreciacion-cuenta"),
				"vidaUtil":$actual.attr("data-depreciacion-vida-util"),
				"depAnual":$actual.attr("data-depreciacion-dep-anual"),
			}, 
			$actual.attr("data-dep-periodo"), 
			$actual.attr("data-dep-acumulada"), 
			$actual.attr("data-valor-libros"), 
			$actual.attr("data-valor-actual"), 
			{
				"contable":$actual.attr("data-contable"),
				"descr":$actual.attr("data-contable-descr")
			}
		);
    	valuaciones.push(nuevo);    			
   	});
   	//clearViewValuaciones($("table.valores"));
	for(var i=0;i<valuaciones.length;i++){
		console.log("mostrando en tabla: "+i);
		console.dir(valuaciones[i]);
		addItemValuaciones(valuaciones[i], $("table.valores tbody"), i);
	}
	$("#current-index").val("");


    $(".content-preloader").fadeOut();
	$("body").removeClass("body-loading");
});

function eliminarValor(valuaciones, index){
	var size=valuaciones.length;
	for(var i=0;i<size;i++){
		if(i>index){
			valuaciones[i].index = valuaciones[i].index-1; 
		}
	}
	valuaciones.splice(index, 1);
	return valuaciones;
}

function clearModalValuacion(){
	console.log("ClearModalValuacion");
	$("#current-index").val("");
	$("#sl-tipo-valuacion-modal option").removeAttr("disabled");
	$("#sl-tipo-valuacion-modal").val("");
	$("#sl-edo-fisico-modal").val("");	
	$("#txt-valor-modal").val("");
	$("#txt-fecha-adquisicion-modal").val("");
	console.log("Fecha: "+$("#txt-fecha-adquisicion-modal").val());
	//$("#txt-fecha-adquisicion-modal").datepicker('update','endDate',moment($("#txt-fecha-cierre-modal").val(),'DD-MM-YYYY').format("DD/MM/YYYY"));
    console.log("Cierre: "+moment($("#cierre").val(),'YYYY-MM-DD').format("DD-MM-YYYY"));
    $("#txt-fecha-cierre-modal").val(moment($("#cierre").val(),'YYYY-MM-DD').format("DD-MM-YYYY"));	
	$("#sl-uma-modal").val("0");
	$("#sl-tipo-depreciacion-modal").val("");
	$("#txt-depreciacion-periodo-modal").val("");
	$("#txt-depreciacion-acumulada-modal").val("");
	$("#txt-valor-libros-modal").val("");
	$("#txt-valor-actual-modal").val("");
	$("#txt-contable-modal").val("INSTRUMENTAL");
	$("#txt-contable-modal").attr("data-inventario-contable",'0');
}


function bindItemValuacion(current){
	$("#current-index").val(current.index);
	$("#sl-tipo-valuacion-modal").val(current.tipoImporte.id);
	$("#txt-valor-modal").val(current.valor);
	$("#txt-fecha-adquisicion-modal").val(current.fechaAdquisicion);
	//$("#txt-fecha-adquisicion-modal").datepicker('update','startDate',moment(current.fechaAdquisicion,'DD-MM-YYYY').format("DD/MM/YYYY"));
	//$("#txt-fecha-adquisicion-modal").datepicker('endDate',moment(current.fechaCierre,'DD-MM-YYYY').format("DD/MM/YYYY"));
	$("#sl-edo-fisico-modal").val(current.edoFisico.id);	
	
	$("#txt-fecha-cierre-modal").val(current.fechaCierre);
	$("#txt-fecha-cierre-modal").datepicker('endDate',moment(current.fechaCierre,'DD-MM-YYYY').format("DD/MM/YYYY"));
	$("#sl-uma-modal").val(current.uma.id);
	$("#sl-tipo-depreciacion-modal").val(current.tipoDepreciacion.id);
	$("#txt-depreciacion-periodo-modal").val(current.depPeriodo);
	$("#txt-depreciacion-acumulada-modal").val(current.depAcumulada);
	$("#txt-valor-libros-modal").val(current.valorLibros);
	$("#txt-valor-actual-modal").val(current.valorActual);
	$("#txt-contable-modal").val(current.clasificacion.descr);
	$("#txt-contable-modal").attr("data-inventario-contable",current.clasificacion.contable);
}

function clearViewValuaciones($tabla){
	$tabla.find("tbody tr").each(function(){
		$(this).remove();
	});
}

function addItemValuaciones(current, $table, index){
	var $tr = $("<tr></tr>");
	$tr.attr("data-index", current.index);
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td></td>"));
	$tr.append($("<td><button data-index='"+index+"' class='btn btn-sm btn-primary btn-edit-modal'><i class='fa fa-edit'></i> Editar</button><button data-index='"+index+"' class='btn btn-sm btn-danger btn-remove-modal'><i class='fa fa-close'></i> Eliminar</button></td>"));
	$tr.find("td:eq(0)").html(current.tipoImporte.descr);
	$tr.find("td:eq(1)").html(current.valor);
	$tr.find("td:eq(2)").html(current.fechaAdquisicion);
	$tr.find("td:eq(3)").html(current.depPeriodo);
	$tr.find("td:eq(4)").html(current.depAcumulada);
	$tr.find("td:eq(5)").html(current.fechaCierre);		
	$tr.find("td:eq(6)").html(current.valorLibros);
	$tr.find("td:eq(7)").html(current.valorActual);
	$tr.appendTo($table);	
	//$table.find("tbody").append("<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>");
}

function setCurrentValuacion(current, valores, $tipo, $valor, $fechaAdquisicion, $edoFisico, $cierre, $uma, $dep, $depPeriodo, $depAcumulada, $valorLibros, $valorActual, $contable){
	current.id = 0;
	current.periodo.id = 0;
	current.index = valores;
	current.tipoImporte.id = $tipo.val();
	current.tipoImporte.descr = $tipo.find("option:selected").html(); 
	current.valor = $valor.val();
	current.fechaAdquisicion = $fechaAdquisicion.val();
	current.edoFisico.id = $edoFisico.val();
	current.edoFisico.descr = $edoFisico.find("option:selected").html(); 	
	current.fechaCierre = $cierre.val();
	current.uma.id = $uma.val();
	current.uma.factor = $uma.find("option:selected").attr("data-factor");
	current.uma.valorDiario = $uma.find("option:selected").attr("data-valor-diario");
	current.uma.valorMensual = $uma.find("option:selected").attr("data-valor-mensual");
	current.uma.valorAnual = $uma.find("option:selected").attr("data-valor-anual");
	current.uma.anio = $uma.find("option:selected").attr("data-anio");
	current.tipoDepreciacion.id = $dep.val();
	current.tipoDepreciacion.descr = $dep.find("option:selected").attr("data-descr");
	current.tipoDepreciacion.cuenta = $dep.find("option:selected").attr("data-cuenta");
	current.depPeriodo = $depPeriodo.val();
	current.depAcumulada = $depAcumulada.val();
	current.valorLibros = $valorLibros.val();
	current.valorActual = $valorActual.val();
	current.clasificacion.contable = $contable.attr("data-inventario-contable");
	current.clasificacion.descr = $contable.val();
	return current;
};

function setCurrentValuacionValues(current, valores, tipo, $valor, $fechaAdquisicion, $edoFisico, $cierre, $uma, $dep, $depPeriodo, $depAcumulada, $valorLibros, $valorActual, $clasificacion){
	current.id = 0;
	current.periodo.id = 0;
	current.index = valores;
	current.tipoImporte.id = tipo.id;
	current.tipoImporte.descr = tipo.descr; 
	current.valor = $valor;
	current.fechaAdquisicion = $fechaAdquisicion;
	current.edoFisico.id = $edoFisico.id;
	current.edoFisico.descr = $edoFisico.descr; 	
	current.fechaCierre = $cierre;
	current.uma.id = $uma.id;
	current.uma.factor = $uma.factor;
	current.uma.valorDiario = $uma.valorDiario;
	current.uma.anio = $uma.anio;
	current.tipoDepreciacion.id = $dep.id;
	current.tipoDepreciacion.descr = $dep.descr;
	current.tipoDepreciacion.cuenta = $dep.cuenta;	
	current.depPeriodo = $depPeriodo;
	current.depAcumulada = $depAcumulada;
	current.valorLibros = $valorLibros;
	current.valorActual = $valorActual;
	current.clasificacion.contable = $clasificacion.contable;
	current.clasificacion.descr = $clasificacion.descr;
	return current;
};

function creaItemValuacion(){
	var current = {
		id:0,
		index:1,
		periodo:{
			id:0,
			descr:""
		},
		tipoImporte:{
			id:0,
			descr:''
		},
		valor:0,
		fechaAdquisicion:'0000-00-00 00:00:00',
		edoFisico:{
			id:0,
			descr:''
		},	
		fechaCierre:'0000-00-00 00:00:00',
		uma:{
			id:0,
			factor:0,
			valorDiario:0,
			valorMensual:0,
			valorAnual:0,
			anio:'0000'
		},
		tipoDepreciacion:{
			id:0,
			descr:'',
			cuenta:'',
		},
		depPeriodo:0,
		depAcumulada:0,
		valorLibros:0,
		valorActual:0,
		clasificacion:{
			contable:0,
			descr:'INSTRUMENTAL'
		}
	};
	return current;
}

function addValuacionFormData(index, formData, current){
	formData.append("v_id["+index+"]", current.id);
	formData.append("v_index["+index+"]", current.index);
	formData.append("v_periodo["+index+"]", current.periodo.id);	
	formData.append("v_tipoImporte["+index+"]", current.tipoImporte.id);
	formData.append("v_valor["+index+"]", current.valor);
	formData.append("v_fechaAdquisicion["+index+"]", $.trim(current.fechaAdquisicion)!=""?moment(current.fechaAdquisicion,"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"):"0000-00-00 00:00:00");
	formData.append("v_edoFisico["+index+"]", current.edoFisico.id);
	formData.append("v_fechaCierre["+index+"]", $.trim(current.fechaCierre)!=""?moment(current.fechaCierre,"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"):"0000-00-00 00:00:00");
	formData.append("v_uma["+index+"]", current.uma.id);
	formData.append("v_tipoDepreciacion["+index+"]", current.tipoDepreciacion.id);
	formData.append("v_depPeriodo["+index+"]", current.depPeriodo);
	formData.append("v_depAcumulada["+index+"]", current.depAcumulada);
	formData.append("v_valorLibros["+index+"]", current.valorLibros);
	formData.append("v_valorActual["+index+"]", current.valorActual);
	formData.append("v_cc["+index+"]", current.clasificacion.contable);
	return formData;
};


function esContable(uma, $valor){
	var contable = false;
	if(uma.id>0&&$valor.val()>0){
		contable = $valor.val()>=(uma.factor*uma.valorDiario)?true:false;
    }
    //console.log($valor.val()+">="+(uma.factor*uma.valorDiario));
    //console.log("contable: "+contable);
    return contable;
}

function getItemUma($uma){
	var response = {
		id:0,
		factor:0,
		valorDiario:0
	};
	if(typeof $uma!="undefined" && $uma.find("option:selected").val()>0){
		response.id = $uma.val();
		response.factor = $uma.find("option:selected").attr("data-factor");
		response.valorDiario = $uma.find("option:selected").attr("data-valor-diario");
	}
	return response;
}


function getItemDepreciacion($dep){
	var response = {
		id:0,
		vidaUtil:0,
		depreciacionAnual:0,
		tipo:0
	};
	if(typeof $dep!="undefined" && $dep.find("option:selected").val()>0){
		response.id = $dep.find("option:selected").val();
		response.vidaUtil = $dep.find("option:selected").attr("data-vida-util");
		response.depreciacionAnual = $dep.find("option:selected").attr("data-depreciacion-anual");
		response.tipo = $dep.attr("data-tipo");
	}
	return response;
}

function aplicaValorContable(valor, valorUma, factorUma){
	if(valor>=(valorUma*factorUma)){
		return true;
	}else{
		return false;
	}
}

function getMeses(fechaCierre, fechaInicio){
	var fin = moment(fechaCierre,'YYYY-MM-DD');
	var ini = moment(fechaInicio,'YYYY-MM-DD');
	console.log("getMeses: ");
	console.log(fin.format('YYYY-MM-DD'));
	console.log(ini.format('YYYY-MM-DD'));
	
	var fechaFin = new Date(fin.format('YYYY'),fin.format('MM')-1);
	var fechaInicio = new Date(ini.format('YYYY'),ini.format('MM')-1)

	console.log(fechaFin);
	console.log(fechaInicio);

	console.log("meses: "+(fechaFin.getMonth()+1)+"-"+(fechaInicio.getMonth()+1)+"="+((fechaFin.getMonth()+1) - (fechaInicio.getMonth()+1) ));
	console.log("meses x anios: "+fechaFin.getFullYear()+"-"+fechaInicio.getFullYear()+"="+(12 * (fechaFin.getFullYear() - fechaInicio.getFullYear())));
	
	var months = (fechaFin.getMonth()+1) - (fechaInicio.getMonth()+1) + (12 * (fechaFin.getFullYear() - fechaInicio.getFullYear())); 
	/*if(fechaFin.getDate() < fechaInicio.getDate()){ 
		months--; 
	}*/
	return months;
	//Fuente: https://www.iteramos.com/pregunta/52411/la-diferencia-en-meses-entre-dos-fechas-en-javascript
	//return dateFin.diff(dateIni, 'months');
}

function calcDepreciacionAcumulada(importe, vidaUtil, porcentajeDepreciacion, meses){
	var depreciacion = 0;
	if(importe>0 && vidaUtil>0 && porcentajeDepreciacion>0){
		/*console.log("depreciacionAcumulada: ");
		console.log("importe: "+importe);
		console.log("vidaUtil: "+vidaUtil);
		console.log("meses: "+meses);*/
		depreciacion = ((importe/vidaUtil)/12)*meses;
		/*console.log("depreciacionAcumulada: "+depreciacion);*/
		if(depreciacion>importe){
			depreciacion = importe;
		}
	}
	return Number.parseFloat(depreciacion).toFixed(2);
}

function calDepreciacionPeriodo(importe, vidaUtil, fechaCierre, fechaInicio){
	var dateCierre = moment(fechaCierre,'YYYY-MM-DD');
	var dateInicio = moment(fechaInicio,'DD-MM-YYYY');
	var fechaInicioAnio = moment(dateCierre.format('YYYY')+'-01-01','YYYY-MM-DD');
	var depPeriodo = 0;
	/*console.log("dateCierre: "+dateCierre.format('YYYY-MM-DD'));
	console.log("dateInicio: "+dateInicio.format('YYYY-MM-DD'));
	console.log("fechaInicioAnio: "+fechaInicioAnio.format('YYYY-MM-DD'));
	console.log("Inicio - IniAño: "+dateInicio.diff(fechaInicioAnio,'months'));
	console.log("vidaUtil: "+vidaUtil);
	console.log("importe: "+importe);*/
	if(typeof vidaUtil!='undefined' && vidaUtil>0 && typeof importe!="undefined" && importe>0){
		//var diferencia = dateInicio.diff(fechaInicioAnio,'months');
		var diferencia = getMeses(dateInicio, fechaInicioAnio);
		if(diferencia>=0){
			var meses = getMeses(dateCierre, dateInicio);
			console.log(">0-Diferencia: "+meses+", ["+dateInicio.format('YYYY-MM-DD')+"-"+dateCierre.format('YYYY-MM-DD'));
			depPeriodo = ((importe/vidaUtil)/12)*meses;
		}else{
			var meses = dateCierre.format("MM");

			depPeriodo = ((importe/vidaUtil)/12)*meses;
		}	
	}
	console.log("depPeriodo: "+depPeriodo);
	return depPeriodo;
}


function getValorLibros($valor, depreciacionAcumulada){
	var response = {
		'importe':0,
		'result':'',
		'message':''
	};
	if(typeof $valor!='undefined' && typeof depreciacionAcumulada!='undefined' && $valor.val()>=0 && depreciacionAcumulada>=0 ){
		if(($valor.val()-depreciacionAcumulada)==0){
			response.importe = 1;
			response.result = 'FAIL';
			response.message = "La depreciación del bien supera su valor";
		}else if(($valor.val()-depreciacionAcumulada)<=0){
    		response.result = 'FAIL';
    		response.importe = 1;
    		response.message = "El bien ya se encuentra depreciado en su totalidad";
    	}else{
    		response.importe = ($valor.val()-depreciacionAcumulada).toFixed(2);
    		response.result = 'SUCCESS';
    	}
	}
	return response;		
}

function calcularAniosUso(fecha, fechaFinal){
	console.log("calcular años uso: "+fecha+" - "+fechaFinal);
	var a = moment(fechaFinal, 'd-M-Y');
	var b = moment(fecha, "d-M-Y");
	//console.log("years: "+a.diff(b, 'years'));
	var res = a.diff(b, 'years');
	console.log("Diferencia: "+res);
	return res;	
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

function b64EncodeUnicode(str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
    }));
}

function b64DecodeUnicode(str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}

//file = $(this)[0].files[x]
//contentDummy = $(".content-item-previews").find(".item-dummy");
//$contentMain = $(".content-item-previews");
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