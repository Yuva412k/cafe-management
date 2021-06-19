<?php 

    extract($shop_details);
    extract($sales_info[0]);

    $sales_invoice_footer = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice 1</title>
    <style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    font-family: 'Open Sans', 'Martel Sans', sans-serif;
}
th, td {
    padding: 5px;
    text-align: left;   
    vertical-align:top 
}
body{
  word-wrap: break-word;
}
</style>
</head>
<body onload="window.print();">
    <table style="margin:auto">
        <thead>
            <tr>
            <th colspan="5" rowspan="2">
                <?=$shop_name?><br>
                Address: <?=$shop_address?><br>
                <?=$shop_city?><br>
                <?=$shop_state?><br>
                <?=$shop_pincode?><br>
                <?=$shop_mobile?><br>
                <?=$shop_phone?><br>
                <?=$shop_gstin?><br>
            </th>

            <th colspan="5" rowspan="1">
              <b>Sales Invoice </b>(<?=$sales_status?>)
            </th>
            </tr>
            <tr>
                <th colspan="3" rowspan="1">
                    Invoice No : <?=$sales_id?><br>
                    Reference No : <?=$reference_no?>
                </th>
                <th colspan="2" rowspan="1">
                    Date: <?=$sales_date."<br> ".$sales_time;?>
                </th>
            </tr>

            <tr>
                <td colspan="5" style="padding-left:15px">
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
                <td colspan="5" style="padding-left:15px">
                    <b>Shipping Address :</b> <br>
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
                        ?>
                        <br>
                        <?php
                        if(!empty($customer_gstin)){
                            echo $customer_gstin;
                        }?>
                        <br>
                </td>
            </tr>
            <tr>
                <th rowspan="2">SL.No.</th>
                <th rowspan="2" colspan="4">Item Name</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Rate</th>
                <th rowspan="2">Discount</th>
                <th rowspan="2" colspan="2">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
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
                    $discount = (empty($res['discount_input']) || $res['discount_input']===0)? '' : $res['discount_input'].' '.$discount_type;
                    echo "<tr style='border:none'>";
                    echo "<td>".++$i."</td>";
                    echo "<td colspan='4'>".$res['item_name']."</td>";
                    echo "<td style='text-align:right'>".$res['sales_qty']."</td>";
                    echo "<td style='text-align:right'>".$res['price_per_unit']."</td>";
                    echo "<td style='text-align:right'>".$discount."</td>";
                    echo "<td style='text-align:right'>".$res['total_cost']."</td>";
                    echo "</tr>";
                }
                $tot_sales_price += $res['price_per_unit'];
                $tot_total_cost +=$res['total_cost'];
            ?>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td colspan="4" style="text-align:right;font-weight:bold;">Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?=$tot_total_cost?></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"><b>Sub Total</b></td>
                <td style="text-align: right;"><?=$sub_total?></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"><b>Other Charges</b></td>
                <td style="text-align: right;"><?=$other_charges_amt?></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;">
                <?php   
                $discount_type = '';
                 if(!empty($discount_on_all_type)){
                        if($discount_on_all_type == 'percentage'){
                            $discount_type = '%';
                        }else{
                            $discount_type = 'Rs';
                        }
                    }?>
                <b>Discount (<?=$discount_on_all_input." ".$discount_type?>)</b></td>
                <td style="text-align: right;"><?=$discount_on_all_amt?></td>
            </tr>
            <?php
                if(!empty($round_off)){
                    echo '<tr>
                    <td colspan="8" style="text-align: right;"><b>Round Off</b></td>
                    <td style="text-align: right;">'.$round_off.'</td>
                    </tr>';
                }
            ?>
            <tr>
                <td colspan="8" style="text-align: right;"><b>Grand Total</b></td>
                <td style="text-align: right;"><?=round($grand_total)?></td>
            </tr>
            <tr>
                <td colspan="10">
                <?php
      function no_to_words($no)
      {   
       $words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fouteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred &','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
        if($no == 0)
          return ' ';
        else {
        $novalue='';
        $highno=$no;
        $remainno=0;
        $value=100;
        $value1=1000;       
            while($no>=100)    {
              if(($value <= $no) &&($no  < $value1))    {
              $novalue=$words["$value"];
              $highno = (int)($no/$value);
              $remainno = $no % $value;
              break;
              }
              $value= $value1;
              $value1 = $value * 100;
            }       
            if(array_key_exists("$highno",$words))
              return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
            else {
             $unit=$highno%10;
             $ten =(int)($highno/10)*10;            
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
             }
        }
      }	
      echo "<span class='amt-in-word'>Amount in words: <i style='font-weight:bold;'>".no_to_words(round($grand_total))." Only</i></span>";

      ?>
                </td>
            </tr>

            <tr>
                <td colspan="5" style="height:100px;">
                <b>Customer Signature</b>
                    <br>
                </td>
                <td colspan="5" style="height:100px;">
                <b>Authorised Signature</b>
                    <br>
                </td>
            </tr>
            <?php if(!empty($sales_invoice_footer)) {?>
            <tr style="border-top: 1px solid;">
            <td colspan="10" style="text-align: center;">
                <b><?= $sales_invoice_footer; ?></b>
            </td>
            </tr>
            <?php } ?>
        </tfoot>
    </table>
</body>
</html>