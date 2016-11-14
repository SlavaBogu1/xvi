<?php
  /** @file  xvi_clAbsModule.php  
   *    This is abstract class describing third party modules.
   *    Each customized module must implement it.
   *    XVI also support function plugins .. to be removed soon.
   *    @todo Finalize module interface (either class or function or both)
    \addtogroup Modules
    @{
  */
/** @class cXVI_AbsModule  Abstarct class describing interfaces
 *  @param Register - to register all supported placeholders and functions
 *  @param Call - simple call a function associated with placeholder
 *  @param CallN - call a function associated with placeholder if ph has a _version_ id at the end. {_PH_1_} - example. 
 *      The version number is the second parameter
 */
 abstract class   cXVI_AbsModule {
     abstract public static function Register();
     abstract public static function Call($placeholder_id);
     abstract public static function CallN($placeholder_id, $number);     
 }
        
/*@}*/
?>
