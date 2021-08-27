<?
	class Depreciacion extends Model{
		public $id;
		public $cuenta;
		public $descr;
		public $vidaUtil;
		public $depreciacionAnual;
		public $tipo;
		public $seleccionable;
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
				$this->cuenta = $this->val("cuenta", $data, $prefix, $separator);
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->vidaUtil = $this->val("vidaUtil", $data, $prefix, $separator);
				$this->depreciacionAnual = $this->val("depreciacionAnual", $data, $prefix, $separator);
				$this->seleccionable = $this->val("seleccionable", $data, $prefix, $separator);
				$this->tipo = $this->prop("tipo", $data, $prefix, $separator, "ItemCatalogo");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			

	}
?>