<?php
/** @xvi_clTemplate.php  
 *    Load HTML template
*/
defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

/**
#   @brief This is hardcoded DEFAULT_HTML_TEMPLATE pattern    
*/
define('DEFAULT_HTML_TEMPLATE',"{_PH_DOCTYPE_}<html>{_PH_HEAD_}{_PH_BODY_}</html>");

/**
  @brief This is external module loading
  @todo I need to replace it by loading a template memorandum from a XML/JSON file 
 * @sa http://nitschinger.at/Handling-JSON-like-a-boss-in-PHP/
 * @sa http://www.elated.com/articles/json-basics/
 * 
 */
class cXVI_Template {
    private static $_instance;
    private static $html_tmplt;
    /**
    @brief Load HTML template from file
    */
    protected function __construct($name){
         /** @todo нужно указать реальный путь работающего скрипта
         *         $path = realpath(dirname(__FILE__));
                    $name = $path."/".$name;

         */

        $res = file_get_contents ($name);
        if($res){
		self::$html_tmplt = $res; //load template if exist
	} else {
		self::$html_tmplt = DEFAULT_HTML_TEMPLATE; 
	}
    }    

    /*  function __destruct(){ } */
    private function __clone(){ }    
    public static function getInstance($name) {
        if (null === self::$_instance) {
          self::$_instance = new self($name);
        }
        return self::$_instance;
    }
    
    public static function getHTML_Template() {
        return self::$html_tmplt;
    }
} // End of class cXVI_Template

?>