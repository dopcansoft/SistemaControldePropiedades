<?
	class BienInmueble extends Model{
		public $id;
		public $folio;
		public $consecutivo;
		public $empresa;
		public $tipo;
		public $clasificacion;
		public $descr;
		public $ubicacion;
		public $medNorte;
		public $medSur;
		public $medEste;
		public $medOeste;
		public $colNorte;
		public $colSur;
		public $colEste;
		public $colOeste;
		public $superficieTerreno;
		public $superficieConstruccion;
		public $uso;
		public $servAgua;
		public $servDrenaje;
		public $servPavimentacion;
		public $servLuz;
		public $servTelefonia;
		public $servInternet;
		public $servGasEstacionario;
		public $aprovechamiento;
		public $modoAdquisicion;
		public $numeroEscrituraConvenio;
		public $numRegistroPropiedad;
		public $cuentaCatastral;
		public $fechaUltimoAvaluo;
		public $gravamenPendiente;
		public $valorTerreno;
		public $valorConstruccion;
		public $valor;
		public $valorCapitalizable;
		public $observaciones;
		public $images;
		public $imagen;
		public $depreciacion;
		public $depreciacionAcumulada;
		public $cuentaContable;
		


		public function __construct($data=null, $prefix=null, $separator="."){
			if(isset($data)){
				$this->unserialize($data, $prefix, $separator);
			}else{
				$this->unserialize(array());
			}	
		}

		public function unserialize($data, $prefix=null, $separator="."){
			$success = false;
			try{
				$this->id= $this->val("id", $data, $prefix, $separator);
				$this->folio = $this->val("folio", $data, $prefix, $separator);
				$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "Empresa");
				$this->tipo = $this->prop("tipo", $data, $prefix, $separator,"ClasificacionInmueble");
				$this->clasificacion = $this->prop("clasificacion", $data, $prefix, $separator,"ClasificacionInmueble");
				$this->descr = $this->val("descr", $data, $prefix, $separator);
				$this->ubicacion = $this->val("ubicacion", $data, $prefix, $separator);
				$this->imagen = $this->val("imagen", $data, $prefix, $separator);
				$this->medNorte = $this->val("medNorte", $data, $prefix, $separator);
				$this->medSur = $this->val("medSur", $data, $prefix, $separator);
				$this->medEste = $this->val("medEste", $data, $prefix, $separator);
				$this->medOeste = $this->val("medOeste", $data, $prefix, $separator);
				$this->colNorte = $this->val("colNorte", $data, $prefix, $separator);
				$this->colSur = $this->val("colSur", $data, $prefix, $separator);
				$this->colEste = $this->val("colEste", $data, $prefix, $separator);
				$this->colOeste = $this->val("colOeste", $data, $prefix, $separator);
				$this->superficieTerreno = $this->val("superficieTerreno", $data, $prefix, $separator);
				$this->superficieConstruccion = $this->val("superficieConstruccion", $data, $prefix, $separator);
				$this->uso = $this->prop("uso", $data, $prefix, $separator,"ItemCatalogo");
				$this->servAgua = $this->val("servAgua", $data, $prefix, $separator);
				$this->servDrenaje = $this->val("servDrenaje", $data, $prefix, $separator);
				$this->servPavimentacion = $this->val(">servPavimentación", $data, $prefix, $separator);
				$this->servLuz = $this->val("servLuz", $data, $prefix, $separator);
				$this->servTelefonia = $this->val("servTelefonia", $data, $prefix, $separator);
				$this->servInternet = $this->val("servInternet", $data, $prefix, $separator);
				$this->servGasEstacionario = $this->val("servGasEstacionario", $data, $prefix, $separator);
				$this->aprovechamiento = $this->prop("aprovechamiento", $data, $prefix, $separator, "ItemCatalogo");
				$this->modoAdquisicion = $this->prop("modoAdquisicion", $data, $prefix, $separator, "ItemCatalogo");
				$this->numeroEscrituraConvenio = $this->val("numeroEscrituraConvenio", $data, $prefix, $separator);
				$this->numRegistroPropiedad = $this->val("numRegistroPropiedad", $data, $prefix, $separator);
				$this->cuentaCatastral = $this->val("cuentaCatastral", $data, $prefix, $separator);
				$this->fechaUltimoAvaluo = $this->val("fechaUltimoAvaluo", $data, $prefix, $separator);
				$this->gravamenPendiente = $this->val("gravamenPendiente", $data, $prefix, $separator);
				$this->valorTerreno = $this->val("valorTerreno", $data, $prefix, $separator);
				$this->valorConstruccion = $this->val("valorConstruccion", $data, $prefix, $separator);
				$this->valor = $this->val("valor", $data, $prefix, $separator);
				$this->valorCapitalizable = $this->val("valorCapitalizable", $data, $prefix, $separator);
				$this->observaciones = $this->val("observaciones", $data, $prefix, $separator);
				$this->cuentaContable = $this->val("cuentaContable", $data, $prefix, $separator);
				$this->images = explode(",",isset($data["images"])?$data["images"]:'');
				$this->depreciacion = 0;
				$this->depreciacionAcumulada = 0;
				$success = true;
			}catch(Exception $e){
				$success = false;
			}			
			return $success;
		}

	}
?>