<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$dataInput = new DaraInput();
	if($dataInput->validSqueme(array(
		"empresa",
		"periodo",
		"departamento",
		"descr"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				if($data["departamento"]!=""){
					$departamento = new DepartamentoDao(array(
						"id"=>isset($data["departamento"])&$data["departamento"]!=""?$data["departamento"]:"",
						"descr"=>$data["descr"],
						"idEmpresa"=>$data["empresa"],
						"idPeriodo"=>$data["periodo"],
						"folio"=>$data["folio"]
					));
					$db->beginTransaction();
					if($departamento->insert($db, $queries, $log)){
						$response["result"] = "SUCCESS";
						$response["data"] = $departamento;
						$response["desc"] = "Se almaceno satisfactoriamente";
						$log->debug('SUCCESS');
						$db->commit();
					}else{
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido almacenar la información solicitada";
						$log->debug('FAIL');
						$db->rollBack();
					}
				}else{
					$departamento = new DepartamentoDao(array(
						"id"=>isset($data["departamento"])&$data["departamento"]!=""?$data["departamento"]:"",
						"descr"=>$data["descr"],
						"idEmpresa"=>$data["empresa"],
						"idPeriodo"=>$data["periodo"],
						"folio"=>$data["folio"]
					));
					if($departamento->update($db, $queries, $log)){
						$response["result"] = "SUCCESS";
						$response["desc"] = "Se actualizo satisfactoriamente";
						$log->debug('SUCCESS');
						$db->commit();
					}else{
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido actualizar satisfactoriamente";
						$log->debug('FAIL');
						$db->rollBack();
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