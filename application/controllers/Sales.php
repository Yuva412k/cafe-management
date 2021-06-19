<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Sales extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','removeMultipleSales','returnSalesList','customerAjax','returnRowWithData','addSales', 'updateSales', 'removeSales'];
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
                $this->Security->config('form',['fields'=>['sales_id']]);    
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
                
        }
        $this->loadModel('salesModel');
        $this->loadModel('unitModel');
        $this->loadModel('taxModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'sales/add');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "sales/list");
    }

    public function add()
    {
        //todo
        //sales id auto generate
        $paymentData = ['payment_type'=>'cash','length'=>1];
       Config::setJsConfig('curPage', 'sales/add');
       $salesid = $this->salesModel->createSalesId();
        $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "sales/add", ['taxData'=>$taxData,'sales_id'=>$salesid, "paymentData"=>$paymentData]);
    }

    public function addSales()
    {
        $salesFields = ['sales_date','sales_id','round_off' ,'reference_no','sub_total' ,'grand_total','payment_type', 'payment_status','amount','payment_note', 'created_by', 'customer_id', 'paid_amount', 'sales_status', 'sales_due','other_charges_input','other_charges_amt', 'other_charges_type', 'discount_on_all_input','discount_on_all_type', 'discount_on_all_amt', 'tax_id','tax_amt_cgst','tax_amt_sgst', 'hidden_rowcount'];
        $created_by = Session::getUserRole();
        $created_date = date('Y-j-d');
        $sales_time = date('h:m:s');
        $fields = [];
        foreach($salesFields as $field){
            if($field=='hidden_rowcount'){
                $fields[$field] = empty($this->request->data($field)) || $this->request->data($field) == 0 ? null:  (int)$this->request->data($field) ;
                continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        if(empty($fields['other_charges_input']) || $fields['other_charges_input'] == 0){
            $fields['other_charges_input'] = null;
        }
        if(empty($fields['discount_on_all_input']) || $fields['discount_on_all_input'] == 0){
            $fields['discount_on_all_input'] = null;
        }
        if(empty($fields['tax_amt_cgst']) || $fields['tax_amt_cgst'] == 0){
            $fields['tax_amt_cgst'] = null;
        }
        if(empty($fields['tax_amt_sgst']) || $fields['tax_amt_sgst'] == 0){
            $fields['tax_amt_sgst'] = null;
        }
        $command = $this->request->param('args')[0];

        $fields = array_merge($fields, ['created_date'=>$created_date,'created_by'=>$created_by, 'sales_time'=>$sales_time,'created_time'=>$sales_time]);
        $result = $this->salesModel->verifyAndSave($fields, $command);

        if(is_array($result)){
            foreach($result as $err){
                echo $err;
            }
            return;
        }
        if(!$result){
            Session::setFlashData('failed', 'Failed to store in database');
            echo "failed";
        }else{
            Session::setFlashData('success', 'sales data is successfully stored');
            echo "success&&".$fields['sales_id'];
        }
    }

    public function update()
    {
        $sales_id = $this->request->param('args')[0];
        $paymentData = ['payment_type'=>'cash','length'=>1];
        $salesData = $this->salesModel->get_details($sales_id);
        $taxData = $this->taxModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "sales/add", ['id'=>$salesData['id'],'taxData'=>$taxData, 'salesData'=>$salesData, 'paymentData'=>$paymentData]);
    }

    public function ajaxList()
    {
        // //todo get date range for sales 

        $result = $this->salesModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $sales){

            $row = array();

            $disable = ($sales['id'] === 1) ? 'disabled' : '';
            
            $row[] ='<input type="checkbox" onclick="checkcheckbox()" name="checkbox[]"'.$disable.'value='.$sales['sales_id'].' class="row_check" >';
            $row[] = $sales['sales_date'];
            $info = (!empty($sales['return_bit'])) ? "\n<span class='label label-danger' style='cursor:pointer'><i class='fa fa-fw fa-undo'></i>Return Raised</span>" : '';
            $row[] = $sales['sales_id'].$info;
            $row[] = $sales['sales_status'];
            $row[] = $sales['reference_no'];
            $row[] = $sales['customer_name'];
            $row[] = number_format($sales['grand_total'],2);
            $row[] = number_format($sales['paid_amount'],2);
            $row[] = number_format($sales['sales_due'],2);
            $str='';
            if($sales['payment_status'] ==='Unpaid')
              $str= "<span class='label label-danger' style='cursor:pointer'>Unpaid </span>";
            if($sales['payment_status'] ==='Partial')
              $str="<span class='label label-warning' style='cursor:pointer'> Partial </span>";
            if($sales['payment_status'] ==='Paid')
              $str="<span class='label label-success' style='cursor:pointer'> Paid </span>";

            $row[] = $str;
            $row[] = ucfirst($sales['created_by']);

            $str2 = '<div>
            <a class="drop-down" onclick="dropdown(this)" href="#">Action</a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="View Invoice" href="sales/invoice/'.$sales['sales_id'].'" >
                        <i class="fa fa-fw fa-eye text-blue"></i>View sales
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" href="sales/update/'.$sales['sales_id'].'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';


                $str2.='<li>
                    <a title="Pay" class="pointer" onclick="pay_now('.$sales['sales_id'].')" >
                        <i class="fa fa-fw fa-hourglass-half text-blue"></i>Payment Receive
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" target="_blank" href="sales/print_invoice/'.$sales['sales_id'].'">
                        <i class="fa fa-fw fa-print text-blue"></i>Print
                    </a>
                </li>

                <li>
                    <a style="cursor:pointer" title="Print POS Invoice ?" onclick="print_invoice('.$sales['sales_id'].')">
                        <i class="fa fa-fw fa-file-text text-blue"></i>POS Invoice
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Sales Return" href="salesReturn/add/'.$sales['sales_id'].'">
                        <i class="fa fa-fw fa-undo text-blue"></i>Sales Return
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_sales(\''.$sales['sales_id'].'\')">
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
            'recordsTotal' => $this->salesModel->countAll(),
            'recordsFiltered' => $this->salesModel->countFiltered(),
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

        $result = $this->salesModel->getCustomerSelect($search);

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
	public function returnSalesList(){
        $sales_id = $this->request->param('args')[0];
		echo $this->salesModel->returnSalesList($sales_id);
	}
	public function delete_payment(){
		$payment_id = $this->request->param('args')[0];
		echo $this->salesModel->delete_payment($payment_id);
	}
	// public function show_pay_now_modal(){
	// 	$sales_id=$this->input->post('sales_id');
	// 	echo $this->sales->show_pay_now_modal($sales_id);
	// }
	public function save_payment(){
		echo $this->salesModel->savePayment();
	}
	public function view_payments_modal(){
		$sales_id=$this->request->data('sales_id');
		echo $this->salesModel->view_payments_modal($sales_id);
	}

    public function invoice()
    {
        $sales_id = $this->request->param('args')[0];
        $data = $this->salesModel->invoiceDetails($sales_id);
        $this->view->render(Config::get("VIEWS_PATH").'invoice/sales_invoice_1', $data);
    }

    public function removeSales()
    {        
        $id = "'".$this->request->data('sales_id')."'";
        $result = $this->salesModel->removeSalesFromTable($id);

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
        $result = $this->salesModel->removeSalesFromTable($ids);

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
        $resource = "sales";

        $action_alias = [
            'ajaxList' => 'view',
            'addSales'=>'add',
            'updateSales'=>'edit',
            'removeSales'=>'delete',
            'removeMultipleSales'=>'delete',
            'returnSalesList'=>'add',
            'returnRowWithData'=>'add',
            'customerAjax'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}