<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Settings extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['updateGeneral','updatePrefix'];
        $this->Security->requirePost($actions);
        switch($action)
        {
            case 'updateGeneral': 
                $this->Security->config('validateForm',false);    
                break;
            case 'updatePrefix':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('settingsModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'settings');
        $data = $this->settingsModel->getShopDetails();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "settings/general", $data);
    }
    public function updateGeneral()
    {
        $res = $this->settingsModel->updateShopDetails($_POST);
        $this->setTheme();
        if(is_array($res)){
            foreach($res as $str){
                echo $str;
            }
           }else if(is_bool($res) && $res == false){
            echo "failed";
           }else{
               echo "success";
           }
    }
    public function prefix()
    {
        Config::setJsConfig('curPage', 'settings/prefix');
        $data = $this->settingsModel->getPrefix();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "settings/prefix", $data);
    }

    public function updatePrefix()
    {
       $res = $this->settingsModel->updatePrefix($_POST);
       if(is_array($res)){
        foreach($res as $str){
            echo $str;
        }
       }else if(is_bool($res) && $res == false){
        echo "failed";
       }else{
           echo "success";
       }
    }
    public function setTheme()
    {
        $cookie_name = "webTheme";
        $this->loadModel("settingsModel");
        $cookie_value = $this->settingsModel->getTheme();
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
    }
    public function others()
    {
        Config::setJsConfig('curPage', 'settings/others');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "settings/others");
    }


    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "units";

        $action_alias = [
            'updateGeneral' => 'edit',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}