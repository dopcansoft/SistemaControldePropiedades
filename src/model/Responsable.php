<?
	class Responsable extends Model{
		public $id;
		public $titulo;
		public $nombre;
		public $apellido;
		public $email;
		public $cargo;
		public $departamento;
		public $empresa;
		public $periodo;

		const CARGO_PRESIDENTE_MUNICIPAL = 2;
		const CARGO_TESORERO = 3;
		const CARGO_REGIDOR = 4;
		const CARGO_SINDICO = 5;
		const CARGO_DIR_JURIDICO = 6;
		const CARGO_ORG_CTRL_INT = 7;
		const CARGO_JEFE_ADQUISICIONES = 8;
		
		public function __construct($data=null, $prefix=null, $separator='.'){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator='.'){
			$unserialized = false;
			try{
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->titulo = $this->val("titulo", $data, $prefix, $separator);
				$this->nombre = $this->val("nombre", $data, $prefix, $separator);
				$this->apellido = $this->val("apellido", $data, $prefix, $separator);
				$this->email = $this->val("email", $data, $prefix, $separator);
				$this->cargo = $this->prop("cargo", $data, $prefix, $separator, "ItemCatalogo");
				$this->departamento = $this->prop("departamento", $data, $prefix, $separator, "Departamento");
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "ItemCatalogo");
				$this->periodo = $this->prop("periodo", $data, $prefix, $separator, "ItemCatalogo");
				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}				
	}
?>