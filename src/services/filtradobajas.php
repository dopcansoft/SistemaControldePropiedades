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
	$dataInput = new DataInput();
	$list = array();
	if($dataInput->validSqueme(array(
		"empresa",
		"periodo"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$idEmpresa = isset($data["empresa"])?$data["empresa"]:"";
				$idPeriodo = isset($data["periodo"])?$data["periodo"]:"";
				$tipoInventario = isset($data["tipoInventario"])?$data["tipoInventario"]:"";
				$fechaInicio = isset($data["fechaInicio"])?$data["fechaInicio"]:"";
				$fechaFin = isset($data["fechaFin"])?$data["fechaFin"]:"";
				$clasificacionBi = isset($data["clasificacionBi"])?$data["clasificacionBi"]:"";
				$clasificacionBm = isset($data["clasificacionBm"])?$data["clasificacionBm"]:"";
				$departamento = isset($data["departamento"])?$data["departamento"]:"";
				$edoFisico = isset($data["estadoFisico"])?$data["estadoFisico"]:"";
				//$estatusInventario = isset($data["estatusInventario"])?$data["estatusInventario"]:"";
				$estatusInventario = Bien::ESTATUS_INVENTARIO_BAJA;

				$_SESSION["clasificacion"] = $clasificacionBm;
				$_SESSION["departamento"] = $departamento;
				$_SESSION["edoFisico"] = $edoFisico;

				$bienFactory = new BienFactory();
				$list = $bienFactory->filtrado($idEmpresa, $idPeriodo, $tipoInventario, $fechaInicio, $fechaFin, $clasificacionBi, $clasificacionBm, $departamento, $edoFisico, $estatusInventario, $db, $queries, $settings, $log);
				
				for($i=0;$i<count($list);$i++){
					$log->debug($list[$i]->imagen);
					if(isset($list[$i]->imagen) && $list[$i]->imagen!=""){
						$list[$i]->imagen = $settings->prop("system.url").$list[$i]->imagen;
					}					
				}
				$response["result"] = "SUCCESS";
				$response["desc"] = isset($list)&&count($list)>0?"":"No se encontraron coincidencias";
				$response["data"] = $list;
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
	} 
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>