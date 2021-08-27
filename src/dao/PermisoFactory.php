<?
	class PermisoFactory{		
		public function listByUser($idUsuario, DBConnector $db, Properties $queries, Logger $log){
			$list = array();
			if(isset($idUsuario)&&trim($idUsuario)!=""){
				try{
					$stmt = $db->prepare($queries->prop("usuario.list.permisos"));	
					$stmt->bindParam(":fk_id_usuario", $idUsuario, PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(isset($rows) && count($rows)>0){
						foreach($rows as $row){
							$list[] = new Permiso($row);
						}
					}
				}catch(PDOException $e){
					$log->error("PDOException: ".$e->getMessage());
				}
				$stmt = null;
			}else{
				$log->error("Falta id de usuario");
			}
			return $list;
		}
	}
?>