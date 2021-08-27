<?
		include("../vo/config.php");
		Logger::configure("../config/log4php.xml");
		$log = Logger::getLogger("Responsable");
		$settings = new Properties("../config/settings.xml");
		$database = new Properties("../config/database.xml");
		$queries = new Properties("../config/queries.xml");
		$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
		$response = array("result"=>"", "desc"=>"", "data"=>array());
		
	if(isset($data["empresa"]) && trim($data["empresa"])!='' && isset($data["descr"]) && trim($data["descr"])!='' && isset($data["status"]) && trim($data["status"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
			    $bandera = new BanderaDao(array(
					"id"=>$data["id"],
					"descr"=>$data["descr"],
					"status"=>$data["status"],
					"empresa"=>$data["empresa"]
				));
				$texto = "fallo general has muerto";
				
				$log->debug($bandera);
				if($bandera->update($db, $queries, $log)){
					$log->debug('SUCCESS');
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se actualizo satisfactoriamente la asignacion";
					
				}else{
					$log->error('FAIL');
					$response["result"] = "FAIL";
					$response["desc"] = $texto;
				
				}

			}else{
				$log->error("No se ha podido establecer conexion con base de datos");
				$response["result"]="FAIL";
				$response["desc"]="No se ha podido establecer conexion con base de datos";		
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