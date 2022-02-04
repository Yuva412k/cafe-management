
<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Stock Report</h2>
        </div>
        <div class="wrapper-container">
            <div class="box-container" style="width: 100%;">
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
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Unit Price</th>
                                <th>Sales Price</th>
                                <th>Current Stock</th>
                                <th>Stock Value</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyid">

                        </tbody>
                    </table>
                </div>
        </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>
<script>
    $(document).ready(function(){
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post("showStockReport",{},function(result){
          //alert(result);
            setTimeout(function() {
             $("#tbodyid").empty().append(result);     
             $(".overlay").remove();
            }, 0);
           }); 
      
    });
</script>