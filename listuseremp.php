<?
    session_start();
    session_name("inv");   
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
  	header("Pragma: no-cache");
    include("src/vo/config.php");
    Logger::configure("src/config/log4php.xml");
    $log = Logger::getLogger("empresas");
    $settings = new Properties("src/config/settings.xml");
    $database = new Properties("src/config/database.xml");
    $queries = new Properties("src/config/queries.xml");
    $data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;

    if(!isset($_SESSION["usuario"])){
        header("Location: ".$settings->prop("url.login"));
    }

    $misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
   	$empresa = unserialize($_SESSION["empresa"]);
   	$periodo = unserialize($_SESSION["periodo"]);


    $usuario = new UsuarioDao(array(
      "id"=>isset($data["usuario"])&&$data["usuario"]!=""?$data["usuario"]:""
    ));
   	$list = array();
    $empresas = array();
    $response = array("result"=>"", "desc"=>"");
    try{
        $db = new DBConnector($database);
        if(isset($db)){
            $log->debug('busca usuario: '.$usuario->id);
            if($usuario->find($db, $queries, $log)){
                $log->debug('Usuario encontrado');
                $permisoFactory = new PermisoFactory();
                $list = $permisoFactory->listByUser($usuario->id, $db, $queries, $log);
                $empresaFactory = new EmpresaFactory();
                $empresas = $empresaFactory->listAll($db, $queries, $settings, $log);
                $log->debug("Permisos encontrados: ".count($list));
                $size = count($empresas);
                foreach($list as $permiso){
                    for($i=0; $i<$size; $i++){
                        if($permiso->empresa->id==$empresas[$i]){
                            $log->debug('Elimina elemento: '.$i." de empresas: ".$empresas[$i]->id);
                            unset($empresas[$i]);    
                        }    
                    }
                }

                $catalogo = new CatalogoFactory();
                $roles = $catalogo->listado($db, $queries->prop("rol.list"), $settings, $log, "ItemCatalogo");
            }else{
                $log->error("Usuario no encontrado");
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
    
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <link href="css/sinv.css" rel="stylesheet">
  </head>
  <body class="nav-md body-loading">
    <!-- PRELOADER -->
    <div class="content-preloader">
    	<div class="loader">Loading...</div>
	</div>
	<!-- TERMINA PRELOADER -->
    <form id="Datos" name="Datos" action="<?$_SERVER["PHP_SELF"]?>" method="post">
      <input type="hidden" name="empresa" id="empresa" value="<?=$empresa->id?>">
      <input type="hidden" name="periodo" id="periodo" value="<?=$periodo->id?>">
      <input type="hidden" name="usr" id="usr" value="<?=$usuario->id?>">
      <input type="hidden" name="perfil" id="perfil" value="<?=$misesion->perfil->id?>">
    </form>
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <? include("logo_principal.php"); ?>
            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <? include("quick_info.php"); ?>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <? include("sidebar.php"); ?>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <? include("menu_footer.php"); ?>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <? include("menu_top.php"); ?>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Listado de Permisos por Municipio</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="row">
                      <div class="col-md-3">
                        <button class="btn btn-md btn-success" id="btn-listado" name="btn-listado" ><i class="fa fa-reply"></i> Listado</button>  
                        <button class="btn btn-md btn-primary" id="btn-agregar" name="btn-agregar" ><i class="fa fa-plus"></i> Agregar</button>  
                      </div>
                      <div class="col-md-12">&nbsp;</div>
                    </div>
                    <table id="usuarios" class="table table-responsive table-striped table-bordered tabla-datos">
                      <thead>
                        <tr>
                          <th>Municipio</th>
                          <th>Rol</th>
                          <th>Fecha Registro</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="dummy">
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>
                        		<a href="#" class="btn btn-danger btn-xs btn-eliminar" data-id-usuario="" data-id-empresa=""><i class="fa fa-trash"></i> Eliminar</a>
                           	</td>
                        </tr>
                        <?
                        if(isset($list) && count($list)>0){
                            foreach($list as $permiso){
                              ?>
                              <tr data-id="<?=$user->id?>">
                                  <td><?=$permiso->empresa->nombre?></td>
                                  <td><?=$permiso->rol->descr?></td>
                                  <td>
                                      <?
                                      $date = new DateTime($permiso->fechaInsert);
                                      echo $date->format("d-m-Y h:i:s");                                 
                                      ?>
                                  </td>
                                  <td>
                                      <a href="#" class="btn btn-primary btn-xs btn-editar" data-id-usuario="<?=$permiso->usuario->id?>" data-id-empresa="<?=$permiso->empresa->id?>"><i class="fa fa-trash"></i> Eliminar</a>
                                      <a href="#" class="btn btn-danger btn-xs btn-eliminar" data-id-usuario="<?=$permiso->usuario->id?>" data-id-empresa="<?=$permiso->empresa->id?>"><i class="fa fa-trash"></i> Eliminar</a>
                                  </td>
                              </tr>     
                              <? 
                            } 
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?=$settings->prop("page.view.footer.label")!=null?$settings->prop("page.view.footer.label"):""?>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>
    <div class="modal modal-agregar fade" id="modal-agregar" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="content-fluid">
						<div class="row">
							<div class="col-md-6 col-sm-12 col-xs-12 form-group">
								<label>Municipio</label>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12 form-group">
								<select id="select-mpio-modal" name="select-mpio-modal" class="form-control">
                  <option value=""> -- </option> 
                </select>
							</div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <label>Rol</label>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <select id="select-rol-modal" name="select-rol-modal" class="form-control">
                                  <option value=""> -- </option>
                                  <?
                                  if(count($roles)>0){
                                    foreach($roles as $rol){
                                      ?>
                                      <option value="<?=$rol->id?>"><?=$rol->descr?></option>
                                      <?
                                    }
                                  }
                                  ?>        
                                </select>
                            </div>
						</div>
						<div class="row">&nbsp;</div>
                        <div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12 form-group">
								<button class="btn btn-success form-control btn-action-guardar" data-id-bien=""><i class="fa fa-save"></i> Guardar</button>
							</div>	
						</div>						
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- NOTIFICACIONES -->
    <script src="vendors/pnotify/dist/pnotify.js"></script>
    <script src="vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- TERMINA NOTIFICACIONES -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="js/bootstrap-datepicker-new.js"></script>
    <script src="js/bootstrap-datepicker.es.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="listuseremp.js"></script>
  </body>
</html>