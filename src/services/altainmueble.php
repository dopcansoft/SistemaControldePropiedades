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
	$successFile = false;
	$imagen = "";
	$images = array();
	if($dataInput->validSqueme(array(
		"folio",
		"tipo",
		"clasificacion"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){				
				if(isset($data["imagenes"]) && $data["imagenes"]>0){
					$savedImage = 0;
					for($i=0;$i<=$data["imagenes"];$i++){
						if(isset($_FILES["imagen".$i]["size"]) && $_FILES["imagen".$i]["size"]>0 && $_FILES["imagen".$i]["error"]==""){
							$name = date("YmdHis")."_".rand(0,9).rand(0,9).rand(0,9)."_".$i;
							$ext = "jpg";//pathinfo($_FILES["imagen".$i]["name"], PATHINFO_EXTENSION);
							$pathDomain = $settings->prop("system.path");
							$pathRelative = $settings->prop("inmueble.repository.path").date("Y-m-d")."/";
							$exist_dir = false;
							if(@is_dir($pathDomain.$pathRelative)){
								$exist_dir = true;
							}else{
								if(@mkdir($pathDomain.$pathRelative, 0775)){
									$exist_dir = true;
								}
							}
							if($exist_dir){
								if(move_uploaded_file($_FILES['imagen'.$i]["tmp_name"], $pathDomain.$pathRelative.$name.".".$ext)){
									$images[] = $pathRelative.$name.".".$ext;
									$savedImage++;
								}
							}										
						}
					}
					if($savedImage>=$data["imagenes"]){
						$successFile = true;
					}else{
						$successFile = false;
					}	
				}else{
					$successFile = true;	
				}

				$inmueble = new BienInmuebleDao(array(
					"folio"=>$data["folio"],
					"consecutivo"=>$data["consecutivo"],
					"empresa.id"=>$data["empresa"],
					"tipo.id"=>$data["tipo"],
					"clasificacion.id"=>$data["clasificacion"],
					"descr"=>$data["descripcion"],
					"ubicacion"=>$data["ubicacion"],
					"imagen"=>(isset($images)&&count($images)>0?$images[0]:""),
					"medNorte"=>$data["medNorte"],
					"medSur"=>$data["medSur"],
					"medEste"=>$data["medEste"],
					"medOeste"=>$data["medOeste"],
					"colNorte"=>$data["colNorte"],
					"colSur"=>$data["colSur"],
					"colEste"=>$data["colEste"],
					"colOeste"=>$data["colOeste"],
					"colNorte"=>$data["colNorte"],
					"superficieTerreno"=>$data["superficieTerreno"],
					"superficieConstruccion"=>$data["superficieConstruccion"],
					"uso.id"=>$data["uso"],
					"aprovechamiento.id"=>$data["aprovechamiento"],
					"modoAdquisicion.id"=>$data["adquisicion"],
					"servAgua"=>$data["servAgua"],
					"servDrenaje"=>$data["servDrenaje"],
					"servLuz"=>$data["servLuz"],
					"servTelefonia"=>$data["servTelefonia"],
					"servInternet"=>$data["servInternet"],
					"servGasEstacionario"=>$data["servGas"],
					"numeroEscrituraConvenio"=>$data["escritura"],
					"numRegistroPropiedad"=>$data["noRegistro"],
					"cuentaCatastral"=>$data["cuentaCatastral"],
					"fechaUltimoAvaluo"=>$data["fechaUltimoAvaluo"],
					"gravamenPendiente"=>$data["gravamen"],
					"fechaUltimoAvaluo"=>$data["fechaUltimoAvaluo"],
					"valorTerreno"=>$data["valorTerreno"],
					"valorConstruccion"=>$data["valorConstruccion"],
					"valorCapitalizable"=>$data["valorCapitalizable"],
					"valor"=>$data["valorInmueble"],
					"observaciones"=>$data["observaciones"]
				));
				$log->debug($inmueble);
				$db->beginTransaction();
				if($successFile){
					if($inmueble->insert($db, $queries, $log)){
						if($inmueble->insertImages($images, $db, $queries, $log)){
							$db->commit();
							$response["result"] = "SUCCESS";
							$response["desc"] = "Se registro satisfactoriamente el inmueble";
							$response["data"] = $inmueble;	
						}else{
							$db->rollBack();
							$log->error('No se pudieron almacenar las imágenes');
							$response["result"] = "FAIL";
							$response["desc"] = "No se pudieron almacenar las imágenes";
						}						
					}else{
						$db->rollBack();
						$response["result"] = "FAIL";
						$response["desc"] = "No se ha podido registrar el Inmueble";
						$log->error("No se ha podido registrar el Inmueble");
					}	
				}else{
					$db->rollBack();
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