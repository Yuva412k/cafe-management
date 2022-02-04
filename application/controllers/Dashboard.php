<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Dashboard extends Controller{


    public function beforeAction()
    {
        parent::beforeAction();
        $this->loadModel('dashboardModel');
    }

    public function index()
    {
       $data = $this->dashboardModel->getDashboardData(); 
       $dataTable = $this->dashboardModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "dashboard", array_merge($data, $dataTable));
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "dashboard";

        
        return Permission::check($role, $resource, $action);
    }
}