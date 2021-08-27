window.onload = addPageNumbers;

function addPageNumbers() {
	console.log("carga el alto de pagina");
	//var totalPages = Math.ceil(document.body.scrollHeight / 1123);  //842px A4 pageheight for 72dpi, 1123px A4 pageheight for 96dpi, 
	var totalPages = Math.ceil(document.body.scrollHeight / 1288);
	for (var i = 1; i <= totalPages; i++) {
	  var pageNumberDiv = document.createElement("div");
	  var pageNumber = document.createTextNode("Página: " + i + " de " + totalPages);
	  //pageNumberDiv.style.background="#336699";
	  pageNumberDiv.style.display = "block";
	  pageNumberDiv.style.paddingTop = "5px";
	  pageNumberDiv.style.textAlign = "right";
	  pageNumberDiv.style.borderTop = "1px solid #DDD";
	  pageNumberDiv.style.position = "absolute";
	  //pageNumberDiv.style.top = "calc((" + i + " * (297mm - 0.5px)) - 40px)"; //297mm A4 pageheight; 0,5px unknown needed necessary correction value; additional wanted 40px margin from bottom(own element height included)
	  pageNumberDiv.style.top = "calc((" + i + " * (258mm)) - 21px)";
	  pageNumberDiv.style.left = "0";
	  pageNumberDiv.style.right = "0";
	  pageNumberDiv.style.height = "16px";
	  pageNumberDiv.appendChild(pageNumber);
	  document.body.insertBefore(pageNumberDiv, document.getElementById("content"));
	  //pageNumberDiv.style.left = "calc(100% - (" + pageNumberDiv.offsetWidth + "px + 20px))";
	}
}
$(document).on("ready", function(){
	window.print();	
});