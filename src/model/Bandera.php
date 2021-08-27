<?
	class Bandera extends Model{
		public $id;
		public $descr;
		public $status;
		public $fechaInsert;
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
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->status = $this->val("status", $data, $prefix, $separator);
				$this->empresa = $this->val("empresa", $data, $prefix, $separator);
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>