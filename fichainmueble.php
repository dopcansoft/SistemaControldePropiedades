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
    $bien = new BienInmuebleDao();

    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])){
                $bien->id = isset($data["id"])?$data["id"]:"";
                $bien->find($db, $queries, $log);
            }
            $list = array($bien);
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
        table.cedula{
            font-size: 0.9em;
        }

        table.cedula tr td.header{
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        table.cedula tr td{
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        .subheader{
            font-weight: 600;
            text-decoration: underline;
            background:#E0E0E0 !important;
        }

        .descripcion{
            font-size: 1em !important;
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
							<h3><small>FICHA INMUEBLE</small></h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<input type="hidden" id="bien" name="bien" value="<?=$bien->id?>">
							<input type="hidden" id="tipo" name="tipo" value="<?=$tipo?>">
							<div class="row">
								<div class="col-md-3">
									<button class="btn btn-success btn-sm btn-listado"> <i class="fa fa-reply"></i> Listado </button>
									<button class="btn btn-primary btn-sm btn-imprimir"> <i class="fa fa-print"></i> Imprimir</button>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<?
							foreach($list as $inmueble){
								?>
								<table class="cedula">
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="titulo">FICHA DE BIEN INMUEBLE</td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" style="width: 12.5%;">DESCRIPCIÓN</td>
                                            <td class="descripcion" colspan="7"><?=strtoupper($inmueble->descr)?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="header">DATOS GENERALES</td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" style="width: 12.5%;">FOLIO</td>
                                            <td colspan="3"><?=strtoupper($inmueble->folio)?></td>
                                            <td class="subheader" style="width: 12.5%;">TIPO DE INMUEBLE</td>
                                            <td colspan="3"><?=trim($inmueble->tipo->descr)!=""?strtoupper($inmueble->tipo->descr):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="header">IDENTIFICACIÓN DEL INMUEBLE</td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="1">UBICACIÓN</td>
                                            <td colspan="7"><?=trim($inmueble->ubicacion)!=""?strtoupper($inmueble->ubicacion):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="header" colspan="4">MEDIDAS</td>
                                            <td rowspan="11" colspan="4">
                                                <img src="<?=$inmueble->imagen?>" style="max-width: 400px !important; max-height: 350px !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" style="width: 12.5%;" >NORTE</td>
                                            <td style="width: 12.5%;"><?=trim($inmueble->medNorte)!=""?strtoupper($inmueble->medNorte):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader" style="width: 12.5%;">ESTE</td>
                                            <td style="width: 12.5%;"><?=trim($inmueble->medEste)!=""?strtoupper($inmueble->medEste):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader">SUR</td>
                                            <td><?=trim($inmueble->medSur)!=""?strtoupper($inmueble->medSur):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader">OESTE</td>
                                            <td><?=trim($inmueble->medOeste)!=""?strtoupper($inmueble->medOeste):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%;" class="header" colspan="4">COLINDANCIAS</td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" style="width: 12.5%;">NORTE</td>
                                            <td style="width: 12.5%;"><?=trim($inmueble->colNorte)!=""?strtoupper($inmueble->colNorte):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader" style="width: 12.5%;">ESTE</td>
                                            <td style="width: 12.5%;"><?=trim($inmueble->colEste)!=""?strtoupper($inmueble->colEste):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader">SUR</td>
                                            <td><?=trim($inmueble->colSur)!=""?strtoupper($inmueble->colSur):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader">OESTE</td>
                                            <td><?=trim($inmueble->colOeste)!=""?strtoupper($inmueble->colOeste):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="2">SUPERFICIE TOTAL</td>
                                            <td colspan="2"><?=number_format($inmueble->superficieTerreno,2,'.',',')?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="2">SUPERFICIE CONSTRUIDA</td>
                                            <td colspan="2"><?=number_format($inmueble->superficieConstruccion,2,'.',',')?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="2">USO</td>
                                            <td colspan="2"><?=trim($inmueble->uso->descr)!=""?strtoupper($inmueble->uso->descr):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="2">APROVECHAMIENTO DEL INMUEBLE</td>
                                            <td colspan="2"><?=trim($inmueble->aprovechamiento->descr)!=""?strtoupper($inmueble->aprovechamiento->descr):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader" colspan="2">MEDIO DE ADQUISICIÓN</td>
                                            <td colspan="2"><?=trim($inmueble->modoAdquisicion->descr)!=""?strtoupper($inmueble->modoAdquisicion->descr):"DATO NO PROPORCIONADO"?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="header">SERVICIOS</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="width: 12.5%;">(<?=$inmueble->servAgua==1?'<strong>X</strong>':' '?>) SERVICIO DE AGUA</td>
                                            <td colspan="2" style="width: 12.5%;">(<?=$inmueble->servDrenaje==1?'<strong>X</strong>':' '?>) SERVICIO DE DRENAJE</td>
                                            <td colspan="2" style="width: 12.5% !important;">(<?=$inmueble->servLuz==1?'<strong>X</strong>':' '?>) SERVICIO DE LUZ</td>
                                            <td colspan="2" style="width: 12.5%;">(<?=$inmueble->servTelefonia==1?'<strong>X</strong>':' '?>) SERVICIO DE TELEFONÍA</td>    
                                        </tr>
                                        <tr>
                                            <td colspan="2">(<?=$inmueble->servInternet==1?'<strong>X</strong>':' '?>) SERVICIO DE INTERNET</td>
                                            <td colspan="2">(<?=$inmueble->servGasEstacionario==1?'<strong>X</strong>':' '?>) SERVICIO DE GAS ESTACIONARIO</td>
                                            <td colspan="4">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="header">SITUACIÓN LEGAL</td>
                                        </tr>
                                        <tr>
                                            <td class="subheader">NÚMERO DE ESCRITURA O CONVENIO</td>
                                            <td><?=trim($inmueble->numeroEscrituraConvenio)!=""?strtoupper($inmueble->numeroEscrituraConvenio):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader">NÚMERO DE REGISTRO DE LA PROPIEDAD</td>
                                            <td><?=trim($inmueble->numRegistroPropiedad)!=""?strtoupper($inmueble->numRegistroPropiedad):"DATO NO PROPORCIONADO"?></td>
                                            <td class="subheader" style="width: 12.5% !important;">CUENTA CATASTRAL</td>
                                            <td style="width: 12.5% !important;"><?=$inmueble->cuentaCatastral!=""?strtoupper($inmueble->cuentaCatastral):"DATO NO PROPORCIONADO"?></td>
                                            <td style="width: 12.5% !important;" class="subheader">FECHA ÚLTIMO AVALUO</td>
                                            <td style="width: 12.5% !important;"><?=trim($inmueble->fechaUltimoAvaluo)!=""?strtoupper($inmueble->fechaUltimoAvaluo):"DATO NO PROPORCIONADO"?></td>                                                    
                                        </tr>
                                        <tr>
                                            <td class="subheader">GRAVAMEN PENDIENTE</td>
                                            <td><?=$inmueble->gravamenPendiente==1?'SI':'NO'?></td>
                                            <td class="subheader">OBSERVACIONES</td>
                                            <td rowspan="2" colspan="5"><?=strtoupper($inmueble->observaciones)?></td>
                                        </tr>
                                        <tr>
                                            <td class="subheader">VALOR DEL INMUEBLE</td>
                                            <td colspan="2"><?=number_format($inmueble->valorCapitalizable>0?$inmueble->valorCapitalizable:$inmueble->valor,2,'.',',')?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?
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
    <script src="fichainmueble.js"></script>
  </body>
</html>