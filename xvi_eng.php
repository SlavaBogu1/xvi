<?php
/** @file  xvi_eng.php
    The main enigne file.  
  */
  defined('_XVI') or die('Engine is not initialized properly'.__FILE__);
/**
  @mainpage XVI engine
  @author SlavaBogu1
  @section intro_sec Introduction 
  XVI is the multi-site CMS engine.<br>

  @version   0.13.11
  @date      2016
  
  @section main_sec Main flow
  XVI main processing flow:
  - get client request info (from _REQUEST and _SERVER global arrays)
  - load configuration data from files
  - create DB instance and access to the DB storage
  - load configuration from DB
  - load template
  - load page info from DB
  - generate page
  - show page
 
  @section design Design ideas
 * How to implement processing of page template?
 * Assume that path from simple html template up to full content is consists of LAYERS.
 * At every layer the engine will call function that change the content of the template. So modules that generate content should be called in order.
 * When the final module from the processing queue has been invoked then pattern shall removed from the template. 
 * Functions called at certain layer can create patterns for next layers.
 * Template processing is ongoing till all patterns are resolved.
 * 
 
  @section feature_sec Features
  @subsection f1 Multisite
  XVI support several sites by the single Engine. Each site must have the config file in the /engine/site/ folder with defined constants.
  In the site root folder there should be php file with engine path and SITE_ID and loading of engine.php. 
  
  @subsection f2 Templates
  XVI engine support CSS templates
  
  @subsection f3 Modules
  XVI engine support dynamic third-party modules loading.

  @section install_sec Installation and Configuration
  @subsection step1 Step 1: Copy engine files
  @subsection step2 Step 2: Create bash and cron tasks
  @subsection step3 Step 3: Create DB tables 
  @subsection step4 Step 4: Create site root folder
  Create .htaccess file and site root html/php file.
  Using the command below you should create a symlink to the engine entry file.
  @code 
  #!/bin/bash
  ln -s /var/www/engine/index.html /var/www/html/test.html
  @endcode
  
  @subsection debug Debug with xdebug
  @sa http://stuporglue.org/setting-up-xdebug-with-netbeans-on-windows-with-a-remote-apache-server/comment-page-1/#comment-6507
*/

	/**
		Memory footprint monitoring
	*/
	//$script_memory = memory_get_usage();
  
  /**
 @brief	Load ALL engine files
*/
  require_once(_XVI."xvi_fl.php");  

 /**
  @todo This is FOR_FUTURE to implement HTTP header
  @sa http://www.w3schools.com/php/func_http_header.asp
  don't forget - haders are depends on SITE_ID
    header("HTTP/1.0 404 Not Found");
 */

/**
  @brief Creade object of main engine class	
*/
    $site_inst = cXVI_engine::getInstance();	

/**
  @brief Check if Engine is running
*/
    if (!$site_inst->CheckIfEngineIsRunning()) {
        $html = file_get_contents(TEPLATE_PATH."default_503.html");
        echo $html;
        exit(0);
    }
/**
  @brief Check if Site is running
*/
    if (!$site_inst->CheckIfSiteIsRunning()) {
        $html = file_get_contents(TEPLATE_PATH."default_503.html");
        echo $html;
        exit(0);
    }
    

/**
  @brief Get client requiest info
*/
    $page_addr = $site_inst->GetPageAddressFromRequest();

/**
  @brief Get page content from DB
*/
    $site_inst->GetPageContent($page_addr);

/**
  @brief Get page template
*/
    $site_inst->GetTemplate($page_addr);
    
    
/**
  @brief Update the template with new content
*/
    $site_inst->UpdateTemplate();


/**
  @brief show the page
*/
    $site_inst->Show();

    //echo "<br> Memory usage: ".(memory_get_usage() - $script_memory )."<br>";
    //echo "Memory peak usage: ".memory_get_peak_usage()."<br>";

    exit(0);
/* END OF ENGINE */

       
/**
    @brief Main engine class. Object of this class will create the page and show it.
 * @param html Store all HTML code for generated page
 * @param gen_db Generic Database interface. Provide extra functions.
*/
class cXVI_engine{
        private $template;
        private $ext_modules;        
        private $request;
        private $content;          //page content from DB in JSON format
        private $page_options; //page options from DB in JSON format
        private $gen_db;
        private $res;
        private $html;
        private static $_instance = null;
        public $generated_content;

        private function __construct(){	
                $this->html = "";
                $this->gen_db = cXVI_db::getInstance();	// Init DB interface to read configuration data
        }		

        private function __clone(){ }		

        public static function getInstance() {
          if (null === self::$_instance) {
                self::$_instance = new self();
          }
          return self::$_instance;
        }		

        public function CheckIfEngineIsRunning(){
            #$this->gen_db->setEngineRunStatus(CFG_ENGINE_RUNNING);
            if(!$res = $this->gen_db->getEngineRunStatus()){
                /**@todo load SITE IS TEMPORARY DISABLED template
                 */
                GenHTTPHeader(503,"Service temporary unavailable");
                return false;
            } 
            return true;
        }
        public function CheckIfSiteIsRunning(){
            #$this->gen_db->setSiteRunStatus(CFG_SITE_RUNNING);
            if(!$res = $this->gen_db->getSiteRunStatus()){
                /**@todo load SITE IS TEMPORARY DISABLED template
                 */
                GenHTTPHeader(503,"Service temporary unavailable");
                return false;
            }                            
            return true;
        }
                
        public function GetPageAddressFromRequest(){
            $this->request = cXVI_Request::getInstance();
            return $this->request->GetClearPath(); 
        }

        /**
         *  @brief - темплейт страницы может быть записан в контенте страницы или общий для сайта
         * @param type $page_addr
         */
        public function GetTemplate($page_addr){
            /**                
              @brief TEPLATE_NAME is the HTML template name. If it is not defined then engine will use default.
            */
            if ($this->page_options["template"]=="default") {
                /// @cond ALL
                // see xvi_clDB.php comments        
                defined('TEPLATE_NAME') or eval('define(TEPLATE_NAME,"default.html");');
                /// @endcond            
                $template_name = TEPLATE_PATH.TEPLATE_NAME;
            } else {
                $template_name = TEPLATE_PATH.$this->page_options["template"];
            }
            $this->template = cXVI_Template::getInstance($template_name);
        }
        
        /**
         * @brief Read content from site database
         * content is JSON list of record "PH":"content"
         * JSON structure
         *  "OPTIONS":["name":"value"]
         *  "CONTENT":["PH":"content"]
         * @param type $page_addr
         */
        public function GetPageContent($page_addr){
            if (empty($page_addr)) {
                $page_addr = DEFAULT_SITE_ROOT_PAGE;
            } 
            
            $data = json_decode($this->gen_db->ReadDBKey(DB_SOURCE_CONTENT,$page_addr),true);
            $this->page_options =$data['OPTIONS'][0];
            if(is_null($this->page_options)) {
                $this->page_options = ""; // @TODO Defaul page options
            }
            $this->content = $data['CONTENT'][0];
            if(is_null($this->content)) {
                $this->content = array("PH_DEMO" => "test", "PH_BODY"=>"test");
            }
        }
        
        public function UpdateTemplate(){
            $res = $this->template->getHTML_Template();

            foreach($this->content as $ph=>$replace_str){
                $res = str_replace(OPEN_PATTERN_SIGN.$ph.CLOSE_PATTERN_SIGN, $replace_str, $res);
            }
            
            $res = $this->ProcessPlaceholders($res); //call list of external modules to update rest of PH 
            
            $this->html = RemovePlaceholders($res); //if there are some PH left - remove them
            
        }
        
        public function ProcessPlaceholders($html_template){
            $this->ext_modules = cXVI_Modules::getInstance();  
            return $this->ext_modules->ReplacePatterns($html_template);
        }
        
        public function Show(){
            echo $this->html;
        }
        
}
?>