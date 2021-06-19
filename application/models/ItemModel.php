<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Model;

class ItemModel extends Model{

    public $table = 'items';

    public $columnOrder = ['item_id', 'item_name', 'category_id', 'unit_id', 'stock_qty','minimum_qty','purchase_price','expire_date','profit_margin','sales_price','description','created_by','created_date'];
    
    public $dbColumnOrder = ['item_id', 'item_name', 'category_id', 'unit_id', 'minimum_qty','purchase_price','expire_date','profit_margin','sales_price','stock_qty','description','created_by','created_date'];

    public $searchOrder = ['a.item_id', 'a.item_name', 'b.category_name', 'c.unit_name', 'a.stock_qty','a.minimum_qty','a.sales_price', 'a.purchase_price', 'a.expire_date', 'a.profit_margin'];
    
    public $searchOrderNew = ['a.id','a.item_id', 'a.item_name', 'b.category_name', 'c.unit_name', 'a.stock_qty','a.minimum_qty','a.sales_price', 'a.purchase_price', 'a.expire_date', 'a.profit_margin'];


    public $order = ['a.id', 'DESC'];

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
            'Item ID' => [$fields['item_id'], 'required|unique(items, item_id)'],
            'Item Name'=> [$fields['item_name'], 'required|unique(items, item_name)'],            
            'Category' =>[$fields['category_id'], 'required'],
            'Unit' =>[$fields['unit_id'], 'required'],
            'Purchase Price' =>[$fields['purchase_price'], 'required'],
            'Sales Price' =>[$fields['sales_price'], 'required'],
            'Final Price' =>[$fields['final_price'], 'required'],
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $fields['sales_price'] = $fields['final_price'];
        unset($fields['final_price']);
        
        $opening_stock = $fields['new_opening_stock'];
        $opening_stock_description = $fields['opening_stock_description'];
        
        unset($fields['new_opening_stock']);
        unset($fields['opening_stock_description']);

        $query = "INSERT INTO items(";
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
        $result = $this->db->execute();   
        
        if(!$result){
            return false;
        }

        //insert opening_stock in stock entry
        if(!empty($opening_stock) && $opening_stock != 0){
            $result = $this->stockEntry($fields['created_date'], $fields['item_id'], $opening_stock, $opening_stock_description);
            if(!$result){
                return false;
            }
        }

        //Update items stock quantity in items table        
        $this->updateItemsQuantity($fields['item_id']);

        return true;
    }
     
    /**
     * Stock Entry
     */
    public function stockEntry($entry_date, $item_id, $qty=0, $note=null){
        $query = "INSERT INTO stockentry(entry_date, item_id, quantity, note) VALUES(:entry_date, :item_id, :qty, :note)";
        $this->db->prepare($query);
        $this->db->bindValue(':entry_date', $entry_date);
        $this->db->bindValue(':item_id', $item_id);
        $this->db->bindValue(':qty', $qty);
        $this->db->bindValue(':note', $note);
        $result = $this->db->execute();
        if(!$result){
            return false;
        }else{
            return true;
        }
    }

    // private funtion _getCus

    public function _getDataTableQuery()
    {
        $query = "SELECT ";
        foreach($this->searchOrder as $column){
            $query .= "$column,";
        }
        $query =  substr($query, 0 , -1). ' FROM '.$this->table.' AS a';
        //join the unit and category table
        $query .= ' LEFT JOIN category as b ON b.category_id=a.category_id';
        $query .= ' LEFT JOIN units as c ON c.unit_id=a.unit_id';
        $start = 0;
        foreach($this->searchOrder as $item){
            
            if(!empty($_POST['search']['value'])){
                if($start === 0){
                    $query .= ' WHERE ( '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }else{
                    $query .= ' OR '.$item." LIKE '%". $_POST['search']['value'] . "%'";
                }

                if(count($this->searchOrder) -1 === $start){
                    $query .= ')';
                }
            }
            $start++;
        }

        if(isset($_POST['order'])){
            $query .= ' ORDER BY ' . $this->searchOrderNew[$_POST['order']['0']['column']] . ' ';
            $query .= ($_POST['order']['0']['dir'] === 'desc') ? 'DESC' : 'ASC' ;
        }else{
            $query .= ' ORDER BY '. $this->order[0] ." ". $this->order[1];
        }
        return $query;
    }


    /**
     * For the Select Category options
     */
    public function getCategorySelect($search= '')
    {
        $query = "SELECT category_id, category_name FROM category";
        if(!empty($search)){
            $query .= " WHERE category_name LIKE '%$search%'";
        }
        $query .= " LIMIT 5";

        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAllAssociative();

       return $result;
    }

    /**
     * Used in Purchase and sales Forms
     * 
     */
	public function getJsonItemsDetailsFromDb($name){
		$data = array();
		$display_json = array();
		//if (!empty($_GET['name'])) {
			$this->db->prepare("SELECT id,item_name,item_id,stock_qty FROM items WHERE item_name LIKE '%$name%' OR item_id LIKE '%$name%'  LIMIT 10");
			$this->db->execute();
            $result = $this->db->fetchAllAssociative();
			foreach ($result as $res) {
			      $json_arr["id"] = $res['id'];
				  $json_arr["value"] = $res['item_name'];
				  $json_arr["label"] = $res['item_name'];
				  $json_arr["item_id"] = $res['item_id'];
				  $json_arr["stock_qty"] = $res['stock_qty'];
				  array_push($display_json, $json_arr);
				 /* $display_json[] =$res->id;
				  $display_json[] =$res->item_name;
				  $display_json[] =$res->item_code;*/
			}
		//}
		//echo json_encode($data);exit;
		return  json_encode($display_json);
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
     * Update Item on DB
     */
    public function updateItemFromTable($fields)
    {
        $validation = new Validation();
        if(!$validation->validate([
            'Item ID' => [$fields['item_id'], 'required'],
            'Item Name'=> [$fields['item_name'], 'required'],            
            'Category' =>[$fields['category_id'], 'required'],
            'Unit' =>[$fields['unit_id'], 'required'],
            'Purchase Price' =>[$fields['purchase_price'], 'required'],
            'Sales Price' =>[$fields['sales_price'], 'required'],
            'Final Price' =>[$fields['final_price'], 'required'],
        ])){
            $this->error = $validation->errors();
            return false;
        }

        $fields['sales_price'] = $fields['final_price'];
        unset($fields['final_price']);
        
        $new_opening_stock = $fields['new_opening_stock'];
        $opening_stock_description = $fields['opening_stock_description'];
        
      
        unset($fields['new_opening_stock']);
        unset($fields['opening_stock_description']);


        $query = "SELECT * FROM items WHERE item_name='".$fields['item_name']."' AND item_id='".$fields['item_id']."' AND id<>".$fields['id'];
        $this->db->prepare($query);
        $this->db->execute();
        if($this->db->countRows()>0){
            return 'This Item Name already Exist.';
        }else{
         
            $columnOrder = ['item_id', 'item_name', 'category_id', 'unit_id', 'stock_qty','minimum_qty','purchase_price','profit_margin','sales_price','description'];
            
            $query = "UPDATE items SET expire_date=:expire_date, ";
            foreach($columnOrder as $column){
                $query .= $column.'= "'.$fields[$column].'",';
            }
            $query = substr($query,0, -1) . ' WHERE id="'.$fields['id'].'"';
            $this->db->prepare($query);
            $this->db->bindValue(':expire_date', $fields['expire_date']);
            $result = $this->db->execute();        
            if(!$result){
                return false;
            }
        }
        $fields['created_date'] = date('Y-m-d');

        //insert opening_stock in stock entry
        if(!empty($new_opening_stock) && $new_opening_stock != 0){
      
            $result = $this->stockEntry($fields['created_date'], $fields['item_id'], $new_opening_stock, $opening_stock_description);
            if(!$result){
                return false;
            }
        }

        //Update items stock quantity in items table        
        $result = $this->updateItemsQuantity($fields['item_id']);
        if(!$result){
            return false;
        }

        return true;
    }

    /**
     * Remove Particular Item on DB
     */
    public function removeItemFromTable($id)
    {
        $query = "SELECT COUNT(*) AS tot , a.item_name FROM items AS a, salesitems AS b WHERE b.item_id=a.item_id AND a.item_id IN ($id) GROUP BY a.item_id";
        $this->db->prepare($query);
        $this->db->execute();
        $item = $this->db->fetchAllAssociative();

        $item_name = [];
        foreach($item as $result){

            if(isset($result['tot']) && $result['tot'] > 0){
                    $item_name[] = $result['item_name'];
            }
        }
        if(count($item_name)>0){
            $list = implode(',', $item_name);
            return $list;
        }   

        $query = "DELETE FROM stockentry WHERE item_id IN ($id)";
        $this->db->prepare($query);
        $r1 = $this->db->execute();


        $query = "DELETE FROM items WHERE item_id IN ($id)";
        $this->db->prepare($query);
        $r2 = $this->db->execute();
        if($r1 && $r2){
            return true;
        }
        return false;
    
    }

 
    /**
     * Get user info from id
     */
    public function get_details($id)
    {
        $query = "SELECT a.* , b.category_name FROM items a, category b WHERE item_id = :id";
        $this->db->prepare($query);
        $this->db->bindValue(':id',$id);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        if(empty($result)){return null;}
        $data = [];
        
        $data['id'] = $result['id'];
        $data['item_id'] = $result['item_id'];
        $data['item_name'] = $result['item_name'];
        $data['category_id'] = $result['category_id'];
        $data['category_name'] = $result['category_name'];
        $data['unit_id'] = $result['unit_id'];
        $data['minimum_qty'] = $result['minimum_qty'];
        $data['expire_date'] = $result['expire_date'];
        $data['description'] = $result['description'];
        $data['purchase_price'] = $result['purchase_price'];
        $data['profit_margin'] = $result['profit_margin'];
        $data['sales_price'] = $result['sales_price'];
        $data['stock'] = $result['stock_qty'];

        return $data;   
    
    }

    public function updateItemsQuantity($item_id){
		
        //UPDATE items QUANTITY IN items TABLE

        $query = "SELECT COALESCE(SUM(quantity),0) AS stock_qty  FROM stockentry WHERE item_id='$item_id'";
        $this->db->prepare($query);
        $this->db->execute();
        $stock_qty = $this->db->fetchAssociative()['stock_qty'];

        //getting purchase quantity for item_id
        $query = "SELECT COALESCE(SUM(purchase_qty),0) AS purchase_qty FROM purchaseitems WHERE item_id='$item_id' AND purchase_status='received'";
        $this->db->prepare($query);
        $this->db->execute();
        $purchase_qty = $this->db->fetchAssociative()['purchase_qty'];

        //getting sales quanity for item_id
        $query = "SELECT COALESCE(SUM(sales_qty),0) AS sales_qty FROM salesitems WHERE item_id='$item_id' and sales_status='final'";
        $this->db->prepare($query);
        $this->db->execute();
        $sales_qty = $this->db->fetchAssociative()['sales_qty'];

        //getting purchase return quanity for item_id
        $query = "SELECT COALESCE(SUM(return_qty),0) AS purchase_return_qty FROM purchasereturnitems WHERE item_id='$item_id' ";/*and purchase_id is null */
        $this->db->prepare($query);
        $this->db->execute();
        $purchase_return_qty = $this->db->fetchAssociative()['purchase_return_qty'];

        // getting salese quanity for item_id
        $query  = "SELECT COALESCE(SUM(return_qty),0) AS sales_return_qty FROM salesreturnitems WHERE item_id='$item_id' ";/*and sales_id is null */
        $this->db->prepare($query);
        $this->db->execute();
        $sales_return_qty = $this->db->fetchAssociative()['sales_return_qty'];

        // 
        $stock = ((($stock_qty+$purchase_qty)-$sales_qty) + $sales_return_qty) - $purchase_return_qty;
        
		$query ="UPDATE items SET stock_qty=$stock WHERE item_id='$item_id'";
        $this->db->prepare($query);
        $result = $this->db->execute();
		if($result){
			return true;
		}
		else{
			return false;
		}
	}	

    /**
     * Delete Stock Entry
     */
    public function deleteStockEntry($entry_id, $item_id)
    {
        $query = "DELETE FROM stockentry WHERE id=$entry_id";
        $this->db->prepare($query);
        $result = $this->db->execute();
        if(!$result){
            return false;
        }
        
        $result = $this->updateItemsQuantity($item_id);
        if(!$result){
            return false;
        }

        return true;

    }

    /** 
     * Generate new Item id
     */
    public function createItemId()
    {
        //TODO PREFIX FOR  FROM SETTING OR SOMETHING
        $prefix = 'IT';
        //Create item unique Id
        $query = "SELECT COALESCE(MAX(id), 0)+1 AS maxid FROM ".$this->table;
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetchAssociative();
        
        $sales_id = $prefix.str_pad($result['maxid'],4,'0',STR_PAD_LEFT);
        
        return $sales_id;
     
    }
}