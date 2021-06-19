<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Tax extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addTax', 'updateTax', 'removeTax', 'removeMulitpleTax'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addTax': 
                $this->Security->config('form',['fields'=>['tax_name','tax_id','tax','tax_description']]);
                break;
            case 'updateTax': 
                $this->Security->config('form',['fields'=>['id','tax_name','tax_id','tax','tax_description']]);
                break;
            case 'removeTax':
                $this->Security->config('form',['fields'=>['tax_id']]);    
                break;
            case 'removeMultipleTax':
                $this->Security->config('form',['fields'=>['tax_id']]);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('taxModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'tax');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "tax/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'tax/add');
       $taxid = $this->taxModel->createTaxId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "tax/add", ['tax_id'=>$taxid]);
    }

    public function addTax()
    {
        $taxFields = ['tax_id', 'tax_name','tax', 'tax_description'];
        $fields = [];
        foreach($taxFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->taxModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Tax Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->taxModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "tax/add", $result); 
    }
    public function updateTax()
    {
        $taxFields = ['id','tax_id', 'tax_name','tax', 'tax_description'];
        $fields = [];
        foreach($taxFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->taxModel->updateTaxFromTable($fields);

        if(is_string($result)){
            echo "This Tax Name or Tax Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Tax Successfully Updated!');
            echo "success";
        }
    }
    public function removeTax()
    {        
        $id = "'".$this->request->data('tax_id')."'";
        $result = $this->taxModel->removeTaxFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Tax Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Taxes
     */
    public function removeMultipleTax()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->taxModel->removeTaxFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Tax Name {".$result."} already in use in Items!";
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

        $result = $this->taxModel->getDataTable();

        //remove the keys from the return value above
        $data = [];

        foreach($result as $tax){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$tax['tax_id']."'>";
            $row[] = $tax['tax_id'];
            $row[] = $tax['tax_name'];
            $row[] = $tax['tax_description'];
            $url = PUBLIC_ROOT.'tax/update/'.$tax['tax_id'];
            $row[] = "<a href='#' class='row-del' onclick='delete_tax(\"". $tax["tax_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $data[] = $row;
        }
        $ajaxData = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->taxModel->countAll(),
            'recordsFiltered' => $this->taxModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "tax";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleTax'=>'delete',
            'removeTax'=>'delete',
            'updateTax'=>'edit',
            'addTax'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}