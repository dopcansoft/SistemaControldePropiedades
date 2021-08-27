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
    
    $presidente = new ResponsableDao();
	$tesorero = new ResponsableDao();
	$regidor = new ResponsableDao();
	$sindico = new ResponsableDao();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "ClasificacionInmueble");
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		$inmuebleFactory = new BienInmuebleFactory();
            $inmuebles = $inmuebleFactory->filtrado($empresa, new ClasificacionInmueble(), new ClasificacionInmueble(), $db, $queries, $log);
            
    		for($x=0;$x<count($list);$x++){        		
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}
    		$list = $inventarioFactory->groupByClasificacion($clasificacionBM, $items);
    		$list2 = $inventarioFactory->groupByClasificacion($clasificacionBI, $inmuebles);
            $list = array_merge($list, $list2);
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
				<table width="100%" class="table-reporte">
					<thead>
						<tr>
							<th colspan="5">
								<table width="100%">
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
						<tr>
							<th colspan="5" style="text-align: center !important;">
								<h3><small>Reporte General del Inventario de Bienes Muebles e Inmuebles al 31 de diciembre de 2019</small></h3>
							</th>
						</tr>
						<tr style="background:#DDD !important;">
							<th>CLAVE</th>
							<th>CUENTA</th>
							<td>VALOR HISTORICO</td>
                            <td>VALOR ACTUAL/AVALUO</td>
							<td>NO. BIENES</td>
						</tr>							
					</thead>	
					<tbody>
						<?
						$totalbienes = 0;
                        $grantotal = 0;
						$grantotalAnterior = 0;
						foreach($list as $data){
							$grantotal = $grantotal+$data["total"];
							$grantotalAnterior = $grantotalAnterior + $data["anterior"];	
						}
						foreach($list as $data){
							$cbm = $data["clasificacion"];
							$items = $data["bienes"];
							$totalbienes = $totalbienes+count($items);                                        
							if(isset($items) && count($items)>0){
								?>
								<tr>
                                    <td>
                                        <?
                                        if(is_a($cbm, 'Clasificacion')){
                                            ?>
                                            <?=$cbm->grupo.".".$cbm->subgrupo.".".$cbm->clase.".".$cbm->subclase?>
                                            <?
                                        }else{
                                            echo $cbm->clasificador;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?=$cbm->descr?>        
                                    </td>
                                    <td style="text-align: right;">
                                        <?="$ ".number_format($data["anterior"],2,'.',',')?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?=number_format($data["total"],2,'.',',')?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?=count($data["bienes"])?>
                                    </td>
                                </tr>
								<?
							}
						}
						?>
						<tr>
                            <td colspan="2" style="text-align: left !important;"><strong>GRANTOTAL</strong></td>
                            <td style="text-align: right !important;"><strong><?=number_format($grantotalAnterior,0,'.',',')?></strong></td>
                            <td style="text-align: right !important;"><strong><?=number_format($grantotal,2,'.',',')?></strong></td>
                        	<td style="text-align: right !important;"><strong><?=number_format($totalbienes,0,'.',',')?></strong></td>
                        </tr>
					</tbody>
				</table>								
				<table width="100%" style="border:0 !important;">
					<tbody>
						<tr>
							<td colspan="11" style="padding-top:25px !important;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="11">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
							<td colspan="3" style="text-align: center !important; font-weight: bold !important;"><?=$presidente->titulo." ".$presidente->nombre." ".$presidente->apellido?></td>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
							<td colspan="3" style="text-align: center !important;">Presidente Municipal</td>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="11">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="11">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="1" width="5%">&nbsp;</td>
							<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
							<td colspan="1" width="5%">&nbsp;</td>
							<td colspan="3" style="text-align: center; font-weight: bold !important;"><?=$tesorero->titulo." ".$tesorero->nombre." ".$tesorero->apellido?></td>
							<td colspan="1" width="5%">&nbsp;</td>
							<td colspan="2" style="text-align: center; font-weight: bold !important;"><?=$regidor->titulo." ".$regidor->nombre." ".$regidor->apellido?></td>
							<td colspan="1" width="5%">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="1" width="5%">&nbsp;</td>
							<td colspan="2" style="text-align: center;">Síndico</td>
							<td colspan="1" width="5%">&nbsp;</td>
							<td colspan="3" style="text-align: center;">Tesorero Municipal</td>
							<td colspan="1" width="5%" >&nbsp;</td>
							<td colspan="2" style="text-align: center;">Regidor de Hacienda</td>
							<td colspan="1" width="5%">&nbsp;</td>
						</tr>
					</tbody>
				</table>	
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="balanzares_print.js"></script>
</html>