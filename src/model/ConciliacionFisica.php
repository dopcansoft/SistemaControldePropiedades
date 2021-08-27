<?
	class ConciliacionFisica extends Model{
		public $id;
		public $usuario;
		public $departamento;
		public $periodo;
		public $empresa;
		public $items;
		public $fechaInsert;
		public $timeInsert;

		public function __construct($data=array(), $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$success = false;
			if(isset($data)){
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->usuario = $this->prop("usuario", $data, $prefix, $separator, "Usuario");
				$this->departamento = $this->prop("departamento", $data, $prefix, $separator, "Departamento");
				$this->periodo = $this->prop("periodo", $data, $prefix, $separator, "Periodo");
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "Empresa");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$this->timeInsert = $this->val("timeInsert", $data, $prefix, $separator);
				$success = true;
			}	
			return $success;
		}
	}
?>