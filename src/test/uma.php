<?
	include("../vo/config.php");
	$uma = new Clasificacion(array(
		"id"=>1,
		"grupo"=>1,
		"subgrupo"=>9,
		"clase"=>4
	));

	$all = get_object_vars($uma);

	echo json_encode($all);
?>