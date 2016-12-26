<?php
  /** @file xvi_ff.php  
	Engine file functions. 
	Open / close / update engine files.
  */
  defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

  /*  function ConvertFile(){
    $handle = @fopen("/home/slava/host/Sprogis/boost_tst_dat_build21.1_x86.txt", "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets();
            echo $buffer;
        }
        fclose($handle);
    }
  }*/
  
  
        /**
         *  @sa https://ubuntuforums.org/showthread.php?t=880698
         *  Возникла проблема, скрипт работает от www-data пользователя  и не имеет прав записи в каталоги внутри /var/www
         *  Не рекомендуется расширять права - это приветет к уязвимости
         *  @sa http://skycase.ru/blog/securing-your-php-sites-hosting/ 
         *  настройка прав доступа к сайтам, обслуживаемых скриптом .. 
          */
        function OpenFile($path,$name,$mode="r"){
        /*
            $userinfo = posix_getpwuid(posix_getuid());
            echo "<br>".$userinfo['name'];//выведет имя пользователя, от которого работает PHP
        */    
            try {
                $handle = fopen($path.$name,$mode);
                if ($handle===false) {                
                    throw new Exception('File open failed.');
                }
                return $handle;
            } catch ( Exception $e ) {
                    /** 
                     * @todo generate error to log
                     */
                     print_r(error_get_last());
            }
        }
  
?>