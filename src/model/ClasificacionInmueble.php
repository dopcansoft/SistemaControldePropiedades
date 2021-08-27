<?
	class ClasificacionInmueble extends Model{
		public $id;
		public $clasificador;
		public $consecutivo;
		public $descr;
		public $cuentaContable;
		public $fechaInsert;

		public function __construct($data=null, $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$this->id = $this->val("id", $data, $prefix, $separator);
			$this->clasificador = $this->val("clasificador", $data, $prefix, $separator);
			$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);
			$this->descr = $this->val("descr", $data, $prefix, $separator);
			$this->cuentaContable = $this->val("cuentaContable", $data, $prefix, $separator);
			$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
			return true;
		}
	}
?>