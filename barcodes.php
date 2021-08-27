<?
	session_start();
	session_name("inv");
	include("src/vo/config.php");
	Logger::configure("src/config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("src/config/settings.xml");
	$database = new Properties("src/config/database.xml");
	$queries = new Properties("src/config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$items = array();
	$misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    $empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    $periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();

	try{
		$db = new DBConnector($database);
		if(isset($db)){
			/*** code here ***/
			$tipoInventario = "INSTRUMENTAL";
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
        	$log->debug('empresa: '.$empresa->id);
			$log->debug('periodo: '.$periodo->id);        	
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);
		}else{
			$log->error("No se ha podido establecer conexión con base de datos");
			$response["result"]="FAIL";
			$response["desc"]="No se ha podido establecer conexión con base de datos";		
		}
	}catch(PDOException $e){
		$log->error("PDOException: ".$e->getMessage());
		$response["result"]="FAIL";
		$response["desc"]="Ocurrio un error al consultar la información";	
	}
	$log->debug('Cierra conexion a Base de datos');
	$db = null;
	$path = "../../../files/barcodes/";	
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" >
    <link rel="stylesheet" href="">
    <title></title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <table width="100%">
    	<tr>
    		<td>Elementos: <?=count($items)?></td>
    	</tr>
    	<tr>
    		<td>Inicio: <?=$_GET["ini"]?></td>
    	</tr>
    	<tr>
    		<td>Fin: <?=$_GET["fin"]?></td>
    	</tr>
    	<?
    	$i=0;
    	$log->debug("generando...");
	    foreach($items as $item){	
	    	$log->debug($i);
	    	if($i >= $_GET["ini"] && $i<=$_GET["fin"] && isset($_GET["fin"]) && isset($_GET["ini"])){
		    	?>
		    	<tr>
		    		<td>
		    			<img width="240px" src="src/lib/phpbarcode/barcode.php?text=<?=$item->empresa->id.';'.$item->periodo->id.';'.$item->id?>&size=20&print=false&filepath=<?=$path.$item->id.".png"?>">
		    		</td>
		    	</tr>
		    	<?
		    }
		    ?>
		    <tr>
		    	<td>i: <?=$i?>, [<?=$item->id?>] [<?=$i?> >= <?=$_GET["ini"]?>] AND [<?=$i?> <= <?=$_GET["fin"]?>]</td>
		    </tr>
		    <?
	    	$i++;
    	}
    	?>
    </table>
</body>
</html>