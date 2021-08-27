<?
	class ClasificacionDao extends Clasificacion{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($this->id) && $this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("clasificacion.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$success = $this->unserialize($rows[0]);
					}	
				}catch(PDOException $e) {
					$log->error("Exception: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("Falta id de la clasificacion");
			}
			return $success;
		}

		public function updateEstatus($idEmpresa, $idPeriodo, DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("clasificacion.updateestatus"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_clasificacion_bien", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":estatus", $this->enabled, PDO::PARAM_STR);
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