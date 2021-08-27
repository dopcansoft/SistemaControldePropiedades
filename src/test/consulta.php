<?
	include("../vo/config.php");

	$cat = CatalogoFactory();
	$list = $cat->listado();
	json_encode($cat);
?>