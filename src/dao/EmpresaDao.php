<?
	class EmpresaDao extends Empresa{
		public function insertBasic(DBConnector $db, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("empresa.insert.short"));
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
				//$stmt->bindParam(":direccion", $this->direccion, PDO::PARAM_STR);
				//$stmt->bindParam(":logo", $this->logo, PDO::PARAM_STR);
				$result = $stmt->execute();
				if($result>0){
					$log->debug('result: success');
					$this->id = $db->lastInsertId();
				}else{
					$log->error('result: fail');
				}
			}catch(PDOException $e){
				$log->error('query: empresa.insert');
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $this->id!=null&&$this->id!=''?true:false;
		}

		public function insertUsuarioByEmpresa($idUsuario, $rolUsuario, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($this->id) && $this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("empresa.insertusuario"));
					$stmt->bindParam("fk_id_empresa", $this->id, PDO::PARAM_INT);
					$stmt->bindParam("fk_id_usuario", $idUsuario, PDO::PARAM_INT);
					$stmt->bindParam("fk_id_cat_rol_usuario", $rolUsuario, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}
			return $success;
		}

		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&& $this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("empresa.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
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

		public function delete(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null && $this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("empresa.delete"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}finally{
					$stmt = null;
				}
			}
			return $success;
		}

		public function update(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("empresa.update"));
					$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
					$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
					$stmt->bindParam(":direccion", $this->direccion, PDO::PARAM_STR);
					$stmt->bindParam(":logo", $this->logo, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PADO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('result: success');
						$success = true;
					}else{
						$log->error('result: fail');
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id');
			}			
			return $success;
		}
	}
?>