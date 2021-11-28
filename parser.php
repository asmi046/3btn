<?php
// php asmi04s9.beget.tech/public_html/parser.php
header("Content-type: text/html; charset=utf-8");
ini_set("display_errors",1);
error_reporting(E_ALL); 

define('BASE_NAME', "asmi04s9_3btn");
define('BASE_HOST', "localhost");
define('BASE_USER', "asmi04s9_3btn");
define('BASE_PASS', "i3R5&KGd");


$xml = simplexml_load_file(__DIR__.'/data/MessageFor_020000000004.xml');

$ploschadki = $xml->getNamespaces(); 
$rows = $xml->xpath('//v8msg:Body');

$typeRbXML = $rows[0]->xpath('//CatalogObject.ТипыРекламныхБлоков');
$typeRbLst = array();
foreach ($typeRbXML as $obj) {
	$typeRbLst[(string)$obj->Ref] = $obj->Description;
}

var_dump($typeRbLst);


$osnIzobrXML = $rows[0]->xpath('//CatalogObject.ТипыРекламныхБлоков');
$osnIzobrLst = array();
foreach ($osnIzobrXML as $obj) {
	$osnIzobrLst[(string)$obj->Ref] = $obj->Description;
}

var_dump($osnIzobrLst);


$raionXML = $rows[0]->xpath('//CatalogObject.Районы');
$raionLst = array();
foreach ($raionXML as $obj) {
	$raionLst[(string)$obj->Ref] = $obj->Description;
}

var_dump($raionLst);

$gorodXML = $rows[0]->xpath('//CatalogObject.Города');
$gorodLst = array();
foreach ($gorodXML as $obj) {
	$gorodLst[(string)$obj->Ref] = $obj->Description;
}


var_dump($gorodLst);


$priceXML = $rows[0]->xpath('//Row');
$priceLst = array();
foreach ($priceXML as $obj) {
	$priceLst[(string)$obj->ID] = (string)$obj->Price;
}

$objectXML = $rows[0]->xpath('//CatalogObject.РекламныеБлоки');
$objectList = array();

$base_connect = new mysqli(BASE_HOST, BASE_USER, BASE_PASS);
$base_connect->select_db(BASE_NAME);

if($base_connect->connect_error){
    die("Ошибка: " . $base_connect->connect_error);
}


$base_connect->query("TRUNCATE transfet_base");

$i=1;

foreach ($objectXML as $obj) {

	$result = $base_connect->query("INSERT INTO `transfet_base` (`id`, `npp`, `Ref`, `Description`, `Code`, `Type`, `Img`, `ImgMap`, `Raion`, `Gorod`, `Opisanie`, `Osveshenie`, `Koordinati`, `GRP`, `Price`)".
		" VALUES ('',". 
		$i.", ". 
		"'".(string)$obj->Ref."', ".
		"'".(string)$obj->Description."', ".
		"'".(string)$obj->НомерБлока."', ".
		"'Type', ".
		"'".(string)$obj->ОсновноеИзображение."', ".
		"'".(string)$obj->ИзображениеНаКарте."', ".
		"'Raion', ".
		"'Gorod', ".
		"'".(string)$obj->Описание."', ". 
		"'".(string)$obj->Освещение."', ".
		"'".(string)$obj->Координаты."', ".
		"'GRP', ".
		"'Price');");

	$i++;

	var_dump($result);
	var_dump($obj);
}

echo "Подключение успешно установлено \n\r";
echo "Данные добавлены \n\r";
$base_connect->close();

?>