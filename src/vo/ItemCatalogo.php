<?
	class ItemCatalogo{
		public $id;
		public $descr;

		public function __construct($data=null, $prefix=null, $separator='.'){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}
		}

		public function unserialize($data,$prefix=null, $separator='.'){
			$unserialized = false;
			try{
				$this->id = isset($data[(isset($prefix)?$prefix.$separator:'')."id"])?$data[(isset($prefix)?$prefix.$separator:'')."id"]:"";
				$this->descr = isset($data[(isset($prefix)?$prefix.$separator:'')."descr"])?$data[(isset($prefix)?$prefix.$separator:'')."descr"]:"";
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}

		public function toArray($prefix = null){
			$prefix = $prefix!=null?$prefix:"";
			$out = array();
			$out[$prefix."id"] = $this->id!=null?$this->id:"";
			$out[$prefix."descr"] = $this->descr!=null?$this->descr:"";
			return $out;
		}
	}
?>