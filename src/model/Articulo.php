<?
	class Articulo extends Model{
		public $id;
		public $empresa;
		public $periodo;
		public $rubro;
		public $departamento;
		public $imagen;
		public $descripcion;
		public $marca;
		public $modelo;
		public $serie;
		public $motor;
		public $factura;
		public $fechaAdquisicion;
		public $importe;
		public $depreciacion;
		public $estadoFisico;
		public $valorReposicion;
		public $valorReemplazo;
		public $depreciacionAcumulada;
		public $depreciacionPeriodo;
		public $aniosUso;
		public $fechaInsert;

		public function __construct($data=null, $prefix=null, $separator='.'){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}
		}

		public function unserialize($data, $prefix=null, $separator='.'){
			$success = false;
			try{
				$this->id = $this->val("id", $data, $prefix, $separator);
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "EmpresaDao");
				$this->periodo = $this->prop("periodo", $data, $prefix, $separator, "ItemCatalogo");
				$this->rubro = $this->prop("rubro", $data, $prefix, $separator, "Rubro");
				$this->departamento = $this->prop("departamento", $data, $prefix, $separator, "ItemCatalogo");
				$this->imagen = $this->val("imagen", $data, $prefix, $separator);
				$this->descripcion = $this->val("descripcion", $data, $prefix, $separator);
				$this->marca = $this->val("marca", $data, $prefix, $separator);
				$this->modelo = $this->val("modelo", $data, $prefix, $separator);
				$this->serie = $this->val("serie", $data, $prefix, $separator);
				$this->motor = $this->val("motor", $data, $prefix, $separator);
				$this->factura = $this->val("factura", $data, $prefix, $separator);
				$this->fechaAdquisicion = $this->val("fechaAdquisicion", $data, $prefix, $separator);
				$this->importe = $this->val("importe", $data, $prefix, $separator);
				$this->depreciacion = $this->prop("depreciacion", $data, $prefix, $separator, 'ItemCatalogo');
				$this->estadoFisico = $this->prop("estadoFisico", $data, $prefix, $separator, 'ItemCatalogo');
				$this->valorReposicion = $this->val("valorReposicion", $data, $prefix, $separator);
				$this->valorReemplazo = $this->val("valorReemplazo", $data, $prefix, $separator);
				$this->depreciacionAcumulada = $this->val("depreciacionAcumulada", $data, $prefix, $separator);
				$this->depreciacionPeriodo = $this->val("depreciacionPeriodo", $data, $prefix, $separator);
				$this->aniosUso = $this->val("aniosUso", $data, $prefix, $separator);
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
			}catch(Exception $e){
				$success = false;
			}
			return $success;
		}
	}
?>