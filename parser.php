<?php
// php map.3bt.ru/docs/parser.php
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


$side_type_xml = $rows[0]->xpath('//CatalogObject.ВидыСторон');
$side_type = array();
foreach ($side_type_xml as $obj) {
	$side_type[(string)$obj->Ref] = (string)$obj->Description;
}

var_dump($side_type);

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
$base_connect->set_charset("utf8");
$base_connect->select_db(BASE_NAME);

if($base_connect->connect_error){
    die("Ошибка: " . $base_connect->connect_error);
}

$i=1;
$deleteCount = 0;

foreach ($objectXML as $obj) {

	$exist = $base_connect->query("SELECT * FROM `transfet_base` WHERE `Ref` = '".(string)$obj->Ref."'");

	$raion = (empty($obj->Район))?"":$raionLst[(string)$obj->Район];
	$gorod = (empty($obj->Город))?"":$gorodLst[(string)$obj->Город];
	$side = (empty($obj->Сторона))?"":$side_type[(string)$obj->Сторона];
	$price = (empty($priceLst[(string)$obj->ID]))?"":$priceLst[(string)$obj->ID];

	$inputSatatus = "";

	if ( !empty($exist->num_rows) ) {

		if ((string)$obj->ДатаУдаления !== "0001-01-01T00:00:00") {
			echo "(Удален) ";
			$deleteCount++;
			$resultDelete = $base_connect->query("DELETE FROM `transfet_base` WHERE `transfet_base`.`Ref` = '".(string)$obj->Ref."'");
		}

		$result = $base_connect->query("UPDATE `transfet_base` SET ".
		"`npp` = ".$i.
		", `Ref` = '".trim((string)$obj->Ref,"¶")."'".
		", `Description` = '".trim((string)$obj->Description,"¶")."'".
		", `geo_name` = '".trim((string)$obj->РекламныйБлок_ГеографическаяМетка,"¶")."'".
		", `Code` = '".trim((string)$obj->Code,"¶")."'".
		", `side` = '".trim($side,"¶")."'".
		", `Type` = '".trim((string)$obj->РекламныйБлок_ТипБлока,"¶")."'".
		", `Img` = '".trim((string)$obj->ОсновноеИзображение,"¶")."'".
		", `ImgMap` = '".trim((string)$obj->ИзображениеНаКарте,"¶")."'".
		", `Raion` = '".trim($raion,"¶")."'".
		", `Gorod` = '".trim($gorod,"¶")."'".
		", `Opisanie` = '".trim((string)$obj->Описание,"¶")."'".
		", `Osveshenie` = '".trim((string)$obj->Освещение,"¶")."'".
		", `Koordinati` = '".trim((string)$obj->Координаты,"¶")."'".
		", `GRP` = ''".
		", `Price` = '".trim($price,"¶")."'".
		" WHERE `transfet_base`.`Ref` = '".(string)$obj->Ref."';");
		$inputSatatus = "Обновлен " . ($result)?"true":"false";
	} else {
		
		if ((string)$obj->ДатаУдаления !== "0001-01-01T00:00:00") {
			echo "(Помечен как Удаленный) ";
			continue;
		}

	$result = $base_connect->query("INSERT INTO `transfet_base` (`id`, `npp`, `Ref`, `Description`, `geo_name`, `Code`, `side`, `Type`, `Img`, `ImgMap`, `Raion`, `Gorod`, `Opisanie`, `Osveshenie`, `Koordinati`, `GRP`, `Price`)".
		" VALUES ('',". 
		$i.", ". 
		"'".(string)$obj->Ref."', ".
		"'".(string)$obj->Description."', ".
		"'".(string)$obj->РекламныйБлок_ГеографическаяМетка."', ".
		"'".(string)$obj->Code."', ".
		"'".$side."', ".
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

	echo (string)$obj->Ref." -> ".$inputSatatus." -> ".$side."\n\r";
}


	

echo "Подключение успешно установлено \n\r";
echo "Данные добавлены \n\r";
$base_connect->close();

?>