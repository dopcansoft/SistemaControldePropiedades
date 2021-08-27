<?
	class CatalogoFactory extends Factory{
		public function listado(DBConnector $db, $query, Properties $settings, Logger $log, $outputClass){
			$list = array();
			$result = $this->exec(
				$query, null, $db, $log
			);
			//$log->debug($result);
			$list = $this->bind($outputClass, $result, array("imagen"=>array("prefix"=>$settings->prop("system.url"), "subfix"=>"")), $log);
			return $list;
		}

		public function listParams(DBConnector $db, $query, array $params, Properties $settings, Logger $log, $outputClass){
			$list = array();
			$result = $this->exec(
				$query, $params, $db, $log
			);
			$list = $this->bind($outputClass, $result, array("imagen"=>array("prefix"=>$settings->prop("system.url"), "subfix"=>"")), $log);
			return $list;
		}

	}
?>