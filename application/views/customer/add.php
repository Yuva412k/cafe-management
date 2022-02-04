<?php 
use app\core\Session;

if(!isset($id)){
    $customer_name=$mobile=$customer_gstin=$opening_balance=$country=$state=$city=$pincode=$address='';
}
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Customer</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='customer-form' id='customer-form' class="validate-form" method="post" action="<?php echo PUBLIC_ROOT.'customer/addCustomer'?>">
                <div class="header">
                    <div class="item-pair">
                        <label for="cust_name">Customer Name <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input" data-validate="Customer Name is required">
                        <input class="req-input" type="text" name="cust_name" style="width: 100%;"  value="<?php echo $customer_name;?>" id="cust_name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="cust_id">Customer ID <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input" data-validate="Customer Id is required">
                        <input class="req-input" type="text" name="cust_id" style="width: 100%;" id="cust_id" value="<?=$customer_id; ?>" readonly>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="cust_mobile">Mobile</label>
                        <input type="text" name="cust_mobile" id="cust_mobile" class="number" value="<?php echo $mobile;?>">
                    </div>
                    <div class="item-pair">
                        <label for="cust_GST">GST Number</label>
                        <input type="text" name="cust_GST" id='cust_GST' value="<?php echo $customer_gstin;?>">
                    </div>

                    <div class="item-pair">
                        <label for="cust_balance">Opening Balence</label>
                        <input type="text" name="cust_balance" id="cust_balance" class="number" value="<?php echo $opening_balance?>">
                    </div>
                    <div class="item-pair">
                        <label for="cust_country">Country</label>
                        <select name="cust_country" id="cust_country" style="width: 60%">
                            <option value="">India</option>
                        </select>
                    </div>    
                    <div class="item-pair">
                        <label for="cust_state">State</label>
                        <select name="cust_state" id="cust_state" style="width: 60%">
                            <option value="puducherry">Puducherry</option>
                        </select>
                    </div>                
                    <div class="item-pair">
                        <label for="cust_city">City</label>
                        <input type="text" name="cust_city" id="cust_city"  value="<?php echo $city;?>">
                    </div>  
                    <div class="item-pair">
                        <label for="cust_pincode">Pincode</label>
                        <input type="text" name="cust_pincode"  id="cust_pincode"  value="<?php echo $pincode;?>">
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="cust_address">Address</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" name="cust_address" id="cust_address"> <?php echo $address;?></textarea>
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
                </div>

                <hr style="background-color: var(--border-color);">
                <br>
                <div class="btn-container">
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'customer' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>

<script src="<?php echo PUBLIC_ROOT.'js/customer.js'?>"></script>

    <script>
        $(document).ready(function() {
            $("#cust_country").select2();  
            $("#cust_state").select2();
        });
        
    </script>