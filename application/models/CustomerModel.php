<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class CustomerModel extends Model{

    public $table = 'customer';

    public $columnOrder= ['customer_id', 'customer_name', 'mobile', 'customer_gstin', 'address', 'city', 'state', 'pincode','sales_due','sales_return_due','opening_balance'];

    public $dbColumnOrder= ['id','customer_id', 'customer_name', 'mobile', 'customer_gstin', 'address', 'city', 'state', 'pincode','country','opening_balance'];
    
    public $order = ['customer_id', 'DESC'];
    
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
            'Customer Name' => [$fields['cust_name'], 'required|maxLen(20)'],
            'Customer ID' => [$fields['cust_id'], 'required|unique(customer,customer_id)'],
            'Mobile'=> [$fields['cust_mobile'], 'unique(customer,mobile)'],
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO customer(customer_id, customer_name, mobile, customer_gstin, address, city, state, pincode, country, opening_balance, created_date,  created_by) 
        VALUES(:cust_id, :cust_name , :cust_mobile , :cust_GST , :cust_address , :cust_city, :cust_state , :cust_pincode , :cust_country , :cust_balance , :createdDate  , :createdBy )";

        $this->db->prepare($query);
        $this->db->bindValues($fields);
        $this->db->execute();   
        
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
            $query .= ($_POST['order']['0']['dir'] == 'desc') ? 'DESC' : 'ASC' ;
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
        return $this->db->countAll('customer');
    }

    public function countFiltered()
    {
        $query = $this->_getDataTableQuery();
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->countRows();
    }

    /**
     * Get user info from id
     */
    public function get_details($id)
    {
        $query = "SELECT * FROM customer WHERE customer_id = '$id'";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['customer_id'] = $result['customer_id'];
        $data['customer_name'] = $result['customer_name'];
        $data['mobile'] = $result['mobile'];
        $data['customer_gstin'] = $result['customer_gstin'];
        $data['address'] = $result['address'];
        $data['city'] = $result['city'];
        $data['state'] = $result['state'];
        $data['country'] = $result['country'];
        $data['pincode'] = $result['pincode'];
        $data['opening_balance'] = $result['opening_balance'];
        
        return $data;   
    
    }

    /**
     * Update Category on DB
     */
    public function updateCustomerFromTable($fields)
    {
        $list = [ "cust_name" , "cust_mobile" , "cust_GST" ,"cust_address" , "cust_city", "cust_state" , "cust_pincode" , "cust_country" , "cust_balance"];
        $query = "SELECT * FROM customer WHERE customer_name='".$fields['cust_name']."' AND customer_id='".$fields['cust_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Customer Name already Exist.';
        }else{

            $query = "UPDATE customer SET customer_name=:cust_name, mobile=:cust_mobile, customer_gstin=:cust_GST, address=:cust_address, city=:cust_city, state=:cust_state, pincode=:cust_pincode, country=:cust_country, opening_balance=:cust_balance WHERE customer_id=:cust_id";
            $this->db->prepare($query);
            $this->db->bindValue(':cust_id',$fields['cust_id']);
            foreach($list as $column){
                $this->db->bindValue(":$column", $fields[$column]);
            }
            $this->db->execute();        
        
            return true;
        }
    }

    /**
     * Remove Particular Category on DB
     */
    public function removeCustomerFromTable($id)
    {
        if((strpos($id, '\'CU0001\'') == true) || $id == "'CU0001'"){
            return "Sorry! This Record Restricted! Can't Delete";
        }


        $query = "SELECT COUNT(*) AS tot , b.customer_name From sales a, customer b WHERE b.customer_id=a.customer_id AND a.customer_id IN ($id) GROUP BY a.customer_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();
        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $customer_name[] = $result[$field];
            }
            $list = implode(',', $customer_name);
            return $list;
        }else{


            $q1 = "DELETE FROM cobpayments WHERE customer_id<>'CU0001' AND customer_id IN ($id)";
            $this->db->prepare($q1);
            $this->db->execute();

            $query = "DELETE FROM customer WHERE customer_id IN ($id) AND customer_id<>'CU001'";
            $this->db->prepare($query);
            $this->db->execute();

            return true;
        }
    }

    public function showPayNowModal($customer_id)
    {
        $sales_id = '';
        $q1= "SELECT * FROM customer WHERE customer_id='$customer_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAllAssociative()[0];

        $customer_name = $r1['customer_name'];
        $customer_mobile = $r1['mobile'];
        $customer_gstin = $r1['customer_gstin'];
        $customer_address = $r1['address'];
        $customer_state = $r1['state'];
        $customer_pincode = $r1['pincode'];
        $customer_opening_balance = $r1['opening_balance'];
        $customer_sales_due = $r1['sales_due'];

        $sales_date = '';
        $reference_no = '';
        $sales_id = '';
        $grand_total = 0;
        $paid_amount = 0;


        $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayments WHERE customer_id='$customer_id'";
        $this->db->prepare($q2);
        $this->db->execute();
        $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
        $customer_opening_balance_due = $customer_opening_balance - $sum_of_ob_paid;

        $q3 = "SELECT COALESCE(SUM(grand_total),0) AS total_sales_amount, COALESCE(SUM(paid_amount),0) AS total_paid_amount FROM sales WHERE customer_id='$customer_id'";
        $this->db->prepare($q3);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();
        $total_sales_amount = $r2['total_sales_amount'];
        $total_paid_amount = $r2['total_paid_amount'];
	    $due_amount = number_format($customer_sales_due + $customer_opening_balance_due,2,'.','') ;

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
                    <strong><?php echo $customer_name; ?></strong><br>
                    <?php echo (!empty(trim($customer_mobile))) ? "Mobile : $customer_mobile <br>" : '';?>
                    <?php echo (!empty(trim($customer_address))) ? "Email :$customer_address <br>" : '';?>
                    <?php echo (!empty(trim($customer_state))) ? "Email :$customer_state <br>" : '';?>
                    <?php echo (!empty(trim($customer_gstin))) ? "GST NO: ".$customer_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;">Opening Balance</td>
                        <td style="text-align: right;"><?=number_format($customer_opening_balance,2);?></td>
                        <td style="text-align: right;">Total Sales Amount</td>
                        <td style="text-align: right;"><?=number_format($total_sales_amount,2);?></td>
                    </tr>
                   <tr>
                        <td style="text-align: right;">Opening Balance Due</td>
                        <td style="text-align: right;"><?=number_format($customer_opening_balance_due,2);?></td>
                        <td style="text-align: right;">Paid Amount</td>
                        <td style="text-align: right;"><?=number_format($paid_amount,2);?></td>
                   </tr> 
                   <tr>
                       <td colspan="2"></td>
                       <td style="text-align: right;">Sales Due</td>
                       <td style="text-align: right;"><?=number_format($customer_sales_due,2);?></td>
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
    <button type="button" id="button" onclick="save_payment('<?=$customer_id;?>')" style="background-color: #5240a8;float:right">Save</button>
    </div>
</div>

        <?php
    }


    public function savePayment($data)
    {
        extract($data);
        $this->db->beginTransaction();

        $customer_id = $data['customer_id'];

        if($data['amount'] == '' || $data['amount'] == 0){$amount = null;}

        if($data['amount'] > 0 && !empty($data['payment_type'])){
            //Get opening balance
            $q1= "SELECT * FROM customer WHERE customer_id='$customer_id'";
            $this->db->prepare($q1);
            $this->db->execute();
            $r1 = $this->db->fetchAllAssociative()[0];

            $customer_opening_balance = $r1['opening_balance'];
            $customer_sales_due = $r1['sales_due'];

            $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayments WHERE customer_id='$customer_id'";
            $this->db->prepare($q2);
            $this->db->execute();
            $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
            $customer_opening_balance_due = $customer_opening_balance - $sum_of_ob_paid;
            

            if($amount>0){

                if($amount<=$customer_opening_balance_due && $customer_opening_balance_due>0){
                    $q3 = "INSERT INTO cobpayments(customer_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :customer_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                    $this->db->prepare($q3);
                    $this->db->bindValue(':customer_id', $customer_id);
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
	    		if($amount>=$customer_opening_balance_due && $customer_opening_balance_due)
                {
                    
                    $q3 = "INSERT INTO cobpayments(customer_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :customer_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                    $this->db->prepare($q3);
                    $this->db->bindValue(':customer_id', $customer_id);
                    $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                    $this->db->bindValue(':payment_type', $payment_type);
                    $this->db->bindValue(':payment', $customer_opening_balance_due);
                    $this->db->bindValue(':payment_note', $payment_note);
                    $this->db->bindValue(':created_date', $created_date);
                    $this->db->bindValue(':created_time', $created_time);
                    $this->db->bindValue(':created_by', $created_by);
    
                    $this->db->execute();

                    $amount -= $customer_opening_balance_due;
                }

                if($amount<=$customer_sales_due){
                    $q4 = "SELECT sales_id, grand_total, paid_amount, COALESCE(grand_total-paid_amount,0) AS sales_due FROM sales WHERE grand_total != paid_amount AND customer_id='$customer_id'";
                    $this->db->prepare($q4);
                    $this->db->execute();
                    $r4 = $this->db->fetchAllAssociative();
                    foreach($r4 as $res){
                        $grand_total = $res['grand_total'];
                        $paid_amount = $res['paid_amount'];
                        $sales_due = $res['sales_due'];
                        $sales_id = $res['sales_id'];

                        if($amount<=$sales_due && $sales_due>0){
                            $q5 = "INSERT INTO salespayments(sales_id, payment_date, payment_type, payment, payment_note, created_date, created_time, created_by) VALUES( :sales_id, :payment_date, :payment_type, :payment, :payment_note, :created_date, :created_time, :created_by)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':sales_id', $sales_id);
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
                        if($amount>=$sales_due && $sales_due>0){
                            $q5 = "INSERT INTO salespayments(sales_id, payment_date, payment_type, payment, payment_note, created_date, created_time, created_by) VALUES( :sales_id, :payment_date, :payment_type, :payment, :payment_note, :created_date, :created_time, :created_by)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':sales_id', $sales_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $sales_due);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount -= $sales_due;
                        }
                        $this->salesModel = new SalesModel();
                        $q6 = $this->salesModel->updateSalesPaymentStatus($sales_id, $customer_id);
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


    public function showPayReturnDueModal($customer_id)
    {
        $sales_id = '';
        $q1= "SELECT * FROM customer WHERE customer_id='$customer_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAllAssociative()[0];

        $customer_name = $r1['customer_name'];
        $customer_mobile = $r1['mobile'];
        $customer_gstin = $r1['customer_gstin'];
        $customer_address = $r1['address'];
        $customer_state = $r1['state'];
        $customer_pincode = $r1['pincode'];
        $customer_opening_balance = $r1['opening_balance'];
        $customer_sales_return_due = $r1['sales_return_due'];

        $sales_date = '';
        $reference_no = '';
        $sales_id = '';
        $grand_total = 0;
        $paid_amount = 0;


        // $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayment WHERE customer_id='$customer_id'";
        // $this->db->prepare($q2);
        // $this->db->execute();
        // $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
        // $customer_opening_balance_due = $customer_opening_balance - $sum_of_ob_paid;

        $q3 = "SELECT COALESCE(SUM(grand_total),0) AS total_sales_amount, COALESCE(SUM(paid_amount),0) AS total_paid_amount FROM salesreturn WHERE customer_id='$customer_id'";
        $this->db->prepare($q3);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();
        $total_sales_amount = $r2['total_sales_amount'];
        $total_paid_amount = $r2['total_paid_amount'];
	    $due_amount = number_format($total_sales_amount - $total_paid_amount,2,'.','') ;

        ?>
         <!-- The Modal -->
    <div id="pay_now" class="modal">

<!-- Modal content -->
<div class="modal-content">
    <div class="modal-header">
    <span class="close">&times;</span>
    <h2>Pay Sales Return Due Payments</h2>
    </div>
    <div class="modal-body">
        <h4>Customer Details</h4>
        <div class="modal-row">
            <div class="modal-col" style="width: 200px;">
                <address>
                    <strong><?php echo $customer_name; ?></strong><br>
                    <?php echo (!empty(trim($customer_mobile))) ? "Mobile : $customer_mobile <br>" : '';?>
                    <?php echo (!empty(trim($customer_address))) ? "Email :$customer_address <br>" : '';?>
                    <?php echo (!empty(trim($customer_state))) ? "Email :$customer_state <br>" : '';?>
                    <?php echo (!empty(trim($customer_gstin))) ? "GST NO: ".$customer_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;">Total Sales Amount</td>
                        <td style="text-align: right;"><?=number_format($total_sales_amount,2)?></td>
                    </tr>
                   <tr>
                         <td style="text-align: right;">Paid Amount</td>
                        <td style="text-align: right;"><?=number_format($total_paid_amount,2)?></td>
                   </tr> 
                   <tr>
                       <td style="text-align: right;">Sales Due</td>
                       <td style="text-align: right;"><?=number_format($customer_sales_return_due,2)?></td>
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
    <button type="button"  id="button" onclick="save_return_due_payment('<?=$customer_id;?>')" style="background-color: #5240a8;float:right">Save</button>
    </div>
</div>

        <?php
    }


    public function saveReturnDuePayment($data)
    {
        extract($data);
        $this->db->beginTransaction();

        $customer_id = $data['customer_id'];

        if($data['amount'] == '' || $data['amount'] == 0){$amount = null;}

        if($data['amount'] > 0 && !empty($data['payment_type'])){
            //Get opening balance
            $q1= "SELECT * FROM customer WHERE customer_id='$customer_id'";
            $this->db->prepare($q1);
            $this->db->execute();
            $r1 = $this->db->fetchAllAssociative()[0];

            $customer_opening_balance = $r1['opening_balance'];
            $customer_sales_return_due = $r1['sales_return_due'];

            // $q2 = "SELECT COALESCE(SUM(payment),0) AS sum_of_ob_paid FROM cobpayment WHERE customer_id='$customer_id'";
            // $this->db->prepare($q2);
            // $this->db->execute();
            // $sum_of_ob_paid = $this->db->fetchAssociative()['sum_of_ob_paid'];
            // $customer_opening_balance_due = $customer_opening_balance - $sum_of_ob_paid;
    

            if($amount>0){

                if($amount<=$customer_sales_return_due){
                    $q4 = "SELECT sales_id, grand_total, paid_amount, COALESCE(grand_total-paid_amount,0) AS sales_due FROM salesreturn WHERE grand_total != paid_amount AND customer_id='$customer_id'";
                    $this->db->prepare($q4);
                    $this->db->execute();
                    $r4 = $this->db->fetchAllAssociative();
                    foreach($r4 as $res){
                        $grand_total = $res['grand_total'];
                        $paid_amount = $res['paid_amount'];
                        $sales_due = $res['sales_due'];
                        $return_id = $res['return_id'];

                        if($amount<=$sales_due && $sales_due>0){
                            $q5 = "INSERT INTO salesreturnpayments(return_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :return_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
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
                        if($amount>=$sales_due && $sales_due>0){
                            $q5 = "INSERT INTO salesreturnpayments(return_id, payment_date, payment, payment_note, created_date, created_by, created_time) VALUES( :return_id, :payment_date, :payment, :payment_note, :created_date, :created_by, :created_time)";
                            $this->db->prepare($q5);
                            $this->db->bindValue(':return_id', $return_id);
                            $this->db->bindValue(':payment_date', date('Y-m-d', strtotime($payment_date)));
                            $this->db->bindValue(':payment_type', $payment_type);
                            $this->db->bindValue(':payment', $sales_due);
                            $this->db->bindValue(':payment_note', $payment_note);
                            $this->db->bindValue(':created_date', $created_date);
                            $this->db->bindValue(':created_time', $created_time);
                            $this->db->bindValue(':created_by', $created_by);
                            $this->db->execute();
                            $amount -= $sales_due;
                        }
                        $this->salesModel = new SalesReturnModel();
                        $q6 = $this->salesReturnModel->updateSalesPaymentStatus($sales_id, $customer_id);
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
     * Generate new Customer id
     */
    public function createCustomerID()
    {
        $this->db->prepare("SELECT customer_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['customer_prefix'];

        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM customer";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $customer_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $customer_id;
     
    }
}