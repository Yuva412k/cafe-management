<?php

/**
 * Error Controller Class
 * 
 */

namespace app\application\core;

use app\core\Config;
use app\core\Controller;

class ErrorController extends Controller{

    public function isAuthorized()
    {
        return true;
    }
    public function notFound(){
        $this->view->render( Config::get('ERRORS_PATH') . "404");
    }

    public function unAuthenticated(){
        $this->view->render( Config::get('ERRORS_PATH') . "401");
    }

    public function unAuthorized(){
        $this->view->render( Config::get('ERRORS_PATH') . "403");
    }

    public function badRequest(){
        $this->view->render( Config::get('ERRORS_PATH') . "400");
    }

    public function system(){
        $this->view->render( Config::get('ERRORS_PATH') . "500");
    }
}