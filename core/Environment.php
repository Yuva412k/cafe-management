<?php

namespace app\core;

/**
 * Environment class.
 * Gets an environment variable from $_SERVER
 *
 */

 class Environment{

   /**
    * Gets an environment variable from $_SERVER, $_ENV, or using getenv()
    *
    * @param $key string
    * @return string|null
    */
    
   public static function get($key){

       $val = null;
       if (isset($_SERVER[$key])) {
           $val = $_SERVER[$key];
       } elseif (isset($_ENV[$key])) {
           $val = $_ENV[$key];
       } elseif (getenv($key) !== false) {
           $val = getenv($key);
       }

       return $val;
   }

}