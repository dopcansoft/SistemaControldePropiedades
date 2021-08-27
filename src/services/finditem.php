<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"scan",
		"conciliacion",
		"periodo"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$datos = explode(";",$data["scan"]);
				if(count($datos)>1){
					$log->debug($data["scan"]);
					$bien = new BienDao(array(
						"id"=>$datos[2],
						"periodo.id"=>$data["periodo"]
					));
					$log->debug('service');
					$log->debug($bien);
					if($bien->find($db, $queries, $log)){
						$item = new ItemConciliacion(array(
							"idConciliacion"=>$data["conciliacion"],
							"bien.id"=>$bien->id,
						));
						$conciliacion = new ConciliacionFisicaDao(array());
						if($conciliacion->insertDetalle($item, $db, $queries, $log)){
							$response["result"] = "SUCCESS";
							$response["desc"] = "Bien localizado y registrado";
							$response["data"] = $item;
						}else{
							$response["result"] = "FAIL";
							$response["desc"] = "Ocurrio un error al registrar";	
						}
					}else{
						$response["result"] = "FAIL";
						$response["desc"] = "Bien no encontrado";
					}
				}else{
					$response["result"] = "FAIL";
					$response["desc"] = "Formato invalido";
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
		}finally{
			$log->debug('Cierra conexion a Base de datos');
			$db = null;
		}
	}else{
		$response["result"]="FAIL";
		$response["desc"]="Request invalido, Faltan parametros";
		$log->error('Request invalido, Faltan parametros');
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>