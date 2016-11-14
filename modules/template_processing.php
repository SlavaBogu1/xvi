<?php

#global $patterns_functions;

/**
 * @brief Generate table of function calls in this module
 * 
 */
function get_fmap_template_processing(){
    $fmap = [
        "PLACEHOLDER" => "templatePH_Parse",
        "PH_DOCTYPE" => "PH_Doctype_Parse",
        ];
    return $fmap;
}

/**
{_PLACEHOLDER_} - test of PLACEHOLDER replacement
*/
###$patterns_functions['PLACEHOLDER'] = 'templatePH_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function templatePH_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "<h1>This is demo of placeholder replacement</h1>";
        $repl = $repl."Quick brown fox jump over the lazy dog<br>";
        $repl = $repl."съешь же ещё этих мягких французских булок, да выпей чаю<br>";
	$content = str_replace($tpl, $repl, $content);
}

/**
{_PH_DOCTYPE_} - Specify the Doctype of the document
*/
##$patterns_functions['PH_DOCTYPE'] = 'PH_Doctype_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Doctype_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "<!DOCTYPE html>";
	$content = str_replace($tpl, $repl, $content);
}

/**
{_PH_LANG_} - Add meta tags
 * @sa http://www.w3schools.com/tags/ref_language_codes.asp
*/
##$patterns_functions['PH_LANG_NUMBER'] = 'PH_Lang_Parse';
function PH_Lang_Parse(&$content,$pattern,$id){
    $tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
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
    $content = str_replace($tpl, $repl, $content);
}

/**
{_PH_META_} - Add meta tags
*/
##$patterns_functions['PH_META'] = 'PH_Meta_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Meta_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'>";
	$content = str_replace($tpl, $repl, $content);
}


/**
{_PH_HEAD_} - Default HTML page header
*/
##$patterns_functions['PH_HEAD'] = 'PH_Head_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Head_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "<head><title>{_PH_TITLE_}</title>{_PH_META_}</head>";
	$content = str_replace($tpl, $repl, $content);
}


/**
{_PH_TITLE_} - Default page title
*/
##$patterns_functions['PH_TITLE'] = 'PH_Title_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Title_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "XVI default page";
	$content = str_replace($tpl, $repl, $content);
}

/**
{_PH_BODY_} - Default page body content
*/
#$patterns_functions['PH_BODY'] = 'PH_Body_Parse';
/**
* @brief Plugin function to replace pattern in template by actual
* @details 
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Body_Parse(&$content,$pattern){
	$tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
	$repl = "<h1>CMS engine default page.</h1><br> Have questions? <a href='mailto:admin@domain.com?Subject=CMS%20question' target='_top'>Contact me.</a>";	
	$content = str_replace($tpl, $repl, $content);
}

#$patterns_functions['PH_DEMO'] = 'PH_Demo';
/**
* @brief This is demo module to show how to call main classes
* @details We can create an instance of any main class and use it to get any internal parameters.
 * @todo This is a potential security hole for module architecture.
* @param $content parameter is the reference to the string
* @param $pattern parameter is the pattern name, without curly bracets
*/
function PH_Demo(&$content,$pattern){
    $tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
    
    $request = cXVI_Request::getInstance();
    $repl = "<p> The incoming request address is:".$request->GetAddrStr()."</p>";
    $content = str_replace($tpl, $repl, $content);
}


/** New Module interface via classes. 
 *  Engine loading the class (same name as file name) and call three static functions (extends cXVI_AbsModule)
 *  
 */
class template_processing extends cXVI_AbsModule{
    private static $_instance;
    private $html_processing;

    /** Generate JSON description of this class and provide to module_queue for processing.
     *    For each PH there can be the only single array with class name.
     *    Priority is ignored here and shall set to 0.
     */
    public static function Register() {
        $template_processing_queue_json =<<< EOF
{               
"TEST_EMPTY": [{ "class":"template_processing", "priority":"0" }],                
"PH_DEMO": [{ "class":"template_processing", "priority":"0" }],
"PH_BODY": [{ "class":"template_processing", "priority":"0" }]
}
EOF;
        return $template_processing_queue_json;        
    }
    public static function Call($placeholder_id) {
            switch ($placeholder_id) {
                case PH_DEMO:
                    $this->PH_Demo($placeholder_id);
                    break;
                case PH_BODY:
                    $this->PH_Body_Parse($placeholder_id);
                    break;
                default:
                    $this->PH_Clear($placeholder_id);
                    break;
            }
    }
    
    public static function CallN($placeholder_id, $number){
    }

    private function Parse(&$content,$pattern){
    }   
    
    function __construct(){
        //$this->$site_instance = cXVI_engine::getInstance();
        //$this->Register();
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
    private function PH_Clear($placeholder_id){
        $site_inst = cXVI_engine::getInstance();
       
        $tpl = OPEN_PATTERN_SIGN.$placeholder_id.CLOSE_PATTERN_SIGN;
        $repl = "";
        
        if (isset($site_inst->generated_content[$tpl])) {
            $site_inst->generated_content[$tpl] = $site_inst->generated_content[$tpl].$repl;
        } else {
            $site_inst->generated_content[$tpl] = $repl;
        }        
    }
    
    private function PH_Demo($placeholder_id){
        $site_inst = cXVI_engine::getInstance();
        $request = cXVI_Request::getInstance();
       
        $tpl = OPEN_PATTERN_SIGN.$placeholder_id.CLOSE_PATTERN_SIGN;
        $repl = "<p> The incoming request address is:".$request->GetAddrStr()."</p>";
        
        if (isset($site_inst->generated_content[$tpl])) {
            $site_inst->generated_content[$tpl] = $site_inst->generated_content[$tpl].$repl;
        } else {
            $site_inst->generated_content[$tpl] = $repl;
        }       
    }
    
    private function PH_Body_Parse($placeholder_id){
       $site_inst = cXVI_engine::getInstance();
         
	$tpl = OPEN_PATTERN_SIGN.$placeholder_id.CLOSE_PATTERN_SIGN;
	$repl = "<h1>CMS engine default page.</h1><br> Have questions? <a href='mailto:admin@domain.com?Subject=CMS%20question' target='_top'>Contact me.</a>";	
        if (isset($site_inst->generated_content[$tpl])) {
            $site_inst->generated_content[$tpl] = $site_inst->generated_content[$tpl].$repl;
        } else {
            $site_inst->generated_content[$tpl] = $repl;
        }  
    }

    
}


?>