<?
	class BienInmuebleDao extends BienInmueble{
		public function find(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("inmueble.find"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows)&&count($rows)>0){
					$success = $this->unserialize($rows[0]);
					$this->images = $this->getImages($db, $queries, $log);
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		
		public function getLastConsecutivo($clasificacion, $empresa, DBConnector $db, Properties $queries, Logger $log){
			$consecutivo = "";
			try{
				$stmt = $db->prepare($queries->prop("inmueble.maxconsecutivo"));
				$stmt->bindParam(":fk_id_empresa", $empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_clasificacion_inmueble", $clasificacion->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($consecutivo)>0 ){
					$consecutivo = $rows[0]["consecutivo"];
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $consecutivo;
		}

		public function insert(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("inmueble.insert"));
				
				$stmt->bindParam(":folio",$this->folio, PDO::PARAM_STR);
				$stmt->bindParam(":consecutivo",$this->consecutivo, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa",$this->empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_tipo_inmueble",$this->tipo->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_clasificacion_inmueble",$this->clasificacion->id, PDO::PARAM_INT);
				$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
				
				$stmt->bindParam(":ubicacion", $this->ubicacion, PDO::PARAM_STR);
				$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
				$stmt->bindParam(":med_norte", $this->medNorte, PDO::PARAM_STR);
				$stmt->bindParam(":med_sur", $this->medSur, PDO::PARAM_STR);
				$stmt->bindParam(":med_este", $this->medEste, PDO::PARAM_STR);
				
				$stmt->bindParam(":med_oeste", $this->medOeste, PDO::PARAM_STR);
				$stmt->bindParam(":col_norte", $this->colNorte, PDO::PARAM_STR);
				$stmt->bindParam(":col_sur", $this->colSur, PDO::PARAM_STR);
				$stmt->bindParam(":col_este", $this->colEste, PDO::PARAM_STR);
				$stmt->bindParam(":col_oeste", $this->colOeste, PDO::PARAM_STR);
				
				$stmt->bindParam(":superficie_terreno", $this->superficieTerreno, PDO::PARAM_STR);
				$stmt->bindParam(":superficie_construccion", $this->superficieConstruccion, PDO::PARAM_STR);
				$stmt->bindParam(":fk_id_cat_uso_inmueble", $this->uso->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_aprovechamiento", $this->aprovechamiento->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_tipo_adquisicion", $this->modoAdquisicion->id, PDO::PARAM_INT);
				
				$stmt->bindParam(":serv_agua", $this->servAgua, PDO::PARAM_INT);
				$stmt->bindParam(":serv_drenaje", $this->servDrenaje, PDO::PARAM_INT);
				$stmt->bindParam(":serv_luz", $this->servLuz, PDO::PARAM_INT);
				$stmt->bindParam(":serv_telefonia", $this->servTelefonia, PDO::PARAM_INT);
				$stmt->bindParam(":serv_internet", $this->servInternet, PDO::PARAM_INT);
				
				$stmt->bindParam(":serv_gas_estacionario", $this->servGasEstacionario, PDO::PARAM_INT);
				$stmt->bindParam(":escritura_convenio", $this->numeroEscrituraConvenio, PDO::PARAM_STR);
				$stmt->bindParam(":num_registro_propiedad", $this->numRegistroPropiedad, PDO::PARAM_STR);
				$stmt->bindParam(":cuenta_catastral", $this->cuentaCatastral, PDO::PARAM_STR);
				$stmt->bindParam(":gravamen_pendiente", $this->gravamenPendiente, PDO::PARAM_STR);
				
				$stmt->bindParam(":fecha_avaluo", $this->fechaUltimoAvaluo, PDO::PARAM_STR);
				$stmt->bindParam(":valor_terreno", $this->valorTerreno, PDO::PARAM_STR);
				$stmt->bindParam(":valor_construccion", $this->valorConstruccion, PDO::PARAM_STR);
				$stmt->bindParam(":valor_capitalizable", $this->valorCapitalizable, PDO::PARAM_STR);

				$stmt->bindParam(":valor", $this->valor, PDO::PARAM_STR);
				$stmt->bindParam(":observaciones", $this->observaciones, PDO::PARAM_STR);


				
				$result = $stmt->execute();
				if($result>0){
					$this->id = $db->lastInsertId();
					if(isset($this->id) && $this->id!=""){
						$success = true;	
					} 
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $success;
		}

		public function update(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($this->id) && $this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("inmueble.update"));
					$stmt->bindParam(":folio",$this->folio, PDO::PARAM_STR);
					$stmt->bindParam(":consecutivo",$this->consecutivo, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_tipo_inmueble",$this->tipo->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_clasificacion_inmueble",$this->clasificacion->id, PDO::PARAM_INT);
					$stmt->bindParam(":descr", $this->descr, PDO::PARAM_STR);
					$stmt->bindParam(":ubicacion", $this->ubicacion, PDO::PARAM_STR);
					$stmt->bindParam(":imagen", $this->imagen, PDO::PARAM_STR);
					$stmt->bindParam(":med_norte", $this->medNorte, PDO::PARAM_STR);
					$stmt->bindParam(":med_sur", $this->medSur, PDO::PARAM_STR);
					$stmt->bindParam(":med_este", $this->medEste, PDO::PARAM_STR);
					$stmt->bindParam(":med_oeste", $this->medOeste, PDO::PARAM_STR);
					$stmt->bindParam(":col_norte", $this->colNorte, PDO::PARAM_STR);
					$stmt->bindParam(":col_sur", $this->colSur, PDO::PARAM_STR);
					$stmt->bindParam(":col_este", $this->colEste, PDO::PARAM_STR);
					$stmt->bindParam(":col_oeste", $this->colOeste, PDO::PARAM_STR);
					$stmt->bindParam(":superficie_terreno", $this->superficieTerreno, PDO::PARAM_STR);
					$stmt->bindParam(":superficie_construccion", $this->superficieConstruccion, PDO::PARAM_STR);
					$stmt->bindParam(":fk_id_cat_uso_inmueble", $this->uso->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_aprovechamiento", $this->aprovechamiento->id, PDO::PARAM_INT);
					$stmt->bindParam(":fk_id_cat_tipo_adquisicion", $this->modoAdquisicion->id, PDO::PARAM_INT);
					$stmt->bindParam(":serv_agua", $this->servAgua, PDO::PARAM_INT);
					$stmt->bindParam(":serv_drenaje", $this->servDrenaje, PDO::PARAM_INT);
					$stmt->bindParam(":serv_luz", $this->servLuz, PDO::PARAM_INT);
					$stmt->bindParam(":serv_telefonia", $this->servTelefonia, PDO::PARAM_INT);
					$stmt->bindParam(":serv_internet", $this->servInternet, PDO::PARAM_INT);
					$stmt->bindParam(":serv_gas_estacionario", $this->servGasEstacionario, PDO::PARAM_INT);
					
					$stmt->bindParam(":escritura_convenio", $this->numeroEscrituraConvenio, PDO::PARAM_STR);
					$stmt->bindParam(":num_registro_propiedad", $this->numRegistroPropiedad, PDO::PARAM_STR);
					$stmt->bindParam(":cuenta_catastral", $this->cuentaCatastral, PDO::PARAM_STR);
					$stmt->bindParam(":gravamen_pendiente", $this->gravamenPendiente, PDO::PARAM_STR);
					$stmt->bindParam(":fecha_avaluo", $this->fechaUltimoAvaluo, PDO::PARAM_STR);
					$stmt->bindParam(":valor_terreno", $this->valorTerreno, PDO::PARAM_STR);
					$stmt->bindParam(":valor_construccion", $this->valorConstruccion, PDO::PARAM_STR);
					$stmt->bindParam(":valor", $this->valor, PDO::PARAM_STR);
					$stmt->bindParam(":valor_capitalizable", $this->valorCapitalizable, PDO::PARAM_STR);
					$stmt->bindParam(":observaciones", $this->observaciones, PDO::PARAM_STR);
					$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);				
					
					$log->debug('params update');
					$log->debug("folio: ".$this->folio);
					$log->debug("fk_id_cat_tipo_inmueble: ".$this->tipo->id);
					$log->debug("fk_id_clasificacion_inmueble: ".$this->clasificacion->id);
					$log->debug("descr: ".$this->descr);
					$log->debug("ubicacion: ".$this->ubicacion);
					$log->debug("med_norte: ".$this->medNorte);
					$log->debug("med_sur: ".$this->medSur);
					$log->debug("med_este: ".$this->medEste);
					$log->debug("med_oeste: ".$this->medOeste);
					$log->debug("col_norte: ".$this->colNorte);
					$log->debug("col_sur: ".$this->colSur);
					$log->debug("col_este: ".$this->colEste);
					$log->debug("col_oeste: ".$this->colOeste);
					$log->debug("superficie_terreno: ".$this->superficieTerreno);
					$log->debug("superficie_construccion: ".$this->superficieConstruccion);
					$log->debug("fk_id_cat_uso_inmueble: ".$this->uso->id);
					$log->debug("fk_id_cat_aprovechamiento: ".$this->aprovechamiento->id);
					$log->debug("fk_id_cat_tipo_adquisicion: ".$this->modoAdquisicion->id);
					$log->debug(":serv_agua: ".$this->servAgua);
					$log->debug(":serv_drenaje: ".$this->servDrenaje);
					$log->debug(":serv_luz: ".$this->servLuz);
					$log->debug(":serv_telefonia: ".$this->servTelefonia);
					$log->debug(":serv_internet: ".$this->servInternet);
					$log->debug(":serv_gas_estacionario: ".$this->servGasEstacionario);
					
					$log->debug("escritura_convenio: ".$this->numeroEscrituraConvenio);
					$log->debug("num_registro_propiedad: ".$this->numRegistroPropiedad);
					$log->debug("cuenta_catastral: ".$this->cuentaCatastral);
					$log->debug("gravamen_pendiente: ".$this->gravamenPendiente);
					$log->debug("fecha_avaluo: ".$this->fechaUltimoAvaluo);
					$log->debug("valor: ".$this->valor);
					$log->debug("valorCapitalizable: ".$this->valorCapitalizable);
					
					$log->debug("observaciones: ".$this->observaciones);
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
				$log->error("Falta id del inmueble para poder actualizar");
			}
			return $success;
		}

		public function delete(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			try{
				$stmt = $db->prepare($queries->prop("inmueble.delete"));
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_empresa", $this->empresa->id, PDO::PARAM_INT);				
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

		public function deleteImgs(DBConnector $db, Properties $queries, Logger $log){
			$success = false;
			if(isset($this->id)&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("inmueble.imagenes.delete"));
					$stmt->bindParam(":fk_id_bien_inmueble",$this->id, PDO::PARAM_INT);
					$result = $stmt->execute();
					if($result>0){
						$log->debug('Se eliminaron imagenes anteriores');
						$success = true;
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error('Falta id del inmueble');
			}
			return $success;
		}

		public function getImages(DBConnector $db, Properties $queries, Logger $log){
			$images = array();
			try{
				$stmt = $db->prepare($queries->prop("inmueble.listimages"));
				$stmt->bindParam(":fk_id_bien_inmueble", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
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
			if(isset($this->id)&&$this->id!=""){
				try{
					$stmt = $db->prepare($queries->prop("inmueble.imagenes.insert"));
					foreach($images as $img){
						$stmt->bindParam(":fk_id_bien_inmueble", $this->id, PDO::PARAM_INT);
						$stmt->bindParam(":path", $img, PDO::PARAM_STR);
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
				$log->error('Falta id del bien');
			}
			return $success>=count($images)?true:false;
		}
	}
?>