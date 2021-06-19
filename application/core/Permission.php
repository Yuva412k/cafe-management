<?php

namespace app\application\core;

use app\core\Database;
use app\core\Logger;
use app\core\Session;

class Permission{

    public static $actionName = [
        'update'=>'edit',
        'remove'=>'delete',
        'list'=>'view',
        'index'=>'view',
    ];

    public static function check($role_id, $resource, $action, $action_alias = [])
    {
        if($role_id == 1){
            return true;
        }
        if(isset($action_alias[$action])){
            $actionName =  $action_alias[$action]; 
        }else{
            $actionName =  isset(self::$actionName[$action])  ? self::$actionName[$action] : $action; 
        }
        $permission = strtolower($resource).'_'.strtolower($actionName);

        $check = self::permissionCheck($role_id, $permission);

        return $check;
    }

    public static function permissionCheck($role_id, $permissions)
    {
        if($role_id == 1){
            return true;
        }
        $db = Database::openConnection();

         //If he the Admin
         if((int)Session::getUserRole()==1){
            return true;
          }

          $db->prepare("SELECT COUNT(*) as tot FROM permissions where permission='$permissions' AND role_id=$role_id");
          $db->execute();
          $tot= $db->fetchAssociative()['tot'];

          if($tot==1){
            return true;
          }
          Logger::log("Permission", $role_id . " is not allowed to perform  " . $permissions . "' User  ".Session::getUserId() , __FILE__, __LINE__);
          return false;

    }


    /**
     * 
     * check if user is admin
     */
    private static function admin($config)
    {
        $db = Database::openConnection();

        $db->prepare('SELECT * FROM '.$config['table'].' WHERE id=:id AND user_id=:user_id LIMIT 1');
        $db->bindValue(':id', $config['id']);
        $db->bindValue(':user_id', $config['user_id']);
        $db->execute();
        return $db->countRows() == 1;
    }
}