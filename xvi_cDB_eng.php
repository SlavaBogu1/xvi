<?php
/**
 \addtogroup db
  @{

  @brief ENGINE Database singleton declaration
  This is the DB interface for ENGINE-wide parameters
*/
class cXVI_db_eng extends cXVI_db_basic{
    private static $_instance;

    /**
        @brief Default constructor with DB parameters set by default constants
    */
    protected function __construct(){
        parent::__construct(DBE_USER,DBE_PSW,DBE_NAME,DBE_KEY,DBE_VAL);
    }    
    /* function __destruct(){ } */

    private function __clone(){ }

    public static function getInstance() {
      // check if instance already exist
      if (null === self::$_instance) {
        // create new instance
        self::$_instance = new self();
      }
      return self::$_instance;
    }

    /**
      @brief Write New/Update the key
      default_db_config - is the table set by dfefault.
    */
    public function WriteDBKey($key, $val){
        return parent::WriteDBKey(DBE_TABLE, $key, $val);
    }

    /**
      @brief Read from DB table key
      default_db_config - is the table set by dfefault.
    */
    public function ReadDBKey($key){
        return parent::ReadDBKey(DBE_TABLE, $key);
    }        
    public function CallDB_SP($sp, $key){
        return parent::CallDB_SP($sp, $key);       
    }


} //End of class cXVI_db_eng
/*@}*/
?>