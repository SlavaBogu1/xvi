<?php
/** External module example #2 
 *  Engine loading the class (same name as file name) and call three static functions (extends cXVI_AbsModule)
 */
class w2w_ph_module extends cXVI_AbsModule{
    private static $_instance;
    private static $xvi_api;

    /** Generate JSON description of this class and provide to module_queue for processing.
     *    For each PH there can be the only single array with class name.
     *    Priority is ignored here and shall set to 0.
     */
    public static function Register() {
        $module_queue_json =<<< EOF
{
"W2W_CSS":                      [{ "class":"w2w_ph_module", "priority":"0" }],
"W2W_JS":                         [{ "class":"w2w_ph_module", "priority":"0" }],
"PH_SITE_HEADER":           [{ "class":"w2w_ph_module", "priority":"0" }],
"SITE_FOOTER":                 [{ "class":"w2w_ph_module", "priority":"0" }],
"CONTENT_MAIN_XLINKS": [{ "class":"w2w_ph_module", "priority":"0" }],
"FAVICON":                         [{ "class":"w2w_ph_module", "priority":"0" }],
"PAGE_MENU":                    [{ "class":"w2w_ph_module", "priority":"0" }],
"PH_GA_ID":                       [{ "class":"w2w_ph_module", "priority":"0" }],                
"TEST_EMPTY":                   [{ "class":"w2w_ph_module", "priority":"0" }]
}
EOF;
        return $module_queue_json;        
    }
    
    public static function Call($placeholder_id) {
            switch ($placeholder_id) {
                case 'W2W_CSS':
                    return self::PH_CSS();
                case 'W2W_JS':
                    return self::PH_JS();
                case 'TEST_EMPTY':
                    return self::PH_Clear();
                case 'PH_SITE_HEADER':
                    return self::PH_SiteHeader();
                case 'SITE_FOOTER':                    
                    return self::PH_SiteFooter();
                case 'CONTENT_MAIN_XLINKS':
                    return self::PH_SiteCrossLinks();
                case 'FAVICON':
                    return self::PH_Favicon();
                case 'PAGE_MENU':
                    return self::PH_PageMenu();
                case 'PH_GA_ID':
                    return self::PH_GoogleID();       
                default:
                    return self::PH_Clear();
            }
    }
    
    function __construct(){
        self::Register();
        self::$xvi_api =xvi_API::getInstance();
    }
    /*  function __destruct(){ } */
    private function __clone(){ }    
    public static function getInstance() {
        if (null === self::$_instance) {
          self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /** 
     * @brief Replace unknown placeholder by empty string
     *  this is default function, don't delete it
     */
        private function PH_Clear(){
            return "";
        }
        
        /**
         * Menu maker
         * take list of menu item form SITE parameters and from PAGE paramenter and generate menu
         */
        private function PH_PageMenu(){
            $site_menu = self::$xvi_api->GetSiteMenu();
            $page_menu = self::$xvi_api->GetPageMenu();
            
            $menu ="";
            foreach($site_menu as $menu_item=>$menu_path) {
                if (strcmp($menu_item,$page_menu["item"])) { /*not selected menu item*/
                    $menu .= "<li><a href=\"".$menu_path."\"<span></span>".$menu_item."</a></li>";
                } else { /* it is selected menu*/
                    $menu .= "<li><a class=\"sel\" href=\"".$menu_path."\"<span></span>".$menu_item."</a></li>";
                }
            }
            return $menu;
        }

        private function PH_CSS(){
            return "<link rel=\"stylesheet\" href=\"/".PUBLIC_HTML."/css/e_style.css\" type=\"text/css\" media=\"screen\">";
        }

         private function PH_JS(){
            $res = "<script src=\"/".PUBLIC_HTML."/js/jquery-2.1.3.min.js\"></script>";
            $res .="<script src=\"/".PUBLIC_HTML."/js/readmore.min.js\"></script>";
            return $res;
        }
 
        private function PH_Favicon(){
            return "<link rel=\"icon\" type=\"image/ico\" href=\"/".PUBLIC_HTML."/favicon.ico\" />";
        }

     private function PH_SiteHeader(){
        $res =<<<EOF
		<div id="hdr-overlay"></div> 
		<div id="hdr-box1" class="box">Видео</div> 
		<div id="hdr-box2" class="box">Путешествия</div> 
		<h1>Бесплатно</h1>  
		<h2>Это <br> стоит увидеть</h2> 
EOF;
        return $res;
    }
    
    private function PH_SiteCrossLinks(){
        $site_xlinks = self::$xvi_api->ReadFromSiteContent(FIELD_XLINKS);
        $page_tags = self::$xvi_api->ReadFromPageContentValue(FIELD_PAGE_TAGS);
        
        if (empty($page_tags) OR empty($site_xlinks)) {
            return null;
        }
        
        //generate list of cross-site references to other pages
        $path = PUBLIC_HTML;
        $res =<<<EOF
<h4>Тематические подборки видео</h4> 
    <ul> 
EOF;
        
        /** @todo Поставить проверку на пустые масссивы
         */
        
        $xlink_names = array_keys($site_xlinks);
        $i = -1;
        foreach($site_xlinks as $xlink){
            $i++;
            $xlink_exist = false;
            foreach($page_tags as $tag){
                if (in_array($tag, $xlink[1]["tags"])){
                    $xlink_exist = true;
                } 
            }
            if ($xlink_exist) {
                $res .= "<li><a href=\"".$path.$xlink_names[$i]."\">".$xlink[0]["name"]."</a></li>";
            }
        }
        $res .= "</ul>";
        return $res;
    }

    private function PH_SiteFooter(){
        //generate list of cross-site references to other pages
    $res =<<<EOF
            <a href="#">Lorem</a> |
            <a href="#">Ipsum</a> |
            <a href="#">Dolor</a> |
            <a href="#">Sit amet</a> |
            <a href="#">Aliquip</a> 
EOF;
        return $res;
    }
    
           private function PH_GoogleID(){
        $site_content =self::$xvi_api->GetSiteContent();
        $ga_code = $site_content['GA_CODE'];
        
        $res =<<< EOF
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '$ga_code', 'auto');
  ga('send', 'pageview');
      
    </script>
EOF;
    return $res;
    }
    
    
}
  
?>
