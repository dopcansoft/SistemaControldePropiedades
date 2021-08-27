<?
	class ClasificacionFactory{
		public function resetAll($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("clasificacion.reset"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		} 
	}
?>