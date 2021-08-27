<?
	class FileUpload{
		public $ancho;
		public $alto;
		public $name;
		public $temp_name; 
		public $archivo;
		 
		public function __construct($archivo){
			$contiene=0;
			if(count($archivo)>0){
				foreach($archivo as $item){
					if($item["size"]>0){
						$contiene++;
					}	
				}
			}
			if($contiene>0){
				$this->archivo = $archivo[0];
				echo "SI";
			}else{
				echo "NADA";
			}			
			
		}


		/*if(isset($_FILES["imagen"]["name"])){
        $mf->setRuta(trim($settings->getProp("posts.image.path.local")));
        $mf->setNombre($_FILES["imagen"]["name"]);
        $mf->setArchivo($_FILES["imagen"]);
        $mf->setMaxSize(intval($settings->getProp("posts.image.max.size")));
        if($mf->guardar($log)){
            $success_image++;
            $log->debug("Se almaceno la imagen de la publicacion");
        }else{
            $log->debug("No se ha podido guardar la imagen de la publicacion");
        }*/

	}
?>