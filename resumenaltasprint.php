<?
	session_start();
	session_name("inv"); 
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
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", Bien::ESTATUS_INVENTARIO_ALTA, $db, $queries, $settings, $log);
    		$list = $inventarioFactory->jerarquiaCC($items, $clasificacion, $headers, $log);
        }else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" >
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
    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet" media="screen">
    <link href="css/principal.css" rel="stylesheet" media="print">

    <link href="css/articulo.css" rel="stylesheet">
    <title></title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?
				foreach($list as $data){
					$header = $data["header"];
					if(count($data["content"])>0){
						foreach($data["content"] as $content){
							$clasificacion = $content["clasificacion"];
							?>
							<table class="table-reporte table-margin-bottom">
								<thead>
									<tr>
										<th colspan="3" style="text-align: center !important;">
											<img src="imgs/escudo_tuxtla.jpg">
										</th>
										<th colspan="5" style="text-align: center !important;">
											<img src="imgs/hyto_santiago.jpg">
										</th>
										<th colspan="3" style="text-align: center !important;">
											<img src="imgs/logo_tuxtla.jpg">
										</th>
									</tr>
									<tr class="subheader" style="background:#CCC !important;">
										<th colspan="9">
											<?=$header->cuentaContable." - ".$header->descr?>
										</th>
										<th style="text-align: right;" width="10%">
											TOTAL
										</th>
										<th style="text-align: right;" width="10%">
											<?=number_format($data["costo"],2,'.',',')?>
										</th>
										<!-- <th style="text-align: right;" width="10%">
											TOTAL DEPRECIADO
										</th>
										<th style="text-align: right;" width="10%">
											<?=number_format($data["total"],2,'.',',')?>
										</th> -->
									</tr>
									<tr class="subheader" style="background:#DDD !important;">
										<th colspan="9">
											<?=$clasificacion->cuentaContable." - ".$clasificacion->descr?>
										</th>
										<th style="text-align: right;" width="10%">
											TOTAL
										</th>
										<th style="text-align: right;" width="10%">
											<?=number_format($content["costo"],2,'.',',')?>
										</th>
										<!-- <th style="text-align: right;" width="10%">
											TOTAL DEPRECIADO
										</th>
										<th style="text-align: right;" width="10%">
											<?=number_format($content["total"],2,'.',',')?>
										</th> -->
									</tr>
									<tr style="background:#A8A8A8 !important;">
										<th width="15%">FOLIO</th>
										<th>FOTOGRAFIA</th>
										<th width="24%">DESCRIPCIÓN</th>
										<th>MARCA</th>
										<th>MODELO</th>
										<th>SERIE</th>
										<th>MOTOR</th>
										<th>ESTADO FÍSICO</th>
										<th>VALOR HISTORICO</th>
										<!-- <th>VALOR DEPRECIADO</th> -->
										<th>DEPRECIACIÓN</th>
										<th>DEPRECIADO</th>
									</tr>
								</thead>
								<tbody style="margin-bottom:0 !important; padding-bottom:0 !important;">
									<?
									$i=1;
									foreach($content["list"] as $bien){									
										?>
										<tr>
											<td><?=$bien->folio?></td>
											<td><? if($bien->imagen!=""){ ?><img src="<?=$settings->prop("system.url").$bien->imagen?>" width="75"><? }else{ echo "&nbsp;"; } ?></td>
											<td width="24%"><?=$bien->descripcion?></td>
											<td><?=$bien->marca?></td>
											<td><?=$bien->modelo?></td>
											<td><?=$bien->serie?></td>
											<td><?=$bien->motor?></td>
											<td><i style="color:<?=$estatusColor[$bien->estadoFisico->id]?>;" class="fa fa-circle"></i>&nbsp;<?=$bien->estadoFisico->descr?></td>
											<td style="text-align: right;"><?=number_format($bien->valor,2,'.',',')?></td>
											<!-- <td style="text-align: right;"><?=number_format($bien->valor-($bien->depreciacionAcumulada!=""?$bien->depreciacionAcumulada:0),2,'.',',')?></td> -->
											<td style="text-align: right;"><?=number_format(($bien->depreciacionAcumulada!=""?$bien->depreciacionAcumulada:0),2,'.',',')?></td>
											<td><?=$bien->valor<=$bien->depreciacionAcumulada?"SI":"NO"?></td>
										</tr>
										<?
										$i++;
									}
									?>
								</tbody>
								<tfoot style="margin-top:0 !important; padding-top:0 !important;">
									<tr>
										<td colspan="11" style="padding-top:25px !important;">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="11">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
										<td colspan="5" style="text-align: center !important; font-weight: bold !important; border-top:1px solid #000 !important;">Lic Argenis Vázquez Copete</td>
										<td colspan="3">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
										<td colspan="5" style="text-align: center !important;">Presidente Municipal</td>
										<td colspan="3">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="11">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="11">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Profa. Carmen Rodas Mejia</td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">C.P. Mónica Lizette Gutiérrez Rosas</td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Crecencio Rascón Cobos</td>
									</tr>
									<tr>
										<td colspan="3" style="text-align: center;">Síndico</td>
										<td colspan="1" >&nbsp;</td>
										<td colspan="3" style="text-align: center;">Tesorero Municipal</td>
										<td colspan="1" >&nbsp;</td>
										<td colspan="3" style="text-align: center;">Regidor de Hacienda</td>
									</tr>
								</tfoot>
							</table>
							<?
						}
					}
				}
				?>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="resumenbajasprint.js"></script>
</html>