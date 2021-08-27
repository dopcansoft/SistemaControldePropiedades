<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("Asignacion");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	if(isset($data["empresa"]) && trim($data["empresa"])!='' && isset($data["departamento"]) && trim($data["departamento"])!='' && isset($data["periodo"]) && trim($data["periodo"])!='' && isset($data["responsable"]) && trim($data["responsable"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$responsable = new ResponsableDao(array(
					"id"=>$data["responsable"],
					"empresa.id"=>$data["empresa"],
					"departamento.id"=>$data["departamento"],
					"departamento.folio"=>$data["folio"],
					"periodo.id"=>$data["periodo"]
				));
				$log->debug($responsable);
				if($responsable->updateAsignacion($db, $queries, $log)){
					$log->debug('SUCCESS');
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se actualizo satisfactoriamente la asignación";
				}else{
					$log->error('FAIL');
					$response["result"] = "FAIL";
					$response["desc"] = "No se ha podido actualizar la asignación";
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