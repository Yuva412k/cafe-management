<?php

    if(!isset($request_name)){
        $request_name = '';
    }
    if($request_name=='update'){  
        extract($returnData);
    }else if($request_name=='add'){
        $return_date=date('Y-m-d');
        extract($returnData);
    }
    else if(!isset($id)){
        $customer_id=$reference_no=$other_charges_amt=$other_charges_input=$other_charges_type=$discount_type=$discount_on_all_input=$discount_on_all_amt=$discount_on_all_type=$tax_id=$return_status='';
        $return_date=date('Y-m-d');
    }
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Sales</h2>
        </div>
        <div class="wrapper-container">
            <form id="sales-form" enctype="multipart/form-data">
                <input type="hidden" name="hidden_rowcount" id="hidden_rowcount" value="1">
                <input type="hidden" id="baseUrl" value="<?php echo PUBLIC_ROOT;?>">
                <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">

                <div class="header">
                    <div class="item-pair">
                        <label for="customer_id">Customer Name <sup style="color: red">*</sup></label>
                        <select name="customer_id" id="customer_id" style="width: 60%;">
                        <option value="CU0001">Walk-in Customer</option>
                                <?php
                                 if(isset($id)){
                                     echo '<option selected value="'.$customer_id.'">'.$customer_name.'</option>';
                                 }
                                 ?>
                        </select>
                    </div>
                    <div class="item-pair">
                        <label for="return_id">Return Id<sup style="color: red">*</sup></label>
                        <input type="text" name="return_id" id="return_id" value="<?php print $return_id ?>" >
                    </div>
                    <?php
                    if(isset($sales_id)):
                    ?>
                    <div class="item-pair">
                        <label for="sales_id">Sales Id<sup style="color: red">*</sup></label>
                        <input type="text" name="sales_id" id="sales_id" value="<?php print $sales_id ?>" >
                    </div>
                    <?php endif;?>
                    <div class="item-pair">
                        <label for="return_date">Sales Date <sup style="color: red">*</sup></label>
                        <input type="date" name="return_date" id="return_date" value="<?php print $return_date; ?>">
                    </div>

                    <div class="item-pair">
                        <label for="return_status">Sales Status <sup style="color: red">*</sup></label>
                        <select name="return_status" id="return_status">
                           <?php
                            $selectreturn = '';
                            $selectcancel = '';
                                if(isset($return_status)){
                                    if($return_status == 'return'){$selectreturn = 'selected';}
                                    if($return_status == 'cancel'){$selectcancel = 'selected';}
                                }
                           ?>
                            <option <?= $selectreturn?>value="return">Return</option>
                            <option <?= $selectcancel?> value="cancel">Cancel</option>
                        </select>
                    </div>
                    <div class="item-pair">
                        <label for="reference_no">Reference No</label>
                        <input type="text" name="reference_no" id="reference_no" value="<?php print $reference_no?>">
                    </div>                
                    <div class="item-pair"></div>
                </div>
                <input type="text" placeholder="Item name or Itemcode" id="item_search" value="">
                <hr style="color: #f4f4f4">
                <br>

                <div class="salesreturn-table">
                    <table style="width: 100%" id="salesreturn_table">
                        <thead style="text-align: right !important;">
                            <tr>
                                <th style="text-align: left !important;">ITEM NAME</th>
                                <th>QUANTITY</th>
                                <th>RATE</th>
                                <th>DISCOUNT</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

                <hr style="color: #f4f4f4">

                <div class="table-row">
                    <div class="table-col">
                    <span>Tax</span>
                    <div>
                    <select id="tax_id" name="tax_id">  
                    <?php
                        foreach($taxData as $row){
                            $select = '';
                            if(isset($tax_id) && $tax_id != ''){
                                if($row['tax_id'] == $tax_id){
                                    $select = 'selected';
                                }
                            }
                        echo "<option $select data-tax='".$row['tax']."' value='".$row['tax_id']."'>".$row['tax_name']."</option>";
                        }
                    ?>
                    </select>
                    </div>
                    <div class="payment_box">
                            <div class="payments_div">
                            <div class="box-body">
                                <div class="row"></div>
                            
                                <div class="amount_div">
                                        <label for="amount">Amount</label>
                                        <div>
                                        <input type="text" class="form-control text-right paid_amt only_currency" id="amount" name="amount" placeholder="" >
                                        <span id="amount_msg"></span>
                                        </div>
                                </div>
                                <div class="">
                                    <label for="payment_type">Payment Type</label>
                                    <div>
                                    <select id='payment_type' name="payment_type">
                                        <?php
                                            if($paymentData['length'] >0){
                                            echo "<option value=''>-Select-</option>";
                                                // foreach($paymentData as $res1){
                                                echo "<option value='".$paymentData['payment_type']."'>".$paymentData['payment_type'] ."</option>";
                                            // }
                                            }
                                            else{
                                            echo "<option>None</option>";
                                            }
                                        ?>
                                    </select>
                                    <span id="payment_type_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="table-col">
                        <div class="row">
                            <div class="col">
                                <span>Sub Total</span>
                            </div>
                            <div class="col rt">
                                <span name="sub_total" id="sub_total" class="sub-total"><?php echo $sub_total ?? '0.00'?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Discount on All</span>
                                <div>
                                    <input style="margin: 0;" class="total-disc" type="text" name="discount_on_all_input" onkeyup="final_total()" id="discount_on_all_input" value="<?php echo $discount_on_all_input ?? 0; ?>">
                                    <select id="discount_on_all_type" name="discount_on_all_type">
                                    <?php 
                                    $selectPer = '';
                                    $selectRup = '';
                                    if($discount_on_all_type === 'percentage'){$selectPer = 'selected';}
                                    if($discount_on_all_type === 'rupee'){$selectRup = 'selected';}
                                    ?>
                                        <option <?=$selectPer?> value="percentage">%</option>
                                        <option <?=$selectRup?> value="rupee">Rs.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col rt">
                                <span id="discount_on_all_amt" name="discount_on_all_amt" class="total-discount"><?php echo $discount_on_all_amt ?? 0.00?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Other Charges</span>
                                <div>
                                    <input style="margin: 0;" class="total-disc" type="text" name="other_charges_input" id="other_charges_input" onkeyup="final_total()" value="<?php echo $other_charges_input?? 0?>">
                                    <select name="other_charges_type" id="other_charges_type">
                                   <?php
                                    $selectPer = '';
                                    $selectRup = '';
                                    if($other_charges_type === 'percentage'){$selectPer = 'selected';}
                                    if($other_charges_type === 'rupee'){$selectRup = 'selected';}
                                    ?>
                                        <option <?=$selectPer?> value="percentage">%</option>
                                        <option <?=$selectRup?> value="rupee">Rs.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col rt">
                                <span id="other_charges_amt" name="other_charges_amt" class="total-charges"><?php echo $other_charges_amt ?? '0.00'; ?></span>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col">
                                <span>CGST <i class="tax_per"></i></span>
                            </div>
                            <div class="col rt">
                                <span id="cgst_amt" name="cgst_amt" class=""><?php echo $tax_amt_cgst ?? 0.00;?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>SGST <i class="tax_per"></i></span>
                            </div>
                            <div class="col rt">
                                <span id="sgst_amt" name="sgst_amt" class=""><?php echo $tax_amt_sgst ?? 0.00;?></span>
                            </div>
                        </div>
                        <!-- Hidden total tax  -->
                        <div class="row">
                            <div class="col">
                                <span>Round Off</span>
                                <input type="checkbox" id="round_off" <?php if(isset($round_off) && $round_off != 0){echo "checked";}?>>
                            </div>
                            <div class="col rt">
                                <span id="round_off_amt" name="round_off_amt" class=""><?php if(isset($round_off) && $round_off != 0){echo $round_off;}?></span>
                            </div>
                        </div>

                        <div class="row" style="font-size: 20px;font-weight: bold;">
                            <div class="col">
                                <span>Grand Total</span>
                            </div>
                            <div class="col rt">
                                <span name="grand_total" id="grand_total"><?php echo $grand_total ?? 0.00?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                        if($request_name=='update'){
                            $btn_name = 'Update';
                            $btn_id = 'update';
                        ?>
                        <input type='hidden' name='id' value='<?php echo $id;?>'>
                        <?php 
                        }if($request_name == 'add'){
                            $btn_id='save';
                            $btn_name='Save';
                        }
                        else{
                            $btn_id='create';
                            $btn_name='Create';
                        }

                        ?>
                </div>

                <hr style="color: #f4f4f4">
                <br>
                <div class="btn-container">
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                    <div class="btn"><input type="reset" name='reset' value="Reset"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>
<script src="<?php echo PUBLIC_ROOT.'Plugins/autocomplete/autocomplete.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery-ui/jquery-ui.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/salesreturn.js'?>"></script>
    <script>
          $(document).ready(function() {
              base_url = $('#baseUrl').val();
            $("#customer_id").select2({
                ajax:{
                    url: base_url+'/sales/customerAjax',
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
        });
    </script>
    <script>
         <?php if($request_name == "update"): ?> 
             $(document).ready(function(){
                var base_url='<?=PUBLIC_ROOT?>';
                var return_id='<?= $return_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"salesReturn/returnSalesReturnList/"+return_id,{},function(result){
                  //alert(result);
                  $('#salesreturn_table tbody').append(result);
                  $("#hidden_rowcount").val(parseFloat(<?=$items_count;?>)+1);
                  $(".overlay").remove();
              }); 
             });
         <?php endif;?>
    </script>
    <script>
        <?php if($request_name == "add"): ?> 
            $(document).ready(function(){
            var base_url='<?=PUBLIC_ROOT?>';
            var sales_id='<?= $sales_id;?>';
            $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $.post(base_url+"salesReturn/returnSalesList/"+sales_id,{},function(result){
                //alert(result);
                $('#salesreturn_table tbody').append(result);
                $("#hidden_rowcount").val(parseFloat(<?=$items_count;?>)+1);
                $(".overlay").remove();
            }); 
            });
        <?php endif;?>
    </script>
    <script>    

    /*********Calculate Amount ***********/
    function calculate_amount(i){

        let qty = $('#td_data_qty_'+i).val();
        let sales_price = parseFloat($('#td_data_tot_unit_cost_'+i).val());
        //set sales price
        $('#td_data_per_unit_price_'+i).val(sales_price);

        /*Discount*/
        let discount_amt = 0;
        let discount_input = $('#td_data_discount_input_'+i).val().trim();
        let discount_type = $('#td_data_discount_type_'+i).val();

        let amt = parseFloat(qty) * sales_price;
      
        if(discount_type === 'percentage'){
            discount_amt = (isNaN(parseFloat(discount_amt))) ? 0 : (parseFloat(discount_amt)*amt)/100;
        }else{
            discount_amt = (isNaN(parseFloat(discount_amt))) ? 0 : parseFloat(discount_amt);
        }

        let total_amt = amt-discount_amt;

        //set hiddent discount amt
        $('#td_data_discount_amt_'+i).val(discount_amt);
        //set rate price or unit price
        $('#td_data_total_cost_'+i).val(total_amt.toFixed(2));

        final_total();
    }

    /********FINAL TOTAL CALCULATION ***********/
    function final_total(){

        let rowcount = $('#hidden_rowcount').val();
        let subtotal = parseFloat(0);
        let other_charges_per_amt = parseFloat(0);
        let other_charges_total_amt = 0;
        let taxable = 0;
        let tax = $('#tax_id').find(':selected').attr('data-tax');


        let total_quantity = 0;
        let tax_amt = 0;

        // calculate subtotal
        for(i=1; i<=rowcount; i++){

            if($('#td_data_qty_'+i).val() != null && $('#td_data_qty_'+i).val() != ''){
                subtotal += parseFloat($('#td_data_total_cost_'+i).val());
            }

        }

        if((subtotal != null || subtotal != '') && (subtotal!=0)){

            $("#sub_total").html(subtotal.toFixed(2));

            let other_charges_input = $('#other_charges_input').val();
            if(other_charges_input != null && other_charges_input != ''){
                other_charges_type = $('option:selected', '#other_charges_type').val();

                if(other_charges_type == "percentage"){
                    other_charges_per_amt = (other_charges_input * subtotal)/100;
                }else{
                    other_charges_per_amt = other_charges_input;
                }

                taxable = parseFloat(other_charges_per_amt);
                other_charges_total_amt = parseFloat(other_charges_per_amt);
            }
            $('#other_charges_amt').html(parseFloat(other_charges_total_amt).toFixed(2));

            taxable=taxable+subtotal;
             
             //discount_on_all_amt
            var discount_on_all_input=parseFloat($("#discount_on_all_input").val());
            discount_on_all_input = isNaN(discount_on_all_input) ? 0 : discount_on_all_input;
            var discount=0;
            if(discount_on_all_input>0){
                var discount_type=$("#discount_on_all_type").val();
                if(discount_type=='rupee'){
                    taxable-=discount_on_all_input;
                    discount=discount_on_all_input;
                }
                else if(discount_type=='percentage'){
                    discount=(taxable*discount_on_all_input)/100;
                    taxable-=discount;
                }
            }
            else{
            //discount += $("#")
            }
            discount=parseFloat(discount).toFixed(2);
            $("#discount_on_all_amt").html(discount);  
            $("#hidden_discount_on_all_amt").val(discount);  

            //GST calculation
            per = tax/2.0;
            cgst = (taxable * per)/100;
            sgst = (taxable * per)/100;
            taxforsubtotal= cgst + sgst;

        
            subtotalwithtax = taxforsubtotal + taxable;
            console.log(subtotalwithtax);

            round_off_check = $('#round_off').is(':checked')
            if(round_off_check == true){         
                round_off =Math.round(subtotalwithtax)- parseFloat(subtotalwithtax);
                subtotal_round=Math.round(subtotalwithtax);
            }else{
                round_off = 0;
                subtotal_round=subtotalwithtax;
            }

            $(".tax_per").html(per+'%');
            $("#sgst_amt").html(parseFloat(sgst).toFixed(2));
            $("#cgst_amt").html(parseFloat(cgst).toFixed(2));
            $('#round_off_amt').html(parseFloat(round_off).toFixed(2));
            $("#grand_total").html(parseFloat(subtotal_round).toFixed(2));
            $("#hidden_total_amt").val(parseFloat(subtotal_round).toFixed(2)); 
        }
        else{
            $("#sub_total").html('0.00'); 
            $("#grand_total").html('0.00'); 
            $("#amount").val('0.00');
            $("#discount_on_all_amt").html('0.00'); 
            $('#round_off_amt').html('0.00');
            $("#subtotal_amt").html('0.00'); 
            $("#other_charges_amt").html('0.00');  
        }
    }
    </script>