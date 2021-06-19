<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class SupplierModel extends Model{

    public $table = 'supplier';

    public $columnOrder= ['supplier_id', 'supplier_name', 'mobile', 'supplier_gstin', 'address', 'city', 'state', 'pincode','country','opening_balance'];

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

        $query = "INSERT INTO supplier(supplier_id, supplier_name, mobile, supplier_gstin, address, city, state, pincode, country, opening_balance, created_date,  created_by) 
        VALUES(:supplier_id, :supplier_name , :supplier_mobile , :supplier_GST , :supplier_address , :supplier_city, :supplier_state , :supplier_pincode , :supplier_country , :supplier_balance , :createdDate  , :createdBy )";

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
        $data[] = $result['supplier_id'];
        $data[] = $result['supplier_name'];
        $data[] = $result['mobile'];
        $data[] = $result['supplier_gstin'];
        $data[] = $result['address'];
        $data[] = $result['city'];
        $data[] = $result['state'];
        $data[] = $result['country'];
        $data[] = $result['opening_balance'];
        
        return $data;   
    
    }

    /**
     * Generate new Supplier id
     */
    public function createSupplierID()
    {
        //TODO PREFIX FOR CUSTOMER FROM SETTING OR SOMETHING
        $prefix = 'SP';
        //Create suppliers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM supplier";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $supplier_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $supplier_id;
     
    }
}