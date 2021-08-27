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
    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;
    $list = array();
    
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$clasificacionBm = isset($data["clasificacionBm"])?$data["clasificacionBm"]:"";
			$departamento = isset($data["departamento"])?$data["departamento"]:"";
			$edoFisico = isset($data["estadoFisico"])?$data["estadoFisico"]:"";

			$fechaInicio = isset($data["fechaInicio"])?$data["fechaInicio"]:"";
			$fechaFin = isset($data["fechaFin"])?$data["fechaFin"]:"";
			$tipoFecha = isset($data["tipoFecha"])?$data["tipoFecha"]:"FECHA_INSERT";
			$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;

        	$inventarioFactory = new BienFactory();
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", $fechaInicio, $fechaFin, "", "", "", "", $estatusInventario, $db, $queries, $settings, $log, $tipoFecha);	
        	for($x=0;$x<count($list);$x++){        		
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}        	
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
<body class="body-loading" style="background:#FFF !important;">
	<div class="content-preloader">
    	<div class="loader">Imprimiendo...</div>
	</div>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<table class="table-reporte">
					<thead>
						<tr style="background:#FFF !important;">
							<!-- <th colspan="4" width="33%" style="text-align: center !important;">
								<img src="imgs/escudo_tuxtla.jpg">
							</th>
							<th colspan="4" width="33%" style="text-align: center !important;">
								<img src="imgs/hyto_santiago.jpg">
							</th>
							<th colspan="4" width="33%" style="text-align: center !important;">
								<img src="imgs/logo_tuxtla.jpg">
							</th> -->
							<th colspan="13" style="background:#FFF !important;">
								<!--<table style="margin-bottom:20px !important; background:#FFF !important;" width="100%">
									<thead>
										<tr style="background:#FFF !important;">
											<th width="33%" style="text-align: left !important; background:#FFF !important;">
												<img width="100px" src="imgs/escudo_jilotepec.jpg">
											</th>
											<th width="33%" style="text-align: center !important; background:#FFF !important;">
												<img width="250px" src="imgs/hyto_jilotepec.jpg">
											</th>
											<th width="33%" style="text-align: right !important; background:#FFF !important;">
												<img width="150px" src="imgs/logo_jilotepec.jpg">
											</th>
										</tr>
							    	</thead>
							    </table> -->
							    <table style="background:#FFF !important;" width="100%">
									<thead>
										<th width="33%" style="text-align: left !important;">
											<img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoMpio?>">
										</th>
										<th width="33%" style="text-align: center !important;">
											<img width="60%" src="<?=$settings->prop("mpio.path.logos").$empresa->logoPeriodo?>">
										</th>
										<th width="33%" style="text-align: right !important;">
											<img style="max-width: 90px; max-height: 90px" src="<?=$settings->prop("mpio.path.logos").$empresa->logoAyuto?>">
										</th>
									</thead>
								</table>
							</th>
						</tr>
						<tr style="background:#DDD !important;">
							<th>Descripción</th>
							<th>Clasificación</th>
							<th>Tipo</th>
							<th>Departamento Asignado</th>
							<th>Origen</th>
							<th>Tipo Valuación</th>
							<th>Valor</th>
							<th>Fotografia</th>
							<th>Marca</th>
							<th>Modelo</th>
							<th>Serie</th>
							<th>Motor</th>
							<th>Estado Físico</th>
						</tr>
					</thead>
					<tbody>
						<?
						if(isset($list) && count($list)>0){
							foreach($list as $bien){
								?>
								<tr>
									<td><?=$bien->descripcion?></td>
									<td><?=$bien->clasificacion->descr?></td>
									<td><?=$bien->tipoClasificacion->descr?></td>
									<td><?=$bien->departamento->descr?></td>
									<td><?=$bien->origen->descr?></td>
									<td><?=$bien->tipoValuacion->descr?></td>
									<td><?=$bien->valor?></td>
									<td><img src="<?=$bien->imagen?>" width="75"></td>
									<td><?=$bien->marca?></td>
									<td><?=$bien->modelo?></td>
									<td><?=$bien->serie?></td>
									<td><?=$bien->motor?></td>
									<td><?=$bien->estadoFisico->descr?></td>
								</tr>
								<?
							}
						}
						?>						
					</tbody>
					<tfoot>
						<tr>
							<td colspan="13">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="13">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" style="border-top:1px solid #FFF !important;">&nbsp;</td>
							<td colspan="5" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">L.A.E.T. SERGIO FERNANDEZ LARA</td>
							<td colspan="4" style="border-top:1px solid #FFF !important;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" style="border-top:1px solid #FFF !important;">&nbsp;</td>
							<td colspan="5" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Presidente Municipal</td>
							<td colspan="4" style="border-top:1px solid #FFF !important;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="13">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="13">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">C. NELIA CALLEJAS RIVERA</td>
							<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
							<td colspan="5" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">L.C. VICTOR HUGO PRIETO GUERRERO</td>
							<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
							<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">C. RAUL BAEZ RODRIGUEZ</td>
						</tr>
						<tr>
							<td colspan="3" style="text-align: center;">Síndico</td>
							<td colspan="1" >&nbsp;</td>
							<td colspan="5" style="text-align: center;">Tesorero Municipal</td>
							<td colspan="1" >&nbsp;</td>
							<td colspan="3" style="text-align: center;">Regidor de Hacienda</td>
						</tr>
					</tfoot>
				</table>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="inventario.imp.js"></script>
</html>