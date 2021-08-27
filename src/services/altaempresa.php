<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"nombre",
		"descripcion",
		"idUsuario"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$empresa = new EmpresaDao(array(
					"nombre"=>$data["nombre"],
					"descr"=>$data["descripcion"]
				));
				$idUsuario = $data["idUsuario"];
				$db->beginTransaction();
				if($empresa->insertBasic($db, $queries, $log) && $empresa->insertUsuarioByEmpresa($idUsuario, Usuario::PERFIL_ADMIN, $db, $queries, $log)){
					$db->commit();
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se guardo satisfactoriamente la información";
					$response["data"] = $empresa;
				}else{
					$db->rollBack();
					$response["result"]="FAIL";
					$response["desc"]="No se ha podido guardar la información";
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