<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;
use PDOException;

class TaxModel extends Model{

    public $table = 'tax';

    public $columnOrder=['tax_id', 'tax_name','tax', 'tax_description'];    
    
    public $dbColumnOrder = ['tax_id', 'tax_name', 'tax','tax_description'];

    public $order = ['tax_id', 'DESC'];

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
            'Tax ID' => [$fields['tax_id'], 'required|unique(tax, tax_id)'],
            'Tax Name'=> [$fields['tax_name'], 'required'],            
            'Tax'=> [$fields['tax'], 'required'],            
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO tax(";
        foreach($this->columnOrder as $column){
            $query .= $column.',';
        }
        $query = substr($query, 0 , -1). ') VALUES( ';
        foreach($this->columnOrder as $column){
            $query .= ':'.$column.',';
        }
        $query = substr($query , 0, -1).')';

        $this->db->prepare($query);
        $this->db->bindValues($fields);
        $this->db->execute();   
        
        return true;
    }

    // private funtion _getCus

    public function _getDataTableQuery()
    {
        $query = "SELECT ";
        foreach($this->dbColumnOrder as $column){
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
     * Update Tax on DB
     */
    public function updateTaxFromTable($fields)
    {
        $query = "SELECT * FROM tax WHERE tax_name='".$fields['tax_name']."' AND tax_id='".$fields['tax_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Tax Name already Exist.';
        }else{
            try{
                $query = "UPDATE tax SET tax_name='".$fields['tax_name']."', tax_description='".$fields['tax_description']."', tax_id='".$fields['tax_id']."' WHERE id=".$fields['id'];
                $this->db->prepare($query);
                $this->db->execute();        
            }catch(PDOException $e){
                return false;
            }
            
            return true;
        }
    }

    /**
     * Remove Particular Tax on DB
     */
    public function removeTaxFromTable($id)
    {
        $query = "SELECT COUNT(*) AS tot , b.tax_name From items a, tax b WHERE b.tax_id=a.tax_id AND a.tax_id IN ($id) GROUP BY a.tax_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $tax_name[] = $result[$field];
            }
            $list = implode(',', $tax_name);
            return $list;
        }else{
            $query = "DELETE FROM tax WHERE tax_id IN ($id)";
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
        $query = "SELECT * FROM tax WHERE tax_id = :id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['tax_id'] = $result['tax_id'];
        $data['tax_name'] = $result['tax_name'];
        $data['tax'] = $result['tax'];
        $data['tax_description'] = $result['tax_description'];
    
        return $data;   
    
    }

    /** 
     * Generate new Customer id
     */
    public function createTaxId()
    {
        //TODO PREFIX FOR CUSTOMER FROM SETTING OR SOMETHING
        $prefix = 'TAX';
        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM ".$this->table;
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $sales_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $sales_id;
     
    }
}