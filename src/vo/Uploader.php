<?
	class Uploader{
		/*
		@input:
			$file: archivo a subir
			$name: nombre final del archivo
			$path: ruta destino relativa al dominio.
			$exts: extensiones aceptadas, ejem. application/pdf
			$maxSize: tamaño maximo permitido
			$log: ..
		@output: 
			$respuesta arreglo result, desc
		*/	
		public function saveFile($file, $name, $path, array $resize=array(), array $exts, $maxSize, Logger $log){
			$response = array("result"=>"FAIL", "desc"=>"");
			$resized = false;
			$counter = 0;
			if(isset($file["error"]) && ($file["error"]=="UPLOAD_ERR_OK") || $file["error"]=="" || $file["error"]=="0"){
				if($file["size"]<=$maxSize){
					if(in_array(strtoupper($file["type"]), $exts)||in_array(strtolower($file["type"]), $exts)){
						if($this->directory($path, $log)){
							if(@move_uploaded_file($file["tmp_name"], $path.$name)){
								if(isset($resize) && count($resize)>0){
									foreach($resize as $sizes){
										$tempName = explode(".",$name); 
										$newName = $tempName[0];
										$newExt = $tempName[1];
										if($this->resize($file["type"], $path.$name, $path.$newName.$sizes["sub"].".".$newExt, $sizes["width"], $sizes["height"], $log)){
											$counter++;
											$log->debug('file: '.$path.$newName.$sizes["sub"].".".$newExt." [success]");
										}else{
											$log->error('file: '.$path.$newName.$sizes["sub"].".".$newExt." [fail]");
										}
									}
									$log->debug($counter.'=='.count($resize));
									if($counter==count($resize)){
										$resized = true;
									}
								}else{
									$resized = true;
									$log->debug('No se encontraron medidas para realizar resize de imagen original');
								}
								if($resized){
									$log->debug('success');
									$response["result"] = "SUCCESS";
									$response["desc"] = "Archivo subido satisfactoriamente";				
								}else{
									$log->debug('Fail');
									$response["result"] = "FAIL";
									$response["desc"] = "No se han podido editar las imagenes";
								}								
							}else{
								$log->error('Ocurrio un problema al subir el archivo');
								$response["result"] = "FAIL";
								$response["desc"] = "Ocurrio un problema al subir el archivo";			
							}
						}else{
							$log->error('Ruta destino no se ha podido crear o es inexistente');
							$response["result"] = "FAIL";
							$response["desc"] = "Ruta destino no se ha podido crear o es inexistente";		
						}
					}else{
						$log->error('Extension no valida');
						$log->error('extension detectada: '.$file["type"]);
						$response["result"] = "FAIL";
						$response["desc"] = "Extension no valida";		
					}							
				}else{
					$log->error('Tamaño no admitido');
					$response["result"] = "FAIL";
					$response["desc"] = "Tamaño no admitido";	
				}
			}else{
				$log->error(isset($file["error"])?$file["error"]:"Error desconocido");
				$response["result"] = "FAIL";
				$response["desc"] = isset($file["error"])?$file["error"]:"Error desconocido";
			}
			return $response;
		}

		public function copyFile($original, $newPath, $newName, Logger $log){
			$response = array("result"=>"FAIL", "desc"=>"");			
			if($this->directory($newPath, $log)){
				if(@copy($original, $newPath.$newName)){
					$log->debug('Se copio el archivo, file: '.$newPath.$newName);
					$response["result"] = "SUCCESS";
					$response["desc"] = "Archivo subido satisfactoriamente";				
				}else{
					$log->error('Ocurrio un error al copiar el archivo, file: '.$newPath.$newName);
					$response["result"] = "FAIL";
					$response["desc"] = "Ocurrio un error al copiar el archivo";			
				}
			}else{
				$log->error('Ruta destino no se ha podido crear o es inexistente');
				$response["result"] = "FAIL";
				$response["desc"] = "Ruta destino no se ha podido crear o es inexistente";		
			}
			return $response;
		}


		public function saveFileOld($file, $name, $path, array $exts, $maxSize, Logger $log){
			$response = array("result"=>"FAIL", "desc"=>"");
			if(isset($file["error"]) && ($file["error"]=="UPLOAD_ERR_OK") || $file["error"]=="" || $file["error"]=="0"){
				if($file["size"]<=$maxSize){
					if(in_array(strtoupper($file["type"]), $exts)||in_array(strtolower($file["type"]), $exts)){
						if($this->directory($path, $log)){
							if(@move_uploaded_file($file["tmp_name"], $path.$name)){
								$log->debug('success');
								$response["result"] = "SUCCESS";
								$response["desc"] = "Archivo subido satisfactoriamente";			
							}else{
								$log->error('Ocurrio un problema al subir el archivo');
								$response["result"] = "FAIL";
								$response["desc"] = "Ocurrio un problema al subir el archivo";			
							}
						}else{
							$log->error('Ruta destino no se ha podido crear o es inexistente');
							$response["result"] = "FAIL";
							$response["desc"] = "Ruta destino no se ha podido crear o es inexistente";		
						}
					}else{
						$log->error('Extension no valida');
						$log->error('extension detectada: '.$file["type"]);
						$response["result"] = "FAIL";
						$response["desc"] = "Extension no valida";		
					}							
				}else{
					$log->error('Tamaño no admitido');
					$response["result"] = "FAIL";
					$response["desc"] = "Tamaño no admitido";	
				}
			}else{
				$log->error(isset($file["error"])?$file["error"]:"Error desconocido");
				$response["result"] = "FAIL";
				$response["desc"] = isset($file["error"])?$file["error"]:"Error desconocido";
			}
			return $response;
		}

		public function resize($type, $imgOrigen, $imgDestino, $ancho, $alto, Logger $log){
			$success = false;
			list($anchoOriginal, $altoOriginal) = getimagesize($imgOrigen);
			if(($ancho>0 && $alto>0) && ($anchoOriginal>$ancho || $altoOriginal>$alto)){
				$canvas = imagecreatetruecolor($ancho, $alto);
				switch($type){
					case "image/jpg":
					case "image/jpeg":
						$image = imagecreatefromjpeg($imgOrigen);
						imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho, $alto, $anchoOriginal, $altoOriginal);
						imagejpeg($canvas, $imgDestino, 100);
						$success = true;
					break;
					case "image/gif":
						$image = imagecreatefromgif($imgOrigen);
						imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho, $alto, $anchoOriginal, $altoOriginal);
						imagegif($canvas, $imgDestino, 100);
						$success = true;
					break;
					case "image/png":
						$image = imagecreatefrompng($imgOrigen);
						imagealphablending($image, FALSE);
						imagealphablending($canvas, FALSE);
						imagesavealpha($image, TRUE);
						imagesavealpha($canvas, TRUE);
						imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho, $alto, $anchoOriginal, $altoOriginal);
						imagepng($canvas, $imgDestino, 0);
						$success = true;
					break;
				}
			}else{
				$log->error('Imagen original más paqueña, aún asi se toma como valida');
				$success = success;
				//$success = move_uploaded_file($file["tmp_name"], $path.$name.".".$extension);
			}
			/*}else{
				$success = false;
				$log->error('No se ha podido crear el directorio');
			}*/			
			return $success;	
		}

		public function directory($path, $log){
			$success = false;
			if(@is_dir($path)){
				$success = true;
			}else{
				if(@mkdir($path, 0775)){
					$success = true;
				}else{
					$log->error('Ruta inexistente/inaccesible:'.$path);
					$success = false;
				}
			}
			return $success;
		}
	}
?>