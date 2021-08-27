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
    $bien = new BienDao();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	if(isset($data["id"])){
                $bien->id = isset($data["id"])?$data["id"]:"";
                $bien->periodo->id = $periodo->id;
                $bien->find($db, $queries, $log);
            }
            $list = array($bien);
        }else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;

    $header = "header-naranja";
    if(isset($data["c"])){
        switch($data["c"]){
            case 1:
                $header = "header-naranja";
            break;
            case 2:
                $header = "header-azul";
            break;
            case 3:
                $header = "header-amarillo";
            break;
            case 4:
                $header = "header-gris";
            break;
        }
    }
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
  </head>
  <body style="background:#FFF !important;">
  	<div class="content-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
			    <table style="margin-bottom:20px !important;" width="100%">
					<thead>
						<tr>
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
						</tr>
			    	</thead>
			    </table>
			    <?
				foreach($list as $item){
					if($empresa->tipoEtiqueta==Empresa::TIPO_ETIQUETA_QR){
                        ?>
                        <!-- FICHAS CON QR's -->
                        <table class="cedula">
                            <tbody>
                              <tr>
                                <td width="80%" colspan="5" class="titulo">CÉDULA DE REGISTRO</td>
                                <td width="20%" class="titulo">CONTABLE</td>    
                              </tr>
                              <tr>
                                <td width="20%" class="header <?=$header?>">FOLIO ÚNICO</td>
                                <td width="18%"><?=$item->folioUnico?></td>
                                <td width="18%" class="header <?=$header?>">FOLIO ANTERIOR</td>
                                <td width="10%"><?=isset($item->folioAnterior)&&$item->folioAnterior!=""?$item->folioAnterior:" -- "?></td>
                                <td width="14%" class="header <?=$header?>">FOLIO ARMONIZADO</td>
                                <td><?=$item->folio?></td>
                              </tr>
                              <tr>
                                <td class="header <?=$header?>">DESCRICIÓN Y NO. DE INVENTARIO</td>
                                <td class="header <?=$header?>">COLOR</td>
                                <td class="header <?=$header?>">MARCA</td>
                                <td class="header <?=$header?>" colspan="2">VALOR HISTORICO</td>
                                <td class="header <?=$header?>">EVIDENCIA FOTOGRAFICA</td>
                              </tr>
                              <tr>
                                <td><?=strtoupper($item->descripcion)?></td>
                                <td><?=strtoupper($item->color->descr)?></td>
                                <td><?=strtoupper($item->marca)?></td>
                                <td colspan="2" rowspan="2"><?="$ ".number_format($item->valor,2,'.',',')?></td>
                                <td rowspan="9"><img src="<?=$item->imagen?>" width="145px"></td>
                              </tr>
                              <tr>
                                <td class="header <?=$header?>">MODELO</td>
                                <td class="header <?=$header?>">NO. SERIE</td>
                                <td class="header <?=$header?>">NO. FACTURA</td>
                              </tr>
                              <tr>
                                <td><?=strtoupper($item->modelo)?></td>
                                <td><?=strtoupper($item->serie)?></td>
                                <td><?=strtoupper($item->factura)?></td>
                                <td colspan="2" class="header <?=$header?>">AVALUO</td>
                              </tr>
                              <tr>
                                <td class="header <?=$header?>">ÁREA DE ADSCRIPCIÓN</td>
                                <td colspan="2" class="header <?=$header?>">ESTADO FISICO</td>
                                <td colspan="2" rowspan="2"><?="$ ".number_format($item->valor,2,'.',',')?></td>
                              </tr>
                              <tr>
                                <td><?=strtoupper($item->departamento->descr)?></td>
                                <td colspan="2"><?=strtoupper($item->estadoFisico->descr)?></td>
                              </tr>
                              <tr>
                                <td class="header <?=$header?>">FECHA DE ADQUISICIÓN HISTORICA</td>
                                <td class="header <?=$header?>">FECHA DE VALUACIÓN</td>
                                <td class="header <?=$header?>">FECHA DE CORTE DE INVENTARIO</td>
                                <td class="header <?=$header?>" colspan="2">VALOR EN LIBROS</td>
                              </tr>
                              <tr>
                                <td><?
                                  $date = new DateTime($item->fechaAdquisicion);
                                  echo $date->format('d-m-Y');
                                ?></td>
                                <td><?=$date->format('d-m-Y')?></td>
                                <td>31-12-2021</td>
                                <td colspan="2"><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>
                              </tr>
                              <tr>
                                <td rowspan="2"><img src="<?=$settings->prop("system.url").$settings->prop("qrcode.path").$item->id.".png"?>" width="145px"></td>
                                <td colspan="4" class="header <?=$header?>">NOTAS</td>
                              </tr>
                              <tr>
                                <td rowspan="2" colspan="4"><?=isset($item->notas)&&$item->notas!=""?$item->notas:"&nbsp;"?></td>
                              </tr>
                            </tbody>  
                        </table>
                        <?
                    }else{
                        ?>
    					<table class="cedula">
    						<tbody>
    							<tr>
    								<td colspan="7" class="titulo">RESGUARDO - CÉDULA DE REGISTRO</td>
    							</tr>
    							<tr>
    								<td class="header <?=$header?>">NO. DE INVENTARIO</td>
    								<td class="header <?=$header?>">DESCRIPCIÓN</td>
    								<td class="header <?=$header?>">NO. DE SERIE</td>
    								<td class="header <?=$header?>">VALOR HISTORICO</td>
    								<td class="header <?=$header?>">VALOR EN LIBROS</td>
    								<td class="header <?=$header?>">REGISTRO FOTOGRÁFICO</td>
    							</tr>
    							<tr>
    								<td><?=$item->folio?></td>
    								<td><?=$item->getShortName(24)?></td>
    								<td><?=$item->serie?></td>
    								<td rowspan="7"><?="$ ".number_format($item->valor,2,'.',',')?></td>
    								<td rowspan="7"><?="$ ".number_format($item->valor-$item->depreciacionAcumulada,2,'.',',')?></td>
    								<td rowspan="7"><img src="<?=$item->imagen?>" width="145px"></td>
    							</tr>
    							<tr>
    								<td class="header <?=$header?>">MARCA</td>
    								<td class="header <?=$header?>">MODELO</td>
                                    <td class="header <?=$header?>">FACTURA</td>
    							</tr>
    							<tr>
    								<td><?=$item->marca?></td>
    								<td><?=$item->modelo?></td>
                                    <td><?=$item->factura?></td>
    							</tr>
    							<tr>
    								<td class="header <?=$header?>">ÁREA DE ADSCRIPCIÓN</td>
    								<td colspan="2" class="header <?=$header?>">CONDICIONES DEL BIEN</td>
    							</tr>
    							<tr>
    								<td><?=strtoupper($item->departamento->descr)?></td>
    								<td colspan="2"><?=$item->estadoFisico->descr?></td>
    							</tr>
    							<tr>
    								<td class="header <?=$header?>">FECHA DE ADQUISICIÓN</td>
    								<td colspan="2" class="header <?=$header?>">OBSERVACIONES</td>
    							</tr>
    							<tr>
    								<td><?
    									$date = new DateTime($item->fechaAdquisicion);
    									echo $date->format('d-m-Y');
    								?></td>
    								<td colspan="2"><?=$item->notas?></td>
    							</tr>
    							<tr>
    								<td rowspan="2">CÓDIGO</td>
    								<td colspan="7" style="padding-top: 7px; padding-bottom: 7px;">
    									<img width="240px" src="src/lib/phpbarcode/barcode.php?text=<?=$item->empresa->id.';'.$item->periodo->id.';'.$item->id?>&size=20&print=false">
    								</td>
    							</tr>
    							<tr>
    								<td colspan="7"><?=$item->folio?></td>
    							</tr>
    						</tbody>
    					</table>
                    <?
                    }
                    ?>
                    <table class="table-reporte">
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
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">LIC. HILDA NAVA SESEÑA</td>
                                <td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;"><?=(isset($item->responsable->nombre)&&$item->responsable->nombre!=""?$item->responsable->titulo." ".$item->responsable->nombre." ".$item->responsable->apellido:"")?></td>
                                <td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Sindico</td>
                                <td colspan="2" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="3" width="42.5%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Resguardatario</td>
                                <td colspan="1" width="5%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3" width="30%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="4" width="40%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">&nbsp;</td>
                                <td colspan="3" width="30%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3" width="30%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                                <td colspan="4" width="40%" style="text-align: center; font-weight: bold !important; border-top:1px solid #000;">Entrego</td>
                                <td colspan="3" width="30%" style="border-top:1px solid #FFF !important;">&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
					<?
					}
				?>
			</div>
		</div>
	</div>							
    <script type="text/javascript" src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script type="text/javascript" src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <!-- <script type="text/javascript" src="vendors/nprogress/nprogress.js"></script> -->
    <!-- iCheck -->
    <script type="text/javascript" src="vendors/iCheck/icheck.min.js"></script>
    <!-- moment -->
    <script type="text/javascript" src="vendors/moment/min/moment.min.js"></script>
    <!-- Datatables -->
    <script type="text/javascript" src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script type="text/javascript" src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script type="text/javascript" src="vendors/jszip/dist/jszip.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- NOTIFICACIONES -->
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script type="text/javascript" src="vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <script type="text/javascript" src="vendors/cropper/dist/cropper.js"></script>
    <script type="text/javascript" src="build/bootstrap-select/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker-new.js"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
    <script type="text/javascript" src="js/jquery.mask.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="js/properties.js"></script>
    <script src="cedulaprint.js"></script>
  </body>
</html>