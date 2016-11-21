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
"W2W_CSS": [{ "class":"w2w_ph_module", "priority":"0" }],
"PH_SITE_HEADER": [{ "class":"w2w_ph_module", "priority":"0" }],
"SITE_FOOTER": [{ "class":"w2w_ph_module", "priority":"0" }],
"CONTENT_MAIN_XLINKS": [{ "class":"w2w_ph_module", "priority":"0" }],
"TEST_EMPTY": [{ "class":"module_1", "priority":"0" }]
}
EOF;
        return $module_queue_json;        
    }
    
    public static function Call($placeholder_id) {
            switch ($placeholder_id) {
                case 'W2W_CSS':
                    return self::PH_CSS();
                case 'TEST_EMPTY':
                    return self::PH_Clear();
                case 'PH_SITE_HEADER':
                    return self::PH_SiteHeader();
                case 'SITE_FOOTER':                    
                    return self::PH_SiteFooter();
                case 'CONTENT_MAIN_XLINKS':
                    return self::PH_SiteCrossLinks();
                default:
                    return self::PH_Clear();
            }
    }
    
    function __construct(){
        self::Register();
        $xvi_api =xvi_API::getInstance();
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
        return "ccc";
    }

    private function PH_CSS(){
        return "<link rel=\"stylesheet\" href=\"/".PUBLIC_HTML."/css/e_style.css\" type=\"text/css\" media=\"screen\">";
     }
     
    private function PH_SiteHeader(){
        $res =<<<EOF
        <div id="header"> 
		<div id="hdr-overlay"></div> 
		<div id="hdr-box1" class="box">Видео</div> 
		<div id="hdr-box2" class="box">Путешествия</div> 
		<h1>Бесплатно</h1>  
		<h2>Это <br> стоит увидеть</h2> 
	</div>
EOF;
        return $res;
    }
    
    private function PH_SiteCrossLinks(){
        //generate list of cross-site references to other pages
        $res =<<<EOF
<h4>Тематические подборки видео</h4> 
    <ul> 
            <li><a href=\"/../video/video-boevik.html\">Кино для мужской компании</a></li> 
            <li><a href=\"/../video/video-crazy.html\">Необычные персонажи</a></li> 
            <li><a href=\"/../video/video-izmena.html\">Про страсть и измены</a></li>
            <li><a href=\"/../video/video-jc.html\">Фильмы с Джимом Керри</a></li> 
            <li><a href=\"/../video/video-jd.html\">Джонни Дэпп - лучший</a></li> 
            <li><a href=\"/../video/video-lonely.html\">Всё про одиночество</a></li> 
            <li><a href=\"/../video/video-love.html\">Про любовь и чувства</a></li> 
            <li><a href=\"/../video/video-motivate.html\">Фильмы для мотивации</a></li>
            <li><a href=\"/../video/video-mystique.html\">Мистические картины</a></li>
            <li><a href=\"/../video/video-poker.html\">Любителям игры в покер</a></li> 
    </ul>
EOF;
        return $res;
    }

    private function PH_SiteFooter(){
        //generate list of cross-site references to other pages
    $res =<<<EOF
    <div id="footer"> 
            <a href="#">Lorem</a> |
            <a href="#">Ipsum</a> |
            <a href="#">Dolor</a> |
            <a href="#">Sit amet</a> |
            <a href="#">Aliquip</a> 
    </div> 
EOF;
        return $res;
    }

    
 
}

  
?>