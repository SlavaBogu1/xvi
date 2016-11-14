<?php

/* 

Обработчик формы - файл putimage.php:
// Проверяем пришел ли файл
if( !empty( $_FILES['image']['name'] ) ) {
  // Проверяем, что при загрузке не произошло ошибок
  if ( $_FILES['image']['error'] == 0 ) {
    // Если файл загружен успешно, то проверяем - графический ли он
    if( substr($_FILES['image']['type'], 0, 5)=='image' ) {
      // Читаем содержимое файла
      $image = file_get_contents( $_FILES['image']['tmp_name'] );
      // Экранируем специальные символы в содержимом файла
      $image = mysql_escape_string( $image );
      // Формируем запрос на добавление файла в базу данных
      $query="INSERT INTO `images` VALUES(NULL, '".$image."')";
      // После чего остается только выполнить данный запрос к базе данных
      mysql_query( $query );
    }
  }
}

Извлечь сохраненный файл изображения можно следующим образом (файл image.php):

if ( isset( $_GET['id'] ) ) {
  // Здесь $id номер изображения
  $id = (int)$_GET['id'];
  if ( $id > 0 ) {
    $query = "SELECT `content` FROM `images` WHERE `id`=".$id;
    // Выполняем запрос и получаем файл
    $res = mysql_query($query);
    if ( mysql_num_rows( $res ) == 1 ) {
      $image = mysql_fetch_array($res);
      // Отсылаем браузеру заголовок, сообщающий о том, что сейчас будет передаваться файл изображения
      header("Content-type: image/*");
      // И  передаем сам файл
      echo $image['content'];
    }
  }
}

Чтобы вывести изображение в HTML-документе, делаем так:
<img src="image.php?id=17" alt="" />
*/

?>