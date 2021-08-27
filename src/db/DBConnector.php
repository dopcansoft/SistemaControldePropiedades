<?
	class DBConnector extends PDO{
		public $url;
		public $schema;
		public $user;
		public $password;

		function setUrl($url){
			$this->url=$url;
		}

		function getUrl(){
			return $this->url;
		}

		function setSchema($schema){
			$this->schema=$schema;
		}

		function getSchema(){
			return $this->schema;
		}

		function setUser($user){
			$this->user=$user;
		}

		function getUser(){
			return $this->user;
		}

		function setPassword($password){
			$this->password=$password;
		}

		function getPassword(){
			return $this->password;
		}

		function __construct(Properties &$config){
			try{
				$this->setUrl($config->getProp("db.url")!=null?$config->getProp("db.url"):"");
				$this->setSchema($config->getProp("db.schema")!=null?$config->getProp("db.schema"):"");
				$this->setUser($config->getProp("db.user")!=null?$config->getProp("db.user"):"");
				$this->setPassword($config->getProp("db.password")!=null?$config->getProp("db.password"):"");
				parent::__construct('mysql:host='.$this->getUrl().';dbname='.$this->getSchema().';charset=utf8', $this->getUser(), $this->getPassword(), array(
  					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			}catch(PDOException $e){
				error_log(get_class($this).'-'.__LINE__.': '.$e->getMessage()."\n", 0);
			}catch(Error $e){
				error_log(get_class($this).'-'.__LINE__.': '.$e->getMessage()."\n", 0);
			}catch(Exception $e){
				error_log(get_class($this).'-'.__LINE__.': '.$e->getMessage()."\n", 0);
			}
		}
	}
?>