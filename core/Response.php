<?php

/**
 * Response Class
 * 
 * handles the response text, status and headers of a http response
 */

namespace app\core;

class Response{

    public array $headers;

    private string $content;

    private string $version = '1.1';

    private int $statusCode;

    private $file;

    private string $statusText;

    private string $charset = 'UTF-8';

    /**
     * Holds HTTP response statuses
     * 
     * @var array
     */
    private array $statusTexts = [
        200 => "OK",
        302 =>"Found",
        400 =>"Bad Request",
        401 =>"Unauthorized",
        403 => "Forbidden",
        404 => "File Not Found",
        500 => "Internal Server Error"
    ];


    /**
     * Constructor
     * 
     * @param string $content
     * @param int $status
     * @param array $headers 
     */
    public function __construct($content='', $status = 200, $headers=[])
    {
        $this->content = $content;
        $this->statusCode = $status;
        $this->headers = $headers;
        $this->statusText = $this->statusTexts[$status];
    }

    /**
     * Sends HTTP headers and content
     * 
     */
    public function send()
    {

        $this->sendHeaders();


        if($this->file){
            $this->readFile();
        }else{

            $this->sendContent();
        }

        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) !== 'cli') {
            $this->flushBuffer();
        }
        return $this;
    }

    /**
     * Clean (erase) the output buffer
     *
     * @return void
     */
    public function clearBuffer(){
	
		// check if output_buffering is active
		if(ob_get_level() > 0){
			return ob_clean();
		}
    }

    /**
     * Flushes output buffers
     */
    private function flushBuffer()
    {
        ob_flush();
    }

    /**
     * read file
     *
     * @return Response
     */
    private function readFile(){
        readfile($this->file);
        return $this;
    }

    /**
     * Sends HTTP headers
     * 
     * @return Response
     */
    private function sendHeaders(){
        if(headers_sent()){
            return $this;
        }

         // status
         header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

         // Content-Type
         // if Content-Type is already exists in headers, then don't send it
         if(!array_key_exists('Content-Type', $this->headers)){
             header('Content-Type: ' . 'text/html; charset=' . $this->charset);
         }
 
         // headers
         foreach ($this->headers as $name => $value) {
             header($name .': '. $value, true, $this->statusCode);
         }
 
         return $this;


    }
    
    /**
     * Sends content for the current web response
     * 
     * @return Response
     */
    private function sendContent()
    {
        echo $this->content;
        return $this;
    }

    /**
     * Sets content for the current web response.
     * 
     * @param string $content The response content
     * @return Response
     */
    public function setContent($content = "")
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Sets content-type for the current web response.
     * 
     * @param string|null $contentType The response content
     * @return Response
     */
    public function type($contentType = null){

        if($contentType == null){
            unset($this->headers['Content-Type']);
        }else{
            $this->headers['Content-Type'] = $contentType;
        }

        return $this;
    }

    /**
    * Stop execution of the current script.
    *
    * @param int|string $status
    * @return void
    * @see http://php.net/exit
    */
    public function stop($status = 0){
        exit($status);
    }

    /**
     * Sets the response status code & it's relevant text.
     *
     * @param int $code HTTP status code
     * @return Response
     */
    public function setStatusCode($code){

        $this->statusCode = (int) $code;
        $this->statusText = isset($this->statusTexts[$code]) ? $this->statusTexts[$code] : '';

        return $this;
    }



 }