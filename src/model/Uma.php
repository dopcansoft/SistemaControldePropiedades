<?
	class Uma extends Model{
		public $id;
		public $anio;
		public $valorDiario;
		public $valorMensual;
		public $valorAnual;
		public $factor;
		public $fechaInsert;

		public function unserialize($data, $prefix=null, $separator='.'){
			$unserialized = false;
			try{
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->anio = $this->val("anio", $data, $prefix, $separator);
				$this->valorDiario = $this->val("valorDiario", $data, $prefix, $separator);
				$this->valorMensual = $this->val("valorMensual", $data, $prefix, $separator);
				$this->valorAnual = $this->val("valorAnual", $data, $prefix, $separator);
				$this->factor = $this->val("factor", $data, $prefix, $separator);
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>