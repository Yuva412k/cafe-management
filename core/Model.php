<?php

/**
 * Model Class
 * 
 */

 namespace app\core;

 abstract class Model{

    public Database $db;

    protected $errors = [];

    public function __construct()
    {
        $this->db = Database::openConnection();
    }

    /**
     * get errors
     *
     * @return array errors
     */
    public function errors(){
        return $this->errors;
    }

}