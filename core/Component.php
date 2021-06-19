<?php 

/**
 * Component Class 
 * 
 * the base class for Auth, Security classes.
 * It provides reusable controller logic
 */

 namespace app\core;

 class Component{

    protected Controller $controller;

    protected Request $request;

    protected $config = [];

    public function __construct(Controller $controller, array $config =[])
    {
        $this->controller = $controller;
        $this->request = $controller->request;
        $this->config = array_merge( $this->config,$config);
    }

    public function config($key, $value=null)
    {
        if($value !== null)
        {
            $this->config = array_merge($this->config, [$key=>$value]);
            return $this;
        }
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }
 }