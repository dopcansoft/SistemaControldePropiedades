<?
	include("../vo/config.php");

	$bien = new BienDao(array(
		"tipoClasificacion.id"=>1,
		"clasificacion.id"=>2,
		"tipoValuacion.id"=>3,
		"valor"=>4
	));

	header("Content-type: application/json; charset=utf-8");
	echo json_encode($bien);
?>