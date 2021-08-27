<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" >
    <link rel="stylesheet" href="css/template2_print.css" media="all">
    <title></title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <!-- <header>
    	<h3>Cabecera</h3>
    </header> -->
    <div id="content">
    	<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
        <p>Pellentesque nec ex nec est malesuada molestie. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Duis tristique semper lacus nec cursus. Vivamus hendrerit metus sed urna placerat, ac vulputate justo laoreet. Nulla euismod velit vel sollicitudin hendrerit. Ut dictum sapien eget enim malesuada mattis. Cras arcu nibh, lacinia id ornare vel, volutpat eu lectus. Aliquam imperdiet mattis odio, ac porta nisl luctus pretium. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam eget consequat sapien. Donec efficitur suscipit neque in malesuada. Donec id vestibulum est, nec mollis purus.</p>
        <p>Cras eget bibendum sem. Suspendisse at odio venenatis, facilisis nisi eu, feugiat dolor. Pellentesque porttitor est sed nulla vestibulum, non aliquam nisl mattis. Donec sapien massa, faucibus sed dolor aliquet, cursus ornare tortor. Aenean semper tincidunt condimentum. Nulla placerat ex ut turpis varius consequat. Donec pellentesque, nunc vitae varius porttitor, tortor orci malesuada ipsum, in congue elit justo eu nulla. Proin convallis ligula in faucibus auctor. Aliquam eu auctor ex, id dictum arcu.</p>
        <p>Praesent id quam ut lacus suscipit blandit. Aenean volutpat sapien nec nisl bibendum tempor. Mauris lobortis lorem at efficitur euismod. Proin vestibulum ipsum finibus, faucibus lacus in, fermentum lacus. In fringilla dictum fringilla. Nam dignissim, sapien quis placerat mollis, arcu felis fermentum est, vel molestie augue libero vitae diam. Vivamus tempor orci nunc, sit amet viverra orci ullamcorper et. Nullam vestibulum, lacus et dictum auctor, nisl augue porta mauris, id venenatis risus lectus nec ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam eget semper ipsum.</p>
        <p>Proin tortor enim, sollicitudin at lorem non, mattis placerat ex. Aliquam rutrum libero tellus, faucibus commodo velit aliquet a. Fusce aliquet purus dui, et rhoncus lorem consectetur quis. Vivamus volutpat ipsum quam. Phasellus lacinia tristique sodales. Nullam ut sem neque. Integer purus sem, interdum ac volutpat nec, mattis eu ligula. Donec ac justo diam. Morbi ut erat condimentum, vulputate urna non, molestie nulla. Nullam sit amet commodo sem.</p>
    </div>
</body>
<script type="text/javascript">
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
</script>
</html>