<?
	session_start();
	session_name("inv");
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("login");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "link"=>"","data"=>array());
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"email",
		"password"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$usuario = new UsuarioDao(array(
					"email"=>$data["email"],
					"password"=>$data["password"]
				));
				if($usuario->findByLogin($db, $queries, array(
					"avatar"=>array("prefix"=>$settings->prop("system.url"), "subfix"=>"", "alternative"=>$settings->prop("system.url").$settings->prop("usuario.noavatar"))
				), $log)){
					$log->debug($usuario);
					if($usuario->estatus->id == Usuario::ESTATUS_ACTIVO){
						$log->debug('result: SUCCESS');
						$log->debug('session: '.$data["session"]);
						if(isset($data["session"]) && $data["session"]=="ON"){
							$log->debug('crea session');
							unset($usuario->password);
							$_SESSION["usuario"] = serialize($usuario);
						}
						$response["result"]="SUCCESS";
						$response["desc"]="Bienvenido";
						$response["link"]=$settings->prop("index.url");
					}else{
						$log->error('Estatus no activo');
						$response["result"]="FAIL";
						$response["desc"]=$usuario->estatus->descr;
					}
				}else{
					$log->error('Email/password incorrecto');
					$response["result"]="FAIL";
					$response["desc"]="Email y/o password incorrecto";
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