<?
	class PeriodoFactory extends Factory{
		public function listAll(EmpresaDao $empresa, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			$result = $this->exec($queries->prop("periodo.list"),array(
				array(":fk_id_empresa", $empresa->id, PDO::PARAM_INT)
			), $db, $log);
			$list = $this->bind("Periodo", $result, null, $log);
			return $list;	
		}
	}
?>