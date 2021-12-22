<!DOCTYPE html>
<html lang="en">
<?
include "setings.php";

$base_connect = new mysqli(BASE_HOST, BASE_USER, BASE_PASS);
$base_connect->set_charset("utf8");
$base_connect->select_db(BASE_NAME);

if($base_connect->connect_error){
  die("Ошибка: " . $base_connect->connect_error);
}

$kraion = empty($_REQUEST["kraion"])?"%":$_REQUEST["kraion"];
$ktype = empty($_REQUEST["ktype"])?"%":$_REQUEST["ktype"];
$sstr = empty($_REQUEST["sstr"])?"%":$_REQUEST["sstr"];

$q = "SELECT `transfet_base`.*, count(*) as 'side_count' FROM `transfet_base` WHERE `Koordinati` != '' AND `Raion` LIKE '".$kraion."' AND `Type` LIKE '".$ktype."' AND `Description` LIKE '".(($sstr === "%")?$sstr:"%".$sstr."%")."' GROUP BY `Koordinati`";
$allblk = $base_connect->query($q);

$resultMass = array();

while ($row = $allblk->fetch_array(MYSQLI_ASSOC)) {
   
    $sides = [];
    $all_side = $base_connect->query("SELECT * FROM `transfet_base` WHERE `Koordinati` = '".$row["Koordinati"]."'  AND `Raion` LIKE '".$kraion."' AND `Type` LIKE '".$ktype."' AND `Description` LIKE '".(($sstr === "%")?$sstr:"%".$sstr."%")."'");
    while ($mr_row = $all_side->fetch_array(MYSQLI_ASSOC)) {
        $sides[] = $mr_row;
    }

    $resultMass[] = [
        "Gorod" => $row["Gorod"],
        "Raion" => $row["Raion"],
        "Code" => $row["Code"],
        "Type" => $row["Type"],
        "Geo_name" => $row["geo_name"],
        "Img" => $row["Img"],
        "Osveshenie" => $row["Osveshenie"],
        "Koordinati" => $row["Koordinati"],
        "Side_count" => $row["side_count"],
        "Sides" => $sides
    ];
}

?>

<head> 
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title>Информация по блоку - <?echo $blkInfoBase["Description"]?></title>
  <link rel="stylesheet" type="text/css" href="style.css"/>

  <link rel="icon" type="image/png" href="img/favicons/icon256.png" sizes="256x256">
  <link rel="icon" type="image/png" href="img/favicons/icon128.png" sizes="128x128">
  <link rel="icon" type="image/png" href="img/favicons/icon64.png" sizes="64x64">
  <link rel="icon" type="image/png" href="img/favicons/icon32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="img/favicons/icon16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="img/favicons/icon.svg" sizes="any">
  <link rel="shortcut icon" href="favicon.ico">

</head>

<script>
    let all_point = <? echo json_encode($resultMass);?>
    
</script>

<body>
  <header class="header">
    <div class="container">
      <a href="" class="logo">
        <img src="img/logo.png" alt="">
      </a>

      <ul class = "menu">
        <li><a href="<?echo SITE_NAME?>">К списку поверхностей</a></li>
        <li><a href="https://www.3bt.ru/">На основной сайт</a></li>
      </ul>
    </div>  
  </header> 

<main class="main main_nopadding">
    <section class="bigmap">
    <div class="search_result">
        <form class = "filter_form" method = "GET" action="">
            <select name = "ktype">
                <option <? echo ($ktype === "%")?"selected":""; ?> selected value="%">Все тип конструкций</option>
                <?
                    $type = $base_connect->query("SELECT `transfet_base`.`Type` FROM `transfet_base` WHERE `Koordinati` != '' GROUP BY `Type`");  
                    while ($row = $type->fetch_array(MYSQLI_ASSOC)) {
                        ?>
                            <option <? echo ($ktype === $row["Type"])?"selected":""; ?> value="<?echo $row["Type"]?>"><?echo $row["Type"]?></option>        
                        <?
                    }
                ?>
            </select>

            <select name = "kraion">
                <option <? echo ($kraion === "%")?"selected":""; ?> value="%">Все районы</option>
                <?
                    $type = $base_connect->query("SELECT `transfet_base`.`Raion` FROM `transfet_base` WHERE `Koordinati` != '' AND `Raion` != '' GROUP BY `Raion`");  
                    while ($row = $type->fetch_array(MYSQLI_ASSOC)) {
                        ?>
                            <option <? echo ($kraion === $row["Raion"])?"selected":""; ?> value="<?echo $row["Raion"]?>"><?echo $row["Raion"]?></option>        
                        <?
                    }
                ?>
            </select>

            <input type="text" name = "sstr" placeholder = "Введите строку для поиска" value = "<?echo ($sstr === "%")?"":$sstr; ?>">

            <button type = "submit" class = "btn">Найти</button>
        </form>
        <h2>Результаты поиска</h2>
        <div id = "resultTable" class="resultTable">
            <?foreach ($resultMass as $rez) {?>
                <div class="rBlk" id = "<?echo $rez["Code"]?>" data-code = "<?echo $rez["Code"]?>" data-koordinat = "<?echo $rez["Koordinati"]?>">
                    <div class="info">
                        <div class="info_element"> 
                            <strong>Город: </strong> <?echo $rez["Gorod"]?>
                        </div> 
                        
                        <div class="info_element"> 
                            <strong>Район: </strong> <?echo $rez["Raion"]?>
                        </div>
                        
                        
                    </div>

                    <div class="blkName">
                        <?echo $rez["Geo_name"]?>
                    </div>

                    <div class="info_bottm">
                        <div class="info_element"> 
                            <strong>Тип: </strong> <?echo $rez["Type"]?>
                        </div>
                        
                        <div class="info_element"> 
                            <strong>Кол-во сторон: </strong> <?echo $rez["Side_count"]?>
                        </div>
                    </div>
                </div>
            <?}?>
        </div>
    </div>    
    <div id = "main_map" class="main_map"></div>

    </section>
</main>

    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
    <script>
      let myMap;
      let selectedElement = undefined;

      function selectElement(element) {
        if (selectedElement !== undefined)
            selectedElement.classList.remove("active")

        element.classList.add("active")
        selectedElement = element
      }

      function generateBoolonContent (element) {
          let be = "<div class = 'blWriper'>"
            be += "<div class = 'infoBlk'>"
                
                be += "<div class = 'img'>"
                    be += "<img src = '<? echo SITE_NAME?>/data/"+element.Img+"' >"
                be += "</div>"

                be += "<div class = 'inform'>"
                    be += "<h2>"+element.Geo_name+"</h2>"
                    be += "<strong>Тип</strong> "+element.Type+"<br/>"
                    be += "<strong>Район</strong> "+element.Raion+"<br/>"
                be += "</div>"

            be += "</div>"

            be += "<div class = 'selectorBlk'>"
                for (let i = 0; i<element.Sides.length;  i++)
                {
                    be += "<a href = 'http://map.3bt.ru/blk?id="+element.Sides[i].Ref+"' class = 'side_lnk'>"
                        be += (element.Sides[i].side == "")?"A":element.Sides[i].side
                    be += "</a>"
                }
            be += "</div>"
          be += "</div>"
          return be
      }

      ymaps.ready(init);

      function init() {

        myMap = new ymaps.Map("main_map", {
          center: [51.730850, 36.193012],
          zoom: 9,
          // Выключаем все управление картой
          controls: []
        });

        var myGeoObjects = [];

        for (let i =0; i<all_point.length; i++) {
            let coord = all_point[i].Koordinati.split(",")

            myPlacemark = new ymaps.Placemark(coord, {
                balloonContent: generateBoolonContent(all_point[i]),
                Code:all_point[i].Code
            }, {
                iconLayout: 'default#image',
                iconImageHref: 'img/map.svg',
                iconImageSize: [60, 30],
				iconImageOffset: [-30, -30]
            });


            myPlacemark.events.add('click' , function(e){
											
				var code = e.get("target").properties.get("Code");
                let element = document.getElementById(code)
                selectElement(element)
                element.scrollIntoView({block: "center", behavior: "smooth"})
			
											
			});

            myGeoObjects[i] = myPlacemark 

            
        }

        var clusterer = new ymaps.Clusterer({
            clusterDisableClickZoom: false,
            clusterIconColor:"#DD290A",
            clusterOpenBalloonOnClick: false,
            // Устанавливаем стандартный макет балуна кластера "Карусель".
            clusterBalloonContentLayout: 'cluster#balloonCarousel',
            // Устанавливаем собственный макет.
            // clusterBalloonItemContentLayout: customItemContentLayout,
            // Устанавливаем режим открытия балуна. 
            // В данном примере балун никогда не будет открываться в режиме панели.
            clusterBalloonPanelMaxMapArea: 0,
            // Устанавливаем размеры макета контента балуна (в пикселях).
            clusterBalloonContentLayoutWidth: 300,
            clusterBalloonContentLayoutHeight: 200,
            // Устанавливаем максимальное количество элементов в нижней панели на одной странице
            clusterBalloonPagerSize: 5
            // Настройка внешего вида нижней панели.
            // Режим marker рекомендуется использовать с небольшим количеством элементов.
            // clusterBalloonPagerType: 'marker',
            // Можно отключить зацикливание списка при навигации при помощи боковых стрелок.
            // clusterBalloonCycling: false,
            // Можно отключить отображение меню навигации.
            // clusterBalloonPagerVisible: false
        });

        clusterer.add(myGeoObjects);
        myMap.geoObjects.add(clusterer)
      }
     </script>

      <script>
          document.addEventListener("DOMContentLoaded", () => { 
            console.log(all_point);

            const blkBtn = document.querySelectorAll(".rBlk")
            
            blkBtn.forEach(element => { 
                element.onclick = (e) => { 
                    
                    selectElement(element)

                    let coord = element.dataset.koordinat.split(",")
                    myMap.setCenter(coord)
                    myMap.setZoom(17)  

                    myMap.geoObjects.each(function (geoObject) {
                        let go = geoObject.getGeoObjects() 
                        
                        for (let i =0; i< go.length; i++)
                        {
                            if (go[i].properties.get('Code') == element.dataset.code) {
                                
                                if (!go[i].balloon.isOpen())
                                    go[i].balloon.open();
                            
                                break;
                            }
                        }
                    }); 

                    
                     
                }
                
            });

          });
      </script>
</body>
</html>
<?
$base_connect->close();
?>