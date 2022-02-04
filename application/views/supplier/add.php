<?php 
use app\core\Session;

if(!isset($id)){
    $supplier_name=$mobile=$supplier_gstin=$opening_balance=$country=$state=$city=$pincode=$address='';
}
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Supplier</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='supplier-form' class="validate-form" id='suppliers-form' method="post" action="<?php echo PUBLIC_ROOT.'supplier/addSupplier'?>">
                <div class="header">
                    <div class="item-pair">
                        <label for="supplier_name">Supplier Name <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input" data-validate="Supplier name is required">
                        <input type="text" class="req-input" name="supplier_name" style="width: 100%;" value="<?php echo $supplier_name;?>"  id="supplier_name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_id">Supplier ID <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Supplier id is required">
                        <input type="text" class="req-input" name="supplier_id" style="width: 100%;" id="supplier_id" readonly value="<?php echo $supplier_id;?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_mobile">Mobile</label>
                        <input type="text" name="supplier_mobile" id="supplier_mobile" class="number" value="<?php echo $mobile;?>">
                    </div>
                    <div class="item-pair">
                        <label for="supplier_GST">GST Number</label>
                        <input type="text" name="supplier_GST" id='supplier_GST' value="<?php echo $supplier_gstin;?>">
                    </div>

                    <div class="item-pair">
                        <label for="supplier_balance">Opening Balence</label>
                        <input type="text" name="supplier_balance" id="supplier_balance" class="number" value="<?php echo $opening_balance?>">
                    </div>
                    <div class="item-pair">
                        <label for="supplier_country">Country</label>
                        <select name="supplier_country" id="supplier_country" style="width: 60%">
                            <option value="">India</option>
                        </select>
                    </div>    
                    <div class="item-pair">
                        <label for="supplier_state">State</label>
                        <select name="supplier_state" id="supplier_state" style="width: 60%">
                            <option value="puducherry">Puducherry</option>
                        </select>
                    </div>                
                    <div class="item-pair">
                        <label for="supplier_city">City</label>
                        <input type="text" name="supplier_city" id="supplier_city" value="<?php echo $city;?>">
                    </div>  
                    <div class="item-pair">
                        <label for="supplier_pincode">Pincode</label>
                        <input type="text" name="supplier_pincode"  id="supplier_pincode" value="<?php echo $pincode;?>">
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="supplier_address">Address</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" name="supplier_address" id="supplier_address"><?php echo $address;?></textarea>
                    </div>
                    <div class="item-pair">
                        <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                </div>

                <?php
                      if(isset($id)){
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
                <hr style="background-color: var(--border-color);">
                <br>
                <div class="btn-container">
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'supplier' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>

<script src="<?php echo PUBLIC_ROOT.'js/supplier.js'?>"></script>

    <script>
        $(document).ready(function() {
            $("#supplier_country").select2();  
            $("#supplier_state").select2();
        });
        
    </script>