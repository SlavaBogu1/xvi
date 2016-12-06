<?php
/** @file  xvi_sitemapgen.php
    Generate sitemaps
  */
   define('_XVI',"../engine/");      #Set the engine path and the engine flag
/**
 * @brief Sitemap generation is a tool to check all deployed sites and generate sitemap.xml
 * 0 - read the number of sites. 
 * foreach:
 * 1 - read site config from DB
 * 2 - load site DB parameters
 * 3 - read all records from CONTENT
 * 4 - generate sitemap.xml
 * 5 - store sitemap.xml 
 */
    require_once(_XVI."xvi_config.php"); 
    require_once(_XVI."passwords.php"); 

 /**
  Load Database interfaces
  configuration and content
*/
    require_once(_XVI."xvi_clDB.php");
    require_once(_XVI."xvi_cDB_basic.php");
    require_once(_XVI."xvi_cDB_sites.php");
    require_once(_XVI."xvi_cDB_eng.php");        
    require_once(_XVI."xvi_clContent.php");
    
    $db = cXVI_db::getInstance();
    
    $jsn = ReadJSON_description($db);
    //print_r ($jsn);
    
    $sites_list = GetTheListOfSites($jsn);
    print_r ($sites_list);
 
    /*
    foreach($sites_list as $sitename) {
        $pages_list = GetTheListOfSites($sitename);
    }*/
    
    exit (0);
    
    function ReadJSON_description($db){
        return  $db->ReadDBKey(DB_SOURCE_ENGINE,'sitelist'); 
    }
    
    function GetTheListOfSites($jsn){
        $list = json_decode($jsn,true);
        //print_r ($list);
        return  array_keys($list);
        
    }
  
?>