<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;
use app\core\Uploader;
use PDOException;

class UsersModel extends Model{

    public $table = 'users';

    public $columnOrder=[ 'name','role_id','email','password'];    
    
    public $dbColumnOrder =['a.id', 'a.name','b.role_name','a.email'];

    public $order = ['id', 'DESC'];

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
            'Username' => [$fields['email'], 'required|unique(users, email)'],
            'Name'=> [$fields['name'], 'required'],            
            'Role'=> [$fields['role_id'], 'required'],       
            'Password'=> [$fields['password'], 'required|equals('.$fields['cpassword'].')'],
        ])){
            $this->error = $validation->errors();
            return $this->error;
        }

        $query = "INSERT INTO users(";
        foreach($this->columnOrder as $column){
            $query .= $column.',';
        }
        $query = substr($query, 0 , -1). ') VALUES( ';
        foreach($this->columnOrder as $column){
            $query .= ':'.$column.',';
        }
        $query = substr($query , 0, -1).')';
        $this->db->prepare($query);
        
        $this->db->bindValue(':email',$fields['email']);
        $this->db->bindValue(':role_id',$fields['role_id']);
        $this->db->bindValue(':name',$fields['name']);
        $this->db->bindValue(':password',password_hash($fields['password'], PASSWORD_DEFAULT));
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
        $query =  substr($query, 0 , -1). ' FROM '.$this->table.' as a, roles as b WHERE a.role_id=b.id';

        $start = 0;
        foreach($this->columnOrder as $item){
            
            if(!empty($_POST['search']['value'])){
                if($start == 0){
                    $query .= ' OR ( '.$item." LIKE '%". $_POST['search']['value'] . "%'";
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
     * Update User on DB
     */
    public function updateUserFromTable($fields)
    {
        $query = 'SELECT * FROM users WHERE email="'.$fields['email'].'" AND id<>'.$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Username already Exist.';
        }else{
            try{
                $query = "UPDATE users SET name='".$fields['name']."', email='".$fields['email']."', role_id='".$fields['role_id']."', password=:password WHERE id=".$fields['id'];
                $this->db->prepare($query);
                $this->db->bindValue(":password", password_hash($fields['password'], PASSWORD_DEFAULT));
                $this->db->execute();        
            }catch(PDOException $e){
                return false;
            }
            
            return true;
        }
    }

    /**
     * Remove Particular User on DB
     */
    public function removeUserFromTable($id)
    {
        if($id == "'1'" || (strpos($id, '\'1\'') == true)){
            $str = 'Restricted! Can\'t Delete Admin';
            return $str;
               
        }else{
            $query = "DELETE FROM users WHERE id IN ($id)";
            $this->db->prepare($query);
            $this->db->execute();

            return true;
        }
    }


    public function changePassword($currentPass, $newPass, $id)
    {
        $query = "SELECT * FROM users WHERE password='$currentPass' AND id=$id";
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()==1){
            $query1 = "UPDATE users SET password=:newPassword WHERE password=:password AND id=$id";
        
            $this->db->prepare($query1);
            $this->db->bindValue(':newPassword', password_hash($newPass,PASSWORD_DEFAULT));
            $this->db->bindValue(':password', password_hash($currentPass,PASSWORD_DEFAULT));
            if($this->db->execute()){

                return true;
            }else{
                return false;
            }


        }else{
            return "Invalid current password";
        }
    }

 
    /**
     * Get user info from id
     */
    public function get_details($id)
    {
        $query = "SELECT * FROM users WHERE id=:id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        $data['id'] = $result['id'];
        $data['email'] = $result['email'];
        $data['name'] = $result['name'];
        $data['role_id'] = $result['role_id'];
    
        return $data;   
    
    }

    public function getProfileInfo($userId){
        $this->db->prepare("SELECT a.*, b.role_name FROM users as a, roles as b WHERE a.role_id=b.id And a.id='$userId'");
        $this->db->execute();
        if($this->db->countRows() != 1){
            throw new \Exception("User ID " .  $userId . " doesn't exists");
        }

        $user = $this->db->fetchAssociative();

        $user["id"] = (int)$user["id"];
        $user["role_name"] = $user["role_name"];
        $user["image"] = PUBLIC_ROOT . "image/profile/" . $user['profile_picture'];

        return $user;
      }

    /**
     * Update Profile Picture.
     *
     * @access public
     * @param  integer $userId
     * @param  array   $fileData
     * @return mixed
     * @throws Exception If failed to update profile picture.
     */
    public function updateProfilePicture($userId, $fileData){

        $image = Uploader::uploadPicture($fileData, $userId);

        if(!$image) {
            $this->errors = Uploader::errors();
            return false;
        }

        $query  =  "UPDATE users SET profile_picture = :profile_picture WHERE id = :id LIMIT 1";

        $this->db->prepare($query);
        $this->db->bindValue(':profile_picture', $image["basename"]);
        $this->db->bindValue(':id', $userId);
        $result = $this->db->execute();

        // if update failed, then delete the user picture
        if(!$result){
            Uploader::deleteFile(IMAGES . "profile_pictures/" . $image["basename"]);
            throw new \Exception("Profile Picture ". $image["basename"] . " couldn't be updated");
        }

        return $image;
      }

}