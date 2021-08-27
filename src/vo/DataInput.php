<?
	class DataInput{
		public $data;
		
		public function validSqueme($squeme, array $data){
			$valid = 0;
			if(isset($squeme) && isset($data) && count($data)>0){
				foreach($squeme as $field){
					if(isset($field) && isset($data[$field])){
						$valid++;
					}
				}
			}
			return $valid==count($squeme)&&$valid>0?true:false;
		}

		public function valid($field, array $data){
			$valid = false;
			if(isset($data) && isset($field) && isset($data[$field]) && trim($data[$field])!=''){
				$valid = true;
			}
			return $valid;
		}
	}
?>