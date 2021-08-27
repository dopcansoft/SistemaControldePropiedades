<?
	class DatosCuenta extends Model{
		public $cuenta;
		public $descr;
		public $valorHistorico;
		public $depreciacionAcumulada;
		public $valorActualizado;
		public $cantidad;
		public $valorAnterior;

		public function __construct($data, $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$this->cuenta = $this->val("cuenta", $data, $prefix, $separator);
			$this->descr = $this->val("descr", $data, $prefix, $separator);
			$this->valorHistorico = $this->val("valorHistorico", $data, $prefix, $separator);
			$this->depreciacionAcumulada = $this->val("depreciacionAcumulada", $data, $prefix, $separator);
			$this->valorActualizado = $this->val("valorActualizado", $data, $prefix, $separator);
			$this->valorAnterior = $this->val("valorAnterior", $data, $prefix, $separator);
			$this->cantidad = $this->val("cantidad", $data, $prefix, $separator);
			return true;
		}

	}
?>