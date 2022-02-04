
<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Customer List</h2>    
        </div>

        <!-- FLASH MESSAGE START -->
        <?php include_once APP.'views/common/flashdata.php'; ?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <div class="table-header" id="cust-list">
            <a href="#" id="delete_record" class="icons"><i class="fas fa-trash-alt"></i></a>

            <div class="dropdown" id="sort-container">
                <a href="#" id="sort" class="icons"><i class="fas fa-sort-alpha-up"></i></a>
                <ul id="sort-opt" class="dropdown-menu"></ul>
            </div>
            <div class="dropdown">
                <a href="#" class="icons"><i class="fas fa-bars"></i></a>
                <ul class="dropdown-menu" id="export-menu">
                    <li class="menu-header" style="padding: 5px 8px;color:var(--box-text);font-weight:bolder">Export</li>
                    <li class="expToExcel"><a href="#">Excel</a></li>
                    <li class="expToCSV"><a href="#">CSV</a></li>
                    <li class="expToPDF"><a href="#">PDF</a></li>
                    <li class="menu-header"  style="padding: 5px 8px;color:var(--box-text);font-weight:bolder">Additional</li>
                    <li class="print"><a href="#">Print</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </div>
            <div class="search-container">
                <i class="fas fa-search icons" style="margin:0;padding: 6px 8px;"></i><input type="text" id="table_search">
            </div>
            </div>
            <table id="cust_list" class="table is-striped table-hover" style="width: 100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkall"></th>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Gstin</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>state</th>
                        <th>Sales Due</th>
                        <th>Sales Return Due</th>
                        <th>Opening Balance</th>
                        <th>Action</th>
                   </tr>
                </thead>
                <tbody>
           
                </tbody>
            </table>
        </div>
    </div>
    <div class="pay_now_modal"></div>
</section>
<input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT;?>">

    <?php include_once APP."views/common/common_js.php"?>

    <?php include_once APP."views/common/datatable_js.php"?>
    <?php include_once APP."views/common/datatable_btn_js.php"?>

    <script src="<?php echo PUBLIC_ROOT.'js/customer.js'?>"></script>
    <!-- Modal -->
    <script>
    
function showModal(){
  // Get the modal
  var modal = document.getElementById("pay_now");
  modal.style.display = "block";
// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
} 
}
function hideModal(){
  var modal = document.getElementById("pay_now");
    modal.style.display = "none";
}


    </script>
    <script>
        function loadTable(){
            const table = $('#cust_list').DataTable( {
                'sDom': '<"top"l>t<"bottom"ip>',
                "pageLength": 10,
                "order": [[ 1, 'dec' ]],
                buttons: true,
                
                'responsive' : true,
                serverSide : true,
                'processing' :true, //Feautre control the processing indicator
                // AJAX LOAD DATA FOR THE TABLE
                'ajax' : {
                    'url': "<?php echo PUBLIC_ROOT.'customer/ajaxList'?>",
                    'type': 'POST',
                },
                'columnDefs' : [
                    {
                        "targets" : [0, -1],
                        "orderable": false,
                    },
                ],
            });
            
            /** EXPORT BUTTON AND LISTENER START */
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
            /** EXPORT BUTTON AND LISTENER END */

            /** SORT BUTTON AND LISTENER START */

            $("#sort").click(function(){
                $("#sort-opt").empty()
                let i= 1;
                var n = $('#cust_list thead th').length;
                for(i = 1; i<n; i++){
                    let title = table.column(i).header().innerHTML
                    let element = $("<li></li>").text(title.replace("#",""))
                    $("#sort-opt").append(element.attr("class","srt s"+i))
                    table.order.listener(".srt.s"+i, i)
                }
            });

            /** SORT BUTTON AND LISTENER END */

            /** TABLE SEARCH START */
            $('#table_search').keyup(function(){
                table.search($(this).val()).draw();
            });
            /** TABLE SEARCH END */

            $('#checkall').click(function(){
                
                if($(this).is(':checked')){
                    $('.row_check').prop('checked', true);
                }else{
                    $('.row_check').prop('checked', false);
                }
            });

        }

        $(document).ready(function(){
            loadTable()
        });
    </script>
