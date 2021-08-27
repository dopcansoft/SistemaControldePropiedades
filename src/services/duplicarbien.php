<?
	include("../vo/config.php");
	include("../lib/phpbarcode/barcodeoffline.php");
	
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$list = array();
	$dataInput = new DataInput();
	if($dataInput->validSqueme(array(
		"id",
		"periodo",
		"repeticiones",
		"empresa"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$bien = new BienDao(array(
					"id"=>$data["id"],
					"periodo.id"=>$data["periodo"]
				));
				if($bien->find($db, $queries, $log)){
					$db->beginTransaction();
					$repeticiones = $data["repeticiones"];
					$imagenOriginal = $bien->imagen;
					$successImg = false;
					$newPath = $settings->prop("bien.repository.path").date("Y-m-d")."/";
					$uploader = new Uploader();
					$bienFactory = new BienFactory();
					if($bien->clasificacion->id!=null && trim($bien->clasificacion->id)!="" && $bien->departamento->id!=null && trim($bien->departamento->id)!=""){
						for($x=0;$x<$repeticiones;$x++){
							$bien->id = null;
							$bien->imagen = "";
							$counter = $bienFactory->getIncrement($data["empresa"], $data["periodo"], $bien->clasificacion->id, $bien->departamento->id, $db, $queries, $log);
							$bien->consecutivo = $counter;
							$bien->folio = $bien->clasificacion->grupo.$bien->clasificacion->subgrupo.$bien->clasificacion->clase.$bien->clasificacion->subclase."-".$bien->departamento->folio."-".str_pad($counter,3,"0", STR_PAD_LEFT);
							
							//duplicado de n imagenes
							$i=0;
							$newImgs = array();
							foreach($bien->images as $img){
								//$name = date("YmdHis").($i++).rand(10,99);
								$name = date("YmdHis")."_".rand(0,9).rand(0,9).rand(0,9)."_".$i;
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
								$log->debug($newImgs);
							}
							//termina duplicado de n imagenes							

							if($bien->insert($db, $queries, $log)){
								if(isset($bien->images) && count($bien->images)>0){
									if($bien->insertImages($bien->images, $db, $queries, $log)){
										$list[] = $bien;	
									}else{
										$log->error('Error al crear el duplicado no: '.$x);		
									}
								}else{
									$list[] = $bien;
								}
								try{
									$log->debug('Creando codigo de barras');
									$filepath = $settings->prop("system.path").$settings->prop("barcode.path").$bien->id.".png";
									$log->debug($filepath);
									$text = $bien->empresa->id.';'.$bien->periodo->id.';'.$bien->id;
									$log->debug($text);
									$size = 20;
									$orientation="horizontal";
									$code_type="code128";
									$print = false;
									$sizefactor=1;
									@barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor);	
									$log->debug('codigo de barras creado satisfactoriamente');
								}catch(Exception $err){
									$log->debug($err);
									$error = ", no obstante ocurrio un error al generar el código de barras";
								}
							}else{
								$log->error('Error al crear el duplicado no: '.$x);
							}
						}
						if($repeticiones == count($list)){
							$db->commit();
							$log->debug('Se crearon las copias satisfactoriamente');
							$response["result"] = "SUCCESS";
							$response["desc"]  = "Se crearon las copias satisfactoriamente";
						}else{
							$db->rollBack();
							$log->debug('Ocurrio un error durante el proceso de duplicado');
							$response["result"] = "FAIL";
							$response["desc"]  = "Ocurrio un error durante el proceso de duplicado";
						}
					}else{
						$db->rollBack();
						$log->debug('El bien que se pretende duplicar no tiene clasificacion armonizada/departamento asignado');
						$response["result"] = "FAIL";
						$response["desc"]  = "El bien que se pretende duplicar no tiene clasificacion armonizada/departamento asignado";
					}	
				}else{
					$log->error('No se pudo cargar la información del bien original');
					$response["result"] = "FAIL";
					$response["desc"] = "No se pudo cargar la información del bien original";
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