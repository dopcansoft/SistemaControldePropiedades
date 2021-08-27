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
	if(isset($data["ids"]) && trim($data["ids"])!='' && isset($data["periodo"]) && trim($data["periodo"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$ids = explode("|",$data["ids"]);
				$db->beginTransaction();
				$stmt = $db->prepare($queries->prop("bien.estatusinventario.update"));
				foreach ($ids as $claves){
					$id = explode(",", $claves);
					$bien = new BienDao(array(
						"id"=>$id[0],
						"estatusInventario.id"=>$id[1],
						"periodo.id"=>$data["periodo"]));
					if($bien->updateBatchEstatusInv($stmt, $log)){
						$success++;
					}
				}
				if(count($ids)==$success){
					$db->commit();
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se actualizaron los registros seleccionados";
				}else{
					$db->rollBack();
					$response["result"] = "FAIL";
					$response["desc"] = "No se han podido actualizar los registros seleccionados";
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