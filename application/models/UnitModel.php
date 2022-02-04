<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;
use PDOException;

class UnitModel extends Model{

    public $table = 'units';

    public $columnOrder=['unit_id', 'unit_name', 'unit_description'];    
    
    public $dbColumnOrder = ['unit_id', 'unit_name','unit_description'];

    public $order = ['unit_id' , 'DESC'];

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
            'Unit ID' => [$fields['unit_id'], 'required|unique(units, unit_id)'],
            'Unit Name'=> [$fields['unit_name'], 'required'],            
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO units(";
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
     * Update Unit on DB
     */
    public function updateUnitFromTable($fields)
    {
        $query = "SELECT * FROM units WHERE unit_name='".$fields['unit_name']."' AND unit_id='".$fields['unit_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Unit Name already Exist.';
        }else{
            try{
                $query = "UPDATE units SET unit_name='".$fields['unit_name']."', unit_description='".$fields['unit_description']."', unit_id='".$fields['unit_id']."' WHERE id=".$fields['id'];
                $this->db->prepare($query);
                $this->db->execute();        
            }catch(PDOException $e){
                return false;
            }
            
            return true;
        }
    }

    /**
     * Remove Particular Unit on DB
     */
    public function removeUnitFromTable($id)
    {
        $query = "SELECT COUNT(*) AS tot , b.unit_name From items a, units b WHERE b.unit_id=a.unit_id AND a.unit_id IN ($id) GROUP BY a.unit_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $unit_name[] = $result[$field];
            }
            $list = implode(',', $unit_name);
            return $list;
        }else{
            $query = "DELETE FROM units WHERE unit_id IN ($id)";
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
        $query = "SELECT * FROM units WHERE unit_id = :id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['unit_id'] = $result['unit_id'];
        $data['unit_name'] = $result['unit_name'];
        $data['unit_description'] = $result['unit_description'];
    
        return $data;   
    
    }

    /** 
     * Generate new Customer id
     */
    public function createUnitId()
    {
        $this->db->prepare("SELECT unit_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['unit_prefix'];

        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM ".$this->table;
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $sales_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $sales_id;
     
    }
}