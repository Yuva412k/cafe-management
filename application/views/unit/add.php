<?php 
use app\core\Session;

if(!isset($unit_name)){
    $unit_name=$unit_description="";
}
?> 

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Units</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='unit-form' id='unit-form'>
                <div class="header">
                    <div class="item-pair">
                        <label for="unit_name">Unit Name <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="unit_name" style="width: 100%;" value="<?php print $unit_name; ?>"  id="unit_name" autofocus>
                        <span id="unit_name_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="item_id">Unit ID <sup style="color: red">*</sup></label>
                        <div style="width:60%">
                        <input type="text" name="unit_id" style="width: 100%;" id="unit_id" value="<?php print $unit_id; ?>">
                        <span id="unit_id_msg" class='required'></span>
                        </div>
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="unit_description">Description</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="unit_description" name="unit_description"><?php print $unit_description; ?></textarea>
                    </div>     
                    
                    <div class="item-pair">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                    <?php 
                        if($unit_name != ""){
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
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">

        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 

<script src="<?php echo PUBLIC_ROOT.'js/unit.js'?>"></script>
