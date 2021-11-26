<?php
header("Content-type: text/html; charset=utf-8");
ini_set("display_errors",1);
error_reporting(E_ALL); 
include_once("../wp-config.php");


$xml = simplexml_load_file("MessageFor_0010000000001.xml");

$ploschadki = $xml->getNamespaces(); 
$rows = $xml->xpath('//v8msg:Body');

$typeRbXML = $rows[0]->xpath('//CatalogObject.ТипыРекламныхБлоков');
$typeRbLst = array();
foreach ($typeRbXML as $obj) {
	$typeRbLst[(string)$obj->Ref] = $obj->Description;
}


$osnIzobrXML = $rows[0]->xpath('//CatalogObject.ТипыРекламныхБлоков');
$osnIzobrLst = array();
foreach ($osnIzobrXML as $obj) {
	$osnIzobrLst[(string)$obj->Ref] = $obj->Description;
}

$raionXML = $rows[0]->xpath('//CatalogObject.Районы');
$raionLst = array();
foreach ($raionXML as $obj) {
	$raionLst[(string)$obj->Ref] = $obj->Description;
}

$gorodXML = $rows[0]->xpath('//CatalogObject.Города');
$gorodLst = array();
foreach ($gorodXML as $obj) {
	$gorodLst[(string)$obj->Ref] = $obj->Description;
}


$priceXML = $rows[0]->xpath('//Row');
$priceLst = array();
foreach ($priceXML as $obj) {
	$priceLst[(string)$obj->ID] = (string)$obj->Price;
}

echo "<pre>";
	//var_dump($priceLst);
echo "</pre>";



$objectXML = $rows[0]->xpath('//CatalogObject.РекламныеБлоки');
$objectList = array();

echo "<pre>";
	//var_dump($objectXML);
echo "</pre>";




global $wpdb;
$wpdb->query("TRUNCATE transfet_base");

$i=1;

foreach ($objectXML as $obj) {

	$arr["npp"] = $i;
	$arr["Ref"] = (string)$obj->Ref;
	
	
	$arr["Description"] = (string)$obj->Description;	
	$arr["Code"] = (string)$obj->НомерБлока;
	
	$arr["Type"] = (string)$typeRbLst[(string)$obj->ТипБлока];
	$arr["Img"] = (string)$obj->ОсновноеИзображение; 
	$arr["ImgMap"] = (string)$obj->ИзображениеНаКарте;
	
	//$arr["Raion"] = (string)$raionLst[(string)$obj->Район];
	$sep = explode("_",$obj->Описание);
	$arr["Raion"] = (string)($sep[1]);
	
	$arr["Gorod"] = (string)$gorodLst[(string)$obj->Город];
	
	$arr["Opisanie"] = (string)$obj->Описание;
	$arr["Osveshenie"] = (string)$obj->Освещение;
	$arr["Koordinati"] = (string)$obj->Координаты;
	
	$arr["GRP"] = (string)($sep[0]);
	$arr["Price"] = $priceLst[(string)$obj->ID];
	
	if (!empty($arr["Koordinati"]))
		$arr["Koordinati"] = "[".$arr["Koordinati"]."]";
	
	$objectList[] = $arr;


	global $wpdb;
	$wpdb->insert(
		'transfet_base',
		$arr,
		array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f' )
	);
	$i++;
}




echo "111";
echo "<pre>";
	//var_dump($typeRbLst);
echo "</pre>";

echo "<pre>";
	var_dump($objectList);
echo "</pre>";



?>