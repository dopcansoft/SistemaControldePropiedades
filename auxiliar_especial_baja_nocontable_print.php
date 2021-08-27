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
	$tesorero = new ResponsableDao();
	$regidor = new ResponsableDao();
	$sindico = new ResponsableDao();

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
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_BAJA;
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log, "", 0);	
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
    <style>
        
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

        body table.table-separator-bottom{
        	page-break-after: always;
        }

        body table.table-separator-bottom:last-child{
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
				<table style="background:#FFF !important; page-break-inside: avoid; " width="100%" class="table-fichas">
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
								<h3 style="text-align: center; font-weight: bold;"><small>BIENES INSERVIBLES Y OBSELETOS NO CONTABLES</small></h3>
							</th>
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
							<table width="100%">
								<thead>
									<tr class="subheader" style="background:#CCC !important;">
										<th colspan="10">
											<?=$header->cuentaContable." - ".$header->descr?>
										</th>
									</tr>
									<tr class="subheader" style="background:#DDD !important; ">
										<td rowspan="2" colspan="8">
                                            <?=$clasificacion->cuentaContable." - ".$clasificacion->descr?>
                                        </td>
                                        <td style="text-align: right;" width="10%">
                                            SUBTOTAL V.H.
                                        </td>
                                        <td style="text-align: right;" width="15%">
                                            SUBTOTAL V.A/A
                                        </td>
									</tr>
									<tr class="subheader" style="background:#DDD !important;">
										<td style="text-align: right;" width="10%">
											<?=number_format($content["anterior"],2,'.',',')?>
										</td>
										<td style="text-align: right;" width="15%">
											<?=number_format($content["costo"],2,'.',',')?>
										</td>
									</tr>
								</thead>
							</table>
							<table class="table-contenido table-separator-bottom">
								<thead style="background:#FFF !important;">
									<tr style="background:#A8A8A8 !important;">
										<th width="15%">FOLIO</th>
										<th>FOTOGRAFIA</th>
										<th width="24%">DESCRIPCIÓN</th>
										<th>MARCA</th>
										<th>MODELO</th>
										<th>SERIE</th>
										<th>FECHA ADQUISICIÓN/AVALUO</th>
										<th>ESTADO FÍSICO</th>
										<th>VALOR HISTORICO</th>
										<th>VALOR ACTUAL/AVALUO</th>
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
											<td width="24%"><?=strtoupper($bien->descripcion)?></td>
											<td><?=strtoupper($bien->marca)?></td>
											<td><?=strtoupper($bien->modelo)?></td>
											<td><?=strtoupper($bien->serie)?></td>
											<td><?
											if($bien->fechaAdquisicion!=""){
                                                $date = new DateTime($bien->fechaAdquisicion);
                                                echo $date->format('d-m-Y');                                                            
                                            }
											?></td>
											<td><?=strtoupper($bien->estadoFisico->descr)?></td>
											<td style="text-align: right;"><?=number_format($bien->valorAnterior,2,'.',',')?></td>
                                            <td style="text-align: right;"><?=number_format($bien->valor,2,'.',',')?></td>
										</tr>
										<?
										if($bien->notas!=""){
                                            ?>
                                            <tr>
                                                <td style="background: #EEE;" colspan="10"><strong>OBSERVACIONES: </strong>
                                                <span style="font-size: 8px !important; font-style: italic !important; padding-top: 4px; padding-bottom: 4px;"><?=strtoupper($bien->notas)?></span></td>
                                            </tr>
                                            <?
                                        }
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
				<table width="100%" class="no-cortes" style="page-break-before: avoid !important;">
					<tr>
						<td colspan="13" style="padding-top:10px !important;">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="13">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
						<td colspan="5" style="text-align: center !important; font-weight: bold !important;"><?=$presidente->titulo." ".$presidente->nombre." ".$presidente->apellido?></td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
						<td colspan="5" style="text-align: center !important;">Presidente Municipal</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="13">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="13">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: center; font-weight: bold !important;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
						<td colspan="1" width="5%" style="">&nbsp;</td>
						<td colspan="5" style="text-align: center; font-weight: bold !important;"><?=$tesorero->titulo." ".$tesorero->nombre." ".$tesorero->apellido?></td>
						<td colspan="1" width="5%" style="">&nbsp;</td>
						<td colspan="3" style="text-align: center; font-weight: bold !important;"><?=$regidor->titulo." ".$regidor->nombre." ".$regidor->apellido?></td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: center;">Síndico</td>
						<td colspan="1" >&nbsp;</td>
						<td colspan="5" style="text-align: center;">Tesorero Municipal</td>
						<td colspan="1" >&nbsp;</td>
						<td colspan="3" style="text-align: center;">Regidor de Hacienda</td>
					</tr>
				</table>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="res_cc.js"></script>
</html>