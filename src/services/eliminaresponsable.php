<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	if(isset($data["id"]) && trim($data["id"])!='' && isset($data["empresa"]) && trim($data["empresa"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				/*** code here ***/
				$responsable = new ResponsableDao(array(
					"id"=>$data["id"],
					"empresa.id"=>$data["empresa"]
				));
				if($responsable->eliminar($db, $queries, $log)){
					$log->debug("SUCCESS");
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se elimino satisfactoriamente al Responsable";
				}else{
					$log->error('FAIL');
					$response["result"] = "FAIL";
					$response["desc"] = "No se ha podido eliminar al Responsable";
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