<?
	include("../vo/config.php");
	include('../lib/phpqrcode/qrlib.php');
	Logger::configure("../config/log4php.xml");
	$log = Logger::getLogger("");
	$settings = new Properties("../config/settings.xml");
	$database = new Properties("../config/database.xml");
	$queries = new Properties("../config/queries.xml");
	$data = strtoupper($_SERVER["REQUEST_METHOD"])=='POST'?$_POST:$_GET;
	$response = array("result"=>"", "desc"=>"", "data"=>array());
	$text = "Nombre empresa
Folio del bien
Departamento";
	$text."</br>";
	$filepath = $settings->prop("system.path").$settings->prop("qrcode.path")."test.png";
	QRcode::png($text, $filepath, "L", 3);
	if(file_exists($filepath)){
		echo "success";
	}else{
		echo "error";
	}

	echo "<".$result.">";
?>

<img src='<?=$settings->prop("system.url").$settings->prop("qrcode.path")."test.png"?>'>