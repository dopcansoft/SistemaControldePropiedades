<?
	class DatosCuentaFactory{
		public function listAll($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if($idEmpresa!="" && $idPeriodo!=""){
				try{
					$stmt = $db->prepare($queries->prop("reporte.cuenta.list"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						foreach($rows as $row){
							$list[] = new DatosCuenta($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("No se encontro idEmpresa/idPeriodo");
			}
			return $list;
		}

		public function filterBM($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if($idEmpresa!="" && $idPeriodo!=""){
				try{
					$stmt = $db->prepare($queries->prop("reporte.cuenta.bienes.muebles.list"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						foreach($rows as $row){
							$list[] = new DatosCuenta($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("No se encontro idEmpresa/idPeriodo");
			}
			return $list;
		}


		public function filterBI($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if($idEmpresa!="" && $idPeriodo!=""){
				try{
					$stmt = $db->prepare($queries->prop("reporte.cuenta.bienes.inmuebles.list"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						foreach($rows as $row){
							$list[] = new DatosCuenta($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("No se encontro idEmpresa/idPeriodo");
			}
			return $list;
		}

		public function listAllInstrumental($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if($idEmpresa!="" && $idPeriodo!=""){
				try{
					$stmt = $db->prepare($queries->prop("reporte.cuenta.instrumental.list"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						foreach($rows as $row){
							$list[] = new DatosCuenta($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("No se encontro idEmpresa/idPeriodo");
			}
			return $list;
		}
	}
?>