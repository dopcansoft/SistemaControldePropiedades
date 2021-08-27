<?
	session_name("inv");
	session_start();
	include("../vo/Properties.php");
	$settings = new Properties("../config/settings.xml");
	unset($_SESSION["empresa"]);
	unset($_SESSION["usuario"]);
	unset($_SESSION["periodo"]);
	session_unset();
	session_destroy();
	//var_dump($_SESSION);
	//unset($_SESSION["clienteSession"]);
	header("Location: ".$settings->prop("system.url")."/".$settings->prop("url.login"));
?>