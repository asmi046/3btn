<!DOCTYPE html>
<html lang="en">
<?
include "setings.php";
$blk_id = $_REQUEST["id"];

$base_connect = new mysqli(BASE_HOST, BASE_USER, BASE_PASS);
$base_connect->select_db(BASE_NAME);

if($base_connect->connect_error){
  die("Ошибка: " . $base_connect->connect_error);
}

$blkInfo = $base_connect->query("SELECT * FROM `transfet_base` WHERE `Ref` = '".$blk_id."'");

$$blkInfoBase = $blkInfo->fetch_array(MYSQLI_ASSOC);

?>

<head> 
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title>Информация по блоку</title>
  <link rel="stylesheet" type="text/css" href="style.css"/>

  <link rel="icon" type="image/png" href="img/favicons/icon256.png" sizes="256x256">
  <link rel="icon" type="image/png" href="img/favicons/icon128.png" sizes="128x128">
  <link rel="icon" type="image/png" href="img/favicons/icon64.png" sizes="64x64">
  <link rel="icon" type="image/png" href="img/favicons/icon32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="img/favicons/icon16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="img/favicons/icon.svg" sizes="any">
  <link rel="shortcut icon" href="favicon.ico">

</head>
<body>
  <header class="header">
    <div class="container">
      <a href="" class="logo">
        <img src="img/logo.png" alt="">
      </a>

      <ul class = "menu">
        <li><a href="">К списку поверхностей</a></li>
        <li><a href="">На основной сайт</a></li>
      </ul>
    </div>  
  </header> 

  <main class="main">
    <section class="shield">
      <div class="container">

        <div class="shield__box">
          <h1 class="shield__box-title"><span>Рекламный блок:</span> 50 лет Октября, 13, нечетная сторона, A</h1>
          <div class="shield__box-inner">
            <div class="shield__box-img">
              <img src="img/shield-img.jpg" alt="">
            </div>
            <div style="border:none;" class="tableBlk one-tableBlk">
              <div class="tabl-flex"><div id="cl1h" class="tabl-flex__1 tabl-flex__gr">Город</div>
              <div id="cl1" class="tabl-flex__2 tabl-flex__gr">Курск</div>
            </div>
            <div class="tabl-flex"><div id="cl2h" class="tabl-flex__1">Район</div>
            <div id="cl2" class="tabl-flex__2">Центральный</div>
          </div>
          <div class="tabl-flex"><div id="cl3h" class="tabl-flex__1 tabl-flex__gr">Адрес</div>
          <div id="cl3" class="tabl-flex__2 tabl-flex__gr">50 лет Октября, 13, нечетная сторона,A</div>
        </div>
        <div class="tabl-flex">
          <div id="cl4h" class="tabl-flex__1">Тип конструкции</div>
          <div id="cl4" class="tabl-flex__2">Щиты 6Х3</div>
        </div>
        <div class="tabl-flex">
          <div id="cl5h" class="tabl-flex__1 tabl-flex__gr">Освещение</div>
          <div id="cl5" class="tabl-flex__2 tabl-flex__gr">Да</div>
        </div>
        <div class="tabl-flex">
          <div id="cl6h" class="tabl-flex__1">Код</div>
          <div id="cl6" class="tabl-flex__2">01-011А</div>
        </div>
        <div class="tabl-flex">
          <div id="cl7h" class="tabl-flex__1 tabl-flex__gr">GRP</div>
          <div id="cl7" class="tabl-flex__2 tabl-flex__gr">7.52</div>
        </div>
      </div>
    </div>
    <div id="map" class="shield__box-map"></div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
    <script>
      ymaps.ready(init);

      function init() {

        var myMap = new ymaps.Map("map", {
          center: [51.725541, 36.162382],
          zoom: 17,
          // Выключаем все управление картой
          controls: []
        });

        var myGeoObjects = [];

        myGeoObjects[0] = new ymaps.Placemark([51.725541, 36.162382], {
        // Свойства. 
        // hintContent: '<div class="map-hint">Авто профи, Курск, ул.Комарова, 16</div>',
        balloonContent: '<div class="map-hint">"ТРИ БОТИНКА - Рекламное агенство"</div>',
      }, {
        // Необходимо указать данный тип макета.
        iconLayout: 'default#image',
        iconImageHref: 'img/map.svg',
        // Размеры метки.
        iconImageSize: [36, 55],
        // Смещение левого верхнего угла иконки относительно
        // её «ножки» (точки привязки).
        iconImageOffset: [-18, -26]
      });

        // var clusterIcons=[{
        //         href:'img/map-marker.svg',
        //         size:[31,40],
        //         offset:[0,0]
        // }];

      var clusterer = new ymaps.Clusterer({
        clusterDisableClickZoom: false,
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
        myMap.geoObjects.add(clusterer);
        myMap.behaviors.disable('scrollZoom');
      }
     </script>
    </div>
  </div>
</section>
</main>

</body>
</html>
<?
$base_connect->close();
?>