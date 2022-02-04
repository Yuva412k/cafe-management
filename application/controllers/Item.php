<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Item extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['ajaxList','addItem', 'updateItem', 'removeItem', 'removeMulitpleItem','categoryAjax', 'getJsonItemsDetails'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'addItem': 
                $this->Security->config('form',['fields'=>['item_id', 'item_name', 'category_id', 'unit_id', 'minimum_qty','purchase_price','expire_date', 'profit_margin','sales_price','final_price','stock_qty','description','new_opening_stock','opening_stock_description']]);
                break;
            case 'updateItem': 
                $this->Security->config('form',['fields'=>['id','item_id', 'item_name', 'category_id', 'unit_id', 'minimum_qty','purchase_price','expire_date', 'profit_margin','sales_price','final_price','stock_qty','description','new_opening_stock','opening_stock_description']]);
                break;
            case 'removeItem':
                $this->Security->config('form',['fields'=>['item_id']]);    
                break;
            case 'removeMultipleItem':
                $this->Security->config('validateForm',false);    
                break;
            case 'ajaxList':
                $this->Security->config('validateForm',false);    
                break;

            case 'getJsonItemsDetails':
                $this->Security->config('validateForm',false);    
                break;

            case 'categoryAjax':
                $this->Security->config('validateForm',false);    
                break;
        }
        $this->loadModel('itemModel');
        $this->loadModel('unitModel');
    }
    
    public function index()
    {
        Config::setJsConfig('curPage', 'item');

       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "item/list");
    }

    public function add()
    {
        //todo
        //customer id auto generate
       Config::setJsConfig('curPage', 'item/add');
       $itemid = $this->itemModel->createItemId();
       $unitData = $this->unitModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "item/add", ['item_id'=>$itemid, 'unitData'=>$unitData]);
    }

    public function addItem()
    {
        $itemFields = ['item_id', 'item_name', 'category_id', 'unit_id', 'minimum_qty','purchase_price','expire_date', 'profit_margin','sales_price','final_price','stock_qty','description','new_opening_stock','opening_stock_description'];
        $createdDate = date('Y-m-d');
        $createdBy = Session::getUsername();
        $fields = [];
        foreach($itemFields as $field){
            if($field == 'minimum_qty' || $field == 'profit_margin' || $field == 'sales_price' || $field == 'final_price' || $field == 'stock_qty' || $field == 'new_opening_stock'){
                 $fields[$field] = empty($this->request->data($field)) ? 0 : (int)$this->request->data($field);
                 continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        $fields['expire_date'] = empty($fields['expire_date']) ? null : date('Y-m-d', strtotime($fields['expire_date']));
        $fields = array_merge($fields, ['created_date'=>$createdDate , 'created_by'=>$createdBy]);
        $result = $this->itemModel->verifyAndSave($fields);

        if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Item Successfully Stored!');
            echo "success";
        }
    }
    public function update()
    {
        $id = $this->request->param('args')[0];
        $result = $this->itemModel->get_details($id);
        $itemid = $this->itemModel->createItemId();
        $unitData = $this->unitModel->getDataTable();
        return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "item/add", ['item_id'=>$itemid, 'unitData'=>$unitData, 'result'=>$result]);
    }
    public function updateItem()
    {
        $itemFields = ['id','item_id', 'item_name', 'category_id', 'unit_id', 'stock_qty','minimum_qty','purchase_price','expire_date','profit_margin','sales_price','final_price','description','new_opening_stock','opening_stock_description'];
        $fields = []; 
        foreach($itemFields as $field){
            if($field == 'minimum_qty' || $field == 'profit_margin' || $field == 'sales_price' || $field == 'final_price' || $field == 'stock_qty' || $field == 'new_opening_stock'){
                 $fields[$field] = empty($this->request->data($field)) ? 0 : (int)$this->request->data($field);
                 continue;
            }
            $fields[$field] = $this->request->data($field);
        }
        $fields['expire_date'] = empty($fields['expire_date']) ? null : date('Y-m-d', strtotime($fields['expire_date']));
        $result = $this->itemModel->updateItemFromTable($fields);

        if(is_string($result)){
            echo "This Item Name or Item Id already Exist!";
        }else if(!$result){
            echo "failed";
        }else{
            Session::setFlashData('complete','Item Successfully Updated!');
            echo "success";
        }
    }
    public function removeItem()
    {        
        $id = "'".$this->request->data('item_id')."'";
        $result = $this->itemModel->removeItemFromTable($id);

        if(is_string($result)){
            echo "Sorry! Can't Delete, Item Name (".$result.") already in use in Sales!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }
    /**
     * Remove Multiple Categories
     */
    public function removeMultipleItem()
    {

        $ids = "'". implode("','", $_POST['deleteids'])."'"; 
        $result = $this->itemModel->removeItemFromTable($ids);
        
        if(is_string($result)){
            echo "Sorry! Can't Delete, Item Name {".$result."} already in use in Items!";
        }else if(!$result)
        {
            echo 'failed';
        }
        else{
            echo 'success';
        }
    }

    /**
     * Category ajaxList for select 2 search option
     */
    public function categoryAjax()
    {

        $search = $this->request->data('searchTerm');

        $result = $this->itemModel->getCategorySelect($search);
        if(!empty($result)){
            $data = array();

            foreach($result as $category){

                $row= array();
                $row['id'] =$category['category_id'];
                $row['text'] = $category['category_name'];
                
                $data[] =$row;
            }

        echo json_encode($data);

        }
    }

    public function getJsonItemsDetails()
    {
		$name = $this->request->data('name');
        $result =$this->itemModel->getJsonItemsDetailsFromDb($name);
        echo $result;
        exit;
    }

    public function ajaxList()
    {

        $result = $this->itemModel->getDataTable();

        //remove the keys from the return value above
        $data = [];
        $columnOrder = ['item_id', 'item_name', 'category_name', 'unit_name', 'stock_qty','minimum_qty','purchase_price','expire_date','profit_margin','sales_price'];

        foreach($result as $item){

            $row = array();
            $row[] = "<input type='checkbox' onclick='checkcheckbox()' class='row_check' name='checkbox[]' value='".$item['item_id']."'>";
            foreach($columnOrder as $column){
                $row[] = $item[$column];
            }
            $url = PUBLIC_ROOT.'item/update/'.$item['item_id'];
            
            $str2 = '<div class="dropdown">
            <a onclick="dropdown(this)" href="#"><i class="fas fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu">';
                $str2.='<li>
                    <a title="Update Record ?" href="item/update/'.$item['item_id'].'">
                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
                    </a>    
                </li>';

                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_item(\''.$item['item_id'].'\')">
                        <i class="fa fa-fw fa-trash text-red"></i>Delete
                    </a>
                </li>
                    
                </ul>
            </div>';			

            $row[] = $str2;

            $data[] = $row;
        }
        $ajaxData = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->itemModel->countAll(),
            'recordsFiltered' => $this->itemModel->countFiltered(),
            'data' => $data,
        );
        
        echo json_encode($ajaxData);
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "items";

        $action_alias = [
            'ajaxList' => 'view',
            'removeMultipleItem'=>'delete',
            'removeItem'=>'delete',
            'updateItem'=>'edit',
            'addItem'=>'add',
            'categoryAjax'=>'add',
            'getJsonItemsDetails'=>'add',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
}