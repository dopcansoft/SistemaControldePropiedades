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
    try{
        $db = new DBConnector($database);
        if(isset($db)){
            $departamentoFactory = new DepartamentoFactory();
            $list = $departamentoFactory->allWithDetails($empresa->id, $periodo->id, $db, $queries, $log);
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
  </head>
  <body style="background:#FFF !important;">
  	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		    	<table class="table-reporte">
					<thead style="border:0 !important; background:#FFF !important;">
                        <tr style="border:0 !important; background:#FFF !important;">
                            <th colspan="4" style="border:0 !important; background:#FFF !important;">
                                <table width="100%" style="border:0 !important; background:#FFF !important;">
                                    <tbody>
                                        <tr>
                                            <td width="33%" style="text-align: left !important; border:0 !important; background:#FFF !important;">
                                                <img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoMpio?>">
                                            </td>
                                            <td width="33%" style="text-align: center !important; border:0 !important; background:#FFF !important;">
                                                <h3><?=strtoupper($empresa->nombre)."</br>".$empresa->periodoDescr?></h3>
                                            </td>
                                            <td width="33%" style="text-align: right !important; border:0 !important; background:#FFF !important;">
                                                <img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoAyuto?>">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>    
                            </th>
                        </tr>
                        <tr style="background:#BBB !important;">
                            <th style="background:#BBB !important;">Departamento</th>
                            <th style="background:#BBB !important;">Folio</th>
                            <th style="background:#BBB !important;">Responsable</th>
                            <th style="background:#BBB !important;">Bienes</th>
                        </tr>                  
                    </thead>
                    <tbody>
					<?
                        foreach($list as $row){
                            $departamento = $row["departamento"];
                            $responsable = $row["responsable"];
                            ?>
                            <tr>
                                <td><?=$departamento->descr?></td>
                                <td><?=$departamento->folio?></td>
                                <td><?=$responsable->titulo." ".$responsable->nombre." ".$responsable->apellido?></td>
                                <td><?=$row["bienes"]?></td>
                            </tr>
                            <?
                        }
                    ?>	
					</tbody>
				</table>
			</div>
		</div>
	</div>							
    <script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script type="text/javascript" src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <!-- <script type="text/javascript" src="vendors/nprogress/nprogress.js"></script> -->
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
    <script type="text/javascript" src="js/bootstrap-datepicker-new.js"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
    <script type="text/javascript" src="js/jquery.mask.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="cedulasprint.js"></script>
  </body>
</html>