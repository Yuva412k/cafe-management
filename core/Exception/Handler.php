<?php

/**
 * Exception Handler
 * 
 * Provides basic error and exception handling
 * It captures and handles all unhandled exceptions and errors
 */

 namespace app\core\Exception;

use app\application\core\ErrorController;
use app\core\Logger;

class Handler{

    /**
     *@return void
     */
    public static function setHandler()
    {
        //turn off error reporting
        // error_reporting(0);

        set_error_handler(__CLASS__."::handleError");
        set_exception_handler(__CLASS__."::handleException");
        register_shutdown_function(__CLASS__."::handleFatalError");
    }


    /**
     * Handle fatal errors
     * 
     * @return void
     */
    public static function handleFatalError()
    {
        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cli') {return;}
        $error = error_get_last();

        if(!is_array($error)){return;}
        $fatals = [E_USER_ERROR, E_ERROR, E_PARSE];

        if(!in_array($error['type'], $fatals, true))
        {
            return;
        }

        self::handleException(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
    }

    /**
     * Handle errors
     * 
     * @return void
     * @throws ErrorException
     */
    public static function handleError($errno, $errmsg, $filename, $linenum){
       throw new \ErrorException($errmsg,0, $errno, $filename, $linenum);
    }

    /**
     * Handle & log exceptions
     * 
     * @param Throwable $e
     * @return void
     */
    public static function handleException($e){
        Logger::Log(get_class($e),$e->getMessage(),$e->getFile(), $e->getLine());
        self::render($e)->send();
    }

    /**
     * Diplay System error page as result of an error or exception
     * 
     * @param Throwable $e
     * @return Response
     */
    private static function render($e)
    {
        if($e->getCode()==400){
            return (new ErrorController())->error(400);
        }
        return (new ErrorController())->error(500);
    }

    /**
     * Map an error code to error text
     * 
     * @param int $errno
     * @return string error text
     */
    private static function errorType($errno){

        // define an assoc array of error string
        $errortype = array (
            E_ERROR              => 'Error',
            E_WARNING            => 'Warning',
            E_PARSE              => 'Parsing Error',
            E_NOTICE             => 'Notice',
            E_CORE_ERROR         => 'Core Error',
            E_CORE_WARNING       => 'Core Warning',
            E_COMPILE_ERROR      => 'Compile Error',
            E_COMPILE_WARNING    => 'Compile Warning',
            E_USER_ERROR         => 'User Error',
            E_USER_WARNING       => 'User Warning',
            E_USER_NOTICE        => 'User Notice',
            E_STRICT             => 'Runtime Notice',
            E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
        );

        return $errortype[$errno];
    }
 }