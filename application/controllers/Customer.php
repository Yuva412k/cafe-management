<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Customer extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addCustomer', 'updateCustomer', 'removeCustomer'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addCustomer': 
                $this->Security->config('form',['fields'=>['cust_name','cust_id','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'],'exclude'=>['save&reset','reset']]);
                break;
            case 'updateCustomer': 
                $this->Security->config('form',['fields'=>['cust_name','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'],'exclude'=>['save&reset','reset']]);
                break;
            case 'removeCustomer':
                $this->Security->config('form',['fields'=>['cust_name', 'cust_id']]);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            
        }
        $this->loadModel('customerModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'customer/add');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "customer/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'customer/add');
       $customerid = $this->customerModel->createCustomerId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "customer/add", ['customerid'=>$customerid]);
    }

    public function addCustomer()
    {
        $custFields = ['cust_name','cust_id','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'];
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUserRole();
        $fields = [];
        foreach($custFields as $field){
            if($field==='cust_mobile' || $field === 'cust_pincode' || $field === 'cust_balance'){
                $fields[$field] = empty($this->request->data($field)) ? null:  (int)$this->request->data($field) ;
                continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        $fields = array_merge($fields, ['createdDate'=>$createdDate , 'createdBy'=>$createdBy]);
        $result = $this->customerModel->verifyAndSave($fields);

        if(!$result){
            Session::setFlashData('failed', 'Failed to store in database');
            echo "failed";
        }
        Session::setFlashData('success', 'Customer data is successfully stored');
        echo "success";
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->customerModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "customer/add", $result); 
    }
    public function updateCustomer()
    {
        $customerFields = ['id','customer_id', 'customer_name', 'customer_description'];
        $fields = [];
        foreach($customerFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->customerModel->updateCustomerFromTable($fields);

        if(is_string($result)){
            echo "This Customer Name or Customer Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Customer Successfully Updated!');
            echo "success";
        }
    }
    public function removeCustomer()
    {        
        $id = "'".$this->request->data('customer_id')."'";
        $result = $this->customerModel->removeCustomerFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Customer Name {".$result."} already in use in Items!";
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
    public function removeMultipleCustomer()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->customerModel->removeCustomerFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Customer Name {".$result."} already in use in Items!";
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
        // //todo get date range for customer 

        // $result = $this->customerModel->get_details($customerid);
        $result = $this->customerModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $customer){

            $row = array();
            $disable = ($customer['customer_id'] === 'CU0001') ? 'disabled' : '';
            if($customer['customer_id'] === 'CU0001'){
                $row[] = '<span class="walk-in-cust">NA</span>';
            }else{
                $row[] ='<input type="checkbox" name="checkbox[]"'.$disable.'value='.$customer['customer_id'].' class="row_check" >';
            }

            $row[] = $customer['customer_id'];
            $row[] = $customer['customer_name'];
            $row[] = $customer['mobile'];
            $row[] = $customer['customer_gstin'];
            $row[] = $customer['address'];
            $row[] = $customer['city'];
            $row[] = $customer['state'];
            $row[] = $customer['country'];
            $row[] = $customer['opening_balance'];

            $data[] = $row;
        }
        $ajaxData = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->customerModel->countAll(),
            'recordsFiltered' => $this->customerModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "customers";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleCustomer'=>'delete',
            'removeCustomer'=>'delete',
            'updateCustomer'=>'edit',
            'addCustomer'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}