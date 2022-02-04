<?php 
use app\core\Session;

if(!isset($tax_name)){
    $tax_name=$tax_description=$tax="";
}
?> 

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Tax</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='tax-form' class="validate-form" id='tax-form'>
                <div class="header">
                    <div class="item-pair">
                        <label for="tax_name">Tax Name <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Tax name is required">
                        <input type="text" class="req-input" name="tax_name" style="width: 100%;" value="<?php print $tax_name; ?>"  id="tax_name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="item_id">Tax ID <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Tax id is required">
                        <input type="text" class="req-input" name="tax_id" style="width: 100%;" id="tax_id" readonly value="<?php print $tax_id; ?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="tax">Tax Value<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Tax value is required">
                        <input type="text" class="req-input number" name="tax" style="width: 100%;" value="<?php print $tax_name; ?>"  id="tax">
                        </div>
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="tax_description">Description</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="tax_description" name="tax_description"><?php print $tax_description; ?></textarea>
                    </div>     
                    
                    <div class="item-pair">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                    <?php 
                        if($tax_name != ""){
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
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'tax' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">

        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 

<script src="<?php echo PUBLIC_ROOT.'js/tax.js'?>"></script>

