<?php
  /** @file xvi_fl.php  
	Load other engine files in pre-defined order.
  */
        defined('_XVI') or die('Engine is not initialized properly'.__FILE__);
/**
 @page Initialization XVI Engine run-time initialization
 @section load_php Load PHP files 
 Then we should check XVI_ENGINE_RUNNING - the configuration parameter that can start/stop the engine
 @subpage config "Engine configuration options" 
 */


/** 
 Load default engine config file
 This file contain engine-wide parameters. 
*/
        require_once(_XVI."xvi_config.php"); 
        require_once(_XVI."passwords.php"); 
  /**
 @section common Common functions
 Load generic functions. 
*/
        require_once(_XVI."xvi_comfunc.php");
  
  /**
	@brief verify global ENGINE_RUNNING key and stop if it is false
   *    This option duplicated in DB by CFG_ENGINE_WORKING_KEY 
   *    Use flag in config file only in case if any issues with DB.
  */
  if (!XVI_ENGINE_RUNNING){
        # @brief Generate 503 server responce
        GenHTTPHeader(503, 0);
        exit(0);
  }
  
    /**
     @section site-specifig Load site-specifig configuration file
     File name is combined from SITES_CFG_PATH and SITE_ID.
     In this file you shall specify:
      TEPLATE_NAME - the name of html template for this site
      SITE_DIR - subfolder in SITES_CFG_PATH to store site specific files
      DB_CONFIG - database configuration file
    */
	require_once(SITES_CFG_PATH.SITE_ID);
  
  
/**
 @section debug_cfg.php Debugging capabilities
 Load FirePHP debug wrapper
*/
        require_once(_XVI."xvi_dbgcfg.php");	  	    
        
/**
 @subsection xvi_ff.php File and directory processing functions
 Load generic file processing functions
*/
	require_once(_XVI."xvi_ff.php");
        

/**
 @subsection xvi_clModules.php External modules for pattern parsing.
 Inlined pattern processing
 Load external files and initialize the processing queue
*/
        require_once (_XVI."xvi_API.php");
	require_once(_XVI."xvi_clModules.php");

	
/**
 @subsection xvi_defhtmltmplt.php Default HTML template
 Load HTML template processing.
 Most likely to be eliminated
*/
        require_once(_XVI."xvi_clTemplate.php");
        require_once(_XVI."xvi_clAbsModule.php");
	
/**
 @subsection xvi_cRequest.php Load cXVI_Request class.
 Load IP REQUEST processing class. This class will gather all information about the incomming request
*/
        require_once(_XVI."xvi_clRequest.php");	
	
/**
 @subsection xvi_cDB.php Load database interface
 Load Database interfaces
 * configuration and content
*/
        require_once(_XVI."xvi_clDB.php");
        require_once(_XVI.'xvi_cDB_basic.php');
        require_once(_XVI.'xvi_cDB_sites.php');
        require_once(_XVI.'xvi_cDB_eng.php');        
        require_once(_XVI."xvi_clContent.php");

        
/**
 @subsection xvi_images.php Common functions
 Just sample of image processing
 TODO
*/
	require_once(_XVI."xvi_images.php");
    
	require_once (_XVI."xvi_mail.php");
?>