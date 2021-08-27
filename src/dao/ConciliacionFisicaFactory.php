<?
	class ConciliacionFisicaFactory{
		public function filter($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$log->debug('ejecuta');
				$stmt = $db->prepare($queries->prop("conciliacionfisica.filter"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$log->debug('obtiene: '.count($rows)." registro(s)");
				if(isset($rows) && count($rows)>0 ){
					foreach($rows as $row){
						$list[] = new ConciliacionFisica($row);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $list;
		}	
	}
?>