<?php
/** External module example #2 
 *  Engine loading the class (same name as file name) and call three static functions (extends cXVI_AbsModule)
 */
class module_2 extends cXVI_AbsModule{
    private static $_instance;

    /** Generate JSON description of this class and provide to module_queue for processing.
     *    For each PH there can be the only single array with class name.
     *    Priority is ignored here and shall set to 0.
     */
    public static function Register() {
        $module_2_queue_json =<<< EOF
{
"TEST_EMPTY": [{ "class":"module_2", "priority":"0" }],
"PH_DEMO": [{ "class":"module_2", "priority":"0" }]
}
EOF;
        return $module_2_queue_json;        
    }
    
    public static function Call($placeholder_id) {
            switch ($placeholder_id) {
                case TEST_EMPTY:
                    return self::PH_TestEmpty();
                case PH_DEMO:
                    return self::PH_Demo();
                default:
                    return self::PH_Clear();
            }
    }
    
    private function Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "test class";
	return str_replace($tpl, $repl, $content);
    }   
    
    function __construct(){
        self::Register();
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
        return "Empty test at module_2.php";
    }
    private function PH_Demo(){
        return "<br> Demo test at module_2.php <br>";
    }
 
 
}

  
?>