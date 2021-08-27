<?
    session_start();
    session_name("inv");   
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

    include("src/vo/config.php");
    Logger::configure("src/config/log4php.xml");
    $log = Logger::getLogger("cupones");
    $settings = new Properties("src/config/settings.xml");
    $database = new Properties("src/config/database.xml");
    $queries = new Properties("src/config/queries.xml");
    $response = array("result"=>"", "desc"=>"");

    if(!isset($_SESSION["usuario"])){
        header("Location: ".$settings->prop("url.login"));
    }

    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;

    $misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    $empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    $periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();
    
    $deptos = array();
    $conciliacion = new ConciliacionFisicaDao();

    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$catalogoFactory = new CatalogoFactory();        	
        	$deptos = $catalogoFactory->listParams($db, $queries->prop("departamento.listall"), array(
        		array("fk_id_empresa", $empresa->id, PDO::PARAM_INT)
        	),$settings, $log, "ItemCatalogo");
            if(isset($data["id"]) && $data["id"]!=""){
                $conciliacion->id = $data["id"];
                $conciliacion->find($db, $queries, $log);
                $log->debug($conciliacion);
            }
        }else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;
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

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->

    <link rel="stylesheet" href="build/bootstrap-select/css/bootstrap-select.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="vendors/cropper/dist/cropper.css">
    <link rel="stylesheet" href="vendors/jquery-ui/jquery-ui.css">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <link href="css/articulo.css" rel="stylesheet">
    <style>
	    .video-capture{
	    	background:#000;
	    	display:block;
	    }
	</style>
  </head>
  <body class="nav-md body-loading">
    <!-- PRELOADER -->
    <div class="content-preloader">
    	<!-- <div id="preloader">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
	    </div> -->
		<div class="loader">Loading...</div>
	</div>
	<!-- TERMINA PRELOADER -->
    <input type="hidden" name="conciliacion" id="conciliacion" value="<?=$conciliacion->id?>">
    <input type="hidden" name="usuario" id="usuario" value="<?=$misesion->id?>">
    <input type="hidden" name="empresa" id="empresa" value="<?=$empresa->id?>">
    <input type="hidden" name="periodo" id="periodo" value="<?=$periodo->id?>">
    
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <? include("logo_principal.php"); ?>
            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <? include("quick_info.php"); ?>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <? include("sidebar.php"); ?>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <? include("menu_footer.php"); ?>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <? include("menu_top.php"); ?>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
          	<div class="page-title">
              <div class="title_left">
                <!-- <h3>Registro de Bien</h3> -->
                <small><a href="inventario.php">Inventario</a> > <a href="#"><strong>Detalle</strong></a></small>
              </div>
            </div>
            <div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 my-form">
					<div class="x_panel">
						<div class="x_title">
							<h3><small>Conciliación Física</small></h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="conciliacion" name="conciliacion" value="">
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<label for="sl-tipo-inmueble">Departamento</label>
									<select tabindex="1" class="form-control" id="sl-departamento" name="sl-departamento">
										<option value="">--</option>
										<?
										foreach($deptos as $depto){
											?>
											<option value="<?=$depto->id?>" <?=$conciliacion->departamento->id==$depto->id?'selected="selected"':''?> ><?=$depto->descr?></option>
											<?
										}
										?>
									</select>
								</div>
								<div class="col-md-2 col-xs-12">
									<label class="form-label">&nbsp;</label>
									<button id="btn-iniciar" name="btn-iniciar" class="btn form-control btn-md btn-success"><i class="fa fa-barcode"></i> Iniciar</button>
								</div>
							</div>
							<div class="ln_solid"></div>
							<div class="row form-group list-item panel-scan">
								<div class="col-md-2">
									<p>Escane el código de barras:</p>
								</div>
								<div class="col-md-3">
									<input type="password" class="form-control" id="txt-scan" name="txt-scan">
								</div>
							</div>
							<div class="ln_solid panel-scan"></div>
							<table id="bienes" width="100%" class="table table-scan table-bordered responsive">
								<thead>
									<tr>
										<th colspan="4" style="text-align: center; font-weight: 600;">LISTADO DE BIENES</th>
									</tr>
									<tr>
										<th>Estatus</th>
										<th>Folio</th>
										<th>Descripción</th>
										<th>Verificación</th>
									</tr>
								</thead>
								<tbody>
									<tr class="dummy" data-id="">
										<td><i class="fa fa-search"></i></td>
										<td></td>
										<td></td>
                                        <td> Pendiente </td>
									</tr>
									<?
                                    if(isset($conciliacion->items)&&count($conciliacion->items)>0){
                                        foreach($conciliacion->items as $item){
                                            ?>
                                            <tr data-id="<?=$item->bien->id?>">
                                                <td><i class="fa fa-check" style="color:#5cb85c;"></i></td>
                                                <td><?=$item->bien->folio?></td>
                                                <td><?=$item->bien->descripcion?></td>
                                                <td><?
                                                $fecha = new DateTime($item->fecha.$item->hora);
                                                echo $fecha->format("d-m-Y h:i:s");
                                                ?></td>
                                            </tr>
                                            <?    
                                        }
                                    }else{
                                        ?>
                                        <tr class="no-departamento">
                                            <td colspan="4" style="text-align: center !important;">
                                                <p>No se ha seleccionado el departamento</p>
                                            </td>
                                        </tr>
                                        <?
                                    }
                                    ?>
								</tbody>		
							</table>
							<!-- TERMINA CONTENIDO -->
						</div>
					</div>
				</div>  
			</div><!-- termina row -->
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?=$settings->prop("page.view.footer.label")!=null?$settings->prop("page.view.footer.label"):""?>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- footer content -->
      </div>
    </div>
    
    
    <!-- jQuery -->
    <script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="vendors/jquery-ui/jquery-ui.js"></script>

    <!-- Bootstrap -->
    <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="build/bootstrap-select/js/bootstrap-select.js"></script>
    
    <!-- FastClick -->
    <script type="text/javascript" src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script type="text/javascript" src="vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script type="text/javascript" src="vendors/iCheck/icheck.min.js"></script>
    <!-- moment -->
    <script type="text/javascript" src="vendors/moment/min/moment.min.js"></script>
    <!-- Datatables -->
    <script type="text/javascript" src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script type="text/javascript" src="vendors/jszip/dist/jszip.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- NOTIFICACIONES -->
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <script type="text/javascript" src="vendors/cropper/dist/cropper.js"></script>
   
    <script type="text/javascript" src="js/bootstrap-datepicker-new.js"></script> -->
    <script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
    <script type="text/javascript" src="js/jquery.mask.js"></script>
    <!-- Custom Theme Scripts -->
    <!-- <script src="vendors/jquery.camera/jquery.camera_capture.js"></script> -->
    <!-- <script src="js/webcamjs-1.025.js"></script> -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="conciliacion.js"></script>
  </body>
</html>