<?
	class Model{
		const TYPE_STRING = "STRING";
		const TYPE_OBJECT = "OBJECT";
		const TYPE_ARRAY = "ARRAY";

		public function __construct($data=null, $prefix=null, $separator='.'){
			if(method_exists($this,'init')){
				$this->init();
			}
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(null);
			}
		}

		public function unserialize($data, $prefix=null, $separator='.'){
			$success = true;
			$vars = get_object_vars($this);
			try{
				if(isset($vars) && count($vars)>0){
					foreach($vars as $property=>$value){
						switch(strtoupper(gettype($value))){
							case $this::TYPE_OBJECT:
								$this->$property = $this->prop($property, $data, $prefix, $separator, get_class($value));
								break;
							case $this::TYPE_STRING:
								$this->$property = $this->val($property, $data, $prefix, $separator);
								break;
							case $this::TYPE_ARRAY:
								foreach($value as $val=>$valor){
									if(strtoupper(gettype($valor))==$this::TYPE_OBJECT){
										echo "[".$property.":"."]";
										$this->$property = array($this->prop($property, $data, $prefix, $separator, get_class($valor)));
									}else{
										$this->$property = array($this->val($property, $data, $prefix, $separator));	
									}
								}
								break;
						}						
					}
				}
			}catch(Exception $e){
				$success = false;
			}
			return $success;			
		}
		
		public function get($field, $data, $prefix=null, $separator='.'){
			$value = null;
			if(strtoupper(gettype($this->$field))=="OBJECT"){
				echo "[".gettype($this->$field)."]";
				$value = $this->prop($field, $data, $prefix, $separator, get_class($this->$field));
			}else{
				$value = $this->val($field, $data, $separator);
			}
		}

		public function val($field, $data, $prefix=null, $separator='.'){
			$value = isset($data[(isset($prefix)?$prefix.$separator:'').$field])?$data[(isset($prefix)?$prefix.$separator:'').$field]:"";
			return $value;
		}

		public function arr($field, $data, $prefix=null, $separator='.', $outputClass=null){
			$value = array();
			if(isset($data[(isset($prefix)?$prefix.$separator:'').$field]) && is_array($data[(isset($prefix)?$prefix.$separator:'').$field])){
				$value = isset($data[(isset($prefix)?$prefix.$separator:'').$field])?$data[(isset($prefix)?$prefix.$separator:'').$field]:array();	
			}else{
				if(isset($outputClass)){
					$value = array(
						new $outputClass($data, $prefix, $separator)
					);
				}else{
					$value = array();
				}
				
			}
			return $value;
		}

		public function prop($field, $data, $prefix=null, $separator='.', $class = null){
			$value = "";
			if(isset($class) && is_a($field, $class) && isset($data[(isset($prefix)?$prefix.$separator:'').$field])){
				$value = $data[(isset($prefix)?$prefix.$separator:'').$field];
			}else{
				$value = new $class($data, (isset($prefix)?$prefix.$separator:'').$field);
			}
			return $value;
		}

		// $extra = array("imagen"=>array("prefix"=>"algo","subfix"=>""));
		public function bind($className, array $data, array $extra=null, Logger $log){
			$list = array();
			if(isset($data) && is_array($data) && count($data)>0){
				foreach($data as $item){
					if(isset($extra)){
						foreach($item as $key=>$value){
							if($extra!=null && $this->contiene($key, $extra)){
								$item[$key] = isset($value)&&!empty($value)?(isset($extra[$key]["prefix"])?$extra[$key]["prefix"]:'').$value.(isset($extra[$key]["subfix"])?$extra[$key]["subfix"]:''):(isset($extra[$key])&&isset($extra[$key]["alternative"])?$extra[$key]["alternative"]:"");
							}
						}
					}
					$list[] = new $className($item);
				}
			}
			$log->debug('se obtuvieron: '.count($list)." registros de tipo: ".$className);
			return $list;
		}

		// $extra = array("imagen"=>array("prefix"=>"algo","subfix"=>""));
		public function bindData(array $data, array $extra=null, Logger $log){
			$list = array();
			if(isset($data) && is_array($data) && count($data)>0){
				foreach($data as $item){
					if(isset($extra)){
						foreach($item as $key=>$value){
							if($extra!=null && $this->contiene($key, $extra)){
								$item[$key] = isset($value)&&!empty($value)?(isset($extra[$key]["prefix"])?$extra[$key]["prefix"]:'').$value.(isset($extra[$key]["subfix"])?$extra[$key]["subfix"]:''):(isset($extra[$key])&&isset($extra[$key]["alternative"])?$extra[$key]["alternative"]:"");
							}
						}
					}
					$list[] = $item;
				}
			}
			return $list;
		}

		public function setter(array $data, array $extra=null){
			if(isset($data) && is_array($data) && count($data)>0){
				if(isset($extra)){
					foreach($data as $key=>$value){
						if($extra!=null && $this->contiene($key, $extra)){
							$data[$key] = isset($value)&&!empty($value)?(isset($extra[$key]["prefix"])?$extra[$key]["prefix"]:'').$value.(isset($extra[$key]["subfix"])?$extra[$key]["subfix"]:''):(isset($extra[$key])&&isset($extra[$key]["alternative"])?$extra[$key]["alternative"]:"");
						}
					}
				}
			}
			return $data;	
		}

		public function contiene($search, $data){
			$success = false;
			foreach($data as $key=>$value){
				if($search==$key){
					$success = true;
					break 1;
				}
			}
			return $success;
		}
	}
?>