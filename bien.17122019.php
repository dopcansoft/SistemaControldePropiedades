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
    
    $list = array();
    $bien = new BienDao();
    $catBienesInmuebles = array();
    $catBienesMuebles = array();
    $departamentos = array();
    $catDepreciacion = array();
    $catEdoFisico = array();
    $catOrigen = array();
    $catUma = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])){
        		$bien->id = isset($data["id"])?$data["id"]:"";
        		$bien->periodo->id = $periodo->id;
   	     		$bien->find($db, $queries, $log);
   	     		$log->debug($bien);
        	}else{
        		$bien->departamento->id = isset($_SESSION["departamento"])?$_SESSION["departamento"]:"";
        		$bien->clasificacion->id = isset($_SESSION["clasificacion"])?$_SESSION["clasificacion"]:"";
        		if(isset($_SESSION["clasificacion"]) && $_SESSION["clasificacion"]!=""){
        			$clasificacion = new ClasificacionDao();
        			$clasificacion->id = $bien->clasificacion->id;
        			$depto = new DepartamentoDao();
        			$depto->id = isset($_SESSION["departamento"])?$_SESSION["departamento"]:"";
        			$emp = unserialize($_SESSION["empresa"]);
        			$per = unserialize($_SESSION["periodo"]);
        			$depto->idEmpresa = $emp->id;
        			$depto->idPeriodo = $per->id;
        			$bien->tipoClasificacion->id = "1";
        			$bienFactory = new BienFactory();
        			if($clasificacion->find($db, $queries, $log) && $depto->find($db, $queries, $log)){
        				$counter = $bienFactory->getIncrement($emp->id, $per->id, $bien->clasificacion->id, $bien->departamento->id, $db, $queries, $log);
	        			$bien->consecutivo = $counter;
	        			$counter = str_pad($counter,3,"0", STR_PAD_LEFT);
	        			$bien->folio = $clasificacion->grupo.$clasificacion->subgrupo.$clasificacion->clase."-".$depto->folio."-".$counter;	
        				$bien->clasificacion = $clasificacion;
        			}
        			
        		}        		
        	}
        	$responsableFactory = new ResponsableFactory();
        	$responsables = $responsableFactory->listAll($empresa->id, $db, $queries, $log);
        	$periodoFactory = new PeriodoFactory();
        	$periodos = $periodoFactory->listAll($empresa, $db, $queries, $log);
        	$catalogoFactory = new CatalogoFactory();
        	$deptosFactory = new DepartamentoFactory();
        	$catBienesInmuebles = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
        	//$catBienesMuebles = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$catBienesMuebles = $catalogoFactory->listParams($db, $queries->prop("catbienesmuebles.listbyfilter"), array(
        		array(":fk_id_empresa", $empresa->id, PDO::PARAM_INT),
        		array(":fk_id_periodo", $periodo->id, PDO::PARAM_INT)
        	), $settings, $log, "Clasificacion");
			$departamentos = $deptosFactory->listAll($empresa->id, $db, $queries, $log); 
			$catUma = $catalogoFactory->listado($db, $queries->prop("catuma.list"), $settings, $log, "Uma");
			$catOrigen = $catalogoFactory->listado($db, $queries->prop("catorigen.list"), $settings, $log, "ItemCatalogo");
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
    <input type="hidden" name="id" id="id" value="<?=$bien->id?>">
    <input type="hidden" name="perfil" id="perfil" value="<?=$misesion->perfil->id?>">
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
							<h3><small>Detalle del Bien</small></h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="bien" name="bien" value="<?=$bien->id?>">
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<input type="hidden" id="inventarioContable" name="inventarioContable" value="<?=$bien->inventarioContable?>">
							<div class="row form-group">
								<div class="col-md-3 col-md-offset-6 col-xs-12">
									<label>Folio</label>
									<input type="text" class="form-control" id="txt-id" name="txt-id" value="<?=$bien->folio?>" data-counter="<?=$bien->consecutivo?>" readonly="readonly">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Departamento</label>
									<select class="form-control" id="sl-departamento" name="sl-departamento">
										<option value="0">--</option>
										<?
										if(isset($departamentos) && count($departamentos)>0){
											foreach($departamentos as $dep){
												?><option data-clave="<?=$dep->folio?>" value="<?=$dep->id?>" <?=$bien->departamento->id==$dep->id?'selected="selected"':''?> ><?=strtoupper($dep->descr)?></option><?
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-6 col-xs-12">
									<label>Clasificación Armonizada</label>
									<select class="form-control selectpicker show-tick" id="sl-clasificacion" name="sl-clasificacion" data-live-search="true">
										<option value="0">--</option>
										<optgroup label="BIENES MUEBLES">
											<?
											if(isset($catBienesMuebles)&&count($catBienesMuebles)>0){
												foreach($catBienesMuebles as $cbm){
													?><option data-grupo="<?=$cbm->grupo?>" data-subgrupo="<?=$cbm->subgrupo?>" data-clase="<?=$cbm->clase?>" value="<?=$cbm->id?>" <?=$cbm->clase=="0"?'disabled="disabled"':''?> data-tipo="<?=$cbm->tipo->id?>" data-cc="<?=$cbm->cuentaContable?>" data-cd="<?=$cbm->cuentaDepreciacion?>" <?=$bien->clasificacion->id==$cbm->id&&$bien->tipoClasificacion->id==$cbm->tipo->id?'selected="selected"':''?> <?=$cbm->enabled=="0"?'disabled="disabled"':''?> ><?=$cbm->grupo.$cbm->subgrupo.$cbm->clase.$cbm->subclase.$cbm->consecutivo." - ".strtoupper($cbm->descr).($cbm->enabled=="0"?' (Deshabilitado)':'')?></option><?
												}
											}
											?>
										</optgroup>
										<!-- <optgroup label="BIENES INMUEBLES"> -->
											<?
											//if(isset($catBienesInmuebles)&&count($catBienesInmuebles)>0){
											//	foreach($catBienesInmuebles as $cbm){
													?><!-- <option value="<?=$cbm->id?>" <?=$cbm->subgrupo=="0"&&$cbm->clase=="0"?'disabled="disabled"':''?> data-tipo="<?=$cbm->tipo->id?>" data-cc="<?=$cbm->cuentaContable?>" data-cd="<?=$cbm->cuentaDepreciacion?>" <?=$bien->clasificacion->id==$cbm->id&&$bien->tipoClasificacion->id==$cbm->tipo->id?'selected="selected"':''?> ><?=$cbm->grupo.$cbm->subgrupo.$cbm->clase.$cbm->subclase.$cbm->consecutivo." - ".strtoupper($cbm->descr)?></option> --><? 
											//	}
											//}
											?>
										<!-- </optgroup> -->
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Cuenta Contable</label>
									<input type="text" class="form-control" id="txt-cuenta-contable" name="txt-cuenta-contable" readonly="readonly" value="<?=$bien->clasificacion->cuentaContable?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Cuenta Depreciación</label>
									<input type="text" class="form-control" id="txt-cuenta-depreciacion" name="txt-cuenta-depreciacion" readonly="readonly" value="<?=$bien->clasificacion->cuentaDepreciacion?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>UMA</label>
									<select class="form-control" id="sl-uma" name="sl-uma">
										<option value="0"> -- </option>	
										<?
										foreach($catUma as $uma){
											?><option data-factor="<?=$uma->factor?>" data-valor-diario="<?=$uma->valorDiario?>" value="<?=$uma->id?>" <?=$uma->anio==date("Y")?'selected="selected"':''?> ><?=$uma->anio." (".$uma->valorDiario*$uma->factor.")"?></option><?
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-9 col-xs-12">
									<label>Descripción</label>
									<textarea class="form-control" rows="5" id="txt-descripcion" name="txt-descripcion"><?=$bien->descripcion?></textarea>
									<br>
									<button class="btn btn-md btn-success" id="btn-descripcion-listen"> <i class="fa fa-microphone"></i> <span>Escuchar</span> </button>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-9 col-xs-12">
									<label>Notas Extras</label>
									<textarea class="form-control" rows="5" id="txt-notas" name="txt-notas"><?=$bien->notas?></textarea>
									<br>
									<button class="btn btn-md btn-success" id="btn-notas-listen"> <i class="fa fa-microphone"></i> <span>Escuchar</span> </button>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3 class="text-center"><small>Caracteristicas Generales</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>					
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Marca</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-marca-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input class="form-control" id="txt-marca" name="txt-marca" value="<?=$bien->marca?>"> 
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-marca">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-marca">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-marca">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-marca">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-marca"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>	
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Color</label>
									<div class="input-group"> 
										<select class="form-control" id="sl-color" name="sl-color">
											<option value=""> -- </option>
										</select>
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-item" data-value="NO APLICA" data-input="sl-color">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-item" data-value="AGREGAR" data-input="sl-color"><i class="fa fa-plus"></i> AGREGAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Modelo</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-modelo-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input type="text" class="form-control" id="txt-modelo" name="txt-modelo" value="<?=$bien->modelo?>">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-modelo">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-modelo">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-modelo">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-modelo">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-modelo"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
							</div>
							<div class="row form-group">							
								<div class="col-md-3 col-xs-12">
									<label>Serie</label>
									<!-- <input type="text" class="form-control" id="txt-serie" name="txt-serie" value="<?=$bien->serie?>"> -->
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-serie-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input type="text" class="form-control" id="txt-serie" name="txt-serie" value="<?=$bien->serie?>">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-serie">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-serie">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-serie">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-serie">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-serie"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Factura</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-factura-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input type="text" class="form-control" id="txt-factura" name="txt-factura" value="<?=$bien->factura?>">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-factura">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-factura">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-factura">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-factura">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-factura"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Motor</label>
									<!-- <input type="text" class="form-control" id="txt-motor" name="txt-motor" value="<?=$bien->motor?>"> -->
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-motor-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input type="text" class="form-control" id="txt-motor" name="txt-motor" value="<?=$bien->motor?>">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-motor">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-motor">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-motor">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-motor">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-motor"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Fecha de Adquisición</label>
									<?
									$fechaAdquisicion = "NO IDENTIFICADO";
									$log->debug($bien->fechaAdquisicion);
									if($bien->fechaAdquisicion!='0000-00-00 00:00:00'){
										$fechaAdquisicion = new DateTime($bien->fechaAdquisicion);
									}																		
									?>
									<div class="input-group"> 
										<input type="text" class="form-control input-calendar" id="txt-fecha-adquisicion" name="txt-fecha-adquisicion" placeholder="dd-mm-yyyy" value="<?=$bien->fechaAdquisicion!='0000-00-00 00:00:00'?$fechaAdquisicion->format("d-m-Y"):'NO IDENTIFICADO'?>">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-fecha-adquisicion">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-fecha-adquisicion"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Estado Físico</label>
									<select class="form-control" id="sl-estado" name="sl-estado">
										<option value="0">--</option>
										<?
										if(isset($catEdoFisico) && count($catEdoFisico)>0){
											foreach($catEdoFisico as $cef){
												?><option value="<?=$cef->id?>" <?=$bien->estadoFisico->id==$cef->id?'selected="selected"':''?> ><?=$cef->descr?></option><?
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Años de uso</label>
									<input type="text" class="form-control" id="txt-anios-uso" name="txt-anios-uso" value="<?=$bien->aniosUso?>">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Fondo de Origen de Adquisición</label>
									<select class="form-control" id="sl-origen" name="sl-origen">
										<option value="0">--</option>
										<?
										if(isset($catOrigen) && count($catOrigen)>0){
											foreach($catOrigen as $co){
												?><option value="<?=$co->id?>" <?=$bien->origen->id==$co->id?'selected="selected"':''?> ><?=$co->descr?></option><?
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Responsable</label>
									<select class="form-control" id="sl-responsable" name="sl-responsable">
										<option value="0"> -- </option>
										<?
											if(isset($responsables) && count($responsables)>0){
												foreach($responsables as $responsable){
													?><option value="<?=$responsable->id?>" <?=$bien->responsable->id==$responsable->id?'selected="selected"':''?>><?=$responsable->titulo." ".$responsable->nombre." ".$responsable->apellido?></option><?
												}
											}
										?>
									</select>	
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3><small>Información complementaria</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>	
							<div class="row form-group">
								<div class="col-md-5 col-xs-12">
									<div class="row">
										<label>Archivo de la Factura</label>
									</div>
									<div class="row">
										<?
										if($bien->archivoFactura!=""){
											?><a href="<?=$settings->prop("system.url").$bien->archivoFactura?>" style="margin-bottom:10px !important; line-height:3em !important; text-decoration: underline !important; vertical-align: super !important;">Ver Factura</a><?
										}
										?>
										<input accept="image/*,application/pdf" class="file" type="file" id="txt-file-factura" name="txt-file-factura" capture="camera">
										<!-- <label class="btn-sm btn-primary">
										    <i class="fa fa-folder-open"></i> Browse <input type="file" style="display: none;">
										</label> -->
									</div>
								</div>	
								<div class="col-md-5 col-xs-12">
									<div class="row">
										<label>Archivo de la Poliza</label>
									</div>
									<div class="row">
										<?
										if($bien->archivoPoliza!=""){
											?><a href="<?=$settings->prop("system.url").$bien->archivoPoliza?>" style="margin-bottom:10px !important; line-height:3em !important; text-decoration: underline !important; vertical-align: super !important;">Ver Poliza</a><?
										}
										?>
										<input accept="image/*,application/pdf" class="file" type="file" id="txt-file-poliza" name="txt-file-poliza" capture="camera">
									</div>
								</div>
							</div>
							<div class="row form-group datos-depreciacion">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3><small>Datos Referentes al Ejercicio</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>					
							<div class="row form-group datos-depreciacion">
								<div class="col-md-6 col-xs-12">
									<label>Porcentaje de Depreciación</label>
									<select class="form-control" id="sl-depreciacion" name="sl-depreciacion" data-live-search="true">
										<option value="0">--</option>
										<?
										if(isset($catDepreciacion)){
											foreach($catDepreciacion as $dep){
												?><option value="<?=$dep->id?>" <?=$dep->vidaUtil>0?'':'disabled="disabled"'?> data-vida-util="<?=$dep->vidaUtil?>" data-depreciacion-anual="<?=$dep->depreciacionAnual?>" data-tipo="<?=$dep->tipo->id?>" <?=$bien->depreciacion->id==$dep->id?'selected="selected"':''?> ><?=$dep->cuenta." - ".strtoupper($dep->descr).($dep->depreciacionAnual>0?" (".$dep->depreciacionAnual."%)":'')?></option><?
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Depreciación del Periodo</label>
									<input type="text" class="form-control" id="txt-depreciacion-periodo" name="txt-depreciacion-periodo" value="<?=$bien->depreciacionPeriodo?>" readonly="readonly">
								</div>
							</div>
							<div class="row form-group datos-depreciacion">
								<div class="col-md-3 col-xs-12">
									<label>Depreciación Acumulada</label>
									<input type="text" class="form-control" id="txt-depreciacion-acumulada" name="txt-depreciacion-acumulada" value="<?=$bien->depreciacionAcumulada?>" readonly="readonly">									
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Valor Actual (después de Depreciación)</label>
									<input type="text" class="form-control" id="txt-valor-actual" name="txt-valor-depreciado" value="" readonly="readonly">									
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3 class="text-center"><small>Valuación del Bien</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-5 col-md-offset-2">
									<div class="row">
										<div class="col-md-6 col-xs-12 form-group">
											<select class="form-control" id="sl-tipo-valuacion" name="sl-tipo-valuacion">
												<option value="<?=Bien::VALUACION_IMPORTE?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_IMPORTE?'selected="selected"':''?> > IMPORTE </option>
												<option value="<?=Bien::VALUACION_REPOSICION?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_REPOSICION?'selected="selected"':''?> > VALOR DE REPOSICIÓN </option>
												<option value="<?=Bien::VALUACION_REEMPLAZO?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_REEMPLAZO?'selected="selected"':''?> > VALOR DE REEMPLAZO </option>
												<option value="<?=Bien::VALUACION_DESECHO?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_DESECHO?'selected="selected"':''?> > VALOR DE DESHECHO </option>
											</select>
										</div>
										<div class="col-md-6 col-xs-12">
											<input type="text" class="form-control" id="txt-valuacion" name="txt-valuacion" value="<?=$bien->valor?>" placeholder="$ 0.00">
										</div>
									</div>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<!-- <div class="row form-group datos-reevaluacion">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3 class="text-center"><small>Re-evaluación del Bien</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group datos-reevaluacion">
								<div class="col-md-5 col-md-offset-2">
									<div class="row">
										<div class="col-md-6 col-xs-12 form-group">
											<select class="form-control" id="sl-tipo-valuacion-final" name="sl-tipo-valuacion-final">
												<option value="<?=Bien::VALUACION_REPOSICION?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_REPOSICION?'selected="selected"':''?> > VALOR DE REPOSICIÓN </option>
												<option value="<?=Bien::VALUACION_REEMPLAZO?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_REEMPLAZO?'selected="selected"':''?> > VALOR DE REEMPLAZO </option>
												<option value="<?=Bien::VALUACION_DESECHO?>" <?=$bien->tipoValuacion->id==Bien::VALUACION_DESECHO?'selected="selected"':''?> > VALOR DE DESHECHO </option>
											</select>
										</div>
										<div class="col-md-6 col-xs-12">
											<input type="text" class="form-control" id="txt-valuacion-final" name="txt-valuacion-final" value="<?=$bien->valorFinal?>" placeholder="$ 0.00">
										</div>
									</div>
								</div>
							</div> -->
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3 class="text-center"><small>Imagenes</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group content-item-previews" id="div-content-sortable">
								<div class="col-md-2 item-preview-img preview-img item-dummy">
									<img src="" class="img-dummy" data-index="0" data-saved="0">
									<a class="btn-editar-imagen edit"><i class="fa fa-edit"></i></a>
									<a class="btn-cerrar-imagen close"><i class="fa fa-close"></i></a>
									<a class="waiting-panel hidden"><i class="fa fa-refresh fa-spin"></i></a>
								</div>
								<?
								if(isset($bien->images) && count($bien->images)>0){
									if(isset($bien->imagen) && trim($bien->imagen)!=""){
										$imagedata = file_get_contents($settings->prop("system.url").$bien->imagen);
										?>
										<div class="col-md-2 item-preview-img preview-img copyable">
											<img src="<?="data:image/jpeg;base64,".base64_encode($imagedata)?>" data-index="0" data-saved="1">
											<a class="btn-editar-imagen edit"><i class="fa fa-edit"></i></a>
											<a class="btn-cerrar-imagen close"><i class="fa fa-close"></i></a>
											<a class="waiting-panel hidden"><i class="fa fa-refresh fa-spin"></i></a>
										</div>
										<?
									}
									for($j=0;$j<count($bien->images);$j++){
										if($bien->images[$j]!=$bien->imagen){	
											$img = $bien->images[$j];
											$imagedata = file_get_contents($settings->prop("system.url").$img);
											?>
											<div class="col-md-2 item-preview-img preview-img copyable">
												<img src="<?="data:image/jpeg;base64,".base64_encode($imagedata)?>" data-index="<?=$j?>" data-saved="1">
												<a class="btn-editar-imagen edit"><i class="fa fa-edit"></i></a>
												<a class="btn-cerrar-imagen close"><i class="fa fa-close"></i></a>
												<a class="waiting-panel hidden"><i class="fa fa-refresh fa-spin"></i></a>
											</div>
											<?
										}
									}
								}
								?>
							</div>
					   		<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<input accept="image/*" type="file" id="txt-file" name="txt-file" multiple>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-2">
									<button id="btn-regresar" class="btn btn-success form-control"><i class="fa fa-reply"></i> Listado</button>
								</div>
								<div class="col-md-3 col-md-offset-1">
									<?
									if($misesion->perfil->id==Usuario::PERFIL_ADMIN||$misesion->perfil->id==Usuario::PERFIL_CAPTURISTA){
										?>
										<button id="btn-guardar" class="btn btn-primary form-control"><i class="fa fa-check"></i> Guardar</button>
										<?
										}
									?>
								</div>
								<!-- <div class="col-md-2">
									<button id="btn-test"><i class="fa fa-copy"></i>Copiar</button>
								</div> -->
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
        <!-- footer content -->
      </div>
    </div>
    <!-- VENTANA MODAL -->
    <div id="modal-editar-img" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="content-img-modal">
						<img class="articulo-imagen" id="image-edit" data-index="" src="<?=$settings->prop("system.url").$bien->imagen?>">
						<!-- <img class="articulo-imagen" id="image-edit" src="files/comprobante_pago.jpg"> -->
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-6 col-md-offset-3">
								<button class="btn btn-success btn-rotate-left"><i class="fa fa-rotate-left"></i></button>
								<?
								if($misesion->perfil->id==Usuario::PERFIL_ADMIN||$misesion->perfil->id==Usuario::PERFIL_CAPTURISTA){
									?>
									<button class="btn btn-primary btn-save-img">Cortar</button>
									<?
								}
								?>
								<button class="btn btn-success btn-rotate-rigth"><i class="fa fa-rotate-right"></i></button>
							</div>
						</div>						
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- TERMINA VENTANA MODAL -->
    <!-- MODAL AGREGAR COLOR -->
    <div id="modal-agregar-color" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Agregar Color</h3>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-2">
								<label>Color</label>
							</div>
							<div class="col-md-10">
								<input type="text" class="form-control" id="txt-color-modal" name="txt-color-modal">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary btn-agregar-color">Agregar</button>
					<button class="btn btn-danger btn-cancelar-color">Cancelar</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- TERMINA MODAL AGREGAR COLOR -->
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
    <script src="bien.js"></script>
  </body>
</html>