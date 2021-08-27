<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	if(isset($data["descr"]) && trim($data["descr"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$color = new ColorDao(array(
					"descr"=>$data["descr"],
					"idEmpresa"=>$data["empresa"]
				));
				if($color->insert($db, $queries, $log)){
					$response["result"] = "SUCCESS";
					$response["data"] = $color;
					$response["desc"] = "Se registro satisfactoriamente";
				}else{
					$response["result"] = "FAIL";
					$response["desc"] = "No se pudo realizar el registro";
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