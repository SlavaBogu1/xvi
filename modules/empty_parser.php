<?php
global $patterns_functions;

$patterns_functions['TEST_EMPTY'] = 'empty_Parse';
/**
* @brief Plugin function to replace pattern by empty space
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function empty_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "";
	$content = str_replace($tpl, $repl, $content);
}

/** New Module interface via classes. 
 *  Engine loading the class (same name as file name) and call three static functions (extends cXVI_AbsModule)
 *  
 */
class empty_parser extends cXVI_AbsModule{
    private static $_instance;
    private $html_processing;

    /** Generate JSON description of this class and provide to module_queue for processing.
     *    For each PH there can be the only single array with class name.
     *    Priority is ignored here and shall set to 0.
     */
    public static function Register() {
        $empty_parser_queue_json =<<< EOF
{
"TEST_EMPTY": [{ "class":"empty_parser", "priority":"0" }],
"PH_DOCTYPE": [{ "class":"empty_parser", "priority":"0" }]
}
EOF;
        return $empty_parser_queue_json;        
    }
    public static function Call($placeholder_id) {
        //self::$html_processing = self::Parse(cXVI_Template::getHTML_Template(),$placeholder_id);    
        echo $placeholder_id."<br>";
    }
    
    public static function CallN($placeholder_id, $number){
        $this->Call($placeholder_id);
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
    
}

  
?>