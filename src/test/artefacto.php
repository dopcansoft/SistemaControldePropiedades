<?
	include("../vo/config.php");

	$artefacto = new Artefacto(
		array(
			"nombre"=>"Hola mundo",
			"clasificacion.id"=>22,
			"clasificacion.grupo"=>33,
			"clasificacion.subgrupo"=>777,
			"clasificacion.clase"=>55,
			"clasificacion.cuentaContable"=>"1.3.4",
			"clasificacion.cuentaDepreciacion"=>"5.6.1",
			"periodos.id"=>88,
			"periodos.idEmpresa"=>2345,
			"periodos.descr"=>"Lavanderia Acuario S.A."
		)
	);

	var_dump($artefacto->periodos);
	//$artefacto->init();

	//var_dump($artefacto);
	//var_dump($artefacto);
?>