<?
	class Usuario extends Model{
		public $id;
		public $nombre;
		public $apellidos;
		public $email;
		public $password;
		public $avatar;
		public $tipo;
		public $perfil;
		public $estatus;
		public $fechaInsert;

		const PERFIL_ADMIN = 1;
		const PERFIL_CAPTURISTA = 2;
		const PERFIL_RESPONSABLE = 3;
		const PERFIL_VISITANTE = 4;

		const ESTATUS_ACTIVO = 1;
		const ESTATUS_SUSPENDIDO = 2;
		const ESTATUS_BAJA = 3;
		const ESTATUS_FIN_PRUEBA = 4;

		public function __construct($data, $prefix=null, $separator=".", $bind=null){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator, $bind);
			}else{
				$this->unserialize(array());
			}
		}	

		public function unserialize($data, $prefix=null, $separator=".", $bind=null){
			if(isset($data)){
				$data = $this->setter($data, $bind);
			}
			$this->id = $this->val("id", $data, $prefix, $separator);
			$this->nombre = $this->val("nombre", $data, $prefix, $separator);
			$this->apellidos = $this->val("apellidos", $data, $prefix, $separator);
			$this->email = $this->val("email", $data, $prefix, $separator);
			$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
			$this->password = $this->val("password", $data, $prefix, $separator);
			$this->avatar = $this->val("avatar", $data, $prefix, $separator);
			$this->tipo = $this->prop("tipo", $data, $prefix, $separator, "ItemCatalogo");
			$this->perfil = $this->prop("perfil", $data, $prefix, $separator, "ItemCatalogo");
			$this->estatus = $this->prop("estatus", $data, $prefix, $separator, "ItemCatalogo");
			return true;
		}
	}
?>