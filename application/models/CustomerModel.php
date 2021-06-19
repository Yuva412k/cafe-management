<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class CustomerModel extends Model{

    public $table = 'customer';

    public $columnOrder= ['customer_id', 'customer_name', 'mobile', 'customer_gstin', 'address', 'city', 'state', 'pincode','country','opening_balance'];

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
                if($start === 0){
                    $query .= ' WHERE ( '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }else{
                    $query .= ' OR '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }

                if(count($this->columnOrder) -1 === $start){
                    $query .= ')';
                }
            }
        $start++;

        }

        if(isset($_POST['order'])){
            $query .= ' ORDER BY ' . $this->dbColumnOrder[$_POST['order']['0']['column']]. ' ';
            $query .= ($_POST['order']['0']['dir'] === 'dec') ? 'DESC' : 'ASC' ;
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
        $data[] = $result['customer_id'];
        $data[] = $result['customer_name'];
        $data[] = $result['mobile'];
        $data[] = $result['customer_gstin'];
        $data[] = $result['address'];
        $data[] = $result['city'];
        $data[] = $result['state'];
        $data[] = $result['country'];
        $data[] = $result['opening_balance'];
        
        return $data;   
    
    }

    /**
     * Generate new Customer id
     */
    public function createCustomerID()
    {
        //TODO PREFIX FOR CUSTOMER FROM SETTING OR SOMETHING
        $prefix = 'CU';
        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM customer";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $customer_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $customer_id;
     
    }
}