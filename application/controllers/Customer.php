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

        $actions = ['ajaxList','addCustomer', 'updateCustomer', 'removeCustomer','showPayNowModal','savePayment','showPayReturnDueModal','saveReturnDuePayment'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addCustomer': 
                $this->Security->config('form',['fields'=>['cust_name','cust_id','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'],'exclude'=>['reset','reset']]);
                break;
            case 'updateCustomer': 
                $this->Security->config('form',['fields'=>['id','cust_id','cust_name','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'],'exclude'=>['reset','reset']]);
                break;
            case 'removeCustomer':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            case 'removeMultipleCustomer':
                $this->Security->config('validateForm',false);    
                break;
            case 'showPayNowModal':
                $this->Security->config('validateForm',false);    
                break;
            case 'savePayment':
                $this->Security->config('validateForm',false);    
                break;
            case 'showPayReturnDueModal':
                $this->Security->config('validateForm',false);    
                break;
            case 'saveReturnDuePayment':
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
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "customer/add", ['customer_id'=>$customerid]);
    }

    public function addCustomer()
    {
        $custFields = ['cust_name','cust_id','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'];
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $fields = [];
        foreach($custFields as $field){
            if($field=='cust_mobile' || $field == 'cust_pincode' || $field == 'cust_balance'){
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
        $custFields = ['id','cust_name','cust_id','cust_mobile', 'cust_GST', 'cust_balance', 'cust_country', 'cust_state', 'cust_city', 'cust_pincode', 'cust_address'];

        $fields = [];
        foreach($custFields as $field){
            if($field=='cust_mobile' || $field == 'cust_pincode' || $field == 'cust_balance'){
                $fields[$field] = empty($this->request->data($field)) ? null:  (int)$this->request->data($field) ;
                continue;
            }
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
            $disable = ($customer['customer_id'] == 'CU0001') ? 'disabled' : '';
            if($customer['customer_id'] == 'CU0001'){
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
            $row[] = $customer['sales_due'];
            $row[] = $customer['sales_return_due'];
            $row[] = $customer['opening_balance'];
            $url = PUBLIC_ROOT.'customer/update/'.$customer['customer_id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_customer(\"". $customer["customer_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';
                $str2.='<li>
                <a style="cursor:pointer;" title="Pay Opening Balance & Sales Due Payments" onclick="pay_now(\''.$customer['customer_id'].'\')" >
                    <i class="fas fa-money"></i> Pay Due 
                    </a>
                </li>';
                $str2.='<li>
                <a style="cursor:pointer;" title="Pay Return Due"  onclick="pay_return_due(\''.$customer['customer_id'].'\')" >
                    <i class="fas fa-money"></i> Pay Return D...
                </a>
                </li>';
                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_customer(\''.$customer["customer_id"].'\')">
                        <i class="fa fa-fw fa-trash text-red"></i> Delete
                    </a>
                </li>
                
            </ul>
        </div>';		

        $row[] = $str2;
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

    public function showPayNowModal()
    {
        $customer_id = $this->request->data('customer_id');
        $res = $this->customerModel->showPayNowModal($customer_id);
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }
    }

    public function savePayment()
    {
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $created_time = date('h:m:s');
        $res = $this->customerModel->savePayment(array_merge($_POST,['created_by'=>$createdBy, 'created_date'=>$createdDate, 'created_time'=>$created_time]));
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }else{
            echo 'success';
        }
    }

    public function showPayReturnDueModal()
    {
        $customer_id = $this->request->data('customer_id');
        $res = $this->customerModel->showPayReturnDueModal($customer_id);
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }
    }
    public function saveReturnDuePayment()
    {
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $created_time = date('h:m:s');
        $res = $this->customerModel->saveReturnDuePayment(array_merge($_POST,['created_by'=>$createdBy, 'created_date'=>$createdDate, 'created_time'=>$created_time]));
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }else{
            return 'success';
        }
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
            'showPayNowModal','sales_payment_add',
            'savePayment', 'sales_payment_add',
            'saveReturnDuePayment','sales_return_payment_add',
            'showPayReturnDueModal','sales_return_payment_add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}