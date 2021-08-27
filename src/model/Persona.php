<?
	class Persona extends Model{
		public $id;
		public $nombre;
		public $apellidos;
		public $cargo;
		public $estatus;
		public $fechaInsert;

		public function __construct($data = null, $prefix = null, $separator='.'){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator='.'){
			$unserialized = false;
			try{
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->nombre = $this->val("nombre", $data, $prefix, $separator);
				$this->apellidos = $this->val("apellidos", $data, $prefix, $separator);
				$this->cargo = $this->prop("cargo", $data, $prefix, $separator, "ItemCatalogo");
				$this->estatus = $this->prop("estatus", $data, $prefix, $separator, "ItemCatalogo");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>