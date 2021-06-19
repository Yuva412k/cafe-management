<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Category extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addCategory', 'updateCategory', 'removeCategory', 'removeMulitpleCategory'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addCategory': 
                $this->Security->config('form',['fields'=>['category_name','category_id','category_description']]);
                break;
            case 'updateCategory': 
                $this->Security->config('form',['fields'=>['id','category_name','category_id','category_description']]);
                break;
            case 'removeCategory':
                $this->Security->config('form',['fields'=>['category_id']]);    
                break;
            case 'removeMultipleCategory':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('categoryModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'category');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "category/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'category/add');
       $categoryid = $this->categoryModel->createCategoryId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "category/add", ['category_id'=>$categoryid]);
    }

    public function addCategory()
    {
        $categoryFields = ['category_id', 'category_name', 'category_description'];
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUserRole();
        $fields = [];
        foreach($categoryFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $fields = array_merge($fields, ['created_date'=>$createdDate , 'created_by'=>$createdBy]);
        $result = $this->categoryModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Category Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->categoryModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "category/add", $result); 
    }
    public function updateCategory()
    {
        $categoryFields = ['id','category_id', 'category_name', 'category_description'];
        $fields = [];
        foreach($categoryFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->categoryModel->updateCategoryFromTable($fields);

        if(is_string($result)){
            echo "This Category Name or Category Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Category Successfully Updated!');
            echo "success";
        }
    }
    public function removeCategory()
    {        
        $id = "'".$this->request->data('category_id')."'";
        $result = $this->categoryModel->removeCategoryFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Category Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Categories
     */
    public function removeMultipleCategory()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->categoryModel->removeCategoryFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Category Name {".$result."} already in use in Items!";
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

        $result = $this->categoryModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $category){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$category['category_id']."'>";
            $row[] = $category['category_id'];
            $row[] = $category['category_name'];
            $row[] = $category['category_description'];
            $url = PUBLIC_ROOT.'category/update/'.$category['category_id'];
            $row[] = "<a href='#' class='row-del' onclick='delete_category(\"". $category["category_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $data[] = $row;
        }
        $ajaxData = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->categoryModel->countAll(),
            'recordsFiltered' => $this->categoryModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "items_category";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleCategory'=>'delete',
            'removeCategory'=>'delete',
            'updateCategory'=>'edit',
            'addCategory'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}