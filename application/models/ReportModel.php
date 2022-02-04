<?php 

namespace app\application\models;

use app\core\Model;

class ReportModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function showSalesReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $query = "SELECT a.id, a.sales_id, a.sales_date, b.customer_name, b.customer_id, a.grand_total, a.paid_amount ";
        $query .= "FROM sales as a, customer as b WHERE b.customer_id=a.customer_id AND sales_status='final' ";
        if($data['customer_id'] != 'all' && $data['customer_id'] != ''){
            $query .= "AND a.customer_id='".$data['customer_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.sales_date >='$from_date' and a.sales_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.sales_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.sales_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND a.payment_status='".$data['payment_status']."'";
        }


        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }

    public function showSalesReturnReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $query = "SELECT a.id, a.sales_id, a.return_id,a.return_date, b.customer_name, b.customer_id, a.grand_total, a.paid_amount ";
        $query .= "FROM salesreturn as a, customer as b WHERE b.customer_id=a.customer_id ";
        if($data['customer_id'] != 'all' && $data['customer_id'] != ''){
            $query .= " AND a.customer_id='".$data['customer_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.sales_date >='$from_date' and a.sales_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.sales_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.sales_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND a.payment_status='".$data['payment_status']."'";
        }



        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }

    public function showPurchaseReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $query = "SELECT a.id, a.purchase_id, a.purchase_date, b.supplier_name, b.supplier_id, a.grand_total, a.paid_amount ";
        $query .= "FROM purchase as a, supplier as b WHERE b.supplier_id=a.supplier_id AND purchase_status='received' ";
        if($data['supplier_id'] != 'all' && $data['supplier_id'] != ''){
            $query .= "AND a.supplier_id='".$data['supplier_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.purchase_date >='$from_date' and a.purchase_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.purchase_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.purchase_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND a.payment_status='".$data['payment_status']."'";
        }

        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }


    public function showPurchaseReturnReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $query = "SELECT a.id, a.purchase_id, a.return_id,a.return_date, b.supplier_name, b.supplier_id, a.grand_total, a.paid_amount ";
        $query .= "FROM purchasereturn as a, supplier as b WHERE b.supplier_id=a.supplier_id ";
        if($data['supplier_id'] != 'all' && $data['supplier_id'] != ''){
            $query .= "AND a.supplier_id='".$data['supplier_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.return_date >='$from_date' and a.return_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.return_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.return_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND a.payment_status='".$data['payment_status']."'";
        }
        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }


    public function showStockReport()
    {

        $query = "SELECT * FROM items";
        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }

    public function showItemSalesReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $query = "SELECT a.id, a.sales_id, a.sales_date, b.customer_name, b.customer_id, c.total_cost, c.sales_qty, d.item_name FROM sales as a, customer as b, salesitems as c, items as d  WHERE a.sales_id=c.sales_id AND a.sales_status='final' AND d.item_id=c.item_id AND b.customer_id=a.customer_id ";

        if($data['item_id'] != 'all' && $data['item_id'] != ''){
            $query .= " AND c.item_id='".$data['item_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.sales_date >='$from_date' and a.sales_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.sales_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.sales_date<='$to_date'";
        }

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAllAssociative();
    }

    public function showPurchasePaymentReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = $data['to_date'];
        $supplier_id = $data['supplier_id'];

        $query = "SELECT c.id, c.purchase_id, a.payment_date, b.supplier_name, b.supplier_id, a.payment_note,a.payment_type, a.payment FROM purchasepayments as a, supplier as b, purchase as c  WHERE  c.purchase_id=a.purchase_id AND b.supplier_id=c.supplier_id AND purchase_status='received' ";
        if($data['supplier_id'] != 'all' && $data['supplier_id'] != ''){
            $query .= "AND c.supplier_id='".$data['supplier_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (c.purchase_date >='$from_date' and c.purchase_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND c.purchase_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND c.purchase_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND c.payment_status='".$data['payment_status']."'";
        }
        $query .= " AND a.payment>0";

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAllAssociative();
    }

    public function supplierPaymentReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $supplier_id = $data['supplier_id'];

        // $query = "SELECT a.payment_date, b.supplier_name, a.payment_type, a.payment FROM supplierpayments as a, supplier as b WHERE b.supplier_id=a.supplier_id";
       
        $query = "SELECT a.id,c.supplier_name,a.payment_date,a.payment_type, COALESCE(SUM(a.payment)),a.payment_note FROM purchasepayments AS a, purchase AS b, supplier AS c WHERE b.purchase_id=a.purchase_id AND b.supplier_id=c.supplier_id ";
        
        if(!empty($supplier_id)){
            $query .= " AND c.supplier_id='$supplier_id'";
        }
        $query .= " AND a.payment>0";
        $query .= " AND (a.payment_date>='$from_date' AND a.payment_date<='$to_date')";

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAllAssociative();
    }


    public function showSalesPaymentReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $customer_id = $data['customer_id'];

        $query = "SELECT c.id, c.sales_id,a.payment_note, a.payment_date, b.customer_name, b.customer_id, a.payment_type, a.payment FROM salespayments as a, customer as b, sales as c  WHERE  c.sales_id=a.sales_id AND b.customer_id=c.customer_id AND c.sales_status='final' ";
        if($data['customer_id'] != 'all' && $data['customer_id'] != ''){
            $query .= "AND c.customer_id='".$data['customer_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (c.sales_date >='$from_date' and c.sales_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND c.sales_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND c.sales_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND c.payment_status='".$data['payment_status']."'";
        }
        $query .= " AND a.payment>0";

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAllAssociative();
    }


    public function customerPaymentReport($data)
    {
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $customer_id = $data['customer_id'];

        // $query = "SELECT a.payment_date, b.customer_name, a.payment_type, a.payment FROM customerpayments as a, customer as b WHERE b.customer_id=a.customer_id";
        $query = "SELECT a.id,c.customer_name,a.payment_date,a.payment_type, COALESCE(SUM(a.payment)),a.payment_note FROM salespayments AS a, sales AS b, customer AS c WHERE b.sales_id=a.sales_id AND b.customer_id=c.customer_id ";
        if($data['customer_id'] != 'all' && $data['customer_id'] != ''){
            $query .= "AND b.customer_id='".$data['customer_id']."'";
        }
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $query .= " AND (a.payment_date >='$from_date' and c.payment_date<='$to_date') ";
        }else if(!empty($data['from_date'])){
            $query .= " AND a.payment_date>='$from_date'";
        }else if(!empty($data['to_date'])){
            $query .= " AND a.payment_date<='$to_date'";
        }
        if(!empty($data['payment_status']) && $data['payment_status'] != 'all'){
            $query .= " AND b.payment_status='".$data['payment_status']."'";
        }
        $query .= " AND a.payment>0";

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAllAssociative();
    }

    public function showExpiredItemsReport($data){
    
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $item_id = $data['item_id'];
    
        $query = "SELECT id, item_id, item_name, expire_date, stock_qty FROM items WHERE expire_date<='$to_date'";

        
        if($data['item_id'] != 'all' && $data['item_id'] != ''){
            $query .= " AND item_id='".$data['item_id']."'";
        }

        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAllAssociative();
    }   
}
?>