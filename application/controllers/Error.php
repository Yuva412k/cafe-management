<?php

namespace app\application\controllers;

use app\core\Controller;

class Error extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();
    }
    
    public function index()
    {
        $code = empty($this->request->param('args')[0]) ? 404 : (int)$this->request->param('args')[0];
        $this->error($code);
    }

    public function isAuthorized()
    {
      return true;

    }
}