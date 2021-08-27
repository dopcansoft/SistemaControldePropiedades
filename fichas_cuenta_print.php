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

    $req = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;

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
        	$tipoInventario = "CONTABLE";
        	$catalogoFactory = new CatalogoFactory();
        	$clasificacionBM = $catalogoFactory->listado($db, $queries->prop("catbienesmuebles.list"), $settings, $log, "Clasificacion");
        	$clasificacionBI = $catalogoFactory->listado($db, $queries->prop("catbienesinmuebles.list"), $settings, $log, "Clasificacion");
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipoInventario, "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		for($x=0;$x<count($list);$x++){        		
        		$list[$x]->imagen = isset($list[$x]->imagen)&&$list[$x]->imagen!=null&&$list[$x]->imagen!=""?$settings->prop("system.url").$list[$x]->imagen:"";
        	}
    		$list = $inventarioFactory->groupByClasificacion($clasificacionBM, $items);
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

    $header = "header-naranja";
    if(isset($req["c"])){
        switch($req["c"]){
            case 1:
                $header = "header-naranja";
            break;
            case 2:
                $header = "header-azul";
            break;
            case 3:
                $header = "header-amarillo";
            break;
        }
    }
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
        table.table-fichas:first-child{
            page-break-before: avoid !important;
        }
        table.table-fichas{
            page-break-before: always !important;
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
				<?
				foreach($list as $data){
					$cbm = $data["clasificacion"];
					$items = $data["bienes"];
					if(isset($items) && count($items)>0){
						?>
						<table width="100%" class="table-fichas">
							<thead style="display: table-row !important; min-width: 100% !important;">
								<tr style="background:#DDD; color:#000; padding:5px; font-weight: bold; margin-bottom: 5px;">
									<td colspan="7" style="padding:5px;">
										<?=$cbm->cuentaContable." - ".$cbm->descr?>		
									</td>
									<td colspan="3" style="text-align: right; padding:5px;">
										TOTAL
									</td>
									<td style="text-align: right; padding:5px;">
										<?=number_format($data["total"],2,'.',',')?>
									</td>
								</tr>
                                <tr style="background:#FFF;">
                                    <td colspan="11">&nbsp;</td>
                                </tr>
							</thead>
							<tbody>
								<?
								$i=1;
								foreach($items as $item){									
									?>
									<tr>
										<td colspan="11">
                                            <!-- FICHA -->
                                            <table class="cedula">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="6" class="titulo">CÉDULA DE REGISTRO</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="header <?=$header?>" style="width: 20% !important;">FOLIO</td>
                                                        <td class="header <?=$header?>" style="width: 35% !important;">DESCRIPCIÓN Y NO. DE INVENTARIO</td>
                                                        <td class="header <?=$header?>" style="width: 15% !important;">NO. DE SERIE</td>
                                                        <td class="header <?=$header?>" style="width: 10% !important;">VALOR HISTORICO</td>
                                                        <td class="header <?=$header?>" style="width: 20% !important;">REGISTRO FOTOGRÁFICO</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?=$item->folio?></td>
                                                        <td><?=strtoupper($item->getShortName(80))?></td>
                                                        <td><?=strtoupper($item->serie)?></td>
                                                        <td <?=$item->tipoValuacion->id==1?'rowspan="3"':'rowspan="2"'?> ><?="$ ".number_format($item->valorAnterior,2,'.',',')?></td>
                                                        <td rowspan="9" style="text-align: center; vertical-align: middle !important;"><img src="<?=$item->imagen?>" style="width: 90%; max-width:120px; margin: 2px; max-height: 140px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="header <?=$header?>">MARCA</td>
                                                        <td class="header <?=$header?>">MODELO</td>
                                                        <td class="header <?=$header?>">FACTURA</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?=strtoupper($item->marca)?></td>
                                                        <td><?=strtoupper($item->modelo)?></td>
                                                        <td><?=strtoupper($item->factura)?></td>
                                                        <?
                                                        if($item->tipoValuacion->id>1){
                                                            ?>
                                                            <td class="header <?=$header?>">AVALUO</td>    
                                                            <?
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <td class="header <?=$header?>">AREA DE ADSCRIPCION</td>
                                                        <td colspan="2" class="header <?=$header?>">CONDICIONES DEL BIEN</td>
                                                        <?
                                                        if($item->tipoValuacion->id>1){
                                                            ?>
                                                            <td rowspan="2"><?="$ ".number_format($item->valor,2,'.',',')?></td>    
                                                            <?
                                                        }else{
                                                            ?>
                                                            <td class="header <?=$header?>">VALOR EN LIBROS</td>    
                                                            <?
                                                        }
                                                        ?>                                                                    
                                                    </tr>
                                                    <tr>
                                                        <td><?=strtoupper($item->departamento->descr)?></td>
                                                        <td colspan="2"><?=$item->estadoFisico->descr?></td>
                                                        <?
                                                        if($item->tipoValuacion->id==1){
                                                            ?>
                                                            <td rowspan="3"><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>    
                                                            <?
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <td class="header <?=$header?>">
                                                            <?=$item->tipoValuacion->id==1?'FECHA DE ADQUISICIÓN':'FECHA DE AVALUO'?>                                            
                                                        </td>
                                                        <td colspan="2" class="header <?=$header?>">OBSERVACIONES</td>
                                                        <?
                                                        if($item->tipoValuacion->id>1){
                                                            ?>
                                                            <td class="header <?=$header?>">VALOR EN LIBROS</td>    
                                                            <?
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <td><?
                                                            $date = new DateTime($item->fechaAdquisicion);
                                                            echo $date->format('d-m-Y');
                                                        ?></td>
                                                        <td colspan="2"><?=strtoupper($item->notas)?></td>
                                                        <?
                                                        if($item->tipoValuacion->id>1){
                                                            ?>
                                                            <td><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>    
                                                            <?
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <td rowspan="2">CÓDIGO</td>
                                                        <td colspan="3" style="padding-top: 7px; padding-bottom: 7px;">
                                                            <!-- <img width="240px" src="src/lib/phpbarcode/barcode.php?text=<?=$item->empresa->id.';'.$item->periodo->id.';'.$item->id?>&size=20&print=false"> -->
                                                            <img width="240px" src="files/barcodes/<?=$item->id.".png"?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><?=$item->folio?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- TERMINA FICHA -->    
                                        </td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
							<tfoot></tfoot>
						</table>	
						<?
					}
				}
				?>	
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
<script type="text/javascript" src="rcac.js"></script>
</html>