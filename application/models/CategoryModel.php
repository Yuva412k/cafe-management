<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;
use PDOException;

class CategoryModel extends Model{

    public $table = 'category';

    public $columnOrder=['category_id', 'category_name', 'category_description', 'created_by', 'created_date'];    
    
    public $dbColumnOrder = ['id','category_id', 'category_name', 'category_description', 'created_by', 'created_date'];

    public $order = ['category_id','DESC'];

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
            'Category ID' => [$fields['category_id'], 'required|unique(category, category_id)'],
            'Category Name'=> [$fields['category_name'], 'required'],            
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO category(";
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
     * Update Category on DB
     */
    public function updateCategoryFromTable($fields)
    {
        $query = "SELECT * FROM category WHERE category_name='".$fields['category_name']."' AND category_id='".$fields['category_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Category Name already Exist.';
        }else{
            try{
                $query = "UPDATE category SET category_name='".$fields['category_name']."', category_description='".$fields['category_description']."', category_id='".$fields['category_id']."' WHERE id=".$fields['id'];
                $this->db->prepare($query);
                $this->db->execute();        
            }catch(PDOException $e){
                return false;
            }
            
            return true;
        }
    }

    /**
     * Remove Particular Category on DB
     */
    public function removeCategoryFromTable($id)
    {
        $query = "SELECT COUNT(*) AS tot , b.category_name From items a, category b WHERE b.category_id=a.category_id AND a.category_id IN ($id) GROUP BY a.category_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $category_name[] = $result[$field];
            }
            $list = implode(',', $category_name);
            return $list;
        }else{
            $query = "DELETE FROM category WHERE category_id IN ($id)";
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
        $query = "SELECT * FROM category WHERE category_id = :id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['category_id'] = $result['category_id'];
        $data['category_name'] = $result['category_name'];
        $data['category_description'] = $result['category_description'];
    
        return $data;   
    
    }

    /** 
     * Generate new Category id
     */
    public function createCategoryId()
    {
        $this->db->prepare("SELECT category_prefix FROM shopdetails");
        $this->db->execute();
        $prefix = $this->db->fetchAssociative()['category_prefix'];

        //Create customers unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM ".$this->table;
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $sales_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $sales_id;
     
    }
}