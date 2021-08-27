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
        	$clasificacion = $catalogoFactory->listado($db, $queries->prop("catcuentacontable.list"), $settings, $log, "Clasificacion");
        	$inventarioFactory = new BienFactory();
    		$items = $inventarioFactory->filtrado($empresa->id, $periodo->id, "", "", "", "", "", "", "", "", $db, $queries, $settings, $log);
    		$list = $inventarioFactory->jerarquiaCC($items, $clasificacion, $headers, $log);
    	}else{
            $log->error('No se ha podido establecer conexion con base de datos');
        }
    }catch(PDOException $e){
        $log->error('PDOException: '.$e->getMessage());
    }
    $db = null;
    //header("Content-type: application/json; charset=utf-8");
    //echo json_encode($list);    	
   	foreach($list as $data){
    	if($data["total"]>0){
    		?>
    		---------------------------------------------------------------------------</br>
    		HEADER - <?=$data["header"]->cuentaContable." - TOTAL: ".$data["total"]."</br>"?>
    		---------------------------------------------------------------------------</br>
    		<?
    		foreach($data["content"] as $content){
				echo $content["clasificacion"]->cuentaContable." - ".$content["total"]."</br>";				
				$list = $content["list"];
				$total = $content["total"];
				if(count($list)>0){
					foreach($list as $i){
						echo $i->cuentaContable." - ".$i->descripcion."</br>";
					}
				}
    		}
    		/*?>
    		CLASIFICACION - <?=$content["clasificacion"]->cuentaContable.", TOTAL: ".$content["TOTAL"]."</br>"?>
    		<?
    		foreach($item as $item){
    			?>
    			BIEN <?=$item->id." - VALOR: ".$item->valor."</br>"?>
    			<?
    		}*/	
    	}
    }    
?>