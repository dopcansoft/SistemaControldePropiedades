<?
	spl_autoload_register("loadAll");
	
	function loadAll($str){
		$source = findSettingsPath("config","classmap.xml");
		$sourceSettings = findSettingsPath("config","settings.xml");
		$success = false;
		if(!class_exists("Properties")){	
			//error_log("NO Existe clase Properties\n",0);
			$settings = getProperties($sourceSettings);
			$data = getProperties($source);
			$clases = array();
			foreach($data as $key => $value){
				$clases[$key] = trim($value);
			}
			foreach ($clases as $key => $value){
				if($key==$str){
					//echo $clases["rootPath"].$value;
					include $settings["system.path"].$value;
					//error_log("incluyendo: ".$settings["system.path"].$value."\n",0);
					$success = true;
				}
			}
		}else{
			//error_log("Existe clase Properties\n",0);
			$settings = new Properties($sourceSettings);
			$classmap = new Properties($source);
			$clases = $classmap->data;
			foreach($clases as $key => $value){
				$clases[$key] = trim($value);
			}
			foreach ($clases as $key => $value){
				if($key==$str){
					//error_log("Incluye: ".$settings->prop("system.path").$value."\n",0);
					include $settings->prop("system.path").$value;
					$success = true;
				}
			}
		}
		if(!$success){
			//echo $str;
			$items = explode("\\",$str);
			$str = isset($items)&&count($items)>0?$items[count($items)-1]:$str; 
			$data = getProp($source);
			$settings = getProperties($sourceSettings);
			$path = $settings["system.path"]."src/lib/PayPal/";
			$dirs = listar_directorios_ruta($path);
			foreach($dirs as $dir){
				$dir = str_replace("\\","/",$dir);
				//echo $dir."/".$str.".php"."</br>";
				if(file_exists($dir."/".$str.".php")){
					//echo $dir."/".$str.".php</br>";
			 		include($dir."/".$str.".php");
			 		$success = true;
				}
			}
		}
		if(!$success){
			//echo $str;
			$items = explode("\\",$str);
			$str = isset($items)&&count($items)>0?$items[count($items)-1]:$str; 
			$data = getProp($source);
			$settings = getProperties($sourceSettings);
			$path = $settings["system.path"]."src/lib/Psr/";
			$dirs = listar_directorios_ruta($path);
			foreach($dirs as $dir){
				$dir = str_replace("\\","/",$dir);
				if(file_exists($dir."/".$str.".php")){
					include($dir."/".$str.".php");
				}
			}
		}
	}


	function listar_directorios_ruta($ruta){ 
		// abrir un directorio y listarlo recursivo 
		$directorios = array();
		if(is_dir($ruta)){ 
	      if($dh = opendir($ruta)){ 
	         while(($file = readdir($dh)) !== false){ 
	            //esta línea la utilizaríamos si queremos listar todo lo que hay en el directorio 
	            //mostraría tanto archivos como directorios 
	            //echo "<br>Nombre de archivo: $file : Es un: " . filetype($ruta . $file); 
	            if (is_dir($ruta . $file) && $file!="." && $file!=".."){ 
	               //solo si el archivo es un directorio, distinto que "." y ".." 
	               $directorios[] = $ruta.$file;
	               //echo "<br>Directorio: $ruta$file"; 
	               $directorio[] = listar_directorios_ruta($ruta . $file . "/"); 
	            } 
	         } 
	      	closedir($dh); 
	      } 
	   }
	   return $directorios;
	 }
	/** Check compatibility on windows platform!! **/
	function findSettingsPath($targetDirectory, $targetFile){
		$path = explode(DIRECTORY_SEPARATOR, __DIR__);
		$elements = count($path);
		$fileName = $targetDirectory.DIRECTORY_SEPARATOR.$targetFile;
		$success = false;
		$dinamicPath = "";
		$completePath = "";
		while($elements>0){
			$dinamicPath = getDinamicPathReverse($path, DIRECTORY_SEPARATOR, $elements);	
			if(file_exists($dinamicPath.$fileName)){
				$completePath = $dinamicPath.$fileName;
				//error_log("encontrado: ".$dinamicPath.$fileName."\n",0);
				//echo "encontrado: ".$dinamicPath.$fileName."</br>";
				$success = true;
				break;
			}else{
				//echo "no esta: ".$dinamicPath.$fileName."</br>";
				//error_log("no esta: ".$dinamicPath.$fileName."\n",0);
				$dinamicPath = "";
			}
			$elements = $elements-1;		
		}
		return $completePath;
	}


	function pathClass($str, $libPath){
		echo $str;
		$str = $libPath."/".str_replace("\\","/",$str).".php";
		return $str;
	}

	function getDinamicPathReverse($directories, $separator, $flag){
		$path = "";
		if(count($directories)>0){
			for($i=0;$i<$flag;$i++){
				$path = $path.$directories[$i].$separator;
			}
		}
		return $path;
	}


	/* Emulamos la funcionalidad de la clase Properties para extraer la informacion de un xml */ 
	function getProperties($pathFile){
		$data = array();
		$xml = "";
		if(file_exists($pathFile)){
			if(isset($pathFile)){
				$data = array();
				$xml = simplexml_load_file($pathFile);
				foreach($xml->entry as $nodo){
					$data[(string)$nodo->attributes()["key"]] = (string)$nodo;
				}
				//error_log("properties encontradas: ".count($data)."\n",0);
			}
		}else{
			//error_log("No existe archivo: ".$pathFile."\n",0);
		}
		return $data;
	}

	function getProp($pathFile){
		$data = array();
		$xml = "";
		if(file_exists($pathFile)){
			if(isset($pathFile)){
				$data = array();
				$xml = simplexml_load_file($pathFile);
				foreach($xml->entry as $nodo){
					$data[(string)$nodo->attributes()["key"]] = (string)$nodo;
				}
			}
		}
		return $data;
	}
?>