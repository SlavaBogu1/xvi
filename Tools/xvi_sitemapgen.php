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
    
    GenerateSiteMap();
    
    exit (0);
    
    function GenerateSiteMap(){
        $db = cXVI_db::getInstance();

        $jsn = ReadJSON_description($db);
        $sites_info = json_decode($jsn,true);
        $sites_names = array_keys($sites_info);

        $i = -1;
        foreach($sites_info as $site){
            $i++;
            LoadSiteParameters($sites_names[$i], $site);
        }
        
    }
    
    function ReadJSON_description($db){
        return  $db->ReadDBKey(DB_SOURCE_ENGINE,'sitelist'); 
    }
    
    function LoadSiteParameters($name, $site){
        return 0;
    }
    
?>