<?php 
use app\core\Session;
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
            <form  name='supplier-form' id='suppliers-form' method="post" action="<?php echo PUBLIC_ROOT.'supplier/addSupplier'?>">
                <div class="header">
                    <div class="item-pair">
                        <label for="supplier_name">Supplier Name <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="supplier_name" style="width: 100%;"  id="supplier_name" autofocus>
                        <span id="supplier_name_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_id">Supplier ID <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="supplier_id" style="width: 100%;" id="supplier_id" value="<?=$supplierid; ?>">
                        <span id="supplier_id_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_mobile">Mobile</label>
                        <input type="text" name="supplier_mobile" id="supplier_mobile" >
                    </div>
                    <div class="item-pair">
                        <label for="supplier_GST">GST Number</label>
                        <input type="text" name="supplier_GST" id='supplier_GST' value="">
                    </div>

                    <div class="item-pair">
                        <label for="supplier_balance">Opening Balence</label>
                        <input type="text" name="supplier_balance" id="supplier_balance">
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
                            <option value="">Puducherry</option>
                        </select>
                    </div>                
                    <div class="item-pair">
                        <label for="supplier_city">City</label>
                        <input type="text" name="supplier_city" id="supplier_city">
                    </div>  
                    <div class="item-pair">
                        <label for="supplier_pincode">Pincode</label>
                        <input type="text" name="supplier_pincode"  id="supplier_pincode">
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="supplier_address">Address</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" name="supplier_address" id="supplier_address"></textarea>
                    </div>
                    <div class="item-pair">
                        <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                </div>

                <hr style="color: #f4f4f4">
                <br>
                <div class="btn-container">
                    <div class="btn"><input type="submit" name='submit' id="save" value="Save"></div>
                    <div class="btn"><input type="submit" name='update' id="update" value="update"></div>
                    <div class="btn"><input type="reset" name='reset' value="Reset"></div>
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