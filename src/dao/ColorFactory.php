<?
	class ColorFactory{
		public function listAll($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("color.list"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa);
				$result = $stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0 ){
					foreach($rows as $row){
						$list[] = new ColorDao($row);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;			
		}
	}
?>