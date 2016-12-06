<?php
/**
   \addtogroup db
    @{
    @brief Database common interface declaration    
    * This class connect to the ANY database and provide methods to read/write records there.
*/  
    class cXVI_db_basic{
        public $db = NULL;
        private $key_name, $val_name, $q;
        
        protected function __construct($dbusr,$dbpwd,$dbname,$k,$v){
            $this->key_name = $k;
            $this->val_name = $v;
            
            // tell mysqli to throw exceptions:
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            try {
                $this->db = new mysqli("localhost", $dbusr, $dbpwd, $dbname);
            } catch (Exception $e ) {
                echo "Sorry, Service is unavailable now";
                // @todo report error to log
                //echo "message: " . $e->message;
                exit(0);
            }       

/*            // for some reason this doesn't stop at hosting. So better to just exit    
            if ($this->db->connect_errno) {
                //GenHTTPHeader(403,mysqli_connect_errno());
                exit(0);
            }
  */          
     
            /** @brief Fix the issue with Cyrillic charset 
             *   without this query return "????"             
            $this->QueryDB("SET character_set_client = utf8");
            $this->QueryDB("SET character_set_connection = utf8");
            $this->QueryDB("SET character_set_results = utf8");
             * or equivalent SET NAMES
             * @sa http://dev.mysql.com/doc/refman/5.7/en/charset-connection.html 
             */ 
            $this->QueryDB("SET NAMES utf8");
        }   
           
        private function QueryDB($q){
                if (!$res = $this->db->query($q)) {
                        /** @todo if Query failed;
                         */
                        #GenHTTPHeader(501,"Wrong DB query at ".print_r(debug_backtrace(),true));
                        GenHTTPHeader(501,"Wrong DB query at ".__FUNCTION__);
                        exit(0);
                }
        }

        protected  function WriteDBKey($table,$key, $val){
            /**
             * @note (MySQL) To use ON DUPLICATE KEY UPDATE the record must be added as a UNIQUE. Otherwise INSERT will add new record.
             * To mark the TEXT as a UNIQUE I had to specify length of the field. Otherwise I got #1170 Error.
             * @sa http://www.w3schools.com/sql/sql_unique.asp
             * @sa http://phpclub.ru/mysql/doc/char.html
             */            
            $q = "INSERT INTO ".$table." (".$this->key_name." ,".$this->val_name.") VALUES ('$key',  '$val') ON DUPLICATE KEY UPDATE `".$this->val_name."`='$val'";

            if (!$res = $this->db->query($q)) {
                #@todo deal with Query error, generate run-time warning
                if (isset($res)){
                    $res->free();
                    $res->close();			
                }
                return false;
            }    
            return true;
        }

        /**
          @brief Read from DB table key
          default_db_config - is the table set by dfefault.
        */
        protected  function ReadDBKey($table,$key){
                $q = "SELECT * FROM `".$table."` WHERE `".$this->key_name."` = '$key' LIMIT 1";		
                
                /**  DB read key error processing */
                if (!$res = $this->db->query($q)) {
                    /**
                        @todo deal with Query error, generate run-time warning
                    */	
                    if (isset($res)){
                        $res->free();
                        //$res->close();			
                    }
                    return false;
                }	

                /**
                 * Reading result. Most common case should be the first one.
                 */
                if ($res->num_rows == 1) {
                        $cfg_val = $res->fetch_row(); 
                        $keyvalue = $cfg_val[2];

                        /*if (isset($res)){
                                $res->free();
                                $res->close();			
                        }*/
                        mysqli_free_result($res);
                        return $keyvalue;
                }

                if ($res->num_rows == 0) {
                     /** @todo Page is not found - no content
                      */
                        if (isset($res)){
                                //$res->free();
                                //$res->close();	
                            mysqli_free_result($res);                            
                        }
                        /**
                         * $key." - key not found";
                         @todo send error message to the log
                         */
                        return false;
                }
                /**	
                        If more than one key - read the one with biggest id.			
                        other - ignored
                        @todo Send WARNING message to the log. 
                */
                if ($res->num_rows > 1) {			
                        while ($cfg_val = $res->fetch_row()) { 
                                $keyvalue = $cfg_val[2];
                        }
                        mysqli_free_result($res);
                }                 	

                /*$res->free();
                $res->close();*/
                return $keyvalue;
        }                
        
    }//End of class cXVI_db_basic
    
/*@}*/
?>