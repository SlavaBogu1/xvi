<?php

/* 
 * The xvi_API class is the interface to major internal functions in the CMS
 * Through this interface external modules can get access to CMS parameters and functions
 * 
 * This API created to make module writing more convenient and easy
 */

/**
    @brief Internal API to plug-in modules
 * API proide wrapper-functions to call other methods or get values from other classes
 * this is just for convenience because external modules may include just a single API interface which will transfer requests to actuall function calls
 * API functions are easy to maintain than fix all function calls in each module.
*/
class xvi_API {
    private static $_instance;
    private static $request;
    private static $engine;
    
    function __construct(){
        self::$request = cXVI_Request::getInstance();
        self::$engine = cXVI_engine::getInstance();
    }
    /*  function __destruct(){ } */
    private function __clone(){ }    
    public static function getInstance() {
        if (null === self::$_instance) {
          self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function GetRequestAddr(){
        return self::$request->GetAddrStr(); 
    }
    
    public static function GetSiteContent(){
        return self::$engine->API_GetSiteContent();
    }
    
    public static function GetSiteMenu(){
        return self::$engine->API_GetSiteMenu();
    }
    public static function GetPageMenu(){
        return self::$engine->API_GetPageMenu();
    }
    public static function ReadFromSiteContent($key){
        return self::$engine->API_SiteReadJSON_array($key);
    }
    public static function ReadFromPageContent($key){
        return self::$engine->API_PageReadJSON_array($key);
    }
    public static function ReadFromPageContentValue($key){
        return self::$engine->API_PageReadJSON_value($key);
    }
    
    
} // end of class xvi_API

?>
