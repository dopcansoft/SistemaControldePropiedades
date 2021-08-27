<?
	class MediaFile{
		public $nombre;
		public $ruta;
		public $archivo;
		public $maxSize;

		function setNombre($nombre){
			$this->nombre=$nombre;
		}

		function getNombre(){
			return $this->nombre;
		}

		function setRuta($ruta){
			$this->ruta=$ruta;
		}

		function getRuta(){
			return $this->ruta;
		}

		function setArchivo($archivo){
			$this->archivo=$archivo;
		}

		function getArchivo(){
			return $this->archivo;
		}

		function setMaxSize($maxSize){
			$this->maxSize=$maxSize;
		}

		function getMaxSize(){
			return $this->maxSize;
		}

		public function validName($nombreOriginal){
			$i=1;
        	$nombreFile = $nombreOriginal;
        	while(file_exists($this->getRuta().$nombreFile)){
        		$nombreFile = $this->renameWithIdx($nombreOriginal,$i);
        		$i++;
        	}
        	return $nombreFile;
		}
		
		public function guardar(Logger $log){
			$result = false;
			$log->trace("MediaFile->guardar");
			$nombreFile = $this->nombre;
			if($this->getArchivo()["size"]<intval($this->getMaxSize()) && $this->getArchivo()["size"] > 0){
	            $log->debug("Imagen del tamaño adecuado");
	            if(strtolower($this->getArchivo()["type"])=="image/png"||strtolower($this->getArchivo()["type"])=="image/jpg"||strtolower($this->getArchivo()["type"])=="image/jpeg"){
	            	/*$i=1;
	            	$nombreOriginal=$nombreFile;
	            	while(file_exists($this->getRuta().$nombreFile)){
	            		$nombreFile = $this->renameWithIdx($nombreOriginal,$i);
	            		$i++;
	            	}*/
	                try{
	                	$log->debug("imagen: ".$this->getArchivo()["tmp_name"]);
	                	$log->debug("nueva ruta: ".$this->getRuta());
	                	$log->debug("nombre: ".$nombreFile);
	              
	                	if(move_uploaded_file($this->getArchivo()["tmp_name"],$this->getRuta().$nombreFile)){
		                	$this->setNombre($nombreFile);
		                	$result = true;
		                }else{
		                	$result = false;
		                }	
	                }catch(Exception $e){
	                	$log->error("Exception: ".$e->getMessage());
	                	$result = false;
	                }catch(Error $e){
	                	$log->error("Error: ".$e->getMessage());
	                	$result = false;
	                }
	            }else{
	                $log->error("Extension de imagen no permitida");
	                $result = false;    
	            }        
	        }else{
	        	$log->error("Tamaño no permitido");
	            $result = false;
	        }
	        return $result; 
		}

		public function eliminar(Logger $log){
			$response = false;
			$log->trace("MediaFile->eliminar");
			if(file_exists($this->getRuta().$this->getNombre())){
				$response = unlink($this->getRuta().$this->getNombre());
			}else{
				$response = false;
				$log->trace("Imagen no encontrada, no fue necesario eliminar");
			}
			return $response;
		}

		function renameWithDate($str){
			if(trim($str)!=""){
				$fecha = getdate();
				$archivoName = explode(".",$str);
				$str_mod=substr($str, 0,(strlen($str)-strlen($archivoName[count($archivoName)-1]))-1);
				$str=$str_mod.".".$fecha["year"].".".$fecha["mon"].".".$fecha["mday"].".".$archivoName[count($archivoName)-1];	
				return $str;
			}else{
				return "";
			}
		}

		function renameWithIdx($str,$idx){
			$archivoName = explode(".",$str);
			$str_mod=substr($str, 0,(strlen($str)-strlen($archivoName[count($archivoName)-1]))-1);
			$str=$str_mod.".".$idx.".".$archivoName[count($archivoName)-1];
			return $str;	
		}

		function base64ToImage( $base64_string, $output_file){
		    // open the output file for writing
		    $ifp = fopen( $output_file, 'wb' ); 

		    // split the string on commas
		    // $data[ 0 ] == "data:image/png;base64"
		    // $data[ 1 ] == <actual base64 string>
		    $data = explode( ',', $base64_string );
		    $elements = count($data);
		    // we could add validation here with ensuring count( $data ) > 1
		    if($elements>1){
		    	fwrite($ifp, base64_decode($data[1]));	
		    }else{
		    	fwrite($ifp, base64_decode($base64_string));
		    }
		    

		    // clean up the file resource
		    fclose( $ifp ); 

		    return $output_file; 
		}

		function guardarBase64( $base64_string, $output_file){
		    // open the output file for writing
		    $ifp = fopen( $output_file, 'wb' ); 

		    // split the string on commas
		    // $data[ 0 ] == "data:image/png;base64"
		    // $data[ 1 ] == <actual base64 string>
		    $data = explode( ',', $base64_string );
		    $elements = count($data);
		    // we could add validation here with ensuring count( $data ) > 1
		    if(count($data)>1){
		    	fwrite($ifp, base64_decode($data[1]));	
		    }else{
		    	fwrite($ifp, base64_decode($base64_string));
		    }
		    

		    // clean up the file resource
		    fclose( $ifp ); 

		    return $output_file; 
		}

	}
?>