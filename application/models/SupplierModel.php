<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class SupplierModel extends Model{

    public $table = 'supplier';

    public $columnOrder= ['supplier_id', 'supplier_name', 'mobile', 'supplier_gstin', 'address', 'city', 'state', 'pincode','purchase_due','purchase_return_due','opening_balance'];

    public $dbColumnOrder= ['id','supplier_id', 'supplier_name', 'mobile', 'supplier_gstin', 'address', 'city', 'state', 'pincode','country','opening_balance'];
    
    public $order = ['supplier_id', 'DESC'];
    
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @param array $fields
     * @return bool
     */
    public function verifyAndSave($fields)
    {
        $validation = new Validation();
        if(!$validation->validate([
            'Supplier Name' => [$fields['supplier_name'], 'required|maxLen(20)'],
            'Supplier ID' => [$fields['supplier_id'], 'required|unique(supplier,supplier_id)'],
            'Mobile'=> [$fields['supplier_mobile'], 'unique(supplier,mobile)'],
        ])){
            $this->error = $validation->errors();
            return false;
        }
        $this->db->beginTransaction();

        $query = "INSERT INTO supplier(supplier_id, supplier_name, mobile, supplier_gstin, address, city, state, pincode, country, opening_balance, created_date,  created_by) 
        VALUES(:supplier_id, :supplier_name , :supplier_mobile , :supplier_GST , :supplier_address , :supplier_city, :supplier_state , :supplier_pincode , :supplier_country , :supplier_balance , :createdDate  , :createdBy )";

        $this->db->prepare($query);
        $this->db->bindValues($fields);
        $this->db->execute();   
        
        $this->db->commit();
        return true;
    }

    // private funtion _getCus

    public function _getDataTableQuery()
    {
        $query = "SELECT ";
        foreach($this->columnOrder as $column){
            $query .= "$column,";
        }
        $query =  substr($query, 0 , -1). ' FROM '.$this->table;

        $start = 0;
        foreach($this->columnOrder as $item){
            
            if(!empty($_POST['search']['value'])){
                if($start == 0){
                    $query .= ' WHERE ( '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }else{
                    $query .= ' OR '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }

                if(count($this->columnOrder) -1 == $start){
                    $query .= ')';
                }
            }
        $start++;

        }

        if(isset($_POST['order'])){
            $query .= ' ORDER BY ' . $this->dbColumnOrder[$_POST['order']['0']['column']]. ' ';
            $query .= ($_POST['order']['0']['dir'] == 'dec') ? 'DESC' : 'ASC' ;
        }else{
            $query .= ' ORDER BY '. $this->order[0] ." ". $this->order[1];
        }
        
        return $query;
    }


    /**
     * For the DataTable 
     */
    public function getDataTable()
    {
        $query = $this->_getDataTableQuery();
        if(isset($_POST['length']) && $_POST['length'] !== -1){
            $query .= ' LIMIT '.$_POST['start'] .','.$_POST['length'];
        }
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();
    
        return $result;
    }
    public function countAll()
    {
        return $this->db->countAll('supplier');
    }

    public function countFiltered()
    {
        $query = $this->_getDataTableQuery();
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->countRows();
    }


    /**
     * Update Supplier on DB
     */
    public function updateSupplierFromTable($fields)
    {
        $query = "SELECT * FROM supplier WHERE supplier_name='".$fields['supplier_name']."' AND supplier_id='".$fields['supplier_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Supplier Name already Exist.';
        }else{
            
            $query = "UPDATE supplier SET supplier_name='".$fields['supplier_name']."', supplier_description='".$fields['supplier_description']."', supplier_id='".$fields['supplier_id']."' WHERE id=".$fields['id'];
            $this->db->prepare($query);
            $this->db->execute();        
        
            return true;
        }
    }

    /**
     * Remove Particular Supplier on DB
     */
    public function removeSupplierFromTable($id)
    {
        $query = "SELECT COUNT(*) AS tot , b.supplier_name From purchase a, supplier b WHERE b.supplier_id=a.supplier_id AND a.supplier_id IN ($id) GROUP BY a.supplier_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $supplier_name[] = $result[$field];
            }
            $list = implode(',', $supplier_name);
            return $list;
        }else{
            $query = "DELETE FROM supplier WHERE supplier_id IN ($id)";
            $this->db->prepare($query);
            $this->db->execute();

            return true;
        }
    }


    /**
     * Get user info from id
     */
    public function get_details($id)
    {
        $query = "SELECT * FROM supplier WHERE supplier_id = '$id'";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['supplier_id'] = $result['supplier_id'];
        $data['supplier_name'] = $result['supplier_name'];
        $data['mobile'] = $result['mobile'];
        $data['supplier_gstin'] = $result['supplier_gstin'];
        $data['address'] = $result['address'];
        $data['city'] = $result['city'];
        $data['state'] = $result['state'];
        $data['country'] = $result['country'];
        $data['pincode'] = $result['pincode'];
        $data['opening_balance'] = $result['opening_balance'];
        
        return $data;   
    
    }

    public function showPayNowModal($supplier_id)
    {
        $purchase_id = '';
        $q1= "SELECT * FROM supplier WHERE supplier_id='$supplier_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAllAssociative()[0];

        $supplier_name = $r1['supplier_name'];
        $supplier_mobile = $r1['mobile'];
        $supplier_gstin = $r1['supplier_gstin'];
        $supplier_address = $r1['address'];
        $supplier_state = $r1['state'];
        $supplier_pincode = $r1['pincode'];
        $supplier_opening_balance = $r1['opening_balance'];
        $supplier_purchase_due = $r1['purchase_due'];

        $purchase_date = '';
        $reference_no = '';
        $purchase_id = '';
        $grand_total = 0;
        $paid_amount = 0;


        $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM sobpayments WHERE supplier_id='$supplier_id'";
        $this->db->prepare($q2);
        $this->db->execute();
        $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
        $supplier_opening_balance_due = $supplier_opening_balance - $sum_of_ob_paid;

        $q3 = "SELECT COALESCE(SUM(grand_total),0) AS total_purchase_amount, COALESCE(SUM(paid_amount),0) AS total_paid_amount FROM purchase WHERE supplier_id='$supplier_id'";
        $this->db->prepare($q3);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();
        $total_purchase_amount = $r2['total_purchase_amount'];
        $total_paid_amount = $r2['total_paid_amount'];
	    $due_amount = number_format($supplier_purchase_due + $supplier_opening_balance_due,2,'.','') ;

        ?>
         <!-- The Modal -->
    <div id="pay_now" class="modal">

<!-- Modal content -->
<div class="modal-content">
    <div class="modal-header">
    <span class="close">&times;</span>
    <h2>Pay Due Payments</h2>
    </div>
    <div class="modal-body">
        <h4>Customer Details</h4><br>
        <div class="modal-row">
            <div class="modal-col" style="width: 200px;">
                <address>
                    <strong><?php echo $supplier_name; ?></strong><br>
                    <?php echo (!empty(trim($supplier_mobile))) ? "Mobile : $supplier_mobile <br>" : '';?>
                    <?php echo (!empty(trim($supplier_address))) ? "Address :$supplier_address <br>$supplier_state<br>" : '';?>
                    <?php echo (!empty(trim($supplier_gstin))) ? "GST NO: ".$supplier_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;">Opening Balance</td>
                        <td style="text-align: right;"><?=number_format($supplier_opening_balance,2);?></td>
                        <td style="text-align: right;">Total Purchase Amount</td>
                        <td style="text-align: right;"><?=number_format($total_purchase_amount,2);?></td>
                    </tr>
                   <tr>
                        <td style="text-align: right;">Opening Balance Due</td>
                        <td style="text-align: right;"><?=number_format($supplier_opening_balance_due,2);?></td>
                        <td style="text-align: right;">Paid Amount</td>
                        <td style="text-align: right;"><?=number_format($paid_amount,2);?></td>
                   </tr> 
                   <tr>
                       <td colspan="2"></td>
                       <td style="text-align: right;">Purchase Due</td>
                       <td style="text-align: right;"><?=number_format($supplier_purchase_due,2);?></td>
                   </tr>
                </table>
            </div>
        </div>
        <br>
        <div >
            <div class="">
		        <input type="hidden" name="payment_row_count" id='payment_row_count' value="1">
                <div class="modal-row" style="justify-content: space-between;">
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                        <label for="payment_date">Date</label>
                        <div class="validate-input" style="width: 90%" data-validate='Date is required'>
                            <input type="date" style="width: 100%;margin:5px" class="req-input" value="<?=date("Y-m-d");?>" id="payment_date" name="payment_date" readonly>
                        </div>
                    </div>
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                        <label for="amount">Amount</label>
                        <div class="validate-input" style="width: 90%" data-validate="Amount is required">
                            <input type="text" style="width: 100%;margin:5px" class="req-input"  id="amount" name="amount" data-due-amt='<?=$due_amount;?>' value="<?=$due_amount?>" onkeyup="calculate_payments()" >
                        </div>
                    </div>
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                    <label for="payment_type">Payment Type</label>
                    <div class="validate-input" style="width: 90%" data-validate="Payment type is required">

                        <select name="payment_type" style="width: 100%;margin:5px" id="payment_type" class="req-input">
                        <?php
                     $q4 = "SELECT * FROM paymenttype";
                     $this->db->prepare($q4);
                     $this->db->execute();
                     $paymenttypeData = $this->db->fetchAllAssociative();
                     foreach($paymenttypeData as $row){
                         echo "<option value='".$row['paymenttype_id']."'>".$row['paymenttype_name']."</option>";
                        }
                        ?>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-col" style="width:100%">
                    <label for="payment_note">Payment Note</label>
                    <textarea type="text" style="width: 100%;margin:5px;height:60px;" id="payment_note" name="payment_note" placeholder="" ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="height:50px">
    <button type="button" id="button" onclick="save_payment('<?=$supplier_id;?>')" style="background-color: #5240a8;float:right">Save</button>
    </div>
</div>

        <?php
    }


    public function savePayment($data)
    {
        extract($data);
        $this->db->beginTransaction();

        $supplier_id = $data['supplier_id'];

        if($data['amount'] == '' || $data['amount'] == 0){$amount = null;}

        if($data['amount'] > 0 && !empty($data['payment_type'])){
            //Get opening balance
            $q1= "SELECT * FROM supplier WHERE supplier_id='$supplier_id'";
            $this->db->prepare($q1);
            $this->db->execute();
            $r1 = $this->db->fetchAllAssociative()[0];

            $supplier_opening_balance = $r1['opening_balance'];
            $supplier_purchase_due = $r1['purchase_due'];

            $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM sobpayments WHERE supplier_id='$supplier_id'";
            $this->db->prepare($q2);
            $this->db->execute();
            $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
            $supplier_opening_balance_due = $supplier_opening_balance - $sum_of_ob_paid;
            

            if($amount>0){

                if($amount<=$supplier_opening_balance_due && $supplier_opening_balance_due>0){
                    $q3 = "INSERT INTO sobpayments(supplier_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :supplier_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                    $this->db->prepare($q3);
                    $this->db->bindValue(':supplier_id', $supplier_id);
                    $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                    $this->db->bindValue(':payment_type', $payment_type);
                    $this->db->bindValue(':payment', $amount);
                    $this->db->bindValue(':payment_note', $payment_note);
                    $this->db->bindValue(':created_date', $created_date);
                    $this->db->bindValue(':created_time', $created_time);
                    $this->db->bindValue(':created_by', $created_by);

                    $this->db->execute();
                    $amount = 0;

                }
	    		if($amount>=$supplier_opening_balance_due && $supplier_opening_balance_due)
                {
                    
                    $q3 = "INSERT INTO sobpayments(supplier_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :supplier_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                    $this->db->prepare($q3);
                    $this->db->bindValue(':supplier_id', $supplier_id);
                    $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                    $this->db->bindValue(':payment_type', $payment_type);
                    $this->db->bindValue(':payment', $supplier_opening_balance_due);
                    $this->db->bindValue(':payment_note', $payment_note);
                    $this->db->bindValue(':created_date', $created_date);
                    $this->db->bindValue(':created_time', $created_time);
                    $this->db->bindValue(':created_by', $created_by);
    
                    $this->db->execute();

                    $amount -= $supplier_opening_balance_due;
                }

                if($amount<=$supplier_purchase_due){
                    $q4 = "SELECT purchase_id, grand_total, paid_amount, COALESCE(grand_total-paid_amount,0) AS purchase_due FROM purchase WHERE grand_total != paid_amount AND supplier_id='$supplier_id'";
                    $this->db->prepare($q4);
                    $this->db->execute();
                    $r4 = $this->db->fetchAllAssociative();
                    foreach($r4 as $res){
                        $grand_total = $res['grand_total'];
                        $paid_amount = $res['paid_amount'];
                        $purchase_due = $res['purchase_due'];
                        $purchase_id = $res['purchase_id'];

                        if($amount<=$purchase_due && $purchase_due>0){
                            $q5 = "INSERT INTO purchasepayments(purchase_id, payment_date, payment_type, payment, payment_note, created_date, created_time, created_by) VALUES( :purchase_id, :payment_date, :payment_type, :payment, :payment_note, :created_date, :created_time, :created_by)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':purchase_id', $purchase_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $amount);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount = 0;

                        }
                        if($amount>=$purchase_due && $purchase_due>0){
                            $q5 = "INSERT INTO purchasepayments(purchase_id, payment_date, payment_type, payment, payment_note, created_date, created_time, created_by) VALUES( :purchase_id, :payment_date, :payment_type, :payment, :payment_note, :created_date, :created_time, :created_by)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':purchase_id', $purchase_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $purchase_due);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount -= $purchase_due;
                        }
                        $this->purchaseModel = new PurchaseModel();
                        $q6 = $this->purchaseModel->updatePurchasePaymentStatus($purchase_id, $supplier_id);
                        if(!$q6){
                            return false;
                        }
                    }
                }
            }
        }else{
            return "Please Enter Valid Amount!";
        }
        $this->db->commit();
        return true;
    }


    public function showPayReturnDueModal($supplier_id)
    {
        $purchase_id = '';
        $q1= "SELECT * FROM supplier WHERE supplier_id='$supplier_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAllAssociative()[0];

        $supplier_name = $r1['supplier_name'];
        $supplier_mobile = $r1['mobile'];
        $supplier_gstin = $r1['supplier_gstin'];
        $supplier_address = $r1['address'];
        $supplier_state = $r1['state'];
        $supplier_pincode = $r1['pincode'];
        $supplier_opening_balance = $r1['opening_balance'];
        $supplier_purchase_return_due = $r1['purchase_return_due'];

        $purchase_date = '';
        $reference_no = '';
        $purchase_id = '';
        $grand_total = 0;
        $paid_amount = 0;


        // $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayment WHERE supplier_id='$supplier_id'";
        // $this->db->prepare($q2);
        // $this->db->execute();
        // $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
        // $supplier_opening_balance_due = $supplier_opening_balance - $sum_of_ob_paid;

        $q3 = "SELECT COALESCE(SUM(grand_total),0) AS total_purchase_amount, COALESCE(SUM(paid_amount),0) AS total_paid_amount FROM purchasereturn WHERE supplier_id='$supplier_id'";
        $this->db->prepare($q3);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();
        $total_purchase_amount = $r2['total_purchase_amount'];
        $total_paid_amount = $r2['total_paid_amount'];
	    $due_amount = number_format($total_purchase_amount - $total_paid_amount,2,'.','') ;

        ?>
         <!-- The Modal -->
    <div id="pay_now" class="modal">

<!-- Modal content -->
<div class="modal-content">
    <div class="modal-header">
    <span class="close">&times;</span>
    <h2>Pay Purchase Return Due Payments</h2>
    </div>
    <div class="modal-body">
        <h4>Customer Details</h4>
        <div class="modal-row">
            <div class="modal-col" style="width: 200px;">
                <address>
                    <strong><?php echo $supplier_name; ?></strong><br>
                    <?php echo (!empty(trim($supplier_mobile))) ? "Mobile : $supplier_mobile <br>" : '';?>
                    <?php echo (!empty(trim($supplier_address))) ? "Email :$supplier_address <br>" : '';?>
                    <?php echo (!empty(trim($supplier_state))) ? "Email :$supplier_state <br>" : '';?>
                    <?php echo (!empty(trim($supplier_gstin))) ? "GST NO: ".$supplier_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;">Total Purchase Amount</td>
                        <td style="text-align: right;"><?=$total_purchase_amount?></td>
                    </tr>
                   <tr>
                         <td style="text-align: right;">Paid Amount</td>
                        <td style="text-align: right;"><?=$total_paid_amount?></td>
                   </tr> 
                   <tr>
                       <td style="text-align: right;">Purchase Due</td>
                       <td style="text-align: right;"><?=$supplier_purchase_return_due?></td>
                   </tr>
                </table>
            </div>
        </div>
        <div >
            <div class="">
		        <input type="hidden" name="payment_row_count" id='payment_row_count' value="1">
                <div class="modal-row" style="justify-content: space-between;">
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                        <label for="payment_date">Date</label>
                        <div class="validate-input" style="width: 90%" data-validate='Date is required'>
                            <input type="date" style="width: 100%;margin:5px" class="req-input" value="<?=date("Y-m-d");?>" id="return_due_payment_date" name="return_due_payment_date" readonly>
                        </div>
                    </div>
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                        <label for="amount">Amount</label>
                        <div class="validate-input" style="width: 90%" data-validate="Amount is required">
                            <input type="text" style="width: 100%;margin:5px" class="req-input" id="return_due_amount" name="return_due_amount" data-due-amt='<?=$due_amount;?>' value="<?=$due_amount?>" onkeyup="calculate_payments()" >
                        </div>
                    </div>
                    <div class="modal-col" style="flex-basis: 120px;flex-grow: 1;">
                    <label for="payment_type">Payment Type</label>
                    <div class="validate-input" style="width: 90%" data-validate="Payment type is required">

                        <select style="width: 100%;margin:5px" name="return_due_payment_type" id="return_due_payment_type" class="req-input">
                        <?php
                     $q4 = "SELECT * FROM paymenttype";
                     $this->db->prepare($q4);
                     $this->db->execute();
                     $paymenttypeData = $this->db->fetchAllAssociative();
                     foreach($paymenttypeData as $row){
                         echo "<option value='".$row['paymenttype_id']."'>".$row['paymenttype_name']."</option>";
                        }
                        ?>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-col" style="width:100%">
                    <label for="payment_note">Payment Note</label>
                    <textarea type="text" style="width: 100%;margin:5px;height:60px;" id="return_due_payment_note" name="return_due_payment_note" placeholder="" ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="height:50px">
    <button type="button"  id="button" onclick="save_return_due_payment('<?=$supplier_id;?>')" style="background-color: #5240a8;float:right">Save</button>
    </div>
</div>

        <?php
    }


    public function saveReturnDuePayment($data)
    {
        extract($data);
        $this->db->beginTransaction();

        $supplier_id = $data['supplier_id'];

        if($data['amount'] == '' || $data['amount'] == 0){$amount = null;}

        if($data['amount'] > 0 && !empty($data['payment_type'])){
            //Get opening balance
            $q1= "SELECT * FROM supplier WHERE supplier_id='$supplier_id'";
            $this->db->prepare($q1);
            $this->db->execute();
            $r1 = $this->db->fetchAllAssociative()[0];

            $supplier_opening_balance = $r1['opening_balance'];
            $supplier_purchase_return_due = $r1['purchase_return_due'];

            // $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayment WHERE supplier_id='$supplier_id'";
            // $this->db->prepare($q2);
            // $this->db->execute();
            // $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
            // $supplier_opening_balance_due = $supplier_opening_balance - $sum_of_ob_paid;
    

            if($amount>0){

                if($amount<=$supplier_purchase_return_due){
                    $q4 = "SELECT purchase_id, grand_total, paid_amount, COALESCE(grand_total-paid_amount,0) AS purchase_due FROM purchasereturn WHERE grand_total != paid_amount AND supplier_id='$supplier_id'";
                    $this->db->prepare($q4);
                    $this->db->execute();
                    $r4 = $this->db->fetchAllAssociative();
                    foreach($r4 as $res){
                        $grand_total = $res['grand_total'];
                        $paid_amount = $res['paid_amount'];
                        $purchase_due = $res['purchase_due'];
                        $return_id = $res['return_id'];

                        if($amount<=$purchase_due && $purchase_due>0){
                            $q5 = "INSERT INTO purchasereturnpayments(return_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :return_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':return_id', $return_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $amount);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount = 0;

                        }
                        if($amount>=$purchase_due && $purchase_due>0){
                            $q5 = "INSERT INTO purchasereturnpayments(return_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :return_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':return_id', $return_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $purchase_due);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount -= $purchase_due;
                        }
                        $this->purchaseModel = new PurchaseReturnModel();
                        $q6 = $this->purchaseReturnModel->updatePurchasePaymentStatus($purchase_id, $supplier_id);
                        if(!$q6){
                            return false;
                        }
                    }
                }
            }
        }else{
            return "Please Enter Valid Amount!";
        }
        $this->db->commit();
        return true;
    }
    /**
     * Generate new Supplier id
     */
    public function createSupplierID()
    {
        $this->db->prepare("SELECT supplier_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['supplier_prefix'];
        
        //Create suppliers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM supplier";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $supplier_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $supplier_id;
     
    }
}