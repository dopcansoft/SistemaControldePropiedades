<?
		include("../vo/config.php");
		Logger::configure("../config/log4php.xml");
		$log = Logger::getLogger("empresas");
		$settings = new Properties("../config/settings.xml");
		$database = new Properties("../config/database.xml");
		$queries = new Properties("../config/queries.xml");
		$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
		$response = array("result"=>"", "desc"=>"", "data"=>array());
		
		$dataInput = new DataInput();
		if($dataInput->validSqueme(array(
			"empresa",
			"periodo",
			"titulo",
			"nombre",
			"apellidos"
		), $data)){
			try{
				$db = new DBConnector($database);
				if(isset($db)){
					$responsable = null;
					if(isset($data["responsable"]) && $data["responsable"]!=""){
						$responsable = new ResponsableDao(array(
							"id"=>$data["responsable"],
							"titulo"=>$data["titulo"],
							"nombre"=>$data["nombre"],
							"apellido"=>$data["apellidos"],
							"empresa.id"=>$data["empresa"]
						));
						if($responsable->update($db, $queries, $log)){
							$response["result"] = "SUCCESS";
							$response["desc"] = "Se actualizo satisfactoriamente";
							$log->debug('SUCCESS');	 
							$log->debug('Se actualizo satisfactoriamente');
						}else{
							$response["result"] = "FAIL";
							$response["desc"] = "No se ha podido actualizar la información";
							$log->debug('FAIL');
							$log->debug("No se ha podido actualizar la información");
						}
					}else{
						$responsable = new ResponsableDao(array(
							"titulo"=>$data["titulo"],
							"nombre"=>$data["nombre"],
							"apellido"=>$data["apellidos"],
							"empresa.id"=>$data["empresa"]
						));
						if($responsable->insert($db, $queries, $log)){
							$response["result"] = "SUCCESS";
							$response["desc"] = "Se guardo satisfactoriamente";
							$response["data"] = $responsable;
							$log->debug('SUCCESS');	 
							$log->debug('Se guardo satisfactoriamente');
						}else{
							$response["result"] = "FAIL";
							$response["desc"] = "No se ha podido guardar la información";
							$log->debug('FAIL');
							$log->debug("No se ha podido guardar la información");
						}
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