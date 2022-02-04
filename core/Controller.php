<?php

/**
 * Controller Class
 */

namespace app\core;


use app\application\core\ErrorController;
use app\core\utility\Utility;

abstract class Controller{

    protected $view;

    public Request $request;

    public Response $response;

    public $redirector;

    public $components = [];

    public abstract function isAuthorized();

    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request !== null ? $request : new Request();        
        $this->response = $response !== null ? $response : new Response();
        $this->view = new View($this);
        $this->redirector = new Redirector();
    }

    /**
     * Perform the startup process for this controller.
     * Events that that will be triggered for each controller:
     * 1. load components
     * 2. perform any logic before calling controller's action(method)
     * 3. trigger startup method of loaded components
     * 
     * @return void|Response
     */

    public function startupProcess()
    {
        $this->initialize();

        $this->beforeAction();

        $result = $this->triggerComponents();

        if($result instanceof Response){
            return $result;
        }
        return $result;
    }

    /**
     * Ititialization method
     * Inititalize components and optionally, assign configuration data
     * 
     */
    public function initialize()
    {
        $this->loadComponents([
            'Auth' =>[
                'authenticate' => ['User'],
                'authorize' => ['Controller']
            ],
            'Security'
        ]);
    }
    //todo check utility print
    /**
     * load the components by setting the component's name to a controller's property
     * 
     * @param array $components
     */
    public function loadComponents(array $components)
    {

        if(!empty($components)){
            $components = Utility::normalize($components);
 
            foreach($components as $component => $config){

                if(!in_array($component, $this->components, true)){
                    $this->components[] = $component;
                }

                $class = $component;
                $class = "app\\core\\components\\". $class;
                $this->{$component} = empty($config) ? new $class($this) : new $class($this,$config);
            }
        }
    }

    /**
     * Triggers component startup methods
     * Fire the components in order of Authentication, Security and Authrization 
     * 
     */
    private function triggerComponents(){
        $components = ['Auth', 'Security'];
        foreach($components as $key => $component){
            if(!in_array($component, $this->components)){
                unset($components[$key]);
            }
        }
        $result = null;
        foreach($components as $component){
            if($component == "Auth"){

                $authenticate = $this->Auth->config("authenticate");

                if(!empty($authenticate)){
                    if(!$this->Auth->authenticate()){
                        
                        $result = $this->Auth->unauthenticated();
                    }   
                }
          
                 //delay checking authorize till after the loop
            $authorize = $this->Auth->config("authorize");

            }else{
                $result = $this->{$component}->startup();
            }
            if($result instanceof Response){return $result;}
        }

        //authorize
        if(!empty($authorize)){
            if(!$this->Auth->authorize()){
                $result = $this->Auth->unauthorized();
            }
        }
        return $result;
    }

    public function error($code)
    {
        $errors = [
            404 => "notFound",
            401 => "unAuthenticated",
            403 => "unAuthorized",
            400 => "badRequest",
            500 => "system"
        ];


        if(!isset($errors[$code])){
            $code = 500;
        }

        $action = isset($errors[$code]) ? $errors[$code]: "system";
        $this->response->setStatusCode($code);


        //clear Buffer
        $this->response->clearBuffer();
        (new ErrorController($this->request, $this->response))->{$action}();

        return $this->response;

    }

    /**
     * magic function called before controller action method
     */
    public function beforeAction(){}

    /**
     * load Model
     * 
     * @param string $model
     */
    public function loadModel($model){
        $ucModel = "app\\application\\models\\". ucwords($model);
     
        return $this->{$model} = new $ucModel();
    }

    /**
     * forces ssl request
     * 
     */
    public function forceSSL(){
        //  $secured = "https//". $this->request->fullUrlWithoutProtocol();
        // return $this->redirector->to($secured);
    }
 }