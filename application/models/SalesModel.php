<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class SalesModel extends Model
{

    public $table = 'sales';

    public $columnOrder = ['sales_date', 'sales_id', 'reference_no', 'grand_total', 'payment_status', 'created_by', 'customer_id', 'paid_amount', 'sales_status'];

    public $columnOrderNew = ['sales_id', 'sales_date', 'reference_no', 'grand_total', 'created_by', 'customer_id', 'sales_status', 'sales_time', 'other_charges_input', 'other_charges_amt', 'other_charges_type', 'discount_on_all_input', 'discount_on_all_type', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off'];

    public $dbColumnOrder = ['a.id', 'a.return_bit', 'a.sales_id', 'a.sales_date', 'a.sales_time', 'a.grand_total', 'a.customer_id', 'a.created_by', 'a.paid_amount',  'a.sales_status', 'b.customer_name', 'a.reference_no', 'a.payment_status'];

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
            'Sales Date' => [$fields['sales_date'], 'required'],
            'Customer ID' => [$fields['customer_id'], 'required'],
            'Sub Total' => [$fields['sub_total'], 'required'],
            'Grand Total' => [$fields['grand_total'], 'required'],
            'Status' => [$fields['sales_status'], 'required'],

        ])) {
            $this->error = $validation->errors();
            return $this->error;
        }
        if ($command == 'save') {
            if (!$validation->validate([
                'Sales ID' => [$fields['sales_id'], 'required|unique(sales, sales_id)'],
            ])) {
                $this->error = $validation->errors();
                return $this->error;
            }
        }
        $this->db->beginTransaction();
        $amount = $fields['amount'];
        $payment_type = $fields['payment_type'];
        $payment_note = $fields['payment_note'];
        unset($fields['amount']);

        $rowcount = $fields['hidden_rowcount'];
        unset($fields['hidden_rowcount']);

        if ($command == 'save') {

            $query = "INSERT INTO sales(";
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
            $columnOrder = ['reference_no', 'sales_date', 'sales_status', 'customer_id', 'other_charges_input', 'other_charges_amt', 'discount_on_all_type', 'discount_on_all_input', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off', 'grand_total'];
            $query = "UPDATE sales SET ";

            foreach ($columnOrder as $column) {
                $query .= "$column =:$column,";
            }
            $query = substr($query, 0, -1) . " WHERE sales_id =:sales_id";

            $this->db->prepare($query);
            $this->db->bindValue(':sales_id', $fields['sales_id']);
            foreach ($columnOrder as $column) {
                $this->db->bindValue(":$column", $fields[$column]);
            }
            $result = $this->db->execute();

            $this->db->prepare("DELETE FROM salesitems WHERE sales_id=:sales_id");
            $this->db->bindValue(':sales_id', $fields['sales_id']);
            $result1 = $this->db->execute();

            if (!$result1) {
                return false;
            }
        }

        //Getting post data from Form
        for ($i = 1; $i <= $rowcount; $i++) {

            if (isset($_REQUEST['tr_item_id_' . $i]) && !empty($_REQUEST['tr_item_id_' . $i])) {
                $item_id = $_REQUEST['tr_item_id_' . $i];
                $sales_qty = $_REQUEST['td_data_qty_' . $i];
                $per_unit_price = (float)   $_REQUEST['td_data_per_unit_price_' . $i];
                $unit_total_cost =(float) $_REQUEST['td_data_tot_unit_cost_' . $i];
                $total_cost = (float)$_REQUEST['td_data_total_cost_' . $i];
                $discount_type = $_REQUEST['item_discount_type_' . $i];
                $discount_input = (float)$_REQUEST['td_data_discount_input_' . $i];
                $discount_amt = (float)$_REQUEST['td_data_discount_amt_' . $i]; //Amount
                $discount_amt_per_unit = $discount_amt / $sales_qty;

                $single_unit_total_cost = $per_unit_price;

                $single_unit_total_cost -= $discount_amt_per_unit;

                if ($discount_input == '' || $discount_input == 0) {
                    $discount_input = null;
                }
                if ($total_cost == '' || $total_cost == 0) {
                    $total_cost = null;
                }

                $sales_status = $fields['sales_status'];
                $sales_id = $fields['sales_id'];
                $query = "INSERT INTO salesitems SET sales_id='$sales_id', sales_status='$sales_status', item_id='$item_id', sales_qty='$sales_qty', price_per_unit='$per_unit_price', discount_type='$discount_type', discount_amt='$discount_amt', discount_input=:discount_input, unit_total_cost='$unit_total_cost', total_cost='$total_cost'";
                $this->db->prepare($query);
                $this->db->bindValue(':discount_input', $discount_input);
                $this->db->execute();

                $result = $this->itemModel->updateItemsQuantity($item_id);
                if (!$result) {
                    return false;
                }
            }
        }

        $sales_id = $fields['sales_id'];
        $sales_date = $fields['sales_date'];
        $created_date = $fields['created_date'];
        $created_time = $fields['created_time'];
        $created_by = $fields['created_by'];

        if ($amount == '' || $amount == 0) {
            $amount = null;
        }
        if ($amount > 0 && !empty($payment_type)) {
            $query = "INSERT INTO salespayments SET sales_id='$sales_id', payment_date='$sales_date', payment='$amount' ,payment_note='$payment_note', payment_type='$payment_type', created_date='$created_date', created_time='$created_time', created_by='$created_by'";
            $this->db->prepare($query);
            $result = $this->db->execute();
            if (!$result) {
                return false;
            }
        }

        $updateSalesPaymentStatus = $this->updateSalesPaymentStatus($sales_id, $fields['customer_id']);
        if (!$updateSalesPaymentStatus) {
            return false;
        }
        $this->db->commit();
        return true;
    }


    public function _getDataTableQuery()
    {
        $query = "SELECT ";
        foreach ($this->dbColumnOrder as $column) {
            $query .= "$column,";
        }
        $query .= ' COALESCE(a.grand_total, 0)-COALESCE(a.paid_amount, 0) as sales_due FROM ' . $this->table;
        $query .= ' as a, customer as b WHERE b.customer_id=a.customer_id';

        $start = 0;
        foreach ($this->dbColumnOrder as $item) {

            if (!empty($_POST['search']['value'])) {
                if ($start == 0) {
                    $query .= ' OR ( ' . $item . " LIKE '%" . $_POST['search']['value'] . "%'";
                } else {
                    $query .= ' OR ' . $item . " LIKE '%" . $_POST['search']['value'] . "%'";
                }

                if (count($this->dbColumnOrder) - 1 == $start) {
                    $query .= ')';
                }
            }
            $start++;
        }

        if (isset($_POST['order'])) {
            if($_POST['order']['0']['column']== 7){
            $query .= ' ORDER BY sales_due ';
            }else{
                $query .= ' ORDER BY ' . $this->dbColumnOrder[$_POST['order']['0']['column']] . ' ';
            }
            $query .= ($_POST['order']['0']['dir'] == 'desc') ? 'DESC' : 'ASC';
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
        $query = "SELECT a.*, b.customer_name FROM sales AS a, customer AS b WHERE a.sales_id = '$id' AND a.customer_id=b.customer_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        if (empty($result)) {
            return null;
        }
        $data = [];
        $data['id'] = $result['id'];
        $data['sales_id'] = $result['sales_id'];
        $data['sales_date'] = $result['sales_date'];
        $data['sales_status'] = $result['sales_status'];
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


        $query1 = "SELECT COUNT(*) AS count_items FROM salesitems WHERE sales_id='$id'";
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
     * Get Items info
     */
    public function getItemsInfo($rowcount, $item_id)
    {
        $query = "SELECT * FROM items WHERE id=:id";
        $this->db->prepare($query);
        $this->db->bindValue(':id', $item_id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        $info = array();
        $info['item_id'] = $result['item_id'];
        $info['item_name'] = $result['item_name'];
        $info['item_available_qty'] = $result['stock_qty'];
        $info['item_sales_price'] = $result['sales_price'];
        $info['purchase_price'] = $result['purchase_price'];
        $info['sales_qty'] = 1;
        $info['item_discount'] = 0;
        $info['item_discount_type'] = 'precentage';
        $info['item_discount_input'] = 0;

        $this->returnRowWithData($rowcount, $info);
    }

    /**
     * Return sales List
     * for purchase items list retrieve
     */
    public function returnSalesList($sales_id)
    {
        $query = "SELECT * FROM salesitems WHERE sales_id='$sales_id'";
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
            $info['sales_qty'] = $s1['sales_qty'];
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
            <td id="td_<?= $rowcount ?>_1" width="40%">
                <input type="text" style="text-align: left;" value="<?= $item_name ?>" id="td_data_<?= $rowcount ?>_1">
            </td>
            <!-- Quantity -->
            <td id="td_qty_<?= $rowcount ?>">
                <input type="number" onchange="calculateQty(<?= $rowcount ?>)" style="text-align: left;" name="td_data_qty_<?= $rowcount ?>" id="td_data_qty_<?= $rowcount ?>" value="<?= $sales_qty ?>">
            </td>

            <!-- Unit Cost -->
            <td id="td_tot_unit_cost_<?= $rowcount ?>_3">
                <input type="text" class="number" onkeyup="calculate_amount(<?= $rowcount; ?>)" id="td_data_tot_unit_cost_<?= $rowcount ?>" name="td_data_tot_unit_cost_<?= $rowcount ?>" value="<?= $item_sales_price; ?>">
            </td>

            <!-- Discount  -->
            <td id="td_discount_amt_<?= $rowcount ?>">
                <div style="display: flex;">
                    <input style="margin: 0;width: 80%" class="total-disc" type="text" name="td_data_discount_input_<?= $rowcount ?>" id="td_data_discount_input_<?= $rowcount ?>" onkeyup="calculate_amount(<?= $rowcount; ?>)" value="<?= $item_discount_input ?>">
                    <select name="item_discount_type_<?= $rowcount ?>" class="select" id="item_discount_type_<?= $rowcount ?>">
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
                <a id="td_data_<?= $rowcount ?>_16" name="td_data_<?= $rowcount ?>_16" onclick="removerow(<?=$rowcount?>)" style="background-color:crimson;padding:5px;border: 1px solid red;cursor:pointer"><i class="fas fa-minus"></i></a>
            </td>

            <input type="hidden" id="td_data_per_unit_price_<?= $rowcount; ?>" name="td_data_per_unit_price_<?= $rowcount; ?>" value="<?= $item_sales_price; ?>">
            <input type="hidden" id="tr_available_qty_<?= $rowcount; ?>_13" value="<?= $item_available_qty; ?>">
            <input type="hidden" id="tr_item_id_<?= $rowcount; ?>" name="tr_item_id_<?= $rowcount; ?>" value="<?= $item_id; ?>">
            <input type="hidden" id="td_data_discount_amt_<?= $rowcount; ?>" name="td_data_discount_amt_<?= $rowcount; ?>" value="<?= $item_id; ?>">

        </tr>
<?php
    }

    // public function record_customer_payment($customer_id=null){
    //     $customer_id_str = '';
    //     if(empty($customer_id)){
    //         $this->db->prepare("DELETE FROM customerpayments");
    //         $this->db->execute();
    //     }else{
    //         $this->db->prepare("DELETE FROM customerpayments WHERE customer_id='$customer_id'");
    //         $this->db->execute();
    //         $customer_id_str = "AND b.customer_id='$customer_id'";
    //     }

    //     $q1 = "INSERT INTO customerpayments (salespayment_id,customer_id,payment_date,payment_type, 
    //     payment,payment_note,
    //     created_date,
    //     created_time,created_by ) 
    //     SELECT a.id,b.customer_id,a.payment_date,a.payment_type, 
    //          COALESCE(SUM(a.payment)),a.payment_note,
    //          a.created_date,a.created_time,a.created_by FROM salespayments AS a, sales AS b WHERE b.sales_id=a.sales_id $customer_id_str GROUP BY b.customer_id,a.payment_type,a.payment_date,a.created_time,a.created_date";
    //     $this->db->prepare($q1);
    //     $r1 = $this->db->execute();
    //     if(!$r1){
    //         return false;
    //     }

    //     return true;

    // }


    public function updateSalesPaymentBySalesId($sales_id, $customer_id)
    {
        $query1 = "SELECT COALESCE(SUM(payment), 0) AS payment FROM salespayments WHERE sales_id='$sales_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $sum_of_payments = $this->db->fetchAssociative()['payment'];

        $query2 = "SELECT COALESCE(SUM(grand_total), 0) AS total FROM sales WHERE sales_id='$sales_id'";
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
        $query3 = "UPDATE sales SET payment_status='$payment_status', paid_amount='$sum_of_payments' WHERE sales_id='$sales_id'";
        $this->db->prepare($query3);
        $this->db->execute();
        $query4 = "UPDATE customer SET sales_due=(SELECT COALESCE(SUM(grand_total),0)-COALESCE(SUM(paid_amount),0) FROM sales WHERE customer_id='$customer_id' AND sales_status='final') WHERE customer_id='$customer_id'";
        $this->db->prepare($query4);
        $res = $this->db->execute();

        if (!$res) {
            return false;
        }

        // if(!$this->record_customer_payment($customer_id)){
		// 	return false;
		// }

        return true;
    }

    public function updateSalesPaymentStatus($sales_id, $customer_id)
    {
        if (!$this->updateSalesPaymentBySalesId($sales_id, $customer_id)) {
            return false;
        }
        return true;
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
                'sales_id'         => $sales_id,
                'payment_date'        => date("Y-m-d", strtotime($payment_date)), //Current Payment with sales entry
                'payment_type'         => $payment_type,
                'payment'             => $amount,
                'payment_note'         => $payment_note,
                'created_date'         => $created_date,
                'created_time'         => $created_time,
                'created_by'         => $created_by,
            );


            $query = "INSERT INTO salespayments SET";
            foreach ($salespayments_entry as $key => $value) {
                $query .= " $key='$value',";
            }
            $query = substr($query, 0, -1);
            $q3 = $this->db->prepare($query);
            $this->db->execute();
        } else {
            return "Please Enter Valid Amount!";
        }
        $query1 = "SELECT customer_id FROM sales WHERE sales_id='$sales_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $customer_id = $this->db->fetchAssociative()['customer_id'];

        $query2 = $this->updateSalesPaymentStatus($sales_id, $customer_id);
        if (!$query2) {
            return false;
        }
        return true;
    }

    public function invoiceDetails($sales_id)
    {
        $sales_info_query = "SELECT a.customer_name, a.mobile, a.customer_gstin, a.address, a.city, a.state, a.country, b.sales_date, b.sales_id, b.sales_time, b.reference_no, b.sales_status, b.tax_amt_cgst, b.tax_amt_sgst, c.tax ,COALESCE(b.grand_total,0) AS grand_total, COALESCE(b.sub_total, 0) AS sub_total, COALESCE(b.paid_amount, 0) AS paid_amount,COALESCE(b.other_charges_input, 0) AS other_charges_input, b.other_charges_type, COALESCE(b.other_charges_amt,0) AS other_charges_amt, COALESCE(b.discount_on_all_input,0) AS discount_on_all_input, COALESCE(b.discount_on_all_amt,0) AS discount_on_all_amt,b.discount_on_all_type, COALESCE(b.round_off,0) AS round_off, c.tax, b.payment_status FROM customer AS a, sales AS b, tax as c WHERE a.`customer_id`=b.`customer_id` AND b.`sales_id`='$sales_id' AND b.`tax_id`=c.`tax_id`";
        $this->db->prepare($sales_info_query);
        $this->db->execute();
        $sales_info = $this->db->fetchAllAssociative();

        $shop_details_query = "SELECT * from shopdetails where id=1";
        $this->db->prepare($shop_details_query);
        $this->db->execute();
        $shop_details = $this->db->fetchAssociative();

        $sales_items_query = "SELECT b.item_name, a.sales_qty, a.price_per_unit, a.discount_input, a.discount_amt, a.unit_total_cost, a.total_cost FROM salesitems AS a, items AS b WHERE b.`item_id`=a.`item_id` AND a.sales_id='$sales_id'";
        $this->db->prepare($sales_items_query);
        $this->db->execute();
        $sales_items = $this->db->fetchAllAssociative();

        return ['sales_info' => $sales_info, 'shop_details' => $shop_details, 'sales_items' => $sales_items];
    }

    public function removeSalesFromTable($ids)
    {
        $this->db->beginTransaction();
        //Find the customer id 
        $query = "SELECT customer_id, sales_id FROM sales WHERE sales_id IN ($ids)";

        $this->db->prepare($query);
        $this->db->execute();
        $cust_result = $this->db->fetchAllAssociative();

        $query = "SELECT * FROM salesreturn WHERE sales_id IN ($ids)";
        $this->db->prepare($query);
        $this->db->execute();
        if ($this->db->countRows() > 0) {
            $res2 = $this->db->fetchAllAssociative();
            $err = '';
            foreach ($res2 as $result) {
                $this->db->prepare("SELECT sales_id FROM sales WHERE sales_id='" . $result['sales_id'] . "'");
                $this->db->execute();
                $res3 = $this->db->fetchAssociative();
                $err .= "<br>Invoice Code: " . $res3['sales_id'];
            }
            $err .= "<br>Already Raised Returns, Please Delete Before Deleting Original Invoice";
            echo $err;
            exit;
        }

        $this->db->prepare("DELETE FROM salespayments WHERE sales_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM salesitems WHERE sales_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM sales WHERE sales_id IN ($ids)");
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

        foreach ($cust_result as $res) {
            $temp = $this->updateSalesPaymentStatus($res['sales_id'], $res['customer_id']);
            if (!$temp) {
                return false;
            }
        }
        $this->db->commit();
        return true;
    }

    public function deletePayments($payment_id)
    {
        $this->db->beginTransaction();
        $query1 = "SELECT sales_id FROM salespayments WHERE id='$payment_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $sales_id = $this->db->fetchAssociative()['sales_id'];

        $query2 = "SELECT customer_id FROM sales WHERE sales_id='$sales_id'";
        $this->db->prepare($query2);
        $this->db->execute();
        $customer_id = $this->db->fetchAssociative()['customer_id'];

        $this->db->prepare("DELETE FROM salespayments WHERE id='$payment_id'");
        $res1 = $this->db->execute();

        $res2 = $this->updateSalesPaymentStatus($sales_id, $customer_id);

        if ($res1 && $res2) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
    }


    public function showPayNowModal($sales_id)
    {
        $q1 = "SELECT * FROM sales WHERE sales_id='$sales_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAssociative();
        $customer_id = $r1['customer_id'];
        $q2 = "SELECT * FROM customer WHERE customer_id='$customer_id'";
        $this->db->prepare($q2);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();

        $customer_name = $r2['customer_name'];
        $customer_mobile = $r2['mobile'];
        $customer_gstin = $r2['customer_gstin'];
        $customer_address = $r2['address'];
        $customer_state = $r2['state'];
        $customer_pincode = $r2['pincode'];
        $customer_opening_balance = $r2['opening_balance'];
        $customer_sales_due = $r2['sales_due'];

        $sales_date = $r1['sales_date'];
        $reference_no = $r1['reference_no'];
        $sales_id = $r1['sales_id'];
        $grand_total = $r1['grand_total'];
        $paid_amount = $r1['paid_amount'];
        $due_amount = $grand_total - $paid_amount;

        
        ?>
         <!-- The Modal -->
    <div id="view_modal" class="modal">

<!-- Modal content -->
<div class="modal-content">
    <div class="modal-header">
    <span class="close">&times;</span>
    <h2> Pay Now</h2>
    </div>
    <div class="modal-body">
        <h4>Customer Details</h4><br>
        <div class="modal-row" style="justify-content:center">
            <div class="modal-col" style="width: 200px;">
                <address>
                    <strong><?php echo $customer_name; ?></strong><br>
                    <?php echo (!empty(trim($customer_mobile))) ? "Mobile : $customer_mobile <br>" : '';?>
                    <?php echo (!empty(trim($customer_address))) ? "Address :$customer_address <br> $customer_state" : '';?>
                    <?php echo (!empty(trim($customer_gstin))) ? "GST NO: ".$customer_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 200px;">
            <h4>Sales Details</h4><br>
                <address>
                   <b>Invoice No: <?php echo $sales_id;?></b><br>
                   <b>Date : <?php echo $sales_date?></b><br>
                   <b>Grand Total: <?php echo $grand_total;?></b><br>
                </address>
            </div>
            </div>
            <div class="modal-row" style="align-items: flex-start;flex-direction:column;margin-top:10px;">
            <b>Paid Amount : <span><?=number_format($paid_amount,2);?></span></b>
            <b>Due Amount  : <span  id='due_amount_temp'><?=number_format($due_amount,2);?></span></b>
            </div>
        <br>
        <div class="modal-row" style="width: 100%;justify-content:center">
            <div class="" style="width: 95%;">
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
                            <input type="text" style="width: 100%;margin:5px" class="req-input"  id="amount" name="amount" data-due-amt='<?=print $due_amount;?>' value="<?=number_format($due_amount,2,'.','');?>" >
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
    <button type="button" id="button" onclick="save_payment('<?=$sales_id;?>')" style="background-color: #5240a8;float:right">Save</button>
    </div>
</div>

        <?php
    }


    public function viewPaymentModal($sales_id)
    {
        $q1 = "SELECT * FROM sales WHERE sales_id='$sales_id'";
        $this->db->prepare($q1);
        $this->db->execute();
        $r1 = $this->db->fetchAssociative();

        $customer_id = $r1['customer_id'];
        $q2 = "SELECT * FROM customer WHERE customer_id='$customer_id'";
        $this->db->prepare($q2);
        $this->db->execute();
        $r2 = $this->db->fetchAssociative();

        
        $customer_name = $r2['customer_name'];
        $customer_mobile = $r2['mobile'];
        $customer_gstin = $r2['customer_gstin'];
        $customer_address = $r2['address'];
        $customer_state = $r2['state'];
        $customer_pincode = $r2['pincode'];
        $customer_opening_balance = $r2['opening_balance'];
        $customer_sales_due = $r2['sales_due'];

        $sales_date = $r1['sales_date'];
        $reference_no = $r1['reference_no'];
        $sales_id = $r1['sales_id'];
        $grand_total = $r1['grand_total'];
        $paid_amount = $r1['paid_amount'];
        $due_amount = $grand_total - $paid_amount;

        ?>
                <!-- The Modal -->
    <div id="view_modal" class="modal">

<!-- Modal content -->
<div class="modal-content">
    <div class="modal-header">
    <span class="close">&times;</span>
    <h2>View Payments</h2>
    </div>
    <div class="modal-body">
        <div class="modal-row" style="justify-content: center;">
            <div class="modal-col" style="width: 200px;">
        <h4>Customer Details</h4><br>
                <address>
                    <strong><?php echo $customer_name; ?></strong><br>
                    <?php echo (!empty(trim($customer_mobile))) ? "Mobile : $customer_mobile <br>" : '';?>
                    <?php echo (!empty(trim($customer_address))) ? "Address :$customer_address <br> $customer_state" : '';?>
                    <?php echo (!empty(trim($customer_gstin))) ? "GST NO: ".$customer_gstin."<br>" : '';?>
                </address>
            </div>
            <div class="modal-col" style="width: 200px;">
            <h4>Sales Details</h4><br>
                <address>
                   <b>Invoice No: <?php echo $sales_id;?></b><br>
                   <b>Date : <?php echo $sales_date?></b><br>
                   <b>Grand Total: <?php echo $grand_total;?></b><br>
                </address>
            </div>
            </div>
           
            <div class="modal-row" style="width: 100%;">
                    <br>
                <table class="table" style="width: 100%;">
                    <thead>
                    <tr class="bg-primary">
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Payment</th>
                    <th>Payment Type</th>
                    <th>Payment Note</th>
                    <th>Created by</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $q5 = "SELECT * FROM salespayments WHERE sales_id='$sales_id' and payment>0";
                        $i = 1;
                        $str = '';
                        $this->db->prepare($q5);
                        $this->db->execute();
                        if($this->db->countRows() > 0){
                            $res = $this->db->fetchAllAssociative();
                            foreach($res as $res1){
                                echo "<tr>";
                                echo "<td>".$i++."</td>";
                                echo "<td>".$res1['payment_date']."</td>";
                                echo "<td>".number_format($res1['payment'],2)."</td>";
                                echo "<td>".$res1['payment_type']."</td>";
                                echo "<td>".$res1['payment_note']."</td>";
                                echo "<td>".$res1['created_by']."</td>";
                                echo "<td><a onclick='delete_sales_payment(".$res1['id'].")' style='color:crimson;cursor:pointer;' ><i class='fa fa-trash'></i></a></td>";	
                                echo "</tr>";
                            }
                        }else{
                            echo "<tr><td colspan='7' style='text-align:center'>No Records Found</td></tr>";
                        }
                    ?>
                </tbody>
                </table>
            </div>
        <br>
    <div class="modal-footer" style="height:50px">
    <div class="modal-row" style="align-items: flex-end;flex-direction:column;">
            <b>Paid Amount : <span><?=number_format($paid_amount,2);?></span></b>
            <b>Due Amount  : <span><?=number_format($due_amount,2);?></span></b>
            </div>

    </div>
</div>

 
        <?php
    }
    /**
     * Generate new Customer id
     */
    public function createSalesID()
    {
        $this->db->prepare("SELECT sales_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['sales_prefix'];
        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM sales";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        $sales_id = $prefix . str_pad($result['maxid'], 4, '0', STR_PAD_LEFT);

        return $sales_id;
    }
}
