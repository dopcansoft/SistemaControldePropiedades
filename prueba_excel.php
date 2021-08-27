<?
ini_set('memory_limit', '512M');
include("src/vo/config.php");
Logger::configure("src/config/log4php.xml");
$log = Logger::getLogger("");
$settings = new Properties("src/config/settings.xml");
$database = new Properties("src/config/database.xml");
$queries = new Properties("src/config/queries.xml");

$archivo = "files/xls/INVENTARIO_ZENTLA_2019.xlsx";
$inputFileType = PHPExcel_IOFactory::identify($archivo);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($archivo);
$sheet = $objPHPExcel->getSheet(0); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();
for ($row = 2; $row <= $highestRow; $row++){ 
		echo $sheet->getCell("A".$row)->getValue()." - ";
		echo $sheet->getCell("B".$row)->getValue()." - ";
		echo $sheet->getCell("C".$row)->getValue();
		echo "<br>";
}
?>