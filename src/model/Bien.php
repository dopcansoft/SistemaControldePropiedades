<?
	class Bien extends Model{
		public $id;
		public $folio;
		public $consecutivo;
		public $folioAnterior;
		public $folioUnico;
		public $numero;
		public $empresa;
		public $periodo;
		public $tipoClasificacion; 
		public $clasificacion;
		public $cuentaContable;
		public $cuentaDepreciacion;
		public $origen;
		public $departamento;
		public $imagen;
		public $imagenBlob;
		public $barcodeBlob;
		public $descripcion;
		public $marca;
		public $modelo;
		public $serie;
		public $motor;
		public $factura;
		public $archivoFactura;
		public $archivoPoliza;
		public $fechaAdquisicion;
		public $tipoValuacion;
		public $valor;
		public $tipoValuacionFinal;
		public $valorFinal;
		public $depreciacion;
		public $depreciacionAcumulada;
		public $depreciacionPeriodo;
		public $depreciacionFinal;
		public $depreciacionAcumuladaFinal;
		public $depreciacionPeriodoFinal;
		public $estadoFisico;		
		public $aniosUso;
		public $inventarioContable;
		public $uma;
		public $valorUma;
		public $estatusInventario;
		public $fechaInsert;
		public $notas;
		public $images;
		public $responsable;
		public $color;
		public $valorAnterior;
		public $matricula;
		public $fechaCierre;
		public $bandera;
		public $descrBandera;
		public $valuaciones;

		const VALUACION_IMPORTE = 1;
		const VALUACION_REPOSICION = 2;
		const VALUACION_REEMPLAZO = 3;
		const VALUACION_DESECHO = 4;
		const ESTATUS_INVENTARIO_MANTIENE = 1;
		const ESTATUS_INVENTARIO_ALTA = 2;
		const ESTATUS_INVENTARIO_BAJA = 3;

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
				$this->folio = $this->val("folio", $data, $prefix, $separator);
				$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);				
				$this->folioAnterior = $this->val("folioAnterior", $data, $prefix, $separator);
				$this->folioUnico = $this->val("folioUnico", $data, $prefix, $separator);
				$this->numero = $this->val("numero", $data, $prefix, $separator);
				$this->consecutivo = $this->val("consecutivo", $data, $prefix, $separator);
				$this->empresa = $this->prop("empresa", $data, $prefix, $separator, "EmpresaDao");
				$this->periodo = $this->prop("periodo", $data, $prefix, $separator, "ItemCatalogo");
				$this->tipoClasificacion = $this->prop("tipoClasificacion", $data, $prefix, $separator, "ItemCatalogo");
				$this->clasificacion = $this->prop("clasificacion", $data, $prefix, $separator, "Clasificacion");
				$this->cuentaContable = $this->val("cuentaContable", $data, $prefix, $separator);
				$this->cuentaDepreciacion = $this->val("cuentaDepreciacion", $data, $prefix, $separator);
				$this->origen = $this->prop("origen", $data, $prefix, $separator, "ItemCatalogo");
				$this->departamento = $this->prop("departamento", $data, $prefix, $separator, "Departamento");				
				$this->imagen = $this->val("imagen", $data, $prefix, $separator);
				$this->imagenBlob = $this->val("imagenBlob", $data, $prefix, $separator);
				$this->barcodeBlob = $this->val("barcodeBlob", $data, $prefix, $separator);
				$this->archivoPoliza = $this->val("archivoPoliza", $data, $prefix, $separator);
				$this->archivoFactura = $this->val("archivoFactura", $data, $prefix, $separator);
				$this->descripcion = $this->val("descripcion", $data, $prefix, $separator);
				$this->marca = $this->val("marca", $data, $prefix, $separator);
				$this->modelo = $this->val("modelo", $data, $prefix, $separator);
				$this->serie = $this->val("serie", $data, $prefix, $separator);
				$this->motor = $this->val("motor", $data, $prefix, $separator);
				$this->factura = $this->val("factura", $data, $prefix, $separator);
				$this->fechaAdquisicion = $this->val("fechaAdquisicion", $data, $prefix, $separator);
				$this->tipoValuacion = $this->prop("tipoValuacion", $data, $prefix, $separator, "ItemCatalogo");
				$this->valor = $this->val("valor", $data, $prefix, $separator);
				$this->depreciacion = $this->prop("depreciacion", $data, $prefix, $separator, 'Depreciacion');
				$this->depreciacionAcumulada = $this->val("depreciacionAcumulada", $data, $prefix, $separator);
				$this->depreciacionPeriodo = $this->val("depreciacionPeriodo", $data, $prefix, $separator);				
				$this->estadoFisico = $this->prop("estadoFisico", $data, $prefix, $separator, 'ItemCatalogo');
				$this->aniosUso = $this->val("aniosUso", $data, $prefix, $separator);
				$this->inventarioContable = $this->val("inventarioContable", $data, $prefix, $separator);
				$this->uma = $this->prop("uma", $data, $prefix, $separator, "Uma");
				$this->valorUma = $this->val("valorUma", $data, $prefix, $separator);
				$this->estatusInventario = $this->prop("estatusInventario", $data, $prefix, $separator, "ItemCatalogo");
				$this->fechaInsert = $this->val("fechaInsert", $data, $prefix, $separator);
				$this->notas = $this->val("notas", $data, $prefix, $separator);
				$this->images = explode(",",isset($data["images"])?$data["images"]:'');
				$this->responsable = $this->prop("responsable", $data, $prefix, $separator, "Responsable");
				$this->color = $this->prop("color", $data, $prefix, $separator, "ItemCatalogo");
				$this->valorAnterior = $this->val("valorAnterior", $data, $prefix, $separator);
				$this->matricula = $this->val("matricula", $data, $prefix, $separator);
				$this->fechaCierre = $this->val("fechaCierre", $data, $prefix, $separator);
				$this->bandera = $this->prop("bandera", $data, $prefix, $separator, "ItemCatalogo");
				$this->valuaciones = isset($this->valuaciones)&&count($this->valuaciones)>0?$this->valuaciones:array();
				$success = true;
			}catch(Exception $e){
				$success = false;
			}
			return $success;
		}

		public function getShortName($caracteres = 20){
			$uncomplete = true;
			$cad = "";
			if(isset($this->descripcion) && $this->descripcion!=""){
				$arr = explode(" ",$this->descripcion);
				$i = 0;			
				while($uncomplete){
					if(isset($arr[$i])){
						$cad  = $cad.$arr[$i]." ";
						if(strlen($cad)>=$caracteres){
							$uncomplete = false;
						}else{
							if($i<=count($arr)){
								$i++;
							}else{
								$uncomplete = false;
							}						
						}	
					}else{
						$uncomplete = false;
					}					
				}
			}
			$cad = trim(trim(trim(trim(trim($cad), ","), "."),":"),"/");
			return $cad; 
		}
	}
?>