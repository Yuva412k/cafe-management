<?php

/**
 * Session Class
 * 
 */

namespace app\core;


class Session{

    protected const FLASH_KEY = 'flash_messages';

    public static function init()
    {
        if(session_status()== PHP_SESSION_NONE){
            session_start();
        }
    }

    /**
     * Check if session data exists
     */
    public static function isSessionValid($ip, $userAgent)
    {
        $isLoggedIn = self::getIsLoggedIn();
        $userId = self::getUserId();
        $userRole = self::getUserRole();

        //Check if there is any data in session
        if(empty($isLoggedIn) || empty($userId) || empty($userRole)){
            return false;
        }

        // then check ip address and user agent
        if(!self::validateIPAddress($ip) || !self::validateUserAgent($userAgent)) {
            Logger::log("SESSION", "current session is invalid", __FILE__, __LINE__);
            self::remove();
            return false;
        }
        //check if session is expired
        if(!self::validateSessionExpiry()){
            self::remove();
            return false;
        }

        return true;
    }


    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    { 
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
    }


    public static function unsetKey($key){
        
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public static function getIsLoggedIn()
    {      
        return empty($_SESSION['is_logged_in']) || !is_bool($_SESSION['is_logged_in']) ? false : $_SESSION['is_logged_in'];         
    }

    public static function getUserId()
    {
        return empty($_SESSION['user_id']) ? null : (int)$_SESSION['user_id'];
    }
    
    public static function getUserRole()
    {
        return empty($_SESSION['role_id']) ? null : $_SESSION['role_id'];
    }
    public static function getUsername()
    {
        return empty($_SESSION['name']) ? null : $_SESSION['name'];
    }
    public static function setFlashData($key, $value){
        if(!empty($key) || !empty($value)){
            $_SESSION[self::FLASH_KEY][$key] = $value;
        }
    }

    public static function getFlashData($key){
        if(!isset($_SESSION[self::FLASH_KEY])){
            return null;
        }
        if(array_key_exists($key, $_SESSION[self::FLASH_KEY]))
        {
            $flashmessage =  $_SESSION[self::FLASH_KEY][$key];
            unset($_SESSION[self::FLASH_KEY][$key]);
            return $flashmessage;
        }
        return null;
    }

    public static function hasflashData($key)
    {
        if(!isset($_SESSION[self::FLASH_KEY])){
            return false;
        }
        return array_key_exists($key, $_SESSION[self::FLASH_KEY]);
    }

    private static function validateSessionExpiry()
    {
        $max_time = 60*60*24;

        if(!isset($_SESSION['generated_time']))
        {
            return false;
        }
        return ($_SESSION['generated_time'] + $max_time > time());
    }

    /**
     * Matches current user agent with the one stored in the session
     * 
     */
    private static function validateUserAgent($userAgent)
    {
        if( !isset($_SESSION['user_agent']) || !isset($userAgent) ){
            return false;
        }
        return $_SESSION['user_agent'] == $userAgent;
    }
    /**
     * matches current Ip Address with the one stored in the session
     * 
     * @param string $ip
     */
    private static function validateIPAddress($ip)
    {
        if(!isset($_SESSION['ip']) || !isset($ip))
        {
            return false;
        }
        return $_SESSION['ip'] == $ip;
    }

    /**
     * Get CSRF Token
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getCsrfToken(){
        return empty($_SESSION["csrf_token"]) ? null : $_SESSION["csrf_token"];
    }

    /**
     * get CSRF token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     *
     */
    public static function generateCsrfToken(){

        $max_time = 60 * 60 * 24;
        $stored_time = self::getCsrfTokenTime();
        $csrf_token  = self::getCsrfToken();

        if($max_time + $stored_time <= time() || empty($csrf_token)){
            $token = md5(uniqid(rand(), true));
            $_SESSION["csrf_token"] = $token;
            $_SESSION["csrf_token_time"] = time();
        }

        return self::getCsrfToken();
    }

    /**
     * Get CSRF Token generated time
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getCsrfTokenTime(){
        return empty($_SESSION["csrf_token_time"]) ? null : $_SESSION["csrf_token_time"];
    }
    
    public static function isConcurrentSessionExists()
    {
        $session_id = session_id();
        $userId = self::getUserId();

        if(isset($userId) && isset($session_id))
        {
            $database = Database::openConnection();
            $database->prepare("SELECT session_id FROM users WHERE id=:id LIMIT 1 ");
            $database->bindValue(":id", $userId);
            $database->execute();
            $result = $database->fetchAssociative();
            $userSessionID = !empty($result) ? $result['session_id'] : null;

            if($userSessionID == null){
                return false;
            }
            return $session_id != $userSessionID;    
        }
        return false;
    }


    public static function updateSessionInDb($userId, $sessionId = null)
    {
        $database = Database::openConnection();
        $database->prepare("UPDATE users SET session_id = :session_id WHERE id=:id");

        $database->bindValue(":session_id",$sessionId);
        $database->bindValue(":id",$userId);
        $database->execute();
    }

    public static function reset(array $data){

        session_regenerate_id(true);
        $_SESSION = array();
        
        $_SESSION["is_logged_in"] = true;
        $_SESSION["user_id"] = (int)$data["user_id"];
        $_SESSION['role_id'] = $data["role_id"];
        $_SESSION['name']= $data['name'];
        $_SESSION['ip'] = $data["ip"];
        $_SESSION['user_agent'] = $data["user_agent"];
        $_SESSION['generated_time'] = time();
        
        self::updateSessionInDb($data["user_id"], session_id());

        setcookie(session_name(), session_id(), time() + Config::get('SESSION_COOKIE_EXPIRY') , Config::get('COOKIE_PATH'), Config::get('COOKIE_DOMAIN'), Config::get('COOKIE_SECURE'), Config::get('COOKIE_HTTP'));        

    }

    public static function remove()
    {
        $userId = self::getUserId();
        if(!empty($userId)){
            self::updateSessionInDb(self::getUserId());
        }

        if(ini_get("session.use_cookies")){
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-36000, 
            $params['path'],$params['domain'],
            $params['secure'], $params['httponly']
            );
        }

        if(session_status()== PHP_SESSION_ACTIVE){
            session_destroy();
        }
    }


}