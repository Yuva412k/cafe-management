<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Purchase extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','removeMultiplePurchase','returnPurchaseList','supplierAjax','returnRowWithData','addPurchase', 'updatePurchase', 'removePurchase'];
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
                $this->Security->config('form',['fields'=>['purchase_id']]);    
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
                
        }
        $this->loadModel('purchaseModel');
        $this->loadModel('unitModel');
        $this->loadModel('taxModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'purchase/add');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchase/list");
    }

    public function add()
    {
        //todo
        //purchase id auto generate
        $paymentData = ['payment_type'=>'cash','length'=>1];
       Config::setJsConfig('curPage', 'purchase/add');
       $purchaseid = $this->purchaseModel->createPurchaseId();
        $taxData = $this->taxModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchase/add", ['taxData'=>$taxData,'purchase_id'=>$purchaseid, "paymentData"=>$paymentData]);
    }

    public function addPurchase()
    {
        $purchaseFields = ['purchase_date','purchase_id','round_off' ,'reference_no','sub_total' ,'grand_total','payment_type', 'payment_status','amount','payment_note', 'created_by', 'supplier_id', 'paid_amount', 'purchase_status', 'purchase_due','other_charges_input','other_charges_amt', 'other_charges_type', 'discount_on_all_input','discount_on_all_type', 'discount_on_all_amt', 'tax_id','tax_amt_cgst','tax_amt_sgst', 'hidden_rowcount'];
        $created_by = Session::getUserRole();
        $created_date = date('Y-j-d');
        $purchase_time = date('h:m:s');
        $fields = [];
        foreach($purchaseFields as $field){
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

        $fields = array_merge($fields, ['created_date'=>$created_date,'created_by'=>$created_by, 'purchase_time'=>$purchase_time,'created_time'=>$purchase_time]);
        $result = $this->purchaseModel->verifyAndSave($fields, $command);

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
            Session::setFlashData('success', 'purchase data is successfully stored');
            echo "success&&".$fields['purchase_id'];
        }
    }

    public function update()
    {
        $purchase_id = $this->request->param('args')[0];
        $paymentData = ['payment_type'=>'cash','length'=>1];
        $purchaseData = $this->purchaseModel->get_details($purchase_id);
        $taxData = $this->taxModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "purchase/add", ['id'=>$purchaseData['id'],'taxData'=>$taxData, 'purchaseData'=>$purchaseData, 'paymentData'=>$paymentData]);
    }

    public function ajaxList()
    {
        // //todo get date range for purchase 

        $result = $this->purchaseModel->getDataTable();

        //remove the keys from the datatable result
        $data = [];

        foreach($result as $purchase){

            $row = array();

            $disable = ($purchase['id'] === 1) ? 'disabled' : '';
            
            $row[] ='<input type="checkbox" onclick="checkcheckbox()" name="checkbox[]"'.$disable.'value='.$purchase['purchase_id'].' class="row_check" >';
            $row[] = $purchase['purchase_date'];
            $info = (!empty($purchase['return_bit'])) ? "\n<span class='label label-danger' style='cursor:pointer'><i class='fa fa-fw fa-undo'></i>Return Raised</span>" : '';
            $row[] = $purchase['purchase_id'].$info;
            $row[] = $purchase['purchase_status'];
            $row[] = $purchase['reference_no'];
            $row[] = $purchase['supplier_name'];
            $row[] = number_format($purchase['grand_total'],2);
            $row[] = number_format($purchase['paid_amount'],2);
            $row[] = number_format($purchase['purchase_due'],2);
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
                    <a title="View Invoice" href="purchase/invoice/'.$purchase['purchase_id'].'" >
                        <i class="fa fa-fw fa-eye text-blue"></i>View purchase
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" href="purchase/update/'.$purchase['purchase_id'].'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>
                </li>';


                $str2.='<li>
                    <a title="Pay" class="pointer" onclick="pay_now('.$purchase['purchase_id'].')" >
                        <i class="fa fa-fw fa-hourglass-half text-blue"></i>Payment Receive
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Update Record ?" target="_blank" href="purchase/print_invoice/'.$purchase['purchase_id'].'">
                        <i class="fa fa-fw fa-print text-blue"></i>Print
                    </a>
                </li>

                <li>
                    <a style="cursor:pointer" title="Print POS Invoice ?" onclick="print_invoice('.$purchase['purchase_id'].')">
                        <i class="fa fa-fw fa-file-text text-blue"></i>POS Invoice
                    </a>
                </li>';

                $str2.='<li>
                    <a title="Purchase Return" href="purchaseReturn/add/'.$purchase['purchase_id'].'">
                        <i class="fa fa-fw fa-undo text-blue"></i>Purchase Return
                    </a>
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_purchase(\''.$purchase['purchase_id'].'\')">
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
            'recordsTotal' => $this->purchaseModel->countAll(),
            'recordsFiltered' => $this->purchaseModel->countFiltered(),
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

        $result = $this->purchaseModel->getSupplierSelect($search);

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
		echo $this->purchaseModel->getItemsInfo($rowcount,$item_id);
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
		echo $this->purchaseModel->savePayment();
	}
	public function view_payments_modal(){
		$purchase_id=$this->request->data('purchase_id');
		echo $this->purchaseModel->view_payments_modal($purchase_id);
	}

    public function invoice()
    {
        $purchase_id = $this->request->param('args')[0];
        $data = $this->purchaseModel->invoiceDetails($purchase_id);
        $this->view->render(Config::get("VIEWS_PATH").'invoice/purchase_invoice_1', $data);
    }

    public function removePurchase()
    {        
        $id = "'".$this->request->data('purchase_id')."'";
        $result = $this->purchaseModel->removePurchaseFromTable($id);

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
        $result = $this->purchaseModel->removePurchaseFromTable($ids);

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
        $resource = "purchase";

        $action_alias = [
            'ajaxList' => 'view',
            'addPurchase'=>'add',
            'updatePurchase'=>'edit',
            'removePurchase'=>'delete',
            'removeMultiplePurchase'=>'delete',
            'returnPurchaseList'=>'add',
            'returnRowWithData'=>'add',
            'supplierAjax'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}