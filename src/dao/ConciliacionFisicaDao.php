<?
	class ConciliacionFisicaDao extends ConciliacionFisica{
		public function insert(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("conciliacion.insert"));
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_usuario", $this->usuario->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result > 0){
					$this->id = $db->lastInsertId();
					$success = isset($this->id)&&$this->id!=""?true:false;
				}else{
					$log->error("No guardo");	
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function insertDetalle(ItemConciliacion $item, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("conciliacion.detalle.insert"));		
				$stmt->bindParam(":fk_id_conciliacion_fisica", $item->idConciliacion, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_bien", $item->bien->id, PDO::PARAM_INT);
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

		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("conciliacion.find"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows)&& count($rows)>0){
					$this->items = $this->getItems($db, $queries, $log);
					$success = $this->unserialize($rows[0]);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function getItems(DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if(isset($this->id) && $this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("conciliacion.list"));		
					$stmt->bindParam(":fk_id_conciliacion_fisica", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						foreach($rows as $row){
							$list[] = new ItemConciliacion($row);
							$log->debug($row);
						}	
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;					
			}else{
				$log->error("Falta id de conciliacion");
			}
			return $list;			
		}
	}
?>