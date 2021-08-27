<?
	session_start();
	session_name("inv");
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	if(isset($data["empresa"]) && trim($data["empresa"])!=''){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$empresa = new EmpresaDao(array(
					"id"=>$data["empresa"]
				));
				$periodo = new PeriodoDao(array(
					"id"=>$data["periodo"],
					"idEmpresa"=>$data["empresa"]
				));
				if($empresa->find($db, $queries, $log) && $periodo->find($db, $queries, $log)){
					if(trim($empresa->tipoEtiqueta)==""){
						$empresa->tipoEtiqueta = $settings->prop("system.label.default");
					}
					$_SESSION["empresa"] = serialize($empresa);
					$_SESSION["periodo"] = serialize($periodo);
					$usuario = unserialize($_SESSION["usuario"]);
					$usuario->getPerfil($empresa->id, $db, $queries, $log);
					$log->debug($usuario);
					$_SESSION["usuario"] = serialize($usuario);
					$response["link"] = $settings->prop("main.url");
					$response["result"] = "SUCCESS";
					$response["desc"] = "Bienvenido";
				}else{
					$response["result"] = "FAIL";
					$response["desc"] = "No se ha podido obtener informacion de la empresa seleccionada";
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