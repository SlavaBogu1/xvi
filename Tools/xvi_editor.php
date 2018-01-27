<?php
/** @file  xvi_editor.php
       Web page editor, part of XVI  admin tools  
 *  1) шаблон страницы
 *  2) загрузить контент без рендеринга
 *  3) показать настройки страницы
 *  4) создать новую страницу / удалить
 */


  define('_XVI',"../../engine/");      #Set the engine path and the engine flag

    require_once(_XVI."xvi_config.php");
    require_once(_XVI."passwords.php"); // passwords must follow config, otherwise DB initialization failed (password not set);
    

    require_once(_XVI."xvi_clDB.php");
    require_once(_XVI."xvi_cDB_basic.php");
    require_once(_XVI."xvi_cDB_sites.php");
    require_once(_XVI."xvi_cDB_eng.php");        
    require_once(_XVI."xvi_clContent.php");

    try {
        $template_path = TEMPLATE_PATH.'editor.html';
        $editor_html_page = file_get_contents ($template_path);
        if($editor_html_page===false){
            throw new Exception('Can not read template' );
        }
    } catch ( Exception $e ) {
        print_r(error_get_last());
    }

    
    echo $editor_html_page;
    exit(0);

 
?>
