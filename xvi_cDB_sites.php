<?php
/**
  \addtogroup db
  @{

 * Description of xvi_cDB_sites
 * This class connect to the SITE database and provide methods to request data from it.
 * @brief SITE Database singleton declaration    
 
 * @author SlavaBogu1
 */

class cXVI_db_sites extends cXVI_db_basic{
    private static $_instance;

    /**
        @brief Default constructor with DB parameters set by default constants
    */
    protected function __construct(){
        parent::__construct(DBS_USER,DBS_PSW,DBS_NAME,DBS_KEY,DBS_VAL);
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
      @brief Write New/Update the key
      default_db_config - is the table set by dfefault.
    */
   public function WriteDBKey($key, $val){
        return parent::WriteDBKey(DBS_TABLE, $key, $val);
    }

    /**
      @brief Read from DB table key
      default_db_config - is the table set by dfefault.
    */
   public function ReadDBKey($key){
        return parent::ReadDBKey(DBS_TABLE,$key);
    }        

} //End of class cXVI_db_sites

/*@}*/
?>
