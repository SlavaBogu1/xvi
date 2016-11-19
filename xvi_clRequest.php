<?php
  ##
  # @brief Class to gather user requrest information and pre-parse it
  # this is key class to identify site view.
  #  - based on request info we will select the "SITE"
  #  - based on request info we will select the "TEMPLATE"
  defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

  
  ##
  # @brief Extract all necessary system information from superglobal ararys
  # $_SERVER
  # $_GET
  # $_POST
  # @sa http://www.firststeps.ru/php/r.php?2
class cXVI_Request {
        private static $_instance = null;
        private $server_req;
        private $cookies_req;

        private function __construct(){		
            $this->server_req = cXVI_ServerReq::getInstance();	// instatnce to deal with $_SERVER
            $this->cookies_req = cXVI_CookiesReq::getInstance();   // instatnce to deal with $_COOKIE
        }	  
        private function __clone(){
        }		
        public static function getInstance() {
          // check if instance already exist
          if (null === self::$_instance) {
                // create new instance
                self::$_instance = new self();
          }
          // return instance
          return self::$_instance;
        }

##
# @brief Return remote IP address in String format
        public function GetAddrStr(){
            return $this->server_req->GetAddrStr();
        }		 
        
        public function GetRqstStr(){
            return $this->server_req->GetRqstStr();
        }
        
        public function GetCompleteUrl(){
            return $this->server_req->GetCompleteUrl();
        }
        public function GetClearPath(){
            return $this->server_req->GetClearPath();
        }
        
}

##	
# @brief Class to deal with superglobal _SERVER parameters
# @sa http://en.wikipedia.org/wiki/Clean_URL
# @sa http://www.php.su/articles/?cat=vars&page=015
# @sa http://php.net/manual/en/reserved.variables.server.php

#- $_SERVER['DOCUMENT_ROOT'] - local path to executed script. 
#- $_SERVER['HTTP_ACCEPT'] - "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"
#- $_SERVER['HTTP_ACCEPT_LANGUAGE'] - en-US,en;q=0.8,ru;q=0.6,de;q=0.4
#+ $_SERVER['HTTP_HOST'] - server name (without http://). 
#~ $_SERVER['HTTP_REFERER'] - address of page the call was made from. 
#~ $_SERVER['HTTP_USER_AGENT'] -browser info. to identify platform? 
#+ $_SERVER["REMOTE_ADDR"] - client IP address
#- $_SERVER['SCRIPT_FILENAME'] - script path and name. 
#- $_SERVER['REQUEST_METHOD'] - ( 'GET', 'HEAD', 'POST', 'PUT' ). Why need it?
#+ $_SERVER['QUERY_STRING'] - info from the URL after "?' sign. This is to parse.
#+ $_SERVER['REQUEST_URI'] - string after the domain name. in addition to 'QUERY_STRING' give the full URL line after the server address, not only parameters after '?'
#                            $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
# @sa http://php.net/manual/ru/function.parse-url.php
# $_SERVER['REQUEST_TIME'] - timestamp of request

# EXAMPLE:
# HTTP_HOST is 192.168.56.102
# HTTP_CONNECTION is keep-alive
# HTTP_PRAGMA is no-cache
# HTTP_CACHE_CONTROL is no-cache
# HTTP_ACCEPT is text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
# HTTP_UPGRADE_INSECURE_REQUESTS is 1
# HTTP_USER_AGENT is Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36
# HTTP_ACCEPT_ENCODING is gzip, deflate, sdch
# HTTP_ACCEPT_LANGUAGE is en-US,en;q=0.8,ru;q=0.6,de;q=0.4
# HTTP_COOKIE is _ga=GA1.1.1783582176.1458639346
# PATH is /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
# SERVER_SIGNATURE is Apache/2.4.7 (Ubuntu) Server at 192.168.56.102 Port 80
# SERVER_SOFTWARE is Apache/2.4.7 (Ubuntu)
# SERVER_NAME is 192.168.56.102
# SERVER_ADDR is 10.0.2.15
# SERVER_PORT is 80
# REMOTE_ADDR is 10.0.2.2
# DOCUMENT_ROOT is /var/www/html
# REQUEST_SCHEME is http
# CONTEXT_PREFIX is 
# CONTEXT_DOCUMENT_ROOT is /var/www/html
# SERVER_ADMIN is webmaster@localhost
# SCRIPT_FILENAME is /var/www/html/test.html
# REMOTE_PORT is 50595
# GATEWAY_INTERFACE is CGI/1.1
# SERVER_PROTOCOL is HTTP/1.1
# REQUEST_METHOD is GET
# QUERY_STRING is 
# REQUEST_URI is /
# SCRIPT_NAME is /test.html
# PHP_SELF is /test.html
# REQUEST_TIME_FLOAT is 1465225885.483
# REQUEST_TIME is 1465225885
class cXVI_ServerReq {
        private static $_instance = null;
        private $client_request_str; // address set by client in the URL request
        private $client_request_str_org; // original address set by client in the URL request
        private $addr_str; // string with client IP address
        private $server_URL; 
        private $query_str; // parameters set by client in the URL after "?" 
        private $query_time_str;
        private $complete_URL; // full URL request received from the client
        private $request_path;
        private $script_name;
        
        
        function __construct(){
            // IP address who sent the request
            //$this->addr_URL = $this->GetClientIP();
            //$this->addr_str = $this->Ip4Ip6_to_string ($this->addr_URL);
            $this->addr_str = $this->Ip4Ip6_to_string ($this->GetClientIP());
                       
            $this->client_request_str_org =  filter_input( INPUT_SERVER, 'REQUEST_URI',  FILTER_SANITIZE_URL);
            $this->client_request_str =  $this->TrimScriptName($this->client_request_str_org);

            // domain name
            $this->server_URL = filter_input( INPUT_SERVER, 'HTTP_HOST',  FILTER_SANITIZE_URL);
           
            // URL query string (URL parameters after "?")
            $this->query_str = filter_input( INPUT_SERVER, 'QUERY_STRING',  FILTER_SANITIZE_URL);
            

            // how to parse the query string - use parse_url.
            // @ca  http://php.net/manual/en/function.parse-url.php
            //PHP_URL_SCHEME, PHP_URL_HOST, PHP_URL_PORT, PHP_URL_USER, PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY или PHP_URL_FRAGMENT
            //$url = 'http://username:password@hostname/path?arg=value#anchor';
            //print_r(parse_url($url));
            //echo parse_url($url,PHP_URL_QUERY);
           
            if (isset($_SERVER['HTTPS']) &&  ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                $serv_scheme = 'https://';  
            } else {
                $serv_scheme = 'http://';
            }
            $this->complete_URL = $serv_scheme.$this->server_URL.$this->client_request_str_org;
            $this->request_path =  trim(parse_url($serv_scheme.$this->server_URL.$this->client_request_str,PHP_URL_PATH),"/");

            // get request timestamp sting in format for log 
            $this->query_time_str = $this->ParseRequestDateTime();		
        }
        
        private function TrimScriptName($addr_str){
            //$this->script_name = $_SERVER['SCRIPT_NAME']; // php script name
            $this->script_name = filter_input( INPUT_SERVER, 'SCRIPT_NAME',  FILTER_SANITIZE_URL);
            // if script name exist in the REQUEST_URI then we should remove it to receive the adress

            $script_pos = strpos($addr_str,$this->script_name);
            if ($script_pos !== false ) {
                $pre_str = substr($addr_str,0,$script_pos);                
                $scr_len = strlen($this->script_name);
                $addr_str = $pre_str.substr($addr_str,$script_pos + $scr_len);
            } 
            
            return $addr_str;            
        }

        private function __clone(){
        }

        public static function getInstance() {
          // check if instance already exist
          if (null === self::$_instance) {
                // create new instance
                self::$_instance = new self();
          }
          // return instance
          return self::$_instance;
        }		

        public function GetCompleteUrl(){
            return $this->complete_URL;
        }
        public function GetClearPath(){
            return $this->request_path;
        }
        /**
         *  @brief GetOriginalRqstStr will return the REQUEST_URI string but script name will be removed from it
         *  This better for DB search
         */
        public function GetRqstStr(){
            return $this->client_request_str;
        }
        /**
         *  @brief GetOriginalRqstStr will return the REQUEST_URI string 
         */
        public function GetOriginalRqstStr(){ 
            return $this->client_request_str_org;
        }
##
# @brief get data time stamp of the request for log
# see more about formats at the link below
# @sa http://stackoverflow.com/questions/10040291/converting-a-unix-timestamp-to-formatted-date-string		
        public function ParseRequestDateTime() {						
                //gmdate("Y-m-d\TH:i:s\Z", $_SERVER['REQUEST_TIME']);
                //date('c',$_SERVER['REQUEST_TIME']);			
                return date('c',$_SERVER['REQUEST_TIME']);;
        }

##
# @brief Interface to private variable.		
        public function GetRequestDateTime(){
                return $this->query_time_str;
        }


##
# @brief Interface to private variable.		
        public function GetAddrStr(){
                return $this->addr_str;
        }

##
# @brief get client IP from one of the sources in trust order. This can be improved, see link.
# if all sources are empty - return 0.0.0.0
# @sa http://stackoverflow.com/questions/7623187/will-the-value-of-a-set-serverhttp-client-ip-be-an-empty-string		
# @sa http://stackoverflow.com/questions/11452938/how-to-use-http-x-forwarded-for-properly
        public function GetClientIP(){
                if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                        return $_SERVER["HTTP_X_FORWARDED_FOR"];  
                } elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
                        return $_SERVER["REMOTE_ADDR"]; 
                } elseif (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
                        return $_SERVER['HTTP_CLIENT_IP'];
                }
                return '0.0.0.0';
        }	

## 
# IP6 / IP4 convert
# from here http://stackoverflow.com/questions/12435582/php-serverremote-addr-shows-ipv6
# Known prefix
        public function Ip4Ip6_to_string ($addr) {
                $v4mapped_prefix_hex = '00000000000000000000ffff';
                $v4mapped_prefix_bin = pack("H*", $v4mapped_prefix_hex);

                # Or more readable when using PHP >= 5.4
                # $v4mapped_prefix_bin = hex2bin($v4mapped_prefix_hex);

                # Parse
                $addr_bin = inet_pton($addr);
                if( $addr_bin === FALSE ) {
                        # Unparsable? How did they connect?!?
                        die('Invalid IP address');
                }

                # Check prefix
                if( substr($addr_bin, 0, strlen($v4mapped_prefix_bin)) == $v4mapped_prefix_bin) {
                        # Strip prefix
                        $addr_bin = substr($addr_bin, strlen($v4mapped_prefix_bin));
                }
                # Convert back to printable address in canonical form			
                return inet_ntop($addr_bin);
        }  		


}
	
	
##
# @brief parse cookie data
class cXVI_CookiesReq {		
        private static $_instance = null;

        function __construct(){
        ##
        # @brief COOKIES PROCESSING
                $flag =0;  		
                #default
                $this->req_cookie_uname = null;
                $this->req_cookie_sid = null;

                if (isset($_COOKIE['login'])) {
                $this->req_cookie_uname = $_COOKIE['login'];
                $flag = 1;
                }

                if (isset($_COOKIE['session_id'])) {
                $this->req_cookie_sid = $_COOKIE['session_id'];
                $flag = $flag | 2;
                }

                if ($flag === 3 ) {
                $this->req_cookie_is_set = true;
                } else {
                $this->req_cookie_is_set = false;
                }
        }

        private function __clone(){
        }

        public static function getInstance() {
          // check if instance already exist
          if (null === self::$_instance) {
                // create new instance
                self::$_instance = new self();
          }
          // return instance
          return self::$_instance;
        }		
}
	

?>