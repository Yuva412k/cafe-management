<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;
use PDOException;

class PaymenttypeModel extends Model{

    public $table = 'paymenttype';

    public $columnOrder=['paymenttype_id', 'paymenttype_name', 'paymenttype_description'];    
    
    public $dbColumnOrder = ['id','paymenttype_id', 'paymenttype_name','paymenttype_description'];

    public $order = ['paymenttype_id' , 'DESC'];

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
            'Payment Type ID' => [$fields['paymenttype_id'], 'required|unique(paymenttype, paymenttype_id)'],
            'Payment Type Name'=> [$fields['paymenttype_name'], 'required'],            
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO paymenttype(";
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
            $query .= ($_POST['order']['0']['dir'] == 'dec') ? 'DESC' : 'ASC' ;
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
     * Update Payment Type on DB
     */
    public function updatepaymenttypeFromTable($fields)
    {
        $query = "SELECT * FROM paymenttype WHERE paymenttype_name='".$fields['paymenttype_name']."' AND paymenttype_id='".$fields['paymenttype_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Payment Type Name already Exist.';
        }else{
            try{
                $query = "UPDATE paymenttype SET paymenttype_name='".$fields['paymenttype_name']."', paymenttype_description='".$fields['paymenttype_description']."', paymenttype_id='".$fields['paymenttype_id']."' WHERE id=".$fields['id'];
                $this->db->prepare($query);
                $this->db->execute();        
            }catch(PDOException $e){
                return false;
            }
            
            return true;
        }
    }

    /**
     * Remove Particular Payment Type on DB
     */
    public function removepaymenttypeFromTable($id)
    {
        if((strpos($id, '\'PT0001\'') == true) || $id = "'PT0001'" )
        {
            return "You can't Delete this Record!";
        }

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $paymenttype_name[] = $result[$field];
            }
            $list = implode(',', $paymenttype_name);
            return $list;
        }else{
            $query = "DELETE FROM paymenttype WHERE paymenttype_id IN ($id)";
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
        $query = "SELECT * FROM paymenttype WHERE paymenttype_id = :id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['paymenttype_id'] = $result['paymenttype_id'];
        $data['paymenttype_name'] = $result['paymenttype_name'];
        $data['paymenttype_description'] = $result['paymenttype_description'];
    
        return $data;   
    
    }

    public function createPaymenttypeId()
    {
        $this->db->prepare("SELECT paymenttype_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['paymenttype_prefix'];

        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM ".$this->table;
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $sales_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $sales_id;
     
    }
}