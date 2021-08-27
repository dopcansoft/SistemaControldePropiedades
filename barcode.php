<?
	session_start();
	session_name("inv");
	include("src/vo/config.php");
	include("src/lib/phpbarcode/barcodeoffline.php");
	
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
			$bien = new BienDao(array(
				"id"=>$data["id"],
				"periodo.id"=>$data["periodo"]
			));
			if($bien->find($db, $queries, $log)){
				$log->debug($bien);
				$error = "";
				try{
					$log->debug('Creando codigo de barras');
					$filepath = $settings->prop("system.path").$settings->prop("barcode.path").$bien->id.".png";
					$log->debug($filepath);
					$text = $bien->empresa->id.';'.$bien->periodo->id.';'.$bien->id;
					$log->debug($text);
					$size = 20;
					$orientation="horizontal";
					$code_type="code128";
					$print = false;
					$sizefactor=1;
					barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor);	
					$log->debug('codigo de barras creado satisfactoriamente');
				}catch(Exception $err){
					$log->debug($err);
					$error = ", no obstante ocurrio un error al generar el código de barras";
				}
				$items[] = $bien;
			}else{
				$log->debug('ID NO ENCONTRADO');
			}
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
    	<?
    	$log->debug("generando...");
	    foreach($items as $item){	
	    	?>
	    	<tr>
	    		<td>ID: <?=$item->id?></td>
	    	</tr>
	    	<tr>
	    		<td>folio: <?=$item->folio?></td>
	    	</tr>
	    	<tr>
	    		<td>Empresa: <?=$item->empresa->id?></td>
	    	</tr>
	    	<tr>
	    		<td>Periodo: <?=$item->periodo->id?></td>
	    	</tr>
	    	<tr>
	    		<td>
	    			<img width="240px" src="files/barcodes/<?=$item->id.".png"?>">
	    		</td>
	    	</tr>
	    	<?
	    }
    	?>
    </table>
</body>
</html>