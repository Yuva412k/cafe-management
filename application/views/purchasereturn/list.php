<?php use app\core\Session;?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Purchase Return</h2>    
        </div>

        <!-- FLASH MESSAGE START -->
        <?php include_once APP.'views/common/flashdata.php'; ?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
        <div class="table-header">
            <a href="#" id="delete_record" class="icons"><i class="fas fa-trash-alt"></i></a>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT;?>">

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
            <table id="purchasereturn_list" class="table is-striped table-hover" style="width: 100%">
                <thead>
                    <tr>
                       <th><input type="checkbox" name="" id="checkall"></th>
                       <th>Return Date</th>
                       <th>Purchase ID</th>
                       <th>Return ID</th>
                       <th>Purchase Status</th>
                       <th>Reference No</th>
                       <th>Supplier Name</th>
                       <th>Total Amount</th>
                       <th>Paid Amount</th>
                       <th>Return Due</th>
                       <th>Payment Status</th>
                       <th>Created By</th>
                       <th>Action</th>
                   </tr>
                </thead>
                <tbody>
       
                </tbody>
            </table>
        </div>
    </div>
    <div class="view_payments_modal">

</section>
<input type="hidden" id="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />

<?php include_once APP."views/common/common_js.php"?>

<?php include_once APP."views/common/datatable_js.php"?>
<?php include_once APP."views/common/datatable_btn_js.php"?>

<script src="<?php echo PUBLIC_ROOT.'Plugins/autocomplete/autocomplete.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery-ui/jquery-ui.min.js'?>"></script>

<script src="<?php echo PUBLIC_ROOT.'js/purchasereturn.js'?>"></script>

<!-- Modal -->
<script>
    
    function showModal(){
      // Get the modal
      var modal = document.getElementById("view_modal");
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
      var modal = document.getElementById("view_modal");
        modal.style.display = "none";
    }
    
    
        </script>
    <script>
         function loadTable(){
          const table = $('#purchasereturn_list').DataTable({
                'sDom': '<"top"l>t<"bottom"ip>',
                "pageLength": 10,
                "order": [[ 1, 'dec' ]],
                buttons: true,
                serverSide : true,
                'processing' :true, //Feautre control the processing indicator
                // AJAX LOAD DATA FOR THE TABLE
                'ajax' : {
                    'url': "<?php echo PUBLIC_ROOT.'purchaseReturn/ajaxList'?>",
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
            $("#sort").click(function(){
                $("#sort-opt").empty()
                let i= 1;
                var n = $('#purchasereturn_list thead th').length;
                for(i = 1; i<n; i++){
                    let title = table.column(i).header().innerHTML
                    let element = $("<li></li>").text(title.replace("#",""))
                    $("#sort-opt").append(element.attr("class","srt s"+i))
                    table.order.listener(".srt.s"+i, i)
                }
            });
            $('#table_search').keyup(function(){
                table.search($(this).val()).draw() ;
            })

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
