<?php
use app\core\Session;
?>
<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Items List</h2>    
        </div>

        <!-- FLASH MESSAGE START -->
        <?php include_once APP.'views/common/flashdata.php'; ?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <div class="table-header">
            <input type="button" value="Delete" id="delete_record">

                <div class="dropdown" id="sort-container">Sort </span>
                    <ul id="sort-opt" class="dropdown-menu"></ul>
                </div>
                <div class="dropdown">
                    <i id="icon-pointer"> icon</i>
                    <ul class="dropdown-menu">
                        <li class="menu-header">Export</li>
                        <li class="expToExcel">Excel</li>
                        <li class="expToCSV">CSV</li>
                        <li class="expToPDF">PDF</li>
                        <li class="menu-header">Additional</li>
                        <li class="print">Print</li>
                        <li>Settings</li>
                    </ul>
                </div>
                <div class="search-container">
                    <i>icon</i><input type="text" id="table_search">
                </div>
            </div>
            <table id="item_list" class="table is-striped" style="width: 100%">
                <thead>
                    <tr>
                       <th><input type="checkbox" name="" id="checkall"></th>
                       <th>Item Id</th>
                       <th>Item Name</th>
                       <th>Category</th>
                       <th>Unit</th>
                       <th>Stock Qty.</th>
                       <th>Minimum Qty.</th>
                       <th>Purchase Price</th>
                       <th>Expire Date</th>
                       <th>Profit Margin(%)</th>
                       <th>Sales Price</th>
                       <th>Action</th>
                   </tr>
                </thead>
                <tbody>
       
                </tbody>
            </table>
        </div>
    </div>
    <input type="hidden" id="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
</section>
<?php include_once APP."views/common/common_js.php"?>

<?php include_once APP."views/common/datatable_js.php"?>
<?php include_once APP."views/common/datatable_btn_js.php"?>

<script src="<?php echo PUBLIC_ROOT.'js/items.js'?>"></script> 
 
    <script>
         function loadTable(){
          const table = $('#item_list').DataTable({
                'sDom': '<"top"l>t<"bottom"ip>',
                "pageLength": 10,
                "order": [[ 1, 'dec' ]],
                buttons: true,
                "scrollX": true,
                'responsive' : true,
                serverSide : true,
                'processing' :true, //Feautre control the processing indicator
                // AJAX LOAD DATA FOR THE TABLE
                'ajax' : {
                    'url': "<?php echo PUBLIC_ROOT.'item/ajaxList'?>",
                    'type': 'POST',
                },
                'columnDefs' : [
                    {
                        "targets" : [0, -1],
                        "orderable": false,
                    },
                ],
            });
            $(".expToExcel").on("click", function() {
                table.button( '.buttons-excel' ).trigger();
            });
            $(".expToPDF").on("click", function() {
                table.button( '.buttons-pdf' ).trigger();
            });
            $(".expToCSV").on("click", function() {
                table.button( '.buttons-csv' ).trigger();
            });
            $(".print").on("click", function() {
                table.button( '.buttons-print' ).trigger();
            });
            $(".sort").click(function(){
                $("#sort-opt").empty()
                let i= 1;
                var n = $('#item_list thead th').length;
                for(i = 1; i<n; i++){
                    let title = table.column(i).header().innerHTML
                    let element = $("<li></li>").text(title.replace("#",""))
                    $("#sort-opt").append(element.attr("class","srt s"+i))
                    table.order.listener(".srt.s"+i, i)
                }
            });
        // Check all 
        $('#checkall').click(function(){
            if($(this).is(':checked')){
                $('.row_check').prop('checked', true);
            }else{
                $('.row_check').prop('checked', false);
            }
        });
            $('#table_search').keyup(function(){
                table.search($(this).val()).draw() ;
            })
        }

        $(document).ready(function(){
            loadTable()
        });
    </script>
