<?
	class DepartamentoAsignado extends Departamento{
		public $responsable;
		public $periodo;
		public $empresa;

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
				parent::unserialize($data, $prefix, $separator);
				$this->responsable = $this->prop("responsable", $data, $prefix, $separator, "UsuarioDao");
				$this->periodo = $this->prop("periodo", $data, $prefix, $separator, "Periodo");
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "Empresa");
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;
		}			
	}
?>