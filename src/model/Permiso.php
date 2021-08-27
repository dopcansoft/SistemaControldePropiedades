<?
	class Permiso extends Model{
		public $id;
		public $usuario;
		public $empresa;
		public $rol;
		public $fechaInsert;

		public function __construct($data=null, $prefix=null, $separator='.'){
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
				$this->usuario = $this->prop("usuario", $data, $prefix, $separator, "Usuario");
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "Empresa");
				$this->rol = $this->prop("rol", $data, $prefix, $separator, "ItemCatalogo");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>