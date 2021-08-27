<?
	include("../vo/config.php");
	include("../lib/phpbarcode/barcodeoffline.php");
	include('../lib/phpqrcode/qrlib.php'); 
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$successFile = false;
	$successFileFactura = false;
	$successFilePoliza = false;
	$images = array();
	$dataInput = new DataInput();
	$imagen = "";
	$images = array();
	$log->debug($data);
	$log->debug($_FILES);
	if($dataInput->validSqueme(array(
		"bien",
		"empresa",
		"periodo",
		"departamento",
		"clasificacion"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				if(isset($data["imagenes"]) && $data["imagenes"]>0){
					$savedImage = 0;
					for($i=1;$i<=$data["imagenes"];$i++){
						if(isset($_FILES["imagen".$i]["size"]) && $_FILES["imagen".$i]["size"]>0 && $_FILES["imagen".$i]["error"]==""){
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
								$log->debug('image'.$i.'|tmp:'.$_FILES['imagen'.$i]["tmp_name"]."|name:".$pathRelative.$name.".".$ext);
								if(move_uploaded_file($_FILES['imagen'.$i]["tmp_name"], $pathDomain.$pathRelative.$name.".".$ext)){
									$log->debug('SUCCESS');
									$images[] = $pathRelative.$name.".".$ext;
									$savedImage++;
								}else{
									$log->debug('ERROR AL GUARDAR IMAGEN: '.$i);
								}
							}else{
								$log->error("Directorio no existente: ".$pathDomain.$pathRelative);
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

				$bien = new BienDao(array(
					"id"=>$data["bien"],
					"empresa.id"=>$data["empresa"],
					"periodo.id"=>$data["periodo"],
					"departamento.id"=>$data["departamento"],
					"folio"=>$data["folio"],
					"folioAnterior"=>$data["folioAnterior"],
					"consecutivo"=>$data["consecutivo"],
					"imagen"=>isset($images)&&count($images)>0?$images[0]:"",
					"tipoClasificacion.id"=>$data["tipoClasificacion"],
					"origen.id"=>$data["origen"],
					"clasificacion.id"=>$data["clasificacion"],
					"cuentaContable"=>$data["cuentaContable"],
					"cuentaDepreciacion"=>$data["cuentaDepreciacion"],
					"descripcion"=>$data["descripcion"],
					"notas"=>$data["notas"],
					"marca"=>$data["marca"],
					"modelo"=>$data["modelo"],
					"serie"=>$data["serie"],
					"motor"=>$data["motor"], //TODO: Falta poner en lo interfaz
					"factura"=>$data["factura"],
					"estadoFisico.id"=>$data["estadoFisico"],
					"fechaAdquisicion"=>$data["fechaAdquisicion"],
					"tipoValuacion.id"=>$data["tipoValuacion"],
					"valor"=>$data["valuacion"],
					"uma.id"=>$data["uma"],
					"valorUma"=>$data["valorUma"],
					"inventarioContable"=>$data["inventarioContable"],
					"depreciacion.id"=>$data["depreciacion"],
					"depreciacionAcumulada"=>$data["depreciacionAcumulada"],
					"depreciacionPeriodo"=>$data["depreciacionPeriodo"],
					"aniosUso"=>$data["aniosUso"],
					"responsable.id"=>$data["responsable"],
					"color.id"=>$data["color"],
					"valorAnterior"=>$data["valorAnterior"],
					"matricula"=>$data["matricula"]
				));

				if(isset($_FILES["archivoFactura"]["size"]) && $_FILES["archivoFactura"]["size"]>0){
					if(isset($_FILES["archivoFactura"]["size"]) && $_FILES["archivoFactura"]["error"]==""){
						$log->debug('guardando archivo Factura');
						$file = $_FILES["archivoFactura"];
						$name = str_replace(" ","",$file["name"]);
						$tempName = explode(".",$file["name"]);
						$newName = $tempName[0];
						$newExt = $tempName[1];
						$pathDomain = $settings->prop("system.path");
						$pathRelative = $settings->prop("bien.repository.path").date("Y-m-d")."/";
						$maxSize = $settings->prop("bien.img.maxsize");
						//$log->debug($_FILES["archivoFactura"]["tmp_name"]);
						//$log->debug($pathDomain.$pathRelative.$newName.".".$newExt);
						$uploader = new Uploader();
						if(!$uploader->directory($pathDomain.$pathRelative, $log)){
							$successFileFactura = false;
							break 2;
						}
						if(copy($_FILES["archivoFactura"]["tmp_name"], $pathDomain.$pathRelative.$newName.".".$newExt)){
							$archivoFactura = $pathRelative.$newName.".".$newExt; 
							$bien->archivoFactura = $archivoFactura;
							$successFileFactura = $bien->updateArchivoFactura($db, $queries, $log);
						}
					}else{
						$successFileFactura = false;
					}										
				}else{
					$successFileFactura = true;
				}
				if(isset($_FILES["archivoPoliza"]["size"]) && $_FILES["archivoPoliza"]["size"]>0){
					if(isset($_FILES["archivoPoliza"]["size"]) && $_FILES["archivoPoliza"]["error"]==""){
						$log->debug('guardando archivo poliza');
						$file = $_FILES["archivoPoliza"];
						$name = str_replace(" ","",$file["name"]);
						$tempName = explode(".",$file["name"]);
						$newName = $tempName[0];
						$newExt = $tempName[1];
						$pathDomain = $settings->prop("system.path");
						$pathRelative = $settings->prop("bien.repository.path").date("Y-m-d")."/";
						$maxSize = $settings->prop("bien.img.maxsize");
						$log->debug($_FILES["archivoPoliza"]["tmp_name"]);
						$log->debug($pathDomain.$pathRelative.$newName.".".$newExt);
						if(!$uploader->directory($pathDomain.$pathRelative, $log)){
							$successFilePoliza = false;
							break 2;
						}
						if(copy($_FILES["archivoPoliza"]["tmp_name"], $pathDomain.$pathRelative.$newName.".".$newExt)){
							$archivoPoliza = $pathRelative.$newName.".".$newExt; 
							$bien->archivoPoliza = $archivoPoliza;
							$successFilePoliza = $bien->updateArchivoPoliza($db, $queries, $log);
						}
					}else{
						$log->debug('successFilePoliza: false');
						$successFilePoliza = false;
					}										
				}else{
					$successFilePoliza = true;
				}
				$log->debug('successFile: '.$successFile);
				$log->debug('successFileFactura: '.$successFileFactura);
				$log->debug('successFilePoliza: '.$successFilePoliza);
				$db->beginTransaction();
				if($successFile && $successFileFactura && $successFilePoliza){
					if($bien->update($db, $queries, $log)){
						/** creacion de imagen con codigo de barras **/
						$error = "";
						try{
							if($data["tipoEtiqueta"]!=Empresa::TIPO_ETIQUETA_QR){
								$log->debug('Creando codigo de barras');
								$filepath = $settings->prop("system.path").$settings->prop("barcode.path").$bien->id.".png";
								$log->debug($filepath);
								$text = $bien->empresa->id.';'.$bien->periodo->id.';'.$bien->id;
								$size = 20;
								$orientation="horizontal";
								$code_type="code128";
								$print = false;
								$sizefactor=1;
								@barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor);	
								$log->debug('codigo de barras creado satisfactoriamente');	
							}
						}catch(Exception $err){
							$log->debug($err);
							$error = ", no obstante ocurrio un error al generar el código de barras";
						}
						/** termina creacion de imagen con codigo de barras **/	
						$valuaciones = array();
						$arrindex = $data["v_index"];
						$arrids = $data["v_id"]; 
						$arrperiodo = $data["v_periodo"]; 
						$arrvalor = $data["v_valor"]; 
						$arrtipo = $data["v_tipoImporte"]; 
						$arrfecha = $data["v_fechaAdquisicion"]; 
						$arrfechaCierre = $data["v_fechaCierre"]; 
						$arrdepAcumulada = $data["v_depAcumulada"]; 
						$arrdepPeriodo = $data["v_depPeriodo"]; 
						//$arraniosUso = $data["v_aniosUso"]; 
						$arrdepreciacion = $data["v_tipoDepreciacion"];
						$arruma = $data["v_uma"]; 
						$arrvalorLibros = $data["v_valorLibros"];
						$arrvalorActual = $data["v_valorActual"];
						$arrestadoFisico = $data["v_edoFisico"];
						
						$i=0;
						foreach($arrindex as $index){
							$valuaciones[] = new ItemValuacion(array(
								"periodo.id"=>$arrperiodo[$i],
								"valor"=>$arrvalor[$i],
								"tipo.id"=>$arrtipo[$i],
								"fecha"=>$arrfecha[$i],
								"fechaCierre"=>$arrfechaCierre[$i],
								"depAcumulada"=>$arrdepAcumulada[$i],
								"depPeriodo"=>$arrdepPeriodo[$i],
								"aniosUso"=>0,
								"depreciacion.id"=>$arrdepreciacion[$i],
								"uma.id"=>$arruma[$i],
								"valorLibros"=>$arrvalorLibros[$i],
								"valorActual"=>$arrvalorActual[$i],
								"estadoFisico.id"=>$arrestadoFisico[$i],
								"orden"=>$arrindex[$i]
							));
							$i++;
						}
						if($bien->deleteValuaciones($db, $queries, $log)){
							if($bien->insertValuaciones($valuaciones, $db, $queries, $log)){
								if($bien->deleteImgs($settings->prop("system.path"), $db, $queries, $log)){
									if($bien->insertImages($images, $db, $queries, $log)){
										$db->commit();
										$log->debug('Se actualizo satisfactoriamente su informacion');
										$response["result"] = "SUCCESS";
										$response["desc"] = "Se actualizo satisfactoriamente su informacion";
										$response["data"] = $bien;
									}else{
										$db->rollBack();
										$log->error('No se han podido actualizar las imágenes');
										$response["result"] = "FAIL";
										$response["desc"] = "No se han podido actualizar las imágenes";
									}
								}else{
									$db->rollBack();
									$log->error('No se han podido actualizar las imagenes');
									$response["result"] = "FAIL";
									$response["desc"] = "No se pudieron eliminar las imagenes anteriores";
								}
							}else{
								$db->rollBack();
								$log->error('No se pudieron insertar las valuaciones');
								$response["result"] = "FAIL";
								$response["desc"] = "Ocurrio un error al guardar la información";
							}
						}else{
							$db->rollBack();
							$log->error('No se pudieron eliminar las valuaciones');
							$response["result"] = "FAIL";
							$response["desc"] = "Ocurrio un error al guardar la información";
						}												
					}else{
						$log->error('No se ha podido realizar la actualizacion');
						$response["result"] = "FAIL";
						$response["desc"] = "Ocurrio un problema al intentar actualizar la información";
					}
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
		$log->error('Request invalido, Faltan parametros');
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>