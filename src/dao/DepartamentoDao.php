<?
	class DepartamentoDao extends Departamento{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				if($this->id!=null&&$this->id!=""){
					$log->debug('departamento.find');
					$stmt = $db->prepare($queries->prop("departamento.find"));
					$log->debug('departamento: '.$this->id);
					$log->debug('empresa: '.$this->idEmpresa);
					$log->debug('periodo: '.$this->idPeriodo);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->idPeriodo, PDO::PARAM_INT);
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

		public function getMaxFolio(DBConnector $db, Properties $queries, Logger $log){
			$max = "";
			try{
				$stmt = $db->prepare($queries->prop("departamento.getmaxfolio"));
				$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					$max = $rows[0]["maxfolio"];
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $max;
		}

		public function update(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("departamento.update"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
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

		public function insert(DBconnector $db, Properties $queries, Logger $log){
			$success = false;
			$this->id = "";
			try{
				$stmt = $db->prepare($queries->prop("departamento.insert"));
				$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
				$stmt->bindParam(":clave", $this->folio, PDO::PARAM_STR);
				$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$this->id = $db->lastInsertId();
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
			try{
				if($this->id!=null&&$this->id!=""){
					$log->debug('departamento.asignacion.update');
					$stmt = $db->prepare($queries->prop("departamento.asignacion.update"));
					$stmt->bindParam(":fk_id_responsable", $this->idResponsable, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_departamento", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->idPeriodo, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
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

		/*public function getAsignacion(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				if($this->id!=null&&$this->id!=""){
					$log->debug('departamento.asignacion.insert');
					$stmt = $db->prepare($queries->prop("departamento.asignacion.insert"));
					$stmt->bindParam(":fk_id_departamento", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->idPeriodo, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_responsable", $this->idResponsable, PDO::PARAM_INT);
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}else{
					$log->error('Falta id de departamento');
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}*/

		public function asignar(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				if($this->id!=null&&$this->id!=""){
					$log->debug('departamento.asignacion.insert');
					$stmt = $db->prepare($queries->prop("departamento.asignacion.insert"));
					$stmt->bindParam(":fk_id_departamento", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_empresa", $this->idEmpresa, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->idPeriodo, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_responsable", $this->idResponsable, PDO::PARAM_INT);
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
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