<?
	class DepreciacionFactory extends Factory{
		public function listAll(DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			$result = $this->exec($queries->prop("catdepreciacion.list"), null, $db, $log);	
			$list = $this->bind("DepreciacionDao", $result, null, $log);
			return $list;
		}
	}
?>