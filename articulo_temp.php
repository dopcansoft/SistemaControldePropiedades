<?
    session_start();
    session_name("inv");   

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
    
    $list = array();
    $articulo = new ArticuloDao();
    $catArmonizado = array();
    $departamentos = array();
    $catDepreciacion = array();
    $catEdoFisico = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])){
        		$articulo->id = $data["id"];
   	     		$articulo->find($db, $queries, $log);
        	}
        	$periodoFactory = new PeriodoFactory();
        	$periodos = $periodoFactory->listAll($empresa, $db, $queries, $log);
        	$catalogoFactory = new CatalogoFactory();
        	$catArmonizado = $catalogoFactory->listado($db, $queries->prop("catbienes.list"), $settings, $log, "ItemCatalogo");
			$departamentos = $catalogoFactory->listado($db, $queries->prop("departamentos.list"), $settings, $log, "ItemCatalogo");        	
			$depreciacionFactory = new DepreciacionFactory();
			$catDepreciacion = $depreciacionFactory->listAll($db, $queries, $log);
			$catEdoFisico = $catalogoFactory->listado($db, $queries->prop("catedofisico.list"), $settings, $log, "ItemCatalogo");
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

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/articulo.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <input type="hidden" name="id" id="id" value="<?=$articulo->id?>">
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
                <h3>Registro de Producto</h3>
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
				<div class="col-md-12 col-sm-12 col-xs-12 my-form">
					<div class="x_panel">
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="articulo" name="articulo" value="<?=$articulo->id?>">
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<div class="row form-group">
								<div class="col-md-3 col-md-offset-6">
									<label>Folio</label>
									<input type="text" class="form-control" id="txt-id" name="txt-id">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<label>Departamento</label>
									<select class="form-control" id="sl-departamento" name="sl-departamento" data-live-search="true">
										<option value="0">--</option>
										<?
										if(isset($departamentos) && count($departamentos)>0){
											foreach($departamentos as $dep){
												?><option value="<?=$dep->id?>" <?=$articulo->departamento->id==$dep->id?'selected="selected"':''?> ><?=$dep->descr?></option><?
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-6">
									<label>Rubro</label>
									<select class="form-control selectpicker show-tick" id="sl-rubro" name="sl-rubro" data-live-search="true">
										<option value="0">--</option>
										<?
										foreach($catArmonizado as $ca){
											?><option value="<?=$ca->id?>" <?=$articulo->rubro->id==$ca->id?'selected="selected"':''?> ><?=$ca->descr?></option><?
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<label>Cuenta Contable</label>
									<input type="text" class="form-control" id="txt-cuenta-contable" name="txt-cuenta-contable" readpnly="readonly" value="">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-9">
									<label>Descripción</label>
									<textarea class="form-control" rows="5" id="txt-descripcion" name="txt-descripcion"><?=$articulo->descripcion?></textarea>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<label>Marca</label>
									<input type="text" class="form-control" id="txt-marca" name="txt-marca" value="<?=$articulo->marca?>">
								</div>
								<div class="col-md-3">
									<label>Modelo</label>
									<input type="text" class="form-control" id="txt-modelo" name="txt-modelo" value="<?=$articulo->modelo?>">
								</div>
								<div class="col-md-3">
									<label>Serie</label>
									<input type="text" class="form-control" id="txt-serie" name="txt-serie" value="<?=$articulo->serie?>">
								</div>
							</div>
							<div class="row form-group">							
								<div class="col-md-3">
									<label>Factura</label>
									<input type="text" class="form-control" id="txt-factura" name="txt-factura" value="<?=$articulo->factura?>">
								</div>
								<div class="col-md-3">
									<label>Motor</label>
									<input type="text" class="form-control" id="txt-motor" name="txt-motor" value="<?=$articulo->motor?>">
								</div>
								<div class="col-md-3">
									<label>Fecha de Adquisición</label>
									<?
									$fechaAdquisicion = new DateTime($articulo->fechaAdquisicion);
									?>
									<input type="text" class="form-control input-calendar" id="txt-fecha-adquisicion" name="txt-fecha-adquisicion" placeholder="dd-mm-yyyy" value="<?=$fechaAdquisicion->format("d-m-Y")?>">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-6">
									<label>Porcentaje de Depreciación</label>
									<select class="form-control" id="sl-depreciacion" name="sl-depreciacion" data-live-search="true">
										<option value="0">--</option>
										<?
										if(isset($catDepreciacion)){
											foreach($catDepreciacion as $dep){
												?><option value="<?=$dep->id?>" <?=$dep->vidaUtil>0?'':'disabled="disabled"'?> data-vida-util="<?=$dep->vidaUtil?>" data-depreciacion-anual="<?=$dep->depreciacionAnual?>" <?=$articulo->depreciacion->id==$dep->id?'selected="selected"':''?> ><?=$dep->cuenta." - ".$dep->descr.($dep->depreciacionAnual>0?" (".$dep->depreciacionAnual."%)":'')?></option><?
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3 class="text-center"><small>Valuación del Bien</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-5 col-md-offset-2">
									<div class="row">
										<div class="col-md-6 form-group">
											<select class="form-control" id="sl-tipo-valuacion" name="sl-tipo-valuacion">
												<option value="IMPORTE"> Importe </option>
												<option value="VALOR_REPOSICION"> Valor de Reposición </option>
												<option value="VALOR_REEMPLAZO"> Valor de Reemplazo </option>
											</select>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control" id="txt-valuacion" name="txt-valuacion" value="<?=$articulo->importe?>">
										</div>
										
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3 class="text-center"><small>Valuación del Bien Mueble</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 form-group">
									<label>Valor de Terreno</label>
									<input type="text" class="form-control">
								</div>
								<div class="col-md-3 form-group">
									<div class="row">&nbsp;</div>
									<div class="row">
										<input type="checkbox">
									</div>

								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 form-group">
									<label>Valor de Edificacion</label>
									<input type="text" class="form-control">
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3><small>Datos Referentes al Ejercicio</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>					
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3">
									<label>Depreciación del Periodo</label>
									<input type="text" class="form-control" id="txt-depreciacion-periodo" name="txt-depreciacion-periodo" value="<?=$articulo->depreciacionPeriodo?>">
								</div>
								<div class="col-md-3">
									<label>Depreciación Acumulada</label>
									<input type="text" class="form-control" id="txt-depreciacion-acumulada" name="txt-depreciacion-acumulada" value="<?=$articulo->depreciacionAcumulada?>">									
								</div>
								<div class="col-md-3">
									<label>Estado Físico</label>
									<select class="form-control" id="sl-estado" name="sl-estado">
										<option value="0">--</option>
										<?
										if(isset($catEdoFisico) && count($catEdoFisico)>0){
											foreach($catEdoFisico as $cef){
												?><option value="<?=$cef->id?>" <?=$articulo->estadoFisico->id==$cef->id?'selected="selected"':''?> ><?=$cef->descr?></option><?
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<label>Años de uso</label>
									<input type="text" class="form-control" id="txt-anios-uso" name="txt-anios-uso" value="<?=$articulo->aniosUso?>">
								</div>
								<div class="col-md-3">
									<label>UMA</label>
									<select class="form-control" id="sl-uma" name="sl-uma">
										<option value="0"> -- </option>	
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3 class="text-center"><small>Imagen</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<img class="articulo-imagen" height="100" src="<?=$settings->prop("system.url").$articulo->imagen?>">
								</div>
								<div class="col-md-3">
									<input type="file" id="txt-file" name="txt-file">
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3 col-md-offset-3">
									<button id="btn-guardar" class="btn btn-primary form-control"><i class="fa fa-check"></i> Guardar</button>
								</div>
							</div>
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
    <!-- moment -->
    <script src="vendors/moment/min/moment.min.js"></script>
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

    <script src="vendors/cropper/dist/cropper.min.js"></script>
    <script src="build/bootstrap-select/js/bootstrap-select.js"></script>
    <script src="js/bootstrap-datepicker-new.js"></script> -->
    <script src="js/bootstrap-datepicker.es.min.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="articulo.js"></script>
  </body>
</html>