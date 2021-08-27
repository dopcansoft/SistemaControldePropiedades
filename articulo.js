var settings = new Properties();
settings.init("src/config/settings.xml");

$(document).on("ready", function(){
	init_sidebar();

	//calcularAniosUso("01-01-2010");

	console.log(moment("01-03-2010","DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"));

	$('#sl-rubro, #sl-departamento, #sl-depreciacion').selectpicker({
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
    	$("#txt-anios-uso").val(anios);
    });

    $("#btn-guardar").click(function(event){
    	if(confirm("¿Está seguro de guardar la información?")){
    		event.preventDefault();
    		$trigger  = $(this);
    		
    		var importe=$("#sl-tipo-valuacion").val()=="IMPORTE"?$("#txt-valuacion").val():"", 
    			valor_reposicion=$("#sl-tipo-valuacion").val()=="VALOR_REPOSICION"?$("#txt-valuacion").val():"",
    			valor_reemplazo=$("#sl-tipo-valuacion").val()=="VALOR_REEMPLAZO"?$("#txt-valuacion").val():"";

    		var ajax_data = new FormData();
    		ajax_data.append("articulo",$("#articulo").val());
    		ajax_data.append("empresa",$("#empresa").val());
    		ajax_data.append("periodo",$("#periodo").val());
    		ajax_data.append("departamento",$("#sl-departamento").val());
    		ajax_data.append("rubro", $("#sl-rubro").val());
    		ajax_data.append("descripcion", $("#txt-descripcion").val());
    		ajax_data.append("marca", $("#txt-marca").val());
    		ajax_data.append("modelo", $("#txt-modelo").val());
    		ajax_data.append("serie", $("#txt-serie").val());
    		ajax_data.append("motor", $("#txt-motor").val());
    		ajax_data.append("factura", $("#txt-factura").val());
    		ajax_data.append("fechaAdquisicion", moment($("#txt-fecha-adquisicion").val(),"DD-MM-YYYY").format("YYYY-MM-DD HH:mm:ss"));    		
    		ajax_data.append("depreciacion", $("#sl-depreciacion").val());
    		ajax_data.append("aniosUso", $("#txt-anios-uso").val());
    		
    		ajax_data.append("importe", importe);
    		ajax_data.append("valorReemplazo", valor_reemplazo);
    		ajax_data.append("valorReposicion", valor_reposicion);
    		
    		ajax_data.append("depreciacionPeriodo", $("#txt-depreciacion-periodo").val());
    		ajax_data.append("depreciacionAcumulada", $("#txt-depreciacion-acumulada").val());
    		ajax_data.append("depreciacionPeriodo", $("#txt-depreciacion-periodo").val());
    		ajax_data.append("estadoFisico", $("#sl-estado").val());
    		ajax_data.append("imagen", $("#txt-file")[0].files[0]);

    		var url=$.trim($("#articulo").val())!=""?'src/services/updarticulo.php':'src/services/altaarticulo.php';
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
						$("#articulo").val(response.data.id);
						new PNotify({
							title: 'Éxito',
							text: response.desc,
							type: 'success',
							hide: false,
							styling: 'bootstrap3',
						});
					}else if(response.result=="FAIL"){
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
			$trigger.removeAttr("disabled");
    	}
    });

    $("#txt-valuacion").on("keyup", function(){
    	$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-valuacion").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));  		
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val()));
    });

    /*$("#sl-depreciacion").on("change",function(){
 		$("#txt-depreciacion-periodo").val(calcDepreciacion($("#txt-importe").val(), $("#sl-depreciacion option:selected").attr("data-depreciacion-anual")));   	
    	$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val()));
    });

    $("#txt-anios-uso, #txt-depreciacion-periodo").on("keyup", function(){
		$("#txt-depreciacion-acumulada").val(calcDepAcumulada($("#txt-anios-uso").val(), $("#txt-depreciacion-periodo").val()));    	
    });*/


});

function calcDepreciacion(importe, porDep){
	var depreciacion = 0;
	if(importe>0 && porDep>0){
		var porDep = porDep/100;  
		depreciacion = importe*porDep;		
	}
	return depreciacion;
}

function calcDepAcumulada(aniosUso, depPeriodo){
	return aniosUso*depPeriodo;
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