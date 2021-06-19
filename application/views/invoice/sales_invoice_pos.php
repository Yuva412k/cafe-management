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
	body{
		font-family: arial;
		font-size: 12px;
		/*font-weight: bold;*/
		padding-top:15px;
	}
    table{
        border-collapse: collapse;
    }

</style>
</head>
<body onload="window.print();">
    <table style="margin:auto">
            <tr>
            <td style="text-align: center;">
                <span>
                <strong><?=$shop_name?></strong><br>
                Address: <?=$shop_address?><br>
                <?=$shop_city?><br>
                <?=$shop_state?> - 
                <?=$shop_pincode?><br>
                <?=$shop_mobile?><br>
                <?=$shop_phone?><br>
                GSTIN : <?=$shop_gstin?><br>
                </span>
            </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                <strong>----------------- Invoice -----------------</strong>
                </td>
            </tr>

            <tr>
                <td>
                    <table>
                        <tr>
                            <td width="40%">
                            Invoice No
                            </td>
                            <td>: <?=$sales_id?></td>
                        </tr>
                        <tr>
                            <td>
                            Name 
                            </td>
                            <td>: <?=$customer_name?></td>
                        </tr>
                        <tr>
                            <td>
                            Date 
                            </td>
                            <td>: <?=$sales_date?></td>
                        </tr>
                        <tr>
                            <td>Time</td>
                            <td>: <?=$sales_time;?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <thead style="border-top-style: dashed; border-bottom-style: dashed;border-width: 0.1px;">
                        <tr>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;"># </th>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;">Description</th>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;">Qty</th>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;">Rate</th>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;">Disc</th>
                            <th style="font-size:11px; text-align:left;padding-left:2px;padding-right:2px;">Amount</th>
                        </tr>
                        </thead>
                        <tbody style="border-bottom-style:dashed;border-width: 0.1px">
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
                                echo "<td >".$res['item_name']."</td>";
                                echo "<td style='text-align:right'>".$res['sales_qty']."</td>";
                                echo "<td style='text-align:right'>".$res['price_per_unit']."</td>";
                                echo "<td style='text-align:right'>".$discount."</td>";
                                echo "<td style='text-align:right'>".$res['total_cost']."</td>";
                                echo "</tr>";
                            }
                            $tot_sales_price += $res['price_per_unit'];
                            $tot_total_cost +=$res['total_cost'];
                        ?>
        </tbody>
        <tfoot>
        <tr></tr>
            <tr>
                <td style="text-align: right;" colspan="4">Sub Total :</td>
                <td style="text-align: right;" colspan="2"><?=$sub_total?></td>
            </tr>
           <?php if(!empty($other_charges_amt) && $other_charges_amt != 0):?>
            <tr>
                <td style="text-align: right;" colspan="4">Other Charges :</td>
                <td style="text-align: right;" colspan="2"><?=$other_charges_amt?></td>
            </tr>
            <?php endif;?>
            <?php if(!empty($discount_on_all_amt) && $discount_on_all_amt != 0):?>
            <tr>
                <td colspan="4" style="text-align: right;">
                <?php   
                $discount_type = '';
                 if(!empty($discount_on_all_type)){
                        if($discount_on_all_type == 'percentage'){
                            $discount_type = '%';
                        }else{
                            $discount_type = 'Rs';
                        }
                    }?>
                <b>Discount (<?=$discount_on_all_input." ".$discount_type?>) :</b></td>
                <td colspan="2" style="text-align: right;"><?=$discount_on_all_amt?></td>
            </tr>
            <?php endif;?>
            <tr style="border-top: .1px dashed #000; border-bottom: .1px dashed #000">
                <?php
                  $tax_per = $tax/2.0;
                ?>
                <td></td>
                <td>CGST <?=$tax_per?>% :</td><td><?=number_format($tax_amt_cgst,2,'.')?></td>
                <td colspan="2" style="padding-left: 5px;">SGST <?=$tax_per?>% :</td><td style="padding-left:10px;"><?=number_format($tax_amt_sgst,2,'.')?></td>
            </tr>
            <tr style="border-bottom-style:dashed;border-width: 0.1px">
                <td colspan="4" style="text-align: right;"><b>Net Total :</b></td>
                <td colspan="2" style="text-align: right;"><?=number_format(round($grand_total),2,'.')?></td>
            </tr>
            <tr></tr>
            <tr>
                <td colspan="6" style="text-align:center">---------- Thank You Visit Again ----------</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>