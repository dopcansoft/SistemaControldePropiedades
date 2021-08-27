<?
	class ResponsableFactory{
		public function listAll($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("responsable.list"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
						$list[] = new ResponsableDao($row);
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