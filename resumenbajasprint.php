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
			/*$dirJuridico->findByCargo($empresa->id, Responsable::CARGO_DIR_JURIDICO, $db, $queries, $log);
			$organoCtrlInt->findByCargo($empresa->id, Responsable::CARGO_ORG_CTRL_INT, $db, $queries, $log);
			$jefeAdquisiciones->findByCargo($empresa->id, Responsable::CARGO_JEFE_ADQUISICIONES, $db, $queries, $log);*/
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

    <!-- <link href="css/articulo.css" rel="stylesheet"> -->
    <title></title>
    <style>
    	table.table-fichas:first-child{
            page-break-before: avoid !important;
        }
        table.table-fichas{
            page-break-before: always !important;
        }

        table.table-contenido{
			/*margin-top: 30px !important;
			max-width: 120px !important;
			font-size:.75em !important; */
			color: #3B3B3B;
			border:1px solid #DDD;
		}

		table.table-contenido thead{
			/*display: table-header-group !important;*/
		}

		table.table-contenido thead tr th{
			font-weight: bold;
			background-color: #A8A8A8;
			color: #000;
			font-size: .9;
			padding: 5px;
		}

		table.table-contenido tbody tr{
			border-bottom:1px solid #DDD;
		}

		table.table-contenido tbody tr td{
			border-right: 1px solid #DDD;
			padding: 5px;
		}

		table.table-contenido tbody tr td:last-child{
			border-right: 0;
		}

		table.table-contenido tr.subheader{
			line-height: 1.2em;
			font-weight: 600;
			font-size: .9;
			background-color: #DDD;
			color: #000 !important;
		}

		table.table-contenido tr.subheader td{
			padding-top: 10px;
			padding-bottom: 10px;
			padding-left: 10px;
			padding-right: 10px;
		}

		table.table-contenido{
			background-color: #FFF;
			/*page-break-inside: avoid;*/
			width: 100%;
		}

		table.table-contenido tbody tr td{
			font-size: .8em !important;
		}
          
        table.table-contenido{
            page-break-before: avoid !important;
        }  

        table.table-separator-bottom{
        	page-break-after: always;
        }

        table.table-separator-bottom:last-child{
        	page-break-after: avoid !important;
        }	
    </style>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
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
						<th colspan="3">
							<h3 style="text-align:center; font-weight: 600; font-size: 18px;">REPORTE DE BAJAS</h3>
						</th>
					</tr>
				</thead>
			</table>
			</table>
				<?
				foreach($list as $data){
					$header = $data["header"];
					if(count($data["content"])>0){
						foreach($data["content"] as $content){
							$clasificacion = $content["clasificacion"];
							?>
							<table width="100%">
								<thead>
									<tr class="subheader" style="background:#CCC !important;">
										<th colspan="9">
											<?=$header->cuentaContable." - ".$header->descr?>
										</th>
										<th style="text-align: right;" width="10%">
											TOTAL
										</th>
										<td style="text-align: right;" width="10%">
                                            <?=number_format($data["anterior"],2,'.',',')?>
                                        </td>
										<th style="text-align: right;" width="10%">
											<?=number_format($data["costo"],2,'.',',')?>
										</th>
									</tr>
									<tr class="subheader" style="background:#DDD !important;">
										<th colspan="9">
											<?=$clasificacion->cuentaContable." - ".$clasificacion->descr?>
										</th>
										<th style="text-align: right;" width="10%">
											TOTAL
										</th>
										<td style="text-align: right;" width="10%">
                                            <?=number_format($content["anterior"],2,'.',',')?>
                                        </td>
										<th style="text-align: right;" width="10%">
											<?=number_format($content["costo"],2,'.',',')?>
										</th>
									</tr>
								</thead>
							</table>
							<table class="table-separator-bottom table-contenido">
								<thead>
									<tr style="background:#A8A8A8 !important;">
										<th width="25%">FOLIO</th>
										<th>FOTOGRAFIA</th>
										<th width="24%">DESCRIPCIÓN</th>
										<th>DEPARTAMENTO</th>
										<th>MARCA</th>
										<th>MODELO</th>
										<th>SERIE</th>
										<th>MOTOR</th>
										<th>FONDNO<br>ORIGEN</th>
										<th>ESTADO FÍSICO</th>
										<th>VALOR DE HISTORICO</th>
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
											<td><? if($bien->imagen!=""){ ?><img src="<?=$settings->prop("system.url").$bien->imagen?>" width="105"><? }else{ echo "&nbsp;"; } ?></td>
											<td width="24%"><?=strtoupper($bien->descripcion)?></td>
											<td><?=strtoupper($bien->departamento->descr)?></td>
											<td><?=strtoupper($bien->marca)?></td>
											<td><?=strtoupper($bien->modelo)?></td>
											<td><?=strtoupper($bien->serie)?></td>
											<td><?=strtoupper($bien->motor)?></td>
											<td><?=strtoupper($bien->origen->descr)?></td>
											<td><?=strtoupper($bien->estadoFisico->descr)?></td>
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
				<table width="100%" class="footer-bajas" style="width:100% !important; background:#FFF !important;">
					<tbody>
						<tr>
							<td width="41.65%" colspan="5"><?=$presidente->titulo." ".$presidente->nombre." ".$presidente->apellido?></td>
							<td width="16.66%" colspan="2">&nbsp;</td>
							<td width="41.65%" colspan="5"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
						</tr>
						<tr>
							<td width="41.65%" colspan="5">Presidente</td>
							<td width="16.66%" colspan="2">&nbsp;</td>
							<td width="41.65%" colspan="5">Sindico</td>
						</tr>
						<tr>
							<td width="100%" colspan="12">&nbsp;</td>
						</tr>
						<tr>
							<td width="41.65%" colspan="5"><?=$regidor->titulo." ".$regidor->nombre." ".$regidor->apellido?></td>
							<td width="16.66%" colspan="2">&nbsp;</td>
							<td width="41.65%" colspan="5"><?=$tesorero->titulo." ".$tesorero->nombre." ".$tesorero->apellido?></td>
						</tr>
						<tr>
							<td width="41.65%" colspan="5">Regidor</td>
							<td width="16.66%" colspan="2">&nbsp;</td>
							<td width="41.65%" colspan="5">Tesorero</td>
						</tr>
						<tr>
							<td width="100%" colspan="12">&nbsp;</td>
						</tr>
					</tbody>
				</table>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="resumenbajasprint.js"></script>
</html>