<?php

/**
 * Router Class
 */

namespace app\core;

use app\application\core\ErrorController;

class Router{


    private $controller = null;

    private $method = null;

    private $args = array();

    public Request $request;

    public Response $response;

    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, array $callback){

        $this->routes[$path] = $callback;
    }

    public function resolve()
    {
        
        //split the requested URL
        $this->parseURL();


        if(!self::isControllerValid($this->controller))
        {
            return $this->notFound();
        }
        
        if(!empty($this->controller)){

            $controllerName = $this->getNamespace() . $this->controller;
            
            if(!self::isMethodValid($controllerName, $this->method))
            {
                return $this->notFound();
            }

            if(!empty($this->method)){

                if(!self::areArgsValid($this->args))
                {
                    return $this->notFound();
                }

                //instantiate the controller object with it's action method
                return $this->invoke($controllerName, $this->method, $this->args);
            }
            else{   

                $this->method = Config::get("DEFAULT_METHOD");
                if(!method_exists($controllerName, $this->method))
                {
                    return $this->notFound();
                }
                return $this->invoke($controllerName, $this->method, $this->args);
            }
        }else{

            //if no controller defined,
            //send to default controller
            $this->method = Config::get("DEFAULT_METHOD");
            return $this->invoke(Config::get("DEFAULT_CONTROLLER"), $this->method, $this->args);
        }
    }

    /**
     * instantiate controller object with it's action method
     * 
     * @param string $controller
     * @param string $method
     * @param array $args
     * @return Response
     * 
     */
    private function invoke($controller, $method, $args)
    {
        $this->request->addParams(['controller'=>$controller, 'action'=>$method, 'args'=>$args]);
        $this->controller = new $controller($this->request, $this->response);
        
        $result = $this->controller->startupProcess();

        if($result instanceof Response){
            return $result->send();
        }
        if(!empty($args)){
            $response = call_user_func([$this->controller, $method],$args);
        }else{
            $response = $this->controller->{$method}();
        }
        if($response instanceof Response)
        {
            return $response->send();
        }
            
        return $this->response->send();
    }

    /**
     * Check if controller is valid
     * 
     * request to error controller will be considered as invalid,
     * 
     * @param string $controller
     * @return bool
     */
    private static function isControllerValid(string $controller){
        
        if(!empty($controller)){
            if (!preg_match('/\A[a-z]+\z/i', $controller) ||
                !file_exists(APP . 'controllers/' . $controller . '.php')){
                return false;
            }else { return true; }

        }else { return true; }
    }
    /**
     * Check if the action method is valid
     * 
     * request to 'index' method will be considered as invalid,
     * the constructor will take care of 'index' methods
     * 
     * @param string $controller
     * @param string $method
     * @return bool 
     */
    private static function isMethodValid($controller, $method)
    {

        if(!empty($method)){
            if (!preg_match('/\A[a-z]+\z/i', $method) ||
                !method_exists($controller, $method)){
                return false;
            }else { return true; }

        }else { return true; }
    }

    /**
     * Check if arguments are valid 
     * 
     * @param array $args
     * @return bool
     */
    private static function areArgsValid(array $args)
    {
        if(!empty($args)){   
            foreach($args as $arg){
                if(!preg_match('/\A[a-z0-9]+\z/i', $arg)){ return false; }
            }
        }
        return true;
    }

    /*
     * Split the URL for the current request
     * 
     */
    private function parseURL()
    {
        $url = $this->request->uri();    
        $baseURL = Config::get("BASE_URL");

        $url = str_replace($baseURL, '', $url);
        
        $path = empty($url) ? '/' : $url;

        $route = $this->routes[$path] ?? null;

        $url = explode('/', filter_var(trim($url,'/'), FILTER_SANITIZE_URL));
        
        if($route !== null){

            $this->controller = $route[0];
            $this->method = $route[1];

        }else if(!empty($url)){

            $this->controller = !empty($url[0]) ? ucwords($url[0]) : null;
            $this->method = !empty($url[1]) ? $this->camelcase($url[1]) : null;
        }
        unset($url[0], $url[1]);
        $this->args = !empty($url) ? array_values($url) : [];
    }


    /**
     * Get the namespace for the controller class, namespace defined within the route paramters
     * only if it was added
     * 
     * @return string
     */
    private function getNamespace(): string
    {
        return Config::get("CONTROLLER_NAMESPACE");
    }

    /**
     * Camel Case the String
     * 
     * @param string $str
     * @return string
     */
    private function camelCase(string $str)
    {
        return lcfirst(ucwords($str));
    }


    /**
     * Display an 404 page
     * 
     */
    private function notFound()
    {
        return (new ErrorController())->error(404)->send();
    }

 }