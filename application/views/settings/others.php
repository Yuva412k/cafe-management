<?php 
$shop_name=$shop_mobile=$phone=$email=$gstin=$city=$state=$address=$pincode='';
?>
<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Settings</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <!-- <?php include_once APP.'views/common/flashdata.php'?> -->
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container" id="settings" style="color:var(--text-color);">
            <div>
                <div class="header">
                <ul class="setting-menu">
                       <li><a href="<?php echo PUBLIC_ROOT.'settings';?>">General</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/prefix';?>">Prefix</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/others';?>" class="active">Others</a></li>
                   </ul>

                </div>
                <br>
                <br>
                <div class="row">
                    <div class="row">
                    <div class="row">
                        <h3>Taxes</h3>
                    </div><br>
                    <div class="row" style="display: flex;">
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'tax/add'?>">Add Tax</a>
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'tax'?>">Tax List</a>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="row">
                        <h3>Units</h3>
                    </div><br>
                    <div class="row" style="display: flex;">
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'unit/add'?>">Add Unit</a>
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'unit'?>">Units List</a>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="row">
                        <h3>Payment Types</h3>
                    </div><br>
                    <div class="row" style="display: flex;">
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'paymenttype/add'?>">Add Payment Type</a>
                        <a class="btn btn-outline" href="<?php echo PUBLIC_ROOT.'paymenttype'?>">Payment Types List</a>
                    </div>
                </div>
                </div>
        </div>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">

        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 

 