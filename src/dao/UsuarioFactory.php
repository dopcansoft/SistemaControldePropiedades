<?
	class UsuarioFactory{
		public function listAll(DBconnector $db, Properties $queries, Logger $log){
			$list = array();
			try{
				$stmt = $db->prepare($queries->prop("usuarios.list"));
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(isset($rows)&&count($rows)>0){
					foreach($rows as $row){
						$list[] = new UsuarioDao($row);
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