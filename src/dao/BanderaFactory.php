<?
	class BanderaFactory{
		public function filtrado(Empresa $empresa, ClasificacionInmueble $tipo, ClasificacionInmueble $clasificacion, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("inmueble.filtrado"));
				$stmt->bindParam(":fk_id_empresa", $empresa->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_cat_tipo_inmueble", $tipo->id, PDO::PARAM_INT);
				$stmt->bindParam(":fk_id_clasificacion_inmueble", $clasificacion->id, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
						$list[] = new BienInmuebleDao($row);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $list;
		}
	

   
   public function allWithDetails($idEmpresa, $idPeriodo, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("bandera.mostrar"));
				//$stmt->bindParam(":fk_id_empresa", $idEmpresa, PDO::PARAM_INT);
				//$stmt->bindParam(":fk_id_periodo", $idPeriodo, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows) && count($rows)>0){
					foreach($rows as $row){
						$list[] = new BanderaDao($row);
					}
				}
			}catch(PDOException $e){
				$log->error("PDOException: ".$e->getMessage());
			}
			$stmt = null;
			return $list;
		}
	}
?>