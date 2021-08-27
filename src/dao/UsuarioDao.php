<?
	class UsuarioDao extends Usuario{
		public function findById(DBConnector $db, Properties $queries, array $bind, Logger $log){
			if($this->id!=null && $this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("usuario.login"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						$this->unserialize($rows[0],null, null, $bind);
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}
			return $this->id!=null&&$this->id!=''?true:false; 
		}

		public function find(DBConnector $db, Properties $queries, Logger $log){
			if($this->id!=null && $this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("usuario.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)){
						$this->unserialize($rows[0]);
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("Falta id de usuario");
			}
			return $this->id!=null&&$this->id!=''?true:false; 
		}

		public function insert(DBConnector $db, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("usuario.insert"));
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}	
			$stmt = null;
		}

		public function delete(DBConnector $db, Properties $queries, Logger $log){}

		public function getPerfil($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("usuario.permiso.empresa"));
				$stmt->bindParam(":fk_id_usuario", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				if(isset($rows) && count($rows)>0){
					$this->perfil = new ItemCatalogo(array(
						"id"=>isset($rows[0]["perfil.id"])?$rows[0]["perfil.id"]:"",
						"descr"=>isset($rows[0]["perfil.descr"])?$rows[0]["perfil.descr"]:""
					));
				}
			}catch(PDOException $e){
				$log->error('PDOException: '.$e->getMessage());
			}
		}

		public function findByLogin(DBConnector $db, Properties $queries, array $bind, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("usuario.login"));
				$stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
				$stmt->bindParam(":password", $this->password, PDO::PARAM_STR);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					$this->unserialize($rows[0],null, null, $bind);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $this->id!=null&&$this->id!=''?true:false; 
		}
	}
?>