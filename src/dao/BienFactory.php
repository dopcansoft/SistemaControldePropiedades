<?
	class BienFactory extends Factory{
		const TIPO_INVENTARIO_CONTABLE = "CONTABLE";
		const TIPO_INVENTARIO_UTILITARIO = "UTILITARIO";
		public function getAll($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Properties $settings, Logger $log){
			$list = array();
			$log->debug('inicia...');
			$db->exec("set names utf8");
			$result = $this->exec($queries->prop("inventario.list"),array(
				array(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT),
				array(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT),
			), $db, $log);
			$list = $this->bind("BienDao", $result, null, $log);
			$log->debug('termina...');
			return $list;
		}

		public function groupByClasificacion(array $clasificaciones,array $list){
			$result = array();
			if(isset($list) && count($list)>0){
				foreach($clasificaciones as $clasificacion){
					$temp = array();
					$total = 0;
					$totalDepreciado = 0;
					$anterior = 0;
					foreach($list as $item){
						if($clasificacion->id==$item->clasificacion->id){
							$temp[] = $item;
							$total+=(isset($item->valorCapitalizable)&&$item->valorCapitalizable>0?$item->valorCapitalizable:$item->valor);
							$totalDepreciado+=((isset($item->valorCapitalizable)&&$item->valorCapitalizable>0?$item->valorCapitalizable:$item->valor)-($item->depreciacionAcumulada!=""?$item->depreciacionAcumulada:0));
							$anterior = isset($item->valorAnterior)&&$item->valorAnterior>0?$item->valorAnterior:0;
						}
					}
					$result[] = array(
						"clasificacion"=>$clasificacion,
						"bienes"=>$temp,
						"total"=>$total,
						"totalDepreciado"=>$totalDepreciado,
						"anterior"=>$anterior
					);
				}
			}
			return $result;
		}

		//public $elements = array();

		public function jerarquiaCC(array $bienes, array $clasificaciones, array $headers, Logger $log){
			$result = array();
			if(isset($headers)&&count($headers)>0){
				foreach($headers as $head){
					$items = array();
					$stotal = 0;
					$scosto = 0;
					$vanterior = 0;
					foreach($clasificaciones as $cla){
						if($head->cuentaContable == substr($cla->cuentaContable, 0, 7)){
							$elements = array();
							$total = 0;
							$costo = 0;
							$anterior = 0;
							foreach($bienes as $bien){
								//$log->debug($bien->cuentaContable.' = '.$cla->cuentaContable);
								if(trim($bien->cuentaContable) == trim($cla->cuentaContable)){
									$elements[] = $bien;
									$total=$total+($bien->depreciacionAcumulada!=""?$bien->depreciacionAcumulada:0);
									$costo = $costo + $bien->valor;
									if(!is_a($cla,'ClasificacionInmueble')){
										$anterior = $anterior + $bien->valorAnterior;
									}
								}
							}
							if(count($elements)>0){
								$items[] = array(
									"clasificacion"=>$cla,
									"list"=>$elements,
									"total"=>$total,
									"costo"=>$costo,
									"anterior"=>$anterior
								);
								//$log->debug("cla: ".$cla->cuentaContable.", total: ".$total);
								$stotal = $stotal+$total;
								$scosto = $scosto+$costo;
								$vanterior = $vanterior+$anterior;
							}							
						}
					}					
					$result[] = array(
						"header"=>$head,
						"content"=>$items,
						"total"=>$stotal,
						"costo"=>$scosto,
						"anterior"=>$vanterior
					);	
				}
			}
			return $result;
		}


		public function jerarquiaCCBI(array $bienes, array $clasificaciones, array $headers, Logger $log){
			$result = array();
			if(isset($headers)&&count($headers)>0){
				foreach($headers as $head){
					$items = array();
					$stotal = 0;
					
					foreach($clasificaciones as $cla){
						if($head->cuentaContable == substr($cla->cuentaContable, 0, 7)){
							$elements = array();
							$total = 0;
							foreach($bienes as $bien){
								$log->debug($bien->cuentaContable.' = '.$cla->cuentaContable);
								if(trim($bien->cuentaContable) == trim($cla->cuentaContable)){
									$elements[] = $bien;
									//$total=$total+($bien->depreciacionAcumulada!=""?$bien->depreciacionAcumulada:0);
									$total = $total+(isset($bien->valorCapitalizable)&&$bien->valorCapitalizable>0?$bien->valorCapitalizable:$bien->valor);
									//$costo = $costo + $bien->valor;
									/*if(!is_a($cla,'ClasificacionInmueble')){
										$anterior = $anterior + $bien->valorAnterior;
									}*/
								}
							}
							if(count($elements)>0){
								$items[] = array(
									"clasificacion"=>$cla,
									"list"=>$elements,
									"total"=>$total
								);
								$log->debug("cla: ".$cla->cuentaContable.", total: ".$total);
								$stotal = $stotal+$total;								
							}							
						}
					}					
					$result[] = array(
						"header"=>$head,
						"content"=>$items,
						"total"=>$stotal
					);	
				}
			}
			return $result;
		}

		//Cuenta Contable
		public function groupByCC(array $ccs,array $list){
			$result = array();
			if(isset($list) && count($list)>0){
				foreach($cs as $cc){
					$temp = array();
					$total = 0;
					$totalDepreciado = 0;
					foreach($list as $item){
						if($cc==$item->cuentaContable){
							$temp[] = $item;
							$total+=$item->valor;
							$totalDepreciado+=($item->valor-($item->depreciacionAcumulada!=""?$item->depreciacionAcumulada:0));  
						}
					}
					$result[] = array(
						"cuentaContable"=>$clasificacion,
						"bienes"=>$temp,
						"total"=>$total,
						"totalDepreciado"=>$totalDepreciado
					);
				}
			}
			return $result;
		}

		public function filtrado($idEmpresa, $idPeriodo, $tipoInventario, $fechaInicio, $fechaFin, $clasificacionBi, $clasificacionBm, $departamento, $edoFisico, $estatusInventario, DBConnector $db, Properties $queries, Properties $settings, Logger $log, $tipoFecha="FECHA_INSERT", $bandera=null){
			$list = array();
			$log->debug(array(
				array(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT),
				array(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT),
				array(":tipo_inventario", $tipoInventario, PDO::PARAM_STR),
				array(":fecha_inicio", $fechaInicio, PDO::PARAM_STR),
				array(":fecha_fin", $fechaFin, PDO::PARAM_STR),
				array(":clasificacion_bi", $clasificacionBi, PDO::PARAM_INT),
				array(":clasificacion_bm", $clasificacionBm, PDO::PARAM_INT),
				array(":fk_id_departamento", $departamento, PDO::PARAM_INT),
				array(":fk_id_cat_estado_fisico", $edoFisico, PDO::PARAM_INT),
				array(":fk_id_cat_estatus_inventario", $estatusInventario, PDO::PARAM_INT),
				array(":tipo_fecha", $tipoFecha, PDO::PARAM_STR),
				array(":bandera", $bandera, PDO::PARAM_STR)
			));
			$result = $this->exec($queries->prop("inventario.filtrado"),array(
				array(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT),
				array(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT),
				array(":tipo_inventario", $tipoInventario, PDO::PARAM_STR),
				array(":fecha_inicio", $fechaInicio, PDO::PARAM_STR),
				array(":fecha_fin", $fechaFin, PDO::PARAM_STR),
				array(":clasificacion_bi", $clasificacionBi, PDO::PARAM_INT),
				array(":clasificacion_bm", $clasificacionBm, PDO::PARAM_INT),
				array(":fk_id_departamento", $departamento, PDO::PARAM_INT),
				array(":fk_id_cat_estado_fisico", $edoFisico, PDO::PARAM_INT),
				array(":fk_id_cat_estatus_inventario", $estatusInventario, PDO::PARAM_INT),
				array(":tipo_fecha", $tipoFecha, PDO::PARAM_STR),
				array(":bandera", $bandera, PDO::PARAM_INT)
			), $db, $log);
			$list = $this->bind("BienDao", $result, null, $log);
			return $list;
		}

		public function copy(Bien $bien, DBConnector $db, Properties $queries, Logger $log){
			$id = null;
			try{
				$stmt = $db->prepare($queries->prop("bien.copy"));
				$stmt->bindParam(":id", $bien->id, PDO::PARAM_INT);
				$result = $stmt->execute();
				if($result>0){
					$id = $db->lastInsertId();
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $id;
		}
		
		public function duplicar(Bien $bien, DBConnector $db, Properties $queries, Logger $log){
			$duplicated = null;
			$key = $this->copy($bien, $db, $queries, $log);
			if(isset($key)){
				$duplicated = new BienDao(array("id"=>$key));
				$duplicated->find($db, $queries, $log);
			}
			return $duplicated;
		}

		public function getIncrement($empresa, $periodo, $clasificacion, $depto, DBConnector $db, Properties $queries, Logger $log){
			$counter = 0;
			try{
				$stmt = $db->prepare($queries->prop("bien.ultimoxdepto"));
				$stmt->bindParam(":fk_id_empresa", $empresa, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_periodo", $periodo, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_clasificacion_bien", $clasificacion, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_departamento", $depto, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$log->debug('ultimo: '.$rows[0]["ultimo"]);
				if(isset($rows) && count($rows)>0){
					$counter = isset($rows[0]["ultimo"])?intval($rows[0]["ultimo"]):0;
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $counter+1;		
		}
	}
?>