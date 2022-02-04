<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class SalesReturn extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','showPayNowModal','deletePayment','savePayment','viewPaymentsModal','removeMultipleSales','returnSalesList','returnSalesReturnList','customerAjax','returnRowWithData','addSales', 'updateSales', 'removeSales'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addSales': 
                $this->Security->config('validateForm',false);    
                break;
            case 'updateSales': 
                $this->Security->config('validateForm',false);    
                break;
            case 'removeSales':
                $this->Security->config('form',['fields'=>['return_id']]);    
                break;
            case 'removeMultipleSales':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            case 'customerAjax':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnRowWithData':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnSalesList':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnSalesReturnList':
                $this->Security->config('validateForm',false);    
                break;    
            case 'viewPaymentsModal':
                $this->Security->config('validateForm',false);    
                break;
            case 'savePayment':
                $this->Security->config('validateForm',false);    
                break;
            case 'deletePayment':
                $this->Security->config('validateForm',false);    
                break;
            case 'showPayNowModal':
                $this->Security->config('validateForm',false);    
                break;
        }
        $this->loadModel('salesReturnModel');
        $this->loadModel('salesModel');
        $this->loadModel('taxModel');
        $this->loadModel('paymenttypeModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'salesReturn/list');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "salesreturn/list");
    }

    public function create()
    {
        //todo
        //sales id auto generate
        $paymenttypeData = $this->paymenttypeModel->getDataTable();
       Config::setJsConfig('curPage', 'salesReturn/add');
       $salesid = $this->salesReturnModel->createSalesReturnId();
        $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "salesreturn/add", ['taxData'=>$taxData,'return_id'=>$salesid,'request_name'=>'create' ,"paymenttypeData"=>$paymenttypeData]);
    }

    public function add()
    {
        //todo
        //sales id auto generate
        $paymenttypeData = $this->paymenttypeModel->getDataTable();
       $sales_id = $this->request->param('args')[0];
       Config::setJsConfig('curPage', 'salesReturn/add');
       $salesData = $this->salesModel->get_details($sales_id);
       $return_id = $this->salesReturnModel->createSalesReturnId();
       $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "salesreturn/add", ['taxData'=>$taxData, 'request_name'=>'add','return_id'=>$return_id, 'returnData'=>$salesData, 'sales_id'=>$sales_id,"paymenttypeData"=>$paymenttypeData]);
    }
    public function addSales()
    {
        $salesFields = ['return_date','sales_id','return_id','round_off' ,'reference_no','sub_total' ,'grand_total','payment_type', 'payment_status','amount','payment_note', 'customer_id', 'paid_amount', 'return_status', 'return_due','other_charges_input','other_charges_amt', 'other_charges_type', 'discount_on_all_input','discount_on_all_type', 'discount_on_all_amt', 'tax_id','tax_amt_cgst','tax_amt_sgst', 'hidden_rowcount'];
        $created_by = Session::getUsername();
        $created_date = date('Y-m-d');
        $sales_time = date('h:m:s');
        $fields = [];
        foreach($salesFields as $field){
            if($field=='hidden_rowcount'){
                $fields[$field] = empty($this->request->data($field)) || $this->request->data($field) == 0 ? null:  (int)$this->request->data($field) ;
                continue;
            }
            if($field=='tax_amt_cgst' || $field == 'tax_amt_sgst' || $field == 'round_off' ){
                $fields[$field] = empty($this->request->data($field)) || $this->request->data($field) == 0 ? null:  (float)$this->request->data($field) ;
                continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        if(empty($fields['sales_id'])){
            $fields['sales_id'] = null;
        }
        if(empty($fields['other_charges_input'])){
            $fields['other_charges_input'] = null;
        }
        if(empty($fields['discount_on_all_input'])){
            $fields['discount_on_all_input'] = null;
        }
        $command = $this->request->param('args')[0];

        $fields = array_merge($fields, ['created_date'=>$created_date,'created_by'=>$created_by,'created_time'=>$sales_time]);
        $result = $this->salesReturnModel->verifyAndSave($fields, $command);

       if(is_string($result)){
            if($result == "redirect"){
                Session::setFlashData('warning', 'Sales Return Invoice Already Generated!');
                $this->redirector->to(PUBLIC_ROOT."/salesReturn/update/".$fields['return_id']);
            }else{
                echo $result;
                $this->redirector->to($_SERVER['HTTP_REFERER']);
            }
        }else if(is_array($result)){
            foreach($result as $err){
                echo $err;
            }
            return;
        }else if(!$result){
            Session::setFlashData('failed', 'Failed to store in database');
            echo "failed";
        }else{
            Session::setFlashData('success', 'sales return data is successfully stored');
            echo "success&&".$fields['return_id'];
        }
    }

    public function update()
    {
        $paymenttypeData = $this->paymenttypeModel->getDataTable();
        $id = $this->request->param('args')[0];
        $salesData = $this->salesReturnModel->get_details($id);
        $taxData = $this->taxModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "salesreturn/add", ['id'=>$salesData['id'],'taxData'=>$taxData, 'returnData'=>$salesData,'request_name'=>'update', "paymenttypeData"=>$paymenttypeData]);
    }

    public function ajaxList()
    {
        // //todo get date range for sales 

        $result = $this->salesReturnModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $sales){

            $row = array();

            $disable = ($sales['id'] == 1) ? 'disabled' : '';
            
            $row[] ='<input type="checkbox" onclick="checkcheckbox()" name="checkbox[]"'.$disable.'value='.$sales['return_id'].' class="row_check" >';
            $row[] = $sales['return_date'];
            $row[] = $sales['sales_id'];
            $row[] = $sales['return_id'];
            $row[] = $sales['return_status'];
            $row[] = $sales['reference_no'];
            $row[] = $sales['customer_name'];
            $row[] = number_format($sales['grand_total'],2);
            $row[] = number_format($sales['paid_amount'],2);
            $row[] = number_format($sales['return_due'],2);
            $str='';
            if($sales['payment_status'] =='Unpaid')
              $str= "<span class='label label-danger' style='cursor:pointer'>Unpaid </span>";
            if($sales['payment_status'] =='Partial')
              $str="<span class='label label-warning' style='cursor:pointer'> Partial </span>";
            if($sales['payment_status'] =='Paid')
              $str="<span class='label label-success' style='cursor:pointer'> Paid </span>";

            $row[] = $str;
            $row[] = ucfirst($sales['created_by']);

            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="View Invoice" href="salesReturn/invoice/'.$sales['return_id'].'" >
                        <i class="fa fa-fw fa-eye text-blue"></i>View sales
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" href="salesReturn/update/'.$sales['return_id'].'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Pay" style="cursor:pointer" onclick="pay_now(\''.$sales['return_id'].'\')" >
                        <i class="fa fa-fw fa-hourglass-half text-blue"></i>Pay Now
                    </a>
                </li>
                <li>
                    <a title="Pay" style="cursor:pointer" onclick="view_payments(\''.$sales['return_id'].'\')" >
                        <i class="fas fa-money"></i>View Payments
                    </a>
                </li>';
                $str2.='<li>
                    <a title="Update Record ?" target="_blank" href="salesReturn/invoice/'.$sales['return_id'].'">
                        <i class="fa fa-fw fa-print text-blue"></i>Print
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_sales(\''.$sales['return_id'].'\')">
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
            'recordsTotal' => $this->salesReturnModel->countAll(),
            'recordsFiltered' => $this->salesReturnModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }


    /**
     * Customer ajaxList for select 2 search option
     */
    public function customerAjax()
    {

        $search = $this->request->data('searchTerm');

        $result = $this->salesReturnModel->getCustomerSelect($search);

        if(!empty($result)){
            $data = array();

            foreach($result as $name){

                $row= array();
                $row['id'] =$name['customer_id'];
                $row['text'] = $name['customer_name'];
                
                $data[] =$row;
            }

        echo json_encode($data);

        }
    }

	//Table ajax code
	public function search_item(){
		$q=$this->request->data('q');
		$result=$this->salesModel->searchItem($q);
		echo $result;
	}
	public function find_item_details(){
		$id=$this->request->data('id');
		
		$result=$this->salesModel->findItemDetails($id);
		echo $result;
	}

    
    public function returnRowWithData(){
        $rowcount = $this->request->data('rowcount');
        $item_id = $this->request->data('item_id');
		echo $this->salesModel->getItemsInfo($rowcount,$item_id);
	}
	public function returnSalesReturnList(){
        $return_id = $this->request->param('args')[0];
		echo $this->salesReturnModel->returnSalesList($return_id);
	}

    public function returnSalesList(){
        $sales_id = $this->request->param('args')[0];
		echo $this->salesModel->returnSalesList($sales_id);
	}

	public function deletePayment(){
		$payment_id = $this->request->data('payment_id');
		$res = $this->salesReturnModel->deletePayments($payment_id);
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }else{
            echo 'success';
        }
	}
    public function showPayNowModal(){
		$return_id=$this->request->data('return_id');
		$res = $this->salesReturnModel->showPayNowModal($return_id);
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }
	}

    public function savePayment(){
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $created_time = date('h:m:s');
		$res = $this->salesReturnModel->savePayment(array_merge($_POST,['created_by'=>$createdBy, 'created_date'=>$createdDate, 'created_time'=>$created_time]));
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }else{
            echo 'success';
        }
	}
	public function viewPaymentsModal(){
        $sales_id=$this->request->data('sales_id');
		$res = $this->salesReturnModel->viewPaymentModal($sales_id);
        if( is_string($res)){
            echo $res;
        }else if(is_bool($res) && $res == false){
            echo "failed";
        }
    }

    public function invoice()
    {
        $return_id = $this->request->param('args')[0];
        $data = $this->salesReturnModel->invoiceDetails($return_id);
        $this->view->render(Config::get("VIEWS_PATH").'invoice/salesreturn_invoice', $data);
    }

    public function removeSales()
    {        
        $id = "'".$this->request->data('return_id')."'";
        $result = $this->salesReturnModel->removeSalesReturnFromTable($id);

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
     * Remove Multiple Sales
     */
    public function removeMultipleSales()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->salesReturnModel->removeSalesFromTable($ids);

        if(is_string($result)){
            echo $result;
        }else if(!$result){
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }


    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "sales_return";

        $action_alias = [
            'ajaxList' => 'view',
            'addSales'=>'add',
            'updateSales'=>'edit',
            'removeSales'=>'delete',
            'removeMultipleSales'=>'delete',
            'returnSalesList'=>'add',
            'returnSalesReturnList'=>'edit',
            'returnRowWithData'=>'add',
            'customerAjax'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}