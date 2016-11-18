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

    function __construct(){
        self::$request = cXVI_Request::getInstance();
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
    
    
} // end of class xvi_API

?>