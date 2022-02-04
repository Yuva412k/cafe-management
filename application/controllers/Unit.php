<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Unit extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addUnit', 'updateUnit', 'removeUnit', 'removeMulitpleUnit'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addUnit': 
                $this->Security->config('form',['fields'=>['unit_name','unit_id','unit_description']]);
                break;
            case 'updateUnit': 
                $this->Security->config('form',['fields'=>['id','unit_name','unit_id','unit_description']]);
                break;
            case 'removeUnit':
                $this->Security->config('form',['fields'=>['unit_id']]);    
                break;
            case 'removeMultipleUnit':
                $this->Security->config('form',['fields'=>['unit_id']]);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('unitModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'unit');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "unit/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'unit/add');
       $unitid = $this->unitModel->createUnitId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "unit/add", ['unit_id'=>$unitid]);
    }

    public function addUnit()
    {
        $unitFields = ['unit_id', 'unit_name', 'unit_description'];
        $fields = [];
        foreach($unitFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->unitModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Unit Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->unitModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "unit/add", $result); 
    }
    public function updateUnit()
    {
        $unitFields = ['id','unit_id', 'unit_name', 'unit_description'];
        $fields = [];
        foreach($unitFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->unitModel->updateUnitFromTable($fields);

        if(is_string($result)){
            echo "This Unit Name or Unit Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Unit Successfully Updated!');
            echo "success";
        }
    }
    public function removeUnit()
    {        
        $id = "'".$this->request->data('unit_id')."'";
        $result = $this->unitModel->removeUnitFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Unit Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Unites
     */
    public function removeMultipleUnit()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->unitModel->removeUnitFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Unit Name {".$result."} already in use in Items!";
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

        $result = $this->unitModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $unit){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$unit['unit_id']."'>";
            $row[] = $unit['unit_id'];
            $row[] = $unit['unit_name'];
            $row[] = $unit['unit_description'];
            $url = PUBLIC_ROOT.'unit/update/'.$unit['unit_id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_unit(\"". $unit["unit_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_unit(\''.$unit["unit_id"].'\')">
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
            'recordsTotal' => $this->unitModel->countAll(),
            'recordsFiltered' => $this->unitModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "units";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleUnit'=>'delete',
            'removeUnit'=>'delete',
            'updateUnit'=>'edit',
            'addUnit'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}