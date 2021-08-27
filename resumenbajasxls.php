<?
	session_start();
	session_name("inv"); 
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=reporte_bajas.xls"); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	include("src/vo/config.php");
	Logger::configure("src/config/log4php.xml");
	$log = Logger::getLogger("cupones");
	$settings = new Properties("src/config/settings.xml");
	$database = new Properties("src/config/database.xml");
	$queries = new Properties("src/config/queries.xml");

	if(!isset($_SESSION["usuario"])){
	    header("Location: ".$settings->prop("url.login"));
	}

	$misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    $empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    $periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();
    
    $list = array();
    $bien = new BienDao();
    $catBienesInmuebles = array();
    $catBienesMuebles = array();
    $departamentos = array();
    $catDepreciacion = array();
    $catEdoFisico = array();
    $catOrigen = array();
    $catUma = array();
    
    $presidente = new ResponsableDao();
	$sindico = new ResponsableDao();
	$regidor = new ResponsableDao();
	$tesorero = new ResponsableDao();
	/*$dirJuridico = new ResponsableDao();
	$organoCtrlInt = new ResponsableDao();
	$jefeAdquisiciones = new ResponsableDao();*/

    $estatusColor = array(
    	"1"=>"#00FF00",
    	"2"=>"#00FF00",
    	"3"=>"#00FF00",
    	"4"=>"#FFFF00",
    	"7"=>"#FFFF00",
    	"5"=>"#FF0000",
    	"6"=>"#FF0000",
    	"8"=>"#FFFF00"
    );

    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$catalogoFactory = new CatalogoFactory();
        	$headers = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.headers.list"), $settings, $log, "Clasificacion");
        	$log->debug($headers);
        	$clasificacion = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.list"), $settings, $log, "Clasificacion");
        	$log->debug($clasificacion);
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", Bien::ESTATUS_INVENTARIO_BAJA, $db, $queries, $settings, $log);
    		$list = $inventarioFactory->jerarquiaCC($items, $clasificacion, $headers, $log);
        	$presidente->findByCargo($empresa->id, Responsable::CARGO_PRESIDENTE_MUNICIPAL, $db, $queries, $log);
			$tesorero->findByCargo($empresa->id, Responsable::CARGO_TESORERO, $db, $queries, $log);
			$regidor->findByCargo($empresa->id, Responsable::CARGO_REGIDOR, $db, $queries, $log);
			$sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);
        }else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;
?>
<table width="100%" class="table-reporte">
	<thead>
		<tr class="subheader">
			<th colspan="10" style="text-align:center; font-weight: 600; font-size: 24px;">REPORTE DE BAJAS</th>
		</tr>
	</thead>
</table>
<?
	foreach($list as $data){
		$header = $data["header"];
		if(count($data["content"])>0){
			foreach($data["content"] as $content){
				$clasificacion = $content["clasificacion"];
				?>
				<table class="table-reporte table-margin-bottom">
					<thead>
						<tr class="subheader" style="background:#CCC !important;">
							<th colspan="9">
								<?=htmlentities($header->cuentaContable." - ".$header->descr, ENT_QUOTES, "UTF-8")?>
							</th>
							<th style="text-align: right;" width="10%">
								TOTAL
							</th>
							<th style="text-align: right;" width="10%">
								<?=number_format($data["costo"],2,'.',',')?>
							</th>
						</tr>
						<tr class="subheader" style="background:#DDD !important;">
							<th colspan="9">
								<?=htmlentities($clasificacion->cuentaContable." - ".$clasificacion->descr, ENT_QUOTES, "UTF-8")?>
							</th>
							<th style="text-align: right;" width="10%">
								TOTAL
							</th>
							<th style="text-align: right;" width="10%">
								<?=number_format($content["costo"],2,'.',',')?>
							</th>
						</tr>
						<tr style="background:#A8A8A8 !important;">
							<th width="25%">FOLIO</th>
							<th width="24%">DESCRIPCI&Oacute;N</th>
							<th>DEPARTAMENTO</th>
							<th>MARCA</th>
							<th>MODELO</th>
							<th>SERIE</th>
							<th>MOTOR</th>
							<th>FONDNO<br>ORIGEN</th>
							<th>ESTADO F&Iacute;SICO</th>
							<th>VALOR HISTORICO</th>
							<th>VALOR DE DESECHO</th>
						</tr>
					</thead>
					<tbody style="margin-bottom:0 !important; padding-bottom:0 !important;">
						<?
						$i=1;
						foreach($content["list"] as $bien){									
							?>
							<tr>
								<td class="row-folio"><?=$bien->folio?></td>
								<td width="24%"><?=htmlentities(strtoupper($bien->descripcion), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->departamento->descr), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->marca), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->modelo), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->serie), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->motor), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->origen->descr), ENT_QUOTES, "UTF-8")?></td>
								<td><?=htmlentities(strtoupper($bien->estadoFisico->descr), ENT_QUOTES, "UTF-8")?></td>
								<td style="text-align: right;"><?=number_format($bien->valorAnterior,2,'.',',')?></td>
								<td style="text-align: right;"><?=number_format($bien->valor,2,'.',',')?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<?
			}
		}
	}
	?>