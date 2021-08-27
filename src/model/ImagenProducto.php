<?
	class ImagenProducto{
		public $id;
		public $orden;
		public $path;
		public $file;

		public function __construct($data, $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator, $bind);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$this->id = $this->val("id", $data, $prefix, $separator);
			$this->orden = $this->val("orden", $data, $prefix, $separator);
			$this->path = $this->val("path", $data, $prefix, $separator);
			$this->file = $this->val("file", $data, $prefix, $separator);
			return true;
		}
	}
?>