function validaFormulario(nombreFormulario){
	var errors=0;
	$(nombreFormulario+" select").each(function(){
		console.log($(this).attr("required"));
		if($(this).attr("required") && $.trim($(this).val())==""){
			console.log("id: "+$(this).attr("id"));
			errors++;
			$("button[data-id="+$(this).attr("id")+"]").addClass("btn-danger-select");
		}else{
			$("button[data-id="+$(this).attr("id")+"]").removeClass("btn-danger-select")
		}
	});
	if(!$(nombreFormulario).parsley().validate()){
		errors++;
	}
	return errors.length>0?false:true;
}