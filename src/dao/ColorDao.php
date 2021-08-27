<?
	class ColorDao extends Color{
		public function insert(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("color.insert"));
				$stmt->bindParam(":descr", $this->descr);
				$stmt->bindParam(":fk_id_empresa", $this->idEmpresa);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
					$this->id = $db->lastInsertId();
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}
	}
?>