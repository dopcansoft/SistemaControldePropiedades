<?
	class Clasificacion extends Model{
		public $id;
		public $grupo;
		public $subgrupo;
		public $clase;
		public $subclase;
		public $consecutivo;
		public $descr;
		public $cuentaContable;
		public $cuentaDepreciacion;

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
				$this->grupo = $this->val("grupo", $data, $prefix, $separator);
				$this->subgrupo = $this->val("subgrupo", $data, $prefix, $separator);
				$this->clase = $this->val("clase", $data, $prefix, $separator);
				$this->subclase = $this->val("subclase", $data, $prefix, $separator);
				$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->cuentaContable = $this->val("cuentaContable", $data, $prefix, $separator);
				$this->cuentaDepreciacion = $this->val("cuentaDepreciacion", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			

	}
?>