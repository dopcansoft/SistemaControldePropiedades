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

    $list = array();
    $response = array("result"=>"", "desc"=>"");
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$inventarioFactory = new BienFactory();
        	//$list = $inventarioFactory->getAll($empresa->id, $periodo->id, $db, $queries, $settings, $log);
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", $clasificacion, $clasificacion, $departamento, $edoFisico, "", $db, $queries, $settings, $log);
        	for($x=0;$x<count($list);$x++){
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}
        	$catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
			//$departamentos = $catalogoFactory->listado($db, $queries->prop("departamentos.list"), $settings, $log, "Departamento");
     		$deptosFactory = new DepartamentoFactory();
     		$departamentos = $deptosFactory->listAll($empresa->id, $db, $queries, $log); 
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

  <body class="nav-md">
    <!-- PRELOADER -->
    <div class="content-preloader">
    	<div id="preloader">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
	    </div> 
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
                <h3>ALTAS/BAJAS</h3>
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
                    		<label>Departamento</label>
                    		<select class="form-control" id="sl-departamento" name="sl-departamento">
                    			<option value=""> -- </option>
                    			<?
                    			foreach($departamentos as $dep){
                    				?><option value="<?=$dep->id?>" <?=$departamento==$dep->id?'selected="selected"':''?> ><?=$dep->descr?></option><?
                    			}
                    			?>
                    		</select>
                    	</div>
                    	<div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    		<label>Estado F??sico</label>
                    		<select class="form-control" id="sl-estado-fisico" name="sl-estado-fisico">
                    			<option value=""> -- </option>
                    			<?
                    			foreach($estados as $edo){
                    				?><option value="<?=$edo->id?>" <?=$edoFisico==$edo->id?'selected="selected"':''?> ><?=$edo->descr?></option><?
                    			}
                    			?>
                    		</select>
                    	</div>
                    	<div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    		<label>Clasificaci??n</label>
                    		<select class="form-control" id="sl-clasificacion" name="sl-clasificacion">
                    			<option value=""> -- </option>
                    			<?
                    			foreach($clasificacionBM as $cla){
                    				?><option value="<?=$cla->id?>" data-tipo="<?=$cla->tipo->id?>" <?=$clasificacion==$cla->tipo->id?'selected="selected"':''?> ><?=$cla->grupo.$cla->subgrupo.$cla->clase." - ".$cla->descr?></option><?
                    			}
                    			?>
                    			<?
                    			foreach($clasificacionBI as $cla){
                    				?><option value="<?=$cla->id?>" data-tipo="<?=$cla->tipo->id?>" <?=$clasificacion==$cla->tipo->id?'selected="selected"':''?> ><?=$cla->grupo.$cla->subgrupo.$cla->clase.$cla->subclase.$cla->consecutivo." - ".$cla->descr?></option><?
                    			}
                    			?>                    			
                    		</select>
                    	</div>
                    	<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                    		<label>&nbsp;</label>
                    		<button id="btn-buscar" name="btn-buscar" class="form-control btn btn-primary"> <i class="fa fa-search"></i> Buscar </button>
                    	</div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="row">
                    	<div class="col-md-6">
	                    	<div class="alert alert-info" role="alert">
	                    		<p><i class="fa fa-info-circle"></i> La baja de los presentes bienes es establecida por inspecci??n f??sica y disminuyendo los efectos debidos al deterioro F??sico y a la Obsolescencia Funcional y Econ??mica de cada bien valuado.</p>
	                    	</div>
                    	</div>
                    </div>
                    <div class="row">                    	
                    	<div class="col-md-2">
                    		<button class="btn btn-success btn-modificar"> GUARDAR CAMBIOS </button>
                    	</div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="row">
                    	<div class="col-md-3">
                    		<label><input class="chk-todos" type="checkbox"> Seleccionar todos los bienes como:</label>
                    	</div>
                    	<div class="col-md-2">
                    		<select id="sl-tipo-seleccion" class="form-control">
                    			<option value="<?=Bien::ESTATUS_INVENTARIO_BAJA?>"> BAJA </option>
                    			<option value="<?=Bien::ESTATUS_INVENTARIO_ALTA?>"> ALTA </option>
                    			<option value="<?=Bien::ESTATUS_INVENTARIO_MANTIENE?>"> PERMANECE </option>
                    		</select>
                    	</div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <table id="articulos" class="table table-responsive table-striped table-bordered tabla-datos">
                      <thead>
                        <tr>
                          <th width="5%">Permanece</th><!-- 1 -->
                          <th width="5%">Alta</th><!-- 1 -->
                          <th width="5%">Baja</th><!-- 1 -->
                          <th>Folio</th><!-- 2 -->
                          <th>Descripci??n</th><!-- 3 -->
                          <th>Clasificaci??n</th><!-- 4 -->
                          <th>Tipo</th><!-- 5 -->
                          <th>Departamento</th><!-- 6 -->
                          <th>Estado Fisico</th><!-- 7 -->
                          <th>Tipo Valuaci??n</th><!-- 8 -->
                          <th>Valor</th><!-- 9 -->
                          <th>Imagen</th><!-- 10 -->
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="dummy">
                        	<td>
                        		<input type="radio" name="radio-" data-id="" value="<?=Bien::ESTATUS_INVENTARIO_MANTIENE?>">
                        	</td><!-- 1 -->
                        	<td>                        		
                        		<input type="radio" name="radio-" data-id="" value="<?=Bien::ESTATUS_INVENTARIO_ALTA?>">
                        	</td><!-- 2 -->
                        	<td>
                        		<input type="radio" name="radio-" data-id="" value="<?=Bien::ESTATUS_INVENTARIO_BAJA?>">
                        	</td><!-- 3 -->
                        	<td>&nbsp;</td><!-- 4 -->
                        	<td>&nbsp;</td><!-- 5 -->
                        	<td>&nbsp;</td><!-- 6 -->
                        	<td>&nbsp;</td><!-- 7 -->
                        	<td>&nbsp;</td><!-- 8 -->
                        	<td>&nbsp;</td><!-- 9 -->
                        	<td>&nbsp;</td><!-- 10 -->
                        	<td>&nbsp;</td><!-- 11 -->
                        	<td>&nbsp;</td><!-- 12 -->
                        </tr>
                        <?
                        if(isset($list) && count($list)>0){
                            foreach($list as $bien){
                              ?>
								<tr>
									<td>
										<input type="radio" name="radio-<?=$bien->id?>" data-id="<?=$bien->id?>" value="<?=Bien::ESTATUS_INVENTARIO_MANTIENE?>" <?=$bien->estatusInventario->id==Bien::ESTATUS_INVENTARIO_MANTIENE?'checked="checked"':''?> >
									</td>
									<td>
										<input type="radio" name="radio-<?=$bien->id?>" data-id="<?=$bien->id?>" value="<?=Bien::ESTATUS_INVENTARIO_ALTA?>" <?=$bien->estatusInventario->id==Bien::ESTATUS_INVENTARIO_ALTA?'checked="checked"':''?>>	
									</td>
									<td>
										<input type="radio" name="radio-<?=$bien->id?>" data-id="<?=$bien->id?>" value="<?=Bien::ESTATUS_INVENTARIO_BAJA?>" <?=$bien->estatusInventario->id==Bien::ESTATUS_INVENTARIO_BAJA?'checked="checked"':''?>>
									</td> 
									<td><?=$bien->folio?></td>
									<td><?=$bien->descripcion?></td>
									<td><?=$bien->clasificacion->descr?></td>
									<td><?=$bien->tipoClasificacion->descr?></td>
									<td><?=$bien->departamento->descr?></td>
									<td><?=$bien->estadoFisico->descr?></td>
									<td><?=$bien->tipoValuacion->descr?></td>
									<td><?=$bien->valor?></td>
									<td><img src="<?=$bien->imagen?>" style="width: 50px;"></td>
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
    <div class="modal modal-duplicar fade" id="modal-login" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="content-fluid">
						<div class="row">
							<div class="col-md-6 col-sm-12 col-xs-12 form-group">
								<label>No de duplicados</label>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12 form-group">
								<input class="form-control" type="text" id="txt-copias" name="txt-copias">
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 form-group">
								<button class="btn btn-success form-control btn-action-duplicar" data-id-bien=""><i class="fa fa-copy"></i> Duplicar</button>
							</div>	
						</div>						
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal modal-edicion fade" id="modal-edicion" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<input type="hidden" id="empresa-modal" name="empresa-modal">
						<input type="hidden" id="periodo-modal" name="periodo-modal">
						<input type="hidden" id="bien-modal" name="bien-modal">
						<div class="row">
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">
								<label>Descripci??n</label>
							</div>
							<div class="col-md-10 col-sm-12 col-xs-12 form-group">
								<textarea rows="6" class="form-control" id="txt-descripcion-modal" name="txt-descripcion-modal"></textarea>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">
								<label>Clasificaci??n</label>
							</div>
							<div class="col-md-10 col-sm-12 col-xs-12 form-group">
								<select id="sl-clasificacion-modal" name="sl-clasificacion-modal" class="form-control">
									<option value=""> -- </option>
									<?
	                    			foreach($clasificacionBM as $cla){
	                    				?><option value="<?=$cla->id?>" data-tipo="<?=$cla->tipo->id?>" <?=$clasificacion==$cla->tipo->id?'selected="selected"':''?> ><?=$cla->grupo.$cla->subgrupo.$cla->clase." - ".$cla->descr?></option><?
	                    			}
	                    			?>
	                    			<?
	                    			foreach($clasificacionBI as $cla){
	                    				?><option value="<?=$cla->id?>" data-tipo="<?=$cla->tipo->id?>" <?=$clasificacion==$cla->tipo->id?'selected="selected"':''?> ><?=$cla->grupo.$cla->subgrupo.$cla->clase.$cla->subclase.$cla->consecutivo." - ".$cla->descr?></option><?
	                    			}
	                    			?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">
								<label>Departamento</label>
							</div>
							<div class="col-md-10 col-sm-12 col-xs-12 form-group">
								<select id="sl-departamento-modal" name="sl-departamento-modal" class="form-control">
									<option value=""> -- </option>
									<?
	                    			foreach($departamentos as $dep){
	                    				?><option value="<?=$dep->id?>"><?=$dep->descr?></option><?
	                    			}
	                    			?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Estado F??sico</label>
							</div>
							<div class="col-md-9 col-sm-12 col-xs-12 form-group">
								<select id="sl-edo-fisico-modal" name="sl-edo-fisico-modal" class="form-control">
									<option value=""> -- </option>
									<?
	                    			foreach($estados as $edo){
	                    				?><option value="<?=$edo->id?>"><?=$edo->descr?></option><?
	                    			}
	                    			?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Tipo Valuaci??n</label>
							</div>
							<div class="col-md-9 col-sm-12 col-xs-12 form-group">
								<select id="sl-tipo-valuacion-modal" name="sl-tipo-valuacion-modal" class="form-control">
									<option value=""> -- </option>
									<option value="<?=Bien::VALUACION_IMPORTE?>"> IMPORTE </option>
									<option value="<?=Bien::VALUACION_REPOSICION?>"> VALOR DE REPOSICI??N </option>
									<option value="<?=Bien::VALUACION_REEMPLAZO?>"> VALOR DE REEMPLAZO </option>
									<option value="<?=Bien::VALUACION_DESECHO?>"> VALOR DE DESHECHO </option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Valor</label>
							</div>
							<div class="col-md-9 col-sm-12 col-xs-12 form-group">
								<input class="form-control" type="text" id="txt-valor-modal" name="txt-valor-modal">
							</div>
						</div>
						<div class="row div-uma">
							<div class="col-md-5 col-sm-12 col-xs-12 form-group">
								<label>UMA</label>
							</div>
							<div class="col-md-7 col-sm-12 col-xs-12 form-group">
								<select id="sl-uma-modal" name="sl-uma-modal" class="form-control">
									<option value="0"> -- </option>
									<?
									foreach($catUma as $uma){
										?><option data-factor="<?=$uma->factor?>" data-valor-diario="<?=$uma->valorDiario?>" value="<?=$uma->id?>"><?=$uma->anio." (".$uma->valorDiario*$uma->factor.")"?></option><?
									}
									?>
								</select>
							</div>
						</div>
						<div class="row div-depreciacion">
							<div class="col-md-5 col-sm-12 col-xs-12 form-group">
								<label>Porcentaje de Depreciaci??n</label>
							</div>
							<div class="col-md-7 col-sm-12 col-xs-12 form-group">
								<select id="sl-depreciacion-modal" name="sl-depreciacion-modal" class="form-control">
									<option value="0">--</option>
									<?
									if(isset($catDepreciacion)){
										foreach($catDepreciacion as $dep){
											?><option value="<?=$dep->id?>" data-vida-util="<?=$dep->vidaUtil?>" data-depreciacion-anual="<?=$dep->depreciacionAnual?>" data-tipo="<?=$dep->tipo->id?>"><?=$dep->cuenta." - ".strtoupper($dep->descr).($dep->depreciacionAnual>0?" (".$dep->depreciacionAnual."%)":'')?></option><?
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="row div-depreciacion-periodo">
							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Depreciaci??n del Periodo</label>
							</div>
							<div class="col-md-8 col-sm-12 col-xs-12 form-group">
								<input class="form-control" type="text" id="txt-depreciacion-periodo-modal" name="txt-depreciacion-periodo-modal">
							</div>
						</div>
						<div class="row div-depreciacion-acumulada">
							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Depreciaci??n Acumulada</label>
							</div>
							<div class="col-md-8 col-sm-12 col-xs-12 form-group">
								<input class="form-control" type="text" id="txt-depreciacion-acumulada-modal" name="txt-depreciacion-acumulada-modal">
							</div>
						</div>
						<div class="row">
							<button class="btn btn-primary btn-guardar-modal col-md-4 col-md-offset-2 col-sm-4 col-xs-12"><i class="fa fa-check"></i> Guardar</button>
							<button class="btn btn-danger btn-cancelar col-md-4 col-sm-4 col-xs-12"><i class="fa fa-times"></i> Cancelar</button>
						</div>
					</div>	
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
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
    <script src="selestatus.js"></script>
  </body>
</html>