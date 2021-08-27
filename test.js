$(document).on("ready", function(){
	$.ajax({
		url:'https://web.tulotero.mx/tuloteroweb/rest/sorteos/finished/melate+revancha+revanchita?',
		type:'GET',
		contentType:'application/x-www-form-urlencoded; charset=UTF-8;',
		dataType:'json', //json
		success:function(respuesta){
			var items = respuesta["resultados"];
			//console.dir(items);
			var dummy = $("table tbody tr:eq(0)"); 
			for(var i=0;i<items.length; i++){
				//console.dir(items[i]);
				var nuevo = dummy.clone(true)
				nuevo.find("td:eq(0)").html(items[i].nombre);
				nuevo.find("td:eq(1)").html(items[i].resultInfoLines[0]["value"]);
				var nums = items[i].combinacionObject.numerosCombinacion;
				//console.dir(nums);
				nuevo.find("td:eq(2)").html(nums[0].numero);
				nuevo.find("td:eq(3)").html(nums[1].numero);
				nuevo.find("td:eq(4)").html(nums[2].numero);
				nuevo.find("td:eq(5)").html(nums[3].numero);
				nuevo.find("td:eq(6)").html(nums[4].numero);
				nuevo.find("td:eq(7)").html(nums[5].numero);
				$("table tbody").append(nuevo);
			}
		},
		error:function(obj,quepaso,otro){
			alert("mensaje: "+quepaso);
		}
	});
});
 

//5, 8, 20, 29, 51, 54 | 1,41, 29