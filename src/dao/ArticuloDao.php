<?
	class ArticuloDao extends Articulo{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("articulo.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchall(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$success = $this->unserialize($rows[0]);
					}
				}catch(PDOException $e){
					$log->error('query: articulo.find');
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}
			return $success;
		}

		public function updateBase(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("articulo.base.update"));
					$stmt->bindParam(":fk_id_cat_rubro", $this->rubro->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_origen", $this->departamento->id, PDO::PARAM_INT);
					$stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
					$stmt->bindParam(":marca", $this->marca, PDO::PARAM_STR);
					$stmt->bindParam(":modelo", $this->modelo, PDO::PARAM_STR);
					$stmt->bindParam(":serie", $this->serie, PDO::PARAM_STR);
					$stmt->bindParam(":motor", $this->motor, PDO::PARAM_STR);
					$stmt->bindParam(":factura", $this->factura, PDO::PARAM_STR);
					$stmt->bindParam(":fecha_adquisicion", $this->fechaAdquisicion, PDO::PARAM_STR);
					$stmt->bindParam(":importe", $this->importe, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_depreciacion", $this->depreciacion->id, PDO::PARAM_INT);
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

		public function updateImagen(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!="" && $this->imagen!=""){
				try{
					$stmt = $db->prepare($queries->prop("articulo.imagen.update"));
					$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
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

		public function insertBase(DBConnector $db, Properties $queries, Logger $log){
			try{
				$stmt = $db->prepare($queries->prop("articulo.base.insert"));
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_rubro", $this->rubro->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_origen", $this->departamento->id, PDO::PARAM_INT);
				$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
				$stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
				$stmt->bindParam(":marca", $this->marca, PDO::PARAM_STR);
				$stmt->bindParam(":modelo", $this->modelo, PDO::PARAM_STR);
				$stmt->bindParam(":serie", $this->serie, PDO::PARAM_STR);
				$stmt->bindParam(":motor", $this->motor, PDO::PARAM_STR);
				$stmt->bindParam(":factura", $this->factura, PDO::PARAM_STR);
				$stmt->bindParam(":fecha_adquisicion", $this->fechaAdquisicion, PDO::PARAM_STR);
				$stmt->bindParam(":importe", $this->importe, PDO::PARAM_STR);
				$stmt->bindParam(":fk_id_cat_depreciacion", $this->depreciacion->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$log->debug('result: success');
					$this->id = $db->lastInsertId();
				}else{
					$log->error('result: fail');
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $this->id!=null&&$this->id!=''?true:false;
		}

		public function insert(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->insertBase($db, $queries, $log)){
				$success = $this->insertByPeriodo($db, $queries, $log);
			}
			return $success;
		}

		public function update(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->updateBase($db, $queries, $log)){
				$success = $this->updateByPeriodo($db, $queries, $log);
			}
			return $success;
		}

		public function insertByPeriodo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("articulo.periodo.insert"));
					$stmt->bindParam(":fk_id_articulo", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_estado_fisico", $this->estadoFisico->id, PDO::PARAM_INT);
					$stmt->bindParam(":valor_reposicion", $this->valorReposicion, PDO::PARAM_STR);
					$stmt->bindParam(":valor_reemplazo", $this->valorReemplazo, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_acumulada", $this->depreciacionAcumulada, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo", $this->depreciacionPeriodo, PDO::PARAM_STR);
					$stmt->bindParam(":anios_uso", $this->aniosUso, PDO::PARAM_STR);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('result: SUCCESS');
						$success = true;
					}else{
						$log->error('result: FAIL');
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
					
			}else{
				$log->error('Falta id de articulo');
			}
			return $success;
		}

		public function updateByPeriodo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!='' && $this->periodo->id!=null && $this->periodo->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("articulo.periodo.update"));
					$stmt->bindParam(":fk_id_articulo", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_estado_fisico", $this->estadoFisico->id, PDO::PARAM_INT);
					$stmt->bindParam(":valor_reposicion", $this->valorReposicion, PDO::PARAM_STR);
					$stmt->bindParam(":valor_reemplazo", $this->valorReemplazo, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_acumulada", $this->depreciacionAcumulada, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo", $this->depreciacionPeriodo, PDO::PARAM_STR);
					$stmt->bindParam(":anios_uso", $this->aniosUso, PDO::PARAM_STR);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('result: SUCCESS');
						$success = true;
					}else{
						$log->error('result: FAIL');
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;	
			}else{
				$log->error('Falta id de articulo');
			}
			return $success;
		}
	}
?>