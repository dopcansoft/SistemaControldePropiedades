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
	$origen = isset($data["origen"])?$data["origen"]:"";

	//$log->debug($periodo);

	$today = new DateTime();								
    $list = array();
    $bien = new BienDao();
    $catBienesInmuebles = array();
    $catBienesMuebles = array();
    $catValores = array();
    $departamentos = array();
    $catDepreciacion = array();
    $catEdoFisico = array();
    $catOrigen = array();
    $catUma = array();
    $colores = array();
    $banderas = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])){
        		$bien->id = isset($data["id"])?$data["id"]:"";
        		$bien->periodo->id = $periodo->id;
   	     		$bien->find($db, $queries, $log);
        	}else{
        		if(!trim($bien->folioUnico)!=""){
        			$log->debug('generando folio unico, empresa: '.$empresa->id);
        			$bien->getUnico($empresa->id, $db, $queries, $log);	
        			$log->debug('numero: '.$bien->numero);
        			$log->debug('folio unico: '.$bien->folioUnico);
        		}
        		
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
        	$banderaFactory = new BanderaFactory();
        	$banderas = $banderaFactory->allWithDetails($empresa->id, $periodo->id, $db, $queries, $log);
        	$responsableFactory = new ResponsableFactory();
        	$responsables = $responsableFactory->listAll($empresa->id, $db, $queries, $log);
        	$periodoFactory = new PeriodoFactory();
        	$periodos = $periodoFactory->listAll($empresa, $db, $queries, $log);
        	$catalogoFactory = new CatalogoFactory();
        	$deptosFactory = new DepartamentoFactory();
        	$colores = $catalogoFactory->listParams($db, $queries->prop("color.list"), array(
        		array(":fk_id_empresa", $empresa->id, PDO::PARAM_INT)
        	), $settings, $log, "ItemCatalogo");
        	$catBienesInmuebles = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
        	//$catBienesMuebles = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$catBienesMuebles = $catalogoFactory->listParams($db, $queries->prop("catbienesmuebles.listbyfilter"), array(
        		array(":fk_id_empresa", $empresa->id, PDO::PARAM_INT),
        		array(":fk_id_periodo", $periodo->id, PDO::PARAM_INT)
        	), $settings, $log, "Clasificacion");
					$catValores = $catalogoFactory->listado($db, $queries->prop("cattipovaluacion.list"), $settings, $log, "ItemCatalogo");
					$departamentos = $deptosFactory->listAll($empresa->id, $db, $queries, $log); 
					$catUma = $catalogoFactory->listado($db, $queries->prop("catuma.list"), $settings, $log, "Uma");
					$catOrigen = $catalogoFactory->listado($db, $queries->prop("catorigen.list"), $settings, $log, "ItemCatalogo");
					$depreciacionFactory = new DepreciacionFactory();
					$catDepreciacion = $depreciacionFactory->listAll($db, $queries, $log);
					$catEdoFisico = $catalogoFactory->listParams($db, $queries->prop("catedofisico.list"), array(
						array(":fk_id_empresa", $empresa->id, PDO::PARAM_INT)
					),$settings, $log, "ItemCatalogo");
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
    <!--<link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet"> -->

    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->

    <link rel="stylesheet" href="build/bootstrap-select/css/bootstrap-select.css">
    <!-- <link rel="stylesheet" href="css/bootstrap-datepicker.css"> -->
    <!-- bootstrap-datetimepicker -->
    <link rel="stylesheet" href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">

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

	    input.input-calendar:disabled{
	    	background-color: #EEE !important;
	    	cursor: default;
	    }

	    input.input-calendar:read-only{
	    	cursor: pointer;
	    	background-color:#FFF;
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
    <input type="hidden" name="cierre" id="cierre" value="<?=$periodo->fechaCierre?>">
    <input type="hidden" name="origen" id="origen" value="<?=$origen?>">
		<input type="hidden" name="tipo_etiqueta" id="tipo_etiqueta" value="<?=$empresa->tipoEtiqueta?>">    
    <input type="hidden" name="uma-periodo" id="uma-periodo" value="<?=$periodo->uma->id?>">
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
								<div class="col-md-3 col-xs-12">
									<label>Folio</label>
									<input type="text" class="form-control" id="txt-id" name="txt-id" value="<?=$bien->folio?>" data-counter="<?=$bien->consecutivo?>" readonly="readonly">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>N??mero ??nico</label>
									<input type="text" class="form-control" id="txt-unico" name="txt-unico" value="<?=$bien->folioUnico?>" data-numero="<?=$bien->numero?>" readonly="readonly">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Folio Anterior</label>
									<input type="text" class="form-control" id="txt-folio-anterior" name="txt-folio-anterior" value="<?=$bien->folioAnterior?>">
									<!-- <label>Folio Externo</label>
									<input type="text" class="form-control" id="txt-folio-alt" name="txt-folio-alt" value="<?=$bien->folioAlt?>"> -->
								</div>
								<!-- <div class="col-md-3 col-xs-12">
									<label>Consecutivo</label>
									<input type="text" class="form-control" id="txt-consecutivo" name="txt-consecutivo" value="<?=$bien->numero?>" readonly="readonly">
								</div> -->
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label for="sl-departamento">Departamento</label>
									<select tabindex="1" class="form-control" id="sl-departamento" name="sl-departamento">
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
									<label for="sl-clasificacion">Clasificaci??n Armonizada</label>
									<select tabindex="2" class="form-control selectpicker show-tick" id="sl-clasificacion" name="sl-clasificacion" data-live-search="true">
										<option value="0">--</option>
										<optgroup label="BIENES MUEBLES">
											<?
											if(isset($catBienesMuebles)&&count($catBienesMuebles)>0){
												foreach($catBienesMuebles as $cbm){
													?><option data-grupo="<?=$cbm->grupo?>" data-subgrupo="<?=$cbm->subgrupo?>" data-clase="<?=$cbm->clase?>" data-subclase="<?=$cbm->subclase?>" value="<?=$cbm->id?>" <?=$cbm->clase=="0"?'disabled="disabled"':''?> data-tipo="<?=$cbm->tipo->id?>" data-cc="<?=$cbm->cuentaContable?>" data-cd="<?=$cbm->cuentaDepreciacion?>" <?=$bien->clasificacion->id==$cbm->id&&$bien->tipoClasificacion->id==$cbm->tipo->id?'selected="selected"':''?> <?=$cbm->enabled=="0"?'disabled="disabled"':''?> ><?=$cbm->grupo.$cbm->subgrupo.$cbm->clase.$cbm->subclase.$cbm->consecutivo." - ".strtoupper($cbm->descr).($cbm->enabled=="0"?' (Deshabilitado)':'')?></option><?
												}
											}
											?>
										</optgroup>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Cuenta Contable</label>
									<input type="text" class="form-control" id="txt-cuenta-contable" name="txt-cuenta-contable" readonly="readonly" value="<?=$bien->clasificacion->cuentaContable?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Cuenta Depreciaci??n</label>
									<input type="text" class="form-control" id="txt-cuenta-depreciacion" name="txt-cuenta-depreciacion" readonly="readonly" value="<?=$bien->clasificacion->cuentaDepreciacion?>">
								</div>
								
								
								
								
								
								<div class="col-md-3 col-xs-12">
									<label for="sl-bandera">Bandera</label>
									<div class="input-group"> 
										<select tabindex="5" class="form-control" id="sl-bandera" name="sl-bandera">
											<option value="0"> -- </option>
											<?
												if(isset($banderas) && count($banderas)>0){
													foreach($banderas as $bandera){
														?><option value="<?=$bandera->id?>" <?=$bien->bandera->id==$bandera->id?'selected="selected"':''?>><?=$bandera->descr?></option><?
													}
												}
											?>
										</select> 

									</div>
								
								
								
								
								
								
								
							</div>
							<div class="row form-group">
								<div class="col-md-9 col-xs-12">
									<label for="txt-descripcion">Descripci??n</label>
									<textarea tabindex="4" class="form-control" rows="2" id="txt-descripcion" name="txt-descripcion"><?=$bien->descripcion?></textarea>
									<br>
									<button class="btn btn-md btn-success" id="btn-descripcion-listen"> <i class="fa fa-microphone"></i> <span>Escuchar</span> </button>
								</div>
							</div>
							<div class="row">&nbsp;</div>
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
									<label>Color</label>
									<div class="input-group"> 
										<select tabindex="5" class="form-control" id="sl-color" name="sl-color">
											<option value=""> -- </option>
											<option value="0">NO APLICA</option>
											<?foreach($colores as $color){
												?><option value="<?=$color->id?>" <?=$bien->color->id==$color->id?'selected="selected"':'' ?> ><?=$color->descr?></option><?
											}
											?>
										</select> 
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right dm-color"> 
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
									<label for="txt-marca">Marca</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-marca-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input tabindex="6" class="form-control" id="txt-marca" name="txt-marca" value="<?=isset($bien->marca)&&$bien->marca!=""?$bien->marca:"NO IDENTIFICADO"?>" autocomplete="off"> 
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
									<label for="txt-modelo" >Modelo</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-modelo-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input tabindex="7" type="text" class="form-control" id="txt-modelo" name="txt-modelo" value="<?=isset($bien->modelo)&&$bien->modelo!=""?$bien->modelo:"NO IDENTIFICADO"?>" autocomplete="off">
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
									<label for="txt-serie">Serie</label>
									<!-- <input type="text" class="form-control" id="txt-serie" name="txt-serie" value="<?=$bien->serie?>"> -->
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-serie-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input tabindex="8" type="text" class="form-control" autocomplete="off" id="txt-serie" name="txt-serie" value="<?=isset($bien->serie)&&$bien->serie!=""?$bien->serie:"NO IDENTIFICADO"?>">
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
									<label for="sl-origen">N??mero de Placa</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-matricula-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input tabindex="9" type="text" class="form-control" id="txt-matricula" name="txt-matricula" value="<?=isset($bien->matricula)&&$bien->matricula!=""?$bien->matricula:"NO APLICA"?>" autocomplete="off">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button>
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-matricula">NO IDENTIFICADO</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="NO LEGIBLE" data-input="txt-matricula">NO LEGIBLE</a>
												</li> 
												<li>
													<a href="#" class="opts-input" data-value="GENERICO" data-input="txt-matricula">GENERICO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="NO APLICA" data-input="txt-matricula">NO APLICA</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-matricula"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<label for="txt-motor">Motor</label>
									<!-- <input type="text" class="form-control" id="txt-motor" name="txt-motor" value="<?=$bien->motor?>"> -->
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-motor-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input tabindex="10" type="text" class="form-control" id="txt-motor" name="txt-motor" value="<?=isset($bien->motor)&&$bien->motor!=""?$bien->motor:"NO APLICA"?>" autocomplete="off">
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
									<label for="sl-origen">Fondo de Origen de Adquisici??n</label>
									<select tabindex="13" class="form-control" id="sl-origen" name="sl-origen">
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
									<label for="sl-responsable">Responsable</label>
									<div class="input-group"> 
										<select tabindex="5" class="form-control" id="sl-responsable" name="sl-responsable">
											<option value="0"> -- </option>
											<?
												if(isset($responsables) && count($responsables)>0){
													foreach($responsables as $responsable){
														?><option value="<?=$responsable->id?>" <?=$bien->responsable->id==$responsable->id?'selected="selected"':''?>><?=$responsable->titulo." ".$responsable->nombre." ".$responsable->apellido?></option><?
													}
												}
											?>
										</select> 
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right dm-responsable"> 
												<li>
													<a href="#" class="opts-item" data-value="AGREGAR" data-input="sl-responsable"><i class="fa fa-plus"></i> AGREGAR</a>
												</li> 
											</ul> 
										</div> 
									</div>	
								</div>
								<div class="col-md-3 col-xs-12">
								<label for="txt-factura">Factura</label>
								<div class="input-group"> 
									<div class="input-group-btn">
										<button id="btn-factura-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
									</div>
									<input tabindex="9" type="text" class="form-control" id="txt-factura" name="txt-factura" value="<?=isset($bien->factura)&&$bien->factura!=""?$bien->factura:"NO IDENTIFICADO"?>" autocomplete="off">
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
							</div>
							<div class="row form-group">
								<div class="col-md-9 col-xs-12">
									<label for="txt-notas">Notas Extras</label>
									<textarea tabindex="15" class="form-control" rows="5" id="txt-notas" name="txt-notas"><?=$bien->notas?></textarea>
									<br>
									<button class="btn btn-md btn-success" id="btn-notas-listen"> <i class="fa fa-microphone"></i> <span>Escuchar</span> </button>
								</div>
							</div>
							
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3><small>Informaci??n complementaria</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>	
							<div class="row form-group">
								<div class="col-md-5 col-xs-12">
									<div class="row">
										<label for="txt-file-factura">Archivo de la Factura</label>
									</div>
									<div class="row">
										<?
										if($bien->archivoFactura!=""){
											?><a href="<?=$settings->prop("system.url").$bien->archivoFactura?>" style="margin-bottom:10px !important; line-height:3em !important; text-decoration: underline !important; vertical-align: super !important;">Ver Factura</a><?
										}
										?>
										<input tabindex="16" accept="image/*,application/pdf" class="file" type="file" id="txt-file-factura" name="txt-file-factura" capture="camera">
									</div>
								</div>	
								<div class="col-md-5 col-xs-12">
									<div class="row">
										<label for="txt-file-poliza">Archivo de la Poliza</label>
									</div>
									<div class="row">
										<?
										if($bien->archivoPoliza!=""){
											?><a href="<?=$settings->prop("system.url").$bien->archivoPoliza?>" style="margin-bottom:10px !important; line-height:3em !important; text-decoration: underline !important; vertical-align: super !important;">Ver Poliza</a><?
										}
										?>
										<input tabindex="17" accept="image/*,application/pdf" class="file" type="file" id="txt-file-poliza" name="txt-file-poliza" capture="camera">
									</div>
								</div>
							</div>
							
							<!-- 
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
							</div> -->

							<!-- <div class="row form-group datos-depreciacion">
								<div class="col-md-6 col-xs-12">
									<label for="sl-depreciacion" >Porcentaje de Depreciaci??n</label>
									<select tabindex="18" class="form-control" id="sl-depreciacion" name="sl-depreciacion" data-live-search="true">
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
									<label>Depreciaci??n del Periodo</label>
									<input type="text" class="form-control" id="txt-depreciacion-periodo" name="txt-depreciacion-periodo" value="<?=$bien->depreciacionPeriodo?>" readonly="readonly">
								</div>
							</div>
							<div class="row form-group datos-depreciacion">
								<div class="col-md-3 col-xs-12">
									<label>Depreciaci??n Acumulada</label>
									<input type="text" class="form-control" id="txt-depreciacion-acumulada" name="txt-depreciacion-acumulada" value="<?=$bien->depreciacionAcumulada?>" readonly="readonly">									
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Valor Actual (despu??s de Depreciaci??n)</label>
									<input type="text" class="form-control" id="txt-valor-actual" name="txt-valor-depreciado" value="" readonly="readonly">									
								</div>
							</div> -->
							<?
							if(isset($bien->valuaciones)&&count($bien->valuaciones)>0){
								foreach($bien->valuaciones as $item){
									$fecha = date_format(date_create($item->fecha.' 00:00:00'),'d-m-Y');
									$fechaCierre = date_format(date_create($item->fechaCierre.' 00:00:00'),'d-m-Y');
									?>
									<input type="hidden" class="item-valuacion" id="item<?=$item->id?>" name="item<?=$item->id?>" value="<?=$item->id?>"
										data-periodo="<?=$item->periodo->id?>"
										data-valor="<?=$item->valor?>"
										data-orden="<?=$item->orden?>"
										data-tipo="<?=$item->tipo->id?>"
										data-tipo-descr="<?=$item->tipo->descr?>"
										data-fecha="<?=$fecha?>"
										data-fecha-cierre="<?=$fechaCierre?>"
										data-dep-acumulada="<?=$item->depAcumulada?>"
										data-dep-periodo="<?=$item->depPeriodo?>"
										data-anios-uso="<?=$item->aniosUso?>"
										data-depreciacion="<?=$item->depreciacion->id?>"
										data-depreciacion-descr="<?=$item->depreciacion->descr?>"
										data-depreciacion-cuenta="<?=$item->depreciacion->cuenta?>"
										data-depreciacion-vida-util="<?=$item->depreciacion->vidaUtil?>"
										data-depreciacion-dep-anual="<?=$item->depreciacion->depreciacionAnual?>"
										data-uma="<?=$item->uma->id?>"
										data-uma-anio="<?=$item->uma->anio?>"
										data-uma-valor-diario="<?=$item->uma->valorDiario?>"
										data-uma-valor-mensual="<?=$item->uma->valorMensual?>"
										data-uma-valor-anual="<?=$item->uma->valorAnual?>"
										data-uma-factor="<?=$item->uma->factor?>"
										data-valor-libros="<?=$item->valorLibros?>"
										data-valor-actual="<?=$item->valorActual?>"
										data-estado-fisico="<?=$item->estadoFisico->id?>"
										data-contable="<?=$item->contable->id?>"
										data-contable-descr="<?=$item->contable->descr?>"
										data-fecha-insert="<?=$item->fechaInsert?>">
									<?	
								}								
							}
							?>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3 col-xs-12">
									<h3 class="text-center"><small>Valuaci??n del Bien</small></h3>								
								</div>
								<div class="col-md-3 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3">
									<button class="btn btn-primary btn-agregar-valor"><i class="fa fa-plus"></i> Agregar Valuaci??n</button>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="valores table table-striped table-responsive">
											<thead>
												<tr class="headings">
													<th class="column-title">Tipo</th>
													<th class="column-title">Valor</th>
													<th class="column-title">Fecha</th>
													<th class="column-title">Depreciaci??n</br>Periodo</th>
													<th class="column-title">Depreciaci??n</br>Acumulada</th>
													<th class="column-title">Fecha Cierre</th>
													<th class="column-title">Valor Libros</th>
													<th class="column-title">Valor Actual</th>
													<th class="column-title">&nbsp;</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
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
									<img src="" class="img-dummy" data-index="<?=count($bien->images)?>" data-saved="0">
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
												<img src="<?="data:image/jpeg;base64,".base64_encode($imagedata)?>" data-index="<?=($j+1)?>" data-saved="1">
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
									<input tabindex="21" accept="image/*" type="file" id="txt-file" name="txt-file" multiple>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-2">
									<button tabindex="23" id="btn-regresar" class="btn btn-success form-control"><i class="fa fa-reply"></i> Listado</button>
								</div>
								<div class="col-md-3 col-md-offset-1">
									<?
									if($misesion->perfil->id==Usuario::PERFIL_ADMIN||$misesion->perfil->id==Usuario::PERFIL_CAPTURISTA){
										?>
										<button tabindex="22" id="btn-guardar" class="btn btn-primary form-control"><i class="fa fa-check"></i> <span class="btn-span">Guardar</span></button>
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

	<!-- MODAL AGREGAR COLOR -->
    <div id="modal-agregar-valor" name="modal-agregar-valor" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Agregar Valuaci??n</h3>
            <input type="hidden" id="current-index" name="current-index" value="0" >
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row form-group">
							<div class="col-md-3">
								<label>Tipo Valuaci??n</label>
							</div>
							<div class="col-md-9">
								<select class="form-control" id="sl-tipo-valuacion-modal" name="sl-tipo-valuacion-modal">
									<option value="0"> -- </option>
								<?
								if(count($catValores)>0){
									foreach($catValores as $item){
										?><option value="<?=$item->id?>"><?=$item->descr?></option><?
									}
								}
								?>	
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-3">
								<label>Importe</label>
							</div>
							<div class="col-md-9">
								<input class="form-control" typ="text" id="txt-valor-modal" name="txt-valor-modal" value="">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-3">
								<label class="label-fecha">Fecha</label>
							</div>
							<div class="col-md-9">
								<div class='input-group date' id='myFechaAdquisicion'>
                    <input type='text' class="form-control" id="txt-fecha-adquisicion-modal" name="txt-fecha-adquisicion-modal" />
                    <span class="input-group-addon">
                       <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
								<!-- <div class="input-group"> 
									<input type="text" class="form-control" id="txt-fecha-adquisicion-modal" name="txt-fecha-adquisicion-modal" placeholder="" readonly="false">
									 <div class="input-group-btn"> 
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
											<span class="caret"></span> 
											<span class="sr-only">Toggle Dropdown</span> 
										</button> 
										<ul class="dropdown-menu dropdown-menu-right"> 
											<li>
												<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-fecha-adquisicion-modal">NO IDENTIFICADO</a>
											</li> 
											<li>
												<a href="#" class="opts-input" data-value="<?=$today->format('d-m-Y')?>" data-input="txt-fecha-adquisicion-modal"><i class="fa fa-edit"></i> EDITAR</a>
											</li> 
										</ul> 
									</div>  
								</div> -->
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-3">
								<label>Estado F??sico</label>
							</div>
							<div class="col-md-9">
								<select class="form-control" id="sl-edo-fisico-modal" name="sl-edo-fisico-modal">
									<option value="0"> -- </option>
									<?
										if(isset($catEdoFisico) && count($catEdoFisico)>0){
											foreach($catEdoFisico as $cef){
												?><option value="<?=$cef->id?>"><?=$cef->descr?></option><?
											}
										}
										?>
								</select>	
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-3">
								<label>Fecha de Cierre</label>
							</div>
							<div class="col-md-9">
								<div class="input-group"> 
									<input type="text" class="form-control input-calendar" id="txt-fecha-cierre-modal" name="txt-fecha-cierre-modal" value="<?=date_format(date_create($periodo->fechaCierre.' 00:00:00'),'d-m-Y')?>" readonly="readonly" disabled="disabled">
									<div class="input-group-btn"> 
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
											<span class="caret"></span> 
											<span class="sr-only">Toggle Dropdown</span> 
										</button> 
										<ul class="dropdown-menu dropdown-menu-right dm-fecha-cierre"> 
											<li>
												<a href="#" class="opts-item" data-instruction="EDITAR" data-value="<?=date_format(date_create($periodo->fechaCierre.' 00:00:00'),'d-m-Y')?>" data-input="txt-fecha-cierre-modal"><i class="fa fa-edit"></i> EDITAR</a>
											</li> 
										</ul> 
									</div> 
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-2">
								<label>UMA</label>
							</div>
							<div class="col-md-10">
								<select class="form-control" id="sl-uma-modal" name="sl-uma-modal">
									<option value="0"> -- </option>
									<?
									foreach($catUma as $uma){
										?><option data-factor="<?=$uma->factor?>" data-valor-diario="<?=$uma->valorDiario?>" data-anio="<?=$uma->anio?>" value="<?=$uma->id?>" <?=($uma->id==$bien->uma->id||$bien->uma->id==''&&$uma->anio==$today->format("Y")?'selected="selected"':'')?> ><?=$uma->anio." (".$uma->valorDiario*$uma->factor.")"?></option><?
									}
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-4">
								<label>Tipo de Depreciaci??n</label>
							</div>
							<div class="col-md-8">
								<select class="form-control" id="sl-tipo-depreciacion-modal" name="sl-tipo-depreciacion-modal">
									<option value="0"> -- </option>
									<?
									if(isset($catDepreciacion)){
										foreach($catDepreciacion as $dep){
											?><option value="<?=$dep->id?>" <?=$dep->seleccionable=="0"?'disabled="disabled"':''?> data-vida-util="<?=$dep->vidaUtil?>" data-depreciacion-anual="<?=$dep->depreciacionAnual?>" data-tipo="<?=$dep->tipo->id?>" data-descr="<?=$dep->tipo->descr?>" data-cuenta="<?=$dep->tipo->cuenta?>" <?=$bien->depreciacion->id==$dep->id?'selected="selected"':''?> ><?=$dep->cuenta." - ".strtoupper($dep->descr).($dep->depreciacionAnual>0?" (".$dep->depreciacionAnual."%)":'')?></option><?
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-5">
								<label>Depreciaci??n del Periodo</label>
							</div>
							<div class="col-md-7">
								<input class="form-control" typ="text" id="txt-depreciacion-periodo-modal" name="txt-depreciacion-periodo-modal" disabled="disabled" value="0">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-5">
								<label>Depreciaci??n Acumulada</label>
							</div>
							<div class="col-md-7">
								<input class="form-control" id="txt-depreciacion-acumulada-modal" name="txt-depreciacion-acumulada-modal" typ="text" disabled="disabled" value="0">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-5">
								<label>A??os de Uso</label>
							</div>
							<div class="col-md-7">
								<input class="form-control" id="txt-anios-uso-modal" name="txt-anios-uso-modal" typ="text" disabled="disabled" value="0">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-4">
								<label>Valor en Libros</label>
								<input class="form-control" id="txt-valor-libros-modal" name="txt-valor-libros-modal" type="text" readonly="readonly" value="0">	
							</div>
							<div class="col-md-4">
								<label>Valor Actual</label>
								<input class="form-control" id="txt-valor-actual-modal" name="txt-valor-actual-modal" type="text" readonly="readonly" value="0">								
							</div>
							<div class="col-md-4">
								<label>Clasificaci??n</label>
								<input class="form-control" id="txt-contable-modal" name="txt-contable-modal" type="text" readonly="readonly" data-inventario-contable="0" value="INSTRUMENTAL">								
							</div>
						</div>
            <div class="row form-group">
                <div class="col-md-12">
                    <div class="alert">Este bien ya se devaluo</div>        
                </div>
            </div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary btn-agregar-valor-confirmacion">Agregar</button>
					<button class="btn btn-danger btn-cancelar-valor-cancelar">Cancelar</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- TERMINA MODAL AGREGAR COLOR -->


	<!-- MODAL AGREGAR RESPONSABLE -->
    <div id="modal-agregar-responsable" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Agregar Responsable</h3>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-2">
								<label>Titulo</label>
							</div>
							<div class="col-md-10">
								<input type="text" class="form-control" id="txt-titulo-modal" name="txt-titulo-modal" placeholder="Lic.">
							</div>
						</div>
						<div class="row">&nbsp;</div>
						<div class="row">
							<div class="col-md-2">
								<label>Nombre</label>
							</div>
							<div class="col-md-10">
								<input type="text" class="form-control" id="txt-nombre-modal" name="txt-nombre-modal">
							</div>
						</div>
						<div class="row">&nbsp;</div>
						<div class="row">
							<div class="col-md-2">
								<label>Apellidos</label>
							</div>
							<div class="col-md-10">
								<input type="text" class="form-control" id="txt-apellidos-modal" name="txt-apellidos-modal">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary btn-agregar-responsable">Agregar</button>
					<button class="btn btn-danger btn-cancelar-responsable">Cancelar</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- TERMINA MODAL AGREGAR RESPONSABLE -->

    <!-- jQuery -->
    <script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="vendors/jquery-ui/jquery-ui.js"></script>

    <!-- Bootstrap -->
    <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="build/bootstrap-select/js/bootstrap-select.js"></script>
    
    <!-- FastClick -->
    <!-- <script type="text/javascript" src="vendors/fastclick/lib/fastclick.js"></script>  -->
    <!-- NProgress -->
    <script type="text/javascript" src="vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <!-- <script type="text/javascript" src="vendors/iCheck/icheck.min.js"></script> -->
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
    <!-- <script type="text/javascript" src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script type="text/javascript" src="vendors/jszip/dist/jszip.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/vfs_fonts.js"></script> -->

    <!-- NOTIFICACIONES -->
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <script type="text/javascript" src="vendors/cropper/dist/cropper.js"></script>
   
    <script type="text/javascript" src="js/bootstrap-datepicker-new.js"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
    <script type="text/javascript" src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    	
    <script type="text/javascript" src="js/jquery.mask.js"></script>
    <!-- Custom Theme Scripts -->
    <!-- <script src="vendors/jquery.camera/jquery.camera_capture.js"></script> -->
    <!-- <script src="js/webcamjs-1.025.js"></script> -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="bien.js"></script>
  </body>
</html>