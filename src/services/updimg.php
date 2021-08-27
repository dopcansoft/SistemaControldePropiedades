<?
	include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$successFile = false;
	$dataInput = new DataInput();
	$imagen = "";
	$log->debug($data);
	$log->debug($_FILES);
	if($dataInput->validSqueme(array(
		"bien",
		"nombre"
	), $data) && $dataInput->validSqueme(array(
		"imagen"
	), $_FILES)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$bien = new BienDao(array(
					"id"=>$data["bien"]
				));
	
				if(isset($_FILES["imagen"]["size"]) && $_FILES["imagen"]["size"]>0){
					if(isset($_FILES["imagen"]["size"]) && ($_FILES["imagen"]["error"]==""||$_FILES["imagen"]["error"]=="0"||$_FILES["imagen"]["error"]==0)){
						$log->debug('guardando imagen');
						$file = $_FILES["imagen"];
						$name = $data["nombre"];//str_replace(" ","",$file["name"]);
						$pathDomain = $settings->prop("system.path");
						$pathRelative = $settings->prop("bien.repository.path").date("Y-m-d")."/";
						$allowedTypes = array("image/pjpeg",
											"image/gif",
											"image/png",
											"image/jpeg",
											"image/jpg");
						$maxSize = $settings->prop("bien.img.maxsize");
						$uploader = new Uploader();
						$uploaderResponse = $uploader->saveFile($file, $name, $pathDomain.$pathRelative, array(), $allowedTypes, $maxSize, $log);
						if(isset($uploaderResponse["result"]) && $uploaderResponse["result"]=="SUCCESS"){	
							$log->debug($pathRelative.$name);
							$imagen = $pathRelative.$name;
							$bien->imagen = $imagen;
							if($bien->updateImagen($db, $queries, $log)){
								$successFile = true;
								$log->debug($settings->prop("system.url").$bien->imagen);
								$bien->imagen = $settings->prop("system.url").$bien->imagen; 
							}							
						}
					}else{
						$log->error('Existe algun tipo de error con la imagen y no puede ser subida');
						$successFile = false;
					}										
				}else{
					$log->error('No existe variable size o No es mayor que 0');
					$log->error($_FILES);
					$successFile = true;
				}
				if($successFile){
					$log->debug('Se actualizo satisfactoriamente la imagen');
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se actualizo satisfactoriamente la imagen";
					$response["data"] = $bien;
				}else{
					$log->error('Ocurrio un problema al intentar cargar la imagen, intente nuevamente');
					$response["result"] = "FAIL";
					$response["desc"] = "Ocurrio un problema al intentar cargar la imagen, intente nuevamente";					
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
		$log->error($data);
		$log->error('Request invalido, Faltan parametros');
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>