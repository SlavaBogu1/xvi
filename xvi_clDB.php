<?php
/** @file  xvi_clDB.php
    All DB related classes 
   \addtogroup db
  @{

  */
defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

require_once("xvi_dbcfg.php"); #load default config data	 
if (defined('DB_CONFIG')) { require_once(DB_CONFIG);}	// load site variable

/// @cond ALL
// DOXYGEN got confused with braces below. 
// exclude from processing be /cond option. 
// In the doxyfile need to change ENABLED_SEC to ALL or SOME to include/exclude these lines
defined('DBE_NAME') or eval('define(\'DBE_NAME\',XVI_DBE_NAME);');	
defined('DBE_PSW') or eval('define(\'DBE_PSW\',XVI_DBE_PWD);');         
defined('DBE_USER') or eval('define(\'DBE_USER\',XVI_DBE_USER);');
defined('DBE_TABLE') or eval('define(\'DBE_TABLE\',XVI_DBE_TABLE);');	

defined('DBS_NAME') or eval('define(\'DBS_NAME\',XVI_DBS_NAME);');	
defined('DBS_PSW') or eval('define(\'DBS_PSW\',XVI_DBS_PWD);');         
defined('DBS_USER') or eval('define(\'DBS_USER\',XVI_DBS_USER);');
defined('DBS_TABLE') or eval('define(\'DBS_TABLE\',XVI_DBS_TABLE);');	
/// @endcond

/**
   @page Database Connect to DB   
   @brief DB interface using mysqli
   this class incapsulate both ENGINE and SITES database interfaces and functions.
 
   
   @todo prevent access to secure storage for non-registered users
   @todo database to store site runtime log?
   @todo Create db template for deployment (default settings)
 */
    class cXVI_db {
        private static $_instance;
        private $eng_db, $sites_db, $content_db;

        private function __construct(){
            $this->eng_db = cXVI_db_eng::getInstance();
            $this->sites_db = cXVI_db_sites::getInstance();
            $this->content_db = cXVI_db_content::getInstance();
        }

        /*  function __destruct(){ } */
        private function __clone(){ }    
        public static function getInstance() {
            if (null === self::$_instance) {
              self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        public function CallDB_SP($db_src, $sp, $key){
            switch ($db_src) {
                case DB_SOURCE_CONTENT:
                    return $this->content_db->CallDB_SP($sp, $key);
                case DB_SOURCE_ENGINE:
                    return $this->eng_db->CallDB_SP($sp, $key);
                case DB_SOURCE_SITES:
                    return $this->sites_db->CallDB_SP($sp, $key);
                default:
                    /** @todo wrong DB source */
                    break;
            }
        }

        public function ReadDBKey($db_src, $key) {
            switch ($db_src) {
                case DB_SOURCE_CONTENT:
                    return $this->content_db->ReadDBKey($key);
                case DB_SOURCE_ENGINE:
                    return $this->eng_db->ReadDBKey($key);
                case DB_SOURCE_SITES:
                    return $this->sites_db->ReadDBKey($key);
                default:
                    /** @todo wrong DB source */
                    break;
            }

        }

        public function WriteDBKey($db_src, $key, $val) {
            switch ($db_src) {
                case DB_SOURCE_CONTENT:
                    return $this->content_db->WriteDBKey($key, $val);
                case DB_SOURCE_ENGINE:
                    return $this->eng_db->WriteDBKey($key, $val);                    
                case DB_SOURCE_SITES:
                    return $this->sites_db->WriteDBKey($key, $val);
                default:
                    /** @todo wrong DB source  */
                    return true;
            }
        }

        public function getSiteRunStatus(){
                $res = $this->sites_db->ReadDBKey(CFG_SITE_WORKING_KEY);
                if (!$res) {
                    return false;
                }
                return true;
        }
        public function setSiteRunStatus($run_status){
            return $this->sites_db->WriteDBKey(CFG_SITE_WORKING_KEY, $run_status);                    
        }

        public function getEngineRunStatus(){
                $res = $this->eng_db->ReadDBKey(CFG_ENGINE_WORKING_KEY);
                if (!$res) {
                    return false;
                }
                return true;            
        }

        public function setEngineRunStatus($run_status) {
            return $this->eng_db->WriteDBKey(CFG_ENGINE_WORKING_KEY, $run_status);
        }

    } //End of class cXVI_db
  
/*@}*/
?>