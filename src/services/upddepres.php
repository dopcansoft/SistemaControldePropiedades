<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$log->debug($data);
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"empresa",
		"periodo",
		"descr"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$departamento = new DepartamentoDao(array(
					"id"=>isset($data["departamento"])?$data["departamento"]:"",
					"descr"=>isset($data["descr"])?$data["descr"]:"",
					"idPeriodo"=>$data["periodo"],
					"idEmpresa"=>$data["empresa"],
					"folio"=>isset($data["folio"])?$data["folio"]:""
				));
				$log->debug($departamento);
				if($departamento->id==""){
					if($departamento->insert($db, $queries, $log)){
						$log->debug('insert');
						$log->debug('SUCCESS');
						$response["result"] = "SUCCESS";
						$response["desc"] = "Se almaceno satisfactoriamente la información";
					}else{
						$log->error('No se ha podido registrar el depto');
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido registrar el depto";	
					}
				}else{
					if($departamento->update($db, $queries, $log)){
						$log->debug('SUCCESS');
						$response["result"] = "SUCCESS";
						$response["desc"] = "Se actualizo satisfactoriamente la información";
						//$departamento->find($db, $queries, $log);
						//$log->debug($data["responsable"]);
						//$log->debug($departamento);
						/*if(isset($data["responsable"])){
							$log->debug('Responsable actual: '.$departamento->idResponsable);
							if($departamento->idResponsable!="" || $departamento->idResponsable=="0"){
								$departamento->idResponsable = $data["responsable"];
								$departamento->idEmpresa = isset($data["empresa"])?$data["empresa"]:0;
								$departamento->idPeriodo = isset($data["periodo"])?$data["periodo"]:0;
								$log->debug('updateAsignacion');
								if($departamento->updateAsignacion($db, $queries, $log)){
									$log->debug('UPDATEASIGNACION');
									$log->debug('SUCCESS');
									$response["result"] = "SUCCESS";
									$response["desc"] = "Se actualizo satisfactoriamente la información";
								}else{
									$log->error('No se ha podido actualizar la información');
									$response["result"] = "FAIL";
									$response["desc"] = "No se ha podido actualizar la información";	
								}
							}else{
								$log->debug('asignacion');
								$departamento->idResponsable = $data["responsable"];
								if($departamento->asignar($db, $queries, $log)){
									$log->debug('ASIGNAR');
									$log->debug('SUCCESS');
									$response["result"] = "SUCCESS";
									$response["desc"] = "Se actualizo satisfactoriamente la información";
								}else{
									$log->error('No se ha podido actualizar la información');
									$response["result"] = "FAIL";
									$response["desc"] = "No se ha podido actualizar la información";
								}
							}	
						}*/
					}else{
						$log->error('No se ha podido actualizar la información del depto.');
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido actualizar la información del depto.";
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
		}
		$log->debug('Cierra conexion a Base de datos');
		$db = null;
	}else{
		$response["result"]="FAIL";
		$response["desc"]="Request invalido, Faltan parametros";
		$log->error('Request invalido, Faltan parametros');
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>