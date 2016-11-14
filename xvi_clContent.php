<?php
/* 
  \addtogroup db
  @{

 * @brief Class to manage site content
 *  Interface to DB and some processing
 *  @todo add cache
 */
    class cXVI_db_content extends cXVI_db_basic{
        private static $_instance;

        /**
            @brief Default constructor with DB parameters set by default constants
        */
        protected function __construct(){
            parent::__construct(DBS_USER,DBS_PSW,DBS_NAME,DBS_CONTENT_KEY,DBS_CONTENT_VAL);
        }    
        /* 
         * function __destruct(){ }
        */

        /**
         @brief Standard implementation of Singletone (_clone and getInstance)
             To protect from second connection creation
        */
        private function __clone(){ }

        public static function getInstance() {
            // check if instance already exist
            if (null === self::$_instance) {
              // create new instance
              self::$_instance = new self();
            }      
            #self::$_instance->copyDBname();
            return self::$_instance;
        }

        /**
          @brief Write New/Update the key at CONTENT DB          
        */
        public function WriteDBKey($key, $val){
            parent::WriteDBKey(DBS_CONTENT_TABLE, $key, $val);
        }

        /**
          @brief Read from DB table key
          default_db_config - is the table set by dfefault.
        */
        public function ReadDBKey($key){
            return parent::ReadDBKey(DBS_CONTENT_TABLE,$key);
        }   
        
        /** @brief Get content from the database
         *   @param $path identified the key in the database to read
         *   @todo Add cache support
         */
        public function getContent($path){
            return parent::ReadDBKey(DB_SOURCE_CONTENT,$path);
        }
        
    } // End of cXVI_db_content

/*@}*/
?>