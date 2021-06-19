<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class SalesReturnModel extends Model
{

    public $table = 'salesreturn';

    public $columnOrder = ['return_date', 'sales_id','return_id', 'reference_no', 'grand_total', 'payment_status', 'created_by', 'customer_id', 'paid_amount', 'return_status'];

    public $columnOrderNew = ['sales_id','return_id' ,'return_date', 'reference_no', 'customer_id', 'return_status', 'other_charges_input', 'other_charges_amt', 'other_charges_type', 'discount_on_all_input', 'discount_on_all_type', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off','grand_total', 'created_by','created_time'];

    public $dbColumnOrder = ['a.id', 'a.return_date', 'a.sales_id', 'a.return_id','a.reference_no', 'a.grand_total', 'a.customer_id', 'a.created_by', 'a.paid_amount',  'a.return_status', 'b.customer_name', 'a.reference_no', 'a.payment_status'];

    public $order = ['id', 'DESC'];

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
    }
    /**
     * @param array $fields
     * @return bool
     */
    public function verifyAndSave($fields, $command = 'save')
    {
        $validation = new Validation();
        if (!$validation->validate([
            'Return ID' => [$fields['return_id'], 'required'],
            'Return Date' => [$fields['return_date'], 'required'],
            'Customer ID' => [$fields['customer_id'], 'required'],
            'Sub Total' => [$fields['sub_total'], 'required'],
            'Grand Total' => [$fields['grand_total'], 'required'],
            'Status' => [$fields['return_status'], 'required'],

        ])) {
            $this->error = $validation->errors();
            return $this->error;
        }

        if ($command == 'save') {
            if (!$validation->validate([
                'Sales ID' => [$fields['sales_id'], 'required'],
            ])) {
                $this->error = $validation->errors();
                return $this->error;
            }

            $this->db->prepare('SELECT sales_status FROM sales WHERE sales_id="'.$fields['sales_id'].'"');
            $this->db->execute();
            $sales_stat_check =  $this->db->fetchAssociative();
            
            if(lcfirst($sales_stat_check['sales_status']) == 'quotation'){
                return "Sorry! Quotation could not be returned";
            }
            
            $this->db->prepare('SELECT id FROM salesreturn WHERE sales_id="'.$fields['sales_id'].'"');
            $this->db->execute();
            if($this->db->countRows() > 0){
                return "redirect";
            }
        }

        $amount = $fields['amount'];
        $payment_type = $fields['payment_type'];
        $payment_note = $fields['payment_note'];
        unset($fields['amount']);

        $rowcount = $fields['hidden_rowcount'];
        unset($fields['hidden_rowcount']);

        if ($command == 'save' || $command=='create') {

            $query = "INSERT INTO salesreturn(";
            foreach ($this->columnOrderNew as $column) {
                $query .= $column . ',';
            }
            $query = substr($query, 0, -1) . ') VALUES( ';
            foreach ($this->columnOrderNew as $column) {
                $query .= ':' . $column . ',';
            }
            $query = substr($query, 0, -1) . ')';

            $this->db->prepare($query);
            foreach ($this->columnOrderNew as $column) {
                $this->db->bindValue(":$column", $fields[$column]);
            }
            $result = $this->db->execute();

            if (!$result) {
                return false;
            }
        } else if ($command == 'update') {
             $columnOrder = ['sales_id' ,'return_date', 'reference_no', 'customer_id', 'return_status', 'other_charges_input', 'other_charges_amt', 'other_charges_type', 'discount_on_all_input', 'discount_on_all_type', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off','grand_total'];
            $query = "UPDATE salesreturn SET ";

            foreach ($columnOrder as $column) {
                $query .= "$column =:$column,";
            }
            $query = substr($query, 0, -1) . " WHERE return_id =:return_id";

            $this->db->prepare($query);
            $this->db->bindValue(':return_id', $fields['return_id']);
            foreach ($columnOrder as $column) {
                $this->db->bindValue(":$column", $fields[$column]);
            }
            $result = $this->db->execute();

            $this->db->prepare("DELETE FROM salesreturnitems WHERE return_id=:return_id");
            $this->db->bindValue(':return_id', $fields['return_id']);
            $result1 = $this->db->execute();

            if (!$result1) {
                return false;
            }
        }

        //Getting post data from Form
        for ($i = 1; $i <= $rowcount; $i++) {

            if (isset($_REQUEST['tr_item_id_' . $i]) && !empty($_REQUEST['tr_item_id_' . $i])) {
                $item_id = $_REQUEST['tr_item_id_' . $i];
                $return_qty = (float)$_REQUEST['td_data_qty_' . $i];
                $per_unit_price = $_REQUEST['td_data_per_unit_price_' . $i];
                $unit_total_cost = $_REQUEST['td_data_tot_unit_cost_' . $i];
                $total_cost =  $_REQUEST['td_data_total_cost_' . $i];
                $discount_type = $_REQUEST['item_discount_type_' . $i];
                $discount_input = (float)$_REQUEST['td_data_discount_input_' . $i];
                $discount_amt = (float)$_REQUEST['td_data_discount_amt_' . $i]; //Amount
                
                $discount_amt_per_unit = $discount_amt / $return_qty;

                $single_unit_total_cost = $per_unit_price;

                $single_unit_total_cost -= $discount_amt_per_unit;

                if ($discount_input == '' || $discount_input === 0) {
                    $discount_input = null;
                }
                if ($total_cost == '' || $total_cost === 0) {
                    $total_cost = null;
                }

                $return_id = $fields['return_id'];
                $sales_id = $fields['sales_id'];
                $return_status = $fields['return_status'];
                $query = "INSERT INTO salesreturnitems SET sales_id=:sales_id, return_id='$return_id', item_id='$item_id', return_qty='$return_qty', return_status='$return_status',price_per_unit='$per_unit_price', discount_type='$discount_type', discount_amt='$discount_amt', discount_input='$discount_input', unit_total_cost='$unit_total_cost', total_cost='$total_cost'";
                $this->db->prepare($query);
                $this->db->bindValue(':sales_id', $sales_id);
                $this->db->execute();

                $result = $this->itemModel->updateItemsQuantity($item_id);
                if (!$result) {
                    return false;
                }
            }
        }

        $sales_id = $fields['sales_id'];
        $return_id = $fields['return_id'];
        $return_date = $fields['return_date'];
        $created_date = $fields['created_date'];
        $created_time = $fields['created_time'];
        $created_by = $fields['created_by'];

        if ($amount == '' || $amount == 0) {
            $amount = null;
        }
        if ($amount > 0 && !empty($payment_type)) {
            $query = "INSERT INTO salesreturnpayments SET sales_id=:sales_id, return_id='$return_id',payment_date='$return_date', payment='$amount' ,payment_note='$payment_note', payment_type='$payment_type', created_date='$created_date', created_time='$created_time', created_by='$created_by'";
            $this->db->prepare($query);
            $this->db->bindValue(':sales_id', $sales_id);
            $result = $this->db->execute();
            if (!$result) {
                return false;
            }
        }

        //update the return bit in sales table
        if(isset($sales_id) && !empty($sales_id)){
            $this->db->prepare("UPDATE sales SET return_bit='1' WHERE sales_id='$sales_id'");
            $this->db->execute();
        }

        $updateSalesPaymentStatus = $this->updateSalesPaymentStatus($return_id, $fields['customer_id']);
        if (!$updateSalesPaymentStatus) {
            return false;
        }
        return true;
    }


    public function _getDataTableQuery()
    {
        $query = "SELECT ";
        foreach ($this->dbColumnOrder as $column) {
            $query .= "$column,";
        }
        $query .= ' COALESCE(a.grand_total, 0)-COALESCE(a.paid_amount, 0) as return_due FROM ' . $this->table;
        $query .= ' as a, customer as b WHERE b.customer_id=a.customer_id';

        $start = 0;
        foreach ($this->columnOrder as $item) {

            if (!empty($_POST['search']['value'])) {
                if ($start === 0) {
                    $query .= ' OR ( ' . $item . " LIKE '%" . $_POST['search']['value'] . "%'";
                } else {
                    $query .= ' OR ' . $item . " LIKE '%" . $_POST['search']['value'] . "%'";
                }

                if (count($this->columnOrder) - 1 === $start) {
                    $query .= ')';
                }
            }
            $start++;
        }

        if (isset($_POST['order'])) {
            if($_POST['order']['0']['column']== 7){
            $query .= ' ORDER BY return_due ';
            }else{
                $query .= ' ORDER BY ' . $this->dbColumnOrder[$_POST['order']['0']['column']] . ' ';
            }
            $query .= ($_POST['order']['0']['dir'] === 'desc') ? 'DESC' : 'ASC';
        } else {
            $query .= ' ORDER BY ' . $this->order[0] . " " . $this->order[1];
        }
        return $query;
    }


    /**
     * For the DataTable 
     */
    public function getDataTable()
    {
        $query = $this->_getDataTableQuery();
        if (isset($_POST['length']) && $_POST['length'] !== -1) {
            $query .= ' LIMIT ' . $_POST['start'] . ',' . $_POST['length'];
        }
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        return $result;
    }
    public function countAll()
    {
        return $this->db->countAll($this->table);
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
        $query = "SELECT a.*, b.customer_name FROM salesreturn AS a, customer AS b WHERE a.return_id = '$id' AND a.customer_id=b.customer_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        if (empty($result)) {
            return null;
        }
        $data = [];
        $data['id'] = $result['id'];
        $data['sales_id'] = $result['sales_id'];
        $data['return_id'] = $result['return_id'];
        $data['return_date'] = $result['return_date'];
        $data['return_status'] = $result['return_status'];
        $data['reference_no'] = $result['reference_no'];
        $data['customer_id'] = $result['customer_id'];
        $data['customer_name'] = $result['customer_name'];
        $data['discount_on_all_input'] = number_format($result['discount_on_all_input'], 2);
        $data['discount_on_all_type'] = $result['discount_on_all_type'];
        $data['discount_on_all_amt'] = number_format($result['discount_on_all_amt'], 2);
        $data['other_charges_input'] = number_format($result['other_charges_input'], 2);
        $data['other_charges_type'] = $result['other_charges_type'];
        $data['other_charges_amt'] = number_format($result['other_charges_amt'], 2);
        $data['tax_id'] = $result['tax_id'];
        $data['tax_amt_cgst'] = number_format($result['tax_amt_cgst'], 2);
        $data['tax_amt_sgst'] = number_format($result['tax_amt_sgst'], 2);
        $data['round_off'] = number_format($result['round_off'], 2);
        $data['grand_total'] = number_format($result['grand_total'], 2);
        $data['sub_total'] = number_format($result['sub_total'], 2);


        $query1 = "SELECT COUNT(*) AS count_items FROM salesreturnitems WHERE return_id='$id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $count_items = $this->db->fetchAssociative()['count_items'];

        return array_merge($data, ['items_count' => $count_items]);
    }

    public function getItemCount($sales_id)
    {
        $this->db->prepare("SELECT COUNT(*) AS items_count FROM salesitems WHERE sales_id='$sales_id'");
        $this->db->execute();
        $result = $this->db->fetchAssociative()['items_count'];
        return $result;
    }

    /**
     * Find Item details
     */
    public function find_item_details($id)
    {
        $json_array = array();
        $query = "SELECT item_id,minimum_qty,unit_id,sales_price,stock from items where item_id=$id";
        $this->db->prepare($query);
        $this->db->execute();
        if ($this->db->countRows() > 0) {
            $res = $this->db->fetchAllAssociative();
            foreach ($res as $value) {
                $json_array[] = [
                    'id_item' => $value['item_id'],
                    'minimum_qty' => $value['minimum_qty'],
                    'unit_id' => $value['unit_id'],
                    'sales_price' => $value['sales_price'],
                    'stock' => $value['stock'],
                ];
            }
        }
        return json_encode($json_array);
    }

    /**
     * For the Select Category options
     */
    public function getCustomerSelect($search = '')
    {
        $query = "SELECT customer_id, customer_name FROM customer";
        if (!empty($search)) {
            $query .= " WHERE customer_name LIKE '%$search%'";
        }
        $query .= " LIMIT 5";

        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        return $result;
    }
    /**
     * Search Item
     */
    public function searchItem($q)
    {
        $json_array = array();
        $query1 = "SELECT item_id, item_name FROM items WHERE item_name LIKE '%$q%' or item_id LIKE '%$q%'";

        $this->db->prepare($query1);
        $this->db->execute();

        if ($this->db->countRows() > 0) {
            $result = $this->db->fetchAllAssociative();
            foreach ($result as $value) {
                $json_array[] = ['id' => $value['item_id'], 'text' => $value['item_name']];
            }
        }
        return json_encode($json_array);
    }


    /**
     * Return sales List
     * for purchase items list retrieve
     */
    public function returnSalesList($id)
    {
        $query = "SELECT * FROM salesreturnitems WHERE return_id='$id'";
        $this->db->prepare($query);
        $this->db->execute();
        $sales = $this->db->fetchAllAssociative();

        $rowcount = 1;
        $info = array();
        foreach ($sales as $s1) {

            $this->db->prepare('SELECT * FROM items WHERE item_id="' . $s1['item_id'] . '"');
            $this->db->execute();
            $result = $this->db->fetchAssociative();
            $info['item_id'] = $s1['item_id'];
            $info['item_name'] = $result['item_name'];
            $info['item_available_qty'] = $result['stock_qty'];
            $info['item_sales_price'] = $s1['price_per_unit'];
            $info['purchase_price'] = $result['purchase_price'];
            $info['sales_qty'] = $s1['return_qty'];
            $info['item_discount'] = $s1['discount_input'];
            $info['item_discount_type'] = $s1['discount_type'];
            $info['item_discount_input'] = $s1['discount_input'];

            $arr = $this->returnRowWithData($rowcount++, $info);
        }
        return $arr;
    }

    public function returnRowWithData($rowcount, $info)
    {

        extract($info);
        $item_amount = ($item_sales_price * $sales_qty);

?>
        <tr id="row_<?= $rowcount ?>" data-row='<?= $rowcount ?>'>

            <!--Item Name -->
            <td id="td_<?= $rowcount ?>_1" width="50%">
                <input type="text" placeholder="Type or click to select an item." value="<?= $item_name ?>" id="td_data_<?= $rowcount ?>_1">
            </td>
            <!-- Quantity -->
            <td id="td_qty_<?= $rowcount ?>">
                <input type="text" onkeyup="calculateQty(<?= $rowcount ?>)" name="td_data_qty_<?= $rowcount ?>" id="td_data_qty_<?= $rowcount ?>" value="<?= $sales_qty ?>">
            </td>

            <!-- Unit Cost -->
            <td id="td_tot_unit_cost_<?= $rowcount ?>_3">
                <input type="item_rate" onkeyup="calculate_amount(<?= $rowcount; ?>)" id="td_data_tot_unit_cost_<?= $rowcount ?>" name="td_data_tot_unit_cost_<?= $rowcount ?>" value="<?= $item_sales_price; ?>">
            </td>

            <!-- Discount  -->
            <td id="td_discount_amt_<?= $rowcount ?>">
                <div style="display: flex;">
                    <input style="margin: 0;width: 80%" class="total-disc" type="text" name="td_data_discount_input_<?= $rowcount ?>" id="td_data_discount_input_<?= $rowcount ?>" onkeyup="calculate_amount(<?= $rowcount; ?>)" value="<?= $item_discount_input ?>">
                    <select name="item_discount_type_<?= $rowcount ?>" id="item_discount_type_<?= $rowcount ?>">
                        <?php
                        $selectper = '';
                        $selectrup = '';
                        if ($item_discount_type == 'percentage') {
                            $selectper = 'selected';
                        }
                        if ($item_discount_type == 'rupee') {
                            $selectrup = 'selected';
                        }
                        ?>
                        <option <?= $selectper ?> value="percentage">%</option>
                        <option <?= $selectrup ?> value="rupees">Rs.</option>
                    </select>
                </div>
            </td>

            <!-- Amount -->
            <td id="td_total_cost_<?= $rowcount ?>">
                <input type="text" id="td_data_total_cost_<?= $rowcount ?>" name="td_data_total_cost_<?= $rowcount ?>" value="<?= $item_amount; ?>" readonly>
            </td>

            <!-- Remove Button -->
            <td id="td_btn_<?= $rowcount ?>">
                <a id="td_data_<?= $rowcount ?>_16" name="td_data_<?= $rowcount ?>_16">-</a>
            </td>

            <input type="hidden" id="td_data_per_unit_price_<?= $rowcount; ?>" name="td_data_per_unit_price_<?= $rowcount; ?>" value="<?= $item_sales_price; ?>">
            <input type="hidden" id="tr_available_qty_<?= $rowcount; ?>_13" value="<?= $item_available_qty; ?>">
            <input type="hidden" id="tr_item_id_<?= $rowcount; ?>" name="tr_item_id_<?= $rowcount; ?>" value="<?= $item_id; ?>">
            <input type="hidden" id="td_data_discount_amt_<?= $rowcount; ?>" name="td_data_discount_amt_<?= $rowcount; ?>" value="<?= $item_id; ?>">

        </tr>
<?php
    }

    function updateSalesPaymentBySalesId($return_id, $customer_id)
    {
        $query1 = "SELECT COALESCE(SUM(payment), 0) AS payment FROM salesreturnpayments WHERE return_id='$return_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $sum_of_payments = $this->db->fetchAssociative()['payment'];

        $query2 = "SELECT COALESCE(SUM(grand_total), 0) AS total FROM salesreturn WHERE return_id='$return_id'";
        $this->db->prepare($query2);
        $this->db->execute();
        $payble_total = $this->db->fetchAssociative()['total'];

        $payment_status = '';
        if ($payble_total == $sum_of_payments) {
            $payment_status = 'Paid';
        } else if ($sum_of_payments != 0 && ($sum_of_payments < $payble_total)) {
            $payment_status = 'Partial';
        } else if ($sum_of_payments == 0) {
            $payment_status = 'Unpaid';
        }


        //Condition if sales record not exist
        //Sometime called after sales record delete
        $query3 = "UPDATE salesreturn SET payment_status='$payment_status', paid_amount='$sum_of_payments' WHERE return_id='$return_id'";
        $this->db->prepare($query3);
        $this->db->execute();
        $query4 = "UPDATE customer SET sales_return_due=(SELECT COALESCE(SUM(grand_total),0)-COALESCE(SUM(paid_amount),0) FROM salesreturn WHERE customer_id='$customer_id') WHERE customer_id='$customer_id'";
        $this->db->prepare($query4);
        $res = $this->db->execute();

        if (!$res) {
            return false;
        }

        return true;
    }

    public function updateSalesPaymentStatus($return_id, $customer_id)
    {
        if (!$this->updateSalesPaymentBySalesId($return_id, $customer_id)) {
            return false;
        }
        return true;
    }

    public function updateStatus($id, $status)
    {
        $this->db->prepare("UPDATE salesreturn SET status='$status' WHERE return_id='$id'");
        $res = $this->db->execute();
        if(!$res){
            return false;
        }
        return true;
    }

    public function updateReturnBit()
    {
        $this->db->prepare("SELECT COUNT(*) AS tot_sales_ids, sales_id FROM salesreturn GROUP BY sales_id");
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        $this->db->prepare("UPDATE sales SET return_bit=NULL");
        $this->db->execute();

        foreach($result as $res){
            if(!empty($res['sales_id'])){
                $this->db->prepare('UPDATE sales SET return_bit="1" WHERE sales_id="'.$res['$sales_id']).'"';
                $this->db->execute();
            }
        }
    }

    /**
     * Save Payment
     */
    public function savePayment($fields)
    {
        extract($fields);

        if ($amount == '' || $amount == 0) {
            $amount = null;
        }
        if ($amount > 0 && !empty($payment_type)) {
            $salespayments_entry = array(
                'return_id'         => $return_id,
                'payment_date'        => date("Y-m-d", strtotime($payment_date)), //Current Payment with sales entry
                'payment_type'         => $payment_type,
                'payment'             => $amount,
                'payment_note'         => $payment_note,
                'created_date'         => $created_date,
                'created_time'         => $created_time,
                'created_by'         => $created_by,
            );


            $query = "INSERT INTO salesreturnpayments SET";
            foreach ($salespayments_entry as $key => $value) {
                $query .= " $key='$value',";
            }
            $query = substr($query, 0, -1);
            $q3 = $this->db->prepare($query);
            $this->db->execute();
        } else {
            return "Please Enter Valid Amount!";
        }
        $query1 = "SELECT customer_id FROM salesreturn WHERE return_id='$return_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $customer_id = $this->db->fetchAssociative()['customer_id'];

        $query2 = $this->updateSalesPaymentStatus($return_id, $customer_id);
        if (!$query2) {
            return false;
        }
        return true;
    }

    public function invoiceDetails($sales_id)
    {
        $sales_info_query = "SELECT a.customer_name, a.mobile, a.customer_gstin, a.address, a.city, a.state, a.country, b.return_date, b.sales_id, b.created_time, b.reference_no, b.return_status, b.tax_amt_cgst, b.tax_amt_sgst ,COALESCE(b.grand_total,0) AS grand_total, COALESCE(b.sub_total, 0) AS sub_total, COALESCE(b.paid_amount, 0) AS paid_amount,COALESCE(b.other_charges_input, 0) AS other_charges_input, b.other_charges_type, COALESCE(b.other_charges_amt,0) AS other_charges_amt, COALESCE(b.discount_on_all_input,0) AS discount_on_all_input, COALESCE(b.discount_on_all_amt,0) AS discount_on_all_amt,b.discount_on_all_type, COALESCE(b.round_off,0) AS round_off, c.tax, b.payment_status FROM customer AS a, salesreturn AS b, tax as c WHERE a.`customer_id`=b.`customer_id` AND b.`return_id`='$sales_id' AND b.`tax_id`=c.`tax_id`";
        $this->db->prepare($sales_info_query);
        $this->db->execute();
        $sales_info = $this->db->fetchAllAssociative();

        $shop_details_query = "SELECT * from shopdetails where id=1";
        $this->db->prepare($shop_details_query);
        $this->db->execute();
        $shop_details = $this->db->fetchAssociative();

        $sales_items_query = "SELECT b.item_name, a.return_qty, a.price_per_unit, a.discount_input, a.discount_amt, a.unit_total_cost, a.total_cost FROM salesreturnitems AS a, items AS b WHERE b.`item_id`=a.`item_id` AND a.return_id='$sales_id'";
        $this->db->prepare($sales_items_query);
        $this->db->execute();
        $sales_items = $this->db->fetchAllAssociative();

        return ['sales_info' => $sales_info, 'shop_details' => $shop_details, 'sales_items' => $sales_items];
    }

    public function removeSalesReturnFromTable($ids)
    {
        //Find the customer id 
        $query = "SELECT customer_id, return_id FROM salesreturn WHERE return_id IN ($ids)";

        $this->db->prepare($query);
        $this->db->execute();
        $cust_result = $this->db->fetchAllAssociative();

        $this->db->prepare("DELETE FROM salesreturnpayments WHERE return_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM salesreturnitems WHERE return_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM salesreturn WHERE return_id IN ($ids)");
        $this->db->execute();

        //update item qty in stockentry 
        $this->db->prepare("SELECT item_id from items");
        $this->db->execute();

        if ($this->db->countRows() > 0) {
            $q1 = $this->db->fetchAllAssociative();
            foreach ($q1 as $res) {
                $temp = $this->itemModel->updateItemsQuantity($res['item_id']);
                if (!$temp) {
                    return false;
                }
            }
        }

        $this->updateReturnBit();

        foreach ($cust_result as $res) {
            $temp = $this->updateSalesPaymentStatus($res['return_id'], $res['customer_id']);
            if (!$temp) {
                return false;
            }
        }
        return true;
    }

    public function deletePayments($payment_id)
    {
        $query = "SELECT return_id FROM salesreturnpayments WHERE id=$payment_id";
        $this->db->prepare($query);
        $this->db->execute();
        $return_id = $this->db->fetchAssociative()['return_id'];
    
        $query1= "DELETE FROM salesreturnpayments WHERE id='$payment_id'";
        $this->db->prepare($query1);
        $res1 = $this->db->execute();

        $query2 = "SELECT customer_id FROM salesreturn WHERE return_id=$return_id";
        $this->db->prepare($query2);
        $this->db->execute();
        $customer_id = $this->db->fetchAssociative()['customer_id'];

        $res2 = $this->updateSalesPaymentStatus($return_id, $customer_id);

        if ($res1 && $res2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate new Customer id
     */
    public function createSalesReturnID()
    {
        //TODO PREFIX FOR CUSTOMER FROM SETTING OR SOMETHING
        $prefix = 'RT';
        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM salesreturn";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        $sales_id = $prefix . str_pad($result['maxid'], 4, '0', STR_PAD_LEFT);

        return $sales_id;
    }
}
