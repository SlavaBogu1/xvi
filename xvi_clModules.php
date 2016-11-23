<?php
  /** @file xvi_clModules.php  
   * @todo надо проверять что внешние модули, указанные в module_queue существуют на момент вызова. Иначе ProcessPlaceholders будт падать.
   * 
   *    Load external modules and initialize the pattern processing queue
    \addtogroup Modules
    @{
  */
defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

define('XVI_MODULE_QUEUE_KEY','module_queue'); 

/** 
 * @todo  Как удалять ненужные записи из DB json ?
 * @todo  Как проверять, что priority в нужном диапазоне, чтобы не возникло класса, который не будет вызываться (если приоритет больше, чем число записей, например)?
 */
class cXVI_Modules {
    private static $_instance;
    private $pattern_start, $pattern_end;
    private $module_queue;
    private $module_queue_str;
    private $module_queue_availabe = false;
    private $db_sites;

    /**
     @section Modules Plugin modules
     XVI angine allow to add external modules and run them in run-time.


     @brief Load external parsers
     Open folder and include all *.php files from there. 
     Each file suppose to have namespace and Parser function defined.

     In theory there should be just a single replacement per template. However to make it more flexible engine should support multiple replacement.
     The engine create a list of module function calls in priority order to generate the final replacement.
     Replacement can produce other templates for future replacement (template splitting)
     
     @todo need to research SPL and autoload functions..
     @sa http://php.net/manual/ru/function.spl-autoload.php

    */    
    protected function __construct(){
        /** Connect to sites DB and read the config key
         */
        $this->db_sites = cXVI_db_sites::getInstance();        
        $this->module_queue_str = $this->db_sites->ReadDBKey(XVI_MODULE_QUEUE_KEY);
        
        $this->module_queue = json_decode($this->module_queue_str,true);
        if (!is_null($this->module_queue)) {
            $this->module_queue_availabe = true;            // Set flag that there is a JSON module queue record in sites config db
        }
        
        defined('PATTERN_PARSERS') or eval('define(\'PATTERN_PARSERS\',_XVI."modules/");');
        /// @cond ALL
        /// @endcond
        $module_folder = opendir(PATTERN_PARSERS); // open folder PATTERN_PARSERS 

        /** @todo because engine is using module classes (same as module file names) 
         *   engine can utilize __autoload functionality
         *   need to investigate this
         *   CORRECTION: I don't know file names, so __autoload is not the option
         */
        while($load_module= readdir($module_folder)){ 
                if($load_module!= "." && $load_module !=".."){ // If it is a file, not directory.             
                        require_once(PATTERN_PARSERS.$load_module);
                        /**
                         * Call get_fmap_$load_module function to load list of functions from the plugin and associated templates
                         * @todo Build the list of calls (template -> module1_func_1 -> module2_func_2... in priority order.
                         */                        
                        $class_name = trim($load_module,".php");

                            /** Optional call of getInstance - to initialize Class. 
                             *  However three members (Register, Call and CallN are available as static functions. 
                                $res = $class_name::Register();
                                $res = $class_name::Call();
                                $res = $class_name::Calln();
                             *  @todo decide if it is necessary to call getInstance for loaded modules
                             */
                            //$res = $class_name::getInstance();

                        
                        if (class_exists($class_name, false)) {
                            if ($this->module_queue_availabe) {
                                $this->Update_queue($class_name);  //  Check list of PH in Register function and add class to module_queue is not exists
                                $class_name::getInstance();
                                /**
                                 * @todo function Update_queue return false in case of problem with key update. neet ot handle this.
                                 */
                            } else {
                                // Create queue and add class. Just copy all from ::Register to module_queue and store to DB
                                $this->Create_queue($class_name);  // Create record in DB if not exisits or empty
                                /**
                                 * @todo function Create_queue return false in case of problem with key update. neet ot handle this.
                                 */
                            }
                        } else {
                            // @todo catch the unable of loading class some way.
                            //echo "Unable to load class: $class_name";
                            //trigger_error("Unable to load class: $class", E_USER_WARNING);                            
                        }
                } 
        }    
        closedir( $module_folder); // Close directory
    }  // End of __construct   

    /*  function __destruct(){ } */
    private function __clone(){ }    
    public static function getInstance() {
        if (null === self::$_instance) {
          self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /** 
    # @brief Find pattern in the string
    # @details Looking for pattern opening '{_' and closing '_}' and read the name in between
    # make sure to replace pattern, otherwise you can easely get infinite cycle
    # return false if pattern start or end signs not found.
    # return pattern if found
    */
    private function getPattern(&$text){
        /*
         * @sa http://php.net/manual/ru/function.preg-match.php
         * Не используйте функцию preg_match(), если необходимо проверить наличие подстроки в заданной строке. 
         * Используйте для этого strpos() либо strstr(), поскольку они выполнят эту задачу гораздо быстрее.
         * 
        $subject = $text;
        $pattern = '{_([A-Z_0-9]*)_}';
        preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
        print_r($matches);
        #echo implode('<br>',$matches);
        */       
    
        $this->pattern_start = strpos($text,OPEN_PATTERN_SIGN);
        if ($this->pattern_start === false) {
            return false;
        } #no more patterns

        // use $this->pattern_start as an offset - this help to avoid the case when END pattern found before START
        $this->pattern_end = strpos($text,CLOSE_PATTERN_SIGN,$this->pattern_start);
        if ($this->pattern_end === false) {
            return false;
        } #pattern not closed

        //check if there is another OPEN_PATTERN_SIGN in between open and close signs found above
        // use $this->pattern_start as an offset - this help to avoid the case when END pattern found before START

        $pattern = substr($text, $this->pattern_start+2, $this->pattern_end - $this->pattern_start - 2); 
        
        $tmp_patt_start = strpos($pattern,OPEN_PATTERN_SIGN);
        if (!$tmp_patt_start === false) {
            return false;
        } #there is another open_pattern_sign found. 
        /** @todo remove wrong patterns, report issue to log.
         */
        return $pattern;
    }

     /**
    # @brief New pattern parsing function
    # @details Get the list of PH supported by engine from DB and associated modules
      * Find the PH in the $html text.
      * If match exist - replace by calling module CallBack
      * If match doesn't exist - fetch next PH
    */
    public function ReplacePatterns($html){
        foreach ($this->module_queue as $ph =>$arr_val){
            $ph_pos = strpos($html,OPEN_PATTERN_SIGN.$ph.CLOSE_PATTERN_SIGN);
          
            if (!($ph_pos===false)) {
                $module_res = "";
                // PH found
                // $arr_id - is the number of modules registered to this PH
                // $arr_val - is actually the json record  "class" - "name"  and "priority" -> "number"
                foreach($this->module_queue[$ph] as $arr_id =>$arr_val) {
                    /**
                     * @TODO Need to add warning is class_name is wrong. 
                     * Otherwise it is hard to debug.
                     */
                    $class_name = $arr_val["class"];
                    $module_res .= $class_name::Call($ph);                    
                }
                //$module_res collect all data for this ph
                $html = str_replace(OPEN_PATTERN_SIGN.$ph.CLOSE_PATTERN_SIGN, $module_res, $html);
                    
            } else {
                // PH not found
                // do nothing ... may be log ..
            }
        }
        return $html;
    }
    
    /**
    # @brief New pattern parsing engine
    # @details call the old engine to keep site up-n-running
    */
    public function ReplacePatternsOld($html){
        
        do {
            /** @bug This doesn't work as required. 
             *    Need to check if there is {_ sign in between {_ and _}  otherwise engine miss not closed pattern
             *    Need to search from certain point, otherwise will stop at {_ {_ _} this combination
             */
            $pattern = $this->getPattern($html); #find pattern in between "{_" and "_}"
            if (($pattern === false) && ($this->pattern_start!=false)) {
                // In this case both $this->pattern_start and $this->pattern_end are not zero.
                // workaround - DELETE first {_ sign.
                $html = substr_replace($html," ",$this->pattern_start,2);
                $pattern = " ";
                continue;
            }

                /**
                * @brief Check if there is number in pattern name 
                * The number is the last element in the pattern name
                * replace actual number with string "NUMBER" and call parser with parameter
                */    
            
                //call "Call"-methods of classes registered for this pattern in priority order
                $class_list = $this->module_queue[$pattern];
                $class_list_size = count($class_list);
                $priority_number = 0;
                
                while ($priority_number < $class_list_size) {
                    
                    foreach($class_list as $id =>$class_methods) {
                        $cmp = $class_methods["priority"];
                        if ($cmp == $priority_number) {
                            //echo $pattern." call - ".$class_methods["class"]."<br>";
                            $class_methods["class"]::Call($pattern);
                        }                        
                    }
                    $priority_number +=1;
                 }
                
                 /** OLD CODE 
                  *  @todo remove it
                if (function_exists($pm)) {
                    if ($number_flag){
                            $pm($html,$pattern,$number_in_pattern); #send content by reference
                    }else {
                            $pm($html,$pattern); #send content by reference
                    }
                } else {
                    # default pattern processing if function not found
                    # change patterns to avoid infinite cycle
                    $tpl = OPEN_PATTERN_SIGN.$pattern.CLOSE_PATTERN_SIGN;
                    $repl = "";
                    $html = str_replace($tpl, $repl, $html);				
                }
                  * 
                  */
            } while ($pattern != false);
        /**return $html;*/
    }

    /** @brief Method to create new queue in the DB.
     */
    private function Create_queue($class_name) {
        // get JSON string from ::Register
        // check if it is valid json
        // Create JSON record or ignore
        $new_json_str = $class_name::Register();        
        $new_json_obj = json_decode($new_json_str,true);
        
        if (!is_null($new_json_obj)){
            $this->module_queue = $new_json_obj;
            $this->module_queue_str = $new_json_str;
            $this->module_queue_availabe = true;
            $this->db_sites->WriteDBKey(XVI_MODULE_QUEUE_KEY,$new_json_str);
        } else {
            /** @todo Check if wrong JSON can create infinite loop.
             */
            return false;
        }
        
    }
    
    /** @brief Method to check if class registered in processing queue and assciated with PH
     *   @todo Всю работу по синхронизации JSON и реально подключенных классов надо вынести в Admin и Maintenance блоки.
     *   Пока будет тут.
     */
    private function Update_queue($class_name) {
        // Check if module_queue contain this class call associated with certain PH
        // From ::Register function we will receive ALL pairs of PH 
       
        // Read list of PH from the class
        // check if this PH exists in module_queue, if not - add it
        // if exists - check if class is inside the PH, if not - add it
        // Update module_queue record in the DB
        
        $new_json_str = $class_name::Register();
        $new_json = json_decode($new_json_str,true);                                

        foreach ($new_json as $ph => $ph_func_array) { //$ph - PH name, ph_func_array - array of classes registered to this PH
            if (is_null($this->module_queue[$ph])){
                /** Class reported new placeholder support
                 *  action - update module_queue record and key
                 */
                $this->Add_PH_to_queue($ph,$ph_func_array[0]); // in the ::Register function only single record can be created for each PH. So we can use [0] index.
            } else {
                /** this placeholder already supported.                  
                *  need to check if new class name is exists as a key in this array                 
                */                
                $new_module_class_name = $ph_func_array[0]["class"];
                
                // @todo работает неправильно - надо проверять все массивы доступные по ключу и если в них нет - добавлять. иначе он добавляет на каждую проверку и создается много одинаковых записей.
                $class_exist_flag = false;
                foreach ($this->module_queue[$ph] as $arr_id =>$arr_val){
                    $res = array_search ( $new_module_class_name, $arr_val);
                    if (is_null($res)){
                        // @todo wrong search parameters
                        continue;
                    }
                    if ($res){
                        // This class is already registered for this PH so skip further search.
                        $class_exist_flag = true;
                    }
                }
                if (!$class_exist_flag) {                        
                    $this->Add_class_to_queue($ph,$ph_func_array[0]);
                    $add_flag = false;
                }

            }                                    
        }                                
    }
    
    /** @brief If PH exists and there are other classes registered except new one we need to add it.
     */
    private function Add_class_to_queue($ph,$ph_class){
        // Get sub-array from the current json
        // $new_priority - is new number of records in array - by default new item added to the end of it, so priority replaced.
        $new_priority = array_push($this->module_queue[$ph],$ph_class) - 1;
        
        // update priorities 
        $this->module_queue[$ph][$new_priority]["priority"] = $new_priority;
        
        // Check json validity
        $res_str = json_encode($this->module_queue);
        
        if ($res_str) {
            $this->module_queue_str = $res_str;
            $this->db_sites->WriteDBKey(XVI_MODULE_QUEUE_KEY,$res_str);
        } else {
            // in case of error - restore the previous json key
            // @todo Generate json error to log
            $this->module_queue = json_decode($this->module_queue_str,true);
        }
    }
    
    /** @brief If modules are registered howevere this is new PH 
     *  Add the whole record to the end of current json set.
     *  @todo Проверить - если я регистрирую несколько классов, что добавляется только новый PH, а не все скопом.
     */
    private function Add_PH_to_queue($ph,$ph_class){
        // generate json string
        $upd_json = "\"$ph\":[".json_encode($ph_class)."]";
        
        $new_json_str = substr_replace($this->module_queue_str,",",-1,1).$upd_json."}";
        $new_json_obj = json_decode($new_json_str,true);
        
        // check validity of new json key
        if (!is_null($new_json_obj)){            
            $this->module_queue_str = $new_json_str;
            $this->module_queue = $new_json_obj;
            $this->db_sites->WriteDBKey(XVI_MODULE_QUEUE_KEY,$new_json_str);
        } else {
            /**
             *  @todo Add log message about wrong JSON key generation
             */
            return false;
        }
        return true;
    }
   
} // End of class cXVI_Modules
/*@}*/
?>
