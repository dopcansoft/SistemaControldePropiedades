function Properties(){
	this.data = new Array();
}

Properties.prototype.prop = function(param){
	var valor = "";
	for(var i=0;i<this.data.length;i++){
		if(this.data[i].index==param){
			//console.log(this.data[i].value);	
			valor =this.data[i].value;
		}			
	}
	return valor;
}

Properties.prototype.init = function(path){
	var datasrc = new Array
	//var path = "src/config/settings.xml"
	if(typeof path!='undefined' && path!=""){
		$.get(path, { async: false }, function(xml){
			$(xml).find("entry").each(function(){
				var key = $(this).attr("key");
				var valor = $(this).html();
				datasrc.push({index:key,value:valor});
			});
			console.log("loaded");
		});
	}		
	this.data = datasrc;		
}
