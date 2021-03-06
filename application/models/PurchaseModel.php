<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class PurchaseModel extends Model
{

    public $table = 'purchase';

    public $columnOrder = ['purchase_date', 'purchase_id', 'reference_no', 'grand_total', 'payment_status', 'created_by', 'supplier_id', 'paid_amount', 'purchase_status'];

    public $columnOrderNew = ['purchase_id', 'purchase_date', 'reference_no', 'grand_total', 'created_by', 'supplier_id', 'purchase_status', 'purchase_time', 'other_charges_input', 'other_charges_amt', 'other_charges_type', 'discount_on_all_input', 'discount_on_all_type', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off'];

    public $dbColumnOrder = ['a.id', 'a.return_bit', 'a.purchase_id', 'a.purchase_date', 'a.purchase_time', 'a.grand_total', 'a.supplier_id', 'a.created_by', 'a.paid_amount',  'a.purchase_status', 'b.supplier_name', 'a.reference_no', 'a.payment_status'];

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
            'Purchase Date' => [$fields['purchase_date'], 'required'],
            'Supplier ID' => [$fields['supplier_id'], 'required'],
            'Sub Total' => [$fields['sub_total'], 'required'],
            'Grand Total' => [$fields['grand_total'], 'required'],
            'Status' => [$fields['purchase_status'], 'required'],

        ])) {
            $this->error = $validation->errors();
            return $this->error;
        }
        if ($command == 'save') {
            if (!$validation->validate([
                'Purchase ID' => [$fields['purchase_id'], 'required|unique(purchase, purchase_id)'],
            ])) {
                $this->error = $validation->errors();
                return $this->error;
            }
        }
        $amount = $fields['amount'];
        $payment_type = $fields['payment_type'];
        $payment_note = $fields['payment_note'];
        unset($fields['amount']);

        $rowcount = $fields['hidden_rowcount'];
        unset($fields['hidden_rowcount']);

        if ($command == 'save') {

            $query = "INSERT INTO purchase(";
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
            $columnOrder = ['reference_no', 'purchase_date', 'purchase_status', 'supplier_id', 'other_charges_input', 'other_charges_amt', 'discount_on_all_type', 'discount_on_all_input', 'discount_on_all_amt', 'tax_id', 'tax_amt_cgst', 'tax_amt_sgst', 'sub_total', 'round_off', 'grand_total'];
            $query = "UPDATE purchase SET ";

            foreach ($columnOrder as $column) {
                $query .= "$column =:$column,";
            }
            $query = substr($query, 0, -1) . " WHERE purchase_id =:purchase_id";

            $this->db->prepare($query);
            $this->db->bindValue(':purchase_id', $fields['purchase_id']);
            foreach ($columnOrder as $column) {
                $this->db->bindValue(":$column", $fields[$column]);
            }
            $result = $this->db->execute();

            $this->db->prepare("DELETE FROM purchaseitems WHERE purchase_id=:purchase_id");
            $this->db->bindValue(':purchase_id', $fields['purchase_id']);
            $result1 = $this->db->execute();

            if (!$result1) {
                return false;
            }
        }

        //Getting post data from Form
        for ($i = 1; $i <= $rowcount; $i++) {

            if (isset($_REQUEST['tr_item_id_' . $i]) && !empty($_REQUEST['tr_item_id_' . $i])) {
                $item_id = $_REQUEST['tr_item_id_' . $i];
                $purchase_qty = $_REQUEST['td_data_qty_' . $i];
                $per_unit_price = $_REQUEST['td_data_per_unit_price_' . $i];
                $unit_total_cost = $_REQUEST['td_data_tot_unit_cost_' . $i];
                $total_cost = $_REQUEST['td_data_total_cost_' . $i];
                $discount_type = $_REQUEST['item_discount_type_' . $i];
                $discount_input = $_REQUEST['td_data_discount_input_' . $i];
                $discount_amt = $_REQUEST['td_data_discount_amt_' . $i]; //Amount
                $discount_amt_per_unit = $discount_amt / $purchase_qty;

                $single_unit_total_cost = $per_unit_price;

                $single_unit_total_cost -= $discount_amt_per_unit;

                if ($discount_input == '' || $discount_input === 0) {
                    $discount_input = null;
                }
                if ($total_cost == '' || $total_cost === 0) {
                    $total_cost = null;
                }

                $purchase_status = $fields['purchase_status'];
                $purchase_id = $fields['purchase_id'];
                $query = "INSERT INTO purchaseitems SET purchase_id='$purchase_id', purchase_status='$purchase_status', item_id='$item_id', purchase_qty='$purchase_qty', price_per_unit='$per_unit_price', discount_type='$discount_type', discount_amt='$discount_amt', discount_input='$discount_input', unit_total_cost='$unit_total_cost', total_cost='$total_cost'";
                $this->db->prepare($query);
                $this->db->execute();

                $result = $this->itemModel->updateItemsQuantity($item_id);
                if (!$result) {
                    return false;
                }
            }
        }

        $purchase_id = $fields['purchase_id'];
        $purchase_date = $fields['purchase_date'];
        $created_date = $fields['created_date'];
        $created_time = $fields['created_time'];
        $created_by = $fields['created_by'];

        if ($amount == '' || $amount == 0) {
            $amount = null;
        }
        if ($amount > 0 && !empty($payment_type)) {
            $query = "INSERT INTO purchasepayments SET purchase_id='$purchase_id', payment_date='$purchase_date', payment='$amount' ,payment_note='$payment_note', payment_type='$payment_type', created_date='$created_date', created_time='$created_time', created_by='$created_by'";
            $this->db->prepare($query);
            $result = $this->db->execute();
            if (!$result) {
                return false;
            }
        }

        $updatePurchasePaymentStatus = $this->updatePurchasePaymentStatus($purchase_id, $fields['supplier_id']);
        if (!$updatePurchasePaymentStatus) {
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
        $query .= ' COALESCE(a.grand_total, 0)-COALESCE(a.paid_amount, 0) as purchase_due FROM ' . $this->table;
        $query .= ' as a, supplier as b WHERE b.supplier_id=a.supplier_id';

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
            $query .= ' ORDER BY purchase_due ';
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
        $query = "SELECT a.*, b.supplier_name FROM purchase AS a, supplier AS b WHERE a.purchase_id = '$id' AND a.supplier_id=b.supplier_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        if (empty($result)) {
            return null;
        }
        $data = [];
        $data['id'] = $result['id'];
        $data['purchase_id'] = $result['purchase_id'];
        $data['purchase_date'] = $result['purchase_date'];
        $data['purchase_status'] = $result['purchase_status'];
        $data['reference_no'] = $result['reference_no'];
        $data['supplier_id'] = $result['supplier_id'];
        $data['supplier_name'] = $result['supplier_name'];
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


        $query1 = "SELECT COUNT(*) AS count_items FROM purchaseitems WHERE purchase_id='$id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $count_items = $this->db->fetchAssociative()['count_items'];

        return array_merge($data, ['items_count' => $count_items]);
    }

    public function getItemCount($purchase_id)
    {
        $this->db->prepare("SELECT COUNT(*) AS items_count FROM purchaseitems WHERE purchase_id='$purchase_id'");
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
        $query = "SELECT item_id,minimum_qty,unit_id,purchase_price,stock from items where item_id=$id";
        $this->db->prepare($query);
        $this->db->execute();
        if ($this->db->countRows() > 0) {
            $res = $this->db->fetchAllAssociative();
            foreach ($res as $value) {
                $json_array[] = [
                    'id_item' => $value['item_id'],
                    'minimum_qty' => $value['minimum_qty'],
                    'unit_id' => $value['unit_id'],
                    'purchase_price' => $value['purchase_price'],
                    'stock' => $value['stock'],
                ];
            }
        }
        return json_encode($json_array);
    }

    /**
     * For the Select Category options
     */
    public function getSupplierSelect($search = '')
    {
        $query = "SELECT supplier_id, supplier_name FROM supplier";
        if (!empty($search)) {
            $query .= " WHERE supplier_name LIKE '%$search%'";
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
        $info['item_purchase_price'] = $result['purchase_price'];
        $info['purchase_price'] = $result['purchase_price'];
        $info['purchase_qty'] = 1;
        $info['item_discount'] = 0;
        $info['item_discount_type'] = 'precentage';
        $info['item_discount_input'] = 0;

        $this->returnRowWithData($rowcount, $info);
    }

    /**
     * Return purchase List
     * for purchase items list retrieve
     */
    public function returnPurchaseList($purchase_id)
    {
        $query = "SELECT * FROM purchaseitems WHERE purchase_id='$purchase_id'";
        $this->db->prepare($query);
        $this->db->execute();
        $purchase = $this->db->fetchAllAssociative();

        $rowcount = 1;
        $info = array();
        foreach ($purchase as $s1) {

            $this->db->prepare('SELECT * FROM items WHERE item_id="' . $s1['item_id'] . '"');
            $this->db->execute();
            $result = $this->db->fetchAssociative();
            $info['item_id'] = $s1['item_id'];
            $info['item_name'] = $result['item_name'];
            $info['item_available_qty'] = $result['stock_qty'];
            $info['item_purchase_price'] = $s1['price_per_unit'];
            $info['purchase_price'] = $result['purchase_price'];
            $info['purchase_qty'] = $s1['purchase_qty'];
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
        $item_amount = ($item_purchase_price * $purchase_qty);

?>
        <tr id="row_<?= $rowcount ?>" data-row='<?= $rowcount ?>'>

            <!--Item Name -->
            <td id="td_<?= $rowcount ?>_1" width="50%">
                <input type="text" placeholder="Type or click to select an item." value="<?= $item_name ?>" id="td_data_<?= $rowcount ?>_1">
            </td>
            <!-- Quantity -->
            <td id="td_qty_<?= $rowcount ?>">
                <input type="text" onkeyup="calculateQty(<?= $rowcount ?>)" name="td_data_qty_<?= $rowcount ?>" id="td_data_qty_<?= $rowcount ?>" value="<?= $purchase_qty ?>">
            </td>

            <!-- Unit Cost -->
            <td id="td_tot_unit_cost_<?= $rowcount ?>_3">
                <input type="item_rate" onkeyup="calculate_amount(<?= $rowcount; ?>)" id="td_data_tot_unit_cost_<?= $rowcount ?>" name="td_data_tot_unit_cost_<?= $rowcount ?>" value="<?= $item_purchase_price; ?>">
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

            <input type="hidden" id="td_data_per_unit_price_<?= $rowcount; ?>" name="td_data_per_unit_price_<?= $rowcount; ?>" value="<?= $item_purchase_price; ?>">
            <input type="hidden" id="tr_available_qty_<?= $rowcount; ?>_13" value="<?= $item_available_qty; ?>">
            <input type="hidden" id="tr_item_id_<?= $rowcount; ?>" name="tr_item_id_<?= $rowcount; ?>" value="<?= $item_id; ?>">
            <input type="hidden" id="td_data_discount_amt_<?= $rowcount; ?>" name="td_data_discount_amt_<?= $rowcount; ?>" value="<?= $item_id; ?>">

        </tr>
<?php
    }

    function updatePurchasePaymentByPurchaseId($purchase_id, $supplier_id)
    {
        $query1 = "SELECT COALESCE(SUM(payment), 0) AS payment FROM purchasepayments WHERE purchase_id='$purchase_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $sum_of_payments = $this->db->fetchAssociative()['payment'];

        $query2 = "SELECT COALESCE(SUM(grand_total), 0) AS total FROM purchase WHERE purchase_id='$purchase_id'";
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


        //Condition if purchase record not exist
        //Sometime called after purchase record delete
        $query3 = "UPDATE purchase SET payment_status='$payment_status', paid_amount='$sum_of_payments' WHERE purchase_id='$purchase_id'";
        $this->db->prepare($query3);
        $this->db->execute();
        $query4 = "UPDATE supplier SET purchase_due=(SELECT COALESCE(SUM(grand_total),0)-COALESCE(SUM(paid_amount),0) FROM purchase WHERE supplier_id='$supplier_id' AND purchase_status='final') WHERE supplier_id='$supplier_id'";
        $this->db->prepare($query4);
        $res = $this->db->execute();

        if (!$res) {
            return false;
        }

        return true;
    }

    public function updatePurchasePaymentStatus($purchase_id, $supplier_id)
    {
        if (!$this->updatePurchasePaymentByPurchaseId($purchase_id, $supplier_id)) {
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
            $purchasepayments_entry = array(
                'purchase_id'         => $purchase_id,
                'payment_date'        => date("Y-m-d", strtotime($payment_date)), //Current Payment with purchase entry
                'payment_type'         => $payment_type,
                'payment'             => $amount,
                'payment_note'         => $payment_note,
                'created_date'         => $created_date,
                'created_time'         => $created_time,
                'created_by'         => $created_by,
            );


            $query = "INSERT INTO purchasepayments SET";
            foreach ($purchasepayments_entry as $key => $value) {
                $query .= " $key='$value',";
            }
            $query = substr($query, 0, -1);
            $q3 = $this->db->prepare($query);
            $this->db->execute();
        } else {
            return "Please Enter Valid Amount!";
        }
        $query1 = "SELECT supplier_id FROM purchase WHERE purchase_id='$purchase_id'";
        $this->db->prepare($query1);
        $this->db->execute();
        $supplier_id = $this->db->fetchAssociative()['supplier_id'];

        $query2 = $this->updatePurchasePaymentStatus($purchase_id, $supplier_id);
        if (!$query2) {
            return false;
        }
        return true;
    }

    public function invoiceDetails($purchase_id)
    {
        $purchase_info_query = "SELECT a.supplier_name, a.mobile, a.supplier_gstin, a.address, a.city, a.state, a.country, b.purchase_date, b.purchase_id, b.purchase_time, b.reference_no, b.purchase_status, b.tax_amt_cgst, b.tax_amt_sgst ,COALESCE(b.grand_total,0) AS grand_total, COALESCE(b.sub_total, 0) AS sub_total, COALESCE(b.paid_amount, 0) AS paid_amount,COALESCE(b.other_charges_input, 0) AS other_charges_input, b.other_charges_type, COALESCE(b.other_charges_amt,0) AS other_charges_amt, COALESCE(b.discount_on_all_input,0) AS discount_on_all_input, COALESCE(b.discount_on_all_amt,0) AS discount_on_all_amt,b.discount_on_all_type, COALESCE(b.round_off,0) AS round_off, c.tax, b.payment_status FROM supplier AS a, purchase AS b, tax as c WHERE a.`supplier_id`=b.`supplier_id` AND b.`purchase_id`='$purchase_id' AND b.`tax_id`=c.`tax_id`";
        $this->db->prepare($purchase_info_query);
        $this->db->execute();
        $purchase_info = $this->db->fetchAllAssociative();

        $shop_details_query = "SELECT * from shopdetails where id=1";
        $this->db->prepare($shop_details_query);
        $this->db->execute();
        $shop_details = $this->db->fetchAssociative();

        $purchase_items_query = "SELECT b.item_name, a.purchase_qty, a.price_per_unit, a.discount_input, a.discount_amt, a.unit_total_cost, a.total_cost FROM purchaseitems AS a, items AS b WHERE b.`item_id`=a.`item_id` AND a.purchase_id='$purchase_id'";
        $this->db->prepare($purchase_items_query);
        $this->db->execute();
        $purchase_items = $this->db->fetchAllAssociative();

        return ['purchase_info' => $purchase_info, 'shop_details' => $shop_details, 'purchase_items' => $purchase_items];
    }

    public function removePurchaseFromTable($ids)
    {
        //Find the supplier id 
        $query = "SELECT supplier_id, purchase_id FROM purchase WHERE purchase_id IN ($ids)";

        $this->db->prepare($query);
        $this->db->execute();
        $cust_result = $this->db->fetchAllAssociative();

        $query = "SELECT * FROM purchasereturn WHERE purchase_id IN ($ids)";
        $this->db->prepare($query);
        $this->db->execute();
        if ($this->db->countRows() > 0) {
            $res2 = $this->db->fetchAllAssociative();
            $err = '';
            foreach ($res2 as $result) {
                $this->db->prepare("SELECT purchase_id FROM purchase WHERE purchase_id='" . $result['purchase_id'] . "'");
                $this->db->execute();
                $res3 = $this->db->fetchAssociative();
                $err .= "<br>Invoice Code: " . $res3['purchase_id'];
            }
            $err .= "<br>Already Raised Returns, Please Delete Before Deleting Original Invoice";
            echo $err;
            exit;
        }

        $this->db->prepare("DELETE FROM purchasepayments WHERE purchase_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM purchaseitems WHERE purchase_id IN ($ids)");
        $this->db->execute();

        $this->db->prepare("DELETE FROM purchase WHERE purchase_id IN ($ids)");
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
            $temp = $this->updatePurchasePaymentStatus($res['purchase_id'], $res['supplier_id']);
            if (!$temp) {
                return false;
            }
        }
        return true;
    }

    public function deletePayments($payment_id)
    {
        $query1 = "SELECT purchase_id FROM purchasepayments WHERE id=$payment_id";
        $this->db->prepare($query1);
        $this->db->execute();
        $purchase_id = $this->db->fetchAssociative()['purchase_id'];

        $query2 = "SELECT supplier_id FROM purchase WHERE purchase_id=$purchase_id";
        $this->db->prepare($query2);
        $this->db->execute();
        $supplier_id = $this->db->fetchAssociative()['supplier_id'];

        $this->db->prepare("DELETE FROM purchasepayments WHERE id='$payment_id'");
        $res1 = $this->db->execute();

        $res2 = $this->updatePurchasePaymentStatus($purchase_id, $supplier_id);

        if ($res1 && $res2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate new Purchase id
     */
    public function createPurchaseID()
    {
        //TODO PREFIX FOR CUSTOMER FROM SETTING OR SOMETHING
        $prefix = 'PR';
        //Create suppliers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM purchase";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();

        $purchase_id = $prefix . str_pad($result['maxid'], 4, '0', STR_PAD_LEFT);

        return $purchase_id;
    }
}
