<?php

?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Purchase Report</h2>
        </div>
        <div class="wrapper-container">
            <div class="box-container" style="width: 100%;">
            <form id="purchase-form" class="validate-form" enctype="multipart/form-data">
                <input type="hidden" name="hidden_rowcount" id="hidden_rowcount" value="1">
                <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT;?>">
                <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">

                <div class="header">
                    
                    <div class="item-pair">
                        <label for="from_date">From<sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="From date is required">
                        <input type="date" class="req-input" style="width:100%" name="from_date" id="from_date" >
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="from_date">To<sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="To date is required">
                        <input type="date" class="req-input" style="width:100%" name="to_date" id="to_date" >
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="supplier_id">Supplier Name <sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Supplier Name is required">
                        <select class="req-input" name="supplier_id" id="supplier_id" style="width: 100%;">
                        <option value="all">--All--</option>
                        </select>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="payment_status">Payment Status <sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Purchase status is required">
                        <select class="select req-input" style="width: 100%;" name="payment_status" id="payment_status">
                            <option value="all">--All--</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                        </div>
                    </div>
                    <div class="btn-container">
                        <div class="btn"><a href="<?php echo PUBLIC_ROOT ?>" id='button'>Cancel</a></div>
                        <div class="btn"><input type="submit" name='submit' id="show" value="Show"></div>
                    </div>
            </form>
        </div>
                   
                </div>
                <br>
                <hr style="background-color: var(--border-color);">
                <br>
                
                <div class="tb-h10">
                    <table style="width: 100%;text-align:center" id="report_table">
                        <thead>
                            <tr style="text-align:center">
                                <th>#</th>
                                <th>Purchase Id</th>
                                <th>Purchase Date</th>
                                <th>Supplier Id</th>
                                <th>Supplier Name</th>
                                <th>Total</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyid">

                        </tbody>
                    </table>
                </div>
        </div>
</section>

<?php include_once APP.'views/common/common_js.php'?>
<script src="<?php echo PUBLIC_ROOT.'Plugins/autocomplete/autocomplete.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery-ui/jquery-ui.min.js'?>"></script>
<script>
  
      $(document).ready(function() {
          base_url = $('#baseURL').val();
        $("#supplier_id").select2({
            ajax:{
                url: base_url+'/purchase/supplierAjax',
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
        $(".select").select2();
    });
   
</script>
<script>
    $("#show").click(function(e){
	e.preventDefault();
	
    var from_date=document.getElementById("from_date").value.trim();
    var to_date=document.getElementById("to_date").value.trim();
    var supplier_id=document.getElementById("supplier_id").value.trim();
    var payment_status = document.getElementById("payment_status").value.trim();

	  
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post("showPurchaseReport",{supplier_id:supplier_id,payment_status:payment_status,from_date:from_date,to_date:to_date},function(result){
          //alert(result);
            setTimeout(function() {
             $("#tbodyid").empty().append(result);     
             $(".overlay").remove();
            }, 0);
           }); 
      
	
});


</script>
