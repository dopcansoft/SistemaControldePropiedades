<?
    session_start();
    session_name("inv");   
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

    include("src/vo/config.php");
    Logger::configure("src/config/log4php.xml");
    $log = Logger::getLogger("cupones");
    $settings = new Properties("src/config/settings.xml");
    $database = new Properties("src/config/database.xml");
    $queries = new Properties("src/config/queries.xml");
    $response = array("result"=>"", "desc"=>"");

    if(!isset($_SESSION["usuario"])){
        header("Location: ".$settings->prop("url.login"));
    }

    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;
    $misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
    $empresa = isset($_SESSION["empresa"])?unserialize($_SESSION["empresa"]):new EmpresaDao();
    $periodo = isset($_SESSION["periodo"])?unserialize($_SESSION["periodo"]):new PeriodoDao();
    
    $list = array();
    $valorHistoricoTotal = 0;
    $valorAnteriorTotal = 0;
    $depreciacionAcumuladaTotal = 0;
    $valorActualizadoTotal = 0;
    $bienesTotal = 0;
    
    $presidente = new ResponsableDao();
	$tesorero = new ResponsableDao();
	$regidor = new ResponsableDao();
	$sindico = new ResponsableDao();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
			$datosCuentaFactory = new DatosCuentaFactory();
			$list = $datosCuentaFactory->listAllInstrumental($empresa->id, $periodo->id, $db, $queries, $log);
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
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=$settings->prop("page.view.name")!=null?$settings->prop("page.view.name"):""?></title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->

    <link rel="stylesheet" href="build/bootstrap-select/css/bootstrap-select.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="vendors/cropper/dist/cropper.css">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <link href="css/articulo.css" rel="stylesheet">
    <style>
	    .video-capture{
	    	background:#000;
	    	display:block;
	    }
	</style>
  </head>
  <body>
  	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<table class="table-reporte-cuentas">
					<thead>
						<tr>
							<th colspan="5">
								<table width="100%">
									<thead>
										<tr>
											<th width="33%" style="text-align: left !important;">
												<img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoMpio?>">
											</th>
											<th width="33%" style="text-align: center !important;">
												<img width="60%" src="<?=$settings->prop("mpio.path.logos").$empresa->logoPeriodo?>">
											</th>
											<th width="33%" style="text-align: right !important;">
												<img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoAyuto?>">
											</th>
										</tr>
										<tr>
											<th colspan="3" style="text-align: center !important;" ><h3><small>BALANZA DE BIENES DE CONTROL INTERNO AL 31 DE DICIEMBRE 2019</small></h3></th>
										</tr>
									</thead>

								</table>	
							</th>
						</tr>
						<tr class="subheader">
							<td width="40%" colspan="2">CUENTA</td>
							<td width="20%">VALOR HISTORICO</td>
							<td width="20%">VALOR ACTUAL/AVALUO</td>
							<td width="20%">NO. BIENES</td>
						</tr>
					</thead>
					<tbody>										
					<?
					foreach($list as $item){
						if(strlen($item->cuenta)==7){
							$valorHistoricoTotal = $valorHistoricoTotal + $item->valorHistorico;
						    $depreciacionAcumuladaTotal = $depreciacionAcumuladaTotal + $item->depreciacionAcumulada;
						    $valorActualizadoTotal = $valorActualizadoTotal+ $item->valorActualizado;
						    $bienesTotal = $bienesTotal + $item->cantidad;	
							$valorAnteriorTotal = $valorAnteriorTotal + $item->valorAnterior;
						}
						?>
						<tr <?=strlen($item->cuenta)==7?'class="subheader" style="background:#DDD !important; font-weight:bold !important;"':''?>>
							<td>
								<?=$item->cuenta?>
							</td>
							<td>
								<?=$item->descr?>
							</td>
							<td style="text-align: right;">
								<?=number_format($item->valorAnterior,2,'.',',')?>
							</td>
							<td style="text-align: right;">
								<?=number_format($item->valorHistorico,2,'.',',')?>
							</td>
							<td style="text-align: right;">
								<?=number_format($item->cantidad,0,'.',',')?>
							</td>
						</tr>										
						<?
					}
					?>
					<tr class="subheader" style="background:#DDD !important; font-weight:bold !important;" >
						<td colspan="2">
							TOTAL
						</td>
						<td style="text-align: right;">
							<?=number_format($valorAnteriorTotal,2,'.',',')?>
						</td>
						<td style="text-align: right;">
							<?=number_format($valorHistoricoTotal,2,'.',',')?>
						</td>
						<td style="text-align: right;">
							<?=number_format($bienesTotal,0,'.',',')?>
						</td>
					</tr>
					</tbody>
					<tfoot>
						<tr colspan="5">
							<table width="100%">
								<tr>
									<td colspan="6">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="6">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" style="">&nbsp;</td>
									<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$presidente->titulo." ".$presidente->nombre." ".$presidente->apellido?></td>
									<td colspan="2" style="">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" style="">&nbsp;</td>
									<td colspan="2" style="text-align: center; font-weight: bold !important;">Presidente Municipal</td>
									<td colspan="2" style="">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="6">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="6">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
									<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$tesorero->titulo." ".$tesorero->nombre." ".$tesorero->apellido?></td>
									<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$regidor->titulo." ".$regidor->nombre." ".$regidor->apellido?></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;">Síndico</td>
									<td colspan="2" style="text-align: center;">Tesorero Municipal</td>
									<td colspan="2" style="text-align: center;">Regidor</td>
								</tr>
							</table>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<footer class="footer">
		<div class="footer-titulo">
			ibexpro.com.mx
		</div>
		<div class="paginado"></div>
	</footer>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="res_cc.js"></script>
</html>
					