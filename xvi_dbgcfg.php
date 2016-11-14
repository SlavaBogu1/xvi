<?php  
  /** @file  xvi_dbgcfg.php
        Debug functions, macro and constants.
  */  

defined('_XVI') or die('Engine is not initialized properly'.__FILE__);
defined('DEBUG_MODE_ON') or define('DEBUG_MODE_ON',false); //if not set - debug is off
 
  global $firephp;
 
  if (DEBUG_MODE_ON) {	//DEBUG_MODE is ON         
    #PATH1
	define('FIREBUG_PATH',"../engine/fb/");
	
	##
	# @brief Enable FirePHP remote debugging for specific IP address
	# How to use firePHP:

	#  $firephp->group('Test Group',array('Collapsed' => true));
	#  	$firephp->log('Plain Message');     
	#  	$firephp->info('Info Message');     
	#  	$firephp->warn('Warn Message');     
	#  	$firephp->error('Error Message');   
	#  $firephp->groupEnd();
	
	#  $firephp->group('Collapsed and Colored Group',
	#              array('Collapsed' => true,
	#                    'Color' => '#FF00FF'));
	#  $firephp->groupEnd();

	##
	# @todo need to avoid using GLOBAL in the code.
	$var = $_SERVER["REMOTE_ADDR"];	 
	
	require_once(FIREBUG_PATH.'FirePHP.class.php');	
	$firephp = FirePHP::getInstance(true);
		

	#if ($var===VALID_IP) {         
	#	$firephp->log($var, 'Debugger connected');
	#} else {
	#	$firephp->setEnabled(false);  // or FB::
	#}
	
	#$firephp->log('Debugger connected');	

	
  } else {
    //DEBUG_MODE is OFF	
	
  }

##
# @brief 
# @TODO add filters for $type and $msg to reduce risk of data insertion.  
	function MyDebugPrint($msg,$type="log"){
		global $firephp;
		if (DEBUG_MODE_ON) {//DEBUG_MODE is ON         			
			eval ('$firephp->$type($msg);');
		} else {//DEBUG_MODE is OFF
		}		
	}
  
?>