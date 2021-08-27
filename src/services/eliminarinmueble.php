<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$list = array();
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"inmueble",
		"empresa"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$inmueble = new BienInmuebleDao(array(
					"id"=>$data["inmueble"]
				));
				$result = $inmueble->find($db, $queries, $log);
				if($result && $inmueble->empresa->id == $data["empresa"]){
					if($inmueble->delete($db, $queries, $log)){
						//$inmuebleFactory = new BienInmuebleFactory();
						//$list = $inmuebleFactory->filtrado($inmueble->empresa, new ClasificacionInmueble(array("id"=>$data["tipo"])), new ClasificacionInmueble(array("id"=>$data["clasificacion"])), $db, $queries, $log);
						$response["result"] = "SUCCESS";
						$response["desc"] = "Se elimino el inmueble satisfactoriamente";
						//$response["data"] = $list;
					}else{
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido eliminar el inmueble";
					}
				}else{
					$response["result"] = "FAIL";
					$response["desc"] = "El id indicado no pertenece a un inmueble de la empresa especificada";
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