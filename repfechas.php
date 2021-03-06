<?
    session_start();
    session_name("inv");   
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
    include("src/vo/config.php");
    Logger::configure("src/config/log4php.xml");
    $log = Logger::getLogger("empresas");
    $settings = new Properties("src/config/settings.xml");
    $database = new Properties("src/config/database.xml");
    $queries = new Properties("src/config/queries.xml");

    if(!isset($_SESSION["usuario"])){
        header("Location: ".$settings->prop("url.login"));
    }

    $misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
 	$empresa = unserialize($_SESSION["empresa"]);
 	$periodo = unserialize($_SESSION["periodo"]);

 	$clasificacion = isset($_SESSION["clasificacion"])?$_SESSION["clasificacion"]:"";
 	$departamento = isset($_SESSION["departamento"])?$_SESSION["departamento"]:"";
 	$edoFisico = isset($_SESSION["edoFisico"])?$_SESSION["edoFisico"]:"";

 	$clasificacionBM = array();
 	$clasificacionBI = array();
 	$departamentos = array();
 	$estados = array();
 	$catUma = array();
 	$catDepreciacion = array();
 	
 	$today = new DateTime();
    $fechaInicio = new DateTime();
    $fechaFin = new DateTime();    
    $fechaInicio->modify("-".($today->format("N")-1).' day');
    $fechaFin->modify("+".(7-$today->format("N")).' day');

    //$fechaInicio->format("Y-m-d H:i:s");
    $list = array();
    $response = array("result"=>"", "desc"=>"");
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
        	//$list = $inventarioFactory->getAll($empresa->id, $periodo->id, $db, $queries, $settings, $log);        	
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", $fechaInicio->format("Y-m-d H:i:s"), $fechaFin->format("Y-m-d H:i:s"), "", $clasificacion, $departamento, $edoFisico, $estatusInventario, $db, $queries, $settings, $log);
        	for($x=0;$x<count($list);$x++){
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}
        	$catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
			$departamentos = $catalogoFactory->listado($db, $queries->prop("departamentos.list"), $settings, $log, "Departamento");
     		$estados = $catalogoFactory->listado($db, $queries->prop("catedofisico.list"), $settings, $log, "ItemCatalogo");
     		$tipos = $catalogoFactory->listado($db, $queries->prop("cattipobien.list"), $settings, $log, "ItemCatalogo");
     		$catUma = $catalogoFactory->listado($db, $queries->prop("catuma.list"), $settings, $log, "Uma");
     		$depreciacionFactory = new DepreciacionFactory();
			$catDepreciacion = $depreciacionFactory->listAll($db, $queries, $log);
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
    
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <link href="css/sinv.css" rel="stylesheet">
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
    <form id="Datos" name="Datos" action="<?$_SERVER["PHP_SELF"]?>" method="post">
      <input type="hidden" name="empresa" id="empresa" value="<?=$empresa->id?>">
      <input type="hidden" name="periodo" id="periodo" value="<?=$periodo->id?>">
      <input type="hidden" name="perfil" id="perfil" value="<?=$misesion->perfil->id?>">
    </form>
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
                <h3>Reporte por Fechas</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="row">
                    	<div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    		<label>Desde</label>
                    		<input class="form-control input-calendar" type="text" id="txt-fecha-inicio" name="txt-fecha-inicio" value="<?=$fechaInicio->format('d-m-Y')?>">
                    	</div>
                    	<div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    		<label>Hasta</label>
                    		<input class="form-control input-calendar" type="text" id="txt-fecha-fin" name="txt-fecha-fin" value="<?=$fechaFin->format('d-m-Y')?>">
                    	</div>
                    	<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                    		<label>Tipo Fecha</label>
                    		<select class="form-control" id="sl-tipo-fecha" name="sl-tipo-fecha">
                    			<option value="FECHA_INSERT"> FECHA DE REGISTRO </option>
                    			<option value="FECHA_ADQUISICION"> FECHA DE ADQUISICI??N </option>
                    		</select>
                    	</div>
                    	<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                    		<label>&nbsp;</label>
                    		<button id="btn-buscar" name="btn-buscar" class="form-control btn btn-primary"> <i class="fa fa-search"></i> Buscar </button>
                    	</div>
                    	<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                    		<label>&nbsp;</label>
                    		<button id="btn-imprimir" name="btn-imprimir" class="form-control btn btn-success"> <i class="fa fa-print"></i> Imprimir </button>
                    	</div>
                    </div>
                    <!-- <div class="row">
                    	<div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    		<label>&nbsp;</label>
                    		<button id="btn-buscar" name="btn-buscar" class="form-control btn btn-primary"> <i class="fa fa-search"></i> Buscar </button>
                    	</div>
                    </div> -->
                    <div class="ln_solid"></div>
                    
                    <table id="articulos" class="table table-responsive table-striped table-bordered tabla-datos">
                      <thead>
                        <tr>
                        	<th>ID</th>
                          <th>Folio</th>
                          <th>Descripci??n</th>
							<th>Clasificaci??n</th>
							<th>Tipo</th>
							<th>Departamento Asignado</th>
							<th>Fondo</th>
							<th>Estado Fisico</th>
							<th>Tipo Valuaci??n</th>
							<th>Valor</th>
							<th>Imagen</th>
							<th>Cta. Contable</th>
							<th>Cta. Depreciacion</th>
							<th>Fecha Registro</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="dummy">
                        	<td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        </tr>
                        <?
                        if(isset($list) && count($list)>0){
                            foreach($list as $bien){
                              ?>
                              <tr data-id="<?=$bien->id?>">
                                  <td><?=$bien->id?></td>
                                  <td><?=$bien->folio?></td>
                                  <td><?=$bien->descripcion?></td>
                                  <td><?=$bien->clasificacion->descr?></td>
                                  <td><?=$bien->tipoClasificacion->descr?></td>
                                  <td><?=$bien->departamento->descr?></td>
                                  <td><?=$bien->origen->descr?></td>
                                  <td><?=$bien->estadoFisico->descr?></td>
                                  <td><?=$bien->tipoValuacion->descr?></td>
                                  <td><?=$bien->valor?></td>
                                  <td><img src="<?=$bien->imagen?>" style="width: 50px;"></td>
                                  <td><?=$bien->cuentaContable?></td>
                                  <td><?=$bien->cuentaDepreciacion?></td>
                                  <td><?
                                  $date = new DateTime($bien->fechaInsert);
                                  echo $date->format("d-m-Y h:i:s");
                                  ?></td>
                              </tr>     
                              <? 
                            } 
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            
            </div>
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
        <!-- /footer content -->
      </div>
    </div>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- NOTIFICACIONES -->
    <script src="vendors/pnotify/dist/pnotify.js"></script>
    <script src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- TERMINA NOTIFICACIONES -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="js/bootstrap-datepicker-new.js"></script>
    <script src="js/bootstrap-datepicker.es.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="repfechas.js"></script>
  </body>
</html>