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
    
    $responsables = array();
    $sindico = new ResponsableDao();

    $data = strtoupper($_SERVER["REQUEST_METHOD"])=="GET"?$_GET:$_POST;
    $list = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$iddep = isset($data["departamento"])&&$data["departamento"]!=""?$data["departamento"]:"";
        	$tipo = isset($data["tipo"])&&$data["tipo"]!=""?$data["tipo"]:"";
        	$departamento = new DepartamentoDao(array("id"=>$iddep));
        	$departamento->find($db, $queries, $log); 
        	$inventarioFactory = new BienFactory();
        	$responsable = new ResponsableDao();
        	$responsable->findByDepartamento($departamento->id, $db, $queries, $log);
        	$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
        	
        	$sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);
        	
        	$list = $inventarioFactory->filtrado($empresa->id, $periodo->id, $tipo, "", "", "", "", $iddep, "", $estatusInventario, $db, $queries, $settings, $log);
        	
        	for($x=0;$x<count($list);$x++){        		
        		$existe = 0;
                if($list[$x]->responsable->id){
                    $log->debug($list[$x]->responsable);    
                }                
                foreach($responsables as $respon){
                    if($respon->id == $list[$x]->responsable->id){
                        $existe++;
                    }
                }
                if($existe<=0 && trim($list[$x]->responsable->id)!=""){
                    $responsables[] = $list[$x]->responsable;     
                }
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
    <style>
    	table.table-final{
    		page-break-after: always !important;
    	}
    </style>
</head>
<body>
	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?
                foreach($responsables as $resp){
                    ?>
					<table class="table-reporte">
						<thead>
							<tr>
								<th colspan="3" width="33%" style="text-align: center !important;">
									<img src="<?=$settings->prop("mpio.path.logos").$empresa->logoMpio?>">
								</th>
								<th colspan="4" width="33%" style="text-align: center !important;">
									<img src="<?=$settings->prop("mpio.path.logos").$empresa->logoPeriodo?>">
								</th>
								<th colspan="3" width="33%" style="text-align: center !important;">
									<img src="<?=$settings->prop("mpio.path.logos").$empresa->logoAyuto?>">
								</th>
							</tr>
							<tr class="subheader" style="background:#FFF !important;">
								<th colspan="10" style="text-align: center; font-weight: bold !important; font-size: 1.2em; margin-bottom: 10px; margin-top: 10px;"> RESGUARDO <?
								if($tipo==""){
									echo "GENERAL";
								}else if($tipo=="UTILITARIO"){
									echo "UTILITARIO";
								}else if($tipo=="CONTABLE"){
									echo "CONTABLE";
								}
								?></th>
							</tr>
							<tr class="subheader" style="background:#CCC !important;">
								<th colspan="10" style="text-align: left; font-weight: bold !important;">DEPARTAMENTO: <?=strtoupper($departamento->descr)?></th>
							</tr>
							<tr class="subheader" style="background:#CCC !important;">
								<th colspan="10" style="text-align: left; font-weight: bold !important;">DEPOSITARIO RESPONSABLE: <?=strtoupper($resp->titulo." ".$resp->nombre." ".$resp->apellido)?></th>
							</tr>
							<tr class="subheader" style="background:#CCC !important;">
								<th colspan="10" style="text-align: center; font-weight: bold !important;">&nbsp;</th>
							</tr>
							<tr style="background:#DDD !important;">
								<th width="12%" >FOLIO</th>
								<th>FOTOGRAFIA</th>
								<th width="24%">DESCRIPCIÓN</th>
								<th>MARCA</th>
								<th>MODELO</th>
								<th>SERIE</th>
								<th>MOTOR</th>
								<th>ORIGEN</th>
								<th>ESTADO FÍSICO</th>
								<th>VALOR</th>
							</tr>
						</thead>
						<tbody>
							<?
							if(isset($list) && count($list)>0){
								foreach($list as $bien){
									if($bien->responsable->id == $resp->id){
										?>
										<tr>
											<td><?=$bien->folio?></td>
											<td><img src="<?=$bien->imagen?>" width="75"></td>
											<td width="24%"><?=$bien->descripcion?></td>
											<td><?=$bien->marca?></td>
											<td><?=$bien->modelo?></td>
											<td><?=$bien->serie?></td>
											<td><?=$bien->motor?></td>
											<td><?=$bien->origen->descr?></td>
											<td><?=$bien->estadoFisico->descr?></td>
											<td style="text-align: right !important;"><?=number_format($bien->valor,2,'.',',')?></td>
										</tr>
										<?
									}
								}
							}
							?>						
						</tbody>
					</table>
					<table class="table-reporte table-final">
						<tbody>
							<tr>
								<td colspan="10" style="font-size:1.2em;">
								Sirva éste como comprobante de materiales, herramientas y/o equipo Así mismo acepto y
	me comprometo a:<br><br>
	Cuidar del equipo asignado, ya que es una herramienta de trabajo y utilizarla única y
	exclusivamente dentro de mis responsabilidades dentro de este H. Ayuntamiento.<br><br>
	Reportar de manera inmediata el equipo cuando requiera servicio o en su caso, cambio
	conforme a los supuestos del artículo 107, 108 y 109 de la Ley No. 539 de Adquisiciones,
	Arrendamientos, Administración y Enajenación de Bienes Muebles del Estado de
	Veracruz de Ignacio de la llave.<br><br>
	En caso de robo o extravío del equipo lo reportaré de manera inmediata para evitar se
	haga mal uso del mismo conforme al artículo 106 de la Ley No. 539 de Adquisiciones,
	Arrendamientos, Administración y Enajenación de Bienes Muebles del Estado de
	Veracruz de Ignacio de la llave.<br><br>
	Esto no me exime de mi responsabilidad y estoy de acuerdo en reponer el bien por uno
	igual o de mismas características conforme al artículo 93 de la Ley N° 539 de
	Adquisiciones, Arrendamientos, Administración y Enajenación de Bienes Muebles del
	Estado de Veracruz de Ignacio de la llave.<br><br>
	<strong>“Los servidores públicos que tengan bienes muebles bajo su custodia, resguardo o uso
	derivado, serán responsables de su cuidado y, en su caso, de su reposición y del
	resarcimiento de los daños y perjuicios causados, independientemente de las
	responsabilidades a que haya lugar”.</strong><br><br>
	En caso de dejar de prestar mis servicios al H. Ayuntamiento, devolveré el equipo
	completo (partes y accesorios), en las condiciones en que me fue entregado,
	considerando el uso normal del equipo.<br><br></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
                                <th colspan="10">
                                    <table width="100%">
                                        <tr>
                                            <td rowspan="2" style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                            <td rowspan="2" width="5%">&nbsp;</td>
                                            <td rowspan="2" style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                            <td rowspan="2" width="5%">&nbsp;</td>
                                            <td rowspan="2" style="border-bottom:1px solid #000; text-align: center !important;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center !important;"><?=$sindico->titulo." ".$sindico->nombre." ".$sindico->apellido?></td>
                                            <td width="5%">&nbsp;</td>
                                            <td style="text-align: center !important;"><?=$responsable->titulo." ".$responsable->nombre." ".$responsable->apellido?></td>
                                            <td width="5%">&nbsp;</td>
                                            <td style="text-align: center !important;"><?=$resp->titulo." ".$resp->nombre." ".$resp->apellido?></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center !important;">SINDICO</td>
                                            <td>&nbsp;</td>
                                            <td style="text-align: center !important;">ENCARGADO DEL ÁREA</td>
                                            <td>&nbsp;</td>
                                            <td style="text-align: center !important;">RESGUARDATARIO</td>
                                        </tr>
                                    </table>
                                </th>
                            </tr>	
						</tfoot>
					</table>
				<?
				}
				?>		
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="rdt.js"></script>
</html>