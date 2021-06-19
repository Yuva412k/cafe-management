<?php 
use app\core\Session;

if(!isset($item_name)){
    $item_name=$category_id=$unit_id=$minimum_qty=$expire_date=$description=$purchase_price=$profit_margin=$sales_price=$stock="";
    $category_name='';
    $new_opening_stock = 0;
}

if(isset($result)){
    extract($result);
}
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Items</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='item-form' id='items-form' method="post" action="<?php echo PUBLIC_ROOT.'item/additem'?>">
                <div class="header">
                    <div class="item-pair">
                        <label for="item_name">Item Name <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="item_name" style="width: 100%;"  id="item_name" value="<?php print $item_name;?>" autofocus>
                        <span id="item_name_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="item_id">Item ID <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="item_id" style="width: 100%;" id="item_id" value="<?php print $item_id ?>">
                        <span id="item_id_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="category_id">Category<sup style="color: red">*</sup></label>
                        <div style="width:60%">
                            <select name="category_id" style="width: 100%;" id="category_id">
                                <?php
                                 if(isset($category_id)){
                                     
                                     echo '<option selected value="'.$category_id.'">'.$category_name.'</option>';
                                 }
                                 ?>
                            </select>
                            <span id="category_id_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="item_unit">Unit <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                            <select name="unit_id" style="width: 100%;" id="unit_id">
                                <option></option>
                                <?php 
                                    if(isset($unitData) && !empty($unitData)){
                                        foreach($unitData as $data){
                                            $selected = ($data['unit_id'] == $unit_id) ? 'selected' : '';
                                            echo '<option '.$selected.' value="'.$data['unit_id'].'">'.$data['unit_name'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                            <span id="unit_id_msg" class='required'></span>
                        </div> 
                    </div>

                    <div class="item-pair">
                        <label for="minimum_qty">Minimum Qty.</label>
                        <input type="text" name="minimum_qty" id="minimum_qty" value="<?php print $minimum_qty;?>">
                    </div>
                    <div class="item-pair">
                        <label for="expire_date">Expire Date</label>
                        <input type="date" name="expire_date" id="expire_date" value="<?php print $expire_date;?>">
                    </div>                 
                    <div class="item-pair">
                        <label for="purchase_price">Purchase Price<sup style="color: red">*</sup></label>
                        <div style="width:60%">
                            <input type="text" style="width: 100%;" name="purchase_price"  id="purchase_price" value="<?php print $purchase_price;?>">
                            <span id="purchase_price_msg" class='required'></span>
                        </div>  
                    </div>
                    <div class="item-pair">
                        <label for="profit_margin">Profit Margin(%)</label>
                        <input type="text" name="profit_margin" id="profit_margin" value="<?php print $profit_margin;?>">
                    </div>  
                    <div class="item-pair">
                        <label for="sales_price">Sales Price<sup style="color: red">*</sup></label>
                        <div style="width:60%">
                            <input type="text" style="width: 100%;" name="sales_price" id="sales_price" value="<?php print $sales_price;?>" >
                            <span id="sales_price_msg" class='required'></span>
                        </div>
                    </div>  
                    <div class="item-pair">
                        <label for="final_price">Final Price<sup style="color: red">*</sup></label>
                        <input type="text" name="final_price" id="final_price" value="<?php print $sales_price;?>">
                    </div>  
                    
                    <div class="item-pair">
                        <label for="stock_qty">Current Opening Stock</label>
                        <input type="text" name="stock_qty" id="stock_qty" value="<?php print $stock;?>">
                    </div>  
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="description">Description</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="description" name="description"><?php print $description;?></textarea>
                    </div>      
                    <div class="item-pair">
                        <label for="new_opening_stock">Adjust Stock(+/-)</label>
                        <input type="text" name="new_opening_stock" placeholder="Example: +10 "id="new_opening_stock" >
                    </div>  
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="opening_stock_description">Adjustment Note</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="opening_stock_description" name="opening_stock_description" ></textarea>
                    </div>     
                    
                    <div class="item-pair">
                        <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                </div>
                <?php 
                        if($item_name != ""){
                            $btn_name = 'Update';
                            $btn_id = 'update';
                        ?>
                        <input type='hidden' name='id' value='<?php echo $id;?>'>
                        <?php 
                        }
                        else{
                            $btn_id='save';
                            $btn_name='Save';
                        }
                        ?>
                </div>

                <hr style="color: #f4f4f4">
                <br>
                <div class="btn-container">
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                    <div class="btn"><input type="reset" name='reset' value="Reset"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>

<script src="<?php echo PUBLIC_ROOT.'js/items.js'?>"></script>

    <script>
        $(document).ready(function() {
            $("#category_id").select2({
                placeholder : 'Select Category',
                ajax:{
                    url:'categoryAjax',
                    type: 'POST',
                    delay: 250,
                    data: function (data){
                        return {
                            searchTerm: data.term //search term
                        };
                    },

                    processResults: function(data){
                        return {
                            results : JSON.parse(data) ,
                        };
                    },
                    cache : true
                } 
            });  
            $("#unit_id").select2({
                placeholder: "Select Unit"
            });
            $("#tax_id").select2({
                placeholder: "Select Tax"
            });
            $("#tax_type").select2({
                placeholder: "Select Tax Type"
            });
        });
        
    </script>