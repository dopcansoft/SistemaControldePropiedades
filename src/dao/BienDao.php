<?
	class BienDao extends Bien{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!='' && $this->periodo->id!=null && $this->periodo->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.find"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$success = $this->unserialize($rows[0]);
						$this->valuaciones = $this->findValuaciones($db, $queries, $log);
						$this->images = $this->getImages($db, $queries, $log);
					}
				}catch(PDOException $e){
					$log->error('query: bien.find');
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id y/o id de periodo');
			}
			return $success;
		}

		public function findValuaciones(DBConnector $db, Properties $queries, Logger $log){
			$items = array();
			if(isset($this->id)!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.valuaciones.find"));
					$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						foreach($rows as $row){
							$items[] = new ItemValuacion($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta Id del bien');
			}
			return $items;
		}

		public function findByFolio(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->folio!=null&&$this->folio!=''){
				try{
					$stmt = $db->prepare($queries->prop("bien.find.folio"));
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$success = $this->unserialize($rows[0]);
						$this->images = $this->getImages($db, $queries, $log);
					}
				}catch(PDOException $e){
					$log->error('query: bien.find');
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta Folio');
			}
			return $success;
		}

		public function updateMin(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.updatemin"));
					$stmt->bindParam(":fk_id_cat_tipo_clasificacion_bien", $this->tipoClasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_clasificacion_bien", $this->clasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_depreciacion", $this->depreciacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$stmt->bindParam(":consecutivo", $this->consecutivo, PDO::PARAM_STR);
					$stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_tipo_valuacion", $this->tipoValuacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":valuacion", $this->valor, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_color", $this->color->id, PDO::PARAM_INT);
					$stmt->bindParam(":valorAnterior", $this->valorAnterior, PDO::PARAM_STR);
					
					$stmt->bindParam(":fk_id_cat_estado_fisico", $this->estadoFisico->id, PDO::PARAM_INT);
					$stmt->bindParam(":depreciacion_acumulada", $this->depreciacionAcumulada, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo", $this->depreciacionPeriodo, PDO::PARAM_STR);
					$stmt->bindParam(":valor_uma", $this->valorUma, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_uma", $this->uma->id, PDO::PARAM_INT);
					$stmt->bindParam(":inventario_contable", $this->inventarioContable, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_origen_fondo_adquisicion", $this->origen->id, PDO::PARAM_INT);
					$stmt->bindParam(":fecha_adquisicion", $this->fechaAdquisicion, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->bindParam(":fecha_cierre", $this->fechaCierre, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);					
					
					$log->debug('updatemin params');
					$log->debug("fk_id_cat_tipo_clasificacion_bien: ".$this->tipoClasificacion->id);
					$log->debug("fk_id_clasificacion_bien: ".$this->clasificacion->id);
					$log->debug("fk_id_departamento: ".$this->departamento->id);
					$log->debug("fk_id_cat_depreciacion: ".$this->depreciacion->id);
					$log->debug("folio: ".$this->folio);
					$log->debug("consecutivo: ".$this->consecutivo);
					$log->debug("descripcion: ".$this->descripcion);
					$log->debug("fk_id_cat_tipo_valuacion: ".$this->tipoValuacion->id);
					$log->debug("valuacion: ".$this->valor);
					$log->debug("valorAnterior: ".$this->valorAnterior);					
					$log->debug("fk_id_cat_estado_fisico: ".$this->estadoFisico->id);
					$log->debug("depreciacion_acumulada: ".$this->depreciacionAcumulada);
					$log->debug("depreciacion_periodo: ".$this->depreciacionPeriodo);
					$log->debug("valor_uma: ".$this->valorUma);
					$log->debug("fk_id_cat_uma: ".$this->uma->id);
					$log->debug("inventario_contable: ".$this->inventarioContable);
					$log->debug("fk_id_cat_origen_fondo_adquisicion: ".$this->origen->id);
					$log->debug("fecha_adquisicion: ".$this->fechaAdquisicion);
					$log->debug("fk_id_periodo: ".$this->periodo->id);
					$log->debug("fecha_cierre: ".$this->fechaCierre);
					$log->debug("id: ".$this->id);					
					
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Id de bien no especificado');
			}
			return $success;
		}

		public function updateBatchEstatusInv(PDOStatement $stmt, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=""){
				try{
					//$stmt = $db->prepare($queries->prop("bien.estatusinventario.update"));
					$stmt->bindParam(":fk_id_cat_estatus_inventario", $this->estatusInventario->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);					
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);					
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
			}else{
				$log->error('Id de bien no especificado');
			}
			return $success;
		}

		public function updateBase(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.update"));
					$stmt->bindParam(":fk_id_cat_tipo_clasificacion_bien", $this->tipoClasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_clasificacion_bien", $this->clasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_depreciacion", $this->depreciacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_origen_fondo_adquisicion", $this->origen->id, PDO::PARAM_INT);
					$stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
					$stmt->bindParam(":notas", $this->notas, PDO::PARAM_STR);
					$stmt->bindParam(":folio_anterior", $this->folioAnterior, PDO::PARAM_STR);					
					$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
					$stmt->bindParam(":marca", $this->marca, PDO::PARAM_STR);
					$stmt->bindParam(":modelo", $this->modelo, PDO::PARAM_STR);
					$stmt->bindParam(":serie", $this->serie, PDO::PARAM_STR);
					$stmt->bindParam(":motor", $this->motor, PDO::PARAM_STR);
					$stmt->bindParam(":factura", $this->factura, PDO::PARAM_STR);
					$stmt->bindParam(":fecha_adquisicion", $this->fechaAdquisicion, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_tipo_valuacion", $this->tipoValuacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":valuacion", $this->valor, PDO::PARAM_STR);
					//$stmt->bindParam(":numero", $this->numero, PDO::PARAM_INT);
					//$stmt->bindParam(":folio_unico", $this->folioUnico, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

					$log->debug("fk_id_cat_tipo_clasificacion_bien: ".$this->tipoClasificacion->id);
					$log->debug("fk_id_clasificacion_bien: ".$this->clasificacion->id);
					$log->debug("fk_id_departamento: ".$this->departamento->id);
					$log->debug("fk_id_cat_depreciacion: ".$this->depreciacion->id);
					$log->debug("fk_id_cat_origen_fondo_adquisicion: ".$this->origen->id);
					$log->debug("descripcion: ".$this->descripcion);
					$log->debug("marca: ".$this->marca);
					$log->debug("modelo: ".$this->modelo);
					$log->debug("serie: ".$this->serie);
					$log->debug("motor: ".$this->motor);
					$log->debug("factura: ".$this->factura);
					$log->debug("fecha_adquisicion: ".$this->fechaAdquisicion);
					$log->debug("fk_id_cat_tipo_valuacion: ".$this->tipoValuacion->id);
					$log->debug("valuacion: ".$this->valor);
					$log->debug("id: ".$this->id);

					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
					$stmt = null;	
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
			}
			return $success;
		}

		public function eliminarBien(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("bien.delete"));
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
				}catch(PDOException $e){
					$log->error('query: bien.delete');
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}
			return $success;
		}


		public function delete(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->eliminarBien($db, $queries, $log)){
				$success = $this->eliminarBien($db, $queries, $log);
			}
			return $success;
		}

		public function getUnico($idEmpresa, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($idEmpresa) && trim($idEmpresa)!=""){
				try{
					$stmt = $db->prepare($queries->prop("empresa.unico.find.max"));
					$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						$this->numero = $rows[0]["numero"];
						$this->folioUnico = $rows[0]["folioUnico"];
					}else{
						$log->error('No se obtuvieron resultados');
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id de empresa');
			}
			return trim($this->numero)!=""&&$this->numero>0&&trim($this->folioUnico)!=""?true:false;
		}

		public function eliminarBienPeriodo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("bien.periodo.delete"));
					$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$success = true;
					}
					$stmt = null;
				}catch(PDOException $e){
					$log->error('query: bien.periodo.delete');
					$log->error("PDOException: ".$e->getMessage());
				}
			}
			return $success;
		}


		public function updateImagen(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!="" && $this->imagen!=""){
				try{
					$stmt = $db->prepare($queries->prop("inmueble.imagen.update"));
					$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('SUCCESS');
						$success = true;
					}	
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
			}
			return $success;
		}

		public function updateArchivoFactura(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			$log->debug('updateArchivoFactura');
			if($this->id!=null&&$this->id!="" && $this->archivoFactura!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.archivofactura.update"));
					$log->debug($queries->prop("bien.archivofactura.update"));
					$stmt->bindParam(":archivo_factura", $this->archivoFactura, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$log->debug('archivo_factura: '.$this->archivoFactura);
					$log->debug('id: '.$this->id);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('SUCCESS');
						$success = true;
					}else{
						$log->debug('FAIL');
					}	
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
			}
			$log->debug($success?"SUCCESS":"FAIL");
			return $success;
		}

		public function updateArchivoPoliza(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			$log->debug('updateArchivoPoliza');
			if($this->id!=null&&$this->id!="" && $this->archivoPoliza!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.archivopoliza.update"));
					$stmt->bindParam(":archivo_poliza", $this->archivoPoliza, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('SUCCESS');
						$success = true;
					}else{
						$log->debug('FAIL');
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
			}
			$log->debug($success?"SUCCESS":"FAIL");
			return $success;
		}

		public function insertBase(DBConnector $db, Properties $queries, Logger $log){
			try{
				if($this->getUnico($this->empresa->id, $db, $queries, $log)){
					$stmt = $db->prepare($queries->prop("bien.insert"));
					$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_tipo_clasificacion_bien", $this->tipoClasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_clasificacion_bien", $this->clasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_departamento", $this->departamento->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_depreciacion", $this->depreciacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_origen_fondo_adquisicion", $this->origen->id, PDO::PARAM_INT);
					$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
					$stmt->bindParam(":archivo_poliza", $this->archivoPoliza, PDO::PARAM_STR);
					$stmt->bindParam(":archivo_factura", $this->archivoFactura, PDO::PARAM_STR);
					$stmt->bindParam(":descripcion", $this->descripcion, PDO::PARAM_STR);
					$stmt->bindParam(":folio_anterior", $this->folioAnterior, PDO::PARAM_STR);
					$stmt->bindParam(":notas", $this->notas, PDO::PARAM_STR);
					$stmt->bindParam(":marca", $this->marca, PDO::PARAM_STR);
					$stmt->bindParam(":modelo", $this->modelo, PDO::PARAM_STR);
					$stmt->bindParam(":serie", $this->serie, PDO::PARAM_STR);
					$stmt->bindParam(":motor", $this->motor, PDO::PARAM_STR);
					$stmt->bindParam(":factura", $this->factura, PDO::PARAM_STR);
					$stmt->bindParam(":fecha_adquisicion", $this->fechaAdquisicion, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_tipo_valuacion", $this->tipoValuacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":valuacion", $this->valor, PDO::PARAM_STR);
					$stmt->bindParam(":numero", $this->numero, PDO::PARAM_INT);
					$stmt->bindParam(":folio_unico", $this->folioUnico, PDO::PARAM_STR);

					$log->debug("fk_id_empresa: ".$this->empresa->id);
					$log->debug("fk_id_cat_tipo_clasificacion_bien: ".$this->tipoClasificacion->id);
					$log->debug("fk_id_clasificacion_bien: ".$this->clasificacion->id);
					$log->debug("fk_id_departamento: ".$this->departamento->id);
					$log->debug("fk_id_cat_depreciacion: ".$this->depreciacion->id);
					$log->debug("fk_id_cat_origen_fondo_adquisicion: ".$this->origen->id);
					$log->debug("imagen: ".$this->imagen);
					$log->debug("archivoFactura: ".$this->archivoFactura);
					$log->debug("archivoPoliza: ".$this->archivoPoliza);
					$log->debug("descripcion: ".$this->descripcion);
					$log->debug("marca: ".$this->marca);
					$log->debug("modelo: ".$this->modelo);
					$log->debug("serie: ".$this->serie);
					$log->debug("motor: ".$this->motor);
					$log->debug("factura: ".$this->factura);
					$log->debug("fecha_adquisicion: ".$this->fechaAdquisicion);
					$log->debug("fk_id_cat_tipo_valuacion: ".$this->tipoValuacion->id);
					$log->debug("valuacion: ".$this->valor);
					$log->debug("numero: ".$this->numero);
					$log->debug("folio_unico: ".$this->folioUnico);
					$result = $stmt->execute();
					if($result>0 ){
						$log->debug('result: success');
						$this->id = $db->lastInsertId();

					}else{
						$log->error('result: fail');
					}					
				}				
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $this->id!=null&&$this->id!=''?true:false;
		}

		public function insertByPeriodo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!=''){
				try{
					$stmt = $db->prepare($queries->prop("bien.periodo.insert"));
					$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$stmt->bindParam(":consecutivo", $this->consecutivo, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_estado_fisico", $this->estadoFisico->id, PDO::PARAM_INT);
					$stmt->bindParam(":depreciacion_acumulada", $this->depreciacionAcumulada, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo", $this->depreciacionPeriodo, PDO::PARAM_STR);
					//$stmt->bindParam(":depreciacion_acumulada_final", $this->depreciacionAcumuladaFinal, PDO::PARAM_STR);
					//$stmt->bindParam(":depreciacion_periodo_final", $this->depreciacionPeriodoFinal, PDO::PARAM_STR);
					//$stmt->bindParam(":fk_id_cat_depreciacion_final", $this->depreciacionFinal->id, PDO::PARAM_STR);
					$stmt->bindParam(":anios_uso", $this->aniosUso, PDO::PARAM_STR);
					$stmt->bindParam(":valor_uma", $this->valorUma, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_uma", $this->uma->id, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_bandera", $this->bandera->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_responsable", $this->responsable->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_color", $this->color->id, PDO::PARAM_INT);
					$stmt->bindParam(":valor_anterior", $this->valorAnterior, PDO::PARAM_STR);
					$stmt->bindParam(":matricula", $this->matricula, PDO::PARAM_STR);
					//$stmt->bindParam(":fk_id_cat_tipo_valuacion_final", $this->tipoValuacionFinal->id, PDO::PARAM_INT);
					//$stmt->bindParam(":valuacion_final", $this->valorFinal, PDO::PARAM_STR);
					$stmt->bindParam(":inventario_contable", $this->inventarioContable, PDO::PARAM_STR);
					
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

		public function updateByPeriodo(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if($this->id!=null&&$this->id!='' && $this->periodo->id!=null && $this->periodo->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("bien.periodo.update"));
					$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_periodo", $this->periodo->id, PDO::PARAM_INT);
					$stmt->bindParam(":folio", $this->folio, PDO::PARAM_STR);
					$stmt->bindParam(":consecutivo", $this->consecutivo, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_estado_fisico", $this->estadoFisico->id, PDO::PARAM_INT);
					$stmt->bindParam(":depreciacion_acumulada", $this->depreciacionAcumulada, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo", $this->depreciacionPeriodo, PDO::PARAM_STR);
					$stmt->bindParam(":anios_uso", $this->aniosUso, PDO::PARAM_STR);
					$stmt->bindParam(":valor_uma", $this->valorUma, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_uma", $this->uma->id, PDO::PARAM_INT);
					$stmt->bindParam(":inventario_contable", $this->inventarioContable, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_bandera", $this->bandera->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_responsable", $this->responsable->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_color", $this->color->id, PDO::PARAM_INT);
					$stmt->bindParam(":valor_anterior", $this->valorAnterior, PDO::PARAM_STR);
					$stmt->bindParam(":matricula", $this->matricula, PDO::PARAM_STR);
					$log->debug(':fk_id_responsable, valor:'.$this->responsable->id);
					/*$stmt->bindParam(":depreciacion_acumulada_final", $this->depreciacionAcumuladaFinal, PDO::PARAM_STR);
					$stmt->bindParam(":depreciacion_periodo_final", $this->depreciacionPeriodoFinal, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_depreciacion_final", $this->depreciacionFinal->id, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_tipo_valuacion_final", $this->tipoValuacionFinal->id, PDO::PARAM_INT);
					$stmt->bindParam(":valuacion_final", $this->valorFinal, PDO::PARAM_STR);*/

					/*$log->debug("fk_id_bien: ".$this->id);
					$log->debug("fk_id_periodo: ".$this->periodo->id);
					$log->debug("fk_id_cat_estado_fisico: ".$this->estadoFisico->id);
					$log->debug("depreciacion_acumulada: ".$this->depreciacionAcumulada);
					$log->debug("depreciacion_periodo: ".$this->depreciacionPeriodo);
					$log->debug("anios_uso: ".$this->aniosUso);
					$log->debug("valor_uma: ".$this->valorUma);
					$log->debug("fk_id_cat_uma: ".$this->uma->id);
					$log->debug("inventario_contable: ".$this->inventarioContable);*/

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

		public function deleteImgs($pathbase, DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($this->id)&&$this->id!=""){
				try{
					$fordelete = $this->getImages($db, $queries, $log);
					$stmt = $db->prepare($queries->prop("bien.imagenes.delete"));
					$stmt->bindParam(":fk_id_bien",$this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						foreach($fordelete as $item){
							$log->debug('eliminado: '.$pathbase.$item);
							if(@unlink($pathbase.$item)){
								$log->debug("success");		
							}
						}
						$log->debug('Se eliminaron imagenes anteriores');
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id del bien');
			}
			return $success;
		}

		public function getImages(DBConnector $db, Properties $queries, Logger $log){
			$images = array();
			try{
				$stmt = $db->prepare($queries->prop("bien.listimages"));
				$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
						$log->debug($row["orden"].": ".$row["path"]);
						$images[] = $row["path"];
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $images;	
		}

		public function insertImages(array $images, DBConnector $db, Properties $queries, Logger $log){
			$success = 0;
			$log->debug('antes de insertar');
			$log->debug($images);
			if(isset($this->id)&&$this->id!=""){
				if(count($images)>0){
					try{
						$stmt = $db->prepare($queries->prop("bien.imagenes.insert"));
						for($i=0;$i<count($images);$i++){
							$img = $images[$i];
							$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
							$stmt->bindParam(":path", $img, PDO::PARAM_STR);
							$stmt->bindParam(":orden", $i, PDO::PARAM_INT);
							$result = $stmt->execute();
							if($result>0){
								$log->debug('SUCCESS: '.$img);
								$success++;
							}else{
								$log->debug('FAIL: '.$img);
							}
						}
					}catch(PDOException $e){
						$log->error("PDOException: ".$e->getMessage());
					}
					$stmt = null;
				}else{
					$log->debug('el arreglo no contenia imagenes para insertar');
				}
			}else{
				$log->error('Falta id del bien');
			}
			return $success>=count($images)?true:false;
		}

		public function insertValuaciones(array $items, DBConnector $db, Properties $queries, Logger $log){
			$log->debug('inicia');
			$records = 0;
			if(isset($items) && count($items)>0){
				try{
					$stmt = $db->prepare($queries->prop("bien.valuacion.insert"));
					foreach($items as $item){
						$log->debug("fk_id_bien: ".$this->id);
						$log->debug("fk_id_periodo: ".$item->periodo->id);
						$log->debug("valor: ".$item->valor);
						$log->debug("fk_id_cat_tipo_valuacion: ".$item->tipo->id);
						$log->debug("fecha: ".$item->fecha);
						$log->debug("fecha_cierre: ".$item->fechaCierre);
						$log->debug("dep_acumulada: ".$item->depAcumulada);
						$log->debug("dep_periodo: ".$item->depPeriodo);
						$log->debug("anios_uso: ".$item->aniosUso);
						$log->debug("fk_id_cat_depreciacion: ".$item->depreciacion->id);
						$log->debug("fk_id_cat_uma: ".$item->uma->id);
						$log->debug("valor_libros: ".$item->valorLibros);
						$log->debug("valor_actual: ".$item->valorActual);
						$log->debug("fk_id_cat_estado_fisico: ".$item->estadoFisico->id);
						$log->debug("orden: ".$item->orden);
						
						$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
						$stmt->bindParam(":fk_id_periodo", $item->periodo->id, PDO::PARAM_INT);
						$stmt->bindParam(":valor", $item->valor, PDO::PARAM_STR);
						$stmt->bindParam(":fk_id_cat_tipo_valuacion", $item->tipo->id, PDO::PARAM_INT);
						$stmt->bindParam(":fecha", $item->fecha, PDO::PARAM_STR);
						$stmt->bindParam(":fecha_cierre", $item->fechaCierre, PDO::PARAM_STR);
						$stmt->bindParam(":dep_acumulada", $item->depAcumulada, PDO::PARAM_STR);
						$stmt->bindParam(":dep_periodo", $item->depPeriodo, PDO::PARAM_STR);
						$stmt->bindParam(":anios_uso", $item->aniosUso, PDO::PARAM_STR);
						$stmt->bindParam(":fk_id_cat_depreciacion", $item->depreciacion->id, PDO::PARAM_INT);
						$stmt->bindParam(":fk_id_cat_uma", $item->uma->id, PDO::PARAM_INT);
						$stmt->bindParam(":valor_libros", $item->valorLibros, PDO::PARAM_STR);
						$stmt->bindParam(":valor_actual", $item->valorActual, PDO::PARAM_STR);
						$stmt->bindParam(":fk_id_cat_estado_fisico", $item->estadoFisico->id, PDO::PARAM_INT);
						$stmt->bindParam(":orden", $item->orden, PDO::PARAM_INT);
						
						if($stmt->execute()>0){
							$records++;
						}
					}
					//$log->debug('Se almacenaron: '.$records." de ".count($items)." registro(s)");
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}	
			}else{
				$log->debug('No se encontraron valuaciones a insertar');
			}			
			$stmt = null;
			return count($items)==$records?true:false;			
		}

		/*public function updateValuaciones(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("bien.valuacion.update"));
				$stmt->bindParam(":fk_id_");
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}*/

		public function deleteValuaciones(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("bien.valuacion.delete"));
				$stmt->bindParam(":fk_id_bien", $this->id, PDO::PARAM_INT);
				//$stmt->bindParam(":fk_id_periodo", $this->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$success = true;
					$log->debug('Se eliminaron: '.$stmt->rowCount()." registro(s)");
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}
	}
?>