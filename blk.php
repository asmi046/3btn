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

</head>
<body>
    <header>
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

</body>
</html>
<?
    $base_connect->close();
?>