<?
	class DepartamentoFactory{
		public function listAll($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("departamento.listall"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				if(isset($rows)&&count($rows)>0){
					foreach($rows as $row){
						$list[] = new Departamento($row);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $list;
		}

		public function listByResponsable($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("departamento.listresponsable"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows)&&count($rows)>0){
					foreach($rows as $row){
						$list[] = array(
							"departamento"=>new Departamento($row, "departamento", "."),
							"responsable"=>new Responsable($row, "responsable", '.')
						);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $list;
		}

		public function getLastCounter($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("departamento.getlastcounter"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					
				}				
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}finally{
				$stmt = null;
			}	
		}

		public function allWithDetails($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("departamento.list.detalles"));
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
						$list[] = array(
							"departamento"=>new DepartamentoDao($row, 'departamento','.'),
							"responsable"=>new Responsable($row, "responsable",'.'),
							"bienes"=>$row["bienes"]
						);
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