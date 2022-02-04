<?php

/**
 * Database Class
 */

 namespace app\core;

use PDO;

class Database{

    private $connection;

    private $statement = null;

    private static $database = null;

    private function __construct()
    {
        if($this->connection == null)
        {
            $this->connection = new PDO('mysql:host='.Config::get('DB_HOST').';dbname='.Config::get('DB_NAME'), Config::get("DB_USER"), Config::get("DB_PASS"));
            // $this->connection = new PDO('mysql:host=localhost;port=3306;dbname=cafemanagement', 'root', null);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Singleton Database Object
     */
    public static function openConnection()
    {
        if(self::$database == null){
            self::$database = new Database();
        }
        return self::$database;
    }

    /**
     * Prepares a SQL query for execution
     */
    public function prepare($query)
    {
        $this->statement = $this->connection->prepare($query);
    }

    /**
     * Binds a value to a parameter
     */
    public function bindValue($param, $value)
    {
        $type = self::getPDOType($value);
        $this->statement->bindValue($param, $value, $type);
    }

    /**
     * Binds an array of values
     */
    public function bindValues(array $data)
    {
        foreach($data as $key=>$value){
            $type = self::getPDOType($value);
            $this->statement->bindValue($key, $value, $type);
        }
    }

    /**
     * RollBack a transaction
     */
    public function rollBack() {
        $this->connection->rollBack();
    }


    /**
     * Begin Transaction
     */
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }



    /**
     * Commit Transaction
     */
    public function commit() {
        $this->connection->commit();
    }


    /**
     * Executes a prepared statement
     */
    public function execute($arr = null)
    {
        if($arr == null){return $this->statement->execute();}
        else{return $this->statement->execute($arr);}
    }

    /**
     * PDOType 
     */
    private static function getPDOType($value){
        switch ($value) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * To fetch the result data in associative array
     * 
     */
    public function fetchAllAssociative()
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

     /**
     * To fetch Only the next row from the result data in form of [key][value] array.
     *
     * @access public
     * @return array|bool   false on if no data returned
     */
    public function fetchAssociative() 
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the number of rows affected by the last SQL statement
     * "If the last SQL statement executed by the associated PDOStatement was a SELECT statement, some databases may return the number of rows returned by that statement"
     *
     * @access public
     */
    public function countRows() 
    {
        return $this->statement->rowCount();
    }

    /**
     * Counts the number of rows in the table
     */
    public function countAll($table)
    {
        $this->statement = $this->connection->prepare("SELECT COUNT(*) AS 'count' FROM ". $table);
        $this->execute();
        return (int)$this->fetchAssociative()['count'];
    }
    
    /**
     * Select all rows from a table
     *
     * @access public
     * @param   string  $table
     *
     */
    public function getAll($table){
        $this->statement = $this->connection->prepare('SELECT * FROM '.$table);
        $this->execute();
    }

    /**
     * Delete all rows from a table
     *
     * @access public
     * @param   string  $table
     *
     */
    public function deleteAll($table){
        $this->statement = $this->connection->prepare('DELETE FROM '.$table);
        $this->execute();
    }

    public static function closeConnection() {
        if(isset(self::$database)) {
            self::$database->connection =  null;
            self::$database->statement = null;
            self::$database = null;
        }
    }

 }