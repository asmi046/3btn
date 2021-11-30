<?php
// php asmi04s9.beget.tech/public_html/parser.php
header("Content-type: text/html; charset=utf-8");
ini_set("display_errors",1);
error_reporting(E_ALL); 

include "setings.php";

$filename = "";
$fileindir = glob(__DIR__.'/data/*.xml');

if (empty($fileindir)) die("Нет файла с данными! \n\r");

$filename = $fileindir[0];

$xml = simplexml_load_file($filename);

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




$i=1;

foreach ($objectXML as $obj) {

	$exist = $base_connect->query("SELECT * FROM `transfet_base` WHERE `Ref` = '".(string)$obj->Ref."'");

	$raion = (empty($obj->Район))?"":$raionLst[(string)$obj->Район];
	$gorod = (empty($obj->Город))?"":$gorodLst[(string)$obj->Город];
	$price = (empty($priceLst[(string)$obj->ID]))?"":$priceLst[(string)$obj->ID];

	$inputSatatus = "";

	if ( !empty($exist->num_rows) ) {

		$result = $base_connect->query("UPDATE `transfet_base` SET ".
		"`npp` = ".$i.
		"`Ref` = '".(string)$obj->Ref."'".
		"`Description` = '".(string)$obj->Description."'".
		"`Code` = '".(string)$obj->Code."'".
		"`Type` = '".(string)$obj->РекламныйБлок_ТипБлока."'".
		"`Img` = '".(string)$obj->ОсновноеИзображение."'".
		"`ImgMap` = '".(string)$obj->ИзображениеНаКарте."'".
		"`Raion` = '".$raion."'".
		"`Gorod` = '".$gorod."'".
		"`Opisanie` = '".(string)$obj->Описание."'".
		"`Osveshenie` = '".(string)$obj->Освещение."'".
		"`Koordinati` = '".(string)$obj->Координаты."'".
		"`GRP` = ''".
		"`Price` = '".$price."'".
		" WHERE `transfet_base`.`Ref` = ".(string)$obj->Ref.";");
		$inputSatatus = "Обновлен";
	} else {

	$result = $base_connect->query("INSERT INTO `transfet_base` (`id`, `npp`, `Ref`, `Description`, `Code`, `Type`, `Img`, `ImgMap`, `Raion`, `Gorod`, `Opisanie`, `Osveshenie`, `Koordinati`, `GRP`, `Price`)".
		" VALUES ('',". 
		$i.", ". 
		"'".(string)$obj->Ref."', ".
		"'".(string)$obj->Description."', ".
		"'".(string)$obj->Code."', ".
		"'".(string)$obj->РекламныйБлок_ТипБлока."', ".
		"'".(string)$obj->ОсновноеИзображение."', ".
		"'".(string)$obj->ИзображениеНаКарте."', ".
		"'".$raion."', ".
		"'".$gorod."', ".
		"'".(string)$obj->Описание."', ". 
		"'".(string)$obj->Освещение."', ".
		"'".(string)$obj->Координаты."', ".
		"'GRP', ".
		"'".$price."');");
		$inputSatatus = "Добавлен";
	}

	$i++;

	echo (string)$obj->Ref." -> ".$inputSatatus."\n\r";
}


	

echo "Подключение успешно установлено \n\r";
echo "Данные добавлены \n\r";
$base_connect->close();

?>