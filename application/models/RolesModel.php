<?php 

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class RolesModel extends Model{

    public $table = 'roles';

    public $columnOrder=['role_name', 'role_description'];    
    
    public $dbColumnOrder = ['id', 'role_name','role_description'];

    public $order = ['role_name' , 'DESC'];

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
            'Role Name'=> [$fields['role_name'], 'required|unique(roles, role_name)'],            
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $query = "INSERT INTO roles(";
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
        $q1 =$this->db->execute();   


        //set permissions
        $this->db->prepare('SELECT id FROM roles WHERE role_name="'.$fields['role_name'].'"');
        $this->db->execute();
        $id = $this->db->fetchAssociative()['id'];
        $q2 = $this->setPersmissions($id);

        if($q1 && $q2){
            return true;
        }
        return false;
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
     * Update Role on DB
     */
    public function updateRoleFromTable($fields)
    {
        if($fields['id'] == 1){
            return 'This role cannot be edited';
        }
        $query = "SELECT * FROM roles WHERE role_name='".$fields['role_name']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Role Name already Exist.';
        }

        $query = 'UPDATE roles SET role_name="'.$fields['role_name'].'", role_description="'.$fields['role_description'].'" WHERE id='.$fields['id'];
        $this->db->prepare($query);
        $q1 = $this->db->execute();        
    

        //set permissions
        $q2 = $this->setPersmissions($fields['id']);

        if($q1 && $q2){
            return true;
        }
        return false;
    }

    /**
     * Remove Particular Role on DB
     */
    public function removeRoleFromTable($id)
    {
        if(str_contains($id, '1')){
            return 'This role cannot be Removed';
        }
        $query = "SELECT COUNT(*) AS tot , b.role_name From users a, roles b WHERE a.role_id IN ($id) GROUP BY a.role_id";
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

        if(isset($result['tot']) && $result['tot'] > 0){
            foreach($result as $field){
                $role_name[] = $result[$field];
            }
            $list = implode(',', $role_name);
            return $list;
        }else{
            $query = "DELETE FROM roles WHERE id IN ($id)";
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
        $query = "SELECT * FROM roles WHERE id =:id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['role_name'] = $result['role_name'];
        $data['role_description'] = $result['role_description'];

        $this->db->prepare("SELECT permission FROM permissions where role_id=".$id);
        $this->db->execute();
        $rowcount = $this->db->countRows();
        $permissionData = $this->db->fetchAllAssociative();
        
        $data['permissions'] = [$rowcount, $permissionData];

        return $data;   
    
    }

    function getSelected($role_id,$permissions_array){
		$info=array();
		foreach ($permissions_array as $key => $value) {
			if(isset($_POST['permission'][$value])) {
				 array_push ($info,array('role_id'=>$role_id,'permission'=>$value));
			}
		}
		return $info;
	}

	public function setPersmissions($role_id= 0){

        $result =array();
		//PERMISSIONS KEY FROM FRONT END
		$result= ($this->getSelected($role_id,array(
														'users_add',
														'users_edit',
														'users_delete',
														'users_view',
														'tax_add',
														'tax_edit',
														'tax_delete',
														'tax_view',
														'units_add',
														'units_edit',
														'units_delete',
														'units_view',
														'roles_add',
							                            'roles_edit',
							                            'roles_delete',
							                            'roles_view',
							                            'items_add',
							                            'items_edit',
							                            'items_delete',
							                            'items_view',
							                            'suppliers_add',
							                            'suppliers_edit',
							                            'suppliers_delete',
							                            'suppliers_view',
							                            'customers_add',
							                            'customers_edit',
							                            'customers_delete',
							                            'customers_view',
							                            'purchase_add',
							                            'purchase_edit',
							                            'purchase_delete',
							                            'purchase_view',
							                            'sales_add',
							                            'sales_edit',
							                            'sales_delete',
							                            'sales_view',
							                            'sales_payment_view',
							                            'sales_payment_add',
							                            'sales_payment_delete',
							                            'sales_report',
							                            'purchase_report',
							                            'profit_report',
							                            'stock_report',
							                            'item_sales_report',
							                            'purchase_payments_report',
							                            'sales_payments_report',
							                            'expired_items_report',
							                            'items_category_add',
							                            'items_category_edit',
							                            'items_category_delete',
							                            'items_category_view',
				                                        'dashboard_view',
				                                        'purchase_return_add',
				                                        'purchase_return_edit',
				                                        'purchase_return_delete',
				                                        'purchase_return_view',
				                                        'purchase_return_report',
				                                        'sales_return_add',
				                                        'sales_return_edit',
				                                        'sales_return_delete',
				                                        'sales_return_view',
				                                        'sales_return_report',
				                                        'sales_return_payment_view',
							                            'sales_return_payment_add',
							                            'sales_return_payment_delete',
							                            'purchase_return_payment_view',
							                            'purchase_return_payment_add',
							                            'purchase_return_payment_delete',
							                            'purchase_payment_view',
							                            'purchase_payment_add',
							                            'purchase_payment_delete',
							                            'payment_types_add',
							                            'payment_types_edit',
							                            'payment_types_delete',
							                            'payment_types_view',
													)));



		//BEFORE SAVING DELETE ALL PERSMISSIONS OF THE SPESIFIED ROLE
        $this->db->prepare("DELETE FROM permissions WHERE role_id=:role_id");
        $this->db->bindValue(':role_id', $role_id);
        $this->db->execute();

		//SAVE PERSMISSIONS
        $insertQuery = array();
        $insertData = array();
		$q1= "INSERT INTO permissions(role_id, permission) VALUES "; 
        foreach($result as $res){
            $insertQuery[] = '(?,?)';
            $insertData[] = $res['role_id'];
            $insertData[] = $res['permission'];
        }

        if(!empty($insertQuery)){
            $q1 .= implode(', ',$insertQuery);
            $this->db->prepare($q1);
            $r = $this->db->execute($insertData);
            
            if(!$r){
                return false;
            }
        }

		return true;
	}

}