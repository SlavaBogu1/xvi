<?php
  /** @file  xvi_dbcfg.php
    This file contain database access constants
  */

defined('_XVI') or die('Engine is not initialized properly'.__FILE__);
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
  Define global, default DB engine-wide name, pwd and user
 * these names are used by-default. 
 * To use another names ou need to overload this constants in site-specific configuration file
*/
define('XVI_DBE_NAME', 'xvi_engine');
define('XVI_DBE_USER', 'xvi_user');
define('XVI_DBE_PWD', 'r6aqqwzexcSApHZa');
define('XVI_DBE_TABLE', 'eng_config');

/**
  Define global, default DB site-wide name, pwd and user
 * these names are used by-default. 
 * To use another names ou need to overload this constants in site-specific configuration file
*/
define('XVI_DBS_NAME', 'xvi_sites');
define('XVI_DBS_USER', 'xvi_user');
define('XVI_DBS_PWD', 'r6aqqwzexcSApHZa');
define('XVI_DBS_TABLE', 'def_config');

define('DBE_KEY', 'cfgkey');
define('DBE_VAL', 'cfgval');


/**
 * DB_SOURCE - either ENGINE DB ot SITES DB.
 */
define('DB_SOURCE_ENGINE', 1);
define('DB_SOURCE_SITES', 2);
define('DB_SOURCE_CONTENT', 3);

/**
 * @brief list of configuration keys 
 * @param CFG_SITE_WORKING_KEY Enable/disable this site (domain) while engine and other domains are running
 * @param CFG_ENGINE_WORKING_KEY Enable/disable the engine and ALL domains at once.
 */
define('CFG_SITE_WORKING_KEY', 'site_enable'); 
define('CFG_SITE_RUNNING', 1); 
define('CFG_SITE_STOPPED', 0);

define('CFG_ENGINE_WORKING_KEY', 'engine_enable');
define('CFG_ENGINE_RUNNING', 1); 
define('CFG_ENGINE_STOPPED', 0);

?>