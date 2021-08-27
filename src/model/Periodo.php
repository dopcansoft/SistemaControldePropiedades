<?
	class Periodo extends Model{
		public $id;
		public $idEmpresa;
		public $descr;
		public $fechaInicio;
		public $fechaFin;
		public $fechaCierre;
		public $fechaInsert;
		public $uma;

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
				$this->idEmpresa = $this->val("idEmpresa", $data, $prefix, $separator);
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->fechaInicio = $this->val("fechaInicio", $data, $prefix, $separator);
				$this->fechaFin = $this->val("fechaFin", $data, $prefix, $separator);
				$this->fechaCierre = $this->val("fechaCierre", $data, $prefix, $separator);
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$this->uma = $this->prop("uma", $data, $prefix, $separator,'Uma');
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;
		}
	}
?>