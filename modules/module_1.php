<?php
/**
 * @brief Generate table of function calls in this module
 *  Engine loading the class (same name as file name) and call three static functions (extends cXVI_AbsModule)
 *  
 */

class module_1 extends cXVI_AbsModule{
    private static $_instance;
    private static $xvi_api;
    private $html_processing;

    /** Generate JSON description of this class and provide to module_queue for processing.
     *    For each PH there can be the only single array with class name.
     *    Priority is ignored here and shall set to 0.
     */
    public static function Register() {
        $module_1_queue_json =<<< EOF
{               
"TEST_EMPTY": [{ "class":"module_1", "priority":"0" }],                
"PH_DOCTYPE":[{ "class":"module_1", "priority":"0" }],
"PH_DEMO": [{ "class":"module_1", "priority":"0" }],
"PH_BODY": [{ "class":"module_1", "priority":"0" }],
"PH_DOCTYPE": [{ "class":"module_1", "priority":"0" }],
"PH_LANG": [{ "class":"module_1", "priority":"0" }],
"PH_HEAD": [{ "class":"module_1", "priority":"0" }],
"PH_TITLE": [{ "class":"module_1", "priority":"0" }],
"PH_META": [{ "class":"module_1", "priority":"0" }]
}
EOF;
        return $module_1_queue_json;        
    }
    public static function Call($placeholder_id) {
            switch ($placeholder_id) {
                case 'TEST_EMPTY':
                    return self::PH_TestEmpty();
                case 'PH_DEMO':
                    return self::PH_Demo();
                case 'PH_BODY':
                    return self::PH_Body();
                case 'PH_DOCTYPE':
                    return self::PH_Doctype();
                case 'PH_LANG':
                    return self::PH_Language();
                case 'PH_META':
                    return self::PH_Meta();
                case 'PH_HEAD':
                    return self::PH_Head();
                case 'PH_TITLE':
                    return self::PH_Title();
                default:
                    return self::PH_Clear();
            }
    }
    
    private function Parse(&$content,$pattern){
    }   
    
    function __construct(){
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
    
    /** Replace unknown placeholder by empty string
     */
    private function PH_Clear(){
        return "";
    }
    
    private function PH_TestEmpty(){
         return "Empty test at module_1.php<br>";
   }
    
    private function PH_Demo(){
        $request_ip_addr =self::$xvi_api->GetRequestAddr();
        $res = "<p> The incoming request address is:".$request_ip_addr."</p>";
        return $res;
    }
    
    private function PH_Body(){
	return  "<h1>CMS engine default page.</h1><br> Have questions? <a href='mailto:admin@domain.com?Subject=CMS%20question' target='_top'>Contact me.</a>";
    }

    private function PH_Doctype(){
	return "<!DOCTYPE html>";
    }
    function PH_Language(){
        // TODO - get the page language code
        $id = 0;
        switch ($id) {
            case 0:
                $repl = ' lang="ru"';
                break;
            case 1:
                $repl = ' lang="en"';
                break;
            default:
                $repl = ' lang="zh"';
                break;
        }
        return $repl;
    }

    private function PH_Meta(){
	return "<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'>";
    }

    private function PH_Head(){
        $res =<<< EOF
      <head>
        <title>{_PH_TITLE_}</title>
        {_PH_META_}
      </head>
EOF;
        return $res;
    }
    private function PH_Title(){
        return "XVI default page";
    }
    
    
 } // end of class module_1
?>