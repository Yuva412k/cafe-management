<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class SettingsModel extends Model{

    public $table = 'sitesettings';

    public function __construct()
    {
        parent::__construct();
    }

    public function getPrefix()
    {
        $column = ['item_prefix','customer_prefix','category_prefix','sales_prefix','sales_return_prefix','purchase_prefix','purchase_return_prefix','supplier_prefix','paymenttype_prefix','tax_prefix','unit_prefix'];
        $query = "SELECT ";
        foreach($column as $col){
            $query .= $col.",";
        }
        $query = substr($query, 0, -1);
        $query .= " FROM shopdetails";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAssociative();
    }
    public function getTheme()
    {
        $this->db->prepare("SELECT theme FROM shopdetails");
        $this->db->execute();
        return $this->db->fetchAssociative()['theme'];
    }

    public function getInvoice()
    {
        $this->db->prepare("SELECT invoice FROM shopdetails");
        $this->db->execute();
        return $this->db->fetchAssociative()['invoice'];
    }
    public function getShopDetails()
    {
        $column = ['shop_name','shop_mobile','shop_phone','shop_email','shop_address','shop_city','shop_state','shop_pincode','shop_gstin', 'invoice_footer','theme','invoice'];
        $query = "SELECT ";
        foreach($column as $col){
            $query .= $col.",";
        }
        $query = substr($query, 0, -1);
        $query .= " FROM shopdetails";
        $this->db->prepare($query);
        $this->db->execute();

        return $this->db->fetchAssociative();
    }
    public function updateShopDetails($fields)
    {
        $validation = new Validation();
        if (!$validation->validate([
            'Shop Name' => [$fields['shop_name'], 'required'],
            'Shop Mobile' => [$fields['shop_mobile'], 'required'],
            'Shop Phone' => [$fields['shop_phone'], 'required'],
            'Shop Email' => [$fields['shop_email'], 'required'],
            'Shop Gst No' => [$fields['shop_gstin'], 'required'],
            'Theme' => [$fields['theme'], 'required'],
            'Invoice' => [$fields['invoice'], 'required'],

        ])) {
            $this->error = $validation->errors();
            return $this->error;
        }
        $this->db->beginTransaction();

        $column = ['shop_name','shop_mobile','shop_phone','shop_email','shop_address','shop_city','shop_state','shop_pincode','shop_gstin', 'invoice_footer','theme','invoice'];
        $query = "UPDATE shopdetails SET ";
        foreach($column as $col){
            $query .= " $col=:$col,";
        }
        $query = substr($query, 0, -1);

        $this->db->prepare($query);
        foreach($column as $col){
            $this->db->bindValue(":$col", $fields[$col]);
        }
        $r = $this->db->execute();
        if(!$r){
            return false;
        }
        $this->db->commit();
        return true;
    }
    public function updatePrefix($fields)
    {
        $validation = new Validation();
        if (!$validation->validate([
            'Item' => [$fields['item_prefix'], 'required|maxLen(4)'],
            'Customer' => [$fields['customer_prefix'], 'required|maxLen(4)'],
            'Catgeory' => [$fields['category_prefix'], 'required|maxLen(4)'],
            'Sales' => [$fields['sales_prefix'], 'required|maxLen(4)'],
            'Sales Return' => [$fields['sales_return_prefix'], 'required|maxLen(4)'],
            'Purchase' => [$fields['purchase_prefix'], 'required|maxLen(4)'],
            'Purchase Return' => [$fields['purchase_return_prefix'], 'required|maxLen(4)'],
            'Supplier' => [$fields['supplier_prefix'], 'required|maxLen(4)'],
            'Payment Types' => [$fields['paymenttype_prefix'], 'required|maxLen(4)'],
            'Tax' => [$fields['tax_prefix'], 'required|maxLen(4)'],
            'Unit' => [$fields['unit_prefix'], 'required|maxLen(4)'],

        ])) {
            $this->error = $validation->errors();
            return $this->error;
        }
        $this->db->beginTransaction();
        $column = ['item_prefix','customer_prefix','category_prefix','sales_prefix','sales_return_prefix','purchase_prefix','purchase_return_prefix','supplier_prefix','paymenttype_prefix','tax_prefix','unit_prefix'];
        $query = "UPDATE shopdetails SET ";
        foreach($column as $col){
            $query .= " $col=:$col,";
        }
        $query = substr($query, 0, -1);
        $query = " WHERE id=1";
        $this->db->prepare($query);
        foreach($column as $col){
            $this->db->bindValue(":$col", $fields[$col]);
        }
        $r = $this->db->execute();
        if(!$r){
            return false;
        }
        $this->db->commit();
        return true;
    }
}