<?php

/**
 * Redirector class
 * 
 */
namespace app\core;

class Redirector{

    /**
     * Redirect to the given location
     */
    public function to($location, $query = '')
    {
        if(!empty($query)){
            $query = "?" . http_build_query((array)$query, '', '&');
        }
        $response = new Response('', 302, ["Location"=>$location. $query]);
        return $response;
    }

    public function root($location ="dashboard", $query = ""){
        return $this->to(PUBLIC_ROOT. $location, $query);
    }

}