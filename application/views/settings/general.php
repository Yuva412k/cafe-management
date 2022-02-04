<?php 
if(!isset($shop_name)){
    $shop_name=$shop_mobile=$shop_phone=$invoice_footer=$shop_email=$shop_gstin=$shop_city=$shop_state=$shop_shop_address=$shop_pincode='';
}
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
            <form  name='general-form' class="validate-form" id='general-form'>
                <div class="header">
                   <ul class="setting-menu">
                       <li><a href="<?php echo PUBLIC_ROOT.'settings';?>" class="active">General</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/prefix';?>">Prefix</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/others';?>">Others</a></li>
                   </ul>
                </div>
                <br>
                <div class="box">
                    <div class="row">
                    <div class="row">
                        <h3>Theme</h3>
                    </div>
                    <?php 
                            $theme1 = '';
                            $theme2 = '';
                            $theme3 = '';
                        if(isset($theme)){
                          
                            if($theme == 1){
                                $theme1 = "checked";
                            }else if($theme == 2){
                                $theme2 = "checked";
                            }else if($theme == 3){
                                $theme3 = "checked";
                            }

                            }?>
                        <div style="width:100%;display: flex;" class="" >
                            <label style="padding:10px;" for="theme1"><input type="radio" name="theme" id="theme1" <?php echo $theme1 ?> value="1"> Dark</label>
                            <label style="padding:10px;" for="theme2"><input type="radio" name="theme" id="theme2" <?php echo $theme2 ?> value="2"> Light</label>
                            <label style="padding:10px;" for="theme3"><input type="radio" name="theme" id="theme3" <?php echo $theme3 ?> value="3"> Theme3</label>
                        </div>
                    </div>
<br>
                    <div class="row">
                        <div class="row">
                            <h3>Shop Details</h3>
                        </div>
                        <div class="row" style="display: flex;flex-wrap: wrap;">
                    <div class="item-pair">
                        <label for="show_name">Shop Name<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Shop name is required">
                        <input type="text" class="req-input" name="shop_name" style="width: 100%;"  id="shop_name" value="<?php echo $shop_name?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="shop_mobile">Shop Mobile<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Shop Mobile is required">
                        <input type="text" class="req-input" name="shop_mobile" style="width: 100%;"  id="shop_mobile" value="<?php echo $shop_mobile;?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="shop_phone">Shop Phone<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="shop_phone no is required">
                        <input type="text" class="req-input" name="shop_phone" style="width: 100%;"  id="shop_phone"  value="<?php print $shop_phone ?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="shop_email">Shop Email<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="shop_email name is required">
                        <input type="text" class="req-input" name="shop_email" style="width: 100%;"  id="shop_email" value="<?php echo $shop_email?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="shop_gstin">Gst No<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="shop_gstin name is required">
                        <input type="text" class="req-input" name="shop_gstin" style="width: 100%;"  id="shop_gstin" value="<?php echo $shop_gstin?>" >
                        </div>
                    </div>

                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="shop_address">Shop Address</label>
                        <textarea style="width: 60%;height:50px;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" name="shop_address" id="shop_address" ><?php echo $shop_address;?></textarea>
                    </div>
                    <div class="item-pair">
                        <label for="shop_state">State</label>
                        <select name="shop_state" class="select" id="shop_state" style="width: 60%">
                            <option value="puducherry">Puducherry</option>
                        </select>
                    </div>                
                    <div class="item-pair">
                        <label for="shop_city">City</label>
                        <input type="text" name="shop_city" id="shop_city"  value="<?php echo $shop_city;?>">
                    </div>  
                    <div class="item-pair">
                        <label for="shop_pincode">Pincode</label>
                        <input type="text" name="shop_pincode"  id="shop_pincode"  value="<?php echo $shop_pincode;?>">
                    </div>
                    <div class="item-pair"></div>
                </div>
                <br>
                <div class="row">
                        <h3>Default Invoice</h3>
                    </div>
                        <div style="width:100%;display: flex;" class="" >
                        <?php 
                            $invoice1 = '';
                            $invoice2 = '';
                        if(isset($invoice)){
                          
                            if($invoice == 1){
                                $invoice1 = "checked";
                            }else if($invoice == 2){
                                $invoice2 = "checked";
                            }

                            }?>
                            <label style="padding:10px;" for="invoice1"><input type="radio" name="invoice" id="invoice1" <?php echo $invoice1?> value="1"> Invoice 1</label>
                            <label style="padding:10px;" for="invoice2"><input type="radio" name="invoice" id="incoice2" <?php echo $invoice2?> value="2"> Invoice 2</label>
                        </div>
                    </div>
                <br>
                <div class="row">
                        <h3>Invoice Footer</h3>
                    </div>
                    <div class="row">
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="invoice_footer">Terms And Policy</label>
                        <textarea style="width: 60%;height:100px;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" name="invoice_footer" id="invoice_footer" ><?php echo $invoice_footer;?></textarea>
                    </div>
                </div>
                <br>
                    <div class="" style="text-align:right;">
                    <div class="btn"><input type="submit" name='submit' id="save" value="Save"></div>
                </div>
                <br><br>
               
            </form>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">

        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 


<script>
    $('.select').select2();


$('#save').click(function (e) {

e.preventDefault();

if(!validateForm()){
    toastr["warning"]("Please Fill Required Fields!");
    return;
}

let base_url=$("#baseURL").val().trim();
//Initially flag set true
let flag=true;

var this_id=this.id;

if(this_id=="save")  //Save start
{
 if(confirm("Do You Wants to Save Record ?")){
    e.preventDefault();
    data = new FormData($('#general-form')[0]);//form name

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
    $.ajax({
    type: 'POST',
    url: base_url+'settings/updateGeneral',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    success: function(result){

        if(result=="success")
        {
            window.location=base_url+"settings";
            return;
        }
        else if(result=="failed")
        {
            toastr["error"]("Sorry! Failed to save Record.Try again!");
        }
        else
        {
            toastr["error"](result);
        }
        $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
    }
    });
}

}
});
</script>