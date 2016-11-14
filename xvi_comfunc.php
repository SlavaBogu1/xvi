<?php
/** @file  xvi_comfunc.php
    Common functions and tools
*/
defined('_XVI') or die('Engine is not initialized properly'.__FILE__);

function gettime(){
        $start_time = explode(' ',microtime());
        $real_time = $start_time[1].substr($start_time[0],1);
        return $real_time;
}

function GenHTTPHeader($http_code,$err_code){
    http_response_code($http_code); 

    switch ($http_code) {
        case "403": //403 - Forbidden
            echo "Site access denied <br> Sorry for the inconvenience. <br> Reference code: ".$err_code;
            break;
        case "503":
            header('Retry-After: 3600');            
            echo "Server is at maintenance.<br>  Please come back later. <br> Sorry for the inconvenience";
            break;
        case "501":
            header('Retry-After: 3600');            
            echo $err_code;
            break;
        default:
            echo "Reference code: ".$err_code;
    }
}

/** @brief Get latest JSON error
 *  @param $flag if flag is TRUE - just return error code. This is default. Othervise return an error string. 
 *  $lfag mean - return just an error code.        
 */
function getJSONerror($flag){
    if (!isset($flag)) { //check if parameter was set. if missed - it is considered as true.
        $flag = true;
    }
    if ($flag) {
        return json_last_error();
    } else {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'JSON_ERROR_NONE - Ошибок нет';
            case JSON_ERROR_DEPTH:
                return 'JSON_ERROR_DEPTH - Достигнута максимальная глубина стека';
            case JSON_ERROR_STATE_MISMATCH:
                return 'JSON_ERROR_STATE_MISMATCH - Некорректные разряды или не совпадение режимов';
            case JSON_ERROR_CTRL_CHAR:
                return 'JSON_ERROR_CTRL_CHAR - Некорректный управляющий символ';
            case JSON_ERROR_SYNTAX:
                return 'JSON_ERROR_SYNTAX - Синтаксическая ошибка, не корректный JSON';
            case JSON_ERROR_UTF8:
                return 'JSON_ERROR_UTF8 - Некорректные символы UTF-8, возможно неверная кодировка';
            default:
                return 'JSON_UNKNOWN_CODE - Неизвестная ошибка';
        }   
    }
}

/***
 * @brief Remove all placeholder patterns from the text
 * take care on corner cases:
 * pattern without start
 * pattern without end
 */
function RemovePlaceholders($text){
        do {
            $pattern_start = strpos($text,OPEN_PATTERN_SIGN);
            if (is_bool($pattern_start)) continue; 
            
            $pattern_end = strpos($text,CLOSE_PATTERN_SIGN,$pattern_start);
            if ($pattern_end===false) break; // closing tag doesn't exist - we can exit now
            
            $text = substr_replace ($text,"",$pattern_start,$pattern_end+2 - $pattern_start);
            
        } while (!is_bool($pattern_start));
        
        return $text;
}

?>