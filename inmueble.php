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
    
    $tipos = array();
    $clasificacion = array();
    $fondoOrigen = array();
	$inmueble = new BienInmuebleDao(); 
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])&&$data["id"]!=""){
        		$inmueble->id = $data["id"];
        		$inmueble->find($db, $queries, $log);
        	}        	        	
        	$catalogoFactory = new CatalogoFactory();        	
        	$tipos = $catalogoFactory->listado($db, $queries->prop("cat.tipoinmueble"), $settings, $log, "ClasificacionInmueble");
        	$clasificacion = $catalogoFactory->listado($db, $queries->prop("cat.tipoinmueble.all"), $settings, $log, "ClasificacionInmueble");
        	$fondoOrigen = $catalogoFactory->listado($db, $queries->prop("catorigen.list"), $settings, $log, "ItemCatalogo");
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
    <!-- <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
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
							<h3><small>Detalle del Inmueble</small></h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="inmueble" name="inmueble" value="<?=$inmueble->id?>">
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<input type="hidden" id="folio" name="folio" value="<?=$inmueble->folio?>">
							<input type="hidden" id="consecutivo" name="consecutivo" value="<?=$inmueble->consecutivo?>">
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label for="txt-folio">Folio</label>
									<input type="text" id="txt-folio" name="txt-folio" class="form-control" disabled="disabled" value="<?=$inmueble->folio?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label for="sl-tipo-inmueble">Tipo de inmueble</label>
									<select tabindex="1" class="form-control" id="sl-tipo-inmueble" name="sl-tipo-inmueble" data-required="1">
										<option value="0">--</option>
										<?
										foreach($tipos as $tipo){
											?>
											<option value="<?=$tipo->id?>" data-cuenta-contable="<?=$tipo->cuentaContable?>" <?=$inmueble->tipo->id==$tipo->id?'selected="selected"':''?> ><?=$tipo->descr?></option>
											<?
										}
										?>
									</select>
								</div>
								<div class="col-md-6 col-xs-12">
									<label for="sl-clasificacion-inmueble">Clasificación</label>
									<select tabindex="1" class="form-control" id="sl-clasificacion-inmueble" name="sl-clasificacion-inmueble" data-required="1">
										<option value="0">--</option>
										<?
										foreach($clasificacion as $item){
											?>
											<option value="<?=$item->id?>" data-consecutivo="<?=$item->consecutivo?>" data-cuenta-contable="<?=$item->cuentaContable?>" <?=$inmueble->clasificacion->id==$item->id?'selected="selected"':''?>><?=strtoupper($item->descr)?></option>
											<?
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-4 col-xs-12">
									<h3 class="text-center"><small>IDENTIFICACION DEL INMUEBLE</small></h3>								
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>					
							<div class="row form-group">
								<div class="col-md-6 col-xs-12">
									<label>Descripción</label>
									<input type="text" class="form-control" id="txt-descripcion" name="txt-descripcion" value="<?=$inmueble->descr?>">
								</div>
								<div class="col-md-6 col-xs-12">
									<label>Ubicación</label>
									<input type="text" class="form-control" id="txt-ubicacion" name="txt-ubicacion" value="<?=$inmueble->ubicacion?>">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-4 col-xs-12">
									<h3 class="text-center"><small>MEDIDAS</small></h3>								
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Norte</label>
									<input type="text" class="form-control" id="txt-medida-norte" name="txt-medida-norte" value="<?=$inmueble->medNorte?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Sur</label>
									<input type="text" class="form-control" id="txt-medida-sur" name="txt-medida-sur" value="<?=$inmueble->medSur?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Este</label>
									<input type="text" class="form-control" id="txt-medida-este" name="txt-medida-este" value="<?=$inmueble->medOeste?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Oeste</label>
									<input type="text" class="form-control" id="txt-medida-oeste" name="txt-medida-oeste" value="<?=$inmueble->medEste?>">
								</div>
							</div>	
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-4 col-xs-12">
									<h3 class="text-center"><small>COLINDANCIAS</small></h3>								
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Norte</label>
									<input type="text" class="form-control" id="txt-colindancia-norte" name="txt-colindancia-norte" value="<?=$inmueble->colNorte?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Sur</label>
									<input type="text" class="form-control" id="txt-colindancia-sur" name="txt-colindancia-sur" value="<?=$inmueble->colSur?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Este</label>
									<input type="text" class="form-control" id="txt-colindancia-este" name="txt-colindancia-este" value="<?=$inmueble->colEste?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Oeste</label>
									<input type="text" class="form-control" id="txt-colindancia-oeste" name="txt-colindancia-oeste" value="<?=$inmueble->colOeste?>">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-12 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12">
									<label>Superficie Terreno (m2)</label>
									<input type="text" class="form-control" id="txt-superficie-terreno" name="txt-superficie-terreno" value="<?=$inmueble->superficieTerreno?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label>Superficie Construida (m2)</label>
									<input type="text" class="form-control" id="txt-superficie-construccion" name="txt-superficie-construccion" value="<?=$inmueble->superficieConstruccion?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label for="sl-uso">Uso</label>
									<select class="form-control selectpicker show-tick" id="sl-uso" name="sl-uso">
										<option value="0">--</option>
										<option value="1" <?=$inmueble->uso->id=='1'?'selected="selected"':''?> >Público</option>
										<option value="2" <?=$inmueble->uso->id=='2'?'selected="selected"':''?>>Oficinas</option>
									</select>
								</div>
								<div class="col-md-3 col-xs-12">
									<label for="sl-aprovechamiento">Aprovechamiento del inmueble</label>
									<select class="form-control" id="sl-aprovechamiento" name="sl-aprovechamiento">
										<option value="0">--</option>
										<option value="1" <?=$inmueble->aprovechamiento->id=='1'?'selected="selected"':''?>>Al 100%</option>
										<option value="2" <?=$inmueble->aprovechamiento->id=='2'?'selected="selected"':''?>>Más del 50%</option>
										<option value="3" <?=$inmueble->aprovechamiento->id=='3'?'selected="selected"':''?>>Menos del 50%</option>
										<option value="4" <?=$inmueble->aprovechamiento->id=='4'?'selected="selected"':''?>>Sin aprovechar</option>
									</select>
								</div>
								<div class="col-md-6 col-xs-12">
									<label for="sl-medio-adquisicion">Medio de Adquisición</label>
									<select class="form-control" id="sl-medio-adquisicion" name="sl-medio-adquisicion">
										<option value="0">--</option>
										<?
										foreach($fondoOrigen as $fondo){
											?>
											<option value="<?=$fondo->id?>" <?=$inmueble->modoAdquisicion->id==$fondo->id?'selected="selected"':''?> ><?=$fondo->descr?></option>
											<?
										}
										?>
									</select>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-4 col-xs-12">
									<h3 class="text-center"><small>Servicios y Equipamiento con que cuenta el inmueble</small></h3>								
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>	
							<div class="row form-group">	
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-agua" name="ipt-serv-agua" value="1" <?=$inmueble->servAgua=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-agua" >Servicio de agua</label>
								</div>
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-drenaje" name="ipt-serv-drenaje" value="1" <?=$inmueble->servDrenaje=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-drenaje" >Servicio de drenaje</label>
								</div>
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-luz" name="ipt-serv-luz" value="1" <?=$inmueble->servLuz=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-luz" >Servicio de luz</label>
								</div>
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-telefonia" name="ipt-serv-telefonia" value="1" <?=$inmueble->servTelefonia=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-telefonia" >Servicio de telefonia</label>
								</div>
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-internet" name="ipt-serv-internet" value="1" <?=$inmueble->servInternet=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-internet" >Servicio de internet</label>
								</div>
								<div class="col-md-3 col-xs-6">
									<input type="checkbox" id="ipt-serv-gas" name="ipt-serv-gas" value="1" <?=$inmueble->servGasEstacionario=='1'?'checked="checked"':''?>>
									<label for="ipt-serv-gas" >Servicio de gas estacionario</label>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-4 col-xs-12">
									<h3 class="text-center"><small>SITUACIÓN LEGAL</small></h3>								
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Número de Escritura o Convenio</label>
									<div class="input-group"> 
										<div class="input-group-btn">
											<button id="btn-escritura-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div>
										<input type="text" class="form-control" id="txt-escritura" name="txt-escritura" value="<?=isset($inmueble->numeroEscrituraConvenio)&&$inmueble->numeroEscrituraConvenio!=""?$inmueble->numeroEscrituraConvenio:"NO ESCRITURADO"?>" autocomplete="off">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO ESCRITURADO" data-input="txt-escritura">NO ESCRITURADO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-escritura"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>									
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Numero de Registro de la Propiedad</label>
									<input type="text" class="form-control" id="txt-numero-registro" name="txt-numero-registro" value="<?=$inmueble->numRegistroPropiedad?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Cuenta Catastral</label>
									<input type="text" class="form-control" id="txt-cuenta-catastral" name="txt-cuenta-catastral" value="<?=$inmueble->cuentaCatastral?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Fecha del Último Avaluo</label>
									<?
									$fechaAvaluo = new DateTime($inmueble->fechaUltimoAvaluo);
									?>
									<div class="input-group"> 
										<!-- <div class="input-group-btn">
											<button id="btn-escritura-listen" class="btn btn-success"><i class="fa fa-microphone"></i></button>
										</div> -->
										<input type="text" class="form-control input-calendar" id="txt-fecha-ultimo-avaluo" name="txt-fecha-ultimo-avaluo" value="<?=isset($inmueble->fechaUltimoAvaluo)&&$inmueble->fechaUltimoAvaluo!='0000-00-00 00:00:00'?$fechaAvaluo->format("d-m-Y"):'NO IDENTIFICADO'?>" autocomplete="off">
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
												<span class="caret"></span> 
												<span class="sr-only">Toggle Dropdown</span> 
											</button> 
											<ul class="dropdown-menu dropdown-menu-right"> 
												<li>
													<a href="#" class="opts-input" data-value="NO IDENTIFICADO" data-input="txt-fecha-ultimo-avaluo">NO IDENTIFICADO</a>
												</li>
												<li>
													<a href="#" class="opts-input" data-value="" data-input="txt-fecha-ultimo-avaluo"><i class="fa fa-edit"></i> EDITAR</a>
												</li> 
											</ul> 
										</div> 
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Gravamen pendiente</label>
									<select class="form-control" id="sl-gravamen" name="sl-gravamen">
										<option value="0">--</option>
										<option value="1" <?=$inmueble->gravamenPendiente=='1'?'selected="selected"':''?> >SÍ</option>
										<option value="2" <?=$inmueble->gravamenPendiente=='2'?'selected="selected"':''?>>NO</option>
									</select>
								</div>
								<!-- <div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Valor del Inmueble</label>
									<input type="text" class="form-control" id="txt-valor-inmueble" name="txt-valor-inmueble" value="<?=$inmueble->valor?>">
								</div> -->
								<div class="col-md-9 col-xs-12">
									<label for="txt-observaciones">Observaciones</label>
									<textarea tabindex="4" class="form-control" rows="2" id="txt-observaciones" name="txt-observaciones"><?=$inmueble->observaciones?></textarea>
									<!-- <br>
									<button class="btn btn-md btn-success" id="btn-descripcion-listen"> <i class="fa fa-microphone"></i> <span>Escuchar</span> </button> -->
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row form-group">
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
								<div class="col-md-3">
									<h3 class="text-center"><small>Valor del Inmueble</small></h3>								
								</div>
								<div class="col-md-3">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Valor del Terreno</label>
									<input type="number" class="form-control" id="txt-valor-terreno" name="txt-valor-terreno" value="<?=$inmueble->valorTerreno?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Valor de la Construccion</label>
									<input type="number" class="form-control" id="txt-valor-construccion" name="txt-valor-construccion" value="<?=$inmueble->valorConstruccion?>">
								</div>
								<div class="col-md-3 col-xs-12 datos-depreciacion">
									<label>Valor Total del Inmueble</label>
									<input type="text" class="form-control" id="txt-valor-inmueble" name="txt-valor-inmueble" value="<?=$inmueble->valor?>" readonly="readonly">
								</div>
							</div>	
							<div class="row form-group">	
								<div class="col-md-3 col-md-offset-3 col-xs-12 datos-depreciacion">
									<label>Valor Capitalizable</label>
									<input type="text" class="form-control" id="txt-valor-capitalizable" name="txt-valor-capitalizable" value="<?=$inmueble->valorCapitalizable?>" <?=$inmueble->valorCapitalizable!=""?'':'readonly="readonly"'?> >
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
									<img src="" class="img-dummy" data-index="<?=count($inmueble->images)?>" data-saved="0">
									<a class="btn-editar-imagen edit"><i class="fa fa-edit"></i></a>
									<a class="btn-cerrar-imagen close"><i class="fa fa-close"></i></a>
									<a class="waiting-panel hidden"><i class="fa fa-refresh fa-spin"></i></a>
								</div>
								<?
								if(isset($inmueble->images) && count($inmueble->images)>0){
									if(isset($inmueble->imagen) && trim($inmueble->imagen)!=""){
										$imagedata = file_get_contents($settings->prop("system.url").$inmueble->imagen);
										?>
										<div class="col-md-2 item-preview-img preview-img copyable">
											<img src="<?="data:image/jpeg;base64,".base64_encode($imagedata)?>" data-index="0" data-saved="1">
											<a class="btn-editar-imagen edit"><i class="fa fa-edit"></i></a>
											<a class="btn-cerrar-imagen close"><i class="fa fa-close"></i></a>
											<a class="waiting-panel hidden"><i class="fa fa-refresh fa-spin"></i></a>
										</div>
										<?
									}
									for($j=0;$j<count($inmueble->images);$j++){
										if($inmueble->images[$j]!=$inmueble->imagen){	
											$img = $inmueble->images[$j];
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
								<div class="col-md-2 col-xs-12">
									<button id="btn-listado" name="btn-listado" class="btn btn-md btn-success form-control"><i class="fa fa-reply"></i> Listado</button>
								</div>
								<div class="col-md-3 col-md-offset-1 col-xs-12">
									<button id="btn-guardar" name="btn-guardar" class="btn btn-md btn-primary form-control"><i class="fa fa-check"></i> Guardar</button>
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
        <!-- footer content -->
      </div>
    </div>
    
    <!-- VENTANA MODAL EDITAR IMAGEN -->
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
    <!-- TERMINA VENTANA MODAL EDITAR IMAGEN -->
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
    <!--<script type="text/javascript" src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
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
    <script type="text/javascript" src="vendors/pdfmake/build/vfs_fonts.js"></script> -->

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
    <script src="inmueble.js"></script>
  </body>
</html>