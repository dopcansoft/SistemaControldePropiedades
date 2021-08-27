<?
    session_start();
    session_name("inv");   
    header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
    header("Content-type:   application/x-msexcel; charset=utf-8");
    header("Content-Disposition: attachment; filename=resumen_bienes.xls"); 
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);

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

    $list = array();
    $bien = new BienDao();
    $catBienesInmuebles = array();
    $catBienesMuebles = array();
    $departamentos = array();
    $catDepreciacion = array();
    $catEdoFisico = array();
    $catOrigen = array();
    $catUma = array();
    try{
        $db = new DBConnector($database);
        if(isset($db)){
        	$catalogoFactory = new CatalogoFactory();
        	$headers = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.headers.list"), $settings, $log, "Clasificacion");
        	$log->debug($headers);
        	$clasificacion = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.list"), $settings, $log, "Clasificacion");
        	//$log->debug($clasificacion);
        	$inventarioFactory = new BienFactory();
    		$estatusInventario = Bien::ESTATUS_INVENTARIO_MANTIENE.",".Bien::ESTATUS_INVENTARIO_ALTA;
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, BienFactory::TIPO_INVENTARIO_CONTABLE, "", "", "", "", "", "", $estatusInventario, $db, $queries, $settings, $log);	
    		$list = $inventarioFactory->jerarquiaCC($items, $clasificacion, $headers, $log);
    		$anioOLD = "";
            $fechaOld = "";
            foreach($items as $item){
                $log->debug('fechaAdquisicion: '.$item->fechaAdquisicion);
                if($item->fechaAdquisicion!=""){
                    $item->fechaAdquisicion = DateTime::createFromFormat('Y-m-d H:i:s',$item->fechaAdquisicion);//DateTime::createFromFormat("Y-m-d H:i:s", $item->fechaAdquisicion);
                    if($fechaOld==""){
                        $fechaOld = $item->fechaAdquisicion;
                    }
                    if($item->fechaAdquisicion<$fechaOld){
                        $fechaOld = $item->fechaAdquisicion;
                    }
                }
            }
            
            $init_year = $fechaOld->format("Y");
            $last_year = "2020";
            $log->debug('items: '.count($items));

            $mes_corte = 12;
            $anio_corte = 2020;
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

    <link rel="stylesheet" href="build/bootstrap-select/css/bootstrap-select.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="vendors/cropper/dist/cropper.css">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <link href="css/articulo.css" rel="stylesheet">
  </head>
  <body>
	<h3><small>Reporte por Cuenta</small></h3>
	<table class="table-reporte table-margin-bottom">
        <thead>
            <tr style="background:#DDD !important;">
                <th width="auto">FOLIO</th>
                <th style="text-align: center;" colspan="2" width="auto">ACTIVO</th>
                <th style="text-align: center;" colspan="2" width="auto">DEPRECIACIÓN</th>
                <th width="auto">DESCRIPCIÓN</th>
                <th width="auto">FECHA ADQUISICIÓN/AVALUO</th>
                <th width="auto">VALOR HISTORICO</th>
                <?
                for($x=$init_year;$x<=$last_year;$x++){
                    ?>
                    <th><?=$x?></th>
                    <?
                }
                ?>
                <th width="auto">DEPRECIACIÓN ACUMULADA</th>
                <th width="auto">VALOR EN LIBROS</th>
                <th width="auto">VALOR HISTORICO</th>
            </tr>
        </thead>
        <tbody>
        <?
		foreach($list as $data){
			$header = $data["header"];
			if(count($data["content"])>0){
				foreach($data["content"] as $content){
					$clasificacion = $content["clasificacion"];
					$i=1;
					foreach($content["list"] as $bien){									
						?>
						<tr>
                            <td style="white-space: nowrap;"><?=$bien->folio?></td>
                            <td width="auto"><?=$bien->clasificacion->cuentaContable?></td>
                            <td width="auto"><?=$bien->clasificacion->descr?></td>
                            <td width="auto"><?=$bien->depreciacion->cuenta?></td>
                            <td width="auto"><?=$bien->depreciacion->descr?></td>
                            <td><?=$bien->descripcion.($bien->marca!=""?", MARCA: ".$bien->marca:'').($bien->modelo!=""?", MODELO: ".$bien->modelo:'').($bien->serie!=""?", NO. DE SERIE: ".$bien->serie:'').($bien->color->descr!=""?", COLOR: ".$bien->color->descr:'')?></td>
                            <td><?
                            if($bien->fechaAdquisicion!=""){
                                echo $bien->fechaAdquisicion->format('d-m-Y');                                                            
                            }
                            ?></td>
                            <td style="text-align: right;"><?=number_format($bien->valor,2,'.',',')?></td>
                            <?
                            $anioAdquisicion = $bien->fechaAdquisicion->format('Y');
                            $mesAdquisicion = $bien->fechaAdquisicion->format('m');
                            $vidaUtil = $bien->depreciacion->vidaUtil;
                            $factor = 0;
                            if($vidaUtil>0){
                                $factor = ($bien->valor/$vidaUtil)/12;
                            }
                            $depPeriodo = 0.00;
                            $depAcumulada = 0.00;
                            $log->debug('vidaUtil: '.$bien->depreciacion->vidaUtil);
                            $log->debug('anio/mes: '.$anioAdquisicion."/".$mesAdquisicion);
                            $log->debug('valor: '.$bien->valor);
                            $log->debug('factor: '.$factor);
                            for($x=$init_year;$x<=$last_year;$x++){
                                if($x>=$anioAdquisicion){
                                    if($x==$anioAdquisicion && $x != $anio_corte){
                                        $depPeriodo = round((12-$mesAdquisicion)*$factor,2);
                                    }else if($x==$anioAdquisicion && $x == $anio_corte){
                                        $depPeriodo = round(($mes_corte-$mesAdquisicion)*$factor,2);
                                    }else if($x>$anioAdquisicion && $x == $anio_corte){
                                        $depPeriodo = round(($mes_corte)*$factor,2);
                                    }else{
                                        $depPeriodo = round(12*$factor,2);
                                    }        
                                }    
                                ?>
                                <td><?=number_format($depPeriodo,2,'.',',')?></td>
                                <?
                                $depAcumulada=$depAcumulada+$depPeriodo;
                            }
                            ?>
                            <td style="text-align: right;"><?=number_format($depAcumulada,2,'.',',')?></td>
                            <td style="text-align: right;"><?=number_format($bien->valor-$depAcumulada,2,'.',',')?></td>
                            <td style="text-align: right;"><?=number_format($bien->valorAnterior,2,'.',',')?></td>
                        </tr>
						<?
					}
				}
            }
		}
	    ?>
        </tbody>
    </table>                        
  </body>
</html>