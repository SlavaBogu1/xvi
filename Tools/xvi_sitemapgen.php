<?php
/** @file  xvi_sitemapgen.php
    Generate sitemaps
  */
   define(_XVI,"../engine/");      #Set the engine path and the engine flag
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
   define(PUBHTML,"/var/tmp/"); //default path to publichtml folder. need to solve issue with access rights
   
    require_once(_XVI."xvi_config.php"); 
    require_once(_XVI."passwords.php"); 

    require_once(_XVI."xvi_ff.php");
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
    
    $sites_description =  json_decode($jsn,true);    
    $sites_list = array_keys($sites_description);
  
    foreach ($sites_description as $site_name => $site_cfg) {
        $pages = GetTheListOfPages($site_cfg[0]["content"],$db);
        /** @todo Set path and name
         */
        $path = PUBHTML.$site_cfg[0]["publichtml"];
        $file_handle = OpenFile($path,"sitemap.xml","w+");

        if ($file_handle) {
            $pages_xml = GenerateXML_Sitemap($pages);

            fwrite($file_handle, $pages_xml);
            fclose($file_handle);
        } else {
            echo "<br>File".$path."sitemap.xml was not created";
        }
    }
    
    /*
        for($i = 0; $i < array_count_values ($sites_list ); $i++) {
        $pages_list = GetTheListOfPages($sites_list[$i]);
    }*/
    
    exit (0);
    
    function GenerateXML_Sitemap($pages){
        $res =<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
EOF;
        for ($i=0;$i<count($pages);$i++){
              $res .="\n";
              $res .="<url>\n<loc>http://".$pages[$i]."</loc>\n";
              $res .="<changefreq>weekly</changefreq>\n";
              $res .="<priority>1.00</priority>\n";
              $res .="</url>";
        }
        $res .= "\n</urlset>\n";
        return $res;
    }


    function ReadJSON_description($db){
        return  $db->ReadDBKey(DB_SOURCE_ENGINE,'sitelist'); 
    }
    
    /* Call stored procedure and get the string of pages */
    function GetTheListOfPages($key,$my_db){
        /* Get list of pages from site content table
         * pages are in the single string, ";" separated
         * the first name usually is default "root"-page;
         */
        $pages = $my_db->CallDB_SP(DB_SOURCE_ENGINE,'GetSitemapList',$key);
        return explode(";",$pages);
    }
?>