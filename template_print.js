$(window).on("load",function(){
	$("body table.template-print table thead").each(function(){
		console.log($(this).length);	
	});

});

$(document).on("ready", function(){
 	window.print();
 	//doPrint();
 });

 /*function doPrint() {    
        bdhtml=window.document.body.innerHTML;    
        sprnstr="<!--startprint-->";    
        eprnstr="<!--endprint-->";    
        prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);    
        prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));    
        console.dir(window.document);
        window.document.body.innerHTML=prnhtml; 
        window.print();    
}*/