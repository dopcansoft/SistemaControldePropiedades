$(document).on("ready", function(){
	$(".content-preloader").fadeOut();
	$(".content-preloader").remove();
	$("body").removeClass("body-loading");
	window.print();	
});