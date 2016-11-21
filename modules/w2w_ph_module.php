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
        return "";
    }

    private function PH_CSS(){
        return "<link rel=\"stylesheet\" href=\"/".PUBLIC_HTML."/css/e_style.css\" type=\"text/css\" media=\"screen\">";
     }
 
}

  
?>