<?
	class DepartamentoAsignadoDao extends DepartamentoAsignado{
		public function find($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				if($this->id!=null&&$this->id!=""){
					$log->debug('departamentoasignado.find');
					$stmt = $db->prepare($queries->prop("departamentoasignado.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);					
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows)&&count($rows)>0){
						$success = $this->unserialize($rows[0]);
					}
				}else{
					$log->error('Falta id de departamento');
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}
	}
?>