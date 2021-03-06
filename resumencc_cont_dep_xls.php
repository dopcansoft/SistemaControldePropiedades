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
    

    $estatusColor = array(
    	"1"=>"#00FF00",
    	"2"=>"#00FF00",
    	"3"=>"#00FF00",
    	"4"=>"#FFFF00",
    	"7"=>"#FFFF00",
    	"5"=>"#FF0000",
    	"6"=>"#FF0000",
    	"8"=>"#FFFF00"
    );

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
        	$catalogoFactory = new CatalogoFactory();
        	$headers = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.headers.list"), $settings, $log, "Clasificacion");
        	$log->debug($headers);
        	$clasificacion = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.list"), $settings, $log, "Clasificacion");
        	//$log->debug($clasificacion);
        	$inventarioFactory = new BienFactory();
    		$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, BienFactory::TIPO_INVENTARIO_CONTABLE, "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		$list = $inventarioFactory->jerarquiaCC($items, $clasificacion, $headers, $log);
    		$anioOLD = "";
            $fechaOld = "";
            foreach($items as $item){
                $log->debug('fechaAdquisicion: '.$item->fechaAdquisicion);
                if($item->fechaAdquisicion!=""){
                    $item->fechaAdquisicion = DateTime::createFromFormat('Y-m-d H:i:s',$item->fechaAdquisicion);//DateTime::createFromFormat("Y-m-d H:i:s", $item->fechaAdquisicion);
                    if($fechaOld==""){
                        $fechaOld = $item->fechaAdquisicion;
                    }
                    if($item->fechaAdquisicion<$fechaOld){
                        $fechaOld = $item->fechaAdquisicion;
                    }
                }
            }
            
            $init_year = $fechaOld->format("Y");
            $last_year = "2020";
            $log->debug('items: '.count($items));
            $log->debug('FECHA OLD:');
            $log->debug($fechaOld);
            $log->debug('inicial:'.$init_year."-".$last_year);
            $mes_corte = 12;
            $anio_corte = 2020;
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
    <!-- <div class="content-preloader">
    	<div id="preloader">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
	    </div>
	</div> -->
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
							<h3><small>Reporte por Cuenta</small> <button id="btn-xls" class="btn btn-md btn-success"><i class="fa fa-file-excel-o"></i>&nbsp;Exportar</button></h3>
						</div>
						<div class="x_content" style="overflow-x:auto; max-height:700px; overflow-y:auto;">
						<!-- CONTENIDO -->
						<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
						<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
						<table class="table-reporte table-margin-bottom">
                            <thead>
                                <tr style="background:#DDD !important; font-size: 0.9em !important; text-align: center;">
                                    <th width="auto">FOLIO</th>
                                    <th style="text-align: center;" colspan="2" width="auto">ACTIVO</th>
                                    <th style="text-align: center;" colspan="2" width="auto">DEPRECIACI??N</th>
                                    <th width="auto">DESCRIPCI??N</th>
                                    <th width="auto">FECHA ADQUISICI??N/AVALUO</th>
                                    <th width="auto">VALOR HISTORICO</th>
                                    <?
                                    for($x=$init_year;$x<=$last_year;$x++){
                                        ?>
                                        <th><?=$x?></th>
                                        <?
                                    }
                                    ?>
                                    <th width="auto">DEPRECIACI??N ACUMULADA</th>
                                    <th width="150px">VALOR EN LIBROS</th>
                                    <th width="150px">VALOR HISTORICO</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
							foreach($list as $data){
								$header = $data["header"];
								if(count($data["content"])>0){
									foreach($data["content"] as $content){
										$clasificacion = $content["clasificacion"];
										$i=1;
										foreach($content["list"] as $bien){									
											//if($bien->folio == "541-054-001"){
                                                ?>
    											<tr>
                                                    <td style="white-space: nowrap;"><?=$bien->folio?></td>
                                                    <td width="auto"><?=$bien->clasificacion->cuentaContable?></td>
                                                    <td width="auto"><?=$bien->clasificacion->descr?></td>
                                                    <td width="auto"><?=$bien->depreciacion->cuenta?></td>
                                                    <td width="auto"><?=$bien->depreciacion->descr?></td>
                                                    <td><?=$bien->descripcion.($bien->marca!=""?", MARCA: ".$bien->marca:'').($bien->modelo!=""?", MODELO: ".$bien->modelo:'').($bien->serie!=""?", NO. DE SERIE: ".$bien->serie:'').($bien->color->descr!=""?", COLOR: ".$bien->color->descr:'')?></td>
                                                    <td><?
                                                    if($bien->fechaAdquisicion!=""){
                                                        //$date = new DateTime($bien->fechaAdquisicion);
                                                        echo $bien->fechaAdquisicion->format('d-m-Y');                                                            
                                                    }
                                                    ?></td>
                                                    <td style="text-align: right;"><?=number_format($bien->valor,2,'.',',')?></td>
                                                    <?
                                                    $anioAdquisicion = $bien->fechaAdquisicion->format('Y');
                                                    $mesAdquisicion = $bien->fechaAdquisicion->format('m');
                                                    $vidaUtil = $bien->depreciacion->vidaUtil;
                                                    $factor = 0;
                                                    if($vidaUtil>0){
                                                        $factor = ($bien->valor/$vidaUtil)/12;
                                                    }                                                    
                                                    $depPeriodo = 0.00;
                                                    $depAcumulada = 0.00;
                                                   for($x=$init_year;$x<=$last_year;$x++){
                                                        if($x>=$anioAdquisicion){
                                                            if($x==$anioAdquisicion && $x != $anio_corte){
                                                                $depPeriodo = round((12-$mesAdquisicion)*$factor,2);
                                                            }else if($x==$anioAdquisicion && $x == $anio_corte){
                                                                $depPeriodo = round(($mes_corte-$mesAdquisicion)*$factor,2);
                                                            }else if($x>$anioAdquisicion && $x == $anio_corte){
                                                                $depPeriodo = round(($mes_corte)*$factor,2);
                                                            }else{
                                                                $depPeriodo = round(12*$factor,2);
                                                            }        
                                                        }   
                                                        ?>
                                                        <td><?=number_format($depPeriodo,2,'.',',')?></td>
                                                        <?
                                                        $depAcumulada=$depAcumulada+$depPeriodo;
                                                    }
                                                    ?>
                                                    <td style="text-align: right;"><?=number_format($depAcumulada,2,'.',',')?></td>
                                                    <td style="text-align: right;"><?=number_format($bien->valor-$depAcumulada,2,'.',',')?></td>
                                                    <td style="text-align: right;"><?=number_format($bien->valorAnterior,2,'.',',')?></td>
                                                </tr>
    											<?
                                            //}
										}
									}
                                }
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
    <script src="resumencc_cont_dep_xls.js"></script>
  </body>
</html>