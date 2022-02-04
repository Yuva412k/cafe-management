<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Supplier extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addSupplier', 'updateSupplier','removeSupplier','showPayNowModal','savePayment','showPayReturnDueModal','saveReturnDuePayment'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addSupplier': 
                $this->Security->config('form',['fields'=>['supplier_name','supplier_id','supplier_mobile', 'supplier_GST', 'supplier_balance', 'supplier_country', 'supplier_state', 'supplier_city', 'supplier_pincode', 'supplier_address']]);
                break;
            case 'updateSupplier': 
                $this->Security->config('form',['fields'=>['supplier_name','supplier_mobile', 'supplier_GST', 'supplier_balance', 'supplier_country', 'supplier_state', 'supplier_city', 'supplier_pincode', 'supplier_address']]);
                break;
            case 'removeSupplier':
                $this->Security->config('form',['fields'=>['supplier_id']]);    
                break;
            case 'removeMultipleSupplier':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
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
        $this->loadModel('supplierModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'supplier/add');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "supplier/list");
    }

    public function add()
    {
        //todo
        //supplier id auto generate
       Config::setJsConfig('curPage', 'supplier/add');
       $supplierid = $this->supplierModel->createSupplierId();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "supplier/add", ['supplier_id'=>$supplierid]);
    }

    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->supplierModel->get_details($id);
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "supplier/add", $result); 
    }
    public function updateSupplier()
    {
        $supplierFields = ['id','supplier_id', 'supplier_name', 'supplier_description'];
        $fields = [];
        foreach($supplierFields as $field){
            $fields[$field] = $this->request->data($field);
        }
        $result = $this->supplierModel->updateSupplierFromTable($fields);

        if(is_string($result)){
            echo "This Supplier Name or Supplier Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Supplier Successfully Updated!');
            echo "success";
        }
    }
    public function removeSupplier()
    {        
        $id = "'".$this->request->data('supplier_id')."'";
        $result = $this->supplierModel->removeSupplierFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Supplier Name {".$result."} already in use in Items!";
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
    public function removeMultipleSupplier()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->supplierModel->removeSupplierFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Supplier Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }


    public function addSupplier()
    {
        $custFields = ['supplier_name','supplier_id','supplier_mobile', 'supplier_GST', 'supplier_balance', 'supplier_country', 'supplier_state', 'supplier_city', 'supplier_pincode', 'supplier_address'];
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $fields = [];
        foreach($custFields as $field){
            if($field=='supplier_mobile' || $field == 'supplier_pincode' || $field == 'supplier_balance'){
                $fields[$field] = empty($this->request->data($field)) ? null:  (int)$this->request->data($field) ;
                continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        $fields = array_merge($fields, ['createdDate'=>$createdDate , 'createdBy'=>$createdBy]);
        $result = $this->supplierModel->verifyAndSave($fields);

        if(!$result){
            Session::setFlashData('failed', 'Failed to store in database');
            echo "failed";
        }
        Session::setFlashData('success', 'Supplier data is successfully stored');
        echo "success";
    }

    public function ajaxList()
    {
        // //todo get date range for supplier 

        // $result = $this->supplierModel->get_details($supplierid);
        $result = $this->supplierModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $supplier){

            $row = array();
        
            $row[] ='<input type="checkbox" name="checkbox[]" onclick="checkcheckbox()" value='.$supplier['supplier_id'].' class="row_check" >';

            $row[] = $supplier['supplier_id'];
            $row[] = $supplier['supplier_name'];
            $row[] = $supplier['mobile'];
            $row[] = $supplier['supplier_gstin'];
            $row[] = $supplier['address'];
            $row[] = $supplier['city'];
            $row[] = $supplier['state'];
            $row[] = $supplier['purchase_due'];
            $row[] = $supplier['purchase_return_due'];
            $row[] = $supplier['opening_balance'];
            $url = PUBLIC_ROOT.'supplier/update/'.$supplier['supplier_id'];
            // $row[] = "<a href='#' class='row-del' onclick='delete_supplier(\"". $supplier["supplier_id"]."\")'>Delete</a> <a href='$url' class='row-edit' >update</a>";

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="'.$url.'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';
                $str2.='<li>
                <a style="cursor:pointer;" title="Pay Opening Balance & Purchase Due Payments" onclick="pay_now(\''.$supplier['supplier_id'].'\')" >
                    <i class="fas fa-money"></i> Pay Due 
                    </a>
                </li>';
                $str2.='<li>
                <a style="cursor:pointer;" title="Pay Return Due"  onclick="pay_return_due(\''.$supplier['supplier_id'].'\')" >
                    <i class="fas fa-money"></i> Pay Return D...
                </a>
                </li>';
                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_sales(\''.$supplier["supplier_id"].'\')">
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
            'recordsTotal' => $this->supplierModel->countAll(),
            'recordsFiltered' => $this->supplierModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function showPayNowModal()
    {
        $supplier_id = $this->request->data('supplier_id');
        $res = $this->supplierModel->showPayNowModal($supplier_id);
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
        $res = $this->supplierModel->savePayment(array_merge($_POST,['created_by'=>$createdBy, 'created_date'=>$createdDate, 'created_time'=>$created_time]));
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
        $supplier_id = $this->request->data('supplier_id');
        $res = $this->supplierModel->showPayReturnDueModal($supplier_id);
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
        $res = $this->supplierModel->saveReturnDuePayment(array_merge($_POST,['created_by'=>$createdBy, 'created_date'=>$createdDate, 'created_time'=>$created_time]));
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
        $resource = "suppliers";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleSupplier'=>'delete',
            'removeSupplier'=>'delete',
            'updateSupplier'=>'edit',
            'addSupplier'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}