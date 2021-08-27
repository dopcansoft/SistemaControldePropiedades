<?
	include("../vo/config.php");
	include("../lib/phpbarcode/barcodeoffline.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$successFile = false;
	$dataInput = new DataInput();
	$imagen = "";
	$log->debug($data);
	$log->debug($_FILES);
	if($dataInput->validSqueme(array(
		"bien",
		"empresa",
		"periodo",
		"departamento",
		"clasificacion"
	), $data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$bien = new BienDao(array(
					"id"=>$data["bien"],
					"empresa.id"=>$data["empresa"],
					"periodo.id"=>$data["periodo"],
					"folio"=>$data["folio"],
					"consecutivo"=>$data["consecutivo"],
					"departamento.id"=>$data["departamento"],
					"tipoClasificacion.id"=>$data["tipoClasificacion"],
					"clasificacion.id"=>$data["clasificacion"],
					"descripcion"=>$data["descripcion"],
					"color.id"=>$data["color"],
					"estadoFisico.id"=>$data["estadoFisico"],
					"tipoValuacion.id"=>$data["tipoValuacion"],
					"valor"=>$data["valuacion"],
					"valorAnterior"=>$data["valorAnterior"],
					"uma.id"=>$data["uma"],
					"valorUma"=>$data["valorUma"],
					"origen.id"=>$data["origen"],
					"fechaAdquisicion"=>$data["fechaAdquisicion"],
					"fechaCierre"=>$data["fechaCierre"],
					"inventarioContable"=>$data["inventarioContable"],
					"depreciacion.id"=>$data["depreciacion"],
					"depreciacionAcumulada"=>$data["depreciacionAcumulada"],
					"depreciacionPeriodo"=>$data["depreciacionPeriodo"]
				));					
				if($bien->updateMin($db, $queries, $log)){
					$bien->find($db, $queries, $log);
					$log->debug('Se actualizo satisfactoriamente su informacion');
					$error = "";
					try{
						$log->debug('Creando codigo de barras');
						$filepath = $settings->prop("system.path").$settings->prop("barcode.path").$bien->id.".png";
						$log->debug($filepath);
						$text = $bien->empresa->id.';'.$bien->periodo->id.';'.$bien->id;
						$log->debug($text);
						$size = 20;
						$orientation="horizontal";
						$code_type="code128";
						$print = false;
						$sizefactor=1;
						barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor);	
						$log->debug('codigo de barras creado satisfactoriamente');
					}catch(Exception $err){
						$log->debug($err);
						$error = ", no obstante ocurrio un error al generar el código de barras";
					}
					$response["result"] = "SUCCESS";
					$response["desc"] = "Se actualizo satisfactoriamente su informacion";
					$response["data"] = $bien;
				}else{
					$log->error('error');
					$response["result"] = "FAIL";
					$response["desc"] = "Ocurrio un problema al intentar actualizar la información";
				}
			}else{
				$log->error("No se ha podido establecer conexión con base de datos");
				$response["result"]="FAIL";
				$response["desc"]="No se ha podido establecer conexión con base de datos";		
			}
		}catch(PDOException $e){
			$log->error("PDOException: ".$e->getMessage());
			$response["result"]="FAIL";
			$response["desc"]="Ocurrio un error al consultar la información";	
		}
		$log->debug('Cierra conexion a Base de datos');
		$db = null;
	}else{
		$response["result"]="FAIL";
		$response["desc"]="Request invalido, Faltan parametros";
		$log->error('Request invalido, Faltan parametros');
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($response);
?>