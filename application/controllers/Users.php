<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Users extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addUser', 'updateUser', 'removeUser', 'removeMulitpleUser'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addUser': 
                $this->Security->config('form',['fields'=>['name','email','role_id','password','cpassword']]);
                break;
            case 'updateUser': 
                $this->Security->config('form',['fields'=>['id','name','email','role_id','password','cpassword']]);
                break;
            case 'removeUser':
                $this->Security->config('form',['fields'=>['id']]);    
                break; 
            case 'updateProfilePicture':
                $this->Security->config('form',['file','id']);    
                break;
            case 'removeMultipleUser':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('usersModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'users');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "users/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'users/add');
       $this->loadModel('rolesModel');
       $usersData =$this->rolesModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "users/add", ['usersData'=>$usersData]);
    }

    public function addUser()
    {
        $usersFields =['name','email','role_id','password','cpassword'];
        $fields = [];
        foreach($usersFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->usersModel->verifyAndSave($fields);
        $fileData   = $this->request->data("file");
       
        if(is_array($result)){
            foreach($result as $err){
                echo $err;
            }
            return;
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','User Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
       $this->loadModel('rolesModel');
        $id = $this->request->param('args')[0];
        $result = $this->usersModel->get_details($id);
        $usersData =$this->rolesModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "users/add", ['result'=>$result,'usersData'=>$usersData]); 
    }
    public function updateUser()
    {
        $usersFields = ['id','name','email','role_id','password','cpassword'];
        $fields = [];
        foreach($usersFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->usersModel->updateUserFromTable($fields);

        if(is_string($result)){
            echo "This User Name or User Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','User Successfully Updated!');
            echo "success";
        }
    }
    public function updateProfilePicture(){

        $fileData   = $this->request->data("file");
        $image      = $this->usersModel->updateProfilePicture(Session::getUserId(), $fileData);

        if(!$image){
            echo $this->usersModel->errors();
        }

        return true;
    }
    
    public function updatePassword()
    {        
        $id = $this->request->data('id');
        $currentPass = $this->request->data('currentPass');
        $newPass = $this->request->data('newPass');
        $cnewPass = $this->request->data('cnewPass');
        if($newPass != $cnewPass){
            echo "Password doesn't match! try agian";
            exit;
        }
        $result = $this->usersModel->changePassword($currentPass, $newPass,$id);

        if(is_string($result)){
            echo $result;
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    
    public function removeUser()
    {        
        $id = "'".$this->request->data('id')."'";
        $result = $this->usersModel->removeUserFromTable($id);

        if(is_string($result)){
            echo $result;
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Useres
     */
    public function removeMultipleUser()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->usersModel->removeUserFromTable($ids);
        
        if(is_string($result)){
            echo $result;
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }

    public function ajaxList()
    {

        $result = $this->usersModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $users){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$users['id']."'>";
            $row[] = $users['name'];
            $row[] = $users['role_name'];
            $row[] = $users['email'];
            $url = PUBLIC_ROOT.'users/update/'.$users['id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_users(\"". $users["id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";
            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_users(\''.$users["id"].'\')">
                        <i class="fa fa-fw fa-trash text-red"></i>Delete
                    </a>
                </li>
                
            </ul>
        </div>';		

        $row[] = $str2;

            $data[] = $row;
        }
        $ajaxData = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->usersModel->countAll(),
            'recordsFiltered' => $this->usersModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "users";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleUser'=>'delete',
            'removeUser'=>'delete',
            'updateUser'=>'edit',
            'addUser'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}