<?
	class ResponsableDao extends Responsable{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("responsable.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows)&&count($rows)){
						$success = $this->unserialize($rows[0]);	
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id de responsable');
			}
			return $success;
		}

		public function insert(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.insert"));
				$stmt->bindParam(":titulo", $this->titulo, PDO::PARAM_STR);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
					$this->id = $db->lastInsertId();
					$log->debug('Se almaceno exitosamente el responsable id: '.$this->id);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function update(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.update"));
				$stmt->bindParam(":titulo", $this->titulo, PDO::PARAM_STR);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
					$log->debug('Se actualizo exitosamente el responsable id: '.$this->id);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function eliminar(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.eliminar"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
					$log->debug('Se elimino satisfactoriamente el responsable id: '.$this->id);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function findByDepartamento($idDepartamento, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($idDepartamento!=null&&$idDepartamento!=""){
				try{
					$stmt = $db->prepare($queries->prop("responsable.findbydepartamento"));
					$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_departamento", $idDepartamento, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows)&&count($rows)){
						$success = $this->unserialize($rows[0]);	
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id de responsable');
			}
			return $success;
		}

		public function findByCargo($idEmpresa, $idCargo, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($idCargo!=null&&$idCargo!=""){
				try{
					$stmt = $db->prepare($queries->prop("responsable.find.bycargo"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_cargo", $idCargo, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows)&&count($rows)){
						$success = $this->unserialize($rows[0]);	
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id de responsable');
			}
			return $success;
		}

		public function deleteAsignacion(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.asignacion.delete"));
				$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
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



		public function insertAsignacion(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.asignacion.insert"));
				$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_responsable", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":folio", $this->departamento->folio, PDO::PARAM_INT);
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

		public function updateCargo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.cargo.update"));
				$stmt->bindParam(":fk_id_cat_cargo", $this->cargo->id, PDO::PARAM_INT);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
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

		public function resetCargo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.cargo.reset"));
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_cargo", $this->cargo->id, PDO::PARAM_INT);
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

		public function updateAsignacion(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->deleteAsignacion($db, $queries, $log)){
				$success = $this->insertAsignacion($db, $queries, $log);
			}
			return $success;
		}

		public function updateAsignacionOLD(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("responsable.asignacion.update"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
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

		public function updateDepartamentoAsignado(DBConnector $db, $idDepartamento, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop(""));
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			
		}

	}
?>