<?
	if(isset($_SESSION["usuario"])){
		//hacer conexion a base de datos
		//crear consulta
		//obtener resultados
		//guardarlos en un arreglo
		$registros
$cadena = "<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div>
		<h3>Bienes</h3>
	</div>
	<table>
		<thead>
			<th>
				<tr>ID</tr>
				<tr>DESCRIPCION</tr>
				<tr>FOTO</tr>
			</th>
		</thead>
		<tbody>
			<?
			if(isset($registros)){
				foreach($registros as $registro){
					?>
					<tr>
						<td><?=$registro->id?></td>
						<td><?=$registro->descripcion?></td>
						<td><img src="<?=$registro->imagen?>" /></td>
					</tr>
					<?
				}
			}
			?>
		</tbody>
	</table>
</body>
</html>
<?
	}else{
		header("location: login.php");
	
	}
?>