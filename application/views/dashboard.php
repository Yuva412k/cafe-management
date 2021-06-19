<?php

use app\application\controllers\Purchase;
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
            <div class="box" style="background-color: #f492a0;">
                <div class="box-icon">
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_sales,2)?></strong></h3>
                    <span>Today Total Sales</span>
                </div>
            </div>
            <div class="box" style="background-color: #feb161">
                <div class="box-icon">
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_purchase,2)?></strong></h3>
                    <span>Today Total Purchase</span>
                </div>
            </div>
            <div class="box" style="background-color:#9194ce">
                <div class="box-icon">
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_sales_due,2)?></strong></h3>
                    <span>Today Sales Due</span>
                </div>
            </div>
            <div class="box" style="background-color:#3e479b">
                <div class="box-icon">
                </div>
                <div class="box-content">
                    <h3>₹<strong class="counter"><?=number_format($today_total_purchase_due,2)?></strong></h3>
                    <span>Today Purchase Due</span>
                </div>
            </div>
        </div>

        <div class="content-container">
            <div class="myChart">
                <canvas id="barChart" height="300" width="500"></canvas>
            </div>
    
            <div class="box-wrapper small">
                <div class="box" style="background-color:#3e479b">
                    <div class="small-box">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_customers?></h1>
                            <span>CUSTOMERS</span>
                        </div>
                        <div class="box-icon"></div>
                    </div>
                    <a href="#">View</a>
                </div>
                <div class="box" style="background-color:#9194ce">
                    <div class="small-box">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_suppliers?></h1>
                            <span>SUPPLIERS</span>
                        </div>
                        <div class="box-icon"></div>
                    </div>
                    <a href="#">View</a>
                </div>
                <div class="box" style="background-color: #feb161">
                    <div class="small-box">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_purchase_count?></h1>
                            <span>PURCHASE INVOICE</span>
                        </div>
                        <div class="box-icon"></div>
                    </div>
                    <a href="#">View</a>
                </div>
                <div class="box" style="background-color: #f492a0;">
                    <div class="small-box">
                        <div class="box-content">
                            <h1 class="counter"><?=$total_sales_count?></h1>
                            <span>SALES INVOICE</span>
                        </div>
                        <div class="box-icon"></div>
                    </div>
                    <a href="#">View</a>
                </div>
            </div>

            <div class="myChart pie">
                <figure class="highcharts-figure">
                    <div id="pieChart"></div>
                </figure>    
            </div>
    

            <div class="table-container">
                <div class="table-header">
                    <h3>Recently Added Items</h3>
                </div>
                <div class="table-content">
                    <table id="mytable" class="table is-striped table-hover">
                        <thead>
                            <tr>
                                <th>SL.No</th>
                                <th>Item ID</th>
                                <th>Item Name</th>
                                <th>Item Sales Price</th>
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
                            <tr><td colspan="3"><a href="#">View All</a></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="table-container exp-table">
                <div class="table-header">
                    <h3>Expired Items</h3>
                </div>
                <div class="table-content">
                    <table style="width: 100%" class="display table is-striped table-hover">
                        <thead>
                            <tr>
                                <td>SL.No</td>
                                <td>Item ID</td>
                                <td>Item Name</td>
                                <td>Category Name</td>
                                <td>Expire Date</td>
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
                    <h3>Stock Alert</h3>
                </div>
                <div class="table-content">
                    <table style="width: 100%" class="display table is-striped table-hover">
                        <thead>
                            <tr>
                                <td>SL.No</td>
                                <td>Item ID</td>
                                <td>Item Name</td>
                                <td>Category Name</td>
                                <td>Stock</td>
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
        window.onload = function () {
            let months = ["January", "Feburary","March","Apirl","May","June","July","August","September","October","November","December"]
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
                    display: true,
                    text: 'Chart.js Bar Chart'
                }
                }
            },
            };
            var myChart = new Chart(ctx, config);

        /****************PIE CHART******************/


        const pieData = {
            labels: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
            datasets: [
                {
                label: 'Dataset 1',
                data: [ 30, 20, 10, 30, 10],
                fill: true,
                backgroundColor: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
                }
            ]
        };
    Highcharts.chart('pieChart', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Top 10 Selling Items'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Items',
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

    }
        </script> 
          <!-- Counter -->
          <?php include_once APP.'/views/common/counter_js.php'?>

      