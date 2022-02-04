<?php

/**
 * Secutiry Class
 */
namespace app\core\components;

use app\core\Component;
use app\core\Logger;
use app\core\Session;

class Security extends Component{

    protected $config = [
        'form' => [],
        'requireSecure' => [],
        'requirePost' => [],
        'requireAjax' => [],
        'requireGet' => [],
        'validateForm' => true,
        'validateCsrfToken'=> false
    ];

    /**
     * Auth startup
     */
    public function startup()
    {

        if(!$this->requestRequired()){
            return $this->invalidRequest();
        }
        if(!$this->secureRequired()){
            return $this->invalidRequest('forceSSL');
        }
        if(!$this->validateDomain()){
            return $this->invalidRequest();
        }

        if($this->request->isPost() && $this->config["validateForm"]){

            if(!$this->form($this->config['form'])){
                return $this->invalidRequest();
            }            
        }

        if($this->config["validateCsrfToken"]){
            if(!$this->CsrfToken()){
                return $this->invalidRequest();
            }
        }
    }


    /**
     * check validate from the required HTTP methods
     * Post, Get, Ajax
     */
    private function requestRequired()
    {
        foreach(['Post', 'Get' , 'Ajax'] as $method){
            $key = 'require'. $method;
            if(!empty($this->config[$key])){
                if(in_array($this->request->param('action'), $this->config[$key], true) || $this->config[$key] == ['*']){
                    if(!$this->request->{"is".$method}()){
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function validateDomain()
    {

        $isValid = true;
        $referer = $this->request->referer();
        if($this->request->isPost()){
            if(!isset($referer)){
                $isValid = false;
            }else{
                $referer_host = parse_url($referer, PHP_URL_HOST);
                $server_host = $this->request->host();
                $isValid = ($referer_host == $server_host) ? true : false;
            }
        }

        if(!$isValid){
            Logger::log("Request Domain", "User: " . Session::getUserId(). "Request is not coming from the same domain with invalid HTTP referer", __FILE__,__LINE__);
            return false;
        }
        return true;
    }

    /**
     * Check if sercured connection is required
     * 
     * @return bool
     */
    private function secureRequired()
    {
        $key = "requireSecure";
        if(!empty($this->config[$key])){
            if(in_array($this->request->param('action'), $this->config[$key], true) || $this->config[$key]==['*']){
                if(!$this->request->isSSL()){
                    return false;
                }
            }
        }
        return true;
    }

    private function invalidRequest($callback = null)
    {
        if(is_callable([$this->controller, $callback])){
            return $this->controller->{$callback}();
        }
        throw new \Exception('The request has been denied', 400);
    }


    /**
     * Sets the actions that require secured connection(SSL)
     *
     * @param array $actions
     */
    public function requireSecure(array $actions)
    {
        $this->config['requireSecure'] = $actions;
    }

    /**
     * Sets the actions that require a POST request
     */
    public function requirePost(array $actions)
    {
        $this->config['requirePost'] = $actions;
    }

    /**
     * Sets the actions that require a GET request
     */
    public function requireGet(array $actions)
    {
        $this->config['requireGet'] = $actions;
    }

    /**
     * Sets the actions that require a Ajax request
     *
     * @param array $actions
     */
    public function requireAjax( array $actions = []){
        $this->config['requireAjax'] = $actions;
    }

    /**
     * validate CSRF token
     * CSRF token can be passed with submitted forms and links asscoiated with sensitive server-side operations
     * 
     * In case of GET request, you need to set 'validateCsrfToken' in $config to true
     */
    public function CsrfToken(array $config=[])
    {
        $userToken = null;
        if($this->request->isPost()){
            $userToken = $this->request->data('csrf_token');
        }else{
            $userToken = $this->request->query('csrf_token');
        }

        if(empty($userToken) || $userToken !== Session::getCsrfToken())
        {
            Logger::log("CSRF Attack", "User: ". Session::getUserId()."provided invalid CSRF Token". $userToken, __FILE__,__LINE__);
            return false;
        }
        return $userToken == Session::getCsrfToken();
    } 


    /**
     * validate submitted form
     * -Unknown fields cannot be added to the form
     * -fields cannot be removed from the form
     * Use $exclude to exclude anything mightn't be send with the form, like possible emty arrays, checkbox
     * radiobtn, ...etc , By default ,, the submit field will be excluded
     * 
     */
    public function form($config)
    {

        if(empty($config['fields']) || $this->request->dataSizeOverflow()){
            return false;   
        }

        if(!in_array('csrf_token', $config['fields'], true)){
            $config['fields'][] = 'csrf_token';
        }
        
        //exclude any checkboxs, radio buttons, possible empty arrays, ...etc
        $exclude = empty($config['exclude']) ? [] : (array)$config['exclude'];
        if(!in_array('submit', $exclude, true)){
            $exclude[] = 'submit';
        }
        $count =$this->request->countData($exclude);
        $acount = count($config['fields']);
        if($this->request->countData($exclude) !== count($config['fields'])){
            Logger::log('Form Tampering', "User: ".Session::getUserId()." is tampering the form with invalid number of fields expected count - $count  actual count $acount", __FILE__, __LINE__);
            return false;
        }

        foreach($config['fields'] as $field){
            if($this->request->data($field) != '' && $this->request->data($field) == null){
                Logger::log("Form Tampering", "User: ". Session::getUserId(). " is tampering the form with invalid field : $field",__FILE__, __LINE__);
                return false;
            }
        }

        return $this->CsrfToken();
    }
}