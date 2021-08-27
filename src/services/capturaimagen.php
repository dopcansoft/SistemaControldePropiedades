<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	if(isset($_FILES)){
		$log->debug($_FILES);
		if(copy($_FILES["file"]["tmp_name"], "c:/xampp/htdocs/inv/files/prueba.jpg")){
			$response["result"] = "SUCCESS";
		}else{
			$response["result"] = "FAIL";
		}
	}else{
		$log->error('No se detecto archivo de imagen');
	}
	/*try{
		$db = new DBConnector($database);
		if(isset($db)){
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
	}*/
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>