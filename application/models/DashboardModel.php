<?php

namespace app\application\models;
use app\core\Model;

class DashboardModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function getDashboardData()
    {
        $today_date = date("Y-m-d");
        // Today Total Sales
        $this->db->prepare("SELECT COALESCE(sum(grand_total),0) AS today_total_sales FROM sales WHERE sales_date='$today_date' AND sales_status='final'");
        $this->db->execute();
        $today_total_sales = $this->db->fetchAssociative()['today_total_sales'];

        //Today Total Purchase
        $this->db->prepare("SELECT COALESCE(sum(grand_total),0) AS today_total_purchase FROM purchase WHERE purchase_date='$today_date'");
        $this->db->execute();
        $today_total_purchase = $this->db->fetchAssociative()['today_total_purchase'];

        // Today Total Sales Due
        $this->db->prepare("SELECT COALESCE(sum(grand_total),0)-COALESCE(SUM(paid_amount),0) AS today_total_sales_due FROM sales WHERE sales_date='$today_date' AND sales_status='final'");
        $this->db->execute();
        $today_total_sales_due = $this->db->fetchAssociative()['today_total_sales_due'];

        //Today Total Purchase Due
        $this->db->prepare("SELECT COALESCE(sum(grand_total),0)-COALESCE(SUM(paid_amount),0) AS today_total_purchase_due FROM purchase WHERE purchase_date='$today_date'");
        $this->db->execute();
        $today_total_purchase_due = $this->db->fetchAssociative()['today_total_purchase_due'];
        
        //Total Suppliers
        $this->db->prepare("SELECT COALESCE(count(*),0) AS total_suppliers FROM supplier");
        $this->db->execute();
        $total_suppliers = $this->db->fetchAssociative()['total_suppliers'];

        //Total Customrs
        $this->db->prepare("SELECT COALESCE(count(*),0) AS total_customers FROM customer WHERE id<>1");
        $this->db->execute();
        $total_customers = $this->db->fetchAssociative()['total_customers'];

        //Total Purchase Count
        $this->db->prepare("SELECT COALESCE(count(*),0) AS total_purchase_count FROM purchase WHERE purchase_status='received'");
        $this->db->execute();
        $total_purchase_count = $this->db->fetchAssociative()['total_purchase_count'];

        //Total Sales Count
        $this->db->prepare("SELECT COALESCE(count(*),0) AS total_sales_count FROM sales WHERE sales_status='final'");
        $this->db->execute();
        $total_sales_count = $this->db->fetchAssociative()['total_sales_count'];

        $data = array();
        $data['total_suppliers'] = $total_suppliers;
        $data['total_customers'] = $total_customers;
        $data['today_total_purchase'] = $today_total_purchase;
        $data['today_total_sales'] = $today_total_sales;
        $data['today_total_sales_due'] = $today_total_sales_due;
        $data['today_total_purchase_due'] = $today_total_purchase_due;
        $data['total_sales_count'] = $total_sales_count;
        $data['total_purchase_count'] = $total_purchase_count;

        return $data;
    }

    public function getDataTable()
    {

        //Recently Added Items
        $q1="SELECT item_id,item_name,sales_price FROM items ORDER BY item_id desc limit 5";
        $this->db->prepare($q1);
        $this->db->execute();
        $recently_add_items = $this->db->fetchAllAssociative();

        //Expired items
        $date = date("Y-m-d");
        $q2="SELECT a.item_name,a.item_id,b.category_name,a.expire_date from items as a, category as b where b.category_id=a.category_id and a.expire_date<='$date'";
        $this->db->prepare($q2);
        $this->db->execute();
        $expired_items = $this->db->fetchAllAssociative();  

        //Stock Alert
        $q3="SELECT b.category_name,a.item_id,a.item_name,a.stock_qty FROM items a, category b WHERE a.stock_qty<=a.minimum_qty and b.category_id=a.category_id GROUP BY a.item_id";
        $this->db->prepare($q3);
        $this->db->execute();
        $stock_alert = $this->db->fetchAllAssociative();

        //Purchase data for Chart
        $q4="SELECT COALESCE(SUM(grand_total),0) AS purchase_total,MONTH(purchase_date) AS purchase_date FROM purchase where purchase_status='received'  GROUP BY MONTH(purchase_date) ";
        $this->db->prepare($q4);
        $this->db->execute();
        $purchase_chart_data = $this->db->fetchAllAssociative();

        //Sales Data for Chart
        $q5="SELECT COALESCE(SUM(grand_total),0) AS sales_total,MONTH(sales_date) AS sales_date FROM sales where sales_status='final' GROUP BY MONTH(sales_date)";
        $this->db->prepare($q5);
        $this->db->execute();
        $sales_chart_data = $this->db->fetchAllAssociative();


        //Pie Chart
        $q6 = "SELECT COALESCE(SUM(b.sales_qty),0) AS sales_qty, a.item_name FROM items AS a, salesitems AS b ,sales AS c WHERE a.item_id=b.`item_id` AND b.sales_id=c.`sales_id` AND c.`sales_status`='final' GROUP BY a.item_id limit 10";
        $this->db->prepare($q6);
        $this->db->execute();
        $pie_chart_data = $this->db->fetchAllAssociative();


        return ['recently_add_items'=>$recently_add_items, 'expired_items'=>$expired_items,'stock_alert'=>$stock_alert, 'purchase_chart_data'=>$purchase_chart_data, 'sales_chart_data'=>$sales_chart_data, 'pie_chart_data'=>$pie_chart_data];
    }
}

?>