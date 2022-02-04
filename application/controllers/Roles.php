<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Roles extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addRole', 'updateRole', 'removeRole', 'removeMulitpleRole'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addRole': 
                $this->Security->config('validateForm',false);    
                break;
            case 'updateRole': 
                $this->Security->config('validateForm',false);    
                break;
            case 'removeRole':
                $this->Security->config('form',['fields'=>['id']]);    
                break;
            case 'removeMultipleRole':
                $this->Security->config('form',['fields'=>['id']]);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('rolesModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'role');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "roles/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'roles/add');
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "roles/add");
    }

    public function addRole()
    {
        $roleFields = ['role_name', 'role_description'];
        $fields = [];
        foreach($roleFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->rolesModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Role Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->rolesModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "roles/add", $result); 
    }
    public function updateRole()
    {
        $roleFields = ['id', 'role_name', 'role_description'];
        $fields = [];
        foreach($roleFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->rolesModel->updateRoleFromTable($fields);

        if(is_string($result)){
            echo $result;
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Role Successfully Updated!');
            echo "success";
        }
    }
    public function removeRole()
    {        
        $id = "'".$this->request->data('id')."'";
        $result = $this->rolesModel->removeRoleFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Role Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Rolees
     */
    public function removeMultipleRole()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->rolesModel->removeRoleFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Role Name {".$result."} already in use in Items!";
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

        $result = $this->rolesModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $role){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$role['id']."'>";
            $row[] = $role['role_name'];
            $row[] = $role['role_description'];
            $url = PUBLIC_ROOT.'roles/update/'.$role['id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_role(\"". $role["id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_sales(\''.$role["id"].'\')">
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
            'recordsTotal' => $this->rolesModel->countAll(),
            'recordsFiltered' => $this->rolesModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "roles";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleRole'=>'delete',
            'removeRole'=>'delete',
            'updateRole'=>'edit',
            'addRole'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}