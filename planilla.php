<?
  session_start();
  session_name("inv");   

  include("src/vo/config.php");
  Logger::configure("src/config/log4php.xml");
  $log = Logger::getLogger("empresas");
  $settings = new Properties("src/config/settings.xml");
  $database = new Properties("src/config/database.xml");
  $queries = new Properties("src/config/queries.xml");

  if(!isset($_SESSION["usuario"])){
      header("Location: ".$settings->prop("url.login"));
  }

  $misesion = isset($_SESSION["usuario"])?unserialize($_SESSION["usuario"]):null;
 	$empresa = unserialize($_SESSION["empresa"]);
 	$periodo = unserialize($_SESSION["periodo"]);
  $presidente = new ResponsableDao();
  $tesorero = new ResponsableDao();
  $regidor = new ResponsableDao();
  $sindico = new ResponsableDao();
  $dirJuridico = new ResponsableDao();
  $organoCtrlInt = new ResponsableDao();
  $jefeAdquisiciones = new ResponsableDao();
  $list = array();
  try{
      $db = new DBConnector($database);
      if(isset($db)){
        $responsableFactory = new ResponsableFactory();
        $list = $responsableFactory->listAll($empresa->id, $db, $queries, $log);
        $presidente->findByCargo($empresa->id, Responsable::CARGO_PRESIDENTE_MUNICIPAL, $db, $queries, $log);
        $tesorero->findByCargo($empresa->id, Responsable::CARGO_TESORERO, $db, $queries, $log);
        $regidor->findByCargo($empresa->id, Responsable::CARGO_REGIDOR, $db, $queries, $log);
        $sindico->findByCargo($empresa->id, Responsable::CARGO_SINDICO, $db, $queries, $log);
        $dirJuridico->findByCargo($empresa->id, Responsable::CARGO_DIR_JURIDICO, $db, $queries, $log);
        $organoCtrlInt->findByCargo($empresa->id, Responsable::CARGO_ORG_CTRL_INT, $db, $queries, $log);
        $jefeAdquisiciones->findByCargo($empresa->id, Responsable::CARGO_JEFE_ADQUISICIONES, $db, $queries, $log);
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
    <link href="build/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" >
    
    <!-- NOTIFICACIONES -->
    <link href="vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <!-- TERMINA NOTIFICACIONES -->

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <form id="Datos" name="Datos" action="<?$_SERVER["PHP_SELF"]?>" method="post">
      <input type="hidden" name="empresa" id="empresa" value="<?=$empresa->id?>">
      <input type="hidden" name="idt" id="idt">
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
                <h3>Cabildo</h3>
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
                    <table id="articulos" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Puesto</th>
                          <th>Nombre</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                    		<tr>
                      		<td>PRESIDENTE MUNICIPAL</td>
                      		<td>
                              <select class="form-control" data-id-cargo="" id="sl-presidente" name="sl-presidente">
                                  <option value=""> -- </option>
                                  <?
                                  foreach($list as $row){
                                    ?><option value="<?=$row->id?>" <?=$presidente->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                  }
                                  ?>  
                              </select>
                          </td>
                      		<td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_PRESIDENTE_MUNICIPAL?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                      	</tr>
                        <tr>
                          <td>TESORERO</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-tesorero" name="sl-tesorero">
                                <option value=""> -- </option>
                                <?
                                foreach($list as $row){
                                  ?><option value="<?=$row->id?>" <?=$tesorero->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                }
                                ?>  
                            </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_TESORERO?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
                        <tr>
                          <td>REGIDOR</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-regidor" name="sl-regidor">
                                  <option value=""> -- </option>
                                  <?
                                  foreach($list as $row){
                                    ?><option value="<?=$row->id?>" <?=$regidor->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                  }
                                  ?>  
                              </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_REGIDOR?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
                        <tr>
                          <td>SINDICO</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-sindico" name="sl-sindico">
                                  <option value=""> -- </option>
                                  <?
                                  foreach($list as $row){
                                    ?><option value="<?=$row->id?>" <?=$sindico->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                  }
                                  ?>  
                              </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_SINDICO?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
                        <tr>
                          <td>DIRECTOR JURIDICO</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-dir-juridico" name="sl-dir-juridico">
                                <option value=""> -- </option>
                                <?
                                foreach($list as $row){
                                  ?><option value="<?=$row->id?>" <?=$dirJuridico->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                }
                                ?>  
                              </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_DIR_JURIDICO?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
                        <tr>
                          <td>ORGANO DE CONTROL INTERNO</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-ogano-control-interno" name="sl-ogano-control-interno">
                                  <option value=""> -- </option>
                                  <?
                                  foreach($list as $row){
                                    ?><option value="<?=$row->id?>" <?=$organoCtrlInt->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                  }
                                  ?>  
                              </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_ORG_CTRL_INT?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
                        <tr>
                          <td>JEFE ADQUISICIONES</td>
                          <td>
                            <select class="form-control" data-id-cargo="" id="sl-jefe-adquisiciones" name="sl-jefe-adquisiciones">
                                  <option value=""> -- </option>
                                  <?
                                  foreach($list as $row){
                                    ?><option value="<?=$row->id?>" <?=$jefeAdquisiciones->id==$row->id?'selected="selected"':''?> ><?=$row->titulo." ".$row->nombre." ".$row->apellido?></option><?
                                  }
                                  ?>  
                              </select>
                          </td>
                          <td><button class="btn btn-primary btn-xs btn-editar" data-id-cargo="<?=Responsable::CARGO_JEFE_ADQUISICIONES?>"> <i class="fa fa-edit"></i> Guardar </button></td>
                        </tr>
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

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="build/bootstrap-select/js/bootstrap-select.js"></script>
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

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <script src="planilla.js"></script>
  </body>
</html>