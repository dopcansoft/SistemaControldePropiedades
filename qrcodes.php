<?
	ini_set('max_execution_time', 300);
    ini_set('memory_limit', '256M');
    include("src/vo/config.php");
    include('src/lib/phpqrcode/qrlib.php');
	Logger::configure("src/config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("src/config/settings.xml");
	$database = new Properties("src/config/database.xml");
	$queries = new Properties("src/config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());	
	$dataInput = new DataInput();
	$list = array();
	if($dataInput->validSqueme(array(
		"empresa",
		"periodo"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$idEmpresa = isset($data["empresa"])?$data["empresa"]:"";
				$idPeriodo = isset($data["periodo"])?$data["periodo"]:"";
				$tipoInventario = isset($data["tipoInventario"])?$data["tipoInventario"]:"";
				$fechaInicio = isset($data["fechaInicio"])?$data["fechaInicio"]:"";
				$fechaFin = isset($data["fechaFin"])?$data["fechaFin"]:"";
				$clasificacionBi = isset($data["clasificacionBi"])?$data["clasificacionBi"]:"";
				$clasificacionBm = isset($data["clasificacionBm"])?$data["clasificacionBm"]:"";
				$departamento = isset($data["departamento"])?$data["departamento"]:"";
				$edoFisico = isset($data["estadoFisico"])?$data["estadoFisico"]:"";
				//$estatusInventario = isset($data["estatusInventario"])?$data["estatusInventario"]:"";
				$estatusInventario = ""; //Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
				$tipoFecha = isset($data["tipoFecha"])?$data["tipoFecha"]:"FECHA_INSERT";

				//$_SESSION["clasificacion"] = $clasificacionBm;
				//$_SESSION["departamento"] = $departamento;
				//$_SESSION["edoFisico"] = $edoFisico;

				$bienFactory = new BienFactory();
				$list = $bienFactory->filtrado($idEmpresa, $idPeriodo, $tipoInventario, $fechaInicio, $fechaFin, $clasificacionBi, $clasificacionBm, $departamento, $edoFisico, $estatusInventario, $db, $queries, $settings, $log, $tipoFecha);
				$qrs = array();
				foreach($list as $item){
					$log->debug('Creando codigo de barras id: '.$item->id." - folio unico: ".$item->folioUnico);
					$filepath = $settings->prop("system.path").$settings->prop("qrcode.path").$item->id.".png";
					$text = $settings->prop("system.url").$settings->prop("qrcode.link").$item->id."/".$item->empresa->id."/".$item->periodo->id;
					$success = false;
					$log->debug('Existe: '.$filepath.": ".file_exists($filepath));
					if(!file_exists($filepath)){
						QRcode::png($text, $filepath, "L", 3);
						if(file_exists($filepath)){
							$success = true;
							$log->debug('codigo QR creado satisfactoriamente');
						}else{
							$log->debug('Ocurrio un error al generar el código QR');
						}	
					}else{
						$log->debug('QR Existente');
					}
										
					$qrs[] = array(
						"bien"=>$item->id,
						"data"=>$text,
						"path"=>$filepath,
						"img"=>$settings->prop("system.url").$settings->prop("qrcode.path").$item->id.".png",
						"result"=>$success
					);
				}
				$response["result"] = "SUCCESS";
				$response["desc"] = isset($list)&&count($list)>0?"":"No se encontraron coincidencias";
				$response["data"] = $qrs;
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
	}else{
		$log->error("Falta id de empresa y periodo");
		$response["result"]="FAIL";
		$response["desc"]="Falta id de empresa y periodo";
	} 
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>