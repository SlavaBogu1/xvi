<?php
  /** @file  xvi_config.php
    This file contain enigne-wide configuration parameters and constants.
    All specific values must be at SIDE_ID_cfg
  */
  defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

  /**
    @brief Start/Stop engine
	To Start engine and ALL sites set the key below to TRUE
	To Stop engine and ALL sites set the key below to FALSE
	Egnine will generate 503 code.
  */
  define('XVI_ENGINE_RUNNING',true); 
   
  #Turn DEBUG mode ON/OFF
  define('DEBUG_MODE_ON',true);

  /**
   SITES_CFG_PATH is the name of folder where sites specific configurations are stored	
   */
  define('SITES_CFG_PATH',_XVI."sites/");  

  /**
   TEPLATE_PATH is the name of folder where HTML templates of the page are stored
   @note separate pages can have different templates. Moreover template may contain HTML,CSS and other files. 
   */
  define('TEPLATE_PATH',_XVI."templates/");
 
  /** 
   Define pattern parser folder and parameters
   patterns allow to replace labels by generated code.
   @todo patterns should depend on project!!!
   @todo when enter to content online - such symbols must be replaced by /xnnn codes.
   */
  define('PATTERN_PARSERS' ,_XVI."modules/");
  define('OPEN_PATTERN_SIGN', '{_');  // &#123;&#95; \x7b\x5f
  define('CLOSE_PATTERN_SIGN', '_}'); // &#95;&#125; \x5f\x7d

  
  
  define('CACHE_PATH' ,_XVI."/cache");
  /** @brief To support dynamic content in cache engine will replace PH marks by these one
  *  later, if page is exist in the cache - engine should replace only these PH. Other content will remain.
  */
  define('OPEN_CACHE_PH_SIGN', '[_');  // &#91;&#95; \x5b\x5f
  define('CLOSE_CACHE_PH_SIGN', '_]'); // &#95;&#93; \x5f\x5d 

  
  /** @brief Configure where to target log output
   *  either to a file system or to DB table
   */
  define('LOG_TO_DB' ,false);
  define('LOGS_PATH' ,_XVI."/log"); // if  LOG_TO_DB false
  define('LOGS_TABLE' ,"def_log");  // if  LOG_TO_DB true
  
 
  /** 	define time constants   */
  define('MIN_IN_SEC',60);  
  define('HOUR_IN_SEC',3600);  
  define('DAYS_IN_SEC',24 * HOUR_IN_SEC);
  define('WEEKS_IN_SEC',7 * DAYS_IN_SEC);
  define('MONTH_IN_SEC',31 * DAYS_IN_SEC);
  
  /** 	define cookie valid period */
  define('SET_COOKIE_VALID_PERIOD',2 * WEEKS_IN_SEC);  
  
  /** DB field names constants
   */
  define('FIELD_OPTIONS','OPTIONS');
  define('FIELD_CONTENT','CONTENT');
  define('FIELD_TAGS','TAGS');
  define('FIELD_MENU','MENU');
  
?>