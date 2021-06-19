<?php

/**
 * Utility class
 * 
 * provide methods for manipulating and extracting data from arrays
 */

 namespace app\core\utility;

 
 class Utility{


    /**
     * Normalizes an Array, and Converts it to a standard format
     * 
     * @param array $array
     * @return array narmalized array
     */
    public static function normalize(array $array)
    {
        $keys = array_keys($array);
        $count = count($keys);
        
        $newArray = [];
        for ($i = 0; $i < $count; $i++)
        {
            if(is_int($keys[$i])){
                // key = 1 => // $array[1]=>"hello" // $newArr["hello"] =>null
                $newArray[$array[$keys[$i]]] = null;
            }else{
                $newArray[$keys[$i]] = $array[$keys[$i]];
            }
        }
        return $newArray;
    }

    public static function commas($arr){
        return implode(',',(array)$arr);
    }

    public static function merge($arr1, $arr2){
        return array_merge((array)$arr1, (array)$arr2);
    }
 }