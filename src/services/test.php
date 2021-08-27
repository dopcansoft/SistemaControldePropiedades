<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	
	try{
		$db = new DBConnector($database);
		if(isset($db)){
			$bien = new BienDao();
			$bien->id = 54;
			$bien->periodo->id = 2;
			$bien->find($db, $queries, $log);
			$i=0;
			$newImgs = array();
			foreach($bien->images as $img){
				$name = date("YmdHis").($i++).rand(10,99);
				$ext = "jpg";//pathinfo($_FILES["imagen".$i]["name"], PATHINFO_EXTENSION);
				$pathDomain = $settings->prop("system.path");
				$pathRelative = $settings->prop("bien.repository.path").date("Y-m-d")."/";
				$exist_dir = false;
				if(@is_dir($pathDomain.$pathRelative)){
					$exist_dir = true;
				}else{
					if(@mkdir($pathDomain.$pathRelative, 0775)){
						$exist_dir = true;
					}
				}
				if($exist_dir){
					if(copy($pathDomain.$img, $pathDomain.$pathRelative.$name.".".$ext)){
						$newImgs[] = $pathRelative.$name.".".$ext;
					}
				}
			}
			if(count($newImgs)>0){
				$bien->imagen = $newImgs[0];
				$bien->images = $newImgs;
				$response["data"] = $bien;	
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
	
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>