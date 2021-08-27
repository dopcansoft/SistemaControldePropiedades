<?
	ini_set('max_execution_time', 300);
    ini_set('memory_limit', '256M');
    include("../vo/config.php");
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$dataInput = new DataInput();
	$list = array();
	if($dataInput->validSqueme(array(
		"empresa",
		"periodo"
	),$data)){
		try{
			$db = new DBConnector($database);
			if(isset($db)){
				$log->debug("Empresa: "+$data["empresa"]);
				$log->debug("Periodo: "+$data["periodo"]);
				$log->debug('Obteniendo info de BD');
				$bienFactory = new BienFactory();
				$list = $bienFactory->filtrado($data["empresa"], $data["periodo"], "", "", "", "", "", "", "", "", $db, $queries, $settings, $log, "");
				$log->debug('Obteniendo registros: '.count($list));
				/*for($i=0;$i<count($list);$i++){
					if(isset($list[$i]->imagen) && $list[$i]->imagen!=""){
						$list[$i]->imagen = $settings->prop("system.path").$list[$i]->imagen;
					}					
				}*/
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
	$log->debug('Creando xlsx');
	$ope = new PHPExcel();
	$ope->getProperties()
				->setCreator("ibexpro.com.mx")
				->setTitle("Inventario Completo")
				->setSubject("Inventario Completo")
				->setDescription("Incluye el total de bienes: ".count($list));

	$ope->setActiveSheetIndex(0)
		->setCellValue('A1', 'ID')
		->setCellValue('B1', 'DESCRIPCIÓN')
		->setCellValue('C1', 'MARCA')
		->setCellValue('D1', 'MODELO')
		->setCellValue('E1', 'FOLIO')
		->setCellValue('F1', 'CONSECUTIVO')
		->setCellValue('G1', 'SERIE')
		->setCellValue('H1', 'MOTOR')
		->setCellValue('I1', 'FACTURA')
		->setCellValue('J1', 'ARCHIVO FACTURA')
		->setCellValue('K1', 'NOTAS')
		->setCellValue('L1', 'FECHA ADQUISICIÓN')
		->setCellValue('M1', 'TIPOVALUACION.ID')
		->setCellValue('N1', 'TIPOVALUACION.DESCR')
		->setCellValue('O1', 'VALOR')
		->setCellValue('P1', 'VALOR ANTERIOR')
		->setCellValue('Q1', 'FECHA_INSERT')
		->setCellValue('R1', 'CLASIFICACION.ID')
		->setCellValue('S1', 'CLASIFICACION.DESCR')
		->setCellValue('T1', 'CLASIFICACION.GRUPO')
		->setCellValue('U1', 'CLASIFICACION.SUBGRUPO')
		->setCellValue('V1', 'CLASIFICACION.CLASE')
		->setCellValue('W1', 'CLASIFICACION.SUBCLASE')
		->setCellValue('X1', 'CLASIFICACION.CUENTA CONTABLE')
		->setCellValue('Y1', 'CLASIFICACION.CUENTA DEPRECIACIÓN')
		->setCellValue('Z1', 'DEPARTAMENTO.ID')
		->setCellValue('AA1', 'DEPARTAMENTO.DESCR')
		->setCellValue('AB1', 'DEPRECIACIÓN.ID')
		->setCellValue('AC1', 'DEPRECIACIÓN.CUENTA')
		->setCellValue('AD1', 'DEPRPECIACIÓN.DESCR')
		->setCellValue('AE1', 'DEPRPECIACIÓN.VIDA ÚTIL')
		->setCellValue('AF1', 'DEPRECIACION.ANUAL')
		->setCellValue('AG1', 'FONDO DE ORIGEN.ID')
		->setCellValue('AH1', 'FONDO DE ORIGEN.DESCR')
		->setCellValue('AI1', 'PERIODO.ID')
		->setCellValue('AJ1', 'PERIODO.DESCR')
		->setCellValue('AK1', 'ESTADO FISICO.ID')
		->setCellValue('AL1', 'ESTADO FISICO.DESCR')
		->setCellValue('AM1', 'DEPRECIACIÓN ACUMULADA')
		->setCellValue('AN1', 'DEPRECIACIÓN PERIODO')
		->setCellValue('AO1', 'AÑOS DE USO')
		->setCellValue('AP1', 'UMA.ID')
		->setCellValue('AQ1', 'UMA.AÑO')
		->setCellValue('AR1', 'UMA.VALOR_DIARIO')
		->setCellValue('AS1', 'UMA.VALOR_MENSUAL')
		->setCellValue('AT1', 'UMA.VALOR_ANUAL')
		->setCellValue('AU1', 'UMA.FACTOR')
		->setCellValue('AV1', 'UMA.VALOR UMA')
		->setCellValue('AW1', 'ESTATUS INVENTARIO.ID')
		->setCellValue('AX1', 'ESTATUS INVENTARIO.DESCR')
		->setCellValue('AY1', 'COLOR.ID')
		->setCellValue('AZ1', 'COLOR.DESCR')
		->setCellValue('BA1', 'BANDERA.ID')
		->setCellValue('BB1', 'BANDERA.DESCR');

	$log->debug('Setteando campos de excel');
	$i=2;
	foreach($list as $item){
		$log->debug('Insertando registro: '.($i-2));
		$ope->setActiveSheetIndex(0)
		->setCellValue('A'.$i, $item->id)
		->setCellValue('B'.$i, $item->descripcion)
		->setCellValue('C'.$i, $item->marca)
		->setCellValue('D'.$i, $item->modelo)
		->setCellValue('E'.$i, $item->folio)
		->setCellValue('F'.$i, $item->consecutivo)
		->setCellValue('G'.$i, $item->serie)
		->setCellValue('H'.$i, $item->motor)
		->setCellValue('I'.$i, $item->factura)
		->setCellValue('J'.$i, $item->archivoFactura)
		->setCellValue('K'.$i, $item->notas)
		->setCellValue('L'.$i, $item->fechaAdquisicion)
		->setCellValue('M'.$i, $item->tipoValuacion->id)
		->setCellValue('N'.$i, $item->tipoValuacion->descr)
		->setCellValue('O'.$i, $item->valor)
		->setCellValue('P'.$i, $item->valorAnterior)
		->setCellValue('Q'.$i, $item->fechaInsert)
		->setCellValue('R'.$i, $item->clasificacion->id)
		->setCellValue('S'.$i, $item->clasificacion->descr)
		->setCellValue('T'.$i, $item->clasificacion->grupo)
		->setCellValue('U'.$i, $item->clasificacion->subgrupo)
		->setCellValue('V'.$i, $item->clasificacion->clase)
		->setCellValue('W'.$i, $item->clasificacion->subclase)
		->setCellValue('X'.$i, $item->clasificacion->cuentaContable)
		->setCellValue('Y'.$i, $item->clasificacion->cuentaDepreciacion)
		->setCellValue('Z'.$i,  $item->departamento->id)
		->setCellValue('AA'.$i, $item->departamento->descr)
		->setCellValue('AB'.$i, $item->depreciacion->id)
		->setCellValue('AD'.$i, $item->depreciacion->descr)
		->setCellValue('AE'.$i, $item->depreciacion->vidaUtil)
		->setCellValue('AF'.$i, $item->depreciacion->depreciacionAnual)
		->setCellValue('AG'.$i, $item->origen->id)
		->setCellValue('AH'.$i, $item->origen->descr)
		->setCellValue('AI'.$i, $item->periodo->id)
		->setCellValue('AJ'.$i, $item->periodo->descr)
		->setCellValue('AK'.$i, $item->estadoFisico->id)
		->setCellValue('AL'.$i, $item->estadoFisico->descr)
		->setCellValue('AM'.$i, $item->depreciacionAcumulada)
		->setCellValue('AN'.$i, $item->depreciacionPeriodo)
		->setCellValue('AO'.$i, $item->aniosUso)
		->setCellValue('AP'.$i, $item->uma->id)
		->setCellValue('AQ'.$i, $item->uma->anio)
		->setCellValue('AR'.$i, $item->uma->valorDiario)
		->setCellValue('AS'.$i, $item->uma->valorMensual)
		->setCellValue('AT'.$i, $item->uma->valorAnual)
		->setCellValue('AU'.$i, $item->uma->factor)
		->setCellValue('AV'.$i, number_format($item->uma->valorDiario*$item->uma->factor,2))
		->setCellValue('AW'.$i, $item->estatusInventario->id)
		->setCellValue('AX'.$i, $item->estatusInventario->descr)
		->setCellValue('AY'.$i, $item->color->id)
		->setCellValue('AZ'.$i, $item->color->descr)
		->setCellValue('BA'.$i, $item->bandera->id)
		->setCellValue('BB'.$i, $item->bandera->descr);

		/*try{
			$objDrawing = new PHPExcel_Worksheet_Drawing();    //create object for Worksheet drawing
			$objDrawing->setName($item->folio);        //set name to image
			$objDrawing->setDescription($item->folio); //set description to image
			$objDrawing->setPath($item->imagen);
			$objDrawing->setOffsetX(25);                       //setOffsetX works properly
			$objDrawing->setOffsetY(10);                       //setOffsetY works properly
			$objDrawing->setCoordinates("BC".$i);        //set image to cell
			$objDrawing->setWidth(32);                 //set width, height
			$objDrawing->setHeight(32);  
			$objDrawing->setWorksheet($ope->getActiveSheet());		
		}catch(PHPExcel_Exception $e){
			$log->error($e->getMessage());
		}*/

		$i++;
	}

	$log->debug('Setteando tamaño de columnas');
	$ope->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$ope->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$ope->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('K')->setWidth(60);
	$ope->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('S')->setWidth(20);
	$ope->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
	$ope->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AK')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AL')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AM')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AN')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AO')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AP')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AQ')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AR')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AS')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AT')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AU')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AV')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AW')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AX')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AY')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('AZ')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('BA')->setAutoSize(true);
	$ope->getActiveSheet()->getColumnDimension('BB')->setAutoSize(true);

	$ope->getActiveSheet()->setAutoFilter("A1:BB".$i);
	
	$ope->getActiveSheet()
		->getStyle('A1:BB1')
		->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()
		->setRGB('C0C0C0');
	
	$ope->getActiveSheet()->setTitle('Bienes Muebles');
	$ope->setActiveSheetIndex(0);
	$log->debug('Termina archivo xlsx');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="inmuebles.xlsx"');
	header('Cache-Control: max-age=0');
	$log->debug('publicando xlsx');
	$objWriter = PHPExcel_IOFactory::createWriter($ope, 'Excel2007');
	$objWriter->save('php://output');
?>