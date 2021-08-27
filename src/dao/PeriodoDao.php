<?
	class PeriodoDao extends Periodo{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("periodo.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$success = $this->unserialize($rows[0]);
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}	
			return $success;
		}
	}
?>