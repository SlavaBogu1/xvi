<?php
/**
 * @page config
 * Configuration parameters for DB access
 * @brief DB interface parameters
 */
defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

#database info used by the site
define('DBS_NAME', 'xvi_sites');
define('DBS_USER', 'xvi_user');
define('DBS_PSW', XVI_DBS_PWD); // don't forget to add your password here

/**
 @brief list of tables in the database
*/
define('DBS_TABLE', 'site_1_config');
define('DBS_KEY', 'cfgkey');
define('DBS_VAL', 'cfgval');


define('DBS_CONTENT_TABLE', 'site_1_content');
define('DBS_CONTENT_KEY', 'key');
define('DBS_CONTENT_VAL', 'content');

/**
 @brief ID of records in the xvi_cfg table
 to access by ID, not use "search and fetch"
*/
#define('CFG_USER_NOT_SUPP_ID', 2);
#define('CFG_ADMIN_PAGE_REQ_ID', 3);
	
?>