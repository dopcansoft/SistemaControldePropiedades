<?
	class ItemValuacion extends Model{
		public $id;
		public $periodo;
		public $valor;
		public $orden;
		public $tipo;
		public $fecha;
		public $fechaCierre;
		public $depAcumulada;
		public $depPeriodo;
		public $aniosUso;
		public $depreciacion;
		public $uma;
		public $valorLibros;
		public $valorActual;
		public $estadoFisico;
		public $contable;
		public $fechaInsert;


		public function __construct($data, $prefix=null, $separator=".", $bind=null){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator, $bind);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator='.'){
			$this->id = $this->val("id", $data, $prefix, $separator);
			$this->periodo = $this->prop("periodo", $data, $prefix, $separator, 'ItemCatalogo');
			$this->valor = $this->val("valor", $data, $prefix, $separator);
			$this->orden = $this->val("orden", $data, $prefix, $separator);
			$this->tipo = $this->prop("tipo", $data, $prefix, $separator, 'ItemCatalogo');
			$this->fecha = $this->val("fecha", $data, $prefix, $separator);
			$this->fechaCierre = $this->val("fechaCierre", $data, $prefix, $separator);
			$this->depAcumulada = $this->val("depAcumulada", $data, $prefix, $separator);
			$this->depPeriodo = $this->val("depPeriodo", $data, $prefix, $separator);
			$this->aniosUso = $this->val("aniosUso", $data, $prefix, $separator);
			$this->depreciacion = $this->prop("depreciacion", $data, $prefix, $separator, 'Depreciacion');
			$this->uma = $this->prop("uma", $data, $prefix, $separator, 'Uma');
			$this->valorLibros = $this->val("valorLibros", $data, $prefix, $separator);
			$this->valorActual = $this->val("valorActual", $data, $prefix, $separator);
			$this->estadoFisico = $this->prop("estadoFisico", $data, $prefix, $separator, 'ItemCatalogo');
			$this->contable = $this->prop("contable", $data, $prefix, $separator, 'ItemCatalogo');
			$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);				
			return true;
		}
		
	}
?>