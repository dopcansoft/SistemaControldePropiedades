<?
	/*session_start();
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
    
    $list = array();
    $bien = new BienDao();
    $catBienesInmuebles = array();
    
    $presidente = new ResponsableDao();
	$tesorero = new ResponsableDao();
	$regidor = new ResponsableDao();
	$sindico = new ResponsableDao();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		for($x=0;$x<count($list);$x++){        		
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}
    		$list = $inventarioFactory->groupByClasificacion($clasificacionBM, $items);
    		$presidente->findByCargo($empresa->id, Responsable::CARGO_PRESIDENTE_MUNICIPAL, $db, $queries, $log);
			$tesorero->findByCargo($empresa->id, Responsable::CARGO_TESORERO, $db, $queries, $log);
			$regidor->findByCargo($empresa->id, Responsable::CARGO_REGIDOR, $db, $queries, $log);
			$sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);
        }else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" >
    <link href="css/template-impresion.css" rel="stylesheet" media="print">
    <title>Template Reporte</title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
	<table class="template-print" width="100%">
		<thead>
			<tr>
				<th>
					<h3>Ibexpro.com.mx</h3>
				</th>	
			</tr>			
		</thead>
		<tfoot>
			<tr>
				<td>Prueba</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>	
					<table class="table-content-intern" width="100%">
						<thead>
							<tr>
								<th>Encabezado</th>
								<th>Dos</th>
								<th>Tres</th>
								<th>Cuatro</th>
								<th>Cinco</th>
							</tr>
						</thead>
						<tbody>
						<?
						for($i=0;$i<200;$i++){
							?>
							<tr>
								<td><?=$i?></td>
								<td>Test</td>
								<td>Test</td>
								<td>Test</td>
								<td>Test</td>
							</tr>	
							<?	
						}
						?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<footer></footer>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<!-- <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script> -->
<script type="text/javascript" src="template_print.js"></script>
</html>