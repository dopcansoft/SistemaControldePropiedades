<?
	session_start();
	session_name("inv"); 
	include("src/vo/config.php");
	Logger::configure("src/config/log4php.xml");
	$log = Logger::getLogger("cupones");
	$settings = new Properties("src/config/settings.xml");
	$database = new Properties("src/config/database.xml");
	$queries = new Properties("src/config/queries.xml");

	if(!isset($_SESSION["usuario"])){
	    header("Location: ".$settings->prop("url.login"));
	}

	$misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    $empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    $periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();
    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;
    $data["tipo"] = isset($empresa->tipoEtiqueta)&&$empresa->tipoEtiqueta!=""?$empresa->tipoEtiqueta:$settings->prop('system.label.default');
    
    $list = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$datos = json_decode($data["datos"], JSON_FORCE_OBJECT);
        	$permitidos = $datos["data"];

        	$log->debug($permitidos);
        	$iddep = isset($datos["departamento"])&&$datos["departamento"]!=""?$datos["departamento"]:"";
        	$tipo = "";
        	$departamento = new DepartamentoDao(array("id"=>$iddep));
        	$departamento->find($db, $queries, $log); 
        	$inventarioFactory = new BienFactory();
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipo, "", "", "", "", $iddep, "", $estatusInventario, $db, $queries, $settings, $log);
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
    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet" media="screen">
    <link href="css/principal.css" rel="stylesheet" media="print">

    <link href="css/articulo.css" rel="stylesheet">
    <title></title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?
				foreach($list as $item){
					foreach($permitidos as $pass){
						if($pass["id"]==$item->id){
							if($data["tipo"]==Empresa::TIPO_ETIQUETA_QR){
								?>
								<table class="table-label">
									<tbody>
										<tr>
											<td rowspan="4">
												<img width="95px" style="margin: 0 !important; margin-bottom:0 !important; min-height: 70px !important; min-width: 70px !important;" src="<?=$settings->prop("system.url").$settings->prop("qrcode.path").$item->id.".png"?>">
												<p style="margin: 0 !important; padding: 0 !important; line-height: 0.6em !important; font-size: 0.6em !important; font-weight: 500; font-style: italic;">CODEPAG</p>
											</td>
											<td style="text-align: center !important;">
												<p  class="descr"><strong><?=$empresa->nombre?></strong></p>
											</td>
										</tr>
										<tr>
											<td style="padding-left:0; text-align: center !important;">
												<p class="descr">2021</p>
											</td>
										</tr>
										<tr>
											<td style="padding-left:0; text-align: center !important; line-height: 1.2em !important;">
												<p class="descr"><?=$item->departamento->descr?></p>
											</td>
										</tr>
										<tr>
											<td style="text-align: center !important;">
												<p class="descr"><?=$item->getShortName(24)?></p>
												<p class="descr" style="font-size: 1em !important; font-weight: bold; margin-top: 3px !important;">
												<?
												if($empresa->tipoNumeracion==Empresa::TIPO_NUMERACION_FOLIO){
													echo $item->folio;
												}else{
													echo $item->folioUnico;  
												}?>	
												</p>												
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
												<p class="descr"><?=$item->departamento->descr?></p>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<p class="descr"><?=$item->getShortName(24)?></p>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<img width="180px" src="src/lib/phpbarcode/barcode.php?text=<?=$item->empresa->id.';'.$item->periodo->id.';'.$item->id?>&size=20&print=false">
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<p class="descr" style="font-size: 1em !important; font-weight: bold; margin-top: 5px !important;"><?=$item->folio?></p>
											</td>
										</tr>
									</tbody>
								</table>		
								<?
							}	
						}
					}
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