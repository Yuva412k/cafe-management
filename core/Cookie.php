<?php

/**
 * Cookie Class
 */

 namespace app\core;

 class Cookie{

    private static $userId;

    private static $token;

    private static $hashedCookie = null;

    /**
     * Getter for $userId
     */
    public static function getUserId()
    {
        return (int)self::$userId;
    }

    public static function isCookieValid()
    {
        if(empty($_COOKIE['auth'])){
            return false;
        }

        // check the count before using explode
        $cookie_auth = explode(':', $_COOKIE['auth']);
        if(count($cookie_auth)!== 3){
            self::remove();
        }

        list($encryptedUserId, self::$token, self::$hashedCookie) = $cookie_auth;

        self::$userId = Encryption::decrypt($encryptedUserId);

        if(self::$hashedCookie === hash('sha256', self::$userId . ':' . self::$token . Config::get('COOKIE_SECRET_KEY'))){

            $database = Database::openConnection();
            $database->prepare("SELECT id, cookie_token FROM users WHERE id =:id AND cookie_token =:cookie_token LIMIT 1");
            $database->bindValue(':id', self::$userId);
            $database->bindValue(':cookie_token', self::$token);
            $database->execute();

            $isValid = $database->countRows() === 1 ? true : false;
        }else{
            $isValid = false;
        }

        if(!$isValid){
            Logger::log("COOKIE", self::$userId."is trying to login using invalid cookie:". self::$token, __FILE__, __LINE__);
            self::remove(self::$userId);
        }
        return $isValid;
    }

    public static function remove($userId = null)
    {
        if(!empty($userId)){
            $database = Database::openConnection();
            $query = "UPDATE users SET cookie_token = NULL WHERE id= :id";
            $database->prepare($query);
            $database->bindValue(":id", $userId);
            $result = $database->execute();

            if(!$result){
                Logger::log("COOKIE", "Couldn't remove cookie from the database for user ID: ". $userId, __FILE__,__LINE__);
            }
        }
        self::$userId = self::$token = self::$hashedCookie= null;

        //kill/delete a cookie 
        setcookie('auth', false, time()-(3600*3650), Config::get('COOKIE_PATH'), Config::get("COOKIE_DOMAIN"), Config::get("COOKIE_SECURE"),Config::get("COOKIE_HTTP"));
    }

    /**
     * Reset Cookie
     * resetting is done by updating the database,
     * and resetting the "auth" cookie in the browser
     * 
     */
    public static function reset($userId)
    {
        self::$userId = $userId;
        self::$token = hash('sha256', mt_rand());
        $database = Database::openConnection();
        $query = "UPDATE users SET cookie_token = :cookie_token WHERE id= :id";
        $database->prepare($query);
        $database->bindValue(':cookie_token', self::$token);
        $database->bindValue(':id', self::$userId);
        $result = $database->execute();

        if(!$result){
            Logger::log('COOKIE',"Couldn't remove cookie from the database for user ID". $userId, __FILE__, __LINE__);
        }

        //generate cookie string(remember me)
        $encryptedUserID = Encryption::encrypt(self::$userId) . ":". self::$token;

        //$hashedCookie generated from the original user Id, NOT from the encrypted one.
        self::$hashedCookie = hash('sha256', self::$userId . ":". self::$token . Config::get("COOKIE_SECRET_KEY"));
        $authCookie = $encryptedUserID . ':'.self::$hashedCookie;

        setcookie('auth', $authCookie, time()+Config::get("COOKIE_EXPIRY"), Config::get("COOKIE_PATH"), Config::get("COOKIE_DOMAIN"), Config::get("COOKIE_SECURE"), Config::get("COOKIE_HTTP"));
    }
 }