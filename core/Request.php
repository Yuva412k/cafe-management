<?php

/**
 * Request Class
 * 
 * It contains the request information and provide methods to fetch request body
 */
namespace app\core;

 class Request{


    /**
     * @var array $params
     */
    public $params = ["controller" => null, "action"=>null, "args"=>null];

    /**
     * Array of POST data 
     * 
     * @var array
     */
    private array $data = [];

    /**
     * Array of querystring arguments
     * 
     * @var $query
     */
    public array $query = [];

    /**
     * The URL used to make the request
     * 
     * @var string
     */
    public $url = null;

    /**
     * Contructor 
     * Create a new request from PHP superglobals.
     * 
     * @param array $config 
     */
    public function __construct($config = [])
    {
        $this->data = $this->mergeData($_POST, $_FILES);
        $this->query = $_GET;
        $this->params += isset($config["params"]) ? $config["params"] : [];
        $this->url = $this->fullURL();
    }

   /**
     * merge POST and FILES data
     * You shouldn't have two fields with the same 'name' attribute in $_POST & $_FILES
     *
     * @param  array $post
     * @param  array $files
     * @return array the merged array
     */
    private function mergeData($post, $files)
    {
        foreach($post as $key=>$value)
        {
            if(is_string($value)){$post[$key] = trim($value);}
        }
        return array_merge($files, $post);
    }

    /**
     * Count fields in $this->data and optionally exclude some fields
     * 
     * @param array $exclude
     * @return mixed
     */
    public function countData(array $exclude = [])
    {
        $count = count($this->data);
        foreach($exclude as $field){
            if(array_key_exists($field, $this->data)){
                $count--;
            }
        }
        return $count;
    }

    public function param($key){
        return array_key_exists($key, $this->params) ? $this->params[$key] : null;
    }
    /**
     * @param string $key
     * @return mixed
     */
    public function data($key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;   
    }

    /**
     * @param string $key
     * @return mixed 
     */
    public function query($key)
    {
        return array_key_exists($key, $this->query) ? $this->query[$key] : null;
    }



    /**
     * Check if request is Ajax
     * 
     * @return bool 
     */
    public function isAjax(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        }
        return false;
    }

    /**
     * check if request is POST request
     * 
     * @return bool
     */
    public function isPost(){
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    /**
     * Check if request is GET request
     * 
     * @return bool
     */
    public function isGet(){
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * check if request over secured connection(ssl)
     * 
     * @return bool
     */
    public function isSSL(){
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off";
    } 

    /**
     * Add parameters to $this->params
     * 
     * @param array $params
     * @return Request
     */
    public function addParams(array $params){
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * get content length
     * 
     * @return int
     * 
     */
    public function contentLength(){
        return (int)$_SERVER['CONTENT_LENGTH'];
    }
    
    /**
     * Check if there is overflow in POST & FILES data.
     * This will lead to having both $_POST and $_FILES = empty array.
     * 
     * @return bool
     */
    public function dataSizeOverflow(){
        $contentLength = $this->contentLength();
        return empty($this->data) && isset($contentLength);
    }

    /**
     * get the current uri of the request
     * 
     * @return string|null
     */
    public function uri()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    }

    /**
     * Get the current host of the request 
     * 
     * @return string
     * @throws UnexpectedValueException if the hostname is invalid
     */
    public function host()
    {
        if(!$host = Environment::get('HTTP_HOST'))
        {
            if(!$host = $this->name())
            {
                $host = Environment::get('SERVER_ADDR'); 
            }
        }
        //trim and remove port number from host
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        // check that it does not contain forbidden characters
        if ($host && preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host) !== '') {
            throw new \UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }

        return $host;
    }

    /**
     * Get the name of the server host
     * 
     * @return string|null
     */
    public function name()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
    }

    /**
     * Get the referer of this request.
     * 
     * @return string|null
     */
    public function referer()
    {
        return isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']: null;
    }

    /**
     * get the client IP addresses.
     *
     * 'REMOTE_ADDR' is the most trusted one,
     * otherwise you can use HTTP_CLIENT_IP, or HTTP_X_FORWARDED_FOR.
     *
     * @return string|null
     */
    public function clientIp()
    {
        return isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: null;
    }
    /**
     * get the userAgent
     */
    public function userAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }
    
    /**
     * Gets the request's protocol.
     *
     * @return string
     */
    public function protocol()
    {
        return $this->isSSL() ? 'https' : 'http';
    }

    /**
     * Gets the protocol and HTTP host.
     *
     * @return string The protocol and the host
     */
    public function getProtocolAndHost()
    {
        return $this->protocol() . '://' . $this->host();
    }

    /**
     * Get the full URL for the request with the added query string parameters
     * 
     * @return string
     */
    public function fullURL()
    {  
        //get uri
        $uri = $this->uri();
        if(strpos($uri, '?') !== false)
        {
            list($uri) = explode('?', $uri, 2);
        }

        // add querystring arguments(neglect 'url' & 'redirect')
        $query    = "";
        $queryArr = $this->query;
        unset($queryArr['url']);
        unset($queryArr['redirect']);

        if(!empty($queryArr)){
            $query .= '?' . http_build_query($queryArr, '', '&');
        }

        return  $this->getProtocolAndHost() . $uri . $query;
    }


    /**
     * Get the full URL for the request without the protocol.
     * 
     * It could be useful to force a specific protocol.
     *
     * @return string
     */
    public function fullUrlWithoutProtocol(){
        return preg_replace('#^https?://#', '', $this->fullUrl());
    }

     /**
     * Returns the base URL.
     *
     * Examples:
     *  * http://localhost/                         returns an empty string
     *  * http://localhost/example/public/user      returns example
     *  * http://localhost/example/posts/view/123   returns example
     *
     * @return string
     */
    public function getBaseUrl(){
        $baseUrl = str_replace(['public', '\\'], ['', '/'], dirname(Environment::get('SCRIPT_NAME')));
        return $baseUrl;
    }

    /**
     * Get the root URL for the application
     * 
     * @return string
     */
    public function root()
    {
        return $this->getProtocolAndHost().$this->getBaseUrl();
    }
 }