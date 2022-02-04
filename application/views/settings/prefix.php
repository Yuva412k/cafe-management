<?php 
if(!isset($item_prefix)){
    $item_prefix=$customer_prefix=$category_prefix=$supplier_prefix=$sales_prefix=$sales_return_prefix=$purchase_prefix=$purchase_return_prefix=$unit_prefix=$tax_prefix=$unit_prefix=$paymenttype_prefix="";
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
            <form  name='prefix-form' class="validate-form" id='prefix-form'>
                <div class="header">
                   <ul class="setting-menu">
                       <li><a href="<?php echo PUBLIC_ROOT.'settings';?>" >General</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/prefix';?>" class="active">Prefix</a></li>
                       <li><a href="<?php echo PUBLIC_ROOT.'settings/others';?>">Others</a></li>
                   </ul>
                </div>
                <br>
                <div class="box">
                    <div class="row">
                        <br>
                    <div class="row">
                        <div class="row" style="display: flex;flex-wrap: wrap;">
                    <div class="item-pair">
                        <label for="item_prefix">Products <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Product prefix is required">
                        <input type="text" class="req-input" name="item_prefix" style="width: 100%;"  id="item_prefix" value="<?php echo $item_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="customer_prefix">Customer <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Customer prefix is required">
                        <input type="text" class="req-input" name="customer_prefix" style="width: 100%;"  id="customer_prefix" value="<?php echo $customer_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="category_prefix">Category <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Category prefix is required">
                        <input type="text" class="req-input" name="category_prefix" style="width: 100%;"  id="category_prefix" value="<?php echo $category_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="sales_prefix">Sales <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Sales prefix is required">
                        <input type="text" class="req-input" name="sales_prefix" style="width: 100%;"  id="sales_prefix" value="<?php echo $sales_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="sales_return_prefix">Sales Return <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Sales Return prefix is required">
                        <input type="text" class="req-input" name="sales_return_prefix" style="width: 100%;"  id="sales_return_prefix" value="<?php echo $sales_return_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="purchase_prefix">Purchase <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Purchase prefix is required">
                        <input type="text" class="req-input" name="purchase_prefix" style="width: 100%;"  id="purchase_prefix" value="<?php echo $purchase_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="purchase_return_prefix">Purchase Return<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Purchase Return prefix is required">
                        <input type="text" class="req-input" name="purchase_return_prefix" style="width: 100%;"  id="purchase_return_prefix" value="<?php echo $purchase_return_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_prefix">Supplier <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Supplier prefix is required">
                        <input type="text" class="req-input" name="supplier_prefix" style="width: 100%;"  id="supplier_prefix" value="<?php echo $supplier_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="paymenttype_prefix">Payment Type <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Payment Type is required">
                        <input type="text" class="req-input" name="paymenttype_prefix" style="width: 100%;"  id="paymenttype_prefix" value="<?php echo $paymenttype_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="tax_prefix">Tax <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Tax prefix is required">
                        <input type="text" class="req-input" name="tax_prefix" style="width: 100%;"  id="tax_prefix" value="<?php echo $tax_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="unit_prefix">Unit <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="Unit prefix is required">
                        <input type="text" class="req-input" name="unit_prefix" style="width: 100%;"  id="unit_prefix" value="<?php echo $unit_prefix?>">
                        </div>
                    </div>
                    <div class="item-pair"></div>
                </div>
                <br>
                    <div class="" style="float: right;">
                    <div class="btn"><input type="submit" id="save" value="Save"></div>
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
    data = new FormData($('#prefix-form')[0]);//form name

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
    $.ajax({
    type: 'POST',
    url: base_url+'settings/UpdatePrefix',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    success: function(result){

        if(result=="success")
        {
            window.location=base_url+"settings/prefix";
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