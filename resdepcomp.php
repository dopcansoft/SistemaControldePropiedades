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
    
    $responsables = array();
    $sindico = new ResponsableDao();
    $list = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$iddep = isset($data["departamento"])&&$data["departamento"]!=""?$data["departamento"]:"";
        	$tipo = isset($data["tipo"])&&$data["tipo"]!=""?$data["tipo"]:"";
        	$departamento = new DepartamentoDao(array("id"=>$iddep));
        	$departamento->find($db, $queries, $log); 
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
        	$responsable = new ResponsableDao();
        	$responsable->findByDepartamento($departamento->id, $db, $queries, $log);
        	$sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipo, "", "", "", "", $iddep, "", $estatusInventario, $db, $queries, $settings, $log);	
        	
        	for($x=0;$x<count($list);$x++){        		
        		$existe = 0;
                if($list[$x]->responsable->id){
                    $log->debug($list[$x]->responsable);    
                }                
                foreach($responsables as $respon){
                    if($respon->id == $list[$x]->responsable->id){
                        $existe++;
                    }
                }
                if($existe<=0 && trim($list[$x]->responsable->id)!=""){
                    $responsables[] = $list[$x]->responsable;     
                }
                $list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
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
							<h3><small>Resguardo por Departamento</small></h3>
						</div>
						<div class="x_content">
							<!-- CONTENIDO -->
							<input type="hidden" id="empresa" name="empresa" value="<?=$empresa->id?>">
							<input type="hidden" id="periodo" name="periodo" value="<?=$periodo->id?>">
							<input type="hidden" id="departamento" name="departamento" value="<?=$departamento->id?>">
							<input type="hidden" id="tipo" name="tipo" value="<?=$tipo?>">
							<div class="row">
								<div class="col-md-2">Departamento</div>
								<div class="col-md-3"><strong><?=$departamento->descr?></strong></div>
							</div>
							<div class="row">
								<div class="col-md-2">Reponsable</div>
								<div class="col-md-3"><strong><?=(isset($responsable->titulo)&&$responsable->titulo!=""?$responsable->titulo." ":"").$responsable->nombre." ".$responsable->apellido?></strong></div>
							</div>
							<div class="row">
								<div class="col-md-2">Tipo de Reporte</div>
								<div class="col-md-3"><strong><?
									if($tipo==""){
										echo "GENERAL";
									}else if($tipo=="CONTABLE"){
										echo $tipo;
									}else if($tipo=="INSTRUMENTAL"){
										echo $tipo;
									}else{
										echo "NO ESPECIFICADO";
									}
								?></strong></div>
							</div>
							<div class="row">&nbsp;</div>
							<div class="row">
								<div class="col-md-3">
									<button class="btn btn-success btn-sm btn-listado"> <i class="fa fa-reply"></i> Resguardos </button>
									<button class="btn btn-primary btn-sm btn-imprimir"> <i class="fa fa-print"></i> Imprimir</button>
								</div>
							</div>
							<div class="row">&nbsp;</div>
							<?
                            foreach($responsables as $resp){
                                ?>
                                <table class="table-reporte">
    								<thead>
    									<tr>
                                            <th>DEPARTAMENTO</th>
                                            <th colspan="9"><strong><?=strtoupper($departamento->descr)?></strong></th>                           
                                        </tr>
                                        <tr>
                                            <th colspan="10">DEPOSITARIO RESPONSABLE:&nbsp;&nbsp;<strong><?=$resp->nombre." ".$resp->apellido?></strong></th>                           
                                        </tr>
                                        <tr>
                                            <th colspan="10">&nbsp;</tr>
                                        </tr>
                                        <tr>
    										<th>FOLIO</th>
    										<th>FOTOGRAFIA</th>
    										<th width="24%">DESCRIPCIÓN</th>
    										<th>MARCA</th>
    										<th>MODELO</th>
    										<th>SERIE</th>
    										<th>MOTOR</th>
    										<th>ORIGEN</th>
    										<th>ESTADO FÍSICO</th>
    										<th>VALOR</th>
    									</tr>
    								</thead>
    								<tbody>
    									<?
    									if(isset($list) && count($list)>0){
    										foreach($list as $bien){
    											if($bien->responsable->id == $resp->id){
                                                    ?>
        											<tr>
        												<td><?=$bien->folio?></td>
        												<td><img src="<?=$bien->imagen?>" width="75"></td>
        												<td width="24%"><?=$bien->descripcion?></td>
        												<td><?=$bien->marca?></td>
        												<td><?=$bien->modelo?></td>
        												<td><?=$bien->serie?></td>
        												<td><?=$bien->motor?></td>
        												<td><?=$bien->origen->descr?></td>
        												<td><?=$bien->estadoFisico->descr?></td>
        												<td style="text-align: right !important;"><?=number_format($bien->valor,2,'.',',')?></td>
        											</tr>
        											<?
                                                }
    										}
    									}
    									?>																
    								</tbody>
    								<tfoot>
                                        <tr>
                                            <th colspan="10">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center !important;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td style="text-align: center !important;"><?=$responsable->titulo." ".$responsable->nombre." ".$responsable->apellido?></td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td style="text-align: center !important;"><?=$resp->titulo." ".$resp->nombre." ".$resp->apellido?></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center !important;">SINDICO</td>
                                                        <td>&nbsp;</td>
                                                        <td style="text-align: center !important;">ENCARGADO DEL ÁREA</td>
                                                        <td>&nbsp;</td>
                                                        <td style="text-align: center !important;">RESGUARDATARIO</td>
                                                    </tr>
                                                </table>
                                            </th>
                                        </tr>
                                    </tfoot>
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
    <script src="resdepcomp.js"></script>
  </body>
</html>