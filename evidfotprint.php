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
        	$iddep = isset($data["departamento"])&&$data["departamento"]!=""?$data["departamento"]:"";
        	$tipo = isset($data["tipo"])&&$data["tipo"]!=""?$data["tipo"]:"";
        	$departamento = new DepartamentoDao(array("id"=>$iddep));
        	$departamento->find($db, $queries, $log); 
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
        	$responsable = new ResponsableDao();
        	$responsable->findByDepartamento($departamento->id, $db, $queries, $log);
        	
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipo, "", "", "", "", $iddep, "", $estatusInventario, $db, $queries, $settings, $log);	
        	
        	for($x=0;$x<count($list);$x++){        		
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
				if(isset($list) && count($list)>0){
					foreach($list as $bien){
						if(count($bien->images)>0 && isset($bien->images[0]) && $bien->images[0]!=""){
							?>
							<table class="table-report-fotos">
								<thead>
									<tr class="subheader">
										<th colspan="2">
											<h3>Evidencia Fotográfica</h3>
										</th>
									</tr>
									<tr class="subheader">
										<th colspan="2">Departamento:&nbsp;<strong><?=$departamento->descr?></strong></th>
									</tr>
									<tr class="report-titulo">
										<td colspan="2"><h3 class="h3-separator"><small><?=$bien->folio?></small></h3></td>
									</tr>
								</thead>
								<tbody>
									<?
									$founded = count($bien->images);
									if($founded>0){
										$rows = $founded>1?ceil($founded/2):1;
										for($i=0;$i<$rows;$i++){
											?>
											<tr>
												<?
												for($j=$i;$j<$i+2;$j++){
													if(isset($bien->images[$j+$i]) && $bien->images[$j+$i]!=""){
														if($j==$i && (!isset($bien->images[1+$i+$j]) || $bien->images[1+$i+$j]=="")){
															?>
															<td colspan="2" class="report-content-img"><img class="report-img" src="<?=$settings->prop("system.url").$bien->images[$j+$i]?>"></td>
															<?		
														}else{
															?>
															<td class="report-content-img"><img class="report-img" src="<?=$settings->prop("system.url").$bien->images[$j+$i]?>"></td>
															<?
														}																	
													}															
												}
												?>
											</tr>
											<?
										}
									}
									?>
									</tbody>
								</table>
								<?
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
<script type="text/javascript" src="rcac.js"></script>
</html>