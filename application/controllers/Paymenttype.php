<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Paymenttype extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addPaymenttype', 'updatePaymenttype', 'removePaymenttype', 'removeMulitplePaymenttype'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addPaymenttype': 
                $this->Security->config('form',['fields'=>['paymenttype_name','paymenttype_id','paymenttype_description']]);
                break;
            case 'updatePaymenttype': 
                $this->Security->config('form',['fields'=>['id','paymenttype_name','paymenttype_id','paymenttype_description']]);
                break;
            case 'removePaymenttype':
                $this->Security->config('form',['fields'=>['paymenttype_id']]);    
                break;
            case 'removeMultiplePaymenttype':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('paymenttypeModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'paymenttype');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "paymenttype/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'paymenttype/add');
       $paymenttypeid = $this->paymenttypeModel->createPaymenttypeId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "paymenttype/add", ['paymenttype_id'=>$paymenttypeid]);
    }

    public function addPaymenttype()
    {
        $paymenttypeFields = ['paymenttype_id', 'paymenttype_name', 'paymenttype_description'];
        $fields = [];
        foreach($paymenttypeFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->paymenttypeModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Paymenttype Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->paymenttypeModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "paymenttype/add", $result); 
    }
    public function updatePaymenttype()
    {
        $paymenttypeFields = ['id','paymenttype_id', 'paymenttype_name', 'paymenttype_description'];
        $fields = [];
        foreach($paymenttypeFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->paymenttypeModel->updatePaymenttypeFromTable($fields);

        if(is_string($result)){
            echo "This Paymenttype Name or Paymenttype Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Paymenttype Successfully Updated!');
            echo "success";
        }
    }
    public function removePaymenttype()
    {        
        $id = "'".$this->request->data('paymenttype_id')."'";
        $result = $this->paymenttypeModel->removePaymenttypeFromTable($id);

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
     * Remove Multiple Paymenttypees
     */
    public function removeMultiplePaymenttype()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->paymenttypeModel->removePaymenttypeFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Payment Type Name {".$result."} already in use in Items!";
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

        $result = $this->paymenttypeModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $paymenttype){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$paymenttype['paymenttype_id']."'>";
            $row[] = $paymenttype['paymenttype_id'];
            $row[] = $paymenttype['paymenttype_name'];
            $row[] = $paymenttype['paymenttype_description'];
            $url = PUBLIC_ROOT.'paymenttype/update/'.$paymenttype['paymenttype_id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_paymenttype(\"". $paymenttype["paymenttype_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_paymenttype(\''.$paymenttype["paymenttype_id"].'\')">
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
            'recordsTotal' => $this->paymenttypeModel->countAll(),
            'recordsFiltered' => $this->paymenttypeModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "paymenttypes";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultiplePaymenttype'=>'delete',
            'removePaymenttype'=>'delete',
            'updatePaymenttype'=>'edit',
            'addPaymenttype'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}