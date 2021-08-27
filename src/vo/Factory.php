<?
	class Factory{
		public function exec($query, array $params=null, DBConnector $db, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($query);
				if(isset($params) && count($params)>0){
					foreach($params as $param){
						$stmt->bindParam($param[0], $param[1], $param[2]);
					}
				}				
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$list = $rows;
				$log->debug('Se encontraron: '.count($rows)." registro(s)");
			}catch(PDOException $e){
				$log->error('PDOException: '.$e->getMessage());
			}
			return $list;
		}

		// $extra = array("imagen"=>array("prefix"=>"algo","subfix"=>""));
		public function bind($className, array $data, array $extra=null, Logger $log){
			$list = array();
			if(isset($data) && is_array($data) && count($data)>0){
				foreach($data as $item){
					if(isset($extra)){
						foreach($item as $key=>$value){
							if($extra!=null && $this->contiene($key, $extra)){
								if(isset($extra[$key]["alternative"])){
									//isset($extra[$key])&&isset($extra[$key]["alternative"])?$extra[$key]["alternative"]:""
								}								
								$item[$key] = isset($value)&&!empty($value)?(isset($extra[$key]["prefix"])?$extra[$key]["prefix"]:'').$value.(isset($extra[$key]["subfix"])?$extra[$key]["subfix"]:''):(isset($extra[$key])&&isset($extra[$key]["alternative"])?$extra[$key]["alternative"]:"");
							}
						}
					}
					//$log->debug(new $className($item));
					$list[] = new $className($item);
				}
			}
			$log->debug('se obtuvieron: '.count($list)." registros de tipo: ".$className);
			return $list;
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