<?
	class Departamento extends Model{
		public $id;
		public $descr;
		public $idEmpresa;
		public $idPeriodo;
		public $idResponsable;
		public $folio;
		public $fechaInsert;

		public function __construct($data=null, $prefix=null, $separator="."){
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
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->idEmpresa = $this->val("idEmpresa", $data, $prefix, $separator);
				$this->idPeriodo = $this->val("idPeriodo", $data, $prefix, $separator);
				$this->idResponsable = $this->val("idResponsable", $data, $prefix, $separator);
				$this->folio = $this->val("folio", $data, $prefix, $separator);
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>