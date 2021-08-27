<?
    session_start();
    session_name("inv");
    include("src/vo/config.php");
    $settings = new Properties("src/config/settings.xml");    
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=$settings->prop("page.view.name")!=null?$settings->prop("page.view.name"):""?></title>

    <link rel="icon" type="image/png" href="imgs/codepag_min.ico" />
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form id="Datos" name="Datos" >
              <img src="imgs/logo_codepag.png" style="width:100%; margin-bottom: 15px;">
          	  <div>
                <input type="text" class="form-control" id="txt-email" name="txt-email" data-parsley-error-message="Debe especificar un email" placeholder="email" required="required" />
              </div>
              <div>
                <input type="password" id="txt-password" name="txt-password" data-parsley-error-message="Debe especificar una contraseña" class="form-control" placeholder="password" required="required" />
              </div>
              <div>
                <button id="btn-login" name="btn-login" class="btn btn-success"> Iniciar sesión </button>
                <!-- <a id="btn-login" name="btn-login" class="btn btn-default submit" href="#">Iniciar sesion</a> -->
                <!-- <a class="reset_pass" href="#">Lost your password?</a> -->
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />

                <div>
                  <!-- <p>©2017 todos los derechos reservados X</p> -->
                  <p><?=$settings->prop("footer.register.label")?></p>
                </div>
              </div>
            </form>
          </section>
        </div>

      </div>
    </div>
  </body>
  <script src="vendors/jquery/dist/jquery.min.js"></script>
  <script src="vendors/pnotify/dist/pnotify.js"></script>
  <script src="vendors/pnotify/dist/pnotify.buttons.js"></script>
  <script src="vendors/pnotify/dist/pnotify.nonblock.js"></script>
  <!-- Parsley -->
    <script src="vendors/parsleyjs/dist/parsley.min.js"></script>
  <script src="login.js"></script>
</html>
