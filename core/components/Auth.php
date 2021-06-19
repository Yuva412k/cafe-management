<?php

/**
 * Authentication and Authorization Class
 * 
 */
namespace app\core\components;

use app\core\Component;
use app\core\Cookie;
use app\core\Session;
use app\core\utility\Utility;

class Auth extends Component{

    protected $config = [
        'authenticate' => [],
        'authorize' => []
    ];

    public function startup()
    {
        //authenticate
        if(!empty($this->config['authenticate'])){
            if(!$this->authenticate()){
                return $this->unauthenticated();
            }
        }

        //authorize
        if(!empty($this->config['authorize'])){
            if(!$this->authorize()){
                return $this->unauthorized();
            }
        }
    }

    /**
     * Handles unauthenticated access attempt
     */
    public function unauthenticated()
    {
        // $this->controller->loadModel('loginModel');
        // $this->controller->loginModel->logout(Session::getUserId());

        if($this->request->isAjax()){
            return $this->controller->error(404);
        }else{
            return $this->controller->redirector->to(PUBLIC_ROOT);
        }
    }

    /**
     * Handles unauthorized access attempt
     */
    public function unauthorized()
    {
        return $this->controller->error(403);
    }

    /**
     * authenticate the user using the defined methods in $config
     */
    public function authenticate()
    {
        return $this->check($this->config['authenticate'], 'authenticate');
    }

    /**
     * Authorize the user using the defined method in $config
     */
    public function authorize()
    {
        return $this->check($this->config['authorize'], 'authorize');
    }

    /**
     * Check for authentication or authorization
     */
    public function check($config, $type)
    {
        if(empty($config)){
            
            throw new \Exception($type. ' methods arent initialized yet in config');
        }

        $auth = Utility::normalize($config);

        foreach($auth as $method=> $config){

            $method = '_'.ucfirst($method) . ucfirst(($type));

            if(!method_exists(__CLASS__, $method)){
                throw new \Exception('Auth Method doesnt exists:'. $method);
            }

            if($this->{$method}() === false){
                return false;
            }
        }
        return true;
    }

    /**
     * check's user is already logged in via session or cookie
     */
    public function isloggedIn()
    {
        if(Session::getIsLoggedIn() === true){
            return true;
        }
        if(Cookie::isCookieValid()=== true){
            return true;
        }
        return false;
    }


    /**
     * Is user authorized for the requested Controller & Action method?
     * 
     * @param array $config 
     */
    private function _ControllerAuthorize()
    {
        if(!method_exists($this->controller, 'isAuthorized')){

            throw new \Exception(sprintf('%s does not implement an isAuthorized() method' ,get_class($this->controller)));
        }

        return (bool)$this->controller->isAuthorized();
    }

    /**
     * Is user authenticated?
     */
    private function _UserAuthenticate()
    {

        if($this->concurrentSession()){
            return false;
        }
    
        if(!$this->loggedIn()){
            return false;
        }
        return true;
    }

    /**
     * Checks if user is logged in or not
     * It uses Session and Cookies to validate the current user
     */
    private function loggedIn()
    {
        if(Session::isSessionValid($this->request->clientIp(), $this->request->userAgent())){
            return true;
        }

        if(Cookie::isCookieValid()){
            //get role from user class, because cookies doen't store roles
            $role = $this->controller->user->getProfileInfo(Cookie::getUserId())["role_id"];
            Session::reset(['user_id'=> Cookie::getUserId(), "role_id"=>$role, "ip"=>$this->request->clientIp(), "user_agent"=> $this->request->userAgent()]);

            //reset cookie, Cookie token is usable only once 
            Cookie::reset(Session::getUserId());
            
            return true;
        }
        return false;
    }

    private function concurrentSession()
    {
        return Session::isConcurrentSessionExists();
    }
    
 }