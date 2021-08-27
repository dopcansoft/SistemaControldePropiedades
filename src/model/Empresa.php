<?
	class Empresa extends Model{
		public $id;
		public $nombre;
		public $descr;
		public $perdiodoDescr;
		public $direccion;
		public $logo;
		public $estatus;
		public $fechaInsert;
		public $bienes;
		public $logoMpio;
		public $logoAyuto;
		public $logoPeriodo;
		public $logoEtiqueta;
		public $tipoEtiqueta;
		public $tipoNumeracion;
		public $inicial;
		public $digitos;
		public $consecutivo;

		const TIPO_ETIQUETA_BARCODE = "CODIGODEBARRAS";
		const TIPO_ETIQUETA_QR = "CODIGOQR";
		const TIPO_NUMERACION_FOLIO = "FOLIO";
		const TIPO_NUMERACION_CONSECUTIVO = "CONSECUTIVO";

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
				$this->nombre = $this->val("nombre", $data, $prefix, $separator);
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->periodoDescr = $this->val("periodoDescr", $data, $prefix, $separator);
				$this->direccion = $this->val("direccion", $data, $prefix, $separator);
				$this->logoMpio = $this->val("logoMpio", $data, $prefix, $separator);
				$this->logoAyuto = $this->val("logoAyuto", $data, $prefix, $separator);
				$this->logoPeriodo = $this->val("logoPeriodo", $data, $prefix, $separator);
				$this->logoEtiqueta = $this->val("logoEtiqueta", $data, $prefix, $separator);
				$this->estatus = $this->prop("estatus", $data, $prefix, $separator, "ItemCatalogo");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$this->bienes = $this->val("bienes", $data, $prefix, $separator);
				$this->tipoEtiqueta = $this->val("tipoEtiqueta", $data, $prefix, $separator);
				$this->tipoNumeracion = $this->val("tipoNumeracion", $data, $prefix, $separator);
				$this->inicial = $this->val("inicial", $data, $prefix, $separator);
				$this->digitos = $this->val("digitos", $data, $prefix, $separator);
				$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);

				$unserialized = true;
			}catch(Exception $e){
				$unserialized = false;
			}
			return $unserialized;	
		}			
	}
?>