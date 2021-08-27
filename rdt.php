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

    $presidente = new ResponsableDao();
	$tesorero = new ResponsableDao();
	$regidor = new ResponsableDao();
	$sindico = new ResponsableDao();

    $list = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$iddep = isset($data["departamento"])&&$data["departamento"]!=""?$data["departamento"]:"";
        	$tipo = isset($data["tipo"])&&$data["tipo"]!=""?$data["tipo"]:"";
        	$departamento = new DepartamentoDao(array("id"=>$iddep));
        	$departamento->find($db, $queries, $log); 
        	$inventarioFactory = new BienFactory();
        	
        	$presidente->findByCargo($empresa->id, Responsable::CARGO_PRESIDENTE_MUNICIPAL, $db, $queries, $log);
			$tesorero->findByCargo($empresa->id, Responsable::CARGO_TESORERO, $db, $queries, $log);
			$regidor->findByCargo($empresa->id, Responsable::CARGO_REGIDOR, $db, $queries, $log);
			$sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);

        	$responsable = new ResponsableDao();
        	$responsable->findByDepartamento($departamento->id, $periodo->id, $db, $queries, $log);
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipo, "", "", "", "", $iddep, "", $estatusInventario, $db, $queries, $settings, $log);
        	
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
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<table class="table-reporte">
					<thead>
						<tr>
							<th colspan="4" width="33%" style="text-align: center !important;">
								<img style="max-width: 90px; max-height: 90px !important;" src="<?=$settings->prop("mpio.path.logos").$empresa->logoMpio?>">
							</th>
							<th colspan="5" width="33%" style="text-align: center !important;">
								<img width="60%" src="<?=$settings->prop("mpio.path.logos").$empresa->logoPeriodo?>">
							</th>
							<th colspan="4" width="33%" style="text-align: center !important;">
								<img style="max-width: 90px; max-height: 90px !important;" src="<?=$settings->prop("mpio.path.logos").$empresa->logoAyuto?>">
							</th>
						</tr>
						<tr class="subheader" style="background:#FFF !important;">
							<th colspan="13" style="text-align: center; font-weight: bold !important; font-size: 1.2em; margin-bottom: 10px; margin-top: 10px;"> RESGUARDO <?
							if($tipo==""){
								echo "GENERAL DE BIENES MUEBLES (CONTABLES Y DE CONTROL INTERNO)";
							}else if($tipo=="UTILITARIO"){
								echo "DE BIENES MUEBLES DE CONTROL INTERNO (MENORES A 70 UMAS)";
							}else if($tipo=="CONTABLE"){
								echo "DE BIENES MUEBLES CONTABLES";
							}
							?></th>
						</tr>
						<tr class="subheader" style="background:#CCC !important;">
							<th colspan="13" style="text-align: center; font-weight: bold !important;">DEPARTAMENTO: <?=strtoupper($departamento->descr)?></th>
						</tr>
						<tr style="background:#DDD !important;">
							<th width="12%">FOLIO ÚNICO</th>
							<th width="12%">FOLIO ARMONIZADO</th>
							<th>FOTOGRAFIA</th>
							<th width="24%">DESCRIPCIÓN</th>
							<th>COLOR</th>
							<th>MARCA</th>
							<th>MODELO</th>
							<th>SERIE</th>
							<th>MOTOR</th>
							<th>ORIGEN</th>
							<th>ESTADO FÍSICO</th>
							<th>VALOR HISTORICO</th>
							<th>VALOR ACTUAL/AVALUO</th>
						</tr>
					</thead>
					<tbody>
						<?
						if(isset($list) && count($list)>0){
							$x=1;
							foreach($list as $bien){
								?>
								<tr>
									<td style="text-align:center !important;"><?=$bien->folioUnico?></td>
									<td><?=$bien->folio?></td>
									<td><img src="<?=$bien->imagen?>" width="120"></td>
									<td width="24%"><?=strtoupper($bien->descripcion)?></td>
									<td style="text-align: center;"><?=$bien->color->descr!=""?strtoupper($bien->color->descr):'--'?></td>
                                    <td><?=strtoupper($bien->marca)?></td>
									<td><?=strtoupper($bien->modelo)?></td>
									<td><?=strtoupper($bien->serie)?></td>
									<td><?=strtoupper($bien->motor)?></td>
									<td><?=strtoupper($bien->origen->descr)?></td>
									<td><?=strtoupper($bien->estadoFisico->descr)?></td>
									<td style="text-align: right !important;"><?=number_format($bien->valorAnterior,2,'.',',')?></td>
									<td style="text-align: right !important;"><?=number_format($bien->valor,2,'.',',')?></td>
								</tr>
								<?
								if($bien->notas!=""){
                                    ?>
                                    <tr>
                                        <td style="background: #EEE;" colspan="12"><strong>OBSERVACIONES: </strong>
                                        <span style="font-size: 8px !important; font-style: italic !important;"><?=strtoupper($bien->notas)?></span></td>
                                    </tr>
                                    <?    
                                }
								$x++;
							}
						}
						?>						
					</tbody>
				</table>
				<table class="table-reporte">
					<tbody>
						<tr>
							<td colspan="11" style="font-size:1.2em;">
							SIRVA ÉSTE COMO COMPROBANTE DE MATERIALES, HERRAMIENTAS Y/O EQUIPO ASÍ MISMO ACEPTO Y
ME COMPROMETO A:<br><br>
CUIDAR DEL EQUIPO ASIGNADO, YA QUE ES UNA HERRAMIENTA DE TRABAJO PARA USO EXCLUSIVO DE MIS RESPONSABILIDADES DENTRO DE ESTE H. AYUNTAMIENTO.<br><br>
REPORTAR DE MANERA INMEDIATA EL EQUIPO CUANDO REQUIERA SERVICIO O EN SU CASO, CAMBIO
CONFORME A LOS SUPUESTOS DEL ARTÍCULO 107, 108 Y 109 DE LA LEY NO. 539 DE ADQUISICIONES,
ARRENDAMIENTOS, ADMINISTRACIÓN Y ENAJENACIÓN DE BIENES MUEBLES DEL ESTADO DE
VERACRUZ DE IGNACIO DE LA LLAVE.<br><br>
EN CASO DE ROBO O EXTRAVÍO DEL EQUIPO LO REPORTARÉ DE MANERA INMEDIATA PARA EVITAR SE
HAGA MAL USO DEL MISMO CONFORME AL ARTÍCULO 106 DE LA LEY NO. 539 DE ADQUISICIONES,
ARRENDAMIENTOS, ADMINISTRACIÓN Y ENAJENACIÓN DE BIENES MUEBLES DEL ESTADO DE
VERACRUZ DE IGNACIO DE LA LLAVE.<br><br>
ESTO NO ME EXIME DE MI RESPONSABILIDAD Y ESTOY DE ACUERDO EN REPONER EL BIEN POR UNO
IGUAL O DE MISMAS CARACTERÍSTICAS CONFORME AL ARTÍCULO 93 DE LA LEY N° 539 DE
ADQUISICIONES, ARRENDAMIENTOS, ADMINISTRACIÓN Y ENAJENACIÓN DE BIENES MUEBLES DEL
ESTADO DE VERACRUZ DE IGNACIO DE LA LLAVE.<br><br>
<STRONG>“LOS SERVIDORES PÚBLICOS QUE TENGAN BIENES MUEBLES BAJO SU CUSTODIA, RESGUARDO O USO
DERIVADO, SERÁN RESPONSABLES DE SU CUIDADO Y, EN SU CASO, DE SU REPOSICIÓN Y DEL
RESARCIMIENTO DE LOS DAÑOS Y PERJUICIOS CAUSADOS, INDEPENDIENTEMENTE DE LAS
RESPONSABILIDADES A QUE HAYA LUGAR”.</STRONG><br><br>
EN CASO DE DEJAR DE PRESTAR MIS SERVICIOS AL H. AYUNTAMIENTO, DEVOLVERÉ EL EQUIPO
COMPLETO (PARTES Y ACCESORIOS), EN LAS CONDICIONES EN QUE ME FUE ENTREGADO,
CONSIDERANDO EL USO NORMAL DEL EQUIPO.<br><br></td>
						</tr>
					</tbody>
					<tfoot>
						<?
							if($tipo==""){
								?>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=(isset($responsable->titulo)&&$responsable->titulo!=""?$responsable->titulo." ":"").$responsable->nombre." ".$responsable->apellido?></td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">SÍNDICO</td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">RESPONSABLE DEL DEPARTAMENTO</td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
								<?
							}else if($tipo=="CONTABLE"){
								?>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$presidente->titulo." ".$presidente->nombre." ".$presidente->apellido?></td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=(isset($responsable->titulo)&&$responsable->titulo!=""?$responsable->titulo." ":"").$responsable->nombre." ".$responsable->apellido?></td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">PRESIDENTE MUNICIPAL</td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">RESPONSABLE DEL DEPARTAMENTO</td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$tesorero->titulo." ".$tesorero->nombre." ".$tesorero->apellido?></td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="2" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$regidor->titulo." ".$regidor->nombre." ".$regidor->apellido?></td>
									</tr>
									<tr>
										<td colspan="2" style="text-align: center;">SÍNDICO</td>
										<td colspan="1" >&nbsp;</td>
										<td colspan="3" style="text-align: center;">TESORERO MUNICIPAL</td>
										<td colspan="1" >&nbsp;</td>
										<td colspan="2" style="text-align: center;">REGIDOR</td>
									</tr>
								<?
							}else if($tipo=="UTILITARIO"){
								?>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="10">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=(isset($responsable->titulo)&&$responsable->titulo!=""?$responsable->titulo." ":"").$responsable->nombre." ".$responsable->apellido?></td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">SÍNDICO</td>
										<td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
										<td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">RESPONSABLE DEL DEPARTAMENTO</td>
										<td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
									</tr>
								<?
							}else{
								echo "NO ESPECIFICADO";
							}
							?></td>
						</tr>
					</tfoot>
				</table>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="rdt.js"></script>
</html>