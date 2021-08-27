<?
	session_start();
	session_name("inv"); 
	include("src/vo/config.php");
	Logger::configure("src/config/log4php.xml");
	$log = Logger::getLogger("cupones");
	$settings = new Properties("src/config/settings.xml");
	$database = new Properties("src/config/database.xml");
	$queries = new Properties("src/config/queries.xml");

	$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$components = parse_url($url);
	parse_str($components['query'], $results);
	$log->debug($_SERVER['REQUEST_URI']);
	$log->debug($url);
	/*if(!isset($_SESSION["usuario"])){
	    header("Location: ".$settings->prop("url.login"));
	}*/

	//$misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    //$empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    //$periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();
    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;
    //$log->debug($data);
    $bien = null;
    $departamento = null;
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$bien = new BienDao(
                array(
                    "id"=>isset($data["id"])?$data["id"]:"",
                    "periodo.id"=>isset($data["periodo"])?$data["periodo"]:"",
                    "empresa.id"=>isset($data["empresa"])?$data["empresa"]:""
                )
            );
        	if($bien->find($db, $queries, $log)){
                $iddep = $bien->departamento->id;
                $tipo = "";
                $departamento = new DepartamentoDao(array("id"=>$bien->departamento->id));
                $departamento->find($db, $queries, $log); 
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
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" >
    <link href="<?=$settings->prop("system.url")?>/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?=$settings->prop("system.url")?>/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?=$settings->prop("system.url")?>/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?=$settings->prop("system.url")?>/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="<?=$settings->prop("system.url")?>/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?=$settings->prop("system.url")?>/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <!-- NOTIFICACIONES -->
    <link href="<?=$settings->prop("system.url")?>/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="<?=$settings->prop("system.url")?>/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="<?=$settings->prop("system.url")?>/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->
    <link href="<?=$settings->prop("system.url")?>/build/css/custom.min.css" rel="stylesheet">
    <link href="<?=$settings->prop("system.url")?>/css/principal.css" rel="stylesheet" media="screen">
    <link href="<?=$settings->prop("system.url")?>/css/principal.css" rel="stylesheet" media="print">

    <link href="<?=$settings->prop("system.url")?>/css/articulo.css" rel="stylesheet">
    <title>Ficha Técnica</title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?
				if($empresa->tipoEtiqueta==Empresa::TIPO_ETIQUETA_QR){
					?>
					<table class="table-label">
						<tbody>
							<tr>
								<?
								$log->debug($settings->prop("system.url").$settings->prop("qrcode.path").$bien->id.".png");
								?>
								<td rowspan="4">
									<img style="margin-bottom:5px !important;min-height: 60px !important; min-width: 60px !important;" src="<?=$settings->prop("system.url").$settings->prop("qrcode.path").$bien->id.".png"?>">
								</td>
								<td width="140px" style="padding-left:5px; text-align: center !important;">
									<p  class="descr"><strong><?=$empresa->nombre?></strong></p>
								</td>
								<td rowspan="3" width="40px" style="text-align: right !important; margin-top:5px !important; vertical-align: middle !important;">
								</td>
							</tr>
							<tr>
								<td style="padding-left:0; text-align: center !important;">
									<p class="descr">2020</p>
								</td>
							</tr>
							<tr>
								<td style="padding-left:0; text-align: center !important; line-height: 1.2em !important;">
									<p class="descr"><?=$bien->departamento->descr?></p>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center !important;">
									<p class="descr"><?=$bien->getShortName(24)?></p>
									<p class="descr" style="font-size: 1em !important; font-weight: bold; margin-top: 5px !important;"><?=$bien->folio?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<?
				}else{
					?>
					<table class="table-label">
						<tbody>
							<tr>
								<td rowspan="3" width="35px" style="text-align: right !important;">
									<img style="margin-bottom:5px !important;" width="30px" src="imgs/<?=$empresa->logoEtiqueta?>">								
								</td>
								<td width="140px" style="padding-left:5px; text-align: left !important;">
									<p  class="descr"><strong><?=$empresa->nombre?></strong></p>
								</td>
							</tr>
							<tr>
								<td style="padding-left:0; text-align: center !important;" colspan="2">
									<p class="descr">2020</p>
								</td>
							</tr>
							<tr>
								<td style="padding-left:0; text-align: center !important;" colspan="2">
									<p class="descr"><?=$bien->departamento->descr?></p>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p class="descr"><?=$bien->getShortName(24)?></p>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<img width="180px" src="src/lib/phpbarcode/barcode.php?text=<?=$bien->empresa->id.';'.$bien->periodo->id.';'.$bien->id?>&size=20&print=false">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p class="descr" style="font-size: 1em !important; font-weight: bold; margin-top: 5px !important;"><?=$bien->folio?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<?
				}
			?>				
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="rdt.js"></script>
</html>