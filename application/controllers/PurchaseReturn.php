<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class PurchaseReturn extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','removeMultiplePurchase','returnPurchaseList','returnPurchaseReturnList','supplierAjax','returnRowWithData','addPurchase', 'updatePurchase', 'removePurchase'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addPurchase': 
                $this->Security->config('validateForm',false);    
                break;
            case 'updatePurchase': 
                $this->Security->config('validateForm',false);    
                break;
            case 'removePurchase':
                $this->Security->config('form',['fields'=>['return_id']]);    
                break;
            case 'removeMultiplePurchase':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;
            case 'supplierAjax':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnRowWithData':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnPurchaseList':
                $this->Security->config('validateForm',false);    
                break;
            case 'returnPurchaseReturnList':
                $this->Security->config('validateForm',false);    
                break;    
        }
        $this->loadModel('purchaseReturnModel');
        $this->loadModel('purchaseModel');
        $this->loadModel('unitModel');
        $this->loadModel('taxModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'purchaseReturn/list');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchasereturn/list");
    }

    public function create()
    {
        //todo
        //purchase id auto generate
        $paymentData = ['payment_type'=>'cash','length'=>1];
       Config::setJsConfig('curPage', 'purchaseReturn/add');
       $purchaseid = $this->purchaseReturnModel->createPurchaseReturnId();
        $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchasereturn/add", ['taxData'=>$taxData,'return_id'=>$purchaseid,'request_name'=>'create' ,"paymentData"=>$paymentData]);
    }

    public function add()
    {
        //todo
        //purchase id auto generate
        $paymentData = ['payment_type'=>'cash','length'=>1];
       $purchase_id = $this->request->param('args')[0];
       Config::setJsConfig('curPage', 'purchaseReturn/add');
       $purchaseData = $this->purchaseModel->get_details($purchase_id);
       $return_id = $this->purchaseReturnModel->createPurchaseReturnId();
       $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchasereturn/add", ['taxData'=>$taxData, 'request_name'=>'add','return_id'=>$return_id, 'returnData'=>$purchaseData, 'purchase_id'=>$purchase_id,"paymentData"=>$paymentData]);
    }
    public function addPurchase()
    {
        $purchaseFields = ['return_date','purchase_id','return_id','round_off' ,'reference_no','sub_total' ,'grand_total','payment_type', 'payment_status','amount','payment_note', 'supplier_id', 'paid_amount', 'return_status', 'return_due','other_charges_input','other_charges_amt', 'other_charges_type', 'discount_on_all_input','discount_on_all_type', 'discount_on_all_amt', 'tax_id','tax_amt_cgst','tax_amt_sgst', 'hidden_rowcount'];
        $created_by = Session::getUserRole();
        $created_date = date('Y-j-d');
        $purchase_time = date('h:m:s');
        $fields = [];
        foreach($purchaseFields as $field){
            if($field=='hidden_rowcount'){
                $fields[$field] = empty($this->request->data($field)) || $this->request->data($field) == 0 ? null:  (int)$this->request->data($field) ;
                continue;
            }
            if($field=='tax_amt_cgst' || $field == 'tax_amt_sgst' ){
                $fields[$field] = empty($this->request->data($field)) || $this->request->data($field) == 0 ? null:  (float)$this->request->data($field) ;
                continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        if(empty($fields['purchase_id'])){
            $fields['purchase_id'] = null;
        }
        if(empty($fields['round_off'])){
            $fields['round_off'] = null;
        }
        if(empty($fields['other_charges_input'])){
            $fields['other_charges_input'] = null;
        }
        if(empty($fields['discount_on_all_input'])){
            $fields['discount_on_all_input'] = null;
        }
        $command = $this->request->param('args')[0];

        $fields = array_merge($fields, ['created_date'=>$created_date,'created_by'=>$created_by,'created_time'=>$purchase_time]);
        $result = $this->purchaseReturnModel->verifyAndSave($fields, $command);

       if(is_string($result)){
            if($result == "redirect"){
                Session::setFlashData('warning', 'Purchase Return Invoice Already Generated!');
                $this->redirector->to(PUBLIC_ROOT."/purchaseReturn/update/".$fields['return_id']);
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
            Session::setFlashData('success', 'purchase return data is successfully stored');
            echo "success&&".$fields['return_id'];
        }
    }

    public function update()
    {
        $id = $this->request->param('args')[0];
        $paymentData = ['payment_type'=>'cash','length'=>1];
        $purchaseData = $this->purchaseReturnModel->get_details($id);
        $taxData = $this->taxModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchasereturn/add", ['id'=>$purchaseData['id'],'taxData'=>$taxData, 'returnData'=>$purchaseData,'request_name'=>'update', 'paymentData'=>$paymentData]);
    }

    public function ajaxList()
    {
        // //todo get date range for purchase 

        $result = $this->purchaseReturnModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $purchase){

            $row = array();

            $disable = ($purchase['id'] === 1) ? 'disabled' : '';
            
            $row[] ='<input type="checkbox" onclick="checkcheckbox()" name="checkbox[]"'.$disable.'value='.$purchase['return_id'].' class="row_check" >';
            $row[] = $purchase['return_date'];
            $row[] = $purchase['purchase_id'];
            $row[] = $purchase['return_id'];
            $row[] = $purchase['return_status'];
            $row[] = $purchase['reference_no'];
            $row[] = $purchase['supplier_name'];
            $row[] = number_format($purchase['grand_total'],2);
            $row[] = number_format($purchase['paid_amount'],2);
            $row[] = number_format($purchase['return_due'],2);
            $str='';
            if($purchase['payment_status'] ==='Unpaid')
              $str= "<span class='label label-danger' style='cursor:pointer'>Unpaid </span>";
            if($purchase['payment_status'] ==='Partial')
              $str="<span class='label label-warning' style='cursor:pointer'> Partial </span>";
            if($purchase['payment_status'] ==='Paid')
              $str="<span class='label label-success' style='cursor:pointer'> Paid </span>";

            $row[] = $str;
            $row[] = ucfirst($purchase['created_by']);

            $str2 = '<div>
            <a class="drop-down" onclick="dropdown(this)" href="#">Action</a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="View Invoice" href="purchaseReturn/invoice/'.$purchase['return_id'].'" >
                        <i class="fa fa-fw fa-eye text-blue"></i>View purchase
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" href="purchaseReturn/update/'.$purchase['return_id'].'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';


                $str2.='<li>
                    <a title="Pay" class="pointer" onclick="pay_now('.$purchase['return_id'].')" >
                        <i class="fa fa-fw fa-hourglass-half text-blue"></i>Payment Receive
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" target="_blank" href="purchaseReturn/print_invoice/'.$purchase['return_id'].'">
                        <i class="fa fa-fw fa-print text-blue"></i>Print
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_purchase(\''.$purchase['return_id'].'\')">
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
            'recordsTotal' => $this->purchaseReturnModel->countAll(),
            'recordsFiltered' => $this->purchaseReturnModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }


    /**
     * Supplier ajaxList for select 2 search option
     */
    public function supplierAjax()
    {

        $search = $this->request->data('searchTerm');

        $result = $this->purchaseReturnModel->getSupplierSelect($search);

        if(!empty($result)){
            $data = array();

            foreach($result as $name){

                $row= array();
                $row['id'] =$name['supplier_id'];
                $row['text'] = $name['supplier_name'];
                
                $data[] =$row;
            }

        echo json_encode($data);

        }
    }

	//Table ajax code
	public function search_item(){
		$q=$this->request->data('q');
		$result=$this->purchaseModel->searchItem($q);
		echo $result;
	}
	public function find_item_details(){
		$id=$this->request->data('id');
		
		$result=$this->purchaseModel->findItemDetails($id);
		echo $result;
	}

    
    public function returnRowWithData(){
        $rowcount = $this->request->data('rowcount');
        $item_id = $this->request->data('item_id');
		echo $this->purchaseReturnModel->getItemsInfo($rowcount,$item_id);
	}
	public function returnPurchaseReturnList(){
        $return_id = $this->request->param('args')[0];
		echo $this->purchaseReturnModel->returnPurchaseList($return_id);
	}

    public function returnPurchaseList(){
        $purchase_id = $this->request->param('args')[0];
		echo $this->purchaseModel->returnPurchaseList($purchase_id);
	}

	public function delete_payment(){
		$payment_id = $this->request->param('args')[0];
		echo $this->purchaseModel->delete_payment($payment_id);
	}
	// public function show_pay_now_modal(){
	// 	$purchase_id=$this->input->post('purchase_id');
	// 	echo $this->purchase->show_pay_now_modal($purchase_id);
	// }
	public function save_payment(){
		echo $this->purchaseReturnModel->savePayment();
	}
	public function view_payments_modal(){
		$purchase_id=$this->request->data('return_id');
		echo $this->purchaseReturnModel->view_payments_modal($purchase_id);
	}

    public function invoice()
    {
        $purchase_id = $this->request->param('args')[0];
        $data = $this->purchaseReturnModel->invoiceDetails($purchase_id);
        $this->view->render(Config::get("VIEWS_PATH").'invoice/purchase_invoice_1', $data);
    }

    public function removePurchase()
    {        
        $id = "'".$this->request->data('return_id')."'";
        $result = $this->purchaseReturnModel->removePurchaseReturnFromTable($id);

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
     * Remove Multiple Purchase
     */
    public function removeMultiplePurchase()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->purchaseReturnModel->removePurchaseFromTable($ids);

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
        $resource = "purchase_return";

        $action_alias = [
            'ajaxList' => 'view',
            'addPurchase'=>'add',
            'updatePurchase'=>'edit',
            'removePurchase'=>'delete',
            'removeMultiplePurchase'=>'delete',
            'returnPurchaseList'=>'add',
            'returnPurchaseReturnList'=>'edit',
            'returnRowWithData'=>'add',
            'supplierAjax'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}