<?
	class ItemConciliacion extends Model{
		public $id;
		public $idConciliacion;
		public $bien;
		public $fecha;
		public $hora;

		public function __construct($data, $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$success = false;
			if(isset($data)){
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->idConciliacion = $this->val("idConciliacion", $data, $prefix, $separator);
				$this->bien = $this->prop("bien", $data, $prefix, $separator, "Bien");
				$this->fecha = $this->val("fecha", $data, $prefix, $separator);
				$this->hora = $this->val("hora", $data, $prefix, $separator);
				$success = true;
			}
			return $success;
		}
	}
?>