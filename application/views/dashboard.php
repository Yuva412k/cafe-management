<?php

use app\core\Session;
?>
<section class="wrapper">
  <div class="content-wrapper">
        <div class="content-header">
            <h2>Dashboard</h2>
        </div>
    <!-- FLASH MESSAGE START -->
        <?php include_once APP.'views/common/flashdata.php'?>
    <!--FLASH MESSAGE END -->
        <div class="box-wrapper">
            <div class="box">
                <div class="box-icon">
                    <i class="fas fa-cart-plus" style="color:var(--icon-color1);"></i>
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_sales,2)?></strong></h3>
                    <span>Today Total Sales</span>
                </div>
            </div>
            <div class="box">
                <div class="box-icon">
                    <i class="fas fa-cart-arrow-down" style="color:var(--icon-color2);"></i>
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_sales_due,2)?></strong></h3>
                    <span>Today Sales Due</span>
                </div>
            </div>
            <div class="box">
                <div class="box-icon">
                    <i class="fas fa-shipping-fast" style="color:var(--icon-color3);"></i>
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_purchase,2)?></strong></h3>
                    <span>Today Total Purchase</span>
                </div>
            </div>

            <div class="box">
                <div class="box-icon">
                    <i class="fas fa-truck" style="color:var(--icon-color4);"></i>
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_purchase_due,2)?></strong></h3>
                    <span>Today Purchase Due</span>
                </div>
            </div>
        </div>
<br><br>
        <div class="content-container">
            <div class="myChart">
                <canvas id="barChart" height="300" width="500"></canvas>
            </div>


            <div class="myChart pie">
                <div id="pieChart"></div>
            </div>
    
    
            <div class="box-wrapper small">
                <div class="box" style="padding: 10px;height:50px"><h3>Daily Updates</h3></div>
                <div class="box">
                    <a href="#">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_purchase_count?></h1>
                            <span>Today Purchase Count</span>
                        </div>
                        <div class="box-icon"><i class="fas fa-truck" style="color:var(--icon-color4);"></i></div>
                    </a>
                </div>
                <div class="box">
                    <a href="#">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_sales_count?></h1>
                            <span>Today Sales Count</span>
                        </div>
                        <div class="box-icon"><i class="fas fa-shopping-bag" style="color:var(--icon-color3);"></i></div>
                    </a>
                </div>
                <div class="box">
                    <a href="#">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_customers?></h1>
                            <span>Total Customer count</span>
                        </div>
                        <div class="box-icon"><i class="fas fa-users" style="color:var(--icon-color2);"></i></div>
                    </a>
                </div>
                <div class="box" style="border: none;">
                    <a href="#">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_suppliers?></h1>
                            <span>Total Supplier count</span>
                        </div>
                        <div class="box-icon"><i class="fas fa-id-badge" style="color:var(--icon-color1);"></i></div>
                    </a>
                </div>
            </div>
            <div class="table-container exp-table">
                <div class="table-header">
                    <h3>Expired Products</h3>
                </div>
                <div class="table-content">
                    <table style="width: 100%" class="display table is-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Category Name</th>
                                <th>Expire Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        foreach($expired_items as $items){
                            echo "<tr>";
                            echo "<td>".$i++."</td>";
                            echo "<td>".$items['item_id']."</td>";
                            echo "<td>".$items['item_name']."</td>";
                            echo "<td>".$items['category_name']."</td>";
                            echo "<td>".$items['expire_date']."</td>";
                            echo "</tr>";
                        }
                       ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Recently Added Products</h3>
                </div>
                <div class="table-content">
                    <table id="mytable" class="table is-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Sales Price</th>
                            </tr>
                        </thead>
                        <tbody>
                       <?php 
                        $i = 1;
                        foreach($recently_add_items as $items){
                            echo "<tr>";
                            echo "<td>".$i++."</td>";
                            echo "<td>".$items['item_id']."</td>";
                            echo "<td>".$items['item_name']."</td>";
                            echo "<td>".number_format($items['sales_price'],2)."</td>";
                            echo "</tr>";
                        }
                       ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="4"><a href="<?php echo PUBLIC_ROOT.'item'?>" style="color:var(--text-color);font-weight:bolder;">View All</a></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
     
            <div class="table-container">
                <div class="table-header">
                    <h3>Stock Alert</h3>
                </div>
                <div class="table-content">
                    <table style="width: 100%" class="display table is-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Category Name</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        foreach($stock_alert as $items){
                            echo "<tr>";
                            echo "<td>".$i++."</td>";
                            echo "<td>".$items['item_id']."</td>";
                            echo "<td>".$items['item_name']."</td>";
                            echo "<td>".$items['category_name']."</td>";
                            echo "<td>".$items['stock_qty']."</td>";
                            echo "</tr>";
                        }
                       ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
  </div>
</section>
    
    <?php include_once APP.'views/common/common_js.php'?>

    <!-- Datatable -->
    <?php include_once APP.'views/common/datatable_js.php'?>

        <script>
            $(document).ready(function() {
                
                $('#mytable').DataTable( {
                    'sDom': 't',
                    "pageLength": 5,
                    "order": [[ 0, 'asc' ]]
                } );
                
                $('table.display').DataTable({
                    "pageLength": 5,
                    "order":[[0, 'asc']],
                    "sDom": 'tip',
                })
            });
        </script>

    <!-- Chart js and Pie Chart js -->
        <script src="<?php echo PUBLIC_ROOT.'Plugins/highcharts/highcharts.js'?>"></script>
        <script src="<?php echo PUBLIC_ROOT.'Plugins/highcharts/exporting.js'?>"></script>
        <script src="<?php echo PUBLIC_ROOT.'Plugins/highcharts/export-data.js'?>"></script>
        <script src="<?php echo PUBLIC_ROOT.'Plugins/highcharts/accessibility.js'?>"></script>
        <script src="<?php echo PUBLIC_ROOT.'Plugins/chart/chart.js'?>"></script>
    <?php 
    // chart data
    $purchase_data = [];

    foreach($purchase_chart_data as $res1){
        for($j=1; $j<=12; $j++){
            if($res1['purchase_date'] =="$j"){$purchase_data[$j] = number_format($res1['purchase_total'],2,'.','');}
        }
      }

      for($i=1;$i<=12;$i++){
          if(!isset($purchase_data[$i])){
              $purchase_data[$i] = 0;
          }
      }


      $sales_data = [];

      foreach($sales_chart_data as $res2){
          for($j=1; $j<=12; $j++){
              if($res2['sales_date'] =="$j"){$sales_data[$j] = number_format($res2['sales_total'],2,'.','');}
          }
        }
        for($i=1;$i<=12;$i++){
            if(!isset($sales_data[$i])){
                $sales_data[$i] = 0;
            }
        }

    ?>
      <script>
        $(document).ready(function () {
            var r = document.querySelector(':root');
            var rs = getComputedStyle(r);

            let months = ["Jan", "Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]
            const ctx = document.getElementById('barChart').getContext('2d');

            const labels = months;
            const data = {
            labels: labels,
            datasets: [
                {
                label: 'Sales',
                data: [
                    <?php
                    for($i=1;$i<=12;$i++){
                        echo $sales_data[$i].',';
                    }
                    ?>
                ],
                borderColor: "#f492a0",
                backgroundColor: "#f492a0",
                borderWidth: 2,
                borderRadius: {
                    topLeft: 50,
                    topRight: 50
                },
                borderSkipped: false,
                },
                {
                label: 'Purchase',
                data:[
                <?php
                    for($i=1;$i<=12;$i++){
                        echo $purchase_data[$i].',';
                    }
                ?>
                ],
               
                borderColor: "#9194ce",
                backgroundColor: "#9194ce",
                borderWidth: 1,
                borderRadius: {
                    topLeft: 50,
                    topRight: 50
                },
                borderSkipped: false,
                }
            ]
            };
            const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    fontSize: 15.5,
                    display: true,
                    text: 'Purchase And Sales',
                }
                }
            },
            };
            Chart.defaults.color = rs.getPropertyValue("--box-text");
            var myChart = new Chart(ctx, config);

            
        /****************PIE CHART******************/
    Highcharts.chart('pieChart', {
        chart: {
            backgroundColor: null,
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            height: '70%',
            type: 'pie'
        },
        title: {
            text: 'Top 10 Selling Products',
            style:{
                color: rs.getPropertyValue("--box-text"),
                fontSize: '15.5px',
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        exporting:{
            enabled: false
        },
        plotOptions: {
            pie: {
                height: '70%',
                size: '80%',
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: rs.getPropertyValue("--box-text"),
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                },
            }
        },
        series: [{
            name: 'Products',
            colorByPoint: true,
            data: [
                <?php
                    foreach($pie_chart_data as $data){
                        if($data['sales_qty']>0){
                            echo "{name:'".$data['item_name']."', y:".$data['sales_qty']."},";
                        }
                    }
                ?>

            ]
        }]
    });

    });
        </script> 
          <!-- Counter -->
          <?php include_once APP.'/views/common/counter_js.php'?>

      