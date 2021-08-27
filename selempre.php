<?
    session_start();
    session_name("inv");
    include("src/vo/config.php");
    Logger::configure("src/config/log4php.xml");
    $log = Logger::getLogger("empresas");
    $database = new Properties("src/config/database.xml");
    $settings = new Properties("src/config/settings.xml");
    $queries = new Properties("src/config/queries.xml");
    $empresas = array();
    $usuario = unserialize($_SESSION["usuario"]);
    try{
    	$db = new DBConnector($database);
    	if(isset($db)){
    		$empresaFactory = new EmpresaFactory();
    		$empresas = $empresaFactory->listByUser($usuario->id, $db, $queries, $settings, $log);
    	}else{
    		$log->error('Sin conexion a bd');
    	}
    }catch(PDOException $e){
    	$log->error("PDOException: ".$e->getMessage());
    }
    $stmt = null;
?>
<!DOCTYPE html>
<html lang="es-mx">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INV</title>
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-wysiwyg -->
    <link href="vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Switchery -->
    <link href="vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <!-- starrr -->
    <link href="vendors/starrr/dist/starrr.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="build/bootstrap-select/css/bootstrap-select.css" rel="stylesheet">
    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->
    
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="build/bootstrap-select/css/bootstrap-select.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/sinv.css" rel="stylesheet">
  </head>
  <body class="login">
      <div class="login_wrapper">
        <div class="animate form">
          <section class="">
            <form id="Datos" data-parsley-validate name="Datos">
                <input type="hidden" name="id-usuario" id="id-usuario" value="<?=$usuario->id?>">
                <div class="x_panel panel-cliente">
                	<h3>Selecciona una Empresa:</h3>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"> 
                            <select id="sl-empresa" name="sl-empresa" data-live-search="true" data-size="8" title="Seleccione alguno..." data-live-search-placeholder="Search" class="form-control" required="required">
                                <option value="0"> - seleccione una empresa - </option>
                                <?
                                if(isset($empresas) && count($empresas)>0){
                                	foreach($empresas as $empresa){
                                		?><option value="<?=$empresa->id?>"><?=$empresa->nombre?></option><?	
                                	}                                	
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                			<select class="form-control" id="sl-periodo" name="sl-periodo">
                				<option value="0"> - seleccione un periodo - </option>	
                			</select>                        		
                        </div>
                        <!-- <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                            <input type="text" placeholder="Fecha de Corte" class="form-control" id="txt-fecha-cierre" name="txt-fecha-cierre">
                        </div> -->
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"> 
                            <button id="btn-seleccionar" name="btn-seleccionar" class="btn btn-success form-control">Seleccionar</button>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"> 
                            <button id="btn-agregar" name="btn-agregar" class="btn btn-primary form-control">Agregar Empresa</button>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"> 
                            <button id="btn-agregar-periodo" name="btn-agregar-periodo" class="btn btn-primary form-control">Agregar Periodo</button>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"> 
                            <button id="btn-salir" name="btn-salir" class="btn btn-danger form-control">Cerrar Sesión</button>
                        </div>
                    </div>
                </div>
            </form>
          </section>
        </div>
      </div>
    </div>
    
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Agregar Empresa</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Nombre Corto</label>
	      		</div>
	      		<div class="col-md-6">
	      			<input type="text" class="form-control" id="txt-nombre" name="txt-nombre">
	      		</div>
	      	</div>
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Descripción</label>
	      		</div>
	      		<div class="col-md-6">
	      			<textarea class="form-control" id="txt-descripcion" name="txt-descripcion"></textarea>
	      		</div>
	      	</div>
	      	<!--  <div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Dirección</label>
	      		</div>
	      		<div class="col-md-6">
	      			<input class="form-control" type="text" id="txt-direccion" name="txt-direccion">
	      		</div>
	      	</div>
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Logotipo</label>
	      		</div>
	      		<div class="col-md-6">
	      			<input type="file" id="txt-logo" name="txt-logo">
	      		</div>
	      	</div> -->
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	        <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- ventana modal periodo -->
	<div class="modal fade" id="modalPeriodo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Agregar Periodo</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Titulo</label>
	      		</div>
	      		<div class="col-md-8">
	      			<input type="text" class="form-control" id="txt-nombre" name="txt-nombre">
	      		</div>
	      	</div>
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Fecha Inicio</label>
	      		</div>
	      		<div class="col-md-4">
	      			<input class="form-control" type="text" id="txt-fecha-inicio" name="txt-fecha-inicio">
	      		</div>
	      	</div>
	      	<div class="row form-group">
	      		<div class="col-md-3">
	      			<label>Fecha Fin</label>
	      		</div>
	      		<div class="col-md-4">
	      			<input class="form-control" type="text" id="txt-fecha-fin" name="txt-fecha-fin">
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	        <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
	      </div>
	    </div>
	  </div>
	</div>
  </body>
  <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Parsley -->
    <script src="vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Select2 -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- NOTIFICACIONES -->
    <script src="vendors/pnotify/dist/pnotify.js"></script>
    <script src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- TERMINA NOTIFICACIONES -->
    <script src="build/bootstrap-select/js/bootstrap-select.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="js/validaciones.js"></script>
	<script src="selempre.js"></script>
</html>
