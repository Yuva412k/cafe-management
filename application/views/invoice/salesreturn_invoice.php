<?php 

    extract($shop_details);
    extract($sales_info[0]);

?>
<html>
    <head>
        <style>
            body{
                font-family: 'Open Sans', 'Martel Sans', sans-serif;
                word-wrap: break-word;
            }
            table, tr, th, td{
                border-collapse: collapse;
            }
            #item_table td{
                padding:10px;
            }
            
        </style>
    </head>
    <body >
        <div class="example">
        <table style="width: 210mm;margin-left: auto;margin-right: auto;">
            <thead>
                <tr>
                     <th style="font-size: 30px;text-align:left" colspan="4">Sales Return Invoice <th>
                </tr>
               <tr>
                   <td colspan="4">
                    Invoice No : <?=$return_id?><br>
                 <?php if(!empty($sales_id)):?>   Sales No : <?=$sales_id?><br><?php endif;?>
                    Reference No : <?=$reference_no?><br>
                    Date: <?=$return_date?>
                </td>
                <td rowspan="2" colspan="4"  style="text-align: right;"><h3><?=$shop_name?></h3>
                   
                    <?=$shop_address?><br>
                    <?=$shop_city?><br>
                    <?=$shop_state?><br>
                    <?=$shop_pincode?><br>
                    <?=$shop_mobile?><br>
                    <?=$shop_phone?><br>
                    <?=$shop_gstin?><br>
                </td>
               </tr>
               <tr>
               <td style="text-align: left;" colspan="4">
                    <b>Customer Address :</b> <br>
                    Name : <?=$customer_name?>
                    <?php echo (!empty(trim($mobile) ? 'Mobile : '.$mobile.'<br>' : ''))?>
                    <?php
                        if(!empty($address)){
                            echo $address;
                        }
                        if(!empty($city)){
                            echo $city;
                        }
                        if(!empty($state)){
                            echo $state;
                        }
                        if(!empty($pincode)){
                            echo $pincode;
                        }
                        if(!empty($customer_gstin)){
                            echo $customer_gstin;
                        }
                        ?>
                        <br>
                </td>
                </tr>
               <tr style="height:80px;"></tr>
            </thead>
            <tbody>
            <tr style="border-bottom: 2px solid #ccc;" >
                <th style="text-align: left;">#</th>
                <th style="text-align: left;" colspan="2">Product Name</th>
                <th style="text-align: right;">Qty</th>
                <th style="text-align: right;">Rate</th>
                <th style="text-align: right;">Discount</th>
                <th style="text-align: right;">Amount</th>
            </tr>
            <tr style="border-bottom: 2px solid #ccc; border-spacing: 5px;">
                <?php
                     $i=0;
                     $tot_sales_price=0;
                     $tot_unit_total_cost=0;
                     $tot_total_cost=0;
    
                    foreach($sales_items as $res){
                        $discount_type = '';
                        if(!empty($res['discount_type'])){
                            if($res['discount_type'] == 'percentage'){
                                $discount_type = '%';
                            }else{
                                $discount_type = 'Rs';
                            }
                        }
                        $discount = (empty($res['discount_input']) || $res['discount_input']==0)? '' : number_format($res['discount_amt'],2);
                        echo "<tr id='item_table' style='border-bottom:1px solid #ccc'>";
                        echo "<td>".++$i."</td>";
                        echo "<td colspan='2'>".$res['item_name']."</td>";
                        echo "<td style='text-align:right'>".$res['return_qty']."</td>";
                        echo "<td style='text-align:right'>".number_format($res['price_per_unit'])."</td>";
                        echo "<td style='text-align:right'>".$discount."</td>";
                        echo "<td style='text-align: right'>".number_format($res['total_cost'],2)."</td>";
                        echo "</tr>";
                    }
                    $tot_sales_price += $res['price_per_unit'];
                    $tot_total_cost +=$res['total_cost'];
                ?>
                </tr>   
            </tbody>
            <tfoot>
              
                <tr>
                    <td colspan="6" style="text-align: right;"><br>Sub Total</td>
                    <td colspan="2" style="text-align: right;"><br><?=number_format($sub_total,2)?></td>
                </tr>
                
               <?php 
                    if(!empty($discount_on_all_amt) && $discount_on_all_amt != 0):
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;">Other Charges</td>
                    <td colspan="2" style="text-align: right;"><?=number_format($other_charges_amt,2)?></td>
                </tr>
                <?php endif; ?>
               <?php 
                    if(!empty($discount_on_all_amt) && $discount_on_all_amt != 0):
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;">
                    <?php   
                    $discount_type = '';
                     if(!empty($discount_on_all_type)){
                            if($discount_on_all_type == 'percentage'){
                                $discount_type = '%';
                            }else{
                                $discount_type = 'Rs';
                            }
                        }?>
                    Discount (<?=$discount_on_all_input." ".$discount_type?>)</td>
                    <td colspan="2" style="text-align: right;"><?=number_format($discount_on_all_amt,2)?></td>
                </tr>
                <?php endif; ?>
            <?php $tax_per = $tax/2 ?>
                <tr>
                    <td colspan="6" style="text-align: right;">CGST <?=$tax_per?>%</td>
                    <td colspan="2" style="text-align: right;"><?=number_format($tax_amt_cgst,2)?></td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right;">SGST <?=$tax_per?>%</td>
                    <td colspan="2" style="text-align: right;"><?=number_format($tax_amt_sgst,2)?></td>
                </tr>
                <?php
                    if(!empty($round_off)){
                        echo '<tr>
                        <td colspan="6" style="text-align: right;">Round Off</td>
                        <td colspan="2" style="text-align: right;">'.number_format($round_off,2).'</td>
                        </tr>';
                    }
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;"><b>Invoice Total</b></td>
                    <td colspan="2" style="text-align: right;"><b><?=number_format($grand_total,2)?><b></td>
                </tr>
                <tr style="height: 50px;"></tr>
                <?php if(!empty($invoice_footer)) {?>
                <tr style="border-top: 1px solid;">
                <td colspan="8" style="text-align: left;border:1px solid #000;padding:5px;font-size:13px">
                    <b style="font-size: 13;">Terms & Conditions:</b><br>
                        <?=$invoice_footer; ?><br>
                </td>
                </tr>
                <?php } ?>
            </tfoot>
        </table>
    </div>
    </body>
</html>
