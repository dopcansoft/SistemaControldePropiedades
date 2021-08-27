<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$success = 0;
	//$log->debug($data);
	if(isset($data["idEmpresa"]) && trim($data["idPeriodo"])!=''){
		try{			
			$ids = $data["ids"];
			$datos = array();
			foreach($ids as $reg){
				$datos[] = new ClasificacionDao(array(
					"id"=>$reg["id"],
					"enabled"=>$reg["enabled"]
				));
			}
			$clasificacionFactory = new ClasificacionFactory();
			$db = new DBConnector($database);
			if(isset($db)){
				$db->beginTransaction();
				$clasificacionFactory->resetAll($data["idEmpresa"], $data["idPeriodo"], $db, $queries, $log);
				foreach($datos as $item){
					$res = $item->updateEstatus($data["idEmpresa"], $data["idPeriodo"], $db, $queries, $log);
					if($res){
						$success++;
					}
				}
				if(count($datos)==$success){
					$db->commit();
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se guardaron satisfactoriamente los cambios";
				}else{
					$db->rollBack();
					$response["result"] = "FAIL";
					$response["desc"] = "No se han almacenar los cambios";
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