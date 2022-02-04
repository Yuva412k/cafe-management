<?php 
use app\core\Session;

if(!isset($paymenttype_name)){
    $paymenttype_name=$paymenttype_description="";
}
?> 

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Payment Types</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='paymenttype-form' class="validate-form" id='paymenttype-form'>
                <div class="header">
                    <div class="item-pair">
                        <label for="paymenttype_name">Payment Type Name <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Payment Type name is required">
                        <input type="text" class="req-input" name="paymenttype_name" style="width: 100%;" value="<?php print $paymenttype_name; ?>"  id="paymenttype_name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="item_id">Payment Type ID <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Payment Type id is required">
                        <input type="text" class="req-input" name="paymenttype_id" style="width: 100%;" id="paymenttype_id" readonly value="<?php print $paymenttype_id; ?>">
                        </div>
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="paymenttype_description">Description</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="paymenttype_description" name="paymenttype_description">&nbsp;<?php print $paymenttype_description; ?></textarea>
                    </div>     
                    
                    <div class="item-pair">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                    <?php 
                        if($paymenttype_name != ""){
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
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'paymenttype' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">

        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 

<script src="<?php echo PUBLIC_ROOT.'js/paymenttype.js'?>"></script>
