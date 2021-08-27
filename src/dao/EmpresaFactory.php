<?
	class EmpresaFactory extends Factory{
		public function listAll(DBConnector $db, Properties $queries, Properties $settings, Logger $log){
			$list = array();
			$result = $this->exec($queries->prop("empresa.list"),null, $db, $log);
			$list = $this->bind("EmpresaDao", $result, array("logo"=>array("prefix"=>$settings->prop("system.url"), "subfix"=>"", "alternative"=>$settings->prop("system.url").$settings->prop("usuario.noavatar"))), $log);
			return $list;
		}

		public function listByUser($idUsuario, DBConnector $db, Properties $queries, Properties $settings, Logger $log){
			$list = array();
			$result = $this->exec($queries->prop("usuario.empresas.list"),
				array(
					array(":fk_id_usuario", $idUsuario, PDO::PARAM_INT)
				)
				, $db, $log);
			$list = $this->bind("EmpresaDao", $result, array("logo"=>array("prefix"=>$settings->prop("system.url"), "subfix"=>"", "alternative"=>$settings->prop("system.url").$settings->prop("usuario.noavatar"))), $log);
			return $list;

		}
	}
?>