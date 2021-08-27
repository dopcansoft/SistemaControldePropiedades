<?
    session_start();
    session_name("inv");   
    //header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	//header("Cache-Control: no-store, no-cache, must-revalidate");
	//header("Cache-Control: post-check=0, pre-check=0", false);
	//header("Pragma: no-cache");

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
        	$tipoInventario ="CONTABLE";
            $catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipoInventario, "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		$total = count($items);
            /*for($i=0;$i<$total;$i++){
                if(isset($items[$i]->imagen) && trim($items[$i]->imagen)!=""){
                    $items[$i]->imagenBlob = "data:image/jpeg;base64,".base64_encode(file_get_contents(ltrim($items[$i]->imagen,"/")));
                    $items[$i]->barcodeBlob = "data:image/png;base64,".base64_encode(file_get_contents("files/barcodes/".$items[$i]->id.".png"));
                }                 
            }*/
            $list = $inventarioFactory->groupByClasificacion($clasificacionBM, $items);        	
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
  <body class="nav-md">
    <div class="content-preloader">
    	<div id="preloader">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
	    </div>
	</div>
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
							<h3><small>Fichas por Clasificación Armonizada (Contable)</small> 
                                <button id="btn-imprimir" class="btn btn-md btn-primary"> Imprimir <i class="fa fa-print"></i></button>
                                <button class="btn btn-sm btn-opt-color bg_morena btn-checked" data-index="1" data-class="header" data-color="#A53420" style="font-size: 1em; padding: 3px 6px 3px 6px; line-height: 1em;"><i class="fa"></i>&nbsp;&nbsp;</button> 
                                <button class="btn btn-sm btn-opt-color bg_azul" data-index="2" data-class="header-azul" data-color="#336699" style="font-size: 1em; padding: 3px 6px 3px 6px; line-height: 1em;"><i class="fa"></i>&nbsp;&nbsp;</button>
                                <button class="btn btn-sm btn-opt-color bg_amarillo" data-index="3" data-class="header-amarillo" data-color="#FFD302" style="font-size: 1em; padding: 3px 6px 3px 6px; line-height: 1em;"><i class="fa"></i>&nbsp;&nbsp;</button>
                            </h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<?
							foreach($list as $data){
								$cbm = $data["clasificacion"];
								$items = $data["bienes"];
								if(isset($items) && count($items)>0){
									?>
									<table width="100%">
										<thead style="display: table-row !important;">
											<tr style="background:#DDD; color:#000; padding:5px; font-weight: bold; margin-bottom: 5px;">
												<td colspan="7" style="padding:5px;">
													<?=$cbm->cuentaContable." - ".$cbm->descr?>
                                                </td>
												<td colspan="3" style="text-align: right; padding:5px;">
													TOTAL
												</td>
												<td style="text-align: right; padding:5px;">
													<?=number_format($data["total"],2,'.',',')?>
												</td>
											</tr>
                                            <tr style="background:#FFF;">
                                                <td colspan="11">&nbsp;</td>
                                            </tr>
										</thead>
										<tbody>
											<?
											$i=1;
											foreach($items as $item){									
												?>
												<tr>
													<td colspan="11">
                                                        <!-- FICHA -->
                                                        <table class="cedula">
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="6" class="titulo">CÉDULA DE REGISTRO</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="header header-naranja">FOLIO</td>
                                                                    <td class="header header-naranja">DESCRIPCIÓN Y NO DE INVENTARIO</td>
                                                                    <td class="header header-naranja">NO. DE SERIE</td>
                                                                    <td class="header header-naranja">VALOR HISTORICO</td>
                                                                    <td class="header header-naranja">REGISTRO FOTOGRÁFICO</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?=$item->folio?></td>
                                                                    <td><?=strtoupper($item->getShortName(80))?></td>
                                                                    <td><?=strtoupper($item->serie)?></td>
                                                                    <td <?=$item->tipoValuacion->id==1?'rowspan="3"':'rowspan="2"'?> ><?="$ ".number_format($item->valorAnterior,2,'.',',')?></td>
                                                                    <td rowspan="7"><img src="<?=$item->imagen?>" width="145px"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="header header-naranja">MARCA</td>
                                                                    <td class="header header-naranja">MODELO</td>
                                                                    <td class="header header-naranja">FACTURA</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?=strtoupper($item->marca)?></td>
                                                                    <td><?=strtoupper($item->modelo)?></td>
                                                                    <td><?=strtoupper($item->factura)?></td>
                                                                    <?
                                                                    if($item->tipoValuacion->id>1){
                                                                        ?>
                                                                        <td class="header header-naranja">AVALUO</td>    
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="header header-naranja">AREA DE ADSCRIPCION</td>
                                                                    <td colspan="2" class="header">CONDICIONES DEL BIEN</td>
                                                                    <?
                                                                    if($item->tipoValuacion->id>1){
                                                                        ?>
                                                                        <td rowspan="2"><?="$ ".number_format($item->valor,2,'.',',')?></td>    
                                                                        <?
                                                                    }else{
                                                                        ?>
                                                                        <td class="header header-naranja">VALOR EN LIBROS</td>    
                                                                        <?
                                                                    }
                                                                    ?>                                                                    
                                                                </tr>
                                                                <tr>
                                                                    <td><?=strtoupper($item->departamento->descr)?></td>
                                                                    <td colspan="2"><?=$item->estadoFisico->descr?></td>
                                                                    <?
                                                                    if($item->tipoValuacion->id==1){
                                                                        ?>
                                                                        <td rowspan="3"><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>    
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="header header-naranja">
                                                                        <?=$item->tipoValuacion->id==1?'FECHA DE ADQUISICIÓN':'FECHA DE AVALUO'?>                                            
                                                                    </td>
                                                                    <td colspan="2" class="header">OBSERVACIONES</td>
                                                                    <?
                                                                    if($item->tipoValuacion->id>1){
                                                                        ?>
                                                                        <td class="header header-naranja">VALOR EN LIBROS</td>    
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td><?
                                                                        $date = new DateTime($item->fechaAdquisicion);
                                                                        echo $date->format('d-m-Y');
                                                                    ?></td>
                                                                    <td colspan="2"><?=strtoupper($item->notas)?></td>
                                                                    <?
                                                                    if($item->tipoValuacion->id>1){
                                                                        ?>
                                                                        <td><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>    
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td rowspan="2">CÓDIGO</td>
                                                                    <td colspan="7" style="padding-top: 7px; padding-bottom: 7px;">
                                                                        <?
                                                                        if($empresa->tipoEtiqueta==Empresa::TIPO_ETIQUETA_QR){
                                                                            ?>
                                                                            <img src="files/qrcodes/<?=$item->id.".png"?>">
                                                                            <?
                                                                        }else{
                                                                            ?>
                                                                            <img width="240px" src="files/barcodes/<?=$item->id.".png"?>">
                                                                            <?    
                                                                        }
                                                                        ?>
                                                                        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7"><?=$item->folio?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <!-- TERMINA FICHA -->    
                                                    </td>
												</tr>
												<?
												$i++;
											}
											?>
										</tbody>
									</table>
									<?
								}
							}
							?>
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
    <!-- VENTANA MODAL -->
    <div id="modal-editar-img" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="content-img-modal">
						<img class="articulo-imagen" id="image-edit" src="<?=$settings->prop("system.url").$bien->imagen?>">
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-6 col-md-offset-3">
								<button class="btn btn-success btn-rotate-left"><i class="fa fa-rotate-left"></i></button>
								<?
								if($misesion->perfil->id==Usuario::PERFIL_ADMIN||$misesion->perfil->id==Usuario::PERFIL_CAPTURISTA){
									?>
									<button class="btn btn-primary btn-save-img">Guardar Cambios</button>
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
    <!-- jQuery -->
    <script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
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
    <script type="text/javascript" src="build/bootstrap-select/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker-new.js"></script> -->
    <script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
    <script type="text/javascript" src="js/jquery.mask.js"></script>
    <!-- Custom Theme Scripts -->
    <!-- <script src="vendors/jquery.camera/jquery.camera_capture.js"></script> -->
    <!-- <script src="js/webcamjs-1.025.js"></script> -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="fichas_cuenta.js"></script>
  </body>
</html>